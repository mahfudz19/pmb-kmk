<div class="w-full py-2">

  <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden">
    <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
      <div>
        <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Manajemen Pengguna</h2>
        <p class="mt-1 text-sm text-slate-500">Kelola peran (*role*) dan hak akses (*permissions*) pendaftar dan administrator secara dinamis.</p>
      </div>
      <span class="bg-indigo-100 text-indigo-700 text-xs px-3 py-1.5 rounded-full font-bold uppercase">Admin Panel</span>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-slate-100">
        <thead class="bg-slate-50/50">
          <tr>
            <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">ID</th>
            <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Nama</th>
            <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Email Address</th>
            <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Role</th>
            <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Permissions</th>
            <th class="px-6 py-4 class-right text-xs font-bold text-slate-400 uppercase tracking-wider text-right">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 bg-white">
          <?php foreach ($users as $u): ?>
            <tr class="hover:bg-slate-50/30 transition-colors">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-slate-500">#<?= htmlspecialchars($u['id']) ?></td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-800"><?= htmlspecialchars($u['name'] ?? '-') ?></td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600"><?= htmlspecialchars($u['email']) ?></td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">
                <?php if (($u['role'] ?? 'user') === 'admin'): ?>
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800 uppercase">Admin</span>
                <?php else: ?>
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-600 uppercase">User</span>
                <?php endif; ?>
              </td>
              <td class="px-6 py-4 text-sm text-slate-600 max-w-xs truncate">
                <?php 
                  $perms = json_decode($u['permissions'] ?? '[]', true);
                  if (empty($perms)) {
                    echo '<span class="text-slate-400 text-xs italic">Tanpa akses</span>';
                  } elseif (in_array('*', $perms, true)) {
                    echo '<span class="inline-flex items-center px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 font-semibold text-xs border border-emerald-200">Akses Penuh (*)</span>';
                  } else {
                    echo implode(', ', array_map(fn($p) => htmlspecialchars($p), $perms));
                  }
                ?>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                <button
                  type="button"
                  onclick="openEditModal(<?= htmlspecialchars(json_encode($u)) ?>)"
                  class="inline-flex items-center justify-center px-4 py-1.5 border border-slate-200 rounded-full shadow-sm text-xs font-bold text-slate-700 bg-white hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-200 transition-all cursor-pointer"
                >
                  Edit Akses
                </button>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Edit Permissions Modal -->
