/**
* remote gallery api class
*/
function UERemoteGeneralAPI(){
	
	var t = this, g_objParent;
	var g_isTypeEvents = false;
	var g_options = null;
	
	
	var g_vars = {
		class_items:"",
		class_active:"",
		selector_item_trigger:"",
		add_set_active_code:false,
		listen_class_change:true,
		enableDebug:false,
		is_editor:false,
		trigger_event: "click"
	};
	
	var g_temp = {
			handle:null,
			trashold:50
	};
	
	/**
	* console log some string
	*/
	function trace(str){
		console.log(str);
	}
	
	/**
	 * get object property
	 */
	function getVal(obj, name, defaultValue){
		
		return window.ueRemoteConnection.getVal(obj, name, defaultValue);
	}
	
	
	/**
	 * run function with trashold
	 */
	function runWithTrashold(func, trashold){
		
		if(g_temp.handle)
			clearTimeout(g_temp.handle);
		
		g_temp.handle = setTimeout(func, g_temp.trashold);
		
	};
	
	
	/**
	 * validate inited
	 */
	function validateInited(){
		
		var isInited = g_objParent.data("remote_inited");
		
		if(isInited == false)
			throw new Error("The remote parent not inited");
		
	}
	
	/**
	 * get items objet
	 */
	function getObjItems(){
		
		var objItems = g_objParent.find("."+g_vars.class_items);
		
		return(objItems);
	}
	
	
	/**
	 * get total items
	 */
	function getNumTotal(){
		
		var objItems = g_objParent.find("."+g_vars.class_items);
		
		var numTotal = objItems.length;
		
		return(numTotal);
	}
	
	
	/**
	 * get current item
	 */
	function getObjCurrentItem(){
		
		var selector = "."+g_vars.class_items+"."+g_vars.class_active;
		
		var objCurrent = g_objParent.find(selector);
		
		return(objCurrent);
	}
	
	
	/**
	 * get current item
	 */
	function getNumCurrent(){
		
		var objCurrent = getObjCurrentItem();
		
		if(objCurrent.length == 0)
			return(0);
		
		var index = objCurrent.index();
		
		return(index);
	}
	
	/**
	 * get item by index
	 */
	function getItem(index){
		
		if(index < 0)
			index = 0;
		
		var objItems = getObjItems();
		
		if(objItems.length == 0)
			return(null);
		
		if(index >= objItems.length )
			index = objItems.length-1;
		
		var objItem = jQuery(objItems[index]);
		
		return(objItem);
	}
	
	
	
	/**
	 * get item number
	 */
	function getItemNum(num){
		
		var total = getNumTotal();
		
		if(jQuery.isNumeric(num) == false)
			throw new Error("num item should be numeric");
		
		if(num >= total )
			num = total-1;
		
		if(num < 0)
			num = 0;
		
		if(!num)
			num = 0;
		
		if(typeof num == "undefined")
			num = 0;
		
		return(num);
	}
	
	
	/**
	 * change item
	 */
	function changeItem(mixed){
		
		var numItem = getItemNum(mixed);
				
		var objItem = getItem(numItem);
		
		if(objItem == null)
			return(false);
		
		var numCurrent = getNumCurrent();
		
		if(numCurrent == numItem)
			return(false);
				
		if(!g_vars.selector_item_trigger){
			
			objItem.trigger(g_vars.trigger_event);
			return(false);
		}
		
		var objInner = objItem.find(g_vars.selector_item_trigger);
		
		if(objInner.length == 0){
			trace(objItem);
			throw new Error("Can't find inner by selector:"+g_vars.selector_item_trigger);
		}
		
		objInner.trigger(g_vars.trigger_event);
		
	}
	
	

	/*
		events
	*/	
	this.onEvent = function(name, func){
	
		  validateInited();
		  
	      switch(name){
			case "change":
				
				g_objParent.on("uc_change", func);
				
			break;
			case "pause":		// do nothing
			break;
			default:
				throw new Error("General API: Wrong event: "+name);
			break;
	
		}
	}
	
	
	/**
	* do some action
	*/
	this.doAction = function(action, arg1, arg2){
		
		validateInited();
		
		if(g_isTypeEvents == true){
			
			var funcRunAction = getVal(g_options, "func_doAction");
			
			if(!funcRunAction)
				throw new Error("Missing option: 'func_doAction' ");
			
			var response = g_options.func_doAction(action, arg1, arg2);
			
			return(response);
		}
		
		if(g_vars.enableDebug){
			trace("DEBUG - Action: " + action+" arg1: "+arg1+" arg2: "+arg2);
		}
		
		switch(action){
					
			case 'get_num_current':
				
				var current = getNumCurrent();
				
				if(g_vars.enableDebug)
					trace("response: "+current);
				
				return(current);
			break;
			case "get_total_items":
				
				var total = getNumTotal();
				
				if(g_vars.enableDebug)
					trace("response: "+total);
				
				return(total);
				
			break;
			case 'change_item':
				
				if(g_vars.enableDebug)
					console.trace();
				
				changeItem(arg1);
				
			break;
			case "pause":
			case "play":
			break;
			default:
				throw new Error("General API: Wrong action: "+action);
			break;
		}
		
	}
	
	/**
	 * listen to class change
	 */
	function initEvents_listenClassChange(){
		
		var objItems = getObjItems();
		
		if(g_vars.enableDebug == true){
			trace("generalAPI: listen class change: ");
			trace(objItems);
		}
		
		jQuery.each(objItems, function(index, item){
			
			var objItem = jQuery(item);
			
			var isSetObserver = objItem.data("uc_set_observer");
			if(isSetObserver === true){
				return(true);
			}
						
			var observer = new MutationObserver(function(records){
				
				runWithTrashold(function(){
					
					if(g_vars.enableDebug == true)
						trace("generalAPI: trigger item change");
					
					g_objParent.trigger("uc_change");
					
				});
								
			});
			
			var config = { attributes: true};
			
			observer.observe(item, config);
			
			objItem.data("uc_set_observer", true);
			
		});
		
		
	}
	
	
	/**
	 * add set active events
	 */
	function initEvents_setActive(){
		
		if(g_vars.enableDebug == true)
			trace("start initEvents_setActive")
		
		var objItems = getObjItems();
		
		if(objItems.length == 0){
			
			if(g_vars.enableDebug == true)
				trace("no items, exit")
			
			return(false);
		}
		
		//activate first item
		
		var objFirstItem = getItem(0);
		
		if(objFirstItem == null){
			
			if(g_vars.enableDebug == true)
				trace("empty first item - exit")
			
			return(false);
		}
		
		objFirstItem.addClass(g_vars.class_active);
		
		objItems.on(g_vars.trigger_event, function(event){
			
			var objItem = jQuery(this);
			
			//clicked element
			var objElement = jQuery(event.target);
			var isLink = objElement.is("a");
			
			//quit on link inside
			if(isLink == true && objElement.hasClass(g_vars.class_items) == false){
								
				return(true);
			}
			
			objItems.not(objItem).removeClass(g_vars.class_active);
			
			objItem.addClass(g_vars.class_active);
			
			g_objParent.trigger("uc_change");
			
			event.preventDefault();
			
		});
		
				
	}
	
	
	/**
	 * init obzerver events
	 */
	function initEvents(){
		
		if(g_vars.enableDebug == true){
			trace("generalAPI: init events");
			trace(g_objParent);
		}
		
		if(g_vars.listen_class_change == true)
			initEvents_listenClassChange();
		
		if(g_vars.add_set_active_code == true){
			
			initEvents_setActive();
			
		}
		
	}
	
	
	/**
	 * get element
	 */
	this.getElement = function(){
		
		return(g_objParent);
	}
	
	/**
	 * get api type
	 */
	this.getAPIType = function(){
		
		return("general");
	}
	
	
	/**
	 * init by classes
	 */
	function initByClasses(){
		
		try{
			
			var widgetName = g_objParent.data("widgetname");
			
			g_vars.class_items = getVal(g_options, "class_items");
			
			if(!g_vars.class_items)
				throw new Error(widgetName +" - missing 'class_items' option");
			
			g_vars.class_active = getVal(g_options, "class_active");
			
			if(!g_vars.class_active)
				throw new Error(widgetName +" - missing 'class_active' in options");
			
			g_vars.selector_item_trigger = getVal(g_options, "selector_item_trigger");
			
			g_vars.add_set_active_code = getVal(g_options, "add_set_active_code");
			
			if(g_vars.add_set_active_code === true)
				g_vars.listen_class_change = false;
			
		}
		catch(e){
			
			trace("ERROR: "+e);
			
			trace("passed options: ");
			trace(g_options);
			throw e;
		}
	}
	
	
	
	/**
	 * init the api
	 */
	this.init = function(objParent, options, isEditor){
				
		//not allow to init general api without options
		if(!options)
			return(false);
		
		g_vars.is_editor = isEditor;
		
		g_objParent = objParent;
		
		var connectType = getVal(options, "connect_type");
		if(connectType == "events")
			g_isTypeEvents = true;		//events allow to connect own functions
		
		g_options = options;
		
		var enableDebug = getVal(options, "trace_debug");
		
		if(enableDebug == true)
			g_vars.enableDebug = true;
		
		if(g_vars.enableDebug == true){
			trace("init general api");
			trace(objParent);
			trace(options);
		}
		
		if(isEditor == true)
			g_vars.trigger_event = "ucclick";
		
		if(g_isTypeEvents == false)
			initByClasses();
		
		g_objParent.data("remote_inited", true);
		
		if(g_isTypeEvents == false){
			
			if(g_vars.listen_class_change == true)
				setTimeout(initEvents, 1000);
			else
				initEvents();
			
			g_objParent.on("uc_ajax_refreshed", function(){
				
				setTimeout(initEvents,500)
				
			});
			
		}
				
		
		return(true);
	}
	
}



