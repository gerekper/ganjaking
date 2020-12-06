jQuery(document).ready(function($) {
  if($('#meprsendy_list_override').is(":checked")) {
    $('div#meprsendy_override_area').show();
  } else {
    $('div#meprsendy_override_area').hide();
  }

  $('#meprsendy_list_override').click(function() {
    $('div#meprsendy_override_area').slideToggle();
  });
}); //End main document.ready func
