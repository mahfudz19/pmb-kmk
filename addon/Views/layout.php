<?php
/**
 * @var \App\Core\View\PageMeta $meta
 * @var string $children
 */
$userId = $_SESSION['auth.user_id'] ?? null;
$unreadCount = 0;
$notifications = [];
if ($userId) {
    try {
        $db = \App\Core\Foundation\Application::getInstance()->getContainer()->resolve(\App\Core\Database\DatabaseManager::class)->connection();
        $stmt = $db->prepare("SELECT * FROM notifications WHERE user_id = :user_id OR user_id IS NULL ORDER BY created_at DESC LIMIT 5");
        $stmt->execute(['user_id' => $userId]);
        $notifications = $stmt->fetchAll();

        $stmtCount = $db->prepare("SELECT COUNT(*) as count FROM notifications WHERE (user_id = :user_id OR user_id IS NULL) AND is_read = 0");
        $stmtCount->execute(['user_id' => $userId]);
        $unreadCount = (int)($stmtCount->fetch()['count'] ?? 0);
    } catch (\Throwable $e) {
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <?= App\Core\View\View::renderMeta($meta) ?>

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <!-- Auto-Injected Styles -->
  <?= App\Core\View\View::renderStyles() ?>

  <!-- Tailwind CSS -->
  <link rel="stylesheet" href="<?= asset('assets/tailwind.css') ?>?v=<?= filemtime(__DIR__ . '/tailwind.css') ?>">

  <!-- Lucide Icons CDN -->
  <script src="https://cdn.jsdelivr.net/npm/lucide@0.460.0/dist/umd/lucide.min.js"></script>
  <!-- SweetAlert2 CDN -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    body {
      font-family: 'Inter', sans-serif;
    }

    .accordion-content {
      max-height: 0;
      overflow: hidden;
      transition: max-height 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .accordion-content.open {
      max-height: 400px;
    }

    .sidebar-collapsed-label {
      opacity: 1;
      transition: opacity 0.15s ease-in-out;
    }

    .sidebar-collapsed .sidebar-collapsed-label {
      opacity: 0;
      width: 0;
      height: 0;
      overflow: hidden;
      display: none;
    }

    .sidebar-collapsed .sidebar-collapsed-hide {
      display: none !important;
    }

    .sidebar-collapsed .sidebar-collapsed-center {
      justify-content: center;
      padding-left: 0;
      padding-right: 0;
    }

    .sidebar-collapsed .sidebar-collapsed-logo-text {
      display: none;
    }

    .dropdown-animate {
      transform: scale(0.95);
      opacity: 0;
      pointer-events: none;
      transition: transform 0.15s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.15s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .dropdown-animate.open {
      transform: scale(1);
      opacity: 1;
      pointer-events: auto;
    }

    #sidebar-container {
      height: calc(100vh - 3.5rem) !important;
      overflow-y: auto !important;
    }

    @media (min-width: 768px) {
      .is-logged-in #sidebar-container {
        width: 16rem;
        transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1), padding 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      }
      .is-logged-in #app-content {
        margin-left: 16rem;
        transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      }
      .sidebar-collapsed #sidebar-container {
        width: 4rem !important;
        padding-left: 0.5rem !important;
        padding-right: 0.5rem !important;
      }
      .sidebar-collapsed #app-content {
        margin-left: 4rem !important;
      }
    }
  </style>
</head>

