<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

class UniteCreatorAddons extends UniteElementsBaseUC{
		
	protected function a_STATIC_METHODS(){}
	
	
    /**
     * get addons thumbnails
     */
    public function getArrAddonPreviewUrls($arrAddons, $keyType){
    	    	
    	$arrPreviews = array();
    	
    	foreach($arrAddons as $addon){
    		
    		switch($keyType){
    			case "title":
    				$key = $addon->getTitle();
    			break;
    			default:
    				$key = $addon->getName();
    			break;
    		}
    		
    		$urlPreview = $addon->getUrlPreview();
    		
    		if(empty($urlPreview))
    			continue;
    			
    		$urlPreview = HelperUC::URLtoAssetsRelative($urlPreview);
    		
    		$arrPreviews[$key] = $urlPreview;
    	}
    	
    	return($arrPreviews);
    }
	
	
	
	/**
	 * get active filter where string
	 */
	public static function getFilterActiveWhere($filterActive = null, $prefix = null, $addonType=""){
		
		if($filterActive === null)
			$filterActive = UniteCreatorManagerAddons::getStateFilterActive($addonType);
		
		$where = "";
		
		//set active fitler where
		switch($filterActive){
			case "active":
				$where = "is_active=1";
				break;
			case "not_active":
				$where = "is_active=0";
				break;
		}
		
		if(!empty($where) && !empty($prefix))
			$where = $prefix.".".$where;
		
		return($where);
	}
	
	
	protected function a______GETTERS_________(){}
	
	
	/**
	 *
	 * get items by id's
	 */
	private function getAddonsByIDs($addonIDs){
		$strAddons = implode(",", $addonIDs);
		$tableAddons = GlobalsUC::$table_addons;
		$sql = "select * from {$tableAddons} where id in({$strAddons})";
		$arrAddons = $this->db->fetchSql($sql);
	
		return($arrAddons);
	}
	
	/**
	 * get html of categories and items.
	 */
	protected function getCatsAndAddonsHtml($catID, $type, $data = null, $parentID = null){
		
		$objManager = UniteCreatorManager::getObjManagerByAddonType($type, $data);
		
		$options = array();
		if(!empty($parentID))
			$options["parent_id"] = $parentID;
		
		$response = $objManager->getCatsAndAddonsHtml($catID, "", false, $options);
		
		return($response);
	}
	
	
	/**
	 *
	 * get layouts array
	 */
	public function getArrAddonsShort($order = "", $params = array(), $addonType = null){
		
		if(empty($params))
			$params = array();
		
		if(!empty($addonType))
			$params["addontype"] = $addonType;
		
		$arrWhere = array();
		
		$filterNames = UniteFunctionsUC::getVal($params, "filter_names");
		if(!empty($filterNames)){
			$strNames = "'".implode("','", $filterNames)."'";
			$arrWhere[] = "name in ($strNames)";
		}
		
		$addonType = UniteFunctionsUC::getVal($params, "addontype");
		
		$filterActive = UniteFunctionsUC::getVal($params, "filter_active");
		if(!empty($filterActive))
			$arrWhere[] = self::getFilterActiveWhere($filterActive, null, $addonType);
		
		$arrWhere[] = $this->db->getSqlAddonType($addonType);
		
		$where = "";
		if(!empty($arrWhere))
			$where = implode(" and ", $arrWhere);
		
		
		$response = $this->db->fetch(GlobalsUc::$table_addons, $where, $order);
		
		return($response);
	}
	
	/**
	 * get addons list with name / title
	 */
	public function getArrAddonsNameTitle($order = "", $params = array(), $addonType = null, $isAlias = false){
		
		$arrAddons = $this->getArrAddonsShort($order, $params, $addonType);
		
		$field = "name";
		if($isAlias == true)
			$field = "alias";
		
		$arrAssoc = UniteFunctionsUC::arrayToAssoc($arrAddons, $field, "title");
		
		return($arrAssoc);
	}
	
	
	/**
	 *
	 * get addons array
	 */
	public function getArrAddons($order = "", $params = array(), $addonType = null){
		
		if(empty($params))
			$params = array();
		
		$response = $this->getArrAddonsShort($order, $params, $addonType);
		
		$arrAddons = array();
		foreach($response as $record){
			$objAddon = new UniteCreatorAddon();
			$objAddon->initByDBRecord($record);
			$arrAddons[] = $objAddon;
		}
		
		return($arrAddons);
	}
	
	
	/**
	 *
	 * get category items
	 */
	public function getCatAddons($catID, $isShort = false, $filterActive = null, $addonType = null, $includeImages = false, $extra = array()){
		
		$arrWhere = array();
		
		if(is_numeric($catID))
			$catID = (int)$catID;
		
		if($catID === null)
			$catID = "all";
		
		//get catID where
		if($catID === "all"){
			$arrWhere = array();
		}
		else if(is_numeric($catID)){
			$catID = (int)$catID;
			$arrWhere[] = "catid=$catID";
		}
		else{			//multiple - array of id's
						
			if(is_array($catID) == false)
				UniteFunctionsUC::throwError("catIDs could be array or number");
			
			$strCats = implode(",", $catID);
			$strCats = $this->db->escape($strCats);		//for any case
			$arrWhere[] = "catid in($strCats)";
		}
		
		$whereFilterActive = self::getFilterActiveWhere($filterActive, null, $addonType);
		if(!empty($whereFilterActive))
			$arrWhere[] = $whereFilterActive;
		
		//set addon type - if specific category - no need
		if(is_numeric($catID) == false || empty($catID) || $catID === "all")
			$arrWhere[] = $this->db->getSqlAddonType($addonType);
		
		$filterSearch = UniteFunctionsUC::getVal($extra, "filter_search");
		$filterSearch = trim($filterSearch);
		
		if(!empty($filterSearch)){
			
			$filterSearch = $this->db->escape($filterSearch);
			$filterSearch = strtolower($filterSearch);
			
			$arrWhere[] = "title like '%$filterSearch%'";
		}
		
		$where = "";
		if(!empty($arrWhere))
			$where = implode(" and ",$arrWhere);
		
		$records = $this->db->fetch(GlobalsUC::$table_addons, $where, "catid, ordering");
				
		$arrAddons = array();
		foreach($records as $record){
						
			$objAddon = new UniteCreatorAddon();
			$objAddon->initByDBRecord($record);
			
			if($isShort == true){
				$arrAddons[] = $objAddon->getArrShort($includeImages);
				
			}else{
				$arrAddons[] = $objAddon;
			}
		}
		
		
		return($arrAddons);
	}
	
