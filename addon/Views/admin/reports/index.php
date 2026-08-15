<?php

/**
 * @var array $stats
 */
?>

<div class="space-y-6">
  <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-100 pb-5">
    <div class="space-y-1">
      <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Laporan & Statistik PMB</h1>
      <p class="text-xs text-slate-500">Analisis data statistik pendaftar, laporan keuangan penerimaan, hasil seleksi ujian, dan rekapitulasi daftar ulang.</p>
    </div>
  </div>

  <!-- Tab Buttons -->
  <div class="border-b border-slate-200">
    <nav class="flex space-x-6" aria-label="Tabs">
      <button onclick="switchTab('tab-stats', this)" class="tab-btn py-3 px-1 border-b-2 font-bold text-xs uppercase tracking-wider border-indigo-600 text-indigo-600 transition-all focus:outline-none">
        📈 Statistik Pendaftar
      </button>
      <button onclick="switchTab('tab-finance', this)" class="tab-btn py-3 px-1 border-b-2 font-bold text-xs uppercase tracking-wider border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-350 transition-all focus:outline-none">
        💰 Laporan Keuangan
      </button>
      <button onclick="switchTab('tab-selection', this)" class="tab-btn py-3 px-1 border-b-2 font-bold text-xs uppercase tracking-wider border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-350 transition-all focus:outline-none">
        🎓 Hasil Seleksi
      </button>
      <button onclick="switchTab('tab-rereg', this)" class="tab-btn py-3 px-1 border-b-2 font-bold text-xs uppercase tracking-wider border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-350 transition-all focus:outline-none">
        ✅ Daftar Ulang
      </button>
      <button onclick="switchTab('tab-notifications', this)" class="tab-btn py-3 px-1 border-b-2 font-bold text-xs uppercase tracking-wider border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-350 transition-all focus:outline-none">
        🔔 Riwayat Notifikasi
      </button>
      <button onclick="switchTab('tab-audit', this)" class="tab-btn py-3 px-1 border-b-2 font-bold text-xs uppercase tracking-wider border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-350 transition-all focus:outline-none">
        🛡️ Log Aktivitas
      </button>
    </nav>
  </div>

  <!-- Tab 1: Statistik Pendaftar -->
  <div id="tab-stats" class="tab-content space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
      <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-2">
        <span class="text-slate-400 font-bold text-[10px] uppercase tracking-wider block">Total Pendaftar</span>
        <h3 class="text-3xl font-extrabold text-slate-900"><?= number_format($stats['total_registrants']) ?></h3>
        <p class="text-[10px] text-slate-400 font-medium">Calon mahasiswa baru terdaftar</p>
      </div>

      <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-2">
        <span class="text-slate-400 font-bold text-[10px] uppercase tracking-wider block">Sudah Kirim Form</span>
        <h3 class="text-3xl font-extrabold text-indigo-650"><?= number_format($stats['status_counts']['Submitted'] ?? 0) ?></h3>
        <p class="text-[10px] text-slate-400 font-medium">Calon mahasiswa status Submitted</p>
      </div>

      <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-2">
        <span class="text-slate-400 font-bold text-[10px] uppercase tracking-wider block">Lolos Seleksi (Released)</span>
        <h3 class="text-3xl font-extrabold text-emerald-650"><?= number_format($stats['status_counts']['Released'] ?? 0) ?></h3>
        <p class="text-[10px] text-slate-400 font-medium">Sudah diumumkan kelulusannya</p>
      </div>

      <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-2">
        <span class="text-slate-400 font-bold text-[10px] uppercase tracking-wider block">Berkas Terverifikasi</span>
        <h3 class="text-3xl font-extrabold text-amber-600"><?= number_format($stats['status_counts']['Verified'] ?? 0) ?></h3>
        <p class="text-[10px] text-slate-400 font-medium">Calon mahasiswa status Verified</p>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div class="md:col-span-2 bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-4">
        <h4 class="text-sm font-extrabold text-slate-800">Minat Program Studi (Pilihan 1)</h4>
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs border-collapse">
            <thead>
              <tr class="border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider text-[10px]">
                <th class="pb-3">Program Studi</th>
                <th class="pb-3 text-right">Jumlah Peminat</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 font-semibold text-slate-700">
              <?php if (empty($stats['program_counts'])): ?>
                <tr>
                  <td colspan="2" class="py-4 text-center text-slate-450 italic font-normal">Belum ada peminat prodi terdaftar.</td>
                </tr>
              <?php else: ?>
                <?php foreach ($stats['program_counts'] as $p): ?>
                  <tr>
                    <td class="py-3.5"><?= htmlspecialchars($p['program_name']) ?></td>
                    <td class="py-3.5 text-right"><?= number_format($p['count']) ?> pendaftar</td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-5">
        <h4 class="text-sm font-extrabold text-slate-800">Demografi Gender</h4>
        <div class="space-y-4 font-semibold text-slate-700 text-xs">
          <div class="space-y-1.5">
            <div class="flex justify-between text-slate-600">
              <span>Laki-Laki</span>
              <span><?= number_format($stats['gender_counts']['Laki-laki'] ?? 0) ?> pendaftar</span>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-2">
              <?php
              $mPercent = $stats['total_registrants'] > 0 ? (($stats['gender_counts']['Laki-laki'] ?? 0) / $stats['total_registrants']) * 100 : 0;
              ?>
              <div class="bg-indigo-600 h-2 rounded-full" style="width: <?= $mPercent ?>%"></div>
            </div>
          </div>
          <div class="space-y-1.5">
            <div class="flex justify-between text-slate-600">
              <span>Perempuan</span>
              <span><?= number_format($stats['gender_counts']['Perempuan'] ?? 0) ?> pendaftar</span>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-2">
              <?php
              $fPercent = $stats['total_registrants'] > 0 ? (($stats['gender_counts']['Perempuan'] ?? 0) / $stats['total_registrants']) * 100 : 0;
              ?>
              <div class="bg-emerald-500 h-2 rounded-full" style="width: <?= $fPercent ?>%"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Tab 2: Laporan Keuangan -->
  <div id="tab-finance" class="tab-content space-y-6 hidden">
    <div class="flex items-center justify-between gap-4">
      <h3 class="text-md font-extrabold text-slate-800">Ringkasan Penerimaan Keuangan</h3>
      <a href="<?= getBaseUrl('/admin/reports/export/finance') ?>" class="inline-flex items-center justify-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-sm text-xs transition-colors">
        🟢 Unduh Laporan Keuangan (CSV)
      </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-2">
        <span class="text-slate-400 font-bold text-[10px] uppercase tracking-wider block">Uang Pendaftaran</span>
        <h3 class="text-3xl font-extrabold text-slate-900">Rp <?= number_format($stats['total_registration_fees'], 0, ',', '.') ?></h3>
        <p class="text-[10px] text-slate-400 font-medium">Dari pembayaran biaya formulir terverifikasi</p>
      </div>

      <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-2">
        <span class="text-slate-400 font-bold text-[10px] uppercase tracking-wider block">Uang Pangkal / UKT</span>
        <h3 class="text-3xl font-extrabold text-indigo-650">Rp <?= number_format($stats['total_re_registration_fees'], 0, ',', '.') ?></h3>
        <p class="text-[10px] text-slate-400 font-medium">Dari pembayaran biaya daftar ulang mahasiswa baru</p>
      </div>

      <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-2">
        <span class="text-slate-400 font-bold text-[10px] uppercase tracking-wider block">Total Akumulasi</span>
        <h3 class="text-3xl font-extrabold text-emerald-650">Rp <?= number_format($stats['total_registration_fees'] + $stats['total_re_registration_fees'], 0, ',', '.') ?></h3>
        <p class="text-[10px] text-slate-400 font-medium">Total dana terkumpul PMB</p>
      </div>
    </div>

    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-4">
      <h4 class="text-sm font-extrabold text-slate-800">Log 10 Transaksi Keuangan Terbaru</h4>
      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs border-collapse">
          <thead>
            <tr class="border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider text-[10px]">
              <th class="pb-3">No</th>
              <th class="pb-3">Nama Lengkap</th>
              <th class="pb-3">Jenis Pembayaran</th>
              <th class="pb-3">Metode/Bank</th>
              <th class="pb-3 text-right">Nominal</th>
              <th class="pb-3 text-center">Status</th>
              <th class="pb-3 text-right">Waktu</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 font-semibold text-slate-700">
            <?php if (empty($stats['latest_transactions'])): ?>
              <tr>
                <td colspan="7" class="py-4 text-center text-slate-450 italic font-normal">Belum ada data transaksi tercatat.</td>
              </tr>
            <?php else: ?>
              <?php $no = 1;
              foreach ($stats['latest_transactions'] as $t): ?>
                <tr>
                  <td class="py-3.5 text-slate-400 font-normal"><?= $no++ ?></td>
                  <td class="py-3.5 text-slate-900"><?= htmlspecialchars($t['full_name']) ?></td>
                  <td class="py-3.5">
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold <?= $t['type'] === 'Pendaftaran' ? 'bg-indigo-50 text-indigo-700' : 'bg-purple-50 text-purple-700' ?>">
                      <?= $t['type'] ?>
                    </span>
                  </td>
                  <td class="py-3.5 text-slate-500 font-medium"><?= htmlspecialchars($t['bank_name']) ?></td>
                  <td class="py-3.5 text-right text-slate-900">Rp <?= number_format($t['amount'], 0, ',', '.') ?></td>
                  <td class="py-3.5 text-center">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold <?= $t['status'] === 'Approved' ? 'bg-emerald-50 text-emerald-700' : ($t['status'] === 'Rejected' ? 'bg-red-50 text-red-700' : 'bg-amber-50 text-amber-700') ?>">
                      <?= $t['status'] ?>
                    </span>
                  </td>
                  <td class="py-3.5 text-right text-slate-450 text-[10px] font-normal"><?= date('d-m-Y H:i', strtotime($t['created_at'])) ?></td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Tab 3: Hasil Seleksi -->
  <div id="tab-selection" class="tab-content space-y-6 hidden">
    <div class="flex items-center justify-between gap-4">
      <h3 class="text-md font-extrabold text-slate-800">Statistik Hasil Ujian & Seleksi</h3>
      <a href="<?= getBaseUrl('/admin/reports/export/selection') ?>" class="inline-flex items-center justify-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-sm text-xs transition-colors">
        🟢 Unduh Laporan Seleksi (CSV)
      </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
      <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-2">
        <span class="text-slate-400 font-bold text-[10px] uppercase tracking-wider block">Total Peserta Seleksi</span>
        <h3 class="text-3xl font-extrabold text-slate-900"><?= number_format($stats['total_exams']) ?></h3>
        <p class="text-[10px] text-slate-400 font-medium">Calon mahasiswa memiliki nilai ujian</p>
      </div>

      <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-2">
        <span class="text-slate-400 font-bold text-[10px] uppercase tracking-wider block">Rata-Rata Nilai CBT</span>
        <h3 class="text-3xl font-extrabold text-indigo-650"><?= number_format($stats['avg_test_score'], 1) ?></h3>
        <p class="text-[10px] text-slate-400 font-medium">Dari skala penilaian maksimal 100</p>
      </div>

      <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-2">
        <span class="text-slate-400 font-bold text-[10px] uppercase tracking-wider block">Rata-Rata Wawancara</span>
        <h3 class="text-3xl font-extrabold text-purple-650"><?= number_format($stats['avg_interview_score'], 1) ?></h3>
        <p class="text-[10px] text-slate-400 font-medium">Berdasarkan tim penilai wawancara</p>
      </div>

      <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-2">
        <span class="text-slate-400 font-bold text-[10px] uppercase tracking-wider block">Kelulusan (Lulus)</span>
        <h3 class="text-3xl font-extrabold text-emerald-650"><?= number_format($stats['selection_counts']['Lulus'] ?? 0) ?></h3>
        <p class="text-[10px] text-slate-400 font-medium">Calon mahasiswa Lulus Seleksi Utama</p>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-4">
        <h4 class="text-sm font-extrabold text-slate-800">Status Keputusan Kelulusan</h4>
        <div class="space-y-4 font-semibold text-slate-700 text-xs">
          <div class="space-y-1">
            <div class="flex justify-between text-slate-600">
              <span>Lulus Seleksi</span>
              <span><?= number_format($stats['selection_counts']['Lulus'] ?? 0) ?></span>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-1.5">
              <?php $lPercent = $stats['total_exams'] > 0 ? (($stats['selection_counts']['Lulus'] ?? 0) / $stats['total_exams']) * 100 : 0; ?>
              <div class="bg-emerald-500 h-1.5 rounded-full" style="width: <?= $lPercent ?>%"></div>
            </div>
          </div>
          <div class="space-y-1">
            <div class="flex justify-between text-slate-600">
              <span>Cadangan</span>
              <span><?= number_format($stats['selection_counts']['Cadangan'] ?? 0) ?></span>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-1.5">
              <?php $cPercent = $stats['total_exams'] > 0 ? (($stats['selection_counts']['Cadangan'] ?? 0) / $stats['total_exams']) * 100 : 0; ?>
              <div class="bg-amber-500 h-1.5 rounded-full" style="width: <?= $cPercent ?>%"></div>
            </div>
          </div>
          <div class="space-y-1">
            <div class="flex justify-between text-slate-600">
              <span>Tidak Lulus</span>
              <span><?= number_format($stats['selection_counts']['Tidak Lulus'] ?? 0) ?></span>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-1.5">
              <?php $tlPercent = $stats['total_exams'] > 0 ? (($stats['selection_counts']['Tidak Lulus'] ?? 0) / $stats['total_exams']) * 100 : 0; ?>
              <div class="bg-red-500 h-1.5 rounded-full" style="width: <?= $tlPercent ?>%"></div>
            </div>
          </div>
        </div>
      </div>

      <div class="md:col-span-2 bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-4">
        <h4 class="text-sm font-extrabold text-slate-800">Catatan/Alasan Hasil Ujian Terbaru</h4>
        <div class="space-y-3 font-semibold text-slate-700 text-xs">
          <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider border-b border-slate-100 pb-1.5">Metodologi & Pengawasan CBT</p>
          <div class="text-slate-600 leading-relaxed space-y-2">
            <p>1. Ujian tulis CBT dikerjakan pendaftar secara daring mandiri dengan sistem proctoring berbasis web browser untuk melacak perpindahan tab.</p>
            <p>2. Kriteria kelulusan utama didasarkan pada perolehan skor CBT minimal 60.00 dan pertimbangan penilaian wawancara oleh kaprodi.</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Tab 4: Daftar Ulang -->
  <div id="tab-rereg" class="tab-content space-y-6 hidden">
    <div class="flex items-center justify-between gap-4">
      <h3 class="text-md font-extrabold text-slate-800">Rekapitulasi Daftar Ulang Mahasiswa Baru</h3>
      <a href="<?= getBaseUrl('/admin/reports/export/re-registrations') ?>" class="inline-flex items-center justify-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-sm text-xs transition-colors">
        🟢 Unduh Laporan Daftar Ulang (CSV)
      </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-2">
        <span class="text-slate-400 font-bold text-[10px] uppercase tracking-wider block">Menunggu Verifikasi</span>
        <h3 class="text-3xl font-extrabold text-amber-600"><?= number_format($stats['rereg_counts']['Pending'] ?? 0) ?></h3>
        <p class="text-[10px] text-slate-400 font-medium">Mahasiswa baru menunggu persetujuan berkas</p>
      </div>

      <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-2">
        <span class="text-slate-400 font-bold text-[10px] uppercase tracking-wider block">Daftar Ulang Disetujui</span>
        <h3 class="text-3xl font-extrabold text-emerald-650"><?= number_format($stats['rereg_counts']['Approved'] ?? 0) ?></h3>
        <p class="text-[10px] text-slate-400 font-medium">Resmi berstatus mahasiswa baru</p>
      </div>

      <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-2">
        <span class="text-slate-400 font-bold text-[10px] uppercase tracking-wider block">Daftar Ulang Ditolak</span>
        <h3 class="text-3xl font-extrabold text-red-650"><?= number_format($stats['rereg_counts']['Rejected'] ?? 0) ?></h3>
        <p class="text-[10px] text-slate-400 font-medium">Pembayaran atau berkas tidak valid</p>
      </div>
    </div>

    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-4">
      <h4 class="text-sm font-extrabold text-slate-800">Daftar Mahasiswa Baru Lunas & Disetujui</h4>
      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs border-collapse">
          <thead>
            <tr class="border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider text-[10px]">
              <th class="pb-3">No</th>
              <th class="pb-3">Nama Lengkap</th>
              <th class="pb-3">Email</th>
              <th class="pb-3">Program Studi</th>
              <th class="pb-3 text-right">Biaya Pangkal</th>
              <th class="pb-3 text-center">Status</th>
              <th class="pb-3 text-right">Tanggal Update</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 font-semibold text-slate-700">
            <?php if (empty($stats['latest_reregistrations'])): ?>
              <tr>
                <td colspan="7" class="py-4 text-center text-slate-450 italic font-normal">Belum ada data pendaftaran ulang terverifikasi.</td>
              </tr>
            <?php else: ?>
              <?php $no = 1;
              foreach ($stats['latest_reregistrations'] as $rr): ?>
                <tr>
                  <td class="py-3.5 text-slate-400 font-normal"><?= $no++ ?></td>
                  <td class="py-3.5 text-slate-900"><?= htmlspecialchars($rr['full_name']) ?></td>
                  <td class="py-3.5 text-slate-500 font-normal"><?= htmlspecialchars($rr['email']) ?></td>
                  <td class="py-3.5"><?= htmlspecialchars($rr['passed_program_name'] ?? '-') ?></td>
                  <td class="py-3.5 text-right text-slate-900">Rp <?= number_format($rr['amount'], 0, ',', '.') ?></td>
                  <td class="py-3.5 text-center">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold <?= $rr['status'] === 'Approved' ? 'bg-emerald-50 text-emerald-700' : ($rr['status'] === 'Rejected' ? 'bg-red-50 text-red-700' : 'bg-amber-50 text-amber-700') ?>">
                      <?= $rr['status'] ?>
                    </span>
                  </td>
                  <td class="py-3.5 text-right text-slate-450 text-[10px] font-normal"><?= date('d-m-Y H:i', strtotime($rr['updated_at'])) ?></td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Tab 5: Riwayat Notifikasi -->
  <div id="tab-notifications" class="tab-content space-y-6 hidden">
    <div class="flex items-center justify-between gap-4">
      <h3 class="text-md font-extrabold text-slate-800">Riwayat Pengiriman Notifikasi</h3>
    </div>

    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-4">
      <h4 class="text-sm font-extrabold text-slate-800">50 Riwayat Pengiriman Notifikasi Sistem & Email</h4>
      <div class="overflow-x-auto">
        <table data-paginate="10" class="w-full text-left text-xs border-collapse">
          <thead>
            <tr class="border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider text-[10px]">
              <th class="pb-3">No</th>
              <th class="pb-3">Nama Penerima</th>
              <th class="pb-3">Email Penerima</th>
              <th class="pb-3">Saluran</th>
              <th class="pb-3">Judul Notifikasi</th>
              <th class="pb-3">Konten / Pesan</th>
              <th class="pb-3 text-center">Status</th>
              <th class="pb-3 text-right">Tanggal Kirim</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 font-semibold text-slate-700">
            <?php if (empty($stats['notification_history'])): ?>
              <tr>
                <td colspan="8" class="py-4 text-center text-slate-450 italic font-normal">Belum ada riwayat pengiriman notifikasi.</td>
              </tr>
            <?php else: ?>
              <?php $no = 1;
              foreach ($stats['notification_history'] as $nh): ?>
                <tr>
                  <td class="py-3.5 text-slate-400 font-normal"><?= $no++ ?></td>
                  <td class="py-3.5 text-slate-900"><?= htmlspecialchars($nh['full_name'] ?? 'Sistem / Semua') ?></td>
                  <td class="py-3.5 text-slate-500 font-normal"><?= htmlspecialchars($nh['recipient_email'] ?? '-') ?></td>
                  <td class="py-3.5">
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold <?= $nh['channel'] === 'email' ? 'bg-blue-50 text-blue-700' : 'bg-slate-50 text-slate-700' ?>">
                      <?= htmlspecialchars($nh['channel']) ?>
                    </span>
                  </td>
                  <td class="py-3.5 text-slate-900"><?= htmlspecialchars($nh['title']) ?></td>
                  <td class="py-3.5 text-slate-500 font-normal max-w-xs truncate" title="<?= htmlspecialchars($nh['content']) ?>"><?= htmlspecialchars($nh['content']) ?></td>
                  <td class="py-3.5 text-center">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-700">
                      <?= htmlspecialchars($nh['status']) ?>
                    </span>
                  </td>
                  <td class="py-3.5 text-right text-slate-450 text-[10px] font-normal"><?= date('d-m-Y H:i', strtotime($nh['created_at'])) ?></td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Tab 6: Log Aktivitas -->
  <div id="tab-audit" class="tab-content hidden space-y-6">
    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-4">
      <div class="flex items-center justify-between">
        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Audit Log Aktivitas Sistem</h3>
      </div>
      <div class="overflow-x-auto">
        <table data-paginate="15" class="w-full text-left border-collapse text-xs">
          <thead>
            <tr class="border-b border-slate-150 text-[10px] font-bold text-slate-450 uppercase tracking-wider">
              <th class="pb-3 w-12">No</th>
              <th class="pb-3 w-40">Pengguna</th>
              <th class="pb-3 w-32">Alamat IP</th>
              <th class="pb-3 w-36">Aktivitas</th>
              <th class="pb-3">Keterangan</th>
              <th class="pb-3 w-36 text-right">Waktu Kejadian</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 font-semibold text-slate-700">
            <?php if (empty($stats['audit_logs'])): ?>
              <tr>
                <td colspan="6" class="py-4 text-center text-slate-450 italic font-normal">Belum ada catatan aktivitas sistem.</td>
              </tr>
            <?php else: ?>
              <?php $no = 1;
              foreach ($stats['audit_logs'] as $log): ?>
                <tr>
                  <td class="py-3.5 text-slate-400 font-normal"><?= $no++ ?></td>
                  <td class="py-3.5 text-slate-900"><?= htmlspecialchars($log['username']) ?></td>
                  <td class="py-3.5 text-slate-500 font-normal"><?= htmlspecialchars($log['ip_address']) ?></td>
                  <td class="py-3.5">
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700">
                      <?= htmlspecialchars($log['activity']) ?>
                    </span>
                  </td>
                  <td class="py-3.5 text-slate-500 font-normal" title="<?= htmlspecialchars($log['user_agent']) ?>"><?= htmlspecialchars($log['description']) ?></td>
                  <td class="py-3.5 text-right text-slate-450 text-[10px] font-normal"><?= date('d-m-Y H:i', strtotime($log['created_at'])) ?></td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script>
  function switchTab(tabId, btnEl) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    document.getElementById(tabId).classList.remove('hidden');

    document.querySelectorAll('.tab-btn').forEach(btn => {
      btn.classList.remove('border-indigo-600', 'text-indigo-600');
      btn.classList.add('border-transparent', 'text-slate-500', 'hover:text-slate-700', 'hover:border-slate-350');
    });

    btnEl.classList.remove('border-transparent', 'text-slate-500', 'hover:text-slate-700', 'hover:border-slate-350');
    btnEl.classList.add('border-indigo-600', 'text-indigo-600');
  }
</script>