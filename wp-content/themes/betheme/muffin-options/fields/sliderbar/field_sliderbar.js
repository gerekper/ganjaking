(function($) {

  /* globals jQuery */

  "use strict";

  var el = $('.mfn-slider-field');

  // delay

  var delay = (function() {

    var timer = 0;
    return function(callback, ms) {
      clearTimeout(timer);
      timer = setTimeout(callback, ms);
    };

  })();

  function isNumeric(n) {

    return !isNaN(parseFloat(n)) && isFinite(n);
  }

  function inputChange(input) {

    var value = input.val();

    var slider = input.siblings('.sliderbar');

    slider.slider("value", value);

    var min = slider.attr('data-min') * 1;
    var max = slider.attr('data-max') * 1;

    if (!isNumeric(value) || value < min || value > max) {
      input.closest('.mfn-slider-field').addClass('range-error');
    } else {
      input.closest('.mfn-slider-field').removeClass('range-error');
    }
		
  }

	/**
   * $(document).ready
   * Specify a function to execute when the DOM is fully loaded.
   */

  $(function($){

    // init

    $('.sliderbar', el).each(function() {

      var slider = $(this);
      var input = $(this).siblings('input');

      var value = input.attr('value');
      var min = slider.attr('data-min') * 1;
      var max = slider.attr('data-max') * 1;

      slider.slider({
        range: "min",
        min: min,
        max: max,
        value: value,
        slide: function(event, ui) {
          input.attr('value', ui.value);
          input.closest('.mfn-slider-field').removeClass('range-error');
        }
      });

    });

    // input value change | focusout

    $('.sliderbar_input', el).on('focusout', function() {

      inputChange($(this));

    });

    // input value change | keyup

    $('.sliderbar_input', el).on('keyup', function() {

      var input = $(this);

      delay(function() {
        inputChange(input);
      }, 500);

    });

  });

})(jQuery);
