jQuery(document).ready(function(){
	if ( jQuery( window ).width() >= 768 || jQuery( 'body' ).hasClass( 'sph-do-mobile' ) ) {
		// The hero component height on full width mode
		// Calculated by measuring from the top of the hero to the bottom of the browser window.
		var heroHeight 		= jQuery( window ).height() - jQuery( '.sph-hero .overlay' ).offset().top;

		// Add the calculated heroHeight as a min-height
		jQuery( '.sph-hero .overlay' ).css({
			'min-height': heroHeight,
		});
	}
});