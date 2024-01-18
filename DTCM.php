<?php
class DTCM
{

	private $baseURL = 'https://et-api.det.gov.ae';
	private $sellerCode = 'AMMEE1';
	private $apiKey = '?api_key=sz4pbntphrygvseq2dr98vh8';
	private $accessToken = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsIng1dCI6InBabTMtTEdudnJVc2VreUx5cTM2bC1oUy10NCIsImtpZCI6InBabTMtTEdudnJVc2VreUx5cTM2bC1oUy10NCJ9.eyJhdWQiOiJodHRwczovL2V0LWFwaS5kZXQuZ292LmFlIiwiaXNzIjoiaHR0cDovL3N0cy5jb3JwLmRldC5nb3YuYWUvYWRmcy9zZXJ2aWNlcy90cnVzdCIsImlhdCI6MTcwNDk2Nzk1NiwibmJmIjoxNzA0OTY3OTU2LCJleHAiOjE3MDQ5NzE1NTYsImFwcHR5cGUiOiJDb25maWRlbnRpYWwiLCJhcHBpZCI6IkFHLUVULUFNTUVFMSIsImF1dGhtZXRob2QiOiJodHRwOi8vc2NoZW1hcy5taWNyb3NvZnQuY29tL3dzLzIwMDgvMDYvaWRlbnRpdHkvYXV0aGVudGljYXRpb25tZXRob2QvcGFzc3dvcmQiLCJhdXRoX3RpbWUiOiIyMDI0LTAxLTExVDEwOjEyOjM2LjMwNloiLCJ2ZXIiOiIxLjAiLCJzY3AiOiJwcm9maWxlIn0.YdVglNrSxovKRIEvUGT0sPk10uyoUJZ67avW7_shhW9kKwrhlJAu76qYUvtiHWtwGuTRCcvlVIobV8mUsJw8FY41kRiGan-e0_GqE9OW8e_A8SZTK299k8siwSRhqJHYf64WnPI1TFIiB-P10Mj7eaPk_8fgx7CuaZsASC_pnreaNHUXCxyOIrrozYx65EzAev06y1Si4-YeyrMwSxKkF3U65WT9_skCxhTPEjPcCG961Rmnqsx0_-mO6Po0TvPGpF27rj7ltTXTBC6T6dIoubKikBqB6tmkCnP8BtEPJkVjlfHswaqYLsjdzId7ckoE7npq81KfYMR16mkCR6Eneg';
	private $clientSecret = '57gZ_cbatt9P2hcUmZC9vQCi9vtBUCf0enK8Z_8Z';
  

  public function __construct()
  {
    $this->GenerateToken();
  }

  /**
   * Generate Token
   * @return String
   */

  function GenerateToken()
  {

    try {
      $requestURL = $this->baseURL . '/adfs/oauth2/token' . $this->apiKey;
      $requestBody = '{
        "client_id": "' . $this->sellerCode . '",
        "client_secret": "' . $this->clientSecret . '",
        "scope": "profile",
        "grant_type": "client_credentials",
        "resource": "https://et-api.det.gov.ae"
      }';
      $accessToken = $this->apiCall($requestURL, $requestBody, 'POST');
      $this->accessToken = $accessToken['access_token'];
    }

