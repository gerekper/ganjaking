/**
 * WooChimp Cookie JavaScript
 */

/**
 * Based on jQuery
 */
jQuery(document).ready(function() {

    /**
     * Save campaign cookies to hidden fields on checkout
     */
    var eid = jQuery.cookie('woochimp_mc_eid');
    var cid = jQuery.cookie('woochimp_mc_cid');

    if (typeof eid !== 'undefined') {
        jQuery('#woochimp_cookie_mc_eid').each(function() {
            jQuery(this).val(eid);
        });
    }

    if (typeof cid !== 'undefined') {
        jQuery('#woochimp_cookie_mc_cid').each(function() {
            jQuery(this).val(cid);
        });
    }

});


