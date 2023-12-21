<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2012 Unite CMS, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

class UniteCreatorSettings extends UniteCreatorSettingsWork{

	
	/**
	 * add settings provider types
	 */
	protected function addSettingsProvider($type, $name,$value,$title,$extra ){
		
		$isAdded = false;
		
		return($isAdded);
	}
	
	
	/**
	 * show taxanomy
	 */
	private function showTax(){
										
		$showTax = UniteFunctionsUC::getGetVar("maxshowtax", "", UniteFunctionsUC::SANITIZE_NOTHING);
		$showTax = UniteFunctionsUC::strToBool($showTax);
		
		if($showTax == true){
			
			$args = array("taxonomy"=>"");
			$cats = get_categories($args);
			
			$arr1 = UniteFunctionsWPUC::getTaxonomiesWithCats();
			
			$arrPostTypes = UniteFunctionsWPUC::getPostTypesAssoc();
			$arrTax = UniteFunctionsWPUC::getTaxonomiesWithCats();
			$arrCustomTypes = get_post_types(array('_builtin' => false));
			
			$arr = get_taxonomies();
			
			$taxonomy_objects = get_object_taxonomies( 'post', 'objects' );
   			dmp($taxonomy_objects);
   			
			dmp($arrCustomTypes);
			dmp($arrPostTypes);
			exit();
		}
		
	}
	
	/**
	 * add template picker
	 */
	protected function addTemplatePicker($name,$value,$title,$extra){
		
        $arrTemplates = HelperProviderCoreUC_EL::getArrElementorTemplatesShort();
		$arrTemplates = UniteFunctionsUC::addArrFirstValue($arrTemplates, __("[No Template Selected]","unlimited-elements-for-elementor"),"__none__");
		
		$arrTemplates = array_flip($arrTemplates);
		
		$params = array();
		$params["origtype"] = "select2";
		
		if(empty($title))
			$title = __("Choose Template", "unlimited-elements-for-elementor");
		
		$this->addSelect($name."_templateid", $arrTemplates, $title ,"__none__", $params);
		
		//get the edit template button
		
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_RAW_HTML;
		$params["html"] = "<div class='uc-edit-template-button'><a href='javascript:void(0)' class='uc-edit-template-button__link unite-setting-special-select' data-settingtype='template_button' style='display:none' data-selectid='{$name}_templateid' target='_blank'>Edit Template</a></div>";
		
		
		
		$this->addTextBox($name."_templateid_button", "", $title , $params);
		
	}
	
	
	/**
	 * get categories from all post types
	 */
	protected function getCategoriesFromAllPostTypes($arrPostTypes){
		
		if(empty($arrPostTypes))
			return(array());

		$arrAllCats = array();
		$arrAllCats[__("All Categories", "unlimited-elements-for-elementor")] = "all";
		
		foreach($arrPostTypes as $name => $arrType){
		
			if($name == "page")
				continue;
			
			$postTypeTitle = UniteFunctionsUC::getVal($arrType, "title");
			
			$cats = UniteFunctionsUC::getVal($arrType, "cats");
			
			if(empty($cats))
				continue;
			
			foreach($cats as $catID => $catTitle){
				
				if($name != "post")
					$catTitle = $catTitle." ($postTypeTitle type)";
				
				$arrAllCats[$catTitle] = $catID;
			}
			
		}
		
		
		return($arrAllCats);
	}
	
	
	
	/**
	 * get taxonomies array for terms picker
	 */
	private function addPostTermsPicker_getArrTaxonomies($arrPostTypesWithTax){
		
		$arrAllTax = array();
		
		//make taxonomies data
		$arrTaxonomies = array();
		foreach($arrPostTypesWithTax as $typeName => $arrType){
			
			$arrItemTax = UniteFunctionsUC::getVal($arrType, "taxonomies");
						
			$arrTaxOutput = array();
			
			//some fix that avoid double names
			$arrDuplicateValues = UniteFunctionsUC::getArrayDuplicateValues($arrItemTax);
			
			if(empty($arrItemTax))
				$arrItemTax = array();
			
			foreach($arrItemTax as $slug => $taxTitle){

				if(is_string($taxTitle) == false)
					continue;
				
				$isDuplicate = array_key_exists($taxTitle, $arrDuplicateValues);
				
				//some modification for woo
				if($taxTitle == "Tag" && $slug != "post_tag")
					$isDuplicate = true;
				
				if(isset($arrAllTax[$taxTitle]))
					$isDuplicate = true;
					
				if($isDuplicate == true)
					$taxTitle = UniteFunctionsUC::convertHandleToTitle($slug);
				
				$taxTitle = ucwords($taxTitle);
				
				$arrTaxOutput[$slug] = $taxTitle;
				
				$arrAllTax[$taxTitle] = $slug;
			}
			
			if(!empty($arrTaxOutput))
				$arrTaxonomies[$typeName] = $arrTaxOutput;
		}
		
		$response = array();
		$response["post_type_tax"] = $arrTaxonomies;
		$response["taxonomies_simple"] = $arrAllTax;
		
		
		return($response);
	}

	
	/**
	 * add users picker
	 */
	protected function addUsersPicker($name,$value,$title,$extra){
		
		//----- custom or manual
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		
		$arrType = array();
		$arrType["custom"] = __("Custom Query", "unlimited-elements-for-elementor");
		$arrType["manual"] = __("Manual Selection", "unlimited-elements-for-elementor");
		
		$arrType = array_flip($arrType);
		
		$this->addSelect($name."_type", $arrType, __("Select Users By", "unlimited-elements-for-elementor"), "custom", $params);
		
		$arrConditionCustom = array();
		$arrConditionCustom[$name."_type"] = "custom";
		
		$arrConditionManual = array();
		$arrConditionManual[$name."_type"] = "manual";
		
		//----- roles in -------
		
		$arrRoles = UniteFunctionsWPUC::getRolesShort();
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["description"] = __("Leave empty for all the roles", "unlimited-elements-for-elementor");
		$params["elementor_condition"] = $arrConditionCustom;
		
		if(!empty($arrRoles))
			$arrRoles = array_flip($arrRoles);
		
		$role = UniteFunctionsUC::getVal($value, $name."_role");
		if(empty($role))
			$role = UniteFunctionsUC::getArrFirstValue($arrRoles);
		
		$params["is_multiple"] = true;
		$params["placeholder"] = __("All Roles", "unlimited-elements-for-elementor");
		//$params["description"] = __("Get all the users if leave empty", "unlimited-elements-for-elementor");
		
		$this->addMultiSelect($name."_role", $arrRoles, __("Select Roles", "unlimited-elements-for-elementor"), $role, $params);
		
		
		//-------- exclude roles ---------- 
		
		$arrRoles = UniteFunctionsWPUC::getRolesShort();
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["elementor_condition"] = $arrConditionCustom;
		
		if(!empty($arrRoles))
			$arrRoles = array_flip($arrRoles);
		
		$roleExclude = UniteFunctionsUC::getVal($value, $name."_role_exclude");
		
		$params["is_multiple"] = true;
		
		$this->addMultiSelect($name."_role_exclude", $arrRoles, __("Exclude Roles", "unlimited-elements-for-elementor"), $roleExclude, $params);
		
		//---- exclude user -----
		
		$arrAuthors = UniteFunctionsWPUC::getArrAuthorsShort();
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["is_multiple"] = true;
		$params["placeholder"] = __("Select one or more users", "unlimited-elements-for-elementor");
		$params["elementor_condition"] = $arrConditionCustom;
		
		$arrAuthorsFlipped = array_flip($arrAuthors);
		
		$this->addMultiSelect($name."_exclude_authors", $arrAuthorsFlipped, __("Exclude By Specific Users", "unlimited-elements-for-elementor"), "", $params);
		
		//---- include users -----
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["is_multiple"] = true;
		$params["placeholder"] = __("Select one or more users", "unlimited-elements-for-elementor");
		$params["elementor_condition"] = $arrConditionManual;
		
		$this->addMultiSelect($name."_include_authors", $arrAuthorsFlipped, __("Select Specific Users", "unlimited-elements-for-elementor"), "", $params);
		
		
		//---- hr before max users -----
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
		$params["elementor_condition"] = $arrConditionCustom;
		
		$this->addHr($name."_hr_before_max", $params);
		
		//---- max items -----
		 
		$params = array("unit"=>"users");
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["placeholder"] = __("all users if empty","unlimited-elements-for-elementor");
		$params["elementor_condition"] = $arrConditionCustom;
		$params["add_dynamic"] = true;
		
		$this->addTextBox($name."_maxusers", "", esc_html__("Max Number of Users", "unlimited-elements-for-elementor"), $params);
		
		//---- hr before order by -----
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
		
		$this->addHr($name."_hr_before_orderby", $params);
		
		//---- orderby -----
		
		$arrOrderBy = HelperProviderUC::getArrUsersOrderBySelect();
		$arrOrderBy = array_flip($arrOrderBy);
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		
		$this->addSelect($name."_orderby", $arrOrderBy, __("Order By", "unlimited-elements-for-elementor"), "default", $params);
		
		//--------- order direction -------------
		
		$arrOrderDir = UniteFunctionsWPUC::getArrSortDirection();
		$arrOrderDir = array_flip($arrOrderDir);
				
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		
		$this->addSelect($name."_orderdir", $arrOrderDir, __("Order Direction", "unlimited-elements-for-elementor"), "default", $params);

		//---- hr before meta -----
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
		
		$this->addHr($name."_hr_before_metakeys", $params);
			
		//---- meta keys addition -----
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["description"] = __("Get additional meta data by given meta keys comma separated","unlimited-elements-for-elementor");
		$params["placeholder"] = "meta_key1, meta_key2...";
		$params["label_block"] = true;
		$params["add_dynamic"] = true;
		
		$this->addTextBox($name."_add_meta_keys", "", __("Additional Meta Data Keys", "unlimited-elements-for-elementor"), $params);
		
		//---- hr before debug -----
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
		
		$this->addHr($name."_hr_before_debug", $params);
				
		//---- show debug -----
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_RADIOBOOLEAN;
		$params["description"] = __("Show the query for debugging purposes. Don't forget to turn it off before page release", "unlimited-elements-for-elementor");
		
		$this->addRadioBoolean($name."_show_query_debug", __("Show Query Debug", "unlimited-elements-for-elementor"), false, "Yes", "No", $params);
		
	}
	
	
	/**
	 * add menu picker
	 */
	protected function addMenuPicker($name, $value, $title, $extra){
		
		$useFor = UniteFunctionsUC::getVal($extra, "usefor");
		
		$showLimitedDepts = false;
		if($useFor == "multisource")
			$showLimitedDepts = true;
		
		
		$arrMenus = array();
		
		//if(GlobalsUC::$is_admin == true)
			$arrMenus = UniteFunctionsWPUC::getMenusListShort();
		
		$menuID = UniteFunctionsUC::getVal($value, $name."_id");
		
		if(empty($menuID))
			$menuID = UniteFunctionsUC::getFirstNotEmptyKey($arrMenus);
					
		$arrMenus = array_flip($arrMenus);
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		
		$this->addSelect($name."_id", $arrMenus, __("Select Menu", "unlimited-elements-for-elementor"), $menuID, $params);
		
		//add depth
		
		$arrDepth = array();
		$arrDepth["0"] = __("All Depths", "unlimited-elements-for-elementor");
		$arrDepth["1"] = __("1", "unlimited-elements-for-elementor");
		
		if($showLimitedDepts == false){
			$arrDepth["2"] = __("2", "unlimited-elements-for-elementor");
			$arrDepth["3"] = __("3", "unlimited-elements-for-elementor");
		}
				
		$arrDepth = array_flip($arrDepth);
		$depth = UniteFunctionsUC::getVal($value, $name."_depth", "0");
				
		$this->addSelect($name."_depth", $arrDepth, __("Max Depth", "unlimited-elements-for-elementor"), $depth, $params);
		
	}
	
	private function __________TERMS_______(){}
	
