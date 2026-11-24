<?php
// OpenAI Codex assisted with implementation, review and QA; see README.md.
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: apply.php");
    exit;
}
$errors = [];

function getValue($name) {
    global $errors;
    if (!isset($_POST[$name])) {
        return "";
    }
    if (!is_string($_POST[$name]) || !mb_check_encoding($_POST[$name], "UTF-8")) {
        $errors[] = "Invalid text submitted for " . str_replace("_", " ", $name) . ".";
        return "";
    }
    // Canvas requires slash stripping; SQL safety still uses prepared statements.
    return trim(stripslashes($_POST[$name]));
}
$job_reference = getValue("job_reference");
$first_name = getValue("first_name");
$last_name = getValue("last_name");
$date_of_birth = getValue("date_of_birth");
$gender = getValue("gender");
$street_address = getValue("street_address");
$suburb = getValue("suburb");
$state = getValue("state");
$postcode = getValue("postcode");
$email = getValue("email");
$phone = getValue("phone");
$other_skills = getValue("other_skills");
$skills = [];

if (isset($_POST["skills"]) && is_array($_POST["skills"])) {
    $skills = $_POST["skills"];
}

$skill_experience = in_array(
    "Required experience for the selected role",
    $skills,
    true
) ? 1 : 0;

$skill_tools_portfolio = in_array(
    "Git teamwork or UX portfolio for the selected role",
    $skills,
    true
) ? 1 : 0;

$skill_accessibility = in_array(
    "Accessibility requirements for the selected role",
    $skills,
    true
) ? 1 : 0;

$valid_job_references = ["SV101", "SV205"];

if (!in_array($job_reference, $valid_job_references, true)) {
    $errors[] = "Please select a valid job reference: SV101 or SV205.";
}
if (!preg_match("/^[A-Za-z]{1,20}$/", $first_name)) {
    $errors[] = "First name must contain 1 to 20 letters (A-Z only).";
}

if (!preg_match("/^[A-Za-z]{1,20}$/", $last_name)) {
    $errors[] = "Last name must contain 1 to 20 letters (A-Z only).";
}
$date_of_birth_sql = "";

if (!preg_match("/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/", $date_of_birth)) {
    $errors[] = "Date of birth must use the format dd/mm/yyyy.";
} else {
    $parts = explode("/", $date_of_birth);

    $day = (int) $parts[0];
    $month = (int) $parts[1];
    $year = (int) $parts[2];

    if (!checkdate($month, $day, $year)) {
        $errors[] = "Please enter a real calendar date.";
    } elseif ($year < 1900) {
        $errors[] = "Date of birth must be from 01/01/1900 onwards.";
    } else {
        $date_of_birth_sql = sprintf(
            "%04d-%02d-%02d",
            $year,
            $month,
            $day
        );

        if ($date_of_birth_sql > date("Y-m-d")) {
            $errors[] = "Date of birth cannot be in the future.";
        }
    }
}

$valid_genders = [
    "female",
    "male",
    "non-binary",
    "prefer-not-to-say"
];

if (!in_array($gender, $valid_genders, true)) {
    $errors[] = "Please select a valid gender option.";
}

if ($street_address === "" || mb_strlen($street_address, "UTF-8") > 40) {
    $errors[] = "Street address is required and must not exceed 40 characters.";
}

if ($suburb === "" || mb_strlen($suburb, "UTF-8") > 40) {
    $errors[] = "Suburb or town is required and must not exceed 40 characters.";
}

$state_postcodes = [
    "VIC" => ["3", "8"],
    "NSW" => ["1", "2"],
    "QLD" => ["4", "9"],
    "NT"  => ["0"],
    "WA"  => ["6"],
    "SA"  => ["5"],
    "TAS" => ["7"],
    "ACT" => ["0"]
];

if (!isset($state_postcodes[$state])) {
    $errors[] = "Please select a valid state or territory.";
} elseif (!preg_match("/^[0-9]{4}$/", $postcode)) {
    $errors[] = "Postcode must contain exactly 4 digits.";
} elseif (!in_array($postcode[0], $state_postcodes[$state], true)) {
    $errors[] = "Postcode does not match the selected state or territory.";
}

