
window.g_ueSettingsAPI = null;

function UnlimitedElementsWidgetSettingsAPI(){
	
	var g_id, g_frontAPI;
	
	function trace(str){
		console.log(str);
	}
	
	
	/**
	 * on event
	 */
	this.on = function(name, func){
		
		if(typeof func !== "function")
			throw new Error("settings api error - second parameter of event: "+name+"should be a function");
			
		g_frontAPI.onEvent(name, function(event, data){
			
			if(!data)
				return(false);
			
			var model = data.model;
			
			if(!model)
				return(false);
						
			if(model.id != g_id)
				return(false);
						
			var attributes = model.settings.attributes;
			
			//run the event function
			func(attributes);
			
		});
		
	}
	
	
	/**
	 * init the api
	 */
	this.init = function(objWidget){
		
		if(!objWidget || objWidget.length == 0)
			return(false);
		
		var objParent = objWidget.parents(".elementor-element.elementor-element-edit-mode");
		
		if(objParent.length == 0)
			throw new Error("settings api error - parent element not found");
		
		var elementType = objParent.data("element_type");
		
		g_id = objParent.data("id");
		
		if(elementType != "widget")
			throw new Error("settings api error - wrong element type");
		
		if(!window.g_ueSettingsAPI)
			throw new Error("settings api error - main api not inited");
		
		g_frontAPI = window.g_ueSettingsAPI;
		
	}
	
	
	
}

/**
 * get editor api by id
 */
function ueGetEditorSettingsAPI(widgetID){
	
	var objWidget = jQuery("#"+widgetID);
	
	if(objWidget.length == 0)
		throw new Error("settings api error, no widget found by id: "+widgetID);
	
	var objWidgetAPI = new UnlimitedElementsWidgetSettingsAPI();
	
	objWidgetAPI.init(objWidget);
	
	return(objWidgetAPI);
}



function ucDocReady(fn) {
    // see if DOM is already available
    if (document.readyState === "complete" || document.readyState === "interactive") {
        // call on next available tick
        setTimeout(fn, 1);
    } else {
        document.addEventListener("DOMContentLoaded", fn);
    }
}    


ucDocReady(function(){
	
	window.parent.g_objUCElementorEditorAdmin.initFrontEndInteraction(window, elementorFrontend);
	
});

