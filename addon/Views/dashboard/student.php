<?php

/**
 * @var null|array $re_registration
 * @var null|array $payment
 * @var null|array $active_announcement
 * @var mixed $exam_results
 * @var mixed $active_waves
 * @var mixed $state
 */
?>

<div class="w-full space-y-4">
  <?php if ($active_announcement && $state !== 'belum_daftar'): ?>
    <div id="announcement-ribbon" class="bg-indigo-50/80 border border-indigo-150 rounded-xl px-4 py-1.5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 shadow-xs">
      <div class="flex items-center gap-2.5 min-w-0 flex-1">
        <span class="text-base shrink-0">📢</span>
        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-indigo-600 text-white uppercase tracking-wider shrink-0">Pengumuman</span>
        <span class="text-xs font-semibold text-slate-800 truncate"><?= htmlspecialchars($active_announcement['title']) ?></span>
      </div>
      <div class="flex items-center gap-2 self-end sm:self-center shrink-0">
        <button type="button" onclick="openAnnouncementModal()" class="inline-flex items-center gap-1 text-xs font-bold text-indigo-650 hover:text-indigo-800 transition-colors cursor-pointer">
          Baca Selengkapnya <span class="text-sm">→</span>
        </button>
      </div>
    </div>

    <div id="announcement-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4" style="z-index: 99999;">
      <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeAnnouncementModal()" style="z-index: 99998;"></div>
      <div class="relative bg-white rounded-3xl max-w-lg w-full p-6 md:p-8 shadow-2xl border border-slate-100 space-y-5" style="z-index: 99999;">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <div class="flex items-center gap-2.5">
            <span class="text-xl">📢</span>
            <div>
              <span class="text-[10px] font-bold text-indigo-600 uppercase tracking-widest block">Pengumuman Resmi</span>
              <h3 class="text-sm font-extrabold text-slate-900"><?= htmlspecialchars($active_announcement['title']) ?></h3>
            </div>
          </div>
          <button type="button" onclick="closeAnnouncementModal()" class="w-8 h-8 rounded-full hover:bg-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-600 transition-colors text-lg font-bold cursor-pointer">
            &times;
          </button>
        </div>
        <div class="max-h-80 overflow-y-auto pr-2 text-xs text-slate-650 leading-relaxed space-y-2">
          <?= nl2br(htmlspecialchars($active_announcement['content'])) ?>
        </div>
        <div class="pt-2 border-t border-slate-100 flex justify-end">
          <button type="button" onclick="closeAnnouncementModal()" class="px-5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition-colors cursor-pointer">
            Tutup
          </button>
        </div>
      </div>
    </div>

    <script>
      function openAnnouncementModal() {
        const modal = document.getElementById('announcement-modal');
        if (modal) {
          if (modal.parentElement !== document.body) {
            document.body.appendChild(modal);
          }
          modal.classList.remove('hidden');
        }
      }
      function closeAnnouncementModal() {
        const modal = document.getElementById('announcement-modal');
        if (modal) {
          modal.classList.add('hidden');
        }
      }
    </script>
  <?php endif; ?>

  <?php if ($state !== 'belum_daftar'): ?>
    <!-- Stepper Timeline -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200/80 p-3 md:p-5">

      <div class="relative flex flex-col md:flex-row items-start md:items-center justify-between gap-6 md:gap-4">
        <!-- Line behind (desktop only) -->
        <div class="hidden md:block absolute top-[18px] left-[5%] right-[5%] h-0.5 bg-slate-100 -z-1"></div>

        <!-- Step 1 -->
        <?php
        $step1_done = in_array($state, ['belum_bayar', 'verifikasi_pembayaran', 'upload_berkas', 'verifikasi_berkas', 'ujian_seleksi', 'lolos', 'tidak_lolos']);
        $step1_active = in_array($state, ['belum_daftar', 'draft']);
        $step1_editable = !in_array($state, ['lolos', 'tidak_lolos']);
        ?>
        <?php if ($step1_editable && $state !== 'draft'): ?>
          <a data-spa href="<?= getBaseUrl('/dashboard') ?>" class="flex md:flex-col items-center gap-4 md:gap-2 flex-1 w-full text-left md:text-center z-10 hover:opacity-80 transition-opacity" title="Formulir Pendaftaran">
        <?php else: ?>
          <div class="flex md:flex-col items-center gap-4 md:gap-2 flex-1 w-full text-left md:text-center z-10">
        <?php endif; ?>
            <span class="flex items-center justify-center w-9 h-9 rounded-full font-bold text-sm transition-all <?= $step1_done ? 'bg-emerald-500 text-white shadow-sm shadow-emerald-200' : ($step1_active ? 'bg-indigo-600 text-white ring-4 ring-indigo-100' : 'bg-slate-100 text-slate-400') ?>">
              <?= $step1_done ? '✓' : '1' ?>
            </span>
            <div>
              <h4 class="text-xs font-bold text-slate-800 leading-tight flex items-center justify-center gap-1">
                Formulir
                <?php if ($step1_editable && $state !== 'belum_daftar'): ?>
                  <span class="text-[9px] text-indigo-600 font-semibold bg-indigo-50 px-1 py-0.2 rounded border border-indigo-100">✏️ Edit</span>
                <?php endif; ?>
              </h4>
              <span class="text-[10px] text-slate-400 font-medium block">Biodata & Pilihan Prodi</span>
            </div>
            <?php if ($step1_editable && $state !== 'draft'): ?>
          </a>
        <?php else: ?>
      </div>
    <?php endif; ?>

    <!-- Step 2 -->
    <?php
    $step2_done = in_array($state, ['upload_berkas', 'verifikasi_berkas', 'ujian_seleksi', 'lolos', 'tidak_lolos']);
    $step2_active = in_array($state, ['belum_bayar', 'verifikasi_pembayaran']);
    ?>
    <div class="flex md:flex-col items-center gap-4 md:gap-2 flex-1 w-full text-left md:text-center z-10">
      <span class="flex items-center justify-center w-9 h-9 rounded-full font-bold text-sm transition-all <?= $step2_done ? 'bg-emerald-500 text-white shadow-sm shadow-emerald-200' : ($step2_active ? 'bg-indigo-600 text-white ring-4 ring-indigo-100' : 'bg-slate-100 text-slate-400') ?>">
        <?= $step2_done ? '✓' : '2' ?>
      </span>
      <div>
        <h4 class="text-xs font-bold text-slate-800 leading-tight">Pembayaran</h4>
        <span class="text-[10px] text-slate-400 font-medium block">Biaya Formulir<?= (empty($wave['registration_fee_total']) || (float)$wave['registration_fee_total'] <= 0) ? ' (Gratis)' : '' ?></span>
      </div>
    </div>

    <!-- Step 3 -->
    <?php
    $step3_done = in_array($state, ['ujian_seleksi', 'lolos', 'tidak_lolos']);
    $step3_active = in_array($state, ['upload_berkas', 'verifikasi_berkas']);
    $step3_clickable = ($step3_done || $step3_active);
    ?>
    <?php if ($step3_clickable): ?>
      <a data-spa href="<?= getBaseUrl('/pendaftaran/dokumen') ?>" class="flex md:flex-col items-center gap-4 md:gap-2 flex-1 w-full text-left md:text-center z-10 hover:opacity-80 transition-opacity">
      <?php else: ?>
        <div class="flex md:flex-col items-center gap-4 md:gap-2 flex-1 w-full text-left md:text-center z-10">
        <?php endif; ?>
        <span class="flex items-center justify-center w-9 h-9 rounded-full font-bold text-sm transition-all <?= $step3_done ? 'bg-emerald-500 text-white shadow-sm shadow-emerald-200' : ($step3_active ? 'bg-indigo-600 text-white ring-4 ring-indigo-100' : 'bg-slate-100 text-slate-400') ?>">
          <?= $step3_done ? '✓' : '3' ?>
        </span>
        <div>
          <h4 class="text-xs font-bold text-slate-800 leading-tight">Unggah Berkas</h4>
          <span class="text-[10px] text-slate-400 font-medium block">Scan Raport / Ijazah</span>
        </div>
        <?php if ($step3_clickable): ?>
      </a>
    <?php else: ?>
    </div>
  <?php endif; ?>

  <!-- Step 4 -->
  <?php
    $step4_done = in_array($state, ['lolos', 'tidak_lolos']);
    $step4_active = ($state === 'ujian_seleksi');
  ?>
  <div class="flex md:flex-col items-center gap-4 md:gap-2 flex-1 w-full text-left md:text-center z-10">
    <span class="flex items-center justify-center w-9 h-9 rounded-full font-bold text-sm transition-all <?= $step4_done ? 'bg-emerald-500 text-white shadow-sm shadow-emerald-200' : ($step4_active ? 'bg-indigo-600 text-white ring-4 ring-indigo-100' : 'bg-slate-100 text-slate-400') ?>">
      <?= $step4_done ? '✓' : '4' ?>
    </span>
    <div>
      <h4 class="text-xs font-bold text-slate-800 leading-tight">Ujian Seleksi</h4>
      <span class="text-[10px] text-slate-400 font-medium block">Tes Tulis / Wawancara</span>
    </div>
  </div>

  <!-- Step 5 -->
  <?php
    $step5_active = in_array($state, ['lolos', 'tidak_lolos']);
  ?>
  <div class="flex md:flex-col items-center gap-4 md:gap-2 flex-1 w-full text-left md:text-center z-10">
    <span class="flex items-center justify-center w-9 h-9 rounded-full font-bold text-sm transition-all <?= $step5_active ? (($state === 'lolos') ? 'bg-emerald-500 text-white shadow-sm shadow-emerald-200' : 'bg-red-500 text-white shadow-sm shadow-red-200') : 'bg-slate-100 text-slate-400' ?>">
      <?= $step5_active ? (($state === 'lolos') ? '✓' : '✗') : '5' ?>
    </span>
    <div>
      <h4 class="text-xs font-bold text-slate-800 leading-tight">Pengumuman</h4>
      <span class="text-[10px] text-slate-400 font-medium block">Hasil Kelulusan PMB</span>
    </div>
  </div>
