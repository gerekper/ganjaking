<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

class UniteCreatorVariablesOutput{
	
	const TYPE_ITEM_SIMPLE = "uc_varitem_simple";
	const TYPE_PARAM_RELATED = "uc_var_paramrelated";
	const TYPE_PARAM_ITEM_RELATED = "uc_var_paramitemrelated";
	
	
	private $isInited = false, $arrParams;
	private $var, $item, $index, $numItem, $numItems, $isFirstItem, $isLastItem;
	
	
	/**
	 * validate that the object is inited
	 */
	private function validateInited(){
		if($this->isInited == false)
			UniteFunctionsUC::throwError("UniteCreatorVariablesOutput not inited");
	}
	
	
	/**
	 * get variable field
	 */
	private function getVarValue($name){
		$value = UniteFunctionsUC::getVal($this->var, $name);
		return($value);
	}
	
	
	/**
	 * get simple item var content
	 */
	private function getContent_varItemSimple(){
		
		$content = $this->getVarValue("default_value");
		
		//in case of first item
		if($this->isFirstItem){
			
			$enableFirstItem = $this->getVarValue("enable_first_item");
			$enableFirstItem = UniteFunctionsUC::strToBool($enableFirstItem);
			if($enableFirstItem == true)
				$content = $this->getVarValue("first_item_value");
			
		}		
		else if($this->isLastItem){		//in case of last item
			
			$enableLastItem = $this->getVarValue("enable_last_item");
			$enableLastItem = UniteFunctionsUC::strToBool($enableLastItem);
			if($enableLastItem == true)
				$content = $this->getVarValue("last_item_value");
			
		}
				
		return($content);
	}
	
	
	/**
	 * get param related content, from item params or main params
	 */
	public function getContent_paramRelated($isItem = false){
		
		$params = $this->arrParams;
		if($isItem == true)
			$params = $this->item;
		
		$paramName = $this->getVarValue("param_name");
		$paramValue = UniteFunctionsUC::getVal($params, $paramName);
		
		$options = $this->getVarValue("options");
		
		if(!empty($options) && getType($options) == "array"){
			if(array_key_exists($paramValue, $options))
				$paramValue = $options[$paramValue];
		}
				
		return($paramValue);
	}
	
	
	/**
	 * get item variable data
	 */
	public function getItemVarContent($var, $item, $index, $numItems){
		
		$this->validateInited();
		
		$this->var = $var;
		$this->numItems = $numItems;
		$this->index = $index;
		$this->numItem = $index+1;
		$this->isFirstItem = ($index == 0);
		$this->isLastItem = ($index == ($numItems-1) );
		$this->item = $item;
		
		$type = UniteFunctionsUC::getVal($var, "type");
		
		switch($type){
			case self::TYPE_ITEM_SIMPLE:
				$content = $this->getContent_varItemSimple();
			break;
			case self::TYPE_PARAM_RELATED:
				$content = $this->getContent_paramRelated();
			break;
			case self::TYPE_PARAM_ITEM_RELATED:
				$content = $this->getContent_paramRelated(true);
			break;
			default:
				UniteFunctionsUC::throwError("Wrong item variable type: <b>{$type}</b>");
			break;
		}
		
		
		//convert numitem
		if(!empty($content)){
			$content = str_replace("%numitem%", $this->numItem, $content);
		}
		
		return($content);
	}
	
	/**
	 * get GET param
	 */
	public function getContent_GETParam(){
		
		$name = UniteFunctionsUC::getVal($this->var, "name");
		$defaultValue = UniteFunctionsUC::getVal($this->var, "default_value");
		$sanitizeType = UniteFunctionsUC::getVal($this->var, "sanitize_type");
		
		$content = UniteFunctionsUC::getGetVar($name, $defaultValue, $sanitizeType);
		
		return($content);
	}
	
	/**
	 * get content by filter
	 */
	private function getContent_filter(){
		
		$filterName = UniteFunctionsUC::getVal($this->var, "filter_name");
		
		$filterParam = UniteFunctionsUC::getVal($this->var, "filter_param");
		
		if(empty($filterName))
			return("");

		if(empty($filterParam))
			$content = apply_filters($filterName, "");
		else
			$content = apply_filters($filterName, "", $filterParam);
		
		return($content);
	}
	
	
	/**
	 * get main var content
	 */
	public function getMainVarContent($var){
		
		$this->var = $var;
		
		$type = UniteFunctionsUC::getVal($var, "type");
		
		switch($type){
			case self::TYPE_PARAM_RELATED:
				$content = $this->getContent_paramRelated();
			break;
			case UniteCreatorDialogParam::PARAM_VAR_GET:
				$content = $this->getContent_GETParam();
			break;
			case UniteCreatorDialogParam::PARAM_VAR_FILTER:
				$content = $this->getContent_filter();
			break;
			default:
				UniteFunctionsUC::throwError("Wrong main variable type: <b>{$type}</b>");
			break;
		}
		
		return($content);
	}
	
	
	/**
	 * init the object, get item params
	 */
	public function init($arrParams){
		$this->arrParams = $arrParams;
		$this->isInited = true;
	}
	
}

