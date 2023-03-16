<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');


class UniteCreatorCategoriesWork extends UniteElementsBaseUC{
	
	private $titleBase = "New Category";
	private static $serial = 0;
	private $lastAddonType;
	
	const TYPE_LAYOUT = "layout";
	
	
	public function __construct(){
		parent::__construct();
		
	}
	
	/**
	 * 
	 * validate that category exists
	 */
	public function validateCatExist($catID){
		
		$this->getCat($catID);
	}
	
	
	/**
	 * validate if category not exists
	 */
	private function validateCatTitleNotExists($title, $type, $catID=null){
		
		$isExists = $this->isCatExistsByTitle($title, $type, $catID);
		
		if($isExists == true)
			UniteFunctionsUC::throwError("Category with title: $title already exists");
		
	}
	
	/**
	 * validate category title
	 */
	private function validateTitle($title){
		
		UniteFunctionsUC::validateNotEmpty($title, "Category Title");
		
		UniteFunctionsUC::validateNoTags($title, "Category Title");
		
	}
	
	/**
	 * validate new title before add or update by type
	 */
	private function validateTitleByType($title, $type, $catID = null){
		
		$this->validateTitle($title);
		
		//validate that not exists for all the types
		$this->validateCatTitleNotExists($title, $type, $catID);
		
	}
	
	
	private function a___________GETTERS__________(){}
	
	
	/**
	 * get uncategorised category
	 */
	private function getFirstCats(UniteCreatorAddonType $objAddonType, $showAll){
		
		$typeName = $objAddonType->typeName;
		
		$filterActive = UniteCreatorManagerAddons::getStateFilterActive($typeName);
		
		if($objAddonType->isLayout == false){
			
			$objAddons = new UniteCreatorAddons();
			$numAddonsZero = $objAddons->getNumAddons(0, $filterActive, $objAddonType);
		
		}else{
			
			$objLayouts = new UniteCreatorLayouts();
			$numAddonsZero = $objLayouts->getNumCatLayouts(0, $objAddonType);
			
		}
		
		
		//all
		$arrCatAll = array();
		$arrCatAll["id"] = "all";
		$arrCatAll["title"] = HelperUC::getText("all_addons");
		$arrCatAll["alias"] = "";
		$arrCatAll["ordering"] = 0;
		$arrCatAll["parent_id"] = "";
		$arrCatAll["params"] = "";
		$arrCatAll["type"] = "";
		$arrCatAll["num_addons"] = "set";
		
		
		//uncategorized
		$arrCatZero = array();
		$arrCatZero["id"] = 0;
		$arrCatZero["title"] = HelperUC::getText("uncategorized");
		$arrCatZero["alias"] = "";
		$arrCatZero["ordering"] = 0;
		$arrCatZero["parent_id"] = "";
		$arrCatZero["params"] = "";
		$arrCatZero["type"] = "";
		$arrCatZero["num_addons"] = $numAddonsZero;
		
		$arrCats = array();
		
		if($showAll == true)
			$arrCats[] = $arrCatAll;
		
		if($numAddonsZero > 0)
			$arrCats[] = $arrCatZero;
		
		return($arrCats);
	}
	
	
	/**
	 * get category list simple
	 */
	private function getListSimple($addonType = ""){
		
		$where = $this->db->getSqlAddonType($addonType, "type");
				
		$response = $this->db->fetch(GlobalsUC::$table_categories, $where);
		
		return($response);
	}

	
	/**
	 * get list extra where ending
	 */
	private function getListExtra_WhereEnding($type, $filterTitle="", $ordering="", $params = array()){
				
		$whereFilterActive = UniteFunctionsUC::getVal($params, "where_filter_active");
		
		$filterSearchAddons = UniteFunctionsUC::getVal($params, "filter_search_addons");
		$filterSearchAddons = trim($filterSearchAddons);
		
		$type = $this->db->escape($type);
	
		if(empty($type))
			$where = "where cats.type is null or cats.type=''";
		else
			$where = "where cats.type='$type'";
	
		//add filter by title
		if(!empty($filterTitle)){				
			
			$filterTitle = $this->db->escape($filterTitle);
			$where .= " and cats.title like '%$filterTitle%'";
		}
				
		if($type != GlobalsUnlimitedElements::ADDONSTYPE_ELEMENTOR_TEMPLATE && !empty($filterSearchAddons)){
			
			$filterSearchAddons = $this->db->escape($filterSearchAddons);
			
			$where .= " and (addons.title like '%$filterSearchAddons%' or cats.title like '%$filterSearchAddons%')";
		}
		
	
		//add ordering
		$ordering = strtolower($ordering);
		switch($ordering){
			case "asc":
				$ordering = "title asc";
				break;
			case "desc":
				$ordering = "title desc";
				break;
			default:
				$ordering = "ordering";
			break;
		}
	
		$whereEnding = "$where $whereFilterActive GROUP BY cats.id order by $ordering";
	
		return($whereEnding);
	}
	
	
	/**
	 * get layouts list extra
	 */
	private function getListExtraLayouts($filterTitle="", $ordering=""){
		
		$type = self::TYPE_LAYOUT;
		
		$whereEnding = $this->getListExtra_WhereEnding($type, $filterTitle, $ordering);
		
		$tableCats = GlobalsUC::$table_categories;
		$tableLayouts = GlobalsUC::$table_layouts;
		
		$query = "select cats.*, count(layouts.id) as num_layouts from {$tableCats} as cats";
		$query .= " left join $tableLayouts as layouts on layouts.catid=cats.id $whereEnding";
		
		$arrCats = $this->db->fetchSql($query);
		
		//make short output
		$arrCatsNew = array();
		
		//add uncategorised
		$arrCatsNew[] = array(
				"id" => 0,
				"title" => esc_html__("Uncategorized", "unlimited-elements-for-elementor"),
				"num_layouts" => 0
		);
		
		foreach($arrCats as $key=>$cat){
			
			$arr = array();
			$arr["id"] = $cat["id"];
			$arr["title"] = $cat["title"];
			$arr["num_layouts"] = $cat["num_layouts"];
			
			$arrCatsNew[] = $arr;
		}
		
		return($arrCatsNew);
	}
	
		
	/**
	 * get list extra query
	 */
	protected function getListExtraQuery(UniteCreatorAddonType $objAddonType, $filterTitle="", $ordering="", $params = null){
		
		$whereFilterActive = "";
		
		$isLayoutsType = $objAddonType->isLayout;
		
		$addonType = $objAddonType->typeName;
		
		if($isLayoutsType == false)
			$whereFilterActive = UniteCreatorAddons::getFilterActiveWhere(null, " and addons", $addonType);
		
		$type = $objAddonType->typeNameCategory;
		
		if(empty($params))
			$params = array();
				
		$params["where_filter_active"] = $whereFilterActive;

		
		$whereEnding = $this->getListExtra_WhereEnding($type, $filterTitle, $ordering, $params);
		
		$tableCats = GlobalsUC::$table_categories;
		
		if($isLayoutsType == false)
			$tableAddons = GlobalsUC::$table_addons;
		else
			$tableAddons = GlobalsUC::$table_layouts;
		
		//if no table like in wp, get without numbers
		if(empty($tableAddons)){
			
			$query = "select cats.* from {$tableCats} as cats {$whereEnding}";
			
		}else{
			
			$query = "select cats.*, count(addons.id) as num_addons from {$tableCats} as cats";
			$query .= " left join $tableAddons as addons on addons.catid=cats.id $whereEnding";
		}
		
		return($query);
	}
	
	
	/**
	 * get categories list with "all" and "uncategorised" and num addons
	 * ordering = asc / desc / empty
	 */
	public function getListExtra(UniteCreatorAddonType $objAddonType, $filterTitle="", $ordering="", $showAll = false, $params = null){
		
		$query = $this->getListExtraQuery($objAddonType, $filterTitle, $ordering, $params);
		
		$arrCats = $this->db->fetchSql($query);
		
		if(empty($arrCats))
			$arrCats = array();
		
		$arrFirstCats = $this->getFirstCats($objAddonType, $showAll);
		
		$arrCats = array_merge($arrFirstCats, $arrCats);
		
		//add num layouts, in post layouts it's missing for elementor template
		$isLayout = $objAddonType->isLayout;
				
		if($isLayout == true){
			
			$objLayouts = new UniteCreatorLayouts();
			
			foreach($arrCats as $index => $cat){
				
				$catID = UniteFunctionsUC::getVal($cat, "id");
				
				//disable uncategorized
				if(empty($catID))
					$numLayouts = 0;
				else
					$numLayouts = $objLayouts->getNumCatLayouts($catID, $objAddonType);
				
				$cat["num_addons"] = $numLayouts;
				$arrCats[$index] = $cat;
			}
		}
				
		if($showAll == false)
			return($arrCats);
		
		//set number of all addons
		$numAddons = 0;
		foreach($arrCats as $cat){
			$numCatAddons = $cat["num_addons"];
			if(!is_numeric($numCatAddons))
				continue;
			
			$numAddons += $numCatAddons;
		}
		
		$arrCats[0]["num_addons"] = $numAddons;
		
		return($arrCats);
	}
	
	
	/**
	 * get category records simple without num items
	 */
	public function getCatRecords($addonType){
		
		$where = "";
		if($addonType != "all")
			$where = $this->db->getSqlAddonType($addonType, "type");
				
		$arrCats = $this->db->fetch(GlobalsUC::$table_categories, $where, "ordering");
				
		return($arrCats);
	}
	
	
	/**
	 * get category records extra with num items
	 */
	public function getCatRecordsExtra($type){
		
		$filterTitle="";
		$ordering = "";
		
		$whereEnding = $this->getListExtra_WhereEnding($type, $filterTitle, $ordering);
		
		$tableCats = GlobalsUC::$table_categories;
		$tableAddons = GlobalsUC::$table_addons;
		
		$query = "select cats.*, count(addons.id) as num_addons from {$tableCats} as cats";
		$query .= " left join $tableAddons as addons on addons.catid=cats.id $whereEnding";
		
		$arrCats = $this->db->fetchSql($query);
		
		return($arrCats);
	}
	
	
	/**
	 * get first category array by cat type
	 */
	private function getFirstCatByAddType($addType){
		
		$arrCatsOutput = array();
		
		switch($addType){
			case "empty":
				$arrCatsOutput[""] = esc_html__("[Not Selected]", "unlimited-elements-for-elementor");
			break;
			case "new":
				$arrCatsOutput["new"] = esc_html__("[Add New Category]", "unlimited-elements-for-elementor");
			break;
			case "component":
				$arrCatsOutput[""] = esc_html__("[From Gallery Settings]", "unlimited-elements-for-elementor");
			break;
			case "all_uncat":
				$arrCatsOutput["all"] = HelperUC::getText("all_addons");
				$arrCatsOutput[0] = HelperUC::getText("uncategorized");
			break;
			case "uncategorized":
				$arrCatsOutput[0] = HelperUC::getText("uncategorized");
			break;
			case "all_uncat_layouts":
				$arrCatsOutput["all"] = HelperUC::getText("all_layouts");
				$arrCatsOutput[0] = HelperUC::getText("uncategorized");
			break;
			
		}
		
		return($arrCatsOutput);
	}
	
	
	/**
	 * get category records with add type
	 */
	public function getCatRecordsWithAddType($addType, $type){
				
		$arrCats = $this->getCatRecords($type);
		
		$arrCatsOutput = $this->getFirstCatByAddType($addType);
		
		foreach($arrCats as $cat){
			$catID = UniteFunctionsUC::getVal($cat, "id");
			$arrCatsOutput[$catID] = $cat;
		}
		
		return($arrCatsOutput);
	}
	
	
	/**
	 * 
	 * get categories list short
	 * addtype: empty (empty category), new (craete new category)
	 */
	public function getCatsShort($addType = "", $type="", $addTypeToTitle = false){
		
		$arrCats = $this->getCatRecords($type);
				
		$arrCatsOutput = $this->getFirstCatByAddType($addType);
		
		foreach($arrCats as $cat){
			
			$catID = UniteFunctionsUC::getVal($cat, "id");
			$title = UniteFunctionsUC::getVal($cat, "title");
			$type = UniteFunctionsUC::getVal($cat, "type");
			
			$objType = null;
			
			//if type not found - skip category
			try{
				
				$objType = UniteCreatorAddonType::getAddonTypeObject($type);
				
			}catch(Exception $e){
			}
			
			//add type title
			if(!empty($objType) && $addTypeToTitle == true)
				$title = $objType->titlePrefix.$title;
			
			$arrCatsOutput[$catID] = $title;
		}
		
		return($arrCatsOutput);
	}
	
	
	/**
	 * get categories array
	 */
	public function getArrCats($type){
		
		$records = $this->getCatRecordsExtra($type);
		
		$arrCats = array();
		foreach($records as $record){
			$cat = $record;
			
			$params = UniteFunctionsUC::getVal($record, "params");
			$cat["params"] = UniteFunctionsUC::jsonDecode($params, true);
			$arrCats[] = $cat;
		}
		
		return($arrCats);
	}
	
	
	/**
	 * 
	 * get assoc value of category name
	 */
	private function getArrCatTitlesAssoc($type=""){
		
		$arrCats = $this->getListSimple($type);
		$arrAssoc = array();
		foreach($arrCats as $cat){
			$arrAssoc[$cat["title"]] = true;
		}
		return($arrAssoc);
	}
	
	
	/**
	 * 
	 * get max order from categories list
	 */
	private function getMaxOrder($type=""){
		
		$where = $this->db->getSqlAddonType($type, "type");
		
		$query = "select MAX(ordering) as maxorder from ".GlobalsUC::$table_categories." $where";
		
		$rows = $this->db->fetchSql($query);
		
		$maxOrder = 0;
		if(count($rows)>0) 
			$maxOrder = $rows[0]["maxorder"];
		
		if(!is_numeric($maxOrder))
			$maxOrder = 0;
		
		return($maxOrder);
	}
	
	
	/**
	 * get true/false if some category exists
	 */
	public function isCatExists($catID){
		
		$arrCat = null;
		
		try{
		
			$arrCat = $this->db->fetchSingle(GlobalsUC::$table_categories,"id=$catID");
			
		}catch(Exception $e){
					
		}
		
		return !empty($arrCat);		
	}
	
	
	/**
	 * check if category exists by title
	 * check in all cats except the current category id
	 */
	private function isCatExistsByTitle($title, $type = "", $catID = null){
		
		if(empty($type))
			$type = UniteCreatorDB::ISNULL;
		
		$arrWhere = array();
		$arrWhere["title"] = $title;
		$arrWhere["type"] = $type;
		
		$response = $this->db->fetch(GlobalsUC::$table_categories, $arrWhere);
		
		if(empty($response))
			return(false);
		
		//check by catID
		if(empty($catID))
			return(true);
		
		$cat = $response[0];
		if($cat["id"] == $catID)
			return(false);
		else
			return(true);		
	}
	
	
	/**
	 * 
	 * get category
	 */
	public function getCat($catID){
		
		$catID = (int)$catID;
		
		try{
			
			$arrCat = $this->db->fetchSingle(GlobalsUC::$table_categories,"id=$catID");
			
		}catch(Exception $e){
			
			UniteFunctionsUC::throwError("Category with id: $catID not found");
			
		}
			
		return($arrCat);
	}
	
	
	/**
	 * get category type by id
	 */
	public function getCatType($catID){
		
		$arrCat = $this->getCat($catID);
		$type = UniteFunctionsUC::getVal($arrCat, "type");
		
		return($type);
	}
	
	
	/**
	 * get category by title
	 * if not found - return null
	 */
	public function getCatByTitle($title, $type=""){
		
		if(empty($type))
			$type = UniteCreatorDB::ISNULL;
		
		$arrWhere = array();
		$arrWhere["title"] = $title;
		$arrWhere["type"] = $type;
		
		try{
			$arrCat = $this->db->fetchSingle(GlobalsUC::$table_categories, $arrWhere);
		
		if(empty($arrCat))
			return(null);
		
		return($arrCat);
		
		}catch(Exception $e){
			return(null);
		}
	}
	
	
	/**
	 *
	 * get items for select categories
	 */
	public function getHtmlSelectCats($type){
		
		$arrCats = $this->getListSimple($type);
		
		$html = "";
		foreach($arrCats as $cat):
			$catID = $cat["id"];
			$title = $cat["title"];
			$html .= "<option value=\"{$catID}\">{$title}</option>";
		endforeach;
	
		return($html);
	}
	
	
	private function a____________CATLIST_____________(){}
	
	
	/**
	 * get list of categories
	 */
	public function getHtmlCatList($selectedCatID = null, $objAddonType=null, $arrCats = null){
		
		$this->lastAddonType = $objAddonType;
				
		$catType = $objAddonType->typeNameCategory;
		
		if($arrCats === null)
			$arrCats = $this->getListExtra($objAddonType);
		
		$html = "";
				
		foreach($arrCats as $index => $cat):
			
			$id = UniteFunctionsUC::getVal($cat, "id");
			
			$class = "";
			if($index == 0)
				$class = "first-item";
			
			if(is_numeric($selectedCatID))
				$selectedCatID = (int)$selectedCatID;
			
			if(is_numeric($id))
				$id = (int)$id;
			
			//select item
			if($selectedCatID !== null && $id === $selectedCatID){
				if(!empty($class))
					$class .= " ";
				$class .= "selected-item";			
			}
			
			$html .= $this->getCatHTML($cat, $class);
			
		endforeach;
	
		return($html);
	}
	
	
	/**
	 *
	 * get html of category
	 */
	private function getCatHTML($cat, $class = ""){
		
		$isWebCatalogMode = false;
		if(!empty($this->lastAddonType))
			$isWebCatalogMode = $this->lastAddonType->isWebCatalogMode;
		
		$id = UniteFunctionsUC::getVal($cat, "id");
				
		$isweb = UniteFunctionsUC::getVal($cat, "isweb");
		
		$title = $cat["title"];
		$numAddons = UniteFunctionsUC::getVal($cat, "num_addons", 0);
				
		$showTitle = $title;
	
		if(!empty($numAddons))
			$showTitle .= " ($numAddons)";
		
		$dataTitle = htmlspecialchars($title);
		
		$classIcon = "";
		
		$addHtml = "";
		if($isweb == true){
			$addHtml .= " data-isweb='true'";
			$class .= " uc-isweb";
		}else{
						
			//get cat params			
			$objCat = new UniteCreatorCategory();
			$objCat->initByRecord($cat);
			$classIcon = $objCat->getParam("icon");
		}
		
		if(!empty($class))
			$class = "class=\"{$class}\"";
		
		$html = "";
		$html .= "<li id=\"category_{$id}\" {$class} data-id=\"{$id}\" data-numaddons=\"{$numAddons}\" data-title=\"{$dataTitle}\" {$addHtml}>\n";
		
		//add icon
		if(!empty($classIcon))
			$showTitle = "<i class='uc-cat-icon $classIcon'></i>".$showTitle;
		
		$html .= "	<span class=\"cat_title\">{$showTitle}</span>\n";
		
		//add web icon
		if($isweb == true && $isWebCatalogMode == false){
			
			$urlWebIcon = GlobalsUC::$urlPluginImages."icon_cloud.svg";
			
			$textCloudCat = __("This category not installed yet in your catalog", "unlimited-elements-for-elementor");
				
			$html .= "<img class=\"uc-state-label-icon\" src=\"{$urlWebIcon}\" title=\"$textCloudCat\">\n";
		}
		
		//add pin icon
		if($isweb == false && $isWebCatalogMode == true){
			
			$urlPinIcon = GlobalsUC::$urlPluginImages."icon_pin.svg";
			
			$textCloudCat = __("This category is installed", "unlimited-elements-for-elementor");
			
			$html .= "<img class=\"uc-state-label-icon\" src=\"{$urlPinIcon}\" width=\"14\" height=\"14\" title=\"$textCloudCat\">\n";
		}
		
		$html .= "</li>\n";
	
		return($html);
	}
	
	
	private function a____________GET_SORTED_CATLIST______________(){}
	
	
	/**
	 * get sorted category list
	 * clean this function
	 */
	public function getLayoutsCatsListFromData($data){
		
		$addType = "all_uncat";
		$type = UniteFunctionsUC::getVal($data, "type");
		$orderParam = UniteFunctionsUC::getVal($data, "sort");
		$filterWord = UniteFunctionsUC::getVal($data, "filter_word");
		
		if($orderParam == 'z-a') 
			$orderParam = "DESC";
		else 
			$orderParam = "ASC";
		 
		$arrCats = $this->getListExtraLayouts($filterWord, $orderParam);
		
		$response = array();
		$response["cats_list"] = $arrCats;
		
		return($response);
	}
	
	
	private function a_________SETTERS__________(){}
	
	
	/**
	 * modify category title before create
	 * function for override
	 */
	protected function modifyCatTitleBeforeCreate($title){
		
		return($title);
	}
	
	
	/**
	 * get category id by title. If the title not exists - create it 
	 */
	public function getCreateCatByTitle($title, $type="", $catData = null){
		
		$title = $this->modifyCatTitleBeforeCreate($title);
		
		//if found, return id
		$arrCat = $this->getCatByTitle($title, $type);
		if(!empty($arrCat)){
			$catID = $arrCat["id"];
			return($catID);
		}
		
		try{
			
			$objCategory = new UniteCreatorCategory();			
			$createData = $objCategory->add($title, $type, $catData);
			$catID = $createData["id"];
			
			return($catID);
			
		}catch(Exception $e){
			
			return(0);
		}
		
	}
	
	
	/**
	 * 
	 * remove the category.
	 */
	private function remove($catID){
		
		$catID = (int)$catID;
		
		$arrCat = $this->getCat($catID);
		$type = UniteFunctionsUC::getVal($arrCat, "type");
		
		//remove category
		$this->db->delete(GlobalsUC::$table_categories,"id=".$catID);
				
		//do action by type after remove
		switch($type){
			case self::TYPE_LAYOUT:	
				$this->db->runSql("UPDATE ".GlobalsUC::$table_layouts." SET catid='0' WHERE catid='".$catID."'");
			break;
			default:	//remove addons
				$this->db->delete(GlobalsUC::$table_addons,"catid=".$catID);
			break;
		}
		
	}
	
	
	
	
	/**
	 * update category type
	 */
	public function updateType($catID, $type){
		
		$catID = (int)$catID;
		
		$this->validateCatExist($catID);
		
		$arrUpdate = array();
		$arrUpdate["type"] = $type;
		$this->db->update(GlobalsUC::$table_categories,$arrUpdate,array("id"=>$catID));
	}
	
	
	/**
	 * 
	 * update categories order
	 */
	private function updateOrder($arrCatIDs){
		
		foreach($arrCatIDs as $index=>$catID){
			$order = $index+1;
			$arrUpdate = array("ordering"=>$order);
			$where = array("id"=>$catID);
			$this->db->update(GlobalsUC::$table_categories,$arrUpdate,$where);
		}
	}
	
	
	/**
	 * 
	 * remove category from data
	 */
	public function removeFromData($data){
		
		$catID = UniteFunctionsUC::getVal($data, "catID");
		$type = UniteFunctionsUC::getVal($data, "type");
		
		$this->remove($catID, $type);
				
		$response = array();
		$response["htmlSelectCats"] = $this->getHtmlSelectCats($type);
		
		return($response);
	}
	
