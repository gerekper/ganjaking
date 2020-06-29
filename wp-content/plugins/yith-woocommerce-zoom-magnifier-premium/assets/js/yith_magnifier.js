/**
 * YITH Magnifier
 *
 * @version 0.1
 * @author Your Inspiration Themes Team <info@yithemes.com>
 *
 * MIT License.
 */
(function (window, $, undefined) {
    'use strict';

    //include imagesLoaded plugin
    if ($.fn.imagesLoaded === undefined) {
        /*!
         * jQuery imagesLoaded plugin v2.1.1
         * http://github.com/desandro/imagesloaded
         *
         * MIT License. by Paul Irish et al.
         */

        /*jshint curly: true, eqeqeq: true, noempty: true, strict: true, undef: true, browser: true */
        /*global jQuery: false */

        ;
        (function ($, undefined) {
            'use strict';

            // blank image data-uri bypasses webkit log warning (thx doug jones)
            var BLANK = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==';

            $.fn.imagesLoaded = function (callback) {
                var $this = this,
                    deferred = $.isFunction($.Deferred) ? $.Deferred() : 0,
                    hasNotify = $.isFunction(deferred.notify),
                    $images = $this.find('img').add($this.filter('img')),
                    loaded = [],
                    proper = [],
                    broken = [];

                // Register deferred callbacks
                if ($.isPlainObject(callback)) {
                    $.each(callback, function (key, value) {
                        if (key === 'callback') {
                            callback = value;
                        } else if (deferred) {
                            deferred[key](value);
                        }
                    });
                }

                function doneLoading() {
                    var $proper = $(proper),
                        $broken = $(broken);

                    if (deferred) {
                        if (broken.length) {
                            deferred.reject($images, $proper, $broken);
                        } else {
                            deferred.resolve($images);
                        }
                    }

                    if ($.isFunction(callback)) {
                        callback.call($this, $images, $proper, $broken);
                    }
                }

                function imgLoadedHandler(event) {
                    imgLoaded(event.target, event.type === 'error');
                }

                function imgLoaded(img, isBroken) {
                    // don't proceed if BLANK image, or image is already loaded
                    if (img.src === BLANK || $.inArray(img, loaded) !== -1) {
                        return;
                    }

                    // store element in loaded images array
                    loaded.push(img);

                    // keep track of broken and properly loaded images
                    if (isBroken) {
                        broken.push(img);
                    } else {
                        proper.push(img);
                    }

                    // cache image and its state for future calls
                    $.data(img, 'imagesLoaded', {isBroken: isBroken, src: img.src});

                    // trigger deferred progress method if present
                    if (hasNotify) {
                        deferred.notifyWith($(img), [isBroken, $images, $(proper), $(broken)]);
                    }

                    // call doneLoading and clean listeners if all images are loaded
                    if ($images.length === loaded.length) {
                        setTimeout(doneLoading);
                        $images.unbind('.imagesLoaded', imgLoadedHandler);
                    }
                }

                // if no images, trigger immediately
                if (!$images.length) {
                    doneLoading();
                } else {
                    $images.bind('load.imagesLoaded error.imagesLoaded', imgLoadedHandler)
                        .each(function (i, el) {
                            var src = el.src;

                            // find out if this image has been already checked for status
                            // if it was, and src has not changed, call imgLoaded on it
                            var cached = $.data(el, 'imagesLoaded');
                            if (cached && cached.src === src) {
                                imgLoaded(el, cached.isBroken);
                                return;
                            }

                            // if complete is true and browser supports natural sizes, try
                            // to check for image status manually
                            if (el.complete && el.naturalWidth !== undefined) {
                                imgLoaded(el, el.naturalWidth === 0 || el.naturalHeight === 0);
                                return;
                            }

                            // cached images don't fire load sometimes, so we reset src, but only when
                            // dealing with IE, or image is complete (loaded) and failed manual check
                            // webkit hack from http://groups.google.com/group/jquery-dev/browse_thread/thread/eee6ab7b2da50e1f
                            if (el.readyState || el.complete) {
                                el.src = BLANK;
                                el.src = src;
                            }
                        });
                }

                return deferred ? deferred.promise($this) : $this;
            };

        })(jQuery);
    }

    $.yith_magnifier = function (options, element) {
        this.t = element;
        this.element = $(element);
        this._init(options);
    };

    $.yith_magnifier.defaults = {
        zoomWidth   : 'auto',
        zoomHeight  : 'auto',
        position    : 'right',
        tint        : false,
        tintOpacity : 0.5,
        lensOpacity : 0.5,
        softFocus   : false,
        smoothMove  : 3,
        showTitle   : true,
        titleOpacity: 0.5,
        adjustX     : 0,
        adjustY     : 0,

        phoneBehavior: 'inside', //allowed values: inside, disable, default
        loadingLabel : 'Loading...',

        enableSlider : true,
        slider       : 'carouFredSel',
        sliderOptions: {},

        from_gallery : 'no',

        onLoad : function () {
            return false;
        },
        onEnter: function () {
            return false
        },
        onLeave: function () {
            return false
        },
        onMove : function () {
            return false
        },
        onClick: function () {
            return false;
        },

        elements: {
            zoom     : $('.yith_magnifier_zoom'),
            zoomImage: $('.yith_magnifier_zoom img').first(),
            gallery  : $('.yith_magnifier_gallery li a')
        }
    };

    $.yith_magnifier.prototype = {

        /**
         * Let's start the magnifier
         *
         * @param {Object} options
         *
         * @private
         */
        _init: function (options) {
            var self = this;
            $(document).trigger('yith_magnifier_before_init');

            $.each($.yith_magnifier.defaults.elements, function (i, v) {
                var el = $.yith_magnifier.defaults.elements;
                el[i] = $(v, self);
            });
            self.options = $.extend(true, {}, $.yith_magnifier.defaults, options);
            self.isPhone = self._isPhone();

            self._loading();

            self.element.imagesLoaded(function () {
                self.options.onLoad();
                self._initZoom();
                self._initGallery();
            });

            $(document).trigger('yith_magnifier_after_init');
        },


        /**
         * Init gallery handlers
         *
         * @private
         *
         */
        _initGallery: function () {

            var self = this;
            var elements = self.options.elements;

            var gallery = elements.gallery;
            var zoom = elements.zoom;
            var zoomImage = elements.zoomImage;

            if ( gallery.length > 0 ) {
                gallery.on('click', function (e) {
                    e.preventDefault();

                    if ( $( '#yith_wczm_traffic_light' ).val() == 'free' ){

                        self.options.from_gallery = 'yes';

                        var t = $(this);
                        self.destroy();

                        zoom.attr('href', this.href);

                        zoomImage.attr('src', t.data('small'))
                            .attr('srcset', t.data('small'))
                            .attr('src-orig', t.data('small'))
                            .attr('title', '')
                            .attr('title', t.attr('title'));
                        $.data(self.t, 'yith_magnifier', new $.yith_magnifier(self.options, self.element));

                    }

                });

                //gallery.filter(':first').trigger('click');

                if (self.options.enableSlider) {
                    gallery.closest('ul').trigger('yith_magnifier_slider_destroy');
                    //gallery.closest('ul')[self.options.slider](self.options.sliderOptions);
                }

            } else {
                gallery.on('click', function (e) {
                    e.preventDefault();
                    return false;
                })
            }
        },

        /**
         * Show a loading icon while image isn't properly loaded
         *
         * @private
         */
        _loading: function () {
            if (this.options.disableRightClick) {
                $(document).on('contextmenu', function (e) {
                    return false;
                });
            }

            if (this.mouseTrap === undefined) {
                var w = this.options.elements.zoom.width();

                if (this.options.loadingLabel) {
                    $('<div class="yith_magnifier_loading">' + this.options.loadingLabel + '</div>').css({
                        'width'     : w,
                        'text-align': 'center',
                        opacity     : .5
                    }).appendTo(this.options.elements.zoom.parent());
                }
            }
        },

        /**
         * Initialize the zoom images
         *
         * @private
         */
        _initZoom: function () {

            if ( $( '#yith_wczm_traffic_light' ).val() == 'free' ){

                $( '#yith_wczm_traffic_light' ).val( 'blocked' );

                var self = this;
                self.zoom = self.options.elements.zoom;
                self.zoomImage = self.options.elements.zoomImage;

                var zoom_wrap_css_class = "yith_magnifier_zoom_wrap";

                if (self.options.zoom_wrap_additional_css) {
                    if (!$('.' + zoom_wrap_css_class).length) {

                        zoom_wrap_css_class += " " + self.options.zoom_wrap_additional_css;
                    } else {
                        return;
                    }
                }
                self.zoom.wrap('<div class="' + zoom_wrap_css_class + '"></div>');

                self.IMG_zoomImage = new Image();

                var product_id = $( '.woocommerce-variation-add-to-cart .variation_id' ).val();

                if ( $.isNumeric( product_id ) && product_id != 0 && self.options.from_gallery == 'no' ){

                    var data = {
                        action      : 'yith_wc_zoom_magnifier_get_main_image',
                        product_id  : product_id,
                        context     : 'frontend'
                    }

                    $.ajax({
                        data      : data,
                        url       : yith_wc_zoom_magnifier_storage_object.ajax_url,
                        type      : 'post',
                        error     : function ( response ) {
                            console.log( 'ERROR - Yith_WC_Zoom_Magnifier' );
                            console.log( response );
                            $( '#yith_wczm_traffic_light' ).val( 'free' );
                        },
                        success   : function ( response ) {

                            self.zoom.attr( 'href', response.url );

                            self.IMG_zoomImage.src = response.url;

                            self.IMG_smallImage = new Image();
                            self.IMG_smallImage.src = self.zoomImage.attr('src');

                            // set new gallery
                            if( response.gallery ) {

                                if ($('.yith_magnifier_gallery').length)
                                    $('.yith_magnifier_gallery').closest('.thumbnails').replaceWith( response.gallery );
                                else
                                    $( '.images').append( response.gallery );

                                self.options.elements.gallery =  $('.yith_magnifier_gallery li a' );
                            }

                            if ($.browser && $.browser.msie && $.browser.version == 8) {
                                $(self.IMG_zoomImage).load(function () {

                                    self._initGallery();
                                    self._initTrap();
                                });
                            } else {

                                $([self.IMG_zoomImage, self.IMG_smallImage]).imagesLoaded(function () {

                                    self._initGallery();
                                    self._initTrap();
                                });
                            }

                            $(document).on('click', 'a.yith_expand', function (e) {

                                e.preventDefault();

                                $().prettyPhoto({
                                    social_tools      : false,
                                    theme             : 'pp_woocommerce',
                                    horizontal_padding: 20,
                                    opacity           : 0.8,
                                    deeplinking       : false,
                                    callback          : function () {
                                        $(".yith_expand").css('display', 'inline');
                                    }
                                });

                                $.prettyPhoto.open(self.zoom[0].href, '', '');
                                $(".yith_expand").css('display', 'none');
                                $(".pp_woocommerce .ppt").css({
                                    'height'      : 0,
                                    'marginBottom': 0
                                });


                            });
                            $(document).trigger('yith_magnifier_after_init_zoom')
                            $( '#yith_wczm_traffic_light' ).val( 'free' );
                        }
                    });

                }
                else{

                    self.IMG_zoomImage.src = self.zoom.attr('href');

                    self.IMG_smallImage = new Image();
                    self.IMG_smallImage.src = self.zoomImage.attr('src');

                    if ($.browser && $.browser.msie && $.browser.version == 8) {
                        $(self.IMG_zoomImage).load(function () {

                            self._initTrap();
                        });
                    } else {

                        $([self.IMG_zoomImage, self.IMG_smallImage]).imagesLoaded(function () {

                            self._initTrap();
                        });
                    }

                    $(document).on('click', 'a.yith_expand', function (e) {

                        e.preventDefault();

                        $().prettyPhoto({
                            social_tools      : false,
                            theme             : 'pp_woocommerce',
                            horizontal_padding: 20,
                            opacity           : 0.8,
                            deeplinking       : false,
                            callback          : function () {
                                $(".yith_expand").css('display', 'inline');
                            }
                        });

                        $.prettyPhoto.open(self.zoom[0].href, '', '');
                        $(".yith_expand").css('display', 'none');
                        $(".pp_woocommerce .ppt").css({
                            'height'      : 0,
                            'marginBottom': 0
                        });


                    });
                    $(document).trigger('yith_magnifier_after_init_zoom');

                    $( '#yith_wczm_traffic_light' ).val( 'free' );

                }

            }

        },

        /**
         * Create the mouse trap
         *
         * @private
         */
        _initTrap: function () {

            var self = this;
            var zoomImg = self.IMG_zoomImage,
                thumbImg = self.IMG_smallImage;

            self.mx = 0;
            self.my = 0;
            self.controlTimer = 0;
            self.lens = null;
            self.tint = null;
            self.softFocus = null;
            self.zoomDiv = null;
            self.cw = 0;
            self.ch = 0;
            self.zw = 0;
            self.destU = 0;
            self.destV = 0;
            self.currU = 0;
            self.currV = 0;
            self.mouseTrap = null;

            var zoom = self.options.elements.zoom;
            var zoomImage = self.options.elements.zoomImage;

            //remove loading div
            //this.zoom.parent().siblings('.yith_magnifier_loading').remove();
            $('.yith_magnifier_loading').remove();

            if (this.element.find('.yith_magnifier_mousetrap')) {
                this.element.find('.yith_magnifier_mousetrap').remove();
            }

            this.mouseTrap = $('<div class="yith_magnifier_mousetrap pp_woocommerce" />').css({
                width : '100%', //zoomImage.outerWidth(),
                height: '100%', //zoomImage.outerHeight(),
                top   : 0,
                left  : 0
            }).appendTo(zoom.parent());

            if( typeof ywzm_data != 'undefined' ) {
                $('<a href="#" class="yith_expand" title="' + ywzm_data.expand_label + '" style="display: inline;">Expand</a>').appendTo(this.mouseTrap);
            }

            if (self.isPhone && self.options.phoneBehavior == 'disable') {
                return;
            }

            // Detect device type, normal mouse or touchy(ipad android) by albanx
            var touchy = ("ontouchstart" in document.documentElement) ? true : false;
            var event_move = 'touchmove mousemove';
            var event_end = 'touchend mouseleave';
            var event_ent = 'touchstart mouseenter';
            var event_click = 'touchstart click';

            this.mouseTrap.on(event_move, this, function (e) {

                self.options.onMove();

                self.mx = ( typeof(e.originalEvent.touches) != 'undefined' ) ? e.originalEvent.touches[0].pageX : e.pageX;
                self.my = ( typeof(e.originalEvent.touches) != 'undefined' ) ? e.originalEvent.touches[0].pageY : e.pageY;

            }).on(event_end, this, function (e) {

                clearTimeout(self.controlTimer);
                //event.data.removeBits();
                if (self.lens) {
                    self.lens.fadeOut(299);
                }
                if (self.tint) {
                    self.tint.fadeOut(299);
                }
                if (self.softFocus) {
                    self.softFocus.fadeOut(299);
                }
                self.zoomDiv.fadeOut(300, function () {
                    self._onLeave();
                });

                return false;

            }).on(event_click, this, function (e) {
                self.options.onClick();
            }).on(event_ent, this, function (e) {

                if ( $( '#yith_wczm_traffic_light' ).val() == 'free' ){

                    self.options.onEnter();

                    if (touchy) {
                        e.preventDefault();
                    }

                    self.mx = ( typeof(e.originalEvent.touches) != 'undefined' ) ? e.originalEvent.touches[0].pageX : e.pageX;
                    self.my = ( typeof(e.originalEvent.touches) != 'undefined' ) ? e.originalEvent.touches[0].pageY : e.pageY;

                    self.zw = e.data;
                    if (self.zoomDiv) {
                        self.zoomDiv.stop(true, false);
                        self.zoomDiv.remove();
                    }

                    var xPos = self.options.adjustX,
                        yPos = self.options.adjustY;

                    var siw = zoomImage.outerWidth();
                    var sih = zoomImage.outerHeight();

                    var w = self.options.zoomWidth;
                    var h = self.options.zoomHeight;

                    if (self.options.zoomWidth == 'auto') {
                        w = siw;
                    }

                    if (self.options.zoomHeight == 'auto') {
                        h = sih;
                    }

                    var appendTo = zoom.parent();
                    switch (self.options.position) {
                        case 'top':
                            yPos -= h;
                            break;
                        case 'right':
                            xPos += siw;
                            break;
                        case 'bottom':
                            yPos += sih;
                            break;
                        case 'left':
                            xPos -= w;
                            break;
                        case 'inside':
                            w = siw;
                            h = sih;
                            break;

                        // All other values, try and find an id in the dom to attach to.
                        default:
                            appendTo = $('#' + self.options.position);
                            // If dom element doesn't exit, just use 'right' position as default.
                            if (!appendTo.length) {
                                appendTo = zoom;
                                xPos += siw; //+ opts.adjustX;
                                yPos += sih; // + opts.adjustY;
                            } else {
                                w = appendTo.innerWidth();
                                h = appendTo.innerHeight();
                            }
                    }

                    if (self.isPhone && self.options.phoneBehavior == 'inside') {
                        w = siw;
                        h = sih;
                        xPos = 0;
                        yPos = 0;
                    }

                    self.zoomDiv = $('<div class="yith_magnifier_zoom_magnifier" />').css({
                        left               : xPos,
                        top                : yPos,
                        width              : w,
                        height             : h,
                        'background-repeat': 'no-repeat',
                        backgroundImage    : 'url(' + zoomImg.src + ')'
                    }).appendTo(appendTo);


                    // Add the title from title tag.
                    if (zoomImage.attr('title') && self.options.showTitle) {
                        $('<div class="yith_magnifier_title">' + zoomImage.attr('title') + '</div>').appendTo(self.zoomDiv);
                    }

                    if (self.isPhone) {
                        if (self.options.phoneBehavior != 'disable') {
                            self.zoomDiv.fadeIn(500);
                        } else {
                            self.lens.fadeOut(299);
                        }
                    } else {
                        self.zoomDiv.fadeIn(500);
                    }

                    if (self.lens) {
                        self.lens.remove();
                        self.lens = null;
                    }

                    if (zoomImg.width <= 1) {
                        self.cw = (zoomImage.outerWidth() / zoomImg.naturalWidth) * self.zoomDiv.width();
                    } else {
                        self.cw = (zoomImage.outerWidth() / zoomImg.width) * self.zoomDiv.width();
                    }

                    if (zoomImg.height <= 1) {
                        self.ch = (zoomImage.outerHeight() / zoomImg.naturalHeight) * self.zoomDiv.height();
                    } else {
                        self.ch = (zoomImage.outerHeight() / zoomImg.height) * self.zoomDiv.height();
                    }

                    // Attach mouse, initially invisible to prevent first frame glitch
                    self.lens = $('<div class="yith_magnifier_lens" />').css({
                        width : self.cw,
                        height: self.ch
                    }).appendTo(zoom);

                    self.mouseTrap.css('cursor', self.lens.css('cursor'));

                    var noTrans = false;


                    // Init tint layer if needed. (Not relevant if using inside mode)
                    if (self.options.tint) {
                        //self.lens.css('background', 'url("' + zoomImage.attr('src') + '")');
                        self.tint = $('<div />').css({
                            display        : 'none',
                            position       : 'absolute',
                            left           : 0,
                            top            : 0,
                            width          : zoomImage.outerWidth(),
                            height         : zoomImage.outerHeight(),
                            backgroundColor: self.options.tint,
                            opacity        : self.options.tintOpacity
                        }).appendTo(zoom);


                        self.lens.append($('<img />', {
                            src: zoomImage.attr('src')
                        }));

                        noTrans = true;
                        self.tint.fadeIn(500);
                    }

                    if (self.options.softFocus) {
                        //self.lens.css('background', 'url("' + zoomImage.attr('src') + '")');
                        self.softFocus = $('<div />').css({
                            position: 'absolute',
                            display : 'none',
                            top     : '1px',
                            left    : '1px',
                            width   : zoomImage.outerWidth(),
                            height  : zoomImage.outerHeight(),
                            //background: 'url("' + zoomImage.attr('src') + '")',
                            //backgroundSize: '100%',
                            opacity : .5
                        }).appendTo(zoom);

                        self.softFocus.append($('<img />', {
                            src: zoomImage.attr('src')
                        }));

                        if (self.lens.find('img').length == 0) {
                            self.lens.append($('<img />', {
                                src: zoomImage.attr('src')
                            }));
                        }

                        noTrans = true;
                        self.softFocus.fadeIn(500);
                    }


                    if (!noTrans) {
                        self.lens.css('opacity', self.options.lensOpacity);
                    }
                    if (self.options.position !== 'inside') {
                        self.lens.fadeIn(500);
                    }

                    // Start processing.
                    self.zw._controlLoop();

                    return; // Don't return false here otherwise opera will not detect change of the mouse pointer type.

                }

            });

        },

        /**
         *
         *
         * @private
         */
        _controlLoop: function () {
            var self = this;

            if (this.lens) {
                var x = (this.mx - this.zoomImage.offset().left - (this.cw * 0.5)) >> 0;
                var y = (this.my - this.zoomImage.offset().top - (this.ch * 0.5)) >> 0;

                if (x < 0) {
                    x = 0;
                } else if (x > (this.zoomImage.outerWidth() - this.cw)) {
                    x = (this.zoomImage.outerWidth() - this.cw);
                }

                if (y < 0) {
                    y = 0;
                } else if (y > (this.zoomImage.outerHeight() - this.ch)) {
                    y = (this.zoomImage.outerHeight() - this.ch);
                }

                this.lens.css({
                    left: x - 2,
                    top : y - 1
                });

                //this.lens.css('background-position', (-x) + 'px ' + (-y) + 'px');


                this.lens.find('img').css({
                    width     : this.zoomImage.outerWidth(),
                    height    : this.zoomImage.outerHeight(),
                    marginLeft: (-x) + 'px ',
                    marginTop : (-y) + 'px'
                });


                if (this.IMG_zoomImage.width <= 1) {
                    this.destU = (((x) / this.zoomImage.outerWidth()) * this.IMG_zoomImage.naturalWidth) >> 0;
                } else {
                    this.destU = (((x) / this.zoomImage.outerWidth()) * this.IMG_zoomImage.width) >> 0;
                }

                if (this.IMG_zoomImage.height <= 1) {
                    this.destV = (((y) / this.zoomImage.outerHeight()) * this.IMG_zoomImage.naturalHeight) >> 0;
                } else {
                    this.destV = (((y) / this.zoomImage.outerHeight()) * this.IMG_zoomImage.height) >> 0;
                }


                this.currU += (this.destU - this.currU) / this.options.smoothMove;
                this.currV += (this.destV - this.currV) / this.options.smoothMove;

                this.zoomDiv.css('background-position', (-(this.currU >> 0) + 'px ') + (-(this.currV >> 0) + 'px'));
            }

            this.controlTimer = setTimeout(function () {
                self._controlLoop();
            }, 30);
        },

        /**
         * This method is called when the mouse leave the image
         *
         * @private
         */
        _onLeave: function () {
            this.options.onLeave();

            if (this.zoomDiv) {
                this.zoomDiv.remove();
                this.zoomDiv = null;
            }

            this._removeElements();
        },

        /**
         * Remove lens, tint and softfocus
         *
         * @private
         */
        _removeElements: function () {
            if (this.lens) {
                this.lens.remove();
                this.lens = null;
            }

            if (this.tint) {
                this.tint.remove();
                this.tint = null;
            }

            if (this.softFocus) {
                this.softFocus.remove();
                this.softFocus = null;
            }

            if (this.element.find('.yith_magnifier_loading').length > 0) {
                this.element.find('.yith_magnifier_loading').remove();
            }
        },

        /**
         * Detect if user is using a phone device (eg iPhone)
         *
         * @private
         */
        _isPhone: function () {
            var userAgent = navigator.userAgent.toLowerCase();

            return ( userAgent.match(/iphone/i) || userAgent.match(/ipod/i) || userAgent.match(/android/i) );
        },

        /**
         * Destroy the instance
         *
         */
        destroy: function () {
            if (this.zoom) {
                this.zoom.unwrap();
            }

            if (this.mouseTrap) {
                this.mouseTrap.unbind();
                this.mouseTrap.remove();
                this.mouseTrap = null;
            }

            if (this.zoomDiv) {
                this.zoomDiv.remove();
                this.zoomDiv = null;
            }

            if (this.options.disableRightClick) {
                $(document).unbind();
            }

            this._removeElements();
            this.options.elements.gallery.unbind();
            this.element.removeData('yith_magnifier');
        }
    };

    $.fn.yith_magnifier = function (options) {

        if (typeof options === 'string') {
            var args = Array.prototype.slice.call(arguments, 1);

            this.each(function () {
                var instance = $.data(this, 'yith_magnifier');
                if (!instance) {
                    $.error("cannot call methods on yith_magnifier prior to initialization; " +
                        "attempted to call method '" + options + "'");
                    return;
                }
                if (!$.isFunction(instance[options]) || options.charAt(0) === "_") {
                    $.error("no such method '" + options + "' for yith_magnifier instance");
                    return;
                }
                instance[options].apply(instance, args);
            });
        } else {
            this.each(function () {
                var instance = $.data(this, 'yith_magnifier');
                if (!instance) {
                    $.data(this, 'yith_magnifier', new $.yith_magnifier(options, this));
                } else {
                    $.error('yith_magnifier already istantiated.');
                }
            });
        }
        return this;
    };

})(window, jQuery);
