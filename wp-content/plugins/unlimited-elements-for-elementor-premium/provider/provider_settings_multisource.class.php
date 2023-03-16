<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2012 Unite CMS, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

class UniteCreatorSettingsMultisource{
	
	private $settings;
	private $objAddon;
	
	const TYPE_JSONCSV = "json_csv";
	const TYPE_REPEATER = "repeater";
	const TYPE_POSTS = "posts";
	const TYPE_PRODUCTS = "products";
	const TYPE_TERMS = "terms";
	const TYPE_USERS = "users";
	const TYPE_MENU = "menu";
	const TYPE_INSTAGRAM = "instagram";
	const TYPE_GALLERY = "gallery";
	
	
	
	public function __construct(){
		
		//for autocomplete
		$this->objAddon	= new UniteCreatorAddon();
		
		$this->objAddon = null;
		
	}
	
	
	/**
	 * set the settings
	 */
	public function setSettings(UniteCreatorSettings $settings){

		$this->settings = $settings;
		$this->objAddon = GlobalsProviderUC::$activeAddonForSettings;
		
	}
	
	
	/**
	 * add items multisource
	 */
	public function addItemsMultisourceSettings($name, $value, $title, $param){
		
		UniteFunctionsUC::validateNotEmpty($this->settings, "settings object");

		if(empty($this->objAddon))
			return(false);
		
		//------ items source ------
		
		$arrSource = array();
		
		$arrSource["items"] = __("Items", "unlimited-elements-for-elementor");
		$arrSource["posts_free"] = __("Posts (pro)", "unlimited-elements-for-elementor");
		
		$isWooActive = UniteCreatorWooIntegrate::isWooActive();
		if($isWooActive == true)
			$arrSource["products_free"] = __("WooCommerce Products (pro)", "unlimited-elements-for-elementor");
		
		$metaRepeaterTitle = __("Meta Field (pro)", "unlimited-elements-for-elementor");
		
		$isAcfExists = UniteCreatorAcfIntegrate::isAcfActive();
		
		if($isAcfExists == true)
			$metaRepeaterTitle = __("ACF Cutom Field (pro)", "unlimited-elements-for-elementor");
		
		$arrSource["repeater_free"] = $metaRepeaterTitle;
		$arrSource["json_free"] = __("JSON or CSV (pro)", "unlimited-elements-for-elementor");
		$arrSource["gallery_free"] = __("Gallery (pro)", "unlimited-elements-for-elementor");
		$arrSource["terms_free"] = __("Terms (pro)", "unlimited-elements-for-elementor");
		$arrSource["users_free"] = __("Users (pro)", "unlimited-elements-for-elementor");
		$arrSource["menu_free"] = __("Menu (pro)", "unlimited-elements-for-elementor");
		
		$hasInstagram = HelperProviderCoreUC_EL::isInstagramSetUp();
		
		if($hasInstagram)
			$arrSource["instagram_free"] = __("Instagram (pro)", "unlimited-elements-for-elementor");
		
		
		$arrSource = array_flip($arrSource);

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		
		$this->settings->addSelect($name."_source", $arrSource, __("Items Source", "unlimited-elements-for-elementor"), "items", $params);
		
		
		//--------- message ---------- 
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_STATIC_TEXT;
		$params["elementor_condition"] = array($name."_source!"=>"items");
		
		$text = __("The Multi-Source feature exists only in the PRO version. 
		<a href='https://unlimited-elements.com/pricing/' target='_blank'>Upgrade Now</a> 
		<br><br>
		To learn more about Multi-Source <a href='https://unlimited-elements.com/multi-source/' target='_blank' >Click Here</a>", "unlimited-elements-for-elementor");
		
		$this->settings->addStaticText($text, $name."_source_free_text", $params);
		
	}
	
	
}