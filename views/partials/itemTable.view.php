<?php
/** @var Device[] $devices */

$statusLabels = [
    'in_stock' => 'In Stock',
    'listed'   => 'Listed',
    'reserved' => 'Reserved',
    'sold'     => 'Sold',
    'returned' => 'Returned',
    'scrapped' => 'Scrapped',
];

$grades = ['A', 'B', 'C', 'D', 'Parts', 'Scrap'];

$activeStatuses = ['in_stock', 'listed'];
$activeBadge = 'bg-primary/10 text-primary dark:bg-primary-light/10 dark:text-primary-light';
$inactiveBadge = 'bg-surface-muted text-text-muted dark:bg-surface-muted-dark dark:text-white/70';

function nearest_pow_of_two($number) {
    return 2 ** ceil(log($number, 2));
}

$selectClasses = 'rounded-md border border-text-muted/25 bg-surface px-3 py-2 text-sm text-text focus:border-primary focus:outline-none dark:bg-surface-dark dark:text-white';
?>
<div id="inventory-table" class="overflow-hidden rounded-md border border-text-muted/20 bg-surface shadow-sm dark:bg-surface-dark">
    <div class="flex flex-wrap items-center gap-3 border-b border-text-muted/20 p-4">
        <input
            type="search"
            id="inventory-table-search"
            placeholder="Search by name, serial number or IMEI"
            class="min-w-56 flex-1 rounded-md border border-text-muted/25 bg-surface px-3 py-2 text-sm text-text placeholder:text-text-muted focus:border-primary focus:outline-none dark:bg-surface-dark dark:text-white"
        />
        <div class="relative w-56">
            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-sm text-text-muted dark:text-white/70">$</span>
            <input
                type="text"
                inputmode="decimal"
                id="cost-to-price-addend"
                placeholder="Suggested additional margin"
                class="w-full rounded-md border border-text-muted/25 bg-surface py-2 pr-3 pl-6 text-sm text-text placeholder:text-text-muted focus:border-primary focus:outline-none dark:bg-surface-dark dark:text-white"
            />
        </div>
        <select id="inventory-table-grade" class="<?= $selectClasses ?>">
            <option value="">All Grades</option>
            <?php foreach ($grades as $grade): ?>
                <option value="<?= htmlspecialchars($grade) ?>"><?= htmlspecialchars($grade) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="max-h-[calc(100vh-13rem)] overflow-auto">
        <table class="min-w-full divide-y divide-text-muted/20">
            <thead class="sticky top-0 z-10 bg-surface-muted dark:bg-surface-muted-dark">
            <tr> <!-- Add back nice filter buttons beside header name once columns are finalized -->
                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold tracking-wide text-text-muted uppercase dark:text-white/70">Product Name</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold tracking-wide text-text-muted uppercase dark:text-white/70">Grade</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold tracking-wide text-text-muted uppercase dark:text-white/70">Storage Capacity</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold tracking-wide text-text-muted uppercase dark:text-white/70">Battery Health</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold tracking-wide text-text-muted uppercase dark:text-white/70">Refurbished?</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold tracking-wide text-text-muted uppercase dark:text-white/70">Serial Number</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold tracking-wide text-text-muted uppercase dark:text-white/70">IMEI</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold tracking-wide text-text-muted uppercase dark:text-white/70">Cost</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold tracking-wide text-text-muted uppercase dark:text-white/70">Suggested Price</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold tracking-wide text-text-muted uppercase dark:text-white/70">Batch</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-text-muted/10">
            <?php foreach ($devices as $device): ?>
                <tr
                    class="hover:bg-surface-muted/50 dark:hover:bg-surface-muted-dark/50"
                    data-name="<?= htmlspecialchars(strtolower($device->friendlyName . ' ' . $device->serialNumber . ' ' . $device->imei)) ?>"
                    data-grade="<?= htmlspecialchars($device->grade) ?>"
                    data-status="<?= htmlspecialchars($device->status) ?>"
                >
                    <td class="px-4 py-3 text-sm font-medium text-text dark:text-white"><?= htmlspecialchars($device->friendlyName) ?></td>
                    <td class="px-4 py-3 text-sm text-text-muted dark:text-white/70"><?= htmlspecialchars($device->grade) ?></td>
                    <td class="px-4 py-3 text-sm text-text-muted dark:text-white/70"><?= htmlspecialchars(nearest_pow_of_two($device->storageGb) . 'gb') ?></td>
                    <td class="px-4 py-3 text-sm text-text-muted dark:text-white/70"><?= htmlspecialchars((int)$device->batteryHealthPct . '%') ?></td>
                    <td class="px-4 py-3 text-sm text-text-muted dark:text-white/70"><?= htmlspecialchars($device->previouslyRepaired ? "Yes" : "No") ?></td>
                    <td class="px-4 py-3 text-sm text-text-muted dark:text-white/70"><?= htmlspecialchars($device->serialNumber) ?></td>
                    <td class="px-4 py-3 text-sm text-text-muted dark:text-white/70"><?= htmlspecialchars($device->imei) ?></td>

                    <td id="cost-column" class="px-4 py-3 text-sm text-text dark:text-white">
                        <?= $device->costPaid !== null ? '$' . number_format($device->costPaid, 2) : '—' ?>
                    </td>
                    <td id="suggested-price-column" class="px-4 py-3 text-sm text-text dark:text-white">—</td>
                    <td class="px-4 py-3 text-sm text-text-muted dark:text-white/70"><?= htmlspecialchars($device->batchNumber) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <div id="inventory-table-empty" class="hidden px-4 py-10 text-center text-sm text-text-muted">
            No devices match your filters.
        </div>
    </div>
</div>
<script>
    // Immediately invoked function expression to avoid global scope pollution
    (() => {
        const container = document.getElementById('inventory-table');
        const rows = Array.from(container.querySelectorAll('tbody tr'));

        // Search
        const search = document.getElementById('inventory-table-search');
        const gradeFilter = document.getElementById('inventory-table-grade');
        const emptyState = document.getElementById('inventory-table-empty');

        function applyFilters() {
            const query = search.value.trim().toLowerCase();
            const grade = gradeFilter.value;
            let visibleCount = 0;

            for (const row of rows) {
                const matchesQuery = !query || row.dataset.name.includes(query);
                const matchesGrade = !grade || row.dataset.grade === grade;
                const visible = matchesQuery && matchesGrade;

                row.classList.toggle('hidden', !visible);
                if (visible) visibleCount++;
            }

            emptyState.classList.toggle('hidden', visibleCount !== 0);
        }

        search.addEventListener('input', applyFilters);
        gradeFilter.addEventListener('change', applyFilters);

        // Margin calculation
        const addendInput = document.getElementById('cost-to-price-addend');

        function applyMarginToTable() {
            const addend = parseFloat(addendInput.value.trim());

            for (const row of rows) {
                if (!isNaN(addend)) {
                    const cost = parseFloat(row.querySelector('#cost-column').textContent.replace('$', ''));
                    const newPrice = Math.round((cost + addend) / 5) * 5;
                    row.querySelector('#suggested-price-column').textContent = '$' + newPrice.toFixed(2);
                } else {
                    row.querySelector('#suggested-price-column').textContent = '—';
                }
            }
        }

        addendInput.addEventListener('input', applyMarginToTable);

    })();
</script>