/*----------------------------------------------------------------------------*\
	MPC_COLORPICKER Param
\*----------------------------------------------------------------------------*/
(function( $ ) {
	"use strict";

	var $popup = $( '#vc_ui-panel-edit-element' );

	$popup.one( 'mpc.render', function() {
		var $mpc_colorpickers = $( '.vc_wrapper-param-type-mpc_colorpicker input' );

		$mpc_colorpickers.each( function() {
			var $field = $( this );

			$field.colorPicker();
		} );
	} );
})( jQuery );
