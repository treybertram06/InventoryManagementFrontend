<?php

function handle_login(Core\Database $db): void {
    $identifier = $_POST['identifier'];
    $password = $_POST['password'];
    $isEmail = Core\Validator::validate_email($identifier);

    $errors = [];

    $credentials = $db->get_login_credentials($identifier, $isEmail);
    $hash = $credentials['password_hash'] ?? '';
    $credentialsValid = password_verify($password, $hash) && $credentials !== null;

    if (!$credentialsValid) {
        $errors[] = "Invalid credentials.";
    }

    if (!empty($errors)) {
        view('login.view.php', ['errors' => $errors, 'identifier' => $identifier]);
        return;
    }

    session_regenerate_id(true);
    $_SESSION['user_id'] = $credentials['id'];

    header('Location: /');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    view('login.view.php');
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    handle_login($db);
}