/**
* remote gallery api class
*/
function UERemoteGalleryAPI(){
	
	var g_api, g_isInited;
	var g_objParent;
	var t = this;
	
	/**
	* console log some string
	*/
	function trace(str){
		console.log(str);
	}
	
	/**
	* validate that the object is inited
	*/
	function validateInited(){
		
		if(g_isInited == false)
			throw new Error("The owl carousel API is not inited");
	}
	
	/**
		do some action
	*/
	this.doAction = function(action, arg1, arg2){
		
		validateInited();
		
		switch(action){
			case 'get_num_current':
				
				var current = g_api.getNumCurrent();
				
				return(current);
			break;
			case "get_total_items":
				
				var total = g_api.getNumItems();
				
				return(total);
			break;
			case 'change_item':
				
				g_api.selectItem(arg1);
				
			break;
			case "is_playing":
				
				var isPlaying = g_api.isPlaying();
				
				return(isPlaying);
				
			break;
			case "pause":
				
				g_api.stop();
				
			break;
			case "play":
				
				g_api.play();
			break;
			default:
				throw new Error("GALLERY API: Wrong action: "+action);
			break;
		}
		
	}
	
	/*
	  events
	*/	
	this.onEvent = function(name, func){
	
		  validateInited();
		  
	      switch(name){
			case "change":
				g_api.on("item_change", func);
			break;
			case "play":
				g_api.on("play",func);
			break;
			case "pause":
				g_api.on("stop",func);
			break;
			default:
				throw new Error("Gallery API: Wrong event: "+name);
			break;
	
		}
	}
	

	/**
	 * get element
	 */
	this.getElement = function(){
		
		return(g_objParent);
	}

	
	/**
	 * get api type
	 */
	this.getAPIType = function(){
		
		return("gallery");
	}

	
	/**
	* init the api
	*/
	this.init = function(objParent, options){
				
		g_api = objParent.data("unitegallery-api");
		if(!g_api)
			return(false);
		
		g_objParent = objParent;
		
		g_isInited = true;
		
		return(true);
	}
	
	
	
}


