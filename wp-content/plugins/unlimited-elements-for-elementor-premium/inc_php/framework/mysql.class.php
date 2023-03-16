<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

	class UniteMySql{
	
		// sqlite class constants:
		const TYPE_KEY = "INTEGER PRIMARY KEY NOT NULL AUTO_INCREMENT";
		const TYPE_STRING = "VARCHAR(120)";
		const TYPE_NUMBER = "INTEGER";
		const TYPE_TEXT = "TEXT";
		const TYPE_BOOLEAN = "BOOLEAN";
		
		//error codes:
		const CODE_ZERO = 0;
		const CODE_TABLE_NOT_EXISTS = 1;
		
		private $mysql_host = "";
		private $mysql_user = ""; 
		private $mysql_pass = "";
		private $mysql_database = "";
		
		public static $handle = null;
		public $lastRowID = -1;
		public $lastArr = array();
		private $charset = null;
				
		
		function __construct(){}		
		
		
		/**
		 * connect to database
		 */
		public function connect($host,$user,$pass,$database){ 
			
			if(!function_exists("mysqli_connect")) 
				throw new Exception("php mysql extension doesn't activated, please activate it in php.ini");
			
			$this->mysql_host = $host;
			$this->mysql_user = $user;
			$this->mysql_pass = $pass;
			$this->mysql_database = $database;
			
			$this->getCreateDatabase();
		}
		
		/**
		 * set charset before connect
		 */
		public function setCharset($charset){
			
			$this->charset = $charset;
			
		}
		
		/**
		 * perform multi queries
		 */
		public function multiQueries($query){
			
			$result = mysqli_multi_query(self::$handle, $query);
			
			$this->checkForErrors("Multi Query");			
			
			return $result;
		}
		
		
		/**
		 * 
		 * throw new error
		 * 
		 */
		private function throwError($message, $code=null){

        if($code == null)
            throw new Exception($message);
        else
            throw new Exception($message,$code);
    }


    //------------------------------------------------------------
    public function confirmOutput($message=""){
        return(array("success"=>true,"message"=>$message));
    }

    //------------------------------------------------------------
    // validates if handle exists. if not - exit with error message
    public function validateHandle($functionName = ""){
        if(self::$handle == false){
            if($functionName) $this->exitWithMessage("$functionName error - no open database");
            else $this->throwError("sqlite error - no open database");
        }
        return($this->confirmOutput());
    }

    //------------------------------------------------------------
    // validate table name field if empty or not exists - write error and exit.
    public function validateTableName($tableName,$functionName=""){
        if(trim($tableName) == ""){
            if($functionName) $this->throwError("$functionName error - no table found");
            else $this->throwError("sqlite error - no table found");
        }
    }

    //------------------------------------------------------------
    // validate if table is created. if not - write error message and exist
    public function validateTable($tableName,$functionName){
        $this->validateTableName($tableName,$functionName);
        if($this->isTableExists($tableName) == false){
            if($functionName) return($this->throwError("$functionName error - the table $tableName doesn't exists",self::CODE_TABLE_NOT_EXISTS));
            else $this->throwError("sqlite error - the table $tableName doesn't exists",self::CODE_TABLE_NOT_EXISTS);
        }
    }

    //------------------------------------------------------------
    // valiadte fields array. if empty or the type is not array - write error message and exit.
    public function validateFields($arrFields,$functionName=""){
        if(gettype($arrFields)!="array") $this->throwError("createTable error - the fields array isn't array type.");
        if(count($arrFields) == 0) $this->throwError("createTable error - the fields don't given.");
    }

    //------------------------------------------------------------
    // set database file (without path)
    public function setDbFile($dbFilepath){
        $this->dbFilepath = $dbFilepath;
        $response = $this->getCreateDatabase();
        return($response);
    }

    //------------------------------------------------------------

    public function setAbsolutePath($path){
        $this->databaseAbsolutePath = $path;
        $this->dbFilepath = "";
    }

    //------------------------------------------------------------

    public function getLastRowID(){
        return($this->lastRowID);
    }

    //------------------------------------------------------------
    // return if table exists or not.
    public function isTableExists($tableName){
        $sql = 'select * from '.$tableName;

        $numRows = mysqli_num_rows(mysqli_query(self::$handle,"SHOW TABLES LIKE '".$tableName."'"));
        $this->checkForErrors("Is table exists error",$sql);

        return($numRows != 0);
    }

    //------------------------------------------------------------
    // create database. if already created - get database.
    public function getCreateDatabase(){

        self::$handle = @mysqli_connect($this->mysql_host, $this->mysql_user, $this->mysql_pass);
		
        if(!self::$handle){
        	
        	$errorNumber = mysqli_connect_errno();
        	
        	if(!empty($errorNumber)){
        		$error = mysqli_connect_error();
            	$this->throwError($error);
        	}    
        }

        mysqli_select_db(self::$handle,$this->mysql_database);
        $this->checkForErrors("Mysql connect to database error");

			if(!empty($this->charset))
				mysqli_set_charset(self::$handle, $this->charset);
		
        return($this->confirmOutput());
    }

		
		//------------------------------------------------------------
		// validate for errors
		private function checkForErrors($prefix = "",$query=""){
			
			if(mysqli_error(self::$handle) == false)
				return(false);
			
			$message = mysqli_error(self::$handle);
			if($prefix) $message = $prefix.' - '.$message;
			if($query) $message .=  ' query: ' . $query;
			$this->throwError($message);
	
		}
		
		/**
		 * get error message
		 */
		public function getErrorMsg(){
			
			return mysqli_error(self::$handle);
		}
		
		
		/**
		 * get last operation error code
		 */
		public function getErrorNumber(){
			
			return mysql_errno(self::$handle);
		}
		
		
		/**
		 * execute query with some error text
		 */
		public function query($query,$errorText=""){
			
			mysqli_query(self::$handle,$query);		
				
			$this->checkForErrors($errorText,$query);
			
			return(mysqli_affected_rows(self::$handle));
		}

		
		/**
		 * get query array
		 */
		public function getQueryArr($query, $errorText){
						
			$rs = mysqli_query(self::$handle, $query);						
			
			$this->checkForErrors($errorText, $query);
			
			$arrRows = array();
			
			while($row = mysqli_fetch_assoc($rs)) 
				$arrRows[] = $row;
			
			$this->lastArr = $arrRows;
			
			return($arrRows);  
		}
		
		
		/**
		 * get affected rows
		 */
		public function getAffectedRows(){
			
			return(mysqli_affected_rows(self::$handle));
		}
    
    /**
     * 
     * returrn last insert id.
     */
    public function insertid(){
    	
        $this->lastRowID = mysqli_insert_id(self::$handle);

        return($this->lastRowID);
    }
    

    //------------------------------------------------------------
    //return escape database parameter string.
    public function escape($str){

        if(function_exists("mysqli_real_escape_string"))
            return mysqli_real_escape_string(self::$handle, $str);

        return $str;
    }



}// Database class end.

?>