	/**
	 * add post terms settings
	 */
	protected function addPostTermsPicker($name, $value, $title, $extra){
		
		$isForWooCommerce = UniteFunctionsUC::getVal($extra, "for_woocommerce");
		$isForWooCommerce = UniteFunctionsUC::strToBool($isForWooCommerce);

		$filterType = UniteFunctionsUC::getVal($extra, "filter_type");
		
		$arrPostTypesWithTax = UniteFunctionsWPUC::getPostTypesWithTaxomonies(GlobalsProviderUC::$arrFilterPostTypes, false);
		
		if($isForWooCommerce == true && isset($arrPostTypesWithTax["product"]))
			$arrPostTypesWithTax = array("product" => $arrPostTypesWithTax["product"]);
		
		$taxData = $this->addPostTermsPicker_getArrTaxonomies($arrPostTypesWithTax);
				
		$arrPostTypesTaxonomies = $taxData["post_type_tax"];
		
		$arrTaxonomiesSimple = $taxData["taxonomies_simple"];
		
		//----- add post types ---------
		
		//prepare post types array
				
		$arrPostTypes = array();
		foreach($arrPostTypesWithTax as $typeName => $arrType){
			
			$title = UniteFunctionsUC::getVal($arrType, "title");
						
			if(empty($title))
				$title = ucfirst($typeName);
			
			if(isset($arrPostTypes[$title]))
				$title = ucfirst($typeName);

			if(isset($arrPostTypes[$title]))
				$title = ucfirst($typeName." ".$title);
			
			$arrPostTypes[$title] = $typeName;
		}
				
		$postType = UniteFunctionsUC::getVal($value, $name."_posttype");
		if(empty($postType))
			$postType = UniteFunctionsUC::getArrFirstValue($arrPostTypes);
		
		
		$params = array();
		
		$params[UniteSettingsUC::PARAM_CLASSADD] = "unite-setting-post-type";
		$dataTax = UniteFunctionsUC::encodeContent($arrPostTypesTaxonomies);
		
		$params[UniteSettingsUC::PARAM_ADDPARAMS] = "data-arrposttypes='$dataTax' data-settingtype='select_post_taxonomy' data-settingprefix='{$name}'";
		$params["datasource"] = "post_type";
		$params["origtype"] = "uc_select_special";
		
		$this->addSelect($name."_posttype", $arrPostTypes, __("Select Post Type", "unlimited-elements-for-elementor"), $postType, $params);
		
		//---------- add taxonomy ---------
				
		$params = array();
		$params["datasource"] = "post_taxonomy";
		$params[UniteSettingsUC::PARAM_CLASSADD] = "unite-setting-post-taxonomy";
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		
		$arrTax = UniteFunctionsUC::getVal($arrPostTypesTaxonomies, $postType, array());
		
		if(!empty($arrTax))
			$arrTax = array_flip($arrTax);
				
		$taxonomy = UniteFunctionsUC::getVal($value, $name."_taxonomy");
		if(empty($taxonomy))
			$taxonomy = UniteFunctionsUC::getArrFirstValue($arrTax);

		if($isForWooCommerce)
			$taxonomy = "product_cat";
		
		$this->addSelect($name."_taxonomy", $arrTaxonomiesSimple, __("Select Taxonomy", "unlimited-elements-for-elementor"), $taxonomy, $params);
		
		// --------- add include by -------------
		
		$arrIncludeBy = array();
		$arrIncludeBy["spacific_terms"] = __("Specific Terms","unlimited-elements-for-elementor");
		$arrIncludeBy["parents"] = __("Children Of","unlimited-elements-for-elementor");
		$arrIncludeBy["children_of_current"] = __("Children Of Current Term","unlimited-elements-for-elementor");
		$arrIncludeBy["current_post_terms"] = __("Current Post Terms","unlimited-elements-for-elementor");
		$arrIncludeBy["search"] = __("By Search Text","unlimited-elements-for-elementor");
		$arrIncludeBy["childless"] = __("Only Childless","unlimited-elements-for-elementor");
		$arrIncludeBy["no_parent"] = __("Not a Child of Other Term","unlimited-elements-for-elementor");
		$arrIncludeBy["only_direct_children"] = __("Only Direct Children","unlimited-elements-for-elementor");
		$arrIncludeBy["meta"] = __("Term Meta","unlimited-elements-for-elementor");
		
		$arrIncludeBy = array_flip($arrIncludeBy);
		
		$params = array();
		$params["is_multiple"] = true;
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
				
		$this->addMultiSelect($name."_includeby", $arrIncludeBy, esc_html__("Include By", "unlimited-elements-for-elementor"), "", $params);
		
		
		// --------- include by meta key -------------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["placeholder"] = __("Meta Key","unlimited-elements-for-elementor");
		$params["elementor_condition"] = array($name."_includeby"=>"meta");
		
		$this->addTextBox($name."_include_metakey", "", esc_html__("Include by Meta Key", "unlimited-elements-for-elementor"), $params);

		// --------- include by meta compare -------------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["elementor_condition"] = array($name."_includeby"=>"meta");
		$params["description"] = __("Get only those terms that has the meta key/value. For IN, NOT IN, BETWEEN, NOT BETWEEN compares, use coma saparated values");
				
		$arrItems = HelperProviderUC::getArrMetaCompareSelect();
		
		$arrItems = array_flip($arrItems);
		
		$this->addSelect($name."_include_metacompare", $arrItems, esc_html__("Include by Meta Compare", "unlimited-elements-for-elementor"), "=", $params);
		
		
		// --------- include by meta value -------------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["placeholder"] = __("Meta Value","unlimited-elements-for-elementor");
		$params["elementor_condition"] = array($name."_includeby"=>"meta");
		$params["add_dynamic"] = true;
		
		$this->addTextBox($name."_include_metavalue", "", esc_html__("Include by Meta Value", "unlimited-elements-for-elementor"), $params);

		
		// --------- add include by specific term -------------
		
		$params = array();
		$params["description"] = __("Only those selected terms will be loaded");
		
		$elementorCondition = array($name."_includeby"=>"spacific_terms");
		
		$exclude = UniteFunctionsUC::getVal($value, $name."_exclude");
		
		$addAttrib = "data-taxonomyname='{$name}_taxonomy'";
		
		$this->addPostIDSelect($name."_include_specific", __("Select Specific Terms", "unlimited-elements-for-elementor"), $elementorCondition, "terms", $addAttrib, $params);
		
		// --------- add terms parents -------------
		
		$params = array();
		$params["placeholder"] = "all--parents";
		
		$elementorCondition = array($name."_includeby"=>"parents");
		
		$exclude = UniteFunctionsUC::getVal($value, $name."_exclude");
		
		$addAttrib = "data-taxonomyname='{$name}_taxonomy' data-issingle='true'";
		
		$this->addPostIDSelect($name."_include_parent", __("Select Parent Term", "unlimited-elements-for-elementor"), $elementorCondition, "terms", $addAttrib, $params);
		
		// --------- add terms parents - direct switcher -------------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_RADIOBOOLEAN;
		$params["description"] = __("If turned off, all the terms tree will be selected", "unlimited-elements-for-elementor");
		$params["elementor_condition"] = array($name."_includeby"=>"parents");
		
		$this->addRadioBoolean($name."_include_parent_isdirect", __("Is Direct Parent", "unlimited-elements-for-elementor"), true, "Yes", "No", $params);

		// --------- by search phrase -------------
		
		$params = array("unit"=>"terms");
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["placeholder"] = __("Search Text","unlimited-elements-for-elementor");
		$params["elementor_condition"] = array($name."_includeby"=>"search");
		$params["add_dynamic"] = true;
		
		$this->addTextBox($name."_include_search", "", esc_html__("Include by Search", "unlimited-elements-for-elementor"), $params);
		
		
		//---------- add hr ---------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
		$this->addHr($name."_after_include_by",$params);

		// --------- add exclude by -------------
		
		$arrExcludeBy = array();
		$arrExcludeBy["spacific_terms"] = __("Specific Terms","unlimited-elements-for-elementor");
		$arrExcludeBy["current_term"] = __("Current Term (for archive only)","unlimited-elements-for-elementor");
		$arrExcludeBy["current_post_terms"] = __("Current Post Terms","unlimited-elements-for-elementor");
		$arrExcludeBy["hide_empty"] = __("Hide Empty Terms","unlimited-elements-for-elementor");
		
		$arrExcludeBy = array_flip($arrExcludeBy);
		
		$params = array();
		$params["is_multiple"] = true;
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
				
		$this->addMultiSelect($name."_excludeby", $arrExcludeBy, esc_html__("Exclude By", "unlimited-elements-for-elementor"), "", $params);
		
		
		//---------- add exclude ---------
		
		$elementorCondition = array($name."_excludeby"=>"spacific_terms");
		
		$exclude = UniteFunctionsUC::getVal($value, $name."_exclude");
		
		$addAttrib = "data-taxonomyname='{$name}_taxonomy' data-isalltax='true'";
		
		$this->addPostIDSelect($name."_exclude", __("Exclude Terms", "unlimited-elements-for-elementor"), $elementorCondition, "terms", $addAttrib);
		
		//----- exclude all the parents tree --------------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_RADIOBOOLEAN;
		$params["elementor_condition"] = $elementorCondition;
		
		$this->addRadioBoolean($name."_exclude_tree", __("Exclude With All Children Tree", "unlimited-elements-for-elementor"), true, "Yes", "No", $params);
		
		
		//----- add hr --------------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
		
		$this->addHr($name."_post_terms_before_additions", $params);
		
		//--------- add max terms -------------
		
		$params = array("unit"=>"terms");
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["placeholder"] = __("100 terms if empty","unlimited-elements-for-elementor");
		$params["add_dynamic"] = true;
		
		$this->addTextBox($name."_maxterms", "", esc_html__("Max Number of Terms", "unlimited-elements-for-elementor"), $params);
						
		//------- add hr before order by -------------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
		
		$this->addHr($name."_post_terms_before_orderby", $params);
		
		
		// --------- add order by -------------
		
		$arrOrderBy = UniteFunctionsWPUC::getArrTermSortBy();
		$arrOrderBy["include"] = __("Include - (specific terms order)", "unlimited-elements-for-elementor");
		$arrOrderBy["meta_value"] = __("Meta Value", "unlimited-elements-for-elementor");
		$arrOrderBy["meta_value_num"] = __("Meta Value - Numeric", "unlimited-elements-for-elementor");
		
		
		$arrOrderBy = array_flip($arrOrderBy);
		
		$orderBy = UniteFunctionsUC::getVal($value, $name."_orderby", "default");
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		
		$this->addSelect($name."_orderby", $arrOrderBy, __("Order By", "unlimited-elements-for-elementor"), $orderBy, $params);
		
		//--- meta value param -------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		
		$arrCondition = array();
		$arrCondition[$name."_orderby"] = array("meta_value","meta_value_num");
		
		$params["elementor_condition"] = $arrCondition;
		$params["add_dynamic"] = true;
		
		$this->addTextBox($name."_orderby_meta_key", "" , __("&nbsp;&nbsp;Custom Field Name","unlimited-elements-for-elementor"), $params);
		
		
		//--------- add order direction -------------
		
		$arrOrderDir = UniteFunctionsWPUC::getArrSortDirection();
		
		$orderDir = UniteFunctionsUC::getVal($value, $name."_orderdir", UniteFunctionsWPUC::ORDER_DIRECTION_ASC);
		
		$arrOrderDir = array_flip($arrOrderDir);
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		
		$this->addSelect($name."_orderdir", $arrOrderDir, __("Order Direction", "unlimited-elements-for-elementor"), $orderDir, $params);
		
		
		//add hr
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
		
		$this->addHr($name."_post_terms_before_queryid", $params);
		
		//---- show debug -----
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_RADIOBOOLEAN;
		$params["description"] = __("Show the query for debugging purposes. Don't forget to turn it off before page release", "unlimited-elements-for-elementor");
		
		$this->addRadioBoolean($name."_show_query_debug", __("Show Query Debug", "unlimited-elements-for-elementor"), false, "Yes", "No", $params);
		
		//---- query id -----
				
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$title = __("Query ID", "unlimited-elements-for-elementor");
		$params["description"] = __("Give your Query unique ID to been able to filter it in server side using add_filter() function. <a href='https://unlimited-elements.com/docs/work-with-query-id-in-terms-selection/'><a target='blank' href='https://unlimited-elements.com/docs/work-with-query-id-in-posts-selection/'>See docs here</a></a>.","unlimited-elements-for-elementor");
		
		$this->addTextBox($name."_queryid", "", $title, $params);
		
		
		//--------- debug type terms ---------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["elementor_condition"] = array($name."_show_query_debug"=>"true");
		
		$arrType = array();
		$arrType["basic"] = __("Basic", "unlimited-elements-for-elementor");
		$arrType["show_query"] = __("Full", "unlimited-elements-for-elementor");
		
		$arrType = array_flip($arrType);
		
		$this->addSelect($name."_query_debug_type", $arrType, __("Debug Options", "unlimited-elements-for-elementor"), "basic", $params);
		
		
		//add hr
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
		
		$this->addHr($name."post_terms_sap", $params);
		
		
	}
	
	
	/**
	 * add woo commerce categories picker
	 */
	protected function addWooCatsPicker($name, $value, $title, $extra){

		$conditionQuery = array(
			$name."_type" => "query",
		);
		
		$conditionManual = array(
			$name."_type" => "manual",
		);
		
		
		//---------- type choosing ---------
		
		$arrType = array();
		$arrType["query"] = __("Categories Query","unlimited-elements-for-elementor");
		$arrType["manual"] = __("Manual Selection","unlimited-elements-for-elementor");
		
		$arrType = array_flip($arrType);
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		
		$type = UniteFunctionsUC::getVal($value, $name."_type", "query");
		
		$this->addSelect($name."_type", $arrType, __("Selection Type", "unlimited-elements-for-elementor"), $type, $params);
		
		//---------- add hr ---------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
		
		$this->addHr("woocommere_terms_sap_type", $params);
		
		
		//---------- add parent ---------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["placeholder"] = __("Example: cat1", "unlimited-elements-for-elementor");
		$params["description"] = __("Write parent category slug, if no parent leave empty", "unlimited-elements-for-elementor");
		$params["elementor_condition"] = $conditionQuery;
		
		$parent = UniteFunctionsUC::getVal($value, $name."_parent", "");
		
		$this->addTextBox($name."_parent", $parent, __("Parent Category", "unlimited-elements-for-elementor"), $params);
		
		
		//---------- include children ---------
		
		$includeChildren = UniteFunctionsUC::getVal($value, $name."_children", "not_include");
		
		$arrChildren = array();
		$arrChildren["not_include"] = __("Don't Include", "unlimited-elements-for-elementor");
		$arrChildren["include"] = __("Include", "unlimited-elements-for-elementor");
		$arrChildren = array_flip($arrChildren);
		
		
		//---------- add children ---------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["elementor_condition"] = $conditionQuery;
		
		$this->addSelect($name."_children", $arrChildren, __("Include Children", "unlimited-elements-for-elementor"), $includeChildren, $params);
		
		
		//---------- add exclude ---------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["placeholder"] = "Example: cat1,cat2";
		$params["description"] = "To exclude, enter comma separated term slugs";
		$params["label_block"] = true;
		$params["elementor_condition"] = $conditionQuery;
		
		$exclude = UniteFunctionsUC::getVal($value, $name."_exclude");
		
		$this->addTextBox($name."_exclude", $exclude, __("Exclude Categories", "unlimited-elements-for-elementor"), $params);
		
		// --------- add exclude categorized -------------
		
		$excludeUncat = UniteFunctionsUC::getVal($value, $name."_excludeuncat", "exclude");
		
		
		$arrExclude = array();
		$arrExclude["exclude"] = __("Exclude","unlimited-elements-for-elementor");
		$arrExclude["no_exclude"] = __("Don't Exclude","unlimited-elements-for-elementor");
		$arrExclude = array_flip($arrExclude);
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["elementor_condition"] = $conditionQuery;
		
		$this->addSelect($name."_excludeuncat", $arrExclude, __("Exclude Uncategorized Category", "unlimited-elements-for-elementor"), $excludeUncat, $params);
		
		// --------- hr -------------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
		$params["elementor_condition"] = $conditionQuery;
		
		$this->addHr("woocommere_terms_sap1", $params);
		
		// --------- add order by -------------
		
		$arrOrderBy = UniteFunctionsWPUC::getArrTermSortBy();
		$arrOrderBy["meta_value"] = __("Meta Value", "unlimited-elements-for-elementor");
		$arrOrderBy["meta_value_num"] = __("Meta Value - Numeric", "unlimited-elements-for-elementor");
		
		
		$arrOrderBy = array_flip($arrOrderBy);
		
		$orderBy = UniteFunctionsUC::getVal($value, $name."_orderby", "name");
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["elementor_condition"] = $conditionQuery;
		
		$this->addSelect($name."_orderby", $arrOrderBy, __("Order By", "unlimited-elements-for-elementor"), $orderBy, $params);

		//--- meta key param -------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		
		$arrCondition = $conditionQuery;
		$arrCondition[$name."_orderby"] = array("meta_value","meta_value_num");
		
		$params["elementor_condition"] = $arrCondition;
		$params["add_dynamic"] = true;
		
		$this->addTextBox($name."_orderby_meta_key", "" , __("&nbsp;&nbsp;Meta Field Name","unlimited-elements-for-elementor"), $params);
		
		
		//--------- add order direction -------------
		
		$arrOrderDir = UniteFunctionsWPUC::getArrSortDirection();
		
		$orderDir = UniteFunctionsUC::getVal($value, $name."_orderdir", UniteFunctionsWPUC::ORDER_DIRECTION_ASC);
		
		$arrOrderDir = array_flip($arrOrderDir);
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["elementor_condition"] = $conditionQuery;
		
		$this->addSelect($name."_orderdir", $arrOrderDir, __("Order Direction", "unlimited-elements-for-elementor"), $orderDir, $params);
		
		
		//--------- add hide empty -------------
		
		$hideEmpty = UniteFunctionsUC::getVal($value, $name."_hideempty", "no_hide");
		
		$arrHide = array();
		$arrHide["no_hide"] = "Don't Hide";
		$arrHide["hide"] = "Hide";
		$arrHide = array_flip($arrHide);
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["elementor_condition"] = $conditionQuery;
		 
		$this->addSelect($name."_hideempty", $arrHide, __("Hide Empty", "unlimited-elements-for-elementor"), $hideEmpty, $params);
		
		//add hr
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
		$params["elementor_condition"] = $conditionQuery;
		
		$this->addHr("woocommere_terms_sap", $params);

		
		//---------- include categories - manual selection ---------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["placeholder"] = __("Example: cat1, cat2", "unlimited-elements-for-elementor");
		$params["description"] = __("Include specific categories by slug", "unlimited-elements-for-elementor");
		$params["label_block"] = true;
		$params["elementor_condition"] = $conditionManual;
		
		$cats = UniteFunctionsUC::getVal($value, $name."_include", "");
		
		$this->addTextBox($name."_include", $cats, __("Include Specific Categories", "unlimited-elements-for-elementor"), $params);
		
		
		//add hr
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
		
		$this->addHr($name."_post_terms_before_queryid", $params);
		
		//---- show debug -----
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_RADIOBOOLEAN;
		$params["description"] = __("Show the query for debugging purposes. Don't forget to turn it off before page release", "unlimited-elements-for-elementor");
		
		$this->addRadioBoolean($name."_show_query_debug", __("Show Query Debug", "unlimited-elements-for-elementor"), false, "Yes", "No", $params);
		
		
		//add hr
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
		
		$this->addHr($name."post_terms_sap", $params);
		
		
	}
	
	
	/**
	 * add background settings
	 */
	protected function addBackgroundSettings($name, $value, $title, $param){
		
		$arrTypes = array();
		$arrTypes["none"] = __("No Background", "unlimited-elements-for-elementor");
		$arrTypes["solid"] = __("Solid", "unlimited-elements-for-elementor");
		$arrTypes["gradient"] = __("Gradient", "unlimited-elements-for-elementor");
		
		$arrTypes = array_flip($arrTypes);
		
		$type = UniteFunctionsUC::getVal($param, "background_type", "none");
		
		$this->addRadio($name."_type", $arrTypes, "Background Type", $type);
		
		$solid = UniteFunctionsUC::getVal($param, "solid_color");
		$gradient1 = UniteFunctionsUC::getVal($param, "gradient_color1");
		$gradient2 = UniteFunctionsUC::getVal($param, "gradient_color2");
		
		$this->addHr();
		
		//solid color
		$this->startBulkControl($name."_type", "show", "solid");
		
			$this->addColorPicker($name."_color_solid", $solid, __("Solid Color", "unlimited-elements-for-elementor"));
		
		$this->endBulkControl();
		
		//gradient color
		$this->startBulkControl($name."_type", "show", "gradient");
		
			$this->addColorPicker($name."_color_gradient1", $gradient1, __("Gradient Color1", "unlimited-elements-for-elementor"));
			$this->addColorPicker($name."_color_gradient2", $gradient2, __("Gradient Color2", "unlimited-elements-for-elementor"));
		
		$this->endBulkControl();
		
	}
	
