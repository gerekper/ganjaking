( function ( theme, $ ) {
	'use strict';

	theme = theme || {};

	$.extend( theme, {
		mfpConfig: {
			tClose: js_porto_vars.popup_close,
			tLoading: '<div class="porto-ajax-loading"><i class="porto-loading-icon"></i></div>',
			gallery: {
				tPrev: js_porto_vars.popup_prev,
				tNext: js_porto_vars.popup_next,
				tCounter: js_porto_vars.mfp_counter
			},
			image: {
				tError: js_porto_vars.mfp_img_error
			},
			ajax: {
				tError: js_porto_vars.mfp_ajax_error
			},
			callbacks: {
				open: function () {
					$( 'body' ).addClass( 'lightbox-opened' );
					var fixed = this.st.fixedContentPos;
					if ( fixed ) {
						$( '#header.sticky-header .header-main.sticky, #header.sticky-header .main-menu-wrap, .fixed-header #header.sticky-header .header-main, .fixed-header #header.sticky-header .main-menu-wrap' ).css( theme.rtl_browser ? 'left' : 'right', theme.getScrollbarWidth() );
					}
					/* D3-Ahsan - Start */
					var that = $( this._lastFocusedEl );
					if ( ( that.closest( '.portfolios-lightbox' ).hasClass( 'with-thumbs' ) ) && $( document ).width() >= 1024 ) {

						var portfolio_lightbox_thumbnails_base = that.closest( '.portfolios-lightbox.with-thumbs' ).find( '.porto-portfolios-lighbox-thumbnails' ).clone(),
							magnificPopup = $.magnificPopup.instance;

						$( 'body' ).prepend( portfolio_lightbox_thumbnails_base );

						var $portfolios_lightbox_thumbnails = $( 'body > .porto-portfolios-lighbox-thumbnails' ),
							$portfolios_lightbox_thumbnails_carousel = $portfolios_lightbox_thumbnails.children( '.owl-carousel' );
						$portfolios_lightbox_thumbnails_carousel.themeCarousel( $portfolios_lightbox_thumbnails_carousel.data( 'plugin-options' ) );
						$portfolios_lightbox_thumbnails_carousel.trigger( 'refresh.owl.carousel' );

						var $carousel_items_wrapper = $portfolios_lightbox_thumbnails_carousel.find( '.owl-stage' );

						$carousel_items_wrapper.find( '.owl-item' ).removeClass( 'current' );
						$carousel_items_wrapper.find( '.owl-item' ).eq( magnificPopup.currItem.index ).addClass( 'current' );

						$.magnificPopup.instance.next = function () {
							var magnificPopup = $.magnificPopup.instance;
							$.magnificPopup.proto.next.call( this );
							$carousel_items_wrapper.find( '.owl-item' ).removeClass( 'current' );
							$carousel_items_wrapper.find( '.owl-item' ).eq( magnificPopup.currItem.index ).addClass( 'current' );
						};

						$.magnificPopup.instance.prev = function () {
							var magnificPopup = $.magnificPopup.instance;
							$.magnificPopup.proto.prev.call( this );
							$carousel_items_wrapper.find( '.owl-item' ).removeClass( 'current' );
							$carousel_items_wrapper.find( '.owl-item' ).eq( magnificPopup.currItem.index ).addClass( 'current' );
						};

						$carousel_items_wrapper.find( '.owl-item' ).on( 'click', function () {
							$carousel_items_wrapper.find( '.owl-item' ).removeClass( 'current' );
							$.magnificPopup.instance.goTo( $( this ).index() );
							$( this ).addClass( 'current' );
						} );

					}
					/* End - D3-Ahsan */
				},
				close: function () {
					$( 'body' ).removeClass( 'lightbox-opened' );
					var fixed = this.st.fixedContentPos;
					if ( fixed ) {
						$( '#header.sticky-header .header-main.sticky, #header.sticky-header .main-menu-wrap, .fixed-header #header.sticky-header .header-main, .fixed-header #header.sticky-header .main-menu-wrap' ).css( theme.rtl_browser ? 'left' : 'right', '' );
					}
					$( '.owl-carousel .owl-stage' ).each( function () {
						var $this = $( this ),
							w = $this.width() + parseInt( $this.css( 'padding-left' ) ) + parseInt( $this.css( 'padding-right' ) );

						$this.css( { 'width': w + 200 } );
						setTimeout( function () {
							$this.css( { 'width': w } );
						}, 0 );
					} );
					/* D3-Ahsan - Start */
					var that = $( this._lastFocusedEl );
					if ( ( that.parents( '.portfolios-lightbox' ).hasClass( 'with-thumbs' ) ) && $( document ).width() >= 1024 ) {
						$( ' body > .porto-portfolios-lighbox-thumbnails' ).remove();
					}
					/* End - D3-Ahsan */
				}
			}
		},
	} );

} ).apply( this, [ window.theme, jQuery ] );


// Animate
( function ( theme, $ ) {
	'use strict';

	theme = theme || {};

	var instanceName = '__animate';

	var Animate = function ( $el, opts ) {
		return this.initialize( $el, opts );
	};

	Animate.defaults = {
		accX: 0,
		accY: -120,
		delay: 1,
		duration: 1000
	};

	Animate.prototype = {
		initialize: function ( $el, opts ) {
			if ( $el.data( instanceName ) ) {
				return this;
			}

			this.$el = $el;

			this
				.setData()
				.setOptions( opts )
				.build();

			return this;
		},

		setData: function () {
			this.$el.data( instanceName, true );

			return this;
		},

		setOptions: function ( opts ) {
			this.options = $.extend( true, {}, Animate.defaults, opts, {
				wrapper: this.$el
			} );

			return this;
		},

		build: function () {
			var self = this,
				$el = this.options.wrapper,
				delay = 0,
				duration = 0;

			if ($el.data('appear-animation-svg')) {
				$el.find('[data-appear-animation]').each(function(){
					var $this = $(this),
						opts;

					var pluginOptions = theme.getOptions($this.data('plugin-options'));
					if (pluginOptions)
						opts = pluginOptions;

					$this.themeAnimate(opts);
				});

				return this;
			}

			$el.addClass( 'appear-animation' );

			var el_obj = $el.get( 0 );

			delay = Math.abs( $el.data( 'appear-animation-delay' ) ? $el.data( 'appear-animation-delay' ) : self.options.delay );
			if ( delay > 1 ) {
				el_obj.style.animationDelay = delay + 'ms';
			}

			duration = Math.abs( $el.data( 'appear-animation-duration' ) ? $el.data( 'appear-animation-duration' ) : self.options.duration );
			if ( duration != 1000 ) {
				el_obj.style.animationDuration = duration + 'ms';
			}

			/*if ( $el.find( '.porto-lazyload:not(.lazy-load-loaded)' ).length ) {
				$el.find( '.porto-lazyload:not(.lazy-load-loaded)' ).trigger( 'appear' );
			}*/
			$el.addClass( $el.data( 'appear-animation' ) + ' appear-animation-visible' );

			return this;
		}
	};

	// expose to scope
	$.extend( theme, {
		Animate: Animate
	} );

	// jquery plugin
	$.fn.themeAnimate = function ( opts ) {
		return this.map( function () {
			var $this = $( this );

			if ( $this.data( instanceName ) ) {
				return $this;
			} else {
				return new theme.Animate( $this, opts );
			}

		} );
	};

} ).apply( this, [ window.theme, jQuery ] );


// Animated Letters
(function(theme, $) {

	theme = theme || {};

	var instanceName = '__animatedLetters';

	var PluginAnimatedLetters = function($el, opts) {
		return this.initialize($el, opts);
	};

	PluginAnimatedLetters.defaults = {
		animationName: 'typeWriter',
		animationSpeed: 50,
		startDelay: 500,
		minWindowWidth: 768,
		letterClass: ''
	};

	PluginAnimatedLetters.prototype = {
		initialize: function($el, opts) {
			if ($el.data(instanceName)) {
				return this;
			}

			var self = this;

			this.$el = $el;
			this.initialText = $el.text();
			this.timeoutId = null;
			this
				.setData()
				.setOptions(opts)
				.build()
				.events();

			return this;
		},

		setData: function() {
			this.$el.data(instanceName, this);

			return this;
		},

		setOptions: function(opts) {
			this.options = $.extend(true, {}, PluginAnimatedLetters.defaults, opts, {
				wrapper: this.$el
			});

			return this;
		},

		build: function() {
			var self    = this,
				letters = self.$el.text().split('');

			if( $(window).width() < self.options.minWindowWidth ) {
				self.$el.addClass('initialized');
				return this;
			}

			if( self.options.firstLoadNoAnim ) {
				self.$el.css({
					visibility: 'visible'
				});

				// Inside Carousel
				if( self.$el.closest('.owl-carousel').get(0) ) {
					setTimeout(function(){
						self.$el.closest('.owl-carousel').on('change.owl.carousel', function(){
							self.options.firstLoadNoAnim = false;
							self.build();
						});
					}, 500);
				}

				return this;
			}

			// Add class to show
			self.$el.addClass('initialized');

			// Set Min Height to avoid flicking issues
			self.setMinHeight();

			self.$el.text('');

			if( self.options.animationName == 'typeWriter' ) {
				self.$el.append( '<span class="letters-wrapper"></span><span class="typeWriter"></pre>' );

				var index = 0;
				var timeout = function(){
					var st = setTimeout(function(){
						var letter = letters[index];
						
						self.$el.find('.letters-wrapper').append( '<span class="letter '+ ( self.options.letterClass ? self.options.letterClass + ' ' : '' ) +'">' + letter + '</span>' );

						index++;
						timeout();
					}, self.options.animationSpeed);

					if( index >= letters.length ) {
						clearTimeout(st);
					}
				};
				timeout();
			} else {
				this.timeoutId = setTimeout(function(){
					for( var i = 0; i < letters.length; i++ ) {
						var letter = letters[i];
						
						self.$el.append( '<span class="letter '+ ( self.options.letterClass ? self.options.letterClass + ' ' : '' ) + self.options.animationName +' animated" style="animation-delay: '+ ( i * self.options.animationSpeed ) +'ms;">' + letter + '</span>' );
	
					}
				}, self.options.startDelay);
			}

			return this;
		},

		setMinHeight: function() {
			var self = this;

			// if it's inside carousel
			if( self.$el.closest('.owl-carousel').get(0) ) {
				self.$el.closest('.owl-carousel').addClass('d-block');
				self.$el.css( 'min-height', self.$el.height() );
				self.$el.closest('.owl-carousel').removeClass('d-block');
			} else {
				self.$el.css( 'min-height', self.$el.height() );
			}

			return this;
		},

		destroy: function() {
			var self = this;

			self.$el
				.html( self.initialText )
				.css( 'min-height', '' );
			if( this.timeoutId ) {
				clearTimeout( this.timeoutId );
				this.timeoutId = null;
			}
			return this;
		},

		events: function() {
			var self = this;

			// Destroy
			self.$el.on('animated.letters.destroy', function(){
				self.destroy();
			});

			// Initialize
			self.$el.on('animated.letters.initialize', function(){
				self.build();
			});

			return this;
		}
	};

	// expose to scope
	$.extend(theme, {
		PluginAnimatedLetters: PluginAnimatedLetters
	});

	// jquery plugin
	$.fn.themePluginAnimatedLetters = function(opts) {
		return this.map(function() {
			var $this = $(this);

			if ($this.data(instanceName)) {
				return $this.data(instanceName);
			} else {
				return new PluginAnimatedLetters($this, opts);
			}

		});
	}

}).apply(this, [window.theme, jQuery]);


