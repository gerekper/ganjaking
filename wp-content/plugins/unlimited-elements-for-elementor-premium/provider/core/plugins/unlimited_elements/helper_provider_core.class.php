<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com / Valiano
 * @copyright (C) 2012 Unite CMS, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

class HelperProviderCoreUC_EL{
	
	public static $pathCore;
	public static $urlCore;
	public static $filepathGeneralSettings;
	public static $operations;
	public static $arrWidgetNames;
	public static $arrImages;
	public static $arrGlobalColors;
	private static $arrCacheElementorTemplate;
	private static $arrPostContentCache = array();
	private static $arrTemplatesCounter = array();
	private static $isInfiniteLoopCode = false;
	private static $arrAddedStyles = array();
	
	
	/**
	 * register post types of elementor library
	 */
	public static function registerPostType_UnlimitedLibrary(){
		
		$arrLabels = array(
						'name' => __( 'Unlimited Elements Library' ,"unlimited-elements-for-elementor"),
						'singular_name' => __( 'Unlimited Elements Library' ,"unlimited-elements-for-elementor"),
						'add_new_item' => __( 'Add New Template' ,"unlimited-elements-for-elementor"),
						'edit_item' => __( 'Edit Template' ,"unlimited-elements-for-elementor"),
						'new_item' => __( 'New Template' ,"unlimited-elements-for-elementor"),
						'view_item' => __( 'View Template' ,"unlimited-elements-for-elementor"),
						'view_items' => __( 'View Template' ,"unlimited-elements-for-elementor"),
						'search_items' => __( 'Search Template' ,"unlimited-elements-for-elementor"),
						'not_found' => __( 'No Template Found' ,"unlimited-elements-for-elementor"),
						'not_found_in_trash' => __( 'No Template found in trash' ,"unlimited-elements-for-elementor"),
						'all_items' => __( 'All Templates' ,"unlimited-elements-for-elementor")
				);
		
		$arrSupports = array(
			"title",
		//	"editor",
			"author",
			"thumbnail",
			"revisions",
			"page-attributes",
		);
		
		$arrPostType =	array(
							'labels' => $arrLabels,
							'public' => true,
							'rewrite' => false,
							
							'show_ui' => true,
							'show_in_menu' => true,		//set to true for show
							'show_in_nav_menus' => true,	//set to true for show
		
							'exclude_from_search' => true,
							'capability_type' => 'post',
							'hierarchical' => true,
							'description' => __("Unlimited Elements Template", "unlimited-elements-for-elementor"),
							'supports' => $arrSupports,
							//'show_in_admin_bar' => true		
					);
		
		
		register_post_type( GlobalsUnlimitedElements::POSTTYPE_UNLIMITED_ELEMENS_LIBRARY, $arrPostType);
		
		add_post_type_support( GlobalsUnlimitedElements::POSTTYPE_UNLIMITED_ELEMENS_LIBRARY, 'elementor' );
		
	}
	
	
	/**
	 * remove elementor cache file by post id
	 */
	public static function removeElementorPostCacheFile($postID){
		
		//remove post meta
		delete_post_meta($postID, "_elementor_css");
		
		$pathFiles = GlobalsUC::$path_images."elementor/css/";
		
		$filepath = $pathFiles."post-{$postID}.css";
		
		$fileExists = file_exists($filepath);
		
		if($fileExists == false)
			return(false);
			
		@unlink($filepath);
	}
	
	
	/**
	 * process param value by type
	 */
	public static function processParamValueByType($value, $type, $param){
		    		
    		switch($type){
    			
    			case UniteCreatorDialogParam::PARAM_RADIOBOOLEAN:
    			    
    				$trueValue = UniteFunctionsUC::getVal($param, "true_value");
    				$falseValue = UniteFunctionsUC::getVal($param, "false_value");
					
    				switch($value){
    					case $trueValue:		//don't change true or false
    					case $falseValue:
    					break;
    					case "yes":
    						$value = $trueValue;
    					break;
    					default:
    						$value = $falseValue;
    					break;
    				}
    				
    				
    			break;
    		}
    	
    		
		return($value);
	}
	
	
	/**
	 * get general settings values
	 */
	public static function getGeneralSettingsValues(){
		
		$arrValues = self::$operations->getCustomSettingsObjectValues(self::$filepathGeneralSettings, GlobalsUnlimitedElements::GENERAL_SETTINGS_KEY);
		
		return($arrValues);
	}
	
