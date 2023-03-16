
function UCWooIntegrate(){
	
	var t = this;
	
	/**
	 * print in console
	 */
	function trace(str){
		
		console.log(str);
		
	}
	
	/**
	 * filter change
	 */
	function onSelectFilterChange(){
		
		var objSelect = jQuery(this);
		var objForm = objSelect.parents("form");
		
		objForm.submit();
		
	}
	
	
	/**
	 * init the object
	 */
	this.init = function(){
		
		var objFilterSelects = jQuery("select.uc-woo-filter");
				
		objFilterSelects.on("change", onSelectFilterChange);
		
	}
	
}


jQuery(document).ready(function(){
		
	var objUCWoo = new UCWooIntegrate();
	objUCWoo.init();
		
});
