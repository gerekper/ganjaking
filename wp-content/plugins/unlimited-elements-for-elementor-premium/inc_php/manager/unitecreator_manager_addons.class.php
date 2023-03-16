<?php

/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

class UniteCreatorManagerAddonsWork extends UniteCreatorManager{
	
	const STATE_FILTER_CATALOG = "manager_filter_catalog";
	const STATE_FILTER_ACTIVE = "fitler_active_addons";
	const STATE_LAST_ADDONS_CATEGORY = "last_addons_cat";
	
	const FILTER_CATALOG_MIXED = "mixed";
	const FILTER_CATALOG_INSTALLED = "installed";
	const FILTER_CATALOG_WEB = "web";
	
	protected $numLocalCats = 0;
	private $filterAddonType = null;
	protected $objAddonType = null, $isLayouts = false, $enableActiveFilter = true, $enableEnterName = true;
	protected $enableSearchFilter = true;
	protected $enablePreview = true, $enableViewThumbnail = false, $enableMakeScreenshots = false;
	protected $enableDescriptionField = true, $enableEditGroup = false, $enableCopy = false;
	protected $enableActions = true;	//enable add/edit actions
	
	protected $textAddAddon, $textSingle, $textPlural, $textSingleLower, $textPluralLower;
	
	private $filterActive = "";
	private $showAddonTooltip = false, $showTestAddon = true;
	
	protected $filterCatalogState;
	protected $defaultFilterCatalog;
	protected $objBrowser;
	protected $urlBuy;
	protected $pluginName;
	protected $putUpdateCatalogButton = true;
	private $urlAjax;
	private $product;		//product for web api
	private $putItemButtonsType = "multiple";
	private $isInsideParent = false;
	private $isWebCatalogMode = false;
	
	public static $stateLabelCounter = 0;
	
	/**
	 * construct the manager
	 */
	public function __construct(){
		
		$this->pluginName = GlobalsUC::PLUGIN_NAME;
		$this->urlAjax = GlobalsUC::$url_ajax;
		$this->hasHeaderLine = true;
		
	}
	
	/**
	 * set plugin name
	 */
	public function setPluginName($pluginName){
		
		$this->pluginName = $pluginName;
	}
	
	
	/**
	 * set filter active state
	 */
	public static function setStateFilterCatalog($filterCatalog, $addonType = ""){
		
		if(empty($filterCatalog))
			return(false);
		
		HelperUC::setState(self::STATE_FILTER_CATALOG, $filterCatalog);
		
	}
	
	/**
	 * get filter active statge
	 */
	protected function getStateFilterCatalog(){
		
		if(GlobalsUC::$enableWebCatalog == false)
			return(self::FILTER_CATALOG_INSTALLED);
		
		if($this->objAddonType->allowWebCatalog == false)
			return(self::FILTER_CATALOG_INSTALLED);
		
		if($this->objAddonType->isWebCatalogMode == true)
			return(self::FILTER_CATALOG_MIXED);
					
		$filterCatalog = HelperUC::getState(self::STATE_FILTER_CATALOG);
		if(empty($filterCatalog))
			$filterCatalog = $this->defaultFilterCatalog;
		
		
		return($filterCatalog);
	}
	
	
	/**
	 * set filter active state
	 */
	public static function setStateFilterActive($filterActive, $addonType = ""){
		
		if(empty($filterActive))
			return(false);
				
		HelperUC::setState(UniteCreatorManagerAddons::STATE_FILTER_ACTIVE, $filterActive);
		
	}
	
	/**
	 * get filter active statge
	 */
	public static function getStateFilterActive($addonType = ""){
		
		$filterActive = HelperUC::getState(UniteCreatorManagerAddons::STATE_FILTER_ACTIVE);
		
		return($filterActive);
	}
	
	
	private function a___________INIT________(){}
	
	/**
	 * validate that addon type is set
	 */
	protected function validateAddonType(){
		
		if(empty($this->objAddonType))
			UniteFunctionsUC::throwError("addons manager error: no addon type is set");
		
		if($this->objAddonType->isLayout != $this->isLayouts)
			UniteFunctionsUC::throwError("addons manager error: mismatch addon and layout types");
		
	}
	
	
	/**
	 * before init
	 */
	protected function beforeInit($addonType){
		
		try{
			
			HelperUC::validateDBTablesExists();
			
		}catch(Exception $e){
			UniteFunctionsUC::throwError("DB Tables don't installed. Please refresh the page.");
		}
		
		
		$this->type = self::TYPE_ADDONS;
		$this->viewType = self::VIEW_TYPE_THUMB;
		$this->defaultFilterCatalog = self::FILTER_CATALOG_INSTALLED;
				
		$this->urlBuy = GlobalsUC::URL_BUY;
		$this->hasCats = true;
		
		if(emptY($this->filterAddonType))
			$this->setAddonType($addonType);
		
		$this->objBrowser = new UniteCreatorBrowser();
		$this->objBrowser->initAddonType($addonType);
		
		if(GlobalsUC::$is_admin_debug_mode == true)
			$this->putDialogDebug = true;
		
	}
	 
	/**
	 * run after init
	 */
	protected function afterInit($addonType){
		
		$this->validateAddonType();
				
		$this->itemsLoaderText = esc_html__("Getting ","unlimited-elements-for-elementor").$this->textPlural;
		$this->textItemsSelected = $this->textPluralLower . esc_html__(" selected","unlimited-elements-for-elementor");
		
		if($this->enableActiveFilter == true)
			$this->filterActive = self::getStateFilterActive($addonType);
		
		$this->filterCatalogState = $this->getStateFilterCatalog();
		
		
		//set selected category
		$lastCatID = HelperUC::getState(self::STATE_LAST_ADDONS_CATEGORY);
		if(!empty($lastCatID))
			$this->selectedCategory = $lastCatID;
		
		UniteProviderFunctionsUC::doAction(UniteCreatorFilters::ACTION_MODIFY_ADDONS_MANAGER, $this);
		
	}
	
	/**
	 * init layout specific permissions
	 */
	protected function initByAddonType_layout(){
		
		$this->isLayouts = true;
		
		if($this->objAddonType->isLayout == false)
			return(false);
		
		$this->enableActiveFilter = false;
		$this->enableEnterName = false;
		$this->showTestAddon = false;
		$this->enablePreview = true;
		$this->enableViewThumbnail = false;
		$this->enableEditGroup = true;
		$this->enableCopy = true;
		
		$this->addClass = "uc-manager-layouts";
		
		$this->isWebCatalogMode = true;
		
		$this->enableActions = false;
		$this->enableCatsActions = false;
		$this->enableStatusLineOperations = false;
		
		UniteProviderFunctionsUC::doAction("uc_manager_init_by_layout", $this);
		
	}
	
	/**
	 * init by layout master mode
	 */
	public function initByAddonType_layoutMaster(){
		
		$this->isWebCatalogMode = true;
		$this->enableActions = true;
		$this->enableCatsActions = true;
		$this->enableStatusLineOperations = true;
		
	}
	
	/**
	 * init some settings by addon type
	 */
	protected function initByAddonType(){
				
		//svg permissions
		if($this->objAddonType->isSVG == true){
			$this->showTestAddon = false;
		}
		
		//layout permissions
		if($this->objAddonType->isLayout == true)
			$this->initByAddonType_layout();
		
		
		$single = $this->objAddonType->textSingle;
		$plural = 	$this->objAddonType->textPlural;
		
		$pluralLower = strtolower($plural);
		
		$this->textSingle = $single;
		$this->textPlural = $plural;
		$this->textSingleLower = strtolower($single);
		$this->textPluralLower = strtolower($plural);
		
		//set text
		$this->arrText["confirm_remove_addons"] = esc_html__("Are you sure you want to delete those {$pluralLower}?", "unlimited-elements-for-elementor");
		
		$objLayouts = new UniteCreatorLayouts();
		
		$this->arrOptions["is_layout"] = $this->isLayouts;
		$this->arrOptions["url_screenshot_template"] = $objLayouts->getUrlTakeScreenshot();
		
		$this->textAddAddon = esc_html__("Add ", "unlimited-elements-for-elementor").$single;
		
		//set default filter
		if($this->objAddonType->allowManagerWebCatalog == true)
			$this->defaultFilterCatalog = self::FILTER_CATALOG_MIXED;
		
		if(!empty($this->objAddonType->browser_urlBuyPro))
			$this->urlBuy = $this->objAddonType->browser_urlBuyPro;
		
		if($this->objAddonType->showDescriptionField == false)
			$this->enableDescriptionField = false;
			
		if($this->objAddonType->enableCategories == false)
			$this->hasCats = false;
		
		
	}
	
	
	/**
	 * set filter addon type to use only it
	 */
	public function setAddonType($addonType){
		
		$this->filterAddonType = $addonType;
		
		$this->objAddonType = UniteCreatorAddonType::getAddonTypeObject($addonType, $this->isLayouts);
						
		$this->initByAddonType();
	}
	
	
	/**
	 * set manager name
	 */
	public function setManagerNameFromData($data){
				
		$name = UniteFunctionsUC::getVal($data, "manager_name");
		$addontype = UniteFunctionsUC::getVal($data, "manager_addontype");
		if(empty($addontype))
			$addontype = UniteFunctionsUC::getVal($data, "addontype");
		
		$passData = UniteFunctionsUC::getVal($data, "manager_passdata");
		
		if(!empty($name))
			$this->setManagerName($name);
			
		if(!empty($passData) && is_array($passData)){
			$this->arrPassData = $passData;			
		}
		
		
		$this->init($addontype);
		
		$this->setProductFromData($data);
		
	}
	
	
	private function a__________ADDON_HTML_______(){}
	
	
	/**
	 * get addon admin html add
	 */
	protected function getAddonAdminAddHtml(UniteCreatorAddon $objAddon){
		
		$addHtml = "";
		
		$addHtml = UniteProviderFunctionsUC::applyFilters(UniteCreatorFilters::FILTER_MANAGER_ADDON_ADDHTML, $addHtml, $objAddon);
		
		return($addHtml);
	}
	