	private function __________POSTS_______(){}
	
	
	/**
	 * add post ID select
	 */
	public function addPostIDSelect($settingName, $text = null, $elementorCondition = null, $isForWoo = false, $addAttribOpt = "", $params = array()){
		
		if(empty($text))
			$text = __("Search and Select Posts", "unlimited-elements-for-elementor");
		
		$params[UniteSettingsUC::PARAM_CLASSADD] = "unite-setting-special-select";
		
		$placeholder = __("All Posts", "unlimited-elements-for-elementor");
		
		if($isForWoo === true)
			$placeholder = __("All Products", "unlimited-elements-for-elementor");
		
		$placeholder = str_replace(" ", "--", $placeholder);
		
		$loaderText = __("Loading Data...", "unlimited-elements-for-elementor");
		$loaderText = UniteFunctionsUC::encodeContent($loaderText);
		
		$addAttrib = "";
		if($isForWoo === true)
			$addAttrib = " data-woo='yes'";
		
		if($isForWoo === "elementor_template"){
			$addAttrib = " data-datatype='elementor_template' data-issingle='true'";
			$placeholder = "All";
		}
		
		if($isForWoo === "terms"){
			$addAttrib = " data-datatype='terms'";
			$placeholder = "All--Terms";
		}
		
		if(isset($params["placeholder"])){
			$placeholder = $params["placeholder"];
		}
		
		if($isForWoo === "single"){
			
			$addAttrib = " data-issingle='true'";
		}
		
		if(!empty($addAttribOpt))
			$addAttrib .= " ".$addAttribOpt;
		
		$params[UniteSettingsUC::PARAM_ADDPARAMS] = "data-settingtype='post_ids' data-placeholdertext='{$placeholder}' data-loadertext='$loaderText' $addAttrib";
		
		$params["datasource"] = "post_type";
		$params["origtype"] = "uc_select_special";
		$params["label_block"] = true;
		
		if(!empty($elementorCondition))
			$params["elementor_condition"] = $elementorCondition;
		
		$this->addSelect($settingName, array(), $text , "", $params);
		
	}
	
	
	/**
	 * add post list picker
	 */
	protected function addPostsListPicker($name,$value,$title,$extra){
		
		
 		$simpleMode = UniteFunctionsUC::getVal($extra, "simple_mode");
		$simpleMode = UniteFunctionsUC::strToBool($simpleMode);
		
		$allCatsMode = UniteFunctionsUC::getVal($extra, "all_cats_mode");
		$allCatsMode = UniteFunctionsUC::strToBool($allCatsMode);
		
		$isForWooProducts = UniteFunctionsUC::getVal($extra, "for_woocommerce_products");
		$isForWooProducts = UniteFunctionsUC::strToBool($isForWooProducts);
		
		$addCurrentPosts = UniteFunctionsUC::getVal($extra, "add_current_posts");
		$addCurrentPosts = UniteFunctionsUC::strToBool($addCurrentPosts);
		
		$defaultMaxPosts = UniteFunctionsUC::getVal($extra, "default_max_posts");
		$defaultMaxPosts = (int)($defaultMaxPosts);

		
		$arrPostTypes = array();
		
		//if(GlobalsUC::$is_admin == true){
			$arrPostTypes = UniteFunctionsWPUC::getPostTypesWithCats(GlobalsProviderUC::$arrFilterPostTypes);
		//}
		
		
		$isWpmlExists = UniteCreatorWpmlIntegrate::isWpmlExists();
		
		$textPosts = __("Posts","unlimited-elements-for-elementor");
		$textPost = __("Post","unlimited-elements-for-elementor");
		
		if($isForWooProducts == true){
			$textPosts = __("Products","unlimited-elements-for-elementor");
			$textPost = __("Product","unlimited-elements-for-elementor");			
		}
		
		/*
		if($isWpmlExists == true){
			
			$objWpmlIntegrate = new UniteCreatorWpmlIntegrate();
			
			$arrLanguages = $objWpmlIntegrate->getLanguagesShort(true);
			$activeLanguege = $objWpmlIntegrate->getActiveLanguage();
		}
		*/
		
		//fill simple types
		$arrTypesSimple = array();
		
		if($simpleMode)
			$arrTypesSimple = array("Post"=>"post","Page"=>"page");
		else{
			
			foreach($arrPostTypes as $arrType){
				
				$postTypeName = UniteFunctionsUC::getVal($arrType, "name");
				$postTypeTitle = UniteFunctionsUC::getVal($arrType, "title");
				
				if(isset($arrTypesSimple[$postTypeTitle]))
					$arrTypesSimple[$postTypeName] = $postTypeName;
				else
					$arrTypesSimple[$postTypeTitle] = $postTypeName;
			}
			
		}
		
		$arrTypesSimple["Any"] = "any";
		
		
		//----- posts source ----
		//UniteFunctionsUC::showTrace();
		
		$arrNotCurrentElementorCondition = array();
		$arrCustomOnlyCondition = array();
		$arrRelatedOnlyCondition = array();
		$arrCurrentElementorCondition = array();
		$arrCustomAndCurrentElementorCondition = array();
		$arrNotManualElementorCondition = array();
		$arrCustomAndRelatedElementorCondition = array();
		$arrManualElementorCondition = array();
		
		
		if($addCurrentPosts == true){
						
			$arrCurrentElementorCondition = array(
				$name."_source" => "current",
			);
			
			$arrNotCurrentElementorCondition = array(
				$name."_source!" => "current",
			);
			
			$arrCustomAndCurrentElementorCondition = array(
				$name."_source" => array("current","custom"),
			);
			
			$arrCustomAndRelatedElementorCondition = array(
				$name."_source" => array("related","custom"), 
			);

			
			$arrCustomOnlyCondition = array(
				$name."_source" => "custom",
			);
			
			$arrRelatedOnlyCondition = array(
				$name."_source" => "related",
			);
			
			$arrNotInRelatedCondition = array(
				$name."_source!" => "related",
			);
			
			$arrNotManualElementorCondition = array(
				$name."_source!" => "manual",
			);
			
			$arrManualElementorCondition = array(
				$name."_source" => "manual",
			);
			
			
			$params = array();
			$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
			//$params["description"] = esc_html__("Choose the source of the posts list", "unlimited-elements-for-elementor");
			
			$source = UniteFunctionsUC::getVal($value, $name."_source", "custom");
			$arrSourceOptions = array();
			$arrSourceOptions[sprintf(__("Current Query %s", "unlimited-elements-for-elementor"), $textPosts)] = "current";
			$arrSourceOptions[sprintf(__("Custom %s", "unlimited-elements-for-elementor"),$textPosts)] = "custom";
			$arrSourceOptions[sprintf(__("Related %s", "unlimited-elements-for-elementor"), $textPosts)] = "related";
			$arrSourceOptions[__("Manual Selection", "unlimited-elements-for-elementor")] = "manual";
			
			$this->addSelect($name."_source", $arrSourceOptions, sprintf(esc_html__("%s Source", "unlimited-elements-for-elementor"), $textPosts), $source, $params);

			//-------- add static text - current --------
			
			$params = array();
			$params["origtype"] = UniteCreatorDialogParam::PARAM_STATIC_TEXT;
			$params["description"] = esc_html__("Choose the source of the posts list", "unlimited-elements-for-elementor");
			$params["elementor_condition"] = $arrCurrentElementorCondition;
			
			$maxPostsPerPage = get_option("posts_per_page");
			
			if($isForWooProducts == true)
				$maxPostsPerPage = UniteCreatorWooIntegrate::getDefaultCatalogNumPosts();

			
			$this->addStaticText("The current $textPosts are being used in archive pages. Posts per page: {$maxPostsPerPage}. Set this option in Settings -> Reading ", $name."_currenttext", $params);
			
			//-------- add static text - related --------
			
			$params = array();
			$params["origtype"] = UniteCreatorDialogParam::PARAM_STATIC_TEXT;
			$params["elementor_condition"] = $arrRelatedOnlyCondition;
			
			$addition1 = "";
			if($isForWooProducts)
				$addition1 .= " or checkout page";
			
			$staticText = "The ".strtolower("related {$textPosts} are being used in single {$textPost} $addition1.  Posts from same post type and terms");
						
			$this->addStaticText($staticText, $name."_relatedtext", $params);
			
		}//if current posts
		
		
		//-------- add related posts options --------
		
		$arrRelatedModes = array();
		$arrRelatedModes["or"] = "OR (default)";
		$arrRelatedModes["and"] = "AND";
		$arrRelatedModes["grouping"] = "GROUPING";
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		
		$params["elementor_condition"] = $arrRelatedOnlyCondition;
		$params["description"] = __("In grouping mode, between taxonomies will be 'and' relation and inside same taxonomy will be 'or' relation ","unlimited-elements-for-elementor");
		
		$arrRelatedModes = array_flip($arrRelatedModes);
		
		$this->addSelect($name."_related_mode", $arrRelatedModes, __("Related Posts Mode", "unlimited-elements-for-elementor"), "or", $params);
		
		
		//----- post type -----
		
		$defaultPostType = "post";
		if($isForWooProducts == true)
			$defaultPostType = "product";
		
		$postType = UniteFunctionsUC::getVal($value, $name."_posttype", $defaultPostType);
		
		$params = array();
		
		if($simpleMode == false){
			$params["datasource"] = "post_type";
			$params[UniteSettingsUC::PARAM_CLASSADD] = "unite-setting-post-type";
			
			$dataCats = UniteFunctionsUC::encodeContent($arrPostTypes);
			
			$params[UniteSettingsUC::PARAM_ADDPARAMS] = "data-arrposttypes='$dataCats' data-settingtype='select_post_type' data-settingprefix='{$name}'";
		}
		
		$params["origtype"] = "uc_select_special";
		//$params["description"] = esc_html__("Select which Post Type or Custom Post Type you wish to display", "unlimited-elements-for-elementor");
		
		$params["elementor_condition"] = $arrCustomOnlyCondition;
		
		$params["is_multiple"] = true;
		
		
		if($isForWooProducts == false)
			$this->addMultiSelect($name."_posttype", $arrTypesSimple, esc_html__("Post Types", "unlimited-elements-for-elementor"), $postType, $params);
		
		//----- hr -------
		
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
		$params["elementor_condition"] = $arrCustomOnlyCondition;
		
		$this->addHr($name."_post_before_include",$params);
		
		// --------- Include BY some options -------------
		
		$arrIncludeBy = array();
		
		$isStickyPluginExists = UniteCreatorPluginIntegrations::isStickySwitchPluginEnabled();
		
		if($isForWooProducts == false || $isStickyPluginExists == true){
			$arrIncludeBy["sticky_posts"] = __("Include Sticky Posts", "unlimited-elements-for-elementor");
			$arrIncludeBy["sticky_posts_only"] = __("Get Only Sticky Posts", "unlimited-elements-for-elementor");			
		}
				
		$arrIncludeBy["author"] = __("Author", "unlimited-elements-for-elementor");
		$arrIncludeBy["date"] = __("Date", "unlimited-elements-for-elementor");
		
		if($isForWooProducts == false){
			$arrIncludeBy["parent"] = __("Post Parent", "unlimited-elements-for-elementor");
		}
		
		$arrIncludeBy["meta"] = __("Post Meta", "unlimited-elements-for-elementor");
		
		$arrIncludeBy["current_terms"] = __("Current Page Terms", "unlimited-elements-for-elementor");
		
		$arrIncludeBy["most_viewed"] = __("Most Viewed", "unlimited-elements-for-elementor");
		$arrIncludeBy["php_function"] = __("IDs from PHP function","unlimited-elements-for-elementor");
		$arrIncludeBy["ids_from_meta"] = __("IDs from Post Meta","unlimited-elements-for-elementor");
		$arrIncludeBy["ids_from_dynamic"] = __("Post IDs from Dynamic Field","unlimited-elements-for-elementor");
		$arrIncludeBy["terms_from_dynamic"] = __("Terms from Dynamic Field", "unlimited-elements-for-elementor");
		$arrIncludeBy["terms_from_current_meta"] = __("Terms from Current Post Meta", "unlimited-elements-for-elementor");
		$arrIncludeBy["current_query_base"] = __("Current Query as a Base", "unlimited-elements-for-elementor");
		
		if($isForWooProducts == true){
			$arrIncludeBy["products_on_sale"] = __("Products On Sale Only (woo)","unlimited-elements-for-elementor");
			$arrIncludeBy["up_sells"] = __("Up Sells Products (woo)","unlimited-elements-for-elementor");
			$arrIncludeBy["cross_sells"] = __("Cross Sells Products (woo)","unlimited-elements-for-elementor");
			$arrIncludeBy["out_of_stock"] = __("Out Of Stock Products Only (woo)", "unlimited-elements-for-elementor");
			$arrIncludeBy["recent"] = __("Recently Viewed Produts (woo)", "unlimited-elements-for-elementor");
			$arrIncludeBy["products_from_post"] = __("Products From Post Content (woo)", "unlimited-elements-for-elementor");
		}
		
		$addPostsText = sprintf(__("Add Specific %s", "unlimited-elements-for-elementor"), $textPosts);
		
		$includeBy = UniteFunctionsUC::getVal($value, $name."_includeby");
		
		$arrIncludeBy = array_flip($arrIncludeBy);
		
		$params = array();
		$params["is_multiple"] = true;
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		
		$arrConditionIncludeBy = $arrCustomOnlyCondition;
		$params["elementor_condition"] = $arrConditionIncludeBy;
		
		$this->addMultiSelect($name."_includeby", $arrIncludeBy, esc_html__("Include By", "unlimited-elements-for-elementor"), $includeBy, $params);
		
		//--- add hr after include by----
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
		
		$params["elementor_condition"] = $arrConditionIncludeBy;
		
		$this->addHr($name."_after_include_by",$params);

		
		//---- Include By Author -----
		
		//optimize requests for front
		
		$arrAuthors = array();
		
		//if(GlobalsUC::$is_admin == true)
		
		$arrAuthors = UniteFunctionsWPUC::getArrAuthorsShort(true);
				
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["is_multiple"] = true;
		$params["placeholder"] = __("Select one or more authors", "unlimited-elements-for-elementor");
		
		$arrConditionIncludeAuthor = $arrConditionIncludeBy;
		$arrConditionIncludeAuthor[$name."_includeby"] = "author";
		
		$params["elementor_condition"] = $arrConditionIncludeAuthor;
		
		$arrAuthors = array_flip($arrAuthors);
		
		$this->addMultiSelect($name."_includeby_authors", $arrAuthors, __("Include By Authors From List", "unlimited-elements-for-elementor"), "", $params);
		
		//---- authors from dynamic field -----
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["add_dynamic"] = true;
		$params["label_block"] = true;
		$params["placeholder"] = __("Example: 3,5,7", "unlimited-elements-for-elementor");
		
		$params["elementor_condition"] = $arrConditionIncludeAuthor;
		
		$this->addTextBox($name."_includeby_authors_dynamic", "", __("Or Include by Authors from Dynamic Field", "unlimited-elements-for-elementor"), $params);
		
		
		//---- Include By Date -----
		
		$arrDates = HelperProviderUC::getArrPostsDateSelect();
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		
		$arrConditionIncludeByDate = $arrConditionIncludeBy;
		$arrConditionIncludeByDate[$name."_includeby"] = "date";

		$params["elementor_condition"] = $arrConditionIncludeByDate;
		
		$arrDates = array_flip($arrDates);
		
		$this->addSelect($name."_includeby_date", $arrDates, __("Include By Date", "unlimited-elements-for-elementor"), "all", $params);

		//----- add date before and after -------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["placeholder"] = __("Choose Date", "unlimited-elements-for-elementor");
		
		$arrConditionDateCustom = $arrConditionIncludeByDate;
		$arrConditionDateCustom[$name."_includeby_date"] = "custom";
		
		$params["elementor_condition"] = $arrConditionDateCustom;
		
		
		//after date (first)
		
		$params["description"] = __("Show all the posts published since the chosen date, inclusive. Format: year-month-day like \"2023-05-20\" or textual like \"sunday next week\"","unlimited-elements-for-elementor");
		
		$this->addTextBox($name."_include_date_after","", __("Published After Date","unlimited-elements-for-elementor"),$params);
		
		
		//before date (second)
		
		$params["description"] = __("Show all the posts published until the chosen date, inclusive. Format: year-month-day like \"2023-04-15\" or textual like \"monday next week\" ","unlimited-elements-for-elementor");
		
		$this->addTextBox($name."_include_date_before","",__("Published Before Date","unlimited-elements-for-elementor"),$params);
				
		
		//----- date meta field -------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["description"] = __("Optional, Select custom field (like ACF) with date format 20210310 (Ymd). For example: event_date","unlimited-elements-for-elementor");
		$params["elementor_condition"] = $arrConditionIncludeByDate;
		
		$this->addTextBox($name."_include_date_meta","",__("Date by Meta Field","unlimited-elements-for-elementor"),$params);

		//----- date meta format -------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["description"] = __("Here you can set the date format for the meta field","unlimited-elements-for-elementor");
		$params["elementor_condition"] = $arrConditionIncludeByDate;
		
		$this->addTextBox($name."_include_date_meta_format","Ymd",__("Date by Meta Field - Format","unlimited-elements-for-elementor"),$params);
		
		
		//----- add hr after date -------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
		$params["elementor_condition"] = $arrConditionIncludeByDate;
		
		$this->addHr($name."_hr_after_date",$params);

		//---- Include By Post Parent -----
		
		$arrConditionIncludeParents = $arrConditionIncludeBy;
		$arrConditionIncludeParents[$name."_includeby"] = "parent";
		
		$this->addPostIDSelect($name."_includeby_parent", sprintf(__("Select %s Parents"), $textPosts), $arrConditionIncludeParents, $isForWooProducts);
		
		//-------- include by post parent - add the parent page--------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["elementor_condition"] = $arrConditionIncludeParents;
		
		$arrItems = array(
			"no"=>__("No","unlimited-elements-for-elementor"),
			"start"=>__("To Beginning","unlimited-elements-for-elementor"),
			"end"=>__("To End","unlimited-elements-for-elementor")
		);
		
		$arrItems = array_flip($arrItems);
		
		$this->addSelect($name."_includeby_parent_addparent", $arrItems, esc_html__("Add The Parent As Well", "unlimited-elements-for-elementor"), "no", $params);
		
		
		//-------- include by recently viewed --------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_STATIC_TEXT;
		$arrConditionIncludeRecent = $arrConditionIncludeBy;
		$arrConditionIncludeRecent[$name."_includeby"] = "recent";
		
		$params["elementor_condition"] = $arrConditionIncludeRecent;
		
		$this->addStaticText("Recently viewed by the current site visitor, taken from cookie: woocommerce_recently_viewed. Works only if active wordpress widget: \"Recently Viewed Products\" ", $name."_includeby_recenttext", $params);
		
		//-------- include by Post Meta --------
		
		// --------- include by meta key -------------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["placeholder"] = __("Meta Key","unlimited-elements-for-elementor");

		$arrConditionIncludeMeta = $arrConditionIncludeBy;
		$arrConditionIncludeMeta[$name."_includeby"] = "meta";
		
		$params["elementor_condition"] = $arrConditionIncludeMeta;
		
		$this->addTextBox($name."_includeby_metakey", "", esc_html__("Include by Meta Key", "unlimited-elements-for-elementor"), $params);
		
		// --------- include by meta compare -------------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["description"] = __("Get only those terms that has the meta key/value. For IN, NOT IN, BETWEEN, NOT BETWEEN compares, use coma separated values","unlimited-elements-for-elementor");
		$params["elementor_condition"] = $arrConditionIncludeMeta;
		
		$arrItems = HelperProviderUC::getArrMetaCompareSelect();
		
		$arrItems = array_flip($arrItems);
		
		$this->addSelect($name."_includeby_metacompare", $arrItems, esc_html__("Include by Meta Compare", "unlimited-elements-for-elementor"), "=", $params);
		
		// --------- include by meta value -------------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["placeholder"] = __("Meta Value","unlimited-elements-for-elementor");
		$params["add_dynamic"] = true;
		$params["description"] = "";
		$params["label_block"] = true;
		
		$params["elementor_condition"] = $arrConditionIncludeMeta;
		
		
		$this->addTextBox($name."_includeby_metavalue", "", esc_html__("Include by Meta Value", "unlimited-elements-for-elementor"), $params);
		$this->addTextBox($name."_includeby_metavalue2", "", esc_html__("Include by Meta Value 2", "unlimited-elements-for-elementor"), $params);
		
		$params["description"] = "Special keywords you can use: {current_user_id}, or like this:  value1||value2||value3";
		
		$this->addTextBox($name."_includeby_metavalue3", "", esc_html__("Include by Meta Value 3", "unlimited-elements-for-elementor"), $params);
		
		// --------- show another meta key -------------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_RADIOBOOLEAN;
		
		$params["elementor_condition"] = $arrConditionIncludeMeta;
				
		$this->addRadioBoolean($name."_includeby_meta_addsecond", __("Add Second Meta Key", "unlimited-elements-for-elementor"), false, "Yes", "No", $params);
		
		// --------- include by SECOND meta key -------------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["placeholder"] = __("Second Meta Key","unlimited-elements-for-elementor");
		
		$arrConditionMetaSecond = $arrConditionIncludeMeta;
		$arrConditionMetaSecond[$name."_includeby_meta_addsecond"] = "true";
		
		$params["elementor_condition"] = $arrConditionMetaSecond;
		
		$this->addTextBox($name."_includeby_second_metakey", "", esc_html__("Include by Second Meta Key", "unlimited-elements-for-elementor"), $params);
		
		// --------- include by SECOND meta compare -------------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["elementor_condition"] = $arrConditionMetaSecond;
				
		$this->addSelect($name."_includeby_second_metacompare", $arrItems, esc_html__("Include by Second Meta Compare", "unlimited-elements-for-elementor"), "=", $params);
		
		// --------- include by SECOND meta value -------------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["placeholder"] = __("Second Meta Value","unlimited-elements-for-elementor");
		$params["add_dynamic"] = true;
		$params["description"] = "";
		
		$params["elementor_condition"] = $arrConditionMetaSecond;
		
		$this->addTextBox($name."_includeby_second_metavalue", "", esc_html__("Include by Second Meta Value", "unlimited-elements-for-elementor"), $params);
		
		// --------- Meta Fields Relation -------------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["elementor_condition"] = $arrConditionMetaSecond;

		$arrRelations = array();
		$arrRelations["AND"] = "AND";
		$arrRelations["OR"] = "OR";
		
		$this->addSelect($name."_includeby_meta_relation", $arrRelations, esc_html__("Meta Fields Relation", "unlimited-elements-for-elementor"), "and", $params);
		
		// --------- debug post meta -------------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_RADIOBOOLEAN;
		
		$params["elementor_condition"] = $arrConditionIncludeMeta;
		
		$this->addRadioBoolean($name."_includeby_meta_debug", __("Show Post Meta Fields for Debug", "unlimited-elements-for-elementor"), false, "Yes", "No", $params);
		
		
		// --------- include by PHP Function -------------
		
		$arrConditionIncludeFunction = $arrConditionIncludeBy;
		$arrConditionIncludeFunction[$name."_includeby"] = "php_function";
		
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["placeholder"] = __("getMyIDs","unlimited-elements-for-elementor");
		$params["description"] = __("Get post id's array from php function. \n For example: function getMyIDs(\$arg){return(array(\"32\",\"58\")). This function MUST begin with 'get'. }");
		$params["elementor_condition"] = $arrConditionIncludeFunction;
		
		$this->addTextBox($name."_includeby_function_name", "", esc_html__("PHP Function Name", "unlimited-elements-for-elementor"), $params);
		
		// --------- include by PHP Function Add Parameter-------------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["placeholder"] = __("yourtext","unlimited-elements-for-elementor");
		$params["description"] = __("Optional. Some argument to be passed to this function. For some \"IF\" statement.","unlimited-elements-for-elementor");
		$params["elementor_condition"] = $arrConditionIncludeFunction;
		
		$this->addTextBox($name."_includeby_function_addparam", "", esc_html__("PHP Function Argument", "unlimited-elements-for-elementor"), $params);
		
		// --------- include by id's from meta -------------
				
		$textIDsFromMeta = __("Select Post (leave empty for current post)","unlimited-elements-for-elementor");
		$arrConditionIncludePostMeta = $arrConditionIncludeBy;
		$arrConditionIncludePostMeta[$name."_includeby"] = "ids_from_meta";
		
		$this->addPostIDSelect($name."_includeby_postmeta_postid", $textIDsFromMeta, $arrConditionIncludePostMeta, false,"data-issingle='true'");
		
		// --------- include by id's from meta field name -------------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["description"] = __("Choose meta field name that has the post id's on it. Good for acf relationship for example","unlimited-elements-for-elementor");
		$params["elementor_condition"] = $arrConditionIncludePostMeta;
		
		$this->addTextBox($name."_includeby_postmeta_metafield", "", esc_html__("Meta Field Name", "unlimited-elements-for-elementor"), $params);
		

		//----- include id's from dynamic field -------
		
		$arrConditionIncludeDynamic = $arrConditionIncludeBy;
		$arrConditionIncludeDynamic[$name."_includeby"] = "ids_from_dynamic";
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["description"] = __("Enter post id's like 45,65,76, or select from dynamic tag","unlimited-elements-for-elementor");
		$params["elementor_condition"] = $arrConditionIncludeDynamic;
		$params["label_block"] = true;
		$params["add_dynamic"] = true;
		
		$this->addTextBox($name."_includeby_dynamic_field","",__("Include Posts by Dynamic Field","unlimited-elements-for-elementor"), $params);

		
		//----- include terms from dynamic field by ids -------
		
		$arrConditionIncludeDynamic = $arrConditionIncludeBy;
		$arrConditionIncludeDynamic[$name."_includeby"] = "terms_from_dynamic";
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["description"] = __("Enter term id's like 12,434,1289, or select from dynamic tag. You can use the term relation and include children options from below","unlimited-elements-for-elementor");
		$params["elementor_condition"] = $arrConditionIncludeDynamic;
		$params["label_block"] = true;
		$params["add_dynamic"] = true;
		
		$this->addTextBox($name."_includeby_terms_dynamic_field","",__("Include by Terms from Dynamic Field","unlimited-elements-for-elementor"), $params);

		//----- include terms from current post meta field -------
		
		$arrConditionIncludeDynamic = $arrConditionIncludeBy;
		$arrConditionIncludeDynamic[$name."_includeby"] = "terms_from_current_meta";
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["description"] = __("Enter current post meta field, that has the terms selection of the posts you want to bring. Use it to connect parent with children posts with terms","unlimited-elements-for-elementor");
		$params["elementor_condition"] = $arrConditionIncludeDynamic;
		$params["label_block"] = true;
		$params["add_dynamic"] = false;
		$params["placeholder"] = "Example: terms_select";
		
		$this->addTextBox($name."_includeby_terms_from_meta","",__("Current Post Terms Select Meta Field","unlimited-elements-for-elementor"), $params);
		
		// --------- current query base -------------

		$arrConditionCurrentQueryBase = $arrConditionIncludeBy;
		$arrConditionCurrentQueryBase[$name."_includeby"] = "current_query_base";
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_STATIC_TEXT;
		$params["elementor_condition"] = $arrConditionCurrentQueryBase;
		
		$text = __("Get current query as a query base. Good for archive page customization. For simple uses use the 'Current Query' product source instead. ","unlimited-elements-for-elementor");
						
		$this->addStaticText($text, $name."_current_query_text", $params);
		
		
		// --------- include by most viewed -------------
		
		$isWPPExists = UniteCreatorPluginIntegrations::isWPPopularPostsExists();
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_STATIC_TEXT;
		
		$arrConditionIncludeViewsCounter = $arrConditionIncludeBy;
		$arrConditionIncludeViewsCounter[$name."_includeby"] = "most_viewed";
		
		$params["elementor_condition"] = $arrConditionIncludeViewsCounter;
		
		$text = __("Select most viewed posts, integration with plugin: 'WordPress Popular Posts' that should be installed", "unlimited-elements-for-elementor");
		
		if($isWPPExists == true)
			$text = __("'WordPress Popular Posts' plugin activated.", "unlimited-elements-for-elementor");
		
		$this->addStaticText($text, $name."_text_includemostviewed", $params);
		
		// --------- most viewed range -------------
		
		if($isWPPExists == true){
		
			$params = array();
			$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
			$params["elementor_condition"] = $arrConditionIncludeViewsCounter;
			$params["description"] = "Besides range, it supports single post type and single category, and order direction query options";
			
			$arrItems = array("last30days"=>"Last 30 Days", 
							  "last7days"=>"Last 7 Days", 
							  "last24hours"=>"Last 24 Hours", 
							  "daily"=>"Daily",
							  "weekly"=>"Weekly", 
							  "monthly"=>"Monthly", 
							  "all"=>"All");
			
			$arrItems = array_flip($arrItems);
			
			$this->addSelect($name."_includeby_mostviewed_range", $arrItems, esc_html__("Most Viewed Time Range", "unlimited-elements-for-elementor"), "last30days", $params);
			
		}
				
		// --------- add hr before categories -------------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
		$params["elementor_condition"] = $arrCustomOnlyCondition;
		
		$this->addHr($name."_before_categories",$params);
		
		
		//----- add categories -------
		
		$arrCats = array();
		
		if($simpleMode == true){
			
			$arrCats = $arrPostTypes["post"]["cats"];
			$arrCats = array_flip($arrCats);
			$firstItemValue = reset($arrCats);
			
		}else if($allCatsMode == true){
			
			//filter only product terms
			
			if($isForWooProducts == true)
				$arrPostTypes = array(
					"product"=>UniteFunctionsUC::getVal($arrPostTypes, "product")
				);	
			
			
			$arrCats = $this->getCategoriesFromAllPostTypes($arrPostTypes);
			$firstItemValue = reset($arrCats);
			
		}else{
			$firstItemValue = "";
		}
		
		$category = UniteFunctionsUC::getVal($value, $name."_category", $firstItemValue);
		
		$params = array();
		
		if($simpleMode == false){
			$params["datasource"] = "post_category";
			$params[UniteSettingsUC::PARAM_CLASSADD] = "unite-setting-post-category";
		}
		
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["is_multiple"] = true;
				
		$params["elementor_condition"] = $arrCustomOnlyCondition;
		
		$paramsTermSelect = $params;
		
		$this->addMultiSelect($name."_category", $arrCats, esc_html__("Include By Terms", "unlimited-elements-for-elementor"), $category, $params);
		
		
		// --------- Include by term relation -------------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		
		$params["elementor_condition"] = $arrCustomOnlyCondition;
		
		$relation = UniteFunctionsUC::getVal($value, $name."_category_relation", "AND");
		
		$arrRelationItems = array();
		$arrRelationItems["And"] = "AND";
		$arrRelationItems["Or"] = "OR";
				
		$this->addSelect($name."_category_relation", $arrRelationItems, __("Include By Terms Relation", "unlimited-elements-for-elementor"), $relation, $params);
		
		//--------- show children -------------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_RADIOBOOLEAN;
		
		$params["elementor_condition"] = $arrCustomOnlyCondition;
		
		$isIncludeChildren = UniteFunctionsUC::getVal($value, $name."_terms_include_children", false);
		$isIncludeChildren = UniteFunctionsUC::strToBool($isIncludeChildren);
		
		$this->addRadioBoolean($name."_terms_include_children", __("Include Terms Children", "unlimited-elements-for-elementor"), $isIncludeChildren, "Yes", "No", $params);
		
		//---- manual selection search and replace -----
		
		$textManualSelect = sprintf(__("Seach And Select %s"), $textPosts);
		
		$this->addPostIDSelect($name."_manual_select_post_ids", $textManualSelect, $arrManualElementorCondition, $isForWooProducts);

		// --------- add dynamic post ids -------------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		
		$params["elementor_condition"] = $arrManualElementorCondition;
		$params["add_dynamic"] = true;
		$params["label_block"] = true;
		$params["description"] = "Optional. Select some dynamic field, that has output of post ids (string or array) like 15,40,23";
		
		$this->addTextBox($name."_manual_post_ids_dynamic", "", __("Or Select Post IDs 	", "unlimited-elements-for-elementor"), $params);

		// --------- add hr before avoid duplicates -------------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
		$params["elementor_condition"] = $arrManualElementorCondition;
		
		$this->addHr($name."_before_avoid_duplicates_manual",$params);
		
		
		//----- avoid duplicates -------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_RADIOBOOLEAN;
		$params["description"] = __("If turned on, those posts in another widgets won't be shown", "unlimited-elements-for-elementor");
		$params["elementor_condition"] = $arrManualElementorCondition;
		
		$this->addRadioBoolean($name."_manual_avoid_duplicates", __("Avoid Duplicates", "unlimited-elements-for-elementor"), false, "Yes", "No", $params);
		
		
		// --------- add hr before exclude -------------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
		$params["elementor_condition"] = $arrCustomOnlyCondition;
		
		$this->addHr($name."_before_exclude_by",$params);
		
		
		// --------- add exclude by -------------
		
		$arrExclude = array();
		
		if($isForWooProducts == true){
			$arrExclude["out_of_stock"] = __("Out Of Stock Products (woo)", "unlimited-elements-for-elementor");
			$arrExclude["products_on_sale"] = __("Products On Sale (woo)","unlimited-elements-for-elementor");
			
			//todo: finish this
			//$arrExclude["out_of_stock_variation"] = __("Out Of Stock Variation (woo)", "unlimited-elements-for-elementor");
		}
		
		$arrExclude["terms"] = __("Terms", "unlimited-elements-for-elementor");		
		$arrExclude["current_post"] = sprintf(__("Current %s", "unlimited-elements-for-elementor"), $textPost);
		$arrExclude["specific_posts"] = sprintf(__("Specific %s", "unlimited-elements-for-elementor"), $textPosts);
		$arrExclude["author"] = __("Author", "unlimited-elements-for-elementor");
		$arrExclude["no_image"] = sprintf(__("%s Without Featured Image", "unlimited-elements-for-elementor"),$textPost);
		$arrExclude["current_category"] = sprintf(__("%s with Current Category", "unlimited-elements-for-elementor"),$textPosts);
		$arrExclude["current_tags"] = sprintf(__("%s With Current Tags", "unlimited-elements-for-elementor"),$textPosts);
		$arrExclude["offset"] = sprintf(__("Offset", "unlimited-elements-for-elementor"),$textPosts);
		$arrExclude["avoid_duplicates"] = sprintf(__("Avoid Duplicates", "unlimited-elements-for-elementor"),$textPosts);
		$arrExclude["ids_from_dynamic"] = sprintf(__("Post IDs from Dynamic Field", "unlimited-elements-for-elementor"),$textPosts);
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["is_multiple"] = true;
		
		$conditionExcludeBy = $arrCustomAndRelatedElementorCondition;
		
		$params["elementor_condition"] = $conditionExcludeBy;
		
		$arrExclude = array_flip($arrExclude);
		
		$arrExcludeValues = "";
		
		$this->addMultiSelect($name."_excludeby", $arrExclude, __("Exclude By", "unlimited-elements-for-elementor"), $arrExcludeValues, $params);

		
		
		//----- exclude id's from dynamic field -------
		
		$conditionExcludeByDynamic = $conditionExcludeBy;
		$conditionExcludeByDynamic[$name."_excludeby"] = "ids_from_dynamic";
				
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["description"] = __("Enter post id's like 45,65,76, or select from dynamic tag","unlimited-elements-for-elementor");
		$params["elementor_condition"] = $conditionExcludeByDynamic;
		$params["label_block"] = true;
		$params["add_dynamic"] = true;
		
		$this->addTextBox($name."_exclude_dynamic_field","",__("Exclude Posts by Dynamic Field","unlimited-elements-for-elementor"), $params);
		
		
		
		//------- Already Fetched --------
		
		$conditionExcludeByFetched = $conditionExcludeBy;
		$conditionExcludeByFetched[$name."_excludeby"] = "avoid_duplicates";
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_STATIC_TEXT;
		$params["elementor_condition"] = $conditionExcludeByFetched;
		
		$this->addStaticText(__("Avoid duplicate posts, that fetched by another post widgets in the page, and have this option seleted (avoid duplicates)","unlimited-elements-for-elementor"), $name."_alreadyfethcedtext", $params);
		
		
		//------- Exclude By --- TERM --------
		
		$params = $paramsTermSelect;
		$conditionExcludeByTerms = $conditionExcludeBy;
		$conditionExcludeByTerms[$name."_excludeby"] = "terms";
		
		$params["elementor_condition"] = $conditionExcludeByTerms;
		
		$this->addMultiSelect($name."_exclude_terms", $arrCats, esc_html__("Exclude By Terms", "unlimited-elements-for-elementor"), "", $params);
		
		//------- Exclude By --- AUTHOR --------
				
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["is_multiple"] = true;
		$params["placeholder"] = __("Select one or more authors", "unlimited-elements-for-elementor");
		
		$arrConditionIncludeAuthor = $conditionExcludeBy;
		$arrConditionIncludeAuthor[$name."_excludeby"] = "author";
		
		$params["elementor_condition"] = $arrConditionIncludeAuthor;
		
		$this->addMultiSelect($name."_excludeby_authors", $arrAuthors, __("Exclude By Author", "unlimited-elements-for-elementor"), "", $params);

		//------- Exclude By --- OFFSET --------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_NUMBER;
		
		$params["description"] = __("Use this setting to skip over posts, not showing first posts to the offset given","unlimited-elements-for-elementor");
		
		$conditionExcludeByOffset = $conditionExcludeBy;
		$conditionExcludeByOffset[$name."_excludeby"] = "offset";
		
		$params["elementor_condition"] = $conditionExcludeByOffset;
		$params["add_dynamic"] = true;
		
		$this->addTextBox($name."_offset", "0", esc_html__("Offset", "unlimited-elements-for-elementor"), $params);
		
		
		//--------- show children -------------
				
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_RADIOBOOLEAN;
		$params["elementor_condition"] = $conditionExcludeByTerms;
		
		$this->addRadioBoolean($name."_terms_exclude_children", __("Exclude Terms With Children", "unlimited-elements-for-elementor"), true, "Yes", "No", $params);

		//------- Exclude By --- SPECIFIC POSTS --------
		
		$conditionExcludeBySpecific = $conditionExcludeBy;
		$conditionExcludeBySpecific[$name."_excludeby"] = "specific_posts";
		
		$params = array();
		$params["elementor_condition"] = $conditionExcludeBySpecific;
		
		$this->addPostIDSelect($name."_exclude_specific_posts", sprintf(__("Specific %s To Exclude", "unlimited-elements-for-elementor"),$textPosts), $conditionExcludeBySpecific, $isForWooProducts);
		
		//----- hr -------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
		$params["elementor_condition"] = $arrNotManualElementorCondition;
		
		$this->addHr($name."_post_after_exclude",$params);
		
		//------- Post Status --------
		
		$arrStatuses = HelperProviderUC::getArrPostStatusSelect();
				
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["is_multiple"] = true;
		$params["placeholder"] = __("Select one or more statuses", "unlimited-elements-for-elementor");
		
		$params["elementor_condition"] = $arrCustomOnlyCondition;
		
		$arrStatuses = array_flip($arrStatuses);
		
		$this->addMultiSelect($name."_status", $arrStatuses, __("Post Status", "unlimited-elements-for-elementor"), array("publish"), $params);
		
		//------- max items --------
		
		$params = array("unit"=>"posts");
		
		if(empty($defaultMaxPosts))
			$defaultMaxPosts = 10;
		
		$maxItems = UniteFunctionsUC::getVal($value, $name."_maxitems", $defaultMaxPosts);
		
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["placeholder"] = __("100 posts if empty","unlimited-elements-for-elementor");
		
		//$params["description"] = "Enter how many Posts you wish to display, -1 for unlimited";
		
		$params["elementor_condition"] = $arrCustomAndRelatedElementorCondition;
		$params["add_dynamic"] = true;
		
		$this->addTextBox($name."_maxitems", $maxItems, sprintf(esc_html__("Max %s", "unlimited-elements-for-elementor"), $textPosts), $params);

		//------- override post type --------
		
		$arrTypesCurrent = UniteFunctionsUC::addArrFirstValue($arrTypesSimple, "","[Original Post Type]");
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["elementor_condition"] = $arrCurrentElementorCondition;
		
		$this->addSelect($name."_posttype_current", $arrTypesCurrent, esc_html__("Post Type Override", "unlimited-elements-for-elementor"), "", $params);
		
		
		//------- max items for current --------
				
		$params = array("unit"=>"posts");
		
		if(empty($defaultMaxPosts))
			$defaultMaxPosts = 10;
		
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		
		$params["description"] = sprintf(__("Override Number Of %s, leave empty for default. If you are using pagination widget, leave it empty", "unlimited-elements-for-elementor"),$textPosts);
		
		$params["elementor_condition"] = $arrCurrentElementorCondition;
		$params["add_dynamic"] = true;
		
		$this->addTextBox($name."_maxitems_current", "", sprintf(esc_html__("Max %s", "unlimited-elements-for-elementor"), $textPosts), $params);
		
		
		//----- hr before orderby --------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
		
		$this->addHr($name."_hr_before_orderby",$params);
		
		
		//----- orderby --------
		
		$arrOrder = UniteFunctionsWPUC::getArrSortBy($isForWooProducts);
		$arrOrder = array_flip($arrOrder);
		
		$arrDir = UniteFunctionsWPUC::getArrSortDirection();
		$arrDir = array_flip($arrDir);
		
		//---- orderby for custom and current -----
		
		$params = array();
		
		//$params[UniteSettingsUC::PARAM_ADDFIELD] = $name."_orderdir1";
		
		$orderBY = UniteFunctionsUC::getVal($value, $name."_orderby", "default");
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		//$params["description"] = esc_html__("Select how you wish to order posts", "unlimited-elements-for-elementor");
		
		$this->addSelect($name."_orderby", $arrOrder, __("Order By", "unlimited-elements-for-elementor"), $orderBY, $params);
		
		//--- meta value param -------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["class"] = "alias";
		
		$arrCondition = array();
		$arrCondition[$name."_orderby"] = array(UniteFunctionsWPUC::SORTBY_META_VALUE, UniteFunctionsWPUC::SORTBY_META_VALUE_NUM);
		
		$params["elementor_condition"] = $arrCondition;
		$params["add_dynamic"] = false;
		
		$this->addTextBox($name."_orderby_meta_key1", "" , __("&nbsp;&nbsp;Custom Field Name","unlimited-elements-for-elementor"), $params);
				
		//---- order dir -----
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		//$params["description"] = esc_html__("Select order direction. Descending A-Z or Accending Z-A", "unlimited-elements-for-elementor");
		
		$orderDir1 = UniteFunctionsUC::getVal($value, $name."_orderdir1", "default" );
		$this->addSelect($name."_orderdir1", $arrDir, __("&nbsp;&nbsp;Order By Direction", "unlimited-elements-for-elementor"), $orderDir1, $params);
		
		
		//---- hr before query id -----
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
		
		
		$this->addHr($name."_hr_after_order_dir", $params);
				
		
		//---- query id -----
		
		$isPro = GlobalsUC::$isProVersion;
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		if($isPro == true){
			
			$title = __("Query ID", "unlimited-elements-for-elementor");
			$params["description"] = __("Give your Query unique ID to been able to filter it in server side using add_filter() function. <a href='https://unlimited-elements.com/docs/work-with-query-id-in-posts-selection/'><a target='blank' href='https://unlimited-elements.com/docs/work-with-query-id-in-posts-selection/'>See docs here</a></a>.","unlimited-elements-for-elementor");
			
		}else{		//free version
			
			$params["description"] = __("Give your Query unique ID to been able to filter it in server side using add_filter() function. This feature exists in a PRO Version only. <a target='blank' href='https://unlimited-elements.com/docs/work-with-query-id-in-posts-selection/'>help</a>","unlimited-elements-for-elementor");
			$title = __("Query ID (pro)", "unlimited-elements-for-elementor");
			$params["disabled"] = true;
		}
		
		$queryID = UniteFunctionsUC::getVal($value, $name."_queryid");
		
		$this->addTextBox($name."_queryid", $queryID, $title, $params);
				
		
		//---- show debug -----
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_RADIOBOOLEAN;
		$params["description"] = __("Show the query for debugging purposes. Don't forget to turn it off before page release", "unlimited-elements-for-elementor");
		
		$this->addRadioBoolean($name."_show_query_debug", __("Show Query Debug", "unlimited-elements-for-elementor"), false, "Yes", "No", $params);
		
		//--------- debug type posts ---------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["elementor_condition"] = array($name."_show_query_debug"=>"true");
		
		$arrType = array();
		$arrType["basic"] = __("Basic", "unlimited-elements-for-elementor");
		$arrType["show_query"] = __("Full", "unlimited-elements-for-elementor");
		
		$arrType = array_flip($arrType);
		
		$this->addSelect($name."_query_debug_type", $arrType, __("Debug Options", "unlimited-elements-for-elementor"), "basic", $params);
				
		
	}
	
