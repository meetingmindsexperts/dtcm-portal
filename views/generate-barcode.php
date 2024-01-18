<?php
include_once '../includes/db.php';
include_once '../includes/curl-requests.php'; // Include the curl-requests.php file

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
        echo json_encode(['error' => 'Invalid data']);
        exit;
    }

    // Retrieve the customer data from the request
    $customerData = $data->customerData;

    // Obtain or pass the access token to the function
    $accessToken = getAccessToken(); // You may need to adjust this based on your logic

    // Create Basket for the record
    $basketId = createBasket($accessToken);

    // Create Customer for the record
    $customerDetails = createCustomer($accessToken, $customerData);
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
?>
