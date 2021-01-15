/*global welaunch_change, welaunch*/

/**
 * Switch
 * Dependencies        : jquery
 * Feature added by    : Smartik - http://smartik.ws/
 * Date            : 03.17.2013
 */

(function( $ ) {
	'use strict';

	welaunch.field_objects        = welaunch.field_objects || {};
	welaunch.field_objects.switch = welaunch.field_objects.switch || {};

	welaunch.field_objects.switch.init = function( selector ) {
		selector = $.welaunch.getSelector( selector, 'switch' );

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

				el.find( '.cb-enable' ).click(
					function() {
						var parent;
						var obj;
						var $fold;

						if ( $( this ).hasClass( 'selected' ) ) {
							return;
						}

						parent = $( this ).parents( '.switch-options' );

						$( '.cb-disable', parent ).removeClass( 'selected' );
						$( this ).addClass( 'selected' );
						$( '.checkbox-input', parent ).val( 1 ).trigger( 'change' );

						welaunch_change( $( '.checkbox-input', parent ) );

						// Fold/unfold related options.
						obj   = $( this );
						$fold = '.f_' + obj.data( 'id' );

						el.find( $fold ).slideDown( 'normal', 'swing' );
					}
				);

				el.find( '.cb-disable' ).click(
					function() {
						var parent;
						var obj;
						var $fold;

						if ( $( this ).hasClass( 'selected' ) ) {
							return;
						}

						parent = $( this ).parents( '.switch-options' );

						$( '.cb-enable', parent ).removeClass( 'selected' );
						$( this ).addClass( 'selected' );
						$( '.checkbox-input', parent ).val( 0 ).trigger( 'change' );

						welaunch_change( $( '.checkbox-input', parent ) );

						// Fold/unfold related options.
						obj   = $( this );
						$fold = '.f_' + obj.data( 'id' );

						el.find( $fold ).slideUp( 'normal', 'swing' );
					}
				);

				el.find( '.cb-enable span, .cb-disable span' ).find().attr( 'unselectable', 'on' );
			}
		);
	};
})( jQuery );
