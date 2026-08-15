<?php

/**
 * @var null|array $registration
 * @var mixed $waves
 * @var mixed $study_programs
 */

$jsonData = json_decode(file_get_contents(MAZU_ENV_PATH . 'data.json'), true);
$agamaList = $jsonData['agama'][0] ?? [];
$negaraList = $jsonData['kewarganegaraan'][0] ?? [];
$tinggalList = $jsonData['jenis_tinggal'][0] ?? [];
$transportList = $jsonData['alat_transportasi'][0] ?? [];
$pendidikanList = $jsonData['jenjang_pendidikan'][0] ?? [];
usort($pendidikanList, function ($a, $b) {
  return ((int)($a['id_jenj_didik'] ?? 0)) <=> ((int)($b['id_jenj_didik'] ?? 0));
});

$penghasilanList = array_values(array_filter($jsonData['penghasilan'][0] ?? [], function ($item) {
  return !empty($item['nm_penghasilan']);
}));
usort($penghasilanList, function ($a, $b) {
  return ((int)($a['id_penghasilan'] ?? 0)) <=> ((int)($b['id_penghasilan'] ?? 0));
});

$pekerjaanList = $jsonData['pekerjaan'][0] ?? [];
usort($pekerjaanList, function ($a, $b) {
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
  <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-100 pb-5">
    <div class="space-y-1">
      <div class="flex items-center gap-2">
        <a data-spa href="<?= getBaseUrl('/admin/registrants/detail?id=' . $registration['id']) ?>" class="text-xs font-bold text-indigo-650 hover:text-indigo-700 flex items-center gap-1 transition-colors">
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

  <form action="<?= getBaseUrl('/admin/registrants/update') ?>" method="POST" class="space-y-6">
    <input type="hidden" name="id" value="<?= $registration['id'] ?>">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <div class="lg:col-span-2 space-y-6">
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
              <label for="nik" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">NIK / Citizen Number</label>
              <input type="text" name="nik" id="nik" minlength="16" maxlength="16" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 font-semibold" value="<?= htmlspecialchars($registration['nik'] ?? '') ?>" required>
            </div>

            <div>
              <label for="nisn" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">NISN</label>
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
                <option value="Media Sosial" <?= ($registration['info_source'] ?? '') === 'Media Sosial' ? 'selected' : '' ?>>Media Sosial</option>
                <option value="Website Kampus" <?= ($registration['info_source'] ?? '') === 'Website Kampus' ? 'selected' : '' ?>>Website Kampus</option>
                <option value="Brosur / Spanduk" <?= ($registration['info_source'] ?? '') === 'Brosur / Spanduk' ? 'selected' : '' ?>>Brosur / Spanduk</option>
                <option value="Teman / Keluarga" <?= ($registration['info_source'] ?? '') === 'Teman / Keluarga' ? 'selected' : '' ?>>Teman / Keluarga</option>
                <option value="Kunjungan Sekolah" <?= ($registration['info_source'] ?? '') === 'Kunjungan Sekolah' ? 'selected' : '' ?>>Kunjungan Sekolah</option>
                <option value="Lainnya" <?= ($registration['info_source'] ?? '') === 'Lainnya' ? 'selected' : '' ?>>Lainnya</option>
              </select>
            </div>
          </div>
        </div>

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
                <select name="district" id="district" onchange="onDistrictSelectChange(this)" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 font-semibold text-slate-700" required>
                  <?php if (!empty($address['district'])): ?>
                    <option value="<?= htmlspecialchars($address['district']) ?>" selected><?= htmlspecialchars($address['district']) ?></option>
                  <?php else: ?>
                    <option value="" disabled selected>Pilih Kecamatan</option>
                  <?php endif; ?>
                </select>
                <input type="hidden" id="district_id_wil" name="district_id_wil" value="<?= htmlspecialchars($address['district_id_wil'] ?? '') ?>">
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

            <div class="<?= ($address['kps_receiver'] ?? '') === 'ya' ? '' : 'hidden' ?>" id="kps_number_wrapper">
              <label for="kps_number" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Nomor KPS</label>
              <input type="text" name="kps_number" id="kps_number" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 font-semibold" value="<?= htmlspecialchars($address['kps_number'] ?? '') ?>">
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
                  <option value="<?= htmlspecialchars($tl['nm_jns_tinggal'] ?? '') ?>" <?= ($address['living_type'] ?? '') === ($tl['nm_jns_tinggal'] ?? '') ? 'selected' : '' ?>><?= htmlspecialchars($tl['nm_jns_tinggal'] ?? '') ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div>
            <label for="address" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Alamat Detail (Jalan, RT/RW, Dusun)</label>
            <textarea name="address" id="address" rows="2" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-xs bg-slate-50 font-semibold" required><?= htmlspecialchars($address['address'] ?? '') ?></textarea>
          </div>
        </div>

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

            <div>
              <label for="school_address" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Kecamatan Sekolah</label>
              <div class="space-y-1.5">
                <input type="text" id="school_address_search" oninput="filterEditSchoolDistricts(this.value)" class="appearance-none block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-xs bg-slate-50 font-medium" placeholder="🔍 Cari nama kecamatan sekolah...">
                <select name="school_address" id="school_address" onchange="onSchoolDistrictSelectChange(this)" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 font-semibold text-slate-700" required>
                  <?php if (!empty($education['school_address'])): ?>
                    <option value="<?= htmlspecialchars($education['school_address']) ?>" selected><?= htmlspecialchars($education['school_address']) ?></option>
                  <?php else: ?>
                    <option value="" disabled selected>Pilih Kecamatan Sekolah</option>
                  <?php endif; ?>
                </select>
                <input type="hidden" id="school_address_id_wil" name="school_address_id_wil" value="<?= htmlspecialchars($education['school_address_id_wil'] ?? '') ?>">
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-6">
          <h3 class="text-xs font-bold text-slate-455 uppercase tracking-widest border-b border-slate-100 pb-2">4. Data Orang Tua / Wali</h3>

          <div class="space-y-3 pt-2">
            <h4 class="text-xs font-bold text-indigo-650 flex items-center gap-1">👨 Data Ayah</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 text-xs">
              <div>
                <label for="father_name" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Nama Ayah</label>
                <input type="text" name="father_name" id="father_name" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 font-semibold" value="<?= htmlspecialchars($parent['father_name'] ?? '') ?>">
              </div>

              <div>
                <label for="father_nik" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">NIK Ayah</label>
                <input type="text" name="father_nik" id="father_nik" maxlength="16" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 font-semibold" value="<?= htmlspecialchars($parent['father_nik'] ?? '') ?>">
              </div>

              <div>
                <label for="father_birth_date" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Tahun Lahir Ayah</label>
                <input type="number" name="father_birth_date" id="father_birth_date" placeholder="Contoh: 1975" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 font-semibold" value="<?= htmlspecialchars($parent['father_birth_date'] ?? '') ?>">
              </div>

              <div>
                <label for="father_education" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Pendidikan Ayah</label>
                <select name="father_education" id="father_education" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 font-semibold text-slate-700">
                  <option value="">Pilih Pendidikan</option>
                  <?php foreach ($pendidikanList as $pen): ?>
                    <option value="<?= htmlspecialchars($pen['nm_jenj_didik']) ?>" <?= ($parent['father_education'] ?? '') === $pen['nm_jenj_didik'] ? 'selected' : '' ?>><?= htmlspecialchars($pen['nm_jenj_didik']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div>
                <label for="father_occupation" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Pekerjaan Ayah</label>
                <select name="father_occupation" id="father_occupation" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 font-semibold text-slate-700">
                  <option value="">Pilih Pekerjaan</option>
                  <?php foreach ($pekerjaanList as $pek): ?>
                    <option value="<?= htmlspecialchars($pek['nm_pekerjaan']) ?>" <?= ($parent['father_occupation'] ?? '') === $pek['nm_pekerjaan'] ? 'selected' : '' ?>><?= htmlspecialchars($pek['nm_pekerjaan']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div>
                <label for="father_income" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Penghasilan Ayah</label>
                <select name="father_income" id="father_income" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 font-semibold text-slate-700">
                  <option value="">Pilih Penghasilan</option>
                  <?php foreach ($penghasilanList as $pg): ?>
                    <option value="<?= htmlspecialchars($pg['nm_penghasilan']) ?>" <?= ($parent['father_income'] ?? '') === $pg['nm_penghasilan'] ? 'selected' : '' ?>><?= htmlspecialchars($pg['nm_penghasilan']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
          </div>

          <div class="space-y-3 border-t border-slate-100 pt-4">
            <h4 class="text-xs font-bold text-indigo-650 flex items-center gap-1">👩 Data Ibu</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 text-xs">
              <div>
                <label for="mother_name_detail" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Nama Ibu</label>
                <input type="text" name="mother_name" id="mother_name_detail" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 font-semibold" value="<?= htmlspecialchars($parent['mother_name'] ?? '') ?>">
              </div>

              <div>
                <label for="mother_nik" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">NIK Ibu</label>
                <input type="text" name="mother_nik" id="mother_nik" maxlength="16" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 font-semibold" value="<?= htmlspecialchars($parent['mother_nik'] ?? '') ?>">
              </div>

              <div>
                <label for="mother_birth_date" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Tahun Lahir Ibu</label>
                <input type="number" name="mother_birth_date" id="mother_birth_date" placeholder="Contoh: 1978" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 font-semibold" value="<?= htmlspecialchars($parent['mother_birth_date'] ?? '') ?>">
              </div>

              <div>
                <label for="mother_education" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Pendidikan Ibu</label>
                <select name="mother_education" id="mother_education" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 font-semibold text-slate-700">
                  <option value="">Pilih Pendidikan</option>
                  <?php foreach ($pendidikanList as $pen): ?>
                    <option value="<?= htmlspecialchars($pen['nm_jenj_didik']) ?>" <?= ($parent['mother_education'] ?? '') === $pen['nm_jenj_didik'] ? 'selected' : '' ?>><?= htmlspecialchars($pen['nm_jenj_didik']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div>
                <label for="mother_occupation" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Pekerjaan Ibu</label>
                <select name="mother_occupation" id="mother_occupation" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 font-semibold text-slate-700">
                  <option value="">Pilih Pekerjaan</option>
                  <?php foreach ($pekerjaanList as $pek): ?>
                    <option value="<?= htmlspecialchars($pek['nm_pekerjaan']) ?>" <?= ($parent['mother_occupation'] ?? '') === $pek['nm_pekerjaan'] ? 'selected' : '' ?>><?= htmlspecialchars($pek['nm_pekerjaan']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div>
                <label for="mother_income" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Penghasilan Ibu</label>
                <select name="mother_income" id="mother_income" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 font-semibold text-slate-700">
                  <option value="">Pilih Penghasilan</option>
                  <?php foreach ($penghasilanList as $pg): ?>
                    <option value="<?= htmlspecialchars($pg['nm_penghasilan']) ?>" <?= ($parent['mother_income'] ?? '') === $pg['nm_penghasilan'] ? 'selected' : '' ?>><?= htmlspecialchars($pg['nm_penghasilan']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
          </div>

          <div class="space-y-3 border-t border-slate-100 pt-4">
            <h4 class="text-xs font-bold text-indigo-650 flex items-center gap-1">👵 Data Wali (Opsional)</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 text-xs">
              <div>
                <label for="guardian_name" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Nama Wali</label>
                <input type="text" name="guardian_name" id="guardian_name" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 font-semibold" value="<?= htmlspecialchars($parent['guardian_name'] ?? '') ?>">
              </div>

              <div>
                <label for="guardian_birth_date" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Tahun Lahir Wali</label>
                <input type="number" name="guardian_birth_date" id="guardian_birth_date" placeholder="Contoh: 1970" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 font-semibold" value="<?= htmlspecialchars($parent['guardian_birth_date'] ?? '') ?>">
              </div>

              <div>
                <label for="guardian_education" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Pendidikan Wali</label>
                <select name="guardian_education" id="guardian_education" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 font-semibold text-slate-700">
                  <option value="">Pilih Pendidikan</option>
                  <?php foreach ($pendidikanList as $pen): ?>
                    <option value="<?= htmlspecialchars($pen['nm_jenj_didik']) ?>" <?= ($parent['guardian_education'] ?? '') === $pen['nm_jenj_didik'] ? 'selected' : '' ?>><?= htmlspecialchars($pen['nm_jenj_didik']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div>
                <label for="guardian_occupation" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Pekerjaan Wali</label>
                <select name="guardian_occupation" id="guardian_occupation" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 font-semibold text-slate-700">
                  <option value="">Pilih Pekerjaan</option>
                  <?php foreach ($pekerjaanList as $pek): ?>
                    <option value="<?= htmlspecialchars($pek['nm_pekerjaan']) ?>" <?= ($parent['guardian_occupation'] ?? '') === $pek['nm_pekerjaan'] ? 'selected' : '' ?>><?= htmlspecialchars($pek['nm_pekerjaan']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div>
                <label for="guardian_income" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Penghasilan Wali</label>
                <select name="guardian_income" id="guardian_income" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 font-semibold text-slate-700">
                  <option value="">Pilih Penghasilan</option>
                  <?php foreach ($penghasilanList as $pg): ?>
                    <option value="<?= htmlspecialchars($pg['nm_penghasilan']) ?>" <?= ($parent['guardian_income'] ?? '') === $pg['nm_penghasilan'] ? 'selected' : '' ?>><?= htmlspecialchars($pg['nm_penghasilan']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="space-y-6">
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-4">
          <h3 class="text-xs font-bold text-slate-455 uppercase tracking-widest border-b border-slate-100 pb-2">Program Studi & Jalur</h3>

          <div class="space-y-4 text-xs">
            <div>
              <label for="wave_id" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Gelombang Pendaftaran</label>
              <select name="wave_id" id="wave_id" class="block w-full px-3 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 font-semibold text-slate-700" required>
                <?php foreach ($waves as $w): ?>
                  <option value="<?= $w['id'] ?>" <?= ($registration['wave_id'] ?? '') == $w['id'] ? 'selected' : '' ?>><?= htmlspecialchars($w['name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div>
              <label for="program1_id" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Pilihan Program Studi 1</label>
              <select name="program1_id" id="program1_id" class="block w-full px-3 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 font-semibold text-slate-700" required>
                <option value="">Pilih Program Studi 1</option>
                <?php foreach ($study_programs as $sp): ?>
                  <option value="<?= $sp['id'] ?>" <?= ($programs['program1_id'] ?? '') == $sp['id'] ? 'selected' : '' ?>><?= htmlspecialchars($sp['name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div>
              <label for="program2_id" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Pilihan Program Studi 2 (Opsional)</label>
              <select name="program2_id" id="program2_id" class="block w-full px-3 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 font-semibold text-slate-700">
                <option value="">Tidak Memilih Pilihan 2</option>
                <?php foreach ($study_programs as $sp): ?>
                  <option value="<?= $sp['id'] ?>" <?= ($programs['program2_id'] ?? '') == $sp['id'] ? 'selected' : '' ?>><?= htmlspecialchars($sp['name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div>
              <label for="program3_id" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Pilihan Program Studi 3 (Opsional)</label>
              <select name="program3_id" id="program3_id" class="block w-full px-3 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 font-semibold text-slate-700">
                <option value="">Tidak Memilih Pilihan 3</option>
                <?php foreach ($study_programs as $sp): ?>
                  <option value="<?= $sp['id'] ?>" <?= ($programs['program3_id'] ?? '') == $sp['id'] ? 'selected' : '' ?>><?= htmlspecialchars($sp['name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-4">
          <button type="submit" class="w-full inline-flex items-center justify-center px-6 py-3 border border-transparent rounded-xl shadow-md text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 transition-all cursor-pointer">
            💾 Simpan Perubahan
          </button>

          <a data-spa href="<?= getBaseUrl('/admin/registrants/detail?id=' . $registration['id']) ?>" class="w-full inline-flex items-center justify-center px-6 py-3 border border-slate-200 rounded-xl shadow-sm text-sm font-bold text-slate-700 bg-white hover:bg-slate-50 transition-all text-center">
            Batal
          </a>
        </div>
      </div>
    </div>
  </form>
</div>

<script>
  (function() {
    const waveStudyPrograms = <?= json_encode($wave_prodis ?? []) ?>;
    const allStudyPrograms = <?= json_encode($study_programs) ?>;
    const waveSelect = document.getElementById('wave_id');
    const p1Select = document.getElementById('program1_id');
    const p2Select = document.getElementById('program2_id');
    const p3Select = document.getElementById('program3_id');

    function updateStudyPrograms() {
      const selectedWaveId = waveSelect.value;
      const allowedProdiIds = waveStudyPrograms[selectedWaveId] || [];

      const currentP1 = p1Select.value;
      const currentP2 = p2Select.value;
      const currentP3 = p3Select.value;

      p1Select.innerHTML = '<option value="">Pilih Program Studi 1</option>';
      p2Select.innerHTML = '<option value="">Tidak Memilih Pilihan 2</option>';
      p3Select.innerHTML = '<option value="">Tidak Memilih Pilihan 3</option>';

      allStudyPrograms.forEach(sp => {
        if (allowedProdiIds.includes(parseInt(sp.id))) {
          const opt1 = document.createElement('option');
          opt1.value = sp.id;
          opt1.textContent = sp.name;
          if (sp.id == currentP1) opt1.selected = true;
          p1Select.appendChild(opt1);

          const opt2 = document.createElement('option');
          opt2.value = sp.id;
          opt2.textContent = sp.name;
          if (sp.id == currentP2) opt2.selected = true;
          p2Select.appendChild(opt2);

          const opt3 = document.createElement('option');
          opt3.value = sp.id;
          opt3.textContent = sp.name;
          if (sp.id == currentP3) opt3.selected = true;
          p3Select.appendChild(opt3);
        }
      });

      if (p1Select.value && !allowedProdiIds.includes(parseInt(p1Select.value))) {
        p1Select.value = "";
      }
      if (p2Select.value && !allowedProdiIds.includes(parseInt(p2Select.value))) {
        p2Select.value = "";
      }
      if (p3Select.value && !allowedProdiIds.includes(parseInt(p3Select.value))) {
        p3Select.value = "";
      }
    }

    waveSelect.addEventListener('change', updateStudyPrograms);
    p1Select.addEventListener('change', updateStudyPrograms);

    updateStudyPrograms();
  })();

  const allWilayahDataEdit = <?= json_encode($wilayahList) ?>;
  const initialDistrictValEdit = <?= json_encode($address['district'] ?? '') ?>;
  const initialSchoolDistrictValEdit = <?= json_encode($education['school_address'] ?? '') ?>;

  function filterEditDistricts(query) {
    const select = document.getElementById('district');
    const term = (query || '').toLowerCase().trim();
    const currentVal = select.value || initialDistrictValEdit;

    select.innerHTML = '<option value="" disabled' + (!currentVal ? ' selected' : '') + '>Pilih Kecamatan</option>';

    let matches = 0;
    for (let i = 0; i < allWilayahDataEdit.length; i++) {
      const item = allWilayahDataEdit[i];
      const fullText = (item.kecamatan + ' ' + (item.kabupaten || '') + ' ' + (item.provinsi || '')).toLowerCase();
      if (!term || fullText.includes(term)) {
        const opt = document.createElement('option');
        opt.value = item.kecamatan;
        opt.setAttribute('data-id-wil', item.id_wil);
        opt.textContent = item.kecamatan + (item.kabupaten ? ' (' + item.kabupaten + ')' : '');
        if (item.kecamatan === currentVal) {
          opt.selected = true;
          document.getElementById('district_id_wil').value = item.id_wil;
        }
        select.appendChild(opt);
        matches++;
        if (matches >= 50) break;
      }
    }
  }

  function filterEditSchoolDistricts(query) {
    const select = document.getElementById('school_address');
    const term = (query || '').toLowerCase().trim();
    const currentVal = select.value || initialSchoolDistrictValEdit;

    select.innerHTML = '<option value="" disabled' + (!currentVal ? ' selected' : '') + '>Pilih Kecamatan Sekolah</option>';

    let matches = 0;
    for (let i = 0; i < allWilayahDataEdit.length; i++) {
      const item = allWilayahDataEdit[i];
      const fullText = (item.kecamatan + ' ' + (item.kabupaten || '') + ' ' + (item.provinsi || '')).toLowerCase();
      if (!term || fullText.includes(term)) {
        const opt = document.createElement('option');
        opt.value = item.kecamatan;
        opt.setAttribute('data-id-wil', item.id_wil);
        opt.textContent = item.kecamatan + (item.kabupaten ? ' (' + item.kabupaten + ')' : '');
        if (item.kecamatan === currentVal) {
          opt.selected = true;
          document.getElementById('school_address_id_wil').value = item.id_wil;
        }
        select.appendChild(opt);
        matches++;
        if (matches >= 50) break;
      }
    }
  }

  function onDistrictSelectChange(selectEl) {
    const selectedOpt = selectEl.options[selectEl.selectedIndex];
    const idWil = selectedOpt.getAttribute('data-id-wil') || '';
    document.getElementById('district_id_wil').value = idWil;
  }

  function onSchoolDistrictSelectChange(selectEl) {
    const selectedOpt = selectEl.options[selectEl.selectedIndex];
    const idWil = selectedOpt.getAttribute('data-id-wil') || '';
    document.getElementById('school_address_id_wil').value = idWil;
  }

  const kpsSelectEdit = document.getElementById('kps_receiver');
  const kpsNumberWrapperEdit = document.getElementById('kps_number_wrapper');
  const kpsNumberInputEdit = document.getElementById('kps_number');

  if (kpsSelectEdit) {
    kpsSelectEdit.addEventListener('change', function() {
      if (this.value === 'ya') {
        kpsNumberWrapperEdit.classList.remove('hidden');
      } else {
        kpsNumberWrapperEdit.classList.add('hidden');
        kpsNumberInputEdit.value = '';
      }
    });
  }

  filterEditDistricts('');
  filterEditSchoolDistricts('');
</script>