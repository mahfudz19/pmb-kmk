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

        $page = (int) ($request->input('page') ?: 1);
        if ($page < 1) $page = 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $db = $this->registrations->getDb();

        $stmtCount = $db->prepare("
            SELECT COUNT(*) as count 
            FROM registrations r
            JOIN users u ON r.user_id = u.id
            WHERE r.status != 'Draft'
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
            SELECT r.*, u.email, 
                   sr.test_score, sr.interview_score, sr.interview_notes, 
                   sr.status as selection_status, sr.passed_program_id, sr.notes as selection_notes,
                   sr.is_published
            FROM registrations r
            JOIN users u ON r.user_id = u.id
            LEFT JOIN selection_results sr ON r.id = sr.registration_id
            WHERE r.status != 'Draft'
            ORDER BY r.updated_at DESC
            LIMIT " . $limit . " OFFSET " . $offset . "
        ");
        $stmt->execute();
        $candidates = $stmt->fetchAll();

        $programs = $this->studyPrograms->all();

        return $response->renderPage([
            'candidates' => $candidates,
            'programs' => $programs,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalCount' => $totalCount,
            'limit' => $limit
        ], [
            'path' => '/admin/selection',
            'meta' => ['title' => 'Penilaian & Kelulusan PMB | ' . env('APP_NAME')]
        ]);
    }

    public function updateQuota(Request $request, Response $response): RedirectResponse
    {
        if (!has_permission('manage_selection')) {
            return $response->redirect('/dashboard?error=Anda+tidak+memiliki+hak+akses+ke+halaman+ini.');
        }

        $programId = (int) $request->input('program_id');
        $quota = (int) $request->input('quota');

        if (!$programId || $quota < 0) {
            return $response->redirect('/admin/selection?error=Input+daya+tampung+tidak+valid');
        }

        $this->studyPrograms->updateById($programId, [
            'quota' => $quota
        ]);

        return $response->redirect('/admin/selection?success=Kuota+program+studi+berhasil+diperbarui&tab=quota');
    }

    public function saveScoresAndStatus(Request $request, Response $response): RedirectResponse
    {
        if (!has_permission('manage_selection')) {
            return $response->redirect('/dashboard?error=Anda+tidak+memiliki+hak+akses+ke+halaman+ini.');
        }

        $regId = (int) $request->input('registration_id');
        $testScore = $request->input('test_score') !== '' ? (float) $request->input('test_score') : null;
        $interviewScore = $request->input('interview_score') !== '' ? (float) $request->input('interview_score') : null;
        $interviewNotes = $request->input('interview_notes') ?: null;
        $status = $request->input('status');
        $passedProgramId = $request->input('passed_program_id') !== '' ? (int) $request->input('passed_program_id') : null;
        $notes = $request->input('notes') ?: null;

        if (!$regId || !in_array($status, ['Pending', 'Lulus', 'Cadangan', 'Tidak Lulus'], true)) {
            return $response->redirect('/admin/selection?error=Masukan+penilaian+tidak+valid');
        }

        $existing = $this->selectionResults->findByRegistrationId($regId);

        $data = [
            'registration_id' => $regId,
            'test_score' => $testScore,
            'interview_score' => $interviewScore,
            'interview_notes' => $interviewNotes,
            'status' => $status,
            'passed_program_id' => $passedProgramId,
            'notes' => $notes
        ];

        if ($existing) {
            $this->selectionResults->updateById($existing['id'], $data);
        } else {
            $this->selectionResults->insert($data);
        }

        return $response->redirect('/admin/selection?success=Data+penilaian+dan+kelulusan+berhasil+disimpan');
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