// Carousel
( function ( theme, $ ) {
	'use strict';

	theme = theme || {};

	var instanceName = '__carousel';

	var Carousel = function ( $el, opts ) {
		return this.initialize( $el, opts );
	};

	Carousel.defaults = $.extend( {}, {
		loop: true,
		navText: [],
		themeConfig: false,
		lazyLoad: true,
		lg: 0,
		md: 0,
		sm: 0,
		xs: 0,
		single: false,
		rtl: theme.rtl
	} );

	Carousel.prototype = {
		initialize: function ( $el, opts ) {
			if ( $el.data( instanceName ) ) {
				return this;
			}

			this.$el = $el;

			this
				.setData()
				.setOptions( opts )
				.build();

			return this;
		},

		setData: function () {
			this.$el.data( instanceName, true );

			return this;
		},

		setOptions: function ( opts ) {
			if ( ( opts && opts.themeConfig ) || !opts ) {
				this.options = $.extend( true, {}, Carousel.defaults, theme.owlConfig, opts, {
					wrapper: this.$el,
					themeConfig: true
				} );
			} else {
				this.options = $.extend( true, {}, Carousel.defaults, opts, {
					wrapper: this.$el
				} );
			}

			return this;
		},

		calcOwlHeight: function ( $el ) {
			var h = 0;
			$el.find( '.owl-item.active' ).each( function () {
				if ( h < $( this ).height() )
					h = $( this ).height();
			} );
			$el.children( '.owl-stage-outer' ).height( h );
		},

		build: function () {
			if ( ! $.fn.owlCarousel ) {
				return this;
			}

			var $el = this.options.wrapper,
				loop = this.options.loop,
				lg = this.options.lg,
				md = this.options.md,
				sm = this.options.sm,
				xs = this.options.xs,
				single = this.options.single,
				zoom = $el.find( '.zoom' ).get( 0 ),
				responsive = {},
				items,
				count = $el.find( '.owl-item' ).length > 0 ? $el.find( '.owl-item:not(.cloned)' ).length : $el.find( '> *' ).length,
				fullscreen = typeof this.options.fullscreen == 'undefined' ? false : this.options.fullscreen,
				// Add default responsive options
				scrollWidth = theme.getScrollbarWidth();


			/*if (fullscreen) {
				$el.children().width(window.innerWidth - theme.getScrollbarWidth());
				$el.children().height($el.closest('.fullscreen-carousel').length ? $el.closest('.fullscreen-carousel').height() : window.innerHeight);
				$el.children().css('max-height', '100%');
				$(window).on('resize', function() {
					$el.find('.owl-item').children().width(window.innerWidth - theme.getScrollbarWidth());
					$el.find('.owl-item').children().height($el.closest('.fullscreen-carousel').length ? $el.closest('.fullscreen-carousel').height() : window.innerHeight);
					$el.find('.owl-item').children().css('max-height', '100%');
				});
			}*/

			if ( single ) {
				items = 1;
			} else if ( typeof this.options.responsive != 'undefined' ) {
				for ( var w in this.options.responsive ) {
					var number_items = Number( this.options.responsive[ w ] );
					responsive[ Number( w ) ] = { items: number_items, loop: ( loop && count >= number_items ) ? true : false };
				}
			} else {
				items = this.options.items ? this.options.items : ( lg ? lg : 1 );
				var isResponsive = ( this.options.xl || lg || md || sm || xs );
				if ( isResponsive ) {
					if ( this.options.xl ) {
						responsive[ 1400 - scrollWidth ] = { items: this.options.xl, loop: ( loop && count > this.options.xl ) ? true : false, mergeFit: this.options.mergeFit };
					} else {
						if ( lg && items > lg + 1 ) {
							responsive[ 1400 - scrollWidth ] = { items: items, loop: ( loop && count > items ) ? true : false, mergeFit: this.options.mergeFit };
							responsive[ theme.screen_lg - scrollWidth ] = { items: lg + 1, loop: ( loop && count > lg + 1 ) ? true : false, mergeFit: this.options.mergeFit };
						}
					}
					if ( typeof responsive[ theme.screen_lg - scrollWidth ] == 'undefined' ) {
						responsive[ theme.screen_lg - scrollWidth ] = { items: items, loop: ( loop && count >= items ) ? true : false, mergeFit: this.options.mergeFit };
					}
					if ( lg ) responsive[ 992 - scrollWidth ] = { items: lg, loop: ( loop && count >= lg ) ? true : false, mergeFit: this.options.mergeFit_lg };
					if ( md ) responsive[ 768 - scrollWidth ] = { items: md, loop: ( loop && count > md ) ? true : false, mergeFit: this.options.mergeFit_md };
					if ( sm ) {
						responsive[ 576 - scrollWidth ] = { items: sm, loop: ( loop && count > sm ) ? true : false, mergeFit: this.options.mergeFit_sm };
					} else {
						responsive[ 576 - scrollWidth ] = { items: 1, mergeFit: false };
					}
					if ( xs ) {
						responsive[ 0 ] = { items: xs, loop: ( loop && count > xs ) ? true : false, mergeFit: this.options.mergeFit_xs };
					} else {
						responsive[ 0 ] = { items: 1 };
					}
				}
			}

			if ( !$el.hasClass( 'show-nav-title' ) && this.options.themeConfig && theme.slider_nav && theme.slider_nav_hover ) {
				$el.addClass( 'show-nav-hover' );
			}

			this.options = $.extend( true, {}, this.options, {
				items: items,
				loop: ( loop && count > items ) ? true : false,
				responsive: responsive,
				onInitialized: function () {
					if ( $el.hasClass( 'stage-margin' ) ) {
						$el.find( '.owl-stage-outer' ).css( {
							'margin-left': this.options.stagePadding,
							'margin-right': this.options.stagePadding
						} );
					}
					var heading_cls = '.porto-u-heading, .vc_custom_heading, .slider-title, .elementor-widget-heading, .porto-heading';
					if ( $el.hasClass( 'show-dots-title' ) && ( $el.prev( heading_cls ).length || $el.closest( '.slider-wrapper' ).prev( heading_cls ).length || $el.closest( '.porto-recent-posts' ).prev( heading_cls ).length || $el.closest( '.elementor-widget-porto_recent_posts, .elementor-section' ).prev( heading_cls ).length ) ) {
						var $obj = $el.prev( heading_cls );
						if ( !$obj.length ) {
							$obj = $el.closest( '.slider-wrapper' ).prev( heading_cls );
						}
						if ( !$obj.length ) {
							$obj = $el.closest( '.porto-recent-posts' ).prev( heading_cls );
						}
						if ( !$obj.length ) {
							$obj = $el.closest( '.elementor-widget-porto_recent_posts, .elementor-section' ).prev( heading_cls );
						}
						try {
							var innerWidth = $obj.addClass( 'w-auto' ).css( 'display', 'inline-block' ).width();
							$obj.removeClass( 'w-auto' ).css( 'display', '' );
							if ( innerWidth + 15 + $el.find( '.owl-dots' ).width() <= $obj.width() ) {
								$el.find( '.owl-dots' ).css( 'left', innerWidth + 15 + ( $el.width() - $obj.width() ) / 2 );
								$el.find( '.owl-dots' ).css( 'top', -1 * $obj.height() / 2 - parseInt( $obj.css( 'margin-bottom' ) ) - $el.find( '.owl-dots' ).height() / 2 + 2 );
							} else {
								$el.find( '.owl-dots' ).css( 'position', 'static' );
							}
						} catch ( e ) { }
					}
				},
				touchDrag: ( count == 1 ) ? false : true,
				mouseDrag: ( count == 1 ) ? false : true
			} );

			// Auto Height Fixes
			if ( this.options.autoHeight ) {
				var thisobj = this;
				$( window ).on( 'resize', function () {
					thisobj.calcOwlHeight( $el );
				} );

				if (theme.isLoaded) {
					setTimeout(function() {
						thisobj.calcOwlHeight( $el );
					}, 100);
				} else {
					$( window ).on( 'load', function () {
						thisobj.calcOwlHeight( $el );
					} );
				}
			}

			var links = false;
			if ( zoom ) {
				links = [];
				var i = 0;

				$el.find( '.zoom' ).each( function () {
					var slide = {},
						$zoom = $( this );

					slide.src = $zoom.data( 'src' );
					slide.title = $zoom.data( 'title' );
					links[ i ] = slide;
					$zoom.data( 'index', i );
					i++;
				} );
			}

			if ( $el.hasClass( 'show-nav-title' ) ) {
				this.options.stagePadding = 0;
			} else {
				if ( this.options.themeConfig && theme.slider_nav && theme.slider_nav_hover )
					$el.addClass( 'show-nav-hover' );
				if ( this.options.themeConfig && !theme.slider_nav_hover && theme.slider_margin )
					$el.addClass( 'stage-margin' );
			}
			$el.owlCarousel( this.options );

			if ( zoom && links ) {
				$el.on( 'click', '.zoom', function ( e ) {
					e.preventDefault();
					if ( $.fn.magnificPopup ) {
						$.magnificPopup.close();
						$.magnificPopup.open( $.extend( true, {}, theme.mfpConfig, {
							items: links,
							gallery: {
								enabled: true
							},
							type: 'image'
						} ), $( this ).data( 'index' ) );
					}
					return false;
				} );
			}

			return this;
		}
	};

	// expose to scope
	$.extend( theme, {
		Carousel: Carousel
	} );

	// jquery plugin
	$.fn.themeCarousel = function ( opts, zoom ) {
		return this.map( function () {
			var $this = $( this );

			if ( $this.data( instanceName ) ) {
				return $this;
			} else {
				return new theme.Carousel( $this, opts, zoom );
			}

		} );
	};

} ).apply( this, [ window.theme, jQuery ] );

