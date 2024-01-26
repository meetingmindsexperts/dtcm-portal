<?php 
include_once "db.php";
include_once "functions.php";
//include_once 'auth.php';

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DTCM CSV</title>
    <link rel="icon" type="image/x-icon" href="../assets/meeting_minds_experts_logo.jpeg">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <!-- Include custom styles -->
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>/css/styles.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <!-- Change the href attribute to point to login.php -->
        <a class="navbar-brand" href="<?php echo $baseUrl; ?>/dashboard.php">DTCM Dashboard</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-between" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $baseUrl; ?>/views/view-events.php">Events</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $baseUrl; ?>/views/csv-upload.php">CSV Upload</a>
                </li>
                
            </ul>
            <div class="nav-item logout_btn">
                <a class="btn btn-primary nav-divnk" href="<?php echo $baseUrl; ?>/logout.php">Logout</a>
            </div>
        </div>
        
    </div>
</nav>

<div class="container mt-5">
