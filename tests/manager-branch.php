<?php
// Diagnostic review of Minh's exact branch snapshot; never changes his source.
// OpenAI Codex assisted with this QA script (see README.md).
if (PHP_SAPI !== 'cli' || !isset($site, $db)) { exit(1); }
$ref = '8431d490fbd2a058d8a760d2f4a988608425991e';
function captureCommand($args) {
    $process = proc_open($args, [0 => ['pipe', 'r'], 1 => ['pipe', 'w'],
        2 => ['pipe', 'w']], $pipes, __DIR__ . '/..');
    fclose($pipes[0]);
    $output = stream_get_contents($pipes[1]);
    $error = stream_get_contents($pipes[2]);
    fclose($pipes[1]); fclose($pipes[2]);
    return [proc_close($process), $output . $error];
}
echo "MANAGER REVIEW snapshot $ref (diagnostic failures expected until teammate fixes)\n";
[$code, $tree] = captureCommand(['git', 'ls-tree', '-r', '--name-only', $ref]);
if ($code !== 0) { throw new RuntimeException($tree); }
verify(strpos($tree, "project2/login.php\n") !== false
    && strpos($tree, "project2/manage.php\n") !== false,
    'Manager layout: pages are inside project2 beside settings.php');
foreach (['login.php', 'logout.php', 'manage.php', 'header.inc', 'footer.inc'] as $file) {
    [$code, $content] = captureCommand(['git', 'show', "$ref:$file"]);
    if ($code !== 0) { throw new RuntimeException($content); }
    file_put_contents($site . '/' . $file, $content);
}
[$code, $output] = captureCommand([PHP_BINARY, '-n', '-l', $site . '/manage.php']);
verify($code === 0, 'Manager PHP syntax valid');
if ($code !== 0) { echo $output; }
[$status, $body] = requestPage('manage.php');
verify($status === 302, 'Unauthenticated manager GET redirects (currently blocked by parse error)');
// Temporary relocation isolates the database-variable mismatch from the layout defect.
[$status, $body] = requestPage('login.php');
$hasUsers = mysqli_num_rows(mysqli_query($db, "SHOW TABLES LIKE 'users'")) === 1;
verify($status === 200 && $hasUsers && strpos($body, 'Warning') === false,
    'Login integrates with shared dbname settings');
echo "DIAGNOSTIC ONLY: add sql_db alias in temporary settings to test remaining login logic.\n";
file_put_contents($site . '/settings.php', $settings . ' $sql_db=$dbname;');
[$status, $body, $headers] = requestPage('login.php');
preg_match('/Set-Cookie: (PHPSESSID=[^;\r\n]+)/i', $headers, $cookieMatch);
$cookie = $cookieMatch[1] ?? '';
$hash = mysqli_fetch_row(mysqli_query($db, "SELECT password_hash FROM users WHERE username='admin'"))[0];
verify($hash !== 'admin' && password_verify('admin', $hash), 'Conditional login: admin password is hashed');
[$status, $body] = requestPage('login.php', ['username' => "admin' OR 1=1 --", 'password' => 'wrong'], $cookie);
verify($status === 200 && strpos($body, 'Invalid username or password') !== false,
    'Conditional login: SQL injection does not bypass authentication');
[$status, $body] = requestPage('login.php', ['username' => '<script>alert(1)</script>', 'password' => 'wrong'], $cookie);
verify($status === 200 && strpos($body, '<script>alert(1)</script>') === false
    && strpos($body, '&lt;script&gt;') !== false, 'Conditional login: reflected script escaped');
[$status, $body] = requestPage('login.php', ['username' => ['admin'], 'password' => 'wrong'], $cookie);
verify($status < 500 && strpos($body, 'Fatal error') === false, 'Login rejects array username without crashing');
[$status, $body] = requestPage('login.php', ['username' => 'admin', 'password' => ['wrong']], $cookie);
verify($status < 500 && strpos($body, 'Fatal error') === false, 'Login rejects array password without crashing');
[$status, $body, $headers] = requestPage('login.php', ['username' => 'admin', 'password' => 'admin'], $cookie);
preg_match('/Set-Cookie: (PHPSESSID=[^;\r\n]+)/i', $headers, $newCookie);
verify($status === 302 && strpos($headers, 'Location: manage.php') !== false
    && !empty($newCookie[1]) && $newCookie[1] !== $cookie,
    'Conditional login: admin/admin succeeds and rotates session ID');
[$status, $body, $headers] = requestPage('logout.php', null, $newCookie[1] ?? '');
verify($status === 302 && strpos($headers, 'Location: login.php?msg=loggedout') !== false,
    'Conditional logout: redirects to login');
[$status] = requestPage('login.php', null, $newCookie[1] ?? '');
verify($status === 200, 'Conditional logout: previous session no longer authenticated');
