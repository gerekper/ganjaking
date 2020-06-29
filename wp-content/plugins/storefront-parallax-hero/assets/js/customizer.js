/**
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 */

( function( $ ) {
	wp.customize( 'sph_hero_text_color', function( value ) {
		value.bind( function( to ) {
			$( '.sph-hero-content' ).css( 'color', to );
		} );
	} );

	wp.customize( 'sph_background_color', function( value ) {
		value.bind( function( to ) {
			$( '.sph-hero' ).css( 'background-color', to );
		} );
	} );
} )( jQuery );