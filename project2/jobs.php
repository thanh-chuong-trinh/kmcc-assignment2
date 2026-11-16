<?php $current_page = 'jobs'; ?>
<?php
// Load the job listings from the database. A search term in ?q= filters by reference, title or summary.
require 'settings.php';
$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$jobs = [];
$db_error = '';
$conn = @mysqli_connect($host, $user, $pwd, $dbname);
if (!$conn) {
    $db_error = 'Sorry, the job list is not available right now.';
} else {
    $like = '%' . $search . '%';
    $stmt = mysqli_prepare($conn, 'SELECT ref, title, summary, salary, reports_to, location, work_type, responsibilities, essential, preferable FROM jobs WHERE ref LIKE ? OR title LIKE ? OR summary LIKE ? ORDER BY ref');
    mysqli_stmt_bind_param($stmt, 'sss', $like, $like, $like);
    mysqli_stmt_execute($stmt);
    $jobs = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
    mysqli_close($conn);
}

// Print one list item per line of text, with HTML characters escaped.
function print_items($text) {
    foreach (explode("
", $text) as $line) {
        if (trim($line) !== '') {
            echo '          <li>' . htmlspecialchars($line) . "</li>
";
        }
    }
}
?>
<!DOCTYPE html>
<!--
  jobs.html - Position descriptions for Second Volt
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

      <form class="search job-search" action="jobs.php" method="get" role="search">
        <label for="q">Search positions</label>
        <input id="q" name="q" type="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Title or reference">
        <button type="submit">Search</button>
      </form>

<?php if ($db_error !== ''): ?>
      <p><?php echo htmlspecialchars($db_error); ?></p>
<?php elseif (count($jobs) === 0): ?>
      <p>No positions match your search.</p>
<?php endif; ?>
<?php foreach ($jobs as $job): $id = 'job-' . strtolower($job['ref']); ?>
      <section class="job" aria-labelledby="<?php echo htmlspecialchars($id); ?>">
        <p class="job-ref"><span class="visually-hidden">Reference number: </span><?php echo htmlspecialchars($job['ref']); ?></p>
        <h3 id="<?php echo htmlspecialchars($id); ?>"><?php echo htmlspecialchars($job['title']); ?></h3>
        <p><?php echo htmlspecialchars($job['summary']); ?></p>
        <dl class="job-facts">
          <dt>Salary</dt><dd><?php echo htmlspecialchars($job['salary']); ?></dd>
          <dt>Reports to</dt><dd><?php echo htmlspecialchars($job['reports_to']); ?></dd>
          <dt>Location</dt><dd><?php echo htmlspecialchars($job['location']); ?></dd>
          <dt>Work type</dt><dd><?php echo htmlspecialchars($job['work_type']); ?></dd>
        </dl>
        <h4>Key responsibilities</h4>
        <ul>
<?php print_items($job['responsibilities']); ?>
        </ul>
        <h4>Essential requirements</h4>
        <ul>
<?php print_items($job['essential']); ?>
        </ul>
        <h4>Preferable requirements</h4>
        <ul>
<?php print_items($job['preferable']); ?>
        </ul>
        <a class="button" href="apply.php">Apply for <?php echo htmlspecialchars($job['ref']); ?></a>
      </section>
<?php endforeach; ?>
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
