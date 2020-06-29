/*----------------------------------------------------------------------------*\
	ALERT SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function init_shortcode( $alert ) {
		var $dismiss   = $( '.mpc-alert__dismiss[data-alert="' + $alert.data( 'id' ) + '"]' ),
			_frequency = $dismiss.data( 'frequency' ),
			$alert_wrap = $alert;

		if( $alert.parents( '.mpc-alert-wrap' ).length ) {
			$alert_wrap = $alert.parents( '.mpc-alert-wrap' );
		} else if( $alert.parents( '.mpc-ribbon-wrap' ).length ) {
			$alert_wrap = $alert.parents( '.mpc-ribbon-wrap' );
		}

		$dismiss.on( 'click', function() {
			$alert_wrap.css( 'height', $alert_wrap.height() );

			$alert_wrap.velocity( {
				opacity: 0,
				height: 0,
				margin: 0
			}, {
				duration: 250,
				complete: function() {
					$alert_wrap.css( 'display', 'none' );
				}
			} );

			if( _frequency != 'always' ) {
				$.post( _mpc_vars.ajax_url, {
					action:    'mpc_set_alert_cookie',
					id:        $alert.data( 'cookie' ),
					frequency: _frequency
				} );
			}
		});

		$alert.trigger( 'mpc.inited' );
	}

	if ( typeof window.InlineShortcodeView != 'undefined' ) {
		window.InlineShortcodeView_mpc_alert = window.InlineShortcodeView.extend( {
			rendered: function() {
				var $alert = this.$el.find( '.mpc-alert' );

				$alert.addClass( 'mpc-waypoint--init' );

				_mpc_vars.$body.trigger( 'mpc.icon-loaded', [ $alert ] );
				_mpc_vars.$body.trigger( 'mpc.font-loaded', [ $alert ] );
				_mpc_vars.$body.trigger( 'mpc.inited', [ $alert ] );

				init_shortcode( $alert );

				window.InlineShortcodeView_mpc_alert.__super__.rendered.call( this );
			},
		} );
	}

	var $alerts = $( '.mpc-alert' );

	$alerts.each( function() {
		var $alert = $( this );

		$alert.one( 'mpc.init', function () {
			init_shortcode( $alert );
		} );
	} );
} )( jQuery );

/*----------------------------------------------------------------------------*\
	ANIMATED TEXT SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function rotate_word( $animated_text, $words, _options ) {
		var $word  = $words.eq( _options[ 'word_index' ] ),
			$block = $word.data( 'parent_block' ),
			_timeout;

		_options[ 'word_index' ] = _options[ 'word_index' ] + 1;

		_timeout = setTimeout( function() {
			if ( ( _options[ 'word_index' ] < _options[ 'words' ] ) ||
				( _options[ 'word_index' ] >= _options[ 'words' ] && _options[ 'loop' ] ) ) {
				if ( _options[ 'word_index' ] >= _options[ 'words' ] && _options[ 'loop' ] ) {
					_options[ 'word_index' ] = 0;
				}

				if ( _options[ 'dynamic' ] ) {
					_options[ 'words_wrap' ].stop( true ).animate( { 'width': $words.eq( _options[ 'word_index' ] ).data( 'width' ) }, _options[ 'duration' ] );
				}

				$block.stop( true ).animate( { 'margin-top': - _options[ 'height' ] }, {
					'duration': _options[ 'duration' ],
					'complete': function() {
						$block
							.appendTo( _options[ 'words_wrap' ] )
							.css( 'margin-top', '' );

						rotate_word( $animated_text, $words, _options );
					}
				} );
			}
		}, _options[ 'delay' ] );

		$animated_text.on( 'mpc.clear', function() {
			clearTimeout( _timeout );
		} );
	}

	function typewrite_word( $animated_text, $words, _options ) {
		var $word       = $words.eq( _options[ 'word_index' ] ),
			_char_index = 0,
			_word       = $word.data( 'clear_text' ),
			_length     = _word.length,
			_write_interval, _erase_interval, _timeout;

		_options[ 'word_index' ] = _options[ 'word_index' ] + 1;

		_write_interval = setInterval( function() {
			$word.text( $word.text() + _word[ _char_index++ ] );

			if ( _char_index >= _length ) {
				clearInterval( _write_interval );

				_timeout = setTimeout( function() {
					if ( ( _options[ 'word_index' ] < _options[ 'words' ] ) || ( _options[ 'word_index' ] >= _options[ 'words' ] && _options[ 'loop' ] ) ) {
						if ( _options[ 'word_index' ] >= _options[ 'words' ] && _options[ 'loop' ] ) {
							_options[ 'word_index' ] = 0;
						}

						_erase_interval = setInterval( function() {
							$word.text( $word.text().slice( 0, -1 ) );
							_char_index--;

							if ( _char_index < 0 ) {
								clearInterval( _erase_interval );

								typewrite_word( $animated_text, $words, _options );
							}
						}, _options[ 'duration' ] / 2 );
					}
				}, _options[ 'delay' ] );
			}
		}, _options[ 'duration' ] );

		$animated_text.on( 'mpc.clear', function() {
			clearInterval( _write_interval );
			clearInterval( _erase_interval );
			clearTimeout( _timeout );
		} );
	}


	function init_shortcode( $animated_text ) {
		var $words_wrap    = $animated_text.find( '.mpc-animated-text' ),
			$words         = $animated_text.find( '.mpc-animated-text__word' ),
			_options       = $animated_text.data( 'options' ),
			_word          = '',
			_longest_word  = '',
			_defaults      = {
				'style':      'rotator',
				'duration':   1000,
				'delay':      1000,
				'loop':       true,
				'dynamic':    false,
				'words':      $words.length,
				'word_index': 0,
				'words_wrap': $words_wrap
			};

		if ( typeof _options == 'undefined' ) {
			_options = _defaults
		} else {
			_options = $.extend( _defaults, _options );

			_options[ 'duration' ] = parseInt( _options[ 'duration' ] );
			_options[ 'delay' ]    = parseInt( _options[ 'delay' ] );
		}

		if ( $words.length == 1 && _options[ 'style' ] == 'rotator' ) {
			return;
		}

		if ( _options[ 'style' ] == 'rotator' ) {
			_options[ 'height' ] = _options[ 'default_height' ] = $words_wrap.height();

			$words_wrap.height( _options[ 'height' ] );

			$animated_text.addClass( 'mpc-loaded' );

			$words.each( function() {
				var $word = $( this );

				$word.data( 'parent_block', $word.parent() );

				if ( _options[ 'dynamic' ] ) {
					$word.data( 'width', $word.width() );
				}
			} );

			if ( _options[ 'dynamic' ] ) {
				$words_wrap.stop( true ).animate( { 'width': $words.eq( 0 ).data( 'width' ) }, _options[ 'duration' ] );

				_mpc_vars.$window.on( 'load', function() {
					$words.each( function() {
						var $word = $( this );

						$word.data( 'width', $word.width() );
					} );
				} );
			}

			rotate_word( $animated_text, $words, _options );

			_mpc_vars.$window.on( 'mpc.resize', function() {
				var _max_height = 0;

				$words.each( function() {
					var $this = $( this );

					if ( $this[ 0 ].scrollHeight > _max_height ) {
						_max_height = $this[ 0 ].scrollHeight;
					}
				} );

				_options[ 'height' ] = _max_height;

				$words.height( _options[ 'height' ] );
				$words_wrap.height( _options[ 'height' ] );
			} );
		} else if ( _options[ 'style' ] == 'typewrite' ) {
			$words.each( function() {
				var $word = $( this );

				_word = $word.text().replace( / /g, '\xa0' ); // non-breaking space

				$word.data( 'clear_text', $word.text() );

				$word.data( 'default_text', _word );

				if ( _longest_word.length < _word.length ) {
					_longest_word = _word.length;
				}
			} );

			$words.text( '' );

			_options[ 'duration' ] = _options[ 'duration' ] / _longest_word;

			typewrite_word( $animated_text, $words, _options );
		}

		$animated_text.trigger( 'mpc.inited' );
	}

	if ( typeof window.InlineShortcodeView != 'undefined' ) {
		window.InlineShortcodeView_mpc_animated_text = window.InlineShortcodeView.extend( {
			rendered: function() {
				var $animated_text = this.$el.find( '.mpc-animated-text-wrap' );

				$animated_text.addClass( 'mpc-waypoint--init' );

				_mpc_vars.$body.trigger( 'mpc.font-loaded', [ $animated_text ] );
				_mpc_vars.$body.trigger( 'mpc.inited', [ $animated_text ] );

				init_shortcode( $animated_text );

				window.InlineShortcodeView_mpc_animated_text.__super__.rendered.call( this );
			},
			beforeUpdate: function () {
				this.$el.find( '.mpc-animated-text-wrap' ).trigger( 'mpc.clear' );
			}
		} );
	}

	var $animated_texts = $( '.mpc-animated-text-wrap' );

	$animated_texts.each( function() {
		var $animated_text = $( this );

		$animated_text.one( 'mpc.init', function () {
			init_shortcode( $animated_text );
		} );
	} );
} )( jQuery );

/*----------------------------------------------------------------------------*\
	BUTTON SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function init_shortcode( $button ) {
		$button.trigger( 'mpc.inited' );
	}

	if ( typeof window.InlineShortcodeView != 'undefined' ) {
		window.InlineShortcodeView_mpc_button = window.InlineShortcodeView.extend( {
			rendered: function() {
				var $button = this.$el.find( '.mpc-button' ),
					$set    = $button.closest( '.vc_element' );

				$button.addClass( 'mpc-waypoint--init' );

				_mpc_vars.$body.trigger( 'mpc.icon-loaded', [ $set ] );
				_mpc_vars.$body.trigger( 'mpc.font-loaded', [ $set ] );
				_mpc_vars.$body.trigger( 'mpc.inited', [ $set ] );

				init_shortcode( $button );

				_mpc_vars.$document.trigger( 'mpc.init-tooltip', [ $button.siblings( '.mpc-tooltip' ) ] );

				window.InlineShortcodeView_mpc_button.__super__.rendered.call( this );
			}
		} );
	}

	var $buttons = $( '.mpc-button' );

	$buttons.each( function() {
		var $button = $( this );

		$button.one( 'mpc.init', function () {
			init_shortcode( $button );
		} );
	} );
} )( jQuery );

/*----------------------------------------------------------------------------*\
 BUTTON SET SHORTCODE
 \*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function switch_style( $button_set ) {
		if ( _mpc_vars.breakpoints.custom( '(max-width: 768px)' ) ) {
			if ( $button_set.is( '.mpc-style--horizontal' ) ) {
				$button_set.removeClass( 'mpc-style--horizontal' ).addClass( 'mpc-style--vertical mpc-style--horizontal-desktop' );
			}
		} else {
			if ( $button_set.is( '.mpc-style--horizontal-desktop' ) ) {
				$button_set.removeClass( 'mpc-style--vertical mpc-style--horizontal-desktop' ).addClass( 'mpc-style--horizontal' );
			}
		}
	}

	function init_shortcode( $button_set ) {
		$button_set.trigger( 'mpc.inited' );

		_mpc_vars.$window.on( 'mpc.resize', function() {
			switch_style( $button_set );
		} );

		switch_style( $button_set );

		if ( $button_set.attr( 'data-animation' ) != undefined ) {
			var $separators = $button_set.find( '.mpc-button-separator' ),
				_animation  = $button_set.attr( 'data-animation' );

			setInterval( function() {
				$separators
					.velocity( 'stop', true )
					.velocity( _animation );
			}, 2500 );
		}
	}

	if ( typeof window.InlineShortcodeViewContainer != 'undefined' ) {
		window.InlineShortcodeView_mpc_button_set = window.InlineShortcodeViewContainer.extend( {
			rendered: function() {
				var $button_set = this.$el.find( '.mpc-button-set' );

				$button_set.addClass( 'mpc-waypoint--init mpc-frontend' );

				_mpc_vars.$body.trigger( 'mpc.icon-loaded', [ $button_set ] );
				_mpc_vars.$body.trigger( 'mpc.font-loaded', [ $button_set ] );
				_mpc_vars.$body.trigger( 'mpc.inited', [ $button_set ] );

				init_shortcode( $button_set );

				window.InlineShortcodeView_mpc_button_set.__super__.rendered.call( this );
			}
		} );
	}

	var $button_sets = $( '.mpc-button-set' );

	$button_sets.each( function() {
		var $button_set = $( this );

		$button_set.find( '.mpc-button-separator-wrap:last-child' ).remove();

		$button_set.one( 'mpc.init', function () {
			init_shortcode( $button_set );
		} );
	} );
} )( jQuery );
/*----------------------------------------------------------------------------*\
	CALLOUT SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function init_shortcode( $callout ) {
		$callout.trigger( 'mpc.inited' );
	}

	if ( typeof window.InlineShortcodeView != 'undefined' ) {
		window.InlineShortcodeView_mpc_callout = window.InlineShortcodeView.extend( {
			rendered: function() {
				var $callout = this.$el.find( '.mpc-callout' );

				$callout.addClass( 'mpc-waypoint--init' );

				_mpc_vars.$body.trigger( 'mpc.icon-loaded', [ $callout ] );
				_mpc_vars.$body.trigger( 'mpc.font-loaded', [ $callout ] );
				_mpc_vars.$body.trigger( 'mpc.inited', [ $callout ] );

				init_shortcode( $callout );

				window.InlineShortcodeView_mpc_callout.__super__.rendered.call( this );
			},
		} );
	}

	var $callouts = $( '.mpc-callout' );

	$callouts.each( function() {
		var $callout = $( this );

		$callout.one( 'mpc.init', function() {
			init_shortcode( $callout );
		} );
	} );
} )( jQuery );

/*----------------------------------------------------------------------------*\
 CAROUSEL ANYTHING SHORTCODE
 \*----------------------------------------------------------------------------*/
(function( $ ) {
	"use strict";

	function wrap_shortcode( $carousel ) {
		$carousel.children().each( function() {
			var $this = $( this );

			$this
				.addClass( 'mpc-init--fast' )
				.wrap( '<div class="mpc-carousel__item-wrapper" />' );

			setTimeout( function() {
				$this.trigger( 'mpc.init-fast' );
			}, 20 );
		} );
	}

	function unwrap_shortcode( $carousel ) {
		$carousel.find( '.vc_element' ).each( function() {
			$( this ).unwrap().unwrap();
		} );
	}

	function get_initial( $carousel ) {
		return Math.random() * $carousel.children().length;
	}

	function delay_init( $carousel ) {
		if( $.fn.mpcslick && !$carousel.is( '.slick-initialized' ) ) {
			var _initial    = $carousel.data( 'slick-random' ) == 'true' ? get_initial( $carousel ) : $carousel.data( 'slick-initial' );

			$carousel.mpcslick( {
				prevArrow: '[data-mpcslider="' + $carousel.attr( 'id' ) + '"] .mpcslick-prev',
				nextArrow: '[data-mpcslider="' + $carousel.attr( 'id' ) + '"] .mpcslick-next',
				adaptiveHeight: true,
				initialSlide: _initial,
				responsive: _mpc_vars.carousel_breakpoints( $carousel ),
				rtl: _mpc_vars.rtl.global()
			} );

			init_shortcode( $carousel );
		} else {
			setTimeout( function() {
				delay_init( $carousel );
			}, 50 );
		}
	}

	function init_shortcode( $carousel ) {
		$carousel.trigger( 'mpc.inited' );
	}

	var $carousels_anything = $( '.mpc-carousel-anything' );

	$carousels_anything.each( function() {
		var $carousel_anything = $( this );

		wrap_shortcode( $carousel_anything );

		$carousel_anything.one( 'mpc.init', function() {
			delay_init( $carousel_anything );
		} );
	} );
})( jQuery );

