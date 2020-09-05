/** @var ct_ultimate_gdpr_eform object - localized */

jQuery(document).on('ready', function (e) {

    jQuery('#registerform #wp-submit').attr('disabled', true).before(ct_ultimate_gdpr_wp_user.link);

});