/**
 * jquery.flipshow.js v1.0.0
 * http://www.codrops.com
 *
 * Licensed under the MIT license.
 * http://www.opensource.org/licenses/mit-license.php
 *
 * Copyright 2013, Codrops
 * http://www.codrops.com
 */
;( function( $, window, undefined ) {

    'use strict';

    // ======================= imagesLoaded Plugin ===============================
    // https://github.com/desandro/imagesloaded

    // $('#my-container').imagesLoaded(myFunction)
    // execute a callback when all images have loaded.
    // needed because .load() doesn't work on cached images

    // callback function gets image collection as argument
    //  this is the container

    // original: mit license. paul irish. 2010.
    // contributors: Oren Solomianik, David DeSandro, Yiannis Chatzikonstantinou

    // blank image data-uri bypasses webkit log warning (thx doug jones)
    // blank image data-uri bypasses webkit log warning (thx doug jones)
    var BLANK = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==';

    // global
    var Modernizr = window.Modernizr;

    $.Flipshow = function( options, element ) {
        this.$el = $( element );
        this._init( options );
    };

    // the options
    $.Flipshow.defaults = {
        // default transition speed (ms)
        speed : 700,
        // default transition easing
        easing : 'ease-out'
    };

    $.Flipshow.prototype = {
        _init : function( options ) {
            if ( typeof Modernizr == 'undefined' ) {
                Modernizr = window.Modernizr;
            }
            // options
            this.options = $.extend( true, {}, $.Flipshow.defaults, options );
            // support for CSS Transitions & 3D transforms
            this.support = Modernizr.csstransitions && Modernizr.csstransforms3d && !(/MSIE (\d+\.\d+);/.test(navigator.userAgent));
            // transition end event name and transform name
            var transEndEventNames = {
                    'WebkitTransition' : 'webkitTransitionEnd',
                    'MozTransition' : 'transitionend',
                    'OTransition' : 'oTransitionEnd',
                    'msTransition' : 'MSTransitionEnd',
                    'transition' : 'transitionend'
                },
                transformNames = {
                    'WebkitTransform' : '-webkit-transform',
                    'MozTransform' : '-moz-transform',
                    'OTransform' : '-o-transform',
                    'msTransform' : '-ms-transform',
                    'transform' : 'transform'
                };

            if( this.support ) {
                this.transEndEventName = transEndEventNames[ Modernizr.prefixed( 'transition' ) ] + '.cbpFWSlider';
                this.transformName = transformNames[ Modernizr.prefixed( 'transform' ) ];
            }
            this.transitionProperties = this.transformName + ' ' + this.options.speed + 'ms ' + this.options.easing;

            // the list of items
            this.$listItems = this.$el.children( 'ul.fc-slides' );
            // the items
            this.$items = this.$listItems.children( 'li' ).hide();
            // total number of items
            this.itemsCount = this.$items.length;
            // current item´s index
            this.current = 0;
            this.$listItems.imagesLoaded( $.proxy( function() {
                // show first item
                this.$items.eq( this.current ).show();
                // add navigation and flipping structure
                if( this.itemsCount > 0 ) {
                    this._addNav();
                    if( this.support ) {
                        this._layout();
                    }
                }
            }, this ) );

        },
        _addNav : function() {

            var self = this,
                $navLeft = $( '<div class="fc-left"><span></span><span></span><span></span><i class="fas fa-arrow-left"></i></div>' ),
                $navRight = $( '<div class="fc-right"><span></span><span></span><span></span><i class="fas fa-arrow-right"></i></div>' );

            $( '<nav></nav>' ).append( $navLeft, $navRight ).appendTo( this.$el );

            $navLeft.find( 'span' ).on( 'click.flipshow touchstart.flipshow', function() {
                self._navigate( $( this ), 'left' );
            } );

            $navRight.find( 'span' ).on( 'click.flipshow touchstart.flipshow', function() {
                self._navigate( $( this ), 'right' );
            } );

        },
        _layout : function( $current, $next ) {

            this.$flipFront = $( '<div class="fc-front"><div></div></div>' );
            this.$frontContent = this.$flipFront.children( 'div:first' );
            this.$flipBack = $( '<div class="fc-back"><div></div></div>' );
            this.$backContent = this.$flipBack.children( 'div:first' );
            this.$flipEl = $( '<div class="fc-flip"></div>' ).append( this.$flipFront, this.$flipBack ).hide().appendTo( this.$el );

        },
        _navigate : function( $nav, dir ) {

            if( this.isAnimating && this.support ) {
                return false;
            }
            this.isAnimating = true;

            var $currentItem = this.$items.eq( this.current ).hide();

            if( dir === 'right' ) {
                this.current < this.itemsCount - 1 ? ++this.current : this.current = 0;
            }
            else if( dir === 'left' ) {
                this.current > 0 ? --this.current : this.current = this.itemsCount - 1;
            }

            var $nextItem = this.$items.eq( this.current );

            if( this.support ) {
                this._flip( $currentItem, $nextItem, dir, $nav.index() );
            }
            else {
                $nextItem.show();
            }

        },
        _flip : function( $currentItem, $nextItem, dir, angle ) {

            var transformProperties = '',
                // overlays
                $overlayLight = $( '<div class="fc-overlay-light"></div>' ),
                $overlayDark = $( '<div class="fc-overlay-dark"></div>' );

            if (typeof this.$flipEl == 'undefined')
                return;

            this.$flipEl.css( 'transition', this.transitionProperties );

            this.$flipFront.find( 'div.fc-overlay-light, div.fc-overlay-dark' ).remove();
            this.$flipBack.find( 'div.fc-overlay-light, div.fc-overlay-dark' ).remove();

            if( dir === 'right' ) {
                this.$flipFront.append( $overlayLight );
                this.$flipBack.append( $overlayDark );
                $overlayDark.css( 'opacity', 1 );
            }
            else if( dir === 'left' ) {
                this.$flipFront.append( $overlayDark );
                this.$flipBack.append( $overlayLight );
                $overlayLight.css( 'opacity', 1 );
            }
            var overlayStyle = { transition : 'opacity ' + ( this.options.speed / 1.3 ) + 'ms' };
            $overlayLight.css( overlayStyle );
            $overlayDark.css( overlayStyle );

            switch( angle ) {
                case 0 :
                    transformProperties = dir === 'left' ? 'rotate3d(-1,1,0,-179deg) rotate3d(-1,1,0,-1deg)' : 'rotate3d(1,1,0,180deg)';
                    break;
                case 1 :
                    transformProperties = dir === 'left' ? 'rotate3d(0,1,0,-179deg) rotate3d(0,1,0,-1deg)' : 'rotate3d(0,1,0,180deg)';
                    break;
                case 2 :
                    transformProperties = dir === 'left' ? 'rotate3d(1,1,0,-179deg) rotate3d(1,1,0,-1deg)' : 'rotate3d(-1,1,0,179deg) rotate3d(-1,1,0,1deg)';
                    break;
            }

            this.$flipBack.css( 'transform', transformProperties );

            this.$frontContent.empty().html( $currentItem.html() );
            this.$backContent.empty().html( $nextItem.html() );
            this.$flipEl.show();

            var self = this;
            setTimeout( function() {

                self.$flipEl.css( 'transform', transformProperties );
                $overlayLight.css( 'opacity', dir === 'right' ? 1 : 0 );
                $overlayDark.css( 'opacity', dir === 'right' ? 0 : 1 );
                self.$flipEl.on( self.transEndEventName, function( event ) {
                    if( event.target.className === 'fc-overlay-light' || event.target.className === 'fc-overlay-dark' ) return;
                    self._ontransitionend( $nextItem );
                } );

            }, 25 );

        },
        _ontransitionend : function( $nextItem ) {
            $nextItem.show();
            this.$flipEl.off( this.transEndEventName ).css( {
                transition : 'none',
                transform : 'none'
            } ).hide();
            this.isAnimating = false;
        }
    };

    var logError = function( message ) {
        if ( window.console ) {
            window.console.error( message );
        }
    };

    $.fn.flipshow = function( options ) {
        if ( typeof options === 'string' ) {
            var args = Array.prototype.slice.call( arguments, 1 );
            this.each(function() {
                var instance = $.data( this, 'flipshow' );
                if ( !instance ) {
                    logError( "cannot call methods on flipshow prior to initialization; " +
                    "attempted to call method '" + options + "'" );
                    return;
                }
                if ( typeof instance[options] !== 'function' || options.charAt(0) === "_" ) {
                    logError( "no such method '" + options + "' for flipshow instance" );
                    return;
                }
                instance[ options ].apply( instance, args );
            });
        }
        else {
            this.each(function() {
                var instance = $.data( this, 'flipshow' );
                if ( instance ) {
                    instance._init();
                }
                else {
                    instance = $.data( this, 'flipshow', new $.Flipshow( options, this ) );
                }
            });
        }
        return this;
    };

} )( jQuery, window );