jQuery(document).ready(function ($) {

    // define form initialization process
    $.fn.yith_wcac_subscription_form = function () {
        $(this).on('submit', 'form', submit_form);
    };

    // hook initialization process to yith_wcac_subscription_form trigger
    var init_forms = function () {
            var forms = $('.yith-wcac-subscription-form');

            forms.yith_wcac_subscription_form();
        },
        submit_form = function (ev) {
            var form = $(this),
                hide_after = form.data('hide'),
                data;

            ev.preventDefault();

            if ($(this).triggerHandler('yith_wcac_subscription_form_validation') !== false) {
                data = form.serialize();
                data += '&action=' + yith_wcac.actions.subscribe_action;

                $.ajax({
                    beforeSend: function () {
                        form.block({
                            message   : null,
                            overlayCSS: {
                                background: '#fff',
                                opacity   : 0.6
                            }
                        });
                    },
                    complete  : function () {
                        form.unblock();
                    },
                    data      : data,
                    dataType  : 'json',
                    error     : function () {

                    },
                    method    : 'POST',
                    success   : function (data, status, xhr) {
                        if (data.length == 0) {
                            return;
                        }

                        var status = data.status,
                            message = data.message,
                            subscription_notice_container = form.prev('.subscription-notice');

                        subscription_notice_container.fadeOut(300, function () {
                            var message_html = '';

                            if (status) {
                                message_html = '<div class="woocommerce-message">' + message + '</div>';
                            }
                            else {
                                message_html = '<div class="woocommerce-error">' + message + '</div>';
                            }

                            $('html, body').animate({
                                scrollTop: form.parent().offset().top
                            }, 1000);

                            if (status && hide_after == 'yes') {
                                form.fadeOut(300, function () {
                                    subscription_notice_container
                                        .html(message_html)
                                        .fadeIn(300);
                                });
                            }
                            else {
                                subscription_notice_container
                                    .html(message_html)
                                    .fadeIn(300);

                                if (status) {
                                    // reset input fields
                                    form.find('input').each(function () {
                                        var field = $(this);

                                        switch (field.attr('type')) {
                                            case 'submit':
                                            case 'hidden':
                                                break;
                                            case 'radio':
                                            case 'checkbox':
                                                field.removeProp('checked');
                                                break;
                                            default:
                                                field.val('');
                                                break;
                                        }
                                    });

                                    // reset select fields
                                    form.find('select').each(function () {
                                        var select = $(this),
                                            options = select.find('option');

                                        options.removeProp('selected');
                                    });
                                }
                            }

                            $(document).trigger('yith_wcac_form_subscription_result', [data, hide_after]);
                        });
                    },
                    url       : yith_wcac.ajax_url
                });
            }

            return false;
        };

    $(document)
        .on('yith_wcac_subscription_form', init_forms)
        .trigger('yith_wcac_subscription_form');

    // init billing email registration
    if( yith_wcac.abandoned_cart_enable_guest && ! yith_wcac.is_user_logged_in ) {
        var timeout,
            xhr,
            billing_email = $('#billing_email');

        billing_email.on('keyup', function () {
            var t = $(this),
                v = t.val();

            // delete previously configured timeout
            if (timeout) {
                clearTimeout(timeout);
            }

            // check if we should wait for terms agreement
            if ( yith_wcac.abandoned_cart_enable_guest_after_tc ) {
                var terms = $('#terms');

                if ( ! terms.length || ! terms.is(':checked') ) {
                   return;
                }
            }

            // validate email before submitting.
            if( ! v.match( /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i ) ){
                return;
            }

            // submit email via ajx request
            timeout = setTimeout(function () {
                if( xhr ){
                    xhr.abort();
                }

                xhr = $.get( yith_wcac.ajax_url,  {
                    action: yith_wcac.actions.register_session_billing_email_action,
                    security: yith_wcac.nonce.register_session_billing_email,
                    email: v
                } );
            }, 500);
        }).keyup();
        $(document).on( 'change', '#terms', function(){
            billing_email.keyup();
        } );
    }

});