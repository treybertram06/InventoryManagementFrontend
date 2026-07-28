<?php

Core\Common::require_admin($db);

$serial = trim($_POST['serial_number'] ?? '');

if ($serial === '') {
    header('Location: /inventory');
    exit;
}

$db->reverse_sale($serial);

header('Location: /device?serial=' . urlencode($serial));
exit;