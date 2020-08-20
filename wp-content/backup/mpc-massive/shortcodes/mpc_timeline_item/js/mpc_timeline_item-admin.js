/*----------------------------------------------------------------------------*\
	TIMELINE ITEM SHORTCODE - Panel
\*----------------------------------------------------------------------------*/
(function( $ ) {
	"use strict";

	var $popup      = $( '#vc_ui-panel-edit-element' ),
		_hide_class = 'vc_dependent-hidden',
		_tabs       = [];

	_tabs[ 'title' ]   = 1;
	_tabs[ 'content' ] = 2;
	_tabs[ 'icon' ]    = 3;
	_tabs[ 'divider' ] = 4;

	function layout_dependency( _layout ) {
		var _parts = _layout.split( ',' ),
			$tabs = $popup.find( '[data-vc-ui-element="panel-tab-control"]:gt(0):lt(4)' );

		$tabs.addClass( _hide_class );

		_parts.forEach( function( _part ) {
			var $tab = $popup.find( '[data-vc-ui-element-target="#vc_edit-form-tab-' + _tabs[ _part ] + '"]' );

			$tab.removeClass( _hide_class );
		} );
	}

	$popup.on( 'mpc.render', function() {
		if( $popup.attr( 'data-vc-shortcode' ) != 'mpc_timeline_item' ) {
			return '';
		}

		var $layout = $popup.find( '[name="layout"]' );

		$layout.on( 'change', function() {
			layout_dependency( $layout.val() );
		} );

		// Triggers
		setTimeout( function() {
			$layout.trigger( 'change' );
		}, 350 );
	} );
})( jQuery );
