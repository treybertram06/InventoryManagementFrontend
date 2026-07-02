<!doctype html>
<html>
<?php require "partials/head.view.php"; ?>
<body>
<?php require "partials/navbar.view.php"; ?>

<?php
/** @var Database $db */ // Makes my IDE happy

if ($devices = $db->get_all_devices()) {
    foreach ($devices as $device) {
        echo $device->friendlyName . '<br>';
    }
} else {
    echo '<div class="flex min-h-[calc(100vh-4rem)] items-center justify-center">';
        require "partials/noDataFound.view.php";
    echo '</div>';
}
?>

</body>
</html>