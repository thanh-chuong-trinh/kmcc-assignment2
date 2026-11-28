<?php
/*
 * login.php - Manager login for manage.php
 * Author: (your name and student ID), Group 6 (KMCC), COS10026
 * Acknowledgements:
 * - Shared header, footer and styles conventions by the team (see README.md).
 * - Parts of this page were drafted with help from Claude (Anthropic) and reviewed and edited by the author.
 *
 * The username and password are checked on the server only (no JavaScript).
 * Passwords are stored as hashes in the users table, never as plain text.
 */
session_start();
require_once 'settings.php';   // gives us $host, $user, $pwd, $dbname

// If the manager is already logged in, there is nothing to do here
if (!empty($_SESSION['user_id'])) {
    header('Location: manage.php');
    exit;
}

// Escape text before printing it in the page
function h($text) {
    return htmlspecialchars((string)$text, ENT_QUOTES, 'UTF-8');
}

$username = '';
$error    = '';
$notice   = '';

// Messages sent here by logout.php and manage.php
$msg = $_GET['msg'] ?? '';
if ($msg === 'loggedout') {
    $notice = 'You have been logged out.';
} elseif ($msg === 'timeout') {
    $notice = 'Your session expired. Please sign in again.';
} elseif ($msg === 'required') {
    $notice = 'Please sign in to use the manager page.';
}

// Do not let PHP stop with a fatal error if a query fails, we check the results ourselves
mysqli_report(MYSQLI_REPORT_OFF);
$conn = @mysqli_connect($host, $user, $pwd, $dbname);

if (!$conn) {
    $error = 'Cannot connect to the database. Please try again later.';
} else {
    // Make sure the users table exists
    mysqli_query($conn, "CREATE TABLE IF NOT EXISTS users (
        user_id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL
    )");

    // If the table is empty, add the admin account for the marker (admin / admin)
    $result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users");
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        if ($row['total'] == 0) {
            $admin_name = 'admin';
            $admin_hash = password_hash('admin', PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($conn, "INSERT INTO users (username, password_hash) VALUES (?, ?)");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'ss', $admin_name, $admin_hash);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        }
    }
}

// The form was submitted
if ($conn && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';   // passwords are not trimmed

    if (!empty($_SESSION['locked_until']) && time() < $_SESSION['locked_until']) {
        // Too many wrong tries, make them wait
        $error = 'Too many failed attempts. Please wait a minute and try again.';
    } elseif ($username === '' || $password === '') {
        $error = 'Please enter your username and password.';
    } else {
        // Look the user up with a prepared statement so the input cannot change the SQL
        $stmt = mysqli_prepare($conn, "SELECT user_id, username, password_hash FROM users WHERE username = ?");

        if (!$stmt) {
            $error = 'Something went wrong. Please try again later.';
        } else {
            mysqli_stmt_bind_param($stmt, 's', $username);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $row = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);

            if ($row && password_verify($password, $row['password_hash'])) {
                // Correct login: start a fresh session ID and remember who is logged in
                session_regenerate_id(true);
                $_SESSION['user_id']     = $row['user_id'];
                $_SESSION['username']    = $row['username'];
                $_SESSION['last_active'] = time();
                unset($_SESSION['failed_logins'], $_SESSION['locked_until']);
                header('Location: manage.php');
                exit;
            }

            // Wrong login. Same message for a wrong username or a wrong password
            $_SESSION['failed_logins'] = ($_SESSION['failed_logins'] ?? 0) + 1;
            if ($_SESSION['failed_logins'] >= 5) {
                $_SESSION['locked_until'] = time() + 60;
                $_SESSION['failed_logins'] = 0;
            }
            $error = 'Invalid username or password.';
        }
    }
}

if ($conn) {
    mysqli_close($conn);
}

$current_page = 'login';   // not in the main menu, so no menu item is highlighted
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Manager login for Second Volt.">
  <meta name="robots" content="noindex">
  <title>Manager Login - Second Volt</title>
  <link rel="stylesheet" href="styles/style.css">
</head>
<body>
<?php include 'header.inc'; ?>

  <main id="main">
    <section class="container hero" aria-labelledby="page-title">
      <p class="eyebrow">Staff only</p>
      <h1 id="page-title">Manager login</h1>
      <p class="intro-copy">Sign in to review and manage job applications.</p>
    </section>

    <section class="container section" aria-labelledby="form-title">
      <div class="form-panel login-panel">
        <div class="form-heading">
          <p class="eyebrow">Your account</p>
          <h2 id="form-title">Sign in</h2>
        </div>

        <?php if ($notice !== ''): ?>
        <p class="form-message form-message-success" role="status"><?php echo h($notice); ?></p>
        <?php endif; ?>
        <?php if ($error !== ''): ?>
        <p class="form-message form-message-error" role="alert"><?php echo h($error); ?></p>
        <?php endif; ?>

        <form class="application-form" method="post" action="login.php" aria-labelledby="form-title" novalidate>
          <fieldset class="form-section">
            <legend class="visually-hidden">Login details</legend>
            <p class="form-field">
              <label for="username">Username</label>
              <input type="text" id="username" name="username" value="<?php echo h($username); ?>" autocomplete="username">
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
</body>
</html>