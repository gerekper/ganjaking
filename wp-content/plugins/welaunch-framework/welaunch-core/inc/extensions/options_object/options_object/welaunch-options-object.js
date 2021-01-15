/*global welaunch, jsonView */

(function( $ ) {
	welaunch.field_objects                = welaunch.field_objects || {};
	welaunch.field_objects.options_object = welaunch.field_objects.options_object || {};

	welaunch.field_objects.options_object.init = function( selector ) {
		var parent;

		selector = $.welaunch.getSelector( selector, 'options_object' );

		parent = selector;

		if ( ! selector.hasClass( 'welaunch-field-container' ) ) {
			parent = selector.parents( '.welaunch-field-container:first' );
		}

		if ( parent.hasClass( 'welaunch-field-init' ) ) {
			parent.removeClass( 'welaunch-field-init' );
		} else {
			return;
		}

		$( '#consolePrintObject' ).on(
			'click',
			function( e ) {
				e.preventDefault();
				console.log( $.parseJSON( $( '#welaunch-object-json' ).html() ) );
			}
		);

		if ( 'function' === typeof jsonView ) {
			jsonView( '#welaunch-object-json', '#welaunch-object-browser' );
		}
	};
})( jQuery );
