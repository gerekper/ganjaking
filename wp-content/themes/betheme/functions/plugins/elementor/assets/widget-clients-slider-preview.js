(function ($) {

  $(window).on("elementor/frontend/init", function () {

    var rtl = $('body').hasClass('rtl');

    function clientsSliderResponsive(slider, max, size) {

      if ( ! max ) max = 5;
      if ( ! size ) size = 380;

      var width = slider.width(),
        count = Math.ceil(width / size);

      if ( count < 1 ) count = 1;
      if ( count > max ) count = max;

      return count;
    };

    elementorFrontend.hooks.addAction("frontend/element_ready/mfn_clients_slider.default", function ($scope, $) {

      $scope.find('.clients_slider_ul').each(function () {

        var slider = $(this);

        slider.slick({
          cssEase: 'ease-out',
          dots: false,
          infinite: true,
          touchThreshold: 10,
          speed: 300,

          prevArrow: '<a class="button button_js slider_prev" href="#"><span class="button_icon"><i class="icon-left-open-big"></i></span></a>',
          nextArrow: '<a class="button button_js slider_next" href="#"><span class="button_icon"><i class="icon-right-open-big"></i></span></a>',
          appendArrows: slider.siblings('.clients_slider_header'),

          rtl: rtl ? true : false,
          autoplay: mfn.slider.clients ? true : false,
          autoplaySpeed: mfn.slider.clients ? mfn.slider.clients : 5000,

          slidesToShow: clientsSliderResponsive(slider, 4),
          slidesToScroll: clientsSliderResponsive(slider, 4)
        });

        // ON | debouncedresize

        $(window).on('debouncedresize', function() {
          slider.slick('slickSetOption', 'slidesToShow', clientsSliderResponsive(slider, 4), false);
          slider.slick('slickSetOption', 'slidesToScroll', clientsSliderResponsive(slider, 4), true);
        });

      });

    });

  });

})(jQuery);