<body class="bg-[#f8fafc] text-slate-900 font-sans antialiased min-h-screen flex flex-col">
  <!-- Global Loading Progress Bar -->
  <div id="global-progress-bar" class="progress-bar-container">
    <div id="global-progress-bar-inner" class="progress-bar-fill"></div>
  </div>

  <?php
    $isLoggedIn = (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true);
    $currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
    $isAuthPage = str_contains($currentPath, '/login') || 
                  str_contains($currentPath, '/register') || 
                  str_contains($currentPath, '/verify-otp') || 
                  str_contains($currentPath, '/otp-sent') || 
                  str_contains($currentPath, '/resend-otp') || 
                  str_contains($currentPath, '/password/forgot') || 
                  str_contains($currentPath, '/password/reset');
    $isDashboard = str_ends_with($currentPath, '/dashboard');
    $isProfile = str_ends_with($currentPath, '/profile');
    $isUsers = str_ends_with($currentPath, '/admin/users');
    $isMaster = str_ends_with($currentPath, '/admin/master');
    $isPayments = str_ends_with($currentPath, '/admin/payments');
    $isVerifications = str_contains($currentPath, '/admin/verifications');
    $isSelection = str_contains($currentPath, '/admin/selection');
    $isAnnouncements = str_contains($currentPath, '/admin/announcements');
    $activeTab = $_GET['tab'] ?? 'academic-year';
  ?>

  <!-- Header -->
  <?php if (!$isAuthPage): ?>
  <header class="fixed top-0 left-0 right-0 z-40 h-14 bg-white/75 backdrop-blur-md border-b border-slate-200/80 px-4 md:px-6 flex items-center justify-between shadow-sm">
    <div class="flex items-center gap-3">
      <?php if ($isLoggedIn): ?>
        <button type="button" id="btn-mobile-toggle" onclick="toggleMobileSidebar()" class="md:hidden p-1.5 text-slate-500 hover:bg-slate-50 rounded-lg transition-colors cursor-pointer focus:outline-none">
          <i data-lucide="menu" class="w-5 h-5"></i>
        </button>
        <button type="button" id="btn-desktop-collapse" onclick="toggleDesktopSidebar()" class="hidden md:flex p-1.5 text-slate-500 hover:bg-slate-50 rounded-lg transition-colors cursor-pointer focus:outline-none">
          <i data-lucide="menu" class="w-5 h-5"></i>
        </button>
      <?php endif; ?>
      <a data-spa href="<?= getBaseUrl('/') ?>" class="flex items-center gap-2.5 transition-all active:scale-[0.98]">
        <img src="<?= getBaseUrl('/logo_app/mazu-logo.svg') ?>" alt="Logo" class="h-7 w-auto" />
      </a>
    </div>

    <div class="flex items-center gap-4">
      <?php if ($isLoggedIn): ?>
        <!-- Notifications Bell & Dropdown -->
        <div class="relative">
          <button type="button" onclick="toggleNotificationsDropdown(event)" class="relative p-1.5 text-slate-500 hover:bg-slate-50 rounded-full transition-colors cursor-pointer focus:outline-none">
            <i data-lucide="bell" class="w-4.5 h-4.5"></i>
            <?php if ($unreadCount > 0): ?>
              <span class="absolute top-1 right-1 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white animate-pulse"></span>
            <?php endif; ?>
          </button>
          
          <!-- Dropdown Card -->
          <div id="notifications-dropdown" style="width: 440px;" class="dropdown-animate absolute right-0 mt-2 max-w-[calc(100vw-2rem)] bg-white border border-slate-200/80 rounded-2xl shadow-lg py-1.5 text-xs text-slate-700 z-50">
            <div class="px-4 py-2 border-b border-slate-100 flex justify-between items-center">
              <span class="font-bold text-slate-800">Notifikasi</span>
              <?php if ($unreadCount > 0): ?>
                <a href="/notifications/mark-read" class="text-[10px] text-indigo-600 hover:text-indigo-750 font-bold">Tandai Semua Dibaca</a>
              <?php endif; ?>
            </div>
            
            <div class="max-h-60 overflow-y-auto divide-y divide-slate-50">
              <?php if (empty($notifications)): ?>
                <div class="px-4 py-6 text-center text-slate-400 font-medium">Tidak ada notifikasi baru.</div>
              <?php else: ?>
                <?php foreach ($notifications as $notif): ?>
                  <div class="px-4 py-3 hover:bg-slate-50 transition-colors flex items-start gap-2.5 <?= !$notif['is_read'] ? 'bg-indigo-50/20' : '' ?>">
                    <span class="text-md mt-0.5 select-none">
                      <?= $notif['type'] === 'success' ? '✅' : ($notif['type'] === 'danger' ? '❌' : ($notif['type'] === 'warning' ? '⚠️' : 'ℹ️')) ?>
                    </span>
                    <div class="space-y-0.5">
                      <p class="font-bold text-slate-800 leading-tight"><?= htmlspecialchars($notif['title']) ?></p>
                      <p class="text-[10px] text-slate-500 leading-normal font-normal"><?= htmlspecialchars($notif['message']) ?></p>
                      <span class="text-[8px] text-slate-400 font-normal block pt-0.5"><?= date('d-m-Y H:i', strtotime($notif['created_at'])) ?></span>
                    </div>
                  </div>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <div class="w-px h-5 bg-slate-200"></div>

        <!-- Profile Dropdown -->
        <div class="relative">
          <button type="button" onclick="toggleUserDropdown(event)" class="flex items-center gap-2 hover:bg-slate-50 p-1.5 rounded-full md:rounded-xl transition-all cursor-pointer focus:outline-none">
            <div class="flex items-center justify-center w-7 h-7 rounded-full bg-indigo-600 text-white font-bold text-xs shadow-sm uppercase">
              <?= substr($_SESSION['auth.user_name'] ?? 'U', 0, 1) ?>
            </div>
            <div class="hidden md:block text-left">
              <div class="text-xs font-semibold text-slate-850 leading-tight max-w-[100px] truncate"><?= htmlspecialchars($_SESSION['auth.user_name'] ?? '') ?></div>
              <div class="text-[9px] text-slate-400 font-bold uppercase tracking-wider mt-0.5"><?= htmlspecialchars($_SESSION['auth.user_role'] ?? 'user') ?></div>
            </div>
            <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-450 hidden md:block transition-transform duration-200" id="dropdown-chevron"></i>
          </button>

          <!-- Dropdown Card -->
          <div id="user-dropdown" class="dropdown-animate absolute right-0 mt-2 w-52 bg-white border border-slate-200/80 rounded-2xl shadow-lg py-1.5 text-xs text-slate-700 z-50">
            <div class="px-4 py-2 border-b border-slate-100 md:hidden">
              <p class="font-bold text-slate-850 truncate"><?= htmlspecialchars($_SESSION['auth.user_name'] ?? '') ?></p>
              <p class="text-[10px] text-slate-400 font-semibold uppercase mt-0.5"><?= htmlspecialchars($_SESSION['auth.user_role'] ?? 'user') ?></p>
            </div>
            <a data-spa href="/profile" onclick="closeUserDropdown()" class="flex items-center gap-2.5 px-4 py-2 hover:bg-slate-50 font-medium transition-colors">
              <i data-lucide="user" class="w-4 h-4 text-slate-400"></i> Profil Saya
            </a>
            <div class="border-t border-slate-100 my-1"></div>
            <form id="logout-form" action="<?= getBaseUrl('/logout') ?>" method="POST" onsubmit="openLogoutModal(event)">
              <button type="submit" onclick="closeUserDropdown()" class="w-full text-left flex items-center gap-2.5 px-4 py-2 hover:bg-red-50 text-red-600 font-bold transition-colors cursor-pointer border-none bg-transparent">
                <i data-lucide="log-out" class="w-4 h-4 text-red-500"></i> Keluar
              </button>
            </form>
          </div>
        </div>
      <?php else: ?>
        <nav class="flex items-center gap-4 text-xs font-semibold">
          <?php
            $isLogin = str_ends_with($currentPath, '/login');
            $isRegister = str_ends_with($currentPath, '/register');
          ?>
          <?php if (!$isLogin): ?>
            <a href="<?= getBaseUrl('/login') ?>" class="text-slate-600 hover:text-indigo-650 transition-colors">Login</a>
          <?php endif; ?>
          <?php if (!$isRegister): ?>
            <a href="<?= getBaseUrl('/register') ?>" class="text-white bg-indigo-600 hover:bg-indigo-700 px-4 py-2 rounded-xl transition-all shadow-sm hover:shadow active:scale-[0.98]">Daftar</a>
          <?php endif; ?>
        </nav>
      <?php endif; ?>
    </div>
  </header>
  <?php endif; ?>

  <!-- Sidebar mobile drawer backdrop -->
  <div id="mobile-sidebar-backdrop" onclick="toggleMobileSidebar()" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-40 hidden md:hidden"></div>

  <!-- Layout Wrapper -->
  <div class="flex-grow flex flex-row <?= $isAuthPage ? '' : 'pt-14' ?> <?= $isLoggedIn ? 'is-logged-in' : 'is-guest' ?>" id="admin-layout-wrapper">
    <!-- Sidebar -->
    <?php if ($isLoggedIn): ?>
      <aside id="sidebar-container" class="fixed top-14 left-0 bottom-0 z-30 w-64 bg-white border-r border-slate-200/80 flex flex-col py-6 px-4 space-y-6 overflow-y-auto transition-all duration-300 transform -translate-x-full md:translate-x-0">
        <!-- Main Links Group -->
        <div class="space-y-1.5">
          <span class="text-[9px] font-bold text-slate-450 uppercase tracking-widest block pl-3 mb-1 sidebar-collapsed-hide">Menu Utama</span>
          <a data-spa data-sidebar-link="dashboard" href="<?= getBaseUrl('/dashboard') ?>" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-semibold transition-all hover:bg-slate-50 sidebar-collapsed-center text-slate-600">
            <i data-lucide="layout-dashboard" class="w-4 h-4 flex-shrink-0"></i>
            <span class="sidebar-collapsed-label">Dashboard</span>
          </a>
          <a data-spa data-sidebar-link="profile" href="<?= getBaseUrl('/profile') ?>" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-semibold transition-all hover:bg-slate-50 sidebar-collapsed-center text-slate-600">
            <i data-lucide="user" class="w-4 h-4 flex-shrink-0"></i>
            <span class="sidebar-collapsed-label">Profil Saya</span>
          </a>
        </div>

        <!-- Administration Group -->
        <?php if (has_any_permission(['manage_users', 'verify_payment', 'verify_document', 'manage_selection', 'manage_settings'])): ?>
          <div class="space-y-1.5 border-t border-slate-100 pt-5">
            <span class="text-[9px] font-bold text-slate-450 uppercase tracking-widest block pl-3 mb-1 sidebar-collapsed-hide">Administrasi</span>
            <?php if (has_permission('manage_users')): ?>
              <a data-spa data-sidebar-link="users" href="<?= getBaseUrl('/admin/users') ?>" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-semibold transition-all hover:bg-slate-50 sidebar-collapsed-center text-slate-600">
                <i data-lucide="key-round" class="w-4 h-4 flex-shrink-0"></i>
                <span class="sidebar-collapsed-label">Hak Akses Pengguna</span>
              </a>
            <?php endif; ?>

            <?php if (has_any_permission(['verify_payment', 'verify_document', 'manage_selection'])): ?>
              <a data-spa data-sidebar-link="registrants" href="<?= getBaseUrl('/admin/registrants') ?>" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-semibold transition-all hover:bg-slate-50 sidebar-collapsed-center text-slate-600">
                <i data-lucide="users" class="w-4 h-4 flex-shrink-0"></i>
                <span class="sidebar-collapsed-label">Manajemen Pendaftar</span>
              </a>
            <?php endif; ?>

            <?php if (has_any_permission(['verify_payment', 'verify_document', 'manage_selection', 'manage_users'])): ?>
              <a data-spa data-sidebar-link="reports" href="<?= getBaseUrl('/admin/reports') ?>" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-semibold transition-all hover:bg-slate-50 sidebar-collapsed-center text-slate-600">
                <i data-lucide="bar-chart-3" class="w-4 h-4 flex-shrink-0"></i>
                <span class="sidebar-collapsed-label">Laporan & Statistik</span>
              </a>
            <?php endif; ?>

            <?php if (has_permission('verify_payment')): ?>
              <a data-spa data-sidebar-link="payments" href="<?= getBaseUrl('/admin/payments') ?>" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-semibold transition-all hover:bg-slate-50 sidebar-collapsed-center text-slate-600">
                <i data-lucide="credit-card" class="w-4 h-4 flex-shrink-0"></i>
                <span class="sidebar-collapsed-label">Verifikasi Pembayaran</span>
              </a>
              <a data-spa data-sidebar-link="re-registrations" href="<?= getBaseUrl('/admin/re-registrations') ?>" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-semibold transition-all hover:bg-slate-50 sidebar-collapsed-center text-slate-600">
                <i data-lucide="check-square" class="w-4 h-4 flex-shrink-0"></i>
                <span class="sidebar-collapsed-label">Verifikasi Daftar Ulang</span>
              </a>
            <?php endif; ?>

            <?php if (has_permission('verify_document')): ?>
              <a data-spa data-sidebar-link="verifications" href="<?= getBaseUrl('/admin/verifications') ?>" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-semibold transition-all hover:bg-slate-50 sidebar-collapsed-center text-slate-600">
                <i data-lucide="folder-open" class="w-4 h-4 flex-shrink-0"></i>
                <span class="sidebar-collapsed-label">Verifikasi Berkas</span>
              </a>
            <?php endif; ?>

            <?php if (has_permission('manage_selection')): ?>
              <a data-spa data-sidebar-link="selection" href="<?= getBaseUrl('/admin/selection') ?>" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-semibold transition-all hover:bg-slate-50 sidebar-collapsed-center text-slate-600">
                <i data-lucide="award" class="w-4 h-4 flex-shrink-0"></i>
                <span class="sidebar-collapsed-label">Seleksi & Kelulusan</span>
              </a>
              <a data-spa data-sidebar-link="announcements" href="<?= getBaseUrl('/admin/announcements') ?>" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-semibold transition-all hover:bg-slate-50 sidebar-collapsed-center text-slate-600">
                <i data-lucide="megaphone" class="w-4 h-4 flex-shrink-0"></i>
                <span class="sidebar-collapsed-label">Kelola Pengumuman</span>
              </a>
            <?php endif; ?>

            <!-- Accordion master settings links -->
            <?php if (has_permission('manage_settings')): ?>
              <a data-spa data-sidebar-link="settings" href="<?= getBaseUrl('/admin/settings') ?>" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-semibold transition-all hover:bg-slate-50 sidebar-collapsed-center text-slate-600">
                <i data-lucide="sliders" class="w-4 h-4 flex-shrink-0"></i>
                <span class="sidebar-collapsed-label">Pengaturan Sistem</span>
              </a>
              <div class="space-y-0.5">
                <button type="button" onclick="toggleAccordion()" class="w-full flex items-center justify-between px-3.5 py-2 rounded-xl text-xs font-semibold transition-all hover:bg-slate-50 text-slate-600 focus:outline-none cursor-pointer sidebar-collapsed-center" id="btn-accordion-master">
                  <div class="flex items-center gap-3">
                    <i data-lucide="settings" class="w-4 h-4 flex-shrink-0"></i>
                    <span class="sidebar-collapsed-label">Kelola Data Master</span>
                  </div>
                  <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200 sidebar-collapsed-hide" id="accordion-chevron"></i>
                </button>

                <div id="accordion-master-sublinks" class="accordion-content pl-4 border-l border-slate-100 ml-5 mt-1 space-y-0.5 sidebar-collapsed-hide">
                  <a data-spa data-sidebar-sublink="wave" href="/admin/master?tab=wave" class="flex items-center gap-2.5 px-3 py-1.5 rounded-lg text-[11px] font-semibold transition-all hover:bg-slate-50 text-slate-500">
                    <i data-lucide="waves" class="w-3.5 h-3.5"></i> Gelombang
                  </a>
                  <a data-spa data-sidebar-sublink="faculty" href="/admin/master?tab=faculty" class="flex items-center gap-2.5 px-3 py-1.5 rounded-lg text-[11px] font-semibold transition-all hover:bg-slate-50 text-slate-500">
                    <i data-lucide="building" class="w-3.5 h-3.5"></i> Fakultas
                  </a>
                  <a data-spa data-sidebar-sublink="study-program" href="/admin/master?tab=study-program" class="flex items-center gap-2.5 px-3 py-1.5 rounded-lg text-[11px] font-semibold transition-all hover:bg-slate-50 text-slate-500">
                    <i data-lucide="graduation-cap" class="w-3.5 h-3.5"></i> Program Studi
                  </a>
                  <a data-spa data-sidebar-sublink="document-type" href="/admin/master?tab=document-type" class="flex items-center gap-2.5 px-3 py-1.5 rounded-lg text-[11px] font-semibold transition-all hover:bg-slate-50 text-slate-500">
                    <i data-lucide="file-text" class="w-3.5 h-3.5"></i> Jenis Dokumen
                  </a>
                  <a data-spa data-sidebar-sublink="payment-account" href="/admin/master?tab=payment-account" class="flex items-center gap-2.5 px-3 py-1.5 rounded-lg text-[11px] font-semibold transition-all hover:bg-slate-50 text-slate-500">
                    <i data-lucide="credit-card" class="w-3.5 h-3.5"></i> Rekening Penerimaan
                  </a>
                  <a data-spa data-sidebar-sublink="registration-fee" href="/admin/master?tab=registration-fee" class="flex items-center gap-2.5 px-3 py-1.5 rounded-lg text-[11px] font-semibold transition-all hover:bg-slate-50 text-slate-500">
                    <i data-lucide="dollar-sign" class="w-3.5 h-3.5"></i> Biaya Formulir
                  </a>
                  <a data-spa data-sidebar-sublink="nim-format" href="/admin/master?tab=nim-format" class="flex items-center gap-2.5 px-3 py-1.5 rounded-lg text-[11px] font-semibold transition-all hover:bg-slate-50 text-slate-500">
                    <i data-lucide="binary" class="w-3.5 h-3.5"></i> Format NIM
                  </a>
                </div>
              </div>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      </aside>
    <?php endif; ?>

    <!-- Main Content Panel -->
    <main id="app-content" data-layout="layout.php" class="flex-grow flex flex-col transition-all duration-300 <?= $isAuthPage ? 'min-h-screen justify-center items-center p-6 w-full' : ($isLoggedIn ? 'p-5 md:p-8 min-h-[calc(100vh-3.5rem)] ml-0 md:ml-64' : 'p-5 md:p-8 min-h-[calc(100vh-3.5rem)] max-w-7xl mx-auto w-full') ?>">
      <?= $children; ?>
    </main>
  </div>

  <!-- Logout Confirmation Modal -->
  <div id="logout-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closeLogoutModal()"></div>
    <div class="relative bg-white rounded-3xl p-6 max-w-sm w-full shadow-2xl border border-slate-100 space-y-6 transform scale-95 opacity-0 transition-all duration-200" id="logout-modal-card">
      <div class="text-center space-y-2">
        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-50 text-red-600 text-xl">⚠️</div>
        <h3 class="text-lg font-bold text-slate-900">Konfirmasi Keluar</h3>
        <p class="text-xs text-slate-500 leading-relaxed">Apakah Anda yakin ingin keluar dari sistem portal PMB KMK?</p>
      </div>
      <div class="flex gap-3">
        <button type="button" onclick="closeLogoutModal()" class="flex-1 py-2.5 px-4 bg-slate-155 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition-colors cursor-pointer">
          Batal
        </button>
        <button type="button" onclick="confirmLogout()" class="flex-1 py-2.5 px-4 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl text-xs transition-colors shadow-sm cursor-pointer">
          Keluar
        </button>
      </div>
    </div>
  </div>

  <!-- Autoload / SPA Script Injections -->
  <?= App\Core\View\View::renderScripts() ?>

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
      const metaToken = document.querySelector('meta[name="csrf-token"]');
      if (metaToken) {
        let tokenInput = form.querySelector('input[name="_token"]');
        if (!tokenInput) {
          tokenInput = document.createElement('input');
          tokenInput.type = 'hidden';
          tokenInput.name = '_token';
          form.appendChild(tokenInput);
        }
        tokenInput.value = metaToken.getAttribute('content');
      }
      form.submit();
    }

    function toggleUserDropdown(event) {
      event.stopPropagation();
      const dropdown = document.getElementById('user-dropdown');
      const chevron = document.getElementById('dropdown-chevron');
      const isOpen = dropdown.classList.contains('open');

      if (isOpen) {
        closeUserDropdown();
      } else {
        dropdown.classList.add('open');
        if (chevron) chevron.classList.add('rotate-180');
      }
    }

    function closeUserDropdown() {
      const dropdown = document.getElementById('user-dropdown');
      const chevron = document.getElementById('dropdown-chevron');
      if (dropdown) dropdown.classList.remove('open');
      if (chevron) chevron.classList.remove('rotate-180');
    }

    function toggleNotificationsDropdown(event) {
      event.stopPropagation();
      const dropdown = document.getElementById('notifications-dropdown');
      const isOpen = dropdown.classList.contains('open');

      if (isOpen) {
        closeNotificationsDropdown();
      } else {
        closeUserDropdown();
        dropdown.classList.add('open');
      }
    }

    function closeNotificationsDropdown() {
      const dropdown = document.getElementById('notifications-dropdown');
      if (dropdown) dropdown.classList.remove('open');
    }

    window.addEventListener('click', () => {
      closeUserDropdown();
      closeNotificationsDropdown();
    });

    function toggleDesktopSidebar() {
      const wrapper = document.getElementById('admin-layout-wrapper');
      const content = document.getElementById('app-content');
      const collapsed = wrapper.classList.toggle('sidebar-collapsed');

      if (collapsed) {
        content.classList.remove('md:ml-64');
        content.classList.add('md:ml-16');
        localStorage.setItem('sidebar-collapsed', '1');
      } else {
        content.classList.remove('md:ml-16');
        content.classList.add('md:ml-64');
        localStorage.setItem('sidebar-collapsed', '0');
      }
    }

    function toggleMobileSidebar() {
      const sidebar = document.getElementById('sidebar-container');
      const backdrop = document.getElementById('mobile-sidebar-backdrop');
      const isOpen = sidebar.classList.contains('translate-x-0');

      if (isOpen) {
        sidebar.classList.remove('translate-x-0');
        sidebar.classList.add('-translate-x-full');
        backdrop.classList.add('hidden');
      } else {
        sidebar.classList.remove('-translate-x-full');
        sidebar.classList.add('translate-x-0');
        backdrop.classList.remove('hidden');
      }
    }

    function closeMobileSidebar() {
      const sidebar = document.getElementById('sidebar-container');
      const backdrop = document.getElementById('mobile-sidebar-backdrop');
      if (sidebar) {
        sidebar.classList.remove('translate-x-0');
        sidebar.classList.add('-translate-x-full');
      }
      if (backdrop) {
        backdrop.classList.add('hidden');
      }
    }

    function toggleAccordion() {
      const content = document.getElementById('accordion-master-sublinks');
      const chevron = document.getElementById('accordion-chevron');
      const isOpen = content.classList.contains('open');

      if (isOpen) {
        content.classList.remove('open');
        if (chevron) chevron.classList.remove('rotate-180');
      } else {
        content.classList.add('open');
        if (chevron) chevron.classList.add('rotate-180');
      }
    }

    function updateSidebarActiveState() {
      const path = window.location.pathname;
      const search = window.location.search;
      const urlParams = new URLSearchParams(search);
      const activeTab = urlParams.get('tab') || 'academic-year';

      const isMasterPage = path.includes('/admin/master');

      const accordionContent = document.getElementById('accordion-master-sublinks');
      const accordionChevron = document.getElementById('accordion-chevron');
      if (accordionContent) {
        if (isMasterPage) {
          accordionContent.classList.add('open');
          if (accordionChevron) accordionChevron.classList.add('rotate-180');
        } else {
          accordionContent.classList.remove('open');
          if (accordionChevron) accordionChevron.classList.remove('rotate-180');
        }
      }

      const links = document.querySelectorAll('[data-sidebar-link]');
      links.forEach(link => {
        const type = link.getAttribute('data-sidebar-link');
        let isActive = false;

        if (type === 'dashboard' && path.endsWith('/dashboard')) isActive = true;
        if (type === 'profile' && path.endsWith('/profile')) isActive = true;
        if (type === 'users' && path.endsWith('/admin/users')) isActive = true;
        if (type === 'registrants' && path.includes('/admin/registrants')) isActive = true;
        if (type === 'reports' && path.includes('/admin/reports')) isActive = true;
        if (type === 'payments' && path.endsWith('/admin/payments')) isActive = true;
        if (type === 're-registrations' && path.includes('/admin/re-registrations')) isActive = true;
        if (type === 'verifications' && path.includes('/admin/verifications')) isActive = true;
        if (type === 'selection' && path.includes('/admin/selection')) isActive = true;
        if (type === 'announcements' && path.includes('/admin/announcements')) isActive = true;
        if (type === 'master' && isMasterPage) isActive = true;
        if (type === 'settings' && path.includes('/admin/settings')) isActive = true;

        if (isActive) {
          link.classList.add('bg-indigo-50', 'text-indigo-600');
          link.classList.remove('text-slate-600', 'hover:bg-slate-50');
        } else {
          link.classList.remove('bg-indigo-50', 'text-indigo-600');
          link.classList.add('text-slate-600', 'hover:bg-slate-50');
        }
      });

      const subLinks = document.querySelectorAll('[data-sidebar-sublink]');
      subLinks.forEach(link => {
        const tabType = link.getAttribute('data-sidebar-sublink');
        if (isMasterPage && tabType === activeTab) {
          link.classList.add('text-indigo-600', 'bg-indigo-50/50');
          link.classList.remove('text-slate-500', 'hover:bg-slate-50');
        } else {
          link.classList.remove('text-indigo-600', 'bg-indigo-50/50');
          link.classList.add('text-slate-500', 'hover:bg-slate-50');
        }
      });
    }

    // Initialize sidebar collapse state from localStorage on load
    const isCollapsed = localStorage.getItem('sidebar-collapsed') === '1';
    if (isCollapsed && window.innerWidth >= 768) {
      const wrapper = document.getElementById('admin-layout-wrapper');
      const content = document.getElementById('app-content');
      if (wrapper && content) {
        wrapper.classList.add('sidebar-collapsed');
        content.classList.remove('md:ml-64');
        content.classList.add('md:ml-16');
      }
    }

    function initClientPagination() {
      document.querySelectorAll('table[data-paginate]').forEach(table => {
        const limit = parseInt(table.getAttribute('data-paginate') || 10, 10);
        const tbody = table.querySelector('tbody');
        if (!tbody) return;
        const rows = Array.from(tbody.querySelectorAll('tr')).filter(row => !row.classList.contains('empty-row-placeholder') && !row.parentElement.tagName.toLowerCase().includes('thead'));
        if (rows.length <= limit) {
          const existingNav = table.parentElement.parentElement.querySelector('.client-pagination-nav');
          if (existingNav) existingNav.remove();
          rows.forEach(r => r.classList.remove('hidden'));
          return;
        }

        const existingNav = table.parentElement.parentElement.querySelector('.client-pagination-nav');
        if (existingNav) existingNav.remove();

        let currentPage = 1;
        const totalPages = Math.ceil(rows.length / limit);

        const navWrapper = document.createElement('div');
        navWrapper.className = 'client-pagination-nav mt-4 flex flex-col md:flex-row items-center justify-between gap-4 bg-white p-4 rounded-2xl border border-slate-200/50 shadow-sm';

        const infoDiv = document.createElement('div');
        infoDiv.className = 'text-xs font-semibold text-slate-500';
        navWrapper.appendChild(infoDiv);

        const buttonsWrapper = document.createElement('div');
        buttonsWrapper.className = 'flex items-center gap-1.5';
        navWrapper.appendChild(buttonsWrapper);

        function showPage(page) {
          currentPage = page;
          const start = (page - 1) * limit;
          const end = page * limit;

          rows.forEach((row, index) => {
            if (index >= start && index < end) {
              row.classList.remove('hidden');
            } else {
              row.classList.add('hidden');
            }
          });

          infoDiv.textContent = `Menampilkan ${Math.min(rows.length, start + 1)} s/d ${Math.min(rows.length, end)} dari ${rows.length} data`;

          buttonsWrapper.innerHTML = '';

          if (currentPage > 1) {
            const prevBtn = document.createElement('button');
            prevBtn.className = 'px-3 py-1.5 rounded-lg text-xs font-bold text-slate-700 bg-slate-50 hover:bg-slate-100 transition-colors border border-slate-200 cursor-pointer';
            prevBtn.textContent = 'Sebelumnya';
            prevBtn.onclick = () => showPage(currentPage - 1);
            buttonsWrapper.appendChild(prevBtn);
          } else {
            const prevSpan = document.createElement('span');
            prevSpan.className = 'px-3 py-1.5 rounded-lg text-xs font-bold text-slate-400 bg-slate-50 border border-slate-100 cursor-not-allowed';
            prevSpan.textContent = 'Sebelumnya';
            buttonsWrapper.appendChild(prevSpan);
          }

          const startPage = Math.max(1, currentPage - 2);
          const endPage = Math.min(totalPages, currentPage + 2);
          for (let i = startPage; i <= endPage; i++) {
            if (i === currentPage) {
              const span = document.createElement('span');
              span.className = 'px-3 py-1.5 rounded-lg text-xs font-bold text-white bg-indigo-600 border border-indigo-600 shadow-sm';
              span.textContent = i;
              buttonsWrapper.appendChild(span);
            } else {
              const btn = document.createElement('button');
              btn.className = 'px-3 py-1.5 rounded-lg text-xs font-bold text-slate-700 bg-slate-50 hover:bg-slate-100 transition-colors border border-slate-200 cursor-pointer';
              btn.textContent = i;
              btn.onclick = () => showPage(i);
              buttonsWrapper.appendChild(btn);
            }
          }

          if (currentPage < totalPages) {
            const nextBtn = document.createElement('button');
            nextBtn.className = 'px-3 py-1.5 rounded-lg text-xs font-bold text-slate-700 bg-slate-50 hover:bg-slate-100 transition-colors border border-slate-200 cursor-pointer';
            nextBtn.textContent = 'Selanjutnya';
            nextBtn.onclick = () => showPage(currentPage + 1);
            buttonsWrapper.appendChild(nextBtn);
          } else {
            const nextSpan = document.createElement('span');
            nextSpan.className = 'px-3 py-1.5 rounded-lg text-xs font-bold text-slate-400 bg-slate-50 border border-slate-100 cursor-not-allowed';
            nextSpan.textContent = 'Selanjutnya';
            buttonsWrapper.appendChild(nextSpan);
          }
        }

        showPage(1);
        table.parentElement.parentElement.appendChild(navWrapper);
      });
    }

    function checkSweetAlertNotifications() {
      const urlParams = new URLSearchParams(window.location.search);
      const successMsg = urlParams.get('success');
      const errorMsg = urlParams.get('error');

      if (successMsg) {
        Swal.fire({
          icon: 'success',
          title: 'Berhasil',
          text: successMsg,
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 3000,
          timerProgressBar: true
        });
        const cleanUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + (window.location.search.replace(/[?&]success=[^&]+/g, '').replace(/^&/, '?'));
        window.history.replaceState({path: cleanUrl}, '', cleanUrl);
      }

      if (errorMsg) {
        Swal.fire({
          icon: 'error',
          title: 'Perhatian',
          text: errorMsg,
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 4000,
          timerProgressBar: true
        });
        const cleanUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + (window.location.search.replace(/[?&]error=[^&]+/g, '').replace(/^&/, '?'));
        window.history.replaceState({path: cleanUrl}, '', cleanUrl);
      }
    }
    window.confirmAction = function(event, title, text) {
      event.preventDefault();
      const form = event.target;
      Swal.fire({
        title: title,
        text: text,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#4f46e5',
        cancelButtonColor: '#ef4444',
        confirmButtonText: 'Ya, Lanjutkan!',
        cancelButtonText: 'Batal'
      }).then((result) => {
        if (result.isConfirmed) {
          form.submit();
        }
      });
      return false;
    }

    document.addEventListener("DOMContentLoaded", () => {
      try { lucide.createIcons(); } catch (e) { console.error(e); }
      try { updateSidebarActiveState(); } catch (e) { console.error(e); }
      try { initClientPagination(); } catch (e) { console.error(e); }
      try { checkSweetAlertNotifications(); } catch (e) { console.error(e); }
    });
    window.addEventListener("spa:navigated", () => {
      try { lucide.createIcons(); } catch (e) { console.error(e); }
      try { updateSidebarActiveState(); } catch (e) { console.error(e); }
      try { closeMobileSidebar(); } catch (e) { console.error(e); }
      try { initClientPagination(); } catch (e) { console.error(e); }
      try { checkSweetAlertNotifications(); } catch (e) { console.error(e); }
    });
  </script>
</body>

</html>