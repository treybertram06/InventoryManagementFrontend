<?php

require_once base_path('core/router.php');

$router = new Core\Router();

$router->get('/', 'controllers/home.php');
$router->get('/inventory', 'controllers/inventory.php');
$router->get('/intake', 'controllers/intake.php');
$router->post('/intake', 'controllers/intake.php');

$router->get('/device-test', 'controllers/deviceTest.php');
$router->post('/device-test', 'controllers/deviceTest.php');

$router->get('/device', 'controllers/deviceView.php');

$router->get('/device-edit', 'controllers/deviceEdit.php');
$router->post('/device-edit', 'controllers/deviceEdit.php');

$router->get('/device-sale', 'controllers/deviceSale.php');
$router->post('/device-sale', 'controllers/deviceSale.php');

$router->post('/device-unsell', 'controllers/deviceUnsell.php');

$router->post('/device-delete', 'controllers/deviceDelete.php');

$router->get('/admin', 'controllers/admin/admin.php');
$router->post('/admin-user-create', 'controllers/admin/adminUserCreate.php');
$router->post('/admin-user-role', 'controllers/admin/adminUserRole.php');
$router->post('/admin-user-delete', 'controllers/admin/adminUserDelete.php');
$router->post('/admin-sale-reverse', 'controllers/admin/adminSaleReverse.php');

$router->get('/login', 'controllers/login.php');
$router->post('/login', 'controllers/login.php');

$router->get('/register', 'controllers/register.php');
$router->post('/register', 'controllers/register.php');

$router->post('/logout', 'controllers/logout.php');


return $router;