	/**
	 * remove non found categories
	 * with 0 addons or if title not match
	 */
	private function getAddonsWidthCategories_removeEmptyCats($arrCatsAssoc, $searchString){
		
		if(empty($searchString))
			return($arrCatsAssoc);
		
		foreach($arrCatsAssoc as $catTitle => $arrCat){
			
			$isTitleMatch = UniteFunctionsUC::isStringContains($catTitle, $searchString);
			$arrAddons = UniteFunctionsUC::getVal($arrCat, "addons");
			
			if(empty($arrAddons))
				$arrAddons = array();
			
			$numAddons = count($arrAddons);
			
			if($numAddons == 0 && $isTitleMatch == false)
				unset($arrCatsAssoc[$catTitle]);
			
		}

		return($arrCatsAssoc);
	}
	
	
	/**
	 * get addons by categories
	 * $publishedCatOnly - get only from published ones
	 */
	public function getAddonsWidthCategories($publishedCatOnly = true, $isShort = false, $type = "", $extra=null){
		
		$getCatObjects = UniteFunctionsUC::getVal($extra, "get_cat_objects");
		$getCatObjects = UniteFunctionsUC::strToBool($getCatObjects);
		
		$objCats = new UniteCreatorCategories();
		
		if($getCatObjects == true)
			$arrCats = $objCats->getCatRecordsWithAddType("uncategorized", $type);
		else
			$arrCats = $objCats->getCatsShort("uncategorized", $type);
					
		$arrIDs = array_keys($arrCats);
				
		$arrCatsAssoc = array();
		
		//prepare structure
		foreach($arrCats as $catID=>$record){
						
			//if it's record
			if(is_array($record))
				$title = $record["title"];
			else 
				$title = $record;
			
			$cat = array();
			$cat["id"] = $catID;
			$cat["title"] = $title;
			$cat["type"] = $type;
			
			//add cat object
			if($getCatObjects == true && !empty($catID)){
				$objCat = new UniteCreatorCategory();
				$objCat->initByRecord($record);
				$cat["objcat"] = $objCat;
			}
			
			$cat["addons"] = array();
						
			$arrCatsAssoc[$title] = $cat;
		}
				
		$filterActive = null;
		if($publishedCatOnly == true)
			$filterActive = "active";
		
		
		$arrAdons = array();
		if(!empty($arrCatsAssoc))
			$arrAdons = $this->getCatAddons(null, false, $filterActive, $type, false, $extra);
		
		//put addons to category
		foreach($arrAdons as $addon){
			
			$addonCatTitle = $addon->getCatTitle();
			$addonCatID = $addon->getCatID();
			
			$addonCatID = (int)$addonCatID;
			
			$name = $addon->getName();
			
			if($isShort == true){
				$addonForInsert = $addon->getArrShort(true);
				$addonForInsert["name"] = $addon->getNameByType();
			}
			else
				$addonForInsert = $addon;
			
			$insertKey = $addonCatTitle;
			if($addonCatID === 0)
				$insertKey = HelperUC::getText("uncategorized");
						
			//skip addons without category
			if(empty($insertKey))
				continue;
						
			$arrCatsAssoc[$insertKey]["addons"][$name] = $addonForInsert;
			
		}
		
			
		//in case of search, filter empty categories
		$filterSearch = UniteFunctionsUC::getVal($extra, "filter_search");
		if(!empty($filterSearch))
			$arrCatsAssoc = $this->getAddonsWidthCategories_removeEmptyCats($arrCatsAssoc, $filterSearch);
		
		
		return($arrCatsAssoc);
	}
	
	
	/**
	 * get addons with categories by comfortable format
	 */
	public function getAddonsWidthCategoriesShort($publishedCatOnly = true, $type=""){
		
		$arrCats = $this->getAddonsWidthCategories($publishedCatOnly, true, $type);
		
		return $arrCats;
	}
	
	
	/**
	 * check if addon exists by name
	 */
	public function isAddonExistsByName($name, $addonType = null){
		
		$name = $this->db->escape($name);
		
		if(empty($addonType))
			$where = "name='{$name}'";
		else{
			$where = "alias='{$name}'";
			$where .= " and ".$this->db->getSqlAddonType($addonType);
		}
		
		$response = $this->db->fetch(GlobalsUC::$table_addons, $where);
		
		return(!empty($response));
	}
		
	
	/**
	 * get addon type from data
	 */
	public function getAddonTypeFromData($data){
		
		$type = UniteFunctionsUC::getVal($data, "addontype");
			
		if(empty($type))
			$type = UniteFunctionsUC::getVal($data, "type");
		
		HelperUC::runProviderFunc("validateDataAddonsType", $type, $data);
		
		return($type);
	}
	
	
	
