<?php $current_page = 'jobs'; ?>
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

      <section class="job" aria-labelledby="sv101">
        <p class="job-ref"><span class="visually-hidden">Reference number: </span>SV101</p>
        <h3 id="sv101">Front-end Developer</h3>
        <p>Build fast, accessible product and trade-in pages for our online store.</p>
        <dl class="job-facts">
          <dt>Salary</dt><dd>AUD $90,000 - $105,000 a year, plus super</dd>
          <dt>Reports to</dt><dd>Head of Digital Retail</dd>
          <dt>Location</dt><dd>Melbourne CBD, hybrid</dd>
          <dt>Work type</dt><dd>Full-time, permanent</dd>
        </dl>
        <h4>Key responsibilities</h4>
        <ul>
          <li>Build product listing, product detail and checkout pages with semantic HTML and responsive CSS.</li>
          <li>Keep every page accessible to WCAG 2.1 AA.</li>
          <li>Improve page speed on phones, where most of our customers shop.</li>
          <li>Review code with the team through GitHub pull requests.</li>
        </ul>
        <h4>Essential requirements</h4>
        <ul>
          <li>3+ years building websites with HTML and CSS.</li>
          <li>Comfortable using Git in a team.</li>
          <li>Experience testing pages with a keyboard and a screen reader.</li>
        </ul>
        <h4>Preferable requirements</h4>
        <ul>
          <li>Experience on an e-commerce website.</li>
          <li>Interest in reuse and reducing e-waste.</li>
        </ul>
        <a class="button" href="apply.php">Apply for SV101</a>
      </section>

      <section class="job" aria-labelledby="sv205">
        <p class="job-ref"><span class="visually-hidden">Reference number: </span>SV205</p>
        <h3 id="sv205">UX Designer</h3>
        <p>Make shopping, checkout and trade-ins easy for every customer.</p>
        <dl class="job-facts">
          <dt>Salary</dt><dd>AUD $95,000 - $110,000 a year, plus super</dd>
          <dt>Reports to</dt><dd>Director of Customer Experience</dd>
          <dt>Location</dt><dd>Melbourne CBD, hybrid</dd>
          <dt>Work type</dt><dd>Full-time, permanent</dd>
        </dl>
        <h4>Key responsibilities</h4>
        <ul>
          <li>Run user tests and accessibility checks with real customers.</li>
          <li>Design wireframes and prototypes for new features, such as the trade-in quote.</li>
          <li>Write clear product grading labels so buyers know a device's condition.</li>
          <li>Share findings with developers and the product team.</li>
        </ul>
        <h4>Essential requirements</h4>
        <ul>
          <li>2+ years of UX design experience.</li>
          <li>A portfolio that shows your design process.</li>
          <li>Working knowledge of WCAG 2.1.</li>
        </ul>
        <h4>Preferable requirements</h4>
        <ul>
          <li>Experience designing for screen reader users.</li>
          <li>Experience with an online store or marketplace.</li>
        </ul>
        <a class="button" href="apply.php">Apply for SV205</a>
      </section>
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