</div>
</div>
<?php endif; ?>



<!-- State-Driven Content Card -->
<div class="bg-white rounded-xl shadow-sm border border-slate-200/80 p-5 md:p-6 space-y-6">
  <?php if ($state === 'belum_daftar'): ?>
    <div class="text-center space-y-6 max-w-2xl mx-auto">
      <div class="text-5xl">🎓</div>
      <div class="space-y-3">
        <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Selamat Datang di Portal PMB</h2>
        <p class="text-xs text-slate-500 leading-relaxed max-w-lg mx-auto">
          Portal ini dirancang untuk memudahkan Anda melakukan proses pendaftaran kuliah secara praktis, terintegrasi, dan aman. Ikuti instruksi pendaftaran berikut untuk memulai.
        </p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-left max-w-2xl mx-auto my-6">
        <div class="bg-indigo-50/40 p-4 rounded-2xl border border-indigo-100/40 space-y-1">
          <span class="text-lg">📝</span>
          <h4 class="text-xs font-bold text-slate-800">1. Pilih Gelombang & Isi Formulir</h4>
          <p class="text-[10px] text-slate-500 leading-normal">Pilih periode gelombang aktif, isi data diri singkat, serta tentukan pilihan program studi impian Anda.</p>
        </div>
        <div class="bg-indigo-50/40 p-4 rounded-2xl border border-indigo-100/40 space-y-1">
          <span class="text-lg">💳</span>
          <h4 class="text-xs font-bold text-slate-800">2. Pembayaran & Upload Berkas</h4>
          <p class="text-[10px] text-slate-500 leading-normal">Bayar biaya administrasi formulir, lalu unggah dokumen kelengkapan berkas pendaftaran Anda.</p>
        </div>
        <div class="bg-indigo-50/40 p-4 rounded-2xl border border-indigo-100/40 space-y-1">
          <span class="text-lg">⚡</span>
          <h4 class="text-xs font-bold text-slate-800">3. Ujian Seleksi & Hasil</h4>
          <p class="text-[10px] text-slate-500 leading-normal">Mengikuti tahapan ujian seleksi masuk dan dapatkan pengumuman hasil kelulusan langsung di portal ini.</p>
        </div>
      </div>

      <div class="space-y-2">
        <p class="text-[11px] text-slate-400 font-semibold uppercase tracking-wider">Silakan pilih gelombang untuk memulai</p>
      </div>

      <form data-spa method="POST" action="<?= getBaseUrl('/dashboard/init-registration') ?>" onsubmit="return validateInitForm(event)" class="space-y-4 max-w-md mx-auto text-left bg-slate-50 p-6 rounded-2xl border border-slate-150">
        <div class="space-y-1">
          <label for="wave_id" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Gelombang Pendaftaran <span class="text-red-550">*</span></label>
          <select id="wave_id" name="wave_id" class="block w-full px-3 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-white font-medium">
            <option value="" disabled selected>Pilih Gelombang</option>
            <?php foreach ($active_waves as $w): ?>
              <option value="<?= $w['id'] ?>"><?= htmlspecialchars($w['name']) ?> (Tahun Akademik: <?= htmlspecialchars($w['academic_year']) ?>) — Periode: <?= date('d M Y', strtotime($w['start_date'])) ?> s/d <?= date('d M Y', strtotime($w['end_date'])) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <button type="submit" class="w-full inline-flex items-center justify-center px-6 py-3 border border-transparent rounded-xl shadow-md text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 transition-all cursor-pointer">
          Mulai Pendaftaran
        </button>
      </form>
    </div>

  <?php elseif ($state === 'draft'): ?>
    <?php require __DIR__ . '/../pendaftaran/form_embed.php'; ?>

  <?php elseif ($state === 'belum_bayar'): ?>
    <div class="space-y-6">
      <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 border-b border-slate-100 pb-4">
        <div class="flex items-start gap-4">
          <span class="text-4xl">💳</span>
          <div>
            <h2 class="text-xl font-bold text-slate-900">Menunggu Pembayaran Biaya Formulir</h2>
            <p class="text-sm text-slate-500">Silakan lakukan transfer pembayaran biaya formulir awal sebelum melanjutkan pengunggahan berkas.</p>
          </div>
        </div>
        <div class="flex items-center gap-2 shrink-0">
          <a data-spa href="<?= getBaseUrl('/pendaftaran') ?>" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold rounded-xl text-xs transition-colors shadow-sm border border-indigo-100">
            ✏️ Ubah Data Formulir
          </a>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div class="space-y-4">
          <div class="flex items-center gap-2 border-b border-slate-100 pb-3 hidden">
            <button type="button" id="btn-pay-manual" onclick="setPaymentMethod('manual')" class="px-4 py-2 text-xs font-bold rounded-xl border transition-all cursor-pointer">
              🏦 Transfer Bank Manual
            </button>
            <button type="button" id="btn-pay-va" onclick="setPaymentMethod('va')" class="px-4 py-2 text-xs font-bold rounded-xl border transition-all cursor-pointer">
              ⚡ Virtual Account (VA)
            </button>
          </div>

          <div id="details-manual" class="bg-slate-50 p-6 rounded-2xl border border-slate-100 space-y-4">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Detail Rekening Tujuan</h3>
            <div class="space-y-2.5">
              <div class="flex justify-between items-center text-xs border-b border-slate-200/50 pb-2">
                <span class="text-slate-500">Bank Tujuan</span>
                <strong class="text-slate-800"><?= htmlspecialchars($active_payment_account['bank_name'] ?? '-') ?></strong>
              </div>
              <div class="flex justify-between items-center text-xs border-b border-slate-200/50 pb-2">
                <span class="text-slate-500">Nomor Rekening</span>
                <strong class="text-slate-800 tracking-wider"><?= htmlspecialchars($active_payment_account['account_number'] ?? '-') ?></strong>
              </div>
              <div class="flex justify-between items-center text-xs border-b border-slate-200/50 pb-2">
                <span class="text-slate-500">Atas Nama</span>
                <strong class="text-slate-800"><?= htmlspecialchars($active_payment_account['account_holder'] ?? '-') ?></strong>
              </div>
              <div class="flex justify-between items-center text-xs">
                <span class="text-slate-500">Jumlah Nominal</span>
                <?php $feeAmount = $payment ? (float)$payment['amount'] : (float)($wave['registration_fee_total'] ?? 0); ?>
                <strong class="text-indigo-650 font-extrabold text-sm">Rp <?= number_format($feeAmount, 0, ',', '.') ?>,-</strong>
              </div>
            </div>
            <p class="text-[10px] text-amber-600 font-semibold leading-relaxed">Penting: Harap transfer tepat sesuai nominal unik di atas (termasuk 3 digit terakhir) agar proses verifikasi berjalan lancar.</p>
          </div>

          <div id="details-va" class="bg-slate-50 p-6 rounded-2xl border border-slate-100 space-y-4 hidden">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Detail Virtual Account</h3>
            <div class="space-y-2.5">
              <div class="flex justify-between items-center text-xs border-b border-slate-200/50 pb-2">
                <span class="text-slate-500">Nomor VA</span>
                <strong class="text-indigo-650 font-extrabold text-sm tracking-wider"><?= '8000' . str_pad($payment['id_payment'] ?? 1, 3, '0', STR_PAD_LEFT) ?></strong>
              </div>
              <div class="flex justify-between items-center text-xs border-b border-slate-200/50 pb-2">
                <span class="text-slate-500">Nama VA</span>
                <strong class="text-slate-800"><?= htmlspecialchars($registration['full_name']) ?></strong>
              </div>
              <div class="flex justify-between items-center text-xs border-b border-slate-200/50 pb-2">
                <span class="text-slate-500">Institusi</span>
                <strong class="text-slate-800">Kampus Mandiri Kencana</strong>
              </div>
              <div class="flex justify-between items-center text-xs">
                <span class="text-slate-500">Jumlah Nominal</span>
                <?php $feeAmount = $payment ? (float)$payment['amount'] : (float)($wave['registration_fee_total'] ?? 0); ?>
                <strong class="text-indigo-650 font-extrabold text-sm">Rp <?= number_format($feeAmount, 0, ',', '.') ?>,-</strong>
              </div>
            </div>
            <p class="text-[10px] text-slate-400 leading-relaxed">Virtual Account Anda aktif 24 jam. Pembayaran dapat dilakukan melalui ATM, Mobile Banking, atau Internet Banking.</p>
          </div>
        </div>

        <form action="<?= getBaseUrl('/pendaftaran/pembayaran/upload') ?>" method="POST" enctype="multipart/form-data" class="space-y-4">
          <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Konfirmasi Pembayaran</h3>
          <div class="space-y-3 text-xs">
            <div class="space-y-1">
              <label for="bank_name" class="block font-bold text-slate-500">Bank Asal Anda</label>
              <input type="text" id="bank_name" name="bank_name" required class="block w-full px-3 py-2 border border-slate-200 rounded-xl focus:ring-1 focus:ring-indigo-500 bg-white" placeholder="Contoh: BCA, Mandiri, BRI">
            </div>

            <div class="space-y-1">
              <label for="account_name" class="block font-bold text-slate-500">Nama Pemilik Rekening</label>
              <input type="text" id="account_name" name="account_name" required class="block w-full px-3 py-2 border border-slate-200 rounded-xl focus:ring-1 focus:ring-indigo-500 bg-white" placeholder="Nama sesuai di buku tabungan">
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div class="space-y-1">
                <label for="amount" class="block font-bold text-slate-500">Jumlah Transfer</label>
                <input type="text" id="amount" name="amount" readonly value="<?= number_format($feeAmount, 0, ',', '.') ?>" class="block w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-100 text-slate-655 font-bold cursor-not-allowed">
              </div>
              <div class="space-y-1">
                <label for="payment_date" class="block font-bold text-slate-500">Tanggal Transfer</label>
                <input type="date" id="payment_date" name="payment_date" required class="block w-full px-3 py-2 border border-slate-200 rounded-xl focus:ring-1 focus:ring-indigo-500 bg-white">
              </div>
            </div>

            <div class="space-y-1">
              <label class="block font-bold text-slate-500">Unggah Bukti Transfer</label>
              <input type="file" name="proof" required class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-3 file:rounded-full file:border-0 file:text-[10px] file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-slate-200 rounded-xl p-1.5 cursor-pointer bg-white" accept=".pdf,.jpg,.jpeg,.png">
            </div>

            <button type="submit" class="w-full inline-flex items-center justify-center px-5 py-2.5 rounded-xl shadow-sm text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 transition-colors">
              Kirim Bukti Pembayaran
            </button>
          </div>
        </form>
      </div>

      <?php
      $feeArchive = get_setting('registration_fee_archive', '');
      if (!empty($feeArchive)):
      ?>
        <div class="space-y-3 pt-2">
          <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Rincian Brosur & Rincian Biaya</h3>
          <div class="w-full overflow-hidden rounded-2xl border border-slate-200 shadow-sm bg-slate-50">
            <object data="<?= htmlspecialchars($feeArchive) ?>#toolbar=1" type="application/pdf" class="w-full h-[600px] border-none" style="display:block;">
              <iframe src="<?= htmlspecialchars($feeArchive) ?>#toolbar=1" class="w-full h-[600px] border-none" style="display:block;"></iframe>
            </object>
          </div>
        </div>
      <?php endif; ?>
    </div>

  <?php elseif ($state === 'verifikasi_pembayaran'): ?>
    <div class="max-w-xl mx-auto text-center space-y-6">
      <div class="flex items-center justify-center mx-auto text-slate-400">
        <i data-lucide="clock" class="w-12 h-12"></i>
      </div>
      <div class="space-y-2">
        <h2 class="text-xl font-extrabold text-slate-800 tracking-tight">Verifikasi Pembayaran Formulir</h2>
        <p class="text-xs text-slate-500 leading-relaxed max-w-md mx-auto">
          Bukti pembayaran pendaftaran Anda telah kami terima. Saat ini, tim kami sedang melakukan verifikasi data transfer Anda. Harap tunggu proses konfirmasi ini.
        </p>
      </div>
    </div>

  <?php elseif ($state === 'upload_berkas'): ?>
    <div class="space-y-6">
      <div class="flex items-start gap-4 border-b border-slate-100 pb-4">
        <span class="text-4xl">📎</span>
        <div>
          <h2 class="text-xl font-bold text-slate-900">Unggah Berkas Persyaratan Akademik</h2>
          <p class="text-sm text-slate-500">Pembayaran pendaftaran Anda telah terkonfirmasi. Silakan upload scan berkas dokumen persyaratan Anda.</p>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-4">
          <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Status Dokumen</h3>
          <div class="border border-slate-200/80 rounded-2xl overflow-hidden bg-white divide-y divide-slate-100">
            <?php
            if (empty($required_docs)):
            ?>
              <div class="p-8 text-center text-xs text-slate-500 font-semibold">Tidak ada dokumen persyaratan yang harus diunggah untuk program studi pilihan Anda.</div>
            <?php else: ?>
              <div class="divide-y divide-slate-100 bg-white">
                <?php foreach ($required_docs as $rd): ?>
                  <?php
                  $docTypeId = $rd['document_type_id'] ?? null;
                  $uploaded = $docTypeId ? ($uploaded_docs[$docTypeId . '_global'] ?? null) : null;
                  $docDisplayName = htmlspecialchars($rd['name']);

                  $prodisStr = implode(', ', $rd['prodi_names'] ?? []);
                  $descParts = [];
                  foreach ($rd['descriptions'] ?? [] as $pName => $pDesc) {
                    if (!empty($pDesc)) {
                      $descParts[] = $pName . ': ' . $pDesc;
                    }
                  }
                  $descStr = (!empty($descParts) ? ' (' . implode('; ', $descParts) . ')' : '');
                  ?>
                  <div class="p-4 flex justify-between items-center gap-4">
                    <div class="min-w-0 flex-1">
                      <span class="text-sm font-semibold text-slate-800"><?= $docDisplayName ?></span>
                      <span class="text-[10px] text-slate-400 block">Prodi: <span class="text-indigo-700 font-semibold"><?= $prodisStr ?></span><?= $descStr ?></span>
                    </div>
                    <div class="shrink-0">
                      <?php if ($uploaded): ?>
                        <?php if ($uploaded['status'] === 'Pending'): ?>
                          <span class="text-xs font-semibold text-amber-700 bg-amber-50 px-2.5 py-1 rounded-xl border border-amber-100 whitespace-nowrap">Menunggu Verifikasi</span>
                        <?php elseif ($uploaded['status'] === 'Approved'): ?>
                          <span class="text-xs font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-xl border border-emerald-100 whitespace-nowrap">Disetujui</span>
                        <?php elseif ($uploaded['status'] === 'Rejected'): ?>
                          <span class="text-xs font-semibold text-red-700 bg-red-50 px-2.5 py-1 rounded-xl border border-red-100 whitespace-nowrap">Ditolak</span>
                        <?php endif; ?>
                      <?php else: ?>
                        <span class="text-xs font-semibold text-red-650 bg-red-50 px-2.5 py-1 rounded-xl border border-red-100 whitespace-nowrap">Belum Ada</span>
                      <?php endif; ?>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
        </div>

        <div class="bg-indigo-50/50 p-6 rounded-2xl border border-indigo-100 space-y-4 h-fit text-center">
          <div class="text-3xl">📤</div>
          <div class="space-y-1">
            <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Kelola Berkas</h4>
            <p class="text-[11px] text-slate-500 leading-relaxed">Kelola, unggah ulang, atau ganti berkas dokumen persyaratan akademik secara langsung.</p>
          </div>
          <a data-spa href="<?= getBaseUrl('/pendaftaran/dokumen') ?>" class="w-full inline-flex items-center justify-center px-4 py-2.5 rounded-full text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 transition-colors shadow-sm">
            Mulai Unggah Dokumen
          </a>
        </div>
      </div>
    </div>

  <?php elseif ($state === 'verifikasi_berkas'): ?>
    <div class="text-center space-y-6 py-6 max-w-lg mx-auto">
      <div class="text-5xl animate-pulse">📁</div>
      <div class="space-y-2">
        <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Dokumen Berkas Sedang Diverifikasi</h2>
        <p class="text-sm text-slate-500 leading-relaxed">Seluruh berkas dokumen persyaratan Anda telah lengkap dikirimkan. Saat ini verifikator akademik kami sedang memeriksa validitas dokumen Anda.</p>
      </div>
      <div class="inline-flex items-center gap-2 bg-amber-50 text-amber-700 text-xs px-4 py-2 rounded-full font-bold">
        <span class="h-2 w-2 rounded-full bg-amber-500 animate-ping"></span>
        Menunggu Verifikasi Berkas Akademik
      </div>
      <div class="pt-2">
        <a data-spa href="<?= getBaseUrl('/pendaftaran/dokumen') ?>" class="inline-flex items-center justify-center px-6 py-3 border border-slate-200 rounded-full shadow-sm text-sm font-semibold text-slate-700 bg-white hover:bg-slate-50 transition-all hover:-translate-y-0.5 cursor-pointer">
          👁️ Lihat Berkas Saya
        </a>
      </div>
    </div>

  <?php elseif ($state === 'ujian_seleksi'): ?>
    <div class="space-y-8">
      <div class="flex items-start gap-4 border-b border-slate-100 pb-4">
        <span class="text-4xl">📝</span>
        <div>
          <h2 class="text-xl font-bold text-slate-900">Kartu Jadwal Ujian Seleksi PMB</h2>
          <p class="text-sm text-slate-500">Berkas dokumen Anda dinyatakan valid. Silakan ikuti tahapan ujian seleksi masuk di bawah ini.</p>
        </div>
      </div>

      <?php
      $stages = json_decode($wave['exam_stages'] ?? '[]', true) ?: [];
      ?>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 pl-2">
        <div class="lg:col-span-2 space-y-4">
          <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Daftar Tahapan Ujian Gelombang</h3>

          <?php if (empty($stages)): ?>
            <div class="p-6 bg-slate-50 border rounded-2xl text-center text-xs text-slate-550 font-semibold italic">
              Belum ada jadwal tahapan ujian yang ditentukan untuk gelombang ini.
            </div>
            <?php else: foreach ($stages as $stg):
              $res = array_values(array_filter(
                $exam_results,
                fn($r) =>
                $r['stage_index'] == $stg['stage_number']
              ))[0] ?? null;
              $status = $res ? $res['status'] : 'Pending';
            ?>
              <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm space-y-3">
                <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                  <span class="text-xs font-extrabold text-indigo-755 font-sans">Tahap <?= $stg['stage_number'] ?>: <?= htmlspecialchars($stg['description'] ?: 'Ujian Masuk') ?></span>
                  <span class="inline-flex px-2.5 py-0.5 rounded-full text-[9px] font-extrabold <?= $status === 'Lulus' ? 'bg-emerald-100 text-emerald-800' : ($status === 'Tidak Lulus' ? 'bg-red-100 text-red-800' : 'bg-slate-150 text-slate-600') ?>">
                    <?= $status === 'Lulus' ? 'LOLOS' : ($status === 'Tidak Lulus' ? 'GAGAL' : 'BELUM UJIAN') ?>
                  </span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                  <div>
                    <span class="text-slate-400 block text-[10px] font-bold uppercase">Tanggal & Jam</span>
                    <strong class="text-slate-800 font-semibold"><?= htmlspecialchars($stg['date']) ?> (<?= htmlspecialchars($stg['time'] ?? '') ?>)</strong>
                  </div>
                  <div>
                    <span class="text-slate-400 block text-[10px] font-bold uppercase">Tipe & Tempat</span>
                    <strong class="text-slate-800 font-semibold"><?= htmlspecialchars($stg['place'] ?? '') ?> (<?= strtoupper($stg['type'] ?? 'OFFLINE') ?>)</strong>
                  </div>
                </div>
              </div>
          <?php endforeach;
          endif; ?>
        </div>

        <div class="bg-indigo-50 p-6 rounded-2xl border border-indigo-100 space-y-3 flex flex-col justify-between h-fit">
          <div>
            <h4 class="font-bold text-indigo-900 text-sm">Cetak Kartu Ujian</h4>
            <p class="text-xs text-indigo-700 mt-1">Kartu ujian wajib dicetak dan dibawa saat pelaksanaan tes seleksi fisik di kampus.</p>
          </div>
          <a href="<?= getBaseUrl('/pendaftaran/kartu-ujian') ?>" download class="inline-flex items-center justify-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-full transition-colors mt-4 text-center">
            🖨️ Cetak Kartu Ujian
          </a>
        </div>
      </div>
    </div>

  <?php elseif ($state === 'lolos'): ?>
    <?php if ($re_registration && $re_registration['status'] === 'Rejected'): ?>
      <div class="text-center space-y-6 py-6 max-w-lg mx-auto">
        <div class="text-6xl text-red-500 animate-pulse">⚠️</div>
        <div class="space-y-2">
          <span class="bg-red-100 text-red-800 text-[10px] px-3 py-1 rounded-full font-bold uppercase tracking-wider">Perbaikan Registrasi</span>
          <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Daftar Ulang Ditolak</h2>
          <p class="text-sm text-slate-500 leading-relaxed">Berkas daftar ulang atau bukti transfer pembayaran Anda ditolak oleh panitia PMB. Silakan lakukan perbaikan berkas agar pendaftaran Anda dapat diverifikasi ulang.</p>
        </div>

        <div class="bg-red-50 border border-red-100 p-5 rounded-2xl text-left space-y-2 max-w-md mx-auto">
          <div class="flex items-center gap-2 text-red-800 font-bold text-xs">
            <span>❌</span>
            <span>Alasan Penolakan:</span>
          </div>
          <div class="text-slate-700 font-medium text-xs leading-relaxed pl-6">
            "<?= htmlspecialchars($re_registration['rejection_reason'] ?: 'Berkas pendaftaran atau bukti pembayaran kurang lengkap/tidak sesuai.') ?>"
          </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 justify-center pt-2">
          <a href="<?= getBaseUrl('/pendaftaran/kelulusan/download') ?>" download class="inline-flex items-center justify-center px-6 py-3 border border-slate-200 rounded-full shadow-sm text-sm font-semibold text-slate-700 bg-white hover:bg-slate-50 transition-all">
            📄 Cetak Surat Kelulusan
          </a>
          <a data-spa href="<?= getBaseUrl('/pendaftaran/daftar-ulang') ?>" class="inline-flex items-center justify-center px-6 py-3 border border-transparent rounded-full shadow-md text-sm font-semibold text-white bg-red-600 hover:bg-red-700 transition-all hover:-translate-y-0.5 cursor-pointer">
            ✏️ Perbaiki Daftar Ulang
          </a>
        </div>
      </div>
    <?php else: ?>
      <div class="text-center space-y-6 py-6 max-w-lg mx-auto">
        <div class="text-6xl animate-bounce">🎉</div>
        <div class="space-y-2">
          <span class="bg-emerald-100 text-emerald-800 text-[10px] px-3 py-1 rounded-full font-bold uppercase tracking-wider">Selamat! Anda Dinyatakan Lolos</span>
          <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Diterima Sebagai Mahasiswa</h2>
          <p class="text-sm text-slate-500 leading-relaxed">Selamat! Anda dinyatakan lulus seleksi PMB pada Program Studi <strong><?= htmlspecialchars($passed_program['name'] ?? '-') ?></strong>.</p>
        </div>

        <?php if (!empty($selection_result['notes'])): ?>
          <div class="bg-emerald-50/50 p-6 rounded-2xl border border-emerald-100/60 text-xs space-y-2 max-w-sm mx-auto text-left">
            <h4 class="font-bold text-slate-850 border-b border-emerald-100 pb-1.5 uppercase tracking-wider text-[10px]">Catatan Panitia</h4>
            <div class="text-slate-650 italic">
              "<?= htmlspecialchars($selection_result['notes']) ?>"
            </div>
          </div>
        <?php endif; ?>

        <div class="space-y-4 pt-2">
          <div class="max-w-sm mx-auto p-5 rounded-3xl border border-slate-200/80 bg-white text-left space-y-4 shadow-sm">
            <h4 class="text-xs font-bold text-slate-750 border-b border-slate-100 pb-2 flex items-center gap-1.5">
              👤 Checklist Kelengkapan Profil
            </h4>
            <div class="space-y-2.5 text-xs">
              <div class="flex items-center justify-between">
                <span class="text-slate-500 font-medium">Alamat & Kontak</span>
                <?php if ($profile_addr_completed): ?>
                  <span class="flex items-center justify-center w-5 h-5 rounded-full bg-emerald-100 text-emerald-800 font-extrabold text-[10px]">✓</span>
                <?php else: ?>
                  <span class="flex items-center justify-center w-5 h-5 rounded-full bg-red-100 text-red-800 font-extrabold text-[10px]">✗</span>
                <?php endif; ?>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-slate-500 font-medium">Orang Tua / Wali</span>
                <?php if ($profile_parent_completed): ?>
                  <span class="flex items-center justify-center w-5 h-5 rounded-full bg-emerald-100 text-emerald-800 font-extrabold text-[10px]">✓</span>
                <?php else: ?>
                  <span class="flex items-center justify-center w-5 h-5 rounded-full bg-red-100 text-red-800 font-extrabold text-[10px]">✗</span>
                <?php endif; ?>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-slate-500 font-medium">Riwayat Pendidikan</span>
                <?php if ($profile_edu_completed): ?>
                  <span class="flex items-center justify-center w-5 h-5 rounded-full bg-emerald-100 text-emerald-800 font-extrabold text-[10px]">✓</span>
                <?php else: ?>
                  <span class="flex items-center justify-center w-5 h-5 rounded-full bg-red-100 text-red-800 font-extrabold text-[10px]">✗</span>
                <?php endif; ?>
              </div>
            </div>
            <div class="pt-3 border-t border-slate-100 text-center">
              <a data-spa href="<?= getBaseUrl('/profile') ?>" class="text-[10px] text-indigo-650 hover:underline font-bold">Lengkapi Profil Saya →</a>
            </div>
          </div>

          <?php if ($re_registration): ?>
            <div class="max-w-sm mx-auto p-4 rounded-2xl border text-left flex items-start gap-3 <?= $re_registration['status'] === 'Approved' ? 'bg-emerald-50 border-emerald-100 text-emerald-800' : 'bg-amber-50 border-amber-100 text-amber-800' ?>">
              <span class="text-xl"><?= $re_registration['status'] === 'Approved' ? '✅' : '⏳' ?></span>
              <div class="flex-1">
                <h5 class="font-bold text-xs">Status Daftar Ulang: <?= $re_registration['status'] === 'Approved' ? 'Disetujui' : 'Menunggu Verifikasi' ?></h5>
                <p class="text-[10px] <?= $re_registration['status'] === 'Approved' ? 'text-emerald-650' : 'text-amber-650' ?> mt-0.5"><?= $re_registration['status'] === 'Approved' ? 'Selamat! Anda resmi menjadi mahasiswa baru.' : 'Kelengkapan data diri Anda sementara ditinjau dari sisi akademik.' ?></p>

                <?php if ($re_registration['status'] === 'Approved'): ?>
                  <div class="mt-2.5 p-2 bg-emerald-150 rounded-xl border border-emerald-250 text-center">
                    <span class="block text-[9px] text-emerald-800 uppercase font-bold tracking-wider">NIM Anda</span>
                    <strong class="block text-sm text-emerald-950 font-extrabold tracking-widest select-all"><?= !empty($registration['nim']) ? htmlspecialchars($registration['nim']) : 'PENDING' ?></strong>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          <?php endif; ?>

          <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="<?= getBaseUrl('/pendaftaran/kelulusan/download') ?>" download class="inline-flex items-center justify-center px-6 py-3 border border-slate-200 rounded-full shadow-sm text-sm font-semibold text-slate-700 bg-white hover:bg-slate-50 transition-all">
              📄 Cetak Surat Kelulusan
            </a>
            <a data-spa href="<?= getBaseUrl('/pendaftaran/daftar-ulang') ?>" class="inline-flex items-center justify-center px-6 py-3 border border-transparent rounded-full shadow-md text-sm font-semibold text-white <?= $re_registration && $re_registration['status'] === 'Approved' ? 'bg-slate-800 hover:bg-slate-900' : 'bg-emerald-600 hover:bg-emerald-700' ?> transition-all hover:-translate-y-0.5">
              💳 <?= $re_registration ? ($re_registration['status'] === 'Approved' ? 'Lihat Bukti Daftar Ulang' : 'Detail Daftar Ulang') : 'Lanjut Daftar Ulang' ?>
            </a>
          </div>
        </div>
      </div>
    <?php endif; ?>

  <?php elseif ($state === 'tidak_lolos'): ?>
    <div class="text-center space-y-6 py-6 max-w-lg mx-auto">
      <div class="text-5xl">🕊️</div>
      <div class="space-y-2">
        <span class="bg-red-100 text-red-800 text-[10px] px-3 py-1 rounded-full font-bold uppercase tracking-wider">Hasil Pengumuman Seleksi</span>
        <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Mohon Maaf, Anda Belum Lolos</h2>
        <p class="text-sm text-slate-500 leading-relaxed">Terima kasih telah mendaftar dan mengikuti seluruh proses seleksi PMB KMK tahun ini. Berdasarkan hasil rapat panitia seleksi, Anda dinyatakan belum berhasil lolos kuota seleksi utama program studi pilihan Anda kali ini.</p>
      </div>

      <?php if (!empty($selection_result['notes'])): ?>
        <div class="bg-red-50/50 p-4 rounded-2xl border border-red-100 text-xs text-red-750 text-left max-w-sm mx-auto">
          <strong>Catatan Panitia:</strong>
          <p class="mt-1 italic text-red-700">"<?= htmlspecialchars($selection_result['notes']) ?>"</p>
        </div>
      <?php endif; ?>
    </div>
  <?php endif; ?>
