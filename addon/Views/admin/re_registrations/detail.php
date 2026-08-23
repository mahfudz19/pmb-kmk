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
          <form action="<?= getBaseUrl('/admin/re-registrations/verify') ?>" method="POST" class="border-t border-slate-100 pt-6 space-y-4">
            <input type="hidden" name="id" value="<?= $re_registration['id'] ?>">

            <h3 class="text-xs font-bold text-slate-450 uppercase tracking-widest">Keputusan Verifikasi</h3>

            <div class="grid grid-cols-2 gap-4">
              <label class="relative flex items-center justify-center p-4 border rounded-2xl cursor-pointer hover:bg-slate-50/50 transition-colors border-slate-200">
                <input type="radio" name="status" value="Approved" class="sr-only peer" onclick="toggleRejectionBox(false)" <?= $re_registration['status'] === 'Approved' ? 'checked' : '' ?> required>
                <div class="text-center peer-checked:text-emerald-700">
                  <span class="block text-lg">✅</span>
                  <span class="block text-xs font-bold mt-1 text-slate-650">Setujui Daftar Ulang</span>
                </div>
                <div class="absolute inset-0 border-2 rounded-2xl border-transparent peer-checked:border-emerald-500 pointer-events-none"></div>
              </label>

              <label class="relative flex items-center justify-center p-4 border rounded-2xl cursor-pointer hover:bg-slate-50/50 transition-colors border-slate-200">
                <input type="radio" name="status" value="Rejected" class="sr-only peer" onclick="toggleRejectionBox(true)" <?= $re_registration['status'] === 'Rejected' ? 'checked' : '' ?>>
                <div class="text-center peer-checked:text-rose-700">
                  <span class="block text-lg">❌</span>
                  <span class="block text-xs font-bold mt-1 text-slate-650">Tolak Daftar Ulang</span>
                </div>
                <div class="absolute inset-0 border-2 rounded-2xl border-transparent peer-checked:border-rose-500 pointer-events-none"></div>
              </label>
            </div>

            <!-- Rejection Reason Textarea -->
            <div id="rejection-reason-container" class="<?= $re_registration['status'] === 'Rejected' ? '' : 'hidden' ?> space-y-2">
              <label for="rejection_reason" class="block text-xs font-bold text-slate-700">Alasan Penolakan <span class="text-red-500">*</span></label>
              <textarea
                name="rejection_reason"
                id="rejection_reason"
                rows="3"
                class="block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-xs transition-all bg-slate-50"
                placeholder="Tulis alasan penolakan berkas atau bukti bayar di sini..."><?= htmlspecialchars($re_registration['rejection_reason'] ?? '') ?></textarea>
            </div>

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
                  value="<?= htmlspecialchars($registration['nim'] ?? '') ?>" />
                <!-- <button type="button" onclick="autoGenerateNim()" class="px-4 py-2.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-bold rounded-xl transition-colors cursor-pointer flex items-center gap-1">
                  🔄 Generate
                </button> -->
              </div>
              <p class="text-[10px] text-slate-400">Kosongkan jika NIM belum ditentukan (di dashboard mahasiswa akan berstatus PENDING).</p>
            </div>

            <div class="pt-3 flex justify-end">
              <button type="submit" class="inline-flex items-center justify-center px-6 py-2.5 border border-transparent rounded-full shadow-md text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 transition-all hover:-translate-y-0.5 cursor-pointer">
                💾 Simpan Hasil Verifikasi
              </button>
            </div>
          </form>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<script>
  function toggleRejectionBox(show) {
    const box = document.getElementById('rejection-reason-container');
    const textarea = document.getElementById('rejection_reason');
    if (show) {
      box.classList.remove('hidden');
      textarea.setAttribute('required', 'required');
    } else {
      box.classList.add('hidden');
      textarea.removeAttribute('required');
    }
  }

  async function autoGenerateNim() {
    const button = document.querySelector('button[onclick="autoGenerateNim()"]');
    const originalText = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '⌛ Generating...';
    try {
      const response = await fetch('<?= getBaseUrl('/admin/re-registrations/generate-nim?registration_id=' . $registration['id']) ?>');
      const data = await response.json();
      if (data.nim) {
        document.getElementById('nim').value = data.nim;
      } else {
        alert('Gagal membuat NIM: ' + (data.error || 'Terjadi kesalahan'));
      }
    } catch (e) {
      alert('Gagal terhubung ke server');
    } finally {
      button.disabled = false;
      button.innerHTML = originalText;
    }
  }
</script>