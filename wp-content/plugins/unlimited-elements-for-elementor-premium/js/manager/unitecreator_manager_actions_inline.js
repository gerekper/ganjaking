"use strict";

function UCManagerActionsInline(){
	 
	var t = this;
	var g_objCats, g_manager, g_objDialogEdit;
	var g_objWrapper, g_objSettings, g_objSettingsWrapper, g_initByAddonID = null;
	var g_imageField = null;		//field that set to be image for html output
	var g_dialogFormItem = new UniteCreatorParamsDialog();
	var g_itemType = "default";	//default / form
	
	var g_objItems = new UCManagerAdminItems();
	
	if(!g_ucAdmin){
		var g_ucAdmin = new UniteAdminUC();
	}
	
	var g_temp = {
			source:""
	};
	
	/**
	 * on item button click
	 */
	this.runItemAction = function(action, data){
		
		switch(action){
			case "add_images":
				onAddImagesClick();
			break;
			case "add_form_item":
				openFormItemDialog();
			break;
			case "add_item":
				openAddEditItemDialog();
			break;
			case "edit_item":
				if(g_itemType == "form")
					onEditFormItemClick();
				else
					onEditItemClick();
			break;
			case "update_order":	//do nothing
			break;
			case "remove_items":
				g_objItems.removeSelectedItems();
			break;
			case "duplicate_items":
				g_objItems.duplicateSelectedItems();
			break;
			case "select_all_items":
				g_objItems.selectUnselectAllItems();
			break;
			default:
				trace("wrong action: "+action);
			break;
		}
		
	};
	
	
	function ________FORM___________(){}
	
	
	/**
	 * open form item dialog - attributes dialog
	 */
	function openFormItemDialog(params, itemID){
		
		var isEdit = false;
		if(params)
			isEdit = true;
		
		if(isEdit == false){		//add
			
			g_dialogFormItem.open(null,null,function(objValues){
				
				appendItem(objValues);
				
			});
			
		}else{			//edit
			
			g_dialogFormItem.open(params, itemID, function(objValues, itemID){
				
				updateItemByID(itemID, objValues);
												
			});
			
		}
		
	}
	
	/**
	 * open edit for item dialog with form data
	 */
	function onEditFormItemClick(){
		
		var objItem = g_objItems.getSelectedItem();
		if(!objItem)
			throw new Error("No items found");
		
		var params = objItem.data("params");
		var itemID = objItem.data("id");
		
		openFormItemDialog(params,itemID);
				
	}
	
	
	/**
	 * get items data
	 */
	this.getItemsData = function(){
		var objItems = g_objItems.getObjItems();
		
		var arrItems = [];
		jQuery.each(objItems, function(index, item){
			var objItem = jQuery(item);
			var params = objItem.data("params");
			arrItems.push(params);
		});
		
		return(arrItems);
	};
	
	
	/**
	 * set items from data
	 */
	this.setItemsFromData = function(arrItems){
		
		g_objItems.removeAllItems(true);
		
		if(typeof arrItems != "object")
			return(false);
		
		jQuery.each(arrItems, function(index, itemData){
			appendItem(itemData, true);
		});
		
		g_objItems.updateAfterHtmlListChange();
	};
	
	
	
	function ________OTHER___________(){}
	
	
	/**
	 * on add images click
	 */
	function onAddImagesClick(){
				
		g_ucAdmin.openAddImageDialog("Add Images",function(response){
			
			if(response && jQuery.isArray(response) == false)
				response = [response];
			
			jQuery.each(response, function(index, item){
				
				if(g_temp.source == "addon"){
					
					var urlAssetsRelative = item.url_assets_relative;
					var urlImage = item.full_url;				
					
				}else{
					var urlImage = item.url;
				}
				
				urlImage = g_ucAdmin.urlToRelative(urlImage);
								
				addItemFromImage(urlImage);
			});
			
		}, true, g_temp.source);
		
	}
	
	
	/**
	 * open edit item dialog
	 */
	function onEditItemClick(){
		
		var objItem = g_objItems.getSelectedItem();
		if(!objItem)
			throw new Error("No items found");
		
		openAddEditItemDialog(true, objItem);
	}
	
	
	/**
	 * open add item dialog
	 */
	function openAddEditItemDialog(isEdit, objItem){
		
		if(!isEdit)
			var isEdit = false;
		
		var buttonText = g_uctext.add_item;
		var titleText = g_uctext.add_item;
		
		if(isEdit){
			var params = objItem.data("params");
			g_objDialogEdit.data("item", objItem);
			
			buttonText = g_uctext.update_item;
			titleText = g_uctext.edit_item;
		}
		
		var buttonOpts = {};
		
		buttonOpts[g_uctext.cancel] = function(){
			g_objDialogEdit.dialog("close");
		};

		buttonOpts[buttonText] = function(){
			
			if(isEdit == false)
				addItemFromDialog();
			else{
				var objItem = g_objDialogEdit.data("item");
				updateItemFromDialog(objItem);
			}
			
			g_objDialogEdit.dialog("close");
		};
		
		
		g_objDialogEdit.dialog({
			dialogClass:"unite-ui",			
			buttons:buttonOpts,
			title: titleText,
			minWidth:800,
			modal:true,
			open:function(){
				
				if(g_initByAddonID){	//ajax init
					
					var data = {
							addonid:g_initByAddonID
					};
					
					g_initByAddonID = null;
					
					g_ucAdmin.ajaxRequest("get_addon_item_settings_html", data, function(response){
						
						g_objSettingsWrapper.html(response.html);
						
						initSettingsObject();
						
						//clear or init settings
						if(isEdit == false)	//add
							g_objSettings.clearSettings();
						else				//edit
							g_objSettings.setValues(params);
						
					});
					
				}else{
					
					if(isEdit == false)	//add
						g_objSettings.clearSettings();
					else				//edit
						g_objSettings.setValues(params);
					
				}
				
				g_objSettings.focusFirstInput();
			}
			
		});
		
	}
	
	
	
	/**
	 * generate item title
	 */
	function generateItemTitle(){
		var numItems = g_objItems.getNumItems()+1;
		var title = "Item " + numItems;
		return(title);
	}
	
	/**
	 * get title from params
	 * @param params
	 */
	function getTitleFromParams(params){
		
		if(params.hasOwnProperty("title") == false)
			return(null);
		
		var title = params["title"];
		if(!title)
			return(null);
		
		return(title);
	}
	
	
	/**
	 * generate item html
	 */
	function generateItemHtml(params, id){
				
		//set title
		var title = generateItemTitle();
		var altTitle = getTitleFromParams(params);
		
		if(altTitle)
			title = altTitle;
		
		var description = "";
		
		//set description style
		var urlImage = null;
		
		if(params.hasOwnProperty("thumb"))
			urlImage = jQuery.trim(params.thumb);
		
		if(!urlImage && g_imageField && params.hasOwnProperty(g_imageField))
			urlImage = jQuery.trim(params[g_imageField]);
		
		//trace('generateItemHtml');
		//trace(urlImage);
		
		var descStyle = "";
		if(urlImage){
			urlImage = g_ucAdmin.urlToFull(urlImage);
			descStyle = "style=\"background-image:url('"+urlImage+"')\"";
		}
		
		//generatet id
		if(id){
			var itemID = g_objItems.getItemIDFromID(id);
		}else{
			var objID = g_objItems.getObjNewID();
			var id = objID.id;
			var itemID = objID.itemID;
		}
		
		
		var $htmlItem = "";
		$htmlItem += "<li id='" + itemID + "' data-id='"+id+"' data-title="+title+" >";
		$htmlItem += "	<div class=\"uc-item-title unselectable\" unselectable=\"on\">"+title+"</div>";
		$htmlItem += "	<div class=\"uc-item-description unselectable\" unselectable=\"on\" "+descStyle+">"+description+"</div>";
		$htmlItem += "	<div class=\"uc-item-icon unselectable\" unselectable=\"on\"></div>";
		$htmlItem += "</li>";
		
		return($htmlItem);
	}
	
	
	/**
	 * update item html from params
	 */
	function updateItemHtml(objItem, params){
		
		var id = objItem.data("id");
		
		var html = generateItemHtml(params, id);
		
		var objNewItem = g_objItems.replaceItemHtml(objItem, html);
		
		objNewItem.data("params", params);
		
	}
	
	
	/**
	 * append item from values
	 * @param objValues
	 */
	function appendItem(objValues, noUpdate){
		var htmlItem = generateItemHtml(objValues);
		var objItem = g_objItems.appendItem(htmlItem, noUpdate);
		objItem.data("params", objValues);
	}
	
	
	/**
	 * add item from dialog
	 */
	function addItemFromDialog(){
		var objValues = g_objSettings.getSettingsValues();
		appendItem(objValues);
	}
	
	
	/**
	 * add item from image
	 */
	function addItemFromImage(urlImage){
		
		var objInfo = g_ucAdmin.pathinfo(urlImage);
		var params = {};
		params.title = objInfo.filename;
		params.image = urlImage;
		
		appendItem(params);
				
	}
	
	
	/**
	 * update item from dialog
	 */
	function updateItemFromDialog(objItem){
		
		var params = g_objSettings.getSettingsValues();
		objItem.data("params", params);
		updateItemHtml(objItem, params);
		
	}
	
	/**
	 * update item setting by id
	 */
	function updateItemByID(itemID, params){
		
		var objItem = jQuery("#uc_item_" + itemID);
		g_ucAdmin.validateDomElement(objItem, "edit item");
		
		objItem.data("params", params);
		updateItemHtml(objItem, params);
		
	}
	
	
	/**
	 * set thumb field for viewing the thumb
	 */
	function init_setImageField(){
				
		var arrFieldNames = g_objSettings.getFieldNamesByType("image");
				
		if(arrFieldNames.length == 0)
			return(false);

		g_imageField = arrFieldNames[0];
		
		if(arrFieldNames.length > 1){
			if(jQuery.inArray("image",arrFieldNames) != -1)
				g_imageField == "image";
		}
		
	}
	
	
	/**
	 * destroy
	 */
	this.destroy = function(){
		
		if(g_objSettings)
			g_objSettings.destroy();
		
		//nothing to destroy yet
	};
	
	/**
	 * init settings, after the settigns html is set
	 */
	function initSettingsObject(){
		
		g_objSettings = new UniteSettingsUC();
		g_objSettings.init(g_objSettingsWrapper);
				
		init_setImageField();
	}
	
	
	/**
	 * init the actions
	 */
	this.init = function(objManager){
		
		g_manager = objManager;
		
		g_manager.initItems();
		
		g_objCats = g_manager.getObjCats();
		g_objItems = g_manager.getObjItems();
		g_objWrapper = g_manager.getObjWrapper();
		
		//init options
		var options = g_objWrapper.data("options");
		var source = g_ucAdmin.getVal(options, "source");
		
		if(source)
			g_temp.source = source;
		
		
		g_objDialogEdit = g_objWrapper.find(".uc-dialog-edit-item");
		if(g_objDialogEdit.length){
			
			g_objSettingsWrapper = g_objWrapper.find(".uc-item-config-settings");
			
			var addonID = g_objSettingsWrapper.data("initbyaddon");
			if(addonID){
				g_objSettingsWrapper.data("initbyaddon", null);
				g_initByAddonID = addonID;
			}else{	//init settings right away - no ajax
				
				initSettingsObject();
			
			}
		}else
			g_objDialogEdit = null;
		
		
		//init form item dialog
		var objDialogFormItemWrapper = g_objWrapper.find(".uc-dialog-param-form_item");
		if(objDialogFormItemWrapper.length){
			g_itemType = "form";
			g_dialogFormItem.init(objDialogFormItemWrapper);
		}else
			g_dialogFormItem = null;
			
		
		//init from data
		var arrInitItems = g_objWrapper.data("init-items");
		
		if(arrInitItems && typeof arrInitItems == "object")
			t.setItemsFromData(arrInitItems);
		
	};
	
}