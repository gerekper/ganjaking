jQuery(document).ready(function($) {
  //Mailster enabled/disable checkbox
  if($('#meprmailster_enabled').is(":checked")) {
    $('div#mailster_hidden_area').show();
  } else {
    $('div#mailster_hidden_area').hide();
  }
  $('#meprmailster_enabled').click(function() {
    $('div#mailster_hidden_area').slideToggle('fast');
  });

  var action = ($('#meprmailster_optin').is(":checked")?'show':'hide');

  $('#meprmailster-optin-text')[action]();
  $('#meprmailster_optin').click(function() {
    $('#meprmailster-optin-text')['slideToggle']('fast');
  });
}); //End main document.ready func
