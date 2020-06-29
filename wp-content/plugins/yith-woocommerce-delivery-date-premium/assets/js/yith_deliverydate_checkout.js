jQuery(document).ready(function ($) {


    var current_shipping_method_id = '',
        current_carrier_id = $('#ywcdd_carrier').val(),
        get_current_shipping_method = function () {

            if ($('.shipping_method').length > 1) {
                return $('.shipping_method:checked').val();
            } else {
                return $('.shipping_method').val();
            }
        },

        ajax_find_date_available = function () {

            var processing_method = $(document).find('#ywcdd_process_method').val(),
                carrier_id = $(document).find('#ywcdd_carrier').val(),
                data = {

                    ywcdd_carrier_id: carrier_id,
                    ywcdd_process_id: processing_method,
                    action: ywcdd_params.actions.update_datepicker
                },
                datepicker = $(document).find('#ywcdd_datepicker');


            datepicker.datepicker('destroy');

            if ('' !== carrier_id) {

                //$('form.checkout').block(block_params);
                $.ajax({
                    type: 'POST',
                    url: ywcdd_params.ajax_url,
                    data: data,
                    dataType: 'json',
                    success: function (response) {
                        //$('form.checkout').unblock();
                        if (typeof response.available_days !== 'undefined') {

                            $(document).trigger('ywcdd_found_available_days', [response.available_days, carrier_id]);
                        }
                        else {
                            $('form.checkout').unblock();
                        }

                    }
                });
            } else {
                $(document).find('.ywcdd_datepicker_content').hide();
                $(document).find('.ywcdd_timeslot_content').hide();
                $('form.checkout').unblock();

            }
        },
        ajax_find_time_slot = function (date_selected) {

            var carrier_id = $(document).find('#ywcdd_carrier').val(),
                data = {
                    ywcdd_carrier_id: carrier_id,
                    ywcdd_date_selected: date_selected,
                    action: ywcdd_params.actions.update_timeslot
                };
            //$('form.checkout').block(block_params);
            $.ajax({
                type: 'POST',
                url: ywcdd_params.ajax_url,
                data: data,
                dataType: 'json',
                success: function (response) {

                    var slots = response.available_timeslot;
                    //$('form.checkout').unblock();
                    $(document).trigger('ywcdd_found_time_slots', [slots, date_selected, carrier_id]);

                }
            });
        },
        update_datepicker_form = function(){

            var processing_method_id = $(document).find('#ywcdd_process_method').val(),
                shipping_id = get_current_shipping_method(),
                data = {
                    ywcdd_update_carrier: 'update_carrier',
                    ywcdd_shipping_id: shipping_id,
                    ywcdd_process_method: processing_method_id,
                    action: ywcdd_params.actions.update_carrier_list
                };

            if (current_shipping_method_id !== shipping_id) {
                $.ajax({
                    type: 'POST',
                    url: ywcdd_params.ajax_url,
                    data: data,
                    dataType: 'json',
                    success: function (response) {
                        if (response.update_delivery_form) {

                            if( response.template !== '' ) {
                                $('.ywcdd_select_delivery_date_content').replaceWith(response.template);
                            }else{
                                $('.ywcdd_select_delivery_date_content').html('');
                            }
                            $('body').trigger('init-delivery-fields');
                        }
                    }

                });
            }
            else {
                $('form.checkout').unblock();
            }
        },
        format_available_days = function( available_days ){

            $.each( available_days, function (i, day ) {

                var date = '';
                try {

                    var new_Date = new Date( day*1000 );
                    new_Date.setTime( new_Date.getTime() + new Date().getTimezoneOffset()*60*1000 );
                    date = $.datepicker.formatDate( ywcdd_params.dateformat, new_Date );

                    available_days[i] = date;
                } catch (error) {

                    date = null;
                }

            });
            return available_days;
        },
        block_params = {
            message: null,
            overlayCSS: {
                background: '#fff',
                opacity: 0.6
            },
            ignoreIfBlocked: true
        },
        init_plugin = function () {
            current_shipping_method_id = get_current_shipping_method();


            $(document).find('#ywcdd_carrier').select2();
            ajax_find_date_available();
        };


    $(document).on('ywcdd_found_available_days', function (e, available_days, carrier_id) {

        var datepicker = $(document).find('#ywcdd_datepicker'),
            datepicker_content = datepicker.parents('.ywcdd_datepicker_content');
        available_days = format_available_days( available_days );


        datepicker.datepicker({
            'dateFormat': ywcdd_params.dateformat,
            'numberOfMonths': ywcdd_params.numberOfMonths * 1,
            'yearSuffix': ywcdd_params.yearSuffix,

            beforeShow: function (input, inst) {
                $('#ui-datepicker-div').removeClass('yith_datepicker');

                $('#ui-datepicker-div').addClass('yith_datepicker');
                setTimeout(function () {
                    $('#ui-datepicker-div').show();
                }, 0);
            },
            beforeShowDay: function (date) {


                var availableDates = available_days,
                    string = $.datepicker.formatDate(ywcdd_params.dateformat, date);

                return [availableDates.indexOf(string) !== -1];
            },
            onSelect: function ($dateSelected, obj) {
                var monthValue = obj.selectedMonth + 1;
                var dayValue = obj.selectedDay;
                var yearValue = obj.selectedYear;
                var all = yearValue + "-" + monthValue + "-" + dayValue;
                $(document).find('.ywcdd_delivery_date').val(all);
                ajax_find_time_slot(all);
            },
            onClose: function (dateSelected, obj) {
                var availableDates = available_days;

                if (typeof availableDates !== 'undefined' && availableDates.indexOf(dateSelected) === -1) {
                    alert('Error: the date ' + dateSelected + ' isn\'t available');
                    datepicker.datepicker('setDate', availableDates[0]);
                    $('.ui-datepicker-current-day').click();
                }
            }
        });


        if (typeof available_days !== 'undefined' && available_days.length > 0) {
            var min = available_days[0],
                max = available_days[available_days.length - 1];
            datepicker.datepicker('option', 'minDate', min);
            datepicker.datepicker('option', 'maxDate', max);

            if( 'yes' === ywcdd_params.show_the_min_date ) {
                datepicker.datepicker('setDate', min);
                $('.ui-datepicker-current-day').click();
            }


        }

        datepicker_content.show();
        if ( 'yes' === ywcdd_params.open_datepicker) {
            datepicker.datepicker('show');
        }
        $('form.checkout').unblock();

    });
    $(document).on('ywcdd_found_time_slots', function (e, slots, date_selected, carrier_id) {

        var timeslot_content = $(document).find('.ywcdd_timeslot_content'),
            timeslot_field = timeslot_content.find('#ywcdd_timeslot'),
            timeslot_av = timeslot_content.find('.ywcdd_timeslot_av');



        timeslot_field.find('option').not("[value='']").remove();

        if (!$.isEmptyObject(slots)) {

            $.each(slots, function (key, value) {
                timeslot_field.append($("<option></option>").attr("value", key).html(value));
            });

            timeslot_content.show();
            timeslot_av.val('yes');

        } else {
            timeslot_av.val('no');
            timeslot_content.hide();
        }
        timeslot_field.select2();
        timeslot_field.trigger('change');
        $('form.checkout').unblock();

    });

    $('form.checkout').on('change', '#ywcdd_timeslot', function (e) {
        if(e.originalEvent !== undefined) {
            $('form.checkout').block(block_params);

        }
        $('body').trigger('update_checkout');
    }).on('change', '#ywcdd_carrier', function (e) {

        var new_carrier_id = $(this).val();
        if( new_carrier_id !== current_carrier_id ) {
            current_carrier_id = new_carrier_id;
            $('form.checkout').block(block_params);
             ajax_find_date_available();
        }
    });

    $( document.body ).on('updated_checkout',function(e, data){
        update_datepicker_form();
    });


    $(document).on('init-delivery-fields', function (e) {
        $('form.checkout').block(block_params);
        init_plugin();
    }).trigger('init-delivery-fields');

    $('body').on('update_checkout', function () {
        $('form.checkout').block(block_params);
    })
});
