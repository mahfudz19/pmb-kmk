<?php

/**
 * @var array $wave
 * @var array $study_programs
 * @var array $document_types
 */
?>
<div class="w-full py-2 space-y-6">
  <div class="flex items-center justify-between">
    <a href="<?= getBaseUrl('/admin/master?tab=wave') ?>" class="inline-flex items-center gap-2 text-xs font-bold text-slate-650 hover:text-slate-900 transition-colors">
      <span>←</span> Kembali ke Data Master
    </a>
  </div>

  <div class="bg-white rounded-xl p-6 md:p-8 shadow-sm border border-slate-200/80 space-y-2">
    <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">⚙️ Pengaturan Gelombang & Program Studi</h2>
    <p class="text-xs text-slate-500">Konfigurasikan program studi aktif, biaya formulir, berkas syarat tambahan, serta tahapan ujian seleksi untuk gelombang: <strong><?= htmlspecialchars($wave['name']) ?></strong>.</p>
  </div>

  <form id="wave-detail-form" method="POST" action="<?= getBaseUrl('/admin/master/wave-detail/save') ?>" enctype="multipart/form-data" class="flex flex-col lg:flex-row gap-6 items-start">

    <!-- Wave General Fee Configuration Card (Left Column) -->
    <div class="w-full lg:w-96 lg:shrink-0 space-y-6">
      <div class="bg-white rounded-xl p-6 border border-slate-200 shadow-sm space-y-6">
        <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
          <input type="hidden" name="wave_id" value="<?= htmlspecialchars($wave['id']) ?>">
          <div>
            <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider">💰 Biaya Pendaftaran Gelombang</h3>
            <p class="text-[11px] text-slate-400 mt-0.5">Konfigurasikan biaya formulir awal pendaftaran beserta dokumen rincian/brosur biaya untuk gelombang ini.</p>
          </div>
        </div>

        <div class="flex flex-col gap-6 text-xs">
          <div class="space-y-1.5">
            <label for="registration_fee_total" class="block text-xs font-bold text-slate-450 uppercase tracking-wider">Nominal Biaya Formulir (Rp)</label>
            <input type="number" id="registration_fee_total" name="registration_fee_total" value="<?= htmlspecialchars($wave['registration_fee_total'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-bold" placeholder="Contoh: 100000 (Kosongkan/isi 0 jika gratis)">
          </div>

          <div class="space-y-1.5">
            <label for="registration_fee_archive" class="block text-xs font-bold text-slate-455 uppercase tracking-wider">Upload PDF Brosur / Rincian Biaya (Gelombang)</label>
            <input type="file" id="registration_fee_archive" accept="application/pdf,image/*" name="registration_fee_archive" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-750 hover:file:bg-indigo-100 cursor-pointer border border-slate-200 rounded-xl p-1 bg-slate-50">
            <?php if (!empty($wave['registration_fee_archive'])): ?>
              <p class="text-[10px] text-indigo-650 font-bold mt-1.5 flex items-center gap-1.5">
                <a href="<?= getBaseUrl(htmlspecialchars($wave['registration_fee_archive'])) ?>" target="_blank" class="hover:underline">📄 Lihat Brosur Saat Ini</a>
              </p>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden p-6 space-y-4">
        <div>
          <h4 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-1.5 font-sans">📝 Tahapan Ujian / Seleksi Masuk Gelombang</h4>
          <p class="text-xs text-slate-500">Tahapan ujian ini berlaku untuk seluruh pendaftar pada gelombang ini.</p>
        </div>
        <button type="button" onclick="addExamRow()" class="px-3 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-750 text-[10px] font-extrabold rounded-lg transition-colors cursor-pointer">+ Tambah Ujian</button>
        <div id="exam-rows-container" class="space-y-4">
          <?php
          $stages = json_decode($wave['exam_stages'] ?? '[]', true) ?: [];
          if (empty($stages)):
          ?>
            <div class="text-center text-[10px] text-slate-400 py-3 font-semibold empty-exams-placeholder">Belum ada tahapan ujian. Klik tambah untuk menambahkan jadwal CBT / wawancara.</div>
            <?php else: foreach ($stages as $idx => $stg): ?>
              <div class="bg-white p-4 rounded-xl border border-slate-150 space-y-3 exam-row">
                <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                  <span class="text-xs font-extrabold text-indigo-750 flex items-center gap-1.5">
                    📅 Tahap Ke-<?= $idx + 1 ?>
                  </span>
                  <button type="button" onclick="removeExamRow(this)" class="text-red-550 hover:text-red-800 text-xs font-bold font-mono cursor-pointer flex items-center gap-1">
                    &times; Hapus Tahap
                  </button>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                  <div class="space-y-1">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase">Tanggal Ujian</label>
                    <input type="date" name="exam_date[]" value="<?= htmlspecialchars($stg['date']) ?>" required class="appearance-none block w-full px-2 py-1.5 border border-slate-200 rounded-lg text-xs bg-slate-50 font-medium">
                  </div>
                  <div class="space-y-1">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase">Jam Pelaksanaan</label>
                    <input type="text" name="exam_time[]" value="<?= htmlspecialchars($stg['time'] ?? '') ?>" placeholder="Contoh: 09:00 - 11:00 WIB" class="appearance-none block w-full px-2 py-1.5 border border-slate-200 rounded-lg text-xs bg-slate-50 font-medium">
                  </div>
                </div>
                <div class="space-y-1">
                  <label class="block text-[10px] font-bold text-slate-500 uppercase">Tipe Seleksi</label>
                  <select name="exam_type[]" class="block w-full px-2 py-1.5 border border-slate-200 rounded-lg text-xs bg-slate-50 font-medium">
                    <option value="offline" <?= ($stg['type'] ?? '') === 'offline' ? 'selected' : '' ?>>Offline (Tatap Muka)</option>
                    <option value="online" <?= ($stg['type'] ?? '') === 'online' ? 'selected' : '' ?>>Online (Virtual/CBT)</option>
                  </select>
                </div>
                <div class="space-y-1">
                  <label class="block text-[10px] font-bold text-slate-500 uppercase">Tempat / Ruangan / Link Seleksi</label>
                  <input type="text" name="exam_place[]" value="<?= htmlspecialchars($stg['place'] ?? '') ?>" placeholder="Contoh: Lab Komputer Gedung B, / zoom link" class="appearance-none block w-full px-2 py-1.5 border border-slate-200 rounded-lg text-xs bg-slate-50 font-medium">
                </div>
                <div class="space-y-1">
                  <label class="block text-[10px] font-bold text-slate-500 uppercase">Keterangan / Materi Seleksi</label>
                  <textarea name="exam_description[]" placeholder="Contoh: Membawa laptop sendiri dan kartu ujian" class="appearance-none block w-full px-2 py-1.5 border border-slate-200 rounded-lg text-xs bg-slate-50 font-medium"><?= htmlspecialchars($stg['description'] ?? '') ?></textarea>
                </div>
              </div>
          <?php endforeach;
          endif; ?>
        </div>
      </div>
    </div>

    <!-- Study Programs Section (Right Column) -->
    <div class="flex-1 w-full min-w-0 space-y-4">
      <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
        <div>
          <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider">🎓 Program Studi Tersedia</h3>
          <p class="text-[11px] text-slate-400 mt-0.5">Centang untuk mengaktifkan prodi di gelombang ini, lalu klik <strong>Atur Detail</strong> untuk mengatur biaya & berkas.</p>
        </div>
        <div class="text-[11px] font-bold text-slate-500 bg-slate-100 px-3 py-1.5 rounded-lg shrink-0">
          <span id="active-prodi-count" class="text-indigo-600 font-extrabold">0</span> dari <?= count($study_programs) ?> Prodi Aktif
        </div>
      </div>

      <div class="grid grid-cols-1 gap-3">
        <?php foreach ($study_programs as $sp):
          $isConfigured = isset($mapped_programs[$sp['id']]);
          $config = $mapped_programs[$sp['id']] ?? null;
          $docs = $config['required_documents'] ?? [];
          $reregFee = (int)($config['reregistration_fee_total'] ?? 0);
          $hasArchive = !empty($config['reregistration_fee_archive']);
        ?>
          <div class="bg-white border rounded-xl shadow-sm p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 transition-all duration-200 <?= $isConfigured ? 'border-indigo-250 ring-1 ring-indigo-50/50' : 'border-slate-200' ?>" id="prodi-card-<?= $sp['id'] ?>">

            <!-- Left Info: Checkbox & Name -->
            <div class="flex items-start sm:items-center gap-3 min-w-0 sm:max-w-xs md:max-w-sm">
              <input type="checkbox" name="prodi_ids[]" value="<?= $sp['id'] ?>" id="chk-prodi-<?= $sp['id'] ?>" <?= $isConfigured ? 'checked' : '' ?> onchange="toggleProdiCard(<?= $sp['id'] ?>)" class="h-4 w-4 mt-0.5 sm:mt-0 rounded text-indigo-600 border-slate-350 focus:ring-indigo-500 cursor-pointer shrink-0">
              <div class="min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                  <label for="chk-prodi-<?= $sp['id'] ?>" class="text-sm font-extrabold text-slate-800 cursor-pointer select-none leading-snug">
                    <?= htmlspecialchars($sp['name']) ?>
                  </label>
                  <span class="text-xs font-semibold text-slate-400 font-mono">[<?= htmlspecialchars($sp['code']) ?>]</span>
                </div>
              </div>
            </div>

            <!-- Middle/Right Info: Fee & Documents Summary + Status + Action -->
            <div class="flex items-center justify-between sm:justify-end gap-3 sm:gap-4 shrink-0 flex-wrap sm:flex-nowrap border-t sm:border-t-0 pt-3 sm:pt-0 border-slate-100">

              <div class="flex items-center gap-2 sm:gap-3">
                <div class="bg-slate-50/90 rounded-lg px-3 py-1.5 border border-slate-200 text-left">
                  <div class="text-[9px] text-slate-400 font-bold uppercase">Biaya Daftar Ulang</div>
                  <div id="summary-fee-<?= $sp['id'] ?>" class="text-xs font-extrabold text-slate-800">
                    <?= $reregFee > 0 ? 'Rp ' . number_format($reregFee, 0, ',', '.') : 'Gratis / 0' ?>
                  </div>
                </div>

                <div class="bg-slate-50/90 rounded-lg px-3 py-1.5 border border-slate-200 text-left">
                  <div class="text-[9px] text-slate-400 font-bold uppercase">Berkas Wajib</div>
                  <div id="summary-docs-<?= $sp['id'] ?>" class="text-xs font-extrabold text-slate-700">
                    <?= count($docs) ?> Dokumen
                  </div>
                </div>
              </div>

              <div class="flex items-center gap-2">
                <span id="badge-status-<?= $sp['id'] ?>" class="text-[10px] font-extrabold px-2.5 py-1 rounded-full shrink-0 <?= $isConfigured ? 'bg-indigo-100 text-indigo-750' : 'bg-slate-100 text-slate-500' ?>">
                  <?= $isConfigured ? 'AKTIF' : 'NONAKTIF' ?>
                </span>

                <button type="button" onclick="openProdiModal(<?= $sp['id'] ?>)" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-xs rounded-xl transition-colors cursor-pointer">
                  <span>⚙️ Atur Detail</span>
                </button>
              </div>

            </div>

            <!-- Hidden Container / Modal Data Structure for this Prodi -->
            <div id="modal-content-<?= $sp['id'] ?>" class="hidden">
              <div class="space-y-4">
                <div class="bg-slate-50/70 p-3.5 rounded-xl border border-slate-200 space-y-3">
                  <h4 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                    💳 Biaya Daftar Ulang (Tahap 2)
                  </h4>
                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="space-y-1">
                      <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wide">Nominal Biaya (Rp)</label>
                      <input type="number" name="reregistration_fee_total_<?= $sp['id'] ?>" id="input-fee-<?= $sp['id'] ?>" oninput="updateSummaryFee(<?= $sp['id'] ?>)" value="<?= htmlspecialchars($config['reregistration_fee_total'] ?? 0) ?>" class="appearance-none block w-full px-3 py-2 border border-slate-200 rounded-lg text-xs bg-white font-bold" placeholder="Contoh: 1500000">
                    </div>
                    <div class="space-y-1">
                      <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wide">Upload PDF Panduan</label>
                      <input type="file" accept="application/pdf" name="reregistration_fee_archive_<?= $sp['id'] ?>" class="block w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[10px] file:font-bold file:bg-indigo-50 file:text-indigo-750 hover:file:bg-indigo-100 cursor-pointer border border-slate-200 rounded-lg p-0.5 bg-white">
                      <?php if (!empty($config['reregistration_fee_archive'])): ?>
                        <p class="text-[10px] text-indigo-650 font-semibold mt-1">
                          <a href="<?= htmlspecialchars($config['reregistration_fee_archive']) ?>" target="_blank" class="hover:underline">📄 Lihat PDF Rincian Saat Ini</a>
                        </p>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>

                <div class="space-y-2.5">
                  <div class="flex justify-between items-center">
                    <div>
                      <h4 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider">📁 Berkas Persyaratan Tambahan</h4>
                      <p class="text-[10px] text-slate-400">Pilih berkas khusus yang wajib diunggah untuk prodi ini.</p>
                    </div>
                    <button type="button" onclick="addDocumentRow(<?= $sp['id'] ?>)" class="px-2.5 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-750 text-[10px] font-extrabold rounded-lg transition-colors cursor-pointer">+ Tambah</button>
                  </div>

                  <div id="document-rows-container-<?= $sp['id'] ?>" class="space-y-2">
                    <div class="grid grid-cols-12 gap-2 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider pl-1">
                      <div class="col-span-5">Jenis Berkas</div>
                      <div class="col-span-5">Keterangan / Panduan</div>
                      <div class="col-span-2 text-right">Aksi</div>
                    </div>

                    <?php if (empty($docs)): ?>
                      <div class="text-center text-[10px] text-slate-400 py-3 font-semibold empty-docs-placeholder-<?= $sp['id'] ?> bg-slate-50 rounded-lg border border-dashed border-slate-200">Tidak ada dokumen tambahan. Klik tambah jika ada syarat khusus.</div>
                      <?php else: foreach ($docs as $doc): ?>
                        <div class="grid grid-cols-12 gap-2 items-center document-row">
                          <div class="col-span-5">
                            <select name="doc_type_ids_<?= $sp['id'] ?>[]" onchange="updateDocDescription(this)" required class="appearance-none block w-full px-2 py-1.5 border border-slate-200 rounded-lg text-xs bg-white font-medium">
                              <option value="" disabled>Pilih Dokumen</option>
                              <?php foreach ($document_types as $dt): ?>
                                <option value="<?= $dt['id'] ?>" data-description="<?= htmlspecialchars($dt['description'] ?? '') ?>" <?= (($doc['document_type_id'] ?? '') == $dt['id'] || ($doc['name'] ?? '') === $dt['name']) ? 'selected' : '' ?>><?= htmlspecialchars($dt['name']) ?></option>
                              <?php endforeach; ?>
                            </select>
                          </div>
                          <div class="col-span-5">
                            <input type="text" name="doc_descriptions_<?= $sp['id'] ?>[]" value="<?= htmlspecialchars($doc['description'] ?? '') ?>" class="appearance-none block w-full px-2 py-1.5 border border-slate-200 rounded-lg text-xs bg-white font-medium" placeholder="Keterangan">
                          </div>
                          <div class="col-span-2 text-right">
                            <button type="button" onclick="removeDocumentRow(this)" class="text-red-550 hover:text-red-800 text-xs font-bold cursor-pointer inline-flex items-center gap-0.5 transition-colors">
                              &times; Hapus
                            </button>
                          </div>
                        </div>
                    <?php endforeach;
                    endif; ?>
                  </div>
                </div>
              </div>
            </div>

          </div>
        <?php endforeach; ?>
      </div>

      <div class="flex justify-end gap-2 items-center border-t border-slate-100 pt-6 mt-8">
        <a href="<?= getBaseUrl('/admin/master?tab=wave') ?>" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition-colors focus:outline-none">
          Batal
        </a>
        <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-xs transition-colors shadow-sm focus:outline-none cursor-pointer">
          Simpan Pengaturan Gelombang
        </button>
      </div>
    </div>

  </form>
