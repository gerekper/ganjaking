<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

class UniteCreatorAddonType_CustomPostType extends UniteCreatorAddonType{
	
	
	/**
	 * init the addon type
	 */
	protected function init(){
		
		parent::init();
		
		$this->typeName = GlobalsUnlimitedElements::ADDONSTYPE_CUSTOM_POSTTYPES;
		$this->isBasicType = false;
		
		$this->allowWebCatalog = false;
		$this->allowManagerWebCatalog = false;
				
		$this->textPlural = __("Custom Post Types", "unlimited-elements-for-elementor");
		$this->textSingle = __("Type", "unlimited-elements-for-elementor");
		$this->textShowType = __("Custom Post Type", "unlimited-elements-for-elementor");
		$this->enableCategories = false;
				
		$responseAssets = UniteProviderFunctionsUC::setAssetsPath("ac_assets", true);
		
		$this->pathAssets = $responseAssets["path_assets"];
		$this->urlAssets = $responseAssets["url_assets"];
		
		$this->addonView_urlBack = HelperUC::getViewUrl(GlobalsUnlimitedElements::VIEW_CUSTOM_POST_TYPES);
		$this->addonView_showSmallIconOption = false;
		
		
	}
	
	
}
