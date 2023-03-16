"use strict";

function UniteCreatorAdmin(){
	
	var t = this, g_objWrapper;
	var g_providerAdmin = new UniteProviderAdminUC();
	var g_objIncludes = new UniteCreatorIncludes();
	
	var g_generalSettings = new UniteSettingsUC();
	
	var g_paramsEditorMain = new UniteCreatorParamsEditor();
	var g_paramsEditorItems = new UniteCreatorParamsEditor();
	
	var g_objDialogParam = new UniteCreatorParamsDialog();
	var g_objDialogItemVariable = new UniteCreatorParamsDialog();
	var g_objDialogMainVariable	= new UniteCreatorParamsDialog();
	
	var g_codemirrorCss = null, g_codemirrorJs = null;
	var g_codemirrorCssItem = null;
	var g_codemirrorHtmlItem = null, g_codemirrorHtml = null;
	var g_codemirrorHtmlItem2 = null, g_objButtonsPanel;
	
	var g_objAssetsManager = new UCAssetsManager();
	var g_objAssetsIncludes = new UCAssetsManager();
	var g_settingsItem = new UniteSettingsUC();
	var g_settingsJS = new UniteSettingsUC();
	
	var g_addonID = null, g_objItemSettingsWrapper, g_objWrapperItems;
	
	//param panels
	var g_paramsPanelMain = new UniteCreatorParamsPanel();
	
	var g_paramsPanelItem = new UniteCreatorParamsPanel();
	var g_paramsPanelItem2 = new UniteCreatorParamsPanel();
	
	var g_paramsPanelJs = new UniteCreatorParamsPanel();
	var g_paramsPanelCss = new UniteCreatorParamsPanel();
	var g_paramsPanelCssItem = new UniteCreatorParamsPanel();
	
	var g_objVariables = new UniteCreatorVariables();
	
	
	var g_temp = {
			isAssetsUpdated: false,
			includesLoadPath: "",
			isItemsAsPostsMode:false,
			itemsByPostsParam:null,
			typeCurrentPost: "uc_current_post",
			typePost: "uc_post",
			putCodeExamplesParams:false
	};
	
	
	if(!g_ucAdmin)
		var g_ucAdmin = new UniteAdminUC();
	
	
	function ____________COMMON____________(){};
	
	
	/**
	 * get add options
	 */
	function getAddonAddOptions(){
		
		var objOptions = g_objWrapper.find("input.uc-addon-addoption");
		
		if(objOptions.length == 0)
			return(null);
		
		var optionsAdd = null;
		jQuery.each(objOptions,function(index, input){
			var objInput = jQuery(input);
			var type = input.type;
			var name = input.name;
			switch(type){
				case "checkbox":
					var value = objInput.is(":checked");
				break;
				default:
					var value = objInput.val();
				break;
			}
			
			if(optionsAdd === null)
				optionsAdd = {};
			
			optionsAdd[name] = value;
		});
		
		
		return(optionsAdd);
	}
	
	
	/**
	 * get addon options
	 */
	function getAddonOptions(){
		
		//get item settings
		var data = g_settingsItem.getSettingsValues();
		data.path_assets = t.getPathAssets();
		
		//get js settings
		var dataJSSettings = g_settingsJS.getSettingsValues();

		data = jQuery.extend(data, dataJSSettings);
		
		var generalSettingsData = g_generalSettings.getSettingsValues();
				
		data = jQuery.extend(data, generalSettingsData);
		
		var addOptions = getAddonAddOptions();
		if(typeof addOptions == "object")
			data = jQuery.extend(data, addOptions);
			
		return(data);
	}
		
	/**
	 * get data from addon view
	 */
	function getDataFromAddonView(){
		
		var objParams = g_paramsEditorMain.getParamsData();
		var objParamsItems = g_paramsEditorItems.getParamsData();
		
		var objParamsCatData = g_paramsEditorMain.getCatData();
		
		var html = g_codemirrorHtml ? g_codemirrorHtml.getValue() : jQuery("#area_addon_html").val();
		var htmlItem = g_codemirrorHtmlItem ? g_codemirrorHtmlItem.getValue() : jQuery("#area_addon_html_item").val();
		var htmlItem2 = g_codemirrorHtmlItem2 ? g_codemirrorHtmlItem2.getValue() : jQuery("#area_addon_html_item2").val();
		
		var css = g_codemirrorCss ? g_codemirrorCss.getValue() : jQuery("#area_addon_css").val();
		var cssItem = g_codemirrorCssItem ? g_codemirrorCssItem.getValue() : jQuery("#area_addon_css_item").val();
		var js = g_codemirrorJs ? g_codemirrorJs.getValue() : jQuery("#area_addon_js").val();
		
		var dataIncludes = g_objIncludes.getIncludesTabData();
		
		var options = getAddonOptions();
				
		var variables_item = g_objVariables.getArrVars(g_objVariables.types.ITEM);		
		var variables_main = g_objVariables.getArrVars(g_objVariables.types.MAIN);
		
		var data = {
				title: jQuery("#text_addon_title").val(),
				name: jQuery("#text_addon_name").val(),
				html: html,
				html_item: htmlItem,
				html_item2: htmlItem2,
				css: css,
				css_item: cssItem,
				js: js,
				includes_js: dataIncludes.arrJS,
				includes_jslib: dataIncludes.arrJSLib,
				includes_css: dataIncludes.arrCSS,
				params: objParams,
				params_items: objParamsItems,
				options: options,
				variables_item: variables_item,
				variables_main: variables_main
		};
		
		if(objParamsCatData)
			data.params_cats = objParamsCatData;
		
		if(g_addonID)
			data.id = g_addonID;
		
		return(data);
	}
	
	function ____________CODEMIRROR____________(){};
	
	
	/**
	 * replace editor text with attribute
	 */
	function replaceEditorTextWithAttribute(objEditor, type){
		
		var text = objEditor.getSelection();
		
		if(!text)
			return(false);
		
		var selections = objEditor.getSelections();
		if(selections.length > 1)
			return(false);
		
		var type = "text";
		
		var data = {};
		data.isnew = true;
		data["default_value"] = "max_default";
		data["font_editable"] = false;
		data["name"] = "max_name";
		data["title"] = "title";
		data["type"] = "uc_textfield";
		
		g_paramsEditorMain.onAddParamButtonClick(data);
		
		
	}
	
	
	/**
	 * set editor events
	 */
	function initCodeMirrorEvents(editor, type){
		
		editor.on("keyup",function(editor, event){
			
			if(!event)
				return(false);
			
			//ctrl+space
			if(event.code && event.code == "Space" && event.ctrlKey == true){
				replaceEditorTextWithAttribute(editor, type);
			}
			
		});

	}
	
	/**
	 * set the editor to the text area
	 */
	function setCodeMirrorEditor(type){
				
		switch(type){
		
			case "html":
								
				if(g_codemirrorHtml)
					return(false);
				
				g_codemirrorHtml = true;
				
				setTimeout(function(){
				    
					var objTextArea = jQuery("#area_addon_html");
				    var textArea = objTextArea[0];
					  
				      var mixedMode = {
				    	        name: "htmlmixed"
				      };
				      
				      var mode = objTextArea.data("mode");
					  if(mode)  		
						  mixedMode = mode;
				      
				      var optionsCM = {
								mode: mixedMode,
								lineNumbers: true
					  };
				    
				    				    				    
					g_codemirrorHtml = CodeMirror.fromTextArea(textArea, optionsCM);
					
					//initCodeMirrorEvents(g_codemirrorHtml, "main");
					
					
					/*
					g_codemirrorHtml.on("change", function(){
						trace("on change");
					});
					*/
					
				}, 500);
				
			break;
			case "html_item":
																
				if(g_codemirrorHtmlItem)
					return(false);
				
				if(isItemsEnabled() == false)
					return(false);
								
				g_codemirrorHtmlItem = true;
				
				setTimeout(function(){
				      var mixedMode = {
				    	        name: "htmlmixed"
				      };
				      var optionsCM = {
								mode: mixedMode,
								lineNumbers: true
					        };
				    
				     var objAreaItem = document.getElementById("area_addon_html_item");
				    	
					g_codemirrorHtmlItem = CodeMirror.fromTextArea(objAreaItem, optionsCM);
										
				}, 500);
				
			break;
			
			case "html_item2":
				if(g_codemirrorHtmlItem2)
					return(false);
				
				if(isItemsEnabled() == false)
					return(false);
				
				g_codemirrorHtmlItem2 = true;
				
				setTimeout(function(){
				      var mixedMode = {
				    	        name: "htmlmixed"
				      };
				      var optionsCM = {
								mode: mixedMode,
								lineNumbers: true
					        };
				      					
					g_codemirrorHtmlItem2 = CodeMirror.fromTextArea(document.getElementById("area_addon_html_item2"), optionsCM);
										
				}, 500);
				
			break;
			
			case "css":
				
				if(g_codemirrorCss)
					return(false);
				
				g_codemirrorCss = true;
				
				setTimeout(function(){
					g_codemirrorCss = CodeMirror.fromTextArea(document.getElementById("area_addon_css"), {
			            mode: {name: "css"},
			            lineNumbers: true
			        });
				}, 500);
				
			break;
			case "css_item":
				if(g_codemirrorCssItem)
					return(false);
				
				g_codemirrorCssItem = true;
				
				setTimeout(function(){
					g_codemirrorCssItem = CodeMirror.fromTextArea(document.getElementById("area_addon_css_item"), {
			            mode: {name: "css"},
			            lineNumbers: true
			        });
				}, 500);
				
			break;
			case "js":
				
				if(g_codemirrorJs)
					return(false);
				
				g_codemirrorJs = true;
				
				setTimeout(function(){
					g_codemirrorJs = CodeMirror.fromTextArea(document.getElementById("area_addon_js"), {
			            mode: {name: "javascript"},
			            lineNumbers: true
			        });
				}, 500);
				
			break;
		}
		
	}
	
	function ____________BULK_DIALOG____________(){};
	
	
	/**
	 * open bulk dialog
	 */
	function openBulkDialog(event, params){
		
		var objDialog = jQuery("#uc_dialog_bulk");
		g_ucAdmin.validateDomElement(objDialog, "bulk dialog");
		
		var options = {minHeight: 450};
		
		g_ucAdmin.openCommonDialog("uc_dialog_bulk", function(){
			
			params.addon_id = g_addonID;
			
			objDialog.html("loading...");
			
			objDialog.data("params", params);
						
			g_ucAdmin.ajaxRequest("get_addon_bulk_dialog", params, function(response){
								
				objDialog.html(response.html);
				
			});
			
		},options);
		
	}
	
	/**
	 * update selected text
	 */
	function bulkDialogUpdateSelectedText(){
		var objDialog = jQuery("#uc_dialog_bulk");
		var objCheckboxes = objDialog.find("input.uc-check-select:checked");
		var numSelected = objCheckboxes.length;
		
		jQuery("#uc_bulk_dialog_num_selected").text(numSelected);
		
	}
	
	
	/**
	 * on checkbox select - mark row
	 */
	function bulkDialogOnCheckboxSelect(event, objCheckbox){
		
		if(!objCheckbox)
			var objCheckbox = jQuery(this);
		
		var isChecked = objCheckbox.is(":checked");
		
		var objRow = objCheckbox.parents("tr");
		if(isChecked)
			objRow.addClass("unite-row-selected");
		else
			objRow.removeClass("unite-row-selected");
		
		bulkDialogUpdateSelectedText();
	}
	
	
	/**
	 * init bulk dialog 
	 */
	function initBulkDialog(){
		
		var objDialog = jQuery("#uc_dialog_bulk");
		
		//--- select all click
		
		objDialog.on("click", ".uc-check-all", function(){
			
			var isChecked = jQuery(this).is(":checked");
			
			var objCheckboxes = objDialog.find("input.uc-check-select");
			
			if(isChecked)
				objCheckboxes.attr("checked","checked");
			else
				objCheckboxes.removeAttr("checked");
			
			objCheckboxes.each(function(index, input){
				var objInput = jQuery(input);
				bulkDialogOnCheckboxSelect(null, objInput);				
			});
			
			bulkDialogUpdateSelectedText();
		});
		
		//--- checkbox click event
		
		objDialog.on("click", "input.uc-check-select", bulkDialogOnCheckboxSelect);
		
		// ---- actions -----
		
		objDialog.on("click", ".uc-action-button", function(){
			
			var objButton = jQuery(this);
			
			var action = objButton.data("action");
			var objLoader = objDialog.find(".uc-dialog-loader");
			var objTable = objDialog.find(".unite_table_items");
			
			var params = objDialog.data("params");
			
						
			//get addons id's
			var addonsIDs = [];
			var objCheckboxes = objDialog.find("input.uc-check-select:checked");
			jQuery.each(objCheckboxes, function(index, checkbox){
				var addonID = jQuery(checkbox).data("id");
				addonsIDs.push(addonID);
			});
			
			if(addonsIDs.length == 0){
				alert("no addons chosen");
				return(false);
			}
			
			params.addon_ids = addonsIDs;
			params.action_bulk = action;
			
			//set loading state
			
			objDialog.find(".uc-section-selected").css("opacity",0);
			objTable.hide();
			objLoader.show();
			
			g_ucAdmin.ajaxRequest("update_addons_bulk", params, function(response){
				
				objDialog.html(response.html);
			});
			
			
		});
		
		
	}
	
	
	function ____________ITEMS_RELATED____________(){};
	
	
	/**
	 * is items enabled
	 */
	function isItemsEnabled(){
				
		if(g_temp.isItemsAsPostsMode == true)
			return(true);
		
		var objValues = g_settingsItem.getSettingsValues();
		var enableItems = objValues.enable_items;
		enableItems = g_ucAdmin.strToBool(enableItems);
		
		return(enableItems);
	}
	
	
	/**
	 * hide items related objects
	 */
	function hideItemsRelated(){
		g_objWrapperItems.hide();
		jQuery(".uc-items-related").hide();
	}
	
	
	/**
	 * show items related objects
	 */
	function showItemsRelated(){
		
		g_objWrapperItems.show();
		jQuery(".uc-items-related").show();
		
	}
	
	
	/**
	 * get if items is visible
	 */
	function isItemsVisible(){
		
		var isVisible = g_objWrapperItems.is(":visible");
		return(isVisible);
	}
	
	
	/**
	 * set items as posts mode
	 */
	function setItemsAsPostsMode(param){
				
		//---- items tab -----
		
		//g_settingsItem.setValues({"enable_items":true});
		
		var itemsParamType = param["items_param_type"];
		
		var itemsParam = jQuery.extend({}, param);
		itemsParam.type = itemsParamType;
		
		g_temp.isItemsAsPostsMode = true;
		g_temp.itemsByPostsParam = itemsParam;
				
		g_objWrapperItems.hide();
		jQuery("#uc_tab_itemattr .unite_settings_wrapper").hide();
		
		var itemTabText = param["items_panel_text"];
		
		jQuery(".uc-postsitems-related").remove();
		jQuery("#uc_tab_itemattr").append("<div class='uc-postsitems-related'>"+itemTabText+"</div>");
		
		jQuery(".uc-items-related").show();
		
		//refresh current tab
		addonSelectTab();
	}
	
	
	/**
	 * remove items as posts mode
	 */
	function disableItemsAsPostsMode(){
				
		g_temp.isItemsAsPostsMode = false;
		g_temp.itemsByPostsParam = null;
		
		jQuery(".uc-items-related").hide();
		jQuery(".uc-postsitems-related").remove();
		
		jQuery("#uc_tab_itemattr .unite_settings_wrapper").show();
		
		onSettingsItemsChange();
	}
	
	
	/**
	 * check items by posts mode, and set if do
	 */
	function checkItemsByPostsMode(arrParams){
		
		var param = getPostsListParam(arrParams);
				
		//set items by posts mode
		if(param){
			
			setItemsAsPostsMode(param);
			return(false);
			
		}
		
		if(g_temp.isItemsAsPostsMode == true)
			disableItemsAsPostsMode();
		
	}
	
	
	function ____________PARAMS_PANEL____________(){};

	/**
	 * get posts list param from params
	 */
	function getPostsListParam(arrParams){
		
		if(!arrParams)
			return(null);
		
		for(var index in arrParams){
			var param = arrParams[index];
			
			switch(param.type){
				case "uc_listing":
										
					var useFor = g_ucAdmin.getVal(param, "use_for");
															
					if(useFor == "template" || useFor == "gallery"){
						param["items_param_type"] = "uc_listing";
						param["items_panel_text"] = "Items as dynamic loop mode";
												
						return(param);
					}
					
				break;
				case "uc_posts_list":
					param["items_param_type"] = "uc_post";
					param["items_panel_text"] = "Items as posts mode";
					return(param);
				break;
				case "uc_instagram":
					param["items_param_type"] = "uc_instagram_item";
					param["items_panel_text"] = "Items as instagram mode";
					return(param);
				break;
				case "uc_form":
					param["items_param_type"] = "uc_form_item";
					param["items_panel_text"] = "Items as form mode";
					return(param);
				break;
				case "uc_dataset":
					param["items_param_type"] = "uc_dataset";
					param["items_panel_text"] = "Items as dataset mode";
					return(param);
				break;
			}
		}
		
		return(null);
	}
	
	
	/**
	 * check dynamic params
	 */
	function paramsPanelMainSync_addDynamicParams(arrParams){
		
		//check if dynamic addon
		var options = getAddonOptions();
		var isDynamicAddon = g_ucAdmin.getVal(options, "dynamic_addon");
		isDynamicAddon = g_ucAdmin.strToBool(isDynamicAddon);
		
		if(isDynamicAddon == false)
			return(arrParams);
		
		//push post
		var objParam = {type:g_temp.typeCurrentPost, name:"current_post"};
		arrParams.push(objParam);
		
		return(arrParams);
	}
	
	/**
	 * add code examples
	 */
	function addEndingParams_addCodeExamples(arrParams){
		
		//push code examples
		var objParam = {type:"uc_code_examples", name:"code_examples", visual:"Twig Code Examples" };
		arrParams.push(objParam);
		
		return(arrParams);
	}
	
	/**
	 * add cod eexamples - js
	 */
	function addEndingParams_addCodeExamplesJS(arrParams){
		
		//push code examples
		var objParam = {type:"uc_code_examples_js", name:"code_examples_js", visual:"JS Code Snippets" };
		arrParams.push(objParam);
		
		return(arrParams);
	}
	
	
	/**
	 * add ending params
	 */
	function paramsPanelMainSync_addEndingParams(arrParams){
		
		if(g_temp.putCodeExamplesParams == true)
			arrParams = addEndingParams_addCodeExamples(arrParams);
		
		
		return(arrParams);
	}
	
	/**
	 * add ending params
	 */
	function paramsPanelMainSync_addEndingParamsJS(arrParams){
		
		if(g_temp.putCodeExamplesParams == true)
			arrParams = addEndingParams_addCodeExamplesJS(arrParams);
		
		
		return(arrParams);
	}
	
	
	/**
	 * sync params from ed
	 */
	function paramsPanelMainSync(){
				
		var arrParams = g_paramsEditorMain.getParamsData();
		
		checkItemsByPostsMode(arrParams);
		
		var arrVars = g_objVariables.getArrVars(g_objVariables.types.MAIN);
		
		//add items param
		var hasItems = isItemsEnabled();
		
		arrParams = paramsPanelMainSync_addDynamicParams(arrParams);
		
		arrParams = paramsPanelMainSync_addEndingParams(arrParams);
		
		var arrParamsForCss = jQuery.extend([], arrParams);
		var arrParamsForJS = jQuery.extend([], arrParams);

		//modify params for js
		if(hasItems == true){
			
			//put extra js functions - put_items_json
			var textPutItemsJson = "{#use put_items_json(\"clean\") to remove extra data#}";
			textPutItemsJson += "\nvar strJsonItems = {{put_items_json()}};";
			textPutItemsJson += "\nvar objItems = JSON.parse(strJsonItems);";
			textPutItemsJson += "\nconsole.log(objItems);";
			
			var paramPutItemJs = {type:"uc_function", name:"put_items_json()","raw_insert_text":textPutItemsJson};
				
			//put extra js functions - put_attributes_json
			var textPutAttributesJson = "{#use put_attributes_json(\"clean\") to remove extra data#}";
			textPutAttributesJson += "\nvar strJsonAttributes = {{put_attributes_json()}};";
			textPutAttributesJson += "\nvar objAttributes = JSON.parse(strJsonAttributes);";
			textPutAttributesJson += "\nconsole.log(objAttributes);";
			
			var paramPutAttributesJs = {type:"uc_function", name:"put_attributes_json()","raw_insert_text":textPutAttributesJson};
			
			
			var textGetItems = "{# Generate javascript array from items using twig#}";
			textGetItems += "\n{% set itemsForJs = get_items() %}";
			textGetItems += "\n\nvar arrTitles = [";
			textGetItems += "\n	{% for item in itemsForJs %}";
			textGetItems += "\n		\"{{item.title|raw}}\",";
			textGetItems += "\n	{% endfor %}";
			textGetItems += "\n\n];";
			textGetItems += "\n\nconsole.log(arrTitles);\n";
			
			var paramGetItemJs = {type:"uc_function", 
					name:"get_items()",
					"raw_insert_text":textGetItems
			};
			
			arrParamsForJS.unshift(paramPutAttributesJs);
			arrParamsForJS.unshift(paramPutItemJs);
			arrParamsForJS.unshift(paramGetItemJs);
			
		}
		
		arrParamsForJS = paramsPanelMainSync_addEndingParamsJS(arrParamsForJS);
		
		//add items related functions
		if(hasItems == true){
			var paramPutItems = {type:"uc_function", name:"put_items()",raw_insert_text:"{{put_items()}} {# - you can use parameters: \"shuffle\", \"one_random\", \"one_first\" like put_items(\"shuffle\")  #}"};
			var paramPutItems2 = {type:"uc_function", name:"put_items2()"};
			var paramNumItems = {type:"text", name:"uc_num_items"};
			
			//add to top of the params
			
			arrParams.unshift(paramPutItems2);
			arrParams.unshift(paramPutItems);
			arrParams.unshift(paramNumItems);
		}
		
				
		g_paramsPanelCss.setParams(arrParamsForCss);
		
		
		g_paramsPanelJs.setParams(arrParamsForJS);
		g_paramsPanelMain.setParams(arrParams, arrVars);
		
		//sync item params as well
		paramsPanelItemSync();
	}
	
	
	/**
	 * sync params from ed
	 */
	function paramsPanelItemSync(){
				
		if(g_temp.isItemsAsPostsMode == false)
			var arrParamsItems = g_paramsEditorItems.getParamsData();
		else{
			var arrParamsItems = [];
			arrParamsItems.push(g_temp.itemsByPostsParam);
		}
		
		var arrVarsItems = g_objVariables.getArrVars(g_objVariables.types.ITEM);
		
		var arrParamsMain = g_paramsEditorMain.getParamsData();
		var arrVarsMain = g_objVariables.getArrVars(g_objVariables.types.MAIN);
	
		var arrPanels = [g_paramsPanelItem, g_paramsPanelItem2, g_paramsPanelCssItem];
		
		jQuery.map(arrPanels,function(objPanel){
			
			
			objPanel.removeAllParams();
			objPanel.setParams(arrParamsItems, arrVarsItems, "item");
			objPanel.setParams(arrParamsMain, arrVarsMain, "main");
			
		});
		
	}
	
	
	/**
	 * init param panel globals
	 */
	function initParamPanelGlobals(arrChildKeys, initParamPanelGlobals, arrAddKeys, arrSkipParams){
		
		var objParamPanel = new UniteCreatorParamsPanel();
		objParamPanel.initGlobalSetting_ChildKeys(arrChildKeys, arrAddKeys);
		objParamPanel.initGlobalSetting_TemplateCode(initParamPanelGlobals);
		objParamPanel.initGlobalSetting_SkipParams(arrSkipParams);
		
		
	}
	
	
	/**
	 * init the params panel
	 */
	function initParamsPanels(arrPanelKeys, arrPanelItemKeys){
				
		//add to beginning main params
		var objWrapperMain = jQuery("#uc_params_panel_main");
		var objWrapperJs = jQuery("#uc_params_panel_js");
		var objWrapperCss = jQuery("#uc_params_panel_css");
		var objWrapperCssItem = jQuery("#uc_params_panel_css_item");
				
		var objWrapperItem = jQuery("#uc_params_panel_item");
		var objWrapperItem2 = jQuery("#uc_params_panel_item2");
		
		
		g_paramsPanelMain.init(objWrapperMain,"main", null, arrPanelKeys);
		g_paramsPanelJs.init(objWrapperJs,"js", null, arrPanelKeys);
		g_paramsPanelCss.init(objWrapperCss,"css", null, arrPanelKeys);
		
		//init items panels
		var arrPanelItems = [
		                     [g_paramsPanelItem, objWrapperItem], 
		                     [g_paramsPanelItem2, objWrapperItem2],
		                     [g_paramsPanelCssItem, objWrapperCssItem]
		];
		
		var itemPrefix = {"item":"item."};
		
		jQuery.map(arrPanelItems, function(arr){
						
			var itemsPanel = arr[0];
			var objPanelWrapper = arr[1];
				
			itemsPanel.init(objPanelWrapper,"item", itemPrefix, arrPanelItemKeys);
			itemsPanel.initConstants(arrPanelKeys, "main");
			itemsPanel.initConstants(arrPanelItemKeys, "item");
			
		});
		
		paramsPanelMainSync();

		//on params update event
		g_paramsEditorMain.onUpdateEvent(onParamsEditorMainUpdate);
		g_paramsEditorItems.onUpdateEvent(paramsPanelItemSync);
		
		g_paramsEditorMain.onEvent(g_paramsEditorMain.events.BULK, openBulkDialog);
		g_paramsEditorItems.onEvent(g_paramsEditorItems.events.BULK, openBulkDialog);
		
		
		//on click events
		g_paramsPanelMain.onParamClick(function(textToAdd){
			g_ucAdmin.insertToCodeMirror(g_codemirrorHtml, textToAdd);
		});
		
		g_paramsPanelJs.onParamClick(function(textToAdd){
			g_ucAdmin.insertToCodeMirror(g_codemirrorJs, textToAdd);
		});
		
		g_paramsPanelCss.onParamClick(function(textToAdd){
			g_ucAdmin.insertToCodeMirror(g_codemirrorCss, textToAdd);
		});
		
		g_paramsPanelCssItem.onParamClick(function(textToAdd){
			g_ucAdmin.insertToCodeMirror(g_codemirrorCssItem, textToAdd);
		});
		
		g_paramsPanelItem.onParamClick(function(textToAdd){
			g_ucAdmin.insertToCodeMirror(g_codemirrorHtmlItem, textToAdd);
		});
		
		g_paramsPanelItem2.onParamClick(function(textToAdd){
			g_ucAdmin.insertToCodeMirror(g_codemirrorHtmlItem2, textToAdd);
		});
		
		
		//delete main variable:
		g_paramsPanelMain.onDeleteVariable(function(event, varName){
			
			g_objVariables.deleteVar(g_objVariables.types.MAIN, varName);
			paramsPanelMainSync();
		});
		
		
		//delete item variable:
		g_paramsPanelItem.onDeleteVariable(function(event, varName){
			
			g_objVariables.deleteVar(g_objVariables.types.ITEM, varName);
			paramsPanelItemSync();
			
		});
		
		//delete item2 variable:
		g_paramsPanelItem2.onDeleteVariable(function(event, varName){
			
			g_objVariables.deleteVar(g_objVariables.types.ITEM, varName);
			paramsPanelItemSync();
			
		});
		
		//on edit Item variable
		function onParamsPanelEditItemVariable(event, varIndex){
			if(!varIndex || varIndex == undefined)
				var varIndex = 0;
			
			var objVar = g_objVariables.getVariable(g_objVariables.types.ITEM, varIndex);
			
			g_ucAdmin.validateNotEmpty(objVar, "variable: " + varIndex);
			
			g_objDialogItemVariable.open(objVar, varIndex, function(objUpdatedVar, varIndex){
				
				g_objVariables.update(g_objVariables.types.ITEM, varIndex, objUpdatedVar);
				
				paramsPanelItemSync();
				
			});
		}
		
		//edit item variable:
		g_paramsPanelItem.onEditVariable(onParamsPanelEditItemVariable);
		g_paramsPanelItem2.onEditVariable(onParamsPanelEditItemVariable);
		
		
		//edit main variable
		g_paramsPanelMain.onEditVariable(function(event, varIndex){
			if(!varIndex || varIndex == undefined)
				var varIndex = 0;
			
			var objVar = g_objVariables.getVariable(g_objVariables.types.MAIN, varIndex);
			
			g_ucAdmin.validateNotEmpty(objVar, "variable: " + varIndex);
			
			g_objDialogMainVariable.open(objVar, varIndex, function(objUpdatedVar, varIndex){
				
				g_objVariables.update(g_objVariables.types.MAIN, varIndex, objUpdatedVar);
				
				paramsPanelMainSync();
				
			});
			
		});
		
		
		//on settings change event
		g_settingsItem.setEventOnChange(function(){
			paramsPanelMainSync();
		});
		
	}
	
	function ____________VARIABLES_BUTTONS____________(){};
	
	
	
	/**
	 * init add variable button
	 */
	function initAddVariableButton(){
		
		//init item add variable
		var objButtonAddVariableItem = jQuery("#uc_params_panel_item_addvar");
		var objButtonAddVariableItem2 = jQuery("#uc_params_panel_item_addvar2");
		
		//on click - open add variable dialog
		objButtonAddVariableItem.add(objButtonAddVariableItem2).on("click",function(){
			
			g_objDialogItemVariable.open(null, 0, function(objVar){
				
				g_objVariables.add(g_objVariables.types.ITEM, objVar);
				
				paramsPanelItemSync();
				
			});
			
		});
		
		
		//init main add variable
		var objButtonAddVariableMain = jQuery("#uc_params_panel_main_addvar");
		
		objButtonAddVariableMain.on("click",function(){
						
			g_objDialogMainVariable.open(null, 0, function(objVar){
				
				g_objVariables.add(g_objVariables.types.MAIN, objVar);
				
				paramsPanelMainSync();
				
			});
			
		});
		
		
	}
	
	
	/**
	 * init variables, must init before the params panels
	 */
	function initVariables(arrVarItems, arrVarMain){
		
		g_objVariables.addFromArray(g_objVariables.types.ITEM, arrVarItems);
		g_objVariables.addFromArray(g_objVariables.types.MAIN, arrVarMain);
		
		initAddVariableButton();
	}
	
	
	function ____________INCLUDES_TAB____________(){};

	
	/**
	 * update include assets checkboxes
	 */
	function updateIncludesAssetsCheckboxes(){
		
		var arrIncludes = g_objIncludes.getArrAllIncludesUrls();
		
		g_objAssetsIncludes.checkByUrls(arrIncludes);
		
	}
	
	
	/**
	 * init assets manager in includes folder
	 */
	function initAssetsManagerIncludes(){
		
		//on checkbox click, add/remove include
		g_objAssetsIncludes.eventOnSelectOperation(function(checked, itemData){
			
			if(checked == true)
				g_objIncludes.addIncludesFromAssets(itemData);
			else{
				g_objIncludes.removeIncludeByAsset(itemData);
			}
			
		});
		
		//on load path - add addonID
		g_objAssetsIncludes.eventOnAjaxLoadpath(function(data){
			data.addonID = g_addonID;
			return(data);
		});
		
		//after update filelist, check assets by url
		g_objAssetsIncludes.eventOnUpdateFilelist(function(){
			updateIncludesAssetsCheckboxes();
		});
		
		//check assets by urls
		updateIncludesAssetsCheckboxes();
		
	}
	
	
	/**
	 * init includes tab
	 */
	function initIncludesTab(){
		
		g_objIncludes.initIncludesTab(t);
		
		var objIncludesBrowserWrapper = jQuery("#uc_includes_browser");
		g_objAssetsIncludes.init(objIncludesBrowserWrapper);
		
		//init includes browser
		initAssetsManagerIncludes();
		
		//set on delete event
		g_objIncludes.eventOnDelete(function(){
			updateIncludesAssetsCheckboxes();
		});
		
		g_objIncludes.eventOnInputBlur(function(){
			updateIncludesAssetsCheckboxes();
		});
		
	}
	
	
	function ____________ASSETS_TAB____________(){};
	
	
	/**
	 * get assets path
	 */
	this.getPathAssets = function(){
		var pathAssets = jQuery("#uc_assets_path").data("path");
		return(pathAssets);
	};
	
	
	/**
	 * get assets url
	 */
	function getUrlAssets(){
		var pathAssets = t.getPathAssets();
		if(!pathAssets)
			return(pathAssets);
		
		var urlAssets = g_urlAssetsUC + pathAssets + "/";
		
		return(urlAssets);
	}
	
	
	/**
	 * update path for image select based on the assets path
	 */
	function updateImageSelectPath(){
		
		var pathAddonAssets = t.getPathAssets();
		var urlAssets = getUrlAssets();
		
		if(pathAddonAssets){
			pathAddonAssets = g_pathAssetsUC+pathAddonAssets;
		}
		
		var urlAssets = getUrlAssets();
		
		g_ucAdmin.triggerEvent("update_assets_path", urlAssets);
		
		g_ucAdmin.setAddImagePath(pathAddonAssets, urlAssets);
		
	}
	
	
	/**
	 * check set path of assets button, enable / disable if needed
	 */
	function assetsPathCheckButtons(){
		
		//disable button
		if(g_objAssetsManager.isStartPath() == true)
			g_ucAdmin.disableButton("#uc_button_set_assets_folder");
		else
			g_ucAdmin.enableButton("#uc_button_set_assets_folder");
		
	}
	
	
	
	/**
	 * init assets path related functions
	 */
	function initAssetsPath(){
		
		assetsPathCheckButtons();
		
		//init assets folder acions
		g_objAssetsManager.eventOnUpdateFilelist(function(){
			assetsPathCheckButtons();
		});
		
		//set path button:
		jQuery("#uc_button_set_assets_folder").on("click",function(){
			
			if(g_ucAdmin.isButtonEnabled(jQuery(this)) == false)
				return(false);
			
			var path = g_objAssetsManager.getActivePathRelative();
			jQuery("#uc_assets_path").html(path).data("path", path);
			jQuery("#uc_button_set_assets_unset").show();
			
			g_temp.includesLoadPath = path;
			
			updateImageSelectPath();
		});
		
		//unset path button:
		jQuery("#uc_button_set_assets_unset").on("click",function(){
			var textNotSet = jQuery("#uc_assets_path").data("textnotset");
			jQuery("#uc_assets_path").html(textNotSet).data("path", "");
			jQuery(this).hide();
			
			g_temp.includesLoadPath = "";
			
			updateImageSelectPath();
		});
		
	}
	
	
	/**
	 * init assets tab
	 */
	function initAssetsTab(){

		//init assets manager
		var objAssetsManagerWrapper = jQuery("#uc_assets_manager");
		if(objAssetsManagerWrapper.length == 0)
			return(false);
		
		g_objAssetsManager.init(objAssetsManagerWrapper);
		
		initAssetsPath();
		
		//set flag if the assets updated
		g_objAssetsManager.eventOnUpdateFiles(function(){
			g_temp.isAssetsUpdated = true;
		});
		
	}
	
	function ____________DYNAMIC____________(){};
	
	/**
	 * update dynamic post keys
	 */
	function updateDynamicPostKeys(arrPostChildKeys, key){
		
		var objPanel = new UniteCreatorParamsPanel();
		var objChildKeys = objPanel.getGlobalSetting_ChildKeys();
		
		objChildKeys[key] = arrPostChildKeys;
		objPanel.updateGlobalSetting_ChildKeys(objChildKeys);
		
		var objChildKeys = objPanel.getGlobalSetting_ChildKeys();
				
		paramsPanelMainSync();
	}
	
	
	
	/**
	 * refresh dynamic post fields (custom post)
	 * and sync again
	 */
	function refreshDynamicPostFields(){
		
		var options = getAddonOptions();
				
		var postID = g_ucAdmin.getVal(options, "dynamic_post");
		var enableCustomFields = g_ucAdmin.getVal(options, "dynamic_post_enable_customfields");
		enableCustomFields = g_ucAdmin.strToBool(enableCustomFields);
		
		var enableCategory = g_ucAdmin.getVal(options, "dynamic_post_enable_category");
		enableCategory = g_ucAdmin.strToBool(enableCategory);
		
		//if no post ID, replace current post by regular post (no meta keys)
		if(!postID){
			
			var objPanel = new UniteCreatorParamsPanel();
			var objChildKeys = objPanel.getGlobalSetting_ChildKeys();
			
			var arrPostKeys = g_ucAdmin.getVal(objChildKeys, "uc_post");
			
			updateDynamicPostKeys(arrPostKeys, g_temp.typeCurrentPost);
			
			return(false);
		}
		
		//request specific post variables by ajax
		ajaxRefreshChildPostParam(postID, enableCustomFields, enableCategory, false, g_temp.typeCurrentPost);
		
	}
	
	
	/**
	 * refresh child post param
	 */
	function ajaxRefreshChildPostParam(postID, enableCustomFields, enableCategory, isForWoo, childKey){
		
		var data = {
				postid: postID,
				enable_custom_fields: enableCustomFields,
				enable_category: enableCategory,
				enable_woo: isForWoo
		};
		
		g_ucAdmin.ajaxRequest("get_post_child_params", data, function(response){
						
			var arrPostParams = g_ucAdmin.getVal(response, "child_params_post");
			
			updateDynamicPostKeys(arrPostParams, childKey);
			
		});
		
	}
	
	function ____________SVG_ICON____________(){};
	
	/**
	 * get svg preview url
	 */
	function getUrlSvgPreview(){
		
		var urlAssets = getUrlAssets();
		
		if(!urlAssets)
			return(null);
		
		var objItem = g_objAssetsManager.getItemByFilename("preview_icon.svg");
				
		if(!objItem)
			return(null);
		
		var urlFull = g_ucAdmin.getVal(objItem, "full_url");
		
		return(urlFull);
	}
	
	
	/**
	 * update preview of svg icon
	 */
	function updateSvgIconPreview(){
		
		try{
			var urlPreview = getUrlSvgPreview();
			
			var objHolder = jQuery("#uc_widget_svg_holder");
			
			if(!urlPreview){
				objHolder.css("background-image","url('')");
				objHolder.hide();
				return(false);
			}
			
			objHolder.show();
			objHolder.css("background-image","url('"+urlPreview+"')");
			
		}catch(error){
			
			setTimeout(updateSvgIconPreview, 1000);
			
		}
		
	}
	
	
	function ____________EVENTS____________(){};
	
	
	/**
	 * on update addon button click
	 */
	function onUpdateAddonClick(){
				
		var data = getDataFromAddonView();
		var strData = g_ucAdmin.encodeObjectForSave(data);
		var passData = {addon_data:strData};
		
		g_ucAdmin.setAjaxLoaderID("uc_loader_update");
		g_ucAdmin.setSuccessMessageID("uc_message_addon_updated");
		g_ucAdmin.setAjaxHideButtonID("button_update_addon");
		g_ucAdmin.setErrorMessageID("uc_update_addon_error");
		
		g_ucAdmin.ajaxRequest("update_addon", passData);
	}


	/**
	 * on update addon button click
	 */
	function onUpdateAddonFromCatalogClick(){
		
		if(confirm("This operation will take the latest version of the addon from the online catalog, and rewrite any custom changes if you made. Proceed?") == false)
			return(false);
		
		var data = {};
		data.id = g_addonID;
		
		g_ucAdmin.setAjaxLoaderID("uc_loader_update_catalog");
		g_ucAdmin.setSuccessMessageID("uc_message_addon_updated_catalog");
		g_ucAdmin.setAjaxHideButtonID("uc_button_update_catalog");
		g_ucAdmin.setErrorMessageID("uc_update_addon_error");
		
		g_ucAdmin.ajaxRequest("update_addon_from_catalog", data);
	}
	
	/**
	 * enable code examples
	 */
	function onShowCodeExamplesClick(){
		
		g_temp.putCodeExamplesParams = true;
		
		paramsPanelMainSync();
		
	}
	
	
	/**
	 * on export addon click
	 */
	function onExportAddonClick(){
				
		var params = "id="+g_addonID;
		var urlExport = g_ucAdmin.getUrlAjax("export_addon", params);
		
		window.open(urlExport);
	}
	
	
	/**
	 * select tab in addon view
	 * tab is the link object to tab
	 */
	function addonSelectTab(objTab, nohash){
		
		if(!objTab)
			var objTab = jQuery("#uc_tabs a.uc-tab-selected");
		else{
			if(objTab.hasClass("uc-tab-selected"))
				return(false);
		}
		
		var contentID = objTab.data("contentid");
		var tabID = objTab.prop("id");
		
		jQuery("#uc_tab_contents .uc-tab-content").hide();
		
		jQuery("#" + contentID).show();
		
		jQuery("#uc_tabs a").not(objTab).removeClass("uc-tab-selected");
		objTab.addClass("uc-tab-selected");
		
		//add hash:
		if(nohash !== true)
			location.hash = "tab="+tabID;
				
		switch(contentID){
			case "uc_tab_general":
				
				setTimeout(updateSvgIconPreview, 500);
				
			break;
			case "uc_tab_html":
				setCodeMirrorEditor("html");
				setCodeMirrorEditor("html_item");
				setCodeMirrorEditor("html_item2");
			break;
			case "uc_tab_js":
				setCodeMirrorEditor("js");
			break;
			case "uc_tab_css":
				setCodeMirrorEditor("css");
				setCodeMirrorEditor("css_item");
			break;
			case "uc_tab_includes":
				
				//load includes path
				
				if(g_temp.isAssetsUpdated == true || g_temp.includesLoadPath != ""){
					g_temp.isAssetsUpdated = false;
					
					if(g_objAssetsIncludes){
						var loadPath = "";
						if(g_temp.includesLoadPath != ""){
							loadPath = g_temp.includesLoadPath;
							g_temp.includesLoadPath = "";
						}
						g_objAssetsIncludes.loadPath(loadPath);
					}
					
				}
				
			break;
		}
		
	}
	
	/**
	 * on settings item change - enable items yes/no
	 */
	function onSettingsItemsChange(){
		
		var enableItems = isItemsEnabled();
		var isVisible = isItemsVisible();
		
		if(enableItems == true && isVisible == false)
			showItemsRelated();
		else
			if(enableItems == false && isVisible == true)
				hideItemsRelated();
		
	}
	
	
	
	
	/**
	 * on general settings change
	 */
	function onGeneralSettingChange(event, data){
		
		var settingName = g_ucAdmin.getVal(data, "name");		
		
		switch(settingName){
			
			case "dynamic_addon":
				paramsPanelMainSync();
			break;
			case "dynamic_post":
			case "dynamic_post_enable_customfields":
			case "dynamic_post_enable_taxonomies":
			case "dynamic_post_enable_category":
				
				refreshDynamicPostFields();
				
			break;
		}
		
		
	}
	
	
	/**
	 * on update main params
	 * check dynamic change, and sync main params
	 */
	function onParamsEditorMainUpdate(){
		
		var objLastParam = g_paramsEditorMain.getLastUpdatedParam();
		
		if(!objLastParam){
			paramsPanelMainSync();
			return(false);
		}
		
		var paramType = g_ucAdmin.getVal(objLastParam, "type");
		
		switch(paramType){
			case "uc_posts_list":
				
				var postID = g_ucAdmin.getVal(objLastParam, "post_example");
				var enableCustomFields = g_ucAdmin.getVal(objLastParam, "use_custom_fields");
				enableCustomFields = g_ucAdmin.strToBool(enableCustomFields);
				
				var enableCategory = g_ucAdmin.getVal(objLastParam, "use_category");
				enableCategory = g_ucAdmin.strToBool(enableCategory);
				
				var isForWoo = g_ucAdmin.getVal(objLastParam, "for_woocommerce_products");
				isForWoo = g_ucAdmin.strToBool(isForWoo);
								
				ajaxRefreshChildPostParam(postID, enableCustomFields, enableCategory, isForWoo,  g_temp.typePost);
				
			break;
			default:
				paramsPanelMainSync();
			break;
		}
								
	}
	
	
	/**
	 * init events
	 */
	function initEvents(){
		
		//general settings events
		g_generalSettings.setEventOnChange(onGeneralSettingChange);
		
		//item tab events
		
		//change files event
		g_settingsItem.setEventOnChange(onSettingsItemsChange);
		
		onSettingsItemsChange();
		
		//expand click
		jQuery(".uc-tabcontent-link-expand").on("click",function(){
			
			var objLink = jQuery(this);
			var objRow = objLink.parents("tr");
			objRow.addClass("uc-row-expanded");
			
			objLink.hide();
			
		});
		
		jQuery("#button_update_addon").on("click",onUpdateAddonClick);
		jQuery("#button_export_addon").on("click",onExportAddonClick);
		jQuery("#uc_button_update_catalog").on("click",onUpdateAddonFromCatalogClick);
		
		jQuery(".uc-link-code-examples").on("click", onShowCodeExamplesClick);
		
	}
	
	function ____________STICKY_MENU____________(){};
	
	/**
	 * init sticky menu
	 */
	function initStickyButtonsPanel(){
		
		jQuery(window).scroll(function () {
			var desTop = jQuery(document).scrollTop();
			
			//clearTrace()
			//trace(desTop);
			
			if(desTop > 79){
				g_objButtonsPanel.addClass("uc-stick-top");
			} else {
				g_objButtonsPanel.removeClass("uc-stick-top");
			}
		});		
		
	}
	
	function ____________INIT____________(){};

	
	/**
	 * init the params editors
	 */
	function initParamsEditors(objParamsMain, objParamsItems, arrParamsCats){
		
		var objWrapperMain = jQuery("#attr_wrapper_main");
		g_objWrapperItems = jQuery("#attr_wrapper_items");
		var objDialogParam = jQuery("#uc_dialog_param_main");
		var objDialogItemVariable = jQuery("#uc_dialog_param_variable_item");
		var objDialogMainVariable = jQuery("#uc_dialog_param_variable_main");
		
		g_objDialogParam.init(objDialogParam, t);
		g_objDialogItemVariable.init(objDialogItemVariable, t);
		g_objDialogMainVariable.init(objDialogMainVariable, t);
		
		g_paramsEditorMain.init(objWrapperMain, objParamsMain, g_objDialogParam, arrParamsCats);
		g_paramsEditorItems.init(g_objWrapperItems, objParamsItems, g_objDialogParam);
		
	}
	
	
	/**
	 * init tabs
	 */
	function initTabs(){
		
		//select current tab
		var initTabID = jQuery("#uc_tabs").data("inittab");
		
		var objCurrentTab = jQuery("#"+initTabID);
		var hash = location.hash;
		if(hash){
			var tabID = hash.replace("tab=","");
			var objTab = jQuery(tabID);
			if(objTab.length)
				objCurrentTab = objTab;
		}
		
		addonSelectTab(objCurrentTab, true);
		
		jQuery("#uc_tabs a").on("click",function(){
			
			var objTabs = jQuery("#uc_tabs");
			if(objTabs.hasClass("uc-tabs-disabled"))
				return(false);
			
			var objTab = jQuery(this);
			addonSelectTab(objTab);
			
		});
		
		//remove the disabled class
		setTimeout(function(){
			var objTabs = jQuery("#uc_tabs");
			objTabs.removeClass("uc-tabs-disabled");
			
		},1000);
		
	}
	
	
	/**
	 * init tipsy
	 */
	function initTipsy(){
		
		if(typeof jQuery("body").tipsy != "function")
			return(false);
		
		var tipsyOptions = {
				html:true,
				gravity:"s",
		        delayIn: 200,
		        selector: ".uc-tip"
		};
		
		g_objWrapper.tipsy(tipsyOptions);
		
	}
	
	
	/**
	 * init items tab
	 */
	function initItemsTab(){
		
		g_objItemSettingsWrapper = jQuery("#uc_tab_itemattr").children(".unite_settings_wrapper");
		
		g_settingsItem.init(g_objItemSettingsWrapper);
		
	}
	
	/**
	 * init js tab
	 */
	function initJSTab(){
		
		var objJSSettingsWrapper = jQuery("#uc_tab_js").find(".unite_settings_wrapper");
		
		g_settingsJS.init(objJSSettingsWrapper);
				
	}

	
	/**
	 * put preview image
	 */
	function putPreviewImage(urlPreview){
		if(!urlPreview || urlPreview == "")
			return(false);
		
		var html = "";
		html += "<div id='uc_edit_addon_preview_image' class='uc-edit-addon-preview-image' style=\"background-image:url('"+urlPreview+"')\"></div>";
		
		jQuery("#unite_setting_text_preview_row .spanSettingsStaticText").parent().append(html);
	}
	
	
	
	
	/**
	 * init view by options related items
	 */
	function initByOptions(arrOptions){
		
		var urlPreview = arrOptions["url_preview"];
		if(urlPreview)
			putPreviewImage(urlPreview);
		
		var objPanel = new UniteCreatorParamsPanel();
		
		//set thumb sizes
		var objThumbSizes = arrOptions["thumb_sizes"];
		if(objThumbSizes){
			objPanel.initGlobalSetting_ThumbSizes(objThumbSizes);
		}
		
		//set image add fields
		var objImageParams = arrOptions["image_add_fields"];
		if(objImageParams){
			objPanel.initGlobalSetting_ImageAddParams(objImageParams);
		}
		
		
	}
	
	
	/**
	 * get control attributes with their values
	 */
	this.getControlParams = function(type){
		
		switch(type){
			default:
			case "main":
				var arrData = g_paramsEditorMain.getParamsData("control", true);
			break;
			case "items":
			case "item":
				var arrData = g_paramsEditorItems.getParamsData("control", true);
			break;
		}
		
		return(arrData);
	};
	
	
	/**
	 * edit addon view
	 */
	this.initEditAddonView = function(){
				
		g_objWrapper = jQuery("#uc_tab_contents");
		
		var objConfig = jQuery("#uc_edit_item_config");
		var objParamsMain = objConfig.data("params");
		var objParamsItems = objConfig.data("params-items");
		var arrPanelKeys = objConfig.data("panel-keys");
		
		var arrPanelItemKeys = objConfig.data("panel-item-keys");
		var arrPanelChildKeys = objConfig.data("panel-child-keys");
		var arrPanelAddKeys = objConfig.data("panel-add-keys");
		var arrPanelTemplateCode = objConfig.data("panel-template-code");
		var arrSkipParams = objConfig.data("panel-skip-params");
		
		var arrVariablesItems = objConfig.data("variables-items"); 
		var arrVariablesMain = objConfig.data("variables-main"); 
		var arrParamsCats = objConfig.data("params-cats");
		
		var arrOptions = objConfig.data("options");
		
		var objSettingsWrapper = jQuery("#uc_general_settings");
				
		if(jQuery("#addon_id").length)
			g_addonID = jQuery("#addon_id").data("addonid");
		
		g_objButtonsPanel = jQuery("#uc_buttons_panel");
		
		initStickyButtonsPanel();
		
		initItemsTab();
		
		initJSTab();
		
		initTabs();
		
		initTipsy();
		
		g_generalSettings.init(objSettingsWrapper);
		
		initIncludesTab();
		
		initParamsEditors(objParamsMain, objParamsItems, arrParamsCats);
		
		initAssetsTab();
		
		initByOptions(arrOptions);
		
		initVariables(arrVariablesItems, arrVariablesMain);	//must init before params panels
		
		//init param parnels
		initParamPanelGlobals(arrPanelChildKeys, arrPanelTemplateCode, arrPanelAddKeys, arrSkipParams);
		initParamsPanels(arrPanelKeys, arrPanelItemKeys);
		
		//focus the title if empty
		var title = jQuery("#text_addon_title").val();
		if(jQuery.trim(title) == "")
			jQuery("#text_addon_title").focus();
		
		initEvents();
		
		updateImageSelectPath();
		
		initBulkDialog();
		
	};
	
	this.________________INIT_OTHER_VIEWS_______________ = function(){};
	
	/**
	 * init assets manager view
	 */
	this.initAssetsManagerView = function(){
		
		var objAssetsManagerWrapper = jQuery("#uc_assets_manager");
		if(objAssetsManagerWrapper.length == 0)
			throw new Error("Assets manager not found");
		
		g_objAssetsManager.init(objAssetsManagerWrapper);
	};

	
	
	
};

