/**
 *  Field Color (color)
 */

/*global jQuery, welaunch_change, welaunch, colorValidate */

(function( $ ) {
	'use strict';

	welaunch.field_objects       = welaunch.field_objects || {};
	welaunch.field_objects.color = welaunch.field_objects.color || {};

	welaunch.field_objects.color.init = function( selector ) {
		selector = $.welaunch.getSelector( selector, 'color' );

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

				el.find( '.welaunch-color-init' ).wpColorPicker(
					{
						change: function( e, ui ) {
							$( this ).val( ui.color.toString() );

							welaunch_change( $( this ) );

							el.find( '#' + e.target.getAttribute( 'data-id' ) + '-transparency' ).removeAttr( 'checked' );
						}, clear: function() {
							$( this ).val( '' );

							welaunch_change( $( this ).parent().find( '.welaunch-color-init' ) );
						}
					}
				);

				el.find( '.welaunch-color' ).on(
					'focus',
					function() {
						$( this ).data( 'oldcolor', $( this ).val() );
					}
				);

				el.find( '.welaunch-color' ).on(
					'keyup',
					function() {
						var value = $( this ).val();
						var color = colorValidate( this );
						var id    = '#' + $( this ).attr( 'id' );

						if ( 'transparent' === value ) {
							$( this ).parent().parent().find( '.wp-color-result' ).css( 'background-color', 'transparent' );

							el.find( id + '-transparency' ).attr( 'checked', 'checked' );
						} else {
							el.find( id + '-transparency' ).removeAttr( 'checked' );

							if ( color && color !== $( this ).val() ) {
								$( this ).val( color );
							}
						}
					}
				);

				// Replace and validate field on blur.
				el.find( '.welaunch-color' ).on(
					'blur',
					function() {
						var value = $( this ).val();
						var id    = '#' + $( this ).attr( 'id' );

						if ( 'transparent' === value ) {
							$( this ).parent().parent().find( '.wp-color-result' ).css( 'background-color', 'transparent' );

							el.find( id + '-transparency' ).attr( 'checked', 'checked' );
						} else {
							if ( colorValidate( this ) === value ) {
								if ( 0 !== value.indexOf( '#' ) ) {
									$( this ).val( $( this ).data( 'oldcolor' ) );
								}
							}

							el.find( id + '-transparency' ).removeAttr( 'checked' );
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

				// When transparency checkbox is clicked.
				el.find( '.color-transparency' ).on(
					'click',
					function() {
						var prevColor;

						if ( $( this ).is( ':checked' ) ) {
							el.find( '.welaunch-saved-color' ).val( $( '#' + $( this ).data( 'id' ) ).val() );
							el.find( '#' + $( this ).data( 'id' ) ).val( 'transparent' );
							el.find( '#' + $( this ).data( 'id' ) ).parents( '.welaunch-field-container' ).find( '.wp-color-result' ).css( 'background-color', 'transparent' );
						} else {
							prevColor =  $( this ).parents( '.welaunch-field-container' ).find( '.welaunch-saved-color' ).val();
							if ( '' === prevColor ) {
								prevColor = $( '#' + $( this ).data( 'id' ) ).data( 'default-color' );
							}
							el.find( '#' + $( this ).data( 'id' ) ).parents( '.welaunch-field-container' ).find( '.wp-color-result' ).css( 'background-color', prevColor );
							el.find( '#' + $( this ).data( 'id' ) ).val( prevColor );
						}

						welaunch_change( $( this ) );
					}
				);
			}
		);
	};
})( jQuery );
