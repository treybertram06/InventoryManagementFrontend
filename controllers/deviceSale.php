<?php

function blank($value): bool {
    return trim((string)($value ?? '')) === '';
}

function handle_device_sale(Core\Database $db, Models\Device $device): void {

    $values = $_POST;
    $errors = [];

    if (blank($values['sale_price'] ?? null)) {
        $errors[] = "Sale price is required.";
    } elseif ((float)$values['sale_price'] <= 0) {
        $errors[] = "Sale price must be greater than zero.";
    }

    if (blank($values['sale_channel'] ?? null)) {
        $errors[] = "Please select a sale channel.";
    }

    if (!empty($errors)) {

        view('deviceSale.view.php', [
            'device' => $device,
            'errors' => $errors,
            'values' => $values
        ]);

        return;
    }

    $db->create_sale([
    'serial_number' => $device->serialNumber,
    'technician_id' => $user->id,
    'sale_price' => (float)$values['sale_price'],
    'sale_channel' => $values['sale_channel'],
    'buyer_info' => trim($values['buyer_info'] ?? ''),
    'sold_at' => date('Y-m-d H:i:s'),
    'notes' => null
]);

    header('Location: /device?serial=' . urlencode($device->serialNumber));
    exit;
}

$user = Core\Common::require_admin($db);

$serial = trim((string)($_GET['serial'] ?? $_POST['serial_number'] ?? ''));

if ($serial === '') {
    header('Location: /inventory');
    exit;
}

$device = $db->get_device_by_serial($serial);

if ($device->status === 'sold') {
    header('Location: /device?serial=' . urlencode($device->serialNumber));
    exit;
}

if (!$device) {
    header('Location: /inventory');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    view('deviceSale.view.php', [
        'device' => $device
    ]);

} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    handle_device_sale($db, $device);

}