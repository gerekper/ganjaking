<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');
?>


<h1>Unlimited Elements - API Access Test</h1>

<br>

<?php

/**
 * check zip file request
 */
function checkZipFile(){

	//request single file
	dmp("requesting widget zip from API");

	$response = UEHttp::make()->post(GlobalsUC::URL_API, array(
		"action" => "get_addon_zip",
		"name" => "team_member_box_overlay",
		"cat" => "Team Members",
		"type" => "addons",
		"catalog_date" => "1563618449",
		"code" => "",
	));

	$data = $response->body();

	if(empty($data))
		UniteFunctionsUC::throwError("Empty server response");

	$len = strlen($data);

	dmp("api response OK, received string size: $len");
}


/**
 * check zip file request
 */
function checkCatalogRequest(){

	try{
		dmp("requesting catalog check");

		$response = UEHttp::make()->post(GlobalsUC::URL_API, array(
			"action" => "check_catalog",
			"catalog_date" => "1563618449",
			"include_pages" => false,
			"domain" => "localhost",
			"platform" => "wp",
		));

		$data = $response->body();

		if(empty($data))
			UniteFunctionsUC::throwError("Empty server response");

		$len = strlen($data);

		dmp("api response OK, received string size: $len");
	}catch(Exception $e){
		$message = $e->getMessage() . "\n<br>";

		$message .= "The request to the catalog url has failed. \n<br>";
		$message .= "Please contact your hosting provider and request to open firewall access to this address: \n<br>";
		$message .= "http://api.unlimited-elements.com/";

		UniteFunctionsUC::throwError($message);
	}
}

/**
 * various
 */
function checkVariousOptions(){

	dmp("checking file get contents");

	$urlAPI = GlobalsUC::URL_API;
	$response = file_get_contents($urlAPI);

	$len = strlen($response);

	dmp("file get contents OK, received string size: $len");

}

/**
 * check and update catalog
 */
function checkUpdateCatalog(){

	dmp("Trying to update the catalog from the api... Printing Debug...");

	$webAPI = new UniteCreatorWebAPI();

	$webAPI->checkUpdateCatalog(true);

	$arrDebug = $webAPI->getDebug();

	dmp($arrDebug);

	//print option content
	$optionCatalog = UniteCreatorWebAPI::OPTION_CATALOG;

	dmp("Option catalog raw data: $optionCatalog");

	$data = get_option($optionCatalog);

	dmp($data);

}


/**
 * check if catalog data is saved well
 */
function checkingCatalogData(){

	$webAPI = new UniteCreatorWebAPI();
	$data = $webAPI->getCatalogData();


	dmp("Checking saved widgets catalog data");

	if(empty($data)){

		dmp("No catalog widgets data found!");

		checkUpdateCatalog();

		return(false);
	}

	if(is_array($data) == false)
		UniteFunctionsUC::throwError("Catalog data is not array");

	$stamp = UniteFunctionsUC::getVal($data, "stamp");
	$catalog = UniteFunctionsUC::getVal($data, "catalog");

	if(empty($stamp))
		UniteFunctionsUC::throwError("No stamp found");

	if(empty($catalog))
		UniteFunctionsUC::throwError("Empty widgets catalog");

	$date = UniteFunctionsUC::timestamp2Date($stamp);

	dmp("catalog data found OK from date: $date");

	$showData = UniteFunctionsUC::getGetVar("showdata","", UniteFunctionsUC::SANITIZE_TEXT_FIELD);
	$showData = UniteFunctionsUC::strToBool($showData);

	if($showData == true)
		dmp($data);

}

try{

	checkVariousOptions();

	echo "<br><br>";

	checkCatalogRequest();

	echo "<br><br>";

	checkZipFile();

	echo "<br><br>";

	checkingCatalogData();


}catch(Exception $e){
	echo $e->getMessage();
}