// Lightbox
( function ( theme, $ ) {
	'use strict';

	theme = theme || {};

	var instanceName = '__lightbox';

	var Lightbox = function ( $el, opts ) {
		return this.initialize( $el, opts );
	};

	Lightbox.defaults = {
		callbacks: {
			open: function () {
				$( 'body' ).addClass( 'lightbox-opened' );
			},
			close: function () {
				$( 'body' ).removeClass( 'lightbox-opened' );
			}
		}
	};

	Lightbox.prototype = {
		initialize: function ( $el, opts ) {
			if ( $el.data( instanceName ) ) {
				return this;
			}

			this.$el = $el;

			this
				.setData()
				.setOptions( opts )
				.build();

			return this;
		},

		setData: function () {
			this.$el.data( instanceName, this );

			return this;
		},

		setOptions: function ( opts ) {
			this.options = $.extend( true, {}, Lightbox.defaults, theme.mfpConfig, opts, {
				wrapper: this.$el
			} );

			return this;
		},

		build: function () {
			if ( ! $.fn.magnificPopup ) {
				return this;
			}

			this.options.wrapper.magnificPopup( this.options );

			return this;
		}
	};

	// expose to scope
	$.extend( theme, {
		Lightbox: Lightbox
	} );

	// jquery plugin
	$.fn.themeLightbox = function ( opts ) {
		return this.map( function () {
			var $this = $( this );

			if ( $this.data( instanceName ) ) {
				return $this.data( instanceName );
			} else {
				return new theme.Lightbox( $this, opts );
			}

		} );
	}

} ).apply( this, [ window.theme, jQuery ] );

// Visual Composer Image Zoom
( function ( theme, $ ) {
	'use strict';

	theme = theme || {};

	var instanceName = '__toggle';

	var VcImageZoom = function ( $el, opts ) {
		return this.initialize( $el, opts );
	};

	VcImageZoom.defaults = {

	};

	VcImageZoom.prototype = {
		initialize: function ( $el, opts ) {
			if ( $el.data( instanceName ) ) {
				return this;
			}

			this.$el = $el;

			this
				.setData()
				.setOptions( opts )
				.build();

			return this;
		},

		setData: function () {
			this.$el.data( instanceName, this );

			return this;
		},

		setOptions: function ( opts ) {
			this.options = $.extend( true, {}, VcImageZoom.defaults, opts, {
				wrapper: this.$el
			} );

			return this;
		},

		build: function () {
			var self = this,
				$el = this.options.container;
			$el.parent().magnificPopup( $.extend( true, {}, theme.mfpConfig, {
				delegate: ".porto-vc-zoom",
				gallery: {
					enabled: true
				},
				mainClass: 'mfp-with-zoom',
				zoom: {
					enabled: true,
					duration: 300
				},
				type: 'image'
			} ) );

			return this;
		}
	};

	// expose to scope
	$.extend( theme, {
		VcImageZoom: VcImageZoom
	} );

	// jquery plugin
	$.fn.themeVcImageZoom = function ( opts ) {
		return this.map( function () {
			var $this = $( this );

			if ( $this.data( instanceName ) ) {
				return $this.data( instanceName );
			} else {
				return new theme.VcImageZoom( $this, opts );
			}

		} );
	}

} ).apply( this, [ window.theme, jQuery ] );

// Post Ajax on Modal
( function ( theme, $ ) {
	'use strict';

	theme = theme || {};

	var $rev_sliders;

	$.extend( theme, {

		PostAjaxModal: {

			defaults: {
				elements: '.page-portfolios'
			},

			initialize: function ( $elements, post_type ) {
				this.$elements = ( $elements || $( this.defaults.elements ) );
				if ( typeof post_type == 'undefined' ) {
					post_type = 'portfolio';
				}

				this.build( post_type );

				return this;
			},

			build: function ( post_type ) {
				var parentobj = this,
					postAjaxOnModal = {

						$wrapper: null,
						modals: [],
						currentModal: 0,
						total: 0,
						p_type: 'portfolio',

						build: function ( $this, p_type ) {
							var self = this;
							self.$wrapper = $this;
							if ( !self.$wrapper ) {
								return;
							}
							self.modals = [];
							self.total = 0;
							self.p_type = p_type;

							$this.find( 'a[data-ajax-on-modal]' ).each( function () {
								self.add( $( this ) );
							} );

							$this.off( 'mousedown', 'a[data-ajax-on-modal]' ).on( 'mousedown', 'a[data-ajax-on-modal]', function ( ev ) {
								if ( ev.which == 2 ) {
									ev.preventDefault();
									return false;
								}
							} );
						},

						add: function ( $el ) {

							var self = this,
								href = $el.attr( 'href' ),
								index = self.total;

							self.modals.push( { src: href } );
							self.total++;

							$el.off( 'click' ).on( 'click', function ( e ) {
								e.preventDefault();
								self.show( index );
								return false;
							} );

						},

						next: function () {
							var self = this;
							if ( self.currentModal + 1 < self.total ) {
								self.show( self.currentModal + 1 );
							} else {
								self.show( 0 );
							}
						},

						prev: function () {
							var self = this;

							if ( ( self.currentModal - 1 ) >= 0 ) {
								self.show( self.currentModal - 1 );
							} else {
								self.show( self.total - 1 );
							}
						},

						show: function ( i ) {
							var self = this;

							self.currentModal = i;

							if ( i < 0 || i > ( self.total - 1 ) ) {
								return false;
							}

							$.magnificPopup.close();
							$.magnificPopup.open( $.extend( true, {}, theme.mfpConfig, {
								type: 'ajax',
								items: self.modals,
								gallery: {
									enabled: true
								},
								ajax: {
									settings: {
										type: 'post',
										data: {
											ajax_action: self.p_type + '_ajax_modal'
										}
									}
								},
								mainClass: self.p_type + '-ajax-modal',
								fixedContentPos: false,
								callbacks: {
									parseAjax: function ( mfpResponse ) {
										var $response = $( mfpResponse.data ),
											$post = $response.find( '#content article.' + self.p_type ),
											$vc_css = $response.filter( 'style[data-type]:not("")' ),
											vc_css = '';

										$vc_css.each( function () {
											vc_css += $( this ).text();
										} );

										if ( $( '#' + self.p_type + 'AjaxCSS' ).get( 0 ) ) {
											$( '#' + self.p_type + 'AjaxCSS' ).text( vc_css );
										} else {
											$( '<style id="' + self.p_type + 'AjaxCSS">' + vc_css + '</style>' ).appendTo( "head" )
										}

										$post.find( '.' + self.p_type + '-nav-all' ).html( '<a href="#" data-ajax-' + self.p_type + '-close data-bs-tooltip data-original-title="' + js_porto_vars.popup_close + '" data-bs-placement="bottom"><i class="fas fa-th"></i></a>' );
										$post.find( '.' + self.p_type + '-nav' ).html( '<a href="#" data-ajax-' + self.p_type + '-prev class="' + self.p_type + '-nav-prev" data-bs-tooltip data-original-title="' + js_porto_vars.popup_prev + '" data-bs-placement="bottom"><i class="fa"></i></a><a href="#" data-toggle="tooltip" data-ajax-' + self.p_type + '-next class="' + self.p_type + '-nav-next" data-bs-tooltip data-original-title="' + js_porto_vars.popup_next + '" data-bs-placement="bottom"><i class="fa"></i></a>' );
										$post.find( '.elementor-invisible' ).removeClass( 'elementor-invisible' );
										mfpResponse.data = '<div class="ajax-container">' + $post.html() + '</div>';
									},
									ajaxContentAdded: function () {
										// Wrapper
										var $wrapper = $( '.' + self.p_type + '-ajax-modal' );

										// Close
										$wrapper.find( 'a[data-ajax-' + self.p_type + '-close]' ).on( 'click', function ( e ) {
											e.preventDefault();
											$.magnificPopup.close();
											return false;
										} );

										$rev_sliders = $wrapper.find( '.rev_slider, rs-module' );

										// Remove Next and Close
										if ( self.modals.length <= 1 ) {
											$wrapper.find( 'a[data-ajax-' + self.p_type + '-prev], a[data-ajax-' + self.p_type + '-next]' ).remove();
										} else {
											// Prev
											$wrapper.find( 'a[data-ajax-' + self.p_type + '-prev]' ).on( 'click', function ( e ) {
												e.preventDefault();
												if ( $rev_sliders && $rev_sliders.get( 0 ) ) {
													try { $rev_sliders.revkill(); } catch ( err ) { }
												}
												$wrapper.find( '.mfp-arrow-left' ).trigger( 'click' );
												return false;
											} );
											// Next
											$wrapper.find( 'a[data-ajax-' + self.p_type + '-next]' ).on( 'click', function ( e ) {
												e.preventDefault();
												if ( $rev_sliders && $rev_sliders.get( 0 ) ) {
													try { $rev_sliders.revkill(); } catch ( err ) { }
												}
												$wrapper.find( '.mfp-arrow-right' ).trigger( 'click' );
												return false;
											} );
										}
										if ( 'portfolio' == self.p_type ) {
											$( window ).trigger( 'resize' );
										}
										porto_init();
										theme.refreshVCContent( $wrapper );
										setTimeout( function () {
											var videos = $wrapper.find( 'video' );
											if ( videos.get( 0 ) ) {
												videos.each( function () {
													$( this )[ 0 ].play();
													$( this ).parent().parent().parent().find( '.video-controls' ).attr( 'data-action', 'play' );
													$( this ).parent().parent().parent().find( '.video-controls' ).html( '<i class="ult-vid-cntrlpause"></i>' );
												} );
											}
										}, 600 );
										$wrapper.off( 'scroll' ).on( 'scroll', function () {
											$.fn.appear.run();
										} );
									},
									change: function () {
										$( '.mfp-wrap .ajax-container' ).trigger('click');
									},
									beforeClose: function () {
										if ( $rev_sliders && $rev_sliders.get( 0 ) ) {
											try { $rev_sliders.revkill(); } catch ( err ) { }
										}
										// Wrapper
										var $wrapper = $( '.' + self.p_type + '-ajax-modal' );
										$wrapper.off( 'scroll' );
									}
								}
							} ), i );
						}
					};

				parentobj.$elements.each( function () {

					var $this = $( this );

					if ( !$this.find( 'a[data-ajax-on-modal]' ).get( 0 ) )
						return;

					postAjaxOnModal.build( $this, post_type );

					$this.data( post_type + 'AjaxOnModal', postAjaxOnModal );
				} );

				return parentobj;
			}
		}

	} );

	// Key Press
	$( document.documentElement ).on( 'keydown', function ( e ) {
		try {
			if ( e.keyCode == 37 || e.keyCode == 39 ) {
				if ( $rev_sliders && $rev_sliders.get( 0 ) ) {
					$rev_sliders.revkill();
				}
			}
		} catch ( err ) { }
	} );

} ).apply( this, [ window.theme, jQuery ] );

