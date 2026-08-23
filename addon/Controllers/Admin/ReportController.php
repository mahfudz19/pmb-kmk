<?php

namespace Addon\Controllers\Admin;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\View\View;
use App\Core\Http\RedirectResponse;
use App\Services\SessionService;
use Addon\Models\RegistrationModel;

class ReportController
{
    public function __construct(
        private SessionService $session,
        private RegistrationModel $registrations
    ) {}

    private function checkAccess(Response $response)
    {
        if (!$this->session->get('is_logged_in')) {
            return $response->redirect('/login');
        }
        if (!has_permission('manage_selection') && !has_permission('verify_document') && !has_permission('verify_payment') && !has_permission('manage_users')) {
            return $response->redirect('/dashboard?error=Anda+tidak+memiliki+hak+akses+ke+halaman+ini.');
        }
        return null;
    }

    public function index(Request $request, Response $response): View|RedirectResponse
    {
        if ($redirect = $this->checkAccess($response)) return $redirect;

        $waveId = $request->input('wave_id');
        $waveIdFilter = ($waveId !== '' && $waveId !== null) ? (int)$waveId : null;

        $db = $this->registrations->getDb();

        $stats = [];

        $regWhere = "";
        $regParams = [];
        if ($waveIdFilter !== null) {
            $regWhere = " WHERE wave_id = :wave_id ";
            $regParams['wave_id'] = $waveIdFilter;
        }

        $stmt = $db->prepare("SELECT COUNT(*) as total FROM registrations" . $regWhere);
        $stmt->execute($regParams);
        $stats['total_registrants'] = $stmt->fetch()['total'];

        $stmt = $db->prepare("SELECT status, COUNT(*) as count FROM registrations" . $regWhere . " GROUP BY status");
        $stmt->execute($regParams);
        $statusCounts = [];
        while ($row = $stmt->fetch()) {
            $statusCounts[$row['status']] = $row['count'];
        }
        $stats['status_counts'] = $statusCounts;

        $stmt = $db->prepare("SELECT gender, COUNT(*) as count FROM registrations" . $regWhere . " GROUP BY gender");
        $stmt->execute($regParams);
        $genderCounts = [];
        while ($row = $stmt->fetch()) {
            $genderCounts[$row['gender']] = $row['count'];
        }
        $stats['gender_counts'] = $genderCounts;

        $progWhere = "";
        $progParams = [];
        if ($waveIdFilter !== null) {
            $progWhere = " WHERE r.wave_id = :wave_id ";
            $progParams['wave_id'] = $waveIdFilter;
        }
        $stmt = $db->prepare("
            SELECT sp.name as program_name, COUNT(rp.id) as count
            FROM study_programs sp
            LEFT JOIN registration_programs rp ON rp.program1_id = sp.id
            LEFT JOIN registrations r ON rp.registration_id = r.id
            " . $progWhere . "
            GROUP BY sp.id, sp.name
            ORDER BY count DESC
        ");
        $stmt->execute($progParams);
        $stats['program_counts'] = $stmt->fetchAll();

        $payWhere = " WHERE rp.status = 'Approved' ";
        $payParams = [];
        if ($waveIdFilter !== null) {
            $payWhere .= " AND r.wave_id = :wave_id ";
            $payParams['wave_id'] = $waveIdFilter;
        }
        $stmt = $db->prepare("
            SELECT SUM(rp.amount) as total 
            FROM registration_payments rp
            JOIN registrations r ON rp.registration_id = r.id
            " . $payWhere . "
        ");
        $stmt->execute($payParams);
        $stats['total_registration_fees'] = $stmt->fetch()['total'] ?? 0;

        $reregWhere = " WHERE rr.status = 'Approved' ";
        $reregParams = [];
        if ($waveIdFilter !== null) {
            $reregWhere .= " AND r.wave_id = :wave_id ";
            $reregParams['wave_id'] = $waveIdFilter;
        }
        $stmt = $db->prepare("
            SELECT SUM(rr.payment_amount) as total 
            FROM re_registrations rr
            JOIN registrations r ON rr.registration_id = r.id
            " . $reregWhere . "
        ");
        $stmt->execute($reregParams);
        $stats['total_re_registration_fees'] = $stmt->fetch()['total'] ?? 0;

        $unionParams = [];
        $payUnionWhere = "";
        $reregUnionWhere = "";
        if ($waveIdFilter !== null) {
            $payUnionWhere = " WHERE r.wave_id = :wave_id ";
            $reregUnionWhere = " WHERE r.wave_id = :wave_id ";
            $unionParams['wave_id'] = $waveIdFilter;
        }
        $stmt = $db->prepare("
            (SELECT rp.id, r.full_name, rp.bank_name, rp.amount, rp.status, rp.created_at, 'Pendaftaran' as type
             FROM registration_payments rp
             JOIN registrations r ON rp.registration_id = r.id
             " . $payUnionWhere . ")
            UNION ALL
            (SELECT rr.id, r.full_name, 'Transfer' as bank_name, rr.payment_amount as amount, rr.status, rr.created_at, 'Daftar Ulang' as type
             FROM re_registrations rr
             JOIN registrations r ON rr.registration_id = r.id
             " . $reregUnionWhere . ")
            ORDER BY created_at DESC LIMIT 10
        ");
        $stmt->execute($unionParams);
        $stats['latest_transactions'] = $stmt->fetchAll();

        $selWhere = "";
        $selParams = [];
        if ($waveIdFilter !== null) {
            $selWhere = " JOIN registrations r ON sr.registration_id = r.id WHERE r.wave_id = :wave_id ";
            $selParams['wave_id'] = $waveIdFilter;
        }
        $stmt = $db->prepare("SELECT COUNT(sr.id) as total FROM selection_results sr" . $selWhere);
        $stmt->execute($selParams);
        $stats['total_exams'] = $stmt->fetch()['total'];

        $stmt = $db->prepare("SELECT sr.status as decision, COUNT(sr.id) as count FROM selection_results sr" . $selWhere . " GROUP BY sr.status");
        $stmt->execute($selParams);
        $selectionCounts = [];
        while ($row = $stmt->fetch()) {
            $selectionCounts[$row['decision']] = $row['count'];
        }
        $stats['selection_counts'] = $selectionCounts;

        $stmt = $db->prepare("SELECT AVG(sr.test_score) as avg_test, AVG(sr.interview_score) as avg_interview FROM selection_results sr" . $selWhere);
        $stmt->execute($selParams);
        $avgScores = $stmt->fetch();
        $stats['avg_test_score'] = $avgScores['avg_test'] ?? 0;
        $stats['avg_interview_score'] = $avgScores['avg_interview'] ?? 0;

        $rrWhere = "";
        $rrParams = [];
        if ($waveIdFilter !== null) {
            $rrWhere = " JOIN registrations r ON rr.registration_id = r.id WHERE r.wave_id = :wave_id ";
            $rrParams['wave_id'] = $waveIdFilter;
        }
        $stmt = $db->prepare("SELECT rr.status, COUNT(rr.id) as count FROM re_registrations rr" . $rrWhere . " GROUP BY rr.status");
        $stmt->execute($rrParams);
        $reRegCounts = [];
        while ($row = $stmt->fetch()) {
            $reRegCounts[$row['status']] = $row['count'];
        }
        $stats['rereg_counts'] = $reRegCounts;

        $stmt = $db->prepare("
            SELECT rr.*, rr.payment_amount as amount, r.full_name, r.email, r.phone, sp.name as passed_program_name
            FROM re_registrations rr
            JOIN registrations r ON rr.registration_id = r.id
            LEFT JOIN selection_results sr ON sr.registration_id = r.id
            LEFT JOIN study_programs sp ON sr.passed_program_id = sp.id
            " . ($waveIdFilter !== null ? " WHERE r.wave_id = :wave_id " : "") . "
            ORDER BY rr.updated_at DESC LIMIT 10
        ");
        $stmt->execute($waveIdFilter !== null ? ['wave_id' => $waveIdFilter] : []);
        $stats['latest_reregistrations'] = $stmt->fetchAll();

        $stmt = $db->prepare("
            SELECT nh.*, r.full_name
            FROM notification_history nh
            LEFT JOIN registrations r ON nh.user_id = r.user_id
            " . ($waveIdFilter !== null ? " WHERE r.wave_id = :wave_id " : "") . "
            ORDER BY nh.created_at DESC LIMIT 50
        ");
        $stmt->execute($waveIdFilter !== null ? ['wave_id' => $waveIdFilter] : []);
        $stats['notification_history'] = $stmt->fetchAll();

        $stmt = $db->prepare("
            SELECT * FROM audit_logs
            ORDER BY created_at DESC LIMIT 100
        ");
        $stmt->execute();
        $stats['audit_logs'] = $stmt->fetchAll();

        $stmtWaves = $db->prepare("SELECT * FROM waves");
        $stmtWaves->execute();
        $waves = $stmtWaves->fetchAll() ?: [];

        return $response->renderPage([
            'stats' => $stats,
            'waves' => $waves,
            'selectedWaveId' => $waveIdFilter
        ], [
            'path' => '/admin/reports/index',
            'meta' => ['title' => 'Laporan & Statistik | ' . env('APP_NAME', 'Mazu')]
        ]);
    }

    public function exportFinance(Request $request, Response $response)
    {
        if ($redirect = $this->checkAccess($response)) return $redirect;

        $waveId = $request->input('wave_id');
        $waveIdFilter = ($waveId !== '' && $waveId !== null) ? (int)$waveId : null;

        $db = $this->registrations->getDb();

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="laporan_keuangan_pmb_' . date('Ymd_His') . '.csv"');

        $output = fopen('php://output', 'w');

        fputcsv($output, ['No', 'Nama Pendaftar', 'Jenis Pembayaran', 'Metode/Bank', 'Nominal', 'Status', 'Tanggal Transaksi']);

        $unionParams = [];
        $payUnionWhere = "";
        $reregUnionWhere = "";
        if ($waveIdFilter !== null) {
            $payUnionWhere = " WHERE r.wave_id = :wave_id ";
            $reregUnionWhere = " WHERE r.wave_id = :wave_id ";
            $unionParams['wave_id'] = $waveIdFilter;
        }

        $stmt = $db->prepare("
            (SELECT r.full_name, 'Pendaftaran' as type, rp.bank_name, rp.amount, rp.status, rp.created_at
             FROM registration_payments rp
             JOIN registrations r ON rp.registration_id = r.id
             " . $payUnionWhere . ")
            UNION ALL
            (SELECT r.full_name, 'Daftar Ulang' as type, 'Transfer' as bank_name, rr.payment_amount as amount, rr.status, rr.created_at
             FROM re_registrations rr
             JOIN registrations r ON rr.registration_id = r.id
             " . $reregUnionWhere . ")
            ORDER BY created_at DESC
        ");
        $stmt->execute($unionParams);

        $no = 1;
        while ($row = $stmt->fetch()) {
            fputcsv($output, [
                $no++,
                $row['full_name'],
                $row['type'],
                $row['bank_name'],
                $row['amount'],
                $row['status'],
                $row['created_at']
            ]);
        }

        fclose($output);
        exit;
    }

    public function exportSelection(Request $request, Response $response)
    {
        if ($redirect = $this->checkAccess($response)) return $redirect;

        $waveId = $request->input('wave_id');
        $waveIdFilter = ($waveId !== '' && $waveId !== null) ? (int)$waveId : null;

        $db = $this->registrations->getDb();

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="laporan_hasil_seleksi_' . date('Ymd_His') . '.csv"');

        $output = fopen('php://output', 'w');

        fputcsv($output, ['No', 'Nama Lengkap', 'NIK', 'NISN', 'Nilai CBT', 'Nilai Wawancara', 'Hasil Keputusan', 'Program Studi Lulus', 'Catatan']);

        $whereSql = "";
        $params = [];
        if ($waveIdFilter !== null) {
            $whereSql = " WHERE r.wave_id = :wave_id ";
            $params['wave_id'] = $waveIdFilter;
        }

        $stmt = $db->prepare("
            SELECT sr.*, r.full_name, r.nik, r.nisn, sp.name as passed_program_name
            FROM selection_results sr
            JOIN registrations r ON sr.registration_id = r.id
            LEFT JOIN study_programs sp ON sr.passed_program_id = sp.id
            " . $whereSql . "
            ORDER BY sr.updated_at DESC
        ");
        $stmt->execute($params);

        $no = 1;
        while ($row = $stmt->fetch()) {
            fputcsv($output, [
                $no++,
                $row['full_name'],
                "'" . $row['nik'],
                "'" . $row['nisn'],
                $row['test_score'] ?? '-',
                $row['interview_score'] ?? '-',
                $row['status'],
                $row['passed_program_name'] ?? '-',
                $row['notes'] ?? '-'
            ]);
        }

        fclose($output);
        exit;
    }

    public function exportReRegistration(Request $request, Response $response)
    {
        if ($redirect = $this->checkAccess($response)) return $redirect;

        $waveId = $request->input('wave_id');
        $waveIdFilter = ($waveId !== '' && $waveId !== null) ? (int)$waveId : null;

        $db = $this->registrations->getDb();

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="laporan_daftar_ulang_' . date('Ymd_His') . '.csv"');

        $output = fopen('php://output', 'w');

        fputcsv($output, ['No', 'Nama Lengkap', 'Email', 'No Telepon', 'Program Studi', 'Nominal Pembayaran', 'Status Verifikasi', 'Tanggal Update']);

        $whereSql = "";
        $params = [];
        if ($waveIdFilter !== null) {
            $whereSql = " WHERE r.wave_id = :wave_id ";
            $params['wave_id'] = $waveIdFilter;
        }

        $stmt = $db->prepare("
            SELECT rr.*, rr.payment_amount as amount, r.full_name, r.email, r.phone, sp.name as passed_program_name
            FROM re_registrations rr
            JOIN registrations r ON rr.registration_id = r.id
            LEFT JOIN selection_results sr ON sr.registration_id = r.id
            LEFT JOIN study_programs sp ON sr.passed_program_id = sp.id
            " . $whereSql . "
            ORDER BY rr.updated_at DESC
        ");
        $stmt->execute($params);

        $no = 1;
        while ($row = $stmt->fetch()) {
            fputcsv($output, [
                $no++,
                $row['full_name'],
                $row['email'],
                "'" . $row['phone'],
                $row['passed_program_name'] ?? '-',
                $row['amount'],
                $row['status'],
                $row['updated_at']
            ]);
        }

        fclose($output);
        exit;
    }
}
