<?php
  $activeTab = $_GET['tab'] ?? 'scoring';
?>
<div class="w-full py-2 space-y-8">
  <!-- Page Header -->
  <div class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-slate-200/80 flex flex-col sm:flex-row justify-between sm:items-center gap-4">
    <div>
      <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Manajemen Seleksi & Kelulusan</h2>
      <p class="mt-1 text-xs text-slate-500">Kelola kuota penerimaan program studi, input nilai ujian dan wawancara, serta tentukan keputusan kelulusan akhir.</p>
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

  <!-- Tab Switcher Header -->
  <div class="flex border-b border-slate-200 gap-6">
    <button 
      type="button" 
      onclick="switchTab('scoring')" 
      id="tab-btn-scoring"
      class="pb-4 text-sm font-bold transition-all border-b-2 cursor-pointer <?= $activeTab === 'scoring' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-400 hover:text-slate-600' ?>"
    >
      🏅 Penilaian & Kelulusan
    </button>
    <button 
      type="button" 
      onclick="switchTab('quota')" 
      id="tab-btn-quota"
      class="pb-4 text-sm font-bold transition-all border-b-2 cursor-pointer <?= $activeTab === 'quota' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-400 hover:text-slate-600' ?>"
    >
      🏢 Daya Tampung / Kuota Prodi
    </button>
  </div>

  <!-- Tab 1: Penilaian & Kelulusan -->
  <div id="tab-content-scoring" class="<?= $activeTab === 'scoring' ? '' : 'hidden' ?> bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden">
    <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row justify-between sm:items-center gap-4">
      <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Hasil Seleksi Calon Mahasiswa</h3>
      <div class="flex gap-2">
        <form action="/admin/selection/publish-all" method="POST" onsubmit="return confirmAction(event, 'Publish Semua', 'Apakah Anda yakin ingin menerbitkan semua hasil kelulusan?')">
          <input type="hidden" name="is_published" value="1">
          <button type="submit" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-[10px] font-bold rounded-xl cursor-pointer shadow-sm">📢 Publish Semua</button>
        </form>
        <form action="/admin/selection/publish-all" method="POST" onsubmit="return confirmAction(event, 'Unpublish Semua', 'Apakah Anda yakin ingin menarik kembali semua pengumuman kelulusan?')">
          <input type="hidden" name="is_published" value="0">
          <button type="submit" class="px-3.5 py-2 bg-red-600 hover:bg-red-700 text-white text-[10px] font-bold rounded-xl cursor-pointer shadow-sm">🔒 Unpublish Semua</button>
        </form>
      </div>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="border-b border-slate-150 text-[10px] font-bold text-slate-450 uppercase tracking-wider bg-slate-50/30">
            <th class="px-6 py-4">Calon Mahasiswa</th>
            <th class="px-6 py-4">Program Pilihan</th>
            <th class="px-6 py-4 text-center">Keputusan</th>
            <th class="px-6 py-4 text-center">Publikasi</th>
            <th class="px-6 py-4">Prodi Penerimaan</th>
            <th class="px-6 py-4 text-center">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-150 text-xs text-slate-650">
          <?php if (empty($candidates)): ?>
            <tr>
              <td colspan="6" class="text-center py-12">
                <div class="flex flex-col items-center justify-center space-y-3">
                  <div class="text-slate-300 text-5xl">🎓</div>
                  <h3 class="text-sm font-bold text-slate-700">Data Pendaftar Kosong</h3>
                  <p class="text-xs text-slate-400 max-w-xs mx-auto">Belum ada calon pendaftar yang siap dinilai dalam proses seleksi.</p>
                </div>
              </td>
            </tr>
          <?php else: foreach ($candidates as $c): ?>
            <?php 
              $passedProdi = '-';
              foreach ($programs as $p) {
                if ($p['id'] == $c['passed_program_id']) {
                  $passedProdi = $p['name'];
                  break;
                }
              }
            ?>
            <tr class="hover:bg-slate-50/30 transition-colors">
              <td class="px-6 py-4">
                <div class="font-bold text-slate-800"><?= htmlspecialchars($c['full_name']) ?></div>
                <div class="text-[10px] text-slate-400 font-medium"><?= htmlspecialchars($c['email']) ?></div>
              </td>
              <td class="px-6 py-4 space-y-1">
                <div class="text-[10px] text-slate-600 font-semibold">1. <?= htmlspecialchars($c['program1_name'] ?? '-') ?></div>
                <?php if (!empty($c['program2_name'])): ?>
                  <div class="text-[10px] text-slate-500 font-medium">2. <?= htmlspecialchars($c['program2_name']) ?></div>
                <?php endif; ?>
                <?php if (!empty($c['program3_name'])): ?>
                  <div class="text-[10px] text-slate-500 font-medium">3. <?= htmlspecialchars($c['program3_name']) ?></div>
                <?php endif; ?>
              </td>
              <td class="px-6 py-4 text-center">
                <?php if ($c['selection_status'] === 'Lulus'): ?>
                  <span class="inline-flex px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 border border-emerald-300 text-emerald-700">Lulus</span>
                <?php elseif ($c['selection_status'] === 'Cadangan'): ?>
                  <span class="inline-flex px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 border border-amber-300 text-amber-700">Cadangan</span>
                <?php elseif ($c['selection_status'] === 'Tidak Lulus'): ?>
                  <span class="inline-flex px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-red-50 border border-red-300 text-red-700">Tidak Lulus</span>
                <?php else: ?>
                  <span class="inline-flex px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 border border-slate-200 text-slate-500">Pending</span>
                <?php endif; ?>
              </td>
              <td class="px-6 py-4 text-center">
                <?php if ((int)$c['is_published'] === 1): ?>
                  <span class="inline-flex px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 border border-emerald-200 text-emerald-700">Terbit</span>
                <?php else: ?>
                  <span class="inline-flex px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 border border-slate-200 text-slate-500">Draft</span>
                <?php endif; ?>
              </td>
              <td class="px-6 py-4 font-semibold text-slate-750"><?= htmlspecialchars($passedProdi) ?></td>
              <td class="px-6 py-4 text-center">
                <div class="flex items-center justify-center gap-2">
                  <button 
                    type="button" 
                    onclick="openScoringModal(<?= htmlspecialchars(json_encode($c)) ?>)"
                    class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-[10px] font-bold text-white rounded-full transition-colors cursor-pointer shadow-sm"
                  >
                    📝 Nilai
                  </button>
                  <form action="/admin/selection/publish" method="POST" class="inline">
                    <input type="hidden" name="registration_id" value="<?= $c['id'] ?>">
                    <input type="hidden" name="is_published" value="<?= (int)$c['is_published'] === 1 ? '0' : '1' ?>">
                    <button type="submit" class="px-3 py-1.5 border border-slate-200 hover:bg-slate-50 text-[10px] font-bold text-slate-750 rounded-full cursor-pointer transition-colors">
                      <?= (int)$c['is_published'] === 1 ? '🔒 Tarik' : '📢 Publish' ?>
                    </button>
                  </form>
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
        Menampilkan <?= min($totalCount, ($currentPage - 1) * $limit + 1) ?> s/d <?= min($totalCount, $currentPage * $limit) ?> dari <?= $totalCount ?> calon mahasiswa
      </div>
      <div class="flex items-center gap-1.5">
        <?php if ($currentPage > 1): ?>
          <a data-spa href="?page=<?= $currentPage - 1 ?>&tab=candidates" class="px-3 py-1.5 rounded-lg text-xs font-bold text-slate-700 bg-slate-50 hover:bg-slate-100 transition-colors border border-slate-200">Sebelumnya</a>
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
            <a data-spa href="?page=<?= $i ?>&tab=candidates" class="px-3 py-1.5 rounded-lg text-xs font-bold text-slate-700 bg-slate-50 hover:bg-slate-100 transition-colors border border-slate-200"><?= $i ?></a>
          <?php endif; ?>
        <?php endfor; ?>

        <?php if ($currentPage < $totalPages): ?>
          <a data-spa href="?page=<?= $currentPage + 1 ?>&tab=candidates" class="px-3 py-1.5 rounded-lg text-xs font-bold text-slate-700 bg-slate-50 hover:bg-slate-100 transition-colors border border-slate-200">Selanjutnya</a>
        <?php else: ?>
          <span class="px-3 py-1.5 rounded-lg text-xs font-bold text-slate-400 bg-slate-50 border border-slate-100 cursor-not-allowed">Selanjutnya</span>
        <?php endif; ?>
      </div>
    </div>
  <?php endif; ?>
