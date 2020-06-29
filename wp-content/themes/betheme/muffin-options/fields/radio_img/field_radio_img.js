(function($) {

  /* globals jQuery */

  "use strict";

  function mfnFieldRadioImg() {

		$('#mfn-wrapper').on('click', '.mfn-radio-img', function(e) {

      var el = $(this);
      var fieldset = $(this).closest('fieldset');

      fieldset.find('.mfn-radio-img').removeClass('mfn-radio-img-selected');
      el.addClass('mfn-radio-img-selected');

      el.find('input[type="radio"]').attr('checked', 'checked');

    });

  }

  /**
   * $(document).ready
   * Specify a function to execute when the DOM is fully loaded.
   */

  $(function($) {
    mfnFieldRadioImg();
  });

})(jQuery);
