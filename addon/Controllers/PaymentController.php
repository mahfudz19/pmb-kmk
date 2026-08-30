<?php

namespace Addon\Controllers;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\View\View;
use App\Core\Http\RedirectResponse;
use Addon\Models\RegistrationModel;
use Addon\Models\RegistrationPaymentModel;

class PaymentController
{
    public function __construct(
        private RegistrationModel $registrations,
        private RegistrationPaymentModel $payments,
        private \Addon\Models\WaveModel $waves
    ) {}

    public function uploadPayment(Request $request, Response $response): RedirectResponse
    {
        $userId = $_SESSION['auth.user_id'] ?? null;
        if (!$userId) {
            return $response->redirect('/login');
        }

        $registration = $this->registrations->findByUserId($userId);
        if (!$registration || $registration['status'] === 'Draft') {
            return $response->redirect('/dashboard?error=Silakan+lengkapi+dan+kunci+formulir+pendaftaran+terlebih+dahulu.');
        }

        $bankName = $request->input('bank_name');
        $accountName = $request->input('account_name');
        $amount = (float) str_replace(['.', ','], '', $request->input('amount') ?? '');
        $paymentDate = $request->input('payment_date');

        if (!$bankName || !$accountName || !$amount || !$paymentDate) {
            return $response->redirect('/dashboard?error=Harap+isi+semua+kolom+informasi+transfer+pembayaran.');
        }

        if (empty($_FILES['proof']) || $_FILES['proof']['error'] !== UPLOAD_ERR_OK) {
            return $response->redirect('/dashboard?error=Gagal+mengunggah+file+bukti+transfer.+Silakan+coba+lagi.');
        }

        $file = $_FILES['proof'];

        // Size Limit 2MB
        if ($file['size'] > 2 * 1024 * 1024) {
            return $response->redirect('/dashboard?error=Ukuran+file+bukti+transfer+maksimal+adalah+2MB.');
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['pdf', 'jpg', 'jpeg', 'png'];
        if (!in_array($ext, $allowed, true)) {
            return $response->redirect('/dashboard?error=Format+file+bukti+transfer+harus+berupa+PDF,+JPG,+JPEG,+atau+PNG.');
        }

        // Save file to private storage
        $storageDir = __DIR__ . '/../../storage/app/payments/';
        if (!is_dir($storageDir)) {
            mkdir($storageDir, 0755, true);
        }

        $filename = 'reg_' . $registration['id'] . '_payment.' . $ext;
        $destPath = $storageDir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            return $response->redirect('/dashboard?error=Gagal+menyimpan+bukti+transfer+di+server.');
        }

        $existingPayment = $this->payments->findByRegistrationId($registration['id']);

        $payData = [
            'registration_id' => $registration['id'],
            'bank_name' => $bankName,
            'account_name' => $accountName,
            'amount' => $amount,
            'payment_date' => $paymentDate,
            'file_path' => $filename,
            'status' => 'Pending',
            'rejection_reason' => null,
            'verified_at' => null,
            'verified_by' => null
        ];

        if ($existingPayment) {
            $this->payments->updateById($existingPayment['id'], $payData);
        } else {
            $this->payments->insert($payData);
        }

        return $response->redirect('/dashboard?success=Bukti+pembayaran+berhasil+diunggah.+Mohon+tunggu+verifikasi+oleh+panitia.');
    }

    public function viewFile(Request $request, Response $response): void
    {
        $userId = $_SESSION['auth.user_id'] ?? null;
        if (!$userId) {
            $response->setStatusCode(401);
            echo "Unauthorized";
            exit;
        }

        $paymentId = (int) $request->input('id');
        if (!$paymentId) {
            $response->setStatusCode(400);
            echo "ID Transaksi tidak valid";
            exit;
        }

        $payment = $this->payments->find($paymentId);
        if (!$payment) {
            $response->setStatusCode(404);
            echo "Transaksi tidak ditemukan";
            exit;
        }

        $userRole = $_SESSION['auth.user_role'] ?? 'user';
        if ($userRole !== 'admin') {
            $registration = $this->registrations->findByUserId($userId);
            if (!$registration || $registration['id'] !== $payment['registration_id']) {
                $response->setStatusCode(403);
                echo "Access Denied";
                exit;
            }
        }

        $storageDir = __DIR__ . '/../../storage/app/payments/';
        $filePath = $storageDir . $payment['file_path'];

        if (!file_exists($filePath)) {
            $response->setStatusCode(404);
            echo "File fisik bukti transfer tidak ditemukan di server";
            exit;
        }

        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $contentTypes = [
            'pdf' => 'application/pdf',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg'
        ];

        $contentType = $contentTypes[$ext] ?? 'application/octet-stream';

        header("Content-Type: " . $contentType);
        header("Content-Length: " . filesize($filePath));
        header("Cache-Control: private, max-age=86400");
        readfile($filePath);
        exit;
    }

