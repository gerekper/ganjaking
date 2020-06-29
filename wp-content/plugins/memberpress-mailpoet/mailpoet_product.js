jQuery(document).ready(function($) {
  if($('#meprmailpoet_list_override').is(":checked")) {
    $('div#meprmailpoet_override_area').show();
  } else {
    $('div#meprmailpoet_override_area').hide();
  }

  $('#meprmailpoet_list_override').click(function() {
    $('div#meprmailpoet_override_area').slideToggle();
  });
}); //End main document.ready func
