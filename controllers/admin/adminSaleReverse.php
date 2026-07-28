<?php

$user = Core\Common::require_admin($db);

$saleId = (int)($_POST['sale_id'] ?? 0);
if ($saleId === 0) {
    header('Location: /sales-history');
    exit;
}

$db->reverse_sale($saleId, $user->ID);
header('Location: /sales-history');
exit;
