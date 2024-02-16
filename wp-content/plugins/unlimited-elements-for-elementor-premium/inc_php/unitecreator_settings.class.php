<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

class UniteCreatorSettingsWork extends UniteSettingsAdvancedUC{

	const TYPE_FONT_PANEL = "fonts_panel";
	const TYPE_ITEMS = "items";
	const INSTAGRAM_DEFAULT_VALUE = "@gianlucavacchi";

	//additional attributes that allowed to add to regular settings from params
	protected $arrAddAttributes = array(
		"simple_mode",
		"all_cats_mode",
		"add_current_posts",
		"selector",
		"selector_value",
		"selector1",		//for typography
		"selector2",
		"selector2_value",
		"selector3",
		"selector3_value"
	);

	private $currentAddon;
	private $currentTabs;


	private function a___________GETTERS_________(){}

	/**
	 * get settings in creator format
	 * the type should be given as "origtype" attribute
	 */
	public function getSettingsCreatorFormat(){

		$arrParams = array();
		foreach($this->arrSettings as $setting){

			$settingName = UniteFunctionsUC::getVal($setting, "name");

			$param = array();
			$origType = UniteFunctionsUC::getVal($setting, "origtype");
			$function = UniteFunctionsUC::getVal($setting, "function");

			UniteFunctionsUC::validateNotEmpty($origType, "settings original type for: $settingName");

			$param["type"] = $origType;
			$param["title"] = UniteFunctionsUC::getVal($setting, "text");
			$param["name"] = UniteFunctionsUC::getVal($setting, "name");
			$param["description"] = UniteFunctionsUC::getVal($setting, "description");
			$param["default_value"] = UniteFunctionsUC::getVal($setting, "default_value");
			$param["placeholder"] = UniteFunctionsUC::getVal($setting, "placeholder");

			$arrKeys = array("min","max","step","units","disabled","html",
							 "settings_items","items_values","hide_label","title_field","usefor");


			foreach($arrKeys as $key){

				$value = UniteFunctionsUC::getVal($setting, $key);

				if(!empty($value))
					$param[$key] = $value;
			}


			if(!empty($function))
				$param["function"] = $function;

			$classAdd = UniteFunctionsUC::getVal($setting, UniteSettingsUC::PARAM_CLASSADD);
			if(!empty($classAdd))
				$param[UniteSettingsUC::PARAM_CLASSADD] = $classAdd;

			$addParams = UniteFunctionsUC::getVal($setting, UniteSettingsUC::PARAM_ADDPARAMS);
			if(!empty($addParams))
				$param[UniteSettingsUC::PARAM_ADDPARAMS] = $addParams;

			$isMultiple = UniteFunctionsUC::getVal($setting, "is_multiple");	//for dropdown
			if(!empty($isMultiple))
				$param["is_multiple"] = true;

			$elementorCondition = UniteFunctionsUC::getVal($setting, "elementor_condition");	//for dropdown
			if(!empty($elementorCondition))
				$param["elementor_condition"] = $elementorCondition;

			$addDynamic = UniteFunctionsUC::getVal($setting, "add_dynamic");
			$addDynamic = UniteFunctionsUC::strToBool($addDynamic);

			if($addDynamic)
				$param["add_dynamic"] = true;


			$labelBlock = UniteFunctionsUC::getVal($setting, "label_block");	//label block
			if(!empty($labelBlock))
				$param["label_block"] = $labelBlock;


			$items = UniteFunctionsUC::getVal($setting, "items");
			if(!empty($items))
				$param["options"] = $items;

			$arrParams[] = $param;
		}

		return($arrParams);
	}

	/**
	 * get setting as creator params, for mapping, used in visual composer
	 * not metter the type
	 */
	public function getSettingsAsCreatorParams(){

		$arrParams = array();

		foreach($this->arrSettings as $setting){

			$param = array();

			$param["type"] = "uc_textfield";	//no metter what type, will be parsed anyway
			$param["name"] = UniteFunctionsUC::getVal($setting, "name");
			$param["title"] = UniteFunctionsUC::getVal($setting, "text");
			$param["description"] = UniteFunctionsUC::getVal($setting, "description");

			$param["uc_setting"] = $setting;

			$arrParams[] = $param;
		}


		return($arrParams);
	}


	/**
	* get multiple params creator format from one param
	*/
	public function getMultipleCreatorParams($param){

		if(!empty($this->arrSettings))
			UniteFunctionsUC::throwError("the settings should be empty for this operation");

		$this->addByCreatorParam($param);

		$arrParams = $this->getSettingsCreatorFormat();

		return($arrParams);
	}


	/**
	 * get settings types array
	 */
	public function getArrUCSettingTypes(){

		$arrTypes = array(
			"uc_textfield",
			UniteCreatorDialogParam::PARAM_NUMBER,
			"uc_textarea",
			"uc_editor",
			UniteCreatorDialogParam::PARAM_RADIOBOOLEAN,
			"uc_checkbox",
			"uc_dropdown",
			"uc_colorpicker",
			"uc_image",
			"uc_mp3",
			"uc_icon",
			UniteCreatorDialogParam::PARAM_ICON_LIBRARY,
			UniteCreatorDialogParam::PARAM_SHAPE,
			UniteCreatorDialogParam::PARAM_HR,
			UniteCreatorDialogParam::PARAM_HEADING,
			"uc_font_override",
			UniteCreatorDialogParam::PARAM_POST,
			UniteCreatorDialogParam::PARAM_POSTS_LIST,
			"uc_statictext",
			UniteCreatorDialogParam::PARAM_MENU
		);

		return($arrTypes);
	}



	private function a________SETTINGS_TYPES_________(){}

	/**
	 * add base url for image settings if needed
	 */
	public function addImage($name,$defaultValue = "",$text = "",$arrParams = array()){

		if(empty($defaultValue))
			$defaultValue = GlobalsUC::$url_no_image_placeholder;

		parent::addImage($name, $defaultValue, $text, $arrParams);

		//check the source param
		$lastIndex = count($this->arrSettings)-1;
		$this->arrSettings[$lastIndex] = $this->checkParamsSource($this->arrSettings[$lastIndex]);

	}


	/**
	 * add base url for image settings if needed
	 */
	public function addMp3($name,$defaultValue = "",$text = "",$arrParams = array()){

		parent::addMp3($name, $defaultValue, $text, $arrParams);

		//check the source param
		$lastIndex = count($this->arrSettings)-1;
		$this->arrSettings[$lastIndex] = $this->checkParamsSource($this->arrSettings[$lastIndex]);

	}

	/**
	 * add settings provider types
	 */
	protected function addSettingsProvider($type, $name,$value,$title,$extra ){

		dmp("function for override: addSettingsProvider ");
		exit();

	}

