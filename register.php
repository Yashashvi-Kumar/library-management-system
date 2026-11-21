<?php
/*
 * register.php  -  create a new user account.
 * Flow: show form -> user submits (POST) -> PHP validates -> INSERT into MySQL.
 */
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

if (is_logged_in()) { header("Location: dashboard.php"); exit; }

$errors   = [];
$username = "";
$email    = "";

// Run this block only when the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // trim() removes spaces at the start/end
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    // ---- Server-side validation (never trust only JavaScript) ----
    if (strlen($username) < 3 || strlen($username) > 50) {
        $errors[] = "Username must be 3 to 50 characters.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }
    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }
    if ($password !== $confirm) {
        $errors[] = "Passwords do not match.";
    }

    // ---- Check if username or email already exists ----
    if (!$errors) {
        // "?" are placeholders. This is a PREPARED STATEMENT, which blocks SQL injection.
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);   // s = string
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "That username or email is already registered.";
        }
        $stmt->close();
    }

    // ---- Everything OK: save the user ----
    if (!$errors) {
        // password_hash() turns the password into a one-way scrambled string
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $hash);
        $stmt->execute();
        $stmt->close();

        set_flash("success", "Account created! Please log in.");
        header("Location: login.php");
        exit;
    }
}

$page_title = "Register";
include __DIR__ . '/includes/header.php';
?>

<div class="card card-narrow">
    <h1>Create Account</h1>

    <?php foreach ($errors as $err): ?>
        <div class="alert alert-error"><?= e($err) ?></div>
    <?php endforeach; ?>

    <!-- novalidate: we use our own JavaScript validation (see js/script.js) -->
    <form method="post" action="register.php" id="registerForm" novalidate>
        <label for="username">Username</label>
        <input type="text" id="username" name="username" value="<?= e($username) ?>" placeholder="e.g. yashashvi">

        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?= e($email) ?>" placeholder="you@example.com">

        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="At least 6 characters">

        <label for="confirm_password">Confirm Password</label>
        <input type="password" id="confirm_password" name="confirm_password">

        <p class="js-error" id="formError"></p>
        <button type="submit" class="btn btn-block">Register</button>
    </form>

    <p class="muted center">Already have an account? <a href="login.php">Login</a></p>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
