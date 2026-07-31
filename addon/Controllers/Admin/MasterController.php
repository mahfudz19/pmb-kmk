<?php

namespace Addon\Controllers\Admin;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\View\View;
use App\Core\Http\RedirectResponse;
use Addon\Models\WaveModel;
use Addon\Models\FacultyModel;
use Addon\Models\StudyProgramModel;
use Addon\Models\DocumentTypeModel;
use Addon\Models\PaymentAccountModel;
use Addon\Models\NimFormatModel;
use Addon\Models\WaveStudyProgramModel;

class MasterController
{
    public function __construct(
        private WaveModel $waves,
        private FacultyModel $faculties,
        private StudyProgramModel $studyPrograms,
        private DocumentTypeModel $documentTypes,
        private PaymentAccountModel $paymentAccounts,
        private NimFormatModel $nimFormats,
        private WaveStudyProgramModel $waveStudyPrograms
    ) {}

    public function index(Request $request, Response $response): View
    {
        $tab = $request->input('tab') ?? 'wave';

        $data = [
            'tab' => $tab,
            'waves' => $this->waves->all(),
            'faculties' => $this->faculties->all(),
            'study_programs' => $this->studyPrograms->all(),
            'document_types' => $this->documentTypes->all(),
            'payment_accounts' => $this->paymentAccounts->all(),
            'nim_formats' => $this->nimFormats->all(),
        ];

        return $response->renderPage($data, [
            'path' => '/admin/master',
            'meta' => ['title' => 'Master Data Control Center | ' . env('APP_NAME')]
        ]);
    }

    public function create(Request $request, Response $response): RedirectResponse
    {
        $type = $request->input('type');

        switch ($type) {
            case 'wave':
                $name = $request->input('name');
                $academic_year = $request->input('academic_year') ?: null;
                $description = $request->input('description') ?: null;
                $start = $request->input('start_date');
                $end = $request->input('end_date');
                $is_active = $request->input('is_active') ? 1 : 0;
                if (!$name || !$start || !$end) return $response->redirect('/admin/master?tab=wave&error=Seluruh+kolom+harus+diisi');
                if ($is_active) {
                    $this->waves->getDb()->query("UPDATE waves SET is_active = 0");
                }
                $this->waves->insert([
                    'name' => $name, 
                    'academic_year' => $academic_year,
                    'description' => $description, 
                    'start_date' => $start, 
                    'end_date' => $end, 
                    'is_active' => $is_active
                ]);
                break;

            case 'payment-account':
                $bank_name = $request->input('bank_name');
                $account_number = $request->input('account_number');
                $account_holder = $request->input('account_holder');
                $is_active = $request->input('is_active') ? 1 : 0;
                if (!$bank_name || !$account_number || !$account_holder) return $response->redirect('/admin/master?tab=payment-account&error=Seluruh+kolom+harus+diisi');
                $this->paymentAccounts->insert([
                    'bank_name' => $bank_name,
                    'account_number' => $account_number,
                    'account_holder' => $account_holder,
                    'is_active' => $is_active
                ]);
                break;

            case 'nim-format':
                $name = $request->input('name');
                $format_pattern = $request->input('format_pattern');
                $is_active = $request->input('is_active') ? 1 : 0;
                if (!$name || !$format_pattern) return $response->redirect('/admin/master?tab=nim-format&error=Seluruh+kolom+harus+diisi');
                if ($is_active) {
                    $this->nimFormats->getDb()->query("UPDATE nim_formats SET is_active = 0");
                }
                $this->nimFormats->insert([
                    'name' => $name,
                    'format_pattern' => $format_pattern,
                    'is_active' => $is_active
                ]);
                break;

            case 'faculty':
                $code = $request->input('code');
                $name = $request->input('name');
                if (!$code || !$name) return $response->redirect('/admin/master?tab=faculty&error=Kode+dan+nama+fakultas+harus+diisi');
                $this->faculties->insert(['code' => strtoupper($code), 'name' => $name]);
                break;

            case 'study-program':
                $faculty_id = $request->input('faculty_id');
                $code = $request->input('code');
                $name = $request->input('name');
                if (!$faculty_id || !$code || !$name) return $response->redirect('/admin/master?tab=study-program&error=Seluruh+kolom+harus+diisi');
                $this->studyPrograms->insert(['faculty_id' => $faculty_id, 'code' => strtoupper($code), 'name' => $name]);
                break;

            case 'document-type':
                $name = $request->input('name');
                $is_required = $request->input('is_required') ? 1 : 0;
                if (!$name) return $response->redirect('/admin/master?tab=document-type&error=Nama+dokumen+harus+diisi');
                $this->documentTypes->insert([
                    'name' => $name, 
                    'is_required' => $is_required,
                    'description' => $request->input('description')
                ]);
                break;
        }

        log_activity('CREATE_MASTER_DATA', "Menambahkan data master '{$type}' baru.");
        return $response->redirect("/admin/master?tab={$type}&success=Data+berhasil+ditambahkan");
    }

