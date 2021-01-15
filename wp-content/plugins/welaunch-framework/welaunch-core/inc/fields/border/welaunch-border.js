/**
 * Field Border (border)
 */

/*global welaunch_change, welaunch, colorValidate */

(function( $ ) {
	'use strict';

	welaunch.field_objects        = welaunch.field_objects || {};
	welaunch.field_objects.border = welaunch.field_objects.border || {};

	welaunch.field_objects.border.init = function( selector ) {
		selector = $.welaunch.getSelector( selector, 'border' );

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

				el.find( '.welaunch-border-top, .welaunch-border-right, .welaunch-border-bottom, .welaunch-border-left, .welaunch-border-all' ).numeric( { allowMinus: false } );
				el.find( '.welaunch-border-style' ).select2();

				el.find( '.welaunch-border-input' ).on(
					'change',
					function() {
						var value;

						var units = $( this ).parents( '.welaunch-field:first' ).find( '.field-units' ).val();

						if ( 0 !== $( this ).parents( '.welaunch-field:first' ).find( '.welaunch-border-units' ).length ) {
							units = $( this ).parents( '.welaunch-field:first' ).find( '.welaunch-border-units option:selected' ).val();
						}

						value = $( this ).val();

						if ( 'undefined' !== typeof units && value ) {
							value += units;
						}

						if ( $( this ).hasClass( 'welaunch-border-all' ) ) {
							$( this ).parents( '.welaunch-field:first' ).find( '.welaunch-border-value' ).each(
								function() {
									$( this ).val( value );
								}
							);
						} else {
							$( '#' + $( this ).attr( 'rel' ) ).val( value );
						}
					}
				);

				el.find( '.welaunch-border-units' ).on(
					'change',
					function() {
						$( this ).parents( '.welaunch-field:first' ).find( '.welaunch-border-input' ).change();
					}
				);

				el.find( '.welaunch-color-init' ).wpColorPicker(
					{
						change: function( e, ui ) {
							$( this ).val( ui.color.toString() );
							welaunch_change( $( this ) );
							el.find( '#' + e.target.getAttribute( 'data-id' ) + '-transparency' ).removeAttr( 'checked' );
						},
						clear: function( e, ui ) {
							e = null;
							$( this ).val( ui.color.toString() );
							welaunch_change( $( this ).parent().find( '.welaunch-color-init' ) );
						}
					}
				);

				el.find( '.welaunch-color' ).on(
					'keyup',
					function() {
						var color = colorValidate( this );

						if ( color && color !== $( this ).val() ) {
							$( this ).val( color );
						}
					}
				);

				// Replace and validate field on blur.
				el.find( '.welaunch-color' ).on(
					'blur',
					function() {
						var value = $( this ).val();

						if ( colorValidate( this ) === value ) {
							if ( 0 !== value.indexOf( '#' ) ) {
								$( this ).val( $( this ).data( 'oldcolor' ) );
							}
						}
					}
				);

				// Store the old valid color on keydown.
				el.find( '.welaunch-color' ).on(
					'keydown',
					function() {
						$( this ).data( 'oldkeypress', $( this ).val() );
					}
				);

			}
		);
	};
})( jQuery );