	/**
	 *
	 * get max order from categories list
	 */
	public function getMaxOrder($catID){
	
		UniteFunctionsUC::validateNotEmpty($catID,"category id");
	
		$tableAddons = GlobalsUC::$table_addons;
		$query = "select MAX(ordering) as maxorder from {$tableAddons} where catid={$catID}";
	
		$rows = $this->db->fetchSql($query);
	
		$maxOrder = 0;
		if(count($rows)>0) 
			$maxOrder = $rows[0]["maxorder"];
		
		if(!is_numeric($maxOrder))
			$maxOrder = 0;
	
		return($maxOrder);
	}
	
	
	/**
	 * get number of addons by category
	 */
	public function getNumAddons($catID=null, $filterActive = null, UniteCreatorAddonType $objTypeName = null){
		
		$tableAddons = GlobalsUC::$table_addons;
		$addonType = $objTypeName->typeName;
		
		$arrWhere = array();
		if(!empty($filterActive)){
			$whereActive = self::getFilterActiveWhere($filterActive, "a", $addonType);
			if(!empty($whereActive))
				$arrWhere[] = $whereActive;
		}
		
		if($objTypeName->isBasicType == true)
			$addonType = null;
		
		
		$arrWhere[] = $this->db->getSqlAddonType($addonType);
		
		//all addons
		if($catID === null){
			$query = "select count(*) as num_addons from {$tableAddons}";
		}
		else{
			$query = "select count(*) as num_addons from {$tableAddons} as a";
			$arrWhere[] = "a.catid=$catID";
		}
		
		
		if(!empty($arrWhere))
			$query .= " where ".implode(" and ",$arrWhere);

		
		$response = $this->db->fetchSql($query);
		
		if(empty($response))
			UniteFunctionsUC::throwError("Can't get number of zero addons");
	
		$numAddons = $response[0]["num_addons"];
		
		return($numAddons);
	}
	
	
	/**
	 * get addon output, for the editor
	 */
	public function getAddonOutput($objAddon, $isWrap = true){
		
		$processType = UniteCreatorParamsProcessor::PROCESS_TYPE_OUTPUT_BACK;
		
		$objOutput = new UniteCreatorOutput();
		$objOutput->setProcessType($processType);
		
		$objOutput->initByAddon($objAddon);
		
		if($isWrap == true)
			$params = array("wrap_js_timeout"=> true);
		else
			$params = array();
			
		$htmlAddon = $objOutput->getHtmlBody(true,false,true,$params);
		
		$arrIncludes = $objOutput->getProcessedIncludes(true);
		
		$arr = array();
		$arr["html"] = $htmlAddon;
		$arr["includes"] = $arrIncludes;
		//$arr["constants"] = $arrConstantData;
		
		return($arr);
	}
	
	
	/**
	 * get addon output data
	 */
	public function getAddonOutputData($addonData){
		
		//set addon type
		$objAddon = $this->prepareAddonByData($addonData);
		
		$arrAddonContents = $this->getAddonOutput($objAddon);
		
		return($arrAddonContents);
	}
	
	
	/**
	 * get addon config html by data
	 */
	public function getAddonConfigHTML($data){
		
		$objAddon = $this->initAddonByData($data);
		
		//init addon config
		$addonConfig = new UniteCreatorAddonConfig();
		$addonConfig->setStartAddon($objAddon);
		
		$html = $addonConfig->getHtmlFrame();
		
		$response = array();
		$response["html_config"] = $html;
		
		//get output data on live mode
		$getOutputData = UniteFunctionsUC::getVal($data, "getcontent");
		$getOutputData = UniteFunctionsUC::strToBool($getOutputData);
		if($getOutputData == true){
			
			$outputData = $this->getLayoutAddonOutputData($data);
			$response["output"] = $outputData;
		}
		
		
		return($response);
	}
	
	
	/**
	 * get item settings html
	 */
	public function getAddonSettingsHTMLFromData($data){
				
		$objAddon = $this->initAddonByData($data);
		$html = $objAddon->getHtmlConfig(false, true);
		
		return($html);
	}
	