/**
* remote carousel api class
*/
function UERemoteCarouselAPI(){

	var g_owlCarousel, g_owl, g_isInited;
	var t = this;
	var enableDebug = false;
	
	
	/**
	* console log some string
	*/
	function trace(str){
		console.log(str);
	}

	/**
	* validate that the object is inited
	*/
	function validateInited(){
		
		if(g_isInited == false)
			throw new Error("The owl carousel API is not inited");
	}

	/**
	 * get total items
	 */
	function getTotalItems(){
		
		var total = g_owlCarousel.find(".owl-item:not(.cloned)").length;
		
		return(total);
	}
	
	/**
	 * reset the autoplay if exists
	 */
	function resetAutoplay(){
		
		if(g_owl.settings.autoplay == false)
			return(false);
	  			
		g_owlCarousel.trigger('stop.owl.autoplay');
		g_owlCarousel.trigger('play.owl.autoplay');		
				
	}
	
	/**
		do some action
	*/
	this.doAction = function(action, arg1, arg2){
		
		validateInited();
		
		if(enableDebug == true){
			trace("carousel action: "+action+" "+arg1+" "+arg2);
		}
		
		switch(action){
			case "next":
				
				g_owlCarousel.trigger('next.owl.carousel');				
			break;
			case "prev":
				g_owlCarousel.trigger('prev.owl.carousel');
			break;
			case "play":
				
				g_owlCarousel.trigger('start_autoplay.owl.autoplay');
				
			break;
			case "pause":

				g_owlCarousel.trigger('stop.owl.autoplay');
				
			break;
			case "is_playing":
				
				if (g_owl.settings.autoplay == true) 
                    return(true)
                else 
                    return(false);

			break;
			case "get_total_items":
				
				var total = getTotalItems()
				
				return(total);

			break;
			case "get_progress_time":
				
      			var progressTime = g_owl.settings.autoplayTimeout / 1000;
				return(progressTime);				

			break;
			case "get_modified_progress_time":
				
      			var progressTime = (g_owl.settings.autoplayTimeout - g_owl.settings.smartSpeed) / 1000;
				return(progressTime);				

			break;
			case 'get_num_current':
				
      			var currentItem = g_owl.relative(g_owl.current());
      			
      			if(enableDebug == true){
      				trace("num current: " + currentItem);
      			}
      			
      			return(currentItem);
			break;
			case "get_total_text":

				var owlTotalItems = g_owlCarousel.find(".owl-item:not(.cloned)").length;
				if(owlTotalItems.toString().length < 2){
                    owlTotalItems = "0" + owlTotalItems;
                   }
				return(owlTotalItems);

			break;			
            case "get_current_text":
                
                var owlCurrentItem = g_owl.relative(g_owl.current()) + 1;
                if(owlCurrentItem.toString().length < 2){
                    owlCurrentItem = "0" + owlCurrentItem;
                   }
				return(owlCurrentItem);                                        
                                                        
            break;
			case 'change_item':
					
				var total = getTotalItems()
				var currentItem = g_owl.relative(g_owl.current());
				
				var slideNum = arg1;
				
				if(slideNum == currentItem)
					return(false);
				
				if(slideNum >= total)
					slideNum = (total-1);
				else
					if(slideNum < 0)
						slideNum = 0;
								
                g_owlCarousel.trigger('to.owl.carousel', [slideNum, null, true]);                 
                
                resetAutoplay();
                
			break;
			default:
				throw new Error("Carousel API: Wrong action: "+action);
			break;
		}
		
	}

	
	/*
		events
	*/
	this.onEvent = function(name, func){
		  
		  validateInited();
		  
          switch(name){
			case "play":
								
				g_owlCarousel.on("play_autoplay.owl.carousel", func);
				
			break;
			case "pause":
								
				g_owlCarousel.on("stop_autoplay.owl.carousel", func);
				
			break;
			case "change":
				
				g_owlCarousel.on("changed.owl.carousel", func);
				
			break;
			case "transition_start":
				
				g_owlCarousel.on("translate.owl.carousel", func);

			break;
			case "transition_end":
				
				g_owlCarousel.on("translated.owl.carousel", func);

			break;
			case "refreshed":
				
				g_owlCarousel.on("refreshed.owl.carousel", func);
				
			break;
			default:
				console.error("Carousel API: Wrong event: "+name);
				throw new Error("Carousel API: Wrong event: "+name);
			break;

		}
	}

	/**
	 * get api type
	 */
	this.getAPIType = function(){
		
		return("carousel");
	}
	
	/**
	 * get the element
	 */
	this.getElement = function(){
		
		return(g_owlCarousel);
	}
	
	
	/**
	* init the api
	*/
	this.init = function(objParent){
		
		if(objParent.hasClass("owl-carousel") == false)
			throw new Error("owl-carousel class not found");
		
		g_owlCarousel = objParent;
        
		g_owl = g_owlCarousel.data("owl.carousel");
				
		if(!g_owl)
			return(false);
				
		g_isInited = true;
		
		return(true);

	}
	
}

/**
 * sync object
 */
