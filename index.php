<?php
session_start();

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
        $segments = explode('\\', $class); // Transforms '\' separated namespaces into an array of segments: Core\Validator -> ['Core', 'Validator']
        $segments[count($segments) - 1] .= '.php'; // Append .php onto the last segment

        $path = rtrim(BASE_PATH, '/'); // Remove trailing slash
        foreach ($segments as $segment) {
            $match = $segment; // Set default for fallback
            foreach (scandir($path) as $entry) {
                if (strcasecmp($entry, $segment) === 0) { // Case-insensitive string comparison of each entry in the directory
                    $match = $entry; // If the file/folder is found, set it as the match
                    break;
                }
            }
            $path .= DIRECTORY_SEPARATOR . $match; // Build the path by appending matching segments
        }

        require $path;
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
