<?php

// $baseURL = 'https://et-api.det.gov.ae/';
// $sellerCode = 'AMMEE1';
// $apiKey ='sz4pbntphrygvseq2dr98vh8';
// $client_secret =  "57gZ_cbatt9P2hcUmZC9vQCi9vtBUCf0enK8Z_8Z";
// $apiUrl = "https://et-api.det.gov.ae/adfs/oauth2/token?api_key=sz4pbntphrygvseq2dr98vh8";

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
        $nationality = $customer_data->nationality;
        $phonenumber = $customer_data->phonenumber;
        $countrycode = $customer_data->countrycode;

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
                throw new Exception("cURL Error: " . curl_error($curl));
            }

            curl_close($curl);

            $decodedResponse = json_decode($response, true);

            if ($decodedResponse === null) {
                throw new Exception("Failed to decode JSON response. Response: " . $response);
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
                throw new Exception("Access token retrieval failed. Response: " . json_encode($response));
            }
        }

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

            if (!isset($response['id'])) {
                throw new Exception("Invalid response from createBasket function. Response: " . json_encode($response));
            }

            return $response['id'];
        }

        // Add similar error handling and logging for other API functions (createCustomer, purchaseBasket, getOrderDetails)

        try {
            $accessToken = getAccessToken();
            $basketId = createBasket($accessToken, $performance_code, $area, $pricetypecode);

            // Handle successful basket creation
            if ($basketId) {
                // Proceed with other API functions (createCustomer, purchaseBasket, getOrderDetails)
                $customerData = [
                    "firstname" => $first_name,
                    "lastname" => $last_name,
                    "nationality" => $nationality,
                    "email" => $email,
                    "phoneNumber" => $phonenumber,
                    "countryCode" => $countrycode,
                ];
                
                function createCustomer($accessToken, $customerData) {
                    $url = 'https://et-api.det.gov.ae/customers?api_key=sz4pbntphrygvseq2dr98vh8';
                
                    $response = makeApiRequest($url, 'POST', $accessToken, $customerData);
                
                    if (!$response) {
                        throw new Exception("No response from createCustomer function.");
                    }
                
                    // Check if the expected indices are present in the response
                    if (!isset($response['id']) || !isset($response['account']) || !isset($response['aFile'])) {
                        throw new Exception("Invalid response from createCustomer function. Response: " . json_encode($response));
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
                                "Amount" => "0",  // Update this based on the basket id
                                "MeansOfPayment" => "EXTERNAL",
                            ],
                        ],
                    ];
                
                    $response = makeApiRequest($url, 'POST', $accessToken, $data);
                
                    if (!$response) {
                        throw new Exception("No response from purchaseBasket function.");
                    }
                
                    if (!isset($response['orderId'])) {
                        throw new Exception("Invalid response from purchaseBasket function. Response: " . json_encode($response));
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
                    $errors[] = "Failed to retrieve access token. </br> ". "\n";
                }
                
                $basketId = createBasket($accessToken, $performance_code, $area, $pricetypecode);
                if (!$basketId) {
                    $errors[] = "Failed to create basket."."\n";
                }
                
                $customerDetails = createCustomer($accessToken, $customerData);
                if (!$customerDetails) {
                    $errors[] = "Failed to create customer."."\n";
                } 
                // Extracting relevant details from $customerDetails
                $customerId = $customerDetails['customerId'];
                $customerAccount = $customerDetails['account'];
                $aFile = $customerDetails['aFile'];
                
                $orderId = purchaseBasket($accessToken, $basketId, $customerId, $customerAccount, $aFile);
                if (!$orderId) {
                    $errors[] = "Failed to purchase basket."."\n";
                } else {
                    $barcodeDetails = getOrderDetails($accessToken, $orderId);
                    if (!$barcodeDetails) {
                        $errors[] = "Failed to retrieve order details.";
                    }
                } 
                
            } else {
                throw new Exception("Failed to create basket.");
            }
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }
    }
}

// Output errors as JSON
if (!empty($errors)) {
    echo json_encode(['errors' => $errors]);
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
