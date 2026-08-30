<?php

/**
 * @var int $totalCount
 * @var int $currentPage
 * @var int $limit
 * @var int $totalPages
 */
?>

<div class="w-full py-2 space-y-8">
  <!-- Page Header -->
  <div class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-slate-200/80 flex flex-col sm:flex-row justify-between sm:items-center gap-4">
    <div>
      <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Verifikasi Berkas Calon Mahasiswa</h2>
      <p class="mt-1 text-xs text-slate-500">Evaluasi data diri, riwayat sekolah, dan setujui atau tolak berkas persyaratan calon mahasiswa.</p>
    </div>
  </div>

  <!-- Verifications List Table -->
  <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden">
    <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
      <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Daftar Pendaftar Masuk</h3>
      <form id="filter-form" method="GET" action="<?= getBaseUrl('/admin/verifications') ?>" class="flex items-center gap-2">
        <label for="wave_id" class="text-xs font-bold text-slate-500 uppercase tracking-wider">Gelombang:</label>
        <select id="wave_id" name="wave_id" onchange="this.form.submit()" class="px-3 py-2 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 text-xs font-semibold bg-white cursor-pointer">
          <option value="">Semua Gelombang</option>
          <?php foreach ($waves as $w): ?>
            <option value="<?= $w['id'] ?>" <?= $selectedWaveId == $w['id'] ? 'selected' : '' ?>><?= htmlspecialchars($w['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </form>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="border-b border-slate-150 text-[10px] font-bold text-slate-450 uppercase tracking-wider bg-slate-50/30">
            <th class="px-6 py-4">Calon Mahasiswa</th>
            <th class="px-6 py-4">Nomor NIK / NISN</th>
            <th class="px-6 py-4 text-center">Berkas Terkumpul</th>
            <th class="px-6 py-4 text-center">Status Berkas</th>
            <th class="px-6 py-4 text-center">Status Pendaftaran</th>
            <th class="px-6 py-4 text-center">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-150 text-xs text-slate-650">
          <?php if (empty($candidates)): ?>
            <tr>
              <td colspan="6" class="text-center py-12">
                <div class="flex flex-col items-center justify-center space-y-3">
                  <div class="text-slate-300 text-5xl">📁</div>
                  <h3 class="text-sm font-bold text-slate-700">Data Pendaftaran Kosong</h3>
                  <p class="text-xs text-slate-400 max-w-xs mx-auto">Belum ada calon pendaftar yang mengunci formulir pendaftaran untuk diverifikasi.</p>
                </div>
              </td>
            </tr>
            <?php else: foreach ($candidates as $c): ?>
              <?php
              $isAllApproved = ($c['approved_count'] === $c['required_count'] && $c['required_count'] > 0);
              ?>
              <tr class="hover:bg-slate-50/30 transition-colors">
                <td class="px-6 py-4">
                  <div class="font-bold text-slate-800"><?= htmlspecialchars((string)($c['full_name'] ?? '-')) ?></div>
                  <div class="text-[10px] text-slate-400 font-medium"><?= htmlspecialchars((string)($c['email'] ?? '-')) ?></div>
                  <span class="inline-flex mt-1 px-1.5 py-0.2 text-[9px] font-bold bg-indigo-50 border border-indigo-200 text-indigo-700 rounded"><?= htmlspecialchars($c['wave_name'] ?? '-') ?></span>
                </td>
                <td class="px-6 py-4 font-mono text-[11px]">
                  <div>NIK: <?= htmlspecialchars((string)($c['nik'] ?? '-')) ?></div>
                  <div>NISN: <?= htmlspecialchars((string)($c['nisn'] ?? '-')) ?></div>
                </td>
                <td class="px-6 py-4 text-center font-bold text-slate-700">
                  <?= $c['uploaded_count'] ?> / <?= $c['required_count'] ?>
                </td>
                <td class="px-6 py-4 text-center">
                  <?php if ($isAllApproved): ?>
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 border border-emerald-300 text-emerald-700">Lengkap</span>
                  <?php else: ?>
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 border border-amber-300 text-amber-700">Belum Lengkap</span>
                  <?php endif; ?>
                </td>
                <td class="px-6 py-4 text-center font-semibold text-slate-800">
                  <?= htmlspecialchars($c['status']) ?>
                </td>
                <td class="px-6 py-4 text-center">
                  <a
                    data-spa
                    href="<?= getBaseUrl('/admin/verifications/detail?id=' . $c['id']) ?>"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-[10px] font-bold rounded-full transition-colors cursor-pointer shadow-sm">
                    Detail & Verifikasi
                  </a>
                </td>
              </tr>
          <?php endforeach;
          endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <?php if (($totalPages ?? 1) > 1): ?>
    <div class="mt-4 flex flex-col md:flex-row items-center justify-between gap-4 bg-white p-4 rounded-2xl border border-slate-200/50 shadow-sm">
      <div class="text-xs font-semibold text-slate-500">
        Menampilkan <?= min($totalCount, ($currentPage - 1) * $limit + 1) ?> s/d <?= min($totalCount, $currentPage * $limit) ?> dari <?= $totalCount ?> pendaftar
      </div>
      <div class="flex items-center gap-1.5">
        <?php if ($currentPage > 1): ?>
          <a data-spa href="?page=<?= $currentPage - 1 ?>" class="px-3 py-1.5 rounded-lg text-xs font-bold text-slate-700 bg-slate-50 hover:bg-slate-100 transition-colors border border-slate-200">Sebelumnya</a>
        <?php else: ?>
          <span class="px-3 py-1.5 rounded-lg text-xs font-bold text-slate-400 bg-slate-50 border border-slate-100 cursor-not-allowed">Sebelumnya</span>
        <?php endif; ?>

        <?php
        $startPage = max(1, $currentPage - 2);
        $endPage = min($totalPages, $currentPage + 2);
        for ($i = $startPage; $i <= $endPage; $i++):
        ?>
          <?php if ($i == $currentPage): ?>
            <span class="px-3 py-1.5 rounded-lg text-xs font-bold text-white bg-indigo-600 border border-indigo-600 shadow-sm"><?= $i ?></span>
          <?php else: ?>
            <a data-spa href="?page=<?= $i ?>" class="px-3 py-1.5 rounded-lg text-xs font-bold text-slate-700 bg-slate-50 hover:bg-slate-100 transition-colors border border-slate-200"><?= $i ?></a>
          <?php endif; ?>
        <?php endfor; ?>

        <?php if ($currentPage < $totalPages): ?>
          <a data-spa href="?page=<?= $currentPage + 1 ?>" class="px-3 py-1.5 rounded-lg text-xs font-bold text-slate-700 bg-slate-50 hover:bg-slate-100 transition-colors border border-slate-200">Selanjutnya</a>
        <?php else: ?>
          <span class="px-3 py-1.5 rounded-lg text-xs font-bold text-slate-400 bg-slate-50 border border-slate-100 cursor-not-allowed">Selanjutnya</span>
        <?php endif; ?>
      </div>
    </div>
  <?php endif; ?>
</div>