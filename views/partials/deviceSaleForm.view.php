<?php
/**
 * @var Models\Device $device
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
?>

<section class="flex min-h-[calc(100vh-4rem)] items-center justify-center p-8">

    <div class="w-full max-w-3xl rounded-md border border-text-muted/20 bg-surface p-8 shadow-sm dark:bg-surface-dark">

        <div class="mb-4">
            <a href="/device?serial=<?= urlencode($device->serialNumber) ?>"
               class="text-sm font-medium text-primary hover:underline dark:text-primary-light">
                &larr; Back to Device
            </a>
        </div>

        <h1 class="text-2xl font-semibold text-text dark:text-white">
            Process Sale
        </h1>

        <p class="mt-2 text-sm text-text-muted dark:text-white/70">
            <?= htmlspecialchars($device->friendlyName) ?>
            &middot;
            Serial <?= htmlspecialchars($device->serialNumber) ?>
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

        <form method="POST" action="/device-sale" class="mt-6 space-y-4">

            <input
                type="hidden"
                name="serial_number"
                value="<?= htmlspecialchars($device->serialNumber) ?>">

            <div class="grid grid-cols-1 gap-4">

                <div>
                    <label for="sale-price" class="<?= $labelClasses ?>">
                        Sale Price ($)
                    </label>

                    <input
                        id="sale-price"
                        type="number"
                        step="0.01"
                        min="0.01"
                        name="sale_price"
                        value="<?= old($values, 'sale_price') ?>"
                        class="<?= $inputClasses ?>"
                        required>
                </div>

                <div>
                    <label for="sale-channel" class="<?= $labelClasses ?>">
                        Sale Channel
                    </label>

                    <select
                        id="sale-channel"
                        name="sale_channel"
                        class="<?= $selectClasses ?>"
                        required>

                        <option value="">Select Sale Channel</option>

                        <option value="In Store"
                            <?= old($values, 'sale_channel') === 'In Store' ? 'selected' : '' ?>>
                            In Store
                        </option>

                        <option value="eBay"
                            <?= old($values, 'sale_channel') === 'eBay' ? 'selected' : '' ?>>
                            eBay
                        </option>

                        <option value="Facebook Marketplace"
                            <?= old($values, 'sale_channel') === 'Facebook Marketplace' ? 'selected' : '' ?>>
                            Facebook Marketplace
                        </option>

                        <option value="Wholesale"
                            <?= old($values, 'sale_channel') === 'Wholesale' ? 'selected' : '' ?>>
                            Wholesale
                        </option>

                    </select>
                </div>

                <div>
                    <label for="buyer-info" class="<?= $labelClasses ?>">
                        Buyer Information
                    </label>

                    <textarea
                        id="buyer-info"
                        name="buyer_info"
                        rows="4"
                        class="<?= $textareaClasses ?>"><?= old($values, 'buyer_info') ?></textarea>

                    <p class="mt-1 text-xs text-text-muted dark:text-white/50">
                        Enter the customer's name, company, invoice number, or any other relevant sale information.
                    </p>
                </div>

            </div>

            <hr class="border-text-muted/20">

            <div class="rounded-md border border-yellow-500/30 bg-yellow-500/10 p-4">
                <p class="text-sm text-yellow-700 dark:text-yellow-300">
                    Completing this sale will mark the device as <strong>Sold</strong>,
                    save the sale details, and remove it from the active inventory list.
                </p>
            </div>

            <button
                type="submit"
                class="w-full rounded-md bg-primary px-5 py-2.5 text-sm font-medium text-white transition hover:bg-primary-hover sm:w-auto">
                Complete Sale
            </button>

        </form>

    </div>

</section>