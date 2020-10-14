jQuery(document).ready(function($) {
    // Campaign Monitor API stuff
    var mepr_check_campaignmonitor_apikey = function( clientid, apikey,  wpnonce ) {
        if( clientid == '' || apikey == '' ) { return; }

        var args = {
            action: 'mepr_campaignmonitor_ping_apikey',
            clientid: clientid,
            apikey: apikey,
            wpnonce: wpnonce
        };

        $.post( ajaxurl, args, function(res) {
            if( res == 'error' ) {
                $('#mepr-campaignmonitor-valid').hide();
                $('#mepr-campaignmonitor-invalid').html( 'Could not validate Client ID or API Key.' );
                $('#mepr-campaignmonitor-invalid').fadeIn();
                $('select#meprcampaignmonitor_list_id').html('');
            }
            else {
                $('#mepr-campaignmonitor-invalid').hide();
                $('#mepr-campaignmonitor-valid').html( 'Ready!' );
                $('#mepr-campaignmonitor-valid').fadeIn();

                mepr_load_campaignmonitor_lists_dropdown('select#meprcampaignmonitor_list_id' ,clientid, apikey, wpnonce);
            }
        } );
    }

    // Campaign Monitor enabled/disable checkbox
    if($('#meprcampaignmonitor_enabled').is(":checked")) {
        mepr_check_campaignmonitor_apikey( $('#meprcampaignmonitor_client_id').val(), $('#meprcampaignmonitor_api_key').val(), MeprCampaignMonitor.wpnonce );
        $('div#campaignmonitor_hidden_area').show();
    } else {
        $('div#campaignmonitor_hidden_area').hide();
    }

    $('#meprcampaignmonitor_enabled').click(function() {
        if($('#meprcampaignmonitor_enabled').is(":checked")) {
            mepr_check_campaignmonitor_apikey( $('#meprcampaignmonitor_client_id').val(), $('#meprcampaignmonitor_api_key').val(), MeprCampaignMonitor.wpnonce );
        }
        $('div#campaignmonitor_hidden_area').slideToggle('fast');
    });

    var action = ($('#meprcampaignmonitor_optin').is(":checked")?'show':'hide');

    $('#meprcampaignmonitor-optin-text')[action]();
    $('#meprcampaignmonitor_optin').click(function() {
        $('#meprcampaignmonitor-optin-text')['slideToggle']('fast');
    });

    // Campaign Monitor Actions
    $('#meprcampaignmonitor_api_key').blur( function(e) {
        mepr_check_campaignmonitor_apikey( $('#meprcampaignmonitor_client_id').val(), $(this).val(), MeprCampaignMonitor.wpnonce );
    });

    $('#meprcampaignmonitor_client_id').blur( function(e) {
        mepr_check_campaignmonitor_apikey( $(this).val(), $('#meprcampaignmonitor_api_key').val(), MeprCampaignMonitor.wpnonce );
    });

}); //End main document.ready func
