/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
(function($, window, document) {
    "use strict";

    $.yit_popup = function(element, options) {

        var defaults = {
            'popup_class' : 'ypop',
            'content' : '',
            'delay' : 0,
            'position': 'center',
            'mobile' : false,
            'destroy_on_close' :true
        };

        var self = this;

        self.settings = {};

        var $element = $(element),
            overlay = null,
            popup = null,
            close = null;

        self.init = function() {
            self.settings = $.extend({}, defaults, options);

                _createElements();
                _initEvents();


        };

        var _initEvents = function() {
                $(document).on('touchstart click', '.ypop-overlay', function(e){
                    if( $( e.target).hasClass('close') || $( e.target ).parents( '.ypop-overlay' ).length == 0 ) {
                        _close();
                    }
                }).on('keyup', function(e) {
                        if (e.keyCode == 27) {
                            _close();
                        }
                    }).on('click', '.ypop-wrapper a.close', function () {
                        _close();
                    });

                $(window).on('resize', function(){
                    _center();
                });

                $('html').removeClass('yit-opened');

                _open();
            },

            _createElements = function() {
				if( $('body').find('.ypop-modal').length == 0 ) {
					self.bigwrapper = $('<div />', {
						'class' : 'ypop-modal'
					}).prependTo('body');
				} else {
					self.bigwrapper = $('body').find('.ypop-modal');
				}

				if( self.bigwrapper.find('.ypop-overlay').length == 0 ) {
                    self.overlay = $('<div />', {
                        'class' : 'ypop-overlay'
                    }).appendTo(self.bigwrapper);
                } else {
                    self.overlay = self.bigwrapper.find('.ypop-overlay');
                }

                if( self.bigwrapper.find('.ypop-wrapper').length == 0 ) {
                    self.popup = $('<div />', {
                        'class' : 'ypop-wrapper ' + self.settings.popup_class
                    }).appendTo( self.bigwrapper );
                } else {
                    self.popup = self.bigwrapper.find('.ypop-wrapper');
                }

                if( self.popup.find('.close').length == 0 ) {
                    self.close = $('<a />', {
                        'class' : 'close'
                    }).appendTo( self.popup );
                } else {
                    self.close = self.popup.find('.close');
                }
            },

            _center = function() {

                var w = self.popup.outerWidth(),
                    h = self.popup.outerHeight();

                if( self.settings.mobile ){
                    if ( self.popup.outerHeight() > ( jQuery(window).height()  - 120 ) ||self.popup.outerWidth() > ( jQuery(window).width()  - 60 ) ){
                         w = jQuery(window).width()  - 60;
                    }

                    self.popup.css({
                        width: w + "px",
                        height: 'auto'
                    });
                }
                if( self.settings.position == 'center'){
                    self.popup.css({
                        position: 'fixed',
                        top: Math.max(0, ((jQuery(window).height() - self.popup.outerHeight()) / 2) - 50 ) + "px",//'15%',
                        left: Math.max(0, ((jQuery(window).width() - w) / 2) ) + "px"
                    });
                }else if( self.settings.position == 'left-top' ){
                    self.popup.css({
                        position: 'fixed',
                        top:  " 0px",//'15%',
                        left: "0px"
                    });
                }else if( self.settings.position == 'left-bottom' ){
                    self.popup.css({
                        position: 'fixed',
                        bottom:  " 0px",//'15%',
                        left: "0px"
                    });
                }else if( self.settings.position == 'right-bottom' ){
                    self.popup.css({
                        position: 'fixed',
                        bottom:  " 0px",//'15%',
                        right: "0px"
                    });
                }else if( self.settings.position == 'right-top' ){
                    self.popup.css({
                        position: 'fixed',
                        top:  " 0px",//'15%',
                        right: "0px"
                    });
                }
            },

            _open = function() {
                if( ( self.settings.delay )*1 > 0){
                   setTimeout( function(){
                       _content();
                       $('.ypop-modal').addClass('open');
                       _center();
                   }, self.settings.delay) ;
                }else{

                    _content();
                    $('.ypop-modal').addClass('open');
                    _center();
                }


                //_center();
              //  self.overlay.css({ 'display': 'block', opacity: 0 }).animate({ opacity: 1 }, 500);
              //  $('html').addClass('yit-opened');

            },

            _close = function() {
                //self.overlay.css({ 'display': 'none', opacity: 1 }).animate({ opacity: 0 }, 500);
                $element.trigger('close.ypop');
                //$('html').removeClass('yit-opened');
				$('.ypop-modal').removeClass('open');
                $('.ypop-modal').data('is_closed', 'yes' );
                if( self.settings.destroy_on_close ){
                   _destroy();
                }
            },

            _destroy = function() {
                self.popup.remove();
                self.overlay.remove();

                //self.popup = self.overlay = null;
                $element.removeData('yit_popup');
            },

            _content = function() {
              
                if( self.settings.content != '' ) {
                    self.popup.html( self.settings.content );
                } else if( $element.data('container') ) {
                    self.popup.html( $($element.data('container')).html() );
                } else if( $element.data('content') ) {
                    self.popup.html( $element.data('content') );
                } else if( $element.attr('title') ) {
                    self.popup.html( $element.attr('title') );
                } else {
                    self.popup.html('');
                }

                //update <input id="" /> and <label for="">
                self.popup.find('form, input, label, a').each(function(){
                    if( typeof $(this).attr('id') != 'undefined' ) {
                        var id = $(this).attr('id') + '_ypop';
                        $(this).attr('id', id);
                    }

                    if( typeof $(this).attr('for') != 'undefined' ) {
                        var id = $(this).attr('for') + '_ypop';
                        $(this).attr('for', id);
                    }
                });

                if( self.overlay.find('.close').length == 0 ) {
                    self.close = $('<a />', {
                        'class' : 'close'
                    }).appendTo( self.popup );
                } else {
                    self.close = self.overlay.find('.close');
                }
            };


        if( options.open_on_click ) {
            _open();
        }

        self.init();
    };

    $.fn.yit_popup = function(options) {

        return this.each(function() {
            if (undefined === $(this).data('yit_popup')) {
                var yit_popup = new $.yit_popup(this, options);
                $(this).data('yit_popup', yit_popup);
            }
        });

    };

})(jQuery, window, document);
