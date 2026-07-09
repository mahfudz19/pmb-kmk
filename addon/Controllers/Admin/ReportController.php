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

        $db = $this->registrations->getDb();

        $stats = [];

        $stmt = $db->prepare("SELECT COUNT(*) as total FROM registrations");
        $stmt->execute();
        $stats['total_registrants'] = $stmt->fetch()['total'];

        $stmt = $db->prepare("SELECT status, COUNT(*) as count FROM registrations GROUP BY status");
        $stmt->execute();
        $statusCounts = [];
        while ($row = $stmt->fetch()) {
            $statusCounts[$row['status']] = $row['count'];
        }
        $stats['status_counts'] = $statusCounts;

        $stmt = $db->prepare("SELECT gender, COUNT(*) as count FROM registrations GROUP BY gender");
        $stmt->execute();
        $genderCounts = [];
        while ($row = $stmt->fetch()) {
            $genderCounts[$row['gender']] = $row['count'];
        }
        $stats['gender_counts'] = $genderCounts;

        $stmt = $db->prepare("
            SELECT sp.name as program_name, COUNT(rp.id) as count
            FROM study_programs sp
            LEFT JOIN registration_programs rp ON rp.program1_id = sp.id
            GROUP BY sp.id, sp.name
            ORDER BY count DESC
        ");
        $stmt->execute();
        $stats['program_counts'] = $stmt->fetchAll();

        $stmt = $db->prepare("SELECT SUM(amount) as total FROM registration_payments WHERE status = 'Approved'");
        $stmt->execute();
        $stats['total_registration_fees'] = $stmt->fetch()['total'] ?? 0;

        $stmt = $db->prepare("SELECT SUM(payment_amount) as total FROM re_registrations WHERE status = 'Approved'");
        $stmt->execute();
        $stats['total_re_registration_fees'] = $stmt->fetch()['total'] ?? 0;

        $stmt = $db->prepare("
            (SELECT rp.id, r.full_name, rp.bank_name, rp.amount, rp.status, rp.created_at, 'Pendaftaran' as type
             FROM registration_payments rp
             JOIN registrations r ON rp.registration_id = r.id)
            UNION ALL
            (SELECT rr.id, r.full_name, 'Transfer' as bank_name, rr.payment_amount as amount, rr.status, rr.created_at, 'Daftar Ulang' as type
             FROM re_registrations rr
             JOIN registrations r ON rr.registration_id = r.id)
            ORDER BY created_at DESC LIMIT 10
        ");
        $stmt->execute();
        $stats['latest_transactions'] = $stmt->fetchAll();

        $stmt = $db->prepare("SELECT COUNT(*) as total FROM selection_results");
        $stmt->execute();
        $stats['total_exams'] = $stmt->fetch()['total'];

        $stmt = $db->prepare("SELECT status as decision, COUNT(*) as count FROM selection_results GROUP BY status");
        $stmt->execute();
        $selectionCounts = [];
        while ($row = $stmt->fetch()) {
            $selectionCounts[$row['decision']] = $row['count'];
        }
        $stats['selection_counts'] = $selectionCounts;

        $stmt = $db->prepare("SELECT AVG(test_score) as avg_test, AVG(interview_score) as avg_interview FROM selection_results");
        $stmt->execute();
        $avgScores = $stmt->fetch();
        $stats['avg_test_score'] = $avgScores['avg_test'] ?? 0;
        $stats['avg_interview_score'] = $avgScores['avg_interview'] ?? 0;

        $stmt = $db->prepare("SELECT status, COUNT(*) as count FROM re_registrations GROUP BY status");
        $stmt->execute();
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
            ORDER BY rr.updated_at DESC LIMIT 10
        ");
        $stmt->execute();
        $stats['latest_reregistrations'] = $stmt->fetchAll();

        $stmt = $db->prepare("
            SELECT nh.*, r.full_name
            FROM notification_history nh
            LEFT JOIN registrations r ON nh.user_id = r.user_id
            ORDER BY nh.created_at DESC LIMIT 50
        ");
        $stmt->execute();
        $stats['notification_history'] = $stmt->fetchAll();

        $stmt = $db->prepare("
            SELECT * FROM audit_logs
            ORDER BY created_at DESC LIMIT 100
        ");
        $stmt->execute();
        $stats['audit_logs'] = $stmt->fetchAll();

        return $response->renderPage([
            'stats' => $stats
        ], [
            'path' => '/admin/reports/index',
            'meta' => ['title' => 'Laporan & Statistik | ' . env('APP_NAME', 'Mazu')]
        ]);
    }

    public function exportFinance(Request $request, Response $response)
    {
        if ($redirect = $this->checkAccess($response)) return $redirect;

        $db = $this->registrations->getDb();

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="laporan_keuangan_pmb_' . date('Ymd_His') . '.csv"');

        $output = fopen('php://output', 'w');

        fputcsv($output, ['No', 'Nama Pendaftar', 'Jenis Pembayaran', 'Metode/Bank', 'Nominal', 'Status', 'Tanggal Transaksi']);

        $stmt = $db->prepare("
            (SELECT r.full_name, 'Pendaftaran' as type, rp.bank_name, rp.amount, rp.status, rp.created_at
             FROM registration_payments rp
             JOIN registrations r ON rp.registration_id = r.id)
            UNION ALL
            (SELECT r.full_name, 'Daftar Ulang' as type, 'Transfer' as bank_name, rr.payment_amount as amount, rr.status, rr.created_at
             FROM re_registrations rr
             JOIN registrations r ON rr.registration_id = r.id)
            ORDER BY created_at DESC
        ");
        $stmt->execute();

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

        $db = $this->registrations->getDb();

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="laporan_hasil_seleksi_' . date('Ymd_His') . '.csv"');

        $output = fopen('php://output', 'w');

        fputcsv($output, ['No', 'Nama Lengkap', 'NIK', 'NISN', 'Nilai CBT', 'Nilai Wawancara', 'Hasil Keputusan', 'Program Studi Lulus', 'Catatan']);

        $stmt = $db->prepare("
            SELECT sr.*, r.full_name, r.nik, r.nisn, sp.name as passed_program_name
            FROM selection_results sr
            JOIN registrations r ON sr.registration_id = r.id
            LEFT JOIN study_programs sp ON sr.passed_program_id = sp.id
            ORDER BY sr.updated_at DESC
        ");
        $stmt->execute();

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

        $db = $this->registrations->getDb();

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="laporan_daftar_ulang_' . date('Ymd_His') . '.csv"');

        $output = fopen('php://output', 'w');

        fputcsv($output, ['No', 'Nama Lengkap', 'Email', 'No Telepon', 'Program Studi', 'Nominal Pembayaran', 'Status Verifikasi', 'Tanggal Update']);

        $stmt = $db->prepare("
            SELECT rr.*, rr.payment_amount as amount, r.full_name, r.email, r.phone, sp.name as passed_program_name
            FROM re_registrations rr
            JOIN registrations r ON rr.registration_id = r.id
            LEFT JOIN selection_results sr ON sr.registration_id = r.id
            LEFT JOIN study_programs sp ON sr.passed_program_id = sp.id
            ORDER BY rr.updated_at DESC
        ");
        $stmt->execute();

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