	/**
	 * get addon editor data
	 * including addon config, and output if needed
	 */
	public function getAddonEditorData($data){
		
		$objAddon = $this->initAddonByData($data);
		
		$addonType = $objAddon->getType();
		
		$arrData = array();
		$arrData["addontype"] = $addonType;
		$arrData["name"] = $objAddon->getName();
		
		$arrExtra = array();
		$arrExtra["title"] = $objAddon->getTitle();
		$arrExtra["url_icon"] = $objAddon->getUrlIcon();
		$arrExtra["admin_labels"] = $objAddon->getAdminLabels();
		$arrExtra["has_items"] = $objAddon->isHasItems();
		$arrExtra["num_items"] = $objAddon->getNumItems();
		$arrExtra["id"] = $objAddon->getID();
				
		$objAddon->setIsInsideGrid();
		$arrExtra["html_settings"] = $objAddon->getHtmlConfig(false, true);
		
		$arrData["extra"] = $arrExtra;
		
		$returnOutput = UniteFunctionsUC::getVal($data, "return_output");
		$returnOutput = UniteFunctionsUC::strToBool($returnOutput);
		if($returnOutput == true){
			
			$objLayoutOutput = new UniteCreatorLayoutOutput();
			$objLayoutOutput->setAddonType($addonType);
			$arrData["output"] = $objLayoutOutput->getAddonOutput($objAddon);
		}
		
		return($arrData);
	}
	
	
	/**
	 * get item settings html
	 */
	public function getAddonItemsSettingsHTMLFromData($data){
		
		$addonID = UniteFunctionsUC::getVal($data, "addonid");
		
		$addon = new UniteCreatorAddon();
		$addon->initByID($addonID);
		
		$html = $addon->getHtmlItemConfig();
		
		return($html);
	}
	
	
	/**
	 * check if needed helper editor on admin addon output
	 */
	public function isHelperEditorNeeded(UniteCreatorAddon $addon){
		
		$hasItems = $addon->isHasItems();
		if($hasItems == false)
			return(false);
		
		$isItemsEditorExists = $addon->isEditorItemsAttributeExists();
		if($isItemsEditorExists == false)
			return(false);
		
		$isMainEditorExists = $addon->isEditorMainAttributeExists();
		if($isMainEditorExists == true)
			return(false);
		
		return(true);
	}
	
	
	/**
	 * prepare addon by data
	 */
	public function prepareAddonByData($addonData){
		
		$addonName = UniteFunctionsUC::getVal($addonData, "name");
		$addonType = UniteFunctionsUC::getVal($addonData, "addontype");
		
		$addonID = UniteFunctionsUC::getVal($addonData, "id");
		
		//init addon
		$objAddon = new UniteCreatorAddon();
				
		if(empty($addonName) && !empty($addonID) && is_numeric($addonID)){
		
			//  init by id
			
			$objAddon->initByID($addonID);
		}else{
			
			//  init by name or alias and type
			
			if(empty($addonType))
				$objAddon->initByName($addonName);
			else
				$objAddon->initByAlias($addonName, $addonType);
			
		}
		
		$elementorSettings = UniteFunctionsUC::getVal($addonData, "elementor_settings");
		
		//init by elementor settings
		if(!empty($elementorSettings)){
			
			$objIntegrate = new UniteCreatorElementorIntegrate();
			$objIntegrate->includePluginFiles();
			
			$objWidget = new UniteCreatorElementorBackgroundWidget();
	    	
			$objAddon = $objWidget->setAddonSettingsFromElementorSettings($objAddon, $elementorSettings);
									
		}else{		//init by blox settings
			
			$arrSettings = UniteFunctionsUC::getVal($addonData, "settings");
			
			if(!empty($arrSettings)){
				
				$objAddon->setParamsValues($arrSettings);
				
			}else{
			
				//set addon data
				$arrConfig = UniteFunctionsUC::getVal($addonData, "config");
				if(!empty($arrConfig))
					$objAddon->setParamsValues($arrConfig);
				
				$arrItems = UniteFunctionsUC::getVal($addonData, "items");
				if(!empty($arrItems))
					$objAddon->setArrItems($arrItems);
				
				$arrFonts = UniteFunctionsUC::getVal($addonData, "fonts");
				if(!empty($arrFonts))
					$objAddon->setArrFonts($arrFonts);
			
			}
			
				
		}
		
		return($objAddon);
	}
	
	
	protected function a____________SETTERS__________(){}
	
	
	/**
	 *
	 * delete addons
	 */
	private function deleteAddons($arrAddons){
		
		//sanitize
		foreach($arrAddons as $key=>$itemID)
			$arrAddons[$key] = (int)$itemID;
		
		UniteProviderFunctionsUC::doAction("uc_before_delete_widgets", $arrAddons);			
		
		$strAddonIDs = implode(",",$arrAddons);
		$this->db->delete(GlobalsUC::$table_addons,"id in($strAddonIDs)");
	}
	
	
	/**
	 *
	 * save items order
	 */
	private function saveAddonsOrder($arrAddonIDs){
	
		//get items assoc
		$arrAddons = $this->getAddonsByIDs($arrAddonIDs);
		$arrAddons = UniteFunctionsUC::arrayToAssoc($arrAddons,"id");
		
		$order = 0;
		foreach($arrAddonIDs as $addonID){
			$order++;
	
			$arrAddon = UniteFunctionsUC::getVal($arrAddons, $addonID);
			if(!empty($arrAddon) && $arrAddon["ordering"] == $order)
				continue;
	
			$arrUpdate = array();
			$arrUpdate["ordering"] = $order;
			$this->db->update(GlobalsUC::$table_addons, $arrUpdate, array("id"=>$addonID));
		}
	
	}
	
	/**
	 *
	 * copy items to some category
	 */
	private function copyAddons($arrAddonIDs,$catID){
		$category = new UniteCreatorCategories();
		$category->validateCatExist($catID);
	
		foreach($arrAddonIDs as $addonID){
			$this->copyAddon($addonID, $catID);
		}
	}
	
	
	/**
	 * migrate addons from some type to blox
	 * copy the addon, skip if exists, remove the type
	 */
	public function migrateAddonsFromType($addonType){
		
		
		dmp("migrate function disabled");
		exit();
		
		if(empty($addonType))
			return(false);
		
		$arrAddons = $this->getArrAddons("", array(), $addonType);
		
		foreach($arrAddons as $addon){
			
			$alias = $addon->getAlias();
			if(empty($alias))
				continue;
			
			$title = $addon->getTitle();
				
			$isExists = $this->isAddonExistsByName($alias);
			if($isExists == true)
				continue;			
			
			$duplicatedID = $addon->duplicate();
			if(empty($duplicatedID))
				continue;
			
			$addonDuplicated = new UniteCreatorAddon();
			$addonDuplicated->initByID($duplicatedID);
			$addonDuplicated->convertToType(GlobalsUC::ADDON_TYPE_REGULAR_ADDON, $alias, $title);
			
			
		}
		
	}
	
