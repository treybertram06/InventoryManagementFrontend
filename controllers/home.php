<?php

$user = Core\Common::current_user($db);
if (!$user) {
    header('Location: /login');
    exit;
}

$devices = $db->get_all_devices() ?? [];

$statusCounts = [];
$needsAttention = [];
foreach ($devices as $device) {
    $statusCounts[$device->status] = ($statusCounts[$device->status] ?? 0) + 1;
    if ($device->status === 'in_stock' && $device->countFail > 0) {
        $needsAttention[] = $device;
    }
}

$recentIntakes = $devices;
usort($recentIntakes, fn($a, $b) => $b->intakeAt <=> $a->intakeAt);
$recentIntakes = array_slice($recentIntakes, 0, 5);
$needsAttention = array_slice($needsAttention, 0, 5);

view('home.view.php', [
    'db' => $db,
    'user' => $user,
    'totalDevices' => count($devices),
    'statusCounts' => $statusCounts,
    'recentIntakes' => $recentIntakes,
    'needsAttention' => $needsAttention,
]);