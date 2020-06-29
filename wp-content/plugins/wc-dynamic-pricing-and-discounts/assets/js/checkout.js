/**
 * WooCommerce Dynamic Pricing & Discounts - Checkout Scripts
 */
jQuery(document).ready(function() {

    /**
     * Update on billing email change
     */
    jQuery('form.checkout input#billing_email').change(function() {
        jQuery('body').trigger('update_checkout');
    }).change();

    /**
     * Update on payment method change
     */
    function set_up_payment_method_checkout_update()
    {
        jQuery('form.checkout input[name="payment_method"]').each(function() {
            if (typeof jQuery(this).data('rp_wcdpd_payment_method_checkout_update') === 'undefined') {
                jQuery(this).change(function() {
                    jQuery('body').trigger('update_checkout');
                });
                jQuery(this).data('rp_wcdpd_payment_method_checkout_update', true);
            }
        });
    }
    jQuery('body').on('updated_checkout', set_up_payment_method_checkout_update);
    set_up_payment_method_checkout_update();




});
