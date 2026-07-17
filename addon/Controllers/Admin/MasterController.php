<?php

namespace Addon\Controllers\Admin;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\View\View;
use App\Core\Http\RedirectResponse;
use Addon\Models\AcademicYearModel;
use Addon\Models\WaveModel;
use Addon\Models\FacultyModel;
use Addon\Models\StudyProgramModel;
use Addon\Models\AdmissionPathModel;
use Addon\Models\ClassModel;
use Addon\Models\DocumentTypeModel;
use Addon\Models\PaymentAccountModel;
use Addon\Models\NimFormatModel;
use Addon\Models\WaveStudyProgramModel;

class MasterController
{
    public function __construct(
        private AcademicYearModel $academicYears,
        private WaveModel $waves,
        private FacultyModel $faculties,
        private StudyProgramModel $studyPrograms,
        private AdmissionPathModel $admissionPaths,
        private ClassModel $classes,
        private DocumentTypeModel $documentTypes,
        private PaymentAccountModel $paymentAccounts,
        private NimFormatModel $nimFormats,
        private WaveStudyProgramModel $waveStudyPrograms
    ) {}

    public function index(Request $request, Response $response): View
    {
        $tab = $request->input('tab') ?? 'academic-year';

        $data = [
            'tab' => $tab,
            'academic_years' => $this->academicYears->all(),
            'waves' => $this->waves->all(),
            'faculties' => $this->faculties->all(),
            'study_programs' => $this->studyPrograms->all(),
            'admission_paths' => $this->admissionPaths->all(),
            'classes' => $this->classes->all(),
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
            case 'academic-year':
                $year = $request->input('year');
                $is_active = $request->input('is_active') ? 1 : 0;
                if (!$year) return $response->redirect('/admin/master?tab=academic-year&error=Tahun+akademik+harus+diisi');
                $this->academicYears->insert(['year' => $year, 'is_active' => $is_active]);
                break;

            case 'wave':
                $name = $request->input('name');
                $description = $request->input('description') ?: null;
                $start = $request->input('start_date');
                $end = $request->input('end_date');
                $is_active = $request->input('is_active') ? 1 : 0;
                if (!$name || !$start || !$end) return $response->redirect('/admin/master?tab=wave&error=Seluruh+kolom+harus+diisi');
                $this->waves->insert(['name' => $name, 'description' => $description, 'start_date' => $start, 'end_date' => $end, 'is_active' => $is_active]);
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

            case 'admission-path':
                $name = $request->input('name');
                $is_active = $request->input('is_active') ? 1 : 0;
                if (!$name) return $response->redirect('/admin/master?tab=admission-path&error=Nama+jalur+harus+diisi');
                $this->admissionPaths->insert(['name' => $name, 'is_active' => $is_active]);
                break;

            case 'class':
                $name = $request->input('name');
                $is_active = $request->input('is_active') ? 1 : 0;
                if (!$name) return $response->redirect('/admin/master?tab=class&error=Nama+kelas+harus+diisi');
                $this->classes->insert(['name' => $name, 'is_active' => $is_active]);
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
            case 'academic-year':
                $year = $request->input('year');
                $is_active = $request->input('is_active') ? 1 : 0;
                $this->academicYears->updateById($id, ['year' => $year, 'is_active' => $is_active]);
                break;

            case 'wave':
                $name = $request->input('name');
                $description = $request->input('description') ?: null;
                $start = $request->input('start_date');
                $end = $request->input('end_date');
                $is_active = $request->input('is_active') ? 1 : 0;
                $this->waves->updateById($id, ['name' => $name, 'description' => $description, 'start_date' => $start, 'end_date' => $end, 'is_active' => $is_active]);
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

            case 'admission-path':
                $name = $request->input('name');
                $is_active = $request->input('is_active') ? 1 : 0;
                $this->admissionPaths->updateById($id, ['name' => $name, 'is_active' => $is_active]);
                break;

            case 'class':
                $name = $request->input('name');
                $is_active = $request->input('is_active') ? 1 : 0;
                $this->classes->updateById($id, ['name' => $name, 'is_active' => $is_active]);
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
            case 'academic-year':
                $this->academicYears->deleteById($id);
                break;
            case 'wave':
                $this->waves->deleteById($id);
                break;
            case 'faculty':
                $this->faculties->deleteById($id);
                break;
            case 'study-program':
                $this->studyPrograms->deleteById($id);
                break;
            case 'admission-path':
                $this->admissionPaths->deleteById($id);
                break;
            case 'class':
                $this->classes->deleteById($id);
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
                'registration_fee_total' => $ap['registration_fee_total'],
                'registration_fee_archive' => $ap['registration_fee_archive'],
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

        $uploadDir = MAZU_ENV_PATH . 'public/uploads/fees/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        foreach ($prodiIds as $prodiId) {
            $regFee = $request->input('registration_fee_total_' . $prodiId) ?: 0;
            $reregFee = $request->input('reregistration_fee_total_' . $prodiId) ?: 0;

            $regArchive = null;
            $reregArchive = null;

            $existingProg = $this->waveStudyPrograms->findByWaveAndProgram($waveId, $prodiId);
            if ($existingProg) {
                $regArchive = $existingProg['registration_fee_archive'];
                $reregArchive = $existingProg['reregistration_fee_archive'];
            }

            if (isset($_FILES['registration_fee_archive_' . $prodiId]) && $_FILES['registration_fee_archive_' . $prodiId]['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['registration_fee_archive_' . $prodiId];
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = 'wave_' . $waveId . '_prodi_' . $prodiId . '_reg_' . uniqid() . '.' . $ext;
                if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
                    $regArchive = '/uploads/fees/' . $filename;
                }
            }

            if (isset($_FILES['reregistration_fee_archive_' . $prodiId]) && $_FILES['reregistration_fee_archive_' . $prodiId]['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['reregistration_fee_archive_' . $prodiId];
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = 'wave_' . $waveId . '_prodi_' . $prodiId . '_rereg_' . uniqid() . '.' . $ext;
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

            $examDates = $request->input('exam_date_' . $prodiId) ?: [];
            $examTimes = $request->input('exam_time_' . $prodiId) ?: [];
            $examPlaces = $request->input('exam_place_' . $prodiId) ?: [];
            $examTypes = $request->input('exam_type_' . $prodiId) ?: [];
            $examDescs = $request->input('exam_description_' . $prodiId) ?: [];
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

            $progData = [
                'wave_id' => $waveId,
                'study_program_id' => $prodiId,
                'registration_fee_total' => $regFee,
                'registration_fee_archive' => $regArchive,
                'reregistration_fee_total' => $reregFee,
                'reregistration_fee_archive' => $reregArchive,
                'required_documents' => json_encode($requiredDocs),
                'exam_stages' => json_encode($examStages)
            ];

            if ($existingProg) {
                $this->waveStudyPrograms->updateById($existingProg['id'], $progData);
            } else {
                $this->waveStudyPrograms->insert($progData);
            }
        }

        log_activity('CONFIGURE_WAVE_STUDY_PROGRAMS', "Mengkonfigurasi detail program studi dan tahapan ujian pada gelombang ID {$waveId}.");
        return $response->redirect("/admin/master?tab=wave&success=Konfigurasi+gelombang+berhasil+disimpan");
    }
}
