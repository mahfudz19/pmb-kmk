<div class="w-full py-2 space-y-8">
  <!-- Page Header -->
  <div class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-slate-200/80 flex flex-col sm:flex-row justify-between sm:items-center gap-4">
    <div>
      <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Verifikasi Pembayaran PMB</h2>
      <p class="mt-1 text-xs text-slate-500">Kelola dan verifikasi transaksi bukti pembayaran biaya formulir calon mahasiswa baru.</p>
    </div>
  </div>

  <!-- Payments Table -->
  <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden">
    <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
      <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Daftar Transaksi Masuk</h3>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="border-b border-slate-150 text-[10px] font-bold text-slate-450 uppercase tracking-wider bg-slate-50/30">
            <th class="px-6 py-4">Nama Calon Mahasiswa</th>
            <th class="px-6 py-4">Bank Asal</th>
            <th class="px-6 py-4">Pemilik Rekening</th>
            <th class="px-6 py-4 text-right">Jumlah</th>
            <th class="px-6 py-4">Tanggal Bayar</th>
            <th class="px-6 py-4 text-center">Bukti</th>
            <th class="px-6 py-4 text-center">Status</th>
            <th class="px-6 py-4 text-center">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-150 text-xs text-slate-650">
          <?php if (empty($payments)): ?>
            <tr>
              <td colspan="8" class="text-center py-12">
                <div class="flex flex-col items-center justify-center space-y-3">
                  <div class="text-slate-300 text-5xl">💳</div>
                  <h3 class="text-sm font-bold text-slate-700">Data Transaksi Kosong</h3>
                  <p class="text-xs text-slate-400 max-w-xs mx-auto">Belum ada transaksi pembayaran pendaftaran masuk dari calon mahasiswa baru.</p>
                </div>
              </td>
            </tr>
          <?php else: foreach ($payments as $p): ?>
            <tr class="hover:bg-slate-50/30 transition-colors">
              <td class="px-6 py-4">
                <div class="font-bold text-slate-800"><?= htmlspecialchars($p['full_name']) ?></div>
                <div class="text-[10px] text-slate-400 font-medium"><?= htmlspecialchars($p['email']) ?></div>
              </td>
              <td class="px-6 py-4 font-semibold"><?= htmlspecialchars($p['bank_name']) ?></td>
              <td class="px-6 py-4 font-semibold"><?= htmlspecialchars($p['account_name']) ?></td>
              <td class="px-6 py-4 font-bold text-slate-900 text-right">Rp <?= number_format($p['amount'], 0, ',', '.') ?></td>
              <td class="px-6 py-4"><?= htmlspecialchars($p['payment_date']) ?></td>
              <td class="px-6 py-4 text-center">
                <button 
                  type="button" 
                  onclick="openPreviewModal(<?= $p['id'] ?>, '<?= strtolower(pathinfo($p['file_path'], PATHINFO_EXTENSION)) ?>')"
                  class="px-3 py-1.5 border border-slate-200 hover:bg-slate-50 text-[10px] font-bold text-indigo-650 rounded-full transition-colors cursor-pointer"
                >
                  👁️ Lihat Bukti
                </button>
              </td>
              <td class="px-6 py-4 text-center">
                <?php if ($p['status'] === 'Pending'): ?>
                  <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 border border-amber-300 text-amber-700">Pending</span>
                <?php elseif ($p['status'] === 'Approved'): ?>
                  <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 border border-emerald-300 text-emerald-700">Disetujui</span>
                <?php elseif ($p['status'] === 'Rejected'): ?>
                  <div class="space-y-0.5">
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-red-50 border border-red-300 text-red-700">Ditolak</span>
                    <?php if (!empty($p['rejection_reason'])): ?>
                      <p class="text-[9px] text-red-650 max-w-[120px] truncate mx-auto" title="<?= htmlspecialchars($p['rejection_reason']) ?>">Alasan: <?= htmlspecialchars($p['rejection_reason']) ?></p>
                    <?php endif; ?>
                  </div>
                <?php endif; ?>
              </td>
              <td class="px-6 py-4 text-center">
                <div class="flex items-center justify-center gap-2">
                  <?php if ($p['status'] === 'Pending'): ?>
                    <form action="/admin/payments/verify" method="POST" onsubmit="return confirmAction(event, 'Setujui Pembayaran', 'Apakah Anda yakin ingin menyetujui bukti pembayaran ini?')">
                      <input type="hidden" name="payment_id" value="<?= $p['id'] ?>">
                      <input type="hidden" name="status" value="Approved">
                      <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-[10px] font-bold text-white rounded-full transition-colors cursor-pointer shadow-sm">
                        Setujui
                      </button>
                    </form>
                    <button 
                      type="button" 
                      onclick="openRejectModal(<?= $p['id'] ?>)"
                      class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-[10px] font-bold text-white rounded-full transition-colors cursor-pointer shadow-sm"
                    >
                      Tolak
                    </button>
                  <?php else: ?>
                    <span class="text-[10px] text-slate-400 font-semibold italic">Selesai</span>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <?php if (($totalPages ?? 1) > 1): ?>
    <div class="mt-4 flex flex-col md:flex-row items-center justify-between gap-4 bg-white p-4 rounded-2xl border border-slate-200/50 shadow-sm">
      <div class="text-xs font-semibold text-slate-500">
        Menampilkan <?= min($totalCount, ($currentPage - 1) * $limit + 1) ?> s/d <?= min($totalCount, $currentPage * $limit) ?> dari <?= $totalCount ?> transaksi
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

