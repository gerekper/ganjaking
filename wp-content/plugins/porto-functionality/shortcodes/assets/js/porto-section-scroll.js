// Section Scroll
(function(theme, $) {
    'use strict';

    theme = theme || {};

    var instanceName = '__sectionScroll';

    var PluginSectionScroll = function($el, opts) {
        return this.initialize($el, opts);
    };

    PluginSectionScroll.defaults = {
        targetClass: '.section-scroll',
        dotsNav: true
    };

    PluginSectionScroll.prototype = {
        initialize: function($el, opts) {
            if ($el.data(instanceName)) {
                return this;
            }

            this.$el = $el;

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
            this.options = $.extend(true, {}, PluginSectionScroll.defaults, opts, {
                wrapper: this.$el
            });

            return this;
        },

        getEventsPage: function(e) {
            var events = [];

            events.y = (typeof e.pageY !== 'undefined' && (e.pageY || e.pageX) ? e.pageY : e.touches[0].pageY);
            events.x = (typeof e.pageX !== 'undefined' && (e.pageY || e.pageX) ? e.pageX : e.touches[0].pageX);

            if ( ('ontouchstart' in window || navigator.msMaxTouchPoints) && ( typeof e.pointerType === 'undefined' || e.pointerType != 'mouse' ) && typeof e.touches !== 'undefined') {
                events.y = e.touches[0].pageY;
                events.x = e.touches[0].pageX;
            }

            return events;
        },

        build: function() {
            if (!this.options.wrapper.length) {
                return;
            }
            var self = this,
                el_obj = this.options.wrapper.get(0);

            // Check type of header and change the target for header (by change header color purpose)
            self.$header = $('#header');

            $('html').addClass('overflow-hidden');

            // Turn the section full height or not depeding on the content size
            self.updateSectionsHeight();

            // Wrap all sections in a section wrapper
            $( this.options.targetClass ).wrap('<div class="section-wrapper"></div>');

            // Set the section wrapper height
            $('.section-wrapper').each(function(){
                $(this).height( $(this).find('.section-scroll').outerHeight() );
            });

            // Add active class to the first section on page load
            $('.section-wrapper').first().addClass('active');
            
            var flag = false,
                scrollableFlag = false,
                touchDirection = '',
                touchstartY = 0,
                touchendY = 0,
                checkTimer;

            var wheelEvent;
            if ('ontouchstart' in window || navigator.msMaxTouchPoints) {
                el_obj.addEventListener('touchstart', function(e) {
                    touchstartY = self.getEventsPage(e).y;
                });
                wheelEvent = 'onwheel' in document ? 'wheel touchmove' : document.onmousewheel !== undefined ? 'mousewheel touchmove' : 'DOMMouseScroll touchmove';
            } else {
                wheelEvent = 'onwheel' in document ? 'wheel pointermove' : document.onmousewheel !== undefined ? 'mousewheel pointermove' : 'DOMMouseScroll pointermove';
                el_obj.addEventListener('pointerdown', function(e) {
                    if (typeof e.pointerType === 'undefined' || 'mouse' != e.pointerType) {
                        touchstartY = self.getEventsPage(e).y;
                    }
                });
            }

            if( window.innerWidth < 992 ) {
                $('html').removeClass('overflow-hidden');
                $(window).on('scroll', function(){

                    var index = 0;
                    $('.section-wrapper').each(function(){
                        if( $(this).offset().top <= $(window).scrollTop() + 50 ) {
                            $('.section-scroll-dots-navigation > ul > li').removeClass('active');
                            $('.section-scroll-dots-navigation > ul > li').eq( index ).addClass('active');

                            self.$header.trigger( 'porto_section_scroll_scrolled', [index] );
                        }

                        index++;
                    });
                    
                });
            }

            var process_scroll = function(e) {
                if ($('.porto-popup-menu.opened').length) {
                    return;
                }
                if ( 'mouse' == e.pointerType ) {
                    return;
                }
                if ( window.innerWidth < 992 ) {
                    return;
                }
                var wheelDirection = null;
                touchDirection = '';
                if ( e.type && ( 'touchmove' == e.type || ( e.pointerType && e.pointerType != 'mouse' ) ) ) {
                    touchendY = self.getEventsPage(e).y;
                    if (touchstartY == touchendY) {
                        return;
                    }
                    //if (Math.abs(touchstartY - touchendY) > (window.innerHeight / 100 * 5)) {
                        if (touchstartY > touchendY) {
                            touchDirection = 'up';
                        } else if (touchendY > touchstartY) {
                            touchDirection = 'down';
                        }
                    //}
                } else {
                    wheelDirection = e.wheelDelta == undefined ? e.deltaY > 0 : e.wheelDelta < 0;
                }

                var $currentSection = $('.section-wrapper').eq( self.getCurrentIndex() ).find('.section-scroll'),
                    $nextSection = self.getNextSection(wheelDirection, touchDirection),
                    nextSectionOffsetTop;

                // If is the last section, then change the offsetTop value
                if( self.getCurrentIndex() == $('.section-wrapper').length - 1 ) {
                    nextSectionOffsetTop = $(document).height();
                } else {
                    nextSectionOffsetTop = $nextSection.offset().top;
                }

                if ( touchDirection ) {
                    if ( checkTimer ) {
                        clearTimeout( checkTimer );
                    }
                    checkTimer = setTimeout(function(){
                        if( $('.section-wrapper').eq( self.getCurrentIndex() ).find('.section-scroll').hasClass('section-scroll-scrollable') ) {
                            $('html').removeClass('overflow-hidden');
                        } else {
                            $('html').addClass('overflow-hidden');
                        }
                    }, 1200);
                }

                // For non full height sections
                if( $currentSection.hasClass('section-scroll-scrollable') ) {
                    
                    if( !flag && !scrollableFlag ) {

                        if(wheelDirection || touchDirection == 'up') {
                            if($nextSection.length && ( $(window).scrollTop() + window.innerHeight ) >= nextSectionOffsetTop ) {
                                flag = true;
                                setTimeout(function(){
                                    setTimeout(function(){
                                        flag = false;
                                    }, 500);
                                }, 1000);

                                if( self.getCurrentIndex() == ( $('.section-wrapper').length - 1 )  ) {
                                    return false;
                                }

                                // Move to the next section
                                self.moveTo( $currentSection.offset().top + $currentSection.outerHeight() );

                                // Change Section Active Class
                                self.changeSectionActiveState( $nextSection );

                                self.$header.css({
                                    opacity: 0,
                                    transition: 'ease opacity 500ms'
                                });
                            }

                            if( !touchDirection ) {
                                for( var i = 1; i < 100; i++ ) {
                                    $('body, html').scrollTop( $(window).scrollTop() + 1 );

                                    if( ( $(window).scrollTop() + window.innerHeight ) >= nextSectionOffsetTop ) {
                                        scrollableFlag = true;
                                        setTimeout(function(){
                                            scrollableFlag = false;
                                        }, 500);
                                        break;
                                    }
                                }
                            }
                        } else {
                            if( $(window).scrollTop() <= $currentSection.offset().top ) {
                                flag = true;
                                setTimeout(function(){
                                    setTimeout(function(){
                                        flag = false;
                                    }, 500);
                                }, 1000);

                                if( self.getCurrentIndex() == 0  ) {
                                    return false;
                                }

                                // Move to the next section
                                self.moveTo( $currentSection.offset().top - window.innerHeight );

                                // Change Section Active Class
                                self.changeSectionActiveState( $nextSection );

                                self.$header.css({
                                    opacity: 0,
                                    transition: 'ease opacity 500ms'
                                });
                            }

                            if( !touchDirection ) {
                                for( var i = 1; i < 100; i++ ) {
                                    $('body, html').scrollTop( $(window).scrollTop() - 1 );

                                    if( $(window).scrollTop() <= $currentSection.offset().top ) {
                                        scrollableFlag = true;
                                        setTimeout(function(){
                                            scrollableFlag = false;
                                        }, 500);
                                        break;
                                    }
                                }
                            }
                        }

                        // Change Dots Active Class
                        self.changeDotsActiveState();

                        return;

                    }
                }

                if (touchDirection && Math.abs(touchstartY - touchendY) <= (window.innerHeight / 100 * 2)) {
                    return;
                }

                // For full height sections
                if( !flag && !scrollableFlag ) {

                    if(wheelDirection || touchDirection == 'up') {
                        if( self.getCurrentIndex() == ( $('.section-wrapper').length - 1 )  ) {
                            return;//return false;
                        }

                        // Change Section Active Class
                        self.changeSectionActiveState( $nextSection );

                        setTimeout(function(){
                            // Move to the next section
                            self.moveTo( $nextSection.offset().top );

                        }, 150);
                    } else {
                        if( self.getCurrentIndex() == 0  ) {
                            return;//return false;
                        }

                        // Change Section Active Class
                        self.changeSectionActiveState( $nextSection );

                        if( $nextSection.height() > window.innerHeight ) {
                            // Move to the next section
                            self.moveTo( $currentSection.offset().top - window.innerHeight );
                        } else {
                            setTimeout(function(){
                                // Move to the next section
                                self.moveTo( $nextSection.offset().top );

                            }, 150);
                        }
                    }

                    // Change Dots Active Class
                    self.changeDotsActiveState();

                    self.$header.css({
                        opacity: 0,
                        transition: 'ease opacity 500ms'
                    });

                    // Style next section
                    $nextSection.css({
                        position: 'relative',
                        opacity: 1,
                        'z-index': 1,
                        transform: 'translate3d(0,0,0) scale(1)'
                    });

                    // Style previous section
                    $currentSection.css({
                        position: 'fixed',
                        width: '100%',
                        top: 0,
                        left: 0,
                        opacity: 0,
                        'z-index': 0,
                        transform: 'translate3d(0,0,-10px) scale(0.7)',
                        transition: 'ease transform 600ms, ease opacity 600ms',
                    });
                    var offsetMargin = parseInt($currentSection.css('marginLeft'), 10) + parseInt($currentSection.css('marginRight'), 10);
                    if (offsetMargin < 0) {
                        $currentSection.css('width', 'calc(100% + ' + offsetMargin * -1 + 'px)');
                    }

                    setTimeout(function(){
                        $currentSection.css({
                            position: 'relative',
                            opacity: 1,
                            transform: 'translate3d(0,0,-10px) scale(1)'
                        });

                        self.$header.css({
                            opacity: 1
                        });

                        self.$header.trigger('porto_section_scroll_scrolled', [self.getCurrentIndex()]);

                        setTimeout(function(){
                            flag = false;
                        }, 500);
                    }, 1000);

                    flag = true;

                }
            };

            wheelEvent.split(' ').forEach(function(eventName) {
                el_obj.addEventListener(eventName, process_scroll);
            });
            // Dots Navigation
            if( this.options.dotsNav ) {
                self.dotsNavigation();
            }

            // First Load
            setTimeout(function(){
                if( $(window.location.hash).get(0) ) {
                    self.moveTo( $(window.location.hash).parent().offset().top );

                    self.changeSectionActiveState( $(window.location.hash) );

                    // Change Dots Active Class
                    self.changeDotsActiveState();

                    self.updateHash( true );
                } else {
                    var hash  = window.location.hash,
                        index = hash.replace('#','');

                    if( !hash ) {
                        index = 1;
                    }

                    self.moveTo( $('.section-wrapper').eq( index - 1 ).offset().top );

                    self.changeSectionActiveState( $('.section-wrapper').eq( index - 1 ).find('.section-scroll') );

                    // Change Dots Active Class
                    self.changeDotsActiveState();

                    self.updateHash( true );
                }

                if( $('.section-wrapper').eq( self.getCurrentIndex() ).find('.section-scroll').hasClass('section-scroll-scrollable') ) {
                    $('html').removeClass('overflow-hidden');
                }

                $(window).trigger('section.scroll.ready');

                self.$header.trigger('porto_section_scroll_scrolled', [self.getCurrentIndex()]);
            }, 500);

            return this;
        },

        updateSectionsHeight: function() {
            var self = this;

            $('.section-scroll').each(function() {
                $(this).css( 'height', '' );
                if( $(this).outerHeight() < ( window.innerHeight + 3 ) ) {
                    $(this).css({ height: '100vh' });
                } else {
                    $(this).addClass('section-scroll-scrollable');
                }
            });

            // Set the section wrapper height
            $('.section-wrapper').each(function(){
                $(this).height( $(this).find('.section-scroll').outerHeight() );
            });

            return this;
        },

        updateHash: function( first_load ){
            var self = this;

            if( !window.location.hash ) {
                window.location.hash = 1;
            } else {
                if(!first_load) {
                    var section_id = self.getCurrentIndex() + 1;

                    window.location.hash = section_id;
                }
            }

            return this;
        },

        getCurrentIndex: function() {
            var self = this,
                currentIndex = 0;

            $('.section-wrapper').each(function(index) {
                if ($(this).hasClass('active')) {
                    currentIndex = index;
                    return currentIndex;
                }
            });

            return currentIndex;
        },

        moveTo: function( $scrollTopValue, first_load ) {
            var self = this;

            $('body, html').animate({
                scrollTop: $scrollTopValue
            }, 1000, 'easeOutQuint');

            setTimeout(function(){
                self.updateHash();
            }, 500);

            return this;
        },

        getNextSection: function(wheelDirection, touchDirection) {
            var self = this,
                $nextSection = '';

            // Scroll Direction
            if(wheelDirection || touchDirection == 'up') {
                $nextSection = $('.section-wrapper').eq( self.getCurrentIndex() + 1 ).children().eq(0);
            } else {
                $nextSection = $('.section-wrapper').eq( self.getCurrentIndex() - 1 ).children().eq(0);
            }

            return $nextSection;
        },

        changeSectionActiveState: function( $nextSection ) {
            var self = this;

            $('.section-wrapper').removeClass('active');
            $nextSection.parent().addClass('active');

            return this;
        },

        changeDotsActiveState: function() {
            var self = this;

            $('.section-scroll-dots-navigation > ul > li').removeClass('active');
            $('.section-scroll-dots-navigation > ul > li').eq( self.getCurrentIndex() ).addClass('active');

            return this;
        },

        dotsNavigation: function() {
            var self = this;

            var dotsNav = $('<div class="section-scroll-dots-navigation"><ul class="list list-unstyled"></ul></div>'),
                currentSectionIndex = self.getCurrentIndex();

            if( self.options.dotsClass ) {
                dotsNav.addClass( self.options.dotsClass );
            }

            for( var i = 0; i < $('.section-scroll').length; i++ ) {
                var title = $('.section-wrapper').eq( i ).find('.section-scroll').data('section-scroll-title');

                dotsNav.find('> ul').append( '<li'+ ( ( currentSectionIndex == i ) ? ' class="active"' : '' ) +'><a href="#'+ i +'" data-nav-id="'+ i +'"><span>'+ title +'</span></a></li>' );
            }

            $('.page-wrapper').append( dotsNav );

            dotsNav.find('a[data-nav-id]').on('click touchstart', function(e){
                e.preventDefault();
                var $this = $(this);

                $('.section-scroll').css({
                    opacity: 0,
                    transition: 'ease opacity 300ms'
                });

                self.$header.css({
                    opacity: 0,
                    transition: 'ease opacity 500ms'
                });

                setTimeout(function(){
                    self.moveTo( $('.section-wrapper').eq( $this.data('nav-id') ).offset().top )

                    $('.section-wrapper').removeClass('active');
                    $('.section-wrapper').eq( $this.data('nav-id') ).addClass('active');

                    $('.section-wrapper').eq( self.getCurrentIndex() ).find('.section-scroll').css({
                        opacity: 1
                    });

                    setTimeout(function(){
                        $('.section-scroll').css({ opacity: 1 });

                        self.$header.css({
                            opacity: 1
                        });

                        self.$header.trigger('porto_section_scroll_scrolled', [self.getCurrentIndex()]);
                    }, 500);

                    self.changeDotsActiveState();
                }, 500);
            });

            return this;
        },

        events: function() {
            var self = this;

            $(window).on('section.scroll.ready', function(){
                $(window).scrollTop(0);
            });

            $(document).ready(function(){
                var resizeTrigger = null;
                $(window).smartresize(function() {
                    if ( resizeTrigger ) {
                        clearTimeout( resizeTrigger );
                    }
                    resizeTrigger = setTimeout(function() {
                        self.updateSectionsHeight();
                    }, 300);

                    if ( window.innerWidth < 992 ) {
                        $('html').removeClass('overflow-hidden');
                    }
                });
            });

            return this;
        }
    };

    // expose to scope
    $.extend(theme, {
        PluginSectionScroll: PluginSectionScroll
    });

    // jquery plugin
    $.fn.themePluginSectionScroll = function(opts) {
        return this.map(function() {
            var $this = $(this);

            if ($this.data(instanceName)) {
                return $this.data(instanceName);
            } else {
                return new PluginSectionScroll($this, opts);
            }

        });
    };

}).apply(this, [window.theme, jQuery]);