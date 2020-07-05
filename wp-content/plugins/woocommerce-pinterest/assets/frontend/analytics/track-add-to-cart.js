jQuery(function ($) {


    $(document.body).on('adding_to_cart', function (e, button, data) {

        if (!data.hasOwnProperty('product_id')) {
            return;
        }
        $.ajax({
            url: window.location.origin,
            data: {
                'wc-ajax': 'woocommerce_pinterest_get_product_data',
                'id': data.product_id,
                '_ajax_nonce': wooPinterestAnalyticsConfig.ajaxNonce,
            },
            success: function (response) {

                if (!response.hasOwnProperty('price')) {
                    return;
                }
                if (!response.hasOwnProperty('currency')) {
                    return;
                }

                if (response.userEmail !== undefined && response.isEnhancedMatchEnabled) {

                    try {
                        pintrk('load', response.tagId, {em: response.userEmail});
                    } catch (e) {

                    }

                }

                pintrk('track', 'AddToCart', {
                    value: parseFloat(response.price),
                    order_quantity: data.quantity,
                    currency: response.currency,
                });

            },
            dataType: 'json'
        });
    });
});
