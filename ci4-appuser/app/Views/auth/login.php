<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
    <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-indigo-100 rounded-full mb-4">
                <i class="fas fa-user-lock text-2xl text-indigo-600"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-800">Login</h1>
            <p class="text-gray-500 mt-2">Masuk ke sistem manajemen user</p>
        </div>

        <?php if ($error ?? null): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <i class="fas fa-exclamation-circle mr-2"></i><?= esc($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/login">
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-semibold mb-2" for="username">
                    <i class="fas fa-user mr-1"></i>Username
                </label>
                <input type="text" id="username" name="username" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    placeholder="Masukkan username" value="<?= old('username') ?>">
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-semibold mb-2" for="password">
                    <i class="fas fa-lock mr-1"></i>Password
                </label>
                <input type="password" id="password" name="password" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    placeholder="Masukkan password">
            </div>

            <button type="submit"
                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 rounded-lg transition duration-200">
                <i class="fas fa-sign-in-alt mr-2"></i>Login
            </button>
        </form>

        <div class="mt-6 text-center text-sm text-gray-500">
            <p>Default akun:</p>
            <p class="mt-1">Admin: <code class="bg-gray-100 px-2 py-1 rounded">admin</code> / <code class="bg-gray-100 px-2 py-1 rounded">admin123</code></p>
        </div>
    </div>
<?= $this->endSection() ?>
