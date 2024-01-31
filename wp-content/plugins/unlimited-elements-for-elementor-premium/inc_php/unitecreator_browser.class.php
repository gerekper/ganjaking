<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

class UniteCreatorBrowserWork extends HtmlOutputBaseUC{
	
	private $selectedCatNum = "";
	private $isPages = false;
	
	private $browserID = "";
	private $addonType = "", $objAddonType;
	
	private $isMultipleAddonTypes = false;
	private $arrAddonTypes = array();
	
	private $webAPI, $prefix;
	private $arrIcons;
	
	private static $isPutOnce_catalogUpdate = false;
	private static $serial = 0;
	
	protected $hasCategories = true, $hasHeader = true, $isSelectMode = false, $putAddonTitle = false;
	protected $isFromWebOnly = false, $addEmptyItem = false;
	
	protected $textBuy, $textHoverProAddon, $urlBuy, $product;
	protected $urlPreviewTemplate;
	
	
	const STATE_INSTALLED = "installed";
	const STATE_FREE = "free";
	const STATE_PRO = "pro";
	
	
	/**
	 * constructor
	 */
	public function __construct(){
		
		$this->webAPI = new UniteCreatorWebAPI();
		$this->textBuy = esc_html__("Buy PRO", "unlimited-elements-for-elementor");
		$this->textHoverProAddon = esc_html__("This addon is available<br>for Blox PRO users only.", "unlimited-elements-for-elementor");
		$this->urlBuy = GlobalsUC::URL_BUY;
		
		self::$serial++;
		$this->prefix = UniteFunctionsUC::getRandomString(5).self::$serial;
		
	}
	
	/**
	 * set id
	 */
	public function setBrowserID($id){
		
		$this->browserID = $id;
	}
	
	/**
	 * set product
	 */
	public function setProduct($product){
		
		$this->product = $product;
	}
	
	
	private function a_INIT(){}
	
	/**
	 * init by shape devider type
	 */
	protected function initSettingsByAddonType_shapeDivider(){
		
		$this->hasCategories = false;
		$this->hasHeader = false;
		$this->isSelectMode = true;
		$this->putAddonTitle = true;
		$this->addEmptyItem = true;
	}
	
	
	/**
	 * init by layout addon type
	 */
	protected function initSettingsByAddonType_layout(){
		
		$this->isPages = true;
				
		if($this->objAddonType->isBasicType == true)
			$this->isFromWebOnly = true;
	}
	
	
	/**
	 * init by general addon type
	 */
	protected function initByGeneralAddonType(){
		
		if($this->objAddonType->browser_addEmptyItem == true)
			$this->addEmptyItem = true;
		
	}
	
	
	/**
	 * init settings by addon type
	 */
	protected function initSettingsByAddonType(){
				
		if(empty($this->addonType))
			return(false);

		
		if($this->objAddonType->isLayout == true){
			
			$this->initSettingsByAddonType_layout();
			return(false);
		}
		
		//init by shape devider
		switch($this->addonType){
			case GlobalsUC::ADDON_TYPE_SHAPE_DEVIDER:
				$this->initSettingsByAddonType_shapeDivider();
			break;
			default:
				$this->initByGeneralAddonType();
			break;
		}
		
		//set texts if available
		if(!empty($this->objAddonType->browser_textBuy))
			$this->textBuy = $this->objAddonType->browser_textBuy;
		
		if(!empty($this->objAddonType->browser_textHoverPro))
			$this->textHoverProAddon = $this->objAddonType->browser_textHoverPro;
		
		if(!empty($this->objAddonType->browser_urlBuyPro))
			$this->urlBuy = $this->objAddonType->browser_urlBuyPro;
		
		if(!empty($this->objAddonType->browser_urlPreview))
			$this->urlPreviewTemplate = $this->objAddonType->browser_urlPreview;
		
	}
	
	
	/**
	 * set browser addon type
	 */
	public function initAddonType($addonType){
		
		if(is_array($addonType)){
			
			//init by multiple types
			
			UniteFunctionsUC::validateNotEmpty($addonType,"multiple addon types");
			
			
			$this->isMultipleAddonTypes = true;
			foreach($addonType as $type){
				
				$objAddonType = UniteCreatorAddonType::getAddonTypeObject($type);
				$this->arrAddonTypes[$type] = $objAddonType;
				$this->addonType = $addonType;
				
				if($type == GlobalsUC::ADDON_TYPE_REGULAR_ADDON)
					$this->objAddonType = $objAddonType;
			}
			
			if(empty($this->objAddonType))
				$this->objAddonType = $objAddonType;
						
		}else{		//init by single
			
			$this->objAddonType = UniteCreatorAddonType::getAddonTypeObject($addonType);
		}
			
		$this->addonType = $this->objAddonType->typeNameDistinct;
		
		$this->initSettingsByAddonType();
		
	}
	
	
	private function a_________GETTERS_________(){}
	
