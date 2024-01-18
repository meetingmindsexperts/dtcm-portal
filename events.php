<?php
class Event {
    var $name;
    var $settings;
    var $err;
    private $baseURL = 'https://et-api.det.gov.ae/';
    private $sellerCode = 'AMMEE1';
    private $apiKey ='sz4pbntphrygvseq2dr98vh8';
    
    // 57gZ_cbatt9P2hcUmZC9vQCi9vtBUCf0enK8Z_8Z - prod secret key
    // below credentials are sandbox credentials 
   
    // private $baseURL = 'https://et-apiuat.detsandbox.com/';
    // private $sellerCode = 'AMMEE1';
    // private $apiKey ='3rcbhsn32xmwvu42bmk2pkak';
    // below is the secret key
    // bbWbFT-5HIaOaaX24otu2JC0S8SpuNNCqhDmv8jC - staging secret key
    // credentials are sandbox credentials
  
    private $accessToken=  "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsIng1dCI6InBabTMtTEdudnJVc2VreUx5cTM2bC1oUy10NCIsImtpZCI6InBabTMtTEdudnJVc2VreUx5cTM2bC1oUy10NCJ9.eyJhdWQiOiJodHRwczovL2V0LWFwaS5kZXQuZ292LmFlIiwiaXNzIjoiaHR0cDovL3N0cy5jb3JwLmRldC5nb3YuYWUvYWRmcy9zZXJ2aWNlcy90cnVzdCIsImlhdCI6MTcwNDk2MTIyOCwibmJmIjoxNzA0OTYxMjI4LCJleHAiOjE3MDQ5NjQ4MjgsImFwcHR5cGUiOiJDb25maWRlbnRpYWwiLCJhcHBpZCI6IkFHLUVULUFNTUVFMSIsImF1dGhtZXRob2QiOiJodHRwOi8vc2NoZW1hcy5taWNyb3NvZnQuY29tL3dzLzIwMDgvMDYvaWRlbnRpdHkvYXV0aGVudGljYXRpb25tZXRob2QvcGFzc3dvcmQiLCJhdXRoX3RpbWUiOiIyMDI0LTAxLTExVDA4OjIwOjI4LjIxNVoiLCJ2ZXIiOiIxLjAiLCJzY3AiOiJwcm9maWxlIn0.c8W9tZFJF5HPktBQ4X8q0hbcFmZdvFTXkR3L80WUUXjYbMj8cRu7x2nQvWFgPJC0Ojz2oScf9l-tU9e1huDy_kSeB8_4B81kM4UwTDhoItRaQKHJ81KxqZV2R3HvESVI7R6GmopHMJHlfRn8WM4pABfN5Or5ZRBs66hdDjM8jo6uXS_tVqeUUu8n4mKtxIqF3WaaaCmxh1_O67GsbAt2B82lGrBUSGPo0lpEWywSN6UJqCnnjc-Wi6b3SQhMmFIsBCyLg8ur5xeIByAOXTAP21dajpKtidfJz9yh9vZTgOEuOTgHVMgjw93Mz4ZgPggCFlfDR5YgwAM2jT409UDW-g";
    
    /// constructors
    function Event($err = "")
    {
        global $db;
        $this->err = $err;
    }
    
    function getArrData()
    {
        return $this->arrData;
    }
    
    function setArrData($szArrData)
    {
        $this->arrData = $szArrData;
    }
    
    function getFileArrData()
    {
        return $this->arrFileData;
    }
    
    function setFileArrData($szArrData)
    {
        $this->arrFileData = $szArrData;
    }
    
    function getErr()
    {
        return $this->err;
    }
    
    function setErr($szError)
    {
        $this->err .= " - <em>$szError</em>";
    }
    
	
	
/**************  Add-Update-Delete-Event  **********/

    function insert()
    {
        global $db;
        $arrData = $this->getArrData();
		$EventTb = $arrData;
		$CatTb = $arrData;
        $arrData["date_added"] = date("Y-m-d H:i:s");
		$EventName = $arrData['eventname'];
	  	$varand=(rand(10,100));
		$EventTblName="EventCustomers_".$varand.$EventName.date("dmy");
		$EventTblName = preg_replace("/[^A-Za-z0-9]/","",$EventTblName);
		$EventTb['Eventblname']=$EventTblName;
		$createEvntCustTbl=$this->CreateEvntCustTbl($EventTblName);
		unset($EventTb['id']);
        unset($EventTb['submit']);
	    unset($EventTb['TypeData']); 
        $rs1 = $db->query($db->InsertQuery(TBL_SMS_EVENTS, $EventTb));
		sleep(10);
		
		$ThsId= $this ->GetIdByEventName($EventName);
		$CatTbIns['event_id']=$ThsId['id'];
			
				for($i=0;$i<count($CatTb['TypeData']);$i++){
					if($CatTb['TypeData']['Catname'][$i]!=""){
						$CatTbIns['Catname'] = $CatTb['TypeData']['Catname'][$i];
						$CatTbIns['areacode'] = $CatTb['TypeData']['areas'][$i];
						$CatTbIns['price'] = $CatTb['TypeData']['price'][$i];
						$CatTbIns['pricetypecode'] = $CatTb['TypeData']['pricetypecode'][$i];
						$rs2 = $db->query($db->InsertQuery(TBL_PERF_AREAS, $CatTbIns));
					}										
				}
        if (is_object($rs['id']))
        {
            return $rs;
        }
        else
        {
            return false;
        }
    }
	
	
	function CreateEvntCustTbl($name)
    {
        global $db;
        $rs = $db->query("CREATE TABLE `$name` (
						  `id` int(11) PRIMARY KEY AUTO_INCREMENT,
						  `event_id` int(11) NOT NULL,
						  `eventsair_id` int(11) NOT NULL,
						  `OrderId` varchar(225) COLLATE utf8_unicode_ci NOT NULL,
						  `PerformanceId` int(11) NOT NULL,
						  `perf_price` varchar(225) COLLATE utf8_unicode_ci NOT NULL,
						  `Barcode` varchar(225) COLLATE utf8_unicode_ci DEFAULT NULL,
						  `Fname` varchar(225) COLLATE utf8_unicode_ci NOT NULL,
						  `Lname` varchar(225) COLLATE utf8_unicode_ci NOT NULL,
						  `email` varchar(225) COLLATE utf8_unicode_ci NOT NULL,
						  `dob` varchar(225) COLLATE utf8_unicode_ci DEFAULT NULL,
						  `MobNumber` varchar(225) DEFAULT NULL,
						  `Nationality` varchar(225) COLLATE utf8_unicode_ci NOT NULL,
						  `PACountry` varchar(225) COLLATE utf8_unicode_ci NOT NULL,
						  `Errormsg` varchar(2000) COLLATE utf8_unicode_ci DEFAULT NULL,
						  `og_eventsair_id` varchar(225) COLLATE utf8_unicode_ci DEFAULT NULL,
						  `rid` int(11) DEFAULT NULL
						) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
        return $rs;
    }
	 
	function GetIdByEventName($name)
    {
        global $db;
        $rs = $db->query("SELECT id FROM " . TBL_SMS_EVENTS . " WHERE `eventname`='$name'");
        return $rs->fetch_array();
    }
	
	function GetEventName($id)
    {
        global $db;
        $rs = $db->query("SELECT eventname FROM " . TBL_SMS_EVENTS . " WHERE `id`='$id'");
        $EventnameArr = $rs->fetch_array();
		$Ename = $EventnameArr['eventname'];
		return $Ename;
    }
	
		
	function updateEvent($id)
    {
        global $db;
        $arrData = $this->getArrData();		
        unset($arrData['submit']);
        unset($arrData['Submit']);
		$EventData =$arrData;
		$PricData =$arrData;
		unset($EventData['TypeData']);
		$InsPricData['event_id']=$id;

        $rs = $db->query($db->UpdateQuery(TBL_SMS_EVENTS, $EventData, $id));
		
		for($i=0;$i<count($PricData);$i++){
			$TypeId=$PricData['TypeData']['Typid'][$i];
			if($TypeId!=""){
				$UpdatePricData['Catname'] =$PricData['TypeData']['Catname'][$i];
				$UpdatePricData['areacode'] =$PricData['TypeData']['areas'][$i];
				$UpdatePricData['price'] =$PricData['TypeData']['price'][$i];
				$UpdatePricData['pricetypecode'] =$PricData['TypeData']['pricetypecode'][$i];
				$rs = $db->query($db->UpdateQuery(TBL_PERF_AREAS, $UpdatePricData, $TypeId));
										
			}else{
				if($PricData['TypeData']['Catname'][$i]!=""){
					$InsPricData['Catname'] =$PricData['TypeData']['Catname'][$i];
					$InsPricData['areacode'] =$PricData['TypeData']['areas'][$i];
					$InsPricData['price'] =$PricData['TypeData']['price'][$i];
					$InsPricData['pricetypecode'] =$PricData['TypeData']['pricetypecode'][$i];
					$rs2 = $db->query($db->InsertQuery(TBL_PERF_AREAS, $InsPricData));
				}
			}
		}
        if ($rs)
        return true;
        else
        return false;
    }
	