    public function update(Request $request, Response $response): RedirectResponse
    {
        $type = $request->input('type');
        $id = $request->input('id');

        if (!$id) return $response->redirect("/admin/master?tab={$type}&error=ID+tidak+valid");

        switch ($type) {
            case 'wave':
                $name = $request->input('name');
                $academic_year = $request->input('academic_year') ?: null;
                $description = $request->input('description') ?: null;
                $start = $request->input('start_date');
                $end = $request->input('end_date');
                $is_active = $request->input('is_active') ? 1 : 0;
                if ($is_active) {
                    $this->waves->getDb()->query("UPDATE waves SET is_active = 0");
                }
                $this->waves->updateById($id, [
                    'name' => $name, 
                    'academic_year' => $academic_year,
                    'description' => $description, 
                    'start_date' => $start, 
                    'end_date' => $end, 
                    'is_active' => $is_active
                ]);
                break;

            case 'payment-account':
                $bank_name = $request->input('bank_name');
                $account_number = $request->input('account_number');
                $account_holder = $request->input('account_holder');
                $is_active = $request->input('is_active') ? 1 : 0;
                if (!$bank_name || !$account_number || !$account_holder) return $response->redirect("/admin/master?tab=payment-account&error=Seluruh+kolom+harus+diisi");
                $this->paymentAccounts->updateById($id, [
                    'bank_name' => $bank_name,
                    'account_number' => $account_number,
                    'account_holder' => $account_holder,
                    'is_active' => $is_active
                ]);
                break;

            case 'nim-format':
                $name = $request->input('name');
                $format_pattern = $request->input('format_pattern');
                $is_active = $request->input('is_active') ? 1 : 0;
                if (!$name || !$format_pattern) return $response->redirect("/admin/master?tab=nim-format&error=Seluruh+kolom+harus+diisi");
                if ($is_active) {
                    $this->nimFormats->getDb()->query("UPDATE nim_formats SET is_active = 0");
                }
                $this->nimFormats->updateById($id, [
                    'name' => $name,
                    'format_pattern' => $format_pattern,
                    'is_active' => $is_active
                ]);
                break;

            case 'faculty':
                $code = $request->input('code');
                $name = $request->input('name');
                $this->faculties->updateById($id, ['code' => strtoupper($code), 'name' => $name]);
                break;

            case 'study-program':
                $faculty_id = $request->input('faculty_id');
                $code = $request->input('code');
                $name = $request->input('name');
                $this->studyPrograms->updateById($id, ['faculty_id' => $faculty_id, 'code' => strtoupper($code), 'name' => $name]);
                break;

            case 'document-type':
                $name = $request->input('name');
                $is_required = $request->input('is_required') ? 1 : 0;
                $this->documentTypes->updateById($id, [
                    'name' => $name, 
                    'is_required' => $is_required,
                    'description' => $request->input('description')
                ]);
                break;
        }

        log_activity('UPDATE_MASTER_DATA', "Memperbarui data master '{$type}' dengan ID {$id}.");
        return $response->redirect("/admin/master?tab={$type}&success=Data+berhasil+diperbarui");
    }

