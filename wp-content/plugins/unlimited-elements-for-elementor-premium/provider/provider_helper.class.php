<?php

class HelperProviderUC{

	private static $numQueriesStart = null;

	/**
	 * is activated by freemius
	 */
	public static function isActivatedByFreemius(){

		global $unl_fs;

		if(isset($unl_fs) === false)
			return (false);

		$isActivated = $unl_fs->is_paying();

		return ($isActivated);
	}

	/**
	 * get freemius account url
	 */
	public static function getFreemiusAccountUrl(){

		global $unl_fs;

		if(isset($unl_fs) === false)
			return "";

		$url = $unl_fs->get_account_url();

		return $url;
	}

	/**
	 * get sort filter default values
	 */
	public static function getSortFilterDefaultValues(){

		$arrValues = array();
		$arrValues["default"] = __("Default","unlimited-elements-for-elementor");
		$arrValues["meta"] = __("Meta Field","unlimited-elements-for-elementor");
		$arrValues["id"] = __("ID","unlimited-elements-for-elementor");
		$arrValues["date"] = __("Date","unlimited-elements-for-elementor");
		$arrValues["title"] = __("Title","unlimited-elements-for-elementor");
		$arrValues["price"] = __("Price","unlimited-elements-for-elementor");
		$arrValues["sale_price"] = __("Sale Price","unlimited-elements-for-elementor");
		$arrValues["sales"] = __("Number Of Sales","unlimited-elements-for-elementor");
		$arrValues["rating"] = __("Rating","unlimited-elements-for-elementor");
		$arrValues["name"] = __("Name","unlimited-elements-for-elementor");
		$arrValues["author"] = __("Author","unlimited-elements-for-elementor");
		$arrValues["modified"] = __("Last Modified","unlimited-elements-for-elementor");
		$arrValues["comment_count"] = __("Number Of Comments","unlimited-elements-for-elementor");
		$arrValues["rand"] = __("Random","unlimited-elements-for-elementor");
		$arrValues["none"] = __("Unsorted","unlimited-elements-for-elementor");
		$arrValues["menu_order"] = __("Menu Order","unlimited-elements-for-elementor");
		$arrValues["parent"] = __("Parent Post","unlimited-elements-for-elementor");

		$output = array();

		foreach($arrValues as $type=>$title){
			$output[] = array("type"=>$type,"title"=>$title);
		}

		return($output);
	}


	/**
	 * get sort filter repeater fields
	 */
	public static function getSortFilterRepeaterFields(){

		$settings = new UniteCreatorSettings();

		//--- field type -----

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;

		$arrSort = UniteFunctionsWPUC::getArrSortBy(true, true);

		$arrSort = array_flip($arrSort);

		$settings->addSelect("type", $arrSort, __("Field Type","unlimited-elements-for-elementor"),"default",$params);


		//--- field Title -----

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;

		$settings->addTextBox("title", "", __("Field Title","unlimited-elements-for-elementor"),$params);

		//--- meta field name -----

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["elementor_condition"] = array("type"=>"meta");

		$settings->addTextBox("meta_name", "", __("Meta Field Name","unlimited-elements-for-elementor"),$params);


		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;

		$arrMetaType = array("Text"=>"text","Number"=>"number");

		$settings->addSelect("meta_type", $arrMetaType, __("Meta Type","unlimited-elements-for-elementor"),"text",$params);


		return($settings);
	}


	/**
	 * get data for meta compare select
	 */
	public static function getArrMetaCompareSelect(){

		$arrItems = array();
		$arrItems["="] = "Equals";
		$arrItems["!="] = "Not Equals";
		$arrItems[">"] = "More Then";
		$arrItems["<"] = "Less Then";
		$arrItems[">="] = "More Or Equal";
		$arrItems["<="] = "Less Or Equal";
		$arrItems["LIKE"] = "LIKE";
		$arrItems["NOT LIKE"] = "NOT LIKE";

		$arrItems["IN"] = "IN";
		$arrItems["NOT IN"] = "NOT IN";
		$arrItems["BETWEEN"] = "BETWEEN";
		$arrItems["NOT BETWEEN"] = "NOT BETWEEN";

		$arrItems["EXISTS"] = "EXISTS";
		$arrItems["NOT EXISTS"] = "NOT EXISTS";

		return($arrItems);
	}


