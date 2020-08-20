/** @var ct_ultimate_gdpr_formcraft object - localized */

jQuery(document).on('ready', function () {
    if( jQuery('body').find('.fc-form').length == 1 ){
        jQuery(ct_ultimate_gdpr_formcraft.checkbox).insertBefore('.fc-form .form-page-content > div:last-child');
    }

    if( jQuery('body').find('.fcb_form').length == 1 ){
        jQuery(ct_ultimate_gdpr_formcraft_basic.checkbox).insertBefore('form.fcb_form > div:last-child');

        jQuery(document).ajaxComplete(function(event, xhr, opt){
            if( jQuery('body').find('.fcb_form .final-success').length == 1 ){
                jQuery('.fcb_form .ct-ultimate-gdpr-formcraft').remove();
            }
        });
    }

});

