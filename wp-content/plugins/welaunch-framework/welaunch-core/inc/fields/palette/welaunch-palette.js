/**
 * Field Palette (color)
 */

/*global jQuery, welaunch*/

(function( $ ) {
	'use strict';

	welaunch.field_objects         = welaunch.field_objects || {};
	welaunch.field_objects.palette = welaunch.field_objects.palette || {};

	welaunch.field_objects.palette.init = function( selector ) {
		selector = $.welaunch.getSelector( selector, 'palette' );

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

				el.find( '.buttonset' ).each(
					function() {
						$( this ).buttonset();
					}
				);
			}
		);
	};
})( jQuery );
