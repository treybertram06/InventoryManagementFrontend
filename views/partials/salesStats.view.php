<?php
/** @var array $stats */

$cardClasses = 'rounded-md border border-text-muted/20 bg-surface p-5 shadow-sm dark:bg-surface-dark';

// Each bar is scaled to its block's largest value. Ternary because max([]) is fatal in PHP 8.
$maxMonthRevenue   = $stats['byMonth']   ? max(array_column($stats['byMonth'], 'revenue'))   : 0;
$maxModelRevenue   = $stats['byModel']   ? max(array_column($stats['byModel'], 'revenue'))   : 0;
$maxChannelRevenue = $stats['byChannel'] ? max(array_column($stats['byChannel'], 'revenue')) : 0;
?>

<!-- Headline figures -->
<div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
    <div class="<?= $cardClasses ?>">
        <p class="text-sm text-text-muted dark:text-white/70">Units Sold</p>
        <p class="mt-2 text-3xl font-bold text-text dark:text-white"><?= $stats['unitsSold'] ?></p>
    </div>
    <div class="<?= $cardClasses ?>">
        <p class="text-sm text-text-muted dark:text-white/70">Total Revenue</p>
        <p class="mt-2 text-3xl font-bold text-text dark:text-white">$<?= number_format($stats['totalRevenue'], 2) ?></p>
    </div>
    <div class="<?= $cardClasses ?>">
        <p class="text-sm text-text-muted dark:text-white/70">Gross Margin</p>
        <p class="mt-2 text-3xl font-bold <?= $stats['totalMargin'] < 0 ? 'text-red-600 dark:text-red-400' : 'text-text dark:text-white' ?>">
            <?= ($stats['totalMargin'] < 0 ? '-$' : '$') . number_format(abs($stats['totalMargin']), 2) ?>
        </p>
        <?php if ($stats['unknownCost'] > 0): ?>
            <p class="mt-1 text-xs text-text-muted dark:text-white/70">
                Optimistic: <?= $stats['unknownCost'] ?> sale<?= $stats['unknownCost'] === 1 ? '' : 's' ?>
                with incomplete cost data
            </p>
        <?php else: ?>
            <p class="mt-1 text-xs text-text-muted dark:text-white/70">
                Revenue less supplier cost and repairs
            </p>
        <?php endif; ?>
    </div>
    <div class="<?= $cardClasses ?>">
        <p class="text-sm text-text-muted dark:text-white/70">Average Sale Price</p>
        <p class="mt-2 text-3xl font-bold text-text dark:text-white">$<?= number_format($stats['avgSalePrice'], 2) ?></p>
    </div>
</div>

<!-- Breakdowns -->
<div class="mt-6 grid gap-4 lg:grid-cols-3">

    <div class="<?= $cardClasses ?>">
        <h2 class="text-sm font-semibold text-text dark:text-white">Revenue by Month</h2>
        <?php if (!$stats['byMonth']): ?>
            <p class="mt-4 text-sm text-text-muted dark:text-white/70">No sales recorded.</p>
        <?php else: ?>
            <ul class="mt-4 space-y-3">
                <?php foreach ($stats['byMonth'] as $month => $row): ?>
                    <?php
                        // (int) so nothing but a plain number can reach the style attribute
                        $pct = $maxMonthRevenue > 0 ? (int)round($row['revenue'] / $maxMonthRevenue * 100) : 0;
                        // 'Y-m' is not a full date, so append a day before formatting it for display
                        $label = DateTime::createFromFormat('Y-m-d', $month . '-01')->format('M Y');
                    ?>
                    <li>
                        <div class="flex items-baseline justify-between text-sm">
                            <span class="text-text dark:text-white"><?= htmlspecialchars($label) ?></span>
                            <span class="text-text-muted dark:text-white/70">
                                $<?= number_format($row['revenue'], 2) ?>
                                <span class="ml-1 text-xs">(<?= $row['units'] ?>)</span>
                            </span>
                        </div>
                        <div class="mt-1 h-2 rounded-full bg-surface-muted dark:bg-surface-muted-dark">
                            <div class="h-2 rounded-full bg-primary" style="width: <?= $pct ?>%"></div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

    <div class="<?= $cardClasses ?>">
        <h2 class="text-sm font-semibold text-text dark:text-white">Top Models by Revenue</h2>
        <?php if (!$stats['byModel']): ?>
            <p class="mt-4 text-sm text-text-muted dark:text-white/70">No sales recorded.</p>
        <?php else: ?>
            <ul class="mt-4 space-y-3">
                <?php // byModel is already revenue-ordered; the `true` preserves the model name as key. ?>
                <?php foreach (array_slice($stats['byModel'], 0, 6, true) as $model => $row): ?>
                    <?php $pct = $maxModelRevenue > 0 ? (int)round($row['revenue'] / $maxModelRevenue * 100) : 0; ?>
                    <li>
                        <div class="flex items-baseline justify-between gap-2 text-sm">
                            <span class="truncate text-text dark:text-white"><?= htmlspecialchars($model) ?></span>
                            <span class="whitespace-nowrap text-text-muted dark:text-white/70">
                                $<?= number_format($row['revenue'], 2) ?>
                                <span class="ml-1 text-xs">(<?= $row['units'] ?>)</span>
                            </span>
                        </div>
                        <div class="mt-1 h-2 rounded-full bg-surface-muted dark:bg-surface-muted-dark">
                            <div class="h-2 rounded-full bg-primary" style="width: <?= $pct ?>%"></div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

    <div class="<?= $cardClasses ?>">
        <h2 class="text-sm font-semibold text-text dark:text-white">Revenue by Channel</h2>
        <?php if (!$stats['byChannel']): ?>
            <p class="mt-4 text-sm text-text-muted dark:text-white/70">No sales recorded.</p>
        <?php else: ?>
            <ul class="mt-4 space-y-3">
                <?php foreach ($stats['byChannel'] as $channel => $row): ?>
                    <?php $pct = $maxChannelRevenue > 0 ? (int)round($row['revenue'] / $maxChannelRevenue * 100) : 0; ?>
                    <li>
                        <div class="flex items-baseline justify-between gap-2 text-sm">
                            <span class="truncate text-text dark:text-white"><?= htmlspecialchars($channel) ?></span>
                            <span class="whitespace-nowrap text-text-muted dark:text-white/70">
                                $<?= number_format($row['revenue'], 2) ?>
                                <span class="ml-1 text-xs">(<?= $row['units'] ?>)</span>
                            </span>
                        </div>
                        <div class="mt-1 h-2 rounded-full bg-surface-muted dark:bg-surface-muted-dark">
                            <div class="h-2 rounded-full bg-primary" style="width: <?= $pct ?>%"></div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

</div>
