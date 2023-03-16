<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

	
	class UniteCreatorDB{
		
		const ISNULL = "dbisnull";
		
		private $pdb;
		private $lastRowID;
		public static $arrTableTitles;
		
		
		/**
		 * 
		 * constructor - set database object
		 */
		public function __construct(){
			
			$this->pdb = new UniteProviderDBUC();
			
		}
		
		
		/**
		 * 
		 * throw error
		 */
		private function throwError($message,$code=-1){
			UniteFunctionsUC::throwError($message,$code);
		}
		
		/**
		 * get the original db object
		 */
		public function getPDB(){
		    
		    return($this->pdb->getDBObject());
		}

		/**
		 * validate for errors
		 * @param unknown_type $prefix
		 */
		private function checkForErrors($prefix = ""){
			
			$message = $this->pdb->getErrorMsg();
			
			if(!$message)
				return(false);
			
			if(!empty($prefix))
				$message = $prefix." ".$message;
			
			$errorNum = $this->pdb->getErrorNum();
			
			$this->throwError($message, $errorNum);
		}
		
		
		/**
		 * return if table exists
		 */
		public function isTableExists($table){
		   
		    try{
		   		 
		         $this->fetchSql("select * from $table limit 1", true);
		
		    }catch(Exception $e){
		        
		        return(false);
		    }
		    
		    return(true);
		}

		
		/**
		 * 
		 * insert variables to some table
		 */
		public function insert($tableName,$arrItems){
			
			$strFields = "";
			$strValues = "";
			foreach($arrItems as $field=>$value){
				$value = "'".$this->escape($value)."'";
				if($field == "id") continue;
				if($strFields != "") $strFields .= ",";
				if($strValues != "") $strValues .= ",";
				$strFields .= $field;
				$strValues .= $value;
			}
			
			$insertQuery = "insert into $tableName($strFields) values($strValues)";									
			
			$this->runSql($insertQuery,"insert");
			$this->lastRowID = $this->pdb->insertid();
			
			return($this->lastRowID);
		}
		
		
		/**
		 * 
		 * get last insert id
		 */
		public function getLastInsertID(){
			$this->lastRowID = $this->pdb->insertid();
			return($this->lastRowID);			
		}
		
		
		/**
		 * 
		 * delete rows
		 */
		public function delete($table,$where){
			
			UniteFunctionsUC::validateNotEmpty($table,"table name");
			UniteFunctionsUC::validateNotEmpty($where,"where");
			
			if(is_array($where))
				$where = $this->getWhereString($where);
			
			$query = "delete from $table where $where";
			
			$success = $this->runSql($query, "delete error");
			return($success);
		}
		
		/**
		 * delete multiple items from table by id
		 * Enter description here ...
		 */
		public function deleteMultipleByID($table, $arrItems){
			
			foreach($arrItems as $key=>$itemID)
				$arrItems[$key] = (int)$itemID;
						
			$strItemsIDs = implode(",", $arrItems);
						
			$this->delete($table,"id in($strItemsIDs)");
		}
		
		/**
		 * get category id with "all" option
		 */
		public function getWhereCatIDWithAll($catID){
			
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
				$strCats = $this->escape($strCats);		//for any case
				$arrWhere[] = "catid in($strCats)";
			}
			
			
			return($arrWhere);
		}
		
		
		/**
		 * 
		 * get where string from where array
		 */
		private function getWhereString($where){
						
			$where_format = null;
						
			foreach ( $where as $key=>$value ) {
				
				if($value == self::ISNULL){
					$wheres[] = "($key = '' or $key is null)";
					continue;
				}
				
				if($key == self::ISNULL || is_numeric($key)){
					$wheres[] = $value;
					continue;
				}
				
				// array('sign',values);
				
				$sign = "=";
					
				$isEscape = true;
				
				if(is_array($value)){
					$sign = $value[0];
					$value = $value[1];
				}
				
				if(is_numeric($value) == false){
					$value = $this->escape($value);
					$value = "'$value'";
				}
				
				$wheres[] = "$key $sign {$value}";
			}
			
			$strWhere = implode( ' AND ', $wheres );
								
			return($strWhere);
		}
		
		
		/**
		 * 
		 * insert variables to some table
		 */
		public function update($tableName,$arrData,$where){
			
			UniteFunctionsUC::validateNotEmpty($tableName,"table cannot be empty");
			UniteFunctionsUC::validateNotEmpty($where,"where cannot be empty");
			UniteFunctionsUC::validateNotEmpty($arrData,"data cannot be empty");
			
			if(is_array($where))
				$where = $this->getWhereString($where);
			
			$strFields = "";
			foreach($arrData as $field=>$value){
				$value = "'".$this->escape($value)."'";
				if($strFields != "") $strFields .= ",";
				$strFields .= "$field=$value";
			}
									
			$updateQuery = "update $tableName set $strFields where $where";
						
			$numRows = $this->runSql($updateQuery, "update error");
			
			return($numRows);
		}
		
		
			/**
		 * 
		 * run some sql query
		 */
		public function runSql($query){
						
			$response = $this->pdb->query($query);
															
			$this->checkForErrors("Regular query error");
						
			return($response);
		}
				
		
		/**
		 * 
		 * fetch rows from sql query
		 */
		public function fetchSql($query, $supressErrors = false){
			
			$rows = $this->pdb->fetchSql($query, $supressErrors);
			
			$this->checkForErrors("fetch");
			
			$rows = UniteFunctionsUC::convertStdClassToArray($rows);
			
			return($rows);
		}
		
		
		/**
		 * 
		 * get row wp emulator
		 */
		public function get_row($query = null){
			
			$rows = $this->pdb->fetchSql($query);
						
			$this->checkForErrors("get_row");
			
			if(count($rows) == 1)
				$result = $rows[0];
			else
				$result = $rows;
			
			return($result);
		}
		
		
		/**
		 * get "where" query part
		 */
		private function getQueryPart_where($where = ""){
			
			if($where){
			
				if(is_array($where))
					$where = $this->getWhereString($where);
				
				$where = " where $where";
			}
			
			return($where);
		}
		
		
		/**
		 * create fetch query
		 */
		private function createFetchQuery($tableName, $fields=null, $where="", $orderField="", $groupByField="", $sqlAddon=""){
			
			if(empty($fields)){
				$fields = "*";
			}else{
				if(is_array($fields))
					$fields = implode(",", $fields);
			}
			
			$query = "select $fields from $tableName";
			
			$where = $this->getQueryPart_where($where);
			
			if(!empty($where))
				$query .= $where;
			
			if($orderField){
				$orderField = $this->escape($orderField);
				$query .= " order by $orderField";
			}
			
			if($groupByField){
				$groupByField = $this->escape($groupByField);
				$query .= " group by $groupByField";
			}
			
			if($sqlAddon)
				$query .= " ".$sqlAddon;
			
			return($query);
		}
		
		
		/**
		 * 
		 * get data array from the database
		 * 
		 */
		public function fetch($tableName, $where="", $orderField="", $groupByField="", $sqlAddon=""){
			
			$query = $this->createFetchQuery($tableName, null, $where, $orderField, $groupByField, $sqlAddon);
						
			$rows = $this->fetchSql($query);
			
			return($rows);
		}
		
		
		/**
		 * get total rows
		 */
		public function getTotalRows($tableName, $where=""){
			
			$where = $this->getQueryPart_where($where);
						
			$query = "select count(*) as numrows from $tableName".$where;
			
			$response = $this->fetchSql($query);
			
			$totalRows = $response[0]["numrows"];
			
			return($totalRows);			
		}
		
		/**
		 * fetch records by id's
		 */
		public function fetchByIDs($table, $arrIDs){
			
			if(is_string($arrIDs))
				$strIDs = $arrIDs;
			else
				$strIDs = implode(",", $arrIDs);
			
			$sql = "select * from {$table} where id in({$strIDs})";
			$arrRecords = $this->fetchSql($sql);

			return($arrRecords);
		}
		
		/**
		 * update objects ordering
		 * using (ordering, id fields, and ID's array)
		 */
		public function updateRecordsOrdering($table, $arrIDs){
			
			$arrRecords = $this->fetchByIDs($table, $arrIDs);
			
			//get items assoc
			$arrRecords = UniteFunctionsUC::arrayToAssoc($arrRecords,"id");
			
			$order = 0;
			foreach($arrIDs as $recordID){
				$order++;
				
				$arrRecord = UniteFunctionsUC::getVal($arrRecords, $recordID);
				if(!empty($arrRecord) && $arrRecord["ordering"] == $order)
					continue;
								
				$arrUpdate = array();
				$arrUpdate["ordering"] = $order;
				$this->update($table, $arrUpdate, array("id"=>$recordID));
			}
			
		}
		
		
		/**
		 *
		 * get data array from the database
		 * pagingOptions - page, inpage
		 */
		public function fetchPage($tableName, $pagingOptions, $where="", $orderField="", $groupByField="", $sqlAddon=""){
			
			$page = UniteFunctionsUC::getVal($pagingOptions, "page");
			$rowsInPage = UniteFunctionsUC::getVal($pagingOptions, "inpage");
			
			
			//valdiate and sanitize
			UniteFunctionsUC::validateNumeric($page);
			UniteFunctionsUC::validateNumeric($rowsInPage);
			UniteFunctionsUC::validateNotEmpty($rowsInPage);
			if($page < 1)
				$page = 1;
			
			
			//get total
			$totalRows = $this->getTotalRows($tableName, $where);
			$numPages = $pages = ceil($totalRows / $rowsInPage);
			
			//build query
			$offset = ($page - 1)  * $rowsInPage;
						
			$query = $this->createFetchQuery($tableName, null, $where, $orderField, $groupByField, $sqlAddon);
			
			$query .= " limit $rowsInPage offset $offset";
			
			$rows = $this->fetchSql($query);
		
			//output response
			$response = array();
			$response["total"] = $totalRows;
			$response["page"] = $page;
			$response["num_pages"] = $numPages;
			$response["inpage"] = $rowsInPage;
			
			$response["rows"] = $rows;
			
			return($response);
		}
		
		
		/**
		 * fields could be array or string comma saparated
		 */
		public function fetchFields($tableName, $fields, $where="", $orderField="", $groupByField="", $sqlAddon=""){
			
			$query = $this->createFetchQuery($tableName, $fields, $where, $orderField, $groupByField, $sqlAddon);
			
			$rows = $this->fetchSql($query);
			
			return($rows);
		}
		
		
		/**
		 * 
		 * fetch only one item. if not found - throw error
		 */
		public function fetchSingle($tableName,$where="",$orderField="",$groupByField="",$sqlAddon=""){
			
			$errorEmpty = "";
			
			if(is_array($tableName)){
				$arguments = $tableName;
				
				$tableName = UniteFunctionsUC::getVal($arguments, "tableName");
				$where = UniteFunctionsUC::getVal($arguments, "where");
				$orderField = UniteFunctionsUC::getVal($arguments, "orderField");
				$groupByField = UniteFunctionsUC::getVal($arguments, "groupByField");
				$sqlAddon = UniteFunctionsUC::getVal($arguments, "sqlAddon");
				$errorEmpty = UniteFunctionsUC::getVal($arguments, "errorEmpty");
			}
			
			if(empty($errorEmpty)){
				$tableTitle = UniteFunctionsUC::getVal(self::$arrTableTitles, $tableName, __("Record", "unlimited-elements-for-elementor"));
				
				$errorEmpty = $tableTitle." ".__("not found", "unlimited-elements-for-elementor");
			}
			
			$response = $this->fetch($tableName, $where, $orderField, $groupByField, $sqlAddon);
			
			if(empty($response)){
				$this->throwError($errorEmpty);
			}
			
			$record = $response[0];
			return($record);
		}
		
		
		/**
		 *
		 * get max order from categories list
		 */
		public function getMaxOrder($table, $field = "ordering"){
			
			$query = "select MAX($field) as maxorder from {$table}";
			
			$rows = $this->fetchSql($query);
		
			$maxOrder = 0;
			if(count($rows)>0)
				$maxOrder = $rows[0]["maxorder"];
		
			if(!is_numeric($maxOrder))
				$maxOrder = 0;
		
			return($maxOrder);
		}
		
		
		/**
		 * update layout in db
		 */
		public function createObjectInDB($table, $arrInsert){
			
			$maxOrder = $this->getMaxOrder($table);
			
			$arrInsert["ordering"] = $maxOrder+1;
			
			$id = $this->insert($table, $arrInsert);
			
			return($id);
		}
		
		
		/**
		 * 
		 * escape data to avoid sql errors and injections.
		 */
		public function escape($string){
			$newString = $this->pdb->escape($string);
			return($newString);
		}
		
		
		/**
		 * get sql addon type for quest string
		 */
		public function getSqlAddonType($addonType, $dbfield = "addontype"){
			
			if(is_array($addonType)){
				
				$arrSql = array();
				foreach($addonType as $singleType)
					$arrSql[] = $this->getSqlAddonType($singleType, $dbfield);
				
				$sql = "(".implode(" or ", $arrSql).")";
				return($sql);
			}
			
			if($addonType == GlobalsUC::ADDON_TYPE_REGULAR_ADDON || $addonType == GlobalsUC::ADDON_TYPE_LAYOUT_GENERAL)
				$addonType = null;
			
			$addonType = $this->escape($addonType);
			if(empty($addonType)){
				$where = "({$dbfield}='' or {$dbfield} is null)";
			}else
				$where = "{$dbfield}='{$addonType}'";
							
			
			return($where);
		}
		
		
	}
	
?>