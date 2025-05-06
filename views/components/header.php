<!doctype html>
<html>
<head>
    <meta charset="UTF-8" />
    <meta lang="en" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    
    <!-- Tailwind CSS via CDN -->
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>

    <!-- Cropper.js CSS -->
    <link href="https://unpkg.com/cropperjs@1.5.13/dist/cropper.min.css" rel="stylesheet"/>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/styles.css" type="text/css" />

    <!-- Favicon -->
    <link rel="icon" href="images/favicon.ico" type="image/x-icon" />

    <!-- Page Title -->
    <title><?= $title ?? 'Storage' ?></title>
</head>
<body class="flex flex-col min-h-screen">

<!-- Navbar -->
<nav class="bg-white shadow">
    <?php component('navbar'); ?>
</nav>

<main>
