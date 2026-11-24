<?php
/*
 * logout.php - Ends the manager's session.
 * No HTML output: clear session data, remove the cookie, destroy the session,
 * then redirect to the login page.
 */
session_start();

$_SESSION = [];                                   // clear all session data

if (ini_get('session.use_cookies')) {             // delete the session cookie cause cookies only last for a bit
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
              $p['path'], $p['domain'], $p['secure'], $p['httponly']);
}

session_destroy();

$msg = (isset($_GET['timeout']) && $_GET['timeout'] === '1') ? 'timeout' : 'loggedout';
header('Location: login.php?msg=' . $msg);
exit;
