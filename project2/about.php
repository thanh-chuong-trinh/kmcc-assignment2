<?php $current_page = 'about'; ?>
<!DOCTYPE html>
<!--
  about.html - About page for Second Volt
  Author: Nguyen Tuan Minh (106393324), Group 6 (KMCC), COS10026
  Acknowledgements:
  - Shared header, footer and styles.css conventions by the team (see README.md).
-->
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Meet Group 6, the team behind the Second Volt website.">
  <meta name="keywords" content="Second Volt, about us, Group 6, team members, COS10026">
  <meta name="author" content="Nguyen Tuan Minh - 106393324">
  <title>About Us - Second Volt</title>
  <link rel="stylesheet" href="styles/style.css">

  <!-- Embedded CSS (assignment requirement) -->
  <style>
    .student-id { letter-spacing: 0.06em; }
  </style>
</head>
<body>
<?php include 'header.inc'; ?>

  <main id="main">
    <section class="container hero" aria-labelledby="page-title">
      <h1 id="page-title">About us</h1>
      <!-- Inline CSS (assignment requirement) -->
      <p style="color: var(--muted);">Short intro to Group 6: who we are and what we built.</p>
    </section>

    <!-- Jump links to the sections below -->
    <nav class="container page-jump" aria-label="On this page">
      <ul>
        <li><a href="#group">Our group</a></li>
        <li><a href="#members">Team members</a></li>
        <li><a href="#photo">Group photo</a></li>
        <li><a href="#facts">Fun facts</a></li>
      </ul>
    </nav>

    <section class="container about-section" id="group" aria-labelledby="group-title">
      <h2 id="group-title">Our group</h2>
      <ul class="group-list">
        <li>Group name: Group 6
          <ul>
            <li>Class day: Wednesday</li>
            <li>Class time: 9:00 - 12:00</li>
          </ul>
        </li>
      </ul>
    </section>

    <section class="container about-section" id="members" aria-labelledby="members-title">
      <h2 id="members-title">Team members</h2>
      <dl class="member-list">
        <div class="member">
          <dt>Trinh Thanh Chuong (<span class="student-id">106392431</span>)</dt>
          <dd>Contribution: index.html, styles.css</dd>
          <dd class="quote"><q lang="vi">Có công mài sắt, có ngày nên kim.</q> (English: <q>With enough perseverance, an iron bar can be ground into a needle.</q>)</dd>
        </div>
        <div class="member">
          <dt>Nguyen Tuan Minh (<span class="student-id">106393324</span>)</dt>
          <dd>Contribution: about.html, about section of styles.css</dd>
          <dd class="quote"><q lang="vi">Không có gì quý hơn độc lập, tự do.</q> (English: <q>Nothing is more precious than independence and freedom.</q>)</dd>
        </div>
        <div class="member">
          <dt>Ngô Mạnh Cường (<span class="student-id">105710919</span>)</dt>
          <dd>Contribution: jobs.html, jobs section of styles.css</dd>
          <dd class="quote"><q lang="vi">Thất bại là mẹ thành công.</q> (English: <q>Failure is the mother of success.</q>)</dd>
        </div>
        <div class="member">
          <dt>Chu Lê Gia Khánh (<span class="student-id">106394068</span>)</dt>
          <dd>Contribution: apply.html, apply section of styles.css</dd>
          <dd class="quote"><q lang="vi">Đi một ngày đàng, học một sàng khôn.</q> (English: <q>Travel a day’s journey, and gain a basketful of wisdom.</q>)</dd>
        </div>
      </dl>
    </section>

    <section class="container about-section" id="photo" aria-labelledby="photo-title">
      <h2 id="photo-title">Group photo</h2>
      <figure class="about-figure">
        <img src="images/group.jpg" alt="Team photo" width="800" height="1000">
        <figcaption>Team Photo</figcaption>
      </figure>
    </section>

    <section class="container about-section" id="facts" aria-labelledby="facts-title">
      <h2 id="facts-title">Fun facts</h2>
      <div class="table-wrap">
        <table class="facts-table">
          <caption>Fun facts about our team</caption>
          <thead>
            <tr>
              <th scope="col">Member</th>
              <th scope="col">Dream job</th>
              <th scope="col">Coding snack</th>
              <th scope="col">Hometown</th>
            </tr>
          </thead>
          <tbody>
            <tr><th scope="row">Trinh Thanh Chuong</th><td>Sports Analyst</td><td>Soft Drinks</td><td>Hanoi</td></tr>
            <tr><th scope="row">Nguyen Tuan Minh</th><td>Game Developer</td><td>Chips</td><td>Bac Ninh</td></tr>
            <tr><th scope="row">Ngô Mạnh Cường</th><td>Web Developer</td><td>Pizza</td><td>Quang Ninh</td></tr>
            <tr><th scope="row">Chu Lê Gia Khánh</th><td>Data Scientist</td><td>Coffee</td><td>Thanh Hoa</td></tr>
          </tbody>
        </table>
      </div>
    </section>
  </main>

<?php include 'footer.inc'; ?>
</body>
</html>
