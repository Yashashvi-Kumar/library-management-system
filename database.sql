-- =====================================================
--  Library Management System  -  MySQL database script
--  Import this file in phpMyAdmin (Import tab) or run:
--      mysql -u root -p < database.sql
-- =====================================================

-- 1) Create the database and select it
CREATE DATABASE IF NOT EXISTS library_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_general_ci;

USE library_db;

-- 2) USERS table: people who can log in
--    "password" stores a HASH (never the real password)
CREATE TABLE IF NOT EXISTS users (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    username   VARCHAR(50)  NOT NULL UNIQUE,
    email      VARCHAR(100) NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- 3) BOOKS table: every book in the library
--    added_by is a FOREIGN KEY pointing to users.id
CREATE TABLE IF NOT EXISTS books (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    title        VARCHAR(150) NOT NULL,
    author       VARCHAR(100) NOT NULL,
    category     VARCHAR(50)  NOT NULL,
    publish_year INT          NOT NULL,
    status       ENUM('Available','Issued') NOT NULL DEFAULT 'Available',
    added_by     INT NULL,
    created_at   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (added_by) REFERENCES users(id) ON DELETE SET NULL
);

-- 4) Sample books so the dashboard is not empty
INSERT INTO books (title, author, category, publish_year, status) VALUES
('Introduction to Algorithms', 'Thomas H. Cormen',   'Computer Science', 2009, 'Available'),
('Clean Code',                 'Robert C. Martin',   'Programming',      2008, 'Available'),
('The C Programming Language', 'Brian W. Kernighan', 'Programming',      1988, 'Issued'),
('Database System Concepts',   'Abraham Silberschatz','Databases',       2019, 'Available'),
('Wings of Fire',              'A. P. J. Abdul Kalam','Biography',       1999, 'Available');

-- NOTE: No sample user is inserted here. Open register.php and create
-- your own account (the password is hashed by PHP).
