/*----------------------------------------------------------------------------*\
	BUTTON SHORTCODE - Panel
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	var $popup = $( '#vc_ui-panel-edit-element' );

	$popup.on( 'mpc.render', function() {
		if ( $popup.attr( 'data-vc-shortcode' ) != 'mpc_button' ) {
			return;
		}

		if ( vc.shortcodes.findWhere( { id: vc.active_panel.model.attributes.parent_id } ).get( 'shortcode' ) == 'mpc_button_set' ) {
			$popup.find( '.vc_shortcode-param[data-vc-shortcode-param-name="block"]' ).hide();
		}
	} );
} )( jQuery );