	/**
	 * get icons array
	 */
	private function getArrIcons(){
		
		if(!empty($this->arrIcons))
			return($this->arrIcons);
		
		$arrIcons = array();
		
		$arrIcons["search"] = "fas fa-search";
		$arrIcons["close"] = "fas fa-times";
		$arrIcons["download"] = "fas fa-download";
		$arrIcons["cloud_download"] = "fas fa-cloud-download";
		$arrIcons["lock"] = "fas fa-lock";
		$arrIcons["spinner"] = "fas fa-spinner";
				
		$this->arrIcons = $arrIcons;
		
		return($arrIcons);
	}
	
	
	private function getIcon($name){
		
		if(empty($this->arrIcons))
			$this->getArrIcons();
		
		$icon = UniteFontManagerUC::getIcon($name, $this->arrIcons);
		
		return($icon);
	}
	
	
	/**
	 * is product active
	 */
	private function isProductActive(){
				
		$isProductActive = $this->webAPI->isProductActive($this->product);
		
		return($isProductActive);
	}
	
	
	/**
	 * get html tabs header
	 */
	private function getHtmlCatalogHeader(){
		
		$html = "";
		
		$textBlox = esc_html__("Blox", "unlimited-elements-for-elementor");
		$textShowAll = esc_html__("Show All", "unlimited-elements-for-elementor");
		$textInstalled = esc_html__("Installed", "unlimited-elements-for-elementor");
		$textFree = esc_html__("Free", "unlimited-elements-for-elementor");
		$textPro = esc_html__("Pro", "unlimited-elements-for-elementor");
		
		$textBuy = $this->textBuy;
		
		$textAlreadyBought = esc_html__("Already bought Blox PRO?", "unlimited-elements-for-elementor");
		$textTheProductActive = esc_html__("The product is Active!", "unlimited-elements-for-elementor");
		$textDeactivate = esc_html__("Deactivate", "unlimited-elements-for-elementor");
		$textCheckUpdate = esc_html__("Check Catalog Update", "unlimited-elements-for-elementor");
		$textClear = esc_html__("Clear", "unlimited-elements-for-elementor");
				
		$urlBuy = $this->urlBuy;
		
		$isProductActive = $this->isProductActive();
		
		
		$htmlAccount = "";
		if($isProductActive == false){
			$htmlAccount = "
			 <div class='uc-header-gotopro'>
			      <a href='javascript:void(0)' class='uc-link-activate-pro'>{$textAlreadyBought}</a>
			      <a href='{$urlBuy}' target='_blank' class='uc-button-buy-pro'>{$textBuy}</a>
			 </div>
		";
		}
		else{		//product is active
			$htmlAccount = "
			<div class='uc-header-gotopro'>
				<span class='uc-catalog-active-text'>{$textTheProductActive}</span>
				<a href='javascript:void(0)' class='uc-link-deactivate'>{$textDeactivate}</a>
			</div>
			";
		}
		
		$iconSearch = $this->getIcon("search");
		$iconClose = $this->getIcon("close");
		$iconDownload = $this->getIcon("download");
		
		
		$html .= "<div class='uc-catalog-header unite-inputs unite-clearfix'>

		 		<div class='uc-catalog-logo'></div>
	    		<div class='uc-catalog-search'>
					<i class='{$iconSearch}' aria-hidden='true'></i> &nbsp;
	    			<input class='uc-catalog-search-input' type='text'>
	    			<a href='javascript:void(0)' class='unite-button-secondary button-disabled uc-catalog-search-clear' style='display:none;'>{$textClear}</a>
	    		</div>
	    		
	    		<div class='uc-catalog-header-menu'>
	     			<a href='javascript:void(0)' class='uc-menu-active' onfocus='this.blur()' data-state='all'>{$textShowAll}</a>
	      	  		<a href='javascript:void(0)' onfocus='this.blur()' data-state='installed'>{$textInstalled}</a>
	      	  		<a href='javascript:void(0)' onfocus='this.blur()' data-state='free'>{$textFree}</a>
	       	 		<a href='javascript:void(0)' onfocus='this.blur()' data-state='pro'>{$textPro}</a>
				</div>
								
		   	 	<a href='javascript:void(0)' onfocus='this.blur()' class='uc-catalog-button-close'>
		   	 		<i class='{$iconClose}' aria-hidden='true'></i>
			 	</a>
				
				<a class='uc-link-update-catalog' title='{$textCheckUpdate}' href='javascript:void(0)' onfocus='this.blur()'><i class='{$iconDownload}' aria-hidden='true'></i></a>
			 	
			 	{$htmlAccount}
			 	
		</div>";
		
		return($html);
	}
	
	
	