	/**
	 * migrate addons from type to type
	 */
	public function migrateAddonsToType($typeFrom, $typeTo){
		
		$arrAddons = $this->getArrAddons("", array(), $typeFrom);
				
		$arrLog = array();
		$arrLog[] = "num addons: ".count($arrAddons);
		
		foreach($arrAddons as $addon){
			
			$title = $addon->getTitle();
			
			$converted = $addon->convertToType($typeTo);
			
			if($converted == true)
				$arrLog[] = "$title converted";
			else
				$arrLog[] = "$title skipped";
			
		}
		
		return($arrLog);		
	}
	
	
	/**
	 *
	 * move multiple items to some category
	 */
	private function moveAddons($arrAddonIDs, $catID){
		
		$category = new UniteCreatorCategories();
		$category->validateCatExist($catID);
	
		foreach($arrAddonIDs as $addonID){
			$this->moveAddon($addonID, $catID);
		}
		
	}
	
	
	/**
	 *
	 * move addons to some category by change category id
	 */
	private function moveAddon($addonID, $catID){
		$addonID = (int)$addonID;
		$catID = (int)$catID;
	
		$arrUpdate = array();
		$arrUpdate["catid"] = $catID;
		$this->db->update(GlobalsUC::$table_addons, $arrUpdate, array("id"=>$addonID));
	}
	
