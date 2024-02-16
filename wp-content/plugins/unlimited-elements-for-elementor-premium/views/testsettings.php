<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');



function ueCheckCatalog(){
	
	$webAPI = new UniteCreatorWebAPI();

	$response = $webAPI->checkUpdateCatalog();

	$lastAPIData = $webAPI->getLastAPICallData();
	
	$arrAddons = $webAPI->getCatalogAddonsByTags(UniteCreatorWebAPI::TAG_ANIMATION);
	
	dmp("addons that support animation");
	
	dmp($arrAddons);
	exit();
	
	
}

if(GlobalsUC::$inDev == true){

	ueCheckCatalog();
	
}



/*
$manager = new UniteFontManagerUC();
$manager->fetchIcons();
*/

//$font = new UniteFontManagerUC();
//$font->fetchIcons();


exit();

/*
$webAPI = new UniteCreatorWebAPI();

dmp("update catalog");

$response = $webAPI->checkUpdateCatalog();

dmp("update catalog response");

dmp($response);

$lastAPIData = $webAPI->getLastAPICallData();
$arrCatalog = $webAPI->getCatalogData();

//$arrNames = $webAPI->getArrAddonNames($arrCatalog["catalog"]);


dmp($lastAPIData);
dmp($arrCatalog);
exit();

*/