	/**
	 * add post terms settings
	 */
	protected function addPostTermsPicker($name,$value,$title,$extra){

		dmp("addPostsListPicker - function for override");
		exit();
	}

	/**
	 * add listing picker, function for override
	 */
	protected function addListingPicker($name,$value,$title,$extra){

		dmp("addListingPicker - function for override");
		exit();
	}

	/**
	 * add post terms settings
	 */
	protected function addWooCatsPicker($name,$value,$title,$extra){

		dmp("addWooCatsPicker - function for override");
		exit();
	}


	/**
	 * add users picker
	 */
	protected function addUsersPicker($name,$value,$title,$extra){

		dmp("addUsersPicker - function for override");
		exit();
	}

	/**
	 * add template picker
	 */
	protected function addTemplatePicker($name,$value,$title,$extra){

		dmp("addTemplatePicker - function for override");
		exit();
	}

	/**
	 * add post list picker
	 */
	protected function addPostsListPicker($name,$value,$title,$extra){
		dmp("addPostsListPicker - function for override");
		exit();
	}


	/**
	 * add background settings
	 */
	protected function addBackgroundSettings($name,$value,$title,$param){



		dmp("addBackgroundSettings - function for override");
		exit();
	}


	/**
	 * add menu picker
	 */
	protected function addMenuPicker($name,$value,$title,$extra){

		dmp("addMenuPicker - function for override");
		exit();
	}



	/**
	 * add instagram selector
	 */
	protected function addInstagramSelector($name,$value,$title,$extra){

		$defaultMaxItems = UniteFunctionsUC::getVal($extra, "max_items");
		if(is_numeric($defaultMaxItems) == false || $defaultMaxItems < 1)
			$defaultMaxItems = 12;

		$objServies = new UniteServicesUC();
		$objServies->includeInstagramAPI();

		$accessData = HelperInstaUC::getInstagramSavedAccessData();
		$accessToken = UniteFunctionsUC::getVal($accessData, "access_token");
		$username = UniteFunctionsUC::getVal($accessData, "username");

		if(!empty($accessToken)){

			$params = array();
			$params["origtype"] = UniteCreatorDialogParam::PARAM_STATIC_TEXT;

			$text = __("Get data from instagram user: ", "unlimited-elements-for-elementor");

			$this->addStaticText($text. $username, $name, $params);

		}else{

			$params = array();
			$params["origtype"] = UniteCreatorDialogParam::PARAM_STATIC_TEXT;

			$linkSettings = HelperUC::getViewUrl(GlobalsUnlimitedElements::VIEW_SETTINGS_ELEMENTOR);
			$htmlLink = HelperHtmlUC::getHtmlLink($linkSettings, __("General Settings","unlimited-elements-for-elementor"),"","",true);

			/*
			$text = __("Please connect to your instagram account from ", "unlimited-elements-for-elementor");
			$text .= $htmlLink;

			$this->addStaticText($text, $name, $params);
			*/

			$description = esc_html__("Type instagram user (@username). This method is deprecated. Please connect with your instagram user from general settings", "unlimited-elements-for-elementor");
			$params = array("description"=>$description);

			if(empty($value))
				$value = self::INSTAGRAM_DEFAULT_VALUE;

			$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;

			$this->addTextBox($name, $value ,esc_html__("Instagram User", "unlimited-elements-for-elementor"), $params);

		}

		//add number of items
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$desciption = __("Number of instagram items. Leave empty for default number ( %d ) set by the widget", "unlimited-elements-for-elementor");
		$desciption = sprintf($desciption, $defaultMaxItems);
		$params["description"] = $desciption;

		$this->addTextBox($name."_num_items", $defaultMaxItems ,esc_html__("Number Of Items", "unlimited-elements-for-elementor"), $params);

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
		$this->addHr("hr_after_insta", $params);

	}


	/**
	 * add font panel setting
	 */
	public function addFontPanel($arrParamNames, $arrFontsData, $name = null, $options = array()){

		$value = "";
		$arrParams = array();
		$arrParams["font_param_names"] = $arrParamNames;
		if(!empty($options))
			$arrParams = array_merge($arrParams, $options);

		if(empty($name))
			$name = "uc_fonts_panel";


		$this->add($name, $arrFontsData, self::PARAM_NOTEXT, self::TYPE_FONT_PANEL, $arrParams);
	}


	/**
	 * add gallery setting
	 */
	public function addGallery($name, $defualtValue, $text){

		$this->add($name, $defualtValue, $text, self::TYPE_GALLERY);

	}


	/**
	 * add repeater items panel
	 */
	public function addItemsPanelRepeater($addon, $source){

		$itemsType = $addon->getItemsType();

		if($itemsType == UniteCreatorAddon::ITEMS_TYPE_IMAGE){
			$this->addItemsPanel($addon, $source);
			return(false);
		}

		$arrDefaults = $addon->getArrItemsForConfig();

		$objSettings = $addon->getSettingsItemsObject();

		$text = self::PARAM_NOTEXT;

		$this->addRepeater("uc_items", $objSettings, $arrDefaults, $text);

	}

	/**
	 * add items setting
	 */
	public function addItemsPanel($addon, $source = null){

		$value = "";
		$arrParams = array();

		if(!empty($source))
			$arrParams["source"] = $source;

		$objManager = new UniteCreatorManagerInline();
		$objManager->setStartAddon($addon);

		$arrParams["items_manager"] = $objManager;
		$this->add("uc_items_editor", "", self::PARAM_NOTEXT, self::TYPE_ITEMS, $arrParams);
	}

	/**
	 * add typography setting
	 */
	public function addTypographySetting($name, $value, $title, $params = array()){

		$this->add($name, $value, $title, self::TYPE_TYPOGRAPHY, $params);
	}

	/**
	 * add text shadow setting
	 */
	public function addTextShadowSetting($name, $value, $title, $params = array()){

		$this->add($name, $value, $title, self::TYPE_TEXTSHADOW, $params);
	}

	/**
	 * add box shadow setting
	 */
	public function addBoxShadowSetting($name, $value, $title, $params = array()){

		$this->add($name, $value, $title, self::TYPE_BOXSHADOW, $params);
	}

	/**
	 * add css filters setting
	 */
	public function addCssFiltersSetting($name, $value, $title, $params = array()){

		$this->add($name, $value, $title, self::TYPE_CSS_FILTERS, $params);
	}

	/**
	 * add dimentions setting
	 */
	public function addDimentionsSetting($name, $value, $title, $params = array()){

		$params["label_block"] = true;

		$this->add($name, $value, $title, self::TYPE_DIMENTIONS, $params);
	}

