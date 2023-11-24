<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

class UniteCreatorCategory extends UniteElementsBaseUC{

	protected $id;
	protected $title,$params,$alias,$ordering;
	protected $type = null;
	private static $cacheRecords;
	
	const TYPE_LAYOUT = "layout";
	
	
	/**
	 * validate that category is inited
	 */
	protected function validateInited(){
		
		if(empty($this->id))
			UniteFunctionsUC::throwError("The category is not inited");
		
	}
	
	
	/**
	 * init by id
	 */
	public function initByID($catID, $useCache = false){
		
		$catID = (int)$catID;
		
		$record = null;
		
		//get from cache
		if($useCache == true){
			$cacheName = "cat_".$catID;
			if(isset(self::$cacheRecords[$cacheName]))
				$record = self::$cacheRecords[$cacheName];
		}
		
		//get from DB
		if(empty($record))
			$record = $this->db->fetchSingle(GlobalsUC::$table_categories,"id=$catID");
		
		if(empty($record))
			UniteFunctionsUC::throwError("Category with id: $catID not found");
		
		$this->initByRecord($record);		
	}
	
	
	/**
	 * init category by record
	 */
	public function initByRecord($record){
				
		$this->id = UniteFunctionsUC::getVal($record, "id");
		
		$cacheName = "cat_".$this->id;
		self::$cacheRecords[$cacheName] = $record;
		
		$this->title = UniteFunctionsUC::getVal($record, "title");
		$this->alias = UniteFunctionsUC::getVal($record, "alias");
		$this->ordering = UniteFunctionsUC::getVal($record, "ordering");
		$this->type = UniteFunctionsUC::getVal($record, "type");
		
		$params = UniteFunctionsUC::getVal($record, "params");
		$params = UniteFunctionsUC::jsonDecode($params);
		
		if(empty($params))
			$params = array();
		
		$this->params = $params;
	}
	
