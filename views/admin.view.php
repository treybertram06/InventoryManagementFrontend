<!doctype html>
<html>
<?php require "partials/head.view.php"; ?>
<body class="min-h-screen bg-background text-text dark:bg-background-dark dark:text-white">
<?php require "partials/navbar.view.php"; ?>

<?php
/**
 * @var Models\User $currentUser
 * @var Models\User[] $users
 * @var int $adminCount
 */
?>

<div class="mx-auto w-full max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

    <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-text dark:text-white">Admin Dashboard</h1>
            <p class="mt-1 text-sm text-text-muted dark:text-white/70">Manage users and device data.</p>
        </div>
        <a href="/sales-history" class="rounded-md bg-surface-muted px-5 py-2.5 text-sm font-medium text-text transition hover:bg-surface-muted/75 dark:bg-surface-muted-dark dark:text-white dark:hover:text-white/75">
            Sales History
        </a>
    </div>

    <div>
        <?php require "partials/adminUsersTable.view.php"; ?>
    </div>

</div>

</body>
</html>
