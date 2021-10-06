/**
 * RightPress Live Product Price Update Scripts
 */

jQuery(document).ready(function() {

    'use strict';

    /**
     * Initialize live product update
     */
    jQuery('.rightpress_live_product_price').rightpress_live_product_update({

        // Params
        ajax_url:   rightpress_live_product_price_update_vars.ajaxurl,
        action:     'rightpress_live_product_price_update',

        // Before send
        before_send: function() {

            // Display placeholder
            add_price_placeholder(jQuery(this).find('span.price'));
        },

        // Callback
        response_handler: function(response) {

            // Display live price
            if (typeof response === 'object' && typeof response.result !== 'undefined' && response.result === 'success' && response.display) {

                // Update label
                jQuery(this).find('dt span.rightpress_live_product_price_label').html(response.label_html);

                // Update price
                var price_html = response.price_html;
                jQuery(this).find('dd span.price').html(price_html);

                // Hide default WooCommerce variation price
                jQuery('body').append('<div id="rightpress_live_product_price_update_hide_default" style="display: none;"><style>div.single_variation_wrap div.single_variation span.price, .single-product div.product .single_variation .price { display: none; }</style></div>');

                // Show
                jQuery(this).slideDown();

                // Trigger event
                jQuery('body').trigger('rightpress_live_product_price_update_updated', response);
            }
            // Hide live price
            else {

                // Hide our price
                jQuery(this).slideUp();
                jQuery(this).find('dt span.rightpress_live_product_price_label').html('');
                jQuery(this).find('dt span.price').html('');

                // Allow default variation price to be displayed
                jQuery('#rightpress_live_product_price_update_hide_default').remove();
            }
        }
    });

    /**
     * Price placeholder animation
     */
    function add_price_placeholder(element)
    {
        var count = 3;

        element.html('<span class="rightpress_dots">. . .</span>');
        var dots = element.find('.rightpress_dots');

        setInterval(add_dot, 400);

        function add_dot()
        {
            if (count < 3) {
                dots.append(' .');
                count++;
            }
            else {
                dots.html('.');
                count = 1;
            }
        }
    }



});
