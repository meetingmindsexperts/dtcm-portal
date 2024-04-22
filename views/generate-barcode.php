<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include_once '../includes/db.php';

//include_once '../includes/curl-requests.php'; 
include_once '../includes/curl-requests-prod.php';

    // Get Order Details for the record
    // $barcodeDetails = getOrderDetails($accessToken, $orderId);
    $barcode = $barcodeDetails['orderLines'][0]['orderLineItems'][0]['barcode'];

    // Check if the table already exists
    $checkTableQuery = "SHOW TABLES LIKE '$event_table_name'";
    $result = $conn->query($checkTableQuery);

    if ($result->num_rows == 0) {
        // Table doesn't exist, create it
        $tableCreationQuery = "CREATE TABLE $event_table_name (
            id INT PRIMARY KEY AUTO_INCREMENT,
            eventsair_id BIGINT,
            first_name VARCHAR(255),
            last_name VARCHAR(255),
            email VARCHAR(255),
            event_name VARCHAR(255),
            basketId VARCHAR(255),
            customerId VARCHAR(255),
            orderId VARCHAR(255),
            barcode VARCHAR(255),
            date_added DATETIME DEFAULT CURRENT_TIMESTAMP
        )";

        if ($conn->query($tableCreationQuery) === TRUE) {
            $messages[] = "New Table created.";
        } else {
            $errors[] = "Table creation error: " . $conn->error;
        }
    }

    // Insert data into your table
    $insertSql = "INSERT INTO $event_table_name (eventsair_id, first_name, last_name, email, event_name, basketId, customerId, orderId, barcode, date_added) 
        VALUES ('$eventsairid', '$first_name', '$last_name', '$email','$event_table_name', '$basketId', '$customerId', '$orderId', '$barcode', NOW())";

    if ($conn->query($insertSql) === TRUE) {
        $messages[] = "Data inserted into the table successfully.";
    } else {
        $errors[] = "Error inserting data into the table: " . $conn->error;
    }

// Output the details as JSON
echo json_encode([
    'basketId' => $basketId,
    'customerId' => $customerId,
    'account' => $customerAccount,
    'aFile' => $aFile,
    'orderId' => $orderId,
    'barcode' => $barcode,
]);



// Output errors as JSON
if (!empty($errors)) {
    echo json_encode(['errors' => $errors]);
    echo json_encode(['messages' => $messages]);

}
    ?>
