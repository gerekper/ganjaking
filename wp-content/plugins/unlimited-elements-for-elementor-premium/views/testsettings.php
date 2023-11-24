<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');


$operations = new ProviderOperationsUC();

$data = array();
$data["term"] = "a";
$data["q"] = "a";
$data["_type"] = "query";


function wpmlAutoTranslationTest(){
	
	$arrWidgets = apply_filters("wpml_elementor_widgets_to_translate",array());
	
	//test the class
	
	$testWidgetClass = "UE_WPML_INTEGRATION__content_accordion_elementor";
	
	foreach($arrWidgets as $widget){
		
		
		$class = UniteFunctionsUC::getVal($widget, "integration-class");
				
		if(empty($class))
			continue;			
		
		if(!empty($testWidgetClass) && $class != $testWidgetClass)
			continue;
			
		dmp($class);
		
		$objIntegration = new $testWidgetClass();

		$objIntegration->printTest();
			
	}
	
}

if(GlobalsUC::$inDev == true){

	wpmlAutoTranslationTest();
	
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