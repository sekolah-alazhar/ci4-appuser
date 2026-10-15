<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <!-- Header -->
    <div class="px-6 py-4 bg-indigo-600 text-white flex justify-between items-center">
        <div>
            <h1 class="text-xl font-bold"><i class="fas fa-users mr-2"></i>Manajemen User</h1>
            <p class="text-indigo-200 text-sm mt-1">Kelola semua user sistem</p>
        </div>
        <a href="/admin/users/create" class="bg-white hover:bg-indigo-50 text-indigo-600 font-semibold px-4 py-2 rounded-lg transition">
            <i class="fas fa-plus mr-1"></i>Tambah User
        </a>
    </div>

    <!-- Stats -->
    <div class="px-6 py-4 bg-gray-50 border-b flex space-x-6">
        <div class="flex items-center">
            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                <i class="fas fa-users text-blue-600"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500">Total User</p>
                <p class="text-xl font-bold text-gray-800"><?= number_format($pagination['total']) ?></p>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="px-6 py-4 border-b bg-gray-50">
        <form method="GET" action="/admin/users" class="flex space-x-4">
            <div class="flex-1">
                <input type="text" name="search" value="<?= esc($pagination['search']) ?>"
                    placeholder="Cari nama, username, atau email..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <select name="role" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua Role</option>
                    <option value="admin" <?= $pagination['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="guest" <?= $pagination['role'] === 'guest' ? 'selected' : '' ?>>Guest</option>
                </select>
            </div>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg transition">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">#</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Nama</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Username</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Dibuat</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-3"></i>
                            <p>Belum ada data user</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($users as $index => $user): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm text-gray-500"><?= $pagination['total'] - (($pagination['current_page'] - 1) * 20) - $index ?></td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-800"><?= esc($user['name']) ?></div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600"><?= esc($user['username']) ?></td>
                            <td class="px-6 py-4 text-sm text-gray-600"><?= esc($user['email']) ?></td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold <?= $user['role'] === 'admin' ? 'bg-indigo-100 text-indigo-800' : 'bg-green-100 text-green-800' ?>">
                                    <?= strtoupper(esc($user['role'])) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                <?= date('d M Y', strtotime($user['created_at'])) ?>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="/admin/users/<?= esc($user['id']) ?>/edit" 
                                       class="text-blue-600 hover:text-blue-800 p-2 rounded hover:bg-blue-50 transition"
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="/admin/users/<?= esc($user['id']) ?>/delete" 
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?')"
                                          class="inline">
                                        <button type="submit" 
                                            class="text-red-600 hover:text-red-800 p-2 rounded hover:bg-red-50 transition"
                                            title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($pagination['total_pages'] > 1): ?>
        <div class="px-6 py-4 border-t bg-gray-50 flex items-center justify-between">
            <p class="text-sm text-gray-600">
                Menampilkan <?= count($users) ?> dari <?= $pagination['total'] ?> user
            </p>
            <div class="flex space-x-2">
                <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                    <a href="?page=<?= $i ?><?= $pagination['search'] ? '&search=' . urlencode($pagination['search']) : '' ?>"
                       class="px-3 py-1 rounded <?= $i === $pagination['current_page'] ? 'bg-indigo-600 text-white' : 'bg-white border hover:bg-gray-50' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