	/**
	 * get date select
	 */
	public static function getArrPostsDateSelect(){

		$arrDate = array(
			"all"=>__("All","unlimited-elements-for-elementor"),
			"this_day"=>__("Today","unlimited-elements-for-elementor"),
			"today"=>__("Past Day","unlimited-elements-for-elementor"),
			"yesterday"=>__("Past 2 days","unlimited-elements-for-elementor"),

			"past_from_today"=>__("Past From Today","unlimited-elements-for-elementor"),
			"past_from_yesterday"=>__("Past From Yesterday","unlimited-elements-for-elementor"),

			"this_week"=>__("This Week","unlimited-elements-for-elementor"),
			"next_week"=>__("Next Week","unlimited-elements-for-elementor"),
			"week"=>__("Past Week","unlimited-elements-for-elementor"),

			"month"=>__("Past Month","unlimited-elements-for-elementor"),
			"three_months"=>__("Past 3 Months","unlimited-elements-for-elementor"),
			"year"=>__("Past Year","unlimited-elements-for-elementor"),
			"this_month"=>__("This Month","unlimited-elements-for-elementor"),
			"next_month"=>__("Next Month","unlimited-elements-for-elementor"),

			"future"=>__("Future From Today","unlimited-elements-for-elementor"),
			"future_tomorrow"=>__("Future From Tomorrow","unlimited-elements-for-elementor"),
			"custom"=>__("Custom","unlimited-elements-for-elementor")
		);

		return($arrDate);
	}

	/**
	 * get select post status
	 */
	public static function getArrPostStatusSelect(){

		$arrStatus = array(
			"publish"=>__("Publish","unlimited-elements-for-elementor"),
			"future"=>__("Future","unlimited-elements-for-elementor"),
			"draft"=>__("Draft","unlimited-elements-for-elementor"),
			"pending"=>__("Pending Review","unlimited-elements-for-elementor"),
			"private"=>__("Private","unlimited-elements-for-elementor"),
			"inherit"=>__("Inherit","unlimited-elements-for-elementor"),
		);

		return($arrStatus);
	}

	/**
	 * get array for users order by select
	 */
	public static function getArrUsersOrderBySelect(){

		$arrOrderby = array(
			"default"=>__("Default", "unlimited-elements-for-elementor"),
			"ID"=>__("User ID", "unlimited-elements-for-elementor"),
			"display_name"=>__("Display Name", "unlimited-elements-for-elementor"),
			"name"=>__("Username", "unlimited-elements-for-elementor"),
			"login"=>__("User Login", "unlimited-elements-for-elementor"),
			"nicename"=>__("Nice Name", "unlimited-elements-for-elementor"),
			"email"=>__("Email", "unlimited-elements-for-elementor"),
			"url"=>__("User Url", "unlimited-elements-for-elementor"),
			"registered"=>__("Registered Date", "unlimited-elements-for-elementor"),
			"post_count"=>__("Number of Posts", "unlimited-elements-for-elementor")
		);

		return($arrOrderby);
	}

	/**
	 * get remote parent names
	 */
	public static function getArrRemoteParentNames($isSecond = false){

		$arrNames = array();

		if($isSecond == false)
			$arrNames["auto"] = __("Auto Detectable", "unlimited-elements-for-elementor");

		$arrNames["first"] = __("First", "unlimited-elements-for-elementor");
		$arrNames["second"] = __("Second", "unlimited-elements-for-elementor");
		$arrNames["third"] = __("Third", "unlimited-elements-for-elementor");
		$arrNames["fourth"] = __("Fourth", "unlimited-elements-for-elementor");

		if($isSecond == false)
			$arrNames["custom"] = __("Custom Name", "unlimited-elements-for-elementor");

		return($arrNames);
	}

	/**
	 * get remote parent names
	 */
	public static function getArrRemoteSyncNames(){

		$arrNames = array();
		$arrNames["group1"] = __("Sync Group 1", "unlimited-elements-for-elementor");
		$arrNames["group2"] = __("Sync Group 2", "unlimited-elements-for-elementor");
		$arrNames["group3"] = __("Sync Group 3", "unlimited-elements-for-elementor");
		$arrNames["group4"] = __("Sync Group 4", "unlimited-elements-for-elementor");
		$arrNames["group5"] = __("Sync Group 5", "unlimited-elements-for-elementor");
		$arrNames["group6"] = __("Sync Group 6", "unlimited-elements-for-elementor");
		$arrNames["group7"] = __("Sync Group 7", "unlimited-elements-for-elementor");
		$arrNames["group8"] = __("Sync Group 8", "unlimited-elements-for-elementor");
		$arrNames["group9"] = __("Sync Group 9", "unlimited-elements-for-elementor");
		$arrNames["group10"] = __("Sync Group 10", "unlimited-elements-for-elementor");

		return($arrNames);
	}

