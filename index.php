<?php

const BASE_PATH = __DIR__ . '/';
function base_path($path = '') {
    return BASE_PATH . $path;
}

function view(string $path, $attributes=[]){
    extract($attributes);
    require base_path("views/{$path}");
}

spl_autoload_register(
    function($class){
        $class = str_replace('\\', DIRECTORY_SEPARATOR, $class); // Core/Database
        require base_path("{$class}.php");
    }
);

const BASE_URL = 'http://localhost/';
define('CONFIG', file_exists(BASE_PATH . 'core/config.php')
    ? require BASE_PATH . 'core/config.php' : require BASE_PATH . 'core/config.example.php');
require_once BASE_PATH . 'core/common.php';
require_once BASE_PATH . 'core/db.php';

// DB object is created here so it's globally accessible
try {
    $db = new Core\Database();
} catch (\RuntimeException $e) {
    http_response_code(503);
    echo "Service unavailable: could not connect to the database.";
    exit;
}

$router = require base_path('routes.php');
$router->dispatch(Core\Common::get_uri(), $_SERVER['REQUEST_METHOD'], ['db' => $db]);
