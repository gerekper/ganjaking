/*----------------------------------------------------------------------------*\
	MAILCHIMP SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function init_shortcode( $mailchimp ) {
		$mailchimp.trigger( 'mpc.inited' );

		var $selects    = $mailchimp.find( 'select' ),
			$inputs     = $mailchimp.find( 'input:not([type="submit"], [type="checkbox"], [type="radio"])' ),
			$radios     = $mailchimp.find( 'input[type="checkbox"], input[type="radio"]' ),
			$submit     = $mailchimp.find( 'input[type="submit"]' ),
			_align      = $mailchimp.attr( 'data-align' ),
			_typography = {
				label: $mailchimp.attr( 'data-typo-label' ),
				input: $mailchimp.attr( 'data-typo-input' ),
				radio: $mailchimp.attr( 'data-typo-radio' ),
				submit: $mailchimp.attr( 'data-typo-submit' )
			};

		_align = _align == undefined ? 'left' : _align;

		$submit.parent().css( 'text-align', _align );

		$radios.closest( 'label' ).addClass( 'mpc-input-wrap' );

		if ( $inputs.length ) {
			$selects.css( 'height', $inputs.outerHeight() );
		}

		if ( _typography.label != undefined ) {
			$mailchimp.find( 'label:not(.mpc-input-wrap)' ).addClass( _typography.label );
		}
		if ( _typography.input != undefined ) {
			$selects.addClass( _typography.input );
			$inputs.addClass( _typography.input );
		}
		if ( _typography.radio != undefined ) {
			$mailchimp.find( 'label.mpc-input-wrap' ).addClass( _typography.radio );
		}
		if ( _typography.submit != undefined ) {
			$submit.addClass( _typography.submit );
		}
	}

	if ( typeof window.InlineShortcodeView != 'undefined' ) {
		window.InlineShortcodeView_mpc_mailchimp = window.InlineShortcodeView.extend( {
			rendered: function() {
				var $icon = this.$el.find( '.mpc-mailchimp' );

				$icon.addClass( 'mpc-waypoint--init' );

				_mpc_vars.$body.trigger( 'mpc.icon-loaded', [ $icon ] );
				_mpc_vars.$body.trigger( 'mpc.font-loaded', [ $icon ] );
				_mpc_vars.$body.trigger( 'mpc.inited', [ $icon ] );

				init_shortcode( $icon );

				window.InlineShortcodeView_mpc_mailchimp.__super__.rendered.call( this );
			}
		} );
	}

	var $mailchimps = $( '.mpc-mailchimp' );

	$mailchimps.each( function() {
		var $mailchimp = $( this );

		$mailchimp.one( 'mpc.init', function () {
			init_shortcode( $mailchimp );
		} );
	} );
} )( jQuery );
