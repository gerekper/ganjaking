(function ($) {

  $(window).on("elementor/frontend/init", function () {

      elementorFrontend.hooks.addAction("frontend/element_ready/mfn_blog.default", function ($scope, $) {

        $(window).trigger('resize');

        $scope.find('.greyscale .image_wrapper > a, .greyscale .image_wrapper_tiles').each(function () {

          $(this).BlackAndWhite({
            hoverEffect: false,
            intensity: 1 // opacity: 0, 0.1, ... 1
          });

        });

      });

  });

})(jQuery);
