(function ($) {

  $(window).on("elementor/frontend/init", function () {

      elementorFrontend.hooks.addAction("frontend/element_ready/mfn_counter.default", function ($scope, $) {

        $scope.find('.number').each(function () {

          var el = $(this);
          var duration = Math.floor((Math.random() * 1000) + 1000);
          var to = el.attr('data-to');

          $({
            property: 0
          }).animate({
            property: to
          }, {
            duration: duration,
            easing: 'linear',
            step: function() {
              el.text(Math.floor(this.property));
            },
            complete: function() {
              el.text(this.property);
            }
          });

        });

      });

  });

})(jQuery);
