<?php

/**
 * @var array{total_applicants: mixed, total_payments: mixed, total_verifications: mixed, total_accepted: mixed} $stats
 * @var array $trend_labels
 * @var array $trend_data
 * @var array $program_labels
 * @var array $program_data
 * @var array $recent_activities
 */

if (!function_exists('time_ago')) {
  function time_ago($timestamp)
  {
    $time = strtotime($timestamp);
    $diff = time() - $time;
    if ($diff < 60) return 'Baru saja';
    $mins = round($diff / 60);
    if ($mins < 60) return $mins . ' menit lalu';
    $hours = round($diff / 3600);
    if ($hours < 24) return $hours . ' jam lalu';
    $days = round($diff / 86400);
    return $days . ' hari lalu';
  }
}

$currentUserRole = $_SESSION['auth.user_role'] ?? 'Admin';
$currentUserName = $_SESSION['auth.user_name'] ?? 'Administrator';
?>
<!-- Include Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="w-full space-y-6">
  <!-- Hero / Welcome Banner -->
  <div class="relative overflow-hidden rounded-2xl bg-white p-6 md:p-7 shadow-sm border border-slate-200/80">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div class="space-y-2">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-50 border border-indigo-100 text-xs font-semibold text-indigo-700">
          <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
          Sistem PMB Aktif & Realtime
        </div>
        <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight text-slate-900">
          Selamat Datang, <?= htmlspecialchars($currentUserName) ?>! 👋
        </h1>
        <p class="text-xs md:text-sm text-slate-500 max-w-2xl leading-relaxed">
          Pantau dinamika pendaftaran mahasiswa baru, kelola verifikasi berkas, dan validasi pembayaran dengan cepat melalui panel kendali terpadu.
        </p>
      </div>

      <div class="flex items-center gap-3 self-start md:self-center shrink-0">
        <div class="text-right hidden sm:block">
          <div class="text-xs font-bold text-slate-800"><?= date('l, d F Y') ?></div>
          <div class="text-[10px] text-slate-400 font-medium">Tahun Akademik Aktif</div>
        </div>
        <div class="h-8 w-px bg-slate-200 hidden sm:block"></div>
        <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-indigo-50 border border-indigo-100 text-indigo-700 font-bold text-xs">
          <i data-lucide="shield-check" class="w-4 h-4 text-indigo-600"></i>
          <?= htmlspecialchars(strtoupper($currentUserRole)) ?>
        </span>
      </div>
    </div>
  </div>

  <!-- KPI Metric Cards Grid -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
    <!-- Stat 1: Total Pendaftar -->
    <div class="bg-white rounded-xl p-6 border border-slate-200/80 shadow-sm flex flex-col justify-between hover:shadow-md transition-shadow">
      <div>
        <div class="flex items-center justify-between pb-2">
          <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Pendaftar</span>
          <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
            <i data-lucide="users" class="w-5 h-5"></i>
          </div>
        </div>
        <div class="mt-4 flex items-center justify-between gap-2">
          <strong class="text-3xl font-extrabold text-slate-900 leading-none">
            <?= number_format((int)$stats['total_applicants']) ?>
          </strong>
          <span class="text-[11px] font-bold text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-full border border-indigo-100">
            Akun Terdaftar
          </span>
        </div>
      </div>
      <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
        <span>Semua Gelombang</span>
        <a data-spa href="<?= getBaseUrl('/admin/registrants') ?>" class="font-bold text-indigo-600 hover:text-indigo-700 inline-flex items-center gap-1">
          Lihat <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
        </a>
      </div>
    </div>

    <!-- Stat 2: Pembayaran Lunas -->
    <div class="bg-white rounded-xl p-6 border border-slate-200/80 shadow-sm flex flex-col justify-between hover:shadow-md transition-shadow">
      <div>
        <div class="flex items-center justify-between pb-2">
          <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pembayaran Lunas</span>
          <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
            <i data-lucide="credit-card" class="w-5 h-5"></i>
          </div>
        </div>
        <div class="mt-4 flex items-center justify-between gap-2">
          <strong class="text-3xl font-extrabold text-slate-900 leading-none">
            <?= number_format((int)$stats['total_payments']) ?>
          </strong>
          <span class="text-[11px] font-bold text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-full border border-indigo-100">
            Terverifikasi
          </span>
        </div>
      </div>
      <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
        <span>Biaya Formulir</span>
        <a data-spa href="<?= getBaseUrl('/admin/payments') ?>" class="font-bold text-indigo-600 hover:text-indigo-700 inline-flex items-center gap-1">
          Periksa <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
        </a>
      </div>
    </div>

    <!-- Stat 3: Total Verifikasi -->
    <div class="bg-white rounded-xl p-6 border border-slate-200/80 shadow-sm flex flex-col justify-between hover:shadow-md transition-shadow">
      <div>
        <div class="flex items-center justify-between pb-2">
          <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Berkas Lengkap</span>
          <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
            <i data-lucide="folder-check" class="w-5 h-5"></i>
          </div>
        </div>
        <div class="mt-4 flex items-center justify-between gap-2">
          <strong class="text-3xl font-extrabold text-slate-900 leading-none">
            <?= number_format((int)$stats['total_verifications']) ?>
          </strong>
          <span class="text-[11px] font-bold text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-full border border-indigo-100">
            Valid
          </span>
        </div>
      </div>
      <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
        <span>Dokumen Pendaftar</span>
        <a data-spa href="<?= getBaseUrl('/admin/verifications') ?>" class="font-bold text-indigo-600 hover:text-indigo-700 inline-flex items-center gap-1">
          Tinjau <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
        </a>
      </div>
    </div>

    <!-- Stat 4: Lolos Seleksi -->
    <div class="bg-white rounded-xl p-6 border border-slate-200/80 shadow-sm flex flex-col justify-between hover:shadow-md transition-shadow">
      <div>
        <div class="flex items-center justify-between pb-2">
          <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Lulus Seleksi</span>
          <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
            <i data-lucide="graduation-cap" class="w-5 h-5"></i>
          </div>
        </div>
        <div class="mt-4 flex items-center justify-between gap-2">
          <strong class="text-3xl font-extrabold text-slate-900 leading-none">
            <?= number_format((int)$stats['total_accepted']) ?>
          </strong>
          <span class="text-[11px] font-bold text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-full border border-indigo-100">
            Diterima
          </span>
        </div>
      </div>
      <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
        <span>Calon Mahasiswa</span>
        <a data-spa href="<?= getBaseUrl('/admin/selection') ?>" class="font-bold text-indigo-600 hover:text-indigo-700 inline-flex items-center gap-1">
          Hasil <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
        </a>
      </div>
    </div>
  </div>

  <!-- Analytics & Side Panel Grid -->
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left Column: Visual Analytics -->
    <div class="lg:col-span-2 space-y-6">
      <!-- Chart 1: Registration Trends -->
      <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-200/80 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <div>
            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Tren Pendaftaran Mingguan</h3>
            <p class="text-xs text-slate-500 mt-0.5">Grafik pendaftar baru dalam 7 hari terakhir</p>
          </div>
          <span class="text-xs font-semibold text-slate-500 bg-slate-100 px-3 py-1 rounded-full">
            7 Hari Terakhir
          </span>
        </div>
        <div class="h-64 relative">
          <canvas id="registrationTrendChart"></canvas>
        </div>
      </div>

      <!-- Chart 2: Program Applicant Distribution -->
      <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-200/80 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <div>
            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Peminat Program Studi Terbanyak</h3>
            <p class="text-xs text-slate-500 mt-0.5">Top 5 pilihan program studi pertama oleh pendaftar</p>
          </div>
          <span class="text-xs font-semibold text-slate-500 bg-slate-100 px-3 py-1 rounded-full">
            Distribusi Pilihan 1
          </span>
        </div>
        <div class="h-64 relative">
          <canvas id="programChoiceChart"></canvas>
        </div>
      </div>
    </div>

    <!-- Right Column: Quick Shortcuts & Audit Timeline -->
    <div class="space-y-6">
      <!-- Quick Navigation Actions -->
      <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-200/80 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Aksi Cepat Admin</h3>
          <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Shortcut</span>
        </div>

        <div class="grid grid-cols-1 gap-3">
          <?php if (has_permission('verify_payment')): ?>
            <a data-spa href="<?= getBaseUrl('/admin/payments') ?>" class="group flex items-center justify-between p-3.5 rounded-xl border border-slate-200/80 hover:bg-slate-50 hover:border-slate-300 transition-all">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                  <i data-lucide="credit-card" class="w-4 h-4"></i>
                </div>
                <div>
                  <div class="text-xs font-bold text-slate-800">Verifikasi Pembayaran</div>
                  <div class="text-[11px] text-slate-400">Validasi slip & biaya pendaftaran</div>
                </div>
              </div>
              <i data-lucide="arrow-up-right" class="w-4 h-4 text-slate-400 group-hover:text-slate-600 transition-colors"></i>
            </a>
          <?php endif; ?>

          <?php if (has_permission('verify_document')): ?>
            <a data-spa href="<?= getBaseUrl('/admin/verifications') ?>" class="group flex items-center justify-between p-3.5 rounded-xl border border-slate-200/80 hover:bg-slate-50 hover:border-slate-300 transition-all">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                  <i data-lucide="folder-check" class="w-4 h-4"></i>
                </div>
                <div>
                  <div class="text-xs font-bold text-slate-800">Verifikasi Berkas</div>
                  <div class="text-[11px] text-slate-400">Pemeriksaan ijazah, KTP & rapor</div>
                </div>
              </div>
              <i data-lucide="arrow-up-right" class="w-4 h-4 text-slate-400 group-hover:text-slate-600 transition-colors"></i>
            </a>
          <?php endif; ?>

          <?php if (has_permission('manage_selection')): ?>
            <a data-spa href="<?= getBaseUrl('/admin/selection') ?>" class="group flex items-center justify-between p-3.5 rounded-xl border border-slate-200/80 hover:bg-slate-50 hover:border-slate-300 transition-all">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                  <i data-lucide="award" class="w-4 h-4"></i>
                </div>
                <div>
                  <div class="text-xs font-bold text-slate-800">Seleksi & Kelulusan</div>
                  <div class="text-[11px] text-slate-400">Nilai ujian & penerbitan status</div>
                </div>
              </div>
              <i data-lucide="arrow-up-right" class="w-4 h-4 text-slate-400 group-hover:text-slate-600 transition-colors"></i>
            </a>
          <?php endif; ?>

          <?php if (has_permission('manage_users')): ?>
            <a data-spa href="<?= getBaseUrl('/admin/users') ?>" class="group flex items-center justify-between p-3.5 rounded-xl border border-slate-200/80 hover:bg-slate-50 hover:border-slate-300 transition-all">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                  <i data-lucide="key-round" class="w-4 h-4"></i>
                </div>
                <div>
                  <div class="text-xs font-bold text-slate-800">Hak Akses Pengguna</div>
                  <div class="text-[11px] text-slate-400">Akses akun panitia & peran</div>
                </div>
              </div>
              <i data-lucide="arrow-up-right" class="w-4 h-4 text-slate-400 group-hover:text-slate-600 transition-colors"></i>
            </a>
          <?php endif; ?>

          <?php if (has_permission('manage_settings')): ?>
            <a data-spa href="<?= getBaseUrl('/admin/master') ?>" class="group flex items-center justify-between p-3.5 rounded-xl border border-slate-200/80 hover:bg-slate-50 hover:border-slate-300 transition-all">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                  <i data-lucide="database" class="w-4 h-4"></i>
                </div>
                <div>
                  <div class="text-xs font-bold text-slate-800">Kelola Data Master</div>
                  <div class="text-[11px] text-slate-400">Gelombang, prodi & format NIM</div>
                </div>
              </div>
              <i data-lucide="arrow-up-right" class="w-4 h-4 text-slate-400 group-hover:text-slate-600 transition-colors"></i>
            </a>
          <?php endif; ?>
        </div>
      </div>

      <!-- Recent System Audit Log -->
      <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-200/80 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Aktivitas Terkini</h3>
          <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Log</span>
        </div>

        <div class="space-y-3">
          <?php if (empty($recent_activities)): ?>
            <div class="text-center py-6 text-slate-400 text-xs font-semibold">
              Belum ada log aktivitas tercatat
            </div>
          <?php else: ?>
            <?php foreach ($recent_activities as $activity): ?>
              <div class="flex items-start gap-3 p-3 rounded-xl bg-slate-50 border border-slate-200/60 text-xs">
                <div class="w-2 h-2 rounded-full bg-indigo-600 mt-1.5 shrink-0"></div>
                <div class="flex-1 min-w-0">
                  <div class="flex items-center justify-between gap-2">
                    <span class="font-bold text-slate-800 truncate">
                      <?= htmlspecialchars($activity['username'] ?? 'Sistem') ?>
                    </span>
                    <span class="text-[10px] text-slate-400 shrink-0">
                      <?= time_ago($activity['created_at']) ?>
                    </span>
                  </div>
                  <p class="text-slate-500 text-[11px] mt-0.5 break-words">
                    <?= htmlspecialchars($activity['description']) ?>
                  </p>
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
    // Ensure Lucide icons render
    if (window.lucide && typeof window.lucide.createIcons === 'function') {
      window.lucide.createIcons();
    }

    // 1. Weekly Registration Trend Chart
    const trendCanvas = document.getElementById('registrationTrendChart');
    if (trendCanvas) {
      const trendCtx = trendCanvas.getContext('2d');
      const gradient = trendCtx.createLinearGradient(0, 0, 0, 260);
      gradient.addColorStop(0, 'rgba(79, 70, 229, 0.28)');
      gradient.addColorStop(1, 'rgba(79, 70, 229, 0.00)');

      new Chart(trendCtx, {
        type: 'line',
        data: {
          labels: <?= json_encode($trend_labels ?? ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu']) ?>,
          datasets: [{
            label: 'Pendaftar Baru',
            data: <?= json_encode($trend_data ?? [0, 0, 0, 0, 0, 0, 0]) ?>,
            borderColor: '#4f46e5',
            borderWidth: 2.5,
            backgroundColor: gradient,
            tension: 0.38,
            fill: true,
            pointBackgroundColor: '#ffffff',
            pointBorderColor: '#4f46e5',
            pointBorderWidth: 2,
            pointRadius: 4,
            pointHoverRadius: 6,
            pointHoverBackgroundColor: '#4f46e5',
            pointHoverBorderColor: '#ffffff',
            pointHoverBorderWidth: 2,
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          interaction: {
            intersect: false,
            mode: 'index',
          },
          plugins: {
            legend: {
              display: false
            },
            tooltip: {
              backgroundColor: '#0f172a',
              titleColor: '#ffffff',
              bodyColor: '#e2e8f0',
              padding: 10,
              borderRadius: 8,
              boxPadding: 4,
              usePointStyle: true,
              callbacks: {
                label: function(context) {
                  return ' ' + context.parsed.y + ' Calon Mahasiswa';
                }
              }
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              grid: {
                color: '#f1f5f9',
                drawBorder: false
              },
              ticks: {
                stepSize: 1,
                font: {
                  size: 11,
                  weight: '500'
                },
                color: '#64748b'
              }
            },
            x: {
              grid: {
                display: false,
                drawBorder: false
              },
              ticks: {
                font: {
                  size: 11,
                  weight: '600'
                },
                color: '#64748b'
              }
            }
          }
        }
      });
    }

    // 2. Program Choice Bar Chart
    const choiceCanvas = document.getElementById('programChoiceChart');
    if (choiceCanvas) {
      const choiceCtx = choiceCanvas.getContext('2d');
      new Chart(choiceCtx, {
        type: 'bar',
        data: {
          labels: <?= json_encode($program_labels ?? []) ?>,
          datasets: [{
            label: 'Jumlah Pendaftar',
            data: <?= json_encode($program_data ?? []) ?>,
            backgroundColor: [
              'rgba(79, 70, 229, 0.90)',
              'rgba(99, 102, 241, 0.85)',
              'rgba(129, 140, 248, 0.80)',
              'rgba(165, 180, 252, 0.80)',
              'rgba(199, 210, 254, 0.85)'
            ],
            borderRadius: 6,
            borderSkipped: false,
            maxBarThickness: 38
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: false
            },
            tooltip: {
              backgroundColor: '#0f172a',
              titleColor: '#ffffff',
              bodyColor: '#e2e8f0',
              padding: 10,
              borderRadius: 8,
              boxPadding: 4,
              usePointStyle: true,
              callbacks: {
                label: function(context) {
                  return ' ' + context.parsed.y + ' Pendaftar';
                }
              }
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              grid: {
                color: '#f1f5f9',
                drawBorder: false
              },
              ticks: {
                stepSize: 1,
                font: {
                  size: 11,
                  weight: '500'
                },
                color: '#64748b'
              }
            },
            x: {
              grid: {
                display: false,
                drawBorder: false
              },
              ticks: {
                font: {
                  size: 11,
                  weight: '600'
                },
                color: '#64748b',
                callback: function(value, index) {
                  const label = this.getLabelForValue(value);
                  return label.length > 15 ? label.substring(0, 15) + '...' : label;
                }
              }
            }
          }
        }
      });
    }
  })();
</script>