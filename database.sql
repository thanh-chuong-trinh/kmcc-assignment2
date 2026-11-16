-- database.sql - one shared SQL file for the whole project (Project Part 2).
-- Import it once in phpMyAdmin (or: mysql -u root < database.sql) to set up the database.
-- Each member adds their own table below, using CREATE TABLE IF NOT EXISTS so the file can be re-run safely.

CREATE DATABASE IF NOT EXISTS secondvolt CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE secondvolt;

-- ---------------------------------------------------------------
-- eoi    (Expression of Interest, filled by process_eoi.php)  - TODO
-- jobs   (job listings shown on jobs.php)                     - TODO
-- users  (manager logins for manage.php, hashed passwords)    - TODO
-- about  (member contributions shown on about.php)            - TODO
-- ---------------------------------------------------------------
