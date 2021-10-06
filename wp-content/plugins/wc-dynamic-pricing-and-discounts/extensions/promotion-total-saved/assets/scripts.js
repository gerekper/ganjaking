/**
 * WooCommerce Dynamic Pricing & Discounts - Promotion - Total Saved - Scripts
 */

jQuery(document).ready(function() {

    'use strict';

    // Fix element
    function fix_element()
    {

        // Move element to correct position
        jQuery('.rp_wcdpd_promotion_total_saved_div').each(function() {
            if (jQuery(this).prev('.rp_wcdpd_promotion_total_saved_div_position').length === 0) {
                jQuery('.rp_wcdpd_promotion_total_saved_div_position').after(jQuery(this));
            }
        });

        // Ensure there is only one element on page
        jQuery('.rp_wcdpd_promotion_total_saved_div').slice(1).remove();

        // Show element
        jQuery('.rp_wcdpd_promotion_total_saved_div').show();
    }

    // Run on WooCommerce cart/checkout update
    jQuery(document.body).on('updated_wc_div updated_cart_totals updated_checkout', function() {
        fix_element();
    });

    // Force checkout update when error is detected
    jQuery(document.body).on('checkout_error', function() {
        jQuery(document.body).trigger('update_checkout');
    });

    // Run on page load
    fix_element();

});
