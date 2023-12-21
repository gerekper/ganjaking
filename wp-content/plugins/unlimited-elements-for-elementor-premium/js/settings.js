
"use strict";

function UniteSettingsUC(){

	var g_arrControls = {};
	var g_arrChildrenControls = {};

	var g_IDPrefix = "#unite_setting_";
	var g_colorPicker, g_colorPickerWrapper, g_iconsHash={};
	var g_objParent = null, g_objWrapper = null, g_objSapTabs = null;
	var g_objProvider = new UniteProviderAdminUC();

	var G_DEBUG_INPUTS = false;

	var g_vars = {
		NOT_UPDATE_OPTION: "unite_settings_no_update_value",
		keyupTrashold: 500,
		animationDuration: 300,
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
		isRepeaterExists: false,
		disableExcludeSelector:false
	};

	this.events = {
			CHANGE: "settings_change",
			INSTANT_CHANGE: "settings_instant_change",
			AFTER_INIT: "after_init",
			OPEN_CHILD_PANEL: "open_child_panel",
			SELECTORS_CHANGE: "selectors_change"
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

		var isEqual = false;

		if(typeof value != "string"){

			isEqual = jQuery.inArray( controlValue, value) != -1;

		}else{

			if(typeof controlValue == "object")
				isEqual = jQuery.inArray(value, controlValue) != -1;
			else
				isEqual = (value.toLowerCase() == controlValue);
		}


		return(isEqual);
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

		if(g_temp.disableExcludeSelector !== true)
			selectorNot += ", .unite-settings-exclude *";

		if(controlsOnly === true){
			selectors = "input[type='radio'], select";
		}else{

			//items

			if(g_temp.objItemsManager){
				selectors += ", .uc-setting-items-panel";
				selectorNot += ", .uc-setting-items-panel select, .uc-setting-items-panel input, .uc-setting-items-panel textarea";
			}

			if(g_temp.isRepeaterExists == true)
				selectorNot += ", .unite-settings-repeater *";

		}

		var objInputs = g_objParent.find(selectors).not(selectorNot);

		if(G_DEBUG_INPUTS == true){

			trace("debug inputs!");
			trace(selectorNot);
			trace(objInputs);

		}


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
				else if(objInput.hasClass("unite-setting-link"))
					type="link";

			break;
			case "textarea":
				if(objInput.hasClass("mce_editable") || objInput.hasClass("wp-editor-area"))
					type = "editor_tinymce";
			break;
			case "span":
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
				value = objInput.prop("checked");
			break;
			case "radio":
				if(objInput.prop("checked") === false)
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
			case "gallery":
				value = getGalleryValues(objInput);
			break;
			case "icon":
				value = getIconInputData(objInput);
			break;
			case "link":
				value = getLinkInputValue(objInput);
			break;
			case "switcher":
				value = getSwitcherValue(objInput);
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
					value = objInput.prop("checked");
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
			case "checkbox":
			case "radio":
				defaultValue = objInput.data(checkboxDataName);
				defaultValue = g_ucAdmin.strToBool(defaultValue);
			break;
			default:
				if(!name)
					return(false);

				defaultValue = objInput.data(dataname);

				if(typeof defaultValue == "object")
					defaultValue = JSON.stringify(defaultValue);

				if(type === "select"){
					if(defaultValue === true)
						defaultValue = "true";
					if(defaultValue === false)
						defaultValue = "false";
				}
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
			dataname = "default";

		if(!checkboxDataName)
			checkboxDataName = "defaultchecked";

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
			case "radio":
				defaultValue = objInput.data(checkboxDataName);
				defaultValue = g_ucAdmin.strToBool(defaultValue);

				objInput.prop("checked", defaultValue);
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
			case "link":
				defaultValue = objInput.data(dataname);

				clearLinkInputValue(objInput, defaultValue);
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
			case "gallery":

				clearGallery(objInput);

			break;
			case "typography":

				clearTypography(objInput);

			break;
			case "switcher":

				clearSwitcher(objInput, dataname);

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
			break;
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

				objInput.prop("checked", value === true);
			break;
			case "radio":
				var radioValue = objInput.val();

				if(radioValue === "true" || radioValue === "false"){
					radioValue = g_ucAdmin.strToBool(radioValue);
					value = g_ucAdmin.strToBool(value);
				}

				objInput.prop("checked", radioValue === value);
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
			case "link":
				setLinkInputValue(objInput, value);
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
			case "gallery":
				setGalleryValues(objInput, value);
			break;
			case "switcher":

				objInput.data("value",value);
				updateSwitcherState(objInput);

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

		jQuery.each(objInputs, function(index, input){
			var objInput = jQuery(input);
			var name = getInputName(objInput);

			if(!name)
				return;

			var type = getInputType(objInput);

			if(type !== "radio")
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

	/**
	 * append plus icon
	 */
	function appendPlusIcon(objInput){

		//first check if all options are selected
		var objInputWrapper = objInput.parents('.unite-setting-input');
		var objSelect = objInputWrapper.find('select');

		if(!objSelect.length)
		return(false);

		var objOptions = objInputWrapper.find('option');

		if(!objOptions.length)
		return(false);

		var objSelectedOptions = objInputWrapper.find('li.select2-selection__choice')
		var numOptions = objOptions.length;
		var numSelectedOptions = objSelectedOptions.length;

		var isAlloptionsSelected = numOptions == numSelectedOptions;

		if(isAlloptionsSelected == true)
		return(false);

		var objSelectedOptionsContainer = objInputWrapper.find('ul.select2-selection__rendered');

		var objPlusButton = objInputWrapper.find('.select2-selection__uc-plus-button');

		//check if button already exist
		if(objPlusButton.length > 0)
		return(false);

		var plusButtonHtml = '<li class="select2-selection__choice select2-selection__uc-plus-button">+</li>';

		//find inline cursor and insert plus button before
		var objCursorInput = objInputWrapper.find('.select2-search--inline');

		objSelectedOptionsContainer.append(plusButtonHtml);

		objPlusButton = objInputWrapper.find('.select2-selection__uc-plus-button');

		objPlusButton.insertBefore(objCursorInput)

	}


	function initSelect2(objInput){

		setTimeout(function(){

			appendPlusIcon(objInput);

		},400)

		objInput.select2({minimumInputLength: 1}).on('change', function(e){

			t.onSettingChange(null,objInput,true);

			appendPlusIcon(objInput);
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
	 * check if thumbnails container is empty and add emty element if needed
	 */
	function appendEmptyThumbnailElement(objImageContainer, singleImageClass, noImagesClass){

		var emptyThumbnailsContainerItemHtml = '<div class="'+singleImageClass+' '+noImagesClass+'"><i class="fa fa-plus"></i></div>';
		var objEmptyThumbnail = objImageContainer.find('.'+noImagesClass);
		var objImages = objImageContainer.children();

		if(objImages.length == 0 && objEmptyThumbnail.length == 0){

			objImageContainer.append(emptyThumbnailsContainerItemHtml);

			var objImageChooser = objImageContainer.next();

			objImageChooser.removeClass('uc-has-items');

		}

	}

	/**
	 * count selected images
	 */
	function countSelectedImages(objInput, objClearAllButton, singleImageClass){

		var objImages = objInput.find('.'+singleImageClass).not('.unite-setting-gallery-thumbnail--empty');
		var objCounter = objInput.find('.unite-setting-gallery-status-title');
		var hiddenClass = 'uc-hidden';
		var imagesNum = objImages.length;

		if(imagesNum == 0 || !imagesNum){

			objCounter.text(0+' images selected');

			//remove invisble class from 'x' button
			objClearAllButton.addClass(hiddenClass);

		}else{

			objCounter.text(imagesNum+' images selected');

			//add invisble class from 'x' button
			objClearAllButton.removeClass(hiddenClass);

		}

	}

	/**
	 * clear the gallery
	 */
	function emptyGallery(objInput, objImageContainer, objClearAllButton, singleImageClass, noImagesClass, noConfirm){

		//check if gallery has items first
		var objImages = objImageContainer.find('.'+singleImageClass).not('.'+noImagesClass);

		//if no items found then do not run function
		if(!objImages.length)
		return(false);

		if(noConfirm == null && confirm("Are you sure you want to delete all images from this gallery?") == false)
			return(false);

		objImages.remove();

		appendEmptyThumbnailElement(objImageContainer, singleImageClass, noImagesClass);

		countSelectedImages(objInput, objClearAllButton, singleImageClass);

	}

	/**
	 * check if emty element exist and remove it
	 */
	function removeEmptyThumbnail(objInput, noImagesClass){

		var objEmptyThumbnail = objInput.find('.'+noImagesClass);

		if(!objEmptyThumbnail.length)
		return(false);

		objEmptyThumbnail.remove();

	}

	/**
	 * single image remove
	 */
	function removeCurrentImage(objRemover, objInput, objImageContainer, objClearAllButton, singleImageClass, noImagesClass){

		//find object that needs to be removed by remove icon that was clicked
		var objImage = objRemover.parents('.'+singleImageClass);

		objImage.remove();

		//update counter
		countSelectedImages(objInput, objClearAllButton, singleImageClass);

		//append empty thumbnail if needed
		appendEmptyThumbnailElement(objImageContainer, singleImageClass, noImagesClass);

	}



	/**
	 * reorder items in container
	 */
	function makeSortable($parent) {

		$parent.sortable({
			cursor: 'move',
			tolerance: 'pointer', // Set the tolerance option
			forcePlaceholderSize: true // Set the forcePlaceholderSize option

		});
	}

	/**
	 * on image chooser click
	 */
	function appendNewImages(newImagesObject, singleImageClass, singleImageClearIconClass, objImageContainer){

		var numSelected = newImagesObject.length;

		for(var i=0;i<numSelected;i++){

			var imageSrc = newImagesObject[i].url;
			var dataId = newImagesObject[i].id;

			var imageHtml = '<div class="'+singleImageClass+'" style="background-image: url('+imageSrc+')" data-id="'+dataId+'" data-src="'+imageSrc+'"><span class="'+singleImageClearIconClass+'" title="Remove Current Image"><i class="fa fa-trash"></i></span></div>';

			objImageContainer.append(imageHtml);

		}

	}

	/**
	 * on image chooser click
	 */
	function galleryChooseImage(objInput, objChooser){

		var hasItemsClass = 'uc-has-items';
		var inputEditClass = 'unite-setting-gallery-edit';

		var isItemsAdded = objChooser.hasClass(inputEditClass) && objChooser.hasClass(hasItemsClass);

		//check if images are alreade added, if so then add new ones only on plus icon click
		if(isItemsAdded == true)
		return(false);

		g_ucAdmin.openAddImageDialog("Choose Images",function(urlImage){

			//add new images
			setGalleryValues(objInput, urlImage);

		},true);

		objInput.trigger("change")

	}

	/**
	 * init the gallery
	 */
	function initGallery(objInput){

		var objChooser = objInput.find('.unite-setting-gallery-edit:not(.uc-dragging)');
		var objImageContainer = objInput.find('.unite-setting-gallery-thumbnails');
		var objAddImage = objInput.find('.unite-setting-gallery-edit-icon');

		var singleImageClass = 'unite-setting-gallery-thumbnail';
		var noImagesClass = 'unite-setting-gallery-thumbnail--empty';
		var singleImageClearIconClass = 'unite-setting-gallery-thumbnail-clear-icon';
		var singleImageClearIconSelector = '.'+singleImageClearIconClass;

		//check if thumbnails container is empty
		appendEmptyThumbnailElement(objImageContainer, singleImageClass, noImagesClass);

		var objClearAllButton = objInput.find('.unite-setting-gallery-status-clear-icon');

		//set counter
		countSelectedImages(objInput, objClearAllButton, singleImageClass);

		//init events

		objInput.on("change",function(){

			t.onSettingChange(null,objInput);

		});


		//select first image
		objChooser.on('click', function(){

			var objChooser = jQuery(this);
			galleryChooseImage(objInput, objChooser);

		});

		objAddImage.on('click', function(){
			var objChooser = jQuery(this);
			galleryChooseImage(objInput, objChooser);

		});


		//remove current image, use delegate method to click on dyamicly added button
		objInput.delegate(singleImageClearIconSelector, 'click', function(){

			var objRemover = jQuery(this);

			removeCurrentImage(objRemover, objInput, objImageContainer, objClearAllButton, singleImageClass, noImagesClass);

		});

		//clear all images on clear button click
		objClearAllButton.on('click', function(){

			emptyGallery(objInput, objImageContainer, objClearAllButton, singleImageClass, noImagesClass);

			objInput.trigger("change");
		});

	}


	/**
	 * get gallery values
	 */
	function getGalleryValues(objInput){

		var imageArray = [];
		var objImages = objInput.find('.unite-setting-gallery-thumbnail').not('.unite-setting-gallery-thumbnail--empty');

		if(!objImages.length)
		return(imageArray);


		objImages.each(function(){

			var objImage = jQuery(this);
			var imageUrl = objImage.data('src');
			var imageId = objImage.data('id');

			var galleryObjItem = {
				id: imageId,
				url: imageUrl
			}
			imageArray.push(galleryObjItem);

		});

		return(imageArray);
	}


	/**
	 * set gallery values
	 */
	function setGalleryValues(objInput, value){


		var singleImageClass = 'unite-setting-gallery-thumbnail';
		var singleImageClearIconClass = 'unite-setting-gallery-thumbnail-clear-icon';
		var noImagesClass = 'unite-setting-gallery-thumbnail--empty';
		var objImageContainer = objInput.find('.unite-setting-gallery-thumbnails');
		var objChooser = objInput.find('.unite-setting-gallery-edit:not(.uc-dragging)');

		//add new images
		appendNewImages(value, singleImageClass, singleImageClearIconClass, objImageContainer);

		//remove empty thumbnail
		removeEmptyThumbnail(objInput, noImagesClass);

		var objClearAllButton = objInput.find('.unite-setting-gallery-status-clear-icon');

		//update counter
		countSelectedImages(objInput, objClearAllButton, singleImageClass);

		makeSortable(objImageContainer);

		var hasItemsClass = 'uc-has-items';
		var inputEditClass = 'unite-setting-gallery-edit';

		if(objChooser.hasClass(inputEditClass) == true)
		objChooser.addClass(hasItemsClass);

		objInput.trigger("change");

	}

	/**
	 * clear the gallery
	 */
	function clearGallery(objInput){

		var objImageContainer = objInput.find('.unite-setting-gallery-thumbnails');
		var objClearAllButton = objInput.find('.unite-setting-gallery-status-clear-icon');
		var singleImageClass = 'unite-setting-gallery-thumbnail';
		var noImagesClass = 'unite-setting-gallery-thumbnail--empty';

		emptyGallery(objInput, objImageContainer, objClearAllButton, singleImageClass, noImagesClass, true)

		trace("clear gallery");

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

		if (!g_objWrapper)
			return (false);

		g_objWrapper.find(".unite-settings-accordion-saps-tabs .unite-settings-tab").on("click", function () {
			var objTab = jQuery(this);
			var objRoot = objTab.closest(".unite-settings-accordion-saps-tabs");
			var id = objTab.data("id");

			objRoot.find(".unite-settings-tab").removeClass("unite-active");
			objTab.addClass("unite-active");

			var objContents = g_objWrapper.find(".unite-postbox").hide().filter(".uctab-" + id).show();

			if (objContents.filter(".unite-active").length === 0)
				objContents.filter(":first").find(".unite-postbox-title").trigger("click");
		});

		g_objWrapper.find(".unite-postbox .unite-postbox-title").on("click", function () {
			jQuery(this).closest(".unite-postbox:not(.unite-no-accordion)")
				.toggleClass("unite-active")
				.find(".unite-postbox-inside")
				.stop()
				.slideToggle(g_vars.animationDuration);
		});

		g_objWrapper.find(".unite-settings-accordion-saps-tabs .unite-settings-tab.unite-active").trigger("click");

	}


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

				var objPickerWrapper = objPickerInput.parents(".unite-settings-iconpicker");

				//check svg icon, if not exist - set text inside svg button
				removeSvgPreview(objPickerWrapper, objPickerInput);

				//enable input
				objPickerInput.removeAttr('disabled');

				//add clear input button
				appendClearInputButton(objPickerWrapper, objPickerInput);

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
	 * show error svg: wrong tupe
	 */
	function showWrongSvgTypeError(objPickerWrapper){

		var errorHtml = '<div class="unite-iconpicker-button-error">Wrong Image Type. Image Needs to Be SVG type.</div>';

		objPickerWrapper.prepend(errorHtml);

		var objError = objPickerWrapper.find('.unite-iconpicker-button-error');

		setTimeout(function(){
			objError.remove();
		},4000);

	}

	/**
	 * remove clear input button
	 */
	function removeClearInputButton(objPickerWrapper){

		var objClearInputButton = objPickerWrapper.find('.unite-iconpicker-button-clear');

		if(!objClearInputButton.length)
		return(false);

		objClearInputButton.remove();

	}

	/**
	 * append clear button
 */
	function appendClearInputButton(objPickerWrapper, objInput){

		var clearInputButtonClassName = 'unite-iconpicker-button-clear';
		var hoverClass = 'uc-hover';
		var clearButtonHtml = ' <span class="'+clearInputButtonClassName+'">Clear</span>';
		var objClearInputButton = objPickerWrapper.find('.'+clearInputButtonClassName);

		if(objClearInputButton.length > 0)
		return(false);

		objPickerWrapper.append(clearButtonHtml);

		//reinit button
		objClearInputButton = objPickerWrapper.find('.'+clearInputButtonClassName);

		objClearInputButton.on('click', function(){

			onClearInputButtonClick(objPickerWrapper, objInput);

			objInput.removeClass(hoverClass);

		});

		objClearInputButton.on('mouseenter', function(){

			objInput.addClass(hoverClass);

		});

		objClearInputButton.on('mouseleave', function(){

			objInput.removeClass(hoverClass);

		});

	}

	/**
	 * remove svg preview
	 */
	function removeSvgPreview(objPickerWrapper, objInput){

		var objSvgButton = objPickerWrapper.find('.unite-iconpicker-button-svg');

		//check if svg exist
		var dataName = objSvgButton.data('svg-name');
		var inputVal = objInput.val();

		if(dataName == inputVal){

			var urlImage = objSvgButton.data('svg-src');
			var urlToSvg = g_ucAdmin.urlToFull(urlImage);

			setSvgPreview(urlToSvg, objSvgButton, objPickerWrapper);

			//hide icon chooser
			hideIconChooseButton(objPickerWrapper);

			//enable input
			objInput.removeAttr('disabled');

			return(false);

		}

		//remove attributes
		objSvgButton.removeAttr('data-svg-src');
		objSvgButton.removeAttr('data-svg-id');
		objSvgButton.removeAttr('data-svg-name');

		objSvgButton.removeClass('svg-selected');

		//set text inside svg button instead of preview
		objSvgButton.html('SVG');

		//remove clear input button
		removeClearInputButton(objPickerWrapper);

		//disable input
		objInput.attr('disabled', 'disabled');

		//show icon chooser
		showIconChooseButton(objPickerWrapper);

	}

	/**
	 * set preview of selected svg icon
	 */
	function setSvgPreview(selectedSvgUrl, objSvgButton, objPickerWrapper){

		var imageHtml = '<img src="'+selectedSvgUrl+'" alt="selected svg" >';

		objSvgButton.addClass('svg-selected');

		objSvgButton.html(imageHtml);

		//remove icon preview
		removeIconPreview(objPickerWrapper);

	}


	/**
	 * get icon value
	 */
	function getIconInputData(objInput){

		var inputText = objInput.val();
		var isTypeSvg = inputText.indexOf('.svg') > -1;

		if(isTypeSvg == true){

			var urlToImage = objInput.data('svg-src');
			var idOfImage = objInput.data('svg-id');

			var svgArray = setSvgArrayObject(urlToImage, idOfImage);

			return(svgArray);

		}

		if(isTypeSvg == false){

			var selectedIconName = objInput.val();

			return(selectedIconName);

		}


	}



	/**
	 * set svg array object
	 */
	function setSvgArrayObject(urlImage, imageId){

		var svgArray = [];
		var urlToSvgFile = g_ucAdmin.urlToRelative(urlImage);


		var svgArrayObject = {
			id: imageId,
			library: 'svg',
			url: urlToSvgFile
		};

		svgArray.push(svgArrayObject);

		return(svgArray);
	}

	/*
	* get svg data
	*/
	function setIconSvgData(urlImage, imageId){

		var svgArray = setSvgArrayObject(urlImage, imageId);

		return(svgArray)

	}

	//hide icon chooser
	function hideIconChooseButton(objPickerWrapper){

		var objChooseIconButton = objPickerWrapper.find('.unite-iconpicker-button');

		if(!objChooseIconButton.length)
		return(false);

		objChooseIconButton.hide();

	}

	//show icon chooser
	function showIconChooseButton(objPickerWrapper){

		var objChooseIconButton = objPickerWrapper.find('.unite-iconpicker-button');

		if(!objChooseIconButton.length)
		return(false);

		objChooseIconButton.show();

	}

	/**
	 * init svg picker
	 */
	function initSvgPicker(objInput){

		var objPickerWrapper = objInput.parents(".unite-settings-iconpicker");
		var objSvgButton = objPickerWrapper.find('.unite-iconpicker-button-svg');

		if(!objSvgButton.length)
		return(false);

		objSvgButton.on('click', function(){

			g_ucAdmin.openAddImageDialog("Choose Images",function(urlImage, imageId){

				var fileName = urlImage.split('/').pop();
				var fileExtension = fileName.split('.').pop();

				if(fileExtension != 'svg'){

					//show error
					showWrongSvgTypeError(objPickerWrapper);

					trace('Image needs to be svg type.')
					return(false);

				}

				//set data-name attr
				objInput.data('svg-name', fileName);

				//set data-src / data-id attr
				var urlToSvg = g_ucAdmin.urlToRelative(urlImage);

				objInput.data('svg-src', urlToSvg);
				objInput.data('svg-id', imageId);

				//put file name inside input
				objInput.val(fileName);

				//hide icon chooser
				hideIconChooseButton(objPickerWrapper);

				//get svg data
				setIconSvgData(urlImage, imageId);

				//disable input
				objInput.attr('disabled', 'disabled');

				//set previe of selected icon inside button
				setSvgPreview(urlImage, objSvgButton, objPickerWrapper);

				//append clear input button
				appendClearInputButton(objPickerWrapper, objInput);

				objInput.trigger("change");


			},false);

		});

	}

	/**
	 * remove icon preview
	 */
	function removeIconPreview(objPickerWrapper){

		var objIconChooseButton = objPickerWrapper.find('.unite-iconpicker-button');

		objIconChooseButton.html('choose');

	}

	/**
	 * clear input
	 */
	function onClearInputButtonClick(objPickerWrapper, objInput){

		//clear input
		objInput.val('');

		//remove svg preview
		removeSvgPreview(objPickerWrapper, objInput);

		//remove icon preview
		removeIconPreview(objPickerWrapper);

		//enable input
		objInput.removeAttr('disabled');

		objInput.trigger("change");
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


	function ______POST_PICKER____(){}


	/**
	 * init post picker
	 */
	function initPostPicker(objWrapper, data, selectedValue){

		//fix select focus inside jquery ui dialogs
		g_ucAdmin.fixModalDialogSelect2();

    	objWrapper.removeData("post_picker_value");

		var objSelect = objWrapper.find(".unite-setting-post-picker");

		var postID = objSelect.data("postid");
		var postTitle = objSelect.data("posttitle");
		var placeholder = objSelect.data("placeholder");

		if(!data){
			var data = [];

			if(postID && postTitle){

				data.push({
					id:postID,
					text: postTitle
				});
			}
		}

		var urlAjax = g_ucAdmin.getUrlAjax("get_posts_list_forselect");

		objSelect.select2({
			minimumInputLength:1,
			data:data,
			placeholder: placeholder,
			allowClear: true,
			ajax:{
				url:urlAjax,
				dataType:"json"
			}
		});

		//on change - trigger change event, only first time

		objSelect.on("change", function (e) {

			t.onSettingChange(null, objWrapper, false);

		});


		//set the value
		if(selectedValue){

			objSelect.val(selectedValues);
			objSelect.trigger('change');
		}

	}


	/**
	 * set post picker value
	 */
	function setPostPickerValue(objWrapper, value){

    	if(!value)
    		var value = [];

    	objWrapper.data("post_picker_value", value);

        if(jQuery.isArray(value) == false)
        	value = [value];

        //clear the picker

        if (value.length === 0) {

        	initPostPicker(objWrapper);

            return(false);
        }

        //get titles then init

        var urlAjax = g_ucAdmin.getUrlAjax("get_select2_post_titles");

        jQuery.get(urlAjax, {

            'post_ids': value

        }).then(function (data) {

            var response = JSON.parse(data);
            var arrData = response.select2_data;

        	initPostPicker(objWrapper, arrData, value);

        });



	}


	/**
	 * get post picker value
	 */
	function getPostPickerValue(objWrapper){

    	var value = objWrapper.data("post_picker_value");

    	if(value)
    		return(value);

		var objSelect = objWrapper.find(".unite-setting-post-picker");

		var value = objSelect.select2("val");

		return(value);
	}


	function _______LINK_____(){}

	/**
	 * init link
	 */
	function initLink(objInput){

		var objRoot = objInput.closest(".unite-setting-link-wrapper");
		var objToggle = objRoot.find(".unite-setting-link-toggle");
		var objFields = objRoot.find(".unite-setting-link-fields");

		objToggle.on("click", function(event){
			event.preventDefault();

			objFields.stop().slideToggle(g_vars.animationDuration);
		});
	}

	/**
	 * get link value
	 */
	function getLinkInputValue(objInput){

		var objRoot = objInput.closest(".unite-setting-link-wrapper");
		var url = objRoot.find(".unite-setting-link").val();
		var external = objRoot.find(".unite-setting-link-external").prop("checked") ? "on" : "";
		var nofollow = objRoot.find(".unite-setting-link-nofollow").prop("checked") ? "on" : "";
		var attributes = objRoot.find(".unite-setting-link-attributes").val();

		var value = {
			url: url,
			is_external: external,
			nofollow: nofollow,
			custom_attributes: attributes
		};

		return value;
	}

	/**
	 * set link value
	 */
	function setLinkInputValue(objInput, value){

		var objRoot = objInput.closest(".unite-setting-link-wrapper");

		if (typeof value === "string")
			value = { url: value };

		objRoot.find(".unite-setting-link").val(value.url);
		objRoot.find(".unite-setting-link-external").prop("checked", value.is_external === "on");
		objRoot.find(".unite-setting-link-nofollow").prop("checked", value.nofollow === "on");
		objRoot.find(".unite-setting-link-attributes").val(value.custom_attributes);

	}

	/**
	 * clear link value
	 */
	function clearLinkInputValue(objInput, defaultValue){

		setLinkInputValue(objInput, { url: defaultValue });
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

		var objInputs = getObjInputs();

		//init animations
		var objAnimations = objInputs.find("select.uc-select-animation-type");

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

	function __________TYPOGRAPHY_PANEL__________(){}

	/**
	 * clear typography dialog
	 */
	function clearTypography(objInput){

		trace("clear typography");

	}

	/**
	 * open typography dialog
	 */
	function onTypographyButtonClick(){

		var objButton = jQuery(this);

		var objInput = objButton.parents(".unite-settings-typography");

		//show the dialog element

		var objTypographyDialog = getTypographyDialog();

		objInput.append(objTypographyDialog);

		objTypographyDialog.show();

		//set the settings

	}


	/**
	 * on body click
	 */
	function onTypographyBodyClick(e){

		//dialog not opened - close

		var objDialog = getTypographyDialog()

		if(objDialog.is(":hidden"))
			return(false);

		//dialog opened, click on the button or the dialog - close

		var objElement = jQuery(e.target);

		if(objElement.hasClass("unite-button-typography")){

			return(false);
		}

		var objParentDialog = objElement.parents(".uc-dialog-typgoraphy");

		if(objParentDialog.length)
			return(false);


		//hide the dialog

		objDialog.hide();

		//apply the selectors

		var objInput = objDialog.parents(".unite-settings-typography");

		if(objInput.length == 0)
			return(false);


		trace("apply the dialog selectors");
		trace("move the values");


	}

	/**
	 * on typography selectors change
	 */
	function onTypgoraphySelectorsChange(){

		var objDialog = getTypographyDialog();

		var objSettings = objDialog.data("settings");

		var cssSelectors = objSettings.getSelectorsCss();

		trace(cssSelectors);

		trace("selectors change");

	}

	/**
	 * init the typography dialog
	 */
	function initTypographyDialog(){

		var objDialog = getTypographyDialog();

		if(objDialog.length == 0)
			throw new Error("missing typography dialog");

		var isInited = objDialog.data("is_inited");

		if(isInited === true)
			return(false);

		//init settings

		var objSettings = new UniteSettingsUC();

		var objSettingsElement = objDialog.find(".unite-settings");

		var options = {disable_exclude_selector: true};

		objSettings.init(objSettingsElement, options);

		objSettings.setEventOnSelectorsChange(onTypgoraphySelectorsChange);

		objDialog.data("is_inited", true);
		objDialog.data("settings", objSettings);

		//init the body click event

		jQuery("body:not(.unite-button-typography)").on('click', onTypographyBodyClick);


	}


	/**
	 * get the dialog
	 */
	function getTypographyDialog(){

		var objDialog = g_objWrapper.find('.uc-dialog-typgoraphy');

		if(objDialog.length == 0)
			throw new Error("typography dialog not found");


		return(objDialog);
	}


	/**
	 * init typography dialog
	 */
	function initTypography(objInput){

		initTypographyDialog();

		var objButton = objInput.find('.unite-button-typography');

		//open dialog
		objButton.on('click', onTypographyButtonClick);

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

			if(objCheck.prop("checked") === true){
				objSection.show();
			}else{
				objSection.hide();
			}
		});

		//init inputs
		var objInputs = g_temp.objFontsPanel.find("input, select");
		jQuery.each(objInputs, function(index, input){
			var objInput = jQuery(input);

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

			if(objCheckbox.prop("checked") === false)
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

			objToggle.prop("checked", true);

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

		objToggles.prop("checked", false);

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

	function _______SWITCHER_____(){}


	/**
	 * update switcher state by it's value
	 */
	function updateSwitcherState(objSwitcher){

		var value = objSwitcher.data("value");

		value = g_ucAdmin.boolToStr(value);

		var valueChecked = objSwitcher.data("checkedvalue");

		valueChecked = g_ucAdmin.boolToStr(valueChecked);

		if(value == valueChecked)
			objSwitcher.addClass("uc-checked");
		else
			objSwitcher.removeClass("uc-checked");

	}

	/**
	 * clear the switcher
	 */
	function clearSwitcher(objSwitcher, dataname, checkboxDataName){

		var defaultValue = objSwitcher.data(dataname);

		objSwitcher.data("value",defaultValue);

		updateSwitcherState(objSwitcher);
	}

	/**
	 * get switcher value
	 */
	function getSwitcherValue(objSwitcher){

		var isCheched = objSwitcher.hasClass("uc-checked");

		var attribute = "uncheckedvalue";

		if(isCheched == true)
			attribute = "checkedvalue";

		var value = objSwitcher.data(attribute);

		value = g_ucAdmin.boolToStr(value);

		return(value);
	}


	/**
	 * init switcher events
	 */
	function initSwitcherEvents(objSwitcher){
				
		objSwitcher.on("click", function(){
			objSwitcher.toggleClass("uc-checked");
		});

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
	 * on selects change
	 * @TODO: implement the hide/show, enabled/disables functionality
	 */
	function onControlSettingChange(event, input){
		
		var debugControls = false;

		if(!input)
			input = this;

		var objInput = jQuery(input);
		var controlID = input.name;
		
		if(!controlID)
			controlID = objInput.data("name");
		
		if(!controlID)
			return(true);
		
		var controlValue = getSettingInputValue(objInput);
		
		if(!g_arrControls[controlID])
			return(true);

		g_temp.cacheValues = null;

		var arrChildControls = g_arrControls[controlID];

		if(debugControls == true){
			trace("controls change");
			trace("parent value: " + controlValue)
			trace(controlValue);
		}

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

			if(debugControls == true){
				trace("setting: "+childName +" | value: "+value+" | action: " + action);
			}

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
		g_objParent.find("input[type='radio']:checked").trigger("click");

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

		var objInputs = getObjInputs();

		var objIcons = objInputs.find(".unite-settings-responsive-icon");

		objIcons.on("click", onResponsiveIconClick);

	}

	function _________SELECTORS__________(){}

	/**
	 * get selectors css
	 */
	function getTypographySelectorsCss(objInput){

		var css = "";

		var objDialog = getTypographyDialog();

		var objSelectedValues = objDialog.find(':selected');

		objSelectedValues.each(function(){

			var objSelected = jQuery(this);
			var selectedIndex = objSelected.index();

			//if first option selected - ignore
			if(selectedIndex == 0)
			return(true);

			var selectedValue = objSelected.val();
			var selectedCssProperty = objSelected.parents('select').data('fieldname');

			css += selectedCssProperty+':'+selectedValue+';';

		});


		return(css);
	}

	/**
	 * check if the input has selector
	 */
	function isInputHasSelector(objInput){

		var objSelectors = objInput.data("selectors");

		if(objSelectors)
			return(true);

		return(false);
	}


	/**
	 * get input selector css
	 */
	function getInputSelectorCss(objInput){

		var objSelectors = objInput.data("selectors");

		if(!objSelectors)
			return(null);

		var selector = g_ucAdmin.getVal(objSelectors,"selector");

		var selectorCss = null;

		if(!selector){

			// selector = g_ucAdmin.getVal(objSelectors,"selector");
			jQuery.each(objSelectors, function(selectorNum, selectorVal){

				//find last value
				var lastValue = Object.values(objSelectors).pop();

				//if last value, then no coma after value
				if(selectorVal == lastValue)
				selector += selectorVal
				else
				selector += selectorVal+','

			});

			var type = getInputType(objInput);

			switch(type){
				case "typography":
					selectorCss = getTypographySelectorsCss(objInput);
				break;
				default:
					return(null);
				break;
			}
		}

		if(!selectorCss)
			var selectorCss = g_ucAdmin.getVal(objSelectors,"selector_value");

		var value = getSettingInputValue(objInput);

		if(typeof value == "object"){
			trace("handle obj value for selector");
			return(false);
		}

		//for empty value, skip

		if(value == "")
			return("");

		//var type = getInputType(objInput);


		selectorCss = g_ucAdmin.replaceAll(selectorCss, "{{VALUE}}", value);

		selectorCss = g_ucAdmin.replaceAll(selectorCss, "{{SIZE}}", value);

		//temporary work around, will be switched later

		selectorCss = g_ucAdmin.replaceAll(selectorCss, "{{UNIT}}", "px");


		var outputCss = selector+"{"+selectorCss+"}";

		return(outputCss);
	}


	/**
	 * get selectors css
	 */
	this.getSelectorsCss = function(){

		var objInputs = getObjInputs();

		var css = "";

		jQuery.each(objInputs, function(index, input){
			var objInput = jQuery(input);

			var inputCss = getInputSelectorCss(objInput);

			if(inputCss)
				css += inputCss+"\n";
		});

		return(css);
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
	 * set on change event, this function should run before init
	 */
	this.setEventOnSelectorsChange = function(func){

		t.onEvent(t.events.SELECTORS_CHANGE, func);
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
			objInput = jQuery(event.target);

		if(!objInput || objInput.length == 0)
			return(true);

		var type = getInputType(objInput);

		if(!type)
			return(true);

		if(type === "color")
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

		//trigger event by type
		var isHasSelector = isInputHasSelector(objInput);

		if(isHasSelector == true){
			//selectors change only apply to instant change
			//if(isInstantChange != true)
				//return(true);

			eventToTrigger = t.events.SELECTORS_CHANGE;
		}
		else{
			var eventToTrigger = t.events.CHANGE;
			if(isInstantChange == true)
				eventToTrigger = t.events.INSTANT_CHANGE;
		}

		var name = getInputName(objInput);

		triggerEvent(eventToTrigger, {"name": name, "value": value});
	};


	/**
	 * trigger event
	 */
	function triggerEvent(eventName, params){
		if(!params)
			params = null;

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
		
		trace(objControls);
		
		if(!objControls)
			return(false);

		g_objWrapper.removeAttr("data-controls");
		
		g_arrControls = objControls.parents;
		g_arrChildrenControls = objControls.children;
		
		//init events
		g_objParent.find("select").on("change", onControlSettingChange);
		g_objParent.find("input[type='radio'], .unite-setting-switcher").on("click", onControlSettingChange);
		
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
				initSvgPicker(objInput);
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
			case "gallery":
				initGallery(objInput);
			break;
			case "typography":
				initTypography(objInput);
			break;
			case "link":
				initAddFieldsEvents(objInput);
			break;
			case "switcher":

				initSwitcherEvents(objInput);

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
				objInput.on("click", funcChange);
			break;
			case "div": //special types
			break;
			default:
				objInput.on("change", funcChange);

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

		//init link events
		var objLinkSettings = g_objParent.find(".unite-setting-link");
		initLink(objLinkSettings);

		initControls();
	}


	/**
	 * init setting additional fields events, trigger event of the base field
	 * on additional fields change
	 */
	function initAddFieldsEvents(objBaseInput){

		var objWrapper = objBaseInput.closest(".unite-setting-input");
		var objAddInputs = objWrapper.find("input").not(objBaseInput);

		jQuery.each(objAddInputs, function(index, addinput){
			var objAddInput = jQuery(addinput);

			initInputEvents(objAddInput, function(event, objInput, isInstant){
				t.onSettingChange(event, objBaseInput, isInstant);
			});
		});

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

		var objInputsClick = g_objParent.find("input[type='radio'],.unite-setting-switcher");
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
		if(g_objWrapper)
			g_objWrapper.find(".unite-postbox .unite-postbox-title").off("click");

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
	this.init = function(objParent, options){

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

		g_temp.disableExcludeSelector =	g_ucAdmin.getVal(options, "disable_exclude_selector");


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
