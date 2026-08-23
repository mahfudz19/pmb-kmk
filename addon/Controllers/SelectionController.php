<?php

namespace Addon\Controllers;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\View\View;
use App\Core\Http\RedirectResponse;
use App\Services\SessionService;

use Addon\Models\RegistrationModel;
use Addon\Models\StudyProgramModel;
use Addon\Models\SelectionResultModel;

class SelectionController
{
    public function __construct(
        private SessionService $session,
        private RegistrationModel $registrations,
        private StudyProgramModel $studyPrograms,
        private SelectionResultModel $selectionResults
    ) {}

    public function listCandidates(Request $request, Response $response): View|RedirectResponse
    {
        if (!has_permission('manage_selection')) {
            return $response->redirect('/dashboard?error=Anda+tidak+memiliki+hak+akses+ke+halaman+ini.');
        }

        $waveId = $request->input('wave_id');
        $waveIdFilter = ($waveId !== '' && $waveId !== null) ? (int)$waveId : null;

        $page = (int) ($request->input('page') ?: 1);
        if ($page < 1) $page = 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $db = $this->registrations->getDb();

        $params = [];
        $whereSql = " WHERE r.status != 'Draft' ";
        if ($waveIdFilter !== null) {
            $whereSql .= " AND r.wave_id = :wave_id ";
            $params['wave_id'] = $waveIdFilter;
        }

        $stmtCount = $db->prepare("
            SELECT COUNT(*) as count 
            FROM registrations r
            JOIN users u ON r.user_id = u.id
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
            SELECT r.*, u.email, w.name as wave_name,
                   rp.program1_id, rp.program2_id, rp.program3_id,
                   sp1.name as program1_name, sp2.name as program2_name, sp3.name as program3_name,
                   sr.test_score, sr.interview_score, sr.interview_notes, 
                   sr.status as selection_status, sr.passed_program_id, sr.notes as selection_notes,
                   sr.is_published
            FROM registrations r
            JOIN users u ON r.user_id = u.id
            LEFT JOIN registration_programs rp ON r.id = rp.registration_id
            LEFT JOIN study_programs sp1 ON rp.program1_id = sp1.id
            LEFT JOIN study_programs sp2 ON rp.program2_id = sp2.id
            LEFT JOIN study_programs sp3 ON rp.program3_id = sp3.id
            LEFT JOIN selection_results sr ON r.id = sr.registration_id
            LEFT JOIN waves w ON r.wave_id = w.id
            $whereSql
            ORDER BY r.updated_at DESC
            LIMIT " . $limit . " OFFSET " . $offset . "
        ");
        $stmt->execute($params);
        $candidates = $stmt->fetchAll() ?: [];
        $stmt->closeCursor();

        $programs = $this->studyPrograms->all();

        $stmtWaves = $db->prepare("SELECT id, exam_stages, name FROM waves");
        $stmtWaves->execute();
        $waves = $stmtWaves->fetchAll() ?: [];
        $stmtWaves->closeCursor();

        $wavesMap = [];
        foreach ($waves as $w) {
            $wavesMap[$w['id']] = json_decode($w['exam_stages'] ?? '[]', true) ?: [];
        }

        $candidateIds = array_column($candidates, 'id');
        $examResultsMap = [];
        if (!empty($candidateIds)) {
            $placeholders = implode(',', array_fill(0, count($candidateIds), '?'));
            $stmt = $db->prepare("SELECT * FROM registration_exam_results WHERE registration_id IN ($placeholders)");
            $stmt->execute($candidateIds);
            foreach ($stmt->fetchAll() as $r) {
                $examResultsMap[$r['registration_id']][$r['stage_index']] = $r['status'];
            }
        }

        return $response->renderPage([
            'candidates' => $candidates,
            'programs' => $programs,
            'waves' => $waves,
            'selectedWaveId' => $waveIdFilter,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalCount' => $totalCount,
            'limit' => $limit,
            'waves_map' => $wavesMap,
            'exam_results_map' => $examResultsMap
        ], [
            'path' => '/admin/selection',
            'meta' => ['title' => 'Penilaian & Kelulusan PMB | ' . env('APP_NAME')]
        ]);
    }

    public function saveScoresAndStatus(Request $request, Response $response): RedirectResponse
    {
        if (!has_permission('manage_selection')) {
            return $response->redirect('/dashboard?error=Anda+tidak+memiliki+hak+akses+ke+halaman+ini.');
        }

        $regId = (int) $request->input('registration_id');
        $status = $request->input('status');
        $passedProgramId = $request->input('passed_program_id') !== '' ? (int) $request->input('passed_program_id') : null;
        $notes = $request->input('notes') ?: null;
        $stages = $request->input('stages');

        if (!$regId || !in_array($status, ['Pending', 'Lulus', 'Cadangan', 'Tidak Lulus'], true)) {
            return $response->redirect('/admin/selection?error=Masukan+penilaian+tidak+valid');
        }

        if (in_array($status, ['Lulus', 'Cadangan'], true) && !$passedProgramId) {
            return $response->redirect('/admin/selection?error=Program+Studi+Penerimaan+wajib+dipilih+jika+status+Lulus+atau+Cadangan');
        }

        $existing = $this->selectionResults->findByRegistrationId($regId);

        $data = [
            'registration_id' => $regId,
            'test_score' => null,
            'interview_score' => null,
            'interview_notes' => null,
            'status' => $status,
            'passed_program_id' => $passedProgramId,
            'notes' => $notes
        ];

        if ($existing) {
            $this->selectionResults->updateById($existing['id'], $data);
        } else {
            $this->selectionResults->insert($data);
        }

        $db = $this->registrations->getDb();
        if (is_array($stages)) {
            foreach ($stages as $stageIndex => $stageStatus) {
                if (in_array($stageStatus, ['Lulus', 'Tidak Lulus', 'Pending'], true)) {
                    $stmt = $db->prepare("SELECT * FROM registration_exam_results WHERE registration_id = :reg_id AND stage_index = :stage_index LIMIT 1");
                    $stmt->execute([
                        'reg_id' => $regId,
                        'stage_index' => $stageIndex
                    ]);
                    $existingStage = $stmt->fetch();
                    if ($existingStage) {
                        $stmt = $db->prepare("UPDATE registration_exam_results SET status = :status, updated_at = :now WHERE id = :id");
                        $stmt->execute([
                            'status' => $stageStatus,
                            'now' => date('Y-m-d H:i:s'),
                            'id' => $existingStage['id']
                        ]);
                    } else {
                        $stmt = $db->prepare("INSERT INTO registration_exam_results (registration_id, stage_index, study_program_id, status, created_at, updated_at) VALUES (:reg_id, :stage_index, NULL, :status, :created_at, :updated_at)");
                        $stmt->execute([
                            'reg_id' => $regId,
                            'stage_index' => $stageIndex,
                            'status' => $stageStatus,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
                    }
                }
            }
        }

        return $response->redirect('/admin/selection?success=Data+penilaian+dan+kelulusan+berhasil+disimpan');
    }

    public function saveExamStages(Request $request, Response $response): RedirectResponse
    {
        if (!has_permission('manage_selection')) {
            return $response->redirect('/dashboard?error=Anda+tidak+memiliki+hak+akses+ke+halaman+ini.');
        }

        $regId = (int) $request->input('registration_id');
        $stages = $request->input('stages');

        if (!$regId) {
            return $response->redirect('/admin/selection?error=Pendaftar+tidak+valid');
        }

        $db = $this->registrations->getDb();

        if (is_array($stages)) {
            foreach ($stages as $stageIndex => $stageStatus) {
                if (in_array($stageStatus, ['Lulus', 'Tidak Lulus', 'Pending'], true)) {
                    $stmt = $db->prepare("SELECT * FROM registration_exam_results WHERE registration_id = :reg_id AND stage_index = :stage_index LIMIT 1");
                    $stmt->execute([
                        'reg_id' => $regId,
                        'stage_index' => $stageIndex
                    ]);
                    $existingStage = $stmt->fetch();
                    if ($existingStage) {
                        $stmt = $db->prepare("UPDATE registration_exam_results SET status = :status, updated_at = :now WHERE id = :id");
                        $stmt->execute([
                            'status' => $stageStatus,
                            'now' => date('Y-m-d H:i:s'),
                            'id' => $existingStage['id']
                        ]);
                    } else {
                        $stmt = $db->prepare("INSERT INTO registration_exam_results (registration_id, stage_index, study_program_id, status, created_at, updated_at) VALUES (:reg_id, :stage_index, NULL, :status, :created_at, :updated_at)");
                        $stmt->execute([
                            'reg_id' => $regId,
                            'stage_index' => $stageIndex,
                            'status' => $stageStatus,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
                    }
                }
            }
        }

        return $response->redirect('/admin/selection?success=Penilaian+tahapan+ujian+berhasil+disimpan');
    }

    public function publishStatus(Request $request, Response $response): RedirectResponse
    {
        if (!has_permission('manage_selection')) {
            return $response->redirect('/dashboard?error=Anda+tidak+memiliki+hak+akses+ke+halaman+ini.');
        }

        $regId = (int) $request->input('registration_id');
        $isPublished = (int) $request->input('is_published');

        if (!$regId) {
            return $response->redirect('/admin/selection?error=Pendaftar+tidak+valid');
        }

        $existing = $this->selectionResults->findByRegistrationId($regId);
        if (!$existing) {
            $this->selectionResults->insert([
                'registration_id' => $regId,
                'status' => 'Pending',
                'is_published' => $isPublished
            ]);
        } else {
            $this->selectionResults->updateById($existing['id'], [
                'is_published' => $isPublished
            ]);
        }

        if ($isPublished === 1) {
            $registration = $this->registrations->find($regId);
            $selResult = $this->selectionResults->findByRegistrationId($regId);
            if ($registration && $selResult && $selResult['status'] !== 'Pending') {
                $userId = $registration['user_id'];
                $statusText = $selResult['status'];
                if ($statusText === 'Lulus') {
                    send_system_notification($userId, 'Selamat! Anda Dinyatakan Lolos Seleksi', 'Selamat! Anda dinyatakan Lulus Seleksi Utama Penerimaan Mahasiswa Baru Kampus Mandiri Kencana. Harap segera lakukan pendaftaran ulang.', 'success');
                    send_email_notification($userId, $registration['email'], 'Selamat! Anda Dinyatakan Lolos Seleksi PMB', 'Selamat! Anda dinyatakan Lulus Seleksi Utama Penerimaan Mahasiswa Baru Kampus Mandiri Kencana. Silakan login ke dashboard untuk mencetak Surat Kelulusan dan melakukan pendaftaran ulang.');
                } elseif ($statusText === 'Tidak Lulus') {
                    send_system_notification($userId, 'Hasil Seleksi PMB', 'Mohon maaf, Anda dinyatakan belum berhasil lolos seleksi penerimaan mahasiswa baru Kampus Mandiri Kencana tahun ini.', 'danger');
                    send_email_notification($userId, $registration['email'], 'Hasil Seleksi PMB', 'Mohon maaf, Anda dinyatakan belum berhasil lolos seleksi penerimaan mahasiswa baru Kampus Mandiri Kencana tahun ini. Terima kasih atas partisipasi Anda.');
                } elseif ($statusText === 'Cadangan') {
                    send_system_notification($userId, 'Hasil Seleksi PMB: Status Cadangan', 'Anda dinyatakan masuk ke dalam daftar Cadangan seleksi penerimaan mahasiswa baru Kampus Mandiri Kencana.', 'warning');
                    send_email_notification($userId, $registration['email'], 'Hasil Seleksi PMB: Status Cadangan', 'Anda dinyatakan masuk ke dalam daftar Cadangan seleksi penerimaan mahasiswa baru Kampus Mandiri Kencana. Harap pantau dashboard untuk pembaruan kuota.');
                }
            }
        }

        $msg = $isPublished === 1 ? 'diterbitkan' : 'ditarik';
        return $response->redirect('/admin/selection?success=Status+kelulusan+berhasil+' . $msg);
    }

    public function publishAll(Request $request, Response $response): RedirectResponse
    {
        if (!has_permission('manage_selection')) {
            return $response->redirect('/dashboard?error=Anda+tidak+memiliki+hak+akses+ke+halaman+ini.');
        }

        $isPublished = (int) $request->input('is_published');

        $db = $this->selectionResults->getDb();
        $stmt = $db->prepare("UPDATE selection_results SET is_published = :is_pub");
        $stmt->execute(['is_pub' => $isPublished]);

        if ($isPublished === 1) {
            $stmtRegs = $db->prepare("
                SELECT id FROM registrations 
                WHERE status != 'Draft' 
                AND id NOT IN (SELECT registration_id FROM selection_results)
            ");
            $stmtRegs->execute();
            $unscored = $stmtRegs->fetchAll();
            foreach ($unscored as $u) {
                $this->selectionResults->insert([
                    'registration_id' => $u['id'],
                    'status' => 'Pending',
                    'is_published' => 1
                ]);
            }

            $stmtAnnounce = $db->prepare("
                SELECT sr.*, r.full_name, r.email, r.user_id 
                FROM selection_results sr
                JOIN registrations r ON sr.registration_id = r.id
                WHERE sr.status != 'Pending'
            ");
            $stmtAnnounce->execute();
            $results = $stmtAnnounce->fetchAll();
            foreach ($results as $res) {
                $userId = $res['user_id'];
                $statusText = $res['status'];
                if ($statusText === 'Lulus') {
                    send_system_notification($userId, 'Selamat! Anda Dinyatakan Lolos Seleksi', 'Selamat! Anda dinyatakan Lulus Seleksi Utama Penerimaan Mahasiswa Baru Kampus Mandiri Kencana. Harap segera lakukan pendaftaran ulang.', 'success');
                    send_email_notification($userId, $res['email'], 'Selamat! Anda Dinyatakan Lolos Seleksi PMB', 'Selamat! Anda dinyatakan Lulus Seleksi Utama Penerimaan Mahasiswa Baru Kampus Mandiri Kencana. Silakan login ke dashboard untuk mencetak Surat Kelulusan dan melakukan pendaftaran ulang.');
                } elseif ($statusText === 'Tidak Lulus') {
                    send_system_notification($userId, 'Hasil Seleksi PMB', 'Mohon maaf, Anda dinyatakan belum berhasil lolos seleksi penerimaan mahasiswa baru Kampus Mandiri Kencana tahun ini.', 'danger');
                    send_email_notification($userId, $res['email'], 'Hasil Seleksi PMB', 'Mohon maaf, Anda dinyatakan belum berhasil lolos seleksi penerimaan mahasiswa baru Kampus Mandiri Kencana tahun ini. Terima kasih atas partisipasi Anda.');
                } elseif ($statusText === 'Cadangan') {
                    send_system_notification($userId, 'Hasil Seleksi PMB: Status Cadangan', 'Anda dinyatakan masuk ke dalam daftar Cadangan seleksi penerimaan mahasiswa baru Kampus Mandiri Kencana.', 'warning');
                    send_email_notification($userId, $res['email'], 'Hasil Seleksi PMB: Status Cadangan', 'Anda dinyatakan masuk ke dalam daftar Cadangan seleksi penerimaan mahasiswa baru Kampus Mandiri Kencana. Harap pantau dashboard untuk pembaruan kuota.');
                }
            }
        }

        $msg = $isPublished === 1 ? 'diterbitkan' : 'ditarik';
        return $response->redirect('/admin/selection?success=Semua+pengumuman+seleksi+berhasil+' . $msg);
    }
}
