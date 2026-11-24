<?php
/*
 * manage.php - HR manager dashboard (login required)
 * Features: list all EOIs, filter by job reference, filter by first/last name
 * (or both), choose sort field, change EOI status, delete all EOIs for a job.
 *
 * Security: session check, idle timeout, CSRF tokens on all POST actions,
 * prepared statements, whitelisted sort column, output escaping.
 * Validation rules for filters reuse the Part 1 apply-form rules, and all
 * checking is done in PHP (forms use novalidate, no JavaScript).
 */
session_start();

1. Access control
if (empty($_SESSION['user_id'])) {
    header('Location: login.php?msg=required');
    exit;
}
if (isset($_SESSION['last_active']) && time() - $_SESSION['last_active'] > 1800) { // 30 min idle
    header('Location: logout.php?timeout=1');
    exit;
}
$_SESSION['last_active'] = time();

require_once 'settings.php';          // expects $host, $user, $pwd, $sql_db
mysqli_report(MYSQLI_REPORT_OFF);

2. Configuration: column names from the Part 1 apply form
const EOI_TABLE  = 'eoi';
const COL_KEY    = 'EOInumber';       // auto-increment primary key Could change after seeing teammates' code
const COL_JOB    = 'job_reference';
const COL_FIRST  = 'first_name';
const COL_LAST   = 'last_name';
const COL_STATUS = 'status';
const STATUSES   = ['New', 'Current', 'Final'];

// Part 1 rules: job reference = 5 letters/digits, names = 1-20 letters
const RE_JOB  = '/^[A-Za-z0-9]{5}$/';
const RE_NAME = '/^[A-Za-z]{1,20}$/';

function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); } //turns <, >, & and quotes into harmless HTML entities.

// Friendly heading text for a column name (first_name -> First name) Display make it look better
function col_label($col) {
    if ($col === 'EOInumber') { return 'EOI number'; }
    return ucfirst(str_replace('_', ' ', $col));
}

// CSRF token A random 64-character token is created once per session and put in a hidden field in every POST form. When a form is submitted, csrf_ok() checks that the token matches the session’s copy.
if (empty($_SESSION['csrf'])) { $_SESSION['csrf'] = bin2hex(random_bytes(32)); }
function csrf_ok() {
    return isset($_POST['csrf']) && hash_equals($_SESSION['csrf'], $_POST['csrf']);
}

$conn = @mysqli_connect($host, $user, $pwd, $sql_db);
if (!$conn) { die('Database connection failed.'); }

// 3. Read and validate filter / sort input (GET) “use an empty string if missing”, which avoids undefined-key warnings
$f_job   = trim($_GET['job_ref']    ?? '');
$f_first = trim($_GET['first_name'] ?? '');
$f_last  = trim($_GET['last_name']  ?? '');
$sort    = trim($_GET['sort'] ?? COL_KEY);
$dir     = (($_GET['dir'] ?? 'asc') === 'desc') ? 'DESC' : 'ASC';

$filter_errors = [];
if ($f_job !== ''   && !preg_match(RE_JOB,  $f_job))   { $filter_errors[] = 'Job reference must be exactly five letters or numbers.'; }
if ($f_first !== '' && !preg_match(RE_NAME, $f_first)) { $filter_errors[] = 'First name must be 1 to 20 letters (A-Z).'; }
if ($f_last !== ''  && !preg_match(RE_NAME, $f_last))  { $filter_errors[] = 'Last name must be 1 to 20 letters (A-Z).'; }

// Actual columns of the eoi table (used as the sort whitelist and table headings)
$columns = [];
$colRes  = mysqli_query($conn, "SHOW COLUMNS FROM `" . EOI_TABLE . "`");
if ($colRes) {
    while ($c = mysqli_fetch_assoc($colRes)) { $columns[] = $c['Field']; }
}
$tableMissing = empty($columns);
if (!in_array($sort, $columns, true)) { $sort = COL_KEY; }   // whitelist check