	function DelRowContent($id)
    {
        global $db;
        $rs = $db->query('DELETE FROM ' . TBL_SMS_EVENTS . ' WHERE id=' . $id);
        if ($rs)
        return true;
        else
        return false;
    }
	
/**************  END Add-Update-Delete-Event  **********/	



/**************  Bulk Import To DB  **********/

		function BulkImportToDB($FId,$EventId){
	   //  echo "<br/>";
	   //  echo "A";
	   //  die();
	   //  die();
			global $db;
			$resultrow = array();
			$returnData = array();
			$DelegateData = array();
			$ErrorData = array();
		
// $Eventblname="EventCustomers66WCOG2022081222";
//   $json = (json_decode(utf8_encode($json), true));
// for($i=0;$i<count($json);$i++){
//     echo $json[$i]['id'];
//     				$CustomerData['eventsair_id']=$json[$i]['eventsair_id'];
// 				$CustomerData['event_id']=95; //Not necessary
// 				$CustomerData['Fname']=$fname=$json[$i]['Fname'];
// 				$CustomerData['Lname']=$lname=$json[$i]['Lname'];		
// 				$CustomerData['dob']=$dob=$json[$i]['dob'];
// 				$CustomerData['MobNumber']=$phnumber=$json[$i]['MobNumber'];
// 				$CustomerData['email']=$email=$json[$i]['email'];	
// 				$CustomerData['PACountry']=$email=$json[$i]['PACountry'];
// 				$CustomerData['Nationality']=$email=$json[$i]['Nationality'];
// 				$CustomerData['PerformanceId']=$json[$i]['PerformanceId'];
// 				$CustomerData['perf_price']=$json[$i]['perf_price'];
// 					$rsCustomerData = $db->query($db->InsertQuery($Eventblname, $CustomerData));
// }
// var_dump(json_last_error_msg());
//   print_r($json);
// 			die();
			
			
			$CSVDataToInsert = $this->getDelegateDataFromFile($FId);
		
			$RegEAirId = $this->getRegEAirIdFromtable($EventId);
			//print_r($RegEAirId);exit;
			$EventData = $this->GetRowContent($EventId);
		
			$Eventblname = $EventData['Eventblname'];
			
			$ObjPerfAreas = load_class('PerfAreas');
			$PerfAreas = $ObjPerfAreas->GetRowContentByEid($EventId);
// 			echo "<pre>";
// 			print_r($PerfAreas);
// 			die();

			for($i=0;$i<count($CSVDataToInsert);$i++){
			
				
		
				$CustomerData['eventsair_id']=$EAirId=$CSVDataToInsert[$i]['Id'];
				$CustomerData['event_id']=$EventId=$EventId; //Not necessary
				$salutatn="Default";
				$CustomerData['Fname']=$fname=$CSVDataToInsert[$i]['FirstName'];
				$CustomerData['Lname']=$lname=$CSVDataToInsert[$i]['LastName'];		
				$CustomerData['dob']=$dob=$CSVDataToInsert[$i]['DOB'];
				$CustomerData['MobNumber']=$phnumber=$CSVDataToInsert[$i]['MobileNumber'];
				$CustomerData['email']=$email=$CSVDataToInsert[$i]['PrimaryEmail'];	
				$CustomerData['PACountry']=$email=$CSVDataToInsert[$i]['Primary-Address-Country'];
				$CustomerData['Nationality']=$email=$CSVDataToInsert[$i]['Nationality'];
				
				$DelegateArea = $CSVDataToInsert[$i]['Registration-Type-Name'];	
					for($n=0;$n<count($PerfAreas);$n++){
						if($PerfAreas[$n]['Catname'] == $DelegateArea){
							//$CustomerData['Fname']=$AreaType=$PerfAreas[$n]['areacode'];
							//$CustomerData['Fname']=$PriceTypeCod=$PerfAreas[$n]['pricetypecode'];
							//$CustomerData['Fname']=$Amnt =$PerfAreas[$n]['price'];
							$CustomerData['PerformanceId']=$PerfId =$PerfAreas[$n]['id'];
						}
					}
				$CustomerData['PerformanceId'] = 161;
				$returnData[$i]['eventsair_id']=$EAirId;
				$returnData[$i]['Fname']=$fname;
				$returnData[$i]['Lname']=$lname;
				$CustomerData['perf_price'] = 0;
				
				// echo $Eventblname;
				// die();
				if (in_array($EAirId, $RegEAirId)){
					$returnData[$i]['Error']="Duplicate EventsAir ID";
					continue;
				}else{
					
					$rsCustomerData = $db->query($db->InsertQuery($Eventblname, $CustomerData));
					$returnData[$i]['Result']="Data Inserted to table";
					
				}
			}
			
			return $returnData;		
		}


/**************  END Bulk Import To DB  **********/



     
     function update($id)
     {
        global $db;
        $arrData = $this->getArrData();
		unset($arrData['accept']);
		unset($arrData['cur_status_pos']);
        unset($arrData['parent_id']);
        unset($arrData['submit']);
        unset($arrData['Submit']);
        unset($arrData['count']);
        unset($arrData['pants']);
        unset($arrData['deletepants']);
        unset($arrData['deletedimg']);
        $rs = $db->query($db->UpdateQuery(TBL_SMS_EVENTS, $arrData, $id));
        if ($rs)
        return true;
        else
        return false;
    }
	  
    
    function GetRowContent($id)
    {
        global $db;
        $rs = $db->GetRowById(TBL_SMS_EVENTS, $id);
        return $rs->fetch_array();
    }
	
	    
	function SelectAllActive(){
        global $db;
        $resultrow = array();
        $i = 0;
        $rs = $db->query("SELECT * FROM " . TBL_SMS_EVENTS . " WHERE `status`=1 order by id");
        /* return $rs; */
        while ($row = $rs->fetch_array())
        {
            $resultrow[$i++] = $row;
        }
        if ($resultrow)
        return $resultrow;
        else
        return $row;
    }
	           
    function getSearch($keyword)
    {
        global $db;
        $resultrow = array();
        $i = 0;
        $rs = $db->query("SELECT * FROM " . TBL_SMS_EVENTS . " WHERE `title` LIKE '%$keyword%' OR `description` LIKE '%$keyword%'");
        while ($row = $rs->fetch_array())
        {
            $resultrow[$i++] = $row;
        }
        if ($resultrow)
        return $resultrow;
        else
        return $row;
    }
    
    function ViewAll($page = null, $record = null)
    {
        global $db;
        $resultrow = array();
        $i = 0;
        $rs = $db->SelectAll(TBL_SMS_EVENTS, $page, $record);
        while ($row = $rs->fetch_array())
        {
            $resultrow[$i++] = $row;
        }
        if ($resultrow)
        return $resultrow;
        else
        return $row;
    }
    
    function SelectAll()
    {
        global $db;
        $resultrow = array();
        $i = 0;
		$rs = $db->query("SELECT * FROM " . TBL_SMS_EVENTS." order by id DESC");
        while ($row = $rs->fetch_array())
        {
            $resultrow[$i++] = $row;
        }
        if ($resultrow)
        return $resultrow;
        else
        return $row;
    }
	
	function getlatest()
    { 
        global $db;
        $resultrow = array();
        $i = 0;
		$rs = $db->query(" select * from " . TBL_SMS_EVENTS . " where `status`!='0' ORDER BY id DESC ");
        while ($row = $rs->fetch_array())
        {
            $resultrow[$i++] = $row;
        }
        if ($resultrow)
        return $resultrow;
        else
        return $row;
    }
    
    function maxid()
    {
        global $db;
        $i = 0;
        $rs = $db->query('SELECT MAX(id) as id FROM ' . TBL_SMS_EVENTS);
        while ($row = $rs->fetch_array())
        {
            $result = $row;
        }
        return $result['id'];

    }    

	/*  Price Type Section */
	function GetPricetypeData($id)
    {
        global $db;
        $rs = $db->GetRowById(TBL_PERFAREAS, $id);
        return $rs->fetch_array();
    }
    
    /*  Country Section */
        function SelectAllCountry()
    {
        global $db;
        $resultrow = array();
        $i = 0;
        $rs = $db->query("SELECT * FROM " . TBL_COUNTRY." order by id ASC");
        while ($row = $rs->fetch_array())
        {
            $resultrow[$i++] = $row;
        }
        if ($resultrow)
        return $resultrow;
        else
        return $row;
    }
	
	function GetCountry($country){
		
		$ArrCountry = $this->SelectAllCountry();
		for($m=0;$m<count($ArrCountry);$m++){
			if($ArrCountry[$m]['nicename'] == $country){
				return $ArrCountry[$m]['iso'];
			}
		}	
	}
	
