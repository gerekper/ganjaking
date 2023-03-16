"use strict";

function UCManagerActionsAddons(){
	
	var g_objCats = new UCManagerAdminCats(), g_objItems;
	var g_manager = new UCManagerAdmin();
	var g_options, g_addonsType = "", g_emptyAddonsWrapper;
	var g_objTooltip, g_objListWrapper, g_objWrapper;
	var g_objBrowser = new UniteCreatorBrowser();
	var g_objSettingsCategory, g_inputShortcode, g_settingsPageProps;
	var g_objDailogPreview, g_objFilterCatalog, g_objFilterActive, g_objFilterSearch;
	var g_objBottomCopyPanel, g_objDialogPreviewTemplate, g_objGroupText;
	
	
	var t = this;
	
	var g_temp = {
			isLayout:false,
			iframe_screenshot_id: "uc_iframe_make_screenshot",
			url_screenshot_template: "",
			screenshot_numCurrent:0,
			screenshot_bulkItems:null,
			class_waiting_screenshot: "uc-waiting-screenshot",
			initHeight: 600,
			edit_group_id:null,
			edit_group_catid:null,
			is_edit_group_mode:false
	};
	
	if(!g_ucAdmin){
		var g_ucAdmin = new UniteAdminUC();
	}
	 
	
	/**
	 * run action function, return if found true/false
	 */
	function runActionFunctions(action,data){
		
		var arrActionFunctions = g_manager.getActionFunctions();
		if(!arrActionFunctions)
			return(false);
		
		if(arrActionFunctions.length == 0)
			return(false);
		
		jQuery.each(arrActionFunctions, function(index, func){
			if(typeof func != "function")
				throw new Error(func+" is not a function");
			
			var isFound = func(action, data);
			if(isFound == true)
				return(true);
		});
		
		return(false);
	}
	
	
	/**
	 * on item button click
	 */
	this.runItemAction = function(action, data){
		
		switch(action){
			case "add_addon":
				openAddAddonDialog();
			break;
			case "update_order":
				updateItemsOrder();
			break;
			case "select_all_items":
				g_objItems.selectUnselectAllItems();
			break;
			case "duplicate_item":
				duplicateItems();
			break;
			case "preview_addon":
				previewAddon();
			break;
			case "preview_thumb":
				previewThumbnail();
			break;
			case "make_screenshots":
				makeScreenshots();
			break;
			case "item_default_action":
				onItemDoubleClick();
			break;
			case "edit_addon":
				editAddon();
			break;
            case "edit_addon_blank":
            	editAddon(true);
            break;
			case "quick_edit":
				quickEdit();
			break;
			case "remove_item":
				removeSelectedAddons();				
			break;
			case "test_addon":
				testAddon();
			break;
			case "test_addon_blank":
				testAddon(true);
			break;
			case "move_items":
				moveAddons(data);
			break;
			case "copymove_move":
				onMoveOperationClick();
			break;
			case "get_cat_items":
				getSelectedCatAddons();
			break;
			case "import_addon":
				openImportAddonDialog();
			break;
			case "export_addon":
				exportAddon();
			break;
			case "activate_addons":
				activateAddons(true);
			break;
			case "deactivate_addons":
				activateAddons(false);
			break;
			case "export_cat_addons":
				var catID = data;
				exportCatAddons(catID);
			break;
			case "page_props":
				openPagePropertiesDialog();
			break;
			case "edit_layout_group":
				onEditLayoutGroupClick();
			break;
			case "copy":
				onCopyClick();
			break;
			default:
				
				var isFound = runActionFunctions(action, data);
				if(isFound == false)
					trace("wrong addon action: " + action);
				
			break;
		}
		
	}
	
	/**
	 * copy / move items
	 */
	function moveAddons(data, callback){
		
		//set status text
		var text = g_uctext.moving_addons;
		
		data = addCommonAjaxData(data);
		
		g_manager.ajaxRequestManager("move_addons",data , g_uctext.moving_addons, function(response){
			
			setHtmlListCombo(response);
			
			if(typeof callback == "function")
				callback(response);
		});
		
	}
	
	
	
	/**
	 * init items
	 */
	function initItems(){
		
		initAddAddonDialog();
		
		initPagePropsDialog();
		
		initQuickEditDialog();
		g_manager.initBottomOperations();
		
		initImportAddonDialog();
	
	}

	/**
	 * set combo lists from response
	 */
	function setHtmlListCombo(response){
				
		var htmlItems = response.htmlItems;
		var htmlCats = response.htmlCats;
		 
		g_objItems.setHtmlListItems(htmlItems);
		
		if(g_objCats)
			g_objCats.setHtmlListCats(htmlCats);
	}

	
	/**
	 * make some copy/move operation and close the dialog
	 */
	function onMoveOperationClick(){
		
		var objDrag = g_objItems.getObjDrag();
		
		var data = {};
		data.targetCatID = objDrag.targetItemID;
		data.selectedCatID = g_objCats.getSelectedCatID();
		data.arrAddonIDs = objDrag.arrItemIDs;
		
		moveAddons(data);

		g_objItems.resetDragData();
	}
	
	
	function ___________ADDONS_DIALOGS________________(){}


	/**
	 * add common ajax data
	 */
	function addCommonAjaxData(data){
		
		data["addontype"] = g_addonsType;
		data["manager_name"] = g_manager.getManagerName();
		
		var passData = g_manager.getManagerPassData();
		if(passData)
			data["manager_passdata"] = passData;
		
		
		return(data);
	}
	

	/**
	 * open page properties dialaog
	 */
	function openPagePropertiesDialog(){
		
		var selectedItem = g_objItems.getSelectedItem();
		var pageTitle = selectedItem.data("title");
		var layoutID = selectedItem.data("id");
		var dialogID = "uc_dialog_addon_properties";
				
		var options = {
				minWidth: 900,
				title:"Edit Page: "+pageTitle
		};
		
		var objDialog = jQuery("#" + dialogID);
		g_ucAdmin.validateDomElement(objDialog, "dialog properties");
		
		var objLoader = objDialog.find(".uc-settings-loader");
		g_ucAdmin.validateDomElement(objLoader, "loader");
		
		
		var objContent = objDialog.find(".uc-settings-content");
		g_ucAdmin.validateDomElement(objContent, "content");
		
		objContent.html("").hide();
		objLoader.show();
		
				
		g_ucAdmin.openCommonDialog("#uc_dialog_addon_properties", function(){
			
			var data = {"id":layoutID};
	        
			data = addCommonAjaxData(data);	
			
			g_ucAdmin.ajaxRequest("get_layouts_params_settings_html", data, function(response){
				
				objLoader.hide();
				objContent.show().html(response.html);
				
				//init settings
				var objSettingsWrapper = objContent.find(".unite_settings_wrapper");
				g_ucAdmin.validateDomElement(objSettingsWrapper, "page properties settings wrapper");
				
				g_settingsPageProps = new UniteSettingsUC();
				g_settingsPageProps.init(objSettingsWrapper);
				
			});
						
		} ,options);
		
	}
	
	/**
	 * init page properties dialog
	 */
	function initPagePropsDialog(){
		
		var objButton = jQuery("#uc_dialog_addon_properties_action");
		if(objButton.length == 0)
			return(false);
		
		objButton.on("click",function(){
			
			var selectedItemID = g_objItems.getSelectedItemID();
			var selectedItem = g_objItems.getSelectedItem();
			
			var data = {"layoutid":selectedItemID};
			data.params = g_settingsPageProps.getSettingsValues();
			data["from_manager"] = true;
			
			data = addCommonAjaxData(data);
			
			g_ucAdmin.dialogAjaxRequest("uc_dialog_addon_properties", "update_layout_params", data, function(response){
				
				g_objItems.replaceItemHtml(selectedItem, response.html_item);
				
			});
		});
		
		
	}
	
	/**
	 * check and fill addon name from title
	 */
	function checkFillAddonNameFromTitle(){
		
		var objTitle = jQuery(this);
		var objName = jQuery("#dialog_add_addon_name")
		
		var title = objTitle.val();
		
		//make all the validations
		var isAscii = g_ucAdmin.isStringAscii(title);
		
		if(isAscii == false)
			return(true);
				
		//validate that name is empty
		
		var name = objName.val();
		name = jQuery.trim(name);
		if(name)
			return(true);
		
		//convert and set the name
		name = g_ucAdmin.getNameFromTitle(title);
		
		objName.val(name);	
	}
	
	
	/**
	 * init add addon dialog actions
	 */
	function initAddAddonDialog(){

		jQuery("#dialog_add_addon_action").on("click",addAddon);
		
		jQuery("#dialog_add_addon_name").add("#dialog_add_addon_title").keyup(function(event){
			if(event.keyCode == 13)
				addAddon();
		});
		
		jQuery("#dialog_add_addon_title").on("blur", checkFillAddonNameFromTitle);
		
	}

	
	/**
	 * init quick edit dialog
	 */
	function initQuickEditDialog(){
		
		// set update title onenter function
		jQuery("#dialog_quick_edit_title").add("#dialog_quick_edit_name").keyup(function(event){
			if(event.keyCode == 13)
				updateItemTitle();
		});
		
	}
	
	
	/**
	 * on add addon click - open add addon dialog
	 */
	function openAddAddonDialog(){
		
		jQuery(".dialog_addon_input").val("");
		
		var options = {};
		options["no_close_button"] = true;
		options["minWidth"] = 400;
		
		g_ucAdmin.openCommonDialog("#dialog_add_addon", function(){
			jQuery("#dialog_add_addon_title").select();			
		}, options);
		
	}
	
	

	function ___________IMPORT_ADDONS_DIALOG________________(){}	//sap for outline	
	
	/**
	 * open import addon dialog
	 */
	function openImportAddonDialog(){
		
		var catData = g_objCats.getSelectedCatData();
		
		var catID = catData.id;
			
		if(!catID || catID == 0 || catID == "all"){
			catID = "";
			var catName = jQuery("#dialog_import_catname").data("text-autodetect");
		}else{
			catName = catData.title;
		}
		
		var parentID = getCurrentParentID();
		
		jQuery("#dialog_import_addons").data("catid", catID);
		jQuery("#dialog_import_addons").data("parentid", parentID);
		
		//reset dialog
		
		jQuery("#dialog_import_catname").val("autodetect");
		jQuery("#dialog_import_catname_specific").html(catName);
		
		jQuery("#dialog_import_addons_log").html("").hide();
		jQuery("#dialog_import_addons_action").show();
		
		//clear dropzone
		var objDialog = jQuery("#dialog_import_addons");
		var objDropzone = objDialog.data("dropzone");
		objDropzone.removeAllFiles();
		
		var options = {minWidth:700};
		options["no_close_button"] = true;
		
		g_ucAdmin.openCommonDialog("#dialog_import_addons", null, options);
		
	}
	
	
	/**
	 * init import addon dialog
	 */
	function initImportAddonDialog(){
		
		var objDialog = jQuery("#dialog_import_addons");
		
		var settingsDropzone = g_ucAdmin.getDropzoneSingleLineSettings();
		
		settingsDropzone.parallelUploads = 1;
		settingsDropzone.autoProcessQueue = false;
		
		settingsDropzone.params = {
	        addontype: g_addonsType
		};
		
		//init dropzone
		Dropzone.autoDiscover = false;
		var objDropzone = new Dropzone("#dialog_import_addons_form", settingsDropzone);
		
		//send file
		objDropzone.on("sending", function(file, xhr, formData){
			
			var catID = jQuery("#dialog_import_addons").data("catid");
			var parentID = jQuery("#dialog_import_addons").data("parentid");
			
			var isOverwrite = jQuery("#dialog_import_check_overwrite").is(":checked");
			var importType = jQuery("#dialog_import_catname").val();
			
			formData.append("catid", catID);
			formData.append("parentid", parentID);
			formData.append("isoverwrite", isOverwrite);
			formData.append("importtype", importType);
		});
		
		
		//on one file complete
		objDropzone.on("complete", function(response) {
			
			objDropzone.removeFile(response);
			
			var responseText = response.xhr.responseText;
			g_ucAdmin.setErrorMessageID("dialog_import_addons_error");
			g_ucAdmin.ajaxReturnCheck(responseText, function(objResponse){
								
				//store response
				objDialog.data("last_response", objResponse);
				
				//show log
				var objLog = jQuery("#dialog_import_addons_log");
				
				objLog.show();
				objLog.append(objResponse.import_log + "<br>");
				
				objDropzone.processQueue();
			});
			
		});
		
		
		//on all complete
		objDropzone.on("queuecomplete", function() {
			
			var lastResponse = objDialog.data("last_response");
			
			if(lastResponse)
				setHtmlListCombo(lastResponse);
			
		});
		
			
		objDialog.data("dropzone", objDropzone);
		
		//on action click
		var objDialog = objDialog;
		
		jQuery("#dialog_import_addons_action").on("click",function(){
	        
			var objDropzone = objDialog.data("dropzone");
			
			objDialog.data("last_response", null);
			
			objDropzone.processQueue();
		});
		
		
	}
	
	
	function ___________ADDONS_RELATED_OPERATIONS________________(){}	//sap for outline	
	
	
	/**
	 * on dialog add addon click
	 */
	function addAddon(){
		
		var selectedCatID = 0;
		
		if(g_objCats)
			selectedCatID = g_objCats.getSelectedCatID();
		
		var data = {
				title: jQuery("#dialog_add_addon_title").val(),
				name: jQuery("#dialog_add_addon_name").val(),
				description: jQuery("#dialog_add_addon_description").val(),
				catid: selectedCatID,
				addontype: g_addonsType
		};
		
		if(g_temp.is_edit_group_mode == true)
			data.parent_id = g_temp.edit_group_id;
		
		g_ucAdmin.dialogAjaxRequest("dialog_add_addon", "add_addon", data, function(response){

			var objItem = g_objItems.appendItem(response.htmlItem);
			
			//update categories list
			if(g_objCats)
				g_objCats.setHtmlListCats(response.htmlCats);
			
			g_objItems.selectSingleItem(objItem);
			
			//var urlAddon = response["url_addon"];
			//location.href = urlAddon;
			
		});
		
	}
	
	
	/**
	 * get item data from server
	 */
	function getItemData(itemID, callbackFunction){
				
		var data = {itemid:itemID};
		
		data = addCommonAjaxData(data);
		
		g_manager.ajaxRequestManager("get_item_data",data,g_uctext.loading_item_data,callbackFunction);
	}
	
	
	/**
	 * get category items
	 */
	function getSelectedCatAddons(){
		
		var catID = 0;
		
		if(g_objCats){
			catID = g_objCats.getSelectedCatID();
		}
				
		//--- parent template ID if available
		var parentID = null;
		if(g_temp.is_edit_group_mode == true){
			
			if(g_temp.edit_group_catid == catID){
				parentID = g_temp.edit_group_id;
				g_ucAdmin.validateNotEmpty(parentID, "parent template ID");
			}else{
				exitEditGroupMode();
			}
			
		}
		
		var catTitle = null;
		var isWeb = false;
		
		if(catID != 0 && catID != -1){
			var catData = g_objCats.getSelectedCatData();
			catTitle = catData["title"];
			isWeb = catData["isweb"];
		}
		
		jQuery("#items_loader").show();
		jQuery("#uc_list_items").hide();
		g_objItems.hideNoAddonsText();
		
		var catalogFilter = getFilterCatalog();
		var activeFilter = getFitlerActive();
		var searchFilter = getFilterSearch();
		
		if(catalogFilter == "mixed")
			activeFilter = "all";		//show all widgets when mixed
					
		var data = {};
		
		data = addCommonAjaxData(data);
		 
		data["catID"] = catID;
				
		data["filter_active"] = activeFilter;
		
		if(catalogFilter)
			data["filter_catalog"] = catalogFilter;
		
		if(searchFilter){
			data["filter_search"] = searchFilter;
			
			//exit group mode on search
			if(g_temp.edit_group_catid == catID){
				parentID = null;
				exitEditGroupMode();
			}
			
		}
		
		if(parentID)
			data["parent_id"] = parentID;
		
		data["response_combo"] = true;
		data["addontype"] = g_addonsType;
		data["title"] = catTitle;
		data["isweb"] = isWeb;
		
		g_ucAdmin.ajaxRequest("get_cat_addons", data, function(response){
			
			setHtmlListCombo(response);
			
			g_objItems.checkSelectRelatedItems();
			
			//patch for showing no addons html if not items on all
			if(catID == "all" && activeFilter == "all" && g_emptyAddonsWrapper){
				var numItems = jQuery("#uc_list_items li").length;
				if(numItems == 0){
					jQuery("#no_items_text").hide();
					g_emptyAddonsWrapper.show();
					g_manager.updateGlobalHeight(null, 390);
				}
			}
			
			//hide the bottom panel if available
			checkBottomCopyPanel();
			
		});
	}
	
	
	
	/**
	 * remove items
	 */
	function removeAddons(arrIDs){
		
		var data = {};
		data.arrAddonsIDs = arrIDs;
		
		data.catid = 0;
		
		if(g_objCats)
			data.catid = g_objCats.getSelectedCatID();
		
		data = addCommonAjaxData(data);
		
		g_manager.ajaxRequestManager("remove_addons",data, g_uctext.removing_addons, function(response){
			setHtmlListCombo(response);			
		});
		
	}
	
	
    /**
     * remove selected items
     */
    function removeSelectedAddons(){
		if(g_ucAdmin.isButtonEnabled(this) == false)
			return(false);
		
		if(confirm(g_uctext.confirm_remove_addons) == false)
			return(false);
		
		var arrIDs = g_objItems.getSelectedItemIDs();
				
		removeAddons(arrIDs);
		
		//hide the bottom panel if available
		hideBottomCopyPanel();	
		
    }


    /**
     * run addons view url
     */
    function runAddonsViewUrl(view, isNewWindow, addParam){
    	
    	var itemID = g_objItems.getSelectedItemID();
		if(itemID == null)
			return(false);

		var objItem = g_objItems.getSelectedItem();
		if(objItem.length == 0)
			return(false);
		
		var urlEdit = objItem.data("urledit");
				
		if(urlEdit){
			var urlViewEdit = urlEdit;
		}else{
			
			var urlViewEdit = g_ucAdmin.getUrlView(view, "id="+itemID);
		}
					
		if(addParam)		
			urlViewEdit += "&"+addParam;
				
		if(isNewWindow === true){
			window.open(urlViewEdit);
		}else{
			location.href = urlViewEdit;
		}
    	
    }

    
    /**
     * on item double click
     */
    function onItemDoubleClick(){
    	
		var objItem = g_objItems.getSelectedItem();
		if(objItem.length == 0)
			return(false);
    	
		if(g_temp.isLayout == false){
			editAddon();
			return(false);
		}
		
		var isGroup = objItem.data("isgroup");
		
		if(isGroup == true)
			onEditLayoutGroupClick();
		else
			editAddon(true);
		
    }
    
    
	/**
	 * edit item operation. open quick edit dialog
	 */
	function editAddon(isNewWindow){
		
		var view = "addon";
		var addParam = "";
	
		if(g_temp.isLayout == true){
			//view = "layout_outer";			//for blox layouts
			//addParam = "ucwindow=blank";
		}
		
		runAddonsViewUrl(view, isNewWindow, addParam);
	}
	
	
	/**
	 * preview addon
	 */
	function previewAddon(){
    	
		var itemID = g_objItems.getSelectedItemID();
		if(itemID == null)
			return(false);
		
		
		if(g_temp.isLayout == false){
			
			var urlPreview = g_ucAdmin.getUrlView("testaddon", "id="+itemID+"&preview=1");
			
		}else{
			
			//in case of layout
			
			var objItem = g_objItems.getSelectedItem();
			var urlPreview = objItem.data("urlview");
			
			if(!urlPreview)
				var urlPreview = g_ucAdmin.getUrlView("layout_preview", "id="+itemID, true);
			
		}
		
		window.open(urlPreview);
	}
	
	
	
	
	/**
	 * preview thumb
	 */
	function previewThumbnail(){
		
		var objItem = g_objItems.getSelectedItem();
		if(!objItem)
			return(false);
		
		var urlImage = g_objItems.getItemThumbImageUrl(objItem);
		
		if(!urlImage){
			alert("no thumb image found");
			return(false);
		}
		
		window.open(urlImage);		
	}
	
	
	/**
	 * test addon
	 */
	function testAddon(isNewWindow){
		runAddonsViewUrl("testaddon", isNewWindow);
	}
	
	/**
	 * export selected addon
	 */
	function exportAddon(){
		
		var arrIDs = g_objItems.getSelectedItemIDs();
		
		if(arrIDs.length == 0)
			return(false);
		
		var addonID = arrIDs[0];
		
		var params = "id="+addonID;
		
		if(g_temp.isLayout == true){	//export layouts
			
			var urlExport = g_ucAdmin.getUrlAjax("export_layout", params);
			
		}else{		//export addons
			
			params += "&addontype="+g_addonsType;
			var urlExport = g_ucAdmin.getUrlAjax("export_addon", params);
			
		}
				
		location.href=urlExport;
	}
	
	
	/**
	 * export category addons
	 */
	function exportCatAddons(catID){
		
		var params = "catid=" + catID;
		var urlExport = g_ucAdmin.getUrlAjax("export_cat_addons", params);
		
		location.href = urlExport;
	}
	
	
	/**
	 * edit item title function
	 */
	function quickEdit(){
		
		var arrIDs = g_objItems.getSelectedItemIDs();
		
		if(arrIDs.length == 0)
			return(false);
		
		var itemID = arrIDs[0];
		
		var objItem = g_objItems.getItemByID(itemID);
		if(objItem.length == 0)
			throw new Error("item not found: "+itemID);
		
		var title = objItem.data("title");
		var name = objItem.data("name");
		var description = objItem.data("description");
		
		var objDialog = jQuery("#dialog_edit_item_title");
		
		jQuery("#dialog_quick_edit_title").val(title).focus();
		jQuery("#dialog_quick_edit_name").val(name);
		jQuery("#dialog_quick_edit_description").val(description);
		
		var objIsFree = jQuery("#dialog_quick_isfree");
		if(objIsFree.length){
			var isFree = objItem.data("isfree");
			if(isFree == true)
				objIsFree.prop("checked", "checked");
			else
				objIsFree.prop("checked", false);				
		}
		
		var buttonOpts = {};
		
		buttonOpts[g_uctext.cancel] = function(){
			jQuery("#dialog_edit_item_title").dialog("close");
		};
		
		buttonOpts[g_uctext.update] = function(){
			updateItemTitle();
		}
		
		objDialog.data("itemid", itemID);
		
		objDialog.dialog({
			dialogClass:"unite-ui",			
			buttons:buttonOpts,
			minWidth:500,
			modal:true,
			open:function(){
				jQuery("#dialog_quick_edit_title").select();
			}
		});
		
	}
	
	
	/**
	 * update item title - on dialog update press
	 */
	function updateItemTitle(){
		
		var objDialog = jQuery("#dialog_edit_item_title");
		var itemID = objDialog.data("itemid");
		
		var objItem = g_objItems.getItemByID(itemID);
		if(objItem.length == 0)
			throw new Error("item not found: "+itemID);
		
		var titleHolder = objItem.find(".uc-item-title");
		var descHolder = objItem.find(".uc-item-description");
		
		var newTitle = jQuery("#dialog_quick_edit_title").val();
		var newName = jQuery("#dialog_quick_edit_name").val();
		var newDesc = jQuery("#dialog_quick_edit_description").val();
		
		var data = {
			itemID: itemID,
			title: newTitle,
			name: newName,
			description: newDesc
		};
		
		//add is free for master
		var objIsFree = jQuery("#dialog_quick_isfree");
		if(objIsFree.length != 0)
			data.isfree = objIsFree.is(":checked");
		
		objDialog.dialog("close");
		
		//update the items
		objItem.data("title", newTitle);
		objItem.data("name", newName);
		objItem.data("description", newDesc);
		
		titleHolder.html(newTitle);
		
		var showDesc = "";
		if(newDesc)
			showDesc = newDesc;
		
		descHolder.html(showDesc);
		
		data = addCommonAjaxData(data);
			
		g_manager.ajaxRequestManager("update_addon_title",data,g_uctext.updating_addon_title, function(){
			
			if(objIsFree.length)
				getSelectedCatAddons();
			
		});
		
	}
	
	
	/**
	 * duplicate items
	 */
	function duplicateItems(){
		
		var arrIDs = g_objItems.getSelectedItemIDs();
		if(arrIDs.length == 0)
			return(false);
		
		var selectedCatID = 0;
		
		if(g_objCats)
			selectedCatID = g_objCats.getSelectedCatID();
		
		if(selectedCatID == -1)
			return(false);
		
		var parentID = getCurrentParentID();
		
		var data = {
			arrIDs: arrIDs,
			catID: selectedCatID
		};
		
		if(parentID)
			data["parentID"] = parentID;
		
		data = addCommonAjaxData(data);
		
		g_manager.ajaxRequestManager("duplicate_addons",data,g_uctext.duplicating_addons,function(response){
			setHtmlListCombo(response);
		});
		
	}
	
	
	/**
	 * update items order in server
	 */
	function updateItemsOrder(){
		
		var arrIDs = g_objItems.getArrItemIDs(false, true);
		
		var data = {addons_order:arrIDs};
		
		data = addCommonAjaxData(data);
		
		g_manager.ajaxRequestManager("update_addons_order",data,g_uctext.updating_addons_order);
	}
	
	
	/**
	 * activate selected addons
	 */
	function activateAddons(isActive){
		var arrIDs = g_objItems.getSelectedItemIDs();
		
		g_objItems.acivateSelectedItems(isActive, true);
		
		var data = {addons_ids:arrIDs,is_active:isActive};
		
		data = addCommonAjaxData(data);

		g_manager.ajaxRequestManager("update_addons_activation",data,g_uctext.updating_addons);
	}
	
	function ___________BOTTOM_PANEL_MOVE________________(){}	

	/**
	 * hide bottom copy panel
	 */
	function hideBottomCopyPanel(){
		
		if(!g_objBottomCopyPanel)
			return(false);
		
		g_objBottomCopyPanel.hide();
	}
	
	/**
	 * check copy panel status when folder change
	 */
	function checkBottomCopyPanel(){
		
		if(!g_objBottomCopyPanel)
			return(false);
		
		if(g_objBottomCopyPanel.is(":visible") == false)
			return(false);
		
		var folderItemIDs = g_objItems.getArrItemIDs();
		
		var copiedIDs = g_objBottomCopyPanel.data("item_ids");
		
		var isFound = g_ucAdmin.isArrIncludesAnotherArrItem(folderItemIDs, copiedIDs);
		
		var objButtonMove = g_objBottomCopyPanel.find(".uc-button-copypanel-move");
		
		//check that one contains the others
		if(isFound == true){
			objButtonMove.addClass("button-disabled");
		}else{
			objButtonMove.removeClass("button-disabled");
		}
		
		
	}
	
	
	/**
	 * copy single item click
	 */
	function onCopyClick(){
		
		g_ucAdmin.validateDomElement(g_objBottomCopyPanel, "bottom copy panel");
		
		var arrIDs = g_objItems.getSelectedItemIDs();
		
		if(arrIDs.length == 0)
			return(false);
		
		g_objBottomCopyPanel.data("item_ids", arrIDs);
		
		//set title
		if(arrIDs.length == 1){
			var objItem = g_objItems.getSelectedItem();
			var title = objItem.data("title");
		}else{		//multiple names
			
			var title = arrIDs.length + " templates";
		}
		
		g_objBottomCopyPanel.find(".uc-copypanel-addon").html(title);
		
		//disable move button
		var objButtonMove = g_objBottomCopyPanel.find(".uc-button-copypanel-move");
		objButtonMove.addClass("button-disabled");
		
		
		g_objBottomCopyPanel.show();
		
	}
	
	/**
	 * get current category parent id
	 */
	function getCurrentParentID(){
		
		var parentID = null;
		if(g_temp.is_edit_group_mode == true)
			parentID = g_temp.edit_group_id;
		
		return(parentID);
	}
	
	
	/**
	 * on bottom panel move click
	 */
	function onBottomCopyPanelMoveClick(){
		
		var objLink = jQuery(this);
		
		if(objLink.hasClass("button-disabled") == true)
			return(true);
		
		//get category and parent ID if group enabled
		var catID = g_objCats.getSelectedCatID();
		var parentID = getCurrentParentID();
		
		var copiedIDs = g_objBottomCopyPanel.data("item_ids");
		
		var data = {};
		data.arrAddonIDs = copiedIDs;
		data.targetCatID = catID;
		data.selectedCatID = catID;
		data.parentID = parentID;
		
		moveAddons(data, function(response){
			
			hideBottomCopyPanel();
			
		});
				
	}
	
	
	
	function ___________GROUP_MODE________________(){}	
	
	
	/**
	 * exit edit group mode to category
	 */
	function exitEditGroupMode(){
		
		g_temp.is_edit_group_mode = false;
		g_temp.edit_group_id = null;
		g_temp.edit_group_catid = null;
		
		g_objWrapper.removeClass("uc-mode-edit-group");
		
		g_objGroupText.html("");
	}
	
	/**
	 * on exit click, exit the group and reload the templates
	 */
	function onExitGroupButtonClick(){
		
		exitEditGroupMode();
		
		getSelectedCatAddons();
	}
	
	/**
	 * set edit group mode
	 */
	function setEditGroupMode(itemID, objItemGroup){
		
		g_temp.is_edit_group_mode = true;
		g_temp.edit_group_id = itemID;
		g_temp.edit_group_catid = g_objCats.getSelectedCatID();
		
		g_objWrapper.addClass("uc-mode-edit-group");
		
		var title = objItemGroup.data("title");
		g_objGroupText.html(title);

		//clear search filter
		clearFilterSearch();
		
		getSelectedCatAddons();
	}
	
	
	/**
	 * on back button click
	 */
	function onGroupBackClick(){
		
		exitEditGroupMode();
		getSelectedCatAddons();
		
	}
	
	
	/**
	 * go to edit layout addon mode
	 */
	function onEditLayoutGroupClick(){
		
		var itemID = g_objItems.getSelectedItemID();
		if(!itemID)
			return(true);
		
		var objItem = g_objItems.getSelectedItem();
		
		setEditGroupMode(itemID, objItem);
	}
	

	function ___________SCREENSHOTS________________(){}	
	
	
	/**
	 * make item thumbnail
	 */
	function makeItemThumb(objItem){
		
		var layoutID = objItem.data("id");
		if(!layoutID)
			return(false);
		
		objItem.addClass("uc-making-thumb");
		
		var objThumb = objItem.find(".uc-item-thumb");
		if(objThumb.length == 0)
			return(false);
		
		objThumb.css("background-image", "url('')");
		
		var iframeID = g_temp.iframe_screenshot_id;
		
		//remove if existing
		var objIframe = jQuery("#" + iframeID);
		if(objIframe.length)
			objIframe.remove();
		
		var urlPreview = g_temp.url_screenshot_template.replace("id=0", "id="+layoutID);
		
		var htmlIframe = "<iframe id='"+iframeID+"' src='"+urlPreview+"' width='1000' style='width:1000px;'></iframe>";
		
		jQuery("body").append(htmlIframe);
		
		return(true);
	}
	
	
	/**
	 * on saved screenshot event, show the thumb image, continue to make screenshots if on the list
	 */
	function onScreenshotSaved(event, response){
		
		var layoutID = g_ucAdmin.getVal(response, "layoutid");
		var urlScreenshot = g_ucAdmin.getVal(response, "url_screenshot");
		
		g_objItems.updateItemScreenshot(layoutID, urlScreenshot);
		
		var objItem = g_objItems.getItemByID(layoutID);
		
		objItem.removeClass("uc-making-thumb");
		
		//for many screenshots
		if(objItem.hasClass(g_temp.class_waiting_screenshot)){
			
			objItem.removeClass(g_temp.class_waiting_screenshot);
			makeScreenshots(true);
		}
		
		
	}
	
	
	/**
	 * make screenshots
	 */
	function makeScreenshots(isContinue){
		
		var objItems = g_objItems.getSelectedItems(true);		
				
		if(isContinue === true){
			
			objItems = objItems.filter("."+g_temp.class_waiting_screenshot);
			if(objItems.length == 0){
				alert("finished making thumbnails");
			}
			
		}else{
			
			if(objItems.length > 1)
				objItems.addClass(g_temp.class_waiting_screenshot);
		}
		
		if(objItems.length == 0)
			return(true);
		
		var firstItem = objItems.first();
		
		makeItemThumb(firstItem);
		
	}
	
	
	
	function ___________FILTERS________________(){}	
		
	
	/**
	 * get active / not active filter
	 */
	function getFitlerActive(){
		
		if(!g_objFilterActive)
			return("all");
		
		var stateActive = g_objFilterActive.val();
				
		return(stateActive);
	}
	
	
	/**
	 * get catalog filter
	 */
	function getFilterCatalog(){
		
		if(g_objFilterCatalog == null)
			return("");
		
		var isChecked = g_objFilterCatalog.is(":checked");
		
		if(isChecked == true)
			var state = g_objFilterCatalog.data("state_active");
		else
			var state = g_objFilterCatalog.data("state_notactive");
				
		return(state);
	}
	
	
	/**
	 * get search filter
	 */
	function getFilterSearch(){
		
		var objInputSearch = jQuery("#uc_manager_addons_input_search");
		if(objInputSearch.length == 0)
			return("");
		
		var filterSearch = objInputSearch.val();
		
		return(filterSearch);
	}
	
	
	/**
	 * clear the search filter if exists
	 */
	function clearFilterSearch(){
		
		var objInputSearch = jQuery("#uc_manager_addons_input_search");
		var objIconSearch = jQuery("#uc_manager_addons_icon_search");
		var objIconClear = jQuery("#uc_manager_addons_clear_search");
		
		if(objInputSearch.length == 0)
			return(false);
		
		objInputSearch.val("");
		objIconSearch.show();
		objIconClear.hide();
		
	}
	
	
	/**
	 * init search filter
	 */
	function initFilterSearch(){
		
		//init search button
		var objInputSearch = jQuery("#uc_manager_addons_input_search");
		
		if(objInputSearch.length == 0)
			return(false);
		
		var objIconSearch = jQuery("#uc_manager_addons_icon_search");
		
		var objIconClear = jQuery("#uc_manager_addons_clear_search");
		
		//init search input
		objInputSearch.val("");
		
		
		/**
		 * on icon click
		 */
		objIconSearch.click(function(){
			
			var searchValue = objInputSearch.val();
			searchValue = jQuery.trim(searchValue);
			
			if(!searchValue)
				objInputSearch.focus();
			else
				getSelectedCatAddons();
			
		});
		
		objInputSearch.doOnEnter(function(){
			getSelectedCatAddons();
		});
		
		g_ucAdmin.onChangeInputValue(objInputSearch, function(){
			
			var searchValue = objInputSearch.val();
			searchValue = jQuery.trim(searchValue);
			
			//handle clear icon
			if(searchValue){
				//objIconClear.css("visibility", "visible");
				objIconClear.show();
				objIconSearch.hide();
			}
			else{
				objIconClear.hide();
				objIconSearch.show();				
			}
			
			getSelectedCatAddons();
		});
		
		
		//clear
		objIconClear.click(function(){
			
			objIconClear.hide()
			objIconSearch.show();
			
			objInputSearch.val("");
			
			getSelectedCatAddons();
		});
		
		
	}
	
	
	/**
	 * on filter link click
	 */
	function onFilterClick(){
		
		var objFilterSetActive = g_objWrapper.find(".uc-filter-set-active");
		//g_ucAdmin.validateDomElement(objFilterSetActive, "filter set - active");
		
		//update filters
		var filterCatalog = getFilterCatalog();
		
		if(objFilterSetActive.length){
			if(filterCatalog == "installed")
				objFilterSetActive.show();
			else
				objFilterSetActive.hide();
		}
		
		getSelectedCatAddons();
		
	}
	
	
	/**
	 * init filters
	 */
	function initFilters(){
						
		g_objFilterCatalog = jQuery("#uc_filter_catalog_installed");
		if(g_objFilterCatalog.length == 0)
			g_objFilterCatalog = null;
		
		g_objFilterActive = jQuery("#uc_manager_filter_active");
		if(g_objFilterActive.length == 0)
			g_objFilterActive = null;
				
		//g_ucAdmin.validateDomElement(g_objFilterActive, "filter catalog checkbox");
		
		if(g_objFilterCatalog)
			g_objFilterCatalog.on("click", onFilterClick);
		
		if(g_objFilterActive)
			g_objFilterActive.on("change", onFilterClick);
		
		initFilterSearch();
		
	}
	
	function ___________TOOLTIP________________(){}	
	
	
	/**
	 * init tooltip
	 */
	function initTooltip(){
		g_objTooltip = jQuery("#uc_manager_addon_preview");
		if(g_objTooltip.length == 0)
			g_objTooltip = null;
		
	}
	
	
	/**
	 * check if the item has tooltip
	 */
	function checkShowTooltip(objItem){
		
		if(!g_objTooltip)
			return(false);
		
		//check if item has preview
		var urlPreview = objItem.data("preview");
		if(!urlPreview)
			return(false);
		
		//show tooltip
		g_objTooltip.show();
		
		var gapTop = 10;
		var gapLeft = 10;
		
		var itemWidth = objItem.width();
		var tooltipHeight = g_objTooltip.height();
		var tooltipWidth = g_objTooltip.width();
		
		var maxLeft = g_objWrapper.width() - tooltipWidth;
		
		var pos = g_objItems.getItemWrapperPos(objItem);
		pos.top = pos.top - tooltipHeight + gapTop;
		pos.left = pos.left + itemWidth - gapLeft;
		
		if(pos.left > maxLeft)
			pos.left = maxLeft;
		
		//set position and image
		
		var objCss = {top:pos.top+"px",left:pos.left+"px"};
		objCss["background-image"] = "url('"+urlPreview+"')";
		
		g_objTooltip.css(objCss);
								
	}
	
	
	/**
	 * hide tooltip if avilable
	 */
	function hideTooltip(){
		
		if(!g_objTooltip)
			return(false);
		
		var objCss = {};
		objCss["background-image"] = "";
		
		g_objTooltip.hide();
		g_objTooltip.css(objCss);
	}
	
	function ___________EDIT_CATEGORY_DIALOG______________(){}	
	
	/**
	 * open category dialog
	 */
	function openCategoryDialog(objDialog, catID){
		
		var data = {};
		data["catid"] = catID;
		
		var objLoader = jQuery("#uc_dialog_edit_category_settings_loader");
		var objContent = jQuery("#uc_dialog_edit_category_settings_content");
		
		objContent.hide();
		objLoader.show();
		
		data = addCommonAjaxData(data);
		
		g_manager.ajaxRequestManager("get_category_settings_html",data , g_uctext.loading, function(response){
			
			objLoader.hide();
			objContent.show();
			
			objContent.html(response.html);
			
			if(g_objSettingsCategory)
				g_objSettingsCategory.destroy();
			
			var objSettingsWrapper = objContent.find(".unite_settings_wrapper");
			
			g_ucAdmin.validateDomElement(objSettingsWrapper, "edit category settings wrapper");
			
			g_objSettingsCategory = new UniteSettingsUC();
			g_objSettingsCategory.init(objSettingsWrapper);
			
		});
		
	}
	
	
	/**
	 * update category click
	 */
	function onUpdateCategoryClick(){
		
		var objDialog = jQuery("#uc_dialog_edit_category");
		
		var catID = objDialog.data("catid");
		var catData = g_objSettingsCategory.getSettingsValues();
		
		var catTitle = catData["category_title"];
		
		var data = {
			cat_id: catID,
			cat_data: catData
		};
		
		g_ucAdmin.dialogAjaxRequest("uc_dialog_edit_category", "update_category", data, function(){
			
			g_objCats.updateTitle(catID, catTitle);
		});
		
	}
	
	
	/**
	 * init edit category dialog
	 */
	function initEditCategoryDialog(){
		
		if(!g_objCats)
			return(false);
		
		g_objCats.events.onOpenCategoryDialog = openCategoryDialog; 
		
		jQuery("#uc_dialog_edit_category_action").on("click",onUpdateCategoryClick);
		
	}
	
	function ___________TEMPLATES_PREVIEW_DIALOG________________(){}	

	
	/**
	 * on web layout click
	 * open the layout preview and install dialog
	 */
	function onWebTemplateClick(objItem){
		
		var isGroup = objItem.data("isgroup");
		
		if(isGroup == true){
			var itemName = objItem.data("name");
			setEditGroupMode(itemName, objItem);
			
			return(false);
		}
				
		if(!g_objDialogPreviewTemplate)
			return(false);
		
		//-- small reset if error message
		
		g_objDialogPreviewTemplate.removeClass("uc-loading-mode");
		g_objDialogPreviewTemplate.removeClass("uc-just-imported");
		
		//---- set the top button - next, prev states
		
		var objButtonNext =  g_objDialogPreviewTemplate.find("#uc_dialog_import_template_button_next");
		var objButtonPrev = g_objDialogPreviewTemplate.find("#uc_dialog_import_template_button_prev");
		
		var objNextItem = g_objItems.getNextItem(objItem);
		var objPrevItem = g_objItems.getPrevItem(objItem);
		
		var classDisabled = "uc-button-disabled";
		
		if(objNextItem)
			objButtonNext.removeClass(classDisabled);
		else
			objButtonNext.addClass(classDisabled);

		if(objPrevItem)
			objButtonPrev.removeClass(classDisabled);
		else
			objButtonPrev.addClass(classDisabled);
		
		//------ 
		
		var isImported = objItem.data("isimported")
		
		//save the item
		g_objDialogPreviewTemplate.data("current_item", objItem);
		
		//set title
		var objTitle = g_objDialogPreviewTemplate.find(".uc-dialog-preview-template__title");
		var templateTitle = objItem.data("title");
		var state = objItem.data("state");
		
		objTitle.html(templateTitle);
		
		//set image
		var urlPreviewImage = objItem.data("preview");
		var objImage = g_objDialogPreviewTemplate.find(".uc-dialog-preview-template__image");
		
		objImage.attr("src", urlPreviewImage);
		
		//clear the create page input
		var objPageName =  g_objDialogPreviewTemplate.find(".uc-dialog-preview-template__page-name");
		
		objPageName.html("");
		
		//if state - is pro, add pro class, hide the operations
		if(state == "pro"){
			g_objDialogPreviewTemplate.addClass("uc-pro-mode");
		}else
			g_objDialogPreviewTemplate.removeClass("uc-pro-mode");
			
		
		//if already imported - show message dialog
		if(isImported === true){
			
			g_objDialogPreviewTemplate.addClass("uc-imported-mode");
			
			var objLinks = {};
			objLinks.url = objItem.data("linkview");
			objLinks.url_edit = objItem.data("linkedit");
			
			previewDialog_showMessageAfterImport("top", objLinks);
			
		}else{
			
			g_objDialogPreviewTemplate.removeClass("uc-imported-mode");
			
			//hide the message dialog
			var objDialogMessage = jQuery("#uc_dialog_import_template_imported_message");
			objDialogMessage.hide();
		}
		
		//show the template dialog
		g_objDialogPreviewTemplate.show();
		
	}
	
	
	/**
	 * show message after import
	 */
	function previewDialog_showMessageAfterImport(position, response){
		
		var urlTemplate = response.url;
		var urlTemplateEdit = response.url_edit;
		
		var objDialogMessage = jQuery("#uc_dialog_import_template_imported_message");
		var linkView = objDialogMessage.find(".uc-dialog-preview-template__imported-message-link1");
		var linkEdit = objDialogMessage.find(".uc-dialog-preview-template__imported-message-link2");
		
		var linkViewText = linkView.data("text-"+position);
		var linkEditText = linkEdit.data("text-"+position);
		
		linkView.html(linkViewText);
		linkEdit.html(linkEditText);
		
		linkView.attr("href", urlTemplate);
		linkEdit.attr("href", urlTemplateEdit);
		
		objDialogMessage.show();
		
		objDialogMessage.appendTo("#uc_dialog_import_template_imported_message_"+position);
		
	}
	
	/**
	 * on import template click
	 * imports template from the catalog to elementor template library
	 */
	function previewDialog_onImportClick(importAgain){
		
		g_objDialogPreviewTemplate.removeClass("uc-imported-mode");
		var objDialogMessage = jQuery("#uc_dialog_import_template_imported_message");
		objDialogMessage.hide();
		
		var objItem = g_objDialogPreviewTemplate.data("current_item");
		
		var objSuccessMessage = jQuery("#uc_dialog_import_template_success");
		
		var data = {};
		data.name = objItem.data("name");
		
		if(importAgain === true)
			data.import_again = true;
		
		g_ucAdmin.setAjaxLoaderID("uc_dialog_import_template_loader");
		g_ucAdmin.setErrorMessageID("uc_dialog_import_template_error");
		
		objSuccessMessage.hide();
		
		g_objDialogPreviewTemplate.addClass("uc-loading-mode");
		
		//g_ucAdmin.setSuccessMessageID("uc_dialog_import_template_success");
		
		g_ucAdmin.ajaxRequest("import_elementor_catalog_template", data, function(response){
			
			g_objDialogPreviewTemplate.removeClass("uc-loading-mode");
			g_objDialogPreviewTemplate.addClass("uc-just-imported");
			
			g_objDialogPreviewTemplate.data("was_import", true);
			
			previewDialog_showMessageAfterImport("top", response);
		});
		
	}
	
	
	/**
	 * import template again (overwrite the old)
	 */
	function previewDialog_onImportAgainClick(){
		
		var objImportAgain = jQuery(this);
		
		g_ucAdmin.validateDomElement(objImportAgain, "import again button");
		
		var message = objImportAgain.data("message-confirm");
				
		if(confirm(message) == false)
			return(false);
		else
			previewDialog_onImportClick(true);
		
	}
	
	/**
	 * create page from the template
	 */
	function previewDialog_onCreatePageClick(){

		var isLoading = g_objDialogPreviewTemplate.hasClass("uc-loading-mode");
		if(isLoading == true)
			return(false);
		
		g_objDialogPreviewTemplate.removeClass("uc-imported-mode");
		var objDialogMessage = jQuery("#uc_dialog_import_template_imported_message");
		objDialogMessage.hide();
		
		var objItem = g_objDialogPreviewTemplate.data("current_item");
		
		var objInput = g_objDialogPreviewTemplate.find(".uc-dialog-preview-template__page-name");
		
		var data = {};
		data.name = objItem.data("name");
		data.create_page = true;
		data.page_name = objInput.val();
		
		
		g_ucAdmin.setAjaxLoaderID("uc_dialog_import_template_createpage_loader");
		
		// -- error handling ---
		
		g_ucAdmin.setErrorMessageID(function(htmlError){
			jQuery("#uc_dialog_import_template_createpage_error").show().html(htmlError);
			g_objDialogPreviewTemplate.removeClass("uc-loading-mode");
		});
		
		g_ucAdmin.setErrorMessageOnHide(function(){
			jQuery("#uc_dialog_import_template_createpage_error").hide();
		});
		
		g_objDialogPreviewTemplate.addClass("uc-loading-mode");
		
		g_ucAdmin.ajaxRequest("import_elementor_catalog_template", data, function(response){
			
			g_objDialogPreviewTemplate.removeClass("uc-loading-mode");
			
			previewDialog_showMessageAfterImport("bottom", response);
			
		});
		
	}
	
	/**
	 * on next template click
	 */
	function previewDialog_onNextTemplateClick(){
		
		var objLink = jQuery(this);
		if(objLink.hasClass("uc-button-disabled"))
			return(false);
		
		var objItem = g_objDialogPreviewTemplate.data("current_item");
		if(!objItem)
			return(false);
		
		var objNextItem = g_objItems.getNextItem(objItem);
		
		if(!objNextItem)
			return(false);
		
		objNextItem.trigger("click");
		
	}
	
	
	/**
	 * on previous template click
	 */
	function previewDialog_onPreviousTemplateClick(){
		
		var objLink = jQuery(this);
		if(objLink.hasClass("uc-button-disabled"))
			return(false);
		
		var objItem = g_objDialogPreviewTemplate.data("current_item");
		if(!objItem)
			return(false);
		
		var objPrevItem = g_objItems.getPrevItem(objItem);
		
		if(!objPrevItem)
			return(false);
		
		objPrevItem.trigger("click");
		
	}
	
	/**
	 * on button close click
	 */
	function previewDialog_onButtonCloseClick(){
		
		g_objDialogPreviewTemplate.hide();
		
		var wasImport = g_objDialogPreviewTemplate.data("was_import");
		
		if(wasImport == true){
			g_objDialogPreviewTemplate.data("was_import", false);
			getSelectedCatAddons();
		}
			
		
	}
	
	
	function ___________EVENTS________________(){}	
	
	
	/**
	 * on hide empty text, hide no addons message as well
	 */
	function onItemHideEmptyText(){
		if(g_emptyAddonsWrapper)
			g_emptyAddonsWrapper.hide();
	}
	
		
	/**
	 * on item mouseover
	 */
	function onItemMouseover(event, item){
		var objItem = jQuery(item);
		
		if(g_objTooltip)
			checkShowTooltip(objItem);
		
		//handle label state
		g_objBrowser.onAddonHover(event, objItem);
		
	}
	
	
	/**
	 * on item mouseover
	 */
	function onItemMouseout(event, item){
		
		var objItem = jQuery(item);
		
		if(g_objTooltip)
			hideTooltip();
		
		g_objBrowser.onAddonHover(event, objItem);
	}
	
	
	
	/**
	 * on item click - if it's web and free addon - install
	 * if it's layout - go to on web click
	 */
	function onItemClick(objItem, itemType){
		
		if(itemType != "web")
			return(false);
		
		if(g_temp.isLayout == true){
			
			onWebTemplateClick(objItem);
			return(false);
		}
		
		
		var state = objItem.data("state");
		if(state != "free")
			return(false);
		
		var catData = g_objCats.getSelectedCatData();
		if(!catData)
			return(false);
		
		var catTitle = g_ucAdmin.getVal(catData, "title");
		
		g_objBrowser.installAddon(objItem, catTitle, function(response){
			
			if(g_temp.isLayout == false)
				var addonID = g_ucAdmin.getVal(response, "addonid");
			else
				var addonID = g_ucAdmin.getVal(response, "layoutid");
			
			g_ucAdmin.validateNotEmpty(addonID, "Addon ID or Layout ID");
			
			objItem.data("id", addonID);
			objItem.attr("id","uc_item_"+addonID);
			objItem.data("itemtype","");
		});
		
		return(true);
	}
	
	/**
	 * open preview dialog
	 */
	function openAddonPreviewDialog(objItem, urlPreview){
		
		g_ucAdmin.validateDomElement(g_objDailogPreview, "dialog preview");
		
		var addonTitle = objItem.data("title");	
		
		var objIframe = g_objDailogPreview.find("iframe");
		
		g_ucAdmin.validateDomElement(objIframe, "dialog iframe");
		
		objIframe.attr( "src", urlPreview );
		
		g_objDailogPreview.dialog({
			title: addonTitle,
			dialogClass:"unite-dialog-responsive",
			modal: true,
			width: 1020,
			height: 600	
		});
		
	}
	
	/**
	 * on dialog preview close - clear the iframe
	 */
	function onDialogPreviewClose(){
		
		var objIframe = g_objDailogPreview.find("iframe");
		objIframe.attr( 'src', "");
	}
	
	/**
	 * on item action click
	 */
	function onItemActionClick(event){
				
		//var obj
		var objButton = jQuery(this);
		var objItem = objButton.parents("li");
		
		g_objItems.selectSingleItem(objItem);
		
		var action = objButton.data("action");
		
		if(action == "open_menu")
			return(true);
		
		t.runItemAction(action);
		
	}
	
	
	/**
	 * on item button click
	 */
	function onItemButtonClick(event){
		
		var objButton = jQuery(this);
		
		//open preview dialog
		if(objButton.hasClass("uc-button-preview")){
			
			//don't open the dialog preview			
			event.stopPropagation();
			
			return(true);
			
			event.preventDefault();
			
			var objItem = objButton.parents("li");
			var urlPreview = objButton.data("url-link");
			
			openAddonPreviewDialog(objItem, urlPreview);
			
			event.stopPropagation();
			return(false);
		}
		
		if(objButton.hasClass("uc-button-free"))
			return(true);
		
		event.stopPropagation();
				
		return(true);
	}
	
	/**
	 * on update click
	 */
	function onUpdateCatalogClick(){
		
		var objButton = jQuery(this);
		var objIcon = objButton.find("i");
		objIcon.addClass("fa-spin");
		
		var isDebugMode = g_ucAdmin.isDebugMode();
		
		var data = {};
		data["force"] = true;
		
		g_ucAdmin.ajaxRequest("check_catalog", data, function(response){
			
			objIcon.removeClass("fa-spin");
			
			var message = g_ucAdmin.getVal(response,"message");
			var errorMessage = g_ucAdmin.getVal(response,"error_message");
			
			if(errorMessage)
				message += errorMessage;
			
			alert(message);
						
		});
		
		
	}
	
	/**
	 * on item action menu click
	 */
	function onItemActionMenuClick(event){
		
		var objButton = jQuery(this);
		var objItem = objButton.parents("li.uc-addon-thumbnail");
		g_ucAdmin.validateDomElement(objItem, "item object");
		
		var objMenu = jQuery("#rightmenu_item_actions");
		g_ucAdmin.validateDomElement(objMenu, "items action menu");
		
		g_manager.showMenuOnMousePos(event, objMenu);
		
	}

	
	/**
	 * init events
	 */
	function initEvents(){
		
		g_manager.onEvent(g_manager.events.ITEM_HIDE_EMPTY_TEXT, onItemHideEmptyText);
		g_manager.onEvent(g_manager.events.ITEM_MOUSEOVER, onItemMouseover);
		g_manager.onEvent(g_manager.events.ITEM_MOUSEOUT, onItemMouseout);
		
		g_objItems.events.onSpecialItemClick = onItemClick;
		
		g_ucAdmin.onEvent("screenshot_saved", onScreenshotSaved);
		
		//on addon button click		
		g_objDailogPreview.on( "dialogclose", onDialogPreviewClose );
		
		g_objListWrapper.on("click", ".uc-item-action-menu", onItemActionMenuClick);
		
		g_objListWrapper.on("click", "li .uc-addon-button", onItemButtonClick); 
		
		g_objListWrapper.on("click","li .uc-item-action", onItemActionClick);
		
		//group mode events
		var objGroup = g_objWrapper.find("#uc_manager_group");
		if(objGroup.length){
			
			var objButtonGroupBack = objGroup.find(".uc-manager-group-back");
			objButtonGroupBack.on("click", onExitGroupButtonClick);
		}
		
		//bottom copy panel
		if(g_objBottomCopyPanel){
			
			var objButtonMove = g_objBottomCopyPanel.find(".uc-button-copypanel-move");
			var objButtonCancel = g_objBottomCopyPanel.find(".uc-button-copypanel-cancel");
			
			objButtonMove.on("click", onBottomCopyPanelMoveClick);
			objButtonCancel.on("click", hideBottomCopyPanel);
		}
		
		//update catalog button
		var objButtonUpdate = g_objWrapper.find(".manager-button-update-catalog");
		if(objButtonUpdate.length)
			objButtonUpdate.on("click", onUpdateCatalogClick);
		
		//init preview template dialaog
		if(g_objDialogPreviewTemplate){
			
			var objButtonClose = g_objDialogPreviewTemplate.find("#uc_dialog_import_template_button_close");
			var objButtonNext =  g_objDialogPreviewTemplate.find("#uc_dialog_import_template_button_next");
			var objButtonPrev = g_objDialogPreviewTemplate.find("#uc_dialog_import_template_button_prev");
						
			var objButtonImport = g_objDialogPreviewTemplate.find(".uc-dialog-preview-template__button-import");
			var objButtonImportAgain = g_objDialogPreviewTemplate.find(".uc-dialog-preview-template__button-import-again");
			var objButtonCreatePage = g_objDialogPreviewTemplate.find(".uc-dialog-preview-template__button-create-page");
			
			objButtonClose.on("click", previewDialog_onButtonCloseClick);
			objButtonImport.on("click", previewDialog_onImportClick);
			objButtonImportAgain.on("click", previewDialog_onImportAgainClick);
			objButtonCreatePage.on("click", previewDialog_onCreatePageClick);
			objButtonNext.on("click", previewDialog_onNextTemplateClick);
			objButtonPrev.on("click", previewDialog_onPreviousTemplateClick);
			
		}
		
	}
	
	
	/**
	 * init shortcode input if available
	 */
	function initShortcode(){
		
		g_inputShortcode = g_objWrapper.find(".uc-filers-set-shortcode");
		if(g_inputShortcode.length == 0){
			g_inputShortcode = null;
			return(false);
		}
		
		g_inputShortcode.focus(function(){
			this.select();
		});
		
		g_manager.onEvent(g_manager.events.ITEM_SELECTION_CHANGE, function(){
			
			var objItem = g_objItems.getSelectedItem();
			if(!objItem)
				return(true);
			
			var itemID = objItem.data("id");
			var title = objItem.data("title");
			
			var shortcode = g_inputShortcode.data("template");
			
			shortcode = shortcode.replace("\"", "");
			shortcode = shortcode.replace("%id%", itemID);
			shortcode = shortcode.replace("%title%", title);
			
			g_inputShortcode.val(shortcode);
			
		});
				
	}
	
	
	/**
	 * init debug dialog
	 */
	function initDebugDialog(){
		
		var objDebugButton = g_objWrapper.find(".manager-button-debug-dialog");
		
		if(objDebugButton.length == 0)
			return(false);
		
		objDebugButton.click(function(){
			
			g_ucAdmin.openCommonDialog("uc_manager_dialog_debug");
		});
		
	}
	
	
	/**
	 * init the actions
	 */
	this.init = function(objManager){
		
		g_manager = objManager;
		
		//set addons type
		g_objWrapper = g_manager.getObjWrapper();
		g_addonsType = g_objWrapper.data("addonstype");
				
		g_options = g_objWrapper.data("options");
		
		g_temp.isLayout = g_ucAdmin.getVal(g_options, "is_layout");
		g_temp.url_screenshot_template = g_ucAdmin.getVal(g_options, "url_screenshot_template");
				
		g_manager.setObjAjaxAddData({addontype: g_addonsType});
		
		//init empty addons wrapper
		g_emptyAddonsWrapper = jQuery("#uc_empty_addons_wrapper");
		if(g_emptyAddonsWrapper.length == 0)
			g_emptyAddonsWrapper = null;
		
		//init cats
		g_objCats = g_manager.getObjCats();
		
		if(g_objCats)
			g_objCats.setObjAjaxAddData({type: g_addonsType});
		
		//init items
		g_objItems = g_manager.getObjItems();
		g_objItems.setSpacesBetween(15,15);
		
		g_objListWrapper = g_objItems.getObjListWrapper();
		
		//init templates preview dialog
		g_objDialogPreviewTemplate = jQuery("#uc_dialog_preview_template");
		if(g_objDialogPreviewTemplate.length == 0)
			g_objDialogPreviewTemplate = null;
		
		//init group related
		g_objGroupText = g_objWrapper.find(".uc-manager-group-text");
		if(g_objGroupText.length == 0)
			g_objGroupText = null;
				
		//g_manager.setNotUpdateHeight();
		
		g_manager.initItems();
		
		//init browser
		g_objBrowser.setAddonType(g_addonsType, g_temp.isLayout, true);
		
		g_objDailogPreview = jQuery('#uc_dialog_item_preview');
		
		//init bottom copy panel
		g_objBottomCopyPanel = g_objWrapper.find(".uc-bottom-copypanel");
		
		if(g_objBottomCopyPanel.length == 0)
			g_objBottomCopyPanel = null;
		
		initDebugDialog();
		
		initItems();
		
		initFilters();
		
		initTooltip();
		
		initEditCategoryDialog();
		
		initShortcode();
		
		initEvents();
		
		//var totalHeight = g_temp.initHeight;
		//g_manager.setTotalHeight(totalHeight);
		
	};
	
	
}