	/**
	 * get addon admin html add
	 */
	protected function getLayoutAdminAddHtml(UniteCreatorLayout $objLayout){
		
		$addHtml = "";
		
		$addHtml = UniteProviderFunctionsUC::applyFilters(UniteCreatorFilters::FILTER_MANAGER_LAYOUT_ADDHTML, $addHtml, $objLayout);
		
				
		return($addHtml);
	}

	/**
	 * get addon admin html add
	 */
	protected function getLayoutAdminLIAddHtml(UniteCreatorLayout $objLayout){
		
		$addHtml = "";
		
		$addHtml = UniteProviderFunctionsUC::applyFilters(UniteCreatorFilters::FILTER_MANAGER_LAYOUT_LI_ADDHTML, $addHtml, $objLayout);
				
		return($addHtml);
	}
	
	
	
	
	/**
	 * get data of the admin html from addon
	 */
	private function getAddonAdminHtml_getDataFromAddon(UniteCreatorAddon $objAddon){
		
		$data = array();
		
		$objAddon->validateInited();
		
		$title = $objAddon->getTitle();
		
		$name = $objAddon->getNameByType();
		
		$description = $objAddon->getDescription();
		
		//set html icon
		$urlIcon = $objAddon->getUrlIcon();
		
		//get preview html
		$urlPreview = $objAddon->getUrlPreview();
		
		$itemID = $objAddon->getID();
		
		$isActive = $objAddon->getIsActive();
		
		$addHtml = $this->getAddonAdminAddHtml($objAddon);
		
		$fontIcon = $objAddon->getFontIcon();
		
		$svgIcon = $objAddon->getUrlSvgIconForEditor();
		
		$linkDemo = $objAddon->getOption("link_preview");
		
		
		$data["title"] = $title;
		$data["name"] = $name;
		$data["description"] = $description;
		$data["url_icon"] = $urlIcon;
		$data["url_preview"] = $urlPreview;
		$data["id"] = $itemID;
		$data["is_active"] = $isActive;
		$data["font_icon"] = $fontIcon;
		$data["svg_icon"] = $svgIcon;
		$data["add_html"] = $addHtml;
		$data["link_demo"] = $linkDemo;
		
		return($data);
	}
	
	/**
	 * get data from layout
	 */
	private function getAddonAdminHtml_getDataFromLayout(UniteCreatorLayout $objLayout){
		
		$addHtml = $this->getLayoutAdminAddHtml($objLayout);
		$liAddHtml = $this->getLayoutAdminLIAddHtml($objLayout);
		
		$data = array();
		
		$data["title"] = $objLayout->getTitle();
		$data["name"] = $objLayout->getName();
		$data["description"] = $objLayout->getDescription();
		$data["url_icon"] = $objLayout->getIcon();
		$data["url_preview"] = $objLayout->getPreviewImage();
		$data["url_preview_default"] = $objLayout->getDefaultPreviewImage();
		$data["id"] = $objLayout->getID();
		$data["is_active"] = true;		//no setting in layout yet
		$data["add_html"] = "";
		$data["url_edit"] = $objLayout->getUrlEditPost();
		$data["url_view_post"] = $objLayout->getUrlViewPost();
		$data["is_group"] = $objLayout->isGroup();
		$data["add_html"] = $addHtml;
		$data["li_add_html"] = $liAddHtml;
		
		
		return($data);
	}
	
	
	/**
	 * get add html of web addon
	 */
	private function getWebAddonData($addon){
		
		$isFree = $this->objBrowser->isWebAddonFree($addon); 
		
		$state = UniteCreatorBrowser::STATE_PRO;
		if($isFree == true)
			$state = UniteCreatorBrowser::STATE_FREE;
		
		$options = array();
		if($this->isWebCatalogMode == true)
			$options["web_catalog_mode"] = true;
		
		$data = $this->objBrowser->getCatalogAddonStateData($state, $this->isLayouts, null, $addon, $options);
		
		if(empty($data))
			$data = array();
		
		$typeName = $this->objAddonType->typeName;
		
		$data["url_preview_default"] = HelperUC::getDefaultPreviewImage($typeName);
		
		return($data);
	}
	
	
	/**
	 * get addons or layout by type
	 */
	private function getCatAddonsOrLayouts($catID, $filterActive, $params = null){
		
		$isLayout = $this->objAddonType->isLayout;
		
		//UniteFunctionsUC::showTrace();
		
		if($isLayout == false){		//addons
			$objAddons = new UniteCreatorAddons();
			$addons = $objAddons->getCatAddons($catID, false, $filterActive, $this->filterAddonType, false, $params);
						
			return($addons);
		}
		
		
		//layouts
		$objLayouts = new UniteCreatorLayouts();
		$arrLayouts = $objLayouts->getCatLayouts($catID, $this->objAddonType, false, $params);
		
				
		return($arrLayouts);
	}
	
	
	/**
	 * get web API
	 */
	private function getWebAPI(){
		
		$webAPI = new UniteCreatorWebAPI();
		
		if(!empty($this->product))
			$webAPI->setProduct($this->product);
					
		return($webAPI);
	}
	
	/**
	 * modify category addons, function for override
	 */
	protected function modifyCatAddons($addons, $addonType){

		return($addons);
	}
	
	/**
	 * get category addons, objects or array from catalog
	 */
	private function getCatAddons($catID, $title = "", $isweb = false, $params = null){
		
		$filterType = $this->filterAddonType;
		$filterActive = self::getStateFilterActive($this->filterAddonType);
		
		$filterCatalog = $this->getStateFilterCatalog();
		
		$filterSearch = UniteFunctionsUC::getVal($params, "filter_search");
		$filterSearch = trim($filterSearch);
		
		//if category title match the search, then get all the addons
		if(!empty($filterSearch)){
			
			$isTitleMatch = UniteFunctionsUC::isStringContains($title, $filterSearch);
			
			if($isTitleMatch == true)
				unset($params["filter_search"]);
		}
				
		$addons = array();
		
		
		switch($filterCatalog){
			case self::FILTER_CATALOG_WEB:
			break;
			case self::FILTER_CATALOG_INSTALLED:
				if($isweb == false)
					$addons = $this->getCatAddonsOrLayouts($catID, $filterActive, $params);
				
				return($addons);
			break;
			case self::FILTER_CATALOG_MIXED:
				if($isweb == false)
					$addons = $this->getCatAddonsOrLayouts($catID, $filterActive, $params);
			break;
		}
		
		//mix with the catalog
		
		//get category title
		if(!empty($catID) && empty($title)){
			$objCategories = new UniteCreatorCategories();
			$arrCat = $objCategories->getCat($catID);
			$title = UniteFunctionsUC::getVal($arrCat, "title");
		}
		
		if(empty($title))
			return($addons);
		
		if($this->objAddonType->allowManagerWebCatalog == false)
			return($addons);
		
		$webAPI = $this->getWebAPI();
		
		$addons = $webAPI->mergeCatAddonsWithCatalog($title, $addons, $this->objAddonType, $params);
		
		$addonType = $this->objAddonType->typeName;
		
		$addons = $this->modifyCatAddons($addons, $addonType);
		
		
		return($addons);
	}
	