// Query string that keeps the current view after an action
$view = http_build_query(['job_ref' => $f_job, 'first_name' => $f_first,
                          'last_name' => $f_last, 'sort' => $sort,
                          'dir' => strtolower($dir)]);

// ---------- 4. Handle POST actions ----------
$flash       = $_SESSION['flash'] ?? '';
$flash_type  = $_SESSION['flash_type'] ?? 'success';
unset($_SESSION['flash'], $_SESSION['flash_type']);
$confirm = null;   // set when asking the manager to confirm a delete

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$tableMissing) {
    $action = $_POST['action'] ?? '';

    if (!csrf_ok()) {
        $flash = 'Invalid request. Please try again.'; $flash_type = 'error';
    }

    // --- Change status ---
    elseif ($action === 'update_status') {
        $id     = filter_var($_POST['eoi_id'] ?? '', FILTER_VALIDATE_INT);
        $status = $_POST['new_status'] ?? '';
        if ($id === false || !in_array($status, STATUSES, true)) {
            $flash = 'Invalid status update.'; $flash_type = 'error';
        } else {
            $stmt = mysqli_prepare($conn, "UPDATE `" . EOI_TABLE . "` SET `" . COL_STATUS . "` = ? WHERE `" . COL_KEY . "` = ?");
            mysqli_stmt_bind_param($stmt, 'si', $status, $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            $_SESSION['flash'] = "EOI #$id status set to $status.";
            header('Location: manage.php?' . $view);
            exit;
        }
    }

    // --- Delete step 1: ask for confirmation ---
    elseif ($action === 'delete_confirm') {
        $ref = trim($_POST['delete_job_ref'] ?? '');
        if (!preg_match(RE_JOB, $ref)) {
            $flash = 'Job reference must be exactly five letters or numbers.'; $flash_type = 'error';
        } else {
            $stmt = mysqli_prepare($conn, "SELECT COUNT(*) FROM `" . EOI_TABLE . "` WHERE `" . COL_JOB . "` = ?");
            mysqli_stmt_bind_param($stmt, 's', $ref);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $count);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);
            if ($count == 0) { $flash = 'No applications found for job reference ' . $ref . '.'; $flash_type = 'error'; }
            else             { $confirm = ['ref' => $ref, 'count' => $count]; }
        }
    }

    // --- Delete step 2: confirmed ---
    elseif ($action === 'delete_do') {
        $ref = trim($_POST['delete_job_ref'] ?? '');
        if (!preg_match(RE_JOB, $ref)) {
            $flash = 'Invalid job reference.'; $flash_type = 'error';
        } else {
            $stmt = mysqli_prepare($conn, "DELETE FROM `" . EOI_TABLE . "` WHERE `" . COL_JOB . "` = ?");
            mysqli_stmt_bind_param($stmt, 's', $ref);
            mysqli_stmt_execute($stmt);
            $n = mysqli_stmt_affected_rows($stmt);
            mysqli_stmt_close($stmt);
            $_SESSION['flash'] = "Deleted $n application(s) for job reference $ref.";
            header('Location: manage.php?' . $view);
            exit;
        }
    }
}

// ---------- 5. Build and run the list query (prepared, dynamic WHERE) ----------
$rows = [];
if (!$tableMissing && !$filter_errors) {
    $where = []; $types = ''; $params = [];
    $like = function ($v) { return '%' . addcslashes($v, '%_\\') . '%'; };

    if ($f_job !== '')   { $where[] = "`" . COL_JOB   . "` = ?";    $types .= 's'; $params[] = $f_job; }
    if ($f_first !== '') { $where[] = "`" . COL_FIRST . "` LIKE ?"; $types .= 's'; $params[] = $like($f_first); }
    if ($f_last !== '')  { $where[] = "`" . COL_LAST  . "` LIKE ?"; $types .= 's'; $params[] = $like($f_last); }

    // $sort is whitelisted against real column names, $dir is ASC/DESC only
    $sql = "SELECT * FROM `" . EOI_TABLE . "`"
         . ($where ? " WHERE " . implode(' AND ', $where) : '')
         . " ORDER BY `" . $sort . "` " . $dir;

    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        if ($params) { mysqli_stmt_bind_param($stmt, $types, ...$params); }
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        while ($res && ($r = mysqli_fetch_assoc($res))) { $rows[] = $r; }
        mysqli_stmt_close($stmt);
    }
}
mysqli_close($conn);

