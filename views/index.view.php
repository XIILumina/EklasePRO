<?php component('header'); ?>

<div class="min-h-screen bg-gradient-to-br from-gray-900 to-gray-800 flex flex-col items-center justify-center px-6 py-12 relative overflow-hidden">
    <!-- Background image with overlay filter -->
    <div class="absolute inset-0 bg-cover bg-center bg-opacity-20" style="background-image: url('https://www.example.com/your-image.jpg');"></div>

    <h1 class="text-6xl sm:text-7xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-teal-500 via-teal-700 to-green-900 mb-4 animate__animated animate__fadeInDown">
        Eklase
    </h1>

    <p class="text-lg sm:text-2xl text-gray-600 max-w-2xl mb-8 animate__animated animate__fadeInUp animate__delay-1s">
        Welcome to Eklase — your modern platform for online learning, where education meets technology.
    </p>

    <!-- Interactive Button with Hover Animation -->
    <a href="/register" class="inline-block bg-teal-600 hover:bg-teal-700 text-white font-semibold py-3 px-8 rounded-xl shadow-md transition duration-300 ease-in-out transform hover:scale-105 animate__animated animate__fadeInUp animate__delay-2s">
        Get Started
    </a>

<?php component('footer'); ?>

<!-- Scroll animation library (animate.css) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

<!-- JavaScript to trigger the features section animation when it's in view -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const featuresSection = document.getElementById('features');

    window.addEventListener('scroll', function() {
        const rect = featuresSection.getBoundingClientRect();
        if (rect.top <= window.innerHeight && rect.bottom >= 0) {
            featuresSection.classList.add('animate__fadeInUp');
        }
    });
});
</script>
