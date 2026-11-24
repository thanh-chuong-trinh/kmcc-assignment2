# Apply / EOI QA

Scope: Khánh's application form, server-side submission handler and EOI schema.

## Run

Start MySQL in XAMPP. From the repository root, run in PowerShell:

```powershell
& "C:\xampp\php\php.exe" -d "extension_dir=C:/xampp/php/ext" -d "browscap=C:/xampp/php/extras/browscap.ini" tests/eoi.php
```

PHP needs mysqli and mbstring. The test uses the host, user and password in
`project2/settings.php`, creates a randomly named `eoi_qa_*` database, copies the
PHP pages/includes to a temporary directory, and starts a temporary server on
127.0.0.1:8099. Leave that port free. The account must be able to create/drop the
test database. The test removes its server, files and database when finished.
It does not change the `secondvolt` database or the project's settings.

## Verified 24 November 2026

Environment: PHP 8.3.36 and MariaDB 10.11.16. Result: **45 checks, 0 failures**.

| Area | Verified result |
| --- | --- |
| SQL import | Complete schema with 17 columns |
| Form | Local POST action, novalidate, current date help, accurate submission notice |
| Direct access | GET redirects to Apply (302) |
| Validation | Missing/invalid fields, wrong dates, future DOB, long text, invalid types, invalid UTF-8 and skill manipulation return 422 without inserting |
| State/postcode | All prefixes in the supplied course mapping accepted; mismatches rejected |
| Save | Missing EOI table is created; a valid submission returns its actual generated ID, correct DOB/skill flags, and status New |
| SQL injection | SQL-like content in free text is stored literally; the table and previous row remain intact |
| XSS in this handler | Script in names is rejected; script text in other skills is not reflected as executable HTML |
| Data integrity | Quotes and backslashes are preserved |
| Database error | HTTP 500 with a friendly message; database name and stack trace are not exposed |

The initial run exposed five failures: stale form help, array-valued other
skills being silently discarded, oversized other skills reaching SQL, invalid
UTF-8 reaching SQL, and backslashes being removed. The final run passes the
same checks after these issues were corrected.

## Integration boundary

The state/postcode mapping follows the supplied coursework table, including
ACT starting with 0; it is not a complete Australian postal-address validator.

Free text is stored as text, not pre-escaped HTML. Any management page that later
displays EOI fields must use `htmlspecialchars($value, ENT_QUOTES, "UTF-8")`.
The current repository has no login.php, logout.php or manage.php; their SQL
injection, stored-XSS, session and access-control behaviour cannot be tested
until those pages exist. This run does not certify security of those future pages.

The application handler and schema are ready for teammate review. Follow the
repository's branch/PR review process before merging into main.
