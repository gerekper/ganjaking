<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

	class UniteCreatorAddonWork extends UniteElementsBaseUC{
		
		const FIELDS_ADDONS = "title,name,alias,addontype,description,ordering,templates,config,catid,test_slot1,test_slot2,test_slot3,is_active";
		const ITEMS_TYPE_IMAGE = "image";
		const ITEMS_TYPE_DEFAULT = "default";
		const ITEMS_TYPE_FORM = "form";
		const ITEMS_TYPE_DATASET = "dataset";
		
		const FILENAME_ICON = "icon_addon.png";
		const FILENAME_PREVIEW = "preview_addon";	//jpg,png,gif
		const FILENAME_ICON_SVG = "preview_icon.svg";
		
		const OPERATION_RENDER = "render";
		const OPERATION_CONFIG = "config";
		const OPERATION_EDIT = "edit";
		const OPERATION_WIDGET = "widget";
		
		private $id = null;
		private $isInited = false;
		private $title,$type,$html,$htmlItem,$htmlItem2,$css,$cssItem,$js,$updateHash;
		private $data, $config, $arrTemplates;
		private $params = array(),$paramsItems = array(), $options = array();
		private $name, $alias, $catid, $ordering, $isActive;
		private $includesCSS, $includesJS, $includesJSLib;
		private $hasItems, $itemsType,  $arrItems, $pathAssets, $urlAssets; 
		private $variablesItems = array(), $variablesMain = array();
		private $paramsCats = array();
		private $operations, $objProcessor;
		private $arrFonts, $arrAdminLabels, $arrHtmlConfigOptions;
		private $isInsideGrid = false, $objAddonType;
		private $pathAssetsBase, $urlAssetsBase;
		private $arrStoredData = array();
		private $operationType = null, $specialType;
		
		
		private static $arrCacheRecords = array();
		private static $arrCacheCats = null;
		private static $arrCacheCatsFull = null;
		private static $defaultOptions = null;
		
		
		/**
		 * 
		 * constructor
		 */
		public function __construct(){
			
			parent::__construct();
			
			$this->operations = new UCOperations();
			
			$this->objProcessor = new UniteCreatorParamsProcessor();
			$this->objProcessor->init($this);
			
			//get options settings
			if(self::$defaultOptions === null)
				$this->initDefaultOptions();
			
		}

		
		/**
		 * init default addon options from settings file
		 */
		private function initDefaultOptions(){
			
			$filepathAddonSettings = GlobalsUC::$pathSettings."addon_fields.php";
			require $filepathAddonSettings;
			
			self::$defaultOptions = $generalSettings->getArrValues();
			
			if(empty(self::$defaultOptions))
				self::$defaultOptions = array();
			
		}
		
		
		protected function a_______INIT_VALIDATE_____(){}
		
		
		/**
		 * 
		 * validate that the item inited
		 */
		public function validateInited(){
			if($this->isInited == false)
				UniteFunctionsUC::throwError("The addon is not inited!");
		}
		
		
		/**
		 * validate title
		 */
		private function validateTitle($title){
			
			UniteFunctionsUC::validateNotEmpty($title, "Widget Title");
			
		}
		
		
		/**
		 * validate addon name
		 */
		private function validateName($name){
			
			$fieldName = esc_html__("Widget Name", "unlimited-elements-for-elementor");
			
			UniteFunctionsUC::validateNotEmpty($name, $fieldName);
			UniteFunctionsUC::validateAlphaNumeric($name, $fieldName);
			
			$this->validateNameNotExists($name);
		}
		
		
		
		
		/**
		 * validate test data slot num
		 */
		private function validateTestSlot($num){
			$num = (int)$num;
			if($num < 0 || $num > 3)
				UniteFunctionsUC::throwError("Wrong test slot number: $num");
		}
		
		
		/**
		 * validate params before save or updata
		 * avoid doubles
		 */
		private function validateParams($arrParams, $type="main"){
			
			$arrParams = $this->objProcessor->initProcessParams($arrParams);
			
			$arrElementorTakenNames = array(
				"animation_duration"=>true
			);
			
			
			$arrTypes = array();
			
			$arrNames = array();
			foreach($arrParams as $param){
				
				$type = UniteFunctionsUC::getVal($param, "type");
				
				if($type == UniteCreatorDialogParam::PARAM_POSTS_LIST && isset($arrTypes[$type]))
					UniteFunctionsUC::throwError("There should be only one post list in attributes");
				
				
				$arrTypes[$type] = true;
				
				$name = UniteFunctionsUC::getVal($param, "name");
				if(empty($name))
					UniteFunctionsUC::throwError("Empty param name found");
				
				if(isset($arrNames[$name])){
					$message = "Duplicate $type attribute name found: <b> $name </b>";
					if(in_array($name, array("link","image","thumb","title","enable_link")))
						$message .= ". <br> The <b>$name</b> param is included in the image base params";
						
					UniteFunctionsUC::throwError($message);
				}
								
				//check for elementor taken name
				if(isset($arrElementorTakenNames[$name])){
					
					$message = "The attribute name: <b> $name </b> is taken by elementor built in attribute. Please use different name";
					UniteFunctionsUC::throwError($message);
				}
				
				$arrNames[$name] = true;
			}
			
		}
				
		
		/**
		 * check if addon exists by name
		 */
		public function isAddonExistsByName($name, $checkID = true){
			
			$name = $this->db->escape($name);
			
			$where = "name='$name'";
			
			if($checkID == true){
				if(!empty($this->id)){
					$addonID = $this->id;
					$where .= " and id<>".$this->id;
				}
			}
						
			$response = $this->db->fetch(GlobalsUC::$table_addons, $where);
			if(!empty($response))
				return(true);
			
			return(false);
		}
		
		
		/**
		 * validate that addon not exists by name
		 */
		private function validateNameNotExists($name){
			
			$isExists = $this->isAddonExistsByName($name);
			if($isExists == true)
				UniteFunctionsUC::throwError("The widget with name: $name already exists");
		}
		
		/**
		 * set loacation
		 */
		public function setOperationType($operation){
			
			$this->operationType = $operation;
			
		}
		
		/**
		 * init item by ID
		 */
		public function initByID($id){
			
			UniteFunctionsUC::validateNotEmpty($id, "widget id");
			
			$id = (int)$id;
			
			try{
				$record = $this->db->fetchSingle(GlobalsUC::$table_addons,"id={$id}");
							
			}catch(Exception $e){
				UniteFunctionsUC::throwError("Widget with ID: {$id} not found");
			}
			
			$this->initByDBRecord($record);
		}
		
		
		/**
		 * init addon by name, 
		 * for this function there is cache get
		 */
		public function initByName($name, $checkCache=true){
			
			try{
								
				//try to get from cache
				if($checkCache == true && array_key_exists($name, self::$arrCacheRecords) == true)
					$record = self::$arrCacheRecords[$name];
				else
					$record = $this->db->fetchSingle(GlobalsUC::$table_addons,array("name"=>$name));
				
				$this->initByDBRecord($record);
				
			}catch(Exception $e){
								
				UniteFunctionsUC::throwError("Widget with name:<b> {$name} </b> not found");
			}
			
		}

		
		/**
		 * init addon by name,
		 * for this function there is cache get
		 */
		public function initByAlias($alias, $type, $checkCache=true){
			
			if($type == GlobalsUC::ADDON_TYPE_REGULAR_ADDON)
				$type = "";
						
			try{
				
				$name = $alias;
				
				//fix double type, if used alias as name
				if(!empty($type)){
					
					$name = $alias."_".$type;
					$doubleType = $type."_".$type;
					
					if(strpos($name, $doubleType) !== false){
						$alias = str_replace("_".$doubleType, "", $name);
						$name = str_replace($doubleType, $type, $name);
					}
					
				}
				
				
				//try to get from cache
				if($checkCache == true && array_key_exists($name, self::$arrCacheRecords) == true)
					$record = self::$arrCacheRecords[$name];
				else{
					
					$record = $this->db->fetchSingle(GlobalsUC::$table_addons, array("alias"=>$alias,"addontype"=>$type));
					
				}
				
				$this->initByDBRecord($record);
		
			}catch(Exception $e){
				
				UniteFunctionsUC::throwError("Widget with name:<b> {$alias} </b> not found");
			}
		
		}
		
		
		/**
		 * init by name or alias
		 */
		public function initByMixed($name, $type = null){
			
			if($type == GlobalsUC::ADDON_TYPE_REGULAR_ADDON)
				$type = "";
			
			if(empty($type))
				$this->initByName($name);
			else
				$this->initByAlias($name, $type);
		}
		
		
		/**
		 * normalize includes array
		 */
		private function normalizeIncludeArray($arr){
						
			if(empty($arr))
				return(array());
			
			$newArr = array();
			foreach($arr as $item){
				if(is_string($item)){
					$item = trim($item);
					if(empty($item))
						continue;
				}else{			//in case of array
					$url = UniteFunctionsUC::getVal($item, "url");
					$url = trim($url);
					if(empty($url))
						continue;
					$item["url"] = $url;
				}
				
				$newArr[] = $item;
			}
			
			return($newArr);
		}
		
		
		/**
		 * find doubles in params on init
		 */
		private function initParamsFindDoubles($arrParams){
			
			if(!is_array($arrParams))
				return(array());
			
			if(empty($arrParams))
				return(array());
			
			$arrNames = array();
			foreach($arrParams as $key=>$param){
			
				if(is_array($param) == false)
					return($arrParams);
			
				$name = UniteFunctionsUC::getVal($param, "name");
				if(array_key_exists($name, $arrNames) == true)
					$arrParams[$key]["param_error"] = esc_html__("Double Name, please remove", "unlimited-elements-for-elementor");
				
				$arrNames[$name] = true;
			}
			
			return($arrParams);
		}
		
		
		/**
		 * parse json params from record
		 */
		private function parseJsonFromRecord($record, $name){
			
			$data = UniteFunctionsUC::getVal($record, $name);
			
			if(empty($data))
				return(array());
			
			if(is_array($data))
				return($data);
			
			if(is_object($data))
				return UniteFunctionsUC::convertStdClassToArray($data);
			
			$content = @json_decode($data);
						
			if(empty($content))
				return($data);

			
			return UniteFunctionsUC::convertStdClassToArray($content);
			
		}
		
		
		/**
		 * get options path
		 */
		private function initAssetsPath(){
			
			$path = $this->getOption("path_assets");
						
			if(empty($path))
				return("");
							
			$pathAbsolute = UniteFunctionsUC::joinPaths($this->pathAssetsBase, $path);
						
			$isUnderAssets = HelperUC::isPathUnderAssetsPath($pathAbsolute, $this->objAddonType);
			
			if($isUnderAssets == false)
				return("");
			
			if(is_dir($pathAbsolute) == false)
				return("");
			
			return($path);
		}
		
		
		/**
		 * get the items type on init
		 */
		private function initItemsType(){
			
			foreach($this->paramsItems as $param){
				$type = UniteFunctionsUC::getVal($param, "type");
				if($type == "uc_imagebase")
					return(self::ITEMS_TYPE_IMAGE);
			}
			
			
			return(self::ITEMS_TYPE_DEFAULT);
		}


		
		/**
		 * convert includes array to full url
		 */
		private function arrIncludesToFullUrl($arrIncludes){
			
			foreach($arrIncludes as $key => $include){
				if(is_string($include))
					$include = HelperUC::URLtoFull($include, $this->urlAssetsBase);
				else{
					$url = UniteFunctionsUC::getVal($include, "url");
					if(!empty($url))
						$include["url"] = HelperUC::URLtoFull($url, $this->urlAssetsBase);
				}
				
				$arrIncludes[$key] = $include;
			}
			
			
			return($arrIncludes);
		}
		
		
		/**
		 * init addon options
		 * 
		 */
		private function initAddonOptions($arrOptions){
			
			if(empty($arrOptions))
				$arrOptions = array();
			
			$arrOptions = array_merge(self::$defaultOptions, $arrOptions);
			
			
			return($arrOptions);
		}
		
		
		/**
		 * get special items type accordign the params
		 */
		protected function getItemsSpecialType(){
			
		    foreach($this->params as $param){
		    	
		        $type = UniteFunctionsUC::getVal($param, "type");
		        
		        switch($type){
		        	case UniteCreatorDialogParam::PARAM_POSTS_LIST:
		            	return("post");
		            break;
		        	case UniteCreatorDialogParam::PARAM_INSTAGRAM:
		        		return("instagram");
		        	break;
		        	case UniteCreatorDialogParam::PARAM_FORM:
		        		return self::ITEMS_TYPE_FORM;
		        	break;
		        	case UniteCreatorDialogParam::PARAM_DATASET:
		        		return self::ITEMS_TYPE_DATASET;
		        	break;
		        	case UniteCreatorDialogParam::PARAM_LISTING:
												
			        	$useFor = UniteFunctionsUC::getVal($param, "use_for");
						
			        	if($useFor == "remote")
			        		continue(2);

			        	if($useFor == "items")
			        		return("multisource");
			        	
			        	return("listing");
		        				        		
		        	break;
		        }
		        
		    }
		    
		    return(null);
		}
		
		
		/**
		 * modify after init settings
		 */
		protected function modifyAfterInit(){
		    			
			//set spacial items type if exists
			$specialType = $this->getItemsSpecialType();
			
			$this->specialType = $specialType;
			
			if(!empty($specialType)){
				
				$this->itemsType = $specialType;
								
			    if($specialType == self::ITEMS_TYPE_FORM || 
			    	$specialType == self::ITEMS_TYPE_DATASET || 
			    	$specialType == "listing" || 
			    	$specialType == "multisource" || 
			    	$specialType == self::ITEMS_TYPE_IMAGE){
			    	
			    	$this->hasItems = true;
			    	
			    }else{
		            $this->hasItems = false;
					$this->options["enable_items"] = false;
			    }
				
			}
			
			
			//add image fields			
			if($specialType == self::ITEMS_TYPE_IMAGE){
				
				if($this->operationType == self::OPERATION_WIDGET){
		            $this->hasItems = false;
					$this->options["enable_items"] = false;
				}
				
				
				$paramImageBase = array();
				$paramImageBase["type"] = "uc_imagebase";
				$paramImageBase["name"] = "imagebase_fields";
				$paramImageBase["title"] = "Image Base Fields";
				
				$this->paramsItems[] = $paramImageBase;
			}
			
		    
		}
		
		
		/**
		 *
		 * init item by db record
		 */
		public function initByDBRecord($record){
						
			//cache db record
			$addonName = UniteFunctionsUC::getVal($record, "name");
			self::$arrCacheRecords[$addonName] = $record;
			
			UniteFunctionsUC::validateNotEmpty($record, "The Widget not exists");
			
			$this->isInited = true;
			
			$this->data = $record;
			
			$this->id = UniteFunctionsUC::getVal($record, "id");
			
			$this->title = UniteFunctionsUC::getVal($record, "title");
			$this->name = UniteFunctionsUC::getVal($record, "name");
			$this->alias = UniteFunctionsUC::getVal($record, "alias");
			$this->catid = UniteFunctionsUC::getVal($record, "catid");
			$this->ordering = (int)UniteFunctionsUC::getVal($record, "ordering");
			$this->isActive = (int)UniteFunctionsUC::getVal($record, "is_active");
			$this->type = UniteFunctionsUC::getVal($record, "addontype");
			$this->updateHash = UniteFunctionsUC::getVal($record, "test_slot1");
			
			if(is_string($this->updateHash) == false || strlen($this->updateHash) > 60)
				$this->updateHash = "";
			
			//get templates
			$this->arrTemplates = $this->parseJsonFromRecord($record, "templates");
			
			if(!empty($this->arrTemplates)){
				
				$this->html = UniteFunctionsUC::getVal($this->arrTemplates, "html");
				$this->htmlItem = UniteFunctionsUC::getVal($this->arrTemplates, "html_item");
				$this->htmlItem2 = UniteFunctionsUC::getVal($this->arrTemplates, "html_item2");
				
				$this->css = UniteFunctionsUC::getVal($this->arrTemplates, "css");
				$this->cssItem = UniteFunctionsUC::getVal($this->arrTemplates, "css_item");
				$this->js = UniteFunctionsUC::getVal($this->arrTemplates, "js");
				
			}else{		//get data the old way
				
				$this->html = UniteFunctionsUC::getVal($record, "html");
				$this->htmlItem = UniteFunctionsUC::getVal($record, "html_item");
				$this->css = UniteFunctionsUC::getVal($record, "css");
				$this->js = UniteFunctionsUC::getVal($record, "js");
				
				$this->arrTemplates = array();
				$this->arrTemplates["html"] = $this->html;
				$this->arrTemplates["html_item"] = $this->htmlItem;
				$this->arrTemplates["css"] = $this->css;
				$this->arrTemplates["js"] = $this->js;
			}
			
			
			$arrIncludes = array();
			
			$arrConfig = $this->parseJsonFromRecord($record, "config");
			
			$this->config = $arrConfig;
			
			if(!empty($arrConfig)){
				
				$this->params = $this->parseJsonFromRecord($arrConfig, "params");
				
				$this->paramsItems = $this->parseJsonFromRecord($arrConfig, "params_items");
				
				$this->options = UniteFunctionsUC::getVal($arrConfig, "options");
								
				$arrIncludes = UniteFunctionsUC::getVal($arrConfig, "includes");
				
				$this->variablesItems = UniteFunctionsUC::getVal($arrConfig, "variables_item");
				if(empty($this->variablesItems))
					$this->variablesItems = array();
				
				$this->variablesMain = UniteFunctionsUC::getVal($arrConfig, "variables_main");
				if(empty($this->variablesMain))
					$this->variablesMain = array();
				
				$this->paramsCats = UniteFunctionsUC::getVal($arrConfig, "params_cats");
				if(empty($this->paramsCats))
					$this->paramsCats = array();	
				
			}else{		//get old fashion
				
				$this->params = $this->parseJsonFromRecord($record, "params");
				$this->paramsItems = $this->parseJsonFromRecord($record, "params_items");
				$this->options = $this->parseJsonFromRecord($record, "options");
				
				$jsonIncludes = UniteFunctionsUC::getVal($record, "includes");
				if(!empty($jsonIncludes))
					$arrIncludes = json_decode($jsonIncludes);
			}
					
			$this->options = $this->initAddonOptions($this->options);
									
			
			//check params for doubles
			$this->params = $this->initParamsFindDoubles($this->params);
			$this->paramsItems = $this->initParamsFindDoubles($this->paramsItems);
			
			//process params items
			$this->paramsItems = $this->operations->checkAddParamTitle($this->paramsItems);

			//set assets path
			$objAddonType = $this->getObjAddonType();
			$this->pathAssetsBase = HelperUC::getAssetsPath($objAddonType);
			$this->urlAssetsBase = HelperUC::getAssetsUrl($objAddonType);
			
			$this->pathAssets = $this->initAssetsPath();
			
			if($this->pathAssets)
				$this->urlAssets = $this->urlAssetsBase.$this->pathAssets."/";
			
			//init if has items
			$enableItems = $this->getOption("enable_items");
			$this->hasItems = UniteFunctionsUC::strToBool($enableItems);
						
			if($this->hasItems)
			     $this->itemsType = $this->initItemsType();
			
			//parse includes
			if(!empty($arrIncludes)){
				
				$arrIncludes = UniteFunctionsUC::convertStdClassToArray($arrIncludes);
				
				$this->includesJS = UniteFunctionsUC::getVal($arrIncludes, "js", array());
				$this->includesJSLib = UniteFunctionsUC::getVal($arrIncludes, "jslib", array());
				$this->includesCSS = UniteFunctionsUC::getVal($arrIncludes, "css", array());
				
				$this->includesJS = $this->arrIncludesToFullUrl($this->includesJS);
				$this->includesCSS = $this->arrIncludesToFullUrl($this->includesCSS);
			}
			
			$this->includesJS = $this->normalizeIncludeArray($this->includesJS);
			$this->includesCSS = $this->normalizeIncludeArray($this->includesCSS);
			$this->includesJSLib = $this->normalizeIncludeArray($this->includesJSLib);			
						
			$this->modifyAfterInit();
						
			//set default data
			$this->setValuesFromDefaultData();
			
			
		}
		
		
		protected function a_________GETTERS_________(){}
		
		/**
		 * get the update hash if available
		 */
		public function getUpdateHash(){
			
			$this->validateInited();
			
			return($this->updateHash);
		}
		
		/**
		 * get addon type object
		 */
		public function getObjAddonType(){
			
			$this->validateInited();
			
			if(!empty($this->objAddonType))
				return $this->objAddonType;
			
			$this->objAddonType = UniteCreatorAddonType::getAddonTypeObject($this->type);
			
			return($this->objAddonType);
		}
		
		
		/**
		 * get html template
		 * @param $isSpecialChars
		 */
		private function getHtmlTemplate($html, $isSpecialChars = false){
			
			$this->validateInited();
			
			if($isSpecialChars == true)
				return(htmlspecialchars($html));
			
			return($html);
		}
		
		
		public function getTitle($isSpecialChars = false){
			return $this->getHtmlTemplate($this->title, $isSpecialChars);
		}
		
		public function getHtml($isSpecialChars = false){
			return $this->getHtmlTemplate($this->html, $isSpecialChars);
		}
		
				
		public function getHtmlItem($isSpecialChars = false){
			return $this->getHtmlTemplate($this->htmlItem, $isSpecialChars);
		}
		
		public function getHtmlItem2($isSpecialChars = false){
			return $this->getHtmlTemplate($this->htmlItem2, $isSpecialChars);
		}
				
		public function getCss($isSpecialChars = false){
			return $this->getHtmlTemplate($this->css, $isSpecialChars);
		}
		
		public function getCssItem($isSpecialChars = false){
			return $this->getHtmlTemplate($this->cssItem, $isSpecialChars);
		}
		
		public function getJs($isSpecialChars = false){
			return $this->getHtmlTemplate($this->js, $isSpecialChars);
		}
		
				
		/**
		 * return ID
		 */
		public function getID(){
			return($this->id);
		}
		
		/**
		 * get addon type
		 */
		public function getType(){
		
			return($this->type);
		}
		
		
		/**
		 * get if addon is active
		 */
		public function getIsActive(){
			
			return($this->isActive);
		}
		
		/**
		 * 
		 * get name
		 */
		public function getName(){
			return($this->name);
		}
		
		/**
		 * get alias
		 */
		public function getAlias(){
			
			return($this->alias);
		}
		
		
		/**
		 * get name or alias according the type
		 */
		public function getNameByType(){
			
			if(empty($this->type))
				return($this->name);
			
			return($this->alias);
		}
		
		
		/**
		 * get description
		 */
		public function getDescription($isSpecialChars = false){
			
			$description = $this->getOption("description");
			
			return $this->getHtmlTemplate($description, $isSpecialChars);
		}		
		
		
		/**
		 * get icon url if exists
		 */
		public function getUrlIcon(){
			
			$showIcon = $this->getOption("show_small_icon");
			$showIcon = UniteFunctionsUC::strToBool($showIcon);
			
			if($showIcon == false)
				return(null);
			
			$urlIcon = GlobalsUC::$url_default_addon_icon;
			
			$pathAssets = $this->getPathAssetsFull();
			
			if(empty($pathAssets))
				return($urlIcon);
			
			$filepathIcon = $pathAssets.self::FILENAME_ICON;
			if(file_exists($filepathIcon) == false)
				return($urlIcon);
			
			$urlAssets = $this->getUrlAssets();
			
			$urlIcon = $urlAssets.self::FILENAME_ICON;
			
			return($urlIcon);
		}
		
		/**
		 * get svg url preview
		 */
		private function getUrlPreview_svg(){
			
			$svgContent = $this->getHtml();
			$svgContent = trim($svgContent);
			if(empty($svgContent))
				return(null);
			
			$urlPreview = UniteFunctionsUC::encodeSVGForBGUrl($svgContent);
			
			return($urlPreview);
		}
		
		
		/**
		 * get default preview url
		 */
		public function getDefaultUrlPreview(){
			
			$objAddonType = $this->getObjAddonType();
			
			$typeName = $objAddonType->typeName;
			
			//get default preview
			$filenameDefaultPreview = self::FILENAME_PREVIEW."_$typeName.jpg";
			$filepathDefaultPreview = GlobalsUC::$pathPlugin."images/".$filenameDefaultPreview;
			
			$urlPreview = null;
			if(file_exists($filepathDefaultPreview))
				$urlPreview = GlobalsUC::$urlPlugin."images/".$filenameDefaultPreview;
			
			return($urlPreview);
		}
		
		
		/**
		 * get preview url
		 */
		public function getUrlPreview($returnFilepath = false, $getDefault = true){
			
			$this->validateInited();
			
			$objAddonType = $this->getObjAddonType();
			
			$typeName = $objAddonType->typeName;
						
			if($objAddonType->isSVG == true){
				$urlPreview = $this->getUrlPreview_svg();
				
				return($urlPreview);
			}
			
			if($getDefault == true)
				$urlPreview = $this->getDefaultUrlPreview();
			else
				$urlPreview = null;
			
			$pathAssets = $this->getPathAssetsFull();
			
			if(empty($pathAssets))
				return($urlPreview);
			
			$arrExt = array("jpg","png","gif");
			foreach($arrExt as $ext){
				$filename = self::FILENAME_PREVIEW.".".$ext;
				$filepathPreview = $pathAssets.$filename;
				if(file_exists($filepathPreview)){
					
					if($returnFilepath == true)
						return($filepathPreview);
					
					$urlAssets = $this->getUrlAssets();
					$urlPreview = $urlAssets.$filename;
					return($urlPreview);
				}
			}
			
			return($urlPreview);
		}
		
		
		/**
		 * get svg icon
		 */
		public function getUrlSvgIconForEditor(){
			
			$this->validateInited();
			
			$pathAssets = $this->getPathAssetsFull();
			
			if(empty($pathAssets))
				return(null);
			
			$filepathIcon = $pathAssets.self::FILENAME_ICON_SVG;
			
			if(file_exists($filepathIcon) == false)
				return(null);
				
			$urlAssets = $this->getUrlAssets();
			
			if(empty($urlAssets))
				return(null);
			
			$urlIcon = $urlAssets.self::FILENAME_ICON_SVG;
			
			return($urlIcon);
		}
		
		
		/**
		 * filter params array by type
		 */
		protected function filterParamsByType($params, $filterType){
			
				$arrFiltered = array();
				foreach($params as $param){
					$type = UniteFunctionsUC::getVal($param, "type");
					
					if(is_array($filterType)){			//multiple filters
						if(in_array($type, $filterType))
							$arrFiltered[] = $param;
						
					}else{								//single line filter
						if($type == $filterType)
							$arrFiltered[] = $param;
					}
				}
				
				return($arrFiltered);
			
		}
		
		
		/**
		 * get params
		 */
		public function getParams($filterType = null){	
			
			if(empty($this->params))
				return($this->params);
			
			//return filteres params
			if(!empty($filterType))
				return $this->filterParamsByType($this->params, $filterType);
			
			return($this->params);
		}
		
		
		/**
		 * get array of default values assoc
		 */
		public function getParamsDefaultValuesAssoc(){
			
			$arrDefaults = array();
			
			foreach($this->params as $param){
				
				if(array_key_exists("default_value", $param) == false)
					continue;

				$name = UniteFunctionsUC::getVal($param, "name");
				$defaultValue = UniteFunctionsUC::getVal($param, "default_value");
				
				$arrDefaults[$name] = $defaultValue;
				
			}
			
			return($arrDefaults);
		}

		
		/**
		 * get items params
		 */
		public function getParamsItems($filterType = null){
			
			if(empty($this->paramsItems))
				return($this->paramsItems);
			
			//return filteres params
			if(!empty($filterType))
				return $this->filterParamsByType($this->paramsItems, $filterType);
				
			return($this->paramsItems);
		}
		
		
		/**
		 * get addon optinos
		 */
		public function getOptions(){
		   	
		   	
			return($this->options);
		}
		
		
		/**
		 * return if the addon has items
		 */
		public function isHasItems(){
						
			return($this->hasItems);
		}
		
		/**
		 * get listing type - if exists
		 */
		public function getListingTypes(){
			
			$paramsDynamic = $this->getParams(UniteCreatorDialogParam::PARAM_LISTING);
			
			if(empty($paramsDynamic))
				return(array());
			
			$arrTypes = array();
			
			foreach($paramsDynamic as $param){
				$useFor = UniteFunctionsUC::getVal($param, "use_for");
				$arrTypes[] = $useFor;
			}
						
			if(empty($arrTypes))
				return(array());
				
			return($arrTypes);
		}
		
		
		/**
		 * check if has remote
		 */
		public function hasRemote(){
			
			$arrTypes = $this->getListingTypes();
						
			return(in_array("remote",$arrTypes));
		}
		
		
		/**
		 * check if has remote
		 */
		public function hasMultisource(){
			
			$arrTypes = $this->getListingTypes();
			
			return(in_array("items",$arrTypes));
		}
		
		
		/**
		 * get special type
		 */
		public function getSpecialType(){
			
			return($this->specialType);
		}
		
		/**
		 * get items type like image / default
		 */
		public function getItemsType(){
			
			return($this->itemsType);
		}
		
		/**
		 * return if has simple items, or multisource not with post list
		 */
		public function isHasSimpleItems(){
			
			if($this->hasItems == false)
				return(false);
			
			//has items
				
			if($this->specialType == "multisource")
				return(true);
			
			//other types - false
			
			if(!empty($this->specialType))
				return(false);
				
			
			return(true);
		}
		
		
		/**
		 * get option
		 */
		public function getOption($name){
			$value = UniteFunctionsUC::getVal($this->options, $name);
			return($value);
		}
		
		
		/**
		 * get category id
		 */
		public function getCatID(){
			return($this->catid);
		}
		
		
		/**
		 * get categories array
		 */
		private function getArrCats(){
		
			$this->validateInited();
		
			if(self::$arrCacheCats !== null)
				return(self::$arrCacheCats);
			
			$objCats = new UniteCreatorCategories();
						
			self::$arrCacheCats = $objCats->getCatsShort("", "all");
			
			return(self::$arrCacheCats);
		}
		
		
		/**
		 * get categories array
		 */
		private function getArrCatsFull(){
		
			$this->validateInited();
		
			if(self::$arrCacheCatsFull !== null)
				return(self::$arrCacheCatsFull);
			
			$objCats = new UniteCreatorCategories();
			$arrCats = $objCats->getArrCats($this->type);
			
			self::$arrCacheCatsFull = UniteFunctionsUC::arrayToAssoc($arrCats,"id");
			
			return(self::$arrCacheCatsFull);
		}
		
		
		/**
		 * get category title
		 */
		public function getCatTitle(){
			
			$catID = $this->catid;
		
			if(empty($catID))
				return("");
		
			$arrCats = $this->getArrCats();
			
			$catTitle = UniteFunctionsUC::getVal($arrCats, $catID);
			
			return($catTitle);
		}
		
		/**
		 * 
		 * get font icon from options
		 */
		public function getFontIcon(){
			
			$icon = $this->getOption("addon_icon");
			
			return($icon);
		}
		
		
		/**
		 * get category icon
		 */
		public function getCatIcon(){
			
			$catID = $this->catid;
		
			if(empty($catID))
				return("");
			
			$arrCats = $this->getArrCatsFull();
			
			$arrCat = UniteFunctionsUC::getVal($arrCats, $catID);
			if(empty($arrCat))
				return("");
			
			$params = UniteFunctionsUC::getVal($arrCat, "params");
			
			$icon = UniteFunctionsUC::getVal($params, "icon");
			
			return($icon);
		}
		
		
		/**
		 * get font icon combined of addon
		 */
		public function getFontIconCombined(){
			
			$icon = $this->getFontIcon();
			
			if(!empty($icon))
				return($icon);
			
			$catIcon = $this->getCatIcon();
			
			return($catIcon);
		}
		
		
		/**
		 * get short array
		 */
		public function getArrShort($includeImages = false){
			
			$this->validateInited();
			
			$arr = array();
			$arr["id"] = $this->id;
			$arr["name"] = $this->name;
			$arr["alias"] = $this->alias;
			$arr["title"] = $this->title;
			
			$arr["description"] = $this->getOption("description");
			
			if($includeImages == false)
				return($arr);
			
			//get images
			
			$objAddonType = $this->getObjAddonType();
			
			$arr["is_svg"] = $objAddonType->isSVG;
			$arr["preview"] = $this->getUrlPreview();
			$arr["icon"] = $this->getUrlIcon();
			
			return($arr);
		}
		
		
		/**
		 * get assets path - relative
		 */
		public function getPathAssetsFull(){
			
			$pathAssetsGlobals = trim($this->pathAssetsBase);
			
			if(empty($pathAssetsGlobals))
				return(null);
			
			$path = UniteFunctionsUC::joinPaths($pathAssetsGlobals, $this->pathAssets);
			
			$path = UniteFunctionsUC::addPathEndingSlash($path);
						
			return($path);
		}
		
		
		/**
		 * return assets path (relative to main assets path)
		 */
		public function getPathAssets(){
			
			return($this->pathAssets);
		}
		
		/**
		 * get base assets path
		 */
		public function getPathAssetsBase(){
			$this->validateInited();
			
			return($this->pathAssetsBase);
		}
		
		/**
		 * get assets url
		 */
		public function getUrlAssets(){
			
			return($this->urlAssets);
		}
		
		
		/**
		 * get item variables
		 */
		public function getVariablesItem(){
			return($this->variablesItems);
		}
		
		
		/**
		 * get item variables
		 */
		public function getVariablesMain(){
			
			return($this->variablesMain);
		}
		
		/**
		 * get params categories
		 */
		public function getParamsCats(){
			
			return($this->paramsCats);
		}
		
		/**
		 * get config
		 */
		public function getConfig(){
			return $this->config;
		}
		
		
		/**
		 * get templates html
		 */
		public function getTemplates(){
			return($this->arrTemplates);
		}
		
		
		/**
		 * get addon row data
		 */
		public function getRowData(){
			
			return($this->data);
		}
		
		/**
		 * get fonts array
		 */
		public function getArrFonts(){
			return($this->arrFonts);
		}
		
		
		/**
		 * get array of admin labels (params names)
		 */
		public function getAdminLabels(){
			
			if(!empty($this->arrAdminLabels))
				return($this->arrAdminLabels);
			
			$this->arrAdminLabels = array();
			
			$params = $this->params;
			
			foreach($params as $param){
										
				$isAdminLabel = UniteFunctionsUC::getVal($param, "admin_label");
				$isAdminLabel = UniteFunctionsUC::strToBool($isAdminLabel);
				
				if($isAdminLabel == true){
					
					$name = UniteFunctionsUC::getVal($param, "name");
					$title = UniteFunctionsUC::getVal($param, "title");
					
					$this->arrAdminLabels[] = array($name, $title);
				}
				
			}
			
			if(!empty($this->arrAdminLabels))
				return($this->arrAdminLabels);
			
			
			$firstTextParam = "";		
			$firstNumberParam = "";
			$firstGoodParam = "";
			
			//get most suitable param
			foreach($params as $param){
				
				$type = UniteFunctionsUC::getVal($param, "type");
				$name = UniteFunctionsUC::getVal($param, "name");
				$title = UniteFunctionsUC::getVal($param, "title");
				
				switch($type){
					case UniteCreatorDialogParam::PARAM_TEXTFIELD:
						if(empty($firstTextParam))
							$firstTextParam = array($name,$title);
					break;
					case UniteCreatorDialogParam::PARAM_EDITOR:
					case UniteCreatorDialogParam::PARAM_TEXTAREA:
						$this->arrAdminLabels[] = array($name,$title);
						return($this->arrAdminLabels);
					break;
					case UniteCreatorDialogParam::PARAM_DROPDOWN:
					case UniteCreatorDialogParam::PARAM_RADIOBOOLEAN:
						if(!empty($firstGoodParam))
							$firstGoodParam = array($name,$title);
					break;
					case UniteCreatorDialogParam::PARAM_NUMBER:
						if(!empty($firstNumberParam))
							$firstNumberParam = array($name,$title);
					break;
				}
				
			}
			
			//select the param by priority
			
			$selectedParam = "";
			
			if(!empty($firstTextParam))
					$selectedParam = $firstTextParam;
			else 
				if(!empty($firstNumberParam))
					$selectedParam = $firstNumberParam;
			else
				if(!empty($firstGoodParam))
					$selectedParam = $firstGoodParam;
			
			if(!empty($selectedParam))
				$this->arrAdminLabels[] = $selectedParam;
			
			if(!empty($this->arrAdminLabels))
				return($this->arrAdminLabels);
				
			//if still empty - get by number of items
			if($this->isHasItems()){
				$this->arrAdminLabels[] = array("uc_num_items", esc_html__("Items", "unlimited-elements-for-elementor"));
			}
			
			
			return($this->arrAdminLabels);
		}
		
		
		/**
		 * check if some attribute type exists
		 */
		private function isAttributeTypeExists($arrParams, $type){
			
			foreach($arrParams as $param){
			
				$paramType = UniteFunctionsUC::getVal($param, "type");
				if($paramType == $type)
					return(true);
			}
			
			return(false);
		}
		
		
		/**
		 * get some param by type
		 */
		public function getParamByType($type){
			
			$arrParams = $this->params;
			
			foreach($arrParams as $param){
			
				$paramType = UniteFunctionsUC::getVal($param, "type");
				if($paramType == $type)
					return($param);
			}
			
			return(null);
		}
		
		/**
		 * get listing param for addon output
		 * get only listing / gallery and multisource. skup the remote
		 */
		public function getListingParamForOutput(){
			
			$arrParams = $this->params;
			
			foreach($arrParams as $param){
				
				$paramType = UniteFunctionsUC::getVal($param, "type");
				if($paramType != UniteCreatorDialogParam::PARAM_LISTING)
					continue;

				$useFor = UniteFunctionsUC::getVal($param, "use_for");
				
				if($useFor == "remote")
					continue;
								
				return($param);
			}
			
			return(null);
		}
		
		/**
		 * get param by name
		 */
		public function getParamByName($name){
			
			$arrParams = $this->params;
			
			foreach($arrParams as $param){
			
				$paramName = UniteFunctionsUC::getVal($param, "name");
				if($paramName == $name)
					return($param);
			}
			
			return(null);
			
		}
		
		/**
		 * get params key->type assoc array
		 */
		public function getParamsTypes($isItems = false){
			
			$this->validateInited();
			
			if($isItems == false)
				$params = $this->params;
			else 
				$params = $this->paramsItems;
			
			$arrTypes = array();
			foreach($params as $param){
				$name = UniteFunctionsUC::getVal($param, "name");
				$type = UniteFunctionsUC::getVal($param, "type");
				$arrTypes[$name] = $type;
			}
			
			return($arrTypes);
		}
		
		
		/**
		 * check if exists editor attribute
		 */
		public function isEditorMainAttributeExists(){
			
			$isExists = $this->isAttributeTypeExists($this->params, UniteCreatorDialogParam::PARAM_EDITOR);
			
			return($isExists);
		}
		
		
		/**
		 * check if exists editor attribute
		 */
		public function isEditorItemsAttributeExists(){
			
			if($this->hasItems == false)
				return(false);
			
			$isExists = $this->isAttributeTypeExists($this->paramsItems, UniteCreatorDialogParam::PARAM_EDITOR);
			
			return($isExists);
		}
		
		/**
		 * check if addon exists in catalog
		 */
		public function isExistsInCatalog(){
			
			$this->validateInited();
			
			$webAPI = new UniteCreatorWebAPI();
			$isExists = $webAPI->isAddonExistsInCatalog($this->name);
			
			return($isExists);
		}
		
		
		/**
		 * get number of items
		 */
		public function getNumItems(){
			
			if(empty($this->arrItems))
				return(0);
			
			$numItems = count($this->arrItems);
			
			return($numItems);
		}
		
		
		/**
		 * check if some param exists
		 */
		public function isParamExists($paramName, $isMain = true){
			
			$this->validateInited();
			
			if($isMain)
				$arrParams = $this->params;
			else
				$arrParams = $this->paramsItems;
			
			foreach($arrParams as $param){
				$name = UniteFunctionsUC::getVal($param, "name");
				if($name == $paramName)
					return(true);
			}
			
			return(false);
		}
		
		/**
		 * check if param type exists
		 */
		public function isParamTypeExists($type){
			
			$param = $this->getParamByType($type);
			
			if(empty($param))
				return(false);
			
			return(true);
		}
		
		/**
		 * get param position
		 */
		public function getParamPosition($paramName, $isMain){
			
			if($isMain)
				$arrParams = $this->params;
			else
				$arrParams = $this->paramsItems;
			
			foreach($arrParams as $index => $param){
				$name = UniteFunctionsUC::getVal($param, "name");
				if($name == $paramName)
					return($index);
			}
			
			$paramType = $isMain?"main":"items";
			UniteFunctionsUC::throwError("Param: {$paramName} don't exist in $paramType params");
		}
		
		/**
		 * get processor
		 */
		public function getObjProcessor(){
			
			return($this->objProcessor);
		}
		
		
		/**
		 * get data by key
		 */
		public function getStoredData($key){
			
			$data = UniteFunctionsUC::getVal($this->arrStoredData, $key);
			
			return($data);
		}
		
		
		private function a_______GET__INCLUDES_____(){}
		
		/**
		 * get js includes array
		 */
		public function getJSIncludes(){
			
			return($this->includesJS);
		}
		
		
		/**
		 * get includes of js libraries
		 */
		public function getJSLibIncludes(){
			
			return($this->includesJSLib);
		}
		
		
		/**
		 * get js includes dependancies
		 */
		public function getIncludesJsDependancies(){
			
			$this->validateInited();
			
			if(empty($this->includesJSLib))
				return(array());
			
			$arrDep = array();
			
			foreach($this->includesJSLib as $name){
				
				if($name == "jquery")
					$arrDep[] = $name;
			}
			
			
			return($arrDep);
		}
		
		/**
		 * get array of library inlcudes url's
		 */
		public function getArrLibraryIncludesUrls($processProvider){
			
			$this->validateInited();
			
			$operations = new UCOperations();
			
			$arrJsIncludes = array();
			$arrCssIncludes = array();
			
			$objLibrary = new UniteCreatorLibrary();
						
			foreach($this->includesJSLib as $libName){
				
				//maybe disable font awesome, if set in general setting
				
				if($libName == "font-awsome"){
					$optionDisable = HelperProviderCoreUC_EL::getGeneralSetting("force_disable_font_awesome");
					if($optionDisable == "disable")
						continue;
				}
				
				//process provider library instead of get files
				if($processProvider == true){
					$isProcessed = $objLibrary->processProviderLibrary($libName);
					
					if($isProcessed == true)
						continue;
				}
				
				$response = $objLibrary->getLibraryIncludes($libName);
								
				$arrJs = $response["js"];
				$arrCss = $response["css"];
				$arrJsIncludes = array_merge($arrJsIncludes, $arrJs);
				$arrCssIncludes = array_merge($arrCssIncludes, $arrCss);
			}

						
			$output = array();
			$output["js"] = $arrJsIncludes;
			$output["css"] = $arrCssIncludes;
			return($output);
		}
		
		
		/**
		 * get css includes array
		 */
		public function getCSSIncludes(){
			
			return($this->includesCSS);
		}
		
		/**
		 * get js and css in one array without library
		 */
		public function getAllRegularIncludesUrls(){
			
			$arrMerged = array_merge($this->includesCSS, $this->includesJS);
			
			$arrUrls = array();
			foreach($arrMerged as $arrInclude){
				if(is_string($arrInclude))
					$arrUrls[] = $arrInclude;
				$url = UniteFunctionsUC::getVal($arrInclude, "url");
				$arrUrls[] = $url;
			}
			
			return($arrUrls);
		}
		
		private function a______GET_HTML______(){}
				
		
		
		/**
		 * get addon config html
		 * for vc make another function - get config only
		 * params - source=addon
		 */
		public function getHtmlConfig($putMode = false, $isOutputSidebar = false, $options = array()){
			
			$this->validateInited();
			
			$this->arrHtmlConfigOptions = $options;
			
			$arrParams = $this->objProcessor->processParamsForOutput($this->params);
						
			//add config
			$objSettings = new UniteCreatorSettings();
			
			$source = UniteFunctionsUC::getVal($this->arrHtmlConfigOptions, "source");
			if($source == "addon"){
				$objSettings->setCurrentAddon($this);
				$objSettings->addGlobalParam("source", "addon", UniteSettingsUC::TYPE_IMAGE);
			}
			
			//choose if add items chooser
			
			if(!empty($this->params) || $this->hasItems){
				
				$objSettings->addSap(esc_html__("General","unlimited-elements-for-elementor"),"config",true);
				
				if($this->hasItems == true){
					
					if($this->itemsType == self::ITEMS_TYPE_IMAGE && $isOutputSidebar == true){
						
						$objSettings->addGallery("uc_items","",__("Select Images","unlimited-elements-for-elementor"));
						
					}else{
						
						$objSettings->addItemsPanel($this, $source);
						$objSettings->addHr("after_items_hr");
						
					}
										
				}
				
				$objSettings->initByCreatorParams($arrParams);
			}
			
			//add repeater
			/*
			 * add repeater
			if($this->hasItems == true){
				$objSettings->addSap(esc_html__("Edit Items","unlimited-elements-for-elementor"),"items");
				$objSettings->addItemsPanelRepeater($this, $source);
			}
			*/
				
			//add fonts
			$isFontsPanelEnabled = $this->objProcessor->isFontsPanelEnabled();
			$arrFontParamNames = $this->objProcessor->getAllParamsNamesForFonts();
			
			if(empty($arrFontParamNames))
				$isFontsPanelEnabled = false;

			//disable fonts panel by setting
			
			$isDisableFonts = UniteFunctionsUC::getVal($this->arrHtmlConfigOptions, "disable_fonts");
			$isDisableFonts = UniteFunctionsUC::strToBool($isDisableFonts);
			if($isDisableFonts == true)
				$isFontsPanelEnabled = false;

			
			if($isFontsPanelEnabled == true){
				
				$objSettings->addSap(esc_html__("Fonts","unlimited-elements-for-elementor"),"fonts");
												
				$arrFontsData = $this->getArrFonts();
				
				
				$fontsPanelOptions = array();
				if($this->isInsideGrid)
					$fontsPanelOptions["inside_grid"] = true;
				
				$objSettings->addFontPanel($arrFontParamNames, $arrFontsData,null,$fontsPanelOptions);
			}
			
			
			$numSettings = $objSettings->getNumSettings();
			
			if($numSettings == 0){
				$textEmpty = esc_html__("no settings for this widget", "unlimited-elements-for-elementor");
				
				$objSettings->addStaticText($textEmpty);
			}
			
			//output
			
			if($isOutputSidebar == false){
				$objOutput = new UniteSettingsOutputWideUC();
				$objOutput->setShowSaps(true);
				
			}else {
				$objOutput = new UniteSettingsOutputSidebarUC();
			}
			
			$objOutput->init($objSettings);
			
			if($putMode == true){
				$objOutput->draw("uc_form_settings_addon", false);
			}else{
				
				ob_start();
				$objOutput->draw("uc_form_settings_addon", false);
				$html = ob_get_contents();
				ob_clean();
				return($html);
			}
		
		}
		
		/**
		 * return if fonts panel enabled
		 */
		public function isFontsPanelEnabled(){
			
			$isEnabled = $this->objProcessor->isFontsPanelEnabled();
			
			return($isEnabled);
		}
		
		
		/**
		 * get fonts parameters for custom output
		 */
		public function getArrFontsParams(){
			
			$isFontsPanelEnabled = $this->objProcessor->isFontsPanelEnabled();
			if($isFontsPanelEnabled == false)
				return(array());
			
			$arrParamNames = $this->objProcessor->getAllParamsNamesForFonts();
			$arrFontsData = $this->getArrFonts();
			
			$settingsOutput = new UniteCreatorSettingsOutput();
			
			$arrParams = $settingsOutput->getFontsParams($arrParamNames, $arrFontsData, $this->type, $this->name);
			
			return($arrParams);
		}
		
		/**
		 * get fonts params
		 */
		public function getArrFontsParamNames(){
			
			$arrParamNames = $this->objProcessor->getAllParamsNamesForFonts();
			
			return($arrParamNames);
		}
		
		
		/**
		 * put config html
		 */
		public function putHtmlConfig($isOutputSidebar = false, $params = array()){
			
			$this->getHtmlConfig(true, $isOutputSidebar, $params);
		}
		
		
		/**
		 * get object settings items for config (repeater)
		 */
		public function getSettingsItemsObject(){
			
			$this->validateInited();
			
			//if output item settings, has to be settings
			if(empty($this->paramsItems)){
		
				UniteFunctionsUC::throwError("Item params not found");
			}
			
			$this->paramsItems = $this->objProcessor->processParamsForOutput($this->paramsItems);
			
			$objSettings = new UniteCreatorSettings();
			
			$source = UniteFunctionsUC::getVal($this->arrHtmlConfigOptions, "source");
			if($source == "addon"){
				$objSettings->setCurrentAddon($this);
				$objSettings->addGlobalParam("source", "addon", UniteSettingsUC::TYPE_IMAGE);
			}
			
			
			$objSettings->initByCreatorParams($this->paramsItems);
			
			return($objSettings);
		}
		
		
		/**
		 * get item config
		 */
		public function getHtmlItemConfig($putMode = false){
			
			$objSettings = $this->getSettingsItemsObject();
			
			$objOutput = new UniteSettingsOutputWideUC();
			
			$objOutput->init($objSettings);
			$objOutput->setShowSaps(false);
			
			if($putMode == true){
				$objOutput->draw("uc_form_addon_item_settings", false);
			}else{
				ob_start();
				$objOutput->draw("uc_form_addon_item_settings", false);
				$html = ob_get_contents();
				ob_clean();
				return($html);
			}
		
		}
		
		
		/**
		 * put item config html
		 */
		public function putHtmlItemConfig(){
		
			$this->getHtmlItemConfig(true);
		
		}
		
		
		private function a______ADDON_CONTENT______(){}
		
		/**
		 * convert from url assets
		 */
		public function convertFromUrlAssets($value){
			
			$urlAssets = $this->getUrlAssets();
		    
			if(!empty($urlAssets))
				$value = HelperUC::convertFromUrlAssets($value, $urlAssets);
		   
			return($value);
		}
		
		
		/**
		 * convert value to url assets
		 */
		private function convertToUrlAssets($val){
			
			if(empty($val))
				return($val);
						
			if(empty($this->urlAssets))
				return($val);
			
			if(is_string($val) == false)
				return($val);
			
			$urlAssetsKey = "[url_assets]/";
			
			$urlAssetsFull = HelperUC::URLtoFull($this->urlAssets);
			$urlAssetsRelative = HelperUC::URLtoRelative($this->urlAssets);
			
			//a little hack
			$val = str_replace("com_addonlibrary", "com_blox", $val);
			
			if(strpos($val, $urlAssetsFull) !== false){
				$val = str_replace($urlAssetsFull, $urlAssetsKey, $val);
				return($val);
			}
			
			if(strpos($val, $urlAssetsRelative) !== false){
				$valNew = str_replace($urlAssetsRelative, $urlAssetsKey, $val);
				$valNew = trim($valNew);
				if(strpos($valNew, $urlAssetsKey) === 0)
					return($valNew);
			}
			
			
			return($val);
		}
		
		
		/**
		 * encode url assets to data
		 */
		public function modifyDataConvertToUrlAssets($arrData){
			
			$this->validateInited();
			
			if(empty($arrData))
				return($arrData);
			
			if(is_string($arrData)){
				$arrData = HelperUC::URLtoRelative($arrData);
				$arrData = $this->convertToUrlAssets($arrData);
			}
			
			if(!is_array($arrData))
				return($arrData);
			
			foreach($arrData as $key=>$val){
				
				$val = HelperUC::URLtoRelative($val);
				
				if(!empty($this->urlAssets) && is_string($val))
					$val = $this->convertToUrlAssets($val);
				
				$arrData[$key] = $val;
			}
			
			return($arrData);
		}
		
		
		/**
		 * get main params processed
		 */
		public function getProcessedMainParamsValues($processType){
			
			$this->validateInited();
			
			$arrParams = $this->objProcessor->getProcessedMainParamsValues($processType);
			
			return($arrParams);
		}
		
		
		/**
		 * get processed main params images
		 */
		public function getProcessedMainParamsImages(){
			
			$this->validateInited();
			
			$objParams = $this->getParams();
			$arrParamsImages = $this->objProcessor->getProcessedParamsValues($objParams, UniteCreatorParamsProcessor::PROCESS_TYPE_SAVE, "uc_image");
			
			
			return($arrParamsImages);
		}
		
		
		/**
		 * get processed main params
		 */
		public function getProcessedMainParams(){
			
			$this->validateInited();
			$arrParams = $this->objProcessor->processParamsForOutput($this->params);
			
			return($arrParams);
		}
		
		
		/**
		 * get processed items params
		 */
		public function getProcessedItemsParams(){
			
			$this->validateInited();
			$arrParams = $this->objProcessor->processParamsForOutput($this->paramsItems);
			
			return($arrParams);
		}
		
		
		/**
		 * get items array, process for config
		 */
		public function getArrItemsForConfig(){
			
			$arrItems = $this->getProcessedItemsData(UniteCreatorParamsProcessor::PROCESS_TYPE_CONFIG, false);
			
			return($arrItems);
		}
		
		/**
		 * get not processed items
		 */
		public function getArrItemsNonProcessed(){
			
			return($this->arrItems);
		}
		
		/**
		 * get item data
		 */
		public function getProcessedItemsData($processType, $forTemplate = true, $filterType = null){
			
			$arrItems = $this->objProcessor->getProcessedItemsData($this->arrItems, $processType, $forTemplate, $filterType);
			
			return($arrItems);
		}
		
		
		/**
		 * get unprocessed data for layout grid
		 */
		public function getDataForLayoutGrid(){
			
			$this->validateInited();
			
			$arrAddon = array();
			$arrAddon["name"] = $this->name;
			$arrAddon["config"] = $this->getProcessedMainParamsValues(UniteCreatorParamsProcessor::PROCESS_TYPE_CONFIG);
			
			if($this->hasItems)
				$arrAddon["items"] = $this->getParamsItems();
			
			if(!empty($this->arrFonts))
				$arrAddon["fonts"] = $this->arrFonts;
			
			return($arrAddon);
		}
		
		private function a__________SET_PARAM_VALUES________(){}
		
		/**
		 * set responsive param values from another fields if available
		 */
		private function setResponsiveParamValues($param, $name, $arrValues){
						
			$isResponsive = UniteFunctionsUC::getVal($param, "is_responsive");
			$isResponsive = UniteFunctionsUC::strToBool($isResponsive);
						
			if($isResponsive == false)
				return($param);
			
			if(isset($arrValues[$name."_tablet"])){
				
				$defaultValueTablet = UniteFunctionsUC::getVal($param, "default_value_tablet");
				$param["value_tablet"] = UniteFunctionsUC::getVal($arrValues, $name."_tablet", $defaultValueTablet);
			}
			
			if(isset($arrValues[$name."_mobile"])){
				
				$defaultValueMobile = UniteFunctionsUC::getVal($param, "default_value_mobile");
				$param["value_mobile"] = UniteFunctionsUC::getVal($arrValues, $name."_mobile", $defaultValueMobile);
			}
			
						
			return($param);
		}	
		
		
		/**
		 * set params values work
		 * type: main,items
		 */
		private function setParamsValuesWork($arrValues, $arrParams, $type){

			$this->validateInited();
			
			if(empty($arrValues))
				$arrValues = array();
						
			if(!is_array($arrValues))
				UniteFunctionsUC::throwError("The values shoud be array");
						
			foreach($arrParams as $key => $param){
			    
				$name = UniteFunctionsUC::getVal($param, "name");
				
				if(empty($name))
					continue;
				
				$defaultValue = UniteFunctionsUC::getVal($param, "default_value");
				
				$type = UniteFunctionsUC::getVal($param, "type");
				
				$value = UniteFunctionsUC::getVal($arrValues, $name, $defaultValue);
								
				$value = $this->objProcessor->getSpecialParamValue($type, $name, $value, $arrValues);
				
				$param["value"] = $value;
								
				$param = $this->setResponsiveParamValues($param, $name, $arrValues);
				
				$param = $this->objProcessor->setExtraParamsValues($type, $param, $name, $arrValues);
				
				//set responsive values								
				$arrParams[$key] = $param;
				
			}
			
			return($arrParams);
		}
		
		
		private function a__________SETTERS________(){}
		
		/**
		 * add some param to the params list
		 */
		public function addParam($param){
			
			$this->params[] = $param;
						
		}
		
		/**
		 * set type
		 */
		public function setType($type){
			
			$this->type = $type;
		}
		
		/**
		 * set that the addon is inside grid
		 */
		public function setIsInsideGrid(){
			$this->isInsideGrid = true;
		}
		
		/**
		 * set params values
		 */
		public function setParamsValues($arrValues){
			
			if(empty($arrValues))
				$arrValues = array();
			
			$this->params = $this->setParamsValuesWork($arrValues, $this->params, "main");
						
		}
		
		/**
		 * set item values params
		 */
		public function setParamsValuesItems($arrItemValues, $arrItemParams){
			
			$arrParamsItemsNew = $this->setParamsValuesWork($arrItemValues, $arrItemParams, "items");
			
			return($arrParamsItemsNew);
		}
		
		
		/**
		 * set items array
		 */
		public function setArrItems($arrItems){
			
			$this->validateInited();
			
			if($this->hasItems == false && $this->operationType != self::OPERATION_WIDGET)
				return(false);
			
			if($arrItems === GlobalsUC::VALUE_EMPTY_ARRAY)
			    $arrItems = array();
				
			if(empty($arrItems))
				$arrItems = array();
			
			//validate that the items is not assoc array
			if(UniteFunctionsUC::isAssocArray($arrItems) == true){
				dmp($arrItems);
				$errorMessage = "the items should not be assoc array";
				dmp("Error: ".$errorMessage);
				UniteFunctionsUC::throwError($errorMessage);
			}
			
			$this->arrItems = $arrItems;
		}
		
		
		
		
		/**
		 * set fonts array
		 */
		public function setArrFonts($arrFonts){
			$this->arrFonts = $arrFonts;
		}
		
		
		/**
		 * add css include
		 */
		public function addCssInclude($url){
			$this->includesCSS[] = array("url"=>$url);
		}
		
		/**
		 * add js include
		 */
		public function addJsInclude($url){
			$this->validateInited();
			
			$this->includesJS[] = array("url"=>$url);
		}
		
		
		/**
		 * add library include
		 */
		public function addLibraryInclude($name){
			$this->validateInited();
			
			$this->includesJSLib[] = $name;
		}
		
		/**
		 * add data to css
		 */
		public function addToCSS($css){
			$this->validateInited();
			$this->css .= $css;
			
		}
		
		/**
		 * add some script to js scripts
		 */
		public function addToJs($script){
			$this->validateInited();
			
			$this->js .= $script;
		}
		
		/**
		 * store some data
		 */
		public function storeData($key, $data){
			
			$this->arrStoredData[$key] = $data;
			
		}
		
		private function a____________UPDATERS________________(){}
		
		
		/**
		 * update addon in db
		 */
		private function updateInDB($arrUpdate){
			$this->validateInited();
			
			$this->db->update(GlobalsUC::$table_addons, $arrUpdate, array("id"=>$this->id));
			
			//init the item again from the new record
			$this->data = array_merge($this->data, $arrUpdate);
			
			$this->initByDBRecord($this->data);
		}
		
		
		/**
		 * update config in db, merge with other addon config fields
		 */
		private function updateConfigInDB($arrConfig){
			
			$this->validateInited();
			
			$this->config = array_merge($this->config, $arrConfig);
			
			$arrUpdate = array();
			$arrUpdate["config"] = json_encode($this->config);
			
			$this->updateInDB($arrUpdate);
		}
		
		
		/**
		 * get data for create / update
		 */
		private function getCreateUpdateData($data){
			
			$title = UniteFunctionsUC::getVal($data, "title");
			$html = UniteFunctionsUC::getVal($data, "html");
			$htmlItem = UniteFunctionsUC::getVal($data, "html_item");
			$htmlItem2 = UniteFunctionsUC::getVal($data, "html_item2");
			$css = UniteFunctionsUC::getVal($data, "css");
			$cssItem = UniteFunctionsUC::getVal($data, "css_item");
			$js = UniteFunctionsUC::getVal($data, "js");
			
			$name = UniteFunctionsUC::getVal($data, "name");
			$name = trim($name);
			
			$alias = "";
			
			if($this->isInited == true)
				$type = $this->type;
			else 
				$type = UniteFunctionsUC::getVal($data, "type");
			
			if($type == GlobalsUC::ADDON_TYPE_REGULAR_ADDON)
				$type = "";
				
			if(!empty($type)){
				$alias = $name;
				$name = $alias."_".$type;
			}
			
			
			//get config related fields
			$params = UniteFunctionsUC::getVal($data, "params");
			$paramsItems = UniteFunctionsUC::getVal($data, "params_items");
			$options = UniteFunctionsUC::getVal($data, "options");
			$variablesItem = UniteFunctionsUC::getVal($data, "variables_item");
			$variablesMain = UniteFunctionsUC::getVal($data, "variables_main");
			
			$includes = UniteFunctionsUC::getVal($data, "includes");
			
			$paramsCats = UniteFunctionsUC::getVal($data, "params_cats");
			
			if(empty($includes)){
				$arrJsIncludes = UniteFunctionsUC::getVal($data, "includes_js");
				$arrJsLib = UniteFunctionsUC::getVal($data, "includes_jslib");
				$arrCssIncludes = UniteFunctionsUC::getVal($data, "includes_css");
		
				$arrJsIncludes = $this->normalizeIncludeArray($arrJsIncludes);
				$arrJsLib = $this->normalizeIncludeArray($arrJsLib);
				$arrCssIncludes = $this->normalizeIncludeArray($arrCssIncludes);
				
				$arrJsIncludes = HelperUC::arrUrlsToRelative($arrJsIncludes, true);
				$arrCssIncludes = HelperUC::arrUrlsToRelative($arrCssIncludes, true);
				
				$includes = array("js"=>$arrJsIncludes, "jslib"=>$arrJsLib, "css"=>$arrCssIncludes);
			}
			
			//validation
			$this->validateName($name);
			$this->validateTitle($title);
			$this->validateParams($paramsItems,"item");
			$this->validateParams($params,"main");
		
			//create config variable
			
			$arrConfig = array();
			$arrConfig["options"] = $options;
			$arrConfig["params"] = $params;
			$arrConfig["params_items"] = $paramsItems;
			$arrConfig["includes"] = $includes;
			$arrConfig["variables_item"] = $variablesItem;
			$arrConfig["variables_main"] = $variablesMain;
			$arrConfig["params_cats"] = $paramsCats;
						
			$strConfig = json_encode($arrConfig);
			
			//create template variables
			
			$arrTemplates = array();
			$arrTemplates["html"] = trim($html);
			$arrTemplates["html_item"] = trim($htmlItem);
			$arrTemplates["html_item2"] = trim($htmlItem2);
			$arrTemplates["css"] = trim($css);
			$arrTemplates["css_item"] = trim($cssItem);
			$arrTemplates["js"] = trim($js);
			
			$strTemplates = json_encode($arrTemplates);
			
			$arr = array();
			$arr["title"] = trim($title);
			$arr["name"] = $name;
			$arr["alias"] = $alias;
			$arr["addontype"] = $type;
			$arr["config"] = $strConfig;
			$arr["templates"] = $strTemplates;
			
			//save hash on test_slot1 for further compare
			$hash = md5(json_encode($arr));
			
			$arr["test_slot1"] = $hash;
			
			return($arr);
		}
		
		
		/**
		 * get last order in category for insert or change
		 */
		private function getLastOrderInCat($catID){
			
			$addons = new UniteCreatorAddons();
			$maxOrder = $addons->getMaxOrder($catID);
			
			return($maxOrder+1);
		}
		
		
		/**
		 * insert new addon to db. add ordering first
		 * @param $arrInsert
		 */
		private function insertNewAddonToDB($arrInsert){
			
			$catID = UniteFunctionsUC::getVal($arrInsert, "catid");
			UniteFunctionsUC::validateNotEmpty($catID, "category id");
			
			//set order
			$newOrder = $this->getLastOrderInCat($catID);
			$arrInsert["ordering"] = $newOrder;
			$arrInsert["is_active"] = 1;
			
			$newID = $this->db->insert(GlobalsUC::$table_addons, $arrInsert);
			
			if($newID === 0){		//in case that the table is not auto incriment
				
				$maxID = $this->db->getMaxOrder(GlobalsUC::$table_addons, "id");
				$newID = $maxID + 1;
				
				$arrUpdate = array();
				$arrUpdate["id"] = $newID;
				
				$this->db->update(GlobalsUC::$table_addons, $arrUpdate, "id=0");
			}
			
			$arrInsert["id"] = $newID;
			
			return($arrInsert);
		}
		
		
		/**
		 *
		 * add addon to database from data.
		 * return item id
		 */
		public function add($data){
			
			$arrInsert = $this->getCreateUpdateData($data);
			
			$arrInsert = $this->insertNewAddonToDB($arrInsert);
			
			$this->initByDBRecord($arrInsert);
		
			return($id);
		}

		
		/**
		 * add from small data, only name, alias and catid
		 */
		public function addSmall($title, $name, $description, $catID, $type){
			
			$this->validateTitle($title);
			
			if(!is_numeric($catID))
				$catID = 0;
			
			if(empty($catID))
				$catID = 0;
			
			if($type == GlobalsUC::ADDON_TYPE_REGULAR_ADDON)
				$type = "";
			
			$alias = "";
			if(!empty($type)){
				$alias = $name;
				$name = $name."_".$type;
			}
			
			$this->validateName($name);
			
			$arrInsert = array();
			$arrInsert["title"] = $title;
			$arrInsert["name"] = $name;
			
			if(!empty($type)){
				$arrInsert["alias"] = $alias;
				$arrInsert["addontype"] = $type;
			}
						
			$arrOptions = array();
			$arrOptions["description"] = $description;
			
			$arrConfig = array();
			$arrConfig["options"] = $arrOptions;
			$arrInsert["config"] = json_encode($arrConfig);
			
			$arrTemplates = array();
			$arrTemplates["html"] = "{$title} HTML";
			
			$strTemplates = json_encode($arrTemplates);
			
			$arrInsert["templates"] = $strTemplates;
			$arrInsert["catid"] = $catID;
			
			$arrInsert = $this->insertNewAddonToDB($arrInsert);
			
			$this->initByDBRecord($arrInsert);
			
			return($this->id);
		}
		
		
		/**
		 * update item data - media in db
		 */
		public function update($data){
			
			$this->validateInited();

			$arrUpdate = $this->getCreateUpdateData($data);
			
			$this->updateInDB($arrUpdate);
		}
		
		
		/**
		 * update name and title
		 */
		public function updateNameTitle($name, $title, $description){
			
			$this->validateInited();
			
			$name = trim($name);
			
			$alias = "";
			$type = $this->type;
			
			if(!empty($type)){
				$alias = $name;
				$name = $alias."_".$type;
			}
			
			$this->validateName($name);
			$this->validateTitle($title);
			
			$arrUpdate = array();
			
			$arrUpdate["name"] = $name;
			$arrUpdate["alias"] = $alias;
			$arrUpdate["title"] = $title;
			
			$this->options["description"] = $description;
			$this->config["options"] = $this->options;
			
			$arrUpdate["config"] = json_encode($this->config);
			
			$this->updateInDB($arrUpdate);
			
			$this->name = $name;
			$this->title = $title;
		}

		
		/**
		 * import addon by data
		 */
		public function importAddonData($data){
						
			$name = UniteFunctionsUC::getVal($data, "name");
			$isExists = $this->isAddonExistsByName($name);
			
			//add new
			if($isExists == false){
				
				$arrInsert = $this->insertNewAddonToDB($data);
				$data["id"] = $arrInsert["id"];
				$this->initByDBRecord($data);
				
				$isAddedNewAddon = true;
				
				return($isAddedNewAddon);
				
			}else{		//overwrite existing
				
				$catID = UniteFunctionsUC::getVal($data, "catid");
				$this->initByName($name);
				
				//change ordering if moving to new category
				if($this->catid != $catID){
					$newOrder = $this->getLastOrderInCat($catID);
					$data["ordering"] = $newOrder;
				}
				
				$data["catid"] = $catID;
				$this->updateInDB($data);
				
				$isAddedNewAddon = false;
				
				return($isAddedNewAddon);
				
			}
			
		}
		
		
		/**
		 * get new name
		 */
		private function getDuplicatedSuffix(){
			$suffixName = "_copy";
			$suffixTitle = " - copy";
			
			$type = $this->getType();
			$name = $this->getName();
			
			if(!empty($type)){
				$alias = $this->getAlias();
				$newAlias = $alias.$suffixName;
				$newName = $newAlias."_".$type;
				
			}else{
				$newName = $name.$suffixName;
			}
						
			$isExists = $this->isAddonExistsByName($newName, true);
			
			$num = 1;
			while($isExists == true){
				$num++;
				$suffixName = "_copy".$num;
				$suffixTitle = " - copy".$num;
				
				if(!empty($type)){
					$newAlias = $alias.$suffixName;
					$newName = $newAlias."_".$type;
				}else{
					$newName = $name.$suffixName;
				}
				
				$isExists = $this->isAddonExistsByName($newName, true);
			}
			
			$output = array();
			$output["name"] = $suffixName;
			$output["title"] = $suffixTitle;
			
			return($output);
		}
		
		
		/**
		 *
		 * duplicate gallery
		 */
		public function duplicate(){
			
			$addons = new UniteCreatorAddons();
			
			$this->validateInited();
			
			//get new name and title
			$suffix = $this->getDuplicatedSuffix();
						
			$newTitle = $this->title.$suffix["title"];
			
			if(!empty($this->type)){
				$newAlias = $this->alias.$suffix["name"];
				$newName = $newAlias."_".$this->type;
			}else{
				$newName = $this->name.$suffix["name"];
			}
						
			$this->validateName($newName);
			
			$addons->shiftOrder($this->catid, $this->ordering);
			
			$newOrder = $this->ordering+1;
			
			//insert a new gallery
			$sqlSelect = "select ".self::FIELDS_ADDONS." from ".GlobalsUc::$table_addons." where id={$this->id}";
			$sqlInsert = "insert into ".GlobalsUC::$table_addons." (".self::FIELDS_ADDONS.") ($sqlSelect)";
			
			$this->db->runSql($sqlInsert);
			$lastID = $this->db->getLastInsertID();
			UniteFunctionsUC::validateNotEmpty($lastID);
		
			//update the new addon with the title and the name values
			$arrUpdate = array();
			$arrUpdate["title"] = $newTitle;
			$arrUpdate["name"] = $newName;
			$arrUpdate["ordering"] = $newOrder;
			if(!empty($this->type))
				$arrUpdate["alias"] = $newAlias;
			
			$this->db->update(GlobalsUC::$table_addons, $arrUpdate, array("id"=>$lastID));
			
			return($lastID);
		}
		
		/**
		 * convert addon to type
		 */
		public function convertToType($addonType){
			
			$this->validateInited();
			
			$currentType = $this->type;
						
			if($currentType == $addonType)
				return(false);
				
			$objNewType = UniteCreatorAddonType::getAddonTypeObject($addonType);
			
			$newType = $objNewType->typeNameDistinct;
			
			$addonAlias = $this->getAlias();
			if(empty($addonAlias))
				$addonAlias = $this->getName();
			
			$newName = $addonAlias;
			$newAlias = "";
			
			if(!empty($addonType)){
				
				$newName = $addonAlias."_".$newType;
				$newAlias = $addonAlias;
			}

			$isExists = $this->isAddonExistsByName($newName, true);
			if($isExists == true)
				return(false);
			
			$arrUpdate = array();
			$arrUpdate["addontype"] = $newType;
			$arrUpdate["name"] = $newName;
			$arrUpdate["alias"] = $newAlias;
			
			$this->db->update(GlobalsUC::$table_addons, $arrUpdate, array("id" => $this->id));

			return(true);
		}
		
		
		/**
		 * convert addon to type, currently only to blox type
		 */
		public function convertToBloxType($addonType, $newName = null, $newTitle = null){
			
			$this->validateInited();
			
			$objNewType = UniteCreatorAddonType::getAddonTypeObject($addonType);
			
			$currentType = $this->type;
			
			if(empty($currentType))
				return(false);
			
			$newType = $objNewType->typeNameDistinct;
			
			if($currentType == $newType)
				return(false);
			
			if($newType != GlobalsUC::ADDON_TYPE_REGULAR_ADDON)
				UniteFunctionsUC::throwError("Convert addon type works for regular addons only for now.");
			
			if(empty($newName))
				$newName = $this->alias;
			
			if(empty($newTitle))
				$newTitle = $this->title;
			
			$isExists = $this->isAddonExistsByName($newName, false);
			if($isExists == true)
				return(false);
			
			//get / create new category by title
			$catID = 0;
			
			$catTitle = $this->getCatTitle();
						
			if(!empty($catTitle)){
				$objCategories = new UniteCreatorCategories();
				$catID = $objCategories->getCreateCatByTitle($catTitle);
			}
					
			//update addon
			$arrUpdate = array();
			$arrUpdate["addontype"] = "";
			$arrUpdate["name"] = $newName;
			$arrUpdate["alias"] = "";
			$arrUpdate["title"] = $newTitle;
			$arrUpdate["catid"] = $catID;
			
			$this->updateInDB($arrUpdate);
			
			//copy assets to blox if it's not there
			$this->copyAssetsPathToBlox();
		}
		
		/**
		 * copy assets to blox folder from another folder
		 */
		public function copyAssetsPathToBlox(){
			
			$this->validateInited();
			
			$pathAssets = $this->getPathAssets();
			if(!empty($pathAssets))
				return(false);
			
			$path = $this->getOption("path_assets");
			
			$pathAssetsOld = str_replace("/blox_assets", "/ac_assets", GlobalsUC::$pathAssets);
			
			$pathOldAbsolute = UniteFunctionsUC::joinPaths($pathAssetsOld, $path);
			$pathOldAbsolute = UniteFunctionsUC::addPathEndingSlash($pathOldAbsolute);
			
			$isExists = is_dir($pathOldAbsolute);
			
			$pathNewAbsolute = UniteFunctionsUC::joinPaths(GlobalsUC::$pathAssets, $path);
			$pathNewAbsolute = UniteFunctionsUC::addPathEndingSlash($pathNewAbsolute);
			
						
			UniteFunctionsUC::copyDir($pathOldAbsolute, $pathNewAbsolute);
			
		}
		
		
		/**
		 * update params in db
		 */
		private function updateParamsInDB($arrParams, $isMain){
			
			$arrConfig = array();
			if($isMain)
				$arrConfig["params"] = $arrParams;
			else 
				$arrConfig["params_items"] = $arrParams;
			
			$this->updateConfigInDB($arrConfig);
		}
		
		
		/**
		 * add or update param , update db after
		 */
		public function addUpdateParam_updateDB($param, $isMain, $position){
			
			$name = UniteFunctionsUC::getVal($param, "name");
			
			$isParamExists = $this->isParamExists($name, $isMain);
			
			if($isMain)
				$arrParams = $this->params;
			else 
				$arrParams = $this->paramsItems;
			
			//add
			if($isParamExists == false){
				$numItems = count($arrParams);
				if($position >= $numItems)
					$arrParams[] = $param;
				else
					array_splice($arrParams, $position, 0, array($param));
			}else{	//update
				
				$pos = $this->getParamPosition($name, $isMain);
				$arrParams[$pos] = $param;
			}
			
			
			$this->updateParamsInDB($arrParams, $isMain);
		}
		
		
		/**
		 * delete param, update db
		 */
		public function deleteParam_updateDB($paramName, $isMain){
			
			$isExists = $this->isParamExists($paramName, $isMain);
			if($isExists == false)
				return(false);
				
			$pos = $this->getParamPosition($paramName, $isMain);
			
			if($isMain)
				$arrParams = $this->params;
			else 
				$arrParams = $this->paramsItems;
				
			array_splice($arrParams, $pos, 1);

			
			$this->updateParamsInDB($arrParams, $isMain);
		}
		
		
		private function a__________TEST_SLOT____________(){}
		

		/**
		 * get test data
		 * @param $num
		 */
		public function getTestData($num){
			$arrData = array();
		
			$this->validateTestSlot($num);
			
			$fieldName = "test_slot".$num;
			$jsonData = UniteFunctionsUC::getVal($this->data, $fieldName);
			
			if(empty($jsonData))
				return(null);
			
			if(!empty($jsonData)){
				$arrData = @json_decode($jsonData);
				if(empty($arrData))
					$arrData = array();
			}
			
			$arrData = UniteFunctionsUC::convertStdClassToArray($arrData);
			
			return($arrData);
		}
		
		
		/**
		 * get addon default data (slot 2)
		 */
		public function getDefaultData(){
			
			$arrData = $this->getTestData(2);
						
			return($arrData);
		}
		
		
		/**
		 * get all test data in array
		 */
		public function getAllTestData($isJson = false){
			$arrData = array();
			
			$testData1 = $this->getTestData(1);
			$testData2 = $this->getTestData(2);
			$testData3 = $this->getTestData(3);
			
			if(empty($testData1) && empty($testData2) && empty($testData3))
				return(null);
			
			$arrData["test_slot1"] = $testData1;
			$arrData["test_slot2"] = $testData2;
			$arrData["test_slot3"] = $testData3;
			
			if($isJson == true)
				return(json_encode($arrData));
			
			return($arrData);
		}
		
		
		/**
		 * get if some test data exists of some slot
		 * @param $num
		 */
		public function isTestDataExists($num){
			$arrData = $this->getTestData($num);
			if(!empty($arrData))
				return(true);
			else
				return(false);
		}
		
		
		/**
		 * return if default data exists
		 */
		public function isDefaultDataExists(){
			
			$isExists = $this->isTestDataExists(2);
			
			return($isExists);
		}
		
		/**
		* modify addon data - convert to url assets
		 */
		public function modifyAddonDataConvertToUrlAssets($addonData){
			
			$this->validateInited();
			
			$arrConfig = UniteFunctionsUC::getVal($addonData, "config");
			$arrItems = UniteFunctionsUC::getVal($addonData, "items");
			
			
			//modify url assets
			if(!empty($arrConfig))
				$addonData["config"] = $this->modifyDataConvertToUrlAssets($arrConfig);
			
			if(!empty($arrItems)){
				
				foreach($arrItems as $key=>$itemData){
					
					if(empty($itemData))
						continue;
					
					$addonData["items"][$key] = $this->modifyDataConvertToUrlAssets($itemData);
				}
			}
			
			return($addonData);
		}
		
		
		/**
		* modify addon data - convert to url assets
		 */
		public function modifyArrItemsConvertUrlAssets($arrItems){
			
			if(empty($arrItems))
				return($arrItems);
			
			foreach($arrItems as $key=>$item){
				
				if(is_array($item) == false)
					continue;
				
				if(empty($item))
					continue;
				
				$arrItems[$key] = $this->modifyDataConvertToUrlAssets($item);
				
			}
			
			return($arrItems);
		}
		
		
		/**
		 * save test slot, slot num can be 1,2,3
		 * slot 2 is the default data slot
		 */
		public function saveTestSlotData($slotNum, $arrConfig, $arrItems, $arrFonts = null){
			
			$this->validateInited();
			$this->validateTestSlot($slotNum);
			
			if(empty($arrItems))
				$arrItems = "";
						
			
			$data = array();
			$data["config"] = $arrConfig;
			$data["items"] = $arrItems;
			if(!empty($arrFonts))
			$data["fonts"] = $arrFonts;
			
			$data = $this->modifyAddonDataConvertToUrlAssets($data);
						
			$dataJson = json_encode($data);
			
			$slotName = "test_slot".$slotNum;
			
			$arrUpdate = array();
			$arrUpdate[$slotName] = $dataJson;
			
			$this->updateInDB($arrUpdate);
		}
		
		
		/**
		 * clear the test data slot
		 */
		public function clearTestDataSlot($slotNum){
			$this->validateInited();
			$this->validateTestSlot($slotNum);
			
			$slotName = "test_slot".$slotNum;
			
			$arrUpdate = array();
			$arrUpdate[$slotName] = "";
			$this->updateInDB($arrUpdate);
		}

		
		/**
		 * set param values and items from some slot
		 */
		public function setValuesFromTestData($slotNum){
			
			$arrData = $this->getTestData($slotNum);
						
			if(empty($arrData))
				return(false);
			
			$config = UniteFunctionsUC::getVal($arrData, "config");
			
			$items = UniteFunctionsUC::getVal($arrData, "items");
			$fonts = UniteFunctionsUC::getVal($arrData, "fonts");
			
			//if(!empty($config))
				//$this->setParamsValues($config);
			
			if(!empty($items))
				$this->setArrItems($items);
			
			if(!empty($fonts))
				$this->setArrFonts($fonts);
			
		}
		
		
		
		/**
		 * set addon values from default data
		 */
		public function setValuesFromDefaultData(){
			
			$this->setValuesFromTestData(2);
		}
		
		
	}

?>