</div>

<!-- Modal Dialog for Prodi Detail -->
<div id="prodi-modal" class="fixed inset-0 z-[9999] flex items-center justify-center p-4" style="display: none !important; background: rgba(15, 23, 42, 0.7); backdrop-filter: blur(4px);">
  <div class="relative bg-white rounded-2xl shadow-2xl border border-slate-200 flex flex-col w-full max-w-2xl max-h-[85vh] h-[85vh] overflow-hidden" id="prodi-modal-card">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between shrink-0 bg-white">
      <div>
        <h3 class="text-sm font-extrabold text-slate-900" id="prodi-modal-title">Atur Detail Program Studi</h3>
        <p class="text-[11px] text-slate-400 mt-0.5" id="prodi-modal-subtitle">Konfigurasi biaya daftar ulang dan berkas persyaratan</p>
      </div>
      <button type="button" onclick="closeProdiModal()" class="text-slate-400 hover:text-slate-750 p-1.5 rounded-lg hover:bg-slate-100 transition-colors text-2xl font-bold leading-none cursor-pointer">&times;</button>
    </div>
    <div class="p-6 overflow-y-auto space-y-4 flex-1 bg-white" id="prodi-modal-body">
      <!-- Injected from prodi content placeholder -->
    </div>
    <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-2 shrink-0 rounded-b-2xl">
      <button type="button" id="btn-save-prodi-modal" onclick="saveAndCloseProdiModal()" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl transition-colors cursor-pointer shadow-sm flex items-center gap-2">
        <span>Selesai & Simpan</span>
      </button>
    </div>
  </div>
