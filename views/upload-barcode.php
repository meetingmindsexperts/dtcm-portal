<?php
// upload-barcode.php

include_once '../includes/db.php'; // Include your database connection file
//include_once '../includes/header.php';

// Initialize messages and errors
$errors = $_SESSION['errors'] = [];
$messages = $_SESSION['messages'] = [];

// Retrieve POST data
$data = json_decode(file_get_contents("php://input"), true);

// Process the data as needed
$id = isset($data['id']) ? (int)$data['id'] : 0;
$tableData = isset($data['tableData']) ? json_encode($data['tableData']) : '';
$eventName = $data['eventName'];
$newTableName = strtolower(str_replace(' ', '', $eventName)).uniqid();

// Perform database operations
try {
    // Check if required data is present
    if ($id > 0 && $tableData !== '' && $eventName !== '') {
        // Create the table if it doesn't exist
        $tableCreationQuery = "CREATE TABLE IF NOT EXISTS $newTableName (
            id INT PRIMARY KEY,
            table_data TEXT,
            date_added DATETIME
        );";

        //echo $tableCreationQuery;  // Add this line for debugging

        if ($conn->query($tableCreationQuery) === TRUE) {
            $messages[] = "Table created or already exists.";
        } else {
            $errors[] = "Table creation error: " . $conn->error;
        }
        // Example: Insert data into a table
        $sql = "INSERT INTO $newTableName (id, table_data, date_added) VALUES ($id, '$tableData', NOW());";
        //echo $sql;  // Add this line for debugging

        if ($conn->query($sql) === TRUE) {
            $messages[] = "Data updated successfully!";
        } else {
            $errors[] = "Database error: " . $conn->error;
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
