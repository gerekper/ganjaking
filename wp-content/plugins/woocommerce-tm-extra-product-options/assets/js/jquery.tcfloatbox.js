/**
 * jquery.tcfloatbox.js
 *
 * @version: v1.1
 * @author: themeComplete
 *
 * Created by themeComplete
 *
 * Copyright (c) 2019 themeComplete http://themecomplete.com
 */
( function( $ ) {
	'use strict';

	var FloatBox = function( dom, options ) {
		this.element = $( dom );

		this.settings = $.extend( {}, $.fn.tcFloatBox.defaults, options );
		this.settings.type = '<' + this.settings.type + '>';

		this.top = 0;
		this.left = 0;
		this.ticking = false;

		if ( this.element.length === 1 ) {
			this.init();
			return this;
		}

		return false;
	};

	FloatBox.prototype = {
		constructor: FloatBox,

		destroy: function() {
			var settings = this.settings;

			if ( this.instance !== undefined ) {
				$.fn.tcFloatBox.instances.splice( this.instance, 1 );

				delete this.instance;

				if ( settings.hideelements ) {
					$( 'embed, object, select' ).css( {
						visibility: 'visible'
					} );
				}

				if ( settings._ovl ) {
					settings._ovl.unbind();
					settings._ovl.remove();
					delete settings._ovl;
				}

				$( settings.floatboxID ).remove();

				this.element.removeData( 'tcfloatbox' );

				$( window ).off( 'scroll.tcfloatbox' + this.instance );
				$( window ).off( 'resize.tcfloatbox' + this.instance );
			}

			return this;
		},

		hide: function() {
			var settings = this.settings;

			if ( settings.hideelements ) {
				$( 'embed, object, select' ).css( {
					visibility: 'visible'
				} );
			}
			if ( settings.showoverlay === true ) {
				if ( settings._ovl ) {
					settings._ovl.hide();
				}
			}

			$( settings.floatboxID )
				.addClass( 'tc-closing' )
				.removeClass( settings.animateIn )
				.addClass( settings.animateOut );
			$( settings.floatboxID ).animate(
				{
					opacity: 0
				},
				settings.closefadeouttime,
				function() {
					$( settings.floatboxID ).hide();
					$( settings.floatboxID )
						.removeClass( 'tc-closing' )
						.addClass( 'tc-closed' );
				}
			);

			$( window ).off( 'scroll.tcfloatbox' + this.instance );
			$( window ).off( 'resize.tcfloatbox' + this.instance );
		},

		requestTick: function() {
			var settings = this.settings;

			if ( ! this.ticking ) {
				if ( settings.refresh ) {
					setTimeout(
						this.requestAnimationFrame.bind( this ),
						settings.refresh
					);
				} else {
					requestAnimationFrame( this.update );
				}

				this.ticking = true;
			}
		},

		requestAnimationFrame: function() {
			requestAnimationFrame( this.update.bind( this ) );
		},

		update: function() {
			this.render();
			this.ticking = false;
		},

		doit: function() {
			this.requestTick();
		},

		render: function() {
			var settings = this.settings;
			var size = $.epoAPI.dom.size();
			var scroll;
			var top;
			var left;

			if ( settings.refresh === 'fixed' ) {
				scroll = { top: 0, left: 0 };
			} else {
				scroll = $.epoAPI.dom.scroll();
			}

			top = parseInt(
				scroll.top +
					( ( size.visibleHeight - $( settings.floatboxID ).height() ) / 2 ),
				10
			);
			left = parseInt(
				scroll.left +
					( ( size.visibleWidth - $( settings.floatboxID ).width() ) / 2 ),
				10
			);

			top = parseInt( ( top - this.top ) / settings.fps, 10 );
			left = parseInt( ( left - this.left ) / settings.fps, 10 );

			this.top += top;
			this.left += left;

			$( settings.floatboxID ).css( {
				top: this.top + 'px',
				left: this.left + 'px',
				opacity: 1
			} );
		},

		show: function() {
			var settings = this.settings;
			var top;
			var size;

			if ( this.element.length === 1 ) {
				if ( this.instance === undefined ) {
					this.init();
				}

				if ( settings.hideelements ) {
					$( 'embed, object, select' ).css( {
						visibility: 'hidden'
					} );
				}

				size = $.epoAPI.dom.size();

				if ( settings.showoverlay === true ) {
					if ( ! settings._ovl ) {
						settings._ovl = $( '<div class="fl-overlay"></div>' ).css(
							{
								zIndex: parseInt( settings.zIndex, 10 ) - 1,
								opacity: settings.overlayopacity
							}
						);
						settings._ovl.appendTo( 'body' );
						if ( ! settings.ismodal ) {
							if ( settings.cancelEvent || settings.unique ) {
								settings._ovl.on(
									'click',
									this.applyCancelEvent.bind( this )
								);
							} else {
								settings._ovl.on(
									'click',
									settings.cancelfunc.bind( this )
								);
							}
						}
					} else {
						settings._ovl.show();
					}
				}

				if ( settings.showfunc ) {
					settings.showfunc.call();
				}

				$( settings.floatboxID )
					.removeClass( 'tc-closing' )
					.addClass(
						settings.animationBaseClass + ' ' + settings.animateIn
					);

				if ( settings.refresh === 'fixed' ) {
					if ( settings.top !== false ) {
						top = settings.top;
					} else {
						top = parseInt(
							( size.visibleHeight -
								$( settings.floatboxID ).height() ) /
								2,
							10
						);
						top = top + 'px';
					}
					$( settings.floatboxID ).css( {
						position: 'fixed',
						top: top
					} );

					if ( settings.left !== false ) {
						$( settings.floatboxID ).css( {
							left: settings.left
						} );
					}
				} else {
					this.render();
				}
			}
		},

		applyCancelEvent: function() {
			var settings = this.settings;

			if ( settings.cancelEvent === true ) {
				this.destroy();
			} else if ( typeof settings.cancelEvent === 'function' ) {
				settings.cancelEvent.call( this, this );
			}
		},

		applyCancelEventFromKey: function( e ) {
			if ( e.which === 27 ) {
				this.applyCancelEvent();
			}
		},

		applyUpdateEvent: function() {
			var settings = this.settings;

			if ( typeof settings.updateEvent === 'function' ) {
				settings.updateEvent.call( this, this );
			}
		},

		applyUpdateEventFromKey: function( e ) {
			if ( e.which === 13 ) {
				this.applyUpdateEvent();
			}
		},

		init: function() {
			var settings = this.settings;
			var size;
			var scroll;
			var l = 0;
			var h;

			if ( this.element.length === 1 ) {
				// Instance initialization
				if ( $.fn.tcFloatBox.instances.length > 0 ) {
					settings.zIndex =
						parseInt(
							$.fn.tcFloatBox.instances[
								$.fn.tcFloatBox.instances.length - 1
							].zIndex,
							10
						) + 100;
				}
				this.instance = $.fn.tcFloatBox.instances.length;
				$.fn.tcFloatBox.instances.push( settings );

				settings.id = settings.id + this.instance;
				settings.floatboxID = '#' + $.epoAPI.dom.id( settings.id );

				this.hide();

				size = $.epoAPI.dom.size();
				scroll = $.epoAPI.dom.scroll();

				$( settings.type )
					.attr( 'id', settings.id )
					.addClass( settings.classname )
					.html( settings.data )
					.appendTo( this.element );

				$( settings.floatboxID ).css( {
					width: settings.width,
					height: settings.height
				} );

				h = parseInt(
					scroll.left +
						( ( size.visibleWidth - $( settings.floatboxID ).width() ) / 2 ),
					10
				);

				$( settings.floatboxID ).css( {
					top: l + 'px',
					left: h + 'px',
					'z-index': settings.zIndex
				} );

				this.top = l;
				this.left = h;
				this.cancelfunc = settings.cancelfunc;

				if ( settings.cancelEvent && settings.cancelClass ) {
					$( settings.floatboxID )
						.find( settings.cancelClass )
						.on( 'click', this.applyCancelEvent.bind( this ) );
					if ( settings.isconfirm ) {
						$( document )
							.off( 'keyup.escape-' + settings.floatboxID )
							.on(
								'keyup.escape-' + settings.floatboxID,
								this.applyCancelEventFromKey.bind( this )
							);
					}
				}

				if ( settings.updateEvent && settings.updateClass ) {
					$( settings.floatboxID )
						.find( settings.updateClass )
						.on( 'click', this.applyUpdateEvent.bind( this ) );
					if ( settings.isconfirm ) {
						$( document )
							.off( 'keyup.enter-' + settings.floatboxID )
							.on(
								'keyup.enter-' + settings.floatboxID,
								this.applyUpdateEventFromKey.bind( this )
							);
					}
				}

				this.show();

				if ( settings.refresh !== 'fixed' ) {
					$( window ).on(
						'scroll.tcfloatbox' + this.instance,
						this.doit.bind( this )
					);
				}

				$( window ).on(
					'resize.tcfloatbox' + this.instance,
					this.doit.bind( this )
				);
			}
		}
	};

	$.fn.tcFloatBox = function( option ) {
		var methodReturn;
		var targets = $( this );
		var data = targets.data( 'tcfloatbox' );
		var options;
		var ret;

		if ( typeof option === 'object' ) {
			options = option;
		} else {
			options = {};
		}

		if ( ! data ) {
			data = new FloatBox( this, options );
			targets.data( 'tcfloatbox', data );
		}

		if ( typeof option === 'string' ) {
			methodReturn = data[ option ].apply( data, [] );
		}

		if ( methodReturn === undefined ) {
			ret = targets;
		} else {
			ret = methodReturn;
		}

		return ret;
	};

	$.fn.tcFloatBox.defaults = {
		id: 'flasho',
		classname: 'flasho',
		type: 'div',
		data: '',
		width: '500px',
		height: 'auto',
		closefadeouttime: 1000,
		animationBaseClass: 'tm-animated',
		animateIn: 'fadeIn',
		animateOut: 'fadeOut',
		top: false,
		left: false,
		refresh: false,
		fps: 4,
		hideelements: false,
		showoverlay: true,
		zIndex: 100100,
		ismodal: false,
		cancelfunc: FloatBox.prototype.hide,
		showfunc: null,
		cancelEvent: true,
		cancelClass: '.floatbox-cancel',
		updateEvent: false,
		updateClass: false,
		unique: true,
		overlayopacity: 0.5,
		isconfirm: false
	};

	$.fn.tcFloatBox.instances = [];

	$.fn.tcFloatBox.Constructor = FloatBox;

	$.tcFloatBox = function( options ) {
		var targets = $( 'body' );
		var data = false;
		var hasAtLeastOneNonToolTip = targets
			.map( function() {
				return $( this ).data( 'tcfloatbox' ) || '';
			} )
			.get()
			.some( function( value ) {
				return value === '';
			} );
		if ( hasAtLeastOneNonToolTip || options.unique ) {
			data = new FloatBox( targets, options );
			targets.data( 'tcfloatbox', data );
		} else {
			data = targets.data( 'tcfloatbox' );
			data.init();
		}
		return data;
	};
}( window.jQuery ) );
