(function ($) {

  $(window).on("elementor/frontend/init", function () {

    elementorFrontend.hooks.addAction("frontend/element_ready/mfn_accordion.default", function ($scope, $) {

      $scope.find('.mfn-acc').each(function () {

        var el = $(this);

        if( el.hasClass('openAll') ){

          // show all

          el.find('.question')
            .addClass("active")
            .children(".answer")
            .show();

        } else {

          // show one

          var activeTab = el.attr('data-active-tab');

          if( el.hasClass('open1st') ){
            activeTab = 1;
          }

          if( activeTab ){
            el.find('.question').eq(activeTab - 1)
              .addClass("active")
              .children(".answer")
              .show();
          }

        }

        $('.question > .title', el).on('click', function() {

          if ($(this).parent().hasClass("active")) {

            $(this).parent().removeClass("active").children(".answer").slideToggle(100);

          } else {

            if (!$(this).closest('.mfn-acc').hasClass('toggle')) {
              $(this).parents(".mfn-acc").children().each(function() {
                if ($(this).hasClass("active")) {
                  $(this).removeClass("active").children(".answer").slideToggle(100);
                }
              });
            }
            $(this).parent().addClass("active");
            $(this).next(".answer").slideToggle(100);

          }

        });

      });


    });

  });

})(jQuery);
