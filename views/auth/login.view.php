<?php component('header'); ?>

<div class="flex items-center justify-center min-h-screen bg-black text-red-500">
    <div class="w-full max-w-md p-8 bg-gray-900 rounded-2xl shadow-2xl ring-1 ring-red-600">
        <h2 class="text-2xl font-bold text-center mb-6 text-red-400">Welcome Back</h2>
        <form action="/login" method="POST" class="space-y-5">
            <div>
                <label class="block text-sm font-medium mb-1" for="email">Email</label>
                <input type="email" name="email" id="email" required
                    class="w-full px-4 py-2 bg-black border border-red-700 rounded-lg text-white focus:ring-red-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1" for="password">Password</label>
                <input type="password" name="password" id="password" required
                    class="w-full px-4 py-2 bg-black border border-red-700 rounded-lg text-white focus:ring-red-500 focus:outline-none">
            </div>
            <button type="submit"
                class="w-full py-2 px-4 bg-red-600 hover:bg-red-500 text-white font-semibold rounded-lg transition duration-150">
                Login
            </button>
        </form>
        <p class="text-center text-sm mt-6 text-gray-400">Don't have an account? <a href="/register" class="text-red-400 hover:underline">Sign up</a></p>
    </div>
</div>

<?php component('footer'); ?>