	/*  All Registered Delegates By Event */
	function getRegDelgEAirId($Eid)
    {
        global $db;
        $resultrow = array();
        $i = 0;
        $rs = $db->query("SELECT `eventsair_id`,`Barcode`,`Errormsg` FROM " . TBL_CUSTOMER." where `event_id` =".$Eid." order by id ASC");
        while ($row = $rs->fetch_array())
        {
            $resultrow[$i++] = $row;
        }
        if ($resultrow)
        return $resultrow;
        else
        return $row;
    }
	
	function getRegEAirIdFromtable($Eid)
    {
        global $db;
        $resultrow = array();
        $i = 0;
		$EventData = $this -> GetRowContent($Eid);
		//print_r($EventData);
		$Eventblname = $EventData['Eventblname'];
		//echo $Eventblname;exit;
		//echo "SELECT `eventsair_id` FROM ".$Eventblname." order by id ASC";
        $rs = $db->query("SELECT `eventsair_id` FROM ".$Eventblname." order by id ASC");
        while ($row = $rs->fetch_array())
        {
            $resultrow[$i++] = $row;
        }
        if ($resultrow){
            
            for($k=0;$k<count($resultrow);$k++){ 
        			$RegEAirId[$k]=$resultrow[$k]['eventsair_id'];
        		}
        return $RegEAirId;
        }
        else{
        return $row;
        }
    }
	
	function getRegDataFrmTblByEAirId($Eid,$EAirId)
    {
        global $db;
        $resultrow = array();
        $i = 0;
		$EventData = $this -> GetRowContent($Eid);
		$Eventblname = $EventData['Eventblname'];
		
        $rs = $db->query("SELECT * FROM ".$Eventblname." WHERE `eventsair_id` =".$EAirId);
        while ($row = $rs->fetch_array())
        {
            $resultrow = $row;
        }
		
        if ($resultrow){
        return $resultrow;
        }
        else{
        return "NODATA";
        }
    }
	
		function getRegDataFrmTblByEAirIdAndBarc($Eid,$EAirId)
    {
        global $db;
        $resultrow = array();
        $i = 0;
		$EventData = $this -> GetRowContent($Eid);
		$Eventblname = $EventData['Eventblname'];

        $rs = $db->query("SELECT * FROM ".$Eventblname." WHERE `eventsair_id` =".$EAirId);
        while ($row = $rs->fetch_array())
        {
            $resultrow = $row;
        }
			//print_r($resultrow);	
        if ($resultrow){
			if(($resultrow['Barcode']=="NULL")||($resultrow['Barcode']=="")){
				$resultrow['is_barcode']="NO";
			 }else{
				$resultrow['is_barcode']="YES"; 
			 }
			return $resultrow;
        }
        else{
			$resultrow['is_barcode']="NODATA"; 
			return $resultrow;
        }
    }
	
	/*********  Token Section  ******/
// 	function GenerateToken(){
// // 		die();
// // 		$TokenDetails = $this->GetTokenContent();
// // 		unset($Token);
// // 		$exptime = $TokenDetails['time'];		
// // 		$CurrentTime = date("m/d/Y H:i:s");
		
// // 				if($CurrentTime < $exptime ){
// // 					// No Token Generate
// // 					$Token = $TokenDetails['token'];
// // 						if(!isset($Token)){
// // 								return "E11";
// // 							}
					
// // 				}else{
// 					// * Generate Access Token */
// 					//$requesturl = 'https://et-api.det.gov.ae/adfs/oauth2/token?api_key=sz4pbntphrygvseq2dr98vh8';

// 						$curl = curl_init();	
					
//         				curl_setopt_array($curl, array(
//                           CURLOPT_URL => 'https://et-api.det.gov.ae/adfs/oauth2/token?api_key=sz4pbntphrygvseq2dr98vh8',
//                           CURLOPT_RETURNTRANSFER => true,
//                           CURLOPT_ENCODING => '',
//                           CURLOPT_MAXREDIRS => 10,
//                           CURLOPT_TIMEOUT => 30,
//                           CURLOPT_FOLLOWLOCATION => true,
//                           CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//                           CURLOPT_CUSTOMREQUEST => 'POST',
//                           CURLOPT_POSTFIELDS =>'{
//                           "client_id": "AMMEE1",
//                           "client_secret": "57gZ_cbatt9P2hcUmZC9vQCi9vtBUCf0enK8Z_8Z",
//                           "scope": "profile",
//                           "grant_type": "client_credentials",
//                           "resource": "https://et-api.det.gov.ae"
//                         }',
//                           CURLOPT_HTTPHEADER => array(
//                             'Content-Type: application/json'
//                           ),
//                         ));

// 					$accTokenRet = curl_exec($curl);
					
// 					$err = curl_error($curl);

// 					curl_close($curl);
// 					if ($err) {
// 					  return "Token Generation Error.cURL Error #:" . $err;
// 					 return "E11";
// 					} else {
// 					    $accTokenArr =json_decode($accTokenRet, TRUE);
// 					    $Token = $accTokenArr['access_token'];
// 					    //$this->accessToken=$Token;
// 						if(!isset($Token)){
// 							return "E12A";
// 						}
// 						//$InsToken = $this->updateToken($Token);
						
// 					}
					
// 					/* Generate Access Token END */
					
// 				//}
				
// 		$Token="Bearer ".$Token;
// 		return $Token;
// 	}
	
// 	function updateToken($Token)
//     {
//         global $db;
// 		$Token_id="1";
// 		$exptime = date("m/d/Y H:i:s", time() + 82800);
//         $sql_qry = "UPDATE " . TBL_ACC_TOKEN . " set `token` = '".$Token."',`time` ='".$exptime."'  where id =" . $Token_id;
// 		//echo $sql_qry;
//         $rs = $db->query($sql_qry);
//         return true;
//     }
	
// 	function GetTokenContent()
//     {
//         global $db;
// 		$id="1";
//         $rs = $db->GetRowById(TBL_ACC_TOKEN, $id);
//         return $rs->fetch_array();
//     }
	
