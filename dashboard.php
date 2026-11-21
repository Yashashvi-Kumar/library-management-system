<?php
/*
 * dashboard.php  -  list all books (the "R" in CRUD = Read).
 * The search box filters rows instantly using JavaScript (js/script.js).
 */
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_login();

// Get every book, newest first
$result = $conn->query("SELECT id, title, author, category, publish_year, status FROM books ORDER BY id DESC");

// Count totals for the stat cards
$total     = $result->num_rows;
$available = 0;
$issued    = 0;
$books     = [];
while ($row = $result->fetch_assoc()) {
    $books[] = $row;
    if ($row['status'] === 'Available') { $available++; } else { $issued++; }
}

$page_title = "Books";
include __DIR__ . '/includes/header.php';
?>

<h1>Book Collection</h1>

<div class="stats">
    <div class="stat"><span class="stat-num"><?= $total ?></span> Total Books</div>
    <div class="stat stat-green"><span class="stat-num"><?= $available ?></span> Available</div>
    <div class="stat stat-orange"><span class="stat-num"><?= $issued ?></span> Issued</div>
</div>

<div class="toolbar">
    <input type="text" id="searchBox" placeholder="🔍 Search by title, author or category...">
    <a class="btn" href="add_book.php">+ Add Book</a>
</div>

<div class="table-wrap">
<table id="booksTable">
    <thead>
        <tr>
            <th>#</th><th>Title</th><th>Author</th><th>Category</th>
            <th>Year</th><th>Status</th><th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php if (count($books) === 0): ?>
        <tr><td colspan="7" class="center muted">No books yet. Click "Add Book".</td></tr>
    <?php endif; ?>

    <?php foreach ($books as $b): ?>
        <tr>
            <td><?= (int)$b['id'] ?></td>
            <td><?= e($b['title']) ?></td>
            <td><?= e($b['author']) ?></td>
            <td><?= e($b['category']) ?></td>
            <td><?= (int)$b['publish_year'] ?></td>
            <td>
                <span class="badge <?= $b['status'] === 'Available' ? 'badge-green' : 'badge-orange' ?>">
                    <?= e($b['status']) ?>
                </span>
            </td>
            <td class="actions">
                <a class="btn btn-small" href="edit_book.php?id=<?= (int)$b['id'] ?>">Edit</a>

                <!-- Delete uses POST (safer than a link). JS asks for confirmation. -->
                <form method="post" action="delete_book.php" class="inline-form js-delete-form">
                    <input type="hidden" name="id" value="<?= (int)$b['id'] ?>">
                    <button type="submit" class="btn btn-small btn-danger">Delete</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>

<p class="muted" id="noResults" style="display:none;">No matching books found.</p>

<?php include __DIR__ . '/includes/footer.php'; ?>
