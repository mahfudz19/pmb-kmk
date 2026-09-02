<?php

/**
 * @var string $tab
 * @var \Addon\Models\FacultyModel $faculties$faculties
 */
?>
<div class="w-full py-2 space-y-8">
  <div class="w-full">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200/80 overflow-hidden">
      <!-- Panel Header -->
      <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row justify-between sm:items-center gap-4">
        <div>
          <?php
          $titles = [
            'wave' => ['Gelombang Pendaftaran', 'Kelola periode buka-tutup gelombang ujian seleksi mahasiswa.'],
            'faculty' => ['Fakultas Kampus', 'Kelola daftar fakultas akademik yang tersedia di universitas.'],
            'study-program' => ['Program Studi (Jurusan)', 'Kelola daftar jurusan perkuliahan serta alokasi fakultasnya.'],
            'document-type' => ['Jenis Dokumen Persyaratan', 'Kelola scan berkas wajib yang harus di-upload pendaftar.'],
            'payment-account' => ['Rekening Penerimaan', 'Kelola nomor rekening bank institusi untuk pembayaran biaya formulir & daftar ulang.'],
            'nim-format' => ['Format Custom NIM', 'Definisikan format generate Nomor Induk Mahasiswa otomatis setelah pembayaran disetujui.']
          ];
          $activeTitle = $titles[$tab] ?? ['Data Master', 'Kelola setelan sistem.'];
          ?>
          <h2 class="text-xl font-extrabold text-slate-900 tracking-tight"><?= htmlspecialchars($activeTitle[0]) ?></h2>
          <p class="mt-1 text-xs text-slate-500"><?= htmlspecialchars($activeTitle[1]) ?></p>
        </div>

        <div class="flex items-center gap-2">
          <?php if ($tab === 'nim-format'): ?>
            <button
              type="button"
              onclick="openPlaceholderSettingsModal()"
              class="inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 text-xs font-bold rounded-full transition-all shadow-xs hover:shadow-sm cursor-pointer">
              ⚙️ Edit Placeholder
            </button>
          <?php endif; ?>
          <button
            type="button"
            onclick="openCreateModal()"
            class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-full transition-all shadow-sm cursor-pointer">
            + Tambah Data
          </button>
        </div>
      </div>

      <!-- Panel Table -->
      <div class="overflow-x-auto">
        <?php if ($tab === 'wave'): ?>
          <table data-paginate="10" class="min-w-full divide-y divide-slate-100">
            <thead class="bg-slate-50/50">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">ID</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Nama Gelombang</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Biaya Formulir</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Tanggal Mulai</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Tanggal Selesai</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Status</th>
                <th class="px-6 py-4 text-right text-xs font-bold text-slate-400 uppercase tracking-wider">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
              <?php if (empty($waves)): ?>
                <tr>
                  <td colspan="7" class="text-center py-12 empty-row-placeholder">
                    <div class="flex flex-col items-center justify-center space-y-3">
                      <div class="text-slate-350 text-4xl">🌊</div>
                      <h3 class="text-xs font-bold text-slate-700">Gelombang Pendaftaran Kosong</h3>
                      <p class="text-[11px] text-slate-400 max-w-xs mx-auto">Belum ada gelombang pendaftaran yang terdaftar.</p>
                    </div>
                  </td>
                </tr>
                <?php else: foreach ($waves as $w): ?>
                  <tr class="hover:bg-slate-50/30 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-slate-500">#<?= htmlspecialchars($w['id']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-800">
                      <?= htmlspecialchars($w['name']) ?>
                      <?php if (!empty($w['academic_year'])): ?>
                        <span class="text-[10px] text-indigo-650 font-bold ml-1.5 px-2 py-0.5 bg-indigo-50 border border-indigo-100 rounded-full"><?= htmlspecialchars($w['academic_year']) ?></span>
                      <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-750">
                      <?php if (empty($w['registration_fee_total']) || (float)$w['registration_fee_total'] <= 0): ?>
                        <span class="text-emerald-600 font-bold text-xs bg-emerald-50 px-2 py-1 rounded-lg">Gratis</span>
                      <?php else: ?>
                        <span>Rp<?= number_format((float)$w['registration_fee_total'], 0, ',', '.') ?></span>
                        <?php if (!empty($w['registration_fee_archive'])): ?>
                          <a href="<?= getBaseUrl(htmlspecialchars($w['registration_fee_archive'])) ?>" target="_blank" class="block text-[10px] text-indigo-650 hover:underline mt-0.5">📂 Brosur</a>
                        <?php endif; ?>
                      <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600"><?= htmlspecialchars($w['start_date']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600"><?= htmlspecialchars($w['end_date']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold <?= $w['is_active'] ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' ?>">
                        <?= $w['is_active'] ? 'AKTIF' : 'TIDAK AKTIF' ?>
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right space-x-2">
                      <a href="<?= getBaseUrl('/admin/master/wave-detail?id=' . htmlspecialchars($w['id'])) ?>" class="inline-block text-xs font-bold text-indigo-650 hover:text-indigo-850 mr-2">Atur Detail</a>
                      <button type="button" onclick="openEditModal('wave', <?= htmlspecialchars(json_encode($w)) ?>)" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 cursor-pointer">Edit</button>
                      <button type="button" onclick="openDeleteModal('wave', <?= htmlspecialchars($w['id']) ?>)" class="text-xs font-bold text-red-650 hover:text-red-800 cursor-pointer">Hapus</button>
                    </td>
                  </tr>
              <?php endforeach;
              endif; ?>
            </tbody>
          </table>

        <?php elseif ($tab === 'faculty'): ?>
          <table data-paginate="10" class="min-w-full divide-y divide-slate-100">
            <thead class="bg-slate-50/50">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">ID</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Kode</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Nama Fakultas</th>
                <th class="px-6 py-4 text-right text-xs font-bold text-slate-400 uppercase tracking-wider">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
              <?php if (empty($faculties)): ?>
                <tr>
                  <td colspan="4" class="text-center py-12 empty-row-placeholder">
                    <div class="flex flex-col items-center justify-center space-y-3">
                      <div class="text-slate-350 text-4xl">🏛️</div>
                      <h3 class="text-xs font-bold text-slate-700">Fakultas Kosong</h3>
                      <p class="text-[11px] text-slate-400 max-w-xs mx-auto">Belum ada data fakultas yang terdaftar.</p>
                    </div>
                  </td>
                </tr>
                <?php else: foreach ($faculties as $fc): ?>
                  <tr class="hover:bg-slate-50/30 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-slate-500">#<?= htmlspecialchars($fc['id']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-800"><code><?= htmlspecialchars($fc['code']) ?></code></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-650"><?= htmlspecialchars($fc['name']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right space-x-2">
                      <button type="button" onclick="openEditModal('faculty', <?= htmlspecialchars(json_encode($fc)) ?>)" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 cursor-pointer">Edit</button>
                      <button type="button" onclick="openDeleteModal('faculty', <?= htmlspecialchars($fc['id']) ?>)" class="text-xs font-bold text-red-650 hover:text-red-800 cursor-pointer">Hapus</button>
                    </td>
                  </tr>
              <?php endforeach;
              endif; ?>
            </tbody>
          </table>

        <?php elseif ($tab === 'study-program'): ?>
          <table data-paginate="10" class="min-w-full divide-y divide-slate-100">
            <thead class="bg-slate-50/50">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">ID</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Kode Teks</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Kode Angka</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Nama Program Studi</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Fakultas</th>
                <th class="px-6 py-4 text-right text-xs font-bold text-slate-400 uppercase tracking-wider">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
              <?php if (empty($study_programs)): ?>
                <tr>
                  <td colspan="6" class="text-center py-12 empty-row-placeholder">
                    <div class="flex flex-col items-center justify-center space-y-3">
                      <div class="text-slate-350 text-4xl">🎓</div>
                      <h3 class="text-xs font-bold text-slate-700">Program Studi Kosong</h3>
                      <p class="text-[11px] text-slate-400 max-w-xs mx-auto">Belum ada program studi yang terdaftar.</p>
                    </div>
                  </td>
                </tr>
                <?php else: foreach ($study_programs as $sp): ?>
                  <tr class="hover:bg-slate-50/30 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-slate-500">#<?= htmlspecialchars($sp['id']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-800"><span class="bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded font-mono text-xs"><?= htmlspecialchars($sp['code']) ?></span></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-800"><span class="bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded font-mono text-xs"><?= htmlspecialchars($sp['num_code'] ?? '-') ?></span></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-800 font-bold"><?= htmlspecialchars($sp['name']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-650">
                      <?php
                      $facName = '-';
                      foreach ($faculties as $fc) {
                        if ($fc['id'] == $sp['faculty_id']) {
                          $facName = $fc['name'];
                          break;
                        }
                      }
                      echo htmlspecialchars($facName);
                      ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right space-x-2">
                      <button type="button" onclick="openEditModal('study-program', <?= htmlspecialchars(json_encode($sp)) ?>)" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 cursor-pointer">Edit</button>
                      <button type="button" onclick="openDeleteModal('study-program', <?= htmlspecialchars($sp['id']) ?>)" class="text-xs font-bold text-red-650 hover:text-red-800 cursor-pointer">Hapus</button>
                    </td>
                  </tr>
              <?php endforeach;
              endif; ?>
            </tbody>
          </table>

        <?php elseif ($tab === 'document-type'): ?>
          <table data-paginate="10" class="min-w-full divide-y divide-slate-100">
            <thead class="bg-slate-50/50">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">ID</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Nama Dokumen</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Sifat</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Deskripsi</th>
                <th class="px-6 py-4 text-right text-xs font-bold text-slate-400 uppercase tracking-wider">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
              <?php if (empty($document_types)): ?>
                <tr>
                  <td colspan="5" class="text-center py-12 empty-row-placeholder">
                    <div class="flex flex-col items-center justify-center space-y-3">
                      <div class="text-slate-350 text-4xl">📄</div>
                      <h3 class="text-xs font-bold text-slate-700">Jenis Dokumen Kosong</h3>
                      <p class="text-[11px] text-slate-400 max-w-xs mx-auto">Belum ada jenis dokumen yang terdaftar.</p>
                    </div>
                  </td>
                </tr>
                <?php else: foreach ($document_types as $dt): ?>
                  <tr class="hover:bg-slate-50/30 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-slate-500">#<?= htmlspecialchars($dt['id']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-800"><?= htmlspecialchars($dt['name']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold <?= $dt['is_required'] ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-650' ?>">
                        <?= $dt['is_required'] ? 'WAJIB' : 'OPSIONAL' ?>
                      </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-500 max-w-xs truncate" title="<?= htmlspecialchars($dt['description'] ?? '') ?>"><?= htmlspecialchars($dt['description'] ?? '-') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right space-x-2">
                      <button type="button" onclick="openEditModal('document-type', <?= htmlspecialchars(json_encode($dt)) ?>)" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 cursor-pointer">Edit</button>
                      <button type="button" onclick="openDeleteModal('document-type', <?= htmlspecialchars($dt['id']) ?>)" class="text-xs font-bold text-red-650 hover:text-red-800 cursor-pointer">Hapus</button>
                    </td>
                  </tr>
              <?php endforeach;
              endif; ?>
            </tbody>
          </table>

        <?php elseif ($tab === 'payment-account'): ?>
          <table data-paginate="10" class="min-w-full divide-y divide-slate-100">
            <thead class="bg-slate-50/50">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">ID</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Nama Bank</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">No. Rekening</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Pemilik</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Status</th>
                <th class="px-6 py-4 text-right text-xs font-bold text-slate-400 uppercase tracking-wider">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
              <?php if (empty($payment_accounts)): ?>
                <tr>
                  <td colspan="6" class="text-center py-12 empty-row-placeholder">
                    <div class="flex flex-col items-center justify-center space-y-3">
                      <div class="text-slate-350 text-4xl">💳</div>
                      <h3 class="text-xs font-bold text-slate-700">Rekening Penerimaan Kosong</h3>
                      <p class="text-[11px] text-slate-400 max-w-xs mx-auto">Belum ada rekening penerimaan yang terdaftar.</p>
                    </div>
                  </td>
                </tr>
                <?php else: foreach ($payment_accounts as $pa): ?>
                  <tr class="hover:bg-slate-50/30 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-slate-500">#<?= htmlspecialchars($pa['id']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-800"><?= htmlspecialchars($pa['bank_name']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-650"><?= htmlspecialchars($pa['account_number']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-650"><?= htmlspecialchars($pa['account_holder']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold <?= $pa['is_active'] ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-650' ?>">
                        <?= $pa['is_active'] ? 'AKTIF' : 'TIDAK AKTIF' ?>
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right space-x-2">
                      <button type="button" onclick="openEditModal('payment-account', <?= htmlspecialchars(json_encode($pa)) ?>)" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 cursor-pointer">Edit</button>
                      <button type="button" onclick="openDeleteModal('payment-account', <?= htmlspecialchars($pa['id']) ?>)" class="text-xs font-bold text-red-650 hover:text-red-800 cursor-pointer">Hapus</button>
                    </td>
                  </tr>
              <?php endforeach;
              endif; ?>
            </tbody>
          </table>

        <?php elseif ($tab === 'nim-format'): ?>
          <table data-paginate="10" class="min-w-full divide-y divide-slate-100">
            <thead class="bg-slate-50/50">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">ID</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Nama Format</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Pola Format</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Status</th>
                <th class="px-6 py-4 text-right text-xs font-bold text-slate-400 uppercase tracking-wider">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
              <?php if (empty($nim_formats)): ?>
                <tr>
                  <td colspan="5" class="text-center py-12 empty-row-placeholder">
                    <div class="flex flex-col items-center justify-center space-y-3">
                      <div class="text-slate-350 text-4xl">🔢</div>
                      <h3 class="text-xs font-bold text-slate-700">Format Custom NIM Kosong</h3>
                      <p class="text-[11px] text-slate-400 max-w-xs mx-auto">Belum ada format custom NIM yang terdaftar.</p>
                    </div>
                  </td>
                </tr>
                <?php else: foreach ($nim_formats as $nf): ?>
                  <tr class="hover:bg-slate-50/30 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-slate-500">#<?= htmlspecialchars($nf['id']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-800"><?= htmlspecialchars($nf['name']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-650"><code><?= htmlspecialchars($nf['format_pattern']) ?></code></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold <?= $nf['is_active'] ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-650' ?>">
                        <?= $nf['is_active'] ? 'AKTIF' : 'TIDAK AKTIF' ?>
                      </span>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right space-x-2">
                      <button type="button" onclick="openEditModal('nim-format', <?= htmlspecialchars(json_encode($nf)) ?>)" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 cursor-pointer">Edit</button>
                      <button type="button" onclick="openDeleteModal('nim-format', <?= htmlspecialchars($nf['id']) ?>)" class="text-xs font-bold text-red-650 hover:text-red-800 cursor-pointer">Hapus</button>
                    </td>
                  </tr>
              <?php endforeach;
              endif; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- Add/Edit Unified Modal -->
<div id="master-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
  <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closeMasterModal()"></div>
  <div class="relative bg-white rounded-xl p-8 max-w-md w-full shadow-2xl border border-slate-100 space-y-6 transform scale-95 opacity-0 transition-all duration-200" id="master-modal-card">
    <div class="flex justify-between items-center border-b border-slate-100 pb-4">
      <h3 class="text-lg font-bold text-slate-900" id="modal-title">Tambah Data</h3>
      <button type="button" onclick="closeMasterModal()" class="text-slate-400 hover:text-slate-655 focus:outline-none text-2xl font-semibold">&times;</button>
    </div>

    <form id="modal-form" method="POST" action="<?= getBaseUrl('/admin/master/create') ?>" enctype="multipart/form-data" class="space-y-4">
      <input type="hidden" name="type" value="<?= htmlspecialchars($tab) ?>">
      <input type="hidden" name="id" id="form-id">

      <!-- Dynamic Field: academic_year (Wave) -->
      <div class="space-y-1 modal-field hidden" id="field-academic-year">
        <label for="input-academic-year" class="block text-sm font-semibold text-slate-700">Tahun Akademik</label>
        <input type="text" id="input-academic-year" name="academic_year" placeholder="Format: 2026/2027" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50">
      </div>

      <!-- Dynamic Field: name (General name) -->
      <div class="space-y-1 modal-field hidden" id="field-name">
        <label for="input-name" class="block text-sm font-semibold text-slate-700">Nama</label>
        <input type="text" id="input-name" name="name" placeholder="Masukkan nama" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50">
      </div>

      <!-- Dynamic Field: description (Wave) -->
      <div class="space-y-1 modal-field hidden" id="field-description">
        <label for="input-description" class="block text-sm font-semibold text-slate-700">Keterangan / Deskripsi</label>
        <textarea id="input-description" name="description" placeholder="Masukkan keterangan gelombang" rows="2" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50"></textarea>
      </div>

      <!-- Dynamic Field: dates (Wave) -->
      <div class="grid grid-cols-2 gap-4 modal-field hidden" id="field-dates">
        <div class="space-y-1">
          <label for="input-start" class="block text-sm font-semibold text-slate-700">Tanggal Mulai</label>
          <input type="date" id="input-start" name="start_date" class="appearance-none block w-full px-3 py-2.5 border border-slate-200 rounded-xl shadow-sm text-sm bg-slate-50">
        </div>
        <div class="space-y-1">
          <label for="input-end" class="block text-sm font-semibold text-slate-700">Tanggal Selesai</label>
          <input type="date" id="input-end" name="end_date" class="appearance-none block w-full px-3 py-2.5 border border-slate-200 rounded-xl shadow-sm text-sm bg-slate-50">
        </div>
      </div>

      <!-- Dynamic Field: code (Faculty & Program) -->
      <div class="space-y-1 modal-field hidden" id="field-code">
        <label for="input-code" class="block text-sm font-semibold text-slate-700">Kode Teks</label>
        <input type="text" id="input-code" name="code" placeholder="Contoh: IF, FIK, FEB" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50">
      </div>

      <!-- Dynamic Field: num_code (Program) -->
      <div class="space-y-1 modal-field hidden" id="field-num-code">
        <label for="input-num-code" class="block text-sm font-semibold text-slate-700">Kode Angka (2-3 Digit untuk NIM)</label>
        <input type="text" id="input-num-code" name="num_code" placeholder="Contoh: 01, 09, 10" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50">
      </div>

      <!-- Dynamic Field: faculty_id (Program Studi relation) -->
      <div class="space-y-1 modal-field hidden" id="field-faculty">
        <label for="input-faculty" class="block text-sm font-semibold text-slate-700">Fakultas Naungan</label>
        <select id="input-faculty" name="faculty_id" class="block w-full px-3 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50">
          <?php foreach ($faculties as $fc): ?>
            <option value="<?= htmlspecialchars($fc['id']) ?>"><?= htmlspecialchars($fc['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Dynamic Field: location (Selection Room) -->
      <div class="space-y-1 modal-field hidden" id="field-location">
        <label for="input-location" class="block text-sm font-semibold text-slate-700">Lokasi Gedung / Ruangan</label>
        <input type="text" id="input-location" name="location" placeholder="Masukkan lokasi detail" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50">
      </div>

      <!-- Dynamic Fields: payment-account -->
      <div class="space-y-1 modal-field hidden" id="field-bank-name">
        <label for="input-bank-name" class="block text-sm font-semibold text-slate-700">Nama Bank</label>
        <input type="text" id="input-bank-name" name="bank_name" placeholder="Contoh: Mandiri, BCA" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50">
      </div>
      <div class="space-y-1 modal-field hidden" id="field-account-number">
        <label for="input-account-number" class="block text-sm font-semibold text-slate-700">Nomor Rekening</label>
        <input type="text" id="input-account-number" name="account_number" placeholder="Masukkan nomor rekening" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50">
      </div>
      <div class="space-y-1 modal-field hidden" id="field-account-holder">
        <label for="input-account-holder" class="block text-sm font-semibold text-slate-700">Nama Pemilik Rekening</label>
        <input type="text" id="input-account-holder" name="account_holder" placeholder="Masukkan nama pemilik rekening" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50">
      </div>

      <!-- Dynamic Fields: nim-format -->
      <div class="space-y-1 modal-field hidden" id="field-format-pattern">
        <label for="input-format-pattern" class="block text-sm font-semibold text-slate-700">Pola Format NIM</label>
        <input type="text" id="input-format-pattern" name="format_pattern" placeholder="Contoh: {YEAR}{PRODI_NUM}{GROUP}{SEQ}" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50">
        <div class="bg-indigo-50 p-3 rounded-xl text-[10px] text-indigo-800 space-y-1.5 mt-1 font-medium border border-indigo-150">
          <p class="font-bold text-indigo-900">Placeholder yang didukung:</p>
          <ul class="list-disc pl-4 space-y-1">
            <li><code class="bg-white px-1 py-0.5 rounded text-indigo-700 font-bold">{YEAR}</code>: Tahun angkatan (panjang digit disesuaikan di konfigurasi placeholder, contoh: 26 atau 2026)</li>
            <li><code class="bg-white px-1 py-0.5 rounded text-indigo-700 font-bold">{PRODI_NUM}</code>: Kode angka program studi dari Master Prodi (contoh: 01, 09, 10)</li>
            <li><code class="bg-white px-1 py-0.5 rounded text-indigo-700 font-bold">{PRODI_CODE}</code>: Kode teks singkatan prodi dari Master Prodi (contoh: FAR, IF)</li>
            <li><code class="bg-white px-1 py-0.5 rounded text-indigo-700 font-bold">{GROUP}</code>: Kode kelompok mahasiswa (contoh: 1, 2, 3)</li>
            <li><code class="bg-white px-1 py-0.5 rounded text-indigo-700 font-bold">{SEQ}</code>: Sequence nomor urut mahasiswa (contoh: 001, 002)</li>
            <li><code class="bg-white px-1 py-0.5 rounded">{DATE}</code>: Tanggal generate (format disesuaikan di konfigurasi placeholder, default: DDMMYYYY)</li>
          </ul>
        </div>
      </div>

      <!-- Dynamic Field: registration_fee_total (Wave) -->

      <div class="flex items-center gap-3 modal-field hidden" id="field-active">
        <input type="checkbox" id="input-active" name="is_active" value="1" class="h-4 w-4 rounded text-indigo-650 border-slate-300 focus:ring-indigo-500">
        <label for="input-active" class="text-sm font-bold text-slate-750 cursor-pointer">Status Aktif / Buka</label>
      </div>

      <!-- Dynamic Field: Checkbox is_required -->
      <div class="flex items-center gap-3 modal-field hidden" id="field-required">
        <input type="checkbox" id="input-required" name="is_required" value="1" class="h-4 w-4 rounded text-indigo-650 border-slate-300 focus:ring-indigo-500">
        <label for="input-required" class="text-sm font-bold text-slate-750 cursor-pointer">Berkas ini Bersifat Wajib</label>
      </div>

      <div class="flex gap-3 pt-4">
        <button type="button" onclick="closeMasterModal()" class="flex-1 py-2.5 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-sm transition-colors focus:outline-none">
          Batal
        </button>
        <button type="submit" class="flex-1 py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl text-sm transition-colors shadow-sm focus:outline-none">
          Simpan Data
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="delete-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
  <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closeDeleteModal()"></div>
  <div class="relative bg-white rounded-xl p-6 max-w-sm w-full shadow-2xl border border-slate-100 space-y-4 text-center transform scale-95 opacity-0 transition-all duration-200" id="delete-modal-card">
    <div class="text-4xl">⚠️</div>
    <div class="space-y-1">
      <h3 class="text-lg font-bold text-slate-900">Konfirmasi Hapus Data</h3>
      <p class="text-xs text-slate-500">Apakah Anda yakin ingin menghapus data ini? Tindakan ini bersifat permanen dan tidak dapat dibatalkan.</p>
    </div>
    <form action="<?= getBaseUrl('/admin/master/delete') ?>" method="POST" class="flex gap-3">
      <input type="hidden" name="type" value="<?= htmlspecialchars($tab) ?>">
      <input type="hidden" name="id" id="delete-id">
      <button type="button" onclick="closeDeleteModal()" class="flex-1 py-2.5 px-4 bg-slate-150 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-xs transition-colors focus:outline-none">
        Batal
      </button>
      <button type="submit" class="flex-1 py-2.5 px-4 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl text-xs transition-colors shadow-sm focus:outline-none">
        Ya, Hapus Data
      </button>
    </form>
  </div>
</div>

<!-- Placeholder Settings Modal (For NIM Format) -->
<?php if ($tab === 'nim-format'):
  $placeholderGroups = $nim_setting['groups'] ?? [
    ['key' => '1', 'name' => 'Reguler 1'],
    ['key' => '2', 'name' => 'Reguler 2'],
    ['key' => '3', 'name' => 'Reguler 3'],
    ['key' => '4', 'name' => 'Karyawan'],
    ['key' => '5', 'name' => 'Pindahan / RPL']
  ];
  $groupsDesc = (string)($nim_setting['groups_desc'] ?? 'Mapping kode kelompok mahasiswa untuk placeholder {GROUP}');
  $seqDigits = (int)($nim_setting['seq_digits'] ?? 3);
  $seqDigitsDesc = (string)($nim_setting['seq_digits_desc'] ?? 'Minimum digit untuk sequence urutan {SEQ} (1-5, default 3)');
  $yearDigits = (int)($nim_setting['year_digits'] ?? 2);
  $yearDigitsDesc = (string)($nim_setting['year_digits_desc'] ?? 'Jumlah digit tahun untuk placeholder {YEAR} (2 sampai 4 digit, default 2)');
  $dateFormat = (string)($nim_setting['date_format'] ?? 'DDMMYYYY');
  $dateFormatDesc = (string)($nim_setting['date_format_desc'] ?? 'Format tanggal untuk placeholder {DATE} (kombinasi DD, MM, YYYY)');
?>
  <div id="placeholder-modal" class="hidden fixed inset-0 z-[9999] flex items-center justify-center p-4" style="background: rgba(15, 23, 42, 0.7); backdrop-filter: blur(4px);">
    <div class="fixed inset-0" onclick="closePlaceholderSettingsModal()"></div>
    <div class="relative bg-white rounded-2xl max-w-2xl w-full shadow-3xl border border-slate-200 flex flex-col transform scale-95 opacity-0 transition-all duration-200 overflow-hidden" style="max-height: 85vh; height: 85vh;" id="placeholder-modal-card">
      <!-- Modal Header (Sticky) -->
      <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between shrink-0 bg-white">
        <div>
          <h3 class="text-sm font-extrabold text-slate-900">Konfigurasi Nilai Placeholder NIM</h3>
          <p class="text-[11px] text-slate-400 mt-0.5">Atur nilai komponen dinamis dan deskripsi pengganti tag placeholder pada NIM.</p>
        </div>
        <button type="button" onclick="closePlaceholderSettingsModal()" class="text-slate-400 hover:text-slate-700 p-1.5 rounded-lg hover:bg-slate-100 transition-colors text-2xl font-bold leading-none cursor-pointer">&times;</button>
      </div>

      <!-- Modal Body (Scrollable) -->
      <form id="placeholder-form" method="POST" action="<?= getBaseUrl('/admin/master/nim-settings/update') ?>" class="flex flex-col flex-1 overflow-hidden m-0">
        <div class="p-6 space-y-4 overflow-y-auto flex-1 min-h-0 bg-white" style="scrollbar-width: thin;">
          <!-- 1. Konfigurasi {GROUP} -->
          <div class="space-y-2.5 bg-slate-50 p-3.5 rounded-xl border border-slate-200">
            <div class="flex items-center justify-between gap-2">
              <div>
                <label class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                  <code class="bg-indigo-100 text-indigo-700 px-1.5 py-0.5 rounded text-[11px] font-mono">{GROUP}</code>
                  <span>Kelompok / Tipe Mahasiswa</span>
                </label>
                <input type="text" name="groups_desc" value="<?= htmlspecialchars($groupsDesc) ?>" placeholder="Deskripsi placeholder kelompok" class="mt-1 w-full px-2.5 py-1 border border-slate-200 rounded-lg text-[11px] bg-white text-slate-600 focus:outline-none focus:border-indigo-400" title="Deskripsi pengaturan groups">
              </div>
              <button type="button" onclick="addGroupRow()" class="px-2.5 py-1 bg-indigo-600 hover:bg-indigo-700 text-white text-[11px] font-bold rounded-lg transition-colors cursor-pointer shrink-0">
                + Tambah
              </button>
            </div>

            <div id="group-rows-container" class="space-y-1.5 max-h-32 overflow-y-auto pr-1" style="scrollbar-width: thin;">
              <?php foreach ($placeholderGroups as $idx => $grp): ?>
                <div class="flex items-center gap-2 group-row">
                  <input type="text" name="group_key[]" value="<?= htmlspecialchars($grp['key'] ?? '') ?>" placeholder="Kode (misal: 1)" class="w-18 px-2 py-1 border border-slate-200 rounded-lg text-xs bg-white font-mono font-bold text-indigo-700 text-center" required>
                  <input type="text" name="group_name[]" value="<?= htmlspecialchars($grp['name'] ?? '') ?>" placeholder="Nama Kelompok (misal: Reguler 1)" class="flex-1 px-2.5 py-1 border border-slate-200 rounded-lg text-xs bg-white text-slate-800 font-medium" required>
                  <button type="button" onclick="removeGroupRow(this)" class="p-1 text-slate-400 hover:text-red-600 rounded-lg transition-colors cursor-pointer text-sm" title="Hapus">&times;</button>
                </div>
              <?php endforeach; ?>
            </div>
          </div>

          <!-- 2. Konfigurasi {SEQ}, {YEAR}, {DATE} -->
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
            <!-- SEQ -->
            <div class="space-y-1.5 bg-slate-50 p-3 rounded-xl border border-slate-200">
              <label class="text-xs font-bold text-slate-800 block">
                <code class="bg-indigo-100 text-indigo-700 px-1 py-0.5 rounded text-[10px] font-mono">{SEQ}</code>
                <span class="block mt-0.5 text-[11px]">Digit Urut</span>
              </label>
              <select name="seq_digits" class="block w-full px-2 py-1.5 border border-slate-200 rounded-lg text-xs bg-white font-semibold text-slate-700">
                <option value="1" <?= $seqDigits === 1 ? 'selected' : '' ?>>1 Digit (1..9)</option>
                <option value="2" <?= $seqDigits === 2 ? 'selected' : '' ?>>2 Digit (01..99)</option>
                <option value="3" <?= $seqDigits === 3 ? 'selected' : '' ?>>3 Digit (001..999)</option>
                <option value="4" <?= $seqDigits === 4 ? 'selected' : '' ?>>4 Digit (0001..9999)</option>
                <option value="5" <?= $seqDigits === 5 ? 'selected' : '' ?>>5 Digit (00001..99999)</option>
              </select>
              <input type="text" name="seq_digits_desc" value="<?= htmlspecialchars($seqDigitsDesc) ?>" placeholder="Deskripsi {SEQ}" class="w-full px-2 py-1 border border-slate-200 rounded-lg text-[10px] bg-white text-slate-600 focus:outline-none focus:border-indigo-400" title="Deskripsi pengaturan sequence">
            </div>

            <!-- YEAR -->
            <div class="space-y-1.5 bg-slate-50 p-3 rounded-xl border border-slate-200">
              <label class="text-xs font-bold text-slate-800 block">
                <code class="bg-indigo-100 text-indigo-700 px-1 py-0.5 rounded text-[10px] font-mono">{YEAR}</code>
                <span class="block mt-0.5 text-[11px]">Digit Tahun</span>
              </label>
              <select name="year_digits" class="block w-full px-2 py-1.5 border border-slate-200 rounded-lg text-xs bg-white font-semibold text-slate-700">
                <option value="2" <?= $yearDigits === 2 ? 'selected' : '' ?>>2 Digit (26)</option>
                <option value="4" <?= $yearDigits === 4 ? 'selected' : '' ?>>4 Digit (2026)</option>
              </select>
              <input type="text" name="year_digits_desc" value="<?= htmlspecialchars($yearDigitsDesc) ?>" placeholder="Deskripsi {YEAR}" class="w-full px-2 py-1 border border-slate-200 rounded-lg text-[10px] bg-white text-slate-600 focus:outline-none focus:border-indigo-400" title="Deskripsi pengaturan tahun">
            </div>

            <!-- DATE -->
            <div class="space-y-1.5 bg-slate-50 p-3 rounded-xl border border-slate-200">
              <label class="text-xs font-bold text-slate-800 block">
                <code class="bg-indigo-100 text-indigo-700 px-1 py-0.5 rounded text-[10px] font-mono">{DATE}</code>
                <span class="block mt-0.5 text-[11px]">Format Tanggal</span>
              </label>
              <select name="date_format" class="block w-full px-2 py-1.5 border border-slate-200 rounded-lg text-xs bg-white font-semibold text-slate-700">
                <option value="DDMMYYYY" <?= $dateFormat === 'DDMMYYYY' ? 'selected' : '' ?>>DDMMYYYY (02092026)</option>
                <option value="YYYYMMDD" <?= $dateFormat === 'YYYYMMDD' ? 'selected' : '' ?>>YYYYMMDD (20260902)</option>
                <option value="DDMMYY" <?= $dateFormat === 'DDMMYY' ? 'selected' : '' ?>>DDMMYY (020926)</option>
                <option value="YYMMDD" <?= $dateFormat === 'YYMMDD' ? 'selected' : '' ?>>YYMMDD (260902)</option>
                <option value="DDMM" <?= $dateFormat === 'DDMM' ? 'selected' : '' ?>>DDMM (0209)</option>
                <option value="MMDD" <?= $dateFormat === 'MMDD' ? 'selected' : '' ?>>MMDD (0902)</option>
              </select>
              <input type="text" name="date_format_desc" value="<?= htmlspecialchars($dateFormatDesc) ?>" placeholder="Deskripsi {DATE}" class="w-full px-2 py-1 border border-slate-200 rounded-lg text-[10px] bg-white text-slate-600 focus:outline-none focus:border-indigo-400" title="Deskripsi pengaturan tanggal">
            </div>
          </div>

          <!-- 3. Konfigurasi {PRODI_NUM} & {PRODI_CODE} -->
          <div class="space-y-2.5 bg-slate-50 p-3.5 rounded-xl border border-slate-200">
            <div>
              <label class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                <code class="bg-indigo-100 text-indigo-700 px-1.5 py-0.5 rounded text-[11px] font-mono">{PRODI_NUM}</code>
                <span class="text-slate-400">&</span>
                <code class="bg-indigo-100 text-indigo-700 px-1.5 py-0.5 rounded text-[11px] font-mono">{PRODI_CODE}</code>
                <span>Kode Program Studi</span>
              </label>
              <p class="text-[10px] text-slate-500">Kode singkatan teks dan kode angka per program studi.</p>
            </div>

            <div class="max-h-40 overflow-y-auto space-y-1.5 pr-1 divide-y divide-slate-200/60" style="scrollbar-width: thin;">
              <?php if (!empty($study_programs)): ?>
                <?php foreach ($study_programs as $sp): ?>
                  <div class="flex items-center gap-2 pt-1.5 first:pt-0">
                    <input type="hidden" name="prodi_id[]" value="<?= htmlspecialchars($sp['id']) ?>">
                    <span class="flex-1 text-xs font-semibold text-slate-700 truncate" title="<?= htmlspecialchars($sp['name']) ?>"><?= htmlspecialchars($sp['name']) ?></span>
                    <div class="w-18 sm:w-20">
                      <label class="block text-[8px] font-bold text-slate-400 uppercase">Teks</label>
                      <input type="text" name="prodi_code[]" value="<?= htmlspecialchars($sp['code'] ?? '') ?>" placeholder="Teks" class="w-full px-2 py-1 border border-slate-200 rounded-lg text-xs bg-white font-mono font-bold text-indigo-700 uppercase" required>
                    </div>
                    <div class="w-18 sm:w-20">
                      <label class="block text-[8px] font-bold text-slate-400 uppercase">Angka</label>
                      <input type="text" name="prodi_num_code[]" value="<?= htmlspecialchars($sp['num_code'] ?? '') ?>" placeholder="Angka" class="w-full px-2 py-1 border border-slate-200 rounded-lg text-xs bg-white font-mono font-bold text-emerald-700 text-center">
                    </div>
                  </div>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <!-- Modal Footer (Sticky) -->
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-2 shrink-0 rounded-b-2xl">
          <button type="button" onclick="closePlaceholderSettingsModal()" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition-colors focus:outline-none cursor-pointer">
            Batal
          </button>
          <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl transition-colors shadow-sm focus:outline-none cursor-pointer">
            Simpan Pengaturan
          </button>
        </div>
      </form>
    </div>
  </div>

  <template id="group-row-template">
    <div class="flex items-center gap-2 group-row">
      <input type="text" name="group_key[]" value="" placeholder="Kode" class="w-24 px-3 py-1.5 border border-slate-200 rounded-lg text-xs bg-white font-mono font-bold text-indigo-700 text-center" required>
      <input type="text" name="group_name[]" value="" placeholder="Nama Kelompok" class="flex-1 px-3 py-1.5 border border-slate-200 rounded-lg text-xs bg-white text-slate-800 font-medium" required>
      <button type="button" onclick="removeGroupRow(this)" class="p-1.5 text-slate-400 hover:text-red-600 rounded-lg transition-colors cursor-pointer text-sm" title="Hapus">&times;</button>
    </div>
  </template>
<?php endif; ?>

<script>
  function openCreateModal() {
    document.getElementById('modal-title').textContent = 'Tambah Data';
    document.getElementById('modal-form').action = "<?= getBaseUrl('/admin/master/create') ?>";
    document.getElementById('form-id').value = '';

    if (document.getElementById('input-academic-year')) document.getElementById('input-academic-year').value = '';
    document.getElementById('input-name').value = '';
    document.getElementById('input-description').value = '';
    document.getElementById('input-start').value = '';
    document.getElementById('input-end').value = '';
    document.getElementById('input-code').value = '';
    if (document.getElementById('input-num-code')) document.getElementById('input-num-code').value = '';
    document.getElementById('input-location').value = '';
    document.getElementById('input-bank-name').value = '';
    document.getElementById('input-account-number').value = '';
    document.getElementById('input-account-holder').value = '';
    document.getElementById('input-format-pattern').value = '';
    document.getElementById('input-active').checked = true;
    document.getElementById('input-required').checked = true;

    showFieldsForTab(document.querySelector('#modal-form input[name="type"]').value);
    showModal('master-modal', 'master-modal-card');
  }

  function openEditModal(tab, item) {
    document.getElementById('modal-title').textContent = 'Ubah Data';
    document.getElementById('modal-form').action = "<?= getBaseUrl('/admin/master/update') ?>";
    document.getElementById('form-id').value = item.id;

    // Fill inputs
    if (item.academic_year && document.getElementById('input-academic-year')) document.getElementById('input-academic-year').value = item.academic_year;
    if (item.name) document.getElementById('input-name').value = item.name;
    if (item.description) document.getElementById('input-description').value = item.description;
    if (item.start_date) document.getElementById('input-start').value = item.start_date;
    if (item.end_date) document.getElementById('input-end').value = item.end_date;
    if (item.code) document.getElementById('input-code').value = item.code;
    if (item.num_code && document.getElementById('input-num-code')) document.getElementById('input-num-code').value = item.num_code;
    if (item.faculty_id) document.getElementById('input-faculty').value = item.faculty_id;
    if (item.location) document.getElementById('input-location').value = item.location;
    if (item.bank_name) document.getElementById('input-bank-name').value = item.bank_name;
    if (item.account_number) document.getElementById('input-account-number').value = item.account_number;
    if (item.account_holder) document.getElementById('input-account-holder').value = item.account_holder;
    if (item.format_pattern) document.getElementById('input-format-pattern').value = item.format_pattern;

    if (item.is_active !== undefined) {
      document.getElementById('input-active').checked = parseInt(item.is_active) === 1;
    }
    if (item.is_required !== undefined) {
      document.getElementById('input-required').checked = parseInt(item.is_required) === 1;
    }

    showFieldsForTab(tab);
    showModal('master-modal', 'master-modal-card');
  }

  function openDeleteModal(tab, id) {
    document.getElementById('delete-id').value = id;
    showModal('delete-modal', 'delete-modal-card');
  }

  function showFieldsForTab(tab) {
    // Hide all first
    const fields = document.querySelectorAll('.modal-field');
    fields.forEach(f => f.classList.add('hidden'));

    // Show specific
    if (tab === 'wave') {
      document.getElementById('field-name').classList.remove('hidden');
      document.getElementById('field-academic-year').classList.remove('hidden');
      document.getElementById('field-description').classList.remove('hidden');
      document.getElementById('field-dates').classList.remove('hidden');
      document.getElementById('field-active').classList.remove('hidden');
    } else if (tab === 'faculty') {
      document.getElementById('field-code').classList.remove('hidden');
      document.getElementById('field-name').classList.remove('hidden');
    } else if (tab === 'study-program') {
      document.getElementById('field-faculty').classList.remove('hidden');
      document.getElementById('field-code').classList.remove('hidden');
      document.getElementById('field-num-code').classList.remove('hidden');
      document.getElementById('field-name').classList.remove('hidden');
    } else if (tab === 'document-type') {
      document.getElementById('field-name').classList.remove('hidden');
      document.getElementById('field-description').classList.remove('hidden');
      document.getElementById('field-required').classList.remove('hidden');
    } else if (tab === 'payment-account') {
      document.getElementById('field-bank-name').classList.remove('hidden');
      document.getElementById('field-account-number').classList.remove('hidden');
      document.getElementById('field-account-holder').classList.remove('hidden');
      document.getElementById('field-active').classList.remove('hidden');
    } else if (tab === 'nim-format') {
      document.getElementById('field-name').classList.remove('hidden');
      document.getElementById('field-format-pattern').classList.remove('hidden');
      document.getElementById('field-active').classList.remove('hidden');
    }
  }

  function showModal(modalId, cardId) {
    const modal = document.getElementById(modalId);
    const card = document.getElementById(cardId);
    modal.classList.remove('hidden');
    setTimeout(() => {
      card.classList.remove('scale-95', 'opacity-0');
      card.classList.add('scale-100', 'opacity-100');
    }, 10);
  }

  function hideModal(modalId, cardId) {
    const modal = document.getElementById(modalId);
    const card = document.getElementById(cardId);
    card.classList.remove('scale-100', 'opacity-100');
    card.classList.add('scale-95', 'opacity-0');
    setTimeout(() => {
      modal.classList.add('hidden');
    }, 200);
  }

  function closeMasterModal() {
    hideModal('master-modal', 'master-modal-card');
  }

  function closeDeleteModal() {
    hideModal('delete-modal', 'delete-modal-card');
  }

  function openPlaceholderSettingsModal() {
    showModal('placeholder-modal', 'placeholder-modal-card');
  }

  function closePlaceholderSettingsModal() {
    hideModal('placeholder-modal', 'placeholder-modal-card');
  }

  function addGroupRow() {
    const template = document.getElementById('group-row-template');
    const container = document.getElementById('group-rows-container');
    if (template && container) {
      const clone = template.content.cloneNode(true);
      container.appendChild(clone);
    }
  }

  function removeGroupRow(btn) {
    const row = btn.closest('.group-row');
    const container = document.getElementById('group-rows-container');
    if (container.querySelectorAll('.group-row').length <= 1) {
      alert('Minimal harus ada 1 kelompok mahasiswa.');
      return;
    }
    if (row) {
      row.remove();
    }
  }

  document.getElementById('modal-form').addEventListener('submit', function(e) {
    const tab = document.querySelector('#modal-form input[name="type"]').value;
    if (tab === 'nim-format') {
      const pattern = document.getElementById('input-format-pattern').value;
      if (!pattern) {
        e.preventDefault();
        alert('Pola format NIM tidak boleh kosong');
        return false;
      }
    }
  });
</script>