	/**
	 * get gallery defaults
	 */
	public static function getArrDynamicGalleryDefaults(){

		$urlImages = GlobalsUC::$urlPluginImages;

		$arrItems = array();

		$arrItems[] = array("id"=>0,"url"=>$urlImages."gallery1.jpg");
		$arrItems[] = array("id"=>0,"url"=>$urlImages."gallery2.jpg");
		$arrItems[] = array("id"=>0,"url"=>$urlImages."gallery3.jpg");
		$arrItems[] = array("id"=>0,"url"=>$urlImages."gallery4.jpg");
		$arrItems[] = array("id"=>0,"url"=>$urlImages."gallery5.jpg");
		$arrItems[] = array("id"=>0,"url"=>$urlImages."gallery6.jpg");

		return($arrItems);
	}



	/**
	 * get post addditions array from options
	 */
	public static function getPostAdditionsArray_fromAddonOptions($arrOptions){

		$arrAdditions = array();

		$enableCustomFields = UniteFunctionsUC::getVal($arrOptions, "dynamic_post_enable_customfields");
		$enableCustomFields = UniteFunctionsUC::strToBool($enableCustomFields);

		$enableCategory = UniteFunctionsUC::getVal($arrOptions, "dynamic_post_enable_category");
		$enableCategory = UniteFunctionsUC::strToBool($enableCategory);

		/*
		$enableTaxonomies = UniteFunctionsUC::getVal($this->addonOptions, "dynamic_post_enable_taxonomies");
		$enableTaxonomies = UniteFunctionsUC::strToBool($enableTaxonomies);
		*/

		if($enableCustomFields == true)
			$arrAdditions[] = GlobalsProviderUC::POST_ADDITION_CUSTOMFIELDS;

		if($enableCategory == true)
			$arrAdditions[] = GlobalsProviderUC::POST_ADDITION_CATEGORY;


		return($arrAdditions);
	}


	/**
	 * get post data additions
	 */
	public static function getPostDataAdditions($addCustomFields, $addCategory){

		$arrAdditions = array();

		$addCustomFields = UniteFunctionsUC::strToBool($addCustomFields);
		$addCategory = UniteFunctionsUC::strToBool($addCategory);

		if($addCustomFields == true)
			$arrAdditions[] = GlobalsProviderUC::POST_ADDITION_CUSTOMFIELDS;

		if($addCategory == true)
			$arrAdditions[] = GlobalsProviderUC::POST_ADDITION_CATEGORY;

		return($arrAdditions);
	}

    /**
     * get white label settings
     */
    public static function getWhiteLabelSettings(){

        $activateWhiteLabel = HelperUC::getGeneralSetting("activate_white_label");
        $activateWhiteLabel = UniteFunctionsUC::strToBool($activateWhiteLabel);

        if($activateWhiteLabel == false)
            return(null);

        $whiteLabelText = HelperUC::getGeneralSetting("white_label_page_builder");
        if(empty($whiteLabelText))
            return(null);

            $whiteLabelSingle = HelperUC::getGeneralSetting("white_label_single");
            if(empty($whiteLabelSingle))
                return(null);

            $arrSettings = array();
            $arrSettings["plugin_text"] = trim($whiteLabelText);
            $arrSettings["single"] = trim($whiteLabelSingle);

           return($arrSettings);
    }


	/**
	 * modify memory limit setting
	 */
	public static function modifyGeneralSettings_memoryLimit($objSettings){

		//modify memory limit

		$memoryLimit = ini_get('memory_limit');
		$htmlLimit = "<b> {$memoryLimit} </b>";

		$isGB = false;

		if(strpos($memoryLimit, "G") !== false)
			$isGB = true;

		$numLimit = (int)$memoryLimit;

		if($numLimit < 10 && $isGB == true)
			$numLimit *= 1024;

		if($numLimit < 512)
			$htmlLimit .= "<div style='color:red;font-size:13px;padding-top:4px;'> Recommended 512M, please increase php memory.</div>";

		$setting = $objSettings->getSettingByName("memory_limit_text");
		if(empty($setting))
			UniteFunctionsUC::throwError("Must be memory limit troubleshooter setting");

		$setting["text"] = str_replace("[memory_limit]", $htmlLimit, $setting["text"]);
		$objSettings->updateArrSettingByName("memory_limit_text", $setting);


		return($objSettings);
	}


