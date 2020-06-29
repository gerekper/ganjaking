(function($) {

  /* globals jQuery */

  "use strict";

  /**
   * $(document).ready
   * Specify a function to execute when the DOM is fully loaded.
   */

  $(function($) {

    /**
     * Dashboard | Deregister theme | Confirmation
     */

    $('.form-deregister .confirm.deregister').on('click', 'a', function(e) {
      e.preventDefault();

      $(this).parent().hide()
        .next().fadeIn();
    });

    $('.form-deregister .question').on('click', 'a.cancel', function(e) {
      e.preventDefault();

      $(this).parent().hide()
        .prev().fadeIn();
    });

  });

})(jQuery);
