(function ($) {

  $(window).on("elementor/frontend/init", function () {

    var rtl = $('body').hasClass('rtl');

    elementorFrontend.hooks.addAction("frontend/element_ready/mfn_gallery.default", function ($scope, $) {

      $scope.find('.gallery').each(function () {

        var el = $(this);
        var id = el.attr('id');

        $('> br', el).remove();

        $('.gallery-icon > a', el)
          .wrap('<div class="image_frame scale-with-grid"><div class="image_wrapper"></div></div>')
          .prepend('<div class="mask"></div>')
          .children('img')
          .css('height', 'auto')
          .css('width', '100%');

        // lightbox | link to media file

        if (el.hasClass('file')) {
          $('.gallery-icon a', el)
            .attr('rel', 'prettyphoto[' + id + ']')
            .attr('data-elementor-lightbox-slideshow', id); // FIX: elementor lightbox gallery
        }

        // isotope for masonry layout

        if (el.hasClass('masonry')) {

          el.isotope({
            itemSelector: '.gallery-item',
            layoutMode: 'masonry',
            isOriginLeft: rtl ? false : true
          });

        }

      });


    });

  });

})(jQuery);
