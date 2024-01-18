<?php

    // private $baseURL = 'https://et-apiuat.detsandbox.com/';
    // private $sellerCode = 'AMMEE1';
    // private $apiKey ='3rcbhsn32xmwvu42bmk2pkak';
    // below is the secret key
    // bbWbFT-5HIaOaaX24otu2JC0S8SpuNNCqhDmv8jC  - staging secret key
$api_url = "https://et-apiuat.detsandbox.com/adfs/oauth2/token?api_key=8555cns4y3hruga8kbtvaubx";
function getAccessToken() {
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://et-apiuat.detsandbox.com/adfs/oauth2/token?api_key=8555cns4y3hruga8kbtvaubx',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => '{
            "client_id": "AMMEE1",
            "client_secret": "bbWbFT-5HIaOaaX24otu2JC0S8SpuNNCqhDmv8jC",
            "scope": "profile",
            "grant_type": "client_credentials",
            "resource": "https://et-apiuat.detsandbox.com"
        }',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
    ));

    $response = curl_exec($curl);

    if ($response === false) {
        echo "cURL Error: " . curl_error($curl);
        echo "cURL Error Code: " . curl_errno($curl);
        return null;
    }

    curl_close($curl);

    $responseArray = json_decode($response, true);

    if (isset($responseArray['access_token'])) {
        return $responseArray['access_token'];
    } else {
        echo "Access token retrieval failed. Response: " . $response;
        return null;
    }
}
$accessToken = getAccessToken();
echo "Access Token: ". $accessToken . "\n"; 
function createBasket($accessToken) {
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://et-apiuat.detsandbox.com/baskets?api_key=3rcbhsn32xmwvu42bmk2pkak',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => '{"Channel":"5","Seller":"AMMEE1","Performancecode":"PDUB01DEC2023B","Area":"SVIP1","autoReduce":false,"holdcode":"","Demand":[{"PriceTypeCode":"A","Quantity":1,"Admits":1,"offerCode":"","qualifierCode":"","entitlement":"","Customer":{}}]}',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer ' . $accessToken
        ),
    ));

    $basketResponse = curl_exec($curl);

    if ($basketResponse === false) {
        echo "cURL Error: " . curl_error($curl);
        echo "cURL Error Code: " . curl_errno($curl);
    }

    curl_close($curl);

    $basketArray = json_decode($basketResponse, true);

    return $basketId = $basketArray['id'];
}
$basketId = createBasket($accessToken);
 echo "BaskedID:".  $basketId . "\n";

function createCustomer($accessToken, $basketId, $customerData) {
    $curl = curl_init();

    $customerData = json_encode($customerData);

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://et-apiuat.detsandbox.com/customers?api_key=3rcbhsn32xmwvu42bmk2pkak',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $customerData,
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json'
        ),
    ));

    $customerResponse = curl_exec($curl);

    curl_close($curl);

    $customerArray = json_decode($customerResponse, true);

    return array(
        'basketId' => $basketId,
        'customerId' => $customerArray['id'],
        'account' => $customerArray['account'],
        'aFile' => $customerArray['aFile']
    );
}


$customerDetails = createCustomer($accessToken, $basketId, $customerData);
$customerId = $customerDetails['id'];
$customerAccount = $customerDetails['account'];
$aFile = $customerDetails['aFile'];

function purchaseBasket($accessToken, $basketId, $customerId, $customerAccount, $aFile) {
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://et-apiuat.detsandbox.com/baskets/' . $basketId . '/purchase?api_key=3rcbhsn32xmwvu42bmk2pkak',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => '{
            "Seller": "AMMEE1",
            "customer": {
                "ID": "' . $customerId . '",
                "Account": "' . $customerAccount . '",
                "AFile": "' . $aFile . '"
            },
            "Payments": [{
                "Amount": "0",
                "MeansOfPayment": "EXTERNAL"
            }]
        }',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json'
        ),
    ));

    $purchaseResponse = curl_exec($curl);
    curl_close($curl);
    if ($purchaseResponse === false) {
        echo "cURL Error: " . curl_error($curl);
        echo "cURL Error Code: " . curl_errno($curl);
    } 
    $orderDetails = json_decode($purchaseResponse, true);
    return $orderDetails['orderId'];
    
}

$orderId = purchaseBasket($accessToken, $basketId, $customerId, $customerAccount, $aFile);

function getOrderDetails($accessToken, $orderId) {
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://et-apiuat.detsandbox.com/orders/' . $orderId . '?api_key=3rcbhsn32xmwvu42bmk2pkak',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer ' . $accessToken,
        ),
    ));

    $orderResponse = curl_exec($curl);
    curl_close($curl);
    if ($orderResponse === false) {
        echo "cURL Error: " . curl_error($curl);
        echo "cURL Error Code: " . curl_errno($curl);
    } 
    $orderArray = json_decode($orderResponse, true);

    return $orderArray;
}

$barcodeDetails = getOrderDetails($accessToken, $orderId);
echo "Barcode:". $barcodeDetails['orderLines'][0]['orderLineItems'][0]['barcode'];

?>