	/**
	 * check if instagram access token is saved
	 */
	public static function isInstagramSetUp(){
		
		$settings = HelperProviderCoreUC_EL::getGeneralSettingsValues();
		
		$token = UniteFunctionsUC::getVal($settings, "instagram_access_token");
		
		$isExists = !empty($token);
		
		return($isExists);
		
	}
	
	/**
	 * get general setting value
	 */
	public static function getGeneralSetting($name){
		
		$arrSettings = self::getGeneralSettingsValues();
		if(isset($arrSettings[$name]) == false)
			UniteFunctionsUC::throwError("Setting: $name does not exists in unlimited elements");
		
		$value = $arrSettings[$name];
		
		return($value);
	}
		
	
	/**
	 * register widget by it's name for outside uses
	 */
	public static function registerWidgetByName($name){
		
		$isAlphaNumeric = UniteFunctionsUC::isAlphaNumeric($name);
		if($isAlphaNumeric == false)
			return(false);
			
		$className = "UCAddon_".$name;
        
		if(class_exists($className) == true)
			return(false);
        
		$code = "class {$className} extends UniteCreatorElementorWidget{}";
		eval($code);
        
		$widget = new $className();
        \Elementor\Plugin::instance()->widgets_manager->register($widget);
		
	}
		
	
	/**
	 * get imported elementor templates
	 * if param given. stop after this param reached
	 */
	public static function getImportedElementorTemplates($paramName = null){
		
		$args = array();
		$args["post_type"] = GlobalsUnlimitedElements::POSTTYPE_ELEMENTOR_LIBRARY;
		$args["posts_per_page"] = 1000;
		$args["meta_key"] = GlobalsUnlimitedElements::META_TEMPLATE_SOURCE;
		$args["meta_value"] = "unlimited";
		
		$arrPosts = get_posts($args);
		
		$arrImportedTemplates = array();
		
		foreach($arrPosts as $post){
						
			$postID = $post->ID;
			
			$sourceName = get_post_meta($postID, GlobalsUnlimitedElements::META_TEMPLATE_SOURCE_NAME, true);
			
			if(empty($sourceName))
				continue;
			
			$arrImportedTemplates[$sourceName] = $postID;
			
			if($sourceName == $paramName)
				break;
		}
		
		return($arrImportedTemplates);
	}
	
	
	/**
	 * get elementor templates list
	 */
	public static function getArrElementorTemplatesShort(){
		
		if(!empty(self::$arrCacheElementorTemplate))
			return(self::$arrCacheElementorTemplate);
		
		
		//save db access where front output
			
		if(GlobalsUC::$is_admin == false)
			return(array());
			
		//our db avoid some filters like polylang
		try{
			$db = HelperUC::getDB();
			
			$tablePosts = UniteProviderFunctionsUC::$tablePosts;
			$sql = "select * from $tablePosts where post_type in ('elementor_library','jet-engine') and post_status='publish'";
			$arrPosts = $db->fetchSql($sql);
			
		
		}catch(Exception $e){
			$arrPosts = array();
		}
		
		if(empty($arrPosts))
			return(array());
		
		$arrItems = array();
		$arrDuplicates = array();
		
		foreach($arrPosts as $post){
			
			$postID = UniteFunctionsUC::getVal($post, "ID");
			$title = UniteFunctionsUC::getVal($post, "post_title");
			$slug = UniteFunctionsUC::getVal($post, "post_name");
			$postType = UniteFunctionsUC::getVal($post, "post_type");
						
			$templateType = get_post_meta($postID, "_elementor_template_type", true);
			
			//jet addition
			if($postType == "jet-engine"){
				$postID = "jet_".$postID;
			}
			
			if(is_string($templateType) == false)
				$templateType = "template";
			
			switch($templateType){
				case "single-post":
					$templateType = "single";
				break;
				case "wp-page":
					$templateType = "page";
				break;
				case "kit":
					continue(2);
				break;
			}
			
			$templateType = ucfirst($templateType);
							
			if(isset($arrDuplicates[$title]))
				$arrDuplicates[$title] = true;
			else
				$arrDuplicates[$title] = false;
			
			$arrItems[$postID] = array($slug, $title, $templateType);
		}
		
		//fix the duplicates
		$arrShort = array();
		foreach($arrItems as $id=>$arr){
			
			$name = $arr[0];
			$title = $arr[1];
			$type = $arr[2];
			
			$isDuplicate = $arrDuplicates[$title];
			
			if($isDuplicate == true)
				$showTitle = "$title ($name) | $type";
			else
				$showTitle = "$title | $type";
			
			$arrShort[$id] = $showTitle;
		}
		
		asort($arrShort);
				
		self::$arrCacheElementorTemplate = $arrShort;
		
		return($arrShort);
	}
	
	
	/**
	 * get imported elementor template by name
	 */
	public static function getImportedElementorTemplateID($name){
		
		$arrTemplates = self::getImportedElementorTemplates($name);
				
		$templateID = UniteFunctionsUC::getVal($arrTemplates, $name);
				
		if(empty($templateID))
			return(null);
		
		return($templateID);
	}
	
	
	/**
	 * get addons list from elementor content
	 */
	private static function getWidgetNamesFromElementorContent_iterate($arrContent){
		
		if(is_array($arrContent) == false)
			return(false);
		
		foreach($arrContent as $item){
			
			if(is_array($item) == false)
				continue;
			
			$type = UniteFunctionsUC::getVal($item, "elType");
						
			if($type == "widget"){
				
				$widgetName = UniteFunctionsUC::getVal($item, "widgetType");
				
				if(strpos($widgetName, "ucaddon_") !== false){
										
					$addonName = str_replace("ucaddon_", "", $widgetName);
					
					self::$arrWidgetNames[$addonName] = true;
				}
				
			}
			
			self::getWidgetNamesFromElementorContent_iterate($item);
				
		}
		
	}
	
