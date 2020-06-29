(function ($) {
    $(document).ready(function () {
        $(".ult-carousel-wrapper").each(function () {
            var $this = $(this);
            if ($this.hasClass("ult_full_width")) {
                $this.css('left', 0);
                $this.css('right', 0);
                var rtl = $this.attr('data-rtl');
                var w = $("html").outerWidth();
                var al = 0;
                var bl = $this.offset().left;
                var xl = Math.abs(al - bl);
                var left = xl;
                if (rtl === 'true' || rtl === true)
                    $this.css({"position": "relative", "right": "-" + left + "px", "width": w + "px"});
                else
                    $this.css({"position": "relative", "left": "-" + left + "px", "width": w + "px"});
            }
        });
        $('.ult-carousel-wrapper').each(function (i, carousel) {
            var gutter = $(carousel).data('gutter');
            var id = $(carousel).attr('id');
            if (gutter != '') {
                var css = '<style>#' + id + ' .slick-slide { margin:0 ' + gutter + 'px; } </style>';
                $('head').append(css);
            }
        });

        $('.ult-carousel-wrapper').on('init', function (event) {
            event.preventDefault();

            $('.ult-carousel-wrapper .ult-item-wrap.slick-active').each(function (index, el) {
                $this = $(this);
                $this.addClass($this.data('animation'));
            });
        });

        $('.ult-carousel-wrapper').on('beforeChange', function (event, slick, currentSlide) {
            $inViewPort = $("[data-slick-index='" + currentSlide + "']");
            $inViewPort.siblings().removeClass($inViewPort.data('animation'));
        });

        $('.ult-carousel-wrapper').on('afterChange', function (event, slick, currentSlide, nextSlide) {
            slidesScrolled = slick.options.slidesToScroll;
            slidesToShow = slick.options.slidesToShow;
            centerMode = slick.options.centerMode;
            windowWidth = jQuery( window ).width();
            if ( windowWidth < 1025 ) {
                slidesToShow = slick.options.responsive[0].settings.slidesToShow;
            }
            if ( windowWidth < 769 ) {
                slidesToShow = slick.options.responsive[1].settings.slidesToShow;
            }
            if ( windowWidth < 481 ) {
                slidesToShow = slick.options.responsive[2].settings.slidesToShow;
            }

            $currentParent = slick.$slider[0].parentElement.id;

            slideToAnimate = currentSlide + slidesToShow - 1;

            if (slidesScrolled == 1) {

                if ( centerMode == true ) {
                    animate = slideToAnimate - 2;
                    $inViewPort = $( '#' + $currentParent + " [data-slick-index='" + animate + "']");
                    $inViewPort.addClass($inViewPort.data('animation'));
                } else {
                    $inViewPort = $( '#' + $currentParent + " [data-slick-index='" + slideToAnimate + "']");
                    $inViewPort.addClass($inViewPort.data('animation'));
                }
            } else {

                for (var i = slidesScrolled + currentSlide; i >= 0; i--) {
                    $inViewPort = $( '#' + $currentParent + " [data-slick-index='" + i + "']");
                    $inViewPort.addClass($inViewPort.data('animation'));
                }
            }
        });

        $(window).resize(function () {
            $(".ult-carousel-wrapper").each(function () {
                var $this = $(this);
                if ($this.hasClass("ult_full_width")) {
                    var rtl = $this.attr('data-rtl');
                    $this.removeAttr("style");
                    var w = $("html").outerWidth();
                    var al = 0;
                    var bl = $this.offset().left;
                    var xl = Math.abs(al - bl);
                    var left = xl;
                    if (rtl === 'true' || rtl === true)
                        $this.css({"position": "relative", "right": "-" + left + "px", "width": w + "px"});
                    else
                        $this.css({"position": "relative", "left": "-" + left + "px", "width": w + "px"});
                }
            });
        });

    });
    $(window).load(function () {
        $(".ult-carousel-wrapper").each(function () {
            var $this = $(this);
            if ($this.hasClass("ult_full_width")) {
                $this.css('left', 0);
                $this.css('right', 0);
                var al = 0;
                var bl = $this.offset().left;
                var xl = Math.abs(al - bl);
                var rtl = $this.attr('data-rtl');
                var w = $("html").outerWidth();
                var left = xl;
                if (rtl === 'true' || rtl === true)
                    $this.css({"position": "relative", "right": "-" + left + "px", "width": w + "px"});
                else
                    $this.css({"position": "relative", "left": "-" + left + "px", "width": w + "px"});
            }
        });
    });
    jQuery(document).on('ultAdvancedTabClickedCarousel',function(event, nav){
             $(nav).find(".ult-carousel-wrapper").each(function () {
            var $this = $(this);
            if ($this.hasClass("ult_full_width")) {
                $this.css('left', 0);
                $this.css('right', 0);
                var al = 0;
                var bl = $this.offset().left;
                var xl = Math.abs(al - bl);
                var rtl = $this.attr('data-rtl');
                var w = $("html").outerWidth();
                var left = xl;
                if (rtl === 'true' || rtl === true)
                    $this.css({"position": "relative", "right": "-" + left + "px", "width": w + "px"});
                else
                    $this.css({"position": "relative", "left": "-" + left + "px", "width": w + "px"});
            }
        });
    });
})(jQuery);