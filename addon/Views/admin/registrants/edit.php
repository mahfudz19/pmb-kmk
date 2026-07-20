<?php
/**
 * @var array $registration
 * @var array $programs
 * @var array $address
 * @var array $education
 * @var array $parent
 * @var array $study_programs
 * @var array $academic_years
 * @var array $waves
 * @var array $admission_paths
 * @var array $classes
 */
$jsonData = json_decode(file_get_contents(MAZU_ENV_PATH . 'data.json'), true);
$agamaList = $jsonData['agama'][0] ?? [];
$negaraList = $jsonData['kewarganegaraan'][0] ?? [];
$tinggalList = $jsonData['jenis_tinggal'][0] ?? [];
$transportList = $jsonData['alat_transportasi'][0] ?? [];
$pendidikanList = $jsonData['jenjang_pendidikan'][0] ?? [];
usort($pendidikanList, function($a, $b) {
    return ((int)($a['id_jenj_didik'] ?? 0)) <=> ((int)($b['id_jenj_didik'] ?? 0));
});

$penghasilanList = array_values(array_filter($jsonData['penghasilan'][0] ?? [], function($item) {
    return !empty($item['nm_penghasilan']);
}));
usort($penghasilanList, function($a, $b) {
    return ((int)($a['id_penghasilan'] ?? 0)) <=> ((int)($b['id_penghasilan'] ?? 0));
});

$pekerjaanList = $jsonData['pekerjaan'][0] ?? [];
usort($pekerjaanList, function($a, $b) {
    $nameA = $a['nm_pekerjaan'] ?? '';
    $nameB = $b['nm_pekerjaan'] ?? '';
    if ($nameA === 'Tidak bekerja') return -1;
    if ($nameB === 'Tidak bekerja') return 1;
    if ($nameA === 'Lainnya' || $nameA === 'Sudah Meninggal') return 1;
    if ($nameB === 'Lainnya' || $nameB === 'Sudah Meninggal') return -1;
    return strcasecmp($nameA, $nameB);
});

$wilayahList = $jsonData['wilayah'][0] ?? [];
?>

