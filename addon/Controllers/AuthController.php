<?php

namespace Addon\Controllers;

use Addon\Models\UserModel;
use Addon\Models\EmailVerificationModel;
use Addon\Models\PasswordResetTokenModel;
use Addon\Models\LoginNotificationModel;
use Addon\Models\RegistrationModel;
use Addon\Models\RegistrationAddressModel;
use Addon\Models\RegistrationParentModel;
use Addon\Models\RegistrationEducationModel;
use Addon\Models\RegistrationSpecialNeedModel;
use Addon\Services\EmailService;
use Addon\Helpers\OtpGenerator;
use App\Services\SessionService;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\View\View;
use App\Core\Http\RedirectResponse;
use App\Exceptions\HttpException;
use Exception;

/**
 * Authentication Controller - Hybrid Auth System
 *
 * Handles:
 * - Login (Email/Password + Google OAuth)
 * - Register (Manual + Google OAuth)
 * - OTP Verification
 * - Logout
 * - Password reset via email
 * - Login notifications
 */
class AuthController
{
    public function __construct(
        private UserModel $users,
        private SessionService $session,
        private EmailVerificationModel $emailVerifications,
        private PasswordResetTokenModel $passwordResetTokens,
        private LoginNotificationModel $loginNotifications,
        private EmailService $emailService,
        private RegistrationModel $registrations,
        private RegistrationAddressModel $addresses,
        private RegistrationParentModel $parents,
        private RegistrationEducationModel $educations,
        private RegistrationSpecialNeedModel $specialNeeds
    ) {}

    /**
     * Minimum password length
     */
    private const MIN_PASSWORD_LENGTH = 8;

