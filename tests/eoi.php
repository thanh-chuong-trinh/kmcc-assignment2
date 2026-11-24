<?php
// CLI integration checks. Only the randomly named eoi_qa_* database is changed.
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}
require __DIR__ . '/../project2/settings.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$db = mysqli_connect($host, $user, $pwd);
$testDb = 'eoi_qa_' . bin2hex(random_bytes(6));
$site = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $testDb;
$server = null;
$failures = [];
$checks = 0;
function verify($condition, $label) {
    global $checks, $failures;
    $checks++;
    echo ($condition ? 'PASS ' : 'FAIL ') . $label . PHP_EOL;
    if (!$condition) {
        $failures[] = $label;
    }
}
function requestPage($path, $data = null) {
    $options = ['method' => $data === null ? 'GET' : 'POST',
        'ignore_errors' => true, 'follow_location' => 0, 'timeout' => 5];
    if ($data !== null) {
        $options['header'] = "Content-Type: application/x-www-form-urlencoded\r\n";
        $options['content'] = http_build_query($data);
    }
    $body = file_get_contents('http://127.0.0.1:8099/' . $path, false,
        stream_context_create(['http' => $options]));
    preg_match('/\s(\d{3})\s/', $http_response_header[0], $match);
    return [(int) $match[1], $body, implode("\n", $http_response_header)];
}
function rowCount($db) {
    return (int) mysqli_fetch_row(mysqli_query($db, 'SELECT COUNT(*) FROM eoi'))[0];
}
try {
    mkdir($site);
    foreach (glob(__DIR__ . '/../project2/*') as $file) {
        if (is_file($file) && in_array(pathinfo($file, PATHINFO_EXTENSION), ['php', 'inc'], true)) {
            copy($file, $site . DIRECTORY_SEPARATOR . basename($file));
        }
    }
    $settings = '<?php $host=' . var_export($host, true) . '; $user=' . var_export($user, true)
        . '; $pwd=' . var_export($pwd, true) . '; $dbname=' . var_export($testDb, true) . ';';
    file_put_contents($site . '/settings.php', $settings);
    mysqli_query($db, "CREATE DATABASE `$testDb` CHARACTER SET utf8mb4");
    mysqli_select_db($db, $testDb);
    mysqli_set_charset($db, 'utf8mb4');
    $sql = str_replace('secondvolt', $testDb, file_get_contents(__DIR__ . '/../database.sql'));
    mysqli_multi_query($db, $sql);
    do {
        $result = mysqli_store_result($db);
        if ($result) { mysqli_free_result($result); }
    } while (mysqli_more_results($db) && mysqli_next_result($db));
    verify(mysqli_num_rows(mysqli_query($db, 'SHOW COLUMNS FROM eoi')) === 17, 'SQL import: 17 EOI columns');
    // Fail safely if the test port is already in use.
    $probe = @stream_socket_server('tcp://127.0.0.1:8099', $errno, $error);
    if (!$probe) { throw new RuntimeException('Test port 8099 is already in use'); }
    fclose($probe);
    $server = proc_open([PHP_BINARY, '-d', 'extension_dir=' . ini_get('extension_dir'),
        '-d', 'browscap=' . ini_get('browscap'), '-S', '127.0.0.1:8099', '-t', $site],
        [0 => ['pipe', 'r'], 1 => ['file', $site . '/server.log', 'a'],
         2 => ['file', $site . '/server.log', 'a']], $pipes, $site);
    if (!is_resource($server)) { throw new RuntimeException('Could not start test server'); }
    fclose($pipes[0]);
    for ($i = 0; $i < 100; $i++) {
        $socket = @fsockopen('127.0.0.1', 8099);
        if ($socket) { fclose($socket); break; }
        usleep(50000);
    }
    [$status, $body, $headers] = requestPage('process_eoi.php');
    verify($status === 302 && strpos($headers, 'Location: apply.php') !== false, 'Direct GET redirects');
    [$status, $body] = requestPage('apply.php');
    verify($status === 200 && strpos($body, 'action="process_eoi.php"') !== false
        && strpos($body, 'novalidate') !== false, 'Apply posts locally with browser validation disabled');
    verify(strpos($body, '01/11/2026') === false && strpos($body, 'university test') === false,
        'Apply help matches current processing');
    $valid = ['job_reference' => 'SV101', 'first_name' => 'Test', 'last_name' => 'User',
        'date_of_birth' => '25/03/2004', 'gender' => 'male', 'street_address' => '123 Test Street',
        'suburb' => 'Melbourne', 'state' => 'VIC', 'postcode' => '3000', 'email' => 'qa@example.com',
        'phone' => '0412345678', 'skills' => ['Required experience for the selected role',
        'Git teamwork or UX portfolio for the selected role', 'Accessibility requirements for the selected role'],
        'other_skills' => ''];
    $cases = [
        'empty form' => [],
        'unknown job' => array_replace($valid, ['job_reference' => 'BAD00']),
        'script in name' => array_replace($valid, ['first_name' => '<script>alert(1)</script>']),
        'long name' => array_replace($valid, ['last_name' => str_repeat('A', 21)]),
        'array name' => array_replace($valid, ['first_name' => ['Test']]),
        'invalid calendar date' => array_replace($valid, ['date_of_birth' => '31/04/2004']),
        'non leap year' => array_replace($valid, ['date_of_birth' => '29/02/2001']),
        'future birth' => array_replace($valid, ['date_of_birth' => date('d/m/Y', strtotime('+1 day'))]),
        'old birth' => array_replace($valid, ['date_of_birth' => '01/01/1899']),
        'wrong date format' => array_replace($valid, ['date_of_birth' => '2004-03-25']),
        'invalid gender' => array_replace($valid, ['gender' => 'unknown']),
        'blank address' => array_replace($valid, ['street_address' => '   ']),
        'long suburb' => array_replace($valid, ['suburb' => str_repeat('A', 41)]),
        'invalid state' => array_replace($valid, ['state' => 'XYZ']),
        'postcode mismatch' => array_replace($valid, ['postcode' => '2000']),
        'short postcode' => array_replace($valid, ['postcode' => '300']),
        'invalid email' => array_replace($valid, ['email' => 'invalid']),
        'long email' => array_replace($valid, ['email' => str_repeat('a', 250) . '@example.com']),
        'short phone' => array_replace($valid, ['phone' => '1234567']),
        'long phone' => array_replace($valid, ['phone' => '1234567890123']),
        'missing skills' => array_replace($valid, ['skills' => []]),
        'scalar skills' => array_replace($valid, ['skills' => 'bad']),
        'nested skills' => array_replace($valid, ['skills' => [['bad']]]),
        'extra skill' => array_replace($valid, ['skills' => array_merge($valid['skills'], ['bad'])]),
        'array other skills' => array_replace($valid, ['other_skills' => ['unexpected']]),
        'oversized other skills' => array_replace($valid, ['other_skills' => str_repeat('a', 65536)]),
        'invalid UTF-8' => array_replace($valid, ['other_skills' => "\xFF"]),
    ];
    foreach ($cases as $label => $data) {
        $before = rowCount($db);
        [$status, $body] = requestPage('process_eoi.php', $data);
        verify($status === 422 && rowCount($db) === $before
            && strpos($body, '<script>alert(1)</script>') === false, $label . ': rejected without insert');
    }
    // Recreate only the disposable test table, to exercise the missing-table requirement.
    mysqli_query($db, 'DROP TABLE eoi');
    [$status, $body] = requestPage('process_eoi.php', $valid);
    preg_match('/reference number is\s*<strong>(\d+)<\/strong>/', $body, $match);
    $row = mysqli_fetch_assoc(mysqli_query($db, 'SELECT * FROM eoi ORDER BY EOInumber DESC LIMIT 1'));
    verify($status === 200 && isset($match[1]) && $match[1] === $row['EOInumber']
        && $row['status'] === 'New' && $row['date_of_birth'] === '2004-03-25'
        && $row['skill_experience'] === '1' && $row['skill_tools_portfolio'] === '1'
        && $row['skill_accessibility'] === '1', 'Auto-create table, save valid EOI, confirm ID and New status');
    $text = "O'Reilly \\ SQL'); DROP TABLE eoi; -- <script>alert(1)</script>";
    [$status, $body] = requestPage('process_eoi.php', array_replace($valid,
        ['job_reference' => 'SV205', 'other_skills' => $text]));
    $stored = mysqli_fetch_row(mysqli_query($db, 'SELECT other_skills FROM eoi ORDER BY EOInumber DESC LIMIT 1'))[0];
    verify($status === 200 && $stored === $text && rowCount($db) === 2
        && strpos($body, '<script>alert(1)</script>') === false,
        'SQL and script strings remain plain data; quotes and backslashes preserved');
    foreach (['VIC' => ['3', '8'], 'NSW' => ['1', '2'], 'QLD' => ['4', '9'], 'NT' => ['0'],
        'WA' => ['6'], 'SA' => ['5'], 'TAS' => ['7'], 'ACT' => ['0']] as $state => $prefixes) {
        foreach ($prefixes as $prefix) {
            [$status] = requestPage('process_eoi.php', array_replace($valid,
                ['state' => $state, 'postcode' => $prefix . '000', 'date_of_birth' => '29/02/2000']));
            verify($status === 200, "$state / {$prefix}000 and leap-year DOB accepted (course mapping)");
        }
    }
    file_put_contents($site . '/settings.php', str_replace($testDb, $testDb . '_missing', $settings));
    [$status, $body] = requestPage('process_eoi.php', $valid);
    verify($status === 500 && strpos($body, 'Unable to save your application') !== false
        && strpos($body, $testDb) === false && strpos($body, 'Stack trace') === false,
        'Database failure shows a friendly error without internals');
} finally {
    if (is_resource($server)) { proc_terminate($server); proc_close($server); }
    // This name is generated above, never taken from the application settings.
    mysqli_query($db, "DROP DATABASE IF EXISTS `$testDb`");
    mysqli_close($db);
    foreach (glob($site . '/*') as $file) { unlink($file); }
    if (is_dir($site)) { rmdir($site); }
}
echo "$checks checks, " . count($failures) . " failures" . PHP_EOL;
exit($failures ? 1 : 0);
