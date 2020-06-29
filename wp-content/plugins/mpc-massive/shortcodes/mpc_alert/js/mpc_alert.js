/*----------------------------------------------------------------------------*\
	ALERT SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function init_shortcode( $alert ) {
		var $dismiss   = $( '.mpc-alert__dismiss[data-alert="' + $alert.data( 'id' ) + '"]' ),
			_frequency = $dismiss.data( 'frequency' ),
			$alert_wrap = $alert;

		if( $alert.parents( '.mpc-alert-wrap' ).length ) {
			$alert_wrap = $alert.parents( '.mpc-alert-wrap' );
		} else if( $alert.parents( '.mpc-ribbon-wrap' ).length ) {
			$alert_wrap = $alert.parents( '.mpc-ribbon-wrap' );
		}

		$dismiss.on( 'click', function() {
			$alert_wrap.css( 'height', $alert_wrap.height() );

			$alert_wrap.velocity( {
				opacity: 0,
				height: 0,
				margin: 0
			}, {
				duration: 250,
				complete: function() {
					$alert_wrap.css( 'display', 'none' );
				}
			} );

			if( _frequency != 'always' ) {
				$.post( _mpc_vars.ajax_url, {
					action:    'mpc_set_alert_cookie',
					id:        $alert.data( 'cookie' ),
					frequency: _frequency
				} );
			}
		});

		$alert.trigger( 'mpc.inited' );
	}

	if ( typeof window.InlineShortcodeView != 'undefined' ) {
		window.InlineShortcodeView_mpc_alert = window.InlineShortcodeView.extend( {
			rendered: function() {
				var $alert = this.$el.find( '.mpc-alert' );

				$alert.addClass( 'mpc-waypoint--init' );

				_mpc_vars.$body.trigger( 'mpc.icon-loaded', [ $alert ] );
				_mpc_vars.$body.trigger( 'mpc.font-loaded', [ $alert ] );
				_mpc_vars.$body.trigger( 'mpc.inited', [ $alert ] );

				init_shortcode( $alert );

				window.InlineShortcodeView_mpc_alert.__super__.rendered.call( this );
			},
		} );
	}

	var $alerts = $( '.mpc-alert' );

	$alerts.each( function() {
		var $alert = $( this );

		$alert.one( 'mpc.init', function () {
			init_shortcode( $alert );
		} );
	} );
} )( jQuery );
