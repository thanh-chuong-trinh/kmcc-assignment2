<?php
/*
 * header.inc - shared page top (doctype, <head>, skip link, site header, main nav)
 * Built from the Part 1 header markup so every page looks the same.
 *
 * Set these variables BEFORE including this file:
 *   $page_title   - text for <title>            (default: "SecondVolt")
 *   $current_page - "home", "jobs", "apply", "about" or "" (highlights the nav item)
 *   $body_class   - optional class for <body>
 */
if (session_status() === PHP_SESSION_NONE) { session_start(); }

$page_title   = $page_title   ?? 'SecondVolt';
$current_page = $current_page ?? '';
$body_class   = $body_class   ?? '';
$is_manager   = !empty($_SESSION['user_id']);

if (!function_exists('nav_current')) {
    function nav_current($page, $current) {
        return $page === $current ? ' aria-current="page"' : '';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="SecondVolt sells tested, used electronics.">
  <title><?= htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') ?></title>
  <link rel="stylesheet" href="styles/style.css">
</head>
<body<?= $body_class !== '' ? ' class="' . htmlspecialchars($body_class, ENT_QUOTES, 'UTF-8') . '"' : '' ?>>
  <a class="skip-link" href="#main">Skip to main content</a>

  <header class="site-header">
    <div class="container header-row">
      <a class="logo" href="index.php">Second<span>Volt</span></a>
      <form class="search" action="jobs.php" method="get" role="search">
        <label class="visually-hidden" for="q">Search jobs</label>
        <input id="q" name="q" type="search" placeholder="Search jobs">
        <button type="submit">Search</button>
      </form>
      <nav aria-label="Account">
        <ul class="inline-list">
          <?php if ($is_manager): ?>
          <li><a href="manage.php">Manage</a></li>
          <li><a href="logout.php">Log out</a></li>
          <?php else: ?>
          <li><a href="login.php">Sign in</a></li>
          <?php endif; ?>
        </ul>
      </nav>
    </div>
    <nav class="container main-nav" aria-label="Main">
      <ul>
        <li><a href="index.php"<?= nav_current('home', $current_page) ?>>Home</a></li>
        <li><a href="jobs.php"<?= nav_current('jobs', $current_page) ?>>Jobs</a></li>
        <li><a href="apply.php"<?= nav_current('apply', $current_page) ?>>Apply</a></li>
        <li><a href="about.php"<?= nav_current('about', $current_page) ?>>About</a></li>
      </ul>
    </nav>
  </header>
