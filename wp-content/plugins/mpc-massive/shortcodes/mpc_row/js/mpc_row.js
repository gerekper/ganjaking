/*----------------------------------------------------------------------------*\
	ROW SHORTCODE
\*----------------------------------------------------------------------------*/

/* Scroll to ID */
(function( $ ) {
	"use strict";

	function smooth_scroll( event ) {
		event.preventDefault();

		event.data.row
			.velocity( 'stop' )
			.velocity( 'scroll', { duration: 500, easing: 'easeOutExpo' } );
	}

	if( typeof _mpc_scroll_to_id !== 'undefined' && _mpc_scroll_to_id == true ) {
		var $links = $( 'a[href^="#"], a[href^="' + window.location.origin + window.location.pathname + '#"]' );

		$links.each( function() {
			var $link = $( this ),
				_href = $link.attr( 'href' ).replace( '#', '' );

			if( _href == '' ) {
				return;
			}

			var $row = $( '.mpc-row[id="' + _href + '"]' );

			if( $row.length ) {
				$link.on( 'click', { row: $row }, smooth_scroll );
			}
		} );
	}

})( jQuery );

/* Toggle */
(function( $ ) {
	"use strict";

	function stretch_toggle_row( $toggle_row, $toggable_row ) {
		var _window_size = parseInt( _mpc_vars.$window.width() ),
		    _init_size   = parseInt( $toggle_row.width() );

		if( ( $toggle_row.is( '.mpc-stretch' ) && $toggable_row.attr( 'data-vc-full-width' ) === 'true' ) ||
			( $toggle_row.is( '.mpc-stretch' ) && $toggable_row.attr( 'data-mk-full-width' ) === 'true' ) ) { // Jupiter theme support

			$toggle_row.find( '.mpc-toggle-row__content' ).css( 'max-width', _init_size );

			$toggle_row.css( {
				'margin-left':  ( _window_size - _init_size ) * -0.5,
				'margin-right': ( _window_size - _init_size ) * -0.5
			} );
		}
	}

	var $toggle_rows = $( '.mpc-toggle-row' );

	$toggle_rows.each( function() {
		var $toggle_row   = $( this ),
		    _row_id       = $toggle_row.attr( 'id' ),
		    $toggable_row = $( '.mpc-row[data-row-id="' + _row_id + '"]' ),
		    _loaded       = false,
		    _row_height;

		stretch_toggle_row( $toggle_row, $toggable_row );

		_mpc_vars.$window.on( 'mpc.resize', function() {
			_row_height = parseInt( $toggable_row[ 0 ].scrollHeight );

			$toggable_row.attr( 'data-height', _row_height );

			if( $toggable_row.is( '.mpc-toggled' ) ) {
				$toggable_row.css( 'max-height', _row_height );
			}

			stretch_toggle_row( $toggle_row, $toggable_row );
		} );

		$toggable_row.imagesLoaded().always( function() {
			_row_height = parseInt( $toggable_row[ 0 ].scrollHeight );

			$toggable_row.attr( 'data-height', _row_height );

			_loaded = true;

			if( !$toggable_row.is( '.mpc-toggled' ) ) {
				$toggable_row.css( 'max-height', 0 );
			} else {
				$toggable_row.css( 'max-height', _row_height );
			}
		} );

		$toggable_row.on( 'mpc.recalc', function() {
			_row_height = parseInt( $toggable_row[ 0 ].scrollHeight );

			$toggable_row.attr( 'data-height', _row_height );

			if( $toggable_row.is( '.mpc-toggled' ) ) {
				$toggable_row.velocity( { 'max-height' : _row_height }, { duration: 150 } );
			}
		});

		$toggle_row.on( 'click', function() {
			if( !_loaded ) {
				return;
			}

			if( $toggable_row.is( '.mpc-toggled' ) ) {
				$toggable_row.velocity( 'stop' ).velocity( { 'max-height': 0 }, { duration: 500 } ).removeClass( 'mpc-toggled' );
				$toggle_row.removeClass( 'mpc-toggled' );
			} else {
				$toggable_row.velocity( 'stop' ).velocity( { 'max-height': _row_height }, { duration: 500 } ).addClass( 'mpc-toggled' );
				$toggle_row.addClass( 'mpc-toggled' );
			}
		} );
	} );
})( jQuery );

