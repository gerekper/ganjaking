<?php

function wpmlAutoTranslationTest(){
	
	if(UniteCreatorWpmlIntegrate::isWpmlExists() == false && GlobalsUC::$inDev == false){
		
		dmp("wpml plugin not installed");
		return(false);
	}
	
	$arrWidgets = apply_filters("wpml_elementor_widgets_to_translate",array());
	
	dmp("Those widgets are selected for the wpml auto translate:");
	
	foreach($arrWidgets as $name => $fields){
		
		dmp("<b>$name</b>");
		
		$arrFields = UniteFunctionsUC::getVal($fields, "fields");
		
		dmp("main fields:");
		
		dmp($arrFields);
		
		if(isset($fields["integration-class"]) == false)
			continue;
			
		$widgetName = str_replace("ucaddon_","",$name)."_elementor";
		
		
		$arrItemsFields = UniteFunctionsUC::getVal(UniteCreatorWpmlIntegrate::$arrWidgetItemsData, $widgetName);

		
		dmp("items fields: ");
		dmp($arrItemsFields);
		
		
	}
	
}

wpmlAutoTranslationTest();