<?php component('header', ['title' => 'Page Not Found']); ?>
<div class="container mx-auto w-full text-center pt-32">
    <h1 class="text-8xl font-extrabold pt-6">404</h1>
    <p class="text-2xl pt-4">The page you are looking for was not found!</p>
    <p class="text-xl pt-4 font-light italic">(<?= /** @var string $message */ $message ?>)</p>
</div>
<?php component('footer'); ?>