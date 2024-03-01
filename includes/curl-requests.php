<?php

// $baseURL = 'https://et-apiuat.detsandbox.com';
// $sellerCode = 'AMMEE1';
// $apiKey ='3rcbhsn32xmwvu42bmk2pkak';
// $client_secret =  "bbWbFT-5HIaOaaX24otu2JC0S8SpuNNCqhDmv8jC";

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

        $apiUrl = "https://et-apiuat.detsandbox.com/adfs/oauth2/token?api_key=8555cns4y3hruga8kbtvaubx";


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
                "client_secret" => "bbWbFT-5HIaOaaX24otu2JC0S8SpuNNCqhDmv8jC",
                "scope" => "profile",
                "grant_type" => "client_credentials",
                "resource" => "https://et-apiuat.detsandbox.com",
            ];

            $response = makeApiRequest($apiUrl, 'POST', null, $data);

            if (isset($response['access_token'])) {
                return $response['access_token'];
            } else {
                echo "Access token retrieval failed. Response: " . json_encode($response);
                return null;
            }
        }

        function createBasket($accessToken, $performance_code, $area, $pricetypecode) {
            $url = 'https://et-apiuat.detsandbox.com/baskets?api_key=3rcbhsn32xmwvu42bmk2pkak';

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
            "firstname" => $first_name,
            "lastname" => $last_name,
            "nationality" => $nationality,
            "email" => $email,
            "phoneNumber" => $phonenumber,
            "countryCode" => $countrycode,
        ];
        function createCustomer($accessToken, $customerData) {
            $url = 'https://et-apiuat.detsandbox.com/customers?api_key=3rcbhsn32xmwvu42bmk2pkak';

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
            $url = 'https://et-apiuat.detsandbox.com/baskets/' . $basketId . '/purchase?api_key=3rcbhsn32xmwvu42bmk2pkak';

            $data = [
                "Seller" => "AMMEE1",
                "customer" => [
                    "ID" => $customerId,
                    "Account" => $customerAccount,
                    "AFile" => $aFile,
                ],
                "Payments" => [
                    [
                        "Amount" => "50000",  //update this based on the basket id
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
            $url = 'https://et-apiuat.detsandbox.com/orders/' . $orderId . '?api_key=3rcbhsn32xmwvu42bmk2pkak';

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
