<!doctype html>
<html>
<?php require "partials/head.view.php"; ?>
<body>
<?php require "partials/navbar.view.php"; ?>

<?php
/** @var Database $db */ // Makes my IDE happy

if ($devices = $db->get_all_devices()) {
    echo '<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">';
        require "partials/itemTable.view.php";
    echo '</div>';
} else {
    echo '<div class="flex min-h-[calc(100vh-4rem)] items-center justify-center">';
        require "partials/noDataFound.view.php";
    echo '</div>';
}
?>

</body>
</html>