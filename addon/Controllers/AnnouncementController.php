<?php

namespace Addon\Controllers;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\View\View;
use App\Core\Http\RedirectResponse;
use App\Services\SessionService;

use Addon\Models\AnnouncementModel;
use Addon\Models\RegistrationModel;
use Addon\Models\SelectionResultModel;

use Dompdf\Dompdf;
use Dompdf\Options;

class AnnouncementController
{
    public function __construct(
        private SessionService $session,
        private AnnouncementModel $announcements,
        private RegistrationModel $registrations,
        private SelectionResultModel $selectionResults
    ) {}

    public function listAnnouncements(Request $request, Response $response): View|RedirectResponse
    {
        if (!has_permission('manage_selection')) {
            return $response->redirect('/dashboard?error=Anda+tidak+memiliki+hak+akses+ke+halaman+ini.');
        }

        $list = $this->announcements->all();

        return $response->renderPage([
            'announcements' => $list
        ], [
            'path' => '/admin/announcements',
            'meta' => ['title' => 'Manajemen Pengumuman PMB | ' . env('APP_NAME')]
        ]);
    }

    public function saveAnnouncement(Request $request, Response $response): RedirectResponse
    {
        if (!has_permission('manage_selection')) {
            return $response->redirect('/dashboard?error=Anda+tidak+memiliki+hak+akses+ke+halaman+ini.');
        }

        $id = $request->input('id') !== '' ? (int) $request->input('id') : null;
        $title = $request->input('title');
        $content = $request->input('content');
        $isActive = (int) $request->input('is_active');

        if (empty($title) || empty($content)) {
            return $response->redirect('/admin/announcements?error=Judul+dan+konten+wajib+diisi');
        }

        $db = $this->announcements->getDb();

        if ($isActive === 1) {
            $stmt = $db->prepare("UPDATE announcements SET is_active = 0");
            $stmt->execute();
        }

        $data = [
            'title' => $title,
            'content' => $content,
            'is_active' => $isActive
        ];

        if ($id) {
            $this->announcements->updateById($id, $data);
        } else {
            $this->announcements->insert($data);
        }

        return $response->redirect('/admin/announcements?success=Pengumuman+berhasil+disimpan');
    }

    public function deleteAnnouncement(Request $request, Response $response): RedirectResponse
    {
        if (!has_permission('manage_selection')) {
            return $response->redirect('/dashboard?error=Anda+tidak+memiliki+hak+akses+ke+halaman+ini.');
        }

        $id = (int) $request->input('id');

        if (!$id) {
            return $response->redirect('/admin/announcements?error=ID+pengumuman+tidak+valid');
        }

        $db = $this->announcements->getDb();
        $stmt = $db->prepare("DELETE FROM announcements WHERE id = :id");
        $stmt->execute(['id' => $id]);

        return $response->redirect('/admin/announcements?success=Pengumuman+berhasil+dihapus');
    }

