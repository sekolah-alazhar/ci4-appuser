<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php
    $s      = $stats;
    $isAdmin = ($role === 'admin');
    $cards = [
        ['label' => 'Total User',   'value' => $s['total'],     'icon' => 'fa-users',        'bg' => 'bg-indigo-500',   'shadow' => 'shadow-indigo-200',  'sub' => 'Seluruh user terdaftar'],
        ['label' => 'Admin',        'value' => $s['admin'],     'icon' => 'fa-user-shield',  'bg' => 'bg-blue-500',     'shadow' => 'shadow-blue-200',    'sub' => 'User dengan role admin'],
        ['label' => 'Guest',        'value' => $s['guest'],     'icon' => 'fa-user',         'bg' => 'bg-emerald-500',  'shadow' => 'shadow-emerald-200', 'sub' => 'User dengan role guest'],
        ['label' => 'Baru Bulan Ini','value' => $s['thisMonth'], 'icon' => 'fa-calendar-plus','bg' => 'bg-amber-500',    'shadow' => 'shadow-amber-200',   'sub' => date('F Y')],
    ];
?>

<!-- Welcome banner -->
<div class="rounded-xl bg-gradient-to-r from-indigo-600 via-indigo-700 to-blue-700 text-white p-6 mb-6 shadow-lg relative overflow-hidden">
    <div class="absolute -right-8 -top-8 w-40 h-40 bg-white/10 rounded-full"></div>
    <div class="absolute right-16 -bottom-14 w-32 h-32 bg-white/5 rounded-full"></div>
    <div class="relative">
        <p class="text-indigo-200 text-sm font-medium uppercase tracking-wide">Selamat datang kembali</p>
        <h2 class="text-2xl font-bold mt-1"><?= esc(session()->get('name')) ?> 👋</h2>
        <p class="text-indigo-100 text-sm mt-2 max-w-xl">
            Anda login sebagai <span class="font-semibold uppercase"><?= esc($role) ?></span>.
            <?php if ($isAdmin): ?>
                Kelola seluruh user sistem melalui menu <strong>Manajemen User</strong>.
            <?php else: ?>
                Anda dapat melihat daftar user melalui menu <strong>Daftar User</strong>.
            <?php endif; ?>
        </p>
    </div>
</div>

<!-- Stat cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
    <?php foreach ($cards as $c): ?>
        <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition p-5 flex items-center border border-gray-100">
            <div class="w-12 h-12 <?= $c['bg'] ?> <?= $c['shadow'] ?> rounded-xl flex items-center justify-center mr-4 shadow-md shrink-0">
                <i class="fas <?= $c['icon'] ?> text-white text-lg"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wide"><?= $c['label'] ?></p>
                <p class="text-2xl font-bold text-gray-800 leading-tight"><?= number_format($c['value']) ?></p>
                <p class="text-[11px] text-gray-400 truncate"><?= $c['sub'] ?></p>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Charts -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
    <!-- Bar: pendaftaran 6 bulan -->
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="font-semibold text-gray-800"><i class="fas fa-chart-column text-indigo-500 mr-2"></i>User Baru per Bulan</h3>
                <p class="text-xs text-gray-400">6 bulan terakhir</p>
            </div>
        </div>
        <div class="h-64"><canvas id="barChart"></canvas></div>
    </div>

    <!-- Doughnut: distribusi role -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <h3 class="font-semibold text-gray-800 mb-1"><i class="fas fa-chart-pie text-indigo-500 mr-2"></i>Distribusi Role</h3>
        <p class="text-xs text-gray-400 mb-4">Rasio admin vs guest</p>
        <div class="h-56"><canvas id="pieChart"></canvas></div>
        <div class="mt-4 flex justify-center space-x-6 text-sm">
            <span class="flex items-center"><span class="w-3 h-3 rounded-full bg-blue-500 mr-2"></span>Admin (<?= $s['admin'] ?>)</span>
            <span class="flex items-center"><span class="w-3 h-3 rounded-full bg-emerald-500 mr-2"></span>Guest (<?= $s['guest'] ?>)</span>
        </div>
    </div>
</div>

<!-- Recent users -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-5 py-4 border-b flex items-center justify-between">
        <h3 class="font-semibold text-gray-800"><i class="fas fa-clock-rotate-left text-indigo-500 mr-2"></i>User Terbaru</h3>
        <a href="<?= $isAdmin ? '/admin/users' : '/guest/users' ?>" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
            Lihat semua <i class="fas fa-arrow-right ml-1 text-xs"></i>
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                <tr>
                    <th class="px-5 py-3 text-left font-semibold">Nama</th>
                    <th class="px-5 py-3 text-left font-semibold">Username</th>
                    <th class="px-5 py-3 text-left font-semibold">Email</th>
                    <th class="px-5 py-3 text-left font-semibold">Role</th>
                    <th class="px-5 py-3 text-left font-semibold">Terdaftar</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (empty($s['recent'])): ?>
                    <tr><td colspan="5" class="px-5 py-8 text-center text-gray-400">Belum ada data user</td></tr>
                <?php else: foreach ($s['recent'] as $u): ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-3">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center mr-3 text-xs font-bold shrink-0">
                                    <?= strtoupper(substr($u['name'], 0, 1)) ?>
                                </div>
                                <span class="font-medium text-gray-800"><?= esc($u['name']) ?></span>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-gray-600"><?= esc($u['username']) ?></td>
                        <td class="px-5 py-3 text-gray-600"><?= esc($u['email']) ?></td>
                        <td class="px-5 py-3">
                            <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold <?= $u['role'] === 'admin' ? 'bg-indigo-100 text-indigo-700' : 'bg-emerald-100 text-emerald-700' ?>">
                                <?= strtoupper(esc($u['role'])) ?>
                            </span>
                        </td>
                        <td class="px-5 py-3 text-gray-500 text-xs">
                            <?= !empty($u['created_at']) ? date('d M Y H:i', strtotime($u['created_at'])) : '-' ?>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    const labels = <?= json_encode($stats['labels']) ?>;
    const counts = <?= json_encode($stats['counts']) ?>;

    // Bar chart: user baru per bulan
    new Chart(document.getElementById('barChart'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'User baru',
                data: counts,
                backgroundColor: 'rgba(99, 102, 241, 0.85)',
                hoverBackgroundColor: 'rgba(79, 70, 229, 1)',
                borderRadius: 8,
                maxBarThickness: 48
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0, stepSize: 1 }, grid: { color: '#f1f5f9' } },
                x: { grid: { display: false } }
            }
        }
    });

    // Doughnut: distribusi role
    new Chart(document.getElementById('pieChart'), {
        type: 'doughnut',
        data: {
            labels: ['Admin', 'Guest'],
            datasets: [{
                data: [<?= (int) $stats['admin'] ?>, <?= (int) $stats['guest'] ?>],
                backgroundColor: ['#3b82f6', '#10b981'],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: { legend: { display: false } }
        }
    });
</script>
<?= $this->endSection() ?>
