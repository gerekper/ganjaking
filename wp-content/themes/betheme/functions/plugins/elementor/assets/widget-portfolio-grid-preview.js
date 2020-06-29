(function ($) {

  $(window).on("elementor/frontend/init", function () {

    elementorFrontend.hooks.addAction("frontend/element_ready/mfn_portfolio_grid.default", function ($scope, $) {

      $scope.find('.greyscale .image_wrapper > a, .greyscale .image_wrapper_tiles, .greyscale.portfolio-photo a').each(function () {

        $(this).BlackAndWhite({
          hoverEffect: false,
          intensity: 1
        });

      });

    });

  });

})(jQuery);
