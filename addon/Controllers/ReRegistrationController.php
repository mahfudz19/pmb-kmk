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

class ReRegistrationController
{
    public function __construct(
        private SessionService $session,
        private ReRegistrationModel $reRegistrations,
        private RegistrationModel $registrations,
        private SelectionResultModel $selectionResults
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

        $tuitionFee = $this->getTuitionFee($passedProgramId);

        $reReg = $this->reRegistrations->findByRegistrationId($registration['id']);

        return $response->renderPage([
            'registration' => $registration,
            'selection' => $selection,
            'program_name' => $programName,
            'tuition_fee' => $tuitionFee,
            're_registration' => $reReg
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

        $fields = ['skl', 'health', 'statement', 'payment'];
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
                
                if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                    $data[$field . '_path'] = 'storage/app/re_registrations/' . $filename;
                }
            }
        }

        $paymentAmount = $request->input('payment_amount');
        if ($paymentAmount !== '') {
            $data['payment_amount'] = (float) str_replace(['.', ','], '', $paymentAmount);
        }

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

        $page = (int) ($request->input('page') ?: 1);
        if ($page < 1) $page = 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $db = $this->reRegistrations->getDb();

        $stmtCount = $db->prepare("
            SELECT COUNT(*) as count
            FROM registrations r
            INNER JOIN selection_results sr ON r.id = sr.registration_id
            WHERE sr.status = 'Lulus' AND sr.is_published = 1
        ");
        $stmtCount->execute();
        $totalCount = (int) ($stmtCount->fetch()['count'] ?? 0);

        $totalPages = (int) ceil($totalCount / $limit);
        if ($totalPages < 1) $totalPages = 1;
        if ($page > $totalPages) {
            $page = $totalPages;
            $offset = ($page - 1) * $limit;
        }

        $stmt = $db->prepare("
            SELECT r.id as registration_id, r.full_name, r.email, sr.passed_program_id, sp.name as program_name, rr.id as re_reg_id, rr.status, rr.payment_amount, rr.created_at
            FROM registrations r
            INNER JOIN selection_results sr ON r.id = sr.registration_id
            LEFT JOIN re_registrations rr ON r.id = rr.registration_id
            LEFT JOIN study_programs sp ON sr.passed_program_id = sp.id
            WHERE sr.status = 'Lulus' AND sr.is_published = 1
            ORDER BY rr.id DESC, r.full_name ASC
            LIMIT " . $limit . " OFFSET " . $offset . "
        ");
        $stmt->execute();
        $list = $stmt->fetchAll();

        return $response->renderPage([
            'list' => $list,
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

        $expectedTuition = $this->getTuitionFee($passedProgramId);

        return $response->renderPage([
            'registration' => $registration,
            'selection' => $selection,
            'program_name' => $programName,
            'expected_tuition' => $expectedTuition,
            're_registration' => $reReg
        ], [
            'path' => '/admin/re_registrations/detail',
            'meta' => ['title' => 'Detail Daftar Ulang | ' . env('APP_NAME')]
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

        if (!$reRegId || !in_array($status, ['Approved', 'Rejected'])) {
            return $response->redirect('/admin/re-registrations?error=Data+verifikasi+tidak+valid');
        }

        $reReg = $this->reRegistrations->find($reRegId);
        if (!$reReg) {
            return $response->redirect('/admin/re-registrations?error=Data+verifikasi+tidak+ditemukan');
        }

        $adminId = $this->session->get('auth.user_id');

        $this->reRegistrations->updateById($reRegId, [
            'status' => $status,
            'rejection_reason' => $status === 'Rejected' ? $reason : null,
            'verified_by' => $adminId,
            'verified_at' => date('Y-m-d H:i:s')
        ]);

        $registration = $this->registrations->find($reReg['registration_id']);
        if ($registration) {
            $userId = $registration['user_id'];
            if ($status === 'Approved') {
                send_system_notification($userId, 'Daftar Ulang Disetujui', 'Selamat! Berkas dan pembayaran daftar ulang Anda telah disetujui. Anda resmi terdaftar sebagai mahasiswa baru.', 'success');
                send_email_notification($userId, $registration['email'], 'Verifikasi Daftar Ulang Disetujui', 'Selamat! Berkas dan pembayaran daftar ulang Anda telah disetujui oleh panitia PMB Kampus Mandiri Kencana. Anda resmi terdaftar sebagai mahasiswa baru.');
            } else {
                send_system_notification($userId, 'Daftar Ulang Perlu Revisi', 'Verifikasi berkas daftar ulang Anda ditolak/perlu direvisi. Alasan: ' . ($reason ?? '-'), 'warning');
                send_email_notification($userId, $registration['email'], 'Daftar Ulang Perlu Revisi', 'Verifikasi berkas daftar ulang Anda ditolak/perlu direvisi oleh panitia PMB Kampus Mandiri Kencana. Alasan: ' . ($reason ?? '-'));
            }
        }

        return $response->redirect('/admin/re-registrations?success=Verifikasi+daftar+ulang+berhasil+disimpan.');
    }

    public function viewFile(Request $request, Response $response)
    {
        if (!$this->session->get('is_logged_in')) {
            return $response->redirect('/login');
        }

        $reRegId = (int) $request->input('id');
        $fileKey = $request->input('file'); // 'skl', 'health', 'statement', 'payment'

        if (!$reRegId || !in_array($fileKey, ['skl', 'health', 'statement', 'payment'])) {
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

        $pathField = $fileKey . '_path';
        $relativeFilePath = $reReg[$pathField];

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
}
