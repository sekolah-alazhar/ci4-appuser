<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 bg-indigo-600 text-white">
            <h1 class="text-xl font-bold">
                <i class="fas fa-<?= empty($user) ? 'plus' : 'edit' ?> mr-2"></i>
                <?= empty($user) ? 'Tambah' : 'Edit' ?> User
            </h1>
        </div>

        <form method="POST" class="p-6" enctype="multipart/form-data">
            <?php if (!empty($errors)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <ul class="list-disc list-inside">
                        <?php foreach ($errors as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-2 gap-6">
                <div class="col-span-2">
                    <label class="block text-gray-700 font-semibold mb-2">
                        <i class="fas fa-user mr-1"></i>Nama Lengkap
                    </label>
                    <input type="text" name="name" required value="<?= esc(old('name', $user['name'] ?? '')) ?>"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-gray-700 font-semibold mb-2">
                        <i class="fas fa-at mr-1"></i>Username
                    </label>
                    <input type="text" name="username" required value="<?= esc(old('username', $user['username'] ?? '')) ?>"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-gray-700 font-semibold mb-2">
                        <i class="fas fa-envelope mr-1"></i>Email
                    </label>
                    <input type="email" name="email" required value="<?= esc(old('email', $user['email'] ?? '')) ?>"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-gray-700 font-semibold mb-2">
                        <i class="fas fa-lock mr-1"></i>Password
                    </label>
                    <input type="password" name="password" 
                        <?= empty($user) ? 'required' : '' ?>
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        placeholder="<?= empty($user) ? 'Masukkan password' : 'Kosongkan jika tidak diubah' ?>">
                    <?php if (!empty($user)): ?>
                        <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin mengubah password</p>
                    <?php endif; ?>
                </div>

                <div>
                    <label class="block text-gray-700 font-semibold mb-2">
                        <i class="fas fa-user-tag mr-1"></i>Role
                    </label>
                    <select name="role" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="admin" <?= old('role', $user['role'] ?? 'guest') === 'admin' ? 'selected' : '' ?>>Admin</option>
                        <option value="guest" <?= old('role', $user['role'] ?? 'guest') === 'guest' ? 'selected' : '' ?>>Guest</option>
                    </select>
                </div>
            </div>

            <div class="mt-8 flex space-x-4">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-2 rounded-lg transition">
                    <i class="fas fa-save mr-1"></i>Simpan
                </button>
                <a href="/admin/users" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold px-6 py-2 rounded-lg transition">
                    <i class="fas fa-arrow-left mr-1"></i>Batal
                </a>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
