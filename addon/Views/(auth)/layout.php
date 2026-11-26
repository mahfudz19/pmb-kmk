<?php
?>
<div class="min-h-screen md:min-h-0 flex items-center justify-center w-full">
  <div class="w-full max-w-[1060px] bg-[#181c36] md:rounded-3xl md:shadow-[0_24px_70px_rgba(0,0,0,0.65)] md:border md:border-white/10 flex flex-col md:flex-row overflow-hidden min-h-screen md:min-h-[640px] transition-all duration-300">
    <div class="hidden md:flex md:w-1/2 bg-[#0d1333] text-white p-10 lg:p-12 flex-col justify-between relative overflow-hidden bg-cover bg-center" style="background-image: linear-gradient(180deg, rgba(11, 16, 42, 0.72) 0%, rgba(13, 19, 51, 0.55) 48%, rgba(9, 13, 35, 0.90) 100%), url('<?= getBaseUrl('/logo_app/auth-hero.jpg') ?>');">
      <div class="absolute inset-0 bg-[radial-gradient(circle_at_25%_20%,rgba(67,104,245,0.22),transparent_55%),radial-gradient(circle_at_80%_80%,rgba(59,130,246,0.18),transparent_50%)] pointer-events-none"></div>

      <div class="relative z-10 flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-[#4368f5] flex items-center justify-center shadow-lg shadow-[#4368f5]/30 flex-shrink-0">
          <i data-lucide="graduation-cap" class="w-5 h-5 text-white"></i>
        </div>
        <div class="flex items-center gap-3">
          <span class="text-xl font-extrabold tracking-tight text-white">Gen-U</span>
          <span class="h-5 w-px bg-white/25"></span>
          <span class="text-sm font-medium text-slate-300 tracking-wide">PMB KMK</span>
        </div>
      </div>

      <div class="relative z-10 my-auto py-8 space-y-5">
        <span class="inline-flex items-center px-3.5 py-1 rounded-full text-[11px] font-bold tracking-[0.12em] uppercase bg-amber-500/15 text-[#f5a623] border border-amber-400/30">
          Simulasi PMB
        </span>
        <h2 class="text-3xl lg:text-[34px] font-extrabold tracking-tight text-white leading-[1.18]">
          Mulai Perjalanan Akademik Anda Bersama <span class="text-[#f59e0b]">Gen-U</span>
        </h2>
        <p class="text-sm text-slate-300/90 leading-relaxed max-w-md">
          Rasakan alur Penerimaan Mahasiswa Baru (PMB) online yang terintegrasi, transparan, dan efisien, langsung dari dasbor Gen-U.
        </p>
      </div>

      <div class="relative z-10 text-[11px] text-slate-400/80 font-semibold uppercase tracking-[0.14em]">
        &copy; <?= date('Y') ?> KMK &middot; DIDUKUNG GEN-U
      </div>
    </div>

    <div class="w-full md:w-1/2 flex flex-col items-center justify-center p-6 sm:p-10 lg:p-12 min-h-screen md:min-h-0 bg-[#181c36] md:border-l md:border-white/[0.06]">
      <div class="flex md:hidden items-center gap-3 mb-8 w-full max-w-[380px]">
        <div class="w-9 h-9 rounded-xl bg-[#4368f5] flex items-center justify-center shadow-lg shadow-[#4368f5]/30 flex-shrink-0">
          <i data-lucide="graduation-cap" class="w-5 h-5 text-white"></i>
        </div>
        <div class="flex items-center gap-2.5">
          <span class="text-lg font-extrabold tracking-tight text-white">Gen-U</span>
          <span class="h-4 w-px bg-white/20"></span>
          <span class="text-xs font-medium text-slate-300 tracking-wide">PMB KMK</span>
        </div>
      </div>

      <div class="w-full max-w-[380px] space-y-6 animate-fade-in" data-layout="<?= $layoutId ?? '(auth)/layout.php' ?>">
        <?= $children; ?>
      </div>
    </div>
  </div>
</div>
