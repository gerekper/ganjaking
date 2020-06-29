jQuery(document).ready(function($) {
  // MadMimi API stuff
  var mepr_check_madmimi_apikey = function( username, apikey, wpnonce ) {
    if(username == '') { return; }
    if(apikey == '') { return; }

    var args = {
      action: 'mepr_madmimi_ping_apikey',
      username: username,
      apikey: apikey,
      wpnonce: wpnonce
    };

    $.post(ajaxurl, args, function(res) {
      if('error' in res) {
        $('#mepr-madmimi-valid').hide();
        $('#mepr-madmimi-invalid').html( res.error );
        $('#mepr-madmimi-invalid').fadeIn();
        $('select#meprmadmimi_list_id').html('');
      }
      else {
        $('#mepr-madmimi-invalid').hide();
        $('#mepr-madmimi-valid').html( res.msg );
        $('#mepr-madmimi-valid').fadeIn();
        mepr_load_madmimi_lists_dropdown('select#meprmadmimi_list_id', username, apikey, wpnonce);
      }
    }, 'json');
  }

  //MadMimi enabled/disable checkbox
  if($('#meprmadmimi_enabled').is(":checked")) {
    mepr_check_madmimi_apikey( $('#meprmadmimi_username').val(), $('#meprmadmimi_api_key').val(), MeprMadMimi.wpnonce );
    $('div#madmimi_hidden_area').show();
  } else {
    $('div#madmimi_hidden_area').hide();
  }
  $('#meprmadmimi_enabled').click(function() {
    if($('#meprmadmimi_enabled').is(":checked")) {
      mepr_check_madmimi_apikey( $('#meprmadmimi_username').val(), $('#meprmadmimi_api_key').val(), MeprMadMimi.wpnonce );
    }
    $('div#madmimi_hidden_area').slideToggle('fast');
  });

  var action = ($('#meprmadmimi_optin').is(":checked")?'show':'hide');

  $('#meprmadmimi-optin-text')[action]();
  $('#meprmadmimi_optin').click(function() {
    $('#meprmadmimi-optin-text')['slideToggle']('fast');
  });

  // MadMimi Actions
  // THIS HAPPENS ABOVE SO LET'S NOT DO IT TWICE
  // if($('#meprmadmimi_enabled').is(':checked')) {
    // mepr_check_madmimi_apikey( $('#meprmadmimi_username').val(), $('#meprmadmimi_api_key').val(), MeprMadMimi.wpnonce );
  // }

  $('#meprmadmimi_username').blur( function(e) {
    mepr_check_madmimi_apikey( $(this).val(), $('#meprmadmimi_api_key').val(), MeprMadMimi.wpnonce );
  });

  $('#meprmadmimi_api_key').blur( function(e) {
    mepr_check_madmimi_apikey( $('#meprmadmimi_username').val(), $(this).val(), MeprMadMimi.wpnonce );
  });
}); //End main document.ready func
