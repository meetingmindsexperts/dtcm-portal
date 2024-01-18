
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
// Get the current server protocol (http or https)
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';

// Get the server name (localhost in AMPPS)
$servername = $_SERVER['SERVER_NAME'];

// Get the server port
$port = $_SERVER['SERVER_PORT'];

// Check if the port is a standard HTTP/HTTPS port
$port = ($port === '80' || $port === '443') ? '' : (':' . $port);

// Get the base directory (if your project is not in the root directory)
$basedir = '/dtcm-new'; // Change this if your project is in a subdirectory

// Combine all components to form the base URL
$baseUrl = "{$protocol}://{$servername}{$port}{$basedir}";

// Use $baseUrl in your HTML link

?>