
function UniteCreatorElementorEditorAdmin(){
	
	var t = this;
	var g_arrPreviews;
	var g_handle = null;
	var g_objSettingsPanel;
	var g_objAddonParams, g_objAddonParamsItems, g_lastAddonName;
	var g_numRepeaterItems = 0;
	var g_windowFront, g_searchDataID, g_searchData, g_frontAPI, g_objBody; 
	var g_temp = {
		startTime:0
	};
	
	
	/**
	 * raw url decode
	 */
	function rawurldecode(str){return decodeURIComponent(str+'');}
	

	/**
	 * utf8 decode
	 */
	function utf8_decode(str_data){var tmp_arr=[],i=0,ac=0,c1=0,c2=0,c3=0;str_data+='';while(i<str_data.length){c1=str_data.charCodeAt(i);if(c1<128){tmp_arr[ac++]=String.fromCharCode(c1);i++;}else if(c1>191&&c1<224){c2=str_data.charCodeAt(i+1);tmp_arr[ac++]=String.fromCharCode(((c1&31)<<6)|(c2&63));i+=2;}else{c2=str_data.charCodeAt(i+1);c3=str_data.charCodeAt(i+2);tmp_arr[ac++]=String.fromCharCode(((c1&15)<<12)|((c2&63)<<6)|(c3&63));i+=3;}}
	return tmp_arr.join('');}
		
	/**
	 * base 64 decode
	 */
	function base64_decode(data){var b64="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";var o1,o2,o3,h1,h2,h3,h4,bits,i=0,ac=0,dec="",tmp_arr=[];if(!data){return data;}
	data+='';do{h1=b64.indexOf(data.charAt(i++));h2=b64.indexOf(data.charAt(i++));h3=b64.indexOf(data.charAt(i++));h4=b64.indexOf(data.charAt(i++));bits=h1<<18|h2<<12|h3<<6|h4;o1=bits>>16&0xff;o2=bits>>8&0xff;o3=bits&0xff;if(h3==64){tmp_arr[ac++]=String.fromCharCode(o1);}else if(h4==64){tmp_arr[ac++]=String.fromCharCode(o1,o2);}else{tmp_arr[ac++]=String.fromCharCode(o1,o2,o3);}}while(i<data.length);dec=tmp_arr.join('');dec=utf8_decode(dec);return dec;}
	
	
	/**
	 * trace function
	 */
	function trace(str){
		console.log(str);
	}
				
	
	function a________AUDIO_CONTROL_________(){}
		
	
	/**
	 * select audio file from library
	 */
	function onChooseAudioClick(){
		
		var objButton = jQuery(this);
		var objInput = objButton.siblings("input[type='text']");
		var objText = objButton.siblings(".uc-audio-control-text");
		
		var frame = wp.media({
			title : "Select Audio File",
			multiple : false,
			library : { type : "audio"},
			button : { text : 'Choose' }
		});
		
		// Runs on select
		frame.on('select',function(){
			var objSettings = frame.state().get('selection').first().toJSON();
			var urlFile = objSettings.url;
			objInput.val(urlFile);
			objInput.trigger("input");
			
			//var text = 'Please copy this url to the input box \n '+urlFile;
			//alert(text);
		});
		
		//open media library
		frame.open();
	}
	
	/**
	 * get elementor panel
	 */
	function getObjElementorPanel(){
		var objPanel = jQuery("#elementor-panel");
		
		return(objPanel);
	}
	
	/**
	 * init audio control
	 */
	function initAudioControl(){
		var objPanel = getObjElementorPanel();
		
		objPanel.on("click",".uc-button-choose-audio",	onChooseAudioClick);
		
	}
	
	
	/**
	 * get object property
	 */
	function getVal(obj, name, defaultValue){
		
		if(!defaultValue)
			var defaultValue = "";
		
		var val = "";
		
		if(!obj || typeof obj != "object")
			val = defaultValue;
		else if(obj.hasOwnProperty(name) == false){
			val = defaultValue;
		}else{
			val = obj[name];			
		}
		
		
		return(val);
	}
	
	/**
	 * escape html, turn html to a string
	 */
	function htmlspecialchars(string){
		
		if(!string)
			return(string);
		
		  return string
		      .replace(/&/g, "&amp;")
		      .replace(/</g, "&lt;")
		      .replace(/>/g, "&gt;")
		      .replace(/"/g, "&quot;")
		      .replace(/'/g, "&#039;");
	};
	
	

	function a________POST_TYPE_SELECT_________(){}
	
	/**
	 * change taxonomy post select
	 */
	function changePostTaxonomySelect(selectPostType, dataPostTypes){
		
		var objPanel = getObjElementorPanel();
		var prefix = selectPostType.data("settingprefix");
		
		var settingKey = prefix+"_taxonomy";
		
		var selectPostTaxonomy = objPanel.find("select[data-setting='"+settingKey+"']");
		
		selectPostTaxonomy.attr("multiple", true);
		selectPostTaxonomy.css("height", "50px");
		
		//remove the tip
		var objInputWrapper = selectPostTaxonomy.parents(".elementor-control-input-wrapper");
		objInputWrapper.addClass("uc-notip");
		
		
		var postType = selectPostType.val();
		var selectedTax = selectPostTaxonomy.val();
		
		//fix the value that could be array
		var objSettings = getLastOpenedWidgetSettings();
		var realValue = getVal(objSettings, settingKey);
		
		if(typeof realValue == "object"){
			selectedTax = realValue;
			selectPostTaxonomy.val(realValue);
		}
		
		var objTax = getVal(dataPostTypes, postType);
		if(!objTax)
			return(true);
		
		//hide not relevant select options
		var objOptions = selectPostTaxonomy.find("option");
		var firstVisibleOption = null;
				
		var numVisible = 0;
		jQuery.each(objOptions, function(index, option){
			
			var objOption = jQuery(option);
			var optionTax = objOption.prop("value");
			
			var taxFound = objTax.hasOwnProperty(optionTax);
			
			if(taxFound == true && firstVisibleOption == null)
				firstVisibleOption = optionTax;
			
			if(taxFound == true){
				objOption.show();
				numVisible++;
			}
			else
				objOption.hide();
							
		});
		
		if(numVisible > 4)
			selectPostTaxonomy.css("height", "80px");
		
		//check and change current tax
		
		if(typeof selectedTax != "string" && selectedTax.length)
			selectedTax = selectedTax[0];
		
		var isCurrentTaxRelevant = objTax.hasOwnProperty(selectedTax);
		
		if(isCurrentTaxRelevant == false && firstVisibleOption){
			
			selectPostTaxonomy.val(firstVisibleOption).trigger("change");
		}
		
		
	}
	
	
	/**
	 * fill category select
	 */
	function onPostTypeSelectChange_fillCategorySelect(objSelectPostCategory, selectPostType, dataPostTypes, placeholder, isInit){
		
		var arrPostTypes = selectPostType.val();
		
		//force array always
		if(jQuery.isArray(arrPostTypes) == false){
			arrPostTypes = [arrPostTypes];
		}
		
		var selectedCatID = objSelectPostCategory.select2("val");
		
		//try to set init vals from the data if available
		if(isInit == true){
			
			var widgetSettings = getLastOpenedWidgetSettings();
			var settingName = objSelectPostCategory.data("setting");
			
			var initValues = getVal(widgetSettings, settingName);
			
			if(initValues && Array.isArray(initValues) && initValues.length != selectedCatID.length){
				selectedCatID = initValues;
			}
			
		}
				
		var options = [];
		
		for(var index in arrPostTypes){
			
			var postType = arrPostTypes[index];
			
			var objPostType = getVal(dataPostTypes, postType);
			
			if(!objPostType)
				continue;
			
			var objCats = objPostType["cats"];
			
			jQuery.each(objCats, function(catID, catText){
				
				var catShowText = htmlspecialchars(catText);
				
			    options.push({
		            text: catShowText,
		            id: catID
		        });
			    
			});
						
		}
		
		objSelectPostCategory.empty().select2({
			data:options,
			placeholder:placeholder
		});
		
		if(jQuery.isEmptyObject(selectedCatID) == false){
			
			objSelectPostCategory.val(selectedCatID);
			
			//var newSelectedCatID = objSelectPostCategory.select2("val");
			
			objSelectPostCategory.trigger("change");
			
		}
		
	}
	
	/**
	 * on post type select change
	 */
	function onPostTypeSelectChange(event, paramSelect){
		
		var isInit = false;
		
		if(paramSelect && event == null){
			var selectPostType = paramSelect;
			isInit = true;
		}
		else
			var selectPostType = jQuery(this);
		
		var dataPostTypes = selectPostType.data("arrposttypes");
		if(typeof dataPostTypes == "string"){
			dataPostTypes = t.decodeContent(dataPostTypes);
			dataPostTypes = JSON.parse(dataPostTypes);
		}
		
		var settingType = selectPostType.data("settingtype");
		
		if(settingType == "select_post_taxonomy"){
			
			changePostTaxonomySelect(selectPostType, dataPostTypes);
			return(false);
		}
		
		//find post category select
		var objPanel = getObjElementorPanel();
		var prefix = selectPostType.data("settingprefix");
		
		var objSelectPostCategory = objPanel.find("select[data-setting='"+prefix+"_category']");
		var objSelectExcludeTerms = objPanel.find("select[data-setting='"+prefix+"_exclude_terms']");
		
		onPostTypeSelectChange_fillCategorySelect(objSelectPostCategory, selectPostType, dataPostTypes, "All Terms", isInit);
		
		onPostTypeSelectChange_fillCategorySelect(objSelectExcludeTerms, selectPostType, dataPostTypes, "Select Terms To Exclude", isInit);
		
	}
	
	
	/**
	 * init post type select control
	 */
	function initPostTypeSelectControl(){
		
		var objPanel = getObjElementorPanel();
		
		objPanel.on("change",".unite-setting-post-type", onPostTypeSelectChange);
		
	}
	
	/**
	 * on panel change - refresh the post type selector again
	 */
	function postSelectOnLoad(){
		
		var objPanel = getObjElementorPanel();
				
		var objSetting = jQuery(".unite-setting-post-type");
		if(objSetting.length == 0)
			return(true);
		
		var isInited = objSetting.data("isinited");
		if(isInited == true)
			return(true);
		
		objSetting.data("isinited", true);
		
		setTimeout(function(){
			
			onPostTypeSelectChange(null, objSetting);
			
		}, 500);
		
	}
	
	
	
	function a________SPECIAL_SELECTS_________(){}
		
	
	/**
	 * decode some content
	 */
	this.decodeContent = function(value){
		
		return rawurldecode(base64_decode(value));
	}
	
		
		
	
	/**
	 * get select 2 ajax options
	 */
	function getSelect2AjaxOptions(action, postType, taxonomySettingName,objSelect){
		
		var optionsAjax = {};
		optionsAjax["url"] = ajaxurl;
		optionsAjax["dataType"] = "json";
		optionsAjax["cache"] = true;
		
		optionsAjax["data"] = function(params){
			
			params["q"] = getVal(params,"term");
			
			if(postType)
				params["post_type"] = postType;
			
			//add taxonomy dynamic
			if(taxonomySettingName){
				
				var isAllTax = objSelect.data("isalltax");
								
				var objSettingTaxonomy = getElementorControlByName(taxonomySettingName);
				
				if(objSettingTaxonomy){
					
					//get all
					if(isAllTax === true){
						var arrAllTax = objSettingTaxonomy.children("option:visible").map(function(){
						    return jQuery(this).val();
						 }).get();
						
						params["taxonomy"] = arrAllTax;
					}else								//get single
						params["taxonomy"] = objSettingTaxonomy.val();
				}
				
			}
			
			var objData = {
				action: "unlimitedelements_ajax_action",
				nonce: g_ucNonce,
				client_action: action,
				data:params
			}
						
			return(objData);
		};
		
		var options = {
				ajax:optionsAjax,
				minimumInputLength:1,
				allowClear:true,
				dir:"ltr"
		};
		
		return(options);
	}
	
	/**
	 * get elementor control by name
	 */
	function getElementorControlByName(controlName){
		
		if(!controlName)
			return(null);
		
		var objWrapper = jQuery("#elementor-controls");
		if(objWrapper.length == 0)
			return(null);
		
		var selector = "*[data-setting=\""+controlName+"\"]";
		
		var objControl = objWrapper.find(selector);
		
		if(objControl.length == 0)
			return(null);
		
		return(objControl);
	}
	
	/**
	 * add edit object if available
	 */
	function postIDsSelect_checkEditButton(objSelect, data, arrData){
		
		var value = objSelect.val();
				
		var type = objSelect.data("datatype");
		
		if(type == "terms" || type == "users")
			return(false);
		
		var objWrapper = objSelect.parents(".elementor-control-input-wrapper");
		
		var objButtonEdit = objWrapper.find(".uc-button-edit-wrapper");
		if(objButtonEdit.length)
			objButtonEdit.remove();
				
		if(!value || value == "")
			return(false);
		
		if(jQuery.isArray(value))
			return(false);
			
		if(jQuery.isNumeric(value) == false)
			return(false);
		
		if(typeof g_ucAdminUrl == "undefined")
			return(false);
		
		//append the new button
		
		switch(type){
			case "post":
			default:
				var buttonText = "Edit Post";
				var urlEdit = g_ucAdminUrl+"post.php?post="+value+"&action=edit";
			break;
			case "elementor_template":
				var buttonText = "Edit Template";
				var urlEdit = g_ucAdminUrl+"post.php?post="+value+"&action=elementor";
			break;
		}
				
		var htmlButton = "<div class='uc-button-edit-wrapper'><a href='"+urlEdit+"' target='_blank'>"+buttonText+"</a></div>";
		
		objWrapper.append(htmlButton);
		
		
	}
	
	
	/**
	 * init the select 2 object eventually
	 */
	function initPostIDsSelect_initObject(objSelect, arrInitData){
				
		var data = objSelect.data();
		
		var postType = null;
		var taxonomy = null;
		
		var isWoo = objSelect.data("woo");
		if(isWoo == "yes")
			postType = "product";
		
		var type = objSelect.data("datatype");
		if(type == "elementor_template")
			postType = "elementor_template";
		
		
		//get terms
		var action = "get_posts_list_forselect";
		
		if(type == "terms"){
			action = "get_terms_list_forselect";
		}
		
		if(type == "users"){
			action = "get_users_list_forselect";
			
		}
		
		var taxonomyName = objSelect.data("taxonomyname");
		
		var options = getSelect2AjaxOptions(action, postType, taxonomyName, objSelect);
		
		var placeholder = objSelect.data("placeholdertext");
		if(placeholder){
			placeholder = placeholder.replace("--"," ");
			options["placeholder"] = placeholder;
		}
		
		if(arrInitData){
			options["data"] = arrInitData;
		}
		
		objSelect.select2(options);
		
		objSelect.select2Sortable();
		
		if(!arrInitData){
			
			objSelect.on("change",function(event){
				postIDsSelect_checkEditButton(objSelect);
			});
			
			return(false);
		}
		
		//avoid trigger event on init
		objSelect.data("stop_trigger_oninit", true);
		objSelect.on("change",function(event){
			
			postIDsSelect_checkEditButton(objSelect);
			
			var stopOnInit = objSelect.data("stop_trigger_oninit");
			if(!stopOnInit)
				return(true);
			
			event.stopPropagation();
			event.stopImmediatePropagation();
			objSelect.data("stop_trigger_oninit", false);
		});
		
		
		//make init values
		var arrInitIDs = [];
		for(var key in arrInitData){
			var item = arrInitData[key];
			arrInitIDs.push(item.id);
		}
		
		objSelect.val(arrInitIDs).trigger("change");
		
	}
	
	/**
	 * init post id's selector
	 */
	function initPostIDsSelect(objSelect){
				
		var widgetSettings = getLastOpenedWidgetSettings();
		
		var settingName = objSelect.data("setting");
		var isSingle = objSelect.data("issingle");
		var dataType = objSelect.data("datatype");
		
		if(isSingle === true)
			objSelect.removeAttr("multiple");
		
		var initValue = getVal(widgetSettings, settingName);
		
		//treat single number
		if(jQuery.isArray(initValue) == false && jQuery.isNumeric(initValue))
			initValue = [initValue];
		
		if(jQuery.isEmptyObject(initValue)){
			initPostIDsSelect_initObject(objSelect);
			return(false);
		}
		
		if( jQuery.isArray(initValue) == false && dataType != "terms" ){
			
			initPostIDsSelect_initObject(objSelect);
			return(false);
		}
				
		//get titles by ajax
		objSelect.hide();
		
		var loaderText = objSelect.data("loadertext");
		loaderText = t.decodeContent(loaderText);
		
		//append loader
		var objParent = objSelect.parent();
		objParent.append("<span class='uc-panel-ajax-loader'>"+loaderText+"</span>");
		
		var objLoader = objParent.find(".uc-panel-ajax-loader");
		
		var ajaxData = {
				post_ids: initValue
		};
		
		var action = "get_select2_post_titles";
		
		if(dataType == "terms")
			action = "get_select2_terms_titles";
		
		if(dataType == "users")
			action = "get_select2_users_titles";
		
		ajaxRequest(action, ajaxData, function(response){
			
			objLoader.remove();
			
			var arrSelectData = getVal(response, "select2_data");
			
			initPostIDsSelect_initObject(objSelect, arrSelectData);
						
		});
		
	}
	
	/**
	 * set the template button
	 */
	function templateButtonSet(objButton, objSelectTemplate){
		
		var value = objSelectTemplate.val();
		
		if(value == "" || value == "__none__"){
			objButton.hide();
			return(false);
		}
		
		if(!g_ucAdminUrl)
			return(false);
		
		objButton.show();
		
		value = value.replace("jet_","");
		
		var urlEdit = g_ucAdminUrl+"post.php?post="+value+"&action=elementor";
		
		objButton.attr("href", urlEdit);
		
	}
	
	
	/**
	 * init template button
	 */
	function initTemplateButton(objButton){
		
		var selectID = objButton.data("selectid");
		
		var objPanel = getObjElementorPanel();
		
		var objSelectTemplate = objPanel.find("select[data-setting='"+selectID+"']");
		
		if(objSelectTemplate.length == 0)
			return(false);
		
		templateButtonSet(objButton, objSelectTemplate);
		
		objSelectTemplate.on("change", function(){
			templateButtonSet(objButton, objSelectTemplate);
		});
		
	}

	
	/**
	 * init the addons selector
	 */
	function initSpecialSelects(){
		
		var objSelects = g_objSettingsPanel.find(".unite-setting-special-select");
		if(objSelects.length == 0)
			return(false);
		
		jQuery.each(objSelects, function(index, select){
			var objSelect = jQuery(select);
			
			var isInited = objSelect.data("isinited");
			if(isInited === true)
				return(true);
			
			objSelect.data("isinited", true);
			
			var settingType = objSelect.data("settingtype");
			
			switch(settingType){
				case "post_ids":
					initPostIDsSelect(objSelect);
				break;
				case "template_button":
					initTemplateButton(objSelect);
				break;
			}
			
		});
				
	}
	
	
	
	/**
	 * occure on change of settings panel
	 */
	function onSettingsPanelInit(){
		
		initSpecialSelects();
		
		//init the post type selector if exists
		postSelectOnLoad();
		
		g_objBody.trigger("uc_settings_panel_change");
		
	}
	
	
	/**
	 * init all the events
	 */
	function initEvents(){
		
		g_objSettingsPanel.bind("DOMSubtreeModified",function(){
			  if(g_handle)
				  clearTimeout(g_handle);
			  
			  g_handle = setTimeout(onSettingsPanelInit, 50);
			  
		});
				
	}
	
	function a________LOAD_INCLUDES_________(){}
	
	
	/**
	 * get object property
	 */
	function getVal(obj, name, defaultValue, opt){
		
		if(!defaultValue)
			var defaultValue = "";
		
		var val = "";
		
		if(!obj || typeof obj != "object")
			val = defaultValue;
		else if(obj.hasOwnProperty(name) == false){
			val = defaultValue;
		}else{
			val = obj[name];			
		}
		
		
		return(val);
	}
	
	
	/**
	 * load include file, js or css
	 */
	function loadDOMIncludeFile(type, url, data){
				
		if(!url)
			return(false);
		
		//additional input values
		var replaceID = getVal(data, "replaceID");
		var name = getVal(data, "name");
		var onload = getVal(data, "onload");
		var iframeWindow = getVal(data, "iframe");
		var isModule = getVal(data, "ismodule");
		
		//add random number at the end
		var noRand = getVal(data, "norand");
		if(!noRand){
			var rand = Math.floor((Math.random()*100000)+1);
			
			if(url.indexOf("?") == -1)
				url += "?rand="+rand;
			else
				url += "&rand="+rand;
		}
		
		if(replaceID)
			jQuery("#"+replaceID).remove();
		
		var objWindow = window;
		if(iframeWindow)
			objWindow = iframeWindow;
		
		switch(type){
			case "js":
				var tag = objWindow.document.createElement('script');
				tag.src = url;
				
				if(isModule === true)
					tag.type = "module";
								
				//add onload function if exists
				if(typeof onload == "function"){
					
					tag.onload = function(){
						onload(jQuery(this), replaceID);
					};
					
				}
				
				var firstScriptTag = objWindow.document.getElementsByTagName('script')[0];
				firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
				tag = jQuery(tag);
				
				if(name)
					tag.attr("name", name);
				
			break;
			case "css":
								
				var objHead = jQuery(objWindow.document).find("head");
				
				objHead.append("<link>");
				var tag = objHead.children(":last");
				var attributes = {
					      rel:  "stylesheet",
					      type: "text/css",
					      href: url
				};
				
				if(name)
					attributes.name = name;
				
				//add onload function if exists
				if(typeof onload == "function"){
					
					attributes.onload = function(){
						
						onload(jQuery(this), replaceID);
					};
					
				}
				
				tag.attr(attributes);
			break;
			default:
				throw Error("Undefined include type: "+type);
			break;
		}
		
			
		//replace current element
		if(replaceID)
			tag.attr({id:replaceID});
		
		return(tag);
	};
	
	
	/**
	 * put addon includes
	 */
	function putIncludes(windowIframe, objIncludes, funcOnLoaded){
		
		var isLoadOneByOne = true;
		
		var handlePrefix = "uc_include_";
		
		//make a list of js handles
		var arrHandles = {};
		jQuery.each(objIncludes, function(event, objInclude){
			
			var handle = handlePrefix + objInclude.type + "_" + objInclude.handle;
			
			if( !(objInclude.type == "js" && objInclude.handle == "jquery") )
				arrHandles[handle] = objInclude;
		});
		
		var isAllFilesLoaded = false;
		
		//inner function that check that all files loaded by handle
		function checkAllFilesLoaded(){
			
			if(isAllFilesLoaded == true)
				return(false);
			
			if(!jQuery.isEmptyObject(arrHandles))
				return(false);
			
			isAllFilesLoaded = true;
			
			if(!funcOnLoaded)
				return(false);
			
			funcOnLoaded();
			
		}
		
		
		/**
		 * on js file loaded - load first js file, from available handles
		 * in case that loading one by one
		 */
		function onJsFileLoaded(){
			
			for(var index in arrHandles){
				var objInclude = arrHandles[index];
				
				if(objInclude.type == "js"){
					loadIncludeFile(objInclude);
					return(false);
				}
				
			}
			
		}
		
		
		/**
		 * load include file
		 */
		function loadIncludeFile(objInclude){
						
			var url = objInclude.url;
			var handle = handlePrefix + objInclude.type + "_" + objInclude.handle;
			var type = objInclude.type;
			var isModule = false;
			if(objInclude.hasOwnProperty("is_module") && objInclude.is_module == true && type == "js")
				isModule = true;
			
			
			//skip jquery for now
			if(objInclude.handle == "jquery"){
				
				checkAllFilesLoaded();
				
				if(isLoadOneByOne)
					onJsFileLoaded();
				
				return(true);
			}
			
			var data = {
					replaceID:handle,
					name: "uc_include_file",
					iframe:windowIframe,
					ismodule:isModule
			};
						
			//onload throw event when all scripts loaded
			data.onload = function(obj, handle){
								
				var objDomInclude = jQuery(obj);
						
				objDomInclude.data("isloaded", true);
								
				//delete the handle from the list, and check for all files loaded
				if(arrHandles.hasOwnProperty(handle) == true){
										
					delete arrHandles[handle];
					
					checkAllFilesLoaded();
					
				}//end checking
				
				if(isLoadOneByOne){
					var tagName = objDomInclude.prop("tagName").toLowerCase();
					if(tagName == "script")
						onJsFileLoaded();
				}
				
			};
			
			
			//if file not included - include it
			var objDomInclude = jQuery("#"+handle);
			
			if(objDomInclude.length == 0){
				
				objDomInclude = loadDOMIncludeFile(type, url, data);
			}
			else{
				
				//if the files is in the loading list but still not loaded, 
				//wait until they will be loaded and then check for firing the finish event (addons with same files)
				
				//check if the file is loaded
				var isLoaded = objDomInclude.data("isloaded");
				if(isLoaded == true){
					
					//if it's already included - remove from handle
					if(arrHandles.hasOwnProperty(handle) == true)
						delete arrHandles[handle];
					
					if(isLoadOneByOne){
						var tagName = objDomInclude.prop("tagName").toLowerCase();
						if(tagName == "script")
							onJsFileLoaded();
					}
					
					
				}else{
					
					var timeoutHandle = setInterval(function(){
						var isLoaded = objDomInclude.data("isloaded");
						
						if(isLoaded == true){
							clearInterval(timeoutHandle);
							
							if(arrHandles.hasOwnProperty(handle) == true)
								delete arrHandles[handle];
							
							checkAllFilesLoaded();
							
							if(isLoadOneByOne){
								var tagName = objDomInclude.prop("tagName").toLowerCase();
								if(tagName == "script")
									onJsFileLoaded();
							}
							
						}
						
					},100);
										
				}
								
			}			
			
		}
		
		if(isLoadOneByOne == false){
			
			jQuery.each(objIncludes, function(event, objInclude){
				loadIncludeFile(objInclude);
			});
			
		}else{
			
			//load css files and first js files
			var isFirstJS = true;
			
			jQuery.each(objIncludes, function(event, objInclude){
				if(objInclude.type == "css")
					loadIncludeFile(objInclude);
				else{		//js file, load first only
					
					if(isFirstJS == true){
						loadIncludeFile(objInclude);
						isFirstJS = false;
					}
					
				}
			});
			
			
		}
		
		//check if all files loaded
		checkAllFilesLoaded();
	}
	
	
	/**
	 * load js includes and then run function
	 */
	this.ucLoadJSAndRun = function(iframeWindow, jsonIncludes, funcRun){
		
		var objIncludes = jQuery.parseJSON(jsonIncludes);
		if(!objIncludes || objIncludes.length == 0){
			funcRun();
			return(false);
		}
		
		putIncludes(iframeWindow, objIncludes, function(){
			funcRun();
		});
		
	}
	
	
	function ____________BACKGROUNDS______________(){}
	
	/**
	 * search elementor data
	 */
	function searchElementorData(data, id){
		
		//get from last opened object
		if(id && id == window.ucLastElementorModelID){
			var objSettings = getVal(window.ucLastElementorModel, "settings");
			var objSettingsAttributes = getVal(objSettings, "attributes");
			
			return(objSettingsAttributes);
		}
		
		//if not last opened - search in data
		
		if(id){		//init values
			g_searchDataID = id;
			g_searchData = null;
		}
		
		if(!g_searchDataID)
			return(false);
		
		if(!data)
			return(false);
		
		var models = getVal(data, "models");
		
		if(models && jQuery.isArray(models)){
			
			searchElementorData(models);
		}
				
		jQuery.each(data, function(index, item){
			
			var attributes = getVal(item, "attributes");
			
			var elType = getVal(attributes, "elType");
			var elID = getVal(attributes, "id");
			var elements = getVal(attributes, "elements");
						
			//if found
			if(g_searchDataID == elID){
								
				var objSettings = getVal(attributes, "settings");
				
				g_searchData = getVal(objSettings, "attributes");
								
				return(false);
			}
						
			if(elType != "widget" && typeof elements == "object" && elements.length > 0){
				searchElementorData(elements);
				return(true);
			}
			
		});
				
		var settingsOutput = {};
		
		if(g_searchData && jQuery.isArray(g_searchData) == false)
			settingsOutput = jQuery.extend({}, g_searchData);
		
		
		return(settingsOutput);
	}
	
	/**
	 * get settings from elementor
	 */
	function getSettingsFromElementor(id){
		
		var objSettings = getVal(window.ucLastElementorModel, "settings");

		var cid = getVal(objSettings, "cid");
		var attributes = getVal(objSettings, "attributes");
		
		if(cid && attributes)
			return(attributes);
		
		if(typeof elementor == "undefined")
			return(null);
				
		var elements = getVal(elementor, "elements");
		
		if(!elements)
			return(null);

		var objSettings = searchElementorData(elements, id);
		
		if(!objSettings)
			return(null);
		
		//if objSettings is model object, return the attributes
		
		var cid = getVal(objSettings, "cid");
		var attributes = getVal(objSettings, "attributes");
				
		if(!cid && !attributes)
			return(objSettings);
		
		
		return(attributes);
	}
	
	
	/**
	 * ajax request to unlimited plugin from the editor
	 */
	function ajaxRequest(action, data, funcSuccess, funcError){
		
		if(!data)
			var data = {};
		
		var objData = {};
		
		objData.action = "unlimitedelements_ajax_action";
		objData.nonce = g_ucNonce;
		objData.client_action = action;
		objData.data = data;		
		
		var ajaxOptions = {
				type:"post",
				url:ajaxurl,
				dataType: 'json',
				data:objData,
				success:function(response){
					if(typeof funcSuccess == "function")
						funcSuccess(response);
					else
						trace(response);
				},
				error:function(jqXHR, textStatus, errorThrown){
					
					trace("unlimited ajax error");
					
					switch(textStatus){
						case "parsererror":
						case "error":
							trace(jqXHR.responseText);
							//showDebug(jqXHR.responseText);
						break;
					}
					
					if(typeof funcError == "function")
						funcError(textStatus);
				}
		}
				
		jQuery.ajax(ajaxOptions);
		
	}
	
	
	/**
	 * get repeater data
	 */
	function getRepeaterData(objData){
		
		var models = getVal(objData, "models");
		
		if(!models || jQuery.isEmptyObject(models))
			return([]);
		
		var settings = [];
		
		jQuery.each(models, function(index, model){
			
			var attributes = getVal(model, "attributes");
			
			settings.push(attributes);
			
		});
				
		return(settings);
	}
	
	
	/**
	 * get relevant settings data from all settings
	 */
	function getBGSettingsData(type, objSettings){
		
		if(!objSettings)
			return({});
		
		var objSettingsData = {};
		
		for(key in objSettings){
			
			if(key.indexOf(type) === -1)
				continue;
			
			var shortKey = key.replace(type+"_", "");
			
			var objData = objSettings[key];
			
			if(typeof objData == "object" && objData.hasOwnProperty("model") && objData.hasOwnProperty("_byId")){
				objData = getRepeaterData(objData);
			}
			
			objSettingsData[shortKey] = objData;
		}
		
		
		return(objSettingsData);
	}
	
	
	/**
	 * load background widget for output
	 */
	function loadBGWidget(bgType, objSettings, funResponse){
		
		var settingsData = getBGSettingsData(bgType, objSettings);
		
		var data = {
			addontype: "bg_addon",
			name: bgType,
			elementor_settings: settingsData
		};
		
		ajaxRequest("get_addon_output_data", data, function(response){
			funResponse(response, objSettings);
		});
		
	}
	
	
	/**
	 * apply background to section
	 */
	function applyBackgroundToElement(objElement, response, settingsData){
		
		var objIframeWindow = jQuery("#elementor-preview-iframe");
		
		if(objIframeWindow.length == 0)
			return(false);
		
		var frameWindow = objIframeWindow[0];
						
		var arrIncludes = getVal(response, "includes");
		
		var location = getVal(settingsData, "uc_background_location");
		
		var classFront = "uc-bg-front";
		
		putIncludes(frameWindow.contentWindow, arrIncludes, function(){
			
			var contentHTML = getVal(response, "html");
			
			var objBackgroundOverlay = objElement.children(".unlimited-elements-background-overlay");
			
			//replace
			if(objBackgroundOverlay.length == 1){
				
				objBackgroundOverlay.html(contentHTML);
				
				if(location == "front")
					objBackgroundOverlay.addClass(classFront);
				else
					objBackgroundOverlay.removeClass(classFront);
				
			}else{
				//add
				
				var addClass = "";
				if(location == "front")
					addClass = " "+classFront;
				
				var html = "<div class='unlimited-elements-background-overlay"+addClass+"'>";
				
				html += contentHTML;
				
				html += "</div>";
				
				objElement.prepend(html);
			}
			
			var objVideoContainer = objElement.children(".elementor-background-video-container");
			if(objVideoContainer.length == 1){
				var objBackgroundOverlay = objElement.children(".unlimited-elements-background-overlay");
				
				objBackgroundOverlay.insertAfter(objVideoContainer);
			}
				
			
		});
		
	}
	
	
	/**
	 * check and load element background if needed
	 */
	function checkElementBackground(element, objSettings){
		
		var backgroundType = getVal(objSettings, "uc_background_type");
				
		if(!backgroundType || backgroundType == "__none__"){
						
			//remove bg if exists
			var objBackgroundOverlay = element.children(".unlimited-elements-background-overlay");
			if(objBackgroundOverlay.length)
				objBackgroundOverlay.remove();
			
			return(false);
		}
				
		loadBGWidget(backgroundType, objSettings, function(response, settingsData){
			applyBackgroundToElement(element, response, settingsData);
		});
		
	}
	
	
	/**
	 * on front end element ready
	 * check section backgrounds
	 */
	function onFrontElementReady(element){
				
		var objElement = jQuery(element);
		
		var type = objElement.data("element_type");
		
		switch(type){
			case "section":
			case "container":
			break;
			default:
				return(true);
			break;
		}
		
				
		var id = objElement.data("id");
		
		//validate it's opened element in panel
		
		if(window.ucLastElementorModelID && id != window.ucLastElementorModelID)
			return(true);
		
		var objSettings = getSettingsFromElementor(id);
		
		checkElementBackground(element, objSettings);
	}
	
	
	/**
	 * get last widget settings
	 */
	function getLastOpenedWidgetSettings(){
		
		if(!window.ucLastElementorModelID)
			return(null);
		
		var settings = getSettingsFromElementor(window.ucLastElementorModelID);
		
		return(settings);
	}
	
	
	/**
	 * on elementor panel change
	 * save current attributes
	 */
	function onElementorSectionPanelChange(event, model){
		
		window.ucLastElementorModelID = model.id;
		window.ucLastElementorModel = model.attributes;
		
	}
	
	/**
	 * init backgrounds
	 */
	function initBackgrounds(){
		
		elementor.hooks.addAction("panel/open_editor/section", onElementorSectionPanelChange);
		elementor.hooks.addAction("panel/open_editor/container", onElementorSectionPanelChange);
		elementor.hooks.addAction("panel/open_editor/widget", onElementorSectionPanelChange);
		
		if(typeof elementorFrontend != "undefined"){
			
			//elementorFrontend.hooks.addAction( 'frontend/element_ready/widget', onFrontWidgetReady); 
			
			elementorFrontend.hooks.addAction( 'frontend/element_ready/section', onFrontElementReady); 
			elementorFrontend.hooks.addAction( 'frontend/element_ready/container', onFrontElementReady); 
		}
	}

	/**
	 * check and remove panel loader, if enought time passed
	 */
	function checkRemoveLoader(){
		
		var hasLoadingClass = jQuery("body").hasClass("elementor-panel-loading");
		
		if(hasLoadingClass == false)
			return(false);
		
		var timePass = Date.now() - g_temp.startTime;
		
		if(timePass > 10000)
			jQuery("body").removeClass("elementor-panel-loading");
		
	}
	

	/**
	 * on open widget
	 * save last model
	 */
	function onElementorOpenWidget(event, model){
		
		//remove the loader if open widget
		checkRemoveLoader();
		
		window.ucLastElementorModelID = model.id;
		window.ucLastElementorModel = model.attributes;
		
		window.lastWidgetType = getVal(model.attributes, "widgetType");
		
		g_frontAPI.triggerEvent("open_widget_settings", window.ucLastElementorModel);
		
		
		
	}
	
	
	/**
	 * run ajax action
	 */
	this.runAjaxAction = function(action){
		
		var data = {
			widget_name: window.lastWidgetType
		};
		
		var ajaxAction;
		switch(action){
			case "reinstall_widget":
				ajaxAction = "update_addon_from_catalog";
				var widgetTitle = jQuery("#elementor-panel-header-title").text();
				
				widgetTitle = widgetTitle.replace("Edit ","");
				
				if(confirm("Do you really want to reinstall \""+widgetTitle+"\" widget?") == false)
					return(false);
				
			break;
		}
		
		if(!ajaxAction){
			alert("no action");
			return(false);
		}
			
		ajaxRequest(ajaxAction, data, function(response){
			
			switch(action){
				case "reinstall_widget":
					alert("widget updated, please refresh the page");
				break;
			}
			
		});
					
	}
	
	/**
	 * ajax request
	 */
	this.ajaxRequest = function(action, ajaxData, response){
		ajaxRequest(action, ajaxData, response);		
	}

	
	/**
	 * init front end interaction
	 * for section background etc
	 */
	this.initFrontEndInteraction = function(windowFront, elementorFrontend){
		
		if(typeof elementorFrontend == "undefined")
			return(false);
		
		//wait for full load of front end object
		if(typeof elementorFrontend.hooks == "undefined"){
			
			setTimeout(function(){
				
				t.initFrontEndInteraction(windowFront, elementorFrontend);
				
			},300);
						
			return(false);
		}
		
		g_frontAPI = new UniteCreatorElementorFrontAPI();
				
		g_windowFront = windowFront;
		
		g_windowFront.g_ueSettingsAPI = g_frontAPI;
		
		if(typeof g_ucHasBackgrounds !== "undefined" && g_ucHasBackgrounds === true)
			initBackgrounds();
		
		g_temp.startTime = Date.now();
		
		elementor.hooks.addAction("panel/open_editor/widget", onElementorOpenWidget);
		
		if(elementor.channels){
			elementor.channels.data.on("element:destroy",function(model){
				
				g_frontAPI.triggerEvent("after_delete_element", model.id);
				
			});
		}
		
		
	}


	function ____________INIT______________(){}


	/**
	 * init the object
	 */
	this.init = function(){
		
		g_objBody = jQuery("body");
		
		g_objSettingsPanel = jQuery("#elementor-panel");
		
		//initPreviewThumbs();
		
		initAudioControl();
		
		initPostTypeSelectControl();
		
		initEvents();
		
	}
	

}

/**
 * front api
 */
function UniteCreatorElementorFrontAPI(){
	
	var g_objAdmin;
	
	/**
	 * debug some string
	 */
	function trace(str){
		console.log(str);
	}
	
	/**
	 * trigger event
	 */
	this.triggerEvent = function(eventName, model, options){
		
		var data = {};
		data.model = model;
				
		jQuery(window).trigger("ue_event_"+eventName, data);
		
	}
	
	
	/**
	 * on event
	 */
	this.onEvent = function(eventName, func){
		
		jQuery(window).on("ue_event_"+eventName, func);
		
	}
	
	
	/**
	 * editor admin
	 */
	this.initAPI = function(objAdmin){
		
		g_objAdmin = objAdmin;
	}
	
}


var g_objUCElementorEditorAdmin = new UniteCreatorElementorEditorAdmin();

jQuery(document).ready(function(){
	g_objUCElementorEditorAdmin.init();
	
});

