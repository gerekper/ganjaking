(function($) {

  /* globals jQuery */

  "use strict";

  function mfnFieldIcon() {

    $('body').on('click', '.mfn-icon-select .mfn-icon', function() {

      var field = $(this).closest('.mfn-icon-field');
      var input = field.find('.mfn-icon-input');

      var icon = $(this).attr('data-rel');

      $(this).siblings().removeClass('active');
      $(this).addClass('active');
      input.val(icon);

    });

  }

  /**
   * $(document).ready
   * Specify a function to execute when the DOM is fully loaded.
   */

  $(function($) {
    mfnFieldIcon();
  });

})(jQuery);