/*----------------------------------------------------------------------------*\
	CAROUSEL IMAGE SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function carousel_responsive( $carousel ) {
		if( _mpc_vars.breakpoints.custom( '(min-width: 1200px)' ) ) {
			if( $carousel.is( '.mpc-layout--classic' ) ) {
				$carousel.css( 'height', '' );
			} else {
				$carousel.css( 'height', $carousel.attr( 'data-height' ) );
			}

			return;
		}

		var $slides = $carousel.find( 'img' ),
		    _max_height = $carousel.height(),
		    _current_slide = $carousel.mpcslick( 'slickCurrentSlide' );

		$.each( $slides, function() {
			var $slide = $( this ),
			    _ratio = ( $slide.attr( 'height' ) / $slide.attr( 'width' ) ) * 0.9;

			if( $carousel.width() < $slide.width() ) {
				_max_height = Math.min( parseInt( $carousel.width() * _ratio ), _max_height );
			}
		});

		$carousel.css( 'height', _max_height );
		$carousel.mpcslick( 'slickGoTo', _current_slide, true );
	}

	function delay_init( $carousel ) {
		if ( $.fn.mpcslick && ! $carousel.is( '.slick-initialized' ) ) {
			init_shortcode( $carousel );
		} else {
			setTimeout( function() {
				delay_init( $carousel );
			}, 50 );
		}
	}

	function init_shortcode( $carousel ) {
		var $slides = $carousel.find( '.mpc-carousel__item-wrapper' ),
		    _height = 9999;

		if( $carousel.is( '.mpc-layout--fluid' ) && ! $carousel.is( '.mpc-force-height' ) ) {
			$slides.each( function() {
				var _slide_height = parseInt( $( this ).attr( 'data-height' ) );

				if( _slide_height < _height ) {
					_height = _slide_height;
				}
			});

			$carousel.css( { height: _height } ).attr( 'data-height', _height );
		}

		if( $carousel.is( '.mpc-layout--fluid' ) ) {
			var _data = $carousel.data( 'mpcslick' );

			_data.slidesToShow = $slides.length - 1;

			$carousel.attr( 'data-mpcslick', JSON.stringify( _data ) );
		}

		$carousel.on( 'init', function() {
			setTimeout( function(){
				mpc_init_lightbox( $carousel, true );
			}, 250 );
		});

		$carousel.mpcslick({
			prevArrow: '[data-mpcslider="' + $carousel.attr( 'id' ) + '"] .mpcslick-prev',
			nextArrow: '[data-mpcslider="' + $carousel.attr( 'id' ) + '"] .mpcslick-next',
			responsive: _mpc_vars.carousel_breakpoints( $carousel, 2 ),
			rtl: _mpc_vars.rtl.global()
		});

		$carousel.on( 'mouseleave', function() {
			setTimeout( function() {
				var _slick = $carousel.mpcslick( 'getSlick' );

				if( _slick.options.autoplay ) {
					$carousel.mpcslick( 'play' );
				}
			}, 250 );
		});

		carousel_responsive( $carousel );

		_mpc_vars.$window.on( 'mpc.resize', function() {
			if( !$carousel.is( '.slick-slider' ) ) {
				$carousel.mpcslick({
					prevArrow: '[data-mpcslider="' + $carousel.attr( 'id' ) + '"] .mpcslick-prev',
					nextArrow: '[data-mpcslider="' + $carousel.attr( 'id' ) + '"] .mpcslick-next',
					responsive: _mpc_vars.carousel_breakpoints( $carousel ),
					rtl: _mpc_vars.rtl.global()
				});
			}

			setTimeout( function() {
				carousel_responsive( $carousel );
			}, 250 );
		});

		$carousel.trigger( 'mpc.inited' );
	}

	if ( typeof window.InlineShortcodeView != 'undefined' ) {
		window.InlineShortcodeView_mpc_carousel_image = window.InlineShortcodeView.extend( {
			events: {
				'click > .vc_controls .vc_control-btn-delete': 'destroy',
				'click > .vc_controls .vc_control-btn-edit': 'edit',
				'click > .vc_controls .vc_control-btn-clone': 'clone',
				'mousemove': 'checkControlsPosition',
				'click > .mpc-carousel__wrapper .mpcslick-prev .mpc-nav__icon': 'prevSlide',
				'click > .mpc-carousel__wrapper .mpcslick-next .mpc-nav__icon': 'nextSlide'
			},
			rendered: function() {
				var $carousel_image = this.$el.find( '.mpc-carousel-image' ),
				    $navigation = $carousel_image.siblings( '.mpc-navigation' );

				$carousel_image.addClass( 'mpc-waypoint--init' );

				_mpc_vars.$body.trigger( 'mpc.icon-loaded', [ $carousel_image, $navigation ] );
				_mpc_vars.$body.trigger( 'mpc.font-loaded', [ $carousel_image, $navigation ] );
				_mpc_vars.$body.trigger( 'mpc.navigation-loaded', [ $navigation ] );
				_mpc_vars.$body.trigger( 'mpc.inited', [ $carousel_image, $navigation ] );

				setTimeout( function() {
					delay_init( $carousel_image );
				}, 250 );

				window.InlineShortcodeView_mpc_carousel_image.__super__.rendered.call( this );
			},
			beforeUpdate: function() {
				this.$el.find( '.mpc-carousel-image' ).mpcslick( 'unslick' );

				window.InlineShortcodeView_mpc_carousel_image.__super__.beforeUpdate.call( this );
			},
			prevSlide: function() {
				this.$el.find( '.mpc-carousel-image' ).mpcslick( 'slickPrev' );
			},
			nextSlide: function() {
				this.$el.find( '.mpc-carousel-image' ).mpcslick( 'slickNext' );
			}
		} );
	}

	var $carousels_image = $( '.mpc-carousel-image' );

	$carousels_image.each( function() {
		var $carousel_image = $( this );

		$carousel_image.one( 'mpc.init', function() {
			delay_init( $carousel_image );
		} );
	});
} )( jQuery );

/*----------------------------------------------------------------------------*\
	CAROUSEL POSTS SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function delay_init( $carousel ) {
		if ( $.fn.mpcslick && ! $carousel.is( '.slick-initialized' ) ) {
			init_shortcode( $carousel );
		} else {
			setTimeout( function() {
				delay_init( $carousel );
			}, 50 );
		}
	}

	function init_shortcode( $carousel ) {
		$carousel.on( 'init', function() {
			setTimeout( function(){
				mpc_init_lightbox( $carousel, true );
			}, 250 );
		});

		$carousel.mpcslick({
			prevArrow: '[data-mpcslider="' + $carousel.attr( 'id' ) + '"] .mpcslick-prev',
			nextArrow: '[data-mpcslider="' + $carousel.attr( 'id' ) + '"] .mpcslick-next',
			responsive: _mpc_vars.carousel_breakpoints( $carousel ),
			rtl: _mpc_vars.rtl.global()
		});

		$carousel.on( 'mouseleave', function() {
			setTimeout( function() {
				var _slick = $carousel.mpcslick( 'getSlick' );

				if( _slick.options.autoplay ) {
					$carousel.mpcslick( 'play' );
				}
			}, 250 );
		});

		$carousel.trigger( 'mpc.inited' );
	}

	if ( typeof window.InlineShortcodeView != 'undefined' ) {
		window.InlineShortcodeView_mpc_carousel_posts = window.InlineShortcodeView.extend( {
			events: {
				'click > .vc_controls .vc_control-btn-delete': 'destroy',
				'click > .vc_controls .vc_control-btn-edit': 'edit',
				'click > .vc_controls .vc_control-btn-clone': 'clone',
				'mousemove': 'checkControlsPosition',
				'click > .mpc-carousel__wrapper .mpcslick-prev .mpc-nav__icon': 'prevSlide',
				'click > .mpc-carousel__wrapper .mpcslick-next .mpc-nav__icon': 'nextSlide'
			},
			rendered: function() {
				var $carousel_posts = this.$el.find( '.mpc-carousel-posts' ),
				    $navigation = $carousel_posts.siblings( '.mpc-navigation' );

				$carousel_posts.addClass( 'mpc-waypoint--init' );

				_mpc_vars.$body.trigger( 'mpc.icon-loaded', [ $carousel_posts, $navigation ] );
				_mpc_vars.$body.trigger( 'mpc.font-loaded', [ $carousel_posts, $navigation ] );
				_mpc_vars.$body.trigger( 'mpc.navigation-loaded', [ $navigation ] );
				_mpc_vars.$body.trigger( 'mpc.inited', [ $carousel_posts, $navigation ] );

				setTimeout( function() {
					delay_init( $carousel_posts );
				}, 250 );

				window.InlineShortcodeView_mpc_carousel_posts.__super__.rendered.call( this );
			},
			beforeUpdate: function() {
				this.$el.find( '.mpc-carousel-posts' ).mpcslick( 'unslick' );

				window.InlineShortcodeView_mpc_carousel_posts.__super__.beforeUpdate.call( this );
			},
			prevSlide: function() {
				this.$el.find( '.mpc-carousel-posts' ).mpcslick( 'slickPrev' );
			},
			nextSlide: function() {
				this.$el.find( '.mpc-carousel-posts' ).mpcslick( 'slickNext' );
			}
		} );
	}

	var $carousels_posts = $( '.mpc-carousel-posts' );

	$carousels_posts.each( function() {
		var $carousel_posts = $( this );

		$carousel_posts.one( 'mpc.init', function() {
			delay_init( $carousel_posts );
		} );
	});
} )( jQuery );

/*----------------------------------------------------------------------------*\
	CAROUSEL SLIDER SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function carousel_responsive( $carousel ) {
		if( _mpc_vars.breakpoints.custom( '(min-width: 1200px)' ) ) {
			$carousel.css( 'height', '' );
			return;
		}

		var $slides = $carousel.find( 'img' ),
		    _max_height = $carousel.height(),
		    _current_slide = $carousel.mpcslick( 'slickCurrentSlide' );

		$.each( $slides, function() {
			var $slide = $( this ),
			    _ratio = ( $slide.attr( 'height' ) / $slide.attr( 'width' ) ) * 0.9;

			if( $carousel.width() < $slide.width() ) {
				_max_height = Math.min( parseInt( $carousel.width() * _ratio ), _max_height );
			}
		});

		$carousel.css( 'height', _max_height );
		$carousel.mpcslick( 'slickGoTo', _current_slide, true );
	}

	function delay_init( $carousel ) {
		if ( $.fn.mpcslick && ! $carousel.is( '.slick-initialized' ) ) {
			init_shortcode( $carousel );
		} else {
			setTimeout( function() {
				delay_init( $carousel );
			}, 50 );
		}
	}

	function init_shortcode( $carousel ) {
		var $slides = $carousel.find( '.mpc-carousel__item-wrapper' ),
			_data   = $carousel.data( 'mpcslick' );

		$carousel.on( 'afterChange', function( ev, slick, currentSlide ) {
			$carousel.find( '.mpc-carousel__count' ).attr( 'data-current-slide', currentSlide + 1 );
		});

		_data.slidesToShow = $slides.length - 1;

		$carousel.attr( 'data-mpcslick', JSON.stringify( _data ) );

		$carousel.on( 'init', function() {
			setTimeout( function(){
				mpc_init_lightbox( $carousel, true );
			}, 250 );
		});

		$carousel.mpcslick({
			slide: '.mpc-carousel__item-wrapper',
			prevArrow: '[data-mpcslider="' + $carousel.attr( 'id' ) + '"] .mpcslick-prev .mpc-nav__icon',
			nextArrow: '[data-mpcslider="' + $carousel.attr( 'id' ) + '"] .mpcslick-next .mpc-nav__icon',
			rtl: _mpc_vars.rtl.global()
		});

		$carousel.on( 'mouseleave', function() {
			var $this = $( this );

			setTimeout( function() {
				var _slick = $this.mpcslick( 'getSlick' );

				if( _slick.options.autoplay ) {
					$this.mpcslick( 'play' );
				}
			}, 250 );
		});

		carousel_responsive( $carousel );

		_mpc_vars.$window.on( 'resize', function() {
			carousel_responsive( $carousel );
		});

		$carousel.trigger( 'mpc.inited' );
	}

	if ( typeof window.InlineShortcodeView != 'undefined' ) {
		window.InlineShortcodeView_mpc_carousel_slider = window.InlineShortcodeView.extend( {
			events: {
				'click > .vc_controls .vc_control-btn-delete': 'destroy',
				'click > .vc_controls .vc_control-btn-edit': 'edit',
				'click > .vc_controls .vc_control-btn-clone': 'clone',
				'mousemove': 'checkControlsPosition',
				'click > .mpc-carousel__wrapper .mpcslick-prev .mpc-nav__icon': 'prevSlide',
				'click > .mpc-carousel__wrapper .mpcslick-next .mpc-nav__icon': 'nextSlide'
			},
			rendered: function() {
				var $carousel_slider = this.$el.find( '.mpc-carousel-slider' ),
					$navigation = $carousel_slider.siblings( '.mpc-navigation' );

				$carousel_slider.addClass( 'mpc-waypoint--init' );

				_mpc_vars.$body.trigger( 'mpc.icon-loaded', [ $carousel_slider, $navigation ] );
				_mpc_vars.$body.trigger( 'mpc.font-loaded', [ $carousel_slider, $navigation ] );
				_mpc_vars.$body.trigger( 'mpc.navigation-loaded', [ $navigation ] );
				_mpc_vars.$body.trigger( 'mpc.inited', [ $carousel_slider, $navigation ] );

				setTimeout( function() {
					delay_init( $carousel_slider );
				}, 250 );

				window.InlineShortcodeView_mpc_carousel_slider.__super__.rendered.call( this );
			},
			beforeUpdate: function() {
				this.$el.find( '.mpc-carousel-slider' ).mpcslick( 'unslick' );

				window.InlineShortcodeView_mpc_carousel_image.__super__.beforeUpdate.call( this );
			},
			prevSlide: function() {
				this.$el.find( '.mpc-carousel-slider' ).mpcslick( 'slickPrev' );
			},
			nextSlide: function() {
				this.$el.find( '.mpc-carousel-slider' ).mpcslick( 'slickNext' );
			}
		} );
	}

	var $carousels_slider = $( '.mpc-carousel-slider' );

	$carousels_slider.each( function() {
		var $carousel_slider = $( this );

		$carousel_slider.one( 'mpc.init', function() {
			delay_init( $carousel_slider );
		} );
	} );
} )( jQuery );

/*----------------------------------------------------------------------------*\
	CAROUSEL TESTIMONIAL SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function delay_init( $carousel ) {
		if ( $.fn.mpcslick && ! $carousel.is( '.slick-initialized' ) ) {
			init_shortcode( $carousel );
		} else {
			setTimeout( function() {
				delay_init( $carousel );
			}, 50 );
		}
	}

	function init_shortcode( $carousel ) {
		var _selector = ( window.vc_mode == 'admin_frontend_editor' ) ? '[data-tag="mpc_testimonial"]' : 'div';

		$carousel.children( '.mpc-init' ).removeClass( 'mpc-init' );

		if( $carousel.is( '.mpc-carousel--gap' ) ) {
			$carousel.find( '.mpc-testimonial' ).each( function() {
				var $testimonial = $( this );

				if ( ! $testimonial.parent().is( '.mpc-gap' ) ) {
					$testimonial.wrap( '<div class="mpc-gap" />' );
				}
			} );
		}

		$carousel.mpcslick( {
			slide: _selector,
			prevArrow: '[data-mpcslider="' + $carousel.attr( 'id' ) + '"] .mpcslick-prev',
			nextArrow: '[data-mpcslider="' + $carousel.attr( 'id' ) + '"] .mpcslick-next',
			adaptiveHeight: true,
			responsive: _mpc_vars.carousel_breakpoints( $carousel, 1 ),
			rtl: _mpc_vars.rtl.global()
		} );

		$carousel.on( 'mouseleave', function () {
			setTimeout( function () {
				var _slick = $carousel.mpcslick( 'getSlick' );

				if ( _slick.options.autoplay ) {
					$carousel.mpcslick( 'play' );
				}
			}, 250 );
		} );

		$carousel.trigger( 'mpc.inited' );
	}

	if ( typeof window.InlineShortcodeView != 'undefined' ) {
		window.InlineShortcodeView_mpc_carousel_testimonial = window.InlineShortcodeViewContainer.extend( {
			events: {
				'click > .vc_controls .vc_element .vc_control-btn-delete': 'destroy',
				'click > .vc_controls .vc_element .vc_control-btn-edit': 'edit',
				'click > .vc_controls .vc_element .vc_control-btn-clone': 'clone',
				'click > .vc_controls .vc_element .vc_control-btn-prepend': 'prependElement',
				'click > .vc_controls .vc_control-btn-append': 'appendElement',
				'click > .vc_empty-element': 'appendElement',
				'mouseenter': 'resetActive',
				'mouseleave': 'holdActive',
				'click > .mpc-carousel__wrapper .mpcslick-prev .mpc-nav__icon': 'prevSlide',
				'click > .mpc-carousel__wrapper .mpcslick-next .mpc-nav__icon': 'nextSlide'
			},
			rendered: function() {
				var $carousel_testimonial = this.$el.find( '.mpc-carousel-testimonial' ),
				    $navigation = $carousel_testimonial.siblings( '.mpc-navigation' );

				$carousel_testimonial.addClass( 'mpc-waypoint--init' );

				_mpc_vars.$body.trigger( 'mpc.icon-loaded', [ $navigation ] );
				_mpc_vars.$body.trigger( 'mpc.font-loaded', [ $navigation ] );
				_mpc_vars.$body.trigger( 'mpc.navigation-loaded', [ $navigation ] );
				_mpc_vars.$body.trigger( 'mpc.inited', [ $carousel_testimonial, $navigation ] );

				setTimeout( function() {
					delay_init( $carousel_testimonial );
				}, 250 );

				window.InlineShortcodeView_mpc_carousel_testimonial.__super__.rendered.call( this );
			},
			beforeUpdate: function() {
				this.$el.find( '.mpc-carousel-testimonial' ).mpcslick( 'unslick' );

				window.InlineShortcodeView_mpc_carousel_testimonial.__super__.beforeUpdate.call( this );
			},
			prevSlide: function() {
				this.$el.find( '.mpc-carousel-testimonial' ).mpcslick( 'slickPrev' );
			},
			nextSlide: function() {
				this.$el.find( '.mpc-carousel-testimonial' ).mpcslick( 'slickNext' );
			}
		} );
	}

	var $carousels_testimonial = $( '.mpc-carousel-testimonial' );

	$carousels_testimonial.each( function() {
		var $carousel_testimonial = $( this );

		$carousel_testimonial.one( 'mpc.init', function() {
			delay_init( $carousel_testimonial );
		} );
	});
} )( jQuery );

/*----------------------------------------------------------------------------*\
	CHART SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function set_defaults( options, leave_empty ) {
		if ( options.type == undefined ) {
			options.type = 'color';

			if ( leave_empty ) {
				options.color = 'transparent';
			} else {
				options.color = '#aaaaaa';
			}
		} else if ( options.type == 'color' ) {
			if ( options.color == undefined || options.color == '' ) {
				options.type = 'color';

				if ( leave_empty ) {
					options.color = 'transparent';
				} else {
					options.color = '#aaaaaa';
				}
			}
		} else if ( options.type == 'image' ) {
			if ( options.image == undefined || options.image == '' ) {
				options.type = 'color';

				if ( leave_empty ) {
					options.color = 'transparent';
				} else {
					options.color = '#aaaaaa';
				}
			}
		} else if ( options.type == 'gradient' ) {
			if ( options.gradient == undefined || options.gradient == '' ) {
				options.type = 'color';

				if ( leave_empty ) {
					options.color = 'transparent';
				} else {
					options.color = '#aaaaaa';
				}
			}
		}

		return options;
	}

	function create_background( _options, _context, _canvas, $wrap ) {
		var _width = _options.width,
			_x = _canvas.width * .5,
			_y = _canvas.height * .5,
			_radius = _canvas.width * .5;

		if ( _options.type == 'color' ) {
			_options.loaded      = true;
			_options.strokeStyle = _options.color;

			$wrap.trigger( 'mpc.loaded' );
		} else if ( _options.type == 'image' ) {
			var _image = new Image();
			_image.onload = function() {
				_options.loaded      = true;
				_options.strokeStyle = _context.createPattern( _image, _options.repeat );

				$wrap.trigger( 'mpc.loaded' );
			};
			_image.src = _options.image;
		} else if ( _options.type == 'gradient' ) {
			var _values = _options.gradient.split( '||' );

			if ( _values.length == 5 ) {
				var _stops = _values[ 2 ].split( ';' ),
					_gradient;

				if ( _values[ 4 ] == 'linear' ) {
					var _angles = parseInt( _values[ 3 ] ) + 180,
						_start_point = arc_to_coords( _x, _y, _radius + _width * .5, _angles ),
						_end_point   = arc_to_coords( _x, _y, _radius + _width * .5, _angles + 180 > 360 ? _angles - 180 : _angles + 180 );

					_gradient = _context.createLinearGradient( _start_point.x, _start_point.y, _end_point.x, _end_point.y );
				} else {
					_gradient = _context.createRadialGradient( _x, _y, 0, _x, _y, _radius + _width * .5 );
				}

				_gradient.addColorStop( _stops[ 0 ] * .01, _values[ 0 ] );
				_gradient.addColorStop( _stops[ 1 ] * .01, _values[ 1 ] );

				_options.loaded      = true;
				_options.strokeStyle = _gradient;

				$wrap.trigger( 'mpc.loaded' );
			}
		}
	}

	function arc_to_coords( _x, _y, _radius, _angle ) {
		_angle = ( _angle - 90 ) * Math.PI / 180;

		return {
			x: _x + ( _radius * Math.cos( _angle ) ),
			y: _y + ( _radius * Math.sin( _angle ) )
		}
	}

	function animate_chart( $wrap, _context, _canvas, _options ) {
		var _offset = - Math.PI * .5,
			_radians = Math.PI / 180,
			_circle = Math.PI * 2,
			_width = _options.width,
			_x = _canvas.width * .5,
			_y = _canvas.height * .5,
			_radius = _canvas.width * .5 - _width * .5,
			_with_marker = _options.marker !== false,
			_duration = 3000,
			_angle,
			_coords;

		if ( _options.fast != undefined ) {
			_duration = 0;
		}

		$wrap.velocity( {
			tween: [ 0, 1 ]
		}, {
			easing: [ 0.25, 0.1, 0.25, 1.0 ],
			duration: _duration * _options.value,
			progress: function( elements, complete, remaining, start, tweenValue ) {
				_angle  = ( _options.value - _options.value * tweenValue ) * 360;
				_coords = arc_to_coords( _x, _y, _radius, _angle );

				_context.clearRect( 0, 0, _canvas.width, _canvas.height );

				// Background
				_context.lineWidth = _options.width - .5;

				_context.strokeStyle = _options.chart_back.strokeStyle;
				_context.beginPath();
				_context.arc( _x, _y, _radius, 0, _circle, false );
				_context.stroke();

				// Foreground
				_context.lineWidth = _options.width;

				_context.strokeStyle = _options.chart_front.strokeStyle;
				_context.beginPath();
				_context.arc( _x, _y, _radius, _offset, _angle * _radians + _offset, false );
				_context.stroke();

				// Marker
				if ( _with_marker ) {
					_options.marker.css( {
						left: _coords.x,
						top:  _coords.y
					} );
				}
			}
		});
	}

	function init_shortcode( $chart, fast_init ) {
		var $marker  = $chart.find( '.mpc-chart__marker' ),
			$box     = $chart.find( '.mpc-chart__box' ),
			$inner   = $chart.find( '.mpc-chart__inner_circle' ),
			$outer   = $chart.find( '.mpc-chart__outer_circle' ),
			_options = $chart.data( 'options' ),
			_canvas  = $chart.find( 'canvas.mpc-chart' )[ 0 ],
			_context = _canvas.getContext( '2d' ),
			_circles_radius;

		if ( typeof _options == 'undefined') {
			return;
		}

		if ( $chart.is( '.mpc-init--fast' ) || fast_init ) {
			_options.fast = true;
		}

		_options.radius = parseInt( _options.radius );
		_options.value  = parseInt( _options.value ) / 100;
		_options.width  = parseInt( _options.width );

		_options.default_radius = _options.radius;

		if ( $chart.width() < _options.radius * 2 ) {
			_options.radius = Math.floor( $chart.width() / 2 );

			$box.width( _options.radius * 2 );

			if ( $inner.length ) {
				_circles_radius = _options.inner_radius * ( _options.radius / _options.default_radius ) * 2;

				$inner.css( { 'width': _circles_radius, 'height': _circles_radius } );
			}

			if ( $outer.length ) {
				_circles_radius = _options.outer_radius * ( _options.radius / _options.default_radius ) * 2;

				$outer.css( { 'width': _circles_radius, 'height': _circles_radius } );
			}
		}

		if ( _options.width >= _options.radius ) {
			_options.width = _options.radius - 0.01;
		}

		_options.marker = $marker.length ? $marker : false;

		_canvas.setAttribute( 'width', _options.radius * 2 );
		_canvas.setAttribute( 'height', _options.radius * 2 );

		_context.lineWidth = _options.width;

		_options.chart_back  = _options.chart_back != undefined ? _options.chart_back : {};
		_options.chart_front = _options.chart_front != undefined ? _options.chart_front : {};

		_options.chart_back.width  = _options.width;
		_options.chart_back.loaded = false;

		_options.chart_front.width  = _options.width;
		_options.chart_front.loaded = false;

		_options.chart_back  = set_defaults( _options.chart_back, true );
		_options.chart_front = set_defaults( _options.chart_front, false );

		$chart.on( 'mpc.loaded', function() {
			if ( _options.chart_front.loaded && _options.chart_back.loaded ) {
				var $parent   = $chart.parents( '.mpc-container' );

				$chart.trigger( 'mpc.inited' );

				if( $parent.length ) {
					if ( $chart.is( '.mpc-parent--init' ) ) {
						$parent.one( 'mpc.parent-init', function() {
							animate_chart( $box, _context, _canvas, _options );
						} );
					} else {
						animate_chart( $box, _context, _canvas, _options );
					}
				} else if ( $chart.is( '.mpc-waypoint--init' ) ) {
					animate_chart( $box, _context, _canvas, _options );
				} else {
					$chart.one( 'mpc.waypoint', function() {
						animate_chart( $box, _context, _canvas, _options );
					} );
				}
			}
		} );

		create_background( _options.chart_back, _context, _canvas, $chart );
		create_background( _options.chart_front, _context, _canvas, $chart );

		_mpc_vars.$window.on( 'mpc.resize', function() {
			var _radius = _options.radius;

			if ( $chart.width() < _options.radius * 2 ) {
				_options.radius = Math.floor( $chart.width() / 2 );

				$box.width( _options.radius * 2 );
			} else if ( _options.radius < _options.default_radius ) {
				_options.radius = Math.floor( Math.min( $chart.width() / 2, _options.default_radius ) );

				$box.width( _options.radius * 2 );
			}

			if ( _radius != _options.radius ) {
				_options.fast = true;

				_canvas.setAttribute( 'width', _options.radius * 2 );
				_canvas.setAttribute( 'height', _options.radius * 2 );

				animate_chart( $box, _context, _canvas, _options );

				if ( $inner.length ) {
					_circles_radius = _options.inner_radius * ( _options.radius / _options.default_radius ) * 2;

					$inner.css( { 'width': _circles_radius, 'height': _circles_radius } );
				}

				if ( $outer.length ) {
					_circles_radius = _options.outer_radius * ( _options.radius / _options.default_radius ) * 2;

					$outer.css( { 'width': _circles_radius, 'height': _circles_radius } );
				}
			}
		} );
	}

	if ( typeof window.InlineShortcodeView != 'undefined' ) {
		window.InlineShortcodeView_mpc_chart = window.InlineShortcodeView.extend( {
			rendered: function() {
				var $chart = this.$el.find( '.mpc-chart-wrap' );

				$chart.addClass( 'mpc-waypoint--init' );

				_mpc_vars.$body.trigger( 'mpc.icon-loaded', [ $chart ] );
				_mpc_vars.$body.trigger( 'mpc.font-loaded', [ $chart ] );
				_mpc_vars.$body.trigger( 'mpc.inited', [ $chart ] );

				init_shortcode( $chart, false );

				window.InlineShortcodeView_mpc_chart.__super__.rendered.call( this );
			}
		} );
	}

	var $charts = $( '.mpc-chart-wrap' );

	$charts.each( function() {
		var $chart = $( this );

		$chart.one( 'mpc.init', function () {
			init_shortcode( $chart, false );
		} );

		$chart.one( 'mpc.init-fast', function () {
			init_shortcode( $chart.parents( '.mpc-carousel__wrapper' ).find( '.slick-cloned .mpc-chart-wrap[data-id="' + $chart.attr( 'data-id' ) + '"]' ), true );
		} );
	} );
} )( jQuery );

/*----------------------------------------------------------------------------*\
	CIRCLE ICONS SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function arc_to_coords( angle ) {
		angle = ( angle - 90 ) * Math.PI / 180;

		return {
			x: _center + ( _radius * Math.cos( angle ) ),
			y: _center + ( _radius * Math.sin( angle ) )
		}
	}

	function next_slide( $icons, $columns, index, _effect, $circle_icon ) {
		var $active_column = $icons.eq( index ).parents( '.mpc-icon-column' );

		$columns.removeClass( 'mpc-active' );
		$active_column.addClass( 'mpc-active' );

		if ( _effect != undefined ) {
			clearInterval( $circle_icon._effect_interval );

			$active_column.find( '.mpc-icon' )
				.velocity( 'stop', true )
				.velocity( _effect );

			$circle_icon._effect_interval = setInterval( function() {
				$active_column.find( '.mpc-icon' )
					.velocity( 'stop', true )
					.velocity( _effect );
			}, 1200 );
		}

		return ++index;
	}

	function init_shortcode( $circle_icon ) {
		var $icons           = $circle_icon.find( '.mpc-icon' ),
			$columns         = $circle_icon.find( '.mpc-icon-column' ),
			_effect          = $circle_icon.attr( 'data-effect' ),
			_active_icon     = 1,
			_max_icons       = $icons.length,
			_slideshow_delay = 0,
			_angle           = 0,
			_angle_gap       = 360 / $icons.length,
			_interval;

		$circle_icon._effect_interval = null;

		if ( $circle_icon.attr( 'data-active-item' ) != undefined ) {
			_active_icon = parseInt( $circle_icon.attr( 'data-active-item' ) ) - 1;
		}

		if ( $circle_icon.attr( 'data-delay' ) != undefined ) {
			_slideshow_delay = parseInt( $circle_icon.attr( 'data-delay' ) );
			_slideshow_delay = Math.max( Math.min( _slideshow_delay, 15000 ), 1000 );
		}

		$icons.each( function() {
			var $icon   = $( this ),
				_coords = arc_to_coords( _angle );

			$icon.css( {
				left: _coords.x + '%',
				top:  _coords.y + '%'
			} );

			$icon.imagesLoaded().always( function() {
				$icon.css( {
					marginLeft: $icon.outerWidth() * -.5,
					marginTop:  $icon.outerHeight() * -.5
				} );
			} );

			_angle += _angle_gap;
		} );

		_mpc_vars.$window.on( 'load', function() {
			// Added in case of slow icons/images load
			$icons.each( function() {
				var $icon = $( this );

				$icon.css( {
					marginLeft: $icon.outerWidth() * -.5,
					marginTop:  $icon.outerHeight() * -.5
				} );
			} );
		} );

		$icons.on( 'click mouseenter', function() {
			var $active_column = $( this ).parents( '.mpc-icon-column' );

			if ( window.vc_mode == 'admin_frontend_editor' ) {
				$active_column = $( this ).parents( '.vc_mpc_icon_column' );
			}

			next_slide( $icons, $columns, $active_column.index(), _effect, $circle_icon );

			_active_icon = $active_column.index();
		} );

		next_slide( $icons, $columns, _active_icon, _effect, $circle_icon );

		$columns.removeClass( 'mpc-parent-hover' );

		if ( _slideshow_delay != 0 ) {
			_active_icon = ( _active_icon + 1 ) % _max_icons;

			_interval = setInterval( function() {
				_active_icon = next_slide( $icons, $columns, _active_icon, _effect, $circle_icon ) % _max_icons;
			}, _slideshow_delay );

			$circle_icon.on( 'click mouseenter', function() {
				clearInterval( _interval );
			} ).on( 'mouseleave', function() {
				_interval = setInterval( function() {
					_active_icon = next_slide( $icons, $columns, _active_icon, _effect, $circle_icon ) % _max_icons;
				}, _slideshow_delay );
			} );
		}

		$circle_icon.trigger( 'mpc.inited' );
	}

	if ( typeof window.InlineShortcodeViewContainer != 'undefined' ) {
		window.InlineShortcodeView_mpc_circle_icons = window.InlineShortcodeViewContainer.extend( {
			initialize: function( params ) {
				this.listenTo( this.model, 'mpc:forceRender', this.rendered );

				window.InlineShortcodeView_mpc_circle_icons.__super__.initialize.call( this, params );
			},
			rendered: function() {
				var $icons = this.$el.find( '.mpc-circle-icons' );

				$icons.addClass( 'mpc-waypoint--init' );

				_mpc_vars.$body.trigger( 'mpc.icon-loaded', [ $icons ] );
				_mpc_vars.$body.trigger( 'mpc.font-loaded', [ $icons ] );
				_mpc_vars.$body.trigger( 'mpc.inited', [ $icons ] );

				setTimeout( function() {
					init_shortcode( $icons );
				}, 250 );

				window.InlineShortcodeView_mpc_circle_icons.__super__.rendered.call( this );
			}
		} );
	}

	var $circle_icons = $( '.mpc-circle-icons' ),
		_center = 50,
		_radius = 45;

	$circle_icons.each( function() {
		var $circle_icon = $( this );

		$circle_icon.one( 'mpc.init', function () {
			init_shortcode( $circle_icon );
		} );
	} );
} )( jQuery );



/*----------------------------------------------------------------------------*\
	CONNECTED ICONS SHORTCODE
\*----------------------------------------------------------------------------*/
(function( $ ) {
	"use strict";

	function init_connector() {
		return {
			size: {
				width: 0,
				height: 0
			},
			position: {
				top: 0,
				left: 0
			},
			margin: {
				top: 0,
				left: 0
			}
		};
	}

	function get_dimensions( $item ) {
		return {
			size: {
				width: $item.outerWidth( false ),
				height: $item.outerHeight( false )
			},
			border: {
				left:   parseInt( $item.css( 'border-left-width' ).replace( 'px', '' ) ),
				right:  parseInt( $item.css( 'border-right-width' ).replace( 'px', '' ) ),
				top:    parseInt( $item.css( 'border-top-width' ).replace( 'px', '' ) ),
				bottom: parseInt( $item.css( 'border-bottom-width' ).replace( 'px', '' ) )
			},
			offset: $item.offset(),
			position: $item.position()
		};
	}

	function wrap_columns( $icons ) {
		$icons.find( '.mpc-icon-column' ).each( function() {
			$( this ).wrap( '<div class="mpc-connected-icons__item">' );
		} );
	}

	function draw_connections( $icons ) {
		var $line        = $icons.find( '.mpc-connected-icons__line' ),
		    _target      = $icons.attr( 'data-target' ) != '' ? $icons.attr( 'data-target' ) : 'icon',
		    _layout      = $icons.attr( 'data-layout' ) != '' ? $icons.attr( 'data-layout' ) : 'vertical',
		    $target      = _target == 'box' ? $icons.find( '.mpc-icon-column' ) : $icons.find( '.mpc-icon-column > .mpc-icon' ),
		    _items_count = $target.length - 1;

		$target.each( function( _index ) {
			if( _index >= _items_count ) return true;

			var $item        = $( this ),
			    $item_parent = $item.parents( '.mpc-connected-icons__item' ),
			    $item_next   = _target == 'box' ? $item_parent.next().find( '.mpc-icon-column' ) : $item_parent.next().find( '.mpc-icon' ),
			    $connector   = $line.clone(),
			    _line_size   = _layout == 'horizontal' ? $line.height() : $line.width(),
			    _item        = get_dimensions( $item ),
			    _item_next   = get_dimensions( $item_next ),
			    _connector   = init_connector(),
			    _css         = {},
			    _animation   = {};

			if( _layout == 'horizontal' ) {
				_connector.size.width = _item_next.offset.left - _item.offset.left - _item.size.width;

				_connector.margin.left = _item.border.right;
				_connector.position.top = ( _item.size.height - _line_size ) * .5 - _item.border.top;
				_connector.position.left = false;

				_css = {
					top: parseInt( _connector.position.top ),
					width: parseInt( _connector.size.width ),
					marginLeft: parseInt( _connector.margin.left )
				};

				_animation = {
					width: parseInt( _connector.size.width )
				};
			} else {
				_connector.size.height = _item_next.offset.top - _item.offset.top - _item.size.height;

				_connector.margin.top = _item.border.top;
				_connector.position.left = ( _item.size.width - _line_size ) * .5 - _item.border.left;

				_connector.position.top = false;

				_css = {
					left: parseInt( _connector.position.left ),
					height: parseInt( _connector.size.height ),
					marginTop: parseInt( _connector.margin.top )
				};

				_animation = {
					height: parseInt( _connector.size.height )
				};
			}

			$connector.css( _css ).appendTo( $item );
			$connector.find( 'span' ).velocity( _animation, 300 );
		} );
	}

	function responsive( $icons ) {
		var _cols = $icons.data( 'ci-cols' );
		$icons.find( '.mpc-connected-icons__item .mpc-connected-icons__line' ).remove();

		if ( _cols >= 3 && _mpc_vars.breakpoints.custom( '(min-width: 992px)' )
			|| _mpc_vars.breakpoints.custom( '(min-width: 769px)' ) ) {
			draw_connections( $icons );
		}
	}

	function init_shortcode( $icons ) {
		var _cols = $icons.data( 'ci-cols' );
		if ( _cols >= 3 && _mpc_vars.breakpoints.custom( '(min-width: 992px)' )
			|| _mpc_vars.breakpoints.custom( '(min-width: 769px)' ) ) {
			draw_connections( $icons );
		}

		$icons.trigger( 'mpc.inited' );
	}

	function delay_init( $icons ) {
		if ( $.fn.imagesLoaded ) {
			$icons.imagesLoaded().always( function() {
				draw_connections( $icons );
			} );

			$icons.trigger( 'mpc.inited' );
		} else {
			setTimeout( function() {
				delay_init( $icons );
			}, 50 );
		}
	}

	function frontend_wrap_columns( $icons ) {
		$icons.find( '.vc_mpc_icon_column' ).addClass( 'mpc-connected-icons__item' );
	}

	if( typeof window.InlineShortcodeViewContainer != 'undefined' ) {
		var $body = $( 'body' );

		window.InlineShortcodeView_mpc_connected_icons = window.InlineShortcodeViewContainer.extend( {
			initialize: function( params ) {
				_.bindAll( this, 'holdActive' );
				window.InlineShortcodeView_mpc_connected_icons.__super__.initialize.call( this, params );
				this.parent_view = vc.shortcodes.get( this.model.get( 'parent_id' ) ).view;

				this.listenTo( this.model, 'mpcRender', this.rendered );
			},
			rendered: function() {
				var $icons = this.$el.find( '.mpc-connected-icons' );

				$icons.addClass( 'mpc-waypoint--init' );

				$body.trigger( 'mpc.icon-loaded', [ $icons ] );
				$body.trigger( 'mpc.font-loaded', [ $icons ] );
				$body.trigger( 'mpc.inited', [ $icons ] );

				setTimeout( function() {
					frontend_wrap_columns( $icons );
					var _cols = $icons.data( 'ci-cols' );
					if( _cols >= 3 && _mpc_vars.breakpoints.custom( '(min-width: 992px)' )
						|| _mpc_vars.breakpoints.custom( '(min-width: 769px)' ) ) {
						init_shortcode( $icons );
					} else {
						$icons.trigger( 'mpc.inited' );
					}
				}, 250 );

				window.InlineShortcodeView_mpc_connected_icons.__super__.rendered.call( this );
			},
			render: function() {
				window.InlineShortcodeView_mpc_connected_icons.__super__.render.call( this );

				this.content().addClass( 'vc_element-container' );
				this.$el.addClass( 'vc_container-block' );

				return this;
			},
			beforeUpdate: function() {
				this.$el.find( '.mpc-icon-column .mpc-connected-icons__line' ).remove();
			}
		} );
	}

	var $connected_icons = $( '.mpc-connected-icons' );

	wrap_columns( $connected_icons );

	$connected_icons.each( function() {
		var $connected_icon = $( this );

		$connected_icon.one( 'mpc.init', function() {
			if( _mpc_vars.breakpoints.custom( '(min-width: 769px)' ) ) {
				delay_init( $connected_icon );
			} else {
				$connected_icon.trigger( 'mpc.inited' );
			}
		} );
	} );

	_mpc_vars.$window.on( 'mpc.resize', function() {
		$.each( $connected_icons, function() {
			responsive( $( this ) );
		} );
	} );
})( jQuery );



