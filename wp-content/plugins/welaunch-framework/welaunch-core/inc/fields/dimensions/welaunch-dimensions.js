/* global jQuery, welaunch */

(function( $ ) {
	'use strict';

	welaunch.field_objects            = welaunch.field_objects || {};
	welaunch.field_objects.dimensions = welaunch.field_objects.dimensions || {};

	welaunch.field_objects.dimensions.init = function( selector ) {
		selector = $.welaunch.getSelector( selector, 'dimensions' );

		$( selector ).each(
			function() {
				var el     = $( this );
				var parent = el;

				if ( ! el.hasClass( 'welaunch-field-container' ) ) {
					parent = el.parents( '.welaunch-field-container:first' );
				}

				if ( parent.is( ':hidden' ) ) {
					return;
				}

				if ( parent.hasClass( 'welaunch-field-init' ) ) {
					parent.removeClass( 'welaunch-field-init' );
				} else {
					return;
				}

				el.find( '.welaunch-dimensions-units' ).select2();

				el.find( '.welaunch-dimensions-input' ).on(
					'change',
					function() {
						var units = $( this ).parents( '.welaunch-field:first' ).find( '.field-units' ).val();
						if ( 0 !== $( this ).parents( '.welaunch-field:first' ).find( '.welaunch-dimensions-units' ).length ) {
							units = $( this ).parents( '.welaunch-field:first' ).find( '.welaunch-dimensions-units option:selected' ).val();
						}
						if ( 'undefined' !== typeof units ) {
							el.find( '#' + $( this ).attr( 'rel' ) ).val( $( this ).val() + units );
						} else {
							el.find( '#' + $( this ).attr( 'rel' ) ).val( $( this ).val() );
						}
					}
				);

				el.find( '.welaunch-dimensions-units' ).on(
					'change',
					function() {
						$( this ).parents( '.welaunch-field:first' ).find( '.welaunch-dimensions-input' ).change();
					}
				);
			}
		);
	};
})( jQuery );
