<?php
// Initialize the session
session_start();

// Check if user is actually logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Clear the session from globals
$_SESSION = array();

// Clear the session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Destroy the session
session_destroy();

// Clear any other cookies set by the application
setcookie('remember_me', '', time() - 3600, '/');

// Prevent caching of this page
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

// Redirect to login page with proper URL encoding
header("location: " . filter_var('login.php', FILTER_SANITIZE_URL));
exit;
?>