	/**
	 * get tabs html
	 */
	private function getHtmlTabs($arrCats){
		
		$html = "";
				
		$numCats = count($arrCats);
		
		$addHtml = "";
				
		$isFirst = true;
		
		$counter = 0;
		$totalItems = 0;
		$htmlTabs = "";
		foreach($arrCats as $catTitle=>$cat){
			
			if($this->isPages == false)
				$arrItems = UniteFunctionsUC::getVal($cat, "addons");
			else
				$arrItems = $cat;
							
			$numItems = 0;
			if(!empty($arrItems)){
				$numItems = count($arrItems);
				$totalItems += $numItems;
			}
						
			$counter++;
			
			if(empty($this->selectedCatNum) && $isFirst == true){
				$isFirst = false;
				$this->selectedCatNum = $counter;
			}
			
			$isSelected = false;
			if($this->selectedCatNum === $counter)
				$isSelected = true;
			
			if(empty($catTitle))
				$catTitle = UniteFunctionsUC::getVal($cat, "title");
			
			if(empty($catTitle)){
				$id = UniteFunctionsUC::getVal($cat, "id");
				if(empty($id))
					$id = $counter;
				
				$catTitle = esc_html__("Category", "unlimited-elements-for-elementor")." {$id}";
			}
			
			$catShowTitle = $catTitle;
			
			if(!empty($numItems))
				$catShowTitle .= " ($numItems)";
			
			$catTitle = htmlspecialchars($catTitle);
			$catShowTitle = htmlspecialchars($catShowTitle);
			
			$addClass = "";
			if($isSelected == true)
				$addClass = " uc-tab-selected";
			
			$htmlTabs .= self::TAB5."<div class='uc-tab-item' data-catid='{$counter}' data-title='{$catTitle}'><a href=\"javascript:void(0)\" onfocus=\"this.blur()\" class=\"uc-browser-tab{$addClass}\" data-catid=\"{$counter}\">{$catShowTitle}</a></div>".self::BR;
		}

		$htmlTitleCategories = esc_html__("Categories", "unlimited-elements-for-elementor");
		if(!empty($totalItems))
			$htmlTitleCategories .= " ($totalItems)";
		
		
		$html .= self::TAB3."<div class=\"uc-browser-tabs-wrapper\" {$addHtml}>".self::BR;
		
		$html .= self::TAB3."	<div class='uc-browser-tabs-heading'>{$htmlTitleCategories}</div>".self::BR;
				
		$html .= $htmlTabs;
		
		$html .= self::TAB3."<div class='unite-clear'></div>".self::BR;
		
		
		$html .= self::TAB3."</div>".self::BR;	//end tabs
				
		return($html);
	}

	
	/**
	 * put html content with cats
	 */
	private function getHtmlContent_cats($arrCats){
		
		$html = "";
				
		$numCats = count($arrCats);
		
		//output addons
		$counter = 0;
		foreach($arrCats as $catTitle => $cat){
			
			$counter++;
			
			$title = UniteFunctionsUC::getVal($cat, "title");
			
			if($this->isPages == true)
				$title = $catTitle;
						
			$title = htmlspecialchars($title);
			
			$style = " style=\"display:none\"";
			if($counter === $this->selectedCatNum || $numCats <= 1)
				$style = "";
			
			if($this->isPages == true)
				$arrItems = $cat;
			else
				$arrItems = UniteFunctionsUC::getVal($cat, "addons");
			
			$prefix = $this->prefix;
			$contentID = "uc_browser_content_{$prefix}_{$counter}";
			
			$html .= self::TAB3."<div id=\"{$contentID}\" class=\"uc-browser-content\" data-cattitle='{$title}' {$style} >".self::BR;
			
			if(empty($arrItems)){
				
				if($this->isPages == false)
					$html .= esc_html__("No addons in this category", "unlimited-elements-for-elementor");
				else 
					$html .= esc_html__("No pages in this category", "unlimited-elements-for-elementor");
				
			}
			else{
				
				if(is_array($arrItems) == false)
					UniteFunctionsUC::throwError("The cat addons array should be array");
				
				foreach($arrItems as $name=>$item){
										
					if($this->isPages == true)
						$item["name"] = $name;
					
					//$htmlItem = "";
					$htmlItem = $this->getHtmlItem($item);
				
					$html .= $htmlItem;
				}
				
			}
			
				$html .= self::TAB2."<div class='unite-clear'></div>".self::BR;
			
			$html .= self::TAB3."</div>".self::BR;	//tab content
			
		}	//end foreach
				
		return($html);
	}
	
	
	/**
	 * get html content by addons list
	 */
	private function getHtmlContent_addons($arrItems){
		
		$html = "";
		
		$html .= self::TAB3."<div class=\"uc-browser-content uc-content-nocats\" >".self::BR;
		
		if($this->hasCategories == false && $this->addEmptyItem == true){
			$htmlEmptyItem = $this->getHtmlEmptyItem();
			$html .= $htmlEmptyItem;
		}
		
		foreach($arrItems as $name=>$item){
			
			if($this->isPages == true)
				$item["name"] = $name;
			
			//$htmlItem = "";
			$htmlItem = $this->getHtmlItem($item);
		
			$html .= $htmlItem;
		}
		
		$html .= self::TAB3."</div>".self::BR;	//tab content
		$html .= self::TAB2."<div class='unite-clear'></div>".self::BR;
		
		return($html);
	}
	
	
	/**
	 * get content html
	 */
	private function getHtmlContent($arrCats){
		
		$html = "";
			
		$html .= self::TAB2."<div class=\"uc-browser-content-wrapper\">".self::BR;
		
		if($this->hasCategories == true)
			$html .= $this->getHtmlContent_cats($arrCats);
		else
			$html .= $this->getHtmlContent_addons($arrCats);
		
		$html .= self::TAB2."</div>".self::BR; //content wrapper
		
		return($html);
	}
	
	
	/**
	 * check if the web addon is free
	 */
	public function isWebAddonFree($addon){
		
		$isActive = $this->isProductActive();
		
		if($isActive == true)
			return(true);
		
		$isFree = UniteFunctionsUC::getVal($addon, "isfree");
		$isFree = UniteFunctionsUC::strToBool($isFree);
		
		return($isFree);
	}
	
