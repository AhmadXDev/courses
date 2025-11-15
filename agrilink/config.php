<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_PORT', 3307);
define('DB_NAME', 'agrilink');
define('DB_USER', 'root');
define('DB_PASS', '');

// Application paths
define('BASE_URL', 'http://' . $_SERVER['HTTP_HOST'] . '/AgriLink' . '/');

define('ROOT_PATH', __DIR__);

// Error reporting (set to 0 in production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Session settings
session_start();

// Time zone
date_default_timezone_set('UTC');

// Database connection using PDO
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