	/**
	 * add all post types
	 */
	private static function modifyGeneralSettings_postType(UniteSettingsUC $objSettings){

		$arrPostTypes = UniteFunctionsWPUC::getPostTypesAssoc();

		if(count($arrPostTypes) <= 2)
			return($objSettings);

		unset($arrPostTypes["elementor_library"]);
		unset($arrPostTypes["uc_layout"]);
		unset($arrPostTypes[GlobalsProviderUC::POST_TYPE_LAYOUT]);

		$arrPostTypes = array_flip($arrPostTypes);

		$objSettings->updateSettingItems("post_types", $arrPostTypes);


		return($objSettings);
	}


	/**
	 * modify general settings
	 */
	private static function modifyGeneralSettings(UniteSettingsUC $objSettings){

		//update memory limit

		$objSettings = self::modifyGeneralSettings_postType($objSettings);


		return($objSettings);
	}


	/**
	 * check if layout editor plugin exists, or exists addons for it
	 */
	public static function isLayoutEditorExists(){

		$classExists = class_exists("LayoutEditorGlobals");
		if($classExists == true)
			return(true);

		return(false);
	}


	/**
	 * on plugins loaded, load textdomains
	 */
	public static function onPluginsLoaded(){

		load_plugin_textdomain("unlimited-elements-for-elementor", false, GlobalsUC::$pathWPLanguages);

		UniteCreatorWooIntegrate::initActions();
	}

	/**
	 * on php error message
	 */
	public static function onPHPErrorMessage($message, $error){

		$errorMessage = UniteFunctionsUC::getVal($error, "message");

		$file = UniteFunctionsUC::getVal($error, "file");
		$line = UniteFunctionsUC::getVal($error, "line");

		if(is_string($errorMessage))
			$message .= "Unlimited Elements Troubleshooting: \n<br><pre>{$errorMessage}</pre>";

		if(!empty($file))
			$message .= "in : <b>$file</b>";

		if(!empty($line))
			$message .= " on line <b>$line</b>";

		$arrDebug = HelperUC::getDebug();

		if(!empty($arrDebug))
			$message .= "<br>\nDebug: \n".print_r($arrDebug, true);
		else
			$message .= "<br>\n no other debug provided";

		$usage = memory_get_usage(true);

		$message .= "<br>\n Memory Usage: $usage";


		/*
		$arrTrace = debug_backtrace();

		if(!empty($arrTrace))
			$message .= "<br>\nTrace: \n".print_r($arrTrace, true);
		else
			$message .= "<br>\n no trace provided";
		*/

		return($message);
	}

	/**
	 * global init function that common to the admin and front
	 */
	public static function globalInit(){

		//disable deprecated warnings - global setting

		$disableDeprecated = HelperProviderCoreUC_EL::getGeneralSetting("disable_deprecated_warnings");
		$disableDeprecated = UniteFunctionsUC::strToBool($disableDeprecated);

		if($disableDeprecated == true)
			UniteFunctionsUC::disableDeprecatedWarnings();
		
		add_action("plugins_loaded", array("HelperProviderUC", "onPluginsLoaded"));

		$showPHPError = HelperProviderCoreUC_EL::getGeneralSetting("show_php_error");
		$showPHPError = UniteFunctionsUC::strToBool($showPHPError);

		if($showPHPError == true)
			add_filter("wp_php_error_message", array("HelperProviderUC", "onPHPErrorMessage"), 100, 2);

		//add_action("wp_loaded", array("HelperProviderUC", "onWPLoaded"));
	}

	/**
	 * on plugins loaded call plugin
	 */
	public static function onPluginsLoadedCallPlugins(){

		do_action("addon_library_register_plugins");

		UniteProviderFunctionsUC::doAction(UniteCreatorFilters::ACTION_EDIT_GLOBALS);


		//init woocommerce integration

		if(UniteCreatorWooIntegrate::isWooActive() == true){

			UniteCreatorWooIntegrate::initMiniCartIntegration();

		}


	}


	/**
	 * register plugins
	 */
	public static function registerPlugins(){

		add_action("plugins_loaded", array("HelperProviderUC","onPluginsLoadedCallPlugins"));

	}


