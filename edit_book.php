<?php
/*
 * edit_book.php  -  change an existing book (the "U" in CRUD = Update).
 * URL looks like: edit_book.php?id=3   ($_GET['id'] gives us the 3)
 */
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_login();

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);

// Load the current data of this book
$stmt = $conn->prepare("SELECT id, title, author, category, publish_year, status FROM books WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$book = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$book) {
    set_flash("error", "Book not found.");
    header("Location: dashboard.php");
    exit;
}

$errors = [];
$categories = ["Computer Science", "Programming", "Databases", "Mathematics", "Biography", "Fiction", "Science", "Other"];

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
        $stmt = $conn->prepare(
            "UPDATE books SET title = ?, author = ?, category = ?, publish_year = ?, status = ? WHERE id = ?"
        );
        $stmt->bind_param("sssisi", $title, $author, $category, $year_int, $status, $id);
        $stmt->execute();
        $stmt->close();

        set_flash("success", "Book updated successfully.");
        header("Location: dashboard.php");
        exit;
    }

    // Validation failed: keep what the user typed in the form
    $book = ['id' => $id, 'title' => $title, 'author' => $author,
             'category' => $category, 'publish_year' => $year, 'status' => $status];
}

$page_title = "Edit Book";
include __DIR__ . '/includes/header.php';
?>

<div class="card card-narrow">
    <h1>Edit Book</h1>

    <?php foreach ($errors as $err): ?>
        <div class="alert alert-error"><?= e($err) ?></div>
    <?php endforeach; ?>

    <form method="post" action="edit_book.php?id=<?= (int)$book['id'] ?>" id="bookForm" novalidate>
        <input type="hidden" name="id" value="<?= (int)$book['id'] ?>">

        <label for="title">Title</label>
        <input type="text" id="title" name="title" value="<?= e($book['title']) ?>">

        <label for="author">Author</label>
        <input type="text" id="author" name="author" value="<?= e($book['author']) ?>">

        <label for="category">Category</label>
        <select id="category" name="category">
            <option value="">-- Select category --</option>
            <?php foreach ($categories as $c): ?>
                <option value="<?= e($c) ?>" <?= $book['category'] === $c ? 'selected' : '' ?>><?= e($c) ?></option>
            <?php endforeach; ?>
        </select>

        <label for="publish_year">Publish Year</label>
        <input type="number" id="publish_year" name="publish_year" value="<?= e($book['publish_year']) ?>">

        <label for="status">Status</label>
        <select id="status" name="status">
            <option value="Available" <?= $book['status'] === 'Available' ? 'selected' : '' ?>>Available</option>
            <option value="Issued"    <?= $book['status'] === 'Issued' ? 'selected' : '' ?>>Issued</option>
        </select>

        <p class="js-error" id="formError"></p>
        <button type="submit" class="btn btn-block">Update Book</button>
        <a class="btn btn-block btn-outline" href="dashboard.php">Cancel</a>
    </form>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
