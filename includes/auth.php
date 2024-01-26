<?php
session_start();
include_once 'init.php';

// // Debugging
// echo '<pre>';
// print_r($_SESSION);
// echo '</pre>';

// Redirect to the login page if the user is not logged in
if (!isset($_SESSION['username'])) {
    $url = getBaseUrl() . "/login.php";
    header("Location: $url");
    exit();
}
// Your page content goes here
?>
