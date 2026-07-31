<div class="w-full py-2">
  <?php if (isset($_GET['success'])): ?>
    <div class="mb-6 p-4 bg-emerald-50 border border-emerald-500 text-emerald-700 rounded-2xl flex items-center gap-3">
      <span>✅</span>
      <span class="text-sm font-semibold"><?= htmlspecialchars($_GET['success']) ?></span>
    </div>
  <?php endif; ?>

  <?php if (isset($_GET['error'])): ?>
    <div class="mb-6 p-4 bg-red-50 border border-red-500 text-red-700 rounded-2xl flex items-center gap-3">
      <span>⚠️</span>
      <span class="text-sm font-semibold"><?= htmlspecialchars($_GET['error']) ?></span>
    </div>
  <?php endif; ?>

  <?php 
    $isLocked = $registration && in_array($registration['status'], ['Submitted', 'Verified', 'Released']);
    $userPerms = json_decode($user['permissions'] ?? '[]', true) ?: [];
    $isAdmin = ($user['role'] ?? 'user') === 'admin' || in_array('*', $userPerms) || count(array_intersect($userPerms, ['verify_payment', 'verify_document', 'manage_selection', 'manage_settings', 'manage_users'])) > 0;
  ?>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="space-y-6">
      <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200/80 text-center space-y-4">
        <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-indigo-50 border-2 border-indigo-100 text-indigo-600 text-3xl">
          👤
        </div>
        <div>
          <h3 class="text-lg font-bold text-slate-900"><?= htmlspecialchars($user['name'] ?? '-') ?></h3>
          <p class="text-xs text-slate-500"><?= htmlspecialchars($user['email']) ?></p>
        </div>
        <div class="pt-2 border-t border-slate-100 flex justify-center gap-2">
          <?php if (($user['role'] ?? 'user') === 'admin'): ?>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800 uppercase">Admin</span>
          <?php else: ?>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600 uppercase">User</span>
          <?php endif; ?>
          <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">Aktif</span>
        </div>
      </div>

      <div class="bg-white rounded-3xl p-4 shadow-sm border border-slate-200/80 space-y-1">
        <?php if (!$isAdmin): ?>
          <button type="button" onclick="switchTab('alamat')" class="w-full text-left px-4 py-3 rounded-2xl text-xs font-extrabold uppercase tracking-wider transition-all cursor-pointer flex items-center gap-3 bg-indigo-50 text-indigo-750 font-black shadow-sm" id="tab-btn-alamat">
            <span>📍</span> Alamat & Kontak
          </button>
          <button type="button" onclick="switchTab('ortu')" class="w-full text-left px-4 py-3 rounded-2xl text-xs font-extrabold uppercase tracking-wider transition-all cursor-pointer flex items-center gap-3 bg-white text-slate-650 hover:bg-slate-50" id="tab-btn-ortu">
            <span>👨‍👩‍👧‍👦</span> Orang Tua / Wali
          </button>
          <button type="button" onclick="switchTab('kebutuhan')" class="w-full text-left px-4 py-3 rounded-2xl text-xs font-extrabold uppercase tracking-wider transition-all cursor-pointer flex items-center gap-3 bg-white text-slate-650 hover:bg-slate-50" id="tab-btn-kebutuhan">
            <span>♿</span> Kebutuhan Khusus
          </button>
          <button type="button" onclick="switchTab('pendidikan')" class="w-full text-left px-4 py-3 rounded-2xl text-xs font-extrabold uppercase tracking-wider transition-all cursor-pointer flex items-center gap-3 bg-white text-slate-650 hover:bg-slate-50" id="tab-btn-pendidikan">
            <span>🎓</span> Riwayat Pendidikan
          </button>
        <?php endif; ?>
        <button type="button" onclick="switchTab('password')" class="w-full text-left px-4 py-3 rounded-2xl text-xs font-extrabold uppercase tracking-wider transition-all cursor-pointer flex items-center gap-3 <?= $isAdmin ? 'bg-indigo-50 text-indigo-750 font-black shadow-sm' : 'bg-white text-slate-650 hover:bg-slate-50' ?>" id="tab-btn-password">
          <span>🔒</span> Ganti Kata Sandi
        </button>
      </div>
    </div>

    <div class="lg:col-span-2">
      <div id="tab-panel-password" class="tab-panel <?= $isAdmin ? '' : 'hidden' ?> bg-white rounded-3xl shadow-sm border border-slate-200/80 p-8 space-y-6">
        <div>
          <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Ganti Kata Sandi</h2>
          <p class="mt-1 text-sm text-slate-500">Ubah password akun Anda secara berkala untuk menjaga keamanan data.</p>
        </div>

        <form method="POST" action="/profile" class="space-y-6">
          <input type="hidden" name="tab" value="password">

          <div class="space-y-1">
            <label for="current_password" class="block text-sm font-semibold text-slate-700">Password Saat Ini</label>
            <input type="password" id="current_password" name="current_password" placeholder="Masukkan password saat ini" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition-all bg-slate-50" required>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-1">
              <label for="new_password" class="block text-sm font-semibold text-slate-700">Password Baru</label>
              <input type="password" id="new_password" name="new_password" placeholder="Min. 8 karakter" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition-all bg-slate-50" required>
            </div>

            <div class="space-y-1">
              <label for="new_password_confirmation" class="block text-sm font-semibold text-slate-700">Konfirmasi Password Baru</label>
              <input type="password" id="new_password_confirmation" name="new_password_confirmation" placeholder="Min. 8 karakter" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition-all bg-slate-50" required>
            </div>
          </div>

          <div class="pt-4 border-t border-slate-100 flex justify-end">
            <button type="submit" class="px-6 py-3 border border-transparent rounded-full shadow-md text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 transition-all cursor-pointer">
              Ubah Kata Sandi
            </button>
          </div>
        </form>
      </div>

      <div id="tab-panel-alamat" class="tab-panel <?= $isAdmin ? 'hidden' : '' ?> bg-white rounded-3xl shadow-sm border border-slate-200/80 p-8 space-y-6">
        <div>
          <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Alamat & Kontak</h2>
          <p class="mt-1 text-sm text-slate-500">Kelola domisili dan kontak detail Anda.</p>
        </div>

        <?php if (!$registration): ?>
          <div class="p-6 bg-slate-50 rounded-2xl text-center space-y-3">
            <p class="text-sm text-slate-500">Silakan pilih gelombang pendaftaran di halaman utama / Dashboard terlebih dahulu sebelum mengisi data profil lengkap.</p>
            <a href="/dashboard" class="inline-flex px-4 py-2 bg-indigo-600 text-white rounded-xl text-xs font-bold hover:bg-indigo-700 transition-all">Pilih Gelombang</a>
          </div>
        <?php else: ?>
          <form method="POST" action="/profile" class="space-y-6">
            <input type="hidden" name="tab" value="alamat">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="space-y-1">
                <label for="email" class="block text-sm font-semibold text-slate-700">Email Aktif <span class="text-red-550">*</span></label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($registration['email'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition-all bg-slate-50" placeholder="Alamat Email Aktif" <?= $isLocked ? 'disabled' : '' ?> required>
              </div>

              <div class="space-y-1">
                <label for="npwp" class="block text-sm font-semibold text-slate-700">NPWP</label>
                <input type="text" id="npwp" name="npwp" value="<?= htmlspecialchars($address['npwp'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition-all bg-slate-50" placeholder="15 digit NPWP jika ada" <?= $isLocked ? 'disabled' : '' ?>>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div class="md:col-span-2 space-y-1">
                <label for="street" class="block text-sm font-semibold text-slate-700">Jalan</label>
                <input type="text" id="street" name="street" value="<?= htmlspecialchars($address['street'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition-all bg-slate-50" placeholder="Nama Jalan" <?= $isLocked ? 'disabled' : '' ?>>
              </div>
              <div class="space-y-1">
                <label for="telephone" class="block text-sm font-semibold text-slate-700">Telepon Rumah</label>
                <input type="text" id="telephone" name="telephone" value="<?= htmlspecialchars($address['telephone'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition-all bg-slate-50" placeholder="Telepon Rumah" <?= $isLocked ? 'disabled' : '' ?>>
              </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
              <div class="space-y-1">
                <label for="dusun" class="block text-sm font-semibold text-slate-700">Dusun</label>
                <input type="text" id="dusun" name="dusun" value="<?= htmlspecialchars($address['dusun'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition-all bg-slate-50" placeholder="Dusun" <?= $isLocked ? 'disabled' : '' ?>>
              </div>
              <div class="space-y-1">
                <label for="rt" class="block text-sm font-semibold text-slate-700">RT</label>
                <input type="text" id="rt" name="rt" maxlength="5" value="<?= htmlspecialchars($address['rt'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition-all bg-slate-50" placeholder="RT" <?= $isLocked ? 'disabled' : '' ?>>
              </div>
              <div class="space-y-1">
                <label for="rw" class="block text-sm font-semibold text-slate-700">RW</label>
                <input type="text" id="rw" name="rw" maxlength="5" value="<?= htmlspecialchars($address['rw'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition-all bg-slate-50" placeholder="RW" <?= $isLocked ? 'disabled' : '' ?>>
              </div>
            </div>

            <div class="space-y-1.5">
              <label for="district" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Kecamatan <span class="text-red-550">*</span></label>
              <input type="text" id="district_search" oninput="filterDistricts(this.value)" class="appearance-none block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-xs bg-slate-50 font-medium" placeholder="🔍 Cari nama kecamatan di sini..." <?= $isLocked ? 'disabled' : '' ?>>
              <select id="district" name="district" onchange="onDistrictChange(this)" class="block w-full px-3 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-medium" <?= $isLocked ? 'disabled' : '' ?> required>
                <?php if (!empty($address['district'])): ?>
                  <option value="<?= htmlspecialchars($address['district']) ?>" data-kabupaten="<?= htmlspecialchars($address['city'] ?? '') ?>" data-provinsi="<?= htmlspecialchars($address['province'] ?? '') ?>" selected><?= htmlspecialchars($address['district']) ?> (<?= htmlspecialchars($address['city'] ?? '') ?>)</option>
                <?php else: ?>
                  <option value="" disabled selected>Pilih Kecamatan</option>
                <?php endif; ?>
              </select>
              <input type="hidden" id="district_id_wil" name="district_id_wil" value="<?= htmlspecialchars($address['district_id_wil'] ?? '') ?>">
            </div>

            <input type="hidden" id="city" name="city" value="<?= htmlspecialchars($address['city'] ?? '') ?>">
            <input type="hidden" id="province" name="province" value="<?= htmlspecialchars($address['province'] ?? '') ?>">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="space-y-1">
                <label for="subdistrict" class="block text-sm font-semibold text-slate-700">Kelurahan / Desa <span class="text-red-550">*</span></label>
                <input type="text" id="subdistrict" name="subdistrict" value="<?= htmlspecialchars($address['subdistrict'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition-all bg-slate-50" placeholder="Nama Kelurahan" <?= $isLocked ? 'disabled' : '' ?> required>
              </div>

              <div class="space-y-1">
                <label for="postal_code" class="block text-sm font-semibold text-slate-700">Kode Pos</label>
                <input type="text" id="postal_code" name="postal_code" maxlength="5" value="<?= htmlspecialchars($address['postal_code'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition-all bg-slate-50" placeholder="Kode Pos" <?= $isLocked ? 'disabled' : '' ?>>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="space-y-1">
                <label for="kps_receiver" class="block text-sm font-semibold text-slate-700">Penerima KPS</label>
                <select name="kps_receiver" id="kps_receiver" onchange="toggleKpsNumber(this.value)" class="block w-full px-3 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50" <?= $isLocked ? 'disabled' : '' ?>>
                  <option value="tidak" <?= ($address['kps_receiver'] ?? 'tidak') === 'tidak' ? 'selected' : '' ?>>Tidak</option>
                  <option value="ya" <?= ($address['kps_receiver'] ?? '') === 'ya' ? 'selected' : '' ?>>Ya</option>
                </select>
              </div>

              <div class="space-y-1 <?= ($address['kps_receiver'] ?? '') === 'ya' ? '' : 'hidden' ?>" id="kps_number_container">
                <label for="kps_number" class="block text-sm font-semibold text-slate-700">Nomor KPS</label>
                <input type="text" id="kps_number" name="kps_number" value="<?= htmlspecialchars($address['kps_number'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition-all bg-slate-50" placeholder="Nomor KPS" <?= $isLocked ? 'disabled' : '' ?>>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="space-y-1">
                <label for="transportation" class="block text-sm font-semibold text-slate-700">Alat Transportasi</label>
                <select id="transportation" name="transportation" class="block w-full px-3 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50" <?= $isLocked ? 'disabled' : '' ?>>
                  <option value="" disabled>Pilih Transportasi</option>
                  <?php foreach ($transportList as $tr): ?>
                    <option value="<?= htmlspecialchars($tr['nm_alat_transport']) ?>" <?= ($address['transportation'] ?? '') === $tr['nm_alat_transport'] ? 'selected' : '' ?>><?= htmlspecialchars($tr['nm_alat_transport']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="space-y-1">
                <label for="living_type" class="block text-sm font-semibold text-slate-700">Jenis Tinggal</label>
                <select id="living_type" name="living_type" class="block w-full px-3 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50" <?= $isLocked ? 'disabled' : '' ?>>
                  <option value="" disabled>Pilih Jenis Tinggal</option>
                  <?php foreach ($tinggalList as $tl): ?>
                    <option value="<?= htmlspecialchars($tl['nm_jns_tinggal'] ?? '') ?>" <?= ($address['living_type'] ?? '') === ($tl['nm_jns_tinggal'] ?? '') ? 'selected' : '' ?>><?= htmlspecialchars($tl['nm_jns_tinggal'] ?? '') ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <div class="space-y-1">
              <label for="address" class="block text-sm font-semibold text-slate-700">Alamat Lengkap Detail <span class="text-red-550">*</span></label>
              <textarea id="address" name="address" rows="3" class="block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition-all bg-slate-50" placeholder="Alamat lengkap beserta blok/no rumah" <?= $isLocked ? 'disabled' : '' ?> required><?= htmlspecialchars($address['address'] ?? '') ?></textarea>
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end">
              <button type="submit" <?= $isLocked ? 'disabled class="px-6 py-3 border border-transparent rounded-full shadow-md text-sm font-semibold text-white bg-slate-400 opacity-60 cursor-not-allowed"' : 'class="px-6 py-3 border border-transparent rounded-full shadow-md text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 transition-all cursor-pointer"' ?>>
                Simpan Alamat & Kontak
              </button>
            </div>
          </form>
        <?php endif; ?>
      </div>

      <div id="tab-panel-ortu" class="tab-panel hidden bg-white rounded-3xl shadow-sm border border-slate-200/80 p-8 space-y-6">
        <div>
          <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Orang Tua / Wali</h2>
          <p class="mt-1 text-sm text-slate-500">Kelola identitas ayah, ibu, dan wali pendaftar.</p>
        </div>

        <?php if (!$registration): ?>
          <div class="p-6 bg-slate-50 rounded-2xl text-center space-y-3">
            <p class="text-sm text-slate-500">Silakan pilih gelombang pendaftaran di halaman utama / Dashboard terlebih dahulu sebelum mengisi data profil lengkap.</p>
            <a href="/dashboard" class="inline-flex px-4 py-2 bg-indigo-600 text-white rounded-xl text-xs font-bold hover:bg-indigo-700 transition-all">Pilih Gelombang</a>
          </div>
        <?php else: ?>
          <form method="POST" action="/profile" class="space-y-6">
            <input type="hidden" name="tab" value="ortu">

            <div class="space-y-4">
              <h3 class="text-base font-bold text-slate-800 border-b pb-1">👨 Data Ayah Kandung</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-1">
                  <label for="father_name" class="block text-sm font-semibold text-slate-700">Nama Lengkap Ayah</label>
                  <input type="text" id="father_name" name="father_name" value="<?= htmlspecialchars($parents['father_name'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition-all bg-slate-50" placeholder="Nama Ayah" <?= $isLocked ? 'disabled' : '' ?>>
                </div>
                <div class="space-y-1">
                  <label for="father_nik" class="block text-sm font-semibold text-slate-700">NIK Ayah</label>
                  <input type="text" id="father_nik" name="father_nik" maxlength="16" value="<?= htmlspecialchars($parents['father_nik'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition-all bg-slate-50" placeholder="16 digit NIK Ayah" <?= $isLocked ? 'disabled' : '' ?>>
                </div>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-1">
                  <label for="father_birth_date" class="block text-sm font-semibold text-slate-700">Tanggal Lahir Ayah</label>
                  <input type="date" id="father_birth_date" name="father_birth_date" value="<?= (!empty($parents['father_birth_date']) && $parents['father_birth_date'] !== '1970-01-01' && $parents['father_birth_date'] !== '01/01/1970') ? date('Y-m-d', strtotime(str_replace('/', '-', $parents['father_birth_date']))) : '' ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition-all bg-slate-50" <?= $isLocked ? 'disabled' : '' ?>>
                </div>
                <div class="space-y-1">
                  <label for="father_education" class="block text-sm font-semibold text-slate-700">Pendidikan Terakhir Ayah</label>
                  <select id="father_education" name="father_education" class="block w-full px-3 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50" <?= $isLocked ? 'disabled' : '' ?>>
                    <option value="" disabled selected>Pilih Pendidikan</option>
                    <?php foreach ($pendidikanList as $pl): ?>
                      <option value="<?= htmlspecialchars($pl['nm_jenj_didik']) ?>" <?= ($parents['father_education'] ?? '') === $pl['nm_jenj_didik'] ? 'selected' : '' ?>><?= htmlspecialchars($pl['nm_jenj_didik']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-1">
                  <label for="father_occupation" class="block text-sm font-semibold text-slate-700">Pekerjaan Ayah</label>
                  <select id="father_occupation" name="father_occupation" class="block w-full px-3 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50" <?= $isLocked ? 'disabled' : '' ?>>
                    <option value="" disabled selected>Pilih Pekerjaan</option>
                    <?php foreach ($pekerjaanList as $pe): ?>
                      <option value="<?= htmlspecialchars($pe['nm_pekerjaan']) ?>" <?= ($parents['father_occupation'] ?? '') === $pe['nm_pekerjaan'] ? 'selected' : '' ?>><?= htmlspecialchars($pe['nm_pekerjaan']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="space-y-1">
                  <label for="father_income" class="block text-sm font-semibold text-slate-700">Penghasilan Ayah</label>
                  <select id="father_income" name="father_income" class="block w-full px-3 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50" <?= $isLocked ? 'disabled' : '' ?>>
                    <option value="" disabled selected>Pilih Penghasilan</option>
                    <?php foreach ($penghasilanList as $ph): ?>
                      <option value="<?= htmlspecialchars($ph['nm_penghasilan']) ?>" <?= ($parents['father_income'] ?? '') === $ph['nm_penghasilan'] ? 'selected' : '' ?>><?= htmlspecialchars($ph['nm_penghasilan']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
            </div>

            <div class="space-y-4 pt-4">
              <h3 class="text-base font-bold text-slate-800 border-b pb-1">👩 Data Ibu Kandung</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-1">
                  <label for="parent_mother_name" class="block text-sm font-semibold text-slate-700">Nama Lengkap Ibu</label>
                  <input type="text" id="parent_mother_name" name="parent_mother_name" value="<?= htmlspecialchars($parents['mother_name'] ?? $registration['mother_name'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition-all bg-slate-50" placeholder="Nama Ibu" <?= $isLocked ? 'disabled' : '' ?>>
                </div>
                <div class="space-y-1">
                  <label for="mother_nik" class="block text-sm font-semibold text-slate-700">NIK Ibu</label>
                  <input type="text" id="mother_nik" name="mother_nik" maxlength="16" value="<?= htmlspecialchars($parents['mother_nik'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition-all bg-slate-50" placeholder="16 digit NIK Ibu" <?= $isLocked ? 'disabled' : '' ?>>
                </div>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-1">
                  <label for="mother_birth_date" class="block text-sm font-semibold text-slate-700">Tanggal Lahir Ibu</label>
                  <input type="date" id="mother_birth_date" name="mother_birth_date" value="<?= (!empty($parents['mother_birth_date']) && $parents['mother_birth_date'] !== '1970-01-01' && $parents['mother_birth_date'] !== '01/01/1970') ? date('Y-m-d', strtotime(str_replace('/', '-', $parents['mother_birth_date']))) : '' ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition-all bg-slate-50" <?= $isLocked ? 'disabled' : '' ?>>
                </div>
                <div class="space-y-1">
                  <label for="mother_education" class="block text-sm font-semibold text-slate-700">Pendidikan Terakhir Ibu</label>
                  <select id="mother_education" name="mother_education" class="block w-full px-3 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50" <?= $isLocked ? 'disabled' : '' ?>>
                    <option value="" disabled selected>Pilih Pendidikan</option>
                    <?php foreach ($pendidikanList as $pl): ?>
                      <option value="<?= htmlspecialchars($pl['nm_jenj_didik']) ?>" <?= ($parents['mother_education'] ?? '') === $pl['nm_jenj_didik'] ? 'selected' : '' ?>><?= htmlspecialchars($pl['nm_jenj_didik']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-1">
                  <label for="mother_occupation" class="block text-sm font-semibold text-slate-700">Pekerjaan Ibu</label>
                  <select id="mother_occupation" name="mother_occupation" class="block w-full px-3 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50" <?= $isLocked ? 'disabled' : '' ?>>
                    <option value="" disabled selected>Pilih Pekerjaan</option>
                    <?php foreach ($pekerjaanList as $pe): ?>
                      <option value="<?= htmlspecialchars($pe['nm_pekerjaan']) ?>" <?= ($parents['mother_occupation'] ?? '') === $pe['nm_pekerjaan'] ? 'selected' : '' ?>><?= htmlspecialchars($pe['nm_pekerjaan']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="space-y-1">
                  <label for="mother_income" class="block text-sm font-semibold text-slate-700">Penghasilan Ibu</label>
                  <select id="mother_income" name="mother_income" class="block w-full px-3 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50" <?= $isLocked ? 'disabled' : '' ?>>
                    <option value="" disabled selected>Pilih Penghasilan</option>
                    <?php foreach ($penghasilanList as $ph): ?>
                      <option value="<?= htmlspecialchars($ph['nm_penghasilan']) ?>" <?= ($parents['mother_income'] ?? '') === $ph['nm_penghasilan'] ? 'selected' : '' ?>><?= htmlspecialchars($ph['nm_penghasilan']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
            </div>

            <div class="space-y-4 pt-4">
              <h3 class="text-base font-bold text-slate-800 border-b pb-1">🧑 Data Wali (Opsional)</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-1">
                  <label for="guardian_name" class="block text-sm font-semibold text-slate-700">Nama Lengkap Wali</label>
                  <input type="text" id="guardian_name" name="guardian_name" value="<?= htmlspecialchars($parents['guardian_name'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition-all bg-slate-50" placeholder="Nama Wali" <?= $isLocked ? 'disabled' : '' ?>>
                </div>
                <div class="space-y-1">
                  <label for="guardian_birth_date" class="block text-sm font-semibold text-slate-700">Tanggal Lahir Wali</label>
                  <input type="date" id="guardian_birth_date" name="guardian_birth_date" value="<?= (!empty($parents['guardian_birth_date']) && $parents['guardian_birth_date'] !== '1970-01-01' && $parents['guardian_birth_date'] !== '01/01/1970') ? date('Y-m-d', strtotime(str_replace('/', '-', $parents['guardian_birth_date']))) : '' ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition-all bg-slate-50" <?= $isLocked ? 'disabled' : '' ?>>
                </div>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="space-y-1">
                  <label for="guardian_education" class="block text-sm font-semibold text-slate-700">Pendidikan Wali</label>
                  <select id="guardian_education" name="guardian_education" class="block w-full px-3 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50" <?= $isLocked ? 'disabled' : '' ?>>
                    <option value="" disabled selected>Pilih Pendidikan</option>
                    <?php foreach ($pendidikanList as $pl): ?>
                      <option value="<?= htmlspecialchars($pl['nm_jenj_didik']) ?>" <?= ($parents['guardian_education'] ?? '') === $pl['nm_jenj_didik'] ? 'selected' : '' ?>><?= htmlspecialchars($pl['nm_jenj_didik']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="space-y-1">
                  <label for="guardian_occupation" class="block text-sm font-semibold text-slate-700">Pekerjaan Wali</label>
                  <select id="guardian_occupation" name="guardian_occupation" class="block w-full px-3 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50" <?= $isLocked ? 'disabled' : '' ?>>
                    <option value="" disabled selected>Pilih Pekerjaan</option>
                    <?php foreach ($pekerjaanList as $pe): ?>
                      <option value="<?= htmlspecialchars($pe['nm_pekerjaan']) ?>" <?= ($parents['guardian_occupation'] ?? '') === $pe['nm_pekerjaan'] ? 'selected' : '' ?>><?= htmlspecialchars($pe['nm_pekerjaan']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="space-y-1">
                  <label for="guardian_income" class="block text-sm font-semibold text-slate-700">Penghasilan Wali</label>
                  <select id="guardian_income" name="guardian_income" class="block w-full px-3 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50" <?= $isLocked ? 'disabled' : '' ?>>
                    <option value="" disabled selected>Pilih Penghasilan</option>
                    <?php foreach ($penghasilanList as $ph): ?>
                      <option value="<?= htmlspecialchars($ph['nm_penghasilan']) ?>" <?= ($parents['guardian_income'] ?? '') === $ph['nm_penghasilan'] ? 'selected' : '' ?>><?= htmlspecialchars($ph['nm_penghasilan']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end">
              <button type="submit" <?= $isLocked ? 'disabled class="px-6 py-3 border border-transparent rounded-full shadow-md text-sm font-semibold text-white bg-slate-400 opacity-60 cursor-not-allowed"' : 'class="px-6 py-3 border border-transparent rounded-full shadow-md text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 transition-all cursor-pointer"' ?>>
                Simpan Orang Tua & Wali
              </button>
            </div>
          </form>
        <?php endif; ?>
      </div>

      <div id="tab-panel-kebutuhan" class="tab-panel hidden bg-white rounded-3xl shadow-sm border border-slate-200/80 p-8 space-y-6">
        <div>
          <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Kebutuhan Khusus</h2>
          <p class="mt-1 text-sm text-slate-500">Kelola kebutuhan khusus bagi pendaftar dan orang tua/wali jika ada.</p>
        </div>

        <?php if (!$registration): ?>
          <div class="p-6 bg-slate-50 rounded-2xl text-center space-y-3">
            <p class="text-sm text-slate-500">Silakan pilih gelombang pendaftaran di halaman utama / Dashboard terlebih dahulu sebelum mengisi data profil lengkap.</p>
            <a href="/dashboard" class="inline-flex px-4 py-2 bg-indigo-600 text-white rounded-xl text-xs font-bold hover:bg-indigo-700 transition-all">Pilih Gelombang</a>
          </div>
        <?php else: ?>
          <form method="POST" action="/profile" class="space-y-6">
            <input type="hidden" name="tab" value="kebutuhan">

            <div class="space-y-1">
              <label for="has_special_needs" class="block text-sm font-semibold text-slate-700">Apakah terdapat kebutuhan khusus?</label>
              <select id="has_special_needs" name="has_special_needs" onchange="toggleSpecialNeeds(this.value)" class="block w-full px-3 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50" <?= $isLocked ? 'disabled' : '' ?> required>
                <option value="tidak" <?= ($special_needs['has_special_needs'] ?? 'tidak') === 'tidak' ? 'selected' : '' ?>>Tidak</option>
                <option value="ya" <?= ($special_needs['has_special_needs'] ?? '') === 'ya' ? 'selected' : '' ?>>Ya</option>
              </select>
            </div>

            <?php
              $studentNeedsArr = json_decode($special_needs['student_needs'] ?? '[]', true) ?: [];
              $fatherNeedsArr = json_decode($special_needs['father_needs'] ?? '[]', true) ?: [];
              $motherNeedsArr = json_decode($special_needs['mother_needs'] ?? '[]', true) ?: [];
              $guardianNeedsArr = json_decode($special_needs['guardian_needs'] ?? '[]', true) ?: [];
            ?>

            <div id="special_needs_container" class="space-y-4 <?= ($special_needs['has_special_needs'] ?? 'tidak') === 'ya' ? '' : 'hidden' ?>">
              <div class="space-y-2">
                <label class="block text-sm font-semibold text-slate-700">Kebutuhan Khusus Pendaftar</label>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                  <?php foreach ($kebutuhanKhususList as $kh): ?>
                    <label class="inline-flex items-center text-xs font-medium text-slate-650 cursor-pointer">
                      <input type="checkbox" name="student_needs[]" value="<?= htmlspecialchars($kh['nm_kebutuhan_khusus']) ?>" <?= in_array($kh['nm_kebutuhan_khusus'], $studentNeedsArr, true) ? 'checked' : '' ?> class="h-4 w-4 rounded border-slate-300 text-indigo-650 focus:ring-indigo-500 cursor-pointer" <?= $isLocked ? 'disabled' : '' ?>>
                      <span class="ml-2"><?= htmlspecialchars($kh['nm_kebutuhan_khusus']) ?></span>
                    </label>
                  <?php endforeach; ?>
                </div>
              </div>

              <div class="space-y-2">
                <label class="block text-sm font-semibold text-slate-700">Kebutuhan Khusus Ayah</label>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                  <?php foreach ($kebutuhanKhususList as $kh): ?>
                    <label class="inline-flex items-center text-xs font-medium text-slate-650 cursor-pointer">
                      <input type="checkbox" name="father_needs[]" value="<?= htmlspecialchars($kh['nm_kebutuhan_khusus']) ?>" <?= in_array($kh['nm_kebutuhan_khusus'], $fatherNeedsArr, true) ? 'checked' : '' ?> class="h-4 w-4 rounded border-slate-300 text-indigo-650 focus:ring-indigo-500 cursor-pointer" <?= $isLocked ? 'disabled' : '' ?>>
                      <span class="ml-2"><?= htmlspecialchars($kh['nm_kebutuhan_khusus']) ?></span>
                    </label>
                  <?php endforeach; ?>
                </div>
              </div>

              <div class="space-y-2">
                <label class="block text-sm font-semibold text-slate-700">Kebutuhan Khusus Ibu</label>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                  <?php foreach ($kebutuhanKhususList as $kh): ?>
                    <label class="inline-flex items-center text-xs font-medium text-slate-650 cursor-pointer">
                      <input type="checkbox" name="mother_needs[]" value="<?= htmlspecialchars($kh['nm_kebutuhan_khusus']) ?>" <?= in_array($kh['nm_kebutuhan_khusus'], $motherNeedsArr, true) ? 'checked' : '' ?> class="h-4 w-4 rounded border-slate-300 text-indigo-650 focus:ring-indigo-500 cursor-pointer" <?= $isLocked ? 'disabled' : '' ?>>
                      <span class="ml-2"><?= htmlspecialchars($kh['nm_kebutuhan_khusus']) ?></span>
                    </label>
                  <?php endforeach; ?>
                </div>
              </div>

              <div class="space-y-2">
                <label class="block text-sm font-semibold text-slate-700">Kebutuhan Khusus Wali</label>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                  <?php foreach ($kebutuhanKhususList as $kh): ?>
                    <label class="inline-flex items-center text-xs font-medium text-slate-650 cursor-pointer">
                      <input type="checkbox" name="guardian_needs[]" value="<?= htmlspecialchars($kh['nm_kebutuhan_khusus']) ?>" <?= in_array($kh['nm_kebutuhan_khusus'], $guardianNeedsArr, true) ? 'checked' : '' ?> class="h-4 w-4 rounded border-slate-300 text-indigo-650 focus:ring-indigo-500 cursor-pointer" <?= $isLocked ? 'disabled' : '' ?>>
                      <span class="ml-2"><?= htmlspecialchars($kh['nm_kebutuhan_khusus']) ?></span>
                    </label>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end">
              <button type="submit" <?= $isLocked ? 'disabled class="px-6 py-3 border border-transparent rounded-full shadow-md text-sm font-semibold text-white bg-slate-400 opacity-60 cursor-not-allowed"' : 'class="px-6 py-3 border border-transparent rounded-full shadow-md text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 transition-all cursor-pointer"' ?>>
                Simpan Kebutuhan Khusus
              </button>
            </div>
          </form>
        <?php endif; ?>
      </div>

      <div id="tab-panel-pendidikan" class="tab-panel hidden bg-white rounded-3xl shadow-sm border border-slate-200/80 p-8 space-y-6">
        <div>
          <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Riwayat Pendidikan</h2>
          <p class="mt-1 text-sm text-slate-500">Kelola riwayat pendidikan terakhir sekolah asal pendaftar.</p>
        </div>

        <?php if (!$registration): ?>
          <div class="p-6 bg-slate-50 rounded-2xl text-center space-y-3">
            <p class="text-sm text-slate-500">Silakan pilih gelombang pendaftaran di halaman utama / Dashboard terlebih dahulu sebelum mengisi data profil lengkap.</p>
            <a href="/dashboard" class="inline-flex px-4 py-2 bg-indigo-600 text-white rounded-xl text-xs font-bold hover:bg-indigo-700 transition-all">Pilih Gelombang</a>
          </div>
        <?php else: ?>
          <form method="POST" action="/profile" class="space-y-6">
            <input type="hidden" name="tab" value="pendidikan">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="space-y-1">
                <label for="school_name" class="block text-sm font-semibold text-slate-700">Nama Sekolah Asal <span class="text-red-550">*</span></label>
                <input type="text" id="school_name" name="school_name" value="<?= htmlspecialchars($education['school_name'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition-all bg-slate-50" placeholder="Nama Sekolah" <?= $isLocked ? 'disabled' : '' ?> required>
              </div>

              <div class="space-y-1">
                <label for="school_major" class="block text-sm font-semibold text-slate-700">Jurusan Sekolah Asal <span class="text-red-550">*</span></label>
                <input type="text" id="school_major" name="school_major" value="<?= htmlspecialchars($education['school_major'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition-all bg-slate-50" placeholder="Contoh: IPA / IPS / Rekayasa Perangkat Lunak" <?= $isLocked ? 'disabled' : '' ?> required>
              </div>
            </div>

            <div class="space-y-1.5">
              <label for="school_address" class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Kecamatan Sekolah <span class="text-red-550">*</span></label>
              <input type="text" id="school_address_search" oninput="filterSchoolDistricts(this.value)" class="appearance-none block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-xs bg-slate-50 font-medium" placeholder="🔍 Cari nama kecamatan sekolah di sini..." <?= $isLocked ? 'disabled' : '' ?>>
              <select id="school_address" name="school_address" onchange="onSchoolDistrictChange(this)" class="block w-full px-3 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-medium" <?= $isLocked ? 'disabled' : '' ?> required>
                <?php if (!empty($education['school_address'])): ?>
                  <option value="<?= htmlspecialchars($education['school_address']) ?>" selected><?= htmlspecialchars($education['school_address']) ?></option>
                <?php else: ?>
                  <option value="" disabled selected>Pilih Kecamatan Sekolah</option>
                <?php endif; ?>
              </select>
              <input type="hidden" id="school_address_id_wil" name="school_address_id_wil" value="<?= htmlspecialchars($education['school_address_id_wil'] ?? '') ?>">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div class="space-y-1">
                <label for="graduation_year" class="block text-sm font-semibold text-slate-700">Tahun Lulus <span class="text-red-550">*</span></label>
                <input type="number" id="graduation_year" name="graduation_year" value="<?= htmlspecialchars($education['graduation_year'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition-all bg-slate-50" placeholder="Tahun Lulus" <?= $isLocked ? 'disabled' : '' ?> required>
              </div>

              <div class="space-y-1">
                <label for="diploma_number" class="block text-sm font-semibold text-slate-700">Nomor Ijazah / SKL <span class="text-red-550">*</span></label>
                <input type="text" id="diploma_number" name="diploma_number" value="<?= htmlspecialchars($education['diploma_number'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition-all bg-slate-50" placeholder="Nomor Ijazah / Surat Keterangan Lulus" <?= $isLocked ? 'disabled' : '' ?> required>
              </div>

              <div class="space-y-1">
                <label for="average_score" class="block text-sm font-semibold text-slate-700">Nilai Rata-rata Ujian / Rapor <span class="text-red-550">*</span></label>
                <input type="number" step="0.01" id="average_score" name="average_score" value="<?= htmlspecialchars($education['average_score'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition-all bg-slate-50" placeholder="Contoh: 85.50" <?= $isLocked ? 'disabled' : '' ?> required>
              </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end">
              <button type="submit" <?= $isLocked ? 'disabled class="px-6 py-3 border border-transparent rounded-full shadow-md text-sm font-semibold text-white bg-slate-400 opacity-60 cursor-not-allowed"' : 'class="px-6 py-3 border border-transparent rounded-full shadow-md text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 transition-all cursor-pointer"' ?>>
                Simpan Pendidikan
              </button>
            </div>
          </form>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<script>
  function switchTab(tabId) {
    document.querySelectorAll('.tab-panel').forEach(panel => {
      panel.classList.add('hidden');
    });
    const selectedPanel = document.getElementById(`tab-panel-${tabId}`);
    if (selectedPanel) {
      selectedPanel.classList.remove('hidden');
    }

    document.querySelectorAll('[id^="tab-btn-"]').forEach(btn => {
      btn.className = "w-full text-left px-4 py-3 rounded-2xl text-xs font-extrabold uppercase tracking-wider transition-all cursor-pointer flex items-center gap-3 bg-white text-slate-650 hover:bg-slate-50";
    });
    const activeBtn = document.getElementById(`tab-btn-${tabId}`);
    if (activeBtn) {
      activeBtn.className = "w-full text-left px-4 py-3 rounded-2xl text-xs font-extrabold uppercase tracking-wider transition-all cursor-pointer flex items-center gap-3 bg-indigo-50 text-indigo-750 font-black shadow-sm";
    }

    const url = new URL(window.location.href);
    url.searchParams.set('tab', tabId);
    window.history.replaceState({}, '', url);
  }

  document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const initialTab = urlParams.get('tab') || '<?= $isAdmin ? "password" : "alamat" ?>';
    switchTab(initialTab);
  });

  function previewPhoto(input) {
    if (input.files && input.files[0]) {
      const reader = new FileReader();
      reader.onload = function(e) {
        document.getElementById('photo-preview').src = e.target.result;
      }
      reader.readAsDataURL(input.files[0]);
    }
  }

  function toggleKpsNumber(val) {
    const container = document.getElementById('kps_number_container');
    if (val === 'ya') {
      container.classList.remove('hidden');
    } else {
      container.classList.add('hidden');
      const input = document.getElementById('kps_number');
      if (input) input.value = '';
    }
  }

  function toggleSpecialNeeds(val) {
    const container = document.getElementById('special_needs_container');
    if (val === 'ya') {
      container.classList.remove('hidden');
    } else {
      container.classList.add('hidden');
      container.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
    }
  }

  const allWilayahData = <?= json_encode($wilayahList) ?>;

  function filterDistricts(query) {
    const select = document.getElementById('district');
    const term = (query || '').toLowerCase().trim();
    const currentVal = select.value;

    select.innerHTML = '<option value="" disabled' + (!currentVal ? ' selected' : '') + '>Pilih Kecamatan</option>';

    let matches = 0;
    allWilayahData.forEach(item => {
      const fullText = (item.kecamatan + ' ' + (item.kabupaten || '') + ' ' + (item.provinsi || '')).toLowerCase();
      if (!term || fullText.includes(term)) {
        if (matches < 50) {
          const opt = document.createElement('option');
          opt.value = item.kecamatan;
          opt.setAttribute('data-kabupaten', item.kabupaten || '');
          opt.setAttribute('data-provinsi', item.provinsi || '');
          opt.setAttribute('data-id-wil', (item.id_wil || '').trim());
          opt.textContent = item.kecamatan + (item.kabupaten ? ' (' + item.kabupaten + ')' : '');
          if (item.kecamatan === currentVal) {
            opt.selected = true;
          }
          select.appendChild(opt);
          matches++;
        }
      }
    });
  }

  function onDistrictChange(select) {
    const opt = select.options[select.selectedIndex];
    if (opt) {
      document.getElementById('city').value = opt.getAttribute('data-kabupaten') || '';
      document.getElementById('province').value = opt.getAttribute('data-provinsi') || '';
      const idWil = opt.getAttribute('data-id-wil') || '';
      document.getElementById('district_id_wil').value = idWil;
    }
  }

  function filterSchoolDistricts(query) {
    const select = document.getElementById('school_address');
    const term = (query || '').toLowerCase().trim();
    const currentVal = select.value;

    select.innerHTML = '<option value="" disabled' + (!currentVal ? ' selected' : '') + '>Pilih Kecamatan Sekolah</option>';

    let matches = 0;
    allWilayahData.forEach(item => {
      const fullText = (item.kecamatan + ' ' + (item.kabupaten || '') + ' ' + (item.provinsi || '')).toLowerCase();
      if (!term || fullText.includes(term)) {
        if (matches < 50) {
          const opt = document.createElement('option');
          opt.value = item.kecamatan;
          opt.setAttribute('data-id-wil', (item.id_wil || '').trim());
          opt.textContent = item.kecamatan + (item.kabupaten ? ' (' + item.kabupaten + ')' : '');
          if (item.kecamatan === currentVal) {
            opt.selected = true;
          }
          select.appendChild(opt);
          matches++;
        }
      }
    });
  }

  function onSchoolDistrictChange(select) {
    const opt = select.options[select.selectedIndex];
    if (opt) {
      const idWil = opt.getAttribute('data-id-wil') || '';
      document.getElementById('school_address_id_wil').value = idWil;
    }
  }
</script>
