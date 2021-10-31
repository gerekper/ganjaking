(function($) {
	"use strict";
	$.fn.portoImageCompare = function() {
		var resizeFn = function( $this, $before, $after, orientation, rect ) {
			if ( 'vertical' == orientation ) {
				$before.css( 'clip', 'rect(0,' + rect.w + ',' + rect.ch + ',0)' );
				$after.css( 'clip', 'rect(' + rect.ch + ',' + rect.w + ',' + rect.h + ',0)' );
			} else {
				$before.css( 'clip', 'rect(0,' + rect.cw + ',' + rect.h + ',0)' );
				$after.css( 'clip', 'rect(0, ' + rect.w + ',' + rect.h + ',' + rect.cw + ')' );
			}
			$this.css( 'height', rect.h );
		};

		var getRatio = function( x, y, orientation, l, t, w, h ) {
			var ratio;
			if ( 'vertical' == orientation ) {
				ratio = ( y - t ) / h;
			} else {
				ratio = ( x - l ) / w;
			}
			return Math.max( 0, Math.min( 1, ratio ) );
		}

		return this.each(function() {
			var $this = $(this),
				offset_ratio = $this.attr('data-offset') ? $this.attr('data-offset') : 0.5,
				orientation = $this.attr('data-orientation') ? $this.attr('data-orientation') : 'horizontal',
				handle_action = $this.attr('data-handle-action') ? $this.attr('data-handle-action') : 'click',
				$before_img = $this.find('.porto-image-comparison-before'),
				$after_img = $this.find('.porto-image-comparison-after'),
				$handle = $this.find('.porto-image-comparison-handle'),
				width1 = 0,
				height1 = 0,
				this_left = 0,
				this_top = 0;

			var moveEnter = function( e ) {
				if ( ( e.distX > e.distY && e.distX < -e.distY || e.distX < e.distY && e.distX > -e.distY ) && 'vertical' !== orientation ) {
					e.preventDefault();
				} else if ( ( e.distX < e.distY && e.distX < -e.distY || e.distX > e.distY && e.distX > -e.distY ) && 'vertical' === orientation ) {
					e.preventDefault();
				}

				$this.addClass( 'active' );
				var this_offset = $this.offset();
				width1 = $before_img.width();
				height1 = $before_img.height();
				this_left = this_offset.left;
				this_top = this_offset.top;
			};

			var moveHandle = function( e ) {
				if ( $this.hasClass( 'active' ) ) {
					offset_ratio = getRatio( e.pageX, e.pageY, orientation, this_left, this_top, width1, height1 );
					var w = width1,
						h = height1,
						t = { w: w + 'px', h: h + 'px', cw: w * offset_ratio + 'px', ch: h * offset_ratio + 'px' };
					$handle.css( 'vertical' === orientation ? 'top' : 'left','vertical' === orientation ? h * offset_ratio : w * offset_ratio );
					resizeFn( $this, $before_img, $after_img, orientation, t );
				}
			};

			$( window ).on( 'resize.porto-image-comparison', function( e ) {
				var w = $before_img.width(),
					h = $before_img.height(),
					t = { w: w + 'px', h: h + 'px', cw: w * offset_ratio + 'px', ch: h * offset_ratio + 'px' };
				$handle.css( 'vertical' === orientation ? 'top' : 'left','vertical' === orientation ? h * offset_ratio : w * offset_ratio );
				resizeFn( $this, $before_img, $after_img, orientation, t );
			} );

			var moveTarget = 'handle_only' == handle_action ? $handle : $this;
			moveTarget.on( 'movestart', moveEnter );
			moveTarget.on( 'move', moveHandle );
			moveTarget.on( 'moveend', function() {
				$this.removeClass( 'active' );
			} );

			if ( 'hover' == handle_action ) {
				$this.on( 'mouseenter', moveEnter );
				$this.on( 'mousemove', moveHandle );
				$this.on( 'mouseleave', function() {
					$this.removeClass( 'active' );
				} );
			}
			$handle.on( 'touchmove', function( e ) {
				e.preventDefault();
			} );
			$this.find( 'img' ).on( 'mousedown', function( e ) {
				e.preventDefault();
			} );

			if ( 'click' == handle_action ) {
				$this.on( 'click', function(e) {
					var this_left = $this.offset().left,
						this_top = $this.offset().top;

					width1 = $before_img.width();
					height1 = $before_img.height();

					offset_ratio = getRatio( e.pageX, e.pageY, orientation, this_left, this_top, width1, height1 );

					$this.addClass( 'active' );
					if ( 'vertical' == orientation ) {
						$handle.stop( true, true ).animate(
							{ top: height1 * offset_ratio + 'px' },
							{
								queue: false,
								duration: 300,
								easing: 'easeOutQuad',
								step: function( s ) {
									var t = { w: width1 + 'px', h: height1 + 'px', cw: height1 * ( s / width1 ) + 'px', ch: s + 'px' };
									resizeFn( $this, $before_img, $after_img, orientation, t );
								},
								complete: function() {
									$this.removeClass( 'active' );
								}
							}
						);
					} else {
						$handle.stop( true, true ).animate(
							{ left: width1 * offset_ratio + 'px' },
							{
								queue: false,
								duration: 300,
								easing: 'easeOutQuad',
								step: function( s ) {
									var t = { w: width1 + 'px', h: height1 + 'px', cw: s + 'px', ch: height1 * ( s / width1 ) + 'px' };
									resizeFn( $this, $before_img, $after_img, orientation, t );
								},
								complete: function() {
									$this.removeClass( 'active' );
								}
							}
						);
					}
				} );
			}

			$this.addClass( 'initialized' );

			$( window ).trigger( 'resize.porto-image-comparison' );

		});

	};

	$(window).on( 'load', function() {
		$( '.porto-image-comparison' ).portoImageCompare();

		$( document.body ).on( 'porto_init', function( e, $wrap ) {
			$wrap.find( '.porto-image-comparison' ).portoImageCompare();
		} );
	} );
})(jQuery);