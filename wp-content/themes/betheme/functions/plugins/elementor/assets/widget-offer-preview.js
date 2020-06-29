(function ($) {

  $(window).on("elementor/frontend/init", function () {

    var rtl = $('body').hasClass('rtl');

    elementorFrontend.hooks.addAction("frontend/element_ready/mfn_offer.default", function ($scope, $) {

      $scope.find('.offer_ul').each(function () {

        var slider = $(this);

        slider.slick({
          cssEase: 'ease-out',
          dots: false,
          infinite: true,
          touchThreshold: 10,
          speed: 300,

          prevArrow: '<a class="button button_large button_js slider_prev" href="#"><span class="button_icon"><i class="icon-up-open-big"></i></span></a>',
          nextArrow: '<a class="button button_large button_js slider_next" href="#"><span class="button_icon"><i class="icon-down-open-big"></i></span></a>',

          adaptiveHeight: true,
          //customPaging 	: pager,

          rtl: rtl ? true : false,
          autoplay: mfn.slider.offer ? true : false,
          autoplaySpeed: mfn.slider.offer ? mfn.slider.offer : 5000,

          slidesToShow: 1,
          slidesToScroll: 1
        });

        // Pagination | Show (css)

        slider.siblings('.slider_pagination').addClass('show');

        // Pager | Set slide number after change

        slider.on('afterChange', function(event, slick, currentSlide, nextSlide) {
          slider.siblings('.slider_pagination').find('.current').text(currentSlide + 1);
        });

      });

    });

  });

})(jQuery);
