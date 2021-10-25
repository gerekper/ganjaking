;(function ( $, window, document, undefined ) {

    "use strict";

    // Create the defaults once
    var pluginName = 'jltmaVisualSelect',
        defaults = {
            item             : 'jltma-select-item',              // visual select item [class name]
            selected         : 'jltma-selected',                 // selected item [class name]
            caption          : 'jltma-select-caption',           // caption under visual item [class name]
            container        : 'jltma-visual-select',            // select items container [class name]

            insertCaption    : false,                          // whether insert captions to visual items
            insertSymbol     : true,                           // whether insert symbol to visual items
            insertTitleAttr  : true,                           // adds title attribute to visual item
            autoHideElement  : true,                           // hide HTML select element after init
            imgTest          : /\.jpg|\.png|\.gif|.jpeg|\.svg/ // test for image src
        },

        attributesMap = {
            'type'             : 'symbolType',
            'title-attr'       : 'insertTitleAttr',
            'auto-hide'        : 'autoHideElement',
            'caption'          : 'insertCaption'
        };

    // The actual plugin constructor
    function Plugin( element, options ) {
        this.element = element;
        this.$element = $(element);
        this.options = $.extend( {}, defaults, options) ;

        // read attributes
        for ( var key in attributesMap ) {
            var value = attributesMap[ key ],
                dataAttr = this.$element.data( key );

            if ( dataAttr === undefined ) {
                continue;
            }

            this.options[ value ] = dataAttr;
        }

        this._defaults = defaults;
        this._name = pluginName;

        this.init();
    }

    $.extend(Plugin.prototype, {

        init : function(){
            var self = this,
                st = self.options;

            self.multiple = self.$element.attr('multiple') === 'multiple';

            if ( st.autoHideElement ) {
                self.$element.css('display', 'none');
            }

            // generate select items
            self.$selectCont = $('<div class="' + st.container + '"></div>').insertAfter(self.$element);
            self.generate();

            self.$element.on( 'change', this.update.bind( this ) );

        },

        /**
         * on visual select item clicked
         * @private
         * @param  {jQuery Event} event
         */
        _onItemClick : function (event) {
            var $visualItem = $(event.currentTarget),
                $selectOption = $visualItem.data('selectOption'),
                st = this.options;

            if ( this.multiple ) {

                if ( $visualItem.hasClass(st.selected) ) {
                    $visualItem.removeClass(st.selected);
                    $selectOption.removeAttr('selected');
                } else {
                    $visualItem.addClass(st.selected);
                    $selectOption.attr('selected', 'selected');

                    var val = this.$element.val();
                    if ( val === null ) {
                        val = [];
                    }

                    val.push( $selectOption.attr( 'value' ) );

                    this.$element.val( val );
                }

            } else if ( !$visualItem.hasClass(st.selected) ) {

                $visualItem.addClass(st.selected);
                $selectOption.attr('selected', 'selected');
                this.$element.val( $selectOption.attr( 'value' ) );

                if ( this.$selectedItem ) {
                    this.$selectedItem.removeClass(st.selected);
                    this.$selectedItem.data('selectOption').removeAttr('selected');
                }

                this.$selectedItem = $visualItem;
            }

            this._internalTrigger = true;
            this.$element.trigger('change');

        },

        /**
         * Generates video element sources by parsing the data-video-src attribute on element
         */
        _generateVideoSource: function( videoSrc ) {
            var source = '';
            videoSrc.split( ',' ).forEach( function( src ) {
                src = src.split( ' ' );
                source += '<source src="' + src[0] + '" type="video/' + src[1] + '">';
            } );

            return source;
        },

        /**
         * On video ready to play
         */
        _videoInit: function( event ) {
            $(event.currentTarget).on( 'mouseenter', function() {
                this.play();
            }).on( 'mouseleave', function() {
                this.pause();
                this.currentTime = 0;
            });
        },

        /**
         * updates selected items in visual form
         */
        update: function() {
            if ( this._internalTrigger ) {
                this._internalTrigger = false;
                return;
            }

            var self = this,
                st = this.options,
                $items = self.$selectCont.find( '.' + st.item ),
                val = self.$element.val();

            self.$element.find( 'option' ).each( function( index, option ) {
                var $option = $(option),
                    $visualItem = $items.eq( index );
                if ( val.indexOf( $option.val() ) !== -1 ) {
                    self.$selectedItem = $visualItem.addClass( st.selected );
                } else {
                    $visualItem.removeClass( st.selected );
                }

            } );
        },

        /**
         * create visual items from HTML select element
         * @param {boolean} reset Remove old visual items [it's useful for updating visual select]
         * @public
         */
        generate : function (reset) {
            var self = this,
                st = self.options;

            if ( reset ) {
                this.$selectCont.find('.' + st.item).remove();
            }

            self.$element.find('option').each(function(){
                var $selectOption = $(this),
                    $visualItem = $('<div class="' + st.item + '"></div>'),
                    symbol = $selectOption.data('symbol'),
                    videoSrc = $selectOption.data('video-src'),
                    caption = $selectOption.html(),
                    cssClass = $selectOption.data('class');

                if ( cssClass ) {
                    $visualItem.addClass(cssClass);
                }

                // insert visual symbol to select item

                if ( st.insertSymbol ) {
                    if ( videoSrc ) {
                        $visualItem.attr('item-type', 'video');
                        var $videoElement = $('<video></video>').attr( 'muted', '' ).attr( 'loop', '' )
                                                                .append( self._generateVideoSource( videoSrc ) )
                                                                .appendTo( $visualItem );

                        $videoElement[0].addEventListener( 'loadedmetadata', self._videoInit );
                    } else if ( st.imgTest.test( symbol ) || $selectOption.data( 'type' ) === 'image' ) {
                        $('<img/>').attr('src', symbol)
                                   .attr('alt', caption)
                                   .appendTo($visualItem);
                    } else {
                        $('<span></span>').addClass(symbol)
                                          .appendTo($visualItem);
                    }
                }

                // insert caption
                if ( st.insertCaption ) {
                    $('<span class="' + st.caption + '">' + caption + '</span>').appendTo($visualItem);
                }

                $visualItem.click($.proxy(self._onItemClick, self))
                           .data('selectOption', $selectOption)
                           .appendTo(self.$selectCont);

                if ( st.insertTitleAttr ) {
                    $visualItem.attr('title', caption);
                }

                if ( $selectOption.attr('selected') === 'selected' ) {
                    self.$selectedItem = $visualItem.addClass(st.selected);
                }

            });
        }

    });


    $.fn[pluginName] = function ( options ) {
        var args = arguments;

        // Is the first parameter an object (options), or was omitted,
        // instantiate a new instance of the plugin.
        if (options === undefined || typeof options === 'object') {
            return this.each(function () {

                // Only allow the plugin to be instantiated once,
                // so we check that the element has no plugin instantiation yet
                if (!$.data(this, 'plugin_' + pluginName)) {

                    // if it has no instance, create a new one,
                    // pass options to our plugin constructor,
                    // and store the plugin instance
                    // in the elements jQuery data object.
                    $.data(this, 'plugin_' + pluginName, new Plugin( this, options ));
                }
            });

        // If the first parameter is a string and it doesn't start
        // with an underscore or "contains" the `init`-function,
        // treat this as a call to a public method.
        } else if (typeof options === 'string' && options[0] !== '_' && options !== 'init') {

            // Cache the method call
            // to make it possible
            // to return a value
            var returns;

            this.each(function () {
                var instance = $.data(this, 'plugin_' + pluginName);

                // Tests that there's already a plugin-instance
                // and checks that the requested public method exists
                if (instance instanceof Plugin && typeof instance[options] === 'function') {

                    // Call the method of our plugin instance,
                    // and pass it the supplied arguments.
                    returns = instance[options].apply( instance, Array.prototype.slice.call( args, 1 ) );
                }

                // Allow instances to be destroyed via the 'destroy' method
                if (options === 'destroy') {
                  $.data(this, 'plugin_' + pluginName, null);
                }
            });

            // If the earlier cached method
            // gives a value back return the value,
            // otherwise return this to preserve chainability.
            return returns !== undefined ? returns : this;
        }
    }

}(jQuery, window, document));
