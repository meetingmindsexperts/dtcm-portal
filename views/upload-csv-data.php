<?php
include_once '../includes/db.php';
include_once '../includes/header.php';

// Initialize an array to store messages and errors
$messages = [];
$errors = [];

// Get the ID from the URL
$id = isset($_GET['id']) ? $_GET['id'] : '';

if ($id === '') {
    $errors[] = "Invalid or missing ID parameter";
} else {
    $sql = "SELECT * FROM events_csv WHERE id = $id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $eventName = $row['event_name'];
        $performanceCode = $row['performance_code'];
        $csvFilePath = $row['csv_file'];

        // Read CSV file content
        $csvData = file_get_contents($csvFilePath);
        $csvRows = explode("\n", $csvData);

        // Create a new table to store CSV data
        $newTableName = strtolower(str_replace(' ', '', $eventName)).uniqid();

        $createTableSql = "CREATE TABLE IF NOT EXISTS $newTableName (
                                id INT AUTO_INCREMENT PRIMARY KEY,
                                eventsair_id VARCHAR(255) NOT NULL,
                                title VARCHAR(255),
                                firstname VARCHAR(255) NOT NULL,
                                lastname VARCHAR(255) NOT NULL,
                                nationality VARCHAR(5),
                                email VARCHAR(255) NOT NULL,
                                phonenumber VARCHAR(50),
                                countrycode VARCHAR(5) NOT NULL,
                                registrationtype VARCHAR(50),
                                pricetype VARCHAR(10),
                                orderid VARCHAR(10),
                                barcode VARCHAR(20),
                                date_modified TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                            )";
        //echo "Create Table SQL: $createTableSql<br>";

        if ($conn->query($createTableSql)) {
            // Read CSV file content
            $csvData = file_get_contents($csvFilePath);

            if ($csvData === false) {
                $errors[] = "Error reading CSV file: $csvFilePath";
            } else {
                $csvRows = explode("\n", $csvData);

                // Insert or update CSV data into the new table
                foreach ($csvRows as $index => $csvRow) {
                    if (empty($csvRow)) {
                        continue; // Skip empty rows
                    }

                    $rowData = str_getcsv($csvRow);

                    // Skip the first row (header)
                    if ($index === 0) {
                        continue;
                    }

                    $eventsairId = $rowData[0];
                    $existingRecord = $conn->query("SELECT * FROM $newTableName WHERE eventsair_id = '$eventsairId'")->fetch_assoc();

                    if ($existingRecord) {
                        // Update the existing record
                        $updateSql = "UPDATE $newTableName 
                                      SET eventsair_id = '$eventsairId', title = '$rowData[1]', firstname = '$rowData[2]', lastname = '$rowData[3]', nationality = '$rowData[4]', 
                                          email = '$rowData[5]', phonenumber = '$rowData[6]', countrycode = '$rowData[7]',
                                          registrationtype = '$rowData[9]', pricetype = '$rowData[10]', orderid = '$rowData[12]', barcode = '$rowData[13]'
                                      WHERE eventsair_id = '$eventsairId'";

                        if (!$conn->query($updateSql)) {
                            $errors[] = "Error updating data in the new table: " . $conn->error;
                        }
                    } else {
                        // Insert a new record
                        $insertSql = "INSERT INTO $newTableName 
                                      (eventsair_id, title, firstname, lastname, nationality, email, phonenumber, countrycode, barcode, registrationtype, pricetype, barcode, orderid) 
                                      VALUES (
                                          '$rowData[0]', '$rowData[1]', '$rowData[2]', '$rowData[3]', '$rowData[4]', '$rowData[5]', '$rowData[6]', '$rowData[7]', '$rowData[8]', '$rowData[9]', '$rowData[10]'
                                      )";

                        if (!$conn->query($insertSql)) {
                            $errors[] = "Error inserting data into the new table: " . $conn->error;
                        }
                    }
                }
                $messages[] = "CSV data successfully inserted or updated in the new table.";
                
                // Display success message
            }
        } else {
            // Display error message if table creation fails
            $errors[] = "Error creating the new table: " . $conn->error;
        }
    }
    echo "
    <div class='container px-lg-5 mt-5'>
        <div class='d-flex justify-content-between'>
            <h1 class='mb-5'>View CSV Data</h1>
            <div>
            <a href='". $baseUrl."/views/view-csv-data.php?id={$id}' class='d-inline-block btn btn-warning'>Back to CSV</a>
            </div>
        </div>";

    // Display messages and errors as Bootstrap toasts
    foreach ($messages as $message) {
        echo "<div class='d-block opacity-100 toast align-items-center text-bg-success border-0' role='alert' aria-live='assertive' aria-atomic='true'>
                <div class='toast-header'>
                    <strong class='mr-auto'>Success</strong>
                    <button type='button' class='ml-2 mb-1 close' data-dismiss='toast' aria-label='Close'>
                        <span aria-hidden='true'>&times;</span>
                    </button>
                </div>
                <div class='toast-body'>$message</div>
            </div>";
    }

    foreach ($errors as $error) {
        echo "<div class='d-block opacity-100  toast align-items-center text-bg-warning border-0' role='alert' aria-live='assertive' aria-atomic='true'>
                <div class='toast-header'>
                    <strong class='mr-auto'>Error</strong>
                    <button type='button' class='ml-2 mb-1 close' data-dismiss='toast' aria-label='Close'>
                        <span aria-hidden='true'>&times;</span>
                    </button>
                </div>
                <div class='toast-body'>$error</div>
            </div>";
    }
}

include_once '../includes/footer.php';
?>
