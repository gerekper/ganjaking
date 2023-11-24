"use strict";

/**
 * browser object
 */
function UniteCreatorBrowser(){
	
	var g_objWrapper, g_objTabsWrapper, g_objBackButton;
	var g_objLoader;
	var g_objCatalog, g_objHeaderMenu;
	var g_objSearchInput, g_addParams = null;
	
	var g_objCache = {};
	
	//return events to the caller with g_temp.funcResponse
	this.events = {
			LOADING_ADDON: "loading_addon",
			ADDON_DATA: "addon_data"
	};
	
	if(!g_ucAdmin)
		var g_ucAdmin = new UniteAdminUC();
	
	
	//temp vars
	var g_temp = {
			funcResponse: null,
			addonType: "",
			isPages:false,
			isDialogInited:false,
			prefix:"",
			isSelectMode:false,
			lastOpenedTime:null,
			objTriggerElement:null,
			isInsideManager:false
	};
	
	var t = this;
	
	function _______________TABS__________(){}
	
	
	/**
	 * return if tab selected or not
	 */
	function isTabSelected(objTab){
		if(objTab.hasClass("uc-tab-selected"))
			return(true);
		
		return(false);
	}
	
	
	/**
	 * select some tab
	 */
	function selectTab(objTab){
		
		if(objTab.hasClass("uc-browser-tab") == false)
			throw new Error("Wrong tab object");
			
		var objOtherTabs = getObjTabs(objTab);
		
		objOtherTabs.removeClass("uc-tab-selected");
		objTab.addClass("uc-tab-selected");
		
		//show content, hide others
		var catID = objTab.data("catid");
		
		showContentCategory(catID);
	}
	
	/**
	 * select first visible tab
	 */
	function selectFirstVisibleTab(){
		
		var objTabItems = g_objWrapper.find(".uc-browser-tabs-wrapper .uc-tab-item:visible");
		if(objTabItems.length == 0)
			return(false);
		
		var objTab = jQuery(objTabItems[0]).children("a");
		
		selectTab(objTab);
		
	}
	
	
	/**
	 * on tab click function
	 */
	function onTabClick(){
		var objTab = jQuery(this);
		if(isTabSelected(objTab))
			return(true);
		
		selectTab(objTab);
		
	}
	
	
	/**
	 * get obj all tabs without some tab
	 */
	function getObjTabs(objWithout){
		var objTabs = g_objWrapper.find(".uc-browser-tabs-wrapper .uc-browser-tab");
		
		if(objWithout)
			objTabs = objTabs.not(objWithout);
		
		return(objTabs);
	}
	
	/**
	 * init tabs
	 */
	function initTabs(){
		
		var objTabs = getObjTabs();
		
		objTabs.on("click",onTabClick);
	}
	
	
	
	function ________CATALOG_HEADER_MENU__________(){}
	
	/**
	 * on header menu item click
	 */
	function onHeaderMenuClick(){
		
		var objItem = jQuery(this);
		g_objHeaderMenu.find("a").not(objItem).removeClass("uc-menu-active");
		objItem.addClass("uc-menu-active");
		
		var state = objItem.data("state");
		
		trace(state);
	}
	
	
	/**
	 * init header menu
	 */
	function initHeaderMenu(){
		
		g_objHeaderMenu = g_objCatalog.find(".uc-catalog-header-menu");
		
		if(g_objHeaderMenu.length == 0){
			g_objHeaderMenu = null;
			return(false);
		}
		
		g_ucAdmin.validateDomElement(g_objHeaderMenu, "header menu");
		
		g_objHeaderMenu.find("a").on("click",onHeaderMenuClick);
		
	}
	
	
	function __________CATALOG_RELATED__________(){}
	
	/**
	 * get addon data by name
	 */
	this.getAddonData = function(addonName){
		
		if(g_objCache.hasOwnProperty(addonName) == true)		
			return(g_objCache[addonName]);
		
		var objAddon = g_objCatalog.find(".uc-browser-addon[data-name='"+addonName+"']");
		
		if(objAddon.length != 1)
			return(null);
		
		var objData = getAddonDataFromAddon(objAddon);
		
		return(objData);
	};
	
	
	/**
	 * install addon
	 */
	this.installAddon = function(objAddon, catTitle, onInstalledFunc){
		
		var addonName = objAddon.data("name");
		
		var isOneClickInstall = false;
		if(g_temp.isInsideManager == false && (!g_temp.addonType || g_temp.addonType == "layout" || g_temp.addonType == "pages"))
			isOneClickInstall = true;
				
		if(!catTitle)
			var catTitle = objAddon.data("cattitle");
	
		if(!catTitle){
			var objContent = objAddon.parents(".uc-browser-content");
			var catTitle = objContent.data("cattitle");
		}
	
		var objInstalled = objAddon.find(".uc-installed-success");
		if(objInstalled.length == 0)
			objInstalled = null;
		
		//set loader
		objAddon.find(".uc-hover-free").hide();
		objAddon.find(".uc-installing").show();
		
		var data = {};
		data["name"] = addonName;
		data["cat"] = catTitle;
		data["type"] = g_temp.addonType;
		
		if(g_temp.isInsideManager)
			data["from_manager"] = true;
		
		g_ucAdmin.setErrorMessageID(function(message){
			
			objAddon.find(".uc-installing div").hide();
			objAddon.find(".uc-installing i").hide();
			objAddon.find(".uc-installing span").hide();
			objAddon.find("h3").show().html(message);
		});
		
		var action = "install_catalog_addon";
		if(g_temp.isPages == true)
			action = "install_catalog_page";
		
		if(g_addParams)
			data["params"] = g_addParams;
		
		g_ucAdmin.ajaxRequest(action, data, function(response){
						
			//set id
			var id = g_ucAdmin.getVal(response, "addonid");
			if(!id)
				id = g_ucAdmin.getVal(response, "layoutid");
			
			//set alias, replace name
			var alias = g_ucAdmin.getVal(response, "alias");
			if(alias){
				addonName = alias;
				objAddon.data("name", alias);
			}
			
			if(id){
				objAddon.data("id", id);
			}
			
			objAddon.find(".uc-installing").hide();
			
			//trigger global event
			var installData = response;
			installData["addontype"] = g_temp.addonType;
			installData["name"] = addonName;
			
			g_ucAdmin.triggerEvent("install_addon", installData);
			
			
			if(isOneClickInstall == false){
				objAddon.find(".uc-state-label").hide();
				objAddon.data("state","installed");
				
			}else{		//on once click install call response func
				
				objAddon.find(".uc-hover-free").show();
				
				if(typeof g_temp.funcResponse == "function")
					g_temp.funcResponse(response);
				
				if(objInstalled)
					objInstalled.show();
			}
			
			if(onInstalledFunc)
				onInstalledFunc(response);
			
		});
		
		
		return(false);
	};
	
	
	
	
	/**
	 * on addon click
	 */
	function onAddonClick(event){
				
		//view page click
		var target = event.target;
		var objTarget = jQuery(target);
		if(objTarget.hasClass("uc-hover-label-preview"))
			return(true);
		
		
		var objAddon = jQuery(this);
		var state = objAddon.data("state");
		
		switch(state){
			case "free":
				t.installAddon(objAddon);
				return(false);
			break;
			case "pro":
				
				return(true);
			break;
		}
		
		var objData = getAddonDataFromAddon(objAddon);
		
		g_temp.funcResponse(objData);
		
		closeCatalog();
	}
	    
    
    /**
     * on addon hover
     */
    this.onAddonHover = function(event, objAddon) {
    	
    	if(!objAddon)
    		var objAddon = jQuery(this);
    			
    	var objLabel = objAddon.find(".uc-hover-label")
    	if(objLabel.length == 0)
    		return(true);
    	    	
        if(objLabel.attr('installing') === 'true' || objLabel.attr('installed') === 'true') {
            return false;
        }
    	
        if(event.type === "mouseenter" || event.type == "item_mouseover") {
        	objAddon.addClass("hover-label-visible");
        	objLabel.removeClass('hidden');
        } else {
        	objAddon.removeClass("hover-label-visible");
        	
        	objLabel.addClass('hidden');
        }
        
    };
	
    /**
     * check if catalog opened
     */
    function isCatalogOpened(){
    	
    	var isOpened = g_objCatalog.is(":visible");
    	
    	return(isOpened);
    }
    
	
	/**
	 * close the catalog
	 */
	function closeCatalog(){
		
		var isOpened = isCatalogOpened();
		if(isOpened == false)
			return(true);
		
		if(g_temp.isSelectMode == true){
			
			var timeNow = jQuery.now();
			var diff = timeNow - g_temp.lastOpenedTime;
			if(diff < 300)
				return(true);
		}			
		
		if(g_temp.isSelectMode == false)
			jQuery("body").removeClass("uc-catalog-open");
		
		g_objWrapper.hide();
		g_objCatalog.hide();
	}
	
	
	/**
	 * position the catalog
	 */
	function positionCatalog(){
		
		if(g_temp.isSelectMode == false)
			return(false);
		
		if(!g_temp.objTriggerElement)
			return(false);
		
		var offset = g_ucAdmin.getCustomDialogOffset(g_objCatalog, g_temp.objTriggerElement);
		
		g_objCatalog.offset(offset);
		
		//set width
		var width = g_temp.objTriggerElement.width();
		g_objCatalog.width(width+10);
		
	}
	
	
	/**
	 * open catalog
	 */
	function openCatalog(){
		
		g_temp.lastOpenedTime = jQuery.now();
		
		g_objWrapper.show();
				
		g_objCatalog.show();
		
		if(g_temp.isSelectMode == false){
			jQuery("body").addClass("uc-catalog-open");
		}else{
			
			//select mode
			positionCatalog();
			
		}
		
		if(g_objSearchInput)
			g_objSearchInput.focus();
		
	}
	
	
	/**
	 * init catalog events
	 */
	function initCatalogEvents(){
		
		//close button
		g_objCatalog.find(".uc-catalog-button-close").on("click",closeCatalog);
		
		g_objCatalog.find(".uc-link-update-catalog").on("click",openDialogCatalogUpdate);
		
		if(g_temp.isSelectMode == true){
			
			g_objCatalog.on("click",function(event){
				event.stopPropagation();
				event.stopImmediatePropagation();
			});
			
			
			jQuery("body").on("click",function(){
				closeCatalog();
			});
			
		}
		
		
	}
	
	
	/**
	 * get category addons
	 */
	function getCatAddons(catID){
		
		var selector = "#uc_browser_content_"+g_temp.prefix+"_"+catID+" .uc-browser-addon";
		
		var objAddons = jQuery(selector);
		
		return(objAddons);
	}
	
	
	
	
	/**
	 * init the catalog
	 */
	function initCatalog(){
        
		g_objCatalog = g_objWrapper.find(".uc-catalog");
		
		g_ucAdmin.validateDomElement(g_objCatalog, "addon browser catalog");
				
		g_objTabsWrapper = g_objWrapper.find(".uc-browser-tabs-wrapper");
		
		//select mode
		if(g_objCatalog.hasClass("uc-select-mode"))
			g_temp.isSelectMode = true;
				
		initTabs();
		
		initHeaderMenu();
		
		initCatalogSearch();
		
		initCatalogEvents();
	}
	
	function _______________SEARCH__________(){}
	
	
	/**
	 * set categories titles according number of items
	 * only on visible items
	 */
	function setCategoriesTitles(){
		
		var objTabItems = g_objWrapper.find(".uc-browser-tabs-wrapper .uc-tab-item:visible");
		
		objTabItems.each(function(index, tabItem){
			var objItem = jQuery(tabItem);
			var title = objItem.data("title");
			var catID = objItem.data("catid");
			var objAddons = getCatAddons(catID);
			var numAddons = objAddons.not(".uc-item-hidden").length;
			var showTitle = title+" ("+numAddons+")";
			objItem.children("a").html(showTitle);
		});
		
	}

	
	/**
	 * show all addons and cats that been hidden by search
	 */
	function search_showAll(){
		
		g_objWrapper.find(".uc-item-hidden").removeClass("uc-item-hidden").show();
		
		setCategoriesTitles();
	}
	
	
	/**
	 * do search
	 */
	function doCatalogSearch(searchValue){
		
		searchValue = jQuery.trim(searchValue);
		
		if(!searchValue){
			search_showAll();
			return(true);
		}
		
		searchValue = searchValue.toLowerCase();
		
		var objTabItems = g_objWrapper.find(".uc-browser-tabs-wrapper .uc-tab-item");
		
		objTabItems.each(function(index, item){
			var objItem = jQuery(this);
			var title = objItem.data("title");
			title = title.toLowerCase();
			
			var pos = title.indexOf(searchValue);
			var isCatFound = (pos !== -1);
			
			var catID = objItem.data('catid');
			var objAddons = getCatAddons(catID);
			
			var isSomeAddonFound = false;
			
			//if category found, all addons will be visible
			if(isCatFound == true){
				
				objAddons.removeClass("uc-item-hidden").show();
				
			}else{	//if cat not found, check addons
				
				jQuery.each(objAddons, function(index, addon){
					
					var objAddon = jQuery(addon);
					
					var addonTitle = objAddon.data("title");
					addonTitle = addonTitle.toLowerCase();
					
					var posAddon = addonTitle.indexOf(searchValue);
					var isAddonFound = (posAddon !== -1);
					if(isAddonFound == true){
						isSomeAddonFound = true;
						objAddon.removeClass("uc-item-hidden").show();
						
					}else{
						objAddon.addClass("uc-item-hidden").hide();
					}
					
				});	//end foreach addons
				
			}
			
			
			if(isCatFound == true || isSomeAddonFound == true){
				objItem.removeClass("uc-item-hidden").show();
			}else
				objItem.addClass("uc-item-hidden").hide();
			
		});
		
		//select first cat
		setCategoriesTitles();
		selectFirstVisibleTab();
		
	}
	
	
	/**
	 * init search in catalog
	 */
	function initCatalogSearch(){
		
		g_objSearchInput =  g_objCatalog.find(".uc-catalog-search-input");
		
		if(g_objSearchInput.length == 0){
			g_objSearchInput = null;
			return(false);
		}
			
		
		var objButtonClear = g_objCatalog.find(".uc-catalog-search-clear");
		
		//-- search input
		
		g_ucAdmin.onChangeInputValue(g_objSearchInput, function(){
						
			var value = g_objSearchInput.val();
			value = jQuery.trim(value);
			
			if(value)
				objButtonClear.fadeTo(500, 1).removeClass("button-disabled");
			else
				objButtonClear.fadeTo(500,0).addClass("button-disabled");
			
			doCatalogSearch(value);
		});
		
		//--clear button
		
		objButtonClear.on("click",function(){
			
			var objButton = jQuery(this);
			if(objButton.hasClass("button-disabled"))
				return(false);
			
			//hide button
			objButton.fadeTo(500,0).addClass("button-disabled");
			
			g_objSearchInput.val("");
			search_showAll();
		});
		
	}
	
	
	function _______________GENERAL__________(){}

	
	/**
	 * get addon data from addon thumbnail
	 */
	function getAddonDataFromAddon(objAddon){
		
		var addonName = objAddon.data("name");
		
		if(g_objCache.hasOwnProperty(addonName) == true)		
			return(g_objCache[addonName]);
		
		var addonTitle = objAddon.data("title");
		var addonID = objAddon.data("id");
		
		var bgImage = null;
		var objBGImage = objAddon.find(".uc-browser-addon-image");
		if(objBGImage.length)
			bgImage = objBGImage.css("background-image");
		
		//load put new addon data, close the catalog first
		
		if(!addonName)
			addonName = null;
		
		if(!addonTitle)
			addonTitle = null;
		
		if(!addonID)
			addonID = null;
		
		var objData = {
				"name":addonName,
				"title":addonTitle,
				"id":addonID,
				"addontype":g_temp.addonType,
				"bgimage":bgImage
		};
		
		g_objCache[addonName] = objData;
		
		return(objData);
	}

	
	/**
	 * show content category
	 */
	function showContentCategory(catID){
				
		var objContent = jQuery("#uc_browser_content_"+g_temp.prefix+"_"+catID);
		g_objWrapper.find(".uc-browser-content").not(objContent).hide();
		objContent.show();
	}
	
			
	/**
	 * open addons browser, for column - add new, for addon - update
	 * objTriggerElement - the button element that trigger the catalog open
	 */
	this.openAddonsBrowser = function(currentAddonData, funcResponse, objTriggerElement){
		
		validateInited();
		
		if(!funcResponse)
			throw new Error("There should be response func");
				
		g_temp.funcResponse = funcResponse;
				
		g_temp.objTriggerElement = objTriggerElement;
		if(!objTriggerElement)
			g_temp.objTriggerElement = null;
				
		openCatalog();
		
	};
	
	
	/**
	 * init update catalog
	 */
	function openDialogCatalogUpdate(){
		
		var options = {
				dialogClass:"uc-dialog-catalog-update unite-ui-black",
				height:300
		};
		
		g_ucAdmin.openCommonDialog("uc_dialog_catalog_update", function(){
			
			g_ucAdmin.setAjaxLoaderID("uc_dialog_catalog_update_loader");
			jQuery("#uc_dialog_catalog_update_message").html("").hide();
			
			g_ucAdmin.setErrorMessageID("uc_dialog_catalog_update_error");
			
			g_ucAdmin.ajaxRequest("check_catalog", {force:true}, function(response){
				
				var errorMessage = g_ucAdmin.getVal(response,"error_message");
				if(errorMessage)
					jQuery("#uc_dialog_catalog_update_error").show().html(errorMessage);
					
				jQuery("#uc_dialog_catalog_update_message").html(response.message).show();
				
			});
			
			
		}, options);
		
				
	}
	
	
	function _______________INIT__________(){}
	
	/**
	 * validate that the browser inited
	 */
	function validateInited(){
		
		g_ucAdmin.validateDomElement(g_objWrapper, "addon browser");
	}
	
	
	/**
	 * init events
	 */
	function initEvents(){
				
		g_objWrapper.find(".uc-browser-addon").on("click",onAddonClick);
		
		//g_objWrapper.find(".buttons-addon").on("click",onAddonButtonClick);
		g_objWrapper.find(".uc-browser-addon").on("mouseenter", t.onAddonHover);
        g_objWrapper.find(".uc-browser-addon").on("mouseleave", t.onAddonHover);
		
		if(g_objBackButton)
			g_objBackButton.on("click",onBackButtonClick);
		
	}	
	
	/**
	 * close catalog
	 */
	this.closeCatalog = function(){
		closeCatalog();
	};
	
	/**
	 * set addon type
	 */
	this.setAddonType = function(addontype, isPages, isFromManager){
		
		g_temp.addonType = addontype;
		g_temp.isPages = isPages;
		g_temp.isInsideManager = isFromManager;
		
	};
	
	
	/**
	 * init browser object
	 */
	this.init = function(objWrapper, addParams){
				
		if(!addParams)
			var addParams = {};
		
		g_objWrapper = objWrapper;
		
		//validate wrapper
		if(g_objWrapper.length == 0){
			console.trace();
			return(false);
		}
		
		g_temp.addonType = g_objWrapper.data("addontype");
		
		var isInited = objWrapper.data("is_inited");
		if(isInited === true){
			trace(g_temp.addonType);
			console.trace();
			throw new Error("The browser is already inited");
		}
		
		//add params on install submit
		g_addParams = addParams;
		
		g_temp.prefix = g_objWrapper.data("prefix");
		
		var isPages = g_objWrapper.data("ispages");
		if(isPages)
			g_temp.isPages = true;
		
		initCatalog();
		
		initEvents();
		
		g_ucAdmin.initActivationDialog();
		
		objWrapper.data("is_inited", true);
	};
	
	
}



