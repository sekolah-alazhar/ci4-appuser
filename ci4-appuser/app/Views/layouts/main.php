<?php
/**
 * Layout utama.
 * - Belum login : tampilan center (halaman login), tanpa footer.
 * - Sudah login : sidebar kiri + topbar, menu aktif highlight, tanpa footer hitam.
 */
$logged  = (bool) session()->get('logged_in');
$role    = session()->get('role');
$segs    = service('uri')->getSegments();
$current = $segs[1] ?? '';              // /admin/users -> 'users', /admin/dashboard -> 'dashboard'
$base    = $role === 'admin' ? '/admin' : '/guest';

$menu = [
    ['key' => 'dashboard', 'label' => 'Dashboard',      'icon' => 'fa-gauge-high', 'url' => $base . '/dashboard'],
    ['key' => 'users',     'label' => $role === 'admin' ? 'Manajemen User' : 'Daftar User',
                           'icon' => 'fa-users',        'url' => $base . '/users'],
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Manajemen User') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen antialiased">

<?php if ($logged): ?>
<div class="flex min-h-screen">

    <!-- ===== Sidebar ===== -->
    <aside id="sidebar"
           class="fixed inset-y-0 left-0 z-40 w-64 bg-slate-900 text-slate-300 flex flex-col transform -translate-x-full md:translate-x-0 transition-transform duration-200 ease-out">

        <!-- Brand -->
        <div class="h-16 flex items-center px-5 border-b border-slate-800">
            <div class="w-9 h-9 rounded-lg bg-indigo-600 flex items-center justify-center mr-3 shadow-md shadow-indigo-900/50">
                <i class="fas fa-users-cog text-white"></i>
            </div>
            <div class="leading-tight">
                <p class="text-white font-bold text-sm">User Management</p>
                <p class="text-slate-500 text-xs">users.pudin.eu.org</p>
            </div>
        </div>

        <!-- Menu -->
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
            <p class="px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500 mb-2">Menu</p>

            <?php foreach ($menu as $m): $active = ($current === $m['key']); ?>
                <a href="<?= $m['url'] ?>"
                   class="group flex items-center px-4 py-3 rounded-lg text-sm font-medium transition-all duration-150 relative
                          <?= $active
                              ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-900/40'
                              : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                    <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 rounded-r transition-all
                                 <?= $active ? 'bg-white' : 'bg-transparent group-hover:bg-slate-600' ?>"></span>
                    <i class="fas <?= $m['icon'] ?> w-5 mr-3 text-center <?= $active ? 'text-white' : 'text-slate-400 group-hover:text-indigo-400' ?>"></i>
                    <?= $m['label'] ?>
                    <?php if ($active): ?>
                        <i class="fas fa-chevron-right ml-auto text-xs text-indigo-200"></i>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </nav>

        <!-- Sidebar footer: user + logout -->
        <div class="p-3 border-t border-slate-800">
            <div class="flex items-center px-3 py-2 mb-2">
                <div class="w-8 h-8 rounded-full bg-indigo-500/20 border border-indigo-500/40 flex items-center justify-center mr-3">
                    <i class="fas fa-user text-indigo-300 text-xs"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-white text-sm font-semibold truncate"><?= esc(session()->get('name')) ?></p>
                    <span class="px-2 py-0.5 text-[10px] rounded-full font-semibold uppercase
                                 <?= $role === 'admin' ? 'bg-indigo-500/20 text-indigo-300' : 'bg-emerald-500/20 text-emerald-300' ?>">
                        <?= esc($role) ?>
                    </span>
                </div>
            </div>
            <a href="/logout"
               class="flex items-center justify-center w-full px-4 py-2.5 rounded-lg bg-slate-800 hover:bg-red-600
                      text-slate-300 hover:text-white text-sm font-medium transition">
                <i class="fas fa-sign-out-alt mr-2"></i>Logout
            </a>
        </div>
    </aside>

    <!-- Overlay (mobile) -->
    <div id="overlay" class="fixed inset-0 bg-black/50 z-30 hidden md:hidden" onclick="toggleSidebar()"></div>

    <!-- ===== Main area ===== -->
    <div class="flex-1 md:ml-64 flex flex-col min-h-screen">

        <!-- Topbar -->
        <header class="sticky top-0 z-20 h-16 bg-white border-b border-gray-200 flex items-center px-4 md:px-6 shadow-sm">
            <button onclick="toggleSidebar()" class="md:hidden mr-3 text-gray-500 hover:text-gray-800" aria-label="Menu">
                <i class="fas fa-bars text-lg"></i>
            </button>
            <h1 class="text-gray-800 font-semibold text-sm md:text-base truncate"><?= esc($title ?? 'Dashboard') ?></h1>

            <div class="ml-auto flex items-center gap-3">
                <span class="hidden sm:flex items-center text-xs text-gray-500 bg-gray-100 px-3 py-1.5 rounded-full">
                    <i class="fas fa-calendar-alt mr-1.5"></i><?= date('d M Y') ?>
                </span>
                <span class="md:hidden px-2.5 py-1 text-[10px] font-semibold uppercase rounded-full
                             <?= $role === 'admin' ? 'bg-indigo-100 text-indigo-700' : 'bg-emerald-100 text-emerald-700' ?>">
                    <?= esc($role) ?>
                </span>
                <a href="/logout" class="text-gray-500 hover:text-red-600 transition" title="Logout">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </header>

        <!-- Flash messages -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="px-4 md:px-6 pt-4">
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg text-sm flex items-center shadow-sm">
                    <i class="fas fa-check-circle mr-2"></i><?= esc(session()->getFlashdata('success')) ?>
                </div>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="px-4 md:px-6 pt-4">
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm flex items-center shadow-sm">
                    <i class="fas fa-exclamation-circle mr-2"></i><?= esc(session()->getFlashdata('error')) ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Content -->
        <main class="flex-1 p-4 md:p-6">
            <?= $this->renderSection('content') ?>
        </main>

        <!-- Footer ringkas (bukan hitam) -->
        <footer class="px-6 py-3 border-t border-gray-200 text-xs text-gray-400 flex justify-between">
            <span>&copy; <?= date('Y') ?> Sistem Manajemen User</span>
            <span>CodeIgniter 4 &middot; Tailwind CSS</span>
        </footer>
    </div>
</div>

<script>
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('-translate-x-full');
    document.getElementById('overlay').classList.toggle('hidden');
}
</script>

<?php else: ?>
<!-- ===== Halaman tanpa login (login) ===== -->
<div class="min-h-screen flex flex-col">
    <main class="flex-1 flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            <?= $this->renderSection('content') ?>
        </div>
    </main>
    <p class="text-center text-xs text-gray-400 pb-6">&copy; <?= date('Y') ?> Sistem Manajemen User</p>
</div>
<?php endif; ?>

<?= $this->renderSection('scripts') ?>
</body>
</html>
