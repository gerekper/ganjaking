"use strict";

function UniteCreatorHelper(){
	
	var t = this;
	
	if(!g_ucAdmin)
		var g_ucAdmin = new UniteAdminUC();
	
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
	 * get value function
	 */
	function getVal(data, name, defaultValue, opt){
		
		var value = g_ucAdmin.getVal(data, name, defaultValue, opt);
		
		return(value);
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
	 * put includes
	 */
	this.putIncludes = function(outputWindow, objIncludes, func){
				
		putIncludes(outputWindow, objIncludes, func);
		
	}
	
}