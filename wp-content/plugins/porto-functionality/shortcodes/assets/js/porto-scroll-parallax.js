// Parallax
(function($) {

    var instanceName = '__scroll_parallax';

    var PluginScrollParallax = function($el, opts) {
        return this.initialize($el, opts);
    };

    PluginScrollParallax.defaults = {
        minWidth: 991,
        transitionDuration: '200ms',
        cssProperty: 'width',
        cssValueStart: 40,
        cssValueEnd: 100,
        cssValueUnit: 'vw',
    };

    PluginScrollParallax.prototype = {
        initialize: function($el, opts) {
            if ($el.data(instanceName)) {
                return this;
            }

            this.$el = $el;

            this.onScroll = this.onScroll.bind( this );

            this
                .setData()
                .setOptions(opts)
                .build();

            return this;
        },

        setData: function() {
            this.$el.data(instanceName, this);

            return this;
        },

        disable: function() {
            var styles = {};
            styles['overflow'] = '';
            styles['width'] = '';
            if ( 'vw' == this.options.cssValueUnit ) {
                styles['position'] = '';
                styles['left'] = '';
            } else if ( '%' == this.options.cssValueUnit ) {
                styles['marginLeft'] = '';
                styles['marginRight'] = '';
            }
            this.$el.css( styles );
            window.removeEventListener( 'scroll', this.onScroll );
        },

        setOptions: function(opts) {
            this.options = $.extend(true, {}, PluginScrollParallax.defaults, opts, {
                wrapper: this.$el
            });

            return this;
        },

        onScroll: function( event ) {
            var self = this;

            if( self.$el.visible( true ) ) {
                var $window = $(window),
                    scrollTop = $window.scrollTop(),
                    elementOffset = self.$el.offset().top,
                    elHeight = self.$el.outerHeight(),
                    winHeight = window.innerHeight,
                    currentElementOffset = (elementOffset - scrollTop),
                    currentPercent = 100,
                    stickyHeight = ( window.theme && theme.StickyHeader.sticky_height ) || 0,
                    ratio = 1;

                if ( elHeight * 1.2 < winHeight ) {
                    ratio = 1.2;
                }

                if ( scrollTop + winHeight <= elementOffset + elHeight * ratio ) { // from bottom
                    currentPercent = ( scrollTop + winHeight - elementOffset ) / elHeight / ratio * 100;
                } else if ( ! self.started && scrollTop + stickyHeight >= elementOffset ) { // from top
                    if ( scrollTop + stickyHeight >= elementOffset + elHeight ) {
                        currentPercent = 0;
                    } else {
                        currentPercent = 100 - ( scrollTop - elementOffset + stickyHeight ) / elHeight * 100;
                    }
                }
                if ( currentPercent >= 98 ) {
                    self.started = true;
                }

                var cssValueUnit = self.options.cssValueUnit ? self.options.cssValueUnit : '';
                // Increment progress value according scroll position
                self.progress = self.options.cssValueStart + ( self.options.cssValueEnd - self.options.cssValueStart ) * ( currentPercent / 100 );

                if ( self.prevProgress && self.prevProgress === self.progress ) {
                    return;
                }
                // Adjust CSS end value
                if( self.progress >= self.options.cssValueEnd ) {
                    self.progress = self.options.cssValueEnd;
                }

                // Adjust CSS start value
                if( self.progress < self.options.cssValueStart ) {
                    self.progress = self.options.cssValueStart;
                }

                self.prevProgress = self.progress;

                var styles = {}
                styles[self.options.cssProperty] = self.progress + cssValueUnit;

                if ( 'width' == self.options.cssProperty ) {
                    if ( self.progress >= self.options.cssValueEnd ) {
                        styles['overflow'] = '';
                        styles['width'] = '';
                        if ( 'vw' == self.options.cssValueUnit ) {
                            styles['position'] = '';
                            styles['left'] = '';
                        } else if ( '%' == self.options.cssValueUnit ) {
                            styles['marginLeft'] = '';
                            styles['marginRight'] = '';
                        }
                    } else {
                        styles['overflow'] = 'hidden';
                        if ( 'vw' == self.options.cssValueUnit ) {
                            styles['position'] = 'relative';
                            styles['left'] = 'calc( ' + ( ( 100 - self.progress ) / 2 ) + cssValueUnit + ' - ' + ( self.scrollbarWidth / 2 ) + 'px )';
                        } else if ( '%' == self.options.cssValueUnit ) {
                            styles['marginLeft'] = 'auto';
                            styles['marginRight'] = 'auto';
                        }
                    }
                }

                self.$el.css(styles);
            }
        },

        build: function() {
            var self = this,
                $window = $(window),
                offset,
                yPos,
                bgpos,
                background,
                rotateY;

            // Scrollable
            if( window.innerWidth > self.options.minWidth ) {
                var $scrollableWrapper = self.$el;

                if( $scrollableWrapper.length ) {

                    var progress     = ( $(window).scrollTop() > ( self.$el.offset().top + $(window).outerHeight() ) ) ? self.options.cssValueEnd : self.options.cssValueStart,
                        cssValueUnit = self.options.cssValueUnit ? self.options.cssValueUnit : '',
                        scrollbarWidth = theme && theme.getScrollbarWidth && theme.getScrollbarWidth();
                    if ( ! scrollbarWidth ) {
                        scrollbarWidth = 0;
                    }
                    self.scrollbarWidth = scrollbarWidth;
                    self.progress = progress;
                    $scrollableWrapper.css({
                        'transition': 'ease '+ self.options.cssProperty +' '+ self.options.transitionDuration  + ', left ' + self.options.transitionDuration,
                        'width': progress + cssValueUnit
                    });
                    if ( 'width' == self.options.cssProperty ) {
                        if ( 'vw' == self.options.cssValueUnit ) {
                            $scrollableWrapper.css({
                                'position': 'relative',
                                'left': 'calc( ' + ( ( 100 - progress ) / 2 ) + cssValueUnit + ' - ' + ( scrollbarWidth / 2 ) + 'px )',
                            });
                        } else if ( '%' == self.options.cssValueUnit ) {
                            $scrollableWrapper.css({
                                'marginLeft': 'auto',
                                'marginRight': 'auto',
                            });
                        }
                    }

                    window.addEventListener( 'scroll', self.onScroll );

                    self.onScroll();
                }

                return;
            }

            return this;
        }
    };

    // jquery plugin
    $.fn.themeScrollParallax = function(opts) {
        return this.map(function() {
            var $this = $(this);

            if ($this.data(instanceName)) {
                return $this.data(instanceName);
            } else {
                return new PluginScrollParallax($this, opts);
            }

        });
    }

    $( '[data-plugin="scroll-parallax"]' ).filter( function () {
        if ( $( this ).find( '.owl-carousel' ).length ) {
            return false;
        }
        return true;
    } ).each( function () {
        var $this = $( this ),
            opts = $this.data( 'sp-options' );

        $this.themeScrollParallax( opts );
    } );

    $( '.owl-carousel' ).filter( function () {
        if ( $( this ).closest( '[data-plugin="scroll-parallax"]' ).length ) {
            return true;
        }
        return false;
    } ).on( 'initialized.owl.carousel', function() {
        var $this = $( this ).closest( '[data-plugin="scroll-parallax"]' ),
            opts = $this.data( 'sp-options' );
        if ( window.theme && theme.dynIntObsInit ) {
            theme.dynIntObsInit( $this, 'themeScrollParallax', opts );
        } else {
            $this.themeScrollParallax( opts );
        }
    } );
}).apply(this, [jQuery]);