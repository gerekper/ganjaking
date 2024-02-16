
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
	function initTipsy(){
		if (typeof jQuery("body").tipsy !== "function")
			return;

		g_objWrapper.tipsy({
			selector: ".uc-tip",
			delayIn: 200,
			offset: 4,
			html: true,
			gravity: function () {
				var objTip = jQuery(this);

				return objTip.data("tipsy-gravity") || "s";
			},
		});

		jQuery(document).on("click", ".uc-tip", function () {
			var objTip = jQuery(this);

			// check if the element is still visible
			if (objTip.is(":visible") === true) {
				// trigger title update
				objTip.tipsy("hide");
				objTip.tipsy("show");
			} else {
				// remove the tipsy to fix the disappearance bug
				// https://github.com/CreativeDream/jquery.tipsy#bugs
				jQuery(".tipsy").remove();
			}
		});
	}

	/**
	 * get all settings inputs
	 */
	function getObjInputs(controlsOnly){

		validateInited();

		//include
		var selectors = "input, textarea, select, .unite-setting-inline-editor, .unite-setting-input-object";
		var selectorNot = "input[type='button'], input[type='range'], input[type='search'], .unite-responsive-picker, .unite-units-picker, .unite-setting-range-input";

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

			if(g_temp.isRepeaterExists === true)
				selectorNot += ", .unite-setting-repeater *";

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
			break;
			case "text":
				if(objInput.hasClass("unite-color-picker"))
					type = "color";
				else if (objInput.hasClass("unite-setting-image-input"))
					type = "image";
				else if (objInput.hasClass("unite-setting-mp3-input"))
					type = "mp3";
				else if (objInput.hasClass("unite-postpicker-input"))
					type = "post";
				else if (objInput.hasClass("unite-setting-link"))
					type = "link";
			break;
			case "textarea":
				if(objInput.hasClass("mce_editable") || objInput.hasClass("wp-editor-area"))
					type = "editor_tinymce";
			break;
			case "hidden":
				if (objInput.hasClass("unite-iconpicker-input"))
					type = "icon";
			break;
			case "span":
			case "div":
				type = customType;

				if (!type) {
					if (objInput.hasClass("uc-setting-items-panel"))
						type = "items";
					else if (objInput.hasClass("uc-setting-fonts-panel"))
						type = "fonts";
					else if (objInput.hasClass("unite-setting-inline-editor"))
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
				value = getDimentionsValue(objInput);
			break;
			case "range":
				value = getRangeSliderValue(objInput);
			break;
			case "switcher":
				value = getSwitcherValue(objInput);
			break;
			case "tabs":
				flagUpdate = false;
			break;
			case "typography":
			case "textshadow":
			case "boxshadow":
			case "css_filters":
				value = getSubSettingsValue(objInput);
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
			case "text":
			case "password":
				if (!name)
					return;

				defaultValue = objInput.data(dataname);

				if (typeof defaultValue === "object")
					defaultValue = JSON.stringify(defaultValue);

				if (type === "select") {
					if (defaultValue === true)
						defaultValue = "true";
					else if (defaultValue === false)
						defaultValue = "false";
				}

				objInput.val(defaultValue);

				if (type === "select")
					objInput.trigger("change.select2");
			break;
			case "hidden":
				defaultValue = objInput.data(dataname);
				objInput.val(defaultValue);
			break;
			case "icon":
				defaultValue = objInput.data(dataname);
				objInput.val(defaultValue);
				objInput.trigger("input");
			break;
			case "dimentions":
				defaultValue = objInput.data(dataname);

				clearDimentionsValue(objInput, defaultValue);
			break;
			case "range":
				defaultValue = objInput.data(dataname);

				clearRangeSliderValue(objInput, defaultValue);
			break;
			case "switcher":
				defaultValue = objInput.data(dataname);

				clearSwitcherValue(objInput, defaultValue);
			break;
			case "tabs":
				defaultValue = objInput.data(dataname);

				clearTabsValue(objInput, defaultValue);
			break;
			case "typography":
			case "textshadow":
			case "boxshadow":
			case "css_filters":
				defaultValue = objInput.data(dataname);

				clearSubSettingsValue(objInput, defaultValue);
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
			case "gallery":
				clearGallery(objInput);
			break;
			case "select2":

				// no clear

				/*
				t.disableTriggerChange();

				defaultValue = objInput.data(dataname);
				objInput.select2("val",defaultValue);
				objInput.trigger("change");

				t.enableTriggerChange();
				*/

			break;
			case "group_selector":
			case "map":
				// no clear
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

				if (type === "select")
					objInput.trigger("change.select2");
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
			case "dimentions":
				setDimentionsValue(objInput, value);
			break;
			case "range":
				setRangeSliderValue(objInput, value);
			break;
			case "switcher":
				setSwitcherValue(objInput, value);
			break;
			case "tabs":
				setTabsValue(objInput, value);
			break;
			case "typography":
			case "textshadow":
			case "boxshadow":
			case "css_filters":
				setSubSettingsValue(objInput, value);
			break;
			case "icon":
				setIconInputValue(objInput, value);
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
			case "multiselect":
				value = multiSelectModifyForSet(value);
				objInput.val(value);
			break;
			case "select2":
				objInput.select2("val", value);
				objInput.trigger("change");
			break;
			case "gallery":
				setGalleryValues(objInput, value);
			break;
			case "group_selector":
			case "map":
				// no set
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

		applyControls(objInputs);

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

		t.disableTriggerChange();

		setInputValue(objInput, value);

		t.enableTriggerChange();

	};


	/**
	 * set values, clear first
	 */
	this.setValues = function(objValues, noClear){

		validateInited();

		if (noClear !== true)
			t.clearSettings();

		//if empty values - exit
		if (typeof objValues !== "object")
			return;

		t.disableTriggerChange();

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

		applyControls(objInputs);

		t.enableTriggerChange();

	};

	/**
	 * determine whether the change event trigger is disabled
	 */
	this.isTriggerChangeDisabled = function () {
		return g_temp.enableTriggerChange === false;
	};

	/**
	 * enable the change event trigger
	 */
	this.enableTriggerChange = function () {
		g_temp.enableTriggerChange = true;
	};

	/**
	 * disable the change event trigger
	 */
	this.disableTriggerChange = function () {
		g_temp.enableTriggerChange = false;
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
	 * init range slider
	 */
	function initRangeSlider(objWrapper, funcChange) {
		var objSlider = objWrapper.find(".unite-setting-range-slider");
		var objInput = objWrapper.find(".unite-setting-range-input");

		objSlider.slider({
			min: objSlider.data("min"),
			max: objSlider.data("max"),
			step: objSlider.data("step"),
			value: objSlider.data("value"),
			range: "min",
			slide: function (event, ui) {
				objInput.val(ui.value);

				funcChange(null, objWrapper);
			},
			change: function () {
				funcChange(null, objWrapper);
			},
		});

		objInput.on("input", function (event) {
			objSlider.slider("value", objInput.val());

			funcChange(event, objWrapper);
		});

		setUnitsPickerChangeHandler(objWrapper, function () {
			funcChange(null, objWrapper);
		});
	}

	/**
	 * destroy range slider
	 */
	function destroyRangeSlider(objWrapper) {
		var objSlider = objWrapper.find(".unite-setting-range-slider");
		var objInput = objWrapper.find(".unite-setting-range-input");

		objSlider.slider("destroy");
		objInput.off("input");
	}

	/**
	 * get range slider value
	 */
	function getRangeSliderValue(objWrapper) {
		var data = {};

		data["size"] = objWrapper.find(".unite-setting-range-input").val();
		data["unit"] = getUnitsPickerValue(objWrapper);

		return data;
	}

	/**
	 * set range slider value
	 */
	function setRangeSliderValue(objWrapper, value) {
		objWrapper.find(".unite-setting-range-input")
			.val(value["size"])
			.trigger("input");

		setUnitsPickerValue(objWrapper, value["unit"]);
	}

	/**
	 * clear range slider value
	 */
	function clearRangeSliderValue(objWrapper, defaultValue) {
		setRangeSliderValue(objWrapper, defaultValue);
	}


	function _______SELECT2_____(){}

	/**
	 * init select2
	 */
	function initSelect2(objInput, options) {
		var isMultiple = objInput.prop("multiple") === true;
		var dropdownParent = objInput.closest(".unite-setting-input");

		if (dropdownParent.length === 0)
			dropdownParent = objInput.closest(".unite-inputs");

		if (dropdownParent.length === 0)
			dropdownParent = jQuery("body");

		var settings = jQuery.extend({
			dropdownParent: dropdownParent,
			closeOnSelect: isMultiple === false,
			minimumResultsForSearch: 10,
			templateResult: prepareTemplate,
			templateSelection: prepareTemplate,
		}, options);

		objInput
			.select2(settings)
			.on("change", function () {
				appendPlusButton();

				t.onSettingChange(null, objInput, true);
			}).on("select2:closing", function () {
			// hide tipsy
			jQuery(".select2-dropdown").find(".uc-tip").trigger("mouseleave");
		});

		appendPlusButton();

		function appendPlusButton() {
			var objOptions = objInput.find("option");
			var objSelectedOptions = objOptions.filter(":selected");

			if (objOptions.length === 0)
				return;

			// first check if all options are selected
			if (objOptions.length === objSelectedOptions.length)
				return;

			// find inline search and insert plus button before
			objInput
				.next(".select2")
				.find(".select2-search--inline")
				.before("<li class=\"select2-selection__choice select2-selection__uc-plus-button\">+</li>");
		}

		function prepareTemplate(data) {
			var $option = jQuery(data.element);
			var content = $option.data("content");
			var output = data.text;

			if (content)
				output = jQuery(content);

			return output;
		}
	}


	function _______MULTI_SELECT_____(){}

	/**
	 * modify value for save, turn to array
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
		var objTabs = g_objWrapper.children(".unite-settings-accordion-saps-tabs").children(".unite-settings-tab");
		var objAccordions = g_objWrapper.children(".unite-postbox:not(.unite-no-accordion)");
		var objAccordionTitles = objAccordions.children(".unite-postbox-title");

		objTabs.on("click", function () {
			var objTab = jQuery(this);
			var objRoot = objTab.closest(".unite-settings-accordion-saps-tabs");
			var id = objTab.data("id");

			objRoot.find(".unite-settings-tab").removeClass("unite-active");
			objTab.addClass("unite-active");

			var objContents = objAccordions.hide().filter("[data-tab='" + id + "']").show();

			if (objContents.filter(".unite-active").length === 0)
				objContents.filter(":first").find(".unite-postbox-title").trigger("click");
		});

		objAccordionTitles.on("click", function () {
			var objRoot = jQuery(this).closest(".unite-postbox");
			var tab = objRoot.data("tab");

			objAccordions.filter("[data-tab='" + tab + "']")
				.not(objRoot)
				.removeClass("unite-active")
				.find(".unite-postbox-inside")
				.stop()
				.slideUp(g_vars.animationDuration);

			objRoot
				.toggleClass("unite-active")
				.find(".unite-postbox-inside")
				.stop()
				.slideToggle(g_vars.animationDuration);
		});

		if (objTabs.length > 0)
			objTabs.filter(":first").trigger("click");
		else if (objAccordionTitles.length > 0)
			objAccordionTitles.filter(":first").trigger("click");
		else
			objAccordions.filter(":first").find(".unite-postbox-inside").show();
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
	 * init icon picker
	 */
	function initIconPicker(objInput, funcChange) {
		var iconsType = objInput.data("icons_type");

		if (!iconsType)
			iconsType = "fa";

		var objDialogWrapper = iconPicker_initDialog(iconsType);

		if (!objDialogWrapper || objDialogWrapper.length === 0) {
			trace("Icon picker dialog not initialized.");
			return;
		}

		var objPickerWrapper = objInput.closest(".unite-iconpicker");
		var objPickerInput = objPickerWrapper.find(".unite-iconpicker-input");
		var objPickerError = objPickerWrapper.find(".unite-iconpicker-error");
		var objPickerButton = objPickerWrapper.find(".unite-iconpicker-button");
		var objPickerButtonNone = objPickerButton.filter("[data-action='none']");
		var objPickerButtonUpload = objPickerButton.filter("[data-action='upload']");
		var objPickerButtonLibrary = objPickerButton.filter("[data-action='library']");
		var objPickerUploadedIcon = objPickerWrapper.find(".unite-iconpicker-uploaded-icon");
		var objPickerLibraryIcon = objPickerWrapper.find(".unite-iconpicker-library-icon");
		var pickerErrorTimeout = -1;

		objPickerButtonNone.on("click", function () {
			objPickerInput.val(null).trigger("input");
		});

		objPickerButtonUpload.on("click", function () {
			g_ucAdmin.openAddImageDialog("Choose Icon", function (imageUrl, imageId) {
				var fileName = imageUrl.split("/").pop();
				var fileExtension = fileName.split(".").pop();

				if (fileExtension !== "svg") {
					clearTimeout(pickerErrorTimeout);

					objPickerError.html("Icon must be of type SVG.").show();

					pickerErrorTimeout = setTimeout(function () {
						objPickerError.hide();
					}, 5000);

					return;
				}

				objPickerError.hide();

				objPickerInput
					.data("image-id", imageId)
					.val(imageUrl)
					.trigger("input");
			}, false);
		});

		objPickerButtonLibrary.on("click", function () {
			if (objDialogWrapper.dialog("isOpen")) {
				objDialogWrapper.dialog("close");
			} else {
				// set selected icon
				var iconName = objPickerInput.data("icon-name");

				objDialogWrapper
					.find(".unite-iconpicker-dialog-icon")
					.removeClass("icon-selected")
					.filter("[data-name='" + iconName + "']")
					.addClass("icon-selected");

				objDialogWrapper
					.data("objpicker", objPickerWrapper)
					.dialog("open");
			}
		});

		objPickerInput.on("input", function (event) {
			var value = objPickerInput.val().trim();

			// trigger settings change
			funcChange(event, objPickerInput);

			// deactivate buttons
			objPickerButton.removeClass("ue-active");

			// check for uploaded icon
			var isUpload = value.indexOf(".svg") > -1;

			if (isUpload === true){
				objPickerButtonUpload.addClass("ue-active");
				objPickerUploadedIcon.attr("src", value);

				return;
			}

			// check for library icon
			var icon = value;

			if (iconsType === "fa")
				icon = value.replace("fa fa-", "");

			var iconHash = icon + "_" + iconsType;

			if (g_iconsHash[iconHash]) {
				objPickerButtonLibrary.addClass("ue-active");

				var objType = iconPicker_getObjIconsType(iconsType);
				var iconHtml = iconPicker_getIconHtmlFromTemplate(objType.template, icon);

				objPickerLibraryIcon.html(iconHtml);

				return;
			}

			// fallback to the "none"
			objPickerButtonNone.addClass("ue-active");
		});

		objPickerInput.trigger("input");
	}

	/**
	 * init icon picker dialog
	 */
	function iconPicker_initDialog(type) {
		if (!type)
			type = "fa";

		var dialogID = "unite_icon_picker_dialog_" + type;

		var objDialogWrapper = jQuery("#" + dialogID);

		if (objDialogWrapper.length !== 0) {
			g_iconsHash = jQuery("body").data("uc_icons_hash");

			return objDialogWrapper;
		}

		if (type === "elementor" && g_ucFaIcons.length === 0)
			type = "fa";

		if (type === "fa") {
			iconPicker_addIconsType(type, g_ucFaIcons, function (icon) {
				if (icon.indexOf("fa-") === -1)
					icon = "fa fa-" + icon;

				var html = "<i class=\"" + icon + "\"></i>";

				return html;
			});
		} else if (type === "elementor") {
			iconPicker_addIconsType(type, g_ucElIcons, function (icon) {
				var html = "<i class=\"" + icon + "\"></i>";

				return html;
			});
		}

		var objType = iconPicker_getObjIconsType(type);
		// var isAddNew = g_ucAdmin.getVal(objType, "add_new");

		var htmlDialog = "<div id=\"" + dialogID + "\" class=\"unite-iconpicker-dialog unite-inputs unite-picker-type-" + type + "\" style=\"display:none\">";
		htmlDialog += "<div class=\"unite-iconpicker-dialog-top\">";
		htmlDialog += "<input class=\"unite-iconpicker-dialog-input-filter\" type=\"text\" placeholder=\"Type to filter\" value=\"\">";
		htmlDialog += "<span class=\"unite-iconpicker-dialog-icon-name\"></span>";

		// add new functionality
		// if (isAddNew === true) {
		// 	htmlDialog += "<a class=\"unite-button-secondary unite-iconpicker-dialog-button-addnew\">Add New Shape</a>";
		// }

		htmlDialog += "</div>";
		htmlDialog += "<div class=\"unite-iconpicker-dialog-icons-container\"></div></div>";

		jQuery("body").append(htmlDialog);

		objDialogWrapper = jQuery("#" + dialogID);

		var objContainer = objDialogWrapper.find(".unite-iconpicker-dialog-icons-container");
		var objFilter = objDialogWrapper.find(".unite-iconpicker-dialog-input-filter");
		var objIconName = objDialogWrapper.find(".unite-iconpicker-dialog-icon-name");

		// add icons
		var arrIcons = objType.icons;
		var isArray = jQuery.isArray(arrIcons);

		jQuery.each(arrIcons, function (index, icon) {
			var iconTitle = null;

			if (isArray === false) {
				iconTitle = icon;
				icon = index;
			}

			var iconHtml = iconPicker_getIconHtmlFromTemplate(objType.template, icon);
			var objIcon = jQuery("<span class=\"unite-iconpicker-dialog-icon\">" + iconHtml + "</span>");

			var iconName = icon;

			if (objType && typeof objType.getIconName === "function")
				iconName = objType.getIconName(icon);

			var iconHash = icon + "_" + type;

			if (g_iconsHash.hasOwnProperty(iconHash) === false) {
				objIcon.attr("data-name", iconName)
				objIcon.data("title", iconTitle);
				objIcon.data("name", iconName);
				objIcon.data("value", icon);

				objContainer.append(objIcon);

				g_iconsHash[iconHash] = objIcon;
			}
		});

		jQuery("body").data("uc_icons_hash", g_iconsHash);

		var dialogTitle = "Choose Icon";

		if (type === "shape")
			dialogTitle = "Choose Shape";

		objDialogWrapper.dialog({
			autoOpen: false,
			height: 500,
			width: 800,
			dialogClass: "unite-ui unite-ui2",
			title: dialogTitle,
			open: function (event, ui) {
				objContainer.scrollTop(0);

				var objSelectedIcon = objContainer.find(".icon-selected");

				if (objSelectedIcon.length === 0)
					return false;

				if (objSelectedIcon.is(":hidden") === true)
					return false;

				// scroll to icon
				var containerHeight = objContainer.height();
				var iconPos = objSelectedIcon.position().top;

				if (iconPos > containerHeight)
					objContainer.scrollTop(iconPos - (containerHeight / 2 - 50));
			},
		});

		// on filter input
		objFilter.on("input", function () {
			var value = objFilter.val().trim();

			objDialogWrapper.find(".unite-iconpicker-dialog-icon").each(function () {
				var objIcon = jQuery(this);
				var name = objIcon.data("name");
				var isVisible = false;

				if (value === "" || name.indexOf(value) > -1)
					isVisible = true;

				objIcon.toggle(isVisible);
			});
		});

		// on icon click
		objContainer.on("click", ".unite-iconpicker-dialog-icon", function () {
			var objIcon = jQuery(this);
			var iconName = objIcon.data("name");
			var iconValue = objIcon.data("value");

			// select icon
			objDialogWrapper
				.find(".unite-iconpicker-dialog-icon")
				.removeClass("icon-selected")
				.filter(objIcon)
				.addClass("icon-selected");

			// update picker value
			var inputValue = iconValue;

			if (type === "fa") {
				if (iconName.indexOf("fa-") === -1)
					inputValue = "fa fa-" + iconName;
				else
					inputValue = iconName;
			}

			objDialogWrapper
				.data("objpicker")
				.find(".unite-iconpicker-input")
				.data("icon-name", iconName)
				.val(inputValue)
				.trigger("input");

			// close dialog
			objDialogWrapper.dialog("close");
		});

		// on icon mouseenter
		objContainer.on("mouseenter", ".unite-iconpicker-dialog-icon", function () {
			var objIcon = jQuery(this);
			var iconName = objIcon.data("name");
			var iconTitle = objIcon.data("title");

			if (iconTitle)
				iconName = iconTitle;

			if (type === "fa") {
				iconName = iconName.replace("fa-", "");

				if (iconName.indexOf("fab ") === 0)
					iconName = iconName.replace("fab ", "") + " [brand]";
				else if (iconName.indexOf("fal ") === 0)
					iconName = iconName.replace("fal ", "") + " [light]";
				else if (iconName.indexOf("far ") === 0)
					iconName = iconName.replace("far ", "") + " [regular]";
				else
					iconName = iconName + " [solid]";
			}

			objIconName.text(iconName);
		});

		// on icon mouseleave
		objContainer.on("mouseleave", ".unite-iconpicker-dialog-icon", function () {
			objIconName.text("");
		});

		return objDialogWrapper;
	}

	/**
	 * icon picker - add icons type
	 */
	function iconPicker_addIconsType(name, arrIcons, iconsTemplate, optParams) {
		var key = "icon_picker_type_" + name;
		var objType = g_ucAdmin.getGlobalData(key);

		if (objType)
			return;

		var params = {
			name: name,
			icons: arrIcons,
			template: iconsTemplate,
		};

		if (optParams)
			jQuery.extend(params, optParams);

		g_ucAdmin.storeGlobalData(key, params);
	}

	/**
	 * icon picker - get icons type object
	 */
	function iconPicker_getObjIconsType(name) {
		var key = "icon_picker_type_" + name;
		var objType = g_ucAdmin.getGlobalData(key);

		if (!objType)
			throw new Error("Icons type \"" + name + "\" not found.");

		return objType;
	}

	/**
	 * icon picker - get icons type object
	 */
	function iconPicker_getIconHtmlFromTemplate(template, icon) {
		if (!template)
			throw new Error("Icon template not found.");

		if (typeof template == "function")
			return template(icon);

		return template.replace("[icon]", icon);
	}

	/**
	 * destroy icon pickers
	 */
	function destroyIconPickers() {
		g_objWrapper.find(".unite-iconpicker-button").off("click");
		g_objWrapper.find(".unite-iconpicker-input").off("input");
	}

	/**
	 * get icon input value
	 */
	function getIconInputData(objInput) {
		var inputValue = objInput.val();
		var isUpload = inputValue.indexOf(".svg") > -1;

		if (isUpload === true) {
			var imageId = objInput.data("image-id");
			var imageUrl = g_ucAdmin.urlToRelative(inputValue);

			var svgArray = [{
				id: imageId,
				url: imageUrl,
				library: 'svg',
			}];

			return svgArray;
		}

		return inputValue;
	}

	/**
	 * set icon input value
	 */
	function setIconInputValue(objInput, value){
		if (jQuery.isArray(value) === true) {
			var image = value[0];

			objInput.data("image-id", image.id);

			value = g_ucAdmin.urlToFull(image.url);
		}

		objInput.val(value).trigger("input");
	}


	function __________TABS__________(){}

	/**
	 * init tabs
	 */
	function initTabs(objWrapper) {
		objWrapper.find(".unite-setting-tabs-item-input").on("change", function () {
			var objInput = jQuery(this);
			var id = objInput.attr("name");
			var value = objInput.val();

			objInput.closest(".unite-list-settings")
				.find(".unite-setting-row[data-tabs-id=\"" + id + "\"]")
				.addClass("unite-tabs-hidden")
				.filter("[data-tabs-value=\"" + value + "\"]")
				.removeClass("unite-tabs-hidden");
		});
	}

	/**
	 * destroy tabs
	 */
	function destroyTabs() {
		objWrapper.find(".unite-setting-tabs-item-input").off("change");
	}

	/**
	 * set tabs value
	 */
	function setTabsValue(objWrapper, value) {
		objWrapper.find(".unite-setting-tabs-item-input").each(function () {
			var objInput = jQuery(this);

			objInput.prop("checked", objInput.val() === value);
		});

		objWrapper.find(".unite-setting-tabs-item-input:checked").trigger("change");
	}

	/**
	 * clear tabs value
	 */
	function clearTabsValue(objWrapper, defaultValue) {
		setTabsValue(objWrapper, defaultValue);
	}


	function _______DIMENTIONS_____(){}

	/**
	 * init dimentions
	 */
	function initDimentions(objWrapper, funcChange) {
		var objInputs = objWrapper.find(".unite-dimentions-field-input");
		var objLink = objWrapper.find(".unite-dimentions-link");

		objInputs.on("input", function (event) {
			if (objLink.hasClass("ue-active") === true) {
				var objInput = jQuery(this);
				var value = objInput.val();

				objInputs.not(objInput).val(value);
			}

			funcChange(event, objWrapper);
		});

		objLink.on("click", function (event) {
			objLink.toggleClass("ue-active");

			if (objLink.hasClass("ue-active") === true) {
				var value = objInputs.first().val();

				objInputs.val(value);
			}

			initDimentions_updateLinkTitle(objLink);

			funcChange(event, objWrapper);
		});

		initDimentions_updateLinkTitle(objLink);

		setUnitsPickerChangeHandler(objWrapper, function () {
			funcChange(null, objWrapper);
		});
	}

	/**
	 * init dimentions - update link title
	 */
	function initDimentions_updateLinkTitle(objLink) {
		var title = objLink.hasClass("ue-active") === true
			? objLink.data("title-unlink")
			: objLink.data("title-link");

		objLink.attr("title", title);
	}

	/**
	 * destroy dimentions
	 */
	function destroyDimentions() {
		g_objWrapper.find(".unite-dimentions-field-input").off("input");
		g_objWrapper.find(".unite-dimentions-link").off("click");
	}

	/**
	 * get dimentions value
	 */
	function getDimentionsValue(objWrapper) {
		var data = {};

		objWrapper.find(".unite-dimentions-field-input").each(function () {
			var objInput = jQuery(this);
			var value = objInput.val();
			var key = objInput.data("key");

			data[key] = value;
		});

		objWrapper.find(".unite-dimentions-link").each(function () {
			var objLink = jQuery(this);
			var value = objLink.hasClass("ue-active") === true;
			var key = objLink.data("key");

			data[key] = value;
		});

		data["unit"] = getUnitsPickerValue(objWrapper);

		return data;
	}

	/**
	 * set dimentions value
	 */
	function setDimentionsValue(objWrapper, value) {
		objWrapper.find(".unite-dimentions-field-input").each(function () {
			var objInput = jQuery(this);
			var key = objInput.data("key");

			objInput.val(value[key]);
		});

		objWrapper.find(".unite-dimentions-link").each(function () {
			var objLink = jQuery(this);
			var key = objLink.data("key");

			objLink.toggleClass("ue-active", value[key] === true);

			initDimentions_updateLinkTitle(objLink);
		});

		setUnitsPickerValue(objWrapper, value["unit"]);
	}

	/**
	 * clear dimentions value
	 */
	function clearDimentionsValue(objWrapper, defaultValue) {
		setDimentionsValue(objWrapper, defaultValue);
	}

	/**
	 * get dimentions selector css
	 */
	function getDimentionsSelectorCss(objWrapper, css){
		var value = getDimentionsValue(objWrapper);

		css = g_ucAdmin.replaceAll(css, "{{TOP}}", value.top + value.unit);
		css = g_ucAdmin.replaceAll(css, "{{RIGHT}}", value.right + value.unit);
		css = g_ucAdmin.replaceAll(css, "{{BOTTOM}}", value.bottom + value.unit);
		css = g_ucAdmin.replaceAll(css, "{{LEFT}}", value.left + value.unit);

		return css;
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
		var objOptions = objRoot.find(".unite-setting-link-options");

		objToggle.on("click", function(event){
			event.preventDefault();

			objOptions.stop().slideToggle(g_vars.animationDuration);
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

	function __________SUB_SETTINGS__________(){}

	/**
	 * init sub settings
	 */
	function initSubSettings(objWrapper, funcChange) {

		initSubSettingsDialog(objWrapper);

		var objDialog = getSubSettingsDialog(objWrapper);
		var objResetButton = objWrapper.find(".unite-sub-settings-reset");
		var objEditButton = objWrapper.find(".unite-sub-settings-edit");

		objResetButton.on("click", function () {
			clearSubSettingsValue(objWrapper);
		});

		objEditButton.on("click", function (event) {
			event.stopPropagation();

			var dialogId = objWrapper.data("dialog-id");
			var containsDialog = objWrapper.find(objDialog).length === 1;

			if (containsDialog === true) {
				objDialog.stop().slideToggle(g_vars.animationDuration);
			} else {
				objWrapper.append(objDialog);

				objDialog.stop().slideDown(g_vars.animationDuration);
			}

			g_objParent
				.find(".unite-sub-settings-dialog:not([data-id='" + dialogId + "'])")
				.stop()
				.slideUp(g_vars.animationDuration);

			setSubSettingsDialogChangeHandler(objWrapper, function (value, css) {
				objWrapper.data("value", value).data("css", css);
				objResetButton.removeClass("unite-hidden");

				funcChange(null, objWrapper);
			});

			setSubSettingsDialogValue(objWrapper, objWrapper.data("value"));
		});

		jQuery(document).on("click", function (event) {
			if (objDialog.is(":hidden") === true)
				return;

			var objElement = jQuery(event.target);

			if (objElement.closest(".unite-sub-settings-dialog").length === 1)
				return;

			objDialog.stop().slideUp(g_vars.animationDuration);
		});
	}

	/**
	 * destroy sub settings
	 */
	function destroySubSettings() {
		g_objWrapper.find(".unite-sub-settings-reset").off("click");
		g_objWrapper.find(".unite-sub-settings-edit").off("click");
	}

	/**
	 * init sub settings dialog
	 */
	function initSubSettingsDialog(objWrapper) {

		var objDialog = getSubSettingsDialog(objWrapper);

		if (objDialog.length === 0)
			throw new Error("Missing sub settings dialog.");

		var isInited = objDialog.data("inited");

		if (isInited === true)
			return;

		var objSettings = new UniteSettingsUC();
		var objSettingsElement = objDialog.find(".unite-settings");
		var options = { disable_exclude_selector: true };

		objSettings.init(objSettingsElement, options);

		objSettings.setEventOnSelectorsChange(function () {
			var value = objSettings.getSettingsValues();
			var css = objSettings.getSelectorsCss();
			var onChange = objDialog.data("on_change");

			if (typeof onChange === "function")
				onChange(value, css);
		});

		objDialog.data("inited", true).data("settings", objSettings);
	}

	/**
	 * get sub settings dialog
	 */
	function getSubSettingsDialog(objWrapper) {
		var id = objWrapper.data("dialog-id");

		return g_objWrapper.find(".unite-sub-settings-dialog[data-id='" + id + "']");
	}

	/**
	 * get sub settings dialog css
	 */
	function getSubSettingsDialogCss(objWrapper) {
		var objSettings = getSubSettingsDialog(objWrapper).data("settings");

		return objSettings.getSelectorsCss();
	}

	/**
	 * set sub settings dialog value
	 */
	function setSubSettingsDialogValue(objWrapper, value) {

		var objSettings = getSubSettingsDialog(objWrapper).data("settings");

		objSettings.disableTriggerChange();
		objSettings.setValues(value);
		objSettings.enableTriggerChange();
	}

	/**
	 * set sub settings dialog change handler
	 */
	function setSubSettingsDialogChangeHandler(objWrapper, handler) {
		getSubSettingsDialog(objWrapper).data("on_change", handler);
	}

	/**
	 * get sub settings value
	 */
	function getSubSettingsValue(objWrapper) {
		return objWrapper.data("value") || {};
	}

	/**
	 * set sub settings value
	 */
	function setSubSettingsValue(objWrapper, value) {
		value = value || {};

		setSubSettingsDialogValue(objWrapper, value);

		var css = getSubSettingsDialogCss(objWrapper);

		objWrapper.data("value", value).data("css", css);

		t.onSettingChange(null, objWrapper);

		objWrapper.find(".unite-sub-settings-reset").toggleClass("unite-hidden", jQuery.isEmptyObject(value));
	}

	/**
	 * clear sub settings value
	 */
	function clearSubSettingsValue(objWrapper, defaultValue) {
		setSubSettingsValue(objWrapper, defaultValue || {});
	}

	/**
	 * get sub settings selector css
	 */
	function getSubSettingsSelectorCss(objWrapper, selector) {
		var css = objWrapper.data("css") || "";

		css = g_ucAdmin.replaceAll(css, "{{SELECTOR}}", selector);

		return css;
	}


	function __________TYPOGRAPHY__________(){}

	/**
	 * get typography selector includes
	 */
	function getTypographySelectorIncludes(objWrapper) {
		var includes = {};
		var value = getSubSettingsValue(objWrapper);

		if (g_ucGoogleFonts.fonts[value.font_family]) {
			var slug = value.font_family
				.replace(/\s+/g, "_") // replace spaces with underscore
				.replace(/[^\da-z_-]/ig, "") // remove special characters
				.toLowerCase();

			var handle = "uc_google_font_" + slug;

			includes[handle] = {
				handle: handle,
				type: "css",
				url: g_ucGoogleFonts.url + g_ucGoogleFonts.fonts[value.font_family],
			};
		}

		return includes;
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


	function _______REPEATERS_____(){}

	/**
	 * init repeaters
	 */
	function initRepeaters() {
		var objRepeaters = g_objWrapper.find(".unite-setting-repeater");

		if (objRepeaters.length === 0)
			return;

		g_temp.isRepeaterExists = true;
	}

	/**
	 * init repeater
	 */
	function initRepeater(objWrapper, funcChange) {
		objWrapper.sortable({
			items: ".unite-repeater-item",
			handle: ".unite-repeater-item-header",
			cursor: "move",
			axis: "y",
			update: function () {
				funcChange(null, objWrapper);
			},
		});

		objWrapper.on("click", ".unite-repeater-add", addRepeaterItem);

		objWrapper.on("click", ".unite-repeater-item-title", function () {
			var objItem = jQuery(this).closest(".unite-repeater-item");

			objItem
				.closest(".unite-repeater-items")
				.find(".unite-repeater-item")
				.not(objItem)
				.find(".unite-repeater-item-content")
				.stop()
				.slideUp(g_vars.animationDuration);

			objItem
				.find(".unite-repeater-item-content")
				.stop()
				.slideToggle(g_vars.animationDuration);
		});

		objWrapper.on("click", ".unite-repeater-item-delete", function () {
			jQuery(this).closest(".unite-repeater-item").remove();

			if (objWrapper.find(".unite-repeater-item").length === 0)
				objWrapper.find(".unite-repeater-empty").show();

			funcChange(null, objWrapper);
		});

		objWrapper.on("click", ".unite-repeater-item-duplicate", function () {
			var objItem = jQuery(this).closest(".unite-repeater-item");
			var itemValues = objItem.data("objsettings").getSettingsValues();

			addRepeaterItem(null, objWrapper, itemValues, objItem);
		});
	}

	/**
	 * add repeater item
	 */
	function addRepeaterItem(event, objWrapper, itemValues, objItemInsertAfter) {
		if (!objWrapper)
			objWrapper = jQuery(this).closest(".unite-setting-repeater");

		var isNewItem = false;

		if (!itemValues)
			isNewItem = true;

		var objSettingsTemplate = objWrapper.find(".unite-repeater-template");
		var objItemsWrapper = objWrapper.find(".unite-repeater-items");
		var objEmpty = objWrapper.find(".unite-repeater-empty");

		g_ucAdmin.validateDomElement(objItemsWrapper, "items wrapper");
		g_ucAdmin.validateDomElement(objSettingsTemplate, "settings template");

		objEmpty.hide();

		// get item title
		var itemTitle;

		if (isNewItem === true) {
			itemTitle = objWrapper.data("item-title");

			if (!itemTitle)
				itemTitle = "Item";

			var itemNumber = objWrapper.find(".unite-repeater-item").length + 1;

			itemTitle += " " + itemNumber;
		} else {
			itemTitle = itemValues.title;
		}

		// get item html
		var textDelete = objWrapper.data("text-delete");
		var textDuplicate = objWrapper.data("text-duplicate");

		var html = "<div class='unite-repeater-item'>";
		html += " <div class='unite-repeater-item-header'>";
		html += "	 <div class='unite-repeater-item-title'>" + itemTitle + "</div>";
		html += "	 <div class='unite-repeater-item-actions'>";
		html += "	  <button class='unite-repeater-item-action unite-repeater-item-duplicate uc-tip' title='" + textDuplicate + "'><svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12'><path d='M8.625.375H.375v8.25h8.25V.375Z' /><path d='M10.125 3.375h1.5v8.25h-8.25v-1.5' /></svg></button>";
		html += "		<button class='unite-repeater-item-action unite-repeater-item-delete uc-tip' title='" + textDelete + "'><svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12'><path d='m1.5 1.5 9 9M10.5 1.5l-9 9' /></svg></button>";
		html += "	 </div>";
		html += "	</div>";
		html += "	<div class='unite-repeater-item-content'>";
		html += objSettingsTemplate.html();
		html += "	</div>";
		html += "</div>";

		// change item settings IDs
		var objItem = jQuery(html);
		var objItemSettingsWrapper = objItem.find(".unite_settings_wrapper");

		g_ucAdmin.validateDomElement(objItemSettingsWrapper, "item settings wrapper");

		var options = objItemSettingsWrapper.data("options");
		var idPrefix = options.id_prefix;
		var newID = idPrefix + "item_" + g_ucAdmin.getRandomString(5) + "_";

		html = g_ucAdmin.replaceAll(html, idPrefix, newID);

		// change item settings wrapper ID
		objItem = jQuery(html);
		objItemSettingsWrapper = objItem.find(".unite_settings_wrapper");
		objItemSettingsWrapper.attr("id", "unite_settings_repeater_" + newID);

		if (objItemInsertAfter)
			objItemInsertAfter.after(objItem);
		else
			objItemsWrapper.append(objItem);

		// init item settings
		var objItemSettings = new UniteSettingsUC();

		objItemSettings.init(objItemSettingsWrapper);

		objItem.data("objsettings", objItemSettings);

		var objTitleInput = objItemSettings.getInputByName("title");
		var objItemTitle = objItem.find(".unite-repeater-item-title");

		objTitleInput.on("input", function () {
			var value = objTitleInput.val();

			objItemTitle.html(value);
		});

		if (isNewItem === true) {
			// update title input
			objTitleInput.val(itemTitle);

			// update generated ID
			var objGeneratedIdInput = objItemSettings.getInputByName("generated_id");

			if (objGeneratedIdInput) {
				var generatedId = g_ucAdmin.getRandomString(7);

				objGeneratedIdInput.val(generatedId);
			}
		} else {
			objItemSettings.setValues(itemValues);
		}

		t.onSettingChange(null, objWrapper);
	}

	/**
	 * destroy repeaters
	 */
	function destroyRepeaters() {
		g_objParent.find(".unite-setting-repeater").sortable("destroy");
		g_objParent.find(".unite-repeater-add").off("click");

		g_objParent.find(".unite-repeater-item").each(function () {
			var objItem = jQuery(this);
			var objSettings = objItem.data("objsettings");

			objSettings.destroy();

			objItem.find(".unite-repeater-item-title").off("click");
			objItem.find(".unite-repeater-item-delete").off("click");
			objItem.find(".unite-repeater-item-duplicate").off("click");
		});
	}

	/**
	 * get repeater values
	 */
	function getRepeaterValues(objWrapper) {
		var values = [];

		objWrapper.find(".unite-repeater-item").each(function () {
			var itemValues = jQuery(this).data("objsettings").getSettingsValues();

			values.push(itemValues);
		});

		return values;
	}

	/**
	 * set repeater values
	 */
	function setRepeaterValues(objWrapper, values, useDefault) {
		objWrapper.find(".unite-repeater-items").empty();

		if (useDefault === true)
			values = objWrapper.data("itemvalues");

		if (!values)
			return;

		if (jQuery.isArray(values) === false)
			return;

		jQuery.each(values, function (index, itemValues) {
			addRepeaterItem(null, objWrapper, itemValues);
		});
	}


	function _______SWITCHER_____(){}

	/**
	 * init switcher
	 */
	function initSwitcher(objWrapper, funcChange) {
		objWrapper.on("click", function () {
			objWrapper.toggleClass("uc-checked");

			funcChange(null, objWrapper);
		});
	}

	/**
	 * get switcher value
	 */
	function getSwitcherValue(objWrapper) {
		var checkedValue = objWrapper.data("checkedvalue");
		var uncheckedValue = objWrapper.data("uncheckedvalue");

		checkedValue = g_ucAdmin.boolToStr(checkedValue);
		uncheckedValue = g_ucAdmin.boolToStr(uncheckedValue);

		return objWrapper.hasClass("uc-checked") ? checkedValue : uncheckedValue;
	}

	/**
	 * set switcher value
	 */
	function setSwitcherValue(objWrapper, value) {
		var checkedValue = objWrapper.data("checkedvalue");

		checkedValue = g_ucAdmin.boolToStr(checkedValue);
		value = g_ucAdmin.boolToStr(value);

		objWrapper.data("value", value).toggleClass("uc-checked", value === checkedValue);
	}


	/**
	 * clear switcher value
	 */
	function clearSwitcherValue(objSwitcher, defaultValue) {
		setSwitcherValue(objSwitcher, defaultValue);
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
	 * on control setting change
	 */
	function onControlSettingChange(event, input) {
		var debugControls = false;

		if (!input)
			input = this;

		var objInput = jQuery(input);
		var controlID = getInputName(objInput);

		if (!controlID)
			return;

		if (!g_arrControls[controlID])
			return;

		var controlValue = getSettingInputValue(objInput);
		var arrChildControls = g_arrControls[controlID];

		g_temp.cacheValues = null;

		if (debugControls === true) {
			trace("controls change");
			trace("parent value: " + controlValue);
			trace(controlValue);
		}

		var objParent = {
			id: controlID,
			value: controlValue,
		};

		jQuery.each(arrChildControls, function (childName, objControl) {
			var isSap = g_ucAdmin.getVal(objControl, "forsap");
			var rowID;
			var objChildInput = null;

			if (isSap === true) {	//sap
				rowID = g_IDPrefix + "ucsap_" + childName;
			} else { //setting
				rowID = g_IDPrefix + childName + "_row";
				objChildInput = jQuery(g_IDPrefix + childName);
			}

			var objChildRow = jQuery(rowID);

			if (objChildRow.length === 0) {
				if (debugControls === true)
					trace("child not found: " + rowID);

				return;
			}

			var value = objControl.value;

			objControl.idChild = childName;

			// check multiple parents
			var arrParents = g_ucAdmin.getVal(g_arrChildrenControls, childName);
			var action;

			if (arrParents)
				action = getControlActionMultiple(objParent, objControl, arrParents);
			else
				action = getControlAction(objParent, objControl);

			if (debugControls === true) {
				trace("setting: " + childName + " | value: " + value + " | action: " + action);
			}

			var isChildRadio = false;
			var isChildColor = false;

			if (objChildInput && objChildInput.length > 0) {
				var inputTagName = objChildInput.get(0).tagName;

				isChildRadio = inputTagName === "SPAN" && objChildInput.hasClass("unite-radio-wrapper");
				isChildColor = objChildInput.hasClass("unite-color-picker");
			}

			switch (objControl.type) {
				case "enable":
				case "disable":
					var isDisable = (action === "disable");

					objChildRow.toggleClass("setting-disabled", isDisable);

					if (!objChildInput)
						return;

					objChildInput.prop("disabled", isDisable);

					if (isChildRadio === true) {
						objChildInput
							.children("input")
							.prop("disabled", isDisable)
							.toggleClass("disabled", isDisable);
					} else if (isChildColor === true) {
						if (g_temp.colorPickerType === "spectrum")
							objChildInput.spectrum(isDisable ? "disable" : "enable");

						if (isDisable === false && g_colorPicker)
							g_colorPicker.linkTo(objChildInput);
					}
				break;
				case "show":
				case "hide":
					var isShow = (action === "show");
					var isHidden = objChildRow.hasClass("unite-setting-hidden");

					objChildRow.toggleClass("unite-setting-hidden", !isShow);

					if (!objChildInput)
						return;

					jQuery.each(objChildInput, function () {
						var objInput = jQuery(this);
						var value = getSettingInputValue(objInput);

						if (isShow === true && isHidden === true) {
							value = objInput.data("previous-value") || value;

							setInputValue(objInput, value);
							applyControls(objInput);

							return;
						}

						if (isShow === false && isHidden === false) {
							objInput.data("previous-value", value);

							clearInput(objInput);
							applyControls(objInput);
						}
					});
				break;
			}
		});
	}


	/**
	 * apply controls if available
	 */
	function applyControls(objInputs){
		objInputs.filter("select").trigger("change");
		objInputs.filter("input[type='radio']:checked").trigger("click");
	}


	function _______RESPONSIVE_PICKER_____(){}

	/**
	 * init responsive picker
	 */
	function initResponsivePicker() {
		g_objWrapper.find(".unite-responsive-picker").each(function () {
			var objPicker = jQuery(this);

			objPicker.on("change", function () {
				var id = objPicker.closest(".unite-setting-row").data("responsive-id");
				var device = objPicker.val();

				var objRow = objPicker.closest(".unite-list-settings")
					.find(".unite-setting-row[data-responsive-id=\"" + id + "\"]")
					.addClass("unite-responsive-hidden")
					.filter("[data-responsive-device=\"" + device + "\"]")
					.removeClass("unite-responsive-hidden");

				objRow.find(".unite-responsive-picker")
					.val(device)
					.trigger("change.select2");
			});

			if (objPicker.val() === "desktop")
				objPicker.trigger("change");

			initSelect2(objPicker, {
				dropdownParent: objPicker.parent(),
			});
		});
	}

	/**
	 * get responsive picker value for element
	 */
	function getResponsivePickerValue(objElement) {
		return objElement.closest(".unite-setting-row").data("responsive-device") || "desktop";
	}


	function _______UNITS_PICKER_____(){}

	/**
	 * init units picker
	 */
	function initUnitsPicker() {

		g_objWrapper.find(".unite-units-picker").each(function () {
			var objPicker = jQuery(this);

			initSelect2(objPicker, {
				dropdownParent: objPicker.parent(),
			});

			objPicker.on("change", function () {
				var value = objPicker.val();
				var onChange = objPicker.data("on_change");

				if (typeof onChange === "function")
					onChange(value);
			});
		});
	}

	/**
	 * get units picker for element
	 */
	function getUnitsPickerForElement(objElement) {
		return objElement.closest(".unite-setting-row").find(".unite-units-picker");
	}

	/**
	 * get units picker value for element
	 */
	function getUnitsPickerValue(objElement) {
		return getUnitsPickerForElement(objElement).val() || "px";
	}

	/**
	 * set units picker value for element
	 */
	function setUnitsPickerValue(objElement, value) {
		getUnitsPickerForElement(objElement).val(value).trigger("change.select2");
	}

	/**
	 * set units picker change handler
	 */
	function setUnitsPickerChangeHandler(objElement, handler) {
		getUnitsPickerForElement(objElement).data("on_change", handler);
	}


	function _________SELECTORS__________(){}

	/**
	 * get selectors includes
	 */
	this.getSelectorsIncludes = function () {
		var objInputs = getObjInputs();
		var includes = {};

		jQuery.each(objInputs, function () {
			var objInput = jQuery(this);
			var type = getInputType(objInput);
			var inputIncludes = {};

			switch (type) {
				case "typography":
					inputIncludes = getTypographySelectorIncludes(objInput);
				break;
			}

			if (inputIncludes)
				jQuery.extend(includes, inputIncludes);
		});

		return includes;
	}

	/**
	 * get selectors css
	 */
	this.getSelectorsCss = function () {
		var objInputs = getObjInputs();
		var css = "";

		jQuery.each(objInputs, function () {
			var objInput = jQuery(this);
			var inputCss = getInputSelectorCss(objInput);

			if (inputCss)
				css += inputCss;
		});

		return css;
	}

	/**
	 * check if the input has selector
	 */
	function isInputHasSelector(objInput) {
		var groupSelector = objInput.data("group-selector");

		if (groupSelector)
			return true;

		var selectors = objInput.data("selectors");

		if (selectors)
			return true;

		return false;
	}

	/**
	 * process selector replaces
	 */
	function processSelectorReplaces(css, replaces) {
		jQuery.each(replaces, function (placeholder, replace) {
			if (typeof replace === "string" || typeof replace === "number")
				css = g_ucAdmin.replaceAll(css, placeholder, replace);
		});

		return css;
	}

	/**
	 * get input selector css
	 */
	function getInputSelectorCss(objInput) {
		var groupSelector = objInput.data("group-selector");

		if (groupSelector)
			return; // skip individual input and process the group at once

		var selectors = objInput.data("selectors");

		if (!selectors)
			return;

		var selector = g_ucAdmin.getVal(selectors, "selector");
		var selectorCss = g_ucAdmin.getVal(selectors, "selector_value");

		if (!selector) {
			// get last selector
			var lastSelectorVal = Object.values(selectors).pop();

			jQuery.each(selectors, function (selectorNum, selectorVal) {
				//if last value, then no comma after value
				if (selectorVal === lastSelectorVal)
					selector += selectorVal;
				else
					selector += selectorVal + ",";
			});
		}

		var type = getInputType(objInput);

		switch (type) {
			case "group_selector":
				selectorCss = getGroupSelectorCss(objInput, selectorCss);
			break;
			case "dimentions":
				selectorCss = getDimentionsSelectorCss(objInput, selectorCss);
			break;
			case "typography":
			case "textshadow":
			case "boxshadow":
			case "css_filters":
				selectorCss = getSubSettingsSelectorCss(objInput, selector);
			break;
			default:
				selectorCss = replaceInputSelectorPlaceholders(objInput, selectorCss);
			break;
		}

		if (!selectorCss)
			return;

		var css = selector + "{" + selectorCss + "}";

		switch (type) {
			case "typography":
			case "textshadow":
			case "boxshadow":
			case "css_filters":
				css = selectorCss;
			break;
		}

		var device = getResponsivePickerValue(objInput);

		switch (device) {
			case "tablet":
				css = "@media(max-width:1024px){" + css + "}";
			break;
			case "mobile":
				css = "@media(max-width:768px){" + css + "}";
			break;
		}

		return css;
	}

	/**
	 * replace input selector placeholders
	 */
	function replaceInputSelectorPlaceholders(objInput, css) {
		var type = getInputType(objInput);
		var value = getSettingInputValue(objInput);

		if (!value)
			return;

		var unit = "px";
		var size;

		switch (type) {
			case "range":
				size = value.size;
				unit = value.unit;
				value = size + unit;

				if (!size)
					return;
			break;
		}

		css = processSelectorReplaces(css, {
			"{{VALUE}}": value,
			"{{SIZE}}": size || value,
			"{{UNIT}}": unit,
		});

		return css;
	}

	/**
	 * get group selector css
	 */
	function getGroupSelectorCss(objWrapper, css) {
		var selectorReplace = objWrapper.data("replace");
		var replaces = {};

		for (var selectorPlaceholder in selectorReplace) {
			var inputName = selectorReplace[selectorPlaceholder];
			var objInput = t.getInputByName(inputName);
			var replace = replaceInputSelectorPlaceholders(objInput, "{{VALUE}}");

			if (replace)
				replaces[selectorPlaceholder] = replace;
		}

		// check if all inputs have a value
		if (Object.values(selectorReplace).length !== Object.values(replaces).length)
			return;

		css = processSelectorReplaces(css, replaces);

		return css;
	}


	function _______EVENTS_____(){}

	/**
	 * update events (in case of ajax set)
	 */
	this.updateEvents = function(){

		initSettingsEvents();
		initTipsy();

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
		if(t.isTriggerChangeDisabled() === true)
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
	function initControls() {
		if (!g_objWrapper)
			return;

		var objControls = g_objWrapper.data("controls");

		if (!objControls)
			return;

		g_objWrapper.removeAttr("data-controls");

		g_arrControls = objControls.parents;
		g_arrChildrenControls = objControls.children;

		var objInputs = getObjInputs();

		objInputs.filter("select").on("change", onControlSettingChange);
		objInputs.filter("input[type='radio'], .unite-setting-switcher").on("click", onControlSettingChange);
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

		if(t.isTriggerChangeDisabled() === true)
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
				initIconPicker(objInput, funcChange);
			break;
			case "dimentions":
				initDimentions(objInput, funcChange);
			break;
			case "range":
				initRangeSlider(objInput, funcChange);
			break;
			case "switcher":
				initSwitcher(objInput, funcChange);
			break;
			case "tabs":
				initTabs(objInput);
			break;
			case "typography":
			case "textshadow":
			case "boxshadow":
			case "css_filters":
				initSubSettings(objInput, funcChange);
			break;
			case "addon":
				initAddonPicker(objInput);
			break;
			case "repeater":
				initRepeater(objInput, funcChange);
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
			case "link":
				initAddFieldsEvents(objInput);
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


		if(g_temp.isRepeaterExists)
			destroyRepeaters();

		destroyTabs();
		destroyDimentions();
		destroyIconPickers();
		destroyRangeSlider();
		destroySubSettings();

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

		g_temp.disableExcludeSelector =	g_ucAdmin.getVal(options, "disable_exclude_selector");

		t.disableTriggerChange();

		validateInited();

		initOptions();

		initColorPicker();	//put the color picker automatically

		initAnimationsSelector();

		initItemsPanel();

		initRepeaters();

		initResponsivePicker();

		initUnitsPicker();

		initGlobalEvents();

		t.updateEvents();

		initSaps();

		t.clearSettingsInit();

		g_objProvider.initEditors(t);

		g_temp.isInited = true;

		t.enableTriggerChange();

	};


} // UniteSettings class end
