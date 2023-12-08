/**
 * Start EDD Category carousel widget script
 */

(function ($, elementor) {
  "use strict";

  var widgetProductReviewCarousel = function ($scope, $) {
    var $ProductReviewCarousel = $scope.find(".ep-edd-product-review-carousel");

    if (!$ProductReviewCarousel.length) {
      return;
    }

    var $ProductReviewCarouselContainer = $ProductReviewCarousel.find(".swiper-carousel"),
      $settings = $ProductReviewCarousel.data("settings");

    const Swiper = elementorFrontend.utils.swiper;
    initSwiper();
    async function initSwiper() {
      var swiper = await new Swiper($ProductReviewCarouselContainer, $settings); // this is an example
      if ($settings.pauseOnHover) {
        $($ProductReviewCarouselContainer).hover(
          function () {
            this.swiper.autoplay.stop();
          },
          function () {
            this.swiper.autoplay.start();
          }
        );
      }
    }
  };

  jQuery(window).on("elementor/frontend/init", function () {
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/bdt-edd-product-review-carousel.default",
      widgetProductReviewCarousel
    );
  });
})(jQuery, window.elementorFrontend);

/**
 * End twitter carousel widget script
 */
