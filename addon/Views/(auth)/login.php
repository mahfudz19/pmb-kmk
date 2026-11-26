<?php
?>
<div class="text-left">
  <h1 class="text-3xl font-extrabold text-white tracking-tight">Masuk</h1>
  <p class="mt-1.5 text-sm text-slate-400">Simulasi Penerimaan Mahasiswa Baru</p>
</div>

<?php if (isset($error)): ?>
  <div class="bg-red-500/10 border border-red-500/30 p-3.5 rounded-xl">
    <div class="flex items-start gap-2.5">
      <i data-lucide="alert-circle" class="w-4 h-4 text-red-400 mt-0.5 flex-shrink-0"></i>
      <p class="text-xs sm:text-sm text-red-300 leading-relaxed"><?= htmlspecialchars($error) ?></p>
    </div>
  </div>
<?php endif; ?>

<form data-spa method="POST" action="<?= getBaseUrl('/login') ?>" class="space-y-5">
  <div>
    <label for="email" class="block text-xs font-semibold text-slate-200 mb-2">Email</label>
    <div class="relative">
      <div class="pointer-events-none absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
        <i data-lucide="mail" class="w-4 h-4"></i>
      </div>
      <input
        type="email"
        id="email"
        name="email"
        class="auth-dark-input appearance-none block w-full pl-10 pr-4 py-3 border border-[#323859] rounded-xl placeholder-[#6d7599] text-white text-sm bg-[#212642] focus:outline-none focus:ring-2 focus:ring-[#4368f5]/40 focus:border-[#4368f5] transition-all"
        value="<?= htmlspecialchars($_GET['email'] ?? '') ?>"
        placeholder="nama@email.com"
        required>
    </div>
  </div>

  <div>
    <div class="flex justify-between items-center mb-2">
      <label for="password" class="block text-xs font-semibold text-slate-200">Password</label>
      <a data-spa tabindex="-1" href="<?= getBaseUrl('/password/forgot') ?>" class="text-xs font-semibold text-[#5b82ff] hover:text-[#7c9cff] transition-colors">Lupa password?</a>
    </div>
    <div class="relative">
      <div class="pointer-events-none absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
        <i data-lucide="lock" class="w-4 h-4"></i>
      </div>
      <input
        type="password"
        id="password"
        name="password"
        class="auth-dark-input appearance-none block w-full pl-10 pr-11 py-3 border border-[#323859] rounded-xl placeholder-[#6d7599] text-white text-sm bg-[#212642] focus:outline-none focus:ring-2 focus:ring-[#4368f5]/40 focus:border-[#4368f5] transition-all"
        placeholder="Masukkan password"
        required>
      <button
        type="button"
        tabindex="-1"
        onclick="togglePasswordVisibility('password', this)"
        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-200 transition-colors focus:outline-none cursor-pointer">
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

  <div class="pt-1">
    <button type="submit" id="login-button" class="w-full flex justify-center items-center gap-2 py-3.5 px-4 border border-transparent rounded-xl shadow-lg shadow-[#4368f5]/25 text-sm font-bold text-white bg-[#4368f5] hover:bg-[#3658db] active:scale-[0.99] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-[#181c36] focus:ring-[#4368f5] disabled:opacity-50 disabled:cursor-not-allowed transition-all cursor-pointer">
      <span class="button-text inline-flex items-center gap-2">
        <span>Masuk ke Akun</span>
        <i data-lucide="arrow-right" class="w-4 h-4"></i>
      </span>
      <span class="button-loading hidden items-center gap-2">
        <span class="animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent"></span>
        <span>Masuk...</span>
      </span>
    </button>
  </div>

  <script>
    (function() {
      const form = document.querySelector('form');
      const button = document.getElementById('login-button');
      form.addEventListener('submit', () => {
        button.disabled = true;
        button.querySelector('.button-text').style.display = 'none';
        button.querySelector('.button-loading').style.display = 'inline-flex';
      });
    })();
  </script>
</form>

<div class="mt-6 border-t border-white/[0.08] pt-6 text-center space-y-4">
  <p class="text-sm text-slate-400">
    Belum memiliki akun?
    <a data-spa href="<?= getBaseUrl('/register') ?>" class="font-semibold text-[#5b82ff] hover:text-[#7c9cff] transition-colors">Daftar Sekarang</a>
  </p>
  <p class="text-xs text-slate-400/80 flex items-center justify-center gap-1.5">
    <i data-lucide="shield-check" class="w-3.5 h-3.5 text-slate-400 flex-shrink-0"></i>
    <span>Data Anda dilindungi dan hanya dipakai untuk proses PMB</span>
  </p>
</div>

<?php if (isset($error)): ?>
  <script>
    Swal.fire({
      icon: 'error',
      title: 'Gagal Login',
      text: <?= json_encode($error) ?>,
      background: '#181c36',
      color: '#f1f5f9',
      confirmButtonColor: '#4368f5'
    });
  </script>
<?php endif; ?>
