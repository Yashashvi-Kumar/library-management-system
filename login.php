<?php
/*
 * login.php  -  check username + password and start a session.
 */
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

if (is_logged_in()) { header("Location: dashboard.php"); exit; }

$error    = "";
$username = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === "" || $password === "") {
        $error = "Please enter both username and password.";
    } else {
        // Find the user by username
        $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user   = $result->fetch_assoc();   // one row as an array, or null
        $stmt->close();

        // password_verify() compares the typed password with the stored hash
        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);    // new session id = safer
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Invalid username or password.";
        }
    }
}

$page_title = "Login";
include __DIR__ . '/includes/header.php';
?>

<div class="card card-narrow">
    <h1>Login</h1>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post" action="login.php" id="loginForm" novalidate>
        <label for="username">Username</label>
        <input type="text" id="username" name="username" value="<?= e($username) ?>" autofocus>

        <label for="password">Password</label>
        <input type="password" id="password" name="password">

        <p class="js-error" id="formError"></p>
        <button type="submit" class="btn btn-block">Login</button>
    </form>

    <p class="muted center">New here? <a href="register.php">Create an account</a></p>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
