jQuery(document).ready(function($) {
  //AWeber enabled/disable checkbox
  if($('#mepraweber_enabled').is(":checked")) {
    $('div#aweber_hidden_area').show();
  } else {
    $('div#aweber_hidden_area').hide();
  }
  $('#mepraweber_enabled').click(function() {
    $('div#aweber_hidden_area').slideToggle('fast');
  });

  //Advanced AWeber enabled/disable checkbox
  var action = ($('#mepr-adv-aweber-enabled').is(":checked")?'show':'hide');

  $('#mepr-adv-aweber-hidden-area')[action]();
  $('#mepr-adv-aweber-enabled').click(function() {
    $('#mepr-adv-aweber-hidden-area')['slideToggle']('fast');
  });

  var action = ($('#mepr-adv-aweber-optin').is(":checked")?'show':'hide');

  $('#mepr-adv-aweber-optin-options')[action]();
  $('#mepr-adv-aweber-optin').click(function() {
    $('#mepr-adv-aweber-optin-options')['slideToggle']('fast');
  });

  // Button used to auth aweber
  $('#mepr-aweber-auth').click( function(e) {
    e.preventDefault();

    // Setup the arguments to be sent to our endpoint handler in AjexAdmin
    var args = {
      action: 'mepr_auth_aweber',
      auth_code: $('#mepr-aweber-api-code').val(),
      wpnonce: MeprAWeber.wpnonce
    };

    $('#mepr-aweber-auth-loading').show();

    $.post( ajaxurl, args,
            function(res) {
              $('#mepr-aweber-auth-loading').hide();

              // Check to see if the action returned an error
              if( 'error' in res ) {
                $('#mepr-aweber-message').hide();

                // Display an error message
                $('#mepr-aweber-error').html( res.error );
                $('#mepr-aweber-error').fadeIn();

                $('#aweber-auth-panel').show();
                $('#aweber-deauth-panel').hide();
              }
              else {
                $('#mepr-aweber-error').hide();

                // Display a success message
                $('#mepr-aweber-message').html( res.message );
                $('#mepr-aweber-message').fadeIn();

                mepr_load_aweber_list_dropdown('#mepr-adv-aweber-list', MeprAWeber.wpnonce);

                $('#aweber-auth-panel').hide();
                $('#aweber-deauth-panel').show();
              }
            },
            'json' );
  });

  // Button used to deauth aweber
  $('#mepr-aweber-deauth').click( function(e) {
    e.preventDefault();

    if(confirm(MeprAWeber.deauth_aweber_message)) {
      // Setup the arguments to be sent to our endpoint handler in AjexAdmin
      var args = {
        action: 'mepr_deauth_aweber',
        wpnonce: MeprAWeber.wpnonce
      };

      $('#mepr-aweber-deauth-loading').show();

      $.post( ajaxurl, args,
        function(res) {
          $('#mepr-aweber-deauth-loading').hide();

          // Check to see if the action returned an error
          if( res !== null && 'error' in res ) {
            $('#mepr-aweber-message').hide();

            // Display an error message
            $('#mepr-aweber-error').html( res.error );
            $('#mepr-aweber-error').fadeIn();

            $('#aweber-auth-panel').hide();
            $('#aweber-deauth-panel').show();
          }
          else {
            $('#mepr-aweber-error').hide();

            // Display a success message
            $('#mepr-aweber-message').html( res.message );
            $('#mepr-aweber-message').fadeIn();

            $('#aweber-auth-panel').show();
            $('#aweber-deauth-panel').hide();
          }
        },
        'json' );
    }
  });

  if(MeprAWeber.aweber_authorized == 1) {
    mepr_load_aweber_list_dropdown('#mepr-adv-aweber-list', MeprAWeber.wpnonce);
  }
}); //End main document.ready func

