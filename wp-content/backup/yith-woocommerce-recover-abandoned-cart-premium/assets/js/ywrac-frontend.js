(function ($) {

    'use strict';

    /****
     * Grab guest user info
     */

    var inp_email = $('#billing_email'),
        first_name = $('#billing_first_name'),
        last_name = $('#billing_last_name'),
        phone = $('#billing_phone'),
        privacy_wrapper = $('.ywrac-privacy-wrapper'),
        is_valid_email = function (email) {
            var pattern = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
            return pattern.test(email);
        },
        needs_privacy = yith_ywrac_frontend.needs_privacy,
        check_privacy = function () {
            return needs_privacy && !$(document).find('#ywrac-privacy').is(':checked');
        },
        readCookie = function () {
            if (document.cookie.length > 0) {
                var name = 'ywrac_guest_cart',
                    start = document.cookie.indexOf(name + "=");
                if (start != -1) {
                    start = start + name.length + 1;
                    var end = document.cookie.indexOf(";", start);
                    if (end == -1) end = document.cookie.length;
                    return unescape(document.cookie.substring(start, end));
                } else {
                    return "";
                }
            }
            return "";
        },
        deleteCookie = function () {
            if (document.cookie.length > 0) {
                var name = 'ywrac_guest_cart';
                document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
            }
        };

    inp_email.on('change', function () {
        var $t = $(this);

        if (check_privacy()) {
            return;
        }
        if (is_valid_email($t.val()) === true) {
            $.post(yith_ywrac_frontend.ajaxurl, {
                action: 'ywrac_grab_guest',
                email: $t.val(),
                first_name: first_name.val(),
                last_name: last_name.val(),
                currency: yith_ywrac_frontend.currency,
                language: yith_ywrac_frontend.language,
                context: 'frontend',
                phone: phone.val(),
                security: yith_ywrac_frontend.grab_guest_nonce
            }, function (resp) {

            });
        }
    });

    phone.on('change,click', function () {

        if (check_privacy()) {
            return;
        }
        var mail = inp_email.val();
        if (mail && is_valid_email(mail) === true) {
            $.post(yith_ywrac_frontend.ajaxurl, {
                action: 'ywrac_grab_guest_phone',
                first_name: first_name.val(),
                last_name: last_name.val(),
                language: yith_ywrac_frontend.language,
                email: inp_email.val(),
                context: 'frontend',
                phone: phone.val(),
                currency: yith_ywrac_frontend.currency,
                security: yith_ywrac_frontend.grab_guest_phone_nonce
            }, function (resp) {
            });
        }
    });

    if (privacy_wrapper.length > 0) {
        $('#billing_email_field').append(privacy_wrapper);
        $(document).find('.ywrac-privacy-wrapper').addClass('show_privacy');
    }

    $(document).find('#ywrac-privacy').on('change', function () {
        if ($(this).is(':checked')) {
            phone.change();
            inp_email.change();
        } else {
            var ywrac_cookie = parseInt(readCookie());
            if (ywrac_cookie > 0) {

                $.post(yith_ywrac_frontend.ajaxurl, {
                        action: 'ywrac_delete_cart',
                        cart_id: ywrac_cookie,
                        security: yith_ywrac_frontend.delete_cart_nonce
                    }, function (resp) {
                        deleteCookie();
                    }
                );
            }
        }
    });

})(jQuery);