// Portfolio Ajax on Page
( function ( theme, $ ) {
	'use strict';

	theme = theme || {};

	var activePortfolioAjaxOnPage;

	$.extend( theme, {

		PortfolioAjaxPage: {

			defaults: {
				elements: '.page-portfolios'
			},

			initialize: function ( $elements ) {
				this.$elements = ( $elements || $( this.defaults.elements ) );

				this.build();

				return this;
			},

			build: function () {
				var self = this;

				self.$elements.each( function () {

					var $this = $( this );

					if ( !$this.find( '#portfolioAjaxBox' ).get( 0 ) )
						return;

					var $container = $( this ),
						portfolioAjaxOnPage = {

							$wrapper: $container,
							pages: [],
							currentPage: 0,
							total: 0,
							$ajaxBox: $this.find( '#portfolioAjaxBox' ),
							$ajaxBoxContent: $this.find( '#portfolioAjaxBoxContent' ),

							build: function () {
								var self = this;

								self.pages = [];
								self.total = 0;

								$this.find( 'a[data-ajax-on-page]' ).each( function () {
									self.add( $( this ) );
								} );

								$this.off( 'mousedown', 'a[data-ajax-on-page]' ).on( 'mousedown', 'a[data-ajax-on-page]', function ( ev ) {
									if ( ev.which == 2 ) {
										ev.preventDefault();
										return false;
									}
								} );
							},

							add: function ( $el ) {

								var self = this,
									href = $el.attr( 'href' );

								self.pages.push( href );
								self.total++;

								$el.off( 'click' ).on( 'click', function ( e ) {
									e.preventDefault();
									/* D3-Start */
									var _class = e.target.className
									if ( _class == 'owl-next' ) {
										return false;
									} else if ( _class == 'owl-prev' ) {
										return false;
									} else {
										self.show( self.pages.indexOf( href ) );
									}
									/* End-D3 */
									return false;
								} );

							},

							events: function () {
								var self = this;

								// Close
								$this.off( 'click', 'a[data-ajax-portfolio-close]' ).on( 'click', 'a[data-ajax-portfolio-close]', function ( e ) {
									e.preventDefault();
									self.close();
									return false;
								} );

								if ( self.total <= 1 ) {
									$( 'a[data-ajax-portfolio-prev], a[data-ajax-portfolio-next]' ).remove();
								} else {
									// Prev
									$this.off( 'click', 'a[data-ajax-portfolio-prev]' ).on( 'click', 'a[data-ajax-portfolio-prev]', function ( e ) {
										e.preventDefault();
										self.prev();
										return false;
									} );
									// Next
									$this.off( 'click', 'a[data-ajax-portfolio-next]' ).on( 'click', 'a[data-ajax-portfolio-next]', function ( e ) {
										e.preventDefault();
										self.next();
										return false;
									} );
								}
							},

							close: function () {
								var self = this;

								if ( self.$ajaxBoxContent.find( '.rev_slider, rs-module' ).get( 0 ) ) {
									try { self.$ajaxBoxContent.find( '.rev_slider, rs-module' ).revkill(); } catch ( err ) { }
								}
								self.$ajaxBoxContent.empty();
								self.$ajaxBox.removeClass( 'ajax-box-init' ).removeClass( 'ajax-box-loading' );
							},

							next: function () {
								var self = this;
								if ( self.currentPage + 1 < self.total ) {
									self.show( self.currentPage + 1 );
								} else {
									self.show( 0 );
								}
							},

							prev: function () {
								var self = this;

								if ( ( self.currentPage - 1 ) >= 0 ) {
									self.show( self.currentPage - 1 );
								} else {
									self.show( self.total - 1 );
								}
							},

							show: function ( i ) {
								var self = this;

								activePortfolioAjaxOnPage = null;

								if ( self.$ajaxBoxContent.find( '.rev_slider, rs-module' ).get( 0 ) ) {
									try { self.$ajaxBoxContent.find( '.rev_slider, rs-module' ).revkill(); } catch ( err ) { }
								}
								self.$ajaxBoxContent.empty();
								self.$ajaxBox.removeClass( 'ajax-box-init' ).addClass( 'ajax-box-loading' );

								theme.scrolltoContainer( self.$ajaxBox );

								self.currentPage = i;

								if ( i < 0 || i > ( self.total - 1 ) ) {
									self.close();
									return false;
								}

								// Ajax
								$.ajax( {
									url: self.pages[ i ],
									complete: function ( data ) {
										var $response = $( data.responseText ),
											$portfolio = $response.find( '#content article.portfolio' ),
											$vc_css = $response.filter( 'style[data-type]:not("")' ),
											vc_css = '';

										if ( $( '#portfolioAjaxCSS' ).get( 0 ) ) {
											$( '#portfolioAjaxCSS' ).text( vc_css );
										} else {
											$( '<style id="portfolioAjaxCSS">' + vc_css + '</style>' ).appendTo( "head" )
										}

										$portfolio.find( '.portfolio-nav-all' ).html( '<a href="#" data-ajax-portfolio-close data-bs-tooltip data-original-title="' + js_porto_vars.popup_close + '"><i class="fas fa-th"></i></a>' );
										$portfolio.find( '.portfolio-nav' ).html( '<a href="#" data-ajax-portfolio-prev class="portfolio-nav-prev" data-bs-tooltip data-original-title="' + js_porto_vars.popup_prev + '"><i class="fa"></i></a><a href="#" data-toggle="tooltip" data-ajax-portfolio-next class="portfolio-nav-next" data-bs-tooltip data-original-title="' + js_porto_vars.popup_next + '"><i class="fa"></i></a>' );
										self.$ajaxBoxContent.html( $portfolio.html() ).append( '<div class="row"><div class="col-lg-12"><hr class="tall"></div></div>' );
										self.$ajaxBox.removeClass( 'ajax-box-loading' );
										$( window ).trigger( 'resize' );
										porto_init();
										theme.refreshVCContent( self.$ajaxBoxContent );
										self.events();
										activePortfolioAjaxOnPage = self;

										self.$ajaxBoxContent.find( '.lightbox:not(.manual)' ).each( function () {
											var $this = $( this ),
												opts;

											var pluginOptions = $this.data( 'plugin-options' );
											if ( pluginOptions )
												opts = pluginOptions;

											$this.themeLightbox( opts );
										} );
									}
								} );
							}
						};

					portfolioAjaxOnPage.build();

					$this.data( 'portfolioAjaxOnPage', portfolioAjaxOnPage );
				} );

				return self;
			}
		}

	} );

	// Key Press
	$( document.documentElement ).on( 'keyup', function ( e ) {
		try {
			if ( !activePortfolioAjaxOnPage ) return;
			// Next
			if ( e.keyCode == 39 ) {
				activePortfolioAjaxOnPage.next();
			}
			// Prev
			if ( e.keyCode == 37 ) {
				activePortfolioAjaxOnPage.prev();
			}
		} catch ( err ) { }
	} );

} ).apply( this, [ window.theme, jQuery ] );

// Post Filter
( function ( theme, $ ) {
	'use strict';

	theme = theme || {};

	$.extend( theme, {

		PostFilter: {

			defaults: {
				elements: '.portfolio-filter'
			},

			initialize: function ( $elements, post_type ) {
				this.$elements = ( $elements || $( this.defaults.elements ) );
				this.post_type = ( typeof post_type == 'undefined' ? 'portfolio' : post_type );

				this.build();

				return this;
			},

			build: function () {
				var self = this;

				self.$elements.each( function () {
					var $this = $( this );
					$this.find( 'li' ).on( 'click', function ( e ) {
						e.preventDefault();
						if ( $( this ).hasClass( 'active' ) ) {
							return;
						}

						var selector = $( this ).attr( 'data-filter' ),
							position = $this.data( 'position' ),
							$parent;

						$this.find( '.active' ).removeClass( 'active' );

						if ( position == 'sidebar' ) {
							$parent = $( '.main-content .page-' + self.post_type + 's' );
							//theme.scrolltoContainer($parent);
							$( '.sidebar-overlay' ).trigger('click');
						} else if ( position == 'global' ) {
							$parent = $( '.main-content .page-' + self.post_type + 's' );
						} else {
							$parent = $( this ).closest( '.page-' + self.post_type + 's' );
						}

						if ( 'faq' == self.post_type ) {
							$parent.find( '.faq' ).each( function () {
								var $that = $( this ), easing = "easeInOutQuart", timeout = 300;
								if ( selector == '*' ) {
									if ( $that.css( 'display' ) == 'none' ) $that.stop( true ).slideDown( timeout, easing, function () {
										$( this ).attr( 'style', '' ).show();
									} );
									selected++;
								} else {
									if ( $that.hasClass( selector ) ) {
										if ( $that.css( 'display' ) == 'none' ) $that.stop( true ).slideDown( timeout, easing, function () {
											$( this ).attr( 'style', '' ).show();
										} );
										selected++;
									} else {
										if ( $that.css( 'display' ) != 'none' ) $that.stop( true ).slideUp( timeout, easing, function () {
											$( this ).attr( 'style', '' ).hide();
										} );
									}
								}
							} );

							if ( !selected && $parent.find( '.faqs-infinite' ).length && typeof ( $.fn.infinitescroll ) != 'undefined' ) {
								$parent.find( '.faqs-infinite' ).infinitescroll( 'retrieve' );
							}
						} else if ( $parent.hasClass( 'portfolios-timeline' ) ) {
							var selected = 0;
							$parent.find( '.portfolio' ).each( function () {
								var $that = $( this ), easing = "easeInOutQuart", timeout = 300;
								if ( selector == '*' ) {
									if ( $that.css( 'display' ) == 'none' ) $that.stop( true ).slideDown( timeout, easing, function () {
										$( this ).attr( 'style', '' ).show();
									} );
									selected++;
								} else {
									if ( $that.hasClass( selector ) ) {
										if ( $that.css( 'display' ) == 'none' ) $that.stop( true ).slideDown( timeout, easing, function () {
											$( this ).attr( 'style', '' ).show();
										} );
										selected++;
									} else {
										if ( $that.css( 'display' ) != 'none' ) $that.stop( true ).slideUp( timeout, easing, function () {
											$( this ).attr( 'style', '' ).hide();
										} );
									}
								}
							} );
							if ( !selected && $parent.find( '.portfolios-infinite' ).length && typeof ( $.fn.infinitescroll ) != 'undefined' ) {
								$parent.find( '.portfolios-infinite' ).infinitescroll( 'retrieve' );
							}
							setTimeout( function () {
								theme.FilterZoom.initialize( $parent );
							}, 400 );
						} else {
							$parent.find( '.' + self.post_type + '-row' ).isotope( {
								filter: selector == '*' ? selector : '.' + selector
							} );
						}

						$( this ).addClass( 'active' );

						if ( position == 'sidebar' ) {
							self.$elements.each( function () {
								var $that = $( this );

								if ( $that == $this && $that.data( 'position' ) != 'sidebar' ) return;
								$that.find( 'li' ).removeClass( 'active' );
								$that.find( 'li[data-filter="' + selector + '"]' ).addClass( 'active' );
							} );
						}

						window.location.hash = '#' + selector;
						theme.refreshVCContent();

					} );
				} );

				function hashchange() {
					var $filter = $( self.$elements.get( 0 ) ), hash = window.location.hash;

					if ( hash ) {
						var $o = $filter.find( 'li[data-filter="' + hash.replace( '#', '' ) + '"]' );
						if ( !$o.hasClass( 'active' ) ) {
							$o.trigger('click');
						}
					}
				}

				$( window ).on( 'hashchange', hashchange );
				hashchange();

				return self;
			}
		}

	} );

} ).apply( this, [ window.theme, jQuery ] );

