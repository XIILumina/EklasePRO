<?php

use App\Controllers\Auth\SessionController;
use App\Controllers\Auth\UserController;
use App\Controllers\PageController;

global $router;

$router->get('/', [PageController::class, 'index']);

$router->get('/login', [SessionController::class, 'create']);
$router->post('/login', [SessionController::class, 'store']);

$router->get('/register', [UserController::class, 'create']);
$router->post('/register', [UserController::class, 'store']);

$router->get('/logout', [SessionController::class, 'destroy']);

$router->get('/diary', [PageController::class, 'index']);
$router->get('/diary/create', [PageController::class, 'create']);
$router->post('/diary/store', [PageController::class, 'store']);
$router->get('/diary/{id}/edit', [PageController::class, 'edit']);
$router->post('/diary/{id}/update', [PageController::class, 'update']);

$router->get('/profile', [UserController::class, 'show']);
$router->post('/profile/image', [UserController::class, 'image']);
$router->post('/profile/update', [UserController::class, 'update']);
$router->post('/profile/delete-image', [UserController::class, 'deleteImage']); // ✅ Новый маршрут
$router->post('/profile/delete', [UserController::class, 'delete']);