	/*********  END Token Section *********/
	
	
	/***** Single Ticket Generation from Admin Section *******/
	function GenerateTicket(){
	
        global $db;
        $resultrow = array();
        $arrData = $this->getArrData();
       
		$EventId=$arrData['id'];
		$EAirIdTrue=False;
		
		//print_r($arrData);
		//$BarcodGenEAirId=
		//$RegEAirId = $this->getRegEAirIdFromtable($EventId);//print_r($RegDelgData);
		   		
		$email=$arrData['email'];
		$EAirId=$arrData['EAirId'];
		$OgEAirId=$arrData['OgEAirId'];
		
		$DelgDetails = $this->getRegDataFrmTblByEAirIdAndBarc($EventId,$EAirId);  

		$EventData = $this->GetRowContent($EventId);

		$PerfomCode = $EventData['perfcode'];
		$Eventtblname = $EventData['Eventblname'];
		
		$PricetypeId=$arrData['PerfAreaCatid'];
		$PricetypeData = $this->GetPricetypeData($PricetypeId);
		
// 		echo "<pre>";
// 		var_dump($PricetypeData);
// 		echo "</pre>";
		$AreaType=$PricetypeData['areacode'];
		
		$PriceTypeCod=$PricetypeData['pricetypecode'];
		
		$Amnt =$PricetypeData['price'];
				
		//$PerfomCode="ETES2015983M";
		$Seller = $this->sellerCode;		
		$salutatn=$arrData['salutation'];		
		$fname=$arrData['fname'];		
		$lname=$arrData['lname'];		
		$nation=$arrData['nation'];		
		$dob=$arrData['dob'];
		$phnumber=$arrData['phnumber'];		
		$cty=$arrData['city'];		
		$stat=$arrData['state'];	
		$CCcode=$arrData['CCcode'];
		
			if($DelgDetails['is_barcode']=="YES"){
				return "BarcodeAlready Generated";
			}elseif($DelgDetails['is_barcode']=="NO"){
				$DataExists=True;
			}elseif($DelgDetails['is_barcode']=="NODATA"){
				$DataExists=False;
			}
		
			/* Data To insert in Result Table */
			$CustomerData['event_id']=$EventId;
			$CustomerData['og_eventsair_id']=$OgEAirId;
			$CustomerData['eventsair_id']=$EAirId;
			$CustomerData['fname']=$fname;
			$CustomerData['lname']=$lname;
			$CustomerData['email']=$email;
			$CustomerData['MobNumber']=$phnumber;
			$CustomerData['PACountry']=$CCcode;
			$CustomerData['Nationality']=$nation;
			$CustomerData['OrderId']=NULL;
			$CustomerData['Barcode']=NULL;
			$CustomerData['Errormsg'] =NULL;
			
		    $accToken = "Bearer ".$this->accessToken;	
		    
		   //var_dump($accToken);
		  //  echo "<h1>in generateticket function</h1>".__LINE__;

			// /* Add Basket  */
			 //$bodAddBasket="{\"Channel\":\"W\",\"Seller\":\"$Seller\",\"Performancecode\":\"$PerfomCode\",\"Area\":\"$AreaType\",\"Demand\":[{\"PriceTypeCode\":\"$PriceTypeCod\",\"Quantity\":1,\"Admits\":1,\"Customer\":{}}],\"Fees\":[{\"Type\":\"5\",\"Code\":\"W\"}]}\n";
 
                // $bodAddBasket ='{"Channel":"5","Seller":"ABART1","Performancecode":"'.$PerfomCode.'","Area":"'.$AreaType.'","autoReduce":false,"holdcode":"","Demand":[{"PriceTypeCode":"'.$PriceTypeCod.'","Quantity":1,"Admits":1,"offerCode":"","qualifierCode":"","entitlement":"","Customer":{}}]}';
                // $bodyBasket='{"Channel":"5","Seller":"ABART1","Performancecode":"PDUB01DEC2023B","Area":"SVIP1","autoReduce":false,"holdcode":"","Demand":[{"PriceTypeCode":"A","Quantity":1,"Admits":1,"offerCode":"","qualifierCode":"","entitlement":"","Customer":{}}]}';

				// $curl = curl_init();

				// curl_setopt_array($curl, array(
				//   CURLOPT_URL => $this->baseURL."baskets?api_key=".$this->apiKey,
				//   CURLOPT_RETURNTRANSFER => true,
				//   CURLOPT_ENCODING => "",
				//   CURLOPT_MAXREDIRS => 10,
				//   CURLOPT_TIMEOUT => 30,
				//   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				//   CURLOPT_CUSTOMREQUEST => "POST",
				//   CURLOPT_POSTFIELDS => $bodAddBasket,
				//   CURLOPT_HTTPHEADER => array(
				// 	"Authorization: $accToken",
				// 	"Cache-Control: no-cache",
				// 	"Content-Type: application/json"
				//   ),
				// ));

		  //      $requestBody = '{"Channel":"5","Seller":"'.$this->sellerCode.'","Performancecode":"'.$PerfomCode.'","Area":"'.$AreaType.'","autoReduce":false,"holdcode":"","Demand":[{"PriceTypeCode":"'.$PriceTypeCod.'","Quantity":1,"Admits":1,"offerCode":"","qualifierCode":"","entitlement":"","Customer":{}}]}';
                
    //             echo "Request Body: " . $requestBody;

    //             $curl = curl_init();
                
    //             curl_setopt_array($curl, array(
    //               CURLOPT_URL => 'https://et-api.det.gov.ae/baskets?api_key=sz4pbntphrygvseq2dr98vh8',
    //               CURLOPT_RETURNTRANSFER => true,
    //               CURLOPT_ENCODING => '',
    //               CURLOPT_MAXREDIRS => 10,
    //               CURLOPT_TIMEOUT => 0,
    //               CURLOPT_FOLLOWLOCATION => true,
    //               CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    //               CURLOPT_CUSTOMREQUEST => 'POST',
    //               CURLOPT_POSTFIELDS => $requestBody,
    //               CURLOPT_HTTPHEADER => array(
    //                 'Authorization: Bearer '.$this->accessToken,
    //                 'Content-Type: application/json'
    //               ),
    //             ));


				// $AddBasketResp = curl_exec($curl);

				// $err = curl_error($curl);
				// curl_close($curl);
				
                    //91.75.69.61 et-api.det.gov.ae
                    $curl = curl_init();

                    curl_setopt_array($curl, array(
                        CURLOPT_URL => 'https://et-api.det.gov.ae/baskets?api_key=sz4pbntphrygvseq2dr98vh8',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_POSTFIELDS => '{"Channel":"5","Seller":"AMMEE1","Performancecode":"PVEN12JAN2024M","Area":"SGENERAL","autoReduce":false,"holdcode":"","Demand":[{"PriceTypeCode":"A","Quantity":1,"Admits":1,"offerCode":"","qualifierCode":"","entitlement":"","Customer":{}}]}',
                        CURLOPT_HTTPHEADER => array(
                            'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsIng1dCI6InBabTMtTEdudnJVc2VreUx5cTM2bC1oUy10NCIsImtpZCI6InBabTMtTEdudnJVc2VreUx5cTM2bC1oUy10NCJ9.eyJhdWQiOiJodHRwczovL2V0LWFwaS5kZXQuZ292LmFlIiwiaXNzIjoiaHR0cDovL3N0cy5jb3JwLmRldC5nb3YuYWUvYWRmcy9zZXJ2aWNlcy90cnVzdCIsImlhdCI6MTcwNDk1Njc2NywibmJmIjoxNzA0OTU2NzY3LCJleHAiOjE3MDQ5NjAzNjcsImFwcHR5cGUiOiJDb25maWRlbnRpYWwiLCJhcHBpZCI6IkFHLUVULUFNTUVFMSIsImF1dGhtZXRob2QiOiJodHRwOi8vc2NoZW1hcy5taWNyb3NvZnQuY29tL3dzLzIwMDgvMDYvaWRlbnRpdHkvYXV0aGVudGljYXRpb25tZXRob2QvcGFzc3dvcmQiLCJhdXRoX3RpbWUiOiIyMDI0LTAxLTExVDA3OjA2OjA3LjY0N1oiLCJ2ZXIiOiIxLjAiLCJzY3AiOiJwcm9maWxlIn0.fyYLesRf-8of1EC3aFzDQCd-jfg5p6bmC-DZE923tAgg7IIrkLdtai66y3aTBJ0Ot0_qnpU3KSJFXHjFe9tx4MhPlISayeE2CgGVFGIVr4jbUyAICVdlR0CqcqmvOl7L4zFd3LYpZixgQ8esTvAjeW4-csZCv7xn9I9DqGkXWF7V9d-kiVAqQt6VFuFkATcCtKbwE3xKBLbzVP69dsP6QcCL7bUXcR90F8P9XwImbZx2orS7MD_IJCNnzwBCafLDiRAOJ1vO_gACWBEaBy6-FZB9bkvIUs_hw48NA98ne_V6aM_P0HkMvTeRM09Mbwb6a-qDgEWcUOUe4mejxPF2Mg',
                            'Content-Type: application/json'
                        ),
                    ));
                    
                    $AddBasketResp = curl_exec($curl);
                    
                    if ($AddBasketResp === false) {
                        echo "cURL Error: " . curl_error($curl);
                        echo "cURL Error Code: " . curl_errno($curl);
                    }
                    
                    $httpStatusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                    echo "HTTP Status Code: " . $httpStatusCode;
                    
                    $err = curl_error($curl);
                    curl_close($curl);

                
				if ($err) {
				  return "E2";
				} else {					
				    $AddBasketArr=json_decode($AddBasketResp, TRUE);
				    
	
				    $BasketId = $AddBasketArr['id'];
				  
				    echo "\n Basket: ".$BasketId;
						if(!isset($BasketId)){
							$CustomerData['Errormsg'] = $this->GenerateMsg(E21);
							if($DataExists==True){$rsCustomerData = $this->updateError($CustomerData,$Eventtblname);}
							return "E21";
						}
					/* Add Basket END  */
				
			
						/* Add Customer To Basket */
						$bodAddCustomer = "{\"firstname\":\"$fname\",\"lastname\":\"$lname \",\"nationality\":\"$nation\",\"email\":\"$email\",\"dateOfBirth\":\"$dob\",\"phoneNumber\":\"$phnumber\",\"city\":\"$cty\",\"state\":\"$stat\",\"countryCode\":\"$CCcode\"}\r\n";
						$curl = curl_init();
						
    				// 		curl_setopt_array($curl, array(
    				// 		  CURLOPT_URL => "https://api.etixdubai.com/customers?sellerCode=".$this->sellerCode,
    				// 		  CURLOPT_RETURNTRANSFER => true,
    				// 		  CURLOPT_ENCODING => "",
    				// 		  CURLOPT_MAXREDIRS => 10,
    				// 		  CURLOPT_TIMEOUT => 30,
    				// 		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    				// 		  CURLOPT_CUSTOMREQUEST => "POST",
    				// 		  CURLOPT_POSTFIELDS => $bodAddCustomer,
    				// 		  CURLOPT_HTTPHEADER => array(
    				// 			"Authorization: $accToken",
    				// 			"Cache-Control: no-cache",
    				// 			"Content-Type: application/json"
    				// 		  ),
    		        // 		));     
				
				
    				curl_setopt_array($curl, array(
    				CURLOPT_URL => 'https://et-api.det.gov.ae/customers?api_key=sz4pbntphrygvseq2dr98vh8',
    				CURLOPT_RETURNTRANSFER => true,
    				CURLOPT_ENCODING => '',
    				CURLOPT_MAXREDIRS => 10,
    				CURLOPT_TIMEOUT => 30,
    				CURLOPT_FOLLOWLOCATION => true,
    				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    				CURLOPT_CUSTOMREQUEST => 'POST',
    				CURLOPT_POSTFIELDS =>$bodAddCustomer,
    				CURLOPT_HTTPHEADER => array(
        				'Authorization: Bearer '.$this->accessToken,
        				'Content-Type: application/json'
    				),
    				));
 
						$AddUserResp = curl_exec($curl);
						$err = curl_error($curl);

						curl_close($curl);
						
						if ($err) {
						  return "E3";
						} else {
							
						  $AddUserArr=json_decode($AddUserResp, TRUE);
							$ErCode = $AddUserArr['Code'];
							if(isset($ErCode)){
								$CustomerData['Errormsg'] = $this->GenerateMsg(E31);
								if($DataExists==True){$rsCustomerData = $this->updateError($CustomerData,$Eventtblname);}
								return "E31";
							}	
							$UserId = $AddUserArr['id'];
							$UserAccount = $AddUserArr['account'];
							$UserAFile = $AddUserArr['aFile'];
							 // echo "\n UserId:".$UserId;
							 // echo "\n Account:".$UserAccount;
								if(!isset($UserId)||!isset($UserAccount)||!isset($UserAFile)){
									$CustomerData['Errormsg'] = $this->GenerateMsg(E32);
									if($DataExists==True){$rsCustomerData = $this->updateError($CustomerData,$Eventtblname);}
									return "E32";
								}
						/* Add Customer To Basket END */	

				
									/* Purchase Basket  */
									$bodPurBasket ="{\"Seller\":\"$Seller\",\"customer\":{\"ID\":$UserId,\"Account\":$UserAccount,\"AFile\":\"$UserAFile\"},\"Payments\":[{\"Amount\":$Amnt,\"MeansOfPayment\":\"EXTERNAL\"}]}";
									$URLPurBasket="https://et-api.det.gov.ae/baskets/".$BasketId."/purchase?api_key=sz4pbntphrygvseq2dr98vh8";
			
									$curl = curl_init();

									curl_setopt_array($curl, array(
									  CURLOPT_URL => $URLPurBasket,
									  CURLOPT_RETURNTRANSFER => true,
									  CURLOPT_ENCODING => "",
									  CURLOPT_MAXREDIRS => 10,
									  CURLOPT_TIMEOUT => 30,
									  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
									  CURLOPT_CUSTOMREQUEST => "POST",
									  CURLOPT_POSTFIELDS => $bodPurBasket,
									  CURLOPT_HTTPHEADER => array(
										'Authorization: Bearer '.$this->accessToken,
										"Cache-Control: no-cache",
										"Content-Type: application/json",
									  ),
									));

									$PurBasketResp = curl_exec($curl);
									$err = curl_error($curl);
									curl_close($curl);

									if ($err) {
									  return "E4";
									} else {
									  $PurBasketArr=  json_decode($PurBasketResp, TRUE);
									  $OrdrId = $PurBasketArr['orderId'];
											if(!isset($OrdrId)){
												$CustomerData['Errormsg'] = $this->GenerateMsg(E41);
												if($DataExists==True){$rsCustomerData = $this->updateError($CustomerData,$Eventtblname);}
												return "E41";
											}
									  //echo "\n OrdrId".$OrdrId;
								
									/* Purchase Basket END  */
						
				
												/*  View Order  */

												// $URLViewOrder="https://api.etixdubai.com/orders/$OrdrId?sellerCode=$Seller";
												$URLViewOrder="https://et-api.det.gov.ae/orders/".$OrdrId;
												$curl = curl_init();

												curl_setopt_array($curl, array(
												  CURLOPT_URL => $URLViewOrder,
												  CURLOPT_RETURNTRANSFER => true,
												  CURLOPT_ENCODING => "",
												  CURLOPT_MAXREDIRS => 10,
												  CURLOPT_TIMEOUT => 30,
												  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
												  CURLOPT_CUSTOMREQUEST => "GET",
												  CURLOPT_HTTPHEADER => array(
													"Authorization: Bearer ".$this->accessToken,
													"Cache-Control: no-cache",
													"Content-Type: application/json"
												  ),
												));

												$ViewOrderResp = curl_exec($curl);
												$err = curl_error($curl);

												curl_close($curl);

												if ($err) {
												  return "E5";
												} else {
												   // echo $ViewOrderResp;
												   $ViewOrderArr = json_decode($ViewOrderResp, TRUE);//echo $ViewOrderResp;
												   $Barcode =$ViewOrderArr['orderLines'][0]['orderLineItems'][0]['barcode'];
												   //echo "Barcode".$Barcode;	
												   
												   $CustomerData['OrderId']=$OrdrId;
												   $CustomerData['Barcode']=$Barcode;
												   $CustomerData['PerformanceId']=$PricetypeId;
												   $CustomerData['perf_price']=$Amnt;
												   $CustomerData['Errormsg'] =NULL;
												   												   												  												   
														if(isset($Barcode)){
														if($DataExists==True){$rsCustomerData = $this->updateBarcode($CustomerData,$Eventtblname);}else{$rsCustomerData = $db->query($db->InsertQuery($Eventtblname, $CustomerData));}
														return "ID:".$EAirId."<br/>  Name :".$fname.".   Email :".$email.".  Barcode :".$Barcode;
														}else{
															$CustomerData['Errormsg'] = $this->GenerateMsg(E51);
															if($DataExists==True){$rsCustomerData = $this->updateError($CustomerData,$Eventtblname);}else{$rsCustomerData = $db->query($db->InsertQuery($Eventtblname, $CustomerData));}
															return "E51";
														}
												}	// View Order Bracket END		
									} // Purchase Basket Bracket END
						} // Add Customer  Bracket END
				}
		
	
    }
	
	
	function getDelegateDataFromFile($FId){
	    
		global $db;
		$rs = $db->query("SELECT `file_name` FROM " . TBL_SMS_UP_CSV." where `id` ='$FId'");
		$fileNameArr = $rs->fetch_array();		
		$flname = $fileNameArr['file_name'];
		$flname=str_replace("[","",$flname);
		$flname=str_replace("]","",$flname);
		$flname=str_replace('"','',$flname);
		$rows = array_map('str_getcsv', file('uploads/'.$flname));
		$header = array_shift($rows);
		$csv = array();
		foreach ($rows as $row) {
		$csv[] = array_combine($header, $row);
		}
		return($csv);		
	}
	
	
	function getDelegateData($EventId,$UserId){
	    
		global $db;
		$EventData = $this->GetRowContent($EventId);
		$Eventblname = $EventData['Eventblname'];
		$UserId = join("','",$UserId);
		//echo "SELECT * FROM " .$Eventblname." where `eventsair_id` IN ('$UserId')";exit;
		$rs = $db->query("SELECT * FROM " .$Eventblname." where `eventsair_id` IN ('$UserId')");
		while ($row = $rs->fetch_array())
        {
            $resultrow[$i++] = $row;
        }
		$j=0;
		foreach($resultrow as $rsarray){
			$rsltarray[$j]=$rsarray;
			$j++;
		}
		//print_r($rsltarray);exit;
        if ($rsltarray)
        return $rsltarray;
        else
        return $row;
			
	}
	
