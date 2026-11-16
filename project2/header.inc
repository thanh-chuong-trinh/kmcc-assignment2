<?php
// header.inc - skip link, logo and main menu, shared by every page.
// Each page sets $current_page (e.g. 'index') before including this file.
?>
  <!-- Skip link: lets keyboard and screen reader users jump past the header -->
  <a class="skip-link" href="#main">Skip to main content</a>

  <header class="site-header">
    <div class="container header-row">
      <a class="logo" href="index.php"><img src="images/logo.svg" width="36" height="36" alt=""><span class="logo-text">Second<span>Volt</span></span></a>
      <p class="header-tagline">Tech revived. <span>Value amplified.</span></p>
    </div>
<?php include 'nav.inc'; ?>
  </header>
