<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');


class UniteCreatorDataset{

	public static $arrDatasetTypes = array();
	public static $arrTypeNames = array();
	
	
	/**
	 * get short queries array
	 */
	private function getArrQueryShort($arrQueries){
		
		$arrShort = array();
		
		foreach($arrQueries as $name => $arrQuery){
			
			$title = UniteFunctionsUC::getVal($arrQuery, "title");
			$arrShort[$name] = $title;
		}
		
		return($arrShort);
	}
	
	
	/**
	 * register dataset
	 */
	public function registerDataset($type, $title, $arrQueries){
		
		if(isset(self::$arrDatasetTypes[$type]))
			UniteFunctionsUC::throwError("Dataset type is already exists");
		
		$arrDataset = array();
		$arrDataset["title"] = $title;
		$arrDataset["queries"] = $arrQueries;
		
		
		self::$arrDatasetTypes[$type] = $arrDataset;
		self::$arrTypeNames[$type] = $title;
	}
	
	
	/**
	 * get dataset types
	 */
	public function getDatasetTypes(){
		
		return(self::$arrDatasetTypes);
	}

	
	/**
	 * get dataset types
	 */
	public function getDatasetTypeNames(){
		
		return(self::$arrTypeNames);
	}
	
	
	/**
	 * get dataset
	 */
	public function getDataset($type){
		
		if(!isset(self::$arrDatasetTypes[$type]))
			UniteFunctionsUC::throwError("The dataset type: $type not exists");
		
		$arrDataset = self::$arrDatasetTypes[$type];
		
		
		return($arrDataset);
	}
	
	
	
}