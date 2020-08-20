/*----------------------------------------------------------------------------*\
	PROGRESS SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function init_shortcode( $progress ) {
		var $value      = $progress.find( '.mpc-progress__value' ),
			_value_text = parseInt( $progress.attr( 'data-value-text' ) ),
			_value      = parseInt( $progress.attr( 'data-value' ) ),
			_unit       = $progress.attr( 'data-unit' ),
			_is_icon    = $progress.is( '.mpc-style--style_5, .mpc-style--style_8' ),
			_percent;

		if ( _is_icon ) {
			var $icons = $progress.find( '.mpc-progress__icon-box' );
		}

		if ( ! _value_text ) {
			_value_text = _value;
		}

		$progress.addClass( 'mpc-anim--init' ).velocity( {
			tween: [ 0, _value ]
		}, {
			easing: [ 0.25, 0.1, 0.25, 1.0 ],
			duration: 1500,
			progress: function( elements, complete ) {
				_percent = parseInt( complete * _value_text );

				$value.text( _percent + _unit );

				if ( _is_icon && _value > 0 ) {
					$icons.slice( 0, Math.ceil( complete * _value / 10 ) ).addClass( 'mpc-filled' );
				}

			}
		} );

		$progress.trigger( 'mpc.inited' );
	}

	if ( typeof window.InlineShortcodeView != 'undefined' ) {
		window.InlineShortcodeView_mpc_progress = window.InlineShortcodeView.extend( {
			rendered: function() {
				var $progress = this.$el.find( '.mpc-progress' );

				$progress.addClass( 'mpc-waypoint--init' );

				_mpc_vars.$body.trigger( 'mpc.icon-loaded', [ $progress ] );
				_mpc_vars.$body.trigger( 'mpc.font-loaded', [ $progress ] );
				_mpc_vars.$body.trigger( 'mpc.inited', [ $progress ] );

				init_shortcode( $progress );

				window.InlineShortcodeView_mpc_progress.__super__.rendered.call( this );
			}
		} );
	}

	var $progress_bars = $( '.mpc-progress' );

	$progress_bars.each( function() {
		var $progress = $( this ),
			$parent = $progress.parents( '.mpc-container' );

		if( $parent.length ) {
			$parent.one( 'mpc.parent-init', function() {
				init_shortcode( $progress );
			} );
		} else if ( $progress.is( '.mpc-waypoint--init' ) ) {
			init_shortcode( $progress );
		} else {
			$progress.one( 'mpc.waypoint', function() {
				init_shortcode( $progress );
			} );
		}
	} );
} )( jQuery );
