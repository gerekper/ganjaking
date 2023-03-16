<?php
/**
* @package Unlimited Elements
* @author unlimited-elements.com
* @copyright (C) 2021 Unlimited Elements, All Rights Reserved. 
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

class UniteCreatorTemplateEnginePro extends UniteCreatorTemplateEngine{

	/**
	 * do some wp
	 */
	public function do_action($tag, $param = null, $param2 = null, $param3=null){
		
		//add debug
		if($param === null)
			HelperUC::addDebug("running action: $tag");
		else
			HelperUC::addDebug("running action: $tag",array(
			"param"=>$param,
			"param2"=>$param2,
			"param3"=>$param3,
		));
		
		//run action, without or with params
		
		if($param === null){
			do_action($tag);
			return(false);
		}
		
		//$param exists
		
		if($param2 === null){
			do_action($tag, $param);
			return(false);
		}
		
		if($param3 === null){
			do_action($tag, $param, $param2);
			return(false);
		}
		
		do_action($tag, $param, $param2, $param3);
		
	}

	
	/**
	 * do some wp
	 */
	public function apply_filters($tag, $value = null, $param1 = null, $param2=null){
		
		
		//add debug
		if($value === null){
			HelperUC::addDebug("applying filter: $tag");
						
		}else
			HelperUC::addDebug("applying filter: $tag",array(
			"value"=>$value,
			"param1"=>$param1,
			"param2"=>$param2,
		));
		
		
		//run action, without or with params
		if($param1 === null){			
			$value = apply_filters($tag, $value);
						
			return($value);
		}
		
		//$param exists
		
		if($param2 === null){
		    $value = apply_filters($tag, $value, $param1);
			return($value);
		}
		
	    $value = apply_filters($tag, $value, $param1, $param2);
		return($value);
	}
	
	
	/**
	  * validate the GET php function
	 */
	private function validateGETPHPFunction($funcName){
				
		if(empty($funcName))
			UniteFunctionsUC::throwError("With getByPHPFunction you can get data from every php function starting with 'get_' example getByPHPFunction(\"get_post\",15)");
		
		if(is_string($funcName) == false)
			UniteFunctionsUC::throwError("function name in wrong format");
		
		if(function_exists($funcName) == false)
			UniteFunctionsUC::throwError("getByPHPFunction: Function '{$funcName}' not exists");
			
		$arrAllowed = array("wc_price");
		if(array_search($funcName, $arrAllowed) !== false)
			return(true);
		
		if(strpos($funcName, "get_") !== 0 && strpos($funcName, "_get_") === false)
			UniteFunctionsUC::throwError("getByPHPFunction: Function '{$funcName}' is not containing with 'get_', can't run it, sorry");
				
	}
	
	
	/**
	 * get data by filters
	 */
	public function getByPHPFunction($funcName = null){
		
		$this->validateGETPHPFunction($funcName);
		
		$args = func_get_args();
		
		//remove first argument
		array_shift($args);
		
		//call the function
		$output = call_user_func_array($funcName, $args);
		
		//output, convert object to array on the way
		if(is_array($output))
			return($output);
		
		if(is_object($output)){
			$output = (array)$output;
			$output = UniteFunctionsUC::convertStdClassToArray($output);
		}
		
		return($output);		
	}
	
	
	/**
	 * function for override
	 */
	protected function initTwig_addExtraFunctionsPro(){
		
		//run action for adding custom twig functions and filters
		do_action("unlimited_elements/twig/add_custom_features", $this->twig);
		
	}
	
	
	
}