<?php component('header'); ?>

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- Sidebar Profile -->
        <div class="bg-gray-900 text-white rounded-2xl shadow-lg p-6 flex flex-col items-center text-center">
            <div class="relative">
                <?php if (isset($_SESSION['user']['image'])): ?>
                    <img src="<?= htmlspecialchars($_SESSION['user']['image'], ENT_QUOTES, 'UTF-8') ?>" alt="Profile"
                        class="w-32 h-32 rounded-full border-4 border-blue-400 object-cover mb-4">
                    <form action="/profile/delete-image" method="POST" class="absolute top-0 right-0">
                        <button type="submit"
                            class="bg-red-600 text-white rounded-full w-8 h-8 flex items-center justify-center hover:bg-red-700 transition"
                            title="Delete Image">&times;</button>
                    </form>
                <?php else: ?>
                    <div class="w-32 h-32 rounded-full bg-gray-700 flex items-center justify-center mb-4">
                        <span class="text-gray-300 text-xl">No Image</span>
                    </div>
                <?php endif; ?>
            </div>
            <h2 class="text-2xl font-bold"><?= htmlspecialchars($_SESSION['user']['username'], ENT_QUOTES, 'UTF-8') ?></h2>
            <p class="text-gray-300 mb-1"><?= htmlspecialchars($_SESSION['user']['email'], ENT_QUOTES, 'UTF-8') ?></p>
            <a href="/logout"
               class="mt-6 inline-block px-5 py-2 bg-gradient-to-r from-pink-500 to-purple-500 hover:to-pink-600 text-white font-bold rounded-xl shadow-lg transition">
                Logout
            </a>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Upload Image -->
            <div class="bg-white rounded-2xl shadow p-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Change Profile Picture</h3>
                <form action="/profile/image" method="POST" enctype="multipart/form-data" class="space-y-4">
                    <input type="file" name="image" accept="image/*"
                           class="w-full text-sm text-gray-700 file:py-2 file:px-4 file:rounded-full file:border-0 file:font-semibold file:bg-blue-100 file:text-blue-700 hover:file:bg-blue-200">
                    <button type="submit"
                            class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 rounded-xl font-semibold transition">
                        Upload
                    </button>
                </form>
            </div>

            <!-- Edit Info -->
            <div class="bg-white rounded-2xl shadow p-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Edit Profile Info</h3>
                <form action="/profile/update" method="POST" class="space-y-4">
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-600">Username</label>
                        <input type="text" name="username" id="username"
                               value="<?= htmlspecialchars($_SESSION['user']['username'], ENT_QUOTES, 'UTF-8') ?>"
                               class="w-full mt-1 px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-600">Email</label>
                        <input type="email" name="email" id="email"
                               value="<?= htmlspecialchars($_SESSION['user']['email'], ENT_QUOTES, 'UTF-8') ?>"
                               class="w-full mt-1 px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>
                    <button type="submit"
                            class="w-full bg-green-500 hover:bg-green-600 text-white py-2 rounded-xl font-semibold transition">
                        Save Changes
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>

<?php component('footer'); ?>
