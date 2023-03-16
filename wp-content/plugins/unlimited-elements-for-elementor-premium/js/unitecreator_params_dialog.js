"use strict";

function UniteCreatorParamsDialog(){
	
	var t = this;
	var g_objWrapper, g_objSelectType, g_objTabContentWrapper, g_objLeftArea, g_objRightArea;
	var g_objError, g_objParamTitle, g_objParamName;
	var g_objTexts, g_objParent, g_objData, g_objSettings, g_currentOpenedType, g_currentOpenedName;
	
	var g_arrSpecialInputs = {};	//array of special inputs
	
	var events = {
			CHANGE_NAME: "name_change",
			OPEN: "open",
			INIT: "init"
	};
	
	
	if(!g_ucAdmin){		//for autocomplete
		var g_ucAdmin = new UniteAdminUC();
	};
	
	
	function ____________GETTERS____________(){};
	
	/**
	 * get all param types array
	 */
	function getArrParamTypes(){
		var arrTypes = [];
		
		var options = g_objSelectType.find("option");
		
		jQuery.each(options, function(index, option){
			var type = jQuery(option).data("type");
			arrTypes.push(type);
		});
		
		
		return(arrTypes);
	}
	
	
	/**
	 * get all inputs
	 */
	function getAllInputs(){
		
		var objInputs = g_objWrapper.find("input,textarea,select,.uc-table-dropdown-items").not(".unite_table_settings_wide").not(".uc-not-input");
		
		return(objInputs);
	}
	
	/**
	 * get current right content
	 */
	function getCurrentRightContent(){

		var objContent = g_objRightArea.find(".uc-content-selected");
		if(objContent.length == 0)
			throw new Error("no current content found");
		
		if(objContent.length > 1)
			throw new Error("multiple selected contents found");
		
		return(objContent);
	}
	
	
	/**
	 * get inputs of params dialog
	 */
	function getCurrentInputs(){
		
		var selector = "input,textarea,select,.uc-table-dropdown-items";
		
		var objCurrentContent = getCurrentRightContent();
		
		var objInputsLeft = g_objLeftArea.find(selector).not(".unite_table_settings_wide").not(".uc-not-input");
		var objInputsRight =  objCurrentContent.find(selector).not(".unite_table_settings_wide").not(".uc-not-input");
		
		var objInputs = objInputsLeft.add(objInputsRight);
		
		return objInputs;
	}
	
	/**
	 * get input name
	 */
	function getInputName(objInput){
		
		var paramName = objInput.prop("name");
		if(!paramName)
			paramName = objInput.data("name");
		
		return(paramName);
	}
	
	
	/**
	 * get content of params dialog
	 */
	function getParamDialogContent(){
		
		var arrInputs = getCurrentInputs();
		
		var objParam = {};
		
		var selectedTab = g_objSelectType.find("option:selected");
		
		if(selectedTab.length == 0)
			throw new Error("No param tab selected");
		
		objParam.type = selectedTab.data("type");
		
		//set inputs data
		jQuery.each(arrInputs, function(index, input){
			
			var objInput = jQuery(input);
			var inputType = getInputType(objInput);
						
			var paramName = getInputName(objInput);
			
			var hasName = true;
			if(paramName == undefined || paramName == "")
				hasName = false;
			
			var isSpecialType = g_arrSpecialInputs.hasOwnProperty(inputType);
			
			//regular items
			if(hasName == true && isSpecialType == false){
				
				switch(inputType){
					case "text":
					case "textarea":
						var paramValue = objInput.val();
						objParam[paramName] = paramValue;
					break;
					case "radio":
						var isChecked = objInput.is(":checked");
						if(isChecked == true){
							var paramValue = objInput.val();
							objParam[paramName] = paramValue;
						}
					break;
					case "select":
						var paramValue = objInput.val();
						objParam[paramName] = paramValue;
					break;
					case "checkbox":
						var paramValue = objInput.is(":checked");
						objParam[paramName] = paramValue;
					break;
					default:
						throw new Error("Unknown input type: " + inputType);
					break;
					
				}//end switch
					
			}else{		//special items
				
				var isSimpleType = g_ucAdmin.isSimpleInputType(inputType);
				
				//special type get function
				if(isSimpleType == false){
									
					if(g_arrSpecialInputs.hasOwnProperty(inputType) == false)
						throw new Error("The input type: "+ inputType + " should have get function");
					
					//get special input data
					
					var objSpecialInput = g_arrSpecialInputs[inputType];
					var objSpecialData = objSpecialInput.onGetInputData(objInput);
					
					if(typeof objSpecialData != "object")
						throw new Error("The special param get function should return object: "+inputType);
					
					objParam = jQuery.extend(objParam, objSpecialData);
					
				}
				
			}
			
		});
		
		
		return(objParam);
	}
	
	
	function ____________GENERAL____________(){};
	
	/**
	 * get input type
	 */
	function getInputType(objInput){
		
		var inputType = g_ucAdmin.getInputType(objInput);
		
		if(objInput.hasClass("select2-hidden-accessible"))
			inputType = "select2";
		
		return(inputType);
	}
	
	
	/**
	 * clear params dialog
	 */
	function clearParamDialog(){
				
		var objInputs = getAllInputs(true);
		
		//clear simple inputs
		objInputs.each(function(index, input){
			var objInput = jQuery(input);
			var initval = objInput.data("initval");
			var attrName = objInput.attr("name");
			
			var inputType = getInputType(objInput);
			
			switch(inputType){
				case "text":
				case "textarea":
					if(initval !== undefined)
						objInput.val(initval);
					else
						objInput.val("");
					
					//check color picker
					if(objInput.hasClass("uc-text-colorpicker"))
						objInput.trigger("keyup");
					objInput.trigger("change");
				break;
				case "radio":
					var objRadioWrapper = objInput.parents(".uc-radioset-wrapper");
					
					if(objRadioWrapper.length == 0)
						throw new Error("Every radio should have a .uc-radioset-wrapper");
					
					var defaultItemChecked = objRadioWrapper.data("defaultchecked");
					var inputValue = objInput.val();
					
					defaultItemChecked = g_ucAdmin.boolToStr(defaultItemChecked);
					
					if(inputValue == defaultItemChecked)
						objInput.trigger("click");
				break;
				case "select":
					var initValue = objInput.data("initval");
					if(initValue != undefined)
						objInput.val(initValue)
					
					//for select unit
					if(objInput.hasClass("uc-select-unit"))
						objInput.siblings(".uc-text-unit-custom").hide();
					
					objInput.trigger("change");
				break;
				case "checkbox":
					var defaultChecked = objInput.data("defaultchecked");
					defaultChecked = g_ucAdmin.strToBool(defaultChecked);
					objInput.prop("checked", defaultChecked);
					objInput.trigger("change");
				break;
			}
			
			
		});
		
		
		//clear special params
		objInputs.each(function(index, input){
			var objInput = jQuery(input);
			var inputType = getInputType(objInput);
			
			var isSimpleType = g_ucAdmin.isSimpleInputType(inputType);
			if(isSimpleType == true)
				return(true);
			
			if(g_arrSpecialInputs.hasOwnProperty(inputType) == false)
				throw new Error("The input type: "+ inputType + " not found");
			
			var objSpecialInput = g_arrSpecialInputs[inputType];
			
			if(typeof objSpecialInput.onClearInputData == "function")
				objSpecialInput.onClearInputData(objInput);
			
		});
		
	}
	
	
	/**
	 * fill params dialog
	 */
	function fillParamsDialog(objData){
		
		clearParamDialog();
		
		selectParamDialogTabByType(objData.type);
		
		var objInputs = getCurrentInputs();
		
		//fill simple type the inputs
		jQuery.each(objInputs, function(index, input){
			
			var objInput = jQuery(input);
			var inputName = getInputName(objInput);
			var inputType = getInputType(objInput);
						
			var isSimpleType = g_ucAdmin.isSimpleInputType(inputType);
			
			if(isSimpleType == false)
				return(true);
			
			if(objData.hasOwnProperty(inputName) == false)
				return(true);
				
			var value = objData[inputName];
						
			switch(inputType){
				case "text":
				case "textarea":
					objInput.val(value);
					objInput.trigger("change");
				break;
				case "radio":
					var radioValue = objInput.val();
					if(radioValue == value)
						objInput.trigger("click");
				break;
				case "select":
					objInput.val(value);
					if(objInput.hasClass("uc-select-unit") && value == "other")
						objInput.siblings(".uc-text-unit-custom").show();
					objInput.trigger("change");
				break;
				case "checkbox":
					
					value = g_ucAdmin.strToBool(value);
					objInput.prop("checked", value);
					objInput.trigger("change");
				break;
			}
			
		});
		
		
		//fill special type inputs
		jQuery.each(objInputs, function(index, input){
			
			var objInput = jQuery(input);
			var inputType = getInputType(objInput);
					
			var isSimpleType = g_ucAdmin.isSimpleInputType(inputType);
			if(isSimpleType == true)
				return(true);
						
			if(g_arrSpecialInputs.hasOwnProperty(inputType) == false)
				throw new Error("The input type: "+ inputType + " not found");
			
			var objSpecialInput = g_arrSpecialInputs[inputType];
			
			if(typeof objSpecialInput.onFillInputData == "function")
				objSpecialInput.onFillInputData(objInput, objData);
			
		});
		
		
		//limit edit
		if(objData.hasOwnProperty("limited_edit") && g_ucAdmin.strToBool(objData.limited_edit) == true)
			limitDialog();
		else
			unlimitDialog();
		
	}
	
	
	/**
	 * validate the param dialog
	 */
	function validateParamDialog(objParam){
		
		try{
			if(objParam.hasOwnProperty("title"))
				g_ucAdmin.validateNotEmpty(objParam.title, "Title");
			
			g_ucAdmin.validateNotEmpty(objParam.name, "Name");
			
			g_ucAdmin.validateNameField(objParam.name, "Name");
			
		}catch(error){
			g_objError.show().html(error.message);
			return(false);
		}
		
		return(true);
	}
	
	
	
	/**
	 * open addon add/edit dialog
	 * paramType - main / items
	 */
	this.open = function(objData, rowIndex, onActionFunc, dialogType){
		
		var isEdit = false;
		if(objData)
			isEdit = true;
		
		var actionTitle = g_objTexts.add_button;
		var dialogTitle = g_objTexts.add_title;
		var paramType = null;
		
		g_currentOpenedType = dialogType;
		g_currentOpenedName = null;
				
		if(isEdit == true){
			
			g_currentOpenedName = objData.name;
			
			actionTitle = g_objTexts.update_button;
			
			var paramTitle = objData.name;
			if(typeof objData.title != "undefined")
				var paramTitle = objData.title;
			
			dialogTitle = g_objTexts.edit_title + ": " + paramTitle;
			
			paramType = objData.type;
		}
		
		//show/hide admin label
		var objAdminLabel = g_objWrapper.find(".uc-dialog-param-admin-label-wrapper");
		if(objAdminLabel.length){
			if(dialogType == "main")
				objAdminLabel.show();
			else
				objAdminLabel.hide();
		}
				
		//set wrapper type class
		if(dialogType == "items"){
			g_objWrapper.addClass("uc-param-type-item");
		}
		else
			g_objWrapper.removeClass("uc-param-type-item");
			
		
		//filter select params by type
		var buttonOpts = {};
		
		//---- cancel click
		
		buttonOpts["Cancel"] = function(){
			g_objWrapper.dialog("close");
		};
		
		//---- action click
		
		buttonOpts[actionTitle] = function(){
			var objParam = getParamDialogContent();
			
			if(typeof onActionFunc != "function")
				throw new Error("on add/edit function not passed");
			
			g_objError.hide();
			
			if(validateParamDialog(objParam) == false)
				return(false);
			
			if(isEdit == false)
				onActionFunc(objParam);		//add function
			else{
				var rowIndex = g_objWrapper.data("rowindex");
				onActionFunc(objParam, rowIndex);		//edit function
			}
			
			g_objWrapper.dialog("close");
		};
		
		//hide error
		g_objError.hide();
		
		//unlimit dialog before open
		if(isEdit == false){		
			unlimitDialog();
		}
		
		
		g_objWrapper.dialog({
			dialogClass:"unite-ui unite-dialog-responsive",
			buttons:buttonOpts,
			minWidth:1020,
			title: dialogTitle,
			modal:true,
			open:function(){
								
				if(isEdit == false){
					clearParamDialog();
					g_objData = null;
				}
				else{
					g_objWrapper.data("rowindex", rowIndex);
					g_objData = objData;
					
					fillParamsDialog(objData);
				}
				
				//focus only if empty
				if(isEdit == false){
					if(g_objParamTitle.length)
						g_objParamTitle.focus();
					else
						g_objParamName.focus();
				}
				
				triggerEvent(events.OPEN);
				
			}
		});
		
	}
	
	
	/**
	 * select param dialog tab by type
	 */
	function selectParamDialogTabByType(type){
			
		var options = g_objSelectType.find("option");
		
		options.each(function(index, option){
			var objOption = jQuery(option);
			var tabType = objOption.data("type");
			
			if(tabType == type){
				var optionValue = objOption.val();
				g_objSelectType.val(optionValue);
				g_objSelectType.trigger("change");
				
				return(false);
			}
			
		});
		
	}
	
	
	
	function ____________DROPDOWN_PARAM____________(){};

	
	/**
	 * add row to dropdown param
	 */
	function dropdownParamAddRow(objTable, objRowBefore, objData){
		
		var html = "";
		
		var valueName = "";
		var valueValue = "";
		var selectedClass = "";
		
		if(objData){
			
			if(objData.name)
				valueName = objData.name;
			
			if(objData.value)
				valueValue = objData.value;
			
			if(objData.isDefault == true)
				selectedClass = " uc-selected";
		}
		
		valueValue = g_ucAdmin.htmlspecialchars(valueValue);
				
		html += "<tr>";
		html += "<td><div class='uc-dropdown-item-handle'></div></td>";
		html += "<td><input type=\"text\" value=\""+valueName+"\" class='uc-dropdown-item-name'></td>";
		html += "<td><input type=\"text\" value=\""+valueValue+"\" class='uc-dropdown-item-value'></td>";
		html += "<td>";
		html += "<div class='uc-dropdown-icon uc-dropdown-item-delete' title=\"Delete Item\"></div>";
		html += "<div class='uc-dropdown-icon uc-dropdown-item-add' title=\"Add Item\"></div>";
		html += "<div class='uc-dropdown-icon uc-dropdown-item-default"+selectedClass+"' title=\"Default Item\"></div>";
		html += "</td>";
		html += "</tr>";
		
		var objNewRow = jQuery(html);
		
		if(!objRowBefore)
			objTable.children("tbody").append(objNewRow);
		else
			objNewRow.insertAfter(objRowBefore);
		
		return(objNewRow);
	}
	
	
	/**
	 * get num, items of dropdown param
	 */
	function dropdownParamGetNumItems(objTable){
		
		var rows = objTable.find("tbody tr");
	
		return(rows.length);
	}
	
	
	/**
	 * get dropdown param data
	 */
	function getDropdownParamData(objTable){
		
		var isMultiple = objTable.data("ismultiple");
				
		var rows = objTable.find(" tbody tr");
		
		var objOptions = {};
		if(isMultiple === true)
			var defaultOption = [];
		else
			var defaultOption = "";
			
		
		jQuery.each(rows, function(index, row){
			var objRow = jQuery(row);
			
			var optionName = objRow.find(".uc-dropdown-item-name").val();
			var optionValue = objRow.find(".uc-dropdown-item-value").val();
			var isDefault = objRow.find(".uc-dropdown-item-default").hasClass("uc-selected");
			
			optionName = jQuery.trim(optionName);
			optionValue = jQuery.trim(optionValue);
			
			if(optionName == "")
				return(true);
			
			//set default option
			if(isMultiple !== true){
				if(defaultOption == "")
					defaultOption = optionValue;
				
				if(isDefault == true)
					defaultOption = optionValue;
			}else{		//multiple
				
				if(isDefault == true)
					defaultOption.push(optionValue);
				
			}
			
			objOptions[optionName] = optionValue;
			
		});
		
		var objOutput = {
			options: objOptions,
			default_value: defaultOption
		};
		
		return(objOutput);
	}
	
	
	/**
	 * clear dropdown param
	 */
	function clearDropdownParam(objTable, leaveOneRow){
		
		if(objTable.length == 0)
			throw new Error("dropdown parameter not found");
		
		objTable.children("tbody").html("");
		
		if(leaveOneRow === true)
			dropdownParamAddRow(objTable, null,{isDefault:true});
	}
	
	
	/**
	 * fill dropdown param options
	 */
	function fillDropdownParamOptions(objTable, options, defaultValue){
		
		if(objTable.length == 0)
			throw new Error("dropdown parameter not found");
		
		clearDropdownParam(objTable);
		
		if(jQuery.isEmptyObject(options))
			dropdownParamAddRow(objTable);
		else{
			
			jQuery.each(options, function(optionName, optionValue){
				
				if(jQuery.isArray(defaultValue)){
					var isDefault = (jQuery.inArray(optionValue, defaultValue) !== -1);
				}else{
					var isDefault = (optionValue == defaultValue);
				}
				
				dropdownParamAddRow(objTable, null, {name: optionName, value: optionValue , isDefault:isDefault});
			});
		}
		
	}
	
	
	/**
	 * set default first item
	 */
	function dropdownSetDefaultFirstItem(objTable){
		var firstRow = objTable.find("tbody tr:first-child");
		
		if(firstRow.length == 0)
			return(false);
		
		dropdownSetDefaultRow(objTable, firstRow);
	}
	
	
	/**
	 * set default row
	 */
	function dropdownSetDefaultRow(objTable, objRow){
		var objRowDefault = objRow.find(".uc-dropdown-item-default");
		objTable.find("tbody tr .uc-dropdown-item-default").not(objRow).removeClass("uc-selected");
		objRowDefault.addClass("uc-selected");
	}
	
	
	/**
	 * set default item by value
	 */
	function dropdownSetDefaultItem(objTable, defaultValue){
		
		var objRows = objTable.find("tbody tr");
				
		objRows.each(function(index, row){
			
			var objRow = jQuery(row);
			
			var optionValue = objRow.find(".uc-dropdown-item-value").val();
			var isDefault = objRow.find(".uc-dropdown-item-default").hasClass("uc-selected");
			
			optionValue = jQuery.trim(optionValue);
			
			if(optionValue == defaultValue && isDefault == false){
				dropdownSetDefaultRow(objTable, objRow);
				return(false);
			}
			
		});
		
	}
	
	
	/**
	 * on dropdown init
	 */
	function dropdownOnInit(objDialogWrapper){
		
		var objTableDropdown = objDialogWrapper.find(".uc-table-dropdown-items");
		if(objTableDropdown.length == 0)
			throw new Error("The table dropdown should be not empty");

		//sortable:
		objTableDropdown.children("tbody").sortable({
			handle: ".uc-dropdown-item-handle"
		});
		
		//add row button
		objTableDropdown.on("click", ".uc-dropdown-item-add", function(){
			var objTable = jQuery(this).parents(".uc-table-dropdown-items");
			var objRow = jQuery(this).parents("tr");
			
			var objNewRow = dropdownParamAddRow(objTable, objRow);
			
			objNewRow.find(".uc-dropdown-item-name").focus();
		});
		
		
		//delete row button
		objTableDropdown.on("click", ".uc-dropdown-item-delete", function(){
			var objRow = jQuery(this).parents("tr");
			var objTable = jQuery(this).parents("table");
			
			//if the row is default, select first remaining row
			var isDefault = objRow.find(".uc-dropdown-item-default").hasClass("uc-selected");
						
			objRow.remove();
			
			var numItems = dropdownParamGetNumItems(objTable);
			if(numItems == 0){
				objNewRow = dropdownParamAddRow(objTable);
				objNewRow.find(".uc-dropdown-item-name").focus();
			}

			if(isDefault || numItems == 0)
				objTable.find("tbody tr:first-child .uc-dropdown-item-default").addClass("uc-selected");
			
		});

		//default icon click
		objTableDropdown.on("click", ".uc-dropdown-item-default", function(){
						
			var objIcon = jQuery(this);
			var objParentTable = objIcon.parents(".uc-table-dropdown-items");
			
			var isMultiple = objParentTable.data("ismultiple");
			
			if(isMultiple === true){		//multiple trigger - just toggle the class
				
				objIcon.toggleClass("uc-selected");
				return(true);
			}
			
			if(objIcon.hasClass("uc-selected"))
				return(false);
			
			objParentTable.find(".uc-dropdown-item-default").removeClass("uc-selected");
			
			objIcon.addClass("uc-selected");
			
						
		});
		
		
	}
	
	
	/**
	 * init edit dialog dropdown param
	 */
	function initDropdownParam(){
		
		//validation if dropdown instance exists
		var objTableDropdown = g_objWrapper.find(".uc-table-dropdown-items");
		if(objTableDropdown.length == 0)
			return(false);
		
		var objDropdownParam = {};
		objDropdownParam.onInitDialog = dropdownOnInit;
		
		//clear
		objDropdownParam.onClearInputData = function(objInput){
			clearDropdownParam(objInput, true);
		}
		
		//get
		objDropdownParam.onGetInputData = function(objInput){
			
			var objParamData = getDropdownParamData(objInput);
						
			return(objParamData);
		}
		
		//fill
		objDropdownParam.onFillInputData = function(objTable, objData){
							
				fillDropdownParamOptions(objTable, objData.options, objData.default_value);
			
		}
		
		
		t.addSpecialInput("table_dropdown", objDropdownParam);
		
		//--------- get values from select related
				
	}
	
	
	/**
	 * init edit dialog select2 param
	 */
	function initSelect2Param(){
				
		var objSelect2Param = {};
		
		//clear
		objSelect2Param.onClearInputData = function(objInput){
			objInput.val("");
		}
		
		//get
		objSelect2Param.onGetInputData = function(objInput){
			
			var value = objInput.select2("val");
			var name = objInput.data("name");
			
			//find the text
			var option = objInput.find(':selected');
			var text = option.text();
			
			var objData = {};
			objData[name] = value;
			
			objData[name+"_text"] = text;
			
			return(objData);
		}
		
		
		//fill
		objSelect2Param.onFillInputData = function(objInput, objData){
			
			var name = objInput.data("name");
			var value = g_ucAdmin.getVal(objData, name);
									
			objInput.val(value);
			
			if(!value)
				return(false);
			
			//add option if not exists, if the value not exists in the select
			var currentValue = objInput.select2("val");
			
			if(!currentValue){
				
				var text = g_ucAdmin.getVal(objData, name + "_text");
				if(!text)
					text = "Example Post";
				
				//add option
				 var newOption = new Option(text, value, true, true);
				 objInput.append(newOption).trigger('change');
			}
			
		}
		
		
		t.addSpecialInput("select2", objSelect2Param);		
	}
	
	
	
	function ____________RADIOBOOLEAN_PARAM____________(){};

	
	/**
	 * init radio boolean param
	 */
	function initRadioBooleanParam(){
		
		var objTableDropdown = g_objWrapper.find(".uc-table-dropdown-items");
		if(objTableDropdown.length == 0)
			return(false);
		
		var objRadioBoolean = {};
		
		//clear
		objRadioBoolean.onClearInputData = function(objInput){
			dropdownSetDefaultFirstItem(objInput);
		}
		
		//get
		objRadioBoolean.onGetInputData = function(objInput){
			var radioBooleanData = getDropdownParamData(objInput);
			
			var returnData = {};
			returnData["default_value"] = radioBooleanData["default_value"];
			
			return(returnData);
			
		}
		
		//fill
		objRadioBoolean.onFillInputData = function(objInput, objData){
			
			if(objData.hasOwnProperty("default_value"))
				dropdownSetDefaultItem(objInput, objData.default_value);
			
		}
		
		
		t.addSpecialInput("radio_boolean", objRadioBoolean);
		
	}
	
	
	function ____________NUMBER_PARAM____________(){};

	
	/**
	 * init various params of params dialog
	 */
	function initNumberParam(){
		
		//init unit select:
		g_objWrapper.find(".uc-select-unit").change(function(){
			var objSelect = jQuery(this);
			var value = objSelect.val();
			var objText = objSelect.siblings(".uc-text-unit-custom"); 
			
			if(value == "other")
				objText.show().focus();
			else
				objText.hide();
			
		});
		
	}
	
	
	function ____________COLOR_PICKER_PARAM____________(){};
	
	/**
	 * init farbtastic color picker
	 */
	function initColorPicker_farbtastic(){
		
		var colorPicker = jQuery.unite_farbtastic('.unite-color-picker-element');
		colorPicker.linkTo(pickerInput);
		
		//on change
		pickerInput.change(function(){
			var objInput = jQuery(this);
			objInput.trigger("keyup");
		});
		
	}
	
	/**
	 * init the color picker element
	 */
	function initColorPicker(){
		
		var pickerInput = g_objWrapper.find(".uc-text-colorpicker");
		
		var colorPickerType = g_ucAdmin.getGeneralSetting("color_picker_type");
		switch(colorPickerType){
			case "farbtastic":
				initColorPicker_farbtastic(pickerInput);
			break;
			case "spectrum":
				pickerInput.spectrum({
					
				});
			break;
		}
			
	}
	
	function ____________IMAGE_PARAM____________(){};
	
	/**
	 * init image param
	 */
	function initImageParam(){
		
		//onchange name - update thumbs input fields
		onEvent(events.CHANGE_NAME, function(index, paramName){
			
			g_objWrapper.find(".uc-param-image-thumbname").each(function(index, input){
				var objInput = jQuery(input);
				var suffix = objInput.data("addsuffix");
				paramName = jQuery.trim(paramName);
				if(paramName){
					var value = paramName + "_" + suffix;
					objInput.val(value);
				}
				
			});
			
		});
		
	}
	
	
	function ____________IMAGE_SELECT_FIELD____________(){};
	
	
	/**
	 * update state of the image select field, to disabled or enabled
	 */
	function chooserField_updateState(objSettingWrapper, type){
		
		if(!g_objParent)
			return(false);
		
		var pathAssets = g_objParent.getPathAssets();
		
		var objInput = objSettingWrapper.find("input");
		
		var isEnabled = true;
		if(!pathAssets)
			isEnabled = false;
		
		switch(type){
			case "image":
				g_objSettings.updateImageFieldState(objInput, pathAssets);
			break;
			case "mp3":
				g_objSettings.updateMp3FieldState(objInput, pathAssets);
			break;
			default:
				throw new Error("Wrong choose field type");
			break;
		}
		
	}
	
	
	/**
	 * init image select field
	 */
	function initImageSelectField(){
		
		var objSettingImage = g_objWrapper.find(".unite-setting-image");
		if(objSettingImage.length == 0)
			return(false);
		
		var objSettings = new UniteSettingsUC();
		
		objSettings.initImageChooser(objSettingImage);
		
		/**
		 * on dialog open, fill selects
		 */
		onEvent(events.OPEN, function(){
			objSettingImage.each(function(){
					chooserField_updateState(jQuery(this),"image")
				});
		});
		
	}
	
	function ____________MP3_SELECT_FIELD____________(){};
	
		
	
	/**
	 * init image select field
	 */
	function initMp3SelectField(){
		
		var objSettingMp3 = g_objWrapper.find(".unite-setting-mp3");
		if(objSettingMp3.length == 0)
			return(false);
		
		var objSettings = new UniteSettingsUC();
		
		objSettings.initMp3Chooser(objSettingMp3);
				
		
		/**
		 * on dialog open, fill selects
		 */
		onEvent(events.OPEN, function(){
			objSettingMp3.each(function(){
					chooserField_updateState(jQuery(this),"mp3")
				});
		});
		
	}
	
	
	function ____________SELECT_PARAMS____________(){};

	
	/**
	 * get optons from every control param (dropdown, radio boolean, checkbox)
	 */
	function getControlParamOptions(param){
		
		switch(param.type){
			case "uc_dropdown":
				if(param.hasOwnProperty("options") == false)
					return(null);
				
				return(param.options);
			break;
			case "uc_checkbox":
				var options = {};
				options["true"] = "true";
				options["false"] = "false";
				return(options);
			break;
			case "uc_radioboolean":
				var options = {};
				options[param.true_name] = param.true_value;
				options[param.false_name] = param.false_value;
				return(options);
			break;
			default:
				throw new Error("Wrong control param type: " + param.type);
			break;
		}
		
	}
	
	
	/**
	 * fill select related table
	 */
	function fillSelectRelatedTable(objSelect, objTable){
		
		var value = objSelect.val();
		var objOption = objSelect.find("option:selected");
		
		var objTableBody = objTable.find("tbody");
		if(objTableBody.length == 0)
			throw new Error("Table body not found");
		
		objTableBody.html("");

		var options = objOption.data("options");
		if(!options)
			return(false);
		
		var dataOptions = {};
		if(g_objData && g_objData.hasOwnProperty("options"))
			dataOptions = g_objData.options;
						
		jQuery.each(options, function(name, value){
			
			var putValue = "";
			if(dataOptions.hasOwnProperty(value))
				putValue = dataOptions[value];
			
			var html = "";
			html += "<tr>";
			html += "	<td>";
			html += "		<input type='text' class='uc-item-value uc-dropdown-item-name' disabled value='"+value+"'>";
			html += "	</td>";
			html += "	<td>";
			html += "		<input type='text' class='uc-item-put-value uc-dropdown-item-value' value='"+putValue+"'>";
			html += "	</td>";
			html += "</tr>";
						
			objTableBody.append(html);
		});
				
	}

	
	/**
	 * get table select param data
	 */
	function getTableSelectRelatedData(objTable){

		var rows = objTable.find(" tbody tr");
		
		var objOptions = {};
		
		jQuery.each(rows, function(index, row){
			var objRow = jQuery(row);
			
			var value = objRow.find(".uc-item-value").val();
			var putValue = objRow.find(".uc-item-put-value").val();
			
			value = jQuery.trim(value);
			putValue = jQuery.trim(putValue);
			
			if(value == "")
				return(true);
					
			objOptions[value] = putValue;
			
		});
		
		var objOutput = {
			options: objOptions
		};
		
		return(objOutput);
		
	}
	
	
	/**
	 * init select related table
	 */
	function initTableSelectRelated(){
				
		var objTables = g_objWrapper.find(".uc-table-select-related");
		if(objTables.length == 0)
			return(false);
		
		var objSpecialInput = {};
		
		//init
		objSpecialInput.onInitDialog = function(){
			
			objTables.each(function(index, table){
				var objTable = jQuery(table);
				var relateToSelector = objTable.data("relateto");
				if(!relateToSelector)
					throw new Error("select table must have relate to data");
				
				var objTabContent = objTable.parents(".uc-tab-content");
				var objSelect = objTabContent.find(relateToSelector);
				
				if(objSelect.length == 0)
					throw new Error("Select with selector: "+relateToSelector+" not found");
				
				//fill table on change
				objSelect.change(function(){
					fillSelectRelatedTable(jQuery(this), objTable);
				});
				
			});
			
		}
		
		//clear - triggered on select change
		objSpecialInput.onClearInputData = null;
		
		//fill - trigered on select change
		objSpecialInput.onFillInputData = null;
		
		
		//get
		objSpecialInput.onGetInputData = getTableSelectRelatedData;
		
		t.addSpecialInput("table_select_related", objSpecialInput);
		
	}
	
	
	/**
	 * init param select
	 */
	function initSelectParams(){
		
		/**
		 * on dialog open, fill selects
		 * set params that control another params
		 */
		
		if(!g_objParent)
			return(false);
		
		onEvent(events.OPEN, function(){
			
			var objSelectParams = g_objWrapper.find(".uc-select-param");
			
			if(!g_objParent)
				throw new Error("on open event: parent not defined");
			
			var arrParams = g_objParent.getControlParams("main");
			var arrParamsItems = g_objParent.getControlParams("item");
			
			objSelectParams.each(function(index, select){
				var objSelect = jQuery(select);
				objSelect.html("");
				
				g_ucAdmin.addOptionToSelect(objSelect, "", "["+g_uctext.not_selected+"]");
				
				var source = objSelect.data("source");
				
				switch(source){
					case "main":
						var arrParamsToAdd = arrParams;
					break;
					case "item":
						var arrParamsToAdd = arrParamsItems;
					break;
					default:
						throw new Error("wrong select param source: "+source+", can be only main, item");
					break;
				}
								
				jQuery.each(arrParamsToAdd, function(index, param){
					
					var options = getControlParamOptions(param);
					
					g_ucAdmin.addOptionToSelect(objSelect, param.name, param.name, "options", options);
				});
				
			});
			
		});
		
		
	}
	
	function ____________CONDITIONS____________(){};
	
	/**
	 * on select change conditions attribute
	 * fill condition values select
	 */
	function onSelectConditionsAttributeChange(){
		
		var objSelect = jQuery(this);
		
		var paramName = objSelect.val();
				
		var objRow = objSelect.parents("tr");
		var objSelectValues = objRow.find(".uc-dialog-condition-value");
		
		var isInitValues = objSelect.data("init_value");
		objSelect.data("init_value",false);
		
		objSelectValues.html("");
		
		//get param
		var arrParams = g_objWrapper.data("condition_params");
				
		var objParam = g_ucAdmin.getVal(arrParams, paramName);
		
		//set or remove not selected mode
		if(!paramName || !objParam){
			objRow.addClass("uc-no-attribute-selected");
			return(false);
		}
		
		
		objRow.removeClass("uc-no-attribute-selected");
		
		//show condition 2
		if(objRow.hasClass("uc-row-condition2"))
			addMoreCondition();		
		
		//fill values select
		var paramType = g_ucAdmin.getVal(objParam, "type");
		
		if(paramType == "uc_radioboolean"){
			
			var trueName = g_ucAdmin.getVal(objParam, "true_name");
			var trueValue = g_ucAdmin.getVal(objParam, "true_value");
			
			var falseName = g_ucAdmin.getVal(objParam, "false_name");
			var falseValue = "";
			
			var options = {};
			options[trueName] = trueValue;
			options[falseName] = falseValue;
			
		}else{		//select and dropdown
			var options = g_ucAdmin.getVal(objParam, "options");
		}
				
		var selectName = objSelectValues.prop("name");
		
		var currentValue = null;
		
		if(isInitValues === true)
			var currentValue = g_ucAdmin.getVal(g_objData, selectName);
		
		var isExists = false;
		
		jQuery.each(options,function(text, value){
			
			if(currentValue === null)
				currentValue = value;
			
			if(jQuery.isArray(currentValue) == true){
				
				if(currentValue.indexOf(value) !== -1)
					isExists = true;
			}else{
				if(currentValue === value)
					isExists = true;
			}
			
			g_ucAdmin.addOptionToSelect(objSelectValues, value, text);
		});
		
		//set current value, fit to multiple
		
		if(isExists == true){
			
			var type = typeof currentValue;
			
			if(jQuery.isArray(currentValue) == false)
				currentValue = [currentValue];
			
			objSelectValues.val(currentValue);
			
		}
		
		
	}
	
	
	/**
	 * fill conditions select row
	 */
	function fillConditionSelectRow(objSelect, arrParams){
		
		var selectName = objSelect.prop("name");
		
		var currentValue = g_ucAdmin.getVal(g_objData, selectName);
		if(!currentValue)
			currentValue = "";
		
		objSelect.html("");
		
		g_ucAdmin.addOptionToSelect(objSelect, "", "[Select Attribute]");
				
		var isFound = false;
		jQuery.each(arrParams, function(index, param){
			
			var name = g_ucAdmin.getVal(param, "name");
			var title = g_ucAdmin.getVal(param, "title");
			
			if(name == g_currentOpenedName)
				return(true);
			
			g_ucAdmin.addOptionToSelect(objSelect, name, title);
			
			if(currentValue == name)
				isFound = true;
		});
		
		if(isFound == false)
			currentValue = "";
		
		setTimeout(function(){
			
			objSelect.data("init_value",true);
			objSelect.val(currentValue).trigger("change");
			
		},100);
		
		
		
	}
	
	
	/**
	 * fill conditions selects
	 */
	function fillConditionsSelects(arrParams){
		
		var objSelects = g_objWrapper.find(".uc-dialog-condition-attribute");
		if(objSelects.length == 0)
			return(false);
		
		if(objSelects.length == 0)
			return(false);
		
		jQuery.each(objSelects, function(index, select){
			var objSelect = jQuery(select);
			
			fillConditionSelectRow(objSelect, arrParams);
		});
		
	}
		
	/**
	 * if no params - set empty class. if params exists - fill the selects
	 */
	function handleConditionsOnOpen(){
		
		var objConditionsWrapper = g_objWrapper.find(".uc-dialog-conditions-content");
		var objSelects = g_objWrapper.find(".uc-dialog-condition-attribute");
		
		resetMoreConditions();
		
		var arrParams = g_objParent.getControlParams(g_currentOpenedType);
		var hasParams = (jQuery.isEmptyObject(arrParams) == false);
		
		//show empty mode
		if(hasParams == false){
			objSelects.html("");
			objConditionsWrapper.addClass("uc-noparents-mode");
			return(false);
		}
		
		objConditionsWrapper.removeClass("uc-noparents-mode");
		
		g_objWrapper.data("condition_params", arrParams);
		
		fillConditionsSelects(arrParams);
		
	}
	
	/**
	 * add more condition
	 */
	function addMoreCondition(){
		
		var objSecondRow = g_objWrapper.find(".uc-row-condition2");
		objSecondRow.show();
		
		var objLinkAdd = g_objWrapper.find(".uc-dialog-link-addcondition");
		
		objLinkAdd.hide();		
	}
	
	
	/**
	 * init more conditions
	 */
	function resetMoreConditions(){
		
		var objSecondRow = g_objWrapper.find(".uc-row-condition2");
		objSecondRow.hide();
		
		var objLinkAdd = g_objWrapper.find(".uc-dialog-link-addcondition");
		
		objLinkAdd.show();		
		
	}
	
	
	/**
	 * init the dialog conditions
	 */
	function initConditions(){
		
		var objTableConditions = g_objWrapper.find(".uc-table-dialog-conditions");
		
		if(objTableConditions.length == 0)
			return(false);
		
		onEvent(events.OPEN, handleConditionsOnOpen);
		
		g_objWrapper.on("change",".uc-dialog-condition-attribute", onSelectConditionsAttributeChange);
		
		var objLinkAdd = g_objWrapper.find(".uc-dialog-link-addcondition");
		
		objLinkAdd.on("click", addMoreCondition);
		
	}
	
	/**
	 * init sections conditions
	 */
	this.initSectionsConditions = function(objWrapper, objParent){
		
		g_objWrapper = objWrapper;
		
		g_objParent = objParent;
				
		var objContent = objWrapper.find(".uc-dialog-conditions-content");
		
		objContent.hide();
		
		initControls();
		
		initConditions();
	}
	
	
	/**
	 * handle sections conditions
	 */
	this.handleSectionConditions = function(objData){
		
		g_objData = objData;
		
		//on / off enable conditions checkbox
		
		var objCheck = jQuery("#uc_dialog_left_condition_section");
		
		var enableCondition = g_ucAdmin.getVal(objData,"enable_condition");
		
		enableCondition = g_ucAdmin.strToBool(enableCondition);
		
		objCheck.prop("checked", enableCondition);
		objCheck.trigger("change");
		
		
		triggerEvent(events.OPEN);
		
	}
	
	
	function ____________EVENTS____________(){};
	
	
	/**
	 * trigger event
	 */
	function triggerEvent(eventName, params){
		if(!params)
			var params = null;
		
		g_objWrapper.trigger(eventName, params);
	}
	
	
	/**
	 * on event name
	 */
	function onEvent(eventName, func){
		g_objWrapper.on(eventName,func);
	}
	
	
	
	function ____________LIMIT____________(){};
	
	
	/**
	 * limit the dialog for edit
	 */
	function limitDialog(){
		
		g_objWrapper.addClass("uc-dialog-limited");
		g_ucAdmin.disableInput(g_objParamName);
	}
	
	
	/**
	 * unlimit dialog for edit
	 */
	function unlimitDialog(){

		g_objWrapper.removeClass("uc-dialog-limited");
		g_ucAdmin.enableInput(g_objParamName);
	}
	
	
	/**
	 * return if the dialog is limited
	 */
	function isDialogLimited(){
		
		if(g_objWrapper.hasClass("uc-dialog-limited"))
			return(true);
		
		return(false);
	}
	
	/**
	 * get ui dialog wrapper
	 */
	function getDialogUIWrapper(){
		
		var objDialogUI = g_objWrapper.parents(".ui-dialog");
		
		if(objDialogUI.length !== 1)
			return(null);
		
		return(objDialogUI);
	}
	
	/**
	 * set pro param mode
	 */
	function setProParamMode(){
		
		g_objWrapper.addClass("uc-pro-param");
		
		var objDialogUI = getDialogUIWrapper();
		if(!objDialogUI)
			return(true);
		
		objDialogUI.find(".ui-dialog-buttonset button:last-child").addClass("uc-button-disabled");
		
		g_ucAdmin.disableInput(g_objParamTitle);
		g_ucAdmin.disableInput(g_objParamName);
		
		var objTextarea = g_objWrapper.find("textarea");
		g_ucAdmin.disableInput(objTextarea);
		
	}
	
	/**
	 * unset pro mode
	 */
	function unsetProParamMode(){
		
		g_objWrapper.removeClass("uc-pro-param");
		
		var objDialogUI = getDialogUIWrapper();
		if(!objDialogUI)
			return(true);
		
		objDialogUI.find(".ui-dialog-buttonset button:last-child").removeClass("uc-button-disabled");
		
		g_ucAdmin.enableInput(g_objParamTitle);
		g_ucAdmin.enableInput(g_objParamName);
		
		var objTextarea = g_objWrapper.find("textarea");
		g_ucAdmin.enableInput(objTextarea);
		
	}
	
	function ____________INIT____________(){};
	
	
	/**
	 * add special param
	 * onClearInputData(objInput)
	 * onGetInputData(objInput)
	 * onFillInputData(objInput,data)
	 * onOpenDialog, onInitDialog
	 */
	this.addSpecialInput = function(inputType, obj){
		
		//on open dialog
		if(obj.hasOwnProperty("onOpenDialog")){
			onEvent(events.OPEN, function(){
				obj.onOpenDialog(g_objWrapper, g_objData);
			});
		}
		
		if(obj.hasOwnProperty("onInitDialog")){
			onEvent(events.INIT, function(){
				obj.onInitDialog(g_objWrapper);
			});
		}
		
		if(obj.hasOwnProperty("onClearInputData") == false)
			throw new Error("Special input myst have function: onClearInputData");
		
		if(obj.hasOwnProperty("onGetInputData") == false)
			throw new Error("Special input myst have function: onGetInputData");

		if(obj.hasOwnProperty("onFillInputData") == false)
			throw new Error("Special input myst have function: onFillInputData");
		
		g_arrSpecialInputs[inputType] = obj;
		
	}
	
		
	/**
	 * on select type change
	 */
	function onSelectTypeChange(){
		
		var isLimited = isDialogLimited();
		if(isLimited == true)
			return(false);
				
		var contentID = jQuery(this).val();
		var objContent = jQuery("#" + contentID);
				
		//show current content
		g_objTabContentWrapper.find(".uc-tab-content").not(objContent).removeClass("uc-content-selected").hide();
		objContent.addClass("uc-content-selected").show();

		var isPro = objContent.hasClass("uc-pro-param");
		
		if(isPro == true)
			setProParamMode();
		else
			unsetProParamMode();
				
		
		//focus title if empty
		var title = g_objParamTitle.val();
		if(title == "")
			g_objParamTitle.focus();
		
	}
	
	
	/**
	 * init tabs of param dialog
	 */
	function initTabs(){
		
		g_objSelectType.change(onSelectTypeChange);
		
	}
	
	
	/**
	 * set default value fields events
	 */
	function initEvents_defaultValues(){
		
		var dialogID = g_objWrapper.prop("id");
		if(dialogID != "uc_dialog_param_main")
			return(false);
		
		var objInputs = jQuery("#uc_tabparam_main_uc_editor textarea[name='default_value']").add(
				"#uc_tabparam_main_uc_textarea textarea[name='default_value']").add(
				"#uc_tabparam_main_uc_content textarea[name='default_value']").add(
				"#uc_tabparam_main_uc_textfield input[name='default_value']");
		
		objInputs.change(function(){
			var objInput = jQuery(this);
			var value = objInput.val();
			objInputs.not(objInput).val(value);
		});
		
	}
	
	
	/**
	 * on title input change, check and fill the name input if it's empty
	 */
	function checkFillNameFromTitle(){
		
		var title = g_objParamTitle.val();
		
		//make all the validations
		var isAscii = g_ucAdmin.isStringAscii(title);
		
		if(isAscii == false)
			return(true);
		
		if(!g_objParamName)
			return(true);
		
		//validate that name is empty
		
		var name = g_objParamName.val();
		name = jQuery.trim(name);
		if(name)
			return(true);
		
		//convert and set the name
		name = g_ucAdmin.getNameFromTitle(title);
		
		g_objParamName.val(name);
		
	}
	
	
	/**
	 * init events
	 */
	function initEvents(){
		
		//trigger on title change event

		g_objParamTitle.on("blur", checkFillNameFromTitle); 


		//trigger on name change event
				
		g_objParamName.on("change",function(){
			var value = jQuery(this).val();
			triggerEvent(events.CHANGE_NAME, value);
		});
		
		g_objParamName.on("keyup",function(){
			var value = jQuery(this).val();
			triggerEvent(events.CHANGE_NAME, value);
		});
		
		initEvents_defaultValues();
	}
	
	
	/**
	 * operate controlled
	 */
	function control_operateControlled(controlledID, toShow, objContent){
		
		if(controlledID == "" || controlledID == undefined){
			trace(objInput);
			throw new Error("empty controlled selector");
		}
		
		var objControlled = objContent.find(controlledID);
		
		if(objControlled.length == 0)
			throw new Error("controlled item not found: " + controlledID);
			
		if(toShow == true){
			objControlled.show();
		}else{
			objControlled.hide();
		}
		
		
	}
	
	/**
	 * init inputs that are controls
	 */
	function initControls(){
		
		var objControls = g_objWrapper.find(".uc-control");
				
		objControls.change(function(){
			
			var objInput = jQuery(this);
			var type = getInputType(objInput);
			
			var objContent = objInput.parents(".uc-tab-content");
			if(objContent.length == 0)
				objContent = g_objWrapper;
			
			switch(type){
				case "checkbox":
					var toShow = objInput.is(":checked");
					var controlledID = objInput.data("controlled-selector");
					
					control_operateControlled(controlledID, toShow, objContent);
					
				break;
				case "radio":					
					var objRadioParent = objInput.parents(".uc-radioset-wrapper");
					var objRadios = objRadioParent.find("input[type='radio'].uc-control");
										
					objRadios.each(function(index, radio){
						
						var objRadio = jQuery(radio);
						var toShow = objRadio.is(":checked");
												
						var controlledID = objRadio.data("controlled-selector");
												
						control_operateControlled(controlledID, toShow, objContent);
					});
					
				break;
				case "select":
					
					var value = objInput.val();
					var controlledSelector = objInput.data("controlled-selector");
					
					var objChildren = objContent.find(controlledSelector);
					
					jQuery.each(objChildren, function(index, child){
						
						var objChild = jQuery(child);
						var childValue = objChild.data("control");
												
						var arrValues = childValue.split(",");
						if(arrValues.length > 1){
							
							var isInArray = jQuery.inArray(value,arrValues);
							
							if(isInArray !== -1)
								objChild.show();
							else
								objChild.hide();						
							
						}else{
							
							if(childValue == value)
								objChild.show();
							else
								objChild.hide();						
							
						}
												
					});
					
					
				break;
				default:
					throw new Error("Wrong control input type: " + type);
				break;
			}
			
			
		});
		
	}
	
	
	/**
	 * init add links. links that adding text to input boxes or textareas
	 */
	function initAddLinks(){
		
		var objAddLinks = g_objWrapper.find(".uc-link-add");
		if(objAddLinks.length == 0)
			return(false);
		
		objAddLinks.on("click",function(){
			
			var objLink = jQuery(this);
			var objContent = objLink.parents(".uc-tab-content");
			var selector = objLink.data("addto-selector");
			var addtext = objLink.data("addtext");
			
			g_ucAdmin.validateNotEmpty(addtext, "add text");
			
			if(selector == "" || selector == undefined){
				trace(objLink);
				throw new Error("empty addto selector");
			}
			
			var objInput = objContent.find(selector);

			if(objInput.length == 0)
				throw new Error("input or textarea not found: " + selector);
			
			g_ucAdmin.addTextToInput(objInput, addtext);
			
		});
		
	}

	
	/**
	 * init right content settings if available
	 */
	function initRightContentSettings(){
		
		var objSettingsWrappers = g_objRightArea.find(".unite_settings_wrapper");
		if(objSettingsWrappers.length == 0)
			return(false);
		
		objSettingsWrappers.each(function(){
			
			var objWrapper = jQuery(this);
			var objSettings = new UniteSettingsUC();
			objSettings.init(objWrapper);
			
		});
		
	}
	
	
	
	/**
	 * init the dialog
	 */
	function init(){
		
		g_objTexts = g_objWrapper.data("texts");
				
		g_objSelectType = g_objWrapper.find(".uc-paramdialog-select-type");
		
		g_objTabContentWrapper = g_objWrapper.find(".uc-tabsparams-content-wrapper");
		g_objLeftArea = g_objTabContentWrapper.find(".dialog-param-left");
		g_objRightArea = g_objTabContentWrapper.find(".dialog-param-right");
		g_objError = g_objWrapper.find(".uc-dialog-param-error");
		g_objParamTitle = g_objWrapper.find(".uc-param-title");
		g_objParamName = g_objWrapper.find(".uc-param-name");
		g_objSettings = new UniteSettingsUC();
				
		initTabs();
		
		initEvents();
		
		initControls();
		
		initAddLinks();
		initSelectParams();
		initDropdownParam();
		initRadioBooleanParam();
		initTableSelectRelated();
		initImageSelectField();
		initMp3SelectField();
		
		initSelect2Param();
		
		initRightContentSettings();
		
		initConditions();
		
		//for all the special params that run on init
		triggerEvent(events.INIT);
		
		//init specific params
		var arrParamTypes = getArrParamTypes();
		
		jQuery.each(arrParamTypes, function(index, type){
			
			switch(type){
				case "uc_number":
					initNumberParam();
				break;
				case "uc_colorpicker":
					initColorPicker();
				break;
				case "uc_image":
					initImageParam();
				break;
			}
		
		});
		
	}
	
	
	/**
	 * init the params dialog
	 */
	this.init = function(objWrapper, objParent){
		
		g_objWrapper = objWrapper;
		
		if(objParent)
			g_objParent = objParent;
		else
			g_objParent = null;
		
		init();
	};
	
}