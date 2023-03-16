<?php


class UniteProviderCoreAdminUC_Elementor extends UniteProviderAdminUC{

	private $objFeedback;
	
	
	/**
	 * the constructor
	 */
	public function __construct($mainFilepath){
		
			$this->pluginName = GlobalsUnlimitedElements::PLUGIN_NAME;
			
			$this->textBuy = esc_html__("Activate Plugin", "unlimited-elements-for-elementor");
			
			$this->linkBuy = null;
			$this->coreAddonType = GlobalsUnlimitedElements::ADDONSTYPE_ELEMENTOR;
			$this->coreAddonsView = GlobalsUnlimitedElements::VIEW_ADDONS_ELEMENTOR;
			$this->pluginTitle = esc_html__("Unlimited Elements", "unlimited-elements-for-elementor");
			
			$this->arrAllowedViews = array(
				"addons_elementor",
				"licenseelementor", 
				"settingselementor", 
				"troubleshooting-overload",
				"troubleshooting-phpinfo",
				"troubleshooting-memory-usage",
				"troubleshooting-connectivity",
				"instagram-test",
				"testaddon",
				"testaddonnew",
				"addon",
				"addondefaults",
				"svg_shapes",
				"wpml-fields",
				"backgrounds",
				GlobalsUnlimitedElements::VIEW_TEMPLATES_ELEMENTOR,
				GlobalsUnlimitedElements::VIEW_CUSTOM_POST_TYPES,
				"testsettings"
			);
					
			HelperProviderCoreUC_EL::globalInit();
			
			//set permission
			$permission = HelperProviderCoreUC_EL::getGeneralSetting("edit_permission");
					
			if($permission == "editor")
				$this->setPermissionEditor();
		
		parent::__construct($mainFilepath);
	}
	
	
	/**
	 * modify category settings, add consolidate addons
	 */
	public function managerAddonsModifyCategorySettings($settings, $objCat, $filterType){
		
		if($filterType != UniteCreatorElementorIntegrate::ADDONS_TYPE)
			return($settings);
				
		$settings->updateSettingProperty("category_alias", "disabled", "true");
		$settings->updateSettingProperty("category_alias", "description", esc_html__("The category name is unchangable, because of the addons consolidation, if changed it could break the layout.", "unlimited-elements-for-elementor") );
				
		return($settings);
	}
		
	/**
	 * modify plugins view links
	 */
	public function modifyPluginViewLinks($arrLinks){
		
		if(GlobalsUC::$isProductActive == true)
			return($arrLinks);
		
		if(empty($this->linkBuy))
			return($arrLinks);
					
		$linkbuy = HelperHtmlUC::getHtmlLink($this->linkBuy, $this->textBuy,"","uc-link-gounlimited", true);
		
		$arrLinks["gounlimited"] = $linkbuy;
		
		return($arrLinks);
	}
	
	
	
	/**
	 * add admin menu links
	 */
	protected function addAdminMenuLinks(){
		
		$urlMenuIcon = HelperProviderCoreUC_EL::$urlCore."images/icon_menu.png";
		
		$mainMenuTitle = $this->pluginTitle;
		
		$this->addMenuPage($mainMenuTitle, "adminPages", $urlMenuIcon);
		
		$this->addSubMenuPage($this->coreAddonsView, __('Widgets', "unlimited-elements-for-elementor"), "adminPages");
		
    	$enableBackgrounds = HelperProviderCoreUC_EL::getGeneralSetting("enable_backgrounds");
    	$enableBackgrounds = UniteFunctionsUC::strToBool($enableBackgrounds);
    	
		if($enableBackgrounds == true)
			$this->addSubMenuPage(GlobalsUnlimitedElements::VIEW_BACKGROUNDS, __('Background Widgets', "unlimited-elements-for-elementor"), "adminPages");

				
		$this->addSubMenuPage(GlobalsUnlimitedElements::VIEW_TEMPLATES_ELEMENTOR, __('Templates',"unlimited-elements-for-elementor"), "adminPages");
		
		$this->addSubMenuPage("settingselementor", __('General Settings',"unlimited-elements-for-elementor"), "adminPages");
		
		if(defined("UNLIMITED_ELEMENTS_UPRESS_VERSION")){
			
			if(GlobalsUC::$isProductActive == false && self::$view != GlobalsUnlimitedElements::VIEW_LICENSE_ELEMENTOR)
				HelperUC::addAdminNotice("The Unlimited Elements Plugin is not Active. Please activete it in license page.");
			
			$this->addSubMenuPage(GlobalsUnlimitedElements::VIEW_LICENSE_ELEMENTOR, __('Upress License',"unlimited-elements-for-elementor"), "adminPages");
		}
		
		$this->addLocalFilter("plugin_action_links_".$this->pluginFilebase, "modifyPluginViewLinks");
		
		//$isFsActivated = HelperProviderUC::isActivatedByFreemius();
		
		//if($isFsActivated == false)
			//$this->addSubMenuPage("licenseelementor", __('Old License Activation',"unlimited-elements-for-elementor"), "adminPages");
		
	}
	
	
	/**
	 * allow feedback on uninstall
	 */
	private function initFeedbackUninstall(){
		
		$this->objFeedback = new UnlimitedElementsFeedbackUC();
		
		$this->objFeedback->init();
		
	}
	
	
	/**
	 * init the admin notices
	 */
	public function initAdminNotices(){
		
		if(GlobalsUnlimitedElements::$showAdminNotice == false)
			return(false);
		
		if(empty(GlobalsUnlimitedElements::$arrAdminNotice))
			return(false);
					
		$arrNotice = GlobalsUnlimitedElements::$arrAdminNotice;
		
		$noticeID = UniteFunctionsUC::getVal($arrNotice, "id");
		$text = UniteFunctionsUC::getVal($arrNotice, "text");
		$expire = UniteFunctionsUC::getVal($arrNotice, "expire");
		$freeOnly = UniteFunctionsUC::getVal($arrNotice, "free_only");
		$freeOnly = UniteFunctionsUC::strToBool($freeOnly);
		
		if($freeOnly == true && GlobalsUC::$isProVersion == true)
			return(false);
		
		$objNotices = new UniteCreatorAdminNotices();
		$objNotices->setNotice($text, $noticeID, $expire, $arrNotice);		
	}
	
	
	/**
	 * init
	 */
	protected function init(){
		
		UniteProviderFunctionsUC::addFilter(UniteCreatorFilters::FILTER_MANAGER_ADDONS_CATEGORY_SETTINGS, array($this,"managerAddonsModifyCategorySettings"),10,3);
		
		if(GlobalsUnlimitedElements::ALLOW_FEEDBACK_ONUNINSTALL == true)
			$this->initFeedbackUninstall();

		//init the admin notices - on admin init ation
		
		add_action("admin_init",array($this,"initAdminNotices"));
						
		parent::init();
	}
	
	
	
}