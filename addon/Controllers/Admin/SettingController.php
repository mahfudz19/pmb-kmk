<?php

namespace Addon\Controllers\Admin;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\View\View;
use App\Core\Http\RedirectResponse;
use App\Services\SessionService;
use Addon\Models\SettingModel;
use Addon\Models\AcademicYearModel;
use Addon\Models\WaveModel;

class SettingController
{
    public function __construct(
        private SessionService $session,
        private SettingModel $settings,
        private AcademicYearModel $academicYears,
        private WaveModel $waves
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

        $years = $this->academicYears->all();
        $wavesList = $this->waves->all();

        return $response->renderPage([
            'settings' => $mappedSettings,
            'academic_years' => $years,
            'waves' => $wavesList
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
            'smtp_from_address', 'smtp_from_name'
        ];

        $db = $this->settings->getDb();
        foreach ($keys as $key) {
            $val = $request->input($key);
            if ($val !== null) {
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

    public function updateAcademicYear(Request $request, Response $response): RedirectResponse
    {
        if ($redirect = $this->checkAccess($response)) {
            return $redirect;
        }

        $activeId = (int)$request->input('active_year_id');
        if ($activeId) {
            $db = $this->academicYears->getDb();
            $stmt1 = $db->prepare("UPDATE academic_years SET is_active = 0");
            $stmt1->execute();
            $stmt2 = $db->prepare("UPDATE academic_years SET is_active = 1 WHERE id = :id");
            $stmt2->execute(['id' => $activeId]);
        }

        log_activity('UPDATE_ACADEMIC_YEAR', "Mengubah tahun akademik aktif ke ID {$activeId}.");
        return $response->redirect('/admin/settings?success=Tahun+akademik+aktif+berhasil+diperbarui.');
    }

    public function updateWave(Request $request, Response $response): RedirectResponse
    {
        if ($redirect = $this->checkAccess($response)) {
            return $redirect;
        }

        $activeId = (int)$request->input('active_wave_id');
        if ($activeId) {
            $db = $this->waves->getDb();
            $stmt1 = $db->prepare("UPDATE waves SET is_active = 0");
            $stmt1->execute();
            $stmt2 = $db->prepare("UPDATE waves SET is_active = 1 WHERE id = :id");
            $stmt2->execute(['id' => $activeId]);
        }

        log_activity('UPDATE_ACTIVE_WAVE', "Mengubah gelombang aktif ke ID {$activeId}.");
        return $response->redirect('/admin/settings?success=Gelombang+aktif+berhasil+diperbarui.');
    }
}
