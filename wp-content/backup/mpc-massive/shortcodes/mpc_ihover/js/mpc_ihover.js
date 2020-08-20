/*----------------------------------------------------------------------------*\
	IHOVER SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function resize_shortcode( $ihover ) {
		var _size = '';

		if ( _mpc_vars.breakpoints.custom( '(max-width: 768px)' ) ) {
			_size = $ihover.width();
		}

		$ihover.find( '.mpc-ihover-item' ).css( {
			width: _size,
			height: _size
		} );
	}

	function init_shortcode( $ihover ) {
		$ihover.trigger( 'mpc.inited' );

		$ihover.on( 'click', '.mpc-ihover-item > a', function( event ) {
			var $ihover_item = $( this );

			if ( $ihover_item.is( '[href="#"]' ) ) {
				event.preventDefault();
			}
		} );
	}

	if ( typeof window.InlineShortcodeViewContainer != 'undefined' ) {
		window.InlineShortcodeView_mpc_ihover = window.InlineShortcodeViewContainer.extend( {
			rendered: function ( params ) {
				var $ihovers = this.$el.find( '.mpc-ihover-wrapper' );

				$ihovers.addClass( 'mpc-waypoint--init' );

				_mpc_vars.$body.trigger( 'mpc.icon-loaded', [ $ihovers ] );
				_mpc_vars.$body.trigger( 'mpc.font-loaded', [ $ihovers ] );
				_mpc_vars.$body.trigger( 'mpc.inited', [ $ihovers ] );

				init_shortcode( $ihovers );

				window.InlineShortcodeView_mpc_ihover.__super__.rendered.call( this, params );
			}
		} );
	}

	var $ihovers = $( '.mpc-ihover-wrapper' );

	$ihovers.each( function() {
		var $ihover = $( this );

		$ihover.one( 'mpc.init', function () {
			init_shortcode( $ihover );
			resize_shortcode( $ihover );
		} );
	} );

	_mpc_vars.$window.on( 'mpc.resize', function() {
		$.each( $ihovers, function() {
			resize_shortcode( $( this ) );
		} );
	} );
} )( jQuery );
