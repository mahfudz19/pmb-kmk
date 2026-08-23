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

  <div class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-slate-200/80 space-y-2">
    <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">⚙️ Pengaturan Gelombang & Program Studi</h2>
    <p class="text-xs text-slate-500">Konfigurasikan program studi aktif, biaya formulir, berkas syarat tambahan, serta tahapan ujian seleksi untuk gelombang: <strong><?= htmlspecialchars($wave['name']) ?></strong>.</p>
  </div>

  <form id="wave-detail-form" method="POST" action="<?= getBaseUrl('/admin/master/wave-detail/save') ?>" enctype="multipart/form-data" class="space-y-6">
    <input type="hidden" name="wave_id" value="<?= htmlspecialchars($wave['id']) ?>">

    <!-- Wave General Fee Configuration Card -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-6">
      <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
        <div>
          <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider">💰 Biaya Pendaftaran Gelombang</h3>
          <p class="text-[11px] text-slate-400 mt-0.5">Konfigurasikan biaya formulir awal pendaftaran beserta dokumen rincian/brosur biaya untuk gelombang ini.</p>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
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

    <div class="grid grid-cols-1 gap-6">
      <?php foreach ($study_programs as $sp):
        $isConfigured = isset($mapped_programs[$sp['id']]);
        $config = $mapped_programs[$sp['id']] ?? null;
      ?>
        <div class="bg-white border rounded-3xl shadow-sm overflow-hidden transition-all duration-200 <?= $isConfigured ? 'border-indigo-250 ring-1 ring-indigo-50/50' : 'border-slate-200' ?>" id="prodi-card-<?= $sp['id'] ?>">

          <div class="px-6 py-4 bg-slate-50/50 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
              <input type="checkbox" name="prodi_ids[]" value="<?= $sp['id'] ?>" id="chk-prodi-<?= $sp['id'] ?>" <?= $isConfigured ? 'checked' : '' ?> onchange="toggleProdiConfig(<?= $sp['id'] ?>)" class="h-4 w-4 rounded text-indigo-600 border-slate-350 focus:ring-indigo-500 cursor-pointer">
              <label for="chk-prodi-<?= $sp['id'] ?>" class="text-sm font-extrabold text-slate-800 cursor-pointer select-none">
                <?= htmlspecialchars($sp['name']) ?> <span class="text-xs font-semibold text-slate-400 font-mono">[<?= htmlspecialchars($sp['code']) ?>]</span>
              </label>
            </div>
            <div class="flex items-center gap-3">
              <span id="badge-status-<?= $sp['id'] ?>" class="text-[10px] font-extrabold px-2.5 py-0.5 rounded-full <?= $isConfigured ? 'bg-indigo-100 text-indigo-750' : 'bg-slate-100 text-slate-500' ?>">
                <?= $isConfigured ? 'AKTIF DI GELOMBANG INI' : 'TIDAK AKTIF' ?>
              </span>
              <button type="button" onclick="toggleProdiAccordion(<?= $sp['id'] ?>)" class="p-1 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition-all focus:outline-none cursor-pointer">
                <svg id="chevron-<?= $sp['id'] ?>" class="w-4 h-4 transform transition-transform <?= $isConfigured ? 'rotate-180' : '' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
              </button>
            </div>
          </div>

          <div id="config-panel-<?= $sp['id'] ?>" class="<?= $isConfigured ? '' : 'hidden' ?> p-6 space-y-6">

            <div class="bg-slate-50/30 p-5 rounded-2xl border border-slate-150 space-y-4 max-w-xl">
              <h4 class="text-xs font-extrabold text-slate-700 uppercase tracking-wider flex items-center gap-1.5 font-sans">💳 Biaya Daftar Ulang (Biaya Tahap 2)</h4>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1">
                  <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wide">Total Nominal (Rp)</label>
                  <input type="number" name="reregistration_fee_total_<?= $sp['id'] ?>" value="<?= htmlspecialchars($config['reregistration_fee_total'] ?? 0) ?>" class="appearance-none block w-full px-3 py-2.5 border border-slate-200 rounded-xl shadow-sm text-xs bg-white font-medium" placeholder="Contoh: 1500000">
                </div>
                <div class="space-y-1">
                  <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wide">Upload PDF Panduan Daftar Ulang</label>
                  <input type="file" accept="application/pdf" name="reregistration_fee_archive_<?= $sp['id'] ?>" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-750 hover:file:bg-indigo-100 cursor-pointer">
                  <?php if (!empty($config['reregistration_fee_archive'])): ?>
                    <p class="text-[10px] text-indigo-650 font-semibold mt-1">
                      <a href="<?= htmlspecialchars($config['reregistration_fee_archive']) ?>" target="_blank" class="hover:underline">📄 Lihat PDF Rincian Saat Ini</a>
                    </p>
                  <?php endif; ?>
                </div>
              </div>
            </div>

            <div class="space-y-3 bg-slate-50/20 p-5 rounded-2xl border border-slate-150">
              <div class="flex justify-between items-center">
                <h4 class="text-xs font-extrabold text-slate-750 uppercase tracking-wider">📁 Berkas Persyaratan Tambahan Program Studi</h4>
                <button type="button" onclick="addDocumentRow(<?= $sp['id'] ?>)" class="px-3 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-750 text-[10px] font-extrabold rounded-lg transition-colors cursor-pointer">+ Tambah Syarat</button>
              </div>
              <div id="document-rows-container-<?= $sp['id'] ?>" class="space-y-2">
                <div class="grid grid-cols-12 gap-3 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider pl-1">
                  <div class="col-span-5">Nama Berkas Wajib</div>
                  <div class="col-span-5">Keterangan / Panduan</div>
                  <div class="col-span-2 text-right">Aksi</div>
                </div>

                <?php
                $docs = $config['required_documents'] ?? [];
                if (empty($docs)):
                ?>
                  <div class="text-center text-[10px] text-slate-400 py-3 font-semibold empty-docs-placeholder-<?= $sp['id'] ?>">Tidak ada dokumen tambahan. Klik tambah jika ingin mewajibkan dokumen khusus.</div>
                  <?php else: foreach ($docs as $doc): ?>
                    <div class="grid grid-cols-12 gap-3 items-center document-row">
                      <div class="col-span-5">
                        <select name="doc_type_ids_<?= $sp['id'] ?>[]" onchange="updateDocDescription(this)" required class="appearance-none block w-full px-3 py-2 border border-slate-200 rounded-lg text-xs bg-white font-medium">
                          <option value="" disabled>Pilih Jenis Dokumen</option>
                          <?php foreach ($document_types as $dt): ?>
                            <option value="<?= $dt['id'] ?>" data-description="<?= htmlspecialchars($dt['description'] ?? '') ?>" <?= (($doc['document_type_id'] ?? '') == $dt['id'] || ($doc['name'] ?? '') === $dt['name']) ? 'selected' : '' ?>><?= htmlspecialchars($dt['name']) ?></option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                      <div class="col-span-5">
                        <input type="text" name="doc_descriptions_<?= $sp['id'] ?>[]" value="<?= htmlspecialchars($doc['description'] ?? '') ?>" class="appearance-none block w-full px-3 py-2 border border-slate-200 rounded-lg text-xs bg-white font-medium" placeholder="Pilih jenis dokumen untuk memuat deskripsi default">
                      </div>
                      <div class="col-span-2 text-right">
                        <button type="button" onclick="removeDocumentRow(this)" class="text-red-550 hover:text-red-800 text-xs font-bold cursor-pointer inline-flex items-center gap-1 transition-colors">
                          &times; Hapus Syarat
                        </button>
                      </div>
                    </div>
                <?php endforeach;
                endif; ?>
              </div>
            </div>

          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="bg-white border border-slate-200 rounded-3xl shadow-sm overflow-hidden p-6 space-y-4">
      <div class="flex justify-between items-center border-b border-slate-100 pb-3">
        <div>
          <h4 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-1.5 font-sans">📝 Tahapan Ujian / Seleksi Masuk Gelombang</h4>
          <p class="text-xs text-slate-500">Tahapan ujian ini berlaku untuk seluruh pendaftar pada gelombang ini.</p>
        </div>
        <button type="button" onclick="addExamRow()" class="px-3 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-750 text-[10px] font-extrabold rounded-lg transition-colors cursor-pointer">+ Tambah Ujian</button>
      </div>
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
              <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="space-y-1">
                  <label class="block text-[10px] font-bold text-slate-500 uppercase">Tanggal Ujian</label>
                  <input type="date" name="exam_date[]" value="<?= htmlspecialchars($stg['date']) ?>" required class="appearance-none block w-full px-2 py-1.5 border border-slate-200 rounded-lg text-xs bg-slate-50 font-medium">
                </div>
                <div class="space-y-1">
                  <label class="block text-[10px] font-bold text-slate-500 uppercase">Jam Pelaksanaan</label>
                  <input type="text" name="exam_time[]" value="<?= htmlspecialchars($stg['time'] ?? '') ?>" placeholder="Contoh: 09:00 - 11:00 WIB" class="appearance-none block w-full px-2 py-1.5 border border-slate-200 rounded-lg text-xs bg-slate-50 font-medium">
                </div>
                <div class="space-y-1">
                  <label class="block text-[10px] font-bold text-slate-500 uppercase">Tipe Seleksi</label>
                  <select name="exam_type[]" class="block w-full px-2 py-1.5 border border-slate-200 rounded-lg text-xs bg-slate-50 font-medium">
                    <option value="offline" <?= ($stg['type'] ?? '') === 'offline' ? 'selected' : '' ?>>Offline (Tatap Muka)</option>
                    <option value="online" <?= ($stg['type'] ?? '') === 'online' ? 'selected' : '' ?>>Online (Virtual/CBT)</option>
                  </select>
                </div>
              </div>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="space-y-1">
                  <label class="block text-[10px] font-bold text-slate-500 uppercase">Tempat / Ruangan / Link Seleksi</label>
                  <input type="text" name="exam_place[]" value="<?= htmlspecialchars($stg['place'] ?? '') ?>" placeholder="Contoh: Lab Komputer Gedung B, / zoom link" class="appearance-none block w-full px-2 py-1.5 border border-slate-200 rounded-lg text-xs bg-slate-50 font-medium">
                </div>
                <div class="space-y-1">
                  <label class="block text-[10px] font-bold text-slate-500 uppercase">Keterangan / Materi Seleksi</label>
                  <input type="text" name="exam_description[]" value="<?= htmlspecialchars($stg['description'] ?? '') ?>" placeholder="Contoh: Membawa laptop sendiri dan kartu ujian" class="appearance-none block w-full px-2 py-1.5 border border-slate-200 rounded-lg text-xs bg-slate-50 font-medium">
                </div>
              </div>
            </div>
        <?php endforeach;
        endif; ?>
      </div>
    </div>

    <div class="flex justify-between items-center border-t border-slate-100 pt-6 mt-8">
      <a href="<?= getBaseUrl('/admin/master?tab=wave') ?>" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition-colors focus:outline-none">
        Batal
      </a>
      <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-xs transition-colors shadow-sm focus:outline-none cursor-pointer">
        Simpan Pengaturan Gelombang
      </button>
    </div>
  </form>
