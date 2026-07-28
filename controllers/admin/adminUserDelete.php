<?php

$user = Core\Common::require_admin($db);

$userId = (int)($_POST['user_id'] ?? 0);

$errors = [];

if ($userId === $user->ID) {
    $errors[] = "You cannot delete your own account.";
}

$target = $userId ? $db->get_user_by_id($userId) : null;
if (!$target) {
    $errors[] = "That user could not be found.";
}

if (empty($errors) && $target->role === Models\UserRole::Admin) {
    $allUsers = $db->get_all_users() ?? [];
    $adminCount = count(array_filter($allUsers, fn($u) => $u->role === Models\UserRole::Admin));
    if ($adminCount <= 1) {
        $errors[] = "Cannot delete the last remaining admin.";
    }
}

if (!empty($errors)) {
    view('admin.view.php', array_merge(
        ['currentUser' => $user, 'userErrors' => $errors],
        Core\Common::admin_dashboard_data($db)
    ));
    return;
}

$db->delete_user($userId);

header('Location: /admin');
exit;
