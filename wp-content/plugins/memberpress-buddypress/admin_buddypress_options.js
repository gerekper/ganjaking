(function($) {
  $(document).ready(function() {
    //Date expiration
    if($('#mepr_bp_enabled').is(":checked")) {
      $('#mepr_bp_options_area').show();
    } else {
      $('#mepr_bp_options_area').hide();
    }
    $('#mepr_bp_enabled').click(function() {
      $('#mepr_bp_options_area').slideToggle('fast');
    });
  });
})(jQuery);
