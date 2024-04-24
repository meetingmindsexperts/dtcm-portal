<?php
// upload-barcode.php

include_once '../includes/db.php'; // Include your database connection file
//include_once '../includes/header.php';

// Initialize messages and errors
$errors = $_SESSION['errors'] = [];
$messages = $_SESSION['messages'] = [];

// Retrieve POST data
$data = json_decode(file_get_contents("php://input"), true);

// var_dump($data);

// Process the data as needed
$id = (isset($data['id']) || isset($_GET['id'])) ? $data['id'] : $_GET['id'];
$tableData = isset($data['tableData']) ? json_encode($data['tableData']) : '';
$eventName = $data['eventName'];
$tableName = strtolower(str_replace(' ', '', $eventName));
$newTableName = strtolower(str_replace(' ', '', $eventName)) . uniqid();

// Perform database operations
try {
    // Check if required data is present
    if ($id > 0 && $tableData !== '' && $eventName !== '') {
        // Create the table if it doesn't exist
        $tableCreationQuery = "CREATE TABLE IF NOT EXISTS `$newTableName`(
            id INT PRIMARY KEY,
            table_data TEXT,
            date_added DATETIME
        );";

        //echo $tableCreationQuery;  // Add this line for debugging

        if ($conn->query($tableCreationQuery) === TRUE) {
            $messages[] = "Table created or already exists.";

            // Example: Insert data into a table
            $updateSql = "UPDATE events_csv SET event_table_name = '$tableName', event_table_data = '$tableData' WHERE id = $id";

            if ($conn->query($updateSql) === TRUE) {
                $messages[] = "Events_csv table updated successfully with: " . $event_table_name;
            } else {
                $errors[] = "Error updating table name in the Events_csv table: " . $conn->error;
            }
        } else {
            $errors[] = "Table creation error: " . $conn->error;
        }
    } else {
        $errors[] = "Invalid or missing data.";
    }
} catch (Exception $e) {
    $errors[] = "Error message: " . $e->getMessage();
} finally {
    // Close the database connection
    $conn->close();
}

// Output messages and errors as JSON response
if (!empty($messages)) {
    echo json_encode(['status' => 'success', 'messages' => $messages, 'tableName' => $newTableName]);
    unset($_SESSION['messages']);
} else if (!empty($errors)) {
    echo json_encode(['status' => 'error', 'errors' => $errors]);
}
?>
<?php //include_once '../includes/footer.php'; ?>