</div>

  <!-- Tab 2: Pengaturan Kuota -->
  <div id="tab-content-quota" class="<?= $activeTab === 'quota' ? '' : 'hidden' ?> bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden">
    <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50">
      <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Kuota / Daya Tampung Program Studi</h3>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="border-b border-slate-150 text-[10px] font-bold text-slate-450 uppercase tracking-wider bg-slate-50/30">
            <th class="px-6 py-4">Kode Prodi</th>
            <th class="px-6 py-4">Nama Program Studi</th>
            <th class="px-6 py-4 text-center">Kuota Daya Tampung</th>
            <th class="px-6 py-4 text-center">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-150 text-xs text-slate-650">
          <?php foreach ($programs as $p): ?>
            <tr class="hover:bg-slate-50/30 transition-colors">
              <td class="px-6 py-4 font-mono font-bold text-slate-800"><?= htmlspecialchars($p['code']) ?></td>
              <td class="px-6 py-4 font-semibold text-slate-800"><?= htmlspecialchars($p['name']) ?></td>
              <td class="px-6 py-4 text-center font-bold text-slate-900"><?= $p['quota'] ?></td>
              <td class="px-6 py-4 text-center">
                <form action="/admin/selection/quota" method="POST" class="inline-flex items-center gap-2">
                  <input type="hidden" name="program_id" value="<?= $p['id'] ?>">
                  <input type="number" name="quota" value="<?= $p['quota'] ?>" required min="0" class="w-20 px-2 py-1 border border-slate-200 rounded-lg text-center focus:outline-none focus:ring-1 focus:ring-indigo-500 font-bold bg-slate-50">
                  <button type="submit" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-900 text-white text-[10px] font-bold rounded-lg cursor-pointer transition-colors shadow-sm">
                    Simpan Kuota
                  </button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Scoring & Status Modal -->
