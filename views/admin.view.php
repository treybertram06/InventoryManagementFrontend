<!doctype html>
<html>
<?php require "partials/head.view.php"; ?>
<body class="min-h-screen bg-background text-text dark:bg-background-dark dark:text-white">
<?php require "partials/navbar.view.php"; ?>

<?php
/**
 * @var Models\User $currentUser
 * @var Models\User[] $users
 * @var Models\Device[] $soldDevices
 * @var int $adminCount
 */
?>

<div class="mx-auto w-full max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-text dark:text-white">Admin Dashboard</h1>
        <p class="mt-1 text-sm text-text-muted dark:text-white/70">Manage users, device data, and sales.</p>
    </div>

    <div class="mb-8">
        <?php require "partials/adminUsersTable.view.php"; ?>
    </div>

    <div>
        <?php require "partials/adminSoldItemsTable.view.php"; ?>
    </div>

</div>

</body>
</html>
