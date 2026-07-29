<?php
/**
 * @var Models\Sale $sale
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

$selectedChannel = $values['sale_channel'] ?? $sale->saleChannel;
$soldAtValue = $values['sold_at'] ?? $sale->soldAt->format('Y-m-d\TH:i');
$channels = ['In Store', 'eBay', 'Facebook Marketplace', 'Wholesale'];
?>

<section class="flex min-h-[calc(100vh-4rem)] items-center justify-center p-8">

    <div class="w-full max-w-3xl rounded-md border border-text-muted/20 bg-surface p-8 shadow-sm dark:bg-surface-dark">

        <div class="mb-4">
            <a href="/sales-history" class="text-sm font-medium text-primary hover:underline dark:text-primary-light">
                &larr; Back to Sales History
            </a>
        </div>

        <h1 class="text-2xl font-semibold text-text dark:text-white">Edit Sale</h1>

        <p class="mt-2 text-sm text-text-muted dark:text-white/70">
            <?= htmlspecialchars($sale->friendlyName) ?>
            &middot;
            Serial <?= htmlspecialchars($sale->serialNumber) ?>
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

        <form method="POST" action="/admin-sale-edit" class="mt-6 space-y-4">

            <input type="hidden" name="id" value="<?= $sale->id ?>">

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
                        value="<?= old($values, 'sale_price', (string)$sale->salePrice) ?>"
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

                        <?php foreach ($channels as $channel): ?>
                            <option value="<?= htmlspecialchars($channel) ?>" <?= $selectedChannel === $channel ? 'selected' : '' ?>>
                                <?= htmlspecialchars($channel) ?>
                            </option>
                        <?php endforeach; ?>

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
                        class="<?= $textareaClasses ?>"><?= old($values, 'buyer_info', (string)$sale->buyerInfo) ?></textarea>
                </div>

                <div>
                    <label for="sold-at" class="<?= $labelClasses ?>">
                        Sale Date &amp; Time
                    </label>

                    <input
                        id="sold-at"
                        type="datetime-local"
                        name="sold_at"
                        max="<?= date('Y-m-d\TH:i') ?>"
                        value="<?= htmlspecialchars($soldAtValue) ?>"
                        class="<?= $inputClasses ?>"
                        required>
                </div>

                <div>
                    <label for="notes" class="<?= $labelClasses ?>">
                        Admin Notes
                    </label>

                    <textarea
                        id="notes"
                        name="notes"
                        rows="3"
                        class="<?= $textareaClasses ?>"><?= old($values, 'notes', (string)$sale->notes) ?></textarea>
                </div>

            </div>

            <button
                type="submit"
                class="w-full rounded-md bg-primary px-5 py-2.5 text-sm font-medium text-white transition hover:bg-primary-hover sm:w-auto">
                Save Changes
            </button>

        </form>

    </div>

</section>