// Member Ajax on Page
( function ( theme, $ ) {
	'use strict';

	theme = theme || {};

	var activeMemberAjaxOnPage;

	$.extend( theme, {

		MemberAjaxPage: {

			defaults: {
				elements: '.page-members'
			},

			initialize: function ( $elements ) {
				this.$elements = ( $elements || $( this.defaults.elements ) );

				this.build();

				return this;
			},

			build: function () {
				var self = this;

				self.$elements.each( function () {

					var $this = $( this );

					if ( !$this.find( '#memberAjaxBox' ).get( 0 ) )
						return;

					var $container = $( this ),
						memberAjaxOnPage = {

							$wrapper: $container,
							pages: [],
							currentPage: 0,
							total: 0,
							$ajaxBox: $this.find( '#memberAjaxBox' ),
							$ajaxBoxContent: $this.find( '#memberAjaxBoxContent' ),

							build: function () {
								var self = this;

								self.pages = [];
								self.total = 0;

								$this.find( 'a[data-ajax-on-page]' ).each( function () {
									self.add( $( this ) );
								} );

								$this.off( 'mousedown', 'a[data-ajax-on-page]' ).on( 'mousedown', 'a[data-ajax-on-page]', function ( ev ) {
									if ( ev.which == 2 ) {
										ev.preventDefault();
										return false;
									}
								} );
							},

							add: function ( $el ) {

								var self = this,
									href = $el.attr( 'href' );

								self.pages.push( href );
								self.total++;

								$el.off( 'click' ).on( 'click', function ( e ) {
									e.preventDefault();
									self.show( self.pages.indexOf( href ) );
									return false;
								} );

							},

							next: function () {
								var self = this;
								if ( self.currentPage + 1 < self.total ) {
									self.show( self.currentPage + 1 );
								} else {
									self.show( 0 );
								}
							},

							prev: function () {
								var self = this;

								if ( ( self.currentPage - 1 ) >= 0 ) {
									self.show( self.currentPage - 1 );
								} else {
									self.show( self.total - 1 );
								}
							},

							show: function ( i ) {
								var self = this;

								activeMemberAjaxOnPage = null;

								if ( self.$ajaxBoxContent.find( '.rev_slider, rs-module' ).get( 0 ) ) {
									try { self.$ajaxBoxContent.find( '.rev_slider, rs-module' ).revkill(); } catch ( err ) { }
								}
								self.$ajaxBoxContent.empty();
								self.$ajaxBox.removeClass( 'ajax-box-init' ).addClass( 'ajax-box-loading' );

								theme.scrolltoContainer( self.$ajaxBox );

								self.currentPage = i;

								if ( i < 0 || i > ( self.total - 1 ) ) {
									self.close();
									return false;
								}

								// Ajax
								$.ajax( {
									url: self.pages[ i ],
									complete: function ( data ) {
										var $response = $( data.responseText ),
											$member = $response.find( '#content article.member' ),
											$vc_css = $response.filter( 'style[data-type]:not("")' ),
											vc_css = '';

										$vc_css.each( function () {
											vc_css += $( this ).text();
										} );

										if ( $( '#memberAjaxCSS' ).get( 0 ) ) {
											$( '#memberAjaxCSS' ).text( vc_css );
										} else {
											$( '<style id="memberAjaxCSS">' + vc_css + '</style>' ).appendTo( "head" )
										}

										var $append = self.$ajaxBox.find( '.ajax-content-append' ), html = '';
										if ( $append.length ) html = $append.html();
										self.$ajaxBoxContent.html( $member.html() ).prepend( '<div class="row"><div class="col-lg-12"><hr class="tall m-t-none"></div></div>' ).append( '<div class="row"><div class="col-md-12"><hr class="m-t-md"></div></div>' + html );

										self.$ajaxBox.removeClass( 'ajax-box-loading' );
										$( window ).trigger( 'resize' );
										porto_init();
										theme.refreshVCContent( self.$ajaxBoxContent );
										activeMemberAjaxOnPage = self;
									}
								} );
							}
						};

					memberAjaxOnPage.build();

					$this.data( 'memberAjaxOnPage', memberAjaxOnPage );
				} );

				return self;
			}
		}

	} );

	// Key Press
	$( document.documentElement ).on( 'keyup', function ( e ) {
		try {
			if ( !activeMemberAjaxOnPage ) return;
			// Next
			if ( e.keyCode == 39 ) {
				activeMemberAjaxOnPage.next();
			}
			// Prev
			if ( e.keyCode == 37 ) {
				activeMemberAjaxOnPage.prev();
			}
		} catch ( err ) { }
	} );

} ).apply( this, [ window.theme, jQuery ] );

// Filter Zoom
( function ( theme, $ ) {
	'use strict';

	theme = theme || {};

	$.extend( theme, {

		FilterZoom: {

			defaults: {
				elements: null
			},

			initialize: function ( $elements ) {
				this.$elements = ( $elements || this.defaults.elements );

				this.build();

				return this;
			},

			build: function () {
				var self = this;

				self.$elements.each( function () {
					var $this = $( this ),
						zoom = $this.find( '.zoom, .thumb-info-zoom' ).get( 0 );

					if ( !zoom ) return;

					$this.find( '.zoom, .thumb-info-zoom' ).off( 'click' );
					var links = [];
					var i = 0;
					$this.find( 'article' ).each( function () {
						var $that = $( this );
						if ( $that.css( 'display' ) != 'none' ) {
							var $zoom = $that.find( '.zoom, .thumb-info-zoom' ),
								slide,
								src = $zoom.data( 'src' ),
								title = $zoom.data( 'title' );

							$zoom.data( 'index', i );
							if ( Array.isArray( src ) ) {
								$.each( src, function ( index, value ) {
									slide = {};
									slide.src = value;
									slide.title = title[ index ];
									links[ i ] = slide;
									i++;
								} );
							} else {
								slide = {};
								slide.src = src;
								slide.title = title;
								links[ i ] = slide;
								i++;
							}
						}
					} );
					$this.find( 'article' ).each( function () {
						var $that = $( this );
						if ( $that.css( 'display' ) != 'none' ) {
							$that.off( 'click', '.zoom, .thumb-info-zoom' ).on( 'click', '.zoom, .thumb-info-zoom', function ( e ) {
								var $zoom = $( this ), $parent = $zoom.parents( '.thumb-info' ), offset = 0;
								if ( $parent.get( 0 ) ) {
									var $slider = $parent.find( '.porto-carousel' );
									if ( $slider.get( 0 ) ) {
										offset = $slider.data( 'owl.carousel' ).current() - $slider.find( '.cloned' ).length / 2;
									}
								}
								e.preventDefault();
								if ( $.fn.magnificPopup ) {
									$.magnificPopup.close();
									$.magnificPopup.open( $.extend( true, {}, theme.mfpConfig, {
										items: links,
										gallery: {
											enabled: true
										},
										type: 'image'
									} ), $zoom.data( 'index' ) + offset );
								}
								return false;
							} );
						}
					} );
				} );

				return self;
			}
		}

	} );

} ).apply( this, [ window.theme, jQuery ] );

// Mouse Parallax
(function (theme, $) {
	'use strict';

	theme = theme || {};

	var instanceName = '__parallax';

	var Mouseparallax = function ($el, opts) {
		return this.initialize($el, opts);
	};

	Mouseparallax.prototype = {
		initialize: function ($el, opts) {
			this.$el = $el;

			this
				.setData()
				.setOptions(opts)
				.build();

			return this;
		},

		setData: function () {
			this.$el.data(instanceName, this);
			return this;
		},

		setOptions: function (opts) {
			this.options = $.extend(true, {}, {
				wrapper: this.$el,
				opts: opts
			});
			return this;
		},

		build: function () {
			if (!$.fn.parallax) {
				return this;
			}

			var $el = this.options.wrapper,
				opts = this.options.opts

			$el.parallax(opts);
		}
	};

	//expose to scope
	$.extend(theme, {
		Mouseparallax: Mouseparallax
	});

	// jquery plugin
	$.fn.themeMouseparallax = function (opts) {
		var obj = this.map(function () {
			var $this = $(this);

			if ($this.data(instanceName)) {
				return $this.data(instanceName);
			} else {
				return new theme.Mouseparallax($this, opts);
			}
		});
		return obj;
	}
}).apply(this, [window.theme, jQuery]);