	/**
	 * get url preview of the addon
	 */
	private function getAddonUrlPreview($addon){
		
		if(is_array($addon) == false)
			return("");
		
		//set by preview site url
		$isWeb = UniteFunctionsUC::getVal($addon, "isweb");
		$previewPage = UniteFunctionsUC::getVal($addon, "preview");
		
		if(!empty($previewPage) && $isWeb == true){
			$urlPreview = GlobalsUC::URL_PREVIEW_WIDGETS.$previewPage;	
			return($urlPreview);
		}
		
		//set by preview template
		if(empty($this->urlPreviewTemplate))
			return("");
		
		$urlPreview = $this->urlPreviewTemplate;
		$addonName = UniteFunctionsUC::getVal($addon, "name");
		
		$urlPreview = str_replace("[name]", $addonName, $urlPreview);
		$urlPreview = htmlspecialchars($urlPreview);			
		
		return($urlPreview);
	}
	
	/**
	 * get catalog addon state html
	 */
	public function getCatalogAddonStateData($state, $isPage = false, $urlPreview = null, $addon = array(),$options = array()){
        
		
		$addonHref = "javascript:void(0)";
        $linkAddHtml = "";
        $htmlState = "";
        
		$isWebCatalogMode = UniteFunctionsUC::getVal($options, "web_catalog_mode");
		$isWebCatalogMode = UniteFunctionsUC::strToBool($isWebCatalogMode);
        
        $urlBuy = $this->urlBuy;
        
		$output = array();
		$output["html_state"] = "";
		$output["html_additions"] = "";
		$output["addon_href"] = "javascript:void(0)";
		$output["link_addhtml"] = "";
		$output["state"] = $state;
		
		$textItem = __("widget","unlimited-elements-for-elementor");
		$textItemHigh = $this->objAddonType->textSingle;
		$textItemSmall = strtolower($textItemHigh);
		
		
		if($isPage){
			$textItem = "template";
		}
		
		$iconCloudDownload = $this->getIcon("cloud_download");
		$iconLock = $this->getIcon("lock");
		$actionPreview = __("Preview", "unlimited-elements-for-elementor");
		
		$showState = true;
		$showAdditions = true;
		
		$urlBuyAttributes = "target='_blank'";
		
        //installed
        switch($state){
        	case self::STATE_FREE:
        		$label = 'free';
        		$labelText = __('Free',"unlimited-elements-for-elementor");
        		$hoverText = __("This ","unlimited-elements-for-elementor"). $textItemHigh .__(" Is Free","unlimited-elements-for-elementor")."<br>".__("To use it click install","unlimited-elements-for-elementor");
        		$hoverIcon = '<i class="'.$iconCloudDownload.'" aria-hidden="true"></i>';
        		$action = "Install";
        		
        		if(GlobalsUC::$isProductActive){
        			$labelText = __('Web',"unlimited-elements-for-elementor");
        			$hoverText = __("You can install this ","unlimited-elements-for-elementor").$textItem ."<br>".__("To use it click install","unlimited-elements-for-elementor");
        		}
        		
        	break;
        	case self::STATE_PRO:
				
        		$isProNewPage = $this->objAddonType->browser_buyProNewPage;
        		
        		if($isProNewPage == false)
        			$urlBuyAttributes = "";
        		
        		$label = 'pro';
        		$labelText = __('Pro',"unlimited-elements-for-elementor");
        		
        		$hoverText = $this->textHoverProAddon;
        		
        		$hoverIcon = '<i class="'.$iconLock.'" aria-hidden="true"></i>';
        		$action = $this->textBuy;
        		$addonHref = $this->urlBuy;
        		$linkAddHtml = " target='_blank'";
        	break;
        	default:
        		return($output);
        	break;
        }
        
        //add pages data
        if($isPage == true){
        	
        	if(!empty($urlPreview)){
        		$urlPreview = htmlspecialchars($urlPreview);
        		$hoverText .= " <br><a class='uc-hover-label-preview' href='{$urlPreview}' target='_blank' >View Page Demo</a>";
        	}
        	
        	//disable the sate and the additions on web catalog mode
	        if(GlobalsUC::$isProductActive && $isWebCatalogMode == true){
	        	$showState = false;
	        }
	        
	        if($isWebCatalogMode == true)
	        	$showAdditions = false;
        	        	
        }
        
        if($showState == true){

        	$addStateClass = "";
        	if(UniteCreatorManagerAddons::$stateLabelCounter > 0)
        	$addStateClass = "uc-state-label".UniteCreatorManagerAddons::$stateLabelCounter;
        	
			$htmlState = "<div class='uc-state-label uc-state-{$label} $addStateClass'>
				<div class='uc-state-label-text'>{$labelText}</div>
			</div>";			
        	
        	UniteCreatorManagerAddons::$stateLabelCounter++;
			
        }
        
		//make html additions
		
		// set addon preview url
		
        if(empty($urlPreview))
        	$urlPreview = $this->getAddonUrlPreview($addon);
        
		$htmlAdditions = "";
				
		if($showAdditions == true){
			
			$htmlAdditions .= "<div class='uc-hover-label uc-hover-{$label} hidden'>
						{$hoverIcon}
						<div class='uc-hover-label-text'>{$hoverText}</div>
						<a href='{$urlBuy}' $urlBuyAttributes class=\"uc-addon-button uc-button-{$label}\">{$action}</a>
						<a href='$urlPreview' target='_blank' class=\"uc-addon-button uc-button-preview uc-button-preview-{$label}\">{$actionPreview}</a>
					</div>";
			
			$textInstalling = esc_html__("Installing", "unlimited-elements-for-elementor");
			
			$iconSpinner = $this->getIcon("spinner");
			
			$htmlAdditions .= "<div class='uc-installing' style='display:none'>
						   <div class='uc-bar'></div>
						   <i class='{$iconSpinner} fa-spin fa-3x fa-fw'></i>
						   <span>{$textInstalling}...</span>
						   <h3 style='display:none'></h3>
					  </div>";
		}
		
		
		//add success message
		if($isPage){
			
			$textInstalled = "Page installed successfully<br> refreshing...";
			
			$htmlAdditions .= "<div class='uc-installed-success' style='display:none'>
						   <span>{$textInstalled}...</span>
					  </div>";
		}
		
		
		$output["html_state"] = $htmlState;
		$output["html_additions"] = $htmlAdditions;
		$output["addon_href"] = $addonHref;
		$output["link_addhtml"] = $linkAddHtml;
		
		return($output);
	}
	
	
	/**
	 * get addon html
	 * @param $addon
	 */
	private function getHtmlItem($arrItem){
		
		$html = "";
		
		if($this->isFromWebOnly == true)
			$isFromWeb = true;
		else{
			$isFromWeb = UniteFunctionsUC::getVal($arrItem, "isweb");
			$isFromWeb = UniteFunctionsUC::strToBool($isFromWeb);
		}
		
		
		if($isFromWeb == true)
			$isFree = $this->isWebAddonFree($arrItem);
		
		/*
		if($isFromWeb == false){
			dmp($arrItem);
			exit();
		}
		*/
				
		$name = UniteFunctionsUC::getVal($arrItem,"name");		
		$alias = UniteFunctionsUC::getVal($arrItem, "alias");
		$cat = UniteFunctionsUC::getVal($arrItem, "cat");
		
		
		if(!empty($alias) && $this->isPages == false && $isFromWeb == false)
			$name = $alias;	
		
		$name = UniteFunctionsUC::sanitizeAttr($name);
		
		$title = UniteFunctionsUC::getVal($arrItem, "title");
		$title = UniteFunctionsUC::sanitizeAttr($title);
				
		$paramImage = "preview";
		if($isFromWeb == true){
			$paramImage = "image";
		}
				
		$urlPreviewImage = UniteFunctionsUC::getVal($arrItem, $paramImage);
		$urlPreviewImage = UniteFunctionsUC::sanitizeAttr($urlPreviewImage);
		
		$id = UniteFunctionsUC::getVal($arrItem, "id");
		
		
		//get state
		$state = self::STATE_INSTALLED;
		
		if($isFromWeb){
			
			if($isFree == true)
				$state = self::STATE_FREE;
			else
				$state = self::STATE_PRO;
		}
        
		$urlItemPreview = null;
		
		if($this->isPages == true){
			
			$urlItemPreview = UniteFunctionsUC::getVal($arrItem, "url");
		}
		
		$stateData = $this->getCatalogAddonStateData($state, $this->isPages, $urlItemPreview);
				
		$addonHref = $stateData["addon_href"];
		$linkAddHtml = $stateData["link_addhtml"];
		
		$classAdd = "";
		if($isFromWeb == true)
			$classAdd = "uc-web-addon";
		
		if($this->putAddonTitle == true)
			$linkAddHtml .= " title='{$title}'";
		
		$addParams = "";
		if($this->hasCategories == false && !empty($cat)){
			$cat = htmlspecialchars($cat);
			$addParams = " data-cattitle=\"{$cat}\"";
		}
		
		$html .= self::TAB4."<div class=\"uc-browser-addon uc-addon-thumbnail {$classAdd}\" href=\"{$addonHref}\" {$linkAddHtml} data-state=\"{$state}\" data-id=\"$id\" data-name=\"{$name}\" data-title=\"{$title}\" $addParams>".self::BR;
		
		if($state != self::STATE_INSTALLED){
			$html .= $stateData["html_state"];
		}
				
		$bgImageStyle = "background-image:url('{$urlPreviewImage}')";
		
		$html .= self::TAB6."<div class=\"uc-browser-addon-image\" style=\"{$bgImageStyle}\"></div>".self::BR;
		$html .= self::TAB6."<div class=\"uc-browser-addon-title\">{$title}</div>".self::BR;
		
		
		if($state != self::STATE_INSTALLED){
			$html .= $stateData["html_additions"];
		}
		
		$html .= self::TAB4."</div>".self::BR;
	
	
		return($html);
	}
	
