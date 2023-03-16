"use strict";

function UniteCreatorAdmin_Template(){
	
	var t = this;
	var g_templateID, g_objWrapper;
	var g_objSettings = new UniteSettingsUC();
	
	
	if(!g_ucAdmin)
		var g_ucAdmin = new UniteAdminUC();
	
	
	function _______GENERAL_________(){}
	
	
	/**
	 * on update layout button click
	 */
	function onUpdateClick(){
		
		var settingsValues = g_objSettings.getSettingsValues();
		
		var data = {
			params: settingsValues
		};
		
		if(g_templateID)
			data.id = g_templateID;
		
		
		g_ucAdmin.setAjaxLoaderID("uc_loader_update");
		g_ucAdmin.setAjaxHideButtonID("uc_button_update_template");
		g_ucAdmin.setSuccessMessageID("uc_message_addon_updated");
		
		
		g_ucAdmin.ajaxRequest("create_update_template", data);
	}
	
	
	function _______INIT_________(){}
    
	
	
	/**
	 * init events
	 */
	function initEvents(){
		
		jQuery("#uc_button_update_template").on("click",onUpdateClick);
		
	}
	
	
	
	/**
	 * objects list view
	 */
	this.initTemplateView = function(){
				
		g_objWrapper = jQuery("#uc_templates_wrapper");
		
		var objSettingsWrapper = jQuery("#uc_template_settings");
		g_objSettings.init(objSettingsWrapper);
		
		initEvents();
		
	};
	
	
	
	
}