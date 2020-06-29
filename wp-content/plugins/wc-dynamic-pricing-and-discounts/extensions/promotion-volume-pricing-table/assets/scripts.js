/**
 * WooCommerce Dynamic Pricing & Discounts - Volume Pricing Table Scripts
 */
jQuery(document).ready(function() {

    /**
     * Modal control
     */
    jQuery('.rp_wcdpd_product_page_modal_link span').click(function() {

        if (!jQuery('#rp_wcdpd_modal_overlay').length) {
            jQuery('body').append('<div id="rp_wcdpd_modal_overlay" class="rp_wcdpd_modal_overlay"></div>');
        }

        jQuery('#rp_wcdpd_modal_overlay').click(function() {
            jQuery('#rp_wcdpd_modal_overlay').fadeOut();
            jQuery('.rp_wcdpd_modal').fadeOut();
        });

        var pricing_table = jQuery(this).closest('.rp_wcdpd_product_page').parent().find('.rp_wcdpd_modal');
        jQuery('#rp_wcdpd_modal_overlay').fadeIn();
        pricing_table.css('top', '50%').css('left', '50%').css('margin-top', -pricing_table.outerHeight()/2).css('margin-left', -pricing_table.outerWidth()/2).fadeIn();

        return false;
    });

    /**
     * Variable product table control
     */
    jQuery('input:hidden[name="variation_id"]').change(function() {
        jQuery('.rp_wcdpd_pricing_table_variation').hide();
        jQuery('#rp_wcdpd_pricing_table_variation_' + jQuery(this).val()).show();
    }).change();

    /**
     * Quantity input control
     */
    jQuery('.rp_wcdpd_pricing_table_quantity[data-rp-wcdpd-from], .rp_wcdpd_pricing_table_product_price[data-rp-wcdpd-from]').click(function() {
        jQuery('form.cart input[name="quantity"]').val(jQuery(this).data('rp-wcdpd-from')).change();
    });

    /**
     * Variation input control
     */
    jQuery('.rp_wcdpd_pricing_table_product_name[data-rp-wcdpd-variation-attributes], .rp_wcdpd_pricing_table_product_price[data-rp-wcdpd-variation-attributes]').click(function() {

        // No attributes
        if (jQuery(this).data('rp-wcdpd-variation-attributes') === '') {
            return;
        }

        var attributes = {};

        // Get attributes
        var attributes_raw = jQuery(this).data('rp-wcdpd-variation-attributes').split('&');

        // Parse attributes
        jQuery.each(attributes_raw, function(index, attribute_raw) {

            // Split name and value
            var parts = attribute_raw.split('=');

            // Set to attributes object
            attributes[parts[0]] = parts[1];
        });

        // Iterate over attributes and reset values
        jQuery.each(attributes, function(attribute, value) {
            jQuery('form.cart .variations select[name="' + attribute + '"]').each(function() {
                jQuery(this).val('').change();
            });
        });

        // Iterate over attributes and set new values
        jQuery.each(attributes, function(attribute, value) {
            jQuery('form.cart .variations select[name="' + attribute + '"]').each(function() {

                var select = jQuery(this);

                // Value present
                if (value !== '') {
                    select.val(value);
                }
                // Any value
                else {
                    select.find('option').each(function() {
                        if (jQuery(this).val() !== '') {
                            select.val(jQuery(this).val());
                            return false;
                        }
                    });
                }

                // Fire change event (delay is a fix for issue #501)
		window.setTimeout(function() {
                    select.change();
		}, 50);
            });
        });
    });


});
