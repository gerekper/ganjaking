<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

class UniteCreatorAddonType_Elementor extends UniteCreatorAddonType{
	
	
	/**
	 * init the addon type
	 */
	protected function init(){
		
		parent::init();
		
		$this->typeName = GlobalsUnlimitedElements::ADDONSTYPE_ELEMENTOR;
		$this->isBasicType = false;
		
		$this->allowWebCatalog = true;
		$this->allowManagerWebCatalog = true;
		$this->catalogKey = "addons";
		$this->arrCatalogExcludeCats = array("basic");
		
		$this->textPlural = __("Widgets", "unlimited-elements-for-elementor");
		$this->textSingle = __("Widget", "unlimited-elements-for-elementor");
		$this->textShowType = __("Elementor Widget", "unlimited-elements-for-elementor");
		
		$this->browser_textBuy = esc_html__("Go Pro", "unlimited-elements-for-elementor");
		$this->browser_textHoverPro = __("Upgrade to PRO version <br> to use this widget", "unlimited-elements-for-elementor");
		$this->browser_urlPreview = "https://unlimited-elements.com/widget-preview/?widget=[name]";
		
		$urlLicense = "https://unlimited-elements.com/pricing/";
				
		$urlBuyInsidePlugin = admin_url("admin.php?".GlobalsUnlimitedElements::SLUG_BUY_BROWSER);
				
		$this->browser_urlBuyPro = $urlBuyInsidePlugin;
		
		$responseAssets = UniteProviderFunctionsUC::setAssetsPath("ac_assets", true);
		
		$this->pathAssets = $responseAssets["path_assets"];
		$this->urlAssets = $responseAssets["url_assets"];
		
		$this->addonView_urlBack = HelperUC::getViewUrl(GlobalsUnlimitedElements::VIEW_ADDONS_ELEMENTOR);
		$this->addonView_showSmallIconOption = false;		
	}
	
	
}
