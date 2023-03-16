<?php

/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

class UniteCreatorLayoutWork extends UniteElementsBaseUC{
	
	protected $id;
	protected $title, $name, $data, $gridData, $ordering, $gridDataFull, $record, $params, $arrAddonNames;
	protected static $arrCacheAddons = array();
	protected $addonType = null, $arrAllOptionsCache = null;
	protected $extraParams = array();	//extra params for link generate etc
	protected $objShapes, $layoutType, $isTemplate = false, $objLayouts;
	protected $objLayoutType;
	
	const KEY_SETTINGS_ADDDATA = "uc_settings_adddata";
	const EXPORT_CAT_TYPE_PREFIX = "uc_addontype_";
	
	
	const FIELDS_LAYOUTS = "title,ordering,layout_data,catid,name,layout_type,parent_id,relate_id,params";
	const LAYOUTS_GLOBAL_SETTINGS_OPTION = "unitecreator_layouts_general_settings";
	
	
	/**
	 * construct the layout
	 */
	public function __construct(){
		
		$this->objShapes = new UniteShapeManagerUC();
		$this->objLayouts = new UniteCreatorLayouts();
		
		parent::__construct();
		
	}
	
	/**
	 * set layout type
	 */
	public function setLayoutType($type){
		
		$this->layoutType = $type;
		$this->objLayoutType = UniteCreatorAddonType::getAddonTypeObject($type, true);
		
		$this->isTemplate = $this->objLayoutType->isTemplate;
	}
	
	
	/**
	 * validate that the layout is inited
	 */
	public function validateInited(){
		
		if(empty($this->id))
			UniteFunctionsUC::throwError("The layout is not inited");
	}
	
	
	/**
	 * check if the layout inited
	 */
	public function isInited(){
		
		if(!empty($this->id))
			return(true);
		
		return(false);
	}
	
	/**
	 * validate title
	 */
	private function validateTitle($title){
		
		UniteFunctionsUC::validateNotEmpty($title, "Title");
		
	}
	
	/**
	 * validate that name not exists
	 */
	private function validateNameNotExists($name, $layoutType){
		
		$isExists = $this->isLayoutExistsByName($name, $layoutType);
		if($isExists == true)
			UniteFunctionsUC::throwError("The layout with name: $name already exists");
		
	}
	
	/**
	 * validate layout name
	 */
	private function validateName($name){
		
		$fieldName = $this->objLayoutType->textSingle.esc_html__(" Name", "unlimited-elements-for-elementor");
		
		UniteFunctionsUC::validateNotEmpty($name, $fieldName);
		UniteFunctionsUC::validateUrlAlias($name, $fieldName);
		
		$this->validateNameNotExists($name, $this->layoutType);
	}
	
	
	/**
	 * validate that layout not exists by title
	 */
	private function validateLayoutNotExistsByTitle($title, $layoutType){
		
		$isExists = $this->isLayoutExistsByTitle($title, $layoutType);
		
		if($isExists == true)
			UniteFunctionsUC::throwError("The layout with title: $title already exists");
		
	}
	
	
	private function a_________STATIC_FUNCTIONS________(){}
	
	
	
	/**
	 * get stored values of grid options
	 */
	public static function getGridGlobalStoredValues(){
	    
		$arrValues = UniteProviderFunctionsUC::getOption(self::LAYOUTS_GLOBAL_SETTINGS_OPTION, array());
		
		return($arrValues);
	}
	
	
	/**
	 * get settings object
	 */
	public static function getGlobalSettingsObject($includeGlobalOptions = true){
		
		$filepathSettings = GlobalsUC::$pathSettings."layouts_global_settings.xml";
		
		$objSettings = new UniteCreatorSettings();
		$objSettings->loadXMLFile($filepathSettings);
		
		if($includeGlobalOptions == true){
			$arrValues = self::getGridGlobalStoredValues();
			
			if(!empty($arrValues))
				$objSettings->setStoredValues($arrValues);
		}
		
		return($objSettings);
	}
	
	/**
	 * get values with settings defaults
	 */
	public static function getLayoutGlobalSettingsValues(){
	    
	       $objSettings = self::getGlobalSettingsObject(true);
	       $arrValues = $objSettings->getArrValues();
	    
	       return($arrValues);
	}
	
	
	/**
	 * get grid settings object
	 */
	public static function getGridSettingsObject(){
		
		$filepathSettings = GlobalsUC::$pathSettings."layouts_grid_settings.xml";
		
		$objSettings = new UniteCreatorSettings();
		$objSettings->loadXMLFile($filepathSettings);
		
		
		return($objSettings);
	}
	
	
	/**
	 * get layout params object
	 */
	public function getPageParamsSettingsObject(){
		
		switch($this->objLayoutType->paramsSettingsType){
			case UniteCreatorAddonType_Layout::LAYOUT_PARAMS_TYPE_SCREENSHOT:
				$filepathSettings = GlobalsUC::$pathSettings."layout_grid_settings_screenshot.xml";
			break;
			default:
				UniteFunctionsUC::throwError("Wrong page params type: ".$this->objLayoutType->paramsSettingsType);
			break;
		}
			
		$objSettings = new UniteCreatorSettings();
		$objSettings->loadXMLFile($filepathSettings);
		
		$objSettings = UniteProviderFunctionsUC::applyFilters(UniteCreatorFilters::FILTER_LAYOUT_PROPERTIES_SETTINGS, $objSettings);
		
		$objSettings->setStoredValues($this->params);
		
		return($objSettings);
	}
	
	/**
	 * get page params values with defaults
	 */
	public function getPageParamsValuesWithDefaults(){
		
		$this->validateInited();
		
		$paramsType = $this->objLayoutType->paramsSettingsType;
		
		if(empty($paramsType))
			return($this->params);
		
		$objSettings = $this->getPageParamsSettingsObject();
		$arrValues = $objSettings->getArrValues();
		
		if(is_array($this->params))
			$arrValues = array_merge($arrValues, $this->params);
		
		return($arrValues);
	}
	
	
	/**
	 * update general settings
	 */
	public static function updateLayoutGlobalSettingsFromData($data){
		
		$arrValues = UniteFunctionsUC::getVal($data, "settings_values");
		
		UniteProviderFunctionsUC::updateOption(self::LAYOUTS_GLOBAL_SETTINGS_OPTION, $arrValues);
	}
	
	/**
	 * init by name and type
	 */
	public function initByName($name, $type){
		
		//validate
		$objType = UniteCreatorAddonType::getAddonTypeObject($type, true);
		
		$name = $this->db->escape($name);
		$sqlAddonType = $this->db->getSqlAddonType($type, "layout_type");
		
		$where = "name='$name' and $sqlAddonType";
		
		try{
			$record = $this->db->fetchSingle(GlobalsUC::$table_layouts, $where);
		}catch(Exception $e){
			UniteFunctionsUC::throwError("Layout with name: $name not found");
		}
		UniteFunctionsUC::validateNotEmpty($record, "layout record");
		
		$this->initByRecord($record);
	}
	
	
	/**
	 * init layout by id
	 */
	public function initByID($id){
		
		$id = (int)$id;
		if(empty($id))
			UniteFunctionsUC::throwError("Empty layout ID");
		
		$options = array();
		$options["tableName"] = GlobalsUC::$table_layouts;
		$options["where"] = "id=".$id;
		$options["errorEmpty"] = "layout with id: {$id} not found";
		
		$record = $this->db->fetchSingle($options);
		
		$this->id = $id;
		
		$this->initByRecord($record);
	}
	
	
	private function a____________GET_OPTIONS_____________(){}
		
	
	/**
	 * get global values - together with default
	 */
	public static function getGridGlobalOptions(){
		
		$settings = self::getGlobalSettingsObject();
		
		$arrValues = $settings->getArrValues();
		
		return($arrValues);
	}
	
	
	/**
	 * get grid settings options
	 */
	public static function getGridSettingsOptions($arrInitValues = array()){
		
		$objSettings = self::getGridSettingsObject();
		
		if(!empty($arrInitValues))
			$objSettings->setStoredValues($arrInitValues);
		
		$arrValues = $objSettings->getArrValues();
		
		return($arrValues);
	}
	
