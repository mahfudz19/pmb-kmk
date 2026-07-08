<div class="max-w-4xl mx-auto w-full py-12 px-4 sm:px-6 lg:px-8">
  <div class="bg-white rounded-2xl shadow-sm p-8 border border-slate-200 space-y-6">
    <div class="border-b border-slate-200 pb-6">
      <h2 class="text-3xl font-extrabold text-slate-900">Selamat Datang di PMB KMK</h2>
      <p class="mt-2 text-sm text-slate-500">Anda berhasil login ke sistem Penerimaan Mahasiswa Baru dengan aman.</p>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4">
      <div class="bg-indigo-50 rounded-2xl p-6 border border-indigo-100 space-y-2">
        <h3 class="font-bold text-indigo-900 text-lg">Informasi Pendaftaran</h3>
        <p class="text-sm text-indigo-700">Silakan lengkapi berkas pendaftaran Anda di menu yang disediakan.</p>
      </div>
      <div class="bg-slate-50 rounded-2xl p-6 border border-slate-100 space-y-2">
        <h3 class="font-bold text-slate-900 text-lg">Status Akun</h3>
        <p class="text-sm text-slate-600">Peran Anda saat ini: <strong class="bg-indigo-100 text-indigo-800 px-2 py-0.5 rounded text-xs font-semibold uppercase"><?= htmlspecialchars($_SESSION['auth.user_role'] ?? 'user') ?></strong></p>
      </div>
    </div>
  </div>
</div>