<div id="edit-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
  <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closeEditModal()"></div>
  <div class="relative bg-white rounded-3xl p-8 max-w-md w-full shadow-2xl border border-slate-100 space-y-6 transform scale-95 opacity-0 transition-all duration-200" id="edit-modal-card">
    <div class="flex justify-between items-center border-b border-slate-100 pb-4">
      <h3 class="text-xl font-bold text-slate-900">Ubah Hak Akses</h3>
      <button type="button" onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600 focus:outline-none text-2xl font-semibold">&times;</button>
    </div>

    <form id="edit-form" method="POST" action="/admin/users/update" class="space-y-6">
      <input type="hidden" name="user_id" id="edit-user-id">

      <div class="space-y-1">
        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Nama Pengguna</label>
        <div id="edit-user-name" class="text-sm font-bold text-slate-800 bg-slate-50 px-4 py-2.5 rounded-xl border border-slate-100"></div>
      </div>

      <div class="space-y-1">
        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Email Address</label>
        <div id="edit-user-email" class="text-sm text-slate-600 bg-slate-50 px-4 py-2.5 rounded-xl border border-slate-100"></div>
      </div>

      <div class="space-y-2">
        <label for="edit-role" class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Peran (Role)</label>
        <select
          name="role"
          id="edit-role"
          onchange="togglePermissionCheckboxes(this.value)"
          class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition-all bg-slate-50"
        >
          <option value="user">User (Pendaftar)</option>
          <option value="admin">Admin (Administrator)</option>
        </select>
      </div>

      <div class="space-y-3" id="permissions-section">
        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Izin Fitur (*Permissions*)</label>
        <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-4 space-y-3">
          <div class="flex items-start gap-3">
            <input type="checkbox" name="permissions[]" value="view_dashboard" id="p-view_dashboard" class="mt-1 h-4 w-4 rounded text-indigo-600 border-slate-350 focus:ring-indigo-500">
            <label for="p-view_dashboard" class="text-xs font-semibold text-slate-700 cursor-pointer">
              view_dashboard
              <span class="block text-[10px] font-medium text-slate-400">Mengakses dashboard dasar pendaftar</span>
            </label>
          </div>
          <div class="flex items-start gap-3">
            <input type="checkbox" name="permissions[]" value="manage_users" id="p-manage_users" class="mt-1 h-4 w-4 rounded text-indigo-600 border-slate-300 focus:ring-indigo-500">
            <label for="p-manage_users" class="text-xs font-semibold text-slate-700 cursor-pointer">
              manage_users
              <span class="block text-[10px] font-medium text-slate-400">Mengakses panel ini & mengelola user</span>
            </label>
          </div>
          <div class="flex items-start gap-3">
            <input type="checkbox" name="permissions[]" value="verify_payment" id="p-verify_payment" class="mt-1 h-4 w-4 rounded text-indigo-600 border-slate-300 focus:ring-indigo-500">
            <label for="p-verify_payment" class="text-xs font-semibold text-slate-700 cursor-pointer">
              verify_payment
              <span class="block text-[10px] font-medium text-slate-400">Memverifikasi berkas pembayaran pendaftaran</span>
            </label>
          </div>
          <div class="flex items-start gap-3">
            <input type="checkbox" name="permissions[]" value="verify_documents" id="p-verify_documents" class="mt-1 h-4 w-4 rounded text-indigo-600 border-slate-300 focus:ring-indigo-500">
            <label for="p-verify_documents" class="text-xs font-semibold text-slate-700 cursor-pointer">
              verify_documents
              <span class="block text-[10px] font-medium text-slate-400">Memverifikasi kelengkapan berkas akademik</span>
            </label>
          </div>
          <div class="flex items-start gap-3">
            <input type="checkbox" name="permissions[]" value="manage_settings" id="p-manage_settings" class="mt-1 h-4 w-4 rounded text-indigo-600 border-slate-300 focus:ring-indigo-500">
            <label for="p-manage_settings" class="text-xs font-semibold text-slate-700 cursor-pointer">
              manage_settings
              <span class="block text-[10px] font-medium text-slate-400">Mengubah konfigurasi sistem dan identitas kampus</span>
            </label>
          </div>
        </div>
      </div>

      <div class="flex gap-3 pt-2">
        <button type="button" onclick="closeEditModal()" class="flex-1 py-2.5 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-sm transition-colors focus:outline-none">
          Batal
        </button>
        <button type="submit" class="flex-1 py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl text-sm transition-colors shadow-sm focus:outline-none">
          Simpan Perubahan
        </button>
      </div>
    </form>
  </div>
</div>

<script>
  function openEditModal(user) {
    document.getElementById('edit-user-id').value = user.id;
    document.getElementById('edit-user-name').textContent = user.name || '-';
    document.getElementById('edit-user-email').textContent = user.email;
    document.getElementById('edit-role').value = user.role || 'user';

    // Uncheck all first
    const checkboxes = document.querySelectorAll('input[name="permissions[]"]');
    checkboxes.forEach(cb => cb.checked = false);

    // Check matching
    if (user.permissions) {
      try {
        const perms = JSON.parse(user.permissions);
        if (Array.isArray(perms)) {
          perms.forEach(p => {
            const cb = document.getElementById('p-' + p);
            if (cb) cb.checked = true;
          });
        }
      } catch (e) {}
    }

    togglePermissionCheckboxes(user.role || 'user');

    const modal = document.getElementById('edit-modal');
    const card = document.getElementById('edit-modal-card');
    modal.classList.remove('hidden');
    setTimeout(() => {
      card.classList.remove('scale-95', 'opacity-0');
      card.classList.add('scale-100', 'opacity-100');
    }, 10);
  }

  function closeEditModal() {
    const modal = document.getElementById('edit-modal');
    const card = document.getElementById('edit-modal-card');
    card.classList.remove('scale-100', 'opacity-100');
    card.classList.add('scale-95', 'opacity-0');
    setTimeout(() => {
      modal.classList.add('hidden');
    }, 200);
  }

  function togglePermissionCheckboxes(role) {
    const checkboxes = document.querySelectorAll('input[name="permissions[]"]');
    if (role === 'admin') {
      // Admin gets full permissions implicitly, check all and disable or keep them checked
      checkboxes.forEach(cb => {
        cb.checked = true;
        cb.disabled = true;
        // Add a hidden input to submit the values if needed, or handle in PHP (which we do)
      });
    } else {
      checkboxes.forEach(cb => {
        cb.disabled = false;
      });
    }
  }
</script>
