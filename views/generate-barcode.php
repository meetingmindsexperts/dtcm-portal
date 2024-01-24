<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include_once '../includes/db.php';
include_once '../includes/curl-requests.php'; // Include the curl-requests.php file//
// include_once '../includes/curl-requests-prod.php'; // Include the curl-requests.php file

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

    if (empty($errors)) {
        // Retrieve the basket data from the request
        $basketData = $data->basketData;
        // Retrieve the specifics of basket data from the request
        $area = $basketData->area; 
        $pricetypecode = $basketData->pricetypecode;

        //get performance code 
        $performance_code = $data->performance_code;

        // Obtain or pass the access token to the function
        $accessToken = getAccessToken(); // You may need to adjust this based on your logic

        // Create Basket for the record
        $basketId = createBasket($accessToken, $performance_code, $area, $pricetypecode);

        // Create Customer for the record
        $customerDetails = createCustomer($accessToken, $customerData);

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