</div>

<script>
  const documentTypesList = <?= json_encode($document_types ?? []) ?>;

  function toggleProdiAccordion(prodiId) {
    const panel = document.getElementById(`config-panel-${prodiId}`);
    const chevron = document.getElementById(`chevron-${prodiId}`);
    if (panel.classList.contains('hidden')) {
      panel.classList.remove('hidden');
      chevron.classList.add('rotate-180');
    } else {
      panel.classList.add('hidden');
      chevron.classList.remove('rotate-180');
    }
  }

  function toggleProdiConfig(prodiId) {
    const card = document.getElementById(`prodi-card-${prodiId}`);
    const panel = document.getElementById(`config-panel-${prodiId}`);
    const badge = document.getElementById(`badge-status-${prodiId}`);
    const chevron = document.getElementById(`chevron-${prodiId}`);
    const isChecked = document.getElementById(`chk-prodi-${prodiId}`).checked;

    if (isChecked) {
      panel.classList.remove('hidden');
      chevron.classList.add('rotate-180');
      card.classList.remove('border-slate-200');
      card.classList.add('border-indigo-250', 'ring-1', 'ring-indigo-50/50');
      badge.className = "text-[10px] font-extrabold px-2.5 py-0.5 rounded-full bg-indigo-100 text-indigo-750";
      badge.textContent = "AKTIF DI GELOMBANG INI";
    } else {
      panel.classList.add('hidden');
      chevron.classList.remove('rotate-180');
      card.classList.remove('border-indigo-250', 'ring-1', 'ring-indigo-50/50');
      card.classList.add('border-slate-200');
      badge.className = "text-[10px] font-extrabold px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-500";
      badge.textContent = "TIDAK AKTIF";
    }
  }

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
    }
  }

  function addDocumentRow(prodiId) {
    const container = document.getElementById(`document-rows-container-${prodiId}`);
    const placeholder = container.querySelector(`.empty-docs-placeholder-${prodiId}`);
    if (placeholder) {
      placeholder.remove();
    }

    const row = document.createElement('div');
    row.className = "grid grid-cols-12 gap-3 items-center document-row";

    let optionsHtml = '<option value="" disabled selected>Pilih Jenis Dokumen</option>';
    documentTypesList.forEach(dt => {
      optionsHtml += `<option value="${dt.id}" data-description="${dt.description ? dt.description.replace(/"/g, '&quot;') : ''}">${dt.name}</option>`;
    });

    row.innerHTML = `
      <div class="col-span-5">
        <select name="doc_type_ids_${prodiId}[]" onchange="updateDocDescription(this)" required class="appearance-none block w-full px-3 py-2 border border-slate-200 rounded-lg text-xs bg-white font-medium">
          ${optionsHtml}
        </select>
      </div>
      <div class="col-span-5">
        <input type="text" name="doc_descriptions_${prodiId}[]" class="appearance-none block w-full px-3 py-2 border border-slate-200 rounded-lg text-xs bg-white font-medium" placeholder="Pilih jenis dokumen untuk memuat deskripsi default">
      </div>
      <div class="col-span-2 text-right">
        <button type="button" onclick="removeDocumentRow(this)" class="text-red-550 hover:text-red-800 text-xs font-bold cursor-pointer inline-flex items-center gap-1 transition-colors">
          &times; Hapus Syarat
        </button>
      </div>
    `;
    container.appendChild(row);
    filterDocumentOptions(prodiId);
  }

  function removeDocumentRow(button) {
    const row = button.closest('.document-row');
    const container = row.parentNode;
    const prodiId = container.id.replace('document-rows-container-', '');
    row.remove();

    if (container.children.length === 1) {
      const placeholder = document.createElement('div');
      placeholder.className = `text-center text-[10px] text-slate-400 py-3 font-semibold empty-docs-placeholder-${prodiId}`;
      placeholder.textContent = "Tidak ada dokumen tambahan. Klik tambah jika ingin mewajibkan dokumen khusus.";
      container.appendChild(placeholder);
    }
    filterDocumentOptions(prodiId);
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
        <div class="space-y-1">
          <label class="block text-[10px] font-bold text-slate-500 uppercase">Tanggal Ujian</label>
          <input type="date" name="exam_date[]" required class="appearance-none block w-full px-2 py-1.5 border border-slate-200 rounded-lg text-xs bg-slate-50 font-medium">
        </div>
        <div class="space-y-1">
          <label class="block text-[10px] font-bold text-slate-500 uppercase">Jam Pelaksanaan</label>
          <input type="text" name="exam_time[]" placeholder="Contoh: 09:00 - 11:00 WIB" class="appearance-none block w-full px-2 py-1.5 border border-slate-200 rounded-lg text-xs bg-slate-50 font-medium">
        </div>
        <div class="space-y-1">
          <label class="block text-[10px] font-bold text-slate-500 uppercase">Tipe Seleksi</label>
          <select name="exam_type[]" class="block w-full px-2 py-1.5 border border-slate-200 rounded-lg text-xs bg-slate-50 font-medium">
            <option value="offline" selected>Offline (Tatap Muka)</option>
            <option value="online">Online (Virtual/CBT)</option>
          </select>
        </div>
      </div>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div class="space-y-1">
          <label class="block text-[10px] font-bold text-slate-500 uppercase">Tempat / Ruangan / Link Seleksi</label>
          <input type="text" name="exam_place[]" placeholder="Contoh: Lab Komputer Gedung B, / zoom link" class="appearance-none block w-full px-2 py-1.5 border border-slate-200 rounded-lg text-xs bg-slate-50 font-medium">
        </div>
        <div class="space-y-1">
          <label class="block text-[10px] font-bold text-slate-500 uppercase">Keterangan / Materi Seleksi</label>
          <input type="text" name="exam_description[]" placeholder="Contoh: Membawa laptop sendiri dan kartu ujian" class="appearance-none block w-full px-2 py-1.5 border border-slate-200 rounded-lg text-xs bg-slate-50 font-medium">
        </div>
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

  document.addEventListener('DOMContentLoaded', () => {
    const chks = document.querySelectorAll('input[name="prodi_ids[]"]');
    chks.forEach(chk => {
      filterDocumentOptions(chk.value);
    });
  });
</script>