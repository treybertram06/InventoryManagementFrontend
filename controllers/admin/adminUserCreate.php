<?php

$user = Core\Common::require_admin($db);

$username = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$role = $_POST['role'] ?? '';

$validRoles = array_map(fn($case) => $case->value, Models\UserRole::cases());

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
if (!in_array($role, $validRoles, true)) {
    $errors[] = "Please select a valid role.";
}
if ($db->does_user_exist($username)) {
    $errors[] = "This username has already been taken.";
}
if ($db->does_user_exist_by_email($email)) {
    $errors[] = "This email has already been taken.";
}

if (!empty($errors)) {
    view('admin.view.php', array_merge(
        ['currentUser' => $user, 'createUserErrors' => $errors, 'createUserValues' => $_POST],
        Core\Common::admin_dashboard_data($db)
    ));
    return;
}

$hash = password_hash($password, PASSWORD_DEFAULT);
$db->create_user($username, $email, $hash, $role);

header('Location: /admin');
exit;
