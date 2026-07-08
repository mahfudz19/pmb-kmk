<?php

/**
 * @var \App\Core\View\PageMeta $meta
 * @var string $children
 */
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <?= App\Core\View\View::renderMeta($meta) ?>

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">

  <!-- Auto-Injected Styles -->
  <?= App\Core\View\View::renderStyles() ?>

  <!-- Tailwind CSS -->
  <link rel="stylesheet" href="<?= asset('assets/tailwind.css') ?>?v=<?= filemtime(__DIR__ . '/tailwind.css') ?>">

</head>

<body class="bg-slate-50 text-slate-900 font-sans min-h-screen flex flex-col">
  <!-- Global Loading Progress Bar -->
  <div id="global-progress-bar" class="progress-bar-container">
    <div id="global-progress-bar-inner" class="progress-bar-fill"></div>
  </div>

  <div class="flex-grow flex flex-col">
    <div class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 flex-grow flex flex-col">
      <!-- Global Header -->
      <header class="flex justify-between items-center py-3 border-b border-slate-200/80 bg-transparent">
        <a data-spa href="<?= getBaseUrl('/') ?>" class="flex items-center gap-3 transition-all hover:opacity-90 active:scale-[0.98]">
          <img src="<?= getBaseUrl('/logo_app/mazu-logo.svg') ?>" alt="PMB KMK" height="40" class="h-10 w-auto" />
        </a>
        <nav class="flex items-center gap-6">
          <?php if (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true): ?>
            <a data-spa href="<?= getBaseUrl('/dashboard') ?>" class="text-sm font-semibold text-slate-600 hover:text-slate-900 transition-colors">Dashboard</a>
            <div class="flex items-center gap-3 bg-slate-100/80 border border-slate-200/60 rounded-full pl-3 pr-2 py-1">
              <span class="flex items-center justify-center w-5 h-5 rounded-full bg-indigo-100 text-indigo-600 text-[10px]">👤</span>
              <span class="text-xs font-semibold text-slate-700 max-w-[120px] truncate"><?= htmlspecialchars($_SESSION['auth.user_name'] ?? '') ?></span>
              <span class="text-[9px] font-bold text-slate-400 border border-slate-300 rounded px-1 uppercase"><?= htmlspecialchars($_SESSION['auth.user_role'] ?? 'user') ?></span>
              <form id="logout-form" action="<?= getBaseUrl('/logout') ?>" method="POST" class="inline" onsubmit="openLogoutModal(event)">
                <button type="submit" class="text-xs font-bold text-red-600 hover:text-red-800 bg-transparent border-none cursor-pointer px-2 py-1 rounded-full hover:bg-red-50 transition-all">Logout</button>
              </form>
            </div>
          <?php else: ?>
            <?php
              $currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
              $isLogin = str_ends_with($currentPath, '/login');
              $isRegister = str_ends_with($currentPath, '/register');
            ?>
            <?php if (!$isLogin): ?>
              <a href="<?= getBaseUrl('/login') ?>" class="text-sm font-semibold text-slate-600 hover:text-indigo-600 transition-colors">Login</a>
            <?php endif; ?>
            <?php if (!$isRegister): ?>
              <a href="<?= getBaseUrl('/register') ?>" class="text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 px-5 py-2.5 rounded-xl shadow-sm hover:shadow hover:-translate-y-0.5 transition-all">Daftar</a>
            <?php endif; ?>
          <?php endif; ?>
        </nav>
      </header>

      <main id="app-content" data-layout="layout.php" class="flex-grow flex flex-col py-10">
        <?= $children; ?>
      </main>

      <!-- Global Footer -->
      <footer class="flex flex-col sm:flex-row justify-between items-center py-5 border-t border-slate-200/80 text-slate-400 text-xs gap-4">
        <div class="flex items-center gap-6">
          <span>&copy; <?= date('Y') ?> PMB KMK. All rights reserved.</span>
          <span class="hidden sm:inline text-slate-200">|</span>
          <div class="hidden sm:flex gap-4">
            <a href="#" class="hover:text-slate-600 transition-colors">Panduan</a>
            <a href="#" class="hover:text-slate-600 transition-colors">Bantuan</a>
            <a href="#" class="hover:text-slate-600 transition-colors">Hubungi Kami</a>
          </div>
        </div>
        <div class="bg-slate-100 text-slate-500 px-3 py-1 rounded-full font-semibold">
          v1.0.0
        </div>
      </footer>
    </div>
  </div>

  <!-- SPA Script -->
  <?= App\Core\View\View::renderScripts() ?>
  
  <!-- Logout Confirmation Modal -->
  <div id="logout-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closeLogoutModal()"></div>
    <div class="relative bg-white rounded-2xl p-6 max-w-sm w-full shadow-xl border border-slate-100 space-y-6 transform scale-95 opacity-0 transition-all duration-200" id="logout-modal-card">
      <div class="text-center space-y-2">
        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 text-red-650 text-xl">⚠️</div>
        <h3 class="text-lg font-bold text-slate-900">Konfirmasi Logout</h3>
        <p class="text-sm text-slate-500">Apakah Anda yakin ingin keluar dari sistem PMB KMK?</p>
      </div>
      <div class="flex gap-3">
        <button type="button" onclick="closeLogoutModal()" class="flex-1 py-2.5 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-sm transition-colors focus:outline-none">
          Batal
        </button>
        <button type="button" onclick="confirmLogout()" class="flex-1 py-2.5 px-4 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl text-sm transition-colors shadow-sm focus:outline-none">
          Ya, Keluar
        </button>
      </div>
    </div>
  </div>

  <script>
    function togglePasswordVisibility(inputId, btn) {
      const input = document.getElementById(inputId);
      const eyeOpen = btn.querySelector('.eye-open');
      const eyeClosed = btn.querySelector('.eye-closed');
      if (input.type === 'password') {
        input.type = 'text';
        eyeOpen.classList.add('hidden');
        eyeClosed.classList.remove('hidden');
      } else {
        input.type = 'password';
        eyeOpen.classList.remove('hidden');
        eyeClosed.classList.add('hidden');
      }
    }

    function openLogoutModal(event) {
      event.preventDefault();
      const modal = document.getElementById('logout-modal');
      const card = document.getElementById('logout-modal-card');
      modal.classList.remove('hidden');
      setTimeout(() => {
        card.classList.remove('scale-95', 'opacity-0');
        card.classList.add('scale-100', 'opacity-100');
      }, 10);
    }

    function closeLogoutModal() {
      const modal = document.getElementById('logout-modal');
      const card = document.getElementById('logout-modal-card');
      card.classList.remove('scale-100', 'opacity-100');
      card.classList.add('scale-95', 'opacity-0');
      setTimeout(() => {
        modal.classList.add('hidden');
      }, 200);
    }

    function confirmLogout() {
      const form = document.getElementById('logout-form');
      form.submit();
    }
  </script>
</body>

</html>