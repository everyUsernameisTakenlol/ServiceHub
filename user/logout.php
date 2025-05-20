<?php
session_start(); // Start the session
session_unset(); // Unset all session variables
session_destroy(); // Destroy the session
// Prevent browser from caching the page and going back after logout
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Location: ../login.php"); // Redirect to login page
// Prevent browser from caching the page and going back after logout


exit;
?>
