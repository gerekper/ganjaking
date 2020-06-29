(function ($) {

  $(window).on("elementor/frontend/init", function () {

      elementorFrontend.hooks.addAction("frontend/element_ready/mfn_countdown.default", function ($scope, $) {

        $scope.find('.downcount').each(function () {

          $(this).downCount({
            date: $(this).attr('data-date'),
            offset: $(this).attr('data-offset')
          });

        });

      });

  });

})(jQuery);
