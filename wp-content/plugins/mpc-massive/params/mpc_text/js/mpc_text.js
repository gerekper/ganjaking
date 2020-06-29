/*----------------------------------------------------------------------------*\
	MPC_TEXT Param
\*----------------------------------------------------------------------------*/
(function( $ ) {
	"use strict";

	function mpc_validate__int( $field ) {
		var _value = parseInt( $field.val() );

		$field.val( isNaN( _value ) ? '' : _value );
	}

	function mpc_validate__float( $field ) {
		var _value = parseFloat( $field.val() );

		$field.val( isNaN( _value ) ? '' : _value );
	}

	var $popup = $( '#vc_ui-panel-edit-element' );

	$popup.one( 'mpc.render', function() {
		var $mpc_texts = $( '.vc_wrapper-param-type-mpc_text .mpc-text-input' );

		$mpc_texts.each( function() {
			var $field = $( this ),
				$input = $field.children( 'input' ),
				_validate = $field.attr( 'data-validate' );

			if ( _validate == '1' || _validate == 'int' ) {
				mpc_validate__int( $input );

				$input.on( 'blur', function() {
					mpc_validate__int( $input );
				} );
			} else if ( _validate == 'float' ) {
				mpc_validate__float( $input );

				$input.on( 'blur', function() {
					mpc_validate__float( $input );
				} );
			}
		} );
	} );
})( jQuery );
