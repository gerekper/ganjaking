'use strict';
jQuery(document).ready(function () {
    function init_bar() {
        if (jQuery('#wbs-content-discount-bar').length > 0) {
            jQuery('body').one('added_to_cart', function (event, fragments, cart_hash, button) {
                jQuery.ajax({
                    type: 'POST',
                    data: 'action=wbs_show_bar&language=' + woocommerce_boost_sales_params.language,
                    url: wboostsales_ajax_url,
                    success: function (response) {
                        if (response.hasOwnProperty('code')) {
                            if (response.code == 200) {
                                jQuery('#wbs-content-discount-bar').html(response.html).css({'position': 'fixed'}).show(200);
                                init_bar();
                            } else if (response.code == 201) {
                                jQuery('#wbs-content-discount-bar').html(response.html).css({'position': ''}).show(200);
                                init_bar();
                            }
                        }
                    },
                    error: function (html) {
                    }
                });
            });
        }
    }

    init_bar();
});

