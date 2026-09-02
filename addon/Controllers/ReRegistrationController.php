<?php

namespace Addon\Controllers;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\View\View;
use App\Core\Http\RedirectResponse;
use App\Services\SessionService;

use Addon\Models\ReRegistrationModel;
use Addon\Models\RegistrationModel;
use Addon\Models\SelectionResultModel;
use Addon\Models\WaveStudyProgramModel;
use Addon\Models\NimSettingModel;

class ReRegistrationController
{
    public function __construct(
        private SessionService $session,
        private ReRegistrationModel $reRegistrations,
        private RegistrationModel $registrations,
        private SelectionResultModel $selectionResults,
        private WaveStudyProgramModel $waveStudyPrograms,
        private NimSettingModel $nimSettings
    ) {}

    private function getTuitionFee(int $programId): float
    {
        return match ($programId) {
            1 => 7500000.00,
            2 => 7000000.00,
            3 => 8000000.00,
            4 => 6000000.00,
            5 => 6000000.00,
            default => 5000000.00
        };
    }

    public function showReRegistrationForm(Request $request, Response $response): View | RedirectResponse
    {
        if (!$this->session->get('is_logged_in')) {
            return $response->redirect('/login');
        }

        $userId = $this->session->get('auth.user_id');
        $registration = $this->registrations->findByUserId($userId);

        if (!$registration || $registration['status'] === 'Draft') {
            return $response->redirect('/dashboard');
        }

        $selection = $this->selectionResults->findByRegistrationId($registration['id']);
        if (!$selection || $selection['status'] !== 'Lulus' || (int)$selection['is_published'] === 0) {
            return $response->redirect('/dashboard?error=Anda+belum+dinyatakan+lulus+seleksi.');
        }

        $passedProgramId = $selection['passed_program_id'];
        $db = $this->registrations->getDb();
        $stmt = $db->prepare("SELECT * FROM study_programs WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $passedProgramId]);
        $program = $stmt->fetch();
        $programName = $program ? $program['name'] : '-';

        $stmt = $db->prepare("SELECT * FROM wave_study_programs WHERE wave_id = :wave_id AND study_program_id = :prodi_id LIMIT 1");
        $stmt->execute([
            'wave_id' => $registration['wave_id'],
            'prodi_id' => $passedProgramId
        ]);
        $waveStudyProgram = $stmt->fetch() ?: null;

        $tuitionFee = $waveStudyProgram ? (float)$waveStudyProgram['reregistration_fee_total'] : $this->getTuitionFee($passedProgramId);

        $stmt = $db->prepare("SELECT * FROM payment_accounts WHERE is_active = 1 LIMIT 1");
        $stmt->execute();
        $activePaymentAccount = $stmt->fetch() ?: null;

        $reReg = $this->reRegistrations->findByRegistrationId($registration['id']);
        if (!$reReg) {
            $stmtFind999 = $db->prepare("SELECT id FROM re_registrations WHERE id_payment = 999 LIMIT 1");
            $stmtFind999->execute();
            $has999 = $stmtFind999->fetch();
            $stmtFind999->closeCursor();

            $nextIdPayment = 1;
            if (!$has999) {
                $stmtMax = $db->prepare("SELECT MAX(id_payment) as max_id FROM re_registrations");
                $stmtMax->execute();
                $maxRow = $stmtMax->fetch();
                $stmtMax->closeCursor();

                $maxVal = $maxRow ? (int)$maxRow['max_id'] : 0;
                $nextIdPayment = $maxVal + 1;
                if ($nextIdPayment >= 1000) {
                    $nextIdPayment = 1;
                }
            }

            $finalAmount = $tuitionFee + $nextIdPayment;

            $newReRegId = $this->reRegistrations->insert([
                'registration_id' => $registration['id'],
                'skl_path' => null,
                'health_path' => null,
                'statement_path' => null,
                'payment_path' => null,
                'payment_amount' => $finalAmount,
                'status' => 'Pending',
                'id_payment' => $nextIdPayment,
                'payment_type' => 'manual',
                'rejection_reason' => null,
                'verified_by' => null,
                'verified_at' => null,
                'dynamic_documents' => json_encode([])
            ]);
            $reReg = $this->reRegistrations->find($newReRegId);
        }

        return $response->renderPage([
            'registration' => $registration,
            'selection' => $selection,
            'program_name' => $programName,
            'tuition_fee' => $tuitionFee,
            're_registration' => $reReg,
            'wave_study_program' => $waveStudyProgram,
            'active_payment_account' => $activePaymentAccount
        ], [
            'path' => '/dashboard/re_registration',
            'meta' => ['title' => 'Daftar Ulang | ' . env('APP_NAME')]
        ]);
    }

    public function submitReRegistration(Request $request, Response $response): RedirectResponse
    {
        if (!$this->session->get('is_logged_in')) {
            return $response->redirect('/login');
        }

        $userId = $this->session->get('auth.user_id');
        $registration = $this->registrations->findByUserId($userId);

        if (!$registration || $registration['status'] === 'Draft') {
            return $response->redirect('/dashboard');
        }

        $selection = $this->selectionResults->findByRegistrationId($registration['id']);
        if (!$selection || $selection['status'] !== 'Lulus' || (int)$selection['is_published'] === 0) {
            return $response->redirect('/dashboard');
        }

        $reReg = $this->reRegistrations->findByRegistrationId($registration['id']);
        $data = [
            'registration_id' => $registration['id'],
            'status' => 'Pending',
            'rejection_reason' => null
        ];

        $storageDir = __DIR__ . '/../../storage/app/re_registrations';
        if (!is_dir($storageDir)) {
            mkdir($storageDir, 0755, true);
        }

        // Standard fields
        $fields = ['payment'];
        foreach ($fields as $field) {
            $fileKey = $field . '_file';
            if (isset($_FILES[$fileKey]) && $_FILES[$fileKey]['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES[$fileKey];
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                if (!in_array(strtolower($ext), ['pdf', 'png', 'jpg', 'jpeg'])) {
                    return $response->redirect('/pendaftaran/daftar-ulang?error=Format+file+tidak+diperbolehkan');
                }
                if ($file['size'] > 2 * 1024 * 1024) {
                    return $response->redirect('/pendaftaran/daftar-ulang?error=Ukuran+file+maksimal+2MB');
                }

                $filename = $field . '_' . $registration['id'] . '_' . uniqid() . '.' . $ext;
                $targetPath = $storageDir . '/' . $filename;

                $moved = php_sapi_name() === 'cli' ? copy($file['tmp_name'], $targetPath) : move_uploaded_file($file['tmp_name'], $targetPath);
                if ($moved) {
                    $data[$field . '_path'] = 'storage/app/re_registrations/' . $filename;
                }
            }
        }

        // Load Wave Study Program Config to process dynamic documents
        $db = $this->registrations->getDb();
        $selection = $this->selectionResults->findByRegistrationId($registration['id']);
        $passedProgramId = $selection ? $selection['passed_program_id'] : null;

        $data['dynamic_documents'] = json_encode([]);

        $stmt = $db->prepare("SELECT * FROM wave_study_programs WHERE wave_id = :wave_id AND study_program_id = :prodi_id LIMIT 1");
        $stmt->execute([
            'wave_id' => $registration['wave_id'],
            'prodi_id' => $passedProgramId
        ]);
        $waveStudyProgram = $stmt->fetch() ?: null;
        $tuitionFee = $waveStudyProgram ? (float)$waveStudyProgram['reregistration_fee_total'] : 0.0;

        if (!$reReg) {
            $stmtFind999 = $db->prepare("SELECT id FROM re_registrations WHERE id_payment = 999 LIMIT 1");
            $stmtFind999->execute();
            $has999 = $stmtFind999->fetch();
            $stmtFind999->closeCursor();

            $nextIdPayment = 1;
            if (!$has999) {
                $stmtMax = $db->prepare("SELECT MAX(id_payment) as max_id FROM re_registrations");
                $stmtMax->execute();
                $maxRow = $stmtMax->fetch();
                $stmtMax->closeCursor();

                $maxVal = $maxRow ? (int)$maxRow['max_id'] : 0;
                $nextIdPayment = $maxVal + 1;
                if ($nextIdPayment >= 1000) {
                    $nextIdPayment = 1;
                }
            }
            $idPayment = $nextIdPayment;
        } else {
            $idPayment = (int)($reReg['id_payment'] ?? 1);
        }

        $data['id_payment'] = $idPayment;
        $data['payment_amount'] = $tuitionFee + $idPayment;

        if ($reReg) {
            $this->reRegistrations->updateById($reReg['id'], $data);
        } else {
            $this->reRegistrations->insert($data);
        }

        return $response->redirect('/pendaftaran/daftar-ulang?success=Berkas+dan+bukti+pembayaran+berhasil+disimpan.+Silakan+menunggu+verifikasi.');
    }

    public function listReRegistrations(Request $request, Response $response): View | RedirectResponse
    {
        if (!has_permission('verify_payment') && !has_permission('manage_selection')) {
            return $response->redirect('/dashboard?error=Anda+tidak+memiliki+hak+akses+ke+halaman+ini.');
        }

        $db = $this->reRegistrations->getDb();

        $stmtWaves = $db->prepare("SELECT * FROM waves ORDER BY id ASC");
        $stmtWaves->execute();
        $waves = $stmtWaves->fetchAll() ?: [];
        $stmtWaves->closeCursor();

        $rawWaveId = $request->input('wave_id');
        if ($rawWaveId === null || $rawWaveId === '') {
            $waveIdFilter = !empty($waves) ? (int)$waves[0]['id'] : null;
        } else if ($rawWaveId === 'all') {
            $waveIdFilter = null;
        } else {
            $waveIdFilter = (int)$rawWaveId;
        }

        $page = (int) ($request->input('page') ?: 1);
        if ($page < 1) $page = 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $params = [];
        $whereSql = " WHERE sr.status = 'Lulus' AND sr.is_published = 1 ";
        if ($waveIdFilter !== null) {
            $whereSql .= " AND r.wave_id = :wave_id ";
            $params['wave_id'] = $waveIdFilter;
        }

        $stmtCount = $db->prepare("
            SELECT COUNT(*) as count
            FROM registrations r
            INNER JOIN selection_results sr ON r.id = sr.registration_id
            $whereSql
        ");
        $stmtCount->execute($params);
        $totalCount = (int) ($stmtCount->fetch()['count'] ?? 0);
        $stmtCount->closeCursor();

        $totalPages = (int) ceil($totalCount / $limit);
        if ($totalPages < 1) $totalPages = 1;
        if ($page > $totalPages) {
            $page = $totalPages;
            $offset = ($page - 1) * $limit;
        }

        $stmt = $db->prepare("
            SELECT r.id as registration_id, r.full_name, r.email, sr.passed_program_id, sp.name as program_name, rr.id as re_reg_id, rr.status, rr.payment_amount, rr.created_at, w.name as wave_name
            FROM registrations r
            INNER JOIN selection_results sr ON r.id = sr.registration_id
            LEFT JOIN re_registrations rr ON r.id = rr.registration_id
            LEFT JOIN study_programs sp ON sr.passed_program_id = sp.id
            LEFT JOIN waves w ON r.wave_id = w.id
            $whereSql
            ORDER BY rr.id DESC, r.full_name ASC
            LIMIT " . $limit . " OFFSET " . $offset . "
        ");
        $stmt->execute($params);
        $list = $stmt->fetchAll() ?: [];
        $stmt->closeCursor();

        return $response->renderPage([
            'list' => $list,
            'waves' => $waves,
            'selectedWaveId' => $waveIdFilter,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalCount' => $totalCount,
            'limit' => $limit
        ], [
            'path' => '/admin/re_registrations/index',
            'meta' => ['title' => 'Verifikasi Daftar Ulang | ' . env('APP_NAME')]
        ]);
    }

    public function showDetail(Request $request, Response $response): View | RedirectResponse
    {
        if (!has_permission('verify_payment') && !has_permission('manage_selection')) {
            return $response->redirect('/dashboard?error=Anda+tidak+memiliki+hak+akses+ke+halaman+ini.');
        }

        $regId = (int) $request->input('registration_id');
        if (!$regId) {
            return $response->redirect('/admin/re-registrations?error=Pendaftar+tidak+valid');
        }

        $registration = $this->registrations->find($regId);
        if (!$registration) {
            return $response->redirect('/admin/re-registrations?error=Pendaftar+tidak+ditemukan');
        }

        $selection = $this->selectionResults->findByRegistrationId($regId);
        $reReg = $this->reRegistrations->findByRegistrationId($regId);

        $passedProgramId = $selection['passed_program_id'];
        $db = $this->registrations->getDb();
        $stmt = $db->prepare("SELECT * FROM study_programs WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $passedProgramId]);
        $program = $stmt->fetch();
        $programName = $program ? $program['name'] : '-';

        $stmt = $db->prepare("SELECT * FROM wave_study_programs WHERE wave_id = :wave_id AND study_program_id = :prodi_id LIMIT 1");
        $stmt->execute([
            'wave_id' => $registration['wave_id'],
            'prodi_id' => $passedProgramId
        ]);
        $waveStudyProgram = $stmt->fetch() ?: null;

        $stmt = $db->prepare("SELECT * FROM waves WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $registration['wave_id']]);
        $waveObj = $stmt->fetch();
        $waveName = $waveObj ? $waveObj['name'] : '-';

        $expectedTuition = $waveStudyProgram ? (float)$waveStudyProgram['reregistration_fee_total'] : $this->getTuitionFee($passedProgramId);
        if ($reReg && !empty($reReg['payment_amount'])) {
            $expectedTuition = (float)$reReg['payment_amount'];
        } else if ($reReg && !empty($reReg['id_payment'])) {
            $expectedTuition += (int)$reReg['id_payment'];
        }

        $stmtAddr = $db->prepare("SELECT * FROM registration_addresses WHERE registration_id = :id LIMIT 1");
        $stmtAddr->execute(['id' => $regId]);
        $addr = $stmtAddr->fetch() ?: null;

        $stmtParent = $db->prepare("SELECT * FROM registration_parents WHERE registration_id = :id LIMIT 1");
        $stmtParent->execute(['id' => $regId]);
        $parent = $stmtParent->fetch() ?: null;

        $stmtEdu = $db->prepare("SELECT * FROM registration_educations WHERE registration_id = :id LIMIT 1");
        $stmtEdu->execute(['id' => $regId]);
        $edu = $stmtEdu->fetch() ?: null;

        $profileAddrCompleted = ($addr && !empty($addr['province']) && !empty($addr['city']) && !empty($addr['district']) && !empty($addr['subdistrict']) && !empty($addr['address']));
        $profileParentCompleted = ($parent && !empty($parent['father_name']) && !empty($parent['mother_name']));
        $profileEduCompleted = ($edu && !empty($edu['school_name']) && !empty($edu['school_major']) && !empty($edu['graduation_year']));

        $stmtNimFormat = $db->prepare("SELECT * FROM nim_formats WHERE is_active = 1 LIMIT 1");
        $stmtNimFormat->execute();
        $activeNimFormat = $stmtNimFormat->fetch() ?: null;
        $nimPattern = $activeNimFormat ? $activeNimFormat['format_pattern'] : '{YEAR2}{PRODI_NUM}{GROUP}-{SEQ}';
        $nimFormatName = $activeNimFormat ? $activeNimFormat['name'] : 'Format Standar KMK';

        $y4 = date('Y');
        $y2 = date('y');
        if (!empty($waveObj['academic_year'])) {
            $yStr = substr($waveObj['academic_year'], 0, 4);
            if (strlen($yStr) === 4 && is_numeric($yStr)) {
                $y4 = $yStr;
                $y2 = substr($yStr, 2, 2);
            }
        }

        $nimSetting = $this->nimSettings->getSettings();
        $pCode = $program['code'] ?? '00';
        $pNum = !empty($program['num_code']) ? $program['num_code'] : str_pad((string)($program['id'] ?? 1), 2, '0', STR_PAD_LEFT);

        $yearDigits = (int)($nimSetting['year_digits'] ?? 2);
        $yConfigured = ($yearDigits === 4) ? $y4 : $y2;
        $seqDigits = (int)($nimSetting['seq_digits'] ?? 3);
        $sampleSeq = str_pad('1', $seqDigits, '0', STR_PAD_LEFT);
        $firstGroup = $nimSetting['groups'][0]['key'] ?? '1';

        $dateFormatPattern = $nimSetting['date_format'] ?? 'DDMMYYYY';
        $formattedDate = match ($dateFormatPattern) {
            'YYYYMMDD' => date('Ymd'),
            'DDMMYY' => date('dmy'),
            'YYMMDD' => date('ymd'),
            'DDMM' => date('dm'),
            'MMDD' => date('md'),
            default => date('dmY')
        };

        $sampleNim = str_replace(
            ['{YEAR2}', '{YEAR}', '{PRODI_NUM}', '{PRODI_CODE}', '{GROUP}', '{STUDENT_GROUP}', '{DATE}', '{TIMESTAMP}', '{SEQ}'],
            [$y2, $yConfigured, $pNum, $pCode, $firstGroup, $firstGroup, $formattedDate, '123456', $sampleSeq],
            $nimPattern
        );

        return $response->renderPage([
            'registration' => $registration,
            'selection' => $selection,
            'program_name' => $programName,
            'expected_tuition' => $expectedTuition,
            're_registration' => $reReg,
            'wave_study_program' => $waveStudyProgram,
            'wave_name' => $waveName,
            'profile_addr_completed' => $profileAddrCompleted,
            'profile_parent_completed' => $profileParentCompleted,
            'profile_edu_completed' => $profileEduCompleted,
            'active_nim_format' => $activeNimFormat,
            'active_nim_pattern' => $nimPattern,
            'active_nim_name' => $nimFormatName,
            'sample_nim' => $sampleNim,
            'nim_groups' => $nimSetting['groups'] ?? []
        ], [
            'path' => '/admin/re_registrations/detail',
            'meta' => ['title' => 'Detail Verifikasi Daftar Ulang | ' . env('APP_NAME')]
        ]);
    }

    public function verifyReRegistration(Request $request, Response $response): RedirectResponse
    {
        if (!has_permission('verify_payment') && !has_permission('manage_selection')) {
            return $response->redirect('/dashboard?error=Anda+tidak+memiliki+hak+akses+ke+halaman+ini.');
        }

        $reRegId = (int) $request->input('id');
        $status = $request->input('status');
        $reason = $request->input('rejection_reason');
        $nim = trim((string)$request->input('nim'));

        if (!$reRegId || !in_array($status, ['Approved', 'Rejected'])) {
            return $response->redirect('/admin/re-registrations?error=Data+verifikasi+tidak+valid');
        }

        $reReg = $this->reRegistrations->find($reRegId);
        if (!$reReg) {
            return $response->redirect('/admin/re-registrations?error=Data+verifikasi+tidak+ditemukan');
        }

        $registration = $this->registrations->find($reReg['registration_id']);
        if (!$registration) {
            return $response->redirect('/admin/re-registrations?error=Data+pendaftar+tidak+ditemukan');
        }

        $db = $this->registrations->getDb();

        if ($status === 'Approved') {
            $stmtAddr = $db->prepare("SELECT * FROM registration_addresses WHERE registration_id = :id LIMIT 1");
            $stmtAddr->execute(['id' => $registration['id']]);
            $addr = $stmtAddr->fetch() ?: null;

            $stmtParent = $db->prepare("SELECT * FROM registration_parents WHERE registration_id = :id LIMIT 1");
            $stmtParent->execute(['id' => $registration['id']]);
            $parent = $stmtParent->fetch() ?: null;

            $stmtEdu = $db->prepare("SELECT * FROM registration_educations WHERE registration_id = :id LIMIT 1");
            $stmtEdu->execute(['id' => $registration['id']]);
            $edu = $stmtEdu->fetch() ?: null;

            $profileAddrCompleted = ($addr && !empty($addr['province']) && !empty($addr['city']) && !empty($addr['district']) && !empty($addr['subdistrict']) && !empty($addr['address']));
            $profileParentCompleted = ($parent && !empty($parent['father_name']) && !empty($parent['mother_name']));
            $profileEduCompleted = ($edu && !empty($edu['school_name']) && !empty($edu['school_major']) && !empty($edu['graduation_year']));

            if (!$profileAddrCompleted || !$profileParentCompleted || !$profileEduCompleted) {
                return $response->redirect('/admin/re-registrations/detail?registration_id=' . $registration['id'] . '&error=Gagal+menyetujui.+Profil+pendaftar+belum+lengkap');
            }
        }

        if (!empty($nim)) {
            $stmt = $db->prepare("SELECT id FROM registrations WHERE nim = :nim AND id != :id LIMIT 1");
            $stmt->execute(['nim' => $nim, 'id' => $registration['id']]);
            if ($stmt->fetch()) {
                return $response->redirect('/admin/re-registrations/detail?registration_id=' . $registration['id'] . '&error=NIM+sudah+terdaftar+untuk+pendaftar+lain');
            }
        }

        $adminId = $this->session->get('auth.user_id');

        $this->reRegistrations->updateById($reRegId, [
            'status' => $status,
            'rejection_reason' => $status === 'Rejected' ? $reason : null,
            'verified_by' => $adminId,
            'verified_at' => date('Y-m-d H:i:s')
        ]);

        $this->registrations->updateById($registration['id'], [
            'nim' => !empty($nim) ? $nim : null
        ]);

        $userId = $registration['user_id'];
        if ($status === 'Approved') {
            send_system_notification($userId, 'Daftar Ulang Disetujui', 'Selamat! Berkas dan pembayaran daftar ulang Anda telah disetujui. Anda resmi terdaftar sebagai mahasiswa baru.', 'success');
            send_email_notification($userId, $registration['email'], 'Verifikasi Daftar Ulang Disetujui', 'Selamat! Berkas dan pembayaran daftar ulang Anda telah disetujui oleh panitia PMB Kampus Mandiri Kencana. Anda resmi terdaftar sebagai mahasiswa baru.');
        } else {
            send_system_notification($userId, 'Daftar Ulang Perlu Revisi', 'Verifikasi berkas daftar ulang Anda ditolak/perlu direvisi. Alasan: ' . ($reason ?? '-'), 'warning');
            send_email_notification($userId, $registration['email'], 'Daftar Ulang Perlu Revisi', 'Verifikasi berkas daftar ulang Anda ditolak/perlu direvisi oleh panitia PMB Kampus Mandiri Kencana. Alasan: ' . ($reason ?? '-'));
        }

        return $response->redirect('/admin/re-registrations?success=Verifikasi+daftar+ulang+berhasil+disimpan.');
    }

    public function apiGenerateNim(Request $request, Response $response)
    {
        if (!has_permission('verify_payment') && !has_permission('manage_selection')) {
            return $response->json(['error' => 'Unauthorized'], 403);
        }

        $regId = (int) $request->input('registration_id');
        if (!$regId) {
            return $response->json(['error' => 'Registration ID is required'], 400);
        }

        $registration = $this->registrations->find($regId);
        if (!$registration) {
            return $response->json(['error' => 'Registration not found'], 404);
        }

        $selection = $this->selectionResults->findByRegistrationId($regId);
        if (!$selection) {
            return $response->json(['error' => 'Selection result not found'], 404);
        }

        $studentGroup = (string) $request->input('group', '3');
        $db = $this->registrations->getDb();
        $nim = $this->generateNim($registration, $selection, $db, $studentGroup);

        return $response->json(['nim' => $nim]);
    }

    public function viewFile(Request $request, Response $response)
    {
        if (!$this->session->get('is_logged_in')) {
            return $response->redirect('/login');
        }

        $reRegId = (int) $request->input('id');
        $fileKey = $request->input('file');

        $isDynamic = str_starts_with($fileKey, 'dynamic_doc_');
        if (!$reRegId || (!in_array($fileKey, ['skl', 'health', 'statement', 'payment']) && !$isDynamic)) {
            $response->setStatusCode(400);
            echo "Parameter tidak valid.";
            exit;
        }

        $reReg = $this->reRegistrations->find($reRegId);
        if (!$reReg) {
            $response->setStatusCode(404);
            echo "Data tidak ditemukan.";
            exit;
        }

        $userRole = $this->session->get('auth.user_role');
        $userId = $this->session->get('auth.user_id');
        $registration = $this->registrations->find($reReg['registration_id']);

        if ($userRole !== 'admin' && (!$registration || $registration['user_id'] !== $userId)) {
            $response->setStatusCode(403);
            echo "Akses ditolak.";
            exit;
        }

        if ($isDynamic) {
            $index = (int) str_replace('dynamic_doc_', '', $fileKey);
            $oldDocs = json_decode($reReg['dynamic_documents'] ?? '[]', true) ?: [];
            $relativeFilePath = $oldDocs[$index]['path'] ?? null;
        } else {
            $pathField = $fileKey . '_path';
            $relativeFilePath = $reReg[$pathField];
        }

        if (empty($relativeFilePath)) {
            $response->setStatusCode(404);
            echo "Berkas belum diunggah.";
            exit;
        }

        $absoluteFilePath = __DIR__ . '/../../' . $relativeFilePath;
        if (!file_exists($absoluteFilePath)) {
            $response->setStatusCode(404);
            echo "Berkas fisik tidak ditemukan.";
            exit;
        }

        $ext = strtolower(pathinfo($absoluteFilePath, PATHINFO_EXTENSION));
        $mime = match ($ext) {
            'pdf' => 'application/pdf',
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            default => 'application/octet-stream'
        };

        header('Content-Type: ' . $mime);
        header('Content-Disposition: inline; filename="' . basename($absoluteFilePath) . '"');
        readfile($absoluteFilePath);
        exit;
    }

    private function generateNim(array $registration, array $selection, $db, string $studentGroup = '1'): string
    {
        $nimSetting = $this->nimSettings->getSettings();
        $stmt = $db->prepare("SELECT * FROM nim_formats WHERE is_active = 1 LIMIT 1");
        $stmt->execute();
        $nimFormat = $stmt->fetch();
        $pattern = $nimFormat ? $nimFormat['format_pattern'] : '{YEAR}{PRODI_NUM}{GROUP}{SEQ}';

        $year4 = date('Y');
        $year2 = date('y');
        if (!empty($registration['wave_id'])) {
            $stmt = $db->prepare("SELECT academic_year FROM waves WHERE id = :id LIMIT 1");
            $stmt->execute(['id' => $registration['wave_id']]);
            $wave = $stmt->fetch();
            if ($wave && !empty($wave['academic_year'])) {
                $yStr = substr($wave['academic_year'], 0, 4);
                if (strlen($yStr) === 4 && is_numeric($yStr)) {
                    $year4 = $yStr;
                    $year2 = substr($yStr, 2, 2);
                }
            }
        }

        $yearDigits = (int)($nimSetting['year_digits'] ?? 2);
        $yearValue = ($yearDigits === 4) ? $year4 : $year2;

        $prodiCode = '00';
        $prodiNum = '01';
        $passedProgramId = $selection['passed_program_id'] ?? null;
        if ($passedProgramId) {
            $stmt = $db->prepare("SELECT id, code, num_code, name FROM study_programs WHERE id = :id LIMIT 1");
            $stmt->execute(['id' => $passedProgramId]);
            $sp = $stmt->fetch();
            if ($sp) {
                $prodiCode = $sp['code'];
                $prodiNum = !empty($sp['num_code']) ? $sp['num_code'] : str_pad((string)$sp['id'], 2, '0', STR_PAD_LEFT);
            }
        }

        $dateFormatPattern = $nimSetting['date_format'] ?? 'DDMMYYYY';
        $formattedDate = match ($dateFormatPattern) {
            'YYYYMMDD' => date('Ymd'),
            'DDMMYY' => date('dmy'),
            'YYMMDD' => date('ymd'),
            'DDMM' => date('dm'),
            'MMDD' => date('md'),
            default => date('dmY')
        };

        $seqDigits = (int)($nimSetting['seq_digits'] ?? 3);

        $prefixTemplate = explode('{SEQ}', $pattern)[0] ?? '';
        $prefix = str_replace(
            ['{YEAR2}', '{YEAR}', '{PRODI_NUM}', '{PRODI_CODE}', '{GROUP}', '{STUDENT_GROUP}', '{DATE}', '{TIMESTAMP}'],
            [$year2, $yearValue, $prodiNum, $prodiCode, $studentGroup, $studentGroup, $formattedDate, ''],
            $prefixTemplate
        );

        $maxSeq = 0;
        if (!empty($prefix)) {
            $stmt = $db->prepare("SELECT nim FROM registrations WHERE nim LIKE :prefix AND nim IS NOT NULL");
            $stmt->execute(['prefix' => $prefix . '%']);
            $existingNims = $stmt->fetchAll(\PDO::FETCH_COLUMN);

            foreach ($existingNims as $exNim) {
                if (preg_match('/(\d+)$/', $exNim, $matches)) {
                    $num = (int)$matches[1];
                    if ($num > $maxSeq) {
                        $maxSeq = $num;
                    }
                }
            }
        }

        $nextSeq = $maxSeq + 1;
        $regId = (int)($registration['id'] ?? 0);

        do {
            $seqStr = str_pad((string) $nextSeq, $seqDigits, '0', STR_PAD_LEFT);
            $candidateNim = str_replace(
                ['{YEAR2}', '{YEAR}', '{PRODI_NUM}', '{PRODI_CODE}', '{GROUP}', '{STUDENT_GROUP}', '{DATE}', '{TIMESTAMP}', '{SEQ}'],
                [$year2, $yearValue, $prodiNum, $prodiCode, $studentGroup, $studentGroup, $formattedDate, substr((string)time(), -6), $seqStr],
                $pattern
            );

            $checkStmt = $db->prepare("SELECT COUNT(*) FROM registrations WHERE nim = :nim AND id != :id");
            $checkStmt->execute(['nim' => $candidateNim, 'id' => $regId]);
            $exists = (int)$checkStmt->fetchColumn() > 0;
            if (!$exists) {
                return $candidateNim;
            }
            $nextSeq++;
        } while (true);
    }

    public function changePaymentType(Request $request, Response $response): Response
    {
        $userId = $_SESSION['auth.user_id'] ?? null;
        if (!$userId) {
            $response->setStatusCode(401);
            return $response->json(['success' => false, 'message' => 'Unauthorized']);
        }

        $registration = $this->registrations->findByUserId($userId);
        if (!$registration) {
            $response->setStatusCode(404);
            return $response->json(['success' => false, 'message' => 'Registration not found']);
        }

        $type = $request->input('payment_type');
        if (!in_array($type, ['manual', 'va'], true)) {
            $response->setStatusCode(400);
            return $response->json(['success' => false, 'message' => 'Invalid payment type']);
        }

        $reReg = $this->reRegistrations->findByRegistrationId($registration['id']);
        if ($reReg) {
            $this->reRegistrations->updateById($reReg['id'], ['payment_type' => $type]);
        }

        return $response->json(['success' => true]);
    }
}
