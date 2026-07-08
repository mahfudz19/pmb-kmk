<?php

/**
 * @var \App\Core\View\PageMeta $meta
 * @var string $children
 */
?>
<div class="min-h-[70vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
  <div class="max-w-md w-full space-y-6 bg-white p-8 sm:p-10 rounded-2xl shadow-lg border border-slate-100" data-layout="(auth)/layout.php">
    <?= $children; ?>
  </div>
</div>