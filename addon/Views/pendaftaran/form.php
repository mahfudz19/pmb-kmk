<div class="w-full py-2 space-y-8">
  <!-- Top Stepper Header -->
  <div class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-slate-200/80">
    <h2 class="text-xl font-extrabold text-slate-900 tracking-tight text-center sm:text-left">Formulir Pendaftaran Mahasiswa Baru</h2>
    <p class="mt-1 text-xs text-slate-500 text-center sm:text-left">Lengkapi formulir pendaftaran 5 langkah di bawah untuk mendaftar kuliah.</p>
    
    <!-- Stepper Navigation -->
    <div class="relative flex items-center justify-between gap-2 mt-8 max-w-xl mx-auto">
      <div class="absolute top-[18px] left-[5%] right-[5%] h-0.5 bg-slate-100 -z-1" id="stepper-line"></div>
      
      <!-- Step 1 Indicator -->
      <div class="flex flex-col items-center text-center space-y-1.5 z-10 w-16" id="step-ind-1">
        <div class="flex items-center justify-center w-9 h-9 rounded-full bg-indigo-600 text-white font-bold text-sm border-4 border-indigo-150 transition-all cursor-default">1</div>
        <span class="text-[10px] md:text-[11px] font-bold text-indigo-600 hidden sm:inline-block">Data Pribadi</span>
      </div>

      <!-- Step 2 Indicator -->
      <div class="flex flex-col items-center text-center space-y-1.5 z-10 w-16" id="step-ind-2">
        <div class="flex items-center justify-center w-9 h-9 rounded-full bg-slate-100 text-slate-400 font-bold text-sm border-4 border-transparent transition-all cursor-default">2</div>
        <span class="text-[10px] md:text-[11px] font-semibold text-slate-400 hidden sm:inline-block">Alamat</span>
      </div>

      <!-- Step 3 Indicator -->
      <div class="flex flex-col items-center text-center space-y-1.5 z-10 w-16" id="step-ind-3">
        <div class="flex items-center justify-center w-9 h-9 rounded-full bg-slate-100 text-slate-400 font-bold text-sm border-4 border-transparent transition-all cursor-default">3</div>
        <span class="text-[10px] md:text-[11px] font-semibold text-slate-400 hidden sm:inline-block">Orang Tua</span>
      </div>

      <!-- Step 4 Indicator -->
      <div class="flex flex-col items-center text-center space-y-1.5 z-10 w-16" id="step-ind-4">
        <div class="flex items-center justify-center w-9 h-9 rounded-full bg-slate-100 text-slate-400 font-bold text-sm border-4 border-transparent transition-all cursor-default">4</div>
        <span class="text-[10px] md:text-[11px] font-semibold text-slate-400 hidden sm:inline-block">Pendidikan</span>
      </div>

      <!-- Step 5 Indicator -->
      <div class="flex flex-col items-center text-center space-y-1.5 z-10 w-16" id="step-ind-5">
        <div class="flex items-center justify-center w-9 h-9 rounded-full bg-slate-100 text-slate-400 font-bold text-sm border-4 border-transparent transition-all cursor-default">5</div>
        <span class="text-[10px] md:text-[11px] font-semibold text-slate-400 hidden sm:inline-block">Pilihan & Submit</span>
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
            <label for="full_name" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Nama Lengkap</label>
            <input type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($registration['full_name'] ?? $_SESSION['auth.user_name'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-medium" placeholder="Nama sesuai ijazah">
          </div>
          
          <div class="space-y-1">
            <label for="nik" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">NIK (No. KTP)</label>
            <input type="text" id="nik" name="nik" maxlength="16" value="<?= htmlspecialchars($registration['nik'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-medium" placeholder="16 digit NIK">
          </div>

          <div class="space-y-1">
            <label for="nisn" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">NISN</label>
            <input type="text" id="nisn" name="nisn" maxlength="10" value="<?= htmlspecialchars($registration['nisn'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-medium" placeholder="10 digit NISN">
          </div>

          <div class="space-y-1">
            <label for="birth_place" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Tempat Lahir</label>
            <input type="text" id="birth_place" name="birth_place" value="<?= htmlspecialchars($registration['birth_place'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-medium" placeholder="Kota lahir">
          </div>

          <div class="space-y-1">
            <label for="birth_date" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Tanggal Lahir</label>
            <input type="date" id="birth_date" name="birth_date" value="<?= htmlspecialchars($registration['birth_date'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm text-sm bg-slate-50 font-medium">
          </div>

          <div class="space-y-1">
            <label for="gender" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Jenis Kelamin</label>
            <select id="gender" name="gender" class="block w-full px-3 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-medium">
              <option value="" disabled <?= empty($registration['gender']) ? 'selected' : '' ?>>Pilih jenis kelamin</option>
              <option value="Laki-laki" <?= ($registration['gender'] ?? '') === 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
              <option value="Perempuan" <?= ($registration['gender'] ?? '') === 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
            </select>
          </div>

          <div class="space-y-1">
            <label for="religion" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Agama</label>
            <select id="religion" name="religion" class="block w-full px-3 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-medium">
              <option value="" disabled <?= empty($registration['religion']) ? 'selected' : '' ?>>Pilih Agama</option>
              <?php foreach (['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'] as $r): ?>
                <option value="<?= $r ?>" <?= ($registration['religion'] ?? '') === $r ? 'selected' : '' ?>><?= $r ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="space-y-1">
            <label for="email" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Email Aktif</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($registration['email'] ?? $_SESSION['auth.user_email'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-medium" placeholder="nama@email.com">
          </div>

          <div class="space-y-1">
            <label for="phone" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">No. HP / WhatsApp</label>
            <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($registration['phone'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-medium" placeholder="08xxxxxxxxxx">
          </div>
        </div>
      </div>

      <!-- STEP 2: DATA ALAMAT -->
      <div id="step-content-2" class="step-pane space-y-6 hidden">
        <h3 class="text-base font-bold text-slate-800 border-b border-slate-100 pb-3 flex items-center gap-2">
          <span>📍</span> Alamat Tinggal & Domisili
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="space-y-1">
            <label for="province" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Provinsi</label>
            <input type="text" id="province" name="province" value="<?= htmlspecialchars($address['province'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-medium" placeholder="Contoh: Jawa Barat">
          </div>

          <div class="space-y-1">
            <label for="city" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Kabupaten / Kota</label>
            <input type="text" id="city" name="city" value="<?= htmlspecialchars($address['city'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-medium" placeholder="Contoh: Kota Bandung">
          </div>

          <div class="space-y-1">
            <label for="district" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Kecamatan</label>
            <input type="text" id="district" name="district" value="<?= htmlspecialchars($address['district'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-medium" placeholder="Kecamatan tinggal">
          </div>

          <div class="space-y-1">
            <label for="subdistrict" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Kelurahan / Desa</label>
            <input type="text" id="subdistrict" name="subdistrict" value="<?= htmlspecialchars($address['subdistrict'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-medium" placeholder="Kelurahan tinggal">
          </div>

          <div class="space-y-1">
            <label for="postal_code" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Kode Pos</label>
            <input type="text" id="postal_code" name="postal_code" maxlength="5" value="<?= htmlspecialchars($address['postal_code'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-medium" placeholder="5 digit kode pos">
          </div>
        </div>

        <div class="space-y-1">
          <label for="address" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Alamat Lengkap</label>
          <textarea id="address" name="address" rows="3" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-medium" placeholder="Nama Jalan, No. Rumah, RT/RW, Dusun/Kampung"><?= htmlspecialchars($address['address'] ?? '') ?></textarea>
        </div>
      </div>

      <!-- STEP 3: DATA ORANG TUA / WALI -->
      <div id="step-content-3" class="step-pane space-y-6 hidden">
        <h3 class="text-base font-bold text-slate-800 border-b border-slate-100 pb-3 flex items-center gap-2">
          <span>👨‍👩‍👦</span> Data Orang Tua / Wali
        </h3>
        
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
                <label for="father_education" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Pendidikan Terakhir</label>
                <select id="father_education" name="father_education" class="block w-full px-2.5 py-2.5 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 text-xs bg-white font-medium">
                  <option value="" disabled <?= empty($parents['father_education']) ? 'selected' : '' ?>>Pilih Pendidikan</option>
                  <?php foreach (['SD', 'SMP', 'SMA/SMK', 'Diploma', 'S1', 'S2', 'S3'] as $edu): ?>
                    <option value="<?= $edu ?>" <?= ($parents['father_education'] ?? '') === $edu ? 'selected' : '' ?>><?= $edu ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="space-y-1">
                <label for="father_occupation" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Pekerjaan</label>
                <input type="text" id="father_occupation" name="father_occupation" value="<?= htmlspecialchars($parents['father_occupation'] ?? '') ?>" class="appearance-none block w-full px-3 py-2.5 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 text-xs bg-white font-medium" placeholder="Pekerjaan saat ini">
              </div>

              <div class="space-y-1">
                <label for="father_income" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Penghasilan Bulanan</label>
                <select id="father_income" name="father_income" class="block w-full px-2.5 py-2.5 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 text-xs bg-white font-medium">
                  <option value="" disabled <?= empty($parents['father_income']) ? 'selected' : '' ?>>Pilih Penghasilan</option>
                  <?php foreach (['< Rp 1.000.000', 'Rp 1.000.000 - Rp 3.000.000', 'Rp 3.000.000 - Rp 5.000.000', 'Rp 5.000.000 - Rp 10.000.000', '> Rp 10.000.000'] as $inc): ?>
                    <option value="<?= $inc ?>" <?= ($parents['father_income'] ?? '') === $inc ? 'selected' : '' ?>><?= $inc ?></option>
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
                <label for="mother_name" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Nama Lengkap Ibu</label>
                <input type="text" id="mother_name" name="mother_name" value="<?= htmlspecialchars($parents['mother_name'] ?? '') ?>" class="appearance-none block w-full px-3 py-2.5 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 text-xs bg-white font-medium">
              </div>

              <div class="space-y-1">
                <label for="mother_education" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Pendidikan Terakhir</label>
                <select id="mother_education" name="mother_education" class="block w-full px-2.5 py-2.5 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 text-xs bg-white font-medium">
                  <option value="" disabled <?= empty($parents['mother_education']) ? 'selected' : '' ?>>Pilih Pendidikan</option>
                  <?php foreach (['SD', 'SMP', 'SMA/SMK', 'Diploma', 'S1', 'S2', 'S3'] as $edu): ?>
                    <option value="<?= $edu ?>" <?= ($parents['mother_education'] ?? '') === $edu ? 'selected' : '' ?>><?= $edu ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="space-y-1">
                <label for="mother_occupation" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Pekerjaan</label>
                <input type="text" id="mother_occupation" name="mother_occupation" value="<?= htmlspecialchars($parents['mother_occupation'] ?? '') ?>" class="appearance-none block w-full px-3 py-2.5 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 text-xs bg-white font-medium" placeholder="Pekerjaan saat ini">
              </div>

              <div class="space-y-1">
                <label for="mother_income" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Penghasilan Bulanan</label>
                <select id="mother_income" name="mother_income" class="block w-full px-2.5 py-2.5 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 text-xs bg-white font-medium">
                  <option value="" disabled <?= empty($parents['mother_income']) ? 'selected' : '' ?>>Pilih Penghasilan</option>
                  <?php foreach (['< Rp 1.000.000', 'Rp 1.000.000 - Rp 3.000.000', 'Rp 3.000.000 - Rp 5.000.000', 'Rp 5.000.000 - Rp 10.000.000', '> Rp 10.000.000'] as $inc): ?>
                    <option value="<?= $inc ?>" <?= ($parents['mother_income'] ?? '') === $inc ? 'selected' : '' ?>><?= $inc ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
          </div>
        </div>

        <!-- Wali Card (Opsional) -->
        <div class="bg-slate-50/50 p-6 rounded-2xl border border-slate-150 space-y-4">
          <h4 class="text-sm font-bold text-slate-700 flex items-center gap-2">👤 Data Wali (Opsional)</h4>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-1">
              <label for="guardian_name" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Nama Lengkap Wali</label>
              <input type="text" id="guardian_name" name="guardian_name" value="<?= htmlspecialchars($parents['guardian_name'] ?? '') ?>" class="appearance-none block w-full px-3 py-2.5 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 text-xs bg-white font-medium">
            </div>

            <div class="space-y-1">
              <label for="guardian_education" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Pendidikan Terakhir</label>
              <select id="guardian_education" name="guardian_education" class="block w-full px-2.5 py-2.5 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 text-xs bg-white font-medium">
                <option value="" <?= empty($parents['guardian_education']) ? 'selected' : '' ?>>Pilih Pendidikan (Kosongkan jika tidak ada)</option>
                <?php foreach (['SD', 'SMP', 'SMA/SMK', 'Diploma', 'S1', 'S2', 'S3'] as $edu): ?>
                  <option value="<?= $edu ?>" <?= ($parents['guardian_education'] ?? '') === $edu ? 'selected' : '' ?>><?= $edu ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="space-y-1">
              <label for="guardian_occupation" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Pekerjaan</label>
              <input type="text" id="guardian_occupation" name="guardian_occupation" value="<?= htmlspecialchars($parents['guardian_occupation'] ?? '') ?>" class="appearance-none block w-full px-3 py-2.5 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 text-xs bg-white font-medium">
            </div>

            <div class="space-y-1">
              <label for="guardian_income" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Penghasilan Bulanan</label>
              <select id="guardian_income" name="guardian_income" class="block w-full px-2.5 py-2.5 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 text-xs bg-white font-medium">
                <option value="" <?= empty($parents['guardian_income']) ? 'selected' : '' ?>>Pilih Penghasilan (Kosongkan jika tidak ada)</option>
                <?php foreach (['< Rp 1.000.000', 'Rp 1.000.000 - Rp 3.000.000', 'Rp 3.000.000 - Rp 5.000.000', 'Rp 5.000.000 - Rp 10.000.000', '> Rp 10.000.000'] as $inc): ?>
                  <option value="<?= $inc ?>" <?= ($parents['guardian_income'] ?? '') === $inc ? 'selected' : '' ?>><?= $inc ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
        </div>
      </div>

      <!-- STEP 4: RIWAYAT PENDIDIKAN -->
      <div id="step-content-4" class="step-pane space-y-6 hidden">
        <h3 class="text-base font-bold text-slate-800 border-b border-slate-100 pb-3 flex items-center gap-2">
          <span>🏫</span> Asal Sekolah & Pendidikan Terakhir
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="space-y-1">
            <label for="school_name" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Nama Sekolah Asal (SMA/SMK/MA)</label>
            <input type="text" id="school_name" name="school_name" value="<?= htmlspecialchars($education['school_name'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-medium" placeholder="Nama sekolah asal">
          </div>

          <div class="space-y-1">
            <label for="school_major" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Jurusan Sekolah</label>
            <input type="text" id="school_major" name="school_major" value="<?= htmlspecialchars($education['school_major'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-medium" placeholder="Contoh: IPA, IPS, Teknik Mesin">
          </div>

          <div class="space-y-1">
            <label for="graduation_year" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Tahun Lulus</label>
            <input type="text" id="graduation_year" name="graduation_year" maxlength="4" value="<?= htmlspecialchars($education['graduation_year'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-medium" placeholder="Contoh: 2026">
          </div>

          <div class="space-y-1">
            <label for="diploma_number" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Nomor Ijazah / SKL</label>
            <input type="text" id="diploma_number" name="diploma_number" value="<?= htmlspecialchars($education['diploma_number'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-medium" placeholder="Isi '-' jika belum terbit">
          </div>

          <div class="space-y-1">
            <label for="average_score" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Nilai Rata-Rata Ujian / Rapor</label>
            <input type="number" step="0.01" min="0" max="100" id="average_score" name="average_score" value="<?= htmlspecialchars($education['average_score'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-medium" placeholder="Contoh: 85.50">
          </div>
        </div>
      </div>

      <!-- STEP 5: PILIHAN PROGRAM STUDI & SUBMIT -->
      <div id="step-content-5" class="step-pane space-y-6 hidden">
        <h3 class="text-base font-bold text-slate-800 border-b border-slate-100 pb-3 flex items-center gap-2">
          <span>🛤️</span> Jalur & Program Studi Pilihan
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="space-y-1">
            <label for="academic_year_id" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Tahun Akademik</label>
            <select id="academic_year_id" name="academic_year_id" class="block w-full px-3 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-medium">
              <option value="" disabled <?= empty($registration['academic_year_id']) ? 'selected' : '' ?>>Pilih Tahun Akademik</option>
              <?php foreach ($academic_years as $ay): ?>
                <option value="<?= $ay['id'] ?>" <?= ($registration['academic_year_id'] ?? '') == $ay['id'] ? 'selected' : '' ?>><?= htmlspecialchars($ay['year']) ?> <?= $ay['is_active'] ? '(Aktif)' : '' ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="space-y-1">
            <label for="wave_id" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Gelombang</label>
            <select id="wave_id" name="wave_id" class="block w-full px-3 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-medium">
              <option value="" disabled <?= empty($registration['wave_id']) ? 'selected' : '' ?>>Pilih Gelombang</option>
              <?php foreach ($waves as $w): ?>
                <option value="<?= $w['id'] ?>" <?= ($registration['wave_id'] ?? '') == $w['id'] ? 'selected' : '' ?>><?= htmlspecialchars($w['name']) ?> (<?= htmlspecialchars($w['start_date']) ?> s.d <?= htmlspecialchars($w['end_date']) ?>)</option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="space-y-1">
            <label for="admission_path_id" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Jalur Masuk</label>
            <select id="admission_path_id" name="admission_path_id" class="block w-full px-3 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-medium">
              <option value="" disabled <?= empty($registration['admission_path_id']) ? 'selected' : '' ?>>Pilih Jalur</option>
              <?php foreach ($admission_paths as $ap): ?>
                <option value="<?= $ap['id'] ?>" <?= ($registration['admission_path_id'] ?? '') == $ap['id'] ? 'selected' : '' ?>><?= htmlspecialchars($ap['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="space-y-1">
            <label for="class_id" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Jenis Kelas</label>
            <select id="class_id" name="class_id" class="block w-full px-3 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-medium">
              <option value="" disabled <?= empty($registration['class_id']) ? 'selected' : '' ?>>Pilih Kelas</option>
              <?php foreach ($classes as $c): ?>
                <option value="<?= $c['id'] ?>" <?= ($registration['class_id'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="space-y-1">
            <label for="program1_id" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Pilihan Program Studi 1</label>
            <select id="program1_id" name="program1_id" class="block w-full px-3 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-medium">
              <option value="" disabled <?= empty($program['program1_id']) ? 'selected' : '' ?>>Pilih Program Studi 1</option>
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
  const maxSteps = 5;

  function showStep(step) {
    document.querySelectorAll('.step-pane').forEach(pane => pane.classList.add('hidden'));
    document.getElementById(`step-content-${step}`).classList.remove('hidden');

    const stepTitles = [
      'Data Pribadi',
      'Alamat Lengkap',
      'Data Orang Tua',
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
      const circle = ind.querySelector('div');
      const text = ind.querySelector('span');

      if (i < step) {
        // Completed step
        circle.className = "flex items-center justify-center w-9 h-9 rounded-full bg-emerald-600 text-white font-bold text-sm border-4 border-emerald-100 transition-all";
        circle.innerHTML = "✓";
        text.className = "text-[11px] font-bold text-emerald-600";
      } else if (i === step) {
        // Active step
        circle.className = "flex items-center justify-center w-9 h-9 rounded-full bg-indigo-600 text-white font-bold text-sm border-4 border-indigo-150 transition-all";
        circle.innerHTML = i;
        text.className = "text-[11px] font-bold text-indigo-600";
      } else {
        // Inactive step
        circle.className = "flex items-center justify-center w-9 h-9 rounded-full bg-slate-100 text-slate-400 font-bold text-sm border-4 border-transparent transition-all";
        circle.innerHTML = i;
        text.className = "text-[11px] font-semibold text-slate-400";
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
      const nik = document.getElementById('nik');
      const nisn = document.getElementById('nisn');
      const birthPlace = document.getElementById('birth_place');
      const birthDate = document.getElementById('birth_date');
      const gender = document.getElementById('gender');
      const religion = document.getElementById('religion');
      const email = document.getElementById('email');
      const phone = document.getElementById('phone');

      if (!fullName.value.trim()) return markError(fullName, 'Nama Lengkap wajib diisi');
      if (!nik.value.trim()) return markError(nik, 'NIK wajib diisi');
      if (!/^\d{16}$/.test(nik.value.trim())) return markError(nik, 'NIK harus berupa 16 digit angka');
      if (!nisn.value.trim()) return markError(nisn, 'NISN wajib diisi');
      if (!/^\d{10}$/.test(nisn.value.trim())) return markError(nisn, 'NISN harus berupa 10 digit angka');
      if (!birthPlace.value.trim()) return markError(birthPlace, 'Tempat Lahir wajib diisi');
      if (!birthDate.value.trim()) return markError(birthDate, 'Tanggal Lahir wajib diisi');
      if (!gender.value) return markError(gender, 'Jenis Kelamin wajib dipilih');
      if (!religion.value) return markError(religion, 'Agama wajib dipilih');
      if (!email.value.trim()) return markError(email, 'Email wajib diisi');
      if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) return markError(email, 'Format email tidak valid');
      if (!phone.value.trim()) return markError(phone, 'Nomor Telepon wajib diisi');
      if (!/^\d{9,15}$/.test(phone.value.trim())) return markError(phone, 'Nomor Telepon harus berupa 9-15 digit angka');
    }

    if (step === 2) {
      const province = document.getElementById('province');
      const city = document.getElementById('city');
      const district = document.getElementById('district');
      const subdistrict = document.getElementById('subdistrict');
      const postalCode = document.getElementById('postal_code');
      const address = document.getElementById('address');

      if (!province.value.trim()) return markError(province, 'Provinsi wajib diisi');
      if (!city.value.trim()) return markError(city, 'Kota/Kabupaten wajib diisi');
      if (!district.value.trim()) return markError(district, 'Kecamatan wajib diisi');
      if (!subdistrict.value.trim()) return markError(subdistrict, 'Kelurahan wajib diisi');
      if (!postalCode.value.trim()) return markError(postalCode, 'Kode Pos wajib diisi');
      if (!/^\d{5}$/.test(postalCode.value.trim())) return markError(postalCode, 'Kode Pos harus berupa 5 digit angka');
      if (!address.value.trim()) return markError(address, 'Alamat Lengkap wajib diisi');
    }

    if (step === 3) {
      const fatherName = document.getElementById('father_name');
      const fatherEducation = document.getElementById('father_education');
      const fatherOccupation = document.getElementById('father_occupation');
      const fatherIncome = document.getElementById('father_income');
      const motherName = document.getElementById('mother_name');
      const motherEducation = document.getElementById('mother_education');
      const motherOccupation = document.getElementById('mother_occupation');
      const motherIncome = document.getElementById('mother_income');

      if (!fatherName.value.trim()) return markError(fatherName, 'Nama Ayah wajib diisi');
      if (!fatherEducation.value) return markError(fatherEducation, 'Pendidikan Ayah wajib dipilih');
      if (!fatherOccupation.value) return markError(fatherOccupation, 'Pekerjaan Ayah wajib dipilih');
      if (!fatherIncome.value) return markError(fatherIncome, 'Penghasilan Ayah wajib dipilih');
      if (!motherName.value.trim()) return markError(motherName, 'Nama Ibu wajib diisi');
      if (!motherEducation.value) return markError(motherEducation, 'Pendidikan Ibu wajib dipilih');
      if (!motherOccupation.value) return markError(motherOccupation, 'Pekerjaan Ibu wajib dipilih');
      if (!motherIncome.value) return markError(motherIncome, 'Penghasilan Ibu wajib dipilih');
    }

    if (step === 4) {
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

    if (step === 5) {
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
    
    // Auto hide after 3 seconds
    setTimeout(() => {
      alert.classList.add('hidden');
    }, 3000);
  }

  function buildReviewSummary() {
    // Fill Step 5 Review Panel elements with form values
    document.getElementById('rev-name').textContent = document.getElementById('full_name').value || '-';
    document.getElementById('rev-nik-nisn').textContent = (document.getElementById('nik').value || '-') + ' / ' + (document.getElementById('nisn').value || '-');
    document.getElementById('rev-birth').textContent = (document.getElementById('birth_place').value || '-') + ', ' + (document.getElementById('birth_date').value || '-');
    
    const genderSel = document.getElementById('gender');
    document.getElementById('rev-gender').textContent = genderSel.options[genderSel.selectedIndex]?.text || '-';
    
    document.getElementById('rev-contact').textContent = (document.getElementById('phone').value || '-') + ' / ' + (document.getElementById('email').value || '-');
    
    const prov = document.getElementById('province').value || '';
    const city = document.getElementById('city').value || '';
    const detailAddr = document.getElementById('address').value || '';
    document.getElementById('rev-address').textContent = `${detailAddr}, ${city}, ${prov}` || '-';

    document.getElementById('rev-school').textContent = (document.getElementById('school_name').value || '-') + ' (' + (document.getElementById('school_major').value || '-') + ')';
    document.getElementById('rev-score').textContent = document.getElementById('average_score').value || '-';

    const p1 = document.getElementById('program1_id');
    document.getElementById('rev-prodi1').textContent = p1.options[p1.selectedIndex]?.text || '-';

    const p2 = document.getElementById('program2_id');
    document.getElementById('rev-prodi2').textContent = p2.options[p2.selectedIndex]?.value ? p2.options[p2.selectedIndex].text : 'Tidak Memilih';
  }

  // Init Step 1 on load
  showStep(currentStep);
</script>
