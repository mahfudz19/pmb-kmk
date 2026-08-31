<?php

/**
 * @var array{total_applicants: mixed, total_payments: mixed, total_verifications: mixed, total_accepted: mixed} $stats
 */

if (!function_exists('time_ago')) {
  function time_ago($timestamp)
  {
    $time = strtotime($timestamp);
    $diff = time() - $time;
    if ($diff < 60) return 'Baru saja';
    $mins = round($diff / 60);
    if ($mins < 60) return $mins . ' menit yang lalu';
    $hours = round($diff / 3600);
    if ($hours < 24) return $hours . ' jam yang lalu';
    $days = round($diff / 86400);
    return $days . ' hari yang lalu';
  }
}
?>
<!-- Include Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="w-full py-2 space-y-8">
  <!-- Top header -->
  <div class="flex justify-between items-center bg-white rounded-xl p-6 shadow-sm border border-slate-200/80">
    <div>
      <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Dashboard Administrator</h2>
      <p class="mt-1 text-sm text-slate-500">Pantau aktivitas pendaftaran, kelola verifikasi berkas, dan periksa statistik real-time.</p>
    </div>
    <span class="bg-indigo-100 text-indigo-700 text-xs px-3 py-1.5 rounded-full font-bold uppercase">Super Admin</span>
  </div>

  <!-- Stats Grid -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
    <!-- Stat 1 -->
    <div class="bg-white rounded-xl p-6 border border-slate-200/80 shadow-sm flex items-center justify-between">
      <div class="space-y-1">
        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Total Pendaftar</span>
        <strong class="text-3xl font-extrabold text-slate-900"><?= htmlspecialchars($stats['total_applicants']) ?></strong>
      </div>
      <span class="text-3xl bg-indigo-50 p-3 rounded-xl text-indigo-650">👥</span>
    </div>

    <!-- Stat 2 -->
    <div class="bg-white rounded-xl p-6 border border-slate-200/80 shadow-sm flex items-center justify-between">
      <div class="space-y-1">
        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Pembayaran Lunas</span>
        <strong class="text-3xl font-extrabold text-slate-900"><?= htmlspecialchars($stats['total_payments']) ?></strong>
      </div>
      <span class="text-3xl bg-emerald-50 p-3 rounded-xl text-emerald-650">💳</span>
    </div>

    <!-- Stat 3 -->
    <div class="bg-white rounded-xl p-6 border border-slate-200/80 shadow-sm flex items-center justify-between">
      <div class="space-y-1">
        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Total Verifikasi</span>
        <strong class="text-3xl font-extrabold text-slate-900"><?= htmlspecialchars($stats['total_verifications']) ?></strong>
      </div>
      <span class="text-3xl bg-amber-50 p-3 rounded-xl text-amber-650">📁</span>
    </div>

    <!-- Stat 4 -->
    <div class="bg-white rounded-xl p-6 border border-slate-200/80 shadow-sm flex items-center justify-between">
      <div class="space-y-1">
        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Lolos Seleksi</span>
        <strong class="text-3xl font-extrabold text-slate-900"><?= htmlspecialchars($stats['total_accepted']) ?></strong>
      </div>
      <span class="text-3xl bg-violet-50 p-3 rounded-xl text-violet-650">🎓</span>
    </div>
  </div>

  <!-- Content Grid -->
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Chart Column (Left) -->
    <div class="lg:col-span-2 space-y-8">
      <!-- Chart 1: Tren Pendaftaran -->
      <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-200/80 space-y-4">
        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider border-b border-slate-100 pb-2">Tren Pendaftaran Mingguan</h3>
        <div class="h-64 relative">
          <canvas id="registrationTrendChart"></canvas>
        </div>
      </div>

      <!-- Chart 2: Pilihan Prodi -->
      <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-200/80 space-y-4">
        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider border-b border-slate-100 pb-2">Peminat Program Studi</h3>
        <div class="h-64 relative">
          <canvas id="programChoiceChart"></canvas>
        </div>
      </div>
    </div>

    <!-- Quick Actions & Log Column (Right) -->
    <div class="space-y-8">
      <!-- Quick Actions -->
      <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-200/80 space-y-4">
        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider border-b border-slate-100 pb-2">Aksi Cepat Admin</h3>
        <div class="grid grid-cols-1 gap-3">
          <?php if (has_permission('manage_users')): ?>
            <a data-spa href="<?= getBaseUrl('/admin/users') ?>" class="flex items-center gap-3 p-3 border border-slate-150/60 rounded-xl hover:bg-indigo-50 hover:text-indigo-700 hover:border-indigo-200 transition-all font-semibold text-slate-700 text-xs">
              <span>🔑</span> Kelola Hak Akses Pengguna
            </a>
          <?php endif; ?>
          <?php if (has_permission('verify_payment')): ?>
            <a data-spa href="<?= getBaseUrl('/admin/payments') ?>" class="flex items-center gap-3 p-3 border border-slate-150/60 rounded-xl hover:bg-indigo-50 hover:text-indigo-700 hover:border-indigo-200 transition-all font-semibold text-slate-700 text-xs">
              <span>💳</span> Verifikasi Bukti Transfer
            </a>
          <?php endif; ?>
          <?php if (has_permission('verify_document')): ?>
            <a data-spa href="<?= getBaseUrl('/admin/verifications') ?>" class="flex items-center gap-3 p-3 border border-slate-150/60 rounded-xl hover:bg-indigo-50 hover:text-indigo-700 hover:border-indigo-200 transition-all font-semibold text-slate-700 text-xs">
              <span>📂</span> Verifikasi Berkas Akademik
            </a>
          <?php endif; ?>
          <?php if (has_permission('manage_settings')): ?>
            <a data-spa href="<?= getBaseUrl('/admin/master') ?>" class="flex items-center gap-3 p-3 border border-slate-150/60 rounded-xl hover:bg-indigo-50 hover:text-indigo-700 hover:border-indigo-200 transition-all font-semibold text-slate-700 text-xs">
              <span>⚙️</span> Kelola Data Master
            </a>
          <?php endif; ?>
        </div>
      </div>

      <!-- Recent Log Activity -->
      <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-200/80 space-y-4">
        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider border-b border-slate-100 pb-2">Aktivitas Terbaru</h3>
        <div class="space-y-4">
          <?php if (empty($recent_activities)): ?>
            <div class="text-center py-6 text-slate-400 text-xs font-semibold">
              Tidak ada aktivitas terbaru
            </div>
          <?php else: ?>
            <?php foreach ($recent_activities as $activity): ?>
              <div class="flex items-start gap-3 text-xs leading-normal">
                <span class="text-indigo-500 mt-0.5">●</span>
                <div>
                  <p class="font-bold text-slate-800"><?= htmlspecialchars($activity['username'] ?? 'System') ?></p>
                  <p class="text-slate-500 text-[10px]"><?= htmlspecialchars($activity['description']) ?></p>
                  <span class="text-[9px] text-slate-400"><?= time_ago($activity['created_at']) ?></span>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  (function() {
    // 1. Weekly Registration Trend Chart
    const trendCtx = document.getElementById('registrationTrendChart').getContext('2d');
    new Chart(trendCtx, {
      type: 'line',
      data: {
        labels: <?= json_encode($trend_labels ?? ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu']) ?>,
        datasets: [{
          label: 'Calon Mahasiswa Baru',
          data: <?= json_encode($trend_data ?? [0, 0, 0, 0, 0, 0, 0]) ?>,
          borderColor: 'rgb(79, 70, 229)',
          backgroundColor: 'rgba(79, 70, 229, 0.05)',
          tension: 0.4,
          fill: true,
          borderWidth: 3,
          pointBackgroundColor: 'rgb(79, 70, 229)',
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false
          }
        },
        scales: {
          y: {
            grid: {
              color: '#f1f5f9'
            },
            ticks: {
              font: {
                size: 10
              }
            }
          },
          x: {
            grid: {
              display: false
            },
            ticks: {
              font: {
                size: 10
              }
            }
          }
        }
      }
    });

    // 2. Program Choice Chart
    const choiceCtx = document.getElementById('programChoiceChart').getContext('2d');
    new Chart(choiceCtx, {
      type: 'bar',
      data: {
        labels: <?= json_encode($program_labels ?? []) ?>,
        datasets: [{
          data: <?= json_encode($program_data ?? []) ?>,
          backgroundColor: [
            'rgba(79, 70, 229, 0.85)',
            'rgba(99, 102, 241, 0.85)',
            'rgba(167, 139, 250, 0.85)',
            'rgba(244, 63, 94, 0.85)',
            'rgba(251, 191, 36, 0.85)'
          ],
          borderRadius: 8,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false
          }
        },
        scales: {
          y: {
            grid: {
              color: '#f1f5f9'
            },
            ticks: {
              font: {
                size: 10
              }
            }
          },
          x: {
            grid: {
              display: false
            },
            ticks: {
              font: {
                size: 10
              }
            }
          }
        }
      }
    });
  })();
</script>