if ($email === "") {
    $errors[] = "Email address is required.";
} elseif (strlen($email) > 254) {
    $errors[] = "Email address must not exceed 254 characters.";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Please enter a valid email address.";
}

if (!preg_match("/^[0-9]{8,12}$/", $phone)) {
    $errors[] = "Phone number must contain 8 to 12 digits, without spaces or symbols.";
}

if (
    count($skills) !== 3 ||
    $skill_experience !== 1 ||
    $skill_tools_portfolio !== 1 ||
    $skill_accessibility !== 1
) {
    $errors[] = "Please confirm all three skill requirements for your selected role.";
}

// MySQL TEXT holds at most 65,535 bytes, including multi-byte UTF-8 characters.
if (strlen($other_skills) > 65535) {
    $errors[] = "Other skills is too long. Please shorten your description.";
}

if (count($errors) > 0) {
    http_response_code(422);
    $current_page = "apply";
    ?>
    <!doctype html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Application errors | Second Volt</title>
        <link rel="stylesheet" href="styles/style.css">
    </head>
    <body>
        <?php include "header.inc"; ?>

        <main id="main" class="container">
            <h1>Please check your application</h1>

            <ul>
                <?php foreach ($errors as $error): ?>
                    <li>
                        <?php echo htmlspecialchars($error, ENT_QUOTES, "UTF-8"); ?>
                    </li>
                <?php endforeach; ?>
            </ul>

            <p><a href="apply.php">Return to the application form</a></p>
        </main>

        <?php include "footer.inc"; ?>
    </body>
    </html>
    <?php
    exit;
}

require_once "settings.php";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$eoi_number = null;

try {
    // Connect using the group's shared database settings.
    $conn = mysqli_connect($host, $user, $pwd, $dbname);
    mysqli_set_charset($conn, "utf8mb4");

    // The assignment requires creating the table if it does not exist.
    $create_table = "
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
            status ENUM('New', 'Current', 'Final')
                NOT NULL DEFAULT 'New'
        )
    ";

    mysqli_query($conn, $create_table);

    // Keep applicant data separate from the SQL command.
    $insert_sql = "
        INSERT INTO eoi (
            job_reference,
            first_name,
            last_name,
            date_of_birth,
            gender,
            street_address,
            suburb,
            state,
            postcode,
            email,
            phone,
            skill_experience,
            skill_tools_portfolio,
            skill_accessibility,
            other_skills
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ";

    $stmt = mysqli_prepare($conn, $insert_sql);

    mysqli_stmt_bind_param(
        $stmt,
        "sssssssssssiiis",
        $job_reference,
        $first_name,
        $last_name,
        $date_of_birth_sql,
        $gender,
        $street_address,
        $suburb,
        $state,
        $postcode,
        $email,
        $phone,
        $skill_experience,
        $skill_tools_portfolio,
        $skill_accessibility,
        $other_skills
    );

    mysqli_stmt_execute($stmt);

    // Read the ID generated for this application.
    $eoi_number = mysqli_insert_id($conn);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
} catch (mysqli_sql_exception $error) {
    error_log($error->getMessage());
    http_response_code(500);
}

$current_page = "apply";
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Application result | Second Volt</title>
    <link rel="stylesheet" href="styles/style.css">
</head>
<body>
    <?php include "header.inc"; ?>

    <main id="main" class="container">
        <?php if ($eoi_number !== null): ?>
            <h1>Application submitted</h1>

            <p>
                Thank you,
                <?php echo htmlspecialchars($first_name, ENT_QUOTES, "UTF-8"); ?>.
                Your application has been saved.
            </p>

            <p>
                Your application reference number is
                <strong><?php echo (int) $eoi_number; ?></strong>.
                Please keep this number for your records.
            </p>

            <p><a href="jobs.php">View available jobs</a></p>
        <?php else: ?>
            <h1>Unable to save your application</h1>

            <p>
                A database error occurred.
                Please try again later.
            </p>

            <p><a href="apply.php">Return to the application form</a></p>
        <?php endif; ?>
    </main>

    <?php include "footer.inc"; ?>
</body>
</html>
