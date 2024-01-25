<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include_once '../includes/db.php';
include_once '../includes/curl-requests.php'; // Include the curl-requests.php file//

// Initialize an array to store messages and errors
$messages = [];
$errors = [];

$id = isset($_GET['id']) ? $_GET['id'] : '';

if ($id === '') {
    $errors[] = "Invalid or missing ID parameter";
} else {
    // Get the data from the POST request
    $data = json_decode(file_get_contents("php://input"));

    // Check if the required data is present
    if (!isset($data->customerData)) {
        $errors[] = 'Invalid Customer data';
    }
    if (!isset($data->basketData)) {
        $errors[] = 'Invalid Basket data';
    }
    if (!isset($data->performance_code)) {
        $errors[] = 'Invalid Performance code';
    }
    if (!isset($data->eventTableName)) {
        $errors[] = 'Invalid eventTableName';
    }

    if (empty($errors)) {

        $customer_data = $data->customerData;
        $eventsairid = $customer_data->eventsairid;
        $first_name = $customer_data->firstname;
        $last_name = $customer_data->lastname;
        $email = $customer_data->email;
        // Retrieve the basket data from the request
        $basketData = $data->basketData;
        // Retrieve the specifics of basket data from the request
        $area = $basketData->area; 
        $pricetypecode = $basketData->pricetypecode;

        //get performance code 
        $performance_code = $data->performance_code;
        //get Event Table Name code 
        $event_table_name = $data->eventTableName;
        // Obtain or pass the access token to the function
        $accessToken = getAccessToken(); // You may need to adjust this based on your logic

        // Create Basket for the record
        $basketId = createBasket($accessToken, $performance_code, $area, $pricetypecode);

        // Create Customer for the record
        $customerDetails = createCustomer($accessToken, $data->customerData);

        if (isset($customerDetails['error'])) {
            $errors[] = $customerDetails['error'];
        } else {
            $customerId = $customerDetails['customerId'];
            $customerAccount = $customerDetails['account'];
            $aFile = $customerDetails['aFile'];

            // Purchase Basket for the record
            $orderId = purchaseBasket($accessToken, $basketId, $customerId, $customerAccount, $aFile);

            // Get Order Details for the record
            $barcodeDetails = getOrderDetails($accessToken, $orderId);
            $barcode = $barcodeDetails['orderLines'][0]['orderLineItems'][0]['barcode'];

            // Check if the table already exists
            $checkTableQuery = "SHOW TABLES LIKE '$event_table_name'";
            $result = $conn->query($checkTableQuery);

            if ($result->num_rows == 0) {
                // Table doesn't exist, create it
                $tableCreationQuery = "CREATE TABLE $event_table_name (
                    id INT PRIMARY KEY AUTO_INCREMENT,
                    eventsairid BIGINT,
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
                    $messages[] = "Table created.";

                    // Insert data into your table
                    $insertSql = "INSERT INTO $event_table_name (eventsairid, first_name, last_name, email, event_name, basketId, customerId, orderId, barcode, date_added) 
                        VALUES ('$eventsairid', '$first_name', '$last_name', '$email','$event_table_name', '$basketId', '$customerId', '$orderId', '$barcode', NOW())";

                    if ($conn->query($insertSql) === TRUE) {
                        $messages[] = "Data inserted into the table successfully.";
                    } else {
                        $errors[] = "Error inserting data into the table: " . $conn->error;
                    }
                } else {
                    $errors[] = "Table creation error: " . $conn->error;
                }
            } else {
                // Table already exists, assume subsequent rows will have the same structure
                // Insert data into your table
                $insertSql = "INSERT INTO $event_table_name (eventsairid, first_name, last_name, email, event_name, basketId, customerId, orderId, barcode, date_added) 
                    VALUES ('$eventsairid', '$first_name', '$last_name', '$email','$event_table_name', '$basketId', '$customerId', '$orderId', '$barcode', NOW())";

                if ($conn->query($insertSql) === TRUE) {
                    $messages[] = "Data inserted into the table successfully.";
                } else {
                    $errors[] = "Error inserting data into the table: " . $conn->error;
                }
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
        }
    }
}

// Output errors as JSON
if (!empty($errors)) {
    echo json_encode(['errors' => $errors]);
}
?>