	/**
	 * get additional addhtml, function for override
	 */
	protected function getAddonAdminHtml_AddHtml($addHtml, $objAddon){
		
		
		return($addHtml);
	}
		
	
	/**
	 * get html addon
	 */
	public function getAddonAdminHtml($objAddon){
		
		
		self::$stateLabelCounter = 0;
		
		$isLayout = false;
		
		if(is_array($objAddon))
			$data = $objAddon;
		else{
			
			$isLayout = $this->objAddonType->isLayout;
			
			if($this->objAddonType->isLayout == false)
				$data = $this->getAddonAdminHtml_getDataFromAddon($objAddon);
			else
				$data = $this->getAddonAdminHtml_getDataFromLayout($objAddon);
		}
				
		//--- prepare data
		
		$title = UniteFunctionsUC::getVal($data, "title");
		$name = UniteFunctionsUC::getVal($data, "name");
		$description = UniteFunctionsUC::getVal($data, "description");
		$urlIcon = UniteFunctionsUC::getVal($data, "url_icon");
		$urlPreview = UniteFunctionsUC::getVal($data, "url_preview");
		$urlPreviewDefault = UniteFunctionsUC::getVal($data, "url_preview_default");
		
		$itemID = UniteFunctionsUC::getVal($data, "id");
		$isActive = UniteFunctionsUC::getVal($data, "is_active");
		$addHtml = UniteFunctionsUC::getVal($data, "add_html");
		$liAddHTML = UniteFunctionsUC::getVal($data, "li_add_html");
		
		$isweb = UniteFunctionsUC::getVal($data, "isweb");
		$fontIcon = UniteFunctionsUC::getVal($data, "font_icon");
		$svgIcon = UniteFunctionsUC::getVal($data, "svg_icon");
		$urlEdit = UniteFunctionsUC::getVal($data, "url_edit");
		$urlViewPost = UniteFunctionsUC::getVal($data, "url_view_post");
		$isGroup = UniteFunctionsUC::getVal($data, "is_group");
		$isGroup = UniteFunctionsUC::strToBool($isGroup);
		$linkToDemo = UniteFunctionsUC::getVal($data, "link_demo");
		
		
		$state = null;
		
		if($isweb == true){
			
			$urlPreview = UniteFunctionsUC::getVal($data, "image");
			
			if(GlobalsUC::ENABLE_CATALOG_SHORTPIXEL == true)
				$urlPreview = GlobalsUC::SHORTPIXEL_PREFIX.$urlPreview;
			
			$isActive = true;
			$webData = $this->getWebAddonData($data);
			
			$urlPreviewDefault = UniteFunctionsUC::getVal($webData, "url_preview_default");
						
			$addHtml .= $webData["html_state"];
			$addHtml .= $webData["html_additions"];
			$state = $webData["state"];
			
			$itemID = UniteFunctionsUC::getSerialID("webaddon");
			$liAddHTML .= " data-itemtype='web' data-state='{$state}'";
			
			$isGroup = UniteFunctionsUC::getVal($data, "is_parent");
			$isGroup = UniteFunctionsUC::strToBool($isGroup);
			
			//for imported template			
			if($this->isLayouts == true)
				$importedTemplateID = UniteFunctionsUC::getVal($data, "imported_templateid");
				
		}
		
		
		//protection for url preview
		$arrInfo = pathinfo($urlPreview);
		$extension = UniteFunctionsUC::getVal($arrInfo, "extension");
		if(empty($extension))
			$urlPreview = null;
		
		
		UniteFunctionsUC::validateNotEmpty($itemID, "item id");
		
		$addHtml = $this->getAddonAdminHtml_AddHtml($addHtml, $objAddon);
		
		//put add html for layout
		if($this->isLayouts == true){
			
			//add group if available
			if($isGroup == true){

	        	$addStateClass = "";
	        	if(self::$stateLabelCounter > 0)
	        	$addStateClass = "uc-state-label".self::$stateLabelCounter;
				
				$stateLabel = __("Template Kit","unlimited-elements-for-elementor");
				$htmlState = "<div class='uc-state-label uc-state-group $addStateClass'>
					<div class='uc-state-label-text'>{$stateLabel}</div>
				</div>";
				
				$addHtml .= $htmlState;
				$liAddHTML .= " data-isgroup='true'";
				
				self::$stateLabelCounter++;
			}
			
			//add imported if available
			if(!empty($importedTemplateID)){

	        	$addStateClass = "";
	        	if(self::$stateLabelCounter > 0)
	        	$addStateClass = "uc-state-label".self::$stateLabelCounter;
				
				$stateLabel = __("Imported","unlimited-elements-for-elementor");
				$htmlState = "<div class='uc-state-label uc-state-imported $addStateClass'>
					<div class='uc-state-label-text'>{$stateLabel}</div>
				</div>";
				
				$arrLinks = HelperProviderUC::getImportedTemplateLinks($importedTemplateID);
				$linkView = $arrLinks["url"];
				$linkEdit = $arrLinks["url_edit"];
				
				$linkView = htmlspecialchars($linkView);
				$linkEdit = htmlspecialchars($linkEdit);
				
				$addHtml .= $htmlState;
				$liAddHTML .= " data-isimported='true' data-linkview='$linkView' data-linkedit='$linkEdit'";
				
				self::$stateLabelCounter++;
			}
			
		}
		
		//--- prepare output
				
		$title = htmlspecialchars($title);
		$name = htmlspecialchars($name);
		$description = htmlspecialchars($description);
		
		$descOutput = $description;
		
		$htmlPreview = "";
		
		$class = "uc-addon-thumbnail";
		$classThumb = "";
		$styleThumb = "";
		
		if(empty($urlPreview)){
			$classThumb = " uc-no-thumb";
			
			//replace by default preview
			if(!empty($urlPreviewDefault)){
				$classThumb = " uc-default-preview";
				$urlPreview = $urlPreviewDefault;
			}
		}
		
		if(!empty($urlPreview)){			
			$styleThumb = "style=\"background-image:url('{$urlPreview}')\"";
		}

		if($this->isWebCatalogMode && !empty($urlPreview)){
			$urlPreviewHtml = htmlspecialchars($urlPreview);
			$htmlPreview = "data-preview='$urlPreviewHtml'";
		}
		
		
		if($isActive == false)
			$class .= " uc-item-notactive";
		
		if($isweb == true)
			$class .= " uc-item-web";
			
		$class = "class=\"{$class}\"";
		
		$addData = "";
		if(!empty($urlEdit)){
			$liAddHTML .= " data-urledit=\"$urlEdit\"";
		}
		
		if(!empty($urlViewPost))
			$liAddHTML .= " data-urlview=\"$urlViewPost\"";
		
		
		//set html output
		$htmlItem  = "<li id=\"uc_item_{$itemID}\" data-id=\"{$itemID}\" data-title=\"{$title}\" data-name=\"{$name}\" data-description=\"{$description}\" {$liAddHTML} {$htmlPreview} {$class} >";
		
		if($state == UniteCreatorBrowser::STATE_PRO){
			$urlBuy = $this->urlBuy;
						
			$htmlItem .= "<a class='uc-link-item-pro' href='$urlBuy' target='_blank'>";
		}
		
		if(!empty($svgIcon)){
			
			$title = "<img class=\"uc-item-title__image\" src=\"{$svgIcon}\"></img>".$title;
			
		}else{
			
			//add icon to title
			if(!empty($fontIcon))
				$title = "<i class=\"{$fontIcon}\"></i> ".$title;
			
		}
		
		
		//if svg type - set preview url as svg
		if($this->objAddonType->isSVG == true){
			
			$classThumb .= " uc-type-shape-devider";
			
			if($isweb == false){
				$urlPreview = null;
				
				$svgContent = $objAddon->getHtml();
				$urlPreview = UniteFunctionsUC::encodeSVGForBGUrl($svgContent);
			}
			
		}
			
			//output thumb
			$htmlItem .= "	<div class=\"uc-item-thumb{$classThumb} unselectable\" unselectable=\"on\" {$styleThumb}>";
			
			//draw item actions
			$actionEdit = "edit_addon";
			if($isLayout == true){
				$actionEdit = "edit_addon_blank";
				
				if($isGroup == true)
					$actionEdit = "edit_layout_group";
			}
			
			$urlIconEdit = GlobalsUC::$urlPluginImages."icon_item_edit.svg";
			$urlIconPreview = GlobalsUC::$urlPluginImages."icon_item_preview.svg";
			$urlIconDuplicate = GlobalsUC::$urlPluginImages."icon_item_duplicate.svg";
			$urlIconMenu = GlobalsUC::$urlPluginImages."icon_item_menu.svg";
			
			$textPreview = __("Preview ", "unlimited-elements-for-elementor").$this->textSingle;
			$textEdit = __("Edit ", "unlimited-elements-for-elementor").$this->textSingle;
			
			if($isGroup == true){
				$textPreview = __("Preview Template Kit", "unlimited-elements-for-elementor");
				$textEdit = __("Edit Template Kit", "unlimited-elements-for-elementor");
			}
			
			$textDuplicate = __("Duplicate ", "unlimited-elements-for-elementor").$this->textSingle;
			
			$htmlItem .= "<div class=\"uc-item-actions\">";
			
			$htmlItem .= "	<a href='javascript:void(0)' class='uc-item-action uc-item-action-edit uc-tip' onfocus='this.blur()' data-action='{$actionEdit}' title='{$textEdit}' ><img src='{$urlIconEdit}'></a>";

			$textViewDemo = __("View ", "unlimited-elements-for-elementor").$this->textSingle.__(" Demo and Help", "unlimited-elements-for-elementor");
			
			if($isGroup == false){
				
				//preview widget
				if(!empty($linkToDemo))
					$htmlItem .= "	<a href='{$linkToDemo}' target='_blank' class='uc-item-action uc-item-action-preview uc-tip' onfocus='this.blur()' title='$textViewDemo'><img src='{$urlIconPreview}'></a>";
				else
					$htmlItem .= "	<a href='javascript:void(0)' class='uc-item-action uc-item-action-preview uc-tip' onfocus='this.blur()' data-action='preview_addon' title='$textPreview'><img src='{$urlIconPreview}'></a>";
					
				$htmlItem .= "	<a href='javascript:void(0)' class='uc-item-action uc-item-action-duplicate uc-tip' onfocus='this.blur()' data-action='duplicate_item' title='$textDuplicate'><img src='{$urlIconDuplicate}'></a>";
			}
			
			$htmlItem .= "	<a href='javascript:void(0)' class='uc-item-action uc-item-action-menu' onfocus='this.blur()' data-action='open_menu'><img src='{$urlIconMenu}'></a>";
			
			$htmlItem .= "	<div class='unite-clear'></div>";
			
			$htmlItem .= "</div>";
			
			$htmlItem .= "</div>";
			
			
			$htmlItem .= "	<div class=\"uc-item-title unselectable\" unselectable=\"on\">{$title}</div>";
			
			if($addHtml)
				$htmlItem .= $addHtml;
			
		
		if($state == UniteCreatorBrowser::STATE_PRO){
			$htmlItem .= "</a>";
		}
		
		$htmlItem .= "</li>";
		
		
		return($htmlItem);
	}
	
	
	/**
	 * get html of cate items
	 */
	public function getCatAddonsHtml($catID, $title = "", $isweb = false, $params = array()){
		
		$addons = $this->getCatAddons($catID, $title, $isweb, $params);
				
		$htmlAddons = "";
		
		foreach($addons as $addon){
			
			$html = $this->getAddonAdminHtml($addon);
			$htmlAddons .= $html;
		}
		
		return($htmlAddons);
	}
	
	
	/**
	 * get html of categories and items.
	 */
	public function getCatsAndAddonsHtml($catID, $catTitle = "", $isweb = false, $params = array()){
		
		$arrCats = $this->getArrCats($params);
		
		//change category if needed
		$arrCatsAssoc = UniteFunctionsUC::arrayToAssoc($arrCats, "id");
		
		if(isset($arrCatsAssoc[$catID]) == false){
			
			$catID = null;
			
			$firstCat = reset($arrCats);
			
			if(!empty($firstCat)){
				$catID = $firstCat["id"];
				$catTitle = $firstCat["title"];
				$isweb = UniteFunctionsUC::getVal($firstCat, "isweb");
				$isweb = UniteFunctionsUC::strToBool($isweb);
			}
		}
		
		
		$objCats = new UniteCreatorCategories();
		$htmlCatList = $this->getCatList($catID, null, $params);
		
		$htmlAddons = $this->getCatAddonsHtml($catID, $catTitle, $isweb, $params);
		
		$response = array();
		$response["htmlItems"] = $htmlAddons;
		$response["htmlCats"] = $htmlCatList;
	
		return($response);
	}
	
	
	/**
	 * set last selected category state
	 */
	private function setStateLastSelectedCat($catID){
		HelperUC::setState(self::STATE_LAST_ADDONS_CATEGORY, $catID);
	}
		
	
	/**
	 * set product from data
	 */
	private function setProductFromData($data){
		
		//get product
		$product = "";
		$passData = UniteFunctionsUC::getVal($data, "manager_passdata");
		if(empty($passData))
			return(false);
			
		$product = UniteFunctionsUC::getVal($passData, "product");
		
		if(empty($product))
			return(false);
		
		
		$this->product = $product;
		
		$this->objBrowser->setProduct($product);
		
	}
	
	
	/**
	 * get category items html
	 */
	public function getCatAddonsHtmlFromData($data){
		
		$this->validateAddonType();
				
		$catID = UniteFunctionsUC::getVal($data, "catID");
		$catTitle = UniteFunctionsUC::getVal($data, "title");
		$parentID = UniteFunctionsUC::getVal($data, "parent_id");
		
		$this->setProductFromData($data);
		
		$objAddons = new UniteCreatorAddons();
		
		$resonseCombo = UniteFunctionsUC::getVal($data, "response_combo");
		$resonseCombo = UniteFunctionsUC::strToBool($resonseCombo);
				
		$filterActive = UniteFunctionsUC::getVal($data, "filter_active");
		
		$filterSearch = UniteFunctionsUC::getVal($data, "filter_search");
		
		$filterSearch = trim($filterSearch);
		
		$isweb = UniteFunctionsUC::getVal($data, "isweb");
		$isweb = UniteFunctionsUC::strToBool($isweb);
		
		if($isweb == false && $catID != "all")
			UniteFunctionsUC::validateNumeric($catID,"category id");
		
		if(GlobalsUC::$enableWebCatalog == true){
			
			$filterCatalog = UniteFunctionsUC::getVal($data, "filter_catalog");
			self::setStateFilterCatalog($filterCatalog);
		}
		
		self::setStateFilterActive($filterActive);
		$this->setStateLastSelectedCat($catID);
		
		$params = array();
		
		if(!empty($filterSearch))
			$params["filter_search"] = $filterSearch;
		
		if(!empty($parentID)){
			$this->isInsideParent = true;
			$params["parent_id"] = $parentID;
		}
		
		
		if($resonseCombo == true){
			
			//dmp($isweb);dmp($catTitle);dmp($catID);dmp($params);exit();
			
			$response = $this->getCatsAndAddonsHtml($catID, $catTitle, $isweb, $params);
			
		}else{
			$itemsHtml = $this->getCatAddonsHtml($catID, $catTitle, $isweb, $params);
			$response = array("itemsHtml"=>$itemsHtml);
		}
		
		
		return($response);
	}
		
		
	private function a________DIALOGS________(){}
	
