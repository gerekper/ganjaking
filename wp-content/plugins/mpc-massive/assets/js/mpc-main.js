/*----------------------------------------------------------------------------*\
	MAIN
\*----------------------------------------------------------------------------*/

/*
 Init rework plan:
 ToDo: create separate init functions - default init, onload init && waypoints init
 ToDo: trigger inits only if shortcode needs it
 ToDo: check the results of Page onLoad time in gtmetrix - Kiwi Studio = 6s, should be <1s
 ToDo: dont create waypoints for shortcodes without animation/waypoint init

 Target: reduce page load time and increase the performance
 */

/*----------------------------------------------------------------------------*\
	INIT
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	var $inits = $( '.mpc-init' ),
		$rows  = $( '.mpc-row.mpc-animation, .mpc-column.mpc-animation, .mpc-toggle-row.mpc-animation' );

	_mpc_vars.$document.ready( function() {
		$inits.trigger( 'mpc.init' );

		$rows.trigger( 'mpc.inited' );
	});

	$inits.one( 'mpc.inited', function( event ) {
		event.stopPropagation();

		var $this = $( this );

		if( ! $this.is( '.mpc-animation' ) ) {
			$this
				.velocity( {
					opacity: 1
				}, {
					duration: 250,
					delay: 100,
					begin: function() {
						$this
							.addClass( 'mpc-inited' )
							.removeClass( 'mpc-init' );
					}
				});
		} else {
			$this
				.addClass( 'mpc-inited' )
				.removeClass( 'mpc-init' );
		}
	});

} )( jQuery );

/*----------------------------------------------------------------------------*\
 NEW INIT - testing with Counter
\*----------------------------------------------------------------------------*/

// function mpc_init_class( $el ) {
// 	$el.addClass( 'mpc-inited' ).removeClass( 'mpc-init' );
// }