// ---------- 6. Output ----------
$page_title   = 'Manage applications | SecondVolt';
$current_page = '';
$body_class   = 'manage-page';
include 'header.inc';
?>

  <main id="main">
    <section class="container hero" aria-labelledby="page-title">
      <p class="eyebrow">Staff only</p>
      <h1 id="page-title">Manage applications</h1>
      <p class="intro-copy">Signed in as <strong><?= h($_SESSION['username']) ?></strong>.
        <a class="text-link" href="logout.php">Log out</a></p>
    </section>

    <?php if ($flash): ?>
    <div class="container section">
      <p class="form-message form-message-<?= h($flash_type) ?>" role="<?= $flash_type === 'error' ? 'alert' : 'status' ?>"><?= h($flash) ?></p>
    </div>
    <?php endif; ?>

    <?php if ($tableMissing): ?>
    <div class="container section">
      <p class="form-message form-message-error" role="alert">The applications table does not exist yet. Submit an application first.</p>
    </div>
    <?php else: ?>

    <?php if ($confirm): ?>
    <section class="container section" aria-labelledby="confirm-title">
      <div class="form-panel">
        <div class="form-heading">
          <h2 id="confirm-title">Confirm deletion</h2>
        </div>
        <p class="form-message form-message-warning" role="alert">
          This will permanently delete <?= h($confirm['count']) ?> application(s) for job reference
          <strong><?= h($confirm['ref']) ?></strong>. This cannot be undone.
        </p>
        <form class="application-form" method="post" action="manage.php?<?= h($view) ?>" novalidate>
          <input type="hidden" name="csrf" value="<?= h($_SESSION['csrf']) ?>">
          <input type="hidden" name="action" value="delete_do">
          <input type="hidden" name="delete_job_ref" value="<?= h($confirm['ref']) ?>">
          <div class="form-bottom">
            <p class="form-actions">
              <button type="submit" class="danger-button">Yes, delete them</button>
              <a class="text-link" href="manage.php?<?= h($view) ?>">Cancel</a>
            </p>
          </div>
        </form>
      </div>
    </section>
    <?php endif; ?>

    <section class="container section" aria-labelledby="filter-title">
      <div class="form-panel">
        <div class="form-heading">
          <p class="eyebrow">Find applications</p>
          <h2 id="filter-title">Search, filter and sort</h2>
          <p>Leave every field empty to list all applications.</p>
        </div>
        <?php if ($filter_errors): ?>
        <div class="form-message form-message-error" role="alert">
          <?php foreach ($filter_errors as $e): ?><p><?= h($e) ?></p><?php endforeach; ?>
        </div>
        <?php endif; ?>
        <form class="application-form" method="get" action="manage.php" novalidate>
          <fieldset class="form-section">
            <legend class="visually-hidden">Filters</legend>
            <div class="form-grid">
              <p class="form-field field-wide">
                <label for="job_ref">Job reference</label>
                <input type="text" id="job_ref" name="job_ref" value="<?= h($f_job) ?>" aria-describedby="job-ref-help">
                <small id="job-ref-help">Five letters or numbers, for example SV101.</small>
              </p>
              <p class="form-field">
                <label for="first_name">First name</label>
                <input type="text" id="first_name" name="first_name" value="<?= h($f_first) ?>">
              </p>
              <p class="form-field">
                <label for="last_name">Last name</label>
                <input type="text" id="last_name" name="last_name" value="<?= h($f_last) ?>">
              </p>
              <p class="form-field">
                <label for="sort">Sort by</label>
                <select id="sort" name="sort">
                  <?php foreach ($columns as $col): ?>
                  <option value="<?= h($col) ?>"<?= $col === $sort ? ' selected' : '' ?>><?= h(col_label($col)) ?></option>
                  <?php endforeach; ?>
                </select>
              </p>
              <p class="form-field">
                <label for="dir">Order</label>
                <select id="dir" name="dir">
                  <option value="asc"<?= $dir === 'ASC' ? ' selected' : '' ?>>Ascending</option>
                  <option value="desc"<?= $dir === 'DESC' ? ' selected' : '' ?>>Descending</option>
                </select>
              </p>
            </div>
          </fieldset>
          <div class="form-bottom">
            <p class="form-test-note"><a class="text-link" href="manage.php">Show all applications (reset)</a></p>
            <p class="form-actions"><button type="submit">Apply filters <span aria-hidden="true">&rarr;</span></button></p>
          </div>
        </form>
      </div>
    </section>

    <section class="container section" aria-labelledby="results-title">
      <h2 id="results-title">Results (<?= count($rows) ?>)</h2>
      <?php if (!$rows): ?>
        <p><?= $filter_errors ? 'Fix the filters above to see results.' : 'No applications match your search.' ?></p>
      <?php else: ?>
      <div class="table-wrap">
        <table class="facts-table manage-table">
          <caption>Applications (expressions of interest)</caption>
          <thead>
            <tr>
              <?php foreach ($columns as $col): ?><th scope="col"><?= h(col_label($col)) ?></th><?php endforeach; ?>
              <th scope="col">Change status</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($rows as $r): ?>
            <tr>
              <?php foreach ($columns as $col): ?><td><?= h($r[$col] ?? '') ?></td><?php endforeach; ?>
              <td>
                <form class="status-form" method="post" action="manage.php?<?= h($view) ?>" novalidate>
                  <input type="hidden" name="csrf" value="<?= h($_SESSION['csrf']) ?>">
                  <input type="hidden" name="action" value="update_status">
                  <input type="hidden" name="eoi_id" value="<?= h($r[COL_KEY]) ?>">
                  <label class="visually-hidden" for="st<?= h($r[COL_KEY]) ?>">Status for EOI <?= h($r[COL_KEY]) ?></label>
                  <select id="st<?= h($r[COL_KEY]) ?>" name="new_status">
                    <?php foreach (STATUSES as $s): ?>
                    <option value="<?= h($s) ?>"<?= ($r[COL_STATUS] ?? '') === $s ? ' selected' : '' ?>><?= h($s) ?></option>
                    <?php endforeach; ?>
                  </select>
                  <button type="submit" class="small-button">Update</button>
                </form>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>
    </section>

    <section class="container section" aria-labelledby="delete-title">
      <div class="form-panel">
        <div class="form-heading">
          <p class="eyebrow">Danger zone</p>
          <h2 id="delete-title">Delete applications by job reference</h2>
          <p>You will be asked to confirm before anything is deleted.</p>
        </div>
        <form class="application-form" method="post" action="manage.php?<?= h($view) ?>" novalidate>
          <input type="hidden" name="csrf" value="<?= h($_SESSION['csrf']) ?>">
          <input type="hidden" name="action" value="delete_confirm">
          <fieldset class="form-section">
            <legend class="visually-hidden">Delete by job reference</legend>
            <p class="form-field">
              <label for="delete_job_ref">Job reference</label>
              <input type="text" id="delete_job_ref" name="delete_job_ref">
            </p>
          </fieldset>
          <div class="form-bottom">
            <p class="form-actions"><button type="submit" class="danger-button">Delete all for this job&hellip;</button></p>
          </div>
        </form>
      </div>
    </section>

    <?php endif; ?>
  </main>

<?php include 'footer.inc'; ?>