<!-- Preview Modal -->
<div id="preview-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
  <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closePreviewModal()"></div>
  <div class="relative bg-white rounded-3xl p-6 max-w-4xl w-full shadow-2xl border border-slate-100 space-y-4 transform scale-95 opacity-0 transition-all duration-200" id="preview-modal-card">
    <div class="flex justify-between items-center border-b border-slate-100 pb-3">
      <h3 class="text-base font-bold text-slate-900">Penampil Bukti Pembayaran</h3>
      <button type="button" onclick="closePreviewModal()" class="text-slate-400 hover:text-slate-655 focus:outline-none text-2xl font-semibold">&times;</button>
    </div>

    <div class="w-full flex items-center justify-center bg-slate-50 rounded-2xl overflow-hidden min-h-[300px]" id="preview-content-panel">
      <!-- Injected via JS -->
    </div>
  </div>
</div>

<!-- Rejection Reason Dialog Modal -->
<div id="reject-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
  <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closeRejectModal()"></div>
  <div class="relative bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl border border-slate-100 space-y-4 transform scale-95 opacity-0 transition-all duration-200" id="reject-modal-card">
    <div class="flex justify-between items-center border-b border-slate-100 pb-2">
      <h3 class="text-sm font-bold text-slate-900">Tolak Pembayaran Pendaftaran</h3>
      <button type="button" onclick="closeRejectModal()" class="text-slate-400 hover:text-slate-655 focus:outline-none text-xl font-semibold">&times;</button>
    </div>

    <form action="/admin/payments/verify" method="POST" class="space-y-4 text-xs">
      <input type="hidden" id="reject-payment-id" name="payment_id">
      <input type="hidden" name="status" value="Rejected">

      <div class="space-y-1">
        <label for="rejection_reason" class="block font-bold text-slate-600">Alasan Penolakan</label>
        <textarea id="rejection_reason" name="rejection_reason" rows="3" required class="block w-full px-3 py-2 border border-slate-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-red-500 bg-slate-50" placeholder="Tuliskan catatan alasan penolakan agar diketahui mahasiswa..."></textarea>
      </div>

      <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
        <button type="button" onclick="closeRejectModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-full transition-colors cursor-pointer">Batal</button>
        <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-bold rounded-full transition-colors cursor-pointer shadow-sm">Tolak Pembayaran</button>
      </div>
    </form>
  </div>
</div>

<script>
  function openPreviewModal(paymentId, ext) {
    const panel = document.getElementById('preview-content-panel');
    panel.innerHTML = '';

    const viewUrl = `/payments/view?id=${paymentId}`;

    if (ext === 'pdf') {
      panel.innerHTML = `<iframe src="${viewUrl}" class="w-full h-[600px] border-none rounded-2xl"></iframe>`;
    } else {
      panel.innerHTML = `<img src="${viewUrl}" class="max-w-full max-h-[600px] object-contain rounded-2xl shadow-sm" alt="Bukti Pembayaran" />`;
    }

    const modal = document.getElementById('preview-modal');
    const card = document.getElementById('preview-modal-card');
    
    modal.classList.remove('hidden');
    setTimeout(() => {
      card.classList.remove('scale-95', 'opacity-0');
      card.classList.add('scale-100', 'opacity-100');
    }, 10);
  }

  function closePreviewModal() {
    const modal = document.getElementById('preview-modal');
    const card = document.getElementById('preview-modal-card');
    
    card.classList.remove('scale-100', 'opacity-100');
    card.classList.add('scale-95', 'opacity-0');
    setTimeout(() => {
      modal.classList.add('hidden');
      document.getElementById('preview-content-panel').innerHTML = '';
    }, 200);
  }

  function openRejectModal(paymentId) {
    document.getElementById('reject-payment-id').value = paymentId;

    const modal = document.getElementById('reject-modal');
    const card = document.getElementById('reject-modal-card');
    
    modal.classList.remove('hidden');
    setTimeout(() => {
      card.classList.remove('scale-95', 'opacity-0');
      card.classList.add('scale-100', 'opacity-100');
    }, 10);
  }

  function closeRejectModal() {
    const modal = document.getElementById('reject-modal');
    const card = document.getElementById('reject-modal-card');
    
    card.classList.remove('scale-100', 'opacity-100');
    card.classList.add('scale-95', 'opacity-0');
    setTimeout(() => {
      modal.classList.add('hidden');
      document.getElementById('rejection_reason').value = '';
    }, 200);
  }
</script>
