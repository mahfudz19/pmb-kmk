<?php

/**
 * @var \App\Core\View\PageMeta $meta
 * @var string $email
 */
?>

<div class="text-center">
  <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-indigo-100 text-indigo-600 text-3xl animate-bounce">📧</div>
  <h1 class="mt-4 text-3xl font-extrabold text-slate-900 tracking-tight">Email Terkirim!</h1>
  <p class="mt-2 text-sm text-slate-500">
    Kami telah mengirim kode verifikasi ke<br>
    <strong class="text-slate-800 break-all"><?= htmlspecialchars($email ?? '') ?></strong>
  </p>
</div>

<div class="bg-slate-50 rounded-2xl p-6 border border-slate-100 space-y-4">
  <div class="flex items-center gap-3">
    <span class="flex items-center justify-center w-7 h-7 bg-indigo-600 text-white rounded-full text-xs font-bold">1</span>
    <span class="text-sm text-slate-700">Buka inbox email Anda</span>
  </div>
  <div class="flex items-center gap-3">
    <span class="flex items-center justify-center w-7 h-7 bg-indigo-600 text-white rounded-full text-xs font-bold">2</span>
    <span class="text-sm text-slate-700">Cari email dari <?= htmlspecialchars(env('MAIL_FROM_NAME', 'PMB KMK')) ?></span>
  </div>
  <div class="flex items-center gap-3">
    <span class="flex items-center justify-center w-7 h-7 bg-indigo-600 text-white rounded-full text-xs font-bold">3</span>
    <span class="text-sm text-slate-700">Salin kode 6 digit dari email</span>
  </div>
  <div class="flex items-center gap-3">
    <span class="flex items-center justify-center w-7 h-7 bg-indigo-600 text-white rounded-full text-xs font-bold">4</span>
    <span class="text-sm text-slate-700">Masukkan kode di halaman verifikasi</span>
  </div>
</div>

<div class="space-y-3">
  <a
    href="<?= getBaseUrl('/verify-otp?email=' . urlencode($email ?? '')) ?>"
    class="w-full flex justify-center py-3 px-4 border border-transparent rounded-full shadow-md text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 transition-all hover:-translate-y-0.5"
    data-spa>
    Buka Halaman Verifikasi
  </a>

  <button
    type="button"
    class="w-full py-3 px-4 border border-indigo-600 text-indigo-600 rounded-full text-sm font-semibold hover:bg-indigo-50 disabled:border-slate-200 disabled:text-slate-400 disabled:cursor-not-allowed transition-all"
    id="resend-from-sent"
    disabled>
    <span>Kirim Ulang Email</span>
    <span class="button-countdown"></span>
  </button>
</div>

<div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 space-y-2">
  <p class="font-semibold text-amber-800 text-sm">💡 Tips:</p>
  <ul class="list-disc pl-5 text-amber-700 text-xs space-y-1">
    <li>Periksa folder spam/junk jika email tidak muncul di inbox</li>
    <li>Pastikan alamat email yang Anda masukkan sudah benar</li>
    <li>Kode verifikasi berlaku selama 15 menit</li>
  </ul>
</div>

<div class="text-center pt-2">
  <a data-spa href="<?= getBaseUrl('/register') ?>" class="text-sm font-semibold text-slate-500 hover:text-indigo-600 transition-colors">
    ← Kembali ke Register
  </a>
</div>

<script>
  (function() {
    const resendButton = document.getElementById('resend-from-sent');
    const countdownSpan = resendButton.querySelector('.button-countdown');
    let cooldown = 60;

    function startCooldown() {
      const interval = setInterval(() => {
        if (cooldown <= 0) {
          clearInterval(interval);
          resendButton.disabled = false;
          countdownSpan.textContent = '';
          return;
        }

        cooldown--;
        countdownSpan.textContent = `(${cooldown}s)`;
      }, 1000);
    }

    resendButton.addEventListener('click', () => {
      if (!resendButton.disabled) {
        window.location.href = '<?= getBaseUrl('/resend-otp?email=') ?>' + encodeURIComponent('<?= htmlspecialchars($email ?? '') ?>');
      }
    });

    startCooldown();
  })();
</script>