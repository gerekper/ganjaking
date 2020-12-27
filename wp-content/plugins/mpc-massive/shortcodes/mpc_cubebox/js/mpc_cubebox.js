/*----------------------------------------------------------------------------*\
	CUBEBOX SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function calculate( $cubebox ) {
		var $front        = $cubebox.find( '.mpc-cubebox__front .mpc-cubebox-side' ),
		    $side         = $cubebox.find( '.mpc-cubebox__side .mpc-cubebox-side' ),
			_front_height = $front.outerHeight(),
			_side_height  = $side.outerHeight(),
			_max_height;

		if ( typeof $cubebox.attr( 'data-max-height' ) !== 'undefined' ) {
			_max_height = $cubebox.attr( 'data-max-height' );
		} else if ( typeof $cubebox.attr( 'data-primary-side' ) !== 'undefined' ) {
			var _side = $cubebox.attr( 'data-primary-side' );

			if ( 'front' === _side ) {
				_max_height = _front_height;
			} else if ( 'side' === _side ) {
				_max_height = _side_height;
			} else {
				_max_height = Math.max( _front_height, _side_height );
			}

		} else {
			_max_height = Math.max( _front_height, _side_height );
		}

		$cubebox.height( _max_height );

		$front.css( 'height', '100%' );
		$side.css( 'height', '100%' );

		$cubebox.trigger( 'mpc.inited' );
	}

	function responsive( $cubebox ) {
		var $front = $cubebox.find( '.mpc-cubebox__front' ),
		    $side = $cubebox.find( '.mpc-cubebox__side' );

		$front.removeAttr( 'style' );
		$side.removeAttr( 'style' );

		calculate( $cubebox );
	}

	function init_shortcode( $cubebox ) {
		if( !$cubebox.is( '.mpc-init' ) ) return;

		if( $cubebox.find( 'img' ).length > 0 ) {
			$cubebox.imagesLoaded().always( function() {
				calculate( $cubebox );
			} );
		} else {
			calculate( $cubebox );
		}

		$cubebox.trigger( 'mpc.inited' );
	}

	var $cubeboxes = $( '.mpc-cubebox' );

	$cubeboxes.each( function() {
		var $cubebox = $( this );

		$cubebox.one( 'mpc.init', function() {
			init_shortcode( $cubebox );
		} );

		$cubebox.on( 'mouseenter', function() {
			$cubebox.addClass( 'mpc-flipped' );

			if( $cubebox.find( '.mpc-parent--init' ).length ) {
				$cubebox.find( '.mpc-container' ).trigger( 'mpc.parent-init' );
				$cubebox.find( '.mpc-parent--init' ).removeClass( 'mpc-parent--init' );
			}
		}).on( 'mouseleave', function(){
			$cubebox.removeClass( 'mpc-flipped' );
		} );

		$cubebox.on( 'click', function( event ) {
			if ( typeof event.currentTarget.href !== 'undefined'
				&& $cubebox.hasClass( 'mpc-cubebox--animate' ) ) {
				window.location.href = event.currentTarget.href;
			} else if ( $cubebox.hasClass( 'mpc-cubebox--animate' ) ) {
				event.stopPropagation();
				$cubebox.toggleClass( 'mpc-cubebox--animate' );
			} else if ( $cubebox.hasClass( 'mpc-cubebox--click' ) ) {
				event.preventDefault();
				$cubebox.toggleClass( 'mpc-cubebox--animate' );
			}
		} );
	} );

	_mpc_vars.$window.on( 'mpc.resize load', function() {
		$.each( $cubeboxes, function() {
			responsive( $( this ) );
		} );
	} );

} )( jQuery );
