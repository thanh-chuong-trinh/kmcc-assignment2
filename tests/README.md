# Apply / EOI QA

Scope: Khánh's application form, server-side submission handler, EOI schema,
and group integration/security QA. OpenAI Codex assisted with these tests and
documentation; see the acknowledgement in the repository README.

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

Environment: PHP 8.3.36 and MariaDB 10.11.16.
Apply/EOI and jobs: **55 checks, 0 failures** in the final combined run.
Jobs source: main at `744a225` (Cường's merged `4ff93dc`).

| Area | Verified result |
| --- | --- |
| SQL import | Complete schema with 17 columns |
| Form | Local POST action, novalidate, current date help, accurate submission notice |
| Direct access | GET redirects to Apply (302) |
| Validation | Missing/invalid fields, wrong dates, future DOB, long text, invalid types, invalid UTF-8 and skill manipulation return 422 without inserting |
| State/postcode | All prefixes in the supplied course mapping accepted; mismatches rejected |
| Save | Missing EOI table is created; a valid submission returns its actual generated ID, correct DOB/skill flags, and status New |
| SQL injection | SQL-like content remains data after Canvas sanitisation; the table and previous row remain intact |
| XSS in this handler | Script in names is rejected; script text in other skills is not reflected as executable HTML |
| Canvas sanitisation | trim and stripslashes run before validation; output is HTML-escaped; literal unescaped quotes remain data |
| Database error | HTTP 500 with a friendly message; database name and stack trace are not exposed |
| Jobs integration | Both seeded jobs render; reference search returns the matching job |
| Jobs security | SQL injection, reflected/stored script, wildcard escaping and array query tested; no table damage; friendly DB error |

The earlier run fixed stale form help, malformed other skills, oversized text
and invalid UTF-8. The Canvas audit then confirmed an explicit slash-stripping
requirement. The regression expectation was changed first: 55 checks gave one
failure. Adding stripslashes made that check pass. This intentionally changes
backslash-containing text; it is a coursework rule, not the SQL-injection defence.
Prepared statements provide that defence. No HTML pre-escaping is applied to DB
values; escape when rendering them.

## Integration boundary

The state/postcode mapping follows the supplied coursework table, including
ACT starting with 0; it is not a complete Australian postal-address validator.

Free text is stored as text, not pre-escaped HTML. Any management page that later
displays EOI fields must use `htmlspecialchars($value, ENT_QUOTES, "UTF-8")`.
Login/manage are not merged into main. Their separate branch was reviewed below.

The application handler and schema are ready for teammate review. Follow the
repository's branch/PR review process before merging into main.

## Manager branch review - failures for Minh to fix

Exact source: `origin/feature/login-manage` at
`8431d490fbd2a058d8a760d2f4a988608425991e`. The tests use this pinned commit,
not a moving branch, so the findings remain reproducible. Fetch it first if needed.
Run the same command above with `--manager-review` appended.

Final combined run: **67 checks, 6 failures**. The 55 Apply/EOI/jobs checks pass.
The 12 manager diagnostics have 6 passes and 6 failures, representing these issues:

| Priority | Reproduction / observed problem | Smallest fix owned by Minh |
| --- | --- | --- |
| P1 | login.php, logout.php and manage.php are at repo root while settings and public pages are under project2. The required project2 routes are absent. | Put manager pages in project2 and reconcile their includes with the shared page structure. |
| P1 | `php -n -l manage.php` fails on line 14: `1. Access control` is PHP code, not a comment. Line 28 has the same defect. GET cannot reach the access-control redirect. | Comment those two headings, then lint and rerun access-control tests. |
| P1 | After temporary relocation, login cannot use the shared DB: login line 30 and manage line 55 pass undefined `$sql_db`; settings defines `$dbname`. | Use `$dbname` consistently; retain the shared settings file. |
| P2 | After the temporary DB alias described below, POST `username[]=admin` or `password[]=wrong` causes HTTP 500 (trim/password_verify TypeError). | Check username and password are strings before string operations; apply the same boundary checks to manage GET/POST and CSRF input. |

The failed redirect check is a consequence of the parse error, not proof that
working access control can be bypassed. The sixth failure is the second malformed
credential case, not a separate security vulnerability.

To examine login logic beyond the blockers, the script copies the exact branch
files into the temporary site and adds `$sql_db=$dbname` **only to temporary
settings**. Under those diagnostic conditions: admin/admin works, the password
is hashed, successful login rotates the session ID, SQL-injection login is denied,
reflected script is escaped, and logout invalidates the previous session.
These conditional passes do not mean the submitted branch works as-is.

Source inspection also found that Minh's header contains a full HTML document,
while the existing project pages already supply doctype/head/body. Blindly
replacing the shared header would nest HTML documents; reconcile templates when
integrating. The new root manager-block.css is not linked by that header.

Blocked by the unmodified manage.php parse error: real EOI listing/filter/sort,
status updates/deletion, stored-XSS rendering, CSRF and authenticated/unauthenticated
access tests. These must be rerun after Minh's fixes; they are not marked passed.
No teammate source, real applications, production sessions or main branch was
changed by the diagnostic script. All its files, sessions and DB are disposable.
