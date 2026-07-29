<?php

Core\Common::require_login($db);

const GRADES = ['A', 'B', 'C', 'D', 'Parts', 'Scrap'];

function blank($value): bool {
    return trim((string)($value ?? '')) === '';
}

function nullable_str($value): ?string {
    return blank($value) ? null : $value;
}

function tri_state_to_bool($value): ?int {
    if (blank($value)) {
        return null;
    }
    return $value === '1' ? 1 : 0;
}

function handle_intake(Core\Database $db): void {
    $values = $_POST;
    $errors = [];

    if (blank($values['serial_number'] ?? null)) {
        $errors[] = "Serial number is required.";
    } elseif ($db->does_device_exist($values['serial_number'])) {
        $errors[] = "A device with this serial number has already been checked in.";
    }

    $isNewProductType = false;
    if (blank($values['product_type'] ?? null)) {
        $errors[] = "Product type is required.";
    } else {
        $isNewProductType = !$db->does_device_model_exist($values['product_type']);
        if ($isNewProductType && blank($values['friendly_name'] ?? null)) {
            $errors[] = "Product Name is required the first time this Product Type is checked in.";
        }
    }

    if (blank($values['batch_number'] ?? null)) {
        $errors[] = "Batch number is required.";
    }

    if (blank($values['storage_gb'] ?? null)) {
        $errors[] = "Storage (GB) is required.";
    }

    if (blank($values['grade'] ?? null)) {
        $errors[] = "Grade is required.";
    } elseif (!in_array($values['grade'], GRADES, true)) {
        $errors[] = "Please select a valid grade.";
    }

    if (!empty($errors)) {
        view('intake.view.php', ['errors' => $errors, 'values' => $values]);
        return;
    }

    if ($isNewProductType) {
        $db->create_device_model($values['product_type'], $values['friendly_name']);
    }

    $batchId = $db->get_batch_id_by_number($values['batch_number']) ?? $db->create_batch($values['batch_number']);

    $created = $db->create_device([
        'serial_number' => $values['serial_number'],
        'product_type' => $values['product_type'],
        'model_number' => nullable_str($values['model_number'] ?? null),
        'color' => nullable_str($values['color'] ?? null),
        'region_code' => nullable_str($values['region_code'] ?? null),
        'storage_gb' => (float)$values['storage_gb'],

        'imei' => nullable_str($values['imei'] ?? null),
        'imei2' => nullable_str($values['imei2'] ?? null),
        'udid' => nullable_str($values['udid'] ?? null),
        'meid' => nullable_str($values['meid'] ?? null),
        'iccid' => nullable_str($values['iccid'] ?? null),
        'wifi_mac' => nullable_str($values['wifi_mac'] ?? null),
        'bluetooth_mac' => nullable_str($values['bluetooth_mac'] ?? null),
        'baseband_serial' => nullable_str($values['baseband_serial'] ?? null),
        'hardware_model' => nullable_str($values['hardware_model'] ?? null),

        'battery_original' => tri_state_to_bool($values['battery_original'] ?? null),
        'screen_original' => tri_state_to_bool($values['screen_original'] ?? null),
        'previously_repaired' => tri_state_to_bool($values['previously_repaired'] ?? null),
        'known_issues' => nullable_str($values['known_issues'] ?? null),

        'activation_state' => nullable_str($values['activation_state'] ?? null),
        'icloud_lock_status' => nullable_str($values['icloud_lock_status'] ?? null),
        'find_my_enabled' => nullable_str($values['find_my_enabled'] ?? null),
        'passcode_protected' => nullable_str($values['passcode_protected'] ?? null),
        'mdm_enrolled' => tri_state_to_bool($values['mdm_enrolled'] ?? null),

        'can_test' => tri_state_to_bool($values['can_test'] ?? null),
        'device_password' => nullable_str($values['device_password'] ?? null),

        'batch_id' => $batchId,
        'supplier_manifest_item_id' => blank($values['supplier_manifest_item_id'] ?? null) ? null : (int)$values['supplier_manifest_item_id'],
    ]);

    if (!$created || !$db->create_inventory_item($values['serial_number'], $values['grade'])) {
        $errors[] = "Unable to check in device. Please try again.";
        view('intake.view.php', ['errors' => $errors, 'values' => $values]);
        return;
    }

    header('Location: /intake');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    view('intake.view.php');
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    handle_intake($db);
}