    public function listPayments(Request $request, Response $response): View|RedirectResponse
    {
        if (!has_permission('verify_payment')) {
            return $response->redirect('/dashboard?error=Anda+tidak+memiliki+hak+akses+ke+halaman+ini.');
        }

        $waveId = $request->input('wave_id');
        $waveIdFilter = ($waveId !== '' && $waveId !== null) ? (int)$waveId : null;

        $page = (int) ($request->input('page') ?: 1);
        if ($page < 1) $page = 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $totalCount = $this->payments->getPaymentsCount($waveIdFilter);
        $totalPages = (int) ceil($totalCount / $limit);
        if ($totalPages < 1) $totalPages = 1;
        if ($page > $totalPages) {
            $page = $totalPages;
            $offset = ($page - 1) * $limit;
        }

        $paymentsList = $this->payments->getPaginatedPayments($limit, $offset, $waveIdFilter);

        return $response->renderPage([
            'payments' => $paymentsList,
            'waves' => $this->waves->all(),
            'selectedWaveId' => $waveIdFilter,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalCount' => $totalCount,
            'limit' => $limit
        ], [
            'path' => '/admin/payments',
            'meta' => ['title' => 'Verifikasi Pembayaran PMB | ' . env('APP_NAME')]
        ]);
    }

    public function verifyPayment(Request $request, Response $response): RedirectResponse
    {
        if (!has_permission('verify_payment')) {
            return $response->redirect('/dashboard?error=Anda+tidak+memiliki+hak+akses+untuk+melakukan+verifikasi.');
        }

        $paymentId = (int) $request->input('payment_id');
        $status = $request->input('status');
        $reason = $request->input('rejection_reason') ?: null;

        if (!$paymentId || !in_array($status, ['Approved', 'Rejected'], true)) {
            return $response->redirect('/admin/payments?error=Masukan+verifikasi+tidak+valid');
        }

        $payment = $this->payments->find($paymentId);
        if (!$payment) {
            return $response->redirect('/admin/payments?error=Data+pembayaran+tidak+ditemukan');
        }

        $this->payments->updateById($paymentId, [
            'status' => $status,
            'rejection_reason' => $reason,
            'verified_at' => date('Y-m-d H:i:s'),
            'verified_by' => $_SESSION['auth.user_id']
        ]);

        $registration = $this->registrations->find($payment['registration_id']);
        if ($registration) {
            $userId = $registration['user_id'];
            if ($status === 'Approved') {
                send_system_notification($userId, 'Pembayaran Formulir Disetujui', 'Pembayaran biaya formulir Anda sebesar Rp 250.000 telah disetujui. Silakan unggah berkas dokumen persyaratan akademik.', 'success');
                send_email_notification($userId, $registration['email'], 'Pembayaran Formulir Disetujui', 'Pembayaran biaya formulir Anda sebesar Rp 250.000 telah disetujui. Silakan unggah berkas dokumen persyaratan akademik.');
            } else {
                send_system_notification($userId, 'Pembayaran Formulir Ditolak', 'Pembayaran biaya formulir Anda ditolak. Alasan: ' . ($reason ?? '-'), 'danger');
                send_email_notification($userId, $registration['email'], 'Pembayaran Formulir Ditolak', 'Pembayaran biaya formulir Anda ditolak. Alasan: ' . ($reason ?? '-'));
            }
        }

        log_activity('VERIFY_PAYMENT', "Verifikasi pembayaran ID {$paymentId} diubah menjadi {$status}.");
        return $response->redirect('/admin/payments?success=Verifikasi+pembayaran+berhasil+disimpan.');
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

        $payment = $this->payments->findByRegistrationId($registration['id']);
        if ($payment) {
            $this->payments->updateById($payment['id'], ['payment_type' => $type]);
        }

        return $response->json(['success' => true]);
    }
}
