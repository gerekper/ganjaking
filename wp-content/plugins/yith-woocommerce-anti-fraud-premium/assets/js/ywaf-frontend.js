jQuery(function ($) {

    $('.ywaf-resend-email').click(function () {

        var container = $(this).parent();

        if (container.is('.processing')) {
            return false;
        }

        container.addClass('processing');

        container.block({
            message   : null,
            overlayCSS: {
                background: '#fff',
                opacity   : 0.6
            }
        });

        $.ajax({
            type    : 'POST',
            url     : ywaf.ajax_url + '&order_id=' + $('#ywcc_order_id').val(),
            success : function (response) {

                $('.woocommerce-error, .woocommerce-message').remove();

                try {

                    if (response.status === 'success') {

                        container.prepend(response.messages);

                    } else if (response.status === 'failure') {
                        throw 'Result failure';
                    } else {
                        throw 'Invalid response';
                    }
                }

                catch (err) {

                    if (response.messages) {
                        container.prepend(response.messages);
                    } else {
                        container.prepend(response);
                    }

                }

                container.removeClass('processing').unblock();

            },
            dataType: 'json'
        });

        return false;

    });

});