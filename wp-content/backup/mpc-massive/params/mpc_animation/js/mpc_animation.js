/*----------------------------------------------------------------------------*\
	MPC_ANIMATION PARAM
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	var $animations = $( '.wpb_el_type_mpc_animation' );

	$animations.each( function() {
		var $animation_wrap = $( this ),
		    $animation = $animation_wrap.find( '.mpc-vc-animation' ),
		    $box = $animation_wrap.find( '.mpc-inner-box' ),
		    $replay = $animation_wrap.find( '.mpc-animation-replay' ),
		    _value;

		$replay.on( 'click', function() {
			var _field_name = $animation_wrap.attr( 'data-vc-shortcode-param-name' ).replace( '_type', '' ),
			    _duration   = parseInt( $( 'input[name="' + _field_name + '_duration"]' ).val() );

			_value = $animation.val();

			if ( _value != '' ) {
				if ( _value === "fadeIn" || /In$/.test( _value ) ) {
					$box.velocity( 'stop' ).velocity( { opacity: 0 }, { duration: _duration } );
				}

				$box.velocity( 'stop' ).velocity( _value, { duration: _duration } );

				if ( _value === "fadeOut" || /Out$/.test( _value ) ) {
					$box.velocity( 'stop' ).velocity( { opacity: 1 }, { display: "block", duration: _duration } );
				}
			}
		} );

		$animation.on( 'change', function() {
			var _field_name = $animation_wrap.attr( 'data-vc-shortcode-param-name' ).replace( '_type', '' ),
			    _duration   = parseInt( $( 'input[name="' + _field_name + '_duration"]' ).val() );

			_value = $animation.val();

			if ( _value != '' ) {
				if ( _value === "fadeIn" || /In$/.test( _value ) ) {
					$box.velocity( 'stop' ).velocity( { opacity: 0 }, { duration: _duration } );
				}

				$box.velocity( 'stop' ).velocity( _value, { duration: _duration } );

				if ( _value === "fadeOut" || /Out$/.test( _value ) ) {
					$box.velocity( 'stop' ).velocity( { opacity: 1 }, { display: "block", duration: _duration } );
				}
			}
		} );
	} );
} )( jQuery );
