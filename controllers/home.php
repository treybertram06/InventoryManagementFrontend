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

$soldDevices = array_filter($devices, fn($d) => $d->status === 'sold' && $d->soldAt !== null);

$currentMonth = (new DateTime())->format('Y-m');
$soldThisMonth = array_filter($soldDevices, fn($d) => $d->soldAt->format('Y-m') === $currentMonth);

$salesThisMonth = count($soldThisMonth);
$revenueThisMonth = array_sum(array_map(fn($d) => $d->salePrice ?? 0, $soldThisMonth));

$modelSaleCounts = [];
foreach ($soldDevices as $device) {
    $modelSaleCounts[$device->friendlyName] = ($modelSaleCounts[$device->friendlyName] ?? 0) + 1;
}
arsort($modelSaleCounts);
$topSellingModel = array_key_first($modelSaleCounts);
$topSellingModelCount = $modelSaleCounts[$topSellingModel] ?? 0;

view('home.view.php', [
    'db' => $db,
    'user' => $user,
    'totalDevices' => count($devices),
    'statusCounts' => $statusCounts,
    'recentIntakes' => $recentIntakes,
    'needsAttention' => $needsAttention,
    'salesThisMonth' => $salesThisMonth,
    'revenueThisMonth' => $revenueThisMonth,
    'topSellingModel' => $topSellingModel,
    'topSellingModelCount' => $topSellingModelCount,
]);