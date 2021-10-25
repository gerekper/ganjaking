(function (api, $) {
    "use strict";

    var mc = {};

    mc.conditional_display_onload = function () {

        $('.mo-integration-widget').each(function () {
            var parent = $(this);

            var connection_service = $("select[name='connection_service']", parent).val();

            if (connection_service !== 'MailChimpConnect') return;

            var segment_type = $("select[name='MailChimpConnect_group_segment_type']", parent).val();

            if ($('.mo_mc_interest', parent).length === 0) {
                $('.mc-group-block', parent).hide();
            } else {
                mc.conditional_user_input_fields(segment_type, parent);
                mc.segment_required_error_toggle_callback(segment_type, parent);
            }
        });
    };

    mc.segment_required_error_toggle_callback = function (segment_type, parent) {
        if (segment_type === 'user_input' && $('input[name="MailChimpConnect_segment_required"]', parent).prop('checked') === true) {
            $('div.MailChimpConnect_segment_required_error', parent).slideDown();
        }
        else {
            $('div.MailChimpConnect_segment_required_error', parent).slideUp();
        }
    };

    mc.conditional_user_input_fields = function (segment_type, parent) {
        if (segment_type === 'user_input') {
            $('div.MailChimpConnect_selection_type', parent).slideDown();
            $('div.MailChimpConnect_user_input_field_color', parent).slideDown();
            $('div.MailChimpConnect_user_input_segment_area_font', parent).slideDown();
            $('div.MailChimpConnect_segment_display_style', parent).slideDown();
            $('div.MailChimpConnect_segment_display_alignment', parent).slideDown();
            $('div.MailChimpConnect_segment_required', parent).slideDown();
            $('div.MailChimpConnect_segment_required_error', parent).slideDown();
            $('div.MailChimpConnect_show_group_label', parent).slideDown();
            $('div.MailChimpConnect_user_input_field_label', parent).slideDown();
        }
        else {
            $('div.MailChimpConnect_selection_type', parent).slideUp();
            $('div.MailChimpConnect_user_input_field_color', parent).slideUp();
            $('div.MailChimpConnect_user_input_segment_area_font', parent).slideUp();
            $('div.MailChimpConnect_segment_display_style', parent).slideUp();
            $('div.MailChimpConnect_segment_display_alignment', parent).slideUp();
            $('div.MailChimpConnect_segment_required', parent).slideUp();
            $('div.MailChimpConnect_segment_required_error', parent).slideUp();
            $('div.MailChimpConnect_show_group_label', parent).slideUp();
            $('div.MailChimpConnect_user_input_field_label', parent).slideUp();
        }
    };

    mc.refresh_preview = function () {

        $(document).on('focus', "select[name='connection_service']", function () {
            // Store the current value on focus, before it changes
            $(this).data('mo_prev_val', this.value);
        }).on('change', function () {

            if ($(this).data('mo_prev_val') === 'MailChimpConnect') {
                api.previewer.refresh();
            }

            $(this).data('mo_prev_val', this.value);
        });

        $(document).on(
            'change',
            '[name="MailChimpConnect_group_segment_type"],[name="MailChimpConnect_user_input_field_label"],[name="MailChimpConnect_selection_type"],[name="MailChimpConnect_user_input_segment_area_font"],[name="MailChimpConnect_segment_display_alignment"],[name="MailChimpConnect_interests[]"],[name="MailChimpConnect_segment_display_style"],[name="MailChimpConnect_user_input_field_color"],[name="MailChimpConnect_show_group_label"]',
            function () {
                api.previewer.refresh();
            }
        )
    };

    mc.toggle_mc_fields_visibility = function (e, connect_service, parent) {

        if ($("select[name='connection_email_list']", parent).val() === '') {
            $('.mc-group-block', parent).hide();
        }
    };

    mc.connection_email_list_handler = function () {

        function add_spinner(placement) {
            var spinner_html = $('<img class="mo-spinner fetch-email-list" src="' + mailoptin_globals.admin_url + '/images/spinner.gif">');
            $(placement).after(spinner_html);
        }

        function remove_spinner(parent) {
            $('.mo-spinner.fetch-email-list', parent).remove();
        }

        var parent = $(this).parents('.mo-integration-widget');

        // hide all mailchimp fields.
        $('div[class*="MailChimpConnect"]', parent).hide();

        var connection_service = $("select[name='connection_service']", parent).val();

        if (connection_service !== 'MailChimpConnect') return;

        var list_id = $(this).val();

        add_spinner(this);

        $.post(
            ajaxurl, {
                action: 'mailoptin_customizer_fetch_mailchimp_groups',
                list_id: list_id,
                security: $("input[data-customize-setting-link*='[ajax_nonce]']").val()
            },
            function (response) {
                if (_.isObject(response) && 'success' in response && 'data' in response) {
                    $('div.MailChimpConnect_interests', parent).replaceWith(response.data.structure);
                }

                // refresh the customizer
                api.previewer.refresh();

                remove_spinner();
                if ($("select[name='connection_email_list']", parent)) {
                    $('div[class*="MailChimpConnect"]', parent).show();
                }

                var segment_type_select_obj = $('select[name="MailChimpConnect_group_segment_type"]', parent);

                if (_.isEmpty(response.data.interests)) {
                    $('.mc-group-block', parent).hide();
                }
                else {
                    mc.conditional_user_input_fields(segment_type_select_obj.val(), parent);
                    mc.segment_required_error_toggle_callback(segment_type_select_obj.val(), parent);
                }

                $(document.body).trigger('mo_mailchimp_groups_ajax_complete', [parent]);
            }
        );
    };

    mc.init = function () {

        $(document).on('change', 'select[name="MailChimpConnect_group_segment_type"]', function () {
            var parent = $(this).parents('.mo-integration-widget');
            mc.conditional_user_input_fields(this.value, parent)
        });

        $(document).on('change', '[name="MailChimpConnect_segment_required"]', function () {
            var parent = $(this).parents('.mo-integration-widget');
            var segment_type = $("select[name='MailChimpConnect_group_segment_type']", parent).val();
            mc.segment_required_error_toggle_callback(segment_type, parent)
        });

        $(document).on('change', "select[name='connection_email_list']", mc.connection_email_list_handler);
        $(document).on('mo_new_email_list_data_found mo_email_list_data_not_found', mc.toggle_mc_fields_visibility);

        mc.refresh_preview();
        mc.conditional_display_onload();
    };

    $(window).on('load', mc.init);

})(wp.customize, jQuery);