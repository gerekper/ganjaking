"use strict";

function UniteCreatorParamsEditor(){

	var t = this;

	var g_objWrapper, g_objTableBody, g_objEmptyParams, g_type;
	var g_objDialog = new UniteCreatorParamsDialog(), g_buttonAddParam;
	var g_buttonAddImageBase, g_objLastParam, g_objCatsWrapper, g_objCopyCatSection;
	var g_objParamsDialogSpecial;

	if(!g_ucAdmin)
		var g_ucAdmin = new UniteAdminUC();

	this.events = {
			UPDATE: "update",	//update list event
			BULK: "bulk"
	};

	var g_temp = {
			hasCats:false,
			isItemsType:false,
			funcOnUpdate: function(){},			//on some element change
			DEFAULT_CAT: "cat_general_general",
			CLASS_MOVE_MODE: "uc-move-mode",
			LOCAL_STORAGE_KEY: "uc_param_cat_copied",
			HOUR_IN_MS: 60*60*1000,
			counter:0
	};


	function ______________GETTERS______________(){}

	/**
	 * get row data
	 */
	function getRowData(objRow){

		var data = objRow.data("paramdata");

		//add catid
		if(g_temp.hasCats == true){

	 		var catid = objRow.data("catid");
			if(catid)
				data["__attr_catid__"] = catid;
		}

		//avoid link return
		var objReturn = {};
		jQuery.extend(objReturn, data);

		return(objReturn);
	}


	/**
	 * get params object (table rows)
	 */
	function getParamsRows(isSelected){

		if(!g_objTableBody)
			throw new Error("The params editor is not inited yet");

		var selector = "tr";
		if(isSelected === true)
			selector = "tr.uc-selected";

		var rows = g_objTableBody.find(selector);

		return(rows);
	}


	/**
	 * check if some param type exists
	 */
	function isParamDataExists(key, value){

		var rows = getParamsRows();

		for(var i=0;i<rows.length;i++){
			var objRow = jQuery(rows[i]);
			var objParam = getRowData(objRow);

			if(objParam[key] == value)
				return(true);
		}


		return(false);
	}


	/**
	 * check if some param type exists
	 */
	function isParamTypeExists(type){

		var isExists = isParamDataExists("type", type);

		return(isExists);
	}


	/**
	 * check if some param type exists
	 */
	function isParamNameExists(name){

		var isExists = isParamDataExists("name", name);

		return(isExists);
	}


	/**
	 * get duplicated new param name
	 */
	function getDuplicateNewName(name){

		var newName = name+"_copy";
		var isExists = isParamNameExists(newName);
		if(isExists == false)
			return(newName);

		var counter = 1;
		do{
			counter++;
			newName = newName + counter;
			isExists = isParamNameExists(newName);
		}while(isExists == true);


		return(newName);
	}


	/**
	 * get params row object by index
	 */
	function getParamsRow(rowIndex){

		var rows = getParamsRows();

		if(rowIndex >= rows.length)
			throw new Error("Row with index: "+rowIndex+" not found");

		var objRow = jQuery(rows[rowIndex]);

		return(objRow);
	}


	/**
	 * get the number of params
	 */
	function getNumParams(){

		var rows = getParamsRows();

		return rows.length;
	}



	/**
	 * get type title from type name
	 */
	function getTypeTitle(type){

		var typeTitle = type;
		if(g_uctext.hasOwnProperty(type))
			typeTitle = g_uctext[type];

		return(typeTitle);
	}


	/**
	 * get data from params table
	 * paramsType could be "control"
	 */
	this.getParamsData = function(paramsType, isAssoc, filterCatID){

		var rows = getParamsRows();

		var arrParams = [];

		jQuery.each(rows, function(index, row){

			var objRow = jQuery(row);
			var objParam = getRowData(objRow);

			if(filterCatID){
				var paramCatID = g_ucAdmin.getVal(objParam, "__attr_catid__");
				if(paramCatID != filterCatID)
					return(true);
			}


			if(paramsType == "control"){
				switch(objParam.type){
					case "uc_dropdown":
					case "uc_radioboolean":
					case "uc_checkbox":
					case "uc_multiple_select":
					break;
					default:
						return(true);
					break;
				}
			}

			arrParams.push(objParam);
		});

		if(isAssoc == true){		//turn to assoc

			var objParams = {};
			jQuery.each(arrParams, function(index, param){
				var name = param.name;
				objParams[name] = param;
			});
			return(objParams);

		}else
			return(arrParams);
	};

	/**
	 * get categories data
	 */
	this.getCatData = function(){

		return getCatsData();
	}

	/**
	 * get condition text row
	 */
	function getParamRowHtml_getConditionTextRow(objParam, suffix){

		var conditionText = "";

		var conditionAttribute = g_ucAdmin.getVal(objParam, "condition_attribute"+suffix);

		if(!conditionAttribute){
			return(null);
		}

		var conditionOperator = g_ucAdmin.getVal(objParam, "condition_operator"+suffix);
		var conditionValue = g_ucAdmin.getVal(objParam, "condition_value"+suffix);

		conditionText = conditionAttribute;

		if(conditionOperator == "equal")
			conditionText += " = ";
		else
			conditionText += " != ";

		conditionText += conditionValue;
		if(!conditionValue)
			conditionText += "[null]";


		return(conditionText);
	}


	/**
	 * get condition text
	 */
	function getParamRowHtml_getConditionText(objParam){

		var textRow1 = getParamRowHtml_getConditionTextRow(objParam,"");

		if(!textRow1)
			return(null);

		var textRow2 = getParamRowHtml_getConditionTextRow(objParam,"2");

		var text = textRow1;
		if(textRow2)
			text += " and " + textRow2;

		return(text);
	}

	/**
	 * get row html, taken from param object
	 *
	 */
	function getParamRowHtml(objParam){

		var typeTitle = getTypeTitle(objParam.type);

		var html = "<tr>";

		var paramError = null;
		if(objParam.hasOwnProperty("param_error"))
			paramError = objParam["param_error"];

		var textRowAdd = "";
		var linkClass = "";
		var linkTitle = "";

		if(paramError){
			linkTitle = "title='"+paramError+"'";
			linkClass = " unite-color-red";
			textRowAdd = "class='unite-color-red' title='"+paramError+"'";
		}

		var isAdminLabel = g_ucAdmin.getVal(objParam, "admin_label", false, g_ucAdmin.getvalopt.FORCE_BOOLEAN);

		var adminLabelClass = (isAdminLabel == true)?" label-active":"";

		//conditoin
		var enableCondition = g_ucAdmin.getVal(objParam, "enable_condition", false, g_ucAdmin.getvalopt.FORCE_BOOLEAN);

		var conditionText = null;

		if(enableCondition == true){

			var conditionAttribute = g_ucAdmin.getVal(objParam, "condition_attribute");
			if(!conditionAttribute)
				enableCondition = false;
			else
				conditionText = getParamRowHtml_getConditionText(objParam);
		}

		var tabText = null;

		var tabName = g_ucAdmin.getVal(objParam, "tabname");
		tabName = jQuery.trim(tabName);

		if(tabName)
			tabText = tabName;

		//icon move
		html += " <td class='uc-hide-on-movemode uc-table-nowrap'><div class='uc-table-row-handle'></div><div class='uc-table-admin-label"+adminLabelClass+"' title='Admin Label'></div></td>";
		html += " <td class='uc-show-on-movemode'> <input type='checkbox' class='uc-check-param-move' data-name='" + objParam.name + "'> </td>";

		//title link
		html += " <td>";
		html += "<a class='uc-button-edit-param"+linkClass+"' "+linkTitle+" href='javascript:void(0)'>" + objParam.title + "</a>";

		if(enableCondition)
			html += "<div class='uc-text-condition' title='"+g_uctext["display_condition"]+"'>" + conditionText + "</div>";

		if(tabText)
			html += "<div class='uc-text-tab'>" + tabText + "</div>";

		html += "</td>";

		html += " <td "+textRowAdd+">" + objParam.name + "</td>";
		html += " <td "+textRowAdd+">" + typeTitle + "</td>";
		html += " <td>"

		switch(objParam.type){
			case "uc_checkbox":
				var checked = "";
				if(objParam.is_checked == "true")
					checked = " checked ";

				html += "<input type='checkbox' " + checked + " readonly>";
				html += "<span>" + objParam.text_near + "</span>";
			break;
			case "uc_dropdown":
				html += "<select>";
				var options = objParam.options;
				var defaultValue = objParam.default_value;

				if(typeof options == "object"){
					jQuery.each(options, function(name, value){
						var selected = "";
						if(value == defaultValue)
							selected = "selected='selected'";

						html += "<option val='" + value + "' " + selected + ">" + name + "</option>";
					});
				}
				html += "</select>"
			break;
			case "uc_radioboolean":
				var trueChecked = " checked";
				var falseChecked = "";

				if(objParam.default_value == objParam.false_value){
					trueChecked = "";
					falseChecked = " checked";
				}

				html += "<label><input type='radio' "+trueChecked+" name="+objParam.name+"></input>"+objParam.true_name+"</label>";
				html += "<label><input type='radio' "+falseChecked+" name="+objParam.name+"></input>"+objParam.false_name+"</label>";

			break;
			case "uc_number":
				var unit = objParam.unit;
				if(unit == "other")
					unit = objParam.unit_custom;

				html += "<input type='text' class='unite-input-number' readonly value='"+objParam.default_value+"'>&nbsp;" + unit;
			break;
			case "uc_colorpicker":
				html += "<input type='text' class='input-color unite-float-left' readonly value='"+objParam.default_value+"'>";
				html += "<div class='colorpicker-bar' style='background-color:"+objParam.default_value+"'></div>";
			break;
			case "uc_textarea":
			case "uc_editor":
				html += "<textarea readonly>"+objParam.default_value+"</textarea>";
			break;
			case "uc_image":
				html += "<input type='text' class='unite-input-image' readonly value=''>";
				html += "<a disabled readonly class='unite-button-secondary button-disabled'>"+g_uctext.choose_image+"</a>";
			break;
			case "uc_mp3":
				html += "<input type='text' class='unite-input-image' readonly value=''>";
				html += "<a disabled readonly class='unite-button-secondary button-disabled'>"+g_uctext.choose_audio+"</a>";
			break;
			default:
				var defaultValue = "";
				if(objParam.hasOwnProperty("default_value"))
					defaultValue = objParam.default_value;

				html += "<input type='text' readonly value='" + defaultValue + "'>";
			break;
		}

		html += " </td>"

		var deleteClass = "";
		if(paramError)
			deleteClass = " unite-bold";

		//add operations
		html += " <td class='uc-table-nowrap'>";
		html += "  <a href='javascript:void(0)' class='unite-button-secondary uc-button-delete-param "+deleteClass+"' title='"+g_uctext.delete_op+"' ><i class='far fa-trash-alt'></i></a>";
		html += "  <a href='javascript:void(0)' class='unite-button-secondary uc-button-duplicate-param' title='"+g_uctext.duplicate_op+"'><i class='far fa-clone'></i></a>";
		html += "  <a href='javascript:void(0)' class='unite-button-secondary uc-button-bulk-param' title='"+g_uctext.bulk+"'><i class='far fa-copy'></i></a>";
		html += " </td>";

		html += "</tr>";

		return(html);
	}

	/**
	 * get specific category params data
	 */
	function getCatParamsData(catid){

		var arrParams = t.getParamsData(null, false, catid);

		return(arrParams);
	}


	function ______________CATS______________(){}

	/**
	 * get number of rows in category
	 */
	function getNumCatRows(paramCatID){

		var objCatIDs = getCatIDs();

		var objNumbers = {};
		var objRows = getParamsRows();

		jQuery.each(objRows, function(index, row){

			var objRow = jQuery(row);
			var catID = objRow.data("catid");

			if(!catID)
				catID = g_temp.DEFAULT_CAT;

			if(objCatIDs.hasOwnProperty(catID) == false)
				catID = g_temp.DEFAULT_CAT;

			var numCats = g_ucAdmin.getVal(objNumbers, catID);

			if(!numCats)
				numCats = 0;

			numCats++;

			objNumbers[catID] = numCats;
		});

		var arrCatIDs = getCatIDs();

		jQuery.each(arrCatIDs, function(catID){

			if(objNumbers.hasOwnProperty(catID) == false)
				objNumbers[catID] = 0;
		});

		// return single cat number
		if(paramCatID){
			var catNumber = g_ucAdmin.getVal(objNumbers, paramCatID);
			if(!catNumber)
				catNumber = 0;
			return(catNumber);
		}

		return(objNumbers);
	}


	/**
	 * get current category
	 */
	function getCurrentCat(){

		if(g_temp.hasCats == false)
			return(null);

		var objCat = g_objCatsWrapper.find(".uc-attr-list-sections li.uc-active");

		if(objCat.length == 0 || objCat.length > 1)
			return(null);

		return(objCat);
	}

	/**
	 * get cat data
	 */
	function getCurrentCatData(name){

		var objCat = getCurrentCat();
		var data = getCatData(objCat);

		if(name == "id")
			return(data.id);

		if(name == "title")
			return(data.title)

		return(data);
	}

	/**
	 * get all categories id's assoc
	 */
	function getCatIDs(tab){

		var objIDs = {};
		var objRows = g_objCatsWrapper.find(".uc-attr-list-sections li");

		jQuery.each(objRows, function(index, row){
			var objRow = jQuery(row);
			var catID = objRow.data("id");

			objIDs[catID] = true;
		});

		return(objIDs);
	}

	/**
	 * get category by ID
	 */
	function getCatByID(catID){

		if(!catID)
			return(null);

		var cat = jQuery("#"+catID);

		if(cat.length == 0)
			return(null);

		return(cat);
	}


	/**
	 * get cat data
	 */
	function getCatData(objRow, includeTab){

		var objTitle = objRow.find(".uc-attr-list__section-title");

		var title = objTitle.html();

		title =	jQuery.trim(title);

		var catID = objRow.data("id");

		var data = {};

		var conditionsData = getConditionsCatData(catID);

		if(conditionsData && jQuery.isEmptyObject(conditionsData) == false && typeof conditionsData == "object")
			jQuery.extend(data, conditionsData);

		data["id"] = catID;
		data["title"] = title;

		if(includeTab === true)
			data["tab"] = getCatTab(objRow);

		return(data);
	}


	/**
	 * get category tab
	 */
	function getCatTab(objRow){

		var objList = objRow.parents(".uc-attr-list-sections");

		var tab = objList.data("tab");

		return(tab);
	}

	/**
	 * get tab data
	 */
	function getCatsData_tab(objCats, name){

		var objList = jQuery("#uc_attr_list_sections_"+name);
		var objlistItems = objList.children("li");

		var tab = objList.data("tab");

		jQuery.each(objlistItems, function(index, item){
			var objItem = jQuery(item);
			var data = getCatData(objItem);
			data.tab = tab;

			objCats.push(data);
		});

		return(objCats);
	}

	/**
	 * get categories data
	 */
	function getCatsData(){

		if(g_temp.hasCats == false)
			return(null);

		var objCats = [];
		objCats = getCatsData_tab(objCats, "content");
		objCats = getCatsData_tab(objCats, "style");

		return(objCats);
	}


	/**
	 * update category num items
	 */
	function updateCatNumItems(objCat, numItems){

		if(!objCat)
			return(false);

		var objNumItems = objCat.find(".uc-attr-list__section-numitems");

		g_ucAdmin.validateDomElement(objNumItems, "num items object of category");

		var html = "("+numItems+")";

		objNumItems.html(html);
	}


	/**
	 * update num items of currnet category
	 */
	function updateCurrentCatNumItems(){

		var objNumParams = getNumCatRows();

		for(var catID in objNumParams){

			var numParams = objNumParams[catID];

			var objCat = getCatByID(catID);

			if(!objCat)
				continue;

			updateCatNumItems(objCat, numParams);
		}

	}


	/**
	 * rename category
	 */
	function renameCategory(objCat, newTitle, isMoveToEnd){

		if(typeof objCat == "string")
			objCat = getCatByID(objCat);

		g_ucAdmin.validateDomElement(objCat, "category");

		var objTitle = objCat.find(".uc-attr-list__section-title");

		objTitle.html(newTitle);

		if(isMoveToEnd === true){
			var objParent = objCat.parent();
			objParent.append(objCat);
		}

	}


	/**
	 * update visibility by categories
	 */
	function updateParamsVisibilityByCats(){

		if(g_temp.hasCats == false)
			return(false);

		var currentCatID = getCurrentCatData("id");

		var objRows = getParamsRows();

		jQuery.each(objRows, function(index, row){

			var objRow = jQuery(row);
			var catID = objRow.data("catid");

			if(currentCatID == catID)
				objRow.show();
			else
				objRow.hide();
		});

	}


	/**
	 * add tab section to some tab
	 */
	function addCatToTab(tab, catTitle, catID, objData){

		var data = jQuery.extend({}, objData);

		delete data.id;
		delete data.tab;
		delete data.title;

		//check and rename if exists

		var objCat = getCatByID(catID);

		if(objCat){
			renameCategory(objCat, catTitle, true);
			updateCatConditionsData(catID, data);

			return(false);
		}

		if(!catID)
			var catID = "cat_"+tab+"_"+g_ucAdmin.getRandomString(8);

		//some length protection
		if(catTitle.length > 60){

			g_temp.counter++;
			catTitle = "Long Category "+g_temp.counter;
		}

		var html ="<li id='"+catID+"' data-id='"+catID+"'>";
		html += "<span class=\"uc-attr-list__section-title\">";
		html += g_ucAdmin.htmlspecialchars(catTitle);
		html += "</span>";
		html += "<span class=\"uc-attr-list__section-numitems\"></span>";

		html += "<i class=\"uc-attr-list-sections__icon-edit fas fa-pen uc-hide-on-movemode\" title=\""+g_uctext.edit_section+"\"></i>";

		html += "<i class=\"uc-attr-list-sections__icon-delete fas fa-trash uc-hide-on-movemode\" title=\""+g_uctext.delete_section+"\"></i>";

		html += "<i class=\"uc-attr-list-sections__icon-copy fas fa-copy uc-hide-on-movemode\" title=\""+g_uctext.copy_section+"\"></i>";

		html += "<i class=\"uc-attr-list-sections__icon-move fas fa-bullseye uc-show-on-movemode\" title=\"Move Here\"></i>";

		html +=	"</li>";

		var objCat = jQuery(html);

		var objList = jQuery("#uc_attr_list_sections_"+tab);
		g_ucAdmin.validateNotEmpty(objList, "list sections");

		objList.append(objCat);

		updateCatConditionsData(catID, data);

		return(catID);
	}



	/**
	 * check if it's move mode
	 */
	function isMoveMode(){

		var isMoveMode = g_objWrapper.hasClass(g_temp.CLASS_MOVE_MODE);

		return(isMoveMode);
	}

	/**
	 * switch to move mode
	 */
	function switchCatMoveMode(isMove){

		if(isMove === undefined)
			var isMove = true;

		if(isMove == true){
			g_objWrapper.addClass(g_temp.CLASS_MOVE_MODE);
		}
		else
			g_objWrapper.removeClass(g_temp.CLASS_MOVE_MODE);

		clearAllMoveCheckboxes();
	}


	/**
	 * select some category
	 */
	function selectCategory(objCat){

		if(objCat.hasClass("uc-active"))
			return(true);

		//don't change categories on move mode
		var isMove = isMoveMode();

		if(isMove == true)
			return(true);

		var objActiveCat = g_objCatsWrapper.find(".uc-attr-list-sections li.uc-active");

		objActiveCat.removeClass("uc-active");
		objCat.addClass("uc-active");

		updateParamsVisibilityByCats();

	}

	/**
	 * on category click
	 */
	function onCatClick(){

		var objCat = jQuery(this);

		selectCategory(objCat);

	}


	/**
	 * edit selected category
	 */
	function onEditCatIconClick(){

		var objCurrentCat = getCurrentCat();
		if(objCurrentCat == null)
			return(false);

		openAddEditCatDialog(null, objCurrentCat);
	}



	/**
	 * on click on delete icon
	 */
	function onDeleteCatIconClick(event){

		event.stopPropagation();

		var objCurrentCat = getCurrentCat();
		var catData = getCatData(objCurrentCat, true);

		var catID = catData.id;
		var numRows = getNumCatRows(catID);

		if(numRows > 0){
			alert(g_uctext.delete_section_error);
			return(false);
		}

		if(catID == g_temp.DEFAULT_CAT){
			alert(g_uctext.delete_default_section_error);
			return(false);
		}

		//select another category

		var objSelectCat = objCurrentCat.prev();
		if(objSelectCat.length == 0)
			objSelectCat = objCurrentCat.next();

		if(objSelectCat.length == 0)
			objSelectCat = getCatById(g_temp.DEFAULT_CAT);

		objCurrentCat.remove();

		selectCategory(objSelectCat);

	}

	/**
	 * clear all move related checkboxes
	 */
	function clearAllMoveCheckboxes(){

		var objCheckboxes = g_objWrapper.find("input.uc-check-param-move");

		objCheckboxes.prop("checked","");

		updateCheckedParams();
	}


	/**
	 * update selected attributes, set selected classes to param rows
	 * by the checked checkbox
	 */
	function updateCheckedParams(){

		var objCheckboxes = g_objWrapper.find("input.uc-check-param-move");

		var hasSelected = false;
		var numSelected = 0;

		jQuery.each(objCheckboxes, function(index, checkbox){

			var objCheckbox = jQuery(checkbox);

			var isChecked = objCheckbox.is(":checked");
			var objParam = objCheckbox.parents("tr");

			if(isChecked){
				objParam.addClass("uc-selected");
				hasSelected = true;
				numSelected++;
			}
			else
				objParam.removeClass("uc-selected");
		});

		if(hasSelected == true)
			g_objWrapper.addClass("uc-has-selected");
		else
			g_objWrapper.removeClass("uc-has-selected");

		var objNumSelected = jQuery("#uc_attr_cats_selected_text_number");
		objNumSelected.html(numSelected);

	}

	/**
	 * move params between categories
	 */
	function onMoveParamsClick(){

		var objButton = jQuery(this);

		var objCatRow = objButton.parents("li");

		if(objCatRow.length == 0)
			return(false);

		var catData = getCatData(objCatRow, false);
		var catID = catData.id;

		var objSelectedRows = getParamsRows(true);

		jQuery.each(objSelectedRows, function(index, row){
			var objRow = jQuery(row);

			objRow.data("catid", catID);

		});

		//clear checkboxes
		clearAllMoveCheckboxes();

		//update visibility
		updateParamsVisibilityByCats();

		//set numbers
		triggerEvent(t.events.UPDATE);

		//stop move mode
		switchCatMoveMode(false);
	}


	/**
	 * init cats events
	 */
	function initCatsEvents(){

		//list add section button
		var objAddButtons = g_objCatsWrapper.find(".uc-attr-cats__button-add");

		objAddButtons.on("click", onCatAddSectionClick);

		//inside dialog button
		var buttonAddSectionDialog = jQuery("#uc_dialog_attribute_category_button_addsection");
		buttonAddSectionDialog.on("click", onDialogAddSectionClick);

		var inputTitleDialog = jQuery("#uc_dialog_attribute_category_addsection .uc-section-title");

		g_ucAdmin.validateDomElement(inputTitleDialog, "dialog input");
		inputTitleDialog.doOnEnter(onDialogAddSectionClick);

		//sortable cats
		var objListContent = jQuery("#uc_attr_list_sections_content");
		var objListStyle = jQuery("#uc_attr_list_sections_style");

		objListContent.sortable();
		objListStyle.sortable();


		//on cat click
		g_objCatsWrapper.on("click",".uc-attr-list-sections li", onCatClick);

		//on edit icon click
		g_objCatsWrapper.on("click",".uc-attr-list-sections__icon-edit", onEditCatIconClick);

		//on delete icon click
		g_objCatsWrapper.on("click",".uc-attr-list-sections__icon-delete", onDeleteCatIconClick);

		//on cat copy icon click
		g_objCatsWrapper.on("click",".uc-attr-list-sections__icon-copy", onCopyCatIconClick);

		var objButtonSwitchMoveMode = jQuery("#uc_attr_button_switch_move_mode");

		objButtonSwitchMoveMode.on("click", function(){switchCatMoveMode()});

		//stop move mode

		var objButtonStopMoveMode = jQuery("#uc_attr_button_stop_move_mode");

		objButtonStopMoveMode.on("click", function(){switchCatMoveMode(false)});
		g_objWrapper.on("click", ".uc-check-param-move", updateCheckedParams);

		//clear selected
		var objClearSelected = jQuery("#uc_attr_cats_selected_clear");
		objClearSelected.on("click", clearAllMoveCheckboxes);

		//move icons
		g_objWrapper.on("click", ".uc-attr-list-sections__icon-move", onMoveParamsClick);

		var objCatsDialog = jQuery("#uc_dialog_attribute_category_addsection");

		g_objParamsDialogSpecial = new UniteCreatorParamsDialog();
		g_objParamsDialogSpecial.initSectionsConditions(objCatsDialog, t);

	}


	/**
	 * on update
	 */
	function onUpdateInternal(){

		if(g_temp.hasCats == false)
			return(true);

		updateCurrentCatNumItems();
	}


	/**
	 * init categories from data
	 */
	function initCatsFromData(arrParamsCats){

		if(!arrParamsCats)
			return(false);

		if(jQuery.isArray(arrParamsCats) == false)
			return(false);

		jQuery.each(arrParamsCats, function(index, objCat){

			var tab = g_ucAdmin.getVal(objCat, "tab");
			var title = g_ucAdmin.getVal(objCat, "title");
			var id = g_ucAdmin.getVal(objCat, "id");

			addCatToTab(tab, title, id, objCat);

		});

	}



	function ______________COPY_CATEGORY______________(){}


	/**
	 * on copy category icon click
	 */
	function onCopyCatIconClick(){

		var objIcon = jQuery(this);
		var objCatRow = objIcon.parents("li");

		//get the data
		var catData = getCatData(objCatRow);
		var catTitle = catData.title;
		var catID = catData.id;

		var arrParams = getCatParamsData(catID);

		var objSaveData = {};
		objSaveData["title"] = catTitle;
		objSaveData["params"] = arrParams;

		//set expire time
		var currentTimeStamp = Date.now();
		var expireTime = currentTimeStamp + g_temp.HOUR_IN_MS;
		objSaveData["expire"] = expireTime;

		var strSaveData = g_ucAdmin.encodeObjectForSave(objSaveData);

		//save the local storage data
		try{
			window.localStorage.setItem(g_temp.LOCAL_STORAGE_KEY, strSaveData);
		}catch(e){

			alert("local storage not available in your site");
			return(null);
		}

		showBottomCopySection(catTitle);

	}


	/**
	* show copy section part
	*/
	function showBottomCopySection(title){

		var objName = g_objCopyCatSection.find(".uc-attr-cats-copied-section__name");

		objName.html(title);

		g_objCopyCatSection.show();
	}


	/**
	 * get stored data
	 */
	function copySectionGetStoredData(){

		try{

			var strData = window.localStorage.getItem(g_temp.LOCAL_STORAGE_KEY);

		}catch(e){
			return(null);
		}
		if(!strData)
			return(null);

		var jsonData = g_ucAdmin.decodeContent(strData);

		var objData = JSON.parse(jsonData);

		return(objData);
	}


	/**
	 * clear copied section
	 */
	function clearCopiedSection(){

		try{
			window.localStorage.removeItem(g_temp.LOCAL_STORAGE_KEY);
		}catch(e){
			return(null);
		}

		g_objCopyCatSection.hide();
	}

	/**
	 * paste copied section
	 */
	function pasteCopiedSection(){

		var objButton = jQuery(this);
		var tab = objButton.data("tab");

		var objData = copySectionGetStoredData();

		if(!objData)
			return(false);

		// add category

		var title = g_ucAdmin.getVal(objData, "title");

		var catID = addCatToTab(tab, title, null, objData);

		// add attributes

		var params = g_ucAdmin.getVal(objData, "params");

		if(!params)
			params = [];

		jQuery.each(params,function(index, param){
			param["__attr_catid__"] = catID;
			addParamRow(param);
		});

		// clear and select

		clearCopiedSection();

		// select cat

		var objCat = getCatByID(catID);
		selectCategory(objCat);

	}

	/**
	 * init copies section div event
	 */
	function initCopiedSectionEvents(){

		jQuery("#uc_attr_cats_copied_section_clear").on("click", clearCopiedSection);

		jQuery("#uc_attr_cats_copied_section_paste_content").on("click", pasteCopiedSection);
		jQuery("#uc_attr_cats_copied_section_paste_style").on("click", pasteCopiedSection);

	}

	/**
	 * init the copy category section.
	 * show on init if data exists
	 */
	function initCopyCatSection(){

		if(g_temp.hasCats == false)
			return(false);

		g_objCopyCatSection = jQuery("#uc_attr_cats_copied_section");

		if(g_objCopyCatSection.length == 0)
			return(false);

		//init events
		initCopiedSectionEvents();

		//show if available
		var objData = copySectionGetStoredData();

		var title = g_ucAdmin.getVal(objData, "title");
		var expire = g_ucAdmin.getVal(objData, "expire");

		if(!expire){
			clearCopiedSection();
			return(false);
		}

		var currentTime = jQuery.now();
		if(currentTime > expire){
			clearCopiedSection();
			return(false);
		}

		if(!title)
			return(false);

		if(objData)
			showBottomCopySection(title);

	}


	function ______________CAT_DIALOG______________(){}


	/**
	 * open add edit dialog
	 */
	function openAddEditCatDialog(tab, objCatRow){

		var isEditMode = false;

		//set to edit mode
		if(objCatRow){
			var catData = getCatData(objCatRow, true);

			isEditMode = true;
		}

		var dialogID = "uc_dialog_attribute_category_addsection";
		var objDialog = jQuery("#"+dialogID);

		if(isEditMode == false){

			var dialogTitle = objDialog.data("title_add");
			var buttonText = objDialog.data("button_add");

			objDialog.data("tab", tab);
			objDialog.data("is_edit", false);
			objDialog.data("catid", null);
		}
		else{

			var dialogTitle = objDialog.data("title_edit");
			var buttonText = objDialog.data("button_update");

			objDialog.data("is_edit", true);
			objDialog.data("catid", catData.id);
		}

		var dialogOptions = {
			title: dialogTitle
		};

		g_ucAdmin.openCommonDialog(objDialog, function(){

			var objError = objDialog.find(".uc-error-message");
			objError.html("").hide();

			var objInput = objDialog.find(".uc-section-title");
			var objButton = objDialog.find(".uc-button-add-section");

			if(isEditMode == true){
				var catTitle = catData.title;

				var objInputTitle = objDialog.find(".uc-section-title");

				objInputTitle.val(catTitle);

			}else{
				objInput.val("");
			}

			objButton.html(buttonText);

			g_objParamsDialogSpecial.handleSectionConditions(catData);

			objInput.focus();

		}, dialogOptions);

	}


	/**
	 * on add section click
	 */
	function onCatAddSectionClick(){

		var objButton = jQuery(this);
		var tab = objButton.data("sectiontab");

		openAddEditCatDialog(tab);

	}

	/**
	 * get dialog cat extra data (conditions values);
	 */
	function getDialogConditionsData(objDialog){

		var objData = {};

		var objWrapper = objDialog.find(".uc-dialog-param");

		if(objWrapper.length == 0)
			return(false);

		var objInputs = objWrapper.find("input,select");

		jQuery.each(objInputs, function(index, input){

			var objInput = jQuery(input);
			var type = g_ucAdmin.getInputType(objInput);

			//get value
			switch(type){
				case "checkbox":
					var value = objInput.is(":checked");
				break;
				case "select":
					var value = objInput.val();
				break;
				default:
					trace(objInput);
					throw new Error("Wrong input type: "+type);
				break;
			}

			var name = objInput.prop("name");

			objData[name] = value;
		});


		return(objData);
	}


	/**
	 * add the section
	 */
	function onDialogAddSectionClick(){

		var dialogID = "uc_dialog_attribute_category_addsection";

		var objDialog = jQuery("#" + dialogID);

		var objInput = objDialog.find(".uc-section-title");

		var catTitle = objInput.val();

		var objError = objDialog.find(".uc-error-message");

		catTitle = jQuery.trim(catTitle);

		var conditionsData = getDialogConditionsData(objDialog);

		if(!catTitle){
			var textError = objError.data("error_empty");
			objError.show().html(textError);
			objInput.focus();
			return(false);
		}

		objError.hide();

		var isEdit = objDialog.data("is_edit");

		if(isEdit === true){

			var catID = objDialog.data("catid");

			renameCategory(catID, catTitle);

			updateCatConditionsData(catID, conditionsData);


		}else{	//add

			var tab = objDialog.data("tab");
			var catID = addCatToTab(tab, catTitle, null, conditionsData);

		}

		objDialog.dialog("close");
	}

	/**
	 * update category extra data
	 */
	function updateCatConditionsData(catID, catData){

		var objCat = getCatByID(catID);

		if(!objCat)
			return(false);

		objCat.data("catdata", catData);

	}

	/**
	 * get category data
	 */
	function getConditionsCatData(catID){

		var objCat = getCatByID(catID);

		var data = objCat.data("catdata");

		if(!data)
			var data = {};

		return(data);
	}


	function ______________ACTIONS______________(){}


	/**
	 * add row from parameter
	 */
	function addParamRow(objParam, rowBefore, noEventTrigger){

		if(!rowBefore)
			var rowBefore = null;

		var html = getParamRowHtml(objParam);

		var objRow = jQuery(html).data("paramdata", objParam);

		//add after some row
		if(rowBefore){

			objRow.insertAfter(rowBefore);

		}else{		//add to bottom
			g_objTableBody.append(objRow);
			g_objEmptyParams.hide();
		}

		//add current category data
		if(g_temp.hasCats == true){

			var objCatIDs = getCatIDs();

			var currentCatID = getCurrentCatData("id");

			var catID = g_ucAdmin.getVal(objParam, "__attr_catid__");
			if(!catID)
				catID = currentCatID;

			//if wrong category - update the param for current category
			if(objCatIDs.hasOwnProperty(catID) == false)
				catID = currentCatID;

			objRow.data("catid", catID);

			if(catID != currentCatID && objCatIDs.hasOwnProperty(catID))
				objRow.hide();

		}

		g_objLastParam = objParam;

		//trigger change event
		if(noEventTrigger !== true)
			triggerEvent(t.events.UPDATE);

	}


	/**
	 * update row param
	 */
	function updateParamRow(rowIndex, objParam){

		if(typeof rowIndex == "object")
			var objRow = rowIndex;
		else
			var objRow = getParamsRow(rowIndex);

		var html = getParamRowHtml(objParam);
		var objNewRow = jQuery(html).data("paramdata", objParam);

		//update category related variables
		if(g_temp.hasCats == true){
			var catID = objRow.data("catid");
			objNewRow.data("catid", catID);
		}

		objRow.replaceWith(objNewRow);

		g_objLastParam = objParam;

		//trigger change event
		triggerEvent(t.events.UPDATE);
	}


	/**
	 * remvoe param row
	 */
	function removeParamRow(objRow){

		objRow.remove();

		var numParams = getNumParams();
		if(numParams == 0)
			g_objEmptyParams.show();

		g_objLastParam = null;

		//trigger change event
		triggerEvent(t.events.UPDATE);
	}



	/**
	 * duplicate param row
	 */
	function duplicateParamRow(objRow){

		var rowData = getRowData(objRow);
		var name = rowData.name;
		rowData.name = getDuplicateNewName(name);

		addParamRow(rowData, objRow);
	}


	function ______________EVENTS______________(){}



	/**
	 * trigger internal event
	 */
	function triggerEvent(eventName, params){
		if(!params)
			var params = null;

		g_objWrapper.trigger(eventName, params);
	}


	/**
	 * on internal event
	 */
	this.onEvent = function(eventName, func){
		g_objWrapper.on(eventName,func);
	};



	/**
	 * on delete param click
	 */
	function onDeleteParamClick(){

		var objRow = jQuery(this).parents("tr");
		removeParamRow(objRow);
	}


	/**
	 * on edit param click
	 */
	function onEditParamClick(){

		var objRow = jQuery(this).parents("tr");
		var paramData = getRowData(objRow);

		switch(paramData.type){
			case "uc_imagebase":
				alert("no edit yet, sorry. will be in the future working on it...");
				return(false);
			break;
		}


		var rowIndex = objRow.index();

		g_objDialog.open(paramData, rowIndex, function(objParam, rowIndex){
			updateParamRow(rowIndex, objParam);
		},g_type);

	}


	/**
	 * on add param button click
	 */
	this.onAddParamButtonClick = function(data){

		if(!data)
			var data = null;

		g_objDialog.open(data, null, function(objParam){
			addParamRow(objParam);
		},g_type);

	};


	/**
	 * on duplicate param click
	 */
	function onDuplicateParamClick(){

		var objRow = jQuery(this).parents("tr");
		duplicateParamRow(objRow);

	}

	/**
	 * on bulk param click, open bulk dialog
	 */
	function onBulkParamClick(){

		var objRow = jQuery(this).parents("tr");
		var paramData = getRowData(objRow);
		var data = {};

		var rowIndex = objRow.index();

		data["param_type"] = g_type;
		data["param_position"] = rowIndex;
		data["param_data"] = paramData;

		//trigger change event
		triggerEvent(t.events.BULK, data);

	}


	/**
	 * init events
	 */
	function initEvents(){

		g_objWrapper.on("click", ".uc-button-delete-param", onDeleteParamClick);
		g_objWrapper.on("click", ".uc-button-edit-param", onEditParamClick);
		g_objWrapper.on("click", ".uc-button-duplicate-param", onDuplicateParamClick);
		g_objWrapper.on("click", ".uc-button-bulk-param", onBulkParamClick);

		//init the sortable
		g_objTableBody.sortable({
			handle: ".uc-table-row-handle"
		});

		//add param button click
		g_buttonAddParam.on("click",function(){
			t.onAddParamButtonClick();
		});

		t.onEvent(t.events.UPDATE, function(){
			onUpdateInternal();
			g_temp.funcOnUpdate();
		});

		if(g_temp.hasCats == true){

			initCatsEvents();

		}

	}


	/**
	 * init addon params from object
	 * add rows according the object
	 */
	function initParamsFromObject(arrParams){

		if(!arrParams)
			return(false);

		jQuery.each(arrParams, function(index, objParam){
			addParamRow(objParam, null, true);
		});

		if(arrParams.length == 0)
			g_objEmptyParams.show();
		else
			g_objEmptyParams.hide();

		triggerEvent(t.events.UPDATE);

	}

	function ______________ITEMS_TYPE______________(){}


	/**
	 * add image base param - items type only
	 */
	function onAddImageBaseClick(){

		var isEnabled = g_ucAdmin.isButtonEnabled(g_buttonAddImageBase);
		if(isEnabled == false)
			return(false);

		var isExists = isParamTypeExists("uc_imagebase");
		if(isExists == true)
			return(false);

		var objParam = {};
		objParam["type"] = "uc_imagebase";
		objParam["name"] = "imagebase_fields";
		objParam["title"] = "Image Base Fields";

		addParamRow(objParam);
	}


	/**
	 * init items type related
	 */
	function initItemsType(){

		g_buttonAddImageBase = g_objWrapper.find(".uc-button-add-imagebase");
		g_buttonAddImageBase.on("click",onAddImageBaseClick);

		//update event - disable / enable button
		t.onEvent(t.events.UPDATE, function(){

			var isImageBaseExists = isParamTypeExists("uc_imagebase");

			if(isImageBaseExists == true){
				g_ucAdmin.disableButton(g_buttonAddImageBase);
			}else{
				g_ucAdmin.enableButton(g_buttonAddImageBase);
			}

		});

	}


	/**
	 * set on change event
	 */
	this.onUpdateEvent = function(func){
		g_temp.funcOnUpdate = func;
	}

	/**
	 * get last updated param
	 */
	this.getLastUpdatedParam = function(){

		return(g_objLastParam);
	}


	/**
	 * get control attributes with their values
	 */
	this.getControlParams = function(){

		var arrData = t.getParamsData("control", true);

		return(arrData);
	};


	/**
	 * init the params editor by wrapper and params
	 */
	this.init = function(objWrapper, objParams, objDialog, arrParamsCats){

		g_objWrapper = objWrapper;

		g_objCatsWrapper = g_objWrapper.find(".uc-attr-cats-wrapper");

		if(g_objCatsWrapper.length){
			g_temp.hasCats = true;
			initCatsFromData(arrParamsCats);
		}
		else
			g_objCatsWrapper = null;

		//set if items type
		var type = objWrapper.data("type");
		if(type == "items")
			g_temp.isItemsType = true;

		g_type = type;

		g_objTableBody = g_objWrapper.find(".uc-table-params tbody");
		g_objEmptyParams = g_objWrapper.find(".uc-text-empty-params");
		g_buttonAddParam = g_objWrapper.find(".uc-button-add-param");

		g_objDialog = objDialog;

		initEvents();

		if(g_temp.isItemsType == true)
			initItemsType();

		initParamsFromObject(objParams);

		initCopyCatSection();

	};


}
