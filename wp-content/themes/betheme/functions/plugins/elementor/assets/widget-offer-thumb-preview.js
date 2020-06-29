(function ($) {

  $(window).on("elementor/frontend/init", function () {

    var rtl = $('body').hasClass('rtl'),
      pager = function(el, i) {
        var img = $(el.$slides[i]).children('.thumbnail').html();
        return '<a>' + img + '</a>';
      };

    elementorFrontend.hooks.addAction("frontend/element_ready/mfn_offer_thumb.default", function ($scope, $) {

      $scope.find('.offer_thumb_ul').each(function () {

        var slider = $(this);

        slider.slick({
          cssEase: 'ease-out',
          arrows: false,
          dots: true,
          infinite: true,
          touchThreshold: 10,
          speed: 300,

          adaptiveHeight: true,
          appendDots: slider.siblings('.slider_pagination'),
          customPaging: pager,

          rtl: rtl ? true : false,
          autoplay: mfn.slider.offer ? true : false,
          autoplaySpeed: mfn.slider.offer ? mfn.slider.offer : 5000,

          slidesToShow: 1,
          slidesToScroll: 1
        });

      });

    });

  });

})(jQuery);