/*----------------------------------------------------------------------------*\
	COUNTDOWN SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function delay_init( $this, options ) {
		if ( $this.countdown ) {
			$this.countdown( {
				until: new Date( options._until ),
				format: options._format,
				layout: options._layout,
				labels: options._labels,
				labels1: options._labels
			} );
		} else {
			setTimeout( function() {
				delay_init( $this, options );
			}, 50 );
		}
	}

	var _top_layout, _bottom_layout;

	_bottom_layout = '{y<}<div class="mpc-countdown__section"><span class="mpc-main"><div><div>{yn}</div></div></span><h4>{yl}</h4></div>{y>}';
	_bottom_layout += '{o<}<div class="mpc-countdown__section"><span class="mpc-main"><div><div>{on}</div></div></span><h4>{ol}</h4></div>{o>}';
	_bottom_layout += '{d<}<div class="mpc-countdown__section"><span class="mpc-main"><div><div>{dn}</div></div></span><h4>{dl}</h4></div>{d>}';
	_bottom_layout += '{h<}<div class="mpc-countdown__section"><span class="mpc-main"><div><div>{hn}</div></div></span><h4>{hl}</h4></div>{h>}';
	_bottom_layout += '{m<}<div class="mpc-countdown__section"><span class="mpc-main"><div><div>{mn}</div></div></span><h4>{ml}</h4></div>{m>}';
	_bottom_layout += '{s<}<div class="mpc-countdown__section"><span class="mpc-main"><div><div>{sn}</div></div></span><h4>{sl}</h4></div>{s>}';

	_top_layout = '{y<}<div class="mpc-countdown__section"><h4>{yl}</h4><span class="mpc-main"><div><div>{yn}</div></div></span></div>{y>}';
	_top_layout += '{o<}<div class="mpc-countdown__section"><h4>{ol}</h4><span class="mpc-main"><div><div>{on}</div></div></span></div>{o>}';
	_top_layout += '{d<}<div class="mpc-countdown__section"><h4>{dl}</h4><span class="mpc-main"><div><div>{dn}</div></div></span></div>{d>}';
	_top_layout += '{h<}<div class="mpc-countdown__section"><h4>{hl}</h4><span class="mpc-main"><div><div>{hn}</div></div></span></div>{h>}';
	_top_layout += '{m<}<div class="mpc-countdown__section"><h4>{ml}</h4><span class="mpc-main"><div><div>{mn}</div></div></span></div>{m>}';
	_top_layout += '{s<}<div class="mpc-countdown__section"><h4>{sl}</h4><span class="mpc-main"><div><div>{sn}</div></div></span></div>{s>}';

	function init_shortcode( $countdown ) {
		var $this   = $countdown.find( '.mpc-countdown__content' ),
		    _until  = $this.attr( 'data-until' ),
		    _format = $this.attr( 'data-format' ),
		    _layout = $this.attr( 'data-layout' ),
		    _labels = $this.attr( 'data-labels' ).split( '/' ),
		    _label_typography = 'mpc-typography--' + $countdown.attr( 'data-label-typo' ),
		    _item_typography  = 'mpc-typography--' + $countdown.attr( 'data-item-typo' );

		if( _layout == 'top' ) {
			_layout = _top_layout.replace( /<span class="mpc-main">/g, '<span class="mpc-main ' + _item_typography + '">' );
			_layout = _layout.replace( /<h4>/g, '<h4 class="' + _label_typography + '">' );
		} else {
			_layout = _bottom_layout.replace( /<span class="mpc-main">/g, '<span class="mpc-main ' + _item_typography + '">' );
			_layout = _layout.replace( /<h4>/g, '<h4 class="' + _label_typography + '">' );
		}

		delay_init( $this, { '_until': _until, '_format': _format, '_layout': _layout, '_labels': _labels } );

		$this.attr( 'data-columns', _format.length );

		$countdown.trigger( 'mpc.inited' );

		if( $countdown.attr( 'data-square' ) == '1' ) {
			setTimeout( function() {
				init_square_countdown( $countdown );
			}, 10 );
		}
	}

	function init_square_countdown( $countdown ) {
		var _section_size = 0,
		    _section_size_with_margin = 0,
		    $sections = $countdown.find( '.mpc-countdown__section span' );

		$sections.each( function() {
			var $section = $( this );

			if( $section.outerHeight() > $section.outerWidth() && $section.outerHeight() >= _section_size ) {
				_section_size = $section.outerHeight();
				_section_size_with_margin = $section.outerHeight( true );
			} else if( $section.outerWidth() >= _section_size ) {
				_section_size = $section.outerWidth();
				_section_size_with_margin = $section.outerWidth( true );
			}
		});

		var $css = '.mpc-countdown[id="' + $countdown.attr( 'id' ) + '"] .mpc-main { height: ' +  _section_size + 'px; width: ' + _section_size +'px; }';
		$css += '.mpc-countdown[id="' + $countdown.attr( 'id' ) + '"] .mpc-countdown__section { min-width: ' + _section_size_with_margin +'px; }';

		$countdown.after( '<style>' + $css + '</style>' );
	}

	if ( typeof window.InlineShortcodeView != 'undefined' ) {
		window.InlineShortcodeView_mpc_countdown = window.InlineShortcodeView.extend( {
			rendered: function() {
				var $countdown = this.$el.find( '.mpc-countdown' );

				$countdown.addClass( 'mpc-waypoint--init' );

				_mpc_vars.$body.trigger( 'mpc.font-loaded', [ $countdown ] );
				_mpc_vars.$body.trigger( 'mpc.inited', [ $countdown ] );

				init_shortcode( $countdown );

				window.InlineShortcodeView_mpc_countdown.__super__.rendered.call( this );
			},
			beforeUpdate: function () {
				var $countdown = this.$el.find( '.mpc-countdown__content' );

				$countdown.countdown( 'destroy' );
				$countdown.siblings( 'style' ).remove();
			}
		} );
	}

	var $countdowns = $( '.mpc-countdown' );

	$countdowns.each( function() {
		var $countdown = $( this );

		$countdown.one( 'mpc.init', function () {
			init_shortcode( $countdown );
		} );
	} );
} )( jQuery );

/*----------------------------------------------------------------------------*\
	COUNTER SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	// var MPC_Counters = function() {
	// 	this.$el = $( '.mpc-counter' );
	//
	// 	this.init = function() {
	// 		var _self = this;
	//
	// 		if ( typeof CountUp !== 'undefined' ) {
	// 			_self.$el.each( function() {
	// 				var $this    = $( this ),
	// 					$counter = $this.find( '.mpc-counter--target' ),
	// 					_options = $counter.data( 'options' ),
	// 					_counter = new CountUp( $counter[ 0 ], _options.initial, _options.value, _options.decimals, _options.duration, _options );
	//
	// 				$this.on( 'mpc.waypoint', function() { _self.inview_init( $counter, _counter ); } );
	// 			} );
	//
	// 			mpc_init_class( _self.$el );
	// 			// this.$el.trigger( 'mpc.inited' );
	// 		} else {
	// 			setTimeout( function() {
	// 				_self.init();
	// 			}, 250 );
	// 		}
	// 	};
	//
	// 	this.inview_init = function( $target, _counter ) {
	// 		_counter.start();
	// 	};
	// };
	//
	// _mpc_vars.$document.ready( function() {
	// 	var _mpc_counters = new MPC_Counters();
	// 	_mpc_counters.init();
	// });

	function fast_init( $this ) {
		$this.text( $this.attr( 'data-to' ) );
	}

	function delay_init( $this ) {
		if ( typeof CountUp !== 'undefined' ) {
			var _options = $this.data( 'options' ),
				_counter = new CountUp( $this[0], parseFloat( _options.initial ), parseFloat( _options.value ),
										parseInt( _options.decimals ), parseFloat( _options.duration ), _options );

			if( parseInt( _options.delay ) > 0 ) {
				setTimeout( function() {
					_counter.start();
				}, parseInt( _options.delay ) );
			} else {
				_counter.start();
			}
		} else {
			setTimeout( function() {
				delay_init( $this );
			}, 50 );
		}
	}

	function init_shortcode( $counter ) {
		$counter.trigger( 'mpc.inited' );
	}

	var $counters = $( '.mpc-counter' );

	$counters.each( function() {
		var $counter = $( this ),
		    $parent = $counter.parents( '.mpc-container' );

		if( $parent.length ) {
			$parent.one( 'mpc.parent-init', function() {
				delay_init( $counter.find( '.mpc-counter--target' ) );
			} );
		} else if ( $counter.is( '.mpc-waypoint--init' ) ) {
			delay_init( $counter.find( '.mpc-counter--target' ) );
		} else {
			$counter.one( 'mpc.waypoint', function() {
				if( !$counter.is( '.mpc-init--fast' ) ) {
					delay_init( $counter.find( '.mpc-counter--target' ) );
				}
			});
		}

		$counter.one( 'mpc.init', function () {
			if( $counter.is( '.mpc-init--fast' ) ) {
				fast_init( $counter.find( '.mpc-counter--target' ) );
			}

			init_shortcode( $counter );
		} );

		$counter.one( 'mpc.init-fast', function() {
			fast_init( $counter.find( '.mpc-counter--target' ) );
		} );
	} );

	/* FrontEnd Init */
	if ( typeof window.InlineShortcodeView != 'undefined' ) {
		window.InlineShortcodeView_mpc_counter = window.InlineShortcodeView.extend( {
			rendered: function() {
				var $counter = this.$el.find( '.mpc-counter' );

				$counter.addClass( 'mpc-waypoint--init' );

				_mpc_vars.$body.trigger( 'mpc.icon-loaded', [ $counter ] );
				_mpc_vars.$body.trigger( 'mpc.font-loaded', [ $counter ] );
				_mpc_vars.$body.trigger( 'mpc.inited', [ $counter ] );

				delay_init( $counter.find( '.mpc-counter--target' ) );

				window.InlineShortcodeView_mpc_counter.__super__.rendered.call( this );
			}
		} );
	}
} )( jQuery );

