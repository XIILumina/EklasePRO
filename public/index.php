<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
const BASE_PATH = __DIR__.'/../';
session_start();
require BASE_PATH . 'vendor/autoload.php';
require BASE_PATH . 'core/functions.php';
$dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH, '.env');

$dotenv->load();

use Core\Session;
use Database\Database;
$config = require BASE_PATH . 'config/database.php';
Database::connect($config);

$router = new \Core\Router;
require BASE_PATH . 'routes/web.php';

$uri = parse_url($_SERVER['REQUEST_URI'])['path'];
$method = $_POST['_method'] ?? $_SERVER['REQUEST_METHOD'];

try {
    $router->route($uri, $method);
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
    if (function_exists('redirect')) {
        return redirect($router->previousUrl());
    }
}

Session::unflash();