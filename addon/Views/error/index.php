<div class="min-h-[70vh] flex items-center justify-center px-4 sm:px-6 lg:px-8">
  <div class="max-w-md w-full bg-white rounded-3xl shadow-lg border border-slate-200/80 p-8 text-center space-y-6">
    <!-- Animated Icon -->
    <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-indigo-50 border border-indigo-100 text-indigo-600 animate-pulse">
      <svg class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
      </svg>
    </div>

    <?php
    $codeVal = (int)($code ?? 403);
    if ($codeVal === 404) {
      $title = 'Halaman Tidak Ditemukan';
      $desc = 'Maaf, halaman yang Anda cari tidak tersedia atau telah dipindahkan.';
    } elseif ($codeVal === 419) {
      $title = 'Sesi Keamanan Kedaluwarsa';
      $desc = 'Sesi keamanan Anda telah habis atau terjadi mismatch token CSRF. Silakan muat ulang halaman.';
    } else {
      $title = 'Akses Tidak Diizinkan';
      $desc = 'Maaf, akun Anda tidak memiliki hak akses (permissions) yang memadai untuk membuka halaman ini.';
    }
    ?>
    <!-- Error Headers -->
    <div class="space-y-2">
      <span class="text-xs font-bold text-indigo-600 uppercase tracking-widest bg-indigo-50 px-3 py-1 rounded-full">Error <?= htmlspecialchars($codeVal) ?></span>
      <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight"><?= $title ?></h2>
      <p class="text-sm text-slate-500 max-w-xs mx-auto">
        <?= $desc ?>
      </p>
    </div>

    <!-- Error Diagnostic Message Box -->
    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 text-left">
      <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Detail Masalah</span>
      <p class="text-xs font-medium text-slate-650 leading-relaxed font-mono break-words">
        <?= htmlspecialchars($message ?? 'Forbidden') ?>
      </p>
    </div>

    <!-- Action Buttons -->
    <div class="pt-2">
      <a
        href="<?= getBaseUrl('/dashboard') ?>"
        class="w-full inline-flex items-center justify-center px-6 py-3 border border-transparent rounded-full shadow-md text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all cursor-pointer hover:-translate-y-0.5">
        Kembali ke Dashboard
      </a>
    </div>
  </div>
</div>