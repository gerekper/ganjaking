<?php

class UniteProviderCoreAdminUC_Elementor extends UniteProviderAdminUC{

	private $objFeedback;

	/**
	 * the constructor
	 */
	public function __construct($mainFilepath){

		$this->pluginName = GlobalsUnlimitedElements::PLUGIN_NAME;
		$this->pluginTitle = esc_html__("Unlimited Elements", "unlimited-elements-for-elementor");

		$this->textBuy = esc_html__("Activate Plugin", "unlimited-elements-for-elementor");
		$this->linkBuy = null;

		$this->defaultAddonType = GlobalsUnlimitedElements::ADDONSTYPE_ELEMENTOR;
		$this->defaultView = (GlobalsUnlimitedElements::$enableDashboard === true)
			? GlobalsUnlimitedElements::VIEW_DASHBOARD
			: GlobalsUnlimitedElements::VIEW_ADDONS_ELEMENTOR;

		$this->arrAllowedViews = array(
			"addons_elementor",
			"licenseelementor",
			"email-test",
			"forms-logs",
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
			"testsettings",
			GlobalsUnlimitedElements::VIEW_DASHBOARD,
			GlobalsUnlimitedElements::VIEW_BACKGROUNDS,
			GlobalsUnlimitedElements::VIEW_TEMPLATES_ELEMENTOR,
			GlobalsUnlimitedElements::VIEW_FORM_ENTRIES,
			GlobalsUnlimitedElements::VIEW_SETTINGS_ELEMENTOR,
			GlobalsUnlimitedElements::VIEW_CUSTOM_POST_TYPES,
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
			return ($settings);

		$settings->updateSettingProperty("category_alias", "disabled", "true");
		$settings->updateSettingProperty("category_alias", "description", esc_html__("The category name is unchangable, because of the addons consolidation, if changed it could break the layout.", "unlimited-elements-for-elementor"));

		return ($settings);
	}

	/**
	 * modify plugins view links
	 */
	public function modifyPluginViewLinks($arrLinks){

		if(GlobalsUC::$isProductActive == true)
			return ($arrLinks);

		if(empty($this->linkBuy))
			return ($arrLinks);

		$linkbuy = HelperHtmlUC::getHtmlLink($this->linkBuy, $this->textBuy, "", "uc-link-gounlimited", true);

		$arrLinks["gounlimited"] = $linkbuy;

		return ($arrLinks);
	}

	/**
	 * add admin menu links
	 */
	protected function addAdminMenuLinks(){

		$urlMenuIcon = HelperProviderCoreUC_EL::$urlCore . "images/icon_menu.png";

		$mainMenuTitle = $this->pluginTitle;

		$this->addMenuPage($mainMenuTitle, "adminPages", $urlMenuIcon);

		if(GlobalsUnlimitedElements::$enableDashboard === true)
			$this->addSubMenuPage(GlobalsUnlimitedElements::VIEW_DASHBOARD, __('Home', "unlimited-elements-for-elementor"), "adminPages");

		$this->addSubMenuPage(GlobalsUnlimitedElements::VIEW_ADDONS_ELEMENTOR, __('Widgets', "unlimited-elements-for-elementor"), "adminPages");

		if(HelperProviderUC::isBackgroundsEnabled() === true)
			$this->addSubMenuPage(GlobalsUnlimitedElements::VIEW_BACKGROUNDS, __('Background Widgets', "unlimited-elements-for-elementor"), "adminPages");

		$this->addSubMenuPage(GlobalsUnlimitedElements::VIEW_TEMPLATES_ELEMENTOR, __('Templates', "unlimited-elements-for-elementor"), "adminPages");

		if(HelperProviderUC::isFormEntriesEnabled() === true)
			$this->addSubMenuPage(GlobalsUnlimitedElements::VIEW_FORM_ENTRIES, __('Form Entries', "unlimited-elements-for-elementor"), "adminPages");

		$this->addSubMenuPage(GlobalsUnlimitedElements::VIEW_SETTINGS_ELEMENTOR, __('General Settings', "unlimited-elements-for-elementor"), "adminPages");

		if(defined("UNLIMITED_ELEMENTS_UPRESS_VERSION")){
			if(GlobalsUC::$isProductActive == false && self::$view != GlobalsUnlimitedElements::VIEW_LICENSE_ELEMENTOR)
				HelperUC::addAdminNotice("The Unlimited Elements Plugin is not active. Please activate it in license page.");

			$this->addSubMenuPage(GlobalsUnlimitedElements::VIEW_LICENSE_ELEMENTOR, __('Upress License', "unlimited-elements-for-elementor"), "adminPages");
		}

		$this->addLocalFilter("plugin_action_links_" . $this->pluginFilebase, "modifyPluginViewLinks");

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
	 * init
	 */
	protected function init(){

		UniteProviderFunctionsUC::addFilter(UniteCreatorFilters::FILTER_MANAGER_ADDONS_CATEGORY_SETTINGS, array($this, "managerAddonsModifyCategorySettings"), 10, 3);

		if(GlobalsUnlimitedElements::ALLOW_FEEDBACK_ONUNINSTALL === true)
			$this->initFeedbackUninstall();

		parent::init();
	}

}
