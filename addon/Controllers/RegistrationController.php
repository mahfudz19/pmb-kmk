<?php

namespace Addon\Controllers;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\View\View;
use App\Core\Http\RedirectResponse;
use Addon\Models\RegistrationModel;
use Addon\Models\RegistrationAddressModel;
use Addon\Models\RegistrationParentModel;
use Addon\Models\RegistrationEducationModel;
use Addon\Models\RegistrationProgramModel;
use Addon\Models\AcademicYearModel;
use Addon\Models\WaveModel;
use Addon\Models\AdmissionPathModel;
use Addon\Models\ClassModel;
use Addon\Models\StudyProgramModel;

use Addon\Models\RegistrationSpecialNeedModel;

class RegistrationController
{
    public function __construct(
        private RegistrationModel $registrations,
        private RegistrationAddressModel $addresses,
        private RegistrationParentModel $parents,
        private RegistrationEducationModel $educations,
        private RegistrationProgramModel $programs,
        private AcademicYearModel $academicYears,
        private WaveModel $waves,
        private AdmissionPathModel $admissionPaths,
        private ClassModel $classes,
        private StudyProgramModel $studyPrograms,
        private RegistrationSpecialNeedModel $specialNeeds
    ) {}

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

    public function showForm(Request $request, Response $response): View|RedirectResponse
    {
        return $response->redirect('/dashboard');
    }

