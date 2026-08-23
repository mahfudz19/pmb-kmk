<?php

/**
 * @var array $history
 * @var bool $all_finalized
 */
?>
<div class="w-full py-2 space-y-6">
  <div class="flex items-center justify-between border-b border-slate-100 pb-4">
    <div class="flex items-center gap-3">
      <span class="text-3xl">📜</span>
      <div>
        <h2 class="text-xl font-bold text-slate-900">Riwayat Pendaftaran</h2>
        <p class="text-xs text-slate-500">Berikut adalah daftar riwayat gelombang pendaftaran Anda di PMB KMK.</p>
      </div>
    </div>
  </div>

  <div class="bg-white rounded-3xl border border-slate-100 p-6 md:p-8 shadow-sm space-y-6">
    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse text-xs">
        <thead>
          <tr class="border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider text-[10px]">
            <th class="pb-3 pr-4 w-[20%]">Gelombang</th>
            <th class="pb-3 px-4 w-[25%]">Program Studi Kelulusan</th>
            <th class="pb-3 px-4 w-[15%]">Status Seleksi</th>
            <th class="pb-3 px-4 w-[20%]">Status Daftar Ulang</th>
            <th class="pb-3 pl-4 text-right w-[20%]">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
          <?php foreach ($history as $index => $h): ?>
            <tr>
              <td class="py-4 pr-4 font-semibold text-slate-800 align-middle">
                <?= htmlspecialchars($h['wave']['name'] ?? '-') ?>
                <span class="block text-[10px] font-normal text-slate-400 mt-0.5"><?= htmlspecialchars($h['wave']['academic_year'] ?? '') ?></span>
              </td>
              <td class="py-4 px-4 text-slate-600 align-middle">
                <?= htmlspecialchars($h['passed_program_name'] ?? '-') ?>
              </td>
              <td class="py-4 px-4 align-middle">
                <?php if ($h['selection'] && (int)$h['selection']['is_published'] === 1): ?>
                  <?php if ($h['selection']['status'] === 'Lulus' || $h['selection']['status'] === 'Cadangan'): ?>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">Lolos</span>
                  <?php elseif ($h['selection']['status'] === 'Tidak Lulus'): ?>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-red-50 text-red-700 border border-red-100">Tidak Lolos</span>
                  <?php else: ?>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-slate-50 text-slate-600 border border-slate-100"><?= htmlspecialchars($h['selection']['status']) ?></span>
                  <?php endif; ?>
                <?php else: ?>
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-amber-50 text-amber-700 border border-amber-100">Dalam Proses</span>
                <?php endif; ?>
              </td>
              <td class="py-4 px-4 align-middle">
                <?php if ($h['selection'] && (int)$h['selection']['is_published'] === 1 && ($h['selection']['status'] === 'Lulus' || $h['selection']['status'] === 'Cadangan')): ?>
                  <?php if (empty($h['re_registration'])): ?>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-slate-100 text-slate-650 border border-slate-200">Belum Daftar Ulang</span>
                  <?php else: ?>
                    <?php if ($h['re_registration']['status'] === 'Pending'): ?>
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-amber-50 text-amber-700 border border-amber-100">Menunggu Verifikasi</span>
                    <?php elseif ($h['re_registration']['status'] === 'Approved'): ?>
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">Disetujui</span>
                    <?php elseif ($h['re_registration']['status'] === 'Rejected'): ?>
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-red-50 text-red-700 border border-red-100">Ditolak</span>
                    <?php endif; ?>
                  <?php endif; ?>
                <?php else: ?>
                  <span class="text-slate-400 font-medium">-</span>
                <?php endif; ?>
              </td>
              <td class="py-4 pl-4 text-right align-middle">
                <div class="flex justify-end gap-1.5">
                  <?php if ($index === 0 && (!$h['selection'] || (int)$h['selection']['is_published'] !== 1 || !in_array($h['selection']['status'], ['Lulus', 'Tidak Lulus', 'Cadangan']))): ?>
                    <a data-spa href="<?= getBaseUrl('/dashboard') ?>" class="inline-flex items-center gap-1 px-2.5 py-1 bg-indigo-600 hover:bg-indigo-700 text-white text-[9px] font-bold rounded-lg cursor-pointer transition-all shadow-sm">
                      ➔ Lanjutkan
                    </a>
                  <?php endif; ?>
                  <?php if ($h['registration']['status'] !== 'Draft'): ?>
                    <a href="<?= getBaseUrl('/pendaftaran/formulir?registration_id=' . $h['registration']['id']) ?>" download class="inline-flex items-center gap-1 px-2 py-1 bg-white border border-slate-200 hover:bg-slate-50 text-[9px] font-bold text-slate-700 rounded-lg cursor-pointer transition-all shadow-sm">
                      📄 Formulir
                    </a>
                  <?php endif; ?>
                  <?php if (in_array($h['registration']['status'], ['Verified', 'Released'])): ?>
                    <a href="<?= getBaseUrl('/pendaftaran/kartu-ujian?registration_id=' . $h['registration']['id']) ?>" download class="inline-flex items-center gap-1 px-2 py-1 bg-white border border-slate-200 hover:bg-slate-50 text-[9px] font-bold text-slate-700 rounded-lg cursor-pointer transition-all shadow-sm">
                      🪪 Kartu Ujian
                    </a>
                  <?php endif; ?>
                  <?php if ($h['selection'] && (int)$h['selection']['is_published'] === 1 && ($h['selection']['status'] === 'Lulus' || $h['selection']['status'] === 'Cadangan')): ?>
                    <a href="<?= getBaseUrl('/pendaftaran/kelulusan/download?registration_id=' . $h['registration']['id']) ?>" download class="inline-flex items-center gap-1 px-2.5 py-1 bg-white border border-emerald-250 text-emerald-700 hover:bg-emerald-50 text-[9px] font-bold rounded-lg cursor-pointer transition-all shadow-sm">
                      🎓 Kelulusan
                    </a>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <?php if ($all_finalized && !empty($active_waves)): ?>
    <div class="bg-white rounded-3xl border border-slate-100 p-6 md:p-8 shadow-sm space-y-4 max-w-lg mx-auto text-center">
      <div class="text-3xl animate-pulse">🚀</div>
      <div class="space-y-1">
        <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider">Daftar Gelombang Lain</h3>
        <p class="text-xs text-slate-500">Semua gelombang pendaftaran Anda sebelumnya telah selesai diproses. Anda dapat mencoba mendaftar kembali pada gelombang aktif berikut ini.</p>
      </div>
      <form data-spa method="POST" action="<?= getBaseUrl('/dashboard/init-registration') ?>" onsubmit="return validateInitForm(event)" class="space-y-4 text-left bg-slate-50 p-6 rounded-2xl border border-slate-150">
        <div class="space-y-1">
          <label for="wave_id" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Pilih Gelombang Baru <span class="text-red-550">*</span></label>
          <select id="wave_id" name="wave_id" class="block w-full px-3 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-white font-medium">
            <option value="" disabled selected>Pilih Gelombang</option>
            <?php foreach ($active_waves as $w): ?>
              <option value="<?= $w['id'] ?>"><?= htmlspecialchars($w['name']) ?> (Tahun Akademik: <?= htmlspecialchars($w['academic_year']) ?>)</option>
            <?php endforeach; ?>
          </select>
        </div>
        <button type="submit" class="w-full inline-flex items-center justify-center px-6 py-3 border border-transparent rounded-xl shadow-md text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 transition-all cursor-pointer">
          Mulai Pendaftaran Gelombang Baru
        </button>
      </form>
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
</script>