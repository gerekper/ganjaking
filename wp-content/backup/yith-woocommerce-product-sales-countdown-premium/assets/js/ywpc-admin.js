// DATE PICKER FIELDS
jQuery(function ($) {

    $('.ywpc-dates input').datetimepicker({
        defaultDate    : '',
        dateFormat     : 'yy-mm-dd',
        numberOfMonths : 1,
        showButtonPanel: true,
        onSelect       : function (selectedDate) {
            var option = $(this).is('.ywpc_sale_price_dates_from') ? 'minDate' : 'maxDate';

            var instance = $(this).data('datepicker'),
                date = $.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat, selectedDate, instance.settings),
                value = $(this).val();

            if (option === 'minDate') {
                $('.ywpc_sale_price_dates_to').datetimepicker('option', option, date);
            } else {
                $('.ywpc_sale_price_dates_from').datetimepicker('option', option, date);
            }

            if (!$('#_ywpc_variations_global_countdown').is(':checked')) {

                if ($(this).is('#_ywpc_sale_price_dates_from')) {

                    $('.ywpc_sale_price_dates_from').val(value);

                } else {

                    $('.ywpc_sale_price_dates_to').val(value);

                }

                $('.woocommerce_variation').addClass('variation-needs-update');
                $('.save-variation-changes').removeAttr('disabled');
                $('.cancel-variation-changes').removeAttr('disabled');

            }


        }

    });

    var dates = $('#_ywpc_sale_price_dates_from, #_ywpc_sale_price_dates_to');

    dates.datetimepicker({
        defaultDate    : '',
        dateFormat     : 'yy-mm-dd',
        numberOfMonths : 1,
        showButtonPanel: true,
        onSelect       : function (selectedDate) {
            var option = $(this).is('#_ywpc_sale_price_dates_from') ? 'minDate' : 'maxDate';

            var instance = $(this).data('datepicker'),
                date = $.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat, selectedDate, instance.settings);

            dates.not(this).datetimepicker('option', option, date);

        }

    });

    function unlock_variations() {
        $('.woocommerce_variation').addClass('variation-needs-update');
        $('.save-variation-changes').removeAttr('disabled');
        $('.cancel-variation-changes').removeAttr('disabled');

    }

    $(document).ready(function () {

        var checkbox = $('#_ywpc_enabled');

        checkbox.change(function () {

            if ($(this).is(':checked')) {

                $('#_ywpc_sale_price_dates_from').removeAttr('disabled');
                $('#_ywpc_sale_price_dates_to').removeAttr('disabled');
                $('#_ywpc_discount_qty').removeAttr('disabled');
                $('#_ywpc_sold_qty').removeAttr('disabled');
                $('#_ywpc_variations_global_countdown').removeAttr('disabled');
                $('.ywpc-variation-field').each(function () {

                    $(this).removeAttr('disabled');

                });

            } else {

                $('#_ywpc_sale_price_dates_from').attr('disabled', 'disabled').val('');
                $('#_ywpc_sale_price_dates_to').attr('disabled', 'disabled').val('');
                $('#_ywpc_discount_qty').attr('disabled', 'disabled').val('');
                $('#_ywpc_sold_qty').attr('disabled', 'disabled').val('');
                $('#_ywpc_variations_global_countdown').attr('disabled', 'disabled').val('');
                $('.ywpc-variation-field').each(function () {

                    $(this).attr('disabled', 'disabled').val('');

                });

            }

            unlock_variations();

        }).change();

        $('#_ywpo_preorder').change(function () {

            if ($(this).is(':checked')) {

                $('#_ywpc_sale_price_dates_from').attr('disabled', 'disabled');
                $('#_ywpc_sale_price_dates_to').attr('disabled', 'disabled');

            } else {

                if ($('#_ywpc_enabled').is(':checked')) {

                    $('#_ywpc_sale_price_dates_from').removeAttr('disabled');
                    $('#_ywpc_sale_price_dates_to').removeAttr('disabled');

                }

            }

        }).change();

        $('#_ywpc_variations_global_countdown').change(function () {

            if ($(this).is(':checked')) {


                $('.ywpc-variation-field').each(function () {

                    $(this).attr('disabled', 'disabled').val('');

                });

            } else {

                $('.woocommerce_variation .ywpc_sale_price_dates_from').removeAttr('disabled').val($('#_ywpc_sale_price_dates_from').val());
                $('.woocommerce_variation .ywpc_sale_price_dates_to').removeAttr('disabled').val($('#_ywpc_sale_price_dates_to').val());
                $('.woocommerce_variation .ywpc_discount_qty').removeAttr('disabled').val($('#_ywpc_discount_qty').val());
                $('.woocommerce_variation .ywpc_sold_qty').removeAttr('disabled').val($('#_ywpc_sold_qty').val());

            }

            unlock_variations();

        }).change();

        $('#_ywpc_discount_qty').change(function () {

            if (!$('#_ywpc_variations_global_countdown').is(':checked')) {

                $('.ywpc_discount_qty').val($(this).val());
                unlock_variations();

            }

        }).change();

        $('#_ywpc_sold_qty').change(function () {

            if (!$('#_ywpc_variations_global_countdown').is(':checked')) {

                $('.ywpc_sold_qty').val($(this).val());
                unlock_variations();

            }

        }).change();

        $('.ywpc-countdown-admin').each(function () {

            var timer = $('input', this).val().split('.'),
                countdown_html = $(this).clone(),
                first_char = ' .ywpc-char-' + (!ywpc.is_rtl ? '1' : '2'),
                second_char = ' .ywpc-char-' + (!ywpc.is_rtl ? '2' : '1'),
                first_char_days = ' .ywpc-char-' + (!ywpc.is_rtl ? '0' : '2'),
                second_char_days = ' .ywpc-char-' + (!ywpc.is_rtl ? '2' : '0');

            $('.ywpc-days' + first_char_days, countdown_html).text('{d100}');
            $('.ywpc-days .ywpc-char-1', countdown_html).text('{d10}');
            $('.ywpc-days' + second_char_days, countdown_html).text('{d1}');

            $('.ywpc-hours' + first_char, countdown_html).text('{h10}');
            $('.ywpc-hours' + second_char, countdown_html).text('{h1}');

            $('.ywpc-minutes' + first_char, countdown_html).text('{m10}');
            $('.ywpc-minutes' + second_char, countdown_html).text('{m1}');

            $('.ywpc-seconds' + first_char, countdown_html).text('{s10}');
            $('.ywpc-seconds' + second_char, countdown_html).text('{s1}');

            $(this).countdown({
                until : $.countdown.UTCDate(
                    ywpc.gmt,
                    timer[0],
                    timer[1],
                    timer[2],
                    timer[3],
                    timer[4]
                ),
                layout: countdown_html.html()
            });

        });

    });

    $('li.product_countdown_tab a').on('click', function () {

        $('#variable_product_options').trigger('reload');

    });

    $('#woocommerce-product-data').on('woocommerce_variations_loaded', function () {

        var wrapper = $('#woocommerce-product-data');
        $('.ywpc-dates', wrapper).each(function () {
            var dates = $(this).find('input').datetimepicker({
                defaultDate    : '',
                dateFormat     : 'yy-mm-dd',
                numberOfMonths : 1,
                showButtonPanel: true,
                onSelect       : function (selectedDate) {
                    var option = $(this).is('.ywpc_sale_price_dates_from') ? 'minDate' : 'maxDate',
                        instance = $(this).data('datepicker'),
                        date = $.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat, selectedDate, instance.settings);

                    dates.not(this).datetimepicker('option', option, date);
                    $(this).change();

                    $('.woocommerce_variation').addClass('variation-needs-update');

                }
            });
        });

        $('.ywpc-variation-field').each(function () {

            if ($('#_ywpc_variations_global_countdown').is(':checked') || (!$('#_ywpc_enabled').is(':checked'))) {

                $(this).attr('disabled', 'disabled');

            } else {

                $(this).removeAttr('disabled');

            }

        });

        $('.variable_is_preorder').change(function () {

            if ($(this).is(':checked')) {

                $(this).parent().parent().parent().find('.ywpc-variation-field').attr('disabled', 'disabled');
                $('#_ywpc_enabled').prop('checked', true);

            } else {

                $(this).parent().parent().parent().find('.ywpc-variation-field').removeAttr('disabled');

            }

        });

    });

    $('#ywpc_before_sale_start').change(function () {

        var ywpc_tag_minimum_value = $('#ywpc_before_sale_start_status').parent().parent().parent().parent();

        if ($(this).is(':checked')) {

            ywpc_tag_minimum_value.show();

        } else {

            ywpc_tag_minimum_value.hide();

        }

    }).change();

});