	/**
	 * get addons names from elementor content
	 */
	public static function getWidgetNamesFromElementorContent($arrContent){
		
		self::$arrWidgetNames = array();
		
		if(is_array($arrContent) == false)
			return(self::$arrWidgetNames);
		
		self::getWidgetNamesFromElementorContent_iterate($arrContent);
				
		return(self::$arrWidgetNames);
	}
	
	/**
	 * include hover animations styles
	 */
	public static function includeHoverAnimationsStyles(){
		
		if(class_exists("\Elementor\Control_Hover_Animation") == false)
			return(false);
		
		if(method_exists("\Elementor\Control_Hover_Animation", "get_assets") == false)
			return(false);
				
		$assets = \Elementor\Control_Hover_Animation::get_assets(array("something"));
		
		if(empty($assets))
			return(false);
			
		$arrStyles = UniteFunctionsUC::getVal($assets, "styles");
		
		foreach($arrStyles as $handle){
			wp_enqueue_style("e-animations");
		}
		
	}
	
	
	/**
	 * get hover animation classes
	 */
	public static function getHoverAnimationClasses($addNotChosen = false){
		
		if(class_exists("\Elementor\Control_Hover_Animation") == false)
			return(array());
		
		$arrAnimations = \Elementor\Control_Hover_Animation::get_animations();
		
		$arrAnimationsNew = array();
		
		if($addNotChosen == true)
			$arrAnimationsNew[""] = __("Not Chosen","unlimited-elements-for-elementor");
		
		foreach($arrAnimations as $key=>$value)
			$arrAnimationsNew["elementor-animation-".$key] = $value;
		
		return($arrAnimationsNew);
	}
	