/*----------------------------------------------------------------------------*\
	DIVIDER SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function init_shortcode( $divider ) {
		$divider.trigger( 'mpc.inited' );
	}

	if ( typeof window.InlineShortcodeView != 'undefined' ) {
		window.InlineShortcodeView_mpc_divider = window.InlineShortcodeView.extend( {
			rendered: function() {
				var $divider = this.$el.find( '.mpc-divider' );

				$divider.addClass( 'mpc-waypoint--init' );

				_mpc_vars.$body.trigger( 'mpc.icon-loaded', [ $divider ] );
				_mpc_vars.$body.trigger( 'mpc.font-loaded', [ $divider ] );
				_mpc_vars.$body.trigger( 'mpc.inited', [ $divider ] );

				init_shortcode( $divider );

				window.InlineShortcodeView_mpc_divider.__super__.rendered.call( this );
			}
		} );
	}

	var $dividers = $( '.mpc-divider' );

	$dividers.each( function() {
		var $divider = $( this );

		$divider.one( 'mpc.init', function () {
			init_shortcode( $divider );
		} );
	} );
} )( jQuery );

/*----------------------------------------------------------------------------*\
	DROPCAP SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function init_shortcode( $dropcap ) {
		$dropcap.trigger( 'mpc.inited' );
	}

	if ( typeof window.InlineShortcodeView != 'undefined' ) {
		var $body = $( 'body' );

		window.InlineShortcodeView_mpc_dropcap = window.InlineShortcodeView.extend( {
			rendered: function() {
				var $dropcap = this.$el.find( '.mpc-dropcap' );

				$dropcap.addClass( 'mpc-waypoint--init' );

				$body.trigger( 'mpc.icon-loaded', [ $dropcap ] );
				$body.trigger( 'mpc.font-loaded', [ $dropcap ] );
				$body.trigger( 'mpc.inited', [ $dropcap ] );

				init_shortcode( $dropcap );

				window.InlineShortcodeView_mpc_dropcap.__super__.rendered.call( this );
			}
		} );
	}

	var $dropcaps = $( '.mpc-dropcap' );

	$dropcaps.each( function() {
		var $dropcap = $( this );

		$dropcap.one( 'mpc.init', function () {
			init_shortcode( $dropcap );
		} );
	} );
} )( jQuery );

/*----------------------------------------------------------------------------*\
	GRID ANYTHING SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function wrap_shortcode( $grid ) {
		$grid.children().each( function() {
			$( this )
                .addClass( 'mpc-init--fast' )
				.wrap( '<div class="mpc-grid__item"><div class="mpc-grid__item-wrapper" /></div>' );
		});
	}

	function unwrap_shortcode( $grid ) {
		$grid.find( '.vc_element' ).each( function() {
			$( this ).unwrap().unwrap();
		});
	}

	function delay_init( $grid ) {
		if ( $.fn.isotope && $.fn.imagesLoaded ) {
			init_shortcode( $grid );
		} else {
			setTimeout( function() {
				delay_init( $grid );
			}, 50 );
		}
	}

	function init_shortcode( $grid ) {
		var $row = $grid.parents( '.mpc-row' );

		$grid.imagesLoaded().done( function() {
			$grid.on( 'layoutComplete', function() {
				MPCwaypoint.refreshAll();
			} );

			$grid.trigger( 'mpc.inited' ); // removing float

			$grid.isotope( {
				itemSelector: '.mpc-grid__item',
				layoutMode: 'masonry'
			} );

			_mpc_vars.$document.ready( function() {
				setTimeout( function() {
					if( $grid.data( 'isotope' ) ) {
						$grid.isotope( 'layout' );
					}
				}, 250 );
			});

			$row.on( 'mpc.rowResize', function() {
				if( $grid.data( 'isotope' ) ) {
					$grid.isotope( 'layout' );
				}
			} );
		} );
	}

	var $grids_anything = $( '.mpc-grid-anything' );

	$grids_anything.each( function() {
		var $grid_anything = $( this );

		wrap_shortcode( $grid_anything );

		$grid_anything.one( 'mpc.init', function() {
			delay_init( $grid_anything );
		} );
	});

} )( jQuery );

/*----------------------------------------------------------------------------*\
	GRID IMAGES SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function delay_init( $grid ) {
		if ( $.fn.isotope && $.fn.imagesLoaded ) {
			init_shortcode( $grid );
		} else {
			setTimeout( function() {
				delay_init( $grid );
			}, 50 );
		}
	}

	function init_shortcode( $grid_images ) {
		var $row = $grid_images.parents( '.mpc-row' );

		$grid_images.imagesLoaded().done( function() {
			$grid_images.on( 'layoutComplete', function() {
				MPCwaypoint.refreshAll();
				mpc_init_lightbox( $grid_images, true );
			} );

			$grid_images.isotope( {
				itemSelector: '.mpc-item',
				layoutMode: 'masonry'
			} );

			$row.on( 'mpc.rowResize', function() {
				if( $grid_images.data( 'isotope' ) ) {
					$grid_images.isotope( 'layout' );
				}
			} );
		} );

		$grid_images.trigger( 'mpc.inited' );
	}

	if ( typeof window.InlineShortcodeView != 'undefined' ) {
		window.InlineShortcodeView_mpc_grid_images = window.InlineShortcodeView.extend( {
			rendered: function() {
				var $grid_images = this.$el.find( '.mpc-grid-images' ),
					$pagination = $grid_images.siblings( '.mpc-pagination' );

				$grid_images.addClass( 'mpc-waypoint--init' );

				_mpc_vars.$body.trigger( 'mpc.icon-loaded', [ $grid_images, $pagination ] );
				_mpc_vars.$body.trigger( 'mpc.font-loaded', [ $grid_images, $pagination ] );
				_mpc_vars.$body.trigger( 'mpc.pagination-loaded', [ $pagination ] );
				_mpc_vars.$body.trigger( 'mpc.inited', [ $grid_images, $pagination ] );

				setTimeout( function() {
					delay_init( $grid_images );
				}, 250 );

				window.InlineShortcodeView_mpc_grid_images.__super__.rendered.call( this );
			},
			beforeUpdate: function() {
				this.$el.find( '.mpc-grid-images' ).isotope( 'destroy' );

				window.InlineShortcodeView_mpc_grid_images.__super__.beforeUpdate.call( this );
			}
		} );
	}

	var $grids_images = $( '.mpc-grid-images' );

	$grids_images.each( function() {
		var $grid_images = $( this );

		$grid_images.one( 'mpc.init', function () {
			delay_init( $grid_images );
		} );
	});
} )( jQuery );

/*----------------------------------------------------------------------------*\
	GRID POSTS SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function resize_single_posts( $grid ) {
		if( $grid.is( '.mpc-layout--style_4' ) ) {
			var $posts_content = $grid.find( '.mpc-post__wrapper > .mpc-post__content' ),
				$first  = $posts_content.eq( 1 ),
				_margin = parseInt( $first.outerHeight() * -0.5 ) ;

			$posts_content.parents( '.mpc-post' ).css( 'margin-bottom', _margin );
		}
	}

	function delay_init( $grid ) {
		if ( $.fn.isotope && $.fn.imagesLoaded ) {
			init_shortcode( $grid );
		} else {
			setTimeout( function() {
				delay_init( $grid );
			}, 50 );
		}
	}

	function init_shortcode( $grid_posts ) {
		var $row = $grid_posts.parents( '.mpc-row' );

		resize_single_posts( $grid_posts );

		$grid_posts.imagesLoaded().done( function() {
			$grid_posts.on( 'layoutComplete', function() {
				mpc_init_lightbox( $grid_posts, true );
				MPCwaypoint.refreshAll();
			} );

			$grid_posts.isotope( {
				itemSelector: '.mpc-post',
				layoutMode: 'masonry',
				masonry: {
					columnWidth: '.mpc-grid-sizer'
				}
			} );

			$row.on( 'mpc.rowResize', function() {
				if( $grid_posts.data( 'isotope' ) ) {
					$grid_posts.isotope( 'layout' );
				}
			} );
		} );

		$grid_posts.trigger( 'mpc.inited' );
	}

	if ( typeof window.InlineShortcodeView != 'undefined' ) {
		window.InlineShortcodeView_mpc_grid_posts = window.InlineShortcodeView.extend( {
			rendered: function() {
				var $grid_posts = this.$el.find( '.mpc-grid-posts' ),
					$pagination = $grid_posts.siblings( '.mpc-pagination' );

				$grid_posts.addClass( 'mpc-waypoint--init' );

				_mpc_vars.$body.trigger( 'mpc.icon-loaded', [ $grid_posts, $pagination ] );
				_mpc_vars.$body.trigger( 'mpc.font-loaded', [ $grid_posts, $pagination ] );
				_mpc_vars.$body.trigger( 'mpc.pagination-loaded', [ $pagination ] );
				_mpc_vars.$body.trigger( 'mpc.inited', [ $grid_posts, $pagination ] );

				setTimeout( function() {
					delay_init( $grid_posts );
				}, 500 );

				window.InlineShortcodeView_mpc_grid_posts.__super__.rendered.call( this );
			},
			beforeUpdate: function() {
				this.$el.find( '.mpc-grid-posts' ).isotope( 'destroy' );

				window.InlineShortcodeView_mpc_grid_posts.__super__.beforeUpdate.call( this );
			}
		} );
	}

	var $grids_posts = $( '.mpc-grid-posts' );

	$grids_posts.each( function() {
		var $grid_posts = $( this );

		$grid_posts.one( 'mpc.init', function () {
			delay_init( $grid_posts );
		} );
	});

	/* Fix Google Fonts resize */
	_mpc_vars.$window.load( function() {
		$grids_posts.each( function() {
			var $grid_posts = $( this );

			if ( $grid_posts.data( 'isotope' ) ) {
				setTimeout( function() {
					$grid_posts.isotope( 'layout' );
				}, 250 );
			}
		});
	});
} )( jQuery );

