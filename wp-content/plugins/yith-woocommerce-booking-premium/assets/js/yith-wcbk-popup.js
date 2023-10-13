/* globals jQuery, bk */
( function ( $, window, document ) {
	$.fn.yith_wcbk_popup = function ( options ) {
		var overlay = null,
			self    = {};

		self.popup = $( this );
		self.opts  = {};

		var defaults = {
			popup_class            : 'yith-wcbk-popup',
			overlay_class          : 'yith-wcbk-overlay',
			close_btn_class        : 'yith-wcbk-popup-close',
			position               : 'center',
			popup_delay            : 0,
			ajax                   : false,
			ajax_container         : 'yith-wcbk-popup-ajax-container',
			url                    : '',
			ajax_data              : {},
			ajax_custom_get_content: false,
			popup_css              : {
				width: '50%'
			},
			block_params           : {
				message        : bk.loader_svg,
				blockMsgClass  : 'yith-wcbk-block-ui-element',
				css            : {
					border    : 'none',
					background: 'transparent'
				},
				overlayCSS     : {
					background: '#fff',
					opacity   : 0.7
				},
				ignoreIfBlocked: true
			}
		};

		self.init = function () {

			self.opts = $.extend( {}, defaults, options );
			if ( options === 'close' ) {
				_close();
				return;
			}

			_createOverlay();
			if ( self.opts.ajax === true ) {
				_getAjaxContent();
				_setPopupPosition( 'init-center' );
			} else {
				self.popup = self.popup.clone();

				self.popup.css( self.opts.popup_css ).addClass( 'yith-wcbk-popup-opened' );
				$( document.body ).append( self.popup );
				_setPopupPosition( 'init-center' );
			}

			_initEvents();
			_show();
		};

		var _createOverlay    = function () {
				// add_overlay if not exist
				if ( $( document ).find( '.' + self.opts.overlay_class ).length > 0 ) {
					overlay = $( document ).find( '.' + self.opts.overlay_class ).first();
				} else {
					overlay = $( '<div />' ).addClass( self.opts.overlay_class );
					$( document.body ).append( overlay );
				}
			},
			_getAjaxContent   = function () {
				self.popup         = $( '<div />' ).addClass( self.opts.popup_class );
				var closeBtn       = $( '<span />' ).addClass( self.opts.close_btn_class + ' dashicons dashicons-no-alt' ),
					popupContainer = $( '<div />' ).addClass( self.opts.ajax_container );

				self.popup.append( closeBtn );
				self.popup.append( popupContainer );

				$( document.body ).append( self.popup );
				self.popup.block( self.opts.block_params );

				if ( self.opts.ajax_custom_get_content ) {

				} else {
					$.ajax( {
								data   : self.opts.ajax_data,
								url    : self.opts.url,
								success: function ( data ) {
									self.popup.find( '.' + self.opts.ajax_container ).html( data );
									self.popup.unblock();

									_resize();
								}
							} );
				}
			},
			_setPopupPosition = function ( position ) {
				var w           = self.popup.outerWidth(),
					h           = self.popup.outerHeight(),
					center_top  = Math.max( 0, ( ( $( window ).height() - h ) / 2 ) ),
					center_left = Math.max( 0, ( ( $( window ).width() - w ) / 2 ) );

				switch ( position ) {
					case 'init-center':
						self.popup.css( {
											position: 'fixed',
											top     : "calc(50% - 100px)",
											left    : "calc(50% - 100px)",
											width   : "100px",
											height  : "100px"
										} );
						break;
					case 'big-center':
						self.popup.css( {
											position: 'fixed',
											top     : "5%",
											left    : "5%",
											width   : "90%",
											height  : "90%"
										} );
						break;
					case 'center':
						self.popup.css( {
											position: 'fixed',
											top     : center_top + "px",
											left    : center_left + "px"
										} );
						break;
				}
			},
			_initEvents       = function () {
				$( document ).on( 'click', '.' + self.opts.overlay_class, function () {
					_close();
				} );

				self.popup.on( 'click', '.' + self.opts.close_btn_class, function () {
					_close();
				} );

			},
			_show             = function () {
				overlay.fadeIn( 'fast' );

				self.popup.fadeIn( 'fast', function () {
					if ( !self.opts.ajax ) {
						_resize();
					}
				} );
			},
			_resize           = function () {
				self.popup.children().hide();
				self.popup.show();

				self.popup.animate( {
										opacity: 1,
										top    : "5%",
										left   : "5%",
										width  : "90%",
										height : "90%",
										easing : 'easeOutBounce'
									}, {
										duration: 300,
										complete: function () {
											self.popup.children().fadeIn( 'fast' );
										}
									} );
			},
			_destroy          = function () {
				overlay.remove();
				self.popup.remove();
			},
			_close            = function () {
				$( document ).find( '.' + self.opts.overlay_class ).hide();
				self.popup.hide();
				_destroy();
			};

		self.init();

		self.popup.on( 'yith_wcbk_popup:set_ajax_content', function ( e, content ) {
			self.popup.find( '.' + self.opts.ajax_container ).html( content );
			self.popup.unblock();

			_resize();
		} );

		return self.popup;
	};

} )( jQuery, window, document );