	/**
	 * get terms picker control
	 */
	public static function getElementorControl_TermsPickerControl($label,$description = null, $condition = null){
		
		$arrControl = array();
		$arrControl["type"] = "uc_select_special";
		$arrControl["label"] = $label;
		$arrControl["default"] = "";
		$arrControl["options"] = array();
		$arrControl["label_block"] = true;
		
		$placeholder = "All--Terms";
		
		$loaderText = __("Loading Data...", "unlimited-elements-for-elementor");
		$loaderText = UniteFunctionsUC::encodeContent($loaderText);
		
		$arrControl["placeholder"] = "All--Terms";

		if(!empty($description))
			$arrControl["description"] = $description;
		
		if(!empty($condition))
			$arrControl["condition"] = $condition;
		
		$addParams = " data-settingtype=post_ids data-datatype=terms data-placeholdertext={$placeholder} data-loadertext=$loaderText data-taxonomyname=taxonomy_taxonomy class=unite-setting-special-select";
		
		$arrControl["addparams"] = $addParams;
		
		return($arrControl);
	}
	
	/**
	 * get elementor data from post id
	 */
	public static function getElementorContentByPostID($postID){
		
		$postID = (int)$postID;
		
		$strData = get_post_meta($postID,"_elementor_data",true);
		
		if(empty($strData))
			return(false);
			
		$arrData = UniteFunctionsUC::jsonDecode($strData);
		
		return($arrData);
	}
	
	/**
	  * get widget elementor from content
	 */
	public static function getArrElementFromContent($arrContent, $elementID){
		
		if(is_array($arrContent) == false)
			return(null);
			
		if(isset($arrContent["elType"]) && isset($arrContent["id"]) && $arrContent["id"] == $elementID)
			return($arrContent);
			
		foreach($arrContent as $child){
			
			if(is_array($child) == false)
				continue;
			
			$arrElement = self::getArrElementFromContent($child, $elementID);
			
			if(!empty($arrElement))
				return($arrElement);
		}
		
		return(null);
	}

	/**
	 * get addon settings from elementor content
	 */
	public static function getAddonValuesWithDataFromContent($arrContent, $elementID){
		
		$addon = self::getAddonWithDataFromContent($arrContent, $elementID);
		
		$arrValues = $addon->getProcessedMainParamsValues();
		
		return($arrValues);
	}
	
	
	/**
	  * get widget elementor from content
	 */
	public static function getAddonWithDataFromContent($arrContent, $elementID){
		
		$arrElement = self::getArrElementFromContent($arrContent, $elementID);
				
		if(empty($arrElement)){
			UniteFunctionsUC::throwError("Elementor Widget with id: $elementID not found");
		}
		
		$type = UniteFunctionsUC::getVal($arrElement, "elType");
		
		if($type != "widget")
			UniteFunctionsUC::throwError("The element is not a widget");
		
		$widgetType = UniteFunctionsUC::getVal($arrElement, "widgetType");
		
		if(strpos($widgetType, "ucaddon_") === false){
			
			if($widgetType == "global")
				UniteFunctionsUC::throwError("The widget can't be global widget. Please change the grid to regular widget.");
			
			UniteFunctionsUC::throwError("Cannot output widget content for widget: $widgetType");
		}

		$arrSettingsValues = UniteFunctionsUC::getVal($arrElement, "settings");
		
		$widgetName = str_replace("ucaddon_", "", $widgetType);
		
		$addon = new UniteCreatorAddon();
		$addon->initByAlias($widgetName, GlobalsUC::ADDON_TYPE_ELEMENTOR);
		
		$addon->setParamsValues($arrSettingsValues);
		
		return($addon);		
	}
		
	
	/**
	 * put post content, or render with elementor
	 */
	public static function getPostContent($postID, $content=""){
		
		if(empty($postID))
			return(false);
			
		if(is_numeric($postID) == false)
			return($content);
					
		//protection against infinate loop
		
		if(isset(self::$arrPostContentCache[$postID]))
			return(self::$arrPostContentCache[$postID]);
		
		$elementorTemplateType = get_post_meta($postID,"_elementor_template_type",true);
		
		//not elementor - regular content
		if(empty($elementorTemplateType)){
		
			if(!empty($content)){
				
				self::$arrPostContentCache[$postID]	= $content;
				return($content);
			}
				
			$post = get_post($postID);
			$content = UniteFunctionsWPUC::getPostContent($post);
				
			self::$arrPostContentCache[$postID]	= $content;
			
			return($content);
		}
		
		//elementor content
		
		$content = self::getElementorTemplate($postID);
		
		self::$arrPostContentCache[$postID]	= $content;
		
		return($content);
	}
	