	/**
	 * output custom styles
	 */
	public static function outputCustomStyles(){

	    $arrStyles = UniteProviderFunctionsUC::getCustomStyles();
	    if(!empty($arrStyles)){
	        echo "\n<!--   Unlimited Elements Styles  --> \n";

	        echo "<style type='text/css'>";

	        foreach ($arrStyles as $style){
	            echo UniteProviderFunctionsUC::escCombinedHtml($style)."\n";
	        }

	        echo "</style>\n";
	    }

	}


	/**
	 * print custom scripts
	 */
	public static function onPrintFooterScripts($isFront = false, $scriptType = "all"){

		//print custom styles
		if($scriptType != "js"){

			self::outputCustomStyles();
		}

		//print inline admin html

		if($isFront == false){

			//print inline html
			$arrHtml = UniteProviderFunctionsUC::getInlineHtml();
			if(!empty($arrHtml)){
				foreach($arrHtml as $html){
					echo UniteProviderFunctionsUC::escCombinedHtml($html);
				}
			}

		}

		//print custom JS script

		if($scriptType != "css"){

			$isSaparateScripts = HelperProviderCoreUC_EL::getGeneralSetting("js_saparate");
			$isSaparateScripts = UniteFunctionsUC::strToBool($isSaparateScripts);

			$arrScrips = UniteProviderFunctionsUC::getCustomScripts();
			$version = UNLIMITED_ELEMENTS_VERSION;

			if(!empty($arrScrips)){
				echo "\n<!--   Unlimited Elements $version Scripts --> \n";

				$arrScriptsOutput = array();
				$arrModulesOutput = array();

				foreach ($arrScrips as $key=>$script){

					$isModule = (strpos($key, "module_") !== false);

					if($isModule == true)
						$arrModulesOutput[$key] = $script;
					else
						$arrScriptsOutput[$key] = $script;
				}

				//print the scripts

				if(!empty($arrScriptsOutput)){

					if($isSaparateScripts == false){		//one script tag

						echo "<script type='text/javascript' id='unlimited-elements-scripts'>\n";

							foreach ($arrScriptsOutput as $script){

								echo $script."\n";
							}

						echo "</script>\n";
					}
					else{			//multiple script tags

						foreach ($arrScriptsOutput as $handle => $script){

							echo "\n<script type='text/javascript' id='{$handle}'>\n";

							echo $script."\n";

							echo "</script>\n";
						}

					}


				}

				//print the modules

				if(!empty($arrModulesOutput)){

					foreach($arrModulesOutput as $script){

						echo "<script type='module'>\n";
						echo $script."\n";
						echo "</script>\n";

					}

				}

			}//if not empty scripts

		}//if js

	}


	/**
	 * change elementor template to page, by it's name
	 */
	public static function changeElementorTemplateToPage($templateID, $pageName){

		$pageName = trim($pageName);

		UniteFunctionsUC::validateNotEmpty($pageName,__("Page Name", "unlimited-elements-for-elementor"));

		$arrUpdate = array();
		$arrUpdate["post_type"] = "page";
		$arrUpdate["post_title"] = $pageName;
		$arrUpdate["post_name"] = "";

		UniteFunctionsWPUC::updatePost($templateID, $arrUpdate);

	}

	/**
	 *
	 * get imported template links
	 */
	public static function getImportedTemplateLinks($templateID){

		$urlTemplate = get_post_permalink($templateID);
		$urlEditWithElementor = UniteFunctionsWPUC::getPostEditLink_editWithElementor($templateID);

		$response = array();
		$response["url"] = $urlTemplate;
		$response["url_edit"] = $urlEditWithElementor;

		return($response);
	}

	/**
	 * get post term for template
	 //arg1 - postID
	 //arg2 - taxonomy
	 //arg3 - term slug
	 */
	public static function getPostTermForTemplate($arg1, $arg2, $arg3){

		if(is_numeric($arg1) == false)
			return(false);

		//no slug found
		if(empty($arg3) || empty($arg2)){

			dmp("get_post_term. please enter second or third parameter - taxonomy or slug ");

			$post = get_post($arg1);
			$arrTerms = UniteFunctionsWPUC::getPostTerms($post);

			dmp("post terms: ");
			dmp($arrTerms);

			return(null);
		}

		$term = UniteFunctionsWPUC::getPostTerm($arg1,$arg2,$arg3);

		return($term);
	}

	/**
	 * remember the current query
	 */
	public static function startDebugQueries(){

		global $wpdb;
		$queries = $wpdb->queries;

		self::$numQueriesStart = count($queries);


	}

