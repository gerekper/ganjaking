<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

class UniteCreatorAddonType{
	
	public $textSingle, $textPlural, $typeName, $typeNameDistinct, $typeNameCategory, $textShowType;
	public $isSVG = false, $isLayout = false, $titlePrefix = "", $isBasicType = true;
	
	//manager
	
	public $enableCategories = true, $enableShortcodes = false;
	public $allowDuplicateTitle = true, $isAutoScreenshot = false;
	public $allowNoCategory = true, $defaultCatTitle, $allowWebCatalog = true;
	public $exportPrefix = null, $requireCatalogPreview = false;
	public $catalogKey="addons", $allowManagerWebCatalog = true;
	public $arrCatalogExcludeCats = array();	//categories for exclude
	public $isWebCatalogMode = false;
	
	public $managerHeaderPrefix = null;
	public $showDescriptionField = true;
	public $hasParents = false;
	
	//addon view
	public $addonView_htmlTabOnly = false, $addonView_showConstantVars = true;
	public $addonView_showPreviewSettings = true, $addonView_showAddonDefaults = true;
	public $addonView_tabHtmlTitle = null, $addonView_htmlEditorMode = null;
	public $addonView_arrCustomConstants = null, $addonView_showTestAddon = true;
	public $addonView_urlBack = null, $addonView_showSmallIconOption = true;
	
	public $browser_addEmptyItem = false, $browser_textBuy = null, $browser_textHoverPro = null, $browser_urlBuyPro = null, $browser_buyProNewPage = false;
	public $browser_urlPreview = null;
	
	public static $arrTypesCache = array();
	public $pathAssets, $urlAssets;		//in case that assets path defers
	
	
	//internal
	public $textNoAddons;
	
	
	/**
	 * init the addon type
	 */
	protected function init(){
		
		$this->typeName = "";
		
		$this->textSingle = __("Addon", "unlimited-elements-for-elementor");
		$this->textPlural = __("Addons", "unlimited-elements-for-elementor");
		$this->textShowType = __("Regular Addon", "unlimited-elements-for-elementor");
		$this->defaultCatTitle = __("Main", "unlimited-elements-for-elementor");
		
	}
	
	
	/**
	 * init distinct type name
	 */
	private function initDistinctTypeName(){
		
		$this->typeNameDistinct = $this->typeName;
				
		if(!empty($this->typeNameDistinct))
			return(false);
		
		if($this->isLayout == true)
			$this->typeNameDistinct = GlobalsUC::ADDON_TYPE_REGULAR_LAYOUT;
		else
			$this->typeNameDistinct = GlobalsUC::ADDON_TYPE_REGULAR_ADDON;
		
	}
	
	
	/**
	 * init derived text
	 */
	protected function initInternal(){
		
		$this->textNoAddons = "No"." ".$this->textPlural." "."Found";
		
		$this->initDistinctTypeName();
		
		$this->typeNameCategory = $this->typeName;
		
		if(empty($this->typeNameCategory) && $this->isLayout == true)
			$this->typeNameCategory = GlobalsUC::ADDON_TYPE_REGULAR_LAYOUT;
		
	}
	
	
	/**
	 * function for override
	 */
	protected function initChild(){}
	
	
	/**
	 * constructor
	 */
	public function __construct($typeName = ""){
				
		$this->init();
		
		if(!empty($typeName))
			$this->typeName = $typeName;
		
		$this->initChild();
		$this->initInternal();
		
	}
	
	
	/**
	 * get addon type object
	 */
	public static function getAddonTypeObject($type, $isLayout = false){
		
		
		if(empty($type)){
			
			if($isLayout == true)
				$type = GlobalsUC::ADDON_TYPE_REGULAR_LAYOUT;
			else
				$type = GlobalsUC::ADDON_TYPE_REGULAR_ADDON;
		}
		
		
		$cacheName = $type;
		if(empty($type))
			$cacheName = GlobalsUC::ADDON_TYPE_REGULAR_ADDON;
		
		if(isset(self::$arrTypesCache[$cacheName]))
			return(self::$arrTypesCache[$cacheName]);
				
		switch($type){
			case GlobalsUC::ADDON_TYPE_SHAPE_DEVIDER:
				$objType = new UniteCreatorAddonType_Shape_Divider();
			break;
			case GlobalsUC::ADDON_TYPE_SHAPES:
				$objType = new UniteCreatorAddonType_Shape();
			break;
			case "elementor":	//special type
			case "vc":	//special type
				$objType = new UniteCreatorAddonType($type);
			break;
			case GlobalsUC::ADDON_TYPE_REGULAR_ADDON:
				$objType = new UniteCreatorAddonType();
			break;
			case GlobalsUC::ADDON_TYPE_REGULAR_LAYOUT:
				$objType = new UniteCreatorAddonType_Layout();
			break;
			case GlobalsUC::ADDON_TYPE_LAYOUT_SECTION:
				$objType = new UniteCreatorAddonType_Layout_Section();
			break;
			case GlobalsUC::ADDON_TYPE_LAYOUT_GENERAL:
				$objType = new UniteCreatorAddonType_Layout_General();
			break;
			case GlobalsUC::ADDON_TYPE_BGADDON:
				$objType = new UniteCreatorAddonType_BGAddon();
			break;
			default:
				UniteFunctionsUC::throwError("wrong addon type: ".$type);
			break;
		}

		
		self::$arrTypesCache[$cacheName] = $objType;
		
		return($objType);
	}
	
	
	/**
	 * get addon types for the picker
	 */
	public static function getAddonTypesForAddonPicker(){
		
		$arrTypeNames = array();
		$arrTypeNames[] = GlobalsUC::ADDON_TYPE_REGULAR_ADDON;
		$arrTypeNames[] = GlobalsUC::ADDON_TYPE_SHAPES;

		$arrTypes = array();
		foreach($arrTypeNames as $typeName){
		
			$objType = self::getAddonTypeObject($typeName);
			$arrTypes[$typeName] = $objType->textShowType;
		}
		
		return($arrTypes);
	}
	
		
}
