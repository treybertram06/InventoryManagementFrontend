<?php

const STORAGE_SIZES = [16, 32, 64, 128, 256, 512, 1024, 2048];
const GRADES = ['A', 'B', 'C', 'D', 'Parts', 'Scrap'];

function blank($value): bool {
    return trim((string)($value ?? '')) === '';
}

function nullable_str($value): ?string {
    return blank($value) ? null : $value;
}

$user = Core\Common::require_admin($db);

function handle_device_edit(Core\Database $db, Models\Device $device, array $models): void {
    $values = $_POST;
    $errors = [];

    if (blank($values['product_type'] ?? null)) {
        $errors[] = "Product type is required.";
    } elseif (!$db->does_device_model_exist($values['product_type'])) {
        $errors[] = "Unknown product type. New product types can only be added through device intake.";
    }

    $storageGb = $values['storage_gb'] ?? null;
    if (blank($storageGb)) {
        $errors[] = "Storage (GB) is required.";
    } elseif (!in_array((int)$storageGb, STORAGE_SIZES, true) && (float)$storageGb !== (float)($device->storageGb ?? 0)) {
        $errors[] = "Please select a valid storage size.";
    }

    if (!in_array($values['grade'] ?? null, GRADES, true)) {
        $errors[] = "Please select a valid grade.";
    }

    if (!empty($errors)) {
        view('deviceEdit.view.php', ['device' => $device, 'models' => $models, 'errors' => $errors, 'values' => $values]);
        return;
    }

    $db->update_device([
        'serial_number' => $device->serialNumber,
        'product_type' => $values['product_type'],
        'model_number' => nullable_str($values['model_number'] ?? null),
        'color' => nullable_str($values['color'] ?? null),
        'region_code' => nullable_str($values['region_code'] ?? null),
        'storage_gb' => (float)$values['storage_gb'],
        'known_issues' => nullable_str($values['known_issues'] ?? null),
    ]);

    $db->update_inventory_item([
        'serial_number' => $device->serialNumber,
        'grade' => $values['grade'],
        'condition_notes' => nullable_str($values['condition_notes'] ?? null),
        'repairs_needed_done' => nullable_str($values['repairs_needed_done'] ?? null),
        'repair_cost' => (float)($values['repair_cost'] ?? 0),
        'b2b_floor_price' => blank($values['b2b_floor_price'] ?? null) ? null : (float)$values['b2b_floor_price'],
        'b2c_floor_price' => blank($values['b2c_floor_price'] ?? null) ? null : (float)$values['b2c_floor_price'],
    ]);

    header('Location: /device?serial=' . urlencode($device->serialNumber));
    exit;
}

$serial = trim((string)($_GET['serial'] ?? $_POST['serial_number'] ?? ''));
if (blank($serial)) {
    header('Location: /inventory');
    exit;
}

$device = $db->get_device_by_serial($serial);
if (!$device) {
    header('Location: /inventory');
    exit;
}

$models = $db->get_all_device_models();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    view('deviceEdit.view.php', ['device' => $device, 'models' => $models]);
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    handle_device_edit($db, $device, $models);
}