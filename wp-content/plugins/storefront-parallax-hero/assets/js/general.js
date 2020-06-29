jQuery(document).ready(function($){
	if ( $( window ).width() >= 768 || $( 'body' ).hasClass( 'sph-do-mobile' ) ) {
		var offset = $( '.site-main' ).offset();

		/**
		 * Apply a negative margin to the parallax hero
		 * When using the full width layout, the hero element needs to break outside of .site-main.
		 * So we calculate the distance between .site-main and the edge of the browser window and
		 * apply a negative left/right margin to the hero component equal to that distance.
		 */
		jQuery( '.page-template-template-homepage .site-main > .sph-hero.full, .page-template-template-fullwidth-php .entry-content .sph-hero.full, .storefront-full-width-content .site-main .sph-hero.full' ).css({
			'margin-left':  -offset.left,
			'margin-right': -offset.left,
		});

		/**
		 * Do the magic on resize as well
		 */
		jQuery(window).resize( function() {
			var offset = $( '.site-main' ).offset();

			jQuery( '.page-template-template-homepage .site-main > .sph-hero.full, .page-template-template-fullwidth-php .entry-content .sph-hero.full, .storefront-full-width-content .site-main .sph-hero.full' ).css({
				'margin-left':  -offset.left,
				'margin-right': -offset.left,
			});
		});

		// Add the calculated heroHeight as a min-height
		var heroContentHeight = jQuery( '.sph-hero .sph-inner' ).height();
		jQuery( '.sph-hero .sph-inner-wrapper' ).css( 'min-height', heroContentHeight );
	}
});