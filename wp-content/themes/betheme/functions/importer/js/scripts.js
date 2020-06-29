(function($) {

	/* globals jQuery */

  "use strict";

  $(function() {

    var importer = $('.mfn-demo-data'),
			overlay = $('#mfn-overlay'),
			popup = $('#mfn-demo-popup'),
			searchTimer;

    /**
     * Filters
     * Filter demos by Category
     *
     * @param el object
     */

    function filter(el) {

      var filter = el.attr('data-filter');
      var demos = $('.demos', importer);

      $('.demo-search input', importer).val('');

      el.addClass('active')
        .siblings().removeClass('active');

      if (filter == '*') {

        $('.item', demos).hide().stop(true, true).fadeIn();

      } else {

        $('.item', demos).hide();
        $('.item.category-' + filter, demos).stop(true, true).fadeIn();

      }
    }

    $('.filters', importer).on('click', 'li', function() {
      filter($(this));
    });

    /**
     * Search
     *
     * @param el object
     */

    function search(el) {

      var filter = el.val().replace('&', '').replace(/ /g, '').toLowerCase();
      var demos = $('.demos', importer);

      if (filter.length) {

        $('.item', demos).hide();
        $('.item[data-id *= ' + filter + ']', demos).stop(true, true).fadeIn();

        $('.filters li.active', importer).removeClass('active');

      } else {

        $('.item', demos).hide().stop(true, true).fadeIn(200);
        $('.filters li:first', importer).addClass('active');

      }

    }

    $('.demo-search input', importer).on('keyup', function() {

			var input = $(this);

			clearTimeout(searchTimer);
			searchTimer = setTimeout(function(){
				search(input);
			}, 400, input);

    });

    /**
     * Item | Open
     * Open item details on click
     */

    $('.demos', importer).on('click', '.item', function(e) {

      overlay.fadeIn(200);

      $(this).addClass('active')
        .siblings().removeClass('active');

      var el = $('.item-inner', this);

      // scroll if active item is not fully visible

      setTimeout(function() {

        var elT = el.offset().top;
        var elB = el.offset().top + el.outerHeight();

        var scrT = $(window).scrollTop() + $('#wpadminbar').height();
        var scrB = $(window).scrollTop() + $(window).height();

        if (elT < scrT) {

          // offset top

          jQuery('html, body').animate({
            scrollTop: elT - $('#wpadminbar').height() - 5
          }, 300);

        } else if (elB > scrB) {

          // offset bottom

          var diff = elB - scrB;
          var scroll = scrT + diff + 5;

          if (scroll > elT) {
            scroll = elT;
          }

          jQuery('html, body').animate({
            scrollTop: scroll - $('#wpadminbar').height() - 5
          }, 200);

        }

      }, 400);

    });

    /**
     * Item | Close
     *
     * Close item detail on close button click
     */

    $('.demos', importer).on('click', '.close', function(e) {

      overlay.fadeOut(200);

      $('.demos .item.active').removeClass('active');

      e.stopPropagation();
    });

    /**
     * Item | Import
     * Popup | Open
     * Open import popup on import button click
     */

    $('.demos', importer).on('click', '.mfn-button-import', function(e) {

      var item = $(this).closest('.item');

      $('#input-demo', importer).val(item.data('id'));

      $('.item-image', popup).css('background-position', item.find('.item-image').css('background-position'));
      $('.item-title', popup).text(item.data('name'));

      // animations

      $('.demos .item.active').removeClass('active');
      e.stopPropagation();

      // reset to default

      $(popup).removeClass('slider slider-active');
      $('.popup-step', popup).hide().first().show();

      $('.popup-step.step-2 input', popup).removeAttr('checked');
      $('.popup-step.step-2 input.checked', popup).attr('checked', 'checked');

      // revolution slider demo installer

      var slider = $('.plugin-rev', item);
      if (slider.length) {
        if ($('span.is-active').length) {
          popup.addClass('slider-active');
        } else {
          popup.addClass('slider');
        }

      }

      // open popup

      popup.addClass('active');

      // reset database reset steps

      $('.db-reset', popup).removeClass('confirm')
      $('.db-reset .checkbox-reset', popup).prop('checked', false);
      $('.db-reset .mfn-button-reset-confirm', popup).addClass('disabled').removeClass('success').text('Reset now');

    });

    /**
     * Popup | Close
     * Close popup on cakcel button click
     */

    popup.on('click', '.mfn-button-cancel', function() {

      popup.removeClass('active');
      overlay.fadeOut(200);

    });

    /**
     * Popup | Next screen
     * Change step screen
     */

    popup.on('click', '.mfn-button-next', function() {

      var step = $(this).closest('.popup-step');
      step.hide().next().fadeIn(200);

    });

    /**
     * Popup | Database reset
     */

    popup.on('click', '.mfn-button-reset', function() {

      $('.db-reset', popup).addClass('confirm');

    });

    /**
     * Popup | Database reset, checkbox
     */

    popup.on('change', '.checkbox-reset', function() {

      var button = $('.db-reset .mfn-button-reset-confirm', popup);

      if ($(this).is(':checked')) {
        button.removeClass('disabled');
      } else {
        button.addClass('disabled');
      }

    });

    /**
     * Popup | Database reset, confirm
     */

    popup.on('click', '.mfn-button-reset-confirm', function() {

      var el = $(this);
      var ajax = el.attr('data-ajax');
      var nonce = $('input[name="mfn-importer-nonce"]', importer).val();

      if( el.hasClass('disabled') ){
        return false;
      }

      el.text('Resetting...');

      var post = {
        action: 'mfn_db_reset',
        'mfn-importer-nonce': nonce
      };

      $.post(ajax, post, function(data) {

        el.text(data).addClass('disabled success');

        setTimeout(function(){
          el.closest('.popup-step').find('.mfn-button-next').click();
        }, 1000);

      });

    });

    /**
     * Popup | Import Options
     * Activate checkboxes for selected radiobox, etc.
     */

    $('.import-options', popup).on('click', 'label', function() {

      var parent = $(this).closest('.import-options');

      parent.addClass('active')
        .siblings().removeClass('active');

      $('input.radio-type', parent).attr('checked', 'checked');

    });

    /**
     * Popup | Submit
     * Submit form
     */

    popup.on('click', '.mfn-button-submit', function() {

      var parent = $(this).closest('.popup-step');

      var type = $('.radio-type:checked', parent).val();
      $('#input-type', importer).val(type);

      var data = $('.radio-data:checked', parent).val();
      $('#input-data', importer).val(data);

      var attachments = $('.checkbox-attachments:checked', parent).val();
      $('#input-attachments', importer).val(attachments);

      var slider = $('.checkbox-slider:checked', parent).val();
      $('#input-slider', importer).val(slider);

      // next step
      $(this).closest('.popup-step').hide().next().fadeIn(200);

      // disable popup close
      overlay.unbind('click');

      // form submit
      $('#form-submit', importer).trigger('click');

    });

    /**
     * Item / Popup | Close
     * Close overlay, item details, popup on overlay click
     */

    overlay.on('click', function() {

      $(this).fadeOut(200);

      popup.removeClass('active');
      $('.demos .item.active').removeClass('active');

    });


    /**
     * Keyboard navigation
     * Previous / next item. Use keyboard left & right arrows
     */

    $('body').on('keydown', function(event) {
      if ($('.item.active', importer).length) {

        // <- arrow
        if (event.keyCode == 37) {
          var prev = $('.item.active', importer).prev();
          if (prev.length) {
            $('.item.active', importer).removeClass('active');
            prev.addClass('active');
          }
        }

        // -> arrow
        if (event.keyCode == 39) {
          var next = $('.item.active', importer).next();
          if (next.length) {
            $('.item.active', importer).removeClass('active');
            next.addClass('active');
          }
        }

        // ESC
        if (event.keyCode == 27) {
          overlay.trigger('click');
        }

      }

    });


  });

})(jQuery);
