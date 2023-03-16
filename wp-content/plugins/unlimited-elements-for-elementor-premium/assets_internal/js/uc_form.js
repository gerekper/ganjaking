"use strict";

function UniteCreatorFormFront(){
	
	var g_objLoader, g_objError, g_objSuccess, g_objButton, g_objFormContent;
	
	
	/**
	 * trace
	 */
	function trace(str){
		console.log(str);
	}
	
	/**
	 * get parent form
	 */
	function getParentForm(objChild){
		var objForm = objChild.parents(".uc-form");
		
		if(objForm.length == 0){
			trace(objChild);
			throw new Error("Form not found from child object");
		}
		
		return(objForm);
	}
	
	/**
	 * get form data
	 */
	function getFormData(objForm){
		
		var arrFields = objForm.find("input,textarea,select");
		
		var arrData = [];
		jQuery.each(arrFields, function(index, input){
			var objInput = jQuery(input);
			var inputData = {};
			
			var type = objInput.attr("type");
			if(type == "submit")
				return(true);
			
			var isRequired = objInput.data("required");
			
			inputData["name"] = objInput.prop("name");
			inputData["value"] = objInput.val();
			
			if(isRequired)
				inputData["required"] = true;
						
			var title = objInput.data("title");
			if(!title)
				title = "";
			inputData["title"] = title;
			
			arrData.push(inputData);
		});
		
		return(arrData);
	}
	
	/**
	 * set loading state
	 */
	function setStateLoading(){
		
		if(g_objError)
			g_objError.hide();
		
		if(g_objLoader){
			g_objLoader.show();
			g_objButton.hide();
		}
		
	}
	
	/**
	 * set success state
	 */
	function setStateSuccess(){
		
		if(g_objLoader)
			g_objLoader.hide();
		
		g_objButton.show();
		
		if(g_objError)
			g_objError.hide();
		
		if(g_objSuccess){
			g_objFormContent.hide();
			g_objSuccess.show();
		}else{
			alert("Form Sent!");
		}
		
	}
	
	
	/**
	 * set error state
	 */
	function setStateError(message){
		
		if(g_objLoader)
			g_objLoader.hide();
		
		g_objButton.show();
		
		if(g_objError){
			g_objError.show();
			g_objError.html(message);
		}else{
			alert(message);
		}
		
	}
	
	
	/**
	 * process response
	 */
	function processSubmitResponse(response){
		
		if(!response){
			setStateError("Empty ajax response!");
			return(false);					
		}
		
		if(typeof response != "object"){
			
			try{
				response = jQuery.parseJSON(response);
			}catch(e){
				setStateError("Ajax Error!!! not ajax response");
				trace(response);
				return(false);
			}
		}
		
		if(response == -1){
			setStateError("ajax error!!!");
			return(false);
		}
		
		if(response == 0){
			setStateError("ajax error, action not found");
			return(false);
		}
		
		if(typeof response.success === "undefined"){
			setStateError("The 'success' param is a must!");
			return(false);
		}
		
		if(response.success === false){
			setStateError(response.message);
			return(false);
		}
		
		//success!!!
		
		setStateSuccess();
		
	}
	
	
	/**
	 * on form submit
	 */
	function onSubmitClick(event){
		
		event.preventDefault();
		
		var objButton = jQuery(this);
		var objForm = getParentForm(objButton);
				
		var arrFormData = getFormData(objForm);

		
		//set objects
		g_objFormContent = objForm.find(".uc-form-content");
		if(g_objFormContent.length == 0)
			throw new Error("Form content div not found");
		
		g_objButton = objButton;
		if(g_objButton.length == 0)
			throw new Error("Submit button not found");
		
		g_objLoader = objForm.find(".uc-form-loading");
		if(g_objLoader.length == 0)
			g_objLoader = null;
		
		g_objError = objForm.find(".uc-form-error");
		if(g_objError.length == 0)
			g_objError = null;
		
		g_objSuccess = jQuery(".uc-form-success");
		if(g_objSuccess.length == 0)
			g_objSuccess = null;
		
		
		var objData = {};
		objData.action = "bloxbuilder_ajax_action";
		objData.client_action = "send_form";
		objData.form_data = arrFormData;
		
		var ajaxOptions = {
				type:"post",
				url:g_urlFormAjaxUC,
				dataType: 'json',
				data:objData,
				success:function(response){
					
					processSubmitResponse(response);
				},
				error:function(jqXHR, textStatus, errorThrown){
					trace(jqXHR.responseText);
					setStateError("Error Occured!");
				}
		};
		
		setStateLoading();
		
		jQuery.ajax(ajaxOptions);
	}
	
	
	/**
	 * init form
	 */
	function initForm(objForm){
		
		var isInited = objForm.data("inited");
		if(isInited == true)
			return(false);
		
		var objSubmit = objForm.find(".uc-submit-button");
		if(objSubmit.length == 0)
			return(false);
		
		objSubmit.click(onSubmitClick);
		
		objForm.data("inited",true);
	}
	
	
	/**
	 * init the forms
	 */
	this.init = function(){
				
		var objForms = jQuery(".uc-form");
		if(objForms.length == 0)
			return(false);
		
		if(typeof g_urlFormAjaxUC == "undefined")
			throw new Error("ajax url not found");
				
		objForms.each(function(index, form){
			var objForm = jQuery(form);
			initForm(objForm);
		});
		
	};
	
}

