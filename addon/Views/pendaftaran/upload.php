<div class="w-full py-2 space-y-8">
  <!-- Page Header -->
  <div class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-slate-200/80 flex flex-col sm:flex-row justify-between sm:items-center gap-4">
    <div>
      <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Unggah Berkas Persyaratan</h2>
      <p class="mt-1 text-xs text-slate-500">Silakan unggah dokumen persyaratan akademik wajib di bawah ini.</p>
    </div>
    <div>
      <a data-spa href="/dashboard" class="inline-flex items-center justify-center px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-full transition-colors">
        ← Kembali ke Dashboard
      </a>
    </div>
  </div>

  <!-- Documents Grid/List -->
  <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden">
    <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50">
      <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Daftar Dokumen Persyaratan</h3>
    </div>

    <div class="divide-y divide-slate-150">
      <?php if (empty($document_types)): ?>
        <div class="p-8 text-center text-sm text-slate-400 italic">Belum ada tipe dokumen persyaratan yang dikonfigurasi.</div>
      <?php else: ?>
        <div class="divide-y divide-slate-150 bg-white">
          <?php foreach ($document_types as $dt): ?>
            <?php 
              $docTypeId = $dt['document_type_id'] ?? null;
              $uploaded = $docTypeId ? ($uploaded_docs[$docTypeId . '_global'] ?? null) : null;
              $docDisplayName = htmlspecialchars($dt['name']);
              
              $prodisStr = implode(', ', $dt['prodi_names'] ?? []);
              $descParts = [];
              foreach ($dt['descriptions'] ?? [] as $pName => $pDesc) {
                  if (!empty($pDesc)) {
                      $descParts[] = $pName . ': ' . $pDesc;
                  }
              }
              $descStr = (!empty($descParts) ? ' (' . implode('; ', $descParts) . ')' : '');
            ?>
            <div class="p-6 md:p-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-6 hover:bg-slate-50/30 transition-colors">
              <!-- Left: Doc Type Info -->
              <div class="space-y-2 max-w-md">
                <div class="flex items-center gap-3">
                  <h4 class="text-sm font-bold text-slate-800"><?= $docDisplayName ?></h4>
                  <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-red-100 text-red-800">
                    WAJIB
                  </span>
                </div>
                <p class="text-[11px] text-slate-400 leading-normal">Prodi: <span class="text-indigo-750 font-bold"><?= $prodisStr ?></span><?= $descStr ?></p>
                <p class="text-[10px] text-slate-400 leading-normal">Format file: PDF, JPG, atau PNG (Maks 2MB).</p>
              </div>

              <!-- Middle: Status Badge -->
              <div class="flex items-center gap-2">
                <?php if ($uploaded): ?>
                  <?php if ($uploaded['status'] === 'Pending'): ?>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 border border-amber-300 text-amber-800">
                      <span class="h-1.5 w-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                      Menunggu Verifikasi
                    </span>
                  <?php elseif ($uploaded['status'] === 'Approved'): ?>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 border border-emerald-300 text-emerald-800">
                      <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                      Disetujui
                    </span>
                  <?php elseif ($uploaded['status'] === 'Rejected'): ?>
                    <div class="space-y-1">
                      <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-red-50 border border-red-300 text-red-800">
                        <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>
                        Ditolak
                      </span>
                      <?php if (!empty($uploaded['rejection_reason'])): ?>
                        <p class="text-[10px] text-red-650 font-bold">Catatan: <?= htmlspecialchars($uploaded['rejection_reason']) ?></p>
                      <?php endif; ?>
                    </div>
                  <?php endif; ?>
                <?php else: ?>
                  <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-100 border border-slate-350 text-slate-600">
                    <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>
                    Belum Diunggah
                  </span>
                <?php endif; ?>
              </div>

              <!-- Right: Upload Form / Actions -->
              <div class="flex items-center gap-3 w-full md:w-auto">
                <?php if ($uploaded): ?>
                  <!-- Actions for uploaded file -->
                  <button 
                    type="button" 
                    onclick="openPreviewModal(<?= $uploaded['id'] ?>, '<?= strtolower(pathinfo($uploaded['file_path'], PATHINFO_EXTENSION)) ?>')"
                    class="flex-grow md:flex-grow-0 px-4 py-2 border border-slate-200 rounded-full text-xs font-bold text-slate-700 hover:bg-slate-50 transition-colors cursor-pointer"
                  >
                    👁️ Preview
                  </button>

                  <?php if ($uploaded['status'] !== 'Approved'): ?>
                    <!-- Can change file if not approved yet -->
                    <button 
                      type="button" 
                      onclick="triggerFileUpload(<?= $dt['document_type_id'] ?>, null)" 
                      class="flex-grow md:flex-grow-0 px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-bold rounded-full transition-colors cursor-pointer"
                    >
                      🔄 Ganti Berkas
                    </button>
                  <?php else: ?>
                    <!-- Locked -->
                    <button 
                      type="button" 
                      disabled 
                      class="flex-grow md:flex-grow-0 px-4 py-2 bg-slate-100 text-slate-400 text-xs font-bold rounded-full cursor-not-allowed"
                    >
                      🔒 Terkunci
                    </button>
                  <?php endif; ?>

                <?php else: ?>
                  <!-- Action for not uploaded file -->
                  <button 
                    type="button" 
                    onclick="triggerFileUpload(<?= $dt['document_type_id'] ?>, null)" 
                    class="w-full md:w-auto px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-full transition-colors shadow-sm cursor-pointer"
                  >
                    📤 Unggah Berkas
                  </button>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Hidden File Inputs Holder -->
