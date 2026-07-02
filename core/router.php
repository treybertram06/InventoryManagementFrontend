<?php

$routes = [
    '/' => 'controllers/home.php',
    '/inventory' => 'controllers/inventory.php',
    '/intake' => 'controllers/intake.php',
    '/login' => 'controllers/login.php',
    '/register' => 'controllers/register.php',
];

$uri = get_uri();
$pageFound = false;

if (isset($routes[$uri])) {
    require $routes[$uri];
} else {
    http_response_code(404);
    require "views/404.view.php";
}