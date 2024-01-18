<?php
// Start the session
session_start();
include_once "includes/init.php";
// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to the login page
$url = $baseUrl.'/login.php';
header("Location: ".$url );
exit();
?>