/*----------------------------------------------------------------------------*\
	HOTSPOT SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function init_shortcode( $hotspot ) {
		var $siblings = $hotspot.siblings( '.mpc-hotspot' );

		$hotspot.on( 'mouseenter mouseover', function() {
			$siblings.removeClass( 'mpc-active' );
			$hotspot.addClass( 'mpc-active' );
		} );

		$hotspot.trigger( 'mpc.inited' );
		$hotspot.find( '.mpc-hotspot__icon' ).trigger( 'mpc.inited' );
	}

	function init_frontend( $hotspot ) {
		var $vc_handler = $hotspot.parents( '.vc_mpc_hotspot' ),
			_position = $hotspot.data( 'position' );

		$vc_handler.css( {
			'top': _position[ 1 ] + '%',
			'left': _position[ 0 ] + '%'
		} );
	}

	if ( typeof window.InlineShortcodeView != 'undefined' ) {
		var $body = $( 'body' );

		window.InlineShortcodeView_mpc_hotspot = window.InlineShortcodeView.extend( {
			rendered: function() {
				var $hotspot = this.$el.find( '.mpc-hotspot' ),
				    $tooltip = $hotspot.find( '.mpc-tooltip' );

				$hotspot.addClass( 'mpc-waypoint--init' );

				$body.trigger( 'mpc.icon-loaded', [ $hotspot ] );
				$body.trigger( 'mpc.font-loaded', [ $hotspot ] );
				$body.trigger( 'mpc.inited', [ $hotspot, $tooltip ] );

				init_shortcode( $hotspot );
				init_frontend( $hotspot  );

				window.InlineShortcodeView_mpc_hotspot.__super__.rendered.call( this );
			}
		} );
	}

	var $hotspots = $( '.mpc-hotspot' );

	$hotspots.each( function() {
		var $hotspot = $( this );

		$hotspot.one( 'mpc.init', function () {
			init_shortcode( $hotspot );
		} );
	} );
} )( jQuery );

/*----------------------------------------------------------------------------*\
	ICON SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function init_shortcode( $icon ) {
		$icon.trigger( 'mpc.inited' );
	}

	if ( typeof window.InlineShortcodeView != 'undefined' ) {
		window.InlineShortcodeView_mpc_icon = window.InlineShortcodeView.extend( {
			rendered: function() {
				var $icon = this.$el.find( '.mpc-icon' );

				$icon.addClass( 'mpc-waypoint--init' );

				_mpc_vars.$body.trigger( 'mpc.icon-loaded', [ $icon ] );
				_mpc_vars.$body.trigger( 'mpc.font-loaded', [ $icon ] );
				_mpc_vars.$body.trigger( 'mpc.inited', [ $icon ] );

				init_shortcode( $icon );

				window.InlineShortcodeView_mpc_icon.__super__.rendered.call( this );
			}
		} );
	}

	var $icons = $( '.mpc-icon' );

	$icons.each( function() {
		var $icon = $( this );

		$icon.one( 'mpc.init', function () {
			init_shortcode( $icon );
		} );
	} );
} )( jQuery );

/*----------------------------------------------------------------------------*\
	ICON LIST SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function init_shortcode( $icon_list ) {
		$icon_list.trigger( 'mpc.inited' );
	}

	if ( typeof window.InlineShortcodeView != 'undefined' ) {
		window.InlineShortcodeView_mpc_icon_list = window.InlineShortcodeView.extend( {
			rendered: function() {
				var $icon_list = this.$el.find( '.mpc-icon-list' );

				$icon_list.addClass( 'mpc-waypoint--init' );

				_mpc_vars.$body.trigger( 'mpc.icon-loaded', [ $icon_list ] );
				_mpc_vars.$body.trigger( 'mpc.font-loaded', [ $icon_list ] );
				_mpc_vars.$body.trigger( 'mpc.inited', [ $icon_list ] );

				init_shortcode( $icon_list );

				window.InlineShortcodeView_mpc_icon_list.__super__.rendered.call( this );
			}
		} );
	}

	var $icon_lists = $( '.mpc-icon-list' );

	$icon_lists.each( function() {
		var $icon_list = $( this );

		$icon_list.one( 'mpc.init', function () {
			init_shortcode( $icon_list );
		} );
	} );
} )( jQuery );

/*----------------------------------------------------------------------------*\
	ICON COLUMN SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function init_shortcode( $icon_column ) {
		var $icon = $icon_column.find( '.mpc-icon' ),
			_icon_size;

		$icon_column.imagesLoaded().always( function() {
			if ( $icon_column.is( '.mpc-icon-column--style_2' ) ) {
				_icon_size = parseInt( $icon.outerHeight() * .5 );
				$icon.css( 'top', '-' + _icon_size + 'px' );
				$icon_column.find( '.mpc-icon-column__content-wrap' ).css( 'margin-top', '-' + _icon_size + 'px' );
			}

			if ( $icon_column.is( '.mpc-icon-column--style_4' ) ) {
				_icon_size = parseInt( $icon.outerHeight() * .5 );
				$icon.css( 'left', '-' + _icon_size + 'px' );
			}

			if ( $icon_column.is( '.mpc-icon-column--style_6' ) ) {
				_icon_size = parseInt( $icon.outerHeight() * .5 );
				$icon.css( 'right', '-' + _icon_size + 'px' );
			}
		} );

		$icon_column.trigger( 'mpc.inited' );
	}

	if ( typeof window.InlineShortcodeView != 'undefined' ) {
		window.InlineShortcodeView_mpc_icon_column = window.InlineShortcodeView.extend( {
			rendered: function() {
				var $icon_column = this.$el.find( '.mpc-icon-column' );

				$icon_column.addClass( 'mpc-waypoint--init' );

				_mpc_vars.$body.trigger( 'mpc.icon-loaded', [ $icon_column ] );
				_mpc_vars.$body.trigger( 'mpc.font-loaded', [ $icon_column ] );
				_mpc_vars.$body.trigger( 'mpc.inited', [ $icon_column ] );

				init_shortcode( $icon_column );

				window.InlineShortcodeView_mpc_icon_column.__super__.rendered.call( this );
			    this.afterRender();
			},
			afterRender: function() {
				var _parent = vc.shortcodes.findWhere( { id: this.model.get( 'parent_id' ) } );

				setTimeout( function() {
					_parent.trigger( 'mpc:forceRender' );
				}, 250 );
			}
		} );
	}

	var $icon_columns = $( '.mpc-icon-column' );

	$icon_columns.each( function() {
		var $icon_column = $( this );

		$icon_column.one( 'mpc.init', function () {
			init_shortcode( $icon_column );
		} );
	} );
} )( jQuery );

/*----------------------------------------------------------------------------*\
	IHOVER SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function resize_shortcode( $ihover ) {
		var _size = '';

		if ( _mpc_vars.breakpoints.custom( '(max-width: 768px)' ) ) {
			_size = $ihover.width();
		}

		$ihover.find( '.mpc-ihover-item' ).css( {
			width: _size,
			height: _size
		} );
	}

	function init_shortcode( $ihover ) {
		$ihover.trigger( 'mpc.inited' );

		$ihover.on( 'click', '.mpc-ihover-item > a', function( event ) {
			var $ihover_item = $( this );

			if ( $ihover_item.is( '[href="#"]' ) ) {
				event.preventDefault();
			}
		} );
	}

	if ( typeof window.InlineShortcodeViewContainer != 'undefined' ) {
		window.InlineShortcodeView_mpc_ihover = window.InlineShortcodeViewContainer.extend( {
			rendered: function ( params ) {
				var $ihovers = this.$el.find( '.mpc-ihover-wrapper' );

				$ihovers.addClass( 'mpc-waypoint--init' );

				_mpc_vars.$body.trigger( 'mpc.icon-loaded', [ $ihovers ] );
				_mpc_vars.$body.trigger( 'mpc.font-loaded', [ $ihovers ] );
				_mpc_vars.$body.trigger( 'mpc.inited', [ $ihovers ] );

				init_shortcode( $ihovers );

				window.InlineShortcodeView_mpc_ihover.__super__.rendered.call( this, params );
			}
		} );
	}

	var $ihovers = $( '.mpc-ihover-wrapper' );

	$ihovers.each( function() {
		var $ihover = $( this );

		$ihover.one( 'mpc.init', function () {
			init_shortcode( $ihover );
			resize_shortcode( $ihover );
		} );
	} );

	_mpc_vars.$window.on( 'mpc.resize', function() {
		$.each( $ihovers, function() {
			resize_shortcode( $( this ) );
		} );
	} );
} )( jQuery );

/*----------------------------------------------------------------------------*\
	IHOVER ITEM SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	if ( typeof window.InlineShortcodeView != 'undefined' ) {
		window.InlineShortcodeView_mpc_ihover_item = window.InlineShortcodeView.extend( {
			initialize: function () {
				var _parent = vc.shortcodes.findWhere( { id: this.model.get( 'parent_id' ) } );

				this.listenTo( this.model, 'destroy', this.removeView );
				this.listenTo( this.model, 'change:params', this.update );
				this.listenTo( this.model, 'change:parent_id', this.changeParentId );

				this.listenTo( _parent, 'change:params', this.forceUpdate );

				this.listenTo( this.model, 'change:parent_id', this.update );

				window.InlineShortcodeView_mpc_ihover_item.__super__.initialize.call( this );
			},
			clone: function( e ) {
				_.isObject( e ) && e.preventDefault() && e.stopPropagation();

				this.forceUpdate();

				window.InlineShortcodeView_mpc_ihover_item.__super__.clone.call( this );
			},
			rendered: function() {
				var _params = this.model.get( 'params' );

				delete _params.globals;

				this.model.set( 'params', _params );

				window.InlineShortcodeView_mpc_ihover_item.__super__.rendered.call( this );
			},
			beforeUpdate: function () {
				var _params = this.model.get( 'params' ),
					_parent = vc.shortcodes.findWhere( { id: this.model.get( 'parent_id' ) } ),
					_parent_attr;

				_parent_attr = {
					"title_font_preset": _parent.attributes.params.title_font_preset,
					"content_font_preset": _parent.attributes.params.content_font_preset,
					"shape": _parent.attributes.params.shape,
					"effect": _parent.attributes.params.effect,
					"style": _parent.attributes.params.style
				};
				_params.globals = encodeURI( JSON.stringify( _parent_attr ) );

				this.model.set( 'params', _params );
			},
			forceUpdate: function() {
				this.update( this.model );
			}
		} );
	}
} )( jQuery );

/*----------------------------------------------------------------------------*\
	IMAGE SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function init_shortcode( $image ) {
		mpc_init_lightbox( $image, false );

		$image.trigger( 'mpc.inited' );
	}

	if ( typeof window.InlineShortcodeView != 'undefined' ) {
		window.InlineShortcodeView_mpc_image = window.InlineShortcodeView.extend( {
			rendered: function() {
				var $image  = this.$el.find( '.mpc-image' ),
					$set    = $image.closest( '.vc_element' );

				$image.addClass( 'mpc-waypoint--init' );

				_mpc_vars.$body.trigger( 'mpc.icon-loaded', [ $set ] );
				_mpc_vars.$body.trigger( 'mpc.font-loaded', [ $set ] );
				_mpc_vars.$body.trigger( 'mpc.inited', [ $set ] );

				setTimeout( function() {
					init_shortcode( $image );
				}, 250 );

				window.InlineShortcodeView_mpc_image.__super__.rendered.call( this );
			}
		} );
	}

	var $images = $( '.mpc-image' );

	$images.each( function() {
		var $image = $( this );

		$image.one( 'mpc.init', function() {
			init_shortcode( $image );
		} );
	});
} )( jQuery );

/*----------------------------------------------------------------------------*\
	INTERACTIVE IMAGE SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function init_shortcode( $interactive_image ) {
		$interactive_image.trigger( 'mpc.inited' );
	}

	if ( typeof window.InlineShortcodeViewContainer != 'undefined' ) {
		window.InlineShortcodeView_mpc_interactive_image = window.InlineShortcodeViewContainer.extend( {
			rendered: function() {
				var $interactive_image = this.$el.find( '.mpc-interactive_image' );

				$interactive_image.addClass( 'mpc-waypoint--init' );

				_mpc_vars.$body.trigger( 'mpc.icon-loaded', [ $interactive_image ] );
				_mpc_vars.$body.trigger( 'mpc.font-loaded', [ $interactive_image ] );
				_mpc_vars.$body.trigger( 'mpc.inited', [ $interactive_image ] );

				init_shortcode( $interactive_image );

				window.InlineShortcodeView_mpc_interactive_image.__super__.rendered.call( this );
			}
		} );
	}

	var $interactive_images = $( '.mpc-interactive_image' );

	$interactive_images.each( function() {
		var $interactive_image = $( this );

		$interactive_image.one( 'mpc.init', function () {
			init_shortcode( $interactive_image );
		} );
	} );
} )( jQuery );

/*----------------------------------------------------------------------------*\
	LIGHTBOX SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function init_shortcode( $lightbox ) {
		mpc_init_lightbox( $lightbox, false );

		$lightbox.trigger( 'mpc.inited' );
	}

	if ( typeof window.InlineShortcodeView != 'undefined' ) {
		var $body = $( 'body' );

		window.InlineShortcodeView_mpc_lightbox = window.InlineShortcodeView.extend( {
			rendered: function() {
				var $lightbox = this.$el.find( '.mpc-lightbox' );

				$lightbox.addClass( 'mpc-waypoint--init' );

				$body.trigger( 'mpc.font-loaded', [ $lightbox ] );
				$body.trigger( 'mpc.inited', [ $lightbox ] );

				init_shortcode( $lightbox );

				window.InlineShortcodeView_mpc_lightbox.__super__.rendered.call( this );
			}
		} );
	}

	var $lightboxs = $( '.mpc-lightbox' );

	$lightboxs.each( function() {
		var $lightbox = $( this );

		$lightbox.one( 'mpc.init', function () {
			init_shortcode( $lightbox );
		} );
	} );
} )( jQuery );

/*----------------------------------------------------------------------------*\
	MAILCHIMP SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function init_shortcode( $mailchimp ) {
		$mailchimp.trigger( 'mpc.inited' );

		var $selects    = $mailchimp.find( 'select' ),
			$inputs     = $mailchimp.find( 'input:not([type="submit"], [type="checkbox"], [type="radio"])' ),
			$radios     = $mailchimp.find( 'input[type="checkbox"], input[type="radio"]' ),
			$submit     = $mailchimp.find( 'input[type="submit"]' ),
			_align      = $mailchimp.attr( 'data-align' ),
			_typography = {
				label: $mailchimp.attr( 'data-typo-label' ),
				input: $mailchimp.attr( 'data-typo-input' ),
				radio: $mailchimp.attr( 'data-typo-radio' ),
				submit: $mailchimp.attr( 'data-typo-submit' )
			};

		_align = _align == undefined ? 'left' : _align;

		$submit.parent().css( 'text-align', _align );

		$radios.closest( 'label' ).addClass( 'mpc-input-wrap' );

		if ( $inputs.length ) {
			$selects.css( 'height', $inputs.outerHeight() );
		}

		if ( _typography.label != undefined ) {
			$mailchimp.find( 'label:not(.mpc-input-wrap)' ).addClass( _typography.label );
		}
		if ( _typography.input != undefined ) {
			$selects.addClass( _typography.input );
			$inputs.addClass( _typography.input );
		}
		if ( _typography.radio != undefined ) {
			$mailchimp.find( 'label.mpc-input-wrap' ).addClass( _typography.radio );
		}
		if ( _typography.submit != undefined ) {
			$submit.addClass( _typography.submit );
		}
	}

	if ( typeof window.InlineShortcodeView != 'undefined' ) {
		window.InlineShortcodeView_mpc_mailchimp = window.InlineShortcodeView.extend( {
			rendered: function() {
				var $icon = this.$el.find( '.mpc-mailchimp' );

				$icon.addClass( 'mpc-waypoint--init' );

				_mpc_vars.$body.trigger( 'mpc.icon-loaded', [ $icon ] );
				_mpc_vars.$body.trigger( 'mpc.font-loaded', [ $icon ] );
				_mpc_vars.$body.trigger( 'mpc.inited', [ $icon ] );

				init_shortcode( $icon );

				window.InlineShortcodeView_mpc_mailchimp.__super__.rendered.call( this );
			}
		} );
	}

	var $mailchimps = $( '.mpc-mailchimp' );

	$mailchimps.each( function() {
		var $mailchimp = $( this );

		$mailchimp.one( 'mpc.init', function () {
			init_shortcode( $mailchimp );
		} );
	} );
} )( jQuery );

/*----------------------------------------------------------------------------*\
	MAP SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	var $maps = $( '.mpc-map' ),
		_styles = {
		    blue_water: [{"featureType":"administrative","elementType":"labels.text.fill","stylers":[{"color":"#444444"}]},{"featureType":"landscape","elementType":"all","stylers":[{"color":"#f2f2f2"}]},{"featureType":"poi","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"all","stylers":[{"saturation":-100},{"lightness":45}]},{"featureType":"road.highway","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"road.arterial","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"water","elementType":"all","stylers":[{"color":"#46bcec"},{"visibility":"on"}]}],

			apple_maps: [{"featureType":"landscape.man_made","elementType":"geometry","stylers":[{"color":"#f7f1df"}]},{"featureType":"landscape.natural","elementType":"geometry","stylers":[{"color":"#d0e3b4"}]},{"featureType":"landscape.natural.terrain","elementType":"geometry","stylers":[{"visibility":"off"}]},{"featureType":"poi","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"poi.business","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"poi.medical","elementType":"geometry","stylers":[{"color":"#fbd3da"}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#bde6ab"}]},{"featureType":"road","elementType":"geometry.stroke","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#ffe15f"}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#efd151"}]},{"featureType":"road.arterial","elementType":"geometry.fill","stylers":[{"color":"#ffffff"}]},{"featureType":"road.local","elementType":"geometry.fill","stylers":[{"color":"black"}]},{"featureType":"transit.station.airport","elementType":"geometry.fill","stylers":[{"color":"#cfb2db"}]},{"featureType":"water","elementType":"geometry","stylers":[{"color":"#a2daf2"}]}],

			blue_essence: [{"featureType":"landscape.natural","elementType":"geometry.fill","stylers":[{"visibility":"on"},{"color":"#e0efef"}]},{"featureType":"poi","elementType":"geometry.fill","stylers":[{"visibility":"on"},{"hue":"#1900ff"},{"color":"#c0e8e8"}]},{"featureType":"road","elementType":"geometry","stylers":[{"lightness":100},{"visibility":"simplified"}]},{"featureType":"road","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"transit.line","elementType":"geometry","stylers":[{"visibility":"on"},{"lightness":700}]},{"featureType":"water","elementType":"all","stylers":[{"color":"#7dcdcd"}]}],

			cool_grey: [{"featureType":"landscape","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"poi","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"water","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"stylers":[{"hue":"#00aaff"},{"saturation":-100},{"gamma":2.15},{"lightness":12}]},{"featureType":"road","elementType":"labels.text.fill","stylers":[{"visibility":"on"},{"lightness":24}]},{"featureType":"road","elementType":"geometry","stylers":[{"lightness":57}]}],

			shades_of_grey: [{featureType:"all",elementType:"labels.text.fill",stylers:[{saturation:36},{color:"#000000"},{lightness:"56"}]},{featureType:"all",elementType:"labels.text.stroke",stylers:[{visibility:"on"},{color:"#000000"},{lightness:16}]},{featureType:"all",elementType:"labels.icon",stylers:[{visibility:"off"}]},{featureType:"administrative",elementType:"geometry.fill",stylers:[{color:"#000000"},{lightness:"30"}]},{featureType:"administrative",elementType:"geometry.stroke",stylers:[{color:"#000000"},{lightness:"17"},{weight:1.2}]},{featureType:"landscape",elementType:"geometry",stylers:[{color:"#000000"},{lightness:"26"}]},{featureType:"poi",elementType:"geometry",stylers:[{color:"#000000"},{lightness:21}]},{featureType:"road.highway",elementType:"geometry.fill",stylers:[{color:"#000000"},{lightness:17}]},{featureType:"road.highway",elementType:"geometry.stroke",stylers:[{color:"#000000"},{lightness:29},{weight:.2}]},{featureType:"road.arterial",elementType:"geometry",stylers:[{color:"#000000"},{lightness:18}]},{featureType:"road.local",elementType:"geometry",stylers:[{color:"#000000"},{lightness:16}]},{featureType:"transit",elementType:"geometry",stylers:[{color:"#000000"},{lightness:19}]},{featureType:"water",elementType:"geometry",stylers:[{color:"#000000"},{lightness:17}]}]
	    };

	function add_map_marker( _marker_options, _map ) {
		var _marker = new google.maps.Marker( {
			position: _marker_options.location,
			map:      _map,
			icon:     _marker_options.icon_url
		} );
	}

	function init_shortcode( $maps ) {
		if ( typeof google == 'undefined' ) {
			$maps
				.addClass( 'mpc-empty' )
				.find( '.mpc-error' )
				.show();

			return;
		}

		$maps.each( function() {
			var $map         = $( this ),
				_map_options = $map.data( 'map-options' ),
				_defaults    = {
					'disable_auto_zoom':     false,
					'zoom':                  '',
					'disable_auto_location': false,
					'location':              '',
					'disable_ui':            false,
					'disable_scroll_wheel':  false,
					'style':                 'default',
					'markers':               []
				};

			if ( typeof _map_options == 'undefined' || typeof _map_options.markers == 'undefined' ) {
				return;
			}

			$.extend( _defaults, _map_options );

			var _map_config = {
					disableDefaultUI: _map_options.disable_ui,
					scrollwheel:      !_map_options.disable_scroll_wheel
				},
				_index,
				_map,
				_bounds,
				_loaded;

			if ( _map_options.style == 'custom' && typeof _map_options.custom_style != 'undefined' ) {
				try {
					_map_options.custom_style = JSON.parse( _map_options.custom_style );

					_map_config.styles = _map_options.custom_style;
				} catch (e) {
					// Parsing failed
				}
			} else if ( _map_options.style != 'default' && typeof _styles[ _map_options.style ] != 'undefined' ) {
				_map_config.styles = _styles[ _map_options.style ];
			}

			if ( _map_options.disable_auto_location && _map_options.location != '' ) {
				_map_config.center = new google.maps.LatLng( _map_options.location.latitude, _map_options.location.longitude );
			}

			if ( _map_options.disable_auto_zoom && _map_options.zoom != '' ) {
				_map_config.zoom = parseInt( _map_options.zoom );
			}

			_map = new google.maps.Map( $map[ 0 ], _map_config );

			_bounds = new google.maps.LatLngBounds();

			for( _index in _map_options.markers ) {
				if ( _map_options.markers[ _index ].location != '' ) {
					_map_options.markers[ _index ].location = new google.maps.LatLng( _map_options.markers[ _index ].location.latitude, _map_options.markers[ _index ].location.longitude );

					_bounds.extend( _map_options.markers[ _index ].location );

					add_map_marker( _map_options.markers[ _index ], _map );
				}
			}

			if ( ! _map_options.disable_auto_location ) {
				_loaded = google.maps.event.addListener( _map, 'idle', function() {
					_map.setCenter( _bounds.getCenter() );

					google.maps.event.removeListener( _loaded );
				} );
			}

			if ( ! _map_options.disable_auto_zoom ) {
				_map.fitBounds( _bounds );
			}
		} );

		$maps.trigger( 'mpc.inited' );
	}

	if ( typeof window.InlineShortcodeViewContainer != 'undefined' ) {
		window.InlineShortcodeView_mpc_map = window.InlineShortcodeViewContainer.extend( {
			initialize: function( params ) {
				this.listenTo( this.model, 'mpc:forceRender', this.rendered );

				window.InlineShortcodeView_mpc_map.__super__.initialize.call( this, params );
			},
			rendered: function() {
				var _self = this,
					$map = this.$el.find( '.mpc-map' );

				$map.addClass( 'mpc-waypoint--init' );

				_mpc_vars.$body.trigger( 'mpc.inited', [ $map ] );

				var _options = $map.data( 'map-options' ),
					_markers = [];

				setTimeout( function() {
					_self.$el.find( '.mpc-markers .mpc-marker' ).each( function() {
						_markers.push( $( this ).data( 'marker-options' ) );
					} );

					if ( _markers.length ) {
						_options.markers = _markers;
						$map.data( 'map-options', _options )
					}

					init_shortcode( $map );

					$map.closest( '.vc_element' ).find( '.mpc-marker-title' ).first().siblings( '.mpc-marker-title' ).remove();
				}, 250 );

				window.InlineShortcodeView_mpc_map.__super__.rendered.call( this );
			}
		} );
	}

	if ( window.vc_mode != 'admin_frontend_editor' ) {
		_mpc_vars.$window.on( 'load', function () {
			init_shortcode( $maps );
		} );
	}
} )( jQuery );

/*----------------------------------------------------------------------------*\
	MARKER SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	if ( typeof window.InlineShortcodeView != 'undefined' ) {
		window.InlineShortcodeView_mpc_marker = window.InlineShortcodeView.extend( {
			initialize: function () {
				this.listenTo( this.model, 'update change', this.mpcUpdate );

				this.$el.find( '.vc_element-move' ).remove();

				window.InlineShortcodeView_mpc_marker.__super__.initialize.call( this );
			},
			mpcUpdate: function() {
				var _parent = vc.shortcodes.findWhere( { id: this.model.get( 'parent_id' ) } );
				_parent.trigger( 'mpc:forceRender' );
			}
		} );
	}
} )( jQuery );
/*----------------------------------------------------------------------------*\
	MODALBOX SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function show_modal( $modal, $document ) {
		$modal.addClass( 'mpc-visible' );

		if ( _is_bridge_theme ) {
			$document.addClass( 'mpc-block-scroll-bridge' );
		} else {
			$document.addClass( 'mpc-block-scroll' );
		}

		stop_body_scrolling( true );
	}

	function close_position( $modal ) {
		var $close = $modal.find( '.mpc-modal__close' );

		if ( ! $modal.is( '.mpc-close--outside' ) ) {
			return false;
		}

		if ( _mpc_vars.breakpoints.custom( '(max-width: 768px)' ) ) {
			$close.prependTo( $modal.find( '.mpc-modal' ) );
		} else {
			$close.prependTo( $modal );
		}
	}

	function stop_body_scrolling( bool ) {
		if ( bool === true ) {
			$document[0].addEventListener( 'touchmove', freeze, false );
		} else {
			$document[0].removeEventListener( 'touchmove', freeze, false );
		}
	}

	function init_shortcode( $modal ) {
		var $modal_box      = $modal.find( '.mpc-modal' ),
			$modal_row      = $modal.closest( '.mpc-row' ),
			$modal_waypoint = $( '.mpc-modal-waypoint[data-id="' + $modal.attr( 'id' ) + '"]' ),
			_delay          = parseInt( $modal.attr( 'data-delay' ) ),
			_frequency      = $modal.attr( 'data-frequency' );

		$modal_row.addClass( 'mpc-row-modal' );

		_delay = isNaN( _delay ) ? 0 : _delay;

		if ( _frequency != undefined && _frequency != 'onclick' ) {
			$.post( _mpc_vars.ajax_url, {
				action:    'mpc_set_modal_cookie',
				id:        $modal.attr( 'id' ),
				frequency: _frequency
			} );
		}

		if ( _frequency == 'onclick' ) {
			if ( !! $modal.attr( 'data-target-id' ) ) {
				$( 'a[href="#' + $modal.attr( 'data-target-id' ) + '"]' ).on( 'click', function( event) {
					event.preventDefault();

					$modal_box.trigger( 'mpc.animation' );

					show_modal( $modal, $document );
				} );
			}
		} else if ( $modal_waypoint.length ) {
			if ( $modal_waypoint.is( '.mpc-waypoint--init' ) ) {
				$modal_box.trigger( 'mpc.animation' );

				show_modal( $modal, $document );
			} else {
				$modal_waypoint.on( 'mpc.waypoint', function() {
					$modal_box.trigger( 'mpc.animation' );

					show_modal( $modal, $document );
				} );
			}
		} else {
			if ( _delay > 0 ) {
				setTimeout( function() {
					$modal_box.trigger( 'mpc.animation' );

					show_modal( $modal, $document );
				}, _delay * 1000 );
			} else {
				$modal_box.trigger( 'mpc.animation' );
			}
		}

		close_position( $modal );
	}

	var $modals          = $( '.mpc-modal-overlay' ),
		$close_modals    = $( '.mpc-modal__close' ),
		$document        = $( 'html, body' ),
		_is_bridge_theme = $document.hasClass( 'qode-theme-bridge' );

	var freeze = function( event ) {
		event.preventDefault();
	};

	$modals.each( function() {
		var $modal = $( this ),
			$modal_box = $modal.find( '.mpc-modal' );

		$modal_box.one( 'mpc.init', function () {
			init_shortcode( $modal );
		} );
	});

	_mpc_vars.$window.on( 'mpc.resize', function() {
		$.each( $modals, function() {
			close_position( $( this ) );
		});
	} );

	$modals.on( 'click', function( event ) {
		if ( event.target == this ) {
			var $this = $( this );

			if ( $this.is( '.mpc-close-on-click' ) || _mpc_vars.breakpoints.custom( '(max-width: 768px)' ) ) {
				$this.find( '.mpc-modal__close' ).trigger( 'click' );
			}
		}
	} );

	$close_modals.on( 'click', function() {
		var $modal = $( this ).closest( '.mpc-modal-overlay' );

		$modal.removeClass( 'mpc-visible' );

		if ( $modals.filter( '.mpc-visible' ).length == 0 ) {
			if ( _is_bridge_theme ) {
				$document.removeClass( 'mpc-block-scroll-bridge' );
			} else {
				$document.removeClass( 'mpc-block-scroll' );
			}

			stop_body_scrolling( false );
		}
	});
} )( jQuery );

/*----------------------------------------------------------------------------*\
	NAVIGATION SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	var $navigations = $( '.mpc-navigation' );

	function calculate_navigation( $navigation, $carousel ) {
		var $nav_items = $navigation.parents( '.mpc-carousel__wrapper' ).find( '.mpc-navigation' ),
		    _stretched_size = $carousel.width(),
		    _window_size = _mpc_vars.window_width;

		if( $carousel.is( '.mpc-carousel--stretched' ) ) {
			$nav_items.addClass( 'mpc-nav--stretched' );

			if( !$navigation.is( '.mpc-navigation--style_1' ) && !$navigation.is( '.mpc-navigation--style_2' ) ) {
				$nav_items.first().css( 'margin-left', -$carousel.offset().left );
				$nav_items.last().css( 'margin-right', -( _window_size - ( $carousel.offset().left + _stretched_size ) ) );
			}
		} else if( $navigation.is( '.mpc-navigation--style_6' ) ) {
			$nav_items.first().css( 'margin-left', -$carousel.offset().left );
			$nav_items.last().css( 'margin-right', -( _window_size - ( $carousel.offset().left + _stretched_size ) ) );
		} else if( $carousel.parents( '.mpc-row' ).attr( 'data-vc-stretch-content' ) == 'true' ) {
			$nav_items.addClass( 'mpc-nav--stretched' );
		}
	}

	function init_shortcode( $navigation ) {
		if( $navigation.is( '.mpc-inited' ) ) {
			return;
		}

		var $carousel  = $navigation.siblings( '[class^="mpc-carousel-"], .mpc-pricing-box' ),
		    $nav_items = $navigation.parents( '.mpc-carousel__wrapper' ).find( '.mpc-navigation' );

		_mpc_vars.$window.on( 'load', function() {
			calculate_navigation( $navigation, $carousel );
		});

		$nav_items.trigger( 'mpc.inited' );
	}

	$navigations.each( function() {
		var $navigation = $( this );

		$navigation.one( 'mpc.init', function () {
			init_shortcode( $navigation );
		} );
	} );

	_mpc_vars.$window.on( 'mpc.resize', function() {
		$.each( $navigations, function() {
			var $navigation = $( this ),
			    $carousel   = $navigation.siblings( '[class^="mpc-carousel-"], .mpc-pricing-box' );

			calculate_navigation( $navigation, $carousel );
		} );
	} );
} )( jQuery );

/*----------------------------------------------------------------------------*\
	PAGINATION SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function mpc_pagination_classic( $pagination ) {
		if( $pagination.is( '.mpc--square-init' ) ) {
			mpc_pagination_square_button( $pagination );
		}

		$pagination.find( 'li a' ).on( 'click', function( _ev ) {
			var $this      = $( this ),
				$parent    = $this.parents( '.mpc-pagination' ),
				_type      = $parent.attr( 'data-type' ),
				_query	   = window[ '_' + $parent.attr( 'data-grid' ) + '_query' ],
				_current   = parseInt( $parent.attr( 'data-current' ) ),
				_max_pages = parseInt( $parent.attr( 'data-pages' ) ),
				_load_page = $this.parents( 'li' ).attr( 'data-page' );

			if( !$parent.is( '.mpc-pagination--classic' ) || $parent.is( '.mpc-non-ajax' ) ) {
				return true;
			}

			_ev.preventDefault();

			if( _current != _load_page && $this.parent( '.mpc-pagination' ).is( '.mpc-disabled' ) ) return false;

			$parent.addClass( 'mpc-disabled' );

			if( _load_page == 'prev' ) {
				_query.paged = _current > 1 ? _current - 1 : false;
			} else if( _load_page == 'next' ) {
				_query.paged = _current < _max_pages ? _current + 1 : false;
			} else {
				_query.paged = parseInt( _load_page ) != _current ? parseInt( _load_page ) : false;
			}

			if( _query.paged ) {
				window[ '_' + $parent.attr( 'data-grid' ) + '_query' ] = _query;
				mpc_get_paged_content( $parent.attr( 'data-grid' ), _type, $this );
				mpc_refresh_pagination( $parent, $parent.attr( 'data-grid' ) );
			}
		} );
	}

	function mpc_pagination_loadmore( $pagination ) {
		$pagination.find( '.mpc-pagination__link' ).on( 'click', function( _ev ) {
			var $this      = $( this ),
				$parent    = $this.parents( '.mpc-pagination' ),
				_type      = $parent.attr( 'data-type' ),
				_current   = parseInt( $parent.attr( 'data-current' ) ),
				_max_pages = parseInt( $parent.attr( 'data-pages' ) );

			if( !$parent.is( '.mpc-pagination--classic' ) ) {
				_ev.preventDefault();

				if( _current >= _max_pages || $this.is( '.mpc-disabled' ) ) return false;

				$this.addClass( 'mpc-disabled' );

				window[ '_' + $parent.attr( 'data-grid' ) + '_query' ].paged = _current + 1;

				mpc_get_paged_content( $parent.attr( 'data-grid' ), _type, $this );
			}
		} );
	}

	function mpc_pagination_infinity( $pagination ) {
		$pagination.on( 'mpc.infinity', function() {
			var $this = $( this );

			if( $this.is( '.mpc-pagination--infinity' ) ) {
				$this.find( '.mpc-pagination__link' ).trigger( 'click' );
			}
		});
	}

	function mpc_get_paged_content( _id, _type, $this ) {
		var _query = window[ '_' + _id + '_query' ],
			_atts  = window[ '_' + _id + '_atts' ];

		$.post(
			_mpc_vars.ajax_url,
			{
				action:     'mpc_pagination_set',
				type:       _type,
				current:    _query.paged,
				query:      _query,
				atts:       _atts,
				dataType:   'html'
			},
			function( _response ) {
				var $grid       = $( '#' + _id ),
					$pagination = $this.parents( '.mpc-pagination' ),
					$items 		= $( _response );

				if( _type == 'classic' && !$pagination.is( '.mpc-append-ajax' ) ) {
					var $grid_items = $grid.children();
					$grid.isotope( 'remove', $grid_items );
				}

				mpc_init_lightbox( $items, true );
				$grid.append( $items ).isotope( 'insert', $items );
				$grid.imagesLoaded().done( function() {
					$grid.isotope( 'layout' );
				} );

				var _pages = $grid.find( '.mpc-pagination--settings' ).data( 'pages' ),
					_current = $grid.find( '.mpc-pagination--settings' ).data( 'current' );

				$grid.find( '.mpc-pagination--settings' ).remove();
				$grid.trigger( 'mpc.loaded' );

				if( _type == 'infinity' ) {
					$pagination.removeClass( 'mpc-infinity--init' );
				}

				$pagination
					.attr( 'data-current', _current )
					.attr( 'data-pages', _pages );

				if( !$pagination.is( '.mpc-pagination--classic' ) && _pages > _current ) {
					$pagination.removeClass( 'mpc-disabled' );
				} else if( $pagination.is( '.mpc-pagination--classic' ) ) {
					$pagination.removeClass( 'mpc-disabled' );
				}

				$pagination.find( '.mpc-current, .mpc-disabled' )
					.removeClass( 'mpc-current mpc-disabled' );

				$pagination.find( '[data-page="' + _current + '"]' )
					.addClass( 'mpc-current' );

				if( $pagination.is( '.mpc-pagination--classic' ) && _current == 1 ) {
					$pagination.find( '.mpc-pagination__prev' ).addClass( 'mpc-disabled' );
				}

				if ( $pagination.is( '.mpc-pagination--classic' ) && _current == _pages ) {
					$pagination.find( '.mpc-pagination__next' ).addClass( 'mpc-disabled' );
				} else if( _current == _pages ) {
					$pagination.off().remove();
				}

			}
		);
	}

	function mpc_refresh_pagination( $pagination, _grid_id ) {
		$.post(
			_mpc_vars.ajax_url,
			{
				action:     'mpc_pagination_refresh',
				query:      window[ '_' + _grid_id + '_query' ],
				preset:     $pagination.data( 'preset' )
			},
			function( _response ) {
				$pagination.after( _response.data );
				$pagination.remove();

				mpc_pagination_classic( $( '.mpc-pagination[data-grid="' + _grid_id + '"]' ) );
			}
		);
	}

	function mpc_pagination_square_button( $pagination ) {
		var $prev = $pagination.find( '.mpc-pagination__prev' ),
			$next = $pagination.find( '.mpc-pagination__next' ),
			$items = $pagination.find( '.mpc-pagination__link' ),
			_max_size = 0;

		$.each( $pagination.find( '.mpc-pagination__link' ), function() {
			var $this = $( this );

			_max_size = Math.max( $this.width(), $this.height(), _max_size );
		} );

		$items.css( {
			'width' : _max_size + 'px',
			'height' : _max_size + 'px',
			'line-height' : _max_size + 'px'
		} );

		$prev.css( {
			'height' : _max_size + 'px',
			'line-height' : _max_size + 'px'
		} );

		$next.css( {
			'height' : _max_size + 'px',
			'line-height' : _max_size + 'px'
		} );

		$pagination.removeClass( 'mpc--square-init' ).addClass( 'mpc--square');
	}

	var $waypoints = $( '.mpc-pagination--infinity' );

	$waypoints.each( function() {
		var $waypoint = $( this ),
		    _inview = new MPCwaypoint( {
			    element: $waypoint[ 0 ],
			    handler: function() {
				    $waypoint
					    .addClass( 'mpc-infinity-init' )
					    .trigger( 'mpc.infinity' );
			    },
			    offset: '80%'
		    } );
	} );

	var $paginations = $( '.mpc-pagination' );

	$paginations.on( 'mpc.init', function() {
		var $pagination = $( this );

		if( $pagination.is( '.mpc--square-init' ) ) {
			mpc_pagination_square_button( $pagination );
		}

		/* Classic */
		mpc_pagination_classic( $pagination );

		/* Load More */
		mpc_pagination_loadmore( $pagination );

		/* Infinity based on Load More */
		mpc_pagination_infinity( $pagination );

		$( '#' + $pagination.data( 'grid' ) ).on( 'layoutComplete', function() {
			MPCwaypoint.refreshAll();
		});

		$pagination.trigger( 'mpc.inited' );
	});
} )( jQuery );

