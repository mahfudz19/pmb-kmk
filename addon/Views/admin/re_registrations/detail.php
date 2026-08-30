<?php

/**
 * @var array $registration
 * @var array $selection
 * @var string $program_name
 * @var float $expected_tuition
 * @var array|null $re_registration
 */
?>

<div class="space-y-6">
  <!-- Top Bar -->
  <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-100 pb-5">
    <div class="space-y-1">
      <div class="flex items-center gap-2">
        <a data-spa href="<?= getBaseUrl('/admin/re-registrations') ?>" class="text-xs font-bold text-indigo-650 hover:text-indigo-700 flex items-center gap-1 transition-colors">
          <span>← Kembali ke Daftar Antrean</span>
        </a>
      </div>
      <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Detail Verifikasi Daftar Ulang</h1>
      <p class="text-xs text-slate-500">Tinjau kelengkapan dokumen dan pembayaran dari calon mahasiswa baru.</p>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left Panel: Applicant & Payment info -->
    <div class="lg:col-span-1 space-y-6">
      <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-4">
        <h3 class="text-xs font-bold text-slate-450 uppercase tracking-widest">Informasi Pendaftar</h3>
        <div class="space-y-3 text-xs">
          <div>
            <span class="text-[10px] text-slate-400 block font-medium">Nama Lengkap</span>
            <strong class="text-slate-800 font-bold text-sm"><?= htmlspecialchars($registration['full_name']) ?></strong>
          </div>
          <div>
            <span class="text-[10px] text-slate-400 block font-medium">Gelombang PMB</span>
            <span class="inline-flex mt-0.5 px-1.5 py-0.2 text-[10px] font-bold bg-indigo-50 border border-indigo-200 text-indigo-755 rounded"><?= htmlspecialchars($wave_name ?? '-') ?></span>
          </div>
          <div>
            <span class="text-[10px] text-slate-400 block font-medium">Email</span>
            <span class="text-slate-700 font-semibold"><?= htmlspecialchars($registration['email']) ?></span>
          </div>
          <div>
            <span class="text-[10px] text-slate-400 block font-medium">Program Studi Lulus</span>
            <span class="text-slate-700 font-semibold"><?= htmlspecialchars($program_name) ?></span>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-4">
        <h3 class="text-xs font-bold text-slate-450 uppercase tracking-widest">Rincian Pembayaran</h3>
        <div class="space-y-3 text-xs">
          <div>
            <span class="text-[10px] text-slate-400 block font-medium">Biaya Pendidikan yang Wajib Dibayar</span>
            <strong class="text-slate-800 font-bold text-sm">Rp <?= number_format($expected_tuition, 0, ',', '.') ?></strong>
          </div>
        </div>
      </div>
    </div>

    <!-- Right Panel: Review Documents & Form -->
    <div class="lg:col-span-2 space-y-6">
      <div class="bg-white rounded-3xl p-6 md:p-8 border border-slate-200/80 shadow-sm space-y-6">
        <h3 class="text-sm font-bold text-slate-800 border-b border-slate-100 pb-3">Berkas Persyaratan Daftar Ulang</h3>

        <div class="divide-y divide-slate-100">

          <!-- Payment Receipt Row -->
          <div class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
            <div>
              <h4 class="font-bold text-slate-800">Bukti Pembayaran UKT Semester 1</h4>
              <p class="text-[10px] text-slate-400 font-medium mt-0.5">Struk / bukti transfer biaya pendidikan awal.</p>
            </div>
            <div>
              <?php if ($re_registration && $re_registration['payment_path']): ?>
                <a href="<?= getBaseUrl('/re-registrations/view?id=' . $re_registration['id'] . '&file=payment') ?>" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold rounded-lg transition-colors cursor-pointer">
                  👁️ Buka File
                </a>
              <?php else: ?>
                <span class="text-slate-400 italic font-semibold">Belum Diunggah</span>
              <?php endif; ?>
            </div>
          </div>

        </div>

        <?php if ($re_registration): ?>
          <!-- Decision / Verification Form -->
          <?php 
          $isProfileComplete = ($profile_addr_completed && $profile_parent_completed && $profile_edu_completed);
          ?>

          <?php if (!$isProfileComplete): ?>
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 text-xs space-y-2 text-amber-800">
              <div class="flex items-center gap-2 font-bold">
                <span>⚠️</span>
                <span>Peringatan: Profil Pendaftar Belum Lengkap!</span>
              </div>
              <p class="leading-relaxed font-medium">
                Verifikasi daftar ulang tidak dapat disetujui karena pendaftar belum melengkapi data berikut:
              </p>
              <ul class="list-disc pl-5 my-1.5 space-y-1 font-semibold">
                <?php if (!$profile_addr_completed): ?>
                  <li>Data Alamat & Kontak</li>
                <?php endif; ?>
                <?php if (!$profile_parent_completed): ?>
                  <li>Data Orang Tua / Wali</li>
                <?php endif; ?>
                <?php if (!$profile_edu_completed): ?>
                  <li>Data Riwayat Pendidikan</li>
                <?php endif; ?>
              </ul>
              <p class="text-[10px] text-amber-600 font-bold pt-1">
                Tombol "Setujui" dinonaktifkan sementara sampai pendaftar melengkapi profil mereka.
              </p>
            </div>
          <?php endif; ?>

          <form action="<?= getBaseUrl('/admin/re-registrations/verify') ?>" method="POST" class="border-t border-slate-100 pt-6 space-y-4">
            <input type="hidden" name="id" value="<?= $re_registration['id'] ?>">
            <input type="hidden" name="status" value="Approved">

            <h3 class="text-xs font-bold text-slate-450 uppercase tracking-widest">Keputusan Verifikasi</h3>

            <!-- NIM Input Section -->
            <div class="space-y-2 border-t border-slate-100 pt-4">
              <label for="nim" class="block text-xs font-bold text-slate-700">Nomor Induk Mahasiswa (NIM) <span class="text-slate-400 font-medium">(Opsional)</span></label>
              <div class="flex gap-2">
                <input
                  type="text"
                  name="nim"
                  id="nim"
                  class="block flex-1 px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-xs transition-all bg-slate-50 font-semibold text-slate-800"
                  placeholder="Masukkan NIM manual atau klik generate..."
                  value="<?= htmlspecialchars($registration['nim'] ?? '') ?>"
                  <?= !$isProfileComplete ? 'disabled' : '' ?> />
              </div>
              <p class="text-[10px] text-slate-400">Kosongkan jika NIM belum ditentukan (di dashboard mahasiswa akan berstatus PENDING).</p>
            </div>

            <div class="pt-3 flex justify-end">
              <button 
                type="submit" 
                <?= !$isProfileComplete ? 'disabled' : '' ?> 
                class="inline-flex items-center justify-center px-6 py-2.5 border border-slate-250 rounded-full shadow-sm text-xs font-bold transition-all <?= $isProfileComplete ? 'bg-emerald-600 hover:bg-emerald-700 text-white hover:-translate-y-0.5 cursor-pointer' : 'bg-slate-100 text-slate-400 cursor-not-allowed' ?>">
                💾 Setujui & Simpan Verifikasi
              </button>
            </div>
          </form>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>