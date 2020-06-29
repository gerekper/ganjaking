jQuery(document).ready(function($) {
  //MailPoet enabled/disable checkbox
  if($('#meprmailpoet_enabled').is(":checked")) {
    $('div#mailpoet_hidden_area').show();
  } else {
    $('div#mailpoet_hidden_area').hide();
  }
  $('#meprmailpoet_enabled').click(function() {
    $('div#mailpoet_hidden_area').slideToggle('fast');
  });

  var action = ($('#meprmailpoet_optin').is(":checked")?'show':'hide');

  $('#meprmailpoet-optin-text')[action]();
  $('#meprmailpoet_optin').click(function() {
    $('#meprmailpoet-optin-text')['slideToggle']('fast');
  });
}); //End main document.ready func
