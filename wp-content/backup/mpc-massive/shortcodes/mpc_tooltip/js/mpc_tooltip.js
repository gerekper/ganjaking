/*----------------------------------------------------------------------------*\
	TOOLTIP SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function position_tooltip( $tooltip, _coords ) {
		var $arrow = $tooltip.find( '.mpc-arrow' );

		if ( _coords.left < 0 || _coords.offset > 0 ) { // Left side
			var _left = _coords.left - _coords.offset;

			if ( _left < 0 ) {
				_coords.offset = _left * -1;

				$tooltip.css( 'margin-left', _coords.offset );
			} else {
				_coords.offset = 0;

				$tooltip.css( 'margin-left', '' );
			}

			if ( $arrow.length ) {
				$arrow.css( 'transform', 'translateX(' + ( - _coords.offset ) + 'px)' );
			}
		} else if ( _coords.right > _mpc_vars.window_width || _coords.offset < 0 ) { // Right side
			var _right = _coords.right - _coords.offset;

			if ( _right > _mpc_vars.window_width ) {
				_coords.offset = _mpc_vars.window_width - _right;

				$tooltip.css( 'margin-left', _coords.offset );
			} else {
				_coords.offset = 0;

				$tooltip.css( 'margin-left', '' );
			}

			if ( $arrow.length ) {
				$arrow.css( 'transform', 'translateX(' + ( - _coords.offset ) + 'px)' );
			}
		}
	}

	function check_position( $tooltip, _coords ) {
		if ( ( _coords.left < 0 || _coords.right > _mpc_vars.window_width ) && $tooltip.is( '.mpc-position--left, .mpc-position--right' ) ) {
			$tooltip.removeClass( 'mpc-position--left mpc-position--right' );
			$tooltip.addClass( 'mpc-position--top' );

			setTimeout( function() {
				_coords = $tooltip[ 0 ].getBoundingClientRect();
				_coords.offset = 0;

				position_tooltip( $tooltip, _coords );
			}, 500 );
		} else {
			position_tooltip( $tooltip, _coords );
		}
	}

	function init_shortcode( $tooltip ) {
		var _coords;

		$tooltip.imagesLoaded().always( function() {
			if ( ! $tooltip.length ) {
				return;
			}

			if ( $tooltip.is( '.mpc-wide' ) ) {
				if ( $tooltip.width() > 500 ) {
					$tooltip.addClass( 'mpc-wrap-content' );
				}
			} else if ( $tooltip.width() > 300 ) {
				$tooltip.addClass( 'mpc-wrap-content' );
			}

			$tooltip.addClass( 'mpc-loaded' );

			_coords = $tooltip[ 0 ].getBoundingClientRect();
			_coords.offset = 0;

			check_position( $tooltip, _coords );
		} );

		if ( ! $tooltip.is( '.mpc-no-arrow' ) && $tooltip.css( 'border-width' ) != '0px' ) {
			var $arrow = $tooltip.find( '.mpc-arrow' );

			if ( $tooltip.is( '.mpc-position--top' ) ) {
				$arrow.css( 'margin-bottom', '-' + $tooltip.css( 'border-bottom-width' ) );
			} else if ( $tooltip.is( '.mpc-position--bottom' ) ) {
				$arrow.css( 'margin-top', '-' + $tooltip.css( 'border-top-width' ) );
			} else if ( $tooltip.is( '.mpc-position--left' ) ) {
				$arrow.css( 'margin-right', '-' + $tooltip.css( 'border-right-width' ) );
			} else if ( $tooltip.is( '.mpc-position--right' ) ) {
				$arrow.css( 'margin-left', '-' + $tooltip.css( 'border-left-width' ) );
			}
		}

		if ( $tooltip.find( 'iframe' ).length ) {
			var $iframe = $tooltip.find( 'iframe' );

			$iframe.wrap( '<div class="mpc-embed-wrap" />' );

			$tooltip.addClass( 'mpc-wrap-content' );
		}

		$tooltip.trigger( 'mpc.inited' );

		_mpc_vars.$window.on( 'load mpc.resize', function() {
			$tooltip.removeClass( 'mpc-loaded' );

			setTimeout( function() {
				if ( $tooltip.is( '.mpc-wide' ) ) {
					if ( $tooltip.width() > 500 ) {
						$tooltip.addClass( 'mpc-wrap-content' );
					}
				} else if ( $tooltip.width() > 300 ) {
					$tooltip.addClass( 'mpc-wrap-content' );
				}

				$tooltip.addClass( 'mpc-loaded' );

				if ( _coords != undefined ) {
					var _offset = _coords.offset;

					_coords = $tooltip[ 0 ].getBoundingClientRect();
					_coords.offset = _offset;

					check_position( $tooltip, _coords );
				}
			}, 10 );

		} );

		if( $tooltip.is( '.mpc-trigger--click' ) ) {
			$tooltip.parent( '.mpc-tooltip-wrap' ).on( 'click', function( _ev ) {
				_ev.preventDefault();
				$tooltip.toggleClass( 'mpc-triggered' );
			});
		}
	}

	if ( typeof window.InlineShortcodeView != 'undefined' ) {
		window.InlineShortcodeView_mpc_tooltip = window.InlineShortcodeView.extend( {
			rendered: function() {
				var $tooltip = this.$el.find( '.mpc-tooltip' );

				$tooltip.addClass( 'mpc-waypoint--init' );

				_mpc_vars.$body.trigger( 'mpc.font-loaded', [ $tooltip ] );
				_mpc_vars.$body.trigger( 'mpc.inited', [ $tooltip ] );

				init_shortcode( $tooltip );

				window.InlineShortcodeView_mpc_tooltip.__super__.rendered.call( this );
			}
		} );

		_mpc_vars.$document.on( 'mpc.init-tooltip', function( event, $tooltip ) {
			init_shortcode( $tooltip );
		} );
	}

	var $tooltips = $( '.mpc-tooltip' );

	$tooltips.each( function() {
		var $tooltip = $( this );

		$tooltip.one( 'mpc.init', function () {
			init_shortcode( $tooltip );
		} );
	} );
} )( jQuery );
