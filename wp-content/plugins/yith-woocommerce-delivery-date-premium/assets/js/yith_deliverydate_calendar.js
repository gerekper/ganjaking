var get_unique_id = function (prefix, length) {

    if (prefix === undefined) {
        prefix = '_';
    }

    if (length === undefined) {
        length = 13;
    }


    return prefix + Math.random().toString(length).substr(2, length);

};

var calendar = jQuery('#ywcdd_general_calendar'),
    block_params = {
        message: null,
        overlayCSS: {
            background: '#fff',
            opacity: 0.6
        },
        ignoreIfBlocked: true
    },
    render_calendar = function () {
        var $events_json = calendar.data('ywcdd_events_json');
        calendar.fullCalendar({
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            defaultDate: ywcdd_calendar_params.starday,
            locale: ywcdd_calendar_params.calendar_language,
            aspectRatio: 1.8,
            editable: false,
            eventLimit: true, // allow "more" link when too many events
         //   events: $events_json,
            timeFormat: 'H:mm',
            buttonIcons: false,
            navLinks: true,
            lazyFetching:true,
            showNonCurrentDates:false,
            events: function(start, end, timezone, callback) {
                var data ={
                    action: ywcdd_calendar_params.actions.show_current_month,
                    start_date: start.format(),
                    end_date:end.format()
                };
                jQuery.ajax({
                    type: 'POST',
                    url: ywcdd_calendar_params.ajax_url,
                    data: data,
                    dataType: 'json',
                    beforeSend:function(){
                        jQuery('#ywcdd_general_calendar').block(block_params);
                    },
                    success: function (events) {
                        callback(events);
                    },
                    complete:function(){
                        jQuery('#ywcdd_general_calendar').unblock();
                    }
                });
            },
            eventRender: function (event, element, view) {
                // we can remove only holiday

                if (event.event_type == 'holiday') {

                    element.append("<span class='ywcdd_delete_calendar'></span>");
                    element.on('click', '.ywcdd_delete_calendar', function (e) {

                        var data = {
                            ywcdd_event_id: event.id,
                            action: ywcdd_calendar_params.actions.delete_calendar_holidays
                        };

                        jQuery.ajax({
                            type: 'POST',
                            url: ywcdd_calendar_params.ajax_url,
                            data: data,
                            dataType: 'json',
                            success: function (response) {

                                if (response.result === 'deleted') {
                                    jQuery('#ywcdd_general_calendar').fullCalendar('removeEvents', event.id);
                                }
                            }

                        });


                    });
                }
                element.find('.fc-title').html(element.find('.fc-title').text());

            }
        });
        jQuery('.fc-button-group button').addClass('button-primary');
        jQuery('.fc-left button.fc-prev-button').html('< ' + jQuery('.fc-left button.fc-prev-button').html());
        jQuery('.fc-left button.fc-next-button').html(jQuery('.fc-left button.fc-next-button').html() + ' >');
    },
    getDate = function (element) {
        var date;
        try {
            date = $.datepicker.parseDate(ywcdd_calendar_params.dateformat, element.value);
        } catch (error) {

            console.log( error );
            date = null;
        }

        return date;
    },
    saveHolidayElement = function (element, spinner) {

        var formData = element.serializeToggleElement();

        jQuery.ajax({
            type: 'POST',
            url: ywcdd_calendar_params.ajax_url + '?action=' + ywcdd_calendar_params.actions.add_holidays,
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {

                if (spinner) {
                    spinner.removeClass('show');
                }

                if( response.title ){
                    element.find('.yith-toggle-title .title').html( response.title );
                }

                if( response.subtitle ){
                    element.find('.yith-toggle-title .subtitle').html( response.subtitle );
                }

               calendar.fullCalendar( 'refetchEvents');
            }
        });

    },
    updateHolidayElement = function (element, spinner) {
        var formData = element.serializeToggleElement();

        jQuery.ajax({
            type: 'POST',
            url: ywcdd_calendar_params.ajax_url + '?action=' + ywcdd_calendar_params.actions.update_holidays,
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {

                if (spinner) {
                    spinner.removeClass('show');
                }

                calendar.fullCalendar( 'refetchEvents');

            }
        });
    },
    deleteHolidayElement = function (element) {

        var item_key = element.data('item_key'),
            data = {
                item_key: item_key,
                action: ywcdd_calendar_params.actions.delete_holidays
            };

        jQuery.ajax({
            type: 'POST',
            url: ywcdd_calendar_params.ajax_url,
            data: data,
            dataType: 'json',
            beforeSend:function(){
             jQuery('#ywcdd_general_calendar').block();
            },
            success: function (response) {

                var events_json = response.result;
                calendar.fullCalendar( 'refetchEvents');
            },

        });
    },

    init_datepicker = function () {
        var datepickers = jQuery(document).find('.ywcdd_datepicker');

        datepickers.each(function(){
            jQuery(this).datepicker('destroy');
            jQuery(this).datepicker(
                {'dateFormat': ywcdd_calendar_params.dateformat}
            );
        });

        jQuery(document).find('.holiday_from').datepicker("option", "minDate", 0).on('change', function () {
            var from_field = jQuery(this),
                parent = from_field.parent().parent().parent(),
                to_field = parent.find('.holiday_to');

            if (from_field.val() !== '') {
                to_field.datepicker("option", 'minDate', getDate(this));
            } else {
                to_field.datepicker("option", 'minDate', 0);
            }
        });
    };


