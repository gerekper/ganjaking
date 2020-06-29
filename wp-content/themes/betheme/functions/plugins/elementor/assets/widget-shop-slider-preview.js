(function ($) {

  $(window).on("elementor/frontend/init", function () {

    var rtl = $('body').hasClass('rtl'),
      pager = function(el, i) {
        return '<a>' + i + '</a>';
      };

    function shopSliderResponsive(slider, max, size) {

      if ( ! max ) max = 5;
      if ( ! size ) size = 380;

      var width = slider.width(),
        count = Math.ceil(width / size);

      if ( count < 1 ) count = 1;
      if ( count > max ) count = max;

      return count;
    };

    elementorFrontend.hooks.addAction("frontend/element_ready/mfn_shop_slider.default", function ($scope, $) {

      $scope.find('.shop_slider_ul').each(function () {

        var slider = $(this);
        var slidesToShow = 4;

        var count = slider.closest('.shop_slider').data('count');
        if (slidesToShow > count) {
          slidesToShow = count;
          if (slidesToShow < 1) {
            slidesToShow = 1;
          }
        }

        slider.slick({
          cssEase: 'ease-out',
          dots: true,
          infinite: true,
          touchThreshold: 10,
          speed: 300,

          prevArrow: '<a class="button button_js slider_prev" href="#"><span class="button_icon"><i class="icon-left-open-big"></i></span></a>',
          nextArrow: '<a class="button button_js slider_next" href="#"><span class="button_icon"><i class="icon-right-open-big"></i></span></a>',
          appendArrows: slider.siblings('.blog_slider_header'),

          appendDots: slider.siblings('.slider_pager'),
          customPaging: pager,

          rtl: rtl ? true : false,
          autoplay: mfn.slider.shop ? true : false,
          autoplaySpeed: mfn.slider.shop ? mfn.slider.shop : 5000,

          slidesToShow: shopSliderResponsive(slider, slidesToShow),
          slidesToScroll: shopSliderResponsive(slider, slidesToShow)
        });

        // ON | debouncedresize

        $(window).on('debouncedresize', function() {
          slider.slick('slickSetOption', 'slidesToShow', shopSliderResponsive(slider, slidesToShow), false);
          slider.slick('slickSetOption', 'slidesToScroll', shopSliderResponsive(slider, slidesToShow), true);
        });

      });

    });

  });

})(jQuery);