	/**
	 *
	 * duplciate addons within same category
	 */
	private function duplicateAddons($arrAddonIDs, $catID){
	
		foreach($arrAddonIDs as $addonID){
			$addon = new UniteCreatorAddon();
			$addon->initByID($addonID);
			$addon->duplicate();
		}
	
	}
	
	
	/**
	 * create addon from data
	 */
	public function createFromData($data){
	
		$objAddon = new UniteCreatorAddon();
		$id = $objAddon->add($data);
	
		return($id);
	}
	
	
	/**
	 * create addon from manager
	 */
	public function createFromManager($data){
		
		$title = UniteFunctionsUC::getVal($data, "title");
		$name = UniteFunctionsUC::getVal($data, "name");
		$description = UniteFunctionsUC::getVal($data, "description");
		$catID = UniteFunctionsUC::getVal($data, "catid");
		$parentID = UniteFunctionsUC::getVal($data, "parent_id");
		
		$addonType = $this->getAddonTypeFromData($data);
		$objAddonType = UniteCreatorAddonType::getAddonTypeObject($addonType);
		
		$objManager = UniteCreatorManager::getObjManagerByAddonType($addonType, $data);
		$isLayout = $objManager->getIsLayoutType();
		
		
		if($isLayout == false){
			
			$objAddon = new UniteCreatorAddon();
			$newAddonID = $objAddon->addSmall($title, $name, $description, $catID, $addonType);
			$urlAddon = HelperUC::getViewUrl_EditAddon($newAddonID);
			$htmlItem = $objManager->getAddonAdminHtml($objAddon);
			
		}else{
			
			//add layout
			
			$objLayout = new UniteCreatorLayout();
			$objLayout->setLayoutType($addonType);
			
			$params = array();
			if(!empty($parentID))
				$params["parent_id"] = $parentID;
			
			$newLayoutID = $objLayout->createSmall($title, $name, $description, $catID, $params);
			$urlAddon = HelperUC::getViewUrl_Layout($newLayoutID);
			
			$htmlItem = $objManager->getAddonAdminHtml($objLayout);
			
		}
		
		
		$objCats = new UniteCreatorCategories();
		$htmlCatList = $objCats->getHtmlCatList($catID, $objAddonType);
		
		$output = array();
		$output["htmlItem"] = $htmlItem;
		$output["htmlCats"] = $htmlCatList;
		$output["url_addon"] = $urlAddon;
		
		return($output);
	}
	
	
	/**
	 * update addon from data
	 */
	public function updateAddonFromData($data){
		
		$addonData = UniteFunctionsUC::getVal($data, "addon_data");
		
		if(!empty($addonData))
			$data = UniteFunctionsUC::decodeContent($addonData);
		
		$addonID = UniteFunctionsUC::getVal($data, "id");
		
		$objAddon = new UniteCreatorAddon();
		$objAddon->initByID($addonID);
		$objAddon->update($data);
	}

	
	/**
	 * duplicate addon from data
	 */
	public function duplicateAddonFromData($data){
		
		$addonID = UniteFunctionsUC::getVal($data, "addonID");
		
		$objAddon = new UniteCreatorAddon();
		$objAddon->initByID($addonID);
		
		$response = $objAddon->duplicate(true);
		
		$htmlRow = HelperHtmlUC::getTableAddonsRow($response["id"], $response["title"]);
		
		return($htmlRow);
	}
	
	
	/**
	 * import addon from library
	 */
	public function importAddonFromLibrary($data){
		
		$path = UniteFunctionsUC::getVal($data, "path");
		if(empty($path))
			UniteFunctionsUC::throwError("Empty Path");
		
		$library = new UniteCreatorLibrary();
		$addonData = $library->getPluginDataByPath($path);
		
		$objAddon = new UniteCreatorAddon();
		$addonID = $objAddon->add($addonData);
		$title = $objAddon->getTitle(true);
		
		$htmlRow = HelperHtmlUC::getTableAddonsRow($addonID, $title);
		
		return($htmlRow);
	}
	
	
	/**
	 * delete addon from imput data
	 */
	public function deleteAddonFromData($data){
		
		$addonID = UniteFunctionsUC::getVal($data, "addonID");
		UniteFunctionsUC::validateNotEmpty($addonID, "Widget ID");
				
		$this->db->delete(GlobalsUC::$table_addons, "id=$addonID");
		
	}
	
	
	/**
	 * update item title
	 */
	public function updateAddonTitleFromData($data){
		
		$itemID = $data["itemID"];
		$title = $data["title"];
		$name = $data["name"];
		$description = $data["description"];
				
		$addonType = $this->getAddonTypeFromData($data);
		$isLayout = HelperUC::isLayoutAddonType($addonType);
				
		if($isLayout == false){
			$addon = new UniteCreatorAddon();
			$addon->initByID($itemID);
			$addon->updateNameTitle($name, $title, $description);
		}else{
			
			$objLayout = new UniteCreatorLayout();
			$objLayout->initByID($itemID);
			$objLayout->updateTitle($title);
			$objLayout->updateParam("description", $description);
			
			//check update isfree param
			if(isset($data["isfree"])){
				$isFree = UniteFunctionsUC::getVal($data, "isfree");
				$isFree = UniteFunctionsUC::strToBool($isFree);
				
				$objLayout->updateParam("isfree", $isFree);
				
			}
			
			
		}
		
	}
	
	
	/**
	 * update items activation from data
	 * @param $data
	 */
	public function activateAddonsFromData($data){
		
		$arrIDs = UniteFunctionsUC::getVal($data, "addons_ids");
		if(is_array($arrIDs) == false)
			return(false);
		
		if(empty($arrIDs))
			return(fale);
		
		$strIDs = implode("," , $arrIDs);
		
		UniteFunctionsUC::validateIDsList($strIDs,"id's list");
		
		$isActive = UniteFunctionsUC::getVal($data, "is_active");
		$isActive = (int)UniteFunctionsUC::strToBool($isActive);
		
		$tableAddons = GlobalsUC::$table_addons;
		$query = "update {$tableAddons} set is_active={$isActive} where id in($strIDs)";
		
		$this->db->runSql($query);
			
	}
	
	
	/**
	 * remove items from data
	 */
	public function removeAddonsFromData($data){
		
		$catID = UniteFunctionsUC::getVal($data, "catid");
		$type = $this->getAddonTypeFromData($data);
		$parentID = UniteFunctionsUC::getVal($data, "parentID");
		$parentID = (int)$parentID;
		
		$addonsIDs = UniteFunctionsUC::getVal($data, "arrAddonsIDs");
		
		
		if(HelperUC::isLayoutAddonType($type) == false){		//delete addons
			$this->deleteAddons($addonsIDs);
		}
		else{		//delete layouts
						
			$objLayouts = new UniteCreatorLayouts();
			$objLayouts->deleteLayouts($addonsIDs);
		}
		
		$response = $this->getCatsAndAddonsHtml($catID, $type, $data, $parentID);
		
		return($response);
	}
	
	
	
	
	
	
	/**
	 *
	 * save items order from data
	 */
	public function saveOrderFromData($data){
		
		$addonType = $this->getAddonTypeFromData($data);
		$isLayout = HelperUC::isLayoutAddonType($addonType);
		
		$addonsIDs = UniteFunctionsUC::getVal($data, "addons_order");
		
		if(empty($addonsIDs))
			return(false);
	
		if($isLayout == false)
			$this->saveAddonsOrder($addonsIDs);
		else{
			
			$objLayouts = new UniteCreatorLayouts();
			$objLayouts->updateOrdering($addonsIDs);
			
		}
			
	}

	
	/**
	 *
	 * copy / move addons to some category
	 * @param $data
	 */
	public function moveAddonsFromData($data){
				
		$targetCatID = UniteFunctionsUC::getVal($data, "targetCatID");
		$selectedCatID = UniteFunctionsUC::getVal($data, "selectedCatID");
		$targetParentID = UniteFunctionsUC::getVal($data, "parentID");
		$targetParentID = (int)$targetParentID;
		
		$type = $this->getAddonTypeFromData($data);
		$isLayouts = HelperUC::isLayoutAddonType($type);
		
		
		$arrAddonIDs = UniteFunctionsUC::getVal($data, "arrAddonIDs");
		
		UniteFunctionsUC::validateNotEmpty($targetCatID,"category id");
		UniteFunctionsUC::validateNotEmpty($arrAddonIDs,"addon id's");
		
		if($isLayouts == false)		//addons
			$this->moveAddons($arrAddonIDs, $targetCatID);
		else{
			$objLayouts = new UniteCreatorLayouts();
			$objLayouts->moveLayouts($arrAddonIDs, $targetCatID, $targetParentID);
		}
		
		
		$repsonse = $this->getCatsAndAddonsHtml($selectedCatID, $type, $data, $targetParentID);
		return($repsonse);
	}
	
	
	/**
	 * duplicate items
	 */
	public function duplicateAddonsFromData($data){
			
		$catID = UniteFunctionsUC::getVal($data, "catID");
		$arrIDs = UniteFunctionsUC::getVal($data, "arrIDs");
		$parentID = UniteFunctionsUC::getVal($data, "parentID");
		
		$type = $this->getAddonTypeFromData($data);
		
		
		$isLayouts = HelperUC::isLayoutAddonType($type);
		
		if($isLayouts == false)
			$this->duplicateAddons($arrIDs, $catID);
		else{
			$objLayouts = new UniteCreatorLayouts();
			$objLayouts->duplicateLayouts($arrIDs, $catID);
		}
		
			
		$response = $this->getCatsAndAddonsHtml($catID, $type, $data, $parentID);
	
		return($response);
	}
	
	
	/**
	 * shift addons in category from some order (more then the order).
	 */
	public function shiftOrder($catID, $order){
		
		$tableAddons = GlobalsUC::$table_addons;
		
		$query = "update $tableAddons set ordering = ordering+1 where catid={$catID} and ordering > {$order}";
		
		$this->db->runSql($query);
	}
	
	
	/**
	 * init addon by data
	 */
	public function initAddonByData($data){
		
		if(is_string($data)){
			$data = json_decode($data);
			$data = UniteFunctionsUC::convertStdClassToArray($data);
		}
		
		$addonID = UniteFunctionsUC::getVal($data, "id");
		$addonName = UniteFunctionsUC::getVal($data, "name");
		$arrConfig = UniteFunctionsUC::getVal($data, "config");
		$arrItemsData = UniteFunctionsUC::getVal($data, "items");
		$addonType = UniteFunctionsUC::getVal($data, "addontype");
		$arrFonts = UniteFunctionsUC::getVal($data, "fonts");
		$arrOptions = UniteFunctionsUC::getVal($data, "options");
		
		$isInsideGrid = UniteFunctionsUC::getVal($data, "is_inside_grid"); 
		$isInsideGrid = UniteFunctionsUC::strToBool($isInsideGrid);
		
		
		$objAddon = new UniteCreatorAddon();
		
		if(!empty($addonID))
			$objAddon->initByID($addonID);
		else{
			if(!empty($addonType))
				$objAddon->initByAlias($addonName, $addonType);
			else
				$objAddon->initByName($addonName);
		}
		
		$objAddon->setParamsValues($arrConfig);
		
		if(is_array($arrItemsData))
			$objAddon->setArrItems($arrItemsData);
		
		if(!empty($arrFonts) && is_array($arrFonts))
			$objAddon->setArrFonts($arrFonts);
		
		if($isInsideGrid == true)
			$objAddon->setIsInsideGrid();
		
		return($objAddon);
	}
	
	
	
	
	/**
	 * show preview by data
	 */
	public function showAddonPreviewFromData($data){
		
		try{
						
			$objAddon = $this->initAddonByData($data);
						
			$objOutput = new UniteCreatorOutput();
			$objOutput->setPreviewAddonMode();
			
			$objOutput->initByAddon($objAddon);
			$objOutput->putPreviewHtml();
						
		}catch(Exception $e){
			$message = $e->getMessage();
									
			$errorMessage = HelperUC::getHtmlErrorMessage($message, GlobalsUC::SHOW_TRACE_FRONT);
			
			echo UniteProviderFunctionsUC::escCombinedHtml($errorMessage);
		}
		
		exit();
	}
	
	
	/**
	 * save test addon data to some slot
	 */
	public function saveTestAddonData($data, $slot=1){
		
		$addonName = UniteFunctionsUC::getVal($data, "name");
		$addontype = UniteFunctionsUC::getVal($data, "addontype");
		
		$config = UniteFunctionsUC::getVal($data, "config", array());
		$items = UniteFunctionsUC::getVal($data, "items", array());
		$fonts = UniteFunctionsUC::getVal($data, "fonts");
		
		
		$objAddon = new UniteCreatorAddon();
		$objAddon->initByMixed($addonName, $addontype);
		
		$objAddon->saveTestSlotData($slot, $config, $items, $fonts);
	}
	
	
	/**
	 * save addon defaults from data
	 */
	public function saveAddonDefaultsFromData($data){
				
		$this->saveTestAddonData($data, 2);
	}
	
	
	/**
	 * get test addon data
	 * @param $data
	 */
	public function getTestAddonData($data){
		
		$objAddon = $this->initAddonByData($data);
		$slotNum = UniteFunctionsUC::getVal($data, "slotnum");
		
		$data = $objAddon->getTestData($slotNum);
		
		return($data);
	}
	
