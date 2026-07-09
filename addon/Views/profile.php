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

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Left Column: User Summary Card -->
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

      <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200/80 space-y-4">
        <h4 class="text-sm font-bold text-slate-800 uppercase tracking-wider border-b border-slate-150 pb-2">Hak Akses (*Permissions*)</h4>
        <div class="flex flex-wrap gap-2">
          <?php 
            $perms = json_decode($user['permissions'] ?? '[]', true);
            if (empty($perms)) {
              echo '<span class="text-slate-400 text-xs italic">Tanpa akses khusus</span>';
            } elseif (in_array('*', $perms, true)) {
              echo '<span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 font-semibold text-xs border border-emerald-250">Akses Penuh (*)</span>';
            } else {
              foreach ($perms as $p) {
                echo '<span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-slate-50 text-slate-650 font-semibold text-xs border border-slate-200">' . htmlspecialchars($p) . '</span>';
              }
            }
          ?>
        </div>
      </div>
    </div>

    <!-- Right Column: Profile Form -->
    <div class="lg:col-span-2">
      <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 p-8 space-y-6">
        <div>
          <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Pengaturan Profil</h2>
          <p class="mt-1 text-sm text-slate-500">Kelola informasi identitas diri Anda dan ganti kata sandi akun secara berkala.</p>
        </div>

        <form id="profile-form" method="POST" action="/profile" class="space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-1">
              <label for="name" class="block text-sm font-semibold text-slate-700">Nama Lengkap</label>
              <input
                type="text"
                id="name"
                name="name"
                value="<?= htmlspecialchars($user['name'] ?? '') ?>"
                class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition-all bg-slate-50"
                required
              >
            </div>

            <div class="space-y-1">
              <label for="email" class="block text-sm font-semibold text-slate-700">Email Address</label>
              <input
                type="email"
                id="email"
                name="email"
                value="<?= htmlspecialchars($user['email'] ?? '') ?>"
                class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition-all bg-slate-50"
                required
              >
            </div>
          </div>

          <div class="border-t border-slate-100 pt-6 space-y-4">
            <div>
              <h3 class="text-base font-bold text-slate-800">Ganti Kata Sandi</h3>
              <p class="text-xs text-slate-500">Kosongkan kolom di bawah jika Anda tidak ingin mengubah kata sandi.</p>
            </div>

            <div class="space-y-4">
              <div class="space-y-1">
                <label for="current_password" class="block text-sm font-semibold text-slate-700">Password Saat Ini</label>
                <div class="relative">
                  <input
                    type="password"
                    id="current_password"
                    name="current_password"
                    placeholder="Masukkan password saat ini"
                    class="appearance-none block w-full pl-4 pr-10 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition-all bg-slate-50"
                  >
                  <button
                    type="button"
                    onclick="togglePasswordVisibility('current_password', this)"
                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-indigo-600 transition-colors focus:outline-none"
                  >
                    <svg class="eye-open h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg class="eye-closed h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.076m3.102-3.007A9.956 9.956 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c.08 0 .16.002.24.006m-2.64 2.64A3 3 0 1014 14.828" />
                    </svg>
                  </button>
                </div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-1">
                  <label for="new_password" class="block text-sm font-semibold text-slate-700">Password Baru</label>
                  <div class="relative">
                    <input
                      type="password"
                      id="new_password"
                      name="new_password"
                      placeholder="Min. 8 karakter"
                      class="appearance-none block w-full pl-4 pr-10 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition-all bg-slate-50"
                    >
                    <button
                      type="button"
                      onclick="togglePasswordVisibility('new_password', this)"
                      class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-indigo-600 transition-colors focus:outline-none"
                    >
                      <svg class="eye-open h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                      </svg>
                      <svg class="eye-closed h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.076m3.102-3.007A9.956 9.956 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c.08 0 .16.002.24.006m-2.64 2.64A3 3 0 1014 14.828" />
                      </svg>
                    </button>
                  </div>
                </div>

                <div class="space-y-1">
                  <label for="new_password_confirmation" class="block text-sm font-semibold text-slate-700">Konfirmasi Password Baru</label>
                  <div class="relative">
                    <input
                      type="password"
                      id="new_password_confirmation"
                      name="new_password_confirmation"
                      placeholder="Min. 8 karakter"
                      class="appearance-none block w-full pl-4 pr-10 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition-all bg-slate-50"
                    >
                    <button
                      type="button"
                      onclick="togglePasswordVisibility('new_password_confirmation', this)"
                      class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-indigo-600 transition-colors focus:outline-none"
                    >
                      <svg class="eye-open h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                      </svg>
                      <svg class="eye-closed h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.076m3.102-3.007A9.956 9.956 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c.08 0 .16.002.24.006m-2.64 2.64A3 3 0 1014 14.828" />
                      </svg>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="pt-4 border-t border-slate-100 flex justify-end">
            <button
              type="submit"
              id="profile-button"
              class="px-6 py-3 border border-transparent rounded-full shadow-md text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all hover:-translate-y-0.5 cursor-pointer"
            >
              <span class="button-text">Simpan Profil</span>
              <span class="button-loading hidden items-center gap-2">
                <span class="animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent"></span>
                Menyimpan...
              </span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  (function() {
    const form = document.getElementById('profile-form');
    const button = document.getElementById('profile-button');
    form.addEventListener('submit', () => {
      button.disabled = true;
      button.querySelector('.button-text').style.display = 'none';
      button.querySelector('.button-loading').style.display = 'inline-flex';
    });
  })();
</script>
