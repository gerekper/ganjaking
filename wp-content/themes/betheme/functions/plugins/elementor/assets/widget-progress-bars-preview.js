(function ($) {

  $(window).on("elementor/frontend/init", function () {

    elementorFrontend.hooks.addAction("frontend/element_ready/mfn_progress_bars.default", function ($scope, $) {

      $scope.find('.bars_list').each(function () {

        $(this).addClass('hover');

      });


    });

  });

})(jQuery);
