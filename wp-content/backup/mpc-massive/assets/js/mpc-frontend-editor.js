/*----------------------------------------------------------------------------*\
	ICON
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function check_loaded_icons() {
		for ( var i = 0; i < _icons_list.length; i++ ) {
			if ( $( '#mpc_icons_font-' + _icons_list[ i ] + '-css' ).length > 0 ) {
				_icons_fonts[ _icons_list[ i ] ] = true;
			}
		}
	}

	function check_used_icons( $icons ) {
		for ( var i = 0; i < _icons_list.length; i++ ) {
			if ( _icons_fonts[ _icons_list[ i ] ] == false && $icons.filter( '.' + _icons_list[ i ] ).length > 0 ) {
				_icons_fonts[ _icons_list[ i ] ] = true;

				add_new_icons( _icons_list[ i ] );
			}
		}
	}

	function add_new_icons( icon ) {
		$( 'head' ).append( '<link rel="stylesheet" id="mpc_icons_font-' + icon + '-css" href="' + _icons_path + icon + '/' + icon + '.css?ver=1.0.0" type="text/css" media="all">' );
	}

	var $parent = parent.jQuery( 'body' ),
		_icons_path = _mpc_frontend.path + '/assets/fonts/',
		_icons_list = [ 'fa', 'eti', 'etl', 'el', 'mi', 'mpci', 'typcn', 'dashicons' ],
		_icons_fonts = {
			'fa':        false,
			'eti':       false,
			'etl':       false,
			'el':        false,
			'mi':        false,
			'mpci':      false,
			'typcn':     false,
			'dashicons': false
		};

	check_loaded_icons();

	$parent.on( 'mpc.icon-loaded', function( event, $target ) {
		var $icons = $target.closest( '.vc_element' ).find( '.fa, .eti, .etl, .el, .mi, .mpci, .typcn, .dashicons' );

		check_used_icons( $icons );
	} );
} )( jQuery );

/*----------------------------------------------------------------------------*\
	TYPOGRAPHY
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function check_used_fonts( $fonts ) {
		$fonts.each( function() {
			var $this = $( this ),
				_classes = $this.attr( 'class' ).split( ' ' );

			for ( var i = 0; i < _classes.length; i++ ) {
				if ( _classes[ i ].indexOf( 'mpc-typography--' ) === 0 ) {
					var _name = _classes[ i ].replace( 'mpc-typography--', '' );

					if ( $( '#mpc-typography--' + _name ).length === 0 && _fonts.indexOf( _name ) == -1 ) {
						_fonts.push( _name );

						add_new_fonts( _name );
					}
				}
			}
		} );
	}

	function add_new_fonts( font ) {
		$.post( _mpc_frontend.ajaxurl, {
			action:     'mpc_get_typography',
			typography: font
		}, function( response ) {
			if ( response != 'error' && response != '' ) {
				$( '#mpc-typography--' + font ).remove();

				$( 'head' ).append( response );
			}
		} );
	}

	var $parent = parent.jQuery( 'body' ),
		_fonts  = [];

	$parent.on( 'mpc.font-loaded', function( event, $target ) {
		var $fonts = $target.closest( '.vc_element' ).find( '[class*="mpc-typography--"]' );

		check_used_fonts( $fonts );
	} ).on( 'mpc.font-edited mpc.font-added', function( event, name ) {
		add_new_fonts( name );
	} );
} )( jQuery );

/*----------------------------------------------------------------------------*\
	NAVIGATION
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function check_used_navigations( $navigations ) {
		$navigations.each( function() {
			var $this = $( this ),
				_classes = $this.attr( 'class' ).split( ' ' );

			for ( var i = 0; i < _classes.length; i++ ) {
				if ( _classes[ i ].indexOf( 'mpc-nav-preset--' ) === 0 ) {
					var _name = _classes[ i ].replace( 'mpc-nav-preset--', '' );

					if ( $( '#mpc-nav-preset--' + _name ).length === 0 && _navigations.indexOf( _name ) == -1 ) {
						_navigations.push( _name );

						add_new_navigations( _name );
					}
				}
			}
		} );
	}

	function add_new_navigations( navigation ) {
		$.post( _mpc_frontend.ajaxurl, {
			action:     'mpc_get_navigation',
			navigation: navigation
		}, function( response ) {
			if ( response != 'error' && response != '' ) {
				$( '#mpc-nav-preset--' + navigation ).remove();

				$( 'head' ).append( response );
			}
		} );
	}

	var $parent      = parent.jQuery( 'body' ),
		_navigations = [];

	$parent.on( 'mpc.navigation-loaded', function( event, $target ) {
		var $navigations = $target.closest( '.vc_element' ).find( '[class*="mpc-nav-preset--"]' );

		check_used_navigations( $navigations );
	} ).on( 'mpc.navigation-edited mpc.navigation-added', function( event, name ) {
		add_new_navigations( name );
	} );
} )( jQuery );

/*----------------------------------------------------------------------------*\
	PAGINATION
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function check_used_paginations( $paginations ) {
		$paginations.each( function() {
			var $this = $( this ),
				_classes = $this.attr( 'class' ).split( ' ' );

			for ( var i = 0; i < _classes.length; i++ ) {
				if ( _classes[ i ].indexOf( 'mpc-pagination-preset--' ) === 0 ) {
					var _name = _classes[ i ].replace( 'mpc-pagination-preset--', '' );

					if ( $( '#mpc-pagination-preset--' + _name ).length === 0 && _paginations.indexOf( _name ) == -1 ) {
						_paginations.push( _name );

						add_new_paginations( _name );
					}
				}
			}
		} );
	}

	function add_new_paginations( pagination ) {
		$.post( _mpc_frontend.ajaxurl, {
			action:     'mpc_get_pagination',
			pagination: pagination
		}, function( response ) {
			if ( response != 'error' && response != '' ) {
				$( '#mpc-pagination-preset--' + pagination ).remove();

				$( 'head' ).append( response );
			}
		} );
	}

	var $parent      = parent.jQuery( 'body' ),
		_paginations = [];

	$parent.on( 'mpc.pagination-loaded', function( event, $target ) {
		var $paginations = $target.closest( '.vc_element' ).find( '[class*="mpc-pagination-preset--"]' );

		check_used_paginations( $paginations );
	} ).on( 'mpc.pagination-edited mpc.pagination-added', function( event, name ) {
		add_new_paginations( name );
	} );
} )( jQuery );

/*----------------------------------------------------------------------------*\
	INIT
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function init_navigation( $carousel, $navigation ) {
		if( $carousel.is( '.mpc-carousel--stretched' ) ) {
			if( !$navigation.is( '.mpc-navigation--style_1' ) && !$navigation.is( '.mpc-navigation--style_2' ) ) {
				var	$nav_items = $navigation.parents( '.mpc-carousel__wrapper' ).find( '.mpc-navigation' ),
				       _stretched_size = $carousel.width(),
				       _window_size = $( window ).width();

				$nav_items.first().css( 'margin-left', - ( ( _window_size - _stretched_size ) * .5 ) );
				$nav_items.last().css( 'margin-right', - ( ( _window_size - _stretched_size ) * .5 ) );

				$nav_items.trigger( 'mpc.inited' );
			} else {
				$navigation.trigger( 'mpc.inited' );
			}
		} else {
			$navigation.trigger( 'mpc.inited' );
		}
	}

	function init_tooltip( $tooltip ) {
		if ( $tooltip.width() > 300 ) {
			$tooltip.addClass( 'mpc-large' );
		}

		$tooltip.trigger( 'mpc.inited' );
	}

	function init_animation( $item, _animation_in, _animation_loop, _animation_hover ) {
		if ( _animation_in != '' ) {
			$item.MPCwaypoint( function( direction ) {
				$item.velocity( _animation_in[ 0 ], {
					duration: parseInt( _animation_loop[ 1 ] ),
					display:  null
				} );

				loop_item( $item, _animation_loop, _animation_hover );

				this.destroy();
			}, {
				offset: parseInt( _animation_in[ 2 ] ) + '%'
			} );
		} else {
			$item.css( 'opacity', 1 );

			loop_item( $item, _animation_loop, _animation_hover );
		}
	}

	function loop_effect( $item, _effect, _duration, _delay, _hover ) {
		if ( _hover && $item._hover ) {
			setTimeout( function() {
				loop_effect( $item, _effect, _duration, _delay, _hover );
			}, _delay );
		} else {
			$item.velocity( _effect, {
				duration: _duration,
				display:  null,
				complete: function() {
					setTimeout( function() {
						loop_effect( $item, _effect, _duration, _delay, _hover );
					}, _delay );
				}
			} );
		}
	}

	function loop_item( $item, _animation_loop, _animation_hover ) {
		if ( _animation_loop != '' ) {
			if ( parseInt( _animation_loop[ 2 ] ) == 0 ) {
				$item.velocity( _animation_loop[ 0 ], {
					duration: parseInt( _animation_loop[ 1 ] ),
					display:  null
				} );
			} else {
				if ( _animation_hover ) {
					$item.on( 'mouseenter', function() {
						$item._hover = true;
					} ).on( 'mouseleave', function() {
						$item._hover = false;
					} );
				}

				loop_effect( $item, _animation_loop[ 0 ], parseInt( _animation_loop[ 1 ] ), parseInt( _animation_loop[ 2 ] ), _animation_hover );
			}
		}
	}

	function prepare_animation( $item ) {
		var _animation_in    = $item.attr( 'data-animation-in' ),
		    _animation_loop  = $item.attr( 'data-animation-loop' ),
		    _animation_hover = $item.attr( 'data-animation-hover' );

		_animation_in = typeof _animation_in != 'undefined' ? _animation_in.split( '||' ) : '';
		_animation_loop = typeof _animation_loop != 'undefined' ? _animation_loop.split( '||' ) : '';
		_animation_hover = typeof _animation_hover != 'undefined';

		$item.on( 'mpc.inited', function () {
			init_animation( $item, _animation_in, _animation_loop, _animation_hover );
		} );
	}

	var $parent = parent.jQuery( 'body' );

	$parent.on( 'mpc.inited', function( _ev, $el, $extra ) {
		var $integrated = $el.find( '.mpc-init:not(.mpc-tooltip)' );

		/* Integrated Shortcodes */
		$integrated.each( function() {
			var $this = $( this );

			if( $this.is( '.mpc-animation' ) ) {
				prepare_animation( $this );
			}

			$this
				.addClass( 'mpc-inited' )
				.removeClass( 'mpc-init' )
				.trigger( 'mpc.inited' );
		} );

		/* This Shortcode */
		$el
			.addClass( 'mpc-inited' )
			.removeClass( 'mpc-init' );

		if( $el.is( '.mpc-animation' ) ) {
			prepare_animation( $el );
		}

		/* Extra Shortcode - Navigation, Paginations, Ribbons, Tooltips */
		if( typeof $extra != 'undefined' ) {
			$extra
				.addClass( 'mpc-inited' )
				.removeClass( 'mpc-init' );

			if( $extra.is( '.mpc-navigation' ) ) {
				init_navigation( $el, $extra );
			} else if( $extra.is( '.mpc-pagination' ) ) {

			} else if( $extra.is( '.mpc-tooltip' ) ) {
				init_tooltip( $extra );
			}
		}
	} );

} )( jQuery );
