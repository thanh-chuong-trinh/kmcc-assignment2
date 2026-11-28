<?php
$current_page = 'jobs';

/*
 * Load the positions from the jobs table (see the jobs section of database.sql).
 * - Every job field comes from its own column.
 * - The search bar uses method="get" and a prepared statement, so the search text never goes into the SQL string.
 */
require 'settings.php';

/* Turn a text column with one item per line into <li> items. */
function list_items($text)
{
    $out = '';
    foreach (preg_split('/\R/', (string) $text) as $line) {
        $line = trim($line);
        if ($line !== '') {
            $out .= '          <li>' . htmlspecialchars($line) . "</li>\n";
        }
    }
    return $out;
}

/* Read and tidy the search term (server-side validation). */
$raw_q = $_GET['q'] ?? '';
$q = is_string($raw_q) ? trim(preg_replace('/\s+/u', ' ', $raw_q) ?? '') : '';
$q_too_long = preg_match('/^(.{60})./us', $q, $prefix);
if ($q_too_long) {
    $q = $prefix[1];
}

/* Query the database. */
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$jobs  = [];
$error = false;

try {
    $conn = mysqli_connect($host, $user, $pwd, $dbname);
    mysqli_set_charset($conn, 'utf8mb4');
    $cols = 'job_ref, title, summary, salary, reports_to, location, work_type, responsibilities, essential, preferable';

    if ($q === '') {
        $stmt = mysqli_prepare($conn, "SELECT $cols FROM jobs ORDER BY job_ref");
    } else {
        $stmt = mysqli_prepare(
            $conn,
            "SELECT $cols FROM jobs
             WHERE job_ref LIKE ? ESCAPE '!' OR title LIKE ? ESCAPE '!' OR summary LIKE ? ESCAPE '!'
                OR salary LIKE ? ESCAPE '!' OR reports_to LIKE ? ESCAPE '!' OR location LIKE ? ESCAPE '!'
                OR work_type LIKE ? ESCAPE '!' OR responsibilities LIKE ? ESCAPE '!'
                OR essential LIKE ? ESCAPE '!' OR preferable LIKE ? ESCAPE '!'
             ORDER BY job_ref"
        );
        /* Only the two outer % characters should act as wildcards. */
        $like = '%' . strtr($q, ['!' => '!!', '%' => '!%', '_' => '!_']) . '%';
        mysqli_stmt_bind_param($stmt, 'ssssssssss', $like, $like, $like, $like, $like, $like, $like, $like, $like, $like);
    }

    mysqli_stmt_execute($stmt);
    $jobs = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
} catch (mysqli_sql_exception $e) {
    $error = true;
    http_response_code(503);
}
?>
<!DOCTYPE html>
<!--
  jobs.php - Position descriptions for Second Volt, loaded from the jobs table
  Author: Ngo Manh Cuong (105710919), Group 6 (KMCC), COS10026
  Acknowledgements:
  - Shared header, footer and styles.css conventions by the team (see README.md).
  - Acknowledgement of Country wording follows the guidance linked in the assignment brief.