	/**
	 * get elementor condition from param
	 */
	public static function paramToElementorCondition($param){
				
		$condition = array();
		
		$enableCondition = UniteFunctionsUC::getVal($param, "enable_condition");
		if($enableCondition == true){
			
			$attribute = UniteFunctionsUC::getVal($param, "condition_attribute");
			$operator = UniteFunctionsUC::getVal($param, "condition_operator");
			$value = UniteFunctionsUC::getVal($param, "condition_value");
			
			if(is_array($value) && count($value) == 1){
				$value = $value[0];
				if($operator == "not_equal")
					$value = "!".$value;
			}
			
			$condition[$attribute] = $value;
		}
		
		
		if(empty($condition))
			return($condition);
		
		$attribute2 = UniteFunctionsUC::getVal($param, "condition_attribute2");
		
		if(!empty($attribute2)){
			
			$operator = UniteFunctionsUC::getVal($param, "condition_operator2");
			$value = UniteFunctionsUC::getVal($param, "condition_value2");
			
			if(is_array($value) && count($value) == 1){
				$value = $value[0];
				if($operator == "not_equal")
					$value = "!".$value;
			}
			
			$condition[$attribute2] = $value;
		}
		
		
		return($condition);
	}
	
	/*
	 * get elementor breakpoints
	 */
	public static function getBreakPoints($onlyCustom = true){
		
		$arrBreakpoints = Elementor\Plugin::$instance->breakpoints->get_breakpoints();
		
		if(empty($arrBreakpoints))
			return(array());
		
		$output = array();
			
		foreach($arrBreakpoints as $objBreakpoint){
			
			//$arrBreakpoint = (array)$objBreakpoint;
						
			$name = $objBreakpoint->get_name();
			
			$isEnabled = $objBreakpoint->is_enabled();
						
			if($isEnabled == false)
				continue;
			
			switch($name){
				case "mobile":
				case "tablet":
					continue(2);
				break;
			}
			
			$value = $objBreakpoint->get_value();
			
			$output = array();
			$output[$name] = $value;
		}
		
		
		return($output);
	}
	
	private static function ______LISTING________(){}
	
	/**
	 * get listing item title
	 */
	private static function getListingItemTitle($type, $item){
		
		switch($type){
			case "post":
				$title = $item->post_title;
			break;
			case "term":
				$title = $item->name;
			break;
			default:
				$title = "item";
			break;
		}
		
		return($title);
	}
	
	
	
	/**
	 * put elementor template
	 * protection against inifinite loop
	 */
	public static function putElementorTemplate($templateID, $mode = null){
		
		if(empty($templateID))
			return(false);
		 
		$numTemplates = 250;
		
		$isWpmlExists = UniteCreatorWpmlIntegrate::isWpmlExists();
				
		//get right template
		if($isWpmlExists == true){
		
			$template = get_post($templateID);
			
			$templateID = apply_filters( 'wpml_object_id', $templateID, $template->post_type, true);
		}

		
		if(!isset(self::$arrTemplatesCounter[$templateID]))
			self::$arrTemplatesCounter[$templateID] = 0;
		
		self::$arrTemplatesCounter[$templateID]++;
		
		if(self::$arrTemplatesCounter[$templateID] >= $numTemplates){
			
			$text = __("Infinite Template Loop Found: $templateID","unlimited-elements-for-elementor");
			
			dmp($text);
			
			if(self::$isInfiniteLoopCode == false){
				echo "<script>alert('Infinite Template Loop Found with id: $templateID')</script>";
			}
			
			self::$isInfiniteLoopCode = true;
			
			return($text);
		}
		
		if($mode == "no_ue_widgets")	//in dynamic popup for example
			GlobalsProviderUC::$isUnderNoWidgetsToDisplay = true;
		
		$output = self::getElementorTemplate($templateID);


		if($mode == "no_ue_widgets")
			GlobalsProviderUC::$isUnderNoWidgetsToDisplay = false;
		
		echo $output;
	}
	
	
	/**
	 * get jet template
	 */
	public static function getJetTemplateListingItem($templateID, $post){
		
		if(function_exists("jet_engine") == false)
			return(false);
		
		jet_engine()->frontend->set_listing( $templateID );
		$content = jet_engine()->frontend->get_listing_item( $post );
		
		return($content);
	}
	
