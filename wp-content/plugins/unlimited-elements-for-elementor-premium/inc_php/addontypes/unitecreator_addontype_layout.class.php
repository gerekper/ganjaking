<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

class UniteCreatorAddonType_Layout extends UniteCreatorAddonType{
	
	const DISPLAYTYPE_TABLE = "table";
	const DISPLAYTYPE_MANAGER = "manager";
	const DISPLAYTYPE_BOTH = "both";
	
	const LAYOUT_PARAMS_TYPE_SCREENSHOT = "screenshot";
	
	public $isTemplate = false, $displayType = self::DISPLAYTYPE_TABLE;
	public $layoutTypeForCategory = "layout", $allowImportFromCatalog = true, $allowManagerLocalLayouts = true;
	public $showPageSettings = true, $defaultBlankTemplate = false;
	public $paramsSettingsType = null, $paramSettingsTitle = null, $showParamsTopBarButton = false;
	public $putScreenshotOnGridSave = false;
	public $arrLayoutBrowserAddonTypes = null;
	public $postType = null, $isBloxPage = true;
	
		
	/**
	 * construct
	 */
	public function __construct($typeName = ""){
		parent::__construct($typeName);
	}
	
	/**
	 * init the addon type
	 */
	protected function initChild(){
		
		$this->isLayout = true;
		
		$this->textShowType = $this->textSingle;
		
		$this->paramsSettingsType = "screenshot";
		$this->paramSettingsTitle = __("Preview Image Settings", "unlimited-elements-for-elementor");
		
		$this->requireCatalogPreview = true;
		$this->allowWebCatalog = false;
		$this->catalogKey = "pages";
		$this->allowManagerWebCatalog = false;
		
	}
	
	
}