	/**
	 * get grid default options - without loading values
	 */
	public static function getGridDefaultOptions(){
		
		$objGlobalSettings = self::getGlobalSettingsObject(false);
						
		$arrValuesGrid = self::getGridSettingsOptions();
				
		$arrValuesGlobal = $objGlobalSettings->getArrValues();
				
		$arrMerged = array_merge($arrValuesGlobal, $arrValuesGrid);
				
		return($arrMerged);
	}
	
	
	/**
	 * get all grid options
	 */
	public function getAllGridOptions(){
		
		$this->validateInited();
		
		if(!empty($this->arrAllOptionsCache))
			return($this->arrAllOptionsCache);
		
		$globalOptions = self::getGridGlobalOptions();
		
		$layoutOptions = UniteFunctionsUC::getVal($this->gridData, "options", array());
		
		if(empty($layoutOptions) || is_array($layoutOptions) == false)
			$layoutOptions = array();
		
		$allOptions = array_merge($globalOptions, $layoutOptions);
		
		$this->arrAllOptionsCache = $allOptions;
		
		return($allOptions);
	}
	
	/**
	 * get layout extra params
	 */
	public function getExtraParams(){
		
		return($this->extraParams);
	}
		
	
	private function a______________DATA_RELATED_______________(){}
	
		
	
	
	/**
	 * modify init data, change to new way, with the containers
	 */
	protected function modifyInitLayoutData($gridData){
		
		if(empty($gridData))
			return($gridData);

		$rows = UniteFunctionsUC::getVal($gridData, "rows");
		if(empty($rows))
			return($gridData);
		
		
		foreach($rows as $keyRow => $row){
			
			$cols = UniteFunctionsUC::getVal($row, "cols");
			if(empty($cols))
				continue;
				
			$arrContainer = array();
			$arrContainer["cols"] = $cols;
			
			$row["containers"] = array($arrContainer);
			
			unset($row["cols"]);
			
			$gridData["rows"][$keyRow] = $row;
		}
				
		return($gridData);
	}
	
	
	/**
	 * generate name for empty name
	 */
	protected function generateName($title = "", $layoutType = null, $baseName = null){
		
		if(empty($title))
			$title = $this->title;
		
		UniteFunctionsUC::validateNotEmpty($title);
		
		if(empty($layoutType)){
			$this->objLayouts->validateLayoutType($this->layoutType);
			$layoutType = $this->layoutType;
		}
		
		$alias = HelperUC::convertTitleToAlias($title);
		
		if(!empty($baseName))
			$alias = $baseName."-".$alias;
		
		$isExists = $this->isLayoutExistsByName($alias, $layoutType);
			
		if($isExists == true)
			$alias .= "-".UniteFunctionsUC::getRandomString(5);
		
		return($alias);
	}
	
	
	/**
	 * init layout by record
	 */
	public function initByRecord($record){
		
		$this->title = UniteFunctionsUC::getVal($record, "title");
		$this->name = UniteFunctionsUC::getVal($record, "name");
		
		if(empty($this->id))
			$this->id = UniteFunctionsUC::getVal($record, "id");
					
		$this->ordering = UniteFunctionsUC::getVal($record, "ordering");
		$this->layoutType = UniteFunctionsUC::getVal($record, "layout_type");
		
		if(empty($this->name))
			$this->name = $this->generateName(null, $this->layoutType);
		
		$this->objLayoutType = UniteCreatorAddonType::getAddonTypeObject($this->layoutType, true);
		
		$this->isTemplate = $this->objLayoutType->isTemplate;
		
		$data = UniteFunctionsUC::getVal($record, "layout_data");
		$data = UniteFunctionsUC::maybeUnserialize($data);
		
		$this->record = $record;
		$this->data = $data;
		
		$params = UniteFunctionsUC::getVal($record, "params");
		if(!empty($params))
			$this->params = UniteFunctionsUC::jsonDecode($params);
		
		if(empty($this->params))
			$this->params = array();
			
		$gridData = UniteFunctionsUC::getVal($data, "grid_data");
		
		$gridData = $this->modifyInitLayoutData($gridData);
		
	
		$this->gridData = $gridData;
		
		
		//dmp("print layout"); dmp($this->gridData);exit();
		
	}
	
	
	/**
	 * get arr addon
	 */
	private function getAddonObject($name, $addontype = null){
				
		if(empty($addontype))
			$addontype = $this->addonType;
		
			
		$cacheName = $name;
		if(!empty($addontype))
			$cacheName = $name."_".$addontype;
		
		
		//take from cache
		if(isset(self::$arrCacheAddons[$cacheName]))
			return(self::$arrCacheAddons[$cacheName]);
				
		//init the obj - take from db
		try{
			
			$addon = new UniteCreatorAddon();
			
			if(empty($addontype))
				$addon->initByName($name);
			else 
				$addon->initByAlias($name, $addontype);
			
			self::$arrCacheAddons[$cacheName] = $addon;
	
			return($addon);
	
		}catch(Exception $e){
			return(null);
		}
	
	}
	
	
	/**
	 * get extra data from addon like title
	 */
	private function getAddonExtraData($name, $addon = null, $addontype=""){
	
		if(empty($addon))
			$addon = $this->getAddonObject($name, $addontype);
		
		
		if(empty($addon))
			return(null);
	
		$arrExtraData = array();
		$arrExtraData["id"] = $addon->getID();
		$arrExtraData["title"] = $addon->getTitle();
		$arrExtraData["url_icon"] = $addon->getUrlIcon();
		$arrExtraData["admin_labels"] = $addon->getAdminLabels();		
		
		return($arrExtraData);
	}
	
	
	/**
	 * modify addon data for editor
	 */
	private function modifyAddonDataForEditor($addonData){
		
		try{
			
			//$objAddon = new UniteCreatorAddon();
			//$objAddon->isHasItems()
			
			$objAddons = new UniteCreatorAddons();
			$objAddon = $objAddons->prepareAddonByData($addonData);
			
			//merge config
			$arrConfig = $objAddon->getProcessedMainParamsValues(UniteCreatorParamsProcessor::PROCESS_TYPE_CONFIG);
			
			$origConfig = UniteFunctionsUC::getVal($addonData, "config");
			if(empty($origConfig))
				$origConfig = array();
			
			$addonData["config"] = array_merge($origConfig, $arrConfig);
			
			if($objAddon->isHasItems()){
				$arrItems = $objAddon->getProcessedItemsData(UniteCreatorParamsProcessor::PROCESS_TYPE_CONFIG, false);
				$addonData["items"] = $arrItems;
			}
			
			return($addonData);
			
		}catch(Exception $e){
			
			return($addonData);
		}
		
	}
	
