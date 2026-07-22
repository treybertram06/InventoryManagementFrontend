<?php
/**
 * @var Models\Device $device
 * @var array $testResults
 */

$statusLabels = [
    'in_stock' => 'In Stock',
    'listed'   => 'Listed',
    'reserved' => 'Reserved',
    'sold'     => 'Sold',
    'returned' => 'Returned',
    'scrapped' => 'Scrapped',
];

$cardClasses = 'rounded-md border border-text-muted/20 bg-surface p-6 shadow-sm dark:bg-surface-dark';
$labelClasses = 'text-xs font-semibold tracking-wide text-text-muted uppercase dark:text-white/70';
$valueClasses = 'mt-1 text-sm text-text dark:text-white';

function fmt($value): string {
    return ($value === null || $value === '') ? '—' : htmlspecialchars((string)$value);
}

function fmt_yes_no(?bool $value): string {
    return $value === null ? '—' : ($value ? 'Yes' : 'No');
}

function fmt_money(?float $value): string {
    return $value === null ? '—' : '$' . number_format($value, 2);
}

function field(string $label, string $value, string $labelClasses, string $valueClasses): void {
    echo '<div><p class="' . $labelClasses . '">' . htmlspecialchars($label) . '</p><p class="' . $valueClasses . '">' . $value . '</p></div>';
}

$storageLabel = $device->storageGb !== null ? (int)(2 ** ceil(log(max($device->storageGb, 1), 2))) . 'GB' : '—';

$statusBadge = 'bg-primary/10 text-primary dark:bg-primary-light/10 dark:text-primary-light';
$gradeBadge = 'bg-surface-muted text-text-muted dark:bg-surface-muted-dark dark:text-white/70';