</div>

<script>
  function validateInitForm(event) {
    const waveSelect = document.getElementById('wave_id');
    const waveId = waveSelect ? waveSelect.value : '';
    if (!waveId) {
      event.preventDefault();
      event.stopPropagation();
      Swal.fire({
        icon: 'warning',
        title: 'Gelombang Belum Dipilih',
        text: 'Silakan pilih salah satu gelombang pendaftaran terlebih dahulu untuk memulai pendaftaran.',
        confirmButtonColor: '#4f46e5',
        customClass: {
          popup: 'rounded-3xl',
          confirmButton: 'rounded-xl text-xs font-bold px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white'
        }
      });
      return false;
    }
    return true;
  }

  function setPaymentMethod(method) {
    const btnManual = document.getElementById('btn-pay-manual');
    const btnVa = document.getElementById('btn-pay-va');
    const containerManual = document.getElementById('details-manual');
    const containerVa = document.getElementById('details-va');
    
    const inputBank = document.getElementById('bank_name');
    const inputAccount = document.getElementById('account_name');

    if (!btnManual || !btnVa) return;

    if (method === 'manual') {
      btnManual.className = 'px-4 py-2 text-xs font-bold rounded-xl border border-indigo-600 bg-indigo-50 text-indigo-700 shadow-sm cursor-pointer';
      btnVa.className = 'px-4 py-2 text-xs font-bold rounded-xl border border-slate-200 bg-white text-slate-655 hover:bg-slate-50 cursor-pointer';
      
      if (containerManual) containerManual.classList.remove('hidden');
      if (containerVa) containerVa.classList.add('hidden');
      
      if (inputBank) {
        if (inputBank.value === 'Virtual Account (VA)') {
          inputBank.value = '';
        }
        inputBank.readOnly = false;
        inputBank.placeholder = 'Contoh: BCA, Mandiri, BRI';
      }
      
      if (inputAccount) {
        if (inputAccount.value === <?= json_encode($registration['full_name'] ?? '') ?>) {
          inputAccount.value = '';
        }
        inputAccount.readOnly = false;
        inputAccount.placeholder = 'Nama sesuai di buku tabungan';
      }
    } else {
      btnVa.className = 'px-4 py-2 text-xs font-bold rounded-xl border border-indigo-600 bg-indigo-50 text-indigo-700 shadow-sm cursor-pointer';
      btnManual.className = 'px-4 py-2 text-xs font-bold rounded-xl border border-slate-200 bg-white text-slate-655 hover:bg-slate-50 cursor-pointer';
      
      if (containerVa) containerVa.classList.remove('hidden');
      if (containerManual) containerManual.classList.add('hidden');
      
      if (inputBank) {
        inputBank.value = 'Virtual Account (VA)';
        inputBank.readOnly = true;
      }
      
      if (inputAccount) {
        inputAccount.value = <?= json_encode($registration['full_name'] ?? '') ?>;
        inputAccount.readOnly = true;
      }
    }

    fetch('<?= getBaseUrl('/pendaftaran/pembayaran/change-type') ?>', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      body: 'payment_type=' + method
    });
  }

  document.addEventListener('DOMContentLoaded', function() {
    <?php if ($state === 'belum_bayar'): ?>
      setPaymentMethod('manual');
    <?php endif; ?>
  });
</script>