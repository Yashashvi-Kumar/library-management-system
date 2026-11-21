<?php
/*
 * auth.php  -  session + helper functions used on every page.
 *
 * A SESSION lets the server remember a user between page loads.
 * After login we store the user's id and name in $_SESSION.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Is someone logged in?
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

// Put this at the top of any page that needs login.
// If not logged in, send them to login.php and stop running the page.
function require_login() {
    if (!is_logged_in()) {
        header("Location: login.php");
        exit;
    }
}

// Escape text before printing it in HTML.
// This prevents XSS (someone typing <script> into a form field).
function e($text) {
    return htmlspecialchars((string)$text, ENT_QUOTES, 'UTF-8');
}

// Flash messages: store a message now, show it on the next page, then delete it.
function set_flash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function show_flash() {
    if (isset($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        echo '<div class="alert alert-' . e($f['type']) . '">' . e($f['message']) . '</div>';
    }
}
