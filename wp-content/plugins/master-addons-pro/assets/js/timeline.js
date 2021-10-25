;(
	function( $, window, document, undefined ) {

		$.maTimeline = function(element, options) {

			var defaults = {
				scope 	: $(window),
				points 	: '.timeline-item__point',
				lineLocation : 50,
			};

			var plugin = this;

			plugin.opts = {};

			var $window			= null,
				$viewport		= $(window),
				$element		= $(element),

				dragging 		= false,
				scrolling 		= false,
				resizing 		= false,

				latestKnownScrollY  	= -1,
				latestKnownWindowHeight = -1,
				currentScrollY 			= 0,
				currentWindowHeight 	= 0,
				ticking 				= false,
				updateAF				= null,

				$line 			= $element.find( '.ma-el-timeline__line' ),
				$progress		= $line.find( '.ma-el-timeline__line__inner' ),
				$cards			= $element.find( '.ma-el-timeline__item' );

			plugin.init = function() {
				plugin.opts = $.extend({}, defaults, options);
				plugin._construct();
			};

			plugin._construct = function() {

				$window				= plugin.opts.scope;
				currentScrollY 		= $window.scrollTop();
				currentWindowHeight = $(window).height();

				plugin.events();
				plugin.requestTick();
				plugin.animateCards();

			};

			plugin.requestTick = function() {
				if ( ! ticking ) {
					updateAF = requestAnimationFrame( plugin.refresh );
				}
				ticking = true;
			};

			plugin.animateCards = function() {
				$cards.each( function() {
					if( $(this).offset().top <= $window.scrollTop() + $viewport.outerHeight() * 0.95 ) {
						$(this).addClass('bounce-in');
					}
				});
			};

			plugin.events = function() {

				$window.on('scroll', plugin.onScroll );
				$(window).on('resize', plugin.onResize );

			};

			plugin.onScroll = function() {
				currentScrollY = $window.scrollTop();

				plugin.requestTick();
				plugin.animateCards();
			};

			plugin.onResize = function() {
				currentScrollY = $window.scrollTop();
				currentWindowHeight = $window.height();

				plugin.requestTick();
			};

			plugin.setup = function() {

				$line.css({
					'top' 		: $cards.first().find( plugin.opts.points ).offset().top - $cards.first().offset().top,
					'bottom'	: $element.offset().top + $element.outerHeight() - $cards.last().find( plugin.opts.points ).offset().top,
				});

			};

			plugin.refresh = function() {

				ticking = false;

				if ( latestKnownWindowHeight !== currentWindowHeight ) {
					plugin.setup();
				}

				if ( ( latestKnownScrollY !== currentScrollY ) || ( latestKnownWindowHeight !== currentWindowHeight ) ) {

					latestKnownScrollY 		= currentScrollY;
					latestKnownWindowHeight = currentWindowHeight;

					plugin.progress();
				}
			}

			plugin.progress = function() {

				var _coeff = 100 / plugin.opts.lineLocation,
					_last_pos = $cards.last().find( plugin.opts.points ).offset().top,
					_pos = ( $window.scrollTop() - $progress.offset().top ) + ( $viewport.outerHeight() / _coeff );

					if ( _last_pos <= ( $window.scrollTop() + $viewport.outerHeight() / _coeff ) ) {
						_pos = _last_pos - $progress.offset().top;
					}

					$progress.css({
						'height' : _pos + 'px'
					});

				$cards.each( function() {
					if ( $(this).find( plugin.opts.points ).offset().top < ( $window.scrollTop() + $viewport.outerHeight() / _coeff ) ) {
						$(this).addClass('is--focused');
					} else {
						$(this).removeClass('is--focused');
					}
				});

			};

			plugin.destroy = function() {

				// $window.off( 'scroll', plugin.update );
				$element.removeData( 'maTimeline' );

			};

			plugin.init();

		};

		$.fn.maTimeline = function(options) {

			return this.each(function() {

				$.fn.maTimeline.destroy = function() {
					if( 'undefined' !== typeof( plugin ) ) {
						$(this).data( 'maTimeline' ).destroy();
						$(this).removeData( 'maTimeline' );
					}
				}

				if (undefined === $(this).data('maTimeline')) {
					var plugin = new $.maTimeline(this, options);
					$(this).data('maTimeline', plugin);
				}
			});

		};

	}

)( jQuery, window, document );