/*----------------------------------------------------------------------------*\
	PRICING BOX SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function init_carousel( $wrapper, $slider ) {
		$slider.mpcslick( {
			prevArrow: '[data-mpcslider="' + $wrapper.attr( 'id' ) + '"] .mpcslick-prev',
			nextArrow: '[data-mpcslider="' + $wrapper.attr( 'id' ) + '"] .mpcslick-next',
			responsive: [
				{
					breakpoint: 992,
					settings: {
						slidesToShow: 3,
						slidesToScroll: 3
					}
				},
				{
					breakpoint: 768,
					settings: {
						slidesToShow: 2,
						slidesToScroll: 2
					}
				},
				{
					breakpoint: 480,
					settings: {
						slidesToShow: 1,
						slidesToScroll: 1
					}
				}
			]
		} );
	}

	function init_shortcode( $carousel ) {
		var $carousel_init = $carousel.find( '.mpc-pricing-box__wrapper' );

		if( $carousel.is( '.mpc-init--slick' ) ) {
			init_carousel( $carousel, $carousel_init );
		}

		$carousel.trigger( 'mpc.inited' );
	}

	function delay_init( $carousel ) {
		if ( $.fn.mpcslick && ! $carousel.is( '.slick-initialized' ) ) {
			init_shortcode( $carousel );
		} else {
			setTimeout( function() {
				delay_init( $carousel );
			}, 50 );
		}
	}

	var $pricing_boxes = $( '.mpc-pricing-box' );

	$pricing_boxes.each( function() {
		var $pricing_box = $( this );

		$pricing_box.one( 'mpc.init', function() {
			delay_init( $pricing_box );
		});
	});

	/* FrontEnd Editor */
	function init_frontend( $pricing_box ) {
		var $columns = $pricing_box.find( '.mpc-pricing-column' );

		$columns.each( function() {
			var $this = $( this );

			$this.parents( '.vc_mpc_pricing_column' ).addClass( 'mpc-pricing-column' );
			$this.removeClass( 'mpc-pricing-column' );
		} );
	}

	if ( typeof window.InlineShortcodeViewContainer != 'undefined' ) {
		window.InlineShortcodeView_mpc_pricing_box = window.InlineShortcodeViewContainer.extend( {
			events: {
				'click > .vc_controls .vc_element .vc_control-btn-delete': 'destroy',
				'click > .vc_controls .vc_element .vc_control-btn-edit': 'edit',
				'click > .vc_controls .vc_element .vc_control-btn-clone': 'clone',
				'click > .vc_controls .vc_element .vc_control-btn-prepend': 'prependElement',
				'click > .vc_controls .vc_control-btn-append': 'appendElement',
				'click > .vc_empty-element': 'appendElement',
				'mouseenter': 'resetActive',
				'mouseleave': 'holdActive',
				'click > .mpc-carousel__wrapper .mpcslick-prev .mpc-nav__icon' : 'prevSlide',
				'click > .mpc-carousel__wrapper .mpcslick-next .mpc-nav__icon' : 'nextSlide'
			},
			rendered: function() {
				var $pricing_box = this.$el.find( '.mpc-pricing-box' ),
				    $navigation = $pricing_box.siblings( '.mpc-navigation' );

				$pricing_box.addClass( 'mpc-waypoint--init' );

				_mpc_vars.$body.trigger( 'mpc.icon-loaded', [ $pricing_box ] );
				_mpc_vars.$body.trigger( 'mpc.font-loaded', [ $pricing_box ] );
				_mpc_vars.$body.trigger( 'mpc.navigation-loaded', [ $navigation ] );
				_mpc_vars.$body.trigger( 'mpc.inited', [ $pricing_box, $navigation ] );

				setTimeout( function() {
					init_shortcode( $pricing_box );
					init_frontend( $pricing_box );
				}, 250 );

				window.InlineShortcodeView_mpc_pricing_box.__super__.rendered.call( this );
			},
			beforeUpdate: function() {
				this.$el.find( '.mpc-pricing-box__wrapper' ).mpcslick( 'unslick' );
			},
			prevSlide: function() {
				this.$el.find( '.mpc-pricing-box__wrapper' ).mpcslick( 'slickPrev' );
			},
			nextSlide: function() {
				this.$el.find( '.mpc-pricing-box__wrapper' ).mpcslick( 'slickNext' );
			}
		} );
	}
} )( jQuery );

/*----------------------------------------------------------------------------*\
	PRICING COLUMN SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function init_shortcode( $pricing ) {
		$pricing.trigger( 'mpc.inited' );
	}

	var $pricing_columns = $( '.mpc-pricing-column' );

	$pricing_columns.each( function() {
		var $pricing = $( this );

		$pricing.one( 'mpc.init', function() {
			init_shortcode( $pricing );
		});
	});
} )( jQuery );

/*----------------------------------------------------------------------------*\
	PRICING LEGEND SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function init_shortcode( $pricing ) {
		$pricing.trigger( 'mpc.inited' );
	}

	var $pricing_legend = $( '.mpc-pricing-legend' );

	$pricing_legend.each( function() {
		var $pricing = $( this );

		$pricing.one( 'mpc.init', function() {
			init_shortcode( $pricing );
		});
	});
} )( jQuery );

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

/*----------------------------------------------------------------------------*\
	QR CODE SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function delay_init( $qr ) {
		if( typeof( QRCode ) === typeof( Function ) ) {
			init_shortcode( $qr );
		} else {
			setTimeout( function() {
				delay_init( $qr );
			}, 50 );
		}
	}

	function init_shortcode( $qr ) {
		var _qrcode_atts = $qr.data( 'qr' );

		new QRCode( $qr[ 0 ], _qrcode_atts );

		$qr.trigger( 'mpc.inited' );
	}

	if ( typeof window.InlineShortcodeView != 'undefined' ) {
		window.InlineShortcodeView_mpc_qrcode = window.InlineShortcodeView.extend( {
			rendered: function() {
				var $qr = this.$el.find( '.mpc-qrcode' );

				_mpc_vars.$body.trigger( 'mpc.inited', [ $qr ] );

				init_shortcode( $qr );

				_mpc_vars.$document.trigger( 'mpc.init-tooltip', [ $qr.siblings( '.mpc-tooltip' ) ] );

				window.InlineShortcodeView_mpc_qrcode.__super__.rendered.call( this );
			}
		} );
	}

	var $qrcodes = $( '.mpc-qrcode' );

	$qrcodes.each( function() {
		var $qr = $( this );

		$qr.one( 'mpc.init', function () {
			delay_init( $qr );
		} );
	} );
} )( jQuery );

/*----------------------------------------------------------------------------*\
	QUOTE SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function init_shortcode( $quote ) {
		$quote.trigger( 'mpc.inited' );
	}

	if ( typeof window.InlineShortcodeView != 'undefined' ) {
		var $body = $( 'body' );

		window.InlineShortcodeView_mpc_quote = window.InlineShortcodeView.extend( {
			rendered: function() {
				var $quote = this.$el.find( '.mpc-quote' );

				$quote.addClass( 'mpc-waypoint--init' );

				$body.trigger( 'mpc.icon-loaded', [ $quote ] );
				$body.trigger( 'mpc.font-loaded', [ $quote ] );
				$body.trigger( 'mpc.inited', [ $quote ] );

				init_shortcode( $quote );

				window.InlineShortcodeView_mpc_quote.__super__.rendered.call( this );
			}
		} );
	}

	var $quotes = $( '.mpc-quote' );

	$quotes.each( function() {
		var $quote = $( this );

		$quote.one( 'mpc.init', function () {
			init_shortcode( $quote );
		} );
	} );
} )( jQuery );

/*----------------------------------------------------------------------------*\
	RIBBON SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function init_shortcode( $ribbon ) {
		$ribbon.trigger( 'mpc.inited' );
	}

	var $ribbons = $( '.mpc-ribbon' );

	$ribbons.each( function() {
		var $ribbon = $( this );

		$ribbon.one( 'mpc.init', function () {
			init_shortcode( $ribbon );
		} );
	} );
} )( jQuery );

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

/*----------------------------------------------------------------------------*\
	SINGLE POST SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function resize( $single_post ) {
		if( $single_post.is( '.mpc-layout--style_4' ) ) {
			var $post_content = $single_post.find( '.mpc-post__wrapper > .mpc-post__content' );

			$post_content.css( 'margin-bottom', parseInt( $post_content.outerHeight() * -0.5 ) );
		}
	}

	function init_shortcode( $single_post ) {
		mpc_init_lightbox( $single_post, false );

		resize( $single_post );

		$single_post.trigger( 'mpc.inited' );
	}

	if ( typeof window.InlineShortcodeView != 'undefined' ) {
		var $body = $( 'body' );

		window.InlineShortcodeView_mpc_single_post = window.InlineShortcodeView.extend( {
			rendered: function() {
				var $single_post = this.$el.find( '.mpc-single-post' );

				$single_post.addClass( 'mpc-waypoint--init' );

				$body.trigger( 'mpc.icon-loaded', [ $single_post ] );
				$body.trigger( 'mpc.font-loaded', [ $single_post ] );
				$body.trigger( 'mpc.inited', [ $single_post ] );

				init_shortcode( $single_post );

				window.InlineShortcodeView_mpc_single_post.__super__.rendered.call( this );
			}
		} );
	}

	var $single_posts = $( '.mpc-single-post' );

	$single_posts.each( function() {
		var $single_post = $( this );

		$single_post.one( 'mpc.init', function () {
			init_shortcode( $single_post );
		} );

		$single_post.on( 'mpc.resize', function() {

		} );
	} );
} )( jQuery );

/*----------------------------------------------------------------------------*\
	COLUMN SHORTCODE
\*----------------------------------------------------------------------------*/
(function( $ ) {
	"use strict";

	var $sticky_columns = $( '.mpc-column--sticky' );

	$sticky_columns.each( function() {
		$( this ).before( '<div class="mpc-column--spacer"></div>' )
	} );

	_mpc_vars.$window.on( 'mpc.resize', function() {
		if ( _mpc_vars.breakpoints.custom( '(max-width: 992px)' ) ) {
			$.each( $sticky_columns, function() {
				var $sticky = $( this );

				$sticky.removeAttr( 'style' );
				$sticky.prev( '.mpc-column--spacer' ).removeClass( 'mpc-active' );
			} );
		}
	} );

	_mpc_vars.$window.on( 'scroll', function() {
		$sticky_columns.each( function() {
			var $this       = $( this ),
				$parent     = $this.parents( '.mpc-row' ),
				_offset     = $this.data( 'offset' ) != '' ? parseInt( $this.data( 'offset' ) ) : 0,
				_windowY    = window.pageYOffset,
				_margin_top;

			if ( _mpc_vars.breakpoints.custom( '(max-width: 992px)' ) ) {
				$this.removeAttr( 'style' );
				$this.prev( '.mpc-column--spacer' ).removeClass( 'mpc-active' );

				return '';
			}

			_margin_top = _windowY - $parent.offset().top + _offset > 0 ? _windowY - $parent.offset().top + _offset : 0;

			if ( $this.outerHeight() + _margin_top >= $parent.height() ) {
				_margin_top = $parent.height() - $this.outerHeight();

				$this
					.removeAttr( 'style' )
					.css( 'top', _margin_top );
				$this
					.prev( '.mpc-column--spacer' )
					.removeClass( 'mpc-active' );

			} else if ( _margin_top == 0 ) {
				$this.removeAttr( 'style' );
				$this
					.prev( '.mpc-column--spacer' )
					.removeClass( 'mpc-active' );
			} else {
				$this.css( {
					'position': 'fixed',
					'top':      _offset,
					'left':     $this.offset().left,
					'width':    $this.outerWidth( true )
				} );

				$this
					.prev( '.mpc-column--spacer' )
					.css( 'width', $this.outerWidth( true ) )
					.addClass( 'mpc-active' );
			}
		} );
	} );
})( jQuery );

