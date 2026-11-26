<?php
?>
<div class="text-left space-y-3">
  <div class="inline-flex items-center justify-center h-11 w-11 rounded-xl bg-[#4368f5]/15 border border-[#4368f5]/30 text-[#5b82ff]">
    <i data-lucide="mail-check" class="w-5 h-5"></i>
  </div>
  <div>
    <h1 class="text-3xl font-extrabold text-white tracking-tight">Email Terkirim!</h1>
    <p class="mt-1.5 text-sm text-slate-400 leading-relaxed">
      Kode verifikasi telah dikirim ke
      <strong class="text-slate-200 break-all"><?= htmlspecialchars($email ?? '') ?></strong>
    </p>
  </div>
</div>

<div class="bg-[#212642]/80 rounded-2xl p-5 border border-[#323859] space-y-3.5">
  <div class="flex items-center gap-3">
    <span class="flex items-center justify-center w-6 h-6 bg-[#4368f5] text-white rounded-full text-xs font-bold flex-shrink-0">1</span>
    <span class="text-xs sm:text-sm text-slate-200">Buka inbox email Anda</span>
  </div>
  <div class="flex items-center gap-3">
    <span class="flex items-center justify-center w-6 h-6 bg-[#4368f5] text-white rounded-full text-xs font-bold flex-shrink-0">2</span>
    <span class="text-xs sm:text-sm text-slate-200">Cari email dari <?= htmlspecialchars(env('MAIL_FROM_NAME', 'PMB KMK')) ?></span>
  </div>
  <div class="flex items-center gap-3">
    <span class="flex items-center justify-center w-6 h-6 bg-[#4368f5] text-white rounded-full text-xs font-bold flex-shrink-0">3</span>
    <span class="text-xs sm:text-sm text-slate-200">Salin kode 6 digit dari email</span>
  </div>
  <div class="flex items-center gap-3">
    <span class="flex items-center justify-center w-6 h-6 bg-[#4368f5] text-white rounded-full text-xs font-bold flex-shrink-0">4</span>
    <span class="text-xs sm:text-sm text-slate-200">Masukkan kode di halaman verifikasi</span>
  </div>
</div>

<div class="space-y-3">
  <a
    href="<?= getBaseUrl('/verify-otp?email=' . urlencode($email ?? '')) ?>"
    class="w-full flex justify-center items-center gap-2 py-3.5 px-4 border border-transparent rounded-xl shadow-lg shadow-[#4368f5]/25 text-sm font-bold text-white bg-[#4368f5] hover:bg-[#3658db] active:scale-[0.99] transition-all"
    data-spa>
    <span>Buka Halaman Verifikasi</span>
    <i data-lucide="arrow-right" class="w-4 h-4"></i>
  </a>

  <button
    type="button"
    class="w-full py-3 px-4 border border-[#4368f5]/50 text-[#7c9cff] bg-[#4368f5]/10 rounded-xl text-sm font-semibold hover:bg-[#4368f5]/20 disabled:border-white/10 disabled:bg-white/[0.03] disabled:text-slate-500 disabled:cursor-not-allowed transition-all cursor-pointer"
    id="resend-from-sent"
    disabled>
    <span>Kirim Ulang Email</span>
    <span class="button-countdown"></span>
  </button>
</div>

<div class="bg-amber-500/10 border border-amber-400/25 rounded-xl p-4 space-y-1.5">
  <p class="font-semibold text-amber-300 text-xs flex items-center gap-1.5">
    <i data-lucide="info" class="w-3.5 h-3.5"></i>
    <span>Tips</span>
  </p>
  <ul class="list-disc pl-5 text-amber-200/80 text-xs space-y-1">
    <li>Periksa folder spam/junk jika email tidak muncul di inbox</li>
    <li>Pastikan alamat email yang Anda masukkan sudah benar</li>
    <li>Kode verifikasi berlaku selama 15 menit</li>
  </ul>
</div>

<div class="text-center pt-1">
  <a data-spa href="<?= getBaseUrl('/register') ?>" class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-400 hover:text-[#5b82ff] transition-colors">
    <i data-lucide="arrow-left" class="w-4 h-4"></i>
    <span>Kembali ke Daftar</span>
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
