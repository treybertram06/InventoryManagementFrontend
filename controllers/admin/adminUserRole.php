<?php

$user = Core\Common::require_admin($db);

$userId = (int)($_POST['user_id'] ?? 0);
$role = $_POST['role'] ?? '';

$validRoles = array_map(fn($case) => $case->value, Models\UserRole::cases());

$errors = [];

if (!in_array($role, $validRoles, true)) {
    $errors[] = "Please select a valid role.";
}

$target = $userId ? $db->get_user_by_id($userId) : null;
if (!$target) {
    $errors[] = "That user could not be found.";
}

if (empty($errors) && $target->role === Models\UserRole::Admin && $role !== Models\UserRole::Admin->value) {
    $allUsers = $db->get_all_users() ?? [];
    $adminCount = count(array_filter($allUsers, fn($u) => $u->role === Models\UserRole::Admin));
    if ($adminCount <= 1) {
        $errors[] = "Cannot remove admin status from the last remaining admin.";
    }
}

if (!empty($errors)) {
    view('admin.view.php', array_merge(
        ['currentUser' => $user, 'userErrors' => $errors],
        Core\Common::admin_dashboard_data($db)
    ));
    return;
}

$db->update_user_role($userId, $role);

header('Location: /admin');
exit;
