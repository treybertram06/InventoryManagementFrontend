<?php

if (!Core\Common::current_user($db)) {
    header('Location: /login');
    exit;
}

view('inventory.view.php', ['db' => $db]);