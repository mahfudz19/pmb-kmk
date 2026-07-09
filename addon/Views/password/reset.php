<div class="text-center">
  <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Reset Password</h1>
  <p class="mt-2 text-sm text-slate-500">Silakan buat password baru Anda</p>
</div>

<?php if (isset($error)): ?>
  <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-md">
    <p class="text-sm text-red-700"><?= htmlspecialchars($error) ?></p>
  </div>
<?php endif; ?>

<form data-spa method="POST" action="/password/reset" class="space-y-4">
  <?php if (isset($token)): ?>
    <input type="hidden" name="token" value="<?= $token ?>">
  <?php endif; ?>

  <div>
    <label for="email" class="block text-sm font-semibold text-slate-700">Email Address</label>
    <div class="mt-1">
      <input
        type="email"
        id="email"
        name="email"
        class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition-all bg-slate-50"
        placeholder="nama@email.com"
        required>
    </div>
  </div>

  <div>
    <label for="password" class="block text-sm font-semibold text-slate-700">Password Baru</label>
    <div class="relative mt-1">
      <input
        type="password"
        id="password"
        name="password"
        class="appearance-none block w-full pl-4 pr-10 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition-all bg-slate-50"
        placeholder="••••••••"
        minlength="8"
        required>
      <button
        type="button"
        onclick="togglePasswordVisibility('password', this)"
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

  <div>
    <label for="password_confirmation" class="block text-sm font-semibold text-slate-700">Konfirmasi Password Baru</label>
    <div class="relative mt-1">
      <input
        type="password"
        id="password_confirmation"
        name="password_confirmation"
        class="appearance-none block w-full pl-4 pr-10 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition-all bg-slate-50"
        placeholder="••••••••"
        minlength="8"
        required>
      <button
        type="button"
        onclick="togglePasswordVisibility('password_confirmation', this)"
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

  <div>
    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-full shadow-md text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all hover:-translate-y-0.5">
      Reset Password
    </button>
  </div>
</form>

<div class="text-center pt-2">
  <a data-spa href="/login" class="text-sm font-semibold text-indigo-600 hover:text-indigo-500 transition-colors">
    Kembali ke Login
  </a>
</div>

<?php if (isset($error)): ?>
  <script>
    Swal.fire({
      icon: 'error',
      title: 'Reset Password Gagal',
      text: <?= json_encode($error) ?>,
      confirmButtonColor: '#4f46e5'
    });
  </script>
<?php endif; ?>