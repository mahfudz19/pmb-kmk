<?php

/**
 * @var string $tab
 * @var \Addon\Models\FacultyModel $faculties$faculties
 */
?>
<div class="w-full py-2 space-y-8">
  <div class="w-full">
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden">
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
            'registration-fee' => ['Biaya Formulir PMB', 'Kelola nominal biaya formulir pendaftaran global beserta brosur rincian biaya.'],
            'nim-format' => ['Format Custom NIM', 'Definisikan format generate Nomor Induk Mahasiswa otomatis setelah pembayaran disetujui.']
          ];
          $activeTitle = $titles[$tab] ?? ['Data Master', 'Kelola setelan sistem.'];
          ?>
          <h2 class="text-xl font-extrabold text-slate-900 tracking-tight"><?= htmlspecialchars($activeTitle[0]) ?></h2>
          <p class="mt-1 text-xs text-slate-500"><?= htmlspecialchars($activeTitle[1]) ?></p>
        </div>

        <?php if ($tab !== 'registration-fee'): ?>
          <div>
            <button
              type="button"
              onclick="openCreateModal()"
              class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-full transition-all shadow-sm cursor-pointer">
              + Tambah Data
            </button>
          </div>
        <?php endif; ?>
      </div>

      <!-- Panel Table -->
      <div class="overflow-x-auto">
        <?php if ($tab === 'wave'): ?>
          <table data-paginate="10" class="min-w-full divide-y divide-slate-100">
            <thead class="bg-slate-50/50">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">ID</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Nama Gelombang</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Tanggal Mulai</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Tanggal Selesai</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Status</th>
                <th class="px-6 py-4 text-right text-xs font-bold text-slate-400 uppercase tracking-wider">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
              <?php if (empty($waves)): ?>
                <tr>
                  <td colspan="6" class="text-center py-12 empty-row-placeholder">
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
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600"><?= htmlspecialchars($w['start_date']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600"><?= htmlspecialchars($w['end_date']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold <?= $w['is_active'] ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' ?>">
                        <?= $w['is_active'] ? 'AKTIF' : 'TIDAK AKTIF' ?>
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right space-x-2">
                      <a href="/admin/master/wave-detail?id=<?= htmlspecialchars($w['id']) ?>" class="inline-block text-xs font-bold text-indigo-650 hover:text-indigo-850 mr-2">Atur Detail</a>
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
                <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Kode</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Nama Program Studi</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Fakultas</th>
                <th class="px-6 py-4 text-right text-xs font-bold text-slate-400 uppercase tracking-wider">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
              <?php if (empty($study_programs)): ?>
                <tr>
                  <td colspan="5" class="text-center py-12 empty-row-placeholder">
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
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-800"><code><?= htmlspecialchars($sp['code']) ?></code></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-650"><?= htmlspecialchars($sp['name']) ?></td>
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
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right space-x-2">
                      <button type="button" onclick="openEditModal('nim-format', <?= htmlspecialchars(json_encode($nf)) ?>)" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 cursor-pointer">Edit</button>
                      <button type="button" onclick="openDeleteModal('nim-format', <?= htmlspecialchars($nf['id']) ?>)" class="text-xs font-bold text-red-650 hover:text-red-800 cursor-pointer">Hapus</button>
                    </td>
                  </tr>
              <?php endforeach;
              endif; ?>
            </tbody>
          </table>
        <?php elseif ($tab === 'registration-fee'): ?>
          <form action="<?= getBaseUrl('/admin/master/registration-fee/save') ?>" method="POST" enctype="multipart/form-data" class="p-8 space-y-6 max-w-2xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
              <div class="space-y-1">
                <label for="registration_fee_total" class="block text-sm font-semibold text-slate-700">Nominal Biaya Formulir (Rp) <span class="text-red-550">*</span></label>
                <?php $feeVal = (float)get_setting('registration_fee_total', '100000'); ?>
                <input type="number" id="registration_fee_total" name="registration_fee_total" required value="<?= htmlspecialchars($feeVal) ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-bold" placeholder="Contoh: 100000">
              </div>

              <div class="space-y-1">
                <label for="registration_fee_archive" class="block text-sm font-semibold text-slate-700">Upload PDF Brosur / Rincian Biaya (Global)</label>
                <input type="file" id="registration_fee_archive" accept="application/pdf" name="registration_fee_archive" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-750 hover:file:bg-indigo-100 cursor-pointer border border-slate-200 rounded-xl p-1 bg-slate-50">
                <?php
                $archiveVal = get_setting('registration_fee_archive', '');
                if (!empty($archiveVal)):
                ?>
                  <p class="text-[10px] text-indigo-650 font-bold mt-1.5 flex items-center gap-1.5">
                    <a href="<?= htmlspecialchars($archiveVal) ?>" target="_blank" class="hover:underline">📄 Lihat Brosur/Rincian Saat Ini</a>
                  </p>
                <?php endif; ?>
              </div>
            </div>

            <div class="flex justify-start">
              <button type="submit" class="inline-flex items-center justify-center px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition-all shadow-sm cursor-pointer">
                Simpan Setelan Biaya
              </button>
            </div>
          </form>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- Add/Edit Unified Modal -->
<div id="master-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
  <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closeMasterModal()"></div>
  <div class="relative bg-white rounded-3xl p-8 max-w-md w-full shadow-2xl border border-slate-100 space-y-6 transform scale-95 opacity-0 transition-all duration-200" id="master-modal-card">
    <div class="flex justify-between items-center border-b border-slate-100 pb-4">
      <h3 class="text-lg font-bold text-slate-900" id="modal-title">Tambah Data</h3>
      <button type="button" onclick="closeMasterModal()" class="text-slate-400 hover:text-slate-600 focus:outline-none text-2xl font-semibold">&times;</button>
    </div>

    <form id="modal-form" method="POST" action="/admin/master/create" class="space-y-4">
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
        <label for="input-code" class="block text-sm font-semibold text-slate-700">Kode / Singkatan</label>
        <input type="text" id="input-code" name="code" placeholder="Contoh: IF, FIK, FEB" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50">
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
        <input type="text" id="input-format-pattern" name="format_pattern" placeholder="Contoh: {YEAR}{PRODI_CODE}{SEQ}" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50">
        <div class="bg-indigo-50 p-3 rounded-lg text-[10px] text-indigo-750 space-y-1 mt-1 font-medium">
          <p class="font-bold">Placeholder yang didukung:</p>
          <ul class="list-disc pl-4 space-y-0.5">
            <li><code>{YEAR}</code>: Tahun akademik masuk (Contoh: 2026)</li>
            <li><code>{PRODI_CODE}</code>: Kode program studi (Contoh: IF)</li>
            <li><code>{DATE}</code>: Tanggal generate (format dmy, Contoh: 170726)</li>
            <li><code>{TIMESTAMP}</code>: 6 angka terakhir Unix timestamp (Contoh: <?= substr((string)time(), -6) ?>)</li>
            <li><code>{SEQ}</code>: Sequence nomor urut mahasiswa (Contoh: 001, 002)</li>
          </ul>
        </div>
      </div>

      <!-- Dynamic Field: Checkbox is_active -->
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
  <div class="relative bg-white rounded-3xl p-6 max-w-sm w-full shadow-2xl border border-slate-100 space-y-4 text-center transform scale-95 opacity-0 transition-all duration-200" id="delete-modal-card">
    <div class="text-4xl">⚠️</div>
    <div class="space-y-1">
      <h3 class="text-lg font-bold text-slate-900">Konfirmasi Hapus Data</h3>
      <p class="text-xs text-slate-500">Apakah Anda yakin ingin menghapus data ini? Tindakan ini bersifat permanen dan tidak dapat dibatalkan.</p>
    </div>
    <form action="/admin/master/delete" method="POST" class="flex gap-3">
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

<script>
  const activeTab = '<?= htmlspecialchars($tab) ?>';

  function openCreateModal() {
    document.getElementById('modal-title').textContent = 'Tambah Data';
    document.getElementById('modal-form').action = '/admin/master/create';
    document.getElementById('form-id').value = '';

    // Clear inputs
    if (document.getElementById('input-academic-year')) document.getElementById('input-academic-year').value = '';
    document.getElementById('input-name').value = '';
    document.getElementById('input-description').value = '';
    document.getElementById('input-start').value = '';
    document.getElementById('input-end').value = '';
    document.getElementById('input-code').value = '';
    document.getElementById('input-location').value = '';
    document.getElementById('input-bank-name').value = '';
    document.getElementById('input-account-number').value = '';
    document.getElementById('input-account-holder').value = '';
    document.getElementById('input-format-pattern').value = '';
    document.getElementById('input-active').checked = true;
    document.getElementById('input-required').checked = true;

    showFieldsForTab(activeTab);
    showModal('master-modal', 'master-modal-card');
  }

  function openEditModal(tab, item) {
    document.getElementById('modal-title').textContent = 'Ubah Data';
    document.getElementById('modal-form').action = '/admin/master/update';
    document.getElementById('form-id').value = item.id;

    // Fill inputs
    if (item.academic_year && document.getElementById('input-academic-year')) document.getElementById('input-academic-year').value = item.academic_year;
    if (item.name) document.getElementById('input-name').value = item.name;
    if (item.description) document.getElementById('input-description').value = item.description;
    if (item.start_date) document.getElementById('input-start').value = item.start_date;
    if (item.end_date) document.getElementById('input-end').value = item.end_date;
    if (item.code) document.getElementById('input-code').value = item.code;
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
</script>