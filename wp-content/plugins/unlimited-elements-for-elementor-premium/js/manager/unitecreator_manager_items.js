"use strict";

function UCManagerAdminItems(){	
	
	var t = this;
	var g_manager, g_objManagerWrapper, g_objField, g_objWrapper;
	
	var g_itemClickReady = false;
	var g_itemSpaceX = 20;
	var g_itemSpaceY = 20;
	var g_itemsMaxHeight = 0;
	var g_lastSelectedItemID = null;
	
	this.events = {
			onItemSelectionChange:function(){},
			onHeightChange:function(itemsHeight){},
			onSpecialItemClick:function(objItem, type){}
		};

	var g_objDrag = {
			isDragMode: false,
			isCopyMoveDialog: false,
			isClicked: false,
			isReorderEnabled: false,
			isOverItem:false,
			isExitFirstItem:false,
			clickedItemID:null,
			targetItemID:null,
			targetItemType:null,
			arrItems: [],
			arrItemIDs: [],
			arrInvalidTargetItemIDs: []
		};
	
	var g_objSelectBar = {
			isEnabled: false,
			startX: 0,
			startY: 0,
			mouseX: 0,
			mouseY: 0
		};
	
	
	var g_temp = {
			isInited: false,
			isDisableUnselectAll: false,
			disableUnselectTime: 0
	};
	
	
	function _______________INIT______________(){}
	
	
	/**
	 * validate that the object is inited
	 */
	this.validateInited = function(){
		if(g_temp.isInited == false)
			throw new Error("The items is not inited");
		
	}
	
	
	/**
	 * write something to debug line
	 */
	function debugLine(html){
		jQuery("#debug_line").show().html(html);
	}
	
	/**
	 * debug drag object
	 */
	function debugLineDrag(){
		var str = "";
		for(key in g_objDrag){
			var value = g_objDrag[key];
			str += key += " : " + value+" ";
		}
		
		debugLine(str);
		trace(g_objDrag, true);
	}
	
	
	/**
	 * init items object
	 */
	function initItems(objManager){
		
		g_manager = objManager;
		g_objManagerWrapper = jQuery("#uc_managerw");
		
		g_temp.isInited = true;
		
		//init context menus
		g_objWrapper = g_manager.getObjWrapper();
		
		g_objField = jQuery("#items_list_wrapper");
		
		//on item context menu
		jQuery("#uc_list_items").on("contextmenu","li", onItemContextMenu);
		
		//on wrapper context menu
		g_objField.on("contextmenu", onFieldContextMenu);
		
				
		//on click
		jQuery("#uc_list_items").on("click", "li", onItemClick); 
		
		jQuery("#uc_list_items").on("mousedown", "li", function(event) {
			
			if(g_ucAdmin.isRightButtonPressed(event))
				return(true);
						
			g_itemClickReady = true;
		});		


		//background click event
		jQuery("#items_list_wrapper").on("click",function(event){
						
	    	if(g_ucAdmin.isRightButtonPressed(event))
	    		return(true);
	    	
	    	if(g_objSelectBar.isEnabled == true)
	    		return(true);
	    	
			t.unselectAllItems("items_list_wrapper");
		});

		
		jQuery("#uc_list_items").on( "mouseenter","li", function() {
			jQuery(this).addClass("item-hover");
			g_manager.triggerEvent(g_manager.events.ITEM_MOUSEOVER, jQuery(this));
		});
		
		//mouse out
		jQuery("#uc_list_items").on( "mouseleave", "li",function() {
			jQuery(this).removeClass("item-hover");
			g_manager.triggerEvent(g_manager.events.ITEM_MOUSEOUT, jQuery(this));
		});

		
		//double click
		jQuery("#uc_list_items").on( "dblclick", "li",function() {
			g_manager.runItemAction("item_default_action");
		});
		
		//on items panel button click
		jQuery("#manager_buttons .uc-button-item").on("click", onItemsButtonClick);
		
		
		initItemsDragEvents();

		initSelectBar();
		
	}
	
	function _______________GENERAL______________(){}
	
	
	/**
	 * get type, item or category of some item
	 */
	function getItemCategoryType(item){
		if(item == null || typeof item == "string")
			return("unknown");
		
		var parentID = item.parent().prop("id");
		switch(parentID){
			case "list_items":
				return("item");
			break;
			case "list_cats":
				return("category");
			break;
			default:
				return("unknown");
			break;
		}
	}


	
	/**
	 * get items array
	 */
	function getArrItems(){
		var arrItems = jQuery("#uc_list_items li").get();
		return(arrItems);
	}
	
	
	/**
	 * get num items in items panel
	 */
	this.getNumItems = function(){
		var numItems = jQuery("#uc_list_items li").length;
		return(numItems);		
	}
	
	
	/**
	 * get item by itemID
	 */
	this.getItemByID = function(itemID){
		var objItem = jQuery("#uc_item_"+itemID);
		return(objItem);
	}
	
	
    /**
     * 
     * select items by shift mode
     */
    function selectItemsShiftMode(itemID){
    	
    	var arrSelectedItems = t.getSelectedItems();
    	var arrIDs = t.getArrItemIDs();
    	
    	//select only one item
    	if(g_lastSelectedItemID == null || arrSelectedItems.length == 0){
    		
    		selectItem(itemID);
    		
    	}else{
    		
    		//select row of items
    		
        	var indexLast = arrIDs.indexOf(g_lastSelectedItemID);
        	var indexCurrent = arrIDs.indexOf(itemID);
        	if(indexLast == -1 || indexCurrent == -1 || indexLast == indexCurrent)
        		return(false);
        	
        	var firstIndex = indexLast;    	
        	var secondIndex = indexCurrent;
        	if(firstIndex > secondIndex){
            	firstIndex = indexCurrent;
            	secondIndex = indexLast;    		
        	}
        	
        	t.unselectAllItems("selectItemsShiftMode");
        	
        	for(var index = firstIndex; index <= secondIndex; index++){
        		var itemID = arrIDs[index];
        		selectItem(itemID);
        	}
    		
    	}  
    }
    
	/**
	 * 
	 * on item mouse up event handler - start the right click menus
	 */
	function onItemContextMenu(event){
		
		var objItem = jQuery(this);
		var itemID = objItem.data("id");
		var isSelected = isItemSelected(itemID);
		var itemType = getItemType(objItem);
		
		var menuID = "#rightmenu_item";
		if(itemType)
			menuID += "_"+itemType;
		
		if(isSelected == false){								
			t.unselectAllItems("onItemContextMenu");
			selectItem(itemID);
			t.checkSelectRelatedItems();
		}
	
		var numSelected = t.getNumItemsSelected();
				
		if(numSelected > 1)
			menuID = "#rightmenu_item_multiple";
		
		if(itemType)
			menuID += "_"+itemType;
		
		event.preventDefault();
		
		var objMenu = jQuery(menuID);
		if(objMenu.length == 0)
			return(false);
						
		g_manager.showMenuOnMousePos(event, objMenu);
		
		return(false);
	}

	/**
	 * on field context menu, open right click field menu if alowed.
	 */
	function onFieldContextMenu(event){
		
		event.preventDefault();
		
		if(g_manager.isItemsAreaEnabled() == false)
			return(true);
		
		var objMenu = jQuery("#rightmenu_field");
		g_manager.showMenuOnMousePos(event, objMenu);
	}

	
	/**
	 * check if the item selected
	 */
	function isItemSelected(itemID){		
		var item = t.getItemByID(itemID);
		
		if(item.length == 0)
			return(false);
		
		if(item.hasClass("item-selected"))
			return(true);
		else
			return(false);
	}
	

	/**
	 * disable item buttons
	 */
	function disableItemButtons(){
		
		var objButtons = jQuery("#manager_buttons a.uc-button-item");
		
		g_ucAdmin.disableButton(objButtons);
		
		//skip if no cat selected
		if(g_manager.isItemsAreaEnabled() == false)
			return(true);
		
		var buttonAdd = objButtons.filter(".uc-button-add");
		
		//enable add button
		g_ucAdmin.enableButton(buttonAdd);
		
		//enable select button
		var numItems = t.getNumItems();
		if(numItems > 0){
			var buttonSelect = objButtons.filter(".uc-button-select");
			g_ucAdmin.enableButton(buttonSelect);
		}
		
		
	}
	
	/**
	 * enable all buttons except single if multiple selected
	 * for each items type has own panel, if types are mixed
	 */
	function enableItemButtons(numSelected, itemsType){
		
		var buttonsPanelID = "#manager_buttons";
		if(itemsType)
			buttonsPanelID += "_"+itemsType;
		
		var objButtonsPanel = jQuery(buttonsPanelID);
		if(objButtonsPanel.length == 0){
			disableItemButtons();
			return(false);
		}
		
		if(!numSelected)
			var numSelected = t.getNumItemsSelected();
		
		g_ucAdmin.enableButton("#manager_buttons a.uc-button-item");
		
		if(numSelected > 1){
			g_ucAdmin.disableButton("#manager_buttons a.uc-single-item");
		}
		
		if(numSelected < 2)
			g_ucAdmin.disableButton("#manager_buttons a.uc-multiple-items");
			
		
		//treat active/not active related buttons
		
		var numNotActive = t.getNumItemsSelectedNotActive();

		if(numNotActive == 0){
			g_ucAdmin.disableButton("#manager_buttons a.uc-notactive-item");
		}else{
			g_ucAdmin.disableButton("#manager_buttons a.uc-active-item");
		}
		
		
	}
	
	
	
	/**
	 * replace item html, remember it's position
	 */
	function replaceItemHtml(objItem, newHtml){
		
		var pos = objItem.position();
		var itemID = objItem.prop("id");
		
		objItem.replaceWith(newHtml);
		
		jQuery("#"+itemID).css({"top":pos.top+"px","left":pos.left+"px"});
	}

	
	/**
	 * on items button click
	 */
	function onItemsButtonClick(){
		var objButton = jQuery(this);

		if(objButton.hasClass("button-disabled"))
			return(false);
		
		var action = objButton.data("action");
		g_manager.runItemAction(action);
	}
	
	
	function ___________ITEMS_SELECTION________________(){}

	
	/**
	 * get selected items
	 */
	this.getSelectedItems = function(isjQuery){
		
		var objItems = jQuery("#uc_list_items li.item-selected");
		
		if(isjQuery === true)
			return(objItems);
		
		var arrItems = objItems.get();
		
		return(arrItems);
	};
	
	
	
	/**
	 * disable unselect all items for a second
	 */
	function disableUnselectAllItems(){
		
		g_temp.isDisableUnselectAll = true;
		g_temp.disableUnselectTime = jQuery.now();
				
	}
	
	
	
	
	/**
	 * select all items
	 */
	function selectAllItems(){
		jQuery("#uc_list_items li").addClass("item-selected");
		g_lastSelectedItemID = null;
		t.checkSelectRelatedItems();
	}
	
	
	/**
	 * unselect some item
	 */
	function unselectItem(itemID){
		var item = t.getItemByID(itemID);
		if(item.length == 0)
			return(false);
		
		item.removeClass("item-selected");
		
	}
	
	
	/**
	 * select some item
	 */
	function selectItem(itemID){
		
		var item = t.getItemByID(itemID);
		
		if(item.length == 0)
			return(false);
		
		item.addClass("item-selected");
	}

	
	/**
	 * 
	 * clear the items panel, and the buttons too.
	 */
	this.clearItemsPanel = function(){
		
		jQuery("#items_loader").hide();
		jQuery("#uc_list_items").html("").hide();
		t.hideNoAddonsText();
		
		t.checkSelectRelatedItems();
	}
	
	
	
	function ___________SELECT_BAR_FUNCTIONS________________(){}	//sap for outline	
	
	/**
	 * get all the data for the select bar
	 */
	function getSelectBarData(){
		
		var data = {};
		if(g_objSelectBar.mouseX > g_objSelectBar.startX){
			data.left = g_objSelectBar.startX;
			data.right = g_objSelectBar.mouseX;			
		}else{
			data.left = g_objSelectBar.mouseX;						
			data.right = g_objSelectBar.startX;
		}
		
		if(g_objSelectBar.mouseY > g_objSelectBar.startY){
			data.top = g_objSelectBar.startY;
			data.bottom = g_objSelectBar.mouseY;
		}else{
			data.top = g_objSelectBar.mouseY; 
			data.bottom = g_objSelectBar.startY;
		}
		
		data.width = Math.round(data.right - data.left);
		data.height = Math.round(data.bottom - data.top);
		
		//fix position by field position
		var objField = jQuery("#items_list_wrapper");
		var objOffset = objField.offset();
		var fieldY = objOffset.top;
		var fieldX = objOffset.left;
		
		data.top = Math.round(data.top - fieldY);
		data.left = Math.round(data.left - fieldX);
		
		data.right = Math.round(data.right - fieldX);
		data.bottom = Math.round(data.bottom - fieldY);
		
		return(data);
	}
	
	
	/**
	 * draw select bar
	 */
	function drawSelectBar(){
		var data = getSelectBarData();
		
		//draw the bar
		var css = {
			"left": data.left+"px",
			"top": data.top+"px",
			"width": data.width+"px",
			"height": data.height+"px"
		};
				
		jQuery("#select_bar").show().css(css);
	}
	
	/**
	 * hide the select bar
	 */
	function hideSelectBar(){
		jQuery("#shadow_bar").hide();
		jQuery("#select_bar").hide();
	}
	
	
	/**
	 * get overlap size of 2 objects 
	 */
	function getOverlapSize(item){
		
		var barData = getSelectBarData();
		
		 var d0 = item.position(),
         x11 = d0.left,
         y11 = d0.top,
         x12 = d0.left + item.width(),
         y12 = d0.top + item.height(),
         x21 = barData.left,
         y21 = barData.top,
         x22 = barData.right,
         y22 = barData.bottom,     
         x_overlap = Math.max(0, Math.min(x12,x22) - Math.max(x11,x21)),
         y_overlap = Math.max(0, Math.min(y12,y22) - Math.max(y11,y21)),
         size = x_overlap * y_overlap;
		 
		 return(size);
	}
	
	
	/**
	 * select item by the select bar position
	 * mode - normal / shift / control
	 */
	function runSelectBarSelection(mode){
			
		if(!mode)
			var mode = "normal";
		
		switch(mode){
			case "shift":
			break;
			case "control":
			break;								
			case "normal":
				t.unselectAllItems("runSelectBarSelection");
			break;
			default:
				trace("unknown selection mode");
				return(false);
			break;
		}
		
		var objBar = jQuery("#select_bar");
		var arrItems = getArrItems();
		
		for(var index in arrItems){
			var objItem = jQuery(arrItems[index]);
			var overlapSize = getOverlapSize(objItem);
					
			if(overlapSize != 0){
				 var itemID = objItem.data("id");
				 switch(mode){
					case "shift":
					case "normal":
						 selectItem(itemID);
					break;
					case "control":
						if(isItemSelected(itemID))
							 unselectItem(itemID);						 
						 else
							 selectItem(itemID);
					 break;
					 
				 }
			}
		}
		
		t.checkSelectRelatedItems();
	}
	
	
	/**
	 * on mouse move select bar event
	 */
	function onBodyMoseMoveSelectBar(event){
		
		if(g_objSelectBar.isEnabled == true){
			if(event.buttons == 0){
				hideSelectBar();
				g_objSelectBar.isEnabled = false;
			}else{
				g_objSelectBar.mouseX = event.pageX;
				g_objSelectBar.mouseY = event.pageY;
				drawSelectBar();
			}
		}
		
	}

	
	/**
	 * on mouse move select bar event
	 */
	function onBodyMoseUpSelectBar(event){
		
		if(g_objSelectBar.isEnabled == true){
			event.stopPropagation();
			
			hideSelectBar();								
			g_objSelectBar.mouseX = event.pageX;
			g_objSelectBar.mouseY = event.pageY;
			var mode = "normal";				
			if(event.shiftKey)
				mode = "shift";
			else if(event.ctrlKey)
				mode = "control";
						
			runSelectBarSelection(mode);
			
			g_objSelectBar.isEnabled = false;
			
			disableUnselectAllItems();
		}
		
	}
	
	
	/**
	 * init the events of the select bar
	 */
	function initSelectBar(){
		
		//on wrapper mouse down
		jQuery("#items_list_wrapper").mousedown(function(event){
			
			if(jQuery(event.target).hasClass("items_list_wrapper") == false)
				return(true);
			
			if(g_ucAdmin.isRightButtonPressed(event))
				return(true);
			
			var itemOver = t.getMouseOverItem();
			
			if(itemOver != "down")
				return(true);
			
			g_objSelectBar.isEnabled = true;
			
			jQuery("#shadow_bar").show();
			
			g_objSelectBar.startX = event.pageX;
			g_objSelectBar.startY = event.pageY;
			
			return(true);
		});
		
		//on body mousemove - draw the bar
		jQuery("body").mousemove(onBodyMoseMoveSelectBar);
		
		// on body mouse up - stop the bar and check selection
		jQuery("body").mouseup(onBodyMoseUpSelectBar);
		
	}

	
	/**
	 * reorder items after dragging.
	 */
	function reorderItemsAfterDrag(){
		
		var targetID = g_objDrag.targetItemID;
		
		if(targetID == null)
			return(false);
		
		var arrIDs = t.getArrItemIDs();
		var arrSelectedIDs = g_objDrag.arrItemIDs;
				
		//create new array of item id's
		var arrNew = [];
		for(var index in arrIDs){
			var itemID = arrIDs[index];
			if(arrSelectedIDs.indexOf(itemID) != -1)
				continue;
						
			if(itemID == targetID){
				arrNew = arrNew.concat(arrSelectedIDs);
			}
			
			arrNew.push(itemID);
		}
		
		//move down selected items
		if(targetID == "down")
			arrNew = arrNew.concat(arrSelectedIDs);
		
		var objList = jQuery("#uc_list_items");
		var objTempList = jQuery("<ul></ul>");
		
		//create new list item
		for(var index in arrNew){
			var itemID = arrNew[index];
			var item = t.getItemByID(itemID);
			objTempList.append(item);
		}
		
		//objTempList
		objTempList.children().each(function(){
			objList.append(this);
		});
		
		t.updateItemPositions(true);
		t.unselectAllItems("reorder");
		
		return(true);
	}


	/**
	 * on body mouse down
	 */
	function onBodyMouseDown(event){
	
		if(g_ucAdmin.isRightButtonPressed(event))
			return(true);
	}
	
	
	/**
	 * on body mouse up event
	 * check items reordering
	 */
	function onBodyMouseUp(event){
		
		if(g_ucAdmin.isRightButtonPressed(event))
			return(true);
		
		var hideMenus = true;
		
		//debugLineDrag();
		
		g_objDrag.isClicked = false;
		
		if(g_objDrag.isDragMode == true){
			
			//reorder or disable drag mode
			if(g_objDrag.isReorderEnabled == true && g_objDrag.isExitFirstItem == true){
				
				//in case of category
				if(g_objDrag.targetItemType == "category"){
					
					startCopyMoveDialogMode(event);
					hideMenus = false;
					
				}else{	//in case of item
					var isReordered = reorderItemsAfterDrag();
					if(isReordered == true)
						g_manager.runItemAction("update_order");
				}
			}
		}
		
		if(g_objDrag.targetItemType != "category" || g_objDrag.isReorderEnabled == false)
			t.resetDragData();
		
		if(hideMenus == true){
			
			g_manager.hideContextMenus();
			
			if(g_objDrag.isCopyMoveDialog == true)
				t.resetDragData();
		}
		
	}
	


	/**
	 * 
	 * on body mousemove
	 */
	function onBodyMouseMove(event){
		//debugLineDrag();
		
		if(g_objManagerWrapper.ismouseover() == false){
			operateDragIndicator(null);
			g_objDrag.isOverItem = false;
			g_objDrag.targetItemID = null;
		}
		
	}

	
	
	/**
	 * on item mouse down - initiate drag functionality
	 */
	function onItemMouseDown(event){
		
		if(g_ucAdmin.isRightButtonPressed(event))
			return(true);
		
		var objItem = jQuery(this);
		var itemID = objItem.data("id");
		g_objDrag.arrItems = [];
		g_objDrag.arrItemIDs = [];
		
		var isSelected = isItemSelected(itemID);
	    g_objDrag.clickedItemID = itemID;
	    
		if(isSelected){
			g_objDrag.arrItems = t.getSelectedItems();
			g_objDrag.arrItemIDs = t.getSelectedItemIDs();
		}else{				
			g_objDrag.arrItems.push(objItem);
			g_objDrag.arrItemIDs.push(itemID);
		}
		
		g_objDrag.arrInvalidTargetItemIDs = getInvalidTargetItemIDs(); 
		
		g_objDrag.isClicked = true;
	}

	
	/**
	 * on wrapper mouse move event
	 */
	function onGalleryMouseMove(event){
		
		//set drag mode and exit first item vars
		if(g_objDrag.isClicked == true && g_objDrag.isDragMode == false){
			g_objDrag.isExitFirstItem = false;
			g_objDrag.isDragMode = true;
		}
		
		if(g_objDrag.isDragMode == true){
			
			var objDraggingTargetItem = g_manager.getMouseOverItem();
			var itemType = getItemCategoryType(objDraggingTargetItem);
			
			g_objDrag.targetItemType = itemType;
			
			//if the mouse over item, check if it's valid for reordering
			if(objDraggingTargetItem != null){
				
				var targetItemID;
				if(objDraggingTargetItem == "down")
					targetItemID = "down";
				else					
					targetItemID = objDraggingTargetItem.data("id");
				
				if(g_objDrag.isExitFirstItem == false){
					if(g_objDrag.clickedItemID != targetItemID)
						g_objDrag.isExitFirstItem = true;
				}
				
				g_objDrag.isOverItem = true;
				var isValid = isDragTargetItemValid(targetItemID, itemType);
				
				g_objDrag.isReorderEnabled = isValid;
				
				if(isValid)
					g_objDrag.targetItemID = targetItemID;
				else
					g_objDrag.targetItemID = null;
				
			}else{		//if mouse not over item - reorder not enabled				
				g_objDrag.isReorderEnabled = false;
				g_objDrag.isOverItem = false;
				g_objDrag.isExitFirstItem = true;
				g_objDrag.targetItemID = null;
			}
						
			operateDragIndicator(objDraggingTargetItem, itemType);
		}
		
		operateDragIcons(event);		
	}
	
	
	/**
	 * init items drag events
	 */
	function initItemsDragEvents(){
		
		// on body mousemove - operate icon hide
		jQuery("body").mousemove(onBodyMouseMove);
		
		//on list wrapper mousemove - operate dragging targetcheck  
		g_objManagerWrapper.mousemove(onGalleryMouseMove);
		
		//on body mouseup
		jQuery("body").mouseup(onBodyMouseUp);
		jQuery("body").mousedown(onBodyMouseDown);
					
		//on item mousedown
		jQuery("#uc_list_items").on("mousedown", "li",onItemMouseDown);
		
	}

	
	/**
	 * on item click event
	 */
    function onItemClick(event){
    	
    	if(g_ucAdmin.isRightButtonPressed(event))
    		return(true);
    	
		if(g_itemClickReady == false)
			return(true);
		
		event.stopPropagation();
		var objItem = jQuery(this);
		
		var itemType = getItemType(objItem);
		if(itemType){
			
			var processed = t.events.onSpecialItemClick(objItem, itemType);
			
			if(processed === true)
				return(false);
		}
		
		var itemID = objItem.data("id");
		
		var isMultiple = event.ctrlKey;
				
		if(event.shiftKey == true)
			selectItemsShiftMode(itemID);
		else
		if(event.ctrlKey && isItemSelected(itemID) == true)
			unselectItem(itemID);
		else{
			if(isMultiple == false)		//remove ctrl mode
				t.unselectAllItems("onItemClick");
			
			selectItem(itemID);				
			g_lastSelectedItemID = itemID;
		}
		
		t.checkSelectRelatedItems();
    }
	
    
	
	
	
	
	function ___________ITEMS_DRAGGING________________(){}	//sap for outline	

	
	
	
	/**
	 * hide drag icons
	 */
	function hideVisualDragData(){
		
		//hide icons and target indicator
		var objIndicator = jQuery("#drag_indicator");
		
		objIndicator.hide();
		g_objManagerWrapper.css("cursor","default");
	}
	
	
	/**
	 * show the drag icon, set to mouse position.
	 */
	function operateDragIcons(event){
		
		if(g_objDrag.isExitFirstItem == false)
			return(false);
		
		if(g_objDrag.isDragMode == false){
			g_objManagerWrapper.css("cursor","default");
			//objIcon.hide();
			return(false);
		}
		
		//show not alowed icon
		if(g_objDrag.isOverItem == true && g_objDrag.isReorderEnabled == false){
			g_objManagerWrapper.css("cursor","no-drop");
			
		}else{	//show drag icon
			var cursorType = "move";
			if(g_objDrag.targetItemType == "category")
				cursorType = "copy";
			
			g_objManagerWrapper.css("cursor",cursorType);
		}
		
	}
	
	
	/**
	 * 
	 * indicate drag indicator by the item that the mouse is over it
	 */
	function operateDragIndicator(objItem, itemType){
		
		var objIndicator = jQuery("#drag_indicator");		
		
		if(objItem == null || objItem == "down" || g_objDrag.isDragMode == false || g_objDrag.isReorderEnabled == false || itemType == "category"){ 
			
			objIndicator.html("");
			objIndicator.hide();			
			return(false);
		}
		
		//set gap from item start
		var gapX = -70;
		var gapY = 10;
		
		//var id = objItem.data("id");
		var pos = objItem.position();
		//var itemWidth = objItem.width();
		var posX = Math.round(pos.left + gapX);
		var posY = pos.top + gapY;

		//set indicatory text
		if(objIndicator.html() == ""){
			var arrDraggingItems = g_objDrag.arrItems;
			var numItems = arrDraggingItems.length;
			if(numItems == 1){
				var objDraggingItem = jQuery(arrDraggingItems[0]);
				var html = objDraggingItem.data("title");
				objIndicator.html(html);
			}else{			
				var html = numItems + " items";
				objIndicator.html(html);
			}
		}
		
		//set indicator position
		objIndicator.show();
		objIndicator.css({"top":posY,"left":posX});
		
		//debugLine(id);
	} 
	
	
	/**
	 * check if target item valid for reorder
	 * item type can be category / item
	 */
	function isDragTargetItemValid(targetItemID, itemType){
		if(g_objDrag.isDragMode == false)
			return(false);
		
		//if it's category, drag allowed to non selected only
		if(itemType == "category"){
			var objCats = g_manager.getObjCats();
			if(objCats.isCatSelected(targetItemID))
				return(false);
			else
				return(true);
		}
		
		if(g_objDrag.arrInvalidTargetItemIDs.indexOf(targetItemID) == -1)
			return(true);
		
		return(false);
	}
	
	/**
	 * start copy / move dialog mode, when dragging to some category
	 */
	function startCopyMoveDialogMode(event){
		g_objDrag.isDragMode = false;
		g_objDrag.isCopyMoveDialog = true;
		
		var objMenu = jQuery("#menu_copymove");

		g_manager.showMenuOnMousePos(event, objMenu);
	}
	
	
	/**
	 * get invalid for target reorder put item id's
	 */
	function getInvalidTargetItemIDs(){
		
		var arrAll = t.getArrItemIDs();
		var arrSelected = g_objDrag.arrItemIDs;
		
		var arrInvalid = [];
		for(var index in arrAll){
			var itemID = arrAll[index];
			
			//check if the item is selected
			if(arrSelected.indexOf(itemID) != -1){
				arrInvalid.push(itemID);
				continue;
			}
			
			//check if previous item is selected
			if(index == 0)
				continue;
			
			var prevItemID = arrAll[index-1];
			if(arrSelected.indexOf(prevItemID) != -1)
				arrInvalid.push(itemID);
		}
		
		var lastItem = arrAll[arrAll.length-1];
		
		//check if can move down
		if( arrSelected.length == 1 && arrSelected[0] == lastItem )
			arrInvalid.push("down");
			
		return(arrInvalid);
	}

	this.___________EXTERNAL_GETTERS_______ = function(){}
	
	
	/**
	 * update size class
	 * called by the parent. make it call internally later
	 */
	this.updateWrapperSizeClass = function(){
		
		var objListWrapper = g_objWrapper.find(".items_list_wrapper");
		
		var width = objListWrapper.width();
		
		if(width < 830)
			g_objWrapper.addClass("uc-items-size-small");
		else
			g_objWrapper.removeClass("uc-items-size-small");
	}
	
	
	/**
	 * get items height
	 */
	this.getItemsMaxHeight = function(){
		
		return(g_itemsMaxHeight);
	}
	
	/**
	 * get if some item mouseovered
	 * if over field, return string - "down"
	 */
	this.getMouseOverItem = function(){
		
		//check mouseover items
		var arrItems = jQuery("#uc_list_items li").get();
		
		for(var index in arrItems){
			var objItem = arrItems[index];
			objItem = jQuery(objItem);
			
			var isMouseOver = objItem.ismouseover();
			if(isMouseOver == true)
				return(objItem);
		}

		//check if down enabled:
		var isOverField = jQuery("#items_list_wrapper").ismouseover();
		
		if(isOverField == true)
			return("down");
		
		return(null);
	}

	/**
	 * get drag object
	 */
	this.getObjDrag = function(){
		
		return(g_objDrag);
	}
	
	/**
	 * get selected item id's
	 */
	this.getSelectedItemIDs = function(){
		var arrIDs = t.getArrItemIDs(true);
		return(arrIDs);
	}

	
	/**
	 * get array of all item id's
	 */
	this.getArrItemIDs = function(selectedOnly, numericOnly){
		
		if(!selectedOnly)
			var selectedOnly = false;
		
		var selector = "#uc_list_items li";
		if(selectedOnly == true)
			selector = "#uc_list_items li.item-selected";
		
		var arrIDs = [];
		jQuery(selector).each(function(){
			var itemID = jQuery(this).data("id"); 
			if(numericOnly == true && jQuery.isNumeric(itemID) == false)
				return(true);
			
			arrIDs.push(itemID); 
		});

		return(arrIDs);
		
	}
	
	
	/**
	 * get items object
	 */
	this.getObjItems = function(){
		var objItems = jQuery("#uc_list_items li");
		return(objItems);
	};
	
	/**
	 * get item thumb image
	 */
	this.getItemThumbImageUrl = function(objItem){
		
		if(!objItem)
			return(null);
		
		var divThumb = objItem.find(".uc-item-thumb");
		
		if(divThumb.length == 0)
			return(false);
		
		var bgImage = divThumb.css("background-image");
		if(!bgImage)
			return(null);
		
		bgImage = bgImage.replace("url('","");
		bgImage = bgImage.replace("')","");
		
		bgImage = bgImage.replace("url(\"","");
		bgImage = bgImage.replace("\")","");
		
		return(bgImage);
	};
	
	
	/**
	 * get if multiple items or single items selected
	 */
	this.getNumItemsSelected = function(){
		var numSelected = jQuery("#uc_list_items li.item-selected").length;
		return(numSelected);
	};
	
	
	/**
	 * get item type, like web item
	 */
	function getItemType(objItem){
		var type = objItem.data("itemtype");
		if(!type)
			type = "";
		return(type);
	}
	
	
	/**
	 * get selected items type, if several types found, give first
	 * if no special types found, returrn 0
	 */
	this.getSelectedItemsType = function(){
		
		var objSelected = jQuery("#uc_list_items li.item-selected");
		if(objSelected.length == 0)
			return(null);

		for(var i=0;i<objSelected.length;i++){
			var item = objSelected[i];
			var objItem = jQuery(item);
			var type = getItemType(objItem);
			if(type)
				return(type);
		}
		
		return(null);
	};
	
	
	/**
	 * get number of items that are selected and active
	 */
	this.getNumItemsSelectedNotActive = function(){
		var objSelected = jQuery("#uc_list_items li.item-selected");
		if(objSelected.length == 0)
			return(0);
		
		var objNotActive = objSelected.filter(".uc-item-notactive");
		
		var numNotActive = objNotActive.length;
		
		return(numNotActive);
	}
	
	
	/**
	 * get selected item ID
	 */
	this.getSelectedItemID = function(){
		
		var objItem = t.getSelectedItem();
		if(objItem === null)
			return(null);
		
		var itemID = objItem.data("id");
		
		return(itemID);
	}
	
	
	/**
	 * get selected item. if not found, or multiple selected return null
	 */
	this.getSelectedItem = function(){
		
		//get selected item
		var arrItems = t.getSelectedItems();
		if(arrItems.length != 1)
			return(null);
		
		var objItem = jQuery(arrItems[0]);
		
		return(objItem);
	}
	
	
	/**
	 * get item absolute position according the manager wrapper
	 */
	this.getItemWrapperPos = function(objItem){
		
		var offset = objItem.offset();
		
		var offsetWrapper = g_objWrapper.offset();
				
		offset.top -= offsetWrapper.top;
		offset.left -= offsetWrapper.left;
		
		return(offset);
	}
	
	 
	/**
	 * get item ID with prefix from number ID
	 */
	this.getItemIDFromID = function(id){
		var itemID = "uc_item_" + id;
		return(itemID);
	};

	
	/**
	 * get random item iD
	 */
	function getRandomID(){
		var id = g_ucAdmin.getRandomNumber();
		var itemID = t.getItemIDFromID(id);
		
		var output = {id: id, itemID: itemID};
		
		return(output);
	}
	
	
	/**
	 * get new item ID
	 */
	this.getObjNewID = function(){

		var objID = getRandomID();
		while(jQuery("#"+objID.itemID).length)
			objID = getRandomID();
		
		return(objID);
	}
	
	/**
	 * get next item, if absent - return null
	 */
	this.getNextItem = function(objItem){
				
		if(!objItem)
			return(false);
				
		var objNextItem = objItem.next();
		
		if(objNextItem.length == 0)
			return(null);
		
		return(objNextItem);
	}
	
	
	/**
	 * get previous item
	 */
	this.getPrevItem = function(objItem){
						
		if(!objItem)
			return(false);
				
		var objPrevItem = objItem.prev();
		
		if(objPrevItem.length == 0)
			return(null);
		
		return(objPrevItem);
	}
	
	
	this.___________EXTERNAL_SETTERS_______ = function(){}
	
	
	/**
	 * make update after html list of items changes
	 */
	this.updateAfterHtmlListChange = function(){
		
		t.validateInited();
		
		t.updateItemPositions();
		t.checkSelectRelatedItems();
	};
	
	/**
	 * append item to list
	 */
	this.appendItem = function(htmlItem, noUpdate){
		
		jQuery("#uc_list_items").show();
		t.hideNoAddonsText();
		
		var objItem = jQuery(htmlItem);
		
		jQuery("#uc_list_items").append(objItem);
		
		if(noUpdate !== true)
			t.updateAfterHtmlListChange();
		
		return(objItem);
	};
	
	
	/**
	 * replace some item html
	 */
	this.replaceItemHtml = function(objItem, htmlItem){
		
		var itemID = objItem.data("id");

		var isSelected = isItemSelected(itemID);
				
		objItem.replaceWith(htmlItem);
		if(isSelected == true)
			selectItem(itemID);
		
		t.updateAfterHtmlListChange();
		
		var objNewItem = t.getItemByID(itemID)
		
		
		return(objNewItem);
	};
	
	
	/**
	 * remove addons by ids
	 */
	function removeItemsByIDs(arrIDs){
		
		jQuery.each(arrIDs, function(index, id){
			var objItem = t.getItemByID(id);
			
			if(objItem)
				objItem.remove();
		});
		
		t.updatePanelView(true);
		
	}
	
	
	/**
	 * remove selected items
	 */
	this.removeSelectedItems = function(){
		var arrIDs = t.getSelectedItemIDs();
		removeItemsByIDs(arrIDs);
	};
	
	


	/**
	 * duplicate item by ID
	 */
	function duplicateItem(id, insertToEnd, noUpdate){
		
		var objItem = t.getItemByID(id);
		var objCloned = objItem.clone(true);
		var objNewID = t.getObjNewID();
		objCloned.prop("id", objNewID.itemID);
		objCloned.data("id", objNewID.id);
		
		if(insertToEnd === true)
			jQuery("#uc_list_items").append(objCloned);
		else
			objCloned.insertAfter(objItem);
		
		if(noUpdate !== true)
			t.updatePanelView(true);
		
	}
	
	
	/**
	 * duplciate selected items
	 */
	this.duplicateSelectedItems = function(){
				
		var arrIDs = t.getSelectedItemIDs();
		var numItems = t.getNumItemsSelected();
		
		//t.unselectAllItems("duplicateSelectedItems");
		
		var insertToEnd = false;
		if(numItems > 1)
			insertToEnd = true;
		
		jQuery.each(arrIDs, function(index, id){
			duplicateItem(id, insertToEnd, true);
		});

		t.updatePanelView(true);
	}
	
	
	/**
	 * clear all the drag data
	 */
	this.resetDragData = function(){
		
		g_objDrag.isDragMode = false;
		g_objDrag.isCopyMoveDialog = false;
		g_objDrag.arrItemIDs = [];
		g_objDrag.arrItems = [];
		g_objDrag.clickedItemID = null;
		g_objDrag.isClicked = false;
		g_objDrag.isExitFirstItem = false;
		g_objDrag.isOverItem = false;
		g_objDrag.isReorderEnabled = false;
		g_objDrag.targetItemID = null;
		g_objDrag.arrInvalidTargetItemIDs = [];
		
		hideVisualDragData();
	}
	
	
	/**
	 * select or unselect all items
	 */
	this.selectUnselectAllItems = function(){
		
		var numSelected = t.getNumItemsSelected();
		var arrItems = getArrItems();
		
		if(numSelected == arrItems.length)
			t.unselectAllItems("button_select_all_items");
		else
			selectAllItems();						
	}
	
	
	/**
	 * select single item, unselect others
	 */
	this.selectSingleItem = function(itemID){
				
		if(typeof itemID == "object")
			var itemID = itemID.data("id");
		
		var selectedItemID = t.getSelectedItemID();
		
		if(selectedItemID == itemID)
			return(true);
		
		t.unselectAllItems("selectSingleItem");
		
		selectItem(itemID);
		
		t.checkSelectRelatedItems();
	}
	
	
	/**
	 * set items area height
	 */
	this.setHeight = function(height){
		
		jQuery("#items_list_wrapper").css("height",height+"px");
		
	}
	
	
	/**
	 * update positions of the items
	 */
	this.updateItemPositions = function(isFancy){
		
		if(!isFancy)
			var isFancy = false;
		
		var marginX = g_itemSpaceX;
		var marginY = g_itemSpaceX;
		
		var objField = jQuery("#items_list_wrapper");
		
		var fieldWidth = objField.width();
		
		//wait till the max width will be more then 0
		if(fieldWidth == 0){
			setTimeout(t.updateItemPositions, 500);
			return(true);
		}
		
		var startPosx = marginX;
		var startPosy = marginY;
		var maxHeight = 0;
				
		jQuery("#uc_list_items li").each(function(){
			
			var objItem = jQuery(this);
			var itemWidth = objItem.width();
			var itemHeight = objItem.height();
			
			var endPosX = startPosx + itemWidth;
			
			if(endPosX > (fieldWidth - marginX)){
				startPosx = marginX;
				startPosy += itemHeight + g_itemSpaceY;
			}
			
			if(isFancy == true){
				objItem.animate({"left":startPosx, "top":startPosy+"px"},300,"swing");
			}
			else
				objItem.css({"left":startPosx, "top":startPosy+"px"});
			
			startPosx += itemWidth + g_itemSpaceX;
			
			maxHeight = startPosy + itemHeight + marginY;
			
		});
		
		g_itemsMaxHeight = maxHeight;
				
		t.events.onHeightChange(maxHeight);
	}

	
	/**
	 * set list items html
	 */
	this.setHtmlListItems = function(htmlItems){
		
		jQuery("#items_loader").hide();
		jQuery("#uc_list_items").html(htmlItems).show();
		
		if(jQuery("#uc_list_items li").length == 0){
			jQuery("#uc_list_items").hide();
			jQuery("#no_items_text").show();
		}else{
			t.hideNoAddonsText();
		}
		
		t.updateItemPositions();
	}
	
	
	/**
	 * remove all items
	 */
	this.removeAllItems = function(noUpdate){
		
		jQuery("#uc_list_items").html("").hide();
		jQuery("#no_items_text").show();
		
		if(noUpdate !== true)
			t.checkSelectRelatedItems();
	};
	
	
    
	/**
	 * check all select related items
	 */
	this.checkSelectRelatedItems = function(){
		
		t.validateInited();
		
		var numSelected = t.getNumItemsSelected();
		var arrItems = getArrItems();
		var itemsType = t.getSelectedItemsType();
		
		var buttonSelectAll = jQuery("#button_select_all_items");
		
		//operate top buttons
		if(numSelected == 0){
			
			disableItemButtons();
			
		}else{
			
			enableItemButtons(numSelected, itemsType);
		}
		
		//add global classes
		if(numSelected == 1)
			g_objWrapper.addClass("uc-single-item-selected");
		else
			g_objWrapper.removeClass("uc-single-item-selected");
		
		if(numSelected == 0)
			g_objWrapper.addClass("uc-no-item-selected");
		else
			g_objWrapper.removeClass("uc-no-item-selected");
		
		if(numSelected > 1)
			g_objWrapper.addClass("uc-multiple-item-selected");
		else
			g_objWrapper.removeClass("uc-multiple-item-selected");
			
		
		
		//check the select all button
		var textSelect = buttonSelectAll.data("textselect");
		var textUnselect = buttonSelectAll.data("textunselect");
		
		if(numSelected > 0 && numSelected == arrItems.length){
			buttonSelectAll.html(textUnselect);
		}else{
			buttonSelectAll.html(textSelect);
		}
		
		//update bottom operations
		t.events.onItemSelectionChange();
		
	};

	/**
	 * unselect all items
	 */
	this.unselectAllItems = function(fromWhere){
		
		//don't do the unselect operation if command given
		if(g_temp.isDisableUnselectAll == true){
			var currentTime = jQuery.now();
			var diff = currentTime - g_temp.disableUnselectTime;
			g_temp.isDisableUnselectAll = false;

			if(diff < 100)
				return(true);
		}
		
		jQuery("#uc_list_items li").removeClass("item-selected").removeClass("item-hover");
		t.checkSelectRelatedItems();
		
	}
	
	/**
	 * activate selected items
	 */
	this.acivateSelectedItems = function(isActivate, isUnselect){
		
		var objItems = t.getSelectedItems();
		objItems = jQuery(objItems);
		
		if(isActivate == true){
			objItems.removeClass("uc-item-notactive");
		}else{
			objItems.addClass("uc-item-notactive");
		}
		
		if(isUnselect === true)
			t.unselectAllItems("acivateSelectedItems");
		
	}
	
	
	/**
	 * update panel view - enable all related items
	 */
	this.updatePanelView = function(isFancy){
		
		var numItems = t.getNumItems();
		
		if(numItems == 0){
			jQuery("#no_items_text").show();
			jQuery("#uc_list_items").hide();
		}else{
			t.updateItemPositions(isFancy);
			
			jQuery("#uc_list_items").show();
			t.hideNoAddonsText();
		}
		
		t.checkSelectRelatedItems();
	}
	
	
	/**
	 * hide no addons text
	 */
	this.hideNoAddonsText = function(){
		jQuery("#no_items_text").hide();
		
		g_manager.triggerEvent(g_manager.events.ITEM_HIDE_EMPTY_TEXT);
	}
	
	
	/**
	 * set new spaces between items
	 */
	this.setSpacesBetween = function(spaceX,spaceY){
		g_itemSpaceX = spaceX;
		g_itemSpaceY = spaceY;
		
	};
	
	
	/**
	 * update item screenshot
	 */
	this.updateItemScreenshot = function(itemID, urlScreenshot){
		
		if(!itemID)
			return(false);
		
		var objItem = t.getItemByID(itemID);
		if(!objItem)
			return(false);
		
		var objThumb = objItem.find(".uc-item-thumb");
		if(objThumb.length == 0)
			return(false);
		
		objThumb.css("background-image","url('" + urlScreenshot + "')");
		
	};
	
	/**
	 * get list wrapper object
	 */
	this.getObjListWrapper = function(){
		
		var objListWrapper = jQuery("#uc_list_items");
		
		return(objListWrapper);
	}
	
	/**
	 * destroy items
	 */
	this.destroy = function(){
		
		t.validateInited();
		
		//on item context menu
		var $objListItems = jQuery("#uc_list_items li");
		
		$objListItems.off("contextmenu");
		$objListItems.off("click");
		$objListItems.off("mousedown");
		$objListItems.off("mouseover");
		$objListItems.off("mouseout");
		$objListItems.off("dblclick");
		
		jQuery("#manager_buttons .uc-button-item").off("click");
		
		jQuery("#items_list_wrapper").off("contextmenu");
		jQuery("#items_list_wrapper").off("click");
		
		//off drag events
		
		jQuery("body").off("mousemove", onGalleryMouseMove);
		g_objManagerWrapper.off("mousemove");
		
		jQuery("body").off("mouseup",onBodyMouseUp);
		jQuery("body").off("mousedown",onBodyMouseDown);
		
		//off status line events
		
		jQuery("#items_list_wrapper").off("mousedown")
		jQuery("body").off("mousemove",onBodyMoseMoveSelectBar);
		jQuery("body").off("mouseup",onBodyMoseUpSelectBar);
		
	};
	
	
	
	/**
	 * init the items
	 */
	this.initItems = function(objManager){
		initItems(objManager);
	};
	
	
}