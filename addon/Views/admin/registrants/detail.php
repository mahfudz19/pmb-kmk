<?php
/**
 * @var array $registration
 * @var array $programs
 * @var array|null $address
 * @var array|null $education
 * @var array|null $parent
 * @var array $documents
 * @var array|null $selection
 */
?>

<div class="space-y-6">
  <!-- Top Bar -->
  <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-100 pb-5">
    <div class="space-y-1">
      <div class="flex items-center gap-2">
        <a data-spa href="/admin/registrants" class="text-xs font-bold text-indigo-650 hover:text-indigo-700 flex items-center gap-1 transition-colors">
          <span>← Kembali ke Manajemen Pendaftar</span>
        </a>
      </div>
      <div class="flex flex-wrap items-center gap-2.5">
        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight"><?= htmlspecialchars($registration['full_name']) ?></h1>
        <?php if (!empty($registration['nim'])): ?>
          <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-250">
            NIM: <?= htmlspecialchars($registration['nim']) ?>
          </span>
        <?php endif; ?>
      </div>
      <p class="text-xs text-slate-500">ID Registrasi: #<?= $registration['id'] ?> | Email: <?= htmlspecialchars($registration['email']) ?></p>
    </div>
    
    <!-- Action Button -->
    <div class="flex flex-wrap items-center gap-2">
      <a href="/admin/registrants/pdf/formulir?id=<?= $registration['id'] ?>" class="inline-flex items-center justify-center px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 font-bold rounded-xl border border-slate-200 shadow-sm text-xs transition-colors">
        📄 Cetak Formulir
      </a>
      <a data-spa href="/admin/registrants/edit?id=<?= $registration['id'] ?>" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-sm text-xs transition-colors cursor-pointer">
        ✏️ Koreksi Data
      </a>
    </div>
  </div>

  <?php if (isset($_GET['success'])): ?>
    <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-xl flex gap-3 text-emerald-800 text-xs">
      <span class="text-lg">✅</span>
      <div>
        <p class="font-bold">Berhasil!</p>
        <p class="mt-0.5"><?= htmlspecialchars($_GET['success']) ?></p>
      </div>
    </div>
  <?php endif; ?>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left Column: Personal, Education, Address, Parents (2 cols span) -->
    <div class="lg:col-span-2 space-y-6">
      <!-- Biodata Pribadi -->
      <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-4">
        <h3 class="text-xs font-bold text-slate-450 uppercase tracking-widest border-b border-slate-100 pb-2">Biodata Pribadi</h3>
        <div class="flex flex-col sm:flex-row gap-6">
          <?php if (!empty($registration['photo_path'])): ?>
            <div class="flex-shrink-0">
              <img src="<?= htmlspecialchars($registration['photo_path']) ?>" class="w-24 h-32 object-cover rounded-2xl border border-slate-200 shadow-sm" alt="Foto Peserta">
            </div>
          <?php endif; ?>
          <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
            <?php if (!empty($registration['nim'])): ?>
              <div class="col-span-2">
                <span class="text-slate-400 font-medium block">Nomor Induk Mahasiswa (NIM)</span>
                <strong class="text-emerald-700 font-extrabold text-sm"><?= htmlspecialchars($registration['nim']) ?></strong>
            <?php endif; ?>
            <div>
              <span class="text-slate-400 font-medium block">NIK / Citizen Number</span>
              <strong class="text-slate-800 font-bold"><?= htmlspecialchars($registration['nik']) ?></strong>
            </div>
            <div>
              <span class="text-slate-400 font-medium block">Nomor Induk Siswa Nasional (NISN)</span>
              <strong class="text-slate-800 font-bold"><?= htmlspecialchars($registration['nisn']) ?></strong>
            </div>
            <div>
              <span class="text-slate-400 font-medium block">Tempat, Tanggal Lahir</span>
              <strong class="text-slate-800 font-bold"><?= htmlspecialchars($registration['birth_place']) ?>, <?= date('d-m-Y', strtotime($registration['birth_date'])) ?></strong>
            </div>
            <div>
              <span class="text-slate-400 font-medium block">Jenis Kelamin</span>
              <strong class="text-slate-800 font-bold"><?= htmlspecialchars($registration['gender']) ?></strong>
            </div>
            <div>
              <span class="text-slate-400 font-medium block">Agama</span>
              <strong class="text-slate-800 font-bold"><?= htmlspecialchars($registration['religion']) ?></strong>
            </div>
            <div>
              <span class="text-slate-400 font-medium block">No. Telepon / WhatsApp</span>
              <strong class="text-slate-800 font-bold"><?= htmlspecialchars($registration['phone']) ?></strong>
            </div>
          </div>
        </div>
      </div>

      <!-- Riwayat Pendidikan -->
      <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-4">
        <h3 class="text-xs font-bold text-slate-450 uppercase tracking-widest border-b border-slate-100 pb-2">Riwayat Sekolah Asal</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
          <div>
            <span class="text-slate-400 font-medium block">Nama Sekolah</span>
            <strong class="text-slate-800 font-bold"><?= htmlspecialchars($education['school_name'] ?? '-') ?></strong>
          </div>
          <div>
            <span class="text-slate-400 font-medium block">Jurusan</span>
            <strong class="text-slate-800 font-bold"><?= htmlspecialchars($education['school_major'] ?? '-') ?></strong>
          </div>
          <div>
            <span class="text-slate-400 font-medium block">Tahun Kelulusan</span>
            <strong class="text-slate-800 font-bold"><?= htmlspecialchars($education['graduation_year'] ?? '-') ?></strong>
          </div>
          <div>
            <span class="text-slate-400 font-medium block">Kecamatan Sekolah</span>
            <strong class="text-slate-800 font-bold"><?= htmlspecialchars($education['school_address'] ?? '-') ?></strong>
          </div>
        </div>
      </div>

      <!-- Alamat Lengkap -->
      <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-4">
        <h3 class="text-xs font-bold text-slate-450 uppercase tracking-widest border-b border-slate-100 pb-2">Alamat Tempat Tinggal</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
          <div>
            <span class="text-slate-400 font-medium block">Kecamatan</span>
            <strong class="text-slate-800 font-bold"><?= htmlspecialchars($address['district'] ?? '-') ?></strong>
          </div>
          <div>
            <span class="text-slate-400 font-medium block">Kode Pos</span>
            <strong class="text-slate-800 font-bold"><?= htmlspecialchars($address['postal_code'] ?? '-') ?></strong>
          </div>
        </div>
        <div class="text-xs">
          <span class="text-slate-400 font-medium block">Alamat Detail</span>
          <strong class="text-slate-800 font-bold leading-relaxed"><?= htmlspecialchars($address['address'] ?? '-') ?></strong>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs pt-2 border-t border-slate-100">
          <div>
            <span class="text-slate-400 font-medium block">Penerima KPS</span>
            <strong class="text-slate-800 font-bold"><?= strtoupper($address['kps_receiver'] ?? '-') ?></strong>
          </div>
          <?php if (($address['kps_receiver'] ?? '') === 'ya'): ?>
            <div>
              <span class="text-slate-400 font-medium block">Nomor KPS</span>
              <strong class="text-slate-800 font-bold"><?= htmlspecialchars($address['kps_number'] ?? '-') ?></strong>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Data Orang Tua -->
      <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-6">
        <h3 class="text-xs font-bold text-slate-450 uppercase tracking-widest border-b border-slate-100 pb-2">Data Orang Tua / Wali</h3>
        
        <?php if (!empty($parent['father_name'])): ?>
          <div class="space-y-3">
            <h4 class="text-xs font-bold text-indigo-650 flex items-center gap-1">👨 Data Ayah</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 text-xs bg-slate-50/50 p-4 rounded-2xl border border-slate-100">
              <div>
                <span class="text-slate-400 font-medium block">Nama Lengkap</span>
                <strong class="text-slate-800 font-bold"><?= htmlspecialchars($parent['father_name']) ?></strong>
              </div>
              <div>
                <span class="text-slate-400 font-medium block">NIK</span>
                <strong class="text-slate-800 font-bold"><?= htmlspecialchars($parent['father_nik'] ?? '-') ?></strong>
              </div>
              <div>
                <span class="text-slate-400 font-medium block">Tanggal Lahir</span>
                <strong class="text-slate-800 font-bold"><?= !empty($parent['father_birth_date']) ? date('d-m-Y', strtotime($parent['father_birth_date'])) : '-' ?></strong>
              </div>
              <div>
                <span class="text-slate-400 font-medium block">Pendidikan Terakhir</span>
                <strong class="text-slate-800 font-bold"><?= htmlspecialchars($parent['father_education'] ?? '-') ?></strong>
              </div>
              <div>
                <span class="text-slate-400 font-medium block">Pekerjaan</span>
                <strong class="text-slate-800 font-bold"><?= htmlspecialchars($parent['father_occupation'] ?? '-') ?></strong>
              </div>
              <div>
                <span class="text-slate-400 font-medium block">Penghasilan Bulanan</span>
                <strong class="text-slate-800 font-bold"><?= htmlspecialchars($parent['father_income'] ?? '-') ?></strong>
              </div>
            </div>
          </div>
        <?php endif; ?>

        <?php if (!empty($parent['mother_name'])): ?>
          <div class="space-y-3">
            <h4 class="text-xs font-bold text-indigo-650 flex items-center gap-1">👩 Data Ibu</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 text-xs bg-slate-50/50 p-4 rounded-2xl border border-slate-100">
              <div>
                <span class="text-slate-400 font-medium block">Nama Lengkap</span>
                <strong class="text-slate-800 font-bold"><?= htmlspecialchars($parent['mother_name']) ?></strong>
              </div>
              <div>
                <span class="text-slate-400 font-medium block">NIK</span>
                <strong class="text-slate-800 font-bold"><?= htmlspecialchars($parent['mother_nik'] ?? '-') ?></strong>
              </div>
              <div>
                <span class="text-slate-400 font-medium block">Tanggal Lahir</span>
                <strong class="text-slate-800 font-bold"><?= !empty($parent['mother_birth_date']) ? date('d-m-Y', strtotime($parent['mother_birth_date'])) : '-' ?></strong>
              </div>
              <div>
                <span class="text-slate-400 font-medium block">Pendidikan Terakhir</span>
                <strong class="text-slate-800 font-bold"><?= htmlspecialchars($parent['mother_education'] ?? '-') ?></strong>
              </div>
              <div>
                <span class="text-slate-400 font-medium block">Pekerjaan</span>
                <strong class="text-slate-800 font-bold"><?= htmlspecialchars($parent['mother_occupation'] ?? '-') ?></strong>
              </div>
              <div>
                <span class="text-slate-400 font-medium block">Penghasilan Bulanan</span>
                <strong class="text-slate-800 font-bold"><?= htmlspecialchars($parent['mother_income'] ?? '-') ?></strong>
              </div>
            </div>
          </div>
        <?php endif; ?>

        <?php if (!empty($parent['guardian_name'])): ?>
          <div class="space-y-3">
            <h4 class="text-xs font-bold text-indigo-650 flex items-center gap-1">👤 Data Wali</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 text-xs bg-slate-50/50 p-4 rounded-2xl border border-slate-100">
              <div>
                <span class="text-slate-400 font-medium block">Nama Lengkap</span>
                <strong class="text-slate-800 font-bold"><?= htmlspecialchars($parent['guardian_name']) ?></strong>
              </div>
              <div>
                <span class="text-slate-400 font-medium block">Tanggal Lahir</span>
                <strong class="text-slate-800 font-bold"><?= !empty($parent['guardian_birth_date']) ? date('d-m-Y', strtotime($parent['guardian_birth_date'])) : '-' ?></strong>
              </div>
              <div>
                <span class="text-slate-400 font-medium block">Pendidikan Terakhir</span>
                <strong class="text-slate-800 font-bold"><?= htmlspecialchars($parent['guardian_education'] ?? '-') ?></strong>
              </div>
              <div>
                <span class="text-slate-400 font-medium block">Pekerjaan</span>
                <strong class="text-slate-800 font-bold"><?= htmlspecialchars($parent['guardian_occupation'] ?? '-') ?></strong>
              </div>
              <div>
                <span class="text-slate-400 font-medium block">Penghasilan Bulanan</span>
                <strong class="text-slate-800 font-bold"><?= htmlspecialchars($parent['guardian_income'] ?? '-') ?></strong>
              </div>
            </div>
          </div>
        <?php endif; ?>
    </div>
    </div>

    <!-- Right Column: Program Studi, Berkas, Seleksi (1 col span) -->
    <div class="lg:col-span-1 space-y-6">
      <!-- Pilihan Program Studi -->
      <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-4">
        <h3 class="text-xs font-bold text-slate-450 uppercase tracking-widest border-b border-slate-100 pb-2">Pilihan Program Studi</h3>
        <div class="space-y-3 text-xs">
          <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100">
            <span class="text-[9px] font-bold text-slate-400 block uppercase">Pilihan 1 (Utama)</span>
            <strong class="text-slate-800 font-bold text-sm mt-0.5 block"><?= htmlspecialchars($programs['program1_name'] ?? '-') ?></strong>
          </div>
          <?php if ($programs && $programs['program2_name']): ?>
            <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100">
              <span class="text-[9px] font-bold text-slate-400 block uppercase">Pilihan 2 (Cadangan)</span>
              <strong class="text-slate-800 font-bold text-sm mt-0.5 block"><?= htmlspecialchars($programs['program2_name']) ?></strong>
            </div>
          <?php endif; ?>
          <?php if ($programs && !empty($programs['program3_name'])): ?>
            <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100">
              <span class="text-[9px] font-bold text-slate-400 block uppercase">Pilihan 3 (Cadangan)</span>
              <strong class="text-slate-800 font-bold text-sm mt-0.5 block"><?= htmlspecialchars($programs['program3_name']) ?></strong>
            </div>
          <?php endif; ?>
        </div>
      </div>


      <!-- Blok Informasi per Program Studi -->
      <?php if (!empty($wave_study_programs)): 
        foreach ($wave_study_programs as $wsp):
          $stages = json_decode($wsp['exam_stages'] ?? '[]', true) ?: [];
      ?>
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-5">
          <div class="border-b border-slate-100 pb-3 flex justify-between items-center">
            <h3 class="text-xs font-bold text-indigo-950 uppercase tracking-wider">
              Prodi: <?= htmlspecialchars($wsp['study_program_name'] ?: 'Umum') ?>
            </h3>
            <a href="/admin/registrants/pdf/kartu-ujian?id=<?= $registration['id'] ?>&study_program_id=<?= $wsp['study_program_id'] ?>" download class="inline-flex items-center gap-1 px-2.5 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg text-[10px] font-bold transition-colors">
              🎟️ Kartu Ujian
            </a>
          </div>

          <!-- 1. Dokumen Pendaftaran Prodi -->
          <div class="space-y-3">
            <h4 class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Dokumen Prodi</h4>
            <div class="space-y-2 text-xs">
              <?php 
              $prodiDocs = array_filter($documents, fn($d) => ($d['study_program_id'] == $wsp['study_program_id']));
              if (empty($prodiDocs)):
              ?>
                <p class="text-slate-400 font-medium italic text-[11px]">Belum ada dokumen yang diunggah.</p>
              <?php else: foreach ($prodiDocs as $doc): ?>
                <div class="flex justify-between items-center py-2 border-b border-slate-50 last:border-0">
                  <div class="truncate pr-2">
                    <span class="font-bold text-slate-700 block truncate"><?= htmlspecialchars($doc['document_name']) ?></span>
                    <span class="inline-flex px-1.5 py-0.5 rounded-full text-[8px] font-bold mt-0.5 <?= $doc['status'] === 'Approved' ? 'bg-emerald-100 text-emerald-800' : ($doc['status'] === 'Rejected' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800') ?>">
                      <?= $doc['status'] ?>
                    </span>
                  </div>
                  <a href="/documents/view?id=<?= $doc['id'] ?>" target="_blank" class="inline-flex items-center justify-center p-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg transition-colors cursor-pointer text-[10px] font-bold">
                    👁️ Lihat
                  </a>
                </div>
              <?php endforeach; endif; ?>
            </div>
          </div>

          <!-- 2. Hasil Seleksi Prodi -->
          <div class="space-y-3 pt-3 border-t border-slate-100">
            <h4 class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Hasil Seleksi</h4>
            <div class="space-y-2 text-xs">
              <?php if (!$selection): ?>
                <p class="text-slate-400 font-medium italic text-[11px]">Belum ada penilaian seleksi masuk.</p>
              <?php else: 
                $isPassedThis = ($selection['passed_program_id'] == $wsp['study_program_id']);
                $prodiStatus = 'Pending';
                if ($selection['status'] === 'Tidak Lulus') {
                    $prodiStatus = 'Tidak Lulus';
                } elseif ($selection['status'] === 'Lulus') {
                    $prodiStatus = $isPassedThis ? 'Lulus' : 'Tidak Diterima';
                } elseif ($selection['status'] === 'Cadangan') {
                    $prodiStatus = $isPassedThis ? 'Cadangan' : 'Tidak Diterima';
                }
              ?>
                <div class="flex justify-between items-center">
                  <span class="text-slate-400 font-medium">Status Akhir</span>
                  <span class="inline-flex px-2 py-0.5 rounded-full text-[9px] font-bold <?= in_array($prodiStatus, ['Lulus', 'Cadangan']) ? 'bg-emerald-100 text-emerald-800' : ($prodiStatus === 'Tidak Diterima' || $prodiStatus === 'Tidak Lulus' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800') ?>">
                    <?= $prodiStatus ?>
                  </span>
                </div>
                <?php if ($isPassedThis): ?>
                  <div class="flex justify-between items-center">
                    <span class="text-slate-400 font-medium">Nilai Ujian CBT</span>
                    <strong class="text-slate-800 font-bold"><?= number_format($selection['test_score'], 2) ?></strong>
                  </div>
                  <div class="flex justify-between items-center">
                    <span class="text-slate-400 font-medium">Nilai Wawancara</span>
                    <strong class="text-slate-800 font-bold"><?= number_format($selection['interview_score'], 2) ?></strong>
                  </div>
                  <?php if (!empty($selection['notes'])): ?>
                    <div class="pt-1.5 text-slate-500 italic text-[11px] border-t border-slate-50 mt-1">
                      "<?= htmlspecialchars($selection['notes']) ?>"
                    </div>
                  <?php endif; ?>
                <?php endif; ?>
              <?php endif; ?>
            </div>
          </div>

          <!-- 3. Tahapan Seleksi Gelombang -->
          <div class="space-y-3 pt-3 border-t border-slate-100">
            <h4 class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Tahapan Ujian</h4>
            <?php if (empty($stages)): ?>
              <p class="text-slate-400 font-medium italic text-[11px]">Belum ada tahapan seleksi yang dikonfigurasi.</p>
            <?php else: ?>
              <div class="space-y-3">
                <?php foreach ($stages as $stg): 
                  $res = array_values(array_filter($exam_results, fn($r) => 
                      $r['stage_index'] == $stg['stage_number'] && 
                      ($r['study_program_id'] == $wsp['study_program_id'] || $r['study_program_id'] === null)
                  ))[0] ?? null;
                  $status = $res ? $res['status'] : 'Pending';
                ?>
                  <div class="p-3 bg-slate-50 rounded-2xl border border-slate-150 space-y-2.5 text-xs">
                    <div class="flex justify-between items-center">
                      <span class="font-bold text-slate-800">Tahap <?= $stg['stage_number'] ?></span>
                      <span class="inline-flex px-2 py-0.5 rounded-full text-[9px] font-extrabold <?= $status === 'Lulus' ? 'bg-emerald-100 text-emerald-800' : ($status === 'Tidak Lulus' ? 'bg-rose-100 text-rose-800' : 'bg-slate-200 text-slate-600') ?>">
                        <?= $status === 'Lulus' ? 'LOLOS' : ($status === 'Tidak Lulus' ? 'GAGAL' : 'BELUM UJIAN') ?>
                      </span>
                    </div>
                    <div class="text-[11px] text-slate-505 space-y-0.5">
                      <div><strong>Waktu:</strong> <?= htmlspecialchars($stg['date']) ?> (<?= htmlspecialchars($stg['time']) ?>)</div>
                      <div><strong>Tempat:</strong> <?= htmlspecialchars($stg['place']) ?> (<?= htmlspecialchars($stg['type']) ?>)</div>
                    </div>

                    <!-- Update Status Form -->
                    <form action="/admin/registrants/exam-stage/save" method="POST" class="flex gap-2 pt-1.5 border-t border-slate-200/50">
                      <input type="hidden" name="registration_id" value="<?= htmlspecialchars($registration['id']) ?>">
                      <input type="hidden" name="stage_number" value="<?= htmlspecialchars($stg['stage_number']) ?>">
                      <input type="hidden" name="study_program_id" value="<?= htmlspecialchars($wsp['study_program_id']) ?>">
                      <select name="status" class="flex-1 px-2.5 py-1 border border-slate-200 rounded-lg text-[10px] bg-white font-semibold text-slate-700 focus:outline-none">
                        <option value="Lulus" <?= $status === 'Lulus' ? 'selected' : '' ?>>Lolos</option>
                        <option value="Tidak Lulus" <?= $status === 'Tidak Lulus' ? 'selected' : '' ?>>Gagal</option>
                        <option value="Pending" <?= $status === 'Pending' ? 'selected' : '' ?>>Pending</option>
                      </select>
                      <button type="submit" class="px-3 py-1 bg-indigo-600 hover:bg-indigo-700 text-white text-[10px] font-bold rounded-lg transition-colors cursor-pointer shadow-sm">Update</button>
                    </form>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; endif; ?>
    </div>
  </div>
</div>