    public function delete(Request $request, Response $response): RedirectResponse
    {
        $type = $request->input('type');
        $id = $request->input('id');

        if (!$id) return $response->redirect("/admin/master?tab={$type}&error=ID+tidak+valid");

        switch ($type) {
            case 'wave':
                $this->waves->deleteById($id);
                break;
            case 'faculty':
                $this->faculties->deleteById($id);
                break;
            case 'study-program':
                $this->studyPrograms->deleteById($id);
                break;
            case 'document-type':
                $this->documentTypes->deleteById($id);
                break;
            case 'payment-account':
                $this->paymentAccounts->deleteById($id);
                break;
            case 'nim-format':
                $this->nimFormats->deleteById($id);
                break;
        }

        log_activity('DELETE_MASTER_DATA', "Menghapus data master '{$type}' dengan ID {$id}.");
        return $response->redirect("/admin/master?tab={$type}&success=Data+berhasil+dihapus");
    }

    public function waveDetail(Request $request, Response $response): View|RedirectResponse
    {
        $id = $request->input('id');
        if (!$id) {
            return $response->redirect('/admin/master?tab=wave&error=ID+gelombang+tidak+valid');
        }

        $wave = $this->waves->find($id);
        if (!$wave) {
            return $response->redirect('/admin/master?tab=wave&error=Gelombang+tidak+ditemukan');
        }

        $activePrograms = $this->waveStudyPrograms->findByWaveId($id);
        $mappedPrograms = [];
        foreach ($activePrograms as $ap) {
            $mappedPrograms[$ap['study_program_id']] = [
                'id' => $ap['id'],
                'reregistration_fee_total' => $ap['reregistration_fee_total'],
                'reregistration_fee_archive' => $ap['reregistration_fee_archive'],
                'required_documents' => json_decode($ap['required_documents'] ?? '[]', true) ?: [],
                'exam_stages' => json_decode($ap['exam_stages'] ?? '[]', true) ?: []
            ];
        }

        $data = [
            'wave' => $wave,
            'study_programs' => $this->studyPrograms->all(),
            'mapped_programs' => $mappedPrograms,
            'document_types' => $this->documentTypes->all()
        ];

        return $response->renderPage($data, [
            'path' => '/admin/master/wave_detail',
            'meta' => ['title' => 'Konfigurasi Gelombang | ' . env('APP_NAME')]
        ]);
    }

