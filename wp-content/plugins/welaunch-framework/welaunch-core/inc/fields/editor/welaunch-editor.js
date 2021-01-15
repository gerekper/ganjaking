/**
 * weLaunch Editor on change callback
 * Dependencies        : jquery
 * Feature added by    : Dovy Paukstys
 *                     : Kevin Provance (who helped)  :P
 * Date                : 07 June 2014
 */

/*global welaunch_change, tinymce, welaunch*/

(function( $ ) {
	'use strict';

	welaunch.field_objects        = welaunch.field_objects || {};
	welaunch.field_objects.editor = welaunch.field_objects.editor || {};

	welaunch.field_objects.editor.init = function() {
		var i;
		var len;

		setTimeout(
			function() {
				if ( 'undefined' !== typeof ( tinymce ) ) {
					len = tinymce.editors.length;

					for ( i = 0; i < len; i += 1 ) {
						welaunch.field_objects.editor.onChange( i );
					}
				}
			},
			1000
		);
	};

	welaunch.field_objects.editor.onChange = function( i ) {
		tinymce.editors[i].on(
			'change',
			function( e ) {
				var el = jQuery( e.target.contentAreaContainer );
				if ( 0 !== el.parents( '.welaunch-container-editor:first' ).length ) {
					welaunch_change( $( '.wp-editor-area' ) );
				}
			}
		);
	};
})( jQuery );
