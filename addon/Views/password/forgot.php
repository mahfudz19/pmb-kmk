<?php
?>
<div class="text-left">
  <h1 class="text-3xl font-extrabold text-white tracking-tight">Lupa Password</h1>
  <p class="mt-1.5 text-sm text-slate-400">Masukkan email Anda untuk menerima tautan reset password</p>
</div>

<?php if (isset($message)): ?>
  <div class="bg-emerald-500/10 border border-emerald-500/30 p-3.5 rounded-xl">
    <div class="flex items-start gap-2.5">
      <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-400 mt-0.5 flex-shrink-0"></i>
      <p class="text-xs sm:text-sm text-emerald-300 leading-relaxed"><?= htmlspecialchars($message) ?></p>
    </div>
  </div>
<?php endif; ?>

<?php if (isset($error)): ?>
  <div class="bg-red-500/10 border border-red-500/30 p-3.5 rounded-xl">
    <div class="flex items-start gap-2.5">
      <i data-lucide="alert-circle" class="w-4 h-4 text-red-400 mt-0.5 flex-shrink-0"></i>
      <p class="text-xs sm:text-sm text-red-300 leading-relaxed"><?= htmlspecialchars($error) ?></p>
    </div>
  </div>
<?php endif; ?>

<form data-spa method="POST" action="<?= getBaseUrl('/password/forgot') ?>" class="space-y-5">
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
        placeholder="nama@email.com"
        required>
    </div>
  </div>

  <div class="pt-1">
    <button type="submit" id="forgot-button" class="w-full flex justify-center items-center gap-2 py-3.5 px-4 border border-transparent rounded-xl shadow-lg shadow-[#4368f5]/25 text-sm font-bold text-white bg-[#4368f5] hover:bg-[#3658db] active:scale-[0.99] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-[#181c36] focus:ring-[#4368f5] disabled:opacity-50 disabled:cursor-not-allowed transition-all cursor-pointer">
      <span class="button-text inline-flex items-center gap-2">
        <span>Kirim Link Reset</span>
        <i data-lucide="arrow-right" class="w-4 h-4"></i>
      </span>
      <span class="button-loading hidden items-center gap-2">
        <span class="animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent"></span>
        <span>Mengirim...</span>
      </span>
    </button>
  </div>

  <script>
    (function() {
      const form = document.querySelector('form');
      const button = document.getElementById('forgot-button');
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
    Ingat password Anda?
    <a data-spa href="<?= getBaseUrl('/login') ?>" class="font-semibold text-[#5b82ff] hover:text-[#7c9cff] transition-colors">Kembali ke Masuk</a>
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
      title: 'Gagal Kirim Link',
      text: <?= json_encode($error) ?>,
      background: '#181c36',
      color: '#f1f5f9',
      confirmButtonColor: '#4368f5'
    });
  </script>
<?php endif; ?>
