/*----------------------------------------------------------------------------*\
	COLUMN SHORTCODE - Panel
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function add_separator_class() {
		vc.shortcodes
			.where( { shortcode: 'vc_column' } )
			.filter( function( shortcode ) { return shortcode.getParam( 'divider_enable' ) == 'true'; } )
			.forEach( function( shortcode ){ shortcode.view.$el.addClass( 'mpc-backend--divider' ); } );
	}

	var $popup      = $( '#vc_ui-panel-edit-element' ),
		$save_panel = $popup.find( '.vc_ui-button[data-vc-ui-element="button-save"]' );

	$( window ).on( 'load', function() {
		if ( typeof vc !== 'undefined' ) {
			add_separator_class();
		}
	} );

	$popup.on( 'mpc.render', function() {
		if ( $popup.attr( 'data-vc-shortcode' ) != 'vc_column' ) {
			return;
		}

		$save_panel.one( 'click', function() {
			add_separator_class();

			setTimeout( function() {
				add_separator_class();
			}, 2000 );
		} );
	} );
} )( jQuery );
