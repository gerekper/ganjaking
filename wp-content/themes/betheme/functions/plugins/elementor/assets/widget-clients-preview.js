(function ($) {

  $(window).on("elementor/frontend/init", function () {

      elementorFrontend.hooks.addAction("frontend/element_ready/mfn_clients.default", function ($scope, $) {

        $scope.find('.greyscale .client_wrapper .gs-wrapper').each(function () {

          $(this).BlackAndWhite({
            hoverEffect: false,
            intensity: 1 // opacity: 0, 0.1, ... 1
          });

        });

      });

  });

})(jQuery);
