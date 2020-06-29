(function ($) {

  $(window).on("elementor/frontend/init", function () {

    var rtl = $('body').hasClass('rtl'),
      pager = function(el, i) {
        var img = $(el.$slides[i]).find('.single-photo-img').html();
        return '<a>' + img + '</a>';
      };

    elementorFrontend.hooks.addAction("frontend/element_ready/mfn_testimonials.default", function ($scope, $) {

      $scope.find('.testimonials_slider_ul').each(function () {

        var slider = $(this);

        slider.slick({
          cssEase: 'ease-out',
          dots: true,
          infinite: true,
          touchThreshold: 10,
          speed: 300,

          prevArrow: '<a class="button button_js slider_prev" href="#"><span class="button_icon"><i class="icon-left-open-big"></i></span></a>',
          nextArrow: '<a class="button button_js slider_next" href="#"><span class="button_icon"><i class="icon-right-open-big"></i></span></a>',

          adaptiveHeight: true,
          appendDots: slider.siblings('.slider_pager'),
          customPaging: pager,

          rtl: rtl ? true : false,
          autoplay: mfn.slider.testimonials ? true : false,
          autoplaySpeed: mfn.slider.testimonials ? mfn.slider.testimonials : 5000,

          slidesToShow: 1,
          slidesToScroll: 1
        });

      });

    });

  });

})(jQuery);
