<?php

/**
 * @var array $study_programs
 * @var array $waves
 */
$jsonData = json_decode(file_get_contents(MAZU_ENV_PATH . 'data.json'), true);
$agamaList = $jsonData['agama'][0] ?? [];
$wilayahList = $jsonData['wilayah'][0] ?? [];
$negaraList = $jsonData['kewarganegaraan'][0] ?? [];

$db = \App\Core\Foundation\Application::getInstance()->getContainer()->resolve(\App\Core\Database\DatabaseManager::class)->connection();
$stmt = $db->prepare("SELECT wave_id, study_program_id FROM wave_study_programs");
$stmt->execute();
$waveProdiList = $stmt->fetchAll() ?: [];
$waveProdis = [];
foreach ($waveProdiList as $wp) {
  $waveProdis[$wp['wave_id']][] = (int)$wp['study_program_id'];
}

$isAddressFilled = !empty($address['district']) && !empty($address['subdistrict']) && !empty($address['address']) && !empty($registration['email']);
$isParentsFilled = !empty($parents['father_name']) || !empty($parents['mother_name']) || !empty($parents['guardian_name']);
$isEducationFilled = !empty($education['school_name']) && !empty($education['school_major']) && !empty($education['graduation_year']) && !empty($education['diploma_number']) && !empty($education['school_address']);
$isProfileComplete = $isAddressFilled && $isParentsFilled && $isEducationFilled;

