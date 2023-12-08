(function ( $ ) {
	'use strict';

	$( document ).on( 'acf/setup_fields', function () {
		// Redeclare active editor.
		setTimeout( function () {
			if ( 'tinymce' === getUserSetting( 'editor' ) ) {
				$( '#content-tmce' ).trigger( 'click' );
			} else {
				$( '#content-html' ).trigger( 'click' );
			}
		}, 10 );
	} );
})( window.jQuery );
