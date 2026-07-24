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
    return 2 ** ceil(log($number ?? 0, 2));
}

$selectClasses = 'rounded-md border border-text-muted/25 bg-surface px-3 py-2 text-sm text-text focus:border-primary focus:outline-none dark:bg-surface-dark dark:text-white';

global $db;
$currentUser = Core\Common::current_user($db);
$isAdmin = $currentUser && $currentUser->role === Models\UserRole::Admin;
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
            <tr>
                <?php
                $columns = [
                    ['label' => 'Product Name',       'type' => 'text'],
                    ['label' => 'Grade',              'type' => 'number'],
                    ['label' => 'Colour',             'type' => 'number'],
                    ['label' => 'Storage Capacity',   'type' => 'number'],
                    ['label' => 'Battery Health',     'type' => 'number'],
                    ['label' => 'Refurbished?',       'type' => 'text'],
                    ['label' => 'Serial Number',      'type' => 'text'],
                    ['label' => 'IMEI',               'type' => 'text'],
                    ['label' => 'Cost',               'type' => 'number'],
                    ['label' => 'Suggested Price',    'type' => 'number'],
                    ['label' => 'Batch',              'type' => 'text'],
                    ['label' => 'Actions',            'type' => 'text'],
                ];
                ?>
                <?php foreach ($columns as $column): ?>
                    <th
                        scope="col"
                        role="button"
                        tabindex="0"
                        aria-sort="none"
                        data-sort-type="<?= $column['type'] ?>"
                        class="cursor-pointer select-none px-4 py-3 text-left text-xs font-semibold tracking-wide whitespace-nowrap text-text-muted uppercase hover:text-text dark:text-white/70 dark:hover:text-white"
                    >
                        <?= htmlspecialchars($column['label']) ?><span class="sort-indicator ml-1 inline-block w-3 text-primary dark:text-primary-light"></span>
                    </th>
                <?php endforeach; ?>
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
                    <?php $gradeRank = array_search($device->grade, $grades); // Has to be sorted manually because theres no implicit value within the grading system
                    // it would actually work fine with the labels I chose for grades - coincidentally, the grades are alphabetical, including 'Parts' and 'Scrap' - but this is more robust ?>
                    <td class="px-4 py-3 text-sm text-text-muted dark:text-white/70" data-sort-value="<?= $gradeRank !== false ? $gradeRank : count($grades) ?>"><?= htmlspecialchars($device->grade) ?></td>
                    <td class="px-4 py-3 text-sm text-text-muted dark:text-white/70"><?= htmlspecialchars($device->color) ?></td>
                    <td class="px-4 py-3 text-sm text-text-muted dark:text-white/70"><?= htmlspecialchars(nearest_pow_of_two($device->storageGb) . 'gb') ?></td>
                    <td class="px-4 py-3 text-sm text-text-muted dark:text-white/70"><?= htmlspecialchars((int)$device->batteryHealthPct . '%') ?></td>
                    <td class="px-4 py-3 text-sm text-text-muted dark:text-white/70"><?= htmlspecialchars($device->previouslyRepaired ? "Yes" : "No") ?></td>
                    <td class="px-4 py-3 text-sm text-text-muted dark:text-white/70"><?= htmlspecialchars($device->serialNumber) ?></td>
                    <td class="px-4 py-3 text-sm text-text-muted dark:text-white/70"><?= htmlspecialchars($device->imei ?? '—') ?></td>

                    <td id="cost-column" class="px-4 py-3 text-sm text-text dark:text-white">
                        <?= $device->costPaid !== null ? '$' . number_format($device->costPaid, 2) : '—' ?>
                    </td>
                    <td id="suggested-price-column" class="px-4 py-3 text-sm text-text dark:text-white">—</td>
                    <td class="px-4 py-3 text-sm text-text-muted dark:text-white/70"><?= htmlspecialchars($device->batchNumber) ?></td>
                    <td class="px-4 py-3 text-sm">
                        <a href="/device?serial=<?= urlencode($device->serialNumber) ?>"
                        class="mr-3 font-medium text-primary hover:underline dark:text-primary-light">
                            View
                        </a>

                        <a href="/device-test?serial=<?= urlencode($device->serialNumber) ?>"
                        class="mr-3 font-medium text-primary hover:underline dark:text-primary-light">
                            Test
                        </a>

                        <?php if ($isAdmin): ?>
                            <a href="/device-edit?serial=<?= urlencode($device->serialNumber) ?>"
                            class="mr-3 font-medium text-primary hover:underline dark:text-primary-light">
                                Edit
                            </a>

                            <form method="POST" action="/device-delete" class="inline" onsubmit="return confirm('Delete this device? This cannot be easily undone.');">
                                <input type="hidden" name="serial_number" value="<?= htmlspecialchars($device->serialNumber) ?>">
                                <button type="submit" class="font-medium text-red-600 hover:underline dark:text-red-400">
                                    Delete
                                </button>
                            </form>
                        <?php endif; ?>
                    </td>
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

        // Sorting
        const tbody = container.querySelector('tbody');
        const headerCells = Array.from(container.querySelectorAll('thead th'));
        const sortState = { index: null, dir: 'asc' };

        function getSortValue(row, index, type) {
            const cell = row.children[index];
            const raw = cell.dataset.sortValue !== undefined ? cell.dataset.sortValue : cell.textContent.trim();

            if (type === 'number') {
                // Coerces into a raw string, replaces any non-(digit, period, or hyphen) character (identified by
                // regex [^0-9.\-], the 'g' at the end makes it replace *every* character in the string) with nothing,
                // then parses that remaining string as a float
                const num = parseFloat(String(raw).replace(/[^0-9.\-]/g, ''));
                return isNaN(num) ? null : num; // Convert NaN to null so it gets put at the bottom of the list regardless of sort direction
            }

            return raw.toLowerCase();
        }

        function sortRows(index, dir, type) {
            // Sort a shallow-copy of rows because you can't sort an arrayLike
            const sorted = [...rows].sort((a, b) => {
                const va = getSortValue(a, index, type);
                const vb = getSortValue(b, index, type);

                if (va === null && vb === null) return 0;
                if (va === null) return 1;
                if (vb === null) return -1;
                if (va < vb) return dir === 'asc' ? -1 : 1;
                if (va > vb) return dir === 'asc' ? 1 : -1;
                return 0;
            });

            for (const row of sorted) tbody.appendChild(row);
        }

        function updateSortIndicators() {
            headerCells.forEach((th, i) => {
                const indicator = th.querySelector('.sort-indicator');
                if (i === sortState.index) {
                    indicator.textContent = sortState.dir === 'asc' ? '▲' : '▼';
                    th.setAttribute('aria-sort', sortState.dir === 'asc' ? 'ascending' : 'descending');
                } else {
                    indicator.textContent = '';
                    th.setAttribute('aria-sort', 'none');
                }
            });
        }

        function handleSort(index) {
            sortState.dir = (sortState.index === index && sortState.dir === 'asc') ? 'desc' : 'asc';
            sortState.index = index;
            sortRows(index, sortState.dir, headerCells[index].dataset.sortType);
            updateSortIndicators();
        }

        headerCells.forEach((th, i) => {
            th.addEventListener('click', () => handleSort(i));
            th.addEventListener('keydown', (event) => {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    handleSort(i);
                }
            });
        });

        // Margin calculation
        const addendInput = document.getElementById('cost-to-price-addend');
        const suggestedPriceColumnIndex = headerCells.findIndex((th) => th.textContent.trim().startsWith('Suggested Price'));

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

            if (sortState.index === suggestedPriceColumnIndex) {
                sortRows(sortState.index, sortState.dir, 'number');
            }
        }

        addendInput.addEventListener('input', applyMarginToTable);

    })();
</script>