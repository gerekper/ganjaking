jQuery(document).ready(function($) {
  // Sendy API stuff
  var mepr_check_sendy_apikey = function(domain, apikey, wpnonce) {
    if(domain == '') { return; }
    if(apikey == '') { return; }

    var args = {
      action: 'mepr_sendy_ping_apikey',
      domain: domain,
      apikey: apikey,
      wpnonce: wpnonce
    };

    $.post(ajaxurl, args, function(res) {
      if('error' in res) {
        $('#mepr-sendy-valid').hide();
        $('#mepr-sendy-invalid').html(res.error);
        $('#mepr-sendy-invalid').fadeIn();
        $('select#meprsendy_list_id').html('');
      }
      else {
        $('#mepr-sendy-invalid').hide();
        $('#mepr-sendy-valid').html(res.msg);
        $('#mepr-sendy-valid').fadeIn();
      }
    }, 'json');
  }

  //Sendy enabled/disable checkbox
  if($('#meprsendy_enabled').is(":checked")) {
    mepr_check_sendy_apikey($('#meprsendy_domain').val(), $('#meprsendy_api_key').val(), MeprSendy.wpnonce);
    $('div#sendy_hidden_area').show();
  } else {
    $('div#sendy_hidden_area').hide();
  }
  $('#meprsendy_enabled').click(function() {
    if($('#meprsendy_enabled').is(":checked")) {
      mepr_check_sendy_apikey($('#meprsendy_domain').val(), $('#meprsendy_api_key').val(), MeprSendy.wpnonce);
    }
    $('div#sendy_hidden_area').slideToggle('fast');
  });

  var action = ($('#meprsendy_optin').is(":checked")?'show':'hide');

  $('#meprsendy-optin-text')[action]();
  $('#meprsendy_optin').click(function() {
    $('#meprsendy-optin-text')['slideToggle']('fast');
  });

  $('#meprsendy_domain').blur( function(e) {
    mepr_check_sendy_apikey($(this).val(), $('#meprsendy_api_key').val(), MeprSendy.wpnonce);
  });

  $('#meprsendy_api_key').blur(function(e) {
    mepr_check_sendy_apikey($('#meprsendy_domain').val(), $(this).val(), MeprSendy.wpnonce);
  });
}); //End main document.ready func
