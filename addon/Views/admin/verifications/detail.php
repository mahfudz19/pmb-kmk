<div class="w-full py-2 space-y-8">
  <!-- Page Header -->
  <div class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-slate-200/80 flex flex-col sm:flex-row justify-between sm:items-center gap-4">
    <div>
      <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Evaluasi Berkas Calon Mahasiswa</h2>
      <p class="mt-1 text-xs text-slate-500">Tinjau biodata pendaftaran dan verifikasi kelengkapan dokumen fisik pendaftar.</p>
    </div>
    <div>
      <a data-spa href="/admin/verifications" class="inline-flex items-center justify-center px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-full transition-colors">
        ← Kembali ke Daftar
      </a>
    </div>
  </div>

  <!-- Notifications -->
  <?php if (isset($_GET['success'])): ?>
    <div class="p-4 bg-emerald-50 border border-emerald-500 text-emerald-700 rounded-2xl flex items-center gap-3">
      <span>✅</span>
      <span class="text-sm font-semibold"><?= htmlspecialchars($_GET['success']) ?></span>
    </div>
  <?php endif; ?>
  <?php if (isset($_GET['error'])): ?>
    <div class="p-4 bg-red-50 border border-red-500 text-red-700 rounded-2xl flex items-center gap-3">
      <span>⚠️</span>
      <span class="text-sm font-semibold"><?= htmlspecialchars($_GET['error']) ?></span>
    </div>
  <?php endif; ?>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
    <!-- Left: Applicant Details Summary (Data Pribadi s.d Prodi Pilihan) -->
    <div class="lg:col-span-2 space-y-8">
      <!-- 1. Biodata Card -->
      <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 p-6 md:p-8 space-y-6">
        <h3 class="text-sm font-bold text-slate-800 border-b border-slate-100 pb-3 flex items-center gap-2">
          <span>👤</span> Data Pribadi & Kontak
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
          <p class="text-slate-500"><strong class="text-slate-700">Nama Lengkap:</strong> <span class="font-medium"><?= htmlspecialchars($candidate['full_name'] ?? '-') ?></span></p>
          <p class="text-slate-500"><strong class="text-slate-700">NIK (No. KTP):</strong> <span class="font-medium"><?= htmlspecialchars($candidate['nik'] ?? '-') ?></span></p>
          <p class="text-slate-500"><strong class="text-slate-700">NISN:</strong> <span class="font-medium"><?= htmlspecialchars($candidate['nisn'] ?? '-') ?></span></p>
          <p class="text-slate-500"><strong class="text-slate-700">Tempat, Tgl Lahir:</strong> <span class="font-medium"><?= htmlspecialchars($candidate['birth_place'] ?? '-') ?>, <?= htmlspecialchars($candidate['birth_date'] ?? '-') ?></span></p>
          <p class="text-slate-500"><strong class="text-slate-700">Jenis Kelamin:</strong> <span class="font-medium"><?= htmlspecialchars($candidate['gender'] ?? '-') ?></span></p>
          <p class="text-slate-500"><strong class="text-slate-700">Agama:</strong> <span class="font-medium"><?= htmlspecialchars($candidate['religion'] ?? '-') ?></span></p>
          <p class="text-slate-500"><strong class="text-slate-700">No. HP / WA:</strong> <span class="font-medium"><?= htmlspecialchars($candidate['phone'] ?? '-') ?></span></p>
          <p class="text-slate-500"><strong class="text-slate-700">Email:</strong> <span class="font-medium"><?= htmlspecialchars($candidate['email'] ?? '-') ?></span></p>
        </div>
      </div>

      <!-- 2. Address Card -->
      <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 p-6 md:p-8 space-y-6">
        <h3 class="text-sm font-bold text-slate-800 border-b border-slate-100 pb-3 flex items-center gap-2">
          <span>📍</span> Alamat Tinggal
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
          <p class="text-slate-500"><strong class="text-slate-700">Kecamatan:</strong> <span class="font-medium"><?= htmlspecialchars($address['district'] ?? '-') ?></span></p>
          <p class="text-slate-500"><strong class="text-slate-700">Kelurahan:</strong> <span class="font-medium"><?= htmlspecialchars($address['subdistrict'] ?? '-') ?></span></p>
          <p class="text-slate-500"><strong class="text-slate-700">Kode Pos:</strong> <span class="font-medium"><?= htmlspecialchars($address['postal_code'] ?? '-') ?></span></p>
          <p class="text-slate-500 md:col-span-2"><strong class="text-slate-700">Alamat Lengkap:</strong> <span class="font-medium"><?= htmlspecialchars($address['address'] ?? '-') ?></span></p>
        </div>
      </div>

      <!-- 3. Parents Card -->
      <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 p-6 md:p-8 space-y-6">
        <h3 class="text-sm font-bold text-slate-800 border-b border-slate-100 pb-3 flex items-center gap-2">
          <span>👨‍👩‍👦</span> Data Orang Tua / Wali
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
          <div class="space-y-1.5 p-4 bg-slate-50 rounded-2xl">
            <h4 class="font-bold text-indigo-750">👨 Data Ayah</h4>
            <p class="text-slate-500"><strong class="text-slate-700">Nama:</strong> <?= htmlspecialchars($parents['father_name'] ?? '-') ?></p>
            <p class="text-slate-500"><strong class="text-slate-700">Pendidikan:</strong> <?= htmlspecialchars($parents['father_education'] ?? '-') ?></p>
            <p class="text-slate-500"><strong class="text-slate-700">Pekerjaan:</strong> <?= htmlspecialchars($parents['father_occupation'] ?? '-') ?></p>
            <p class="text-slate-500"><strong class="text-slate-700">Penghasilan:</strong> <?= htmlspecialchars($parents['father_income'] ?? '-') ?></p>
          </div>
          
          <div class="space-y-1.5 p-4 bg-slate-50 rounded-2xl">
            <h4 class="font-bold text-indigo-750">👩 Data Ibu</h4>
            <p class="text-slate-500"><strong class="text-slate-700">Nama:</strong> <?= htmlspecialchars($parents['mother_name'] ?? '-') ?></p>
            <p class="text-slate-500"><strong class="text-slate-700">Pendidikan:</strong> <?= htmlspecialchars($parents['mother_education'] ?? '-') ?></p>
            <p class="text-slate-500"><strong class="text-slate-700">Pekerjaan:</strong> <?= htmlspecialchars($parents['mother_occupation'] ?? '-') ?></p>
            <p class="text-slate-500"><strong class="text-slate-700">Penghasilan:</strong> <?= htmlspecialchars($parents['mother_income'] ?? '-') ?></p>
          </div>

          <?php if (!empty($parents['guardian_name'])): ?>
            <div class="space-y-1.5 p-4 bg-slate-50 rounded-2xl md:col-span-2">
              <h4 class="font-bold text-slate-700">👤 Data Wali</h4>
              <p class="text-slate-500"><strong class="text-slate-700">Nama:</strong> <?= htmlspecialchars($parents['guardian_name']) ?></p>
              <p class="text-slate-500"><strong class="text-slate-700">Pendidikan:</strong> <?= htmlspecialchars($parents['guardian_education']) ?></p>
              <p class="text-slate-500"><strong class="text-slate-700">Pekerjaan:</strong> <?= htmlspecialchars($parents['guardian_occupation']) ?></p>
              <p class="text-slate-500"><strong class="text-slate-700">Penghasilan:</strong> <?= htmlspecialchars($parents['guardian_income']) ?></p>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- 4. Education & Choices Card -->
      <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 p-6 md:p-8 space-y-6">
        <h3 class="text-sm font-bold text-slate-800 border-b border-slate-100 pb-3 flex items-center gap-2">
          <span>🏫</span> Pendidikan & Pilihan PMB
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
          <div class="space-y-1.5 p-4 bg-slate-50 rounded-2xl">
            <h4 class="font-bold text-indigo-750">🎓 Pendidikan Asal</h4>
            <p class="text-slate-500"><strong class="text-slate-700">Nama Sekolah:</strong> <?= htmlspecialchars($education['school_name'] ?? '-') ?></p>
            <p class="text-slate-500"><strong class="text-slate-700">Jurusan:</strong> <?= htmlspecialchars($education['school_major'] ?? '-') ?></p>
            <p class="text-slate-500"><strong class="text-slate-700">Tahun Lulus:</strong> <?= htmlspecialchars($education['graduation_year'] ?? '-') ?></p>
            <p class="text-slate-500"><strong class="text-slate-700">Nomor Ijazah/SKL:</strong> <?= htmlspecialchars($education['diploma_number'] ?? '-') ?></p>
            <p class="text-slate-500"><strong class="text-slate-700">Nilai Rata-Rata:</strong> <?= htmlspecialchars($education['average_score'] ?? '-') ?></p>
          </div>

          <div class="space-y-1.5 p-4 bg-slate-50 rounded-2xl">
            <h4 class="font-bold text-indigo-750">🛤️ Pilihan Program Studi</h4>
            <p class="text-slate-500"><strong class="text-slate-700">Pilihan 1:</strong> <?= htmlspecialchars($program['prodi1_name'] ?? '-') ?></p>
            <p class="text-slate-500"><strong class="text-slate-700">Pilihan 2:</strong> <?= htmlspecialchars($program['prodi2_name'] ?? 'Tidak Memilih') ?></p>
          </div>
        </div>
      </div>
    </div>

    <!-- Right: Document Verification Area -->
    <div class="space-y-8">
      <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 p-6 md:p-8 space-y-6">
        <h3 class="text-sm font-bold text-slate-800 border-b border-slate-100 pb-3 flex items-center gap-2">
          <span>📎</span> Berkas Dokumen Persyaratan
        </h3>
        
        <div class="space-y-4">
          <?php foreach ($document_types as $dt): ?>
            <?php 
              $uploaded = $uploaded_docs[$dt['id']] ?? null;
            ?>
            <div class="p-4 bg-slate-50/50 rounded-2xl border border-slate-150 space-y-3">
              <div class="flex justify-between items-start gap-2">
                <div>
                  <h4 class="text-xs font-bold text-slate-800"><?= htmlspecialchars($dt['name']) ?></h4>
                  <span class="text-[9px] text-slate-400 font-semibold block"><?= $dt['is_required'] ? 'Wajib' : 'Opsional' ?></span>
                </div>
                <div>
                  <?php if ($uploaded): ?>
                    <?php if ($uploaded['status'] === 'Pending'): ?>
                      <span class="inline-flex px-2 py-0.5 rounded-full text-[9px] font-bold bg-amber-50 border border-amber-300 text-amber-700 animate-pulse">Pending</span>
                    <?php elseif ($uploaded['status'] === 'Approved'): ?>
                      <span class="inline-flex px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 border border-emerald-300 text-emerald-700">Approved</span>
                    <?php elseif ($uploaded['status'] === 'Rejected'): ?>
                      <span class="inline-flex px-2 py-0.5 rounded-full text-[9px] font-bold bg-red-50 border border-red-300 text-red-700">Rejected</span>
                    <?php endif; ?>
                  <?php else: ?>
                    <span class="inline-flex px-2 py-0.5 rounded-full text-[9px] font-bold bg-slate-100 border border-slate-200 text-slate-500">Belum Ada</span>
                  <?php endif; ?>
                </div>
              </div>

              <?php if ($uploaded): ?>
                <?php if ($uploaded['status'] === 'Rejected' && !empty($uploaded['rejection_reason'])): ?>
                  <p class="text-[9px] text-red-650 font-bold bg-red-50 p-2 rounded-lg border border-red-100">Catatan: <?= htmlspecialchars($uploaded['rejection_reason']) ?></p>
                <?php endif; ?>

                <div class="flex gap-2">
                  <button 
                    type="button" 
                    onclick="openPreviewModal(<?= $uploaded['id'] ?>, '<?= strtolower(pathinfo($uploaded['file_path'], PATHINFO_EXTENSION)) ?>')"
                    class="flex-1 px-3 py-1.5 border border-slate-200 hover:bg-slate-50 text-[10px] font-bold text-slate-700 rounded-full transition-colors cursor-pointer text-center"
                  >
                    👁️ Preview Berkas
                  </button>

                  <div class="flex gap-1.5">
                    <?php if ($uploaded['status'] !== 'Approved'): ?>
                      <form action="/admin/verifications/verify-document" method="POST" onsubmit="return confirmAction(event, 'Setujui Dokumen', 'Apakah Anda yakin ingin menyetujui dokumen ini?')">
                        <input type="hidden" name="document_id" value="<?= $uploaded['id'] ?>">
                        <input type="hidden" name="status" value="Approved">
                        <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-[10px] font-bold rounded-full cursor-pointer shadow-sm">
                          Setujui
                        </button>
                      </form>
                    <?php endif; ?>

                    <?php if ($uploaded['status'] !== 'Rejected'): ?>
                      <button 
                        type="button" 
                        onclick="openRejectModal(<?= $uploaded['id'] ?>)"
                        class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-[10px] font-bold rounded-full cursor-pointer shadow-sm"
                      >
                        Tolak
                      </button>
                    <?php endif; ?>
                  </div>
                </div>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Preview Modal -->
