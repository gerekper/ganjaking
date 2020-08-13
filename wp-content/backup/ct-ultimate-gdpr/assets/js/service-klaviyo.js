/** @var ct_ultimate_gdpr_klaviyo object - localized */

jQuery(document).on('ready', function () {
    jQuery('.klaviyo_field_group').append(ct_ultimate_gdpr_klaviyo.checkbox)
    jQuery('.klaviyo_submit_button').attr('disabled', true);
});

jQuery(document).on('change', '.ct-ultimate-gdpr-consent-field', function () {
    var checked = jQuery(this).attr('checked');
    jQuery(this).closest('form').find('.klaviyo_submit_button').attr('disabled', !checked);
});

