jQuery(document).ready(function($) {
    //trial period
    if($('#meprconstantcontact_list_override').is(":checked")) {
        mepr_load_constantcontact_lists_dropdown( '#meprconstantcontact_list_override_id',
            $('#meprconstantcontact_list_override').data('apikey'),
            $('#meprconstantcontact_list_override').data('access-token'),
            MeprProducts.wpnonce );
        $('div#meprconstantcontact_override_area').show();
    } else {
        $('div#meprconstantcontact_override_area').hide();
    }

    $('#meprconstantcontact_list_override').click(function() {
        if($('#meprconstantcontact_list_override').is(":checked")) {
            mepr_load_constantcontact_lists_dropdown( '#meprconstantcontact_list_override_id',
                $('#meprconstantcontact_list_override').data('apikey'),
                $('#meprconstantcontact_list_override').data('access-token'),
                MeprProducts.wpnonce );
        }
        $('div#meprconstantcontact_override_area').slideToggle();
    });
}); //End main document.ready func
