-- database.sql - one shared SQL file for the whole project (Project Part 2).
-- Import it once in phpMyAdmin (or: mysql -u root < database.sql) to set up the database.
-- Each member adds their own table below, using CREATE TABLE IF NOT EXISTS so the file can be re-run safely.

CREATE DATABASE IF NOT EXISTS secondvolt CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE secondvolt;

-- ---------------------------------------------------------------
-- eoi    (Expression of Interest, filled by process_eoi.php)
-- jobs   (job listings shown on jobs.php)                     - TODO
-- users  (manager logins for manage.php, hashed passwords)    - TODO
-- about  (member contributions shown on about.php)            - TODO
-- ---------------------------------------------------------------

CREATE TABLE IF NOT EXISTS eoi (
  EOInumber INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  job_reference VARCHAR(5) NOT NULL,
  first_name VARCHAR(20) NOT NULL,
  last_name VARCHAR(20) NOT NULL,
  date_of_birth DATE NOT NULL,
  gender VARCHAR(20) NOT NULL,
  street_address VARCHAR(40) NOT NULL,
  suburb VARCHAR(40) NOT NULL,
  state VARCHAR(3) NOT NULL,
  postcode CHAR(4) NOT NULL,
  email VARCHAR(254) NOT NULL,
  phone VARCHAR(12) NOT NULL,
  skill_experience TINYINT(1) NOT NULL DEFAULT 0,
  skill_tools_portfolio TINYINT(1) NOT NULL DEFAULT 0,
  skill_accessibility TINYINT(1) NOT NULL DEFAULT 0,
  other_skills TEXT,
  status ENUM('New', 'Current', 'Final') NOT NULL DEFAULT 'New'
);
