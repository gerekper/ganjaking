jQuery(function ($) {

    $('body')
        .on('click', 'button.ywces-send-test-email', function () {

            var result = $(this).next(),
                email = $(this).prev().attr('value'),
                type = $(this).prev().attr('id').replace('ywces_test_', ''),
                template = $('#ywces_mail_template').val() || 'base',
                coupon = '',
                threshold = '',
                products = '',
                coupon_info = '',
                days_elapsed = '',
                vendor_id = (ywces_admin.vendor_id !== '0') ? '_' + ywces_admin.vendor_id : '',
                re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

            result.show();
            result.removeClass('send-progress send-fail send-success');

            if (!re.test(email)) {

                result.addClass('send-fail');
                result.html(ywces_admin.test_mail_wrong);

            } else {

                switch (type) {

                    case 'purchases':
                    case 'spending':

                        var thresholds = $('#ywces_thresholds_' + type + vendor_id);

                        if (thresholds.find('tr.threshold').size() === 0) {

                            result.addClass('send-fail');
                            result.html(ywces_admin.test_mail_no_threshold);
                            return;

                        }

                        thresholds.find('tr.threshold').each(function () {

                            var row_coupon = $(this).find('.ywces-threshold-coupon').val(),
                                row_threshold = $(this).find('.ywces-threshold-amount').val();

                            if (row_coupon !== '') {
                                coupon = row_coupon;
                                threshold = row_threshold;
                            }

                        });

                        break;

                    case 'product_purchasing':
                    case 'birthday':
                    case 'last_purchase':

                        if (type === 'product_purchasing') {

                            products = $('#ywces_targets_product_purchasing' + vendor_id).val();

                            if (products === '') {

                                result.addClass('send-fail');
                                result.html(ywces_admin.test_mail_no_product);
                                return;

                            }

                        } else if (type === 'last_purchase') {

                            days_elapsed = $('#ywces_days_last_purchase' + vendor_id).val();

                            if (days_elapsed === '') {

                                result.addClass('send-fail');
                                result.html(ywces_admin.test_mail_days_elapsed);
                                return;

                            }
                        }


                        var element = $('#ywces_coupon_' + type + '_settings' + vendor_id);


                        if (element.find('.ywces-coupon-amount').val() === '') {

                            result.addClass('send-fail');
                            result.html(ywces_admin.test_mail_no_amount);
                            return;
                        }

                        coupon_info = {
                            discount_type     : element.find('.ywces-discount-type').val(),
                            coupon_amount     : element.find('.ywces-coupon-amount').val(),
                            expiry_days       : element.find('.ywces-expiry-days').val(),
                            minimum_amount    : element.find('.ywces-minimum-amount').val(),
                            maximum_amount    : element.find('.ywces-maximum-amount').val(),
                            free_shipping     : (element.find('.ywces-free-shipping').is(':checked') ? 'yes' : ''),
                            individual_use    : (element.find('.ywces-individual-use').is(':checked') ? 'yes' : ''),
                            exclude_sale_items: (element.find('.ywces-exclude-sale-items').is(':checked') ? 'yes' : '')
                        };

                        break;

                    default:
                        coupon = $('#ywces_coupon_' + type + vendor_id).val()

                }

                var data = {
                    action      : 'ywces_send_test_mail',
                    email       : email,
                    type        : type,
                    coupon      : coupon,
                    threshold   : threshold,
                    template    : template,
                    products    : products,
                    coupon_info : coupon_info,
                    days_elapsed: days_elapsed,
                    vendor_id   : (ywces_admin.vendor_id !== '0') ? ywces_admin.vendor_id : ''
                };

                result.addClass('send-progress');
                result.html(ywces_admin.before_send_test_email);

                $.post(ywces_admin.ajax_url, data, function (response) {

                    result.removeClass('send-progress');

                    if (response === true) {

                        result.addClass('send-success');
                        result.html(ywces_admin.after_send_test_email);

                    } else {

                        result.addClass('send-fail');
                        result.html(response.error);

                    }

                });

            }

        })
        .on('click', 'button.ywces-purge-coupon', function () {

            var result = $('.ywces-clear-result'),
                data = {
                    action: 'ywces_clear_expired_coupons'
                };

            result.show();
            $(this).hide();

            $.post(ywces_admin.ajax_url, data, function (response) {

                result.removeClass('clear-progress');

                if (response.success) {

                    result.addClass('clear-success');
                    result.html(response.message);

                } else {

                    result.addClass('clear-fail');
                    result.html(response.error);

                }

            });

        });

    $(document).ready(function ($) {

        var collapse = $('.ywces-collapse');

        collapse.each(function () {
            $(this).toggleClass('expand').nextUntil('tr.ywces-collapse').slideToggle(100);
        });

        collapse.click(function () {
            $(this).toggleClass('expand').nextUntil('tr.ywces-collapse').slideToggle(100);
        });

        $('#ywces_mail_template_enable').change(function () {

            if ($(this).is(':checked')) {

                $('#ywces_mail_template').val('base').prop("disabled", true);
                $('.ywces-mailskin').hide();


            } else {

                $('#ywces_mail_template').prop("disabled", false);
                $('.ywces-mailskin').show();

            }

        }).change();

    });
});

