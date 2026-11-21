<?php
/*
 * view_xml.php  -  shows the XML file.
 * PHP only prints the page shell. JavaScript (js/xml_viewer.js) then
 * downloads books.xml with fetch() and builds the table from it.
 */
require_once __DIR__ . '/includes/auth.php';
require_login();

$xml_exists = file_exists(__DIR__ . '/books.xml');

$page_title = "XML View";
include __DIR__ . '/includes/header.php';
?>

<h1>Books as XML</h1>
<p class="muted">
    Click <b>Generate XML</b> to export the MySQL data into <code>books.xml</code> (PHP).
    The table below is built by reading that XML file with JavaScript.
</p>

<div class="toolbar">
    <a class="btn" href="export_xml.php">⚙ Generate XML from Database</a>
    <?php if ($xml_exists): ?>
        <a class="btn btn-outline" href="books.xml" target="_blank">Open books.xml</a>
    <?php endif; ?>
</div>

<?php if (!$xml_exists): ?>
    <div class="alert alert-error">books.xml does not exist yet. Click "Generate XML" first.</div>
<?php else: ?>
    <h2>Parsed table (JavaScript + XML)</h2>
    <p class="muted" id="xmlInfo"></p>
    <div class="table-wrap">
        <table id="xmlTable">
            <thead>
                <tr><th>ID</th><th>Title</th><th>Author</th><th>Category</th><th>Year</th><th>Status</th></tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <h2>Raw XML</h2>
    <pre id="rawXml" class="code-box">Loading...</pre>

    <script src="js/xml_viewer.js"></script>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