	/**
	 * delete test addon data
	 * @param $data
	 */
	public function deleteTestAddonData($data){
		$objAddon = $this->initAddonByData($data);
		$slotNum = UniteFunctionsUC::getVal($data, "slotnum");
		
		$objAddon->clearTestDataSlot($slotNum);
	}
	
	
	/**
	 * export addon
	 */
	public function exportAddon($data){
		
		$addonType = $this->getAddonTypeFromData($data);
		$isLayout = HelperUC::isLayoutAddonType($addonType);
		
		try{
			
			if($isLayout == false){
				
				$addon = $this->initAddonByData($data);
				$exporter = new UniteCreatorExporter();
				$exporter->initByAddon($addon);
				$exporter->export();
				
			}else{
				
				$layoutID = UniteFunctionsUC::getVal($data, "id");
				
				$objLayout = new UniteCreatorLayout();
				$objLayout->initByID($layoutID);
				
				$objExporter = new UniteCreatorLayoutsExporter();
				$objExporter->initByLayout($objLayout);
				$objExporter->export();
				
			}
						
		}catch(Exception $e){
			
			$message = "Export error: " . $e->getMessage();
			echo esc_html($message);
		}
		
		$message = "Export error:item not exported"; 
		echo esc_html($message);
		exit();
		
	}
	
	
	/**
	 * export category addons
	 */
	public function exportCatAddons($data, $exportType=""){
		
		try{
			$catID = UniteFunctionsUC::getVal($data, "catid");
			UniteFunctionsUC::validateNotEmpty($catID);
			
			$objCats = new UniteCreatorCategories();
			$objCats->validateCatExist($catID);
			
			$exporter = new UniteCreatorExporter();
			$exporter->exportCatAddons($catID, $exportType);
			
		}catch(Exception $e){
			$message = "Export category addons error: " . $e->getMessage();
			echo esc_html($message);
		}
		
		$message = "Export category addons error: addons not exported";
		echo esc_html($message);
		exit();
		
	}
	
	
	/**
	 * import addons
	 */
	public function importAddons($data){
				
		$catID = UniteFunctionsUC::getVal($data, "catid");
		$addonType = $this->getAddonTypeFromData($data);
		$isLayout = HelperUC::isLayoutAddonType($addonType);
		
		$isOverwrite = UniteFunctionsUC::getVal($data, "isoverwrite");
		$isOverwrite = UniteFunctionsUC::strToBool($isOverwrite);
		
		$importType	= UniteFunctionsUC::getVal($data, "importtype"); 
						
		switch($importType){
			case "autodetect":
				$forceToCat = false;
			break;
			case "specific":
				$forceToCat = true;
			break;
			default:
				UniteFunctionsUC::throwError("Wrong type: $importType");
			break;
		}
		
		if(empty($catID))
			$catID = null;
		
		$arrTempFile = UniteFunctionsUC::getVal($_FILES, "file");
		
	//---- addon -----
		
		if($isLayout == false){			
			
			$exporter = new UniteCreatorExporter();
			$exporter->setMustImportAddonType($addonType);
			
			$importLog = $exporter->import($catID, $arrTempFile, $isOverwrite, $forceToCat);
			
			$catID = $exporter->getImportedCatID();
			
			
		}else{
			
	//----- layout -------
									
			$arrParams = array();
			if(!empty($forceToCat))
				$arrParams["force_to_cat"] = $catID;
			
			if($addonType == GlobalsUnlimitedElements::ADDONSTYPE_ELEMENTOR_TEMPLATE){
				
				$exporterLayouts = new UniteCreatorLayoutsExporterElementor();
				$exporterLayouts->importElementorLayoutNew($arrTempFile, $isOverwrite, $data);
				
			}else{
				$exporterLayouts = new UniteCreatorLayoutsExporter();
				$exporterLayouts->import($arrTempFile, null, $isOverwrite, $arrParams);
			}
							
			$importLog = $exporterLayouts->getLogText();
		}
		
				
		$response = $this->getCatsAndAddonsHtml($catID, $addonType, $data);
		$response["import_log"] = $importLog;
		
		return($response);
	}
	
	
	/**
	 * update addon from catalog
	 */
	public function updateAddonFromCatalogFromData($data){
		
		$widgetName = UniteFunctionsUC::getVal($data, "widget_name");
		
		$objAddon = new UniteCreatorAddon();
		
		$addonID = null;
		
		if(!empty($widgetName)){
			
			$alias = str_replace("ucaddon_", "", $widgetName);
			
			$objAddon->initByAlias($alias, GlobalsUC::ADDON_TYPE_ELEMENTOR);
			
		}else{		//init by id
			
			$addonID = UniteFunctionsUC::getVal($data, "id");
			$addonID = (int)$addonID;
			
			$objAddon->initByID($addonID);
		}
		
		$installData = array();
		
		$installData["name"] = $objAddon->getAlias();
		$installData["cat"] = $objAddon->getCatTitle();
		$installData["type"] = $objAddon->getType();
		
		$webAPI = new UniteCreatorWebAPI();
		
		$webAPI->checkUpdateCatalog(true);
		$webAPI->installCatalogAddonFromData($installData);
		
		if(empty($addonID))
			return(null);
		
		$urlRedirect = HelperUC::getViewUrl_EditAddon($addonID);
		
		return($urlRedirect);
	}

	
	/**
	 * update bulk params in addons from data
	 * return bulk dialog html
	 */
	public function updateAddonsBulkFromData($data){
		
		$paramType = UniteFunctionsUC::getVal($data, "param_type");
		$paramData = UniteFunctionsUC::getVal($data, "param_data");
		$paramName = UniteFunctionsUC::getVal($paramData, "name");
		
		$sourceAddonID = UniteFunctionsUC::getVal($data, "addon_id");
		$targetAddonIDs = UniteFunctionsUC::getVal($data, "addon_ids");
		$action = UniteFunctionsUC::getVal($data, "action_bulk");
		
		$position = UniteFunctionsUC::getVal($data, "param_position");
		
		//get position in addon
		
		$objAddon = new UniteCreatorAddon();
		$objAddon->initByID($sourceAddonID);
		
		$isMain = ($paramType == "main");
		
		if(empty($position))
			$position = $objAddon->getParamPosition($paramName, $isMain);
		
		//clear category data
		unset($paramData["__attr_catid__"]);

		
		//update addons
		
		foreach($targetAddonIDs as $addonID){
			
			$objTargetAddon = new UniteCreatorAddon();
			$objTargetAddon->initByID($addonID);
			
			switch($action){
				case "update":
					$objTargetAddon->addUpdateParam_updateDB($paramData, $isMain, $position);
				break;
				case "delete":
					$objTargetAddon->deleteParam_updateDB($paramName, $isMain);
				break;
				default:
					UniteFunctionsUC::throwError("Wrong bulk action: $action");
				break;
			}
		}
		
	}
	
	/**
	 * install addons from catalog if not exists
	 */
	public function installMultipleAddons($arrAddonNames, $addonType){
		
		if(empty($addonType))
			return("installMultipleAddons - addonType is empty");
		
		if(empty($arrAddonNames))
			return("installMultipleAddons - no addons found");
		
			
		$numAddons = count($arrAddonNames);
		
		if($numAddons > 25)
			UniteFunctionsUC::throwError("Too much widgets to install: $numAddons");
		
		$webAPI = new UniteCreatorWebAPI();
		
		$strLog = "";
		
		foreach($arrAddonNames as $alias){
			
			$isExists = $this->isAddonExistsByName($alias, $addonType);
					
			if(!empty($strLog))
				$strLog .= "\n"; 
			
			if($isExists == true){
				$strLog .= "Skipped widget install: $alias";
				continue;
			}
						
			$strLog .= $webAPI->installCatalogAddonByName($alias, $addonType);
			
		}
		
		
		return($strLog);
	}
	
	
}

