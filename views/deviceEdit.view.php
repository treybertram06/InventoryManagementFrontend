<!doctype html>
<html>
<?php require "partials/head.view.php"; ?>

<body class="min-h-screen bg-background text-text dark:bg-background-dark dark:text-white">

<?php require "partials/navbar.view.php"; ?>

<div class="mx-auto max-w-4xl px-4 py-6">

    <h1 class="text-3xl font-bold mb-6">
        Edit Device
    </h1>

    <form method="POST" action="/device-edit">

        <input
            type="hidden"
            name="serial_number"
            value="<?= htmlspecialchars($device->serialNumber) ?>">

        <div class="mb-4">
            <label>Friendly Name</label>

            <input
                type="text"
                name="friendly_name"
                value="<?= htmlspecialchars($device->friendlyName) ?>"
                class="w-full border rounded p-2">
        </div>

        <div class="mb-4">
            <label>Product Type</label>

            <input
                type="text"
                name="product_type"
                value="<?= htmlspecialchars($device->productType) ?>"
                class="w-full border rounded p-2">
        </div>

        <div class="mb-4">
            <label>Model Number</label>

            <input
                type="text"
                name="model_number"
                value="<?= htmlspecialchars($device->modelNumber ?? '') ?>"
                class="w-full border rounded p-2">
        </div>

        <div class="mb-4">
            <label>Color</label>

            <input
                type="text"
                name="color"
                value="<?= htmlspecialchars($device->color ?? '') ?>"
                class="w-full border rounded p-2">
        </div>

        <div class="mb-4">
            <label>Region Code</label>

            <input
                type="text"
                name="region_code"
                value="<?= htmlspecialchars($device->regionCode ?? '') ?>"
                class="w-full border rounded p-2">
        </div>

        <div class="mb-4">
            <label>Storage (GB)</label>

            <input
                type="number"
                step="0.01"
                name="storage_gb"
                value="<?= htmlspecialchars($device->storageGb ?? '') ?>"
                class="w-full border rounded p-2">
        </div>

        <div class="mb-4">
            <label>Known Issues</label>

            <textarea
                name="known_issues"
                class="w-full border rounded p-2"
                rows="4"><?= htmlspecialchars($device->knownIssues ?? '') ?></textarea>
        </div>

        <button
            class="rounded bg-blue-600 px-5 py-2 text-white">
            Save Changes
        </button>

    </form>

</div>

</body>
</html>