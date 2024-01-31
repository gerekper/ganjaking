jQuery(function ($) {
    var get_unique_id = function (prefix, length) {

            if (prefix === undefined) {
                prefix = '_';
            }

            if (length === undefined) {
                length = 13;
            }


            return prefix + Math.random().toString(length).substr(2, length);

        },
        checkRequiredFields = function (toggle_el) {
            var req_fields = toggle_el.find(':input.yith-required-field'),
                is_valid = 'yes';

            $.each(req_fields, function (i, field) {

                var value = $(field).val();

                if (value === '' || value === null) {

                    if ($(field).hasClass('wc-enhanced-select') || $(field).hasClass('yith-post-search') || $(field).hasClass('yith-term-search')) {
                        $(field).parent().find('.select2.select2-container').addClass('ywcdd_required_field');
                    } else {
                        $(field).addClass('ywcdd_required_field');
                    }
                    is_valid = 'no';
                } else {
                    if ($(field).hasClass('wc-enhanced-select') || $(field).hasClass('yith-post-search') || $(field).hasClass('yith-term-search')) {
                        $(field).parent().find('.select2.select2-container').removeClass('ywcdd_required_field');
                    } else {
                        $(field).removeClass('ywcdd_required_field');
                    }
                }
            });

            return is_valid;
        },
        init_timepicker = function () {
            $(document).find('.yith_timepicker').timepicker({
                'timeFormat': yith_delivery_parmas.timeformat,
                'step': yith_delivery_parmas.timestep,

            });
        },
        format_timeslot_Toggle_Title = function( toggle_row ){
            var title = toggle_row.find('.ywcdd_timeslot_name').val(),
                from = toggle_row.find('.yith_timepicker_from').val(),
                to  = toggle_row.find('.yith_timepicker_to').val(),
                old_title = toggle_row.find('span.title').html(),
                old_subtitle = toggle_row.find('div.subtitle').html();

            var new_title = old_title.replace('%%slot_name%%', title ),
                new_subtitle = old_subtitle.replace('%%timefrom%%', from);

            new_subtitle= new_subtitle.replace('%%timeto%%', to );
            toggle_row.find('span.title').html( new_title );
            toggle_row.find('div.subtitle').html( new_subtitle );
        };
    $(document).on('click', '.yith_delivery_date_fields_container a.ywcdd_add_new_day', function (e) {
        e.preventDefault();

        var counter = $('.ywcdd_estimated_day_row').length,
            list = $('.ywcdd_field_list');
        template = wp.template('ywcdd-carrier-estimated-days');

        template = $(template({index: counter}));


        list.append(template);
    });

    $(document).on('click', '.ywcdd_field_list a.ywcdd_delete_range', function (e) {
        e.preventDefault();

        $(this).parents('.ywcdd_estimated_day_row').remove();
    });
    $(document).on('yith-toggle-change-counter', function (e, hidden_obj, add_box) {

        if ('_ywcdd_addtimeslot_add_box' === add_box.attr('id')) {

            var post_id = $('#post_ID').val(),
                prefix = 'ywcdd_carrier_' + post_id + '_timeslot_',
                new_id = get_unique_id(prefix);

            hidden_obj.val(new_id);
        }
    });

    $(document).on('yith-toggle-element-item-before-update', function( e,toggle, toggle_row, form_is_valid ){

        var is_valid = checkRequiredFields( toggle_row );

        form_is_valid.val(is_valid);

        if( 'yes' === is_valid ) {
            format_timeslot_Toggle_Title(toggle_row);
        }
    });

    $(document).on('yith-toggle-element-item-before-add', function( e,add_box, toggle_row, form_is_valid ){

        var is_valid = checkRequiredFields( add_box );


        form_is_valid.val(is_valid);

        if( 'yes' === is_valid ) {
            format_timeslot_Toggle_Title(toggle_row);
        }
    });

    init_timepicker();

    $(document).on('yith-add-box-button-toggle',function(e, toggle){
        init_timepicker();
    }).on('yith_save_toggle_element_done', function(e, toggle ){
        init_timepicker();
    });

    if( yith_delivery_parmas.disable_timeslot_metabox ){

        $('#yit-carrier-time-slot-metaboxes #_ywcdd_addtimeslot').addClass('yith-disabled');
    }
});
