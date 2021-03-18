/* terms accept shortcode features */
jQuery(document).ready(function ($) {

    function onAccept() {
        jQuery.post(ct_ultimate_gdpr_terms.ajaxurl, {"action": "ct_ultimate_gdpr_terms_consent_give"}, function () {
            ct_ultimate_gdpr_terms.redirect ? window.location.replace(ct_ultimate_gdpr_terms.redirect) : window.location.reload(true);
        });
        jQuery('#ct-ultimate-gdpr-terms-accept').hide();
    }

    function onDecline() {
        jQuery.post(ct_ultimate_gdpr_terms.ajaxurl, {"action": "ct_ultimate_gdpr_terms_consent_decline"}, function () {
            window.location.reload(true);
        });
        jQuery('#ct-ultimate-gdpr-terms-decline').hide();
    }

    $('#ct-ultimate-gdpr-terms-accept').bind('click', onAccept);
    $('#ct-ultimate-gdpr-terms-decline').bind('click', onDecline);

});