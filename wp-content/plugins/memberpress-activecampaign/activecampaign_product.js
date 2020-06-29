jQuery(document).ready(function($) {
    //trial period
    if($('#mepractivecampaign_list_override').is(":checked")) {
        mepr_load_activecampaign_lists_dropdown( '#mepractivecampaign_list_override_id',
            $('#mepractivecampaign_list_override').data('account'),
            $('#mepractivecampaign_list_override').data('apikey'),
            MeprProducts.wpnonce );
        $('div#mepractivecampaign_override_area').show();
    } else {
        $('div#mepractivecampaign_override_area').hide();
    }
    $('#mepractivecampaign_list_override').click(function() {
        if($('#mepractivecampaign_list_override').is(":checked")) {
            mepr_load_activecampaign_lists_dropdown( '#mepractivecampaign_list_override_id',
                $('#mepractivecampaign_list_override').data('account'),
                $('#mepractivecampaign_list_override').data('apikey'),
                MeprProducts.wpnonce );
        }
        $('div#mepractivecampaign_override_area').slideToggle();
    });
}); //End main document.ready func
