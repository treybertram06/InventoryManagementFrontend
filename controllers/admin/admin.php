<?php

$user = Core\Common::require_admin($db);

view('admin.view.php', array_merge(
    ['currentUser' => $user],
    Core\Common::admin_dashboard_data($db)
));