	/**
	 * modify image for editor, add url from id if set up
	 */
	protected function createGridDataFull_modifySettingsImage($settings, $key){
		
		$value = UniteFunctionsUC::getVal($settings, $key);
		if(empty($value))
			return($settings);
		
		if(is_numeric($value) == false)
			return($settings);

		//url is ID
		$urlImage = UniteProviderFunctionsUC::getImageUrlFromImageID($value);
		if(empty($urlImage))
			return($settings);
		
		//the image is ok		
		$settings[$key] = $urlImage;	//image url, replace the initial id
		$settings[$key."_imageid"] = $value;	//image id
		
		
		return($settings);
	}
	
	
	/**
	 * modify shape devider, and remove all occuranceies of shapes if old way selected
	 */
	private function createGridDataFull_modifySettingsShapes($settings){
		
		if(empty($settings))
			return($settings);
		
		$arr = array("top", "bottom");
				
		foreach($arr as $pos){
			
			if(array_key_exists("enable_shape_devider_{$pos}", $settings) == false)
				continue;
				
			if($settings["enable_shape_devider_{$pos}"] === true)
				continue;
			
			unset($settings["enable_shape_devider_{$pos}"]);
				
			foreach($settings as $key=>$value)
				if(strpos($key, "shape_devider_{$pos}_") === 0)
					unset($settings[$key]);
			
		}
		
		
		return($settings);
	}
	
	
	/**
	 * modify settings - get bg addons output data
	 */
	protected function createGridDataFull_modifySettingsBGAddons($settings, $addParams){
		
		$settingsKey = "bg_addon_single";
		$bgAddonName = UniteFunctionsUC::getVal($settings, $settingsKey);
		
		if(empty($bgAddonName))
			return($settings);	
	
		$addonData = UniteFunctionsUC::getVal($settings,  $settingsKey."_data");
		if(empty($addonData)){
		    
		    $addonData = array();
		    $addonData["name"] = $bgAddonName;
		    $addonData["addontype"] = GlobalsUC::ADDON_TYPE_BGADDON;
		    
		}
		
		$addonData = $this->createGridDataFull_modifyAddonData($addonData, $addParams);
		$settings[$settingsKey."_data"] = $addonData;
		
		return($settings);
	}
	
	
	/**
	 * modify settings
	 */
	protected function createGridDataFull_modifySettings($settings, $name,  $addParams = null){
		
		$settings = $this->createGridDataFull_modifySettingsImage($settings, "bg_image_url");
		
		$settings = $this->createGridDataFull_modifySettingsShapes($settings);
		
		$settings = $this->createGridDataFull_modifySettingsBGAddons($settings, $addParams);
		
		
		return($settings);		
	}
	
	
	/**
	 * modify addon data for editor get
	 */
	private function createGridDataFull_modifyAddonData($addonData, $addParams = null){
		
		if(empty($addonData))
			return($addonData);
		
		$addonData = $this->modifyAddonDataForEditor($addonData);
		
			
		$addonName = UniteFunctionsUC::getVal($addonData, "name");
		$addonType = UniteFunctionsUC::getVal($addonData, "addontype");
		
		if(empty($addonName))
			return($addonData);
		
		$addonExtraData = $this->getAddonExtraData($addonName, null, $addonType);
		
		if(empty($addonExtraData)){
			$addonExtraData = array();
			$addonExtraData["missing"] = true;
		}
				
		$addonData["extra"] = $addonExtraData;
		
		//add addon content if needed
		$isAddContent = UniteFunctionsUC::getVal($addParams, "add_addon_content");
		$isAddContent = UniteFunctionsUC::strToBool($isAddContent);
		
		if($isAddContent == true){
			
			$addons = new UniteCreatorAddons();
			$addonData["output"] = $addons->getLayoutAddonOutputData($addonData);
		}
		
		return($addonData);
	}
	
	
	/**
	 * create grid data full
	 * complete the addon with it's data
	 */
	private function createGridDataFull($addAddonContent = false){
		
		if(!empty($this->gridDataFull))
			return($this->gridDataFull);
					
		$addParams = array();
		$addParams["add_addon_content"] = $addAddonContent;
		
		$this->gridDataFull = $this->mapModifyLayoutDataAddons($this->gridData, "createGridDataFull_modifyAddonData",  $addParams);
		$this->gridDataFull = $this->mapModifyLayoutDataSettings($this->gridDataFull, array($this, "createGridDataFull_modifySettings"), $addParams);
		
		
		return($this->gridDataFull);
	}
	
	
	
	/**
	 * modify grid data for save
	 */
	public function modifyAddonDataForSave($data){
		
		$name = UniteFunctionsUC::getVal($data, "name");
		$addonType = UniteFunctionsUC::getVal($data, "addontype");
		
		try{
			
			unset($data["url_icon"]);
			unset($data["title"]);
			unset($data["extra"]);
			unset($data["output"]);
			
			$config = UniteFunctionsUC::getVal($data, "config");
			$items = UniteFunctionsUC::getVal($data, "items");
			
			//init addon
			$addon = new UniteCreatorAddon();
			if(empty($addonType))
				$addon->initByName($name);
			else
				$addon->initByAlias($name, $addonType);
				
			$addon->setParamsValues($config);
			
			if(!empty($items))
				$addon->setArrItems($items);
			
			
			if(!empty($config)){
				
				$arrImages = $addon->getProcessedMainParamsImages();
				if(!empty($arrImages)){
					$arrImages = $addon->modifyDataConvertToUrlAssets($arrImages);
					$data["config"] = array_merge($config, $arrImages);
				}
			}
			
			if(!empty($items) && is_array($items)){
				
				$arrItemsImages = $addon->getProcessedItemsData(UniteCreatorParamsProcessor::PROCESS_TYPE_SAVE, false, "uc_image");
				
				foreach($arrItemsImages as $key=>$itemImage){
					$itemImage = $addon->modifyDataConvertToUrlAssets($itemImage);
					
					if(!empty($itemImage))
						$items[$key] = array_merge($items[$key],$itemImage);
				}
			
				$data["items"] =  $items;
			}
	
		}catch(Exception $e){
			
			return($data);
		}
		
		
		return($data);
	}
	
	
	/**
	 * sanitize layout data for save
	 */
	private function prepareDataForSave($gridData){
		
		$data = array();
		
		//decode the data:
		if(is_string($gridData)){
			$gridData = UniteFunctionsUC::decodeContent($gridData);
			
			$gridData = $this->modifyGridDataForSave($gridData);
						
			$data["grid_data"] = $gridData;
		}else{
			
			$data["grid_data"] = $gridData;
		}
		
		
		$data = serialize($data);
	
		return($data);
	}
	
	private function a______MAPPING_______(){}
	
	/**
	 * modify columns settings
	 */
	private function mapModifyLayoutDataSettings_columns($parent, $modifyFunc, $addParams = null){
				
			$cols = UniteFunctionsUC::getVal($parent, "cols");
			if(empty($cols))
				return($parent);
			
			foreach($cols as $colIndex=>$col){
				
				$colSettings = UniteFunctionsUC::getVal($col, "settings");
				
				if(!empty($colSettings)){
					$parent["cols"][$colIndex]["settings"] = call_user_func($modifyFunc, $colSettings, "col", $addParams);
					//$parent["cols"][$colIndex] = $this->checkSettingsAddData($parent["cols"][$colIndex], "settings");
				}
				
				//modify addons settings
				
				$addonData = UniteFunctionsUC::getVal($col, "addon_data");
				if(empty($addonData))
					continue;
				
				foreach($addonData as $addonIndex=>$addon){
					
					$addonOptions = UniteFunctionsUC::getVal($addon, "options");
					if(empty($addonOptions))
						continue;
					
					$parent["cols"][$colIndex]["addon_data"][$addonIndex]["options"] = call_user_func($modifyFunc, $addonOptions, "addon", $addParams);
					//$parent["cols"][$colIndex]["addon_data"][$addonIndex] = $this->checkSettingsAddData($parent["cols"][$colIndex]["addon_data"][$addonIndex], "options");
				}
				
			}
			
			return($parent);
	}
	
