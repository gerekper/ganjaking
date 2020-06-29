(function ($) {

  $(window).on("elementor/frontend/init", function () {

    var rtl = $('body').hasClass('rtl');

    elementorFrontend.hooks.addAction("frontend/element_ready/mfn_portfolio.default", function ($scope, $) {

      $scope.find('.greyscale .image_wrapper > a, .greyscale .image_wrapper_tiles, .greyscale.portfolio-photo a').each(function () {

        $(this).BlackAndWhite({
          hoverEffect: false,
          intensity: 1 // opacity: 0, 0.1, ... 1
        });

      });

      $scope.find('.portfolio_wrapper .isotope:not( .masonry-flat, .masonry-hover, .masonry-minimal )').each(function () {

        $(this).isotope({
          itemSelector: '.isotope-item',
          layoutMode: 'fitRows',
          isOriginLeft: rtl ? false : true
        });

      });

      $scope.find('.portfolio_wrapper .masonry-flat').each(function () {

        $(this).isotope({
          itemSelector: '.isotope-item',
          percentPosition: true,
          masonry: {
            columnWidth: 1
          },
          isOriginLeft: rtl ? false : true
        });

      });

      $scope.find('.isotope.masonry, .isotope.masonry-hover, .isotope.masonry-minimal').each(function () {

        $(this).isotope({
          itemSelector: '.isotope-item',
          layoutMode: 'masonry',
          isOriginLeft: rtl ? false : true
        });

      });

    });

  });

})(jQuery);
