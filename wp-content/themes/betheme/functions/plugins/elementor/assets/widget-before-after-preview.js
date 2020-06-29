(function ($) {

  $(window).on("elementor/frontend/init", function () {

      elementorFrontend.hooks.addAction("frontend/element_ready/mfn_before_after.default", function ($scope, $) {

        $scope.find(".twentytwenty-container").each(function () {
          $(this).twentytwenty();
        });

      });

  });

})(jQuery);
