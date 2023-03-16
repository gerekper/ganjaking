<?php

defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

class UniteCreatorElementorBackgroundWidget extends UniteCreatorElementorWidget {
	
	
    /**
     * set the addon
     */
    public function __construct($data = array(), $args = null){
    	//skip constructor
    }
    
    
    /**
     * set the addon
     */
    public function initBGWidget($objAddon, $objControls){
    	
    	$this->isBGWidget = true;
    	
    	$this->objAddon = $objAddon;
    	$this->objControls = $objControls;
    }
    
    /**
     * modify background widget params
     */
    protected function modifyBGWidgetParams($params){
    	
    	$alias = $this->objAddon->getAlias();
    	
    	$condition = array(UniteCreatorElementorIntegrate::CONTROL_BACKGROUND_TYPE=>$alias);
    	
    	foreach($params as $key=>$param){
    		
    		//modify nmae
    		$name = UniteFunctionsUC::getVal($param, "name");
    		if(empty($name))
    			continue;
    		    		
    		$param["name"] = $alias."_".$name;
    		$param["elementor_condition"] = $condition;
    		
    		//modify condition
    		$conditionAttribute = UniteFunctionsUC::getVal($param, "condition_attribute");
    		if(!empty($conditionAttribute))
    			$param["condition_attribute"] = $alias."_".$conditionAttribute;
    		    		
    		$params[$key] = $param;
    	}

    	
    	return($params);
    }
    
    /**
     * add no params heading
     */
    private function addNoParamsBGHeading(){
    	
    	$alias = $this->objAddon->getAlias();
    	
    	$condition = array(UniteCreatorElementorIntegrate::CONTROL_BACKGROUND_TYPE=>$alias);
    	
    	$name = $alias."_no_params";
    	
		$this->objControls->add_control(
					$name,
					array(
						'label' => __( 'No settings for this background', 'unlimited_elements' ),
						'type' => \Elementor\Controls_Manager::HEADING,
						'condition'=>$condition
					)
		);    	
    	
    }
    
    /**
     * register background controls
     */
    public function registerBGControls(){

    	 if(empty($this->objAddon))
    	 	return(false);
    	
    	 $isItemsEnabled = $this->objAddon->isHasItems();
    	 $itemsType = $this->objAddon->getItemsType();
    	     	 
         $params = $this->objAddon->getProcessedMainParams();
    	 
         $isItemsEnabled = $this->objAddon->isHasItems();
         $itemsType = $this->objAddon->getItemsType();
         
         $isAddItems = false;
         if($isItemsEnabled == true && $itemsType != UniteCreatorAddon::ITEMS_TYPE_IMAGE)
         	$isAddItems = true;
                  	
         
         if(empty($params)){
	     	
         	$this->addNoParamsBGHeading();
         	return(false);
         }
         
         $params = $this->modifyBGWidgetParams($params);
         
	     $params = $this->addDynamicAttributes($params);          	
         	     
          foreach($params as $param){
          		
          		$type = UniteFunctionsUC::getVal($param, "type");
          		
          		if($type == UniteCreatorDialogParam::PARAM_POSTS_LIST){
          			continue;
          		}
          		
          		$this->addElementorParamUC($param);
          }
          
          if($isAddItems == true)
          	$this->addItemsControlsUC($itemsType);
          	
    }
    
    
    /**
     * get background settings by section settings
     */
    public function getBGSettings($arrSettings, $bgType){
    	
    	$arrBGSettings = array();
    	$typeSearch = $bgType."_";
    	
    	foreach($arrSettings as $key => $value){
    		
    		if(strpos($key, $typeSearch) !== 0)
    			continue;
    		
    		$addonKey = UniteFunctionsUC::replaceFirstSubstring($key, $bgType."_", "");
    		
    		$arrBGSettings[$addonKey] = $value;
    	}
    	
    	return($arrBGSettings);
    }
    
}