</div>

<script>
  const documentTypesList = <?= json_encode($document_types ?? []) ?>;
  const currentWaveId = <?= (int)($wave['id'] ?? 0) ?>;
  let currentOpenProdiId = null;

  function updateActiveProdiCount() {
    const chks = document.querySelectorAll('input[name="prodi_ids[]"]:checked');
    const countEl = document.getElementById('active-prodi-count');
    if (countEl) {
      countEl.textContent = chks.length;
    }
  }

  function toggleProdiCard(prodiId) {
    const card = document.getElementById(`prodi-card-${prodiId}`);
    const badge = document.getElementById(`badge-status-${prodiId}`);
    const chk = document.getElementById(`chk-prodi-${prodiId}`);
    if (!card || !badge || !chk) return;

    if (chk.checked) {
      card.classList.remove('border-slate-200');
      card.classList.add('border-indigo-250', 'ring-1', 'ring-indigo-50/50');
      badge.className = "text-[9px] font-extrabold px-2 py-0.5 rounded-full shrink-0 bg-indigo-100 text-indigo-750";
      badge.textContent = "AKTIF";
    } else {
      card.classList.remove('border-indigo-250', 'ring-1', 'ring-indigo-50/50');
      card.classList.add('border-slate-200');
      badge.className = "text-[9px] font-extrabold px-2 py-0.5 rounded-full shrink-0 bg-slate-100 text-slate-400";
      badge.textContent = "NONAKTIF";
    }
    updateActiveProdiCount();
  }

  function updateSummaryFee(prodiId) {
    const input = document.getElementById(`input-fee-${prodiId}`);
    const summary = document.getElementById(`summary-fee-${prodiId}`);
    if (!input || !summary) return;
    const val = parseInt(input.value, 10);
    if (isNaN(val) || val <= 0) {
      summary.textContent = 'Gratis / Belum Diatur';
    } else {
      summary.textContent = 'Rp ' + val.toLocaleString('id-ID');
    }
  }

  function updateSummaryDocsCount(prodiId) {
    const container = document.getElementById(`document-rows-container-${prodiId}`);
    const summary = document.getElementById(`summary-docs-${prodiId}`);
    if (!container || !summary) return;
    const count = container.querySelectorAll('.document-row').length;
    summary.textContent = `${count} Berkas Wajib`;
  }

  function openProdiModal(prodiId) {
    try {
      currentOpenProdiId = prodiId;
      const prodiCard = document.getElementById(`prodi-card-${prodiId}`);
      const prodiLabel = prodiCard ? (prodiCard.querySelector('label')?.textContent?.trim() || '') : '';

      const titleEl = document.getElementById('prodi-modal-title');
      const subtitleEl = document.getElementById('prodi-modal-subtitle');
      if (titleEl) titleEl.textContent = prodiLabel ? `Atur: ${prodiLabel}` : 'Atur Detail Program Studi';
      if (subtitleEl) subtitleEl.textContent = 'Konfigurasi biaya daftar ulang & berkas persyaratan';

      const contentHolder = document.getElementById(`modal-content-${prodiId}`);
      const modalBody = document.getElementById('prodi-modal-body');

      if (contentHolder && modalBody) {
        while (contentHolder.firstChild) {
          modalBody.appendChild(contentHolder.firstChild);
        }
      }

      const modal = document.getElementById('prodi-modal');
      if (modal) {
        modal.style.setProperty('display', 'flex', 'important');
      }

      filterDocumentOptions(prodiId);
    } catch (e) {
      console.error('Error opening modal:', e);
    }
  }

  async function saveAndCloseProdiModal() {
    if (!currentOpenProdiId) return;
    const prodiId = currentOpenProdiId;
    const saveBtn = document.getElementById('btn-save-prodi-modal');
    const originalText = saveBtn ? saveBtn.innerHTML : 'Selesai & Simpan';

    try {
      if (saveBtn) {
        saveBtn.disabled = true;
        saveBtn.innerHTML = `
          <svg class="animate-spin h-3.5 w-3.5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
          </svg>
          <span>Menyimpan...</span>
        `;
      }

      const formData = new FormData();
      formData.append('wave_id', currentWaveId);
      formData.append('study_program_id', prodiId);

      const feeInput = document.getElementById(`input-fee-${prodiId}`);
      if (feeInput) {
        formData.append(`reregistration_fee_total_${prodiId}`, feeInput.value);
      }

      const fileInput = document.getElementById(`input-file-${prodiId}`);
      if (fileInput && fileInput.files && fileInput.files[0]) {
        formData.append(`reregistration_fee_archive_${prodiId}`, fileInput.files[0]);
      }

      const container = document.getElementById(`document-rows-container-${prodiId}`);
      if (container) {
        const docSelects = container.querySelectorAll(`select[name="doc_type_ids_${prodiId}[]"]`);
        const docDescs = container.querySelectorAll(`input[name="doc_descriptions_${prodiId}[]"]`);

        docSelects.forEach((select) => {
          if (select.value) {
            formData.append(`doc_type_ids_${prodiId}[]`, select.value);
          }
        });
        docDescs.forEach((desc) => {
          formData.append(`doc_descriptions_${prodiId}[]`, desc.value);
        });
      }

      const res = await fetch('<?= getBaseUrl('/admin/master/wave-detail/save-prodi') ?>', {
        method: 'POST',
        body: formData
      });

      const result = await res.json();
      if (!result.success) {
        alert(result.message || 'Gagal menyimpan detail prodi');
      } else {
        // Otomatis aktifkan checkbox prodi bila belum tercentang
        const chk = document.getElementById(`chk-prodi-${prodiId}`);
        if (chk && !chk.checked) {
          chk.checked = true;
          toggleProdiCard(prodiId);
        }
      }
    } catch (err) {
      console.error('Error saving single prodi:', err);
      alert('Terjadi kesalahan koneksi saat menyimpan.');
    } finally {
      if (saveBtn) {
        saveBtn.disabled = false;
        saveBtn.innerHTML = originalText;
      }
      closeProdiModal();
    }
  }

  function closeProdiModal() {
    try {
      if (!currentOpenProdiId) return;
      const prodiId = currentOpenProdiId;
      const contentHolder = document.getElementById(`modal-content-${prodiId}`);
      const modalBody = document.getElementById('prodi-modal-body');

      if (contentHolder && modalBody) {
        while (modalBody.firstChild) {
          contentHolder.appendChild(modalBody.firstChild);
        }
      }

      updateSummaryFee(prodiId);
      updateSummaryDocsCount(prodiId);

      const modal = document.getElementById('prodi-modal');
      if (modal) {
        modal.style.setProperty('display', 'none', 'important');
      }
      currentOpenProdiId = null;
    } catch (e) {
      console.error('Error closing modal:', e);
    }
  }

  // Ensure any open modal content is moved back into the form before submitting
  document.getElementById('wave-detail-form').addEventListener('submit', function() {
    if (currentOpenProdiId) {
      const prodiId = currentOpenProdiId;
      const contentHolder = document.getElementById(`modal-content-${prodiId}`);
      const modalBody = document.getElementById('prodi-modal-body');
      while (modalBody.firstChild) {
        contentHolder.appendChild(modalBody.firstChild);
      }
    }
  });

  function filterDocumentOptions(prodiId) {
    const container = document.getElementById(`document-rows-container-${prodiId}`);
    if (!container) return;
    const selects = container.querySelectorAll(`select[name="doc_type_ids_${prodiId}[]"]`);
    const selectedValues = Array.from(selects).map(s => s.value).filter(val => val !== '');

    selects.forEach(select => {
      const curVal = select.value;
      Array.from(select.options).forEach(opt => {
        if (opt.value === '') return;
        const selectedElsewhere = selectedValues.includes(opt.value) && opt.value !== curVal;
        if (selectedElsewhere) {
          opt.disabled = true;
          opt.style.display = 'none';
        } else {
          opt.disabled = false;
          opt.style.display = '';
        }
      });
    });
  }

  function updateDocDescription(select) {
    const selectedOpt = select.options[select.selectedIndex];
    const desc = selectedOpt.getAttribute('data-description') || '';
    const row = select.closest('.document-row');
    const inputDesc = row.querySelector('input[name^="doc_descriptions_"]');
    if (inputDesc) {
      inputDesc.value = desc;
    }
    const nameAttr = select.getAttribute('name');
    const match = nameAttr.match(/doc_type_ids_(\d+)\[\]/);
    if (match) {
      filterDocumentOptions(match[1]);
      updateSummaryDocsCount(match[1]);
    }
  }

  function addDocumentRow(prodiId) {
    const container = document.getElementById(`document-rows-container-${prodiId}`);
    const placeholder = container.querySelector(`.empty-docs-placeholder-${prodiId}`);
    if (placeholder) {
      placeholder.remove();
    }

    const row = document.createElement('div');
    row.className = "grid grid-cols-12 gap-2 items-center document-row";

    let optionsHtml = '<option value="" disabled selected>Pilih Jenis Dokumen</option>';
    documentTypesList.forEach(dt => {
      optionsHtml += `<option value="${dt.id}" data-description="${dt.description ? dt.description.replace(/"/g, '&quot;') : ''}">${dt.name}</option>`;
    });

    row.innerHTML = `
      <div class="col-span-5">
        <select name="doc_type_ids_${prodiId}[]" onchange="updateDocDescription(this)" required class="appearance-none block w-full px-2.5 py-1.5 border border-slate-200 rounded-lg text-xs bg-white font-medium">
          ${optionsHtml}
        </select>
      </div>
      <div class="col-span-5">
        <input type="text" name="doc_descriptions_${prodiId}[]" class="appearance-none block w-full px-2.5 py-1.5 border border-slate-200 rounded-lg text-xs bg-white font-medium" placeholder="Keterangan">
      </div>
      <div class="col-span-2 text-right">
        <button type="button" onclick="removeDocumentRow(this)" class="text-red-550 hover:text-red-800 text-xs font-bold cursor-pointer inline-flex items-center gap-1 transition-colors">
          &times; Hapus
        </button>
      </div>
    `;
    container.appendChild(row);
    filterDocumentOptions(prodiId);
    updateSummaryDocsCount(prodiId);
  }

  function removeDocumentRow(button) {
    const row = button.closest('.document-row');
    const container = row.parentNode;
    const prodiId = container.id.replace('document-rows-container-', '');
    row.remove();

    const remainingRows = container.querySelectorAll('.document-row');
    if (remainingRows.length === 0) {
      const placeholder = document.createElement('div');
      placeholder.className = `text-center text-[10px] text-slate-400 py-4 font-semibold empty-docs-placeholder-${prodiId} bg-slate-50 rounded-lg border border-dashed border-slate-200`;
      placeholder.textContent = "Tidak ada dokumen tambahan. Klik tambah jika ingin mewajibkan dokumen khusus.";
      container.appendChild(placeholder);
    }
    filterDocumentOptions(prodiId);
    updateSummaryDocsCount(prodiId);
  }

  function addExamRow() {
    const container = document.getElementById('exam-rows-container');
    const placeholder = container.querySelector('.empty-exams-placeholder');
    if (placeholder) {
      placeholder.remove();
    }

    const stageNumber = container.querySelectorAll('.exam-row').length + 1;

    const row = document.createElement('div');
    row.className = "bg-white p-4 rounded-xl border border-slate-150 space-y-3 exam-row";
    row.innerHTML = `
      <div class="flex justify-between items-center border-b border-slate-100 pb-2">
        <span class="text-xs font-extrabold text-indigo-750 flex items-center gap-1.5">
          📅 Tahap Ke-<span class="stage-num">${stageNumber}</span>
        </span>
        <button type="button" onclick="removeExamRow(this)" class="text-red-550 hover:text-red-800 text-xs font-bold font-mono cursor-pointer flex items-center gap-1">
          &times; Hapus Tahap
        </button>
      </div>
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <div class="space-y-1">
            <label class="block text-[10px] font-bold text-slate-500 uppercase">Tanggal Ujian</label>
            <input type="date" name="exam_date[]" required class="appearance-none block w-full px-2 py-1.5 border border-slate-200 rounded-lg text-xs bg-slate-50 font-medium">
          </div>
          <div class="space-y-1">
            <label class="block text-[10px] font-bold text-slate-500 uppercase">Jam Pelaksanaan</label>
            <input type="text" name="exam_time[]" placeholder="Contoh: 09:00 - 11:00 WIB" class="appearance-none block w-full px-2 py-1.5 border border-slate-200 rounded-lg text-xs bg-slate-50 font-medium">
          </div>
        </div>
        <div class="space-y-1">
          <label class="block text-[10px] font-bold text-slate-500 uppercase">Tipe Seleksi</label>
          <select name="exam_type[]" class="block w-full px-2 py-1.5 border border-slate-200 rounded-lg text-xs bg-slate-50 font-medium">
            <option value="offline" selected>Offline (Tatap Muka)</option>
            <option value="online">Online (Virtual/CBT)</option>
          </select>
        </div>
      </div>
      <div class="space-y-1">
        <label class="block text-[10px] font-bold text-slate-500 uppercase">Tempat / Ruangan / Link Seleksi</label>
        <input type="text" name="exam_place[]" placeholder="Contoh: Lab Komputer Gedung B, / zoom link" class="appearance-none block w-full px-2 py-1.5 border border-slate-200 rounded-lg text-xs bg-slate-50 font-medium">
      </div>
      <div class="space-y-1">
        <label class="block text-[10px] font-bold text-slate-500 uppercase">Keterangan / Materi Seleksi</label>
        <textarea type="text" name="exam_description[]" placeholder="Contoh: Membawa laptop sendiri dan kartu ujian" class="appearance-none block w-full px-2 py-1.5 border border-slate-200 rounded-lg text-xs bg-slate-50 font-medium"></textarea>
      </div>
    `;
    container.appendChild(row);
  }

  function removeExamRow(button) {
    const row = button.closest('.exam-row');
    const container = row.parentNode;
    row.remove();

    const remaining = container.querySelectorAll('.exam-row');
    remaining.forEach((r, index) => {
      r.querySelector('.stage-num').textContent = index + 1;
    });

    if (remaining.length === 0) {
      const placeholder = document.createElement('div');
      placeholder.className = "text-center text-[10px] text-slate-400 py-3 font-semibold empty-exams-placeholder";
      placeholder.textContent = "Belum ada tahapan ujian. Klik tambah untuk menambahkan jadwal CBT / wawancara.";
      container.appendChild(placeholder);
    }
  }

  // Ensure prodi details are returned to form before submit
  document.getElementById('wave-detail-form').addEventListener('submit', function() {
    if (currentOpenProdiId) {
      const prodiId = currentOpenProdiId;
      const contentHolder = document.getElementById(`modal-content-${prodiId}`);
      const modalBody = document.getElementById('prodi-modal-body');
      while (modalBody.firstChild) {
        contentHolder.appendChild(modalBody.firstChild);
      }
    }
  });

  document.addEventListener('DOMContentLoaded', () => {
    updateActiveProdiCount();
    const chks = document.querySelectorAll('input[name="prodi_ids[]"]');
    chks.forEach(chk => {
      filterDocumentOptions(chk.value);
    });
  });
</script>