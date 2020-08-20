/*----------------------------------------------------------------------------*\
	ICON LIST SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function init_shortcode( $icon_list ) {
		$icon_list.trigger( 'mpc.inited' );
	}

	if ( typeof window.InlineShortcodeView != 'undefined' ) {
		window.InlineShortcodeView_mpc_icon_list = window.InlineShortcodeView.extend( {
			rendered: function() {
				var $icon_list = this.$el.find( '.mpc-icon-list' );

				$icon_list.addClass( 'mpc-waypoint--init' );

				_mpc_vars.$body.trigger( 'mpc.icon-loaded', [ $icon_list ] );
				_mpc_vars.$body.trigger( 'mpc.font-loaded', [ $icon_list ] );
				_mpc_vars.$body.trigger( 'mpc.inited', [ $icon_list ] );

				init_shortcode( $icon_list );

				window.InlineShortcodeView_mpc_icon_list.__super__.rendered.call( this );
			}
		} );
	}

	var $icon_lists = $( '.mpc-icon-list' );

	$icon_lists.each( function() {
		var $icon_list = $( this );

		$icon_list.one( 'mpc.init', function () {
			init_shortcode( $icon_list );
		} );
	} );
} )( jQuery );
