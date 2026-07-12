<?php

require_once base_path('core/router.php');

$router = new Core\Router();

$router->get('/', 'controllers/home.php');
$router->get('/inventory', 'controllers/inventory.php');
$router->get('/intake', 'controllers/intake.php');

$router->get('/login', 'controllers/login.php');
$router->post('/login', 'controllers/login.php');

$router->get('/register', 'controllers/register.php');
$router->post('/register', 'controllers/register.php');

$router->post('/logout', 'controllers/logout.php');


return $router;