/*global jQuery, welaunch, welaunch_change, ace */

( function( $ ) {
	'use strict';

	welaunch.field_objects            = welaunch.field_objects || {};
	welaunch.field_objects.ace_editor = welaunch.field_objects.ace_editor || {};

	welaunch.field_objects.ace_editor.init = function( selector ) {
		selector = $.welaunch.getSelector( selector, 'ace_editor' );

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

				el.find( '.ace-editor' ).each(
					function( index, element ) {
						var area      = element;
						var params    = JSON.parse( $( this ).parent().find( '.localize_data' ).val() );
						var editor    = $( element ).attr( 'data-editor' );
						var aceeditor = ace.edit( editor );
						var id        = '';

						index = null;

						aceeditor.setTheme( 'ace/theme/' + jQuery( element ).attr( 'data-theme' ) );
						aceeditor.getSession().setMode( 'ace/mode/' + $( element ).attr( 'data-mode' ) );

						if ( el.hasClass( 'welaunch-field-container' ) ) {
							id = el.attr( 'data-id' );
						} else {
							id = el.parents( '.welaunch-field-container:first' ).attr( 'data-id' );
						}

						aceeditor.setOptions( params );
						aceeditor.on(
							'change',
							function() {
								$( '#' + area.id ).val( aceeditor.getSession().getValue() );
								welaunch_change( $( element ) );
								aceeditor.resize();
							}
						);
					}
				);
			}
		);
	};
})( jQuery );