	function updateError($UpdateData,$UpdateTbl){
	global $db;
	
	$EventId=$UpdateData['event_id'];
	$Eventblname = $UpdateTbl;
	
	$EAirId=$UpdateData['eventsair_id'];
	$UpErr=$UpdateData['Errormsg'];
	
	$rs = $db->query("UPDATE `".$Eventblname."` SET `Errormsg`='".$UpErr."' WHERE `eventsair_id`=".$EAirId);	
	if ($rs)
    return true;
    else
    return false;
	}
	
	function updateOrderId($UpdateData,$UpdateTbl){
	global $db;
	
	$EventId=$UpdateData['event_id'];
	$Eventblname = $UpdateTbl;
	
	$EventId=$UpdateData['event_id'];
	$EAirId=$UpdateData['eventsair_id'];
	$UpOrderId=$UpdateData['OrdrId'];
	$UpAmnt=$UpdateData['perf_price'];
	
	$rs = $db->query("UPDATE `".$Eventblname."` SET `OrderId`='".$UpOrderId."',`perf_price`='".$UpAmnt."' WHERE `eventsair_id`=".$EAirId);	
	if ($rs)
    return true;
    else
    return false;
	}
	
	function updateBarcode($UpdateData,$UpdateTbl){ 
	global $db;
	
	$EventId=$UpdateData['event_id'];	
	$Eventblname = $UpdateTbl;
	$EAirId=$UpdateData['eventsair_id'];
	$UpErr=NULL;
	$UpBarCod=$UpdateData['Barcode'];												 
	$UpOrdrId=$UpdateData['OrderId'];
	$UpAmnt=$UpdateData['perf_price'];
	//echo "UPDATE `".$Eventblname."` SET `Errormsg`='".$UpErr."',`Barcode`='".$UpBarCod."',`perf_price`='".$UpPerfPric."',`OrderId`='".$UpOrdrId."' WHERE `eventsair_id`=".$EAirId;
	$rs = $db->query("UPDATE `".$Eventblname."` SET `Errormsg`='".$UpErr."',`Barcode`='".$UpBarCod."',`perf_price`='".$UpAmnt."' WHERE `eventsair_id`=".$EAirId);	
	if ($rs)
    return true;
    else
    return false;
	}
	
	
	function GenerateBulkTicket($EventId,$UserId){
	
        global $db;
        $resultrow = array();
		$returnData = array();
		$DelegateData = array();
		$ErrorData = array();
		
		$DelegateData = $this->getDelegateData($EventId,$UserId);
		
		$Seller = $this->sellerCode;

		$EventData = $this->GetRowContent($EventId);
		$PerfomCode = $EventData['perfcode'];
		$Eventblname = $EventData['Eventblname'];
		
		$ObjPerfAreas = load_class('PerfAreas');
		$PerfAreas = $ObjPerfAreas -> GetRowContentByEid($EventId);
		
		
		$CustomerData['event_id']=$EventId;
		set_time_limit(1800);
		
		
		for($i=0;$i<count($DelegateData);$i++){
			//for($i=0;$i<2;$i++){
				//echo "<br/>from loop".$i."<br/>";
				$EAirId=$DelegateData[$i]['eventsair_id'];		
				$salutatn="Default";
				$fname=$DelegateData[$i]['Fname'];
				$lname=$DelegateData[$i]['Lname'];		
				$dob=$DelegateData[$i]['dob'];
				$phnumber=$DelegateData[$i]['MobNumber'];
				$email=$DelegateData[$i]['email'];
				$DelegatePerfId	=$DelegateData[$i]['PerformanceId'];
				
				$EAirIdTrue=false;
				$returnData[$i]['EAirId']=$EAirId;
				
					/* Data To insert in Result Table */
					$CustomerData['eventsair_id']=$EAirId;
					$CustomerData['OrderId']=NULL;
					$CustomerData['Barcode']=NULL;
					
				/*
				if (in_array($EAirId, $BarDelgEAirId)){
					$returnData[$i]['Message']="Barcode Already Generated.";
					continue;
				}else{
					//echo "<br/>NOT In Array ID:".$EAirId."<br/>" ;
				}
					
				if(in_array($EAirId, $ErrRegDelgEAirId)){
					$EAirIdTrue=True;
				}
				*/
				
							
				
					for($n=0;$n<count($PerfAreas);$n++){					
						if($PerfAreas[$n]['id'] == $DelegatePerfId){
							$AreaType=$PerfAreas[$n]['areacode'];
							$PriceTypeCod=$PerfAreas[$n]['pricetypecode'];
							$Amnt =$PerfAreas[$n]['price'];
							$PerfId =$PerfAreas[$n]['id'];
						}
						//echo "Area".$AreaType;echo "PriceTypeCod".$PriceTypeCod;echo "Amnt".$Amnt;echo "PerfId".$PerfId."<br/>";
					}
					
					if(!isset($AreaType)||!isset($PriceTypeCod)||!isset($Amnt)){
						//echo"<br/> AREA TYPE FETCH ERROR FOR ID:-".$EAirId." Email:".$email."<br/>";
						$CustomerData['Errormsg']="AREA TYPE FETCH ERROR.";
						$rsCustomerData = $this->updateError($CustomerData,$Eventblname);
						$returnData[$i]['Error']="AREA TYPE FETCH ERROR.";
						continue;
					}

				$nation = $DelegateData[$i]['Nationality'];
				$CCcode = $DelegateData[$i]['PACountry'];		
				$nation = $this->GetCountry($NCountry);
				$CCcode = $this->GetCountry($PACountry);
				$cty="Default";
				$stat="Default";
				// 	$bodAddCustomer = "{\"salutation\":\"$salutatn\",\"firstname\":\"$fname\",\"lastname\":\"$lname \",\"nationality\":\"$nation\",\"email\":\"$email\",\"dateofbirth\":\"$dob\",\"phonenumber\":\"$phnumber\",\"city\":\"$cty\",\"state\":\"$stat\",\"countrycode\":\"$CCcode\"}\r\n";
				// 			echo $bodAddCustomer; PVEN12JAN2024M	
				// 			die();
					
				$accToken = "Bearer ".$this->accessToken;
				
						/* Add Basket  */
						$bodAddBasket="{\"Channel\":\"W\",\"Seller\":\"$Seller\",\"Performancecode\":\"$PerfomCode\",\"Area\":\"$AreaType\",\"Demand\":[{\"PriceTypeCode\":\"$PriceTypeCod\",\"Quantity\":1,\"Admits\":1,\"Customer\":{}}],\"Fees\":[{\"Type\":\"5\",\"Code\":\"W\"}]}\n";
						$basketUrl="https://et-api.det.gov.ae/baskets";
						$curl = curl_init();

						curl_setopt_array($curl, array(
						  CURLOPT_URL => $basketUrl,
						  CURLOPT_RETURNTRANSFER => true,
						  CURLOPT_ENCODING => "",
						  CURLOPT_MAXREDIRS => -1,
						  CURLOPT_TIMEOUT => 0,
						  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
						  CURLOPT_CUSTOMREQUEST => "POST",
						  CURLOPT_POSTFIELDS => $bodAddBasket,
						  CURLOPT_HTTPHEADER => array(
							"Authorization: Bearer ".$this->accessToken,
							"Cache-Control: no-cache",
							"Content-Type: application/json"
						  ),
						));

						$AddBasketResp = curl_exec($curl);
						$err = curl_error($curl);

						curl_close($curl);

						if ($err) {
						  $returnData[$i]['Error']="ERROR 2.0 - Basket Creation API Call Failed";
						  continue;
						  
						} else {					
						  $AddBasketArr=json_decode($AddBasketResp, TRUE);
						  $BasketId = $AddBasketArr['Id'];
								if(!isset($BasketId)){								
									$CustomerData['Errormsg']="ERROR 2.1 - Basket Creation Failed.";
									$rsCustomerData = $this->updateError($CustomerData,$Eventblname);
									$returnData[$i]['Error']="ERROR 2.1 - Basket Creation Failed.";
									continue;
								}
																
							/* Add Basket END  */				
						
								/* Add Customer To Basket */
								$bodAddCustomer = "{\"salutation\":\"$salutatn\",\"firstname\":\"$fname\",\"lastname\":\"$lname \",\"nationality\":\"$nation\",\"email\":\"$email\",\"dateofbirth\":\"$dob\",\"phonenumber\":\"$phnumber\",\"city\":\"$cty\",\"state\":\"$stat\",\"countrycode\":\"$CCcode\"}\r\n";
								
								$curl = curl_init();
								
								curl_setopt_array($curl, array(
								  CURLOPT_URL => "https://et-api.det.gov.ae/customers?sellerCode=AMMEE1",
								  CURLOPT_RETURNTRANSFER => true,
								  CURLOPT_ENCODING => "",
								  CURLOPT_MAXREDIRS => -1,
								  CURLOPT_TIMEOUT => 0,
								  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
								  CURLOPT_CUSTOMREQUEST => "POST",
								  CURLOPT_POSTFIELDS => $bodAddCustomer,
								  CURLOPT_HTTPHEADER => array(
									"Authorization: Bearer ". $this->accessToken,
									"Cache-Control: no-cache",
									"Content-Type: application/json"
								  ),
								));
								
								$AddUserResp = curl_exec($curl);
								$err = curl_error($curl);
								
								curl_close($curl);
								
								if ($err) {								  
								  $returnData[$i]['Error']="ERROR 3.0 - Add Cusomer API Call Failed";
								  continue;
								} else {
									
								  $AddUserArr=json_decode($AddUserResp, TRUE);
									$ErCode = $AddUserArr['Code'];
									$ErMsg = $AddUserArr['Message'];
									if(isset($ErCode)){									 
									$CustomerData['Errormsg']="ERROR 3.1 - Code:".$ErCode."Message:".$ErMsg;
									$rsCustomerData = $this->updateError($CustomerData,$Eventblname);
									$returnData[$i]['Error']="ERROR 3.1 - Code:".$ErCode."Message:".$ErMsg;
									continue;
									}	
									$UserId = $AddUserArr['ID'];
									$UserAccount = $AddUserArr['Account'];
									$UserAFile = $AddUserArr['AFile'];
									
										if(!isset($UserId)||!isset($UserAccount)||!isset($UserAFile)){										 
										$CustomerData['Errormsg']="ERROR 3.2 - User ID API Return Failed";
										$rsCustomerData = $this->updateError($CustomerData,$Eventblname);
										$returnData[$i]['Error'] = "ERROR 3.2 - User ID API Return Failed";
										continue;
										}
										
								/* Add Customer To Basket END */	
						
											/* Purchase Basket  */
											$bodPurBasket ="{\"Seller\":\"$Seller\",\"customer\":{\"ID\":$UserId,\"Account\":$UserAccount,\"AFile\":\"$UserAFile\"},\"Payments\":[{\"Amount\":$Amnt,\"MeansOfPayment\":\"EXTERNAL\"}]}";
											//$URLPurBasket="https://api.etixdubai.com/Baskets/".$BasketId."/purchase";
											$URLPurBasket="https://et-api.det.gov.ae/baskets/".$BasketId."/purchase";
											

											$curl = curl_init();

											curl_setopt_array($curl, array(
											  CURLOPT_URL => $URLPurBasket,
											  CURLOPT_RETURNTRANSFER => true,
											  CURLOPT_ENCODING => "",
											  CURLOPT_MAXREDIRS => -1,
											  CURLOPT_TIMEOUT => 0,
											  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
											  CURLOPT_CUSTOMREQUEST => "POST",
											  CURLOPT_POSTFIELDS => $bodPurBasket,
											  CURLOPT_HTTPHEADER => array(
												"Authorization: Bearer ".$this->accessToken,
												"Cache-Control: no-cache",
												"Content-Type: application/json",
											  ),
											));
											$PurBasketResp = curl_exec($curl);
											$err = curl_error($curl);
											curl_close($curl);
											if ($err) {
											  $returnData[$i]['Error'] = "ERROR 4.0 - Purchase Basket API Call Failed.";
											  continue;
											} else {
											  $PurBasketArr=  json_decode($PurBasketResp, TRUE);
											  $OrdrId = $PurBasketArr['OrderId'];
													if(!isset($OrdrId)){
													//echo "ERROR4.1";
													$CustomerData['Errormsg']="ERROR 4.1 - Order ID Return Failed.";
													$rsCustomerData = $this->updateError($CustomerData,$Eventblname);
													$returnData[$i]['Error'] = "ERROR 4.1 - Order ID Return Failed.";
													continue;
													}else{
													//echo "OrdrId".$OrdrId."<br/>";
													$CustomerData['OrdrId']=$OrdrId;
													$CustomerData['perf_price']=$Amnt;
													$rsCustomerData = $this->updateOrderId($CustomerData,$Eventblname);													
													}
										
												/* Purchase Basket END  */	
											
														/*  View Order  */

														$URLViewOrder="https://et-api.det.gov.ae/orders/".$OrdrId."?sellerCode=AMMEE1";
														$curl = curl_init();
														curl_setopt_array($curl, array(
														  CURLOPT_URL => $URLViewOrder,
														  CURLOPT_RETURNTRANSFER => true,
														  CURLOPT_ENCODING => "",
														  CURLOPT_MAXREDIRS => -1,
														  CURLOPT_TIMEOUT => 0,
														  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
														  CURLOPT_CUSTOMREQUEST => "GET",
														  CURLOPT_HTTPHEADER => array(
															"Authorization: Bearer ".$this->accessToken,
															"Cache-Control: no-cache",
															"Content-Type: application/json"
														  ),
														));
														$ViewOrderResp = curl_exec($curl);
														$err = curl_error($curl);
														curl_close($curl);
														if ($err) {												  
														  $returnData[$i]['Error'] = "ERROR 5.0 - View Order API Call Failed";
														  continue;
														} else {												   
														   $ViewOrderArr = json_decode($ViewOrderResp, TRUE);
														   $Barcode =$ViewOrderArr['OrderItems'][0]['OrderLineItems'][0]['Barcode'];												   
														   
														   $CustomerData['Barcode']=$Barcode;														   														   														   
																																																		   
																if(isset($Barcode)){
																//$c=count($BarDelgEAirId);$BarDelgEAirId[$c]=$EAirId;
																$CustomerData['Errormsg'] =NULL;
																$rsCustomerData = $this->updateBarcode($CustomerData,$Eventblname);										
																$returnData[$i]['Message'] = "Name :".$fname.". EventsAir ID :".$EAirId.".  Barcode :".$Barcode;
																}else{
																	
																	$CustomerData['Errormsg']="ERROR 5.1 - Barcode Not Found!!";
																	$rsCustomerData = $this->updateError($CustomerData,$Eventblname);
																	$returnData[$i]['Error'] = "ERROR 5.1 - Barcode Not Found!!";
																	continue;
																}
														}	// View Order Bracket END		
											} // Purchase Basket Bracket END
								} // Add Customer  Bracket END
						} // Add Basket Bracket END

		}// For LOOP END
		return $returnData;
   }
   
   
   	function GenerateTicketFrForm($CData){
	//echo "from Function";	
        global $db;
        $resultrow = array();
        //$arrData = $this->getArrData();	
		//print_r($CData);	exit;
		$EventId=$CData['eid'];
		$Eventname = $this->GetEventName($EventId);
		$rid=rand(10,10000);
		
		$RegDelgData = $this->getRegDelgEAirId($EventId);//print_r($RegDelgData);
		for($k=0;$k<count($RegDelgData);$k++){ $RegDelgEAirId[$k]=$RegDelgData[$k]['eventsair_id'];}
		
		$email=$CData['userEmail'];
	
		$uid=$CData['uid'];
		$fname=$CData['firstName'];
		
		if (in_array($uid, $RegDelgEAirId)){
			return "Barcode Already Generated For ID:".$uid." Name:".$fname."<br/>" ;			
		}
		
		$EventData = $this->GetRowContent($EventId);
	
		$PerfomCode = $EventData['perfcode'];
		
		$PricetypeId=$CData['PerfCatid'];
		$PricetypeData = $this->GetPricetypeData($PricetypeId);
		$AreaType=$PricetypeData['areacode'];
		$PriceTypeCod=$PricetypeData['pricetypecode'];
		$Amnt =$PricetypeData['price'];
		
		//$PerfomCode="ETES2015983M";
		$Seller = $this->sellerCode;
		
		//$salutatn=$CData['salutation'];
		$salutatn="Default";		
		$lname=$CData['lastName'];
		$nation=$CData['Nation'];		
		$dobDate=$CData['dob-date'];
		$dobMonth=$CData['dob-month'];
		$dobYear=$CData['dob-year'];
		$dob=$dobDate."-".$dobMonth."-".$dobYear;
		//$dob = "11-12-2000";
		$phnumber=$CData['mnumber'];
		$cty=$CData['city'];
		//$stat=$CData['state'];
		$stat="Default";
		$CCcode=$CData['country'];		
		//$accToken = $this->GenerateToken();
		$accToken = "Bearer ".$this->accessToken;

	
				/* Add Basket  */
				$bodAddBasket="{\"Channel\":\"W\",\"Seller\":\"$Seller\",\"Performancecode\":\"$PerfomCode\",\"Area\":\"$AreaType\",\"Demand\":[{\"PriceTypeCode\":\"$PriceTypeCod\",\"Quantity\":1,\"Admits\":1,\"Customer\":{}}],\"Fees\":[{\"Type\":\"5\",\"Code\":\"W\"}]}\n";

				$curl = curl_init();

				curl_setopt_array($curl, array(
				  CURLOPT_URL => "https://et-api.det.gov.ae/baskets",
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_ENCODING => "",
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 30,
				  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  CURLOPT_CUSTOMREQUEST => "POST",
				  CURLOPT_POSTFIELDS => $bodAddBasket,
				  CURLOPT_HTTPHEADER => array(
					"Authorization: $accToken",
					"Cache-Control: no-cache",
					"Content-Type: application/json"
				  ),
				));

				$AddBasketResp = curl_exec($curl);
				$err = curl_error($curl);

				curl_close($curl);

				if ($err) {
				  return "E2";
				} else {					
				  $AddBasketArr=json_decode($AddBasketResp, TRUE);
				  $BasketId = $AddBasketArr['Id'];
				 // echo "\n Basket: ".$BasketId;
						if(!isset($BasketId)){
							return "E21";
						}
					/* Add Basket END  */
				
				
						/* Add Customer To Basket */
						$bodAddCustomer = "{\"salutation\":\"$salutatn\",\"firstname\":\"$fname\",\"lastname\":\"$lname \",\"nationality\":\"$nation\",\"email\":\"$email\",\"dateofbirth\":\"$dob\",\"phonenumber\":\"$phnumber\",\"city\":\"$cty\",\"state\":\"$stat\",\"countrycode\":\"$CCcode\"}\r\n";
						$curl = curl_init();
						
						curl_setopt_array($curl, array(
						  CURLOPT_URL => $this->baseURL."customers?sellerCode=AMMEE1",
						  CURLOPT_RETURNTRANSFER => true,
						  CURLOPT_ENCODING => "",
						  CURLOPT_MAXREDIRS => 10,
						  CURLOPT_TIMEOUT => 30,
						  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
						  CURLOPT_CUSTOMREQUEST => "POST",
						  CURLOPT_POSTFIELDS => $bodAddCustomer,
						  CURLOPT_HTTPHEADER => array(
							"Authorization: $accToken",
							"Cache-Control: no-cache",
							"Content-Type: application/json"
						  ),
						));
						
						$AddUserResp = curl_exec($curl);
						$err = curl_error($curl);
						
						curl_close($curl);
						
						if ($err) {
						  return "E3";
						} else {
							
						  $AddUserArr=json_decode($AddUserResp, TRUE);
							$ErCode = $AddUserArr['Code'];
							if(isset($ErCode)){
							return "E31";
							}	
							$UserId = $AddUserArr['ID'];
							$UserAccount = $AddUserArr['Account'];
							$UserAFile = $AddUserArr['AFile'];
							 // echo "\n UserId:".$UserId;
							 // echo "\n Account:".$UserAccount;
								if(!isset($UserId)||!isset($UserAccount)||!isset($UserAFile)){
								return "E32";
								}
						/* Add Customer To Basket END */	

				
									/* Purchase Basket  */
									$bodPurBasket ="{\"Seller\":\"$Seller\",\"customer\":{\"ID\":$UserId,\"Account\":$UserAccount,\"AFile\":\"$UserAFile\"},\"Payments\":[{\"Amount\":$Amnt,\"MeansOfPayment\":\"EXTERNAL\"}]}";
									$URLPurBasket="https://et-api.det.gov.ae/baskets/".$BasketId."/purchase";

									$curl = curl_init();

									curl_setopt_array($curl, array(
									  CURLOPT_URL => $URLPurBasket,
									  CURLOPT_RETURNTRANSFER => true,
									  CURLOPT_ENCODING => "",
									  CURLOPT_MAXREDIRS => 10,
									  CURLOPT_TIMEOUT => 30,
									  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
									  CURLOPT_CUSTOMREQUEST => "POST",
									  CURLOPT_POSTFIELDS => $bodPurBasket,
									  CURLOPT_HTTPHEADER => array(
										"Authorization: $accToken",
										"Cache-Control: no-cache",
										"Content-Type: application/json",
									  ),
									));

									$PurBasketResp = curl_exec($curl);
									$err = curl_error($curl);

									curl_close($curl);

									if ($err) {
									  return "E4";
									} else {
									  $PurBasketArr=  json_decode($PurBasketResp, TRUE);
									  $OrdrId = $PurBasketArr['OrderId'];
											if(!isset($OrdrId)){
											return "E41";
											}
									  //echo "\n OrdrId".$OrdrId;
								
									/* Purchase Basket END  */
						
				
												/*  View Order  */

												$URLViewOrder=$this->baseURL."orders/".$OrdrId."?sellerCode=".$Seller;
												$curl = curl_init();

												curl_setopt_array($curl, array(
												  CURLOPT_URL => $URLViewOrder,
												  CURLOPT_RETURNTRANSFER => true,
												  CURLOPT_ENCODING => "",
												  CURLOPT_MAXREDIRS => 10,
												  CURLOPT_TIMEOUT => 30,
												  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
												  CURLOPT_CUSTOMREQUEST => "GET",
												  CURLOPT_HTTPHEADER => array(
													"Authorization: $accToken",
													"Cache-Control: no-cache",
													"Content-Type: application/json"
												  ),
												));

												$ViewOrderResp = curl_exec($curl);
												$err = curl_error($curl);

												curl_close($curl);

												if ($err) {
												  return "E5";
												} else {
												   // echo $ViewOrderResp;
												   $ViewOrderArr = json_decode($ViewOrderResp, TRUE);//echo $ViewOrderResp;
												   $Barcode =$ViewOrderArr['OrderItems'][0]['OrderLineItems'][0]['Barcode'];
												   //echo "Barcode".$Barcode;	
												   $CustomerData['event_id']=$EventId;
												   $CustomerData['eventsair_id']=$uid;
												   $CustomerData['OrderId']=$OrdrId;
												   $CustomerData['Barcode']=$Barcode;
												   $CustomerData['fname']=$fname;
												   $CustomerData['lname']=$lname;
												   $CustomerData['email']=$email;
												   $CustomerData['rid']=$rid;
												   												   												  												   
														if(isset($Barcode)){
														//$rsCustomerData = $db->query($db->InsertQuery(TBL_CUSTOMER, $CustomerData));
														$Barcode_image=$this->Generate_BarcImage($Barcode);
														$emailresp = $this->SendEmail($fname,$lname,$email,$Eventname,$rid,$Barcode);
														return $emailresp;
														}else{
															return "E51";
														}
												}	// View Order Bracket END		
									} // Purchase Basket Bracket END
						} // Add Customer  Bracket END
				} // Add Basket Bracket END

    }
    
