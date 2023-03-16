
"use strict";

function UniteSettingsUC(){
	
	var g_arrControls = {};
	var g_arrChildrenControls = {};
	
	var g_IDPrefix = "#unite_setting_";
	var g_colorPicker, g_colorPickerWrapper, g_iconsHash={};
	var g_objParent = null, g_objWrapper = null, g_objSapTabs = null;
	var g_objProvider = new UniteProviderAdminUC();
	
	
	var g_vars = {
		NOT_UPDATE_OPTION: "unite_settings_no_update_value",
		keyupTrashold: 500
	};
	
	var g_temp = {
		settingsID:null,
		handle: null,
		enableTriggerChange: true,
		cacheValues: null,
		objItemsManager: null,
		objFontsPanel:null,
		isSidebar: false,
		isInited: false,
		customSettingsKey: "custom_setting_type",
		colorPickerType: null,
		isRepeaterExists: false
	};
	
	this.events = {
			CHANGE: "settings_change",
			INSTANT_CHANGE: "settings_instant_change",
			AFTER_INIT: "after_init",
			OPEN_CHILD_PANEL: "open_child_panel"
	};
	
	var g_options = {
			show_saps:false,
			saps_type:""
	};
	
	if(!g_ucAdmin)
		var g_ucAdmin = new UniteAdminUC();
	
	var t=this;
	
	
	/**
	 * validate that the parent exists
	 */
	function validateInited(){
		
		if(!g_objParent || g_objParent.length == 0)
			throw new Error("The parent not given, settings not inited");
		
	}
	
	
	/**
	 * compare control values
	 */
	function iscValEQ(controlValue, value){
		
		if(typeof value != "string"){
			
			return jQuery.inArray( controlValue, value) != -1;
		}else{
			return (value.toLowerCase() == controlValue);
		}

	}
	
	
	/**
	 * get input by name and filter by type. 
	 * if not found or filtered, return null
	 */
	this.getInputByName = function(name, type){
		
		var inputID = g_IDPrefix+name;
		var objInput = jQuery(inputID);
		if(objInput.length == 0)
			return(null);
		
		if(!type)
			return(objInput);
		
		var inputType = objInput[0].type;
		if(type != inputType)
			return(null);
		
		return(objInput);
	};
	
	
	/**
	 * close all accordion items
	 */
	function closeAllAccordionItems(formID){
		jQuery("#"+formID+" .unite-postbox .inside").slideUp("fast");
		jQuery("#"+formID+" .unite-postbox .unite-postbox-title").addClass("box_closed");
	}
	
	
	this.__________OTHER_EXTERNAL__________ = function(){};
		
	
	/**
	 * init tipsy
	 */
	function initTipsy(gravity){
		
		if(!g_objWrapper)
			return(false);
		
		if(typeof jQuery("body").tipsy != "function")
			return(false);
				
		if(!gravity)
			gravity = "s";
		
		var tipsyOptions = {
				html:true,
				gravity:"s",
		        delayIn: 1000,
		        selector: ".uc-tip"
		};
		
		g_objWrapper.tipsy(tipsyOptions);
		
	}
	
	
	/**
	 * get all settings inputs
	 */
	function getObjInputs(controlsOnly){
		
		validateInited();
				
		//include
		var selectors = "input, textarea, select, .unite-setting-inline-editor, .unite-setting-input-object";
		var selectorNot = "input[type='button'], input[type='range'], input[type='search']";
		
		if(controlsOnly === true){
			selectors = "input[type='radio'], select, input.unite-setting-addonpicker";
		}else{
			
			//items
			
			if(g_temp.objItemsManager){
				selectors += ", .uc-setting-items-panel";
				selectorNot += ", .uc-setting-items-panel select, .uc-setting-items-panel input, .uc-setting-items-panel textarea";
			}
			
			//fonts
			if(g_temp.objFontsPanel){
				selectors += ", .uc-setting-fonts-panel";
				selectorNot += ", .uc-setting-fonts-panel select, .uc-setting-fonts-panel input, .uc-setting-fonts-panel textarea";
			}
			
			if(g_temp.isRepeaterExists == true)
				selectorNot += ", .unite-settings-repeater *";
			
		}
		
		var objInputs = g_objParent.find(selectors).not(selectorNot);
		
		
		return(objInputs);
	}
	
	
	/**
	 * get input name
	 */
	function getInputName(objInput){
		var name = objInput.attr("name");
		if(!name)
			name = objInput.data("name");
		
		return(name);
	}
	
	
	/**
	 * get input basic type
	 */
	function getInputBasicType(objInput){
		
		if(!objInput){
			console.trace();
			throw new Error("empty input, can't get basic type");
		}
		
		var type = objInput[0].type;
		if(!type)
			type = objInput.prop("tagName").toLowerCase();
		
		switch(type){
			case "select-one":
			case "select-multiple":
				type = "select";
			break;
		}
		
		return(type);
	}
	
	
	/**
	 * get input type
	 */
	function getInputType(objInput){
		
		if(!objInput){
			console.trace();
			throw new Error("empty input, can't get type");
		}
				
		if(!objInput || objInput.length == 0){
			console.trace();
			throw new Error("getInputType - objInput is empty");
		}
			
		if(!objInput[0]){
			trace("wrong input object");
			console.trace();
		}
		
		var type = objInput[0].type;
		if(!type)
			type = objInput.prop("tagName").toLowerCase();
		
		var customType = objInput.data("settingtype");
		
		
		switch(type){
			case "select-multiple":
			case "multiple_select":  // obj name fix
				
				type = "multiselect";
								
				if(objInput.hasClass("select2"))
					type = "select2";
				
				if(customType)
					type = customType;
								
			break;
			case "select-one":
				type = "select";
				if(customType)
					type = customType;
				
				if(objInput.hasClass("select2"))
					type = "select2";
				
			break;
			case "number":
				type = "text";
				
				if(objInput.hasClass("unite-setting-range"))
					type="range";
				
			break;
			case "text":
								
				if(objInput.hasClass("unite-color-picker"))
					type = "color";
				else if(objInput.hasClass("unite-setting-image-input"))
					type = "image";
				else if(objInput.hasClass("unite-setting-mp3-input"))
					type = "mp3";
				else if(objInput.hasClass("unite-postpicker-input"))
					type="post";
				else if(objInput.hasClass("unite-iconpicker-input"))
					type="icon";
				else if(objInput.hasClass("unite-setting-range"))
					type="range";
				else if(objInput.hasClass("unite-setting-addonpicker"))
					type="addon";
				
			break;
			case "textarea":
				if(objInput.hasClass("mce_editable") || objInput.hasClass("wp-editor-area"))
					type = "editor_tinymce";
			break;
			case "div":
				
				type = customType;
				
				if(!type){
					
					if(objInput.hasClass("uc-setting-items-panel"))
						type = "items";
					else
					if(objInput.hasClass("uc-setting-fonts-panel"))
						type = "fonts";
					else
					if(objInput.hasClass("unite-setting-inline-editor"))
						type = "editor_tinymce";
				}
				
			break;
		}
		
		return(type);
	}
	
	
	/**
	 * get input value
	 */
	function getSettingInputValue(objInput){
		
		var name = getInputName(objInput);
		var type = getInputType(objInput);
		var value = objInput.val();
		var inputID = objInput.prop("id");


		
		if(!name)
			return(g_vars.NOT_UPDATE_OPTION);
		
		var flagUpdate = true;
		
		switch(type){
			case "hidden":		//allow to pass objects
				var hiddenValue = objInput.data("input_value");
				if(hiddenValue)
					value = hiddenValue;
			break;
			case "select":
				var selectedText = objInput.children("option:selected").html();
			break;
			case "checkbox":
				value = objInput.is(":checked");
			break;
			case "radio":
				if(objInput.is(":checked") == false) 
					flagUpdate = false;
			break;
			case "button":
				flagUpdate = false;
			break;
			case "editor_tinymce":
				
				if(typeof tinyMCE != "undefined"){
					
					var objEditor = tinyMCE.EditorManager.get(inputID);
					if(objEditor)						
						value = objEditor.getContent();
				}
				
			break;
			case "image":
				var imageID = objInput.data("imageid");
				if(imageID && jQuery.isNumeric(imageID))
					value = imageID;
			case "mp3":
				var source = objInput.data("source");
				
				//convert to relative url if not addon
				if(source != "addon" && jQuery.isNumeric(value) == false)
					value = g_ucAdmin.urlToRelative(value);
			break;				
			case "post":
				value = getPostPickerValue(objInput);
			break;
			case "items":
				value = g_temp.objItemsManager.getItemsData();
			break;
			case "fonts":
				value = t.getFontsPanelData();
			break;
			case "map":
				value = objInput.data("mapdata");
			break;
			case "repeater":
				value = getRepeaterValues(objInput);
			break;
			case "multiselect":
				value = multiSelectModifyAfterGet(value);
			break;
			case "dimentions":
				value = dimentionsGetValues(objInput);
			break;
			case "select2":
			break;
			case "post_select":
				
				trace("get value post select");
				
			break;
			case "gallery":
				
				value = getGalleryValues(objInput);
				
			break;
			default:

				//custom settings
				var objCustomType = getCustomSettingType(type);
				if(objCustomType){
					if(objCustomType.funcGetValue)
						value = objCustomType.funcGetValue(objInput, t);
					else
						value = "";
				}
			break;
		}
		
		
		if(flagUpdate == false)
			return(g_vars.NOT_UPDATE_OPTION);
				
		return(value);
	}
	
	
	/**
	 * get settings values object by the parent
	 */
	this.getSettingsValues = function(controlsOnly, isChangedOnly){
		
		validateInited();
		
		var obj = new Object();
		
		var name,value,type,flagUpdate,inputID;
		
		if(controlsOnly == true)
			var objInputs = getObjInputs(controlsOnly);
		else{
			var objInputs = getObjInputs().not(".unite-setting-transparent");
		}
				
		jQuery.each(objInputs, function(index, input){
			
			var objInput = jQuery(input);
			
			name = getInputName(objInput);
			type = getInputType(objInput);
			value = getSettingInputValue(objInput);
						
			if(value == g_vars.NOT_UPDATE_OPTION)
				return(true);
			
			//remain only changed values from default values
			if(isChangedOnly === true){
				var defaultValue = getInputDefaultValue(objInput);
								
				if(defaultValue === value)
					return(true);
			}
			
			
			inputID = objInput.prop("id");
			
			//set additional vars
			
			switch(type){
				case "select":
					var selectedText = objInput.children("option:selected").html();
					obj[name+"_unite_selected_text"] = selectedText;
				break;
				case "checkbox":
					value = objInput.is(":checked");
				break;
				case "image":
					
					if(value && jQuery.isNumeric(value)){
						obj[name+"_url"] = objInput.data("url");
					}
				break;
			}
			
			
			obj[name] = value;
			
		});
		
		
		return(obj);
	};
	
	
	/**
	 * get default value
	 */
	function getInputDefaultValue(objInput){
		
		var type = getInputType(objInput);
		var name = getInputName(objInput);
		
		var dataname = "default";
		var checkboxDataName = "defaultchecked";
		
		var defaultValue;
		
		switch(type){
			default:
				if(!name)
					return(false);
				
				defaultValue = objInput.data(dataname);
												
				if(typeof defaultValue == "object")
					defaultValue = JSON.stringify(defaultValue);
								
				if(type == "select"){
					if(defaultValue === true)
						defaultValue = "true";
					if(defaultValue === false)
						defaultValue = "false";
				}
				
			break;					
			case "radio":
			case "checkbox":
				defaultValue = objInput.data(checkboxDataName);
				defaultValue = g_ucAdmin.strToBool(defaultValue);				
			break;
		}
		
		if(jQuery.isNumeric(defaultValue))
			defaultValue = defaultValue.toString();
		
		return(defaultValue);
	}
	
	
	/**
	 * clear input
	 */
	function clearInput(objInput, dataname, checkboxDataName){
		
		var name = getInputName(objInput);
				
		var type = getInputType(objInput);
		var inputID = objInput.prop("id");
		var defaultValue;
		
		if(!dataname)
			var dataname = "default";
		
		if(!checkboxDataName)
			var checkboxDataName = "defaultchecked";
		
		
		switch(type){
			case "select":
			case "textarea":
			case "range":
			case "text":
			case "password":
				
				if(!name)
					return(false);
				
				defaultValue = objInput.data(dataname);
												
				if(typeof defaultValue == "object")
					defaultValue = JSON.stringify(defaultValue);
								
				if(type == "select"){
					if(defaultValue === true)
						defaultValue = "true";
					if(defaultValue === false)
						defaultValue = "false";
				}
				
				objInput.val(defaultValue);
				
			break;					
			case "hidden":
				defaultValue = objInput.data(dataname);
				objInput.val(defaultValue);
			break;
			case "icon":
				defaultValue = objInput.data(dataname);
				objInput.val(defaultValue);
				objInput.trigger("blur");
			break;
			case "color":
				
				defaultValue = objInput.data(dataname);
				objInput.val(defaultValue);
				
				if(g_colorPicker)
					g_colorPicker.linkTo(objInput);	
				
				objInput.trigger("change");
				
				//clear manually
				if(defaultValue == "")
					objInput.attr("style","");
				
			break;
			case "checkbox":
				defaultValue = objInput.data(checkboxDataName);
				defaultValue = g_ucAdmin.strToBool(defaultValue);
				
				if(defaultValue == true)
					objInput.attr("checked", true);
				else
					objInput.attr("checked", false);
			break;
			case "radio":
				
				defaultValue = objInput.data(checkboxDataName);
				
				defaultValue = g_ucAdmin.strToBool(defaultValue);
								
				if(defaultValue == true){
					objInput.prop("checked", true);
					
					//maybe change to attr
					
				}
				
			break;
			case "editor_tinymce":
				
				var objEditorWrapper = objInput.parents(".unite-editor-setting-wrapper");
				defaultValue = objEditorWrapper.data(dataname);
					
				if(typeof tinyMCE == "undefined")	//skip the init, if no editor yet
					break;
				
				var objEditor = tinyMCE.EditorManager.get(inputID);
				
				if(objEditor){
					objEditor.setContent(defaultValue);
				}else{
					objInput.val(defaultValue);
				}
				
			break;
			case "addon":
			case "image":
			case "mp3":
				defaultValue = objInput.data(dataname);
				objInput.val(defaultValue);
				objInput.trigger("change");
			break;
			case "post":
				defaultValue = objInput.data(dataname);
				
				setPostPickerValue(objInput, defaultValue);
				
			break;
			case "items":
				if(dataname != "initval")
					g_temp.objItemsManager.clearItemsPanel();
			break;
			case "fonts":
				//don't clear here
			break;
			case "map":
				//don't clear map
			break;
			case "repeater":
				setRepeaterValues(objInput, null, true);
			break;
			case "col_layout":
				//don't clear col layout
			break;
			case "multiselect":
								
				defaultValue = objInput.data(dataname);
				defaultValue = multiSelectModifyForSet(defaultValue);
				
				objInput.val(defaultValue);
			break;
			case "select2":
				
				//no clear for now
			
				/*
				g_temp.enableTriggerChange = false;
				
				defaultValue = objInput.data(dataname);
				objInput.select2("val",defaultValue);
				objInput.trigger("change");
				
				g_temp.enableTriggerChange = true;
				*/
				
			break;
			case "dimentions":
				//no clear for now
			break;
			case "post_select":
				
				trace("clear post select");
				
			break;
			case "gallery":
				
				clearGallery(objInput);
				
			break;
			default:
				
				var objCustomType = getCustomSettingType(type);
				
				if(objCustomType){
					if(objCustomType.funcClearValue)
						objCustomType.funcClearValue(objInput);
				}
				else{
					
					var success = g_ucAdmin.clearProviderSetting(type, objInput, dataname);
					if(success == false){
						trace("for clear - wrong type: " + type);
						trace(objInput);
					}
					
				}
			break;
		}
		
		objInput.removeData("unite_setting_oldvalue");
		
	}

	
	/**
	 * set input value
	 */
	function setInputValue(objInput, value, value2){
				
		var type = getInputType(objInput);
		var inputID = objInput.prop("id");
		var name = objInput.prop("name");
		
		switch(type){
			case "select":
			case "textarea":
			case "text":
			case "password":
								
				objInput.val(value);
			case "hidden":
				if(typeof value == "object"){
					objInput.data("input_value", value);
				}
				else{
					objInput.val(value);
					objInput.data("input_value", null);
				}
			break;
			case "addon":
				
				objInput.val(value);
				objInput.trigger("change");				
			break;
			case "range":
				
				value = parseFloat(value);
				
				objInput.val(value);
				objInput.trigger("input");
			break;
			case "icon":
				objInput.val(value);
				objInput.trigger("blur");
			break;
			case "color":
				objInput.val(value);
				
				if(g_colorPicker)
					g_colorPicker.linkTo(objInput);
				
				objInput.trigger("change");
			break;
			case "checkbox":
				value = g_ucAdmin.strToBool(value);
				
				if(value == true){
					objInput.attr("checked", "checked");
					objInput.prop("checked", "checked");
				}
				else{
					objInput.prop("checked", false);
					objInput.removeAttr("checked");
				}
				
			break;
			case "radio":
				
				var radioValue = objInput.val();		//set by radio text
								
				if(radioValue === "true" || radioValue === "false"){
					radioValue = g_ucAdmin.strToBool(radioValue);
					value = g_ucAdmin.strToBool(value);
				}
				
				if(radioValue === value){
					objInput.prop("checked", true);
				}else{
					
					objInput.removeAttr("checked");
					objInput.attr("checked", false);
				}
				
			break;
			case "editor_tinymce":
				
				if(typeof tinyMCE == "undefined"){	//set textarea content
					
					objInput.val(value);
					
				}else{
					var objEditor = tinyMCE.EditorManager.get(inputID);
					if(objEditor){
						objEditor.setContent(value);
					}else{
						objInput.val(value);
					}
				}
				
			break;
			case "image":
				if(value2){
					objInput.data("imageid",value2);	//set image id
				}
				objInput.data("url", value);
				
				
				
			case "mp3":
				objInput.val(value);
				objInput.trigger("change");
			break;
			case "post":
				
				setPostPickerValue(objInput, value);
				
			break;
			case "items":
				g_temp.objItemsManager.setItemsFromData(value);
			break;
			case "map":
				//set map value
			break;
			case "fonts":
				setFontPanelData(objInput, value);
			break;
			case "multiselect":
				value = multiSelectModifyForSet(value);
				objInput.val(value);
			break;
			case "select2":
				
				objInput.select2("val",value);
				objInput.trigger("change");
				
			break;
			case "post_select":
				
				trace("set value post select");
				
			break;
			case "gallery":
				
				setGalleryValues(value);
				
			break;
			default:
				
				var objCustomType = getCustomSettingType(type);
				if(objCustomType)
					if(objCustomType.funcSetValue)
						objCustomType.funcSetValue(objInput, value);
				else{
					
					//check for provider
					var success =  g_ucAdmin.providerSettingSetValue(type, objInput, value);
					
					//trace error
					if(success == false){
						trace("for setvalue - wrong type: " + type);
					}
					
				}	
				
			break;
		}
		
		
	}
	
	
	/**
	 * clear settings
	 */
	this.clearSettings = function(dataname, checkboxDataName){
				
		validateInited();
		
		var objInputs = getObjInputs();
				
		jQuery.each(objInputs, function(index, input){
			
			var objInput = jQuery(input);
			clearInput(objInput, dataname, checkboxDataName);
		});
	};
	
	
	/**
	 * get field names by type
	 */
	this.getFieldNamesByType = function(type){
		
		validateInited();
		
		var objInputs = getObjInputs();
		var arrFieldsNames = [];
		
		jQuery.each(objInputs, function(index, input){
			var objInput = jQuery(input);
			var name = getInputName(objInput);
			
			var inputType = getInputType(objInput);
			if(inputType == type)
				arrFieldsNames.push(name);
		});
		
		return(arrFieldsNames);
	};

	
	/**
	 * clear settings
	 */
	this.clearSettingsInit = function(){
		
		validateInited();
		
		t.clearSettings("initval","initchecked");
		
	};
	
	
	/**
	 * set single setting value
	 */
	this.setSingleSettingValue = function(name, value){
				
		var objInput = t.getInputByName(name);
		
		if(!objInput || objInput.length == 0)
			return(false);
		
		g_temp.enableTriggerChange = false;
		
		setInputValue(objInput, value);
		
		g_temp.enableTriggerChange = true;
		
	};
	
	
	/**
	 * set values, clear first
	 */
	this.setValues = function(objValues, noClear){
				
		validateInited();
		
		if(noClear !== true)
			this.clearSettings();
		
		
		//if empty values - exit
		if(typeof objValues != "object"){
			return(false);
		}
								
		g_temp.enableTriggerChange = false;
		
		var objInputs = getObjInputs();
		
		//trace(objValues); trace(objInputs);
				
		jQuery.each(objInputs, function(index, input){
			var objInput = jQuery(input);
						
			var name = getInputName(objInput);
			
			if(!name || typeof name == "undefined")
				return(true);
										
			var type = getInputType(objInput);
			
			if(type != "radio")
				clearInput(objInput);
			
			if(objValues.hasOwnProperty(name)){
				var value = objValues[name];
				var value2 = null;
				
				switch(type){
					case "image":
						
						if(jQuery.isNumeric(value)){
							
							var url = g_ucAdmin.getVal(objValues, name+"_url");
							if(url){
								value2 = value;
								value = url;
							}
							
						}else{
							var imageID = g_ucAdmin.getVal(objValues, name+"_imageid");
							if(imageID)
								value2 = imageID;
						}
					break;
				}
				
				setInputValue(objInput, value, value2);
			}
			
		});
		
		applyControls();
		
		g_temp.enableTriggerChange = true;
		
	};

	function _________CUSTOM_SETTING_TYPES__________(){}
	
	
	/**
	 * get custom setting type, if empty setting name - returrn all types
	 */
	function getCustomSettingType(settingName){
		
		var objCustomSettings = g_ucAdmin.getGlobalData(g_temp.customSettingsKey);
		if(!objCustomSettings)
			objCustomSettings = {};
		
		if(!settingName)
			return(objCustomSettings);
		
		var objType = g_ucAdmin.getVal(objCustomSettings, settingName, null);
		
		return(objType);
	}
	
	function _______RANGE_SLIDER_____(){}
	
	
	/**
	 * get range input from input that can be range or text
	 */
	function getRangeInput(objInput){
		
		var objRangeWrapper = objInput.parents(".unite-setting-range-wrapper");
		if(objRangeWrapper.length == 0)
			throw new Error("range input not found");
		
		var inputText = objRangeWrapper.find("input.unite-setting-range");
		return(inputText);
	}
	
	
	/**
	 * init range slider
	 * input can be input text or range
	 */
	function initRangeInput(objInput, funcChange){
				
		var objRangeWrapper = objInput.parents(".unite-setting-range-wrapper");
		if(objRangeWrapper.length == 0)
			return(false);
		
		var isInited = objRangeWrapper.data("inited");
		if(isInited === true)
			return(false);
						
		var inputRange = objRangeWrapper.find("input[type='range']");
		var inputText = objRangeWrapper.find("input.unite-setting-range");
		if(inputText.length == 0)
			throw new Error("range text field not found");
		
		
		//on range change
		inputRange.on("input", function(event){
			var value = jQuery(this).val();
			inputText.val(value);
			
			//trigger instant event
			funcChange(null, inputText, true);
			
		});
		
		//on text change
		inputText.on("input", function(){
			var value = jQuery(this).val();
			
			if(value === "")
				value = jQuery(this).prop("placeholder");
						
			inputRange.val(value);
		});
		
		inputText.on("placeholder_change",function(){
			
			inputText.trigger("input");
		});
		
		objRangeWrapper.data("inited", true);
	}
	


	function _______SELECT2_____(){}


	function initSelect2(objInput){
		
		objInput.select2()
			.on('change', function(e){
				t.onSettingChange(null,objInput,true)
			})
	}

	
	function _______MULTI_SELECT_____(){}

	
	/**
	 * modify value for save, turrn to array
	 */
	function multiSelectModifyForSet(value){
		
		if(typeof value != "string")
			return(value);
		
		value = value.split(",");
		
		return(value);
	}
	
	
	/**
	 * modify value after get
	 */
	function multiSelectModifyAfterGet(value){
		
		if(jQuery.isArray(value) == false)
			return(false);
		
		value = value.join(",");
		
		return(value);
	}
	
	
	function _______COLOR_PICKER_____(){}
	
	
	/**
	 * init farbtastic color picker input events
	 */
	function initColorPickerInputEvents_farbtastic(objInput){
		
		if(!g_colorPickerWrapper){
			initColorPicker();
		}
		
		var input = objInput[0];
		g_colorPicker.linkTo(input);
		
		objInput.focus(function(){
			
			g_colorPicker.linkTo(this);
			
			var bodyWidth = jQuery("body").width();
			
			g_colorPickerWrapper.show();
			var input = jQuery(this);
			var offset = input.offset();
			
			var wrapperWidth = g_colorPickerWrapper.width();
			var inputWidth = input.width();
			var inputHeight = input.height();
			
			var posLeft = offset.left - wrapperWidth / 2 + inputWidth/2;
			
			var posRight = posLeft + wrapperWidth;
			if(posRight > bodyWidth)
				posLeft = bodyWidth - wrapperWidth;
			
			if(posLeft < 0)
				posLeft = 0;
			
			var posTop = offset.top - g_colorPickerWrapper.height() - inputHeight + 10;
			if(posTop < 0)
				posTop = 0;
			
			g_colorPickerWrapper.css({
				"left":posLeft,
				"top":posTop
			});

		}).on("click",function(){			
			return(false);	//prevent body click
		});
		
	}
	
	
	/**
	 * init color picker input
	 */
	function initColorPickerInputEvents(objInput, funcChange){
		
		switch(g_temp.colorPickerType){
			case "farbtastic":
				initColorPickerInputEvents_farbtastic(objInput, funcChange);
			break;
			case "spectrum":
				objInput.spectrum({
					move:function(){
						
						g_ucAdmin.runWithTrashold(function(){
														
							if(funcChange && typeof funcChange == "function"){
								funcChange(null, objInput, false);
							}
							else
								t.onSettingChange(null, objInput, false);
							
							
						});
						
					}
				});
			break;
		}
				
				
	}
	
	/**
	 * check onchange on color input
	 */
	function checkColorInputOnchange(objInput){
		
		if(objInput.val() == "")
			objInput.css("background-color","");
		
	}
	
	
	/**
	 * init farbtastic color picker
	 */
	function initColorPicker_farbtastic(){
		
		g_colorPickerWrapper = jQuery('#divColorPicker');
		if(g_colorPickerWrapper.length == 0){
			jQuery("body").append('<div id="divColorPicker" style="display:none;"></div>');
			g_colorPickerWrapper = jQuery('#divColorPicker');
		}
		
		//init the wrapper itself
		var isInited = g_colorPickerWrapper.data("inited");
		
		if(isInited !== true){
						
			g_colorPickerWrapper.on("click",function(){
				
				return(false);	//prevent body click
			});
			
			jQuery("body").on("click",function(){
				g_colorPickerWrapper.hide();
			});
			
			g_colorPickerWrapper.data("inited", true);
			
		}
		
		if(!g_colorPicker)
		g_colorPicker = jQuery.unite_farbtastic('#divColorPicker', null, function(input, value){
			var objInput = jQuery(input);
			
			objInput.trigger("keyup");
		});
		
	}
	
	
	/**
	 * init color picker
	 */
	function initColorPicker(){
		
		g_temp.colorPickerType = g_ucAdmin.getGeneralSetting("color_picker_type");
		
		switch(g_temp.colorPickerType){
			case "farbtastic":
				initColorPicker_farbtastic();
			break;
			case "spectrum":
			break;
			default:
				throw new Error("Wrong color picker type: " + g_temp.colorPickerType);
			break;
		}
		
		
				
		
	};

	
	function _______MP3_SETTING_____(){}
	
	
	/**
	 * update image url base
	 */
	this.updateMp3FieldState = function(objInput, isEnable){
		
		var objButton = objInput.siblings(".unite-button-choose");
		var objError = objInput.siblings(".unite-setting-mp3-error");
		
		objInput.trigger("change");
		
		if(!isEnable){				//set disabled mode
			
			if(objError.length)
				objError.show();
			
			g_ucAdmin.disableInput(objInput);
			g_ucAdmin.disableButton(objButton);
			
		}else{						//set enabled mode
			
			if(objError.length)
				objError.hide();
			
			g_ucAdmin.enableInput(objInput);
			g_ucAdmin.enableButton(objButton);
		}
		
		
	};
	
	
	/**
	 * on change image click - change the image
	 */
	function onChooseMp3Click(){
		var objButton = jQuery(this);
		
		if(g_ucAdmin.isButtonEnabled(objButton) == false)
			return(true);
		
		var objInput = objButton.siblings(".unite-setting-mp3-input");
		var source = objInput.data("source");
		
		g_ucAdmin.openAddMp3Dialog(g_uctext.choose_audio,function(urlMp3){
			
			if(source == "addon"){		//in that case the url is an object
				var inputValue = urlMp3.url_assets_relative;
				var fullUrl = urlMp3.full_url;
				objInput.data("url", fullUrl);
				
				setInputValue(objInput, inputValue);
			}else{
				setInputValue(objInput, urlMp3);
			}
			
			objInput.trigger("change");
			
		},false, source);
		
	}
	
	function _______IMAGE_SETTING_____(){}
	
	
	/**
	 * set image preview
	 */
	function setImagePreview(){
				
		var objInput = jQuery(this);
						
		if(objInput.length == 0)
			throw new Error("wrong image input given");
				
		var source = objInput.data("source");			
		
		var url = objInput.val();
				
		if(source == "addon"){
			
			if(url == ""){
				objInput.data("urlfull","");
			}
			
			var urlFull = objInput.data("urlfull");
			
			urlFull = g_ucAdmin.urlToFull(url);
			objInput.data("urlfull", urlFull);
			
			url = g_ucAdmin.urlToFull(urlFull);
			
		}else{
			url = g_ucAdmin.urlToFull(url);
		}
		
		var objPreview = objInput.siblings(".unite-setting-image-preview");
		var objWrapper = objInput.parents(".unite-setting-image");
		
		url = jQuery.trim(url);
				
		if(url == ""){
			
			objWrapper.removeClass("unite-image-exists");
			objPreview.css("background-image","");
			objInput.data("imageid","");
			objInput.data("url","");
			
		}else{
			objInput.data("url",url);
			
			objWrapper.addClass("unite-image-exists");
			objPreview.css("background-image","url('"+url+"')");
		}
		
		return(true);
	}
	
	
	/**
	 * on change image click - change the image
	 */
	function onChooseImageClick(event){
		
		//event.preventDefault();
		
		var objButton = jQuery(this);
		
		var objWrapper = objButton.parents(".unite-setting-image");
		
		var objInput = objWrapper.find(".unite-setting-image-input");
		var source = objInput.data("source");
		
		g_ucAdmin.openAddImageDialog(g_uctext.choose_image,function(urlImage, imageID){
			
			if(source == "addon"){		//in that case the url is an object
				var inputValue = urlImage.url_assets_relative;
				var fullUrl = urlImage.full_url;				
				objInput.data("urlfull", fullUrl);
				
				setInputValue(objInput, inputValue);
			}else
				setInputValue(objInput, urlImage, imageID);
			
			objInput.trigger("change");
			
		},false, source);
		
		return(false);
	}
	
	
	/**
	 * on clear image click
	 */
	function onClearImageClick(event){
		
		event.preventDefault();
		
		var objButton = jQuery(this);
		var objWrapper = objButton.parents(".unite-setting-image");
				
		var objInput = objWrapper.find("input.unite-setting-image-input");
		
		objInput.val("");
		objInput.data("urlfull","");
		objInput.data("imageid","");
		objInput.data("url","");
		
		objInput.trigger("change");
		
		return(false);
	}
	
	
	/**
	 * update image url base
	 */
	this.updateImageFieldState = function(objInput, isEnable){
		
		var objError = objInput.siblings(".unite-setting-image-error");
		var objPreview = objInput.siblings(".unite-setting-image-preview");
		var objWrapper = objInput.parents(".unite-setting-image");
		
		objInput.trigger("change");
		
		if(isEnable == false){				//set disabled mode
			
			if(objError.length)
				objError.show();
			
			g_ucAdmin.disableInput(objInput);
			objWrapper.addClass("unite-disabled");
			
		}else{						//activate image input
			if(objError.length)
				objError.hide();
			
			g_ucAdmin.enableInput(objInput);
			objWrapper.removeClass("unite-disabled");
			
			objPreview.show();
			
			var backgroundImage = objPreview.css("background-image");
			
			if(backgroundImage && backgroundImage != "none")
				objWrapper.addClass("unite-image-exists");
			else
				objWrapper.removeClass("unite-image-exists");
			
		}
		
		
	}
	
	
	/**
	 * on update assets path
	 * update all image addon inputs url base
	 */
	function onUpdateAssetsPath(event, urlBase){
		
		validateInited();
		
		var objInputs = getObjInputs();

		objInputs.each(function(index, input){
			
			var objInput = jQuery(input);
			var type = getInputType(objInput);
			if(type != "image" || type != "mp3")
				return(true);
			
			var source = objInput.data("source");
			
			if(source == "addon"){
				var isEnable = true;
				if(!urlBase)
					isEnable = false;
				
				t.updateImageFieldState(objInput, isEnable);
			}
			
		});
		
	}
	
	function _______GALLERY_____(){}
	
	
	/**
	 * clear the gallery
	 */
	function clearGallery(objInput){
		
		trace("clear gallery");
		trace(objInput);
		
	}
	
	/**
	 * init the gallery
	 */
	function initGallery(objInput){
		
		trace("init gallery");
		trace(objInput);
		
	}

	/**
	 * get gallery values
	 */
	function getGalleryValues(objInput){
		
		trace("get gallery values");
		trace(objInput);
		
		return([]);
	}

	/**
	 * get gallery values
	 */
	function setGalleryValues(objInput){
		
		trace("set gallery values");
		trace(objInput);
		
		return([]);
	}
	
	
	function _______SAPS_____(){}
	
	/**
	 * get all sap tabs
	 */
	function getAllSapTabs(){
		
		var objTabs = g_objSapTabs.children("a");
		
		return(objTabs);
	}
	
	
	/**
	 * show sap elmeents
	 */
	function showSapInlineElements(numSap){

		var elementClass = ".unite-sap-" + numSap;
		var objElements = g_objParent.find(".unite-sap-element");
		
		if(objElements.length == 0)
			return(false);
		
		var objSapElements = g_objParent.find(elementClass);
		
		objElements.not(objSapElements).addClass("unite-setting-hidden");
		
		objSapElements.removeClass("unite-setting-hidden");
	}
	
	
	/**
	 * on sap tab click
	 */
	function onSapTabClick(){
		
		var classSelected = "unite-tab-selected";
		
		var objTab = jQuery(this);
		
		if(objTab.hasClass(classSelected))
			return(false);
		
		var allTabs = getAllSapTabs();
		
		allTabs.not(objTab).removeClass(classSelected);
		
		objTab.addClass(classSelected);
		
		var sapNum = objTab.data("sapnum");
		
		showSapInlineElements(sapNum);
			
	}
	
	/**
	 * init saps tabs
	 */
	function initSapsTabs(){
		
		if(!g_objWrapper){
			g_objSapTabs = null;
			return(false);
		}
		
		g_objSapTabs = g_objWrapper.find(".unite-settings-tabs");
		
		if(g_objSapTabs.length == 0){
			
			g_objSapTabs = null;
			return(false);
		}
		
		g_objSapTabs.children("a").on("click",onSapTabClick);
	}
	
		
	
	/**
	 * init saps accordion type
	 */
	function initSapsAccordion(){
		
		if(!g_objWrapper)
			return(false);
		
		var classClosed = "unite-closed";
		
		g_objWrapper.find(".unite-postbox .unite-postbox-title").not(".unite-no-accordion").on("click",function(){
			
			var objHandle = jQuery(this);
			var objInside = objHandle.siblings(".unite-postbox-inside");
			
			//open
			if(objHandle.hasClass(classClosed)){
				
				//close all items
				g_objWrapper.find(".unite-postbox .unite-postbox-inside").not(objInside).slideUp("fast");
				g_objWrapper.find(".unite-postbox .unite-postbox-title").not(objHandle).addClass("unite-closed");
								
				objHandle.removeClass(classClosed);
				objInside.slideDown("fast");
				
			}else{	//close
				objHandle.addClass(classClosed);
				objInside.slideUp("fast");
			}
			
		});
		
	}
	
	
	/**
	 * set accordion max height. set the inner options max height
	 */
	this.setAccordionMaxHeight = function(bodyHeight){
		
		if(!g_objWrapper)
			return(false);
		
		var spaceBetween = g_ucAdmin.getVal(g_options, "accordion_sap", null);
		if(spaceBetween === null){
			trace(g_options);
			throw new Error("Space between accordion items not set in settings options");
		}
				
		var titleHeight = g_ucAdmin.getVal(g_options, "accordion_title_height", null);
		var numTitles = g_objWrapper.find(".unite-postbox .unite-postbox-title").length;
				
		var extraHeight = 0;
		if(numTitles > 0)
			extraHeight = titleHeight * numTitles + spaceBetween * (numTitles-1);
		
		var insideMaxHeight = bodyHeight - extraHeight;
				
		g_objWrapper.find(".unite-postbox-inside").css("max-height",insideMaxHeight+"px");
	};
	
	
	/**
	 * init saps
	 */
	function initSaps(){
				
		if(g_options.show_saps == false)
			return(false);
		
		if(!g_objWrapper)
			return(false);
		
		switch(g_options.saps_type){
			case "saps_type_inline":
				initSapsTabs();
			break;
			case "saps_type_accordion":
				initSapsAccordion();
			break;
			default:
				throw new Error("Init saps error: wrong saps type: " + g_options.saps_type);
			break;
		}
		
	}

	function ______ADDON_PICKER____(){}
	
	
	/**
	 * get addons browser object
	 */
	this.getObjAddonBrowser = function(addonType){
		
		var keyCache = "uc_obj_addons_browsers";
		var objAddonBrowsersCache = g_ucAdmin.getGlobalData(keyCache);
		if(!objAddonBrowsersCache)
			objAddonBrowsersCache = {};
		
		var objBrowser = g_ucAdmin.getVal(objAddonBrowsersCache, addonType);
		
		//init browser if not inited yet
		if(!objBrowser){
			
			var browserID = "uc_addon_browser_"+addonType;
			
			var objBrowserWrapper = jQuery("#" + browserID);
			g_ucAdmin.validateDomElement(objBrowserWrapper,"addons browser with id: "+browserID);
			
			var objBrowser = new UniteCreatorBrowser();
			objBrowser.init(objBrowserWrapper);
			
			objAddonBrowsersCache[addonType] = objBrowser;
			g_ucAdmin.storeGlobalData(keyCache, objAddonBrowsersCache);
		}
		
		return(objBrowser);
	};
	
	
	/**
	 * init addon picker
	 */
	function initAddonPicker(objInput){
		
		var addonType = objInput.data("addontype");
		var objSelectButton = objInput.siblings(".unite-addonpicker-button");
		var objWrapper = objInput.parents(".unite-settings-addonpicker-wrapper");
		var objButtons = objWrapper.find(".uc-action-button");
		var objTitle = objInput.siblings(".unite-addonpicker-title");
		var objBrowser = t.getObjAddonBrowser(addonType);
		var settingName = objInput.prop("name");
		var settingDataName = settingName + "_data";
		var objAddonConfig = new UniteCreatorAddonConfig();
		var settingDataName = settingName + "_data";
		var objInputData = t.getInputByName(settingDataName);
		
		
		objInput.change(function(){
			
			var addonname = objInput.val();
						
			//set empty
			if(!addonname){
				objWrapper.addClass("unite-empty-content");
				objSelectButton.css("background-image", "none");
				if(objTitle.length)
					objTitle.html("");
					
				return(true);
			}
			
			var objData = objBrowser.getAddonData(addonname);
						
			if(!objData)
				return(true);
			
			if(objData.bgimage)
				objSelectButton.css("background-image", objData.bgimage);
			
			objWrapper.removeClass("unite-empty-content");
			
			if(objTitle.length){
				var title = g_ucAdmin.getVal(objData, "title");
				if(!title)
					title = addonname;
				
				objTitle.show().html(title);
			}
			
			//update data
			if(objInputData){
				
				var addonData = objAddonConfig.getGridAddonDataFromBrowserData(objData);
				setInputValue(objInputData, addonData);
				
			}
						
		});
		
		
		//---- select button
		
		objSelectButton.on("click",function(event){
			
			event.stopPropagation();
			event.stopImmediatePropagation();
			
			objBrowser.openAddonsBrowser(null, function(objData){
				
				var addonName = objData.name;
				
				//clear
				if(!addonName){
					
					objInput.val("");
					
					objWrapper.addClass("unite-empty-content");
					objSelectButton.css("background-image", "");
					
					if(objTitle.length)
						objTitle.html("");
					
				}else{
					
					objInput.val(addonName);
					
					if(objData.bgimage)
						objSelectButton.css("background-image", objData.bgimage);
					
					objWrapper.removeClass("unite-empty-content");
					
					if(objTitle.length)
						objTitle.html(objData.title);
					
					//update data
					if(objInputData){
						var addonData = objAddonConfig.getGridAddonDataFromBrowserData(objData);
						setInputValue(objInputData, addonData);
					}
					
				}
								
				t.onSettingChange(null, objInput);
				onControlSettingChange(null, objInput[0]);
				
			}, objSelectButton);
			
		});
		
		// ------------ action buttons 
		
		objButtons.on("click",function(){
			var objButton = jQuery(this);
			var action = objButton.data("action");
						
			switch(action){
				case "clear":
					objInput.val("");
					objInput.trigger("change");
				break;
				case "configure":
					
					var configureAction = objButton.data("configureaction");
					g_ucAdmin.validateNotEmpty(configureAction, "configure action");
					
					g_ucAdmin.validateDomElement(objInputData, "addon picker input data");
					var addonData = getSettingInputValue(objInputData);
					
					if(!addonData){
						var addonName = objInput.val();
						addonData = objAddonConfig.getEmptyAddonData(addonName, addonType);
					}
					
					//open the panel
					var sendData = objAddonConfig.getSendDataFromAddonData(addonData);
					var panelTitle = objAddonConfig.getAddonTitle(addonData);
					var panelData = objAddonConfig.getPanelData(addonData);
					
					var options = {
							pane_name: "addon-settings",
							send_data: sendData,
							panel_title: panelTitle,
							panel_data: panelData,
							setting_name: settingDataName,
							changing_setting_name: settingName,
							addon_data: addonData
					};
					
					
					triggerEvent(t.events.OPEN_CHILD_PANEL, options);
				break;
			}
			
		});
		
	}
	
	function ______ICON_PICKER____(){}
	
	
	/**
	 * add icons type
	 */
	this.iconPicker_addIconsType = function(name, arrIcons, iconsTemplate, optParams){
		
		var key = "icon_picker_type_"+name;
		var objType = g_ucAdmin.getGlobalData(key);
		if(objType)
			return(false);
		
		var params = {
				"name": name,
				"icons":arrIcons,
				"template":iconsTemplate
		};
		
		if(optParams)
			jQuery.extend(params, optParams);
			
		g_ucAdmin.storeGlobalData(key, params);
	};
	
	
	/**
	 * get icons type object
	 */
	function iconPicker_getObjIconsType(name){
		var key = "icon_picker_type_"+name;
		var objType = g_ucAdmin.getGlobalData(key);
		
		if(!objType)
			throw new Error("Icons type: "+name+" not found");
		
		return(objType);
	}
	
	
	/**
	 * init the dialog
	 */
	function iconPicker_initDialog(type){
		
		if(!type)
			var type = "fa";
		
		var dialogID = "unite_icon_picker_dialog_"+type;
		
		var objDialogWrapper = jQuery("#"+dialogID);
		if(objDialogWrapper.length){
			g_iconsHash = jQuery("body").data("uc_icons_hash");
						
			return(objDialogWrapper);
		}
		
		if(type == "elementor" && g_ucFaIcons.length == 0)
			type = "fa";
		
		//set "fa" template
		if(type == "fa"){
			
			t.iconPicker_addIconsType("fa", g_ucFaIcons, function(icon){	//icon to html functoin
				var classAdd = "fa fa-";
				if(icon.indexOf("fa-") != -1)
					classAdd = "";
				 
				var html = '<i class="'+classAdd+icon+'"></i>';
								
				return(html);
			});	
			
		}else if(type == "elementor"){
			
			t.iconPicker_addIconsType("elementor", g_ucElIcons, function(icon){	//icon to html functoin
				
				var html = '<i class="'+icon+'"></i>';
								
				return(html);
			});	
			
			
			
		}
		
		var objType = iconPicker_getObjIconsType(type);
		var isAddNew = g_ucAdmin.getVal(objType, "add_new");
		
		
		var htmlDialog = '<div id="'+dialogID+'" class="unite-icon-picker-dialog unite-inputs unite-picker-type-'+type+'" style="display:none">';
		htmlDialog += '<div class="unite-iconpicker-dialog-top">';
		htmlDialog += '<input class="unite-iconpicker-dialog-input-filter" type="text" placeholder="Type to filter" value="">';
		htmlDialog += '<span class="unite-iconpicker-dialog-icon-name"></span>';
		
		//add new functionality
		/*
		if(isAddNew === true){
			htmlDialog += '<a class="unite-button-secondary unite-iconpicker-dialog-button-addnew">Add New Shape</a>';
		}
		*/
		
		htmlDialog += '</div>';
		htmlDialog += '<div class="unite-iconpicker-dialog-icons-container"></div></div>';
		
		
		jQuery("body").append(htmlDialog);
		
		objDialogWrapper = jQuery('#'+dialogID);
		
		var objContainer = objDialogWrapper.find('.unite-iconpicker-dialog-icons-container');
		var objFilter = objDialogWrapper.find('.unite-iconpicker-dialog-input-filter');
		var objIconName = objDialogWrapper.find(".unite-iconpicker-dialog-icon-name");
		
		//set the icons
		
		var arrIcons = [];
					
		arrIcons = objType.icons;
		var iconTemplate = objType.template;
								
		var isArray = jQuery.isArray(arrIcons);
		
		jQuery.each(arrIcons, function(index, icon) {
			
			var iconTitle = null;
			
			if(isArray == false){
				iconTitle = icon;
				icon = index;
			}
			
			var hashName = icon+"_"+type;
			
			if(typeof iconTemplate == "function"){
				var iconHtml = iconTemplate(icon);
			}else{
				var iconHtml = iconTemplate.replace("[icon]", icon);
			}
			
			var objIcon = jQuery('<span class="unite-iconpicker-icon">'+iconHtml+'</span>');
			
			var iconName = icon;
			if(objType && typeof objType.getIconName == "function")
				iconName = objType.getIconName(icon);
			
			//avoid doubles
			if(g_iconsHash.hasOwnProperty(hashName) == false){
				objIcon.data('title', iconTitle);
				objIcon.data('name', iconName);
				objIcon.data('value', icon);
				
				objContainer.append(objIcon);
				g_iconsHash[hashName] = objIcon;
			}
			
		});
		
		jQuery("body").data("uc_icons_hash", g_iconsHash);
		
		var dialogTitle = "Choose Icon";
		if(type == "shape")
			dialogTitle = "Choose Shape";
		
		
		objDialogWrapper.dialog({
			autoOpen: false,
			height: 500,
			width: 800,
			dialogClass:"unite-ui unite-ui2",
			title: dialogTitle,
			open: function( event, ui ) {
			  
			  objContainer.scrollTop(0);
			  
			  var objSelectedIcon = objContainer.find('.icon-selected');
			  if (!objSelectedIcon.length) 
				  return(false);
			  
			  if(objSelectedIcon.is(":hidden") == true)
				  return(false);
			  
			  //scroll to icon
			  var containerHeight = objContainer.height();
			  var iconPos = objSelectedIcon.position().top;
			  
			  if(iconPos > containerHeight)
				  objContainer.scrollTop(iconPos - (containerHeight / 2 - 50) );
			}
		
		  });
		
		//init events
		objContainer.on('click', '.unite-iconpicker-icon', function (event) {
				
				objContainer.find('.icon-selected').removeClass('icon-selected');
				var objIcon = jQuery(event.target).closest('.unite-iconpicker-icon');
				objIcon.addClass('icon-selected');
				
				var iconName = objIcon.data('name');
				var iconValue = objIcon.data('value');
				var iconTitle = objIcon.data('title');
				
				//update picker object
				var objPicker = objDialogWrapper.data("objpicker");
				var objPickerInput = objPicker.find(".unite-iconpicker-input");
				var objPickerButton = objPicker.find(".unite-iconpicker-button");
				var objPickerTitle = objPicker.find(".unite-iconpicker-title");
				
				var inputValue = iconValue;
				if(type == "fa"){
					if(iconName.indexOf("fa-") == -1)
						inputValue = 'fa fa-' + iconName;
					else
						inputValue = iconName;
				}
				
				objPickerInput.val(inputValue);
				
				if(typeof iconTemplate == "function")
					var htmlIcon = iconTemplate(iconValue);
				else 
					var htmlIcon = iconTemplate.replace("[icon]", iconValue);
				
				objPickerButton.html(htmlIcon);
				
				//set title
				if(iconTitle)
					objPickerTitle.show().html(iconTitle);
				else
					objPickerTitle.hide()
				
				objPickerInput.trigger("change");
				
				//close dialog
				objDialogWrapper.dialog("close");
		});
		
		//on icon mouseover
		objContainer.on('mouseenter', '.unite-iconpicker-icon', function (event) {
			
			var objIcon = jQuery(event.target).closest('.unite-iconpicker-icon');
			
			var iconNameStr = objIcon.data('name');
			var iconTitle = objIcon.data('title');
				
			var iconName = iconNameStr;
			if(iconTitle)
				iconName = iconTitle;
			
			if(type == "fa"){
				
				iconName = iconName.replace("fa-", "");
				
				if(iconName.indexOf("fab ") == 0)
					iconName = iconName.replace("fab ", "")+" [brand]";
				else
					if(iconName.indexOf("fal ") == 0)
						iconName = iconName.replace("fal ", "")+" [light]";
				else
					if(iconName.indexOf("far ") == 0)
						iconName = iconName.replace("far ", "")+" [regular]";
				else
					iconName = iconName+" [solid]";
				
			}
			
			objIconName.text(iconName);
		});
		
		//on icon mouseover
		objContainer.on('mouseleave', '.unite-iconpicker-icon', function (event) {
			objIconName.text("");
		});
		
			
		//filter functionality
		objFilter.on('keyup', function () {
			
			var strFilter = objFilter.val();
			strFilter = jQuery.trim(strFilter);
			
			var objIcons = objDialogWrapper.find(".unite-iconpicker-icon");
			
			jQuery(objIcons).each(function(index, icon){
				  var objIcon = jQuery(icon);
				  var name = objIcon.data("name");
				  				  
				  var isVisible = false;
				  if(strFilter == "" || name.indexOf(strFilter) !== -1)
					  isVisible = true;
				  
				  if(isVisible == true)
					  objIcon.show();
				  else
					  objIcon.hide();
				  
			});
		  });
		  
		
		return(objDialogWrapper);
	}
	
	
	
	/**
	 * init icon picker raw function
	 */
	function initIconPicker(objInput){
		
		var iconsType = objInput.data("icons_type");
		if(!iconsType)
			iconsType = "fa";
		
		var objDialogWrapper = iconPicker_initDialog(iconsType);
		
		if(!objDialogWrapper || objDialogWrapper.length == 0){
			trace("icon picker dialog not inited");
			return(false);
		}
		
		var objPickerWrapper = objInput.parents(".unite-settings-iconpicker");
		var objInput = objPickerWrapper.find('input.unite-iconpicker-input');
		var objButton = objPickerWrapper.find('.unite-iconpicker-button');
		var objTitle = objPickerWrapper.find('.unite-iconpicker-title');
		
		//on button click
		objButton.on("click",function () {
			
				if (objDialogWrapper.dialog('isOpen')) {
					objDialogWrapper.dialog('close');
				} else {
					objDialogWrapper.data("objpicker", objPickerWrapper);
					objDialogWrapper.dialog('open');
				}
		});
		
		
		//on input blur
		objInput.on('blur', function () {
			
			var value = jQuery(this).val();
			
			if(iconsType == "fa")
				value = value.replace("fa fa-","");
			
			value = jQuery.trim(value);
			
			var hashName = value+"_"+iconsType;
			
			if(!g_iconsHash[hashName]){
				var text = "choose";
				if(iconsType == "shape")
					text = "Choose Shape";
				
				objButton.html(text);
				return(false);
			}
			
			var objIcon = g_iconsHash[hashName];
			var iconTitle = objIcon.data("title");
			
			var objType = iconPicker_getObjIconsType(iconsType);
			if(!objType.template)
				throw new Error("icon template not found");
			
			if(typeof objType.template == "function")
				var htmlIcon = objType.template(value);				
			else
				var htmlIcon = objType.template.replace("[icon]", value);
			
			objButton.html(htmlIcon);
			
			//set title
			if(iconTitle)
				objTitle.show().html(iconTitle);
			else
				objTitle.hide();
			
			
			//set selected icon in dialog
			var objContainer = objDialogWrapper.find('.unite-iconpicker-dialog-icons-container');
			
			objContainer.find('.icon-selected').removeClass('icon-selected');
			objIcon.addClass('icon-selected');
			
		});
		
		objInput.trigger("blur");
		
	}
	
	
	function ______MAP_PICKER____(){}
	
	
	/**
	 * init google map picker
	 */
	function initMapPicker(objPickerWrapper){
		
		var dialogTitle = objPickerWrapper.data("dialogtitle");
		
		var objButton = objPickerWrapper.find(".unite-mappicker-button");
		var objOverlay = objPickerWrapper.find(".unite-mappicker-chooser-overlay");
		g_ucAdmin.validateDomElement(objButton, "choose map button");
		
		
		objButton.on("click",function(){
			
			var dialogOptions = {};
			dialogOptions["title"] = dialogTitle;
			
			var onMapUpdate = function(data, objDialog){
				
				var urlMapImage = data.url_static_map;
				objPickerWrapper.find(".unite-mappicker-mapimage").attr("src", urlMapImage);
				
				objPickerWrapper.data("mapdata", data);
				
				t.onSettingChange(null, objPickerWrapper);
				
				setTimeout(function(){
					objDialog.dialog("close");
				}, 100);
			};
			
			var urlParams = {};
			var mapData = objPickerWrapper.data("mapdata");
			window.uc_mappicker_data = mapData;
			
			g_ucAdmin.openIframeDialog("mappicker", urlParams, dialogOptions, onMapUpdate);
						
		});
		
		objOverlay.on("click",function(){
			objButton.trigger("click");
		});
	}
	
	
	function ______POST_PICKER____(){}
	
	/**
	 * init post picker 
	 */
	function initPostPicker(objWrapper){
		
		//fix select focus inside jquery ui dialogs
		g_ucAdmin.fixModalDialogSelect2();
		
		var objSelect = objWrapper.find(".unite-setting-post-picker");
		
		var postID = objSelect.data("postid");
		var postTitle = objSelect.data("posttitle");
		var placeholder = objSelect.data("placeholder");
		
		var data = [];
		if(postID && postTitle){
			
			data.push({
				id:postID,
				text: postTitle
			});
		}
		
		var urlAjax = g_ucAdmin.getUrlAjax("get_posts_list_forselect");
		
		objSelect.select2({
			data:data,
			placeholder: placeholder,
			allowClear: true,
			ajax:{
				url:urlAjax,
				dataType:"json"
			}
		});
		
		
		//on change - trigger change event
		objSelect.on("change", function (e) { 
			
			t.onSettingChange(null, objWrapper, false);
			
		});
		
	}
	
	/**
	 * set post picker value
	 */
	function setPostPickerValue(objWrapper, value){
				
		var objSelect = objWrapper.find(".unite-setting-post-picker");
				
		objSelect.val(value);
	}
	
	
	/**
	 * get post picker value
	 */
	function getPostPickerValue(objWrapper){
		
		var objSelect = objWrapper.find(".unite-setting-post-picker");
				
		var value = objSelect.select2("val");
				
		return(value);
	}
		
	function _______ANIMATIONS_____(){}
	
	/**
	 * on settings animation change, run the demo
	 */
	function onAnimationSettingChange(){
		
		var objSelect = jQuery(this);
		var objParent = objSelect.parents("table");
		if(objParent.length == 0)
			objParent = objSelect.parents("ul");
			
		g_ucAdmin.validateDomElement(objParent, "Animation setting parent");
		
		var objDemo = objParent.find(".uc-animation-demo span");
		var animation = objSelect.val();
		
		g_ucAdmin.validateDomElement(objDemo, "Animation setting demo");
		
		var className = animation + ' animated';
		objDemo.removeClass().addClass(className).one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){
		      jQuery(this).removeClass();
		 });
		
	}
	
	
	/**
	 * init animations selector settings
	 */
	function initAnimationsSelector(){
		
		if(!g_objWrapper)
			return(false);
		
		//init animations
		var objAnimations = g_objWrapper.find("select.uc-select-animation-type");
		
		if(objAnimations.length == 0)
			return(false);
		
		objAnimations.change(onAnimationSettingChange);
		
	}
	
	function _____ITEMS_PANEL_____(){}
	
	
	/**
	 * open the items manager in dialog
	 */
	function onItemsPanelEditItemsClick(){
		
		var objButton = jQuery(this);
		var objSetting = objButton.parents(".uc-setting-items-panel");
		var settingID = objSetting.prop("id");
		var dialogID = settingID+"_dialog";
		
		var objDialog = jQuery("#"+dialogID);
		
		g_ucAdmin.validateDomElement(objDialog, "items dialog");
		
		var buttonOpts = {};
		
		buttonOpts[g_uctext.update] = function(){
			objDialog.dialog("close");
			
			var objItemsWrapper = g_objParent.find(".uc-setting-items-panel");
			t.onSettingChange(null, objItemsWrapper);
		};
		
		var dialogOptions = {
				buttons:buttonOpts,
				minWidth:800,
				modal:true,
				dialogClass:"unite-ui",
				open:function(){
				}
			};
		
		objDialog.dialog(dialogOptions);
		
	}
	
	
	/**
	 * init items panel setting
	 */
	function initItemsPanel(){
		
		var objItemsWrapper = g_objParent.find(".uc-setting-items-panel");
		if(objItemsWrapper.length == 0)
			return(false);
		
		if(objItemsWrapper.length != 1){
			throw new Error("There must be only 1 items panel");
		}
					
		g_temp.objItemsManager = new UCManagerAdmin();
		g_temp.objItemsManager.initManager();
		
		//side panel dialog
		
		var objButtonEditItems = objItemsWrapper.find(".uc-setting-items-panel-button");
		if(objButtonEditItems.length){
			objButtonEditItems.on("click",onItemsPanelEditItemsClick);
		}
		
	}
	
	function __________FONTS_PANEL__________(){}
	
	
	/**
	 * on font panel setting change
	 */
	function onFontPanelInputChange(event, objInput, isInstant){
		
		if(!objInput)
			var objInput = jQuery(event.target);
		
		var objFontsWrapper = objInput.parents(".uc-setting-fonts-panel");
		
		var type = getInputType(objInput);
		
		if(type == "color")
			checkColorInputOnchange(objInput);
				
		if(isInstant !== true){
			updateInputChildPlaceholders(objInput);
		}
		
		//throw font panel setting change event
		t.onSettingChange(null, objFontsWrapper, isInstant);
	}
	
	
	/**
	 * init fonts panel
	 */
	this.initFontsPanel = function(objFontsWrapper){
				
		if(!objFontsWrapper)
			var objFontsWrapper = g_objParent.find(".uc-setting-fonts-panel");
		
		if(objFontsWrapper.length == 0)
			return(false);
				
		//if init without parent
		if(!g_objParent)
			g_objParent = objFontsWrapper.parent();
			
			
		g_temp.objFontsPanel = objFontsWrapper.find(".uc-fontspanel");
		if(g_temp.objFontsPanel.length == 0){
			g_temp.objFontsPanel = null;
			return(null);
		}
			
		
		//checkbox event
		g_temp.objFontsPanel.find(".uc-fontspanel-toggle").on("click",function(){
				
			var objCheck = jQuery(this);
			var sectionID = objCheck.data("target");
			var objSection = jQuery("#" + sectionID);
			g_ucAdmin.validateDomElement(objSection, "fonts panel section");
			
			if(objCheck.is(":checked")){
				
				objSection.show();
				
			}else{
				
				objSection.hide();
			}
			
		});
		
		//init inputs
		var objInputs = g_temp.objFontsPanel.find("input, select");
		jQuery.each(objInputs, function(index, input){
			var objInput = jQuery(input);
			
			var type = getInputType(objInput);
			
			initInputEvents(objInput, onFontPanelInputChange);
			
		});
		
		
		return(g_temp.objFontsPanel);
	};
	
	
	/**
	 * get fonts panel data
	 */
	this.getFontsPanelData = function(){
		
		if(!g_temp.objFontsPanel)
			return(null);
		
		var objData = {};
		var objCheckboxes = g_temp.objFontsPanel.find(".uc-fontspanel-toggle");
		jQuery.each(objCheckboxes, function(index, checkbox){
			
			var objCheckbox = jQuery(checkbox);
			
			if(objCheckbox.is(":checked") == false)
				return(true);
			
			var sectionID = objCheckbox.data("target");
			var sectionName = objCheckbox.data("sectionname");
			
			var objSection = jQuery("#" + sectionID);
			g_ucAdmin.validateDomElement(objSection, "fonts panel section "+sectionID);
			
			//get fields values
			var objFields = objSection.find(".uc-fontspanel-field");
			
			var fieldsValues = {};
			jQuery.each(objFields, function(index, field){
			
				var objField = jQuery(field);
				
				var fieldName = objField.data("fieldname");
				var value = objField.val();
								
				if(jQuery.trim(value) == "")
					return(true);
				
				fieldsValues[fieldName] = value;
				
			});
						
			if(jQuery.isEmptyObject(fieldsValues) == false)
				objData[sectionName] = fieldsValues;
			
		});
		
		return(objData);
	};
	
	
	/**
	 * set fonts panel data
	 */
	function setFontPanelData(objInput, arrData){
				
		clearFontsPanelData(objInput);
		
		jQuery.each(arrData, function(sectionName, objFields){
			
			//check toggle
			var objToggle = objInput.find(".uc-fontspanel-toggle-"+sectionName);
			if(objToggle.length == 0)
				return(true);
			
			objToggle.attr("checked","checked");
			objToggle.prop("checked","checked");
						
			//open section
			var sectionID = objToggle.data("target");
						
			var objSection = jQuery("#"+sectionID);
			
			g_ucAdmin.validateDomElement(objSection, "section: "+sectionID);
			
			objSection.show();
							
			//go through the section fields
			jQuery.each(objFields, function(fieldName, fieldValue){
				
				var objInput = objSection.find(".uc-fontspanel-field[data-fieldname="+fieldName+"]");
				
				if(!objInput || objInput.length == 0){
					
					if(fieldName == "mobile-size")
						return(true);
					
					throw new Error("field not found: "+fieldName);
				}
								
				setInputValue(objInput, fieldValue);
				
			});
			
		});
		
	}
	
	
	/**
	 * clear data
	 */
	function clearFontsPanelData(objInput){
		
		//uncheck toggles
		var objToggles = objInput.find(".uc-fontspanel-toggle");
		
		objToggles.removeAttr("checked");
		objToggles.attr("checked",false);
		
		//hide fields
		var objSections = objInput.find(".uc-fontspanel-section");
		objSections.hide();
		
		//clear inputs
		var objInputs = objSections.find("input.uc-fontspanel-field");
		objInputs.val("");
		
	}
	
	
	/**
	 * destroy fonts panel
	 */
	this.destroyFontsPanel = function(){
		
		if(!g_temp.objFontsPanel)
			return(false);
		
		g_temp.objFontsPanel.find(".uc-fontspanel-toggle").off("click");
		g_temp.objFontsPanel = null;
		
	}
	
	
	/**
	 * destroy repeaters
	 */
	this.destroyRepeaters = function(){
		
		var objRepeaterItems = g_objParent.find(".unite-repeater-item");
		
		if(objRepeaterItems.length == 0)
			return(false);
		
		var objRepeaters = g_objParent.find(".unite-settings-repeater");
		objRepeaters.sortable("destroy");
		
		
		jQuery.each(objRepeaterItems, function(index, item){
			
			var objItem = jQuery(item);
			var objSettings = objItem.data("itemsettings");
			objSettings.destroy();
		});
		
		objRepeaterItems.find(".unite-repeater-item-head").off("click");
				
	}
	
	
	function _______REPEATERS_____(){}

	/**
	 * set repetaer values
	 */
	function setRepeaterValues(objWrapper, arrValues, takeDefault){
		
		if(takeDefault === true)
			var arrValues = objWrapper.data("itemvalues");
		
		var objItemsWrapper = objWrapper.find(".unite-repeater-items");
		
		//clear existing
		objItemsWrapper.html("");
		
		if(!arrValues)
			return(false);
		
		if(jQuery.isArray(arrValues) == false)
			return(false);
		
		
		jQuery.each(arrValues, function(index, objItemValues){
			
			repeaterAddItem(null, objWrapper, objItemValues);
			
		});
		
	}
	
	
	/**
	 * get repeater values
	 */
	function getRepeaterValues(objWrapper){
		
		var objItemsWrapper = objWrapper.find(".unite-repeater-items");
		var objItems = objItemsWrapper.find(".unite-repeater-item");

		var arrItemsValues = [];
		
		if(objItems.length == 0)
			return(arrItemsValues);
		
		jQuery.each(objItems, function(index, item){
			
			var objItem = jQuery(item);
			
			var objSettings = objItem.data("objsettings");
							
			var itemValues = objSettings.getSettingsValues();
			
			arrItemsValues.push(itemValues);
			
		});
		
		return(arrItemsValues);
	}
	
	
	/**
	 * add repeater item
	 */
	function repeaterAddItem(event, objWrapper, objItemValues, objItemInsertAfter){
		
		if(!objWrapper){
			var objButton = jQuery(this);
			var objWrapper = objButton.parents(".unite-settings-repeater");
		}
		
		var isNewItem = false;
		if(!objItemValues)
			isNewItem = true;
		
		var isDuplicated = false;
		if(objItemInsertAfter)
			isDuplicated = true;
		
		var objSettingsTemplate = objWrapper.find(".unite-repeater-template");
		var objItemsWrapper = objWrapper.find(".unite-repeater-items");
		var objEmptyText = objWrapper.find(".unite-repeater-emptytext")
		var objItems = objItemsWrapper.find(".unite-repeater-item");
		
		g_ucAdmin.validateDomElement(objItemsWrapper, "items wrapper");
		g_ucAdmin.validateDomElement(objSettingsTemplate, "settings template");
		
		//close other items, if not duplicated
		if(isDuplicated == false)		
			objItems.addClass("unite-item-closed");
		
		//set item title
		
		if(isNewItem == false){
			
			var itemTitle = objItemValues.title;
		}else{
			
			var itemTitle = objWrapper.data("itemtitle");
			if(!itemTitle)
				itemTitle = "Item";
			
			var numItems = objItemsWrapper.children().length;
			var currentNumItem = numItems+1;
			itemTitle += " "+currentNumItem;
			
		}
		
		var deleteText = objWrapper.data("deletetext");
		var duplicateText = objWrapper.data("duplicatext");
		
		//append item
		var htmlSettingsTemplate = objSettingsTemplate.html();
		
		var addClass = "";
		if(objItemInsertAfter)
			addClass = " unite-item-closed";
			
		var html = "<div class='unite-repeater-item"+addClass+"'>";
		html += "	<div class='unite-repeater-item-head'>";
		html += "		<a class='unite-repeater-trash unite-repeater-buttondelete' title='"+deleteText+"'><i class='fa fa-trash' aria-hidden='true'></i></a>";
		html += "		<a class='unite-repeater-duplicate unite-repeater-buttonduplicate' title='"+duplicateText+"'><i class='fa fa-clone' aria-hidden='true'></i></a>";
		
		html += "		<div class='unite-repeater-arrow'></div>";
		html += "	<span>" + itemTitle+"</span>";
		html += "	</div>";	
		html += "	<div class='unite-repeater-item-settings'>";
		html += htmlSettingsTemplate;
		html += "	</div>";
		html += "</div>";
		
		objEmptyText.hide();
		
		var objItem = jQuery(html);
		
		//change the id's
		
		var objItemSettingsWrapper = objItem.find(".unite_settings_wrapper");
		g_ucAdmin.validateDomElement(objItemSettingsWrapper, "item settigns wrapper");
		
		var objOptions = objItemSettingsWrapper.data("options");
		
		//replace setting IDs
		var idPrefix = objOptions.id_prefix;
		var newID = idPrefix+"item_"+g_ucAdmin.getRandomString(5)+"_";
		html = g_ucAdmin.replaceAll(html, idPrefix, newID);
				
		//put to the item again
		var objItem = jQuery(html);
		var objItemSettingsWrapper = objItem.find(".unite_settings_wrapper");
		
		//change wrapper ID
		var settingsWrapperID = "unite_settings_repeater_"+newID;
		objItemSettingsWrapper.attr("id", settingsWrapperID);
				
		g_ucAdmin.validateDomElement(objItemSettingsWrapper, "item settigns wrapper");
		
		if(objItemInsertAfter){
			objItem.insertAfter(objItemInsertAfter);
		}
		else
		objItemsWrapper.append(objItem);
		
		var objHeadTitle = objItem.find(".unite-repeater-item-head span");
		
		var objItemSettings = new UniteSettingsUC();
		objItemSettings.init(objItemSettingsWrapper);
		
		var objInputTitle = objItemSettings.getInputByName("title");
		
		objInputTitle.on("input",function(){
			
			var objInput = jQuery(this);
			var value = objInput.val();
			objHeadTitle.html(value);			
		});
		
		if(isNewItem == false){
			
			objItemSettings.setValues(objItemValues);
			
		}else{		//if new item
			
			//Update item title input if available
			if(objInputTitle)
				objInputTitle.val(itemTitle);
			
			//update generated ID
			var objInputGeneratedID = objItemSettings.getInputByName("generated_id");
			if(objInputGeneratedID){
				var generatedID = g_ucAdmin.getRandomString(7);
				objInputGeneratedID.val(generatedID);
			}
		
		}
		
		
		objItem.data("objsettings", objItemSettings);
		
		t.onSettingChange(null, objWrapper);
		
	}
	
	
	/**
	 * init repeater
	 */
	function initRepeater(objWrapper){
		
		var objButtonAdd = objWrapper.find(".unite-repeater-buttonadd");
		var objItemsWrapper = objWrapper.find(".unite-repeater-items");
		var objEmptyText = objWrapper.find(".unite-repeater-emptytext")
			
		//init button
		objButtonAdd.on("click",repeaterAddItem);
		
		//init container sortable
		objWrapper.sortable({
			items: ".unite-repeater-item",
			handle: ".unite-repeater-item-head",
			cursor: "move",
			axis: "y",
			update: function(event, ui){
				
				var objItem = ui.item;
				
				t.onSettingChange(null, objWrapper);
				
				objItem.data("just_moved",true);
								
			}
		});
		
		//head on click
		objWrapper.on("click", ".unite-repeater-item-head", function(){
			
			var objHead = jQuery(this);
			var objItem = objHead.parents(".unite-repeater-item");
			
			var justMoved = objItem.data("just_moved");
			if(justMoved === true){
				objItem.data("just_moved", false);
				return(true);
			}
			
			objItem.toggleClass("unite-item-closed");
			
		});
		
		//delete
		
		objWrapper.on("click", ".unite-repeater-buttondelete", function(){
			
			var objButton = jQuery(this);
			var objItem = objButton.parents(".unite-repeater-item");
			objItem.remove();
			
			var numItems = objItemsWrapper.children().length;
			if(numItems == 0)
				objEmptyText.show();
			
			t.onSettingChange(null, objWrapper);
			
		});
		
		objWrapper.on("click", ".unite-repeater-buttonduplicate", function(){
						
			var objButton = jQuery(this);
			var objItem = objButton.parents(".unite-repeater-item");
			
			objItem.data("just_moved",true);
			
			var itemSettings = objItem.data("objsettings");
			var settingsValues = itemSettings.getSettingsValues();
			
			repeaterAddItem(null, objWrapper, settingsValues, objItem);
						
		});
		
	}
	
	
	/**
	 * init repeaters globally
	 */
	function initRepeaters(){
		
		var objRepeaters = g_objWrapper.find(".unite-settings-repeater");
		if(objRepeaters.length == 0)
			return(false);
		
		g_temp.isRepeaterExists = true;
		
	}
	
	function _______DIMENTIONS_____(){}
	
	
	/**
	 * get dimentions setting value
	 */
	function dimentionsGetValues(objWrapper){
		
		var objAllInputs = objWrapper.find("input");
		var objUnits = objWrapper.find("select");
		
		var data = {};
		
		objAllInputs.each(function(index, input){
			var objInput = jQuery(input);
			var value = objInput.val();
			var pos = objInput.data("pos");
			
			data[pos] = value;
		});
		
		data["unit"] = objUnits.val();
		
		return(data);
	}
	
	
	
	function _______CONTROLS_____(){}
	
	/**
	 * get control action, according all the parents of the controlled children
	 * isSingle == true - don't do recursion
	 */
	function getControlAction(parent, control){
				
		var isEqual = iscValEQ(parent.value, control.value);
		
		var action = null;
		
		switch(control.type){
			case "enable":
			case "disable":
		
				if(control.type == "enable" && !isEqual || control.type == "disable" && isEqual)
					action = "disable";
				else
					action = "enable";
			break;
			case "show":
				if(isEqual) 
					action = "show";
				else 
					action = "hide";
			break;
			case "hide":
				if(isEqual) 
					action = "hide";
				else 
					action = "show";
			break;
		}	
				
		return(action);
	}
	
	
	/**
	 * get action of multiple parents
	 */
	function getControlActionMultiple(parent, control, arrParents){
		
		if(g_temp.cacheValues == null)
			g_temp.cacheValues = t.getSettingsValues(true);
				
		var isShow = null;
		var isEnable = null;
		
		var action = "";
		var mainAction = "";
		
		jQuery.each(arrParents, function(index, parentID){
			
			//get action
			if(parentID == parent.id){
				action = getControlAction(parent, control);
				mainAction = action;
			}
			else{
				var objControl = g_arrControls[parentID][control.idChild];
				
				var parentValue = g_temp.cacheValues[parentID];
				
				var objParent = {
						id: parentID, 
						value: parentValue
				};
				
				action = getControlAction(objParent, objControl);
				
			}
			
			switch(action){
				case "show":
					if(isShow === null)
						isShow = true;
				break;
				case "hide":
					isShow = false;
				break;
				case "enable":
					if(isEnable === null)
						isEnable = true;
				break;
				case "disable":
					isEnable = false;
				break;
			}
			
		});

		if(isEnable === null && isShow === null)
			return(null);
			
		var outputShow = (isShow === true)?"show":"hide";
		var outputEnable = (isEnable === true)?"enable":"disable";
		
		if(isEnable !== null && isShow !== null){
			if(mainAction == "show" || mainAction == "hide")
				return(outputShow);
			else
				return(outputEnable);
		}
		
		if(isShow !== null)
			return(outputShow);
		
		return(outputEnable);
	}
	
	
	/**
	 * on selects change - impiment the hide/show, enabled/disables functionality
	 */
	function onControlSettingChange(event, input){
		
		if(!input)
			input = this;
		
		var controlValue = input.value.toLowerCase();
		var controlID = input.name;
		
		
		if(!g_arrControls[controlID]) 
			return(false);
		
		g_temp.cacheValues = null;
		
		var arrChildControls = g_arrControls[controlID];
		
		var objParent = {
				id: controlID,
				value: controlValue
		};
		
		jQuery.each(arrChildControls, function(childName, objControl){
			
			var objChildInput = jQuery(g_IDPrefix + childName);
			
			var rowID = g_IDPrefix + childName + "_row";
			
			var objChildRow = jQuery(rowID);
			
			if(objChildRow.length == 0)
				return(true);
			
			var value = objControl.value;
			
			objControl.idChild = childName;
			
			//check multiple parents
			var arrParents = g_ucAdmin.getVal(g_arrChildrenControls, childName);
			if(arrParents)
				var action = getControlActionMultiple(objParent, objControl, arrParents);
			else
				var action = getControlAction(objParent, objControl);
			
			
			var inputTagName = "";
			if(objChildInput.length)
				inputTagName = objChildInput.get(0).tagName;
			
			var isChildRadio = (inputTagName == "SPAN" && objChildInput.length && objChildInput.hasClass("radio_wrapper"));
			
			switch(objControl.type){
				case "enable":
				case "disable":
					
					if(objChildInput.length > 0){
						
						//disable
						if(action == "disable"){
							
							objChildRow.addClass("setting-disabled");
							
							if(objChildInput.length)
								objChildInput.prop("disabled","disabled").css("color","");
							
							if(isChildRadio)
								objChildInput.children("input").prop("disabled","disabled").addClass("disabled");
							
							if(objChildInput.length && objChildInput.hasClass("unite-color-picker") && g_temp.colorPickerType == "spectrum")
								objChildInput.spectrum("disable");
							
						}//enable						
						else{	
							
							objChildRow.removeClass("setting-disabled");
							
							if(objChildInput.length)
								objChildInput.prop("disabled","");
							
							if(isChildRadio)
								objChildInput.children("input").prop("disabled","").removeClass("disabled");
							
							//color the input again
							if(objChildInput.length && objChildInput.hasClass("unite-color-picker")){
								
								if(g_colorPicker)
									g_colorPicker.linkTo(objChildInput);
								else if(g_temp.colorPickerType == "spectrum")
									objChildInput.spectrum("enable");
								
							}
		 				}
						
					}
				break;
				case "show":
				case "hide":
					
					if(action == "show") 
						objChildRow.removeClass("unite-setting-hidden");
					else 
						objChildRow.addClass("unite-setting-hidden");
					
				break;
			}
			
		});
	}
	
	
	/**
	 * apply controls if available
	 */
	function applyControls(){
				
		if(!g_objParent)
			return(false);
		
		g_objParent.find("select").trigger("change");
		g_objParent.find("input[type='radio']:checked").trigger("change");
		
	}
	
	function _______RESPNSIVE_ICONS_____(){}
	
	
	/**
	 * on icon click
	 */
	function onResponsiveIconClick(){
		
		var objIcon = jQuery(this);

		var iconsWrapper = objIcon.parents('.unite-settings-responsive-wrapper');
		
		var responsiveIdHolder = iconsWrapper.parents('li.unite-setting-row');
		
		var responsiveId = responsiveIdHolder.data("responsiveid");

		
		//open icons if closed
		
		if(iconsWrapper.hasClass('unite-settings-responsive-wrapper__open') == false){
			iconsWrapper.addClass('unite-settings-responsive-wrapper__open')
			return;
		}
		
		//change click, change the responsive type
		
		iconsWrapper.removeClass('unite-settings-responsive-wrapper__open');

		var selectedDevice = objIcon.data("device");
		
		
		//hide other rows
		
		var rowElements = jQuery(responsiveIdHolder).parents('ul.unite-list-settings').find('li[data-responsiveid="'+responsiveId+'"]');
		
		rowElements.addClass('uc-responsive-hidden');
		
		//show the device row
		
		var deviceRow = rowElements.filter('.unite-setting-row__'+selectedDevice);
		
		deviceRow.removeClass('uc-responsive-hidden');

		
	}
	
	
	/**
	 * init responsive icons
	 */
	function initResponsiveIcons(){
		
		var objIcons = g_objWrapper.find(".unite-settings-responsive-icon");
		
		objIcons.on("click", onResponsiveIconClick);
				
	}
	
	
	function _______EVENTS_____(){}
	
	
	/**
	 * update events (in case of ajax set)
	 */
	this.updateEvents = function(){
		
		initSettingsEvents();
		
		initTipsy("s");
		
		if(typeof g_objProvider.onSettingsUpdateEvents == "function")
			g_objProvider.onSettingsUpdateEvents(g_objParent);
		
	};

	
	/**
	 * set on change event, this function should run before init
	 */
	this.setEventOnChange = function(func){
		
		t.onEvent(t.events.CHANGE, func);
	};
	
	
	/**
	 * update input child placeholders if avilable
	 */
	function updateInputChildPlaceholders(objInput){
				
		if(!objInput)
			return(false);
		
		if(objInput.length == 0)
			return(false);
		
		var type = getInputType(objInput);
		if(type == "range")
			objInput = getRangeInput(objInput);
		
		var arrPlaceholderGroup = objInput.data("placeholder_group");
		
		if(!arrPlaceholderGroup)
			return(false);
		
		if(jQuery.isArray(arrPlaceholderGroup) == false)
			return(false);
				
		var valuePrev = "";
		
		jQuery.each(arrPlaceholderGroup, function(index, inputID){
			
			var objChildInput = jQuery("#" + inputID);
			if(objChildInput.length == 0)
				throw new Error("input not found with id: " + inputID);
							
			if(index > 0){
				
				objChildInput.attr("placeholder", valuePrev);
				objChildInput.trigger("placeholder_change");
			}
			
			var value = objChildInput.val();
			if(value !== "")
				valuePrev = value;
						
			
		});
		
		
	}
	
	
	/**
	 * run on setting change
	 */
	this.onSettingChange = function(event, objInput, isInstantChange){
		
	
		if(g_temp.enableTriggerChange == false)
			return(true);
		
		var dataOldValue = "unite_setting_oldvalue";
		if(isInstantChange == true)
			dataOldValue = "unite_setting_oldvalue_instant";
		
		if(!objInput)
			var objInput = jQuery(event.target);

				
		var type = getInputType(objInput);
		
		if(!type)
			return(true);
		
		
		if(type == "color")
			checkColorInputOnchange(objInput);
		
		var value = getSettingInputValue(objInput);
				
		switch(type){
			case "radio":
			case "select":
			case "fonts":
			case "items":
			case "map":
			break;
			default:
				//check by value
				var oldValue = objInput.data(dataOldValue);
				
				if(value === oldValue)
					return(true);
				
				objInput.data(dataOldValue, value);
			break;
		}
				
		//trigger event
		
		var eventToTrigger = t.events.CHANGE;
		if(isInstantChange == true)
			eventToTrigger = t.events.INSTANT_CHANGE;
		
		var name = getInputName(objInput);
		
		triggerEvent(eventToTrigger, {"name": name, "value": value});
	};

	
	
	/**
	 * trigger event
	 */
	function triggerEvent(eventName, params){
		if(!params)
			var params = null;
		
		if(g_objParent)
			g_objParent.trigger(eventName, params);
	}
	
	
	/**
	 * on event name
	 */
	this.onEvent = function(eventName, func){
		validateInited();				
		
		g_objParent.on(eventName,func);
	};

	
	/**
	 * combine controls to one object, and init control events.
	 */
	function initControls(){
		
		if(!g_objWrapper)
			return(false);
		
		var objControls = g_objWrapper.data("controls");
		
		if(!objControls)
			return(false);
		
		g_objWrapper.removeAttr("data-controls");
		
		g_arrControls = objControls.parents;
		g_arrChildrenControls = objControls.children;
		
		
		//init events
		g_objParent.find("select, input[type='radio'], input.unite-setting-addonpicker").change(onControlSettingChange);
		
	}
	
	
	
	/**
	 * init image chooser
	 */
	this.initImageChooser = function(objImageSettings){
				
		if(objImageSettings.length == 0)
			return(false);
		
		objImageSettings.find(".unite-setting-image-preview").on("click",onChooseImageClick);
		objImageSettings.find(".unite-button-choose").on("click",onChooseImageClick);
		objImageSettings.find(".unite-button-clear").on("click",onClearImageClick);
		
		var objInput = objImageSettings.find("input");
		
		objInput.change(setImagePreview);
	};
	
	
	/**
	 * init mp3 chooser
	 */
	this.initMp3Chooser = function(objMp3Setting){

		if(objMp3Setting.length == 0)
			return(false);
		
		
		objMp3Setting.find(".unite-button-choose").on("click",onChooseMp3Click);
	};
	
	
	
	/**
	 * trigger on keyup
	 */
	this.triggerKeyupEvent = function(objInput, event, funcChange){
		
		if(!funcChange)
			funcChange = t.onSettingChange;
		
		if(g_temp.enableTriggerChange == false)
			return(true);
		
		//run instant
		funcChange(event, objInput, true);
		
		g_ucAdmin.runWithTrashold(funcChange, event, objInput);
		
	};
	
	
	/**
	 * init single input event
	 */
	function initInputEvents(objInput, funcChange){
		
		if(!funcChange)
			funcChange = t.onSettingChange;
		
		var type = getInputType(objInput);
		var basicType = getInputBasicType(objInput);

				
		//init by type
		switch(type){
			case "color":
				initColorPickerInputEvents(objInput, funcChange);
			break;
			case "icon":
				initIconPicker(objInput);
			break;
			case "range":
				initRangeInput(objInput, funcChange);
			break;
			case "map":
				initMapPicker(objInput);
			break;
			case "addon":
				initAddonPicker(objInput);
			break;
			case "repeater":
				initRepeater(objInput);
			break;
			case "post":
				initPostPicker(objInput);
			break;
			case "multiselect":
				objInput.on("input", funcChange);
			break;
			case "select2":

				initSelect2(objInput)
								
			break;
			case "post_select":
				
				trace("init post select");
				
			break;
			case "gallery":
				
				initGallery(objInput);
				
			break;
			default:
				//custom setting
				var objCustomType = getCustomSettingType(type);
				if(objCustomType){
					if(objCustomType.funcInit)
						objCustomType.funcInit(objInput, t);
				}
				else	//provider setting
					g_ucAdmin.initProviderSettingEvents(type, objInput);
				
			break;
		}
		
		//init by base type
		switch(basicType){
			case "checkbox":
			case "radio":
				objInput.on("click",funcChange);
			break;
			case "div":		//special types
				
			break;
			default:
								
				objInput.change(funcChange);
				
				objInput.on("keyup", function(event){
					t.triggerKeyupEvent(objInput, event, funcChange);
				});
			break;
		}
		
	}
	
	
	/**
	 * init settings events
	 */
	function initSettingsEvents(){
				
		var objInputs = getObjInputs();
				
		jQuery.each(objInputs, function(index, input){
			var objInput = jQuery(input);
						
			initInputEvents(objInput);
		});
		
		
		//init image input events
		var objImageSettings = g_objParent.find(".unite-setting-image");
		t.initImageChooser(objImageSettings);
		
		//init mp3 input events
		var objMp3Settings = g_objParent.find(".unite-setting-mp3");
		t.initMp3Chooser(objMp3Settings);
		
		initControls();
	}
	
	
	/**
	 * init global events - not repeating
	 */
	function initGlobalEvents(){
		
		g_ucAdmin.onEvent("update_assets_path", onUpdateAssetsPath);
		
	}
	
	
	/**
	 * init options
	 */
	function initOptions(){
		
		if(!g_objWrapper)
			return(false);
		
		var objOptions = g_objWrapper.data("options");
		
		if(typeof objOptions != "object")
			throw new Error("The options should be an object");
		
		g_objWrapper.removeAttr("data-options");
		
		var arrOptions = ["show_saps","saps_type","id_prefix"];
		
		jQuery.each(arrOptions, function(index, optionKey){
			g_options[optionKey] = g_ucAdmin.getVal(objOptions, optionKey, g_options[optionKey]);
			
			//delete option key
			objOptions[optionKey] = true;
			delete objOptions[optionKey];
			
		});
		
		//merge with other options
		jQuery.extend(g_options, objOptions);
				
		if(g_options["id_prefix"])
			g_IDPrefix = "#"+g_options["id_prefix"];
		
	}
	
	/**
	 * update placeholders
	 */
	this.updatePlaceholders = function(objPlaceholders){
		
		if(!g_objParent)
			return(false);
		
		jQuery.each(objPlaceholders, function(key, value){
			
			var objInput = t.getInputByName(key);
			if(!objInput)
				return(true);
			
			objInput.attr("placeholder", value);
			objInput.trigger("placeholder_change");
		});
		
	};
	
	/**
	 * focus first input
	 */
	this.focusFirstInput = function(){
		
		var objInputs = getObjInputs();
		
		jQuery.each(objInputs, function(index, input){
			var objInput = jQuery(input);
			var type = getInputType(objInput);
			switch(type){
				case "textarea":
				case "text":
					objInput.focus();
					return(false);
				break;
			}
		});
	};
	
	
	
	/**
	 * destroy settings object
	 */
	this.destroy = function(){
				
		g_ucAdmin.offEvent("update_assets_path");

		var objInputs = g_objParent.find("input,textarea,select").not("input[type='radio']");
		
		objInputs.off("change");
		
		var objInputsClick = g_objParent.find("input[type='radio']");
		objInputsClick.off("click");
				
		var objImageSettings = g_objParent.find(".unite-setting-image");
		
		//destroy image events:
		if(objImageSettings.length){
			
			objImageSettings.find(".unite-button-choose").off("click");
			objImageSettings.find(".unite-button-clear").off("click");
			objImageSettings.find("input").off("change");
		}
		
		//destroy control events
		g_objParent.find("select, input").off("change");
		
		//destroy loaded events
		g_objParent.off(t.events.CHANGE);
		
		//destroy tabs events
		if(g_objSapTabs)
			g_objSapTabs.children("a").off("click");
		
		//destroy accordion events
		if(g_objWrapper){
			var objAccordionItems = g_objWrapper.find(".unite-postbox .unite-postbox-title");
			if(objAccordionItems.length)
				objAccordionItems.off("click");
		}
		
		g_objProvider.destroyEditors(t);
		
		//null parent object so it won't pass the validation
		if(g_objParent.length){
			g_objParent.html("");
		}
		
		//destroy items manager
		if(g_temp.objItemsManager){
			g_temp.objItemsManager.destroy();
			g_temp.objItemsManager = null;
			g_objParent.find(".uc-setting-items-panel-button").off("click");
		}
		
		if(g_temp.objFontsPanel)
			t.destroyFontsPanel();
		
		if(g_temp.isRepeaterExists == true)
			t.destroyRepeaters();
		
		
		//destroy icon pickers
		g_objParent.find(".unite-settings-iconpicker input").off("blur");
		g_objParent.find(".unite-iconpicker-button").off("blur");
		
		//destroy custom setting types
		var objCustomTypes = getCustomSettingType();
		if(objCustomTypes){
			jQuery.each(objCustomTypes, function(index, objType){
				
				if(objType.funcDestroy && g_objParent && g_objParent.length)
					objType.funcDestroy(g_objParent);
				
			});
		}
		
		//destroy addon picker
		g_objParent.find(".unite-addonpicker-button").off("click");
		
		g_objParent.find(".unite-button-primary, .unite-button-secondary").off("click");
		
		g_objParent = null;
	};
	
	
	/**
	 * set id prefix
	 */
	this.setIDPrefix = function(idPrefix){
		g_IDPrefix = "#"+idPrefix;
	};
	
	
	/**
	 * get id prefix
	 */
	this.getIDPrefix = function(){
		
		return(g_IDPrefix);
	};
	
	/**
	 * get wrapper
	 */
	this.getObjWrapper = function(){
		
		return(g_objParent);
	};
	
	/**
	 * return if the settings are in sidebar
	 */
	this.isSidebar = function(){
		return(g_temp.isSidebar);
	};
	
	
	/**
	 * run custom command
	 */
	this.runCommand = function(command){
				
		switch(command){
			case "open_items_panel":
				var objButton = g_objParent.find(".uc-setting-items-panel-button");
				if(objButton.length)
					objButton.trigger("click");
			break;
		}
		
	};
	
	
	
	
	/**
	 * add custom type
	 * fields: type, funcInit, funcSetValue, funcGetValue, funcClearValue
	 */
	this.addCustomSettingType = function(type, objType){
		
		g_ucAdmin.validateObjProperty(objType, ["funcInit",
                               "funcSetValue",
                               "funcGetValue",
                               "funcClearValue",
                               "funcDestroy",
		 ],"custom setting type object");
					
		var objCustomSettings = getCustomSettingType();
		
		var existing = g_ucAdmin.getVal(objCustomSettings, type);
		if(existing)
			throw new Error("The custom settings type: "+type+" alrady exists");
		
		objCustomSettings[type] = objType;
		
		g_ucAdmin.storeGlobalData(g_temp.customSettingsKey, objCustomSettings);
	};
	
	
	
	/**
	 * init the settings function, set the tootips on sidebars.
	 */
	this.init = function(objParent){
				
		if(!g_ucAdmin)
			g_ucAdmin = new UniteAdminUC();
		
		g_objParent = objParent;
		
		if(g_objParent.length > 1){
			
			trace(g_objParent);
			throw new Error("Settings object can't be inited with too many parents");
		}
		
		//init settings wrapper
		if(g_objParent.hasClass("unite_settings_wrapper") == true)
			g_objWrapper = g_objParent;
		else{
			g_objWrapper = g_objParent.children(".unite_settings_wrapper");
		}
		
		if(g_objWrapper.length == 0)
			g_objWrapper = g_objParent.parents(".unite_settings_wrapper");
		
		if(g_objWrapper.length == 0)
			g_objWrapper = null;
		
		//set if sidebar
		if(g_objWrapper && g_objWrapper.hasClass("unite-settings-sidebar"))
			g_temp.isSidebar = true;
		
		if(g_objWrapper)
			g_temp.settingsID = g_objWrapper.prop("id");
		
		g_temp.enableTriggerChange = false;
		
		validateInited();
		
		initOptions();
		
		initColorPicker();	//put the color picker automatically
		
		initAnimationsSelector();
		
		initItemsPanel();
		
		initRepeaters();
		
		initResponsiveIcons();
		
		t.initFontsPanel();
		
		initGlobalEvents();
		
		t.updateEvents();
		
		initSaps();
		
		t.clearSettingsInit();
		
		g_objProvider.initEditors(t);
		
		g_temp.isInited = true;

		g_temp.enableTriggerChange = true;
		
	};


} // UniteSettings class end

