jQuery( function ( $, window, document ) {
    $.fn.yith_fl_slider = function () {
        return $( this ).each( function () {
            self.opts = {};

            var slider    = $( this ),
                container = null,
                items     = null,
                title     = null,
                right     = null,
                left      = null,
                number    = 0,
                defaults  = {
                    animate: 'yes',
                    delay  : 6000
                };

            self.dom = {
                title_class    : 'yith-wcbsl-bestsellers-slider-title',
                container_class: 'yith-wcbsl-bestsellers-slider-container',
                item_class     : 'yith-wcbsl-bestseller-in-slider-wrapper',
                right_class    : 'yith-wcbsl-bestseller-slider-right',
                left_class     : 'yith-wcbsl-bestseller-slider-left',
                items_number   : 100
            };

            self.init = function () {
                self.opts = $.extend( {}, defaults, slider.data() );

                _set_elements();
                _set_buttons_actions();
                _start_animations();
            };

            var _set_elements        = function () {
                    container = slider.find( '.' + self.dom.container_class );
                    items     = slider.find( '.' + self.dom.item_class );
                    title     = slider.find( '.' + self.dom.title_class );
                    right     = slider.find( '.' + self.dom.right_class );
                    left      = slider.find( '.' + self.dom.left_class );

                    number = items.length;

                    var first           = items.first(),
                        first_width     = first.outerWidth() + parseFloat( first.css( 'margin-left' ) ) + parseFloat( first.css( 'margin-right' ) ),
                        container_width = ( first_width * number ) +
                                          parseFloat( container.css( 'padding-left' ) ) + parseFloat( container.css( 'padding-right' ) ) +
                                          1; // add 1px to prevent any kind of issue
                    container.css( { width: container_width + 'px' } );

                },
                _set_buttons_actions = function () {
                    right.on( 'mousedown', function () {
                        _move_items( 20 );
                    } ).on( 'mouseout mouseup', function () {
                        'yes' === self.opts.animate ? _move_items() : container.stop();
                    } );

                    left.on( 'mousedown', function () {
                        _move_items( -20 );
                    } ).on( 'mouseout mouseup', function () {
                        'yes' === self.opts.animate ? _move_items() : container.stop();
                    } );
                },
                _start_animations    = function () {
                    title.fadeOut( 3000 );
                    container.css( { left: '0' } );

                    if ( 'yes' === self.opts.animate ) {
                        setTimeout( _move_items, self.opts.delay );

                        container.on( 'mouseover', function () {
                            container.stop();
                        } );
                        container.on( 'mouseout', function () {
                            _move_items();
                        } );
                    }
                },
                _move_items          = function ( speed ) {
                    speed = speed || 1;

                    var w             = container.outerWidth(),
                        c             = container.position().left,
                        time          = ( ( w + c ) * 40 ),
                        complete_func = _show_title;

                    if ( speed < 0 ) {
                        time          = ( c * ( -40 ) );
                        w             = 0;
                        speed         = speed * ( -1 );
                        complete_func = {};
                    }

                    container.stop();
                    container.animate( { left: '-' + w + 'px' }, {
                        duration: time / speed,
                        easing  : 'linear',
                        complete: complete_func
                    } );
                }, _show_title       = function () {
                    title.fadeIn( 1000, _start_animations );
                };


            self.init();
        } );
    }
} );