jQuery(function ($) {


    if ($('#yith_delivery_date_panel_general-calendar').length) {
        $(document).off('click', '.yith-add-box-buttons .yith-save-button');
        $(document).off('click', '.yith-toggle-row .yith-save-button');
        $(document).off('click', '.yith-toggle-onoff');
        $(document).off('click', '.yith-toggle-row .yith-delete-button');
    }

    $(document).on('click', '#ywcdd_holidays_option_add_box .yith-save-button', function (e) {
        e.preventDefault();
        var add_box = $(this).parents('.yith-add-box'),
            id = $(this).closest('.yith-toggle_wrapper').attr('id'),
            spinner = add_box.find('.spinner'),
            toggle_element = $(this).parents('.toggle-element'),
            fields = add_box.find(':input'),
            counter = get_unique_id('ywcdd_holiday_'),
            template = wp.template('yith-toggle-element-item-'+ id),
            toggle_el = $(template({index: counter}));

        var form_is_valid =  add_box.checkRequiredFields();

        if( 'yes' === form_is_valid ) {
            spinner.addClass('show');

            $.each(fields, function (i, field) {
                if (typeof $(field).attr('id') != 'undefined') {
                    $field_id = $(field).attr('id');
                    $field_id = $field_id.replace('new_','')+'_'+counter;
                    $field_val = $(field).val();

                    $(toggle_el).find('#' + $field_id).val($field_val);

                    if ($(field).is(':checked')) {
                        $(toggle_el).find('#' + $field_id).prop('checked', true);
                    }
                }
            });

            $(document).trigger('yith-toggle-element-item-before-add', [add_box, toggle_el]);

            if (toggle_element.find('.yith-toggle-row').length) {
                $(toggle_el).insertAfter(toggle_element.find('.yith-toggle-row').last());
            } else {
                $(toggle_el).appendTo(toggle_element.find('td'));
            }

            $(document.body).trigger('wc-enhanced-select-init');

            saveHolidayElement(toggle_el, spinner);
            $(add_box).toggle();
          init_datepicker();
        }
    });
    $(document).on('click', '#yith_delivery_date_panel_general-calendar .yith-toggle-row .yith-save-button', function (e) {
        e.preventDefault();
        var toggle = $(this).closest('.toggle-element'),
            toggle_row = $(this).closest('.yith-toggle-row'),
            spinner = toggle_row.find('.spinner');
        var form_is_valid =  toggle_row.checkRequiredFields();

        if( form_is_valid ) {
            spinner.addClass('show');
            updateHolidayElement(toggle_row, spinner);
        }
    });
    //register onoff status
    $(document).on('click', '#yith_delivery_date_panel_general-calendar .yith-toggle-onoff', function (event) {
        event.preventDefault();
        var toggle = $(this).closest('.toggle-element'),
            toggle_row = $(this).closest('.yith-toggle-row');
        updateHolidayElement(toggle_row);
    });

    $(document).on('click', '#yith_delivery_date_panel_general-calendar .yith-toggle-row .yith-delete-button', function (e) {
        e.preventDefault();
        var toggle = $(this).closest('.toggle-element'),
            toggle_row = $(this).closest('.yith-toggle-row');
        toggle_row.remove();
        deleteHolidayElement(toggle_row);
    });

    $(document).on( 'change', '#yith_delivery_date_panel_general-calendar :input.yith-required-field',function(e){

        var val = $(this).val();

        if( val !== '' || val !== null ){
            if( $(this).hasClass('wc-enhanced-select') ){
                $(this).parent().find('.select2.select2-container').removeClass('ywcdd_required_field');
            }else{
                $(this).removeClass('ywcdd_required_field');
            }
        }
    });
    render_calendar();
    init_datepicker();


    $(document).on('yith-add-box-button-toggle',function (e, button) {

        init_datepicker();
    } );

});