	/**
	 * print queries debug
	 */
	public static function printDebugQueries($showTrace = false){

		global $wpdb;
		$queries = $wpdb->queries;

		if(empty($queries)){
			dmp("queries not collected");
			exit();
		}

		$numQueries = count($queries);

		dmp("num querie found: ".$numQueries);

		$start = 0;
		if(!empty(self::$numQueriesStart))
			$start = self::$numQueriesStart;

		if(!empty($start) && $start == $numQueries){

			dmp("nothing changed since the start : $start");
			exit();
		}

		if(!empty($start)){

			$numToShow = $numQueries - $start;

			dmp("Showing $numToShow queries");
		}

		echo "<div style='font-size:12px;color:black;'>";

		foreach($queries as $index => $query){

			if($index < $start)
				continue;

			if(empty($query))
				continue;

			$color = "";

			$sql = $query[0];

			$strTrace = $query[2];


			if(strpos($sql, "wp_postmeta") !== false)
				$color = "red";

			echo("<div style='padding:10px;border-bottom:1px solid lightgray;color:$color'> $index: {$sql} </div>");

			if($showTrace){
				echo "<div>";
				dmp($strTrace);
				echo "<div>";
			}

		}

		echo "<div style='font-size:10px;'>";

	}

	/**
	 * check if user has some operations permissions
	 */
	public static function isUserHasOperationsPermissions(){

		$permission = HelperProviderCoreUC_EL::getGeneralSetting("edit_permission");

		$capability = "manage_options";
		if($permission == "editor")
			$capability = "edit_posts";

		$isUserHasPermission = current_user_can($capability);

		return($isUserHasPermission);
	}

	/**
	 * verify admin permisison of the plugin, use it before ajax actions
	 */
	public static function verifyAdminPermission(){

		$hasPermission = self::isUserHasOperationsPermissions();

		if($hasPermission == false)
			UniteFunctionsUC::throwError("The user don't have permission to do this operation");
	}

	/**
	 * check if addon revisions are enabled
	 */
	public static function isAddonRevisionsEnabled(){

		$isRevisionsEnabled = HelperProviderCoreUC_EL::getGeneralSetting("enable_revisions");
		$isRevisionsEnabled = UniteFunctionsUC::strToBool($isRevisionsEnabled);

		return $isRevisionsEnabled;
	}

	/**
	 * verify if addon revisions are enabled, use it before ajax actions
	 */
	public static function verifyAddonRevisionsEnabled(){

		$isRevisionsEnabled = self::isAddonRevisionsEnabled();

		if($isRevisionsEnabled === false)
			UniteFunctionsUC::throwError("The revisions are disabled.");
	}

	/**
	 * check if backgrounds enabled
	 */
	public static function isBackgroundsEnabled(){

		$isBackgroundsEnabled = HelperProviderCoreUC_EL::getGeneralSetting("enable_backgrounds");
		$isBackgroundsEnabled = UniteFunctionsUC::strToBool($isBackgroundsEnabled);

		return $isBackgroundsEnabled;
	}

	/**
	 * check if form entries are enabled
	 */
	public static function isFormEntriesEnabled(){

		if(GlobalsUnlimitedElements::$enableForms == false)
			return(false);

		$isEntriesEnabled = HelperProviderCoreUC_EL::getGeneralSetting("enable_form_entries");
		$isEntriesEnabled = UniteFunctionsUC::strToBool($isEntriesEnabled);

		return $isEntriesEnabled;
	}

	/**
	 * check if form logs saving is enabled
	 */
	public static function isFormLogsSavingEnabled(){

		$isLogsSavingEnabled = HelperProviderCoreUC_EL::getGeneralSetting("save_form_logs");
		$isLogsSavingEnabled = UniteFunctionsUC::strToBool($isLogsSavingEnabled);

		return $isLogsSavingEnabled;
	}

	/**
	 * get google connect credentials
	 */
	public static function getGoogleConnectCredentials(){

		$credentials = HelperProviderCoreUC_EL::getGeneralSetting("google_connect_credentials");
		$credentials = UniteFunctionsUC::decodeContent($credentials);

		return $credentials;
	}

	/**
	 * save google connect credentials
	 */
	public static function saveGoogleConnectCredentials($credentials){

		$settings["google_connect_credentials"] = UniteFunctionsUC::encodeContent($credentials);

		HelperUC::$operations->updateUnlimitedElementsGeneralSettings($settings);
	}

}
