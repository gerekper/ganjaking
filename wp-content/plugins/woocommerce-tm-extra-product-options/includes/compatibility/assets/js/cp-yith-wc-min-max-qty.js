( function( window, document, $ ) {
	'use strict';

	$( document ).on( 'ywmmq_additional_operations', function() {
		$( window ).trigger( 'tm-do-epo-update' );
	} );
}( window, document, window.jQuery ) );
