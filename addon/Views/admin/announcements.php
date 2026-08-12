<div class="w-full py-2 space-y-8">
  <!-- Page Header -->
  <div class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-slate-200/80 flex flex-col sm:flex-row justify-between sm:items-center gap-4">
    <div>
      <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Manajemen Pengumuman Hasil Seleksi</h2>
      <p class="mt-1 text-xs text-slate-500">Buat dan kelola template pengumuman kelulusan yang akan tampil pada dashboard portal pendaftar.</p>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
    <!-- Form Card -->
    <div class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-slate-200/80 space-y-6">
      <h3 class="text-sm font-bold text-slate-800 border-b border-slate-100 pb-3" id="form-title">Buat Pengumuman Baru</h3>

      <form action="<?= getBaseUrl('/admin/announcements/save') ?>" method="POST" class="space-y-4 text-xs" id="announcement-form">
        <input type="hidden" id="announcement-id" name="id">

        <div class="space-y-1">
          <label for="title" class="block font-bold text-slate-600">Judul Pengumuman</label>
          <input type="text" id="title" name="title" required class="block w-full px-3 py-2.5 border border-slate-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-indigo-500 bg-slate-50 font-semibold" placeholder="Contoh: Pengumuman Hasil Seleksi PMB Mandiri">
        </div>

        <div class="space-y-1">
          <label for="content" class="block font-bold text-slate-600">Konten Pengumuman</label>
          <textarea id="content" name="content" required rows="6" class="block w-full px-3 py-2.5 border border-slate-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-indigo-500 bg-slate-50 leading-relaxed" placeholder="Tulis instruksi lengkap kelulusan atau pesan di sini..."></textarea>
        </div>

        <div class="space-y-1">
          <label for="is_active" class="block font-bold text-slate-600">Status Keaktifan</label>
          <select id="is_active" name="is_active" required class="block w-full px-3 py-2.5 border border-slate-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-indigo-500 bg-slate-50 font-bold">
            <option value="1">Aktif (Tampilkan di Portal)</option>
            <option value="0">Draft / Tidak Aktif</option>
          </select>
          <p class="text-[10px] text-slate-400 mt-1 leading-normal">Mengaktifkan pengumuman ini otomatis akan menonaktifkan pengumuman aktif lainnya.</p>
        </div>

        <div class="flex gap-2 pt-2 border-t border-slate-100">
          <button type="button" onclick="resetForm()" class="flex-1 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition-colors cursor-pointer">Batal</button>
          <button type="submit" class="flex-1 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl transition-colors cursor-pointer shadow-sm text-center">Simpan</button>
        </div>
      </form>
    </div>

    <!-- Table Card -->
    <div class="lg:col-span-2 bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden">
      <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50">
        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Riwayat Pengumuman</h3>
      </div>

      <div class="overflow-x-auto">
        <table data-paginate="5" class="w-full text-left border-collapse">
          <thead>
            <tr class="border-b border-slate-150 text-[10px] font-bold text-slate-450 uppercase tracking-wider bg-slate-50/30">
              <th class="px-6 py-4">Pengumuman</th>
              <th class="px-6 py-4">Konten Singkat</th>
              <th class="px-6 py-4 text-center">Status</th>
              <th class="px-6 py-4 text-center">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-150 text-xs text-slate-650">
            <?php if (empty($announcements)): ?>
              <tr>
                <td colspan="4" class="text-center py-12">
                  <div class="flex flex-col items-center justify-center space-y-3">
                    <div class="text-slate-300 text-5xl">📢</div>
                    <h3 class="text-sm font-bold text-slate-700">Belum Ada Pengumuman</h3>
                    <p class="text-xs text-slate-400 max-w-xs mx-auto">Silakan buat pengumuman baru menggunakan formulir di sebelah kiri.</p>
                  </div>
                </td>
              </tr>
              <?php else: foreach ($announcements as $a): ?>
                <tr class="hover:bg-slate-50/30 transition-colors">
                  <td class="px-6 py-4 font-bold text-slate-800"><?= htmlspecialchars($a['title']) ?></td>
                  <td class="px-6 py-4 max-w-xs truncate text-slate-500"><?= htmlspecialchars($a['content']) ?></td>
                  <td class="px-6 py-4 text-center">
                    <?php if ((int)$a['is_active'] === 1): ?>
                      <span class="inline-flex px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 border border-emerald-300 text-emerald-700">Aktif</span>
                    <?php else: ?>
                      <span class="inline-flex px-2 py-0.5 rounded-full text-[9px] font-bold bg-slate-100 border border-slate-200 text-slate-500">Draft</span>
                    <?php endif; ?>
                  </td>
                  <td class="px-6 py-4 text-center">
                    <div class="flex items-center justify-center gap-2">
                      <button
                        type="button"
                        onclick="editAnnouncement(<?= htmlspecialchars(json_encode($a)) ?>)"
                        class="px-2.5 py-1.5 border border-slate-200 hover:bg-slate-50 text-[10px] font-bold text-slate-700 rounded-lg cursor-pointer">
                        ✏️ Edit
                      </button>
                      <form action="<?= getBaseUrl('/admin/announcements/delete') ?>" method="POST" onsubmit="return confirmAction(event, 'Hapus Pengumuman', 'Apakah Anda yakin ingin menghapus pengumuman ini?')">
                        <input type="hidden" name="id" value="<?= $a['id'] ?>">
                        <button type="submit" class="px-2.5 py-1.5 bg-red-50 hover:bg-red-100 border border-red-200 text-[10px] font-bold text-red-600 rounded-lg cursor-pointer">
                          🗑️ Hapus
                        </button>
                      </form>
                    </div>
                  </td>
                </tr>
            <?php endforeach;
            endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script>
  function editAnnouncement(ann) {
    document.getElementById('form-title').innerText = "Edit Pengumuman";
    document.getElementById('announcement-id').value = ann.id;
    document.getElementById('title').value = ann.title;
    document.getElementById('content').value = ann.content;
    document.getElementById('is_active').value = ann.is_active;
  }

  function resetForm() {
    document.getElementById('form-title').innerText = "Buat Pengumuman Baru";
    document.getElementById('announcement-id').value = "";
    document.getElementById('announcement-form').reset();
  }
</script>