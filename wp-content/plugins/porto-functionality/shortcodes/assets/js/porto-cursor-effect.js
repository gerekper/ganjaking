// CursorEffect
(function($) {
    
    var instanceName = '__cursorEffect';

    var PluginCursorEffect = function($el, opts) {
        return this.initialize($el, opts);
    };
    
    PluginCursorEffect.defaulst = {

    }

    PluginCursorEffect.prototype = {
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
            this.options = $.extend(true, {}, PluginCursorEffect.defaults, opts, {
                wrapper: this.$el
            });

            return this;
        },

        build: function() {
            var self = this;

            // Global Variables for cursor position
            self.clientX = -100;
            self.clientY = -100;

            self.size = 40;

            // Hide Mouse Cursor
            if( self.options.hideMouseCursor ) {
                self.$el.addClass('hide-mouse-cursor');
            }

            // Creates the cursor wrapper node
            var cursorOuter = document.createElement('DIV');
                cursorOuter.className = 'cursor-outer';

            // Creates the cursor inner node
            var cursorInner = document.createElement('DIV');
                cursorInner.className = 'cursor-inner';

            // Prepend cursor wrapper node to the body
            document.body.prepend( cursorOuter );

            // Prepend cursor inner node to the body
            document.body.prepend( cursorInner );

            var $cursorOuter = $('.cursor-outer'),
                $cursorInner = $('.cursor-inner');

            window.porto_cursor_effects.forEach( function( item, index ) {
                if ( ! item.selector || 'body' == item.selector ) { // body tag
                    if ( item.id ) {
                        $cursorOuter.addClass( item.id );
                        $cursorInner.addClass( item.id )
                        self.initialCls = item.id;
                    }
                    if ( item.icon ) {
                        $cursorInner.children( 'i' ).remove();
                        $cursorInner.addClass( 'cursor-inner-icon' ).append( '<i class="' + item.icon + '"></i>' );
                        self.initialInnerIcon = item.icon;
                    } else {
                        $cursorInner.removeClass( 'cursor-inner-icon' );
                    }
                    if ( item.cursor_w ) {
                        self.size = Number( item.cursor_w );
                    }
                }
            } );
            if ( ! self.initialCls ) {
                $cursorOuter.addClass( 'cursor-hover-visible' );
                $cursorInner.addClass( 'cursor-hover-visible' )
            }

            // Loop for render
            var render = function() {
                cursorOuter.style.transform = `translate(${self.clientX}px, ${self.clientY}px)`;
                cursorInner.style.transform = `translate(${self.clientX}px, ${self.clientY}px)`;

                self.loopInside = requestAnimationFrame(render);
            }
            self.loop = requestAnimationFrame(render);

            return this;
        },

        events: function() {
            var self = this,
                $cursorOuter = $('.cursor-outer'),
                $cursorInner = $('.cursor-inner');

            var initialCursorOuterBox    = $cursorOuter[0].getBoundingClientRect(),
                initialCursorOuterRadius = $cursorOuter.css('border-radius'),
                initialSize = self.size;

            // Update Cursor Position
            document.addEventListener('mousemove', function(e){
                if( !self.isStuck ) {
                    self.clientX = e.clientX - self.size / 2;
                    self.clientY = e.clientY - self.size / 2;
                }

                $cursorOuter.removeClass('opacity-0');
            });

            self.isStuck = false;

            window.porto_cursor_effects.forEach( function( item, index ) {
                var $obj = $( item.selector || document.body );
                if ( ! $obj.length ) {
                    return;
                }
                var cursorEffectType = item.hover_effect;
                $obj.on('mouseenter', function(e){

                    // Identify Event With Hover Class
                    $cursorOuter.addClass('cursor-outer-hover');
                    $cursorInner.addClass('cursor-inner-hover');

                    if ( item.selector && self.initialCls ) {
                        $cursorOuter.removeClass( self.initialCls );
                        $cursorInner.removeClass( self.initialCls );
                    }
                    if ( item.id ) {
                        $cursorOuter.addClass( item.id );
                        $cursorInner.addClass( item.id )
                    }

                    $cursorInner.children( 'i' ).remove();
                    if ( item.icon ) {
                        $cursorInner.addClass( 'cursor-inner-icon' ).append( '<i class="' + item.icon + '"></i>' );
                    } else {
                        $cursorInner.removeClass( 'cursor-inner-icon' );
                    }

                    // Effect Types
                    switch ( cursorEffectType ) {
                        case 'fit':
                            var thisBox = $(this)[0].getBoundingClientRect();

                            self.clientX = thisBox.x;
                            self.clientY = thisBox.y;

                            $cursorOuter.css({
                                width: thisBox.width,
                                height: thisBox.height,
                                'border-radius': $(this).css('border-radius')
                            }).addClass('cursor-outer-fit');

                            $cursorInner.addClass('opacity-0');

                            self.isStuck = true;
                            break;

                        case 'plus':
                            $cursorInner.addClass('cursor-inner-plus');
                            if ( item.cursor_w ) {
                                self.size = Number( item.cursor_w );
                            }
                            break;
                    }
                });

                $obj.on('mouseleave', function(){
                    
                    // Identify Event With Hover Class
                    $cursorOuter.removeClass('cursor-outer-hover');
                    $cursorInner.removeClass('cursor-inner-hover');
                    if ( item.id ) {
                        $cursorOuter.removeClass( item.id );
                        $cursorInner.removeClass( item.id )
                    }
                    if ( item.icon ) {
                        $cursorInner.removeClass( 'cursor-inner-icon' ).children( 'i' ).remove();
                    }

                    if ( self.initialCls ) {
                        $cursorOuter.addClass( self.initialCls );
                        $cursorInner.addClass( self.initialCls );
                    }
                    if ( self.initialInnerIcon ) {
                        $cursorInner.children( 'i' ).remove();
                        $cursorInner.addClass( 'cursor-inner-icon' ).append( '<i class="' + self.initialInnerIcon + '"></i>' );
                    }

                    // Effect Types
                    switch ( cursorEffectType ) {
                        case 'fit':
                            $cursorOuter.css({
                                width: '',
                                height: '',
                                'border-radius': ''
                            }).removeClass('cursor-outer-fit');

                            $cursorInner.removeClass('opacity-0');

                            self.isStuck = false;
                            break;

                        case 'plus':
                            $cursorInner.removeClass('cursor-inner-plus');
                            self.size = initialSize;
                            break;
                    }
                });

                if ( ! item.selector ) {
                    $obj.trigger( 'mouseenter' );
                }
            } );

            $(window).on('scroll', function(){
                if( $cursorOuter.hasClass('cursor-outer-fit') ) {
                    $cursorOuter.addClass('opacity-0').removeClass('cursor-outer-fit');
                }
            });

            return this;
        },

        destroy: function() {
            var self = this;

            self.$el.removeClass('hide-mouse-cursor');

            cancelAnimationFrame( self.loop );
            cancelAnimationFrame( self.loopInside );

            document.querySelector('.cursor-outer').remove();
            document.querySelector('.cursor-inner').remove();

            self.$el.removeData( instanceName, self );
        }
    };

    // jquery plugin
    $.fn.themePluginCursorEffect = function(opts) {
        return this.map(function() {
            var $this = $(this);

            if ($this.data(instanceName)) {
                return $this.data(instanceName);
            } else {
                return new PluginCursorEffect($this, opts);
            }

        });
    }

    $( document ).ready( function() {
        if ( window.porto_cursor_effects && window.porto_cursor_effects.length ) {
            $( document.body ).themePluginCursorEffect();
        }
    } );

}).apply(this, [jQuery]);
