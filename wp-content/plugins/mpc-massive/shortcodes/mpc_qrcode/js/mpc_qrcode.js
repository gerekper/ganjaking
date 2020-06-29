/*----------------------------------------------------------------------------*\
	QR CODE SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function delay_init( $qr ) {
		if( typeof( QRCode ) === typeof( Function ) ) {
			init_shortcode( $qr );
		} else {
			setTimeout( function() {
				delay_init( $qr );
			}, 50 );
		}
	}

	function init_shortcode( $qr ) {
		var _qrcode_atts = $qr.data( 'qr' );

		new QRCode( $qr[ 0 ], _qrcode_atts );

		$qr.trigger( 'mpc.inited' );
	}

	if ( typeof window.InlineShortcodeView != 'undefined' ) {
		window.InlineShortcodeView_mpc_qrcode = window.InlineShortcodeView.extend( {
			rendered: function() {
				var $qr = this.$el.find( '.mpc-qrcode' );

				_mpc_vars.$body.trigger( 'mpc.inited', [ $qr ] );

				init_shortcode( $qr );

				_mpc_vars.$document.trigger( 'mpc.init-tooltip', [ $qr.siblings( '.mpc-tooltip' ) ] );

				window.InlineShortcodeView_mpc_qrcode.__super__.rendered.call( this );
			}
		} );
	}

	var $qrcodes = $( '.mpc-qrcode' );

	$qrcodes.each( function() {
		var $qr = $( this );

		$qr.one( 'mpc.init', function () {
			delay_init( $qr );
		} );
	} );
} )( jQuery );
