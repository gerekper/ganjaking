/*----------------------------------------------------------------------------*\
 MPC_SHADOW Param
 \*----------------------------------------------------------------------------*/
(function( $ ) {
	"use strict";

	var $popup = $( '#vc_ui-panel-edit-element' );

	$popup.one( 'mpc.render', function() {
		var $shadow_wraps = $( '.vc_wrapper-param-type-mpc_shadow' );

		$shadow_wraps.each( function() {
			var $shadow_wrap  = $( this ),
				$shadow_value = $shadow_wrap.find( '.mpc-value' ),
				$left_offset  = $shadow_wrap.find( '.mpc-shadow-left' ),
				$top_offset   = $shadow_wrap.find( '.mpc-shadow-top' ),
				$blur         = $shadow_wrap.find( '.mpc-shadow-blur' ),
				$params       = $shadow_wrap.find( '.mpc_text_field' ),
				$color_picker = $shadow_wrap.find( '.mpc-shadow-color' ),
				$shadow_text  = $shadow_wrap.find( '.mpc-shadow-text' ),
				_color_picker = {
					defaultColor: $( this ).val(),
					change:       function() {
						setTimeout( function() {
							$shadow_value.val( shadow_param_change( $left_offset, $top_offset, $blur, $color_picker, $shadow_text ) );
						}, 50 );
					},
					clear:        function() {
						setTimeout( function() {
							$shadow_value.val( shadow_param_change( $left_offset, $top_offset, $blur, $color_picker, $shadow_text ) );
						}, 50 );
					},
					hide:         true,
					palettes:     true
				};

			$color_picker.wpColorPicker( _color_picker );

			$params.on( 'change keyup', function() {
				$shadow_value.val( shadow_param_change( $left_offset, $top_offset, $blur, $color_picker, $shadow_text ) );
			} );

			$shadow_value.on( 'mpc.change', function() {
				shadow_value_change( $shadow_value.val(), $left_offset, $top_offset, $blur, $color_picker, $shadow_text );
			} );
		} );
	} );

	function shadow_param_change( $left_offset, $top_offset, $blur, $color_picker, $shadow_text ) {
		var _text_shadow = 'none';
		var _values = {
			left: $left_offset.val(),
			top: $top_offset.val(),
			blur: $blur.val(),
			color: $color_picker.val()
		};

		if ( _values.color != '' ) {
			_text_shadow = '';

			if ( _values.left != '' ) {
				_text_shadow += _values.left + 'px ';
			} else {
				_text_shadow += '0 ';
			}

			if ( _values.top != '' ) {
				_text_shadow += _values.top + 'px ';
			} else {
				_text_shadow += '0 ';
			}

			if ( _values.blur != '' ) {
				_text_shadow += _values.blur + 'px ';
			} else {
				_text_shadow += '0 ';
			}

			_text_shadow += _values.color;
		}

		$shadow_text.css( 'text-shadow', _text_shadow );

		return _text_shadow != 'none' ? _text_shadow : '';
	}

	function shadow_value_change( value, $left_offset, $top_offset, $blur, $color_picker, $shadow_text ) {
		value = value.trim().replace( /\s+/g, ' ' ).split( ' ' );

		if ( value.length > 4 ) {
			value[ 3 ] = value.slice( 3 ).join( '' );
		}

		if ( value.length != 4 ) {
			value[ 0 ] = value[ 1 ] = value[ 2 ] = value[ 3 ] = '';
		}

		$left_offset.val( parseInt( value[ 0 ] ) );
		$top_offset.val( parseInt( value[ 1 ] ) );
		$blur.val( parseInt( value[ 2 ] ) );
		$color_picker.val( value[ 3 ] ).trigger( 'change' );

		shadow_param_change( $left_offset, $top_offset, $blur, $color_picker, $shadow_text );
	}

})( jQuery );