	/**
	 * add settings adddata after modify
	 */
	private function checkSettingsAddData($arrElement, $keyOptions){
		
		/*
		$settings = UniteFunctionsUC::getVal($arrElement, $keyOptions);
		
		if(!isset($settings[self::KEY_SETTINGS_ADDDATA]))
			return($arrElement);
		
		$addData = $settings[self::KEY_SETTINGS_ADDDATA];
		unset($arrElement[$keyOptions][self::KEY_SETTINGS_ADDDATA]);
		
		if(is_array($addData) == false)
			return($arrElement);
			
		if(empty($addData))
			return($arrElement);
		
		$arrElement = array_merge($arrElement, $addData);
		*/
		
		return($arrElement);
	}
	
	
	/**
	 * modify layout data settings objects
	 */
	public function mapModifyLayoutDataSettings($arrData, $modifyFunc, $addParams = null){

		//modify grid options
		$gridSettings = UniteFunctionsUC::getVal($arrData, "options");
				
		if(!empty($gridSettings)){
			$arrData["options"] = call_user_func($modifyFunc, $gridSettings, "grid", $addParams);
			//$arrData = $this->checkSettingsAddData($arrData, "options");
		}
		
		//modify rows options
		$rows = UniteFunctionsUC::getVal($arrData, "rows");
		
		if(empty($rows))
			return(false);
			
		foreach($rows as $rowIndex => $row){
			
			$rowSettings = UniteFunctionsUC::getVal($row, "settings");
			if(!empty($rowSettings)){
				$arrData["rows"][$rowIndex]["settings"] = call_user_func($modifyFunc, $rowSettings, "row", $addParams);
				//$arrData["rows"][$rowIndex] = $this->checkSettingsAddData($arrData["rows"][$rowIndex], "settings");
			}
			
			$arrContainers = UniteFunctionsUC::getVal($row, "containers");
			
			//modify containers settings
			if(!empty($arrContainers)){
				
				foreach($arrContainers as $containerIndex=>$container){
					
					$containerSettings = UniteFunctionsUC::getVal($container, "settings");
					
					if(!empty($containerSettings)){
						$arrContainer = $arrData["rows"][$rowIndex]["containers"][$containerIndex];
						
						$arrContainer["settings"] = call_user_func($modifyFunc, $containerSettings, "container", $addParams);
						//$arrContainer = $this->checkSettingsAddData($arrContainer, "settings");
						
						$arrData["rows"][$rowIndex]["containers"][$containerIndex] = $arrContainer;
					}
					
					$arrData["rows"][$rowIndex]["containers"][$containerIndex] = $this->mapModifyLayoutDataSettings_columns($container, $modifyFunc, $addParams);
				}
				
			}else{	//modify if only 1 container
				
				//modify columns settings
				$arrData["rows"][$rowIndex] = $this->mapModifyLayoutDataSettings_columns($row, $modifyFunc, $addParams);
			}
			
		}
		
				
		return($arrData);
	}
	
	/**
	 * modify columns
	 */
	private function mapModifyLayoutDataAddons_columns($arrParent, $modifyFunc, $addParams = null){
			
			$cols = UniteFunctionsUC::getVal($arrParent, "cols");
			
			if(empty($cols))
				return($arrParent);
			
			foreach($cols as $keyCol=>$col){
				$addonData = UniteFunctionsUC::getVal($col, "addon_data");
								
				if(isset($addonData["config"]))
					$addonData = array($addonData);
	
				if(empty($addonData))
					$addonData = array();
								
				foreach($addonData as $keyAddon=>$addon){
								
					//modify addon
					if(is_array($modifyFunc))
						$addon = call_user_func($modifyFunc,$addon);
					else
						$addon = call_user_func(array($this, $modifyFunc),$addon, $addParams);
					
					if(!empty($addon))
						$addonData[$keyAddon] = $addon;
					else
						unset($addonData[$keyAddon]);
					
				}
	
				$col["addon_data"] = $addonData;
	
				$cols[$keyCol] = $col;
			}
	
			$arrParent["cols"] = $cols;
 		
		
		return($arrParent);
	}
	
	
	/**
	 * map layout data, call modify function to modify each addon
	 */
	public function mapModifyLayoutDataAddons($arrData, $modifyFunc, $addParams = null){
		
		$rows = UniteFunctionsUC::getVal($arrData, "rows");
		if(empty($rows))
			return($arrData);
		
		foreach($rows as $keyRow=>$row){
			
			//modify by containers
			$containers = UniteFunctionsUC::getVal($row, "containers");
			if(!empty($containers)){
				
				foreach($containers as $keyContainer => $container)
					$row["containers"][$keyContainer] = $this->mapModifyLayoutDataAddons_columns($container, $modifyFunc, $addParams);
			}else{
				$row = $this->mapModifyLayoutDataAddons_columns($row, $modifyFunc, $addParams);
			}
			
			$rows[$keyRow] = $row;
		}
		
		$arrData["rows"] = $rows;
				
		return($arrData);
	}
	
	/**
	 * map layout data walk through all the layout array using recursion
	 */
	public function mapModifyLayoutDataAll($arrData, $modifyFunc){
		
		if(is_array($arrData)){
			
			$arrData = call_user_func($modifyFunc, $arrData);
			
			foreach($arrData as $index=>$item){
				if(is_array($item)){
					$item = call_user_func($modifyFunc, $item);
					$arrData[$index] = $this->mapModifyLayoutDataAll($item, $modifyFunc);
				}
					
			}
			
		}
		
		return($arrData);
	}
	
	/**
	 * map layout params, call some function, not mapping actually but need to keep name consistentcy
	 */
	public function modifyParams($modifyFunc){
		
		$this->params = call_user_func($modifyFunc, $this->params);
	}
	
	
	private function a_____________EXTERNAL_GETTERS____________(){}
	
	
	/**
	 * get grid data
	 */
	public function getGridDataForEditor($addAddonContent = false){
		
		$this->validateInited();
				
		try{
			
			if(empty($this->gridDataFull))
				$this->createGridDataFull($addAddonContent);
			
		}catch(Exception $e){
			
			HelperHtmlUC::outputException($e);
			
			return $this->gridData;
		}
		
		
		return($this->gridDataFull);
	}
	
	
	/**
	 * get grid data for front
	 */
	public function getRowsFront(){
		
		$this->validateInited();
		
		$rows = UniteFunctionsUC::getVal($this->gridData, "rows");
		
		if(empty($rows))
		    $rows = array();
		
		//UniteFunctionsUC::validateNotEmpty($rows, "Layout Rows");		
		//dmp($this->gridData); exit();
		
		return($rows);
	}
	
		
	
	/**
	 * get grid options - only those that different from default
	 */
	public function getGridOptionsDiff(){
		
		$defaultOptions = self::getGridDefaultOptions();
		
		$allOptions = $this->getAllGridOptions();
		
		$arrDiffOptions = UniteFunctionsUC::getDiffArrItems($allOptions, $defaultOptions);
		
		return($arrDiffOptions);
	}
	
	
	/**
	 * get title
	 */
	public function getTitle($specialChars = false){
		
		if($specialChars == true)
			return(htmlspecialchars($this->title));
		else
			return($this->title);
	}
	
	/**
	 * get name
	 */
	public function getName(){
		
		$this->validateInited();
		
		return($this->name);
	}
	
	/**
	 * get title
	 */
	public function getDescription($specialChars = false){
		
		$this->validateInited();

		$description = UniteFunctionsUC::getVal($this->params, "description");
		if($specialChars == true)
			$description = htmlspecialchars($description);
		
		return($description);
	}
	
	/**
	 * get layout icon
	 */
	public function getIcon(){
		
		$this->validateInited();

		$icon = UniteFunctionsUC::getVal($this->params, "page_icon");
		
		return($icon);
	}
	
	
	
	/**
	 * get page image
	 */
	public function getPreviewImage($getThumb = false){
		
		$this->validateInited();
		
		$params = $this->getPageParamsValuesWithDefaults();
		
		//$isAutoGenerate = UniteFunctionsUC::getVal($params, "auto_generate_thumb");
		//$isAutoGenerate = UniteFunctionsUC::strToBool($isAutoGenerate);

		$arrImageKeys = array("preview_image","custom_preview_image","page_image");
		
		foreach($arrImageKeys as $key){
		    $urlPreview = UniteFunctionsUC::getVal($params, $key);
		    $urlPreview = trim($urlPreview);
		    
		    if(empty($urlPreview))   
		        continue;
		    
		   if(is_numeric($urlPreview))
		         $urlPreview = UniteProviderFunctionsUC::getImageUrlFromImageID($urlPreview);
		           
		   $urlPreview = HelperUC::URLtoFull($urlPreview);
		   
		   return($urlPreview);
		}
	
		
		return($urlPreview);
	}
	
	
	/**
	 * get preview image filepath
	 */
	public function getPreviewImageFilepath(){
		
		$urlPreview = $this->getPreviewImage();
		if(empty($urlPreview))
			return(null);
		
		$pathImage = HelperUC::urlToPath($urlPreview);
		if(empty($pathImage))
			return(null);
		
		return($pathImage);
	}
	
