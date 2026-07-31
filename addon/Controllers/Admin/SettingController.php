<?php

namespace Addon\Controllers\Admin;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\View\View;
use App\Core\Http\RedirectResponse;
use App\Services\SessionService;
use Addon\Models\SettingModel;

class SettingController
{
    public function __construct(
        private SessionService $session,
        private SettingModel $settings
    ) {}

    private function checkAccess(Response $response): ?RedirectResponse
    {
        if (!$this->session->get('is_logged_in')) {
            return $response->redirect('/login');
        }
        if ($this->session->get('auth.user_role') !== 'admin') {
            return $response->redirect('/dashboard?error=Anda+tidak+memiliki+hak+akses+ke+halaman+ini.');
        }
        return null;
    }

    public function index(Request $request, Response $response): View|RedirectResponse
    {
        if ($redirect = $this->checkAccess($response)) {
            return $redirect;
        }

        $allSettings = $this->settings->all();
        $mappedSettings = [];
        foreach ($allSettings as $s) {
            $mappedSettings[$s['key']] = $s['value'];
        }

        return $response->renderPage([
            'settings' => $mappedSettings
        ], [
            'path' => '/admin/settings/index',
            'meta' => ['title' => 'Pengaturan Sistem | ' . env('APP_NAME')]
        ]);
    }

    public function updateGeneral(Request $request, Response $response): RedirectResponse
    {
        if ($redirect = $this->checkAccess($response)) {
            return $redirect;
        }

        $keys = [
            'campus_name', 'campus_address', 'campus_email', 'campus_phone',
            'registration_number_format', 'smtp_host', 'smtp_port',
            'smtp_username', 'smtp_password', 'smtp_encryption',
            'smtp_from_address', 'smtp_from_name',
            'pmb_chairman_name', 'pmb_chairman_nip'
        ];

        $db = $this->settings->getDb();
        foreach ($keys as $key) {
            $val = $request->input($key);
            if ($val !== null) {
                $check = $db->prepare("SELECT COUNT(*) as count FROM settings WHERE `key` = :key");
                $check->execute(['key' => $key]);
                if ((int)($check->fetch()['count'] ?? 0) === 0) {
                    $insert = $db->prepare("INSERT INTO settings (`key`, `value`) VALUES (:key, '')");
                    $insert->execute(['key' => $key]);
                }
                $stmt = $db->prepare("UPDATE settings SET value = :val WHERE `key` = :key");
                $stmt->execute(['val' => $val, 'key' => $key]);
            }
        }

        if (isset($_FILES['campus_logo_file']) && $_FILES['campus_logo_file']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['campus_logo_file'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['png', 'jpg', 'jpeg', 'svg', 'webp'], true)) {
                $fileName = 'logo_' . time() . '.' . $ext;
                $uploadDir = __DIR__ . '/../../../public/uploads/logo/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                if (move_uploaded_file($file['tmp_name'], $uploadDir . $fileName)) {
                    $val = '/uploads/logo/' . $fileName;
                    $stmt = $db->prepare("UPDATE settings SET value = :val WHERE `key` = 'campus_logo'");
                    $stmt->execute(['val' => $val]);
                }
            }
        }

        log_activity('UPDATE_SYSTEM_SETTINGS', "Memperbarui konfigurasi pengaturan umum sistem.");
        return $response->redirect('/admin/settings?success=Pengaturan+umum+berhasil+disimpan.');
    }
}