    /**
     * Hash password menggunakan bcrypt
     *
     * @param string $password Plain text password
     * @return string Hashed password
     */
    private function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
    }

    /**
     * Verify password against hash
     *
     * @param string $password Plain text password
     * @param string $hash Hashed password
     * @return bool True if password matches
     */
    private function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Validate password strength
     *
     * @param string $password Password to validate
     * @return array{valid: bool, errors: array<string>} Validation result
     */
    private function validatePassword(string $password): array
    {
        $errors = [];

        if (strlen($password) < self::MIN_PASSWORD_LENGTH) {
            $errors[] = "Password minimal " . self::MIN_PASSWORD_LENGTH . " karakter";
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Check if user is logged in
     */
    private function check(): bool
    {
        $userId = $this->session->get('auth.user_id');
        return $userId !== null;
    }

    /**
     * Get authenticated user (returns array)
     */
    private function user(): ?array
    {
        $userId = $this->session->get('auth.user_id');

        if ($userId === null) {
            return null;
        }

        return $this->users->find($userId);
    }

    /**
     * Login user dan simpan session
     */
    private function loginSession(array $user): void
    {
        $this->session->set('auth.user_id', $user['id']);
        $this->session->set('auth.user_email', $user['email']);
        $this->session->set('auth.user_name', $user['name']);
        $this->session->set('auth.user_avatar', $user['avatar'] ?? null);

        if (isset($user['role'])) {
            $this->session->set('auth.user_role', $user['role']);
        }

        if (isset($user['permissions'])) {
            $permissions = json_decode($user['permissions'], true);
            $this->session->set('auth.user_permissions', is_array($permissions) ? $permissions : []);
        } else {
            $this->session->set('auth.user_permissions', ($user['role'] ?? 'user') === 'admin' ? ['*'] : ['view_dashboard']);
        }

        $this->session->set('is_logged_in', true);
    }

    /**
     * Logout user
     */
    private function logoutSession(): void
    {
        $email = $this->session->get('auth.user_email') ?? 'User';
        $this->session->destroy();
    }

    /**
     * Show login form
     */
    public function showLogin(Request $request, Response $response): View | RedirectResponse
    {
        // If already logged in, redirect to dashboard
        if ($this->check()) {
            return $response->redirect('/dashboard');
        }

        return $response->renderPage([], ['path' => '/login', 'meta' => ['title' => 'Login | ' . env('APP_NAME')]]);
    }

    /**
     * Process login (Email/Password)
     */
    public function login(Request $request, Response $response): RedirectResponse
    {
        try {
            $email = $request->input('email');
            $password = $request->input('password');

            if (!$email || !$password) {
                return $response->redirect('/login?error=Email+dan+password+harus+diisi');
            }

            // Find user by email
            $user = $this->users->findByEmail($email);

            if (!$user) {
                return $response->redirect('/login?error=Email+tidak+ditemukan');
            }

            // Verify password
            if (!$this->verifyPassword($password, $user['password'])) {
                return $response->redirect('/login?error=Password+salah');
            }

            // Check if user is active
            if (!$user['is_active']) {
                // User not active - resend OTP and redirect to verify
                $this->sendOtpToUser($user['id'], $user['email']);
                return $response->redirect('/verify-otp?email=' . urlencode($user['email']) . '&info=Akun+belum+terverifikasi.+Silakan+verifikasi+email+Anda');
            }

            // Update last login
            $this->users->updateLastLogin($user['id']);

            // Login successful - save session
            $this->loginSession($user);

            // Send login notification email
            // $this->sendLoginNotification($user);

            return $response->redirect('/dashboard')->hard();
        } catch (\Throwable $th) {
            return $response->redirect('/login?error=Something+went+wrong');
        }
    }

    /**
     * Show register form
     */
    public function showRegister(Request $request, Response $response): View | RedirectResponse
    {
        // If already logged in, redirect to dashboard
        if ($this->check()) {
            return $response->redirect('/dashboard');
        }

        return $response->renderPage([], ['path' => '/register', 'meta' => ['title' => 'Register | ' . env('APP_NAME')]]);
    }

    /**
     * Process register (Manual with OTP verification)
     */
    public function register(Request $request, Response $response): View | RedirectResponse
    {
        $email = $request->input('email');
        $password = $request->input('password');
        $name = $request->input('name');
        $passwordConfirmation = $request->input('password_confirmation');

        // Validation
        if (!$email || !$password || !$name) {
            return $response->redirect('/register?error=Semua+field+harus+diisi');
        }

        if ($password !== $passwordConfirmation) {
            return $response->redirect('/register?error=Password+konfirmasi+tidak+cocok');
        }

        // Validate password strength
        $passwordValidation = $this->validatePassword($password);
        if (!$passwordValidation['valid']) {
            return $response->redirect('/register?error=' . urlencode(implode(', ', $passwordValidation['errors'])));
        }

        // Role handling
        $role = $request->input('role', 'user');
        if (!in_array($role, ['admin', 'user'])) {
            $role = 'user';
        }

        // Check if email already exists
        $existingUser = $this->users->findByEmail($email);
        if ($existingUser) {
            return $response->redirect('/register?error=Email+sudah+terdaftar');
        }

        // Prepare user data (is_active = false, waiting for OTP verification)
        $userData = [
            'email' => $email,
            'password' => $this->hashPassword($password),
            'name' => $name,
            'avatar' => null,
            'is_active' => 0, // Not active until OTP verified
        ];

        // Add role to user data
        $userData['role'] = $role;

        // Create user with try-catch
        try {
            $userId = $this->users->create($userData);
            $newUser = $this->users->find($userId);

            if (!$newUser) {
                throw new Exception('Gagal membuat user');
            }

            // Send OTP to user's email
            $otpCode = OtpGenerator::generate();
            $this->emailVerifications->createOtp($userId, $email, $otpCode, 15);

            // Send email with OTP
            // $this->emailService->sendOtpVerification($email, $name, $otpCode, 15);

            // Store user ID in session for OTP verification
            $this->session->set('auth.pending_user_id', $userId);
            $this->session->set('auth.pending_user_email', $email);

            // Redirect to OTP sent page
            return $response->redirect('/otp-sent?email=' . urlencode($email));
        } catch (\Exception $e) {
            return $response->redirect('/register?error=' . urlencode($e->getMessage()));
        }
    }

    /**
     * Logout
     */
    public function logout(Request $request, Response $response): View | RedirectResponse
    {
        $this->logoutSession();
        return $response->redirect('/login')->hard();
    }

    /**
     * Show OTP verification page
     */
    public function showVerifyOtp(Request $request, Response $response): View | RedirectResponse
    {
        $email = $request->query['email'] ?? null;
        $info = $request->query['info'] ?? null;

        if (!$email) {
            return $response->redirect('/register');
        }

        return $response->renderPage([
            'email' => $email,
            'info' => $info,
        ], ['path' => '/verify-otp', 'meta' => ['title' => 'Verifikasi Email | ' . env('APP_NAME')]]);
    }

    /**
     * Process OTP verification
     */
    public function verifyOtp(Request $request, Response $response): View | RedirectResponse
    {
        $email = $request->input('email');
        $otpCode = $request->input('otp_code');

        if (!$email || !$otpCode) {
            return $response->redirect('/verify-otp?email=' . urlencode($email ?? '') . '&error=Email+dan+kode+OTP+harus+diisi');
        }

        // Find user by email
        $user = $this->users->findByEmail($email);

        if (!$user) {
            return $response->redirect('/verify-otp?email=' . urlencode($email) . '&error=User+tidak+ditemukan');
        }

        // Verify OTP
        $result = $this->emailVerifications->verifyOtp($user['id'], $otpCode);

        if (!$result['valid']) {
            return $response->redirect('/verify-otp?email=' . urlencode($email) . '&error=' . urlencode($result['message']));
        }

        // Activate user account
        $this->users->updateById($user['id'], [
            'is_active' => 1,
        ]);

        // Invalidate all other OTPs
        $this->emailVerifications->invalidateAll($user['id']);

        // Auto-login after verification
        $this->loginSession($user);

        // Send login notification
        $this->sendLoginNotification($user);

        return $response->redirect('/dashboard?success=Email+berhasil+diverifikasi')->hard();
    }

    /**
     * Resend OTP
     */
    public function resendOtp(Request $request, Response $response): View | RedirectResponse
    {
        $email = $request->query['email'] ?? null;

        if (!$email) {
            return $response->redirect('/register');
        }

        $user = $this->users->findByEmail($email);

        if (!$user) {
            return $response->redirect('/register?error=User+tidak+ditemukan');
        }

        // Check if user already verified
        if ($user['is_active']) {
            return $response->redirect('/login?info=Akun+sudah+aktif.+Silakan+login');
        }

        // Send new OTP
        $this->sendOtpToUser($user['id'], $user['email']);

        return $response->redirect('/otp-sent?email=' . urlencode($email) . '&success=Kode+OTP+telah+dikirim+kembali');
    }

    /**
     * Show OTP sent page
     */
    public function showOtpSent(Request $request, Response $response): View | RedirectResponse
    {
        $email = $request->query['email'] ?? null;
        $success = $request->query['success'] ?? null;

        if (!$email) {
            return $response->redirect('/register');
        }

        return $response->renderPage([
            'email' => $email,
            'success' => $success,
        ], ['path' => '/otp-sent', 'meta' => ['title' => 'Email Terkirim | ' . env('APP_NAME')]]);
    }



    /**
     * Send OTP to user
     */
    private function sendOtpToUser(int $userId, string $email): void
    {
        // Invalidate old OTPs
        $this->emailVerifications->invalidateAll($userId);

        // Generate new OTP
        $otpCode = OtpGenerator::generate();

        // Create OTP record
        $this->emailVerifications->createOtp($userId, $email, $otpCode, 15);

        // Get user name
        $user = $this->users->find($userId);
        $name = $user['name'] ?? $email;

        // Send email
        $this->emailService->sendOtpVerification($email, $name, $otpCode, 15);
    }

    /**
     * Send login notification email
     */
    private function sendLoginNotification(array $user): void
    {
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        $loginAt = date('Y-m-d H:i:s');

        // Log to database
        $this->loginNotifications->logLogin(
            $user['id'],
            $user['email'],
            $ipAddress,
            $userAgent,
            $loginAt
        );

        // Send notification email
        $this->emailService->sendLoginNotification(
            $user['email'],
            $user['name'] ?? $user['email'],
            $ipAddress,
            $userAgent,
            $loginAt
        );
    }

    /**
     * Show forgot password form
     */
    public function showForgotPassword(Request $request, Response $response): View | RedirectResponse
    {
        return $response->renderPage([], ['path' => '/password/forgot', 'meta' => ['title' => 'Lupa Password | ' . env('APP_NAME')]]);
    }

    /**
     * Send password reset link
     */
    public function sendResetLink(Request $request, Response $response): View | RedirectResponse
    {
        $email = $request->input('email');

        if (!$email) {
            return $response->redirect('/password/forgot?Email+harus+diisi');
        }

        $user = $this->users->findByEmail($email);

        if (!$user) {
            // For security, show same success message even if email not found
            return $response->renderPage(
                ['message' => 'Jika email terdaftar, link reset password telah dikirim'],
                ['path' => '/password/forgot', 'meta' => ['title' => 'Lupa Password | ' . env('APP_NAME')]]
            );
        }



        // Generate reset token
        $token = $this->passwordResetTokens->generateToken();
        $this->passwordResetTokens->createToken($user['id'], $token, 60);

        // Build reset URL
        $resetUrl = rtrim(env('APP_URL', 'http://localhost:8000'), '/') . '/password/reset'  . '?email=' . urlencode($email) . '&token=' . $token;

        // Send email
        $this->emailService->sendPasswordReset(
            $user['email'],
            $user['name'] ?? $user['email'],
            $resetUrl,
            60
        );

        return $response->renderPage([
            'message' => 'Jika email terdaftar, link reset password telah dikirim',
        ], ['path' => '/password/forgot', 'meta' => ['title' => 'Lupa Password | ' . env('APP_NAME')]]);
    }

    /**
     * Show reset password form
     */
    public function showResetPassword(Request $request, Response $response): View | RedirectResponse
    {
        $token = $request->query['token'] ?? null;
        $email = $request->query['email'] ?? null;

        if (!$token) {
            return $response->redirect('/password/forgot');
        }

        // Validate token
        $tokenData = $this->passwordResetTokens->findValidToken($token);

        if (!$tokenData || $tokenData['user_id'] !== $this->users->findByEmail($email)['id']) {
            return $response->redirect('/password/reset?error=Link+reset+password+tidak+valid+atau+telah+kedaluwarsa');
        }

        return $response->renderPage(
            ['token' => $token, 'email' => $email,],
            ['meta' => ['title' => 'Reset Password | ' . env('APP_NAME')]]
        );
    }

    /**
     * Process reset password
     */
    public function resetPassword(Request $request, Response $response): View | RedirectResponse
    {
        $email = $request->input('email');
        $password = $request->input('password');
        $passwordConfirmation = $request->input('password_confirmation');
        $token = $request->input('token') ?? null;

        if ($password !== $passwordConfirmation) {
            return $response->redirect('/password/reset?Password+konfirmasi+tidak+cocok');
        }

        // Validate new password
        $passwordValidation = $this->validatePassword($password);
        if (!$passwordValidation['valid']) {
            return $response->redirect('/password/reset?' . urlencode(implode(', ', $passwordValidation['errors'])));
        }

        // Validate token
        $tokenData = $this->passwordResetTokens->findValidToken($token);

        if (!$tokenData) {
            return $response->redirect('/password/reset?error=Link+reset+password+tidak+valid+atau+telah+kedaluwarsa');
        }

        $user = $this->users->findByEmail($email);

        if (!$user || $user['id'] !== $tokenData['user_id']) {
            return $response->redirect('/password/reset?Email+tidak+valid');
        }

        // Update password
        $this->users->updateById($user['id'], ['password' => $this->hashPassword($password)]);

        // Invalidate all reset tokens
        $this->passwordResetTokens->invalidateAll($user['id']);

        return $response->renderPage(
            ['message' => 'Password berhasil direset. Silakan login dengan password baru',],
            ['path' => '/password/reset', 'meta' => ['title' => 'Password Direset | ' . env('APP_NAME')]]
        );
    }

    public function listUsers(Request $request, Response $response): View | RedirectResponse
    {
        $users = $this->users->all();
        return $response->renderPage(['users' => $users], [
            'path' => '/admin/users',
            'meta' => ['title' => 'Manajemen Pengguna | ' . env('APP_NAME')]
        ]);
    }

    public function updateUser(Request $request, Response $response): RedirectResponse
    {
        $userId = $request->input('user_id');
        $role = $request->input('role');
        $permissions = $request->input('permissions') ?? [];

        if (!$userId || !in_array($role, ['admin', 'user'])) {
            return $response->redirect('/admin/users?error=Data+input+tidak+valid');
        }

        $user = $this->users->find($userId);
        if (!$user) {
            return $response->redirect('/admin/users?error=Pengguna+tidak+ditemukan');
        }

        $this->users->updateById($userId, [
            'role' => $role,
            'permissions' => json_encode($permissions)
        ]);

        if ($this->session->get('auth.user_id') == $userId) {
            $this->session->set('auth.user_role', $role);
            $this->session->set('auth.user_permissions', $permissions);
        }

        return $response->redirect('/admin/users?success=Hak+akses+pengguna+berhasil+diperbarui');
    }

    private function convertDateToDb(?string $dateStr): ?string
    {
        if (empty($dateStr)) return null;
        $parts = explode('/', $dateStr);
        if (count($parts) === 3) {
            return $parts[2] . '-' . $parts[1] . '-' . $parts[0];
        }
        return $dateStr;
    }

    private function convertDateToUi(?string $dateStr): string
    {
        if (empty($dateStr)) return '';
        $timestamp = strtotime($dateStr);
        return $timestamp ? date('d/m/Y', $timestamp) : $dateStr;
    }

    public function showProfile(Request $request, Response $response): View | RedirectResponse
    {
        $user = $this->user();
        if (!$user) {
            return $response->redirect('/login');
        }

        $response->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $response->setHeader('Pragma', 'no-cache');

        $registration = $this->registrations->findByUserId($user['id']);
        $regId = $registration ? $registration['id'] : null;

        $parents = $regId ? $this->parents->findByRegistrationId($regId) : null;
        if ($parents) {
            $parents['father_birth_date'] = $this->convertDateToUi($parents['father_birth_date'] ?? '');
            $parents['mother_birth_date'] = $this->convertDateToUi($parents['mother_birth_date'] ?? '');
            $parents['guardian_birth_date'] = $this->convertDateToUi($parents['guardian_birth_date'] ?? '');
        }

        $address = $regId ? $this->addresses->findByRegistrationId($regId) : null;
        $education = $regId ? $this->educations->findByRegistrationId($regId) : null;
        $specialNeeds = $regId ? $this->specialNeeds->findByRegistrationId($regId) : null;

        $jsonData = json_decode(file_get_contents(MAZU_ENV_PATH . 'data.json'), true);
        $wilayahList = $jsonData['wilayah'][0] ?? [];
        $agamaList = $jsonData['agama'][0] ?? [];
        $negaraList = $jsonData['kewarganegaraan'][0] ?? [];
        $tinggalList = $jsonData['jenis_tinggal'][0] ?? [];
        $transportList = $jsonData['alat_transportasi'][0] ?? [];
        $pendidikanList = $jsonData['jenjang_pendidikan'][0] ?? [];
        usort($pendidikanList, fn($a, $b) => ((int)($a['id_jenj_didik'] ?? 0)) <=> ((int)($b['id_jenj_didik'] ?? 0)));
        $penghasilanList = array_values(array_filter($jsonData['penghasilan'][0] ?? [], fn($item) => !empty($item['nm_penghasilan'])));
        usort($penghasilanList, fn($a, $b) => ((int)($a['id_penghasilan'] ?? 0)) <=> ((int)($b['id_penghasilan'] ?? 0)));
        $pekerjaanList = $jsonData['pekerjaan'][0] ?? [];
        usort($pekerjaanList, function ($a, $b) {
            $nameA = $a['nm_pekerjaan'] ?? '';
            $nameB = $b['nm_pekerjaan'] ?? '';
            if ($nameA === 'Tidak bekerja') return -1;
            if ($nameB === 'Tidak bekerja') return 1;
            return strcasecmp($nameA, $nameB);
        });
        $kebutuhanKhususList = $jsonData['kebutuhan_khusus'][0] ?? [];
        if (empty($kebutuhanKhususList)) {
            $kebutuhanKhususList = [
                ['nm_kebutuhan_khusus' => 'Tuna Netra'],
                ['nm_kebutuhan_khusus' => 'Tuna Rungu'],
                ['nm_kebutuhan_khusus' => 'Tuna Wicara'],
                ['nm_kebutuhan_khusus' => 'Tuna Daksa'],
                ['nm_kebutuhan_khusus' => 'Tuna Grahita'],
                ['nm_kebutuhan_khusus' => 'Tuna Laras'],
                ['nm_kebutuhan_khusus' => 'Autis'],
                ['nm_kebutuhan_khusus' => 'Lainnya']
            ];
        }

        return $response->renderPage([
            'user' => $user,
            'registration' => $registration,
            'address' => $address,
            'parents' => $parents,
            'education' => $education,
            'special_needs' => $specialNeeds,
            'wilayahList' => $wilayahList,
            'agamaList' => $agamaList,
            'negaraList' => $negaraList,
            'tinggalList' => $tinggalList,
            'transportList' => $transportList,
            'pendidikanList' => $pendidikanList,
            'penghasilanList' => $penghasilanList,
            'pekerjaanList' => $pekerjaanList,
            'kebutuhanKhususList' => $kebutuhanKhususList
        ], [
            'path' => '/profile',
            'meta' => ['title' => 'Profil Saya | ' . env('APP_NAME')]
        ]);
    }

    public function updateProfile(Request $request, Response $response): RedirectResponse
    {
        $user = $this->user();
        if (!$user) {
            return $response->redirect('/login');
        }

        $tab = $request->input('tab') ?: 'alamat';

        $registration = $this->registrations->findByUserId($user['id']);

        if ($tab === 'password') {
            $currentPassword = $request->input('current_password');
            $newPassword = $request->input('new_password');
            $newPasswordConfirmation = $request->input('new_password_confirmation');

            if (empty($currentPassword) || empty($newPassword) || empty($newPasswordConfirmation)) {
                return $response->redirect('/profile?tab=password&error=Seluruh+kolom+password+harus+diisi');
            }

            if (!$this->verifyPassword($currentPassword, $user['password'])) {
                return $response->redirect('/profile?tab=password&error=Password+saat+ini+salah');
            }

            if ($newPassword !== $newPasswordConfirmation) {
                return $response->redirect('/profile?tab=password&error=Konfirmasi+password+baru+tidak+cocok');
            }

            $validation = $this->validatePassword($newPassword);
            if (!$validation['valid']) {
                return $response->redirect('/profile?tab=password&error=' . urlencode(implode(', ', $validation['errors'])));
            }

            $this->users->updateById($user['id'], [
                'password' => password_hash($newPassword, PASSWORD_BCRYPT)
            ]);

            return $response->redirect('/profile?tab=password&success=Password+berhasil+diubah');
        }

        $registration = $this->registrations->findByUserId($user['id']);
        if (!$registration) {
            $regId = $this->registrations->insert([
                'user_id' => $user['id'],
                'wave_id' => null,
                'full_name' => $user['name'] ?: '',
                'birth_place' => '',
                'birth_date' => '1970-01-01',
                'gender' => 'Laki-laki',
                'religion' => '',
                'status' => 'Draft',
                'current_step' => 1
            ]);
            $registration = $this->registrations->find($regId);
        }
        $regId = $registration['id'];

        if ($tab === 'alamat') {
            $existingAddr = $this->addresses->findByRegistrationId($regId);
            $citizenship = $request->input('citizenship') ?: ($existingAddr['citizenship'] ?? 'WNI');
            $nik = $request->input('nik') ?: $registration['nik'];
            $nisn = $request->input('nisn') ?: $registration['nisn'];
            $npwp = $request->input('npwp') ?: null;
            $street = $request->input('street') ?: null;
            $telephone = $request->input('telephone') ?: null;
            $dusun = $request->input('dusun') ?: null;
            $rt = $request->input('rt') ?: null;
            $rw = $request->input('rw') ?: null;
            $hp = $request->input('hp') ?: $request->input('phone') ?: $registration['phone'];
            $kelurahan = $request->input('subdistrict');
            $postalCode = $request->input('postal_code') ?: null;
            $email = $request->input('email');
            $kps_receiver = $request->input('kps_receiver');
            $kps_number = $request->input('kps_number') ?: null;
            $kecamatan = $request->input('district');
            $districtIdWil = $request->input('district_id_wil') ?: null;
            $transportation = $request->input('transportation') ?: null;
            $living_type = $request->input('living_type') ?: null;
            $province = $request->input('province') ?: null;
            $city = $request->input('city') ?: null;
            $addressDetail = $request->input('address') ?: null;

            $npwp = $npwp ? substr($npwp, 0, 30) : null;
            $street = $street ? substr($street, 0, 100) : null;
            $telephone = $telephone ? substr($telephone, 0, 15) : null;
            $dusun = $dusun ? substr($dusun, 0, 50) : null;
            $rt = $rt ? substr($rt, 0, 5) : null;
            $rw = $rw ? substr($rw, 0, 5) : null;
            $kelurahan = $kelurahan ? substr($kelurahan, 0, 100) : null;
            $postalCode = $postalCode ? substr($postalCode, 0, 5) : null;
            $kps_number = $kps_number ? substr($kps_number, 0, 50) : null;
            $kecamatan = $kecamatan ? substr($kecamatan, 0, 100) : null;
            $province = $province ? substr($province, 0, 100) : null;
            $city = $city ? substr($city, 0, 100) : null;

            if (!$email || !$kelurahan || !$kps_receiver || !$kecamatan || !$addressDetail) {
                return $response->redirect('/profile?tab=alamat&error=Harap+isi+semua+kolom+wajib+pada+alamat');
            }

            if ($kps_receiver === 'ya' && !$kps_number) {
                return $response->redirect('/profile?tab=alamat&error=Nomor+KPS+wajib+diisi');
            }

            $this->registrations->updateById($regId, [
                'nik' => $nik,
                'nisn' => $nisn,
                'phone' => $hp,
                'email' => $email
            ]);

            $addrData = [
                'registration_id' => $regId,
                'citizenship' => $citizenship,
                'npwp' => $npwp,
                'street' => $street,
                'telephone' => $telephone,
                'dusun' => $dusun,
                'rt' => $rt,
                'rw' => $rw,
                'subdistrict' => $kelurahan,
                'kps_receiver' => $kps_receiver,
                'kps_number' => $kps_receiver === 'ya' ? $kps_number : null,
                'district' => $kecamatan,
                'district_id_wil' => $districtIdWil,
                'transportation' => $transportation,
                'living_type' => $living_type,
                'postal_code' => $postalCode,
                'province' => $province,
                'city' => $city,
                'address' => $addressDetail
            ];

            $existingAddr = $this->addresses->findByRegistrationId($regId);
            if ($existingAddr) {
                $this->addresses->updateById($existingAddr['id'], $addrData);
            } else {
                $this->addresses->insert($addrData);
            }

            $this->registrations->updateById($regId, [
                'email' => $email,
                'phone' => $hp
            ]);

            return $response->redirect('/profile?tab=alamat&success=Alamat+dan+kontak+berhasil+diperbarui');
        }

        if ($tab === 'ortu') {
            $fatherName = $request->input('father_name');
            $fatherNik = $request->input('father_nik');
            $fatherBirthDate = $request->input('father_birth_date');
            $fatherEducation = $request->input('father_education');
            $fatherOccupation = $request->input('father_occupation');
            $fatherIncome = $request->input('father_income');

            $motherName = $request->input('parent_mother_name') ?: $request->input('mother_name');
            $motherNik = $request->input('mother_nik');
            $motherBirthDate = $request->input('mother_birth_date');
            $motherEducation = $request->input('mother_education');
            $motherOccupation = $request->input('mother_occupation');
            $motherIncome = $request->input('mother_income');

            $guardianName = $request->input('guardian_name');
            $guardianBirthDate = $request->input('guardian_birth_date');
            $guardianEducation = $request->input('guardian_education');
            $guardianOccupation = $request->input('guardian_occupation');
            $guardianIncome = $request->input('guardian_income');

            $isParentFilled = !empty($fatherName) || !empty($motherName);

            if ($isParentFilled) {
                if (
                    !$fatherName || !$fatherNik || !$fatherBirthDate || !$fatherEducation || !$fatherOccupation || !$fatherIncome ||
                    !$motherName || !$motherNik || !$motherBirthDate || !$motherEducation || !$motherOccupation || !$motherIncome
                ) {
                    return $response->redirect('/profile?tab=ortu&error=Harap+lengkapi+semua+kolom+data+Orang+Tua+(Ayah+dan+Ibu)');
                }
            } else {
                if (!$guardianName || !$guardianBirthDate || !$guardianEducation || !$guardianOccupation || !$guardianIncome) {
                    return $response->redirect('/profile?tab=ortu&error=Jika+Orang+Tua+tidak+diisi,+maka+semua+kolom+data+Wali+wajib+diisi');
                }
            }

            $parentData = [
                'registration_id' => $regId,
                'father_name' => $fatherName ?: null,
                'father_nik' => $fatherNik ?: null,
                'father_birth_date' => $fatherBirthDate ? $this->convertDateToDb($fatherBirthDate) : null,
                'father_education' => $fatherEducation ?: null,
                'father_occupation' => $fatherOccupation ?: null,
                'father_income' => $fatherIncome ?: null,
                'mother_name' => $motherName ?: null,
                'mother_nik' => $motherNik ?: null,
                'mother_birth_date' => $motherBirthDate ? $this->convertDateToDb($motherBirthDate) : null,
                'mother_education' => $motherEducation ?: null,
                'mother_occupation' => $motherOccupation ?: null,
                'mother_income' => $motherIncome ?: null,
                'guardian_name' => $guardianName ?: null,
                'guardian_birth_date' => $guardianBirthDate ? $this->convertDateToDb($guardianBirthDate) : null,
                'guardian_education' => $guardianEducation ?: null,
                'guardian_occupation' => $guardianOccupation ?: null,
                'guardian_income' => $guardianIncome ?: null
            ];

            $this->registrations->updateById($regId, ['mother_name' => $motherName]);

            $existingParents = $this->parents->findByRegistrationId($regId);
            if ($existingParents) {
                $this->parents->updateById($existingParents['id'], $parentData);
            } else {
                $this->parents->insert($parentData);
            }

            return $response->redirect('/profile?tab=ortu&success=Data+orang+tua+dan+wali+berhasil+diperbarui');
        }

        if ($tab === 'kebutuhan') {
            $hasSpecialNeeds = $request->input('has_special_needs');
            $studentNeeds = $request->input('student_needs') ?: [];
            $fatherNeeds = $request->input('father_needs') ?: [];
            $motherNeeds = $request->input('mother_needs') ?: [];
            $guardianNeeds = $request->input('guardian_needs') ?: [];

            if (!$hasSpecialNeeds) {
                return $response->redirect('/profile?tab=kebutuhan&error=Pilihan+Kebutuhan+Khusus+wajib+diisi');
            }

            $needsData = [
                'registration_id' => $regId,
                'has_special_needs' => $hasSpecialNeeds,
                'student_needs' => json_encode($studentNeeds),
                'father_needs' => json_encode($fatherNeeds),
                'mother_needs' => json_encode($motherNeeds),
                'guardian_needs' => json_encode($guardianNeeds)
            ];

            $existingNeeds = $this->specialNeeds->findByRegistrationId($regId);
            if ($existingNeeds) {
                $this->specialNeeds->updateById($existingNeeds['id'], $needsData);
            } else {
                $this->specialNeeds->insert($needsData);
            }

            return $response->redirect('/profile?tab=kebutuhan&success=Kebutuhan+khusus+berhasil+diperbarui');
        }

        if ($tab === 'pendidikan') {
            $schoolName = $request->input('school_name');
            $schoolMajor = $request->input('school_major');
            $graduationYear = $request->input('graduation_year');
            $diplomaNumber = $request->input('diploma_number');
            $averageScore = $request->input('average_score');
            $schoolAddress = $request->input('school_address');
            $schoolAddressIdWil = $request->input('school_address_id_wil');

            if (!$schoolName || !$schoolMajor || !$graduationYear || !$diplomaNumber || !$averageScore || !$schoolAddress) {
                return $response->redirect('/profile?tab=pendidikan&error=Harap+isi+semua+kolom+riwayat+pendidikan+wajib');
            }

            if (!is_numeric($graduationYear)) {
                return $response->redirect('/profile?tab=pendidikan&error=Tahun+lulus+harus+berupa+angka');
            }

            if (!is_numeric($averageScore) || $averageScore < 0 || $averageScore > 100) {
                return $response->redirect('/profile?tab=pendidikan&error=Rata-rata+nilai+harus+berupa+angka+antara+0+sampai+100');
            }

            $eduData = [
                'registration_id' => $regId,
                'school_name' => $schoolName,
                'school_major' => $schoolMajor,
                'graduation_year' => $graduationYear,
                'diploma_number' => $diplomaNumber,
                'average_score' => $averageScore,
                'school_address' => $schoolAddress,
                'school_address_id_wil' => $schoolAddressIdWil ?: null
            ];

            $existingEdu = $this->educations->findByRegistrationId($regId);
            if ($existingEdu) {
                $this->educations->updateById($existingEdu['id'], $eduData);
            } else {
                $this->educations->insert($eduData);
            }

            return $response->redirect('/profile?tab=pendidikan&success=Riwayat+pendidikan+berhasil+diperbarui');
        }

        return $response->redirect('/profile?error=Aksi+tidak+valid');
    }
}
