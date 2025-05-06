<?php component('header'); ?>
<div class="w-300px h-200px flex flex-col items-center justify-center rounded-lg shadow-md mx-auto mt-20">
    <form action="/register" method="POST" class="flex flex-col items-center justify-center h-screen space-y-4 ">
        <input type="name" name="name" placeholder="name" required>
        <input type="email" name="email" placeholder="email" required>
        <input type="password" name="password" placeholder="password" required>
        <button >Login</button>
    </form>
</div>
<?php component('footer'); ?>