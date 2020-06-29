(function($) {

  /* globals jQuery */

  "use strict";

  function mfnFieldTabs() {

    // sortable init

    $('#mfn-builder').on('mouseenter', '.tabs-ul', function(e) {
      $('.tabs-ul').sortable({
        handle: 'label',
        cursor: 'move',
        opacity: 0.9
      });
    });

    // add new tab

    $('#mfn-builder').on('click', '.mfn-add-tab', function(e) {

      // increase tabs counter

      var count = $(this).siblings('.mfn-tabs-count');
      count.val(count.val() * 1 + 1);

      var name = $(this).attr('rel-name');
      var wrapper = $(this).siblings('.tabs-ul');
      var newTab = wrapper.children('li.tabs-default').clone(true);

      newTab.removeClass('tabs-default');
      newTab.children('input').attr('name', name + '[title][]');
      newTab.children('textarea').attr('name', name + '[content][]');

      wrapper
        .append(newTab)
        .children('li:last')
        .fadeIn(500);

    });

    // remove tab

    $('#mfn-builder').on('click', '.mfn-remove-tab', function(e) {
      e.preventDefault();

      // decrease tabs counter

      var count = $(this).parents('td').children('.mfn-tabs-count');
      count.val(count.val() * 1 - 1);

      $(this).parent().fadeOut(300, function() {
        $(this).remove();
      });

    });

  }

  /**
   * $(document).ready
   * Specify a function to execute when the DOM is fully loaded.
   */

  $(function($) {
    mfnFieldTabs();
  });

})(jQuery);
