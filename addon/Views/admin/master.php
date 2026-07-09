<div class="w-full py-2 space-y-8">
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

  <div class="w-full">
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden">
        <!-- Panel Header -->
        <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row justify-between sm:items-center gap-4">
          <div>
            <?php 
              $titles = [
                'academic-year' => ['Tahun Akademik', 'Kelola tahun kalender akademik pendaftaran PMB yang aktif.'],
                'wave' => ['Gelombang Pendaftaran', 'Kelola periode buka-tutup gelombang ujian seleksi mahasiswa.'],
                'faculty' => ['Fakultas Kampus', 'Kelola daftar fakultas akademik yang tersedia di universitas.'],
                'study-program' => ['Program Studi (Jurusan)', 'Kelola daftar jurusan perkuliahan serta alokasi fakultasnya.'],
                'admission-path' => ['Jalur Masuk Pendaftaran', 'Kelola opsi jalur penerimaan (Prestasi, Mandiri, dll.).'],
                'class' => ['Pilihan Kelas Kuliah', 'Kelola opsi waktu kuliah (Reguler Pagi, Sore, Karyawan).'],
                'selection-room' => ['Ruangan Ujian Seleksi', 'Kelola lokasi pelaksanaan tes tulis CBT atau wawancara fisik.'],
                'document-type' => ['Jenis Dokumen Persyaratan', 'Kelola scan berkas wajib yang harus di-upload pendaftar.']
              ];
              $activeTitle = $titles[$tab] ?? ['Data Master', 'Kelola setelan sistem.'];
            ?>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight"><?= htmlspecialchars($activeTitle[0]) ?></h2>
            <p class="mt-1 text-xs text-slate-500"><?= htmlspecialchars($activeTitle[1]) ?></p>
          </div>

          <div>
            <button
              type="button"
              onclick="openCreateModal()"
              class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-full transition-all shadow-sm cursor-pointer"
            >
              + Tambah Data
            </button>
          </div>
        </div>

        <!-- Panel Table -->
        <div class="overflow-x-auto">
          <?php if ($tab === 'academic-year'): ?>
            <table data-paginate="10" class="min-w-full divide-y divide-slate-100">
              <thead class="bg-slate-50/50">
                <tr>
                  <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">ID</th>
                  <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Tahun Akademik</th>
                  <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Status</th>
                  <th class="px-6 py-4 text-right text-xs font-bold text-slate-400 uppercase tracking-wider">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 bg-white">
                <?php if (empty($academic_years)): ?>
                  <tr><td colspan="4" class="text-center py-12 empty-row-placeholder"><div class="flex flex-col items-center justify-center space-y-3"><div class="text-slate-350 text-4xl">📅</div><h3 class="text-xs font-bold text-slate-700">Tahun Akademik Kosong</h3><p class="text-[11px] text-slate-400 max-w-xs mx-auto">Belum ada tahun akademik yang terdaftar.</p></div></td></tr>
                <?php else: foreach ($academic_years as $ay): ?>
                  <tr class="hover:bg-slate-50/30 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-slate-500">#<?= htmlspecialchars($ay['id']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-800"><?= htmlspecialchars($ay['year']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold <?= $ay['is_active'] ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' ?>">
                        <?= $ay['is_active'] ? 'AKTIF' : 'TIDAK AKTIF' ?>
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right space-x-2">
                      <button type="button" onclick="openEditModal('academic-year', <?= htmlspecialchars(json_encode($ay)) ?>)" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 cursor-pointer">Edit</button>
                      <button type="button" onclick="openDeleteModal('academic-year', <?= htmlspecialchars($ay['id']) ?>)" class="text-xs font-bold text-red-650 hover:text-red-800 cursor-pointer">Hapus</button>
                    </td>
                  </tr>
                <?php endforeach; endif; ?>
              </tbody>
            </table>

          <?php elseif ($tab === 'wave'): ?>
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
                  <tr><td colspan="6" class="text-center py-12 empty-row-placeholder"><div class="flex flex-col items-center justify-center space-y-3"><div class="text-slate-350 text-4xl">🌊</div><h3 class="text-xs font-bold text-slate-700">Gelombang Pendaftaran Kosong</h3><p class="text-[11px] text-slate-400 max-w-xs mx-auto">Belum ada gelombang pendaftaran yang terdaftar.</p></div></td></tr>
                <?php else: foreach ($waves as $w): ?>
                  <tr class="hover:bg-slate-50/30 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-slate-500">#<?= htmlspecialchars($w['id']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-800"><?= htmlspecialchars($w['name']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600"><?= htmlspecialchars($w['start_date']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600"><?= htmlspecialchars($w['end_date']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold <?= $w['is_active'] ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' ?>">
                        <?= $w['is_active'] ? 'AKTIF' : 'TIDAK AKTIF' ?>
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right space-x-2">
                      <button type="button" onclick="openEditModal('wave', <?= htmlspecialchars(json_encode($w)) ?>)" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 cursor-pointer">Edit</button>
                      <button type="button" onclick="openDeleteModal('wave', <?= htmlspecialchars($w['id']) ?>)" class="text-xs font-bold text-red-650 hover:text-red-800 cursor-pointer">Hapus</button>
                    </td>
                  </tr>
                <?php endforeach; endif; ?>
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
                  <tr><td colspan="4" class="text-center py-12 empty-row-placeholder"><div class="flex flex-col items-center justify-center space-y-3"><div class="text-slate-350 text-4xl">🏛️</div><h3 class="text-xs font-bold text-slate-700">Fakultas Kosong</h3><p class="text-[11px] text-slate-400 max-w-xs mx-auto">Belum ada fakultas yang terdaftar.</p></div></td></tr>
                <?php else: foreach ($faculties as $f): ?>
                  <tr class="hover:bg-slate-50/30 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-slate-500">#<?= htmlspecialchars($f['id']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-800 tracking-wider"><?= htmlspecialchars($f['code']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700"><?= htmlspecialchars($f['name']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right space-x-2">
                      <button type="button" onclick="openEditModal('faculty', <?= htmlspecialchars(json_encode($f)) ?>)" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 cursor-pointer">Edit</button>
                      <button type="button" onclick="openDeleteModal('faculty', <?= htmlspecialchars($f['id']) ?>)" class="text-xs font-bold text-red-650 hover:text-red-800 cursor-pointer">Hapus</button>
                    </td>
                  </tr>
                <?php endforeach; endif; ?>
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
                  <tr><td colspan="5" class="text-center py-12 empty-row-placeholder"><div class="flex flex-col items-center justify-center space-y-3"><div class="text-slate-350 text-4xl">🎓</div><h3 class="text-xs font-bold text-slate-700">Program Studi Kosong</h3><p class="text-[11px] text-slate-400 max-w-xs mx-auto">Belum ada program studi yang terdaftar.</p></div></td></tr>
                <?php else: foreach ($study_programs as $sp): ?>
                  <tr class="hover:bg-slate-50/30 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-slate-500">#<?= htmlspecialchars($sp['id']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-800 tracking-wider"><?= htmlspecialchars($sp['code']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700"><?= htmlspecialchars($sp['name']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 font-medium">
                      <?php 
                        $fac = array_values(array_filter($faculties, fn($fc) => $fc['id'] == $sp['faculty_id']));
                        echo htmlspecialchars($fac[0]['name'] ?? 'Fakultas Tidak Ditemukan');
                      ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right space-x-2">
                      <button type="button" onclick="openEditModal('study-program', <?= htmlspecialchars(json_encode($sp)) ?>)" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 cursor-pointer">Edit</button>
                      <button type="button" onclick="openDeleteModal('study-program', <?= htmlspecialchars($sp['id']) ?>)" class="text-xs font-bold text-red-650 hover:text-red-800 cursor-pointer">Hapus</button>
                    </td>
                  </tr>
                <?php endforeach; endif; ?>
              </tbody>
            </table>

          <?php elseif ($tab === 'admission-path'): ?>
            <table data-paginate="10" class="min-w-full divide-y divide-slate-100">
              <thead class="bg-slate-50/50">
                <tr>
                  <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">ID</th>
                  <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Nama Jalur</th>
                  <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Status</th>
                  <th class="px-6 py-4 text-right text-xs font-bold text-slate-400 uppercase tracking-wider">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 bg-white">
                <?php if (empty($admission_paths)): ?>
                  <tr><td colspan="4" class="text-center py-12 empty-row-placeholder"><div class="flex flex-col items-center justify-center space-y-3"><div class="text-slate-350 text-4xl">🛤️</div><h3 class="text-xs font-bold text-slate-700">Jalur Masuk Kosong</h3><p class="text-[11px] text-slate-400 max-w-xs mx-auto">Belum ada jalur masuk pendaftaran yang terdaftar.</p></div></td></tr>
                <?php else: foreach ($admission_paths as $ap): ?>
                  <tr class="hover:bg-slate-50/30 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-slate-500">#<?= htmlspecialchars($ap['id']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-800"><?= htmlspecialchars($ap['name']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold <?= $ap['is_active'] ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' ?>">
                        <?= $ap['is_active'] ? 'AKTIF' : 'TIDAK AKTIF' ?>
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right space-x-2">
                      <button type="button" onclick="openEditModal('admission-path', <?= htmlspecialchars(json_encode($ap)) ?>)" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 cursor-pointer">Edit</button>
                      <button type="button" onclick="openDeleteModal('admission-path', <?= htmlspecialchars($ap['id']) ?>)" class="text-xs font-bold text-red-650 hover:text-red-800 cursor-pointer">Hapus</button>
                    </td>
                  </tr>
                <?php endforeach; endif; ?>
              </tbody>
            </table>

          <?php elseif ($tab === 'class'): ?>
            <table data-paginate="10" class="min-w-full divide-y divide-slate-100">
              <thead class="bg-slate-50/50">
                <tr>
                  <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">ID</th>
                  <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Nama Kelas</th>
                  <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Status</th>
                  <th class="px-6 py-4 text-right text-xs font-bold text-slate-400 uppercase tracking-wider">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 bg-white">
                <?php if (empty($classes)): ?>
                  <tr><td colspan="4" class="text-center py-12 empty-row-placeholder"><div class="flex flex-col items-center justify-center space-y-3"><div class="text-slate-350 text-4xl">🏫</div><h3 class="text-xs font-bold text-slate-700">Pilihan Kelas Kosong</h3><p class="text-[11px] text-slate-400 max-w-xs mx-auto">Belum ada pilihan kelas kuliah yang terdaftar.</p></div></td></tr>
                <?php else: foreach ($classes as $c): ?>
                  <tr class="hover:bg-slate-50/30 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-slate-500">#<?= htmlspecialchars($c['id']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-800"><?= htmlspecialchars($c['name']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold <?= $c['is_active'] ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' ?>">
                        <?= $c['is_active'] ? 'AKTIF' : 'TIDAK AKTIF' ?>
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right space-x-2">
                      <button type="button" onclick="openEditModal('class', <?= htmlspecialchars(json_encode($c)) ?>)" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 cursor-pointer">Edit</button>
                      <button type="button" onclick="openDeleteModal('class', <?= htmlspecialchars($c['id']) ?>)" class="text-xs font-bold text-red-650 hover:text-red-800 cursor-pointer">Hapus</button>
                    </td>
                  </tr>
                <?php endforeach; endif; ?>
              </tbody>
            </table>

          <?php elseif ($tab === 'selection-room'): ?>
            <table data-paginate="10" class="min-w-full divide-y divide-slate-100">
              <thead class="bg-slate-50/50">
                <tr>
                  <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">ID</th>
                  <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Nama Ruangan</th>
                  <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Lokasi / Gedung</th>
                  <th class="px-6 py-4 text-right text-xs font-bold text-slate-400 uppercase tracking-wider">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 bg-white">
                <?php if (empty($selection_rooms)): ?>
                  <tr><td colspan="4" class="text-center py-12 empty-row-placeholder"><div class="flex flex-col items-center justify-center space-y-3"><div class="text-slate-350 text-4xl">🚪</div><h3 class="text-xs font-bold text-slate-700">Ruangan Seleksi Kosong</h3><p class="text-[11px] text-slate-400 max-w-xs mx-auto">Belum ada ruangan ujian seleksi yang terdaftar.</p></div></td></tr>
                <?php else: foreach ($selection_rooms as $sr): ?>
                  <tr class="hover:bg-slate-50/30 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-slate-500">#<?= htmlspecialchars($sr['id']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-800"><?= htmlspecialchars($sr['name']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600"><?= htmlspecialchars($sr['location']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right space-x-2">
                      <button type="button" onclick="openEditModal('selection-room', <?= htmlspecialchars(json_encode($sr)) ?>)" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 cursor-pointer">Edit</button>
                      <button type="button" onclick="openDeleteModal('selection-room', <?= htmlspecialchars($sr['id']) ?>)" class="text-xs font-bold text-red-650 hover:text-red-800 cursor-pointer">Hapus</button>
                    </td>
                  </tr>
                <?php endforeach; endif; ?>
              </tbody>
            </table>

          <?php elseif ($tab === 'document-type'): ?>
            <table data-paginate="10" class="min-w-full divide-y divide-slate-100">
              <thead class="bg-slate-50/50">
                <tr>
                  <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">ID</th>
                  <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Nama Dokumen Syarat</th>
                  <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Sifat Berkas</th>
                  <th class="px-6 py-4 text-right text-xs font-bold text-slate-400 uppercase tracking-wider">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 bg-white">
                <?php if (empty($document_types)): ?>
                  <tr><td colspan="4" class="text-center py-12 empty-row-placeholder"><div class="flex flex-col items-center justify-center space-y-3"><div class="text-slate-350 text-4xl">📁</div><h3 class="text-xs font-bold text-slate-700">Jenis Dokumen Kosong</h3><p class="text-[11px] text-slate-400 max-w-xs mx-auto">Belum ada jenis berkas wajib yang terdaftar.</p></div></td></tr>
                <?php else: foreach ($document_types as $dt): ?>
                  <tr class="hover:bg-slate-50/30 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-slate-500">#<?= htmlspecialchars($dt['id']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-800"><?= htmlspecialchars($dt['name']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold <?= $dt['is_required'] ? 'bg-red-100 text-red-800' : 'bg-slate-100 text-slate-650' ?>">
                        <?= $dt['is_required'] ? 'WAJIB' : 'OPSIONAL' ?>
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right space-x-2">
                      <button type="button" onclick="openEditModal('document-type', <?= htmlspecialchars(json_encode($dt)) ?>)" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 cursor-pointer">Edit</button>
                      <button type="button" onclick="openDeleteModal('document-type', <?= htmlspecialchars($dt['id']) ?>)" class="text-xs font-bold text-red-650 hover:text-red-800 cursor-pointer">Hapus</button>
                    </td>
                  </tr>
                <?php endforeach; endif; ?>
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
  <div class="relative bg-white rounded-3xl p-8 max-w-md w-full shadow-2xl border border-slate-100 space-y-6 transform scale-95 opacity-0 transition-all duration-200" id="master-modal-card">
    <div class="flex justify-between items-center border-b border-slate-100 pb-4">
      <h3 class="text-lg font-bold text-slate-900" id="modal-title">Tambah Data</h3>
      <button type="button" onclick="closeMasterModal()" class="text-slate-400 hover:text-slate-600 focus:outline-none text-2xl font-semibold">&times;</button>
    </div>

    <form id="modal-form" method="POST" action="/admin/master/create" class="space-y-4">
      <input type="hidden" name="type" value="<?= htmlspecialchars($tab) ?>">
      <input type="hidden" name="id" id="form-id">

      <!-- Dynamic Field: year (Academic Year) -->
      <div class="space-y-1 modal-field hidden" id="field-year">
        <label for="input-year" class="block text-sm font-semibold text-slate-700">Tahun Akademik</label>
        <input type="text" id="input-year" name="year" placeholder="Format: 2026/2027" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50">
      </div>

      <!-- Dynamic Field: name (General name) -->
      <div class="space-y-1 modal-field hidden" id="field-name">
        <label for="input-name" class="block text-sm font-semibold text-slate-700">Nama</label>
        <input type="text" id="input-name" name="name" placeholder="Masukkan nama" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50">
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
    document.getElementById('input-year').value = '';
    document.getElementById('input-name').value = '';
    document.getElementById('input-start').value = '';
    document.getElementById('input-end').value = '';
    document.getElementById('input-code').value = '';
    document.getElementById('input-location').value = '';
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
    if (item.year) document.getElementById('input-year').value = item.year;
    if (item.name) document.getElementById('input-name').value = item.name;
    if (item.start_date) document.getElementById('input-start').value = item.start_date;
    if (item.end_date) document.getElementById('input-end').value = item.end_date;
    if (item.code) document.getElementById('input-code').value = item.code;
    if (item.faculty_id) document.getElementById('input-faculty').value = item.faculty_id;
    if (item.location) document.getElementById('input-location').value = item.location;
    
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
    if (tab === 'academic-year') {
      document.getElementById('field-year').classList.remove('hidden');
      document.getElementById('field-active').classList.remove('hidden');
    } else if (tab === 'wave') {
      document.getElementById('field-name').classList.remove('hidden');
      document.getElementById('field-dates').classList.remove('hidden');
      document.getElementById('field-active').classList.remove('hidden');
    } else if (tab === 'faculty') {
      document.getElementById('field-code').classList.remove('hidden');
      document.getElementById('field-name').classList.remove('hidden');
    } else if (tab === 'study-program') {
      document.getElementById('field-faculty').classList.remove('hidden');
      document.getElementById('field-code').classList.remove('hidden');
      document.getElementById('field-name').classList.remove('hidden');
    } else if (tab === 'admission-path' || tab === 'class') {
      document.getElementById('field-name').classList.remove('hidden');
      document.getElementById('field-active').classList.remove('hidden');
    } else if (tab === 'selection-room') {
      document.getElementById('field-name').classList.remove('hidden');
      document.getElementById('field-location').classList.remove('hidden');
    } else if (tab === 'document-type') {
      document.getElementById('field-name').classList.remove('hidden');
      document.getElementById('field-required').classList.remove('hidden');
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
