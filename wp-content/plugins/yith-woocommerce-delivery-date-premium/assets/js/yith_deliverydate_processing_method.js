jQuery(function ($) {

    var select_carrier = $('select.ywcdd_select_carrier').length ? $('select.ywcdd_select_carrier').select2() : false;

    if( select_carrier ) {
        is_carrier_system_enabled = select_carrier.data('readonly');

    }
    $('.yith_timepicker').timepicker({
        'timeFormat': yith_delivery_parmas.timeformat,
        'step': yith_delivery_parmas.timestep
    });


    $('.yith_select_all_day').on('click', function (e) {
        e.preventDefault();
        var options = $("#_ywcdd_carrier>option").map(function () {
            return $(this).val();
        });
        select_carrier.val(options).trigger('change');
    });
    $('.yith_select_clear').on('click', function (e) {
        e.preventDefault();
        select_carrier.val(null).trigger('change');
    });

    $('.ywcdd_all_day').on('change', function (e) {

        var row = $(this).parents('li'),
            hidden_value = row.find('.ywcdd_value_en');

        if ($(this).is(':checked')) {
            hidden_value.val('yes');
            $('.ywcdd_enable_day').attr('checked', 'checked').trigger('change');
        } else {
            hidden_value.val('no');
            $('.ywcdd_enable_day').attr('checked', false).trigger('change');
        }
    });
    $('.ywcdd_enable_day').on('change', function (e) {

        var t = $(this),
            row = t.parents('li'),
            hidden_value = row.find('.ywcdd_value_en');

        if (t.is(':checked')) {
            hidden_value.val('yes');
        } else {
            hidden_value.val('no');
            row.find('.yith_timepicker').val('');
        }
    });

    $('#ywcdd_shipping_zones').on('change', function (e) {

        var select_shipping_method = $('#ywcdd_shipping_methods'),
            container_shipping_method = select_shipping_method.parent(),
            zone_id = $(this).val(),
            data = {

                ywcdd_zone_id: zone_id,
                action: yith_delivery_parmas.actions.update_shipping_methods
            };

        select_shipping_method.find('option').not("[value='']").remove();

        if (zone_id !== '') {
            $.ajax({
                type: 'POST',
                url: yith_delivery_parmas.ajax_url,
                data: data,
                dataType: 'json',
                success: function (response) {
                    var options = response.shipping_methods;

                    if (!$.isEmptyObject(options)) {


                        $.each(options, function (key, value) {

                            select_shipping_method
                                .append($("<option></option>")
                                    .attr("value", value['key'])
                                    .html(value['value']))
                                .attr('selected', value['selected'] ? 'selected' : '');
                        });

                        container_shipping_method.removeClass('ywcdd_hide');

                    }
                }
            });
        }

    });
});
