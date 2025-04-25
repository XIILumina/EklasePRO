<!doctype html>
<html>
<head>
    <meta charset="UTF-8" />
    <meta lang="en" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="icon" href="images/favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" href="css/styles.css" type="text/css" />
    <title><?= $title ?? 'Storage' ?></title>
</head>
<body class="flex flex-col min-h-screen">
<nav class="bg-white shadow">
    <?php component('navbar'); ?>
</nav>
<main></main>