
function UniteCreatorElementorTemplateLibraryAdmin(){
	
	var t = this;
	
	
	/**
	 * put import button
	 */
	function putImportButton(){
		
		var objButton = jQuery("#uc_button_import_layout");
		objButton.remove();
		
		var objButtonCloned = objButton.clone();
		
		var objAdminBarButtonsNew = jQuery(".e-admin-top-bar__main-area-buttons");
		
		if(objAdminBarButtonsNew.length){
			
			objAdminBarButtonsNew.append(objButtonCloned);
			
		}else{
			var objHeaderEnd = jQuery(".wp-header-end");
			objHeaderEnd.before(objButtonCloned);
		}
		
		
		//set event
		objButtonCloned.click(onToggleButtonClick);
	}
	
	
	/**
	 * put import area
	 */
	function putImportArea(){
		
		var objAnchor = jQuery("h1.wp-heading-inline");
		var objForm = jQuery("#uc_import_layout_area");
		var objFormClone = objForm.clone();
		
		objAnchor.after(objFormClone);
	}
	
	/**
	 * toggle import area
	 */
	function onToggleButtonClick(){
		
		jQuery("#uc_import_layout_area").toggle();
		
	}
	
	
	/**
	 * init integration
	 */
	this.init = function(){
		
		setTimeout(putImportButton, 500);
		
		putImportArea();
	}
	
}

jQuery(document).ready(function(){
	
	var objAdmin = new UniteCreatorElementorTemplateLibraryAdmin();
	
	objAdmin.init();
});