	/**
	 * add tabs
	 */
	public function addTabs($name, $items, $value, $params = array()){

		$params["items"] = $items;
		$params["label_block"] = true;

		$this->add($name, $value, self::PARAM_NOTEXT, self::TYPE_TABS, $params);
	}

	/**
	 * add tab
	 */
	public function addTabIfNeeded($name){

		// check if the name is empty - reset the tabs
		if(empty($name) === true){
			$this->currentTabs = null;

			return null;
		}

		// initialize the tabs
		$tabs = $this->currentTabs;

		if(empty($tabs) === true){
			$tabs = array(
				"name" => "tabs_" . UniteFunctionsUC::getRandomString(5),
				"items" => array(),
			);
		}

		// add/update the tab
		$value = UniteFunctionsUC::getVal($tabs["items"], $name);

		if(empty($value) === true){
			$value = "tab_" . UniteFunctionsUC::getRandomString(5);

			$tabs["items"][$name] = $value;

			try{
				$this->updateSettingItems($tabs["name"], $tabs["items"]);
			}catch(Exception $exception){
				$this->addTabs($tabs["name"], $tabs["items"], $value);
			}
		}

		$this->currentTabs = $tabs;

		return array(
			"name" => $tabs["name"],
			"value" => $value,
		);
	}


	private function a__________SETTERS_________(){}

	/**
	 * set current addon
	 */
	public function setCurrentAddon(UniteCreatorAddon $addon){

		$this->currentAddon = $addon;

	}


	/**
	 * if the source == "addon" add url base
	 */
	private function checkParamsSource($arrParams){

		$source = UniteFunctionsUC::getVal($arrParams, "source");

		if($source == "addon"){

			if(empty($this->currentAddon))
				UniteFunctionsUC::throwError("You must set current addon before init settings for addon related image select option");

			$urlAssets = $this->currentAddon->getUrlAssets();

			$arrParams["url_base"] = $urlAssets;
		}

		return($arrParams);
	}



	/**
	 * if in this type exists multiple settings
	 */
	public static function isMultipleUCSettingType($type){

		switch($type){
			case UniteCreatorDialogParam::PARAM_POSTS_LIST:
			case UniteCreatorDialogParam::PARAM_CONTENT:
			case UniteCreatorDialogParam::PARAM_INSTAGRAM:
			case UniteCreatorDialogParam::PARAM_POST_TERMS:
			case UniteCreatorDialogParam::PARAM_WOO_CATS:
			case UniteCreatorDialogParam::PARAM_USERS:
			case UniteCreatorDialogParam::PARAM_TEMPLATE:
			case "uc_filters_repeater_params":
			case UniteCreatorDialogParam::PARAM_LISTING:
			case UniteCreatorDialogParam::PARAM_SPECIAL:

				return(true);
			break;
		}

		return(false);
	}


	/**
	 * add image base settings
	 */
	public function addImageBaseSettings(){

		$extra = array("origtype"=>"uc_image");
		$this->addImage("image","","Image",$extra);

		$extra = array("origtype"=>"uc_textarea");
		$this->addTextArea("description", "", esc_html__("Description", "unlimited-elements-for-elementor"),$extra);

		/*
		$extra = array("origtype"=>"uc_radioboolean");
		$this->addRadioBoolean("enable_link", esc_html__("Enable Link", "unlimited-elements-for-elementor"),false, "Yes","No",$extra);

		$extra = array("class"=>"unite-input-link", "origtype"=>"uc_textfield");
		$this->addTextBox("link", "", esc_html__("Link", "unlimited-elements-for-elementor"),$extra);
		*/

	}

	/**
	 * add items image size setting
	 */
	private function addItemsImageSizeSetting($name, $param){

		$title = UniteFunctionsUC::getVal($param, "title");

		$arrSizes = UniteFunctionsWPUC::getArrThumbSizes();

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["label_block"] = true;

		$arrSizes = array_flip($arrSizes);

		$this->addSelect($name."_size", $arrSizes, $title, "medium_large", $params);

	}


