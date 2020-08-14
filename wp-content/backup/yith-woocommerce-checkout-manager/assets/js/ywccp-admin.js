jQuery(document).ready(function ($) {
    "use strict";

    var add_new = $('#add-new'),
        add_new_input = $('#add-new-name'),
        fields_add_edit_form = $("#ywccp_field_add_edit_form"),
        main_table = $('#ywccp_checkout_fields'),
        init_dialog_form = function (form, title, action, row, is_custom ) {

            form.attr('data-row', row);
            form.attr('data-action', action);

            // remove input for type custom
            if( ! is_custom ) {
                var input = form.find('tr.remove_default');
                if( input.length )
                    input.remove();
            }

            form.find('select[name^="field_type"]').on('change', function () {
                var input = form.find('tr[data-hide]'),
                    value = $(this).val();

                if( ! input.length ) {
                    return
                }

                $.each( input, function(){
                   var deps = $(this).data('hide').split(',');

                    if( $.inArray( value, deps ) > -1 ){
                        $(this).hide();
                    }
                    else {
                        $(this).show();
                    }
                });
            }).trigger('change');



            form.dialog({
                title: title,
                modal: true,
                width: 900,
                resizable: false,
                autoOpen: false,
                buttons: [{
                    text: "Save",
                    click: function () {
                        if ($.edit_add_field(this)) {
                            $(this).dialog("close");
                        }
                    }
                }],
                close: function (event, ui) {
                    form.dialog("destroy");
                    form.remove();
                }
            });

        },
        format_name = function (name) {
            var prepend = main_table.data('prepend');
            // first replace all space with _
            name = Array.isArray(name) ? name[0].value : name.trim();
            name = name.replace(/\s/g, "_");
            if (prepend != '' && name.indexOf(prepend) === -1) {
                name = prepend + name;
            }

            return name;
        };

    // OPEN ADD POPUP

    add_new_input.on('focus', function () {
        $(this).removeClass('required field-exists');
    });

    add_new.on('click', function () {

        var exists,
            val = add_new_input.val();

        if (val == '') {
            add_new_input.addClass('required');
            return false;
        }
        else {

            val = format_name(val);

            exists = main_table.find('input.field_name[value="' + val + '"]');
            if (exists.length) {
                add_new_input.addClass('field-exists');
                return false;
            }
            else {
                // clone the form
                var the_form = fields_add_edit_form.clone();
                // init dialog
                init_dialog_form( the_form, ywccp_admin.popup_add_title, 'add', '', true );
                // set name
                the_form.find('input[name="field_name[]"]').val(val);
                // finally open
                the_form.dialog('open');
            }
        }
    });

    // OPEN EDIT POPUP

    $(document).on('click', 'button.edit_field', function () {

        var tr = $(this).closest('tr'),
            row = tr.data('row'),
            input = tr.find('input[type="hidden"]');

        // clone the form
        var the_form = fields_add_edit_form.clone();

        var field_condition_input_name = tr.find('input[type="hidden"][name="field_condition_input_name[]"]' );

        the_form = add_new_rows_for_conditions( field_condition_input_name, the_form );

        // then load data
        $.each( input, function ( i, hidden ) {

            var name = $(hidden).data('name'),
                form_input = the_form.find('td *[name="' + name + '[]"]');

            if( $(this).hasClass('condition') ){

                var exploded_value = $(hidden).val().split("|");
                var n_conditions = exploded_value.length;
                for ( var index=0; index < n_conditions; index++ ) {
                    form_input = the_form.find('td *[name="' + name + '[]"]').eq( index );
                    if ( form_input.attr('type') == 'checkbox' ) {
                        set_checked_input( form_input, exploded_value[index] );
                    }else{
                        form_input.val( exploded_value[index] );
                    }

                }
            }else{
                if ( form_input.attr('type') == 'checkbox' ) {
                    var value = $(hidden).val();
                    set_checked_input( form_input, value );
                }
                else {
                    form_input.val( $(hidden).val() );
                }
            }


        });

        // first init and open dialog
        init_dialog_form( the_form, ywccp_admin.popup_edit_title, 'edit', row, tr.hasClass('is_custom') );

        // then open
        the_form.dialog('open');
    });


    var set_checked_input = function( input, value ){
        if ( value == 0 ) {
            input.removeAttr('checked');
        }
        else {
            input.attr('checked', 'checked');
        }
    }

    // SET FIELD HANDLER

    $.edit_add_field = function (form) {

        // validate fields
        // here the code for validate fields

        var fields = main_table.find('tbody tr'),
            action = $(form).data('action'),
            new_field,
            index;

        if (action == 'edit') {
            index = $(form).data('row');
            new_field = fields.filter('[data-row="' + index + '"]');
        }
        else {
            new_field = fields.filter(':not(.disabled-row)').last().clone();
            index = fields.size();

            // increment row index
            new_field.attr('data-row', index);
            // add class custom
            new_field.addClass('is_custom');
        }

        // change field value

        $.each(new_field.find('input[type="hidden"]'), function (i, hidden) {
            var name = $(hidden).data('name'),
                form_input = $(form).find('td *[name="' + name + '[]"]'),
                value = '',
                value_td = '';

            if (form_input.length) {
                if (form_input.attr('type') == 'checkbox' ) {
                    if( !form_input.hasClass('condition-field') ){
                        value = form_input.is(':checked') ? 1 : 0;
                        value_td = value == 1 ? ywccp_admin.enabled : '';
                    }else{
                        for ( var index=0; index < form_input.length; index++ ){
                            value = form_input[index].checked ? 1 : 0;
                            value_td = value_td + value + '|';
                        }
                    }
                }
                else {
                    value = form_input.serializeArray();
                    if (name == 'field_name') {
                        value_td = format_name(value);
                    }else{
                        value_td = unserializeValue(value);
                    }
                }

                // set new name
                $(hidden).val(value_td);

                new_field.find('.td_' + name).html(value_td);
            }else if( $(hidden).hasClass('condition') ){
                $(hidden).val('');
            }
        });

        // add new row if add
        if (action == 'add') {
            fields.last().after(new_field);

            // reinit Tooltips
            if( typeof $.fn.tipTip != 'undefined' ) {
                var tiptip_args = {
                    'attribute': 'data-tip',
                    'fadeIn': 50,
                    'fadeOut': 50,
                    'delay': 200
                };
                new_field.find('.tips').tipTip(tiptip_args);
            }
        }

        return true;
    };

    // BULK ACTION

    $('.check-column input').on('change', function () {
        var t = $(this),
            fields_check = $('td.td_select input');

        if ($(this).is(':checked')) {
            fields_check.attr('checked', 'checked');
        }
        else {
            fields_check.removeAttr('checked');
        }
    });

    // DISABLE/ENABLE FIELDS

    $(document).on('click', 'button.enable_field', function () {
        var button = $(this),
            row = button.closest('tr'),
            enable_hidden = row.find('input[data-name="field_enabled"]'),
            button_label;

        row.toggleClass('disabled-row');

        if (enable_hidden.length) {
            enable_hidden.val(row.hasClass('disabled-row') ? '0' : '1');
        }

        // change button label
        button_label = button.html();
        button.html(button.data('label'));
        button.data('label', button_label);

    });

    // REMOVE CUSTOM FIELDS

    var reindex_row = function () {
        var tr = main_table.find('tbody tr');

        tr.each(function (i) {
            $(this).attr('data-row', i);
        });
    };

    $(document).on('click', 'button.remove_field', function () {
        var button = $(this),
            row = button.closest('tr');

        if ( ! row.hasClass( 'is_custom' ) ) {
            return;
        }

        row.fadeOut(400, function () {
            row.addClass('disabled-row').hide();
            row.find('input[data-name="field_deleted"]').val('yes');
        });

    });

    /*=================
     * EDIT ORDER
     ===================*/

    var admin_multiselect = $('.ywccp_multiselect_admin'),
        admin_datepicker = $('.ywccp_datepicker_admin');

    if (admin_multiselect) {
        $.each(admin_multiselect, function () {
            var s = $(this),
                old_value = s.data('value').split(', ');

            s.after('<input type="hidden" name="' + s.attr('name') + '" value>');
            s.on('change', function () {
                var new_value = $(this).val();

                new_value = new_value ? new_value.join(', ') : '';

                $(this).next().val(new_value);

            });

            s.val(old_value).trigger('change');

            if (typeof $.fn.select2 != 'undefined') {
                s.select2();
            }
        });
    }

    if (typeof $.fn.datepicker != 'undefined' && admin_datepicker) {
        $.each(admin_datepicker, function () {
            $(this).datepicker({
                dateFormat: $(this).data('format') || "dd-mm-yy"
            });
        });
    }


    /* Add/remove conditions */
    $(document).on('click','.add-new',function(e){
        e.preventDefault();
        var row = $(this).closest('.single-condition');
        var clone = jQuery(row).clone();
        clone.find('.condition-field').val("").prop('disabled', false).prop('checked', false).removeClass('disabled');
        row.after(clone);
    })

    $(document).on('click','.remove',function(e){
        e.preventDefault();
        $(this).closest('.single-condition').remove();
    })


    var unserializeValue = function( arrayValues ){
        var n = arrayValues.length;
        var unserialized_value = '';
        var i;
        for( i=0; i<n; i++ ){
            if( i != 0 ){
                unserialized_value = unserialized_value + '|';
            }
            unserialized_value = unserialized_value + arrayValues[i].value;

        }
        return unserialized_value;
    }


    var add_new_rows_for_conditions = function( field, form ){
        var value = field[0].value;
        var exploded_value = value.split("|");
        var n_conditions = exploded_value.length;
        var table_conditions = form.find('table.wrap-conditions');

        for ( var i=1; i<n_conditions; i++ ){
            var single_condition = form.find('.single-condition').eq(0).clone();
            single_condition.appendTo(table_conditions);
        }
        return form;
    }


    $(document).on('change','select.field_condition_input_name',function(e){
        if($(this).val() == 'products'){
            $(this).closest('.single-condition').find('select.condition-type').find('option[value="is-set"]').attr("disabled","disabled");
            $(this).closest('.single-condition').find('select.condition-type').find('option[value="is-empty"]').attr("disabled","disabled");
            $(this).closest('.single-condition').find('select.condition-type').find('option[value="has-value"]').attr("disabled","disabled");
            $(this).closest('.single-condition').find('select.condition-type').find('option[value="all-in-cart"]').removeAttr("disabled");
            $(this).closest('.single-condition').find('select.condition-type').find('option[value="at-least-one-in-cart"]').removeAttr("disabled");
        }else{
            $(this).closest('.single-condition').find('select.condition-type').find('option[value="is-set"]').removeAttr("disabled");
            $(this).closest('.single-condition').find('select.condition-type').find('option[value="is-empty"]').removeAttr("disabled")
            $(this).closest('.single-condition').find('select.condition-type').find('option[value="has-value"]').removeAttr("disabled")
            $(this).closest('.single-condition').find('select.condition-type').find('option[value="all-in-cart"]').attr("disabled","disabled");
            $(this).closest('.single-condition').find('select.condition-type').find('option[value="at-least-one-in-cart"]').attr("disabled","disabled");
        }
    });

    $(document).on('change','select.condition-type',function(e){
        if( $(this).val() == 'is-set' || $(this).val() == 'is-empty' ) {
            $(this).closest('.single-condition').find('input.field_condition_value').addClass('disabled');
        }else{
            $(this).closest('.single-condition').find('input.field_condition_value').removeClass('disabled');
        }
    });

});