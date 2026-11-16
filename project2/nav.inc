<?php
// nav.inc - main menu. Marks the current page with aria-current="page".
$menu = [
    'index' => 'Home',
    'jobs'  => 'Jobs',
    'apply' => 'Apply',
    'about' => 'About',
];
?>
    <nav class="container main-nav" aria-label="Main">
      <ul>
<?php foreach ($menu as $page => $label): ?>
        <li><a href="<?php echo $page; ?>.php"<?php echo ($page === $current_page) ? ' aria-current="page"' : ''; ?>><?php echo $label; ?></a></li>
<?php endforeach; ?>
      </ul>
    </nav>