/* Separator */
(function( $ ) {
	"use strict";

	var $rows = $( '.mpc-row' );

	$rows.each( function() {
		var $row                    = $( this ),
		    $prev_row               = $row.prevAll( '.mpc-row' ).first(),
		    $next_row               = $row.nextAll( '.mpc-row' ).first(),
		    $top_separator          = $row.children( '.mpc-separator.mpc-separator--top' ),
		    $bottom_separator       = $row.children( '.mpc-separator.mpc-separator--bottom' ),
		    _top_separator_color    = $top_separator.attr( 'data-color' ),
		    _bottom_separator_color = $bottom_separator.attr( 'data-color' ),
		    _top_separator_css      = $top_separator.is( '.mpc-separator--css' ),
		    _bottom_separator_css   = $bottom_separator.is( '.mpc-separator--css' );

		if( $top_separator.length && typeof _top_separator_color != 'undefined' ) {
			$top_separator.css( _top_separator_css ? 'border-color' : 'fill', _top_separator_color );
		} else if( $top_separator.length && $prev_row.length ) {
			$top_separator.css( _top_separator_css ? 'border-color' : 'fill', $prev_row.css( 'background-color' ) );
		} else if( $prev_row.length === 0 ) {
			//$row.addClass( 'mpc-first-row' );
		}

		if( $bottom_separator.length && typeof _bottom_separator_color != 'undefined' ) {
			$bottom_separator.css( _bottom_separator_css ? 'border-color' : 'fill', _bottom_separator_color );
		} else if( $bottom_separator.length && $next_row.length ) {
			$bottom_separator.css( _bottom_separator_css ? 'border-color' : 'fill', $next_row.css( 'background-color' ) );
		} else if( $next_row.length === 0 ) {
			//$row.addClass( 'mpc-last-row' );
		}
	} );
})( jQuery );

/* Parallax */
(function( $ ) {
	"use strict";

	function disable_on_mobile() {
		if ( skrollr == undefined ) {
			return;
		}

		var skrollr_instance = skrollr.get();

		if ( _mpc_vars.breakpoints.custom( '(max-width: 992px)' ) && _mpc_vars.parallax != true ) {
			if ( skrollr_instance != undefined ) {
				skrollr_instance.destroy();
			}
		} else {
			if ( skrollr_instance == undefined ) {
				skrollr.init( {
					smoothScrolling: false,
					forceHeight: false,
					mobileCheck: function() {
						return false;
					}
				} );
			}
		}
	}

	_mpc_vars.$window.on( 'load', function() {
		setTimeout( function() {
			if ( skrollr == undefined ) {
				return;
			}

			var skrollr_instance = skrollr.get();

			if ( _mpc_vars.breakpoints.custom( '(max-width: 992px)' ) && _mpc_vars.parallax != true ) {
				if ( skrollr_instance != undefined ) {
					skrollr_instance.destroy();
				}
			} else {
				if ( skrollr_instance != undefined ) {
					skrollr_instance.refresh();
				} else {
					skrollr.init( {
						smoothScrolling: false,
						forceHeight: false,
						mobileCheck: function() {
							return false;
						}
					} );
				}
			}
		}, 10 );
	} ).on( 'mpc.resize', disable_on_mobile );
})( jQuery );

/* Scrolling */
(function( $ ) {
	"use strict";

	function can_scroll_check() {
		if ( _mpc_vars.breakpoints.custom( '(max-width: 992px)' ) && _mpc_vars.animations != true ) {
			_can_scroll = false;
		} else {
			_can_scroll = _focused;
		}
	}

	var _focused    = true,
		_can_scroll = true;

	if ( document.hasFocus != undefined ) {
		_focused = document.hasFocus();
	}

	window.onfocus = function() {
		_focused = _can_scroll = true;
	};

	window.onblur = function() {
		_focused = _can_scroll = false;
	};

	_mpc_vars.$window.on( 'mpc.resize', can_scroll_check );

	can_scroll_check();

	$( '.mpc-overlay.mpc-overlay--scrolling' ).each( function() {
		var $overlay  = $( this ),
		    _self     = this,
		    _speed    = parseInt( $overlay.attr( 'data-speed' ) ),
		    _align    = $overlay.css( 'background-position' ).split( ' ' ),
		    _position = 0;

		if( isNaN( _speed ) ) {
			_speed = 25;
		}

		if( _align[ 1 ] != undefined ) {
			_align = _align[ 1 ];
		} else {
			_align = '50%';
		}

		_self.style.backgroundPosition = _position + 'px ' + _align;

		setTimeout( function() {
			$overlay.addClass( 'mpc-overlay--inited' );
		}, 10 );

		setInterval( function() {
			if ( _can_scroll ) {
				_position += _speed;

				_self.style.backgroundPosition = _position + 'px ' + _align;
			}
		}, 1000 );
	} );
})( jQuery );
