<?php

const GRADES = ['A', 'B', 'C', 'D', 'Parts', 'Scrap'];
const TEST_STATUSES = ['pass', 'fail', 'na'];

function blank($value): bool {
    return trim((string)($value ?? '')) === '';
}

function nullable_str($value): ?string {
    return blank($value) ? null : $value;
}

function nullable_float($value): ?float {
    return blank($value) ? null : (float)$value;
}

function nullable_int($value): ?int {
    return blank($value) ? null : (int)$value;
}

function tri_state_to_bool($value): ?int {
    if (blank($value)) {
        return null;
    }
    return $value === '1' ? 1 : 0;
}

function get_manual_test_catalog(array $capabilities): array {
    $catalog = [
        'buttons' => [
            'label' => 'Buttons',
            'tests' => [
                'vol_up' => 'Volume Up button',
                'vol_down' => 'Volume Down button',
                'silent_switch' => 'Silent switch',
                'power_btn' => 'Power / Side button',
            ],
        ],
        'display' => [
            'label' => 'Display & Touch',
            'tests' => [
                'touchscreen' => 'Touchscreen response',
                'display' => 'Display (dead pixels / burn-in / discoloration)',
            ],
        ],
        'camera' => [
            'label' => 'Camera',
            'tests' => [
                'cam_rear' => 'Rear camera',
                'cam_front' => 'Front camera',
                'torch' => 'Flash / Torch',
            ],
        ],
        'biometrics' => [
            'label' => 'Biometrics',
            'tests' => [],
        ],
        'audio' => [
            'label' => 'Audio & Haptics',
            'tests' => [
                'speaker' => 'Speaker',
                'mic' => 'Microphone',
                'haptic' => 'Vibration / Haptics',
            ],
        ],
        'connectivity' => [
            'label' => 'Connectivity',
            'tests' => [
                'wifi' => 'Wi-Fi',
                'bluetooth' => 'Bluetooth',
                'cellular' => 'Cellular / SIM',
            ],
        ],
        'sensors' => [
            'label' => 'Sensors',
            'tests' => [
                'prox' => 'Proximity sensor',
                'als' => 'Ambient light sensor',
            ],
        ],
    ];

    if (!empty($capabilities['has_action_button'])) {
        $catalog['buttons']['tests']['action_btn'] = 'Action button';
    }
    if (!empty($capabilities['has_camera_button'])) {
        $catalog['buttons']['tests']['camera_btn'] = 'Camera Control button';
    }
    if (!empty($capabilities['has_telephoto'])) {
        $catalog['camera']['tests']['cam_telephoto'] = 'Telephoto camera';
    }
    if (!empty($capabilities['has_face_id'])) {
        $catalog['biometrics']['tests']['face_id'] = 'Face ID';
    }
    if (!empty($capabilities['has_home_button'])) {
        $catalog['biometrics']['tests']['touch_id'] = 'Home button / Touch ID';
    }

    if (empty($catalog['biometrics']['tests'])) {
        unset($catalog['biometrics']);
    }

    return $catalog;
}

function handle_device_test(Core\Database $db, string $serial): void {
    $device = $db->get_device_by_serial($serial);
    if (!$device) {
        header('Location: /inventory');
        exit;
    }

    $capabilities = $db->get_device_model_capabilities($device->productType) ?? [];
    $catalog = get_manual_test_catalog($capabilities);

    $values = $_POST;
    $errors = [];

    foreach ($catalog as $group) {
        foreach ($group['tests'] as $testId => $testLabel) {
            $status = $values['test_status'][$testId] ?? null;
            if (blank($status) || !in_array($status, TEST_STATUSES, true)) {
                $errors[] = "Please set a status for \"$testLabel\".";
            }
        }
    }

    $grade = nullable_str($values['grade'] ?? null);
    if ($grade !== null && !in_array($grade, GRADES, true)) {
        $errors[] = "Please select a valid grade.";
    }

    if (!empty($errors)) {
        view('deviceTest.view.php', ['device' => $device, 'catalog' => $catalog, 'errors' => $errors, 'values' => $values]);
        return;
    }

    $counts = ['pass' => 0, 'fail' => 0, 'na' => 0, 'pending' => 0];
    $results = [];
    foreach ($catalog as $groupKey => $group) {
        foreach ($group['tests'] as $testId => $testLabel) {
            $status = $values['test_status'][$testId];
            if (isset($counts[$status])) {
                $counts[$status]++;
            }
            $results[] = ['test_id' => $testId, 'label' => $testLabel, 'group' => $groupKey, 'status' => $status];
        }
    }

    $now = date('Y-m-d H:i:s');
    $technician = Core\Common::current_user($db)->username ?? null;

    $sessionId = $db->create_diagnostic_session([
        'serial_number' => $device->serialNumber,
        'technician' => $technician,
        'ios_version' => nullable_str($values['os_version'] ?? null),
        'build_version' => nullable_str($values['build_version'] ?? null),
        'baseband_version' => nullable_str($values['baseband_version'] ?? null),
        'battery_pct' => nullable_int($values['battery_pct'] ?? null),
        'battery_health_pct' => nullable_float($values['battery_health_pct'] ?? null),
        'battery_cycles' => nullable_int($values['battery_cycles'] ?? null),
        'battery_temp_c' => nullable_float($values['battery_temp_c'] ?? null),
        'battery_impedance_mohm' => nullable_int($values['battery_impedance_mohm'] ?? null),
        'battery_serial' => nullable_str($values['battery_serial'] ?? null),
        'is_charging' => tri_state_to_bool($values['is_charging'] ?? null),
        'data_capacity_gb' => nullable_float($values['data_capacity_gb'] ?? null),
        'data_available_gb' => nullable_float($values['data_available_gb'] ?? null),
        'count_pass' => $counts['pass'],
        'count_fail' => $counts['fail'],
        'count_na' => $counts['na'],
        'count_pending' => $counts['pending'],
        'started_at' => $now,
        'ended_at' => $now,
        'elapsed_seconds' => 0,
    ]);

    foreach ($results as $result) {
        $db->create_test_result($sessionId, $result['test_id'], $result['label'], $result['group'], $result['status']);
    }

    $db->set_canonical_session($device->serialNumber, $sessionId);

    if ($grade !== null && $grade !== $device->grade) {
        $conditionNotes = nullable_str($values['condition_notes'] ?? null) ?? $device->conditionNotes;
        $db->update_inventory_item_grade($device->serialNumber, $grade, $conditionNotes);
    }

    header('Location: /inventory');
    exit;
}

if (!Core\Common::current_user($db)) {
    header('Location: /login');
    exit;
}

$serial = $_GET['serial'] ?? '';
if (blank($serial)) {
    header('Location: /inventory');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $device = $db->get_device_by_serial($serial);
    if (!$device) {
        header('Location: /inventory');
        exit;
    }
    $capabilities = $db->get_device_model_capabilities($device->productType) ?? [];
    view('deviceTest.view.php', ['device' => $device, 'catalog' => get_manual_test_catalog($capabilities)]);
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    handle_device_test($db, $serial);
}