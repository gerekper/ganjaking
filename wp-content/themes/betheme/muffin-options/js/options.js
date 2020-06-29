(function($) {

  /* globals jQuery */

  "use strict";

  function mfnOptions() {

    var current = 'general';

  	// show last open tab

    if ($('#last_tab').val()) {
      current = $('#last_tab').val();
    }

		// show active tab and menu

    $('#' + current + '-mfn-section').show();
    $('#' + current + '-mfn-submenu-li').addClass('active').parent('ul').show().parent('li').addClass('active');

    // parent menu | click
		// show childrens and select 1st

    $('#mfn-wrapper').on('click', '.mfn-menu-a', function() {

      if (!$(this).parent().hasClass('active')) {

        $('.mfn-menu-li').removeClass('active');
        $('.mfn-submenu').slideUp('fast');

        $(this).next('ul').stop().slideDown('fast');
        $(this).parent('li').addClass('active');

        $('.mfn-submenu-li').removeClass('active');
        $('.mfn-section').hide();

        current = $(this).next('ul').children('li:first').addClass('active').children('a').attr('data-rel');
        $('#' + current + '-mfn-section').stop().fadeIn(1200);
        $('#last_tab').val(current);
      }

    });

    // submenu click

    $('#mfn-wrapper').on('click', '.mfn-submenu-a', function() {

      if (!$(this).parent().hasClass('active')) {

        $('.mfn-submenu-li').removeClass('active');
        $(this).parent('li').addClass('active');

        $('.mfn-section').hide();

        current = $(this).attr('data-rel');
        $('#' + current + '-mfn-section').stop().fadeIn(1200);
        $('#last_tab').val(current);
      }

    });

    // submenu | add last

    $('.mfn-submenu .mfn-submenu-li:last-child').addClass('last');

    // reset

    $('#mfn-wrapper').on('click', '.reset-pre-confirm', function() {
      $(this).closest('.step-1').hide().next().fadeIn(200);
    });

    $('#mfn-wrapper').on('click', '.mfn-popup-reset', function() {

      if ($('.reset-security-code').val() != 'r3s3t') {
        alert('Please insert correct security code: r3s3t');
        return false;
      }

      if (confirm("Are you sure?\n\nClicking this button will reset all custom values across your entire Theme Options panel")) {
        $(this).val('Resetting...');
        return true;
      } else {
        return false;
      }
    });

    // import code button

		$('#mfn-wrapper').on('click', '.mfn-import-imp-code-btn', function() {
      $('.mfn-import-imp-link-wrapper').hide();
      $('.mfn-import-imp-code-wrapper').stop().fadeIn(500);
    });

    // import link button

		$('#mfn-wrapper').on('click', '.mfn-import-imp-link-btn', function() {
      $('.mfn-import-imp-code-wrapper').hide();
      $('.mfn-import-imp-link-wrapper').stop().fadeIn(500);
    });

    // export code button

		$('#mfn-wrapper').on('click', '.mfn-import-exp-code-btn', function() {
      $('.mfn-import-exp-link').hide();
      $('.mfn-import-exp-code').stop().fadeIn(500);
    });

    // export link button

		$('#mfn-wrapper').on('click', '.mfn-import-exp-link-btn', function() {
      $('.mfn-import-exp-code').hide();
      $('.mfn-import-exp-link').stop().fadeIn(500);
    });

  }

	/**
	 * $(document).ready
	 * Specify a function to execute when the DOM is fully loaded.
	 */

  $(function($) {
    mfnOptions();
  });

})(jQuery);