	/**
	 * put debug dialog
	 */
	private function putDialogDebug(){
		
		?>
		
		<div id="uc_manager_dialog_debug" title="Debug Dialog" style="display:none;">
			
			<h2>Url API: </h2>
			
			<?php echo GlobalsUC::URL_API?>
		</div>
		
		<?php 
	}
	
	/**
	 * put import addons dialog
	 */
	private function putDialogImportAddons(){
		
		$importText = esc_html__("Import ", "unlimited-elements-for-elementor").$this->textPlural;
		$textSelect = esc_html__("Select ","unlimited-elements-for-elementor") . $this->textPluralLower . __(" export zip file (or files)","unlimited-elements-for-elementor");
		$textLoader = esc_html__("Uploading ","unlimited-elements-for-elementor") . $this->textSingleLower. __(" file...", "unlimited-elements-for-elementor");
		$textSuccess = $this->textSingle . esc_html__(" Added Successfully", "unlimited-elements-for-elementor");
		
		$dialogTitle = $importText;
		
		//overwrite checkbox
		$checkboxOverwriteAddParams = "checked=\"checked\"";
		$textOverwrite = esc_html__("Overwrite Existing ", "unlimited-elements-for-elementor").$this->textPlural;
		if($this->isLayouts == true){
			$textOverwrite = esc_html__("Overwrite Widgets", "unlimited-elements-for-elementor");
			$checkboxOverwriteAddParams = "";			
		}
		
		$nonce = "";
		if(method_exists("UniteProviderFunctionsUC", "getNonce"))
			$nonce = UniteProviderFunctionsUC::getNonce();
		?>
		
			<div id="dialog_import_addons" class="unite-inputs" title="<?php echo esc_attr($dialogTitle)?>" style="display:none;">
				
				<div class="unite-dialog-top"></div>
				
				<div class='dialog-import-addons-left'>
					
					<div class="unite-inputs-label">
						<?php echo esc_html($textSelect)?>:
					</div>
					
					<div class="unite-inputs-sap-small"></div>
					
					<form id="dialog_import_addons_form" action="<?php echo esc_attr($this->urlAjax)?>" name="form_import_addon" class="dropzone uc-import-addons-dropzone">
						<input type="hidden" name="action" value="<?php echo esc_attr($this->pluginName)?>_ajax_action">
						<input type="hidden" name="client_action" value="import_addons">
						
						<?php if(!empty($nonce)):?>
							<input type="hidden" name="nonce" value="<?php echo esc_attr($nonce)?>">
						<?php endif?>
						<script type="text/javascript">
							if(typeof Dropzone != "undefined")
								Dropzone.autoDiscover = false;
						</script>
					</form>	
						<div class="unite-inputs-sap-double"></div>
						
						<div class="unite-inputs-label">
							<?php esc_html_e("Import to Category", "unlimited-elements-for-elementor")?>:
							
						<select id="dialog_import_catname">
							<option value="autodetect" ><?php esc_html_e("[Autodetect]", "unlimited-elements-for-elementor")?></option>
							<option id="dialog_import_catname_specific" value="specific"><?php esc_html_e("Current Category", "unlimited-elements-for-elementor")?></option>
						</select>
							
						</div>
						
						<div class="unite-inputs-sap-double"></div>
						
						<div class="unite-inputs-label">
							<label for="dialog_import_check_overwrite">
							
								<input type="checkbox" <?php echo $checkboxOverwriteAddParams?>  id="dialog_import_check_overwrite"></input>
								
								<?php echo esc_html($textOverwrite) ?>
								
							</label>
						</div>
						
				
				</div>
				
				<div id="dialog_import_addons_log" class='dialog-import-addons-right' style="display:none">
					
					<div class="unite-bold"> <?php echo esc_html($importText).esc_html__(" Log","unlimited-elements-for-elementor")?> </div>
					
					<br>
					
					<div id="dialog_import_addons_log_text" class="dialog-import-addons-log"></div>
				</div>
				
				<div class="unite-clear"></div>
				
				<?php 
					$prefix = "dialog_import_addons";
					$buttonTitle = $importText;
					$loaderTitle = $textLoader;
					$successTitle = $textSuccess;
					HelperHtmlUC::putDialogActions($prefix, $buttonTitle, $loaderTitle, $successTitle);
				?>
				
					
			</div>		
		<?php 
	}
	
	
	/**
	 * put quick edit dialog
	 */
	private function putDialogQuickEdit(){
		?>
			<!-- dialog quick edit -->
		
			<div id="dialog_edit_item_title"  title="<?php esc_html_e("Quick Edit","unlimited-elements-for-elementor")?>" style="display:none;">
			
				<div class="dialog_edit_title_inner unite-inputs mtop_20 mbottom_20" >
			
					<div class="unite-inputs-label-inline">
						<?php esc_html_e("Title", "unlimited-elements-for-elementor")?>:
					</div>
					<input type="text" id="dialog_quick_edit_title" class="unite-input-wide">
					
					<?php if($this->enableEnterName):?>
					<div class="unite-inputs-sap"></div>
							
					<div class="unite-inputs-label-inline">
						<?php esc_html_e("Name", "unlimited-elements-for-elementor")?>:
					</div>
					<input type="text" id="dialog_quick_edit_name" class="unite-input-wide">
					
					<?php else:?>
					
					<input type="hidden" id="dialog_quick_edit_name">
					
					<?php endif?>
					
					<div class="unite-inputs-sap"></div>
					
					<div class="unite-inputs-label-inline">
						<?php esc_html_e("Description", "unlimited-elements-for-elementor")?>:
					</div>
					
					<textarea class="unite-input-wide" id="dialog_quick_edit_description"></textarea>
					
					<?php UniteProviderFunctionsUC::doAction("uc_quick_edit_dialog_html", $this)?>
					
				</div>
				
			</div>
		
		<?php 
	}

	
	/**
	 * put category edit dialog
	 */
	protected function putDialogEditCategory(){
		
		$prefix = "uc_dialog_edit_category";
		
		?>
			<div id="uc_dialog_edit_category" class="uc-dialog-edit-category" data-custom='yes' title="<?php esc_html_e("Edit Category","unlimited-elements-for-elementor")?>" style="display:none;" >
				
				<div class="unite-dialog-top"></div>
				
				<div class="unite-dialog-inner-constant">	
					<div id="<?php echo esc_attr($prefix)?>_settings_loader" class="loader_text"><?php esc_html_e("Loading Settings", "unlimited-elements-for-elementor")?>...</div>
					
					<div id="<?php echo esc_attr($prefix)?>_settings_content"></div>
					
				</div>
				
				<?php 
					$buttonTitle = esc_html__("Update Category", "unlimited-elements-for-elementor");
					$loaderTitle = esc_html__("Updating Category...", "unlimited-elements-for-elementor");
					$successTitle = esc_html__("Category Updated", "unlimited-elements-for-elementor");
					HelperHtmlUC::putDialogActions($prefix, $buttonTitle, $loaderTitle, $successTitle);
				?>
				
			</div>
		
		<?php
	}
	