    //catch exception
    catch (Exception $e) {
      echo 'Message: ' . $e->getMessage();
    }
  }

  /**
   * Create Basket
   * @param Array
   * @return BaseketId
   */

  public function createBasket($dataArray)
  {
    try {
      $requestURL = $this->baseURL . '/baskets' . $this->apiKey;
      $requestBody = '{
        "Channel":"5",
        "Seller":"' . $this->sellerCode . '",
        "Performancecode":"' . $dataArray['performanceCode'] . '",
        "Area":"' . $dataArray['area'] . '",
        "autoReduce":false,
        "holdcode":"",
        "Demand":
          [{
            "PriceTypeCode":"' . $dataArray['priceCategory'] . '"
            ,"Quantity":1,
            "Admits":1,
            "offerCode":"",
            "qualifierCode":"",
            "entitlement":"",
            "Customer":{}
          }]
        }';
      $baseResponse = $this->apiCall($requestURL, $requestBody, 'POST');
      return $baseResponse['id'];
    }

    //catch exception
    catch (Exception $e) {
      echo 'Message: ' . $e->getMessage();
    }
  }

  /**
   * Create Basket
   * @param Array
   * @return Array 
   */


  public function createCustomer($dataArray)
  {
    try {
      $requestURL = $this->baseURL . '/customers' . $this->apiKey;
      $requestBody = '{
        "firstname":"' . $dataArray['firstname'] . '",
        "lastname":"' . $dataArray['lastname'] . '",
        "nationality":"' . $dataArray['nationality'] . '",
        "email":"' . $dataArray['email'] . '",
        "dateOfBirth":"' . $dataArray['dob'] . '",
        "phoneNumber":"' . $dataArray['phone'] . '",
        "city":"Default",
        "state":"Default",
        "countryCode":"' . $dataArray['country'] . '"
        }';

      $response = $this->apiCall($requestURL, $requestBody, 'POST');
      return $response;
    }

    //catch exception
    catch (Exception $e) {
      echo 'Message: ' . $e->getMessage();
    }
  }

  /**
   * Purchase Basket
   * @param Array
   * @return Array 
   */


  public function purchaseBucket($dataArray)
  {

    try {
      $requestURL = $this->baseURL . "/baskets/" . $dataArray['baseketId'] . "/purchase" . $this->apiKey;
      $requestBody = '{
        "Seller":"' . $this->sellerCode . '",
        "customer":{
          "ID":"' . $dataArray['id'] . '",
          "Account":"' . $dataArray['account'] . '",
          "AFile":"' . $dataArray['afile'] . '"
          },
          "Payments":[
            {
              "Amount":"' . $dataArray['amount'] . '",
              "MeansOfPayment":"EXTERNAL"
            }
          ]
        }';

      $response = $this->apiCall($requestURL, $requestBody, 'POST');
      return $response;
    }

    //catch exception
    catch (Exception $e) {
      echo 'Message: ' . $e->getMessage();
    }
  }


  /**
   * Order Details
   * @param Int
   * @return Array 
   */


  public function getOrderDetails($orderId)
  {
    try {
      $requestURL = $this->baseURL . '/orders/' . $orderId . $this->apiKey;
      $requestBody = '{}';
      $response = $this->apiCall($requestURL, $requestBody, 'GET');
      return $response;
    }



    //catch exception
    catch (Exception $e) {
      echo 'Message: ' . $e->getMessage();
    }
  }

  /**
   * General Function for API Call
   * @param $requestURL,$requestBody,$method
   * @return Array 
   */

  private function apiCall($requestURL, $requestBody, $method)
  {
    try {
      $curl = curl_init();
      curl_setopt_array($curl, array(
        CURLOPT_URL => $requestURL,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_POSTFIELDS => $requestBody,
        CURLOPT_HTTPHEADER => array(
          'Authorization: Bearer ' . $this->accessToken,
          'Content-Type: application/json'
        ),
      ));

      $response = curl_exec($curl);
      curl_close($curl);
      return json_decode($response, true);
    } catch (Exception $e) {
      echo 'Message: ' . $e->getMessage();
    }
  }
}





$ea = new DTCM();

// Array to create Basket
$createBasket = array(
  'performanceCode' => 'PVEN12JAN2024M',
  'area' => 'SGENERAL',
  'priceCategory' => 'A'
);

$BasketId = $ea->createBasket($createBasket);

// Array to create Customer
$customerArray = array(
  'firstname' => 'muhammadf',
  'lastname' => 'waqasf',
  'nationality' => 'PK',
  'email' => 'abcsd@gmail.com',
  'dob' => '13/04/1999',
  'phone' => '971565546190',
  'country' => 'PK'
);

$customerResponse = $ea->createCustomer($customerArray);

// Array to Purchase Basket
$customerArray = array(
  'id' => $customerResponse['id'],
  'account' => $customerResponse['account'],
  'afile' =>  $customerResponse['aFile'],
  'amount' => '0',
  'baseketId' => $BasketId,
);

$order = $ea->purchaseBucket($customerArray);

// Funcatoin call to get the OrderDetails
$orderDetails = $ea->getOrderDetails($order['orderId']);

$Barcode = $orderDetails['orderLines'][0]['orderLineItems'][0]['barcode'];
echo "BarCode:" . $Barcode;
