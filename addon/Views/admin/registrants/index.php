<?php
/**
 * @var array $registrants
 * @var array $programs
 * @var array $filters
 */
?>

<div class="space-y-6">
  <!-- Top Bar -->
  <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-100 pb-5">
    <div class="space-y-1">
      <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Manajemen Pendaftar</h1>
      <p class="text-xs text-slate-500">Kelola, saring, koreksi, dan ekspor data administratif calon mahasiswa baru.</p>
    </div>
    
    <!-- Export Actions -->
    <div class="flex items-center gap-2.5">
      <?php 
        $queryString = http_build_query($filters);
      ?>
      <a href="/admin/registrants/export/pdf?<?= $queryString ?>" class="inline-flex items-center justify-center px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 font-bold rounded-xl border border-slate-200 shadow-sm text-xs transition-colors">
        📄 Ekspor PDF
      </a>
      <a href="/admin/registrants/export/csv?<?= $queryString ?>" class="inline-flex items-center justify-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-sm text-xs transition-colors">
        🟢 Ekspor Excel
      </a>
    </div>
  </div>

  <!-- Search & Filter Card -->
  <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-sm">
    <form action="<?= getBaseUrl('/admin/registrants') ?>" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <!-- Search Input -->
      <div>
        <label for="search" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Cari Pendaftar</label>
        <input
          type="text"
          name="search"
          id="search"
          class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-xs transition-all bg-slate-50 font-semibold"
          placeholder="Nama, Email, NIK, NISN..."
          value="<?= htmlspecialchars($filters['search'] ?? '') ?>"
        />
      </div>

      <!-- Program Studi Filter -->
      <div>
        <label for="program_id" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Program Studi</label>
        <select
          name="program_id"
          id="program_id"
          class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-xs transition-all bg-slate-50 font-semibold text-slate-700"
        >
          <option value="">Semua Program Studi</option>
          <?php foreach ($programs as $prog): ?>
            <option value="<?= $prog['id'] ?>" <?= (string)($filters['program_id'] ?? '') === (string)$prog['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($prog['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Status Filter -->
      <div>
        <label for="status" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Status Pendaftaran</label>
        <select
          name="status"
          id="status"
          class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-xs transition-all bg-slate-50 font-semibold text-slate-700"
        >
          <option value="">Semua Status</option>
          <option value="Draft" <?= ($filters['status'] ?? '') === 'Draft' ? 'selected' : '' ?>>Draft</option>
          <option value="Submitted" <?= ($filters['status'] ?? '') === 'Submitted' ? 'selected' : '' ?>>Submitted</option>
          <option value="Verified" <?= ($filters['status'] ?? '') === 'Verified' ? 'selected' : '' ?>>Verified</option>
          <option value="Rejected" <?= ($filters['status'] ?? '') === 'Rejected' ? 'selected' : '' ?>>Rejected</option>
          <option value="Released" <?= ($filters['status'] ?? '') === 'Released' ? 'selected' : '' ?>>Released</option>
        </select>
      </div>

      <!-- Submit & Reset -->
      <div class="flex items-end gap-2">
        <button type="submit" class="flex-1 py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-xs transition-colors shadow-sm cursor-pointer">
          🔍 Saring
        </button>
        <a href="/admin/registrants" class="py-2.5 px-4 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl text-xs transition-colors text-center cursor-pointer">
          🔄 Reset
        </a>
      </div>
    </form>
  </div>

  <!-- Table Card -->
  <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="border-b border-slate-100 text-[10px] font-bold text-slate-450 uppercase bg-slate-50/50">
            <th class="py-4 px-6 w-12 text-center">No</th>
            <th class="py-4 px-6">Nama Lengkap</th>
            <th class="py-4 px-6">Identitas</th>
            <th class="py-4 px-6">Pilihan Program Studi</th>
            <th class="py-4 px-6 text-center">Status</th>
            <th class="py-4 px-6">Tanggal Daftar</th>
            <th class="py-4 px-6 text-center">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 text-xs text-slate-600">
          <?php if (empty($registrants)): ?>
            <tr>
              <td colspan="7" class="text-center py-12">
                <div class="flex flex-col items-center justify-center space-y-3">
                  <div class="text-slate-300 text-5xl">📁</div>
                  <h3 class="text-sm font-bold text-slate-700">Data Pendaftar Kosong</h3>
                  <p class="text-xs text-slate-400 max-w-xs mx-auto">Tidak ada pendaftar yang memenuhi kriteria pencarian atau belum ada pendaftaran baru.</p>
                </div>
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($registrants as $r): ?>
              <tr class="hover:bg-slate-50/70 border-b border-slate-100 transition-colors">
                <td class="py-4 px-6 text-slate-400 font-semibold"><?= htmlspecialchars((string)(get_registration_number($r) ?? '-')) ?></td>
                <td class="py-4 px-6 font-bold text-slate-800">
                  <div><?= htmlspecialchars((string)($r['full_name'] ?? '-')) ?></div>
                  <div class="text-[10px] text-slate-400 font-medium tracking-wide"><?= htmlspecialchars((string)($r['email'] ?? '-')) ?></div>
                </td>
                <td class="py-4 px-6 text-slate-500 font-medium text-xs">
                  <div>NIK: <span class="font-bold text-slate-700"><?= htmlspecialchars((string)($r['nik'] ?? '-')) ?></span></div>
                  <div>NISN: <span class="font-bold text-slate-700"><?= htmlspecialchars((string)($r['nisn'] ?? '-')) ?></span></div>
                </td>
                <td class="py-4 px-6 space-y-0.5">
                  <div class="font-semibold text-slate-700"><span class="text-[9px] text-slate-400 font-bold">1:</span> <?= htmlspecialchars((string)($r['program1_name'] ?? '-')) ?></div>
                  <?php if (!empty($r['program2_name'])): ?>
                    <div class="text-[10px] text-slate-500 font-medium"><span class="text-[9px] text-slate-355 font-bold">2:</span> <?= htmlspecialchars((string)$r['program2_name']) ?></div>
                  <?php endif; ?>
                </td>
                <td class="py-4 px-6 text-center">
                  <?php if ($r['status'] === 'Verified'): ?>
                    <span class="inline-flex px-2.5 py-1 rounded-full text-[9px] font-bold bg-emerald-100 text-emerald-800 uppercase tracking-wider">Verified</span>
                  <?php elseif ($r['status'] === 'Submitted'): ?>
                    <span class="inline-flex px-2.5 py-1 rounded-full text-[9px] font-bold bg-blue-100 text-blue-800 uppercase tracking-wider">Submitted</span>
                  <?php elseif ($r['status'] === 'Rejected'): ?>
                    <span class="inline-flex px-2.5 py-1 rounded-full text-[9px] font-bold bg-rose-100 text-rose-800 uppercase tracking-wider">Rejected</span>
                  <?php elseif ($r['status'] === 'Released'): ?>
                    <span class="inline-flex px-2.5 py-1 rounded-full text-[9px] font-bold bg-indigo-100 text-indigo-850 uppercase tracking-wider">Released</span>
                  <?php else: ?>
                    <span class="inline-flex px-2.5 py-1 rounded-full text-[9px] font-bold bg-slate-100 text-slate-500 uppercase tracking-wider">Draft</span>
                  <?php endif; ?>
                </td>
                <td class="py-4 px-6 text-slate-400 font-medium"><?= date('d-m-Y H:i', strtotime($r['created_at'])) ?></td>
                <td class="py-4 px-6">
                  <div class="flex items-center justify-center gap-1.5">
                    <a data-spa href="/admin/registrants/detail?id=<?= $r['id'] ?>" class="inline-flex items-center justify-center px-2.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold rounded-lg transition-colors cursor-pointer">
                      🔎 Detail
                    </a>
                    <a data-spa href="/admin/registrants/edit?id=<?= $r['id'] ?>" class="inline-flex items-center justify-center px-2.5 py-1.5 bg-slate-50 hover:bg-slate-100 text-slate-650 font-bold rounded-lg transition-colors cursor-pointer">
                      ✏️ Koreksi
                    </a>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
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
          <a data-spa href="?page=<?= $currentPage - 1 ?><?= !empty($filters['search']) ? '&search=' . urlencode($filters['search']) : '' ?><?= !empty($filters['program_id']) ? '&program_id=' . $filters['program_id'] : '' ?><?= !empty($filters['status']) ? '&status=' . $filters['status'] : '' ?>" class="px-3 py-1.5 rounded-lg text-xs font-bold text-slate-700 bg-slate-50 hover:bg-slate-100 transition-colors border border-slate-200">Sebelumnya</a>
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
            <a data-spa href="?page=<?= $i ?><?= !empty($filters['search']) ? '&search=' . urlencode($filters['search']) : '' ?><?= !empty($filters['program_id']) ? '&program_id=' . $filters['program_id'] : '' ?><?= !empty($filters['status']) ? '&status=' . $filters['status'] : '' ?>" class="px-3 py-1.5 rounded-lg text-xs font-bold text-slate-700 bg-slate-50 hover:bg-slate-100 transition-colors border border-slate-200"><?= $i ?></a>
          <?php endif; ?>
        <?php endfor; ?>

        <?php if ($currentPage < $totalPages): ?>
          <a data-spa href="?page=<?= $currentPage + 1 ?><?= !empty($filters['search']) ? '&search=' . urlencode($filters['search']) : '' ?><?= !empty($filters['program_id']) ? '&program_id=' . $filters['program_id'] : '' ?><?= !empty($filters['status']) ? '&status=' . $filters['status'] : '' ?>" class="px-3 py-1.5 rounded-lg text-xs font-bold text-slate-700 bg-slate-50 hover:bg-slate-100 transition-colors border border-slate-200">Selanjutnya</a>
        <?php else: ?>
          <span class="px-3 py-1.5 rounded-lg text-xs font-bold text-slate-400 bg-slate-50 border border-slate-100 cursor-not-allowed">Selanjutnya</span>
        <?php endif; ?>
      </div>
    </div>
  <?php endif; ?>
</div>
