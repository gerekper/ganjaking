( function ( $, window, document ) {
    $.fn.yith_wcet_popup = function ( options ) {
        var overlay = null;

        self.popup = $( this );
        self.opts = {};

        var defaults = {
            popup_class        : 'yith-wcet-popup',
            overlay_class      : 'yith-wcet-overlay',
            close_btn_class    : 'yith-wcet-popup-close',
            position           : 'center',
            popup_delay        : 0,
            ajax               : false,
            ajax_container     : 'yith-wcet-popup-ajax-container',
            url                : '',
            ajax_data          : {},
            ajax_complete      : function () {
            },
            ajax_success       : function () {
            },
            popup_css          : {},
            responsive_controls: false,
            loader             : '<span class="yith-wcet-popup-loader dashicons dashicons-image-filter"></span>'
        };

        self.init = function () {

            self.opts = $.extend( {}, defaults, options );
            if ( options === 'close' ) {
                _close();
                return;
            }

            if ( self.opts.responsive_controls ) {
                self.opts.popup_class += ' responsive-controls-enabled';
            }

            _createOverlay();
            if ( self.opts.ajax == true ) {
                _getAjaxContent();
                _setStartingPopupPosition();
            } else {
                self.popup = self.popup.clone();

                self.popup.css( self.opts.popup_css ).addClass( 'yith-wcet-popup-opened' );
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

                var responsive_controls, rc_desktop, rc_tablet, rc_mobile;
                if ( self.opts.responsive_controls ) {
                    responsive_controls = $( '<div class="responsive-controls-wrapper"></div>' );
                    rc_desktop = $( '<button class="preview-desktop active"></button>' );
                    rc_tablet = $( '<button class="preview-tablet"></button>' );
                    rc_mobile = $( '<button class="preview-mobile"></button>' );

                    responsive_controls.append( rc_desktop );
                    responsive_controls.append( rc_tablet );
                    responsive_controls.append( rc_mobile );
                }

                var closeBtn       = $( '<span />' ).addClass( self.opts.close_btn_class + ' dashicons dashicons-no-alt' ),
                    popupContainer = $( '<div />' ).addClass( self.opts.ajax_container );
                popupContainer.html( self.opts.loader );
                self.popup.append( popupContainer );

                $.ajax( {
                            method  : 'POST',
                            data    : self.opts.ajax_data,
                            url     : self.opts.url,
                            success : function ( data ) {
                                self.popup.find( '.' + self.opts.ajax_container ).html( data );
                                //_setPopupPosition( 'big-center' );
                                _resize();
                                self.opts.ajax_success();
                            },
                            complete: function () {
                                self.popup.append( closeBtn.hide().delay( 500 ).fadeIn() );

                                if ( self.opts.responsive_controls ) {
                                    self.popup.append( responsive_controls.hide().delay( 500 ).fadeIn() );
                                }

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
                self.popup.on( 'click', '.responsive-controls-wrapper button', _setResponsiveClass );

                $( document ).on( 'keydown', function ( event ) {
                    if ( event.keyCode === 27 ) {
                        _close();
                    }
                } );

            },
            _setResponsiveClass       = function ( e ) {
                var target = $( e.target );
                self.popup.find( '.responsive-controls-wrapper button' ).removeClass( 'active' );
                target.addClass( 'active' );

                self.popup.removeClass( 'preview-desktop' );
                self.popup.removeClass( 'preview-tablet' );
                self.popup.removeClass( 'preview-mobile' );
                if ( target.is( '.preview-desktop' ) ) {
                    self.popup.addClass( 'preview-desktop' );
                } else if ( target.is( '.preview-tablet' ) ) {
                    self.popup.addClass( 'preview-tablet' );
                } else if ( target.is( '.preview-mobile' ) ) {
                    self.popup.addClass( 'preview-mobile' );
                }
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

                self.popup.css( {
                                    opacity: 1,
                                    width  : "90%",
                                    height : "90%",
                                } );

                setTimeout( function () {
                    self.popup.children().fadeIn( 'fast' );
                }, 500 );

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