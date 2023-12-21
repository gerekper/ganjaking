<?php

defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Scheme_Color;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Core\Schemes;
use Elementor\Repeater;
use Elementor\Utils;


class UniteCreatorElementorWidget extends Widget_Base {
	
    protected $objAddon;
    
    private $isConsolidated = false;
    private $objCat, $arrAddons;
    private $isNoMemory = false;
    private $isNoMemory_addonName;
    private $listingName;
    private static $arrGlobalColors = array();
    protected $isBGWidget = false;
	protected $objControls;
	protected $isAddSapBefore = false;
    protected $tabsCounter = 1;
    private $hasDynamicSettings = false;
    
    const DEBUG_SETTINGS_VALUES = false;
    const DEBUG_WIDGETS_OUTPUT = false;
    
    const DEBUG_CONTROLS = false;
    const DEBUG_ITEMS_CONTROLS = false;
    
    
    private function a_______INIT______(){}
    
    
    /**
     * set the addon
     */
    public function __construct($data = array(), $args = null) {
		
        $className = get_class($this);
    	
        if(strpos($className, "UCAddon_uccat_") === 0)
        	$this->initByCategory($className);
        else
        	$this->initByAddon($className);

        $this->objControls = $this;
        
        parent::__construct($data, $args);
    }

    
    /**
	 * get help url
     */
	public function get_custom_help_url(){
		
		if(empty($this->objAddon))
			return(null);
		
		$link = $this->objAddon->getOption("link_resource");
		
		if(empty($link))
			$link = $this->objAddon->getOption("link_preview");
		
		$link = trim($link);
		
		if(!empty($link)){
			$isValid = filter_var($link, FILTER_VALIDATE_URL);
			if($isValid == true)
				return($link);
		}
		
		$options = $this->objAddon->getOptions();
		
		
		/*			
		$isPostListExists = $this->objAddon->isParamTypeExists(UniteCreatorDialogParam::PARAM_POSTS_LIST);
		
		if($isPostListExists == true)
			return(GlobalsUnlimitedElements::LINK_HELP_POSTSLIST);
		*/
		
		return(null);
	}
    
    
    /**
     * init by category
     */
    private function initByCategory($className){

    	$catName = str_replace("UCAddon_uccat_", "", $className);
        $catName = trim($catName);
    	
        $arrCache = UniteFunctionsUC::getVal(UniteCreatorElementorIntegrate::$arrCatsCache, $catName);
        if(empty($arrCache))
        	return(false);
        
        $this->isConsolidated = true;
        $this->objCat = $arrCache["objcat"];
        $this->arrAddons = $arrCache["addons"];
            	
    }
    
    
    /**
     * init by addon
     */
    private function initByAddon($className){
        
    	$addonName = str_replace("UCAddon_", "", $className);
        $addonName = trim($addonName);
        
        if(strpos($addonName, "_no_memory") !== false){
        	
        	$addonName = str_replace("_elementor", "", $addonName);
        	$addonName = str_replace("_no_memory", "", $addonName);
        	
        	$this->isNoMemory = true;
        	$this->isNoMemory_addonName = $addonName;
        	        	
        	return(false);
        }
        
        if(empty($addonName))
        	UniteFunctionsUC::throwError("Widget name is empty");
    	
    	$this->objAddon = new UniteCreatorAddon();
    	
    	$this->objAddon->setOperationType(UniteCreatorAddon::OPERATION_WIDGET);
    	
    	$record = UniteFunctionsUC::getVal(UniteCreatorElementorIntegrate::$arrAddonsRecords, $addonName);
    	
    	if(!empty($record))
        	$this->objAddon->initByDBRecord($record);
        else
        	$this->objAddon->initByName($addonName);
    	
    }
        
    private function a______GETTERS____(){}
    
    /**
     * get widget icon
     */
    private function ucGetWidgetIcon(){
    	
    	if($this->isNoMemory == true)
    		return UniteCreatorElementorIntegrate::DEFAULT_ICON;
    	
    	if($this->isConsolidated == true){
    		$icon = $this->objCat->getParam("icon");
    				
    		if(!empty($icon))
    			return($icon);
    		
    		
        	return UniteCreatorElementorIntegrate::DEFAULT_ICON;
    	}
		
    	$urlSvgIcon = $this->objAddon->getUrlSvgIconForEditor();
    	
		$text = $this->objAddon->getTitle();

    	$addonName = $this->objAddon->getAlias();
		
    	$className = "uewi-".$addonName;
		
    	$className = str_replace("_", "-", $className);
    	
    	$classNameBasic = $className;
    			
		$arrResponse = array();
		$arrResponse["text"] = $text;
    	
		$hasIcon = false;
    		
		$fontClass = $this->objAddon->getFontIcon();
		
		if(empty($fontClass))
			$fontClass = UniteCreatorElementorIntegrate::DEFAULT_ICON;
		
    	if(!empty($urlSvgIcon)){
    		
    		$hasIcon = true;
    		
			$className = "ue-wi-svg ".$className;
						
			$arrResponse["url_icon"] = $urlSvgIcon;			
    	}
    	
    	$className .= " ".$fontClass;
		
		//add preview
    	if(UniteCreatorElementorIntegrate::$showWidgetPreviews){
    	
			$urlPreview = $this->objAddon->getUrlPreview(false, false);
	    	if(!empty($urlPreview)){
	    		$arrResponse["url_preview"] = $urlPreview;
	    		
	    		$className .= " uc-wi-preview";
	    	}
    	}
	    	
		UniteCreatorElementorIntegrate::$arrWidgetIcons[$classNameBasic] = $arrResponse;
    	
        return $className;
    }
    
    
    /**
     * return category icon
     */
    public function get_icon() {
    	
    	$classIcon = $this->ucGetWidgetIcon();
    	
    	$addition = "";
    	if(UniteCreatorElementorIntegrate::$isDarkMode == true)
    		$addition = "ue-dark-mode ";
    	
    	
    	$classIcon = "ue-widget-icon $addition".$classIcon;
    	    	
    	return($classIcon);
    }
	
    
    /**
     * get addon category
     */
    public function get_categories() {
    	
    	if(UniteCreatorElementorIntegrate::$isOutputPage == true)
        	return array(UniteCreatorElementorIntegrate::ADDONS_CATEGORY_NAME);
    	
    	if($this->isConsolidated)
        	return array(UniteCreatorElementorIntegrate::ADDONS_CATEGORY_NAME);
    	
    	$catID = $this->objAddon->getCatID();
    	if(empty($catID))
        	return array(UniteCreatorElementorIntegrate::ADDONS_CATEGORY_NAME);
        
        $catName = UniteCreatorElementorIntegrate::getCategoryName($catID);
        
        return array($catName);
    }
	
    
    /**
	 * put scripts
     */
    public function get_script_depends() {
    	 
    	$isEditMode = $this->isEditMode();
    	 
    	if($isEditMode == true)
    		return(array());
    	
    	/*
    	
    	// - process from output
    	 
    	$arrScriptsHandles = $this->getScriptsDependsUC();
        return $arrScriptsHandles;
        */
    	
    	return(array());
    }
    
    
    /**
     * get widget name
     */
    public function get_name() {
		
    	if($this->isNoMemory == true){
    		$name = "ucaddon_".$this->isNoMemory_addonName;
    		
    		return($name);
    	}
    	
    	if($this->isConsolidated)
        	$name = "ucaddon_cat_".$this->objCat->getAlias();
    	else
        	$name = "ucaddon_".$this->objAddon->getAlias();    	
        
        return $name;
    }

    /**
     * get addon name
     */
    public function getAddonName(){
    	
    	$name = $this->objAddon->getName();
    	
    	return($name);
    }
    
    /**
     * get addon object
     */
    public function getObjAddon(){
    	
    	return($this->objAddon);
    }
    
    
    /**
     * get widget title
     */
    public function get_title() {
		
    	if($this->isNoMemory == true)
    		return($this->isNoMemory_addonName. "(no memory)");
    	
    		
    	if($this->isConsolidated)
    		$title = $this->objCat->getTitle();
    	else
        	$title = $this->objAddon->getTitle();
		
        return $title;
    }
    
    
    /**
     * get addon scripts depends
     */
    protected function getScriptsDependsUC(){
    	
    	if($this->isNoMemory == true)
    		return(array());
    	
    	
    	if($this->isConsolidated == true){
    		
    		$arrHandles = array();
    		foreach($this->arrAddons as $objAddon)
    			$arrHandles = $this->ucGetAddonDepents($objAddon, $arrHandles);
    		
    	}
    	else
    		$arrHandles = $this->ucGetAddonDepents($objAddon);
    	
    	$arrHandles = array_values($arrHandles);
    	
    	return($arrHandles);
    }
    
    private function a___________ADD_CONTROLS__________(){}
    
    
    /**
     * modify value before add by type
     */
    protected function modifyValueByTypeUC($type, $value){
    	    	
    	switch($type){
    		case "image_json":
    			
    			//get json url
    			
    			$arrEmpty = array("url"=>"");
    			    			
    			$value = trim($value);

    			if(empty($value))
    				return($arrEmpty);
    			
    			$urlAssets = $this->objAddon->getUrlAssets();
    				
    			if(empty($urlAssets))
    				return($arrEmpty);
    				
    			$value = $urlAssets.$value;

    			$arrOutput = array("url"=>$value);
    			
    			return($arrOutput);
    			
    		break;
    		case UniteCreatorDialogParam::PARAM_IMAGE:
    			
    			if(empty($value))
    				$value = GlobalsUC::$url_no_image_placeholder;
    			
    			if(is_numeric($value))    				
    				$value = array("id"=>$value);
    			else
    				$value = array("url"=>$value);
    		break;
    		case UniteCreatorDialogParam::PARAM_LINK:
    			
    			$value = array("url"=>$value);
    		break;
    		case UniteCreatorDialogParam::PARAM_ICON:
    			
    			$value = UniteFontManagerUC::fa_convertIconTo5($value);
    		break;
    		case UniteCreatorDialogParam::PARAM_ICON_LIBRARY:
    			
    			$value = $this->getIconArrayValue($value);
    				    			
    		break;
    	}
    	
    	return($value);
    }
    
    
    /**
     * process data, set images as array
     */
    protected function modifyArrValuesUC($arrTypes, $arrValues){
    	
    	$arrData = array();
    	
    	    	
    	foreach($arrValues as $paramName=>$value){
    		
    		$type = UniteFunctionsUC::getVal($arrTypes, $paramName);
    		
    		if(empty($type))
    			$arrData[$paramName] = $value;
    		else
    			$arrData[$paramName] = $this->modifyValueByTypeUC($type, $value);
    		
    	}
    	
    	return($arrData);
    }
    
    /**
     * add font controls
     */
    protected function addFontControlsUC(){
    	  
            $arrFontsSections = $this->objAddon->getArrFontsParamNames();
	        $arrFontsParams = $this->objAddon->getArrFontsParams();
            
          	foreach($arrFontsSections as $name=>$title){
		         
          		$this->start_controls_section(
		                'section_styles_'.$name, array(
		                'label' => $title." ".esc_html__("Styles", "unlimited-elements-for-elementor"),
          				'tab' => "style"
		                 )
		         );
          			          	
	          	$arrParams = $arrFontsParams[$name];
	        		        	
	          	foreach($arrParams as $name => $param)
	          		$this->addElementorParamUC($param);
	          	
	          	$this->end_controls_section();
          	}
          	
    }
    
    
    /**
     * modify default items data, to make in elementor way
     */
    protected function modifyDefaultItemsDataUC($arrItemsData, $objAddon){
    	
    	$arrItemsTypes = $objAddon->getParamsTypes(true);
    	
    	foreach($arrItemsData as $key=>$arrData){    		
    		
    		$arrItemsData[$key] = $this->modifyArrValuesUC($arrItemsTypes, $arrData);
    	}
    	
    	return $arrItemsData;
    }

    
    /**
     * add items controls
     */
    protected function addItemsControlsUC($itemsType){
    	
    	if($itemsType == "listing")
    		return(false);
    	
    	if($itemsType == "image")
    		return(false);
		
    	$itemsLabel = esc_html__("Items", "unlimited-elements-for-elementor");

    	$itemsTitle = $this->objAddon->getOption("items_section_title");
    	$itemsTitle = trim($itemsTitle);

    	$itemsHeading = $this->objAddon->getOption("items_section_heading");
    	$itemsHeading = trim($itemsHeading);
    	
    	$titleField = $this->objAddon->getOption("items_title_field");
    	$titleField = trim($titleField);
    	
    	
    	if(!empty($itemsTitle))
    		$itemsLabel = $itemsTitle;
    	    	
    	 if($this->isBGWidget == false){
    	 	    	 	
	    	//add multisource condition
	    	
    	 	$arrSection = array(
                'label' => $itemsLabel
    	 	);
    	 	
    	 	
	    	if($itemsType == "multisource" && GlobalsUC::$isProVersion){
	    		
	    		$condition = array($this->listingName."_source"=>"items");
	    		$arrSection["condition"] = $condition;
	    	}
	    	
    	 		    	
	        $this->start_controls_section('section_items', $arrSection);
    	 	
    	 }
			
          //add heading label text
          if(!empty($itemsHeading)){
          	
			$this->add_control(
				'uc_item_section_heading10',
				array(
					'label' => $itemsHeading,
					'type' => Controls_Manager::HEADING,
					'separator' => 'after',
				)
			);
          	
          	
          }
         
		 $repeater = new Repeater();
         
         $paramsItems = $this->objAddon->getProcessedItemsParams();
         $paramsItems = $this->addDynamicAttributes($paramsItems);
                
	     $activeTab = null;
         
         foreach($paramsItems as $param){

         	$name = UniteFunctionsUC::getVal($param, "name");
         	$type = UniteFunctionsUC::getVal($param, "type");
         	
         	if($name == "title" && empty($titleField))
         		$titleField = "{{{ $name }}}";
         	
         	switch($type){
         		case UniteCreatorDialogParam::PARAM_TEMPLATE:	//convert template to text param
         			
			        $arrTemplates = HelperProviderCoreUC_EL::getArrElementorTemplatesShort();
					$arrTemplates = UniteFunctionsUC::addArrFirstValue($arrTemplates, __("[No Template Selected]","unlimited-elements-for-elementor"),"__none__");
					$arrTemplates = array_flip($arrTemplates);
         			
         			$param["type"] = "select2";
         			$param["name"] .= "_templateid";
         			$param["options"] = $arrTemplates;
         			$param["placeholder"] = __("Choose Template","unlimited-elements-for-elementor");
         			$param["label_block"] = true;   
         			$param["value"] = "__none__";
         			
         			$name .= "_templateid";
         		break;
         	}
         	
          	$tabName = UniteFunctionsUC::getVal($param, "tabname");
          	$tabName = trim($tabName);

          	/**
          	* end tabs, if opened and no active tab
          	*/
          	if(!empty($activeTab) && empty($tabName)){
          			$repeater->end_controls_tab();
          			$repeater->end_controls_tabs();
          			$activeTab = null;
          	}
          	
	          		//check and add tabs 
	          		
	          		if(!empty($tabName)){
	          			
	          			//if empty active tab - start tabs area
	          			if(empty($activeTab)){
          					
	          				$numTab = $this->tabsCounter++;
          					
	          				$tabsID = "uctabs{$numTab}";
	          				$tabID = "uctab{$numTab}";
	          				
	          				$repeater->start_controls_tabs($tabsID);
		          			$repeater->start_controls_tab($tabID, array("label"=>$tabName));
	          				
	          				$activeTab = $tabName;
	          				
	          			}else{
	          				
	          				//open another tab, if changed
	          				
	          				if($activeTab != $tabName){
	          					$numTab = $this->tabsCounter++;
	          					
	          					$tabID = "uctab{$numTab}";
	          					
			          			$repeater->end_controls_tab();
			          			$repeater->start_controls_tab($tabID, array("label"=>$tabName));
	          					$activeTab = $tabName;
	          				}
	          			
	          			}//else
	          			
	          			
	          		}//not emtpy tabName
          	
         	
         	$arrControl = $this->getControlArrayUC($param, true);
			
			//add control (responsive or not)
			if(isset($arrControl["uc_responsive"])){
    						
				unset($arrControl["uc_responsive"]);
				$repeater->add_responsive_control($name, $arrControl);
    						
			}else{
    						
				$repeater->add_control($name, $arrControl);
			 }
         	
         	     
	    	//add some child params
	    	$this->checkAddRelatedControls($param, $repeater);
	   
         }
		 
	       //if inside some tab, in last option - close tabs
	      if(!empty($activeTab)){
	          	
          	$repeater->end_controls_tab();
          	$repeater->end_controls_tabs();
          	$activeTab = null;
	      }
         
         
         $arrItemsControl = array();
         $arrItemsControl["type"] = Controls_Manager::REPEATER;
         $arrItemsControl["fields"] = $repeater->get_controls();
         
         if(!empty($titleField))
         	$arrItemsControl["title_field"] = $titleField;
         
         //---- set default data
         
         $arrItemsData = $this->objAddon->getArrItemsForConfig();
         
         $arrItemsData = $this->modifyDefaultItemsDataUC($arrItemsData, $this->objAddon);

         if(empty($arrItemsData))
         	$arrItemsData = array();
         
         $arrItemsControl["default"] = $arrItemsData;
		 
         $controlName = 'uc_items';
        
    	if($this->isBGWidget == true){
    		
    		$alias = $this->objAddon->getAlias();
    		$condition = array(UniteCreatorElementorIntegrate::CONTROL_BACKGROUND_TYPE=>$alias);
    		
    		$controlName = $alias."_".$controlName;
    		
    		$arrItemsControl["condition"] = $condition;
    		    		
    	}
    	
    	//add the control actually
		
    	$widgetName = $this->objAddon->getName();
    	
    	
    	if(self::DEBUG_ITEMS_CONTROLS && $this->isBGWidget == false){
    	
	    	dmp("---- debug items repeater ----");
	    	dmp($widgetName);
	    	dmp($controlName);
	    	dmp($arrItemsControl);
    	}
    	
	    	
         $this->objControls->add_control($controlName, $arrItemsControl);
          
         if($this->isBGWidget == false)
         	$this->end_controls_section();
         
    }
    
