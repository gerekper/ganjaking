/* Ajax-Layerd Nav Widgets 
 * Shopping Cart: WooCommerce
 * File: Admin JS 
 * License: GPL
 * Copyright: SixtyOneDesigns 
 */

/*	Event: document.ready
 *  	Add Live Handlers to:
 * 			1. Attribute Values dropdown 
 * 			2. Layered Nav Type dropdown
 * 			3. Colorpickers
 */
jQuery(document).ready(function(){
	/* Populate attribute values on attribute change*/ 
	jQuery(document).on('change','.layered_nav_attributes', function(){
		
		var $type = jQuery(this).parent().parent().find('.layered_nav_type');
		
		var attr_name = jQuery(this).parent().parent().find('.layered_nav_attributes').val();
		var id = jQuery(this).parent().parent().next('.widget-id').val();
		var target = 'widget-'+id+'-labels';
		if (jQuery(" option:selected").length){
			jQuery('#'+target).empty().addClass('spinner');
			jQuery.post(  
	            site.siteurl+"/wp-admin/admin-ajax.php",  
	            //Data  
	            {  
	                action:"set_type",  
	                'cookie': encodeURIComponent(document.cookie),  
	                'attr_name':attr_name,
	                'type':$type.val(),
	                'id':id,
	                'sod_ajax_layered_nonce':site.sod_ajax_layered_nonce
	               },  
	            //on success function 
	            function(output){  
	            	jQuery('#'+target).html(output).removeClass('spinner');  
	               	return false;  
	            } 
	        );
		};
	});
	/* Sets layered nav display type*/
	jQuery(document).on('change','.layered_nav_type', function(){
		Othis = jQuery(this); 
		var attr_name = jQuery(this).parent().parent().find('.layered_nav_attributes').val();
		var id = jQuery(this).parent().parent().next('.widget-id').val();
		var target = 'widget-'+id+'-labels';
		jQuery('#'+target).empty().addClass('spinner');
		jQuery.post(  
            site.siteurl+"/wp-admin/admin-ajax.php",  
            //Data  
            {  
                action:"set_type",  
                'cookie': encodeURIComponent(document.cookie),  
                'attr_name':attr_name,
                'type':Othis.val(),
                'id':id,
                'sod_ajax_layered_nonce':site.sod_ajax_layered_nonce
               },  
            //on success function  
            function(output){  
            	
               	jQuery('#'+target).html(output).removeClass('spinner');  
               	return false;  
            } 
        );  
	});
	/* Show color picker on focusin*/
	jQuery(document).on('focusin','.color_input', function () {
		jQuery(this).showColorPicker();
	});
});

/*	Function: showColorPicker()
 *	Shows jquery UI color picker and updates adjacent input box with picker hex value 
 */
 jQuery.fn.showColorPicker = function() {
	var Othis = jQuery(this[0]); //cache a copy of the this variable for use inside nested function
	var initialColor = jQuery(Othis).next('input').attr('value');
	jQuery(this).ColorPicker({
		color: initialColor,
		onShow: function (colpkr) {
			jQuery(colpkr).fadeIn(500).css( 'zIndex', 999999 );
			return false;
		},
		onHide: function (colpkr) {
			jQuery(colpkr).fadeOut(500);
			return false;
		},
		onChange: function (hsb, hex, rgb) {
			jQuery(Othis).parent().find('.colorSelector div').css('backgroundColor', '#' + hex);
			jQuery(Othis).attr('value','#' + hex);
		}
	});
};
/**
 *
 * Zoomimage
 * Author: Stefan Petre www.eyecon.ro
 * required for colorpicker to show-up
 */
(function($){
	var EYE = window.EYE = function() {
		var _registered = {
			init: []
		};
		return {
			init: function() {
				$.each(_registered.init, function(nr, fn){
					fn.call();
				});
			},
			extend: function(prop) {
				for (var i in prop) {
					if (prop[i] != undefined) {
						this[i] = prop[i];
					}
				}
			},
			register: function(fn, type) {
				if (!_registered[type]) {
					_registered[type] = [];
				}
				_registered[type].push(fn);
			}
		};
	}();
	$(EYE.init);
})(jQuery);