function UESyncObject(){
	
	var g_objApis = {};
	var g_groupName, g_objIDs;
	var t = this;
	
	var g_vars = {
		is_editor:false,
		is_editor_func_started:false,
		show_debug:false
	};
	
	/**
	* console log some string
	*/
	function trace(str){
		console.log(str);
	}	
	
	
	
	/**
	 * validate api, that they match the existing by number of items
	 */
	function validate(objAPI){
		
		if(g_objApis.length == 0)
			return(false);
		
		var numItems = objAPI.doAction("get_total_items");
		
		//check with first existing api number of items
		for(var elID in g_objApis){
			
			var firstExistingAPI = g_objApis[elID];
			
			var numItemsExisting = firstExistingAPI.doAction("get_total_items");
			
			if(numItemsExisting !== numItems)
				throw new Error("Sync failed, number of items should be the same. Now it's "+numItems+" and "+numItemsExisting);
			
			return(false);
		}
		
				
	}
	
	
	/**
	 * set options
	 */
	this.setOptions = function(groupName, isEditor){
		
		if(isEditor === true)
			g_vars.is_editor = true;
		
		if(!g_groupName)
			g_groupName = groupName;
		
	}
	
	
	/**
	 * get api parent id
	 */
	function getElementID(objAPI){
		
		var objElement = objAPI.getElement();
		
		var elementID = objElement.attr("id");
		
		return(elementID);
	}
	
	
	/**
	 * get all ips except the given
	 */
	function mapAPIs(func, objElement){
		
		if(typeof ucRemoteDebugEnabled != "undefined")
			g_vars.show_debug = true;
		
		var elementID = null;
		
		if(objElement){
			
			var elementID = objElement.attr("id");
			
			if(g_objApis.length == 1)
				return(null);
		}
				
		for(var elID in g_objApis){
			
			var api = g_objApis[elID];
			
			//except if exists
			if(elementID && elID == elementID)
					continue;
			
			func(api);
			
		}
		
	}
	
	
	/**
	 * activate change command on all other apis
	 */
	function onItemChange(objAPI){
		
		var numCurrent = objAPI.doAction("get_num_current");
		
		var objElement = objAPI.getElement();
		
		if(g_vars.show_debug == true){
			
			trace("sync onchange: " + numCurrent);
			trace(objElement);
			trace(g_objApis);
		}
			
		mapAPIs(function(api){
						
			api.doAction("change_item", numCurrent);
			
		}, objElement);
		
	}
	
	/**
	 * on pause - pause others as well
	 */
	function onPause(objAPI){
		
		var objElement = objAPI.getElement();
		
		mapAPIs(function(api){
						
			api.doAction("pause");
			
		}, objElement);
		
		
	}
	
	
	/**
	 * trigger some action
	 */
	function trigger(action, params){
		
		var realAction = "uc_remote_sync_"+g_groupName+"_action_"+action;
		
		jQuery("body").trigger(realAction, params);
	}
	
	
	/**
	 * on event
	 */
	this.on = function(action, func){
		
		var realAction = "uc_remote_sync_"+g_groupName+"_action_"+action;
				
		jQuery("body").on(realAction, func);
		
	}
	
	
	/**
	 * get debug text
	 */
	this.getDebugText = function(objElement){
		
		var text = "sync group: <b>" + g_groupName+"</b>, ";
		
		var textWidgets = "";
		
		mapAPIs(function(api){
			
			var objElement = api.getElement();
			var widgetName = objElement.data("widgetname");
			var widgetID = objElement.attr("id");
			
			if(textWidgets)
				textWidgets += ", ";
			
			textWidgets += "<a href='#"+widgetID+"' style='color:green;text-decoration:underline'><b>" + widgetName + "</b></a>";
		
		}, objElement);
				
		if(textWidgets)
			text += "sync with: " + textWidgets;
		
		return(text);
	}
	
	
	/**
	 * remove deleted from page APIs
	 */
	function removeDeletedAPIs(){
		
		var objAPIsNew = {};
				
		mapAPIs(function(api){
			
			var elementID = getElementID(api);
			var objElement = jQuery("#"+elementID);
						
			if(objElement.length == 0)
				return(false);
						
			var parent = objElement.parent();
			
			objAPIsNew[elementID] = api;
			
		});
		
		g_objApis = objAPIsNew;
		
	}
	
	
	/**
	 * on editor check
	 */
	function onEditorCheck(){
		
		removeDeletedAPIs();
		
		trigger("update_debug", g_groupName);
		
	}
	
	/**
	 * get group name
	 */
	this.getGroupName = function(){
		
		return(g_groupName);
	}
	
	/**
	 * add widget to sync object
	 */
	this.addAPI = function(objAPI){
		
		var id = getElementID(objAPI);
		
		if(g_objApis.hasOwnProperty(id))
			return(false);
		
		g_objApis[id] = objAPI;
		
		if(g_vars.is_editor == true)
			removeDeletedAPIs();
		
		validate(objAPI);
		
		
		//debug
		var objElement = objAPI.getElement();
		
		//add debug		
		trigger("update_debug", g_groupName);
		
		//set events
		objAPI.onEvent("change", function(){
			
			onItemChange(objAPI);
			
		});
		
		//sync objects pause 
		
		objAPI.onEvent("pause", function(){
			
			onPause(objAPI);
		});
				
		
		if(g_vars.is_editor == true && g_vars.is_editor_func_started == false){
			
			setInterval(onEditorCheck, 700);
			g_vars.is_editor_func_started = false;
		}
			
		
	}
	
	/**
	 * get all elements
	 */
	this.getElements = function(exceptID){
		
		if(!g_objApis)
			return(null);
		
		var arrElements = [];
		
		//check with first existing api number of items
		for(var elID in g_objApis){
			
			if(exceptID && exceptID == elID)
				continue;
			
			var objApi = g_objApis[elID];
			
			var objElement = objApi.getElement();
			
			arrElements.push(objElement);
		}
		
		if(arrElements.length == 0)
			return(null);
		
		return(arrElements);
	}
	
}

