<?php

use App\Controllers\Auth\SessionController;
use App\Controllers\Auth\UserController;
use App\Controllers\DiaryController;
use App\Controllers\PageController;
use App\Controllers\GradeController;


global $router;

$router->get('/', [PageController::class, 'index']);

$router->get('/login', [SessionController::class, 'create']);
$router->post('/login', [SessionController::class, 'store']);

$router->get('/register', [UserController::class, 'create']);
$router->post('/register', [UserController::class, 'store']);

$router->get('/logout', [SessionController::class, 'destroy']);

$router->get('/diary', [DiaryController::class, 'index']);
$router->get('/diary/create', [DiaryController::class, 'create']);
$router->post('/diary/store', [DiaryController::class, 'store']);
$router->get('/diary/{id}/edit', [DiaryController::class, 'edit']);
$router->post('/diary/{id}/update', [DiaryController::class, 'update']);

$router->get('/profile', [UserController::class, 'show']);
$router->post('/profile/image', [UserController::class, 'image']);
$router->post('/profile/update', [UserController::class, 'update']);
$router->post('/profile/delete-image', [UserController::class, 'deleteImage']); // ✅ Новый маршрут
$router->post('/profile/delete', [UserController::class, 'delete']);


$router->get('/grades', [GradeController::class, 'index']);
$router->get('/grades/create', [GradeController::class, 'create']);
$router->post('/grades/store', [GradeController::class, 'store']);
$router->get('/grades/{id}/edit', [GradeController::class, 'edit']);
$router->post('/grades/{id}/update', [GradeController::class, 'update']);
$router->get('/grades/{id}/delete', [GradeController::class, 'delete']);

$router->get('/view-grades', [GradeController::class, 'viewGrades']);
$router->get('/filter-students', [GradeController::class, 'filterStudents']);