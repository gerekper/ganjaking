jQuery(document).ready(e) {
    // select a customer
    jQuery("select.ajax_chosen_select_customer").selectWoo({
        method: "GET",
        url: giveProducts.admin_ajax_url,
        dataType: "json",
        afterTypeDelay: 100,
        minTermLength: 1,
        data: {
            action: "woocommerce_json_search_customers",
            security: giveProducts.nonce
        }
    }, function(data) {

        var terms = {};

        jQuery.each(data, function(i, val) {
            terms[i] = val;
        });

        return terms;
    });

    // add Chosen various select boxes
    jQuery(function($) {
        jQuery("select.chosen_select").selectWoo();
        jQuery("select.give_products_search").selectWoo({
            method: "GET",
            url: giveProducts.admin_ajax_url,
            dataType: "json",
            afterTypeDelay: 100,
            data: {
                action: "give_products_json_search_products_and_variations",
                security: giveProducts.nonce
            }
        }, function(data) {

            var terms = {};

            $.each(data, function(i, val) {
                terms[i] = val;
            });

            return terms;
        });
    });
};
