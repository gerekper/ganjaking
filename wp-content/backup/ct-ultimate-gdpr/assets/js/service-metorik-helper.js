/** @var object ct-ultimate-gdpr-service-metorik-helper */

// disable input untill consent checked
jQuery(document).on('click', '.add_to_cart_button', function () {
    jQuery('.email-input-wrapper').before(ct_ultimate_gdpr_service_metorik_helper.checkbox);
    jQuery('.email-input-wrapper input').attr('disabled', true);
});

// maybe reenable checkbox
jQuery(document).on('change', '.ct-ultimate-gdpr-consent-field', function (e) {
    var checked = jQuery(this).attr('checked');
    jQuery(this).parent().find('.email-input').attr('disabled', !checked);
});