/* global welaunch */

(function( $ ) {
	'use strict';

	welaunch.field_objects         = welaunch.field_objects || {};
	welaunch.field_objects.spinner = welaunch.field_objects.spinner || {};

	welaunch.field_objects.spinner.init = function( selector ) {
		selector = $.welaunch.getSelector( selector, 'spinner' );

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

				el.find( '.welaunch_spinner' ).each(
					function() {

						// Slider init.
						var spinner = $( this ).find( '.spinner-input' ).data();

						spinner.id = $( this ).find( '.spinner-input' ).attr( 'id' );

						el.find( '#' + spinner.id ).spinner(
							{
								value:      parseFloat( spinner.val, null ),
								min:        parseFloat( spinner.min, null ),
								max:        parseFloat( spinner.max, null ),
								step:       parseFloat( spinner.step, null ),
								addText:    spinner.plus,
								subText:    spinner.minus,
								prefix:     spinner.prefix,
								suffix:     spinner.suffix,
								places:     spinner.places,
								point:      spinner.point
							}
						);
					}
				);
			}
		);
	};
})( jQuery );
