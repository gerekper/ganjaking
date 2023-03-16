<?php

defined('UNLIMITED_ELEMENTS_INC') or die;

class UniteCreatorAddonViewProvider extends UniteCreatorAddonView{
	
	

	/**
	 * add dynamic fields child keys
	 */
	protected function addDynamicChildKeys($arrChildKeys){
		
		$isDynamicAddon = UniteFunctionsUC::getVal($this->addonOptions, "dynamic_addon");
		$isDynamicAddon = UniteFunctionsUC::strToBool($isDynamicAddon);
		
		if($isDynamicAddon == false)
			return($arrChildKeys);
			
		$postID = UniteFunctionsUC::getVal($this->addonOptions, "dynamic_post");
		
		if(empty($postID))
			return($arrChildKeys);
		
		$post = get_post($postID);
		
		if(empty($post))
			return($arrChildKeys);

		//add current post
		$arrPostAdditions = HelperProviderUC::getPostAdditionsArray_fromAddonOptions($this->addonOptions);
		
		//add current post child keys
		$arrChildKeys["uc_current_post"] = $this->getChildParams_post($postID, $arrPostAdditions);
		
		
		return($arrChildKeys);
	}
		
	
	
	/**
	 * get image param add fields
	 */
	protected function getImageAddFields(){
		
		$arrFields = array();
		$arrFields[] = "title";
		$arrFields[] = "alt";
		$arrFields[] = "description";
		$arrFields[] = "caption";
		
		return($arrFields);
	}
	
	
	/**
	 * get thumb sizes
	 */
	protected function getThumbSizes(){
		
		$arrThumbSizes = UniteFunctionsWPUC::getArrThumbSizes();
		
		//modify sizes
		$arrSizesModified = array();
		
		foreach($arrThumbSizes as $key => $size){
			
			if($key == "medium")
				continue;
				
			$key = str_replace("-", "_", $key);
			
			$arrSizesModified[$key] = $size;
		}
		
		return($arrSizesModified);
	}
	
	
	
}