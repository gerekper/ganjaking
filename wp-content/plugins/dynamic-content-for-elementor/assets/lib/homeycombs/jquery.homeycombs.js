(function ($) {
    /**
     * jQuery Honeycombs
     * jQuery plugin for displaying images in a responsive honeycomb/hexagon grid with caption and anchor
     * 
     * https://github.com/nasirkhan/honeycombs
     * 
     */
    $.fn.honeycombs = function (options) {

        // Establish our default settings
        var settings = $.extend({
            combWidth: 200,
            margin: 10
        }, options);

        function initialise(element) {

            $(element).addClass('honeycombs-wrapper');

            var width = 0;
            var combWidth = 0;
            var combHeight = 0;
            var num = 0;
            var $wrapper = null;

            /**
             * Build the dom
             */
            function buildHtml() {
                // add the 2 other boxes
                $(element).find('.comb').wrapAll('<div class="honeycombs-inner-wrapper"></div>');
                $wrapper = $(element).find('.honeycombs-inner-wrapper');



                $(element).find('.comb').append('<div class="hex_l"></div>');
                $(element).find('.hex_l').append('<div class="hex_r"></div>');
                $(element).find('.hex_r').append('<div class="hex_inner"></div>');

                $(element).find('.hex_inner').append('<div class="inner_span"><div class="inner-text"></div></div>');

                num = 0;

                $(element).find('.comb').each(function () {
                    num = num + 1;
                    var image = $(this).find('img').attr('src');
                    var css = 'url("' + image + '") ';
                    var a_href = $(this).find('a').attr('href');
                    var a_class = $(this).find('a').attr('class');

                    if (typeof (a_href) != "undefined" && a_href !== null) {
                        if (typeof (a_class) != "undefined" && a_class !== null) {
                            $(this).find('.hex_inner').wrapAll('<a class="' + a_class + '" href="' + a_href + '"></a>');
                        }
                        else {
                            $(this).find('.hex_inner').wrapAll('<a class="" href="' + a_href + '"></a>');
                        }
                    }

                    $(this).find('.hex_inner').attr('style', 'background-image: ' + css);
                    $(this).find('.hex_inner').attr('style', 'background-image: ' + css);

                    if ($(this).find('span').length > 0) {
                        $(this).find('.inner_span .inner-text').html($(this).find('span').html());
                    } else {
                        $(this).find('.inner_span').remove();
                    };

                });

                $(element).find('img, span, .inner_span').hide();
            }

            /**
             * Update all scale values
             */
            function updateScales() {
                combWidth = settings.combWidth;
                combHeight = (Math.sqrt(3) * combWidth) / 2;
                edgeWidth = combWidth / 2;

                $(element).find('.comb').width(combWidth).height(combHeight);
                $(element).find('.hex_l, .hex_r').width(combWidth).height(combHeight);
                $(element).find('.hex_inner').width(combWidth).height(combHeight);
            }

            /**
             * update css classes
             */
            function reorder(animate) {

                updateScales();
                width = $(element).width();

                newWidth = (num / 1.5) * settings.combWidth;

                if (newWidth < width) {
                    width = newWidth;
                }

                $wrapper.width(width);

                var row = 0; // current row
                var upDown = 1; // 1 is down
                var left = 0; // pos left
                var top = 0; // pos top

                var cols = 0;

                $(element).find('.comb').each(function (index) {

                    top = (row * (combHeight + settings.margin)) + (upDown * (combHeight / 2 + (settings.margin / 2)));

                    if (animate == true) {
                        $(this).stop(true, false);
                        $(this).animate({ 'left': left, 'top': top });
                    } else {
                        $(this).css('left', left).css('top', top);
                    }

                    left = left + (combWidth - combWidth / 4 + settings.margin);
                    upDown = (upDown + 1) % 2;

                    if (row == 0) {
                        cols = cols + 1;
                    }

                    if (left + combWidth > width) {
                        left = 0;
                        row = row + 1;
                        upDown = 1;
                    }
                });

                $wrapper
                    .width(cols * (combWidth / 4 * 3 + settings.margin) + combWidth / 4)
                    .height((row + 1) * (combHeight + settings.margin) + combHeight / 2);
            }

            $(window).resize(function () {
                reorder(true);
            });

            $(element).find('.comb').mouseenter(function () {
                $(this).find('.inner_span').stop(true, true);
                $(this).find('.inner_span').fadeIn();
            });

            $(element).find('.comb').mouseleave(function () {
                $(this).find('.inner_span').stop(true, true);
                $(this).find('.inner_span').fadeOut();
            });

            buildHtml();
            reorder(false);
        }

        return this.each(function () {
            initialise(this);
        });

    }

}(jQuery));
