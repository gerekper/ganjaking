/* Custom JS starts here */
jQuery(document).ready(function() {

	/**
		custom radio buttons
	**/
	jQuery(document).on('click', '.emd-filters input[type=radio]', function(e){
		var field = jQuery(this).parents('.emd-filter');
		field.find('span').removeClass('checked');
		jQuery(this).parents('label').find('span').addClass('checked');
	});
	
	/**
		custom checkbox buttons
	**/
	jQuery(document).on('change', '.emd-filters input[type=checkbox]', function(e){
		if (jQuery(this).is(':checked')) {
			jQuery(this).parents('label').find('span').addClass('checked');
		} else {
			jQuery(this).parents('label').find('span').removeClass('checked');
		}
	});
	
	/**
	masonry
	**/
	jQuery('.emd-list').imagesLoaded( function(){
		jQuery(this).isotope({
			itemSelector: '.emd-user',
			layoutMode: 'masonry',
		});
	});
	
});