/*----------------------------------------------------------------------------*\
	FLIPBOX SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function calculate( $flipbox ) {
		var $front        = $flipbox.find( '.mpc-flipbox__front' ),
			$back         = $flipbox.find( '.mpc-flipbox__back' ),
			_front_height = $front.outerHeight(),
			_back_height  = $back.outerHeight(),
			_max_height;

		if ( typeof $flipbox.attr( 'data-max-height' ) !== 'undefined' ) {
			_max_height = $flipbox.attr( 'data-max-height' );
		} else {
			_max_height = Math.max( _front_height, _back_height );
		}

		$flipbox.height( _max_height );

		$front.css( 'height', '100%' );
		$back.css( 'height', '100%' );

		$flipbox.trigger( 'mpc.inited' );
	}

	function responsive( $flipbox ) {
		var $front = $flipbox.find( '.mpc-flipbox__front' ),
		    $side = $flipbox.find( '.mpc-flipbox__back' );

		$front.removeAttr( 'style' );
		$side.removeAttr( 'style' );

		calculate( $flipbox );
	}

	function init_shortcode( $flipbox ) {
		if( ! $flipbox.is( '.mpc-init' ) ) return;

		if ( $flipbox.find( 'img' ).length > 0 ) {
			$flipbox.imagesLoaded().always( function() {
				calculate( $flipbox );
			} );
		} else {
			calculate( $flipbox );
		}
	}

	var $flipboxes = $( '.mpc-flipbox' );

	$flipboxes.each( function() {
		var $flipbox = $( this );

		$flipbox.one( 'mpc.init', function() {
			init_shortcode( $flipbox );
		} );

		$flipbox.on( 'mouseenter', function() {
			if( $flipbox.find( '.mpc-parent--init' ).length ) {
				$flipbox.find( '.mpc-container' ).trigger( 'mpc.parent-init' );
				$flipbox.find( '.mpc-parent--init' ).removeClass( 'mpc-parent--init' );
			}
		} );

		$flipbox.on( 'click', function( event ) {
			if ( typeof event.currentTarget.href !== 'undefined'
				&& $flipbox.hasClass( 'mpc-flipbox--animate' ) ) {
				window.location.href = event.currentTarget.href;
			} else if ( $flipbox.hasClass( 'mpc-flipbox--animate' ) ) {
				event.stopPropagation();
				$flipbox.toggleClass( 'mpc-flipbox--animate' );
			}else if ( $flipbox.hasClass( 'mpc-flipbox--click' ) ) {
				event.preventDefault();
				$flipbox.toggleClass( 'mpc-flipbox--animate' );
			}
		} );
	});

	_mpc_vars.$window.on( 'mpc.resize load', function() {
		$.each( $flipboxes, function() {
			responsive( $( this ) );
		} );
	} );
} )( jQuery );

/*----------------------------------------------------------------------------*\
	CUBEBOX SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function calculate( $cubebox ) {
		var $front        = $cubebox.find( '.mpc-cubebox__front .mpc-cubebox-side' ),
		    $side         = $cubebox.find( '.mpc-cubebox__side .mpc-cubebox-side' ),
			_front_height = $front.outerHeight(),
			_side_height  = $side.outerHeight(),
			_max_height;

		if ( typeof $cubebox.attr( 'data-max-height' ) !== 'undefined' ) {
			_max_height = $cubebox.attr( 'data-max-height' );
		} else {
			_max_height = Math.max( _front_height, _side_height );
		}

		$cubebox.height( _max_height );

		$front.css( 'height', '100%' );
		$side.css( 'height', '100%' );

		$cubebox.trigger( 'mpc.inited' );
	}

	function responsive( $cubebox ) {
		var $front = $cubebox.find( '.mpc-cubebox__front' ),
		    $side = $cubebox.find( '.mpc-cubebox__side' );

		$front.removeAttr( 'style' );
		$side.removeAttr( 'style' );

		calculate( $cubebox );
	}

	function init_shortcode( $cubebox ) {
		if( !$cubebox.is( '.mpc-init' ) ) return;

		if( $cubebox.find( 'img' ).length > 0 ) {
			$cubebox.imagesLoaded().always( function() {
				calculate( $cubebox );
			} );
		} else {
			calculate( $cubebox );
		}

		$cubebox.trigger( 'mpc.inited' );
	}

	var $cubeboxes = $( '.mpc-cubebox' );

	$cubeboxes.each( function() {
		var $cubebox = $( this );

		$cubebox.one( 'mpc.init', function() {
			init_shortcode( $cubebox );
		} );

		$cubebox.on( 'mouseenter', function() {
			$cubebox.addClass( 'mpc-flipped' );

			if( $cubebox.find( '.mpc-parent--init' ).length ) {
				$cubebox.find( '.mpc-container' ).trigger( 'mpc.parent-init' );
				$cubebox.find( '.mpc-parent--init' ).removeClass( 'mpc-parent--init' );
			}
		}).on( 'mouseleave', function(){
			$cubebox.removeClass( 'mpc-flipped' );
		} );

		$cubebox.on( 'click', function( event ) {
			if ( typeof event.currentTarget.href !== 'undefined'
				&& $cubebox.hasClass( 'mpc-cubebox--animate' ) ) {
				window.location.href = event.currentTarget.href;
			} else if ( $cubebox.hasClass( 'mpc-cubebox--animate' ) ) {
				event.stopPropagation();
				$cubebox.toggleClass( 'mpc-cubebox--animate' );
			} else if ( $cubebox.hasClass( 'mpc-cubebox--click' ) ) {
				event.preventDefault();
				$cubebox.toggleClass( 'mpc-cubebox--animate' );
			}
		} );
	} );

	_mpc_vars.$window.on( 'mpc.resize load', function() {
		$.each( $cubeboxes, function() {
			responsive( $( this ) );
		} );
	} );


} )( jQuery );

/*----------------------------------------------------------------------------*\
	TABS SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function switch_tab( $this ) {
		var $tab_container = $this.closest( '.mpc-tabs' ),
		 	$tab           = $tab_container.find( '#' + $this.attr( 'data-tab_id' ) ),
		    $tabs          = $tab.siblings( '.mpc-tab' );

		$this.siblings().removeClass( 'mpc-active' );
		$this.addClass( 'mpc-active' );

		$tabs.attr( 'data-active', false );
		$tab.attr( 'data-active', true );

		if( $tab.find( '.mpc-parent--init' ).length ) {
			$tab.trigger( 'mpc.parent-init' );
			$tab.find( '.mpc-parent--init' ).removeClass( 'mpc-parent--init' );
		}
	}

	function responsive( $tabs ) {
		var _is_12_col = $tabs.parents( '.mpc-column' ).is( '.vc_col-sm-12' );

		if(  _mpc_vars.breakpoints.custom( '(max-width: 767px)' ) || ( _mpc_vars.breakpoints.medium && !_is_12_col ) ) {
			if( $tabs.is( '.mpc-tabs--left' ) || $tabs.is( '.mpc-tabs--right' ) ) {
				var _class = $tabs.is( '.mpc-tabs--left' ) ? 'left' : 'right';

				$tabs
					.attr( 'data-nav-position', _class )
					.removeClass( 'mpc-tabs--left mpc-tabs--right' )
					.addClass( 'mpc-tabs--top' );
			}
		} else if( $tabs.attr( 'data-nav-position' ) == 'left' || $tabs.attr( 'data-nav-position' ) == 'right' ) {
			var _position = $tabs.attr( 'data-nav-position' );

			$tabs
				.removeClass( 'mpc-tabs--top' )
				.addClass( 'mpc-tabs--' + _position )
				.removeAttr( 'data-nav-position' );
		}
	}

	function init_shortcode( $tabs ) {
		var $tabs_nav = $tabs.find( '.mpc-tabs__nav-item' ),
			_hash_url = window.location.hash;

		$tabs_nav.on( 'click', function() {
			switch_tab( $( this ) );
		} );

		if ( _hash_url !== undefined ) {
			use_hash( $tabs, _hash_url );
		}

		var $tab = $tabs.find( '.mpc-tab[data-active="true"]' );
		if ( $tab.length ) {
			if ( $tab.find( '.mpc-parent--init' ).length ) {
				$tab.trigger( 'mpc.parent-init' );
				$tab.find( '.mpc-parent--init' ).removeClass( 'mpc-parent--init' );
			}
		}

		responsive( $tabs );

		$tabs.trigger( 'mpc.inited' );
	}

	function use_hash( $tabs, _hash_url ) {
		var _open_tab		    = '#open-tab_',
			_open_tab_indicator = _hash_url.indexOf( _open_tab );

		if (  _open_tab_indicator < 0 ) {
			return false;
		}

		var _possible_tab_id = _hash_url.substr( _open_tab.length ),
			$possible_tab    = $tabs.find( 'li[data-tab_id="' + _possible_tab_id + '"]');

		if ( $possible_tab.length ) {

			$possible_tab.each( function() {
				switch_tab( $( this ) );
			} );
		}
	}

	var $tabs = $( '.mpc-tabs' );

	$tabs.each( function() {
		var $tab = $( this );

		$tab.one( 'mpc.init', function () {
			init_shortcode( $tab );
		} );
	} );

	_mpc_vars.$window.on( 'mpc.resize', function() {
		$.each( $tabs, function() {
			responsive( $( this ) );
		} );
	} );

} )( jQuery );
/*----------------------------------------------------------------------------*\
	ACCORDION SHORTCODE
\*----------------------------------------------------------------------------*/
(function( $ ) {
	"use strict";

	function calculate_height( $items ) {
		$items.each( function() {
			var $item = $( this ).find( '.mpc-accordion-item__content' );

			if( $item.attr( 'data-active' ) === 'true' ) {
				$item
					.removeAttr( 'style' )
					.removeClass( 'mpc-hidden' )
					.css( 'height', parseInt( $item.height() ) );
			}

			$item.addClass( 'mpc-hidden' );
		} );
	}

	function init_shortcode( $accordion ) {
		var $items             = $accordion.find( '.mpc-accordion__item' ),
		    $active            = $accordion.find( '[data-active="true"].mpc-accordion-item__content' ),
		    $accordions_toggle = $accordion.find( '.mpc-accordion-item__heading' );

		if( $active.find( '.mpc-parent--init' ).length ) {
			$active.trigger( 'mpc.parent-init' );
			$active.find( '.mpc-parent--init' ).removeClass( 'mpc-parent--init' );
		}

		calculate_height( $items );

		$accordions_toggle.on( 'click', function() {
			toggle_accordion( $( this ) );
		} );

		setTimeout( function() {
			calculate_height( $items );
		}, 250 );

		$accordion.trigger( 'mpc.inited' );
	}

	function toggle_accordion( $accordion ) {
		var $item    = $accordion.siblings( '.mpc-accordion-item__content' ),
		    $current = $accordion.parents( '.mpc-accordion' ).find( '[data-active="true"].mpc-accordion-item__content' ),
		    _height  = $item.find( '.mpc-accordion-item__wrapper' ).outerHeight( true ),
		    _toggle  = $accordion.parents( '.mpc-accordion' ).is( '.mpc-accordion--toggle' );

		if( _toggle ) {

			if( $item.attr( 'data-active' ) === 'true' ) {
				$item.velocity( 'stop' ).velocity( { height: 0 }, 300 );

				$item.removeAttr( 'data-active' );
				$accordion.removeClass( 'mpc-active' );
			} else if ( $item !== $current ) {
				$current.velocity( 'stop' ).velocity( { height: 0 }, 300 );
				$item.velocity( 'stop' ).velocity( { height: _height }, 300 );

				$current.removeAttr( 'data-active' ).siblings( '.mpc-accordion-item__heading' ).removeClass( 'mpc-active' );
				$item.attr( 'data-active', 'true' );
				$accordion.addClass( 'mpc-active' );

				if( $item.find( '.mpc-parent--init' ).length ) {
					$item.trigger( 'mpc.parent-init' );
					$item.find( '.mpc-parent--init' ).removeClass( 'mpc-parent--init' );
				}
			}
		} else {
			if( $item.attr( 'data-active' ) === 'true' ) {
				$item.velocity( 'stop' ).velocity( { height: 0 }, 300 );

				$item.removeAttr( 'data-active' );
				$accordion.removeClass( 'mpc-active' );
			} else {
				$item.velocity( 'stop' ).velocity( { height: _height }, 300 );

				$item.attr( 'data-active', 'true' );
				$accordion.addClass( 'mpc-active' );

				if( $item.find( '.mpc-parent--init' ).length ) {
					$item.trigger( 'mpc.parent-init' );
					$item.find( '.mpc-parent--init' ).removeClass( 'mpc-parent--init' );
				}
			}
		}

		setTimeout( function() {
			$accordion.parents( '.mpc-row' ).trigger( 'mpc.recalc' );
		}, 300 );
	}

	var $accordions = $( '.mpc-accordion' );

	$accordions.each( function() {
		var $accordion = $( this );

		$accordion.one( 'mpc.init', function() {
			init_shortcode( $accordion );
		} );
	} );

	_mpc_vars.$window.on( 'mpc.resize', function() {
		$.each( $accordions, function() {
			calculate_height( $( this ).find( '.mpc-accordion__item' ) );
		} );
	} );
})( jQuery );

