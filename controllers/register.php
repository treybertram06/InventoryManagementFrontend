<?php
function handle_registration(Core\Database $db): void {
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];

    $errors = [];

    if (!Core\Validator::validate_username($username)) {
        $errors[] = "Username must be 3-32 characters and start with a letter.";
    }
    if (!Core\Validator::validate_email($email)) {
        $errors[] = "Please enter a valid email address.";
    }
    if (!Core\Validator::validate_password($password)) {
        $errors[] = "Password must be at least 8 characters and include an uppercase letter, a lowercase letter, and a digit.";
    }

    if ($db->does_user_exist($username)) {
        $errors[] = "This username has already been taken.";
    }
    if ($db->does_user_exist_by_email($email)) {
        // redirect to login page
        $errors[] = "This email has already been taken.";

    }


    if (!empty($errors)) {
        view('register.view.php', ['errors' => $errors, 'username' => $username, 'email' => $email]);
        return;
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);

    if (!$db->create_user($username, $email, $hash)) {
        view('register.view.php', ['errors' => ["That username or email is already taken."], 'username' => $username, 'email' => $email]);
        return;
    }

    header('Location: /login');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    view('register.view.php');
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    handle_registration($db);
}