	private function __________REMOTE_______(){}
	
	/**
	 * add remote parent settings
	 */
	private function addRemoteSettingsParent($name,$value,$title,$param){
		
		$prefix = $name."_";
		
		$remoteEnableName = $prefix."enable";
		$condition = array($remoteEnableName=>"true");
		
		//---- enable remote -----
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_RADIOBOOLEAN;
		$params["description"] = __("Enable the remote connection functionality for this widget", "unlimited-elements-for-elementor");
		
		$this->addRadioBoolean($remoteEnableName, __("Enable Remote Connection", "unlimited-elements-for-elementor"), false, "Yes", "No", $params);
		
		//widget name
		
		$arrNames = HelperProviderUC::getArrRemoteParentNames();
		$arrNames = array_flip($arrNames);
		
		$params = array(
			"description"=>__("This name will be used to connect and control this widget by other widgets"),
			"origtype" => UniteCreatorDialogParam::PARAM_DROPDOWN,
			"elementor_condition" => $condition
		);
		
		$this->addSelect($prefix."name", $arrNames, __("Widget Name for Connection", "unlimited-elements-for-elementor"), "auto", $params);
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["elementor_condition"] = array($remoteEnableName=>"true",$prefix."name"=>"custom");
		
		$this->addTextBox($prefix."custom_name", "", __("Custom Name","unlimited-elements-for-elementor"), $params);
		
		$params = array(
			"origtype" => UniteCreatorDialogParam::PARAM_HR,
		);
		
		$this->addHr("hr_before_sync",$params);
		
		//sync
		
		$remoteSyncName = $prefix."sync";
		$conditionSync = array($remoteSyncName=>"true");
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_RADIOBOOLEAN;
		$params["description"] = __("Sync slide run with other widgets", "unlimited-elements-for-elementor");
		
		$this->addRadioBoolean($prefix."sync", __("Sync", "unlimited-elements-for-elementor"), false, "Yes", "No", $params);
		
		//sync with widget name
		
		$arrNames = HelperProviderUC::getArrRemoteSyncNames();
		$arrNames = array_flip($arrNames);
				
		$params = array(
			"description"=>__("Choose the sync group"),
			"origtype" => UniteCreatorDialogParam::PARAM_DROPDOWN,
			"elementor_condition" => $conditionSync
		);
		
		$this->addSelect($prefix."sync_name", $arrNames, __("Sync Group", "unlimited-elements-for-elementor"), "group1", $params);
		
		$params = array(
			"origtype" => UniteCreatorDialogParam::PARAM_HR
		);
		
		$this->addHr("hr_before_debug",$params);
		
		//debug remote widgets
			
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_RADIOBOOLEAN;
		$params["description"] = __("Show information about remote widgets that connected to this widget. Please turn off this option before release", "unlimited-elements-for-elementor");
		
		$this->addRadioBoolean($prefix."debug", __("Show Debug", "unlimited-elements-for-elementor"), false, "Yes", "No", $params);
		
		
	}
	

