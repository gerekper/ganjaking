/*----------------------------------------------------------------------------*\
	MPC_DATETIME Param
\*----------------------------------------------------------------------------*/
(function( $ ) {
	"use strict";

	var $popup = $( '#vc_ui-panel-edit-element' );

	$popup.one( 'mpc.render', function() {
		$( '.vc_wrapper-param-type-mpc_datetime input' ).MPCdatetimepicker( {
			format:  'd/m/Y H:i',
			minDate: 0 // today
		});
	} );
})( jQuery );