$selectedWave = null;
foreach ($waves as $w) {
  if ($w['id'] == ($registration['wave_id'] ?? null)) {
    $selectedWave = $w;
    break;
  }
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
  <?php if (!$selectedWave): ?>
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 p-8 max-w-md mx-auto text-center space-y-6">
      <div class="w-16 h-16 bg-amber-55 border border-amber-200 rounded-full flex items-center justify-center text-2xl mx-auto shadow-sm">⚠️</div>
      <div class="space-y-2">
        <h3 class="text-lg font-bold text-slate-800">Gelombang Belum Dipilih</h3>
        <p class="text-xs text-slate-500 leading-relaxed">Silakan pilih gelombang pendaftaran pada halaman Dashboard terlebih dahulu untuk mengisi formulir pendaftaran.</p>
      </div>
      <div class="pt-2">
        <a href="<?= getBaseUrl('/dashboard') ?>" class="inline-flex px-6 py-3 bg-indigo-600 text-white rounded-xl text-xs font-bold hover:bg-indigo-750 transition-all shadow-sm">Pilih Gelombang</a>
      </div>
    </div>
  <?php else: ?>
    <div class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-slate-200/80">
      <h2 class="text-xl font-extrabold text-slate-900 tracking-tight text-center sm:text-left">Formulir Pendaftaran Mahasiswa Baru</h2>
      <p class="mt-1 text-xs text-slate-500 text-center sm:text-left">Lengkapi formulir pendaftaran 3 langkah di bawah untuk mengajukan berkas PMB.</p>

      <div class="relative flex items-center justify-between gap-2 mt-8 max-w-xl mx-auto">
        <div class="absolute top-[18px] left-[5%] right-[5%] h-0.5 bg-slate-100 -z-1" id="stepper-line"></div>

        <div class="flex flex-col items-center text-center space-y-1.5 z-10 w-24" id="step-ind-1">
          <div class="flex items-center justify-center w-9 h-9 rounded-full bg-indigo-600 text-white font-bold text-sm border-4 border-indigo-150 transition-all cursor-default">1</div>
          <span class="text-[10px] md:text-[11px] font-bold text-indigo-600 step-label font-sans">Pilihan Prodi</span>
        </div>

        <div class="flex flex-col items-center text-center space-y-1.5 z-10 w-24" id="step-ind-2">
          <div class="flex items-center justify-center w-9 h-9 rounded-full bg-slate-100 text-slate-400 font-bold text-sm border-4 border-transparent transition-all cursor-default">2</div>
          <span class="text-[10px] md:text-[11px] font-semibold text-slate-400 step-label font-sans">Data Pribadi</span>
        </div>

        <div class="flex flex-col items-center text-center space-y-1.5 z-10 w-24" id="step-ind-3">
          <div class="flex items-center justify-center w-9 h-9 rounded-full bg-slate-100 text-slate-400 font-bold text-sm border-4 border-transparent transition-all cursor-default">3</div>
          <span class="text-[10px] md:text-[11px] font-semibold text-slate-400 step-label font-sans">Konfirmasi</span>
        </div>
      </div>
      <div class="block sm:hidden text-center text-xs font-extrabold text-indigo-600 mt-4" id="mobile-step-title">Langkah 1: Pilihan Prodi</div>
    </div>

    <div id="step-error-alert" class="hidden p-4 bg-red-50 border border-red-500 text-red-700 rounded-2xl flex items-center gap-3">
      <span>⚠️</span>
      <span class="text-sm font-semibold" id="error-alert-message"></span>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden p-6 md:p-8">
      <form id="wizard-form" method="POST" action="<?= getBaseUrl('/pendaftaran/submit') ?>" enctype="multipart/form-data" class="space-y-6">

        <div id="step-content-1" class="step-pane space-y-6">
          <h3 class="text-base font-bold text-slate-800 border-b border-slate-100 pb-3 flex items-center gap-2">
            <span>🛤️</span> Program Studi Pilihan
          </h3>

          <div class="grid grid-cols-1 gap-6">
            <div class="flex justify-between items-center bg-indigo-50/40 p-5 rounded-2xl border border-indigo-100/50">
              <div class="space-y-1">
                <span class="text-[10px] font-extrabold text-indigo-600 uppercase tracking-wider block">Gelombang Pendaftaran Aktif</span>
                <h4 class="text-sm font-extrabold text-slate-900"><?= htmlspecialchars($selectedWave['name']) ?></h4>
                <p class="text-xs text-slate-500">Tahun Akademik: <?= htmlspecialchars($selectedWave['academic_year']) ?> — Periode: <?= date('d M Y', strtotime($selectedWave['start_date'])) ?> s/d <?= date('d M Y', strtotime($selectedWave['end_date'])) ?></p>
              </div>
              <button type="button" onclick="confirmResetWave(event)" class="px-3.5 py-2 bg-white border border-indigo-200 text-indigo-700 hover:bg-indigo-50 font-bold rounded-xl text-[10px] uppercase tracking-wider shadow-sm transition-all cursor-pointer">
                🔄 Ganti
              </button>
            </div>
            <input type="hidden" name="wave_id" id="wave_id" value="<?= htmlspecialchars($selectedWave['id']) ?>">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
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
                  <option value="" <?= empty($program['program2_id']) ? 'selected' : '' ?>>Pilih Program Studi 2</option>
                  <?php foreach ($study_programs as $sp): ?>
                    <option value="<?= $sp['id'] ?>" <?= ($program['program2_id'] ?? '') == $sp['id'] ? 'selected' : '' ?>><?= htmlspecialchars($sp['name']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="space-y-1">
                <label for="program3_id" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Pilihan Program Studi 3 (Opsional)</label>
                <select id="program3_id" name="program3_id" class="block w-full px-3 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-medium">
                  <option value="" <?= empty($program['program3_id']) ? 'selected' : '' ?>>Pilih Program Studi 3</option>
                  <?php foreach ($study_programs as $sp): ?>
                    <option value="<?= $sp['id'] ?>" <?= ($program['program3_id'] ?? '') == $sp['id'] ? 'selected' : '' ?>><?= htmlspecialchars($sp['name']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
          </div>
        </div>

        <div id="step-content-2" class="step-pane space-y-6 hidden">
          <h3 class="text-base font-bold text-slate-800 border-b border-slate-100 pb-3 flex items-center gap-2">
            <span>👤</span> Data Pribadi Calon Mahasiswa
          </h3>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-1">
              <label for="full_name" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Nama Lengkap <span class="text-red-550">*</span></label>
              <input type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($registration['full_name'] ?? $_SESSION['auth.user_name'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-medium" placeholder="Nama sesuai ijazah">
            </div>

            <div class="space-y-1">
              <label for="nik" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">NIK / Citizen Number <span class="text-red-550">*</span></label>
              <input type="text" id="nik" name="nik" maxlength="16" value="<?= htmlspecialchars($registration['nik'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-medium" placeholder="16 digit NIK">
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-1">
              <label for="nisn" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">NISN <span class="text-red-550">*</span></label>
              <input type="text" id="nisn" name="nisn" maxlength="10" value="<?= htmlspecialchars($registration['nisn'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-medium" placeholder="10 digit NISN">
            </div>

            <div class="space-y-1">
              <label for="birth_place" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Tempat Lahir <span class="text-red-550">*</span></label>
              <input type="text" id="birth_place" name="birth_place" value="<?= htmlspecialchars($registration['birth_place'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-medium" placeholder="Kota lahir">
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-1">
              <label for="birth_date" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Tanggal Lahir <span class="text-red-550">*</span></label>
              <input type="date" id="birth_date" name="birth_date" value="<?= (!empty($registration['birth_date']) && $registration['birth_date'] !== '1970-01-01' && $registration['birth_date'] !== '01/01/1970') ? date('Y-m-d', strtotime(str_replace('/', '-', $registration['birth_date']))) : '' ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm text-sm bg-slate-50 font-medium">
            </div>

            <div class="space-y-1">
              <label for="gender" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Jenis Kelamin <span class="text-red-550">*</span></label>
              <select id="gender" name="gender" class="block w-full px-3 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-medium">
                <option value="" disabled <?= (empty($registration['birth_place']) || empty($registration['religion'])) ? 'selected' : '' ?>>Pilih Jenis Kelamin</option>
                <option value="Laki-laki" <?= (!empty($registration['birth_place']) && !empty($registration['religion']) && ($registration['gender'] ?? '') === 'Laki-laki') ? 'selected' : '' ?>>Laki-laki</option>
                <option value="Perempuan" <?= ($registration['gender'] ?? '') === 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
              </select>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
              <label for="hp" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">No. HP / WhatsApp <span class="text-red-550">*</span></label>
              <input type="text" id="hp" name="hp" value="<?= htmlspecialchars($registration['phone'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-medium" placeholder="08xxxxxxxxxx">
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-1">
              <label for="info_source" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Sumber Informasi <span class="text-red-550">*</span></label>
              <select id="info_source" name="info_source" class="block w-full px-3 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-medium">
                <option value="" disabled selected>Pilih Sumber Informasi</option>
                <option value="Media Sosial" <?= ($registration['info_source'] ?? '') === 'Media Sosial' ? 'selected' : '' ?>>Media Sosial</option>
                <option value="Website Kampus" <?= ($registration['info_source'] ?? '') === 'Website Kampus' ? 'selected' : '' ?>>Website Kampus</option>
                <option value="Brosur / Spanduk" <?= ($registration['info_source'] ?? '') === 'Brosur / Spanduk' ? 'selected' : '' ?>>Brosur / Spanduk</option>
                <option value="Teman / Keluarga" <?= ($registration['info_source'] ?? '') === 'Teman / Keluarga' ? 'selected' : '' ?>>Teman / Keluarga</option>
                <option value="Kunjungan Sekolah" <?= ($registration['info_source'] ?? '') === 'Kunjungan Sekolah' ? 'selected' : '' ?>>Kunjungan Sekolah</option>
                <option value="Lainnya" <?= ($registration['info_source'] ?? '') === 'Lainnya' ? 'selected' : '' ?>>Lainnya</option>
              </select>
            </div>

            <div class="space-y-1">
              <label for="citizenship" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Kewarganegaraan <span class="text-red-550">*</span></label>
              <select id="citizenship" name="citizenship" class="block w-full px-3 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-medium">
                <option value="" disabled <?= empty($address['citizenship']) ? 'selected' : '' ?>>Pilih Kewarganegaraan</option>
                <?php foreach ($negaraList as $neg): ?>
                  <option value="<?= htmlspecialchars($neg['nm_negara']) ?>" <?= ($address['citizenship'] ?? '') === $neg['nm_negara'] ? 'selected' : '' ?>><?= htmlspecialchars($neg['nm_negara']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>


          <div class="space-y-1">
            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Foto Resmi 3x4 (Background Merah/Biru) <span class="text-red-550">*</span></label>
            <div class="flex items-center gap-4 mt-1">
              <div class="h-16 w-12 rounded-lg border border-slate-200 bg-slate-50 flex items-center justify-center overflow-hidden relative shrink-0">
                <div id="photo-placeholder" class="absolute inset-0 flex items-center justify-center text-slate-300 <?= !empty($registration['photo_path']) ? 'hidden' : '' ?>">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                </div>
                <img id="photo-preview" src="<?= !empty($registration['photo_path']) ? htmlspecialchars($registration['photo_path']) : '' ?>" class="h-full w-full object-cover <?= empty($registration['photo_path']) ? 'hidden' : '' ?>">
              </div>
              <div class="space-y-1.5">
                <input type="file" id="photo" name="photo" accept="image/jpeg,image/png,image/jpg" onchange="previewPhoto(this)" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-750 hover:file:bg-indigo-100 cursor-pointer">
                <p class="text-[10px] text-slate-400 font-medium">* Format JPG/PNG, Maks. 2MB</p>
              </div>
            </div>
          </div>
        </div>

        <div id="step-content-3" class="step-pane space-y-6 hidden">
          <h3 class="text-base font-bold text-slate-800 border-b border-slate-100 pb-3 flex items-center gap-2">
            <span>📝</span> Konfirmasi & Submit Pendaftaran
          </h3>

          <div class="bg-amber-50 border border-amber-250 p-5 rounded-2xl space-y-3">
            <h4 class="text-xs font-extrabold text-amber-800 uppercase tracking-wider">⚠️ PERINGATAN PENTING</h4>
            <p class="text-xs text-amber-900 leading-relaxed font-semibold">Sebelum mengunci pendaftaran, pastikan Anda telah melengkapi seluruh data profil Anda di halaman <a href="<?= getBaseUrl('/profile') ?>" target="_blank" class="text-indigo-650 hover:underline"><strong>Profil Saya</strong></a> (termasuk Alamat Lengkap, Data Orang Tua/Wali, Kebutuhan Khusus, dan Riwayat Pendidikan). Data profil yang tidak lengkap dapat menyebabkan pendaftaran Anda ditolak.</p>

            <div class="bg-white/50 p-4 rounded-xl border border-amber-200/50 space-y-2 mt-2">
              <h5 class="text-[10px] font-extrabold text-slate-700 uppercase">Status Kelengkapan Profil Anda:</h5>
              <ul class="text-xs space-y-1 font-semibold">
                <li class="flex items-center gap-2">
                  <span><?= $isAddressFilled ? '✅' : '❌' ?></span>
                  <span class="<?= $isAddressFilled ? 'text-emerald-700' : 'text-red-700' ?>">Alamat Lengkap & Kontak (<?= $isAddressFilled ? 'Lengkap' : 'Belum Lengkap' ?>)</span>
                </li>
                <li class="flex items-center gap-2">
                  <span><?= $isParentsFilled ? '✅' : '❌' ?></span>
                  <span class="<?= $isParentsFilled ? 'text-emerald-700' : 'text-red-700' ?>">Data Orang Tua / Wali (<?= $isParentsFilled ? 'Lengkap' : 'Belum Lengkap' ?>)</span>
                </li>
                <li class="flex items-center gap-2">
                  <span><?= $isEducationFilled ? '✅' : '❌' ?></span>
                  <span class="<?= $isEducationFilled ? 'text-emerald-700' : 'text-red-700' ?>">Riwayat Pendidikan (<?= $isEducationFilled ? 'Lengkap' : 'Belum Lengkap' ?>)</span>
                </li>
              </ul>
            </div>
          </div>

          <div class="bg-slate-50 p-6 rounded-2xl border border-slate-150 space-y-4">
            <h4 class="text-xs font-extrabold text-slate-750 uppercase tracking-wider">🔍 Ringkasan Formulir</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs font-semibold">
              <div class="space-y-1 border-b border-slate-200/40 pb-2">
                <span class="text-slate-400 block uppercase text-[10px]">Nama Lengkap</span>
                <span id="rev-name" class="text-slate-800">-</span>
              </div>
              <div class="space-y-1 border-b border-slate-200/40 pb-2">
                <span class="text-slate-400 block uppercase text-[10px]">NIK / NISN</span>
                <span id="rev-nik-nisn" class="text-slate-800">-</span>
              </div>
              <div class="space-y-1 border-b border-slate-200/40 pb-2">
                <span class="text-slate-400 block uppercase text-[10px]">Tempat, Tanggal Lahir</span>
                <span id="rev-birth" class="text-slate-800">-</span>
              </div>
              <div class="space-y-1 border-b border-slate-200/40 pb-2">
                <span class="text-slate-400 block uppercase text-[10px]">Jenis Kelamin / Agama</span>
                <span id="rev-gender" class="text-slate-800">-</span>
              </div>
              <div class="space-y-1 border-b border-slate-200/40 pb-2 md:col-span-2">
                <span class="text-slate-400 block uppercase text-[10px]">Nomor HP / Email</span>
                <span id="rev-contact" class="text-slate-800">-</span>
              </div>
              <div class="space-y-1 border-b border-slate-200/40 pb-2">
                <span class="text-slate-400 block uppercase text-[10px]">Pilihan Program Studi 1</span>
                <span id="rev-prodi1" class="text-indigo-750 font-bold">-</span>
              </div>
              <div class="space-y-1 border-b border-slate-200/40 pb-2">
                <span class="text-slate-400 block uppercase text-[10px]">Pilihan Program Studi 2</span>
                <span id="rev-prodi2" class="text-slate-800">-</span>
              </div>
              <div class="space-y-1 border-b border-slate-200/40 pb-2">
                <span class="text-slate-400 block uppercase text-[10px]">Pilihan Program Studi 3</span>
                <span id="rev-prodi3" class="text-slate-800">-</span>
              </div>
            </div>
          </div>

          <div class="flex items-start gap-3 mt-6">
            <input type="checkbox" id="declaration" required class="mt-1 h-4 w-4 rounded text-indigo-650 border-slate-300 focus:ring-indigo-500 cursor-pointer">
            <label for="declaration" class="text-xs text-slate-650 leading-relaxed cursor-pointer select-none">
              Dengan ini saya menyatakan bahwa seluruh data yang saya isikan pada Formulir Pendaftaran ini serta seluruh data di halaman <strong>Profil Saya</strong> adalah benar, lengkap, dan sesuai dengan dokumen asli yang sah. Saya bersedia menerima sanksi pembatalan kelulusan apabila terbukti melakukan pemalsuan data.
            </label>
          </div>
        </div>

        <div class="flex justify-between items-center border-t border-slate-100 pt-6 mt-8">
          <button type="button" id="btn-back" onclick="navigateStep(-1)" class="hidden px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition-colors focus:outline-none">
            Sebelumnya
          </button>

          <div class="ml-auto flex items-center gap-3">
            <button type="button" id="btn-next" onclick="navigateStep(1)" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-xs transition-colors shadow-sm focus:outline-none">
              Selanjutnya
            </button>

            <?php if ($isProfileComplete): ?>
              <button type="submit" id="btn-submit" class="hidden px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs transition-colors shadow-sm focus:outline-none">
                Kunci & Finalisasi Pendaftaran
              </button>
            <?php else: ?>
              <a href="<?= getBaseUrl('/profile') ?>" id="btn-locked" class="hidden px-6 py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-xl text-xs transition-colors shadow-sm focus:outline-none text-center">
                Lengkapi Profil
              </a>
            <?php endif; ?>
          </div>
        </div>
      </form>
    </div>
  <?php endif; ?>
</div>

<script>
  (() => {
    function confirmResetWave(event) {
      event.preventDefault();
      Swal.fire({
        title: 'Ganti Gelombang?',
        text: 'Mengganti gelombang pendaftaran akan membatalkan pilihan program studi yang sudah Anda pilih. Apakah Anda yakin?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#4f46e5',
        cancelButtonColor: '#ef4444',
        confirmButtonText: 'Ya, Ganti',
        cancelButtonText: 'Batal',
        customClass: {
          popup: 'rounded-3xl',
          confirmButton: 'rounded-xl text-xs font-bold px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white',
          cancelButton: 'rounded-xl text-xs font-bold px-4 py-2 bg-red-600 hover:bg-red-700 text-white'
        }
      }).then((result) => {
        if (result.isConfirmed) {
          const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
          fetch('<?= getBaseUrl('/pendaftaran/reset-wave') ?>', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken || ''
              }
            })
            .then(res => res.json())
            .then(data => {
              if (data.success) {
                window.location.href = '<?= getBaseUrl('/dashboard') ?>';
              } else {
                Swal.fire('Error', data.message || 'Gagal mengganti gelombang', 'error');
              }
            })
            .catch(() => {
              Swal.fire('Error', 'Terjadi kesalahan sistem', 'error');
            });
        }
      });
    }

    let currentStep = <?= (int)($registration['current_step'] ?? 1) ?>;
    let isFirstLoad = true;
    const maxSteps = 3;

    function showStep(step) {
      document.querySelectorAll('.step-pane').forEach(pane => pane.classList.add('hidden'));
      document.getElementById(`step-content-${step}`).classList.remove('hidden');

      const stepTitles = [
        'Pilihan Prodi',
        'Data Pribadi',
        'Konfirmasi & Submit'
      ];
      const mobileTitle = document.getElementById('mobile-step-title');
      if (mobileTitle) {
        mobileTitle.textContent = `Langkah ${step}: ${stepTitles[step - 1]}`;
      }

      for (let i = 1; i <= maxSteps; i++) {
        const ind = document.getElementById(`step-ind-${i}`);
        if (!ind) continue;
        const circle = ind.querySelector('div');
        const text = ind.querySelector('span');

        if (i < step) {
          circle.className = "flex items-center justify-center w-9 h-9 rounded-full bg-emerald-600 text-white font-bold text-sm border-4 border-emerald-100 transition-all";
          circle.innerHTML = "✓";
          if (text) text.className = "text-[11px] font-bold text-emerald-600 step-label font-sans";
        } else if (i === step) {
          circle.className = "flex items-center justify-center w-9 h-9 rounded-full bg-indigo-600 text-white font-bold text-sm border-4 border-indigo-150 transition-all";
          circle.innerHTML = i;
          if (text) text.className = "text-[11px] font-bold text-indigo-600 step-label font-sans";
        } else {
          circle.className = "flex items-center justify-center w-9 h-9 rounded-full bg-slate-100 text-slate-400 font-bold text-sm border-4 border-transparent transition-all";
          circle.innerHTML = i;
          if (text) text.className = "text-[11px] font-semibold text-slate-400 step-label font-sans";
        }
      }

      if (step === 1) {
        document.getElementById('btn-back').classList.add('hidden');
      } else {
        document.getElementById('btn-back').classList.remove('hidden');
      }

      if (step === maxSteps) {
        document.getElementById('btn-next').classList.add('hidden');
        const submitBtn = document.getElementById('btn-submit');
        const lockedBtn = document.getElementById('btn-locked');
        if (submitBtn) submitBtn.classList.remove('hidden');
        if (lockedBtn) lockedBtn.classList.remove('hidden');
        buildReviewSummary();
      } else {
        document.getElementById('btn-next').classList.remove('hidden');
        const submitBtn = document.getElementById('btn-submit');
        const lockedBtn = document.getElementById('btn-locked');
        if (submitBtn) submitBtn.classList.add('hidden');
        if (lockedBtn) lockedBtn.classList.add('hidden');
      }

      document.getElementById('step-error-alert').classList.add('hidden');
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      });
      updateActiveStepOnServer(step);

      if (window.spa && typeof window.spa.clearCache === 'function') {
        window.spa.clearCache();
      }
    }

    async function updateActiveStepOnServer(stepNum) {
      if (isFirstLoad) {
        isFirstLoad = false;
        return;
      }
      try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const formData = new FormData();
        formData.append('current_step', stepNum);
        await fetch('<?= getBaseUrl('/pendaftaran/step') ?>', {
          method: 'POST',
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken || ''
          },
          body: formData
        });
      } catch (e) {}
    }

    async function saveActiveStepDraft(targetStep = null) {
      const form = document.getElementById('wizard-form');
      const formData = new FormData(form);
      formData.append('step', currentStep);
      if (targetStep !== null) {
        formData.append('current_step', targetStep);
      }

      try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const response = await fetch('<?= getBaseUrl('/pendaftaran/save') ?>', {
          method: 'POST',
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken || ''
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
        showErrorAlert('Gagal tersambung ke server: ' + e.message);
        return false;
      }
    }

    async function navigateStep(direction) {
      const targetStep = currentStep + direction;
      if (targetStep < 1 || targetStep > maxSteps) return;

      if (direction > 0) {
        if (!validateStep(currentStep)) return;
        const saved = await saveActiveStepDraft(targetStep);
        if (!saved) return;
      }

      currentStep = targetStep;
      showStep(currentStep);
    }

    function validateStep(step) {
      document.getElementById('step-error-alert').classList.add('hidden');

      if (step === 1) {
        const w = document.getElementById('wave_id').value;
        const p1 = document.getElementById('program1_id').value;
        const p2 = document.getElementById('program2_id').value;
        const p3 = document.getElementById('program3_id').value;

        if (!w || !p1) {
          showErrorAlert('Harap lengkapi semua pilihan prodi wajib');
          return false;
        }

        const selected = [p1, p2, p3].filter(v => v !== "");
        const unique = [...new Set(selected)];
        if (selected.length !== unique.length) {
          showErrorAlert('Pilihan program studi tidak boleh ada yang sama');
          return false;
        }
      }

      if (step === 2) {
        const name = document.getElementById('full_name').value.trim();
        const nik = document.getElementById('nik').value.trim();
        const nisn = document.getElementById('nisn').value.trim();
        const place = document.getElementById('birth_place').value.trim();
        const date = document.getElementById('birth_date').value;
        const religion = document.getElementById('religion').value;
        const gender = document.getElementById('gender').value;
        const hp = document.getElementById('hp').value.trim();
        const info = document.getElementById('info_source').value;
        const citizenship = document.getElementById('citizenship').value;
        if (!name || !nik || !nisn || !place || !date || !gender || !religion || !hp || !info || !citizenship) {
          showErrorAlert('Harap lengkapi semua data pribadi wajib');
          return false;
        }

        if (nik.length !== 16 || isNaN(nik)) {
          showErrorAlert('NIK harus berupa 16 digit angka');
          return false;
        }

        if (nisn.length !== 10 || isNaN(nisn)) {
          showErrorAlert('NISN harus berupa 10 digit angka');
          return false;
        }

        if (hp.length < 9 || hp.length > 15 || isNaN(hp)) {
          showErrorAlert('Nomor HP harus berupa angka dengan panjang 9-15 digit');
          return false;
        }
      }

      return true;
    }

    function showErrorAlert(msg) {
      Swal.fire({
        icon: 'error',
        title: 'Perhatian',
        text: msg,
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 4000,
        timerProgressBar: true
      });
    }

    function showTemporaryAlert(msg, type = 'emerald') {
      Swal.fire({
        icon: type === 'emerald' ? 'success' : 'error',
        title: type === 'emerald' ? 'Berhasil' : 'Perhatian',
        text: msg,
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
      });
    }

    function previewPhoto(input) {
      if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
          document.getElementById('photo-preview').src = e.target.result;
          document.getElementById('photo-preview').classList.remove('hidden');
          document.getElementById('photo-placeholder').classList.add('hidden');
        }
        reader.readAsDataURL(input.files[0]);
      }
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
      document.getElementById('rev-gender').textContent = (genderSel.options[genderSel.selectedIndex]?.text || '-') + ' / ' + document.getElementById('religion').value;

      document.getElementById('rev-contact').textContent = (document.getElementById('hp').value || '-') + ' / ' + '<?= htmlspecialchars($registration['email'] ?? $_SESSION['auth.user_email'] ?? '') ?>';

      const p1 = document.getElementById('program1_id');
      document.getElementById('rev-prodi1').textContent = p1.options[p1.selectedIndex]?.text || '-';

      const p2 = document.getElementById('program2_id');
      document.getElementById('rev-prodi2').textContent = p2.options[p2.selectedIndex]?.value ? p2.options[p2.selectedIndex].text : 'Tidak Memilih';

      const p3 = document.getElementById('program3_id');
      document.getElementById('rev-prodi3').textContent = p3.options[p3.selectedIndex]?.value ? p3.options[p3.selectedIndex].text : 'Tidak Memilih';
    }

    const waveStudyPrograms = <?= json_encode($waveProdis) ?>;
    const allStudyPrograms = <?= json_encode($study_programs) ?>;
    const waveSelect = document.getElementById('wave_id');
    const p1Select = document.getElementById('program1_id');
    const p2Select = document.getElementById('program2_id');
    const p3Select = document.getElementById('program3_id');

    function updateStudyPrograms() {
      const selectedWaveId = waveSelect ? waveSelect.value : '';
      const allowedProdiIds = waveStudyPrograms[selectedWaveId] || [];

      const currentP1 = p1Select.value;
      const currentP2 = p2Select.value;
      const currentP3 = p3Select.value;

      p1Select.innerHTML = '<option value="">Pilih Program Studi 1</option>';
      p2Select.innerHTML = '<option value="">Pilih Program Studi 2</option>';
      p3Select.innerHTML = '<option value="">Pilih Program Studi 3</option>';

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

          if (sp.id != currentP1 && sp.id != currentP2) {
            const opt3 = document.createElement('option');
            opt3.value = sp.id;
            opt3.textContent = sp.name;
            if (sp.id == currentP3) opt3.selected = true;
            p3Select.appendChild(opt3);
          }
        }
      });

      if (p1Select.value && !allowedProdiIds.includes(parseInt(p1Select.value))) {
        p1Select.value = "";
      }
      if (p2Select.value && (!allowedProdiIds.includes(parseInt(p2Select.value)) || p2Select.value == p1Select.value)) {
        p2Select.value = "";
      }
      if (p3Select.value && (!allowedProdiIds.includes(parseInt(p3Select.value)) || p3Select.value == p1Select.value || p3Select.value == p2Select.value)) {
        p3Select.value = "";
      }
    }

    function onWaveChange(selectEl, isUserChange = false) {
      updateStudyPrograms();
      if (isUserChange) {
        p1Select.value = "";
        p2Select.value = "";
        p3Select.value = "";
        const allowedProdiIds = waveStudyPrograms[selectEl.value] || [];
        if (allowedProdiIds.length > 0) {
          p1Select.value = allowedProdiIds[0];
        }
        updateStudyPrograms();
      }
      buildReviewSummary();
    }

    if (waveSelect) {
      if (p1Select) {
        p1Select.addEventListener('change', () => {
          updateStudyPrograms();
          buildReviewSummary();
        });
      }
      if (p2Select) {
        p2Select.addEventListener('change', () => {
          updateStudyPrograms();
          buildReviewSummary();
        });
      }
      if (p3Select) {
        p3Select.addEventListener('change', buildReviewSummary);
      }

      if (waveSelect.value) {
        onWaveChange(waveSelect);
      } else {
        updateStudyPrograms();
      }

      showStep(currentStep);

      document.getElementById('wizard-form').addEventListener('submit', function(e) {
        if (!validateStep(1) || !validateStep(2)) {
          e.preventDefault();
          return false;
        }
        const decl = document.getElementById('declaration');
        if (decl && !decl.checked) {
          e.preventDefault();
          showErrorAlert('Harap centang pernyataan keabsahan data terlebih dahulu.');
          return false;
        }
      });

      document.getElementById('wizard-form').addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
          e.preventDefault();
          return false;
        }
      });
    }

    window.confirmResetWave = confirmResetWave;
    window.previewPhoto = previewPhoto;
    window.navigateStep = navigateStep;
  })();
</script>