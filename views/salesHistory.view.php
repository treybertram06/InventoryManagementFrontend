<!doctype html>
<html>
<?php require "partials/head.view.php"; ?>
<body class="min-h-screen bg-background text-text dark:bg-background-dark dark:text-white">
<?php require "partials/navbar.view.php"; ?>

<?php
/** @var Models\Sale[] $sales */

if (!empty($sales)) {
    echo '<div class="mx-auto w-full px-4 py-4 sm:px-6 lg:px-8">';
        require "partials/salesTable.view.php";
    echo '</div>';
} else {
    echo '<div class="flex min-h-[calc(100vh-4rem)] items-center justify-center">';
        require "partials/noDataFound.view.php";
    echo '</div>';
}
?>

</body>
</html>