	/**
	 * add remote controller settings
	 */
	private function addRemoteSettingsController($name,$value,$title,$param){
		
		$prefix = $name."_";

		$arrNames = HelperProviderUC::getArrRemoteParentNames();
		$arrNames = array_flip($arrNames);
		
		$params = array(
			"description"=>__("Select the name of the parent for connetion"),
			"origtype" => UniteCreatorDialogParam::PARAM_DROPDOWN
		);
		
		$this->addSelect($prefix."name", $arrNames, __("Remote Parent Name", "unlimited-elements-for-elementor"), "auto", $params);
		
		$isMoreParents = UniteFunctionsUC::getVal($param, "controller_more_parents");
		$isMoreParents = UniteFunctionsUC::strToBool($isMoreParents);
		
		if($isMoreParents == true){
			
			$params = array();
			$params["origtype"] = UniteCreatorDialogParam::PARAM_RADIOBOOLEAN;
						
			$this->addRadioBoolean($prefix."more_parent", __("Connect To One More Parent", "unlimited-elements-for-elementor"), false, "Yes", "No", $params);
			
			$params = array(
				"description"=>__("Select the name of the second parent for connetion both parents in one click"),
				"origtype" => UniteCreatorDialogParam::PARAM_DROPDOWN,
				"elementor_condition" => array($prefix."more_parent"=>"true")
			);
			
			$arrNames = HelperProviderUC::getArrRemoteParentNames(true);
			$arrNames = array_flip($arrNames);
			
			$this->addSelect($prefix."name2", $arrNames, __("Remote Parent Name", "unlimited-elements-for-elementor"), "first", $params);
			
		}
		
		// ---- custom name
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["elementor_condition"] = array($prefix."name"=>"custom");
			
		$this->addTextBox($prefix."custom_name", "", __("Custom Parent Name","unlimited-elements-for-elementor"), $params);
		
		
		// ---- show debug
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_RADIOBOOLEAN;
					
		$this->addRadioBoolean($prefix."show_debug", __("Show Debug", "unlimited-elements-for-elementor"), false, "Yes", "No", $params);

		// ---- hr
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
		
		$this->addHr("hr_remote_child",$params);
		
	}
	
