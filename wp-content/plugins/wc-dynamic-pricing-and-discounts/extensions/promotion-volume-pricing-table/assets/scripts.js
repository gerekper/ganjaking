/**
 * WooCommerce Dynamic Pricing & Discounts - Volume Pricing Table Scripts
 */
jQuery(document).ready(function() {

    'use strict';

    var inhibit_live_product_update = false;

    /**
     * Individual volume pricing table loading for product variations
     */
    jQuery('#rp_wcdpd_pricing_table_variation_container').rightpress_live_product_update({

        // Params
        ajax_url:   rp_wcdpd_promotion_volume_pricing_table_scripts_vars.ajaxurl,
        action:     'rp_wcdpd_load_variation_pricing_table',

        // Before send
        before_send: function(xhr) {

            // Maybe inhibit request
            if (inhibit_live_product_update) {

                // Inhibit request
                xhr.abort();

                // Unset flag
                inhibit_live_product_update = false;

                // Do not proceed further
                return;
            }

            // Change opacity of the element
            jQuery(this).css('opacity', '0.25');
        },

        // Callback
        response_handler: function(response) {

            // Display pricing table
            if (typeof response === 'object' && typeof response.result !== 'undefined' && response.result === 'success' && response.display) {

                // Maybe update pricing table
                if (typeof jQuery(this).data('rp_wcdpd_html_hash') === 'undefined' || jQuery(this).data('rp_wcdpd_html_hash') !== response.html_hash) {

                    // Update content
                    jQuery(this).html(response.html);

                    // Set hash
                    jQuery(this).data('rp_wcdpd_html_hash', response.html_hash);

                    // Show
                    jQuery(this).show();

                    // Set up pricing table controls
                    set_up_pricing_table_controls();

                    // Trigger event
                    jQuery('body').trigger('rp_wcdpd_volume_pricing_table_updated', response);
                }

                // Change opacity of the element
                jQuery(this).css('opacity', '1.0');
            }
            // Hide pricing table
            else {

                // Hide
                jQuery(this).hide();

                // Unset hash
                jQuery(this).removeData('rp_wcdpd_html_hash');
            }
        }
    });

    /**
     * Set up pricing table controls
     */
    function set_up_pricing_table_controls()
    {

        /**
         * Quantity input control
         */
        jQuery('.rp_wcdpd_pricing_table_quantity[data-rp-wcdpd-from], .rp_wcdpd_pricing_table_product_price[data-rp-wcdpd-from]').on('click', function() {

            // Set flag to inhibit product update request
            inhibit_live_product_update = true;

            // Change quantity
            jQuery('form.cart input[name="quantity"]').val(jQuery(this).data('rp-wcdpd-from')).change();
        });

        /**
         * Variation input control
         */
        jQuery('.rp_wcdpd_pricing_table_product_name[data-rp-wcdpd-variation-attributes], .rp_wcdpd_pricing_table_product_price[data-rp-wcdpd-variation-attributes]').on('click', function() {

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

        /**
         * Modal control
         */
        jQuery('.rp_wcdpd_product_page_modal_link span').on('click', function() {

            if (!jQuery('#rp_wcdpd_modal_overlay').length) {
                jQuery('body').append('<div id="rp_wcdpd_modal_overlay" class="rp_wcdpd_modal_overlay"></div>');
            }

            jQuery('#rp_wcdpd_modal_overlay').on('click', function() {
                jQuery('#rp_wcdpd_modal_overlay').fadeOut();
                jQuery('.rp_wcdpd_modal').fadeOut();
            });

            var pricing_table = jQuery(this).closest('.rp_wcdpd_product_page').parent().find('.rp_wcdpd_modal');
            jQuery('#rp_wcdpd_modal_overlay').fadeIn();
            pricing_table.css('top', '50%').css('left', '50%').css('margin-top', -pricing_table.outerHeight()/2).css('margin-left', -pricing_table.outerWidth()/2).fadeIn();

            return false;
        });
    }

    // Call set up now
    set_up_pricing_table_controls();



});
