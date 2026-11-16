<?php
// process_eoi.php - receives the application form from apply.php, checks every field
// on the server, saves a valid Expression of Interest (EOI) in the database and shows its number.
$current_page = 'apply';
require 'settings.php';

// Block direct access: only a POST from the form (with its main fields) is accepted.
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['job_reference'], $_POST['first_name'], $_POST['email'])) {
    header('Location: apply.php');
    exit;
}

// Sanitise: trim spaces and strip slashes. Text is escaped with htmlspecialchars() whenever it is printed.
function clean($value) {
    return trim(stripslashes($value));
}
function field($name) {
    return isset($_POST[$name]) && !is_array($_POST[$name]) ? clean($_POST[$name]) : '';
}

$job_reference  = strtoupper(field('job_reference'));
$first_name     = field('first_name');
$last_name      = field('last_name');
$date_of_birth  = field('date_of_birth');
$gender         = field('gender');
$street_address = field('street_address');
$suburb         = field('suburb');
$state          = field('state');
$postcode       = field('postcode');
$email          = field('email');
$phone          = field('phone');
$other_skills   = field('other_skills');
$skills_posted  = isset($_POST['skills']) && is_array($_POST['skills']) ? $_POST['skills'] : [];

$allowed_skills = [
    'Required experience for the selected role',
    'Git teamwork or UX portfolio for the selected role',
    'Accessibility requirements for the selected role',
];
// The first digit of a postcode must fit the state.
$postcode_start = [
    'VIC' => ['3', '8'], 'NSW' => ['1', '2'], 'QLD' => ['4', '9'], 'NT'  => ['0'],
    'WA'  => ['6'],      'SA'  => ['5'],      'TAS' => ['7'],      'ACT' => ['0', '2'],
];

$errors = [];

// Connect first: the job reference is checked against the jobs table.
$conn = @mysqli_connect($host, $user, $pwd, $dbname);
if (!$conn) {
    $errors[] = 'The application could not be saved because the database is not available. Please try again later.';
} else {
    $stmt = mysqli_prepare($conn, 'SELECT ref FROM jobs WHERE ref = ?');
    mysqli_stmt_bind_param($stmt, 's', $job_reference);
    mysqli_stmt_execute($stmt);
    if (mysqli_num_rows(mysqli_stmt_get_result($stmt)) === 0) {
        $errors[] = 'Job reference number must match a position on the Jobs page, for example SV101.';
    }
}

if (!preg_match('/^[A-Za-z]{1,20}$/', $first_name)) {
    $errors[] = 'First name must be 1 to 20 letters.';
}
if (!preg_match('/^[A-Za-z]{1,20}$/', $last_name)) {
    $errors[] = 'Last name must be 1 to 20 letters.';
}

// Date of birth: dd/mm/yyyy, a real calendar date from 1900 up to today.
$dob_sql = '';
if (preg_match('#^(\d{2})/(\d{2})/(\d{4})$#', $date_of_birth, $m) && checkdate((int)$m[2], (int)$m[1], (int)$m[3])
    && (int)$m[3] >= 1900 && sprintf('%04d-%02d-%02d', $m[3], $m[2], $m[1]) <= date('Y-m-d')) {
    $dob_sql = sprintf('%04d-%02d-%02d', $m[3], $m[2], $m[1]);
} else {
    $errors[] = 'Date of birth must be a real date as dd/mm/yyyy, from 01/01/1900 to today.';
}

