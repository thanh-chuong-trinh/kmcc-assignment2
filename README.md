# Second Volt (kmcc-assignment2)

COS10026 Project Part 2, Group 6. Second Volt is a demo second-hand electronics store.
This version adds PHP and MySQL to the Part 1 website. It is not a real store.

## Running it locally (XAMPP)

1. Start Apache and MySQL in XAMPP.
2. Copy the `project2` folder into `htdocs` (so it is `htdocs/project2/`).
3. Import `database.sql` in phpMyAdmin. It creates the `secondvolt` database.
4. Open http://localhost/project2/index.php

Or double-click `run.bat` in the repo folder: it starts MySQL, imports `database.sql`, starts PHP and opens http://localhost:8089/index.php.
Close its window to stop the site. It looks for XAMPP in `C:\xampp` and `D:\xampp`.

## Folder structure

```
project2/
  index.php  jobs.php  apply.php  about.php
  header.inc  nav.inc  footer.inc  settings.php
  process_eoi.php
  search.php  manage.php   (to be added)
  images/             page images
  styles/style.css    shared stylesheet
  styles/images/      images used by the CSS
database.sql          one shared SQL file for all tables
tests/eoi.php         isolated CLI checks for application submissions
```

## Checking Apply and EOI

See [the EOI test instructions and results](tests/README.md). The checks use a temporary database and do not change applications in `secondvolt`.

## GenAI acknowledgement - Khanh's Part 2 work

OpenAI Codex assisted with the EOI schema, Apply form integration,
process_eoi.php implementation and debugging, automated QA scripts, and QA
documentation. The work reuses the team's Part 1 form and shared includes.
Course requirements were checked against COS10026 Canvas and the team's task
allocation. Automated checks and their limits are recorded in tests/README.md.
This acknowledgement covers Khanh's work only; it does not describe how other
members created their code.

## Team coding conventions

- Every page starts with `<?php $current_page = 'pagename'; ?>` (for example `'jobs'`), then the HTML head,
  then `<?php include 'header.inc'; ?>` after `<body>` and `<?php include 'footer.inc'; ?>` before `</body>`.
- Do not copy the header, menu or footer into a page. Change the `.inc` file instead.
- Connect to the database with `require 'settings.php';` and the variables `$host`, `$user`, `$pwd`, `$dbname`.
- Add your table to `database.sql` with `CREATE TABLE IF NOT EXISTS` so the file can be imported again safely.
- Use relative links only (`jobs.php`, `images/logo.svg`). No absolute paths.
- Do all form validation on the server. Trim and escape user input before showing it, and use prepared statements for queries.
- Work on a branch, open a pull request, and wait for a review before merging into `main`.