	/**
	 * get layout ID
	 */
	public function getID(){
		
		$this->validateInited();
		
		return($this->id);
	}
    
	/**
	 * get parent ID
	 */
	public function getParentID(){
		
		dmp("getParentID - function for override");
		exit();
	}

	
	/**
	 * get layout type
	 */
	public function getLayoutType(){
		
		return($this->layoutType);
	}
	
	/**
	 * get layout type object
	 */
	public function getObjLayoutType(){
		
		if(!empty($this->objLayoutType))
			return($this->objLayoutType);
		
		if($this->isInited() == true)
			UniteFunctionsUC::throwError("The layout type should be inited");
		
		$this->objLayoutType = UniteCreatorAddonType::getAddonTypeObject(GlobalsUC::ADDON_TYPE_REGULAR_LAYOUT, true);
		
		return($this->objLayoutType);
	}
	
	
	/**
	 * get category name for export
	 */
	public function getCatNameForExport(){
		
		$arrCat = $this->getCategory();
		$catName = UniteFunctionsUC::getVal($arrCat, "name");
		if(empty($catName))
			return(false);
		
		$objLayoutType = $this->getObjLayoutType();
		$typeName = $objLayoutType->typeNameDistinct;
		
		$prefix = "";
		if($typeName != GlobalsUC::ADDON_TYPE_REGULAR_LAYOUT)
			$prefix = self::EXPORT_CAT_TYPE_PREFIX.$typeName."___";
		
		if(!empty($prefix))
			$catName = $prefix.$catName;
		
		return($catName);
	}
	
     /**
	 * get layout category info
	 */
    public function getCategory(){
		
		$this->validateInited();
        $category_info = array("layoutid" => $this->getID());
        $objCats = new UniteCreatorCategories();
            
       	$catID = UniteFunctionsUC::getVal($this->record, "catid");
        
          if(empty($catID))
		  {
		    $category_info["name"] = "Uncategorized";
		    $category_info["id"] = 0;
		    
		  } else {
                $cat = $objCats->getCat($catID);
                $category_info["name"] = $cat["title"];
			 	$category_info["id"] = $cat["id"];
            }
            
		return $category_info;
	}
	
	/**
	 * get category name
	 */
	public function getCategoryName(){
		
		$objCat = $this->getCategory();
		$catName = $objCat["name"];
		
		return($catName);
	}
	
	
	/**
	 * get record
	 */
	public function getRecord(){
		
		return($this->record);
	}
	
	
	/**
	 * get shortcode
	 */
	public function getShortcode($shortcodeName = ""){
		
		$title = $this->getTitle(true);
		$id = $this->id;
		
		if(empty($shortcodeName))
			$shortcodeName = GlobalsUC::$layoutShortcodeName;
		
		$shortcode = $shortcodeName." id={$id} title=\"{$title}\"";
		
		$shortcode = UniteProviderFunctionsUC::wrapShortcode($shortcode);
		
		return($shortcode);
	}
	
	
	/**
	 * get layout addon type
	 */
	public function getAddonType(){
		
		return($this->addonType);
	}
	
	
	/**
	 * get raw layout data
	 */
	public function getRawLayoutData(){
		$this->validateInited();
		
		$strLayoutData = $this->record["layout_data"];
		
		return($strLayoutData);
	}
	
	
	/**
	 * get short data for catalog etc
	 */
	public function getShortData(){
		
		$this->validateInited();
		
		$data = array();
		
		$data["title"] = $this->getTitle();
		$data["name"] = $this->getName();
		$data["description"] = $this->getDescription();
		$data["url_icon"] = $this->getIcon();
		$data["preview"] = $this->getPreviewImage();
		$data["id"] = $this->getID();
		$data["is_active"] = true;		//no setting in layout yet
		
		return($data);
		
	}
	
	/**
	 * check if layout exists by title
	 */
	protected function isLayoutExistsByTitle($title, $layoutType){
		
		$title = $this->db->escape($title);
		
		$sqlType = $this->db->getSqlAddonType($layoutType, "layout_type");
				
		$response = $this->db->fetch(GlobalsUC::$table_layouts, "title='{$title}' and {$sqlType}");
		
		$isExists = !empty($response);
		
		return($isExists);
	}
	
	
	/**
	 * check if layout exists by title
	 */
	protected function isLayoutExistsByName($name, $layoutType){
		
		$title = $this->db->escape($name);
		
		$sqlType = $this->db->getSqlAddonType($layoutType, "layout_type");
		
		$where = "name='{$name}' and {$sqlType}";
		
		if(!empty($this->id))
			$where .= " and id <> ".$this->id;
					
		$response = $this->db->fetch(GlobalsUC::$table_layouts, $where);
		
		$isExists = !empty($response);
		
		return($isExists);
	}
	
	
	/**
	 * get new layout title
	 */
	public function getNewLayoutTitle(){
		
		$titleBase = $this->objLayoutType->textSingle;
						
		$counter = 1;
		
		do{
			$title = $titleBase.$counter;			
			$found = $this->isLayoutExistsByTitle($title, $this->layoutType);
			$counter++;
		}while($found == true);
		
		return($title);
	}
	
	
	
	/**
	 * collect shapes from config
	 */
	private function collectAddonSpecialAddons_params($paramsShapes, $config, $addonType){
		
		foreach($paramsShapes as $param){
			$paramName = UniteFunctionsUC::getVal($param, "name");
			$shapeName = UniteFunctionsUC::getVal($config, $paramName);
			if(empty($shapeName))
				continue;
			
			$shapeAddonName = $shapeName."_".$addonType;
			$this->arrAddonNames[$shapeAddonName] = null;
		}
		
	}
	