	/**
	 * put category edit dialog
	 */
	protected function putDialogAddonProperties(){
		
		$prefix = "uc_dialog_addon_properties";
		
		$textTitle =  $this->textSingle.esc_html__(" Properties", "unlimited-elements-for-elementor");
		
		
		?>
			<div id="uc_dialog_addon_properties" class="uc-dialog-addon-properties" data-custom='yes' title="<?php echo esc_attr($textTitle)?>" style="display:none;" >
				
				<div class="unite-dialog-top"></div>
				
				<div class="unite-dialog-inner-constant">	
					<div id="<?php echo esc_attr($prefix)?>_settings_loader" class="loader_text uc-settings-loader"><?php esc_html_e("Loading Properties", "unlimited-elements-for-elementor")?>...</div>
					
					<div id="<?php echo esc_attr($prefix)?>_settings_content" class="uc-settings-content"></div>
					
				</div>
				
				<?php 
					$buttonTitle = esc_html__("Update ", "unlimited-elements-for-elementor").$this->textSingle;
					$loaderTitle = esc_html__("Updating...", "unlimited-elements-for-elementor");
					$successTitle = $this->textSingle.esc_html__(" Updated", "unlimited-elements-for-elementor");
					HelperHtmlUC::putDialogActions($prefix, $buttonTitle, $loaderTitle, $successTitle);
				?>			
				
			</div>
		
		<?php
	}
	
	
	/**
	 * put add addon dialog
	 */
	private function putDialogAddAddon(){
		
		$styleDesc = "";
		if($this->enableDescriptionField == false)
			$styleDesc = "style='display:none'";
		
		
		?>
			<!-- add addon dialog -->
			
			<div id="dialog_add_addon" class="unite-inputs" title="<?php echo esc_attr($this->textAddAddon)?>" style="display:none;">
				
				<div class="unite-dialog-top"></div>
			
				<div class="unite-inputs-label">
					<?php echo esc_html($this->textSingle).esc_html__(" Title", "unlimited-elements-for-elementor")?>:
				</div>
				
				<input type="text" id="dialog_add_addon_title" class="dialog_addon_input unite-input-100" />
				
				<?php if($this->enableEnterName):?>
				<div class="unite-inputs-sap"></div>
				
				<div class="unite-inputs-label">
					<?php echo esc_html($this->textSingle.__(" Name"))?>:
				</div>
				
				<input type="text" id="dialog_add_addon_name" class="dialog_addon_input unite-input-100" />
				
				<?php else:?>
				
				<input type="hidden" id="dialog_add_addon_name" value="" />
				
				<?php endif?>
				
				<?php 
					if($this->enableDescriptionField == false):		//description placeholder
					?>
					<div class="vert_sap30"></div>
					<?php 
					endif;
				?>
				
				<div class="unite-dialog-description-wrapper" <?php echo $styleDesc?>>
					
					<div class="unite-inputs-sap"></div>
					
					<div class="unite-inputs-label">
						<?php echo esc_html($this->textSingle).esc_html__(" Description")?>:
					</div>
					
					<textarea id="dialog_add_addon_description" class="dialog_addon_input unite-input-100" ></textarea>
				</div>
				
				<?php 
				
					$prefix = "dialog_add_addon";
					$buttonTitle = $this->textAddAddon;
					$loaderTitle = esc_html__("Adding ","unlimited-elements-for-elementor").$this->textSingle."...";
					$successTitle = $this->textSingle. esc_html__(" Added Successfully", "unlimited-elements-for-elementor");
					HelperHtmlUC::putDialogActions($prefix, $buttonTitle, $loaderTitle, $successTitle);
				?>
				
			</div>
		
		<?php 
	}	
	
	/**
	 * put preview addon dialog
	 */
	private function putDialogPreviewAddons(){
		
		$textPreviw = "Preview ".$this->textSingle;
		
		$textPreviw = htmlspecialchars($textPreviw);
		
		?>				
		
		<div id="uc_dialog_item_preview" title="<?php echo $textPreviw?>" style="display:none;">
			
			<iframe src="" width="100%" height="100%"  style="overflow-x: hidden;overflow-y:auto;">
			
		</iframe>
		
		</div>
		
		<?php 
	}
	
	/**
	 * put preview template dialog
	 */
	private function putPreviewTemplateDialog(){
		
		//set warning text
		$maxExecutionTime = @ini_get("max_execution_time");
		
		$warningText = "";
		$maxExecutionTime = (int)$maxExecutionTime;
		
		if($maxExecutionTime > 0 && $maxExecutionTime <= 30){
			@ini_set("max_execution_time", 300);
			
			$maxTime = @ini_get("max_execution_time");
			$maxTime = (int)$maxTime;
			
			if($maxTime <= 30)
				$warningText = __("Notice: Your php setting: max_execution_time is <b>$maxExecutionTime</b> seconds. It is not efficient enough for importing the template. Please increase this value in php.ini. If you don't know how to change it please contact your hosting provider.");
		}
		
		$dialogTitle = __("Preview Template");
		$confirmImportAgainMessage = __("This import will overwrite the existing imported template. Continue?");
		$confirmImportAgainMessage = htmlspecialchars($confirmImportAgainMessage);
		
		$urlImageBase = GlobalsUC::$urlPluginImages;
		
		$isRTL = GlobalsUC::$isAdminRTL;
		
		$addClass = "";
		if($isRTL == true)
			$addClass = " uc-rtl";
		
		?>
		<div id="uc_dialog_preview_template" class="uc-dialog-preview-template unite-inputs<?php echo $addClass?>" title="<?php echo esc_attr($dialogTitle)?>" style="display:none;">
				
				<div class="uc-dialog-preview-template__preview">
					<img src="" class="uc-dialog-preview-template__image">
				</div>
				
				<div class="uc-dialog-preview-template__right">
					
					<div class="uc-dialog-preview-template__buttons-panel">
						
						<a id="uc_dialog_import_template_button_prev" href="javascript:void(0)" class="uc-dialog-preview-template__button-top uc-button-disabled" title="<?php _e("To Previous Template", "unlimited-elements-for-elementor")?>">
							<img src="<?php echo $urlImageBase?>icon-gray-prev.svg">
						</a><a id="uc_dialog_import_template_button_next" href="javascript:void(0)" class="uc-dialog-preview-template__button-top" title="<?php _e("To Next Template", "unlimited-elements-for-elementor")?>">
							<img src="<?php echo $urlImageBase?>icon-gray-next.svg">
						</a><a id="uc_dialog_import_template_button_close" href="javascript:void(0)" class="uc-dialog-preview-template__button-top" title="<?php _e("Back To Catalog", "unlimited-elements-for-elementor")?>">
							<img src="<?php echo $urlImageBase?>icon-gray-close.svg">
						</a>
						
					</div>
									
					<div class="uc-dialog-preview-template__title">Template Title</div>
					
					<div class="uc-dialog-preview-template__right-operations">
					
						<h2><?php _e("Import Template","unlimited-elements-for-elementor")?></h2>
						
						<p>
							<?php _e("To get started click the \"Import Template\" button.","unlimited-elements-for-elementor")?>
							<?php _e("After import is completed the template will show under Elementor Saved Templates list for future use.","unlimited-elements-for-elementor")?>
						</p>
						
						<br>
						
						<a href="javascript:void(0)" class="unite-button-primary uc-dialog-preview-template__button-import uc-show-when-new uc-hide-when-loading uc-hide-when-just-imported"><?php _e("Import Template","unlimited-elements-for-elementor")?></a>
						<a href="javascript:void(0)" class="unite-button-primary uc-dialog-preview-template__button-import-again uc-show-when-imported uc-hide-when-loading uc-hide-when-just-imported" data-message-confirm="<?php echo $confirmImportAgainMessage?>" ><?php _e("Import Template Again","unlimited-elements-for-elementor")?></a>
						
						<div id="uc_dialog_import_template_loader" class="uc-dialog-preview-template__loader" style="display:none">
							<span class="template-dialog-loader">
								<span>I</span>
								<span>m</span>
								<span>p</span>
								<span>o</span>
								<span>r</span>
								<span>t</span>
								<span>i</span>
								<span>n</span>
								<span>g</span>
							</span>
						</div>
						<div id="uc_dialog_import_template_success" class="uc-dialog-preview-template__import-success" style="display:none"></div>
						<div id="uc_dialog_import_template_error" class="uc-dialog-preview-template__import-error" style="display:none"></div>
						
						<div id="uc_dialog_import_template_imported_message_top"></div>
						
						<div id="uc_dialog_import_template_imported_message" class="uc-dialog-preview-template__imported-message" style="display:none">
							
							<div class="uc-dialog-preview-template__imported-message-text">
								<span class="uc-show-when-new"><?php _e("Template Imported Successfully","unlimited-elements-for-elementor")?>.</span>
								<span class="uc-show-when-imported"><?php _e("Template Already Imported","unlimited-elements-for-elementor")?>.</span>
							</div>
							
							<div class="uc-dialog-preview-template__action-buttons-wrapper">
								<a href="#" class="unite-button-secondary uc-dialog-preview-template__imported-message-link1" target="_blank" data-text-bottom="<?php _e("View Page", "unlimited-elements-for-elementor")?>" data-text-top="<?php _e("View Template", "unlimited-elements-for-elementor")?>"><?php _e("View Template", "unlimited-elements-for-elementor")?></a>
								<a href="#" class="unite-button-secondary uc-dialog-preview-template__imported-message-link2" target="_blank"><?php _e("Edit With Elementor","unlimited-elements-for-elementor")?></a>
							</div>
						</div>
						
						<div class="uc-dialog-preview-template__create-page-wrapper">
							
							<h2><?php _e("Create Page From Template","unlimited-elements-for-elementor")?></h2>
							
							<div class="uc-dialog-preview-template__import-page-wrapper">
							
								<input type="text" placeholder="<?php _e("Enter Page Name", "unlimited-elements-for-elementor")?>" class="uc-dialog-preview-template__page-name">
								<a href="javascript:void(0)" class="unite-button-secondary uc-dialog-preview-template__button-create-page uc-disable-when-loading"><?php _e("Create Page","unlimited-elements-for-elementor")?></a>
								
							</div>
							
							<div id="uc_dialog_import_template_createpage_loader" class="uc-dialog-preview-template__createpage-loader" style="display:none">
								
								<span class="template-dialog-loader">
									<span>I</span>
									<span>m</span>
									<span>p</span>
									<span>o</span>
									<span>r</span>
									<span>t</span>
									<span>i</span>
									<span>n</span>
									<span>g</span>
								</span>
								
							</div>
														
							<div id="uc_dialog_import_template_createpage_error" class="uc-dialog-preview-template__createpage-error" style="display:none"></div>
		
							<div id="uc_dialog_import_template_imported_message_bottom"></div>
						</div>						
					</div>
					
					<div class="uc-dialog-preview-template__right-message-pro">
						
						<?php _e("This template is available only for the PRO version users of Unlimited Elements plugin.","unlimited-elements-for-elementor")?>
						
						<br><br>
						
						<?php _e("You can purchase a pro version here","unlimited-elements-for-elementor")?>: 
						
						<br><br>
						
						<a href="<?php echo GlobalsUC::URL_BUY?>" class="unite-button-primary" target="_blank">Buy Unlimited Elements PRO</a>
						
					</div>
					
					<?php if(!empty($warningText)):?>
					<div class="uc-dialog-preview-template__right-warning-message">
						<?php echo $warningText?>
					</div>
					<?php endif?>
					
				</div>		<!-- right -->
							
		</div>
		<?php 
	}
	
