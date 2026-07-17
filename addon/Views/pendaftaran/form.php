<?php
$jsonData = json_decode(file_get_contents(MAZU_ENV_PATH . 'data.json'), true);
$agamaList = $jsonData['agama'][0] ?? [];
$negaraList = $jsonData['kewarganegaraan'][0] ?? [];
$tinggalList = $jsonData['jenis_tinggal'][0] ?? [];
$transportList = $jsonData['alat_transportasi'][0] ?? [];
$pendidikanList = $jsonData['jenjang_pendidikan'][0] ?? [];
$penghasilanList = $jsonData['penghasilan'][0] ?? [];
$pekerjaanList = $jsonData['pekerjaan'][0] ?? [];
$wilayahList = $jsonData['wilayah'][0] ?? [];

$studentNeedsArr = json_decode($special_needs['student_needs'] ?? '[]', true) ?: [];
$fatherNeedsArr = json_decode($special_needs['father_needs'] ?? '[]', true) ?: [];
$motherNeedsArr = json_decode($special_needs['mother_needs'] ?? '[]', true) ?: [];
$guardianNeedsArr = json_decode($special_needs['guardian_needs'] ?? '[]', true) ?: [];

$db = \App\Core\Foundation\Application::getInstance()->getContainer()->resolve(\App\Core\Database\DatabaseManager::class)->connection();
$stmt = $db->prepare("SELECT wave_id, study_program_id FROM wave_study_programs");
$stmt->execute();
$waveProdiList = $stmt->fetchAll() ?: [];
$waveProdis = [];
foreach ($waveProdiList as $wp) {
    $waveProdis[$wp['wave_id']][] = (int)$wp['study_program_id'];
}
?>
<style>
  .step-label {
    display: none;
  }
  @media (min-width: 640px) {
    .step-label {
      display: inline-block !important;
    }
  }
