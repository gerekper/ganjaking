/* global confirm, redux, redux_change */

jQuery(document).ready(function() {
	jQuery('.has_settings').each(function(){
		var attr = jQuery(this).attr('data-settings-id');
		if (typeof attr !== typeof undefined && attr !== false) {
		 	jQuery(this).find('.gt3_header_builder__setting-icon').on('click',function(){
		 		if (jQuery(this).parent().parent('.gt3_header_builder__side').hasClass('empty_section')) {
		 			jQuery('#no_item-start').addClass('showSettings');
		 		}else{
		  			jQuery('#'+attr+'-start').addClass('showSettings');
		 		}
		  	})
		}
	})
	jQuery('.gt3_section__close-icon').each(function(){
		jQuery(this).on('click',function(){
			jQuery(this).parents('.gt3_section_container').removeClass('showSettings');
		})
	})
	jQuery('.gt3_section_container .gt3_section_container__cover').each(function(){
		jQuery(this).on('click',function(){
			jQuery(this).parents('.gt3_section_container').removeClass('showSettings');
		})
	})

	jQuery('.gt3_section_container .form-table > tbody > tr').each(function(){
		jQuery(this).attr('data-field-id',jQuery(this).find('fieldset').attr('data-id'))
	});


	jQuery('.gt3_header_builder__side.tablet .logo_container').each(function(){
		var image_url = jQuery('fieldset[data-id="logo_tablet"]').find('.upload-thumbnail').val();
		if (image_url.length) {
			jQuery(this).css("cssText", "background-image: url("+image_url+") !important");
		}		
	})

	jQuery('.gt3_header_builder__side.mobile .logo_container').each(function(){
		var image_tablet_url = jQuery('fieldset[data-id="logo_tablet"]').find('.upload-thumbnail').val();
		var image_url = jQuery('fieldset[data-id="logo_mobile"]').find('.upload-thumbnail').val();
		if (image_url.length) {
			jQuery(this).css("cssText", "background-image: url("+image_url+") !important");
		}else if(image_tablet_url.length){
			jQuery(this).css("cssText", "background-image: url("+image_tablet_url+") !important");
		}		
	})

});
