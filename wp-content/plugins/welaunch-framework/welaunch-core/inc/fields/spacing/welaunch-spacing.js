/*global welaunch*/

(function( $ ) {
	'use strict';

	welaunch.field_objects         = welaunch.field_objects || {};
	welaunch.field_objects.spacing = welaunch.field_objects.spacing || {};

	welaunch.field_objects.spacing.init = function( selector ) {
		selector = $.welaunch.getSelector( selector, 'spacing' );

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

				el.find( '.welaunch-spacing-units' ).select2();

				el.find( '.welaunch-spacing-input' ).on(
					'change',
					function() {
						var value;

						var units = $( this ).parents( '.welaunch-field:first' ).find( '.field-units' ).val();

						if ( 0 !== $( this ).parents( '.welaunch-field:first' ).find( '.welaunch-spacing-units' ).length ) {
							units = $( this ).parents( '.welaunch-field:first' ).find( '.welaunch-spacing-units option:selected' ).val();
						}

						value = $( this ).val();

						if ( 'undefined' !== typeof units && value ) {
							value += units;
						}

						if ( $( this ).hasClass( 'welaunch-spacing-all' ) ) {
							$( this ).parents( '.welaunch-field:first' ).find( '.welaunch-spacing-value' ).each(
								function() {
									$( this ).val( value );
								}
							);
						} else {
							$( '#' + $( this ).attr( 'rel' ) ).val( value );
						}
					}
				);

				el.find( '.welaunch-spacing-units' ).on(
					'change',
					function() {
						$( this ).parents( '.welaunch-field:first' ).find( '.welaunch-spacing-input' ).change();

						el.find( '.field-units' ).val( $( this ).val() );
					}
				);
			}
		);
	};
})( jQuery );
