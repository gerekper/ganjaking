(function($) {
  $(document).ready(function() {
    $('#mpmma_held_disabled').change(function(e) {
      e.preventDefault();
      $('span.held_hidden_box').slideToggle();
    });

    if($('#mpmma_held_disabled').is(':checked')) {
      $('span.held_hidden_box').hide();
    } else {
      $('span.held_hidden_box').show();
    }
  });
})(jQuery);