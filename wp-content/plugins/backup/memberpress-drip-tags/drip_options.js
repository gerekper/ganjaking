jQuery(document).ready(function($) {
    // Drip API stuff
    var mepr_check_driptags_apikey = function( apikey, wpnonce ) {
        if( apikey == '' ) { return; }

        var args = {
            action: 'mepr_drip_tags_ping_apikey',
            apikey: apikey,
            wpnonce: wpnonce
        };

        $.post( ajaxurl, args, function(res) {
            if( res == 'error' ) {
                $('#mepr-drip-tags-valid').hide();
                $('#mepr-drip-tags-invalid').html( 'Could not validate API Token.' );
                $('#mepr-drip-tags-invalid').fadeIn();
                $('select#meprdriptags_account_id').html('');
            }
            else {
                $('#mepr-drip-tags-invalid').hide();
                $('#mepr-drip-tags-valid').html( 'Ready!' );
                $('#mepr-drip-tags-valid').fadeIn();

                mepr_load_driptags_accounts_dropdown( 'select#meprdriptags_account_id', apikey, wpnonce );
            }
        });

    }

    //Drip enabled/disable checkbox
    if($('#meprdriptags_enabled').is(":checked")) {
        mepr_check_driptags_apikey( $('#meprdriptags_api_key').val(), MeprDripTags.wpnonce );
        $('div#meprdriptags_hidden_area').show();
    } else {
        $('div#meprdriptags_hidden_area').hide();
    }
    $('#meprdriptags_enabled').click(function() {
        if($('#meprdriptags_enabled').is(":checked")) {
            mepr_check_driptags_apikey( $('#meprdriptags_api_key').val(), MeprDripTags.wpnonce );
        }
        $('div#meprdriptags_hidden_area').slideToggle('fast');
    });

    var action = ($('#meprdriptags_optin').is(":checked")?'show':'hide');
    $('#meprdriptags-optin-text')[action]();
    $('#meprdriptags_optin').click(function() {
        $('#meprdriptags-optin-text')['slideToggle']('fast');
    });

    // Drip Actions
    $('#meprdriptags_api_key').blur( function(e) {
        mepr_check_driptags_apikey( $(this).val(), MeprDripTags.wpnonce );
    });
}); //End main document.ready func