	/**
	 * put elementor template
	 */
	public static function getElementorTemplate($templateID, $withCss = false){
		
		if(empty($templateID) || is_numeric($templateID) == false)
			return("");
		
		
		$output = \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $templateID, $withCss);
		
		
		return($output);
	}
	
	
	/**
	 * put the post listing template
	 */
	public static function putListingItemTemplate_post($post, $templateID, $widgetID, $listingType = "elementor", $withCss = false){
		
		if(empty($widgetID))
			return(false);
		
		if(empty($templateID))
			return(false);
		
			
		//change the template ID according the language for wpml
		
		$isWpmlExists = UniteCreatorWpmlIntegrate::isWpmlExists();
			
		//get right template
		if($isWpmlExists == true){
		
			$template = get_post($templateID);
			
			$templateID = apply_filters( 'wpml_object_id', $templateID, $template->post_type, true);
		}

		
		//--------------------
				
		global $wp_query;
		
		//empty the infinite loop protection
		
		self::$arrTemplatesCounter = array();
		
		$originalPost = $GLOBALS['post'];
		
		//backup the original querified object
		$originalQueriedObject = $wp_query->queried_object;
		$originalQueriedObjectID = $wp_query->queried_object_id;
		
		$postID = $post->ID;
		
		//set the post qieried object
		
		$wp_query->queried_object = $post;
		$wp_query->queried_object_id = $postID;
			
		$GLOBALS['post'] = $post;
		
		//set author data
		
		UniteFunctionsWPUC::setGlobalAuthorData($post);
		
		//fix for jet engine
		
		$isJetExists = UniteCreatorPluginIntegrations::isJetEngineExists();
		
		if($isJetExists == true)
			do_action("the_post", $post, false);
					
		//set the flag on dynamic ajax
					
		if(GlobalsProviderUC::$isUnderAjax == true){
			GlobalsProviderUC::$isUnderAjaxDynamicTemplate = true;
		}
		
		//set elementor document
		
		$isElementorProActive = HelperUC::isElementorProActive();
		
		$documentToChange = null;
				
		if($isElementorProActive == true){
			
			$currentDocument = \ElementorPro\Plugin::elementor()->documents->get_current();
			
			$documentToChange = \Elementor\Plugin::$instance->documents->get( $postID );
			
			//do it from elementorIntegrate class
			//\ElementorPro\Plugin::elementor()->documents->switch_to_document( $document );
			
		}
		
		GlobalsUnlimitedElements::$renderingDynamicData = array(
			"post_id"=>$postID, 
			"template_id" => $templateID, 
			"widget_id"=>$widgetID,
			"doc_to_change"=>$documentToChange
		);
		
		//handle the additional css files
		
		if(GlobalsProviderUC::$isUnderAjax == true){
			
			$objStyles = wp_styles();
			$arrHandles = $objStyles->queue;
		}
		
		
		if($listingType == "jet")
			$htmlTemplate = self::getJetTemplateListingItem($templateID, $post);
		else
			$htmlTemplate = self::getElementorTemplate($templateID, $withCss);
		
		
		//handle the additional css files
		
		if(GlobalsProviderUC::$isUnderAjax == true){
			
			$objStyles2 = wp_styles();
			$arrHandles2 = $objStyles->queue;
			
			$arrDiff = array_diff($arrHandles2, $arrHandles);
			
			if(!empty($arrDiff))
			foreach($arrDiff as $handleToAdd){
				
				if(isset(self::$arrAddedStyles[$handleToAdd]))
					continue;
				
				self::$arrAddedStyles[$handleToAdd] = true;
				
				$objStyle = UniteFunctionsUC::getVal($objStyles2->registered, $handleToAdd);
				
				if(empty($objStyle))
					continue;

				$srcStyle = $objStyle->src;
				$srcStyle = esc_url($srcStyle);
				
				$htmlStyle = "<link rel=\"stylesheet\" href=\"{$srcStyle}\">\n";
				
				$htmlTemplate = $htmlStyle.$htmlTemplate;
			}
			
		}
			
		
			
		//add one more class
		
		$source = "class=\"elementor elementor-{$templateID}";
		$dest = "{$source} uc-post-$postID";
		
		$htmlTemplate = str_replace($source, $dest, $htmlTemplate);
		
		$htmlTemplate = do_shortcode($htmlTemplate);
		
		echo $htmlTemplate;
				
		GlobalsUnlimitedElements::$renderingDynamicData = null;
		
		GlobalsProviderUC::$isUnderAjaxDynamicTemplate = false;
		
		
		//restore the original queried object
		
		$wp_query->queried_object = $originalQueriedObject;
		$wp_query->queried_object_id = $originalQueriedObjectID;
		$GLOBALS['post'] = $originalPost;

		if($isElementorProActive == true){
			
			\ElementorPro\Plugin::elementor()->documents->switch_to_document( $currentDocument );
		}
		
		GlobalsProviderUC::$isUnderDynamicTemplateLoop = false;
		
	}
	
	/**
	 * put dynamic loop element style if exists
	 */
	public static function putDynamicLoopElementStyle($element){
		
		if(empty(GlobalsUnlimitedElements::$renderingDynamicData))
			return(false);
		
		$postID = UniteFunctionsUC::getVal(GlobalsUnlimitedElements::$renderingDynamicData, "post_id");
		$templateID = UniteFunctionsUC::getVal(GlobalsUnlimitedElements::$renderingDynamicData, "template_id");
		$widgetID = UniteFunctionsUC::getVal(GlobalsUnlimitedElements::$renderingDynamicData, "widget_id");
		
		if(empty($postID))
			return(false);
			
		if(empty($templateID))
			return(false);
					
		$elementID = $element->get_ID();
  		$dynamicSettings = $element->get_settings( '__dynamic__' );
		
  		if(empty($dynamicSettings))
  			return(false);
  		  			
 		$arrControls = $element->get_controls();
  		if(empty($arrControls))
  			return(false);
  			
  		$arrControls = array_intersect_key($arrControls, $dynamicSettings);
 		
  		if(empty($arrControls))
  			return(false);
  		
  		if(is_array($arrControls) == false)
  			return(false);
  		
  		if(isset($arrControls[0]) && is_string($arrControls[0]))
  			return(false);
  		
  		unset($dynamicSettings["link"]);
  		unset($dynamicSettings["eael_cta_btn_link"]);	//some protection
		
  		//unset dynamic settings
  		foreach($dynamicSettings as $key => $setting){
  			
  			$arrControl = UniteFunctionsUC::getVal($arrControls, $key);
  			
  			$type = UniteFunctionsUC::getVal($arrControl, "type");
  			  			
  			switch($type){
  				case "url":
  					  					
  					unset($dynamicSettings[$key]);
  				break;
  			}
  			  
  		}
  		
  		if(empty($dynamicSettings))
  			return(false);
  		
  		try{
  			
  			
  			$settings = @$element->parse_dynamic_settings( $dynamicSettings, $arrControls);
			
  		}catch(Exception $e){
  			return(false);
  		}
  		
  		if(empty($settings))
			return(false);
		
					
  		$strStyle = "";
  		
  		$wrapperCssKey = "#{$widgetID} .uc-post-{$postID}.elementor-{$templateID} .elementor-element.elementor-element-{$elementID}";
  		
  		foreach($arrControls as $controlName => $control){
  			  			
  			$arrValues = UniteFunctionsUC::getVal($settings, $controlName);
  			
  			if(empty($arrValues))
  				continue;
  			
  			if(is_string($arrValues))
  				$value = $arrValues;
  			else{
  				
  				$value = UniteFunctionsUC::getVal($arrValues, "url");
  			}
  			
  			if(empty($value))
  				continue;
  			
  			$arrSelectors = UniteFunctionsUC::getVal($control, "selectors");
  			if(empty($arrSelectors))
  				continue;
  			
  			$responsive = UniteFunctionsUC::getVal($control, "responsive");
  			
  			$maxRes = UniteFunctionsUC::getVal($responsive, "max");
  			  			
  			//modify the selectors
  			
  			foreach($arrSelectors as $cssKey=>$cssValue){
				  				
  				$cssKey = str_replace("{{WRAPPER}}", $wrapperCssKey, $cssKey);
  				
  				if(strpos($cssValue, "{{URL}}") !== false){
  					$cssValue = str_replace("{{URL}}", $value, $cssValue);
  					
  					$cssValue = str_replace("{{VALUE}}", "", $cssValue);
  					
  				}else{
  					
  					$cssValue = str_replace("{{VALUE}}", $value, $cssValue);  					
  				}
  				
  				//clear other placeholders
  				
  				$cssValue = str_replace("{{UNIT}}", "", $cssValue);
				 
  				if(!empty($strStyle))
  					$strStyle .= "\n";
  				
  				$styleToAdd = $cssKey."{{$cssValue}}"; 
  				
  				if($maxRes == "tablet")
  					$styleToAdd = HelperHtmlUC::wrapCssMobile($styleToAdd, true);
  				else
  				if($maxRes == "mobile")
  					$styleToAdd = HelperHtmlUC::wrapCssMobile($styleToAdd);
  				  					
  				$strStyle .= $styleToAdd."\n";
  				
  			}
  			
  		}
  		 
  		
  		if(empty($strStyle))
  			return(false);
		
  			
  		//output the style
  		
  		$strOutput = "<style type='text/css' data-type='dynamic-loop-item'>\n";
  		$strOutput .= $strStyle;
  		$strOutput .= "</style>";
		
  		echo $strOutput;		
	}
	
	
	/**
	 * put listing loop
	 */
	public static function putListingItemTemplate($item, $templateID, $widgetID){
		
		$objFilters = new UniteCreatorFiltersProcess();
		$isFrontAjax = $objFilters->isFrontAjaxRequest();
		
		
		$listingType = "elementor";
		
		//jet determine
		if(is_string($templateID) == true && strpos($templateID, "jet_") !== false){
			$templateID = (int)str_replace("jet_", "", $templateID);
			$listingType = "jet";			
		}
		
		//set type
		
		$type = null;
		
		if($item instanceof WP_Post)
			$type = "post";
		else if($item instanceof WP_Term)
			$type = "term";
		
		if(empty($type)){
			dmp("wrong listing type, can't output");
			return(false);
		}
		
		
		if(empty($templateID)){
			
			$title = self::getListingItemTitle($type, $item);
			
			dmp("$type - $title - no template id");
			return(false);
		}
		
		$withCss = false;
		if($isFrontAjax == true)
			$withCss = true;
		
		//template output
		if($type == "post")
			self::putListingItemTemplate_post($item, $templateID, $widgetID, $listingType, $withCss);
		else
			echo "output term";
		
	}
	
	
	
	/**
	 * global init
	 */
	public static function globalInit(){
		
		self::$operations = new UCOperations();
				
		//set path and url
		self::$pathCore = dirname(__FILE__)."/";
		self::$urlCore = HelperUC::pathToFullUrl(self::$pathCore);
		
		self::$filepathGeneralSettings = self::$pathCore."settings/general_settings_el.xml";
		
		GlobalsProviderUC::$pluginName = "unlimited_elementor";
		
		GlobalsUC::$currentPluginTitle = GlobalsUnlimitedElements::PLUGIN_TITLE;
		
		//add_action("init", array("HelperProviderCoreUC_EL", "onInitAction"));
		
	}
		
	
}