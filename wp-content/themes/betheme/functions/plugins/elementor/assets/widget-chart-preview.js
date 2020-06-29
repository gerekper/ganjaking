(function ($) {

  $(window).on("elementor/frontend/init", function () {

      elementorFrontend.hooks.addAction("frontend/element_ready/mfn_chart.default", function ($scope, $) {

        var simple = $('body').hasClass('style-simple'),
          lineW = simple ? 4 : 8;

        $scope.find('.chart').each(function () {

          $(this).easyPieChart({
            animate: 1000,
            lineCap: 'circle',
            lineWidth: lineW,
            size: 140,
            scaleColor: false
          });

        });

      });

  });

})(jQuery);
