<?php

// include_once '../includes/auth.php';
include_once '../includes/db.php';
include_once '../includes/header.php';

$errors = $_SESSION['errors'] = [];
$messages = [];
// Get the ID from the URL
$id = isset($_GET['id']) ? $_GET['id'] : '';

if ($id === '') {
    echo "Invalid or missing ID parameter";
    exit();
}

// Fetch the data for the selected ID
$result = $conn->query("SELECT * FROM events_csv WHERE id = $id");

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $eventName = $row['event_name'];
    $performanceCode = $row['performance_code'];
    $csvFile = $row['csv_file'];
    $dateModified = $row['date_modified'];
} else {
    // Handle the case where no data is found for the given ID
    $_SESSION['errors'] = "No data found for ID: $id";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $id = $_POST['id'];
    $eventName = $_POST['eventName'];
    $performanceCode = $_POST['performanceCode'];

    // Check if a new CSV file is uploaded
    if (isset($_FILES['newCsvFile']) && $_FILES['newCsvFile']['error'] === UPLOAD_ERR_OK) {
        $newCsvFileName = $_FILES['newCsvFile']['name'];
        $newCsvTmpName = $_FILES['newCsvFile']['tmp_name'];

        // Move the uploaded file to a designated folder
        $uploadDirectory = '../csv-uploads/';
        $newUploadedFilePath = $uploadDirectory . $eventName . '_' . date('Y-m-d') . '_' . uniqid() . '.csv';

        if (move_uploaded_file($newCsvTmpName, $newUploadedFilePath)) {
            // Update the data in the database with the new CSV file
            $sql = "UPDATE events_csv SET event_name = '$eventName', performance_code = '$performanceCode', csv_file = '$newUploadedFilePath', event_table_name = '', date_modified = NOW() WHERE id = $id";

            if ($conn->query($sql) === TRUE) {
                $_SESSION['successMessage'] = "Data updated successfully!";
                // Redirect back to edit-events-csv.php
                header("Location: edit-events-csv.php?id=$id");
                exit(); 
            } else {
              $_SESSION['errors'] = ["Database error: " . $conn->error];
            }
        } else {
          $_SESSION['errors'] = ["File upload failed."];
        }
    } else {
        // Update the data in the database without changing the CSV file
        $sql = "UPDATE events_csv SET event_name = '$eventName', performance_code = '$performanceCode' WHERE id = $id";

        if ($conn->query($sql) === TRUE) {
            $_SESSION['successMessage'] = "Data updated successfully!";
            // Redirect back to edit-events-csv.php
            header("Location: edit-events-csv.php?id=$id");
            exit(); 
        } else {
          $_SESSION['errors'] = ["Database error: " . $conn->error];
        }
    }
} else {
  $_SESSION['errors'] = ["Invalid request method"];
}
?>

<div class="container px-lg-5 mt-5">
    <h1 class="mb-5">Edit Events Data</h1>

    <div class="errors">
        <?php 
        if (isset($_SESSION['successMessage'])) {

            echo '<div class="d-block opacity-100 toast mb=4" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-body">
                        '.$_SESSION['successMessage'].'
                    </div>
                </div>';
                unset($_SESSION['successMessage']);
        } else if (isset($_SESSION['errors'])) {
            foreach ($_SESSION['errors'] as $error) {
                echo '<div class="d-block opacity-100 toast mb-4" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-body">
                        '.$error.'</div>
                    </div>' ;   
            }
        }
        ?>
    </div>

    <form class="w-75" action="" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $id; ?>">

        <div class="mb-3">
            <label for="eventName" class="form-label">Event Name:</label>
            <input type="text" name="eventName" id="eventName" class="form-control" value="<?php echo $eventName; ?>" required>
        </div>

        <div class="mb-3">
            <label for="performanceCode" class="form-label">Performance Code:</label>
            <input type="text" name="performanceCode" id="performanceCode" class="form-control" value="<?php echo $performanceCode; ?>" required>
        </div>

        <div class="mb-3">
            <label for="csvFile" class="form-label">Current CSV File:</label>
            <input type="text" name="csvFile" id="csvFile" class="form-control" value="<?php echo $csvFile; ?>" disabled>
            <small class="form-text text-muted">CSV File cannot be edited.</small>
        </div>

        <div class="mb-3">
            <label for="newCsvFile" class="form-label">Upload New CSV File:</label>
            <input type="file" name="newCsvFile" id="newCsvFile" class="form-control">
        </div>
        <div class="d-flex align-items-centers"> 
            <div class="m-2 py-4">
                <button type="submit" class="btn btn-primary">Update Data</button>
            </div>
            <div class="m-2 py-4">
                <button type="button" onclick="history.go(-2)" class="btn btn-warning">Go Back</button>
            </div>
        </div>
    </form>
</div>

<?php
include_once '../includes/footer.php';
?>
