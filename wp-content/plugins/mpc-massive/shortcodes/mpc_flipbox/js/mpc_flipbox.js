/*----------------------------------------------------------------------------*\
	FLIPBOX SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function calculate( $flipbox ) {
		var $front        = $flipbox.find( '.mpc-flipbox__front' ),
			$back         = $flipbox.find( '.mpc-flipbox__back' ),
			_front_height = $front.outerHeight(),
			_back_height  = $back.outerHeight(),
			_max_height;

		if ( typeof $flipbox.attr( 'data-max-height' ) !== 'undefined' ) {
			_max_height = $flipbox.attr( 'data-max-height' );
		} else if ( typeof $flipbox.attr( 'data-primary-side' ) !== 'undefined' ) {
			var _side = $flipbox.attr( 'data-primary-side' );

			if ( 'front' === _side ) {
				_max_height = _front_height;
			} else if ( 'back' === _side ) {
				_max_height = _back_height;
			} else {
				_max_height = Math.max( _front_height, _back_height );
			}

		} else {
			_max_height = Math.max( _front_height, _back_height );
		}

		$flipbox.height( _max_height );

		$front.css( 'height', '100%' );
		$back.css( 'height', '100%' );

		$flipbox.trigger( 'mpc.inited' );
	}

	function responsive( $flipbox ) {
		var $front = $flipbox.find( '.mpc-flipbox__front' ),
		    $side = $flipbox.find( '.mpc-flipbox__back' );

		$front.removeAttr( 'style' );
		$side.removeAttr( 'style' );

		calculate( $flipbox );
	}

	function init_shortcode( $flipbox ) {
		if( ! $flipbox.is( '.mpc-init' ) ) return;

		if ( $flipbox.find( 'img' ).length > 0 ) {
			$flipbox.imagesLoaded().always( function() {
				calculate( $flipbox );
			} );
		} else {
			calculate( $flipbox );
		}
	}

	var $flipboxes = $( '.mpc-flipbox' );

	$flipboxes.each( function() {
		var $flipbox = $( this );

		$flipbox.one( 'mpc.init', function() {
			init_shortcode( $flipbox );
		} );

		$flipbox.on( 'mouseenter', function() {
			if( $flipbox.find( '.mpc-parent--init' ).length ) {
				$flipbox.find( '.mpc-container' ).trigger( 'mpc.parent-init' );
				$flipbox.find( '.mpc-parent--init' ).removeClass( 'mpc-parent--init' );
			}
		} );

		$flipbox.on( 'click', function( event ) {
			if ( typeof event.currentTarget.href !== 'undefined'
				&& $flipbox.hasClass( 'mpc-flipbox--animate' ) ) {
				window.location.href = event.currentTarget.href;
			} else if ( $flipbox.hasClass( 'mpc-flipbox--animate' ) ) {
				event.stopPropagation();
				$flipbox.toggleClass( 'mpc-flipbox--animate' );
			}else if ( $flipbox.hasClass( 'mpc-flipbox--click' ) ) {
				event.preventDefault();
				$flipbox.toggleClass( 'mpc-flipbox--animate' );
			}
		} );
	});

	_mpc_vars.$window.on( 'mpc.resize load', function() {
		$.each( $flipboxes, function() {
			responsive( $( this ) );
		} );
	} );
} )( jQuery );
