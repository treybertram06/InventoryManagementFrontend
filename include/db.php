<?php

require_once 'include/common.php';

$host = "localhost";
$dbName = "PhoneInventory";
$user = "root";
$pass = "password";
$charset = "utf8mb4";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

$dsn = "mysql:host=$host; dbname=$dbName; charset=$charset;";

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    log_info("Connected to database successfully!");
} catch (PDOException $e) {
    log_error("Connection failed: \n    DSN: " . $dsn . "\n    Error: " . $e->getMessage());
}
