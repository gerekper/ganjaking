( function ( $, window, document ) {
    $.fn.yith_wcpb_popup = function ( options ) {
        var overlay = null;

        self.popup = $( this );
        self.opts = {};

        var defaults = {
            popup_class    : 'yith-wcpb-popup',
            overlay_class  : 'yith-wcpb-overlay',
            close_btn_class: 'yith-wcpb-popup-close',
            position       : 'center',
            popup_delay    : 0,
            ajax           : false,
            ajax_container : 'yith-wcpb-popup-ajax-container',
            url            : '',
            ajax_data      : {},
            ajax_complete  : function () {
            },
            ajax_success   : function () {
            },
            popup_css      : {},
            block_params   : {
                message        : '',
                blockMsgClass  : 'yith-wcpb-popup-loader',
                css            : {
                    border    : 'none',
                    background: 'transparent'
                },
                overlayCSS     : {
                    background: '#fff',
                    opacity   : 0.7
                },
                ignoreIfBlocked: true
            },
            loader         : '<span class="yith-wcpb-popup-loader"></span>'
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
                _setStartingPopupPosition();
            } else {
                self.popup = self.popup.clone();

                self.popup.css( self.opts.popup_css ).addClass( 'yith-wcpb-popup-opened' );
                $( document.body ).append( self.popup );
                _setStartingPopupPosition();
            }

            _initEvents();
            _show();
        };

        var _createOverlay            = function () {
                // add_overlay if not exist
                if ( $( document ).find( '.' + self.opts.overlay_class ).length > 0 ) {
                    overlay = $( document ).find( '.' + self.opts.overlay_class ).first();
                } else {
                    overlay = $( '<div />' ).addClass( self.opts.overlay_class );
                    $( document.body ).append( overlay );
                }
            },
            _getAjaxContent           = function () {
                self.popup = $( '<div />' ).addClass( self.opts.popup_class );
                var closeBtn       = $( '<span />' ).addClass( self.opts.close_btn_class + ' dashicons dashicons-no-alt' ),
                    popupContainer = $( '<div />' ).addClass( self.opts.ajax_container );
                popupContainer.html( self.opts.loader );
                self.popup.append( popupContainer );

                $.ajax( {
                            data    : self.opts.ajax_data,
                            url     : self.opts.url,
                            success : function ( data ) {
                                self.popup.find( '.' + self.opts.ajax_container ).html( data );
                                //self.popup.unblock();
                                _resize();
                                self.opts.ajax_success();
                            },
                            complete: function () {
                                self.popup.append( closeBtn.hide().delay( 500 ).fadeIn() );

                                self.opts.ajax_complete();
                            }
                        } );

                $( document.body ).append( popup );

            },
            _setStartingPopupPosition = function () {
                self.popup.css( {
                                    position: 'fixed',
                                    top     : "45%",
                                    left    : "45%",
                                    width   : "5%",
                                    height  : "5%"
                                } );
            },
            _initEvents               = function () {
                $( document ).on( 'click', '.' + self.opts.overlay_class, _close );
                self.popup.on( 'click', '.' + self.opts.close_btn_class, _close );

                $( document ).on( 'keydown', function ( event ) {
                    if ( event.keyCode === 27 ) {
                        _close();
                    }
                } );

            },
            _show                     = function () {
                overlay.fadeIn( 'fast' );

                self.popup.fadeIn( 'fast', function () {
                    if ( !self.opts.ajax ) {
                        _resize();
                    }
                } );
            },
            _resize                   = function () {
                self.popup.children().hide();
                self.popup.show();

                self.popup.animate( {
                                        opacity: 1,
                                        top    : "20%",
                                        left   : "15%",
                                        width  : "70%",
                                        height : "70%",
                                        easing : 'easeOutBounce'
                                    }, {
                                        duration: 300,
                                        complete: function () {
                                            self.popup.children().fadeIn( 'fast' );
                                        }
                                    } );
            },
            _destroy                  = function () {
                self.popup.remove();
            },
            _close                    = function () {
                $( document ).find( '.' + self.opts.overlay_class ).hide();
                self.popup.hide();
                _destroy();
            };


        self.init();
        return self.popup;
    };

} )( jQuery, window, document );