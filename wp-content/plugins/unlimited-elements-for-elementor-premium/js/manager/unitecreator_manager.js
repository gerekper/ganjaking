"use strict";

function UCManagerAdmin(){
	
	var g_objWrapper = null;
	var t = this;
	var g_objCats,g_arrPlugins = [], g_arrPluginsObj = [];
	var g_arrActionsFunctions = [];
	var g_objItems = new UCManagerAdminItems();
	var g_objActions, g_type, g_name, g_passData, g_customOptions = {}, g_objAjaxDataAdd = null;
	
	var g_minHeight = 450;
	
	var g_temp = {
			hasCats: true,
			updateHeight: true
	};
	
	//internal events
	this.events = {
			ITEM_HIDE_EMPTY_TEXT:"hide_empty_text",
			ITEM_MOUSEOVER: "item_mouseover",
			ITEM_MOUSEOUT: "item_mouseout",
			ITEM_SELECTION_CHANGE: "item_selection_change"
	};
	
	
	function ___________GENERAL_FUNCTIONS________________(){}	//sap for outline	
	
	
	/**
	 * trigger internal event
	 */
	this.triggerEvent = function(eventName, params){
		if(!params)
			var params = null;
		
		g_objWrapper.trigger(eventName, params);
	}
	
	
	/**
	 * on internal event
	 */
	this.onEvent = function(eventName, func){
		g_objWrapper.on(eventName, func);
	}
	
	
	/**
	 * update global height, by of categories and items
	 */
	this.updateGlobalHeight = function(catHeight, itemsHeight){
		
		
		setManagerWidthClass();
		
		if(g_temp.updateHeight == false)
			return(true);
		
		if(!catHeight || catHeight === null){
			if(g_objCats)
				var catHeight = g_objCats.getCatsHeight();
			else
				var catHeight = 0;
		}
		
		
		if(!itemsHeight)
			var itemsHeight = g_objItems.getItemsMaxHeight();
		
		var maxHeight = catHeight;
		
		if(itemsHeight > maxHeight)
			maxHeight = itemsHeight;
		
		
		maxHeight += 20;			
		
		if(maxHeight < g_minHeight)
			maxHeight = g_minHeight;
				
		//set list height
		g_objItems.setHeight(maxHeight);
		
		if(g_objCats)
			g_objCats.setHeight(maxHeight);
		
	}
	
	
	/**
	 * set width class to manager
	 */
	function setManagerWidthClass(){
		
		g_objItems.updateWrapperSizeClass();		
	}
	
	/**
	 * set global height
	 */
	this.setTotalHeight = function(totalHeight){
		
		g_objItems.setHeight(totalHeight);
		
		if(g_objCats){
			var catHeight = totalHeight + 50;
			g_objCats.setHeight(catHeight);
		}
		
	}
	
	
	/**
	 * validate that the manager is already inited
	 */
	function validateInited(){

		var isInited = g_objWrapper.data("inited");
		
		if(isInited === true)
			throw new Error("Can't init manager twice");
		
		g_objWrapper.data("inited", true);
	}
	
	/**
	 * make sure the manager is not inited
	 */
	function validateNotInited(){
		
		if(!g_objWrapper)
			return(false);
		
		var isInited = g_objWrapper.data("inited");
		if(isInited === true)
			throw new Error("The manager has to be not inited for this operation");
	}
	
	
	/**
	 * destroy the manager
	 */
	this.destroy = function(){
				
		g_objWrapper.add("#manager_shadow_overlay").off("contextmenu");
		
		g_objWrapper.find(".unite-context-menu li a").off("mouseup");
		
		g_objWrapper.find("#button_items_operation").off("click");
		
		if(g_objCats)
			g_objCats.destroy();
		
		g_objItems.destroy();
		
		g_objActions.destroy();
		
		g_objWrapper.html("");
		g_objWrapper = null;
	}
	
	
	this. ___________PLUGINS_EXTERNAL________________ = function(){}		
	
	
	/**
	 * add plugin
	 */
	this.addPlugin = function(plugin){
		
		validateNotInited();
		
		g_arrPlugins.push(plugin);
	}
	
	
	/**
	 * add cats action function
	 */
	this.addActionFunction = function(func){
		
		if(typeof func != "function")
			throw new Error("the action function should be a function type");
		
		g_arrActionsFunctions.push(func);
	};
	
	/**
	 * get action functions
	 */
	this.getActionFunctions = function(){
		
		return(g_arrActionsFunctions);
	};
	
	this. ___________EXTERNAL_GETTERS________________ = function(){}		
	
	/**
	 * get custom option by name
	 */
	this.getCustomOption = function(name){
		if(g_customOptions.hasOwnProperty(name) == false)
			return(undefined);
		
		var value = g_customOptions[name];
		
		return(value);
	};
	
	
	/**
	 * get all items data - from actions
	 */
	this.getItemsData = function(){

		if(typeof g_objActions.getItemsData != "function")
			throw new Error("get items data function not exists in this type");
		
		var arrItems = g_objActions.getItemsData();
		
		return(arrItems);
	}
	
	/**
	 * get items data json
	 */
	this.getItemsDataJson = function(){
		var data = t.getItemsData();
		if(typeof data != "object")
			return("");
		
		var dataJson = JSON.stringify(data);
		
		return(dataJson);
	}
	
	/**
	 * get categories object
	 */
	this.getObjCats = function(){
		return(g_objCats);
	}

	
	/**
	 * get items objects
	 */
	this.getObjItems = function(){
		
		return(g_objItems);
	}
	
	
	/**
	 * get wrapper object
	 */
	this.getObjWrapper = function(){
		
		return(g_objWrapper);
	}
	
	/**
	 * get mouseover item
	 */
	this.getMouseOverItem = function(){
		
		if(g_objCats){
			var catItem = g_objCats.getMouseOverCat();
			if(catItem)
				return(catItem);
		}
		
		var item = g_objItems.getMouseOverItem();
		
		return(item);
	}
	
	
	/**
	 * return if the items field enabled
	 */
	this.isItemsAreaEnabled = function(){

		if(!g_objCats)
			return(true);
		
		if(g_objCats && g_objCats.isSomeCatSelected() == false)
			return(false);
		
		return(true);
	}

	this. ___________EXTERNAL_SETTERS________________ = function(){}		

	
	/**
	 * 
	 * set some menu on mouse position
	 */
	this.showMenuOnMousePos = function(event, objMenu){
		
		var objOffset = g_objWrapper.offset();
		var managerY = objOffset.top;
		var managerX = objOffset.left;
		
		var menuX = Math.round(event.pageX - managerX);
		var menuY = Math.round(event.pageY - managerY);
		
		var menuHeight = objMenu.height();
		var menuEndY = menuY+menuHeight;
		
		var parentHeight = g_objWrapper.height();
		
		//open from bottom
		if(menuEndY > parentHeight)
			menuY = menuY - menuHeight;				
		
		jQuery("#manager_shadow_overlay").show();
		objMenu.css({"left":menuX+"px","top":menuY+"px"}).show();
	}
	
	
	/**
	 * hide all context menus
	 */
	this.hideContextMenus = function(){
		jQuery("#manager_shadow_overlay").hide();
		jQuery("ul.unite-context-menu").hide();
	};
	
	/**
	 * return if the manager has cats
	 */
	this.isHasCats = function(){
		
		return(g_temp.hasCats);
	};

	
	/**
	 * on item context menu click
	 */
	function onContextMenuClick(){
		
		var objLink = jQuery(this);
		var action = objLink.data("operation");
		var objMenu = objLink.parents("ul.unite-context-menu");
		
		var menuType = objMenu.data("type");
		
		
		//get extra data according the menu type
		
		var data = null;
		
		switch(menuType){
			case "category":
				data = g_objCats.getContextMenuCatID();
			break;
		}
		
		var actionFound = false;
		
		if(g_objCats)
			actionFound = g_objCats.runCategoryAction(action, data);
		
		if(actionFound == false)
			t.runItemAction(action, data);
		
		t.hideContextMenus();
	}
	
	
	/**
	 * init context menu events
	 * other context menu functions are located in the items
	 */
	function initContextMenus(){

		g_objWrapper.add("#manager_shadow_overlay").on("contextmenu",function(event){
			event.preventDefault();
		});
		
		//on item right menu click
		g_objWrapper.find(".unite-context-menu li a").mouseup(onContextMenuClick);
		
	}

	/**
	 * init plugins
	 */
	function initPlugins(){
		if(g_arrPlugins.length == 0)
			return(false);
		
		jQuery.each(g_arrPlugins, function(index, pluginClass){
			
			
			if(typeof eval(pluginClass) != "function")
				throw new Error("Plugin "+pluginClass+" not found");
				
			var objPlugin = eval("new "+pluginClass+"()");
			objPlugin.init(t);
			
		});
		
		
	}
	
	/**
	 * init gallery view
	 */		
	function initManager(selectedCatID){
		
		g_objWrapper = jQuery("#uc_managerw");
		if(g_objWrapper.length == 0)
			return(false);
		
		g_type = g_objWrapper.data("type");
		g_name = g_objWrapper.data("managername");
		g_passData = g_objWrapper.data("passdata");
		
		//init text
		var objText = g_objWrapper.data("text");
		if(objText && typeof objText == "object"){
			jQuery.extend(g_uctext, objText);
			g_objWrapper.removeAttr("data-text");
		}
		
		
		if(g_type == "inline")
			g_minHeight = 210;
		
		validateInited();
		
		//set if no cats
		var objCatsSection = jQuery("#cats_section");
		if(objCatsSection.length == 0){
			g_temp.hasCats = false;
			g_objCats = null;
		}else{
			g_objCats = new UCManagerAdminCats();
		}
		
		if(!g_ucAdmin)
			g_ucAdmin = new UniteAdminUC();		
		
		if(g_temp.hasCats == true)
			initCategories();
		
				
		//init actions
		switch(g_type){
			case "addons":
				g_objActions = new UCManagerActionsAddons();
			break;
			case "inline":
				g_objActions = new UCManagerActionsInline();
			break;
			case "pages":
				g_objActions = new UCManagerActionsPages();
			break;
			default:
				throw new Error("Wrong manager type: " + g_type);
			break;
		}
				
		if(g_objActions)
			g_objActions.init(t);
		
		//the items must be inited from the manager action file		
		g_objItems.validateInited();
		
		//check first item select
		if(g_objCats){
		
			if(selectedCatID){
				var isSelected = g_objCats.selectCategory(selectedCatID);
				if(isSelected === false)
					g_objCats.selectFirstCategory();
			}
			else
				g_objCats.selectFirstCategory();
		}
		
		t.updateGlobalHeight();
				
		initPlugins();
	};
	
	
	function ___________CATEGORIES________________(){}	//sap for outline
	
	
	/**
	 * init the categories actions
	 */
	function initCategories(){
		
		g_objCats.init(t);
		
		//init events
		g_objCats.events.onRemoveSelectedCategory = function(){
			t.clearItemsPanel();
		};
		
		g_objCats.events.onHeightChange = function(){
			t.updateGlobalHeight();
		};
		
	}
	
	
	function ___________ITEMS_FUNCTIONS________________(){}	//sap for outline	
	
	
	/**
	 * update bottom operations
	 */
	function updateBottomOperations(){
		
		var numSelected = g_objItems.getNumItemsSelected();
		
		var numCats = 0;
		
		if(g_objCats)
			var numCats = g_objCats.getNumCats();
		
		jQuery("#num_items_selected").html(numSelected);
				
		//in case of less then 2 cats - disable operations
		if(numCats <= 1){
			
			jQuery("#item_operations_wrapper").hide();
			return(false);
		}
		
		//in case of more then one cat
		jQuery("#item_operations_wrapper").show();
		
		//enable operations
		if(numSelected > 0){
			jQuery("#select_item_category").prop("disabled","");
			jQuery("#item_operations_wrapper").removeClass("unite-disabled");
			jQuery("#button_items_operation").removeClass("button-disabled");
			
		}else{		//disable operations
			jQuery("#select_item_category").prop("disabled","disabled");
			jQuery("#button_items_operation").addClass("button-disabled");
			jQuery("#item_operations_wrapper").addClass("unite-disabled");
		}
		
		//hide / show operation categories 
		jQuery("#select_item_category option").show();
		var arrOptions = jQuery("#select_item_category option").get();
		
		var firstSelected = false;
		
		var selectedCatID = g_objCats.getSelectedCatID();
		
		for(var index in arrOptions){
			var objOption = jQuery(arrOptions[index]);
			var value = objOption.prop("value");
			
			if(value == selectedCatID)
				objOption.hide();
			else
				if(firstSelected == false){
					objOption.prop("selected","selected");
					firstSelected = true;
				}
		}
			
		
	}


	/**
	 * run items action
	 */
	this.runItemAction = function(action, data){
		
		g_objActions.runItemAction(action, data);
	};

	
    /**
     * on select category event
     */
    this.onCatSelect = function(catID){
    	g_objActions.runItemAction("get_cat_items", catID);
    	g_objItems.unselectAllItems("selectCategory");		
    };
	
    
	/**
	 * run gallery ajax request
	 */
	this.ajaxRequestManager = function(action,data,status,funcSuccess){
		
		jQuery("#status_loader").show();
		jQuery("#status_text").show().html(status);
		
		if(g_objAjaxDataAdd && typeof(data) == "object"){
			jQuery.extend(data, g_objAjaxDataAdd);
		}
		
		g_ucAdmin.ajaxRequest(action,data,function(response){
			jQuery("#status_loader").hide();
			jQuery("#status_text").hide();
			if(typeof funcSuccess == "function")
				funcSuccess(response);
			
			g_objItems.checkSelectRelatedItems();
		});
		
	}
	
	
	/**
	 * 
	 * on bottom GO button click,  move items
	 */
	function onBottomOperationsClick(){
			
			var arrIDs = g_objItems.getSelectedItemIDs();
			
			if(arrIDs.length == 0)
				return(false);
			
			var selectedCatID = g_objCats.getSelectedCatID();
			
			var targetCatID = jQuery("#select_item_category").val();
			if(targetCatID == selectedCatID){
				alert("Can't move addons to same category");
				return(false);
			}
			
			var data = {};
			data.targetCatID = targetCatID;
			data.selectedCatID = selectedCatID;
			data.arrAddonIDs = arrIDs;
			
			g_objActions.runItemAction("move_items", data);
			
	}
	
	
	/**
	 * set actions options
	 * some data goes directly to options
	 */
	this.setCustomOptions = function(options){
		g_customOptions = options;
	};
	
	
	
	/**
	 * set items from data
	 */
	this.setItemsFromData = function(arrItems){
		if(typeof g_objActions.setItemsFromData != "function")
			throw new Error("set items from data function not exists in this type");
		
		g_objActions.setItemsFromData(arrItems);
	};
	
	
	/**
	 * clear items panel
	 */
	this.clearItemsPanel = function(){
		g_objItems.clearItemsPanel();
	}
	
	/**
	 * set object add data to every ajax request
	 */
	this.setObjAjaxAddData = function(objData){
		
		g_objAjaxDataAdd = objData;
		
	}
	
	this. ___________EXTERNAL_INIT________________ = function(){}		
	
	
	
	/**
	 * init bottom operations
	 */
	this.initBottomOperations = function(){
		
		// do items operations
		g_objWrapper.find("#button_items_operation").on("click",onBottomOperationsClick);
		
	}
	
	
	/**
	 * init items actions
	 */
	this.initItems = function(){
		
		g_objItems.initItems(t);
		
		//on selection change
		g_objItems.events.onItemSelectionChange = function(){
			updateBottomOperations();
			
			t.triggerEvent(t.events.ITEM_SELECTION_CHANGE);
		};
		
		//on items height change
		g_objItems.events.onHeightChange = function(itemsHeight){
			t.updateGlobalHeight(null, itemsHeight);
		};
		
		initContextMenus();
		
		//if items only - clear panel
		if(g_temp.hasCats == false)
			g_objItems.updatePanelView();
		
	};

	/**
	 * get manager name
	 */
	this.getManagerName = function(){
		
		return(g_name);
	};
	
	/**
	 * get manager pass data
	 */
	this.getManagerPassData = function(){
		
		return(g_passData);
	}

	/**
	 * set not to update height
	 */
	this.setNotUpdateHeight = function(){
		
		g_temp.updateHeight = false;
		
	}
	
	
	/**
	 * init manager
	 */
	this.initManager = function(selectedCatID){
		
		initManager(selectedCatID);
	};
		
	
};