<?php

require_once 'include/common.php';
require_once 'include/db.php';

// An array of routes and their corresponding controllers,
// ex: 'localhost/about' could return the page found at 'controllers/about.php'
$routes = [
    '/' => 'controllers/home.php',
    '/inventory' => 'controllers/inventory.php',
    '/intake' => 'controllers/intake.php',
    '/login' => 'controllers/login.php',
    '/register' => 'controllers/register.php',
];

$theme = 'mono';

$uri = get_uri();
$pageFound = false;

if (isset($routes[$uri])) {
    require $routes[$uri];
} else {
    http_response_code(404);
    require "views/404.view.php";
}
