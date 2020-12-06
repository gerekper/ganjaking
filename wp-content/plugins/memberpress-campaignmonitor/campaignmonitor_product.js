jQuery(document).ready(function($) {
    //trial period
    if($('#meprcampaignmonitor_list_override').is(":checked")) {
        mepr_load_campaignmonitor_lists_dropdown( '#meprcampaignmonitor_list_override_id',
            $('#meprcampaignmonitor_list_override').data('clientid'),
            $('#meprcampaignmonitor_list_override').data('apikey'),
            MeprProducts.wpnonce );
        $('div#meprcampaignmonitor_override_area').show();
    } else {
        $('div#meprcampaignmonitor_override_area').hide();
    }

    $('#meprcampaignmonitor_list_override').click(function() {
        if($('#meprcampaignmonitor_list_override').is(":checked")) {
            mepr_load_campaignmonitor_lists_dropdown( '#meprcampaignmonitor_list_override_id',
                $('#meprcampaignmonitor_list_override').data('clientid'),
                $('#meprcampaignmonitor_list_override').data('apikey'),
                MeprProducts.wpnonce );
        }
        $('div#meprcampaignmonitor_override_area').slideToggle();
    });
}); //End main document.ready func
