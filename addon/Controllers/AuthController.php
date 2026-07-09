<?php

namespace Addon\Controllers;

use Addon\Models\UserModel;
use Addon\Models\EmailVerificationModel;
use Addon\Models\PasswordResetTokenModel;
use Addon\Models\LoginNotificationModel;
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
        log_activity('LOGIN_SUCCESS', "Pengguna {$user['email']} berhasil masuk ke sistem.");
    }

    /**
     * Logout user
     */
    private function logoutSession(): void
    {
        $email = $this->session->get('auth.user_email') ?? 'User';
        log_activity('LOGOUT', "Pengguna {$email} keluar dari sistem.");
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
    public function login(Request $request, Response $response): View | RedirectResponse
    {
        $email = $request->input('email');
        $password = $request->input('password');

        if (!$email || !$password) {
            return $response->redirect('/login?error=Email+dan+password+harus+diisi');
        }

        // Find user by email
        $user = $this->users->findByEmail($email);

        if (!$user) {
            log_activity('LOGIN_FAILED', "Percobaan masuk gagal: Email {$email} tidak ditemukan.");
            return $response->redirect('/login?error=Email+tidak+ditemukan');
        }



        // Verify password
        if (!$this->verifyPassword($password, $user['password'])) {
            log_activity('LOGIN_FAILED', "Percobaan masuk gagal: Password salah untuk pengguna {$email}.");
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
        $this->sendLoginNotification($user);

        return $response->redirect('/dashboard')->hard();
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
            $this->emailService->sendOtpVerification($email, $name, $otpCode, 15);

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

    public function showProfile(Request $request, Response $response): View | RedirectResponse
    {
        $user = $this->user();
        if (!$user) {
            return $response->redirect('/login');
        }

        return $response->renderPage(['user' => $user], [
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

        $name = $request->input('name');
        $email = $request->input('email');
        $currentPassword = $request->input('current_password');
        $newPassword = $request->input('new_password');
        $newPasswordConfirmation = $request->input('new_password_confirmation');

        if (!$name || !$email) {
            return $response->redirect('/profile?error=Nama+dan+email+tidak+boleh+kosong');
        }

        if (strtolower($email) !== strtolower($user['email'])) {
            $existing = $this->users->findByEmail($email);
            if ($existing) {
                return $response->redirect('/profile?error=Email+sudah+terdaftar+oleh+pengguna+lain');
            }
        }

        $updateData = [
            'name' => $name,
            'email' => $email,
        ];

        if (!empty($currentPassword) || !empty($newPassword) || !empty($newPasswordConfirmation)) {
            if (empty($currentPassword) || empty($newPassword) || empty($newPasswordConfirmation)) {
                return $response->redirect('/profile?error=Untuk+mengganti+password,+seluruh+kolom+password+harus+diisi');
            }

            if (!$this->verifyPassword($currentPassword, $user['password'])) {
                return $response->redirect('/profile?error=Password+saat+ini+salah');
            }

            if ($newPassword !== $newPasswordConfirmation) {
                return $response->redirect('/profile?error=Konfirmasi+password+baru+tidak+cocok');
            }

            $validation = $this->validatePassword($newPassword);
            if (!$validation['valid']) {
                return $response->redirect('/profile?error=' . urlencode(implode(', ', $validation['errors'])));
            }

            $updateData['password'] = $this->hashPassword($newPassword);
        }

        $this->users->updateById($user['id'], $updateData);

        $this->session->set('auth.user_name', $name);
        $this->session->set('auth.user_email', $email);

        return $response->redirect('/profile?success=Profil+Anda+berhasil+diperbarui');
    }
}