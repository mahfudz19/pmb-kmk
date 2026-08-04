<?php

namespace Addon\Controllers;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\View\View;
use App\Core\Http\RedirectResponse;
use Addon\Models\RegistrationModel;
use Addon\Models\RegistrationDocumentModel;
use Addon\Models\DocumentTypeModel;

class DocumentController
{
    public function __construct(
        private RegistrationModel $registrations,
        private RegistrationDocumentModel $documents,
        private DocumentTypeModel $documentTypes
    ) {}

    public function showUploadPage(Request $request, Response $response): View|RedirectResponse
    {
        $userId = $_SESSION['auth.user_id'] ?? null;
        if (!$userId) {
            return $response->redirect('/login');
        }

        $registration = $this->registrations->findByUserId($userId);

        if (!$registration || $registration['status'] === 'Draft') {
            return $response->redirect('/dashboard?error=Silakan+lengkapi+dan+kunci+formulir+pendaftaran+terlebih+dahulu.');
        }

        $regId = $registration['id'];
        $uploadedDocs = $this->documents->findByRegistrationId($regId);

        $mappedDocs = [];
        foreach ($uploadedDocs as $doc) {
            $key = $doc['document_type_id'] . '_' . ($doc['study_program_id'] ?? 'global');
            $mappedDocs[$key] = $doc;
        }

        $db = $this->registrations->getDb();
        $stmt = $db->prepare("SELECT * FROM registration_programs WHERE registration_id = :id LIMIT 1");
        $stmt->execute(['id' => $regId]);
        $regProgram = $stmt->fetch() ?: null;

        $uniqueDocsMap = [];
        if ($regProgram && $registration['wave_id']) {
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

                $stmtProdis = $db->prepare("SELECT id, name FROM study_programs WHERE id IN ($placeholders)");
                $stmtProdis->execute($prodiIds);
                $prodiNamesMap = [];
                foreach ($stmtProdis->fetchAll() as $p) {
                    $prodiNamesMap[$p['id']] = $p['name'];
                }

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
            }
        }

        $data = [
            'registration' => $registration,
            'document_types' => array_values($uniqueDocsMap),
            'uploaded_docs' => $mappedDocs
        ];

