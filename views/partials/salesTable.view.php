<?php
/** @var Models\Sale[] $sales */

$selectClasses = 'rounded-md border border-text-muted/25 bg-surface px-3 py-2 text-sm text-text focus:border-primary focus:outline-none dark:bg-surface-dark dark:text-white';
$inputClasses = 'rounded-md border border-text-muted/25 bg-surface px-3 py-2 text-sm text-text placeholder:text-text-muted focus:border-primary focus:outline-none dark:bg-surface-dark dark:text-white';

$technicians = array_values(array_unique(array_map(fn($s) => $s->technician, $sales)));
sort($technicians);

global $db;
$currentUser = Core\Common::current_user($db);
$isAdmin = $currentUser && $currentUser->role === Models\UserRole::Admin;
?>
<div id="sales-table" class="overflow-hidden rounded-md border border-text-muted/20 bg-surface shadow-sm dark:bg-surface-dark">
    <div class="flex flex-wrap items-center gap-3 border-b border-text-muted/20 p-4">
        <input
            type="search"
            id="sales-table-search"
            placeholder="Search by serial number, IMEI, or buyer info"
            class="min-w-64 flex-1 <?= $inputClasses ?>"
        />
        <label class="flex items-center gap-2 text-sm text-text-muted dark:text-white/70">
            From
            <input type="date" id="sales-table-date-from" class="<?= $inputClasses ?>">
        </label>
        <label class="flex items-center gap-2 text-sm text-text-muted dark:text-white/70">
            To
            <input type="date" id="sales-table-date-to" class="<?= $inputClasses ?>">
        </label>
        <select id="sales-table-technician" class="<?= $selectClasses ?>">
            <option value="">All Technicians</option>
            <?php foreach ($technicians as $technician): ?>
                <option value="<?= htmlspecialchars($technician) ?>"><?= htmlspecialchars($technician) ?></option>
            <?php endforeach; ?>
        </select>
        <select id="sales-table-status" class="<?= $selectClasses ?>">
            <option value="">All Statuses</option>
            <option value="completed">Completed</option>
            <option value="reversed">Reversed</option>
        </select>
    </div>
    <div class="max-h-[calc(100vh-13rem)] overflow-auto">
        <table class="min-w-full divide-y divide-text-muted/20">
            <thead class="sticky top-0 z-10 bg-surface-muted dark:bg-surface-muted-dark">
            <tr>
                <?php
                $columns = [
                    ['label' => 'Date Sold',     'type' => 'number'],
                    ['label' => 'Product',       'type' => 'text'],
                    ['label' => 'Serial Number', 'type' => 'text'],
                    ['label' => 'IMEI',          'type' => 'text'],
                    ['label' => 'Sale Price',    'type' => 'number'],
                    ['label' => 'Sale Channel',  'type' => 'text'],
                    ['label' => 'Technician',    'type' => 'text'],
                    ['label' => 'Status',        'type' => 'text'],
                    ['label' => 'Actions',       'type' => 'text'],
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
            <?php foreach ($sales as $sale): ?>
                <?php
                    $status = $sale->is_reversed() ? 'reversed' : 'completed';
                    $statusBadge = $sale->is_reversed()
                        ? 'bg-red-500/10 text-red-600 dark:text-red-400'
                        : 'bg-green-500/20 text-green-700 dark:text-green-400';
                    $searchValue = strtolower($sale->serialNumber . ' ' . $sale->imei . ' ' . $sale->friendlyName . ' ' . $sale->buyerInfo);
                ?>
                <tr
                    class="hover:bg-surface-muted/50 dark:hover:bg-surface-muted-dark/50"
                    data-search="<?= htmlspecialchars($searchValue) ?>"
                    data-technician="<?= htmlspecialchars($sale->technician) ?>"
                    data-status="<?= $status ?>"
                    data-sold-date="<?= $sale->soldAt->format('Y-m-d') ?>"
                >
                    <td class="px-4 py-3 text-sm whitespace-nowrap text-text-muted dark:text-white/70" data-sort-value="<?= $sale->soldAt->getTimestamp() ?>"><?= htmlspecialchars($sale->soldAt->format('M j, Y g:i A')) ?></td>
                    <td class="px-4 py-3 text-sm font-medium whitespace-nowrap text-text dark:text-white"><?= htmlspecialchars($sale->friendlyName) ?></td>
                    <td class="px-4 py-3 text-sm whitespace-nowrap text-text-muted dark:text-white/70"><?= htmlspecialchars($sale->serialNumber) ?></td>
                    <td class="px-4 py-3 text-sm whitespace-nowrap text-text-muted dark:text-white/70"><?= htmlspecialchars($sale->imei ?? '—') ?></td>
                    <td class="px-4 py-3 text-sm whitespace-nowrap text-text dark:text-white">$<?= number_format($sale->salePrice, 2) ?></td>
                    <td class="px-4 py-3 text-sm whitespace-nowrap text-text-muted dark:text-white/70"><?= htmlspecialchars($sale->saleChannel ?? '—') ?></td>
                    <td class="px-4 py-3 text-sm whitespace-nowrap text-text-muted dark:text-white/70"><?= htmlspecialchars($sale->technician) ?></td>
                    <td class="px-4 py-3 text-sm whitespace-nowrap">
                        <span class="rounded px-2 py-1 text-xs font-semibold uppercase <?= $statusBadge ?>"><?= ucfirst($status) ?></span>
                    </td>
                    <td class="px-4 py-3 text-sm whitespace-nowrap">
                        <a href="/device?serial=<?= urlencode($sale->serialNumber) ?>"
                           class="mr-3 font-medium text-primary hover:underline dark:text-primary-light">
                            View Device
                        </a>

                        <?php if ($isAdmin && !$sale->is_reversed()): ?>
                            <a href="/admin-sale-edit?id=<?= $sale->id ?>"
                               class="mr-3 font-medium text-primary hover:underline dark:text-primary-light">
                                Edit
                            </a>

                            <form method="POST" action="/admin-sale-reverse" class="inline" onsubmit="return confirm('Reverse this sale? The item will return to in-stock.');">
                                <input type="hidden" name="sale_id" value="<?= $sale->id ?>">
                                <button type="submit" class="font-medium text-red-600 hover:underline dark:text-red-400">
                                    Reverse Sale
                                </button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <div id="sales-table-empty" class="hidden px-4 py-10 text-center text-sm text-text-muted">
            No sales match your filters.
        </div>
    </div>
</div>
<script>
    // Immediately invoked function expression to avoid global scope pollution
    (() => {
        const container = document.getElementById('sales-table');
        const rows = Array.from(container.querySelectorAll('tbody tr'));

        // Search & filters
        const search = document.getElementById('sales-table-search');
        const dateFrom = document.getElementById('sales-table-date-from');
        const dateTo = document.getElementById('sales-table-date-to');
        const technicianFilter = document.getElementById('sales-table-technician');
        const statusFilter = document.getElementById('sales-table-status');
        const emptyState = document.getElementById('sales-table-empty');

        function applyFilters() {
            const query = search.value.trim().toLowerCase();
            const from = dateFrom.value;
            const to = dateTo.value;
            const technician = technicianFilter.value;
            const status = statusFilter.value;
            let visibleCount = 0;

            for (const row of rows) {
                const matchesQuery = !query || row.dataset.search.includes(query);
                const matchesFrom = !from || row.dataset.soldDate >= from;
                const matchesTo = !to || row.dataset.soldDate <= to;
                const matchesTechnician = !technician || row.dataset.technician === technician;
                const matchesStatus = !status || row.dataset.status === status;
                const visible = matchesQuery && matchesFrom && matchesTo && matchesTechnician && matchesStatus;

                row.classList.toggle('hidden', !visible);
                if (visible) visibleCount++;
            }

            emptyState.classList.toggle('hidden', visibleCount !== 0);
        }

        search.addEventListener('input', applyFilters);
        dateFrom.addEventListener('change', applyFilters);
        dateTo.addEventListener('change', applyFilters);
        technicianFilter.addEventListener('change', applyFilters);
        statusFilter.addEventListener('change', applyFilters);

        // Sorting
        const tbody = container.querySelector('tbody');
        const headerCells = Array.from(container.querySelectorAll('thead th'));
        const sortState = { index: null, dir: 'asc' };

        function getSortValue(row, index, type) {
            const cell = row.children[index];
            const raw = cell.dataset.sortValue !== undefined ? cell.dataset.sortValue : cell.textContent.trim();

            if (type === 'number') {
                const num = parseFloat(String(raw).replace(/[^0-9.\-]/g, ''));
                return isNaN(num) ? null : num;
            }

            return raw.toLowerCase();
        }

        function sortRows(index, dir, type) {
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
    })();
</script>