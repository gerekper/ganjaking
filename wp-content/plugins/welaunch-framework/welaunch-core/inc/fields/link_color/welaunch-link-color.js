/*
 * Field Link Color
 */

/*global jQuery, welaunch_change, welaunch, colorValidate */

(function( $ ) {
	'use strict';

	welaunch.field_objects            = welaunch.field_objects || {};
	welaunch.field_objects.link_color = welaunch.field_objects.link_color || {};

	welaunch.field_objects.link_color.init = function( selector ) {
		selector = $.welaunch.getSelector( selector, 'link_color' );

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
						}, clear: function( e, ui ) {
							e = null;
							$( this ).val( ui.color.toString() );

							welaunch_change( $( this ).parent().find( '.welaunch-color-init' ) );
						}
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
			}
		);
	};
})( jQuery );
