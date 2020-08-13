/**
 * policy accept shortcode features
 * @var object ct_ultimate_gdpr_policy - from wp_localize_script
 *
 * */
jQuery(document).ready(function ($) {

    function onAccept() {
        jQuery.post(ct_ultimate_gdpr_policy.ajaxurl, {"action": "ct_ultimate_gdpr_policy_consent_give"}, function () {
            ct_ultimate_gdpr_policy.redirect ? window.location.replace(ct_ultimate_gdpr_policy.redirect) : window.location.reload(true);
        });
        jQuery('#ct-ultimate-gdpr-policy-accept').hide();
    }

    function onDecline() {
        jQuery.post(ct_ultimate_gdpr_policy.ajaxurl, {"action": "ct_ultimate_gdpr_policy_consent_decline"}, function () {
            window.location.reload(true);
        });
        jQuery('#ct-ultimate-gdpr-policy-accept').hide();
    }

    $('#ct-ultimate-gdpr-policy-accept').bind('click', onAccept);
    $('#ct-ultimate-gdpr-policy-decline').bind('click', onDecline);

});