    public function saveWaveDetail(Request $request, Response $response): RedirectResponse
    {
        $db = $this->waveStudyPrograms->getDb();
        $waveId = $request->input('wave_id');
        if (!$waveId) {
            return $response->redirect('/admin/master?tab=wave&error=ID+gelombang+tidak+valid');
        }

        $prodiIds = $request->input('prodi_ids') ?: [];

        $existing = $this->waveStudyPrograms->findByWaveId($waveId);
        foreach ($existing as $ex) {
            if (!in_array($ex['study_program_id'], $prodiIds)) {
                $this->waveStudyPrograms->deleteById($ex['id']);
            }
        }


        foreach ($prodiIds as $prodiId) {
            $reregFee = $request->input('reregistration_fee_total_' . $prodiId) ?: 0;
            $reregArchive = null;

            $existingProg = $this->waveStudyPrograms->findByWaveAndProgram($waveId, $prodiId);
            if ($existingProg) {
                $reregArchive = $existingProg['reregistration_fee_archive'];
            }

            if (isset($_FILES['reregistration_fee_archive_' . $prodiId]) && $_FILES['reregistration_fee_archive_' . $prodiId]['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['reregistration_fee_archive_' . $prodiId];
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = 'wave_' . $waveId . '_prodi_' . $prodiId . '_rereg_' . uniqid() . '.' . $ext;
                $uploadDir = MAZU_PUBLIC_PATH . 'uploads/fees/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
                    $reregArchive = '/uploads/fees/' . $filename;
                }
            }

            $docTypeIds = $request->input('doc_type_ids_' . $prodiId) ?: [];
            $docDescs = $request->input('doc_descriptions_' . $prodiId) ?: [];
            $requiredDocs = [];

            if (!empty($docTypeIds)) {
                $placeholders = implode(',', array_fill(0, count($docTypeIds), '?'));
                $stmt = $db->prepare("SELECT id, name FROM document_types WHERE id IN ($placeholders)");
                $stmt->execute($docTypeIds);
                $dbDocTypes = $stmt->fetchAll() ?: [];
                $docNamesMap = [];
                foreach ($dbDocTypes as $dt) {
                    $docNamesMap[$dt['id']] = $dt['name'];
                }

                for ($i = 0; $i < count($docTypeIds); $i++) {
                    $dtId = (int)$docTypeIds[$i];
                    if ($dtId && isset($docNamesMap[$dtId])) {
                        $requiredDocs[] = [
                            'document_type_id' => $dtId,
                            'name' => $docNamesMap[$dtId],
                            'description' => $docDescs[$i] ?? ''
                        ];
                    }
                }
            }

            $progData = [
                'wave_id' => $waveId,
                'study_program_id' => $prodiId,
                'reregistration_fee_total' => $reregFee,
                'reregistration_fee_archive' => $reregArchive,
                'required_documents' => json_encode($requiredDocs),
                'exam_stages' => json_encode([])
            ];

            if ($existingProg) {
                $this->waveStudyPrograms->updateById($existingProg['id'], $progData);
            } else {
                $this->waveStudyPrograms->insert($progData);
            }
        }

        $examDates = $request->input('exam_date') ?: [];
        $examTimes = $request->input('exam_time') ?: [];
        $examPlaces = $request->input('exam_place') ?: [];
        $examTypes = $request->input('exam_type') ?: [];
        $examDescs = $request->input('exam_description') ?: [];
        $examStages = [];
        for ($i = 0; $i < count($examDates); $i++) {
            if (!empty($examDates[$i])) {
                $examStages[] = [
                    'stage_number' => $i + 1,
                    'date' => $examDates[$i],
                    'time' => $examTimes[$i] ?? '',
                    'place' => $examPlaces[$i] ?? '',
                    'type' => $examTypes[$i] ?? 'offline',
                    'description' => $examDescs[$i] ?? ''
                ];
            }
        }
        $this->waves->updateById($waveId, ['exam_stages' => json_encode($examStages)]);

        log_activity('CONFIGURE_WAVE_STUDY_PROGRAMS', "Mengkonfigurasi detail program studi dan tahapan ujian pada gelombang ID {$waveId}.");
        return $response->redirect("/admin/master?tab=wave&success=Konfigurasi+gelombang+berhasil+disimpan");
    }

    public function saveRegistrationFee(Request $request, Response $response): RedirectResponse
    {
        if (!has_permission('manage_settings')) {
            return $response->redirect('/dashboard?error=Anda+tidak+memiliki+hak+akses+ke+halaman+ini.');
        }

        $regFee = $request->input('registration_fee_total') !== '' ? (int)$request->input('registration_fee_total') : 100000;
        $db = $this->waves->getDb();

        $stmt = $db->prepare("SELECT COUNT(*) as count FROM settings WHERE `key` = :key");
        
        $stmt->execute(['key' => 'registration_fee_total']);
        if ($stmt->fetch()['count'] == 0) {
            $db->prepare("INSERT INTO settings (`key`, `value`) VALUES ('registration_fee_total', '100000')")->execute();
        }

        $stmt->execute(['key' => 'registration_fee_archive']);
        if ($stmt->fetch()['count'] == 0) {
            $db->prepare("INSERT INTO settings (`key`, `value`) VALUES ('registration_fee_archive', '')")->execute();
        }

        $db->prepare("UPDATE settings SET value = :val WHERE `key` = 'registration_fee_total'")->execute(['val' => $regFee]);

        if (isset($_FILES['registration_fee_archive']) && $_FILES['registration_fee_archive']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['registration_fee_archive'];
            $uploadDir = MAZU_ENV_PATH . 'public/uploads/fees/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'global_fee_' . uniqid() . '.' . $ext;
            if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
                $regArchive = '/uploads/fees/' . $filename;
                $db->prepare("UPDATE settings SET value = :val WHERE `key` = 'registration_fee_archive'")->execute(['val' => $regArchive]);
            }
        }

        log_activity('UPDATE_GLOBAL_REGISTRATION_FEE', "Memperbarui biaya formulir pendaftaran global menjadi Rp" . number_format($regFee, 0, ',', '.'));
        return $response->redirect('/admin/master?tab=registration-fee&success=Pengaturan+biaya+formulir+berhasil+disimpan');
    }
}
