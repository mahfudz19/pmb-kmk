<?php
/**
 * @var array $registration
 * @var array $selection
 * @var string $program_name
 * @var float $tuition_fee
 * @var array|null $re_registration
 */
?>

<div class="space-y-6">
  <!-- Top Bar -->
  <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-100 pb-5">
    <div class="space-y-1">
      <div class="flex items-center gap-2">
        <a data-spa href="/dashboard" class="text-xs font-bold text-indigo-650 hover:text-indigo-700 flex items-center gap-1 transition-colors">
          <span>← Kembali ke Dashboard</span>
        </a>
      </div>
      <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Daftar Ulang Mahasiswa Baru</h1>
      <p class="text-xs text-slate-500">Silakan lengkapi berkas persyaratan dan bukti pembayaran untuk menyelesaikan proses registrasi.</p>
    </div>
  </div>

  <?php if (isset($_GET['success'])): ?>
    <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-xl flex gap-3 text-emerald-800 text-xs">
      <span class="text-lg">✅</span>
      <div>
        <p class="font-bold">Berhasil!</p>
        <p class="mt-0.5"><?= htmlspecialchars($_GET['success']) ?></p>
      </div>
    </div>
  <?php endif; ?>

  <?php if (isset($_GET['error'])): ?>
    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-xl flex gap-3 text-red-800 text-xs">
      <span class="text-lg">⚠️</span>
      <div>
        <p class="font-bold">Gagal!</p>
        <p class="mt-0.5"><?= htmlspecialchars($_GET['error']) ?></p>
      </div>
    </div>
  <?php endif; ?>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left Column: Guidelines & Payment Instructions -->
    <div class="lg:col-span-1 space-y-6">
      <!-- Program Info Card -->
      <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-4">
        <h3 class="text-sm font-bold text-slate-850 border-b border-slate-100 pb-4">Informasi Program Studi</h3>
        <div class="space-y-3 pt-1">
          <div>
            <span class="text-[10px] text-slate-400 block font-medium">Program Studi Penerimaan</span>
            <span class="text-sm font-extrabold text-slate-800"><?= htmlspecialchars($program_name) ?></span>
          </div>
          <div>
            <span class="text-[10px] text-slate-400 block font-medium">Biaya Pendidikan (UKT Semester 1)</span>
            <span class="text-sm font-extrabold text-indigo-650">Rp <?= number_format($tuition_fee, 0, ',', '.') ?></span>
          </div>
        </div>
      </div>

      <!-- Bank Transfer Instructions -->
      <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-4">
        <h3 class="text-sm font-bold text-slate-850 border-b border-slate-100 pb-4">Panduan Pembayaran</h3>
        <p class="text-xs text-slate-500 leading-relaxed pt-1">Silakan transfer nominal UKT Semester 1 di atas ke rekening bank resmi kampus berikut:</p>
        <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100 space-y-3">
          <div class="flex justify-between items-center text-xs">
            <span class="text-slate-400 font-medium">Bank</span>
            <strong class="text-slate-800 font-bold">Mandiri</strong>
          </div>
          <div class="flex justify-between items-center text-xs">
            <span class="text-slate-400 font-medium">No. Rekening</span>
            <span class="flex items-center gap-1">
              <strong class="text-slate-800 font-bold">124-000-987-6543</strong>
            </span>
          </div>
          <div class="flex justify-between items-center text-xs">
            <span class="text-slate-400 font-medium">Atas Nama</span>
            <strong class="text-slate-800 font-bold">PMB KAMPUS MANDIRI KENCANA</strong>
          </div>
        </div>
        <div class="text-[11px] text-amber-600 bg-amber-50 p-3.5 rounded-xl border border-amber-100 flex gap-2">
          <span>💡</span>
          <p>Tuliskan nama lengkap pendaftar pada berita acara transfer untuk mempercepat proses verifikasi.</p>
        </div>
      </div>
    </div>

    <!-- Right Column: Re-registration Upload Form -->
    <form action="/pendaftaran/daftar-ulang/submit" method="POST" enctype="multipart/form-data" class="lg:col-span-2" style="margin: 0; padding: 0;">
      <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-6">
          <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <h2 class="text-sm font-bold text-slate-850">Upload Dokumen & Bukti Bayar</h2>
            <?php if ($re_registration): ?>
              <span class="inline-flex px-3 py-1 rounded-full text-[10px] font-bold <?= $re_registration['status'] === 'Approved' ? 'bg-emerald-100 text-emerald-800' : ($re_registration['status'] === 'Rejected' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') ?>">
                Status: <?= $re_registration['status'] === 'Approved' ? 'Disetujui' : ($re_registration['status'] === 'Rejected' ? 'Ditolak' : 'Menunggu Verifikasi') ?>
              </span>
            <?php else: ?>
              <span class="inline-flex px-3 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500">
                Belum Diajukan
              </span>
            <?php endif; ?>
          </div>

          <?php if ($re_registration && $re_registration['status'] === 'Rejected'): ?>
            <div class="bg-red-50 border border-red-200 rounded-2xl p-4 text-xs text-red-800 flex gap-2">
              <span>⚠️</span>
              <div>
                <strong class="font-bold">Alasan Penolakan:</strong>
                <p class="mt-1 leading-relaxed"><?= htmlspecialchars($re_registration['rejection_reason']) ?></p>
              </div>
            </div>
          <?php endif; ?>

          <?php 
            $isApproved = $re_registration && $re_registration['status'] === 'Approved'; 
          ?>

          <div class="space-y-4">
            <!-- SKL File -->
            <div class="p-4 rounded-2xl border border-slate-100 bg-slate-50/40 hover:bg-slate-50/70 transition-all flex flex-col sm:flex-row sm:items-center justify-between gap-4">
              <div class="space-y-1">
                <label class="block text-xs font-bold text-slate-700">Scan Ijazah Asli / SKL (PDF/JPG/PNG, Max 2MB) <span class="text-red-500">*</span></label>
                <?php if ($re_registration && $re_registration['skl_path']): ?>
                  <a href="/re-registrations/view?id=<?= $re_registration['id'] ?>&file=skl" target="_blank" class="inline-flex items-center gap-1.5 text-[10px] font-bold text-indigo-650 hover:underline">
                    👁️ Lihat Berkas Terunggah
                  </a>
                <?php endif; ?>
              </div>
              <div class="flex items-center">
                <input type="file" name="skl_file" class="text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" <?= $isApproved ? 'disabled' : '' ?> />
              </div>
            </div>

            <!-- Health Certificate File -->
            <div class="p-4 rounded-2xl border border-slate-100 bg-slate-50/40 hover:bg-slate-50/70 transition-all flex flex-col sm:flex-row sm:items-center justify-between gap-4">
              <div class="space-y-1">
                <label class="block text-xs font-bold text-slate-700">Scan Surat Keterangan Sehat / Bebas Narkoba <span class="text-red-500">*</span></label>
                <?php if ($re_registration && $re_registration['health_path']): ?>
                  <a href="/re-registrations/view?id=<?= $re_registration['id'] ?>&file=health" target="_blank" class="inline-flex items-center gap-1.5 text-[10px] font-bold text-indigo-650 hover:underline">
                    👁️ Lihat Berkas Terunggah
                  </a>
                <?php endif; ?>
              </div>
              <div class="flex items-center">
                <input type="file" name="health_file" class="text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" <?= $isApproved ? 'disabled' : '' ?> />
              </div>
            </div>

            <!-- Statement File -->
            <div class="p-4 rounded-2xl border border-slate-100 bg-slate-50/40 hover:bg-slate-50/70 transition-all flex flex-col sm:flex-row sm:items-center justify-between gap-4">
              <div class="space-y-1">
                <label class="block text-xs font-bold text-slate-700">Scan Surat Pernyataan Mahasiswa Baru <span class="text-red-500">*</span></label>
                <?php if ($re_registration && $re_registration['statement_path']): ?>
                  <a href="/re-registrations/view?id=<?= $re_registration['id'] ?>&file=statement" target="_blank" class="inline-flex items-center gap-1.5 text-[10px] font-bold text-indigo-650 hover:underline">
                    👁️ Lihat Berkas Terunggah
                  </a>
                <?php endif; ?>
              </div>
              <div class="flex items-center">
                <input type="file" name="statement_file" class="text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" <?= $isApproved ? 'disabled' : '' ?> />
              </div>
            </div>

            <!-- Payment Receipt File -->
            <div class="p-4 rounded-2xl border border-slate-100 bg-slate-50/40 hover:bg-slate-50/70 transition-all flex flex-col sm:flex-row sm:items-center justify-between gap-4">
              <div class="space-y-1">
                <label class="block text-xs font-bold text-slate-700">Bukti Pembayaran UKT Semester 1 <span class="text-red-500">*</span></label>
                <?php if ($re_registration && $re_registration['payment_path']): ?>
                  <a href="/re-registrations/view?id=<?= $re_registration['id'] ?>&file=payment" target="_blank" class="inline-flex items-center gap-1.5 text-[10px] font-bold text-indigo-650 hover:underline">
                    👁️ Lihat Bukti Terunggah
                  </a>
                <?php endif; ?>
              </div>
              <div class="flex items-center">
                <input type="file" name="payment_file" class="text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" <?= $isApproved ? 'disabled' : '' ?> />
              </div>
            </div>
          </div>

          <div class="border-t border-slate-100 pt-5 space-y-4">
            <div>
              <label for="payment_amount" class="block text-xs font-bold text-slate-700">Nominal Transfer Pembayaran (Rp) <span class="text-red-500">*</span></label>
              <div class="mt-1.5 relative rounded-xl shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none" style="position: absolute; top: 0; bottom: 0; left: 12px; display: flex; align-items: center; pointer-events: none; z-index: 10;">
                  <span class="text-xs text-slate-400 font-semibold">Rp</span>
                </div>
                <input
                  type="text"
                  name="payment_amount"
                  id="payment_amount"
                  style="padding-left: 44px !important;"
                  class="block w-full pr-4 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-xs transition-all bg-slate-50 font-semibold text-slate-800"
                  placeholder="Contoh: <?= number_format($tuition_fee, 0, '', '') ?>"
                  value="<?= $re_registration ? number_format($re_registration['payment_amount'], 0, '', '') : '' ?>"
                  <?= $isApproved ? 'disabled' : 'required' ?>
                />
              </div>
            </div>
          </div>

          <?php if (!$isApproved): ?>
            <div class="pt-2 flex justify-end">
              <button type="submit" class="inline-flex items-center justify-center px-6 py-3 border border-transparent rounded-full shadow-md text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 transition-all hover:-translate-y-0.5 cursor-pointer">
                💾 Simpan & Ajukan Daftar Ulang
              </button>
            </div>
          <?php endif; ?>
        </div>
      </form>
  </div>
</div>
