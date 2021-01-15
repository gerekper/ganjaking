/**
 * Field Button Set (button_set)
 */

/*global jQuery, welaunch, welaunch_change */

(function( $ ) {
	'use strict';

	welaunch.field_objects            = welaunch.field_objects || {};
	welaunch.field_objects.button_set = welaunch.field_objects.button_set || {};

	welaunch.field_objects.button_set.init = function( selector ) {
		selector = $.welaunch.getSelector( selector, 'button_set' );

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
						if ( $( this ).is( ':checkbox' ) ) {
							$( this ).find( '.buttonset-item' ).button();
						}

						$( this ).buttonset();
					}
				);

				el.find( '.buttonset-item.multi' ).on(
					'click',
					function() {
						var val       = '';
						var name      = '';
						var id        = $( this ).attr( 'id' );
						var empty     = $( this ).parent().find( '.buttonset-empty' );
						var idName    = empty.attr( 'data-name' );
						var isChecked = false;

						$( this ).parent().find( '.buttonset-item' ).each(
							function() {
								if ( $( this ).is( ':checked' ) ) {
									isChecked = true;
								}
							}
						);

						if ( isChecked ) {
							empty.attr( 'name', '' );
						} else {
							empty.attr( 'name', idName );
						}

						if ( $( this ).is( ':checked' ) ) {
							val  = $( this ).attr( 'data-val' );
							name = idName + '[]';

						}

						$( this ).parent().find( '#' + id + '-hidden.buttonset-check' ).val( val );
						$( this ).parent().find( '#' + id + '-hidden.buttonset-check' ).attr( 'name', name );

						welaunch_change( $( this ) );
					}
				);
			}
		);
	};
})( jQuery );
