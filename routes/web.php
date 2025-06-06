<?php
use App\Controllers\Auth\SessionController;
use App\Controllers\Auth\UserController;
use App\Controllers\DiaryController;
use App\Controllers\PageController;
use App\Controllers\GradeController;
use App\Controllers\ClassController;
use App\Controllers\MailController;
use App\Controllers\DetentionController;
use App\Controllers\LessonController;
use App\Controllers\TeacherController;

global $router;

$router->get('/', [PageController::class, 'index']);
$router->get('/dashboard', [PageController::class, 'index']);

$router->get('/login', [SessionController::class, 'create']);
$router->post('/login', [SessionController::class, 'store']);
$router->get('/register', [UserController::class, 'create']);
$router->post('/register', [UserController::class, 'store']);
$router->get('/logout', [SessionController::class, 'destroy']);

$router->get('/profile', [UserController::class, 'show']);
$router->post('/profile/image', [UserController::class, 'image']);
$router->post('/profile/update', [UserController::class, 'update']);
$router->post('/profile/delete-image', [UserController::class, 'deleteImage']);
$router->post('/profile/delete', [UserController::class, 'delete']);

$router->get('/diary', [DiaryController::class, 'index']);
$router->get('/diary/{id}', [DiaryController::class, 'show']);
$router->get('/diary/create', [DiaryController::class, 'create']);
$router->post('/diary/store', [DiaryController::class, 'store']);
$router->get('/diary/{id}/edit', [DiaryController::class, 'edit']);
$router->post('/diary/{id}/update', [DiaryController::class, 'update']);
$router->get('/daily-lessons', [DiaryController::class, 'dailyLessons']);

$router->get('/grades', [GradeController::class, 'index']);
$router->get('/grades/create', [GradeController::class, 'create']);
$router->post('/grades/store', [GradeController::class, 'store']);
$router->get('/grades/edit', [GradeController::class, 'edit']); // Updated for matrix edit
$router->post('/grades/{id}/update', [GradeController::class, 'update']);
$router->get('/grades/{id}/delete', [GradeController::class, 'delete']);
$router->post('/grades/bulk-update', [GradeController::class, 'bulkUpdate']);
$router->get('/detentions/create', [GradeController::class, 'createDetention']);
$router->post('/detentions/store', [GradeController::class, 'storeDetention']);

$router->get('/classes', [App\Controllers\ClassController::class, 'index']);
$router->get('/classes/create', [App\Controllers\ClassController::class, 'create']);
$router->post('/classes/store', [App\Controllers\ClassController::class, 'store']);
$router->get('/classes/{id}/edit', [App\Controllers\ClassController::class, 'edit']);
$router->post('/classes/{id}/update', [App\Controllers\ClassController::class, 'update']);
$router->get('/classes/{id}/delete', [App\Controllers\ClassController::class, 'delete']);
$router->get('/classes/{id}/add-students', [App\Controllers\ClassController::class, 'addStudents']);
$router->post('/classes/{id}/store-students', [App\Controllers\ClassController::class, 'storeStudents']);
$router->post('/classes/{id}/remove-student', [App\Controllers\ClassController::class, 'removeStudent']);

$router->get('/lessons', [LessonController::class, 'index']);
$router->get('/lessons/create', [LessonController::class, 'create']);
$router->post('/lessons/store', [LessonController::class, 'store']);
$router->get('/lessons/{id}/edit', [LessonController::class, 'edit']);
$router->post('/lessons/{id}/update', [LessonController::class, 'update']);
$router->post('/lessons/{id}/delete', [LessonController::class, 'delete']);

$router->get('/mail', [MailController::class, 'index']);
$router->get('/mail/create', [MailController::class, 'create']);
$router->post('/mail/store', [MailController::class, 'store']);
$router->get('/mail/{id}', [MailController::class, 'show']);

$router->get('/detentions', [DetentionController::class, 'index']);
$router->get('/detentions/create', [DetentionController::class, 'create']);
$router->post('/detentions/store', [DetentionController::class, 'store']);


