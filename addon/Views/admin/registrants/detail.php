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
      <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight"><?= htmlspecialchars($registration['full_name']) ?></h1>
      <p class="text-xs text-slate-500">ID Registrasi: #<?= $registration['id'] ?> | Email: <?= htmlspecialchars($registration['email']) ?></p>
    </div>
    
    <!-- Action Button -->
    <div class="flex flex-wrap items-center gap-2">
      <a href="/admin/registrants/pdf/formulir?id=<?= $registration['id'] ?>" class="inline-flex items-center justify-center px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 font-bold rounded-xl border border-slate-200 shadow-sm text-xs transition-colors">
        📄 Cetak Formulir
      </a>
      <a href="/admin/registrants/pdf/kartu-ujian?id=<?= $registration['id'] ?>" class="inline-flex items-center justify-center px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 font-bold rounded-xl border border-slate-200 shadow-sm text-xs transition-colors">
        🎟️ Cetak Kartu Ujian
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
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
          <div>
            <span class="text-slate-400 font-medium block">Nomor Induk Kependudukan (NIK)</span>
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

      <!-- Riwayat Pendidikan -->
      <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-4">
        <h3 class="text-xs font-bold text-slate-450 uppercase tracking-widest border-b border-slate-100 pb-2">Riwayat Sekolah Asal</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
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
        </div>
      </div>

      <!-- Alamat Lengkap -->
      <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-4">
        <h3 class="text-xs font-bold text-slate-450 uppercase tracking-widest border-b border-slate-100 pb-2">Alamat Tempat Tinggal</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
          <div>
            <span class="text-slate-400 font-medium block">Provinsi</span>
            <strong class="text-slate-800 font-bold"><?= htmlspecialchars($address['province'] ?? '-') ?></strong>
          </div>
          <div>
            <span class="text-slate-400 font-medium block">Kota / Kabupaten</span>
            <strong class="text-slate-800 font-bold"><?= htmlspecialchars($address['city'] ?? '-') ?></strong>
          </div>
          <div>
            <span class="text-slate-400 font-medium block">Kecamatan</span>
            <strong class="text-slate-800 font-bold"><?= htmlspecialchars($address['district'] ?? '-') ?></strong>
          </div>
        </div>
        <div class="text-xs">
          <span class="text-slate-400 font-medium block">Alamat Detail</span>
          <strong class="text-slate-800 font-bold leading-relaxed"><?= htmlspecialchars($address['address'] ?? '-') ?></strong>
        </div>
      </div>

      <!-- Data Orang Tua -->
      <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-4">
        <h3 class="text-xs font-bold text-slate-450 uppercase tracking-widest border-b border-slate-100 pb-2">Data Orang Tua / Wali</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
          <div>
            <span class="text-slate-400 font-medium block">Nama Ayah Kandung</span>
            <strong class="text-slate-800 font-bold"><?= htmlspecialchars($parent['father_name'] ?? '-') ?></strong>
          </div>
          <div>
            <span class="text-slate-400 font-medium block">Pekerjaan Ayah</span>
            <strong class="text-slate-800 font-bold"><?= htmlspecialchars($parent['father_occupation'] ?? '-') ?></strong>
          </div>
          <div>
            <span class="text-slate-400 font-medium block">Nama Ibu Kandung</span>
            <strong class="text-slate-800 font-bold"><?= htmlspecialchars($parent['mother_name'] ?? '-') ?></strong>
          </div>
          <div>
            <span class="text-slate-400 font-medium block">Pekerjaan Ibu</span>
            <strong class="text-slate-800 font-bold"><?= htmlspecialchars($parent['mother_occupation'] ?? '-') ?></strong>
          </div>
        </div>
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
        </div>
      </div>

      <!-- Dokumen Pendukung -->
      <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-4">
        <h3 class="text-xs font-bold text-slate-450 uppercase tracking-widest border-b border-slate-100 pb-2">Dokumen Pendaftaran</h3>
        <div class="space-y-2 text-xs">
          <?php if (empty($documents)): ?>
            <p class="text-slate-400 font-medium italic">Belum ada dokumen yang diunggah.</p>
          <?php else: ?>
            <?php foreach ($documents as $doc): ?>
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
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>

      <!-- Hasil Seleksi -->
      <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-4">
        <h3 class="text-xs font-bold text-slate-450 uppercase tracking-widest border-b border-slate-100 pb-2">Hasil Ujian & Seleksi</h3>
        <div class="space-y-3 text-xs">
          <?php if (!$selection): ?>
            <p class="text-slate-400 font-medium italic">Belum ada penilaian seleksi masuk.</p>
          <?php else: ?>
            <div class="flex justify-between">
              <span class="text-slate-400 font-medium">Nilai Ujian CBT</span>
              <strong class="text-slate-800 font-bold"><?= number_format($selection['test_score'], 2) ?></strong>
            </div>
            <div class="flex justify-between">
              <span class="text-slate-400 font-medium">Nilai Wawancara</span>
              <strong class="text-slate-800 font-bold"><?= number_format($selection['interview_score'], 2) ?></strong>
            </div>
            <div class="flex justify-between">
              <span class="text-slate-400 font-medium">Status Akhir</span>
              <span class="inline-flex px-2 py-0.5 rounded-full text-[9px] font-bold <?= $selection['status'] === 'Lulus' ? 'bg-emerald-100 text-emerald-800' : ($selection['status'] === 'Tidak Lulus' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800') ?>">
                <?= $selection['status'] ?>
              </span>
            </div>
            <?php if (!empty($selection['notes'])): ?>
              <div class="pt-2 border-t border-slate-100 text-slate-500 italic">
                "<?= htmlspecialchars($selection['notes']) ?>"
              </div>
            <?php endif; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>
