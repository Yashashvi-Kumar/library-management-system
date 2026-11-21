<?php
/*
 * export_xml.php  -  reads all books from MySQL and writes them into books.xml
 *
 * XML = eXtensible Markup Language. It stores DATA inside tags that we name
 * ourselves. Result looks like:
 *
 *   <library>
 *      <book id="1">
 *          <title>Clean Code</title>
 *          <author>Robert C. Martin</author>
 *          ...
 *      </book>
 *   </library>
 */
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_login();

$result = $conn->query("SELECT id, title, author, category, publish_year, status FROM books ORDER BY id");

// DOMDocument is PHP's built-in tool for building XML
$dom = new DOMDocument("1.0", "UTF-8");
$dom->formatOutput = true;               // nice indentation

// Root element: every XML file needs exactly ONE root
$library = $dom->createElement("library");
$library->setAttribute("exported_on", date("Y-m-d H:i:s"));
$dom->appendChild($library);

while ($row = $result->fetch_assoc()) {
    $book = $dom->createElement("book");
    $book->setAttribute("id", $row['id']);     // attribute: <book id="1">

    // createTextNode() automatically escapes characters like & < >
    foreach (['title', 'author', 'category', 'publish_year', 'status'] as $field) {
        $child = $dom->createElement($field);
        $child->appendChild($dom->createTextNode($row[$field]));
        $book->appendChild($child);
    }
    $library->appendChild($book);
}

// Save the XML file next to this script
$dom->save(__DIR__ . "/books.xml");

set_flash("success", "books.xml was generated from the database.");
header("Location: view_xml.php");
exit;
