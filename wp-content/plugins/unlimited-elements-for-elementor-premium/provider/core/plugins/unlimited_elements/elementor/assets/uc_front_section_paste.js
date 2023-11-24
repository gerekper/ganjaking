function UnlimitedElementsPasteSection(){
	
	var g_options = {};
	var g_objPupup, g_objButton, g_objInput, g_objLoader, g_objDebug;
	
	
	/**
	 * put paste html
	 */
	function putPasteHtml(){
		
		var html = "";
		html += "<div id='uc_paste_section_popup' class='uc-section-paste-popup'>";
		html += "	<div class='uc-section-paste-debug' style='display:none'>debug</div>";
		html += "	<input type='text' class='uc-section-paste-input'>";
		html += "	<a href='javascript:void(0)' class='uc-section-paste-button'>Paste Section</a>";
		html += "	<div class='uc-section-paste-loader' style='display:none'>Pasting, please wait...</div>";
		
		html += "</div>";
		
		jQuery("body").append(html);
	}
	
	/**
	 * show ajax error
	 */
	function showAjaxError(error){
		
		g_objDebug.html(error);
		g_objDebug.show();
		
		//alert(error);
	}
	
	/**
	 * small ajax request
	 */
	function ajaxRequest(action, objData, onSuccess){
				
		if(!objData)
			var objData = {};
		
		if(typeof objData != "object")
			throw new Error("wrong ajax param");
		
		var ajaxUrl = g_options.ajax_url;
		
		var ajaxData = {};
		ajaxData["action"] = "unlimitedelements_ajax_action";
		ajaxData["client_action"] = action;
		ajaxData["data"] = objData;
		
		
		var ajaxOptions = {
				type:"post",
				url:ajaxUrl,
				dataType: 'json',
				data:ajaxData,
				success:function(response){
					
					if(!response){
						showAjaxError("Empty ajax response!");
						return(false);					
					}
					
					if(typeof response != "object"){
						
						try{
							response = jQuery.parseJSON(response);
						}catch(e){
							showAjaxError("Ajax Error!!! not ajax response");
							return(false);
						}
					}
					
					if(response == -1){
						showAjaxError("ajax error!!!");
						return(false);
					}
					
					if(response == 0){
						showAjaxError("ajax error, action: <b>"+action+"</b> not found");
						return(false);
					}
					
					if(response.success == undefined){
						showAjaxError("The 'success' param is a must!");
						return(false);
					}
					
					if(response.success == false){
						showAjaxError(response.message);
						return(false);
					}
					
					if(typeof onSuccess == "function")
						onSuccess(response);
					
				},
				error:function(jqXHR, textStatus, errorThrown){
					
					switch(textStatus){
						case "parsererror":
						case "error":
							showAjaxError(jqXHR.responseText);
						break;
					}
				}
		}
		
		
		jQuery.ajax(ajaxOptions);
		
	}
	
	/**
	 * on paste button click
	 */
	function onPasteButtonClick(){
		
		var postID = g_options.post_id;
		
		if(!postID)
			throw new Error("missing target post id");
		
		g_objLoader.show();
		
		var strCopyData = g_objInput.val();
		
		var objData = {};
		objData["params_data"] = strCopyData;
		objData["targetid"] = postID;
		
		ajaxRequest("paste_section_front", objData, function(){
			
			location.reload();
			
		});
		
	}
	
	/**
	 * init events
	 */
	function initEvents(){
		
		g_objButton.click(onPasteButtonClick);
		
	}
	
	/**
	 * paste section init
	 */
	this.init = function(){
		
		if(typeof g_ucPasteSectionConfig == "undefined"){
			console.log("paste section error - no config found");
			return(false);
		}
				
		g_options = JSON.parse(g_ucPasteSectionConfig);
				
		putPasteHtml();
		
		g_objPopup = jQuery("#uc_paste_section_popup");
		
		if(g_objPopup.length == 0){
			console.log("paste section not created well");
			return(false);
		}
		
		g_objButton = g_objPopup.find(".uc-section-paste-button");
		g_objLoader = g_objPopup.find(".uc-section-paste-loader");
		g_objInput = g_objPopup.find(".uc-section-paste-input");
		g_objDebug = g_objPopup.find(".uc-section-paste-debug");
		
		initEvents();
	}
	
}


jQuery(document).ready(function(){
			
	var objPasteSection = new UnlimitedElementsPasteSection();
	objPasteSection.init();
	
});

