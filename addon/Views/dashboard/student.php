<div class="w-full py-2 space-y-10">
  <!-- Active Announcement Alert -->
  <?php if ($active_announcement): ?>
    <div class="bg-indigo-50 border border-indigo-200/60 rounded-3xl p-6 md:p-8 flex gap-4 items-start shadow-sm">
      <span class="text-3xl">📢</span>
      <div class="space-y-1 text-xs">
        <h4 class="font-bold text-indigo-900 text-sm"><?= htmlspecialchars($active_announcement['title']) ?></h4>
        <p class="text-slate-600 leading-relaxed"><?= nl2br(htmlspecialchars($active_announcement['content'])) ?></p>
      </div>
    </div>
  <?php endif; ?>

  <!-- Stepper Timeline -->
  <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 p-6 md:p-8">
    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-6 text-center">Progress Alur Pendaftaran PMB</h3>
    
    <div class="relative flex flex-col md:flex-row items-start md:items-center justify-between gap-6 md:gap-4">
      <!-- Line behind (desktop only) -->
      <div class="hidden md:block absolute top-[18px] left-[5%] right-[5%] h-0.5 bg-slate-100 -z-1"></div>

      <!-- Step 1 -->
      <?php 
        $step1_done = in_array($state, ['belum_bayar', 'verifikasi_pembayaran', 'upload_berkas', 'verifikasi_berkas', 'ujian_seleksi', 'lolos', 'tidak_lolos']);
        $step1_active = ($state === 'belum_daftar');
        $step1_editable = !in_array($state, ['lolos', 'tidak_lolos']);
      ?>
      <?php if ($step1_editable): ?>
        <a data-spa href="/pendaftaran" class="flex md:flex-col items-center gap-4 md:gap-2 flex-1 w-full text-left md:text-center z-10 hover:opacity-80 transition-opacity" title="Klik untuk mengedit data formulir">
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
      <?php if ($step1_editable): ?>
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
          <span class="text-[10px] text-slate-400 font-medium block">Biaya Pendaftaran</span>
        </div>
      </div>

      <!-- Step 3 -->
      <?php 
        $step3_done = in_array($state, ['ujian_seleksi', 'lolos', 'tidak_lolos']);
        $step3_active = in_array($state, ['upload_berkas', 'verifikasi_berkas']);
        $step3_clickable = ($step3_done || $step3_active);
      ?>
      <?php if ($step3_clickable): ?>
        <a data-spa href="/pendaftaran/dokumen" class="flex md:flex-col items-center gap-4 md:gap-2 flex-1 w-full text-left md:text-center z-10 hover:opacity-80 transition-opacity">
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

  <?php if ($state !== 'belum_daftar'): ?>
    <div class="bg-indigo-50/40 rounded-3xl border border-indigo-100 p-6 flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-sm">
      <div class="flex items-start gap-4">
        <span class="text-3xl">🖨️</span>
        <div class="space-y-1 text-xs">
          <h4 class="font-bold text-indigo-900 text-sm">Cetak Dokumen Pendaftaran</h4>
          <p class="text-slate-600 leading-relaxed">Unduh atau cetak dokumen resmi pendaftaran Anda untuk keperluan administratif fisik di kampus.</p>
        </div>
      </div>
      <div class="flex flex-wrap items-center gap-2">
        <a href="/pendaftaran/formulir" download class="inline-flex items-center justify-center px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 font-bold rounded-xl border border-slate-200 shadow-sm text-xs transition-colors">
          📄 Cetak Formulir
        </a>
        <?php if (in_array($state, ['ujian_seleksi', 'lolos', 'tidak_lolos'])): ?>
          <a href="/pendaftaran/kartu-ujian" download class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-sm text-xs transition-colors">
            🎟️ Cetak Kartu Ujian
          </a>
        <?php endif; ?>
      </div>
    </div>
  <?php endif; ?>

  <!-- State-Driven Content Card -->
  <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 p-8 space-y-6">
    <?php if ($state === 'belum_daftar'): ?>
      <div class="text-center space-y-6 py-6 max-w-lg mx-auto">
        <div class="text-5xl">📝</div>
        <div class="space-y-2">
          <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Formulir Pendaftaran Belum Diisi</h2>
          <p class="text-sm text-slate-500 leading-relaxed">Selamat bergabung! Langkah pertama untuk mendaftar sebagai mahasiswa baru adalah mengisi formulir data diri, data orang tua, riwayat pendidikan, dan program studi pilihan Anda.</p>
        </div>
        <div class="pt-2">
          <a data-spa href="<?= getBaseUrl('/pendaftaran') ?>" class="inline-flex items-center justify-center px-6 py-3 border border-transparent rounded-full shadow-md text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 transition-all hover:-translate-y-0.5">
            Mulai Isi Formulir
          </a>
        </div>
      </div>

    <?php elseif ($state === 'belum_bayar'): ?>
      <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 border-b border-slate-100 pb-4">
          <div class="flex items-start gap-4">
            <span class="text-4xl">💳</span>
            <div>
              <h2 class="text-xl font-bold text-slate-900">Menunggu Pembayaran Biaya Pendaftaran</h2>
              <p class="text-sm text-slate-500">Silakan lakukan transfer pembayaran biaya pendaftaran awal sebelum melanjutkan pengunggahan berkas.</p>
            </div>
          </div>
          <a data-spa href="/pendaftaran" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold rounded-xl text-xs transition-colors shadow-sm border border-indigo-100 shrink-0">
            ✏️ Ubah Data Formulir
          </a>
        </div>

        <?php if ($payment && $payment['status'] === 'Rejected'): ?>
          <div class="p-4 bg-red-50 border border-red-500 text-red-700 rounded-2xl flex flex-col gap-1">
            <span class="text-xs font-bold uppercase tracking-wider flex items-center gap-1.5">⚠️ Pembayaran Ditolak</span>
            <p class="text-xs">Alasan: <strong><?= htmlspecialchars($payment['rejection_reason']) ?></strong>. Silakan upload kembali bukti transfer yang benar.</p>
          </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100 space-y-4">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Detail Rekening Tujuan</h3>
            <div class="space-y-2.5">
              <div class="flex justify-between items-center text-xs border-b border-slate-200/50 pb-2">
                <span class="text-slate-500">Bank Tujuan</span>
                <strong class="text-slate-800"><?= htmlspecialchars($active_payment_account['bank_name'] ?? 'BANK MANDIRI') ?></strong>
              </div>
              <div class="flex justify-between items-center text-xs border-b border-slate-200/50 pb-2">
                <span class="text-slate-500">Nomor Rekening</span>
                <strong class="text-slate-800 tracking-wider"><?= htmlspecialchars($active_payment_account['account_number'] ?? '123-000-456-7890') ?></strong>
              </div>
              <div class="flex justify-between items-center text-xs border-b border-slate-200/50 pb-2">
                <span class="text-slate-500">Atas Nama</span>
                <strong class="text-slate-800"><?= htmlspecialchars($active_payment_account['account_holder'] ?? 'PANITIA PMB KMK') ?></strong>
              </div>
              <div class="flex justify-between items-center text-xs">
                <span class="text-slate-500">Jumlah Nominal</span>
                <?php $feeAmount = $wave_study_program ? (float)$wave_study_program['registration_fee_total'] : 250000; ?>
                <strong class="text-indigo-650 font-extrabold text-sm">Rp <?= number_format($feeAmount, 0, ',', '.') ?>,-</strong>
              </div>
            </div>

            <?php if ($wave_study_program && !empty($wave_study_program['registration_fee_archive'])): ?>
              <div class="pt-2">
                <a href="<?= htmlspecialchars($wave_study_program['registration_fee_archive']) ?>" download class="block text-center px-4 py-2.5 bg-white hover:bg-slate-50 text-indigo-700 border border-slate-200 shadow-sm font-bold rounded-xl text-xs transition-colors">
                  📄 Unduh Rincian Biaya (PDF)
                </a>
              </div>
            <?php endif; ?>
          </div>

          <form action="/pendaftaran/pembayaran/upload" method="POST" enctype="multipart/form-data" class="space-y-4">
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
      </div>

    <?php elseif ($state === 'verifikasi_pembayaran'): ?>
      <div class="text-center space-y-6 py-6 max-w-lg mx-auto">
        <div class="text-5xl animate-bounce">⏳</div>
        <div class="space-y-2">
          <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Bukti Pembayaran Sedang Diverifikasi</h2>
          <p class="text-sm text-slate-500 leading-relaxed">Bukti transfer pembayaran biaya pendaftaran Anda telah kami terima dan sedang diproses oleh Tim Keuangan kami. Proses verifikasi biasanya memakan waktu maksimal 24 jam kerja.</p>
        </div>
        <div class="inline-flex items-center gap-2 bg-indigo-50 text-indigo-700 text-xs px-4 py-2 rounded-full font-bold">
          <span class="animate-spin rounded-full h-3 w-3 border-2 border-indigo-700 border-t-transparent"></span>
          Menunggu Konfirmasi Keuangan
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
            <div class="divide-y divide-slate-100 border border-slate-200/80 rounded-2xl overflow-hidden bg-white">
              <?php 
              $reqDocs = [];
              if ($wave_study_program) {
                  $reqDocs = json_decode($wave_study_program['required_documents'] ?? '[]', true) ?: [];
              }
              if (empty($reqDocs)):
              ?>
                <div class="p-8 text-center text-xs text-slate-500 font-semibold">Tidak ada dokumen persyaratan yang harus diunggah untuk program studi pilihan Anda.</div>
              <?php else: foreach ($reqDocs as $rd): ?>
                <?php 
                  $docTypeId = $rd['document_type_id'] ?? null;
                  $uploaded = $docTypeId ? ($uploaded_docs[$docTypeId] ?? null) : null;
                ?>
                <div class="p-4 flex justify-between items-center">
                  <div>
                    <span class="text-sm font-semibold text-slate-800"><?= htmlspecialchars($rd['name'] ?? '-') ?></span>
                    <span class="text-[10px] text-slate-400 block"><?= htmlspecialchars($rd['description'] ?? '') ?></span>
                  </div>
                  <div>
                    <?php if ($uploaded): ?>
                      <?php if ($uploaded['status'] === 'Pending'): ?>
                        <span class="text-xs font-semibold text-amber-700 bg-amber-50 px-2 py-0.5 rounded border border-amber-100">Menunggu Verifikasi</span>
                      <?php elseif ($uploaded['status'] === 'Approved'): ?>
                        <span class="text-xs font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-100">Disetujui</span>
                      <?php elseif ($uploaded['status'] === 'Rejected'): ?>
                        <span class="text-xs font-semibold text-red-700 bg-red-50 px-2 py-0.5 rounded border border-red-100">Ditolak</span>
                      <?php endif; ?>
                    <?php else: ?>
                      <span class="text-xs font-semibold text-red-650 bg-red-50 px-2 py-0.5 rounded border border-red-100">Belum Ada</span>
                    <?php endif; ?>
                  </div>
                </div>
              <?php endforeach; endif; ?>
            </div>
          </div>

          <div class="bg-indigo-50/50 p-6 rounded-2xl border border-indigo-100 space-y-4 h-fit text-center">
            <div class="text-3xl">📤</div>
            <div class="space-y-1">
              <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Kelola Berkas</h4>
              <p class="text-[11px] text-slate-500 leading-relaxed">Kelola, unggah ulang, atau ganti berkas dokumen persyaratan akademik secara langsung.</p>
            </div>
            <a data-spa href="/pendaftaran/dokumen" class="w-full inline-flex items-center justify-center px-4 py-2.5 rounded-full text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 transition-colors shadow-sm">
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
      </div>

    <?php elseif ($state === 'ujian_seleksi'): ?>
      <div class="space-y-6">
        <div class="flex items-start gap-4 border-b border-slate-100 pb-4">
          <span class="text-4xl">📝</span>
          <div>
            <h2 class="text-xl font-bold text-slate-900">Kartu Jadwal Ujian Seleksi PMB</h2>
            <p class="text-sm text-slate-500">Berkas dokumen Anda dinyatakan valid. Silakan ikuti tahapan ujian seleksi masuk di bawah ini.</p>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <div class="lg:col-span-2 space-y-4">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Daftar Tahapan Ujian</h3>
            
            <?php 
            $stages = [];
            if ($wave_study_program) {
              $stages = json_decode($wave_study_program['exam_stages'] ?? '[]', true) ?: [];
            }
            if (empty($stages)):
            ?>
              <div class="p-6 bg-slate-50 border rounded-2xl text-center text-xs text-slate-550 font-semibold italic">
                Belum ada jadwal tahapan ujian yang ditentukan untuk program studi pilihan Anda.
              </div>
            <?php else: foreach ($stages as $stg): 
              $res = array_values(array_filter($exam_results, fn($r) => $r['stage_index'] == $stg['stage_number']))[0] ?? null;
              $status = $res ? $res['status'] : 'Pending';
            ?>
              <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm space-y-3">
                <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                  <span class="text-xs font-extrabold text-indigo-755">Tahap <?= $stg['stage_number'] ?>: <?= htmlspecialchars($stg['description'] ?: 'Ujian Masuk') ?></span>
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
            <?php endforeach; endif; ?>
          </div>

          <div class="bg-indigo-50 p-6 rounded-2xl border border-indigo-100 space-y-3 flex flex-col justify-between h-fit">
            <div>
              <h4 class="font-bold text-indigo-900 text-sm">Cetak Kartu Ujian</h4>
              <p class="text-xs text-indigo-700 mt-1">Kartu ujian wajib dicetak dan dibawa saat pelaksanaan tes seleksi fisik di kampus.</p>
            </div>
            <a href="/pendaftaran/kartu-ujian" class="inline-flex items-center justify-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-full transition-colors mt-4 text-center">
              🖨️ Cetak Kartu CBT
            </a>
          </div>
        </div>
      </div>

    <?php elseif ($state === 'lolos'): ?>
      <div class="text-center space-y-6 py-6 max-w-lg mx-auto">
        <div class="text-6xl animate-bounce">🎉</div>
        <div class="space-y-2">
          <span class="bg-emerald-100 text-emerald-800 text-[10px] px-3 py-1 rounded-full font-bold uppercase tracking-wider">Selamat! Anda Dinyatakan Lolos</span>
          <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Diterima Sebagai Mahasiswa</h2>
          <p class="text-sm text-slate-500 leading-relaxed">Selamat! Anda dinyatakan lulus seleksi PMB pada Program Studi <strong><?= htmlspecialchars($passed_program['name'] ?? '-') ?></strong>.</p>
        </div>

        <div class="bg-emerald-50/50 p-6 rounded-2xl border border-emerald-100/60 text-xs space-y-3 max-w-sm mx-auto text-left">
          <h4 class="font-bold text-slate-800 border-b border-emerald-100 pb-1.5 uppercase tracking-wider text-[10px]">Rincian Hasil Penilaian</h4>
          <div class="flex justify-between">
            <span class="text-slate-500">Nilai Ujian CBT:</span>
            <strong class="text-slate-800"><?= number_format($selection_result['test_score'], 2) ?></strong>
          </div>
          <div class="flex justify-between">
            <span class="text-slate-500">Nilai Wawancara:</span>
            <strong class="text-slate-800"><?= number_format($selection_result['interview_score'], 2) ?></strong>
          </div>
          <?php if (!empty($selection_result['notes'])): ?>
            <div class="pt-1.5 border-t border-emerald-100 text-slate-650 italic">
              "<?= htmlspecialchars($selection_result['notes']) ?>"
            </div>
          <?php endif; ?>
        </div>
        
        <div class="space-y-4 pt-2">
          <?php if ($re_registration): ?>
            <div class="max-w-sm mx-auto p-4 rounded-2xl border text-left flex items-start gap-3 <?= $re_registration['status'] === 'Approved' ? 'bg-emerald-50 border-emerald-100 text-emerald-800' : ($re_registration['status'] === 'Rejected' ? 'bg-red-50 border-red-100 text-red-800' : 'bg-amber-50 border-amber-100 text-amber-800') ?>">
              <span class="text-xl"><?= $re_registration['status'] === 'Approved' ? '✅' : ($re_registration['status'] === 'Rejected' ? '❌' : '⏳') ?></span>
              <div class="flex-1">
                <h5 class="font-bold text-xs">Status Daftar Ulang: <?= $re_registration['status'] === 'Approved' ? 'Disetujui' : ($re_registration['status'] === 'Rejected' ? 'Ditolak' : 'Menunggu Verifikasi') ?></h5>
                <?php if ($re_registration['status'] === 'Rejected'): ?>
                  <p class="text-[10px] text-red-650 mt-1">Alasan: <?= htmlspecialchars($re_registration['rejection_reason']) ?></p>
                <?php else: ?>
                  <p class="text-[10px] <?= $re_registration['status'] === 'Approved' ? 'text-emerald-650' : 'text-amber-650' ?> mt-0.5"><?= $re_registration['status'] === 'Approved' ? 'Selamat! Anda resmi menjadi mahasiswa baru.' : 'Pembayaran Anda sedang ditinjau oleh tim akademik.' ?></p>
                <?php endif; ?>

                <?php if ($re_registration['status'] === 'Approved' && !empty($registration['nim'])): ?>
                  <div class="mt-2.5 p-2 bg-emerald-150 rounded-xl border border-emerald-250 text-center">
                    <span class="block text-[9px] text-emerald-800 uppercase font-bold tracking-wider">NIM Anda</span>
                    <strong class="block text-sm text-emerald-950 font-extrabold tracking-widest select-all"><?= htmlspecialchars($registration['nim']) ?></strong>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          <?php endif; ?>

          <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="/pendaftaran/kelulusan/download" download class="inline-flex items-center justify-center px-6 py-3 border border-slate-200 rounded-full shadow-sm text-sm font-semibold text-slate-700 bg-white hover:bg-slate-50 transition-all">
              📄 Cetak Surat Kelulusan
            </a>
            <a data-spa href="/pendaftaran/daftar-ulang" class="inline-flex items-center justify-center px-6 py-3 border border-transparent rounded-full shadow-md text-sm font-semibold text-white <?= $re_registration && $re_registration['status'] === 'Approved' ? 'bg-slate-800 hover:bg-slate-900' : 'bg-emerald-600 hover:bg-emerald-700' ?> transition-all hover:-translate-y-0.5">
              💳 <?= $re_registration ? ($re_registration['status'] === 'Approved' ? 'Lihat Bukti Daftar Ulang' : ($re_registration['status'] === 'Rejected' ? 'Perbaiki Daftar Ulang' : 'Detail Daftar Ulang')) : 'Lanjut Daftar Ulang' ?>
            </a>
          </div>
        </div>
      </div>

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

        <div class="pt-2">
          <a href="#" class="inline-flex items-center justify-center px-6 py-3 border border-slate-200 rounded-full shadow-sm text-sm font-semibold text-slate-700 bg-white hover:bg-slate-50 transition-all">
            Hubungi Panitia Seleksi
          </a>
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php /*
<!-- Floating Developer State Simulator Menu -->
<div class="fixed bottom-6 right-6 z-50">
  <div class="bg-slate-900 border border-slate-800 rounded-3xl p-5 shadow-2xl space-y-3 max-w-xs transition-all hover:scale-[1.02]">
    <div class="flex items-center justify-between gap-4">
      <span class="text-[10px] font-bold text-indigo-400 uppercase tracking-widest block">⚙️ State Simulator</span>
      <span class="bg-indigo-900 text-indigo-200 font-extrabold text-[8px] px-2 py-0.5 rounded-full uppercase">Testing Mode</span>
    </div>
    <form action="/dashboard/simulate-state" method="POST" class="space-y-2">
      <label for="state-select" class="block text-[10px] text-slate-400 leading-normal">Ubah status alur pendaftaran mahasiswa untuk menguji UI transisi:</label>
      <select 
        name="state" 
        id="state-select" 
        onchange="this.form.submit()"
        class="block w-full px-3 py-2 border border-slate-800 rounded-xl shadow-sm text-xs bg-slate-950 text-slate-200 focus:outline-none focus:ring-1 focus:ring-indigo-500"
      >
        <option value="belum_daftar" <?= $state === 'belum_daftar' ? 'selected' : '' ?>>1. Belum Mengisi Formulir</option>
        <option value="belum_bayar" <?= $state === 'belum_bayar' ? 'selected' : '' ?>>2. Menunggu Pembayaran</option>
        <option value="verifikasi_pembayaran" <?= $state === 'verifikasi_pembayaran' ? 'selected' : '' ?>>3. Verifikasi Bukti Bayar</option>
        <option value="upload_berkas" <?= $state === 'upload_berkas' ? 'selected' : '' ?>>4. Unggah Berkas Akademik</option>
        <option value="verifikasi_berkas" <?= $state === 'verifikasi_berkas' ? 'selected' : '' ?>>5. Verifikasi Dokumen</option>
        <option value="ujian_seleksi" <?= $state === 'ujian_seleksi' ? 'selected' : '' ?>>6. Jadwal Tes / CBT</option>
        <option value="lolos" <?= $state === 'lolos' ? 'selected' : '' ?>>7. Dinyatakan Lulus (Lolos)</option>
        <option value="tidak_lolos" <?= $state === 'tidak_lolos' ? 'selected' : '' ?>>8. Dinyatakan Gagal</option>
      </select>
    </form>
  </div>
</div>
*/ ?>
