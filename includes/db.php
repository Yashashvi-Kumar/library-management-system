<?php
/*
 * db.php  -  connects PHP to the MySQL database.
 * Every other PHP file does:  require 'includes/db.php';
 * and then uses the variable $conn to run queries.
 *
 * The real connection settings live in config.php (NOT uploaded to GitHub).
 * First time setup: copy config.example.php to config.php and edit the values.
 */
$config_file = __DIR__ . "/config.php";

if (!file_exists($config_file)) {
    die("config.php not found. Copy <b>includes/config.example.php</b> to "
        . "<b>includes/config.php</b> and put your MySQL details in it.");
}

require $config_file;   // defines $db_host, $db_user, $db_pass, $db_name, $db_port

// Make mysqli throw errors we can catch instead of silent warnings
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name, $db_port);
    $conn->set_charset("utf8mb4");   // support all characters
} catch (mysqli_sql_exception $e) {
    die("Database connection failed. Did you import database.sql and start MySQL? <br>Details: "
        . htmlspecialchars($e->getMessage()));
}