	/**
	 * get shape params from addon
	 */
	private function getParamsShapesFromAddon($objAddon, $isItems = false){
		
		if($isItems == false)
			$paramsShapes = $objAddon->getParams(UniteCreatorDialogParam::PARAM_SHAPE);
		else
			$paramsShapes = $objAddon->getParamsItems(UniteCreatorDialogParam::PARAM_SHAPE);
		
			
		if(empty($paramsShapes))
			$paramsShapes = array();
			
		if($isItems == false)
			$paramsAddonPickers = $objAddon->getParams(UniteCreatorDialogParam::PARAM_ADDONPICKER);
		else
			$paramsAddonPickers = $objAddon->getParamsItems(UniteCreatorDialogParam::PARAM_ADDONPICKER);
		
			
		if(empty($paramsAddonPickers))
			return($paramsShapes);
			
		foreach($paramsAddonPickers as $param){
			$addonType = UniteFunctionsUC::getVal($param, "addon_type");
			if($addonType == GlobalsUC::ADDON_TYPE_SHAPES)
				$paramsShapes[] = $param;
		}
		
		return($paramsShapes);
	}
	
	
	/**
	 * collect special addons of some addon, like shapes
	 */
	private function collectAddonSpecialAddons($addonName, $addonData){
		
		$objAddon = new UniteCreatorAddon();
		$objAddon->initByName($addonName);
		
		//collect from config
		$paramsShapes = $this->getParamsShapesFromAddon($objAddon);
		if(!empty($paramsShapes)){
			$config = UniteFunctionsUC::getVal($addonData, "config");
			$this->collectAddonSpecialAddons_params($paramsShapes, $config, GlobalsUC::ADDON_TYPE_SHAPES);
		}
		
		
		//collect from items
		$hasItems = $objAddon->isHasItems();
		if($hasItems == false)
			return(false);
		
		$paramsItemsShapes = $this->getParamsShapesFromAddon($objAddon);
		if(empty($paramsItemsShapes))
			return(false);
			
		$arrItems = UniteFunctionsUC::getVal($addonData, "items");
		if(empty($arrItems) || is_array($arrItems) == false)
			return(false);
		
		foreach($arrItems as $item)
			$this->collectAddonSpecialAddons_params($paramsItemsShapes, $item, GlobalsUC::ADDON_TYPE_SHAPES);
		
		
		
	}
	
	
	/**
	 * collect addon names
	 */
	private function modifyAddons_collectAddonNames($addonData){
				
		$name = UniteFunctionsUC::getVal($addonData, "name");
		$this->arrAddonNames[$name] = null;
		
		$this->collectAddonSpecialAddons($name, $addonData);
		
		return(null);
	}
	
	
	/**
	 * get row settigns combined with grid options
	 */
	public function getRowSettingsCombined($row){
		
		$settings = UniteFunctionsUC::getVal($row, "settings");

		$allOptions = $this->getAllGridOptions();
		
		
		//add option values
		if(empty($allOptions))
			return($settings);
		
		if(empty($settings))
			return($allOptions);
		
		//combine both
					
		foreach($settings as $key=>$value){
			
			if($value !== "")
				continue;
			
			$globalValue = UniteFunctionsUC::getVal($allOptions, $key);
			if($globalValue !== "")
				$settings[$key] = $globalValue;
		}
		
		//get the rest options
		foreach($allOptions as $key=>$value){
			
			$localValue = UniteFunctionsUC::getVal($settings, $key);
			if($localValue !== "")
				$value = $localValue;
			
			$settings[$key] = $value;
		}
		
		
		return($settings);
	}
	
	/**
	 * get info text
	 */
	public function getInfoText(){
				
		$textEmpty = esc_html__("empty layout", "unlimited-elements-for-elementor");
		
		
		if(empty($this->id))
			return($textEmpty);
		
		$rows = UniteFunctionsUC::getVal($this->gridData, "rows");
				
		if(empty($rows))
			return($textEmpty);
		
		if(is_array($rows) == false)
			return($textEmpty);
		
		$numRows = count($rows);
		
		$text = esc_html__("layout with","unlimited-elements-for-elementor")." ". $numRows. " " . __("sections", "unlimited-elements-for-elementor");
		
		return($text);
	}
	
	/**
	 * get rows
	 */
	public function getRows(){
		
		$this->validateInited();
		
		$rows = UniteFunctionsUC::getVal($this->gridData, "rows");
		if(empty($rows))
			$rows = array();
		
		return($rows);
	}
	
	/**
	 * get params
	 */
	public function getParams(){
		
		return($this->params);
	}
	
	
	/**
	 * get param
	 */
	public function getParam($paramName){
		
		$value = UniteFunctionsUC::getVal($this->params, $paramName);
		
		return($value);
	}
	
	
	/**
	 * get edit layout url
	 */
	public function getUrlEditPost(){
		
		dmp("getUrlEditPost function for override");
		exit();
	}
	
	
	/**
	 * get edit layout url
	 */
	public function getUrlViewPost(){
		
		dmp("getUrlViewPost function for override");
		exit();
	}
	
	
	private function a_______________EXPORT_RELATED_____________(){}
	
	
	/**
	 * map through all layout settings and modify if needed
	 */
	public function modifyLayoutElementsSettings($modifyFunc){
		$this->validateInited();
		if(is_array($modifyFunc) == false)
			UniteFunctionsUC::throwError("Wrong modify func");
		
		$this->gridData = $this->mapModifyLayoutDataSettings($this->gridData, $modifyFunc);
	}

	
	/**
	 * modify grid data by some function
	 */
	public function modifyGridDataAddons($modifyFunc){
		
		$this->validateInited();
		if(is_array($modifyFunc) == false)
			UniteFunctionsUC::throwError("Wrong modify func");
		
		$this->gridData = $this->mapModifyLayoutDataAddons($this->gridData, $modifyFunc);
		
	}

	/**
	* get background addon name from settings
	 */
	private function getBGAddonNameFromSettings($settings){
		
		$enableBackground = UniteFunctionsUC::getVal($settings, "bg_enable");
		$enableBackground = UniteFunctionsUC::strToBool($enableBackground);
		
		if($enableBackground == false)
		return(null);
		
		$bgAddonKey = "bg_addon_single";
		$bgAddonEnableKey = $bgAddonKey."_enable";
		
		$enableBGAddon = UniteFunctionsUC::getVal($settings, $bgAddonEnableKey);
		$enableBGAddon = UniteFunctionsUC::strToBool($enableBGAddon);
		if($enableBGAddon == false)
			return(null);
		
		$bgAddonName = UniteFunctionsUC::getVal($settings, $bgAddonKey);
		if(empty($bgAddonName))
			return(null);
		
		return($bgAddonName);
	}
	
	
	/**
	 * collect special addon names like shape deviders
	 */
	public function modifySettings_collectSpecialAddonNames($settings, $elementType){
		
		$nameTop = $this->objShapes->getShapeDividerNameFromSettings($settings, "top");
		if(!empty($nameTop))
			$this->arrAddonNames[$nameTop."_".GlobalsUC::ADDON_TYPE_SHAPE_DEVIDER] = GlobalsUC::ADDON_TYPE_SHAPE_DEVIDER;
		
		$nameBottom = $this->objShapes->getShapeDividerNameFromSettings($settings, "bottom");
		if(!empty($nameBottom))
			$this->arrAddonNames[$nameBottom."_".GlobalsUC::ADDON_TYPE_SHAPE_DEVIDER] = GlobalsUC::ADDON_TYPE_SHAPE_DEVIDER;
		
		
		$bgAddonName = $this->getBGAddonNameFromSettings($settings);
		if(!empty($bgAddonName))
			$this->arrAddonNames[$bgAddonName] = GlobalsUC::ADDON_TYPE_BGADDON;
		
	}
	
	
	/**
	 * get error message prefix
	 */
	private function getErrorMessagePrefix(){
		
		$title = $this->getTitle(true);
		
		$prefix = esc_html__("Error in ","unlimited-elements-for-elementor").$title." :";
		
		return($prefix);
	}
	
	
	/**
	 * get all layout addons without content
	 */
	public function getArrAddons(){
		
		$this->validateInited();
		
		$this->arrAddonNames = array();
		
		//get all regular addons, and special addons related to regular like shapes
		$this->mapModifyLayoutDataAddons($this->gridData, "modifyAddons_collectAddonNames");
		
		//get all special addons from design
		$this->mapModifyLayoutDataSettings($this->gridData, array($this, "modifySettings_collectSpecialAddonNames"));
		
		$arrAddons = array();
				
		foreach($this->arrAddonNames as $name=>$type){
			
			try{
				
				$objAddon = new UniteCreatorAddon();
				
				switch($type){
					case GlobalsUC::ADDON_TYPE_BGADDON:
						$objAddon->initByAlias($name, $type);					
					break;
					default:
						$objAddon->initByMixed($name, $this->addonType);
					break;
				}
				
				$arrAddons[] = $objAddon;
				
			}catch(Exception $e){
				
				if($type != GlobalsUC::ADDON_TYPE_SHAPE_DEVIDER){
					$message = $this->getErrorMessagePrefix();
					$message .= $e->getMessage();
					
					dmp($message);
					
					UniteFunctionsUC::throwError($message);
				}
				
				//$message = $this->getErrorMessagePrefix();
				//$message .= $e->getMessage();
				//dmp($message);
									
				//dmp("error!");exit();
				
			}
			
		}
		
		
		return($arrAddons);
	}
	