    public function downloadLetter(Request $request, Response $response)
    {
        if (!$this->session->get('is_logged_in')) {
            return $response->redirect('/login');
        }

        $userRole = $this->session->get('auth.user_role');
        $regId = $request->input('registration_id') !== '' ? (int) $request->input('registration_id') : null;

        if ($userRole === 'admin' && $regId) {
            $registration = $this->registrations->find($regId);
        } else {
            $userId = $this->session->get('auth.user_id');
            $registration = $this->registrations->findByUserId($userId);
        }

        if (!$registration) {
            $response->setStatusCode(404);
            echo "Data pendaftaran tidak ditemukan.";
            exit;
        }

        $selection = $this->selectionResults->findByRegistrationId($registration['id']);
        if (!$selection || $selection['status'] !== 'Lulus') {
            $response->setStatusCode(403);
            echo "Akses ditolak. Anda belum dinyatakan lulus seleksi.";
            exit;
        }

        if ($userRole !== 'admin' && (int) $selection['is_published'] === 0) {
            $response->setStatusCode(403);
            echo "Akses ditolak. Pengumuman kelulusan belum dipublikasikan.";
            exit;
        }

        $db = $this->registrations->getDb();
        $stmt = $db->prepare("SELECT * FROM study_programs WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $selection['passed_program_id']]);
        $program = $stmt->fetch();
        $programName = $program ? $program['name'] : '-';

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);

        $today = date('d F Y');
        $letterNo = "No: " . (100 + $registration['id']) . "/PMB-KMK/VII/" . date('Y');

        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <style>
                body {
                    font-family: Arial, sans-serif;
                    font-size: 13px;
                    line-height: 1.5;
                    color: #1e293b;
                    margin: 0;
                    padding: 0;
                }
                .kop-surat {
                    border-bottom: 3px double #000;
                    padding-bottom: 15px;
                    margin-bottom: 25px;
                    text-align: center;
                }
                .kop-surat h1 {
                    font-size: 18px;
                    margin: 0 0 5px 0;
                    text-transform: uppercase;
                    color: #1e3a8a;
                }
                .kop-surat p {
                    margin: 2px 0;
                    font-size: 11px;
                    color: #475569;
                }
                .title {
                    text-align: center;
                    font-weight: bold;
                    font-size: 14px;
                    text-decoration: underline;
                    margin-bottom: 5px;
                    text-transform: uppercase;
                }
                .letter-no {
                    text-align: center;
                    font-size: 11px;
                    margin-bottom: 25px;
                }
                .content-table {
                    width: 100%;
                    margin: 15px 0;
                    border-collapse: collapse;
                }
                .content-table td {
                    padding: 5px 0;
                }
                .content-table td.label {
                    width: 25%;
                    font-weight: bold;
                }
                .content-table td.colon {
                    width: 3%;
                }
                .status-box {
                    background-color: #f0fdf4;
                    border: 1px solid #bbf7d0;
                    padding: 12px;
                    text-align: center;
                    font-size: 14px;
                    font-weight: bold;
                    color: #15803d;
                    margin: 20px 0;
                    border-radius: 6px;
                }
                .footer {
                    margin-top: 50px;
                    width: 100%;
                }
                .footer td {
                    width: 50%;
                }
                .signature-panel {
                    text-align: right;
                    padding-right: 30px;
                }
            </style>
        </head>
        <body>
            <div class="kop-surat">
                <h1>PANITIA SELEKSI PENERIMAAN MAHASISWA BARU</h1>
                <h1>KAMPUS MANDIRI KENCANA (KMK)</h1>
                <p>Jl. Pendidikan Kencana No. 45, Jakarta Selatan | Telp: (021) 789456</p>
                <p>Email: pmb@kmk.ac.id | Website: www.kmk.ac.id</p>
            </div>

            <div class="title">SURAT KEPUTUSAN HASIL SELEKSI</div>
            <div class="letter-no">' . $letterNo . '</div>

            <p>Dengan hormat,</p>
            <p>Berdasarkan hasil penilaian ujian tertulis (CBT) dan wawancara seleksi penerimaan mahasiswa baru Kampus Mandiri Kencana tahun akademik ' . date('Y') . '/' . (date('Y') + 1) . ', Panitia Seleksi menetapkan bahwa:</p>

            <table class="content-table">
                <tr>
                    <td class="label">Nama Lengkap</td>
                    <td class="colon">:</td>
                    <td>' . htmlspecialchars($registration['full_name']) . '</td>
                </tr>
                <tr>
                    <td class="label">NIK (No. KTP)</td>
                    <td class="colon">:</td>
                    <td>' . htmlspecialchars($registration['nik']) . '</td>
                </tr>
                <tr>
                    <td class="label">NISN</td>
                    <td class="colon">:</td>
                    <td>' . htmlspecialchars($registration['nisn']) . '</td>
                </tr>
                <tr>
                    <td class="label">Pilihan Program Studi</td>
                    <td class="colon">:</td>
                    <td>' . htmlspecialchars($programName) . '</td>
                </tr>
            </table>

            <div class="status-box">
                DINYATAKAN: LULUS SELEKSI UTAMA
            </div>

            <p>Bagi calon mahasiswa yang dinyatakan Lulus Seleksi Utama, harap segera melakukan pendaftaran ulang dengan mengunggah berkas persyaratan daftar ulang dan melakukan pembayaran biaya kuliah semester awal sebelum batas waktu yang ditentukan.</p>
            
            ' . (!empty($selection['notes']) ? '<p><strong>Catatan Tambahan Panitia:</strong><br><span style="font-style: italic;">"' . htmlspecialchars($selection['notes']) . '"</span></p>' : '') . '

            <p>Demikian surat keputusan ini disampaikan untuk dipergunakan sebagaimana mestinya.</p>

            <table class="footer">
                <tr>
                    <td></td>
                    <td class="signature-panel">
                        <p>Jakarta, ' . $today . '</p>
                        <p>Ketua Panitia PMB KMK,</p>
                        <br><br><br><br>
                        <p><strong>Prof. Dr. Ir. Hermawan, M.T.</strong></p>
                        <p>NIP. 19750812 200212 1 002</p>
                    </td>
                </tr>
            </table>
        </body>
        </html>
        ';

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = "Surat_Kelulusan_" . str_replace(' ', '_', $registration['full_name']) . ".pdf";
        $dompdf->stream($filename, ["Attachment" => 1]);
        exit;
    }
}
