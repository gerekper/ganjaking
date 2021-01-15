/**
 * weLaunch Checkbox
 * Dependencies        : jquery
 * Feature added by    : Dovy Paukstys
 * Date                : 17 June 2014
 */

/*global welaunch_change, welaunch*/

(function( $ ) {
	'use strict';

	welaunch.field_objects          = welaunch.field_objects || {};
	welaunch.field_objects.checkbox = welaunch.field_objects.checkbox || {};

	welaunch.field_objects.checkbox.init = function( selector ) {
		selector = $.welaunch.getSelector( selector, 'checkbox' );

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

				el.find( '.checkbox' ).on(
					'click',
					function() {
						var val = 0;

						if ( $( this ).is( ':checked' ) ) {
							val = $( this ).parent().find( '.checkbox-check' ).attr( 'data-val' );
						}

						$( this ).parent().find( '.checkbox-check' ).val( val );

						welaunch_change( $( this ) );
					}
				);
			}
		);
	};
})( jQuery );