    function Generate_BarcImage($barc){
        
        include "barcode.php"; 
        // set Barcode39 object 
        // $barc="100001001";
        $bc = new Barcode39("$barc"); 
        
        $bc->barcode_text_size = 5; 
        // set barcode bar thickness (thick bars) 
        $bc->barcode_bar_thick = 4; 
        // set barcode bar thickness (thin bars) 
        $bc->barcode_bar_thin = 2; 
        // save barcode GIF file 
        $bc->draw("$barc.png");
       
    }    
	
	function SendEmail($fname,$lname,$email,$Eventname,$rid,$Barcode){
	    
        require 'PHPMailerAutoload.php';
        
        $mail = new PHPMailer;
        
        //$imag_Barcode="../images/barcode/".$Barcode.".png";
        $imag_Barcode=$Barcode.".png";
        //$full_name=$fname." ".$lname;
		
		$message ="		
    		<html>
    		<head>
    		<title>Thank You</title>
    		</head>
    		<body>	
    		<p>Dear ".$fname.",</p>
    		<p>Thank you for your registration to attend the ".$Eventname."</p>
    		<p>Your registration request has been allocated a unique account number. </p>
    		<p>Please take note of your account number below as you will require it on all future communications with the registration team.</p>
    		<p>Registration ID: ".$rid." </p>
    		<p>Barcode:</p>
    		<p><img src='cid:logo_2u' /></p>		
    		<p>Should you require any further assistance, please do not hesitate to contact us.</p>
    		<p>Best Regards,<br/>
    
    		".$Eventname." Secretariat<br/>
    		Professional Conference Organizer<br/>
    		c/o Meeting Minds Experts<br/>
    		Tel: +971 4 427 0492 - admin@meetingmindsexperts.com<br/>
    		P.O Box 502464 Dubai, United Arab Emirates<br/><br/>
    
    		Our Office Hours : Sun to Wed - 08:30-18:00 | Thu - 08:30-16:00 | Fri to Sat - Closed</p>
    		</body>
    		</HTML>
		" ;
		

		$subject     = $Eventname."- Thank you for your registration ";

            $mail->isSMTP();                                      // Set mailer to use SMTP
            $mail->Host = 'mail.meetingmindsdubai.com';  // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;                               // Enable SMTP authentication
            $mail->Username = 'adarsh@meetingmindsdubai.com';                 // SMTP username
            $mail->Password = 'Mxn?#8@E#+&K';                           // SMTP password
            $mail->SMTPSecure = 'tls';                            // Enable encryption, 'ssl' also accepted
            
            $mail->From = 'admin@meetingmindsdubai.com';
            $mail->FromName = $Eventname;
            $mail->addAddress($email, $fname);     // Add a recipient
            $mail->addReplyTo('admin@meetingmindsdubai.com', 'Information');

            $mail->WordWrap = 500;                                 // Set word wrap to 50 characters

            $mail->isHTML(true); // Set email format to HTML
            
            $mail->AddEmbeddedImage($imag_Barcode, 'logo_2u');
            
            $mail->Subject =  $subject;
            //$mail->Body    = "<p>This is a test picture:</P><img src='cid:logo_2u' />";
            $mail->Body= $message;
          // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';


						if(!$mail->send()) {
									 return "There was an error while sending the email.Please contact conference Secretariat";
										}else {
								    return "Thank you for your registration to attend the ".$Eventname.".<br/>A confirmation email has been sent to your registered email address.<br/>Should you require any further assistance, please do not hesitate to contact us.<br/><br/><b>Regards</b><br/> ".$Eventname." Secretariat<br/>C/o Meeting Minds Experts";
									   }  
																														

		
	}

	
	function GenerateMsg($erId){
			if($erId=="E11"){
				return "Error 11 :Token Fetch Failed !!! .Please contact conference Secretariat";
			}elseif($erId=="E1"){
				return "Error 1 :Token Generation API Call Failed .Please contact conference Secretariat";
			}elseif($erId=="E12"){
				return "Error 12 :Token Generation Failed .Please contact conference Secretariat";
			}elseif($erId=="E2"){
				return "Error 2 :Basket Creation API Call Failed. Please contact conference Secretariat";
			}elseif($erId=="E21"){
				return "Error 21 :Basket Creation Failed. Please Contact conference Secretariat";
			}elseif($erId=="E3"){
				return "Error 3 :Add Customer API Call Failed. Please Contact conference Secretariat";
			}elseif($erId=="E31"){
				return "ERROR 31 :Not all customer details were provided or some details were incorrect(2300). Please Contact conference Secretariat";
			}elseif($erId=="E32"){
				return "ERROR 32 :Customer Data Return Failed. Please Contact conference Secretariat";
			}elseif($erId=="E4"){
				return "ERROR 4 :Purchase Basket API Call Failed. Please Contact conference Secretariat";
			}elseif($erId=="E41"){
				return "ERROR 41 :Purchase Basket  Return Failed. Please Contact conference Secretariat ";
			}elseif($erId=="E5"){
				return "ERROR 5 :View Order API Call Failed. Please Contact conference Secretariat";
			}elseif($erId=="E51"){
				return "ERROR 51 :View Order Return Failed. Please Contact conference Secretariat";
			}else{
				return $erId;
			}
	}
}
// class Article end
?>