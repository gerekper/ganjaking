"use strict";

function UCManagerAdminCats(){
	
	var g_catClickReady = false;
	var g_catFieldRightClickReady = true;		//avoid double menu on cat field
	var g_maxCatHeight = 450;
	var g_manager, g_objAjaxDataAdd = null;
	var g_objListCats;
	
	
	//event functions
	this.events = {
		onRemoveSelectedCategory: function(){},
		onHeightChange: function(){},
		onOpenCategoryDialog:function(){}
	};
	
	
	var g_temp = {
			isInited: false
	};
	
	var t = this;
	

	function _______________INIT______________(){}
	
	/**
	 * validate that the object is inited
	 */
	function validateInited(){
		if(g_temp.isInited == false)
			throw new Error("The categories is not inited");
		
	}
	
	
	/**
	 * init the categories
	 */
	function initCats(objManager){
				
		if(g_temp.isInited == true)
			throw new Error("Can't init cat object twice");

		g_manager = objManager;
		g_objListCats = jQuery("#list_cats");
		
		g_temp.isInited = true;

		if(!g_ucAdmin)
			g_ucAdmin = new UniteAdminUC();		
		
		initEvents();
		
		//update sortable categories		
		try{
		g_objListCats.sortable({
			axis:'y',
			start: function( event, ui ) {
				g_catClickReady = false;
			},
			update: function(){
				updateCatOrder();
				//save sorting order
			}
		});	
		}catch(error){
			trace("error occured in jquery sortable!");
			trace(error);
		}
		
		initAddCategoryDialog();
		
		initEditCategoryDialog();
		
		initDeleteCategoryDialog();
	}
	
	
	function _______________GETTERS______________(){}
	
	/**
	 * 
	 * get category by id
	 */
	function getCatByID(catID){
		var objCat = jQuery("#category_" + catID);
		return(objCat);
	}
	
	
	/**
	 * get category data
	 */
	function getCatData(catID){
		
		var objCat = getCatByID(catID);
		if(objCat.length == 0)
			return(null);
		
		var data = {};
		data.id = catID;
		data.title = objCat.data("title");
		data.isweb = objCat.data("isweb");
		data.isweb = g_ucAdmin.strToBool(data.isweb);
				
		return(data);
	}
	
	
	/**
	 * check if some category selected
	 * 
	 */
	this.isCatSelected = function(catID){
		
		var selectedCatID = t.getSelectedCatID();
		
		if(catID == selectedCatID)
			return(true);
		
		return(false);
	};
	
	
	function _______________SETTERS______________(){}
	
	
	/**
	 * remove category from html
	 */
	function removeCategoryFromHtml(catID){
		
		jQuery("#category_"+catID).remove();
				
		//disableCatButtons();
	}
	
	
	/**
	 * set first category selected
	 */
	this.selectFirstCategory = function(){
		
		var arrCats = getArrCats();
		if(arrCats.length == 0)
			return(false);
		
		var firstCat = arrCats[0];
		
		var catID = jQuery(firstCat).data("id");
		t.selectCategory(catID);
	}
	
	
	/**
	 * run category action
	 */
	this.runCategoryAction = function(action, catID){
		
		if(!catID)
			var catID = t.getSelectedCatID();
		
		switch(action){
			case "add_category":
				openAddCategoryDialog();
			break;
			case "edit_category":
				openEditCategoryDialog(catID);
			break;
			case "delete_category":
				openDeleteCategoryDialog(catID);
			break;
			default:
				return(false);
			break;
		}
	
		return(true);
	}

	
	/**
	 * enable category buttons
	 */
	function enableCatButtons(){
		
		//cat butons:
		//g_ucAdmin.enableButton("#button_remove_category, #button_edit_category");
		
	}
	
	/**
	 * enable category buttons
	 */
	function disableCatButtons(){
		
		//g_ucAdmin.disableButton("#button_remove_category, #button_edit_category");
		
	}

	
	/**
	 * update categories order
	 */
	function updateCatOrder(){
		
		//get sortIDs
		var arrSortCats = jQuery( "#list_cats" ).sortable("toArray");
		var arrSortIDs = [];
		for(var i=0;i < arrSortCats.length; i++){
			var catHtmlID = arrSortCats[i];
			var catID = catHtmlID.replace("category_","");
			arrSortIDs.push(catID);
		}
		
		var data = {cat_order:arrSortIDs};
		g_manager.ajaxRequestManager("update_cat_order",data,g_uctext.updating_categories_order);
	}
	
	function _______________ADD_CATEGORY______________(){}
		
	/**
	 * add category
	 */
	function addCategory(){
		
		var data = {};
		data.catname = jQuery("#uc_dialog_add_category_catname").val();
		
		if(g_objAjaxDataAdd && typeof(data) == "object"){
			jQuery.extend(data, g_objAjaxDataAdd);
		}
		
		g_ucAdmin.dialogAjaxRequest("uc_dialog_add_category", "add_category", data, function(response){
			
			var html = response.htmlCat;
			
			jQuery("#list_cats").append(html);
			
			//update html cats select
			var htmlSelectCats = response.htmlSelectCats;
			jQuery("#select_item_category").html(htmlSelectCats);
			
			t.events.onHeightChange();
			
		});


	}
	
	
	/**
	 * open add category dialog
	 */
	function openAddCategoryDialog(){
		
		g_ucAdmin.openCommonDialog("#uc_dialog_add_category", function(){
			
			jQuery("#uc_dialog_add_category_catname").val("").focus();
			
		});
		
	}
	
	
	/**
	 * init add category dialog
	 */
	function initAddCategoryDialog(){
		
		jQuery("#uc_dialog_add_category_action").on("click",addCategory);
		
		// set update title onenter function
		jQuery("#uc_dialog_add_category_catname").keyup(function(event){
			if(event.keyCode == 13)
				addCategory();
		});
		
	}
	
	function _______________EDIT_CATEGORY______________(){}
	
	/**
	 * 
	 * open the edit category dialog by category id
	 */
	function openEditCategoryDialog(catID){
		
		if(catID == -1)
			return(false);
		
		var cat = getCatByID(catID);
		
		if(cat.length == 0){
			trace("category with id: " + catID + " don't exists");
			return(false);
		}
		
		if(jQuery.isNumeric(catID) == false)
			return(false);
		
		//set data
		var dialogEdit = jQuery("#uc_dialog_edit_category");
		var isCustom = dialogEdit.data("custom");
			
		dialogEdit.data("catid", catID);
		
		//update catid field		
		if(!isCustom){
			jQuery("#span_catdialog_id").html(catID);
			
			var title = cat.data("title");
			jQuery("#uc_dialog_edit_category_title").val(title).focus();
		}
		
		var options = {
				minWidth: 900
		};
		
		g_ucAdmin.openCommonDialog("#uc_dialog_edit_category", function(){
			
			if(!isCustom)
				jQuery("#uc_dialog_edit_category_title").select();
			else{
				t.events.onOpenCategoryDialog(dialogEdit, catID);
			}
			
		},options);
		
	}
	
	
	/**
	 * function invoke from the dialog update button
	 */
	function updateCategoryTitle(){
		
		var dialogEdit = jQuery("#uc_dialog_edit_category");
		
		var catID = dialogEdit.data("catid");		
		
		var cat = getCatByID(catID);
				
		var newTitle = jQuery("#uc_dialog_edit_category_title").val();
		var data = {
			catID: catID,
			title: newTitle
		};
		
		if(g_objAjaxDataAdd && typeof(data) == "object"){
			jQuery.extend(data, g_objAjaxDataAdd);
		}
		
		g_ucAdmin.dialogAjaxRequest("uc_dialog_edit_category", "update_category", data, function(response){
			
			t.updateTitle(catID, newTitle);
		});
		
	}
	
	/**
	 * update category title
	 */
	this.updateTitle = function(catID, newTitle){
		
		var objCat = getCatByID(catID);
		var numItems = objCat.data("numaddons");
		
		var newTitleShow = newTitle;
		if(numItems && numItems != undefined && numItems > 0)
			newTitleShow += " ("+numItems+")";
			
		objCat.html("<span>" + newTitleShow + "</span>");
		
		objCat.data("title",newTitle);
		
	};
	
	
	/**
	 * init edit category dialog
	 */
	function initEditCategoryDialog(){
		
		var objEditDialog = jQuery("#uc_dialog_edit_category");
		
		if(objEditDialog.length == 0)
			return(false);
		
		var isCustom = objEditDialog.data("custom");
				
		if(isCustom)
			return(false);
		
		// set update title onenter function
		jQuery("#uc_dialog_edit_category_action").on("click",updateCategoryTitle);
		
		jQuery("#uc_dialog_edit_category_title").doOnEnter(updateCategoryTitle);
		
	}
	
	
	
	function _______________DELETE_CATEGORY______________(){}
		
	
	/**
	 * remove some category by id
	 */
	function deleteCategory(){
		 
		var dialogDelete = jQuery("#uc_dialog_delete_category");
		var catID = dialogDelete.data("catid");
				
		var data = {};
		data.catID = catID;
		
		var selectedCatID = t.getSelectedCatID();
		
		//get if selected category will be removed
		var isSelectedRemoved = (catID == selectedCatID);
		
		if(g_objAjaxDataAdd && typeof(data) == "object"){
			jQuery.extend(data, g_objAjaxDataAdd);
		}
		
		g_ucAdmin.dialogAjaxRequest("uc_dialog_delete_category", "remove_category", data, function(response){
			
			removeCategoryFromHtml(catID);
			
			//update html cats select
			var htmlSelectCats = response.htmlSelectCats;
			jQuery("#select_item_category").html(htmlSelectCats);
			
			//clear the items panel
			if(isSelectedRemoved == true){
				
				//run event
				t.events.onRemoveSelectedCategory();
								
				t.selectFirstCategory();
			}
			
			//fire height change event
			t.events.onHeightChange();
			
		});
		
				
	}
	
	/**
	 * 
	 * open the edit category dialog by category id
	 */
	function openDeleteCategoryDialog(catID){
		
		if(catID == -1)
			return(false);
		
		var cat = getCatByID(catID);
		
		if(cat.length == 0){
			trace("category with id: " + catID + " don't exists");
			return(false);
		}
		
		//set data
		var dialogDelete = jQuery("#uc_dialog_delete_category");
		dialogDelete.data("catid", catID);
		
		var title = cat.data("title");
		
		jQuery("#uc_dialog_delete_category_catname").html(title);
		
		g_ucAdmin.openCommonDialog("#uc_dialog_delete_category");
		
	}
	
	
	/**
	 * init edit category dialog
	 */
	function initDeleteCategoryDialog(){
		
		// set update title onenter function
		jQuery("#uc_dialog_delete_category_action").on("click",deleteCategory);
		
	}
	
	
	function _______________EVENTS______________(){}
	
	
	/**
	 * on category list item click
	 */
	function onCatListItemClick(event){

		if(g_ucAdmin.isRightButtonPressed(event))
    		return(true);
		
		if(g_catClickReady == false)
			return(false);
		
		if(jQuery(this).hasClass("selected-item"))
			return(false);
		
		var catID = jQuery(this).data("id");
		t.selectCategory(catID);
		
	}
	
	/**
	 * on double click
	 */
	function onCatListItemDblClick(event){
		
		if(g_ucAdmin.isRightButtonPressed(event))
    		return(true);
		
		if(g_catClickReady == false)
			return(false);
		
		var catID = jQuery(this).data("id");
		
		t.runCategoryAction("edit_category",catID);

	}
	
	/**
	 * on cat list item mousedown
	 */
	function onCatListItemMousedown(event){
	
		if(g_ucAdmin.isRightButtonPressed(event))
			return(true);
		
		g_catClickReady = true;
		
	}

	
	/**
	 * on category context menu click
	 */
	function onCategoryContextMenu(event){
		
		g_catFieldRightClickReady = false;
		
		var objCat = jQuery(this);
		var catID = objCat.data("id");
		
		if(catID == 0 || catID == "all")
			return(false);
		
		var objMenu = jQuery("#rightmenu_cat");
		
		objMenu.data("catid",catID);
		g_manager.showMenuOnMousePos(event, objMenu);
	}

	
	/**
	 * on categories context menu
	 */
	function onCatsFieldContextMenu(event){
		
		event.preventDefault();
		
		if(g_catFieldRightClickReady == false){
			g_catFieldRightClickReady = true;
			return(true);
		}
		
		var objMenu = jQuery("#rightmenu_catfield");
		g_manager.showMenuOnMousePos(event, objMenu);
	}
	
	
	/**
	 * on action button click
	 */
	function onActionByttonClick(){
		
		var objButton = jQuery(this);
		
		if(!g_ucAdmin.isButtonEnabled(objButton))
			return(false);
		
		var action = objButton.data("action");
		
		t.runCategoryAction(action);
		
	}
	
	
	/**
	 * init events
	 */
	function initEvents(){
		
		jQuery(".uc-cat-action-button").on("click",onActionByttonClick);
		
		//list categories actions
		jQuery("#list_cats").on("mouseover", "li", function() {
			jQuery(this).addClass("item-hover");
			
		});
		
		jQuery("#list_cats").on("mouseout","li", function() {
			jQuery(this).removeClass("item-hover");
		});
		
		jQuery("#list_cats").on("click", "li", onCatListItemClick);
		jQuery("#list_cats").on("dblclick", "li", onCatListItemDblClick);
		
		jQuery("#list_cats").on("mousedown", "li", onCatListItemMousedown );
		
		//init context menus
		jQuery("#list_cats").on("contextmenu", "li", onCategoryContextMenu);
		jQuery("#cats_section").on("contextmenu", onCatsFieldContextMenu);
		
	}
	
	this._______________EXTERNAL_GETTERS______________ = function(){}
	
	
	/**
	 * get selected category ID
	 */
	this.getSelectedCatID = function(){
		var objCat = g_objListCats.find("li.selected-item");
		if(objCat.length == 0)
			return(-1);
		
		var catID = objCat.data("id");
		
		return(catID);
	};
	
	
	/**
	 * get selected category data
	 */
	this.getSelectedCatData = function(){
		
		var selectedCatID = t.getSelectedCatID();
		
		if(selectedCatID == -1)
			return(null);
		
		var data = getCatData(selectedCatID);
		
		return(data);
	};
	
	
	/**
	 * return if some category selected
	 */
	this.isSomeCatSelected = function(){
		
		var selectedCatID = t.getSelectedCatID();
		
		if(selectedCatID == -1)
			return(false);
		
		return(true);
	};
	
	
	/**
	 * get height of the categories list
	 */
	this.getCatsHeight = function(){
		
		var catsWrapper = jQuery("#cats_section .cat_list_wrapper");
		var catHeight = catsWrapper.height();

		if(catHeight > g_maxCatHeight)
			catHeight = g_maxCatHeight;
		
		return(catHeight);
	};
	
	/**
	 * get arr categories
	 */
	function getArrCats(){
		var arrCats = jQuery("#list_cats li").get();
		return(arrCats);
	}
	
	
	/**
	 * get num categories
	 */
	this.getNumCats = function(){
		var numCats = jQuery("#list_cats li").length;
		return(numCats);
	};
	
	
	/**
	 * get mouseover category
	 */
	this.getMouseOverCat = function(){

		var arrCats = getArrCats();
		
		for(var index in arrCats){
			var objCat = arrCats[index];
			objCat = jQuery(objCat);
			
			var isMouseOver = objCat.ismouseover();
			if(isMouseOver == true)
				return(objCat);
		}
		
		return(null);
	};
	
	
	this._______________EXTERNAL_SETTERS______________ = function(){}
	
	
	/**
	 * set object add data to every ajax request
	 */
	this.setObjAjaxAddData = function(objData){
		
		g_objAjaxDataAdd = objData;
		
	};
	
	/**
	 * set cat section height
	 */
	this.setHeight = function(height){
		
		jQuery("#cats_section").css("height", height+"px");
		
	};
	
	/**
	 * set html cats list
	 */
	this.setHtmlListCats = function(htmlCats){
		
		jQuery("#list_cats").html(htmlCats);
		
	};
	
	/**
	 * select some category by id
	 */
	this.selectCategory = function(catID){
		
		var fullCatID = "#category_"+catID;
		
		var cat = jQuery(fullCatID);
		
		if(cat.length == 0){
			//g_ucAdmin.showErrorMessage("category with id: "+catID+" not found");
			return(false);
		}
		
		cat.removeClass("item-hover");
		
		if(cat.hasClass("selected-item"))
			return(false);
		
		
		jQuery("#list_cats li").removeClass("selected-item");
		cat.addClass("selected-item");
		
		/*
		if(catID == 0 || catID == "all")
			disableCatButtons();
		else
			enableCatButtons();
		*/
		
		g_manager.onCatSelect(catID);
		
		return(true);
	};
	
	
	/**
	 * get context menu category ID
	 */
	this.getContextMenuCatID = function(){
		var catID = jQuery("#rightmenu_cat").data("catid");
		return(catID);
	};
	
	
	/**
	 * destroy the categories
	 */
	this.destroy = function(){
		
		//add category
		jQuery("#button_add_category").off("click");
		
		//remove category:
		jQuery("#button_remove_category").off("click");
		
		//edit category
		jQuery("#button_edit_category").off("click");
		
		var objListItems = jQuery("#list_cats").find("li");
		objListItems.off("mouseover");
		objListItems.off("mouseout");
		objListItems.off("click");
		objListItems.off("dblclick");
		objListItems.off("mousedown");
							
		//init context menus
		jQuery("#list_cats").off("contextmenu");
		jQuery("#cats_section").off("contextmenu");
		
		
	};
	
	
	/**
	 * init categories
	 */
	this.init = function(objManager){
		
		initCats(objManager);
		
	};
	
	
}