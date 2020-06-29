/*----------------------------------------------------------------------------*\
	MPC_CSS PARAM
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function fields_to_css( $css_field, $fields, _section, _selector, _advanced ) {
		var _styles = '',
			_unit = '',
			_value = '';

		if ( _section == 'border' || _section == 'inner_border' ) {
			_unit = 'px';
		} else if ( _section == 'padding' || _section == 'margin' ) {
			_unit = $fields.filter( _selector + 'unit' ).val();
		}

		if ( _advanced ) {
			if ( $fields.filter( _selector + 'top' ).val() != '' && ! isNaN( _value = parseFloat( $fields.filter( _selector + 'top' ).val() ) ) ) {
				_styles += _section + '-top:' + _value + _unit + ';';
			}
			if ( $fields.filter( _selector + 'right' ).val() != '' && ! isNaN( _value = parseFloat( $fields.filter( _selector + 'right' ).val() ) ) ) {
				_styles += _section + '-right:' + _value + _unit + ';';
			}
			if ( $fields.filter( _selector + 'bottom' ).val() != '' && ! isNaN( _value = parseFloat( $fields.filter( _selector + 'bottom' ).val() ) ) ) {
				_styles += _section + '-bottom:' + _value + _unit + ';';
			}
			if ( $fields.filter( _selector + 'left' ).val() != '' && ! isNaN( _value = parseFloat( $fields.filter( _selector + 'left' ).val() ) ) ) {
				_styles += _section + '-left:' + _value + _unit + ';';
			}
		} else {
			if ( _section == 'border' ) {
				if ( $fields.filter( _selector + 'all' ).val() != '' && ! isNaN( _value = parseFloat( $fields.filter( _selector + 'all' ).val() ) ) ) {
					_styles += _section + '-width:' + _value + _unit + ';';
				}
			} else if ( _section == 'padding' || _section == 'margin' ) {
				if ( $fields.filter( _selector + 'all' ).val() != '' && ! isNaN( _value = parseFloat( $fields.filter( _selector + 'all' ).val() ) ) ) {
					_styles += _section + ':' + _value + _unit + ';';
				}
			}
		}

		if ( _section == 'border' ) {
			if ( $fields.filter( _selector + 'color' ).val() != '' ) {
				_styles += 'border-color:' + $fields.filter( _selector + 'color' ).val() + ';';
			}

			if ( $fields.filter( _selector + 'style' ).val() != '' ) {
				_styles += 'border-style:' + $fields.filter( _selector + 'style' ).val() + ';';
			}

			if ( $fields.filter( _selector + 'radius' ).val() != '' && ! isNaN( _value = parseFloat( $fields.filter( _selector + 'radius' ).val() ) ) ) {
				_styles += 'border-radius:' + _value + _unit + ';';
			}
		}

		if ( _section == 'inner_border' ) {
			if ( $fields.filter( _selector + 'color' ).val() != '' &&  $fields.filter( _selector + 'width' ).val() != '' ) {
				_styles += 'box-shadow: inset 0px 0px 0px ' + $fields.filter( _selector + 'width' ).val() + _unit + ' ' + $fields.filter( _selector + 'color' ).val() + ';';
			}
		}

		$css_field.val( _styles );
	}

	function css_to_fields( $css_field, $fields, _section, _selector ) {
		var _css    = $css_field.val(),
			_prefix = $css_field.attr( 'data-prefix' );

		_prefix = typeof _prefix == 'undefined' ? '' : _prefix;

		$fields.val( '' );
		$fields.filter( _selector + 'unit' ).val( 'px' );

		if ( _css != '' ) {
			_css = _css.split( ';' );

			for ( var _index = 0; _index < _css.length; _index++ ) {
				var _pair = _css[ _index ].split( ':' );

				if ( _pair.length == 2 ) {
					_pair[ 0 ] = _pair[ 0 ].replace( '-', '_' );

					if ( _section == 'border' ) {
						$fields.filter( '.' + _prefix + _pair[ 0 ] ).val( _pair[ 1 ] ).trigger( 'change' ).trigger( 'blur' );

						if ( _pair[ 0 ] == 'border_width' ) {
							$fields.filter( _selector + 'all' ).val( _pair[ 1 ] ).trigger( 'blur' );
						}
					}

					if ( _section == 'padding' || _section == 'margin' ) {
						$fields.filter( '.' + _prefix + _pair[ 0 ] ).val( _pair[ 1 ] ).trigger( 'change' ).trigger( 'blur' );
						$fields.filter( _selector + 'unit' ).val( _pair[ 1 ].replace( parseFloat( _pair[ 1 ] ), '' ) ).trigger( 'change' );

						if ( _pair[ 0 ] == 'padding' || _pair[ 0 ] == 'margin' ) {
							$fields.filter( _selector + 'all' ).val( _pair[ 1 ] ).trigger( 'blur' );
						}
					}
				}
			}
		}
	}

	function inner_border_css_to_fields( $css_field, $fields ) {
		var _css    = $css_field.val(),
		    _prefix = $css_field.attr( 'data-prefix' );

		_prefix = typeof _prefix == 'undefined' ? '' : _prefix;

		if ( _css != '' ) {
			_css = /box-shadow: inset 0px 0px 0px (.*?);/.exec( _css );

			if( _css[ 1 ] == 'undefined' )
				return;

			_css = _css[ 1 ].split( ' ' );

			$fields.filter( '.' + _prefix + 'inner_border_width' ).val( parseFloat( _css[ 0 ] ) ).trigger( 'blur' );
			$fields.filter( '.' + _prefix + 'inner_border_color'  ).val( _css[ 1 ] ).trigger( 'change' );
		}
	}

	var $css_fields = $( '.mpc-vc-css' ),
		$popup      = $( '#vc_ui-panel-edit-element' ),
		$save_panel = $popup.find( '.vc_ui-button[data-vc-ui-element="button-save"]' );

	$popup.one( 'mpc.render', function() {
		$css_fields.each( function() {
			var $css_field       = $( this ),
				_prefix          = $css_field.attr( 'data-prefix' ),
				_section         = $css_field.attr( 'data-section' ),
				_selector        = '.' + _prefix + _section + '_',
				$advanced        = $popup.find( _selector + 'divider' ),
				$separate_fields = $( _selector + 'top,' + _selector + 'right,' + _selector + 'bottom,' + _selector + 'left' ),
				$compact_field   = $( _selector + 'all' ),
				$radius_field    = $( _selector + 'radius' ),
				$other_fields    = $css_field.parents( '.mpc-vc-indent' ).find( '.wpb_vc_param_value' ).not( $css_field );

			$advanced.on( 'change', function() {
				if ( $advanced.is( ':checked' ) ) {
					$compact_field.parents( '.vc_shortcode-param' ).stop( true ).fadeOut( 100, function() {
						$compact_field.parents( '.vc_shortcode-param' ).css( 'display', 'none' );

						$separate_fields.parents( '.vc_shortcode-param' ).stop( true ).fadeIn( 100, function() {
							$separate_fields.parents( '.vc_shortcode-param' ).css( 'display', '' );
						} );
					} );
				} else {
					$separate_fields.parents( '.vc_shortcode-param' ).stop( true ).fadeOut( 100, function() {
						$separate_fields.parents( '.vc_shortcode-param' ).css( 'display', 'none' );

						$compact_field.parents( '.vc_shortcode-param' ).stop( true ).fadeIn( 100, function() {
							$compact_field.parents( '.vc_shortcode-param' ).css( 'display', '' );
						} );
					} );
				}
			} ).trigger( 'change' );

			$compact_field.add( $separate_fields ).add( $radius_field ).on( 'blur mpc.change', function() {
				var $field = $( this ),
					_value = parseFloat( $field.val() );

				$field.val( isNaN( _value ) ? '' : _value );
			} );

			$compact_field.on( 'change', function() {
				$separate_fields.val( $compact_field.val() );
			} );

			$other_fields.on( 'blur change', function() {
				fields_to_css( $css_field, $other_fields, _section, _selector, $advanced.is( ':checked' ) );
			} );

			$save_panel.one( 'click', function() {
				if ( ! $advanced.is( ':checked' ) ) {
					$advanced.val( '' );

					if ( window.vc_mode == 'admin_frontend_editor' ) {
						setTimeout( function() {
							$advanced.val( 'true' );
						}, 500 );
					}
				}

				$other_fields.each( function() {
					var $field    = $( this ),
						_settings = JSON.parse( $field.parents( '.vc_shortcode-param' ).attr( 'data-param_settings' ) );

					if ( window.vc_mode != 'admin_frontend_editor' ) {
						if ( typeof _settings.std != 'undefined' ) {
							$field.val( _settings.std );
						} else if ( typeof _settings.value != 'undefined' ) {
							$field.val( _settings.value );
						}
					}
				} );
			} );

			if( _section == 'inner_border' ) {
				inner_border_css_to_fields( $css_field, $other_fields );
			} else {
				css_to_fields( $css_field, $other_fields, _section, _selector );
			}

			$css_field.on( 'mpc.change', function() {
				if( _section == 'inner_border' ) {
					inner_border_css_to_fields( $css_field, $other_fields );
				} else {
					css_to_fields( $css_field, $other_fields, _section, _selector );
				}
			} );

			$advanced.on( 'change', function() {
				fields_to_css( $css_field, $other_fields, _section, _selector, $advanced.is( ':checked' ) );
			} )
		} );
	} );
} )( jQuery );