    public function saveDraft(Request $request, Response $response): Response
    {
        $userId = $_SESSION['auth.user_id'] ?? null;
        if (!$userId) {
            $response->setStatusCode(401);
            return $response->json(['success' => false, 'message' => 'Unauthorized']);
        }

        $step = (int) $request->input('step');
        $registration = $this->registrations->findByUserId($userId);

        $db = $this->registrations->getDb();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $userId]);
        $user = $stmt->fetch() ?: null;

        if ($registration && in_array($registration['status'], ['Verified', 'Released'])) {
            $response->setStatusCode(403);
            return $response->json(['success' => false, 'message' => 'Pendaftaran sudah diverifikasi dan tidak dapat diubah']);
        }

        $regId = $registration ? $registration['id'] : null;

        $targetWaveId = null;
        if ($step === 1) {
            $targetWaveId = $request->input('wave_id') ?: ($registration['wave_id'] ?? null);
        } else {
            $targetWaveId = $registration['wave_id'] ?? null;
        }

        if ($targetWaveId) {
            $stmtWave = $db->prepare("SELECT * FROM waves WHERE id = :id LIMIT 1");
            $stmtWave->execute(['id' => $targetWaveId]);
            $w = $stmtWave->fetch() ?: null;
            if (!$w || (int)$w['is_active'] !== 1) {
                $response->setStatusCode(400);
                return $response->json(['success' => false, 'message' => 'Gelombang pendaftaran yang Anda pilih saat ini tidak aktif atau sudah ditutup.']);
            }
        }

        if ($step === 1) {
            $waveId = $request->input('wave_id') ?: ($registration['wave_id'] ?? null);
            $prog1Id = $request->input('program1_id');
            $prog2Id = $request->input('program2_id') ?: null;
            $prog3Id = $request->input('program3_id') ?: null;

            if (!$waveId || !$prog1Id) {
                $response->setStatusCode(400);
                return $response->json(['success' => false, 'message' => 'Harap lengkapi semua pilihan PMB wajib']);
            }

            $prodiIds = array_filter([$prog1Id, $prog2Id, $prog3Id]);
            if (count($prodiIds) !== count(array_unique($prodiIds))) {
                $response->setStatusCode(400);
                return $response->json(['success' => false, 'message' => 'Pilihan program studi tidak boleh ada yang sama']);
            }

            $regData = [
                'user_id' => $userId,
                'wave_id' => $waveId,
                'status' => 'Draft'
            ];

            if ($regId) {
                $this->registrations->updateById($regId, $regData);
            } else {
                $regData['full_name'] = '';
                $regData['birth_place'] = '';
                $regData['birth_date'] = '1970-01-01';
                $regData['gender'] = 'Laki-laki';
                $regData['religion'] = '';
                $regId = $this->registrations->insert($regData);
            }

            $progData = [
                'registration_id' => $regId,
                'program1_id' => $prog1Id,
                'program2_id' => $prog2Id,
                'program3_id' => $prog3Id
            ];

            $existingProg = $this->programs->findByRegistrationId($regId);
            if ($existingProg) {
                $this->programs->updateById($existingProg['id'], $progData);
            } else {
                $this->programs->insert($progData);
            }
        }

        if ($step === 2) {
            if (!$regId) {
                $response->setStatusCode(400);
                return $response->json(['success' => false, 'message' => 'Selesaikan data pilihan program studi terlebih dahulu']);
            }

            $fullName = $request->input('full_name');
            $nik = $request->input('nik');
            $nisn = $request->input('nisn');
            $birthPlace = $request->input('birth_place');
            $birthDate = $request->input('birth_date');
            $gender = $request->input('gender');
            $religion = $request->input('religion');
            $phone = $request->input('phone') ?: $request->input('hp');
            $email = $user['email'];
            $infoSource = $request->input('info_source');
            $citizenship = $request->input('citizenship');

            if (!$fullName || !$nik || !$birthPlace || !$birthDate || !$gender || !$religion || !$phone || !$infoSource || !$citizenship) {
                $response->setStatusCode(400);
                return $response->json(['success' => false, 'message' => 'Harap isi semua kolom wajib pada data pribadi']);
            }

            if (!is_numeric($nik) || strlen($nik) !== 16) {
                $response->setStatusCode(400);
                return $response->json(['success' => false, 'message' => 'NIK harus berupa angka dan berjumlah 16 digit']);
            }

            if (!empty($nisn) && (!is_numeric($nisn) || strlen($nisn) !== 10)) {
                $response->setStatusCode(400);
                return $response->json(['success' => false, 'message' => 'NISN harus berupa angka dan berjumlah 10 digit']);
            }

            if (!is_numeric($phone) || strlen($phone) < 9 || strlen($phone) > 15) {
                $response->setStatusCode(400);
                return $response->json(['success' => false, 'message' => 'Nomor HP harus berupa angka dengan panjang 9-15 digit']);
            }

            $photoPath = $registration['photo_path'] ?? null;
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['photo'];
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                if (!in_array($ext, ['jpg', 'jpeg', 'png'])) {
                    $response->setStatusCode(400);
                    return $response->json(['success' => false, 'message' => 'Format foto harus berupa JPG, JPEG, atau PNG']);
                }
                $uploadDir = MAZU_PUBLIC_PATH . 'uploads/photos/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $filename = 'photo_' . $regId . '_' . uniqid() . '.' . $ext;
                if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
                    $photoPath = '/uploads/photos/' . $filename;
                }
            }

            if (!$photoPath) {
                $response->setStatusCode(400);
                return $response->json(['success' => false, 'message' => 'Foto resmi 3x4 wajib diunggah']);
            }

            $regData = [
                'full_name' => $fullName,
                'nik' => $nik,
                'nisn' => $nisn,
                'birth_place' => $birthPlace,
                'birth_date' => $this->convertDateToDb($birthDate),
                'gender' => $gender,
                'religion' => $religion,
                'phone' => $phone,
                'email' => $email,
                'info_source' => $infoSource,
                'photo_path' => $photoPath
            ];

            $this->registrations->updateById($regId, $regData);

            $addr = $this->addresses->findByRegistrationId($regId);
            if (!$addr) {
                $this->addresses->insert([
                    'registration_id' => $regId,
                    'citizenship' => $citizenship
                ]);
            } else {
                $this->addresses->updateById($addr['id'], [
                    'citizenship' => $citizenship
                ]);
            }
        }

        if ($regId) {
            $targetStep = $request->input('current_step');
            $stepToPersist = $targetStep ? (int)$targetStep : $step;
            $this->registrations->updateById($regId, ['current_step' => $stepToPersist]);
        }

        return $response->json(['success' => true, 'message' => 'Draft berhasil disimpan', 'registration_id' => $regId]);
    }

    public function submit(Request $request, Response $response): RedirectResponse
    {
        $userId = $_SESSION['auth.user_id'] ?? null;
        if (!$userId) {
            return $response->redirect('/login');
        }

        $registration = $this->registrations->findByUserId($userId);

        $db = $this->registrations->getDb();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $userId]);
        $user = $stmt->fetch() ?: null;

        if (!$registration || in_array($registration['status'], ['Verified', 'Released'])) {
            return $response->redirect('/dashboard?error=Pendaftaran+tidak+valid+atau+sudah+dikunci');
        }

        $regId = $registration['id'];

        $existingProg = $this->programs->findByRegistrationId($regId);

        $waveId = $request->input('wave_id') ?: ($registration['wave_id'] ?? null);
        $prog1Id = $request->input('program1_id') ?: ($existingProg['program1_id'] ?? null);
        $prog2Id = $request->input('program2_id') ?: ($existingProg['program2_id'] ?? null);
        $prog3Id = $request->input('program3_id') ?: ($existingProg['program3_id'] ?? null);

        if (!$waveId || !$prog1Id) {
            return $response->redirect('/pendaftaran?error=' . urlencode('Harap lengkapi semua pilihan PMB dan program studi wajib sebelum memfinalisasi pendaftaran.'));
        }

        $stmtWave = $db->prepare("SELECT * FROM waves WHERE id = :id LIMIT 1");
        $stmtWave->execute(['id' => $waveId]);
        $w = $stmtWave->fetch() ?: null;
        if (!$w || (int)$w['is_active'] !== 1) {
            return $response->redirect('/pendaftaran?error=' . urlencode('Gelombang pendaftaran tidak aktif atau sudah ditutup.'));
        }

        $prodiIds = array_filter([$prog1Id, $prog2Id, $prog3Id]);
        if (count($prodiIds) !== count(array_unique($prodiIds))) {
            return $response->redirect('/pendaftaran?error=' . urlencode('Pilihan program studi tidak boleh ada yang sama.'));
        }

        $email = ($registration['email'] ?? '') ?: ($user['email'] ?? '');
        if (empty($registration['nik']) || empty($registration['phone']) || empty($email)) {
            return $response->redirect('/pendaftaran?error=' . urlencode('Harap lengkapi Data Pribadi di formulir terlebih dahulu.'));
        }

        $this->registrations->updateById($regId, [
            'wave_id' => $waveId,
            'status' => 'Submitted'
        ]);

        $progData = [
            'registration_id' => $regId,
            'program1_id' => $prog1Id,
            'program2_id' => $prog2Id,
            'program3_id' => $prog3Id
        ];

        if ($existingProg) {
            $this->programs->updateById($existingProg['id'], $progData);
        } else {
            $this->programs->insert($progData);
        }

        $baseAmount = (float)($w['registration_fee_total'] ?? 0);

        if ($baseAmount > 0) {
            $stmtCheckPay = $db->prepare("SELECT * FROM registration_payments WHERE registration_id = :reg_id LIMIT 1");
            $stmtCheckPay->execute(['reg_id' => $regId]);
            $existingPayment = $stmtCheckPay->fetch();
            $stmtCheckPay->closeCursor();

            if (!$existingPayment) {
                $stmtFind999 = $db->prepare("SELECT id FROM registration_payments WHERE id_payment = 999 LIMIT 1");
                $stmtFind999->execute();
                $has999 = $stmtFind999->fetch();
                $stmtFind999->closeCursor();

                $nextIdPayment = 1;
                if (!$has999) {
                    $stmtMax = $db->prepare("SELECT MAX(id_payment) as max_id FROM registration_payments");
                    $stmtMax->execute();
                    $maxRow = $stmtMax->fetch();
                    $stmtMax->closeCursor();

                    $maxVal = $maxRow ? (int)$maxRow['max_id'] : 0;
                    $nextIdPayment = $maxVal + 1;
                    if ($nextIdPayment >= 1000) {
                        $nextIdPayment = 1;
                    }
                }

                $finalAmount = $baseAmount + $nextIdPayment;

                $stmtInsertPay = $db->prepare("
                    INSERT INTO registration_payments 
                    (registration_id, bank_name, account_name, amount, payment_date, file_path, status, id_payment, payment_type, created_at, updated_at) 
                    VALUES 
                    (:reg_id, '', '', :amount, :pay_date, '', 'Pending', :id_payment, 'manual', :created_at, :updated_at)
                ");
                $stmtInsertPay->execute([
                    'reg_id' => $regId,
                    'amount' => $finalAmount,
                    'pay_date' => date('Y-m-d'),
                    'id_payment' => $nextIdPayment,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                $stmtInsertPay->closeCursor();
            }
        }

        return $response->redirect('/dashboard?success=Pendaftaran+berhasil+dikunci.+Panitia+akan+segera+memverifikasi+berkas+Anda.');
    }

    public function updateActiveStep(Request $request, Response $response): Response
    {
        $userId = $_SESSION['auth.user_id'] ?? null;
        if (!$userId) {
            $response->setStatusCode(401);
            return $response->json(['success' => false, 'message' => 'Unauthorized']);
        }

        $currentStep = (int) $request->input('current_step');

        if ($currentStep < 1 || $currentStep > 3) {
            $response->setStatusCode(400);
            return $response->json(['success' => false, 'message' => 'Langkah tidak valid']);
        }

        $registration = $this->registrations->findByUserId($userId);
        if ($registration) {
            if (in_array($registration['status'], ['Verified', 'Released'])) {
                $response->setStatusCode(403);
                return $response->json(['success' => false, 'message' => 'Pendaftaran sudah diverifikasi']);
            }
            $this->registrations->updateById($registration['id'], [
                'current_step' => $currentStep
            ]);
        }

        return $response->json(['success' => true]);
    }

    public function resetWave(Request $request, Response $response): Response
    {
        $userId = $_SESSION['auth.user_id'] ?? null;
        if (!$userId) {
            $response->setStatusCode(401);
            return $response->json(['success' => false, 'message' => 'Unauthorized']);
        }

        $registration = $this->registrations->findByUserId($userId);
        if ($registration) {
            if (in_array($registration['status'], ['Verified', 'Released', 'Submitted'])) {
                $response->setStatusCode(400);
                return $response->json(['success' => false, 'message' => 'Tidak dapat merubah gelombang pendaftaran']);
            }

            $this->registrations->updateById($registration['id'], [
                'wave_id' => null,
                'current_step' => 1
            ]);

            $db = $this->registrations->getDb();
            $stmt = $db->prepare("DELETE FROM registration_programs WHERE registration_id = :id");
            $stmt->execute(['id' => $registration['id']]);
        }

        return $response->json(['success' => true]);
    }
}
