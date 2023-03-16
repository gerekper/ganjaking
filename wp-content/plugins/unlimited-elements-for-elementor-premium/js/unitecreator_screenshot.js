"use strict";

function UCScreenshot(){
	
	var t = this;
	var g_pageBuilder = null;
	var g_ucAdminTop = null;
	
	var g_temp = {
			isInsideIframe: false,
			allowLogging: false
	};
	
	
	/**
	 * trigger end event
	 */
	function triggerEndEvent(response){
		
		if(g_pageBuilder){
			g_pageBuilder.triggerEvent("screenshot_saved", response);
			return(true);
		}
		
		if(g_ucAdminTop)
			g_ucAdminTop.triggerEvent("screenshot_saved", response);
		
	}
	
	
	/**
	 * save data - send ajax request to save
	 */
	function saveScreenshotData(data, ext){
		
		writeLog("done screenshot, saving " + ext);
		
		jQuery("body").append("<div id='div_debug'></div>");
		
		var data = {
			screenshot_data:data,
			source:"layout",
			layoutid:g_layoutID,
			ext:ext
		};
		
		g_ucAdmin.ajaxRequest("save_screenshot", data, function(response){
			
			writeLog("file saved");
			
			triggerEndEvent(response);
		});
		
		
	}
	
	/**
	 * write log
	 */
	function writeLog(str){
		
		if(g_temp.allowLogging == false)
			return(false);
		
		trace(str);
	}
	
	
	/**
	 * take screenshot
	 */
	this.takeScreenshot = function(){
		
		writeLog("start screenshot");
		
		var scale = window.devicePixelRatio * 0.5;
		
		var objBody = jQuery("body");
		var bodyHeight = objBody.height();
		var bodyWidth = objBody.width();
				
		var maxHeight = bodyWidth*3;
		
		var options = {
					logging:false,
					scale:scale
				};
		
		if(bodyHeight > maxHeight)
			options["height"] = maxHeight;
				
		html2canvas(document.body, options).then(canvas => {
						
			var dataJpg = canvas.toDataURL("image/jpeg",0.7);
			var dataPng = canvas.toDataURL("image/png");
			
			//document.body.innerHTML = "";
			//document.body.appendChild(canvas)
			
			if(dataJpg.length < dataPng.length)
				saveScreenshotData(dataJpg, "jpg");
			else
				saveScreenshotData(dataPng, "png");
						
		});
				
	};
	
	/**
	 * init page builder
	 */
	function initTopObjects(){
				
		if(!window.top)
			return(false);
		
		if(window.top.g_objPageBuilder){
			g_pageBuilder = window.top.g_objPageBuilder;
		}
		
		g_ucAdminTop = window.top.g_ucAdmin;
		if(!g_ucAdminTop)
			g_ucAdminTop = null;
		
	}
	
	
	/**
	 * init
	 */
	this.init = function(){
		
		initTopObjects();
		
		if(window.top){
			g_temp.isInsideIframe = true;
		}else{
			
			g_temp.allowLogging = true;
			
		}
				
	}
	
	
}

jQuery(document).ready(function(){
	
	var objScreenshot = new UCScreenshot();
	objScreenshot.init();
	objScreenshot.takeScreenshot();
	
});
