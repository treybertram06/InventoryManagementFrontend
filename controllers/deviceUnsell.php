<?php

$user = Core\Common::require_admin($db);

$saleId = (int)($_POST['sale_id'] ?? 0);
$serial = trim($_POST['serial_number'] ?? '');

if ($saleId <= 0 || $serial === '') {
    header('Location: /inventory');
    exit;
}

$db->reverse_sale($saleId, $user->ID);

header('Location: /device?serial=' . urlencode($serial));
exit;