if (!in_array($gender, ['female', 'male', 'non-binary', 'prefer-not-to-say'], true)) {
    $errors[] = 'Please choose one of the gender options.';
}
if ($street_address === '' || strlen($street_address) > 40) {
    $errors[] = 'Street address must be 1 to 40 characters.';
}
if ($suburb === '' || strlen($suburb) > 40) {
    $errors[] = 'Suburb or town must be 1 to 40 characters.';
}
if (!isset($postcode_start[$state])) {
    $errors[] = 'Please choose a state or territory.';
}
if (!preg_match('/^[0-9]{4}$/', $postcode)) {
    $errors[] = 'Postcode must be exactly 4 digits.';
} elseif (isset($postcode_start[$state]) && !in_array($postcode[0], $postcode_start[$state], true)) {
    $errors[] = 'Postcode ' . $postcode . ' does not match the state ' . $state . '.';
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 100) {
    $errors[] = 'Email must look like name@example.com.';
}
if (!preg_match('/^[0-9]{8,12}$/', $phone)) {
    $errors[] = 'Phone number must be 8 to 12 digits.';
}
// All three skill boxes must be ticked, and only the known values are accepted.
$skills = array_values(array_unique(array_map('clean', array_filter($skills_posted, 'is_string'))));
if (count(array_diff($allowed_skills, $skills)) > 0 || count(array_diff($skills, $allowed_skills)) > 0) {
    $errors[] = 'Please confirm all three skill requirements.';
}
if (strlen($other_skills) > 1000) {
    $errors[] = 'Other skills must be 1000 characters or fewer.';
}

$eoi_number = 0;
if (count($errors) === 0) {
    // Create the table the first time this runs.
    mysqli_query($conn, "CREATE TABLE IF NOT EXISTS eoi (
        EOInumber      INT AUTO_INCREMENT PRIMARY KEY,
        job_reference  VARCHAR(5)   NOT NULL,
        first_name     VARCHAR(20)  NOT NULL,
        last_name      VARCHAR(20)  NOT NULL,
        date_of_birth  DATE         NOT NULL,
        gender         VARCHAR(20)  NOT NULL,
        street_address VARCHAR(40)  NOT NULL,
        suburb         VARCHAR(40)  NOT NULL,
        state          VARCHAR(3)   NOT NULL,
        postcode       CHAR(4)      NOT NULL,
        email          VARCHAR(100) NOT NULL,
        phone          VARCHAR(12)  NOT NULL,
        skills         TEXT         NOT NULL,
        other_skills   TEXT         NOT NULL,
        status         ENUM('New', 'Current', 'Final') NOT NULL DEFAULT 'New'
    )");
    $skills_text = implode(', ', $skills);
    $stmt = mysqli_prepare($conn, 'INSERT INTO eoi (job_reference, first_name, last_name, date_of_birth, gender, street_address, suburb, state, postcode, email, phone, skills, other_skills) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    mysqli_stmt_bind_param($stmt, 'sssssssssssss', $job_reference, $first_name, $last_name, $dob_sql, $gender, $street_address, $suburb, $state, $postcode, $email, $phone, $skills_text, $other_skills);
    if (mysqli_stmt_execute($stmt)) {
        $eoi_number = mysqli_insert_id($conn);
    } else {
        $errors[] = 'Sorry, your application could not be saved. Please try again.';
    }
}
if ($conn) {
    mysqli_close($conn);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Application received | Second Volt</title>
  <link rel="stylesheet" href="styles/style.css">
</head>
<body>
<?php include 'header.inc'; ?>

  <main id="main">
    <section class="container section" aria-labelledby="result-title">
<?php if (count($errors) === 0): ?>
      <h1 id="result-title">Application received</h1>
      <p>Thank you, <?php echo htmlspecialchars($first_name); ?>. Your application for <?php echo htmlspecialchars($job_reference); ?> has been saved.</p>
      <p>Your EOI number is <strong><?php echo (int)$eoi_number; ?></strong>. Please keep it for your records.</p>
      <a class="button" href="jobs.php">Back to jobs</a>
<?php else: ?>
      <h1 id="result-title">We could not save your application</h1>
      <p>Please fix the following and submit the form again:</p>
      <ul>
<?php foreach ($errors as $error): ?>
        <li><?php echo htmlspecialchars($error); ?></li>
<?php endforeach; ?>
      </ul>
      <a class="button" href="apply.php">Back to the application form</a>
<?php endif; ?>
    </section>
  </main>

<?php include 'footer.inc'; ?>
</body>
</html>
