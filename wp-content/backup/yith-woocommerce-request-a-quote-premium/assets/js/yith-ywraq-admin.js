/**
 * Javascript functions to administrator pane
 *
 * @package YITH Woocommerce Request A Quote
 * @since   1.0.0
 * @version 1.0.0
 * @author  YITH
 */
jQuery(document).ready(function ($) {
    "use strict";

    var select = $(document).find('.yith-ywraq-chosen');

    $('#_ywraq_safe_submit_field').val('');

    select.each(function () {
        if ($.fn.chosen !== undefined) {
            $(this).chosen({
                width         : '350px',
                disable_search: true,
                multiple      : true
            })
        }
    });


    //Contact form selection
    var yit_contact_form = $('select.yit-contact-form').parent().parent(),
        contact_form_7 = $('select.contact-form-7').parent().parent(),
        gravity_forms = $('select.gravity-forms').parent().parent();

    $('select#ywraq_inquiry_form_type').change(function () {

        var option = $('option:selected', this).val();

        switch (option) {
            case "yit-contact-form":
                yit_contact_form.show();
                contact_form_7.hide();
                gravity_forms.hide();
                break;
            case "contact-form-7":
                yit_contact_form.hide();
                gravity_forms.hide();
                contact_form_7.show();
                break;
            case "gravity-forms":
                yit_contact_form.hide();
                contact_form_7.hide();
                gravity_forms.show();
                break;
            default:
                yit_contact_form.hide();
                contact_form_7.hide();
                gravity_forms.hide();
        }

    }).change();


    //Order functions
    $('#ywraq_submit_button').on('click', function (e) {
        e.preventDefault();
        $('#_ywraq_safe_submit_field').val('send_quote');

        $(this).closest('form').submit();
    });

    //Order functions
    $('#ywraq_pdf_button').on('click', function (e) {
        e.preventDefault();
        $('#_ywraq_safe_submit_field').val('create_preview_pdf');

        $(this).closest('form').submit();
    });

    //datepicker
    if ($('#_ywcm_request_expire').length > 0) {
        $('#_ywcm_request_expire').each(function () {
            $.datepicker.setDefaults({
                gotoCurrent: true,
                dateFormat : 'yy-mm-dd'
            });
            $(this).datepicker('option', 'minDate', "1d");

        });
    }


    //Metabox Pay Quote Now
    $(document).on('change', $('#_ywraq_pay_quote_now'), function() { check_pay_quote_now(); } );

    function check_pay_quote_now() {
        var value = $('#_ywraq_pay_quote_now').val(),
            $dom1 = $('#_ywraq_checkout_info').closest('.the-metabox'),
            $dom2 = $('#_ywraq_lock_editing').closest('.the-metabox'),
            $dom3 = $('#_ywraq_disable_shipping_method').closest('.the-metabox'),
            $dom5 = $('#_ywraq_deposit_rate').closest('.the-metabox'),
            $dom4 = $('#_ywraq_deposit_enable').closest('.the-metabox');
        if ( value == 0 || value == 'no' ) {
            $dom1.show();
            $dom2.show();
            $dom3.show();
            $dom4.show();
            $dom5.show();
        } else {
            $dom1.hide();
            $dom2.hide();
            $dom3.hide();
            $dom4.hide();
            $dom5.hide();
        }
    }

    check_pay_quote_now();

    //Metabox Pay Quote Now
    $(document).on('change', $('#_ywraq_deposit_enable'), function() { check_deposit_enabled(); } );

    function check_deposit_enabled() {
        var value = $('#_ywraq_deposit_enable').val(),
            pay_now = $('#_ywraq_pay_quote_now').val(),
            $rate = $('#_ywraq_deposit_rate').closest('.the-metabox');
        if ( pay_now == 0 || pay_now == 'no' ) {
            if ( value == 0 || value == 'no' ) {
                $rate.hide();
            } else {
                $rate.show();
            }
        }

    }

    check_deposit_enabled();



    $('#ywraq_pdf_file').attr('disabled', 'disabled');

    /**************************
     DEFAULT FORM
     **************************/
    var add_new = $('#add-new'),
        add_new_input = $('#add-new-name'),
        fields_add_edit_form = $("#ywraq_field_add_edit_form"),
        main_table = $('#ywraq_form_fields'),
        init_dialog_form = function (form, title, action, row, is_custom) {

            form.attr('data-row', row);
            form.attr('data-action', action);

            // remove input for type custom
            if (!is_custom) {
                var input = form.find('tr.remove_default');
                if (input.length)
                    input.remove();
            }

            form.find('select[name="field_type"]').on('change', function () {
                var input = form.find('tr[data-hide]'),
                    value = $(this).val();

                if (!input.length) {
                    return
                }

                $.each(input, function () {
                    var deps = $(this).data('hide').split(',');

                    if ($.inArray(value, deps) > -1) {
                        $(this).hide();
                    }
                    else {
                        $(this).show();
                    }
                });
            }).trigger('change');

            form.dialog({
                title    : title,
                modal    : true,
                width    : 500,
                resizable: false,
                autoOpen : false,
                buttons  : [{
                    text : ywraq_admin.default_form_submit_label,
                    click: function () {
                        if ($.edit_add_field(this)) {
                            $(this).dialog("close");
                        }
                    }
                }],
                close    : function (event, ui) {
                    form.dialog("destroy");
                    form.remove();
                }
            });

        },
        format_name = function (name) {
            // first replace all space with _
            name = name.trim();
            name = name.replace(/\s/g, "_");
            var regex = /[^A-Za-z0-9_]+/gi;
            name = name.replace(regex, "");
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
                init_dialog_form(the_form, ywraq_admin.popup_add_title, 'add', '', true);
                // set name
                the_form.find('input[name="field_name"]').val(val);
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

        // then load data
        $.each(input, function (i, hidden) {
            var name = $(hidden).data('name'),
                form_input = the_form.find('td *[name="' + name + '"]');

            if (form_input.attr('type') == 'checkbox') {
                var value = $(hidden).val();
                if (value == 0) {
                    form_input.removeAttr('checked');
                }
                else {
                    form_input.attr('checked', 'checked');
                }
            }
            else {
                form_input.val($(hidden).val());
            }
        });

        // first init and open dialog
        init_dialog_form(the_form, ywraq_admin.popup_edit_title, 'edit', row, tr.hasClass('is_custom'));

        // then open
        the_form.dialog('open');
    });

    // EDIT ADD FIELD HANDLER

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
                form_input = $(form).find('td *[name="' + name + '"]'),
                value = '',
                value_td = '';

            if (form_input.length) {
                if (form_input.attr('type') == 'checkbox') {
                    value = form_input.is(':checked') ? 1 : 0;
                    value_td = value == 1 ? ywraq_admin.enabled : '-';
                }
                else {
                    value = form_input.val();
                    if (name == 'field_name') {
                        value = format_name(value);
                    }
                    value_td = value;
                }

                // set new name
                $(hidden).val(value);

                new_field.find('.td_' + name).html(value_td);
            }
        });

        // add new row if add
        if (action == 'add') {
            fields.last().after(new_field);

            // reinit Tooltips
            if (typeof $.fn.tipTip != 'undefined') {
                var tiptip_args = {
                    'attribute': 'data-tip',
                    'fadeIn'   : 50,
                    'fadeOut'  : 50,
                    'delay'    : 200
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

        if (!row.hasClass('is_custom')) {
            return;
        }

        row.fadeOut(400, function () {
            row.addClass('disabled-row').hide();
            row.find('input[data-name="field_deleted"]').val('yes');
        });

    });


    //SAVE FORM
    $(document).on('click', '.save-form', function (e) {
        e.preventDefault();

        var form = $('#ywraq_form_fields_form');

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
            url     : ywraq_admin.ajax_url + '?action=ywraq_save_default_form',
            data    : $("#ywraq_form_fields_form :input").serialize(),
            success : function () {

                // Cancel processing
                location.reload()
            }
            ,
            dataType: 'html'
        })
        ;


    });


    /**
     * Editor dei form compatibili
     * @type {*|jQuery}
     */
    var cf7_link = $(document).find('.ywraq_cf7_link'),
        gf_link = $(document).find('.ywraq_gf_link');

    cf7_link.each(function () {
        var $t = $(this),
            select = $t.closest('.forminp-select').find('select'),
            link = $t.attr('href'),
            value = select.val();

        if (value != 0) {
            $t.attr('href', link + '&post=' + value + '&action=edit');
        }

        select.on('change', function () {
            var newvalue = $(this).val();
            $t.attr('href', link + '&post=' + newvalue + '&action=edit');
        });
    });

    gf_link.each(function () {
        var $t = $(this),
            select = $t.closest('.forminp-select').find('select'),
            link = $t.attr('href'),
            value = select.val();

        if (value != 0) {
            $t.attr('href', link + '&id=' + value);
        }

        select.on('change', function () {
            var newvalue = $(this).val();
            $t.attr('href', link + '&id=' + newvalue);
        });
    });

    /*FORM SETTINGS FIELD*/

    var tr_view_details = $('#ywraq_message_to_view_details').closest('tr'),
        tr_view_thank_you_page = $('#ywraq_thank_you_page').closest('tr');

    $(document).on('change', 'input[name="ywraq_how_show_after_sent_the_request"]:checked', function(e){

        var value = $(this).val();

          if( 'simple_message' !== value ){
              tr_view_details.hide();

          }else{

              $('#ywraq_enable_link_details').trigger('change');

          }
          if( 'thank_you_page' !== value ){
              tr_view_thank_you_page.hide();
          }else{

              tr_view_thank_you_page.show();

          }
    });

    $(document).find('input[name="ywraq_how_show_after_sent_the_request"]').trigger('change');

    var radio_checked =  $(document).find('input[name="ywraq_how_show_after_sent_the_request"]:checked').val();


    if( 'simple_message' !== radio_checked ){
        tr_view_details.hide();

    }

});