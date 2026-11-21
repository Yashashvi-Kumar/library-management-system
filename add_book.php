<?php
/*
 * add_book.php  -  add a new book (the "C" in CRUD = Create).
 */
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_login();

$errors = [];
$title = $author = $category = "";
$year = "";
$status = "Available";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title    = trim($_POST['title'] ?? '');
    $author   = trim($_POST['author'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $year     = trim($_POST['publish_year'] ?? '');
    $status   = $_POST['status'] ?? 'Available';

    $max_year = (int)date("Y") + 1;

    if ($title === "" || strlen($title) > 150)   { $errors[] = "Title is required (max 150 characters)."; }
    if ($author === "" || strlen($author) > 100) { $errors[] = "Author is required (max 100 characters)."; }
    if ($category === "")                        { $errors[] = "Please choose a category."; }
    if (!ctype_digit($year) || (int)$year < 1000 || (int)$year > $max_year) {
        $errors[] = "Enter a valid year between 1000 and $max_year.";
    }
    if (!in_array($status, ['Available', 'Issued'], true)) { $errors[] = "Invalid status."; }

    if (!$errors) {
        $year_int = (int)$year;
        $user_id  = $_SESSION['user_id'];

        $stmt = $conn->prepare(
            "INSERT INTO books (title, author, category, publish_year, status, added_by)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        // s = string, i = integer
        $stmt->bind_param("sssisi", $title, $author, $category, $year_int, $status, $user_id);
        $stmt->execute();
        $stmt->close();

        set_flash("success", "Book added successfully.");
        header("Location: dashboard.php");
        exit;
    }
}

$categories = ["Computer Science", "Programming", "Databases", "Mathematics", "Biography", "Fiction", "Science", "Other"];

$page_title = "Add Book";
include __DIR__ . '/includes/header.php';
?>

<div class="card card-narrow">
    <h1>Add New Book</h1>

    <?php foreach ($errors as $err): ?>
        <div class="alert alert-error"><?= e($err) ?></div>
    <?php endforeach; ?>

    <form method="post" action="add_book.php" id="bookForm" novalidate>
        <label for="title">Title</label>
        <input type="text" id="title" name="title" value="<?= e($title) ?>">

        <label for="author">Author</label>
        <input type="text" id="author" name="author" value="<?= e($author) ?>">

        <label for="category">Category</label>
        <select id="category" name="category">
            <option value="">-- Select category --</option>
            <?php foreach ($categories as $c): ?>
                <option value="<?= e($c) ?>" <?= $category === $c ? 'selected' : '' ?>><?= e($c) ?></option>
            <?php endforeach; ?>
        </select>

        <label for="publish_year">Publish Year</label>
        <input type="number" id="publish_year" name="publish_year" value="<?= e($year) ?>" placeholder="e.g. 2020">

        <label for="status">Status</label>
        <select id="status" name="status">
            <option value="Available" <?= $status === 'Available' ? 'selected' : '' ?>>Available</option>
            <option value="Issued"    <?= $status === 'Issued' ? 'selected' : '' ?>>Issued</option>
        </select>

        <p class="js-error" id="formError"></p>
        <button type="submit" class="btn btn-block">Save Book</button>
        <a class="btn btn-block btn-outline" href="dashboard.php">Cancel</a>
    </form>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