// Read More
( function ( theme, $ ) {

	theme = theme || {};

	var instanceName = '__readmore';

	var PluginReadMore = function ( $el, opts ) {
		return this.initialize( $el, opts );
	};

	PluginReadMore.defaults = {
		buttonOpenLabel: 'Read More <i class="fas fa-chevron-down text-2 ms-1"></i>',
		buttonCloseLabel: 'Read Less <i class="fas fa-chevron-up text-2 ms-1"></i>',
		enableToggle: true,
		maxHeight: 300,
		overlayColor: '#43a6a3',
		overlayHeight: 100,
		startOpened: false,
		align: 'left'
	};

	PluginReadMore.prototype = {
		initialize: function ( $el, opts ) {
			var self = this;

			this.$el = $el;

			this
				.setData()
				.setOptions( opts )
				.build()
				.events()
				.resize();

			if ( self.options.startOpened ) {
				self.options.wrapper.find( '.readmore-button-wrapper > button' ).trigger( 'click' );
			}

			return this;
		},

		setData: function () {
			this.$el.data( instanceName, this );

			return this;
		},

		setOptions: function ( opts ) {
			this.options = $.extend( true, {}, PluginReadMore.defaults, opts, {
				wrapper: this.$el
			} );

			return this;
		},

		build: function () {
			var self = this;

			self.options.wrapper.addClass( 'position-relative' );

			// Overlay
			self.options.wrapper.append( '<div class="readmore-overlay"></div>' );

			// Check if is Safari
			var backgroundCssValue = 'linear-gradient(180deg, rgba(2, 0, 36, 0) 0%, ' + self.options.overlayColor + ' 100%)';
			if ( $( 'html' ).hasClass( 'safari' ) ) {
				backgroundCssValue = '-webkit-linear-gradient(top, rgba(2, 0, 36, 0) 0%, ' + self.options.overlayColor + ' 100%)'
			}

			self.options.wrapper.find( '.readmore-overlay' ).css( {
				background: backgroundCssValue,
				position: 'absolute',
				bottom: 0,
				left: 0,
				width: '100%',
				height: self.options.overlayHeight,
				'z-index': 1
			} );

			// Read More Button
			self.options.wrapper.find( '.readmore-button-wrapper' ).removeClass( 'd-none' ).css( {
				position: 'absolute',
				bottom: 0,
				left: 0,
				width: '100%',
				'z-index': 2
			} );

			// Button Label
			self.options.wrapper.find( '.readmore-button-wrapper > button' ).html( self.options.buttonOpenLabel );

			self.options.wrapper.css( {
				'height': self.options.maxHeight,
				'overflow-y': 'hidden'
			} );

			// Alignment
			switch ( self.options.align ) {
				case 'center':
					self.options.wrapper.find( '.readmore-button-wrapper' ).addClass( 'text-center' );
					break;

				case 'right':
					self.options.wrapper.find( '.readmore-button-wrapper' ).addClass( 'text-end' );
					break;

				case 'left':
				default:
					self.options.wrapper.find( '.readmore-button-wrapper' ).addClass( 'text-start' );
					break;
			}

			return this;

		},

		events: function () {
			var self = this;

			// Read More
			self.readMore = function () {
				self.options.wrapper.find( '.readmore-button-wrapper > button:not(.readless)' ).on( 'click', function ( e ) {
					e.preventDefault();
					self.options.wrapper.addClass( 'opened' );

					var $this = $( this );

					setTimeout( function () {
						self.options.wrapper.animate( {
							'height': self.options.wrapper[ 0 ].scrollHeight
						}, function () {
							if ( !self.options.enableToggle ) {
								$this.fadeOut();
							}

							$this.html( self.options.buttonCloseLabel ).addClass( 'readless' ).off( 'click' );

							self.readLess();

							self.options.wrapper.find( '.readmore-overlay' ).fadeOut();
							self.options.wrapper.css( {
								'max-height': 'none',
								'overflow': 'visible'
							} );

							self.options.wrapper.find( '.readmore-button-wrapper' ).animate( {
								bottom: -20
							} );
						} );
					}, 200 );
				} );
			}

			// Read Less
			self.readLess = function () {
				self.options.wrapper.find( '.readmore-button-wrapper > button.readless' ).on( 'click', function ( e ) {
					e.preventDefault();
					self.options.wrapper.removeClass( 'opened' );

					var $this = $( this );

					// Button
					self.options.wrapper.find( '.readmore-button-wrapper' ).animate( {
						bottom: 0
					} );

					// Overlay
					self.options.wrapper.find( '.readmore-overlay' ).fadeIn();

					setTimeout( function () {
						self.options.wrapper.height( self.options.wrapper[ 0 ].scrollHeight ).animate( {
							'height': self.options.maxHeight
						}, function () {
							$this.html( self.options.buttonOpenLabel ).removeClass( 'readless' ).off( 'click' );

							self.readMore();

							self.options.wrapper.css( {
								'overflow': 'hidden'
							} );
						} );
					}, 200 );
				} );
			}

			// First Load
			self.readMore();

			return this;
		},

		resize: function () {
			var self = this;
			window.addEventListener( 'resize', function () {
				self.options.wrapper.hasClass( 'opened' ) ? self.options.wrapper.css( { 'height': 'auto' } ) : self.options.wrapper.css( { 'height': self.options.maxHeight } );
			} )
		}
	};

	// expose to scope
	$.extend( theme, {
		PluginReadMore: PluginReadMore
	} );

	// jquery plugin
	$.fn.themePluginReadMore = function ( opts ) {
		return this.map( function () {
			var $this = $( this );

			if ( $this.data( instanceName ) ) {
				return $this.data( instanceName );
			} else {
				return new PluginReadMore( $this, $this.data( 'plugin-options' ) );
			}

		} );
	}

} ).apply( this, [ window.theme, jQuery ] );


/* initialize */
(function (theme, $) {
	theme.initAsync = function( $wrap, wrapObj ) {
		// Animate
		if ( $.fn.themeAnimate ) {

			$( function () {
				var svgAnimates = wrapObj.querySelectorAll( 'svg [data-appear-animation]' );
				if (svgAnimates.length) {
					$(svgAnimates).closest('svg').attr('data-appear-animation-svg', '1');
				}
				var $animates = wrapObj.querySelectorAll( '[data-plugin-animate], [data-appear-animation], [data-appear-animation-svg]' );
				if ( $animates.length ) {
					var animateResize = function() {
						if (window.innerWidth < 768) {
							window.removeEventListener( 'resize', animateResize );
							$animates.forEach(function(o) {
								o.classList.add('appear-animation-visible');
							});
						}
					};
					if (theme.animation_support) {
						window.addEventListener( 'resize', animateResize );
						theme.dynIntObsInit( $animates, 'themeAnimate', theme.Animate.defaults );
					} else {
						$animates.forEach(function(o) {
							o.classList.add('appear-animation-visible');
						});
					}
				}
			} );
		}

		// Animated Letters
		if ($.fn.themePluginAnimatedLetters && ( $('[data-plugin-animated-letters]').length || $('.animated-letters').length )) {
			theme.intObs( '[data-plugin-animated-letters]:not(.manual), .animated-letters', 'themePluginAnimatedLetters' );
		}

		// Carousel
		if ( $.fn.themeCarousel ) {

			$( function () {
				// Carousel Lazyload images
				var portoCarouselInit = function ( e ) {
					var $this = $( e.currentTarget );

					$this.find( '[data-appear-animation]:not(.appear-animation)' ).addClass('appear-animation');
					if ( $this.find( '.owl-item.cloned' ).length ) {
						$this.find( '.porto-lazyload:not(.lazy-load-loaded)' ).themePluginLazyLoad( { effect: 'fadeIn', effect_speed: 400 } );
						var $animates = e.currentTarget.querySelectorAll('.appear-animation');
						if ($animates.length) {
							theme.dynIntObsInit( $animates, 'themeAnimate', theme.Animate.defaults );
						}
						if ($.fn.themePluginAnimatedLetters && ($(this).find('.owl-item.cloned [data-plugin-animated-letters]:not(.manual)').length )) {
							theme.dynIntObsInit( $(this).find('.owl-item.cloned [data-plugin-animated-letters]:not(.manual)'), 'themePluginAnimatedLetters' );
						}
					}

					setTimeout( function () {
						var $hiddenItems = $this.find( '.owl-item:not(.active)' );
						if ( !$( 'html' ).hasClass( 'no-csstransitions' ) && window.innerWidth > 767 ) {
							$hiddenItems.find( '.appear-animation' ).removeClass( 'appear-animation-visible' );
							$hiddenItems.find( '.appear-animation' ).each( function () {
								var $el = $( this ),
									delay = Math.abs( $el.data( 'appear-animation-delay' ) ? $el.data( 'appear-animation-delay' ) : 0 );
								if ( delay > 1 ) {
									this.style.animationDelay = delay + 'ms';
								}

								var duration = Math.abs( $el.data( 'appear-animation-duration' ) ? $el.data( 'appear-animation-duration' ) : 1000 );
								if ( 1000 != duration ) {
									this.style.animationDuration = duration + 'ms';
								}
							} );
						}
						if ( window.innerWidth >= 1200 ) {
							$hiddenItems.find( '[data-vce-animate]' ).removeAttr( 'data-vcv-o-animated' );
						}
					}, 300 );
				};
				var portoCarouselTranslated = function ( e ) {
					var $this = $( e.currentTarget );
					/*if ( window.innerWidth > 767 ) {
						if ( $this.find( '.owl-item.cloned' ).length && $this.find( '.appear-animation:not(.appear-animation-visible)' ).length ) {
							$( document.body ).trigger( 'appear_refresh' );
						}
					}*/

					var $active = $this.find( '.owl-item.active' );
					if ( $active.hasClass( 'translating' ) ) {
						$active.removeClass( 'translating' );
						return;
					}
					$this.find( '.owl-item.translating' ).removeClass( 'translating' );
					// Animated Letters
					$this.find('[data-plugin-animated-letters]').removeClass('invisible');
					$this.find('.owl-item.active [data-plugin-animated-letters]').trigger('animated.letters.initialize');

					if ( window.innerWidth > 767 ) {
						// WPBakery
						$this.find( '.appear-animation' ).removeClass( 'appear-animation-visible' );
						$active.find( '.appear-animation' ).each( function () {
							var $animation_item = $( this ),
								anim_name = $animation_item.data( 'appear-animation' );
							$animation_item.addClass( anim_name + ' appear-animation-visible' );
						} );
					}

					// Elementor
					$active.find( '.slide-animate' ).each( function () {
						var $animation_item = $( this ),
							settings = $animation_item.data( 'settings' );
						if ( settings && ( settings._animation || settings.animation ) ) {
							var animation = settings._animation || settings.animation,
								delay = settings._animation_delay || settings.animation_delay || 0;
							theme.requestTimeout( function () {
								$animation_item.removeClass( 'elementor-invisible' ).addClass( 'animated ' + animation );
							}, delay );
						}
					} );

					// Visual Composer
					if ( window.innerWidth >= 1200 ) {
						$this.find( '[data-vce-animate]' ).removeAttr( 'data-vcv-o-animated' ).removeAttr( 'data-vcv-o-animated-fully' );
						$active.find( '[data-vce-animate]' ).each( function () {
							var $animation_item = $( this );
							if ( $animation_item.data( 'porto-origin-anim' ) ) {
								var anim_name = $animation_item.data( 'porto-origin-anim' );
								$animation_item.attr( 'data-vce-animate', anim_name ).attr( 'data-vcv-o-animated', true );
								var duration = parseFloat( window.getComputedStyle( this )[ 'animationDuration' ] ) * 1000,
									delay = parseFloat( window.getComputedStyle( this )[ 'animationDelay' ] ) * 1000;
								window.setTimeout( function () {
									$animation_item.attr( 'data-vcv-o-animated-fully', true );
								}, delay + duration + 5 );
							}
						} );
					}
				};
				var portoCarouselTranslateVC = function ( e ) {
					var $this = $( e.currentTarget );
					$this.find( '.owl-item.active' ).addClass( 'translating' );

					if ( window.innerWidth >= 1200 ) {
						$this.find( '[data-vce-animate]' ).each( function () {
							var $animation_item = $( this );
							$animation_item.data( 'porto-origin-anim', $animation_item.data( 'vce-animate' ) ).attr( 'data-vce-animate', '' );
						} );
					}
				};
				var portoCarouselTranslateElementor = function ( e ) {
					var $this = $( e.currentTarget );
					$this.find( '.owl-item.active' ).addClass( 'translating' );
					$this.find( '.owl-item:not(.active) .slide-animate' ).addClass( 'elementor-invisible' );
					$this.find( '.slide-animate' ).each( function () {
						var $animation_item = $( this ),
							settings = $animation_item.data( 'settings' );
						if ( settings._animation || settings.animation ) {
							$animation_item.removeClass( settings._animation || settings.animation );
						}
					} );
				};
				var portoCarouselTranslateWPB = function ( e ) {
					if ( window.innerWidth > 767 ) {
						var $this = $( e.currentTarget );
						$this.find( '.owl-item.active' ).addClass( 'translating' );
						$this.find( '.appear-animation' ).each( function () {
							var $animation_item = $( this );
							$animation_item.removeClass( $animation_item.data( 'appear-animation' ) );
						} );
					}
				};

				var carouselItems = $wrap.find( '.owl-carousel:not(.manual)' );
				carouselItems.on( 'initialized.owl.carousel refreshed.owl.carousel', portoCarouselInit ).on( 'translated.owl.carousel', portoCarouselTranslated );
				carouselItems.on( 'translate.owl.carousel', function() {
					// Hide elements inside carousel
					$(this).find('[data-plugin-animated-letters]').addClass('invisible');
					// Animated Letters
					$(this).find('[data-plugin-animated-letters]').trigger('animated.letters.destroy');
				});
				carouselItems.filter( function () {
					if ( $( this ).find( '[data-vce-animate]' ).length ) {
						return true;
					}
					return false;
				} ).on( 'translate.owl.carousel', portoCarouselTranslateVC );
				carouselItems.filter( function () {
					var $anim_obj = $( this ).find( '.elementor-invisible' );
					if ( $anim_obj.length ) {
						$anim_obj.addClass( 'slide-animate' );
						return true;
					}
					return false;
				} ).on( 'translate.owl.carousel', portoCarouselTranslateElementor );
				carouselItems.filter( function () {
					if ( $( this ).find( '.appear-animation' ).length ) {
						return true;
					}
					return false;
				} ).on( 'translate.owl.carousel', portoCarouselTranslateWPB );

				$wrap.find( '[data-plugin-carousel]:not(.manual), .porto-carousel:not(.manual)' ).each( function () {
					var $this = $( this ),
						opts;

					var pluginOptions = $this.data( 'plugin-options' );
					if ( pluginOptions )
						opts = pluginOptions;

					$this.themeCarousel( opts );
				} );
			} );

		}

		// Thumb Gallery
		$wrap.find( '.thumb-gallery-thumbs' ).each( function () {
			var $thumbs = $( this ),
				$detail = $thumbs.parent().find( '.thumb-gallery-detail' ),
				flag = false,
				duration = 300;

			if ( $thumbs.data( 'initThumbs' ) )
				return;

			$detail.on( 'changed.owl.carousel', function ( e ) {
				if ( !flag ) {
					flag = true;
					var len = $detail.find( '.owl-item' ).length,
						cloned = $detail.find( '.cloned' ).length;
					if ( len ) {
						$thumbs.trigger( 'to.owl.carousel', [ ( e.item.index - cloned / 2 - 1 ) % len, duration, true ] );
					}
					flag = false;
				}
			} );

			$thumbs.on( 'changed.owl.carousel', function ( e ) {
				if ( !flag ) {
					flag = true;
					var len = $thumbs.find( '.owl-item' ).length,
						cloned = $thumbs.find( '.cloned' ).length;
					if ( len ) {
						$detail.trigger( 'to.owl.carousel', [ ( e.item.index - cloned / 2 ) % len, duration, true ] );
					}
					flag = false;
				}
			} ).on( 'click', '.owl-item', function () {
				if ( !flag ) {
					flag = true;
					var len = $thumbs.find( '.owl-item' ).length,
						cloned = $thumbs.find( '.cloned' ).length;
					if ( len ) {
						$detail.trigger( 'to.owl.carousel', [ ( $( this ).index() - cloned / 2 ) % len, duration, true ] );
					}
					flag = false;
				}
			} ).data( 'initThumbs', true );
		} );

		// Fixed video
		$wrap.find( '.video-fixed' ).each( function () {
			var $this = $( this ),
				$video = $this.find( 'video, iframe' );

			if ( $video.length ) {
				window.addEventListener( 'scroll', function () {
					var offset = $( window ).scrollTop() - $this.position().top + theme.adminBarHeight();
					$video.css( "cssText", "top: " + offset + "px !important;" );
				}, { passive: true } );
			}
		} );

	};

	$( document.body ).trigger( 'porto_async_init' );
}).apply(this, [window.theme, jQuery]);