/*----------------------------------------------------------------------------*\
	ANIMATIONS
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function force_animation( $item, _animation_in, _animation_loop, _animation_hover ) {
		if ( _animation_in != '' && ( ! _mpc_vars.breakpoints.custom( '(max-width: 768px)' ) || _mpc_vars.animations ) ) {
			$item.velocity( _animation_in[ 0 ], {
				duration: parseInt( _animation_in[ 1 ] ),
				delay:    parseInt( _animation_in[ 2 ] ),
				display:  null,
				complete: function() {
					$item.css( 'transform', '' );
				}
			} );

			loop_item( $item, _animation_loop, _animation_hover );
		} else {
			$item.css( 'opacity', 1 );

			loop_item( $item, _animation_loop, _animation_hover );
		}
	}

	function init_animation( $item, _animation_in, _animation_loop, _animation_hover ) {
		if ( _animation_in != '' && ( ! _mpc_vars.breakpoints.custom( '(max-width: 768px)' ) || _mpc_vars.animations ) ) {
			var _item = new MPCwaypoint( {
				element: $item[ 0 ],
				handler: function() {
					$item.velocity( _animation_in[ 0 ], {
						duration: parseInt( _animation_in[ 1 ] ),
						delay:    parseInt( _animation_in[ 2 ] ),
						display:  null,
						complete: function() {
							$item.css( 'transform', '' );
						}
					} );

					loop_item( $item, _animation_loop, _animation_hover );

					if ( this.destroy !== undefined ) {
						this.destroy();
					}
				},
				offset: parseInt( _animation_in[ 3 ] ) + '%'
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

	$( 'body' ).addClass( 'mpc-loaded' );

	var $animated_items = $( '.mpc-animation' );

	$animated_items.each( function() {
		var $item            = $( this ),
			_animation_in    = $item.attr( 'data-animation-in' ),
			_animation_loop  = $item.attr( 'data-animation-loop' ),
			_animation_hover = $item.attr( 'data-animation-hover' );

		_animation_in    = typeof _animation_in != 'undefined' ? _animation_in.split( '||' ) : '';
		_animation_loop  = typeof _animation_loop != 'undefined' ? _animation_loop.split( '||' ) : '';
		_animation_hover = typeof _animation_hover != 'undefined';

		$item.one( 'mpc.inited', function() {
			init_animation( $item, _animation_in, _animation_loop, _animation_hover );
		} );

		$item.on( 'mpc.animation', function() {
			force_animation( $item, _animation_in, _animation_loop, _animation_hover );
		} );
	} );
} )( jQuery );

/*----------------------------------------------------------------------------*\
	WAYPOINTS
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	var $waypoints = $( '.mpc-waypoint' );

	$waypoints.each( function() {
		var $waypoint = $( this ),
			_inview = new MPCwaypoint.Inview( {
				element: $waypoint[ 0 ],
				enter: function() {
					$waypoint
						.addClass( 'mpc-waypoint--init' )
						.trigger( 'mpc.waypoint' );

					setTimeout( function() {
						_inview.destroy();
					}, 10 );
				}
			} );
	} );
} )( jQuery );

/*----------------------------------------------------------------------------*\
	RESIZE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	var _resize_timer;

	_mpc_vars.window_width = _mpc_vars.$window.width();
	_mpc_vars.window_height = _mpc_vars.$window.height();

	_mpc_vars.$window.on( 'resize', function() {
		clearTimeout( _resize_timer );

		_resize_timer = setTimeout( function() {
			_mpc_vars.window_width = _mpc_vars.$window.width();
			_mpc_vars.window_height = _mpc_vars.$window.height();

			_mpc_vars.$window.trigger( 'mpc.resize' );
		}, 250 );
	} );
} )( jQuery );

/*----------------------------------------------------------------------------*\
	VC Row Stretch trigger workaround...
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	var $rows = $( '[data-vc-full-width="true"]' );

	function mpc_stretch_row_trigger( $row ) {
		if( $row.attr( 'data-vc-full-width-init' ) == 'true' ) {
			$row.trigger( 'mpc.rowResize' );
		} else {
			setTimeout( function() {
				mpc_stretch_row_trigger( $row );
			}, 250 );
		}
	}

	$.each( $rows, function() {
		mpc_stretch_row_trigger( $( this ) );
	} );

} )( jQuery );

/*----------------------------------------------------------------------------*\
 Magnific Popup init
\*----------------------------------------------------------------------------*/
var mpc_init_lightbox;
( function( $ ) {
	mpc_init_lightbox = function( $element, _is_gallery ) {
		var $lightbox = '',
			_vendor = '';

		if( $element.is( '.mpc-pretty-photo' ) || $element.find( '.mpc-pretty-photo' ).length ) {
			_vendor = 'prettyphoto';
		} else if( $element.is( '.mpc-magnific-popup' ) || $element.find( '.mpc-magnific-popup' ).length ) {
			_vendor = 'magnificPopup';
		}

		if( _vendor == '' ) {
			return;
		}

		if ( $.fn.lightbox ) {
			$element.find( 'a[rel^=mpc]' ).unbind( 'click' );
		}

		if( $.fn.prettyPhoto && _vendor == 'prettyphoto' ) {
			$lightbox = $element.is( '.mpc-pretty-photo' ) ? $element : $element.find( '.mpc-pretty-photo' );

			$lightbox.prettyPhoto( {
				animationSpeed: 'normal',
				padding: 15,
				opacity: 0.7,
				showTitle: true,
				allowresize: false,
				hideflash: true,
				modal: false,
				social_tools: '',
				overlay_gallery: false,
				deeplinking: false,
				ie6_fallback: false
			} );
		} else if( $.fn.magnificPopup && _vendor == 'magnificPopup' ) {
			$lightbox = $element.is( '.mpc-magnific-popup' ) ? $element : $element.find( '.mpc-magnific-popup' );

			var _type = /(\.gif|\.jpg|\.jpeg|\.tiff|\.png|lightbox_src)/i.test( $lightbox.attr( 'href' ) ) ? 'image' : 'iframe',
				_atts;

			_atts = {
				type: _type,
				closeOnContentClick: true,
				mainClass:           'mfp-img-mobile',
				image:               {
					verticalFit: true
				},
				callbacks:           {
					beforeOpen: function() {
						_mpc_vars.$window.trigger( 'mpc.lightbox.open' );
					},
					afterClose: function() {
						_mpc_vars.$window.trigger( 'mpc.lightbox.close' );
					}
				},
				iframe: {
					patterns:  {
						youtube: {
							id: function( _url ) {
								var _re = /https?:\/\/(?:[0-9A-Z-]+\.)?(?:youtu\.be\/|youtube(?:-nocookie)?\.com\S*?[^\w\s-])([\w-]{11})(?=[^\w-]|$)(?![?=&+%\w.-]*(?:['"][^<>]*>|<\/a>))([?=&+%\w.-]*)/ig;

								var _id =  _re.exec( _url );
								_url = typeof _id[ 1 ] !== typeof undefined ? _id[ 1 ] : '';

								if( _url !== '' && typeof _id[ 2 ] !== typeof undefined ) {
									_url += _id[ 2 ][ 0 ] == '&' ? _id[ 2 ].replace( '&', '?' ) : _id[ 2 ];
								}

								return _url;
							},
							src: '//www.youtube.com/embed/%id%?autoplay=1' // URL that will be set as a source for iframe.
						},
						vimeo: {
							id: function( _url ) {
								var _re = /https?:\/\/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)(?:$|\/|\?)/ig;

								var _id =  _re.exec( _url );
								_url = typeof _id[ 3 ] !== typeof undefined ? _id[ 3 ] : '';

								return _url;
							},
							src: '//player.vimeo.com/video/%id%?autoplay=1' // URL that will be set as a source for iframe.
						}
					}
				}
			};

			if( _is_gallery ) {
				_atts.gallery = {
					enabled: true,
					preload: [0,1],
					tCounter: ''
				}
			}

			$lightbox.magnificPopup( _atts );
		} else {
			setTimeout( mpc_init_lightbox( $element, _is_gallery ), 250 );
		}
	};
} )( jQuery );

/*----------------------------------------------------------------------------*\
 MPC JS linking
 \*----------------------------------------------------------------------------*/
(function( $ ) {
	"use strict";

	function mpc_js_link_trigger( $link, event ) {

		var _link = $link.attr( 'data-mpc_link' );

		if ( event.target !== $link[ 0 ] ) {
			return;
		}

		if ( typeof _link !== 'undefined' && _link !== false && _link !== '' ) {
			window.location.href = _link;
		}
	}

	$( document ).on( 'click', '[class^="mpc-"][data-mpc_link]', function( event ) {
		mpc_js_link_trigger( $( this ), event );
	} );

})( jQuery );

/*----------------------------------------------------------------------------*\
 MPC Background images lazyload
 \*----------------------------------------------------------------------------*/
 (function( $ ) {
	"use strict";

	var $image_backgrounds = $( '[class^="mpc-"][data-mpc_src]' ),
		$iframe            = $( '#vc_inline-frame' ),
		$infinite_sliders  = $( '[data-mpcslick^=\'{"infinite":true\']' ),
		_win_height        = document.documentElement.clientHeight || document.body.clientHeight,
		_win_width         = document.documentElement.clientWidth || document.body.clientWidth;

	function mpc_background_image_load( $background_elements) {
		$background_elements.each( function(){
			var $background = $( this ),
				_cache_src  = $background.attr( 'data-mpc_src' );

			if ( _cache_src !== '' && _cache_src !== 'loaded' ) {
				var _threshold     = 200,
					element_rect   = $background[ 0 ].getBoundingClientRect();

				if ( _win_height - element_rect.top + _threshold > 0 && element_rect.top !== 0 && _win_width - element_rect.left > 0 ) {
					$background.attr( 'data-mpc_src', 'loaded' );
					$background[ 0 ].style.backgroundImage = 'url(' + _cache_src + ')';
				}
			}
		} );
	}

	if ( $image_backgrounds.length ) {

		window.addEventListener( 'load', function(){
			mpc_background_image_load( $image_backgrounds );
		} );

		window.addEventListener( 'resize', function(){
			_win_height = document.documentElement.clientHeight || document.body.clientHeight;
			_win_width  = document.documentElement.clientWidth || document.body.clientWidth;
			mpc_background_image_load( $image_backgrounds );
		} );

		window.addEventListener( 'scroll', function(){
			mpc_background_image_load( $image_backgrounds );
		} );
	}

	if ( $image_backgrounds.length && $infinite_sliders.length ) {
		$infinite_sliders.on( 'afterChange', function( slick, currentSlide ){
			$image_backgrounds = $( '[class^="mpc-"][data-mpc_src]' );
			mpc_background_image_load( $image_backgrounds );
		});
	}

	// Inline editor support
	if ( $iframe.length ) {
		$iframe.load( function(){

			var $iframe_DOM = $iframe.contents();

			$image_backgrounds = $iframe_DOM.find( '[class^="mpc-"][data-mpc_src]' );

			$iframe[ 0 ].contentWindow.addEventListener( 'load', function(){
				mpc_background_image_load( $image_backgrounds );
			} );

			$iframe[ 0 ].contentWindow.addEventListener( 'resize', function(){
				mpc_background_image_load( $image_backgrounds );
			} );

			$iframe[ 0 ].contentWindow.addEventListener( 'scroll', function(){
				mpc_background_image_load( $image_backgrounds );
			} );

			$iframe_DOM.on( 'mpc.loaded mpc.inited', function() {
				$image_backgrounds = $iframe_DOM.find( '[class^="mpc-"][data-mpc_src]' );

				mpc_background_image_load( $image_backgrounds );
			} );
		} );
	}

	// Trigger lazy load after grid load or carousel init
	$( document ).on( 'mpc.loaded mpc.inited', function() {
		$image_backgrounds = $( '[class^="mpc-"][data-mpc_src]' );

		mpc_background_image_load( $image_backgrounds );
	} );
})( jQuery );