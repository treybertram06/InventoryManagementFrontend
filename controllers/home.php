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

// From the sale table, not $devices: a device row only carries its latest non-reversed sale.
$stats = Core\Common::sales_statistics(Core\Common::reportable_sales($db->get_all_sales()));

$currentMonth = (new DateTime())->format('Y-m');
$salesThisMonth = $stats['byMonth'][$currentMonth]['units'] ?? 0;
$revenueThisMonth = $stats['byMonth'][$currentMonth]['revenue'] ?? 0;

// byModel is ordered by revenue, but this card shows most units - so max on 'units', not first.
$topSellingModel = null;
$topSellingModelCount = 0;
foreach ($stats['byModel'] as $model => $row) {
    if ($row['units'] > $topSellingModelCount) {
        $topSellingModel = $model;
        $topSellingModelCount = $row['units'];
    }
}

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