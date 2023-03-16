"use strict";

function UniteCreatorAddonDefaultsAdmin(){
	
	var g_objWrapper, g_objConfig = new UniteCreatorAddonConfig();
	var g_objLoaderSave, g_options;
	
	var t = this;
	
	if(!g_ucAdmin)
		var g_ucAdmin = new UniteAdminUC();
	
	
	/**
	 * on save data event
	 */
	function onSaveDataClick(){
		
		var objData = g_objConfig.getObjData();
				
		if(objData.hasOwnProperty("extra"))
			delete objData["extra"];
		
		g_ucAdmin.setAjaxLoaderID("uc_addondefaults_loader_save");
		g_ucAdmin.setAjaxHideButtonID("uc_addondefaults_button_save");
		
		g_ucAdmin.ajaxRequest("save_addon_defaults", objData, function(){
			
			jQuery("#uc_addondefaults_button_save").show();
		});
	}

	
	/**
	 * restore data
	 */
	function onRestoreDataClick(){
		
		g_ucAdmin.setAjaxLoaderID("uc_addondefaults_loader_restore");
		g_ucAdmin.setAjaxHideButtonID("uc_addondefaults_button_restore");
		
		var addonID = g_objConfig.getAddonID();
		var data = {"id":addonID,"slotnum":1};
		
		g_ucAdmin.ajaxRequest("get_test_addon_data", data, function(response){
			
			g_objConfig.setData(response.config, response.items);
			
			jQuery("#uc_addondefaults_button_restore").show();
		});
		
	}
	
	
	/**
	 * on clear data click
	 */
	function onDeleteDataClick(){
		
		g_ucAdmin.setAjaxLoaderID("uc_addondefaults_loader_delete");
		g_ucAdmin.setAjaxHideButtonID("uc_addondefaults_button_delete");
		
		var addonID = g_objConfig.getAddonID();
		var data = {"id":addonID,"slotnum":1};
		
		g_ucAdmin.ajaxRequest("delete_test_addon_data", data, function(response){

			jQuery("#uc_addondefaults_button_delete").show();
			
			g_objConfig.clearData();
			
		});
		
	}
	
	
	/**
	 * on show preview - change the buttons
	 */
	function onShowPreview(){
		
		jQuery("#uc_button_preview").hide();
		jQuery("#uc_button_close_preview").show();
		
	}
	
	
	/**
	 * on hide preview - change the buttons
	 */
	function onHidePreview(){
		jQuery("#uc_button_preview").show();
		jQuery("#uc_button_close_preview").hide();
	}
	
	
	/**
	 * init events
	 */
	function initEvents(){
		
		jQuery("#uc_button_preview").on("click",g_objConfig.showPreview);
		jQuery("#uc_button_preview_tab").on("click",g_objConfig.showPreviewNewTab);
		jQuery("#uc_button_close_preview").on("click",g_objConfig.hidePreview);
		
		g_objConfig.onShowPreview(onShowPreview);
		g_objConfig.onHidePreview(onHidePreview);
		
		jQuery("#uc_addondefaults_button_save").on("click",onSaveDataClick);
		
		jQuery("#uc_addondefaults_button_delete").on("click",onDeleteDataClick);

		jQuery("#uc_addondefaults_button_restore").on("click",onRestoreDataClick);
	
		jQuery("#uc_addondefaults_button_clear").on("click",g_objConfig.clearData);
		
	}


	/**
	 * get assets path
	 */
	function getPathAssets(){
		
		var pathAssets = g_options["path_assets"];
		return(pathAssets);
	};
	
	
	/**
	 * get assets url
	 */
	function getUrlAssets(){
		var pathAssets = getPathAssets();
		if(!pathAssets)
			return(pathAssets);
		
		var urlAssets = g_urlAssetsUC + pathAssets + "/";
		
		return(urlAssets);
	}
	

	/**
	 * update path for image select based on the assets path
	 */
	function updateImageSelectPath(){
		
		var pathAddonAssets = getPathAssets();
		if(!pathAddonAssets)
			return(false);
						
				
		if(pathAddonAssets){
			pathAddonAssets = g_pathAssetsUC+pathAddonAssets;
		}
				
		var urlAssets = getUrlAssets();
		
		g_ucAdmin.triggerEvent("update_assets_path", urlAssets);
		
		g_ucAdmin.setAddImagePath(pathAddonAssets, urlAssets);
		
	}
	
	
	
	/**
	 * init test view
	 */
	this.init = function(){
		
		g_objWrapper = jQuery("#uc_addondefaults_wrapper");
		g_options = g_objWrapper.data("options");
		
		//init config
		var objConfigWrapper = jQuery("#uc_addon_config");
		
		updateImageSelectPath();
		 
		g_objConfig = new UniteCreatorAddonConfig();
		g_objConfig.init(objConfigWrapper);
		
		initEvents();
		
	};
	
}