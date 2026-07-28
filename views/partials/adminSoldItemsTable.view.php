<?php
/** @var Models\Device[] $soldDevices */
?>
<div class="overflow-hidden rounded-md border border-text-muted/20 bg-surface shadow-sm dark:bg-surface-dark">
    <div class="border-b border-text-muted/20 p-4">
        <h2 class="text-lg font-semibold text-text dark:text-white">Sale Reversal</h2>
    </div>

    <?php if (empty($soldDevices)): ?>
        <div class="flex items-center justify-center p-10">
            <?php require "noDataFound.view.php"; ?>
        </div>
    <?php else: ?>
        <div class="overflow-auto">
            <table class="min-w-full divide-y divide-text-muted/20">
                <thead class="bg-surface-muted dark:bg-surface-muted-dark">
                <tr>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold tracking-wide text-text-muted uppercase dark:text-white/70">Product</th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold tracking-wide text-text-muted uppercase dark:text-white/70">Serial Number</th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold tracking-wide text-text-muted uppercase dark:text-white/70">Sale Price</th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold tracking-wide text-text-muted uppercase dark:text-white/70">Sale Channel</th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold tracking-wide text-text-muted uppercase dark:text-white/70">Sold At</th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold tracking-wide text-text-muted uppercase dark:text-white/70">Actions</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-text-muted/10">
                <?php foreach ($soldDevices as $device): ?>
                    <tr class="hover:bg-surface-muted/50 dark:hover:bg-surface-muted-dark/50">
                        <td class="px-4 py-3 text-sm font-medium whitespace-nowrap text-text dark:text-white"><?= htmlspecialchars($device->friendlyName) ?></td>
                        <td class="px-4 py-3 text-sm whitespace-nowrap text-text-muted dark:text-white/70"><?= htmlspecialchars($device->serialNumber) ?></td>
                        <td class="px-4 py-3 text-sm whitespace-nowrap text-text dark:text-white"><?= $device->salePrice !== null ? '$' . number_format($device->salePrice, 2) : '—' ?></td>
                        <td class="px-4 py-3 text-sm whitespace-nowrap text-text-muted dark:text-white/70"><?= htmlspecialchars($device->saleChannel ?? '—') ?></td>
                        <td class="px-4 py-3 text-sm whitespace-nowrap text-text-muted dark:text-white/70"><?= $device->soldAt ? htmlspecialchars($device->soldAt->format('M j, Y g:i A')) : '—' ?></td>
                        <td class="px-4 py-3 text-sm whitespace-nowrap">
                            <form method="POST" action="/admin-sale-reverse" onsubmit="return confirm('Reverse this sale? The item will return to in-stock and its sale details will be cleared.');">
                                <input type="hidden" name="serial_number" value="<?= htmlspecialchars($device->serialNumber) ?>">
                                <button type="submit" class="font-medium text-primary hover:underline dark:text-primary-light">
                                    Reverse Sale
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