	/**
	 * add remote background settings
	 */
	protected function addRemoteSettingsBackground($name,$value,$title,$param){
		
		$prefix = $name."_";
		
		// --- sync ----
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_RADIOBOOLEAN;
		
		$condition = UniteFunctionsUC::getVal($param, "elementor_condition");
		$params["elementor_condition"] = $condition;

		$this->addRadioBoolean($prefix."sync", __("Enable Sync and Remote", "unlimited-elements-for-elementor"), false, "Yes", "No", $params);
		
		// --- sync name ----
		
		$arrNames = HelperProviderUC::getArrRemoteSyncNames();
		$arrNames = array_flip($arrNames);
		
		$conditionSync = $condition;
		$conditionSync[$prefix."sync"] = "true";
		
		$params = array(
			"origtype" => UniteCreatorDialogParam::PARAM_DROPDOWN,
			"elementor_condition" => $conditionSync
		);
		
		$this->addSelect($prefix."sync_name", $arrNames, __("Sync Group", "unlimited-elements-for-elementor"), "group1", $params);
		
		
		// --- remote name ----
		
		$arrNames = HelperProviderUC::getArrRemoteParentNames();
		$arrNames = array_flip($arrNames);
		
		$conditionSync = $condition;
		$conditionSync[$prefix."sync"] = "true";
		
		$params = array(
			"origtype" => UniteCreatorDialogParam::PARAM_DROPDOWN,
			"elementor_condition" => $conditionSync
		);
		
		$this->addSelect($prefix."remote_name", $arrNames, __("Remote Parent Name", "unlimited-elements-for-elementor"), "auto", $params);
		
		
		//  --- debug ---
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_RADIOBOOLEAN;
		$params["elementor_condition"] = $conditionSync;
		
		$this->addRadioBoolean($prefix."debug", __("Show Debug", "unlimited-elements-for-elementor"), false, "Yes", "No", $params);
		
		
	}
	
	
	/**
	 * add remote settings
	 */
	protected function addRemoteSettings($name,$value,$title,$param){
		
		$type = UniteFunctionsUC::getVal($param, "remote_type");
		
		switch($type){
			case "controller":
				
				$this->addRemoteSettingsController($name,$value,$title,$param);
				
			break;
			case "background":
				
				$this->addRemoteSettingsBackground($name,$value,$title,$param);
				
			break;
			default:
			case "parent":
				
				$this->addRemoteSettingsParent($name,$value,$title,$param);				
			break;
		}
				
	}
	
	
	private function __________DYNAMIC_______(){}
	
