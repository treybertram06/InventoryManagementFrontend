<?php
/**
 * @var Models\Device $device
 * @var array $models
 * @var array $errors
 * @var array $values
 */
$values ??= [];
$errors ??= [];

function old($values, string $key, string $default = ''): string {
    return htmlspecialchars((string)($values[$key] ?? $default));
}

$inputClasses = 'w-full rounded-md border border-text-muted/25 bg-surface px-3 py-2 text-sm text-text placeholder:text-text-muted focus:border-primary focus:outline-none dark:bg-surface-dark dark:text-white';
$selectClasses = $inputClasses;
$textareaClasses = $inputClasses . ' resize-y';
$labelClasses = 'mb-1.5 block text-sm font-medium text-text dark:text-white';

$selectedProductType = $values['product_type'] ?? $device->productType;

$storageSizes = [16, 32, 64, 128, 256, 512, 1024, 2048];
$selectedStorage = $values['storage_gb'] ?? ($device->storageGb !== null ? (int)$device->storageGb : '');
if ($selectedStorage !== '' && !in_array((int)$selectedStorage, $storageSizes, true)) {
    $storageSizes[] = (int)$selectedStorage;
    sort($storageSizes);
}
?>
<section class="flex min-h-[calc(100vh-4rem)] items-center justify-center p-8">
    <div class="w-full max-w-3xl rounded-md border border-text-muted/20 bg-surface p-8 shadow-sm dark:bg-surface-dark">
        <div class="mb-4">
            <a href="/device?serial=<?= urlencode($device->serialNumber) ?>" class="text-sm font-medium text-primary hover:underline dark:text-primary-light">&larr; Back to Device</a>
        </div>

        <h1 class="text-2xl font-semibold text-text dark:text-white">Edit Device</h1>
        <p class="mt-2 text-sm text-text-muted dark:text-white/70">
            <?= htmlspecialchars($device->friendlyName) ?> &middot; Serial <?= htmlspecialchars($device->serialNumber) ?>
        </p>

        <?php if (!empty($errors)): ?>
            <div class="mt-4 rounded-md border border-red-500/30 bg-red-500/10 p-3">
                <ul class="list-inside list-disc text-sm text-red-600 dark:text-red-400">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="/device-edit" class="mt-6 space-y-4">
            <input type="hidden" name="serial_number" value="<?= htmlspecialchars($device->serialNumber) ?>">

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="edit-product-type" class="<?= $labelClasses ?>">Product Type</label>
                    <select id="edit-product-type" name="product_type" class="<?= $selectClasses ?>">
                        <?php foreach ($models as $model): ?>
                            <option value="<?= htmlspecialchars($model['product_type']) ?>" <?= $selectedProductType === $model['product_type'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($model['friendly_name']) ?> (<?= htmlspecialchars($model['product_type']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="mt-1 text-xs text-text-muted dark:text-white/50">New product types can only be added through device intake.</p>
                </div>

                <div>
                    <label for="edit-model-number" class="<?= $labelClasses ?>">Model Number</label>
                    <input
                        id="edit-model-number"
                        type="text"
                        name="model_number"
                        value="<?= old($values, 'model_number', $device->modelNumber ?? '') ?>"
                        class="<?= $inputClasses ?>">
                </div>

                <div>
                    <label for="edit-color" class="<?= $labelClasses ?>">Color</label>
                    <input
                        id="edit-color"
                        type="text"
                        name="color"
                        value="<?= old($values, 'color', $device->color ?? '') ?>"
                        class="<?= $inputClasses ?>">
                </div>

                <div>
                    <label for="edit-region-code" class="<?= $labelClasses ?>">Region Code</label>
                    <input
                        id="edit-region-code"
                        type="text"
                        name="region_code"
                        value="<?= old($values, 'region_code', $device->regionCode ?? '') ?>"
                        class="<?= $inputClasses ?>">
                </div>

                <div>
                    <label for="edit-storage-gb" class="<?= $labelClasses ?>">Storage (GB)</label>
                    <select id="edit-storage-gb" name="storage_gb" class="<?= $selectClasses ?>">
                        <?php foreach ($storageSizes as $size): ?>
                            <option value="<?= $size ?>" <?= (string)$size === (string)$selectedStorage ? 'selected' : '' ?>><?= $size ?> GB</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label for="edit-known-issues" class="<?= $labelClasses ?>">Known Issues</label>
                    <textarea
                        id="edit-known-issues"
                        name="known_issues"
                        rows="4"
                        class="<?= $textareaClasses ?>"><?= old($values, 'known_issues', $device->knownIssues ?? '') ?></textarea>
                </div>
            </div>

            <hr class="border-text-muted/20">

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="edit-grade" class="<?= $labelClasses ?>">Grade</label>
                    <?php $selectedGrade = $values['grade'] ?? $device->grade; ?>
                    <select id="edit-grade" name="grade" class="<?= $selectClasses ?>">
                        <?php foreach (['A', 'B', 'C', 'D', 'Parts', 'Scrap'] as $grade): ?>
                            <option value="<?= $grade ?>" <?= $selectedGrade === $grade ? 'selected' : '' ?>><?= $grade ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="edit-repair-cost" class="<?= $labelClasses ?>">Repair Cost</label>
                    <input
                        id="edit-repair-cost"
                        type="number"
                        step="0.01"
                        name="repair_cost"
                        value="<?= old($values, 'repair_cost', (string)$device->repairCost) ?>"
                        class="<?= $inputClasses ?>">
                </div>

                <div>
                    <label for="edit-b2b-floor-price" class="<?= $labelClasses ?>">B2B Floor Price</label>
                    <input
                        id="edit-b2b-floor-price"
                        type="number"
                        step="0.01"
                        name="b2b_floor_price"
                        value="<?= old($values, 'b2b_floor_price', $device->b2bFloorPrice !== null ? (string)$device->b2bFloorPrice : '') ?>"
                        class="<?= $inputClasses ?>">
                </div>

                <div>
                    <label for="edit-b2c-floor-price" class="<?= $labelClasses ?>">B2C Floor Price</label>
                    <input
                        id="edit-b2c-floor-price"
                        type="number"
                        step="0.01"
                        name="b2c_floor_price"
                        value="<?= old($values, 'b2c_floor_price', $device->b2cFloorPrice !== null ? (string)$device->b2cFloorPrice : '') ?>"
                        class="<?= $inputClasses ?>">
                </div>

                <div class="sm:col-span-2">
                    <label for="edit-condition-notes" class="<?= $labelClasses ?>">Condition Notes</label>
                    <textarea
                        id="edit-condition-notes"
                        name="condition_notes"
                        rows="3"
                        class="<?= $textareaClasses ?>"><?= old($values, 'condition_notes', $device->conditionNotes ?? '') ?></textarea>
                </div>

                <div class="sm:col-span-2">
                    <label for="edit-repairs-needed-done" class="<?= $labelClasses ?>">Repairs Needed / Done</label>
                    <textarea
                        id="edit-repairs-needed-done"
                        name="repairs_needed_done"
                        rows="3"
                        class="<?= $textareaClasses ?>"><?= old($values, 'repairs_needed_done', $device->repairsNeededDone ?? '') ?></textarea>
                </div>
            </div>

            <button
                type="submit"
                class="w-full rounded-md bg-primary px-5 py-2.5 text-sm font-medium text-white transition hover:bg-primary-hover sm:w-auto"
            >
                Save Changes
            </button>
        </form>
    </div>
</section>