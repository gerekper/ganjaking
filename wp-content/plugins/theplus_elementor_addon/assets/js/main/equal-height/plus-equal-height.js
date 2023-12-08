/*equal height*/
(function($) {

	$.fn.equalHeights = function() {
		var max_height = 0;

		jQuery(this).each(function() {
			max_height = Math.max(jQuery(this).outerHeight(), max_height);
		});

		jQuery(this).each(function() {
			jQuery(this).css('min-height', max_height);
		});
	};

	jQuery(document).ready(function() {
		EqualHeightsLoadded()
	});

	jQuery(window).on("load resize",function() {
		EqualHeightsLoadded()
	});

}(jQuery));

/**Equal Height*/
function EqualHeightsLoadded(){
	var container = jQuery('.elementor-element[data-tp-equal-height-loadded]');
	if( container.length > 0 ){
		container.each(function() {
			var id = jQuery(this).data('id'),
				new_find = jQuery(this).data('tp-equal-height-loadded');

				jQuery(`.elementor-element-${id} ${new_find}`).equalHeights();
		});	
	}
}