	/**
	 * get gallery title title source options
	 */
	protected function getGalleryTitleSourceOptions($isDescription = false, $hasPosts = false){

		if($isDescription == false){
			
			$arrTitleOptions = array();
			
			if($hasPosts){
				$arrTitleOptions["post_title"] = __("Post Title", "unlimited-elements-for-elementor");
				$arrTitleOptions["post_excerpt"]= __("Post Excerpt", "unlimited-elements-for-elementor");
			}
			
			$arrTitleOptions["image_auto"] = __("Image Auto (title or alt or caption)", "unlimited-elements-for-elementor");
			$arrTitleOptions["image_title"] = __("Image Title", "unlimited-elements-for-elementor");
			$arrTitleOptions["image_alt"] = __("Image Alt", "unlimited-elements-for-elementor");
			$arrTitleOptions["image_caption"] = __("Image Caption", "unlimited-elements-for-elementor");
		
			$arrTitleOptions = array_flip($arrTitleOptions);
			
			return($arrTitleOptions);
		}
		
		//description
		
		$arrDescOptions = array();
		
		if($hasPosts == true){
			$arrDescOptions["post_excerpt"]= __("Post Excerpt", "unlimited-elements-for-elementor");
			$arrDescOptions["post_title"] = __("Post Title", "unlimited-elements-for-elementor");
			$arrDescOptions["post_content"] = __("Post Content", "unlimited-elements-for-elementor");
		}
		
		$arrDescOptions["image_description"] = __("Image Description", "unlimited-elements-for-elementor");
		$arrDescOptions["image_title"] = __("Image Title", "unlimited-elements-for-elementor");
		$arrDescOptions["image_alt"] = __("Image Alt", "unlimited-elements-for-elementor");
		$arrDescOptions["image_caption"] = __("Image Caption", "unlimited-elements-for-elementor");
		
		$arrDescOptions = array_flip($arrDescOptions);
		
		return($arrDescOptions);
	}
	
	
	/**
	 * add gallery field
	 */
	protected function addListingPicker_gallery($name,$value,$title,$param){
		
		//---- gallery option
		
		$conditionGallery = array($name."_source" => "gallery");
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_GALLERY;
		$params["elementor_condition"] = $conditionGallery;
		
		$galleryDefaults = HelperProviderUC::getArrDynamicGalleryDefaults();
		
		$this->addTextBox($name."_gallery", $galleryDefaults, __("Choose Images","unlimited-elements-for-elementor"), $params);
		
		//============
		
		$conditionPost = array($name."_source" => "posts");
		$conditionPostProduct = array($name."_source" => array("posts","products") );

		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
		$params["elementor_condition"] = $conditionPostProduct;
		
		$this->addHr($name."_hr_before_title_sources_post",$params);
		
		
		//---- posts options - title source
		
		$arrTitleOptions = $this->getGalleryTitleSourceOptions(false, true);
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["label_block"] = true;
		$params["elementor_condition"] = $conditionPostProduct;
		
		$this->addSelect($name."_title_source_post", $arrTitleOptions, __("Image Title Source", "unlimited-elements-for-elementor"), "post_title", $params);
		
		//---- posts options - description source
		
		$arrDescOptions = $this->getGalleryTitleSourceOptions(true, true);
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["label_block"] = true;
		$params["elementor_condition"] = $conditionPostProduct;
		
		$this->addSelect($name."_description_source_post", $arrDescOptions, __("Image Description Source", "unlimited-elements-for-elementor"), "post_excerpt", $params);
		
		//---- current post meta
		
		$conditionCurrentMeta = array($name."_source" => "current_post_meta");
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["elementor_condition"] = $conditionCurrentMeta;
		
		$this->addTextBox($name."_current_metakey", "", __("Meta Key","unlimited-elements-for-elementor"), $params);
		
		//---- current post meta - DEBUG
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_RADIOBOOLEAN;
		$params["description"] = __("Show the current post meta fields, turn off it after choose the right one", "unlimited-elements-for-elementor");
		$params["elementor_condition"] = $conditionCurrentMeta;
		
		$this->addRadioBoolean($name."_show_metafields", __("Debug - Show Meta Fields", "unlimited-elements-for-elementor"), false, "Yes", "No", $params);

		//=========== GALLERY TITLE AND DESCRIPTION SOURCE =================
		
		//---- hr before title source
		
		$conditionTitleSource = array($name."_source" => array("gallery", "current_post_meta"));
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
		$params["elementor_condition"] = $conditionTitleSource;
		
		$this->addHr($name."_hr_before_title_sources",$params);
		
		
		//---- gallery title source
				
		$arrTitleOptions = $this->getGalleryTitleSourceOptions(false, false);
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["label_block"] = true;
		$params["elementor_condition"] = $conditionTitleSource;
		
		$this->addSelect($name."_title_source_gallery", $arrTitleOptions, __("Image Title Source", "unlimited-elements-for-elementor"), "image_auto", $params);
		
		
		//---- gallery description source
		
		$arrDescOptions = $this->getGalleryTitleSourceOptions(true, false);
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["label_block"] = true;
		$params["elementor_condition"] = $conditionTitleSource;
		
		$this->addSelect($name."_description_source_gallery", $arrDescOptions, __("Image Description Source", "unlimited-elements-for-elementor"), "image_description", $params);
		
		//----- hr before image size
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
		
		$this->addHr($name."_hr_before_imagesize",$params);
		
		//----- thumb image size
		
		$arrSizes = UniteFunctionsWPUC::getArrThumbSizes();
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["label_block"] = true;
		
		$arrSizes = array_flip($arrSizes);
		$this->addSelect($name."_thumb_size", $arrSizes, __("Thumb Image Size", "unlimited-elements-for-elementor"), "medium_large", $params);
		
		
		//----- big image size
		
		$arrSizes = UniteFunctionsWPUC::getArrThumbSizes();
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["label_block"] = true;
		
		$arrSizes = array_flip($arrSizes);
		$this->addSelect($name."_image_size", $arrSizes, __("Big Image Size", "unlimited-elements-for-elementor"), "large", $params);
		

		//=========== GALLERY POSTS VIDEOS =================

		//----- hr before videos
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
		
		$this->addHr($name."_hr_before_videos",$params);
		
		//----- hr before videos
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_RADIOBOOLEAN;
		$params["elementor_condition"] = $conditionPost;
		
		$this->addRadioBoolean($name."_posts_enable_videos", "Enable Videos Items",false,"Yes","No",$params);

		//----- meta field for item type
		
		$condionEnableVideos = $conditionPost;
		$condionEnableVideos[$name."_posts_enable_videos"] = "true";
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["placeholder"] = "example: item_type";
		$params["description"] = "A custom fields that store item type text. The types are: image|youtube|vimeo";
		$params["elementor_condition"] = $condionEnableVideos;
		
		$this->addTextBox($name."_meta_itemtype", "", __("Meta Field for Item Type","unlimited-elements-for-elementor"), $params);
		
		//----- meta field for video id
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["placeholder"] = "example: video_id";
		$params["description"] = "A custom fields that store Youtube ID / link or Vimeo ID";
		$params["elementor_condition"] = $condionEnableVideos;
		
		$this->addTextBox($name."_meta_videoid", "", __("Meta Field for Video ID","unlimited-elements-for-elementor"), $params);

		//----- debug meta fields
		/*
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_RADIOBOOLEAN;
		$params["elementor_condition"] = $condionEnableVideos;
		
		$this->addRadioBoolean($name."_debug_meta", "Debug Meta Fields",false,"Yes","No",$params);
		*/
		
	}
	
	
	/**
	 * add listing picker, function for override
	 */
	protected function addListingPicker($name,$value,$title,$param){
		
		//add template picker
		$useFor = UniteFunctionsUC::getVal($param, "use_for");
		
		if($useFor == "remote"){
			$this->addRemoteSettings($name, $value, $title, $param);
			return(false);
		}
		
		if($useFor == "items"){
			
			$this->addItemsMultisourceSettings($name, $value, $title, $param);
			
			return(false);
		}
		
		$isForGallery = ($useFor == "gallery");
		
		$isEnableVideoItems = UniteFunctionsUC::getVal($param, "gallery_enable_video");
		$isEnableVideoItems = UniteFunctionsUC::strToBool($isEnableVideoItems);
		
		//set text prefix
		$textPrefix = __("Items ","unlimited-elements-for-elementor");		
		if($isForGallery == true)
			$textPrefix = __("Gallery Items ","unlimited-elements-for-elementor");
		
		if($isForGallery == false){
			$params = array();
			$params["origtype"] = UniteCreatorDialogParam::PARAM_TEMPLATE;
			$this->addTextBox($name."_template", "", $textPrefix.__(" Item Template","unlimited-elements-for-elementor"), $params);
		}
		
		//-------------------
		
		// add type select
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		
		$arrSource = array();
		
		if($isForGallery == true){
			$arrSource["gallery"] = __("Gallery", "unlimited-elements-for-elementor");
			
			if($isEnableVideoItems == true)
				$arrSource["image_video_repeater"] = __("Image And Video Items", "unlimited-elements-for-elementor");
			else
				$arrSource["image_video_repeater"] = __("Image Items", "unlimited-elements-for-elementor");
			
			$arrSource["instagram"] = __("Instagram", "unlimited-elements-for-elementor");
		}
		
		$arrSource["posts"] = __("Posts", "unlimited-elements-for-elementor");
		
		$isWooActive = UniteCreatorWooIntegrate::isWooActive();
		if($isWooActive == true)
			$arrSource["products"] = __("Products", "unlimited-elements-for-elementor");
		
		
		if($isForGallery == true){
			$arrSource["current_post_meta"] = __("Current Post Metafield", "unlimited-elements-for-elementor");
		}
		
		//$arrSource["terms"] = __("Terms", "unlimited-elements-for-elementor");
		
		$arrSource = array_flip($arrSource);
		
		$default = "posts";
		if($isForGallery == true)
			$default = "gallery";
		
		$this->addSelect($name."_source", $arrSource, $textPrefix.__("Source", "unlimited-elements-for-elementor"), $default, $params);
		
		if($isForGallery == true)
			$this->addListingPicker_gallery($name,$value,$title,$param);
		
	}
	
	
	private function __________MULTISOURCE_______(){}
	
	
	/**
	 * add items multisource
	 */
	protected function addItemsMultisourceSettings($name, $value, $title, $param){
		
		//pro version - add all settings
		
		if(GlobalsUC::$isProVersion == true){
			
			require_once GlobalsUC::$pathPro."provider_settings_multisource_pro.class.php";
			$objMultisourceSettings = new UniteCreatorSettingsMultisourcePro();
			
		}else {
		
			//free version - add placeholders
			
			$objMultisourceSettings = new UniteCreatorSettingsMultisource();
		}
		
		$objMultisourceSettings->setSettings($this);
		$objMultisourceSettings->addItemsMultisourceSettings($name, $value, $title, $param);
		
	}
	