    /**
     * get icon array value
     */
    private function getIconArrayValue($value){
    	
    	if(is_array($value))
    		return($value);
    				
    	$value = UniteFontManagerUC::fa_convertIconTo5($value);
    	$library = UniteFontManagerUC::fa_getIconLibrary($value);
    			
    	$arrValue = array(
    		"library" => $library,
    		"value" => $value
    	);
    	
    	return($arrValue);
    }
    
    /**
     * get elementor condition from the param
     */
    private function getControlArrayUC_getCondition($param, $elementorCondition = null){
    	    	
    	$conditionAttribute = UniteFunctionsUC::getVal($param, "condition_attribute");
    	$conditionOperator = UniteFunctionsUC::getVal($param, "condition_operator");
    	$conditionValue = UniteFunctionsUC::getVal($param, "condition_value");
    	
    	if(empty($conditionAttribute))
    		return($elementorCondition);
    	    		
    	$arrCondition = array();
    	
	    if(!empty($elementorCondition) && is_array($elementorCondition))
	    		$arrCondition = $elementorCondition;
    	
    	if($conditionOperator == "not_equal")
    		$conditionAttribute .= "!";
    	
    	if(is_array($conditionValue) && count($conditionValue) == 1)
    		$conditionValue = $conditionValue[0];
    	
    	$arrCondition[$conditionAttribute] = $conditionValue;
    	    	
    	// add second condition
    	
    	$conditionAttribute2 = UniteFunctionsUC::getVal($param, "condition_attribute2");
    	$conditionOperator2 = UniteFunctionsUC::getVal($param, "condition_operator2");
    	$conditionValue2 = UniteFunctionsUC::getVal($param, "condition_value2");
    	
    	if(empty($conditionAttribute2))
    		return($arrCondition);
    	
    	if($conditionOperator2 == "not_equal")
    		$conditionAttribute2 .= "!";
    	
    	if(isset($arrCondition[$conditionAttribute2]))
    		return($arrCondition);

    	if(is_array($conditionValue2) && count($conditionValue2) == 1)
    		$conditionValue2 = $conditionValue2[0];
    		
    		
    	$arrCondition[$conditionAttribute2] = $conditionValue2;
    	
    	
    	return($arrCondition);
    }
    
    
    /**
     * get control array from param
     */
    protected function getControlArrayUC($param, $forItems = false){

		    	
    	$type = UniteFunctionsUC::getVal($param, "type");
    	$title = UniteFunctionsUC::getVal($param, "title");
    	$name = UniteFunctionsUC::getVal($param, "name");
    	$description = UniteFunctionsUC::getVal($param, "description");
    	$defaultValue = UniteFunctionsUC::getVal($param, "default_value");
    	$value = $defaultValue;
    	$isMultiple = UniteFunctionsUC::getVal($param, "is_multiple");
    	$elementorCondition = UniteFunctionsUC::getVal($param, "elementor_condition");
    	$labelBlock = UniteFunctionsUC::getVal($param, "label_block");
    	$placeholder = UniteFunctionsUC::getVal($param, "placeholder");
		$disabled = UniteFunctionsUC::getVal($param, "disabled");
    	
		$disabled = UniteFunctionsUC::strToBool($disabled);
		
    	$description = trim($description);
    	$placeholder = trim($placeholder);
    	
    	$enableCondition = UniteFunctionsUC::getVal($param, "enable_condition");
    	$enableCondition = UniteFunctionsUC::strToBool($enableCondition);
    	
    	//set condition
    	if($enableCondition == true){
    		$elementorCondition = $this->getControlArrayUC_getCondition($param, $elementorCondition);   
    	}
    	
    	if(isset($param["value"]))
    		$value = $param["value"];
    	
    		
    	$arrControl = array();
    	
    	//set by previous control
    	if($this->isAddSapBefore == true){
    		$arrControl["separator"] = "before";
    		$this->isAddSapBefore = false;
    	}
    	
    	switch($type){
    		case "uc_post":
    		case UniteCreatorDialogParam::PARAM_TEXTFIELD:
    			$controlType = Controls_Manager::TEXT;
    			if($disabled === true){		//show disabled input rawadd_dynamic html
					$arrControl['classes'] = "uc-elementor-control-disabled";
    			}
    				
    		break;
    		case UniteCreatorDialogParam::PARAM_COLORPICKER:
    			$controlType = Controls_Manager::COLOR;
    		break;
    		case UniteCreatorDialogParam::PARAM_NUMBER:
    			
    			$controlType = Controls_Manager::NUMBER;
    			$unit = UniteFunctionsUC::getVal($param, "unit");
    			if($unit == "other")
    				$unit = UniteFunctionsUC::getVal($param, "unit_custom");
    			
    			if(!empty($unit))
    				$title .= " ($unit)";
    			    			
    		break;
    		case UniteCreatorDialogParam::PARAM_RADIOBOOLEAN:
    			
    			$controlType = Controls_Manager::SWITCHER;
    			$trueValue = UniteFunctionsUC::getVal($param, "true_value");
    			
    			if($defaultValue == $trueValue)
    				$defaultValue = $trueValue;
    			else
    				$defaultValue = "";
				
    			$arrControl["label_on"] = UniteFunctionsUC::getVal($param, "true_name");
    			$arrControl["label_off"] = UniteFunctionsUC::getVal($param, "false_name");
    			$arrControl["return_value"] = $trueValue;
    			
    			if(empty($arrControl["label_on"])){
    				$arrControl["label_on"] = __("Yes", "unlimited-elements-for-elementor");
    				$arrControl["label_off"] = __("No", "unlimited-elements-for-elementor");
    				
    				$arrControl["return_value"] = "true";
    			}
    			
    		break;
    		case UniteCreatorDialogParam::PARAM_TEXTAREA:
    			$controlType = Controls_Manager::TEXTAREA;
    		break;
    		case UniteCreatorDialogParam::PARAM_CHECKBOX:
    			$controlType = Controls_Manager::SWITCHER;
    			$isChecked = UniteFunctionsUC::getVal($param, "is_checked");
    			$isChecked = UniteFunctionsUC::strToBool($isChecked);
    			$value = ($isChecked == true)?"yes":"no";
    			
    		break;
    		case UniteCreatorDialogParam::PARAM_DROPDOWN:
    			
    			$isSelect2 = UniteFunctionsUC::getVal($param, "select2");
    			$isSelect2 = UniteFunctionsUC::strToBool($isSelect2);
    			
    			if($isMultiple == true || $isSelect2 == true){
    				
    				$controlType = Controls_Manager::SELECT2;
    				$arrControl["label_block"] = true;
    			}
    			else
    				$controlType = Controls_Manager::SELECT;
    			   
    		break;
    		case UniteCreatorDialogParam::PARAM_MULTIPLE_SELECT:
    				$isMultiple = true;
    		case "select2":
    				$controlType = Controls_Manager::SELECT2;
    				$arrControl["label_block"] = true;
    		break;
    		case "uc_select_special":
    			
    			$controlType = "uc_select_special";
    			
    		break;
    		case UniteCreatorDialogParam::PARAM_EDITOR:
    			$controlType = Controls_Manager::WYSIWYG;
    		break;
    		case UniteCreatorDialogParam::PARAM_ICON:
    			$controlType = Controls_Manager::ICON;
    		break;
    		case UniteCreatorDialogParam::PARAM_ICON_LIBRARY:
    			$controlType = Controls_Manager::ICONS;
    		break;
    		case UniteCreatorDialogParam::PARAM_IMAGE:
    			$controlType = Controls_Manager::MEDIA;
    		break;
    		case UniteCreatorDialogParam::PARAM_HR:
    			//$controlType = "uc_hr";
    			$controlType = Controls_Manager::DIVIDER;
    		break;
    		case UniteCreatorDialogParam::PARAM_HEADING:
    			$controlType = Controls_Manager::HEADING;
    		break;
    		case UniteCreatorDialogParam::PARAM_AUDIO:
    			$controlType = "uc_mp3";
    		break;
    		case "uc_gallery":
    			$controlType = Controls_Manager::GALLERY;
    		break;
    		case UniteCreatorDialogParam::PARAM_LINK:
    			$controlType = Controls_Manager::URL;
    		break;
    		case UniteCreatorDialogParam::PARAM_SHAPE:
    			
    			$controlType = "uc_shape_picker";
    			//$controlType = Controls_Manager::ICON;
    		break;
    		case UniteCreatorDialogParam::PARAM_HIDDEN:
    			
    			$controlType = Controls_Manager::HIDDEN;
    		break;
    		case UniteCreatorDialogParam::PARAM_STATIC_TEXT:
    			$controlType = Controls_Manager::HEADING;
    		break;
    		case UniteCreatorDialogParam::PARAM_MARGINS:
    		case UniteCreatorDialogParam::PARAM_PADDING:
    		case UniteCreatorDialogParam::PARAM_BORDER_DIMENTIONS:
    			$controlType = Controls_Manager::DIMENSIONS;
    		break;
    		case UniteCreatorDialogParam::PARAM_BACKGROUND:    			
    			$controlType = Group_Control_Background::get_type();
    		break;    		
    		case UniteCreatorDialogParam::PARAM_SLIDER:
    			$controlType = Controls_Manager::SLIDER;
    		break;
    		case UniteCreatorDialogParam::PARAM_BORDER:
    			$controlType = Group_Control_Border::get_type();
    		break;
    		case UniteCreatorDialogParam::PARAM_DATETIME:
    			$controlType = Controls_Manager::DATE_TIME;
    		break;
    		case UniteCreatorDialogParam::PARAM_TEXTSHADOW:
    			$controlType = Group_Control_Text_Shadow::get_type();
    		break;
    		case UniteCreatorDialogParam::PARAM_BOXSHADOW:
    			$controlType = Group_Control_Box_Shadow::get_type();
    		break;
    		case UniteCreatorDialogParam::PARAM_CSS_FILTERS:
    			$controlType = Group_Control_Css_Filter::get_type();
    		break;
    		case UniteCreatorDialogParam::PARAM_HOVER_ANIMATIONS:
    			$controlType = Controls_Manager::SELECT;
    		break;
    		case UniteCreatorDialogParam::PARAM_SPECIAL:
    			
    			$controlType = Controls_Manager::TEXT;
    			 
    		break;
    		case UniteCreatorDialogParam::PARAM_POST_SELECT:
    			
    			$controlType = "uc_select_special";
    			
    		break;
    		case UniteCreatorDialogParam::PARAM_TERM_SELECT:
    			$controlType = "uc_select_special";
    		break;
    		case UniteCreatorDialogParam::PARAM_RAW_HTML:
    			$controlType = "raw_html";
    			$arrControl["label_block"] = true;
    		break;
    		case UniteCreatorDialogParam::PARAM_REPEATER:
    			$controlType = Controls_Manager::REPEATER;
    			    			
    		break;
    		default:
    			
    			$addonTitle = $this->objAddon->getTitle();
    			
    			dmp("param not found in widget: $addonTitle");
    			dmp($param);
    			UniteFunctionsUC::showTrace();
    			UniteFunctionsUC::throwError("Wrong param type: ".$type);
    		break;
    	}
    	
    	//------- add special params ---------
    	
    	$value = $this->modifyValueByTypeUC($type, $value);
    	
    	if(empty($controlType)){
    		dmp("empty control param type");
    		dmp($param);
    		exit();
    	}
    	
    	$arrControl["type"] = $controlType;
    	$arrControl["label"] = $title;
    	
    	$arrControl["default"] = $value;
    	
    	//add options
    	switch($type){
    		case UniteCreatorDialogParam::PARAM_RAW_HTML:
    		
    			$html = UniteFunctionsUC::getVal($param, "html");
    			
    			$arrControl["show_label"] = false;
    			$arrControl["raw"] = $html;
    		break;
    		case UniteCreatorDialogParam::PARAM_HEADING:
    			
    			$arrControl["label"] = $defaultValue;
    			unset($arrControl["default"]);
    			
    		break;
    		case UniteCreatorDialogParam::PARAM_IMAGE:
				
    			$mediaType = UniteFunctionsUC::getVal($param, "media_type");
    			
    			if($mediaType == "json"){
    				
    				$defaultValue = UniteFunctionsUC::getVal($param, "default_value_json");
    				$defaultValue = $this->modifyValueByTypeUC("image_json", $defaultValue);
				
    				$arrControl['media_type'] = 'application/json';
    				$arrControl['default'] = $defaultValue;
    			}
    			
    		break;
    		case UniteCreatorDialogParam::PARAM_POST_SELECT:
    			
    			$placeholder = "All--Posts";
    			
				$loaderText = __("Loading Data...", "unlimited-elements-for-elementor");
				$loaderText = UniteFunctionsUC::encodeContent($loaderText);
    			
    			$addParams = "class=unite-setting-special-select data-settingtype=post_ids data-loadertext={$loaderText} data-placeholdertext={$placeholder} data-issingle=true";
    			
				$arrControl["addparams"] = $addParams;
				$arrControl["label_block"] = true;
    			
    		break;
    		case UniteCreatorDialogParam::PARAM_TERM_SELECT:
    			
    			$placeholder = "All--Terms";
				
    			$loaderText = __("Loading Data...", "unlimited-elements-for-elementor");
				$loaderText = UniteFunctionsUC::encodeContent($loaderText);
    			
    			$addParams = "class=unite-setting-special-select data-settingtype=post_ids data-loadertext={$loaderText} data-placeholdertext={$placeholder} data-issingle=true data-datatype=terms";
    			
				$arrControl["addparams"] = $addParams;
				$arrControl["label_block"] = true;
    			
    		break;
    		case "uc_select_special":
    			   
    			$addParams = UniteFunctionsUC::getVal($param, "addparams");
    			$classAdd = UniteFunctionsUC::getVal($param, "classAdd");
				
    			if(!empty($classAdd))
    				$addParams .= " class={$classAdd}";

    			$addParams = str_replace("'", "", $addParams);
    			
				$arrControl["addparams"] = $addParams;
				
    		case "select2":
			case UniteCreatorDialogParam::PARAM_MULTIPLE_SELECT:
    		case UniteCreatorDialogParam::PARAM_DROPDOWN:
    		    
    			$options = UniteFunctionsUC::getVal($param, "options", array());
    			
    			$options = array_flip($options);
    			$arrControl["options"] = $options;
    			
    			if($isMultiple == true){
    				$arrControl["multiple"] = true;
    				
    				if(is_array($value) == false){
    					if(empty($value))
    						$arrControl["default"] = array();
    					else
    						$arrControl["default"] = array($value);
    				}
    					
    			}

    			
    			$isResponsive = UniteFunctionsUC::getVal($param, "is_responsive");
    			
    			if($isResponsive == true){
    				
    				$defaultValueTablet = UniteFunctionsUC::getVal($param, "default_value_tablet");
    				$defaultValueMobile = UniteFunctionsUC::getVal($param, "default_value_mobile");
					
    				$defaultValueDesktop = UniteFunctionsUC::getVal($arrControl, "default");
    				    				
    				$arrControl["uc_responsive"] = true;
    				
    				$arrControl["desktop_default"] = $defaultValueDesktop;
    				$arrControl["tablet_default"] = $defaultValueTablet;
    				$arrControl["mobile_default"] = $defaultValueMobile;
    			}
    			
    			
    		break;
    		case UniteCreatorDialogParam::PARAM_PADDING:
    		case UniteCreatorDialogParam::PARAM_MARGINS:
    		case UniteCreatorDialogParam::PARAM_BORDER_DIMENTIONS:
    			
    			$arrControl["size_units"] = array("px","%","em","rem");
    			
    			$addUnits = UniteFunctionsUC::getVal($param, "add_units");
				
    			if(!empty($addUnits)){
    			
    				$arrAddUnits = explode(",", $addUnits);
    				
    				$arrControl["size_units"] = array_merge($arrControl["size_units"], $arrAddUnits);    				
    			}
    			
    			$isResponsive = UniteFunctionsUC::getVal($param, "is_responsive");
    			$isResponsive = UniteFunctionsUC::strToBool($isResponsive);
    			
    			//set default value
    			$arrDefaultValue = array();
    			$arrDefaultValue["top"] = UniteFunctionsUC::getVal($param, "desktop_top");
    			$arrDefaultValue["bottom"] = UniteFunctionsUC::getVal($param, "desktop_bottom");
    			$arrDefaultValue["left"] = UniteFunctionsUC::getVal($param, "desktop_left");
    			$arrDefaultValue["right"] = UniteFunctionsUC::getVal($param, "desktop_right");
    			
    			
    			$unit = UniteFunctionsUC::getVal($param, "units");
    			if(!empty($unit))
    				$arrDefaultValue["unit"] = $unit;
    			
    			
    			if($isResponsive == true){
    				
    				$arrTabletDefaults = array();
    				$arrTabletDefaults["top"] = UniteFunctionsUC::getVal($param, "tablet_top");
    				$arrTabletDefaults["bottom"] = UniteFunctionsUC::getVal($param, "tablet_bottom");
    				$arrTabletDefaults["left"] = UniteFunctionsUC::getVal($param, "tablet_left");
    				$arrTabletDefaults["right"] = UniteFunctionsUC::getVal($param, "tablet_right");
    				
    				$arrMobileDefaults = array();
    				$arrMobileDefaults["top"] = UniteFunctionsUC::getVal($param, "mobile_top");
    				$arrMobileDefaults["bottom"] = UniteFunctionsUC::getVal($param, "mobile_bottom");
    				$arrMobileDefaults["left"] = UniteFunctionsUC::getVal($param, "mobile_left");
    				$arrMobileDefaults["right"] = UniteFunctionsUC::getVal($param, "mobile_right");
    				
    				$arrControl["uc_responsive"] = true;
    				$arrControl["default"] = $arrDefaultValue;
    				$arrControl["desktop_default"] = $arrDefaultValue;
    				$arrControl["tablet_default"] = $arrTabletDefaults;
    				$arrControl["mobile_default"] = $arrMobileDefaults;
    				
    			}
    			else{
    				$arrControl["default"] = $arrDefaultValue;
    			}
    			
    			
    			//set selector
    			$arrSelectors = array();
    			$selector = UniteFunctionsUC::getVal($param, "selector");
    			
    			$attribute = "margin";
    			switch($type){
    				case UniteCreatorDialogParam::PARAM_PADDING:
    					$attribute = "padding";
    				break;
    				case UniteCreatorDialogParam::PARAM_BORDER_DIMENTIONS:
    					$attribute = "border-radius";
    				break;
    			}
    			
    			if(!empty($selector)){
    				$selector = $this->addWrapperToSelector($selector);
    				$selectorContent = $attribute.': {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};';
    				
    				$arrSelectors[$selector] = $selectorContent;
    			}
    			
    			if(!empty($arrSelectors))
    				$arrControl["selectors"] = $arrSelectors;
				
    		break;
    		case UniteCreatorDialogParam::PARAM_BORDER:
    			
    			$arrControl["name"] = $name;
    			
    			unset($arrControl["default"]);
    			
    			$arrDefaults = array();
    			
				$arrDefaults["border"] = array(
					"label" => $title." ".__("Type","unlimited-elements-for-elementor")
				);
    			
    			$borderStyle = UniteFunctionsUC::getVal($param, "border_type");
    			if(!empty($borderStyle) && $borderStyle != "none"){
    				$arrDefaults["border"]["default"] = $borderStyle;
    			}
    			
    			$borderColor = UniteFunctionsUC::getVal($param, "border_color");
    			if(!empty($borderColor))
    				$arrDefaults["color"] = array(
					"label" => $title." ".__("Color","unlimited-elements-for-elementor"),
    				"default" => $borderColor
    			);
				
    			
    			$arrWidthDesktop = array();
    			$arrWidthDesktop["top"] = UniteFunctionsUC::getVal($param, "width_desktop_top");
    			$arrWidthDesktop["bottom"] = UniteFunctionsUC::getVal($param, "width_desktop_bottom");
    			$arrWidthDesktop["left"] = UniteFunctionsUC::getVal($param, "width_desktop_left");
    			$arrWidthDesktop["right"] = UniteFunctionsUC::getVal($param, "width_desktop_right");

    			$arrWidthTablet = array();
    			$arrWidthTablet["top"] = UniteFunctionsUC::getVal($param, "width_tablet_top");
    			$arrWidthTablet["bottom"] = UniteFunctionsUC::getVal($param, "width_tablet_bottom");
    			$arrWidthTablet["left"] = UniteFunctionsUC::getVal($param, "width_tablet_left");
    			$arrWidthTablet["right"] = UniteFunctionsUC::getVal($param, "width_tablet_right");
    			
    			$arrWidthMobile = array();
    			$arrWidthMobile["top"] = UniteFunctionsUC::getVal($param, "width_mobile_top");
    			$arrWidthMobile["bottom"] = UniteFunctionsUC::getVal($param, "width_mobile_bottom");
    			$arrWidthMobile["left"] = UniteFunctionsUC::getVal($param, "width_mobile_left");
    			$arrWidthMobile["right"] = UniteFunctionsUC::getVal($param, "width_mobile_right");
    			
    			$arrDefaults["width"] = array(
    				"label"=>$title." ".__("Width","unlimited-elements-for-elementor"),
    				"desktop_default"=>$arrWidthDesktop,
    				"tablet_default"=>$arrWidthTablet,
    				"mobile_default"=>$arrWidthMobile,
    			);
    			
    			if(!empty($arrDefaults))
    				$arrControl["fields_options"] = $arrDefaults;
    			
    			//add always before sap
    			$arrControl["separator"] = "before";
    			$this->isAddSapBefore = true;
    			
    		break;
    		case UniteCreatorDialogParam::PARAM_BACKGROUND:
    			
    			unset($arrControl["default"]);
    			
    			$arrControl["name"] = $name;
    			$arrControl["types"] = array('classic', 'gradient');
    			
    			if(!empty($selector))
    				$arrControl["selector"] = $selector;
    			
    			//set defaults
    			$arrDefaults = array();
    			
    			$bgType = UniteFunctionsUC::getVal($param, "background_type");
    			
    			switch($bgType){
    				
    				case "solid":
    					
    					$color = UniteFunctionsUC::getVal($param, "solid_color");
    					$image = UniteFunctionsUC::getVal($param, "solid_bg_image");
    					$image = $this->objAddon->convertFromUrlAssets($image);

    					if(!empty($color) || !empty($image)){    						
    						$arrDefaults["background"] = array("default"=>"classic");
    					}
    					
    					if(!empty($color)){
	    					$arrDefaults["color"] = array("default"=>$color);
    					}
    					
    					if(!empty($image)){
	    					$arrDefaults["image"] = array("default"=>array("url"=>$image));

	    					$imageBGPosition = UniteFunctionsUC::getVal($param, "solid_bg_image_position");
	    					$imageBGRepeat = UniteFunctionsUC::getVal($param, "solid_bg_image_repeat");
	    					$imageBGSize = UniteFunctionsUC::getVal($param, "solid_bg_image_size");
	    					
	    					if($imageBGPosition)
	    						$arrDefaults["position"] = array("default"=>$imageBGPosition);
	    					
	    					if($imageBGRepeat)
	    						$arrDefaults["repeat"] = array("default"=>$imageBGRepeat);
	    					
	    					if($imageBGSize)
	    						$arrDefaults["size"] = array("default"=>$imageBGSize);
	    					
    					}
    					
    				break;
    				case "gradient":
    					
    					$color1 = UniteFunctionsUC::getVal($param, "gradient_color1");
    					$color2 = UniteFunctionsUC::getVal($param, "gradient_color2");
    					
    					if(!empty($color1) && !empty($color2)){
	    					
    						$arrDefaults["background"] = array("default"=>"gradient");
	    					$arrDefaults["color"] = array("default"=>$color1);
	    					$arrDefaults["color_b"] = array("default"=>$color2);
    					}
    					   					
    				break;
    			}

    			if(!isset($arrDefaults["background"]))
    				$arrDefaults["background"] = array();
    			
    			//default label
    			$arrDefaults["background"]["label"] = $title;
    			
    			if(!isset($arrDefaults["color"]))
    				$arrDefaults["color"] = array();
    			
    			if(!isset($arrDefaults["image"]))
    				$arrDefaults["image"] = array();
    			
    			$arrDefaults["color"]["label"] = $title. " ".__("Color", "unlimited-elements-of-elementor");
    			$arrDefaults["image"]["label"] = $title." ".__("Image", "unlimited-elements-of-elementor");
    			
    			if(!empty($arrDefaults)){
    				
    				$arrControl["fields_options"] = $arrDefaults;
    			}
    			
    			$arrControl["separator"] = "before";
    			$this->isAddSapBefore = true;
    			
    		break;
    		case UniteCreatorDialogParam::PARAM_SLIDER:
    			
    			$units = UniteFunctionsUC::getVal($param, "units");
				
    			
    			$rangeUnit = "px";
    			
    			switch($units){
    				case "px":
    					$arrControl["size_units"] = array("px");
    					$rangeUnit = "px";
    				break;
    				case "%":
    					$arrControl["size_units"] = array("%");
    					$rangeUnit = "%";
    				break;
    				case "em":
    					$arrControl["size_units"] = array("em");
    					$rangeUnit = "em";
    				break;
    				case "px_percent":
    					$arrControl["size_units"] = array("px","%");
    					$rangeUnit = "px";
    				break;
    				case "percent_px":
    					$arrControl["size_units"] = array("%","px");
    					$rangeUnit = "%";
    				break;
    				case "vh":
    					$arrControl["size_units"] = array("vh");
    					$rangeUnit = "vh";
    				break;
    				case "vh_px":
    					$arrControl["size_units"] = array("vh","px","re,");
    					$rangeUnit = "vh";
    				break;
    				case "px_vh":
    					$arrControl["size_units"] = array("px","vh","rem");
    					$rangeUnit = "px";
    				break;
    				case "px_vh_percent":
    					$arrControl["size_units"] = array("px","vh","%");
    					$rangeUnit = "px";
    				break;
    				case "nothing":
    					$arrControl["size_units"] = array();
    					$rangeUnit = "";
    				break;
    				case "vw":
    					$arrControl["size_units"] = array("vw");
    					$rangeUnit = "vw";
    				break;
    				case "px_vw":
    					$arrControl["size_units"] = array("px","vw");
    					$rangeUnit = "px";
    				break;
    				case "vw_px":
    					$arrControl["size_units"] = array("vw","px");
    					$rangeUnit = "vw";
    				break;
    				case "px_vw_percent":
    					$arrControl["size_units"] = array("px","vw","%");
    					$rangeUnit = "px";
    				break;
    				case "px_percent_em":
    				default:
    					$arrControl["size_units"] = array("px","%","em");
    					$rangeUnit = "px";
					break;
    				
    			}
    			
    			//set range
    			$arrRangeUnit = array(
    				"min"=>(int)UniteFunctionsUC::getVal($param, "min"),
    				"max"=>(int)UniteFunctionsUC::getVal($param, "max"),
    				"step"=>(int)UniteFunctionsUC::getVal($param, "step")
    			);
    			
    			$arrRange = array();
    			
	    		$arrUnits = $arrControl["size_units"];
				
	    		if(empty($arrUnits))
	    			$arrUnits = array();
	    		
    			$numUnits = count($arrUnits);
    			
    			if($numUnits == 0){
    				$arrRange = $arrRangeUnit;
    			}
    			if($numUnits == 1)
    				$arrRange[$rangeUnit] = $arrRangeUnit;
    			else{

	    			//for multiple units - handle percent or others
	    			    			    				
	    				foreach($arrUnits as $unit){
	    					
	    					switch($unit){
		    					case "vh":
	    							$arrRange[$unit] = array("min"=>0,"max"=>200,"step"=>1);
		    					break;
		    					case "%":
		    					case "vw":
	    							$arrRange[$unit] = array("min"=>0,"max"=>100,"step"=>1);
		    					break;
		    					case "px":
    								$arrRange[$unit] = $arrRangeUnit;
		    					break;
	    					}
	    				}
	    				
    			}
    				
    			
    			$arrControl["range"] = $arrRange;
    			
    			$arrControl["default"] = array(
    				"size" => UniteFunctionsUC::getVal($param, "default_value"),
    				"unit" => $rangeUnit
    			);
    			
    			
    			$isResponsive = UniteFunctionsUC::getVal($param, "is_responsive");
    			$isResponsive = UniteFunctionsUC::strToBool($isResponsive);
    			
    			
    			if($isResponsive == true){
    				
    				$arrControl["uc_responsive"] = true;
    				
    				$defaultValueDesktop = UniteFunctionsUC::getVal($param, "default_value");
    				
    				$defaultValueTablet = UniteFunctionsUC::getVal($param, "default_value_tablet");
    				$defaultValueMobile = UniteFunctionsUC::getVal($param, "default_value_mobile");
    				    				
    				$unitTablet = $rangeUnit;
    				
    				if(!empty($defaultValueTablet)){
    					$arrSize = UniteFunctionsUC::getSizeAndUnit($defaultValueTablet, $rangeUnit);
    					$defaultValueTablet = $arrSize["size"];
    					$unitTablet = $arrSize["unit"];
    				}

    				    				    				
    				$unitMobile = $rangeUnit;
    				
    				if(!empty($defaultValueMobile)){
    					$arrSize = UniteFunctionsUC::getSizeAndUnit($defaultValueMobile, $rangeUnit);
    					$defaultValueMobile = $arrSize["size"];
    					$unitMobile = $arrSize["unit"];
    				}
    				
    				if(!empty($defaultValueTablet)){
		    			$arrControl["tablet_default"] = array(
		    				"size" => $defaultValueTablet,
		    				"unit" => $rangeUnit
		    			);
    				}
	    			
    				if(!empty($defaultValueMobile)){
		    			$arrControl["mobile_default"] = array(
		    				"size" => $defaultValueMobile,
		    				"unit" => $unitMobile
		    			);
    				}
	    			
    			}
				
	    			
    		break;
    		case UniteCreatorDialogParam::PARAM_NUMBER:
    			
    			//add responsive controls
    			
    			$isResponsive = UniteFunctionsUC::getVal($param, "is_responsive");
    			$isResponsive = UniteFunctionsUC::strToBool($isResponsive);
    			
    			if($isResponsive == true){
    				$arrControl["uc_responsive"] = true;
    				
    				$defaultTablet = UniteFunctionsUC::getVal($param, "default_value_tablet",$defaultValue);
    				$defaultMobile = UniteFunctionsUC::getVal($param, "default_value_mobile",$defaultValue);
     				
	    			$arrControl["desktop_default"] = $defaultValue;
	    			$arrControl["tablet_default"] = $defaultTablet;
	    			$arrControl["mobile_default"] = $defaultMobile;	    			
    			}
    			
    			//add min max step
    			$min = UniteFunctionsUC::getVal($param, "min_value");
    			$max = UniteFunctionsUC::getVal($param, "max_value");
    			$step = UniteFunctionsUC::getVal($param, "step");
    			
    			if(!empty($min))
    				$arrControl["min"] = $min;
    			
    			if(!empty($max))
    				$arrControl["max"] = $max;
    			
    			if(!empty($step))
    				$arrControl["step"] = $step;
    			
    				
    		break;
    		case UniteCreatorDialogParam::PARAM_TEXTSHADOW:
    			
    			$arrControl["name"] = $name;
					
				$arrDefaults["text_shadow_type"] = array(
					'label' => $title
				);
				
				$arrControl["fields_options"] = $arrDefaults;
				
    		break;
    		case UniteCreatorDialogParam::PARAM_BOXSHADOW:
    			$arrControl["name"] = $name;
					
				$arrDefaults["box_shadow_type"] = array(
					'label' => $title
				);
				
				$arrControl["fields_options"] = $arrDefaults;
    			
    		break;
    		case UniteCreatorDialogParam::PARAM_CSS_FILTERS:
    			$arrControl["name"] = $name;
    		break;
    		case UniteCreatorDialogParam::PARAM_HOVER_ANIMATIONS:
    			
    			$arrControl["name"] = $name;
    			
    			$options = HelperProviderCoreUC_EL::getHoverAnimationClasses(true);
    			
    			$arrControl["options"] = $options;
    			
    			$arrControl["default"] = UniteFunctionsUC::getVal($param, "default_value");
    			    			
    		break;
    		case UniteCreatorDialogParam::PARAM_ICON_LIBRARY:
    			
    			$enableSVG = UniteFunctionsUC::getVal($param, "enable_svg");
    			$enableSVG = UniteFunctionsUC::strToBool($enableSVG);
    			
    			if($enableSVG == false){
	    			$arrControl["skin"] = "inline";
	    			$arrControl["exclude_inline_options"] = array("svg");
    			}
    			
    			$arrControl["default"] = $this->getIconArrayValue($value);
    			
    		break;
    		case UniteCreatorDialogParam::PARAM_DATETIME:
    			
    			$mode = UniteFunctionsUC::getVal($param, "date_time_mode");
    			    			
    			$pickerOptions = array();
    			
    			switch($mode){
    				case "time":
    					$pickerOptions["enableTime"] = true;
    					$pickerOptions["noCalendar"] = true;
    					$pickerOptions["dateFormat"] = "H:i";
    					$pickerOptions["time_24hr"] = true;
    				break;
    				case "date_time":
    					$pickerOptions["enableTime"] = true;
    					$pickerOptions["dateFormat"] = "Y-m-d H:i";
    					$pickerOptions["time_24hr"] = true;
    				break;
    				case "date":
    				default:
    					$pickerOptions["dateFormat"] = "Y-m-d";
    					$pickerOptions["enableTime"] = false;
    				break;
    				
    			}
    			
    			
    			$arrControl["ue_date_mode"] = $mode;
    			
    			$arrControl["picker_options"] = $pickerOptions;

    			$arrControl["default"] = UniteFunctionsUC::getVal($param, "default");
    			
    			//if(!empty($mode) && $mode == "time"){dmp($arrControl);exit();}
    			
    		break;
    		case UniteCreatorDialogParam::PARAM_GALLERY:
    			   
    			if(empty($defaultValue)){
					
					$arrDefaults = $this->getGalleryParamDefaultItems();
										
    				$arrControl["default"] = $arrDefaults;
    			}
    			
    			$param["add_dynamic"] = true;
    			
    		break;
    		case UniteCreatorDialogParam::PARAM_REPEATER:
    			
    			//prepeare the repeater items and default values
    			
    			$repeater = new Repeater();
    			
    			$settingsItems = UniteFunctionsUC::getVal($param, "settings_items");
    			UniteFunctionsUC::validateNotEmpty($settingsItems,"settings_items");
    			
    			$hideLabel = UniteFunctionsUC::getVal($param, "hide_label");
    			$hideLabel = UniteFunctionsUC::strToBool($hideLabel);
    			
    			if($hideLabel == true)
    				$arrControl["show_label"] = false;
    			
    			$titleField = UniteFunctionsUC::getVal($param, "title_field");
    				
    			if(!empty($titleField))
    				$arrControl["title_field"] = $titleField;
    			
    			$arrParamsItems = $settingsItems->getSettingsCreatorFormat();
    			
    			foreach($arrParamsItems as $itemParam)
    				$this->addElementorParamUC($itemParam, $repeater);
    			
    			$arrItemValues = UniteFunctionsUC::getVal($param, "items_values");
    			
    			if(empty($arrItemValues))
    				$arrItemValues = array();
    			
 				$arrControl["fields"] = $repeater->get_controls();
    			
         		$arrControl["default"] = $arrItemValues;
         		
         		$arrControl["prevent_empty"] = false;
         		
    		break;
    	}
    	
    	//---- add selectors --- 
    	
    	switch($type){		//single selector
    		case UniteCreatorDialogParam::PARAM_AUDIO:
    		case UniteCreatorDialogParam::PARAM_TEXTSHADOW:
    		case UniteCreatorDialogParam::PARAM_BOXSHADOW:
    		case UniteCreatorDialogParam::PARAM_BORDER:
    		case UniteCreatorDialogParam::PARAM_BACKGROUND:
    		case UniteCreatorDialogParam::PARAM_CSS_FILTERS:
    			
    			$selector = UniteFunctionsUC::getVal($param, "selector");
    			if(!empty($selector)){
    				$selector = $this->addWrapperToSelector($selector);
    				
    				$arrControl["selector"] = $selector;    				
    			}
    			
    		break;		//name value selectors
    		case UniteCreatorDialogParam::PARAM_COLORPICKER:    			
    		case UniteCreatorDialogParam::PARAM_NUMBER:
    		case UniteCreatorDialogParam::PARAM_SLIDER:
    		case UniteCreatorDialogParam::PARAM_DROPDOWN:
    			
    			$selector = UniteFunctionsUC::getVal($param, "selector");
    			$selectorValue = UniteFunctionsUC::getVal($param, "selector_value");
    			
    			if(!empty($selector)){
    				$selector = $this->addWrapperToSelector($selector);
    				$arrControl["selectors"][$selector] = $selectorValue;
    			}
				
    			$selector2 = UniteFunctionsUC::getVal($param, "selector2");
    			$selector2Value = UniteFunctionsUC::getVal($param, "selector2_value");
    			
    			if(!empty($selector2)){
    				$selector2 = $this->addWrapperToSelector($selector2);
    				$arrControl["selectors"][$selector2] = $selector2Value;
    			}
    			
    			$selector3 = UniteFunctionsUC::getVal($param, "selector3");
    			$selector3Value = UniteFunctionsUC::getVal($param, "selector3_value");
    			
    			if(!empty($selector3)){
    				$selector3 = $this->addWrapperToSelector($selector3);
    				$arrControl["selectors"][$selector3] = $selector3Value;
    			}
    			
    		break;
    	}
    	
    	if($forItems == true)
    		$arrControl["name"] = $name;
    	
    	//add description
    	if(!empty($description))
    		$arrControl["description"] = $description;
    	
    	if(!empty($placeholder))
    		$arrControl["placeholder"] = $placeholder;
    	    	
    		
    	//add dynamic
    	$isAddDynamic = UniteFunctionsUC::getVal($param, "add_dynamic");
    	$isAddDynamic = UniteFunctionsUC::strToBool($isAddDynamic);
    	
    	$disableDynamic = UniteFunctionsUC::getVal($param, "disable_dynamic");
    	$disableDynamic = UniteFunctionsUC::strToBool($disableDynamic);
    	
    	if($disableDynamic === true)
    		$isAddDynamic = false;
    	
    	if($isAddDynamic == true){
    		
    		$arrControl['dynamic'] = array(
				'active' => true
    		);
			
			$arrControl["recursive"] = true;
    	}
    	
    	//condition
    	if(!empty($elementorCondition)){
    		$arrControl["condition"] = $elementorCondition;
    		
    	}
    	
    	//label block
    	if($labelBlock === true)
    		$arrControl["label_block"] = true;

    	/*
    	if($name == "another"){//dmp($arrControl);exit();}
    	*/
    
    	return($arrControl);
    }

    
    /**
     * add wrapper to selector
     */
    protected function addWrapperToSelector($selector){
    	
    	if(is_string($selector) == false)
    		return(false);
    	
    	if(empty($selector))
    		return(false);
    	
    	$selector = trim($selector);
    	$selector = "{{WRAPPER}} $selector";
    	
    	//handle the commas
    	if(strpos($selector, ",") === false)
    		return($selector);
    	
    	$selector = str_replace(",", ",{{WRAPPER}} ", $selector);
    	$selector = str_replace("  ", " ", $selector);
    	
    	return($selector);
    }
    
    
    /**
     * add typography control by param
     */
    protected function addTypographyByParamUC($param){
    	
    	$name = UniteFunctionsUC::getVal($param, "name");
    	$title = UniteFunctionsUC::getVal($param, "title");
    	
    	$elementorCondition = UniteFunctionsUC::getVal($param, "elementor_condition");
    	$enableCondition = UniteFunctionsUC::getVal($param, "enable_condition");
    	$enableCondition = UniteFunctionsUC::strToBool($enableCondition);
		
    	//set condition
    	if($enableCondition == true){
    		$elementorCondition = $this->getControlArrayUC_getCondition($param, $elementorCondition);    		
    	}
    	
    	
    	//get selectors
    	$selector1 = UniteFunctionsUC::getVal($param, "selector1");
    	$selector1 = trim($selector1);

    	$selector2 = UniteFunctionsUC::getVal($param, "selector2");
    	$selector2 = trim($selector2);
    	
    	$selector3 = UniteFunctionsUC::getVal($param, "selector3");
    	$selector3 = trim($selector3);
    	
    	//make selector string
    	
    	$selector = "";
    	
    	if(!empty($selector1))
    		$selector .= "{{WRAPPER}} $selector1";
    	
    		
    	if(!empty($selector2)){
    		
    		if(!empty($selector))
    			$selector .= ",";
    		
    		$selector .= "{{WRAPPER}} $selector2";
    	}
    	
    	if(!empty($selector3)){
    		
    		if(!empty($selector))
    			$selector .= ",";
    		
    		$selector .= "{{WRAPPER}} $selector3";
    	}
    	
    	//add the typography control
    	$controlName = HelperUC::convertTitleToHandle($name);
    	
    	$controlName = $name;
    	$controlName = str_replace(".", "_", $controlName);
    	
    	$arrControl = array();
    	$arrControl["name"] = $controlName;
    	$arrControl["selector"] = $selector;
    	$arrControl["scheme"] = 3;
    	
    	if(!empty($title))
    		$arrControl["label"] = $title;
    	
    	if(!empty($elementorCondition)){
    		$arrControl["condition"] = $elementorCondition;
    	}
    	
    	
    	$this->objControls->add_group_control(Group_Control_Typography::get_type(), $arrControl);
    	
    }
    
    
    /**
     * add elementor param
     */
    protected function addElementorParamUC($param, $objControls = null){
    	  
    	if(empty($objControls))
    		$objControls = $this->objControls;
    	
    	$name = UniteFunctionsUC::getVal($param, "name");
    	$type = UniteFunctionsUC::getVal($param, "type");
    	
    	
    	switch($type){
    		case "custom_controls":		//add controls by custom function
    			
    			$function = UniteFunctionsUC::getVal($param, "function");
    			if(empty($function))
    				UniteFunctionsUC::throwError("No function given for 'custom_controls' setting");
    			
    			unset($param["function"]);
    				
				call_user_func($function, $this->objControls, $param);
    			
    		break;
    		case UniteCreatorDialogParam::PARAM_INSTAGRAM:
    		case UniteCreatorDialogParam::PARAM_POST_TERMS:
    		case UniteCreatorDialogParam::PARAM_WOO_CATS:
    		case UniteCreatorDialogParam::PARAM_USERS:
    		case UniteCreatorDialogParam::PARAM_TEMPLATE:
    		case UniteCreatorDialogParam::PARAM_MENU:
    		case UniteCreatorDialogParam::PARAM_LISTING:
    		case UniteCreatorDialogParam::PARAM_SPECIAL:
    			
    			$settings = new UniteCreatorSettings();
    			
    			$arrChildParams = $settings->getMultipleCreatorParams($param);
    			
    			foreach($arrChildParams as $childParam){
    				
    				//add condition to child params
    				
    				if($type == UniteCreatorDialogParam::PARAM_TEMPLATE){
    					
    					foreach($param as $paramKey => $paramValue ){
    						if(strpos($paramKey, "condition") !== false)
    							$childParam[$paramKey] = $paramValue;
    					}
    					
    				}
    				
    				$this->addElementorParamUC($childParam,$objControls);
    			}
    			
    		break;
    		case UniteCreatorDialogParam::PARAM_POSTS_LIST:
    			
    			$param["all_cats_mode"] = true;
    			
    			//add current posts settings    			   
    			$param["add_current_posts"] = true;
    			
    			$settings = new UniteCreatorSettings();
    			    			
    			$arrChildParams = $settings->getMultipleCreatorParams($param);
    			
    			foreach($arrChildParams as $childParam)
    				$this->addElementorParamUC($childParam, $objControls);
    			
    		break;
    		case UniteCreatorDialogParam::PARAM_TYPOGRAPHY:
    			$this->addTypographyByParamUC($param);
    		break;
    		case UniteCreatorDialogParam::PARAM_FONT_OVERRIDE:
    		break;
    		default:
    			
    			//add regular control
    			
    			$arrControl = $this->getControlArrayUC($param);
    			
    			$type = UniteFunctionsUC::getVal($param, "type");

				if(self::DEBUG_CONTROLS && $this->isBGWidget == false){
				
					dmp("--- debug control ---");
					dmp($type);
					dmp($name);
					dmp($arrControl);
				}
    			
    			
    			switch($type){
    				case UniteCreatorDialogParam::PARAM_BACKGROUND:
    				case UniteCreatorDialogParam::PARAM_BORDER:
    				case UniteCreatorDialogParam::PARAM_TEXTSHADOW:
    				case UniteCreatorDialogParam::PARAM_BOXSHADOW:
    				case UniteCreatorDialogParam::PARAM_CSS_FILTERS:
    					
    					$groupType = $arrControl["type"];
    					    					
    					$values = $objControls->add_group_control($groupType, $arrControl);
    					    					
    				break;
    				default:
    					
    					//add control (responsive or not)
    					if(isset($arrControl["uc_responsive"])){
    						
    						unset($arrControl["uc_responsive"]);
    						    						
    						$objControls->add_responsive_control($name, $arrControl);
    						
    					}else{
    						
    						$objControls->add_control($name, $arrControl);
    					}
    					    					
    				break;
    			}
    		break;
    	}
    	
    	
    	//add some child params
    	$this->checkAddRelatedControls($param, $objControls);
    	
    }
    
