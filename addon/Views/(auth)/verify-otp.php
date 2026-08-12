<?php

/**
 * @var \App\Core\View\PageMeta $meta
 * @var string $email
 * @var string|null $error
 */
?>

<div class="text-center">
  <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 text-indigo-600 text-2xl">🔐</div>
  <h1 class="mt-4 text-3xl font-extrabold text-slate-900 tracking-tight">Verifikasi Email</h1>
  <p class="mt-2 text-sm text-slate-500">
    Kami telah mengirim kode 6-digit ke<br>
    <strong class="text-slate-800 break-all"><?= htmlspecialchars($email ?? '') ?></strong>
  </p>
</div>

<?php if (isset($error)): ?>
  <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-md">
    <div class="flex">
      <div class="flex-shrink-0">
        <span class="text-red-500">⚠️</span>
      </div>
      <div class="ml-3">
        <p class="text-sm text-red-700"><?= htmlspecialchars($error) ?></p>
      </div>
    </div>
  </div>
<?php endif; ?>

<?php if (isset($success)): ?>
  <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-md">
    <div class="flex">
      <div class="flex-shrink-0">
        <span class="text-emerald-500">✅</span>
      </div>
      <div class="ml-3">
        <p class="text-sm text-emerald-700"><?= htmlspecialchars($success) ?></p>
      </div>
    </div>
  </div>
<?php endif; ?>

<form data-spa method="POST" action="<?= getBaseUrl('/verify-otp') ?>" id="otp-form" class="space-y-6">
  <input type="hidden" name="email" value="<?= htmlspecialchars($email ?? '') ?>">

  <div class="grid grid-cols-6 gap-2 sm:gap-3" role="group" aria-label="Kode verifikasi 6 digit">
    <input
      type="text"
      maxlength="1"
      pattern="[0-9]"
      inputmode="numeric"
      autocomplete="one-time-code"
      aria-label="Digit pertama"
      required
      class="otp-digit w-full text-center py-3 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 font-bold text-xl bg-slate-50 transition-all"
      data-index="0">
    <input
      type="text"
      maxlength="1"
      pattern="[0-9]"
      inputmode="numeric"
      autocomplete="off"
      aria-label="Digit kedua"
      required
      class="otp-digit w-full text-center py-3 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 font-bold text-xl bg-slate-50 transition-all"
      data-index="1">
    <input
      type="text"
      maxlength="1"
      pattern="[0-9]"
      inputmode="numeric"
      autocomplete="off"
      aria-label="Digit ketiga"
      required
      class="otp-digit w-full text-center py-3 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 font-bold text-xl bg-slate-50 transition-all"
      data-index="2">
    <input
      type="text"
      maxlength="1"
      pattern="[0-9]"
      inputmode="numeric"
      autocomplete="off"
      aria-label="Digit keempat"
      required
      class="otp-digit w-full text-center py-3 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 font-bold text-xl bg-slate-50 transition-all"
      data-index="3">
    <input
      type="text"
      maxlength="1"
      pattern="[0-9]"
      inputmode="numeric"
      autocomplete="off"
      aria-label="Digit kelima"
      required
      class="otp-digit w-full text-center py-3 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 font-bold text-xl bg-slate-50 transition-all"
      data-index="4">
    <input
      type="text"
      maxlength="1"
      pattern="[0-9]"
      inputmode="numeric"
      autocomplete="off"
      aria-label="Digit keenam"
      required
      class="otp-digit w-full text-center py-3 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 font-bold text-xl bg-slate-50 transition-all"
      data-index="5">
  </div>

  <input type="hidden" name="otp_code" id="otp-code-hidden" required>

  <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-full shadow-md text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all" id="verify-button" disabled>
    <span class="button-text">Verifikasi</span>
    <span class="button-loading hidden items-center gap-2">
      <span class="animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent"></span>
      Memverifikasi...
    </span>
  </button>
</form>

<div class="mt-8 pt-6 border-t border-slate-200 text-center space-y-4">
  <div class="otp-timer inline-flex items-center gap-2 px-4 py-2 bg-slate-100 rounded-lg text-sm text-slate-600" id="otp-timer">
    <span>⏱️</span>
    <span id="timer-text">Kode berlaku 15:00</span>
  </div>

  <button
    type="button"
    class="w-full py-2.5 px-4 border border-indigo-600 text-indigo-600 rounded-xl text-sm font-semibold hover:bg-indigo-50 disabled:border-slate-200 disabled:text-slate-400 disabled:cursor-not-allowed transition-all"
    id="resend-button"
    disabled
    data-spa>
    <span>Kirim Ulang OTP</span>
    <span class="resend-countdown"></span>
  </button>

  <a data-spa href="/register" class="inline-block text-sm font-semibold text-slate-500 hover:text-indigo-600 transition-colors">
    ← Kembali ke Register
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
          timerText.closest('.otp-timer').classList.add('bg-red-50', 'text-red-700');
          return;
        }

        timeLeft--;
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        timerText.textContent = `Kode berlaku ${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

        if (timeLeft < 60) {
          timerText.closest('.otp-timer').classList.add('bg-red-50', 'text-red-700', 'animate-pulse');
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
        window.location.href = '/resend-otp?email=' + encodeURIComponent('<?= htmlspecialchars($email ?? '') ?>');
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
      confirmButtonColor: '#4f46e5'
    });
  </script>
<?php endif; ?>