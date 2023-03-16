<?php

defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

class UniteProviderDBUC{
	
	private $wpdb;
	
	/**
	 *
	 * constructor - set database object
	 */
	public function __construct(){
		global $wpdb;
		$this->wpdb = $wpdb;
	}
	
	/**
	 * get error number
	 */
	public function getErrorNum(){
		return -1;
	}
	
	
	/**
	 * get error message
	 */
	public function getErrorMsg(){
		
		/*
		if(!empty($this->wpdb->last_error)){
			UniteFunctionsUC::showTrace();exit();
		}
		*/
		
		return $this->wpdb->last_error;
	}
	
	/**
	 * get last row insert id
	 */
	public function insertid(){
		return $this->wpdb->insert_id;
	}
	
	
	/**
	 * do sql query, return success
	 */
	public function query($query){
		
		$this->wpdb->suppress_errors(false);
		
		$success = $this->wpdb->query($query);
		return($success);
	}
	
	
	/**
	 * get affected rows after operation
	 */
	public function getAffectedRows(){
		return $this->wpdb->num_rows;
	}
	
	/**
	 * fetch objects from some sql
	 */
	public function fetchSql($query, $supressErrors = false){
		
		$this->wpdb->suppress_errors($supressErrors);
		
		$rows = $this->wpdb->get_results($query, ARRAY_A);
		
		return($rows);
	}
	
	/**
	 * escape some string
	 */
	public function escape($string){
		return $this->wpdb->_escape($string);
	}
	
}



?>