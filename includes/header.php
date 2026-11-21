<?php
/*
 * header.php  -  top part of every page (HTML head + navigation bar).
 * Set $page_title before including this file.
 */
require_once __DIR__ . '/auth.php';
if (!isset($page_title)) { $page_title = "Library System"; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($page_title) ?> | Library Management</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<nav class="navbar">
    <a class="brand" href="dashboard.php">📚 Library Management</a>
    <div class="nav-links">
        <?php if (is_logged_in()): ?>
            <a href="dashboard.php">Books</a>
            <a href="add_book.php">Add Book</a>
            <a href="view_xml.php">XML View</a>
            <span class="nav-user">👤 <?= e($_SESSION['username']) ?></span>
            <a class="btn btn-small btn-outline" href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        <?php endif; ?>
    </div>
</nav>

<main class="container">
<?php show_flash(); ?>
