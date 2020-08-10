/*!
 * wooinstant
 * WooInstant - WooCommerce Instant / One Page Checkout
 * https://a-web.org/
 * @author AWEB
 * @version 1.0.0
 * Copyright 2018. MIT licensed.
 */
(function($) {
    'use strict';

    jQuery(document).ready(function() {

        //Add To Cart Fly Effect
        $(document).on('click', '.add_to_cart_button:not(.product_type_variable), .single_add_to_cart_button:not(.disabled)', function() {

            $('body').append('<div id="wi-cart-fly"><i class="fa fa-shopping-cart"></i></div>');

            var endPos = $("#wi-toggler").offset();
            var startPos = $(this).offset();

            $('#wi-cart-fly').css({
                'top': startPos.top + 'px',
                'left': startPos.left + 'px'
            })
            .animate({
                opacity: 1,
                top: endPos.top,
                left: endPos.left
            }, 1500, function() {
                $(this).css({
                    'opacity': '0',
                    'z-index': '0'
                });
                $(this).detach();
            });
        });

        // iframeResizer Init
        jQuery('#wi-iframe').iFrameResize({
            log:false,
            enablePublicMethods : true,
            warningTimeout: 60000,
            resizedCallback : function(messageData){
                if( messageData.type == 'init' ){
                    jQuery('#wi-checkout-frame').removeClass('iframe-loading');
                }
                //console.log('Event type:' + messageData.type);
            }
        });

        // Added to cart JS
        // For custom trigger use https://pastebin.com/Y2QpTm1b
        $(document.body).on('added_to_cart', function() {
            if ( wiCartTotal > 0 ) {
                $('#wi-toggler').addClass( 'hascart' );
            } else {
                $('#wi-toggler').removeClass( 'hascart' );
            }
            // Update Cart on added to card
            jQuery('[name="update_cart"]').trigger( 'click' );

            jQuery(document).trigger( 'wi_checkout_refresh' );

        });

        // Update cart total JS
        $(document.body).on('updated_cart_totals', function() {
            jQuery('.woocommerce-cart-form:not(:last)').remove();
            jQuery('.cart_totals:not(:last)').remove();
        });

        // Applied Coupon JS
        $(document.body).on('applied_coupon', function() {
            setTimeout(
                function() {
                    jQuery('.woocommerce-cart-form:not(:last)').remove();
                }, 1000);
        });

        // Remove Coupon JS
        $(document.body).on('removed_coupon', function() {
            jQuery('.woocommerce-cart-form:not(:last)').remove();
        });

        // Update shipping method JS
        $(document.body).on('updated_shipping_method', function() {
            jQuery('.woocommerce-cart-form:not(:last)').remove();
        });

        // Checkout area toggle JS
        $(document).on('click', '#wi-cart-area .checkout-button,#wi-cart-area .wc-proceed-to-checkout a', function(e) {
            e.preventDefault();

            jQuery('#wi-cart-area').fadeOut();
            jQuery('#wi-checkout-area').fadeIn();

            jQuery(document).trigger( 'wi_checkout_refresh' );

        });

        // Back to cart from checkout JS
        $(document).on('click', '#back_to_cart', function() {
            jQuery('#wi-cart-area').fadeIn();
            jQuery('#wi-checkout-area').fadeOut();
        });

        // Update checkout on country changing JS
        $(document).on('change', '#billing_country, #shipping_country, .country_to_state', function() {
            jQuery(document.body).trigger('update_checkout');
        });

        // Payment method clicking JS
        $(document).on('click', '.payment_methods input.input-radio', function() {

            if ($('.payment_methods input.input-radio').length > 1) {
                var target_payment_box = $('div.payment_box.' + $(this).attr('ID')),
                    is_checked = $(this).is(':checked');

                if (is_checked && !target_payment_box.is(':visible')) {
                    $('div.payment_box').filter(':visible').slideUp(230);

                    if (is_checked) {
                        target_payment_box.slideDown(230);
                    }
                }

            } else {
                $('div.payment_box').show();
            }

            if ($(this).data('order_button_text')) {
                $('#place_order').text($(this).data('order_button_text'));
            } else {
                $('#place_order').text($('#place_order').data('value'));
            }

        });

        $(document).on('change', '#ship-to-different-address input', function() {
            jQuery(document.body).trigger('update_checkout');

            $('.shipping_address').hide();
            if ($(this).is(':checked')) {
                $('.shipping_address').slideDown();
            }
        });

        // Payment method clicking JS
        $(document).on('click', '#close_wi', function() {
            $(this).closest('.wi-container').find('#wi-toggler').trigger('click');
        });

    });

})(jQuery);