/**
* ---------------------------------------------------
* remote widget api class
*/
function UERemoteWidgets(){
	
	var g_objParent, g_objWidget, g_parentID;
	var g_api, g_objSync, g_remoteConnection = window.ueRemoteConnection;
	var t = this;
		
	var g_vars = {
		is_inited:false,
		funcOnInit:null,
		is_editor:null,
		widget_id:null,
		init_options:null,
		is_parent_mode: false,
		syncid:null,
		options_api:null,
		show_connection_debug:false,
		debug_show_ids:false,
		debug_show_widget: "",
		trace_debug:false,	//debug
		show_trace_when_debug_on: false
	};
	
	var g_types = {
		CAROUSEL:"carousel",
		GALLERY:"gallery",
		GENERAL:"general"
	};
	
	
	/**
	* console log some string
	*/
	function trace(str){
		console.log(str);
	}	
	
	/**
	 * get object property
	 */
	function getVal(obj, name, defaultValue){
		
		return window.ueRemoteConnection.getVal(obj, name, defaultValue);
	}
	
	
	
	function _______INIT_________(){}
	
	/**
	* init widget 
	*/
	function initWidget(widgetID){
		
		g_objWidget = jQuery("#"+widgetID);
		if(g_objWidget.length == 0)
			throw new Error("Widget not found by id: "+widgetID);
		
		g_vars.widget_id = widgetID;
		
		var forceID = getVal(g_vars.init_options, "force_parentid");
		
		if(forceID)
			g_parentID = forceID;
		else		
			g_parentID = g_objWidget.data("parentid");
		
		if(!g_parentID)
			throw new Error("Parent controlled ID not set");
        
	}

	/**
	* get controlled parent type 
	*/
	function getParentType(){
		
		if(!g_objParent || g_objParent.length == 0)
			throw new Error("getParentType: no parent found");
		
		if(g_objParent.hasClass("owl-carousel"))
			return(g_types.CAROUSEL);
		
		var dataType = g_objParent.data("remote-type");
		
		switch(dataType){
			case "gallery":
				return g_types.GALLERY;
			break;
		}
		
		
		return(g_types.GENERAL);
	}
	
	
	/**
	 * get offsets distance
	 */
	function getOffsetsDistance(offset1, offset2){
	  
	  var dx = offset2.left-offset1.left;
	  var dy = offset2.top-offset1.top;
	  
	  return Math.sqrt(dx*dx+dy*dy); 
	}
	
	
	/**
	 * get closest object by offset
	 */
	function getClosestByOffset(objParents, objElement){
		
		var objClosest = null;
		var minDiff = 1000000;
		
		var elementOffset = objElement.offset();
		
		if(g_vars.trace_debug){
			trace("Widget Offset: ");
			trace(elementOffset);
		}
		
		jQuery.each(objParents, function(index, parent){
			
			var objParent = jQuery(parent);
			var parentOffset = objParent.offset();
			
			if(parentOffset.top == 0){
				
				var firstParent = getWidgetContainer(objParent);
				var parentOffset = firstParent.offset();
				
			}
			
			var distance = getOffsetsDistance(parentOffset, elementOffset);
			
			if(g_vars.trace_debug){
				trace("Parent offset: ");
				trace(objParent);
				trace(parentOffset);
				trace("distance: " + distance);
			}
			
			if(distance < minDiff){
				minDiff = distance;
				objClosest = objParent;
			}
			
		});
				
		return(objClosest);
	}

	/**
	 * filter parents by section
	 */
	function filterParentsBySection(objParents, objElement){
				
		var objSection = objElement.closest(".elementor-top-section");
		
		if(objSection.length == 0)
			return(objParents);
		
		var section = objSection[0];
		
		var objFiltered = objParents.filter(function(index, parent){
			
			var isContains = jQuery.contains(section, parent);
			
			return isContains;
		});
		
		if(objFiltered.length)
			return(objFiltered);
		
		return(objParents);
	}
	
	
	/**
	 * detect closest parent
	 */
	function detectClosestParent(objParents){
		
		if(!objParents)
			var objParents = jQuery(".uc-remote-parent").not(g_objWidget);
		
		if(g_vars.trace_debug){
			trace("detect closest start. group:");
			trace(objParents);
		}
		
		var numParents = objParents.length;
				
		if(numParents == 0)
			return(null);
		
		if(numParents == 1)
			return(objParents);
		
		//filter by auto
		
		if(g_vars.trace_debug)
			trace("filter auto");
		
		var objParentsFiltered = objParents.filter("[data-remoteid='"+g_parentID+"']");
		
		if(objParentsFiltered.length == 1)
			return(objParentsFiltered);
		
		//find by offset
		
		if(g_vars.trace_debug){
			trace("filter by section");
		}
		
		var objParents = filterParentsBySection(objParents, g_objWidget);
		
		if(g_vars.trace_debug){
			trace(objParents);
		}
		
		if(objParents.length == 1)
			return(objParents);
		
		if(g_vars.trace_debug)
			trace("get closest by parent");
		
		var objClosest = getClosestByOffset(objParents, g_objWidget);
		
		if(objClosest)
			return(objClosest);

		if(g_vars.trace_debug)
			trace("get first");
		
		var firstParent = jQuery(objParentsFiltered[0]);
		
		
		return(firstParent);
	}
	
	/**
	 * set parent object
	 */
	function setParentObject(){
		
		var objForceParent = getVal(g_vars.init_options, "force_parent_obj");
		
		var widgetID = g_objWidget.attr("id");
		
		if(g_vars.trace_debug)
			trace("start set parent for: "+widgetID);
		
		if(objForceParent){
			
			g_objParent = objForceParent;
			
			if(g_vars.trace_debug){
				trace("set force parent");
				trace(g_objParent);
			}
			
			return(false);
		}
		
		if(!g_parentID)
		   throw new Error("Parent controller ID not found");
		
		if(!g_objParent || g_objParent.length == 0){
			
			if(g_parentID == "auto"){
				
				if(g_vars.trace_debug){
					trace("auto detect selected");
				}
								
				g_objParent = detectClosestParent();
				
				if(g_vars.trace_debug){
					trace("detect closest");
					trace(g_objParent);
				}
				
				if(!g_objParent)
					throw new Error("Can't detect remote parent");
												
			}
			else{
				
				var objParents = jQuery(".uc-remote-parent[data-remoteid='"+g_parentID+"']").not(g_objWidget);
				
				if(g_vars.trace_debug)
					trace("Detect from group: "+g_parentID);
				
				g_objParent = detectClosestParent(objParents);
				
				if(g_vars.trace_debug){
					trace("detected from group");
					trace(g_objParent);
				}
								
				if(!g_objParent || g_objParent.length == 0)
				   throw new Error("Parent widget with remote name: '"+g_parentID+"' not found");
			}
			
		}
		
		if(g_objParent && g_objParent.length > 1){
			g_objParent = jQuery(g_objParent[0]);
		}
		
		if(g_vars.trace_debug == true){
			
			var parentID = g_objParent.attr("id");
			var widgetID = g_objWidget.attr("id");
			
			trace("widget: "+widgetID+" connected to: "+parentID);
			
		}
		
		if(!g_objParent || g_objParent.length == 0)
			  throw new Error("Remote parent not found");
		
	}
	
	
	/**
	 * init api variable
	 */
	function initAPI(){
				
		if(g_vars.trace_debug == true){
			trace("start init api function");
		}
		
		//set type and related objects
		if(!g_api){
			
			var parentType = getParentType();
			
						
			if(!parentType){
				trace(g_objParent);
				throw new Error("No parent type found");
			}

			if(g_vars.trace_debug == true){
				
				trace("init api: " + parentType);
				trace(g_objParent);
			}
			
			//init the api
			switch(parentType){
				case g_types.CAROUSEL:
					g_api = new UERemoteCarouselAPI();
				break;
				case g_types.GENERAL:
					
					g_api = new UERemoteGeneralAPI();
					
				break;
				case g_types.TABS:
					
					//g_api = new UERemoteTabsAPI();
					g_api = new UERemoteGeneralAPI();
					
				break;
				case g_types.GALLERY:
				
					g_api = new UERemoteGalleryAPI();					
				
				break;
				default:
					throw new Error("Wrong parent type: "+parentType);
				break;
			}
			
		}
		
		var optionsFromData = g_objParent.data("uc-remote-options");
		
		var isEditor = isInsideEditor();
				
		if(optionsFromData)
			g_vars.options_api = optionsFromData;
		
		if(g_vars.trace_debug == true){
			if(g_vars.options_api)
				trace(g_vars.options_api);
			else
				g_vars.options_api = {};
			
			g_vars.options_api.trace_debug = true;
		}
		
		var isInited = g_api.init(g_objParent, g_vars.options_api, isEditor);
		
		if(g_vars.trace_debug == true){
			trace("inited: " + isInited);
		}
		
		return(isInited);
	}
	
	
	/**
	*	init parent
	*/
	function initParent(){
		
		setParentObject();
		
		var isInited = initAPI();
				
		return(isInited);		
	}
	
	
	/**
		init global helper function
	*/
	function initGlobal(widgetID, func){
		
		if(!g_objWidget)
			initWidget(widgetID);
		
		//init the debug related
		
		var isDebug = g_objWidget.data("debug");
		if(isDebug === true || typeof ucRemoteDebugEnabled != "undefined"){
			
			if(g_vars.show_trace_when_debug_on == true)
				g_vars.trace_debug = true;
			
			g_vars.show_connection_debug = true;
		}
				
		
		g_vars.is_inited = initParent();
		
		if(g_vars.is_inited == false){
			
			if(g_vars.trace_debug == true){
				
				trace(g_objParent);
				trace("set object ready event");	//onWidgetInit
			}
			
			g_objParent.on("uc-object-ready", func);
			
		}
	}
	
	
	/**
	* set action, bind to some object
	* objElement can be jQuery object or selector
	*/
	this.setAction = function(action, objElement, allowMultiple){
		
		if(g_vars.trace_debug == true){
			trace("set action: "+action);
		}
		
		if(g_vars.is_inited == false)
			throw new Error("Widget not inited");
		
		if(typeof objElement == "string"){
		  var selector = objElement;
		   objElement = g_objWidget.find(objElement);
		   if(objElement.length == 0)
				throw new Error("Remote '"+action+"' action error: element: "+selector+" not found");
		}
		
		if(!objElement || objElement.length == 0)
			throw new Error("Element not inited");	
		
		if(!g_api)
			throw new Error("API not inited!");	
			
		//avoid double action
		var linkedAction = objElement.data("uc-action");
		
		if(allowMultiple !== true)
			if(linkedAction)
				return(false);
				
		objElement.data("uc-action", action);
		
		objElement.on("click",function(){
			
			var objElement = jQuery(this);
			
			if(objElement.hasClass("uc-disabled"))
				return(true);
			
			t.doAction(action);
			
		});
		
	}
	
	
	/**
	 * run this function after the widget is ready to use
	 */
	function onWidgetReady(){
				
		checkWidgetDebug();
		
		var isEditorMode = isInsideEditor();
		
		//in editor mode check debug every second
		
		if(isEditorMode == true){
			
			hideErrorOnWidget();
			
			setInterval(checkWidgetInsideEditor, 700);
		}		
		
		
	}
	

	function _______TEXT_ON_WIDGET_________(){}
	
	
	/**
	 * hide error on widget
	 */
	function hideErrorOnWidget(){
		
		if(!g_objWidget || g_objWidget.length == 0)
			return(false);
		
		var objParent = getWidgetContainer(g_objWidget);
		
		//don't hide if no message
		
		var objError = objParent.find('.uc-remote-error');
		
		if(objError.length == 0)
			return(false);
		
		if(objError.is(":visible") == false)
			return(false);
		
		objError.hide();
		
		g_objWidget.css({
			"border" : "none"
		});
		
		//try to set the debug if avaliable
		checkWidgetDebug();
				
	}
	
	/**
	 * get widget container
	 */
	function getWidgetContainer(objWidget){
		
		var objParent = objWidget.parents(".elementor-widget-container");
		if(objParent.length == 0)
			objParent = objWidget.parent();
		
		return(objParent);
	}
	
	
	/**
	 * add error message div on the widget div
	 * 
	 */
	function addTextDiv(objWidget, type){
		
		var objParent = getWidgetContainer(objWidget);
		
		var isDebug = (type == "debug");
		
		var className = "uc-remote-error";
		if(isDebug == true)
			className = "uc-remote-debug";
		
		var divText = "<div class='"+className+"'></div>";
		objParent.append(divText);
		
		var objError = objParent.find('.'+className+'');
		
		var css = {
				"position":"absolute",
				"color":"red",
				"top":"-30px",
				"left":"0px",
				"z-index":"999999",
				"background-color":"#ffffff"			
		};
		
		if(isDebug == true){
			css["color"] = "green";
			css["z-index"] = "999998";
		}
		
		//fix position for bg
		var objParentsBG = objParent.parents(".unlimited-elements-background-overlay");
		if(objParentsBG.length){
			css["top"] = "0px";
		}
		
		objError.css(css);
		
		var objError = objParent.find('.'+className+'');
		
		return(objError);
	}
	
	/**
	 * display some error on widget interface
	 */
	function displayTextOnWidget(objWidget, message, type){
		
		var isDebug = (type == "debug");
		
		var className = "uc-remote-error";
		if(isDebug == true)
			className = "uc-remote-debug";
		
		var objParent = getWidgetContainer(objWidget);
		
		objText = objParent.find("."+className);
		
		//add error div if missing. pause error in editor
		//second time the div will be not empty and it will show the message
		if(objText.length == 0){
			
			objText = addTextDiv(objWidget, type);
			
			if(isDebug == false){
			
				var isInEditor = isInsideEditor();
				
				if(isInEditor == true){
					
					setTimeout(function(){
						displayTextOnWidget(objWidget, message, type);
					},2000);
					
					return(false);
				}
				
			}
			
		}
	    
		//add the error border
		if(isDebug == false){
			
			objWidget.css({
				"border" : "2px solid red",
				"position" : "relative"
			});
			
		}
		
		objText.show();
		objText.html(message);
		
	}
	
	
	/**
	 * display error message
	 */
	function displayErrorMessage(message){
		
		//trace(message);
		//console.trace();
		
		if(g_vars.is_parent_mode == false){
			
			if(g_objWidget && g_objWidget.length)
				displayTextOnWidget(g_objWidget, message,"error");
				
		}else{
			
			displayTextOnWidget(g_objParent, message, "error");
			
		}
				
		//console.log("UE Remote Error: "+message);
		//console.log(message);			
		
	}
	
	function _______DEBUG_________(){}
	
	
	/**
	 * return if the debug is active
	 */
	function isDebugActive(objWidget){
		
		if(!objWidget)
			objWidget = g_objWidget;
		
		var isActive = objWidget.data("debug_active");
		
		if(isActive === true)
			return(true);
		
		return(false);
	}
	
	
	/**
	 * remove debug visual from the widget
	 */
	function removeDebugVisual(objWidget){
				
		if(!objWidget)
			objWidget = g_objWidget;
		
		g_objWidget.data("debug_active", false);
		
		g_objWidget.css({
			"border-style":"none"
		});
		
	}
	
	
	/**
	 * set debug visual
	 */
	function setDebugVisual(color, objWidget){
		
		if(!objWidget)
			objWidget = g_objWidget;
				
		objWidget.data("debug_active", true);
			
		objWidget.css({
			"border-style":"solid",
			"border-width":"3px",
			"border-color":color
		});
		
	}
	
	/**
	 * check if parent debug is active
	 */
	function isParentDebugActive(objParent){
		
		var dataDebug = objParent.data("debug");
		var isDebug = (dataDebug === true);
		
		return(isDebug);
	}
	
	
	/**
	 * check widget debug work
	 */
	function checkWidgetDebugWork(objWidget, objParent){
		
		if(!objWidget)
			objWidget = g_objWidget;
		
		if(!objParent)
			objParent = g_objParent;
						
		var isDebug = isParentDebugActive(objParent);
		
		var isActive = isDebugActive(objWidget);
		
		/*
		trace("check! "+isActive+" "+isDebug);
		trace(objParent);		
		trace(objWidget);		
		*/
		
		//remove debug if active but no need
		
		if(isDebug == false){
			
			if(isActive == true)
				removeDebugVisual(objWidget);
			
			return(false);
		}
		
		//add debug is not in debug mode
		
		if(isActive == false){
			
			//get parent color
			var color = addParentDebug(objParent);
			setDebugVisual(color, objWidget);
			
		}
		
		
	}
	
	/**
	 * check and put debug connection
	 */
	function checkDebugConnectionText(){
		
		if(!g_objWidget)
			return(false);
		
		if(g_vars.show_connection_debug == false)
			return(false);
		
		if(!g_objParent || g_objParent.length == 0)
			throw new Error("Not conneted to no parent");
		
		var widgetName = g_objParent.data("widgetname");
		var widgetID = g_objParent.attr("id");
				
		var text = "connected to <a href='#"+widgetID+"' style='color:green;text-decoration:underline'><b>" + widgetName + "</b></a>";
		
		displayTextOnWidget(g_objWidget, text, "debug");
		
	}
	
	
	/**
	 * check widget debug
	 */
	function checkWidgetDebug(){
		
		var noDebugCheck = getVal(g_vars.init_options, "no_debug_check");
		
		if(noDebugCheck === true)
			return(false);
				
		//get debug color		
		if(!g_objParent || g_vars.is_inited == false){
			removeDebugVisual();
			return(false);
		}
		
		checkWidgetDebugWork();
		
		checkDebugConnectionText();
		
	}
		
	/**
	 * on api event
	 */
	this.onEvent = function(name, func){ 
		g_api.onEvent(name,func);
	}

	
	/**
	 * change item by action (0 - total-1)
	 */
	function changeItemByAction(dir){
		
		var current = t.doAction("get_num_current");
		var total = t.doAction("get_total_items");
		
		switch(dir){
			case "next":
				
				var num = current+1;
				if(num >= total )
					num = 0;
				
			break;
			case "prev":
				
				var num = current-1;
				if(num < 0)
					num = total-1;
				
			break;
			default:
				throw new Error("wrong direction type: "+dir);
			break;
		}
		
		t.doAction("change_item", num);
		
	}
	
	
	/**
	 * do api action
	 */
	this.doAction = function(action, arg1, arg2){
		
		if(g_vars.trace_debug){
			trace("Do Action: ");
			trace(action+ " "+arg1+" "+arg2);
		}
		
		switch(action){
			case "prev":
			case "next":
				
				var apiType = g_api.getAPIType();
				
				if(apiType == "carousel"){
					
					g_api.doAction(action);
					
					return(false);
				}
				
				changeItemByAction(action);
				return(false);
			break;
		}
				
		var response = g_api.doAction(action, arg1, arg2);
		
		if(g_vars.trace_debug){
			trace("Response: ");
			trace(response);
		}


		return(response);
	}
	
	/**
	 * add the parent some debug color
	 */
	function addParentDebug(objParent){
		
		if(!objParent)
			objParent = g_objParent;
		
		var color = g_objParent.data("uc-debug-color");
		if(color)
			return(color);
		
		var objBody = jQuery("body");
		var dataColors = "uc-remote-debug-colors";
		
		var objColors = objBody.data(dataColors);
		
		if(!objColors){
			objColors = ["#ffeb00","blue","#808000","#d1e231","#01796f","#8e4585","#ff33cc","#436b95","#eaa221","#b86d29"];
		}
		
		var color = objColors.pop();
		
		g_objParent.data("uc-debug-color", color);
		objBody.data(dataColors, objColors);
		
		g_objParent.css("border","3px solid "+color);		
		
		return(color);
	}
	
	
	/**
	 * update sync debug
	 */
	function updateSyncDebug(event, syncID){
		
		//if show id's disable other debug
		if(g_vars.debug_show_ids == true)
			return(false);
		
		try{
			
			if(syncID != g_vars.syncid){
				
				var name = g_objParent.data("widgetname");
				throw new Error("Wrong sync group mishmash "+g_vars.syncid+" and " + syncID);
			}
						
			var debugText = g_objSync.getDebugText(g_objParent);
			
			if(!debugText)
				return(false);
			
			displayTextOnWidget(g_objParent, debugText, "debug");
			
		}catch(error){
			
			displayErrorMessage(error);
			
		}

	}
	
	
	function _______EDITOR_RELATED_________(){}
	
	
	/**
	 * check if nside editor
	 */
	function isInsideEditor(){
		
		if(g_vars.is_editor !== null)
			return(g_vars.is_editor);
		
		if(typeof window.parent == "undefined"){
			
			g_vars.is_editor = false;
			
			return(false);
		}
		
		if(typeof window.parent.elementor != "undefined"){
			
			g_vars.is_editor = true;
			
			return(true);
		}
		
		g_vars.is_editor = false;
		
		return(false);
	}
	
	/**
	 * reset the settings
	 */
	function resetSettingsInsideEditor(){
		
		g_objParent = null;
		g_api = null;
		g_vars.is_inited = false;
		
	}
	
	
	/**
	 * check widget inside editor
	 */
	function checkWidgetInsideEditor(){
		
		//check for disconnect
		try{
			
			hideErrorOnWidget();
			
			if(g_vars.is_inited == true){
				
				if(g_objParent.is(":hidden")){
					
					//disconnect
					resetSettingsInsideEditor();
				}
				
			}else{
				
				//check for connect
				g_vars.is_inited = initParent();
				
				if(g_vars.is_inited == true){
					
					g_vars.funcOnInit();
				}
				else				
				if(g_vars.is_inited == false){
					
					g_objParent.on("uc-object-ready", function(){
						
						g_vars.is_inited = true;
						g_vars.funcOnInit();
						
					});
				}
				
			}
			
			checkWidgetDebug();
		
		}catch(message){
			
			displayErrorMessage(message);
			
			return(false);
		}	
		
				
	}
		
	
	
	/**
	* on widget init
	*/
	this.onWidgetInit = function(widgetID, func, options){
		
		try{
			
			if(g_vars.trace_debug == true){
				trace("on widget init");
			}
						
			if(!g_vars.funcOnInit){
				
				if(typeof func != "function")
					throw new Error("onWidgetInit error: the second parameter should be a function");
				
				g_vars.funcOnInit = func;
			}
						
			if(options && g_vars.init_options == null)
				g_vars.init_options = options;
			
			if(g_vars.debug_show_widget && g_vars.debug_show_widget == widgetID)
				g_vars.trace_debug = true;
			
			initGlobal(widgetID, t.onWidgetInit);
			
			if(g_vars.is_inited == false){
				
				if(g_vars.trace_debug == true){
					trace(widgetID+" not inited yet, waiting for parent init");
				}
				
				return(false);
			}
			
			//show id's on the widgets
			if(g_vars.debug_show_ids == true){
				
				trace("start debug show id's");
				
				displayTextOnWidget(g_objWidget, g_objWidget.attr("id"), "debug");
			}
			
			if(g_vars.trace_debug == true)
				trace("start debug - show connect");
			
			if(g_objParent.length > 1){
				
				trace(g_objWidget);
				trace(g_objParent);
				
				throw new Error("Remote widget can't connect to more then 1 parents");
			}
			
			//widget is inited
						
			onWidgetReady();
						
			g_vars.funcOnInit(g_objWidget);
			
		}catch(message){
			
			
			displayErrorMessage(message);
			
			var isEditorMode = isInsideEditor();
			
			//in editor mode check debug every second
			
			if(isEditorMode == true)
				setInterval(checkWidgetInsideEditor, 700);
			
			return(false);
		}	
		
	}
			
	
	/**
	 * start sync
	 */
	function startParentSync(){
		
		var syncID = g_objParent.data("syncid");
		
		
		if(g_vars.trace_debug == true){
			trace("Start parent sync");
			trace(g_objParent);
		}
		
		if(!syncID){
			
			if(g_vars.trace_debug == true){
				trace("no sync id");
			}
			
			return(false);
		}
		
		var objSync = g_remoteConnection.getSyncObject(syncID);
		
		var isEditorMode = isInsideEditor();
				
		objSync.setOptions(syncID, isEditorMode);
				
		var isInited = initAPI();
				
		if(isInited == false){
			
			var widgetID = g_objParent.attr("id");
			
			var parentType = getParentType();
			
			var message = "Sync Error - can't init api for "+widgetID; 
			
			if(parentType == g_types.CAROUSEL){
				
				message += ", please check that the owl carousel js file loading from unlimited elements plugin.";
				
			}else{
				message += ", please check if the widget is inited and working.";
			}
			
			throw new Error(message);
		}
		
		g_vars.syncid = syncID;
		
		//add debug event listener
		if(g_vars.trace_debug === true)
			objSync.on("update_debug", updateSyncDebug);
		
		g_objSync = objSync;
		
		objSync.addAPI(g_api);
		
		//if(isEditorMode == true)
			//setInterval(checkSyncInsideEditor, 700);
		
	}
	
	
	/**
	 * parent init, init the debug
	 */
	this.onParentInit = function(objParent, optionsAPI){
		
		try{
			
			g_objParent = objParent;
			
			if(!g_objParent)
				return(false);
			
			if(g_objParent.length == 0)
				return(false);
			
			g_vars.is_parent_mode = true;
			
			var optionsFromData = g_objParent.data("uc-remote-options");
			
			if(optionsFromData)
				optionsAPI = optionsFromData;
						
			if(optionsAPI)
				g_vars.options_api = optionsAPI;
			
			var isDebug = g_objParent.data("debug");
			
			if(typeof ucRemoteDebugEnabled != "undefined")
				isDebug = true;
			
			g_vars.trace_debug = isDebug;
							
			if(isDebug === true)
				addParentDebug(objParent);
			
			var isSync = objParent.data("sync");
			
			if(isSync == true)
				startParentSync();
			
			if(g_vars.debug_show_ids == true){
				displayTextOnWidget(g_objParent, g_objParent.attr("id"), "debug");
			}
			
		}catch(message){
			
			displayErrorMessage(message);
			
			return(false);
			
		}
		
	}
	
	/**
	 * show the info
	 */
	this.showInfo = function(){
		
		trace("parent");
		trace(g_objParent);
		
		trace("current widget");
		trace(g_objWidget);
		
		
	}
	
	
	/**
	 * get the connected parent
	 */
	this.getParent = function(){
		
		return(g_objParent);
	}
	
}

