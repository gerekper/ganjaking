"use strict";

function UniteCreatorAddonConfig(){
	
	var g_objWrapper, g_addonName, g_addonType; 
	var g_objSettingsContainer, g_objFontsPanel;
	var g_objConfigTable;
	var g_objTitle, g_objSettings = new UniteSettingsUC();
	
	var g_objPreviewWrapper, g_objIframePreview, g_objManager = new UCManagerAdmin();
	var g_objInputUpdate = null;	//field for put settings values
	
	
	var t = this;
	
	if(!g_ucAdmin)
		var g_ucAdmin = new UniteAdminUC();
	
	var g_temp = {
		
	};
	
	var g_options = {
			addon_id:"",
			title: "",
			url_icon: "",
			enable_items: false,
			admin_labels: null
	};
	
	this.events = {
			SHOW_PREVIEW: "show_preview",
			HIDE_PREVIEW: "hide_preview"
	};
	
	
	/**
	 * validate that addon exists
	 */
	function validateInited(){
		if(!g_addonName)
			throw new Error("Addon name not given");
	}
	
	
	/**
	 * show the addon config
	 */
	this.show = function(){
		g_objWrapper.show();
	};
	
	
	/**
	 * hide the addon config
	 */
	this.hide = function(){
		g_objWrapper.hide();
		
		triggerEvent(t.events.HIDE_PREVIEW);
	};
	
	
	/**
	 * get extra addon data from browser addon data
	 */
	this.getGridAddonDataFromBrowserData = function(objData){
		
		var extra = {};
		extra["id"] = g_ucAdmin.getVal(objData, "id");
		extra["title"] = g_ucAdmin.getVal(objData, "title");
		
		var addonData = {};
		addonData["name"] = g_ucAdmin.getVal(objData, "name");
		addonData["addontype"] = g_ucAdmin.getVal(objData, "addontype");
		
		addonData["extra"] = extra;
		
		return(addonData);
	};
	
	
	/**
	 * get addon data without any config from name and type
	 */
	this.getEmptyAddonData = function(name, addontype){
		
		if(!addontype)
			var addontype = "";
		
		var addonData = {};
		addonData["name"] = name;
		addonData["addontype"] = addontype;
		
		return(addonData);
	};
	
	/**
	 * update addon data setting
	 */
	this.updateAddonDataSetting = function(addonData, settingName, settingValue){
				
		var itemsPrefix = "uc_items_attribute_";
		
		if(!addonData)
			addonData = {};
		
		//check items
		if(settingName.indexOf(itemsPrefix) === 0){
			
			var arrItems = g_ucAdmin.getVal(addonData,"items");
						
			var trimmed = settingName.replace(itemsPrefix,"");
			var posSap = settingName.indexOf("_");
			var numItem = trimmed.substr(0, posSap-1);
			var dataSetting = settingName.replace(itemsPrefix+numItem+"_","");
						
			numItem = parseInt(numItem);
						
			if(addonData.items)				
				addonData.items[numItem][dataSetting] = settingValue;
						
		}else{		//check config
			
			var config = g_ucAdmin.getVal(addonData,"config");
			var isExistsInConfig = g_ucAdmin.isObjPropertyExists(config, settingName);
			
			if(!addonData.config)
				addonData.config = {};
			
			addonData.config[settingName] = settingValue;
			
		}
		
		addonData = removeHtmlSettingsFromData(addonData);
		
		return(addonData);
	}
	
	/**
	 * remove settings from data
	 */
	function removeHtmlSettingsFromData(addonData){
		
		var extra = g_ucAdmin.getVal(addonData,"extra");
		delete extra["html_settings"];
		addonData["extra"] = extra;
		
		return(addonData);
	}
	
	/**
	 * clear duplicated addon data
	 */
	this.clearDuplicatedAddonData = function(addonData){
		
		//remove settings html
		addonData = removeHtmlSettingsFromData(addonData);
		
		return(addonData);		
	}
	
	
	/**
	 * get send to panel data from addon data
	 */
	this.getSendDataFromAddonData = function(addonData){
		
		var sendData = {};
		
		//check for html settings
		var extra = g_ucAdmin.getVal(addonData, "extra");
		
		var htmlSettings = g_ucAdmin.getVal(extra, "html_settings");
		if(htmlSettings){
			sendData["html_settings"] = htmlSettings;
			return(sendData);
		}
		
		var sendFields = ["config","items","fonts","name","addontype"];
		
		for(var index in sendFields){
			var field = sendFields[index];
			sendData[field] = g_ucAdmin.getVal(addonData, field);
		}
		
		//add id
		sendData["id"] = g_ucAdmin.getVal(extra, "id");
				
		return(sendData);
	};
	
	
	/**
	 * get addon data from settings data
	 */
	this.getAddonDataFromSettingsValues = function(objValuesParam){
		
		var objValues = jQuery.extend({}, objValuesParam);
		
		var objData = {};
		objData["items"] = "";
		objData["fonts"] = g_ucAdmin.getVal(objValues, "uc_fonts_panel");
		
		//------ get items
		objData["items"] = g_ucAdmin.getVal(objValues, "uc_items_editor");
		
		delete objValues["uc_items_editor"];
		delete objValues["uc_fonts_panel"];
		
		objData["config"] = objValues;
		
		return(objData);
	};
	
	
	/**
	 * return if the addon data is equal
	 */
	this.isAddonsDataEqual = function(addonData1, addonData2){
		
		if(typeof addonData1 == "object")
			var str1 = JSON.stringify(addonData1);
		else
			var str1 = addonData1;
		
		if(typeof addonData2 == "object")
			var str2 = JSON.stringify(addonData2);
		else
			var str2 = addonData2;
		
		if(str1 === str2)
			return(true);
		
		return(false);		
	};
	
	
	/**
	 * get addon title from data
	 */
	this.getAddonTitle = function(addonData){
		
		var extra = g_ucAdmin.getVal(addonData, "extra");
		var title = g_ucAdmin.getVal(extra, "title");
		
		if(!title)
			title = "Untitled Addon";
		
		return(title);
	};
	
	/**
	 * return command to panel settings on some events
	 */
	this.getPanelCommand = function(eventName, addonData){
		
		var extra = g_ucAdmin.getVal(addonData, "extra");
		
		switch(eventName){
			case "add_addon":
				var hasItems = g_ucAdmin.getVal(extra, "has_items");
				if(hasItems == false)
					return(false);
				
				var numItems = g_ucAdmin.getVal(extra, "num_items");
				if(numItems === 0)
					return("open_items_panel");
			break;
		}
		
	};
	
	
	/**
	 * get additional data to pass the panel
	 */
	this.getPanelData = function(addonData){
		
		var panelData = {};
		
		var extra = g_ucAdmin.getVal(addonData, "extra");
		var addonID = g_ucAdmin.getVal(extra, "id");
		if(addonID){
			panelData["header_edit_link"] = g_ucAdmin.getUrlView("addon", "id="+addonID);
		}
		
		return(panelData);
	};
	
	
	/**
	 * set new addon data, accured on save data from panel settings
	 * remove addon config if exists
	 */
	this.setNewAddonData = function(addonData, addonDataNew){
		
		addonData.config = g_ucAdmin.getVal(addonDataNew,"config");
		addonData.fonts = g_ucAdmin.getVal(addonDataNew,"fonts");
		addonData.items = g_ucAdmin.getVal(addonDataNew,"items");
		
		//remove settings html
		var extra = g_ucAdmin.getVal(addonData,"extra");
		delete extra["html_settings"];
		addonData["extra"] = extra;
		
		return(addonData);
	};
	
	
	/**
	 * set html settings in addon data
	 */
	this.setHtmlSettingsInAddonData = function(addonData, htmlSettings){
		
		var extra = g_ucAdmin.getVal(addonData,"extra");
		if(!extra)
			extra = {};
		
		extra["html_settings"] = htmlSettings;
		
		addonData["extra"] = extra;
		
		return(addonData);
	};
	
	
	/**
	 * load new addon data
	 */
	this.loadNewAddonData = function(data, funcResponse){
				
		g_ucAdmin.ajaxRequest("get_addon_editor_data", data, funcResponse);
		
	};
	
	
	/**
	 * get data object
	 */
	this.getObjData = function(isChangedOnly){
		
		validateInited();
				
		if(isChangedOnly === true){
			
			var objValues = g_objSettings.getSettingsValues(false, true);
						
		}else{
			
			var objValues = g_objSettings.getSettingsValues();
		}
		
		var objExtra = {};
		objExtra["title"] = g_options.title;
		objExtra["url_icon"] = g_options.url_icon;
		objExtra["admin_labels"] = g_options.admin_labels;
		
		var objData = t.getAddonDataFromSettingsValues(objValues);
		
		objData["name"] = g_addonName;
		objData["addontype"] = g_addonType;
		objData["extra"] = objExtra;
				
		return(objData);
	};
	
	
	/**
	 * get addon ID
	 */
	this.getAddonID = function(){
		
		
		return(g_options.addon_id);
	};
	
	
	/**
	 * get json data from the settings
	 */
	function getJsonData(isChangedOnly){
		
		var objData = t.getObjData(isChangedOnly);
		
		var strData = JSON.stringify(objData);
		
		return(strData);
	}

	
	/**
	 * update values field if exists
	 */
	function updateValuesInput(){

		if(!g_objInputUpdate)
			return(false);
		
		if(!g_addonName)
			throw new Error("Addon name should be exists");
		
		var strData = getJsonData();
		
		g_objInputUpdate.val(strData);
	}
	
		
	
	/**
	 * set update input ID, this function should be run before init
	 */
	this.setInputUpdate = function(objInput){
		g_objInputUpdate = objInput;
	}
	
	
	/**
	 * parse options from input
	 */
	function parseInputOptions(optionsInput){
		
		jQuery.each(optionsInput, function(key, value){
			
			if(g_options.hasOwnProperty(key)){
				if(value === "true")
					value = true;
				else
				if(value === "false")
					value = false;
				
				g_options[key] = value;
			}
						
		});

	}
	
	/**
	 * clear addon configuration to default
	 */
	this.clearData = function(){
		validateInited();
		g_objSettings.clearSettings();
		
	};
	
	
	/**
	 * set addon config
	 */
	this.setData = function(settingsData, itemsData, optionsData){
		
		validateInited();
		g_objSettings.setValues(settingsData);
		
	};

	
	/**
	 * get ajax preview url
	 */
	function getPreviewUrl(){
		
		var jsonData = getJsonData(true);
		
		jsonData = encodeURIComponent(jsonData);
		
		var params = "data="+jsonData+"";
		var urlPreview = g_ucAdmin.getUrlAjax("show_preview", params);
		
		return(urlPreview);
	}
	
	/**
	 * validate that preview exists
	 */
	function validatePreviewExists(){
	
		if(!g_objPreviewWrapper)
			throw new Error("The preview container not exists");
		
	}
	
	
	/**
	 * show preview
	 */
	this.showPreview = function(){
		
		validatePreviewExists();
		
		g_objConfigTable.hide();
		g_objPreviewWrapper.show();
		
		var urlPreview = getPreviewUrl();
		g_objIframePreview.attr("src", urlPreview);
		
		triggerEvent(t.events.SHOW_PREVIEW);
	}

	
	/**
	 * hide the preview
	 */
	this.hidePreview = function(){
		g_objIframePreview.attr("src", "");
		g_objPreviewWrapper.hide();

		g_objConfigTable.show();
		
		triggerEvent(t.events.HIDE_PREVIEW);
	}
	
	
	/**
	 * show preview in new tab
	 */
	this.showPreviewNewTab = function(){
		
		var urlPreview = getPreviewUrl();
		window.open(urlPreview);
		
	}
	
	/**
	 * clean tags
	 */
	function cleanNonFormattingTags(htmlContents) {
		
		htmlContents = jQuery(htmlContents);
		
	    if (htmlContents && htmlContents.length) {
	        var result = '';
	        htmlContents.each(function () {
	            var $child = $(this), type = $child.prop('tagName'), isTextNode = this.nodeName == "#text";
	            if (isTextNode) {
	                result += this.textContent;
	            }
	            else if (type == 'B' || type == 'U' || type == 'I' || type == 'BR') { // Allow only these types of tags
	                var innerContent = cleanNonFormattingTags($child.contents());
	                var $newTag = $(document.createElement(type)).html(innerContent);
	                result += $newTag[0].outerHTML;
	            }
	            else {
	                result += cleanNonFormattingTags($child.contents());
	            }
	        });
	        return result;
	    }
	    
	    return htmlContents.text();
	}
	
	
	
	
	
	
	/**
	 * init preview button
	 */
	function initPreview(){
		
		g_objPreviewWrapper = g_objWrapper.find(".uc-addon-config-preview");
				
		if(g_objPreviewWrapper.length == 0){
			g_objPreviewWrapper = null;
			return(false);
		}
		
		g_objIframePreview = g_objPreviewWrapper.find(".uc-preview-iframe");
		
		
	}
		
	
	function ______________EVENTS____________(){};
		
	/**
	 * on settings change event. 
	 * Update field if exists
	 */
	function onSettingsChange(){
		
		if(g_objInputUpdate)
			updateValuesInput();
	}
	
	
	/**
	 * grigger event
	 */
	function triggerEvent(eventName, options){
		
		g_objWrapper.trigger(eventName, options);
	
	}
	
	
	/**
	 * on some event
	 */
	function onEvent(eventName, func){
		
		g_objWrapper.on(eventName, func);
		
	}
	
	
	/**
	 * set on show preview function
	 */
	this.onShowPreview = function(func){
		
		onEvent(t.events.SHOW_PREVIEW, func);
	
	}
	
	
	/**
	 * set on hide preview function
	 */
	this.onHidePreview = function(func){
		
		onEvent(t.events.HIDE_PREVIEW, func);
		
	}
	
	
	/**
	 * init events
	 */
	function initEvens(){
		
		g_objSettings.setEventOnChange(onSettingsChange);
		
	}
	
		
	/**
	 * destroy object
	 */
	this.destroy = function(){
		
		if(!g_objWrapper || g_objWrapper.length == 0)
			return(false);

		g_objSettings.destroy();
				
		g_objWrapper.html("");
		g_objWrapper = null;
		
	};
	
	
	/**
	 * 
	 * @param objWrapper
	 */
	this.init = function(objWrapper, isPreviewMode){
		
		if(g_objWrapper)
			throw new Error("the config is alrady inited, can't init it twice");
		
		g_objWrapper = objWrapper;
		
		if(g_objWrapper.length == 0)
			throw new Error("wrong config object");
		
		
		g_objSettingsContainer = g_objWrapper.find(".uc-addon-config-settings");
		g_objTitle = g_objWrapper.find(".uc-addon-config-title");
		g_objConfigTable = g_objWrapper.find(".uc-addon-config-table");
		
		g_ucAdmin.validateDomElement(g_objConfigTable, "config table: .uc-addon-config-table");
		
		//get name
		g_addonName = g_objWrapper.data("name");
		g_addonType = g_objWrapper.data("addontype");
		
		
		g_ucAdmin.validateNotEmpty(g_addonName, "addon admin");
		
		//get options
		var objOptions = g_objWrapper.data("options");
		parseInputOptions(objOptions);
		
		
		//set settings events
		g_objSettings.init(g_objSettingsContainer);
								
		initEvens();
				
		initPreview();
				
	};
	
	
	
}