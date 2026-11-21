<?php
/* logout.php - destroy the session and go back to login. */
require_once __DIR__ . '/includes/auth.php';

$_SESSION = [];          // empty all session data
session_destroy();       // delete the session on the server

session_start();         // start a fresh one just to show a message
set_flash("success", "You have been logged out.");
header("Location: login.php");
exit;
