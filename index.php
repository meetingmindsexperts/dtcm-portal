<?php
// Include necessary files
include_once 'includes/header.php';

// Check if the user is logged in
session_start();
if (isset($_SESSION['user_id'])) {
    // User is logged in, redirect to dashboard.php
    header('Location: dashboard.php');
    exit();
} else {
    // User is not logged in, redirect to login.php
    header('Location: login.php');
    exit();
}

// Include necessary files
include_once 'includes/footer.php';
?>
