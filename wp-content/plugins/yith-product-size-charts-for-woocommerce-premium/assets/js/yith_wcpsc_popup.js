(function ( $, window, document ) {
    $.fn.yith_wcpsc_popup = function ( options ) {
        var overlay = null;

        self.popup = $(this);
        self.opts = {};

        var defaults = {
            popup_class: 'yith-wcpsc-popup',
            overlay_class: 'yith-wcpsc-overlay',
            close_btn_class: 'yith-wcpsc-popup-close',
            position: 'center',
            popup_delay: 0,
            effect: 'fade',
            ajax: false,
            url: '',
            popup_css: {
                width: '50%'
            },
            block_params: {
                message: 	null,
                overlayCSS: {
                    background: 'transparent',
                    opacity: 	0.7
                }
            }
        };

        self.init = function () {

            self.opts = $.extend( {}, defaults, options );
            if ( options === 'close' ) {
                _close();
                return;
            }

            _createOverlay();
            if ( self.opts.ajax == true ) {
                _getAjaxContent();
            }else {
                self.popup = self.popup.clone();
            }

            self.popup.css( self.opts.popup_css ).addClass('yith-wcpsc-popup-opened');
            $( document.body ).append( self.popup );

            _setPopupPosition( self.opts.position );
            _initEvents();
            _show( self.opts.effect );
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
                self.popup = $( '<div />' ).addClass( self.opts.popup_class );
                $.ajax( {
                    url: self.opts.url,
                    success: function ( data ) {
                        self.popup.append( data );
                        self.popup.unblock();
                        _setPopupPosition();
                    }
                } );
                $( document.body ).append( popup );
                self.popup.block(self.opts.block_params);
            },
            _setPopupPosition = function ( position ) {
                var w           = self.popup.outerWidth(),
                    h           = self.popup.outerHeight(),
                    center_top  = Math.max( 0, ( ( $( window ).height() - h) / 2) ),
                    center_left = Math.max( 0, ( ( $( window ).width() - w) / 2) );

                switch ( position ) {
                    case 'center':
                        self.popup.css( {
                            position: 'fixed',
                            top: center_top + "px",
                            left: center_left + "px"
                        } );
                        break;
                    case 'top-left':
                        self.popup.css( { position: 'fixed', top: 0, left: 0 } );
                        break;
                    case 'top-rigth':
                        self.popup.css( { position: 'fixed', top: 0, right: 0, left: 'auto' } );
                        break;
                    case 'bottom-left':
                        self.popup.css( { position: 'fixed', bottom: 0, left: 0, top: 'auto' } );
                        break;
                    case 'bottom-right':
                        self.popup.css( { position: 'fixed', bottom: 0, right: 0, top: 'auto', left: 'auto' } );
                        break;
                    case 'top-center':
                        self.popup.css( { position: 'fixed', top: 0, left: center_left + "px" } );
                        break;
                    case 'bottom-center':
                        self.popup.css( { position: 'fixed', bottom: 0, left: center_left + "px", top: 'auto' } );
                        break;
                }
            },
            _initEvents       = function () {
                $( document ).on( 'click', '.' + self.opts.overlay_class, function () {
                    _close();
                } );

                self.popup.on('click', '.' + self.opts.close_btn_class, function(){
                    _close();
                });

            },
            _show             = function ( effect ) {
                overlay.fadeIn( 'fast' );

                var w = self.popup.outerWidth(),
                    h = self.popup.outerHeight();

                switch ( effect ) {
                    case 'fade':
                        self.popup.fadeIn( 'slow', function(){
                            self.popup.css({height: 'auto'});
                        } );
                        break;
                    case 'slide':
                        self.popup.slideDown( 'slow', function(){
                            self.popup.css({height: 'auto'});
                        } );
                        break;
                    case 'zoomIn':
                        self.popup.css( {
                            opacity: 0,
                            width: '0',
                            height: '0',
                            top: '50%',
                            left: '50%'

                        } );

                        self.popup.children().hide();
                        self.popup.show();

                        self.popup.animate( {
                            opacity: 1,
                            width: w,
                            height: h,
                            easing: 'easeOutBounce'
                        }, {
                            duration: 400,
                            progress: function () {
                                _setPopupPosition( self.opts.position );
                            },
                            complete: function () {
                                self.popup.children().fadeIn( 'fast' );
                                self.popup.css({height: 'auto'});
                            }
                        });
                        break;
                    case 'zoomOut':
                        self.popup.css( {
                            opacity: 0,
                            width: '100%',
                            height: '100%',
                            top: '0',
                            left: '0'
                        } );

                        self.popup.children().hide();
                        self.popup.show();

                        self.popup.animate( {
                            opacity: 1,
                            width: w,
                            height: h,
                            easing: 'easeOutBounce'
                        }, {
                            duration: 400,
                            progress: function () {
                                _setPopupPosition( self.opts.position );
                            },
                            complete: function () {
                                self.popup.children().fadeIn( 'fast' );
                                self.popup.css({height: 'auto'});
                            }
                        });
                        break;

                }
            },
            _destroy          = function () {
                if (self.popup.is('.yith-wcpsc-popup-opened')) {
                    self.popup.remove();
                }
            },
            _close            = function () {
                $( document ).find( '.' + self.opts.overlay_class ).hide();
                self.popup.hide();
                _destroy();
            };


        self.init();
        return self.popup;
    };


})( jQuery, window, document );
