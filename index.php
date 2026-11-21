<?php
/* index.php - entry point. Sends the visitor to the right page. */
require_once __DIR__ . '/includes/auth.php';

if (is_logged_in()) {
    header("Location: dashboard.php");
} else {
    header("Location: login.php");
}
exit;
