<?php
?>
<div class="text-left space-y-3">
  <div class="inline-flex items-center justify-center h-11 w-11 rounded-xl bg-[#4368f5]/15 border border-[#4368f5]/30 text-[#5b82ff]">
    <i data-lucide="shield-check" class="w-5 h-5"></i>
  </div>
  <div>
    <h1 class="text-3xl font-extrabold text-white tracking-tight">Verifikasi Email</h1>
    <p class="mt-1.5 text-sm text-slate-400 leading-relaxed">
      Masukkan kode 6-digit yang telah dikirim ke
      <strong class="text-slate-200 break-all"><?= htmlspecialchars($email ?? '') ?></strong>
    </p>
  </div>
</div>

<?php if (isset($error)): ?>
  <div class="bg-red-500/10 border border-red-500/30 p-3.5 rounded-xl">
    <div class="flex items-start gap-2.5">
      <i data-lucide="alert-circle" class="w-4 h-4 text-red-400 mt-0.5 flex-shrink-0"></i>
      <p class="text-xs sm:text-sm text-red-300 leading-relaxed"><?= htmlspecialchars($error) ?></p>
    </div>
  </div>
<?php endif; ?>

<?php if (isset($success)): ?>
  <div class="bg-emerald-500/10 border border-emerald-500/30 p-3.5 rounded-xl">
    <div class="flex items-start gap-2.5">
      <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-400 mt-0.5 flex-shrink-0"></i>
      <p class="text-xs sm:text-sm text-emerald-300 leading-relaxed"><?= htmlspecialchars($success) ?></p>
    </div>
  </div>
<?php endif; ?>

<form data-spa method="POST" action="<?= getBaseUrl('/verify-otp') ?>" id="otp-form" class="space-y-5">
  <input type="hidden" name="email" value="<?= htmlspecialchars($email ?? '') ?>">

  <div class="grid grid-cols-6 gap-2 sm:gap-2.5" role="group" aria-label="Kode verifikasi 6 digit">
    <input
      type="text"
      maxlength="1"
      pattern="[0-9]"
      inputmode="numeric"
      autocomplete="one-time-code"
      aria-label="Digit pertama"
      required
      class="otp-digit w-full text-center py-3 border border-[#323859] rounded-xl focus:outline-none focus:ring-2 focus:ring-[#4368f5]/40 focus:border-[#4368f5] font-bold text-xl text-white bg-[#212642] transition-all"
      data-index="0">
    <input
      type="text"
      maxlength="1"
      pattern="[0-9]"
      inputmode="numeric"
      autocomplete="off"
      aria-label="Digit kedua"
      required
      class="otp-digit w-full text-center py-3 border border-[#323859] rounded-xl focus:outline-none focus:ring-2 focus:ring-[#4368f5]/40 focus:border-[#4368f5] font-bold text-xl text-white bg-[#212642] transition-all"
      data-index="1">
    <input
      type="text"
      maxlength="1"
      pattern="[0-9]"
      inputmode="numeric"
      autocomplete="off"
      aria-label="Digit ketiga"
      required
      class="otp-digit w-full text-center py-3 border border-[#323859] rounded-xl focus:outline-none focus:ring-2 focus:ring-[#4368f5]/40 focus:border-[#4368f5] font-bold text-xl text-white bg-[#212642] transition-all"
      data-index="2">
    <input
      type="text"
      maxlength="1"
      pattern="[0-9]"
      inputmode="numeric"
      autocomplete="off"
      aria-label="Digit keempat"
      required
      class="otp-digit w-full text-center py-3 border border-[#323859] rounded-xl focus:outline-none focus:ring-2 focus:ring-[#4368f5]/40 focus:border-[#4368f5] font-bold text-xl text-white bg-[#212642] transition-all"
      data-index="3">
    <input
      type="text"
      maxlength="1"
      pattern="[0-9]"
      inputmode="numeric"
      autocomplete="off"
      aria-label="Digit kelima"
      required
      class="otp-digit w-full text-center py-3 border border-[#323859] rounded-xl focus:outline-none focus:ring-2 focus:ring-[#4368f5]/40 focus:border-[#4368f5] font-bold text-xl text-white bg-[#212642] transition-all"
      data-index="4">
    <input
      type="text"
      maxlength="1"
      pattern="[0-9]"
      inputmode="numeric"
      autocomplete="off"
      aria-label="Digit keenam"
      required
      class="otp-digit w-full text-center py-3 border border-[#323859] rounded-xl focus:outline-none focus:ring-2 focus:ring-[#4368f5]/40 focus:border-[#4368f5] font-bold text-xl text-white bg-[#212642] transition-all"
      data-index="5">
  </div>

  <input type="hidden" name="otp_code" id="otp-code-hidden" required>

  <button type="submit" class="w-full flex justify-center items-center gap-2 py-3.5 px-4 border border-transparent rounded-xl shadow-lg shadow-[#4368f5]/25 text-sm font-bold text-white bg-[#4368f5] hover:bg-[#3658db] active:scale-[0.99] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-[#181c36] focus:ring-[#4368f5] disabled:opacity-50 disabled:cursor-not-allowed transition-all cursor-pointer" id="verify-button" disabled>
    <span class="button-text inline-flex items-center gap-2">
      <span>Verifikasi</span>
      <i data-lucide="arrow-right" class="w-4 h-4"></i>
    </span>
    <span class="button-loading hidden items-center gap-2">
      <span class="animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent"></span>
      <span>Memverifikasi...</span>
    </span>
  </button>