	private function __________TYPOGRAPHY_______(){}
	
	
	/**
	 * add all the typographyc settings
	 */
	public function addTypographyDialogSettings(){
		
		$arrData = HelperUC::getFontPanelData();
		
		$arrFontFamily = UniteFunctionsUC::getVal($arrData, "arrFontFamily");
		
		$arrFontSize = UniteFunctionsUC::getVal($arrData, "arrFontSize");
		
		$arrFontSize = UniteFunctionsUC::arrayToAssoc($arrFontSize);
		$arrFontSize = UniteFunctionsUC::addArrFirstValue($arrFontSize, "[Default]", "");
		
		$arrGoogleFonts = UniteFunctionsUC::getVal($arrData, "arrGoogleFonts");
		
		$arrFontWeight = UniteFunctionsUC::getVal($arrData, "arrFontWeight");

		$arrFontWeight = UniteFunctionsUC::arrayToAssoc($arrFontWeight);
		$arrFontWeight = UniteFunctionsUC::addArrFirstValue($arrFontWeight, "[Default]", "");
		
		$arrLineHeight = UniteFunctionsUC::getVal($arrData, "arrLineHeight");
		
		$textDecoration = UniteFunctionsUC::getVal($arrData, "arrTextDecoration");
		
		
		//add the settings
		
		$arrFontFamily = array_flip($arrFontFamily);
		$arrFontSize = array_flip($arrFontSize);
		$arrFontWeight = array_flip($arrFontWeight);
		
		//$this->addSelect("font_family", $arrFontFamily, __("Font Family","unlimited-elements-for-elementor"));
		
		//font size
		$params = array();
		$params["selector"] = "%selector%";
		$params["selector_value"] = "font-size:{{VALUE}}px;";
		
		$this->addSelect("font_size", $arrFontSize, __("Font Size","unlimited-elements-for-elementor"),"", $params);
		
		
		//font weight
		
		$params = array();
		$params["selector"] = "%selector%";
		$params["selector_value"] = "font-weight:{{VALUE}};";
		
		$this->addSelect("font_weight", $arrFontWeight, __("Font Weight","unlimited-elements-for-elementor"),"",$params);
		
		
	}
	
	
}