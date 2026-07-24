<!doctype html>
<html>
<?php require "partials/head.view.php"; ?>
<body class="min-h-screen bg-background text-text dark:bg-background-dark dark:text-white">
<?php require "partials/navbar.view.php"; ?>

<?php
/** @var User $user */
/** @var Device[] $recentIntakes */
/** @var Device[] $needsAttention */
/** @var array<string,int> $statusCounts */

$statusLabels = [
    'in_stock' => 'In Stock',
    'listed'   => 'Listed',
    'reserved' => 'Reserved',
    'sold'     => 'Sold',
    'returned' => 'Returned',
    'scrapped' => 'Scrapped',
];

$inStock = $statusCounts['in_stock'] ?? 0;
$sold = $statusCounts['sold'] ?? 0;

$cardClasses = 'rounded-md border border-text-muted/20 bg-surface p-5 shadow-sm dark:bg-surface-dark';
$placeholderCardClasses = 'rounded-md border border-dashed border-text-muted/30 bg-surface/50 p-5 dark:bg-surface-dark/50';
?>

<div class="mx-auto w-full max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

    <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-text dark:text-white">
                Welcome back, <?= htmlspecialchars($user->username) ?>
            </h1>
            <p class="mt-1 text-sm text-text-muted dark:text-white/70">
                Here's what's happening in the shop today.
            </p>
        </div>
        <div class="flex gap-3">
            <a href="/intake" class="rounded-md bg-primary px-5 py-2.5 text-sm font-medium text-white transition hover:bg-primary-hover">
                New Intake
            </a>
            <a href="/inventory" class="rounded-md bg-surface-muted px-5 py-2.5 text-sm font-medium text-text transition hover:bg-surface-muted/75 dark:bg-surface-muted-dark dark:text-white dark:hover:text-white/75">
                View Inventory
            </a>
        </div>
    </div>

    <!-- Live stats -->
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="<?= $cardClasses ?>">
            <p class="text-sm text-text-muted dark:text-white/70">Total Devices</p>
            <p class="mt-2 text-3xl font-bold text-text dark:text-white"><?= $totalDevices ?></p>
        </div>
        <div class="<?= $cardClasses ?>">
            <p class="text-sm text-text-muted dark:text-white/70">In Stock</p>
            <p class="mt-2 text-3xl font-bold text-text dark:text-white"><?= $inStock ?></p>
        </div>
        <div class="<?= $cardClasses ?>">
            <p class="text-sm text-text-muted dark:text-white/70">Sold</p>
            <p class="mt-2 text-3xl font-bold text-text dark:text-white"><?= $sold ?></p>
        </div>
        <div class="<?= $cardClasses ?>">
            <p class="text-sm text-text-muted dark:text-white/70">Needs Attention</p>
            <p class="mt-2 text-3xl font-bold text-text dark:text-white"><?= count($needsAttention) ?></p>
        </div>
    </div>

    <!-- Placeholder stats: sales module not built yet -->
    <div class="mt-4 grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="<?= $placeholderCardClasses ?>">
            <p class="text-sm text-text-muted dark:text-white/70">Sales This Month</p>
            <p class="mt-2 text-3xl font-bold text-text-muted/50 dark:text-white/30">&mdash;</p>
            <p class="mt-1 text-xs text-text-muted/70 dark:text-white/50">Coming soon</p>
        </div>
        <div class="<?= $placeholderCardClasses ?>">
            <p class="text-sm text-text-muted dark:text-white/70">Revenue This Month</p>
            <p class="mt-2 text-3xl font-bold text-text-muted/50 dark:text-white/30">&mdash;</p>
            <p class="mt-1 text-xs text-text-muted/70 dark:text-white/50">Coming soon</p>
        </div>
        <div class="<?= $placeholderCardClasses ?> col-span-2">
            <p class="text-sm text-text-muted dark:text-white/70">Top Selling Model</p>
            <p class="mt-2 text-3xl font-bold text-text-muted/50 dark:text-white/30">&mdash;</p>
            <p class="mt-1 text-xs text-text-muted/70 dark:text-white/50">Coming soon</p>
        </div>
    </div>

    <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-2">

        <!-- Recent intakes -->
        <div class="<?= $cardClasses ?>">
            <h2 class="text-lg font-semibold text-text dark:text-white">Recent Intakes</h2>
            <?php if (empty($recentIntakes)): ?>
                <p class="mt-4 text-sm text-text-muted dark:text-white/70">No devices have been added yet.</p>
            <?php else: ?>
                <ul class="mt-4 divide-y divide-text-muted/10">
                    <?php foreach ($recentIntakes as $device): ?>
                        <li class="flex items-center justify-between gap-4 py-3 first:pt-0 last:pb-0">
                            <div class="min-w-0">
                                <a href="/device?serial=<?= urlencode($device->serialNumber) ?>" class="block truncate font-medium text-primary hover:underline dark:text-primary-light">
                                    <?= htmlspecialchars($device->friendlyName) ?>
                                </a>
                                <p class="truncate text-xs text-text-muted dark:text-white/70">
                                    <?= htmlspecialchars($device->serialNumber) ?> &middot; <?= $device->intakeAt->format('M j, Y') ?>
                                </p>
                            </div>
                            <span class="shrink-0 rounded-full bg-surface-muted px-3 py-1 text-xs font-medium text-text-muted dark:bg-surface-muted-dark dark:text-white/70">
                                <?= htmlspecialchars($statusLabels[$device->status] ?? $device->status) ?>
                            </span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <!-- Needs attention -->
        <div class="<?= $cardClasses ?>">
            <h2 class="text-lg font-semibold text-text dark:text-white">Needs Attention</h2>
            <?php if (empty($needsAttention)): ?>
                <p class="mt-4 text-sm text-text-muted dark:text-white/70">Nothing needs attention right now.</p>
            <?php else: ?>
                <ul class="mt-4 divide-y divide-text-muted/10">
                    <?php foreach ($needsAttention as $device): ?>
                        <li class="flex items-center justify-between gap-4 py-3 first:pt-0 last:pb-0">
                            <div class="min-w-0">
                                <a href="/device?serial=<?= urlencode($device->serialNumber) ?>" class="block truncate font-medium text-primary hover:underline dark:text-primary-light">
                                    <?= htmlspecialchars($device->friendlyName) ?>
                                </a>
                                <p class="truncate text-xs text-text-muted dark:text-white/70">
                                    <?= htmlspecialchars($device->serialNumber) ?>
                                </p>
                            </div>
                            <span class="shrink-0 rounded-full bg-red-500/10 px-3 py-1 text-xs font-medium text-red-600 dark:text-red-400">
                                <?= $device->countFail ?> failed
                            </span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

    </div>

</div>

</body>
</html>