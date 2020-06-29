(function ($) {

  $(window).on("elementor/frontend/init", function () {

    var rtl = $('body').hasClass('rtl'),
      pager = function(el, i) {
        return '<a>' + i + '</a>';
      };

    function portfolioSliderResponsive(slider, max, size) {

      if ( ! max ) max = 5;
      if ( ! size ) size = 380;

      var width = slider.width(),
        count = Math.ceil(width / size);

      if ( count < 1 ) count = 1;
      if ( count > max ) count = max;

      return count;
    };

    elementorFrontend.hooks.addAction("frontend/element_ready/mfn_portfolio_slider.default", function ($scope, $) {

      $scope.find('.portfolio_slider_ul').each(function () {

        var slider = $(this);
        var size = 380;
        var scroll = 5;

        if (slider.closest('.portfolio_slider').data('size')) {
          size = slider.closest('.portfolio_slider').data('size');
        }

        if (slider.closest('.portfolio_slider').data('size')) {
          scroll = slider.closest('.portfolio_slider').data('scroll');
        }

        slider.slick({
          cssEase: 'ease-out',
          dots: false,
          infinite: true,
          touchThreshold: 10,
          speed: 300,

          prevArrow: '<a class="slider_nav slider_prev themebg" href="#"><i class="icon-left-open-big"></i></a>',
          nextArrow: '<a class="slider_nav slider_next themebg" href="#"><i class="icon-right-open-big"></i></a>',

          rtl: rtl ? true : false,
          autoplay: mfn.slider.portfolio ? true : false,
          autoplaySpeed: mfn.slider.portfolio ? mfn.slider.portfolio : 5000,

          slidesToShow: portfolioSliderResponsive(slider, 5, size),
          slidesToScroll: portfolioSliderResponsive(slider, scroll, size)
        });

        // ON | debouncedresize
        $(window).on('debouncedresize', function() {
          slider.slick('slickSetOption', 'slidesToShow', portfolioSliderResponsive(slider, 5, size), false);
          slider.slick('slickSetOption', 'slidesToScroll', portfolioSliderResponsive(slider, scroll, size), true);
        });

      });

    });

  });

})(jQuery);
