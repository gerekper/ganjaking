/**
 * WooCommerce Dynamic Pricing & Discounts - Promotion - Product Banners - Scripts
 */
jQuery(document).ready(function() {

    'use strict';

    /**
     * Set up live update
     */
    jQuery('#rp_wcdpd_promotion_product_banners_container').rightpress_live_product_update({

        // Params
        ajax_url:   rp_wcdpd_promotion_product_banners_scripts_vars.ajaxurl,
        action:     'rp_wcdpd_load_product_banner',

        // Before send
        before_send: function(xhr) {

            // Reference container
            var container = jQuery(this);

            // Change opacity of the container
            container.css('opacity', '0.25');
        },

        // Callback
        response_handler: function(response) {

            // Reference container
            var container = jQuery(this);

            // Display banners
            if (typeof response === 'object' && typeof response.result !== 'undefined' && response.result === 'success' && response.display) {

                // Maybe update banners
                if (typeof container.data('rp_wcdpd_banners_hash') === 'undefined' || container.data('rp_wcdpd_banners_hash') !== response.banners_hash) {

                    // Clear container
                    container.html('');

                    // Iterate over banners
                    jQuery.each(response.banners, function(banner_hash, banner) {

                        // Add to container
                        container.append(banner.html);
                    });

                    // Set hash
                    container.data('rp_wcdpd_banners_hash', response.banners_hash);

                    // Show container
                    container.show();

                    // Trigger event
                    jQuery('body').trigger('rp_wcdpd_promotion_product_banners_updated', response);
                }

                // Change opacity of the container
                container.css('opacity', '1.0');
            }
            // Hide banners
            else {

                // Hide container
                container.hide();

                // Unset hash
                container.removeData('rp_wcdpd_banners_hash');
            }
        }
    });





});
