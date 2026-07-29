<?php
/**
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

$triState = [
    ''  => 'Unknown',
    '1' => 'Yes',
    '0' => 'No',
];

$grades = ['A', 'B', 'C', 'D', 'Parts', 'Scrap'];
$brands = ['Apple', 'Samsung', 'Google', 'OnePlus', 'Motorola', 'Other'];

$sections = [
    'identification' => [
        'legend' => 'Identification',
        'fields' => [
            ['name' => 'serial_number', 'label' => 'Serial Number', 'required' => true, 'placeholder' => 'C02XXXXXXXXX or a Samsung/Google serial'],
            ['name' => 'product_type', 'label' => 'Product Type / Model Identifier', 'required' => true, 'placeholder' => 'iPhone17,2 or SM-S918B'],
            ['name' => 'brand', 'label' => 'Brand', 'type' => 'select', 'options' => $brands, 'required' => true, 'hint' => "Only used the first time this Product Type is checked in — ignored afterwards."],
            ['name' => 'friendly_name', 'label' => 'Product Name', 'required' => true, 'placeholder' => 'Galaxy S23 Ultra', 'hint' => "Only used the first time this Product Type is checked in — ignored afterwards."],
            ['name' => 'model_number', 'label' => 'Model Number', 'placeholder' => 'MQ9T3LL/A or SM-S918BZKEXSA'],
            ['name' => 'color', 'label' => 'Colour', 'placeholder' => 'Phantom Black'],
            ['name' => 'region_code', 'label' => 'Region Code', 'placeholder' => 'Optional — mainly applies to iPhones'],
            ['name' => 'storage_gb', 'label' => 'Storage (GB)', 'type' => 'number', 'required' => true],
            ['name' => 'imei', 'label' => 'IMEI', 'placeholder' => 'Dial *#06# if not printed on the device/box'],
        ],
    ],
    'hardware' => [
        'legend' => 'Additional Hardware Identifiers',
        'hint' => 'Optional and rarely available at manual intake — expand only if you actually have this on hand.',
        'collapsible' => true,
        'fields' => [
            ['name' => 'imei2', 'label' => 'IMEI 2', 'placeholder' => 'Dual-SIM devices only'],
            ['name' => 'udid', 'label' => 'UDID / Android ID'],
            ['name' => 'meid', 'label' => 'MEID'],
            ['name' => 'iccid', 'label' => 'ICCID (SIM Serial)'],
            ['name' => 'wifi_mac', 'label' => 'Wi-Fi MAC', 'placeholder' => 'aa:bb:cc:dd:ee:ff'],
            ['name' => 'bluetooth_mac', 'label' => 'Bluetooth MAC', 'placeholder' => 'aa:bb:cc:dd:ee:ff'],
            ['name' => 'baseband_serial', 'label' => 'Baseband / Modem Version'],
            ['name' => 'hardware_model', 'label' => 'Hardware Model / Codename'],
        ],
    ],
    'condition' => [
        'legend' => 'Condition',
        'fields' => [
            ['name' => 'grade', 'label' => 'Grade', 'type' => 'select', 'options' => $grades, 'required' => true],
            ['name' => 'battery_original', 'label' => 'Battery Original?', 'type' => 'boolean'],
            ['name' => 'screen_original', 'label' => 'Screen Original?', 'type' => 'boolean'],
            ['name' => 'previously_repaired', 'label' => 'Previously Repaired?', 'type' => 'boolean'],
            ['name' => 'known_issues', 'label' => 'Known Issues', 'type' => 'textarea', 'full' => true, 'placeholder' => 'Any visible damage or reported faults'],
        ],
    ],
    'lock_state' => [
        'legend' => 'Activation & Lock State at Intake',
        'hint' => 'Optional — device management terms vary by platform, expand if relevant.',
        'collapsible' => true,
        'fields' => [
            ['name' => 'activation_state', 'label' => 'Activation State', 'placeholder' => 'Activated'],
            ['name' => 'icloud_lock_status', 'label' => 'iCloud / FRP Lock', 'placeholder' => 'Unlocked'],
            ['name' => 'find_my_enabled', 'label' => 'Find My / Find My Device', 'placeholder' => 'Yes / No'],
            ['name' => 'mdm_enrolled', 'label' => 'MDM / Device Admin Enrolled?', 'type' => 'boolean'],
        ],
    ],
    'check_in' => [
        'legend' => 'Check-in Details',
        'fields' => [
            ['name' => 'passcode_protected', 'label' => 'Passcode / Screen Lock Protected', 'placeholder' => 'Yes / No'],
            ['name' => 'device_password', 'label' => 'Device Passcode / PIN', 'placeholder' => 'If known'],
            ['name' => 'can_test', 'label' => 'Can Test?', 'type' => 'boolean'],
        ],
    ],
    'batch' => [
        'legend' => 'Batch / Provenance',
        'fields' => [
            ['name' => 'batch_number', 'label' => 'Batch Number', 'required' => true, 'placeholder' => 'BATCH-2026-001'],
            ['name' => 'supplier_manifest_item_id', 'label' => 'Supplier Manifest Item ID', 'type' => 'number', 'placeholder' => 'Optional'],
        ],
    ],
];
?>
<section class="flex min-h-[calc(100vh-4rem)] items-center justify-center p-8">
    <div class="w-full max-w-4xl rounded-md border border-text-muted/20 bg-surface p-8 shadow-sm dark:bg-surface-dark">
        <h1 class="text-2xl font-semibold text-text dark:text-white">Device Intake</h1>
        <p class="mt-2 text-sm text-text-muted dark:text-white/70">Check in a new device by recording its identifiers and condition at intake</p>

        <?php if (!empty($errors)): ?>
            <div class="mt-4 rounded-md border border-red-500/30 bg-red-500/10 p-3">
                <ul class="list-inside list-disc text-sm text-red-600 dark:text-red-400">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="/intake" class="mt-6 space-y-8">
            <?php foreach ($sections as $section):
                $legend = $section['legend'];
                $fields = $section['fields'];
                $isCollapsible = !empty($section['collapsible']);
                $wrapperTag = $isCollapsible ? 'details' : 'fieldset';
                $legendTag = $isCollapsible ? 'summary' : 'legend';
            ?>
                <<?= $wrapperTag ?> class="<?= $isCollapsible ? 'group rounded-md border border-text-muted/20 p-4' : '' ?>">
                    <<?= $legendTag ?> class="mb-3 text-sm font-semibold tracking-wide text-text-muted uppercase dark:text-white/70 <?= $isCollapsible ? 'cursor-pointer select-none group-open:mb-3' : '' ?>">
                        <?= htmlspecialchars($legend) ?>
                    </<?= $legendTag ?>>
                    <?php if (!empty($section['hint'])): ?>
                        <p class="-mt-2 mb-3 text-xs font-normal text-text-muted normal-case dark:text-white/50"><?= htmlspecialchars($section['hint']) ?></p>
                    <?php endif; ?>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <?php foreach ($fields as $field): ?>
                            <?php
                            $name = $field['name'];
                            $type = $field['type'] ?? 'text';
                            $id = 'intake-' . str_replace('_', '-', $name);
                            ?>
                            <div class="<?= !empty($field['full']) ? 'sm:col-span-2' : '' ?>">
                                <label for="<?= $id ?>" class="mb-1.5 block text-sm font-medium text-text dark:text-white">
                                    <?= htmlspecialchars($field['label']) ?><?= !empty($field['required']) ? ' <span class="text-red-500">*</span>' : '' ?>
                                </label>
                                <?php if ($type === 'boolean'): ?>
                                    <select id="<?= $id ?>" name="<?= $name ?>" class="<?= $selectClasses ?>">
                                        <?php foreach ($triState as $value => $label): ?>
                                            <option value="<?= $value ?>" <?= ($values[$name] ?? '') === $value ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php elseif ($type === 'select'): ?>
                                    <select id="<?= $id ?>" name="<?= $name ?>" class="<?= $selectClasses ?>">
                                        <option value="" <?= ($values[$name] ?? '') === '' ? 'selected' : '' ?>>Select…</option>
                                        <?php foreach ($field['options'] as $option): ?>
                                            <option value="<?= htmlspecialchars($option) ?>" <?= ($values[$name] ?? '') === $option ? 'selected' : '' ?>><?= htmlspecialchars($option) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php elseif ($type === 'textarea'): ?>
                                    <textarea
                                        id="<?= $id ?>"
                                        name="<?= $name ?>"
                                        rows="3"
                                        placeholder="<?= htmlspecialchars($field['placeholder'] ?? '') ?>"
                                        class="<?= $textareaClasses ?>"
                                    ><?= old($values, $name) ?></textarea>
                                <?php else: ?>
                                    <input
                                        id="<?= $id ?>"
                                        type="<?= $type ?>"
                                        name="<?= $name ?>"
                                        placeholder="<?= htmlspecialchars($field['placeholder'] ?? '') ?>"
                                        value="<?= old($values, $name) ?>"
                                        class="<?= $inputClasses ?>"
                                    />
                                <?php endif; ?>
                                <?php if (!empty($field['hint'])): ?>
                                    <p class="mt-1 text-xs text-text-muted dark:text-white/50"><?= htmlspecialchars($field['hint']) ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </<?= $wrapperTag ?>>
            <?php endforeach; ?>

            <button
                type="submit"
                class="w-full rounded-md bg-primary px-5 py-2.5 text-sm font-medium text-white transition hover:bg-primary-hover sm:w-auto"
            >
                Check In Device
            </button>
        </form>
    </div>
</section>