</form>

<div class="mt-6 pt-6 border-t border-white/[0.08] text-center space-y-4">
  <div class="otp-timer inline-flex items-center gap-2 px-3.5 py-2 bg-[#212642] border border-[#323859] rounded-xl text-xs font-medium text-slate-300" id="otp-timer">
    <i data-lucide="clock" class="w-3.5 h-3.5 text-[#5b82ff]"></i>
    <span id="timer-text">Kode berlaku 15:00</span>
  </div>

  <button
    type="button"
    class="w-full py-3 px-4 border border-[#4368f5]/50 text-[#7c9cff] bg-[#4368f5]/10 rounded-xl text-sm font-semibold hover:bg-[#4368f5]/20 disabled:border-white/10 disabled:bg-white/[0.03] disabled:text-slate-500 disabled:cursor-not-allowed transition-all cursor-pointer"
    id="resend-button"
    disabled
    data-spa>
    <span>Kirim Ulang OTP</span>
    <span class="resend-countdown"></span>
  </button>

  <a data-spa href="<?= getBaseUrl('/register') ?>" class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-400 hover:text-[#5b82ff] transition-colors">
    <i data-lucide="arrow-left" class="w-4 h-4"></i>
    <span>Kembali ke Daftar</span>
  </a>
</div>

<script>
  (function() {
    const inputs = document.querySelectorAll('.otp-digit');
    const form = document.getElementById('otp-form');
    const verifyButton = document.getElementById('verify-button');
    const otpHidden = document.getElementById('otp-code-hidden');
    const timerText = document.getElementById('timer-text');
    const resendButton = document.getElementById('resend-button');
    const resendCountdown = resendButton.querySelector('.resend-countdown');

    let timeLeft = 900;
    let resendCooldown = 60;

    inputs[0].focus();

    inputs.forEach((input, index) => {
      input.addEventListener('input', (e) => {
        const value = e.target.value;

        if (!/^\d*$/.test(value)) {
          e.target.value = '';
          return;
        }

        if (value.length === 1 && index < inputs.length - 1) {
          inputs[index + 1].focus();
        }

        checkAllFilled();
      });

      input.addEventListener('keydown', (e) => {
        if (e.key === 'Backspace' && input.value === '' && index > 0) {
          inputs[index - 1].focus();
        }
      });

      input.addEventListener('paste', (e) => {
        e.preventDefault();
        const pasted = e.clipboardData.getData('text').slice(0, 6);

        if (/^\d{6}$/.test(pasted)) {
          inputs.forEach((inp, i) => {
            inp.value = pasted[i];
            if (i < 5) inputs[i + 1].focus();
          });
          checkAllFilled();
        }
      });
    });

    function checkAllFilled() {
      const allFilled = Array.from(inputs).every(i => i.value.length === 1);
      if (allFilled) {
        verifyButton.disabled = false;
        otpHidden.value = Array.from(inputs).map(i => i.value).join('');
      } else {
        verifyButton.disabled = true;
        otpHidden.value = '';
      }
    }

    form.addEventListener('submit', (e) => {
      verifyButton.disabled = true;
      verifyButton.querySelector('.button-text').style.display = 'none';
      verifyButton.querySelector('.button-loading').style.display = 'inline-flex';
    });

    function startTimer() {
      const interval = setInterval(() => {
        if (timeLeft <= 0) {
          clearInterval(interval);
          timerText.textContent = 'Kode telah kedaluwarsa';
          timerText.closest('.otp-timer').classList.add('bg-red-500/10', 'border-red-500/30', 'text-red-300');
          return;
        }

        timeLeft--;
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        timerText.textContent = `Kode berlaku ${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

        if (timeLeft < 60) {
          timerText.closest('.otp-timer').classList.add('bg-red-500/10', 'border-red-500/30', 'text-red-300', 'animate-pulse');
        }
      }, 1000);
    }

    function startResendCooldown() {
      resendButton.disabled = true;
      let cooldown = resendCooldown;

      const interval = setInterval(() => {
        if (cooldown <= 0) {
          clearInterval(interval);
          resendButton.disabled = false;
          resendCountdown.textContent = '';
          return;
        }

        cooldown--;
        resendCountdown.textContent = `(${cooldown}s)`;
      }, 1000);
    }

    resendButton.addEventListener('click', () => {
      if (!resendButton.disabled) {
        window.location.href = '<?= getBaseUrl('/resend-otp?email=') ?>' + encodeURIComponent('<?= htmlspecialchars($email ?? '') ?>');
      }
    });

    startTimer();
    startResendCooldown();
  })();
</script>

<?php if (isset($error)): ?>
  <script>
    Swal.fire({
      icon: 'error',
      title: 'Verifikasi Gagal',
      text: <?= json_encode($error) ?>,
      background: '#181c36',
      color: '#f1f5f9',
      confirmButtonColor: '#4368f5'
    });
  </script>
<?php endif; ?>
