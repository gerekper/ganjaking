/*global welaunch*/

(function( $ ) {
	'use strict';

	welaunch.field_objects              = welaunch.field_objects || {};
	welaunch.field_objects.select_image = welaunch.field_objects.select_image || {};

	welaunch.field_objects.select_image.init = function( selector ) {
		selector = $.welaunch.getSelector( selector, 'select_image' );

		$( selector ).each(
			function() {
				var value;
				var preview;

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

				el.find( 'select.welaunch-select-images' ).select2();

				value   = el.find( 'select.welaunch-select-images' ).val();
				preview = el.find( 'select.welaunch-select-images' ).parents( '.welaunch-field:first' ).find( '.welaunch-preview-image' );

				preview.attr( 'src', value );

				el.find( '.welaunch-select-images' ).on(
					'change',
					function() {
						var preview = $( this ).parents( '.welaunch-field:first' ).find( '.welaunch-preview-image' );

						if ( '' === $( this ).val() ) {
							preview.fadeOut(
								'medium',
								function() {
									preview.attr( 'src', '' );
								}
							);
						} else {
							preview.attr( 'src', $( this ).val() );
							preview.fadeIn().css( 'visibility', 'visible' );
						}
					}
				);
			}
		);
	};
})( jQuery );