-->
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Jobs at Second Volt, an online store for tested, used electronics in Melbourne, Australia.">
  <meta name="keywords" content="Second Volt, jobs, careers, front-end developer, UX designer, e-commerce">
  <meta name="author" content="Ngo Manh Cuong - 105710919">
  <title>Jobs - Second Volt</title>
  <link rel="stylesheet" href="styles/style.css">

  <!-- Embedded CSS (assignment requirement): badge for the job reference number -->
  <style>
    .job-ref { display: inline-block; padding: 0.1rem 0.6rem; color: #fff; background: var(--accent); border-radius: var(--radius); font-size: 0.8rem; font-weight: 600; }
  </style>
</head>
<body>
<?php include 'header.inc'; ?>

  <main id="main">
    <section class="container hero" aria-labelledby="page-title">
      <h1 id="page-title">Work at Second Volt</h1>
      <!-- Inline CSS (assignment requirement) -->
      <p style="color: var(--muted);">Help us give used electronics a second life.</p>
    </section>

    <section class="container section jobs" aria-labelledby="jobs-title">
      <h2 id="jobs-title">Open positions</h2>

      <aside class="jobs-aside" aria-labelledby="apply-steps-title">
        <h3 id="apply-steps-title">How to apply</h3>
        <ol>
          <li>Choose a position below.</li>
          <li>Note its five-character reference number.</li>
          <li>Fill in the <a href="apply.php">application form</a>.</li>
          <li>We reply to every applicant within 10 working days.</li>
        </ol>
        <h3>Benefits</h3>
        <ul>
          <li>Hybrid working: 3 days in the office, 2 from home</li>
          <li>$2,500 yearly learning budget</li>
          <li>30% staff discount on refurbished devices</li>
          <li>Paid volunteer day each year</li>
        </ul>
        <p>Questions? <a href="mailto:careers@secondvolt.com.au">careers@secondvolt.com.au</a></p>
      </aside>

      <!-- Search bar: GET, so a search can be bookmarked and shared.
           The #job-search fragment brings the reloaded page back to the search bar and results. -->
      <form class="job-search" id="job-search" method="get" action="jobs.php#job-search" role="search" aria-label="Search jobs">
        <label for="q">Search jobs</label>
        <div class="job-search-row">
          <input type="search" id="q" name="q" maxlength="60" value="<?php echo htmlspecialchars($q); ?>" placeholder="Try a title, skill or reference, e.g. design or SV101">
          <button type="submit">Search</button>
<?php if ($q !== '') { ?>
          <a class="job-search-clear" href="jobs.php#job-search">Show all jobs</a>
<?php } ?>
        </div>
      </form>

      <p class="job-count" role="status">
<?php
if ($error) {
    echo "        We cannot load the positions right now. Please try again later.\n";
} elseif ($q === '') {
    echo '        Showing all ' . count($jobs) . ' ' . (count($jobs) === 1 ? 'position' : 'positions') . ".\n";
} elseif (count($jobs) === 0) {
    echo '        No positions match &ldquo;' . htmlspecialchars($q) . "&rdquo;. Try a shorter word, or <a href=\"jobs.php#job-search\">show all jobs</a>.\n";
} else {
    echo '        ' . count($jobs) . ' ' . (count($jobs) === 1 ? 'position matches' : 'positions match') . ' &ldquo;' . htmlspecialchars($q) . "&rdquo;.\n";
}
if ($q_too_long) {
    echo "        <br>Your search was shortened to 60 characters.\n";
}
?>
      </p>

<?php foreach ($jobs as $job) {
    $ref = htmlspecialchars($job['job_ref']);
    $id  = strtolower($ref);
?>
      <section class="job" aria-labelledby="<?php echo $id; ?>">
        <p class="job-ref"><span class="visually-hidden">Reference number: </span><?php echo $ref; ?></p>
        <h3 id="<?php echo $id; ?>"><?php echo htmlspecialchars($job['title']); ?></h3>
        <p><?php echo htmlspecialchars($job['summary']); ?></p>
        <dl class="job-facts">
          <dt>Salary</dt><dd><?php echo htmlspecialchars($job['salary']); ?></dd>
          <dt>Reports to</dt><dd><?php echo htmlspecialchars($job['reports_to']); ?></dd>
          <dt>Location</dt><dd><?php echo htmlspecialchars($job['location']); ?></dd>
          <dt>Work type</dt><dd><?php echo htmlspecialchars($job['work_type']); ?></dd>
        </dl>
        <h4>Key responsibilities</h4>
        <ul>
<?php echo list_items($job['responsibilities']); ?>
        </ul>
        <h4>Essential requirements</h4>
        <ul>
<?php echo list_items($job['essential']); ?>
        </ul>
        <h4>Preferable requirements</h4>
        <ul>
<?php echo list_items($job['preferable']); ?>
        </ul>
        <a class="button" href="apply.php">Apply for <?php echo $ref; ?></a>
      </section>

<?php } ?>
    </section>

    <!-- Acknowledgement and inclusive employment statement (assignment requirement) -->
    <section class="container section commitment" aria-labelledby="commitment-title">
      <h2 id="commitment-title">Our commitment</h2>
      <p>Second Volt acknowledges the Wurundjeri Woi-wurrung people of the Kulin Nation, the Traditional Owners of the land in Naarm (Melbourne) where our team works. We pay our respects to their Elders past and present.</p>
      <p>We encourage Aboriginal and Torres Strait Islander people to apply for any of our roles. If you would like a chat with a team member before you apply, or changes to how your interview runs, email <a href="mailto:careers@secondvolt.com.au">careers@secondvolt.com.au</a>.</p>
      <p>We include this on our jobs page because this is where people decide whether to join us, and we want every applicant to know they are welcome.</p>
    </section>
  </main>

<?php include 'footer.inc'; ?>
</body>
</html>
