/*!
 * Lazy Load - jQuery plugin for lazy loading images
 *
 * Copyright (c) 2007-2015 Mika Tuupola
 *
 * Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * Project home:
 *   http://www.appelsiini.net/projects/lazyload
 *
 * Version:  1.9.7
 *
 */

(function($, window, document, undefined) {
    'use strict';

    var $window = $(window),
        lz_elements = [],
        lz_timer = false,
        lz_action = false;


    var lz_update = function(ev, settings) {
        var counter = 0;
        lz_timer = false;
        if (!lz_elements.length) {
            return;
        }
        lz_elements.forEach(function(obj, index) {
            var $this = $(obj);
            if (!$this.length) {
                //delete lz_elements[index];
                lz_elements.splice(index, 1);
                return;
            }
            if ($this.hasClass('lazy-load-loaded') || (obj.lz_count && obj.lz_count > 3)) {
                lz_elements.splice(index, 1);
                return;
            }
            if (settings.skip_invisible && !$this.is(":visible")) {
                return;
            }
            if (($this.closest('.owl-carousel').length || $this.closest('.swiper-wrapper').length) && !$.abovethetop(obj, settings) && !$.belowthefold(obj, settings)) {
                $this.trigger("appear");
                counter = 0;
            } else if ($.abovethetop(obj, settings) || $.leftofbegin(obj, settings)) {
                /* Nothing. */
            } else if (!$.belowthefold(obj, settings) && !$.rightoffold(obj, settings)) {
                $this.trigger("appear");
                /* if we found an image we'll load, reset the counter */
                counter = 0;
                if (!obj.lz_count) {
                    obj.lz_count = 1;
                } else {
                    obj.lz_count++;
                }
            }/* else {
                if (++counter > settings.failure_limit) {
                    return false;
                }
            }*/
        });
    };

    $.fn.lazyload = function(options) {
        var elements = this,
            $container,
            settings = {
                threshold       : 0,
                failure_limit   : 0,
                event           : "scroll",
                effect          : "show",
                container       : window,
                data_attribute  : "original",
                data_srcset     : "srcset",
                skip_invisible  : false,
                appear          : null,
                load            : null,
                placeholder     : "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAANSURBVBhXYzh8+PB/AAffA0nNPuCLAAAAAElFTkSuQmCC"
            };

        if(options) {
            /* Maintain BC for a couple of versions. */
            if (undefined !== options.failurelimit) {
                options.failure_limit = options.failurelimit;
                delete options.failurelimit;
            }
            if (undefined !== options.effectspeed) {
                options.effect_speed = options.effectspeed;
                delete options.effectspeed;
            }

            $.extend(settings, options);
        }

        /* Cache container as jQuery as object. */
        $container = (settings.container === undefined ||
                      settings.container === window) ? window : settings.container;

        /* Fire one scroll event per scroll. Not one scroll event per image. */
        if (0 === settings.event.indexOf("scroll") && !lz_action) {
            //$container.bind(settings.event, lz_update);
            $container.addEventListener('scroll', function() {
                if (!lz_timer) {
                    lz_timer = theme.requestTimeout(function() {
                        lz_update(null, settings);
                    }, 100);
                }
            }, {passive: true});
        }

        this.each(function() {
            lz_elements.push(this);
            var self = this;
            var $self = $(self);

            self.loaded = false;

            /* If no src attribute given use data:uri. */
            if ($self.attr("src") === undefined || $self.attr("src") === false) {
                if ($self.is("img")) {
                    $self.attr("src", settings.placeholder);
                }
            }

            /* When appear is triggered load original image. */
            $self.one("appear", function(e) {
                if (!this.loaded) {
                    if (settings.appear) {
                        var elements_left = elements.length;
                        settings.appear.call(self, elements_left, settings);
                    }
                    var o_img_attr = $self.attr('data-oi') ? 'data-oi' : ($self.attr('data-' + settings.data_attribute) ? 'data-' + settings.data_attribute : 'data-src');
                    $("<img />")
                        .bind("load", function() {

                            var original = $self.attr(o_img_attr),
                                srcset = $self.attr("data-" + settings.data_srcset);
                            if ($self.is("img")) {
                                if ($self.is(':visible')) {
                                    $self.hide().addClass('no-transition');
                                }
                                $self.attr("src", original);
                                if(srcset) {
                                    $self.attr("srcset", srcset);
                                }
                                if ($self.hasClass('no-transition')) {
                                    $self[settings.effect](settings.effect_speed, function() {
                                      $self.removeClass('no-transition');
                                    });
                                }
                            } else {
                                $self.css("background-image", "url('" + original + "')").removeAttr(o_img_attr);
                            }

                            self.loaded = true;

                            /* Remove image from array so it is not looped next time. */
                            var temp = $.grep(lz_elements, function(element) {
                                return !element.loaded;
                            });
                            lz_elements = temp;

                            if (settings.load) {
                                var elements_left = elements.length;
                                settings.load.call(self, elements_left, settings);
                            }
                        })
                        .attr("src", $self.attr(o_img_attr));
                }
            });

            /* When wanted event is triggered load original image */
            /* by triggering appear.                              */
            if (0 !== settings.event.indexOf("scroll")) {
                $self.bind(settings.event, function() {
                    if (!self.loaded) {
                        $self.trigger("appear");
                    }
                });
            }

            if ($self.is(":hidden") && !self.loaded) {
                $self.trigger("appear");
            }
        });

        /* Check if something appears when window is resized. */
        if (!lz_action) {
            $window.on("resize", function() {
                lz_update(null, settings);
            });
        }

        /* With IOS5 force loading images when navigating with back button. */
        /* Non optimal workaround. */
        if ((/(?:iphone|ipod|ipad).*os 5/gi).test(navigator.appVersion)) {
            $window.bind("pageshow", function(event) {
                if (event.originalEvent && event.originalEvent.persisted) {
                    lz_elements.forEach(function(obj) {
                        $(obj).trigger("appear");
                    });
                }
            });
        }

        /* Force initial check if images should appear. */
        if (!lz_action) {
            $(document).ready(function() {
                lz_update(null, settings);
            });
            $(window).on('load', function() {
                lz_update(null, settings);
            });
        }

        lz_action = true;

        return this;
    };

    /* Convenience methods in jQuery namespace.           */
    /* Use as  $.belowthefold(element, {threshold : 100, container : window}) */

    $.belowthefold = function(element, settings) {
        var fold;

        if (settings.container === undefined || settings.container === window) {
            fold = (window.innerHeight ? window.innerHeight : $window.height()) + $window.scrollTop();
        } else {
            fold = $(settings.container).offset().top + $(settings.container).height();
        }

        return fold <= $(element).offset().top - settings.threshold;
    };

    $.rightoffold = function(element, settings) {
        var fold;

        if (settings.container === undefined || settings.container === window) {
            fold = $window.width() + $window.scrollLeft();
        } else {
            fold = $(settings.container).offset().left + $(settings.container).width();
        }

        return fold <= $(element).offset().left - settings.threshold;
    };

    $.abovethetop = function(element, settings) {
        var fold;

        if (settings.container === undefined || settings.container === window) {
            fold = $window.scrollTop();
        } else {
            fold = $(settings.container).offset().top;
        }

        return fold >= $(element).offset().top + settings.threshold  + $(element).height();
    };

    $.leftofbegin = function(element, settings) {
        var fold;

        if (settings.container === undefined || settings.container === window) {
            fold = $window.scrollLeft();
        } else {
            fold = $(settings.container).offset().left;
        }

        return fold >= $(element).offset().left + settings.threshold + $(element).width();
    };

    $.inviewport = function(element, settings) {
         return !$.rightoffold(element, settings) && !$.leftofbegin(element, settings) &&
                !$.belowthefold(element, settings) && !$.abovethetop(element, settings);
     };

    /* Custom selectors for your convenience.   */
    /* Use as $("img:below-the-fold").something() or */
    /* $("img").filter(":below-the-fold").something() which is faster */

    $.extend($.expr[":"], {
        "below-the-fold" : function(a) { return $.belowthefold(a, {threshold : 0}); },
        "above-the-top"  : function(a) { return !$.belowthefold(a, {threshold : 0}); },
        "right-of-screen": function(a) { return $.rightoffold(a, {threshold : 0}); },
        "left-of-screen" : function(a) { return !$.rightoffold(a, {threshold : 0}); },
        "in-viewport"    : function(a) { return $.inviewport(a, {threshold : 0}); },
        /* Maintain BC for couple of versions. */
        "above-the-fold" : function(a) { return !$.belowthefold(a, {threshold : 0}); },
        "right-of-fold"  : function(a) { return $.rightoffold(a, {threshold : 0}); },
        "left-of-fold"   : function(a) { return !$.rightoffold(a, {threshold : 0}); }
    });

})(jQuery, window, document);