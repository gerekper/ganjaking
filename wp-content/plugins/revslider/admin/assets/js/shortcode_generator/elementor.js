/*
	Elementor RevSlider Extension
*/
jQuery(function() {
	
	function openObjectLibrary() {
		
		if(RS_SC_WIZARD.suppress) return;
		
		RS_SC_WIZARD.elementor_button = jQuery(this);
		RS_SC_WIZARD.openTemplateLibrary('elementor');
		
	}
	
	window.elementorSelectRevSlider = function(e) {
		
		if(e) openObjectLibrary.call(this);
		else jQuery('button[data-event="themepunch.selectslider"]').click();
		
	}
	
	jQuery('body').on('click', 'button[data-event="themepunch.selectslider"]', elementorSelectRevSlider);
	
});