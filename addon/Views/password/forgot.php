<div class="text-center">
  <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Lupa Password</h1>
  <p class="mt-2 text-sm text-slate-500">Masukkan email Anda untuk menerima link reset</p>
</div>

<?php if (isset($message)): ?>
  <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-md">
    <p class="text-sm text-emerald-700"><?= htmlspecialchars($message) ?></p>
  </div>
<?php endif; ?>

<?php if (isset($error)): ?>
  <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-md">
    <p class="text-sm text-red-700"><?= htmlspecialchars($error) ?></p>
  </div>
<?php endif; ?>

<form data-spa method="POST" action="<?= getBaseUrl('/password/forgot') ?>" class="space-y-5">
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
    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-full shadow-md text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all hover:-translate-y-0.5">
      Kirim Link Reset
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
      title: 'Gagal Kirim Link',
      text: <?= json_encode($error) ?>,
      confirmButtonColor: '#4f46e5'
    });
  </script>
<?php endif; ?>