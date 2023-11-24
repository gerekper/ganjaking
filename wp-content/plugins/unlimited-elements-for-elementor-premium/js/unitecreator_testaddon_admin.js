"use strict";

function UniteCreatorTestAddon(){
	
	var g_objWrapper, g_objConfig = new UniteCreatorAddonConfig();
	var g_objLoaderSave;
	
	var t = this;
	
	
	/**
	 * on save data event
	 */
	function onSaveDataClick(){
		
		var objData = g_objConfig.getObjData();
		
		if(objData.hasOwnProperty("extra"))
			delete objData["extra"];
		
		g_ucAdmin.setAjaxLoaderID("uc_testaddon_loader_save");
		g_ucAdmin.setAjaxHideButtonID("uc_testaddon_button_save");
		
		g_ucAdmin.ajaxRequest("save_test_addon", objData, function(){
			
			jQuery("#uc_testaddon_slot1").show();
			
			jQuery("#uc_testaddon_button_save").show();
		});
	}

	
	/**
	 * restore data
	 */
	function onRestoreDataClick(){
		
		g_ucAdmin.setAjaxLoaderID("uc_testaddon_loader_restore");
		g_ucAdmin.setAjaxHideButtonID("uc_testaddon_button_restore");
		
		var addonID = g_objConfig.getAddonID();
		var data = {"id":addonID,"slotnum":1};
		
		g_ucAdmin.ajaxRequest("get_test_addon_data", data, function(response){
			
			g_objConfig.setData(response.config, response.items);
			
			jQuery("#uc_testaddon_button_restore").show();
		});
		
	}
	
	
	/**
	 * on clear data click
	 */
	function onDeleteDataClick(){
		
		g_ucAdmin.setAjaxLoaderID("uc_testaddon_loader_delete");
		g_ucAdmin.setAjaxHideButtonID("uc_testaddon_button_delete");
		
		var addonID = g_objConfig.getAddonID();
		var data = {"id":addonID,"slotnum":1};
		
		g_ucAdmin.ajaxRequest("delete_test_addon_data", data, function(response){

			jQuery("#uc_testaddon_button_delete").show();
			
			g_objConfig.clearData();
			jQuery("#uc_testaddon_slot1").hide();
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
		
		jQuery("#uc_testaddon_button_save").on("click",onSaveDataClick);
		
		jQuery("#uc_testaddon_button_delete").on("click",onDeleteDataClick);

		jQuery("#uc_testaddon_button_restore").on("click",onRestoreDataClick);
	
		jQuery("#uc_testaddon_button_clear").on("click",g_objConfig.clearData);
		
	}
	
	
	/**
	 * init test view
	 */
	this.init = function(){
				
		g_objWrapper = jQuery("#uc_testaddon_wrapper");
		
		//init config
		var objConfigWrapper = jQuery("#uc_addon_config");
		
		g_objConfig = new UniteCreatorAddonConfig();
		g_objConfig.init(objConfigWrapper);
		
		initEvents();
	}
	
}