        return $response->renderPage($data, [
            'path' => '/pendaftaran/upload',
            'meta' => ['title' => 'Unggah Dokumen Persyaratan | ' . env('APP_NAME')]
        ]);
    }

    public function upload(Request $request, Response $response): Response
    {
        $userId = $_SESSION['auth.user_id'] ?? null;
        if (!$userId) {
            $response->setStatusCode(401);
            return $response->json(['success' => false, 'message' => 'Unauthorized']);
        }

        $registration = $this->registrations->findByUserId($userId);
        if (!$registration) {
            $response->setStatusCode(400);
            return $response->json(['success' => false, 'message' => 'Pendaftaran tidak ditemukan']);
        }

        $documentTypeId = (int) $request->input('document_type_id');
        $studyProgramId = null;
        if (!$documentTypeId) {
            $response->setStatusCode(400);
            return $response->json(['success' => false, 'message' => 'Jenis dokumen tidak valid']);
        }

        if (empty($_FILES['document']) || $_FILES['document']['error'] !== UPLOAD_ERR_OK) {
            $response->setStatusCode(400);
            return $response->json(['success' => false, 'message' => 'Gagal mengunggah file. Silakan coba lagi']);
        }

        $file = $_FILES['document'];

        if ($file['size'] > 2 * 1024 * 1024) {
            $response->setStatusCode(400);
            return $response->json(['success' => false, 'message' => 'Ukuran file maksimal adalah 2MB']);
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['pdf', 'jpg', 'jpeg', 'png'];
        if (!in_array($ext, $allowed, true)) {
            $response->setStatusCode(400);
            return $response->json(['success' => false, 'message' => 'Format file harus berupa PDF, JPG, JPEG, atau PNG']);
        }

        $storageDir = __DIR__ . '/../../storage/app/documents/';
        if (!is_dir($storageDir)) {
            mkdir($storageDir, 0755, true);
        }

        $filename = 'reg_' . $registration['id'] . '_doc_' . $documentTypeId . '.' . $ext;
        $destPath = $storageDir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            $response->setStatusCode(500);
            return $response->json(['success' => false, 'message' => 'Gagal menyimpan file ke penyimpanan server']);
        }

        $existingDoc = $this->documents->findByRegAndType($registration['id'], $documentTypeId);
        
        $docData = [
            'registration_id' => $registration['id'],
            'document_type_id' => $documentTypeId,
            'study_program_id' => null,
            'file_path' => $filename,
            'status' => 'Pending',
            'rejection_reason' => null
        ];

        if ($existingDoc) {
            $this->documents->updateById($existingDoc['id'], $docData);
        } else {
            $this->documents->insert($docData);
        }

        return $response->json(['success' => true, 'message' => 'Dokumen berhasil diunggah']);
    }

    public function viewFile(Request $request, Response $response): void
    {
        $userId = $_SESSION['auth.user_id'] ?? null;
        if (!$userId) {
            $response->setStatusCode(401);
            echo "Unauthorized";
            exit;
        }

        $docId = (int) $request->input('id');
        if (!$docId) {
            $response->setStatusCode(400);
            echo "ID Dokumen tidak valid";
            exit;
        }

        $doc = $this->documents->find($docId);
        if (!$doc) {
            $response->setStatusCode(404);
            echo "Dokumen tidak ditemukan";
            exit;
        }

        // Check ownership: must be the owner or admin
        $userRole = $_SESSION['auth.user_role'] ?? 'user';
        if ($userRole !== 'admin') {
            $registration = $this->registrations->findByUserId($userId);
            if (!$registration || $registration['id'] !== $doc['registration_id']) {
                $response->setStatusCode(403);
                echo "Access Denied";
                exit;
            }
        }

        $storageDir = __DIR__ . '/../../storage/app/documents/';
        $filePath = $storageDir . $doc['file_path'];

        if (!file_exists($filePath)) {
            $response->setStatusCode(404);
            echo "File fisik tidak ditemukan di server";
            exit;
        }

        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $contentTypes = [
            'pdf' => 'application/pdf',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg'
        ];

        $contentType = $contentTypes[$ext] ?? 'application/octet-stream';

        header("Content-Type: " . $contentType);
        header("Content-Length: " . filesize($filePath));
        header("Cache-Control: private, max-age=86400");
        readfile($filePath);
        exit;
    }

    public function listVerifications(Request $request, Response $response): View|RedirectResponse
    {
        if (!has_permission('verify_document')) {
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
            SELECT r.*, u.email 
            FROM registrations r
            JOIN users u ON r.user_id = u.id
            WHERE r.status != 'Draft'
            ORDER BY r.updated_at DESC
            LIMIT " . $limit . " OFFSET " . $offset . "
        ");
        $stmt->execute();
        $candidates = $stmt->fetchAll();

        foreach ($candidates as &$c) {
            $stmt = $db->prepare("SELECT * FROM registration_programs WHERE registration_id = :id LIMIT 1");
            $stmt->execute(['id' => $c['id']]);
            $prog = $stmt->fetch() ?: null;

            $requiredDocTypes = [];
            if ($prog && $c['wave_id']) {
                $prodiIds = [];
                if (!empty($prog['program1_id'])) $prodiIds[] = (int)$prog['program1_id'];
                if (!empty($prog['program2_id'])) $prodiIds[] = (int)$prog['program2_id'];
                if (!empty($prog['program3_id'])) $prodiIds[] = (int)$prog['program3_id'];
                $prodiIds = array_unique(array_filter($prodiIds));

                if (!empty($prodiIds)) {
                    $placeholders = implode(',', array_fill(0, count($prodiIds), '?'));
                    $stmtWaveDocs = $db->prepare("SELECT required_documents FROM wave_study_programs WHERE wave_id = ? AND study_program_id IN ($placeholders)");
                    $stmtWaveDocs->execute(array_merge([$c['wave_id']], $prodiIds));
                    $waveStudyPrograms = $stmtWaveDocs->fetchAll() ?: [];

                    foreach ($waveStudyPrograms as $wsp) {
                        $reqDocs = json_decode($wsp['required_documents'] ?? '[]', true) ?: [];
                        foreach ($reqDocs as $rd) {
                            if (isset($rd['document_type_id'])) {
                                $requiredDocTypes[] = (int)$rd['document_type_id'];
                            }
                        }
                    }
                    $requiredDocTypes = array_unique($requiredDocTypes);
                }
            }

            $docs = $this->documents->findByRegistrationId($c['id']);
            $uploadedCount = 0;
            $approvedCount = 0;
            foreach ($docs as $doc) {
                $dtId = (int)$doc['document_type_id'];
                if (in_array($dtId, $requiredDocTypes, true)) {
                    $uploadedCount++;
                    if ($doc['status'] === 'Approved') {
                        $approvedCount++;
                    }
                }
            }
            $c['uploaded_count'] = $uploadedCount;
            $c['required_count'] = count($requiredDocTypes);
            $c['approved_count'] = $approvedCount;
        }

        return $response->renderPage([
            'candidates' => $candidates,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalCount' => $totalCount,
            'limit' => $limit
        ], [
            'path' => '/admin/verifications/index',
            'meta' => ['title' => 'Verifikasi Berkas PMB | ' . env('APP_NAME')]
        ]);
    }

    public function showVerificationDetail(Request $request, Response $response): View|RedirectResponse
    {
        if (!has_permission('verify_document')) {
            return $response->redirect('/dashboard?error=Anda+tidak+memiliki+hak+akses+ke+halaman+ini.');
        }

        $regId = (int) $request->input('id');
        $registration = $this->registrations->find($regId);

        if (!$registration) {
            return $response->redirect('/admin/verifications?error=Data+pendaftar+tidak+ditemukan');
        }

        $stmt = $this->registrations->getDb()->prepare("
            SELECT r.*, u.email 
            FROM registrations r
            JOIN users u ON r.user_id = u.id
            WHERE r.id = :id LIMIT 1
        ");
        $stmt->execute(['id' => $regId]);
        $candInfo = $stmt->fetch();

        // Fetch related models
        $db = $this->registrations->getDb();
        
        $stmt = $db->prepare("SELECT * FROM registration_addresses WHERE registration_id = :id LIMIT 1");
        $stmt->execute(['id' => $regId]);
        $address = $stmt->fetch() ?: null;

        $stmt = $db->prepare("SELECT * FROM registration_parents WHERE registration_id = :id LIMIT 1");
        $stmt->execute(['id' => $regId]);
        $parents = $stmt->fetch() ?: null;

        $stmt = $db->prepare("SELECT * FROM registration_educations WHERE registration_id = :id LIMIT 1");
        $stmt->execute(['id' => $regId]);
        $education = $stmt->fetch() ?: null;

        $stmt = $db->prepare("
            SELECT rp.*, p1.name as prodi1_name, p2.name as prodi2_name, p3.name as prodi3_name
            FROM registration_programs rp
            JOIN study_programs p1 ON rp.program1_id = p1.id
            LEFT JOIN study_programs p2 ON rp.program2_id = p2.id
            LEFT JOIN study_programs p3 ON rp.program3_id = p3.id
            WHERE rp.registration_id = :id LIMIT 1
        ");
        $stmt->execute(['id' => $regId]);
        $program = $stmt->fetch() ?: null;

        // Fetch documents
        $uploadedDocs = $this->documents->findByRegistrationId($regId);
        $mappedDocs = [];
        foreach ($uploadedDocs as $doc) {
            $key = $doc['document_type_id'] . '_' . ($doc['study_program_id'] ?? 'global');
            $mappedDocs[$key] = $doc;
        }

        $uniqueDocsMap = [];
        if ($program && $registration['wave_id']) {
            $prodiIds = [];
            if (!empty($program['program1_id'])) $prodiIds[] = (int)$program['program1_id'];
            if (!empty($program['program2_id'])) $prodiIds[] = (int)$program['program2_id'];
            if (!empty($program['program3_id'])) $prodiIds[] = (int)$program['program3_id'];
            $prodiIds = array_unique(array_filter($prodiIds));

            if (!empty($prodiIds)) {
                $placeholders = implode(',', array_fill(0, count($prodiIds), '?'));
                $stmt = $db->prepare("SELECT * FROM wave_study_programs WHERE wave_id = ? AND study_program_id IN ($placeholders)");
                $stmt->execute(array_merge([$registration['wave_id']], $prodiIds));
                $waveStudyPrograms = $stmt->fetchAll() ?: [];

                $stmtProdis = $db->prepare("SELECT id, name FROM study_programs WHERE id IN ($placeholders)");
                $stmtProdis->execute($prodiIds);
                $prodiNamesMap = [];
                foreach ($stmtProdis->fetchAll() as $p) {
                    $prodiNamesMap[$p['id']] = $p['name'];
                }

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
                                    'descriptions' => [$prodiName => $rd['description'] ?? ''],
                                    'is_required' => true
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
            }
        }

        $data = [
            'candidate' => $candInfo,
            'address' => $address,
            'parents' => $parents,
            'education' => $education,
            'program' => $program,
            'document_types' => array_values($uniqueDocsMap),
            'uploaded_docs' => $mappedDocs
        ];

        return $response->renderPage($data, [
            'path' => '/admin/verifications/detail',
            'meta' => ['title' => 'Evaluasi Berkas ' . htmlspecialchars($candInfo['full_name']) . ' | ' . env('APP_NAME')]
        ]);
    }

    public function verifyDocument(Request $request, Response $response): RedirectResponse
    {
        if (!has_permission('verify_document')) {
            return $response->redirect('/dashboard?error=Anda+tidak+memiliki+hak+akses+untuk+melakukan+verifikasi.');
        }

        $docId = (int) $request->input('document_id');
        $status = $request->input('status');
        $reason = $request->input('rejection_reason') ?: null;

        if (!$docId || !in_array($status, ['Approved', 'Rejected'], true)) {
            return $response->redirect('/admin/verifications?error=Masukan+verifikasi+tidak+valid');
        }

        $doc = $this->documents->find($docId);
        if (!$doc) {
            return $response->redirect('/admin/verifications?error=Dokumen+tidak+ditemukan');
        }

        $this->documents->updateById($docId, [
            'status' => $status,
            'rejection_reason' => $reason
        ]);

        $registration = $this->registrations->find($doc['registration_id']);
        if ($registration) {
            $userId = $registration['user_id'];
            if ($status === 'Rejected') {
                send_system_notification($userId, 'Berkas Akademik Perlu Revisi', 'Salah satu dokumen persyaratan akademik Anda ditolak/perlu direvisi. Alasan: ' . ($reason ?? '-'), 'warning');
                send_email_notification($userId, $registration['email'], 'Berkas Akademik Perlu Revisi', 'Salah satu dokumen persyaratan akademik Anda ditolak/perlu direvisi. Alasan: ' . ($reason ?? '-'));
            } else {
                $allDocs = $this->documents->findByRegistrationId($doc['registration_id']);
                $allApproved = true;
                foreach ($allDocs as $d) {
                    if ((int)$d['id'] !== $docId && $d['status'] !== 'Approved') {
                        $allApproved = false;
                        break;
                    }
                }
                if ($allApproved) {
                    send_system_notification($userId, 'Berkas Akademik Disetujui', 'Seluruh berkas dokumen akademik Anda dinyatakan valid. Silakan ikuti tes CBT / ujian seleksi.', 'success');
                    send_email_notification($userId, $registration['email'], 'Berkas Akademik Disetujui', 'Seluruh berkas dokumen akademik Anda dinyatakan valid. Silakan ikuti tes CBT / ujian seleksi.');
                }
            }
        }

        log_activity('VERIFY_DOCUMENT', "Verifikasi dokumen ID {$docId} diubah menjadi {$status}.");
        return $response->redirect('/admin/verifications/detail?id=' . $doc['registration_id'] . '&success=Status+dokumen+berhasil+diperbarui.');
    }
}