	/**
	 * add special param
	 */
	private function addSpecialParam($name, $param){

		$attributeType = UniteFunctionsUC::getVal($param, "attribute_type");

		$condition = HelperProviderCoreUC_EL::paramToElementorCondition($param);

		switch($attributeType){
			case "non":
			case "none":
			break;
			case "entrance_animation":

				UniteCreatorEntranceAnimations::addSettings($this, $name, $param);

			break;
			case "items_image_size":

				$this->addItemsImageSizeSetting($name, $param);

			break;
			case "schema":

				$arrParam = array();
				$arrParam["origtype"] = UniteCreatorDialogParam::PARAM_RADIOBOOLEAN;
				$arrParam["description"] = UniteFunctionsUC::getVal($param, "description");

				$this->addRadioBoolean($name."_enable", $param["title"],false,"Yes","No",$arrParam);

				$arrParam = array();
				$arrParam["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
				$arrParam["description"] = __("Use 'Schema Collect' option where there are multiple schemas on page. The last schema should be 'Output' type, others are 'Collect' type","unlimited-elements-for-elementor");
				$arrParam["elementor_condition"] = array($name."_enable"=>"true");

				//------- schema type

				$arrOptions = array();
				$arrOptions["output"] = "Schema Output";
				$arrOptions["collect"] = "Schema Collect";

				$arrOptions = array_flip($arrOptions);

				$this->addSelect($name."_type", $arrOptions, "Schema Type","output", $arrParam);

			break;
			case "dynamic_popup":

				$title = UniteFunctionsUC::getVal($param, "title");

				$arrOptions = array();
				$arrOptions["post"] = __("Post Link","unlimited-elements-for-elementor");
				$arrOptions["popup"] = __("Dynamic Post Popup","unlimited-elements-for-elementor");
				$arrOptions["empty"] = __("Disable Link","unlimited-elements-for-elementor");
				$arrOptions["meta"] = __("Link From Meta Field","unlimited-elements-for-elementor");

				$params = array();
				$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
				$params["elementor_condition"] = $condition;

				$arrOptions = array_flip($arrOptions);

				$this->addSelect("{$name}_link_type", $arrOptions, $title, "post", $params);

				//add text

				$params = array();
				$params["origtype"] = UniteCreatorDialogParam::PARAM_STATIC_TEXT;

				$condition["{$name}_link_type"] = "popup";
				$params["elementor_condition"] = $condition;

				$text = __("This option works with \"Dynamic Post Popup\" widget. Please put it to the page.", "unlimited-elements-for-elementor");

				$this->addStaticText($text, $name."_text", $params);

				//add meta name

				$params = array();
				$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;

				$condition["{$name}_link_type"] = "meta";
				$params["elementor_condition"] = $condition;

				$this->addTextBox($name."_meta_field","", __("Meta Field Name","unlimited-elements-for-elementor"), $params);

			break;
			case "contact_form7":

				//add not exists heading.
				$isInstalled = UniteCreatorPluginIntegrations::isContactFrom7Installed();

				if($isInstalled == false){

					$params = array();
					$params["origtype"] = UniteCreatorDialogParam::PARAM_STATIC_TEXT;
					$params["elementor_condition"] = $condition;

					$text = __("The Contact Form 7 Plugin is not installed", "unlimited-elements-for-elementor");

					$this->addStaticText($text, "{$name}_text", $params);

				}


				$title = UniteFunctionsUC::getVal($param, "title");

				$arrForms = UniteCreatorPluginIntegrations::getArrContactForm7();

				$default = UniteFunctionsUC::getArrFirstValue($arrForms);

				$params = array();
				$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
				$params["elementor_condition"] = $condition;

				$this->addSelect("{$name}_form", $arrForms, $title, $default, $params);

			break;
			case "ucform_conditions":

				$params = array();
				$params["elementor_condition"] = $condition;
				$params["origtype"] = UniteCreatorDialogParam::PARAM_REPEATER;
				$params["hide_label"] = true;
				$params["title_field"] = "{{{operator}}} {{{field_name}}} {{{condition}}} {{{field_value}}}";

				$settingsItems = UniteCreatorForm::getConditionsRepeaterSettings();

				$title = UniteFunctionsUC::getVal($param, "title");

				$this->addRepeater("{$name}_conditions", $settingsItems, array(), $title, $params);

			break;
			case "sort_filter_fields":

				$params = array();
				$params["elementor_condition"] = $condition;
				$params["origtype"] = UniteCreatorDialogParam::PARAM_REPEATER;
				$params["hide_label"] = true;
				$params["title_field"] = "{{{title}}} ({{{type}}})";

				$description = UniteFunctionsUC::getVal($param, "description");

				if(!empty($description))
					$params["description"] = $description;

				$settingsItems = HelperProviderUC::getSortFilterRepeaterFields();

				$settingsValues = HelperProviderUC::getSortFilterDefaultValues();

				$title = UniteFunctionsUC::getVal($param, "title");

				$this->addRepeater("{$name}_fields", $settingsItems, $settingsValues, $title, $params);

			break;
			case "currency_api":
				UniteCreatorAPIIntegrations::getInstance()->addServiceSettingsFields($this, UniteCreatorAPIIntegrations::TYPE_CURRENCY_EXCHANGE, $name, $condition);
			break;
			case "weather_api":
				UniteCreatorAPIIntegrations::getInstance()->addServiceSettingsFields($this, UniteCreatorAPIIntegrations::TYPE_WEATHER_FORECAST, $name, $condition);
			break;
			default:
				UniteFunctionsUC::throwError("Add special param error: wrong attribute type: $attributeType, please check that the plugin version is up to date");
			break;
		}

	}



	/**
	 * modify external loaded settings
	 */
	public function modifyLoadedSettings($loadParam){

		if($loadParam != "layout_row"){
			parent::modifyLoadedSettings($loadParam);
			return(false);
		}

		$arrChange = array(
			"padding_top"=>"row_padding_top",
			"padding_bottom"=>"row_padding_bottom",
			"padding_left"=>"row_padding_left",
			"padding_right"=>"row_padding_right",

			"margin_top"=>"row_margin_top",
			"margin_bottom"=>"row_margin_bottom",
			"margin_left"=>"row_margin_left",
			"margin_right"=>"row_margin_right"
		);


		foreach($arrChange as $oldName=>$newName)
			$this->changeSettingName($oldName, $newName);

	}

	/**
	 * modify by categories of the addons or layouts
	 */
	protected function modifyBeforeAdd_categories($setting, $addonType, $addNew = false){

		$objCategories = new UniteCreatorCategories();
		$arrCats = $objCategories->getCatsShort("", $addonType);

		if($addNew == true)
			$arrCats["new"] = "[". esc_html__("New Category", "unlimited-elements-for-elementor")."]";

		$fistValue = UniteFunctionsUC::getFirstNotEmptyKey($arrCats);

		$arrCats = array_flip($arrCats);

		$setting["items"] = $arrCats;

		$setting["value"] = $fistValue;
		$setting["default_value"] = $fistValue;

		return($setting);
	}


	/**
	 * modify setting before add (any setting)
	 */
	protected function modifyBeforeAdd($setting, $modifyType){

		switch($modifyType){
			case "choose_background":

				if($this->settingsType != "row_bg")
					unset($setting["items"]["More"]);

			break;
			case "library_sections_categories":

				$setting = $this->modifyBeforeAdd_categories($setting, GlobalsUC::ADDON_TYPE_LAYOUT_SECTION, true);

			break;
		}

		return($setting);
	}

	private function a__________CONDITIONS_________(){}


	/**
	 * add controls
	 */
	private function addByCreatorParam_handleConditions($param, $isForSap = false){

		$enableCondition = UniteFunctionsUC::getVal($param, "enable_condition");

		$enableCondition = UniteFunctionsUC::strToBool($enableCondition);

		if($enableCondition == false){
			return(false);
		}

		$name = UniteFunctionsUC::getVal($param, "name");

		$attribute = UniteFunctionsUC::getVal($param, "condition_attribute");
		$operator = UniteFunctionsUC::getVal($param, "condition_operator");
		$value = UniteFunctionsUC::getVal($param, "condition_value");

		$attribute2 = UniteFunctionsUC::getVal($param, "condition_attribute2");
		$operator2 = UniteFunctionsUC::getVal($param, "condition_operator2");
		$value2 = UniteFunctionsUC::getVal($param, "condition_value2");

		if(empty($attribute))
			return(false);

		$action = "show";
		if($operator == "not_equal")
			$action = "hide";

		$this->addControl($attribute, $name, $action, $value, $isForSap);

		if(empty($attribute2))
			return(false);

		$action = "show";
		if($operator2 == "not_equal")
			$action = "hide";


		$this->addControl($attribute2, $name, $action, $value2, $isForSap);
	}

    /**
     * add control by elementor condition
     */
    private function addControl_byElementorConditions($nameChild, $arrConditions){

    	if(empty($arrConditions) == true)
    		return(false);

    	if(is_array($arrConditions) == false)
    		UniteFunctionsUC::throwError("The elementor conditions should be array");

    	foreach($arrConditions as $nameParent=>$value){

    		$type = "show";

			$lastCharacter = substr($nameParent, -1);

			if($lastCharacter == "!"){
				$type = "hide";
				$nameParent = substr($nameParent, 0, -1);	//cut last character
			}

    		$this->addControl($nameParent, $nameChild, $type, $value);
    	}


    }


    /**
     * add controls by elementor conditions
     */
	private function addControls_byElementorConditions(){

		if(empty($this->arrSettings))
			return(false);

		foreach($this->arrSettings as $setting){

			$elementorCondition	 = UniteFunctionsUC::getVal($setting, "elementor_condition");

			if(empty($elementorCondition))
				continue;

			$name = UniteFunctionsUC::getVal($setting, "name");

			$this->addControl_byElementorConditions($name, $elementorCondition);
		}

	}


	/**
	 * Test addon settings - inside addon use and gutenberg.
	 * Not for elementor
	 */
	private function a__________TEST_ADDON_SETTINGS_________(){}

	/**
	 * check and add images sizes chooser
	 */
	private function checkAddImageSizes($paramImage){

		$isAddSizes = UniteFunctionsUC::getVal($paramImage, "add_image_sizes");
		$isAddSizes = UniteFunctionsUC::strToBool($isAddSizes);

		if($isAddSizes == false)
			return(false);

    	$type = UniteFunctionsUC::getVal($paramImage, "type");
    	$title = UniteFunctionsUC::getVal($paramImage, "title");
    	$name = UniteFunctionsUC::getVal($paramImage, "name");

    	$arrSizes = UniteFunctionsWPUC::getArrThumbSizes();

    	$arrSizes = array_flip($arrSizes);

    	if($type == UniteCreatorDialogParam::PARAM_POSTS_LIST){
	    	$paramTitle = $title .= " ".__("Image Size","unlimited-elements-for-elementor");
	    	$paramName = $name .= "_imagesize";
    	}else{
	    	$paramTitle = $title .= " ".__("Size","unlimited-elements-for-elementor");
    		$paramName = $name .= "_size";
    	}

    	// add the new setting

    	$arrOptions = array();
    	$this->addSelect($paramName, $arrSizes, $paramTitle, "medium_large", $arrOptions);


    	//handle new param conditions

    	$newParam = $paramImage;

    	$newParam["name"] = $paramName;
    	$newParam["type"] = UniteCreatorDialogParam::PARAM_DROPDOWN;

		$this->addByCreatorParam_handleConditions($newParam);


	}



	/**
	 * add setting by creator param
	 */
	public function addByCreatorParam($param, $inputValue = null){

		//add ready setting if exists
		$arrReadySetting = UniteFunctionsUC::getVal($param, "uc_setting");

		if(!empty($arrReadySetting)){
			$classAdd = UniteFunctionsUC::getVal($arrReadySetting, UniteSettingsUC::PARAM_CLASSADD);

			$arrReadySetting[UniteSettingsUC::PARAM_CLASSADD] = $classAdd;

			if(!empty($inputValue))
				$arrReadySetting["value"] = $inputValue;

			$this->addSettingByArray($arrReadySetting);

			return;
		}

		$defaultValue = UniteFunctionsUC::getVal($param, "default_value");
		$value = UniteFunctionsUC::getVal($param, "value", $defaultValue);
		$type = UniteFunctionsUC::getVal($param, "type");
		$title = UniteFunctionsUC::getVal($param, "title");
		$name = UniteFunctionsUC::getVal($param, "name");
		$description = UniteFunctionsUC::getVal($param, "description");
		$placeholder = UniteFunctionsUC::getVal($param, "placeholder");
		$tabName = UniteFunctionsUC::getVal($param, "tabname");
		$labelBlock = UniteFunctionsUC::getVal($param, "label_block");
		$labelBlock = UniteFunctionsUC::strToBool($labelBlock);
		$isResponsive = UniteFunctionsUC::getVal($param, "is_responsive");
		$isResponsive = UniteFunctionsUC::strToBool($isResponsive);
		$unit = UniteFunctionsUC::getVal($param, "unit");

		if($unit == "other")
			$unit = UniteFunctionsUC::getVal($param, "unit_custom");

		$extra = array(
			"origtype" => $type,
			"label_block" => $labelBlock,
			"is_responsive" => $isResponsive,
			"responsive_id" => $name,
		);

		if(!empty($description))
			$extra["description"] = $description;

		if(!empty($placeholder))
			$extra["placeholder"] = $placeholder;

		if(!empty($unit))
			$extra["unit"] = $unit;

		$tab = $this->addTabIfNeeded($tabName);

		if(!empty($tab)){
			$extra["tabs_id"] = $tab["name"];
			$extra["tabs_value"] = $tab["value"];
		}

		foreach($this->arrAddAttributes as $attributeName){
			$attributeValue = UniteFunctionsUC::getVal($param, $attributeName);

			if(!empty($attributeValue))
				$extra[$attributeName] = $attributeValue;
		}

		$isMultipleSettingType = self::isMultipleUCSettingType($type);
		$isUpdateValue = true;

		if($isMultipleSettingType && !empty($inputValue)){
			$value = $inputValue;
			$isUpdateValue = false;
		}

		switch ($type){
			case UniteCreatorDialogParam::PARAM_EDITOR:
				$this->addEditor($name, $value, $title, $extra);
			break;
			case UniteCreatorDialogParam::PARAM_TEXTFIELD:
				$this->addTextBox($name, $value, $title, $extra);
			break;
			case UniteCreatorDialogParam::PARAM_LINK:
				$this->addLink($name, $value, $title, $extra);
			break;
			case UniteCreatorDialogParam::PARAM_NUMBER:

				$extra["class"] = UniteCreatorSettingsOutput::INPUT_CLASS_NUMBER;
				$extra["responsive_type"] = "desktop";

				$this->addTextBox($name, $value, $title, $extra);

				if($isResponsive === true){
					$valueTablet = UniteFunctionsUC::getVal($param, "default_value_tablet");

					$extra["responsive_type"] = "tablet";

					$this->addTextBox($name . "_tablet", $valueTablet, $title, $extra);

					$valueMobile = UniteFunctionsUC::getVal($param, "default_value_mobile");

					$extra["responsive_type"] = "mobile";

					$this->addTextBox($name . "_mobile", $valueMobile, $title, $extra);
				}

			break;
			case UniteCreatorDialogParam::PARAM_RADIOBOOLEAN:
				$extra["true_value"] = $param["true_value"];
				$extra["false_value"] = $param["false_value"];

				$this->addRadioBoolean($name, $title, $value, $param["true_name"], $param["false_name"], $extra);
			break;
			case UniteCreatorDialogParam::PARAM_TEXTAREA:
				$this->addTextArea($name, $value, $title, $extra);
			break;
			case UniteCreatorDialogParam::PARAM_CHECKBOX:
				$textNear = UniteFunctionsUC::getVal($param, "text_near");
				$isChecked = UniteFunctionsUC::getVal($param, "is_checked");
				$isChecked = UniteFunctionsUC::strToBool($isChecked);

				$this->addCheckbox($name, $isChecked, $title, $textNear, $extra);
			break;
			case UniteCreatorDialogParam::PARAM_DROPDOWN:
				$options = UniteFunctionsUC::getVal($param, "options");

				$this->addSelect($name, $options, $title, $value, $extra);
			break;
			case UniteCreatorDialogParam::PARAM_MULTIPLE_SELECT:

				$options = UniteFunctionsUC::getVal($param, "options");

				$this->addMultiSelect($name, $options, $title, $value, $extra);

			break;
			case UniteCreatorDialogParam::PARAM_TERM_SELECT:

				$extra["post_select"] = true;
				$extra["post_select_type"] = "term";

				$this->addMultiSelect($name, array(), $title, $value, $extra);

			break;
			case UniteCreatorDialogParam::PARAM_POST_SELECT:
				$extra["post_select"] = true;
				$extra["post_select_type"] = "post";

				$this->addMultiSelect($name, array(), $title, $value, $extra);
			break;
			case UniteCreatorDialogParam::PARAM_COLORPICKER:
				$this->addColorPicker($name, $value, $title, $extra);
			break;
			case UniteCreatorDialogParam::PARAM_ADDONPICKER:
				$extra["addontype"] = UniteFunctionsUC::getVal($param, "addon_type");

				$this->addAddonPicker($name, $value, $title, $extra);
			break;
			case UniteCreatorDialogParam::PARAM_IMAGE:

				$this->addImage($name,$value,$title,$extra);

				$this->checkAddImageSizes($param);

			break;
			case "uc_mp3":
				$this->addMp3($name,$value,$title,$extra);
			break;
			case "uc_imagebase":
				$this->addImageBaseSettings();
			break;
			case "uc_statictext":
				$this->addStaticText($title, $name, $extra);
				$isUpdateValue = false;
			break;
			case UniteCreatorDialogParam::PARAM_ICON:
				$this->addIconPicker($name,$value,$title,$extra);
			break;
			case UniteCreatorDialogParam::PARAM_ICON_LIBRARY:
				$extra["enable_svg"] = UniteFunctionsUC::getVal($param, "enable_svg");

				$this->addIconPicker($name,$value,$title,$extra);
			break;
			case UniteCreatorDialogParam::PARAM_SHAPE:
				$this->addShapePicker($name,$value,$title,$extra);
			break;
			case UniteCreatorDialogParam::PARAM_MAP:
				$this->addGoogleMap($name,$value,$title,$extra);
			break;
			case UniteCreatorDialogParam::PARAM_HR:
				$this->addHr($name);
				$isUpdateValue = false;
			break;
			case UniteCreatorDialogParam::PARAM_HEADING:
				$extra["is_heading"] = true;

				$this->addStaticText($value,$name,$extra);
			break;
			case "uc_font_override":
				//don't draw anything
			break;
			case UniteCreatorDialogParam::PARAM_INSTAGRAM:
				$extra["max_items"] = UniteFunctionsUC::getVal($param, "max_items");

				$this->addInstagramSelector($name, $value, $title, $extra);
			break;
			case UniteCreatorDialogParam::PARAM_POST:
				$this->addPostPicker($name,$value,$title,$extra);
			break;
			case UniteCreatorDialogParam::PARAM_POSTS_LIST:
				$extra["for_woocommerce_products"] = UniteFunctionsUC::getVal($param, "for_woocommerce_products");
				$extra["default_max_posts"] = UniteFunctionsUC::getVal($param, "default_max_posts");

				$this->addPostsListPicker($name,$value,$title,$extra);
			break;
			case UniteCreatorDialogParam::PARAM_POST_TERMS:
				$extra["for_woocommerce"] = UniteFunctionsUC::getVal($param, "for_woocommerce");
				$extra["filter_type"] = UniteFunctionsUC::getVal($param, "filter_type");

				$this->addPostTermsPicker($name,$value,$title,$extra);
			break;
			case UniteCreatorDialogParam::PARAM_WOO_CATS:
				$this->addWooCatsPicker($name,$value,$title,$extra);
			break;
			case UniteCreatorDialogParam::PARAM_LISTING:
				$this->addListingPicker($name,$value,$title,$param);
			break;
			case UniteCreatorDialogParam::PARAM_USERS:
				$this->addUsersPicker($name,$value,$title,$extra);
			break;
			case UniteCreatorDialogParam::PARAM_TEMPLATE:
				$this->addTemplatePicker($name,$value,$title,$extra);
			break;
			case UniteCreatorDialogParam::PARAM_DATASET:
				//don't add any settings
			break;
			case UniteCreatorDialogParam::PARAM_CONTENT;
				$this->addContentSelector($name,$value,$title,$extra);
			break;
			case UniteCreatorDialogParam::PARAM_MENU:
				$useFor = UniteFunctionsUC::getVal($param, "usefor");

				if(!empty($useFor))
					$extra["usefor"] = $useFor;

				$this->addMenuPicker($name,$value,$title,$extra);
			break;
			case UniteCreatorDialogParam::PARAM_BORDER:
				$types = array_flip(array(
					"none" => "None",
					"solid" => "Solid",
					"dashed" => "Dashed",
					"dotted" => "Dotted",
					"double" => "Double",
					"groove" => "Groove",
					"ridge" => "Ridge",
					"inset" => "Inset",
					"outset" => "Outset",
				));

				$selector = UniteFunctionsUC::getVal($param, "selector");

				$typeName = $name . "_type";
				$typeTitle = sprintf(__("%s Type", "unlimited-elements-for-elementor"), $title);
				$typeDefault = UniteFunctionsUC::getVal($param, "border_type");
				$typeCondition = array($typeName . "!" => "none");

				$typeParams = array_merge($extra, array(
					"selector" => $selector,
					"selector_value" => HelperHtmlUC::getCSSSelectorValueByParam($type, "style"),
				));

				$this->addSelect($typeName, $types, $typeTitle, $typeDefault, $typeParams);

				$widthTitle = sprintf(__("%s Width", "unlimited-elements-for-elementor"), $title);

				$widthParams = array_merge($extra, array(
					"elementor_condition" => $typeCondition,
					"selector" => $selector,
					"selector_value" => HelperHtmlUC::getCSSSelectorValueByParam($type, "width"),
					"units" => array("px", "%", "em", "rem"),
					"is_responsive" => true,
				));

				$responsive = array(
					"desktop" => "",
					"tablet" => "_tablet",
					"mobile" => "_mobile",
				);

				foreach($responsive as $responsiveType => $responsiveSuffix){
					$addValue = array();
					$addValue["top"] = UniteFunctionsUC::getVal($param, "width_{$responsiveType}_top");
					$addValue["bottom"] = UniteFunctionsUC::getVal($param, "width_{$responsiveType}_bottom");
					$addValue["left"] = UniteFunctionsUC::getVal($param, "width_{$responsiveType}_left");
					$addValue["right"] = UniteFunctionsUC::getVal($param, "width_{$responsiveType}_right");
					$addValue["unit"] = UniteFunctionsUC::getVal($param, "width_{$responsiveType}_unit");
					$addValue["is_linked"] = UniteFunctionsUC::getVal($param, "width_{$responsiveType}_is_linked", true);

					$widthParams["responsive_type"] = $responsiveType;

					$this->addDimentionsSetting("{$name}_width{$responsiveSuffix}", $addValue, $widthTitle, $widthParams);
				}

				$colorTitle = sprintf(__("%s Color", "unlimited-elements-for-elementor"), $title);
				$colorDefault = UniteFunctionsUC::getVal($param, "border_color");

				$colorParams = array_merge($extra, array(
					"elementor_condition" => $typeCondition,
					"selector" => $selector,
					"selector_value" => HelperHtmlUC::getCSSSelectorValueByParam($type, "color"),
				));

				$this->addColorPicker($name . "_color", $colorDefault, $colorTitle, $colorParams);
			break;
			case UniteCreatorDialogParam::PARAM_PADDING:
			case UniteCreatorDialogParam::PARAM_MARGINS:
			case UniteCreatorDialogParam::PARAM_BORDER_DIMENTIONS:
				$extra["selector_value"] = HelperHtmlUC::getCSSSelectorValueByParam($type);

				$withoutUnits = UniteFunctionsUC::getVal($param, "no_units");
				$withoutUnits = UniteFunctionsUC::strToBool($withoutUnits);

				if($withoutUnits === false){
					$extra["units"] = array("px", "%", "em", "rem");
					$extra["units_selected"] = reset($extra["units"]);
				}

				$responsive = array("desktop" => "");

				if($isResponsive === true){
					$responsive["tablet"] = "_tablet";
					$responsive["mobile"] = "_mobile";
				}

				foreach($responsive as $responsiveType => $responsiveSuffix){
					$addValue = array();
					$addValue["top"] = UniteFunctionsUC::getVal($param, "{$responsiveType}_top");
					$addValue["bottom"] = UniteFunctionsUC::getVal($param, "{$responsiveType}_bottom");
					$addValue["left"] = UniteFunctionsUC::getVal($param, "{$responsiveType}_left");
					$addValue["right"] = UniteFunctionsUC::getVal($param, "{$responsiveType}_right");
					$addValue["unit"] = UniteFunctionsUC::getVal($param, "{$responsiveType}_unit");
					$addValue["is_linked"] = UniteFunctionsUC::getVal($param, "{$responsiveType}_is_linked", true);

					$extra["responsive_type"] = $responsiveType;

					$this->addDimentionsSetting($name . $responsiveSuffix, $addValue, $title, $extra);
				}
			break;
			case UniteCreatorDialogParam::PARAM_SLIDER:
				$extra["min"] = UniteFunctionsUC::getVal($param, "min");
				$extra["max"] = UniteFunctionsUC::getVal($param, "max");
				$extra["step"] = UniteFunctionsUC::getVal($param, "step");
				$extra["units"] = UniteFunctionsUC::getVal($param, "units");
				$extra["units"] = str_replace("percent", "%", $extra["units"]);
				$extra["units"] = explode("_", $extra["units"]);

				$responsive = array("desktop" => "");

				if($isResponsive === true){
					$responsive["tablet"] = "_tablet";
					$responsive["mobile"] = "_mobile";
				}

				foreach($responsive as $responsiveType => $responsiveSuffix){
					$value = UniteFunctionsUC::getVal($param, "default_value{$responsiveSuffix}");

					$extra["responsive_type"] = $responsiveType;

					$this->addRangeSlider($name . $responsiveSuffix, $value, $title, $extra);
				}
			break;
			case UniteCreatorDialogParam::PARAM_TYPOGRAPHY:
				$this->addTypographySetting($name, $value, $title, $extra);
			break;
			case UniteCreatorDialogParam::PARAM_TEXTSHADOW:
				$this->addTextShadowSetting($name, $value, $title, $extra);
			break;
			case UniteCreatorDialogParam::PARAM_BOXSHADOW:
				$this->addBoxShadowSetting($name, $value, $title, $extra);
			break;
			case UniteCreatorDialogParam::PARAM_CSS_FILTERS:
				$this->addCssFiltersSetting($name, $value, $title, $extra);
			break;
			case UniteCreatorDialogParam::PARAM_BACKGROUND:
				$this->addBackgroundSettings($name, $value, $title, $param);
			break;
			case UniteCreatorDialogParam::PARAM_HOVER_ANIMATIONS:
				$animations = HelperProviderCoreUC_EL::getHoverAnimationClasses(true);
				$animations = array_flip($animations);

				$value = UniteFunctionsUC::getVal($param, "default_value");

				$this->addSelect($name, $animations, $title, $value, $extra);
			break;
			case UniteCreatorDialogParam::PARAM_SPECIAL:
				$this->addSpecialParam($name, $param);
			break;
			case UniteCreatorDialogParam::PARAM_DATETIME:
				$extra["placeholder"] = "YYYY-mm-dd HH:ii";

				$this->addTextBox($name, $value, $title, $extra);
			break;
			default:
				$isAdded = $this->addSettingsProvider($type, $name, $value, $title, $extra);

				if($isAdded == false)
					UniteFunctionsUC::throwError("initByCreatorParams error: Wrong setting type: $type");
			break;
		}

		$this->addByCreatorParam_handleConditions($param);

		//set setting value
		if($inputValue !== null && $isUpdateValue === true)
			$this->updateSettingValue($name, $inputValue);

	}



    /**
     * sort params by categories
     */
    private function sortParamsByCats($arrCats, $params){

    	if(empty($arrCats))
    		$arrCats = array();

    	$arrOutput = array();

    	foreach($arrCats as $cat){
    		$catID = UniteFunctionsUC::getVal($cat, "id");
    		unset($cat["id"]);

    		$cat["params"] = array();

    		$arrOutput[$catID] = $cat;
    	}

    	foreach($params as $param){

    		$catID = UniteFunctionsUC::getVal($param, "__attr_catid__");

    		if(empty($catID))
    			$catID = "cat_general_general";

    		if(array_key_exists($catID, $arrOutput) == false)
    			$catID = "cat_general_general";

    		unset($param["__attr_catid__"]);

    		$sectionCounter = 0;

    		//add category
    		if(array_key_exists($catID, $arrOutput) == false){

    			//set category title
    			$catTitle = __("General", "unlimited-elements-for-elementor");

    			if($catID != "cat_general_general"){
    				$sectionCounter++;
    				$catTitle = __("Section ","unlimited-elements-for-elementor") . $sectionCounter;
    			}

    			$catTab = "content";

    			$arrOutput[$catID] = array(
    				"title"=>$catTitle,
    				"tab"=>$catTab,
    				"params"=>array()
    			);
    		}


    		$arrOutput[$catID]["params"][] = $param;
    	}

    	//remove empty categories
    	foreach($arrOutput as $catID => $cat){
    		if(empty($cat["params"]))
    			unset($arrOutput[$catID]);
    	}

    	return($arrOutput);
    }


	/**
	 * add edit widget button to advanced settings - if allowed
	 */
	private function addEditWidgetButton(){

    	if(is_admin() == false)
    		return(false);

    	if(class_exists("UniteProviderAdminUC") == false)
    		return(false);

    	if(UniteProviderAdminUC::$isUserHasCapability == false)
    		return(false);

    	$addonID =  $this->currentAddon->getID();

    	$urlEditAddon = HelperUC::getViewUrl_EditAddon($addonID, "", "tab=uc_tablink_html");

    	$arrParams = array();
    	$arrParams["url"] = $urlEditAddon;
    	$arrParams["newwindow"] = true;

    	$this->addButton("html_button_gotoaddon", __("Edit Widget HTML","unlimited-elements-for-elementor"), self::PARAM_NOTEXT, $arrParams);

	}


	/**
	 * add advanced settings section
	 */
	private function addAdvancedSection(){

		$this->addSap(esc_html__("Advanced", "unlimited-elements-for-elementor"), "advanced");

		$params = array();
		$params["description"] = __("Show widget data for debugging purposes. Please turn off this option when you releasing the widget.", "unlimited-elements-for-elementor");

		$this->addRadioBoolean("show_widget_debug_data", __("Show Widget Data For Debug", "unlimited-elements-for-elementor"), false, "Yes", "No", $params);

		$isItemsEnabled = $this->currentAddon->isHasItems();
		$hasPostsList = $this->currentAddon->isParamTypeExists(UniteCreatorDialogParam::PARAM_POSTS_LIST);

		//--------- debug type options ---------
		$debugTypeOptions = array();
		$debugTypeOptions["default"] = __('Default', 'unlimited-elements-for-elementor');

		if($hasPostsList == true)
			$isItemsEnabled = true;

		if($isItemsEnabled == true)
			$debugTypeOptions["items_only"] = __('Items Only', 'unlimited-elements-for-elementor');

		if($hasPostsList == true){
			$debugTypeOptions["post_titles"] = __('Posts Titles', 'unlimited-elements-for-elementor');
			$debugTypeOptions["post_meta"] = __('Posts Titles and Meta', 'unlimited-elements-for-elementor');
		}

		$debugTypeOptions["current_post_data"] = __('Current Post Data', 'unlimited-elements-for-elementor');
		$debugTypeOptions["settings_values"] = __('Show Settings Values', 'unlimited-elements-for-elementor');

		$hasDebugType = (count($debugTypeOptions) > 1);

		if($hasDebugType == true){
			$params = array();

			$debugTypeOptions = array_flip($debugTypeOptions);

			$this->addSelect("widget_debug_data_type", $debugTypeOptions,
				__("Debug Data Type", "unlimited-elements-for-elementor"), "default", $params);
		}

		$this->addControl("show_widget_debug_data", "widget_debug_data_type", "show", "true");

		$this->addEditWidgetButton();
	}

	/**
	 * add settings by creator params - works for single widget only (not for elementor)
	 */
	public function initByCreatorParams($arrParams, $arrCats = array()){

		if(empty($arrCats) === true){
			foreach($arrParams as $param){
				$this->addByCreatorParam($param);
			}

			return;
		}

		// put params with cats
		$arrParamsWithCats = $this->sortParamsByCats($arrCats, $arrParams);

		if(empty($arrParamsWithCats) === true)
			return;

		$hasPostsList = false;
		$postListParam = null;

//		$hasListing = false;
//		$listingParam = null;

		foreach($arrParamsWithCats as $catID => $arrCat){
			$title = UniteFunctionsUC::getVal($arrCat, "title");
			$tab = UniteFunctionsUC::getVal($arrCat, "tab");
			$arrParams = UniteFunctionsUC::getVal($arrCat, "params");

			$sapParams = $arrCat;

			unset($sapParams["params"]);

			$this->addSap($title, $catID, $tab);

			// handle sap conditions
			$sapParams["name"] = $catID;

			$this->addByCreatorParam_handleConditions($sapParams, true);

			foreach($arrParams as $param){
				$type = UniteFunctionsUC::getVal($param, "type");

				if($type === UniteCreatorDialogParam::PARAM_POSTS_LIST){
					$hasPostsList = true;
					$postListParam = $param;

//					$showImageSizes = UniteFunctionsUC::getVal($postListParam, "show_image_sizes");
//					$showImageSizes = UniteFunctionsUC::strToBool($showImageSizes);
//
//					if($showImageSizes == true)
//					$this->addImageSizesParam($postListParam);

					continue;
				}

//				if($type == UniteCreatorDialogParam::PARAM_LISTING){
//					$useFor = UniteFunctionsUC::getVal($param, "use_for");
//
//					switch($useFor){
//						case "remote":
//						case "filter":
//						break;
//						default:
//							$hasListing = true;
//							$listingParam = $param;
//						break;
//					}
//				}

				$this->addByCreatorParam($param);
			}
		}

		// add query settings section (post list) if exists

        if($hasPostsList == true){

			$forWooCommerce = UniteFunctionsUC::getVal($postListParam, "for_woocommerce_products");
			$forWooCommerce = UniteFunctionsUC::strToBool($forWooCommerce);

          	if($forWooCommerce == true)
				$labelPosts = esc_html__("Products Query", "unlimited-elements-for-elementor");
			else
				$labelPosts = esc_html__("Posts Query", "unlimited-elements-for-elementor");

			$this->addSap($labelPosts, "section_query");

			$this->addByCreatorParam($postListParam);
		}

		$this->addAdvancedSection();

		//add control by elementor conditions - from post list, terms list etc.

		$this->addControls_byElementorConditions();
	}

}
