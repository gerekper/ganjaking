(function ($) {

  $(window).on("elementor/frontend/init", function () {

    elementorFrontend.hooks.addAction("frontend/element_ready/mfn_tabs.default", function ($scope, $) {

      $scope.find('.jq-tabs').each(function () {

        $(this).tabs();

      });


    });

  });

})(jQuery);
