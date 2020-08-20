/*----------------------------------------------------------------------------*\
	ICON COLUMN SHORTCODE - Panel
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	var $popup = $( '#vc_ui-panel-edit-element' );

	$popup.on( 'mpc.render', function() {
		if ( $popup.attr( 'data-vc-shortcode' ) != 'mpc_icon_column' ) {
			return;
		}

		if ( vc.shortcodes.findWhere( { id: vc.active_panel.model.attributes.parent_id } ).attributes.shortcode == 'mpc_circle_icons' ) {
			$popup.find( '.vc_shortcode-param[data-vc-shortcode-param-name="layout"], .vc_shortcode-param[data-vc-shortcode-param-name="border_radius"]' ).hide( 0 );

			$popup.find( '.vc_shortcode-param[data-vc-shortcode-param-name="margin_divider"]' ).closest( '.mpc-vc-wrapper' ).hide( 0 );
		}
	} );
} )( jQuery );
