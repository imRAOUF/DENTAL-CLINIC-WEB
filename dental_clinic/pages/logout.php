<?php
// Start session
session_start();

// Clear session variables
session_unset();

// Destroy the session
session_destroy();

// Redirect to the login page
header("Location: ../login.php");
exit();
?>
