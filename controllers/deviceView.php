<?php
if (!Core\Common::current_user($db)) {
    header('Location: /login');
    exit;
}

$serial = trim((string)($_GET['serial'] ?? ''));
if ($serial === '') {
    header('Location: /inventory');
    exit;
}

$device = $db->get_device_by_serial($serial);
if (!$device) {
    header('Location: /inventory');
    exit;
}

$testResults = $db->get_canonical_test_results($serial);
view('deviceView.view.php', ['device' => $device, 'testResults' => $testResults]);