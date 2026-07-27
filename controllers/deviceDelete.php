<?php

$user = Core\Common::current_user($db);
if (!$user) {
    header('Location: /login');
    exit;
}

if ($user->role !== Models\UserRole::Admin) {
    header('Location: /inventory');
    exit;
}

$serial = trim((string)($_POST['serial_number'] ?? ''));
if ($serial === '') {
    header('Location: /inventory');
    exit;
}

$device = $db->get_device_by_serial($serial);
if (!$device) {
    header('Location: /inventory');
    exit;
}

$db->delete_device($serial);
header('Location: /inventory');
exit;