	private function a______MENUS_______(){}
	
	
	/**
	 * get single item menu
	 */
	protected function getMenuSingleItem(){
		
		$arrMenuItem = array();
		
		if($this->isLayouts == false){
			$arrMenuItem["edit_addon"] = esc_html__("Edit ","unlimited-elements-for-elementor").$this->textSingle;
			$arrMenuItem["edit_addon_blank"] = esc_html__("Edit In New Tab","unlimited-elements-for-elementor");
		}else{
			$arrMenuItem["edit_addon_blank"] = esc_html__("Edit ","unlimited-elements-for-elementor").$this->textSingle;			
		}
	
		if($this->enableEditGroup)
			$arrMenuItem["edit_layout_group"] = esc_html__("Edit Template Kit","unlimited-elements-for-elementor");
		
		if($this->enablePreview == true)
			$arrMenuItem["preview_addon"] = esc_html__("Preview","unlimited-elements-for-elementor");
		
		if($this->enableViewThumbnail)
			$arrMenuItem["preview_thumb"] = esc_html__("View Thumbnail","unlimited-elements-for-elementor");
		
		if($this->enableMakeScreenshots)
			$arrMenuItem["make_screenshots"] = esc_html__("Make Thumbnail","unlimited-elements-for-elementor");
		
			
		$arrMenuItem["quick_edit"] = esc_html__("Quick Edit","unlimited-elements-for-elementor");
		
		if($this->enableCopy == true)
			$arrMenuItem["copy"] = esc_html__("Copy","unlimited-elements-for-elementor");
		
			
		$arrMenuItem["remove_item"] = esc_html__("Delete","unlimited-elements-for-elementor");
		
		if($this->showTestAddon){
			$arrMenuItem["test_addon"] = esc_html__("Test ","unlimited-elements-for-elementor").$this->textSingle;
			$arrMenuItem["test_addon_blank"] = esc_html__("Test In New Tab","unlimited-elements-for-elementor");
		}	
		
		$arrMenuItem["export_addon"] = esc_html__("Export ","unlimited-elements-for-elementor").$this->textSingle;
		
		$arrMenuItem = UniteProviderFunctionsUC::applyFilters(UniteCreatorFilters::FILTER_MANAGER_MENU_SINGLE, $arrMenuItem);
		
		return($arrMenuItem);
	}

	
	
	/**
	 * get item field menu
	 */
	protected function getMenuField(){
		
		if($this->enableActions == false)
			return parent::getMenuField();
		
		$arrMenuField = array();
				
		$arrMenuField["select_all"] = esc_html__("Select All","unlimited-elements-for-elementor");
		
		$arrMenuField = UniteProviderFunctionsUC::applyFilters(UniteCreatorFilters::FILTER_MANAGER_MENU_FIELD, $arrMenuField);
		
		return($arrMenuField);
	}
	
	
	
	/**
	 * get multiple items menu
	 */
	protected function getMenuMulitipleItems(){
		$arrMenuItemMultiple = array();
		$arrMenuItemMultiple["remove_item"] = esc_html__("Delete","unlimited-elements-for-elementor");
		
		if($this->enableMakeScreenshots == true)
			$arrMenuItemMultiple["make_screenshots"] = esc_html__("Make Thumbnails","unlimited-elements-for-elementor");
		
		$arrMenuItemMultiple = UniteProviderFunctionsUC::applyFilters(UniteCreatorFilters::FILTER_MANAGER_MENU_MULTIPLE, $arrMenuItemMultiple);
		
		return($arrMenuItemMultiple);
	}
	
	
	/**
	 * get category menu
	 */
	protected function getMenuCategory(){
	
		$arrMenuCat = array();
		$arrMenuCat["edit_category"] = esc_html__("Edit Category","unlimited-elements-for-elementor");
		$arrMenuCat["delete_category"] = esc_html__("Delete Category","unlimited-elements-for-elementor");
		
		$arrMenuCat = UniteProviderFunctionsUC::applyFilters(UniteCreatorFilters::FILTER_MANAGER_MENU_CATEGORY, $arrMenuCat);
		
		if($this->enableCatsActions == false){
			$arrMenuCat = array();
			$arrMenuCat["no_action"] = esc_html__("No Action","unlimited-elements-for-elementor");
		}
		
		
		return($arrMenuCat);
	}
	
	private function a_______DATA______(){}
	
	
	/**
	 * filter categories without web addons
	 */
	private function filterCatsWithoutWeb($arrCats){
		
		foreach($arrCats as $key=>$cat){
			$isweb = UniteFunctionsUC::getVal($cat, "isweb");
			$isweb = UniteFunctionsUC::strToBool($isweb);
			if($isweb == true)
				continue;
			
			$numWebAddons = UniteFunctionsUC::getVal($cat, "num_web_addons");
			if($numWebAddons == 0)
				unset($arrCats[$key]);
		}
		
		return($arrCats);
	}
	
	
	/**
	 * get categories with catalog
	 */
	private function getCatsWithCatalog($filterCatalog, $params = array()){
		
		$objAddons = new UniteCreatorAddons();
		$webAPI = $this->getWebAPI();
						
		$arrCats = $objAddons->getAddonsWidthCategories(true, true, $this->filterAddonType, $params);
		
		if(empty($params))
			$arrCats = $this->modifyLocalCats($arrCats);
			
		if($this->objAddonType->allowManagerWebCatalog == true)
			$arrCats = $webAPI->mergeCatsAndAddonsWithCatalog($arrCats, true, $this->objAddonType, $params);
		
		if($filterCatalog == self::FILTER_CATALOG_WEB)
			$arrCats = $this->filterCatsWithoutWeb($arrCats);
		
		return($arrCats);
	}
	
	
	/**
	 * modify local categories - create one if empty, and required
	 */
	protected function modifyLocalCats($arrCats){
		
		if(!empty($arrCats))
			return($arrCats);
					
		if($this->objAddonType->allowNoCategory == true)
			return($arrCats);
		
		//add default category
		$objCategory = new UniteCreatorCategory();
		$objCategory->addDefaultByAddonType($this->objAddonType);
		
		$arrCats = $this->objCats->getListExtra($this->objAddonType);
		
		return($arrCats);
	}
	
	/**
	 * clear uncategorized category
	 */
	private function getArrCats_clearUncategorized($arrCats){
				
		//modify categories, clear uncategorized if empty
		foreach($arrCats as $dir=>$cat){
			
			$isweb = UniteFunctionsUC::getVal($cat, "isweb");
			if($isweb === true)
				continue;
			
			$arrAddons = UniteFunctionsUC::getVal($cat, "addons");
			
			$catID = UniteFunctionsUC::getVal($cat, "id");
			if($catID === 0 && empty($arrAddons)){
				
				$numAddons = UniteFunctionsUC::getVal($cat, "num_addons");
				$numAddons = UniteFunctionsUC::strToBool($numAddons);
				
				if($numAddons == 0){
					unset($arrCats[$dir]);
					return($arrCats);
				}
				
			}
			
		}		
		
		return($arrCats);
	}
	
	/**
	 * get categories
	 */
	protected function getArrCats($params = array()){
		
		$filterCatalog = $this->getStateFilterCatalog();
		
		switch($filterCatalog){
			case self::FILTER_CATALOG_MIXED:
			case self::FILTER_CATALOG_WEB:
				$arrCats = $this->getCatsWithCatalog($filterCatalog, $params);
								
			break;
			default:	//installed type
				
				$filterSearch = UniteFunctionsUC::getVal($params, "filter_search");
				if(empty($filterSearch))
					$filterSearch = "";
				
				$filterSearch = trim($filterSearch);
				
				$catsParams = array();
				if(!empty($filterSearch))
					$catsParams["filter_search_addons"] = $filterSearch;
									
				$arrCats = $this->objCats->getListExtra($this->objAddonType, "","", false, $catsParams);
				
				$arrCats = $this->modifyLocalCats($arrCats);
				
			break;
		}
		
		//don't clear uncategorized at elements master
		$isClear = true;
		$addAll = false;
		if($this->objAddonType->typeName == GlobalsUnlimitedElements::ADDONSTYPE_ELEMENTOR_TEMPLATE && $this->objAddonType->allowWebCatalog == false){
			$isClear = false;
			$addAll = true;
		}

		if($addAll == true)
			$arrCats = $this->getCatList_addAllCategory($arrCats);
		
		if($isClear == true)
			$arrCats = $this->getArrCats_clearUncategorized($arrCats);
				
		return($arrCats);
	}
	
	/**
	 * add "all" category, for master templates
	 */
	private function getCatList_addAllCategory($arrCats){
		
		$arrCat = array();
		$arrCat["id"] = "all";
		$arrCat["title"] = __("All","unlimited-elements-for-elementor");
		$arrCat["alias"] = "";
		$arrCat["ordering"] = 0;
		$arrCat["params"] = "";
		$arrCat["type"] = "";
		$arrCat["num_addons"] = "";
		
		array_unshift($arrCats, $arrCat);
		
		return($arrCats);
	}
	