	private function a_VALIDATE(){}
	
	
	/**
	 * validate that category type exists, "" is not null
	 */
	private function validateTypeExists(){
				
		if($this->type === null)
			UniteFunctionsUC::throwError("The type should be set");
		
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
	private function validateTitleByType($title){
		
		$this->validateTitle($title);
		
		//validate that not exists for all the types
		$this->validateCatTitleNotExists($title);
		
	}
	
	/**
	 * validate alias 
	 */
	private function validateAliasForUpdate($newAlias){
		
		UniteFunctionsUC::validateNotEmpty($newAlias);
		UniteFunctionsUC::validateAlphaNumeric($newAlias);
		if(is_string($newAlias) == false)
			UniteFunctionsUC::throwError("The alias must be string");
		
		if(is_numeric($newAlias) == true)
			UniteFunctionsUC::throwError("The alias must be a words, not only numbers");
	}
	
	
	/**
	 * validate if category not exists
	 */
	private function validateCatTitleNotExists($title){
		
		$this->validateTypeExists();
		
		$isExists = $this->isCatExistsByTitle($title, $this->type);
		
		if($isExists == true)
			UniteFunctionsUC::throwError("Category with title: $title already exists");
		
	}
	
	
	private function a_____GETTERS______(){}
	
	
	/**
	 * check if category exists by title
	 * check in all cats except the current category id
	 */
	private function isCatExistsByTitle($title, $type = ""){
		
		if(empty($type))
			$type = UniteCreatorDB::ISNULL;
		
		$arrWhere = array();
		$arrWhere["title"] = $title;
		$arrWhere["type"] = $type;
		
		
		$isExists = $this->returnIfObjectExists($arrWhere);
		return($isExists);
	}
	
	/**
	 * return if some object exists by record, and id if available
	 */
	private function returnIfObjectExists($arrWhere){
		
		$response = $this->db->fetch(GlobalsUC::$table_categories, $arrWhere);
		
		if(empty($response))
			return(false);
		
		//check by catID
		if(empty($this->id))
			return(true);
		
		$cat = $response[0];
		if($cat["id"] == $this->id)
			return(false);
		else
			return(true);		
	}
	
	
	/**
	 * check if alias exists in db
	 */
	private function isAliasExists($alias){
		
		$this->validateTypeExists();
		
		$type = $this->type;
		if(empty($type))
			$type = UniteCreatorDB::ISNULL;
		
		$arrWhere = array();
		$arrWhere["alias"] = $alias;
		$arrWhere["type"] = $this->type;
				
		$isExists = $this->returnIfObjectExists($arrWhere);
		return($isExists);
	}
	
	
	/**
	 * get category alias, if not exists, create it
	 */
	public function getAlias(){
		
		$this->validateInited();
		
		//create and update alias in DB
		if(empty($this->alias)){
			$this->alias = $this->createAliasFromTitle($this->title);
			$this->updateValueInDB("alias", $this->alias);
		}
		
		return($this->alias);
	}
	
	
	/**
	 * get title
	 */
	public function getTitle(){
		$this->validateInited();
		
		return($this->title);
	}
	
	
	/**
	 * get category ID
	 */
	public function getID(){
		$this->validateInited();
		
		return($this->id);
	}
	
	/**
	 * get params
	 */
	public function getParams(){
		$this->validateInited();
		
		return($this->params);
	}
	
	
	/**
	 * get param
	 */
	public function getParam($key){
		
		$value = UniteFunctionsUC::getVal($this->params, $key);
		
		return($value);
	}
	
	
	/**
	 * get export category data for saving
	 */
	public function getExportCatData($exportType = null){
		
		$this->validateInited();
		
		if($exportType === null)
			$exportType = $this->type;
		
		$arrCat = array();
		
		$exportTitle = UniteProviderFunctionsUC::applyFilters(UniteCreatorFilters::FILTER_EXPORT_CAT_TITLE, $this->title, $exportType, $this->type);
		
		$arrCat["title"] = $exportTitle;
		$arrCat["alias"] = $this->alias;
			
		$params = array();
		$params["icon"] = $this->getParam("icon");
		
		$arrCat["params"] = $params;
		$arrCat["type"] = $exportType;
		
		return($arrCat);
	}
	
	
	
	private function a_____SETTERS____(){}
	
	
	/**
	 * set category type. can be even before init
	 */
	public function setType($type){
		$this->type = $type;
	}
	
	
	/**
	 * get unique alias
	 */
	protected function getUniqueAlias($alias){
		
		$ending = "";
		$counter = 0;
		do{
			$aliasUnique = $alias.$ending;
			
			$isExists = $this->isAliasExists($aliasUnique);
			$counter++;
			$ending = "_".$counter;
			
		}while($isExists == true);
		
		return($aliasUnique);
	}
	
	
	/**
	 * create alias from title
	 */
	private function createAliasFromTitle($title, $existingAlias = null){
		
		$this->validateTypeExists();
		
		if(is_array($existingAlias))
			UniteFunctionsUC::throwError("The alias can't be array");
		
		$alias = $existingAlias;
		
		if(empty($alias))
			$alias = HelperUC::convertTitleToHandle($title);
		
		if(is_numeric($alias))
			$alias = "cat_".$alias;
		
		$alias = $this->getUniqueAlias($alias);
		
		return($alias);
	}
	
	
	/**
	 * update some table value in db
	 */
	private function updateValueInDB($key, $value){
		
		$arrUpdate = array();
		$arrUpdate[$key] = $value;
		
		$this->db->update(GlobalsUC::$table_categories, $arrUpdate, "id=".$this->id);
	}
	
	
	/**
	 * update category
	 */
	private function update($title, $alias=null, $params=null){
		
		$title = trim($title);
		
		$this->validateInited();
		
		//check validate exists		
		$this->validateTitleByType($title);
		
		if(!empty($alias)){
			$alias = $this->getUniqueAlias($alias);
			$this->validateAliasForUpdate($alias);
		}
		
		$arrUpdate = array();
		$arrUpdate["title"] = $title;
		
		if(!empty($alias))
			$arrUpdate["alias"] = $alias;
		else{
			if(empty($this->alias))
				$arrUpdate["alias"] = $this->createAliasFromTitle($title);
		}
		
		
		if($params !== null)
			$arrUpdate["params"] = json_encode($params);
		
		$this->db->update(GlobalsUC::$table_categories, $arrUpdate, array("id"=>$this->id));
	}
	
	
	/**
	 * add default category by addon type
	 */
	public function addDefaultByAddonType(UniteCreatorAddonType $objType){
				
		$title = $objType->defaultCatTitle;
		
		if(empty($title))
			UniteFunctionsUC::throwError("There should be default cat title");
		
		$type = $objType->typeNameCategory;
		
		$newID = $this->add($title, $type);
		
		return($newID);
	}
	
	
	/**
	 * add category
	 */
	public function add($title, $type, $catData = null){
		
		$title = trim($title);
		
		$this->type = $type;
		
		$this->validateTitleByType($title);
		
		$objAddonType = UniteCreatorAddonType::getAddonTypeObject($type);
		
		$maxOrder = $this->db->getMaxOrder(GlobalsUC::$table_categories);
		
		$existingAlias = UniteFunctionsUC::getVal($catData, "alias");
		
		//prepare insert array
		$arrInsert = array();
		$arrInsert["title"] = $title;
		$arrInsert["alias"] = $this->createAliasFromTitle($title, $existingAlias);
		
		$arrInsert["type"] = $objAddonType->typeNameCategory;
		
		$arrInsert["ordering"] = $maxOrder+1;
		
		//set params
		$arrParams = UniteFunctionsUC::getVal($catData, "params");
		if(!empty($arrParams)){
			$jsonParams = json_encode($arrParams);
			$arrInsert["params"] = $jsonParams;
		}
		
		
		//insert the category
		$catID = $this->db->insert(GlobalsUC::$table_categories, $arrInsert);
				
		//prepare output
		$returnData = array("id"=>$catID,"title"=>$title);
		return($returnData);
	}
	
	
	/**
	* update category from data
	*/
	public function updateFromData($data){
				
		$this->validateInited();
		
		if(isset($data["cat_data"]))		//don't delete this
			$data = $data["cat_data"];
		
		$title = UniteFunctionsUC::getVal($data, "title");
		if(empty($title))
			$title = UniteFunctionsUC::getVal($data, "category_title");
		
		$alias = UniteFunctionsUC::getVal($data, "category_alias");
		if(empty($alias))
			$alias = null;
		
		$params = $data;
		
		unset($params["category_title"]);
		unset($params["category_alias"]);
		
		if(empty($params))
			$params = null;
		
		$this->update($title, $alias, $params);
	}
	
	
}