	/**
	 * get empty item
	 */
	protected function getHtmlEmptyItem(){
		
		$state = self::STATE_INSTALLED;
		$title = esc_html__("Not Selected", "unlimited-elements-for-elementor");
		
		$html = "";
		$html .= self::TAB4."<div class=\"uc-browser-addon uc-addon-thumbnail\" href=\"javascript:void(0)\" data-state=\"{$state}\">".self::BR;
		$html .= self::TAB6."<div class=\"uc-browser-addon-empty-title\">{$title}</div>".self::BR;
		$html .= self::TAB4."</div>".self::BR;
		
		
		return($html);
	}
	
	
	private function a__________OPERATIONS_________(){}
	
	
	/**
	 * sort catalog items
	 */
	public function sortCatalogItems($key1, $key2){
		
		if(strtolower($key1) == "basic")
			return(-1);
		
		if(strtolower($key2) == "basic")
			return(1);
		
		return strcmp($key1, $key2);
	}

	
	/**
	 * sort the categories
	 */
	private function sortCatalog($arrCats){
				
		uksort($arrCats, array($this,"sortCatalogItems"));
				
		return($arrCats);
	}
	
	
	/**
	 * remove empty cats
	 */
	private function removeEmptyCatalogCats($arrCats){
		
		foreach($arrCats as $key=>$cat){
			
			$addons = UniteFunctionsUC::getVal($cat, "addons");
			if(empty($addons))
				unset($arrCats[$key]);			
		}
		
		return($arrCats);
	}
		
