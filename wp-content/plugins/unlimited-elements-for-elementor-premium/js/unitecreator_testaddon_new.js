"use strict";

function UniteCreatorTestAddonNew(){

	var g_settings, g_objPreview, g_addonID, g_requestPreview;

	var g_helper = new UniteCreatorHelper();

	var t = this;

	if(!g_ucAdmin)
		var g_ucAdmin = new UniteAdminUC();


	/**
	 * on check settings values click
	 */
	function onCheckClick(){

		var values = g_settings.getSettingsValues();

		var selectorsCss = g_settings.getSelectorsCss();

		trace("Settings Values Are:");

		trace(values);

		if(selectorsCss){

			trace("Selectors Css:");

			trace(selectorsCss);

		}

	}


	/**
	 * on clear settings click
	 */
	function onClearClick(){

		trace("clear settings");

		g_settings.clearSettings();
	}

	/**
	 * delete slot data
	 */
	function onDeleteDataClick(){

		g_ucAdmin.setAjaxLoaderID("uc_testaddon_loader_delete");
		g_ucAdmin.setAjaxHideButtonID("uc_testaddon_button_delete");

		var data = {"id":g_addonID,"slotnum":1};

		g_ucAdmin.ajaxRequest("delete_test_addon_data", data, function(response){

			jQuery("#uc_testaddon_button_delete").show();

			jQuery("#uc_testaddon_slot1").hide();
		});


	}

	/**
	 * on save data event
	 */
	function onSaveDataClick(){

		var objData = {};
		objData["id"] = g_addonID;

		var values = g_settings.getSettingsValues();

		objData["settings_values"] = values;

		trace("Saving Settings...");
		trace(values);


		g_ucAdmin.setAjaxLoaderID("uc_testaddon_loader_save");
		g_ucAdmin.setAjaxHideButtonID("uc_testaddon_button_save");

		g_ucAdmin.ajaxRequest("save_test_addon", objData, function(){

			jQuery("#uc_testaddon_slot1").show();

			jQuery("#uc_testaddon_button_save").show();

		});
	}

	/**
	 * restore data from saved slot
	 */
	function onRestoreDataClick(){

		g_ucAdmin.setAjaxLoaderID("uc_testaddon_loader_restore");
		g_ucAdmin.setAjaxHideButtonID("uc_testaddon_button_restore");

		var data = {"id":g_addonID,"slotnum":1,"combine":true};

		trace("Restoring Settings...");

		g_ucAdmin.ajaxRequest("get_test_addon_data", data, function(response){

			jQuery("#uc_testaddon_button_restore").show();

			var objValues = g_ucAdmin.getVal(response,"settings_values");

			trace(objValues);

			if(!objValues){
				trace("no settings found");
				return(false);
			}

			g_settings.setValues(objValues);

			setTimeout(function(){

				refreshPreview();

			},500);

		});

	}


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
			settings: objValues,
			selectors:true
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
	 * update settings selectors
	 */
	function updateSelectors(){

		var css = g_settings.getSelectorsCss();

		var objStyle = jQuery("[name=uc_selectors_css]");

		if(objStyle.length == 0)
			throw new Error("No style element found, it should be in the styles");

		objStyle.text(css);

	}


	/**
	 * init the settings by it's html
	 */
	function initSettingsByHtml(htmlSettings){

		var objSettingsWrapper = jQuery("#uc_settings_wrapper");

		objSettingsWrapper.html(htmlSettings);

		g_settings = new UniteSettingsUC();

		g_settings.init(objSettingsWrapper);

		g_settings.setEventOnChange(refreshPreview);

		g_settings.setEventOnSelectorsChange(updateSelectors);

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
	 * init events
	 */
	function initEvents(){

		jQuery("#uc_testaddon_button_save").on("click",onSaveDataClick);

		jQuery("#uc_testaddon_button_restore").on("click",onRestoreDataClick);

		jQuery("#uc_testaddon_button_clear").on("click",onClearClick);

		jQuery("#uc_testaddon_button_check").on("click",onCheckClick);

		jQuery("#uc_testaddon_button_delete").on("click",onDeleteDataClick);

	}


	/**
	 * init the testaddon class
	 */
	this.init = function(){

		var objWrapper = jQuery("#uc_preview_addon_wrapper");
		g_addonID = objWrapper.data("addonid");

		g_objPreview = jQuery("#uc_preview_wrapper");

		initEvents();

		loadSettings();


	}

}
