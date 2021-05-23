jQuery(document).on('change', '.nf-form-layout', ct_ultimate_gdpr_service_ninja_forms_switch);

function ct_ultimate_gdpr_service_ninja_forms_switch(e) {
    
    var checked = jQuery(this).find('.ct-ultimate-gdpr-consent-field');
    if(checked.is(':checked')) {
        jQuery(this).find(":button").attr('disabled', false);
        jQuery(".ct-ultimate-gdpr-nf-consent-field-error").hide();
      
    } else {
        jQuery(this).find(":button").attr('disabled', true);
        jQuery(".ct-ultimate-gdpr-nf-consent-field-error").show();
    
    }

}