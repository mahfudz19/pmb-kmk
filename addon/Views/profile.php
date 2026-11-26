<?php

/**
 * @var \App\Core\View\PageMeta $meta
 * @var array $user
 * @var null|array $registration
 * @var mixed $transportList
 * @var mixed $tinggalList
 * @var mixed $pendidikanList
 * @var mixed $pekerjaanList
 * @var mixed $penghasilanList
 * @var mixed $kebutuhanKhususList
 * @var mixed $wilayahList
 */
$isLocked = false;
$userPerms = json_decode($user['permissions'] ?? '[]', true) ?: [];
$isAdmin = ($user['role'] ?? 'user') === 'admin' || in_array('*', $userPerms) || count(array_intersect($userPerms, ['verify_payment', 'verify_document', 'manage_selection', 'manage_settings', 'manage_users'])) > 0;
?>

<div class="w-full space-y-6">
  <!-- Profile Header Banner -->
  <div class="relative overflow-hidden rounded-2xl bg-white p-6 md:p-7 shadow-sm border border-slate-200/80">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div class="flex items-center gap-4">
        <div class="w-16 h-16 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center shrink-0">
          <i data-lucide="user" class="w-8 h-8 text-indigo-600"></i>
        </div>
        <div>
          <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full bg-indigo-50 border border-indigo-100 text-xs font-semibold text-indigo-700 mb-1">
            <span class="w-1.5 h-1.5 rounded-full bg-indigo-600"></span>
            <?= $isAdmin ? 'Panel Akun Administrator' : 'Panel Data Pendaftar' ?>
          </div>
          <h1 class="text-xl md:text-2xl font-extrabold text-slate-900 tracking-tight">
            <?= htmlspecialchars($user['name'] ?? '-') ?>
          </h1>
          <p class="text-xs md:text-sm text-slate-500">
            <?= htmlspecialchars($user['email']) ?>
          </p>
        </div>
      </div>

      <div class="flex items-center gap-2.5 self-start md:self-center shrink-0">
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-indigo-50 border border-indigo-100 text-indigo-700 font-bold text-xs">
          <i data-lucide="<?= $isAdmin ? 'shield-check' : 'user-check' ?>" class="w-3.5 h-3.5 text-indigo-600"></i>
          <?= htmlspecialchars(strtoupper($user['role'] ?? 'user')) ?>
        </span>
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-indigo-50 border border-indigo-100 text-indigo-700 font-bold text-xs">
          <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
          Akun Aktif
        </span>
      </div>
    </div>
  </div>

  <div class="flex flex-col lg:flex-row gap-6 items-start">
    <!-- Navigation Sidebar -->
    <div class="w-full lg:w-72 shrink-0 space-y-4">
      <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200/80 space-y-1.5">
        <div class="px-3 py-2 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
          Menu Pengaturan
        </div>

        <?php if (!$isAdmin): ?>
          <button type="button" onclick="switchTab('alamat')" class="w-full text-left px-3.5 py-2.5 rounded-xl text-xs font-bold tracking-wide transition-all cursor-pointer flex items-center justify-between group bg-indigo-50 text-indigo-700 border border-indigo-100 shadow-sm" id="tab-btn-alamat">
            <span class="flex items-center gap-2.5">
              <i data-lucide="map-pin" class="tab-icon w-4 h-4 text-indigo-600 shrink-0"></i>
              Alamat & Kontak
            </span>
            <i data-lucide="chevron-right" class="tab-chevron w-3.5 h-3.5 text-indigo-400"></i>
          </button>
          <button type="button" onclick="switchTab('ortu')" class="w-full text-left px-3.5 py-2.5 rounded-xl text-xs font-bold tracking-wide transition-all cursor-pointer flex items-center justify-between group bg-white text-slate-600 hover:bg-slate-50 border border-transparent" id="tab-btn-ortu">
            <span class="flex items-center gap-2.5">
              <i data-lucide="users" class="tab-icon w-4 h-4 text-slate-400 group-hover:text-slate-600 shrink-0"></i>
              Orang Tua / Wali
            </span>
            <i data-lucide="chevron-right" class="tab-chevron w-3.5 h-3.5 text-slate-300 group-hover:text-slate-500"></i>
          </button>
          <button type="button" onclick="switchTab('kebutuhan')" class="w-full text-left px-3.5 py-2.5 rounded-xl text-xs font-bold tracking-wide transition-all cursor-pointer flex items-center justify-between group bg-white text-slate-600 hover:bg-slate-50 border border-transparent" id="tab-btn-kebutuhan">
            <span class="flex items-center gap-2.5">
              <i data-lucide="accessibility" class="tab-icon w-4 h-4 text-slate-400 group-hover:text-slate-600 shrink-0"></i>
              Kebutuhan Khusus
            </span>
            <i data-lucide="chevron-right" class="tab-chevron w-3.5 h-3.5 text-slate-300 group-hover:text-slate-500"></i>
          </button>
          <button type="button" onclick="switchTab('pendidikan')" class="w-full text-left px-3.5 py-2.5 rounded-xl text-xs font-bold tracking-wide transition-all cursor-pointer flex items-center justify-between group bg-white text-slate-600 hover:bg-slate-50 border border-transparent" id="tab-btn-pendidikan">
            <span class="flex items-center gap-2.5">
              <i data-lucide="graduation-cap" class="tab-icon w-4 h-4 text-slate-400 group-hover:text-slate-600 shrink-0"></i>
              Riwayat Pendidikan
            </span>
            <i data-lucide="chevron-right" class="tab-chevron w-3.5 h-3.5 text-slate-300 group-hover:text-slate-500"></i>
          </button>
          <button type="button" onclick="switchTab('kebutuhan')" class="w-full text-left px-4 py-3 rounded-2xl text-xs font-extrabold uppercase tracking-wider transition-all cursor-pointer flex items-center gap-3 bg-white text-slate-650 hover:bg-slate-50" id="tab-btn-kebutuhan">
            <span>♿</span> Kebutuhan Khusus
          </button>
        <?php endif; ?>

        <?php
        $passwordBtnClass = $isAdmin
          ? 'bg-indigo-50 text-indigo-700 border border-indigo-100 shadow-sm'
          : 'bg-white text-slate-600 hover:bg-slate-50 border border-transparent';
        ?>
        <button type="button" onclick="switchTab('password')" class="w-full text-left px-3.5 py-2.5 rounded-xl text-xs font-bold tracking-wide transition-all cursor-pointer flex items-center justify-between group <?= $passwordBtnClass ?>" id="tab-btn-password">
          <span class="flex items-center gap-2.5">
            <i data-lucide="lock" class="tab-icon w-4 h-4 <?= $isAdmin ? 'text-indigo-600' : 'text-slate-400 group-hover:text-slate-600' ?> shrink-0"></i>
            Ganti Kata Sandi
          </span>
          <i data-lucide="chevron-right" class="tab-chevron w-3.5 h-3.5 <?= $isAdmin ? 'text-indigo-400' : 'text-slate-300 group-hover:text-slate-500' ?>"></i>
        </button>
      </div>

      <!-- Quick info badge -->
      <div class="bg-indigo-50/60 rounded-2xl p-4 border border-indigo-100 text-xs space-y-2">
        <div class="flex items-center gap-2 font-bold text-indigo-900">
          <i data-lucide="info" class="w-4 h-4 text-indigo-600 shrink-0"></i>
          Informasi Akun
        </div>
        <p class="text-slate-600 text-[11px] leading-relaxed">
          <?= $isAdmin ? 'Pastikan selalu menggunakan password yang kuat dan unik untuk mengamankan hak akses administratif sistem.' : 'Lengkapi data profil Anda dengan akurat untuk keperluan verifikasi berkas dan seleksi mahasiswa baru.' ?>
        </p>
      </div>
    </div>

    <!-- Main Tab Content Area -->
    <div class="flex-1 w-full min-w-0">
      <!-- Tab Password -->
      <div id="tab-panel-password" class="tab-panel <?= $isAdmin ? '' : 'hidden' ?> bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6 md:p-8 space-y-6">
        <div class="border-b border-slate-100 pb-4">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
              <i data-lucide="lock" class="w-4 h-4"></i>
            </div>
            <div>
              <h2 class="text-lg md:text-xl font-extrabold text-slate-900 tracking-tight">Ganti Kata Sandi</h2>
              <p class="text-xs text-slate-500 mt-0.5">Ubah password akun Anda secara berkala untuk menjaga keamanan data.</p>
            </div>
          </div>
        </div>

        <form method="POST" action="<?= getBaseUrl('/profile') ?>" class="space-y-6">
          <input type="hidden" name="tab" value="password">

          <div class="space-y-1.5">
            <label for="current_password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Password Saat Ini</label>
            <div class="relative">
              <input type="password" id="current_password" name="current_password" placeholder="Masukkan password saat ini" class="appearance-none block w-full pl-4 pr-11 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition-all bg-slate-50" required>
              <button type="button" tabindex="-1" onclick="togglePasswordVisibility('current_password', this)" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-indigo-600 transition-colors focus:outline-none cursor-pointer">
                <svg class="eye-open h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                <svg class="eye-closed h-4 w-4 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.076m3.102-3.007A9.956 9.956 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c.08 0 .16.002.24.006m-2.64 2.64A3 3 0 1014 14.828" />
                </svg>
              </button>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-1.5">
              <label for="new_password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Password Baru</label>
              <div class="relative">
                <input type="password" id="new_password" name="new_password" placeholder="Min. 8 karakter" class="appearance-none block w-full pl-4 pr-11 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition-all bg-slate-50" required>
                <button type="button" tabindex="-1" onclick="togglePasswordVisibility('new_password', this)" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-indigo-600 transition-colors focus:outline-none cursor-pointer">
                  <svg class="eye-open h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                  <svg class="eye-closed h-4 w-4 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.076m3.102-3.007A9.956 9.956 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c.08 0 .16.002.24.006m-2.64 2.64A3 3 0 1014 14.828" />
                  </svg>
                </button>
              </div>
            </div>

            <div class="space-y-1.5">
              <label for="new_password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Konfirmasi Password Baru</label>
              <div class="relative">
                <input type="password" id="new_password_confirmation" name="new_password_confirmation" placeholder="Ulangi password baru" class="appearance-none block w-full pl-4 pr-11 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition-all bg-slate-50" required>
                <button type="button" tabindex="-1" onclick="togglePasswordVisibility('new_password_confirmation', this)" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-indigo-600 transition-colors focus:outline-none cursor-pointer">
                  <svg class="eye-open h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                  <svg class="eye-closed h-4 w-4 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.076m3.102-3.007A9.956 9.956 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c.08 0 .16.002.24.006m-2.64 2.64A3 3 0 1014 14.828" />
                  </svg>
                </button>
              </div>
            </div>
          </div>

          <div class="pt-4 border-t border-slate-100 flex justify-end">
            <button type="submit" class="px-6 py-2.5 border border-transparent rounded-xl shadow-sm text-xs font-bold uppercase tracking-wider text-white bg-indigo-600 hover:bg-indigo-700 transition-all cursor-pointer inline-flex items-center gap-2">
              <i data-lucide="check" class="w-4 h-4"></i>
              Ubah Kata Sandi
            </button>
          </div>
        </form>
      </div>

      <!-- Tab Alamat -->
      <div id="tab-panel-alamat" class="tab-panel <?= $isAdmin ? 'hidden' : '' ?> bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6 md:p-8 space-y-6">
        <div class="border-b border-slate-100 pb-4">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
              <i data-lucide="map-pin" class="w-4 h-4"></i>
            </div>
            <div>
              <h2 class="text-lg md:text-xl font-extrabold text-slate-900 tracking-tight">Alamat & Kontak</h2>
              <p class="text-xs text-slate-500 mt-0.5">Kelola domisili dan kontak detail Anda.</p>
            </div>
          </div>
        </div>

        <?php if (!$registration): ?>
          <div class="p-8 bg-slate-50 rounded-2xl text-center space-y-3 border border-slate-200/80">
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center mx-auto">
              <i data-lucide="calendar" class="w-6 h-6"></i>
            </div>
            <h3 class="text-sm font-bold text-slate-800">Gelombang Belum Dipilih</h3>
            <p class="text-xs text-slate-500 max-w-md mx-auto">Silakan pilih gelombang pendaftaran di halaman utama / Dashboard terlebih dahulu sebelum melengkapi profil.</p>
            <a href="<?= getBaseUrl('/dashboard') ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-xl text-xs font-bold hover:bg-indigo-700 transition-all">
              <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> Ke Dashboard
            </a>
          </div>
        <?php else: ?>
          <form method="POST" action="<?= getBaseUrl('/profile') ?>" class="space-y-6">
            <input type="hidden" name="tab" value="alamat">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="space-y-1.5">
                <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Email Aktif <span class="text-red-500">*</span></label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($registration['email'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition-all bg-slate-50" placeholder="Alamat Email Aktif" <?= $isLocked ? 'disabled' : '' ?> required>
              </div>

              <div class="space-y-1.5">
                <label for="npwp" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">NPWP</label>
                <input type="text" id="npwp" name="npwp" maxlength="30" value="<?= htmlspecialchars($address['npwp'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition-all bg-slate-50" placeholder="15 digit NPWP jika ada" <?= $isLocked ? 'disabled' : '' ?>>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div class="md:col-span-2 space-y-1.5">
                <label for="street" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Jalan</label>
                <input type="text" id="street" name="street" maxlength="100" value="<?= htmlspecialchars($address['street'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition-all bg-slate-50" placeholder="Nama Jalan" <?= $isLocked ? 'disabled' : '' ?>>
              </div>
              <div class="space-y-1.5">
                <label for="telephone" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Telepon Rumah</label>
                <input type="text" id="telephone" name="telephone" maxlength="15" value="<?= htmlspecialchars($address['telephone'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition-all bg-slate-50" placeholder="Telepon Rumah" <?= $isLocked ? 'disabled' : '' ?>>
              </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
              <div class="space-y-1.5">
                <label for="dusun" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Dusun</label>
                <input type="text" id="dusun" name="dusun" maxlength="50" value="<?= htmlspecialchars($address['dusun'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition-all bg-slate-50" placeholder="Dusun" <?= $isLocked ? 'disabled' : '' ?>>
              </div>
              <div class="space-y-1.5">
                <label for="rt" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">RT</label>
                <input type="text" id="rt" name="rt" maxlength="5" value="<?= htmlspecialchars($address['rt'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition-all bg-slate-50" placeholder="RT" <?= $isLocked ? 'disabled' : '' ?>>
              </div>
              <div class="space-y-1.5">
                <label for="rw" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">RW</label>
                <input type="text" id="rw" name="rw" maxlength="5" value="<?= htmlspecialchars($address['rw'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition-all bg-slate-50" placeholder="RW" <?= $isLocked ? 'disabled' : '' ?>>
              </div>
            </div>

            <div class="space-y-1.5">
              <label for="district" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Kecamatan <span class="text-red-500">*</span></label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                  <i data-lucide="search" class="w-4 h-4"></i>
                </div>
                <input type="text" id="district_search" oninput="filterDistricts(this.value)" class="appearance-none block w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-xs bg-slate-50 font-medium" placeholder="Cari nama kecamatan di sini..." <?= $isLocked ? 'disabled' : '' ?>>
              </div>
              <select id="district" name="district" onchange="onDistrictChange(this)" class="block w-full px-3 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-medium mt-1.5" <?= $isLocked ? 'disabled' : '' ?> required>
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
              <div class="space-y-1.5">
                <label for="subdistrict" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Kelurahan / Desa <span class="text-red-500">*</span></label>
                <input type="text" id="subdistrict" name="subdistrict" value="<?= htmlspecialchars($address['subdistrict'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition-all bg-slate-50" placeholder="Nama Kelurahan" <?= $isLocked ? 'disabled' : '' ?> required>
              </div>

              <div class="space-y-1.5">
                <label for="postal_code" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Kode Pos</label>
                <input type="text" id="postal_code" name="postal_code" maxlength="5" value="<?= htmlspecialchars($address['postal_code'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition-all bg-slate-50" placeholder="Kode Pos" <?= $isLocked ? 'disabled' : '' ?>>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="space-y-1.5">
                <label for="kps_receiver" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Penerima KPS</label>
                <select name="kps_receiver" id="kps_receiver" onchange="toggleKpsNumber(this.value)" class="block w-full px-3 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50" <?= $isLocked ? 'disabled' : '' ?>>
                  <option value="tidak" <?= ($address['kps_receiver'] ?? 'tidak') === 'tidak' ? 'selected' : '' ?>>Tidak</option>
                  <option value="ya" <?= ($address['kps_receiver'] ?? '') === 'ya' ? 'selected' : '' ?>>Ya</option>
                </select>
              </div>

              <div class="space-y-1.5 <?= ($address['kps_receiver'] ?? '') === 'ya' ? '' : 'hidden' ?>" id="kps_number_container">
                <label for="kps_number" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Nomor KPS</label>
                <input type="text" id="kps_number" name="kps_number" value="<?= htmlspecialchars($address['kps_number'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition-all bg-slate-50" placeholder="Nomor KPS" <?= $isLocked ? 'disabled' : '' ?>>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="space-y-1.5">
                <label for="transportation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Alat Transportasi</label>
                <select id="transportation" name="transportation" class="block w-full px-3 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50" <?= $isLocked ? 'disabled' : '' ?>>
                  <option value="" disabled>Pilih Transportasi</option>
                  <?php foreach ($transportList as $tr): ?>
                    <option value="<?= htmlspecialchars($tr['nm_alat_transport']) ?>" <?= ($address['transportation'] ?? '') === $tr['nm_alat_transport'] ? 'selected' : '' ?>><?= htmlspecialchars($tr['nm_alat_transport']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="space-y-1.5">
                <label for="living_type" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Jenis Tinggal</label>
                <select id="living_type" name="living_type" class="block w-full px-3 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50" <?= $isLocked ? 'disabled' : '' ?>>
                  <option value="" disabled>Pilih Jenis Tinggal</option>
                  <?php foreach ($tinggalList as $tl): ?>
                    <option value="<?= htmlspecialchars($tl['nm_jns_tinggal'] ?? '') ?>" <?= ($address['living_type'] ?? '') === ($tl['nm_jns_tinggal'] ?? '') ? 'selected' : '' ?>><?= htmlspecialchars($tl['nm_jns_tinggal'] ?? '') ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <div class="space-y-1.5">
              <label for="address" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Alamat Lengkap Detail <span class="text-red-500">*</span></label>
              <textarea id="address" name="address" rows="3" class="block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition-all bg-slate-50" placeholder="Alamat lengkap beserta blok/no rumah" <?= $isLocked ? 'disabled' : '' ?> required><?= htmlspecialchars($address['address'] ?? '') ?></textarea>
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end">
              <button type="submit" <?= $isLocked ? 'disabled class="px-6 py-2.5 border border-transparent rounded-xl shadow-sm text-xs font-bold uppercase tracking-wider text-white bg-slate-400 opacity-60 cursor-not-allowed inline-flex items-center gap-2"' : 'class="px-6 py-2.5 border border-transparent rounded-xl shadow-sm text-xs font-bold uppercase tracking-wider text-white bg-indigo-600 hover:bg-indigo-700 transition-all cursor-pointer inline-flex items-center gap-2"' ?>>
                <i data-lucide="save" class="w-4 h-4"></i>
                Simpan Alamat & Kontak
              </button>
            </div>
          </form>
        <?php endif; ?>
      </div>

      <!-- Tab Orang Tua / Wali -->
      <div id="tab-panel-ortu" class="tab-panel hidden bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6 md:p-8 space-y-6">
        <div class="border-b border-slate-100 pb-4">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
              <i data-lucide="users" class="w-4 h-4"></i>
            </div>
            <div>
              <h2 class="text-lg md:text-xl font-extrabold text-slate-900 tracking-tight">Orang Tua / Wali</h2>
              <p class="text-xs text-slate-500 mt-0.5">Kelola identitas ayah, ibu, dan wali pendaftar.</p>
            </div>
          </div>
        </div>

        <?php if (!$registration): ?>
          <div class="p-8 bg-slate-50 rounded-2xl text-center space-y-3 border border-slate-200/80">
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center mx-auto">
              <i data-lucide="calendar" class="w-6 h-6"></i>
            </div>
            <h3 class="text-sm font-bold text-slate-800">Gelombang Belum Dipilih</h3>
            <p class="text-xs text-slate-500 max-w-md mx-auto">Silakan pilih gelombang pendaftaran di halaman utama / Dashboard terlebih dahulu sebelum melengkapi profil.</p>
            <a href="<?= getBaseUrl('/dashboard') ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-xl text-xs font-bold hover:bg-indigo-700 transition-all">
              <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> Ke Dashboard
            </a>
          </div>
        <?php else: ?>
          <form method="POST" action="<?= getBaseUrl('/profile') ?>" class="space-y-6">
            <input type="hidden" name="tab" value="ortu">

            <!-- Data Ayah -->
            <div class="space-y-4 p-5 rounded-2xl bg-slate-50 border border-slate-200/70">
              <div class="flex items-center gap-2 text-sm font-bold text-slate-800 pb-2 border-b border-slate-200/80">
                <i data-lucide="user" class="w-4 h-4 text-indigo-600"></i>
                <span>Data Ayah Kandung</span>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                  <label for="father_name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Nama Lengkap Ayah</label>
                  <input type="text" id="father_name" name="father_name" value="<?= htmlspecialchars($parents['father_name'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition-all bg-white" placeholder="Nama Ayah" <?= $isLocked ? 'disabled' : '' ?>>
                </div>
                <div class="space-y-1.5">
                  <label for="father_nik" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">NIK Ayah</label>
                  <input type="text" id="father_nik" name="father_nik" maxlength="16" value="<?= htmlspecialchars($parents['father_nik'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition-all bg-white" placeholder="16 digit NIK Ayah" <?= $isLocked ? 'disabled' : '' ?>>
                </div>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                  <label for="father_birth_date" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Tanggal Lahir Ayah</label>
                  <input type="date" id="father_birth_date" name="father_birth_date" value="<?= (!empty($parents['father_birth_date']) && $parents['father_birth_date'] !== '1970-01-01' && $parents['father_birth_date'] !== '01/01/1970') ? date('Y-m-d', strtotime(str_replace('/', '-', $parents['father_birth_date']))) : '' ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition-all bg-white" <?= $isLocked ? 'disabled' : '' ?>>
                </div>
                <div class="space-y-1.5">
                  <label for="father_education" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Pendidikan Terakhir Ayah</label>
                  <select id="father_education" name="father_education" class="block w-full px-3 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-white" <?= $isLocked ? 'disabled' : '' ?>>
                    <option value="" disabled selected>Pilih Pendidikan</option>
                    <?php foreach ($pendidikanList as $pl): ?>
                      <option value="<?= htmlspecialchars($pl['nm_jenj_didik']) ?>" <?= ($parents['father_education'] ?? '') === $pl['nm_jenj_didik'] ? 'selected' : '' ?>><?= htmlspecialchars($pl['nm_jenj_didik']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                  <label for="father_occupation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Pekerjaan Ayah</label>
                  <select id="father_occupation" name="father_occupation" class="block w-full px-3 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-white" <?= $isLocked ? 'disabled' : '' ?>>
                    <option value="" disabled selected>Pilih Pekerjaan</option>
                    <?php foreach ($pekerjaanList as $pe): ?>
                      <option value="<?= htmlspecialchars($pe['nm_pekerjaan']) ?>" <?= ($parents['father_occupation'] ?? '') === $pe['nm_pekerjaan'] ? 'selected' : '' ?>><?= htmlspecialchars($pe['nm_pekerjaan']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="space-y-1.5">
                  <label for="father_income" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Penghasilan Ayah</label>
                  <select id="father_income" name="father_income" class="block w-full px-3 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-white" <?= $isLocked ? 'disabled' : '' ?>>
                    <option value="" disabled selected>Pilih Penghasilan</option>
                    <?php foreach ($penghasilanList as $ph): ?>
                      <option value="<?= htmlspecialchars($ph['nm_penghasilan']) ?>" <?= ($parents['father_income'] ?? '') === $ph['nm_penghasilan'] ? 'selected' : '' ?>><?= htmlspecialchars($ph['nm_penghasilan']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
            </div>

            <!-- Data Ibu -->
            <div class="space-y-4 p-5 rounded-2xl bg-slate-50 border border-slate-200/70">
              <div class="flex items-center gap-2 text-sm font-bold text-slate-800 pb-2 border-b border-slate-200/80">
                <i data-lucide="user" class="w-4 h-4 text-indigo-600"></i>
                <span>Data Ibu Kandung</span>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                  <label for="parent_mother_name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Nama Lengkap Ibu</label>
                  <input type="text" id="parent_mother_name" name="parent_mother_name" value="<?= htmlspecialchars($parents['mother_name'] ?? $registration['mother_name'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition-all bg-white" placeholder="Nama Ibu" <?= $isLocked ? 'disabled' : '' ?>>
                </div>
                <div class="space-y-1.5">
                  <label for="mother_nik" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">NIK Ibu</label>
                  <input type="text" id="mother_nik" name="mother_nik" maxlength="16" value="<?= htmlspecialchars($parents['mother_nik'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition-all bg-white" placeholder="16 digit NIK Ibu" <?= $isLocked ? 'disabled' : '' ?>>
                </div>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                  <label for="mother_birth_date" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Tanggal Lahir Ibu</label>
                  <input type="date" id="mother_birth_date" name="mother_birth_date" value="<?= (!empty($parents['mother_birth_date']) && $parents['mother_birth_date'] !== '1970-01-01' && $parents['mother_birth_date'] !== '01/01/1970') ? date('Y-m-d', strtotime(str_replace('/', '-', $parents['mother_birth_date']))) : '' ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition-all bg-white" <?= $isLocked ? 'disabled' : '' ?>>
                </div>
                <div class="space-y-1.5">
                  <label for="mother_education" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Pendidikan Terakhir Ibu</label>
                  <select id="mother_education" name="mother_education" class="block w-full px-3 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-white" <?= $isLocked ? 'disabled' : '' ?>>
                    <option value="" disabled selected>Pilih Pendidikan</option>
                    <?php foreach ($pendidikanList as $pl): ?>
                      <option value="<?= htmlspecialchars($pl['nm_jenj_didik']) ?>" <?= ($parents['mother_education'] ?? '') === $pl['nm_jenj_didik'] ? 'selected' : '' ?>><?= htmlspecialchars($pl['nm_jenj_didik']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                  <label for="mother_occupation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Pekerjaan Ibu</label>
                  <select id="mother_occupation" name="mother_occupation" class="block w-full px-3 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-white" <?= $isLocked ? 'disabled' : '' ?>>
                    <option value="" disabled selected>Pilih Pekerjaan</option>
                    <?php foreach ($pekerjaanList as $pe): ?>
                      <option value="<?= htmlspecialchars($pe['nm_pekerjaan']) ?>" <?= ($parents['mother_occupation'] ?? '') === $pe['nm_pekerjaan'] ? 'selected' : '' ?>><?= htmlspecialchars($pe['nm_pekerjaan']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="space-y-1.5">
                  <label for="mother_income" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Penghasilan Ibu</label>
                  <select id="mother_income" name="mother_income" class="block w-full px-3 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-white" <?= $isLocked ? 'disabled' : '' ?>>
                    <option value="" disabled selected>Pilih Penghasilan</option>
                    <?php foreach ($penghasilanList as $ph): ?>
                      <option value="<?= htmlspecialchars($ph['nm_penghasilan']) ?>" <?= ($parents['mother_income'] ?? '') === $ph['nm_penghasilan'] ? 'selected' : '' ?>><?= htmlspecialchars($ph['nm_penghasilan']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
            </div>

            <!-- Data Wali -->
            <div class="space-y-4 p-5 rounded-2xl bg-slate-50 border border-slate-200/70">
              <div class="flex items-center gap-2 text-sm font-bold text-slate-800 pb-2 border-b border-slate-200/80">
                <i data-lucide="shield" class="w-4 h-4 text-indigo-600"></i>
                <span>Data Wali (Opsional)</span>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                  <label for="guardian_name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Nama Lengkap Wali</label>
                  <input type="text" id="guardian_name" name="guardian_name" value="<?= htmlspecialchars($parents['guardian_name'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition-all bg-white" placeholder="Nama Wali" <?= $isLocked ? 'disabled' : '' ?>>
                </div>
                <div class="space-y-1.5">
                  <label for="guardian_birth_date" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Tanggal Lahir Wali</label>
                  <input type="date" id="guardian_birth_date" name="guardian_birth_date" value="<?= (!empty($parents['guardian_birth_date']) && $parents['guardian_birth_date'] !== '1970-01-01' && $parents['guardian_birth_date'] !== '01/01/1970') ? date('Y-m-d', strtotime(str_replace('/', '-', $parents['guardian_birth_date']))) : '' ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition-all bg-white" <?= $isLocked ? 'disabled' : '' ?>>
                </div>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="space-y-1.5">
                  <label for="guardian_education" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Pendidikan Wali</label>
                  <select id="guardian_education" name="guardian_education" class="block w-full px-3 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-white" <?= $isLocked ? 'disabled' : '' ?>>
                    <option value="" disabled selected>Pilih Pendidikan</option>
                    <?php foreach ($pendidikanList as $pl): ?>
                      <option value="<?= htmlspecialchars($pl['nm_jenj_didik']) ?>" <?= ($parents['guardian_education'] ?? '') === $pl['nm_jenj_didik'] ? 'selected' : '' ?>><?= htmlspecialchars($pl['nm_jenj_didik']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="space-y-1.5">
                  <label for="guardian_occupation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Pekerjaan Wali</label>
                  <select id="guardian_occupation" name="guardian_occupation" class="block w-full px-3 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-white" <?= $isLocked ? 'disabled' : '' ?>>
                    <option value="" disabled selected>Pilih Pekerjaan</option>
                    <?php foreach ($pekerjaanList as $pe): ?>
                      <option value="<?= htmlspecialchars($pe['nm_pekerjaan']) ?>" <?= ($parents['guardian_occupation'] ?? '') === $pe['nm_pekerjaan'] ? 'selected' : '' ?>><?= htmlspecialchars($pe['nm_pekerjaan']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="space-y-1.5">
                  <label for="guardian_income" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Penghasilan Wali</label>
                  <select id="guardian_income" name="guardian_income" class="block w-full px-3 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-white" <?= $isLocked ? 'disabled' : '' ?>>
                    <option value="" disabled selected>Pilih Penghasilan</option>
                    <?php foreach ($penghasilanList as $ph): ?>
                      <option value="<?= htmlspecialchars($ph['nm_penghasilan']) ?>" <?= ($parents['guardian_income'] ?? '') === $ph['nm_penghasilan'] ? 'selected' : '' ?>><?= htmlspecialchars($ph['nm_penghasilan']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end">
              <button type="submit" <?= $isLocked ? 'disabled class="px-6 py-2.5 border border-transparent rounded-xl shadow-sm text-xs font-bold uppercase tracking-wider text-white bg-slate-400 opacity-60 cursor-not-allowed inline-flex items-center gap-2"' : 'class="px-6 py-2.5 border border-transparent rounded-xl shadow-sm text-xs font-bold uppercase tracking-wider text-white bg-indigo-600 hover:bg-indigo-700 transition-all cursor-pointer inline-flex items-center gap-2"' ?>>
                <i data-lucide="save" class="w-4 h-4"></i>
                Simpan Orang Tua & Wali
              </button>
            </div>
          </form>
        <?php endif; ?>
      </div>

      <!-- Tab Kebutuhan Khusus -->
      <div id="tab-panel-kebutuhan" class="tab-panel hidden bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6 md:p-8 space-y-6">
        <div class="border-b border-slate-100 pb-4">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
              <i data-lucide="accessibility" class="w-4 h-4"></i>
            </div>
            <div>
              <h2 class="text-lg md:text-xl font-extrabold text-slate-900 tracking-tight">Kebutuhan Khusus</h2>
              <p class="text-xs text-slate-500 mt-0.5">Kelola kebutuhan khusus bagi pendaftar dan orang tua/wali jika ada.</p>
            </div>
          </div>
        </div>

        <?php if (!$registration): ?>
          <div class="p-8 bg-slate-50 rounded-2xl text-center space-y-3 border border-slate-200/80">
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center mx-auto">
              <i data-lucide="calendar" class="w-6 h-6"></i>
            </div>
            <h3 class="text-sm font-bold text-slate-800">Gelombang Belum Dipilih</h3>
            <p class="text-xs text-slate-500 max-w-md mx-auto">Silakan pilih gelombang pendaftaran di halaman utama / Dashboard terlebih dahulu sebelum melengkapi profil.</p>
            <a href="<?= getBaseUrl('/dashboard') ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-xl text-xs font-bold hover:bg-indigo-700 transition-all">
              <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> Ke Dashboard
            </a>
          </div>
        <?php else: ?>
          <form method="POST" action="<?= getBaseUrl('/profile') ?>" class="space-y-6">
            <input type="hidden" name="tab" value="kebutuhan">

            <div class="space-y-1.5">
              <label for="has_special_needs" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Apakah terdapat kebutuhan khusus?</label>
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
              <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/70 space-y-3">
                <label class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-2">
                  <i data-lucide="user" class="w-3.5 h-3.5 text-indigo-600"></i>
                  Kebutuhan Khusus Pendaftar
                </label>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-2.5">
                  <?php foreach ($kebutuhanKhususList as $kh): ?>
                    <label class="inline-flex items-center text-xs font-medium text-slate-600 cursor-pointer p-2 rounded-lg bg-white border border-slate-200 hover:border-indigo-200 transition-colors">
                      <input type="checkbox" name="student_needs[]" value="<?= htmlspecialchars($kh['nm_kebutuhan_khusus']) ?>" <?= in_array($kh['nm_kebutuhan_khusus'], $studentNeedsArr, true) ? 'checked' : '' ?> class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer" <?= $isLocked ? 'disabled' : '' ?>>
                      <span class="ml-2"><?= htmlspecialchars($kh['nm_kebutuhan_khusus']) ?></span>
                    </label>
                  <?php endforeach; ?>
                </div>
              </div>

              <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/70 space-y-3">
                <label class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-2">
                  <i data-lucide="user" class="w-3.5 h-3.5 text-indigo-600"></i>
                  Kebutuhan Khusus Ayah
                </label>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-2.5">
                  <?php foreach ($kebutuhanKhususList as $kh): ?>
                    <label class="inline-flex items-center text-xs font-medium text-slate-600 cursor-pointer p-2 rounded-lg bg-white border border-slate-200 hover:border-indigo-200 transition-colors">
                      <input type="checkbox" name="father_needs[]" value="<?= htmlspecialchars($kh['nm_kebutuhan_khusus']) ?>" <?= in_array($kh['nm_kebutuhan_khusus'], $fatherNeedsArr, true) ? 'checked' : '' ?> class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer" <?= $isLocked ? 'disabled' : '' ?>>
                      <span class="ml-2"><?= htmlspecialchars($kh['nm_kebutuhan_khusus']) ?></span>
                    </label>
                  <?php endforeach; ?>
                </div>
              </div>

              <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/70 space-y-3">
                <label class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-2">
                  <i data-lucide="user" class="w-3.5 h-3.5 text-indigo-600"></i>
                  Kebutuhan Khusus Ibu
                </label>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-2.5">
                  <?php foreach ($kebutuhanKhususList as $kh): ?>
                    <label class="inline-flex items-center text-xs font-medium text-slate-600 cursor-pointer p-2 rounded-lg bg-white border border-slate-200 hover:border-indigo-200 transition-colors">
                      <input type="checkbox" name="mother_needs[]" value="<?= htmlspecialchars($kh['nm_kebutuhan_khusus']) ?>" <?= in_array($kh['nm_kebutuhan_khusus'], $motherNeedsArr, true) ? 'checked' : '' ?> class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer" <?= $isLocked ? 'disabled' : '' ?>>
                      <span class="ml-2"><?= htmlspecialchars($kh['nm_kebutuhan_khusus']) ?></span>
                    </label>
                  <?php endforeach; ?>
                </div>
              </div>

              <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/70 space-y-3">
                <label class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-2">
                  <i data-lucide="shield" class="w-3.5 h-3.5 text-indigo-600"></i>
                  Kebutuhan Khusus Wali
                </label>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-2.5">
                  <?php foreach ($kebutuhanKhususList as $kh): ?>
                    <label class="inline-flex items-center text-xs font-medium text-slate-600 cursor-pointer p-2 rounded-lg bg-white border border-slate-200 hover:border-indigo-200 transition-colors">
                      <input type="checkbox" name="guardian_needs[]" value="<?= htmlspecialchars($kh['nm_kebutuhan_khusus']) ?>" <?= in_array($kh['nm_kebutuhan_khusus'], $guardianNeedsArr, true) ? 'checked' : '' ?> class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer" <?= $isLocked ? 'disabled' : '' ?>>
                      <span class="ml-2"><?= htmlspecialchars($kh['nm_kebutuhan_khusus']) ?></span>
                    </label>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end">
              <button type="submit" <?= $isLocked ? 'disabled class="px-6 py-2.5 border border-transparent rounded-xl shadow-sm text-xs font-bold uppercase tracking-wider text-white bg-slate-400 opacity-60 cursor-not-allowed inline-flex items-center gap-2"' : 'class="px-6 py-2.5 border border-transparent rounded-xl shadow-sm text-xs font-bold uppercase tracking-wider text-white bg-indigo-600 hover:bg-indigo-700 transition-all cursor-pointer inline-flex items-center gap-2"' ?>>
                <i data-lucide="save" class="w-4 h-4"></i>
                Simpan Kebutuhan Khusus
              </button>
            </div>
          </form>
        <?php endif; ?>
      </div>

      <!-- Tab Riwayat Pendidikan -->
      <div id="tab-panel-pendidikan" class="tab-panel hidden bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6 md:p-8 space-y-6">
        <div class="border-b border-slate-100 pb-4">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
              <i data-lucide="graduation-cap" class="w-4 h-4"></i>
            </div>
            <div>
              <h2 class="text-lg md:text-xl font-extrabold text-slate-900 tracking-tight">Riwayat Pendidikan</h2>
              <p class="text-xs text-slate-500 mt-0.5">Kelola riwayat pendidikan terakhir sekolah asal pendaftar.</p>
            </div>
          </div>
        </div>

        <?php if (!$registration): ?>
          <div class="p-8 bg-slate-50 rounded-2xl text-center space-y-3 border border-slate-200/80">
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center mx-auto">
              <i data-lucide="calendar" class="w-6 h-6"></i>
            </div>
            <h3 class="text-sm font-bold text-slate-800">Gelombang Belum Dipilih</h3>
            <p class="text-xs text-slate-500 max-w-md mx-auto">Silakan pilih gelombang pendaftaran di halaman utama / Dashboard terlebih dahulu sebelum melengkapi profil.</p>
            <a href="<?= getBaseUrl('/dashboard') ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-xl text-xs font-bold hover:bg-indigo-700 transition-all">
              <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> Ke Dashboard
            </a>
          </div>
        <?php else: ?>
          <form method="POST" action="<?= getBaseUrl('/profile') ?>" class="space-y-6">
            <input type="hidden" name="tab" value="pendidikan">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="space-y-1.5">
                <label for="school_name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Nama Sekolah Asal <span class="text-red-500">*</span></label>
                <input type="text" id="school_name" name="school_name" value="<?= htmlspecialchars($education['school_name'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition-all bg-slate-50" placeholder="Nama Sekolah" <?= $isLocked ? 'disabled' : '' ?> required>
              </div>

              <div class="space-y-1.5">
                <label for="school_major" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Jurusan Sekolah Asal <span class="text-red-500">*</span></label>
                <input type="text" id="school_major" name="school_major" value="<?= htmlspecialchars($education['school_major'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition-all bg-slate-50" placeholder="Contoh: IPA / IPS / Rekayasa Perangkat Lunak" <?= $isLocked ? 'disabled' : '' ?> required>
              </div>
            </div>

            <div class="space-y-1.5">
              <label for="school_address" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Kecamatan Sekolah <span class="text-red-500">*</span></label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                  <i data-lucide="search" class="w-4 h-4"></i>
                </div>
                <input type="text" id="school_address_search" oninput="filterSchoolDistricts(this.value)" class="appearance-none block w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-xs bg-slate-50 font-medium" placeholder="Cari nama kecamatan sekolah di sini..." <?= $isLocked ? 'disabled' : '' ?>>
              </div>
              <select id="school_address" name="school_address" onchange="onSchoolDistrictChange(this)" class="block w-full px-3 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-slate-50 font-medium mt-1.5" <?= $isLocked ? 'disabled' : '' ?> required>
                <?php if (!empty($education['school_address'])): ?>
                  <option value="<?= htmlspecialchars($education['school_address']) ?>" selected><?= htmlspecialchars($education['school_address']) ?></option>
                <?php else: ?>
                  <option value="" disabled selected>Pilih Kecamatan Sekolah</option>
                <?php endif; ?>
              </select>
              <input type="hidden" id="school_address_id_wil" name="school_address_id_wil" value="<?= htmlspecialchars($education['school_address_id_wil'] ?? '') ?>">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div class="space-y-1.5">
                <label for="graduation_year" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Tahun Lulus <span class="text-red-500">*</span></label>
                <input type="number" id="graduation_year" name="graduation_year" value="<?= htmlspecialchars($education['graduation_year'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition-all bg-slate-50" placeholder="Tahun Lulus" <?= $isLocked ? 'disabled' : '' ?> required>
              </div>

              <div class="space-y-1.5">
                <label for="diploma_number" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Nomor Ijazah / SKL <span class="text-red-500">*</span></label>
                <input type="text" id="diploma_number" name="diploma_number" value="<?= htmlspecialchars($education['diploma_number'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition-all bg-slate-50" placeholder="Nomor Ijazah / SKL" <?= $isLocked ? 'disabled' : '' ?> required>
              </div>

              <div class="space-y-1.5">
                <label for="average_score" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Nilai Rata-rata <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" id="average_score" name="average_score" value="<?= htmlspecialchars($education['average_score'] ?? '') ?>" class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition-all bg-slate-50" placeholder="Contoh: 85.50" <?= $isLocked ? 'disabled' : '' ?> required>
              </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end">
              <button type="submit" <?= $isLocked ? 'disabled class="px-6 py-2.5 border border-transparent rounded-xl shadow-sm text-xs font-bold uppercase tracking-wider text-white bg-slate-400 opacity-60 cursor-not-allowed inline-flex items-center gap-2"' : 'class="px-6 py-2.5 border border-transparent rounded-xl shadow-sm text-xs font-bold uppercase tracking-wider text-white bg-indigo-600 hover:bg-indigo-700 transition-all cursor-pointer inline-flex items-center gap-2"' ?>>
                <i data-lucide="save" class="w-4 h-4"></i>
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
      btn.className = "w-full text-left px-3.5 py-2.5 rounded-xl text-xs font-bold tracking-wide transition-all cursor-pointer flex items-center justify-between group bg-white text-slate-600 hover:bg-slate-50 border border-transparent";
      const icon = btn.querySelector('.tab-icon, i[data-lucide], svg');
      if (icon) {
        icon.setAttribute('class', 'tab-icon w-4 h-4 text-slate-400 group-hover:text-slate-600 shrink-0');
      }
      const chevron = btn.querySelector('.tab-chevron');
      if (chevron) {
        chevron.setAttribute('class', 'tab-chevron w-3.5 h-3.5 text-slate-300 group-hover:text-slate-500');
      }
    });

    const activeBtn = document.getElementById(`tab-btn-${tabId}`);
    if (activeBtn) {
      activeBtn.className = "w-full text-left px-3.5 py-2.5 rounded-xl text-xs font-bold tracking-wide transition-all cursor-pointer flex items-center justify-between group bg-indigo-50 text-indigo-700 border border-indigo-100 shadow-sm";
      const icon = activeBtn.querySelector('.tab-icon, i[data-lucide], svg');
      if (icon) {
        icon.setAttribute('class', 'tab-icon w-4 h-4 text-indigo-600 shrink-0');
      }
      const chevron = activeBtn.querySelector('.tab-chevron');
      if (chevron) {
        chevron.setAttribute('class', 'tab-chevron w-3.5 h-3.5 text-indigo-400');
      }
    }

    if (window.lucide && typeof window.lucide.createIcons === 'function') {
      window.lucide.createIcons();
    }

    const url = new URL(window.location.href);
    url.searchParams.set('tab', tabId);
    window.history.replaceState({}, '', url);
  }

  document.addEventListener('DOMContentLoaded', () => {
    if (window.lucide && typeof window.lucide.createIcons === 'function') {
      window.lucide.createIcons();
    }
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