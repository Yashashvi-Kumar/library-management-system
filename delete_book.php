<?php
/*
 * delete_book.php  -  remove a book (the "D" in CRUD = Delete).
 * Only accepts POST requests (the form on dashboard.php).
 */
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);

    $stmt = $conn->prepare("DELETE FROM books WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        set_flash("success", "Book deleted.");
    } else {
        set_flash("error", "Book not found.");
    }
    $stmt->close();
}

header("Location: dashboard.php");
exit;
