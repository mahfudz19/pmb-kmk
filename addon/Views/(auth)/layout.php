<?php
/**
 * @var \App\Core\View\PageMeta $meta
 * @var string $children
 */
?>
<div class="min-h-screen flex items-center justify-center p-0 md:p-6 w-full">
  <div class="w-full max-w-5xl bg-white md:rounded-3xl md:shadow-xl md:border md:border-slate-200/50 flex flex-col md:flex-row overflow-hidden min-h-screen md:min-h-[620px] transition-all duration-300">
    <!-- Left Column: Visual Illustration (Desktop/Tablet only) -->
    <div class="hidden md:flex md:w-1/2 bg-slate-900 text-white p-10 lg:p-12 flex-col justify-between relative overflow-hidden bg-cover bg-center" style="background-image: linear-gradient(rgba(30, 27, 75, 0.4), rgba(15, 23, 42, 0.85)), url('<?= getBaseUrl('/logo_app/campus-hero.png') ?>');">
      <!-- Gradient overlay decoration -->
      <div class="absolute inset-0 bg-indigo-650/15 mix-blend-overlay"></div>
      
      <!-- Top Brand area -->
      <div class="z-10 flex items-center gap-2.5">
        <img src="<?= getBaseUrl('/logo_app/mazu-logo.svg') ?>" alt="Logo" class="h-7 w-auto filter brightness-0 invert" />
      </div>

      <!-- Bottom Title & Description -->
      <div class="z-10 space-y-4">
        <span class="inline-flex px-3 py-1 rounded-full text-[9px] font-bold bg-indigo-500/30 text-indigo-150 uppercase tracking-widest">Portal Pendaftaran</span>
        <h2 class="text-2xl lg:text-3xl font-extrabold tracking-tight text-white leading-tight">Mulai Perjalanan Akademik Anda Bersama Kampus Mandiri Kencana</h2>
        <p class="text-xs lg:text-sm text-slate-300 leading-relaxed font-medium">Sistem Informasi Penerimaan Mahasiswa Baru (PMB) online terintegrasi, transparan, dan efisien.</p>
      </div>

      <!-- Tiny copyright/version in visual side -->
      <div class="z-10 text-[9px] text-slate-400 font-bold uppercase tracking-wider">
        &copy; <?= date('Y') ?> KMK. All rights reserved.
      </div>
    </div>

    <!-- Right Column: Authentication Form -->
    <div class="w-full md:w-1/2 flex items-center justify-center p-6 sm:p-10 lg:p-12 min-h-screen md:min-h-0 bg-white">
      <div class="w-full max-w-sm space-y-6 animate-fade-in" data-layout="<?= $layoutId ?? '(auth)/layout.php' ?>">
        <?= $children; ?>
      </div>
    </div>
  </div>
</div>