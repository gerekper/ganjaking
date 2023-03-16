<h1>Unlimited Elements - Instagram Test</h1>

<br>

<?php

function UnlimitedElementsputInstagramTest(){
	
	$objServices = new UniteServicesUC();
	
	$arrData = $objServices->getInstagramSavedDataArray();
	
	if(empty($arrData)){
		dmp("no saved instagram data found");
		return(false);
	}
		
	dmp("<b>Saved Instagram Data</b>");
	
	foreach($arrData as $key=>$value){
		
		if($key == "expires")
			$value = UniteFunctionsUC::timestamp2Date($value);
		
		dmp("$key: $value");
		
	}
	
	$userName = $arrData["username"];
	
	$response = $objServices->getInstagramData($userName);
	
	if(!empty($response))
		dmp("<b>Instagram data found, all ok</b>");
	else 
		dmp("<b>Error: No Instagram Data Fetched</b>");
	
	dmp($response);
	
	
}


try{

	UnlimitedElementsputInstagramTest();
	
}catch(Exception $e){
	
	HelperHtmlUC::outputException($e);
	
}

