<?php

Core\Common::require_admin($db);

$serial = trim((string)($_POST['serial_number'] ?? ''));
if ($serial === '') {
    header('Location: /admin');
    exit;
}

$device = $db->get_device_by_serial($serial);
if (!$device || $device->status !== 'sold') {
    header('Location: /admin');
    exit;
}

$db->reverse_sale($serial);
header('Location: /admin');
exit;
