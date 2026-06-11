<!doctype html>
<html>
<?php require "partials/head.view.php"; ?>
<body>
<?php require "partials/navbar.view.php"; ?>

<?php
/** @var Database $db */ // Makes my IDE happy

if ($users = $db->get_all_users()) {
    foreach ($users as $user) {
        echo $user->get_name() . '<br>';
    }
} else {
    echo '<div class="flex min-h-[calc(100vh-4rem)] items-center justify-center">';
        require "partials/noDataFound.view.php";
    echo '</div>';
}
?>

</body>
</html>