    /**
     * add image sizes control
     */
    private function addImageSizesControl($paramImage, $objControls){
    	
    	$type = UniteFunctionsUC::getVal($paramImage, "type");
    	$title = UniteFunctionsUC::getVal($paramImage, "title");
    	$name = UniteFunctionsUC::getVal($paramImage, "name");
    	
    	$copyKeys = array("enable_condition","condition_attribute","condition_operator","condition_value");
    	
    	$arrSizes = UniteFunctionsWPUC::getArrThumbSizes();
    	
    	$arrSizes = array_flip($arrSizes);
    	
    	$param = array();
    	$param["type"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
    	
    	if($type == UniteCreatorDialogParam::PARAM_POSTS_LIST){
	    	$param["title"] = $title .= " ".__("Image Size","unlimited-elements-for-elementor");
    		$param["name"] = $name .= "_imagesize";
    	}
    	else{
	    	$param["title"] = $title .= " ".__("Size","unlimited-elements-for-elementor");
    		$param["name"] = $name .= "_size";
    	}
    	
    	$param["options"] = $arrSizes;
    	$param["default_value"] = "medium_large";
    	
    	//duplicate all keys
    	foreach($copyKeys as $key)
    		$param[$key] = UniteFunctionsUC::getVal($paramImage, $key);
    	
    	
    	$this->addElementorParamUC($param, $objControls);
    }
    
    
    /**
     * add related controls for some params like image
     */
    private function checkAddRelatedControls($param, $objControls){
    	
    	$type = UniteFunctionsUC::getVal($param, "type");
    	
    	switch($type){
    		case UniteCreatorDialogParam::PARAM_IMAGE:
    			
    			$isAddSizes = UniteFunctionsUC::getVal($param, "add_image_sizes");
    			$isAddSizes = UniteFunctionsUC::strToBool($isAddSizes);
    			
    			if($isAddSizes == true)
    				$this->addImageSizesControl($param, $objControls);
    			    			
    		break;
    	}
    	
    }
    
    /**
     * get addon depends
     */
    protected function ucGetAddonDepents(UniteCreatorAddon $objAddon, $arrHandles=array()){
		
    	$output = new UniteCreatorOutput();
    	$output->initByAddon($objAddon);
    	
    	$arrIncludes = $output->getProcessedIncludes(true, true, "js");
    	        
        foreach($arrIncludes as $arrInclude){
        	
        	$handle = UniteFunctionsUC::getVal($arrInclude, "handle");
        	
        	$arrHandles[$handle] = $handle;
        }
		
        return($arrHandles);
    	
    }
    
    /**
     * get gallery param default items
     */
    private function getGalleryParamDefaultItems(){
    	
    	$arrItems = $this->objAddon->getProcessedItemsData(UniteCreatorParamsProcessor::PROCESS_TYPE_OUTPUT);
    	if(empty($arrItems))
    		$arrItems = array();
    	
    	$arrDefaults = array();
    	
    	$urlAssets = $this->objAddon->getUrlAssets();
    	
    	foreach($arrItems as $arrItem){
    		    		
    		$urlImage = UniteFunctionsUC::getVal($arrItem, "image");
    		
    		if(is_array($urlImage)){
    			$urlImage = UniteFunctionsUC::getVal($urlImage, "item");
    			$urlImage = UniteFunctionsUC::getVal($urlImage, "image");
    		}
    		
    		if(empty($urlImage))
    			continue;
    		
    		if(!empty($urlAssets))
    			$urlImage = HelperUC::convertFromUrlAssets($urlImage, $urlAssets);
    			    		
    		$arrDefaults[] = array("url"=>$urlImage,"id"=>null);
    	}
    	
    	return($arrDefaults);
    }
    
    /**
     * get gallery control
     */
    protected function getGalleryParamUC(){
    	
    	//get internal defaults
		$arrDefaults = $this->getGalleryParamDefaultItems();
		
    	$param = array();
    	$param["type"] = "uc_gallery";
    	$param["title"] = __("Add Images","unlimited-elements-for-elementor");
    	$param["name"] = "uc_gallery_items";
    	$param["default_value"] = $arrDefaults;
    	$param["add_dynamic"] = true;
    	
    	return($param);
    }
    
    /**
     * add gallery param
     */
    protected function addGalleryControlUC(){
    	
    	$param = $this->getGalleryParamUC();
    	
    	$this->addElementorParamUC($param);
    	
    }
    
    /**
     * add edit html control
     */
    private function addEditAddonControl(){
    	
    	if($this->isConsolidated == true)
    		return(false);
    	
    	if(is_admin() == false)
    		return(false);
    	
    	if(class_exists("UniteProviderAdminUC") == false)
    		return(false);
    	
    	if(UniteProviderAdminUC::$isUserHasCapability == false)
    		return(false);
    	
    	$addonID =  $this->objAddon->getID();
    	
    	$urlEditAddon = HelperUC::getViewUrl_EditAddon($addonID, "", "tab=uc_tablink_html");
		
    	$html = "<button class='elementor-button elementor-button-default uc-button-edit-html' onclick='window.open(\"$urlEditAddon\")'>Edit Widget HTML</button>";
		
		$this->objControls->add_control(
			'html_button_gotoaddon',
			array(
				'type' => Controls_Manager::RAW_HTML,
				'label' => '',
				'raw' => $html
			)
		);
		
    }
    
    
    /**
     * add dynamic attribute
     */
    protected function addDynamicAttributes($params){
    	
    	foreach($params as $index => $param){
    		
    		$type = UniteFunctionsUC::getVal($param, "type");
    		
	    	switch($type){
	    		case UniteCreatorDialogParam::PARAM_NUMBER:
	    		case UniteCreatorDialogParam::PARAM_SLIDER:
	    		case UniteCreatorDialogParam::PARAM_TEXTAREA:
	    		case UniteCreatorDialogParam::PARAM_TEXTFIELD:
	    		case UniteCreatorDialogParam::PARAM_LINK:
	    		case UniteCreatorDialogParam::PARAM_EDITOR:
	    		case UniteCreatorDialogParam::PARAM_IMAGE:
	    		case UniteCreatorDialogParam::PARAM_GALLERY:
	    		break;
	    		default:
	    			continue(2);
	    		break;
	    	}
	    	
	    	//skip selector enabled
			$selector = UniteFunctionsUC::getVal($param, "selector");
			$selector = trim($selector);
			
			if(!empty($selector))
				continue;
	    	
    		$param["add_dynamic"] = true;
    		
    		$params[$index] = $param;
    	}
    	
    	
    	return($params);    	
    }
    
    /**
     * add pagination controls
     */
    protected function addPaginationControls($postListParam = null){
		
    	$objPagination = new UniteCreatorElementorPagination();
    	$objPagination->addElementorSectionControls($this, $postListParam);
    	
    }
    
    /**
     * add advanced section widget controls
     */
    protected function addAdvancedSectionControls($hasPostsList = false, $isItemsEnabled = false){
    	
    	$this->start_controls_section("unlimited_advanced_features", array(
    		"label"=>__("Advanced", 'unlimited-elements-for-elementor'),
    		"tab"=>"general"));
    	
       //update button if loaded from ajax
       
       if(UniteCreatorElementorIntegrate::$enableEditHTMLButton === null){
	    	UniteCreatorElementorIntegrate::$enableEditHTMLButton = HelperProviderCoreUC_EL::getGeneralSetting("show_edit_html_button");
	    	UniteCreatorElementorIntegrate::$enableEditHTMLButton = UniteFunctionsUC::strToBool(UniteCreatorElementorIntegrate::$enableEditHTMLButton);
       }
	   
       if(UniteCreatorElementorIntegrate::$enableEditHTMLButton == true)
          $this->addEditAddonControl();
    	
		$this->add_control(
			'show_widget_debug_data',
			array(
				'label' => __( 'Show Widget Data For Debug', 'unlimited-elements-for-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'unlimited-elements-for-elementor' ),
				'label_off' => __( 'No', 'unlimited-elements-for-elementor' ),
				'return_value' => 'true',
				'default' => '',
				'separator' => 'before',
				'description'=>__('Show widget data for debugging purposes. Please turn off this option when you releasing the widget', 'unlimited-elements-for-elementor')
			)
		);
    	
		$debugTypeOptions = array();
		$debugTypeOptions["default"] = __( 'Default', 'unlimited-elements-for-elementor' );
		
		if($hasPostsList == true)
			$isItemsEnabled = true;
		
		if($isItemsEnabled == true)
			$debugTypeOptions["items_only"] = __( 'Items Only', 'unlimited-elements-for-elementor' );
		
		if($hasPostsList == true){
			$debugTypeOptions["post_titles"] = __( 'Posts Titles', 'unlimited-elements-for-elementor' );
			$debugTypeOptions["post_meta"] = __( 'Posts Titles and Meta', 'unlimited-elements-for-elementor' );
		}
		
		$debugTypeOptions["current_post_data"] = __( 'Current Post Data', 'unlimited-elements-for-elementor' );
		$debugTypeOptions["settings_values"] = __( 'Show Settings Values', 'unlimited-elements-for-elementor' );
		
		$hasDebugType = (count($debugTypeOptions) > 1);
		
		//show post enabled selection
		if($hasDebugType == true){
			
			$this->add_control(
				'widget_debug_data_type',
				array(
					'label' => __( 'Debug Data Type', 'unlimited-elements-for-elementor' ),
					'type' => Controls_Manager::SELECT,
					'default' => 'default',
					'condition'=>array("show_widget_debug_data"=>"true"),
					'options' => $debugTypeOptions,
				)
			);
		}
		
		do_action("ue_widget_advanced_controls", $this);
		
    	$this->end_controls_section();
    }
    
    
    /**
     * sort params by categories
     */
    private function sortParamsByCats($arrCats, $params){
    	
    	if(empty($arrCats))
    		$arrCats = array();
    	
    	$arrOutput = array();
    	    	
    	foreach($arrCats as $cat){
    		$catID = UniteFunctionsUC::getVal($cat, "id");
    		unset($cat["id"]);
    		
    		$cat["params"] = array();
    		
    		$arrOutput[$catID] = $cat;
    	}
    	    	
    	foreach($params as $param){
    		
    		$catID = UniteFunctionsUC::getVal($param, "__attr_catid__");
    		    		
    		if(empty($catID))
    			$catID = "cat_general_general";
    		
    		if(array_key_exists($catID, $arrOutput) == false)
    			$catID = "cat_general_general";
    		
    		unset($param["__attr_catid__"]);
			
    		$sectionCounter = 0;
    		
    		//add category
    		if(array_key_exists($catID, $arrOutput) == false){

    			//set category title
    			$catTitle = __("General", "unlimited-elements-for-elementor");
    				
    			if($catID != "cat_general_general"){
    				$sectionCounter++;
    				$catTitle = __("Section ","unlimited-elements-for-elementor") . $sectionCounter;
    			}
    			
    			$catTab = "content";
    			
    			$arrOutput[$catID] = array(
    				"title"=>$catTitle,
    				"tab"=>$catTab,
    				"params"=>array()
    			);
    		}
    		
    		
    		$arrOutput[$catID]["params"][] = $param;
    	}
    	    	
    	//remove empty categories
    	foreach($arrOutput as $catID => $cat){
    		if(empty($cat["params"]))
    			unset($arrOutput[$catID]);
    	}
    	
    	return($arrOutput);    	
    }
    
    
    /**
     * add cta control to general section
     */
    protected function addFreeVersionCTAControl(){
    	
    	
    	if(GlobalsUC::$isProVersion == true)
    		return(false);
    	    		
    	if(GlobalsUnlimitedElements::$enableInsideNotification == false)
    		return(false);
    	
    	$urlBuy = GlobalsUnlimitedElements::$insideNotificationUrl;
    	
    	$text = GlobalsUnlimitedElements::$insideNotificationText;
    	
    	$text = str_replace("[url_buy]", $urlBuy ,$text);
    	
    	$isDarkMode = UniteCreatorElementorIntegrate::isElementorPanelDarkMode();
    	
    	$addClass = "";
    	if($isDarkMode == true)
    		$addClass = " uc-dark-mode";
    	 
    	$html = "<div class='uc-notification-control {$addClass}'>$text</div>";
    	
		$this->objControls->add_control(
			'uc_pro_notification',
			array(
				'type' => Controls_Manager::RAW_HTML,
				'label' => '',
				'raw' => $html
			)
		);
    	
		
    }
    
    
    /**
     * register controls with not consolidated addon
     */
   protected function ucRegisterControls_addon(){
		
   		//$name = $this->objAddon->getAlias();
   		   	
   		//check low memory
   		if(UniteCreatorElementorIntegrate::$enableLowMemoryCheck == true){
   			
	   		 $isEnoughtMemory = UniteFunctionsUC::isEnoughtPHPMemory();
			 if($isEnoughtMemory == false){
				UniteCreatorElementorIntegrate::logMemoryUsage("Skip register controls (no memory): " . $this->objAddon->getName()." counter:".UniteCreatorElementorIntegrate::$counterControls, true);
				return(false);
			 }
   			
   		}

   		//remember the addon for use inside the settings classes
   		GlobalsProviderUC::$activeAddonForSettings = $this->objAddon;
   		
		 UniteCreatorElementorIntegrate::$counterControls++;
		 
    	 UniteCreatorElementorIntegrate::logMemoryUsage("Register controls: ".$this->objAddon->getName()." counter: ".UniteCreatorElementorIntegrate::$counterControls);
   	
    	 $isItemsEnabled = $this->objAddon->isHasItems();
         $itemsType = $this->objAddon->getItemsType();
         
         
         $allParams = $this->objAddon->getProcessedMainParams();
         $arrCats = $this->objAddon->getParamsCats();
         
         
         $isNoSettings = false;
         if(empty($allParams))
         	$isNoSettings = true;
         
         $arrCatsAndParams = $this->sortParamsByCats($arrCats, $allParams);
	              
         $hasPostsList = false;
	     $postListParam = null;
         
	     $hasListing = false;
         $listingParam = null;
	              
	     //foreach the categories
         foreach($arrCatsAndParams as $catID => $arrCat){
         	
         	$isGeneralSection = ($catID == "cat_general_general");
         	
         	$catTitle = UniteFunctionsUC::getVal($arrCat, "title");
         	$catTab = UniteFunctionsUC::getVal($arrCat, "tab");
         	
         	$arrSectionOptions = array();
         	$arrSectionOptions["label"] = $catTitle;
         	
         	$params = UniteFunctionsUC::getVal($arrCat, "params");
			
         	if($catTab == "style")
         		$arrSectionOptions["tab"] = "style";
         	
         	//section conditions
	    	$enableCondition = UniteFunctionsUC::getVal($arrCat, "enable_condition");
	    	$enableCondition = UniteFunctionsUC::strToBool($enableCondition);
         	
	    	if($enableCondition == true){
    		
	    		$elementorCondition = $this->getControlArrayUC_getCondition($arrCat);    		
	    		
	    		if(!empty($elementorCondition))
	    			$arrSectionOptions["condition"] = $elementorCondition;
	    	}
         	
         	$this->start_controls_section($catID, $arrSectionOptions);
         		
	          if($isGeneralSection == true && $isItemsEnabled == true && $itemsType == "image")
	          		$this->addGalleryControlUC();
	          
	          //add dynamic to all the addons, not only dynamic
	          $params = $this->addDynamicAttributes($params);
	          
	          $activeTab = null;
	          
	          foreach($params as $index => $param){
					
	          		$type = UniteFunctionsUC::getVal($param, "type");
	          		if($type === UniteCreatorDialogParam::PARAM_POSTS_LIST){
	          			$hasPostsList = true;
	          			$postListParam = $param;
	          			
	          			$showImageSizes = UniteFunctionsUC::getVal($postListParam, "show_image_sizes");
	          			$showImageSizes = UniteFunctionsUC::strToBool($showImageSizes);
	          			
	          			if($showImageSizes == true)
	          				$this->addImageSizesControl($postListParam, $this->objControls);
	          			
	          			continue;
	          		}
	          		
	          		if($type == UniteCreatorDialogParam::PARAM_LISTING){
	          			
	          			$useFor = UniteFunctionsUC::getVal($param, "use_for");
	          			switch($useFor){
	          				case "remote":
	          				case "filter":
	          				break;
	          				default:
			          			$hasListing = true;
			          			$listingParam = $param;
	          				break;
	          			}
	          		}
	          		
	          		$tabName = UniteFunctionsUC::getVal($param, "tabname");
	          		$tabName = trim($tabName);

	          		/**
	          		 * end tabs, if opened and no active tab
	          		 */
	          		if(!empty($activeTab) && empty($tabName)){
	          			$this->end_controls_tab();
	          			$this->end_controls_tabs();
	          			$activeTab = null;
	          		}
	          		
	          		//check and add tabs 
	          		
	          		if(!empty($tabName)){
	          			
	          			//if empty active tab - start tabs area
	          			if(empty($activeTab)){
          					
	          				$numTab = $this->tabsCounter++;
          					
	          				$tabsID = "uctabs{$numTab}";
	          				$tabID = "uctab{$numTab}";
	          				
	          				$this->start_controls_tabs($tabsID);
		          			$this->start_controls_tab($tabID, array("label"=>$tabName));
	          				
	          				$activeTab = $tabName;
	          				
	          			}else{
	          				
	          				//open another tab, if changed
	          				
	          				if($activeTab != $tabName){
	          					$numTab = $this->tabsCounter++;
	          					
	          					$tabID = "uctab{$numTab}";
	          					
			          			$this->end_controls_tab();
			          			$this->start_controls_tab($tabID, array("label"=>$tabName));
	          					$activeTab = $tabName;
	          				}
	          			
	          			}//else
	          			
	          			
	          		}//not emtpy tabName

	          		$this->addElementorParamUC($param);
	          			          		
	          		
	          }	//end params foreach
	          
	          //if inside some tab, in last option - close tabs
	          if(!empty($activeTab)){
	          	
          		 $this->end_controls_tab();
          		 $this->end_controls_tabs();
          		 $activeTab = null;
	          }

	          
	        //add free version notification
	       	if($isGeneralSection && GlobalsUC::$isProVersion == false)
	       		$this->addFreeVersionCTAControl();
		  
	       	
          $this->end_controls_section();
              
         } //end sections foreach
         
         
          //add query controls section (post list) if exists
          if($hasPostsList == true){
			
          	$forWooCommerce = UniteFunctionsUC::getVal($postListParam, "for_woocommerce_products");
          	$forWooCommerce = UniteFunctionsUC::strToBool($forWooCommerce);
          	
          	if($forWooCommerce == true)
          		$labelPosts = esc_html__("Products Query", "unlimited-elements-for-elementor");
			else
          		$labelPosts = esc_html__("Posts Query", "unlimited-elements-for-elementor");
			
	        $this->start_controls_section(
	                'section_query', array(
	                'label' => $labelPosts,
	              )
	        );

          	  $this->addElementorParamUC($postListParam);
	          
	          $this->end_controls_section();
          }
		  
          if($hasListing == true){
          	
          		$this->listingName = UniteFunctionsUC::getVal($listingParam, "name");
          		
          		$this->putListingSections($listingParam);
          }
          	
          //add no attributes section
         if($isNoSettings == true){
         	
	        $this->start_controls_section(
	                'section_general_general', array(
	                'label' => __("General", "unlimited-elements-for-elementor"),
	              )
	        );
	         
	        if($isItemsEnabled == true && $itemsType == "image")
	          	$this->addGalleryControlUC();
	        else
		        $this->add_control("no_settings_heading", array(
		        	"label"=>__("No Settings for this Widget", "unlimited-elements-for-elementor"),
		        	"type"=>Controls_Manager::HEADING
		        ));
	        
	        $this->end_controls_section();
         }

         
          // --- add items
          if($isItemsEnabled == true && $itemsType != "image")
          	$this->addItemsControlsUC($itemsType);
          
          
          // --- add fonts
          $isFontsEnabled = $this->objAddon->isFontsPanelEnabled();
          if($isFontsEnabled == true)
          		$this->addFontControlsUC();
   		  
          
          //add pagination section if needed
          if($hasPostsList == true){
          		$this->addPaginationControls($postListParam);
          
          }else if($hasListing == true){
          	          	
          	$enablePagination = UniteFunctionsUC::getVal($listingParam, "enable_pagination");
          	$enablePagination = UniteFunctionsUC::strToBool($enablePagination);
          	
          	$enableFiltering = UniteFunctionsUC::getVal($listingParam, "enable_ajax");
          	$enableFiltering = UniteFunctionsUC::strToBool($enableFiltering);
          	
          	if($enableFiltering == true)
          		$listingParam["is_filterable"] = true;
          	
          	$listingName = $listingParam["name"];
          	          	
          	$listingParam["condition"] = array($listingName."_source"=>array("posts","products"));
          	
          	
          	if($enablePagination == true)
          		$this->addPaginationControls($listingParam);
          }
          
          
          $showMore = false;
          if($hasPostsList == true || $hasListing == true)
          	$showMore = true;
          
          //add debug controls
          $this->addAdvancedSectionControls($showMore, $isItemsEnabled);
          
          
		if(self::DEBUG_CONTROLS && $this->isBGWidget == false){
		
			dmp("end debug");
			exit();
		}
          
          
   }
   
   private function a__________DYNAMIC_SECTIONS_________(){}
   
   
    /**
     * put listing sections
     */
    private function putListingSections($listingParam){
    	
    	
    	$objUEControls = new UniteCreatorElementorControls();
    	
    	$name = UniteFunctionsUC::getVal($listingParam, "name");
    	
    	$useFor = UniteFunctionsUC::getVal($listingParam, "use_for");
    	$isForGallery = ($useFor == "gallery");
    	
    	//multisource
    	
    	$isForItems = ($useFor == "items");
    	
    	
    	switch($useFor){
    		case "remote":
    		case "filter":
    			return(false);
    		break;
    	}
    	
		//set text prefix
		
		$textPrefix = __("Items ","unlimited-elements-for-elementor");		
		if($isForGallery == true)
			$textPrefix = __("Gallery ","unlimited-elements-for-elementor");
		
    	//add post section
        $this->start_controls_section(
                'uc_section_listing_posts_query', array(
                'label' => $textPrefix.__("Posts Query", "unlimited-elements-for-elementor"),
        		'condition'=>array($name."_source"=>"posts")
              )
        );
		
        $postParam = $listingParam;
        
        $postParam["type"] = UniteCreatorDialogParam::PARAM_POSTS_LIST;
        $postParam["name"] = $name."_posts";
        
        $this->addElementorParamUC($postParam);
           
        $this->end_controls_section();
    	
        
        //------ terms -------------
        
        if($isForItems == true){
        				
	    	//add post section
	        $this->start_controls_section(
	                'uc_section_listing_terms_query', array(
	                'label' => $textPrefix.__("Terms Query", "unlimited-elements-for-elementor"),
	        		'condition'=>array($name."_source"=>"terms")
	              )
	        );
			
	        $termParam = $listingParam;
	        
	        $termParam["type"] = UniteCreatorDialogParam::PARAM_POST_TERMS;
	        $termParam["name"] = $name."_terms";
	        
	        $this->addElementorParamUC($termParam);
        
	        $this->end_controls_section();
        
        //------ users -------------
        
	    	//add post section
	        $this->start_controls_section(
	                'uc_section_listing_users_query', array(
	                'label' => $textPrefix.__("Users Query", "unlimited-elements-for-elementor"),
	        		'condition'=>array($name."_source"=>"users")
	              )
	        );
			
	        $usersParam = $listingParam;
	        
	        $usersParam["type"] = UniteCreatorDialogParam::PARAM_USERS;
	        $usersParam["name"] = $name."_users";
	        
	        $this->addElementorParamUC($usersParam);
        
	        $this->end_controls_section();
	        
        //------ menu -------------
	        
	        $this->start_controls_section(
	                'uc_section_listing_menu_query', array(
	                'label' => $textPrefix.__("Menu Query", "unlimited-elements-for-elementor"),
	        		'condition'=>array($name."_source"=>"menu")
	              )
	        );
			
	        $menuParam = $listingParam;
	        
	        $menuParam["type"] = UniteCreatorDialogParam::PARAM_MENU;
	        $menuParam["name"] = $name."_menu";
	        $menuParam["usefor"] = "multisource";
	        
	        $this->addElementorParamUC($menuParam);
        	
	        $this->end_controls_section();
	        
	        
        	//------ gallery -------------
	        
	        $this->start_controls_section(
	                'uc_section_listing_gallery', array(
	                'label' => __("Select Items Images", "unlimited-elements-for-elementor"),
	        		'condition'=>array($name."_source"=>"gallery")
	              )
	        );
			
	        $galleryParam = $listingParam;
			
			$galleryDefaults = HelperProviderUC::getArrDynamicGalleryDefaults();
	        	        
	        $galleryParam["type"] = UniteCreatorDialogParam::PARAM_GALLERY;
	        $galleryParam["name"] = $name."_gallery";
			$galleryParam["default_value"] = $galleryDefaults;
    		$galleryParam["add_dynamic"] = true;
	        
	        $this->addElementorParamUC($galleryParam);
        
	        $this->end_controls_section();
	        
	        
        }
	        
        
        //woocommerce
        
        $isWooActive = UniteCreatorWooIntegrate::isWooActive();
        if($isWooActive == true){
			
        	//add products section
        	
	        $this->start_controls_section(
	                'uc_section_listing_products_query', array(
	                'label' => $textPrefix.__("Products Query", "unlimited-elements-for-elementor"),
	        		'condition'=>array($name."_source"=>"products")
	              )
	        );
	
		    
	        $postParam = $listingParam;
	        
	        $postParam["type"] = UniteCreatorDialogParam::PARAM_POSTS_LIST;
	        $postParam["name"] = $name."_products";
	        $postParam["for_woocommerce_products"] = true;
	        
	        $this->addElementorParamUC($postParam);
	           
	        
	        $this->end_controls_section();
        	
        }
        
        
        //add the gallery repeater
		if($isForGallery == true || $isForItems == true){
			
			$objUEControls->addGalleryImageVideoRepeater($this, $textPrefix, $name, $listingParam, $this->objAddon);
			
			//add instagram param
			
	        $this->start_controls_section(
	                'uc_section_listing_instagram', array(
	                'label' => __("Instagram Source", "unlimited-elements-for-elementor"),
	        		'condition'=>array($name."_source"=>"instagram")
	              )
	        );
			
			
	        $instagramParam = $listingParam;
	        
	        $instagramParam["type"] = UniteCreatorDialogParam::PARAM_INSTAGRAM;
	        $instagramParam["name"] = $name."_instagram";
	        	        
	        $this->addElementorParamUC($instagramParam);
			
			$this->end_controls_section();
		}
        
    }
   
   
   
   private function a__________CONSOLIDATION_________(){}
    
    
   /**
    * add addons dropdown
    */
   protected function ucRegisterControls_cat_addAddonsDropdown($meta){

   		$arrOptions = array();
   		foreach($this->arrAddons as $objAddon){
   			$title = $objAddon->getTitle();
   			$name = $objAddon->getAlias();
   			$arrOptions[$name] = $title;
   		}
   		
   		$defaultValue = UniteFunctionsUC::getFirstNotEmptyKey($arrOptions);
   		
   		$meta =	UniteFunctionsUC::encodeContent($meta);
   		
   		$control = array();
   		$control["type"] = "uc_addon_selector";
   		$control["label"] = esc_html__("Select Style", "unlimited-elements-for-elementor");
   		$control["options"] = $arrOptions;
   		$control["default"] = $defaultValue;
   		$control["meta"] = $meta;
   		
   		
   		$this->objControls->add_control("uc_addon_name", $control);
   }
   
   
   /**
    * get param value
    */
   protected function getUCParamValue($param){
   	
    	$value = UniteFunctionsUC::getVal($param, "default_value");
    	    	
    	if(array_key_exists("value", $param))
    		$value = $param["value"];
   		
    	return($value);
   }
   
   
   /**
    * get consolidated params
    */
   protected function getConslidatedData(){
   		
   		$params = array();
		$paramsFonts = array();
   		$paramsItems = array();
   		$arrDefaultItemsData = array();
   		
   		
   		//params per addon
   		$meta_addonsParams = array();
   		$meta_addonParamsItems = array();
   		
   		$minItems = 10000;		//min default items
   		
   		foreach($this->arrAddons as $objAddon){
   			
   			//get addon data
   			$addonName = $objAddon->getAlias();
   			   				   			
	        $isFontsEnabled = $objAddon->isFontsPanelEnabled();
	    	$isItemsEnabled = $objAddon->isHasItems();
	        $itemsType = $objAddon->getItemsType();
	        
	        	        
   			$meta_addonsParams[$addonName] = array();
   			
   			//add gallery param
   			   			
   			//consolidate main params
   			$addonParams = $objAddon->getProcessedMainParams();
   			
   			if($isItemsEnabled == true && $itemsType == "image"){
   				$paramGallery = $this->getGalleryParamUC();
   				
   				array_unshift($addonParams, $paramGallery);
   			}
   			
   			foreach($addonParams as $param){
   				
   				$name = UniteFunctionsUC::getVal($param, "name");
				$meta_addonsParams[$addonName][] = $name;
   				
				$existingParam = UniteFunctionsUC::getVal($params, $name);
   				
				//set default value if exists
   				if(!empty($existingParam)){
   					$existingValue = $this->getUCParamValue($existingParam);
   					$newParamValue = $this->getUCParamValue($param);
   					if(empty($existingValue) && !empty($newParamValue))
   						$params[$name]["value"] = $newParamValue;
   				}
   				else
   				  $params[$name] = $param;
   			}
   			   			
   			
   			//----- consolidate fonts
   			
	        if($isFontsEnabled == true){
	            $arrFontsSections = $objAddon->getArrFontsParamNames();
		        $arrFontsParams = $objAddon->getArrFontsParams();
	        	
		        if(empty($arrFontsSections))
		        	$arrFontsSections = array();
		        
		        //consolidate font controls
		        foreach($arrFontsSections as $sectionName => $sectionTitle){
		        	
		        	$addonFontParams = $arrFontsParams[$sectionName];
		        	
		        	if(isset($paramsFonts[$sectionName]) == false){
		        		$paramsFonts[$sectionName] = array(
		        			"title"=>$sectionTitle,
		        			"params"=>$addonFontParams
		        		);
		        	}
		        	
		        	//update meta
		        	$meta_addonsParams[$addonName][] = "section_styles_".$sectionName;
		        	
		        	foreach($addonFontParams as $fontParam){
		        		$fontParamName = $fontParam["name"];
		        		$meta_addonsParams[$addonName][] = $fontParamName;
		        	}
		        			        	
		        }//foreach sections
		       	
	        }
	        
	        //----- consolidate items
	        
	        if($isItemsEnabled == true){
				$meta_addonsParams[$addonName][] = "uc_items";
				$meta_addonsParams[$addonName][] = "section_uc_items_consolidation";
	        }
	       	
	        if($isItemsEnabled == true && $itemsType != "image"){
	   			
	        	$meta_addonParamsItems[$addonName] = array();
	        	
	        	$addonParamsItems = $objAddon->getProcessedItemsParams();
	        	
	   			foreach($addonParamsItems as $paramItem){
	   				
	   				$name = UniteFunctionsUC::getVal($paramItem, "name");
					
	   				$meta_addonParamsItems[$addonName][] = $name;
					
					$existingParam = UniteFunctionsUC::getVal($paramsItems, $name);
					
					if(empty($existingParam))
						$paramsItems[$name] = $paramItem;
						
	   			}
   				
	        }
	        
	        //get default item data
	        if($isItemsEnabled == true){
		       	  		        
	        	$arrItemsData = $objAddon->getArrItemsForConfig();
	        	
	        	//dmp($arrItemsData);exit();
	        	
	        	if(!empty($arrItemsData)){
	        		
		         	$arrItemsData = $this->modifyDefaultItemsDataUC($arrItemsData, $objAddon);
		         	
		         	$numItems = count($arrItemsData);
		         	
		         	if($numItems < $minItems)
		         		$minItems = $numItems;
		         			         	
		         	foreach($arrItemsData as $index=>$item){
		         		
		         		//add
		         		if(!isset($arrDefaultItemsData[$index])){		
		         			$arrDefaultItemsData[$index] = $item;
		         		}else{	//update not existing
		         			foreach($item as $paramName=>$value){
		         				
		         				if(isset($arrDefaultItemsData[$index][$paramName]) == false)
		         					$arrDefaultItemsData[$index][$paramName] = $value;
		         					
		         			}
		         			
		         		}//else
		         	
		         	}//foreach
		         			         	
		         	
	        	}//if not empty defaults
	        	
	        }//is items enabled
	        
   		}//foreach addons

   		
   		
   		//cut by min items the defaults
   		if(!empty($arrDefaultItemsData)){
   			
   			if(count($arrDefaultItemsData) > $minItems)
   				$arrDefaultItemsData = array_slice($arrDefaultItemsData, 0, $minItems);
   		}
   		
   		$meta = array();
   		$meta["addon_params"] = $meta_addonsParams;
   		$meta["addon_params_items"] = $meta_addonParamsItems;
   		
   		$output = array();
   		$output["params"] = $params;
   		$output["params_fonts"] = $paramsFonts;
   		$output["params_items"] = $paramsItems;
   		$output["items_defaults"] = $arrDefaultItemsData;
   		$output["meta"] = $meta;
   		
		return($output);
   }
   
   
   
   /**
    * register consolidated font controls
    */
   protected function registerControls_cat_fonts($arrFontSections){
			   			
          	foreach($arrFontSections as $sectionName=>$section){
				
          		$title = $section["title"];
          		$arrParams = $section["params"];
          		
          		$this->start_controls_section(
		                'section_styles_'.$sectionName, array(
		                'label' => $title." ".esc_html__("Styles", "unlimited-elements-for-elementor"),
          				'tab' => "style"
		                 )
		         );
          			        		        	
	          	foreach($arrParams as $name => $param)
	          		$this->addElementorParamUC($param);
	          	
	          	
	          	$this->end_controls_section();
          	}
          	
          	//add fake section
          	
          	$this->start_controls_section(
		                'uc_section_styles_indicator', array(
		                'label' => "UC Styles",
          				'tab' => "style"
		                 )
		     );
		     
	   		$arrControl = array();
	    	$arrControl["type"] = "uc_hr";
	    	$arrControl["class"] = "uc_style_controls_hr";
    		$this->objControls->add_control("uc_style_controls_".$sectionName, $arrControl);
		     
		    $this->end_controls_section();
		         
   }
   
   /**
    * register category items consolidated
    */
   protected function registerControls_cat_items($paramsItems, $itemsDefaults){
		 
         $this->start_controls_section(
                'section_uc_items_consolidation', array(
                'label' => esc_html__("Items", "unlimited-elements-for-elementor"),
                    )
          );

         $arrFields = array();
         foreach($paramsItems as $param){
         	
         	$arrControl = $this->getControlArrayUC($param, true);
         	$arrFields[] = $arrControl;
         }
         
         $arrItemsControl = array();
         $arrItemsControl["type"] = Controls_Manager::REPEATER;
         $arrItemsControl["fields"] = $arrFields;
         
         if(empty($itemsDefaults))
         	$itemsDefaults = array();
                  	
         $arrItemsControl["default"] = $itemsDefaults;
         $this->objControls->add_control('uc_items', $arrItemsControl);
         
         $this->end_controls_section();
   }
   
   
   /**
    * register addon by some category
    */
    protected function ucRegisterControls_cat(){
    	    	
    	$data = $this->getConslidatedData();
                
        $meta = $data["meta"];
        $params = $data["params"];
        $paramsFonts = $data["params_fonts"];
    	$paramsItems = $data["params_items"];
    	$itemsDefaults = $data["items_defaults"];
        
        //register general controls
        
    	$this->start_controls_section(
                'section_general', array(
                'label' => esc_html__("General", "unlimited-elements-for-elementor"),
                    )
          );
    	
    	$this->ucRegisterControls_cat_addAddonsDropdown($meta);
    		
        foreach($params as $param)
          		$this->addElementorParamUC($param);
    	          		
    	$this->end_controls_section();
    	
    	//register font controls
    	if(!empty($paramsFonts))
    		$this->registerControls_cat_fonts($paramsFonts);
    	
    	//register items controls
    	if(!empty($paramsItems))
    		$this->registerControls_cat_items($paramsItems, $itemsDefaults);
    		
    }
    
    /**
     * test controls
     */
    private function registerControlsTest(){
    	
         $this->start_controls_section(
                'section_general', array(
                'label' => "General"
                    )
          );
    	
    	
    	$this->objControls->add_control("title",[
    		"type"=>"text",
    		"label"=>"Title",
    		"default"=>"This is some title"    	
    	]);
    	
    	$this->end_controls_section();
	}
    
    /**
	* register controls
    */
    protected function register_controls() {

    	//$this->registerControlsTest();
    	//return(false);
    	
    	try{
          
    	  if($this->isConsolidated == true){
    	  		
    	  		$this->ucRegisterControls_cat();
    	  	
    	  }else{
    	  		
    	  		$this->ucRegisterControls_addon();
    	  	
    	  }
    		
    	  
    	}catch(Exception $e){
    		
    		HelperHtmlUC::outputException($e);
    		exit();
    	}
        
    }
    
    
    private function a________RENDER_RELATED__________(){}
    
    /**
     * get addon settings from elementor settings
     * function used from outside, not related to the widget
     */
    public function setAddonSettingsFromElementorSettings($objAddon, $arrValues){
    	
		$hasItems = $objAddon->isHasItems();
    	$itemsType = $objAddon->getItemsType();
		
    	if($hasItems == true){
    		
    		if($itemsType == "image")
    			$arrItems = UniteFunctionsUC::getVal($arrValues, "uc_gallery_items");
    		else 
    			$arrItems = UniteFunctionsUC::getVal($arrValues, "uc_items");
    			    		
    		$arrItems = $this->modifyArrItemsParamsValuesUC($arrItems, $itemsType);
    	}
    	
    	$arrMainParamValues = $this->getArrMainParamValuesUC($arrValues, $objAddon);
	    
    	$objAddon->setParamsValues($arrMainParamValues);
    	
    	if($hasItems == true)
    		$objAddon->setArrItems($arrItems);
    	
    	return($objAddon);
    	    	
    }
    
    
    /**
     * get global colors array if missing
     */
    public static function getGlobalColors(){
    	
    	if(!empty(self::$arrGlobalColors))
    		return(false);

    	self::$arrGlobalColors = array();
    	
		$plugin = \Elementor\Plugin::$instance;
		
		if(isset($plugin) == false)
			return(false);
			
		$dataManager = null;
		if(isset($plugin->data_manager))
			$dataManager = $plugin->data_manager;
		
		if(empty($dataManager) && isset($plugin->data_manager_v2))
			$dataManager = $plugin->data_manager_v2;
		
		if(empty($dataManager))
			return(false);
		
		if(method_exists($dataManager,"run") == false)
			return(false);
		
		$arrColors = $dataManager->run("globals/colors");
		
		if(empty($arrColors))
			return(false);
		
		//get saved ones
    	foreach($arrColors as $colorID=>$color){
    		    			
    		$value = UniteFunctionsUC::getVal($color, "value");
    		    		
    		self::$arrGlobalColors[$colorID] = $value;
    	}
    	
    	return(self::$arrGlobalColors);
    }
    
    
    /**
     * process global colors
     */
    private function getSettingsValues_processGlobalColors($arrValues, $arrSettings){
    	
    	self::getGlobalColors();
    	
    	if(empty(self::$arrGlobalColors))
    		return($arrValues);
    	
    	$globalColors = UniteFunctionsUC::getVal($arrSettings, "__globals__");
    	if(empty($globalColors))
    		return($arrValues);
    	
    	foreach($globalColors as $key=>$colorDomain){
    		
    		if(strpos($colorDomain, "globals/colors?id=") === false)
    			continue;
    		
    		$colorName = str_replace("globals/colors?id=", "", $colorDomain);
    		$colorName = trim($colorName);
    		
    		$color = UniteFunctionsUC::getVal(self::$arrGlobalColors, $colorName);
    		
    		if(empty($color))
    			continue;
    			
    		$arrValues[$key] = $color;
    	}
    	
    	return($arrValues);
    }
    
    
    
    /**
     * get settings values
     */
    protected function getSettingsValuesUC($arrSettings){
		
		if(self::DEBUG_SETTINGS_VALUES === true){
			dmp("Debug settings values");
			dmp($arrSettings);
		}
				
    	$arrValues = array();
    	foreach($arrSettings as $key=>$value){
    		    		
    		if(empty($key))
    			continue;
    		    		
    		if($key[0] == "_")
    			continue;
    		
    		$arrValues[$key] = $value;
    	}
		
    	//add elementor id
    	//$elementorID = $this->get_id();
    	//$arrValues["uc_widget_system_id"] = $elementorID;
    	
    	$arrValues = $this->getSettingsValues_processGlobalColors($arrValues, $arrSettings);
    	
    	    	
    	return($arrValues);
    }
    
    /**
     * parse font key
     */
    protected function parseFontKey($key){
    	
    	if(strpos($key, "ucfont_") !== 0)
			UniteFunctionsUC::throwError("Wrong font key: $key");
    	
		$key = substr($key, strlen("ucfont_"));
		
		$arrKey = explode("__", $key);
		
		if(count($arrKey) != 2)
			UniteFunctionsUC::throwError("Wrong font key, no __ delimiter: $key");
		
		$output = array();
		$output["param_name"] = $arrKey[0];
		$output["font_name"] = $arrKey[1];
		
		return($output);
    }
    
    
    /**
     * get fonts from settings values
     */
    protected function getArrFonts($arrValues){
    	
    	$arrFonts = array();
    	$arrEnabled = array();
    	
    	foreach($arrValues as $key=>$value){
    		
    		if(strpos($key, "ucfont_") !== 0)
    			continue;
    		
    		$arrParsed = $this->parseFontKey($key);
    		
    		$paramName = $arrParsed["param_name"];
    		$fontName = $arrParsed["font_name"];
    		
    		if($fontName == "fonts-enabled"){
    			if($value == "on" || $value == "yes" || $value === true)
    				$arrEnabled[$paramName] = true;
    				
    			continue;
    		}
    		
    		if(!isset($arrFonts[$paramName]))
    			$arrFonts[$paramName] = array();
    		
    		$arrFonts[$paramName][$fontName] = $value;
    					
    	}
    	    	
    	//prepare output
    	$arrFontsOutput = array();
    	
    	if(empty($arrEnabled))
    		return($arrFontsOutput);
    	
    	foreach($arrEnabled as $paramName=>$nothing){
    		
    		if(isset($arrFonts[$paramName]))
    			$arrFontsOutput[$paramName] = $arrFonts[$paramName];
    	}
    	
    	
    	return($arrFontsOutput);
    }
    
    
    /**
     * modify image value from array to regular
     */
    protected function modifyImageValueUC($arrValue){
    	    	
    	if(is_array($arrValue) == false)
    		return($arrValue);
		
    	$isLink = $this->isValueIsLink($arrValue);
    	if($isLink == true)
    		return($arrValue);
    	
    	if(array_key_exists("url", $arrValue) == false && array_key_exists("id", $arrValue) == false)
    		return($arrValue);
    	
    	$id = UniteFunctionsUC::getVal($arrValue, "id");
    	if(!empty($id))
    		return($id);
    	
    	$url = UniteFunctionsUC::getVal($arrValue, "url");
		
    	return($url);
    }
    
    
    /**
     * modify items values array
     */
    public function modifyArrItemsParamsValuesUC($arrItems, $itemsType){
    	
    	if(empty($arrItems))
    		return(array());
    	    	
    	foreach($arrItems as $itemIndex => $arrItem){
    		
    		if($itemsType == "image"){	//modify image base
    			    			
    			try{
	    			$imageValue = $this->modifyImageValueUC($arrItem);
	    				    			
	    			$arrData = UniteFunctionsWPUC::getAttachmentData($imageValue);
	    			
    			}catch(Exception $e){
    				$arrData = array();
    			}
    			
    			if(empty($arrData)){
    				$arrData = array();
    				$arrData["title"] = "";
    				$arrData["image"] = "";
    				$arrData["thumb"] = "";
    				$arrData["description"] = "";
    			}
    			
    			$arrItems[$itemIndex] = $arrData;
    			
    		}else{		//modify regular repeater
	    		
    			foreach($arrItem as $key=>$value){
		    		
	    			if(is_array($value))
		    			$arrItems[$itemIndex][$key] = $this->modifyImageValueUC($value);
		    		
	    		}
    			
    		}
    		    		
    	}
    	
    	
    	return($arrItems);
    }
    

    /**
     * check all the switchers params, if value not found, set false value
     */
    protected function modifyValuesBySwitchers($arrValues, $objAddon){
    	
    	$paramsSwitchers = $objAddon->getParams(UniteCreatorDialogParam::PARAM_RADIOBOOLEAN);
    	
    	if(empty($paramsSwitchers))
    		return($arrValues);
    	
    	foreach($paramsSwitchers as $param){
    		
    		$name = UniteFunctionsUC::getVal($param, "name");
    		$value = UniteFunctionsUC::getVal($arrValues, $name);
    		    		
    		if(empty($value)){
    			$falseValue = UniteFunctionsUC::getVal($param, "false_value");
    			$arrValues[$name] = $falseValue;
    		}
    		    		
    	}
    	
    	return($arrValues);
    }
    
    /**
     * return if the value is a link
     */
    private function isValueIsLink($arrValue){
    	
    	if(is_array($arrValue) == false)
    		return(false);
    		
    	if(array_key_exists("is_external", $arrValue) == false)
    		return(false);
    	
    	if(array_key_exists("nofollow", $arrValue) == false)
    		return(false);
    	
    	return(true);
    }
        
    
    /**
     * get main param values from values
     */
    protected function getArrMainParamValuesUC($arrValues, $objAddon){
    			    	
    	$arrValues = $this->modifyValuesBySwitchers($arrValues, $objAddon);
    	
    	foreach($arrValues as $key => $value){
    		
    		
    		if(strpos($key, "ucfont_") === 0){
    			unset($arrValues[$key]);
    			continue;
    		}
    		
    		if($key == "uc_items"){
    			unset($arrValues[$key]);
    			continue;
    		}
    		
    		if(is_array($value)){
    			
    			$arrValues[$key] = $this->modifyImageValueUC($value);
    		}
    		
    	}
    	
    	
    	return($arrValues);
    }

    /**
     * check if edit mode
     */
    protected function isEditMode(){
		
    	$isEditMode = HelperUC::isElementorEditMode();
    	
    	return($isEditMode);
    }
    
    /**
     * put addon not exist error message
     */
    private function putAddonNotExistErrorMesssage(){
    	
    	$addonName = $this->get_name();
    	
    	echo "<span style='color:red'>$addonName widget not exists</span>";
    }

    
    /**
     * get pagination extra html
     */
    private function getExtraWidgetHTML_pagination($arrValues, UniteCreatorAddon $objAddon){
    	
    	$isPaginationExists = false;
    	
    	//----------- by post
    	
    	$arrPostListParam = $objAddon->getParamByType(UniteCreatorDialogParam::PARAM_POSTS_LIST);
    	
    	if(!empty($arrPostListParam)){
    		$isPaginationExists = true;
    		$postListName = UniteFunctionsUC::getVal($arrPostListParam, "name");
    	}
    	
    	//----- by listing
    	
    	$arrListingParam = $objAddon->getListingParamForOutput();
    	
    	if(!empty($arrListingParam)){
    		
    		$enablePagination = UniteFunctionsUC::getVal($arrListingParam, "enable_pagination");
    		$enablePagination = UniteFunctionsUC::strToBool($enablePagination);
    		   
    		if($enablePagination == true){
    			$isPaginationExists = true;
	    		$name = UniteFunctionsUC::getVal($arrListingParam, "name");
	    		$postListName = "{$name}_posts";
    		}
    		
    	}
    	
    	if($isPaginationExists == false)
    		return("");
    	
    	//----------- check by type
    		
    	$paginationType = UniteFunctionsUC::getVal($arrValues, "pagination_type");
    	
    	if(empty($paginationType))
    		return("");
    	
    	$isArchivePage = UniteFunctionsWPUC::isArchiveLocation();
    	
    	//validate post source, enable only on current
    	if($isArchivePage == true){
    	
	    	$postListName = UniteFunctionsUC::getVal($arrPostListParam, "name");
	    	$postsSource = UniteFunctionsUC::getVal($arrValues, $postListName."_source");
	    	
	    	if($postsSource != "current")
	    		return("");
    	}
    	
    		    	    	    	
		$objPagination = new UniteCreatorElementorPagination();
		$htmlPagination = $objPagination->getHTMLPaginationByElementor($arrValues, $isArchivePage);
    	
		return($htmlPagination);
    }
    
    /**
     * get extra widget html
     */
    private function getExtraWidgetHTML($arrValues, UniteCreatorAddon $objAddon){
    	  
    	$htmlPagination = $this->getExtraWidgetHTML_pagination($arrValues, $objAddon);
    	
    	return($htmlPagination);
    }
    
    
    /**
     * get dynamic settings values if exists
     */
    public function ueGetDynamicSettingsValues(){
    	
    	if($this->hasDynamicSettings == false)
    		return(null);
    	
    	$arrDynamic = $this->parse_dynamic_settings(null);
    	
    	
    	return($arrDynamic);
    }
    
    
    /**
     * render by addon
     */
    protected function ucRenderByAddon($objAddon){
    	
    	try{
    		
	    	if(empty($objAddon)){
	    		$this->putAddonNotExistErrorMesssage();
	    		return(false);
	    	}

	    	GlobalsUnlimitedElements::$currentRenderingWidget = $this;
			
	    	$arrAllSettings = $this->get_settings_for_display();
	    	
	    	//check if has dynamic
	    	
	    	$arrDynamicSettings = UniteFunctionsUC::getVal($arrAllSettings, "__dynamic__");
	    	
	    	if(!empty($arrDynamicSettings))
	    		$this->hasDynamicSettings = true;
	    	
	    	$arrValues = $this->getSettingsValuesUC($arrAllSettings);
	    	
	        $widgetID = $this->get_id();
	    	
	        $addonTitle = $objAddon->getTitle();
	    	
	        if(GlobalsProviderUC::$isUnderNoWidgetsToDisplay == true){
	        	echo "<!-- skip widget output: {$addonTitle} -->\n";
	        	return(false);
	        }
	        
	    	HelperUC::addDebug("output widget ($widgetID) - $addonTitle");
	    	
	    	HelperUC::addDebug("widget values", $arrValues);
	    	
	    	$arrFonts = $this->getArrFonts($arrValues);
	    	
	    	if(self::DEBUG_WIDGETS_OUTPUT == true){
	    		HelperUC::showDebug();
	    		HelperUC::clearDebug();
	    	}
	    	
	    	
	    	//get items
	    	$hasItems = $objAddon->isHasItems();
	    	$itemsType = $objAddon->getItemsType();
	    	
	    	if($hasItems == true){
	    		
	    		if($itemsType == "image")
	    			$arrItems = UniteFunctionsUC::getVal($arrValues, "uc_gallery_items");
	    		else 
	    			$arrItems = UniteFunctionsUC::getVal($arrValues, "uc_items");
	    			    		
	    		$arrItems = $this->modifyArrItemsParamsValuesUC($arrItems, $itemsType);
	    		 
	    	}
	   	
	    	$arrMainParamValues = $this->getArrMainParamValuesUC($arrValues, $objAddon);
	    		
	    	//transfer the pagination type
    		$arrPostListParam = $objAddon->getParamByType(UniteCreatorDialogParam::PARAM_POSTS_LIST);
    		
    		if(!empty($arrPostListParam)){
    		
    			$paginationType = UniteFunctionsUC::getVal($arrMainParamValues, "pagination_type");
    			$postListName = UniteFunctionsUC::getVal($arrPostListParam, "name");
    			
    			$arrMainParamValues[$postListName."_pagination_type"] = $paginationType;    			    			
    		}
	    	
    		$arrListingParam = $objAddon->getListingParamForOutput();
    		if(!empty($arrListingParam)){
    			
    			$paginationType = UniteFunctionsUC::getVal($arrMainParamValues, "pagination_type");
    			$postListingName = UniteFunctionsUC::getVal($arrListingParam, "name");
    			
    			$arrMainParamValues[$postListingName."_posts_pagination_type"] = $paginationType;
    			$arrMainParamValues[$postListingName."_products_pagination_type"] = $paginationType;
    		}
    		
    		
	    	//check if inside editor
	        $isEditMode = $this->isEditMode();
	       	
	    	$objAddon->setParamsValues($arrMainParamValues);
	    	$objAddon->setArrFonts($arrFonts);
	    		    	
	    	if($hasItems == true)
	    		$objAddon->setArrItems($arrItems);
	   		
	        $output = new UniteCreatorOutput();
			    
	        if(!empty($widgetID))
	        	$output->setSystemOutputID($widgetID);
	        
	        //set show debug data
	        $isShowDebugData = UniteFunctionsUC::getVal($arrValues, "show_widget_debug_data");
	        $isShowDebugData = UniteFunctionsUC::strToBool($isShowDebugData);
	        
	        $isDebugFromGet = HelperUC::hasPermissionsFromQuery("ucfieldsdebug");
	        
	        if($isDebugFromGet === true)
	        	$isShowDebugData = true;
	        
	        if($isShowDebugData == true){
	        	
	        	$debugDataType = UniteFunctionsUC::getVal($arrValues, "widget_debug_data_type");
	        	
	        	$output->showDebugData(true, $debugDataType, $arrValues);
	        }
	        
	        $output->initByAddon($objAddon);
	        
	        $cssFilesPlace = HelperProviderCoreUC_EL::getGeneralSetting("css_includes_to");
			if($isEditMode == true)
				$cssFilesPlace = "body";
			
	        if($cssFilesPlace == "footer")
	        	$output->processIncludes("css");
	        
	        //decide if the js will be in footer
	        $scriptsHardCoded = false;
	        
	        $isInFooter = HelperProviderCoreUC_EL::getGeneralSetting("js_in_footer");
	        $isInFooter = UniteFunctionsUC::strToBool($isInFooter);
			
	        if ($isInFooter == false)
	            $scriptsHardCoded = true;
			
	        if($isEditMode == true)
	            $scriptsHardCoded = true;
			
	        //put scripts if under dynami template in ajax
	        
	        if(GlobalsProviderUC::$isUnderAjaxDynamicTemplate == true)
	            $scriptsHardCoded = true;
	        
	            
	        $putCssIncludesInBody = ($cssFilesPlace == "body") ? true : false;
			
	        $params = array();
	        
	        if($isEditMode == true){
				$arrIncludes = $output->getProcessedIncludes(true, false, "js");
				
	        	$jsonIncludes = UniteFunctionsUC::jsonEncodeForClientSide($arrIncludes);
	        	
	        	if(empty($arrIncludes))
	        		$params["wrap_js_timeout"] = true;
	        	else{
	        		$params["wrap_js_start"] = "window.parent.g_objUCElementorEditorAdmin.ucLoadJSAndRun(window, {$jsonIncludes}, function(){";
	        		$params["wrap_js_end"] = "});";
	        	}
	        	
	        }else{
	        	
	        	$output->processIncludes("js");
	        }
	       	
	        $htmlOutput = $output->getHtmlBody($scriptsHardCoded, $putCssIncludesInBody,true,$params);
	       	
        	echo UniteProviderFunctionsUC::escCombinedHtml($htmlOutput);
	        
	        $htmlExtra = $this->getExtraWidgetHTML($arrValues, $objAddon);
        	
	        if(!empty($htmlExtra))
	        	echo $htmlExtra;
        	
    	}catch(Exception $e){
    		
    		HelperHtmlUC::outputException($e);
    		
    	}
                
    }
    
    
    /**
     * render the HTML
     */    
    protected function render() {
		
    	if($this->isNoMemory == true){
    		echo "no memory to render ".$this->isNoMemory_addonName." widget. <br> Please increase memory_limit in php.ini";	
    		return(false);
    	}
    	
    	if($this->isConsolidated == false){
    		
    		$this->ucRenderByAddon($this->objAddon);
    		
    	}else{		//for consolidated, find the right addon
    		
    		$arrSettings = $this->get_settings();
    		$addonName = UniteFunctionsUC::getVal($arrSettings, "uc_addon_name");
			
    		try{
	    		$objAddon = new UniteCreatorAddon();
	    		$objAddon->initByAlias($addonName, UniteCreatorElementorIntegrate::ADDONS_TYPE);
    			$this->ucRenderByAddon($objAddon);
	    		
    		}catch(Exception $e){
    			echo "error render widget: $addonName";
    			HelperHtmlUC::outputException($e);
    		}
    		
    	}
    	
    }
    
    
    /**
     * render HTML with backbone.js for elementor admin
     */ 
    /* 
    protected function content_template() {
    	
    	echo "content template";
    	
    }
    */
	

}
