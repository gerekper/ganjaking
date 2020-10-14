jQuery(document).ready(function($) {
    // Constant Contact API stuff
    var mepr_check_constantcontact_apikey = function( apikey, access_token,  wpnonce ) {
        if( apikey == '' || access_token == '' ) { return; }

        var args = {
            action: 'mepr_constantcontact_ping_apikey',
            apikey: apikey,
            access_token: access_token,
            wpnonce: wpnonce
        };

        $.post( ajaxurl, args, function(res) {
            if( res == 'error' ) {
                $('#mepr-constantcontact-valid').hide();
                $('#mepr-constantcontact-invalid').html( 'Could not validate API Key or Access Token .' );
                $('#mepr-constantcontact-invalid').fadeIn();
                $('select#meprconstantcontact_list_id').html('');
            }
            else {
                $('#mepr-constantcontact-invalid').hide();
                $('#mepr-constantcontact-valid').html( 'Ready!' );
                $('#mepr-constantcontact-valid').fadeIn();

                mepr_load_constantcontact_lists_dropdown('select#meprconstantcontact_list_id' ,apikey, access_token, wpnonce);
            }
        } );
    }

    //Constant Contact enabled/disable checkbox
    if($('#meprconstantcontact_enabled').is(":checked")) {
        mepr_check_constantcontact_apikey( $('#meprconstantcontact_api_key').val(), $('#meprconstantcontact_access_token').val(), MeprConstantContact.wpnonce );
        $('div#constantcontact_hidden_area').show();
    } else {
        $('div#constantcontact_hidden_area').hide();
    }
    $('#meprconstantcontact_enabled').click(function() {
        if($('#meprconstantcontact_enabled').is(":checked")) {
            mepr_check_constantcontact_apikey( $('#meprconstantcontact_api_key').val(), $('#meprconstantcontact_access_token').val(), MeprConstantContact.wpnonce );
        }
        $('div#constantcontact_hidden_area').slideToggle('fast');
    });

    var action = ($('#meprconstantcontact_optin').is(":checked")?'show':'hide');

    $('#meprconstantcontact-optin-text')[action]();
    $('#meprconstantcontact_optin').click(function() {
        $('#meprconstantcontact-optin-text')['slideToggle']('fast');
    });

    // Constant Contact Actions
    if($('#meprconstantcontact_enabled').is(':checked')) {
        mepr_check_constantcontact_apikey( $('#meprconstantcontact_api_key').val(), $('#meprconstantcontact_access_token').val(),  MeprConstantContact.wpnonce );
    }

    $('#meprconstantcontact_api_key').blur( function(e) {
        mepr_check_constantcontact_apikey( $(this).val(), $('#meprconstantcontact_access_token').val(), MeprConstantContact.wpnonce );
    });

    $('#meprconstantcontact_access_token').blur( function(e) {
        mepr_check_constantcontact_apikey( $('#meprconstantcontact_api_key').val(), $(this).val(), MeprConstantContact.wpnonce );
    });

}); //End main document.ready func