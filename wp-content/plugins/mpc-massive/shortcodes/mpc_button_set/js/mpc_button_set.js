/*----------------------------------------------------------------------------*\
 BUTTON SET SHORTCODE
 \*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function switch_style( $button_set ) {
		if ( _mpc_vars.breakpoints.custom( '(max-width: 768px)' ) ) {
			if ( $button_set.is( '.mpc-style--horizontal' ) ) {
				$button_set.removeClass( 'mpc-style--horizontal' ).addClass( 'mpc-style--vertical mpc-style--horizontal-desktop' );
			}
		} else {
			if ( $button_set.is( '.mpc-style--horizontal-desktop' ) ) {
				$button_set.removeClass( 'mpc-style--vertical mpc-style--horizontal-desktop' ).addClass( 'mpc-style--horizontal' );
			}
		}
	}

	function init_shortcode( $button_set ) {
		$button_set.trigger( 'mpc.inited' );

		_mpc_vars.$window.on( 'mpc.resize', function() {
			switch_style( $button_set );
		} );

		switch_style( $button_set );

		if ( $button_set.attr( 'data-animation' ) != undefined ) {
			var $separators = $button_set.find( '.mpc-button-separator' ),
				_animation  = $button_set.attr( 'data-animation' );

			setInterval( function() {
				$separators
					.velocity( 'stop', true )
					.velocity( _animation );
			}, 2500 );
		}
	}

	if ( typeof window.InlineShortcodeViewContainer != 'undefined' ) {
		window.InlineShortcodeView_mpc_button_set = window.InlineShortcodeViewContainer.extend( {
			rendered: function() {
				var $button_set = this.$el.find( '.mpc-button-set' );

				$button_set.addClass( 'mpc-waypoint--init mpc-frontend' );

				_mpc_vars.$body.trigger( 'mpc.icon-loaded', [ $button_set ] );
				_mpc_vars.$body.trigger( 'mpc.font-loaded', [ $button_set ] );
				_mpc_vars.$body.trigger( 'mpc.inited', [ $button_set ] );

				init_shortcode( $button_set );

				window.InlineShortcodeView_mpc_button_set.__super__.rendered.call( this );
			}
		} );
	}

	var $button_sets = $( '.mpc-button-set' );

	$button_sets.each( function() {
		var $button_set = $( this );

		$button_set.find( '.mpc-button-separator-wrap:last-child' ).remove();

		$button_set.one( 'mpc.init', function () {
			init_shortcode( $button_set );
		} );
	} );
} )( jQuery );