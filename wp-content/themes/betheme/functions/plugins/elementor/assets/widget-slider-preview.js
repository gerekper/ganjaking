(function ($) {

  $(window).on("elementor/frontend/init", function () {

    var rtl = $('body').hasClass('rtl'),
      pager = function(el, i) {
        return '<a>' + i + '</a>';
      };

    function sliderResponsive(slider, max, size) {

      if ( ! max ) max = 5;
      if ( ! size ) size = 380;

      var width = slider.width(),
        count = Math.ceil(width / size);

      if ( count < 1 ) count = 1;
      if ( count > max ) count = max;

      return count;
    };

    elementorFrontend.hooks.addAction("frontend/element_ready/mfn_slider.default", function ($scope, $) {

      $scope.find('.content_slider_ul').each(function () {

        var slider = $(this);
        var count = 1;
        var centerMode = false;

        if (slider.closest('.content_slider').hasClass('carousel')) {
          count = sliderResponsive(slider);

          $(window).on('debouncedresize', function() {
            slider.slick('slickSetOption', 'slidesToShow', sliderResponsive(slider), false);
            slider.slick('slickSetOption', 'slidesToScroll', sliderResponsive(slider), true);
          });
        }

        if (slider.closest('.content_slider').hasClass('center')) {
          centerMode = true;
        }

        slider.slick({
          cssEase: 'cubic-bezier(.4,0,.2,1)',
          dots: true,
          infinite: true,
          touchThreshold: 10,
          speed: 300,

          centerMode: centerMode,
          centerPadding: '20%',

          prevArrow: '<a class="button button_js slider_prev" href="#"><span class="button_icon"><i class="icon-left-open-big"></i></span></a>',
          nextArrow: '<a class="button button_js slider_next" href="#"><span class="button_icon"><i class="icon-right-open-big"></i></span></a>',

          adaptiveHeight: true,
          appendDots: slider.siblings('.slider_pager'),
          customPaging: pager,

          rtl: rtl ? true : false,
          autoplay: mfn.slider.slider ? true : false,
          autoplaySpeed: mfn.slider.slider ? mfn.slider.slider : 5000,

          slidesToShow: count,
          slidesToScroll: count
        });

      });

    });

  });

})(jQuery);