</style>
<div class="w-full py-2 space-y-8">
  <!-- Top Stepper Header -->
  <div class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-slate-200/80">
    <h2 class="text-xl font-extrabold text-slate-900 tracking-tight text-center sm:text-left">Formulir Pendaftaran Mahasiswa Baru</h2>
    <p class="mt-1 text-xs text-slate-500 text-center sm:text-left">Lengkapi formulir pendaftaran 6 langkah di bawah untuk mendaftar kuliah.</p>
    
    <!-- Stepper Navigation -->
    <div class="relative flex items-center justify-between gap-2 mt-8 max-w-2xl mx-auto">
      <div class="absolute top-[18px] left-[5%] right-[5%] h-0.5 bg-slate-100 -z-1" id="stepper-line"></div>
      
      <!-- Step 1 Indicator -->
      <div class="flex flex-col items-center text-center space-y-1.5 z-10 w-16" id="step-ind-1">
        <div class="flex items-center justify-center w-9 h-9 rounded-full bg-indigo-600 text-white font-bold text-sm border-4 border-indigo-150 transition-all cursor-default">1</div>
        <span class="text-[10px] md:text-[11px] font-bold text-indigo-600 step-label">Data Pribadi</span>
      </div>

      <!-- Step 2 Indicator -->
      <div class="flex flex-col items-center text-center space-y-1.5 z-10 w-16" id="step-ind-2">
        <div class="flex items-center justify-center w-9 h-9 rounded-full bg-slate-100 text-slate-400 font-bold text-sm border-4 border-transparent transition-all cursor-default">2</div>
        <span class="text-[10px] md:text-[11px] font-semibold text-slate-400 step-label">Alamat</span>
      </div>

      <!-- Step 3 Indicator -->
      <div class="flex flex-col items-center text-center space-y-1.5 z-10 w-16" id="step-ind-3">
        <div class="flex items-center justify-center w-9 h-9 rounded-full bg-slate-100 text-slate-400 font-bold text-sm border-4 border-transparent transition-all cursor-default">3</div>
        <span class="text-[10px] md:text-[11px] font-semibold text-slate-400 step-label">Orang Tua & Wali</span>
      </div>

      <!-- Step 4 Indicator -->
      <div class="flex flex-col items-center text-center space-y-1.5 z-10 w-16" id="step-ind-4">
        <div class="flex items-center justify-center w-9 h-9 rounded-full bg-slate-100 text-slate-400 font-bold text-sm border-4 border-transparent transition-all cursor-default">4</div>
        <span class="text-[10px] md:text-[11px] font-semibold text-slate-400 step-label">Kebutuhan Khusus</span>
      </div>

      <!-- Step 5 Indicator -->
      <div class="flex flex-col items-center text-center space-y-1.5 z-10 w-16" id="step-ind-5">
        <div class="flex items-center justify-center w-9 h-9 rounded-full bg-slate-100 text-slate-400 font-bold text-sm border-4 border-transparent transition-all cursor-default">5</div>
        <span class="text-[10px] md:text-[11px] font-semibold text-slate-400 step-label">Pendidikan</span>
      </div>

      <!-- Step 6 Indicator -->
      <div class="flex flex-col items-center text-center space-y-1.5 z-10 w-16" id="step-ind-6">
        <div class="flex items-center justify-center w-9 h-9 rounded-full bg-slate-100 text-slate-400 font-bold text-sm border-4 border-transparent transition-all cursor-default">6</div>
        <span class="text-[10px] md:text-[11px] font-semibold text-slate-400 step-label">Pilihan & Submit</span>
      </div>
    </div>
    <div class="block sm:hidden text-center text-xs font-extrabold text-indigo-600 mt-4" id="mobile-step-title">Langkah 1: Data Pribadi</div>
  </div>

  <!-- Alert Container for errors -->
  <div id="step-error-alert" class="hidden p-4 bg-red-50 border border-red-500 text-red-700 rounded-2xl flex items-center gap-3">
    <span>⚠️</span>
    <span class="text-sm font-semibold" id="error-alert-message"></span>
  </div>

  <!-- Wizard Form Card -->
  <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden p-6 md:p-8">
    <form id="wizard-form" method="POST" action="/pendaftaran/submit" class="space-y-6">
      
      <!-- STEP 1: DATA PRIBADI -->
      <div id="step-content-1" class="step-pane space-y-6">
        <h3 class="text-base font-bold text-slate-800 border-b border-slate-100 pb-3 flex items-center gap-2">
          <span>👤</span> Data Pribadi Calon Mahasiswa
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="space-y-1">
            <label for="full_name" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Nama Lengkap <span class="text-red-550">*</span></label>
            <input type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($registration['full_name'] ?? $_SESSION['auth.user_name'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-medium" placeholder="Nama sesuai ijazah">
          </div>

          <div class="space-y-1">
            <label for="birth_place" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Tempat Lahir <span class="text-red-550">*</span></label>
            <input type="text" id="birth_place" name="birth_place" value="<?= htmlspecialchars($registration['birth_place'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-medium" placeholder="Kota lahir">
          </div>

          <div class="space-y-1">
            <label for="birth_date" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Tanggal Lahir <span class="text-red-550">*</span></label>
            <input type="date" id="birth_date" name="birth_date" value="<?= !empty($registration['birth_date']) ? date('Y-m-d', strtotime(str_replace('/', '-', $registration['birth_date']))) : '' ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm text-sm bg-slate-50 font-medium">
          </div>

          <div class="space-y-1">
            <label for="gender" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Jenis Kelamin <span class="text-red-550">*</span></label>
            <select id="gender" name="gender" class="block w-full px-3 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-medium">
              <option value="" disabled <?= empty($registration['gender']) ? 'selected' : '' ?>>Pilih jenis kelamin</option>
              <option value="Laki-laki" <?= ($registration['gender'] ?? '') === 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
              <option value="Perempuan" <?= ($registration['gender'] ?? '') === 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
            </select>
          </div>

          <div class="space-y-1">
            <label for="religion" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Agama <span class="text-red-550">*</span></label>
            <select id="religion" name="religion" class="block w-full px-3 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-medium">
              <option value="" disabled <?= empty($registration['religion']) ? 'selected' : '' ?>>Pilih Agama</option>
              <?php foreach ($agamaList as $ag): ?>
                <option value="<?= htmlspecialchars($ag['nm_agama']) ?>" <?= ($registration['religion'] ?? '') === $ag['nm_agama'] ? 'selected' : '' ?>><?= htmlspecialchars($ag['nm_agama']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="space-y-1">
            <label for="mother_name" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Nama Ibu Kandung <span class="text-red-550">*</span></label>
            <input type="text" id="mother_name" name="mother_name" value="<?= htmlspecialchars($registration['mother_name'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-medium" placeholder="Nama ibu kandung">
          </div>

          <div class="space-y-1">
            <label for="info_source" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Dapat Info Kampus Dari Mana? <span class="text-red-550">*</span></label>
            <select id="info_source" name="info_source" class="block w-full px-3 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-medium">
              <option value="" disabled <?= empty($registration['info_source']) ? 'selected' : '' ?>>Pilih Info Kampus</option>
              <option value="media sosial" <?= ($registration['info_source'] ?? '') === 'media sosial' ? 'selected' : '' ?>>Media Sosial</option>
              <option value="sosialisasi" <?= ($registration['info_source'] ?? '') === 'sosialisasi' ? 'selected' : '' ?>>Sosialisasi</option>
            </select>
          </div>
        </div>
      </div>

      <!-- STEP 2: ALAMAT -->
      <div id="step-content-2" class="step-pane space-y-6 hidden">
        <h3 class="text-base font-bold text-slate-800 border-b border-slate-100 pb-3 flex items-center gap-2">
          <span>📍</span> Data Alamat Lengkap & Kontak
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="space-y-1">
            <label for="citizenship" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Kewarganegaraan <span class="text-red-550">*</span></label>
            <select id="citizenship" name="citizenship" class="block w-full px-3 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-medium">
              <option value="" disabled <?= empty($address['citizenship']) ? 'selected' : '' ?>>Pilih Kewarganegaraan</option>
              <?php foreach ($negaraList as $neg): ?>
                <option value="<?= htmlspecialchars($neg['nm_negara']) ?>" <?= ($address['citizenship'] ?? '') === $neg['nm_negara'] ? 'selected' : '' ?>><?= htmlspecialchars($neg['nm_negara']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="space-y-1">
            <label for="nik" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">NIK (No. KTP) <span class="text-red-550">*</span></label>
            <input type="text" id="nik" name="nik" maxlength="16" value="<?= htmlspecialchars($registration['nik'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-medium" placeholder="16 digit NIK">
          </div>

          <div class="space-y-1">
            <label for="nisn" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">NISN <span class="text-red-550">*</span></label>
            <input type="text" id="nisn" name="nisn" maxlength="10" value="<?= htmlspecialchars($registration['nisn'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-medium" placeholder="10 digit NISN">
          </div>

          <div class="space-y-1">
            <label for="npwp" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">NPWP</label>
            <input type="text" id="npwp" name="npwp" value="<?= htmlspecialchars($address['npwp'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-medium" placeholder="Isi NPWP jika ada">
          </div>

          <div class="space-y-1">
            <label for="street" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Jalan</label>
            <input type="text" id="street" name="street" value="<?= htmlspecialchars($address['street'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-medium" placeholder="Nama Jalan">
          </div>

          <div class="space-y-1">
            <label for="telephone" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Telepon</label>
            <input type="text" id="telephone" name="telephone" value="<?= htmlspecialchars($address['telephone'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-medium" placeholder="Format: 081234567890">
            <span class="text-[10px] text-slate-400 font-medium block mt-0.5">* Gunakan format: 081234567890</span>
          </div>

          <div class="space-y-1">
            <label for="dusun" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Dusun</label>
            <input type="text" id="dusun" name="dusun" value="<?= htmlspecialchars($address['dusun'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-medium" placeholder="Dusun / Kampung">
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div class="space-y-1">
              <label for="rt" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">RT</label>
              <input type="text" id="rt" name="rt" maxlength="5" value="<?= htmlspecialchars($address['rt'] ?? '') ?>" class="appearance-none block w-full px-3 py-3 border border-slate-200 rounded-xl shadow-sm text-sm bg-slate-50 font-medium text-center" placeholder="RT">
            </div>
            <div class="space-y-1">
              <label for="rw" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">RW</label>
              <input type="text" id="rw" name="rw" maxlength="5" value="<?= htmlspecialchars($address['rw'] ?? '') ?>" class="appearance-none block w-full px-3 py-3 border border-slate-200 rounded-xl shadow-sm text-sm bg-slate-50 font-medium text-center" placeholder="RW">
            </div>
          </div>

          <div class="space-y-1">
            <label for="hp" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">No. HP / WhatsApp <span class="text-red-550">*</span></label>
            <input type="text" id="hp" name="hp" value="<?= htmlspecialchars($registration['phone'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-medium" placeholder="08xxxxxxxxxx">
            <span class="text-[10px] text-slate-400 font-medium block mt-0.5">* Gunakan format: 081234567890</span>
          </div>

          <div class="space-y-1">
            <label for="district" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Kecamatan <span class="text-red-550">*</span></label>
            <select id="district" name="district" onchange="onDistrictChange(this)" class="block w-full px-3 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-medium">
              <option value="" disabled <?= empty($address['district']) ? 'selected' : '' ?>>Pilih Kecamatan</option>
              <?php foreach ($wilayahList as $wil): ?>
                <option value="<?= htmlspecialchars($wil['kecamatan']) ?>" data-kabupaten="<?= htmlspecialchars($wil['kabupaten']) ?>" data-provinsi="<?= htmlspecialchars($wil['provinsi']) ?>" <?= ($address['district'] ?? '') === $wil['kecamatan'] ? 'selected' : '' ?>><?= htmlspecialchars($wil['kecamatan']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="space-y-1">
            <label for="subdistrict" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Kelurahan / Desa <span class="text-red-550">*</span></label>
            <input type="text" id="subdistrict" name="subdistrict" value="<?= htmlspecialchars($address['subdistrict'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-medium" placeholder="Kelurahan tinggal">
          </div>

          <input type="hidden" id="city" name="city" value="<?= htmlspecialchars($address['city'] ?? '') ?>">
          <input type="hidden" id="province" name="province" value="<?= htmlspecialchars($address['province'] ?? '') ?>">

          <div class="space-y-1">
            <label for="postal_code" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Kode Pos</label>
            <input type="text" id="postal_code" name="postal_code" maxlength="5" value="<?= htmlspecialchars($address['postal_code'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-medium" placeholder="5 digit kode pos">
          </div>

          <div class="space-y-1">
            <label for="email" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Email Aktif <span class="text-red-550">*</span></label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($registration['email'] ?? $_SESSION['auth.user_email'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-medium" placeholder="nama@email.com">
          </div>

          <div class="space-y-1">
            <label for="kps_receiver" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Penerima KPS? <span class="text-red-550">*</span></label>
            <select id="kps_receiver" name="kps_receiver" class="block w-full px-3 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-medium">
              <option value="" disabled <?= empty($address['kps_receiver']) ? 'selected' : '' ?>>Pilih Penerima KPS</option>
              <option value="ya" <?= ($address['kps_receiver'] ?? '') === 'ya' ? 'selected' : '' ?>>Ya</option>
              <option value="tidak" <?= ($address['kps_receiver'] ?? '') === 'tidak' ? 'selected' : '' ?>>Tidak</option>
            </select>
          </div>

          <div class="space-y-1">
            <label for="transportation" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Alat Transportasi</label>
            <select id="transportation" name="transportation" class="block w-full px-3 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-medium">
              <option value="" <?= empty($address['transportation']) ? 'selected' : '' ?>>Pilih Alat Transportasi</option>
              <?php foreach ($transportList as $tr): ?>
                <option value="<?= htmlspecialchars($tr['nm_alat_transport']) ?>" <?= ($address['transportation'] ?? '') === $tr['nm_alat_transport'] ? 'selected' : '' ?>><?= htmlspecialchars($tr['nm_alat_transport']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="space-y-1">
            <label for="living_type" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Jenis Tinggal</label>
            <select id="living_type" name="living_type" class="block w-full px-3 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-medium">
              <option value="" <?= empty($address['living_type']) ? 'selected' : '' ?>>Pilih Jenis Tinggal</option>
              <?php foreach ($tinggalList as $tg): ?>
                <option value="<?= htmlspecialchars($tg['nm_jns_tinggal']) ?>" <?= ($address['living_type'] ?? '') === $tg['nm_jns_tinggal'] ? 'selected' : '' ?>><?= htmlspecialchars($tg['nm_jns_tinggal']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="space-y-1">
          <label for="address" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Alamat Lengkap Detail</label>
          <textarea id="address" name="address" rows="3" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-medium" placeholder="Detail alamat, No. Rumah, RT/RW, Dusun/Kampung"><?= htmlspecialchars($address['address'] ?? '') ?></textarea>
        </div>
      </div>

      <!-- STEP 3: ORANG TUA & WALI -->
      <div id="step-content-3" class="step-pane space-y-6 hidden">
        <h3 class="text-base font-bold text-slate-800 border-b border-slate-100 pb-3 flex items-center gap-2">
          <span>👨‍👩‍👦</span> Data Orang Tua & Wali
        </h3>
        
        <p class="text-xs text-indigo-750 font-semibold bg-indigo-50 p-4 rounded-xl leading-relaxed">
          💡 <strong>Ketentuan Pengisian:</strong> Silakan isi data orang tua (Ayah & Ibu). Apabila data orang tua tidak diisi, maka formulir Wali <strong>wajib</strong> dilengkapi.
        </p>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
          <!-- Ayah Card -->
          <div class="bg-slate-50/50 p-6 rounded-2xl border border-slate-150 space-y-4">
            <h4 class="text-sm font-bold text-indigo-750 flex items-center gap-2">👨 Data Ayah</h4>
            
            <div class="space-y-3">
              <div class="space-y-1">
                <label for="father_name" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Nama Lengkap Ayah</label>
                <input type="text" id="father_name" name="father_name" value="<?= htmlspecialchars($parents['father_name'] ?? '') ?>" class="appearance-none block w-full px-3 py-2.5 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 text-xs bg-white font-medium">
              </div>

              <div class="space-y-1">
                <label for="father_nik" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">NIK Ayah</label>
                <input type="text" id="father_nik" name="father_nik" maxlength="16" value="<?= htmlspecialchars($parents['father_nik'] ?? '') ?>" class="appearance-none block w-full px-3 py-2.5 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 text-xs bg-white font-medium" placeholder="16 digit NIK">
              </div>

              <div class="space-y-1">
                <label for="father_birth_date" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Tanggal Lahir Ayah</label>
                <input type="date" id="father_birth_date" name="father_birth_date" value="<?= !empty($parents['father_birth_date']) ? date('Y-m-d', strtotime(str_replace('/', '-', $parents['father_birth_date']))) : '' ?>" class="appearance-none block w-full px-3 py-2.5 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 text-xs bg-white font-medium">
              </div>

              <div class="space-y-1">
                <label for="father_education" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Pendidikan Terakhir</label>
                <select id="father_education" name="father_education" class="block w-full px-2.5 py-2.5 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 text-xs bg-white font-medium">
                  <option value="" disabled <?= empty($parents['father_education']) ? 'selected' : '' ?>>Pilih Pendidikan</option>
                  <?php foreach ($pendidikanList as $edu): ?>
                    <option value="<?= htmlspecialchars($edu['nm_jenj_didik']) ?>" <?= ($parents['father_education'] ?? '') === $edu['nm_jenj_didik'] ? 'selected' : '' ?>><?= htmlspecialchars($edu['nm_jenj_didik']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="space-y-1">
                <label for="father_occupation" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Pekerjaan</label>
                <select id="father_occupation" name="father_occupation" class="block w-full px-2.5 py-2.5 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 text-xs bg-white font-medium">
                  <option value="" disabled <?= empty($parents['father_occupation']) ? 'selected' : '' ?>>Pilih Pekerjaan</option>
                  <?php foreach ($pekerjaanList as $pek): ?>
                    <option value="<?= htmlspecialchars($pek['nm_pekerjaan']) ?>" <?= ($parents['father_occupation'] ?? '') === $pek['nm_pekerjaan'] ? 'selected' : '' ?>><?= htmlspecialchars($pek['nm_pekerjaan']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="space-y-1">
                <label for="father_income" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Penghasilan Bulanan</label>
                <select id="father_income" name="father_income" class="block w-full px-2.5 py-2.5 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 text-xs bg-white font-medium">
                  <option value="" disabled <?= empty($parents['father_income']) ? 'selected' : '' ?>>Pilih Penghasilan</option>
                  <?php foreach ($penghasilanList as $inc): ?>
                    <?php if (!empty($inc['nm_penghasilan'])): ?>
                      <option value="<?= htmlspecialchars($inc['nm_penghasilan']) ?>" <?= ($parents['father_income'] ?? '') === $inc['nm_penghasilan'] ? 'selected' : '' ?>><?= htmlspecialchars($inc['nm_penghasilan']) ?></option>
                    <?php endif; ?>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
          </div>

          <!-- Ibu Card -->
          <div class="bg-slate-50/50 p-6 rounded-2xl border border-slate-150 space-y-4">
            <h4 class="text-sm font-bold text-indigo-750 flex items-center gap-2">👩 Data Ibu</h4>
            
            <div class="space-y-3">
              <div class="space-y-1">
                <label for="mother_name_parent" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Nama Lengkap Ibu</label>
                <input type="text" id="mother_name_parent" name="parent_mother_name" value="<?= htmlspecialchars($parents['mother_name'] ?? '') ?>" class="appearance-none block w-full px-3 py-2.5 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 text-xs bg-white font-medium">
              </div>

              <div class="space-y-1">
                <label for="mother_nik" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">NIK Ibu</label>
                <input type="text" id="mother_nik" name="mother_nik" maxlength="16" value="<?= htmlspecialchars($parents['mother_nik'] ?? '') ?>" class="appearance-none block w-full px-3 py-2.5 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 text-xs bg-white font-medium" placeholder="16 digit NIK">
              </div>

              <div class="space-y-1">
                <label for="mother_birth_date" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Tanggal Lahir Ibu</label>
                <input type="date" id="mother_birth_date" name="mother_birth_date" value="<?= !empty($parents['mother_birth_date']) ? date('Y-m-d', strtotime(str_replace('/', '-', $parents['mother_birth_date']))) : '' ?>" class="appearance-none block w-full px-3 py-2.5 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 text-xs bg-white font-medium">
              </div>

              <div class="space-y-1">
                <label for="mother_education" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Pendidikan Terakhir</label>
                <select id="mother_education" name="mother_education" class="block w-full px-2.5 py-2.5 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 text-xs bg-white font-medium">
                  <option value="" disabled <?= empty($parents['mother_education']) ? 'selected' : '' ?>>Pilih Pendidikan</option>
                  <?php foreach ($pendidikanList as $edu): ?>
                    <option value="<?= htmlspecialchars($edu['nm_jenj_didik']) ?>" <?= ($parents['mother_education'] ?? '') === $edu['nm_jenj_didik'] ? 'selected' : '' ?>><?= htmlspecialchars($edu['nm_jenj_didik']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="space-y-1">
                <label for="mother_occupation" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Pekerjaan</label>
                <select id="mother_occupation" name="mother_occupation" class="block w-full px-2.5 py-2.5 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 text-xs bg-white font-medium">
                  <option value="" disabled <?= empty($parents['mother_occupation']) ? 'selected' : '' ?>>Pilih Pekerjaan</option>
                  <?php foreach ($pekerjaanList as $pek): ?>
                    <option value="<?= htmlspecialchars($pek['nm_pekerjaan']) ?>" <?= ($parents['mother_occupation'] ?? '') === $pek['nm_pekerjaan'] ? 'selected' : '' ?>><?= htmlspecialchars($pek['nm_pekerjaan']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="space-y-1">
                <label for="mother_income" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Penghasilan Bulanan</label>
                <select id="mother_income" name="mother_income" class="block w-full px-2.5 py-2.5 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 text-xs bg-white font-medium">
                  <option value="" disabled <?= empty($parents['mother_income']) ? 'selected' : '' ?>>Pilih Penghasilan</option>
                  <?php foreach ($penghasilanList as $inc): ?>
                    <?php if (!empty($inc['nm_penghasilan'])): ?>
                      <option value="<?= htmlspecialchars($inc['nm_penghasilan']) ?>" <?= ($parents['mother_income'] ?? '') === $inc['nm_penghasilan'] ? 'selected' : '' ?>><?= htmlspecialchars($inc['nm_penghasilan']) ?></option>
                    <?php endif; ?>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
          </div>
        </div>

        <!-- Wali Section -->
        <div class="bg-indigo-50/20 p-6 rounded-2xl border border-indigo-100/50 space-y-4 mt-6">
          <h4 class="text-sm font-bold text-indigo-750 flex items-center gap-2">👤 Data Wali (Opsional / Pengganti)</h4>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-1">
              <label for="guardian_name" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Nama Lengkap Wali</label>
              <input type="text" id="guardian_name" name="guardian_name" value="<?= htmlspecialchars($parents['guardian_name'] ?? '') ?>" class="appearance-none block w-full px-3 py-2.5 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 text-xs bg-white font-medium">
            </div>

            <div class="space-y-1">
              <label for="guardian_birth_date" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Tanggal Lahir Wali</label>
              <input type="date" id="guardian_birth_date" name="guardian_birth_date" value="<?= !empty($parents['guardian_birth_date']) ? date('Y-m-d', strtotime(str_replace('/', '-', $parents['guardian_birth_date']))) : '' ?>" class="appearance-none block w-full px-3 py-2.5 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 text-xs bg-white font-medium">
            </div>

            <div class="space-y-1">
              <label for="guardian_education" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Pendidikan Terakhir Wali</label>
              <select id="guardian_education" name="guardian_education" class="block w-full px-2.5 py-2.5 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 text-xs bg-white font-medium">
                <option value="" <?= empty($parents['guardian_education']) ? 'selected' : '' ?>>Pilih Pendidikan (Kosongkan jika tidak ada)</option>
                <?php foreach ($pendidikanList as $edu): ?>
                  <option value="<?= htmlspecialchars($edu['nm_jenj_didik']) ?>" <?= ($parents['guardian_education'] ?? '') === $edu['nm_jenj_didik'] ? 'selected' : '' ?>><?= htmlspecialchars($edu['nm_jenj_didik']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="space-y-1">
              <label for="guardian_occupation" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Pekerjaan Wali</label>
              <select id="guardian_occupation" name="guardian_occupation" class="block w-full px-2.5 py-2.5 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 text-xs bg-white font-medium">
                <option value="" <?= empty($parents['guardian_occupation']) ? 'selected' : '' ?>>Pilih Pekerjaan (Kosongkan jika tidak ada)</option>
                <?php foreach ($pekerjaanList as $pek): ?>
                  <option value="<?= htmlspecialchars($pek['nm_pekerjaan']) ?>" <?= ($parents['guardian_occupation'] ?? '') === $pek['nm_pekerjaan'] ? 'selected' : '' ?>><?= htmlspecialchars($pek['nm_pekerjaan']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="space-y-1">
              <label for="guardian_income" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Penghasilan Bulanan Wali</label>
              <select id="guardian_income" name="guardian_income" class="block w-full px-2.5 py-2.5 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 text-xs bg-white font-medium">
                <option value="" <?= empty($parents['guardian_income']) ? 'selected' : '' ?>>Pilih Penghasilan (Kosongkan jika tidak ada)</option>
                <?php foreach ($penghasilanList as $inc): ?>
                  <?php if (!empty($inc['nm_penghasilan'])): ?>
                    <option value="<?= htmlspecialchars($inc['nm_penghasilan']) ?>" <?= ($parents['guardian_income'] ?? '') === $inc['nm_penghasilan'] ? 'selected' : '' ?>><?= htmlspecialchars($inc['nm_penghasilan']) ?></option>
                  <?php endif; ?>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
        </div>
      </div>

      <!-- STEP 4: KEBUTUHAN KHUSUS -->
      <div id="step-content-4" class="step-pane space-y-6 hidden">
        <h3 class="text-base font-bold text-slate-800 border-b border-slate-100 pb-3 flex items-center gap-2">
          <span>♿</span> Kebutuhan Khusus
        </h3>
        
        <div class="space-y-4">
          <div class="space-y-1">
            <label for="has_special_needs" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Apakah Anda memiliki kebutuhan khusus? <span class="text-red-550">*</span></label>
            <select id="has_special_needs" name="has_special_needs" onchange="toggleSpecialNeedsList(this.value)" class="block w-full px-3 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-medium">
              <option value="" disabled <?= empty($special_needs['has_special_needs']) ? 'selected' : '' ?>>Pilih Jawaban</option>
              <option value="Ya" <?= ($special_needs['has_special_needs'] ?? '') === 'Ya' ? 'selected' : '' ?>>Ya</option>
              <option value="Tidak" <?= ($special_needs['has_special_needs'] ?? '') === 'Tidak' ? 'selected' : '' ?>>Tidak</option>
            </select>
          </div>

          <!-- Special Needs Choices -->
          <div id="special-needs-container" class="hidden space-y-6 pt-4">
            <?php
            $needsList = [
                'tuna netra', 'tuna rungu', 'tuna grahita ringan', 'tuna grahita sedang', 
                'tuna daksa ringan', 'tuna daksa sedang', 'tuna laras', 'tuna wicara', 
                'hiperaktif', 'cerdas istimewa', 'bakat istimewa', 'kesulitan belajar', 
                'narkoba', 'indigo', 'down syndrome', 'autis'
            ];
            ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Calon Mahasiswa -->
              <div class="bg-slate-50/50 p-5 rounded-2xl border border-slate-150 space-y-3">
                <h4 class="text-xs font-bold text-indigo-750 uppercase tracking-wider">🎓 Calon Mahasiswa</h4>
                <div class="grid grid-cols-1 gap-2 max-h-48 overflow-y-auto pr-2">
                  <?php foreach ($needsList as $need): ?>
                    <label class="flex items-center gap-2 text-xs text-slate-650 cursor-pointer select-none">
                      <input type="checkbox" name="student_needs[]" value="<?= $need ?>" <?= in_array($need, $studentNeedsArr) ? 'checked' : '' ?> class="rounded text-indigo-650 border-slate-350 focus:ring-indigo-500">
                      <?= ucwords($need) ?>
                    </label>
                  <?php endforeach; ?>
                </div>
              </div>

              <!-- Ayah -->
              <div class="bg-slate-50/50 p-5 rounded-2xl border border-slate-150 space-y-3">
                <h4 class="text-xs font-bold text-indigo-750 uppercase tracking-wider">👨 Ayah</h4>
                <div class="grid grid-cols-1 gap-2 max-h-48 overflow-y-auto pr-2">
                  <?php foreach ($needsList as $need): ?>
                    <label class="flex items-center gap-2 text-xs text-slate-650 cursor-pointer select-none">
                      <input type="checkbox" name="father_needs[]" value="<?= $need ?>" <?= in_array($need, $fatherNeedsArr) ? 'checked' : '' ?> class="rounded text-indigo-650 border-slate-350 focus:ring-indigo-500">
                      <?= ucwords($need) ?>
                    </label>
                  <?php endforeach; ?>
                </div>
              </div>

              <!-- Ibu -->
              <div class="bg-slate-50/50 p-5 rounded-2xl border border-slate-150 space-y-3">
                <h4 class="text-xs font-bold text-indigo-750 uppercase tracking-wider">👩 Ibu</h4>
                <div class="grid grid-cols-1 gap-2 max-h-48 overflow-y-auto pr-2">
                  <?php foreach ($needsList as $need): ?>
                    <label class="flex items-center gap-2 text-xs text-slate-650 cursor-pointer select-none">
                      <input type="checkbox" name="mother_needs[]" value="<?= $need ?>" <?= in_array($need, $motherNeedsArr) ? 'checked' : '' ?> class="rounded text-indigo-650 border-slate-350 focus:ring-indigo-500">
                      <?= ucwords($need) ?>
                    </label>
                  <?php endforeach; ?>
                </div>
              </div>

              <!-- Wali -->
              <div class="bg-slate-50/50 p-5 rounded-2xl border border-slate-150 space-y-3">
                <h4 class="text-xs font-bold text-indigo-750 uppercase tracking-wider">👤 Wali</h4>
                <div class="grid grid-cols-1 gap-2 max-h-48 overflow-y-auto pr-2">
                  <?php foreach ($needsList as $need): ?>
                    <label class="flex items-center gap-2 text-xs text-slate-650 cursor-pointer select-none">
                      <input type="checkbox" name="guardian_needs[]" value="<?= $need ?>" <?= in_array($need, $guardianNeedsArr) ? 'checked' : '' ?> class="rounded text-indigo-650 border-slate-350 focus:ring-indigo-500">
                      <?= ucwords($need) ?>
                    </label>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- STEP 5: RIWAYAT PENDIDIKAN -->
      <div id="step-content-5" class="step-pane space-y-6 hidden">
        <h3 class="text-base font-bold text-slate-800 border-b border-slate-100 pb-3 flex items-center gap-2">
          <span>🏫</span> Asal Sekolah & Pendidikan Terakhir
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="space-y-1">
            <label for="school_name" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Nama Sekolah Asal (SMA/SMK/MA) <span class="text-red-550">*</span></label>
            <input type="text" id="school_name" name="school_name" value="<?= htmlspecialchars($education['school_name'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-medium" placeholder="Nama sekolah asal">
          </div>

          <div class="space-y-1">
            <label for="school_major" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Jurusan Sekolah <span class="text-red-550">*</span></label>
            <input type="text" id="school_major" name="school_major" value="<?= htmlspecialchars($education['school_major'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-medium" placeholder="Contoh: IPA, IPS, Teknik Mesin">
          </div>

          <div class="space-y-1">
            <label for="graduation_year" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Tahun Lulus <span class="text-red-550">*</span></label>
            <input type="text" id="graduation_year" name="graduation_year" maxlength="4" value="<?= htmlspecialchars($education['graduation_year'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-medium" placeholder="Contoh: 2026">
          </div>

          <div class="space-y-1">
            <label for="diploma_number" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Nomor Ijazah / SKL <span class="text-red-550">*</span></label>
            <input type="text" id="diploma_number" name="diploma_number" value="<?= htmlspecialchars($education['diploma_number'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-medium" placeholder="Isi '-' jika belum terbit">
          </div>

          <div class="space-y-1">
            <label for="average_score" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Nilai Rata-Rata Ujian / Rapor <span class="text-red-550">*</span></label>
            <input type="number" step="0.01" min="0" max="100" id="average_score" name="average_score" value="<?= htmlspecialchars($education['average_score'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-medium" placeholder="Contoh: 85.50">
          </div>
        </div>
      </div>

      <!-- STEP 6: PILIHAN PROGRAM STUDI & SUBMIT -->
      <div id="step-content-6" class="step-pane space-y-6 hidden">
        <h3 class="text-base font-bold text-slate-800 border-b border-slate-100 pb-3 flex items-center gap-2">
          <span>🛤️</span> Jalur & Program Studi Pilihan
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="space-y-1">
            <label for="academic_year_id" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Tahun Akademik <span class="text-red-550">*</span></label>
            <select id="academic_year_id" name="academic_year_id" class="block w-full px-3 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-medium">
              <option value="" disabled <?= empty($registration['academic_year_id']) ? 'selected' : '' ?>>Pilih Tahun Akademik</option>
              <?php foreach ($academic_years as $ay): ?>
                <?php if ($ay['is_active']): ?>
                  <option value="<?= $ay['id'] ?>" <?= ($registration['academic_year_id'] ?? '') == $ay['id'] ? 'selected' : '' ?>><?= htmlspecialchars($ay['year']) ?> (Aktif)</option>
                <?php endif; ?>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="space-y-1">
            <label for="wave_id" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Gelombang <span class="text-red-550">*</span></label>
            <select id="wave_id" name="wave_id" class="block w-full px-3 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-medium">
              <option value="" disabled <?= empty($registration['wave_id']) ? 'selected' : '' ?>>Pilih Gelombang</option>
              <?php foreach ($waves as $w): ?>
                <?php if ($w['is_active']): ?>
                  <option value="<?= $w['id'] ?>" <?= ($registration['wave_id'] ?? '') == $w['id'] ? 'selected' : '' ?>><?= htmlspecialchars($w['name']) ?> (<?= htmlspecialchars($w['start_date']) ?> s.d <?= htmlspecialchars($w['end_date']) ?>)</option>
                <?php endif; ?>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="space-y-1">
            <label for="admission_path_id" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Jalur Masuk <span class="text-red-550">*</span></label>
            <select id="admission_path_id" name="admission_path_id" class="block w-full px-3 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-medium">
              <option value="" disabled <?= empty($registration['admission_path_id']) ? 'selected' : '' ?>>Pilih Jalur</option>
              <?php foreach ($admission_paths as $ap): ?>
                <option value="<?= $ap['id'] ?>" <?= ($registration['admission_path_id'] ?? '') == $ap['id'] ? 'selected' : '' ?>><?= htmlspecialchars($ap['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="space-y-1">
            <label for="class_id" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Jenis Kelas <span class="text-red-550">*</span></label>
            <select id="class_id" name="class_id" class="block w-full px-3 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-medium">
              <option value="" disabled <?= empty($registration['class_id']) ? 'selected' : '' ?>>Pilih Kelas</option>
              <?php foreach ($classes as $c): ?>
                <option value="<?= $c['id'] ?>" <?= ($registration['class_id'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="space-y-1">
            <label for="program1_id" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Pilihan Program Studi 1 <span class="text-red-550">*</span></label>
            <select id="program1_id" name="program1_id" class="block w-full px-3 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-medium">
              <option value="" <?= empty($program['program1_id']) ? 'selected' : '' ?>>Pilih Program Studi 1</option>
              <?php foreach ($study_programs as $sp): ?>
                <option value="<?= $sp['id'] ?>" <?= ($program['program1_id'] ?? '') == $sp['id'] ? 'selected' : '' ?>><?= htmlspecialchars($sp['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="space-y-1">
            <label for="program2_id" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Pilihan Program Studi 2 (Opsional)</label>
            <select id="program2_id" name="program2_id" class="block w-full px-3 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-medium">
              <option value="" <?= empty($program['program2_id']) ? 'selected' : '' ?>>Pilih Program Studi 2 (Kosongkan jika tidak memilih)</option>
              <?php foreach ($study_programs as $sp): ?>
                <option value="<?= $sp['id'] ?>" <?= ($program['program2_id'] ?? '') == $sp['id'] ? 'selected' : '' ?>><?= htmlspecialchars($sp['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <!-- REVIEW SUMMARY SECTION -->
        <div class="bg-indigo-50/40 p-6 rounded-2xl border border-indigo-100 space-y-4 mt-8">
          <h4 class="text-sm font-bold text-indigo-850">🔍 Review Ringkasan Formulir</h4>
          <p class="text-[11px] text-slate-500 leading-normal">Silakan periksa kembali semua data yang telah Anda isi sebelum mengirim pendaftaran secara permanen.</p>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
            <div class="space-y-1.5">
              <p class="text-slate-500"><strong class="text-slate-700">Nama Lengkap:</strong> <span id="rev-name">-</span></p>
              <p class="text-slate-500"><strong class="text-slate-700">NIK / NISN:</strong> <span id="rev-nik-nisn">-</span></p>
              <p class="text-slate-500"><strong class="text-slate-700">Tempat, Tgl Lahir:</strong> <span id="rev-birth">-</span></p>
              <p class="text-slate-500"><strong class="text-slate-700">Jenis Kelamin:</strong> <span id="rev-gender">-</span></p>
              <p class="text-slate-500"><strong class="text-slate-700">Nomor HP / Email:</strong> <span id="rev-contact">-</span></p>
            </div>
            <div class="space-y-1.5">
              <p class="text-slate-500"><strong class="text-slate-700">Alamat Lengkap:</strong> <span id="rev-address">-</span></p>
              <p class="text-slate-500"><strong class="text-slate-700">Asal Sekolah:</strong> <span id="rev-school">-</span></p>
              <p class="text-slate-500"><strong class="text-slate-700">Nilai Rata-Rata:</strong> <span id="rev-score">-</span></p>
              <p class="text-slate-500"><strong class="text-slate-700">Pilihan Prodi 1:</strong> <span id="rev-prodi1">-</span></p>
              <p class="text-slate-500"><strong class="text-slate-700">Pilihan Prodi 2:</strong> <span id="rev-prodi2">-</span></p>
            </div>
          </div>
        </div>

        <!-- FINALIZATION STATEMENT CHECKBOX -->
        <div class="flex items-start gap-3 mt-6">
          <input type="checkbox" id="declaration" required class="mt-1 h-4 w-4 rounded text-indigo-650 border-slate-300 focus:ring-indigo-500 cursor-pointer">
          <label for="declaration" class="text-xs text-slate-650 leading-relaxed cursor-pointer select-none">
            Dengan ini saya menyatakan bahwa seluruh data yang saya isikan pada Formulir Pendaftaran ini adalah benar, lengkap, dan sesuai dengan dokumen asli yang sah. Saya bersedia menerima sanksi pembatalan kelulusan apabila terbukti melakukan pemalsuan data.
          </label>
        </div>
      </div>

      <!-- Action Buttons Footer -->
      <div class="flex justify-between items-center border-t border-slate-100 pt-6 mt-8">
        <button type="button" id="btn-back" onclick="navigateStep(-1)" class="hidden px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition-colors focus:outline-none">
          Sebelumnya
        </button>

        <button type="button" id="btn-draft" onclick="saveActiveStepDraft()" class="px-5 py-2.5 bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-600 font-bold rounded-xl text-xs transition-all focus:outline-none">
          Simpan Draft
        </button>

        <button type="button" id="btn-next" onclick="navigateStep(1)" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-xs transition-colors shadow-sm focus:outline-none">
          Selanjutnya
        </button>

        <button type="submit" id="btn-submit" class="hidden px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs transition-colors shadow-sm focus:outline-none">
          Kunci & Finalisasi Pendaftaran
        </button>
      </div>
    </form>
  </div>
</div>

<script>
  let currentStep = 1;
  const maxSteps = 6;

  function showStep(step) {
    document.querySelectorAll('.step-pane').forEach(pane => pane.classList.add('hidden'));
    document.getElementById(`step-content-${step}`).classList.remove('hidden');

    const stepTitles = [
      'Data Pribadi',
      'Alamat Lengkap',
      'Orang Tua & Wali',
      'Kebutuhan Khusus',
      'Riwayat Pendidikan',
      'Pilihan & Konfirmasi'
    ];
    const mobileTitle = document.getElementById('mobile-step-title');
    if (mobileTitle) {
      mobileTitle.textContent = `Langkah ${step}: ${stepTitles[step - 1]}`;
    }

    // Update stepper indicators styles
    for (let i = 1; i <= maxSteps; i++) {
      const ind = document.getElementById(`step-ind-${i}`);
      if (!ind) continue;
      const circle = ind.querySelector('div');
      const text = ind.querySelector('span');

      if (i < step) {
        // Completed step
        circle.className = "flex items-center justify-center w-9 h-9 rounded-full bg-emerald-600 text-white font-bold text-sm border-4 border-emerald-100 transition-all";
        circle.innerHTML = "✓";
        if (text) text.className = "text-[11px] font-bold text-emerald-600 step-label";
      } else if (i === step) {
        // Active step
        circle.className = "flex items-center justify-center w-9 h-9 rounded-full bg-indigo-600 text-white font-bold text-sm border-4 border-indigo-150 transition-all";
        circle.innerHTML = i;
        if (text) text.className = "text-[11px] font-bold text-indigo-600 step-label";
      } else {
        // Inactive step
        circle.className = "flex items-center justify-center w-9 h-9 rounded-full bg-slate-100 text-slate-400 font-bold text-sm border-4 border-transparent transition-all";
        circle.innerHTML = i;
        if (text) text.className = "text-[11px] font-semibold text-slate-400 step-label";
      }
    }

    // Toggle Back button visibility
    if (step === 1) {
      document.getElementById('btn-back').classList.add('hidden');
    } else {
      document.getElementById('btn-back').classList.remove('hidden');
    }

    // Toggle Next vs Submit button visibility
    if (step === maxSteps) {
      document.getElementById('btn-next').classList.add('hidden');
      document.getElementById('btn-submit').classList.remove('hidden');
      buildReviewSummary();
    } else {
      document.getElementById('btn-next').classList.remove('hidden');
      document.getElementById('btn-submit').classList.add('hidden');
    }

    // Hide error alert
    document.getElementById('step-error-alert').classList.add('hidden');
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  async function saveActiveStepDraft() {
    const form = document.getElementById('wizard-form');
    const formData = new FormData(form);
    formData.append('step', currentStep);

    try {
      const response = await fetch('/pendaftaran/save', {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
      });

      const res = await response.json();

      if (response.ok && res.success) {
        showTemporaryAlert('Draft pendaftaran berhasil disimpan', 'emerald');
        return true;
      } else {
        showErrorAlert(res.message || 'Gagal menyimpan draft');
        return false;
      }
    } catch (e) {
      showErrorAlert('Gagal tersambung ke server');
      return false;
    }
  }

  function validateStep(step) {
    document.querySelectorAll('.border-red-500').forEach(el => {
      el.classList.remove('border-red-500', 'ring-2', 'ring-red-150');
      el.classList.add('border-slate-200');
    });

    if (step === 1) {
      const fullName = document.getElementById('full_name');
      const birthPlace = document.getElementById('birth_place');
      const birthDate = document.getElementById('birth_date');
      const gender = document.getElementById('gender');
      const religion = document.getElementById('religion');
      const motherName = document.getElementById('mother_name');
      const infoSource = document.getElementById('info_source');

      if (!fullName.value.trim()) return markError(fullName, 'Nama Lengkap wajib diisi');
      if (!birthPlace.value.trim()) return markError(birthPlace, 'Tempat Lahir wajib diisi');
      if (!birthDate.value.trim()) return markError(birthDate, 'Tanggal Lahir wajib diisi');
      if (!/^\d{4}-\d{2}-\d{2}$/.test(birthDate.value.trim())) return markError(birthDate, 'Tanggal Lahir harus diisi dengan format yang valid');
      if (!gender.value) return markError(gender, 'Jenis Kelamin wajib dipilih');
      if (!religion.value) return markError(religion, 'Agama wajib dipilih');
      if (!motherName.value.trim()) return markError(motherName, 'Nama Ibu Kandung wajib diisi');
      if (!infoSource.value) return markError(infoSource, 'Info kampus wajib dipilih');
    }

    if (step === 2) {
      const citizenship = document.getElementById('citizenship');
      const nik = document.getElementById('nik');
      const nisn = document.getElementById('nisn');
      const hp = document.getElementById('hp');
      const telephone = document.getElementById('telephone');
      const subdistrict = document.getElementById('subdistrict');
      const email = document.getElementById('email');
      const kpsReceiver = document.getElementById('kps_receiver');
      const district = document.getElementById('district');
      const postalCode = document.getElementById('postal_code');

      if (!citizenship.value) return markError(citizenship, 'Kewarganegaraan wajib dipilih');
      if (!nik.value.trim()) return markError(nik, 'NIK wajib diisi');
      if (!/^\d{16}$/.test(nik.value.trim())) return markError(nik, 'NIK harus berupa 16 digit angka');
      if (!nisn.value.trim()) return markError(nisn, 'NISN wajib diisi');
      if (!/^\d{10}$/.test(nisn.value.trim())) return markError(nisn, 'NISN harus berupa 10 digit angka');
      if (!hp.value.trim()) return markError(hp, 'Nomor HP wajib diisi');
      if (!/^\d{9,15}$/.test(hp.value.trim())) return markError(hp, 'Nomor HP harus berupa 9-15 digit angka');
      if (telephone.value.trim() && !/^\d{9,15}$/.test(telephone.value.trim())) return markError(telephone, 'Nomor Telepon harus berupa angka');
      if (!subdistrict.value.trim()) return markError(subdistrict, 'Kelurahan wajib diisi');
      if (!email.value.trim()) return markError(email, 'Email wajib diisi');
      if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) return markError(email, 'Format email tidak valid');
      if (!kpsReceiver.value) return markError(kpsReceiver, 'Penerima KPS wajib dipilih');
      if (!district.value) return markError(district, 'Kecamatan wajib dipilih');
      if (postalCode.value.trim() && !/^\d{5}$/.test(postalCode.value.trim())) return markError(postalCode, 'Kode Pos harus berupa 5 digit angka');
    }

    if (step === 3) {
      const fatherName = document.getElementById('father_name');
      const fatherNik = document.getElementById('father_nik');
      const fatherBirthDate = document.getElementById('father_birth_date');
      const fatherEducation = document.getElementById('father_education');
      const fatherOccupation = document.getElementById('father_occupation');
      const fatherIncome = document.getElementById('father_income');

      const motherName = document.getElementById('mother_name_parent');
      const motherNik = document.getElementById('mother_nik');
      const motherBirthDate = document.getElementById('mother_birth_date');
      const motherEducation = document.getElementById('mother_education');
      const motherOccupation = document.getElementById('mother_occupation');
      const motherIncome = document.getElementById('mother_income');

      const guardianName = document.getElementById('guardian_name');
      const guardianBirthDate = document.getElementById('guardian_birth_date');
      const guardianEducation = document.getElementById('guardian_education');
      const guardianOccupation = document.getElementById('guardian_occupation');
      const guardianIncome = document.getElementById('guardian_income');

      const isParentFilled = fatherName.value.trim() !== '' || motherName.value.trim() !== '';

      if (isParentFilled) {
        if (!fatherName.value.trim()) return markError(fatherName, 'Nama Ayah wajib diisi');
        if (!fatherNik.value.trim()) return markError(fatherNik, 'NIK Ayah wajib diisi');
        if (!/^\d{16}$/.test(fatherNik.value.trim())) return markError(fatherNik, 'NIK Ayah harus berupa 16 digit angka');
        if (!fatherBirthDate.value.trim()) return markError(fatherBirthDate, 'Tanggal Lahir Ayah wajib diisi');
        if (!/^\d{4}-\d{2}-\d{2}$/.test(fatherBirthDate.value.trim())) return markError(fatherBirthDate, 'Tanggal Lahir Ayah harus diisi dengan format yang valid');
        if (!fatherEducation.value) return markError(fatherEducation, 'Pendidikan Ayah wajib dipilih');
        if (!fatherOccupation.value) return markError(fatherOccupation, 'Pekerjaan Ayah wajib dipilih');
        if (!fatherIncome.value) return markError(fatherIncome, 'Penghasilan Ayah wajib dipilih');

        if (!motherName.value.trim()) return markError(motherName, 'Nama Ibu wajib diisi');
        if (!motherNik.value.trim()) return markError(motherNik, 'NIK Ibu wajib diisi');
        if (!/^\d{16}$/.test(motherNik.value.trim())) return markError(motherNik, 'NIK Ibu harus berupa 16 digit angka');
        if (!motherBirthDate.value.trim()) return markError(motherBirthDate, 'Tanggal Lahir Ibu wajib diisi');
        if (!/^\d{4}-\d{2}-\d{2}$/.test(motherBirthDate.value.trim())) return markError(motherBirthDate, 'Tanggal Lahir Ibu harus diisi dengan format yang valid');
        if (!motherEducation.value) return markError(motherEducation, 'Pendidikan Ibu wajib dipilih');
        if (!motherOccupation.value) return markError(motherOccupation, 'Pekerjaan Ibu wajib dipilih');
        if (!motherIncome.value) return markError(motherIncome, 'Penghasilan Ibu wajib dipilih');
      } else {
        if (!guardianName.value.trim()) return markError(guardianName, 'Nama Wali wajib diisi (karena data Orang Tua dikosongkan)');
        if (!guardianBirthDate.value.trim()) return markError(guardianBirthDate, 'Tanggal Lahir Wali wajib diisi');
        if (!/^\d{4}-\d{2}-\d{2}$/.test(guardianBirthDate.value.trim())) return markError(guardianBirthDate, 'Tanggal Lahir Wali harus diisi dengan format yang valid');
        if (!guardianEducation.value) return markError(guardianEducation, 'Pendidikan Wali wajib dipilih');
        if (!guardianOccupation.value) return markError(guardianOccupation, 'Pekerjaan Wali wajib dipilih');
        if (!guardianIncome.value) return markError(guardianIncome, 'Penghasilan Wali wajib dipilih');
      }
    }

    if (step === 4) {
      const hasSpecialNeeds = document.getElementById('has_special_needs');
      if (!hasSpecialNeeds.value) return markError(hasSpecialNeeds, 'Pilihan Kebutuhan Khusus wajib dipilih');
    }

    if (step === 5) {
      const schoolName = document.getElementById('school_name');
      const schoolMajor = document.getElementById('school_major');
      const graduationYear = document.getElementById('graduation_year');
      const diplomaNumber = document.getElementById('diploma_number');
      const averageScore = document.getElementById('average_score');

      if (!schoolName.value.trim()) return markError(schoolName, 'Nama Sekolah/Instansi asal wajib diisi');
      if (!schoolMajor.value.trim()) return markError(schoolMajor, 'Jurusan/Peminatan wajib diisi');
      if (!graduationYear.value.trim()) return markError(graduationYear, 'Tahun Lulus wajib diisi');
      if (!/^\d{4}$/.test(graduationYear.value.trim())) return markError(graduationYear, 'Tahun Lulus harus berupa 4 digit angka');
      if (!diplomaNumber.value.trim()) return markError(diplomaNumber, 'Nomor Ijazah/SKL wajib diisi');
      if (!averageScore.value.trim()) return markError(averageScore, 'Rata-rata Nilai Rapor/Ijazah wajib diisi');
      const scoreNum = parseFloat(averageScore.value.trim());
      if (isNaN(scoreNum) || scoreNum < 0 || scoreNum > 100) return markError(averageScore, 'Rata-rata Nilai harus berupa angka antara 0 s/d 100');
    }

    if (step === 6) {
      const ay = document.getElementById('academic_year_id');
      const wave = document.getElementById('wave_id');
      const path = document.getElementById('admission_path_id');
      const clazz = document.getElementById('class_id');
      const prog1 = document.getElementById('program1_id');
      const prog2 = document.getElementById('program2_id');

      if (!ay.value) return markError(ay, 'Tahun Akademik wajib dipilih');
      if (!wave.value) return markError(wave, 'Gelombang Pendaftaran wajib dipilih');
      if (!path.value) return markError(path, 'Jalur Masuk wajib dipilih');
      if (!clazz.value) return markError(clazz, 'Pilihan Kelas wajib dipilih');
      if (!prog1.value) return markError(prog1, 'Pilihan Program Studi 1 wajib dipilih');
      if (prog1.value && prog2.value && prog1.value === prog2.value) {
        return markError(prog2, 'Pilihan program studi 1 dan program studi 2 tidak boleh sama');
      }
    }

    return true;
  }

  function markError(element, message) {
    element.classList.remove('border-slate-200');
    element.classList.add('border-red-500', 'ring-2', 'ring-red-150');
    element.focus();
    showErrorAlert(message);
    return false;
  }

  async function navigateStep(direction) {
    if (direction === 1) {
      if (!validateStep(currentStep)) return;
      const isSaved = await saveActiveStepDraft();
      if (!isSaved) return;
    }

    currentStep += direction;
    if (currentStep < 1) currentStep = 1;
    if (currentStep > maxSteps) currentStep = maxSteps;

    showStep(currentStep);
  }

  function showErrorAlert(message) {
    const alert = document.getElementById('step-error-alert');
    const msg = document.getElementById('error-alert-message');
    alert.classList.remove('hidden', 'bg-emerald-50', 'border-emerald-500', 'text-emerald-700');
    alert.classList.add('bg-red-50', 'border-red-500', 'text-red-700');
    msg.textContent = message;
    alert.scrollIntoView({ behavior: 'smooth', block: 'center' });
  }

  function showTemporaryAlert(message, color = 'emerald') {
    const alert = document.getElementById('step-error-alert');
    const msg = document.getElementById('error-alert-message');
    alert.classList.remove('hidden', 'bg-red-50', 'border-red-500', 'text-red-700');
    alert.classList.add('bg-emerald-50', 'border-emerald-500', 'text-emerald-700');
    msg.textContent = '✅ ' + message;
    
    setTimeout(() => {
      alert.classList.add('hidden');
    }, 3000);
  }

  function onDistrictChange(selectEl) {
    const selectedOption = selectEl.options[selectEl.selectedIndex];
    if (selectedOption) {
      const kabupaten = selectedOption.getAttribute('data-kabupaten') || '';
      const provinsi = selectedOption.getAttribute('data-provinsi') || '';
      document.getElementById('city').value = kabupaten;
      document.getElementById('province').value = provinsi;
    }
  }

  function toggleSpecialNeedsList(value) {
    const container = document.getElementById('special-needs-container');
    if (value === 'Ya') {
      container.classList.remove('hidden');
    } else {
      container.classList.add('hidden');
      container.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
    }
  }

  function setupDateMask(elementId) {
    const input = document.getElementById(elementId);
    if (!input) return;
    input.addEventListener('input', function() {
      let v = this.value.replace(/\D/g, '');
      if (v.length > 8) v = v.substring(0, 8);
      let r = '';
      if (v.length > 0) {
        r += v.substring(0, 2);
        if (v.length > 2) {
          r += '/' + v.substring(2, 4);
          if (v.length > 4) {
            r += '/' + v.substring(4, 8);
          }
        }
      }
      this.value = r;
    });
  }

  function formatDateToUi(dateStr) {
    if (!dateStr) return '-';
    const parts = dateStr.split('-');
    if (parts.length === 3) {
      return parts[2] + '/' + parts[1] + '/' + parts[0];
    }
    return dateStr;
  }

  function buildReviewSummary() {
    document.getElementById('rev-name').textContent = document.getElementById('full_name').value || '-';
    
    const nikVal = document.getElementById('nik').value || '-';
    const nisnVal = document.getElementById('nisn').value || '-';
    document.getElementById('rev-nik-nisn').textContent = `${nikVal} / ${nisnVal}`;
    
    const bDateVal = document.getElementById('birth_date').value;
    document.getElementById('rev-birth').textContent = (document.getElementById('birth_place').value || '-') + ', ' + formatDateToUi(bDateVal);
    
    const genderSel = document.getElementById('gender');
    document.getElementById('rev-gender').textContent = genderSel.options[genderSel.selectedIndex]?.text || '-';
    
    document.getElementById('rev-contact').textContent = (document.getElementById('hp').value || '-') + ' / ' + (document.getElementById('email').value || '-');
    
    const detailAddr = document.getElementById('address').value || '';
    const subdist = document.getElementById('subdistrict').value || '';
    const distSel = document.getElementById('district');
    const distText = distSel.options[distSel.selectedIndex]?.text || '';
    const postal = document.getElementById('postal_code').value || '';
    
    let fullAddr = detailAddr;
    if (subdist) fullAddr += `, Kel. ${subdist}`;
    if (distText && distSel.value) fullAddr += `, Kec. ${distText}`;
    if (postal) fullAddr += `, ${postal}`;
    
    document.getElementById('rev-address').textContent = fullAddr || '-';

    document.getElementById('rev-school').textContent = (document.getElementById('school_name').value || '-') + ' (' + (document.getElementById('school_major').value || '-') + ')';
    document.getElementById('rev-score').textContent = document.getElementById('average_score').value || '-';

    const p1 = document.getElementById('program1_id');
    document.getElementById('rev-prodi1').textContent = p1.options[p1.selectedIndex]?.text || '-';

    const p2 = document.getElementById('program2_id');
    document.getElementById('rev-prodi2').textContent = p2.options[p2.selectedIndex]?.value ? p2.options[p2.selectedIndex].text : 'Tidak Memilih';
  }

  const waveStudyPrograms = <?= json_encode($waveProdis) ?>;
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
    p2Select.innerHTML = '<option value="">Pilih Program Studi 2 (Kosongkan jika tidak memilih)</option>';

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

  waveSelect.addEventListener('change', () => {
    updateStudyPrograms();
    buildReviewSummary();
  });
  p1Select.addEventListener('change', () => {
    updateStudyPrograms();
    buildReviewSummary();
  });
  p2Select.addEventListener('change', buildReviewSummary);
  updateStudyPrograms();

  // Trigger initial UI state
  toggleSpecialNeedsList(document.getElementById('has_special_needs').value);
  
  // Initialize
  showStep(currentStep);
</script>
