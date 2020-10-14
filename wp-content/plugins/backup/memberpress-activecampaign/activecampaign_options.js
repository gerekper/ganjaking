jQuery(document).ready(function($) {
    // ActiveCampaign API stuff
    var mepr_check_activecampaign_apikey = function( account, apikey, wpnonce ) {
        if(account == '' || apikey == '') { return; }

        var args = {
            action: 'mepr_activecampaign_ping_apikey',
            account: account,
            apikey: apikey,
            wpnonce: wpnonce
        };

        $.post( ajaxurl, args, function(res) {
            if(res.result_code != 1) {
                $('#mepr-activecampaign-valid').hide();
                $('#mepr-activecampaign-invalid').html( res.result_message );
                $('#mepr-activecampaign-invalid').fadeIn();
                $('select#mepractivecampaign_list_id').html('');
            }
            else {
                $('#mepr-activecampaign-invalid').hide();
                $('#mepr-activecampaign-valid').html( res.result_message );
                $('#mepr-activecampaign-valid').fadeIn();
                mepr_load_activecampaign_lists_dropdown('select#mepractivecampaign_list_id' ,account, apikey, wpnonce);
            }
        }, 'json' );
    }

    //ActiveCampaign enabled/disable checkbox
    if($('#mepractivecampaign_enabled').is(":checked")) {
        mepr_check_activecampaign_apikey( $('#mepractivecampaign_account').val(), $('#mepractivecampaign_api_key').val(), MeprActiveCampaign.wpnonce );
        $('div#activecampaign_hidden_area').show();
    } else {
        $('div#activecampaign_hidden_area').hide();
    }
    $('#mepractivecampaign_enabled').click(function() {
        if($('#mepractivecampaign_enabled').is(":checked")) {
            mepr_check_activecampaign_apikey( $('#mepractivecampaign_account').val(), $('#mepractivecampaign_api_key').val(), MeprActiveCampaign.wpnonce );
        }
        $('div#activecampaign_hidden_area').slideToggle('fast');
    });

    var action = ($('#mepractivecampaign_optin').is(":checked")?'show':'hide');

    $('#mepractivecampaign-optin-text')[action]();
    $('#mepractivecampaign_optin').click(function() {
        $('#mepractivecampaign-optin-text')['slideToggle']('fast');
    });

    // ActiveCampaign Actions
    if($('#mepractivecampaign_enabled').is(':checked')) {
        mepr_check_activecampaign_apikey( $('#mepractivecampaign_account').val(), $('#mepractivecampaign_api_key').val(), MeprActiveCampaign.wpnonce );
    }

    $('#mepractivecampaign_account').blur( function(e) {
        mepr_check_activecampaign_apikey( $(this).val(), $('#mepractivecampaign_api_key').val(), MeprActiveCampaign.wpnonce );
    });

    $('#mepractivecampaign_api_key').blur( function(e) {
        mepr_check_activecampaign_apikey( $('#mepractivecampaign_account').val(), $(this).val(), MeprActiveCampaign.wpnonce );
    });
}); //End main document.ready func

