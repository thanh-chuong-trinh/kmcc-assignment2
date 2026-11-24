<?php
/*
 * login.php - Manager login
 * Verifies username/password against the `users` table and starts a session.
 * Security: prepared statements, password_hash/password_verify, session ID
 * regeneration, generic error message, simple brute-force lockout.
 * All validation is server-side (form uses novalidate, no JavaScript).
 */
session_start();
require_once 'settings.php';          // expects $host, $user, $pwd, $sql_db
mysqli_report(MYSQLI_REPORT_OFF);     // we check errors ourselves

// Already logged in? Go straight to the dashboard.
if (!empty($_SESSION['user_id'])) {
    header('Location: manage.php');
    exit;
}

function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

$error    = '';
$notice   = '';
$username = '';

// Messages passed from logout.php / manage.php
$msg = $_GET['msg'] ?? '';
if ($msg === 'loggedout') { $notice = 'You have been logged out.'; }
if ($msg === 'timeout')   { $notice = 'Your session expired. Please sign in again.'; }
if ($msg === 'required')  { $notice = 'Please sign in to access the manager page.'; }

$conn = @mysqli_connect($host, $user, $pwd, $sql_db);
if (!$conn) {
    $error = 'The service is temporarily unavailable. Please try again later.';
} else {
    // Create users table if it does not exist
    mysqli_query($conn, "CREATE TABLE IF NOT EXISTS users (
        user_id       INT AUTO_INCREMENT PRIMARY KEY,
        username      VARCHAR(50)  NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL
    )");

    // Seed the marker's account (admin/admin) if the table is empty
    $res = mysqli_query($conn, "SELECT COUNT(*) AS c FROM users");
    if ($res && (int)mysqli_fetch_assoc($res)['c'] === 0) {
        $hash  = password_hash('admin', PASSWORD_DEFAULT);
        $seed  = mysqli_prepare($conn, "INSERT INTO users (username, password_hash) VALUES (?, ?)");
        $admin = 'admin';
        mysqli_stmt_bind_param($seed, 'ss', $admin, $hash);
        mysqli_stmt_execute($seed);
        mysqli_stmt_close($seed);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $conn) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';   // never trim/alter passwords

    // Lockout after 5 failed attempts for 60 seconds
    if (!empty($_SESSION['lock_until']) && time() < $_SESSION['lock_until']) {
        $error = 'Too many failed attempts. Please wait a minute and try again.';
    } elseif ($username === '' || $password === '') {
        $error = 'Please enter both username and password.';
    } elseif (strlen($username) > 50) {
        $error = 'Invalid username or password.';
    } else {
        $stmt = mysqli_prepare($conn, "SELECT user_id, username, password_hash FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row    = $result ? mysqli_fetch_assoc($result) : null;
        mysqli_stmt_close($stmt);

        if ($row && password_verify($password, $row['password_hash'])) {
            session_regenerate_id(true);               // prevents session fixation
            $_SESSION['user_id']     = (int)$row['user_id'];
            $_SESSION['username']    = $row['username'];
            $_SESSION['last_active'] = time();
            unset($_SESSION['fails'], $_SESSION['lock_until']);
            header('Location: manage.php');
            exit;
        }

        // Generic message: never reveal which field was wrong
        $_SESSION['fails'] = ($_SESSION['fails'] ?? 0) + 1;
        if ($_SESSION['fails'] >= 5) {
            $_SESSION['lock_until'] = time() + 60;
            $_SESSION['fails'] = 0;
        }
        $error = 'Invalid username or password.';
    }
}
if ($conn) { mysqli_close($conn); }

$page_title   = 'Manager login | SecondVolt';
$current_page = '';
$body_class   = 'login-page';
include 'header.inc';
?>

  <main id="main">
    <section class="container hero" aria-labelledby="page-title">
      <p class="eyebrow">Staff only</p>
      <h1 id="page-title">Manager login</h1>
      <p class="intro-copy">Sign in to review and manage applications.</p>
    </section>

    <section class="container section" aria-labelledby="form-title">
      <div class="form-panel login-panel">
        <div class="form-heading">
          <p class="eyebrow">Your account</p>
          <h2 id="form-title">Sign in</h2>
        </div>

        <?php if ($notice): ?>
        <p class="form-message form-message-success" role="status"><?= h($notice) ?></p>
        <?php endif; ?>
        <?php if ($error): ?>
        <p class="form-message form-message-error" role="alert"><?= h($error) ?></p>
        <?php endif; ?>

        <form class="application-form" method="post" action="login.php" aria-labelledby="form-title" novalidate>
          <fieldset class="form-section">
            <legend class="visually-hidden">Login details</legend>
            <p class="form-field">
              <label for="username">Username</label>
              <input type="text" id="username" name="username" value="<?= h($username) ?>" autocomplete="username">
            </p>
            <p class="form-field">
              <label for="password">Password</label>
              <input type="password" id="password" name="password" autocomplete="current-password">
            </p>
          </fieldset>
          <div class="form-bottom">
            <p class="form-actions">
              <button type="submit">Sign in <span aria-hidden="true">&rarr;</span></button>
            </p>
          </div>
        </form>
      </div>
    </section>
  </main>

<?php include 'footer.inc'; ?>
