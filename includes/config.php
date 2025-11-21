<?php
// includes/config.php
session_start();

define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'marketmm');
define('DB_USER', 'root');
define('DB_PASS', '');


define('BASE_URL', 'http://localhost/marketmm/public');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (Exception $e) {
    die("DB Connection failed: " . $e->getMessage());
}
