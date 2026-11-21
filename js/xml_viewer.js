/*
 * xml_viewer.js  -  reads books.xml and shows it as a table.
 *
 * Steps:
 *   1. fetch()        -> download the XML file as text
 *   2. DOMParser      -> convert that text into an XML document object
 *   3. getElementsByTagName() -> walk through every <book>
 *   4. build table rows with the values
 */
document.addEventListener("DOMContentLoaded", function () {
    var tbody   = document.querySelector("#xmlTable tbody");
    var rawBox  = document.getElementById("rawXml");
    var infoBox = document.getElementById("xmlInfo");

    // "?t=" + time makes the URL unique so the browser never shows an old cached copy
    fetch("books.xml?t=" + Date.now())
        .then(function (response) {
            if (!response.ok) { throw new Error("Could not load books.xml"); }
            return response.text();                // the XML as plain text
        })
        .then(function (xmlText) {
            // Show the raw XML text
            rawBox.textContent = xmlText;

            // Turn the text into an XML DOM
            var parser = new DOMParser();
            var xml    = parser.parseFromString(xmlText, "application/xml");

            // If the XML is not well-formed, the browser inserts a <parsererror> tag
            if (xml.getElementsByTagName("parsererror").length > 0) {
                throw new Error("books.xml is not valid XML.");
            }

            var books = xml.getElementsByTagName("book");
            var root  = xml.documentElement;       // the <library> tag
            infoBox.textContent = books.length + " book(s) found. Exported on: " +
                                  root.getAttribute("exported_on");

            // Helper: get the text inside a child tag, e.g. <title>...</title>
            function textOf(bookNode, tagName) {
                var node = bookNode.getElementsByTagName(tagName)[0];
                return node ? node.textContent : "";
            }

            // Build one table row per <book>
            for (var i = 0; i < books.length; i++) {
                var b  = books[i];
                var tr = document.createElement("tr");

                var cells = [
                    b.getAttribute("id"),          // attribute value
                    textOf(b, "title"),
                    textOf(b, "author"),
                    textOf(b, "category"),
                    textOf(b, "publish_year"),
                    textOf(b, "status")
                ];

                cells.forEach(function (value) {
                    var td = document.createElement("td");
                    td.textContent = value;        // textContent is safe (no HTML injection)
                    tr.appendChild(td);
                });
                tbody.appendChild(tr);
            }
        })
        .catch(function (error) {
            rawBox.textContent = "Error: " + error.message;
        });
});
