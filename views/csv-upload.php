<?php
// Include necessary files
// include_once '../includes/auth.php';
include_once '../includes/db.php';
include_once '../includes/functions.php';
include_once '../includes/header.php';

// Get all events from the database
//$events = getEvents();

// Initialize an error array
$errors = $_SESSION['errors'] =[];
$successMessage = '';

// Validate form data
$eventName = isset($_POST['eventName']) ? mysqli_real_escape_string($conn, trim($_POST['eventName'])) : '';
$performanceCode = isset($_POST['performanceCode']) ? mysqli_real_escape_string($conn, trim($_POST['performanceCode'])) : '';

// Validate file upload
if (isset($_FILES['csvFile']) && $_FILES['csvFile']['error'] === UPLOAD_ERR_OK) {
    $csvFileName = $_FILES['csvFile']['name'];
    $csvTmpName = $_FILES['csvFile']['tmp_name'];

    // Move uploaded file to a designated folder with a unique filename
    $uploadDirectory = '../csv-uploads/';
    $uploadedFilePath = $uploadDirectory . $eventName . '_' . date('Y-m-d') . '_' . uniqid() . '.csv';

    if (move_uploaded_file($csvTmpName, $uploadedFilePath)) {
        // Escape user input for SQL query
        $eventName = mysqli_real_escape_string($conn, $eventName);
        $performanceCode = mysqli_real_escape_string($conn, $performanceCode);

        // Insert data into the database
        $sql = "INSERT INTO events_csv (event_name, performance_code, csv_file, event_table_name, date_added, date_modified) VALUES ('$eventName', '$performanceCode', '$uploadedFilePath', '', NOW(), NOW())";
        
        if ($conn->query($sql) === TRUE) {
            $successMessage = "Data inserted successfully!";
        } else {
            $errors[] = "Database error: " . $conn->error;
        }
    } else {
        // Print more information about the file upload failure
        $errors[] = "File upload failed. Error code: " . $_FILES['csvFile']['error'];
        $errors[] = "Uploaded file name: " . $csvFileName;
        $errors[] = "Uploaded file temporary name: " . $csvTmpName;
        $errors[] = "Destination file path: " . $uploadedFilePath;
        $errors[] = "Upload directory: " . $uploadDirectory;
    }
} else {
    // Handle file upload errors
    $errors[] = "File upload failed with error code: " . $_FILES['csvFile']['error'];
}

// Save errors in the session
$_SESSION['errors'] = $errors;
$_SESSION['successMessage'] = $successMessage;
?>

<div class="container px-lg-5 mt-5">
    <?php

    // Check if there are errors in the session
    if (isset($_SESSION['successMessage'])) {
    echo '<div classs="container">
                '.$_SESSION['successMessage'].'
        </div>';
        unset($_SESSION['successMessage']);
    }
    if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])) {
        // Print errors
        foreach ($_SESSION['errors'] as $error) {
            echo "<p>Error: $error</p>";
        }
        
        // Clear the errors from the session
        unset($_SESSION['errors']);
    } 

    ?>
    <h1 class="mb-5">CSV Upload</h1>
    <form class="w-75" action="" method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="eventName" class="form-label">Event Name:</label>
            <input type="text" name="eventName" id="eventName" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="performanceCode" class="form-label">Performance Code:</label>
            <input type="text" name="performanceCode" id="performanceCode" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="csvFile" class="form-label">Choose CSV file:</label>
            <input type="file" name="csvFile" id="csvFile" class="form-control" required>
        </div>
        <div class="py-4">
            <button type="submit" class="btn btn-primary">Upload to Database</button>
        </div>
    </form>


    <!-- show the available csv files -->

    <?php
        // Set the number of items to display per page
        $itemsPerPage = 4;

        // Get the current page number from the URL, default to 1 if not set
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;

        // Calculate the offset for the query based on the current page
        $offset = ($page - 1) * $itemsPerPage;

        // Fetch data with pagination
        $result = $conn->query("SELECT * FROM events_csv ORDER BY date_modified DESC LIMIT $offset, $itemsPerPage");

        // Display the table
        ?>
        <div class="py-5">
            <h2>List of CSV files</h2>
            <table class="table table-striped table-responsive">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Event Name</th>
                        <th>Performance Code</th>
                        <th>CSV File</th>
                        <th>Date Modified</th>
                        <th>View Data</th>
                        <th>Edit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row['id'] . "</td>";
                            echo "<td>" . $row['event_name'] . "</td>";
                            echo "<td>" . $row['performance_code'] . "</td>";
                            echo "<td>" . $row['csv_file'] . "</td>";
                            echo "<td>" . $row['date_modified'] . "</td>";
                            echo "<td><a class='btn btn-success' href='view-csv-data.php?id=".$row['id']."'>View Data</a></td>";
                            echo "<td>
                                    <form method='get' action='edit-events-csv.php'>
                                        <input type='hidden' name='id' value='{$row['id']}'>
                                        <button type='submit' class='btn btn-link'>Edit</button>
                                    </form>
                                </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7'>No rows in events_csv table.</td></tr>";
                    }
                    
                    ?>
                </tbody>
            </table>

            <!-- Pagination links -->
            <div class="pagination">
                <?php
                // Calculate total number of pages
                $totalPages = ceil(countEvents() / $itemsPerPage);

                // Display pagination links
                for ($i = 1; $i <= $totalPages; $i++) {
                    echo "<a class='btn btn-outline-primary m-1' href='?page=$i'>$i</a>";
                }
                $conn->close();
                ?>
            </div>



</div><!--  Container -->

<?php
include_once '../includes/footer.php';
?>