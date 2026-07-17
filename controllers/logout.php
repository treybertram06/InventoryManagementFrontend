<?php

session_unset();
session_destroy();

if (ini_get('session.use_cookies')) { // If the browser still has a the session cookie stored locally, replace it with one that is explicitly invalid
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
}

header('Location: /login');
exit;