	/**
	* get catagory object from data
	*/
	private function getObjCatFromData($data){
		
		$catID = UniteFunctionsUC::getVal($data, "cat_id");
		if(empty($catID))
			$catID = UniteFunctionsUC::getVal($data, "id");
					
		$objCat = new UniteCreatorCategory();
		$objCat->initByID($catID);
		
		return($objCat);
	}
	
	
	/**
	 * 
	 * update category from data
	 */
	public function updateFromData($data){
				
		$objCat = $this->getObjCatFromData($data);
				
		$objCat->updateFromData($data);
				
	}
	
	
	/**
	 * 
	 * update order from data
	 */
	public function updateOrderFromData($data){
		$arrCatIDs = UniteFunctionsUC::getVal($data, "cat_order");
		if(is_array($arrCatIDs) == false)
			UniteFunctionsUC::throwError("Wrong categories array");
			
		$this->updateOrder($arrCatIDs);
	}
	
	
	/**
	 * 
	 * add catgory from data, return cat select html list
	 */
	public function addFromData($data){
				
		$title = UniteFunctionsUC::getVal($data, "catname");
		$type = UniteFunctionsUC::getVal($data, "type");
				
		$objCategory = new UniteCreatorCategory();
		$response = $objCategory->add($title, $type);
		
		$arrCat = array("id"=>$response["id"],"title"=>$response["title"],"num_addons"=>0);
		$html = $this->getCatHTML($arrCat);
		
		$response["message"] = esc_html__("Category Added", "unlimited-elements-for-elementor");
		$response["htmlSelectCats"] = $this->getHtmlSelectCats($type);
		$response["htmlCat"] = $html;
		
		return($response);
		
	}
	
	/**
	 * convert category array to type
	 */
	private function convertArrCatToType($cat, $typeDest){
		
		$title = UniteFunctionsUC::getVal($cat, "title");
		$id = UniteFunctionsUC::getVal($cat, "id");
		
		$isCatExists = $this->isCatExistsByTitle($title, $typeDest, $id);
		
		if($isCatExists == true)
			return(false);
			
		$arrUpdate = array();
		$arrUpdate["type"] = $typeDest;
		
		$this->db->update(GlobalsUC::$table_categories, $arrUpdate, "id=".$id);
		
		return(true);
	}
	
	
	/**
	 * modify all categories from type to type
	 */
	public function convertAllFromTypeToToType($typeSource, $typeDest){
		
		$arrCats = $this->getArrCats($typeSource);
		
		$arrLog = array();
		foreach($arrCats as $cat){
			
			$title = $cat["title"];
			
			$success = $this->convertArrCatToType($cat, $typeDest);
			if($success == true)
				$arrLog[] = "$title converted";
			else 
				$arrLog[] = "$title skipped";
			
		}

		return($arrLog);		
	}
	
}

?>