<div id="preview-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
  <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closePreviewModal()"></div>
  <div class="relative bg-white rounded-3xl p-6 max-w-4xl w-full shadow-2xl border border-slate-100 space-y-4 transform scale-95 opacity-0 transition-all duration-200" id="preview-modal-card">
    <div class="flex justify-between items-center border-b border-slate-100 pb-3">
      <h3 class="text-base font-bold text-slate-900">Penampil Dokumen Persyaratan</h3>
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
      <h3 class="text-sm font-bold text-slate-900">Tolak Dokumen Persyaratan</h3>
      <button type="button" onclick="closeRejectModal()" class="text-slate-400 hover:text-slate-655 focus:outline-none text-xl font-semibold">&times;</button>
    </div>

    <form action="/admin/verifications/verify-document" method="POST" class="space-y-4 text-xs">
      <input type="hidden" id="reject-document-id" name="document_id">
      <input type="hidden" name="status" value="Rejected">

      <div class="space-y-1">
        <label for="rejection_reason" class="block font-bold text-slate-600">Alasan Penolakan / Catatan Revisi</label>
        <textarea id="rejection_reason" name="rejection_reason" rows="3" required class="block w-full px-3 py-2 border border-slate-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-red-500 bg-slate-50" placeholder="Tuliskan catatan alasan mengapa dokumen ini ditolak..."></textarea>
      </div>

      <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
        <button type="button" onclick="closeRejectModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-full transition-colors cursor-pointer">Batal</button>
        <button type="submit" class="px-4 py-2 bg-red-655 hover:bg-red-700 text-white font-bold rounded-full transition-colors cursor-pointer shadow-sm">Tolak Dokumen</button>
      </div>
    </form>
  </div>
</div>

<script>
  function openPreviewModal(docId, ext) {
    const panel = document.getElementById('preview-content-panel');
    panel.innerHTML = '';

    const viewUrl = `/documents/view?id=${docId}`;

    if (ext === 'pdf') {
      panel.innerHTML = `<iframe src="${viewUrl}" class="w-full h-[600px] border-none rounded-2xl"></iframe>`;
    } else {
      panel.innerHTML = `<img src="${viewUrl}" class="max-w-full max-h-[600px] object-contain rounded-2xl shadow-sm" alt="Preview Dokumen" />`;
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

  function openRejectModal(docId) {
    document.getElementById('reject-document-id').value = docId;

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
