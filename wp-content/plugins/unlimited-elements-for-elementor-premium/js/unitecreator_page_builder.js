"use strict";

function UniteCreatorPageBuilder(){
	
	var t = this;
	var g_objIframe, g_objWrapper, g_panel, g_layoutID, g_layoutType = null;
	var g_panelActions = new UniteCreatorGridActionsPanel();
	var g_gridBuilder, g_objPanelWrapper;
	var g_objSettings = new UniteSettingsUC();
	var g_objBuffer = new UniteCreatorBuffer();
	var g_panel = new UniteCreatorGridPanel();
	var g_objBrowser = new UniteCreatorBrowser();
	var g_objBrowserSections = new UniteCreatorBrowser();
	
	
	if(!g_ucAdmin)
		var g_ucAdmin = new UniteAdminUC();
	
	this.events = {
			VIEW_CHANGED: "view_changed",
			IFRAME_INITED: "iframe_inited",
			SCREENSHOT_SAVED: "screenshot_saved"
	};
	
	this.vars = {
			VIEW_MOBILE: "mobile",
			VIEW_TABLET: "tablet",
			VIEW_DESKTOP: "desktop"
	};
	
	var g_temp = {
			is_save_enabled: false,
			iframe_screenshot_id:"uc_iframe_make_screenshot",
			view: t.vars.VIEW_DESKTOP,
			skip_message_onexit: false,
			sectionsave:null,
			takeScreeshotOnSave:false,
			url_screnshot_template:""
	};
	
	
	/**
	 * show loader message
	 */
	function showLoaderMessage(message){
		
		jQuery("#uc_layout_status_loader").show();
		
		if(message)
			jQuery("#uc_layout_status_loader span").html(message);
			
	}
	
	
	/**
	 * show error message
	 */
	function showErrorMessage(message){
		
		jQuery("#uc_layout_status_error").show();
		jQuery("#uc_layout_status_error span").html(message);
	}	
	
	
	/**
	 * show error message
	 */
	this.showErrorMessage = function(message){		//for outside
		
		showErrorMessage(message);
	};
	
	
	/**
	 * hide save button
	 */
	function hideSaveButton(){
		jQuery("#uc_button_update_layout").hide();
		g_temp.is_save_enabled = false;
		
	};
	
	
	/**
	 * disable save button
	 */
	this.hideSaveButton = function(){		//for outside
		hideSaveButton();
	};

	
	/**
	 * hide new page stuff and show existing page stuff
	 */
	function switchInterfaceToExisting(layoutID, pageName){
		
		g_layoutID = layoutID;
		
		//g_objWrapper.data("layoutid", g_layoutID);
		
		jQuery(".uc-layout-newpage").hide();
		
		jQuery(".uc-layout-existingpage").show();
		
		updateShortcode();
		
		//update preview button
		var buttonPreview = jQuery("#uc-button-preview-layout");
		var url = buttonPreview.attr("href");
		
		url = url.replace("id=0","id="+layoutID);
		buttonPreview.attr("href", url);
		
		//update button mode
		var buttonMode = jQuery("#uc_button_edit_mode");
		
		var urlBox = buttonMode.data("urlbox");
		urlBox += "&id="+layoutID;
		buttonMode.data("urlbox", urlBox);
		
		var urlLive = buttonMode.data("urllive");
		urlLive += "&id="+layoutID;
		buttonMode.data("urllive", urlLive);
		
		location.hash = "layoutid="+layoutID;
	}
	
	/**
	 * update preview url by name
	 */
	function updateUrlPreviewByName(pageName){
		
		if(!pageName)
			return(false);
		
		var buttonPreview = jQuery("#uc-button-preview-layout");
		if(buttonPreview.length == 0)
			return(false);
		
		var urlTemplate = buttonPreview.data("template");
		if(!urlTemplate)
			return(false);
		
		var urlPreview = urlTemplate.replace("[page]", pageName);
		buttonPreview.attr("href", urlPreview);
		
	}
	
	/**
	 * check and redirect if needed
	 */
	function checkRedirectLayout(){
		
		if(g_layoutID)
			return(false);
		
		var hash = location.hash;
		if(!hash)
			return(false);
		
		var layoutID = hash.replace("#layoutid=","");
		
		layoutID = jQuery.trim(layoutID);
		if(jQuery.isNumeric(layoutID) == false)
			return(false);
			
		var viewLayout = g_ucAdmin.getUrlView("layout_outer","id="+layoutID+"&ucwindow=blank");
		
		
		showLoaderMessage("Loading Page...");
		
		location.href = viewLayout;
		
		return(true);
	}

	/**
	 * enter view mode
	 */
	function toViewMode(){
		
		g_objWrapper.addClass("uc-view-mode");
		
		if(g_gridBuilder)
			g_gridBuilder.doAction("to_view_mode");
		
	}
	
	/**
	 * exit view mode
	 */
	function exitViewMode(){
		
		g_objWrapper.removeClass("uc-view-mode");
		
		if(g_gridBuilder)
			g_gridBuilder.doAction("to_regular_mode");
		
	}
	
	
	/**
	 * on actions panel button click
	 */
	function runAction(action, params){
		
		switch(action){
			case "open_main_menu":
				g_panel.toggle("main-menu");
			break;
			case "view_desktop":
				changeView(t.vars.VIEW_DESKTOP);
			break;
			case "view_mobile":
				changeView(t.vars.VIEW_MOBILE);
			break;
			case "view_tablet":
				changeView(t.vars.VIEW_TABLET);				
			break;
			case "import":
				openImportLayoutDialog();				
			break;
			case "export":
				exportLayout();
			break;
			case "save_exit":
				var urlBack = g_ucAdmin.getVal(params, "url_back");				
				var message = g_ucAdmin.getVal(params, "message");
				updateLayoutAndExit(urlBack, message);
			break;			
			case "exit":
				var urlBack = g_ucAdmin.getVal(params, "url_back");				
				var message = g_ucAdmin.getVal(params, "message");
	    		exitEditor(urlBack, message);
			break;
			case "tolive":
			case "tobox":
				var urlBack = g_ucAdmin.getVal(params, "url_redirect");				
				var message = g_ucAdmin.getVal(params, "message");
				updateLayoutAndExit(urlBack, message);
			break;
			case "page_settings":
				if(g_gridBuilder)
					g_gridBuilder.doAction("open_grid_settings");
			break;
			case "page_params":
				
				if(g_gridBuilder)
					g_gridBuilder.doAction("open_page_params_panel");
			break;
			case "duplicate":
				updateLayoutAndDuplicate();
			break;
			case "to_view_mode":
				toViewMode();
			break;
			case "to_regular_mode":
				exitViewMode();
			break;
			case "play_panelobject_animation":
			case "play_panelobject_section_animation":
			case "undo":
				if(g_gridBuilder)
					g_gridBuilder.doAction(action, params);
			break;
			default:
				throw new Error("wrong action: "+action);
			break;
		}
		
	}
	
	function _______SHORTCODE_________(){}
	
	
	/**
	 * init shortcode
	 */
	function initShortcode(){
		
		var objShortcode = jQuery("#uc_layout_shortcode");
		if(objShortcode.length == 0)
			return(false);
				
		updateShortcode();
		
		jQuery("#uc_layout_title").change(updateShortcode);
		
		jQuery("#uc_link_copy_shortcode").on("click",onCopyShortcodeClick);
	}
	
	
	/**
	 * set shortcode
	 */
	function updateShortcode(){
		
		var objShortcode = jQuery("#uc_layout_shortcode");
		
		var titleText = jQuery("#uc_layout_title").val();
		titleText = g_ucAdmin.stripslashes(titleText);
		
		titleText = g_ucAdmin.escapeDoubleQuote(titleText);
		
		var wrappersType = objShortcode.data("wrappers");
		var shortcodeName = objShortcode.data("shortcode");
		
		var wrapperLeft = "{", wrapperRight = "}";
		
		if(wrappersType == "wp"){
			wrapperLeft = "[";
			wrapperRight = "]";
		}
		
		var shortcode = wrapperLeft+shortcodeName+" id="+g_layoutID+" title=\""+titleText+"\" " + wrapperRight;
		
		objShortcode.val(shortcode);
	}
	
	
	/**
	 * on copy click
	 */
	function onCopyShortcodeClick(){
				
		jQuery("#uc_layout_shortcode").focus().select();
		
		document.execCommand("copy");
	}
	
	
	function _______SAVE_LAYOUT_________(){}
	
	
	/**
	 * on update layout button click
	 */
	function updateLayout(isTitleOnly, funcOnSuccess){
		
		if(!isTitleOnly && g_temp.is_save_enabled == false)
			return(true);
		
		var title = jQuery("#uc_layout_title").val();
		var objLayoutName = jQuery("#uc_layout_name");
		
		
		var data = {
				layoutid: g_layoutID,
				title: title
		};
		
		if(g_layoutType)
			data["layout_type"] = g_layoutType;
		
		if(objLayoutName.length)
			data["name"] = objLayoutName.val();
		
		
		if(isTitleOnly !== true){
			var dataGrid = g_gridBuilder.getGridData();
			
			//page params
			var pageParams = g_gridBuilder.getPageParams();
			
			if(pageParams)
				data["params"] = pageParams;
			
			var jsonData = JSON.stringify(dataGrid);
			var strEncodedData = g_ucAdmin.encodeContent(jsonData);
			
			data["grid_data"] = strEncodedData;
			
			g_ucAdmin.setAjaxLoaderID(function(eventName){
				
				if(eventName == "show_loader"){
					
					jQuery("#uc_layout_status_loader").show();
					jQuery("#uc_layout_save_button_text").hide();					
					jQuery("#uc_layout_save_button_loader").show();					
				}else{
					jQuery("#uc_layout_save_button_text").show();					
					jQuery("#uc_layout_status_loader").hide();					
					jQuery("#uc_layout_save_button_loader").hide();					
					
				}
				
			});
			
			
		}else{
			
			g_ucAdmin.setAjaxLoaderID("uc_button_rename_page_loader");
			g_ucAdmin.setAjaxHideButtonID("uc_button_rename_page");
			
			data["title_only"] = true;
		}
		
		
		//var data
		g_ucAdmin.setSuccessMessageID("uc_layout_status_success");
		
		g_ucAdmin.setErrorMessageOnHide(function(){
			jQuery("#uc_layout_status_error").hide();
		});
			
		g_ucAdmin.setErrorMessageID(t.showErrorMessage);
		
		g_ucAdmin.ajaxRequest("update_layout", data, function(response){
								
				var pageName = g_ucAdmin.getVal(response, "page_name");
				
				jQuery("#uc_page_title").html(title);
				var objLayoutNameInput = jQuery("#uc_layout_name");
				if(objLayoutNameInput.length)
					objLayoutNameInput.val(pageName);
				
				
				//handle new page
				if(!g_layoutID){
					
					switchInterfaceToExisting(response.layout_id);
										
				}else{
										
					if(isTitleOnly !== true)
						disableSave();
				}
				
				if(pageName)
					updateUrlPreviewByName(pageName);
				
				
				if(!funcOnSuccess && g_temp.takeScreeshotOnSave == true && isTitleOnly !== true){
					createLayoutThumbnail();
				}
								
				if(funcOnSuccess)
					funcOnSuccess();
		});
	}

    /*
     * on save title button click
     */
    function onSaveTitleClick(){
       
    	updateLayout(true);
	}

    /**
     * exit editor
     */
	function exitEditor(urlBack, message, isSkip){

		urlBack = g_ucAdmin.convertAmpSign(urlBack);
		
		showLoaderMessage(message);
		
		if(isSkip === true)
			g_temp.skip_message_onexit = true;
		
		setTimeout(function(){
			location.href = urlBack;
		},500);
	}
    
    
    /**
     * update layout and exit
     */
    function updateLayoutAndExit(urlBack, message){
		    	
    	g_ucAdmin.validateNotEmpty(urlBack, "back url");
    	g_ucAdmin.validateNotEmpty(message, "message");
    	
    	g_temp.is_save_enabled = true;
    	
    	updateLayout(false, function(){
    		
    		exitEditor(urlBack,message,true);
    	});
    	
    }
	
    /**
     * update layout then duplicate
     */
    function updateLayoutAndDuplicate(){
    	
    	g_temp.is_save_enabled = true;
    	
    	//update layout first
    	updateLayout(false, function(){
    		
    		duplicateLayout();
    		
    	});
    	
    }
    
    
    /**
     * duplicate layout
     */
    function duplicateLayout(){
    	
		var data = {
				layout_id: g_layoutID,
				redirect_to_layout:true
		};
		
		g_ucAdmin.ajaxRequest("duplicate_layout", data);
    	
    }
		
	function _______IMPORT_EXPORT_________(){}
	
	/**
	 * export layout
	 */
	function exportLayout(){
		
		var params = "id="+g_layoutID;
		var urlExport = g_ucAdmin.getUrlAjax("export_layout", params);
		location.href=urlExport;
		
		g_temp.skip_message_onexit = true;
	}
	
	
	/**
	 * open import layout dialog
	 */
	function openImportLayoutDialog(){
		
		jQuery("#dialog_import_layouts_file").val("");
		
		var options = {minWidth:700};
		
		g_ucAdmin.openCommonDialog("#uc_dialog_import_layouts", null, options);
		
	}
	
	
	/**
	 * init import layout dialog
	 */
	function initImportLayoutDialog(){
						
		jQuery("#uc_dialog_import_layouts_action").on("click",function(){
			
			var isOverwrite = jQuery("#dialog_import_layouts_file_overwrite").is(":checked");
	        var data = {overwrite_addons:isOverwrite};
	        
	        data.layoutID = g_layoutID;
			
	        if(!g_layoutID)
	        	throw new Error("layout id not found");
	        
	        var objData = new FormData();
	        var jsonData = JSON.stringify(data);
	    	objData.append("data", jsonData);
	    	
	    	g_ucAdmin.addFormFilesToData("dialog_import_layouts_form", objData);
	    	
	    	g_temp.skip_message_onexit = true;
	    	
			g_ucAdmin.dialogAjaxRequest("uc_dialog_import_layouts", "import_layouts", objData);
			
		});
		
	}
	
	
	/**
	 * change view - desktop / mobile / tablet
	 */
	function changeView(view){
		
		switch(view){
			case t.vars.VIEW_DESKTOP:
				g_objWrapper.removeClass("uc-view-mobile");
				g_objWrapper.removeClass("uc-view-tablet");
				g_objWrapper.addClass("uc-view-desktop");				
			break;
			case t.vars.VIEW_MOBILE:
				g_objWrapper.removeClass("uc-view-desktop");
				g_objWrapper.removeClass("uc-view-tablet");
				g_objWrapper.addClass("uc-view-mobile");
			break;
			case t.vars.VIEW_TABLET:
				g_objWrapper.removeClass("uc-view-desktop");
				g_objWrapper.removeClass("uc-view-mobile");
				g_objWrapper.addClass("uc-view-tablet");
			break;
			default:
				throw new Error("Wrong view: "+t.vars.VIEW_MOBILE);
			break;
		}
				
		
		g_temp.view = view;
		triggerEvent(t.events.VIEW_CHANGED, view);
	}
	
	
	/**
	 * disable save button
	 */
	function disableSave(){
		
		g_objWrapper.addClass("uc-state-saved");
		g_temp.is_save_enabled = false;
		
	}
	
	/**
	 * enable save button
	 */
	function enableSave(){
		
		g_objWrapper.removeClass("uc-state-saved");
		g_temp.is_save_enabled = true;
		
	}
	
	
	function ____________SAVE_PANEL_AND_SCREENSHOT______________(){}
	
	
	/**
	 * create layout thumbnail
	 */
	function createLayoutThumbnail(layoutID){
		
		if(!layoutID)
			layoutID = g_layoutID;
		
		if(!layoutID){
			trace("no layout found!");
			return(false);
		}

		var iframeID = g_temp.iframe_screenshot_id;
		
		removeScreenshotIframe();
		
		var urlPreview = g_temp.url_screnshot_template.replace("id=0", "id="+layoutID);
		
		var htmlIframe = "<iframe id='"+iframeID+"' src='"+urlPreview+"' width='1000' style='width:1000px;'></iframe>";
		
		jQuery("body").append(htmlIframe);
	}
	
	/**
	 * remove screenshot iframe if exists
	 */
	function removeScreenshotIframe(){
		
		var iframeID = g_temp.iframe_screenshot_id;
		var objIframe = jQuery("#" + iframeID);
		if(objIframe.length)
			objIframe.remove();
		
	}
	
	/**
	 * init save screenshot events
	 */
	function initSaveScreenshotEvents(){
		
		/**
		 * on screenshot saved, close the iframe
		 */
		t.onEvent(t.events.SCREENSHOT_SAVED, function(event, response){
			
			//remove iframe
			removeScreenshotIframe();
			
			if(!g_temp.sectionsave)
				return(false);
			
			g_temp.sectionsave.objButton.hide();
			g_temp.sectionsave.objLoader.hide();
			
			if(response.success == true)
				g_temp.sectionsave.objSuccess.show();
			else
				g_temp.sectionsave.objError.show().html(response.message);
			
			setTimeout(function(){
				
				g_temp.sectionsave.objSuccess.hide();
				g_temp.sectionsave.objError.hide();
				g_temp.sectionsave.objButton.show();
				
			}, 1000);
			
		});
		
		
	}
	
	/**
	 * init save panel button settings type
	 */
	function initCustomSettingTypes_saveSection(){
		
		
		var objType = {
				
				funcInit: function(objWrapper, objSettings){
					
					var settingID = objWrapper.prop("id");
					var buttonID = "#"+settingID+"_action";
					var objButton = jQuery(buttonID);
					var objLoader =  jQuery("#"+settingID+"_loader");
					var objSuccess = jQuery("#"+settingID+"_success");
					
					var errorMessageID = settingID+"_error";
					var objError = jQuery("#"+errorMessageID);
					
					//save data
					objLoader.data("inittext", objLoader.html());
					
					//save objects for later
					objButton.on("click",function(){
						
						g_temp.sectionsave = {};
						g_temp.sectionsave.objButton = objButton;
						g_temp.sectionsave.objLoader = objLoader;
						g_temp.sectionsave.objSuccess = objSuccess;
						g_temp.sectionsave.objError = objError;
						
						//restore init values
						objLoader.html(objLoader.data("inittext"));
						
						var data = objSettings.getSettingsValues();
						
						var isCreateThumb = g_ucAdmin.getVal(data, "section_create_thumbnail");
						isCreateThumb = g_ucAdmin.strToBool(isCreateThumb);
						
						if(isCreateThumb == false){
							
							g_ucAdmin.panelAjaxRequest(settingID, "save_section_tolibrary", data);
							
						}else{
							
							objSuccess.hide();
							objError.hide();
							objButton.hide();
							objLoader.show();
							
							g_ucAdmin.setErrorMessageID(function(message){
								objError.show().html(message);
								objButton.show();
								objLoader.hide();
								objSuccess.hide();
							});	
							
							g_ucAdmin.ajaxRequest("save_section_tolibrary", data, function(response){
								
								g_temp.sectionsave.objLoader.show().html("Making Thumbnail...");
								
								createLayoutThumbnail(response.layoutid);
							});
							
						}
						
												
					});
					
				},
				funcClearValue:function(objWrapper){
					
					if(g_temp.sectionsave){
						g_temp.sectionsave.objButton.show();
						g_temp.sectionsave.objError.hide();
						g_temp.sectionsave.objLoader.hide();
						g_temp.sectionsave.objSuccess.hide();
					}
										
				},
				funcSetValue:null,
				funcGetValue: null,
				funcDestroy: null
		};
		
		g_objSettings.addCustomSettingType("save_section_tolibrary", objType);
		
	}
	
	function ____________CUSTOM_SETTINGS______________(){}
	
	/**
	 * init col layout setting type
	 */
	function initCustomSettingTypes_colLayout(){
		
		var objType = {
				
				funcInit: function(objWrapper, objSettings){
					
					var objRows = objWrapper.find(".uc-layout-row");
					 
					objRows.on("click",function(){
						var objRow = jQuery(this);
						objRows.not(objRow).removeClass("uc-layout-selected");
						objRow.addClass("uc-layout-selected");
						
						var layoutType = objRow.data("layout-type");
						
						objSettings.onSettingChange(null, objWrapper);
					});
					
				},
				funcClearValue:null,
				funcSetValue: function(objWrapper, value){
					var objRows = objWrapper.find(".uc-layout-row");
					
					var objRow = objWrapper.find(".uc-layout-row[data-layout-type='" + value + "']");
					objRows.not(objRow).removeClass("uc-layout-selected");
					if(objRow.length)
						objRow.addClass("uc-layout-selected");
										
				},
				funcGetValue: function(objWrapper){
					var objSelectedRow = objWrapper.find(".uc-layout-row.uc-layout-selected");
					if(objSelectedRow.length == 0)
						return(null);
					
					var layoutType = objSelectedRow.data("layout-type");
					return(layoutType);
				},
				funcDestroy: function(settingsWrapper){
					if(!settingsWrapper || settingsWrapper.length == 0)
						return(true);
					
					settingsWrapper.find(".uc-setting-cols-layout .uc-layout-row").off("click");
				}
		};
		
		g_objSettings.addCustomSettingType("col_layout", objType);
		
	}
	
	/**
	 * init custom setting types
	 */
	function initCustomSettingTypes_gridActionButton(){
		
		var objType = {
				
				funcInit: function(objWrapper, objSettings){
					
					var objButton = objWrapper.find(".uc-grid-panel-button");
					objButton.on("click",function(){
						var action = objButton.data("action");
						var params = {};
						var actionParam = objButton.data("actionparam");
						if(actionParam)
							params["action_param"] = actionParam;
						
						runAction(action, params);
					});
				},
				funcClearValue:null,
				funcSetValue: null,
				funcGetValue: null,
				funcDestroy: function(settingsWrapper){
					if(!settingsWrapper || settingsWrapper.length == 0)
						return(true);
					
					settingsWrapper.find(".uc-grid-panel-button").off("click");
				}
		};
		
		g_objSettings.addCustomSettingType("grid_panel_button", objType);
		
	}
	
	
	/**
	 * init custom setting types
	 */
	function initCustomSettingTypes(){
		
		initCustomSettingTypes_colLayout();
		
		initCustomSettingTypes_saveSection();
		
		initCustomSettingTypes_gridActionButton();
	}
	
	
	
	function ____________EVENTS______________(){}
	
	
	
	/**
	 * detect if menu opened, and update wrapper class
	 */
	function checkPanelMenuState(){
		
		var paneName = g_panel.getActivePaneName();
		var isVisible = g_panel.isVisible();
		
		var className = "uc-main-menu-opened";
		
		if(paneName == "main-menu" && isVisible == true)
			g_objWrapper.addClass(className);
		else
			g_objWrapper.removeClass(className);
		
	}
	
	
	/**
	 * run on grid some change taken
	 */
	function onGridChangeTaken(event, origEventName){
		
		enableSave();
	}
	
	
	/**
	 * trigger event
	 */
	function triggerEvent(eventName, options){
		
		g_objWrapper.trigger(eventName, options);
		
	}
	
	
	/**
	 * on some event
	 */
	this.onEvent = function(eventName, func){
		
		g_objWrapper.on(eventName, func);
		
	};
	
	/**
	 * trigger event
	 */
	this.triggerEvent = function(eventName, options){
		
		triggerEvent(eventName, options);
	};
	
	
	/**
	 * on key press
	 */
	function onKeyPress(event){
		
		var obj = jQuery(event.target);
		if(obj.is("textarea") || obj.is("select") || obj.is("input"))
			return(true);
		
		var keyCode = (event.charCode) ? event.charCode :((event.keyCode) ? event.keyCode :((event.which) ? event.which : 0));
		var isControlKey = event.ctrlKey;
		
		var wasAction = true;
		
		 switch(keyCode){
			 case 90:	//zed
				 if(isControlKey == true){
					 
					 runAction("undo");
					 
				 }else
					 wasAction = false;
					 
			 break;
			 default:
				 wasAction = false;
			 break; 
		 }
		
		
		if(wasAction == true){
			 event.preventDefault();
			 event.stopPropagation();
			 event.stopImmediatePropagation();
		}
		
		
	}
	
	
	/**
	 * init events
	 */
	function initEvents(){
		
		jQuery("#uc_button_update_layout").on("click",updateLayout);
		jQuery("#uc_button_rename_page").on("click",onSaveTitleClick);
								
		jQuery(".uc-save-status-close").on("click",function(){
			var objMessage = jQuery(this).parents(".uc-save-status");
			objMessage.hide();
		});
		
		jQuery("#uc_button_grid_settings").on("click",function(){
			
			if(g_gridBuilder)
				g_gridBuilder.doAction("open_grid_settings");
			
		});
		
		//on action buttons click
		g_panelActions.onEvent(g_panelActions.events.BUTTON_CLICK, function(event, action){
			runAction(action);
		});
		
		g_panel.onEvent(g_panel.events.ACTION_BUTTON_CLICK, function(event, action, params){
			runAction(action, params);
		});
		
		g_panel.onEvent(g_panel.events.SWITCH_PANE, checkPanelMenuState);
		g_panel.onEvent(g_panel.events.SHOW, checkPanelMenuState);
		g_panel.onEvent(g_panel.events.HIDE, checkPanelMenuState);
		
		
		//init grid events:
		t.onEvent(t.events.IFRAME_INITED, function(){
			
			g_gridBuilder.onEvent(g_gridBuilder.events.CHANGE_TAKEN, onGridChangeTaken);
			g_gridBuilder.onEvent(g_gridBuilder.events.BODY_CLICK, function(){
				jQuery("body").trigger("click");
			});
			
			jQuery(document).keydown(onKeyPress);
			
		});
		
		//run grid action event from everywhere
		g_ucAdmin.onEvent("run_grid_action", function(event, action, params){
			
			runAction(action, params);
			
		});
		
		//init sreenshot events
		initSaveScreenshotEvents();
		
	}
	
	
	/**
	 * init grid builder from inside of iframe
	 */
	this.initGridBuilder = function(objGridBuilder){
		
		g_gridBuilder = objGridBuilder;
		
		g_objWrapper.addClass("uc-state-inited");
		
		triggerEvent(t.events.IFRAME_INITED);
	};
	
	
	/**
	 * get panel wrapper
	 */
	this.getSidePanel = function(){
		
		return(g_panel);
	};
	
	
	/**
	 * init memory container
	 */
	function initBufferContainer(){
		
		g_objBuffer.addType("row", g_uctext["row"]);
		g_objBuffer.init();
		
	}
	
	/**
	 * get buffer
	 */
	this.getObjBuffer = function(){
		
		return(g_objBuffer);
	};
	
	/**
	 * get browser
	 */
	this.getObjBrowser = function(type){
		
		switch(type){
			case "sections":
				return(g_objBrowserSections);
			break;
			case "shape_deviders":
				
				var objBrowser = g_objSettings.getObjAddonBrowser("shape_devider");
				
				return(objBrowser);				
			break;
			default:
				return(g_objBrowser);
			break;
		}
		
	};
	
	
	/**
	 * get iframe
	 */
	this.getIframe = function(){
		return(g_objIframe);
	};
	
	/**
	 * on before unload - decide if show message before depend on save state
	 */
	this.onBeforeUnload = function(){
		
		if(g_temp.skip_message_onexit == true){
			g_temp.skip_message_onexit = false;
			return(false);
		}
		
		return g_temp.is_save_enabled;
	};
	
	
	/**
	 * init browser
	 */
	function initBrowser(browserID, objBrowser, noInit){
		
		var objBrowserWrapper = jQuery(browserID);
		
		g_ucAdmin.validateDomElement(objBrowserWrapper, "browser with id: "+browserID);
		
		if(noInit !== true)
			objBrowser.init(objBrowserWrapper);
		
	}
	
	
	/**
	 * init browsers
	 */
	function initBrowsers(){
		
		//addons browser
		
		initBrowser("#uc_addon_browser_regular_addon", g_objBrowser);
		initBrowser("#uc_addon_browser_layout_section", g_objBrowserSections);
		
	}
	
	/**
	 * get window.body
	 */
	this.getDomBody = function(){
		
		var objBody = jQuery("body");
		return(objBody);
	};
	
	
	/**
	 * init the outer grid builder
	 */
	this.init = function(){
		
		g_objWrapper = jQuery("#uc_page_builder");
		g_ucAdmin.validateDomElement(g_objWrapper, "page builder wrapper");
		
		g_objIframe = g_objWrapper.find("iframe.uc-layout-iframe");
		g_ucAdmin.validateDomElement(g_objIframe, "page builder iframe");
		
		checkRedirectLayout();
		
		//get layout ID - if exists		
		g_layoutID = g_objWrapper.data("pageid");
		g_layoutType = g_objWrapper.data("layouttype");
		if(!g_layoutType)
			g_layoutType = null;
				
		
		//init options
		var options = g_objWrapper.data("options");
		g_ucAdmin.validateObjProperty(options, ["url_screenshot_template"]);
		
		g_temp.url_screnshot_template = options.url_screenshot_template;
		
		g_temp.takeScreeshotOnSave = options.screenshot_on_save;
				
		//init browsers
		initBrowsers();
				
		
		//init buffer
		initBufferContainer();
		
		//init side panel
		g_objPanelWrapper = g_objWrapper.find(".uc-grid-panel");
		g_ucAdmin.validateDomElement(g_objPanelWrapper, "grid panel wrapper");
		
		initCustomSettingTypes();		
		
		g_panel.init(g_objPanelWrapper, g_objBuffer);
		
		if(!g_layoutID)
			g_layoutID = null;
		
		initShortcode();
		
		//init actions panel
		var objActionsPanelWrapper = g_objWrapper.find(".uc-edit-layout-panel");
		g_ucAdmin.validateDomElement(objActionsPanelWrapper, "actions panel");
		
		g_panelActions.init(objActionsPanelWrapper);
		
		initEvents();		
		
		initImportLayoutDialog();
		
	};
	
}