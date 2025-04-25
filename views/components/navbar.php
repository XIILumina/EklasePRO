<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between h-16">
        <div class="flex">
            <ul class="flex space-x-8 pt-3">
                <li>
                    <a href="/" class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 hover:border-b-2 hover:border-blue-600 transition-all duration-150">
                        Home
                    </a>
                </li>
                <li>
                    <a href="/products" class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 hover:border-b-2 hover:border-blue-600 transition-all duration-150">
                        Products
                    </a>
                </li>
                <li>
                    <a href="/shelves" class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 hover:border-b-2 hover:border-blue-600 transition-all duration-150">
                        Shelves
                    </a>
                </li>
                <li>
                    <a href="/orders" class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 hover:border-b-2 hover:border-blue-600 transition-all duration-150">
                        Orders
                    </a>
                </li>
                <li>
                    <a href="/actions" class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 hover:border-b-2 hover:border-blue-600 transition-all duration-150">
                        History
                    </a>
                </li>
                <li>
                    <a href="/admin" class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 hover:border-b-2 hover:border-blue-600 transition-all duration-150">
                        Admin
                    </a>
                </li>
            </ul>
        </div>
        <div class="flex">
            <?php if (!auth()): ?>
            <ul class="flex space-x-4 pt-3">
                <li>
                    <a href="/login" class="inline-flex bg-gradient-to-br from-blue-400 to-purple-500 rounded-l-3xl px-8 items-center py-2 text-sm font-bold text-gray-100 hover:to-blue-500 hover:from-purple-600 transition hover:scale-105 duration-400">
                        Login
                    </a>
                </li>
                <li>
                    <a href="/register" class="inline-flex items-center px-6 py-2 text-sm font-bold rounded-r-3xl text-gray-700 hover:text-blue-600 transition-all duration-400 border hover:scale-105 border-dashed">
                        Register
                    </a>
                </li>
            </ul>
            <?php else: ?>
            <img src="<?= $_SESSION['user']['image'] ?>" alt="Profile" class="w-10 h-10 rounded-full mt-2.5 mr-3 border border-white">
            <ul class="flex space-x-4 pt-3">
                <li>
                    <a href="/logout" class="inline-flex bg-gradient-to-br from-blue-400 to-purple-500 rounded-l-3xl px-8 items-center py-2 text-sm font-bold text-gray-100 hover:to-blue-500 hover:from-purple-600 transition hover:scale-105 duration-400">
                        Logout
                    </a>
                </li>
                <li>
                    <a href="/profile" class="inline-flex items-center px-6 py-2 text-sm font-bold rounded-r-3xl text-gray-700 hover:text-blue-600 transition-all duration-400 border hover:scale-105 border-dashed">
                        Profile
                    </a>
                </li>
            </ul>
            <?php endif ?>
        </div>
    </div>
</div>