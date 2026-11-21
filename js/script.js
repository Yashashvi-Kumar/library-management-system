/*
 * script.js  -  JavaScript used on all pages.
 *   1. Form validation (register, login, add/edit book)
 *   2. Live search on the books table
 *   3. "Are you sure?" confirmation before deleting
 *
 * JavaScript runs in the BROWSER, so it gives instant feedback.
 * PHP re-checks everything on the SERVER because JS can be bypassed.
 */

// Run after the HTML has fully loaded
document.addEventListener("DOMContentLoaded", function () {

    /* ---------- Helper functions ---------- */

    // Show an error message under the form and highlight bad fields
    function showError(form, message, fields) {
        var errorBox = form.querySelector("#formError");
        if (errorBox) { errorBox.textContent = message; }
        form.querySelectorAll(".invalid").forEach(function (el) { el.classList.remove("invalid"); });
        (fields || []).forEach(function (el) { el.classList.add("invalid"); });
        if (fields && fields.length) { fields[0].focus(); }
    }

    // Simple email check using a regular expression
    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    /* ---------- 1. Register form ---------- */
    var registerForm = document.getElementById("registerForm");
    if (registerForm) {
        registerForm.addEventListener("submit", function (event) {
            var username = registerForm.username;
            var email    = registerForm.email;
            var password = registerForm.password;
            var confirm  = registerForm.confirm_password;

            if (username.value.trim().length < 3) {
                event.preventDefault();               // stop the form from submitting
                showError(registerForm, "Username must be at least 3 characters.", [username]);
            } else if (!isValidEmail(email.value.trim())) {
                event.preventDefault();
                showError(registerForm, "Enter a valid email address.", [email]);
            } else if (password.value.length < 6) {
                event.preventDefault();
                showError(registerForm, "Password must be at least 6 characters.", [password]);
            } else if (password.value !== confirm.value) {
                event.preventDefault();
                showError(registerForm, "Passwords do not match.", [confirm]);
            }
        });
    }

    /* ---------- 2. Login form ---------- */
    var loginForm = document.getElementById("loginForm");
    if (loginForm) {
        loginForm.addEventListener("submit", function (event) {
            var username = loginForm.username;
            var password = loginForm.password;

            if (username.value.trim() === "" || password.value === "") {
                event.preventDefault();
                var bad = [];
                if (username.value.trim() === "") { bad.push(username); }
                if (password.value === "")        { bad.push(password); }
                showError(loginForm, "Please fill in both fields.", bad);
            }
        });
    }

    /* ---------- 3. Add / Edit book form ---------- */
    var bookForm = document.getElementById("bookForm");
    if (bookForm) {
        bookForm.addEventListener("submit", function (event) {
            var title    = bookForm.elements["title"];   // elements[] avoids clashing with form.title
            var author   = bookForm.author;
            var category = bookForm.category;
            var year     = bookForm.publish_year;
            var maxYear  = new Date().getFullYear() + 1;

            if (title.value.trim() === "") {
                event.preventDefault();
                showError(bookForm, "Title is required.", [title]);
            } else if (author.value.trim() === "") {
                event.preventDefault();
                showError(bookForm, "Author is required.", [author]);
            } else if (category.value === "") {
                event.preventDefault();
                showError(bookForm, "Please choose a category.", [category]);
            } else if (year.value < 1000 || year.value > maxYear || year.value === "") {
                event.preventDefault();
                showError(bookForm, "Enter a year between 1000 and " + maxYear + ".", [year]);
            }
        });
    }

    /* ---------- 4. Live search on the dashboard ---------- */
    var searchBox = document.getElementById("searchBox");
    var table     = document.getElementById("booksTable");
    if (searchBox && table) {
        var noResults = document.getElementById("noResults");

        searchBox.addEventListener("input", function () {
            var term = searchBox.value.toLowerCase().trim();
            var rows = table.querySelectorAll("tbody tr");
            var visible = 0;

            rows.forEach(function (row) {
                // Compare the search text with all text inside the row
                var match = row.textContent.toLowerCase().indexOf(term) !== -1;
                row.style.display = match ? "" : "none";
                if (match) { visible++; }
            });

            if (noResults) { noResults.style.display = (visible === 0) ? "block" : "none"; }
        });
    }

    /* ---------- 5. Confirm before delete ---------- */
    document.querySelectorAll(".js-delete-form").forEach(function (form) {
        form.addEventListener("submit", function (event) {
            if (!confirm("Are you sure you want to delete this book?")) {
                event.preventDefault();               // cancel the delete
            }
        });
    });
});