jQuery( document ).ready( function ( $ ) {
	'use strict';

	// Visual Composer Image Zoom
	if ( $.fn.themeVcImageZoom ) {

		$( function () {
			var $galleryParent = null;
			$( '.porto-vc-zoom:not(.manual)' ).each( function () {
				var $this = $( this ),
					opts,
					gallery = $this.attr( 'data-gallery' );

				var pluginOptions = $this.data( 'plugin-options' );
				if ( pluginOptions )
					opts = pluginOptions;

				if ( typeof opts == "undefined" ) {
					opts = {};
				}
				opts.container = $this.parent();

				if ( gallery == 'true' ) {
					var container = 'vc_row';

					if ( $this.attr( 'data-container' ) )
						container = $this.attr( 'data-container' );

					var $parent = $( $this.closest( '.' + container ).get( 0 ) );
					if ( $parent.length > 0 && $galleryParent != null && $galleryParent.is( $parent ) ) {
						return;
					} else if ( $parent.length > 0 ) {
						$galleryParent = $parent;
					}
					if ( $galleryParent != null && $galleryParent.length > 0 ) {
						opts.container = $galleryParent;
					}
				}

				$this.themeVcImageZoom( opts );
			} );
		} );
	}

	function porto_modal_open( $this ) {
		var trigger = $this.data( 'trigger-id' ),
			overlayClass = $this.data( 'overlay-class' ),
			extraClass = $this.data( 'extra-class' ) ? $this.data( 'extra-class' ) : '',
			type = $this.data( 'type' );
		if ( typeof trigger != 'undefined'/* && $('#' + escape(trigger)).length > 0*/ ) {
			if ( typeof type == 'undefined' ) {
				type = 'inline';
			}
			if ( type == 'inline' ) {
				trigger = '#' + escape( trigger );
			}
			var args = {
				items: {
					src: trigger
				},
				type: type,
				mainClass: extraClass
			};
			if ( $this.hasClass( 'porto-onload' ) ) {
				args[ 'callbacks' ] = {
					'beforeClose': function () {
						if ( $( '.mfp-wrap .porto-modal-content .porto-disable-modal-onload' ).length && ($( '.mfp-wrap .porto-modal-content .porto-disable-modal-onload' ).is( ':checked' ) || $( '.mfp-wrap .porto-modal-content .porto-disable-modal-onload input[type="checkbox"]' ).is( ':checked' ))) {
							$.cookie( 'porto_modal_disable_onload', 'true', { expires: 7 } );
						}
					}
				};
			}
			if ( typeof overlayClass != "undefined" && overlayClass ) {
				args.mainClass += escape( overlayClass );
			}
			$.magnificPopup.open( $.extend( true, {}, theme.mfpConfig, args ), 0 );
		}
	}

	function porto_init_magnific_popup_functions() {
		$( '.lightbox:not(.manual)' ).each( function () {
			var $this = $( this ),
				opts;

			var pluginOptions = $this.data( 'plugin-options' );
			if ( pluginOptions )
				opts = pluginOptions;

			$this.themeLightbox( opts );
		} );

		// Popup with video or map
		$( '.porto-popup-iframe' ).magnificPopup( $.extend( true, {}, theme.mfpConfig, {
			disableOn: 700,
			type: 'iframe',
			mainClass: 'mfp-fade',
			removalDelay: 160,
			preloader: false,
			fixedContentPos: false
		} ) );

		// Popup with ajax
		$( '.porto-popup-ajax' ).magnificPopup( $.extend( true, {}, theme.mfpConfig, {
			type: 'ajax'
		} ) );

		// Popup with content
		$( '.porto-popup-content' ).each( function () {
			var animation = $( this ).attr( 'data-animation' );
			$( this ).magnificPopup( $.extend( true, {}, theme.mfpConfig, {
				type: 'inline',
				fixedContentPos: false,
				fixedBgPos: true,
				overflowY: 'auto',
				closeBtnInside: true,
				preloader: false,
				midClick: true,
				removalDelay: 300,
				mainClass: animation
			} ) );
		} );

		// Porto Modal
		$( '.popup-youtube, .popup-vimeo, .popup-gmaps' ).each( function ( index ) {
			var overlayClass = $( this ).find( '.porto-modal-trigger' ).data( 'overlay-class' ),
				args = {
					type: 'iframe',
					removalDelay: 160,
					preloader: false,

					fixedContentPos: false
				};
			if ( typeof overlayClass != "undefined" && overlayClass ) {
				args.mainClass = escape( overlayClass );
			}
			$( this ).magnificPopup( args );
		} );

		if ( $( '.porto-modal-trigger.porto-onload' ).length > 0 ) {
			var $obj = $( '.porto-modal-trigger.porto-onload' ).eq( 0 ),
				timeout = 0;
			if ( $obj.data( 'timeout' ) ) {
				timeout = parseInt( $obj.data( 'timeout' ), 10 );
			}
			setTimeout( function () {
				porto_modal_open( $obj );
			}, timeout );
		}
		$( '.porto-modal-trigger' ).on( 'click', function ( e ) {
			e.preventDefault();
			porto_modal_open( $( this ) );
		} );

		/* Woocommerce */
		// login popup
		$( '.login-popup .porto-link-login, .login-popup .porto-link-register' ).magnificPopup( {
			items: {
				src: theme.ajax_url + '?action=porto_account_login_popup&nonce=' + js_porto_vars.porto_nonce,
				type: 'ajax'
			},
			tLoading: '<i class="porto-loading-icon"></i>',
			callbacks: {
				ajaxContentAdded: function () {
					$( window ).trigger( 'porto_login_popup_opened' );
				}
			}
		} );

		$( '.product-images' ).magnificPopup(
			$.extend( true, {}, theme.mfpConfig, {
				delegate: '.img-thumbnail a.zoom',
				type: 'image',
				gallery: { enabled: true }
			} )
		);
	}

	if ( $.fn.magnificPopup ) {
		porto_init_magnific_popup_functions();
	} else {
		setTimeout( function () {
			if ( $.fn.magnificPopup ) {
				porto_init_magnific_popup_functions();
			}
		}, 500 );
	}

	// Post Ajax Modal
	if ( typeof theme.PostAjaxModal !== 'undefined' ) {
		// Portfolio
		if ( $( '.page-portfolios' ).length ) {
			theme.PostAjaxModal.initialize( $( '.page-portfolios' ) );
		}
		// Member
		if ( $( '.page-members' ).length ) {
			theme.PostAjaxModal.initialize( $( '.page-members' ), 'member' );
		}
	}

	// Portfolio Ajax on Page
	if ( typeof theme.PortfolioAjaxPage !== 'undefined' ) {
		theme.PortfolioAjaxPage.initialize();
	}

	// Post Filter
	if ( typeof theme.PostFilter !== 'undefined' ) {
		// Portfolio
		if ( $( '.portfolio-filter' ).length ) {
			theme.PostFilter.initialize( $( '.portfolio-filter' ), 'portfolio' );
		}
		// Member
		if ( $( '.member-filter' ).length ) {
			theme.PostFilter.initialize( $( '.member-filter' ), 'member' );
		}
		// Faq
		if ( $( '.faq-filter' ).length ) {
			theme.PostFilter.initialize( $( '.faq-filter' ), 'faq' );
		}
	}

	// Member Ajax on Page
	if ( typeof theme.MemberAjaxPage !== 'undefined' ) {
		theme.MemberAjaxPage.initialize();
	}

	// Filter Zooms
	if ( typeof theme.FilterZoom !== 'undefined' ) {
		// Portfolio Filter Zoom
		theme.FilterZoom.initialize( $( '.page-portfolios' ) );
		// Member Filter Zoom
		theme.FilterZoom.initialize( $( '.page-members' ) );
		// Posts Related Style Filter Zoom
		theme.FilterZoom.initialize( $( '.blog-posts-related' ) );
	}

	// close popup using esc key
	var $minicart_offcanvas = $('.minicart-offcanvas'),
		$wl_offcanvas = $('.wishlist-offcanvas'),
		$mobile_sidebar = $('.mobile-sidebar'),
		$mobile_panel = $('#side-nav-panel'),
		$overlay_search = $('#header .btn-close-search-form'),
		$html = $('html');
	if ( $minicart_offcanvas.length || $wl_offcanvas.length || $mobile_sidebar.length || $mobile_panel.length || $('.skeleton-loading').length || $overlay_search.length ) {
		$(document.documentElement).on('keyup', function(e) {
			try {
				if ( e.keyCode == 27 ) {
					$minicart_offcanvas.removeClass( 'minicart-opened' );
					$wl_offcanvas.removeClass( 'minicart-opened' );
					if ($mobile_sidebar.length) {
						$html.removeClass('filter-sidebar-opened');
						$html.removeClass('sidebar-opened');
						$('.sidebar-overlay').removeClass('active');
					}
					if ($mobile_panel.length && $html.hasClass('panel-opened')) {
						$html.removeClass('panel-opened');
						$('.panel-overlay').removeClass('active');
					}
					if ($overlay_search.length) {
						$overlay_search.trigger('click');
					}
				}
			} catch ( err ) { }
		});
		$('.skeleton-loading').on('skeleton-loaded', function() {
			$mobile_sidebar = $('.mobile-sidebar');
		});
	}


	// Mouse Parallax
	if ($.fn.themeMouseparallax) {
		$(function () {
			$('[data-plugin="mouse-parallax"]').each(function () {
				var $this = $(this),
					opts;
				if ($this.data('parallax')) {
					$this.parallax('disable');
					$this.removeData('parallax');
					$this.removeData('options');
				}
				if ($this.hasClass('elementor-element')) {
					$this.children('.elementor-widget-container, .elementor-container, .elementor-widget-wrap, .elementor-column-wrap').addClass('layer').attr('data-depth', $this.attr('data-floating-depth'));
				} else {
					$this.children('.layer').attr('data-depth', $this.attr('data-floating-depth'));
				}

				var pluginOptions = $this.data('options');
				if (pluginOptions)
					opts = pluginOptions;

				$this.themeMouseparallax(opts);
			});
		});
	}

	if ( $.fn[ 'themePluginReadMore' ] && $( '[data-plugin-readmore]' ).length ) {
		$( '[data-plugin-readmore]:not(.manual)' ).themePluginReadMore();
	}
} );

