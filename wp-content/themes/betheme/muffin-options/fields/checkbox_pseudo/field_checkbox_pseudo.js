(function($) {

  /* globals jQuery */

  "use strict";

  var MfnFieldCheckboxPseudo = (function() {

    /**
     * Attach events to buttons. Runs whole script.
     */

    function init() {
      check();
    }

    /**
     * Checkbox click.
     */

    function check() {

      $('body').on('change', '.mfnf-checkbox.pseudo input[type="checkbox"]', function() {

        var el = $(this).closest('.mfnf-checkbox.pseudo'),
          value = '';

        $('input:checked', el).each(function() {
          value = value + ' ' + $(this).val();
        });

        $('input.value', el).val(value);

      });

    }

    /**
     * Return
     * Method to start the closure
     */

    return {
      init: init
    };

  })();

  /**
   * $(document).ready
   * Specify a function to execute when the DOM is fully loaded.
   */

  $(function() {
    MfnFieldCheckboxPseudo.init();
  });

})(jQuery);