/**
 * remote connection class - for interacting with outside
 */
function UERemoteConnection(){
	
	var t = this;
	
	/**
	 * get object property
	 */
	this.getVal = function(obj, name, defaultValue){
		
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
	 * get the sync object, if not exists, create one
	 */
	this.getSyncObject = function(syncID){
		
		var syncRealID = "uc_sync_"+syncID;
		
		var objSync = t.getVal(window, syncRealID);
		
		if(objSync)
			return(objSync);
		
		var objSync = new UESyncObject();
		
		window[syncRealID] = objSync;
		
		return(objSync);
	}
	
	
	/**
	 * get object synced other objets id's
	 */
	this.getSyncedElements = function(objElement){
		
		if(!objElement)
			return(null);
		
		if(objElement.length == 0)
			return(null);
		
		if(objElement.hasClass("uc-remote-parent") == false)
			return(null);
		
		var isSync = objElement.data("sync");
		
		if(isSync !== true)
			return(null);
		
		var syncID = objElement.data("syncid");
		
		if(!syncID)
			return(null);
		
		var objSync = t.getSyncObject(syncID);
		
		var currentID = objElement.attr("id");
		
		var arrElements = objSync.getElements(currentID);
		
		return(arrElements);
	}
	
}


//body init

jQuery("body").on("uc-remote-parent-init",function(event, objParent, optionsAPI){
	
	var objRemote = new UERemoteWidgets();
	
	objRemote.onParentInit(objParent, optionsAPI);
	
});

window.ueRemoteConnection = new UERemoteConnection();