	private function a_______DATA______(){}
	
	/**
	 * get addons items
	 */
	private function getArrCats_addons(){
				
		
		//get categories
		$objAddons = new UniteCreatorAddons();
		$arrCats = $objAddons->getAddonsWidthCategories(true, true, $this->addonType);
		
		if($this->hasCategories == false){
			
			$arrAddons = $objAddons->getCatAddons(null, true, true, $this->addonType, true);
			
			if(GlobalsUC::$enableWebCatalog == true)
				$arrAddons = $this->webAPI->mergeAddonsWithCatalog($arrAddons, $this->objAddonType);
			
			//merge addons with catalog
			return($arrAddons);
		}
		
		if($this->isMultipleAddonTypes == true){
			
			$arrCats = array();
			
			foreach($this->arrAddonTypes as $type => $objType){
				
				$arrCatsType = $objAddons->getAddonsWidthCategories(true, true, $type);
				if(GlobalsUC::$enableWebCatalog == true)
					$arrCatsType = $this->webAPI->mergeCatsAndAddonsWithCatalog($arrCatsType, false, $objType);

				if($type = GlobalsUC::ADDON_TYPE_REGULAR_ADDON)
					$arrCats = $this->sortCatalog($arrCats);
				
				$arrCats = array_merge($arrCats, $arrCatsType);				
			}
			
		}else{		//single category
			
			$arrCats = $objAddons->getAddonsWidthCategories(true, true, $this->addonType);
					
			if(GlobalsUC::$enableWebCatalog == true)
				$arrCats = $this->webAPI->mergeCatsAndAddonsWithCatalog($arrCats, false, $this->objAddonType);
			
			$arrCats = $this->sortCatalog($arrCats);
		}
		
		
		$arrCats = $this->removeEmptyCatalogCats($arrCats);
		
		
		return($arrCats);
	}
	
	
	/**
	 * get addons items
	 */
	private function getArrCats_pages(){
		
		if($this->isFromWebOnly == true){
			
			//get only catalog meanwhile, later merge
			$arrPagesCatalog = $this->webAPI->getCatalogArray_pages();
			
			if(empty($arrPagesCatalog))
				$arrPagesCatalog = array();
			
			return($arrPagesCatalog);
		}
		
		//get without catalog meanwhile, later merge
		
		$objLayouts = new UniteCreatorLayouts();
		$arrCats = $objLayouts->getLayoutsWithCategories($this->addonType, true);
		
		
		if(GlobalsUC::$enableWebCatalog == true)
			$arrCats = $this->webAPI->mergeCatsAndLayoutsWithCatalog($arrCats, $this->objAddonType);
					
		return($arrCats);
		
	}
	
