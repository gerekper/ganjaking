jQuery(document).ready(function ($) {

    $('body')
        .on('click', 'button.ywrr-unsubscribe, button.ywrac-unsubscribe', function () {

            var form = $('.ywrac-unsubscribe-form');

            if (form.is('.processing')) {
                return false;
            }

            form.addClass('processing');

            var form_data = form.data();

            if (form_data["blockUI.isBlocked"] !== 1) {
                form.block({
                    message   : null,
                    overlayCSS: {
                        background: '#fff',
                        opacity   : 0.6
                    }
                });
            }

            $.ajax({
                type    : 'POST',
                url     : ywrac_unsubscribe.ajax_url + '?action=' + $('#email_type').val(),
                data    : {
                    user_id   : $('#account_id').val(),
                    email     : $('#account_email').val(),
                    email_hash: $('#email_hash').val()
                },
                success : function (code) {

                    // Get the valid JSON only from the returned string
                    if (code.indexOf('<!--WC_START-->') >= 0)
                        code = code.split('<!--WC_START-->')[1]; // Strip off before after WC_START

                    if (code.indexOf('<!--WC_END-->') >= 0)
                        code = code.split('<!--WC_END-->')[0]; // Strip off anything after WC_END

                    // Parse
                    var result = $.parseJSON(code);

                    if (result.status === 'success') {

                        form.find('div').hide();
                        $('.return-to-shop').show();

                    }

                    // Remove old errors
                    $('.woocommerce-error, .woocommerce-message').remove();

                    // Add new errors
                    if (result.messages) {
                        form.prepend(result.messages);
                    } else {
                        form.prepend(code);
                    }

                    // Cancel processing
                    form.removeClass('processing').unblock();

                },
                dataType: 'html'
            });

        });

});