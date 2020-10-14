jQuery(document).ready(function($) {
  // GetResponse API stuff
  var mepr_check_getresponse_apikey = function( apikey, wpnonce ) {
    if( apikey == '' ) { return; }

    var args = {
      action: 'mepr_gr_ping_apikey',
      apikey: apikey,
      wpnonce: wpnonce
    };

    $.post( ajaxurl, args, function(res) {
      if( res == 'error' ) {
        $('#mepr-getresponse-valid').hide();
        $('#mepr-getresponse-invalid').html( 'Could not validate key.' );
        $('#mepr-getresponse-invalid').fadeIn();
        $('select#meprgetresponse_list_id').html('');
      }
      else {
        $('#mepr-getresponse-invalid').hide();
        $('#mepr-getresponse-valid').html( 'Ready!' );
        $('#mepr-getresponse-valid').fadeIn();
        mepr_load_getresponse_lists_dropdown( 'select#meprgetresponse_list_id', apikey, wpnonce );
      }
    });
  }

  //GetResponse enabled/disable checkbox
  if($('#meprgetresponse_enabled').is(":checked")) {
    mepr_check_getresponse_apikey( $('#meprgetresponse_api_key').val(), MeprGetResponse.wpnonce );
    $('div#getresponse_hidden_area').show();
  } else {
    $('div#getresponse_hidden_area').hide();
  }
  $('#meprgetresponse_enabled').click(function() {
    if($('#meprgetresponse_enabled').is(":checked")) {
      mepr_check_getresponse_apikey( $('#meprgetresponse_api_key').val(), MeprGetResponse.wpnonce );
    }
    $('div#getresponse_hidden_area').slideToggle('fast');
  });

  var action = ($('#meprgetresponse_optin').is(":checked")?'show':'hide');
  $('#meprgetresponse-optin-text')[action]();
  $('#meprgetresponse_optin').click(function() {
    $('#meprgetresponse-optin-text')['slideToggle']('fast');
  });

  // GetResponse Actions
  $('#meprgetresponse_api_key').blur( function(e) {
    mepr_check_getresponse_apikey( $(this).val(), MeprGetResponse.wpnonce );
  });
}); //End main document.ready func

