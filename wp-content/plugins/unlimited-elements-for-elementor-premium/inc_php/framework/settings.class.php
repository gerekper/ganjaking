<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');


/**
 *
 * unite settings class.
 * @version 1.1
 *
 */
	class UniteSettingsUC{

		const COLOR_OUTPUT_FLASH = "flash";
		const COLOR_OUTPUT_HTML = "html";

		//------------------------------------------------------------
		const ID_PREFIX = "unite_setting_";

		const RELATED_NONE = "";
		const TYPE_TEXT = "text";
		const TYPE_COLOR = "color";
		const TYPE_SELECT = "list";
		const TYPE_MULTISELECT = "multiselect";
		const TYPE_CHECKBOX = "checkbox";
		const TYPE_RADIO = "radio";
		const TYPE_TEXTAREA = "textarea";
		const TYPE_STATIC_TEXT = "statictext";
		const TYPE_HR = "hr";
		const TYPE_CUSTOM = "custom";
		const TYPE_CONTROL = "control";
		const TYPE_BUTTON = "button";
		const TYPE_LINK = "link";
		const TYPE_IMAGE = "image";
		const TYPE_BOOLEAN = "boolean";
		const TYPE_EDITOR = "editor";
		const TYPE_MP3 = "mp3";
		const TYPE_ICON = "icon";
		const TYPE_ADDON = "addon";
		const TYPE_SHAPE = "shape";
		const TYPE_POST = "post";
		const TYPE_MAP = "map";
		const TYPE_REPEATER = "repeater";
		const TYPE_RANGE = "range";
		const TYPE_HIDDEN = "hidden";
		const TYPE_TYPOGRAPHY = "typography";
		const TYPE_DIMENTIONS = "dimentions";
		const TYPE_GALLERY = "gallery";


		//------------------------------------------------------------
		//set data types
		const DATATYPE_NUMBER = "number";
		const DATATYPE_NUMBEROREMTY = "number_empty";
		const DATATYPE_STRING = "string";
		const DATATYPE_PLAINTEXT = "plaintext";
		const DATATYPE_LINK = "link";
		const DATATYPE_FREE = "free";

		const CONTROL_TYPE_ENABLE = "enable";
		const CONTROL_TYPE_DISABLE = "disable";
		const CONTROL_TYPE_SHOW = "show";
		const CONTROL_TYPE_HIDE = "hide";

		//additional parameters that can be added to settings.
		const PARAM_TEXTSTYLE = "textStyle";
		const PARAM_ADDPARAMS = "addparams";	//additional text after the field
		const PARAM_ADDTEXT = "addtext";	//additional text after the field
		const PARAM_ADDTEXT_BEFORE_ELEMENT = "addtext_before_element";	//additional text after the field
		const PARAM_CELLSTYLE = "cellStyle";	//additional text after the field
		const PARAM_NODRAW = "nodraw";			//don't draw the setting row
		const PARAM_MODE_TRANSPARENT = "mode_transparent";	//don't get value
		const PARAM_ADDFIELD = "addfield";		//add field to draw
		const PARAM_CLASSADD = "classAdd";		//add some class
		const PARAM_NOTEXT = "unite_setting_notext";			//don't set a text

		const TAB_CONTENT = "content";
		const TAB_STYLE = "style";


		//view defaults:
		protected $settingsType = null;
		protected $idPrefix;
		protected $defaultText = "Enter value";
		protected $sap_size = 5;

		//other variables:
		protected $HRIdCounter = 0;	//counter of hr id

		protected $arrSettings = array();
		protected $arrIndex = array();	//index of name->index of the settings.
		protected $arrSaps = array();
		protected $currentSapKey = 0;

		//controls:
		protected $arrControls = array();		//array of items that controlling others (hide/show or enabled/disabled)
		protected $arrControlChildren = array();

		protected $arrBulkControl = array();	//bulk cotnrol array. if not empty, every settings will be connected with control.

		//custom functions:
		protected $colorOutputType = self::COLOR_OUTPUT_HTML;

		protected $arrGlobalParams = array();		//add global params, will be added with every setting


		/**
		 * constructor
		 */
	    public function __construct(){

	    	$suffix = time()."_".UniteFunctionsUC::getRandomString(5)."_";

	    	$this->idPrefix = self::ID_PREFIX.$suffix;

	    }



		//-----------------------------------------------------------------------------------------------
		//set type of color output
		public function setColorOutputType($type){
			$this->colorOutputType = $type;
		}


		//-----------------------------------------------------------------------------------------------
		//modify the data before save
		private function modifySettingsData($arrSettings){

			foreach($arrSettings as $key=>$content){
				switch(getType($content)){
					case "string":
						//replace the unicode line break (sometimes left after json)
						$content = str_replace("u000a","\n",$content);
						$content = str_replace("u000d","",$content);
					break;
					case "object":
					case "array":
						$content = UniteFunctionsUC::convertStdClassToArray($content);
					break;
				}

				$arrSettings[$key] = $content;
			}

			return($arrSettings);
		}

		//-----------------------------------------------------------------------------------------------
		// add the section value to the setting
		private function checkAndAddSap($setting){

			$setting["sap"] = $this->currentSapKey;

			return($setting);
		}

		//-----------------------------------------------------------------------------------------------
		// validate items parameter. throw exception on error
		private function validateParamItems($arrParams){
			if(!isset($arrParams["items"])) throw new Exception("no select items presented");
			if(!is_array($arrParams["items"])) throw new Exception("the items parameter should be array");
			//if(empty($arrParams["items"])) throw new Exception("the items array should not be empty");
		}


		/**
		 * add setting to index
		 */
		private function addSettingToIndex($name){
			$this->arrIndex[$name] = count($this->arrSettings)-1;
		}

		private function a_______GETTERS_________(){}

		//-----------------------------------------------------------------------------------------------
		//get types array from all the settings:
		protected function getArrTypes(){
			$arrTypesAssoc = array();
			$arrTypes = array();
			foreach($this->arrSettings as $setting){
				$type = $setting["type"];
				if(!isset($arrTypesAssoc[$type])) $arrTypes[] = $type;
				$arrTypesAssoc[$type] = "";
			}
			return($arrTypes);
		}


		/**
		 *
		 * get settings array
		 */
		public function getArrSettings(){
			return($this->arrSettings);
		}


		/**
		 *
		 * get the keys of the settings
		 */
		public function getArrSettingNames(){
			$arrKeys = array();
			$arrNames = array();
			foreach($this->arrSettings as $setting){
				$name = UniteFunctionsUC::getVal($setting, "name");
				if(!empty($name))
					$arrNames[] = $name;
			}

			return($arrNames);
		}

		/**
		 *
		 * get the keys of the settings
		 */
		public function getArrSettingNamesAndTitles(){
			$arrKeys = array();
			$arrNames = array();
			foreach($this->arrSettings as $setting){
				$name = UniteFunctionsUC::getVal($setting, "name");
				$title = UniteFunctionsUC::getVal($setting, "text");
				if(!empty($name))
					$arrNames[$name] = $title;
			}

			return($arrNames);
		}



		/**
		 * get first section saps
		 */
		public function getArrSaps(){

			return($this->arrSaps);
		}


		/**
		 * get number of saps
		 */
		public function getNumSaps(){

			$numSaps = count($this->arrSaps);

			return($numSaps);
		}


		/**
		 *
		 * get controls
		 */
		public function getArrControls($withChildren = false){

			if($withChildren == true){
				$output = array();
				$output["parents"] = $this->arrControls;
				$output["children"] = $this->arrControlChildren;
				return($output);
			}

			return($this->arrControls);
		}

		/**
		 * get control children array
		 */
		public function getArrControlChildren(){

			return($this->arrControlChildren);
		}

		/**
		 *
		 * set settings array
		 */
		public function setArrSettings($arrSettings){
			$this->arrSettings = $arrSettings;
		}




		/**
		 * get setting index by name
		 */
		private function getIndexByName($name){

			//if index present
			if(!empty($this->arrIndex)){
				if(array_key_exists($name, $this->arrIndex) == false)
					UniteFunctionsUC::throwError("setting $name not found");
				$index = $this->arrIndex[$name];
				return($index);
			}

			//if no index
			foreach($this->arrSettings as $index=>$setting){
				$settingName = UniteFunctionsUC::getVal($setting, "name");
				if($settingName == $name)
					return($index);
			}

			UniteFunctionsUC::throwError("Setting with name: $name don't exists");
		}


		/**
		 *
		 * get setting array by name
		 */
		public function getSettingByName($name){

			$index = $this->getIndexByName($name);
			$setting = $this->arrSettings[$index];
			return($setting);
		}

		/**
		 * get arr settings by saps
		 */
		public function getArrSettingsBySap($sapName){
			$sapKey = $this->getSapKeyByName($sapName);
			if($sapKey === null)
				UniteFunctionsUC::throwError("sap: $sapName not found");
			$arrSapSettings = array();
			foreach($this->arrSettings as $setting){
				$sap = UniteFunctionsUC::getVal($setting, "sap");
				if($sap === $sapKey)
					$arrSapSettings[] = $setting;
			}
			return($arrSapSettings);
		}

		/**
		 * get setting values. replace from stored ones if given
		 */
		public function getArrValues(){

			$arrSettingsOutput = array();

			//modify settings by type
			foreach($this->arrSettings as $setting){
				if($setting["type"] == self::TYPE_HR
						||$setting["type"] == self::TYPE_STATIC_TEXT)
					continue;

				$value = $setting["value"];

				//modify value by type
				switch($setting["type"]){
					case self::TYPE_COLOR:
						$value = strtolower($value);
						//set color output type
						if($this->colorOutputType == self::COLOR_OUTPUT_FLASH)
							$value = str_replace("#","0x",$value);
						break;
					case self::TYPE_CHECKBOX:
						if($value == true) $value = "true";
						else $value = "false";
						break;
				}

				//remove lf
				if(isset($setting["remove_lf"])){
					$value = str_replace("\n","",$value);
					$value = str_replace("\r\n","",$value);
				}

				$arrSettingsOutput[$setting["name"]] = $value;
			}

			return($arrSettingsOutput);
		}

		/**
		 *
		 * get titles and descriptions array
		 */
		public function getArrTextFromAllSettings(){
			$arr = array();
			$arrUnits = array();

			if($this->arrSaps)
				$this->arrSaps = array();

			foreach($this->arrSaps as $sap){
				$text = $sap["text"];
				if(!empty($text))
					$arr[] = $text;
			}

			foreach($this->arrSettings as $setting){

				$text = UniteFunctionsUC::getVal($setting, "text");
				$desc = UniteFunctionsUC::getVal($setting, "description");
				$unit = UniteFunctionsUC::getVal($setting, "unit");

				if(!empty($text))
					$arr[] = $text;

				if(!empty($desc))
					$arr[] = $desc;

				if(!empty($unit)){
					if(!isset($arrUnits[$unit]))
						$arr[] = $unit;
					$arrUnits[$unit] = true;
				}

				$items = UniteFunctionsUC::getVal($setting, "items");
				if(!empty($items)){
					foreach($items as $item){
						if(!isset($arrUnits[$item]))
							$arr[] = $item;
						$arrUnits[$item] = true;
					}
				}
			}

			return($arr);
		}


		/**
		 *
		 * get value of some setting
		 * @param $name
		 */
		public function getSettingValue($name,$default=""){
			$setting = $this->getSettingByName($name);
			$value = UniteFunctionsUC::getVal($setting, "value",$default);

			return($value);
		}


		/**
		 * get id prefix
		 */
		public function getIDPrefix(){

			return($this->idPrefix);
		}

		private function a_________SAPS_________(){}

		//-----------------------------------------------------------------------------------------------
		//get number of settings
		public function getNumSettings(){
			$counter = 0;
			foreach($this->arrSettings as $setting){
				switch($setting["type"]){
					case self::TYPE_HR:
					case self::TYPE_STATIC_TEXT:
					break;
					default:
						$counter++;
					break;
				}
			}
			return($counter);
		}


		/**
		 * get sap data
		 */
		public function getSap($sapKey){

			$arrSap = UniteFunctionsUC::getVal($this->arrSaps, $sapKey);
			if(empty($arrSap))
				UniteFunctionsUC::throwError("sap with key: $sapKey not found");

			return($arrSap);
		}


		/**
		 * get sap by name
		 */
		protected function getSapKeyByName($name){

			foreach($this->arrSaps as $key=>$sap){
				if($sap["name"] == $name)
					return($key);
			}

			return(null);
		}

		/**
		 * hide sap from showing
		 */
		public function hideSap($name){

			foreach($this->arrSaps as $key=>$sap){

				$sapName = UniteFunctionsUC::getVal($sap, "name");

				if($sapName == $name){
					$this->arrSaps[$key]["hidden"] = true;
				}

			}

		}

		private function a_________ADD_________(){}

		//private function
		//-----------------------------------------------------------------------------------------------
		// add radio group
		public function addRadio($name,$arrItems,$text = "",$defaultItem="",$arrParams = array()){
			$params = array("items"=>$arrItems);
			$params = array_merge($params,$arrParams);
			$this->add($name,$defaultItem,$text,self::TYPE_RADIO,$params);
		}

		//-----------------------------------------------------------------------------------------------
		//add text area control
		public function addTextArea($name,$defaultValue,$text,$arrParams = array()){
			$this->add($name,$defaultValue,$text,self::TYPE_TEXTAREA,$arrParams);
		}

		//-----------------------------------------------------------------------------------------------
		//add button control
		public function addButton($name, $value, $text, $arrParams = array()){
			$this->add($name,$value,$text,self::TYPE_BUTTON,$arrParams);
		}


		//-----------------------------------------------------------------------------------------------
		// add checkbox element
		public function addCheckbox($name,$defaultValue = false,$text = "", $textNear="", $arrParams = array()){
			$defaultValue = UniteFunctionsUC::strToBool($defaultValue);

			if(!empty($textNear))
				$arrParams["text_near"] = $textNear;

			$this->add($name,$defaultValue,$text,self::TYPE_CHECKBOX,$arrParams);
		}


		/**
		 * add text box
		 */
		public function addTextBox($name,$defaultValue = "",$text = "",$arrParams = array()){
			$this->add($name,$defaultValue,$text,self::TYPE_TEXT,$arrParams);
		}

		/**
		 * add hidden input
		 */
		public function addHiddenInput($name,$defaultValue = "",$text = "",$arrParams = array()){

			$arrParams["hidden"] = true;

			$this->add($name,$defaultValue,$text,self::TYPE_HIDDEN,$arrParams);
		}


		/**
		 * add range slider
		 */
		public function addRangeSlider($name, $defaultValue = "",$text = "",$arrParams = array()){

			if(isset($arrParams["unit"])){
				$arrParams["range_unit"] = $arrParams["unit"];
				unset($arrParams["unit"]);
			}

			$this->add($name,$defaultValue,$text,self::TYPE_RANGE,$arrParams);
		}


		/**
		 * add text box
		 */
		public function addEditor($name,$defaultValue = "",$text = "",$arrParams = array()){
			$this->add($name,$defaultValue,$text,self::TYPE_EDITOR,$arrParams);
		}

		/**
		 * add link setting
		 */
		public function addLink($name,$defaultValue = "",$text = "",$arrParams = array()){
			$this->add($name,$defaultValue,$text,self::TYPE_LINK,$arrParams);
		}

		/**
		 * add image chooser setting
		 */
		public function addImage($name,$defaultValue = "",$text = "",$arrParams = array()){
			$this->add($name,$defaultValue,$text,self::TYPE_IMAGE,$arrParams);
		}

		/**
		 * add image chooser setting
		 */
		public function addMp3($name,$defaultValue = "",$text = "",$arrParams = array()){
			$this->add($name,$defaultValue,$text,self::TYPE_MP3,$arrParams);
		}

		/**
		 * add image chooser setting
		 */
		public function addGoogleMap($name,$defaultValue = "",$text = "",$arrParams = array()){
			$this->add($name,$defaultValue,$text,self::TYPE_MAP,$arrParams);
		}


		/**
		 * add icon picker
		 */
		public function addIconPicker($name,$defaultValue = "",$text = "",$arrParams = array()){
			$this->add($name,$defaultValue,$text,self::TYPE_ICON,$arrParams);
		}

		/**
		 * add icon picker
		 */
		public function addShapePicker($name,$defaultValue = "",$text = "",$arrParams = array()){

			$arrParams["icons_type"] = "shape";

			$this->add($name,$defaultValue,$text,self::TYPE_ICON,$arrParams);
		}


		/**
		 * add addon picker
		 */
		public function addAddonPicker($name,$defaultValue = "",$text = "",$arrParams = array()){

			$this->add($name,$defaultValue,$text,self::TYPE_ADDON,$arrParams);
		}

		/**
		 * add post picker
		 */
		public function addPostPicker($name,$defaultValue = "",$text = "",$arrParams = array()){
			$this->add($name,$defaultValue,$text,self::TYPE_POST,$arrParams);
		}


		/**
		 * add color picker setting
		 */
		public function addColorPicker($name,$defaultValue = "",$text = "",$arrParams = array()){
			$this->add($name,$defaultValue,$text,self::TYPE_COLOR,$arrParams);
		}


		/**
		 * add repeater
		 */
		public function addRepeater($name, $settingsItems, $arrValues, $text, $arrParams = array()){

			$arrParams["settings_items"] = $settingsItems;
			$arrParams["items_values"] = $arrValues;

			$this->add($name, null, $text, self::TYPE_REPEATER, $arrParams);
		}


		/**
		 *
		 * add custom setting
		 */
		public function addCustom($customType,$name,$defaultValue = "",$text = "",$arrParams = array()){
			$params = array();
			$params["custom_type"] = $customType;
			$params = array_merge($params,$arrParams);

			$this->add($name,$defaultValue,$text,self::TYPE_CUSTOM,$params);
		}


		//-----------------------------------------------------------------------------------------------
		//add horezontal sap
		public function addHr($name="",$params=array()){
			$setting = array();
			$setting["type"] = self::TYPE_HR;

			//set item name
			$itemName = "";
			if($name != "")
				$itemName = $name;
			else{	//generate hr id
			  $this->HRIdCounter++;
			  $itemName = "hr_".UniteFunctionsUC::getRandomString();

			  if(array_key_exists($itemName, $this->arrIndex))
			  	$itemName = "hr_".UniteFunctionsUC::getRandomString();

			  if(array_key_exists($itemName, $this->arrIndex))
			  	$itemName = "hr_".UniteFunctionsUC::getRandomString();
			}

			$setting["id"] = $this->idPrefix.$itemName;
			$setting["id_row"] = $setting["id"]."_row";
			$setting["name"] = $itemName;

			//add sap key
			$setting = $this->checkAndAddSap($setting);

			$this->checkAddBulkControl($itemName);

			$setting = array_merge($params,$setting);
			$this->arrSettings[] = $setting;

			//add to settings index
			$this->addSettingToIndex($itemName);
		}


		/**
		 * add static text
		 */
		public function addStaticText($text,$name="",$params=array()){
			$setting = array();
			$setting["type"] = self::TYPE_STATIC_TEXT;

			//set item name
			$itemName = "";
			if($name != "")
				 $itemName = $name;
			else{	//generate hr id
			  $this->HRIdCounter++;
			  $itemName = "textitem".$this->HRIdCounter;
			}

			//if passed text with label, add label to settings
			if(isset($params["text"])){
				$setting["label"] = $text;
				$text = $params["text"];
			}

			$setting["id"] = $this->idPrefix.$itemName;
			$setting["name"] = $itemName;
			$setting["id_row"] = $setting["id"]."_row";
			$setting["text"] = $text;

			$this->checkAddBulkControl($itemName);

			$setting = array_merge($setting, $params);


			//add sap key
			$setting = $this->checkAndAddSap($setting);

			$this->arrSettings[] = $setting;

			//add to settings index
			$this->addSettingToIndex($itemName);

		}


		/**
		 * add select setting
		 */
		public function addSelect($name,$arrItems,$text,$defaultItem="",$arrParams=array()){
			$params = array("items"=>$arrItems);
			$params = array_merge($params,$arrParams);

			$this->add($name,$defaultItem,$text,self::TYPE_SELECT,	$params);
		}

		/**
		 * add select setting
		 */
		public function addMultiSelect($name,$arrItems,$text,$defaultItem="",$arrParams=array()){

			$params = array("items"=>$arrItems);
			$params = array_merge($params,$arrParams);

			$this->add($name,$defaultItem,$text,self::TYPE_MULTISELECT,	$params);
		}


		/**
		 *
		 * add saporator
		 */
		public function addSap($text, $name="", $tab = null, $params = array()){

			if(empty($tab))
				$tab = self::TAB_CONTENT;

			if(empty($text))
				UniteFunctionsUC::throwError("sap $name must have a text");


			$opened = UniteFunctionsUC::getVal($params, "opened");
			$icon = UniteFunctionsUC::getVal($params, "icon");

			//create sap array
			$sap = array();
			$sap["name"] = $name;
			$sap["text"] = $text;
			$sap["icon"] = $icon;
			$sap["tab"] = $tab;

			if(!empty($params))
				$sap = array_merge($sap, $params);

			if($opened === true)
				$sap["opened"] = true;

			$this->arrSaps[] = $sap;

			$this->currentSapKey = count($this->arrSaps)-1;

		}

		/**
		 * function for override
		 */
		protected function modifyBeforeAdd($setting, $modifyType){

			return($setting);
		}


		/**
		 * add setting global params if exists
		 */
		protected function addSettingGlobalParams($setting){

			if(empty($this->arrGlobalParams))
				return($setting);

			$settingType = UniteFunctionsUC::getVal($setting, "type");
			foreach($this->arrGlobalParams as $param){

				$types = UniteFunctionsUC::getVal($param, "types");

				//add without filter types
				if(empty($types)){
					$setting[$param["name"]] = $param["value"];
					continue;
				}

				//filter by type
				if(is_string($types)){
					if(strpos($types, $settingType) !== false)
						$setting[$param["name"]] = $param["value"];

				}else if(is_array($types)){
					if(array_search($settingType, $types) !== false)
						$setting[$param["name"]] = $param["value"];
				}

			}

			return($setting);
		}


		/**
		 * add setting, may be in different type, of values
		 */
		protected function add($name,$defaultValue = "",$text = "",$type = self::TYPE_TEXT,$arrParams = array()){

			//validation:
			if(empty($name)) throw new Exception("Every setting should have a name!");

			switch($type){
				case self::TYPE_RADIO:
				case self::TYPE_SELECT:
				case self::TYPE_MULTISELECT:
					$this->validateParamItems($arrParams);
				break;
				case self::TYPE_CHECKBOX:
					if(!is_bool($defaultValue))
						throw new Exception("The checkbox value should be boolean");
				break;
			}

			//validate name:
			if(isset($this->arrIndex[$name]))
				throw new Exception("Duplicate setting name:".$name);


			//set defaults:
			if($text == "")
				$text = $this->defaultText;

			if($text == self::PARAM_NOTEXT)
				$text = "";

			$setting = array();
			$setting["name"] = $name;
			$setting["type"] = $type;
			$setting["text"] = $text;
			$setting["value"] = $defaultValue;
			$setting["default_value"] = $defaultValue;

			$setting = array_merge($setting,$arrParams);

			//set datatype
			if(!isset($setting["datatype"])){
				$datatype = self::DATATYPE_STRING;
				switch ($type){
					case self::TYPE_TEXTAREA:
						$datatype = self::DATATYPE_FREE;
					break;
					default:
						$datatype = self::DATATYPE_STRING;
					break;
				}

				$setting["datatype"] = $datatype;
			}

			//add global params
			$setting = $this->addSettingGlobalParams($setting);


			$modifyType = UniteFunctionsUC::getVal($setting, "modifytype");
			if(!empty($modifyType)){
				$setting = $this->modifyBeforeAdd($setting, $modifyType);
			}


			$this->addSettingByArray($setting);
		}


		/**
		 * add some setting by array or prepared setting
		 */
		public function addSettingByArray($setting){

			if(is_array($setting) == false)
				UniteFunctionsUC::throwError("addSettingByArray: Empty setting given");

			$name = UniteFunctionsUC::getVal($setting, "name");

			UniteFunctionsUC::validateNotEmpty($name, "setting name");

			$this->checkAddBulkControl($name);

			$setting["id"] = $this->idPrefix.$name;
			$setting["id_service"] = $setting["id"]."_service";
			$setting["id_row"] = $setting["id"]."_row";

			//add sap key and sap keys

			$setting = $this->checkAndAddSap($setting);

			$this->arrSettings[] = $setting;

			//add to settings index
			$this->addSettingToIndex($name);
		}


		private function a___________CONTROLS____________(){}

		/**
		 * add child control with parent name
		 */
		private function addControlChildArray($childName, $parentName){

			if(!isset($this->arrControlChildren[$childName]))
				$this->arrControlChildren[$childName] = array();

			$this->arrControlChildren[$childName][] = $parentName;
		}

		/**
		 * add a item that controlling visibility of enabled/disabled of other.
		 */
		public function addControl($control_item_name,$controlled_item_name,$control_type,$value){

			UniteFunctionsUC::validateNotEmpty($control_item_name,"control parent");
			UniteFunctionsUC::validateNotEmpty($controlled_item_name,"control child");
			UniteFunctionsUC::validateNotEmpty($control_type,"control type");

			if(empty($value))
				$value = "";

			//check for multiple control items
			if(strpos($controlled_item_name, ",") !== false){
				$controlled_item_name = explode(",", $controlled_item_name);

				foreach($controlled_item_name as $key=>$cvalue)
					$controlled_item_name[$key] = trim($cvalue);
			}

			//modify for multiple values
			$arrValues = array();

			if(is_array($value) == false && strpos($value, ",") !== false){

				$arrValues = explode(",", $value);

				foreach($arrValues as $key=>$value)
					$arrValues[$key] = trim($value);

				$value = $arrValues;
			}

			//get the control by parent, or create new
			$arrControl = array();
			if(isset($this->arrControls[$control_item_name]))
				$arrControl = $this->arrControls[$control_item_name];

			if(is_array($controlled_item_name)){

				foreach($controlled_item_name as $cname){
					$arrControl[$cname] = array("type"=>$control_type, "value"=>$value);

					$this->addControlChildArray($cname, $control_item_name);
				}
			}else{
				$arrControl[$controlled_item_name] = array("type"=>$control_type, "value"=>$value);

				$this->addControlChildArray($controlled_item_name, $control_item_name);
			}

			$this->arrControls[$control_item_name] = $arrControl;

		}


		/**
		 * start control of all settings that comes after this function (between startBulkControl and endBulkControl)
		 */
		public function startBulkControl($control_item_name,$control_type,$value){

			$this->arrBulkControl[] = array("control_name"=>$control_item_name,"type"=>$control_type,"value"=>$value);

		}


		/**
		 * end bulk control
		 */
		public function endBulkControl(){

			if(empty($this->arrBulkControl))
				return(false);

			array_pop($this->arrBulkControl);
		}


		/**
		 * compare if the control values are equal
		 */
		private function isControlValuesEqual($parentValue, $value){

			if(is_array($value))
				return (in_array($parentValue, $value) === true);
			else {
				$value = strtolower($value);
				return ($parentValue === $value);
			}

		}


		/**
		 * get control action
		 */
		private function getControlAction($parentName, $arrControl){

			$parentValue = $this->getSettingValue($parentName);
			$value = $arrControl["value"];
			$type = $arrControl["type"];

			switch($type){
				case self::CONTROL_TYPE_ENABLE:
					if($this->isControlValuesEqual($parentValue, $value) == false)
						return("disable");
					break;
				case self::CONTROL_TYPE_DISABLE:
					if($this->isControlValuesEqual($parentValue, $value) == true)
						return("disable");
					break;
				case self::CONTROL_TYPE_SHOW:
					if($this->isControlValuesEqual($parentValue, $value) == false)
						return("hide");
					break;
				case self::CONTROL_TYPE_HIDE:
					if($this->isControlValuesEqual($parentValue, $value) == true)
						return("hide");
					break;
			}

			return(null);
		}


		/**
		 * set sattes of the settings (enabled/disabled, visible/invisible) by controls
		 */
		public function setSettingsStateByControls(){

			if(empty($this->arrControls))
				return(false);

			foreach($this->arrControlChildren as $childName => $arrParents){

				foreach($arrParents as $parentName){

					$arrControl = $this->arrControls[$parentName][$childName];
					$action = $this->getControlAction($parentName, $arrControl);

					if($action == "disable"){
						$this->updateSettingProperty($childName, "disabled", true);
						break;
					}

					if($action == "hide"){
						$this->updateSettingProperty($childName, "hidden", true);
						break;
					}

				}

			}

		}


		/**
		 * set the nodraw params if paired setting available
		 */
		public function setPairedSettings(){


			foreach($this->arrSettings as $setting){
				$addSettingName = UniteFunctionsUC::getVal($setting, self::PARAM_ADDFIELD);

				if(empty($addSettingName))
					continue;

				$this->updateSettingProperty($addSettingName, self::PARAM_NODRAW, true);
			}

		}


		//-----------------------------------------------------------------------------------------------
		//check that bulk control is available , and add some element to it.
		private function checkAddBulkControl($name){
			//add control
			if(empty($this->arrBulkControl))
				return(false);

			foreach($this->arrBulkControl as $control)
				$this->addControl($control["control_name"],$name, $control["type"], $control["value"]);

		}


		private function a______XML_______(){}


		/**
		 *
		 * load settings from xml file
		 */
		public function loadXMLFile($filepath, $loadedSettingsType = null){
			
			
			$obj = UniteFunctionsUC::loadXMLFile($filepath);
			
			if(empty($obj))
				UniteFunctionsUC::throwError("Wrong xml file format: $filepath");

			$fieldsets = $obj->fieldset;
            if(!@count($obj->fieldset)){
                $fieldsets = array($fieldsets);
            }

			foreach($fieldsets as $fieldset){

				//Add Section
				$attribs = $fieldset->attributes();

				$sapName = (string)UniteFunctionsUC::getVal($attribs, "name");
				$sapLabel = (string)UniteFunctionsUC::getVal($attribs, "label");
				$sapIcon = (string)UniteFunctionsUC::getVal($attribs, "icon");
				$loadFrom = (string)UniteFunctionsUC::getVal($attribs, "loadfrom");
				$loadParam = (string)UniteFunctionsUC::getVal($attribs, "loadparam");
				$loadedSettingsType = (string)UniteFunctionsUC::getVal($attribs, "loadtype");
				$nodraw = (string)UniteFunctionsUC::getVal($attribs, "nodraw");
				$visibility = (string)UniteFunctionsUC::getVal($attribs, "visibility");

				$isForceShow = false;

				/*
				 * //demo for show setting
				if($sapName == "my_setting" && defined("UC_ENABLE_COPYPASTE"))
					$isForceShow = true;
				*/

				if($visibility == "dev" && GlobalsUC::$inDev == false && $isForceShow == false)
					continue;

				$sapParams = array();
				if(!empty($nodraw)){
					$sapParams["nodraw"] = UniteFunctionsUC::strToBool($nodraw);
				}

				UniteFunctionsUC::validateNotEmpty($sapName,"sapName");

				if(!empty($loadFrom)){

					$this->addExternalSettings($loadFrom, $loadParam, $loadedSettingsType);
					continue;
				}

				UniteFunctionsUC::validateNotEmpty($sapLabel,"sapLabel");

				//check for duplicate sap
				$sapKey = $this->getSapKeyByName($sapName);

				if($sapKey === null)
					$this->addSap($sapLabel, $sapName, false, $sapIcon, $sapParams);
				else{
					$this->currentSapKey = $sapKey;
				}

				//--- add fields
				$fieldset = (array)$fieldset;

				$fields = UniteFunctionsUC::getVal($fieldset, "field");

				if(empty($fields))
					$fields = array();
				else
				if(is_array($fields) == false)
					$fields = array($fields);

				foreach($fields as $field){
					$attribs = $field->attributes();
					$fieldType = (string)UniteFunctionsUC::getVal($attribs, "type");
					$fieldName = (string)UniteFunctionsUC::getVal($attribs, "name");
					$fieldLabel = (string)UniteFunctionsUC::getVal($attribs, "label");
					$fieldDefaultValue = (string)UniteFunctionsUC::getVal($attribs, "default");

					//all other params will be added to "params array".
					$arrMustParams = array("type","name","label","default");

					$arrParams = array();

					foreach($attribs as $key=>$value){
						$key = (string)$key;
						$value = (string)$value;

						//skip must params:
						if(in_array($key, $arrMustParams))
							continue;

						$arrParams[$key] = $value;
					}


					$options = $this->getOptionsFromXMLField($field, $fieldName);

					//validate must fields:
					UniteFunctionsUC::validateNotEmpty($fieldType,"type");

					//validate name
					if($fieldType != self::TYPE_HR && $fieldType != self::TYPE_CONTROL &&
						$fieldType != "bulk_control_start" && $fieldType != "bulk_control_end")
						UniteFunctionsUC::validateNotEmpty($fieldName,"name");
					switch ($fieldType){
						case self::TYPE_CHECKBOX:

							$fieldDefaultValue = UniteFunctionsUC::strToBool($fieldDefaultValue);
							$this->addCheckbox($fieldName,$fieldDefaultValue,$fieldLabel,"", $arrParams);
						break;
						case self::TYPE_COLOR:
							$this->addColorPicker($fieldName,$fieldDefaultValue,$fieldLabel,$arrParams);
						break;
						case self::TYPE_HR:
							$this->addHr($fieldName);
						break;
						case self::TYPE_TEXT:
							$this->addTextBox($fieldName,$fieldDefaultValue,$fieldLabel,$arrParams);
						break;
						case self::TYPE_STATIC_TEXT:

							$this->addStaticText($fieldLabel, $fieldName, $arrParams);
						break;
						case self::TYPE_IMAGE:
							$this->addImage($fieldName,$fieldDefaultValue,$fieldLabel,$arrParams);
						break;
						case self::TYPE_MP3:
							$this->addMp3($fieldName,$fieldDefaultValue,$fieldLabel,$arrParams);
						break;
						case self::TYPE_SELECT:
							$this->addSelect($fieldName, $options, $fieldLabel,$fieldDefaultValue,$arrParams);
						break;
						case self::TYPE_MULTISELECT:
							$this->addMultiSelect($fieldName, $options, $fieldLabel,$fieldDefaultValue,$arrParams);
						break;
						case self::TYPE_CHECKBOX:
							$this->addChecklist($fieldName, $options, $fieldLabel,$fieldDefaultValue,$arrParams);
						break;
						case self::TYPE_RADIO:
							$this->addRadio($fieldName, $options, $fieldLabel,$fieldDefaultValue,$arrParams);
						break;
						case self::TYPE_BOOLEAN:
							$options = array("Yes"=>"true","No"=>"false");
							$this->addRadio($fieldName, $options, $fieldLabel,$fieldDefaultValue,$arrParams);
						break;
						case self::TYPE_TEXTAREA:
							$this->addTextArea($fieldName, $fieldDefaultValue, $fieldLabel, $arrParams);
						break;
						case self::TYPE_CUSTOM:
							$this->add($fieldName, $fieldDefaultValue, $fieldLabel, self::TYPE_CUSTOM, $arrParams);
						break;
						case self::TYPE_BUTTON:
							$this->addButton($fieldName, $fieldDefaultValue, $fieldLabel, $arrParams);
						break;
						case self::TYPE_ICON:
							$this->addIconPicker($fieldName, $fieldDefaultValue, $fieldLabel, $arrParams);
						break;
						case self::TYPE_ADDON:
							$this->addAddonPicker($fieldName, $fieldDefaultValue, $fieldLabel, $arrParams);
						break;
						case self::TYPE_RANGE:
							$this->addRangeSlider($fieldName, $fieldDefaultValue, $fieldLabel, $arrParams);
						break;
						case self::TYPE_HIDDEN:
							$this->addHiddenInput($fieldName, $fieldDefaultValue, $fieldLabel, $arrParams);
						break;
						case self::TYPE_POST:
							$this->addPostPicker($fieldName, $fieldDefaultValue, $fieldLabel, $arrParams);
						break;
						case "fonts_panel":
							$this->addFontPanel(array(), array(), $fieldName);
						break;
						case self::TYPE_CONTROL:
							$parent = UniteFunctionsUC::getVal($arrParams, "parent");
							$child =  UniteFunctionsUC::getVal($arrParams, "child");
							$ctype =  UniteFunctionsUC::getVal($arrParams, "ctype");
							$value =  UniteFunctionsUC::getVal($arrParams, "value");
							$this->addControl($parent, $child, $ctype, $value);
						break;
						case "bulk_control_start":
							$parent = UniteFunctionsUC::getVal($arrParams, "parent");
							$ctype =  UniteFunctionsUC::getVal($arrParams, "ctype");
							$value =  UniteFunctionsUC::getVal($arrParams, "value");

							$this->startBulkControl($parent, $ctype, $value);
						break;
						case "bulk_control_end":
							$this->endBulkControl();
						break;
						default:
							UniteFunctionsUC::throwError("wrong type: $fieldType");
						break;
					}

				}
			}


		}


		/**
		 * add settings from xml file with a new sap
		 */
		public function addFromXmlFile($filepathXML){

			$objNewSettings = new UniteSettingsUC();
			$objNewSettings->loadXMLFile($filepathXML);

			$this->mergeSettings($objNewSettings);

		}

		private function a_______OTHERS______(){}


		/**
		 * set settings type - for using it later for loading
		 * Enter description here ...
		 */
		public function setType($type){
			$this->settingsType = $type;
		}


		/**
		 * add global param, global params will be added to all settings
		 * types is string or array
		 */
		public function addGlobalParam($name, $value, $types = null){

			$this->arrGlobalParams[] = array("name"=>$name, "value"=>$value, "types" => $types);
		}


		/**
		 * build name->(array index) of the settings
		 */
		private function reindex(){
			$this->arrIndex = array();
			foreach($this->arrSettings as $key=>$value)
				if(isset($value["name"]))
					$this->arrIndex[$value["name"]] = $key;
		}


		//-----------------------------------------------------------------------------------------------
		//set custom function that will be run after sections will be drawen
		public function setCustomDrawFunction_afterSections($func){
			$this->customFunction_afterSections = $func;
		}


		/**
		 *
		 * parse options from xml field
		 * @param $field
		 */
		private function getOptionsFromXMLField($field, $fieldName){

			$arrOptions = array();

			$arrField = (array)$field;

			$options = UniteFunctionsUC::getVal($arrField, "option");
			if(!empty($options) && is_array($options) == false)
				$options = array($options);

			if(empty($options))
				return($arrOptions);

			foreach($options as $option){

				if(gettype($option) == "string")
					UniteFunctionsUC::throwError("Wrong options type: ".$option." in field: $fieldName");

				$attribs = $option->attributes();

				$optionValue = (string)UniteFunctionsUC::getVal($attribs, "value");
				$optionText = (string)UniteFunctionsUC::getVal($attribs, "text");

				//validate options:
				UniteFunctionsUC::validateNotEmpty($optionText,"option text");

				$arrOptions[$optionText] = $optionValue;
			}

			return($arrOptions);
		}


		/**
		 *
		 * merge settings with another settings object
		 */
		public function mergeSettings(UniteSettingsUC $settings){

			$arrSapsNew = $settings->getArrSaps();
			$arrSapsCurrent = $this->arrSaps;
			$arrNewSapKeys = array();


			//add new saps to saps array and remember keys
			foreach($arrSapsNew as $key => $sap){
				$sapName = $sap["name"];

				$currentSapKey = $this->getSapKeyByName($sapName);
				if($currentSapKey === null){
					$this->arrSaps[] = $sap;
					$this->currentSapKey = count($this->arrSaps)-1;
					$arrNewSapKeys[$key] = $this->currentSapKey;
				}else{
					$arrNewSapKeys[$key] = $currentSapKey;
				}

			}


			//add settings
			$arrSettingsNew = $settings->getArrSettings();

			foreach($arrSettingsNew as $setting){
				$name = $setting["name"];
				$sapOld = $setting["sap"];
				$setting["id"] = $this->idPrefix.$name;
				$setting["id_service"] = $this->idPrefix.$name."_service";
				$setting["id_row"] = $this->idPrefix.$name."_row";

				if(array_key_exists($sapOld, $arrNewSapKeys) == false)
					UniteFunctionsUC::throwError("sap {$sapOld} should be exists in sap keys array");

				$sapNew = $arrNewSapKeys[$sapOld];

				$setting["sap"] = $sapNew;
				$this->arrSettings[] = $setting;

				if(array_key_exists($name, $this->arrIndex))
					UniteFunctionsUC::throwError("The setting <b>{$name} </b> already exists. ");

				$this->arrIndex[$name] = count($this->arrSettings)-1;

			}

			//add controls
			$arrControlsNew = $settings->getArrControls();
			$this->arrControls = array_merge($this->arrControls, $arrControlsNew);

			$arrControlChildrenNew = $settings->getArrControlChildren();
			$this->arrControlChildren = array_merge($this->arrControlChildren, $arrControlChildrenNew);

		}

		/**
		 * modify external loaded settings
		 */
		public function modifyLoadedSettings($loadParam){

		}

		/**
		 * add settings from external file
		 */
		private function addExternalSettings($filename, $loadParam = null, $loadType = null){

			$filepathSettings = GlobalsUC::$pathSettings."{$filename}.xml";

			if(file_exists($filepathSettings) == false)
				UniteFunctionsUC::throwError("The file: {$filename}.xml don't found in settings folder");

			$settings = new UniteCreatorSettings();

			if(!empty($loadType))
				$settings->setType($loadType);

			$settings->loadXMLFile($filepathSettings, $loadParam);

			if(!empty($loadParam))
				$settings->modifyLoadedSettings($loadParam);

			$this->mergeSettings($settings);
		}


		/**
		 *
		 * update setting array by name
		 */
		public function updateArrSettingByName($name,$setting){

			foreach($this->arrSettings as $key => $settingExisting){
				$settingName = UniteFunctionsUC::getVal($settingExisting,"name");
				if($settingName == $name){
					$this->arrSettings[$key] = $setting;
					return(false);
				}
			}

			UniteFunctionsUC::throwError("Setting with name: $name don't exists");
		}

		/**
		 * hide some setting
		 * @param unknown_type $name
		 */
		public function hideSetting($name){
			$this->updateSettingProperty($name, "hidden", "true");
		}

		/**
		 * hide multiple settings from array
		 *
		 */
		public function hideSettings($arrSettings){

			foreach($arrSettings as $settingName)
				$this->hideSetting($settingName);
		}


		/**
		 * remove setting
		 * don't remove handle controls yet
		 */
		public function removeSetting($name){

			$index = $this->getIndexByName($name);

			array_splice($this->arrSettings, $index, 1);

			$this->reindex();

		}


		/**
		 *
		 * modify some value by it's datatype
		 */
		public function modifyValueByDatatype($value,$datatype){
			if(is_array($value)){
				foreach($value as $key => $val){
					$value[$key] = $this->modifyValueByDatatypeFunc($val,$datatype);
				}
			}else{
				$value = $this->modifyValueByDatatypeFunc($value,$datatype);
			}
			return($value);
		}

		/**
		 *
		 * modify some value by it's datatype
		 */
		public function modifyValueByDatatypeFunc($value, $datatype){

			switch($datatype){
				case self::DATATYPE_STRING:
				break;
				case self::DATATYPE_LINK:

					$link = UniteFunctionsUC::getHrefFromHtml($value);
					if(!empty($link))
						$value = $link;

					$value = strip_tags($value);

				break;
				case self::DATATYPE_PLAINTEXT:

					$value = strip_tags($value);

				break;
				case self::DATATYPE_NUMBER:
					$value = floatval($value);	//turn every string to float
					if(!is_numeric($value))
						$value = 0;
					break;
				case self::DATATYPE_NUMBEROREMTY:
					$value = trim($value);
					if($value !== "")
						$value = floatval($value);	//turn every string to float
					break;
			}

			return $value;
		}

		/**
		 *
		 * set values from array of stored settings elsewhere.
		 */
		public function setStoredValues($arrValues){

			if(empty($arrValues))
				return(false);

			foreach($this->arrSettings as $key=>$setting){

				$name = UniteFunctionsUC::getVal($setting, "name");

				//type consolidation
				$type = UniteFunctionsUC::getVal($setting, "type");

				$datatype = UniteFunctionsUC::getVal($setting, "datatype");

				//skip custom type
				$customType = UniteFunctionsUC::getVal($setting, "custom_type");

				if(!empty($customType))
					continue;

				if(array_key_exists($name, $arrValues)){

					$value = $arrValues[$name];

					$value = $this->modifyValueByDatatype($value, $datatype);

					$this->arrSettings[$key]["value"] = $value;
					$arrValues[$name] = $value;
				}

			}//end foreach

			return($arrValues);
		}




		private function a__________UPDATE____________(){}


		/**
		 * set addtext to the setting
		 */
		public function updateSettingAddHTML($name, $html){
			$this->updateSettingProperty($name, self::PARAM_ADDTEXT, $html);
		}

		/**
		 * update setting property
		 */
		public function updateSettingProperty($settingName, $propertyName, $value){

			try{
				$setting = $this->getSettingByName($settingName);

				if(empty($setting))
					return(false);

			}catch(Exception $e){
				return(false);
			}

			$setting[$propertyName] = $value;

			$this->updateArrSettingByName($settingName, $setting);
		}


		/**
		 *
		 * update default value in the setting
		 */
		public function updateSettingValue($name,$value){

			$setting = $this->getSettingByName($name);
			$setting["value"] = $value;

			$this->updateArrSettingByName($name, $setting);
		}

		/**
		 *
		 * update default value in the setting
		 */
		public function updateSettingItems($name, $items, $default = null){
			$setting = $this->getSettingByName($name);
			$setting["items"] = $items;
			if($default !== null)
				$setting["value"] = $default;

			$this->updateArrSettingByName($name, $setting);
		}

		/**
		 * change setting name
		 */
		public function changeSettingName($oldName, $newName){

			$index = $this->getIndexByName($oldName);

			unset($this->arrIndex[$oldName]);

			//change in index
			$this->arrIndex[$newName] = $index;

			//change in settings
			$this->arrSettings[$index]["name"] = $newName;

		}


	}

?>
