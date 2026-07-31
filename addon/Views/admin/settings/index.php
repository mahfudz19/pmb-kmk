<?php
/**
 * @var array $settings
 * @var array $academic_years
 * @var array $waves
 */
?>
<div class="w-full py-2 space-y-8">
  <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
      <span class="text-xs font-bold text-indigo-650 uppercase tracking-widest block mb-1">Konfigurasi Aplikasi</span>
      <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Pengaturan Sistem</h1>
      <p class="text-xs text-slate-500">Sesuaikan identitas kampus, format nomor pendaftaran, gelombang aktif, dan SMTP email.</p>
    </div>
  </div>

  <?php if ($success = $_GET['success'] ?? null): ?>
    <div class="bg-emerald-50 border border-emerald-250 rounded-2xl p-4 text-xs font-semibold text-emerald-800 flex items-center gap-3">
      <span>✅</span>
      <span><?= htmlspecialchars($success) ?></span>
    </div>
  <?php endif; ?>

  <div class="border-b border-slate-200">
    <nav class="flex space-x-6" aria-label="Tabs">
      <button onclick="switchTab('tab-general', this)" class="tab-btn py-3 px-1 border-b-2 font-bold text-xs uppercase tracking-wider border-indigo-600 text-indigo-600 transition-all focus:outline-none">
        🏢 Identitas Kampus
      </button>
      <button onclick="switchTab('tab-format', this)" class="tab-btn py-3 px-1 border-b-2 font-bold text-xs uppercase tracking-wider border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-350 transition-all focus:outline-none">
        🔢 Format No. Pendaftaran
      </button>
      <button onclick="switchTab('tab-smtp', this)" class="tab-btn py-3 px-1 border-b-2 font-bold text-xs uppercase tracking-wider border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-350 transition-all focus:outline-none">
        ✉️ Pengaturan SMTP
      </button>
    </nav>
  </div>

  <div id="tab-general" class="tab-content space-y-6">
    <form action="/admin/settings/general" method="POST" enctype="multipart/form-data" class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-6">
      <h3 class="text-sm font-extrabold text-slate-800 border-b border-slate-100 pb-3">Informasi Umum Institusi</h3>
      
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-1.5">
          <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Nama Kampus</label>
          <input type="text" name="campus_name" value="<?= htmlspecialchars($settings['campus_name'] ?? '') ?>" class="w-full text-xs font-semibold px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 focus:bg-white transition-all text-slate-800" required>
        </div>

        <div class="space-y-1.5">
          <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Email Resmi Kampus</label>
          <input type="email" name="campus_email" value="<?= htmlspecialchars($settings['campus_email'] ?? '') ?>" class="w-full text-xs font-semibold px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 focus:bg-white transition-all text-slate-800" required>
        </div>

        <div class="space-y-1.5">
          <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Telepon Kampus</label>
          <input type="text" name="campus_phone" value="<?= htmlspecialchars($settings['campus_phone'] ?? '') ?>" class="w-full text-xs font-semibold px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 focus:bg-white transition-all text-slate-800" required>
        </div>

        <div class="space-y-1.5">
          <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Nama Ketua Panitia PMB</label>
          <input type="text" name="pmb_chairman_name" value="<?= htmlspecialchars($settings['pmb_chairman_name'] ?? 'Prof. Dr. Ir. Hermawan') ?>" class="w-full text-xs font-semibold px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 focus:bg-white transition-all text-slate-800" required>
        </div>

        <div class="space-y-1.5">
          <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">NIP Ketua Panitia PMB</label>
          <input type="text" name="pmb_chairman_nip" value="<?= htmlspecialchars($settings['pmb_chairman_nip'] ?? 'NIP. 19750812 200212 1 002') ?>" class="w-full text-xs font-semibold px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 focus:bg-white transition-all text-slate-800" required>
        </div>

        <div class="space-y-1.5 col-span-2">
          <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Logo Kampus</label>
          <div class="flex items-center gap-6 p-4 bg-slate-50 border border-slate-200 rounded-xl">
            <div class="w-16 h-16 rounded-2xl bg-white border border-slate-200 flex items-center justify-center p-2 shadow-sm">
              <img src="<?= htmlspecialchars($settings['campus_logo'] ?? '/logo_app/mazu-logo.svg') ?>" alt="Logo Kampus" class="max-w-full max-h-full object-contain">
            </div>
            <div class="space-y-1">
              <input type="file" name="campus_logo_file" accept="image/png,image/jpeg,image/svg+xml,image/webp" class="text-xs text-slate-650 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
              <p class="text-[9px] text-slate-450 font-normal">Format yang didukung: PNG, JPG, JPEG, SVG, WEBP (Maksimal 2MB)</p>
            </div>
          </div>
        </div>
      </div>

      <div class="space-y-1.5">
        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Alamat Kampus</label>
        <textarea name="campus_address" rows="3" class="w-full text-xs font-semibold px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 focus:bg-white transition-all text-slate-800" required><?= htmlspecialchars($settings['campus_address'] ?? '') ?></textarea>
      </div>

      <div class="flex justify-end pt-3">
        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs px-5 py-3 rounded-xl transition-all shadow-md active:scale-95 cursor-pointer">
          Simpan Identitas Kampus
        </button>
      </div>
    </form>
  </div>



  <div id="tab-format" class="tab-content space-y-6 hidden">
    <form action="/admin/settings/general" method="POST" class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-6">
      <h3 class="text-sm font-extrabold text-slate-800 border-b border-slate-100 pb-3">Format Custom Nomor Pendaftaran</h3>
      
      <div class="bg-indigo-50/50 border border-indigo-100 rounded-2xl p-4 text-xs text-indigo-850 space-y-2">
        <p class="font-bold">Panduan Placeholder Format Custom:</p>
        <ul class="list-disc pl-5 space-y-1 font-medium text-indigo-700">
          <li><code>{YEAR}</code>: Diganti secara otomatis dengan tahun saat pendaftar membuat akun (misal: <code><?= date('Y') ?></code>).</li>
          <li><code>{SEQ}</code>: Diganti dengan nomor urut pendaftaran peserta dengan padding 4 digit angka (misal: <code>0012</code>).</li>
        </ul>
        <p class="font-semibold text-[10px] text-slate-400 mt-2 block">Contoh: <code>PMB-{YEAR}-{SEQ}</code> akan menghasilkan format nomor pendaftaran <code>PMB-<?= date('Y') ?>-0012</code>.</p>
      </div>

      <input type="hidden" name="campus_name" value="<?= htmlspecialchars($settings['campus_name'] ?? '') ?>">
      <input type="hidden" name="campus_email" value="<?= htmlspecialchars($settings['campus_email'] ?? '') ?>">
      <input type="hidden" name="campus_phone" value="<?= htmlspecialchars($settings['campus_phone'] ?? '') ?>">
      <input type="hidden" name="campus_logo" value="<?= htmlspecialchars($settings['campus_logo'] ?? '') ?>">
      <input type="hidden" name="campus_address" value="<?= htmlspecialchars($settings['campus_address'] ?? '') ?>">
      <input type="hidden" name="smtp_host" value="<?= htmlspecialchars($settings['smtp_host'] ?? '') ?>">
      <input type="hidden" name="smtp_port" value="<?= htmlspecialchars($settings['smtp_port'] ?? '') ?>">
      <input type="hidden" name="smtp_username" value="<?= htmlspecialchars($settings['smtp_username'] ?? '') ?>">
      <input type="hidden" name="smtp_password" value="<?= htmlspecialchars($settings['smtp_password'] ?? '') ?>">
      <input type="hidden" name="smtp_encryption" value="<?= htmlspecialchars($settings['smtp_encryption'] ?? '') ?>">
      <input type="hidden" name="smtp_from_address" value="<?= htmlspecialchars($settings['smtp_from_address'] ?? '') ?>">
      <input type="hidden" name="smtp_from_name" value="<?= htmlspecialchars($settings['smtp_from_name'] ?? '') ?>">

      <div class="space-y-1.5">
        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Format Nomor Pendaftaran</label>
        <input type="text" name="registration_number_format" value="<?= htmlspecialchars($settings['registration_number_format'] ?? '') ?>" class="w-full text-xs font-semibold px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 focus:bg-white transition-all text-slate-800" required>
      </div>

      <div class="flex justify-end pt-3">
        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs px-5 py-3 rounded-xl transition-all shadow-md active:scale-95 cursor-pointer">
          Simpan Format Nomor
        </button>
      </div>
    </form>
  </div>

  <div id="tab-smtp" class="tab-content space-y-6 hidden">
    <form action="/admin/settings/general" method="POST" class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-6">
      <h3 class="text-sm font-extrabold text-slate-800 border-b border-slate-100 pb-3">Konfigurasi SMTP Mail Server</h3>
      
      <input type="hidden" name="campus_name" value="<?= htmlspecialchars($settings['campus_name'] ?? '') ?>">
      <input type="hidden" name="campus_email" value="<?= htmlspecialchars($settings['campus_email'] ?? '') ?>">
      <input type="hidden" name="campus_phone" value="<?= htmlspecialchars($settings['campus_phone'] ?? '') ?>">
      <input type="hidden" name="campus_logo" value="<?= htmlspecialchars($settings['campus_logo'] ?? '') ?>">
      <input type="hidden" name="campus_address" value="<?= htmlspecialchars($settings['campus_address'] ?? '') ?>">
      <input type="hidden" name="registration_number_format" value="<?= htmlspecialchars($settings['registration_number_format'] ?? '') ?>">

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-1.5">
          <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">SMTP Host</label>
          <input type="text" name="smtp_host" value="<?= htmlspecialchars($settings['smtp_host'] ?? '') ?>" class="w-full text-xs font-semibold px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 focus:bg-white transition-all text-slate-800" required>
        </div>

        <div class="space-y-1.5">
          <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">SMTP Port</label>
          <input type="text" name="smtp_port" value="<?= htmlspecialchars($settings['smtp_port'] ?? '') ?>" class="w-full text-xs font-semibold px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 focus:bg-white transition-all text-slate-800" required>
        </div>

        <div class="space-y-1.5">
          <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">SMTP Username</label>
          <input type="text" name="smtp_username" value="<?= htmlspecialchars($settings['smtp_username'] ?? '') ?>" class="w-full text-xs font-semibold px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 focus:bg-white transition-all text-slate-800">
        </div>

        <div class="space-y-1.5">
          <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">SMTP Password</label>
          <input type="password" name="smtp_password" value="<?= htmlspecialchars($settings['smtp_password'] ?? '') ?>" class="w-full text-xs font-semibold px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 focus:bg-white transition-all text-slate-800">
        </div>

        <div class="space-y-1.5">
          <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Protokol Keamanan (Encryption)</label>
          <select name="smtp_encryption" class="w-full text-xs font-semibold px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 focus:bg-white transition-all text-slate-800 cursor-pointer">
            <option value="none" <?= ($settings['smtp_encryption'] ?? '') === 'none' ? 'selected' : '' ?>>None (Tidak Aman)</option>
            <option value="tls" <?= ($settings['smtp_encryption'] ?? '') === 'tls' ? 'selected' : '' ?>>TLS (Rekomendasi)</option>
            <option value="ssl" <?= ($settings['smtp_encryption'] ?? '') === 'ssl' ? 'selected' : '' ?>>SSL</option>
          </select>
        </div>

        <div class="space-y-1.5">
          <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Email Pengirim (From Address)</label>
          <input type="email" name="smtp_from_address" value="<?= htmlspecialchars($settings['smtp_from_address'] ?? '') ?>" class="w-full text-xs font-semibold px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 focus:bg-white transition-all text-slate-800" required>
        </div>

        <div class="space-y-1.5">
          <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Nama Pengirim (From Name)</label>
          <input type="text" name="smtp_from_name" value="<?= htmlspecialchars($settings['smtp_from_name'] ?? '') ?>" class="w-full text-xs font-semibold px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 focus:bg-white transition-all text-slate-800" required>
        </div>
      </div>

      <div class="flex justify-end pt-3">
        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs px-5 py-3 rounded-xl transition-all shadow-md active:scale-95 cursor-pointer">
          Simpan SMTP Email
        </button>
      </div>
    </form>
  </div>
</div>

<script>
function switchTab(tabId, btnEl) {
  document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
  document.getElementById(tabId).classList.remove('hidden');

  document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.classList.remove('border-indigo-600', 'text-indigo-600');
    btn.classList.add('border-transparent', 'text-slate-500', 'hover:text-slate-700', 'hover:border-slate-350');
  });

  btnEl.classList.remove('border-transparent', 'text-slate-500', 'hover:text-slate-700', 'hover:border-slate-350');
  btnEl.classList.add('border-indigo-600', 'text-indigo-600');
}
</script>
