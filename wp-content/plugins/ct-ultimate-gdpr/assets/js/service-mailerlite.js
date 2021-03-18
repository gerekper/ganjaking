/** @var ct_ultimate_gdpr_mailerlite object - localized */

jQuery(document).on('ready', function () {
    if( jQuery('body').find('.ml-form-embedWrapper.embedForm').length == 1 ){
        jQuery('.ml-form-embedSubmit').last().append(ct_ultimate_gdpr_mailerlite.checkbox);
    }

    if( jQuery('body').find('.mailerlite-subscribe-button-container').length == 1 ){
        jQuery('.mailerlite-subscribe-button-container').last().append(ct_ultimate_gdpr_mailerlite.checkbox);
    }
}); 