( function ( theme, $ ) {
	// init wishlist off-canvas
	if ( $( '.wishlist-popup' ).length ) {
		var worker = null;

		$( '.wishlist-offcanvas .my-wishlist' ).on( 'click', function(e) {
			e.preventDefault();
			$(this).parent().toggleClass( 'minicart-opened' );
		} );
		$( '.wishlist-offcanvas .minicart-overlay' ).on( 'click', function() {
			$(this).closest('.wishlist-offcanvas').removeClass( 'minicart-opened' );
		} );

		var init_wishlist = function() {
			worker = new Worker( js_porto_vars.ajax_loader_url.replace( '/images/ajax-loader@2x.gif', '/js/woocommerce-worker.js' ) );
			worker.onmessage = function(e) {
				$( '.wishlist-popup' ).html( e.data );
			};
			worker.postMessage( { initWishlist: true, ajaxurl: theme.ajax_url, nonce: js_porto_vars.porto_nonce } );
		};

		if ( theme && theme.isLoaded ) {
			setTimeout(function() {
				init_wishlist();
			}, 100);
		} else {
			$( window ).on( 'load', function() {
				init_wishlist();
			} );
		}

		// remove from wishlist
		$('.wishlist-popup').on('click', '.remove_from_wishlist', function(e) {
			e.preventDefault();

			var $this = $(this),
				id = $this.attr('data-product_id'),
				$table = $('.wishlist_table #yith-wcwl-row-' + id + ' .remove_from_wishlist');

			$this.closest('.wishlist-item').find('.ajax-loading').show();

			if ($table.length) {
				$table.trigger('click');
			} else {
				$.ajax({
					url: yith_wcwl_l10n.ajax_url,
					data: {
						action: yith_wcwl_l10n.actions.remove_from_wishlist_action,
						remove_from_wishlist: id,
						from: 'theme'
					},
					method: 'post',
					success: function (data) {
						var $wcwlWrap = $('.yith-wcwl-add-to-wishlist.add-to-wishlist-' + id);
						if ($wcwlWrap.length) {
							var fragmentOptions = $wcwlWrap.data('fragment-options'),
								$link = $wcwlWrap.find('a');
							if ( $link.length ) {
								if (fragmentOptions.in_default_wishlist) {
									delete fragmentOptions.in_default_wishlist;
									$wcwlWrap.attr(JSON.stringify(fragmentOptions));
								}
								$wcwlWrap.removeClass('exists');
								$wcwlWrap.find('.yith-wcwl-wishlistexistsbrowse').addClass('yith-wcwl-add-button').removeClass('yith-wcwl-wishlistexistsbrowse');
								$wcwlWrap.find('.yith-wcwl-wishlistaddedbrowse').addClass('yith-wcwl-add-button').removeClass('yith-wcwl-wishlistaddedbrowse');
								$link.attr('href', location.href + '?post_type=product&amp;add_to_wishlist=' + id).attr('data-product-id', id).attr('data-product-type', fragmentOptions.product_type);
								var text = $('.single_add_to_wishlist').data('title');
								if ( ! text ) {
									text = 'Add to wishlist';
								}
								$link.attr('title', text).attr('data-title', text).addClass('add_to_wishlist single_add_to_wishlist').html('<span>' + text + '</span>');
							}
						}
						$(document.body).trigger('removed_from_wishlist');
						//$this.closest('.wishlist-item').remove();
					}
				});
			}
		});

		$( document.body ).on( 'added_to_wishlist removed_from_wishlist', function ( e ) {
			if ( worker ) {
				worker.postMessage( { loadWishlist: true, ajaxurl: theme.ajax_url, nonce: js_porto_vars.porto_nonce } );
			}
		});
	}

	// init Youtube video api
	var $youtube_videos = $('.porto-video-social.video-youtube');
	if ( $youtube_videos.length ) {
		window.onYouTubeIframeAPIReady = function() {
			$youtube_videos.each(function() {
				var $this = $(this),
					$wrap = $this.parent('.video-wrapper'),
					item_id = $this.attr('id'),
					youtube_id = $this.data('video'),
					is_loop = $this.data('loop'),
					enable_audio = $this.data('audio');
				new YT.Player(item_id, {
					width: '100%',
					//height: '100%',
					videoId: youtube_id,
					playerVars: {
						'autoplay': 1,
						'controls': 0,
						'modestbranding': 1,
						'rel': 0,
						'playsinline': 1,
						'showinfo': 0,
						'loop': is_loop
					},
					events: {
						onReady: function( t ) {
							if ($wrap.length) {
								$wrap.themeFitVideo();
							}
							if ( 0 === parseInt( enable_audio ) && t && t.target && t.target.mute ) {
								t.target.mute();
							}
						}
					}
				});
			});
		};

		if ($('script[src*="www.youtube.com/iframe_api"]').length) {
			setTimeout(onYouTubeIframeAPIReady, 350);
		} else {
			var tag = document.createElement('script');
			tag.src = "//www.youtube.com/iframe_api";
			var firstScriptTag = document.getElementsByTagName('script')[0];
			firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
		}
	}

	// init Vimeo video api
	var $vimeo_videos = $('.porto-video-social.video-vimeo');
	if ( $vimeo_videos.length ) {
		var portoVimeoInit = function() {
			$vimeo_videos.each(function() {
				var $this = $(this),
					$wrap = $this.parent('.fit-video'),
					item_id = $this.attr('id'),
					youtube_id = $this.data('video'),
					is_loop = $this.data('loop'),
					enable_audio = $this.data('audio');
				var player = new Vimeo.Player(item_id, {
					id: youtube_id,
					loop: 1 === parseInt( is_loop ) ? true : false,
					autoplay: true,
					transparent: false,
					background: true,
					muted: 0 === parseInt( enable_audio ) ? true : false,
					events: {
						onReady: function( t ) {
							if ($wrap.length) {
								$wrap.themeFitVideo();
							}
							if ( 0 === parseInt( enable_audio ) && t && t.target && t.target.mute ) {
								t.target.mute();
							}
						}
					}
				});
				if ( 0 === parseInt( enable_audio ) ) {
					player.setVolume( 0 );
				}
				if ( $wrap.length ) {
					player.ready().then(function () {
						$wrap.themeFitVideo();
					});
				}
			});
		};

		if ($('script[src="https://player.vimeo.com/api/player.js"]').length) {
			setTimeout(portoVimeoInit, 350);
		} else {
			var tag = document.createElement('script');
			tag.addEventListener('load', function(event) {
				setTimeout(portoVimeoInit, 50);
			});
			tag.src = "https://player.vimeo.com/api/player.js";
			var firstScriptTag = document.getElementsByTagName('script')[0];
			firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
		}
	}
} ).apply( this, [ window.theme, jQuery ] );