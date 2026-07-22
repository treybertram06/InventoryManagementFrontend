<?php
/**
 * @var Models\Device $device
 * @var array $catalog
 * @var array $errors
 * @var array $values
 */
$values ??= [];
$errors ??= [];

function old($values, string $key, string $default = ''): string {
    return htmlspecialchars((string)($values[$key] ?? $default));
}

function old_test_status($values, string $testId): string {
    return htmlspecialchars((string)($values['test_status'][$testId] ?? 'na'));
}

$inputClasses = 'w-full rounded-md border border-text-muted/25 bg-surface px-3 py-2 text-sm text-text placeholder:text-text-muted focus:border-primary focus:outline-none dark:bg-surface-dark dark:text-white';
$selectClasses = $inputClasses;
$textareaClasses = $inputClasses . ' resize-y';

$triState = [
    ''  => 'Unknown',
    '1' => 'Yes',
    '0' => 'No',
];
?>
<section class="flex min-h-[calc(100vh-4rem)] items-center justify-center p-6">
    <div class="w-full max-w-3xl rounded-md border border-text-muted/20 bg-surface p-6 shadow-sm dark:bg-surface-dark">
        <h1 class="text-xl font-semibold text-text dark:text-white">Manual Device Test</h1>
        <p class="mt-1 text-sm text-text-muted dark:text-white/70">
            <?= htmlspecialchars($device->friendlyName) ?> &middot; <?= htmlspecialchars($device->productType) ?> &middot; Serial <?= htmlspecialchars($device->serialNumber) ?>
        </p>
        <?php if (!empty($device->knownIssues)): ?>
            <p class="mt-1 text-xs text-text-muted dark:text-white/50">Known issues at intake: <?= htmlspecialchars($device->knownIssues) ?></p>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="mt-4 rounded-md border border-red-500/30 bg-red-500/10 p-3">
                <ul class="list-inside list-disc text-sm text-red-600 dark:text-red-400">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="/device-test?serial=<?= urlencode($device->serialNumber) ?>" class="mt-5 space-y-5">

            <fieldset>
                <legend class="mb-2 text-xs font-semibold tracking-wide text-text-muted uppercase dark:text-white/70">Session Info</legend>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                    <div>
                        <label for="test-battery-pct" class="mb-1 block text-sm font-medium text-text dark:text-white">Battery Charge (%)</label>
                        <input id="test-battery-pct" type="number" name="battery_pct" min="0" max="100" value="<?= old($values, 'battery_pct') ?>" class="<?= $inputClasses ?>" />
                    </div>
                    <div>
                        <label for="test-battery-health-pct" class="mb-1 block text-sm font-medium text-text dark:text-white">Battery Health (%)</label>
                        <input id="test-battery-health-pct" type="number" name="battery_health_pct" min="0" max="100" value="<?= old($values, 'battery_health_pct') ?>" class="<?= $inputClasses ?>" />
                    </div>
                    <div>
                        <label for="test-os-version" class="mb-1 block text-sm font-medium text-text dark:text-white">OS Version</label>
                        <input id="test-os-version" type="text" name="os_version" placeholder="iOS 18.2 / Android 14" value="<?= old($values, 'os_version') ?>" class="<?= $inputClasses ?>" />
                    </div>
                </div>
            </fieldset>

            <details class="group rounded-md border border-text-muted/20 p-3">
                <summary class="cursor-pointer text-xs font-semibold tracking-wide text-text-muted uppercase select-none group-open:mb-3 dark:text-white/70">Additional Readouts</summary>
                <p class="-mt-2 mb-3 text-xs font-normal text-text-muted normal-case dark:text-white/50">Optional — only fill in what you can read off the device.</p>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                    <div>
                        <label for="test-build-version" class="mb-1.5 block text-sm font-medium text-text dark:text-white">Build Version</label>
                        <input id="test-build-version" type="text" name="build_version" value="<?= old($values, 'build_version') ?>" class="<?= $inputClasses ?>" />
                    </div>
                    <div>
                        <label for="test-baseband-version" class="mb-1.5 block text-sm font-medium text-text dark:text-white">Baseband / Modem Version</label>
                        <input id="test-baseband-version" type="text" name="baseband_version" value="<?= old($values, 'baseband_version') ?>" class="<?= $inputClasses ?>" />
                    </div>
                    <div>
                        <label for="test-battery-cycles" class="mb-1.5 block text-sm font-medium text-text dark:text-white">Battery Cycle Count</label>
                        <input id="test-battery-cycles" type="number" min="0" name="battery_cycles" value="<?= old($values, 'battery_cycles') ?>" class="<?= $inputClasses ?>" />
                    </div>
                    <div>
                        <label for="test-battery-temp-c" class="mb-1.5 block text-sm font-medium text-text dark:text-white">Battery Temp (&deg;C)</label>
                        <input id="test-battery-temp-c" type="number" step="0.1" name="battery_temp_c" value="<?= old($values, 'battery_temp_c') ?>" class="<?= $inputClasses ?>" />
                    </div>
                    <div>
                        <label for="test-battery-impedance" class="mb-1.5 block text-sm font-medium text-text dark:text-white">Battery Impedance (mOhm)</label>
                        <input id="test-battery-impedance" type="number" name="battery_impedance_mohm" value="<?= old($values, 'battery_impedance_mohm') ?>" class="<?= $inputClasses ?>" />
                    </div>
                    <div>
                        <label for="test-battery-serial" class="mb-1.5 block text-sm font-medium text-text dark:text-white">Battery Serial</label>
                        <input id="test-battery-serial" type="text" name="battery_serial" value="<?= old($values, 'battery_serial') ?>" class="<?= $inputClasses ?>" />
                    </div>
                    <div>
                        <label for="test-is-charging" class="mb-1.5 block text-sm font-medium text-text dark:text-white">Charging While Testing?</label>
                        <select id="test-is-charging" name="is_charging" class="<?= $selectClasses ?>">
                            <?php foreach ($triState as $value => $label): ?>
                                <option value="<?= $value ?>" <?= ($values['is_charging'] ?? '') === $value ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="test-data-capacity-gb" class="mb-1.5 block text-sm font-medium text-text dark:text-white">Storage Capacity (GB)</label>
                        <input id="test-data-capacity-gb" type="number" step="0.1" name="data_capacity_gb" value="<?= old($values, 'data_capacity_gb') ?>" class="<?= $inputClasses ?>" />
                    </div>
                    <div>
                        <label for="test-data-available-gb" class="mb-1.5 block text-sm font-medium text-text dark:text-white">Storage Available (GB)</label>
                        <input id="test-data-available-gb" type="number" step="0.1" name="data_available_gb" value="<?= old($values, 'data_available_gb') ?>" class="<?= $inputClasses ?>" />
                    </div>
                </div>
            </details>

            <fieldset>
                <legend class="mb-1 text-xs font-semibold tracking-wide text-text-muted uppercase dark:text-white/70">Functional Checklist</legend>
                <p class="mb-2 text-xs text-text-muted dark:text-white/50">Tap a test to cycle through the options.</p>
                <div class="space-y-3">
                    <?php foreach ($catalog as $groupKey => $group): ?>
                        <div>
                            <p class="mb-1.5 text-[11px] font-semibold tracking-wide text-text-muted uppercase dark:text-white/50"><?= htmlspecialchars($group['label']) ?></p>
                            <div class="grid grid-cols-2 gap-1.5 sm:grid-cols-3 lg:grid-cols-4">
                                <?php foreach ($group['tests'] as $testId => $testLabel): ?>
                                    <button
                                        type="button"
                                        class="test-toggle flex items-center justify-between gap-1.5 rounded-md border px-2 py-1.5 text-left text-xs font-medium transition"
                                        data-status="<?= old_test_status($values, $testId) ?>"
                                    >
                                        <span class="truncate"><?= htmlspecialchars($testLabel) ?></span>
                                        <span class="test-toggle-badge shrink-0 rounded px-1.5 py-0.5 text-[10px] font-semibold uppercase"></span>
                                    </button>
                                    <input type="hidden" name="test_status[<?= htmlspecialchars($testId) ?>]" id="test-status-<?= htmlspecialchars($testId) ?>" value="<?= old_test_status($values, $testId) ?>" />
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </fieldset>

            <fieldset>
                <legend class="mb-2 text-xs font-semibold tracking-wide text-text-muted uppercase dark:text-white/70">Revise Grade (Optional)</legend>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div>
                        <label for="test-grade" class="mb-1 block text-sm font-medium text-text dark:text-white">Grade</label>
                        <select id="test-grade" name="grade" class="<?= $selectClasses ?>">
                            <option value="" <?= ($values['grade'] ?? '') === '' ? 'selected' : '' ?>>Keep current (<?= htmlspecialchars($device->grade) ?>)</option>
                            <?php foreach (GRADES as $gradeOption): ?>
                                <option value="<?= htmlspecialchars($gradeOption) ?>" <?= ($values['grade'] ?? '') === $gradeOption ? 'selected' : '' ?>><?= htmlspecialchars($gradeOption) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label for="test-condition-notes" class="mb-1 block text-sm font-medium text-text dark:text-white">Condition Notes</label>
                        <textarea id="test-condition-notes" name="condition_notes" rows="2" class="<?= $textareaClasses ?>"><?= old($values, 'condition_notes', $device->conditionNotes ?? '') ?></textarea>
                    </div>
                </div>
            </fieldset>

            <button
                type="submit"
                class="w-full rounded-md bg-primary px-5 py-2.5 text-sm font-medium text-white transition hover:bg-primary-hover sm:w-auto"
            >
                Submit Test Results
            </button>
        </form>
    </div>
    <script>
        (() => {
            const STATUSES = ['na', 'pass', 'fail'];
            const STYLES = {
                na: {
                    box: 'border-text-muted/25 border-dashed bg-surface text-text-muted dark:bg-surface-dark dark:text-white/60',
                    badge: 'bg-text-muted/15 text-text-muted dark:bg-white/10 dark:text-white/50',
                    text: 'N/A',
                },
                pass: {
                    box: 'border-green-500/40 bg-green-500/10 text-text dark:text-white',
                    badge: 'bg-green-500/20 text-green-700 dark:text-green-400',
                    text: 'Pass',
                },
                fail: {
                    box: 'border-red-500/40 bg-red-500/10 text-text dark:text-white',
                    badge: 'bg-red-500/20 text-red-700 dark:text-red-400',
                    text: 'Fail',
                },
            };
            const BOX_BASE = 'test-toggle flex items-center justify-between gap-1.5 rounded-md border px-2 py-1.5 text-left text-xs font-medium transition';

            function applyStatus(button, status) {
                const style = STYLES[status] ?? STYLES.na;
                button.dataset.status = status;
                button.className = `${BOX_BASE} ${style.box}`;
                const badge = button.querySelector('.test-toggle-badge');
                badge.textContent = style.text;
                badge.className = `test-toggle-badge shrink-0 rounded px-1.5 py-0.5 text-[10px] font-semibold uppercase ${style.badge}`;
            }

            document.querySelectorAll('.test-toggle').forEach((button) => {
                const hidden = button.nextElementSibling;
                applyStatus(button, button.dataset.status || 'na');

                button.addEventListener('click', () => {
                    const current = button.dataset.status || 'na';
                    const next = STATUSES[(STATUSES.indexOf(current) + 1) % STATUSES.length];
                    applyStatus(button, next);
                    hidden.value = next;
                });
            });
        })();
    </script>
</section>