<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');


class UniteCreatorExporterBase extends UniteElementsBaseUC{
	
	protected $pathExport;
	protected $pathImport;
	public static $serial = 0;	//serial number
	private $arrLog = array();
	
	
	/**
	 * constructor
	 */
	public function __construct(){
	}
	
	/**
	 * add log text
	 */
	protected function addLog($text){
		$this->arrLog[] = $text;
	}
	
	
	/**
	 * get log text
	 */
	public function getLogText(){
		
		$text = implode("<br>", $this->arrLog);
		
		return($text);
	}
	
	
	/**
	 * prepare global export path
	 */
	protected function prepareExportFolders_globalExport(){
	
		$pathCache = GlobalsUC::$path_cache;
		
		UniteFunctionsUC::mkdirValidate($pathCache, "Cache");
	
		$pathExport = $pathCache."export/";
	
		UniteFunctionsUC::mkdirValidate($pathExport, "Export");
	
		$this->pathExport = $pathExport;
		
	}
	
	/**
	 * prepare global import folders
	 */
	protected function prepareImportFolders_globalImport(){
		
		//create cache folder
		$pathCache = GlobalsUC::$path_cache;
		UniteFunctionsUC::mkdirValidate($pathCache, "cache");
		
		//create import folder
		$this->pathImport = $pathCache."import/";
		UniteFunctionsUC::mkdirValidate($this->pathImport, "import");
			
		
		//create index.html
		UniteFunctionsUC::writeFile("", $this->pathImport."index.html");
		
	}
	
	
	
}