-- database.sql - one shared SQL file for the whole project (Project Part 2).
-- Import it once in phpMyAdmin (or: mysql -u root < database.sql) to set up the database.
-- Each member adds their own table below, using CREATE TABLE IF NOT EXISTS so the file can be re-run safely.

CREATE DATABASE IF NOT EXISTS secondvolt CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE secondvolt;

-- ---------------------------------------------------------------
-- eoi    (Expression of Interest, filled by process_eoi.php)
-- jobs   (job listings shown on jobs.php)                     - done (Cuong)
-- users  (manager logins for manage.php, hashed passwords)    - TODO
-- about  (member contributions shown on about.php)            - TODO
-- ---------------------------------------------------------------

-- ---------------------------------------------------------------
-- jobs: one row per position on jobs.php (Ngo Manh Cuong, 105710919).
-- Each job field has its own column. The three list columns hold one item
-- per line; jobs.php turns each line into an <li>.
-- ---------------------------------------------------------------
CREATE TABLE IF NOT EXISTS jobs (
  job_id           INT          NOT NULL AUTO_INCREMENT PRIMARY KEY,
  job_ref          CHAR(5)      NOT NULL UNIQUE,   -- five-character reference, matches the apply form
  title            VARCHAR(80)  NOT NULL,
  summary          VARCHAR(255) NOT NULL,
  salary           VARCHAR(100) NOT NULL,
  reports_to       VARCHAR(80)  NOT NULL,
  location         VARCHAR(80)  NOT NULL,
  work_type        VARCHAR(60)  NOT NULL,
  responsibilities TEXT         NOT NULL,          -- one item per line
  essential        TEXT         NOT NULL,          -- one item per line
  preferable       TEXT         NOT NULL           -- one item per line
);

-- Re-running the file keeps existing rows (job_ref is UNIQUE) instead of failing.
INSERT INTO jobs (job_ref, title, summary, salary, reports_to, location, work_type, responsibilities, essential, preferable) VALUES
(
  'SV101',
  'Front-end Developer',
  'Build fast, accessible product and trade-in pages for our online store.',
  'AUD $90,000 - $105,000 a year, plus super',
  'Head of Digital Retail',
  'Melbourne CBD, hybrid',
  'Full-time, permanent',
  'Build product listing, product detail and checkout pages with semantic HTML and responsive CSS.
Keep every page accessible to WCAG 2.1 AA.
Improve page speed on phones, where most of our customers shop.
Review code with the team through GitHub pull requests.',
  '3+ years building websites with HTML and CSS.
Comfortable using Git in a team.
Experience testing pages with a keyboard and a screen reader.',
  'Experience on an e-commerce website.
Interest in reuse and reducing e-waste.'
),
(
  'SV205',
  'UX Designer',
  'Make shopping, checkout and trade-ins easy for every customer.',
  'AUD $95,000 - $110,000 a year, plus super',
  'Director of Customer Experience',
  'Melbourne CBD, hybrid',
  'Full-time, permanent',
  'Run user tests and accessibility checks with real customers.
Design wireframes and prototypes for new features, such as the trade-in quote.
Write clear product grading labels so buyers know a device''s condition.
Share findings with developers and the product team.',
  '2+ years of UX design experience.
A portfolio that shows your design process.
Working knowledge of WCAG 2.1.',
  'Experience designing for screen reader users.
Experience with an online store or marketplace.'
)
ON DUPLICATE KEY UPDATE job_ref = jobs.job_ref;

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
