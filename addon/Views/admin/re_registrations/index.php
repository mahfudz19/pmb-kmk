<?php
/**
 * @var array $list
 */
?>

<div class="space-y-6">
  <!-- Top Bar -->
  <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-100 pb-5">
    <div class="space-y-1">
      <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Verifikasi Daftar Ulang</h1>
      <p class="text-xs text-slate-500">Tinjau berkas persyaratan dan bukti pembayaran biaya kuliah awal dari calon mahasiswa baru.</p>
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

  <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
    <div class="p-6 border-b border-slate-100">
      <h3 class="text-sm font-bold text-slate-800">Daftar Antrean Daftar Ulang</h3>
    </div>
    
    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="border-b border-slate-100 text-[10px] font-bold text-slate-450 uppercase bg-slate-50/50">
            <th class="py-4 px-6 w-12 text-center">No</th>
            <th class="py-4 px-6">Nama Lengkap</th>
            <th class="py-4 px-6">Program Studi Lulus</th>
            <th class="py-4 px-6">Pembayaran UKT</th>
            <th class="py-4 px-6 text-center">Status</th>
            <th class="py-4 px-6">Tanggal Pengajuan</th>
            <th class="py-4 px-6 text-center">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 text-xs text-slate-600">
          <?php if (empty($list)): ?>
            <tr>
              <td colspan="7" class="text-center py-12">
                <div class="flex flex-col items-center justify-center space-y-3">
                  <div class="text-slate-300 text-5xl">📁</div>
                  <h3 class="text-sm font-bold text-slate-700">Data Daftar Ulang Kosong</h3>
                  <p class="text-xs text-slate-400 max-w-xs mx-auto">Belum ada calon mahasiswa baru yang dinyatakan Lulus Seleksi untuk melakukan daftar ulang.</p>
                </div>
              </td>
            </tr>
          <?php else: ?>
            <?php $no = ($currentPage - 1) * $limit + 1; foreach ($list as $row): ?>
              <tr class="hover:bg-slate-50/50 transition-colors">
                <td class="py-4 px-6 text-center text-slate-400 font-bold"><?= $no++ ?></td>
                <td class="py-4 px-6">
                  <div class="font-bold text-slate-800"><?= htmlspecialchars($row['full_name']) ?></div>
                  <div class="text-[10px] text-slate-400 font-medium"><?= htmlspecialchars($row['email']) ?></div>
                </td>
                <td class="py-4 px-6 font-semibold text-slate-700"><?= htmlspecialchars($row['program_name'] ?? '-') ?></td>
                <td class="py-4 px-6 font-bold text-indigo-650">
                  <?= $row['payment_amount'] ? 'Rp ' . number_format($row['payment_amount'], 0, ',', '.') : '-' ?>
                </td>
                <td class="py-4 px-6 text-center">
                  <?php if (!$row['re_reg_id']): ?>
                    <span class="inline-flex px-2.5 py-1 rounded-full text-[9px] font-bold bg-slate-150 text-slate-500 uppercase tracking-wider">Belum Mengajukan</span>
                  <?php elseif ($row['status'] === 'Approved'): ?>
                    <span class="inline-flex px-2.5 py-1 rounded-full text-[9px] font-bold bg-emerald-100 text-emerald-800 uppercase tracking-wider">Disetujui</span>
                  <?php elseif ($row['status'] === 'Rejected'): ?>
                    <span class="inline-flex px-2.5 py-1 rounded-full text-[9px] font-bold bg-red-100 text-red-800 uppercase tracking-wider">Ditolak</span>
                  <?php else: ?>
                    <span class="inline-flex px-2.5 py-1 rounded-full text-[9px] font-bold bg-amber-100 text-amber-800 uppercase tracking-wider">Menunggu Verifikasi</span>
                  <?php endif; ?>
                </td>
                <td class="py-4 px-6 text-slate-400 font-medium"><?= $row['created_at'] ? date('d-m-Y H:i', strtotime($row['created_at'])) : '-' ?></td>
                <td class="py-4 px-6 text-center">
                  <?php if ($row['re_reg_id']): ?>
                    <a data-spa href="/admin/re-registrations/detail?registration_id=<?= $row['registration_id'] ?>" class="inline-flex items-center justify-center px-3.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold rounded-lg transition-colors cursor-pointer">
                      🔎 Tinjau
                    </a>
                  <?php else: ?>
                    <span class="text-slate-355 italic font-semibold">Menunggu Berkas</span>
                  <?php endif; ?>
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
        Menampilkan <?= min($totalCount, ($currentPage - 1) * $limit + 1) ?> s/d <?= min($totalCount, $currentPage * $limit) ?> dari <?= $totalCount ?> data
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