<form id="hidden-upload-form" method="POST" class="hidden">
  <input type="file" id="hidden-file-input" name="document" accept=".pdf,.png,.jpg,.jpeg" onchange="handleFileSelected(event)">
  <input type="hidden" id="hidden-doc-type-id" name="document_type_id">
</form>

<!-- Uploading Overlay Spinner -->
<div id="upload-spinner" class="hidden fixed inset-0 z-50 flex flex-col items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm">
  <div class="bg-white rounded-3xl p-8 max-w-sm w-full shadow-2xl border border-slate-100 text-center space-y-4">
    <div class="h-12 w-12 rounded-full border-4 border-indigo-600 border-t-transparent animate-spin mx-auto"></div>
    <div class="space-y-1">
      <h3 class="text-sm font-bold text-slate-900" id="spinner-title">Mengunggah berkas...</h3>
      <p class="text-[11px] text-slate-500">Mohon tunggu beberapa saat. Jangan menutup atau merefresh halaman.</p>
    </div>
  </div>
</div>

<!-- Preview Modal -->
<div id="preview-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
  <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closePreviewModal()"></div>
  <div class="relative bg-white rounded-3xl p-6 max-w-4xl w-full shadow-2xl border border-slate-100 space-y-4 transform scale-95 opacity-0 transition-all duration-200" id="preview-modal-card">
    <div class="flex justify-between items-center border-b border-slate-100 pb-3">
      <h3 class="text-base font-bold text-slate-900">Preview Dokumen</h3>
      <button type="button" onclick="closePreviewModal()" class="text-slate-400 hover:text-slate-655 focus:outline-none text-2xl font-semibold">&times;</button>
    </div>

    <!-- Content Panel -->
    <div class="w-full flex items-center justify-center bg-slate-50 rounded-2xl overflow-hidden min-h-[300px]" id="preview-content-panel">
      <!-- Injected via JS -->
    </div>
  </div>
</div>

<script>
  let activeDocTypeId = null;

  function triggerFileUpload(docTypeId) {
    activeDocTypeId = docTypeId;
    document.getElementById('hidden-doc-type-id').value = docTypeId;
    document.getElementById('hidden-file-input').click();
  }

  async function handleFileSelected(event) {
    const fileInput = event.target;
    if (fileInput.files.length === 0) return;

    const file = fileInput.files[0];
    
    // Client-side validations
    if (file.size > 2 * 1024 * 1024) {
      alert('Ukuran file maksimal adalah 2MB');
      fileInput.value = '';
      return;
    }

    const ext = file.name.split('.').pop().toLowerCase();
    if (!['pdf', 'png', 'jpg', 'jpeg'].includes(ext)) {
      alert('Format file harus berupa PDF, JPG, JPEG, atau PNG');
      fileInput.value = '';
      return;
    }

    // Show spinner overlay
    document.getElementById('upload-spinner').classList.remove('hidden');

    const form = document.getElementById('hidden-upload-form');
    const formData = new FormData(form);

    try {
      const response = await fetch('<?= getBaseUrl('/pendaftaran/dokumen/upload') ?>', {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
      });

      const res = await response.json();

      document.getElementById('upload-spinner').classList.add('hidden');
      fileInput.value = '';

      if (response.ok && res.success) {
        // Refresh to reflect changes
        window.location.reload();
      } else {
        alert(res.message || 'Gagal mengunggah file.');
      }
    } catch (e) {
      document.getElementById('upload-spinner').classList.add('hidden');
      fileInput.value = '';
      alert('Terjadi kesalahan koneksi server');
    }
  }

  function openPreviewModal(docId, ext) {
    const panel = document.getElementById('preview-content-panel');
    panel.innerHTML = ''; // Clear old content

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
</script>