	/**
	 * get category list
	 */
	protected function getCatList($selectCatID = null, $arrCats = null, $params = array()){
		
		if($arrCats === null)
			$arrCats = $this->getArrCats($params);
				
		//check for error
		if(empty($arrCats)){
			
			$urlApiConnectivity = HelperUC::getViewUrl("troubleshooting-connectivity");
			
			HelperUC::addAdminNotice("No widgets fetched from the API. Please check <a href='$urlApiConnectivity'>api connectivity</a> from general settings - troubleshooting");
		}
		
		$htmlCatList = $this->objCats->getHtmlCatList($selectCatID, $this->objAddonType, $arrCats);
		
		return($htmlCatList);
	}
	
	
	/**
	 * get cat list from data
	 */
	public function getCatListFromData($data){
		
		$selectedCat = UniteFunctionsUC::getVal($data, "selected_catid");
		$filterActive = UniteFunctionsUC::getVal($data, "filter_active");
		$filterCatalog = UniteFunctionsUC::getVal($data, "filter_catalog");
				
		$typeDistinct = $this->objAddonType->typeNameDistinct;
		
		self::setStateFilterActive($filterActive, $typeDistinct);
		self::setStateFilterCatalog($filterCatalog, $typeDistinct);
		
		$htmlCats = $this->getCatList($selectedCat);
		
		$response = array();
		$response["htmlCats"] = $htmlCats;
		
		return($response);
	}
	
	
	/**
	 * get category settings from cat ID
	 */
	protected function getCatagorySettings(UniteCreatorCategory $objCat){
		
		$title = $objCat->getTitle();
		$alias = $objCat->getAlias();
		$params = $objCat->getParams();
		$catID = $objCat->getID();
		
		$settings = new UniteCreatorSettings();
		
		$settings->addStaticText("Category ID: <b>$catID</b>","some_name");
		$settings->addTextBox("category_title", $title, esc_html__("Category Title","unlimited-elements-for-elementor"));
		$settings->addTextBox("category_alias", $alias, esc_html__("Category Name","unlimited-elements-for-elementor"));
		$settings->addIconPicker("icon","",esc_html__("Category Icon", "unlimited-elements-for-elementor"));
		
		$settings = UniteProviderFunctionsUC::applyFilters(UniteCreatorFilters::FILTER_MANAGER_ADDONS_CATEGORY_SETTINGS, $settings, $objCat, $this->filterAddonType);
		
		$settings->setStoredValues($params);
		
		return($settings);
	}
	
	private function a______HEADER_LINE______(){}
	
	
	/**
	 * put catalog filters
	 */
	protected function putFiltersCatalog(){
		
		if(GlobalsUC::$enableWebCatalog == false)
			return(false);
		
		if($this->objAddonType->allowManagerWebCatalog == false)
			return(false); 
		
		//don't filter web catalog mode
		if($this->objAddonType->isWebCatalogMode == true)
			return(false);
		
		$classActive = "class='uc-active'";
		
		$filterCatalog = $this->filterCatalogState;
		
		$addParams = "";
		if($filterCatalog == self::FILTER_CATALOG_INSTALLED)
			$addParams = " checked='checked'";
		
		?>
			<div class="uc-filter-set-wrapper uc-filter-set-checkbox">
				<label>
					<input id="uc_filter_catalog_installed" type="checkbox" data-state_active="<?php echo self::FILTER_CATALOG_INSTALLED?>" data-state_notactive="<?php echo self::FILTER_CATALOG_MIXED?>" <?php echo $addParams?>>
					<?php _e("Show Only Installed", "unlimited-elements-for-elementor")?>
				</label>
			</div>
			
		<?php 
	}
	
	/**
	 * put search filter
	 */
	protected function putFilterSearch(){
				
		$textPlaceholder = __("Search...","unlimited-elements-for-elementor");
		
		?>			
			<div class="uc-filters-set-search">
							
				<input id="uc_manager_addons_input_search" class="uc-filter-search-input" type="text" placeholder="<?php echo $textPlaceholder?>">
				
				<i id="uc_manager_addons_icon_search" class="fa fa-search uc-icon-search" title="<?php _e("Search Widget","unlimited-elements-for-elementor")?>"></i>
				
				<a id="uc_manager_addons_clear_search" href="javascript:void(0)" onfocus="this.blur()" class="uc-filter-button-clear" title="<?php _e("Clear Search","unlimited-elements-for-elementor")?>" style="display:none" >
					<i class="fa fa-times uc-icon-clear"></i>
				</a>
			</div>
		
		<?php 
		
	}
	
	
	/**
	 * put items filters links
	 */
	private function putItemsFilters_active(){
		
		$classActive = "class='uc-active'";
		$filter = $this->filterActive;
		if(empty($filter))
			$filter = "all";
		
		//show only if installed
		$style = "style='display:none'";
		if($this->filterCatalogState == "installed")
			$style = "";
		
		$arrFilter = array();
		$arrFilter["all"] = __("Show all states", "unlimited-elements-for-elementor");
		$arrFilter["active"] = __("Active state only","unlimited-elements-for-elementor");
		$arrFilter["not_active"] = __("Not active state only","unlimited-elements-for-elementor");
		
		$htmlSelect = HelperHtmlUC::getHTMLSelect($arrFilter, $filter, "id='uc_manager_filter_active' class='uc-select-filter-active'", true);
		
		?>
		<div class="uc-filter-set-wrapper uc-filter-set-active" <?php echo $style?>>
			
			<?php echo $htmlSelect ?>
			
		</div>
		<?php 
	}
	
	
	/**
	 * put filters - function for override
	 */
	private function putHeaderLineFilters(){
		
		?>
		
		<div class="uc-items-filters">
		
			<?php 
				if($this->enableActiveFilter)
					$this->putItemsFilters_active();
			?>
			
			<?php $this->putFiltersCatalog()?>
			
			<?php 
				if($this->enableSearchFilter == true)
					$this->putFilterSearch();
			?>
			
			<?php $this->putShortcode()?>
			
			<div class="unite-clear"></div>
			
		</div>
		
		<?php 
	}
	
	/**
	 * put the header logo
	 */
	protected function putHtmlHeaderLogo(){
		
		$urlLogo = GlobalsUC::$urlPluginImages."logo_unlimited.svg";
		$logoWidth = "216";
		
		if(GlobalsUC::$isProVersion == true){
			$urlLogo = GlobalsUC::$urlPluginImages."logo_unlimited-pro.svg";
			$logoWidth = "256";
		}
		
		?>
			<img class="uc-manager-header-logo" src="<?php echo $urlLogo?>" width="<?php echo $logoWidth?>">
		<?php 
	}
	
	
	/**
	 * put html header line
	 * function for override
	 */
	protected function putHtmlHeaderLine(){
		
		
		?>
		<div class="uc-manager-header-line">
			
			<?php $this->putHtmlHeaderLogo()?>
			
			<?php if(!empty($this->headerLineText)):?>
			<div class="uc-manager-header-text">
				<?php echo $this->headerLineText?>
			</div>
			<?php endif?>
			
			<div class="uc-manager-header-filters">
				<?php $this->putHeaderLineFilters()?>
			</div>
			
			<div class="unite-clear"></div>
			
		</div>
		
		<?php 
		
	}

		/**
	 * put after buttons html
	 */
	protected function putHtmlAfterButtons(){
		
		if($this->enableEditGroup == false)
			return(false);
		
		?>
		 	<div id="uc_manager_group" class="uc-manager-group">
		 		
		 		<a href="javascript:void(0)" class="uc-manager-group-back"><?php _e("Back To Category","unlimited-elements-for-elementor")?></a>
		 		
		 		<div class="uc-manager-group-text"><?php _e("Template Kit","unlimited-elements-for-elementor")?></div>
		 				 		
		 	</div>
		
		<?php 
		
	}
	
	private function a______STATUS_LINE______(){}
	
	/**
	 * add copy panel to status line
	 * 
	 */
	protected function putStatusLineOperationsAdditions(){
		
		if($this->enableCopy == true):
		?>
		<div class="item_operations_wrapper uc-bottom-copypanel" style="display:none">
			
			 <?php _e("Copied", "unlimited-elements-for-elementor")?>: <span class="uc-copypanel-addon"></span>
			 
			 <a class="unite-button-secondary button-disabled uc-button-copypanel-move" href="javascript:void(0)"><?php _e("Move Here","unlimited-elements-for-elementor")?></a>
			 <a class="unite-button-secondary uc-button-copypanel-cancel" href="javascript:void(0)"><?php _e("Cancel")?></a>
		 </div>
		
		<?php 
		endif;
		
	}
	
	
	private function a______OTHERS______(){}
	
	
	
	/**
	 * get addon type object
	 */
	public function getObjAddonType(){
		
		return($this->objAddonType);
	}
	
	/**
	 * return if layouts or addons type
	 */
	public function getIsLayoutType(){
		$this->validateAddonType();
		
		return($this->isLayouts);
	}
	
	
	/**
	 * get no items text
	 */
	protected function getNoItemsText(){
		
		$text = $this->objAddonType->textNoAddons;

		UniteFunctionsUC::validateNotEmpty($text,"text addon type");
		
		return($text);
	}
	
	
	/**
	 * get html categories select
	 */
	protected function getHtmlSelectCats(){
		
		if($this->hasCats == false)
			UniteFunctionsUC::throwError("the function ");
		
		$htmlSelectCats = $this->objCats->getHtmlSelectCats($this->filterAddonType);
		
		return($htmlSelectCats);
	}
	
	
	/**
	 * put content to items wrapper div
	 */
	protected function putListWrapperContent(){
		$addonType = $this->filterAddonType;
		if(empty($addonType))
			$addonType = "default";
		
		$filepathEmptyAddons = GlobalsUC::$pathProviderViews."empty_addons_text_{$addonType}.php";
		if(file_exists($filepathEmptyAddons) == false)
			return(false);
		
		?>
		<div id="uc_empty_addons_wrapper" class="uc-empty-addons-wrapper" style="display:none">
			
			<?php include $filepathEmptyAddons?>
			
		</div>
		<?php 
	}
		
