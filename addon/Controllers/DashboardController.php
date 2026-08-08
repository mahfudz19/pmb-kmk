<?php

namespace Addon\Controllers;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\View\View;
use App\Core\Http\RedirectResponse;
use App\Services\SessionService;

use Addon\Models\RegistrationModel;
use Addon\Models\RegistrationDocumentModel;
use Addon\Models\DocumentTypeModel;
use Addon\Models\RegistrationPaymentModel;
use Addon\Models\AnnouncementModel;
use Addon\Models\ReRegistrationModel;
use Addon\Models\SelectionResultModel;

class DashboardController
{
    public function __construct(
        private SessionService $session,
        private RegistrationModel $registrations,
        private RegistrationDocumentModel $documents,
        private DocumentTypeModel $documentTypes,
        private RegistrationPaymentModel $payments,
        private SelectionResultModel $selectionResults,
        private AnnouncementModel $announcements,
        private ReRegistrationModel $reRegistrations
    ) {}

    public function index(Request $request, Response $response): View | RedirectResponse
    {
        if (!$this->session->get('is_logged_in')) {
            return $response->redirect('/login');
        }

        $role = $this->session->get('auth.user_role');

        if ($role === 'admin' || has_any_permission(['manage_users', 'verify_payment', 'verify_document', 'manage_selection', 'manage_settings'])) {
            $db = $this->registrations->getDb();
            
            $stmt = $db->prepare("SELECT COUNT(*) as total FROM registrations");
            $stmt->execute();
            $totalApplicants = $stmt->fetch()['total'] ?? 0;

            $stmt = $db->prepare("SELECT COUNT(*) as total FROM registration_payments WHERE status = 'Approved'");
            $stmt->execute();
            $totalPayments = $stmt->fetch()['total'] ?? 0;

            $stmt = $db->prepare("SELECT COUNT(*) as total FROM registrations WHERE status = 'Verified'");
            $stmt->execute();
            $totalVerifications = $stmt->fetch()['total'] ?? 0;

            $stmt = $db->prepare("SELECT COUNT(*) as total FROM selection_results WHERE status = 'Lulus'");
            $stmt->execute();
            $totalAccepted = $stmt->fetch()['total'] ?? 0;

            $stats = [
                'total_applicants' => $totalApplicants,
                'total_payments' => $totalPayments,
                'total_verifications' => $totalVerifications,
                'total_accepted' => $totalAccepted,
            ];

            $stmt = $db->prepare("
                SELECT DATE(created_at) as date, COUNT(*) as count 
                FROM registrations 
                WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
                GROUP BY DATE(created_at)
                ORDER BY DATE(created_at) ASC
            ");
            $stmt->execute();
            $rawTrend = $stmt->fetchAll();

            $trendData = [];
            $trendLabels = [];
            $daysIndo = [
                'Mon' => 'Senin', 'Tue' => 'Selasa', 'Wed' => 'Rabu', 
                'Thu' => 'Kamis', 'Fri' => 'Jumat', 'Sat' => 'Sabtu', 'Sun' => 'Minggu'
            ];
            for ($i = 6; $i >= 0; $i--) {
                $date = date('Y-m-d', strtotime("-$i days"));
                $dayName = date('D', strtotime($date));
                $trendLabels[] = $daysIndo[$dayName] ?? $dayName;
                
                $count = 0;
                foreach ($rawTrend as $t) {
                    if ($t['date'] === $date) {
                        $count = (int)$t['count'];
                        break;
                    }
                }
                $trendData[] = $count;
            }

            $stmt = $db->prepare("
                SELECT sp.name as program_name, COUNT(rp.id) as count
                FROM study_programs sp
                LEFT JOIN registration_programs rp ON rp.program1_id = sp.id
                GROUP BY sp.id, sp.name
                ORDER BY count DESC
                LIMIT 5
            ");
            $stmt->execute();
            $rawPrograms = $stmt->fetchAll();
            
            $programLabels = [];
            $programData = [];
            foreach ($rawPrograms as $p) {
                $programLabels[] = $p['program_name'];
                $programData[] = (int)$p['count'];
            }

            $stmt = $db->prepare("
                SELECT * FROM audit_logs 
                ORDER BY created_at DESC 
                LIMIT 5
            ");
            $stmt->execute();
            $recentActivities = $stmt->fetchAll();

            return $response->renderPage([
                'stats' => $stats,
                'trend_labels' => $trendLabels,
                'trend_data' => $trendData,
                'program_labels' => $programLabels,
                'program_data' => $programData,
                'recent_activities' => $recentActivities
            ], [
                'path' => '/dashboard/admin',
                'meta' => ['title' => 'Admin Dashboard | ' . env('APP_NAME')]
            ]);
        }

        $userId = $this->session->get('auth.user_id');
        $registration = $this->registrations->findByUserId($userId);

        $db = $this->registrations->getDb();
        $stmt = $db->prepare("SELECT * FROM waves WHERE is_active = 1");
        $stmt->execute();
        $activeWaves = $stmt->fetchAll() ?: [];

        if ($userId) {
            $stmt = $db->prepare("SELECT wave_id FROM registrations WHERE user_id = :user_id AND wave_id IS NOT NULL");
            $stmt->execute(['user_id' => $userId]);
            $registeredWaveIds = $stmt->fetchAll(\PDO::FETCH_COLUMN) ?: [];
            $activeWaves = array_values(array_filter($activeWaves, function($w) use ($registeredWaveIds) {
                return !in_array((int)$w['id'], array_map('intval', $registeredWaveIds));
            }));
        }

        $wave = null;
        if ($registration && $registration['wave_id']) {
            $stmt = $db->prepare("SELECT * FROM waves WHERE id = :wave_id LIMIT 1");
            $stmt->execute(['wave_id' => $registration['wave_id']]);
            $wave = $stmt->fetch() ?: null;
        }

        $state = $this->session->get('registration_state') ?? 'belum_daftar';

        $uploadedDocs = [];
        $documentTypesList = [];
        $payment = null;
        $selectionResult = null;
        $passedProgram = null;
        $waveStudyProgram = null;
        $activePaymentAccount = null;
        $examResults = [];
        $regProgram = null;
        $requiredDocs = [];

        if ($registration) {
            $db = $this->registrations->getDb();
            $stmt = $db->prepare("SELECT * FROM registration_programs WHERE registration_id = :id LIMIT 1");
            $stmt->execute(['id' => $registration['id']]);
            $regProgram = $stmt->fetch() ?: null;

             $waveStudyPrograms = [];
             if ($regProgram && $registration['wave_id']) {
                 $stmt = $db->prepare("SELECT * FROM wave_study_programs WHERE wave_id = :wave_id AND study_program_id = :prodi_id LIMIT 1");
                 $stmt->execute([
                     'wave_id' => $registration['wave_id'],
                     'prodi_id' => $regProgram['program1_id']
                 ]);
                 $waveStudyProgram = $stmt->fetch() ?: null;

                 $prodiIds = [];
                 if (!empty($regProgram['program1_id'])) $prodiIds[] = (int)$regProgram['program1_id'];
                 if (!empty($regProgram['program2_id'])) $prodiIds[] = (int)$regProgram['program2_id'];
                 if (!empty($regProgram['program3_id'])) $prodiIds[] = (int)$regProgram['program3_id'];
                 $prodiIds = array_unique(array_filter($prodiIds));

                 if (!empty($prodiIds)) {
                     $placeholders = implode(',', array_fill(0, count($prodiIds), '?'));
                     $stmt = $db->prepare("SELECT * FROM wave_study_programs WHERE wave_id = ? AND study_program_id IN ($placeholders)");
                     $stmt->execute(array_merge([$registration['wave_id']], $prodiIds));
                     $waveStudyPrograms = $stmt->fetchAll() ?: [];
                 }

                  $prodiNamesMap = [];
                  if (!empty($prodiIds)) {
                      $placeholders = implode(',', array_fill(0, count($prodiIds), '?'));
                      $stmtProdis = $db->prepare("SELECT id, name FROM study_programs WHERE id IN ($placeholders)");
                      $stmtProdis->execute($prodiIds);
                      foreach ($stmtProdis->fetchAll() as $p) {
                          $prodiNamesMap[$p['id']] = $p['name'];
                      }
                  }

                  foreach ($waveStudyPrograms as &$wsp) {
                      $wsp['study_program_name'] = $prodiNamesMap[$wsp['study_program_id']] ?? '';
                  }
                  unset($wsp);

                   $uniqueDocsMap = [];
                   foreach ($waveStudyPrograms as $wsp) {
                       $prodiId = (int)$wsp['study_program_id'];
                       $prodiName = $prodiNamesMap[$prodiId] ?? '';
                       $reqDocs = json_decode($wsp['required_documents'] ?? '[]', true) ?: [];
                       foreach ($reqDocs as $rd) {
                           if (isset($rd['document_type_id'])) {
                               $dtId = (int)$rd['document_type_id'];
                               if (!isset($uniqueDocsMap[$dtId])) {
                                   $uniqueDocsMap[$dtId] = [
                                       'document_type_id' => $dtId,
                                       'name' => preg_replace('/ \(Prodi: .*\)$/', '', $rd['name']),
                                       'prodi_names' => [$prodiName],
                                       'descriptions' => [$prodiName => $rd['description'] ?? '']
                                   ];
                               } else {
                                   if (!in_array($prodiName, $uniqueDocsMap[$dtId]['prodi_names'])) {
                                       $uniqueDocsMap[$dtId]['prodi_names'][] = $prodiName;
                                   }
                                   $uniqueDocsMap[$dtId]['descriptions'][$prodiName] = $rd['description'] ?? '';
                               }
                           }
                       }
                   }
                   $requiredDocs = array_values($uniqueDocsMap);
              }

            $stmt = $db->prepare("SELECT * FROM registration_exam_results WHERE registration_id = :reg_id ORDER BY stage_index ASC");
            $stmt->execute(['reg_id' => $registration['id']]);
            $examResults = $stmt->fetchAll() ?: [];

            $stmt = $db->prepare("SELECT * FROM payment_accounts WHERE is_active = 1 LIMIT 1");
            $stmt->execute();
            $activePaymentAccount = $stmt->fetch() ?: null;

            $documentTypesList = $this->documentTypes->all();
            $docs = $this->documents->findByRegistrationId($registration['id']);
            foreach ($docs as $d) {
                $key = $d['document_type_id'] . '_' . ($d['study_program_id'] ?? 'global');
                $uploadedDocs[$key] = $d;
            }

            $payment = $this->payments->findByRegistrationId($registration['id']);
            $selectionResult = $this->selectionResults->findByRegistrationId($registration['id']);
            if ($selectionResult && $selectionResult['passed_program_id']) {
                $stmt = $this->registrations->getDb()->prepare("SELECT * FROM study_programs WHERE id = :id LIMIT 1");
                $stmt->execute(['id' => $selectionResult['passed_program_id']]);
                $passedProgram = $stmt->fetch() ?: null;
            }

            if (empty($registration['wave_id'])) {
                $state = 'belum_daftar';
            } else if ($registration['status'] === 'Draft') {
                $state = 'draft';
            } else if ($registration['status'] === 'Submitted') {
                if (!$payment) {
                    $state = 'belum_bayar';
                } else if ($payment['status'] === 'Pending') {
                    $state = 'verifikasi_pembayaran';
                } else if ($payment['status'] === 'Rejected') {
                    $state = 'belum_bayar';
                } else if ($payment['status'] === 'Approved') {
                    $allRequiredUploaded = true;
                    $allRequiredApproved = true;

                    $requiredDocKeys = [];
                    foreach ($waveStudyPrograms as $wsp) {
                        $reqDocs = json_decode($wsp['required_documents'] ?? '[]', true) ?: [];
                        foreach ($reqDocs as $rd) {
                            if (isset($rd['document_type_id'])) {
                                $requiredDocKeys[] = (int)$rd['document_type_id'] . '_global';
                            }
                        }
                    }
                    $requiredDocKeys = array_unique($requiredDocKeys);

                    foreach ($requiredDocKeys as $key) {
                        $doc = $uploadedDocs[$key] ?? null;
                        if (!$doc) {
                            $allRequiredUploaded = false;
                            $allRequiredApproved = false;
                        } else {
                            if ($doc['status'] !== 'Approved') {
                                $allRequiredApproved = false;
                            }
                        }
                    }

                    if (!$allRequiredUploaded) {
                        $state = 'upload_berkas';
                    } else if (!$allRequiredApproved) {
                        $state = 'verifikasi_berkas';
                    } else {
                        $state = 'ujian_seleksi';
                        if ($selectionResult && (int)$selectionResult['is_published'] === 1) {
                            if ($selectionResult['status'] === 'Lulus') {
                                $state = 'lolos';
                            } else if ($selectionResult['status'] === 'Tidak Lulus') {
                                $state = 'tidak_lolos';
                            } else if ($selectionResult['status'] === 'Cadangan') {
                                $state = 'lolos';
                            }
                        }
                    }
                }
            }
        } else {
            $state = 'belum_daftar';
        }

        $reReg = $registration ? $this->reRegistrations->findByRegistrationId($registration['id']) : null;
        $activeAnnouncement = $this->announcements->getActive();

        $history = [];
        if ($registration) {
            $allRegs = $this->registrations->findAllByUserId($userId);
            if (count($allRegs) > 1) {
                $historyRegs = array_slice($allRegs, 1);
                foreach ($historyRegs as $hr) {
                    $stmt = $db->prepare("SELECT * FROM waves WHERE id = :id LIMIT 1");
                    $stmt->execute(['id' => $hr['wave_id']]);
                    $w = $stmt->fetch();

                    $stmt = $db->prepare("SELECT * FROM selection_results WHERE registration_id = :id LIMIT 1");
                    $stmt->execute(['id' => $hr['id']]);
                    $sel = $stmt->fetch();

                    $passedProdi = '-';
                    if ($sel && $sel['passed_program_id']) {
                        $stmt = $db->prepare("SELECT * FROM study_programs WHERE id = :id LIMIT 1");
                        $stmt->execute(['id' => $sel['passed_program_id']]);
                        $prodi = $stmt->fetch();
                        if ($prodi) {
                            $passedProdi = $prodi['name'];
                        }
                    }

                    $history[] = [
                        'registration' => $hr,
                        'wave' => $w,
                        'selection' => $sel,
                        'passed_program_name' => $passedProdi
                    ];
                }
            }
        }

        return $response->renderPage([
            'state' => $state,
            'registration' => $registration,
            'document_types' => $documentTypesList,
            'uploaded_docs' => $uploadedDocs,
            'payment' => $payment,
            'selection_result' => $selectionResult,
            'passed_program' => $passedProgram,
            'active_announcement' => $activeAnnouncement,
            're_registration' => $reReg,
            'wave_study_program' => $waveStudyProgram,
            'wave_study_programs' => $waveStudyPrograms ?? [],
            'required_docs' => $requiredDocs,
            'active_payment_account' => $activePaymentAccount,
            'exam_results' => $examResults,
            'active_waves' => $activeWaves,
            'wave' => $wave,
            'history' => $history
        ], [
            'path' => '/dashboard/student',
            'meta' => ['title' => 'Dashboard Pendaftaran | ' . env('APP_NAME')]
        ]);
    }

    public function initRegistration(Request $request, Response $response): RedirectResponse
    {
        if (!$this->session->get('is_logged_in')) {
            return $response->redirect('/login');
        }

        $userId = $this->session->get('auth.user_id');
        $waveId = (int)$request->input('wave_id');

        if (!$waveId) {
            return $response->redirect('/dashboard?error=Gelombang+pendaftaran+harus+dipilih');
        }

        $db = $this->registrations->getDb();
        $stmt = $db->prepare("SELECT * FROM registrations WHERE user_id = :user_id AND wave_id = :wave_id LIMIT 1");
        $stmt->execute(['user_id' => $userId, 'wave_id' => $waveId]);
        if ($stmt->fetch()) {
            return $response->redirect('/dashboard?error=' . urlencode('Anda sudah terdaftar di gelombang ini.'));
        }

        $latest = $this->registrations->findByUserId($userId);
        if ($latest && $latest['status'] === 'Draft') {
            $this->registrations->updateById($latest['id'], [
                'wave_id' => $waveId,
                'status' => 'Draft',
                'current_step' => 1
            ]);
            $stmt = $db->prepare("DELETE FROM registration_programs WHERE registration_id = :id");
            $stmt->execute(['id' => $latest['id']]);

            return $response->redirect('/pendaftaran')->hard();
        }

        $newRegId = $this->registrations->insert([
            'user_id' => $userId,
            'wave_id' => $waveId,
            'full_name' => $latest ? $latest['full_name'] : ($this->session->get('auth.user_name') ?: ''),
            'nik' => $latest ? $latest['nik'] : null,
            'nisn' => $latest ? $latest['nisn'] : null,
            'birth_place' => $latest ? $latest['birth_place'] : '',
            'birth_date' => $latest ? $latest['birth_date'] : '1970-01-01',
            'gender' => $latest ? $latest['gender'] : 'Laki-laki',
            'religion' => $latest ? $latest['religion'] : '',
            'email' => $latest ? $latest['email'] : null,
            'phone' => $latest ? $latest['phone'] : null,
            'mother_name' => $latest ? $latest['mother_name'] : null,
            'info_source' => $latest ? $latest['info_source'] : null,
            'photo_path' => $latest ? $latest['photo_path'] : null,
            'status' => 'Draft',
            'current_step' => 1
        ]);

        if ($latest) {
            $stmt = $db->prepare("SELECT * FROM registration_addresses WHERE registration_id = :id LIMIT 1");
            $stmt->execute(['id' => $latest['id']]);
            $addr = $stmt->fetch();
            if ($addr) {
                unset($addr['id']);
                $addr['registration_id'] = $newRegId;
                $cols = implode(', ', array_keys($addr));
                $vals = ':' . implode(', :', array_keys($addr));
                $db->prepare("INSERT INTO registration_addresses ($cols) VALUES ($vals)")->execute($addr);
            }

            $stmt = $db->prepare("SELECT * FROM registration_educations WHERE registration_id = :id LIMIT 1");
            $stmt->execute(['id' => $latest['id']]);
            $edu = $stmt->fetch();
            if ($edu) {
                unset($edu['id']);
                $edu['registration_id'] = $newRegId;
                $cols = implode(', ', array_keys($edu));
                $vals = ':' . implode(', :', array_keys($edu));
                $db->prepare("INSERT INTO registration_educations ($cols) VALUES ($vals)")->execute($edu);
            }

            $stmt = $db->prepare("SELECT * FROM registration_parents WHERE registration_id = :id LIMIT 1");
            $stmt->execute(['id' => $latest['id']]);
            $par = $stmt->fetch();
            if ($par) {
                unset($par['id']);
                $par['registration_id'] = $newRegId;
                $cols = implode(', ', array_keys($par));
                $vals = ':' . implode(', :', array_keys($par));
                $db->prepare("INSERT INTO registration_parents ($cols) VALUES ($vals)")->execute($par);
            }

            $stmt = $db->prepare("SELECT * FROM registration_special_needs WHERE registration_id = :id");
            $stmt->execute(['id' => $latest['id']]);
            $needs = $stmt->fetchAll();
            foreach ($needs as $n) {
                unset($n['id']);
                $n['registration_id'] = $newRegId;
                $cols = implode(', ', array_keys($n));
                $vals = ':' . implode(', :', array_keys($n));
                $db->prepare("INSERT INTO registration_special_needs ($cols) VALUES ($vals)")->execute($n);
            }
        }

        return $response->redirect('/pendaftaran')->hard();
    }

    public function simulateState(Request $request, Response $response): RedirectResponse
    {
        $state = $request->input('state');
        if (in_array($state, ['belum_daftar', 'belum_bayar', 'verifikasi_pembayaran', 'upload_berkas', 'verifikasi_berkas', 'ujian_seleksi', 'lolos', 'tidak_lolos'])) {
            $this->session->set('registration_state', $state);
        }
        return $response->redirect('/dashboard');
    }

    public function markNotificationsRead(Request $request, Response $response): RedirectResponse
    {
        $userId = $this->session->get('auth.user_id');
        if ($userId) {
            $db = $this->registrations->getDb();
            $stmt = $db->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = :user_id OR user_id IS NULL");
            $stmt->execute(['user_id' => $userId]);
        }
        return $response->redirect('/dashboard');
    }

    public function showHistory(Request $request, Response $response): View | RedirectResponse
    {
        if (!$this->session->get('is_logged_in')) {
            return $response->redirect('/login');
        }

        $userId = $this->session->get('auth.user_id');
        $db = $this->registrations->getDb();

        $stmt = $db->prepare("SELECT id FROM registrations WHERE user_id = :user_id ORDER BY id ASC LIMIT 1");
        $stmt->execute(['user_id' => $userId]);
        $firstReg = $stmt->fetch();
        $isAccessAllowed = false;
        if ($firstReg) {
            $stmt = $db->prepare("SELECT count(*) FROM selection_results WHERE registration_id = :id AND is_published = 1 AND status IN ('Lulus', 'Tidak Lulus', 'Cadangan')");
            $stmt->execute(['id' => $firstReg['id']]);
            if ($stmt->fetchColumn() > 0) {
                $isAccessAllowed = true;
            }
        }

        if (!$isAccessAllowed) {
            return $response->redirect('/dashboard?error=' . urlencode('Halaman riwayat belum dapat diakses.'));
        }

        $allRegs = $this->registrations->findAllByUserId($userId);
        $allFinalized = true;
        foreach ($allRegs as $r) {
            if ($r['status'] === 'Draft') {
                $allFinalized = false;
                break;
            }
            $stmt = $db->prepare("SELECT * FROM selection_results WHERE registration_id = :id AND is_published = 1 LIMIT 1");
            $stmt->execute(['id' => $r['id']]);
            $sel = $stmt->fetch();
            if (!$sel || !in_array($sel['status'], ['Lulus', 'Tidak Lulus', 'Cadangan'])) {
                $allFinalized = false;
                break;
            }
        }

        $stmt = $db->prepare("SELECT * FROM waves WHERE is_active = 1");
        $stmt->execute();
        $activeWaves = $stmt->fetchAll() ?: [];
        $registeredWaveIds = array_filter(array_column($allRegs, 'wave_id'));
        $activeWaves = array_values(array_filter($activeWaves, function($w) use ($registeredWaveIds) {
            return !in_array((int)$w['id'], array_map('intval', $registeredWaveIds));
        }));

        $history = [];
        foreach ($allRegs as $hr) {
            $stmt = $db->prepare("SELECT * FROM waves WHERE id = :id LIMIT 1");
            $stmt->execute(['id' => $hr['wave_id']]);
            $w = $stmt->fetch();

            $stmt = $db->prepare("SELECT * FROM selection_results WHERE registration_id = :id LIMIT 1");
            $stmt->execute(['id' => $hr['id']]);
            $sel = $stmt->fetch();

            $passedProdi = '-';
            if ($sel && $sel['passed_program_id']) {
                $stmt = $db->prepare("SELECT * FROM study_programs WHERE id = :id LIMIT 1");
                $stmt->execute(['id' => $sel['passed_program_id']]);
                $prodi = $stmt->fetch();
                if ($prodi) {
                    $passedProdi = $prodi['name'];
                }
            }

            $history[] = [
                'registration' => $hr,
                'wave' => $w,
                'selection' => $sel,
                'passed_program_name' => $passedProdi
            ];
        }

        return $response->renderPage([
            'history' => $history,
            'all_finalized' => $allFinalized,
            'active_waves' => $activeWaves
        ], [
            'path' => '/dashboard/history',
            'meta' => ['title' => 'Riwayat Pendaftaran | ' . env('APP_NAME')]
        ]);
    }
}