	/**
	 * clean layout data before save
	 * get array of data each time
	 */
	public function cleanLayoutDataBeforeExport($arrData){
		
		if(is_array($arrData) == false)
			return($arrData);
		
		$arrDataNew = array();
		foreach($arrData as $key=>$item){
			
			if(strpos($key, "_unite_selected_text") !== false)
				continue;
						
			$arrDataNew[$key] = $item;
		}
		
		
		return($arrDataNew);
	}
	
	/**
	 * clean layout settings before save or export from extra data
	 */
	public function cleanLayoutSettingsBeforeExport(){
		
		$cleanFunc = array($this, "cleanLayoutDataBeforeExport");
		
		$this->gridData = $this->mapModifyLayoutDataAll($this->gridData, $cleanFunc);
		
		$this->modifyParams($cleanFunc);
	}
	
	
	/**
	 * get layout record for export
	 */
	public function getRecordForExport(){
		
		$this->validateInited();
		
		$record = array();
		$record["title"] = $this->title;
		$record["name"] = $this->name;
		$record["type"] = $this->layoutType;
				
		
		//add category name
		$catName = $this->getCategoryName();
		if(!empty($catName))
			$record["catname"] = $catName;
		
		
		$gridData = $this->modifyGridDataForSave($this->gridData);
		
		$record["params"] = $this->modifyParamsForSave($this->params);
		
		
		$this->data["grid_data"] = $gridData;
		$layoutData = serialize($this->data);
		$record["layout_data"] = $layoutData;
		
		
		return($record);
	}
	
	
	private function a________________MODIFY_FOR_SAVE____________(){}
	
	/**
	 * modify grid containers, convert to cols if only 1
	 * to preserve backward compatability
	 */
	public function modifyGridDataForSave_containers($gridData){
		
		$arrRows = UniteFunctionsUC::getVal($gridData, "rows");
		
		if(empty($arrRows))
			return($gridData);
		
		foreach($arrRows as $index=>$row){
			
			$arrContainers = UniteFunctionsUC::getVal($row, "containers");
			if(empty($arrContainers))
				continue;
				
			if(count($arrContainers) > 1)
				continue;
			
			$container = $arrContainers[0];
						
			$settings = UniteFunctionsUC::getVal($container, "settings");
			if(!empty($settings))
				continue;
			
			$cols = UniteFunctionsUC::getVal($container, "cols");
			$row["cols"] = $cols;
			unset($row["containers"]);
			
			$arrRows[$index] = $row;
		}
		
		$gridData["rows"] = $arrRows;
				
		return($gridData);
	}
	
	
	/**
	 * modify layout data for save, check bg image and background addons
	 */
	public function modifyLayoutDataForSave($arrSettings, $type){
		
		if(isset($arrSettings["bg_image_url_url"]))
			unset($arrSettings["bg_image_url_url"]);
		
		//check background addons
		$bgAddonSingleData = UniteFunctionsUC::getVal($arrSettings, "bg_addon_single_data");
		
		if(!empty($bgAddonSingleData))
			$arrSettings["bg_addon_single_data"] = $this->modifyAddonDataForSave($bgAddonSingleData);
		
			
		return($arrSettings);
	}
	
	
	/**
	 * modify grid data for save
	 */
	public function modifyGridDataForSave($gridData){
		
		$gridData = $this->mapModifyLayoutDataSettings($gridData, array($this, "modifyLayoutDataForSave"));
				
		$gridData = $this->mapModifyLayoutDataAddons($gridData, "modifyAddonDataForSave");
		
		$gridData = $this->modifyGridDataForSave_containers($gridData);
		
		return($gridData);
	}
	
	/**
	 * 
	 * modify params images for save
	 */
	protected function modifyParamsImagesForSave($params, $arrImageNames){
		
		foreach($arrImageNames as $name){
			
			$unsetName = $name."_url";
			
			if(isset($params[$unsetName]))
				unset($params[$unsetName]);
		}
		
		return($params);
	}
	
	
	/**
	* modify layout params for save
	 */
	public function modifyParamsForSave($params){
		
		$arrImagesNames = array("preview_image", "custom_preview_image",  "page_image");
		
		$params = $this->modifyParamsImagesForSave($params, $arrImagesNames);
		
		
		return($params);
	}
	
	
	private function a_____________EXTERNAL_SETTERS_____________(){}
	
	
	/**
	 * update layout in db
	 */
	public function createLayoutInDB($arrInsert, $arrParams = array()){
		
		$objLayouts = new UniteCreatorLayouts();
		
		$maxOrder = $objLayouts->getMaxOrder();
		$arrInsert["ordering"] = $maxOrder+1;
		
		if(!isset($arrInsert["catid"]))
			$arrInsert["catid"] = '0';
		
		if(!isset($arrInsert["parent_id"]))
			$arrInsert["parent_id"] = '0';
		
		if(!isset($arrInsert["relate_id"]))
			$arrInsert["relate_id"] = '0';
		
		if(!isset($arrInsert["params"]))
			$arrInsert["params"] = '';
			
		$id = $this->db->insert(GlobalsUC::$table_layouts, $arrInsert);
		
		return($id);
	}
	
	/**
	 * get new layout name
	 */
	protected function getNewLayoutName($title, $importParams){
		
		$name = $this->generateName($title);
		
		return($name);
	}
	
