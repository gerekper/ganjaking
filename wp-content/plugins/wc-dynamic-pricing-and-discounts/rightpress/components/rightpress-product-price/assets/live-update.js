/**
 * RightPress Product Price Live Update Scripts
 */

jQuery(document).ready(function() {

    'use strict';

    /**
     * Maybe modify default WooCommerce price element
     */
    if (rightpress_product_price_live_update_vars.replace_wc_price) {
        jQuery('.product .price').first().addClass('rightpress_product_price_live_update_price');
    }

    /**
     * Initialize live product update
     */
    jQuery('.rightpress_product_price_live_update_price').rightpress_live_product_update({

        // Params
        ajax_url:   rightpress_product_price_live_update_vars.ajaxurl,
        action:     'rightpress_product_price_live_update',

        // Before send
        before_send: function() {

            // Change opacity of the element
            jQuery(this).closest('.rightpress_product_price_live_update').css('opacity', '0.25');
        },

        // Callback
        response_handler: function(response) {

            var container = jQuery(this).closest('.rightpress_product_price_live_update');

            // Check if valid response was received
            var is_valid_response = (typeof response === 'object' && typeof response.result !== 'undefined' && response.result === 'success');

            // Maybe hide live price element
            if (!rightpress_product_price_live_update_vars.replace_wc_price && (!is_valid_response || !response.display)) {

                // Hide our price
                container.slideUp();
                container.find('.rightpress_product_price_live_update_label').html('');
                container.find('.price').html('');

                // Allow default variation price to be displayed
                jQuery('#rightpress_product_price_live_update_hide_default').remove();
            }
            // Element does not need to be hidden and response is valid
            else if (is_valid_response) {

                // Update price
                var price_html = response.price_html;
                jQuery(this).html(price_html);

                // Separate element is displayed
                if (!rightpress_product_price_live_update_vars.replace_wc_price) {

                    // Update label
                    container.find('span.rightpress_product_price_live_update_label').html(response.label_html);

                    // Hide default WooCommerce variation price
                    jQuery('body').append('<div id="rightpress_product_price_live_update_hide_default" style="display: none;"><style>div.single_variation_wrap div.single_variation span.price, .single-product div.product .single_variation .price { display: none; }</style></div>');

                    // Show
                    container.slideDown();
                }

                // Trigger event
                jQuery('body').trigger('rightpress_product_price_live_update_updated', response);
                jQuery('body').trigger('rightpress_live_product_price_update_updated', response);   // Legacy
            }

            // Change opacity of the element
            container.css('opacity', '1.0');
        }
    });





});
