<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');


class UniteCreatorPlugins extends UniteCreatorFilters{
	
	private static $isInited = false;
	private static $arrPlugins = array();
	
	
	/**
	 * register plugin
	 */
	public function registerPlugin($name, $title, $version, $description, $params=null){
				
		$arrPlugin = array();
		$arrPlugin["name"] = $name;
		$arrPlugin["title"] = $title;
		$arrPlugin["version"] = $version;
		$arrPlugin["description"] = $description;
		
		if(!empty($params) && is_array($params))
			$arrPlugin = array_merge($arrPlugin, $params);
		
		self::$arrPlugins[$name] = $arrPlugin;
	}
	
	
	/**
	 * init plugins
	 */
	public function initPlugins(){
		
		if(self::$isInited == true)
			UniteFunctionsUC::throwError("The plugins are already inited");
		
		$arrPaths = UniteProviderFunctionsUC::getArrPluginsPaths();
				
		foreach($arrPaths as $path){
			if(file_exists($path) == false)
				continue;
								
			require_once $path;
		}
		
	}
	
	
	/**
	 * get arr plugins
	 */
	public function getArrPlugins(){
		
		return(self::$arrPlugins);
	}
	
	/**
	 * check if plugin exists by name
	 */
	public function isPluginExists($name){
		
		if(empty(self::$arrPlugins))
			return(false);
			
		if(array_key_exists($name, self::$arrPlugins) == true)
			return(true);
			
		return(false);
	}
	
	
}