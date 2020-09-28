(function (api, $) {
    "use strict";

    function add_spinner(placement) {
        var spinner_html = $('<img class="mo-spinner fetch-email-list" src="' + mailoptin_globals.admin_url + '/images/spinner.gif">');
        $(placement).after(spinner_html);
    }

    function remove_spinner(parent) {
        $('.mo-spinner.fetch-email-list', parent).remove();
    }

    var connection_email_list_handler = function () {

        var parent = $(this).parents('.mo-integration-widget');

        // hide all Jilt fields.
        $('div[class*="JiltConnect_shop_lists"]', parent).hide();

        var connection_service = $("select[name='connection_service']", parent).val();

        if (connection_service !== 'JiltConnect') return;

        var shop_id = $(this).val();

        if(shop_id === '') return;

        add_spinner(this);

        $.post(
            ajaxurl, {
                action: 'mailoptin_customizer_fetch_shop_lists',
                shop_id: shop_id,
                security: $("input[data-customize-setting-link*='[ajax_nonce]']").val()
            },
            function (response) {
                if (_.isObject(response) && 'success' in response && 'data' in response) {

                    var shop_lists_chosen = $(".JiltConnect_shop_lists select[name='JiltConnect_shop_lists']", parent);

                    shop_lists_chosen.html(response.data);
                }

                remove_spinner();

                $('div[class*="JiltConnect_shop_lists"]', parent).show();
            }
        );
    };

    $(window).on('load', function () {
        $(document).on('change', "select[name='connection_email_list']", connection_email_list_handler);
    });

})(wp.customize, jQuery);