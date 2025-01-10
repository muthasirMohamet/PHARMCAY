<?php
session_start();

// Clear session
session_unset();
session_destroy();

// Clear cookies
setcookie('username', '', time() - 3600, "/");

// Redirect to login
header("Location: login.php");
exit;
?>
