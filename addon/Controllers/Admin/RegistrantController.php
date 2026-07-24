<?php

namespace Addon\Controllers\Admin;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\View\View;
use App\Core\Http\RedirectResponse;
use App\Services\SessionService;

use Addon\Models\RegistrationModel;
use Addon\Models\RegistrationDocumentModel;
use Addon\Models\RegistrationProgramModel;
use Addon\Models\RegistrationEducationModel;
use Addon\Models\RegistrationParentModel;
use Addon\Models\RegistrationAddressModel;
use Addon\Models\SelectionResultModel;

use Dompdf\Dompdf;
use Dompdf\Options;

class RegistrantController
{
    public function __construct(
        private SessionService $session,
        private RegistrationModel $registrations,
        private RegistrationDocumentModel $documents,
        private RegistrationProgramModel $programs,
        private RegistrationEducationModel $educations,
        private RegistrationParentModel $parents,
        private RegistrationAddressModel $addresses,
        private SelectionResultModel $selectionResults
    ) {}

    private function checkAccess(Response $response)
    {
        if (!$this->session->get('is_logged_in')) {
            return $response->redirect('/login');
        }
        if (!has_permission('manage_selection') && !has_permission('verify_document') && !has_permission('verify_payment')) {
            return $response->redirect('/dashboard?error=Anda+tidak+memiliki+hak+akses+ke+halaman+ini.');
        }
        return null;
    }

    private function getFilteredRegistrants(Request $request, int $limit = -1, int $offset = 0): array
    {
        $db = $this->registrations->getDb();
        $search = $request->input('search');
        $programId = $request->input('program_id');
        $status = $request->input('status');

        $query = "
            SELECT r.*, sp1.name as program1_name, sp2.name as program2_name, sp3.name as program3_name 
            FROM registrations r 
            LEFT JOIN registration_programs rp ON r.id = rp.registration_id 
            LEFT JOIN study_programs sp1 ON rp.program1_id = sp1.id 
            LEFT JOIN study_programs sp2 ON rp.program2_id = sp2.id
            LEFT JOIN study_programs sp3 ON rp.program3_id = sp3.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($search)) {
            $query .= " AND (r.full_name LIKE :search OR r.email LIKE :search OR r.nik LIKE :search OR r.nisn LIKE :search)";
            $params['search'] = '%' . $search . '%';
        }

        if (!empty($programId)) {
            $query .= " AND (rp.program1_id = :program_id OR rp.program2_id = :program_id OR rp.program3_id = :program_id)";
            $params['program_id'] = (int) $programId;
        }

        if (!empty($status)) {
            $query .= " AND r.status = :status";
            $params['status'] = $status;
        }

        $query .= " ORDER BY r.created_at DESC";
        if ($limit > 0) {
            $query .= " LIMIT " . $limit . " OFFSET " . $offset;
        }

        $stmt = $db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    private function getFilteredRegistrantsCount(Request $request): int
    {
        $db = $this->registrations->getDb();
        $search = $request->input('search');
        $programId = $request->input('program_id');
        $status = $request->input('status');

        $query = "
            SELECT COUNT(DISTINCT r.id) as count
            FROM registrations r 
            LEFT JOIN registration_programs rp ON r.id = rp.registration_id 
            WHERE 1=1
        ";
        $params = [];

        if (!empty($search)) {
            $query .= " AND (r.full_name LIKE :search OR r.email LIKE :search OR r.nik LIKE :search OR r.nisn LIKE :search)";
            $params['search'] = '%' . $search . '%';
        }

        if (!empty($programId)) {
            $query .= " AND (rp.program1_id = :program_id OR rp.program2_id = :program_id OR rp.program3_id = :program_id)";
            $params['program_id'] = (int) $programId;
        }

        if (!empty($status)) {
            $query .= " AND r.status = :status";
            $params['status'] = $status;
        }

        $stmt = $db->prepare($query);
        $stmt->execute($params);
        return (int) ($stmt->fetch()['count'] ?? 0);
    }

    public function listRegistrants(Request $request, Response $response): View | RedirectResponse
    {
        if ($redirect = $this->checkAccess($response)) return $redirect;

        $page = (int) ($request->input('page') ?: 1);
        if ($page < 1) $page = 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $totalCount = $this->getFilteredRegistrantsCount($request);
        $totalPages = (int) ceil($totalCount / $limit);
        if ($totalPages < 1) $totalPages = 1;
        if ($page > $totalPages) {
            $page = $totalPages;
            $offset = ($page - 1) * $limit;
        }

        $registrants = $this->getFilteredRegistrants($request, $limit, $offset);

        $db = $this->registrations->getDb();
        $stmt = $db->prepare("SELECT * FROM study_programs ORDER BY name ASC");
        $stmt->execute();
        $programsList = $stmt->fetchAll();

        return $response->renderPage([
            'registrants' => $registrants,
            'programs' => $programsList,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalCount' => $totalCount,
            'limit' => $limit,
            'filters' => [
                'search' => $request->input('search'),
                'program_id' => $request->input('program_id'),
                'status' => $request->input('status')
            ]
        ], [
            'path' => '/admin/registrants/index',
            'meta' => ['title' => 'Manajemen Pendaftar | ' . env('APP_NAME')]
        ]);
    }

