<?php

$user = Core\Common::require_admin($db);

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