<?php component('header'); ?>

<div class="flex items-center justify-center min-h-screen bg-black text-red-500">
    <div class="w-full max-w-md p-8 bg-gray-900 rounded-md shadow-2xl ring-1 ring-red-600">
        <h2 class="text-2xl font-bold text-center mb-6 text-red-400">Create Your Account</h2>
        <form action="/register" method="POST" class="space-y-5">
            <div>
                <label class="block text-sm font-medium mb-1" for="first_name">First Name</label>
                <input type="text" name="first_name" id="first_name" required
                    class="w-full px-4 py-2 bg-black border border-red-700 rounded-lg text-white focus:ring-red-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1" for="Last_name">Last Name</label>
                <input type="text" name="last_name" id="last_name" required
                    class="w-full px-4 py-2 bg-black border border-red-700 rounded-lg text-white focus:ring-red-500 focus:outline-none">
            </div>
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
                Register
            </button>
        </form>
        <p class="text-center text-sm mt-6 text-gray-400">Already have an account? <a href="/login" class="text-red-400 hover:underline">Login</a></p>
    </div>
</div>

<?php component('footer'); ?>
