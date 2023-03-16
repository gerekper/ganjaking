"use strict";

function UniteCreatorTestAddonNew(){
	
	var g_settings, g_objPreview, g_addonID, g_requestPreview;
	
	var g_helper = new UniteCreatorHelper();
	
	var t = this;
	
	if(!g_ucAdmin)
		var g_ucAdmin = new UniteAdminUC();
	
	
	/**
	 * output preview
	 */
	function outputWidgetPreview(response){
		
		var html = g_ucAdmin.getVal(response, "html");
		var arrIncludes = g_ucAdmin.getVal(response, "includes");
		
		g_helper.putIncludes(window, arrIncludes, function(){
			
			g_objPreview.html(html);
			
		});
		
	}
	
	
	/**
	 * refresh preview
	 */
	function refreshPreview(){
		
		var objValues = g_settings.getSettingsValues();
				
		var data = {
			id:g_addonID,
			settings: objValues
		};
		
		g_ucAdmin.setAjaxLoaderID("uc_preview_loader");
		
		g_objPreview.addClass("uc-preview-loading");
		
		if(g_requestPreview)
			g_requestPreview.abort();
		
		g_requestPreview = g_ucAdmin.ajaxRequest("get_addon_output_data", data, function(response){
			
			g_objPreview.removeClass("uc-preview-loading");
			
			outputWidgetPreview(response);
			
		});
		
	}
	
	
	/**
	 * init the settings by it's html
	 */
	function initSettingsByHtml(htmlSettings){
		
		var objSettingsContainer = jQuery("#uc_settings_container");
		
		objSettingsContainer.html(htmlSettings);
		
		g_settings = new UniteSettingsUC();
		
		g_settings.init(objSettingsContainer);
		
		g_settings.setEventOnChange(refreshPreview);
	}
	
	
	/**
	 * load the settings from ajax
	 */
	function loadSettings(){
		
		var data = {};
		data["id"] = g_addonID;
		
		g_ucAdmin.setAjaxLoaderID("uc_settings_loader");
		
		g_ucAdmin.ajaxRequest("get_addon_settings_html", data, function(response){
			
			initSettingsByHtml(response.html);
			
			refreshPreview();
			
			//testSettings();
		});
		
		
	}
	
	
	function testSettings(){
		
		setTimeout(function(){

			//get values test

			var values = g_settings.getSettingsValues();
			
			trace(values);
			
			//set values test
			
			setTimeout(function(){
				values.text1 = "other text";
				values.multiple_select = ["option1"];
				
				g_settings.setValues(values);
				
				setTimeout(function(){
					g_settings.clearSettings();
				},500);
				
			},500);
			
			
		},1000);
		
		trace("test settings!");
		
	}
	
	/**
	 * init the testaddon class
	 */
	this.init = function(){
				
		var objWrapper = jQuery("#uc_preview_addon_wrapper");
		g_addonID = objWrapper.data("addonid");
		
		g_objPreview = jQuery("#uc_preview_wrapper");
		
		loadSettings();
		
	}
	
}