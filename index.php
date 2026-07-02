<?php

const BASE_PATH = __DIR__ . '/';
const BASE_URL = 'http://localhost/';
define('CONFIG', file_exists(BASE_PATH . 'core/config.php')
    ? require BASE_PATH . 'core/config.php' : require BASE_PATH . 'core/config.example.php');
require_once BASE_PATH . 'core/common.php';
require_once BASE_PATH . 'core/db.php';

// DB object is created here so it's globally accessible
$db = new Database();

require_once BASE_PATH . 'core/router.php';
