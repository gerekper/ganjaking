/*----------------------------------------------------------------------------*\
	IHOVER ITEM SHORTCODE - Panel
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function get_styles( _mpc_shape, _mpc_effect ) {
		if ( _mpc_shape == 'circle' ) {
			switch( _mpc_effect ) {
				case 'effect1':
				case 'effect5':
				case 'effect15':
				case 'effect17':
				case 'effect19':
					return _styles[ 'style1' ];
				case 'effect2':
				case 'effect3':
				case 'effect4':
				case 'effect7':
				case 'effect8':
				case 'effect9':
				case 'effect11':
				case 'effect12':
				case 'effect14':
				case 'effect18':
					return _styles[ 'style2' ];
				case 'effect6':
					return _styles[ 'style3' ];
				case 'effect10':
				case 'effect20':
					return _styles[ 'style4' ];
				case 'effect13':
					return _styles[ 'style5' ];
				case 'effect16':
					return _styles[ 'style6' ];
				default:
					return '';
			}
		} else {
			switch( _mpc_effect ) {
				case 'effect2':
				case 'effect4':
				case 'effect7':
					return _styles[ 'style1' ];
				case 'effect9':
				case 'effect10':
				case 'effect11':
				case 'effect12':
				case 'effect13':
				case 'effect14':
				case 'effect15':
					return _styles[ 'style2' ];
				case 'effect3':
					return _styles[ 'style4' ];
				case 'effect5':
					return _styles[ 'style6' ];
				case 'effect1':
					return _styles[ 'style7' ];
				case 'effect6':
					return _styles[ 'style8' ];
				case 'effect8':
					return _styles[ 'style9' ];
				default:
					return '';
			}
		}
	}

	var _styles = {
		'style1': '.none',
		'style2': '.left_to_right, .right_to_left, .top_to_bottom, .bottom_to_top',
		'style3': '.scale_up, .scale_down, .scale_down_up',
		'style4': '.top_to_bottom, .bottom_to_top',
		'style5': '.from_left_and_right, .top_to_bottom, .bottom_to_top',
		'style6': '.left_to_right, .right_to_left',
		'style7': '.left_and_right, .top_to_bottom, .bottom_to_top',
		'style8': '.from_top_and_bottom, .from_left_and_right, .top_to_bottom, .bottom_to_top',
		'style9': '.scale_up, .scale_down'
	};

	var $popup = $( '#vc_ui-panel-edit-element' );

	$popup.on( 'mpc.render', function() {
		if ( $popup.attr( 'data-vc-shortcode' ) != 'mpc_ihover' ) {
			return;
		}

		var $mpc_shape  = $popup.find( '.mpc-ihover-shape select.shape' ),
			$mpc_effect = $popup.find( '.mpc-ihover-effect select.effect' ),
			$mpc_style  = $popup.find( '.mpc-ihover-style select.style' );

		$mpc_shape.on( 'change', function() {
			if ( $mpc_shape.val() == 'circle' ) {
				$mpc_effect.children().prop( 'disabled', false );
			} else {
				$mpc_effect.children( '.effect16, .effect17, .effect18, .effect19, .effect20' ).prop( 'disabled', true );
			}

			if ( $mpc_effect.val() == null ) {
				$mpc_effect.val( $mpc_effect.children( ':not(:disabled)' ).first().attr( 'value' ) );
			}

			$mpc_effect.trigger( 'change' );
		} );
		$mpc_shape.trigger( 'change' );

		$mpc_effect.on( 'change', function() {
			$mpc_style.children().prop( 'disabled', true );

			$mpc_style.find( get_styles( $mpc_shape.val(), $mpc_effect.val() ) ).prop( 'disabled', false );

			if ( $mpc_style.val() == null ) {
				$mpc_style.val( $mpc_style.children( ':not(:disabled)' ).first().attr( 'value' ) );
			}
		} );
		$mpc_effect.trigger( 'change' );
	});

	$popup.on( 'mpc.render', function() {
		if ( $popup.attr( 'data-vc-shortcode' ) != 'mpc_ihover_item' ) {
			return;
		}

		var $mpc_style  = $popup.find( '.mpc-ihover-style select.style' ),
			_params = vc.shortcodes.findWhere( { id: vc.active_panel.model.attributes.parent_id } ).attributes.params,
			_shape = _params.shape,
			_effect = _params.effect;

		$mpc_style.children().prop( 'disabled', true );

		$mpc_style.find( get_styles( _shape, _effect ) + ', .default' ).prop( 'disabled', false );

		if ( $mpc_style.val() == null ) {
			$mpc_style.val( $mpc_style.children( ':not(:disabled)' ).first().attr( 'value' ) );
		}
	});
} )( jQuery );