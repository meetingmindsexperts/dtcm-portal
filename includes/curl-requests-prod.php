<?php
$baseURL = 'https://et-api.det.gov.ae/';
$sellerCode = 'AMMEE1';
$apiKey ='sz4pbntphrygvseq2dr98vh8';
$client_secret =  "57gZ_cbatt9P2hcUmZC9vQCi9vtBUCf0enK8Z_8Z";
$apiUrl = "https://et-api.det.gov.ae/adfs/oauth2/token?api_key=sz4pbntphrygvseq2dr98vh8";

function makeApiRequest($url, $method, $accessToken, $data = null) {
    $curl = curl_init();

    $headers = ['Authorization: Bearer ' . $accessToken, 'Content-Type: application/json'];

    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_POSTFIELDS => $data ? json_encode($data) : null,
        CURLOPT_HTTPHEADER => $headers,
    ]);

    $response = curl_exec($curl);

    if ($response === false) {
        echo "cURL Error: " . curl_error($curl);
        echo "cURL Error Code: " . curl_errno($curl);
        curl_close($curl);
        return null;
    }

    curl_close($curl);

    $decodedResponse = json_decode($response, true);

    if ($decodedResponse === null) {
        echo "Failed to decode JSON response. Response: " . $response;
        return null;
    }

    return $decodedResponse;
}

function getAccessToken() {
    global $apiUrl;

    $data = [
        "client_id" => "AMMEE1",
        "client_secret" => "57gZ_cbatt9P2hcUmZC9vQCi9vtBUCf0enK8Z_8Z",
        "scope" => "profile",
        "grant_type" => "client_credentials",
        "resource" => "https://et-api.det.gov.ae",
    ];

    $response = makeApiRequest($apiUrl, 'POST', null, $data);

    if (isset($response['access_token'])) {
        return $response['access_token'];
    } else {
        echo "Access token retrieval failed. Response: " . json_encode($response);
        return null;
    }
}

$performance_code = "PVEN26JAN2024T";
$pricetypecode = "A";
$area = "SDELEGATECOMP";
function createBasket($accessToken, $performance_code, $area, $pricetypecode) {
    $url = 'https://et-api.det.gov.ae/baskets?api_key=sz4pbntphrygvseq2dr98vh8';

    $data = [
        "Channel" => "API",
        "Seller" => "AMMEE1",
        "Performancecode" => $performance_code,
        "Area" =>  $area,
        "autoReduce" => false,
        "holdcode" => "",
        "Demand" => [
            [
                "PriceTypeCode" => $pricetypecode,
                "Quantity" => 1,
                "Admits" => 1,
                "offerCode" => "",
                "qualifierCode" => "",
                "entitlement" => "",
                "Customer" => [],
            ],
        ],
    ];

    $response = makeApiRequest($url, 'POST', $accessToken, $data);

    if (!$response) {
        return null;
    }

    if (!isset($response['id'])) {
        echo "Invalid response from createBasket function. Response: " . json_encode($response);
        return null;
    }

    return $response['id'];
}

$customerData = [
    "firstname" => "Krishna",
    "lastname" => "Test ",
    "nationality" => "IN",
    "email" => "krishna@test.com",
    "phoneNumber" => "8908978667",
    "countryCode" => "AE",
];
function createCustomer($accessToken, $customerData) {
    $url = 'https://et-api.det.gov.ae/customers?api_key=sz4pbntphrygvseq2dr98vh8';

    $response = makeApiRequest($url, 'POST', $accessToken, $customerData);

    if (!$response) {
        echo "No response from createCustomer function.";
        return null;
    }

    // Check if the expected indices are present in the response
    if (!isset($response['id']) || !isset($response['account']) || !isset($response['aFile'])) {
        echo "Invalid response from createCustomer function. Response: " . json_encode($response);
        return null;
    }

    return [
        'customerId' => $response['id'],
        'account' => $response['account'],
        'aFile' => $response['aFile'],
    ];
}

function purchaseBasket($accessToken, $basketId, $customerId, $customerAccount, $aFile) {
    $url = 'https://et-api.det.gov.ae/baskets/' . $basketId . '/purchase?api_key=sz4pbntphrygvseq2dr98vh8';

    $data = [
        "Seller" => "AMMEE1",
        "customer" => [
            "ID" => $customerId,
            "Account" => $customerAccount,
            "AFile" => $aFile,
        ],
        "Payments" => [
            [
                "Amount" => "0",  //update this based on the basket id
                "MeansOfPayment" => "EXTERNAL",
            ],
        ],
    ];

    $response = makeApiRequest($url, 'POST', $accessToken, $data);

    if (!$response) {
        return null;
    }

    if (!isset($response['orderId'])) {
        echo "Invalid response from purchaseBasket function. Response: " . json_encode($response);
        return null;
    }

    return $response['orderId'];
}

function getOrderDetails($accessToken, $orderId) {
    $url = 'https://et-api.det.gov.ae/orders/' . $orderId . '?api_key=sz4pbntphrygvseq2dr98vh8';

    return makeApiRequest($url, 'GET', $accessToken);
}

// Collect all errors in this array
$errors = [];

$accessToken = getAccessToken();
if (!$accessToken) {
    $errors[] = "Failed to retrieve access token.";
}

$basketId = createBasket($accessToken, $performance_code, $area, $pricetypecode);
if (!$basketId) {
    $errors[] = "Failed to create   .";
}

$customerDetails = createCustomer($accessToken, $customerData);
if (!$customerDetails) {
    $errors[] = "Failed to create customer.";
} 
// Extracting relevant details from $customerDetails
$customerId = $customerDetails['customerId'];
$customerAccount = $customerDetails['account'];
$aFile = $customerDetails['aFile'];

$orderId = purchaseBasket($accessToken, $basketId, $customerId, $customerAccount, $aFile);
if (!$orderId) {
    $errors[] = "Failed to purchase basket.";
} else {
    $barcodeDetails = getOrderDetails($accessToken, $orderId);
    if (!$barcodeDetails) {
        $errors[] = "Failed to retrieve order details.";
    }
}


// $accessToken = getAccessToken();
// echo "Access Token: " . $accessToken . "\n";

// $basketId = createBasket($accessToken, $area, $pricetypecode);
// echo "Basket ID: " . $basketId . "\n";

// $customerDetails = createCustomer($accessToken, $customerData);

// if ($customerDetails) {
//     // Extracting relevant details from $customerDetails
//     $customerId = $customerDetails['customerId'];
//     $customerAccount = $customerDetails['account'];
//     $aFile = $customerDetails['aFile'];

//     echo "Customer ID: " . $customerId . "\n";
//     echo "Customer Account: " . $customerAccount . "\n";
//     echo "AFile: " . $aFile . "\n";

//     $orderId = purchaseBasket($accessToken, $basketId, $customerId, $customerAccount, $aFile);

//     if ($orderId) {
//         echo "Order ID: " . $orderId . "\n";

//         $barcodeDetails = getOrderDetails($accessToken, $orderId);

//         if ($barcodeDetails) {
//             echo "Barcode: " . $barcodeDetails['orderLines'][0]['orderLineItems'][0]['barcode'] . "\n";
//         }
//     }
// }
?>
