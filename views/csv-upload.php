<?php
// Include necessary files
include_once '../includes/functions.php';
include_once '../includes/header.php';

// Get all events from the database
$events = getEvents();
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
    <form class="w-75" action="../csv-process.php" method="post" enctype="multipart/form-data">
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
                $result = $conn->query("SELECT * FROM events_csv");

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['id'] . "</td>";
                        echo "<td>" . $row['event_name'] . "</td>";
                        echo "<td>" . $row['performance_code'] . "</td>";
                        echo "<td>" . $row['csv_file'] . "</td>";
                        echo "<td>" . $row['date_modified'] . "</td>";
                        echo "<td><a class='btn btn-success'href='view-csv-data.php?id=".$row['id']."'>View Data</a></td>";
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
                </td>
            </tbody>
        </table>
    </div>

</div><!--  Container -->

<?php
include_once '../includes/footer.php';
?>