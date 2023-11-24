<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

class UniteCreatorAddonType_BGAddon extends UniteCreatorAddonType{
	
	/**
	 * init the addon type
	 */
	protected function init(){
		 
		$this->typeName = GlobalsUC::ADDON_TYPE_BGADDON;
		$this->textSingle = __("Background Widget", "unlimited-elements-for-elementor");
		$this->textPlural = __("Background Widgets", "unlimited-elements-for-elementor");
		$this->textShowType = $this->textSingle;
		$this->titlePrefix = $this->textSingle." - ";
		$this->isBasicType = false;
		$this->allowWebCatalog = true;
		$this->allowManagerWebCatalog = true;
		$this->catalogKey = $this->typeName;
		$this->allowNoCategory = false;
		$this->defaultCatTitle = "Main";

		$this->browser_textBuy = esc_html__("Go Pro", "unlimited-elements-for-elementor");
		$this->browser_textHoverPro = __("Upgrade to PRO version <br> to use this widget", "unlimited-elements-for-elementor");
		$this->browser_urlPreview = "https://unlimited-elements.com/widget-preview/?widget=[name]";
		
		$urlLicense = GlobalsUnlimitedElements::LINK_BUY;
		
		$this->browser_urlBuyPro = $urlLicense;
		
		$responseAssets = UniteProviderFunctionsUC::setAssetsPath("ac_assets", true);
		
		$this->pathAssets = $responseAssets["path_assets"];
		$this->urlAssets = $responseAssets["url_assets"];
		
		$this->addonView_urlBack = HelperUC::getViewUrl(GlobalsUnlimitedElements::VIEW_BACKGROUNDS);
		$this->addonView_showSmallIconOption = false;		
		
		
	}
	
	
}