<div id="scoring-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
  <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closeScoringModal()"></div>
  <div class="relative bg-white rounded-3xl p-6 max-w-lg w-full shadow-2xl border border-slate-100 space-y-4 transform scale-95 opacity-0 transition-all duration-200" id="scoring-modal-card">
    <div class="flex justify-between items-center border-b border-slate-100 pb-2">
      <h3 class="text-sm font-bold text-slate-900" id="scoring-modal-title">Penilaian Calon Mahasiswa</h3>
      <button type="button" onclick="closeScoringModal()" class="text-slate-400 hover:text-slate-655 focus:outline-none text-xl font-semibold">&times;</button>
    </div>

    <form action="/admin/selection/save" method="POST" class="space-y-4 text-xs">
      <input type="hidden" id="score-registration-id" name="registration_id">

      <div class="grid grid-cols-2 gap-4 pt-3">
        <div class="space-y-1">
          <label for="status" class="block font-bold text-slate-600">Keputusan Seleksi <span class="text-red-550">*</span></label>
          <select id="status" name="status" required class="block w-full px-3 py-2 border border-slate-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-indigo-500 bg-slate-50 font-bold">
            <option value="Pending">Pending</option>
            <option value="Lulus">Lulus</option>
            <option value="Cadangan">Cadangan</option>
            <option value="Tidak Lulus">Tidak Lulus</option>
          </select>
        </div>
        <div class="space-y-1">
          <label for="passed_program_id" class="block font-bold text-slate-600">Program Studi Penerimaan</label>
          <select id="passed_program_id" name="passed_program_id" class="block w-full px-3 py-2 border border-slate-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-indigo-500 bg-slate-50 font-bold">
            <option value="">-- Pilih Prodi --</option>
            <?php foreach ($programs as $p): ?>
              <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="space-y-1">
        <label for="notes" class="block font-bold text-slate-600">Catatan Kelulusan (Opsional)</label>
        <textarea id="notes" name="notes" rows="3" class="block w-full px-3 py-2 border border-slate-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-indigo-500 bg-slate-50" placeholder="Catatan kelulusan untuk mahasiswa..."></textarea>
      </div>

      <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
        <button type="button" onclick="closeScoringModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-full transition-colors cursor-pointer">Batal</button>
        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-full transition-colors cursor-pointer shadow-sm">Simpan Penilaian</button>
      </div>
    </form>
  </div>