	private function a________OUTPUT_______(){}
	
	
	
	
	/**
	* get catalog html
	 */
	private function getHtmlCatalog($putMode = false){
		
		
		if($this->isPages == false)
			$arrCats = $this->getArrCats_addons();
		else
			$arrCats = $this->getArrCats_pages();
		
		
		$addClass = "";
		if($this->isPages == true)
			$addClass = " uc-catalog-pages";
		
		if($this->hasCategories == false)
			$addClass .= " uc-param-nocats";
		
		if(!empty($this->addonType))
			$addClass .= " uc-addontype-".$this->addonType;
		
		if($this->isSelectMode == true)
			$addClass .= " uc-select-mode";
		
			
		$html = "";
		
		$html .= self::BR.self::TAB2."<!-- start addon catalog -->".self::BR;
		
		$html .= self::TAB2."<div class='uc-catalog{$addClass}'>".self::BR;
		
		if($this->hasHeader == true)
			$html .= $this->getHtmlCatalogHeader();
		
		$html .= self::TAB2."<div class='uc-browser-body unite-clearfix'>".self::BR;
		
		//output tabs
		if($this->hasCategories == true)
			$html .= $this->getHtmlTabs($arrCats);
		
		//output content
		$html .= $this->getHtmlContent($arrCats);
		
		$html .= self::TAB2."</div>".self::BR;	//end body
		
		$html .= self::TAB2."</div>".self::BR;	//end catalog
		
		$html .= self::BR.self::TAB2."<!-- end addon catalog -->".self::BR;
		
		return($html);
	}
	
