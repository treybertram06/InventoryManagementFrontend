<?php

if (!Core\Common::current_user($db)) {
    header('Location: /login');
    exit;
}

/*
|--------------------------------------------------------------------------
| POST - Save Changes
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $db->update_device([
        'serial_number' => $_POST['serial_number'],
        'product_type' => $_POST['product_type'],
        'model_number' => $_POST['model_number'],
        'color' => $_POST['color'],
        'region_code' => $_POST['region_code'],
        'storage_gb' => $_POST['storage_gb'],
        'known_issues' => $_POST['known_issues'],
    ]);

    header('Location: /device?serial=' . urlencode($_POST['serial_number']));
    exit;
}

/*
|--------------------------------------------------------------------------
| GET - Show Edit Form
|--------------------------------------------------------------------------
*/

$serial = $_GET['serial'] ?? '';

if (empty($serial)) {
    header('Location: /inventory');
    exit;
}

$device = $db->get_device_by_serial($serial);

if (!$device) {
    header('Location: /inventory');
    exit;
}

view('deviceEdit.view.php', [
    'device' => $device
]);