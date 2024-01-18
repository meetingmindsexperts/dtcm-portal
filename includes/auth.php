<?php

include_once 'init.php';
// Define a function to check if the user is logged in
function checkLoggedIn() {
    // Redirect to the login page if the user is not logged in
    if (!isset($_SESSION['user_id'])) {
        $url = getBaseUrl() . "/login.php";
        header("Location: $url");
        exit();
    }
}


// Call the function to check if the user is logged in
checkLoggedIn();


?>