$resultBadgeClasses = [
    'pass'    => 'bg-green-500/20 text-green-700 dark:text-green-400',
    'fail'    => 'bg-red-500/20 text-red-700 dark:text-red-400',
    'na'      => 'bg-text-muted/15 text-text-muted dark:bg-white/10 dark:text-white/50',
    'pending' => 'bg-yellow-500/20 text-yellow-700 dark:text-yellow-400',
    'skipped' => 'bg-text-muted/15 text-text-muted dark:bg-white/10 dark:text-white/50',
];
?>
<div class="mx-auto max-w-[96rem] px-4 py-6 sm:px-6 lg:px-8">

    <div class="mb-4">
        <a href="/inventory" class="text-sm font-medium text-primary hover:underline dark:text-primary-light">&larr; Back to Inventory</a>
    </div>

    <div class="<?= $cardClasses ?> mb-6 flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="text-xl font-semibold text-text dark:text-white"><?= fmt($device->friendlyName) ?></h1>
            <p class="mt-1 text-sm text-text-muted dark:text-white/70">
                <?= fmt($device->productType) ?> &middot; Serial <?= fmt($device->serialNumber) ?>
            </p>
            <div class="mt-3 flex flex-wrap gap-2">
                <span class="rounded px-2 py-1 text-xs font-semibold uppercase <?= $statusBadge ?>"><?= fmt($statusLabels[$device->status] ?? $device->status) ?></span>
                <span class="rounded px-2 py-1 text-xs font-semibold uppercase <?= $gradeBadge ?>">Grade <?= fmt($device->grade) ?></span>
            </div>
        </div>
        <a href="/device-test?serial=<?= urlencode($device->serialNumber) ?>" class="rounded-md bg-primary px-4 py-2 text-sm font-medium text-white transition hover:bg-primary-hover">
            Run Test
        </a>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

        <div class="<?= $cardClasses ?>">
            <h2 class="mb-4 text-sm font-semibold text-text dark:text-white">Specs</h2>
            <div class="grid grid-cols-2 gap-4">
                <?php field('Model Number', fmt($device->modelNumber), $labelClasses, $valueClasses); ?>
                <?php field('Color', fmt($device->color), $labelClasses, $valueClasses); ?>
                <?php field('Region', fmt($device->regionCode), $labelClasses, $valueClasses); ?>
                <?php field('Storage', $storageLabel, $labelClasses, $valueClasses); ?>
                <?php field('IMEI', fmt($device->imei), $labelClasses, $valueClasses); ?>
            </div>
        </div>

        <div class="<?= $cardClasses ?>">
            <h2 class="mb-4 text-sm font-semibold text-text dark:text-white">Condition</h2>
            <div class="grid grid-cols-2 gap-4">
                <?php field('Original Battery', fmt_yes_no($device->batteryOriginal), $labelClasses, $valueClasses); ?>
                <?php field('Original Screen', fmt_yes_no($device->screenOriginal), $labelClasses, $valueClasses); ?>
                <?php field('Previously Repaired', fmt_yes_no($device->previouslyRepaired), $labelClasses, $valueClasses); ?>
                <?php field('Battery Health', $device->batteryHealthPct !== null ? (int)$device->batteryHealthPct . '%' : '—', $labelClasses, $valueClasses); ?>
                <div class="col-span-2"><?php field('Known Issues at Intake', fmt($device->knownIssues), $labelClasses, $valueClasses); ?></div>
                <div class="col-span-2"><?php field('Condition Notes', fmt($device->conditionNotes), $labelClasses, $valueClasses); ?></div>
                <div class="col-span-2"><?php field('Repairs Needed / Done', fmt($device->repairsNeededDone), $labelClasses, $valueClasses); ?></div>
            </div>
        </div>

        <div class="<?= $cardClasses ?>">
            <h2 class="mb-4 text-sm font-semibold text-text dark:text-white">Commercial</h2>
            <div class="grid grid-cols-2 gap-4">
                <?php field('Cost Paid', fmt_money($device->costPaid), $labelClasses, $valueClasses); ?>
                <?php field('Repair Cost', fmt_money($device->repairCost), $labelClasses, $valueClasses); ?>
                <?php field('B2B Floor Price', fmt_money($device->b2bFloorPrice), $labelClasses, $valueClasses); ?>
                <?php field('B2C Floor Price', fmt_money($device->b2cFloorPrice), $labelClasses, $valueClasses); ?>
                <?php if ($device->status === 'sold'): ?>
                    <?php field('Sale Price', fmt_money($device->salePrice), $labelClasses, $valueClasses); ?>
                    <?php field('Sale Channel', fmt($device->saleChannel), $labelClasses, $valueClasses); ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="<?= $cardClasses ?>">
            <h2 class="mb-4 text-sm font-semibold text-text dark:text-white">Provenance</h2>
            <div class="grid grid-cols-2 gap-4">
                <?php field('Batch Number', fmt($device->batchNumber), $labelClasses, $valueClasses); ?>
                <?php field('Technician', fmt($device->technician), $labelClasses, $valueClasses); ?>
                <?php field('Received At', fmt($device->receivedAt->format('M j, Y g:i A')), $labelClasses, $valueClasses); ?>
                <?php field('Intake At', fmt($device->intakeAt->format('M j, Y g:i A')), $labelClasses, $valueClasses); ?>
            </div>
        </div>

    </div>

    <div class="<?= $cardClasses ?> mt-6">
        <h2 class="mb-4 text-sm font-semibold text-text dark:text-white">Diagnostic Summary</h2>
        <?php if (empty($testResults) && $device->countPass === null && $device->countFail === null): ?>
            <p class="text-sm text-text-muted dark:text-white/70">
                No diagnostic session recorded yet.
                <a href="/device-test?serial=<?= urlencode($device->serialNumber) ?>" class="font-medium text-primary hover:underline dark:text-primary-light">Run a test</a>
                to get started.
            </p>
        <?php else: ?>
            <div class="mb-5 flex flex-wrap gap-3">
                <span class="rounded px-2 py-1 text-xs font-semibold <?= $resultBadgeClasses['pass'] ?>"><?= (int)$device->countPass ?> Pass</span>
                <span class="rounded px-2 py-1 text-xs font-semibold <?= $resultBadgeClasses['fail'] ?>"><?= (int)$device->countFail ?> Fail</span>
                <span class="rounded px-2 py-1 text-xs font-semibold <?= $resultBadgeClasses['na'] ?>"><?= (int)$device->countNa ?> N/A</span>
                <span class="rounded px-2 py-1 text-xs font-semibold <?= $resultBadgeClasses['pending'] ?>"><?= (int)$device->countPending ?> Pending</span>
            </div>
            <?php if (!empty($testResults)): ?>
                <div class="overflow-hidden rounded-md border border-text-muted/20">
                    <table class="min-w-full divide-y divide-text-muted/20">
                        <thead class="bg-surface-muted dark:bg-surface-muted-dark">
                        <tr>
                            <th scope="col" class="px-4 py-2 text-left text-xs font-semibold tracking-wide text-text-muted uppercase dark:text-white/70">Group</th>
                            <th scope="col" class="px-4 py-2 text-left text-xs font-semibold tracking-wide text-text-muted uppercase dark:text-white/70">Test</th>
                            <th scope="col" class="px-4 py-2 text-left text-xs font-semibold tracking-wide text-text-muted uppercase dark:text-white/70">Status</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-text-muted/10">
                        <?php foreach ($testResults as $result): ?>
                            <tr>
                                <td class="px-4 py-2 text-sm text-text-muted dark:text-white/70"><?= fmt($result['test_group']) ?></td>
                                <td class="px-4 py-2 text-sm text-text dark:text-white"><?= fmt($result['test_label'] ?? $result['test_id']) ?></td>
                                <td class="px-4 py-2 text-sm">
                                    <span class="rounded px-2 py-0.5 text-xs font-semibold uppercase <?= $resultBadgeClasses[$result['status']] ?? $resultBadgeClasses['na'] ?>"><?= fmt($result['status']) ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

</div>