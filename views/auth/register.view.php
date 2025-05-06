<?php component('header'); ?>

<div class="flex items-center justify-center min-h-screen bg-gradient-to-r from-green-100 via-teal-100 to-cyan-100">
    <div class="w-full max-w-md p-8 bg-white rounded-2xl shadow-xl ring-1 ring-gray-200">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Create Your Account ✨</h2>
        <form action="/register" method="POST" class="space-y-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="username">Username</label>
                <input
                    type="text"
                    name="username"
                    id="username"
                    placeholder="Your username"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent"
                >
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="email">Email</label>
                <input
                    type="email"
                    name="email"
                    id="email"
                    placeholder="you@example.com"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent"
                >
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="password">Password</label>
                <input
                    type="password"
                    name="password"
                    id="password"
                    placeholder="••••••••"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent"
                >
            </div>
            <button
                type="submit"
                class="w-full py-2 px-4 bg-teal-500 hover:bg-teal-600 text-white font-semibold rounded-lg shadow-md transition duration-200"
            >
                Register
            </button>
        </form>
        <p class="text-center text-sm text-gray-500 mt-6">Already have an account? <a href="/login" class="text-teal-500 hover:underline">Login</a></p>
    </div>
</div>

<?php component('footer'); ?>