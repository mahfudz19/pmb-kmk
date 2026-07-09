<?php
/**
 * @var array $registration
 * @var array $programs
 * @var array $address
 * @var array $education
 * @var array $parent
 * @var array $study_programs
 */
?>

<div class="space-y-6">
  <!-- Top Bar -->
  <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-100 pb-5">
    <div class="space-y-1">
      <div class="flex items-center gap-2">
        <a data-spa href="/admin/registrants/detail?id=<?= $registration['id'] ?>" class="text-xs font-bold text-indigo-650 hover:text-indigo-700 flex items-center gap-1 transition-colors">
          <span>← Kembali ke Detail Pendaftar</span>
        </a>
      </div>
      <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Koreksi Data Pendaftar</h1>
      <p class="text-xs text-slate-500">Edit data calon mahasiswa secara langsung untuk memperbaiki kesalahan input pendaftaran.</p>
    </div>
  </div>

  <?php if (isset($_GET['error'])): ?>
    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-xl flex gap-3 text-red-800 text-xs">
      <span class="text-lg">⚠️</span>
      <div>
        <p class="font-bold">Gagal!</p>
        <p class="mt-0.5"><?= htmlspecialchars($_GET['error']) ?></p>
      </div>
    </div>
  <?php endif; ?>

  <form action="/admin/registrants/update" method="POST" class="space-y-6">
    <input type="hidden" name="id" value="<?= $registration['id'] ?>">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Left Forms (2 col span) -->
      <div class="lg:col-span-2 space-y-6">
        <!-- Biodata Pribadi -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-4">
          <h3 class="text-xs font-bold text-slate-450 uppercase tracking-widest border-b border-slate-100 pb-2">1. Biodata Pribadi</h3>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label for="full_name" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Nama Lengkap</label>
              <input type="text" name="full_name" id="full_name" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-xs transition-all bg-slate-50 font-semibold" value="<?= htmlspecialchars($registration['full_name']) ?>" required>
            </div>

            <div>
              <label for="phone" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">No. Telepon / WhatsApp</label>
              <input type="text" name="phone" id="phone" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-xs transition-all bg-slate-50 font-semibold" value="<?= htmlspecialchars($registration['phone']) ?>" required>
            </div>

            <div>
              <label for="nik" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">NIK (Nomor Induk Kependudukan)</label>
              <input type="text" name="nik" id="nik" minlength="16" maxlength="16" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-xs transition-all bg-slate-50 font-semibold" value="<?= htmlspecialchars($registration['nik']) ?>" required>
            </div>

            <div>
              <label for="nisn" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">NISN (Nomor Induk Siswa Nasional)</label>
              <input type="text" name="nisn" id="nisn" minlength="10" maxlength="10" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-xs transition-all bg-slate-50 font-semibold" value="<?= htmlspecialchars($registration['nisn']) ?>" required>
            </div>

            <div>
              <label for="birth_place" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Tempat Lahir</label>
              <input type="text" name="birth_place" id="birth_place" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-xs transition-all bg-slate-50 font-semibold" value="<?= htmlspecialchars($registration['birth_place']) ?>" required>
            </div>

            <div>
              <label for="birth_date" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Tanggal Lahir</label>
              <input type="date" name="birth_date" id="birth_date" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-xs transition-all bg-slate-50 font-semibold" value="<?= htmlspecialchars($registration['birth_date']) ?>" required>
            </div>

            <div>
              <label for="gender" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Jenis Kelamin</label>
              <select name="gender" id="gender" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-xs transition-all bg-slate-50 font-semibold text-slate-700" required>
                <option value="Laki-laki" <?= $registration['gender'] === 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                <option value="Perempuan" <?= $registration['gender'] === 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
              </select>
            </div>

            <div>
              <label for="religion" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Agama</label>
              <input type="text" name="religion" id="religion" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-xs transition-all bg-slate-50 font-semibold" value="<?= htmlspecialchars($registration['religion']) ?>" required>
            </div>
          </div>
        </div>

        <!-- Asal Sekolah -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-4">
          <h3 class="text-xs font-bold text-slate-450 uppercase tracking-widest border-b border-slate-100 pb-2">2. Asal Sekolah</h3>
          
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label for="school_name" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Nama Sekolah</label>
              <input type="text" name="school_name" id="school_name" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-xs transition-all bg-slate-50 font-semibold" value="<?= htmlspecialchars($education['school_name'] ?? '') ?>" required>
            </div>

            <div>
              <label for="major" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Jurusan</label>
              <input type="text" name="major" id="major" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-xs transition-all bg-slate-50 font-semibold" value="<?= htmlspecialchars($education['school_major'] ?? '') ?>" required>
            </div>

            <div>
              <label for="graduation_year" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Tahun Lulus</label>
              <input type="number" name="graduation_year" id="graduation_year" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-xs transition-all bg-slate-50 font-semibold" value="<?= htmlspecialchars($education['graduation_year'] ?? '') ?>" required>
            </div>
          </div>
        </div>

        <!-- Alamat -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-4">
          <h3 class="text-xs font-bold text-slate-450 uppercase tracking-widest border-b border-slate-100 pb-2">3. Alamat Rumah</h3>
          
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label for="province" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Provinsi</label>
              <input type="text" name="province" id="province" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-xs transition-all bg-slate-50 font-semibold" value="<?= htmlspecialchars($address['province'] ?? '') ?>" required>
            </div>

            <div>
              <label for="city" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Kota / Kabupaten</label>
              <input type="text" name="city" id="city" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-xs transition-all bg-slate-50 font-semibold" value="<?= htmlspecialchars($address['city'] ?? '') ?>" required>
            </div>

            <div>
              <label for="district" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Kecamatan</label>
              <input type="text" name="district" id="district" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-xs transition-all bg-slate-50 font-semibold" value="<?= htmlspecialchars($address['district'] ?? '') ?>" required>
            </div>
          </div>
          <div>
            <label for="address" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Alamat Detail (Jalan, RT/RW, Dusun)</label>
            <textarea name="address" id="address" rows="2" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-xs transition-all bg-slate-50 font-semibold" required><?= htmlspecialchars($address['address'] ?? '') ?></textarea>
          </div>
        </div>

        <!-- Orang Tua -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-4">
          <h3 class="text-xs font-bold text-slate-450 uppercase tracking-widest border-b border-slate-100 pb-2">4. Data Orang Tua</h3>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label for="father_name" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Nama Ayah</label>
              <input type="text" name="father_name" id="father_name" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-xs transition-all bg-slate-50 font-semibold" value="<?= htmlspecialchars($parent['father_name'] ?? '') ?>" required>
            </div>

            <div>
              <label for="mother_name" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Nama Ibu</label>
              <input type="text" name="mother_name" id="mother_name" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-xs transition-all bg-slate-50 font-semibold" value="<?= htmlspecialchars($parent['mother_name'] ?? '') ?>" required>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Sidebar Column (1 col span) -->
      <div class="lg:col-span-1 space-y-6">
        <!-- Pilihan Prodi -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-4">
          <h3 class="text-xs font-bold text-slate-450 uppercase tracking-widest border-b border-slate-100 pb-2">Program Studi</h3>
          
          <div class="space-y-4">
            <div>
              <label for="program1_id" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Pilihan 1 (Utama)</label>
              <select name="program1_id" id="program1_id" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-xs transition-all bg-slate-50 font-semibold text-slate-700" required>
                <?php foreach ($study_programs as $sp): ?>
                  <option value="<?= $sp['id'] ?>" <?= (int)($programs['program1_id'] ?? 0) === $sp['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($sp['name']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div>
              <label for="program2_id" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Pilihan 2 (Cadangan)</label>
              <select name="program2_id" id="program2_id" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-xs transition-all bg-slate-50 font-semibold text-slate-700">
                <option value="">Tidak Memilih Pilihan 2</option>
                <?php foreach ($study_programs as $sp): ?>
                  <option value="<?= $sp['id'] ?>" <?= (int)($programs['program2_id'] ?? 0) === $sp['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($sp['name']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
        </div>

        <!-- Submit Panel -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-4">
          <h3 class="text-xs font-bold text-slate-450 uppercase tracking-widest border-b border-slate-100 pb-2">Simpan Perubahan</h3>
          <p class="text-[11px] text-slate-500 leading-relaxed">Pastikan semua data hasil koreksi yang dimasukkan sudah benar sesuai berkas fisik atau instruksi resmi calon mahasiswa.</p>
          <div class="pt-2">
            <button type="submit" class="w-full inline-flex items-center justify-center px-6 py-3 border border-transparent rounded-full shadow-md text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 transition-all hover:-translate-y-0.5 cursor-pointer">
              💾 Simpan Koreksi Data
            </button>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>
