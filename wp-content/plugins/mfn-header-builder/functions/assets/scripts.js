/**
 * BeTheme Header Builder
 *
 * @package Betheme
 * @author Muffin group
 * @link https://muffingroup.com
 */

(function($) {

  /* globals jQuery */

  "use strict";

  var Mfn_HB = (function($) {

    /**
     * Init
     */

    function init() {

      menu.init();
      menu.onePage();

      bind();

    }

    /**
     * Menu related functions
     */

    var menu = {

      init: function() {

        var mobileInit = '';

        $('.mhb-menu ul.menu').each(function(index) {

          if ($(this).parent().hasClass('tabletMobile')) {
            mobileInit = 959;
          } else {
            mobileInit = 768;
          }

          $(this).mfnMenu({
            addLast: false,
            arrows: false,
            responsive: true,
            mobileInit: mobileInit
          });

        });

      },

      toggle: function(button) {

        var menu = $(button).siblings('ul.menu');

        menu.stop(true, true).slideToggle(200);

      },

      onePage: function() {

        if( ! $('body').hasClass('one-page') ){
          return false;
        }

        $('.mhb-menu ul.menu').each(function(index) {

          var menu = $(this);

          // add attr [data-hash] & [data-id]

          $('a[href]', menu).each(function() {

            var url = $(this).attr('href');
            if (url && url.split('#')[1]) {

              // data-hash
              var hash = '#' + url.split('#')[1];
              if (hash && $(hash).length) {
                // check if element with specified ID exists
                $(this).attr('data-hash', hash);
                $(hash).attr('data-id', hash);
              }

            }

          });

          // click

          $('a[data-hash]', menu).on('click', function(e) {

            e.preventDefault();

            var currentView = $('.mhb-view').filter(':visible');
            var hash = $(this).attr('data-hash');

            hash = '[data-id="' + hash + '"]';

            // offset

            var headerH = currentView.height();
            var adminBarH = $('#wpadminbar').height();

            var offset = headerH + adminBarH;

            // animate scroll

            $('html, body').animate({
              scrollTop: $(hash).offset().top - offset
            }, 500);

          });

        });

      }

    };

    /**
     * Retina logo
     */

    function retinaLogo() {

      if (window.devicePixelRatio > 1) {

        $('.mhb-logo img[data-retina]').each(function() {

          var height = 0;

          if (!$(this).data('retina')) {
            return false;
          }

          if (!$(this).attr('height')) {
            height = $(this).height();
          }

          $(this).attr('src', $(this).data('retina'));

          if (height) {
            $(this).attr('height', height);
          }

        });

      }

    }

    /**
     * Sticky header
     */

    var sticky = {

      init: function() {

        var sticky_wrapper = $('.mhb-grid');
        var start_y = 0;
        var window_y = $(window).scrollTop();
        var current_view = $('.mhb-view').filter(':visible');

        if (window_y > start_y) {

          if (!sticky_wrapper.hasClass('is-sticky')) {
            sticky_wrapper.addClass('is-sticky');

            this.placeholderHeight(current_view);
          }

        } else {

          if (sticky_wrapper.hasClass('is-sticky')) {
            sticky_wrapper.removeClass('is-sticky');
          }

        }

      },

      placeholderHeight: function(current_view) {

        if( current_view.hasClass('on-top') ){
          $('.mhb-placeholder').height(0);
          return false;
        }

        $('.mhb-placeholder').height(current_view.height());

      }

    };

    /**
     * Search icon
     */

    var search = {

      toggle: function(search_wrapper) {
        $(search_wrapper).next('form').fadeToggle()
          .find('.field').focus();
      }

    };

    /**
     * Bind
     */

    function bind() {

      // menu | menu toggle click | mobile menu open

      $('.mhb-menu').on('click', '.mobile-menu-toggle', function(e) {
        e.preventDefault();
        menu.toggle(this);
      });

      // search | icon click | form open

      $('.mhb-extras').on('click', '.search-icon', function(e) {
        e.preventDefault();
        search.toggle(this);
      });

      // window.scroll

      $(window).scroll(function() {
        sticky.init();
      });

      // window.load

      $(window).load(function() {
        retinaLogo();
      });

    }

    /**
     * Return
     */

    return {
      init: init

    };

  })(jQuery);

  /**
   * $(document).ready
   * Specify a function to execute when the DOM is fully loaded.
   */

  $(function() {
    Mfn_HB.init();
  });

})(jQuery);