<div class="space-y-6">
  <!-- Top Bar -->
  <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-100 pb-5">
    <div class="space-y-1">
      <div class="flex items-center gap-2">
        <a data-spa href="/admin/registrants/detail?id=<?= $registration['id'] ?>" class="text-xs font-bold text-indigo-650 hover:text-indigo-700 flex items-center gap-1 transition-colors">
          <span>← Kembali ke Detail Pendaftar</span>
        </a>
      </div>
      <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Koreksi Data Pendaftar</h1>
      <p class="text-xs text-slate-500">Edit data calon mahasiswa secara langsung untuk memperbaiki kesalahan input pendaftaran.</p>
    </div>
  </div>

  <?php if (isset($_GET['error'])): ?>
    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-xl flex gap-3 text-red-800 text-xs">
      <span class="text-lg">⚠️</span>
      <div>
        <p class="font-bold">Gagal!</p>
        <p class="mt-0.5"><?= htmlspecialchars($_GET['error']) ?></p>
      </div>
    </div>
  <?php endif; ?>

  <form action="/admin/registrants/update" method="POST" class="space-y-6">
    <input type="hidden" name="id" value="<?= $registration['id'] ?>">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Left Forms (2 col span) -->
      <div class="lg:col-span-2 space-y-6">
        <!-- Biodata Pribadi -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-4">
          <h3 class="text-xs font-bold text-slate-455 uppercase tracking-widest border-b border-slate-100 pb-2">1. Biodata Pribadi</h3>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
            <div>
              <label for="full_name" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Nama Lengkap</label>
              <input type="text" name="full_name" id="full_name" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 font-semibold" value="<?= htmlspecialchars($registration['full_name'] ?? '') ?>" required>
            </div>

            <div>
              <label for="phone" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">No. Telepon / WhatsApp</label>
              <input type="text" name="phone" id="phone" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 font-semibold" value="<?= htmlspecialchars($registration['phone'] ?? '') ?>" required>
            </div>

            <div>
              <label for="nik" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">NIK (Nomor Induk Kependudukan)</label>
              <input type="text" name="nik" id="nik" minlength="16" maxlength="16" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 font-semibold" value="<?= htmlspecialchars($registration['nik'] ?? '') ?>" required>
            </div>

            <div>
              <label for="nisn" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">NISN (Nomor Induk Siswa Nasional)</label>
              <input type="text" name="nisn" id="nisn" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 font-semibold" value="<?= htmlspecialchars($registration['nisn'] ?? '') ?>" required>
            </div>

            <div>
              <label for="birth_place" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Tempat Lahir</label>
              <input type="text" name="birth_place" id="birth_place" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 font-semibold" value="<?= htmlspecialchars($registration['birth_place'] ?? '') ?>" required>
            </div>

            <div>
              <label for="birth_date" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Tanggal Lahir</label>
              <input type="date" name="birth_date" id="birth_date" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 font-semibold" value="<?= !empty($registration['birth_date']) ? date('Y-m-d', strtotime(str_replace('/', '-', $registration['birth_date']))) : '' ?>" required>
            </div>

            <div>
              <label for="gender" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Jenis Kelamin</label>
              <select name="gender" id="gender" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 font-semibold text-slate-700" required>
                <option value="Laki-laki" <?= ($registration['gender'] ?? '') === 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                <option value="Perempuan" <?= ($registration['gender'] ?? '') === 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
              </select>
            </div>

            <div>
              <label for="religion" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Agama</label>
              <select name="religion" id="religion" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 font-semibold text-slate-700" required>
                <option value="" disabled <?= empty($registration['religion']) ? 'selected' : '' ?>>Pilih Agama</option>
                <?php foreach ($agamaList as $ag): ?>
                  <option value="<?= htmlspecialchars($ag['nm_agama']) ?>" <?= ($registration['religion'] ?? '') === $ag['nm_agama'] ? 'selected' : '' ?>><?= htmlspecialchars($ag['nm_agama']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div>
              <label for="mother_name" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Nama Ibu Kandung</label>
              <input type="text" name="mother_name" id="mother_name" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 font-semibold" value="<?= htmlspecialchars($registration['mother_name'] ?? '') ?>" required>
            </div>

            <div>
              <label for="info_source" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Dapat Info Kampus Dari Mana?</label>
              <select name="info_source" id="info_source" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 font-semibold text-slate-700" required>
                <option value="" disabled <?= empty($registration['info_source']) ? 'selected' : '' ?>>Pilih Info Kampus</option>
                <option value="media sosial" <?= ($registration['info_source'] ?? '') === 'media sosial' ? 'selected' : '' ?>>Media Sosial</option>
                <option value="sosialisasi" <?= ($registration['info_source'] ?? '') === 'sosialisasi' ? 'selected' : '' ?>>Sosialisasi</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Alamat -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-4">
          <h3 class="text-xs font-bold text-slate-455 uppercase tracking-widest border-b border-slate-100 pb-2">2. Alamat Rumah & Kontak</h3>
          
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 text-xs">
            <div>
              <label for="citizenship" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Kewarganegaraan</label>
              <select name="citizenship" id="citizenship" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 font-semibold text-slate-700" required>
                <option value="" disabled <?= empty($address['citizenship']) ? 'selected' : '' ?>>Pilih Kewarganegaraan</option>
                <?php foreach ($negaraList as $neg): ?>
                  <option value="<?= htmlspecialchars($neg['nm_negara']) ?>" <?= ($address['citizenship'] ?? '') === $neg['nm_negara'] ? 'selected' : '' ?>><?= htmlspecialchars($neg['nm_negara']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div>
              <label for="npwp" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">NPWP</label>
              <input type="text" name="npwp" id="npwp" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 font-semibold" value="<?= htmlspecialchars($address['npwp'] ?? '') ?>">
            </div>

            <div>
              <label for="telephone" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Telepon Rumah</label>
              <input type="text" name="telephone" id="telephone" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 font-semibold" value="<?= htmlspecialchars($address['telephone'] ?? '') ?>">
            </div>

            <div>
              <label for="street" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Nama Jalan</label>
              <input type="text" name="street" id="street" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 font-semibold" value="<?= htmlspecialchars($address['street'] ?? '') ?>">
            </div>

            <div>
              <label for="dusun" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Dusun</label>
              <input type="text" name="dusun" id="dusun" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 font-semibold" value="<?= htmlspecialchars($address['dusun'] ?? '') ?>">
            </div>

            <div class="grid grid-cols-2 gap-2">
              <div>
                <label for="rt" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">RT</label>
                <input type="text" name="rt" id="rt" class="block w-full px-3 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 font-semibold text-center" value="<?= htmlspecialchars($address['rt'] ?? '') ?>">
              </div>
              <div>
                <label for="rw" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">RW</label>
                <input type="text" name="rw" id="rw" class="block w-full px-3 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 font-semibold text-center" value="<?= htmlspecialchars($address['rw'] ?? '') ?>">
              </div>
            </div>

            <div>
              <label for="district" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Kecamatan</label>
              <div class="space-y-1.5">
                <input type="text" id="district_search" oninput="filterEditDistricts(this.value)" class="appearance-none block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-xs bg-slate-50 font-medium" placeholder="🔍 Cari nama kecamatan di sini...">
                <select name="district" id="district" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 font-semibold text-slate-700" required>
                  <option value="" disabled <?= empty($address['district']) ? 'selected' : '' ?>>Pilih Kecamatan</option>
                  <?php foreach ($wilayahList as $wil): ?>
                    <option value="<?= htmlspecialchars($wil['kecamatan']) ?>" <?= ($address['district'] ?? '') === $wil['kecamatan'] ? 'selected' : '' ?>><?= htmlspecialchars($wil['kecamatan']) ?> (<?= htmlspecialchars($wil['kabupaten']) ?>)</option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <div>
              <label for="subdistrict" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Kelurahan / Desa</label>
              <input type="text" name="subdistrict" id="subdistrict" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 font-semibold" value="<?= htmlspecialchars($address['subdistrict'] ?? '') ?>" required>
            </div>

            <div>
              <label for="postal_code" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Kode Pos</label>
              <input type="text" name="postal_code" id="postal_code" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 font-semibold" value="<?= htmlspecialchars($address['postal_code'] ?? '') ?>" required>
            </div>

            <div>
              <label for="kps_receiver" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Penerima KPS?</label>
              <select name="kps_receiver" id="kps_receiver" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 font-semibold text-slate-700" required>
                <option value="tidak" <?= ($address['kps_receiver'] ?? '') === 'tidak' ? 'selected' : '' ?>>Tidak</option>
                <option value="ya" <?= ($address['kps_receiver'] ?? '') === 'ya' ? 'selected' : '' ?>>Ya</option>
              </select>
            </div>

            <div>
              <label for="transportation" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Alat Transportasi</label>
              <select name="transportation" id="transportation" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 font-semibold text-slate-700">
                <option value="">Pilih Transportasi</option>
                <?php foreach ($transportList as $tr): ?>
                  <option value="<?= htmlspecialchars($tr['nm_alat_transport']) ?>" <?= ($address['transportation'] ?? '') === $tr['nm_alat_transport'] ? 'selected' : '' ?>><?= htmlspecialchars($tr['nm_alat_transport']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div>
              <label for="living_type" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Jenis Tinggal</label>
              <select name="living_type" id="living_type" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 font-semibold text-slate-700">
                <option value="">Pilih Jenis Tinggal</option>
                <?php foreach ($tinggalList as $tl): ?>
                  <option value="<?= htmlspecialchars($tl['nm_jenis_tinggal']) ?>" <?= ($address['living_type'] ?? '') === $tl['nm_jenis_tinggal'] ? 'selected' : '' ?>><?= htmlspecialchars($tl['nm_jenis_tinggal']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div>
            <label for="address" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Alamat Detail (Jalan, RT/RW, Dusun)</label>
            <textarea name="address" id="address" rows="2" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-xs bg-slate-50 font-semibold" required><?= htmlspecialchars($address['address'] ?? '') ?></textarea>
          </div>
        </div>

        <!-- Asal Sekolah -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-4">
          <h3 class="text-xs font-bold text-slate-455 uppercase tracking-widest border-b border-slate-100 pb-2">3. Riwayat Pendidikan Asal</h3>
          
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 text-xs">
            <div>
              <label for="school_name" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Nama Sekolah</label>
              <input type="text" name="school_name" id="school_name" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 font-semibold" value="<?= htmlspecialchars($education['school_name'] ?? '') ?>" required>
            </div>

            <div>
              <label for="major" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Jurusan</label>
              <input type="text" name="major" id="major" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 font-semibold" value="<?= htmlspecialchars($education['school_major'] ?? '') ?>" required>
            </div>

            <div>
              <label for="graduation_year" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Tahun Lulus</label>
              <input type="number" name="graduation_year" id="graduation_year" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 font-semibold" value="<?= htmlspecialchars($education['graduation_year'] ?? '') ?>" required>
            </div>

            <div>
              <label for="diploma_number" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Nomor Ijazah / SKL</label>
              <input type="text" name="diploma_number" id="diploma_number" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 font-semibold" value="<?= htmlspecialchars($education['diploma_number'] ?? '') ?>" required>
            </div>

            <div>
              <label for="average_score" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Rata-Rata Rapor/Ijazah</label>
              <input type="number" step="0.01" min="0" max="100" name="average_score" id="average_score" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 font-semibold" value="<?= htmlspecialchars($education['average_score'] ?? '') ?>" required>
            </div>
          </div>
        </div>

        <!-- Data Orang Tua & Wali -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-6">
          <h3 class="text-xs font-bold text-slate-455 uppercase tracking-widest border-b border-slate-100 pb-2">4. Data Orang Tua / Wali</h3>
          
          <!-- Data Ayah -->
          <div class="space-y-3 pt-2">
            <h4 class="text-xs font-bold text-indigo-650 flex items-center gap-1">👨 Data Ayah</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 bg-slate-50/50 p-4 rounded-2xl border border-slate-100 text-xs">
              <div>
                <label for="father_name" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Nama Ayah</label>
                <input type="text" name="father_name" id="father_name" class="block w-full px-3 py-2 border border-slate-200 rounded-xl bg-white font-medium" value="<?= htmlspecialchars($parent['father_name'] ?? '') ?>">
              </div>
              <div>
                <label for="father_nik" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">NIK Ayah</label>
                <input type="text" name="father_nik" id="father_nik" minlength="16" maxlength="16" class="block w-full px-3 py-2 border border-slate-200 rounded-xl bg-white font-medium" value="<?= htmlspecialchars($parent['father_nik'] ?? '') ?>">
              </div>
              <div>
                <label for="father_birth_date" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Tanggal Lahir Ayah</label>
                <input type="date" name="father_birth_date" id="father_birth_date" class="block w-full px-3 py-2 border border-slate-200 rounded-xl bg-white font-medium" value="<?= !empty($parent['father_birth_date']) ? date('Y-m-d', strtotime(str_replace('/', '-', $parent['father_birth_date']))) : '' ?>">
              </div>
              <div>
                <label for="father_education" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Pendidikan Ayah</label>
                <select name="father_education" id="father_education" class="block w-full px-3 py-2 border border-slate-200 rounded-xl bg-white font-medium text-slate-700">
                  <option value="">Pilih Pendidikan</option>
                  <?php foreach ($pendidikanList as $pl): ?>
                    <option value="<?= htmlspecialchars($pl['nm_jenj_didik']) ?>" <?= ($parent['father_education'] ?? '') === $pl['nm_jenj_didik'] ? 'selected' : '' ?>><?= htmlspecialchars($pl['nm_jenj_didik']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div>
                <label for="father_occupation" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Pekerjaan Ayah</label>
                <select name="father_occupation" id="father_occupation" class="block w-full px-3 py-2 border border-slate-200 rounded-xl bg-white font-medium text-slate-700">
                  <option value="">Pilih Pekerjaan</option>
                  <?php foreach ($pekerjaanList as $pej): ?>
                    <option value="<?= htmlspecialchars($pej['nm_pekerjaan']) ?>" <?= ($parent['father_occupation'] ?? '') === $pej['nm_pekerjaan'] ? 'selected' : '' ?>><?= htmlspecialchars($pej['nm_pekerjaan']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div>
                <label for="father_income" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Penghasilan Ayah</label>
                <select name="father_income" id="father_income" class="block w-full px-3 py-2 border border-slate-200 rounded-xl bg-white font-medium text-slate-700">
                  <option value="">Pilih Penghasilan</option>
                  <?php foreach ($penghasilanList as $pen): ?>
                    <option value="<?= htmlspecialchars($pen['nm_penghasilan']) ?>" <?= ($parent['father_income'] ?? '') === $pen['nm_penghasilan'] ? 'selected' : '' ?>><?= htmlspecialchars($pen['nm_penghasilan']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
          </div>

          <!-- Data Ibu -->
          <div class="space-y-3">
            <h4 class="text-xs font-bold text-indigo-650 flex items-center gap-1">👩 Data Ibu</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 bg-slate-50/50 p-4 rounded-2xl border border-slate-100 text-xs">
              <div>
                <label for="mother_name" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Nama Ibu</label>
                <input type="text" name="mother_name" id="mother_name" class="block w-full px-3 py-2 border border-slate-200 rounded-xl bg-white font-medium" value="<?= htmlspecialchars($parent['mother_name'] ?? '') ?>">
              </div>
              <div>
                <label for="mother_nik" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">NIK Ibu</label>
                <input type="text" name="mother_nik" id="mother_nik" minlength="16" maxlength="16" class="block w-full px-3 py-2 border border-slate-200 rounded-xl bg-white font-medium" value="<?= htmlspecialchars($parent['mother_nik'] ?? '') ?>">
              </div>
              <div>
                <label for="mother_birth_date" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Tanggal Lahir Ibu</label>
                <input type="date" name="mother_birth_date" id="mother_birth_date" class="block w-full px-3 py-2 border border-slate-200 rounded-xl bg-white font-medium" value="<?= !empty($parent['mother_birth_date']) ? date('Y-m-d', strtotime(str_replace('/', '-', $parent['mother_birth_date']))) : '' ?>">
              </div>
              <div>
                <label for="mother_education" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Pendidikan Ibu</label>
                <select name="mother_education" id="mother_education" class="block w-full px-3 py-2 border border-slate-200 rounded-xl bg-white font-medium text-slate-700">
                  <option value="">Pilih Pendidikan</option>
                  <?php foreach ($pendidikanList as $pl): ?>
                    <option value="<?= htmlspecialchars($pl['nm_jenj_didik']) ?>" <?= ($parent['mother_education'] ?? '') === $pl['nm_jenj_didik'] ? 'selected' : '' ?>><?= htmlspecialchars($pl['nm_jenj_didik']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div>
                <label for="mother_occupation" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Pekerjaan Ibu</label>
                <select name="mother_occupation" id="mother_occupation" class="block w-full px-3 py-2 border border-slate-200 rounded-xl bg-white font-medium text-slate-700">
                  <option value="">Pilih Pekerjaan</option>
                  <?php foreach ($pekerjaanList as $pej): ?>
                    <option value="<?= htmlspecialchars($pej['nm_pekerjaan']) ?>" <?= ($parent['mother_occupation'] ?? '') === $pej['nm_pekerjaan'] ? 'selected' : '' ?>><?= htmlspecialchars($pej['nm_pekerjaan']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div>
                <label for="mother_income" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Penghasilan Ibu</label>
                <select name="mother_income" id="mother_income" class="block w-full px-3 py-2 border border-slate-200 rounded-xl bg-white font-medium text-slate-700">
                  <option value="">Pilih Penghasilan</option>
                  <?php foreach ($penghasilanList as $pen): ?>
                    <option value="<?= htmlspecialchars($pen['nm_penghasilan']) ?>" <?= ($parent['mother_income'] ?? '') === $pen['nm_penghasilan'] ? 'selected' : '' ?>><?= htmlspecialchars($pen['nm_penghasilan']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
          </div>

          <!-- Data Wali -->
          <div class="space-y-3">
            <h4 class="text-xs font-bold text-indigo-650 flex items-center gap-1">👤 Data Wali (Opsional)</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 bg-slate-50/50 p-4 rounded-2xl border border-slate-100 text-xs">
              <div>
                <label for="guardian_name" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Nama Wali</label>
                <input type="text" name="guardian_name" id="guardian_name" class="block w-full px-3 py-2 border border-slate-200 rounded-xl bg-white font-medium" value="<?= htmlspecialchars($parent['guardian_name'] ?? '') ?>">
              </div>
              <div>
                <label for="guardian_birth_date" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Tanggal Lahir Wali</label>
                <input type="date" name="guardian_birth_date" id="guardian_birth_date" class="block w-full px-3 py-2 border border-slate-200 rounded-xl bg-white font-medium" value="<?= !empty($parent['guardian_birth_date']) ? date('Y-m-d', strtotime(str_replace('/', '-', $parent['guardian_birth_date']))) : '' ?>">
              </div>
              <div>
                <label for="guardian_education" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Pendidikan Wali</label>
                <select name="guardian_education" id="guardian_education" class="block w-full px-3 py-2 border border-slate-200 rounded-xl bg-white font-medium text-slate-700">
                  <option value="">Pilih Pendidikan</option>
                  <?php foreach ($pendidikanList as $pl): ?>
                    <option value="<?= htmlspecialchars($pl['nm_jenj_didik']) ?>" <?= ($parent['guardian_education'] ?? '') === $pl['nm_jenj_didik'] ? 'selected' : '' ?>><?= htmlspecialchars($pl['nm_jenj_didik']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div>
                <label for="guardian_occupation" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Pekerjaan Wali</label>
                <select name="guardian_occupation" id="guardian_occupation" class="block w-full px-3 py-2 border border-slate-200 rounded-xl bg-white font-medium text-slate-700">
                  <option value="">Pilih Pekerjaan</option>
                  <?php foreach ($pekerjaanList as $pej): ?>
                    <option value="<?= htmlspecialchars($pej['nm_pekerjaan']) ?>" <?= ($parent['guardian_occupation'] ?? '') === $pej['nm_pekerjaan'] ? 'selected' : '' ?>><?= htmlspecialchars($pej['nm_pekerjaan']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div>
                <label for="guardian_income" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Penghasilan Wali</label>
                <select name="guardian_income" id="guardian_income" class="block w-full px-3 py-2 border border-slate-200 rounded-xl bg-white font-medium text-slate-700">
                  <option value="">Pilih Penghasilan</option>
                  <?php foreach ($penghasilanList as $pen): ?>
                    <option value="<?= htmlspecialchars($pen['nm_penghasilan']) ?>" <?= ($parent['guardian_income'] ?? '') === $pen['nm_penghasilan'] ? 'selected' : '' ?>><?= htmlspecialchars($pen['nm_penghasilan']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Sidebar Column (1 col span) -->
      <div class="lg:col-span-1 space-y-6">
        <!-- Pilihan Jalur & Program Studi -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-4">
          <h3 class="text-xs font-bold text-slate-450 uppercase tracking-widest border-b border-slate-100 pb-2">Jalur & Program Studi</h3>
          
          <div class="space-y-4 text-xs">
            <div>
              <label for="academic_year_id" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Tahun Akademik</label>
              <select name="academic_year_id" id="academic_year_id" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-slate-50 font-semibold text-slate-700" required>
                <option value="" disabled <?= empty($registration['academic_year_id']) ? 'selected' : '' ?>>Pilih Tahun Akademik</option>
                <?php foreach ($academic_years as $ay): ?>
                  <option value="<?= $ay['id'] ?>" <?= ($registration['academic_year_id'] ?? 0) == $ay['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($ay['year']) ?> <?= $ay['is_active'] ? '(Aktif)' : '' ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div>
              <label for="wave_id" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Gelombang PMB</label>
              <select name="wave_id" id="wave_id" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-slate-50 font-semibold text-slate-700" required>
                <option value="" disabled <?= empty($registration['wave_id']) ? 'selected' : '' ?>>Pilih Gelombang</option>
                <?php foreach ($waves as $w): ?>
                  <option value="<?= $w['id'] ?>" <?= ($registration['wave_id'] ?? 0) == $w['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($w['name']) ?> <?= $w['is_active'] ? '(Aktif)' : '' ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div>
              <label for="admission_path_id" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Jalur Masuk</label>
              <select name="admission_path_id" id="admission_path_id" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-slate-50 font-semibold text-slate-700" required>
                <option value="" disabled <?= empty($registration['admission_path_id']) ? 'selected' : '' ?>>Pilih Jalur</option>
                <?php foreach ($admission_paths as $ap): ?>
                  <option value="<?= $ap['id'] ?>" <?= ($registration['admission_path_id'] ?? 0) == $ap['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($ap['name']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div>
              <label for="class_id" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Jenis Kelas</label>
              <select name="class_id" id="class_id" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-slate-50 font-semibold text-slate-700" required>
                <option value="" disabled <?= empty($registration['class_id']) ? 'selected' : '' ?>>Pilih Kelas</option>
                <?php foreach ($classes as $c): ?>
                  <option value="<?= $c['id'] ?>" <?= ($registration['class_id'] ?? 0) == $c['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c['name']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div>
              <label for="program1_id" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Pilihan 1 (Utama)</label>
              <select name="program1_id" id="program1_id" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-slate-50 font-semibold text-slate-700" required>
                <option value="" <?= empty($programs['program1_id']) ? 'selected' : '' ?>>Pilih Program Studi 1</option>
                <?php foreach ($study_programs as $sp): ?>
                  <option value="<?= $sp['id'] ?>" <?= (int)($programs['program1_id'] ?? 0) === $sp['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($sp['name']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div>
              <label for="program2_id" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Pilihan 2 (Cadangan)</label>
              <select name="program2_id" id="program2_id" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-slate-50 font-semibold text-slate-700">
                <option value="">Tidak Memilih Pilihan 2</option>
                <?php foreach ($study_programs as $sp): ?>
                  <option value="<?= $sp['id'] ?>" <?= (int)($programs['program2_id'] ?? 0) === $sp['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($sp['name']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
        </div>

        <!-- Submit Panel -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-4">
          <h3 class="text-xs font-bold text-slate-450 uppercase tracking-widest border-b border-slate-100 pb-2">Simpan Perubahan</h3>
          <p class="text-[11px] text-slate-500 leading-relaxed">Pastikan semua data hasil koreksi yang dimasukkan sudah benar sesuai berkas fisik atau instruksi resmi calon mahasiswa.</p>
          <div class="pt-2">
            <button type="submit" class="w-full inline-flex items-center justify-center px-6 py-3 border border-transparent rounded-full shadow-md text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 transition-all hover:-translate-y-0.5 cursor-pointer">
              💾 Simpan Koreksi Data
            </button>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>

<script>
(() => {
  const waveStudyPrograms = <?= json_encode($wave_prodis) ?>;
  const allStudyPrograms = <?= json_encode($study_programs) ?>;
  const waveSelect = document.getElementById('wave_id');
  const p1Select = document.getElementById('program1_id');
  const p2Select = document.getElementById('program2_id');

  function updateStudyPrograms() {
    const selectedWaveId = waveSelect.value;
    const allowedProdiIds = waveStudyPrograms[selectedWaveId] || [];

    const currentP1 = p1Select.value;
    const currentP2 = p2Select.value;

    p1Select.innerHTML = '<option value="">Pilih Program Studi 1</option>';
    p2Select.innerHTML = '<option value="">Tidak Memilih Pilihan 2</option>';

    allStudyPrograms.forEach(sp => {
      if (allowedProdiIds.includes(parseInt(sp.id))) {
        const opt1 = document.createElement('option');
        opt1.value = sp.id;
        opt1.textContent = sp.name;
        if (sp.id == currentP1) opt1.selected = true;
        p1Select.appendChild(opt1);

        if (sp.id != currentP1) {
          const opt2 = document.createElement('option');
          opt2.value = sp.id;
          opt2.textContent = sp.name;
          if (sp.id == currentP2) opt2.selected = true;
          p2Select.appendChild(opt2);
        }
      }
    });

    if (p1Select.value && !allowedProdiIds.includes(parseInt(p1Select.value))) {
      p1Select.value = "";
    }
    if (p2Select.value && !allowedProdiIds.includes(parseInt(p2Select.value))) {
      p2Select.value = "";
    }
  }

  waveSelect.addEventListener('change', updateStudyPrograms);
  p1Select.addEventListener('change', updateStudyPrograms);
  
  updateStudyPrograms();
})();

const allWilayahDataEdit = <?= json_encode($wilayahList) ?>;
const initialDistrictValEdit = <?= json_encode($address['district'] ?? '') ?>;

function filterEditDistricts(query) {
  const select = document.getElementById('district');
  const term = (query || '').toLowerCase().trim();
  const currentVal = select.value || initialDistrictValEdit;

  select.innerHTML = '<option value="" disabled' + (!currentVal ? ' selected' : '') + '>Pilih Kecamatan</option>';

  allWilayahDataEdit.forEach(item => {
    const fullText = (item.kecamatan + ' ' + (item.kabupaten || '') + ' ' + (item.provinsi || '')).toLowerCase();
    if (!term || fullText.includes(term)) {
      const opt = document.createElement('option');
      opt.value = item.kecamatan;
      opt.textContent = item.kecamatan + (item.kabupaten ? ' (' + item.kabupaten + ')' : '');
      if (item.kecamatan === currentVal) {
        opt.selected = true;
      }
      select.appendChild(opt);
    }
  });
}
</script>
