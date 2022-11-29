/**
 * jquery.swatchbook.js v1.1.0
 * http://www.codrops.com
 *
 * Licensed under the MIT license.
 * http://www.opensource.org/licenses/mit-license.php
 *
 * Copyright 2012, Codrops
 * http://www.codrops.com
 */

( function ( $, window, undefined ) {
	'use strict';

	// global
	const Modernizr = window.bsfmodernizr;

	jQuery.fn.reverse = [].reverse;

	$.SwatchBook = function ( options, element ) {
		this.$el = $( element );
		this._init( options );
	};

	$.SwatchBook.defaults = {
		// index of initial centered item
		center: 6,
		// number of degrees that is between each item
		angleInc: 8,
		speed: 700,
		easing: 'ease',
		// amount in degrees for the opened item's next sibling
		proximity: 45,
		// amount in degrees between the opened item's next siblings
		neighbor: 4,
		// animate on load
		onLoadAnim: true,
		// if it should be closed by default
		initclosed: false,
		// index of the element that when clicked, triggers the open/close function
		// by default there is no such element
		closeIdx: -1,
		// open one specific item initially (overrides initclosed)
		openAt: -1,
	};

	$.SwatchBook.prototype = {
		_init( options ) {
			this.options = $.extend( true, {}, $.SwatchBook.defaults, options );

			this.$items = this.$el.children( 'div' );
			this.itemsCount = this.$items.length;
			this.current = -1;
			this.support = Modernizr.csstransitions;
			this.cache = [];

			if ( this.options.onLoadAnim ) {
				this._setTransition();
			}

			if ( ! this.options.initclosed ) {
				this._center( this.options.center, this.options.onLoadAnim );
			} else {
				this.isClosed = true;
				if ( ! this.options.onLoadAnim ) {
					this._setTransition();
				}
			}

			if (
				this.options.openAt >= 0 &&
				this.options.openAt < this.itemsCount
			) {
				this._openItem( this.$items.eq( this.options.openAt ) );
			}

			this._initEvents();
		},
		_setTransition() {
			if ( this.support ) {
				this.$items.css( {
					transition:
						'all ' +
						this.options.speed +
						'ms ' +
						this.options.easing,
				} );
			}
		},
		_openclose() {
			this.isClosed
				? this._center( this.options.center, true )
				: this.$items.css( { transform: 'rotate(0deg)' } );
			this.isClosed = ! this.isClosed;
		},
		_center( idx, anim ) {
			const self = this;

			this.$items.each( function ( i ) {
				const transformStr =
					'rotate(' + self.options.angleInc * ( i - idx ) + 'deg)';
				$( this ).css( { transform: transformStr } );
			} );
		},
		_openItem( $item ) {
			const itmIdx = $item.index();

			if ( itmIdx !== this.current ) {
				if (
					this.options.closeIdx !== -1 &&
					itmIdx === this.options.closeIdx
				) {
					this._openclose();
					this._setCurrent();
				} else {
					this._setCurrent( $item );
					$item.css( { transform: 'rotate(0deg)' } );
					this._rotateSiblings( $item );
				}
			}
		},
		_initEvents() {
			const self = this;

			this.$items.on( 'click.swatchbook', function ( event ) {
				self._openItem( $( this ) );
			} );
		},
		_rotateSiblings( $item ) {
			let self = this,
				idx = $item.index(),
				$cached = this.cache[ idx ],
				$siblings;

			if ( $cached ) {
				$siblings = $cached;
			} else {
				$siblings = $item.siblings();
				this.cache[ idx ] = $siblings;
			}

			$siblings.each( function ( i ) {
				const rotateVal =
					i < idx
						? self.options.angleInc * ( i - idx )
						: i - idx === 1
						? self.options.proximity
						: self.options.proximity +
						  ( i - idx - 1 ) * self.options.neighbor;

				const transformStr = 'rotate(' + rotateVal + 'deg)';

				$( this ).css( { transform: transformStr } );
			} );
		},
		_setCurrent( $el ) {
			this.current = $el ? $el.index() : -1;
			this.$items.removeClass( 'ff-active' );
			if ( $el ) {
				$el.addClass( 'ff-active' );
			}
		},
	};

	const logError = function ( message ) {
		if ( window.console ) {
			window.console.error( message );
		}
	};

	$.fn.swatchbook = function ( options ) {
		let instance = $.data( this, 'swatchbook' );

		if ( typeof options === 'string' ) {
			const args = Array.prototype.slice.call( arguments, 1 );

			this.each( function () {
				if ( ! instance ) {
					logError(
						'cannot call methods on swatchbook prior to initialization; ' +
							"attempted to call method '" +
							options +
							"'"
					);
					return;
				}

				if (
					! $.isFunction( instance[ options ] ) ||
					options.charAt( 0 ) === '_'
				) {
					logError(
						"no such method '" +
							options +
							"' for swatchbook instance"
					);
					return;
				}

				instance[ options ].apply( instance, args );
			} );
		} else {
			this.each( function () {
				if ( instance ) {
					instance._init();
				} else {
					instance = $.data(
						this,
						'swatchbook',
						new $.SwatchBook( options, this )
					);
				}
			} );
		}

		return instance;
	};
} )( jQuery, window );
