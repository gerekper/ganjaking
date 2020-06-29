/* Ultimate Carousel */
jQuery(document).ready(function($) {
    'use strict';
    $('.porto-carousel-wrapper').each(function (i, carousel) {
        var gutter = $(carousel).data('gutter');
        var id = $(carousel).attr('id');
        if (gutter != '') {
            var css = '<style>#' + id + ' .slick-slide { margin:0 ' + gutter + 'px; } </style>';
            $('head').append(css);
        }
    });

    $('.porto-carousel-wrapper').on('init', function (event) {
        event.preventDefault();

        $('.porto-carousel-wrapper .porto-item-wrap.slick-active').each(function (index, el) {
            var $this = $(this);
            $this.addClass($this.data('animation'));
        });
    });

    $('.porto-carousel-wrapper').on('beforeChange', function (event, slick, currentSlide) {
        var $inViewPort = $("[data-slick-index='" + currentSlide + "']");
        $inViewPort.siblings().removeClass($inViewPort.data('animation'));
    });

    $('.porto-carousel-wrapper').on('afterChange', function (event, slick, currentSlide, nextSlide) {
        var $inViewPort,
            slidesScrolled = slick.options.slidesToScroll,
            slidesToShow = slick.options.slidesToShow,
            centerMode = slick.options.centerMode,
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

        var $currentParent = slick.$slider[0].parentElement.id,
            slideToAnimate = currentSlide + slidesToShow - 1;

        if (slidesScrolled == 1) {

            if ( centerMode == true ) {
                var animate = slideToAnimate - 2;
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
});