/*----------------------------------------------------------------------------*\
	TESTIMONIAL SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function init_shortcode( $testimonial ) {
		$testimonial.trigger( 'mpc.inited' );
	}

	if ( typeof window.InlineShortcodeView != 'undefined' ) {
		window.InlineShortcodeView_mpc_testimonial = window.InlineShortcodeView.extend( {
			rendered: function() {
				var $testimonial = this.$el.find( '.mpc-testimonial' );

				$testimonial.addClass( 'mpc-waypoint--init' );

				_mpc_vars.$body.trigger( 'mpc.icon-loaded', [ $testimonial ] );
				_mpc_vars.$body.trigger( 'mpc.font-loaded', [ $testimonial ] );
				_mpc_vars.$body.trigger( 'mpc.inited', [ $testimonial ] );

				init_shortcode( $testimonial );

				window.InlineShortcodeView_mpc_testimonial.__super__.rendered.call( this );
				this.afterRender();
			},
			afterRender: function() {
				var _parent = vc.shortcodes.findWhere( { id: this.model.get( 'parent_id' ) } );

				setTimeout( function() {
					_parent.trigger( 'mpc:forceRender' );
				}, 250 );
			}
		} );
	}

	var $testimonials = $( '.mpc-testimonial' );

	$testimonials.each( function() {
		var $testimonial = $( this );

		$testimonial.one( 'mpc.init', function () {
			init_shortcode( $testimonial );
		} );
	} );
} )( jQuery );

/*----------------------------------------------------------------------------*\
	TEXTBLOCK SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function init_shortcode( $textblock ) {
		$textblock.trigger( 'mpc.inited' );
	}

	if ( typeof window.InlineShortcodeView != 'undefined' ) {
		var $body = $( 'body' );

		window.InlineShortcodeView_mpc_textblock = window.InlineShortcodeView.extend( {
			rendered: function() {
				var $textblock = this.$el.find( '.mpc-textblock' );

				$textblock.addClass( 'mpc-waypoint--init' );

				$body.trigger( 'mpc.icon-loaded', [ $textblock ] );
				$body.trigger( 'mpc.font-loaded', [ $textblock ] );
				$body.trigger( 'mpc.inited', [ $textblock ] );

				init_shortcode( $textblock );

				window.InlineShortcodeView_mpc_textblock.__super__.rendered.call( this );
			}
		} );
	}

	var $textblocks = $( '.mpc-textblock' );

	$textblocks.each( function() {
		var $textblock = $( this );

		$textblock.one( 'mpc.init', function () {
			init_shortcode( $textblock );
		} );
	} );
} )( jQuery );

/*----------------------------------------------------------------------------*\
	TIMELINE BASIC SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	var _icon_spots;

	function in_range( _r_start, _r_end, _needle ) {
		return _r_start <= _needle && _needle <= _r_end;
	}

	function spots_walker( _icon_pos, _spots, _pos_range ) {
		// ToDo: Check if the pointer is inside item range, test more variations: middle, bottom alignment etc
		var _modifier = 0;
		_spots.forEach( function( _spot ) {
			if( in_range( _spot.top, _spot.bottom, _icon_pos.top ) ) {
				// top inside, move below
				return _modifier = _spot.bottom - _icon_pos.top + 5;
			} else if( in_range( _spot.top, _spot.bottom, _icon_pos.bottom ) ) {
				// bottom inside, move above
				return _modifier = _spot.top - _icon_pos.bottom + 5;
			}
		} );

		return _modifier;
	}

	function check_position_overflow( $parent, $icon, $pointer ) {
		var _icon_pos = {
				top: $icon.offset().top,
				bottom: $icon.offset().top + $icon.outerHeight()
			},
			_pos_range = {
				top: $parent.offset().top,
				bottom: $parent.offset().top + $parent.height()
			},
			_side = $parent.attr( 'data-side' ),
			_modifier = 0;

		if( _side == 'left' ) {
			_modifier = spots_walker( _icon_pos, _icon_spots.right, _pos_range );
			_icon_pos = track_icon_recalc( $icon, $pointer, _icon_pos, _modifier );
			_icon_spots.left.push( _icon_pos );

			return true;
		} else {
			_modifier = spots_walker( _icon_pos, _icon_spots.left, _pos_range );
			_icon_pos = track_icon_recalc( $icon, $pointer, _icon_pos, _modifier );
			_icon_spots.right.push( _icon_pos );

			return true;
		}
	}

	function track_icon_recalc( $icon, $pointer, _cur_pos, _modifier ) {
		if( _modifier == 0 ) { return _cur_pos; }

		_cur_pos.top += _modifier;
		_cur_pos.bottom += _modifier;

		$icon.css( { "margin-top" : parseInt( $icon.css( 'margin-top' ) ) +  parseInt( _modifier ) } );
		$pointer.css( { "margin-top" : parseInt( $pointer.css( 'margin-top' ) ) + parseInt( _modifier ) } );

		return _cur_pos;
	}

	function track_icon_calc( $timeline ) {
		var $timeline_items = $timeline.find( '.mpc-timeline-item__wrap' );

		$timeline_items.each( function() {
			var $parent = $( this ),
				$item = $parent.find( '.mpc-timeline-item' ),
				$icon = $parent.find( '.mpc-tl-icon' ),
				$pointer = $item.find( '.mpc-tl-before' ),
				_top = 0;

			if( $timeline.is( '.mpc-layout--left' ) ) {
				$parent.attr( 'data-side', 'right' );
			} else if( $timeline.is( '.mpc-layout--right' ) ) {
				$parent.attr( 'data-side', 'left' );
			} else {
				if( $parent.css( 'left' ) == '0px' ) {
					$parent.attr( 'data-side', 'left' );
				} else {
					$parent.attr( 'data-side', 'right' );
				}
			}

			$pointer.removeAttr( 'style' );
			if( $parent.attr( 'data-side' ) == 'left' && !$timeline.is( '.mpc-layout--right' ) && _mpc_vars.breakpoints.custom( '(min-width: 767px)' ) ) {
				$pointer.css( { 'margin-left' : parseInt( $item.css( 'border-right-width' ) ) } ) ;
			} else {
				$pointer.css( { 'margin-right' : parseInt( $item.css( 'border-left-width' ) ) } ) ;
			}

			if( $timeline.is( '.mpc-pointer--middle' ) ) {
				$pointer.css( { 'margin-top' : parseInt( $pointer.css( 'margin-top' ) ) - parseInt( $pointer.outerHeight() * 0.5 ) })
			}

			_top = $pointer.offset().top - $parent.offset().top - $icon.height() * 0.5;
			if( !$timeline.is( '.mpc-pointer--right-triangle' ) ) {
				_top += $pointer.outerHeight() * .5;
			}

			$icon.css( { "margin-top": parseInt( _top ) } );

			if( $timeline.is( '.mpc-layout--both' ) ) {
				if ( $parent.length && $icon.length && $pointer.length ) {
					check_position_overflow( $parent, $icon, $pointer );
				}
			}
		});
	}

	function ornament_icon_pos( $timeline ) {
		var $icon = $timeline.find( '.mpc-track__icon' ),
			$track = $timeline.find( '.mpc-timeline__track' );

		$icon.css( { 'margin-left' : - parseInt( ( $icon.outerWidth() + $track.outerWidth() ) * .5 ) } );
	}

	function delay_init( $timeline ) {
		if ( $.fn.isotope && $.fn.imagesLoaded ) {
			init_shortcode( $timeline );
		} else {
			setTimeout( function() {
				delay_init( $timeline );
			}, 50 );
		}
	}

	function init_shortcode( $timeline ) {
		var $row = $timeline.parents( '.mpc-row' );

		ornament_icon_pos( $timeline );

		$timeline.trigger( 'mpc.inited' ); // removing float

		var _isotope = {
			itemSelector: '.mpc-timeline-item__wrap',
			layoutMode: 'masonry'
		};

		if( $timeline.is( '.mpc-layout--right' ) ) {
			_isotope.isOriginLeft = false;
		}

		$timeline.imagesLoaded().done( function() {
			$timeline.on( 'layoutComplete', function() {
				_icon_spots = {
					'left' : [],
					'right' : []
				};
				track_icon_calc( $( this ) );

				MPCwaypoint.refreshAll();
			} );

			$timeline.isotope(  _isotope );

			$row.on( 'mpc.rowResize', function() {
				if( $timeline.data( 'isotope' ) ) {
					$timeline.isotope( 'layout' );
				}
			} );

			_mpc_vars.$document.ready( function() {
				setTimeout( function() {
					if( $timeline.data( 'isotope' ) ) {
						$timeline.isotope( 'layout' );
					}
				}, 50 );
			});
		});
	}

	var $timelines_basic = $( '.mpc-timeline-basic' );

	$timelines_basic.each( function() {
		var $timeline_basic = $( this );

		$timeline_basic.one( 'mpc.init', function() {
			delay_init( $timeline_basic );
		} );
	});

} )( jQuery );

/*----------------------------------------------------------------------------*\
 TIMELINE BASIC SHORTCODE
 \*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function init_shortcode( $item ) {
		//if( $item.is( '.mpc-animation' ) )
		$item.trigger( 'mpc.inited' );
	}

	var $timeline_items = $( '.mpc-timeline-item__wrap' );

	$timeline_items.each( function() {
		var $timeline_item = $( this );

		$timeline_item.one( 'mpc.init', function() {
			init_shortcode( $timeline_item );
		} );
	});

} )( jQuery );

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

/*----------------------------------------------------------------------------*\
	ADD TO CART SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function adjust_width( $button ) {
		var	$parent = $button.parent( '.mpc-wc-add_to_cart-wrap' ),
			   $title_hover = $button.find( '.mpc-atc__title-hover' ),
			   _css = { left: 0, top: 0 }, _css_hover = {};

		_css.width = $button.width();
		_css_hover.width = $title_hover.outerWidth( true );
		_css.height = $button.height();
		_css_hover.height = $title_hover.outerHeight( true );

		if( _css.width + 10 > _css_hover.width && _css_hover.width < _css.width - 10 ) { // Tolerance to fix skipping content
			_css_hover.width = _css.width;
		}
		if( _css.height + 10 > _css_hover.height && _css_hover.height < _css.height - 10 ) { // Tolerance to fix skipping content
			_css_hover.height = _css.height;
		}

		if( typeof _css_hover.width !== typeof undefined || typeof _css_hover.height !== typeof undefined ) {
			$button.css( _css );
			$parent.css( _css );

			$button.attr( 'data-css', JSON.stringify( _css ) )
				.attr( 'data-css-hover', JSON.stringify( _css_hover ) );

			$parent.on( 'mouseenter', function() {
				var $this = $( this ).children( '.mpc-wc-add_to_cart' ),
					_default = $this.data( 'css' ),
					_hover  = $this.data( 'css-hover' );

				_hover.left = ( _default.width - _hover.width ) * 0.5;
				_hover.top = ( _default.height - _hover.height ) * 0.5;

				$this.css( _hover );
			} ).on( 'mouseleave', function() {
				var $this = $( this ).children( '.mpc-wc-add_to_cart' );

				$this.css( $this.data( 'css' ) );
			});
		}
	}

	function add_to_cart_call( _ev, $button ) {
		var cart_data = $button.data( 'cart' ),
		    $notices = $button.find( '.mpc-atc__notices' );

		if( cart_data && !$button.is( '.mpc-disabled' ) ) {
			_ev.preventDefault();

			$button.addClass( 'mpc-disabled' );
			$button.attr( 'data-notice', 'show:loader' );

			$.post(
				_mpc_vars.ajax_url,
				{
					action: 'mpc_wc_add_to_cart',
					product_id: cart_data.product_id,
					variation_id: cart_data.variation_id,
					dataType: 'json'
				},
				function( _response ) {
					if( _response ) {
						$button.attr( 'data-notice', 'show:success' );

						$( document.body ).trigger( 'added_to_cart', [ _response.fragments, _response.cart_hash, null ] );
					} else {
						$button.attr( 'data-notice', 'show:error' );

						setTimeout( function() {
							$button.removeClass( 'mpc-disabled' )
									.removeAttr( 'data-notice' );
						}, 2000 );
					}
				}
			);
		} else if( $button.is( '.mpc-disabled' ) && $notices.attr( 'data-notice' ) != '' ) {
			$button.removeClass( 'mpc-disabled' )
					.removeAttr( 'data-notice' );
		}
	}

	function init_shortcode( $button ) {
		if( $button.is( '.mpc-auto-size' ) && !$button.is( '.mpc-display--block' ) ) {
			adjust_width( $button );
		}

		$button.trigger( 'mpc.inited' );
	}

	if ( typeof window.InlineShortcodeView != 'undefined' ) {
		var $body = $( 'body' );

		window.InlineShortcodeView_mpc_wc_add_to_cart = window.InlineShortcodeView.extend( {
			rendered: function() {
				var $button = this.$el.find( '.mpc-button' );

				$button.addClass( 'mpc-waypoint--init' );

				$body.trigger( 'mpc.icon-loaded', [ $button ] );
				$body.trigger( 'mpc.font-loaded', [ $button ] );
				$body.trigger( 'mpc.inited', [ $button ] );

				init_shortcode( $button );

				window.InlineShortcodeView_mpc_wc_add_to_cart.__super__.rendered.call( this );
			}
		} );
	}

	var $buttons = $( '.mpc-wc-add_to_cart' );

	$buttons.each( function() {
		var $button = $( this );

		$button.one( 'mpc.init', function () {
			init_shortcode( $button );
		} );
	} );

	$( document ).on( 'click', '.mpc-wc-add_to_cart', function( _ev ){
		add_to_cart_call( _ev, $( this ) );
	} );
} )( jQuery );

/*----------------------------------------------------------------------------*\
	WC PRODUCT SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function init_shortcode( $product ) {
		mpc_init_lightbox( $product, false );

		$product.trigger( 'mpc.inited' );
	}

	if ( typeof window.InlineShortcodeView != 'undefined' ) {
		var $body = $( 'body' );

		window.InlineShortcodeView_mpc_wc_product = window.InlineShortcodeView.extend( {
			rendered: function() {
				var $product = this.$el.find( '.mpc-wc-product' );

				$product.addClass( 'mpc-waypoint--init' );

				$body.trigger( 'mpc.icon-loaded', [ $product ] );
				$body.trigger( 'mpc.font-loaded', [ $product ] );
				$body.trigger( 'mpc.inited', [ $product ] );

				init_shortcode( $product );

				window.InlineShortcodeView_mpc_wc_product.__super__.rendered.call( this );
			}
		} );
	}

	var $products = $( '.mpc-wc-product' );

	$products.each( function() {
		var $product = $( this );

		$product.one( 'mpc.init', function () {
			init_shortcode( $product );
		} );
	} );
} )( jQuery );

/*----------------------------------------------------------------------------*\
	PRODUCTS CATEGORY SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function init_shortcode( $products_category ) {
		$products_category.trigger( 'mpc.inited' );
	}

	if ( typeof window.InlineShortcodeView != 'undefined' ) {
		var $body = $( 'body' );

		window.InlineShortcodeView_mpc_wc_category = window.InlineShortcodeView.extend( {
			rendered: function() {
				var $products_category = this.$el.find( '.mpc-wc-category' );

				$products_category.addClass( 'mpc-waypoint--init' );

				$body.trigger( 'mpc.icon-loaded', [ $products_category ] );
				$body.trigger( 'mpc.font-loaded', [ $products_category ] );
				$body.trigger( 'mpc.inited', [ $products_category ] );

				init_shortcode( $products_category );

				window.InlineShortcodeView_mpc_wc_category.__super__.rendered.call( this );
			}
		} );
	}

	var $products_categories = $( '.mpc-wc-category' );

	$products_categories.each( function() {
		var $products_category = $( this );

		$products_category.one( 'mpc.init', function () {
			init_shortcode( $products_category );
		} );
	} );
} )( jQuery );

/*----------------------------------------------------------------------------*\
	CAROUSEL PRODUCTS CATEGORIES SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {

	function delay_init( $carousel ) {
		if ( $.fn.mpcslick && ! $carousel.is( '.slick-initialized' ) ) {
			init_shortcode( $carousel );
		} else {
			setTimeout( function() {
				delay_init( $carousel );
			}, 50 );
		}
	}

	function init_shortcode( $carousel ) {
		$carousel.mpcslick({
			prevArrow: '[data-mpcslider="' + $carousel.attr( 'id' ) + '"] .mpcslick-prev',
			nextArrow: '[data-mpcslider="' + $carousel.attr( 'id' ) + '"] .mpcslick-next',
			responsive: _mpc_vars.carousel_breakpoints( $carousel ),
			rtl: _mpc_vars.rtl.global()
		});

		$carousel.on( 'mouseleave', function() {
			setTimeout( function() {
				var _slick = $carousel.mpcslick( 'getSlick' );

				if( _slick.options.autoplay ) {
					$carousel.mpcslick( 'play' );
				}
			}, 250 );
		});

		$carousel.trigger( 'mpc.inited' );
	}

	if ( typeof window.InlineShortcodeView != 'undefined' ) {
		var $body = $( 'body' );

		window.InlineShortcodeView_mpc_wc_carousel_categories = window.InlineShortcodeView.extend( {
			events: {
				'click > .vc_controls .vc_control-btn-delete': 'destroy',
				'click > .vc_controls .vc_control-btn-edit': 'edit',
				'click > .vc_controls .vc_control-btn-clone': 'clone',
				'mousemove': 'checkControlsPosition',
				'click > .mpc-carousel__wrapper .mpcslick-prev .mpc-nav__icon': 'prevSlide',
				'click > .mpc-carousel__wrapper .mpcslick-next .mpc-nav__icon': 'nextSlide'
			},
			rendered: function() {
				var $carousel_wc_categories = this.$el.find( '.mpc-wc-carousel-categories' ),
					$navigation = $carousel_wc_categories.siblings( '.mpc-navigation' );

				$carousel_wc_categories.addClass( 'mpc-waypoint--init' );

				_mpc_vars.$body.trigger( 'mpc.icon-loaded', [ $carousel_wc_categories, $navigation ] );
				_mpc_vars.$body.trigger( 'mpc.font-loaded', [ $carousel_wc_categories, $navigation ] );
				_mpc_vars.$body.trigger( 'mpc.navigation-loaded', [ $navigation ] );
				_mpc_vars.$body.trigger( 'mpc.inited', [ $carousel_wc_categories, $navigation ] );

				setTimeout( function() {
					delay_init( $carousel_wc_categories );
				}, 250 );

				window.InlineShortcodeView_mpc_wc_carousel_categories.__super__.rendered.call( this );
			},
			beforeUpdate: function() {
				this.$el.find( '.mpc-wc-carousel-categories' ).mpcslick( 'unslick' );

				window.InlineShortcodeView_mpc_wc_carousel_categories.__super__.beforeUpdate.call( this );
			},
			prevSlide: function() {
				this.$el.find( '.mpc-wc-carousel-categories' ).mpcslick( 'slickPrev' );
			},
			nextSlide: function() {
				this.$el.find( '.mpc-wc-carousel-categories' ).mpcslick( 'slickNext' );
			}
		} );
	}

	var $carousels_wc_categories = $( '.mpc-wc-carousel-categories' );

	$carousels_wc_categories.each( function() {
		var $carousel_wc_categories = $( this );

		$carousel_wc_categories.one( 'mpc.init', function() {
			delay_init( $carousel_wc_categories );
		} );
	});
} )( jQuery );

/*----------------------------------------------------------------------------*\
	GRID PRODUCTS CATEGORIES SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function delay_init( $grid ) {
		if ( $.fn.isotope && $.fn.imagesLoaded ) {
			init_shortcode( $grid );
		} else {
			setTimeout( function() {
				delay_init( $grid );
			}, 50 );
		}
	}

	function init_shortcode( $grid ) {
		var $row = $grid.parents( '.vc_row' );

		$grid.imagesLoaded().done( function() {
			$grid.on( 'layoutComplete', function() {
				MPCwaypoint.refreshAll();
			} );

			$grid.isotope( {
				itemSelector: '.mpc-wc-category',
				layoutMode: 'masonry'
			} );

			$row.on( 'mpc.rowResize', function() {
				if( $grid.data( 'isotope' ) ) {
					$grid.isotope( 'layout' );
				}
			} );
		} );

		$grid.on( 'mpc.loaded', function() {
			mpc_init_lightbox( $grid, true );
		} );

		$grid.trigger( 'mpc.inited' );
	}

	var $grids_wc_categories = $( '.mpc-wc-grid-categories' );

	$grids_wc_categories.each( function() {
		var $grid_wc_categories = $( this );

		$grid_wc_categories.one( 'mpc.init', function () {
			delay_init( $grid_wc_categories );
		} );
	});
} )( jQuery );

/*----------------------------------------------------------------------------*\
	CAROUSEL PRODUCTS SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function delay_init( $carousel ) {
		if ( $.fn.mpcslick && ! $carousel.is( '.slick-initialized' ) ) {
			init_shortcode( $carousel );
		} else {
			setTimeout( function() {
				delay_init( $carousel );
			}, 50 );
		}
	}

	function init_shortcode( $carousel ) {
		$carousel.on( 'init', function() {
			setTimeout( function(){
				mpc_init_lightbox( $carousel, true );
			}, 250 );
		});

		$carousel.mpcslick({
			prevArrow: '[data-mpcslider="' + $carousel.attr( 'id' ) + '"] .mpcslick-prev',
			nextArrow: '[data-mpcslider="' + $carousel.attr( 'id' ) + '"] .mpcslick-next',
			responsive: _mpc_vars.carousel_breakpoints( $carousel ),
			rtl: _mpc_vars.rtl.global()
		});

		$carousel.on( 'mouseleave', function() {
			setTimeout( function() {
				var _slick = $carousel.mpcslick( 'getSlick' );

				if( _slick.options.autoplay ) {
					$carousel.mpcslick( 'play' );
				}
			}, 250 );
		});

		$carousel.trigger( 'mpc.inited' );
	}

	if ( typeof window.InlineShortcodeView != 'undefined' ) {
		window.InlineShortcodeView_mpc_wc_carousel_products = window.InlineShortcodeView.extend( {
			events: {
				'click > .vc_controls .vc_control-btn-delete': 'destroy',
				'click > .vc_controls .vc_control-btn-edit': 'edit',
				'click > .vc_controls .vc_control-btn-clone': 'clone',
				'mousemove': 'checkControlsPosition',
				'click > .mpc-carousel__wrapper .mpcslick-prev .mpc-nav__icon': 'prevSlide',
				'click > .mpc-carousel__wrapper .mpcslick-next .mpc-nav__icon': 'nextSlide'
			},
			rendered: function() {
				var $wc_carousel_products = this.$el.find( '.mpc-wc-carousel-products' ),
				    $navigation = $wc_carousel_products.siblings( '.mpc-navigation' );

				$wc_carousel_products.addClass( 'mpc-waypoint--init' );

				_mpc_vars.$body.trigger( 'mpc.icon-loaded', [ $wc_carousel_products, $navigation ] );
				_mpc_vars.$body.trigger( 'mpc.font-loaded', [ $wc_carousel_products, $navigation ] );
				_mpc_vars.$body.trigger( 'mpc.navigation-loaded', [ $navigation ] );
				_mpc_vars.$body.trigger( 'mpc.inited', [ $wc_carousel_products, $navigation ] );

				setTimeout( function() {
					delay_init( $wc_carousel_products );
				}, 250 );

				window.InlineShortcodeView_mpc_wc_carousel_products.__super__.rendered.call( this );
			},
			beforeUpdate: function() {
				this.$el.find( '.mpc-wc-carousel-products' ).mpcslick( 'unslick' );

				window.InlineShortcodeView_mpc_wc_carousel_products.__super__.beforeUpdate.call( this );
			},
			prevSlide: function() {
				this.$el.find( '.mpc-wc-carousel-products' ).mpcslick( 'slickPrev' );
			},
			nextSlide: function() {
				this.$el.find( '.mpc-wc-carousel-products' ).mpcslick( 'slickNext' );
			}
		} );
	}

	var $wc_carousels_products = $( '.mpc-wc-carousel-products' );

	$wc_carousels_products.each( function() {
		var $wc_carousel_products = $( this );

		$wc_carousel_products.one( 'mpc.init', function() {
			delay_init( $wc_carousel_products );
		} );
	});
} )( jQuery );

/*----------------------------------------------------------------------------*\
	GRID PRODUCTS SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function delay_init( $grid ) {
		if ( $.fn.isotope && $.fn.imagesLoaded ) {
			init_shortcode( $grid );
		} else {
			setTimeout( function() {
				delay_init( $grid );
			}, 50 );
		}
	}

	function init_shortcode( $grid_products ) {
		var $row = $grid_products.parents( '.mpc-row' );

		$grid_products.imagesLoaded().done( function() {
			$grid_products.on( 'layoutComplete', function() {
				mpc_init_lightbox( $grid_products, true );
				MPCwaypoint.refreshAll();
			} );

			$grid_products.isotope( {
				itemSelector: '.mpc-wc-product',
				layoutMode: 'masonry',
				masonry: {
					columnWidth: '.mpc-grid-sizer'
				}
			} );

			$row.on( 'mpc.rowResize', function() {
				if( $grid_products.data( 'isotope' ) ) {
					$grid_products.isotope( 'layout' );
				}
			} );
		} );

		$grid_products.trigger( 'mpc.inited' );
	}

	if ( typeof window.InlineShortcodeView != 'undefined' ) {
		window.InlineShortcodeView_mpc_wc_grid_products = window.InlineShortcodeView.extend( {
			rendered: function() {
				var $grid_products = this.$el.find( '.mpc-wc-grid-products' ),
					$pagination = $grid_products.siblings( '.mpc-pagination' );

				$grid_products.addClass( 'mpc-waypoint--init' );

				_mpc_vars.$body.trigger( 'mpc.icon-loaded', [ $grid_products, $pagination ] );
				_mpc_vars.$body.trigger( 'mpc.font-loaded', [ $grid_products, $pagination ] );
				_mpc_vars.$body.trigger( 'mpc.pagination-loaded', [ $pagination ] );
				_mpc_vars.$body.trigger( 'mpc.inited', [ $grid_products, $pagination ] );

				setTimeout( function() {
					delay_init( $grid_products );
				}, 500 );

				window.InlineShortcodeView_mpc_wc_grid_products.__super__.rendered.call( this );
			},
			beforeUpdate: function() {
				this.$el.find( '.mpc-wc-grid-products' ).isotope( 'destroy' );

				window.InlineShortcodeView_mpc_wc_grid_products.__super__.beforeUpdate.call( this );
			}
		} );
	}

	var $grids_products = $( '.mpc-wc-grid-products' );

	$grids_products.each( function() {
		var $grid_product = $( this );

		$grid_product.one( 'mpc.init', function () {
			delay_init( $grid_product );
		} );
	});

	/* Fix Google Fonts resize */
	_mpc_vars.$window.load( function() {
		if( $grids_products.data( 'isotope' ) ) {
			setTimeout( function() {
				$grids_products.isotope( 'layout' );
			}, 250 );
		}
	});
} )( jQuery );

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