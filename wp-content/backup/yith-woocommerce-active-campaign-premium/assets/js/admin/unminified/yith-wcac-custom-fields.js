jQuery(document).ready(function ($) {
    var body = $('body'),
        fields_content = $('.fields-content'),
        id = fields_content.parents('.form-table').data('id');

    $('#add_field').on('click', function (ev) {
        var t = $(this),
            item = t.parents('.advanced-panel-item'),
            item_id = item.data('id');

        ev.preventDefault();

        $.ajax({
            beforeSend: function () {
                t.block({
                    message   : null,
                    overlayCSS: {
                        background: '#fff',
                        opacity   : 0.6
                    }
                });
            },
            complete  : function () {
                t.unblock();
            },
            data      : {
                action : yith_wcac_custom_fields.actions.wcac_add_custom_field_action,
                item_id: yith_wcac_custom_fields.item_id,
                list_id: t.parents('#plugin-fw-wc').find('.list-select').val(),
                tab    : yith_wcac.tab,
                id     : id
            },
            dataType  : 'html',
            error     : function () {

            },
            method    : 'POST',
            success   : function (data) {
                if (data.length != 0) {
                    fields_content.prepend(data);
                    $(fields_content.find('.fields-item').get(0)).hide().slideDown();
                    yith_wcac_custom_fields.item_id++;

                    handle_fields_button();
                    body.trigger('wc-enhanced-select-init');
                }
            },
            url       : ajaxurl
        });
    });

    $('#yith_wcac_shortcode_style_enable, #yith_wcac_register_style_enable').on('change', function () {
        var t = $(this),
            round_corners = $('#yith_wcac_shortcode_subscribe_button_round_corners'),
            background_color = $('#yith_wcac_shortcode_subscribe_button_background_color'),
            text_color = $('#yith_wcac_shortcode_subscribe_button_color'),
            border_color = $('#yith_wcac_shortcode_subscribe_button_border_color'),
            background_hover_color = $('#yith_wcac_shortcode_subscribe_button_background_hover_color'),
            text_hover_color = $('#yith_wcac_shortcode_subscribe_button_hover_color'),
            border_hover_color = $('#yith_wcac_shortcode_subscribe_button_border_hover_color'),
            custom_css = $('#yith_wcac_shortcode_custom_css, #yith_wcac_register_custom_css');

        if (t.is(':checked')) {
            round_corners.parents('tr').show();
            background_color.parents('tr').show();
            text_color.parents('tr').show();
            border_color.parents('tr').show();
            background_hover_color.parents('tr').show();
            text_hover_color.parents('tr').show();
            border_hover_color.parents('tr').show();
            custom_css.parents('tr').show();
        }
        else {
            round_corners.parents('tr').hide();
            background_color.parents('tr').hide();
            text_color.parents('tr').hide();
            border_color.parents('tr').hide();
            background_hover_color.parents('tr').hide();
            text_hover_color.parents('tr').hide();
            border_hover_color.parents('tr').hide();
            custom_css.parents('tr').hide();
        }
    }).change();

    $('#yith_wcac_widget_style_enable').on('change', function () {
        var t = $(this),
            round_corners = $('#yith_wcac_widget_subscribe_button_round_corners'),
            background_color = $('#yith_wcac_widget_subscribe_button_background_color'),
            text_color = $('#yith_wcac_widget_subscribe_button_color'),
            border_color = $('#yith_wcac_widget_subscribe_button_border_color'),
            background_hover_color = $('#yith_wcac_widget_subscribe_button_background_hover_color'),
            text_hover_color = $('#yith_wcac_widget_subscribe_button_hover_color'),
            border_hover_color = $('#yith_wcac_widget_subscribe_button_border_hover_color'),
            custom_css = $('#yith_wcac_widget_custom_css');

        if (t.is(':checked')) {
            round_corners.parents('tr').show();
            background_color.parents('tr').show();
            text_color.parents('tr').show();
            border_color.parents('tr').show();
            background_hover_color.parents('tr').show();
            text_hover_color.parents('tr').show();
            border_hover_color.parents('tr').show();
            custom_css.parents('tr').show();
        }
        else {
            round_corners.parents('tr').hide();
            background_color.parents('tr').hide();
            text_color.parents('tr').hide();
            border_color.parents('tr').hide();
            background_hover_color.parents('tr').hide();
            text_hover_color.parents('tr').hide();
            border_hover_color.parents('tr').hide();
            custom_css.parents('tr').hide();
        }
    }).change();

    var handle_fields_button = function () {
        $('.fields-item a.remove-button').off('click').on('click', function (ev) {
            var t = $(this),
                tab = t.parents('.fields-item');

            ev.preventDefault();

            tab.slideUp(300, function () {
                $(this).remove();
            });
        });

        $('.fields-item a.update-fields').on('click', function (ev) {
            var t = $(this),
                select = t.parent().find('select'),
                selected_option = select.val(),
                list_id = t.parents('#plugin-fw-wc').find('.list-select').val();

            ev.preventDefault();

            $.ajax({
                beforeSend: function () {
                    t.block({
                        message   : null,
                        overlayCSS: {
                            background: '#fff',
                            opacity   : 0.6
                        }
                    });


                },
                complete  : function () {
                    t.unblock();
                },
                data      : {
                    force_update                : true,
                    args                        : {},
                    action                      : yith_wcac.actions.wcac_get_fields_via_ajax_action,
                    yith_wcac_ajax_request_nonce: yith_wcac.ajax_request_nonce
                },
                dataType  : 'json',
                method    : 'POST',
                success   : function (fields) {
                    var new_options = '',
                        i = 0;
                    jQuery.each(fields, function (i, item) {

                        print_mail = ('register' == yith_wcac.tab) ? ('email' == i) ? true : false : false;

                        if (print_mail) {
                            return;
                        }
                        new_options += '<option value="' + i + '" ' + ( ( selected_option == i ) ? 'selected="selected"' : '' ) + ' >' + item.title + '</option>';
                    });

                    /*if ('register' != yith_wcac.tab) {

                     if (typeof( selected_option ) != 'undefined') {
                     selected_option = 'email';
                     }
                     new_options += '<option value="email" ' + ( ( selected_option == 'email' ) ? 'selected="selected"' : '' ) + ' >' + yith_wcac.texts.email + '</option>';
                     }

                     new_options += '<option value="first_name" ' + ( ( selected_option == 'first_name' ) ? 'selected="selected"' : '' ) + ' >' + yith_wcac.texts.first_name + '</option>';
                     new_options += '<option value="last_name" ' + ( ( selected_option == 'last_name' ) ? 'selected="selected"' : '' ) + ' >' + yith_wcac.texts.last_name + '</option>';


                     for (var i = 0; i < Object.keys(fields).length; i++) {
                     if (typeof fields[i] === 'object') {
                     new_options += '<option value="' + fields[i].id + '" ' + ( ( selected_option == fields[i].id ) ? 'selected="selected"' : '' ) + ' >' + fields[i].title + '</option>';
                     }
                     }*/

                    select.html(new_options).select2();

                },
                url       : ajaxurl
            });
        });
    };

    handle_fields_button();

    fields_content.sortable();
});