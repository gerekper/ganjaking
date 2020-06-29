(function($) {

  /* globals jQuery */

  "use strict";

  function mfnFieldMultiText() {

    // remove

    $('#mfn-wrapper').on('click', '.multi-text-remove', function(e) {

      e.preventDefault();

      $(this).prev('input[type="text"]').val('');

      $(this).parent().fadeOut(300, function() {
        $(this).remove();
      });

    });

    // add new

    $('#mfn-wrapper').on('click', '.multi-text-btn', function(e) {

      var clone = $('#' + $(this).attr('rel-id') + ' li.multi-text-default').clone(true);
      var value = $(this).siblings('.multi-text-add').val();

      if (value) {

        $(this).prev('input[type="text"]').val('');

        $('#' + $(this).attr('rel-id')).append(clone);

        $('#' + $(this).attr('rel-id') + ' li:last-child')
          .fadeIn(500)
          .removeClass('multi-text-default')
          .children('input')
          .val(value)
          .attr('name', $(this).attr('rel-name'))
          .parent().children('span')
          .text(value);
      }

    });

  }

  /**
   * $(document).ready
   * Specify a function to execute when the DOM is fully loaded.
   */

  $(function($) {
    mfnFieldMultiText();
  });

})(jQuery);