	/**
	 * init the id
	 */
	private function initBrowserID(){
		
		if(!empty($this->browserID))
			return(false);
				
		$this->browserID = "uc_addon_browser_".$this->objAddonType->typeNameDistinct;
		$addText = "";
		
		$this->browserID .= $addText;
	}
	
	
	/**
	 * get browser html
	 */
	private function getHtml($putMode = false){
				
		$this->initBrowserID();
		
		$htmlCatalog = $this->getHtmlCatalog();
				
		$html = "";
		$html .= self::TAB."<!-- start addon browser --> ".self::BR;
				
		$addHtml = "";
		if(!empty($this->inputIDForUpdate))
			$addHtml .= " data-inputupdate=\"".$this->inputIDForUpdate."\"";
		
		$addHtml .= " data-prefix=\"".$this->prefix."\"";
		
		$addonType = $this->addonType;
		$addHtml .= " data-addontype='{$addonType}'";

		if($this->isPages)
			$addHtml .= " data-ispages='true'";
							
		$addClass = "";
		if($this->objAddonType->isSVG == true){
			$addClass .= " uc-svg-thumbs";
		}
		
		$id = $this->browserID;
		$id = esc_attr($id);
		$html .= self::TAB."<div id=\"{$id}\" class=\"uc-browser-wrapper{$addClass}\" {$addHtml} style='display:none'>".self::BR;
		
		if($putMode == true){
			echo UniteProviderFunctionsUC::escCombinedHtml($html);
			$html = "";
		}
				
		$html .= $htmlCatalog;
		
		$html .= self::TAB."</div>"; //wrapper
		
		if($putMode == true)
			echo UniteProviderFunctionsUC::escCombinedHtml($html);
		else
			return($html);
	}
	
	
	/**
	 * put html
	 */
	private function putHtml(){
					
		$this->getHtml(true);
	}
	
	
	/**
	 * put scripts
	 */
	public function putScripts(){
		
		UniteCreatorAdmin::onAddScriptsBrowser();
	}
	
		
	/**
	 * put browser
	 */
	public function putBrowser($putMode = true){
		
		if(empty($this->objAddonType))
			$this->objAddonType = UniteCreatorAddonType::getAddonTypeObject(GlobalsUC::ADDON_TYPE_REGULAR_ADDON);
					
		if($putMode == false){
			$html = $this->getHtml();
			return($html);
		}
				
		$this->putHtml();
		
		if(self::$isPutOnce_catalogUpdate == false){
			$this->putActivateProDialog();
			$this->putCatalogUpdateDialog();
		}
		
		self::$isPutOnce_catalogUpdate = true;
		
	}
	
	
	/**
	 * put scripts and browser
	 */
	public function putScriptsAndBrowser($getHTML = false){
		
		try{
			
			$this->putScripts();
			$html = $this->putBrowser($getHTML);
			
			if($getHTML == true)
				return($html);
			else
				echo UniteProviderFunctionsUC::escCombinedHtml($html);
			
		}catch(Exception $e){
			
			$message = $e->getMessage();
			
			$trace = "";
			if(GlobalsUC::$SHOW_TRACE == true)
				$trace = $e->getTraceAsString();
			
			$htmlError = HelperUC::getHtmlErrorMessage($message, $trace);
			
			return($htmlError);
		}
		
	}
	
	
	/**
	 * put activate dialog
	 */
	private function putActivateProDialog() {
		
		$path = HelperUC::getPathViewObject("activation_view.class");
		require_once $path;
		
		$objActivationView = new UniteCreatorActivationView();
		$objActivationView->putHtmlPopup();
	}
	
	
	/**
	 * put check udpate dialog
	 */
	private function putCatalogUpdateDialog(){
				
		?>
		
			<div id="uc_dialog_catalog_update" title="<?php esc_html_e("Check And Update Catalog")?>" class="unite-inputs" style="display:none">
				<div class="unite-dialog-inside">
					
					<span id="uc_dialog_catalog_update_loader" class="loader_text">
						<?php esc_html_e("Checking Update", "unlimited-elements-for-elementor")?>...
					</span>
					
					<div id="uc_dialog_catalog_update_error" class="error-message"></div>
					
					<div id="uc_dialog_catalog_update_message" class="uc-catalog-update-message"></div>
					
				</div>
				
			</div>		
		<?php 
	}
	
}