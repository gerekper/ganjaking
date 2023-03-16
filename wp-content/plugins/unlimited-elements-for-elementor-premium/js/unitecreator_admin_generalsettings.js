"use strict";

function UniteCreatorAdmin_GeneralSettings(){
	
	var t = this;
	var g_providerAdmin = new UniteProviderAdminUC();
	var g_settings = new UniteSettingsUC();
	var g_saveAction = null;
	
	if(!g_ucAdmin)
		var g_ucAdmin = new UniteAdminUC();
	
	
	/**
	 * on save button click function
	 */
	function onSaveButtonClick(){
		
		g_ucAdmin.validateNotEmpty(g_saveAction, "save action");
		var objButton = jQuery(this);
		var prefix = objButton.data("prefix");
		var settingsKey = objButton.data("settingskey");
				
		var setting_values = g_settings.getSettingsValues();
		
		var data = {settings_values:setting_values};
		
		if(settingsKey)
			data.settings_key = settingsKey;
		
		g_ucAdmin.setAjaxLoaderID(prefix+"_loader_save");
		g_ucAdmin.setSuccessMessageID(prefix+"_message_saved");
		g_ucAdmin.setAjaxHideButtonID(prefix+"_button_save_settings");
		g_ucAdmin.setErrorMessageID(prefix+"_save_settings_error");
		
		g_ucAdmin.ajaxRequest(g_saveAction, data);
		
	}
	
	
	/**
	 * select tab in addon view
	 * tab is the link object to tab
	 */
	function onTabSelect(objTab){
				
		if(objTab.hasClass("uc-tab-selected"))
			return(false);
		
		var contentID = objTab.data("contentid");
		var tabID = objTab.prop("id");
		var tabName = objTab.data("name");
		
		var hash = "tab="+tabName;
		if(!tabName)
			hash = "";
		
		location.hash = hash;
		
		jQuery("#uc_tab_contents .uc-tab-content").hide();
		
		jQuery("#" + contentID).show();
		
		jQuery("#uc_tabs a").not(objTab).removeClass("uc-tab-selected");
		objTab.addClass("uc-tab-selected");
		
	}
	
	
	/**
	 * go to tab by name
	 */
	function gotoTabByName(tabName){
		var tabID = "#uc_tab_"+tabName+"_tablink";
		var objTab = jQuery(tabID);
				
		if(objTab.length)
			onTabSelect(objTab);
			
	}
	
	
	/**
	 * init tabs
	 */
	function initTabs(){
		
		jQuery("#uc_tabs a").on("click",function(){
			var objTab = jQuery(this);
			onTabSelect(objTab);
		});
		
	}
	
	/**
	 * see if there is some tab in the hash, and go to this tab
	 */
	function gotoTabByHash(){
		var hash = location.hash;
		if(!hash)
			return(false);
		if(hash == "#")
			return(false);
		
		if(hash.indexOf("#tab=") !== 0)
			return(false);
		
		var tabName = hash.replace("#tab=", "");
		if(!tabName)
			return(false);
		
		gotoTabByName(tabName);
				
	}
	
	/**
	 * on instagram delete button click
	 */
	function onInstagramDeleteDataClick(){
		
		var objMessage = jQuery("#uc_instagram_reconnect_message");
		var objButtonWrapper = jQuery("#uc_instagram_connect_button_wrapper");
		
		var objInputToken = jQuery("input[name=\"instagram_access_token\"]");
		var objInputUserID = jQuery("input[name=\"instagram_user_id\"]");
		var objInputUserName = jQuery("input[name=\"instagram_username\"]");
		
		//clear buttons
		objInputToken.val("");
		objInputUserID.val("");
		objInputUserName.val("");
		
		//show hide messages
		objMessage.hide();
		objButtonWrapper.show();
	}
	
	
	/**
	 * init general settings view
	 */
	this.initView = function(saveAction){
		
		g_ucAdmin.validateNotEmpty(saveAction, "save action");
		g_saveAction = saveAction;
		
		var objSettingsWrapper = jQuery("#uc_general_settings");
		
		if(objSettingsWrapper.length == 0)
			throw new Error("general settings not found");
		
		initTabs();
		
		g_settings.init(objSettingsWrapper);
		
		//save settings click
		jQuery(".uc-button-save-settings").on("click",onSaveButtonClick);
		
		jQuery("#uc_button_delete_insta_data").on("click", onInstagramDeleteDataClick);
		
		//goto tab by hash
		gotoTabByHash();
		
	};
	
}