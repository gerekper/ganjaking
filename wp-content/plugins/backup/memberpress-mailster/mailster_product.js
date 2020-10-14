jQuery(document).ready(function($) {
  if($('#meprmailster_list_override').is(":checked")) {
    $('div#meprmailster_override_area').show();
  } else {
    $('div#meprmailster_override_area').hide();
  }

  $('#meprmailster_list_override').click(function() {
    $('div#meprmailster_override_area').slideToggle();
  });
}); //End main document.ready func