</div>

<script>
  const allProgramsList = <?= json_encode($programs) ?>;

  function switchTab(tab) {
    const scoringContent = document.getElementById('tab-content-scoring');
    const quotaContent = document.getElementById('tab-content-quota');
    const scoringBtn = document.getElementById('tab-btn-scoring');
    const quotaBtn = document.getElementById('tab-btn-quota');

    if (tab === 'scoring') {
      scoringContent.classList.remove('hidden');
      quotaContent.classList.add('hidden');
      scoringBtn.className = "pb-4 text-sm font-bold transition-all border-b-2 border-indigo-600 text-indigo-600 cursor-pointer";
      quotaBtn.className = "pb-4 text-sm font-bold transition-all border-b-2 border-transparent text-slate-400 hover:text-slate-600 cursor-pointer";
    } else {
      scoringContent.classList.add('hidden');
      quotaContent.classList.remove('hidden');
      scoringBtn.className = "pb-4 text-sm font-bold transition-all border-b-2 border-transparent text-slate-400 hover:text-slate-600 cursor-pointer";
      quotaBtn.className = "pb-4 text-sm font-bold transition-all border-b-2 border-indigo-600 text-indigo-600 cursor-pointer";
    }
  }

  function openScoringModal(c) {
    document.getElementById('score-registration-id').value = c.id;
    document.getElementById('scoring-modal-title').innerText = `Penilaian: ${c.full_name}`;

    document.getElementById('status').value = c.selection_status !== null ? c.selection_status : 'Pending';
    document.getElementById('notes').value = c.selection_notes !== null ? c.selection_notes : '';

    const passedSelect = document.getElementById('passed_program_id');
    passedSelect.innerHTML = '<option value="">-- Pilih Prodi --</option>';

    const chosenIds = [];

    if (c.program1_id && c.program1_name) {
      chosenIds.push(parseInt(c.program1_id));
      const opt1 = document.createElement('option');
      opt1.value = c.program1_id;
      opt1.textContent = `Pilihan 1: ${c.program1_name}`;
      passedSelect.appendChild(opt1);
    }

    if (c.program2_id && c.program2_name) {
      chosenIds.push(parseInt(c.program2_id));
      const opt2 = document.createElement('option');
      opt2.value = c.program2_id;
      opt2.textContent = `Pilihan 2: ${c.program2_name}`;
      passedSelect.appendChild(opt2);
    }

    if (c.program3_id && c.program3_name) {
      chosenIds.push(parseInt(c.program3_id));
      const opt3 = document.createElement('option');
      opt3.value = c.program3_id;
      opt3.textContent = `Pilihan 3: ${c.program3_name}`;
      passedSelect.appendChild(opt3);
    }

    const otherProdis = allProgramsList.filter(p => !chosenIds.includes(parseInt(p.id)));
    if (otherProdis.length > 0) {
      const group = document.createElement('optgroup');
      group.label = 'Program Studi Lainnya';
      otherProdis.forEach(p => {
        const opt = document.createElement('option');
        opt.value = p.id;
        opt.textContent = p.name;
        group.appendChild(opt);
      });
      passedSelect.appendChild(group);
    }

    const selectedPassedId = c.passed_program_id !== null ? c.passed_program_id : '';
    passedSelect.value = selectedPassedId;

    const modal = document.getElementById('scoring-modal');
    const card = document.getElementById('scoring-modal-card');
    
    modal.classList.remove('hidden');
    setTimeout(() => {
      card.classList.remove('scale-95', 'opacity-0');
      card.classList.add('scale-100', 'opacity-100');
    }, 10);
  }

  function closeScoringModal() {
    const modal = document.getElementById('scoring-modal');
    const card = document.getElementById('scoring-modal-card');
    
    card.classList.remove('scale-100', 'opacity-100');
    card.classList.add('scale-95', 'opacity-0');
    setTimeout(() => {
      modal.classList.add('hidden');
    }, 200);
  }
</script>