	/**
	 * create layout by some vars
	 */
	public function createSmall($title, $name, $description, $catID, $importParams = array()){
		
		$arrInsert = array();
		
		$this->validateTitle($title);
				
		$this->objLayouts->validateLayoutType($this->layoutType);
		
		//always generate name
		$name = $this->getNewLayoutName($title, $importParams);
				
		$name = HelperUC::convertTitleToAlias($name);
		
		$this->validateName($name);
		
		if(!is_numeric($catID))
			$catID = 0;
			
		if(empty($catID))
			$catID = 0;
		
		//prepare arrInsert
		$arrInsert["title"] = $title;
		$arrInsert["layout_type"] = $this->layoutType;
		$arrInsert["name"] = $name;
		$arrInsert["catid"] = $catID;
		
		$arrParams = array();
		
		$description = $this->db->escape($description);		
		$arrParams["description"] = $description;
		
		$arrInsert["params"] = json_encode($arrParams);
		
		$isNameExists = $this->isLayoutExistsByName($name, $this->layoutType);
		if($isNameExists == true){		//update
			
			$objLayoutExisting = new UniteCreatorLayout();
			$objLayoutExisting->initByName($name, $this->layoutType);
			$objLayoutExisting->updateLayoutInDB($arrInsert);
			
			$this->id = $objLayoutExisting->getID();
			
			$this->initByID($this->id);
			
		}else{
			$this->id = $this->createLayoutInDB($arrInsert, $importParams);
			$this->initByID($this->id);
		}
		
		
		return($this->id);
	}
	
	
	/**
	 * create layout from data
	 */
	public function create($data){
			
		$title = UniteFunctionsUC::getVal($data, "title");
		
		unset($data["title"]);
		unset($data["layoutid"]);
		UniteFunctionsUC::validateNotEmpty($title, HelperUC::getText("layout_title"));

		$layoutType = UniteFunctionsUC::getVal($data, "layout_type");
		$objLayoutType = UniteCreatorAddonType::getAddonTypeObject($layoutType, true);
		
		$this->objLayoutType = $objLayoutType;
		
		$pageName = UniteFunctionsUC::getVal($data, "name");
		if(!empty($pageName)){
			$pageName = HelperUC::convertTitleToAlias($pageName);
			
			$isExists = $this->isLayoutExistsByName($pageName, $layoutType);
			if($isExists)
				$pageName = null;
		}

		if(empty($pageName))
			$pageName = $this->generateName($title);
		
		
		$arrInsert = array();
		$arrInsert["title"] = $title;
		$arrInsert["name"] = $pageName;
		
		$gridData = UniteFunctionsUC::getVal($data, "grid_data");
		
		$arrInsert["layout_data"] = $this->prepareDataForSave($gridData);
		
		//put params:
		$params = UniteFunctionsUC::getVal($data, "params");
		if(!empty($params))
			$arrInsert["params"] = json_encode($params);
		
		
		//put to category
		$catID = UniteFunctionsUC::getVal($data, "catid");
		if(!empty($catID)){
			$objCategories = new UniteCreatorCategories();
			$objCategories->validateCatExist($catID);
			
			$arrInsert["catid"] = $catID;
		}
		
		
		
		//check duplicate title
		if($objLayoutType->allowDuplicateTitle == false)
			$this->validateLayoutNotExistsByTitle($title, $layoutType);
		
		if(!empty($layoutType))
			$arrInsert["layout_type"] = $layoutType;
		
		$id = $this->createLayoutInDB($arrInsert);
		
		$arrResponse = array();
		$arrResponse["id"] = $id;
		$arrResponse["name"] = $pageName;
		
		return($arrResponse);
	}
	
	
	/**
	 * update layout in db
	 */
	public function updateLayoutInDB($arrUpdate){
		
		$this->validateInited();
			
		unset($arrUpdate["import_title"]);
		
		//check layout name
		if(isset($arrUpdate["name"]))
			$this->validateName($arrUpdate["name"]);
		
		$where = "id={$this->id}";
		
		$this->db->update(GlobalsUC::$table_layouts, $arrUpdate, $where);
	}
	
	
	/**
	 * update layout category
	 */
	public function updateCategory($catID){
		
		$this->validateInited();
		$catID = (int)$catID;
		
		$arrUpdate = array();
		$arrUpdate['catid'] = $catID;
		
		$this->updateLayoutInDB($arrUpdate);
	}
	
	
	/**
	 * update title
	 */
	public function updateTitle($title, $name = null){
		
		$this->validateInited();
		
		$this->validateTitle($title);
		
		$arrUpdate = array();
		$arrUpdate["title"] = $title;
		if($name !== null){
			$this->validateName($name);
			$arrUpdate["name"] = $name;
		}
		
		$this->updateLayoutInDB($arrUpdate);
	}
	
	
	/**
	 * 
	 * update saved internal params in db
	 */
	public function updateInternalParamsInDB(){
		
		$jsonParams = json_encode($this->params);
		
		$arrUpdate = array();
		$arrUpdate["params"] = $jsonParams;
		
		$this->updateLayoutInDB($arrUpdate);
	}
	
	
	/**
	 * update param
	 */
	public function updateParam($paramName, $paramValue){
		
		$this->validateInited();
		
		$this->params[$paramName] = $paramValue;
		
		$this->updateInternalParamsInDB();
	}
	
	
	/**
	 * update layout params
	 */
	public function updateParams($arrParams, $isRewrite = false){
		
		$this->validateInited();
		if(empty($arrParams))
			return(false);
		
		if(is_array($arrParams) == false)
			return(false);
		
		if($isRewrite == true)
			$this->params = $arrParams;
		else
			$this->params = array_merge($this->params, $arrParams);
		
		$this->updateInternalParamsInDB();
	}
	
	
	/**
	 * update layout
	 */
	public function update($data){
		
		$this->validateInited();
	
		$title = UniteFunctionsUC::getVal($data, "title");
		$this->validateTitle($title);
		
		$gridData = UniteFunctionsUC::getVal($data, "grid_data");
				
		$arrUpdate = array();
		$arrUpdate["title"] = $title;
		$arrUpdate["layout_data"] = $this->prepareDataForSave($gridData);
		
		if(isset($data["name"])){
			$pageName = $data["name"];
			$arrUpdate["name"] = $pageName;
		}
		
		$this->updateLayoutInDB($arrUpdate);
		
		$params = UniteFunctionsUC::getVal($data, "params");
		if(!empty($params))
			$this->updateParams($params);
		
	}
	
	
	/**
	 * update grid data in db
	 */
	public function updateGridData($gridData = null){
		
		if($gridData !== null){
			
			if(is_string($gridData))
				$gridData = UniteFunctionsUC::decodeContent($gridData);
				
			$this->gridData = $gridData;
		}
		
		$arrUpdate = array();
		$arrUpdate["layout_data"] = $this->prepareDataForSave($this->gridData);
		
		$this->updateLayoutInDB($arrUpdate);
	}
	
	
	/**
	 * delete layout
	 */
	public function delete(){
		$this->validateInited();
		
		$this->db->delete(GlobalsUC::$table_layouts, "id=".$this->id);
		
	}
	
	
	/**
	 * get new name
	 */
	protected function getDuplicateTitle(){
		
		$objLayouts = new UniteCreatorLayouts();
		
		$suffixTitle = " - copy";
	
		$title = $this->getTitle();
				
		$newTitle = $title.$suffixTitle;
		$isExists = $objLayouts->isLayoutExistsByTitle($newTitle, $this->layoutType);
		
		$num = 1;
		$limit = 1;
		while($isExists == true && $limit < 10){
			$limit++;
			$num++;
			$suffixTitle = " - copy".$num;
			$newTitle = $title.$suffixTitle;
			$isExists = $objLayouts->isLayoutExistsByTitle($newTitle, $this->layoutType);
		}
		
		
		return($newTitle);
	}
	
	
	/**
	 * duplicate layout
	 */
	public function duplicate(){
		
		$this->validateInited();
				
		$newTitle = $this->getDuplicateTitle();
		$newName = $this->generateName($newTitle);
				
		$layouts = new UniteCreatorLayouts();
		
		$layouts->shiftOrder($this->ordering, $this->layoutType);
		
		$newOrder = $this->ordering+1;
		
		//insert a new gallery
		$sqlSelect = "select ".self::FIELDS_LAYOUTS." from ".GlobalsUC::$table_layouts." where id={$this->id}";
		$sqlInsert = "insert into ".GlobalsUC::$table_layouts." (".self::FIELDS_LAYOUTS.") ($sqlSelect)";
		
		$this->db->runSql($sqlInsert);
		$lastID = $this->db->getLastInsertID();
		UniteFunctionsUC::validateNotEmpty($lastID);
		
		//update the new layout with the title and the name values
		$arrUpdate = array();
		$arrUpdate["title"] = $newTitle;
		$arrUpdate["name"] = $newName;
		$arrUpdate["ordering"] = $newOrder;
		
		$this->db->update(GlobalsUC::$table_layouts, $arrUpdate, array("id"=>$lastID));
		
		return($lastID);
	}
	
	
	private function a________________CREATE_CONTENT____________(){}
		
	
	/**
	 * get empty row, with 1 container, 1 column, 1 addon, and add some content
	 */
	public function getArrNewRow($arrAddonContent = array()){
		
		$row = array();
		$containers = array();
		$column = array();
		$addon = array();
		
		//set column
		$column["size"] = "1_1";
		
		//add addon
		$arrAddons = array();
		if(!empty($arrAddonContent))
			$arrAddons[] = $arrAddonContent;
		
		$column["addon_data"] = $arrAddons;
		
		//add column
		$containers["cols"] = array($column);
		
		//add container
		$row["containers"]  = array($containers);
		
		return($row);
	}
	
	
	/**
	 * set arr rows
	 */
	public function setRows($arrRows){
		
		$this->validateInited();
		
		if(empty($this->gridData))
			$this->gridData = array();
		
		$this->gridData["rows"] = $arrRows;
		
		//dmp($this->gridData);exit();
	}
	
	
	/**
	 * add row
	 */
	public function addRow($arrAddonContent = array()){
		
		$this->validateInited();
		
		if(empty($this->gridData))
			$this->gridData = array();
		
		$arrRows = $this->getRows();
		$arrRows[] = $this->getArrNewRow($arrAddonContent);
		
		$this->setRows($arrRows);
	}
	
	
	/**
	 * add row with html editor addon
	 * if html editor not exists, skip
	 */
	public function addRowWithHtmlAddon($arrAddonContent){
		
		
		$this->addRow($arrAddonContent);
		
	}
	
	
}