	/**
	 * put multiple buttons
	 */
	protected function putMultipleItemButtons(){
		?>
		 	<a data-action="remove_item" type="button" class="unite-button-secondary button-disabled uc-button-item uc-multiple-items"><?php esc_html_e("Delete","unlimited-elements-for-elementor")?></a>
		 	<a data-action="duplicate_item" type="button" class="unite-button-secondary button-disabled uc-button-item uc-multiple-items"><?php esc_html_e("Duplicate","unlimited-elements-for-elementor")?></a>
		
	 		<?php if($this->enableActiveFilter == true):?>
	 			
		 		<a data-action="activate_addons" type="button" class="unite-button-secondary button-disabled uc-button-item uc-notactive-item uc-multiple-items"><?php esc_html_e("Activate","unlimited-elements-for-elementor")?></a>
		 		<a data-action="deactivate_addons" type="button" class="unite-button-secondary button-disabled uc-button-item uc-active-item uc-multiple-items"><?php esc_html_e("Deactivate","unlimited-elements-for-elementor")?></a>
	 		
	 		<?php endif?>
		
		<?php 
	}
	
	
	/**
	 * put items buttons
	 */
	protected function putItemsButtons(){
		
		if($this->enableActions == false)
			return(false);
		
		$textImport = esc_html__("Import ","unlimited-elements-for-elementor") . $this->textPlural;
		$textEdit = esc_html__("Edit ","unlimited-elements-for-elementor") . $this->textSingle;
		$textTest = "Test ".$this->textSingle;
		
		?>
			
			<?php 
			 UniteProviderFunctionsUC::doAction(UniteCreatorFilters::ACTION_MANAGER_ITEM_BUTTONS1);
			?>
 			<a data-action="add_addon" type="button" class="unite-button-primary button-disabled uc-button-item uc-button-add"><?php echo esc_html($this->textAddAddon)?></a> 
 			<a data-action="import_addon" type="button" class="unite-button-secondary button-disabled uc-button-item uc-button-add"><?php echo esc_html($textImport)?></a>
 			
 			<?php 
				if($this->putItemButtonsType == "multiple"){
					$this->putMultipleItemButtons();
					return(false);
				}
 			?>
 			
			<?php 
			 	UniteProviderFunctionsUC::doAction(UniteCreatorFilters::ACTION_MANAGER_ITEM_BUTTONS2);
			?>
 				
		 		<a data-action="remove_item" type="button" class="unite-button-secondary button-disabled uc-button-item"><?php esc_html_e("Delete","unlimited-elements-for-elementor")?></a>
		 		<a data-action="edit_addon" type="button" class="unite-button-primary button-disabled uc-button-item uc-single-item"><?php echo esc_html($textEdit)?> </a>
		 		<a data-action="preview_addon" type="button" class="unite-button-secondary button-disabled uc-button-item uc-single-item"><?php esc_html_e("Preview", "unlimited-elements-for-elementor")?> </a>
	 			
		 		<?php if($this->showTestAddon):?>
		 		<a data-action="test_addon" type="button" class="unite-button-secondary button-disabled uc-button-item uc-single-item"><?php echo esc_html($textTest)?></a>
				<?php endif?>
				
				<?php 
				 UniteProviderFunctionsUC::doAction(UniteCreatorFilters::ACTION_MANAGER_ITEM_BUTTONS3);
				?>
			
				<?php if($this->enablePreview == true):?>
		 		
		 			<a data-action="preview_addon" type="button" class="unite-button-secondary button-disabled uc-button-item uc-single-item"><?php esc_html_e("Preview", "unlimited-elements-for-elementor")?> </a>
				
				<?php endif?>
							
	 		<?php if($this->enableActiveFilter == true):?>
	 			
		 		<a data-action="activate_addons" type="button" class="unite-button-secondary button-disabled uc-button-item uc-notactive-item"><?php esc_html_e("Activate","unlimited-elements-for-elementor")?></a>
		 		<a data-action="deactivate_addons" type="button" class="unite-button-secondary button-disabled uc-button-item uc-active-item"><?php esc_html_e("Deactivate","unlimited-elements-for-elementor")?></a>
	 		
	 		<?php endif?>
	 		
	 		<?php if($this->enableMakeScreenshots == true):?>
	 		
	 		<a data-action="make_screenshots" type="button" class="unite-button-secondary button-disabled uc-button-item uc-single-item"><?php esc_html_e("Make Thumb", "unlimited-elements-for-elementor")?> </a>
	 		<a data-action="make_screenshots" type="button" class="unite-button-secondary button-disabled uc-button-item uc-multiple-items"><?php esc_html_e("Make Thumbs", "unlimited-elements-for-elementor")?> </a>
	 		
	 		<?php endif?>
		<?php
	}
	
	/**
	 * get current layout shortcode template
	 */
	protected function getShortcodeTemplate(){
		
		$shortcodeTemplate = "{blox_page id=%id% title=\"%title%\"}";
		
		return($shortcodeTemplate);
	}
	
	
	/**
	 * put shortcode in the filters area
	 */
	protected function putShortcode(){
	
		if($this->objAddonType->enableShortcodes == false)
			return(false);
		
		$shortcodeTemplate = $this->getShortcodeTemplate();
		$shortcodeTemplate = htmlspecialchars($shortcodeTemplate);
		
		?>
		<div class="uc-single-item-related">
			<div class="uc-filters-set-title"><?php esc_html_e("Shortcode", "unlimited-elements-for-elementor")?>:</div>
			<div class="uc-filters-set-content"> <input type="text" readonly class="uc-filers-set-shortcode" data-template="<?php echo esc_attr($shortcodeTemplate)?>" value=""></div>
		</div>
		
		<?php 
		
	}
	
	
	/**
	 * get category settings html
	 */
	public function getCatSettingsHtmlFromData($data){
		
		$catID = UniteFunctionsUC::getVal($data, "catid");
		UniteFunctionsUC::validateNotEmpty($catID, "category id");
		
		$objCat = new UniteCreatorCategory();
		$objCat->initByID($catID);
		
		$settings = $this->getCatagorySettings($objCat);
		
		$output = new UniteSettingsOutputWideUC();
		$output->init($settings);
		
		ob_start();
		$output->draw("uc_category_settings");
		
		$htmlSettings = ob_get_contents();
		
		ob_end_clean();
		
		$response = array();
		$response["html"] = $htmlSettings;
		
		return($response);
	}
	
	/**
	 * 
	 * get properties html from data
	 */
	public function getAddonPropertiesDialogHtmlFromData($data){
		
		if($this->objAddonType->isLayout == false)
			UniteFunctionsUC::throwError("The addon type should be layouts for props");
		
		$layoutID = UniteFunctionsUC::getVal($data, "id");
		$objLayout = new UniteCreatorLayout();
		$objLayout->initByID($layoutID);
		
		$settings = $objLayout->getPageParamsSettingsObject();
		
		$htmlSettings = HelperHtmlUC::drawSettingsGetHtml($settings,"settings_addon_props");
		
		$output = array();
		$output["html"] = $htmlSettings;
		
		return($output);
	}
	
	
	
	
	

	/**
	 * put scripts
	 */
	private function putScripts(){
		
		$arrPlugins = UniteProviderFunctionsUC::applyFilters(UniteCreatorFilters::FILTER_MANAGER_ADDONS_PLUGINS, array());
				
		$script = "
			var g_ucManagerAdmin;
			
			jQuery(document).ready(function(){
				var selectedCatID = \"{$this->selectedCategory}\";
				g_ucManagerAdmin = new UCManagerAdmin();";
		
		if(!empty($arrPlugins)){
			foreach($arrPlugins as $plugin)
				$script .= "\n				g_ucManagerAdmin.addPlugin('{$plugin}');";
		}
		
		$script .= "
				g_ucManagerAdmin.initManager(selectedCatID);
			});
		";
		
		
		UniteProviderFunctionsUC::printCustomScript($script);
	}
	
	
	/**
	 * put preview tooltips
	 */
	protected function putPreviewTooltips(){
		?>
		<div id="uc_manager_addon_preview" class="uc-addon-preview-wrapper" style="display:none"></div>
		<?php 
	}
	
	/**
	 * get single item menu
	 */
	protected function getMenuSingleItemActions(){
		
		$arrMenuItem = array();
		$arrMenuItem["edit_addon_blank"] = esc_html__("Edit In New Tab","unlimited-elements-for-elementor");
		
		if($this->enableEditGroup)
			$arrMenuItem["edit_layout_group"] = esc_html__("Edit Template Kit","unlimited-elements-for-elementor");
		
		if($this->enableViewThumbnail)
			$arrMenuItem["preview_thumb"] = esc_html__("View Thumbnail","unlimited-elements-for-elementor");
		
		if($this->enableMakeScreenshots)
			$arrMenuItem["make_screenshots"] = esc_html__("Make Thumbnail","unlimited-elements-for-elementor");
		
		$arrMenuItem["quick_edit"] = esc_html__("Quick Edit","unlimited-elements-for-elementor");
		
		if($this->enableCopy == true)
			$arrMenuItem["copy"] = esc_html__("Copy","unlimited-elements-for-elementor");
		
		$arrMenuItem["remove_item"] = esc_html__("Delete","unlimited-elements-for-elementor");
		
		if($this->showTestAddon){
			$arrMenuItem["test_addon"] = esc_html__("Test ","unlimited-elements-for-elementor").$this->textSingle;
			$arrMenuItem["test_addon_blank"] = esc_html__("Test In New Tab","unlimited-elements-for-elementor");
		}	
		
		$arrMenuItem["export_addon"] = esc_html__("Export ","unlimited-elements-for-elementor").$this->textSingle;
		
		return($arrMenuItem);
	}
	
	
	/**
	 * put single item actions menu
	 */
	private function putMenuSingleItemActions(){
		
		$arrMenuItem = $this->getMenuSingleItemActions();
		
		if(!is_array($arrMenuItem))
			$arrMenuItem = array();
		
		$this->putRightMenu($arrMenuItem, "rightmenu_item_actions", "single_item_actions");
		
	}
	
	
	/**
	 * put additional html here
	 */
	protected function putAddHtml(){
		
		$this->putDialogQuickEdit();
		$this->putDialogAddAddon();
		$this->putDialogAddonProperties();
		$this->putDialogImportAddons();
		$this->putDialogPreviewAddons();
		
		$this->putMenuSingleItemActions();
		
		if($this->putDialogDebug == true)
			$this->putDialogDebug();
		
		if($this->isWebCatalogMode == true)
			$this->putPreviewTemplateDialog();
		
		if($this->showAddonTooltip)
			$this->putPreviewTooltips();
		
		$this->putScripts();
	}
	
	
	/**
	 * put init items, will not run, because always there are cats
	 */
	protected function putInitItems(){
		
		if($this->hasCats == true)
			return(false);
		
		$htmlAddons = $this->getCatAddonsHtml(null);
		
		echo $htmlAddons;
	}
	
	
	/**
	 * 
	 * set the custom data to manager wrapper div
	 */
	protected function onBeforePutHtml(){
				
		$addonsType = $this->objAddonType->typeNameDistinct;
		
		$addHTML = "data-addonstype=\"{$addonsType}\"";
		
		$this->setManagerAddHtml($addHTML); 
	}
	
		
	
}