<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <!-- Header -->
    <div class="px-6 py-4 bg-green-600 text-white">
        <h1 class="text-xl font-bold"><i class="fas fa-users mr-2"></i>Daftar User</h1>
        <p class="text-green-200 text-sm mt-1">Mode tamu — hanya melihat daftar user</p>
    </div>

    <!-- Info -->
    <div class="px-6 py-3 bg-green-50 border-b text-sm text-green-800">
        <i class="fas fa-info-circle mr-1"></i> Anda login sebagai <strong>tamu (guest)</strong>. Tidak dapat menambah / mengubah / menghapus user.
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
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-3"></i>
                            <p>Belum ada data user</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($users as $index => $user): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm text-gray-500"><?= $index + 1 ?></td>
                            <td class="px-6 py-4 font-medium text-gray-800"><?= esc($user['name']) ?></td>
                            <td class="px-6 py-4 text-sm text-gray-600"><?= esc($user['username']) ?></td>
                            <td class="px-6 py-4 text-sm text-gray-600"><?= esc($user['email']) ?></td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold <?= $user['role'] === 'admin' ? 'bg-indigo-100 text-indigo-800' : 'bg-green-100 text-green-800' ?>">
                                    <?= strtoupper(esc($user['role'])) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                <?= !empty($user['created_at']) ? date('d M Y', strtotime($user['created_at'])) : '-' ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="px-6 py-4 border-t bg-gray-50 text-sm text-gray-600">
        Total: <strong><?= count($users) ?></strong> user
    </div>
</div>
<?= $this->endSection() ?>
