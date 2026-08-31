<?php

/**
 * @var array $registration
 * @var array $selection
 * @var string $program_name
 * @var float $expected_tuition
 * @var array|null $re_registration
 */
?>

<div class="w-full py-2 space-y-6">
  <!-- Top Bar -->
  <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-100 pb-4">
    <div>
      <div class="flex items-center gap-2 mb-1">
        <a data-spa href="<?= getBaseUrl('/admin/re-registrations') ?>" class="text-xs text-indigo-600 font-bold hover:underline">← Kembali ke Antrean</a>
      </div>
      <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Detail Verifikasi Daftar Ulang</h1>
      <p class="text-xs text-slate-500">Tinjau kelengkapan dokumen dan pembayaran dari calon mahasiswa baru.</p>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left Panel: Applicant & Payment info -->
    <div class="lg:col-span-1 space-y-6">
      <div class="bg-white rounded-xl p-6 border border-slate-200/80 shadow-sm space-y-4">
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

      <div class="bg-white rounded-xl p-6 border border-slate-200/80 shadow-sm space-y-4">
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
      <div class="bg-white rounded-xl p-6 md:p-8 border border-slate-200/80 shadow-sm space-y-6">
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
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-xs space-y-2 text-amber-800">
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
            <div class="space-y-3 border-t border-slate-100 pt-4">
              <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                <label for="nim" class="block text-xs font-bold text-slate-700">Nomor Induk Mahasiswa (NIM) <span class="text-slate-400 font-medium">(Opsional)</span></label>
                <span class="text-[10px] text-slate-400">Contoh format aktif: <strong class="text-slate-700 font-bold"><?= htmlspecialchars($sample_nim ?? '26013-001') ?></strong></span>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-12 gap-2.5">
                <div class="sm:col-span-5">
                  <label for="student_group" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Kelompok Mahasiswa</label>
                  <select id="student_group" class="block w-full px-3 py-2.5 border border-slate-200 rounded-xl shadow-xs focus:outline-none focus:ring-2 focus:ring-indigo-500 text-xs bg-slate-50 font-semibold text-slate-750" <?= !$isProfileComplete ? 'disabled' : '' ?>>
                    <option value="3" selected>Mahasiswa Reguler</option>
                    <option value="8">Mahasiswa Pindahan</option>
                    <option value="9">Mahasiswa Profesi</option>
                  </select>
                </div>
                <div class="sm:col-span-4">
                  <label for="nim" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Nomor Induk (NIM)</label>
                  <input
                    type="text"
                    name="nim"
                    id="nim"
                    class="block w-full px-3 py-2.5 border border-slate-200 rounded-xl shadow-xs placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-xs transition-all bg-slate-50 font-bold text-indigo-700 tracking-wider"
                    placeholder="Contoh: <?= htmlspecialchars($sample_nim ?? '26013-001') ?>"
                    value="<?= htmlspecialchars($registration['nim'] ?? '') ?>"
                    <?= !$isProfileComplete ? 'disabled' : '' ?> />
                </div>
                <div class="sm:col-span-3 flex items-end">
                  <button 
                    type="button" 
                    id="btn-generate-nim"
                    onclick="handleGenerateNim()" 
                    <?= !$isProfileComplete ? 'disabled' : '' ?>
                    class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 rounded-xl text-xs font-bold transition-all cursor-pointer shadow-xs hover:shadow-sm active:scale-95">
                    ⚡ Generate NIM
                  </button>
                </div>
              </div>
              <p class="text-[10px] text-slate-400">Pilih kelompok mahasiswa lalu klik tombol <strong>Generate NIM</strong> untuk membuat NIM unik otomatis sesuai format aktif: <span class="font-semibold text-slate-600"><?= htmlspecialchars($active_nim_name ?? 'Format Standar KMK') ?> (<code><?= htmlspecialchars($active_nim_pattern ?? '{YEAR2}{PRODI_NUM}{GROUP}-{SEQ}') ?></code>)</span>.</p>
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

<script>
async function handleGenerateNim() {
  const btn = document.getElementById('btn-generate-nim');
  const groupSelect = document.getElementById('student_group');
  const nimInput = document.getElementById('nim');
  const group = groupSelect ? groupSelect.value : '3';
  const regId = <?= (int)($registration['id'] ?? 0) ?>;

  if (!regId) return;

  const originalContent = btn.innerHTML;
  btn.innerHTML = '<span class="animate-spin text-xs">⏳</span> Generating...';
  btn.disabled = true;

  try {
    const res = await fetch(`<?= getBaseUrl('/admin/re-registrations/generate-nim') ?>?registration_id=${regId}&group=${group}`);
    const data = await res.json();
    if (data.nim) {
      nimInput.value = data.nim;
      nimInput.classList.add('ring-2', 'ring-emerald-400', 'bg-emerald-50/50');
      setTimeout(() => {
        nimInput.classList.remove('ring-2', 'ring-emerald-400', 'bg-emerald-50/50');
      }, 1500);
      
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          toast: true,
          position: 'top-end',
          icon: 'success',
          title: 'NIM berhasil di-generate: ' + data.nim,
          showConfirmButton: false,
          timer: 3000
        });
      }
    } else if (data.error) {
      if (typeof Swal !== 'undefined') {
        Swal.fire({ icon: 'error', title: 'Gagal', text: data.error });
      } else {
        alert('Gagal generate NIM: ' + data.error);
      }
    }
  } catch (err) {
    if (typeof Swal !== 'undefined') {
      Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan sistem saat menghubungi server.' });
    } else {
      alert('Terjadi kesalahan sistem saat menghubungi server.');
    }
  } finally {
    btn.innerHTML = originalContent;
    btn.disabled = false;
  }
}
</script>