    public function showDetail(Request $request, Response $response): View | RedirectResponse
    {
        if ($redirect = $this->checkAccess($response)) return $redirect;

        $regId = (int) $request->input('id');
        if (!$regId) {
            return $response->redirect('/admin/registrants?error=Pendaftar+tidak+valid');
        }

        $registration = $this->registrations->find($regId);
        if (!$registration) {
            return $response->redirect('/admin/registrants?error=Pendaftar+tidak+ditemukan');
        }

        $db = $this->registrations->getDb();
        
        $stmt = $db->prepare("
            SELECT rp.*, sp1.name as program1_name, sp2.name as program2_name, sp3.name as program3_name 
            FROM registration_programs rp
            LEFT JOIN study_programs sp1 ON rp.program1_id = sp1.id
            LEFT JOIN study_programs sp2 ON rp.program2_id = sp2.id
            LEFT JOIN study_programs sp3 ON rp.program3_id = sp3.id
            WHERE rp.registration_id = :reg_id LIMIT 1
        ");
        $stmt->execute(['reg_id' => $regId]);
        $prog = $stmt->fetch();

        $stmt = $db->prepare("SELECT * FROM registration_addresses WHERE registration_id = :reg_id LIMIT 1");
        $stmt->execute(['reg_id' => $regId]);
        $address = $stmt->fetch();

        $stmt = $db->prepare("SELECT * FROM registration_educations WHERE registration_id = :reg_id LIMIT 1");
        $stmt->execute(['reg_id' => $regId]);
        $education = $stmt->fetch();

        $stmt = $db->prepare("SELECT * FROM registration_parents WHERE registration_id = :reg_id LIMIT 1");
        $stmt->execute(['reg_id' => $regId]);
        $parent = $stmt->fetch();

        $stmt = $db->prepare("
            SELECT rd.*, dt.name as document_name 
            FROM registration_documents rd
            JOIN document_types dt ON rd.document_type_id = dt.id
            WHERE rd.registration_id = :reg_id
        ");
        $stmt->execute(['reg_id' => $regId]);
        $docs = $stmt->fetchAll();

        $stmt = $db->prepare("SELECT * FROM selection_results WHERE registration_id = :reg_id LIMIT 1");
        $stmt->execute(['reg_id' => $regId]);
        $selection = $stmt->fetch();

        $waveStudyPrograms = [];
        if ($registration['wave_id'] && $prog) {
            $prodiIds = [];
            if (!empty($prog['program1_id'])) $prodiIds[] = (int)$prog['program1_id'];
            if (!empty($prog['program2_id'])) $prodiIds[] = (int)$prog['program2_id'];
            if (!empty($prog['program3_id'])) $prodiIds[] = (int)$prog['program3_id'];
            $prodiIds = array_unique(array_filter($prodiIds));

            if (!empty($prodiIds)) {
                $placeholders = implode(',', array_fill(0, count($prodiIds), '?'));
                $stmt = $db->prepare("SELECT * FROM wave_study_programs WHERE wave_id = ? AND study_program_id IN ($placeholders)");
                $stmt->execute(array_merge([$registration['wave_id']], $prodiIds));
                $waveStudyPrograms = $stmt->fetchAll() ?: [];

                // Fetch study program names for display
                $prodiNamesMap = [];
                $stmtProdis = $db->prepare("SELECT id, name FROM study_programs WHERE id IN ($placeholders)");
                $stmtProdis->execute($prodiIds);
                foreach ($stmtProdis->fetchAll() as $p) {
                    $prodiNamesMap[$p['id']] = $p['name'];
                }

                foreach ($waveStudyPrograms as &$wsp) {
                    $wsp['study_program_name'] = $prodiNamesMap[$wsp['study_program_id']] ?? '';
                }
                unset($wsp);
            }
        }

        $stmt = $db->prepare("SELECT * FROM registration_exam_results WHERE registration_id = :reg_id ORDER BY stage_index ASC");
        $stmt->execute(['reg_id' => $regId]);
        $examResults = $stmt->fetchAll() ?: [];

        return $response->renderPage([
            'registration' => $registration,
            'programs' => $prog,
            'address' => $address,
            'education' => $education,
            'parent' => $parent,
            'documents' => $docs,
            'selection' => $selection,
            'wave_study_program' => $waveStudyPrograms[0] ?? null,
            'wave_study_programs' => $waveStudyPrograms,
            'exam_results' => $examResults
        ], [
            'path' => '/admin/registrants/detail',
            'meta' => ['title' => 'Detail Pendaftar | ' . env('APP_NAME')]
        ]);
    }

    public function saveExamStageStatus(Request $request, Response $response): RedirectResponse
    {
        if ($redirect = $this->checkAccess($response)) return $redirect;

        $regId = (int)$request->input('registration_id');
        $stageNumber = (int)$request->input('stage_number');
        $prodiId = $request->input('study_program_id') ? (int)$request->input('study_program_id') : null;
        $status = $request->input('status');

        if (!$regId || !$stageNumber || !in_array($status, ['Lulus', 'Tidak Lulus', 'Pending'])) {
            return $response->redirect('/admin/registrants/detail?id=' . $regId . '&error=Input+tidak+valid');
        }

        $db = $this->registrations->getDb();
        $stmt = $db->prepare("SELECT * FROM registration_exam_results WHERE registration_id = :reg_id AND stage_index = :stage_index AND (study_program_id = :prodi_id OR (:prodi_id_is_null = 1 AND study_program_id IS NULL)) LIMIT 1");
        $stmt->execute([
            'reg_id' => $regId,
            'stage_index' => $stageNumber,
            'prodi_id' => $prodiId,
            'prodi_id_is_null' => ($prodiId === null) ? 1 : 0
        ]);
        $existing = $stmt->fetch();

        if ($existing) {
            $stmt = $db->prepare("UPDATE registration_exam_results SET status = :status, study_program_id = :prodi_id, updated_at = :now WHERE id = :id");
            $stmt->execute([
                'status' => $status,
                'prodi_id' => $prodiId,
                'now' => date('Y-m-d H:i:s'),
                'id' => $existing['id']
            ]);
        } else {
            $stmt = $db->prepare("INSERT INTO registration_exam_results (registration_id, stage_index, study_program_id, status, created_at, updated_at) VALUES (:reg_id, :stage_index, :prodi_id, :status, :created_at, :updated_at)");
            $stmt->execute([
                'reg_id' => $regId,
                'stage_index' => $stageNumber,
                'prodi_id' => $prodiId,
                'status' => $status,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }

        log_activity('UPDATE_EXAM_STAGE_STATUS', "Memperbarui status ujian tahap {$stageNumber} pendaftar ID {$regId} menjadi {$status}.");
        return $response->redirect('/admin/registrants/detail?id=' . $regId . '&success=Status+tahap+ujian+berhasil+diperbarui');
    }

    public function editRegistrantForm(Request $request, Response $response): View | RedirectResponse
    {
        if ($redirect = $this->checkAccess($response)) return $redirect;

        $regId = (int) $request->input('id');
        if (!$regId) {
            return $response->redirect('/admin/registrants?error=Pendaftar+tidak+valid');
        }

        $registration = $this->registrations->find($regId);
        if (!$registration) {
            return $response->redirect('/admin/registrants?error=Pendaftar+tidak+ditemukan');
        }

        $db = $this->registrations->getDb();

        $stmt = $db->prepare("SELECT * FROM registration_programs WHERE registration_id = :reg_id LIMIT 1");
        $stmt->execute(['reg_id' => $regId]);
        $prog = $stmt->fetch();

        $stmt = $db->prepare("SELECT * FROM registration_addresses WHERE registration_id = :reg_id LIMIT 1");
        $stmt->execute(['reg_id' => $regId]);
        $address = $stmt->fetch();

        $stmt = $db->prepare("SELECT * FROM registration_educations WHERE registration_id = :reg_id LIMIT 1");
        $stmt->execute(['reg_id' => $regId]);
        $education = $stmt->fetch();

        $stmt = $db->prepare("SELECT * FROM registration_parents WHERE registration_id = :reg_id LIMIT 1");
        $stmt->execute(['reg_id' => $regId]);
        $parent = $stmt->fetch();

        $stmt = $db->prepare("SELECT * FROM study_programs ORDER BY name ASC");
        $stmt->execute();
        $programsList = $stmt->fetchAll();

        $stmt = $db->prepare("SELECT * FROM waves ORDER BY name ASC");
        $stmt->execute();
        $waves = $stmt->fetchAll();

        $stmt = $db->prepare("SELECT wave_id, study_program_id FROM wave_study_programs");
        $stmt->execute();
        $waveProdiList = $stmt->fetchAll() ?: [];
        $waveProdis = [];
        foreach ($waveProdiList as $wp) {
            $waveProdis[$wp['wave_id']][] = (int)$wp['study_program_id'];
        }

        return $response->renderPage([
            'registration' => $registration,
            'programs' => $prog,
            'address' => $address,
            'education' => $education,
            'parent' => $parent,
            'study_programs' => $programsList,
            'waves' => $waves,
            'wave_prodis' => $waveProdis
        ], [
            'path' => '/admin/registrants/edit',
            'meta' => ['title' => 'Koreksi Data Pendaftar | ' . env('APP_NAME')]
        ]);
    }
    public function updateRegistrant(Request $request, Response $response): RedirectResponse
    {
        if ($redirect = $this->checkAccess($response)) return $redirect;

        $regId = (int) $request->input('id');
        if (!$regId) {
            return $response->redirect('/admin/registrants?error=Pendaftar+tidak+valid');
        }

        $waveId = $request->input('wave_id');
        $prog1Id = $request->input('program1_id');
        $prog2Id = $request->input('program2_id') ?: null;
        $prog3Id = $request->input('program3_id') ?: null;

        if (!$waveId || !$prog1Id) {
            return $response->redirect('/admin/registrants/edit?id=' . $regId . '&error=' . urlencode('Harap lengkapi semua pilihan PMB wajib'));
        }

        $prodis = [$prog1Id];
        if ($prog2Id) {
            if (in_array($prog2Id, $prodis)) {
                return $response->redirect('/admin/registrants/edit?id=' . $regId . '&error=' . urlencode('Pilihan program studi tidak boleh ada yang sama'));
            }
            $prodis[] = $prog2Id;
        }
        if ($prog3Id) {
            if (in_array($prog3Id, $prodis)) {
                return $response->redirect('/admin/registrants/edit?id=' . $regId . '&error=' . urlencode('Pilihan program studi tidak boleh ada yang sama'));
            }
            $prodis[] = $prog3Id;
        }

        $db = $this->registrations->getDb();

        try {
            $db->beginTransaction();

            $this->registrations->updateById($regId, [
                'full_name' => $request->input('full_name'),
                'nik' => $request->input('nik'),
                'nisn' => $request->input('nisn'),
                'birth_place' => $request->input('birth_place'),
                'birth_date' => $request->input('birth_date'),
                'gender' => $request->input('gender'),
                'religion' => $request->input('religion'),
                'phone' => $request->input('phone'),
                'mother_name' => $request->input('mother_name'),
                'info_source' => $request->input('info_source'),
                'wave_id' => (int)$request->input('wave_id')
            ]);

            $stmt = $db->prepare("UPDATE registration_programs SET program1_id = :p1, program2_id = :p2, program3_id = :p3 WHERE registration_id = :reg_id");
            $stmt->execute([
                'p1' => (int)$request->input('program1_id'),
                'p2' => $request->input('program2_id') ? (int)$request->input('program2_id') : null,
                'p3' => $request->input('program3_id') ? (int)$request->input('program3_id') : null,
                'reg_id' => $regId
            ]);

            $stmt = $db->prepare("
                UPDATE registration_addresses 
                SET province = '',
                    city = '',
                    district = :district,
                    subdistrict = :subdistrict,
                    postal_code = :postal_code,
                    address = :address,
                    kps_receiver = :kps_receiver,
                    kps_number = :kps_number,
                    transportation = :transportation,
                    living_type = :living_type,
                    citizenship = :citizenship,
                    npwp = :npwp,
                    street = :street,
                    telephone = :telephone,
                    dusun = :dusun,
                    rt = :rt,
                    rw = :rw
                WHERE registration_id = :reg_id
            ");
            $kps_receiver = $request->input('kps_receiver');
            $kps_number = $kps_receiver === 'ya' ? $request->input('kps_number') : null;
            $stmt->execute([
                'district' => $request->input('district'),
                'subdistrict' => $request->input('subdistrict'),
                'postal_code' => $request->input('postal_code'),
                'address' => $request->input('address'),
                'kps_receiver' => $kps_receiver,
                'kps_number' => $kps_number,
                'transportation' => $request->input('transportation') ?: null,
                'living_type' => $request->input('living_type') ?: null,
                'citizenship' => $request->input('citizenship') ?: null,
                'npwp' => $request->input('npwp') ?: null,
                'street' => $request->input('street') ?: null,
                'telephone' => $request->input('telephone') ?: null,
                'dusun' => $request->input('dusun') ?: null,
                'rt' => $request->input('rt') ?: null,
                'rw' => $request->input('rw') ?: null,
                'reg_id' => $regId
            ]);

            $stmt = $db->prepare("
                UPDATE registration_educations 
                SET school_name = :school,
                    school_major = :major,
                    graduation_year = :year,
                    diploma_number = :diploma_number,
                    average_score = :average_score
                WHERE registration_id = :reg_id
            ");
            $stmt->execute([
                'school' => $request->input('school_name'),
                'major' => $request->input('major'),
                'year' => (int)$request->input('graduation_year'),
                'diploma_number' => $request->input('diploma_number') ?: null,
                'average_score' => $request->input('average_score') ? (float)$request->input('average_score') : 0.0,
                'reg_id' => $regId
            ]);

            $stmt = $db->prepare("
                UPDATE registration_parents 
                SET father_name = :father_name,
                    father_nik = :father_nik,
                    father_birth_date = :father_birth_date,
                    father_education = :father_education,
                    father_occupation = :father_occupation,
                    father_income = :father_income,
                    mother_name = :mother_name,
                    mother_nik = :mother_nik,
                    mother_birth_date = :mother_birth_date,
                    mother_education = :mother_education,
                    mother_occupation = :mother_occupation,
                    mother_income = :mother_income,
                    guardian_name = :guardian_name,
                    guardian_birth_date = :guardian_birth_date,
                    guardian_education = :guardian_education,
                    guardian_occupation = :guardian_occupation,
                    guardian_income = :guardian_income
                WHERE registration_id = :reg_id
            ");
            $stmt->execute([
                'father_name' => $request->input('father_name') ?: null,
                'father_nik' => $request->input('father_nik') ?: null,
                'father_birth_date' => $request->input('father_birth_date') ?: null,
                'father_education' => $request->input('father_education') ?: null,
                'father_occupation' => $request->input('father_occupation') ?: null,
                'father_income' => $request->input('father_income') ?: null,
                'mother_name' => $request->input('mother_name') ?: null,
                'mother_nik' => $request->input('mother_nik') ?: null,
                'mother_birth_date' => $request->input('mother_birth_date') ?: null,
                'mother_education' => $request->input('mother_education') ?: null,
                'mother_occupation' => $request->input('mother_occupation') ?: null,
                'mother_income' => $request->input('mother_income') ?: null,
                'guardian_name' => $request->input('guardian_name') ?: null,
                'guardian_birth_date' => $request->input('guardian_birth_date') ?: null,
                'guardian_education' => $request->input('guardian_education') ?: null,
                'guardian_occupation' => $request->input('guardian_occupation') ?: null,
                'guardian_income' => $request->input('guardian_income') ?: null,
                'reg_id' => $regId
            ]);

            $db->commit();
            return $response->redirect('/admin/registrants/detail?id=' . $regId . '&success=Data+pendaftar+berhasil+dikoreksi.');
        } catch (\Throwable $e) {
            $db->rollBack();
            return $response->redirect('/admin/registrants/edit?id=' . $regId . '&error=' . urlencode('Gagal menyimpan koreksi: ' . $e->getMessage()));
        }
    }

    public function exportPdf(Request $request, Response $response)
    {
        if ($redirect = $this->checkAccess($response)) return $redirect;

        $registrants = $this->getFilteredRegistrants($request);

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);

        ob_start();
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Laporan Pendaftar PMB</title>
            <style>
                body { font-family: sans-serif; font-size: 11px; color: #333; }
                .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
                .header h2 { margin: 0; font-size: 16px; }
                .header p { margin: 4px 0 0 0; font-size: 11px; color: #666; }
                table { width: 100%; border-collapse: collapse; margin-top: 10px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; font-weight: bold; }
                .status-badge { font-weight: bold; }
            </style>
        </head>
        <body>
            <div class="header">
                <h2>DAFTAR CALON MAHASISWA BARU</h2>
                <h2>KAMPUS MANDIRI KENCANA</h2>
                <p>Dicetak pada: <?php echo date('d-m-Y H:i'); ?></p>
            </div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 30px; text-align: center;">No</th>
                        <th>Nama Lengkap</th>
                        <th>Email</th>
                        <th>Program Studi Pilihan 1</th>
                        <th>Program Studi Pilihan 2</th>
                        <th>Program Studi Pilihan 3</th>
                        <th style="width: 80px; text-align: center;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($registrants)): ?>
                        <tr>
                            <td colspan="7" style="text-align: center;">Tidak ada data pendaftar.</td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($registrants as $r): ?>
                            <tr>
                                <td style="text-align: center;"><?= $no++ ?></td>
                                <td><strong><?= htmlspecialchars($r['full_name']) ?></strong></td>
                                <td><?= htmlspecialchars($r['email']) ?></td>
                                <td><?= htmlspecialchars($r['program1_name'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($r['program2_name'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($r['program3_name'] ?? '-') ?></td>
                                <td style="text-align: center;" class="status-badge"><?= htmlspecialchars($r['status']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </body>
        </html>
        <?php
        $html = ob_get_clean();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $dompdf->stream('daftar_pendaftar_' . date('Ymd_His') . '.pdf', ['Attachment' => true]);
        exit;
    }

    public function exportCsv(Request $request, Response $response)
    {
        if ($redirect = $this->checkAccess($response)) return $redirect;

        $registrants = $this->getFilteredRegistrants($request);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="daftar_pendaftar_' . date('Ymd_His') . '.csv"');

        $output = fopen('php://output', 'w');

        // Column headers
        fputcsv($output, ['No', 'Nama Lengkap', 'Email', 'NIK', 'NISN', 'Jenis Kelamin', 'Agama', 'No Telepon', 'Pilihan Prodi 1', 'Pilihan Prodi 2', 'Pilihan Prodi 3', 'Status']);

        $no = 1;
        foreach ($registrants as $r) {
            fputcsv($output, [
                $no++,
                $r['full_name'],
                $r['email'],
                "'" . $r['nik'], // Force text format for NIK to prevent truncation/exponential notation in Excel
                "'" . $r['nisn'],
                $r['gender'],
                $r['religion'],
                $r['phone'],
                $r['program1_name'] ?? '-',
                $r['program2_name'] ?? '-',
                $r['program3_name'] ?? '-',
                $r['status']
            ]);
        }

        fclose($output);
        exit;
    }

    private function generateExamCardPdf(array $registration, ?int $studyProgramId, Response $response): Response
    {
        $db = $this->registrations->getDb();
        $regId = $registration['id'];
        
        $stmt = $db->prepare("
            SELECT rp.*, sp1.name as program1_name, sp2.name as program2_name, sp3.name as program3_name
            FROM registration_programs rp
            LEFT JOIN study_programs sp1 ON rp.program1_id = sp1.id
            LEFT JOIN study_programs sp2 ON rp.program2_id = sp2.id
            LEFT JOIN study_programs sp3 ON rp.program3_id = sp3.id
            WHERE rp.registration_id = :reg_id LIMIT 1
        ");
        $stmt->execute(['reg_id' => $regId]);
        $prog = $stmt->fetch();

        if (!$studyProgramId) {
            $studyProgramId = $prog ? (int)$prog['program1_id'] : 0;
        }

        $selectedProgramName = '-';
        if ($prog) {
            if ((int)$prog['program1_id'] === $studyProgramId) {
                $selectedProgramName = $prog['program1_name'];
            } elseif ((int)$prog['program2_id'] === $studyProgramId) {
                $selectedProgramName = $prog['program2_name'];
            } elseif ((int)$prog['program3_id'] === $studyProgramId) {
                $selectedProgramName = $prog['program3_name'];
            }
        }

        $stmt = $db->prepare("SELECT * FROM wave_study_programs WHERE wave_id = :wave_id AND study_program_id = :prodi_id LIMIT 1");
        $stmt->execute([
            'wave_id' => $registration['wave_id'],
            'prodi_id' => $studyProgramId
        ]);
        $waveStudyProgram = $stmt->fetch() ?: null;
        $stages = $waveStudyProgram ? (json_decode($waveStudyProgram['exam_stages'] ?? '[]', true) ?: []) : [];

        $photoSrc = null;
        if (!empty($registration['photo_path'])) {
            $fullPhotoPath = MAZU_PUBLIC_PATH . ltrim($registration['photo_path'], '/');
            if (file_exists($fullPhotoPath)) {
                $ext = strtolower(pathinfo($fullPhotoPath, PATHINFO_EXTENSION));
                if ($ext !== 'png' || extension_loaded('gd')) {
                    $imgData = base64_encode(file_get_contents($fullPhotoPath));
                    $mimeType = ($ext === 'jpg' || $ext === 'jpeg') ? 'image/jpeg' : (($ext === 'gif') ? 'image/gif' : 'image/png');
                    $photoSrc = 'data:' . $mimeType . ';base64,' . $imgData;
                }
            }
        }

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $dompdf = new Dompdf($options);

        ob_start();
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Kartu Ujian PMB</title>
            <style>
                body { font-family: sans-serif; font-size: 11px; color: #333; }
                .card-border { border: 2px dashed #4f46e5; padding: 20px; border-radius: 10px; width: 480px; margin: 0 auto; background-color: #fafafa; }
                .header { text-align: center; border-bottom: 2px solid #4f46e5; padding-bottom: 10px; margin-bottom: 15px; }
                .header h3 { margin: 0; font-size: 14px; color: #1e1b4b; }
                .header p { margin: 3px 0 0 0; font-size: 10px; color: #4f46e5; font-weight: bold; }
                .title { text-align: center; font-size: 12px; font-weight: bold; margin-bottom: 15px; text-transform: uppercase; color: #333; }
                .photo-box { width: 90px; height: 120px; border: 1px solid #ccc; text-align: center; line-height: 120px; font-size: 9px; color: #999; float: left; background-color: #fff; margin-right: 20px; overflow: hidden; }
                .info-table { float: left; width: 330px; font-size: 11px; border-collapse: collapse; }
                .info-table td { padding: 4px 0; vertical-align: top; }
                .info-table .label { width: 110px; color: #666; }
                .info-table .colon { width: 10px; }
                .clearfix { clear: both; }
                .schedule-box { background-color: #e0e7ff; border: 1px solid #c7d2fe; padding: 10px; border-radius: 8px; margin-top: 15px; font-size: 10px; color: #3730a3; }
                .schedule-box strong { display: block; margin-bottom: 5px; font-size: 11px; }
                .footer { margin-top: 25px; font-size: 10px; }
                .footer-left { float: left; width: 200px; text-align: center; }
                .footer-right { float: right; width: 200px; text-align: center; }
            </style>
        </head>
        <body>
            <div class="card-border">
                <div class="header">
                    <h3><?= strtoupper(htmlspecialchars(get_setting('campus_name', 'KAMPUS MANDIRI KENCANA'))) ?></h3>
                    <p>PANITIA PENERIMAAN MAHASISWA BARU (PMB) <?= date('Y') ?></p>
                </div>
                <div class="title">KARTU PESERTA UJIAN SELEKSI</div>
                
                <div class="photo-box" style="<?= $photoSrc ? 'line-height: normal;' : '' ?>">
                    <?php if ($photoSrc): ?>
                        <img src="<?= $photoSrc ?>" style="width: 90px; height: 120px; object-fit: cover; display: block;">
                    <?php else: ?>
                        Foto 3x4
                    <?php endif; ?>
                </div>
                
                <table class="info-table">
                    <tr>
                        <td class="label">No. Registrasi</td>
                        <td class="colon">:</td>
                        <td><strong><?= get_registration_number($registration) ?></strong></td>
                    </tr>
                    <tr>
                        <td class="label">Nama Lengkap</td>
                        <td class="colon">:</td>
                        <td><?= htmlspecialchars($registration['full_name']) ?></td>
                    </tr>
                    <tr>
                        <td class="label">NIK / NISN</td>
                        <td class="colon">:</td>
                        <td><?= htmlspecialchars($registration['nik']) ?> / <?= htmlspecialchars($registration['nisn']) ?></td>
                    </tr>
                    <tr>
                        <td class="label">Program Studi Pilihan</td>
                        <td class="colon">:</td>
                        <td><?= htmlspecialchars($selectedProgramName) ?></td>
                    </tr>
                </table>
                <div class="clearfix"></div>

                <div class="schedule-box">
                    <strong>JADWAL & LOKASI UJIAN:</strong>
                    <?php if (empty($stages)): ?>
                        Metode Ujian: Computer Based Test (CBT) secara Online / Mandiri<br>
                        Ruangan: Virtual Room CBT (dapat diakses melalui dashboard)<br>
                        Tanggal Pelaksanaan: Sesuai petunjuk pada menu Ujian Seleksi
                    <?php else: 
                        foreach ($stages as $stg): ?>
                            Tahap <?= $stg['stage_number'] ?>: <?= htmlspecialchars($stg['description'] ?: 'Ujian Masuk') ?><br>
                            Tanggal: <?= htmlspecialchars($stg['date']) ?> (<?= htmlspecialchars($stg['time']) ?>)<br>
                            Tempat: <?= htmlspecialchars($stg['place']) ?> (<?= strtoupper($stg['type']) ?>)<br><br>
                        <?php endforeach; 
                    endif; ?>
                </div>

                <div class="footer">
                    <div class="footer-left">
                        <p>Peserta Ujian,</p>
                        <br><br><br>
                        <p>____________________</p>
                        <p>Tanda Tangan</p>
                    </div>
                    <div class="footer-right">
                        <p>Ketua Panitia PMB,</p>
                        <br><br><br>
                        <p><strong><?= htmlspecialchars(get_setting('pmb_chairman_name', 'Prof. Dr. Ir. Hermawan')) ?></strong></p>
                        <p><?= htmlspecialchars(get_setting('pmb_chairman_nip', 'NIP. 19750812 200212 1 002')) ?></p>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </body>
        </html>
        <?php
        $html = ob_get_clean();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $pdfOutput = $dompdf->output();

        $response->setHeader('Content-Type', 'application/pdf');
        $response->setHeader('Content-Disposition', 'attachment; filename="Kartu_Ujian_' . str_replace(' ', '_', $registration['full_name']) . '.pdf"');
        $response->setHeader('Content-Length', (string)strlen($pdfOutput));
        $response->setContent($pdfOutput);
        return $response;
    }

    private function generateRegistrationFormPdf(array $registration, Response $response): Response
    {
        $db = $this->registrations->getDb();
        $regId = $registration['id'];

        $stmt = $db->prepare("
            SELECT rp.*, sp1.name as program1_name, sp2.name as program2_name, sp3.name as program3_name 
            FROM registration_programs rp
            LEFT JOIN study_programs sp1 ON rp.program1_id = sp1.id
            LEFT JOIN study_programs sp2 ON rp.program2_id = sp2.id
            LEFT JOIN study_programs sp3 ON rp.program3_id = sp3.id
            WHERE rp.registration_id = :reg_id LIMIT 1
        ");
        $stmt->execute(['reg_id' => $regId]);
        $prog = $stmt->fetch();

        $photoSrc = null;
        if (!empty($registration['photo_path'])) {
            $fullPhotoPath = MAZU_PUBLIC_PATH . ltrim($registration['photo_path'], '/');
            if (file_exists($fullPhotoPath)) {
                $ext = strtolower(pathinfo($fullPhotoPath, PATHINFO_EXTENSION));
                if ($ext !== 'png' || extension_loaded('gd')) {
                    $imgData = base64_encode(file_get_contents($fullPhotoPath));
                    $mimeType = ($ext === 'jpg' || $ext === 'jpeg') ? 'image/jpeg' : (($ext === 'gif') ? 'image/gif' : 'image/png');
                    $photoSrc = 'data:' . $mimeType . ';base64,' . $imgData;
                }
            }
        }

        $stmt = $db->prepare("SELECT * FROM registration_addresses WHERE registration_id = :reg_id LIMIT 1");
        $stmt->execute(['reg_id' => $regId]);
        $address = $stmt->fetch();

        $stmt = $db->prepare("SELECT * FROM registration_educations WHERE registration_id = :reg_id LIMIT 1");
        $stmt->execute(['reg_id' => $regId]);
        $education = $stmt->fetch();

        $stmt = $db->prepare("SELECT * FROM registration_parents WHERE registration_id = :reg_id LIMIT 1");
        $stmt->execute(['reg_id' => $regId]);
        $parent = $stmt->fetch();

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $dompdf = new Dompdf($options);

        ob_start();
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Formulir Pendaftaran PMB</title>
            <style>
                body { font-family: sans-serif; font-size: 11px; color: #333; line-height: 1.4; }
                .header { text-align: center; border-bottom: 3px double #333; padding-bottom: 10px; margin-bottom: 20px; }
                .header h2 { margin: 0; font-size: 16px; color: #1e1b4b; }
                .header h3 { margin: 5px 0 0 0; font-size: 12px; color: #666; }
                .title { text-align: center; font-size: 13px; font-weight: bold; margin-bottom: 20px; text-decoration: underline; }
                .section-title { font-weight: bold; font-size: 11px; background-color: #f2f2f2; padding: 4px 8px; margin-top: 15px; margin-bottom: 10px; text-transform: uppercase; border-left: 3px solid #4f46e5; }
                table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
                td { padding: 4px 8px; vertical-align: top; }
                td.label { width: 150px; color: #555; }
                td.colon { width: 10px; }
                .footer-table { margin-top: 40px; }
                .footer-table td { text-align: center; width: 50%; }
            </style>
        </head>
        <body>
            <div class="header">
                <h2><?= strtoupper(htmlspecialchars(get_setting('campus_name', 'KAMPUS MANDIRI KENCANA'))) ?></h2>
                <h3>PANITIA PENERIMAAN MAHASISWA BARU (PMB) TAHUN AKADEMIK <?= date('Y') ?>/<?= date('Y') + 1 ?></h3>
            </div>
            
            <?php if ($photoSrc): ?>
                <div style="float: right; width: 90px; height: 120px; border: 1px solid #ccc; overflow: hidden; margin-left: 20px; margin-bottom: 20px; text-align: center; background: #fff;">
                    <img src="<?= $photoSrc ?>" style="width: 90px; height: 120px; object-fit: cover; display: block;">
                </div>
            <?php endif; ?>

            <div class="title">FORMULIR PENDAFTARAN MAHASISWA BARU</div>

            <div class="section-title" style="clear: left;">1. Data Pendaftaran</div>
            <table>
                <tr>
                    <td class="label">ID Registrasi</td>
                    <td class="colon">:</td>
                    <td><strong><?= get_registration_number($registration) ?></strong></td>
                </tr>
                <tr>
                    <td class="label">Tanggal Pendaftaran</td>
                    <td class="colon">:</td>
                    <td><?= date('d-m-Y H:i', strtotime($registration['created_at'])) ?></td>
                </tr>
                <tr>
                    <td class="label">Program Studi Pilihan 1</td>
                    <td class="colon">:</td>
                    <td><?= htmlspecialchars($prog['program1_name'] ?? '-') ?></td>
                </tr>
                <?php if ($prog && $prog['program2_name']): ?>
                    <tr>
                        <td class="label">Program Studi Pilihan 2</td>
                        <td class="colon">:</td>
                        <td><?= htmlspecialchars($prog['program2_name']) ?></td>
                    </tr>
                <?php endif; ?>
                <?php if ($prog && !empty($prog['program3_name'])): ?>
                    <tr>
                        <td class="label">Program Studi Pilihan 3</td>
                        <td class="colon">:</td>
                        <td><?= htmlspecialchars($prog['program3_name']) ?></td>
                    </tr>
                <?php endif; ?>
            </table>

            <div class="section-title">2. Identitas Diri Calon Mahasiswa</div>
            <table>
                <tr>
                    <td class="label">Nama Lengkap</td>
                    <td class="colon">:</td>
                    <td><?= htmlspecialchars($registration['full_name']) ?></td>
                </tr>
                <tr>
                    <td class="label">NIK / No. KTP</td>
                    <td class="colon">:</td>
                    <td><?= htmlspecialchars($registration['nik']) ?></td>
                </tr>
                <tr>
                    <td class="label">NISN</td>
                    <td class="colon">:</td>
                    <td><?= htmlspecialchars($registration['nisn']) ?></td>
                </tr>
                <tr>
                    <td class="label">Tempat, Tanggal Lahir</td>
                    <td class="colon">:</td>
                    <td><?= htmlspecialchars($registration['birth_place']) ?>, <?= date('d-m-Y', strtotime($registration['birth_date'])) ?></td>
                </tr>
                <tr>
                    <td class="label">Jenis Kelamin</td>
                    <td class="colon">:</td>
                    <td><?= htmlspecialchars($registration['gender']) ?></td>
                </tr>
                <tr>
                    <td class="label">Agama</td>
                    <td class="colon">:</td>
                    <td><?= htmlspecialchars($registration['religion']) ?></td>
                </tr>
                <tr>
                    <td class="label">Nomor WhatsApp</td>
                    <td class="colon">:</td>
                    <td><?= htmlspecialchars($registration['phone']) ?></td>
                </tr>
                <tr>
                    <td class="label">Email Aktif</td>
                    <td class="colon">:</td>
                    <td><?= htmlspecialchars($registration['email']) ?></td>
                </tr>
            </table>

            <div class="section-title">3. Alamat Tinggal</div>
            <table>
                <tr>
                    <td class="label">Kecamatan</td>
                    <td class="colon">:</td>
                    <td><?= htmlspecialchars($address['district'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td class="label">Kelurahan</td>
                    <td class="colon">:</td>
                    <td><?= htmlspecialchars($address['subdistrict'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td class="label">Alamat Lengkap (Jalan, Dusun, RT/RW)</td>
                    <td class="colon">:</td>
                    <td><?= htmlspecialchars($address['address'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td class="label">Kode Pos</td>
                    <td class="colon">:</td>
                    <td><?= htmlspecialchars($address['postal_code'] ?? '-') ?></td>
                </tr>
            </table>

            <div class="section-title">4. Riwayat Pendidikan</div>
            <table>
                <tr>
                    <td class="label">Sekolah Asal</td>
                    <td class="colon">:</td>
                    <td><?= htmlspecialchars($education['school_name'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td class="label">Jurusan</td>
                    <td class="colon">:</td>
                    <td><?= htmlspecialchars($education['school_major'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td class="label">Tahun Kelulusan</td>
                    <td class="colon">:</td>
                    <td><?= htmlspecialchars($education['graduation_year'] ?? '-') ?></td>
                </tr>
            </table>

            <div class="section-title">5. Data Orang Tua Kandung</div>
            <table>
                <tr>
                    <td class="label">Nama Ayah Kandung</td>
                    <td class="colon">:</td>
                    <td><?= htmlspecialchars($parent['father_name'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td class="label">Pekerjaan Ayah</td>
                    <td class="colon">:</td>
                    <td><?= htmlspecialchars($parent['father_occupation'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td class="label">Nama Ibu Kandung</td>
                    <td class="colon">:</td>
                    <td><?= htmlspecialchars($parent['mother_name'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td class="label">Pekerjaan Ibu</td>
                    <td class="colon">:</td>
                    <td><?= htmlspecialchars($parent['mother_occupation'] ?? '-') ?></td>
                </tr>
            </table>

            <?php
            $campusAddress = get_setting('campus_address', 'Jakarta');
            $city = 'Jakarta';
            $parts = explode(',', $campusAddress);
            if (count($parts) > 1) {
                $city = trim(end($parts));
            }
            ?>
            <table class="footer-table">
                <tr>
                    <td>
                        <p>Calon Mahasiswa,</p>
                        <br><br><br>
                        <p><strong><?= htmlspecialchars($registration['full_name']) ?></strong></p>
                    </td>
                    <td>
                        <p><?= htmlspecialchars($city) ?>, <?= date('d-m-Y') ?></p>
                        <p>Petugas PMB,</p>
                        <br><br><br>
                        <p>___________________________</p>
                    </td>
                </tr>
            </table>
        </body>
        </html>
        <?php
        $html = ob_get_clean();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $pdfOutput = $dompdf->output();

        $response->setHeader('Content-Type', 'application/pdf');
        $response->setHeader('Content-Disposition', 'attachment; filename="Formulir_Pendaftaran_' . str_replace(' ', '_', $registration['full_name']) . '.pdf"');
        $response->setHeader('Content-Length', (string)strlen($pdfOutput));
        $response->setContent($pdfOutput);
        return $response;
    }

    public function downloadExamCard(Request $request, Response $response)
    {
        if (!$this->session->get('is_logged_in')) {
            return $response->redirect('/login');
        }
        $userId = $this->session->get('auth.user_id');
        $registration = $this->registrations->findByUserId($userId);
        if (!$registration) {
            return $response->redirect('/dashboard?error=Pendaftaran+tidak+ditemukan');
        }
        $prodiId = $request->input('study_program_id') ? (int)$request->input('study_program_id') : null;
        return $this->generateExamCardPdf($registration, $prodiId, $response);
    }

    public function downloadRegistrationForm(Request $request, Response $response)
    {
        if (!$this->session->get('is_logged_in')) {
            return $response->redirect('/login');
        }
        $userId = $this->session->get('auth.user_id');
        $registration = $this->registrations->findByUserId($userId);
        if (!$registration) {
            return $response->redirect('/dashboard?error=Pendaftaran+tidak+ditemukan');
        }
        return $this->generateRegistrationFormPdf($registration, $response);
    }

    public function downloadExamCardAdmin(Request $request, Response $response)
    {
        if ($redirect = $this->checkAccess($response)) return $redirect;
        $id = (int)$request->input('id');
        $registration = $this->registrations->find($id);
        if (!$registration) {
            return $response->redirect('/admin/registrants?error=Pendaftar+tidak+ditemukan');
        }
        $prodiId = $request->input('study_program_id') ? (int)$request->input('study_program_id') : null;
        return $this->generateExamCardPdf($registration, $prodiId, $response);
    }

    public function downloadRegistrationFormAdmin(Request $request, Response $response)
    {
        if ($redirect = $this->checkAccess($response)) return $redirect;
        $id = (int)$request->input('id');
        $registration = $this->registrations->find($id);
        if (!$registration) {
            return $response->redirect('/admin/registrants?error=Pendaftar+tidak+ditemukan');
        }
        return $this->generateRegistrationFormPdf($registration, $response);
    }
}
