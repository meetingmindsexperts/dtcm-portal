<?php
session_start();

// Define a function to get the base URL
function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $servername = $_SERVER['SERVER_NAME'];
    $port = $_SERVER['SERVER_PORT'];

    if ($servername === 'localhost') {
        $basedir = '/dtcm-new'; // Change this if your project is in a subdirectory

    } else {
        $basedir = ''; // Change this if your project is in a subdirectory
    }   

    $port = ($port === '80' || $port === '443') ? '' : (':' . $port);

    return "{$protocol}://{$servername}{$port}{$basedir}";
}

// Set the base URL
$baseUrl = getBaseUrl();

?>
