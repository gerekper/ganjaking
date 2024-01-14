/*
 * Anniversary Points Module.
 */
jQuery(function ($) {
    var Anniversary_Points_Module = {
        init: function () {
            this.trigger_on_page_load();

            // Toggle account anniversary email type.
            $(document).on('change', '#rs_enable_account_anniversary_mail', this.toggle_account_anniversary_mail);
            $('#rs_enable_account_anniversary_mail').change();
            // Toggle custom anniversary email type.
            $(document).on('change', '#rs_enable_custom_anniversary_mail', this.toggle_custom_anniversary_mail);
            $('#rs_enable_custom_anniversary_mail').change();

            $(document).on('click', '.rs-add-account-anniversary-rule', this.add_account_anniversary_rule);
            $(document).on('click', '.rs-remove-account-anniversary-rule', this.remove_account_anniversary_rule);

            $(document).on('click', '.rs-add-custom-anniversary-rule', this.add_custom_anniversary_rule);
            $(document).on('click', '.rs-remove-custom-anniversary-rule', this.remove_custom_anniversary_rule);

            $(document).on('change', '#rs_account_anniversary_point_type', this.toggle_account_anniversary_point_type);
            $('#rs_account_anniversary_point_type').change();

            $(document).on('change', '.rs_enable_account_anniversary_point', this.toggle_account_anniversary_checkbox);
            $('.rs_enable_account_anniversary_point').change();

            $(document).on('change', '#rs_custom_anniversary_point_type', this.toggle_custom_anniversary_point_type);
            $('#rs_custom_anniversary_point_type').change();

            $(document).on('change', '.rs_enable_custom_anniversary_point', this.toggle_custom_anniversary_checkbox);
            $('.rs_enable_custom_anniversary_point').change();
            
            $(document).on('click', '.rs-account-anniv-details-popup', this.view_account_anniversary_points_popup);
            $(document).on('click', '.rs-single-anniv-details-popup', this.view_single_anniversary_points_popup);
            $(document).on('click', '.rs-multiple-anniv-details-popup', this.view_multiple_anniversary_points_popup);
        },
        trigger_on_page_load: function () {

        },
        toggle_account_anniversary_mail: function (event) {
            event.preventDefault();
            var $this = $(event.currentTarget);
            if ($this.is(':checked')) {
                $('#rs_email_subject_account_anniversary').closest('tr').show();
                $('#rs_email_message_account_anniversary').closest('tr').show();
            } else {
                $('#rs_email_subject_account_anniversary').closest('tr').hide();
                $('#rs_email_message_account_anniversary').closest('tr').hide();
            }
        },
        toggle_custom_anniversary_mail: function (event) {
            event.preventDefault();
            var $this = $(event.currentTarget);
            if ($this.is(':checked')) {
                $('#rs_email_subject_custom_anniversary').closest('tr').show();
                $('#rs_email_message_custom_anniversary').closest('tr').show();
            } else {
                $('#rs_email_subject_custom_anniversary').closest('tr').hide();
                $('#rs_email_message_custom_anniversary').closest('tr').hide();
            }
        },
        add_account_anniversary_rule: function (event) {
            event.preventDefault( );
            var $this = $(event.currentTarget);
            var data = {
                action: 'add_account_anniversary_rule',
                sumo_security: fp_anniversary_points_module_params.add_account_anniversary_rule_nonce
            };
            Anniversary_Points_Module.block($($this));
            $.post(fp_anniversary_points_module_params.ajaxurl, data, function (response) {
                if (true == response.success && response.data.html) {
                    $($this).closest('.rs-account-anniversary-rule-based-type').find('tbody').append(response.data.html);
                    Anniversary_Points_Module.unblock($($this));
                } else {
                    alert(response.data.error);
                    Anniversary_Points_Module.unblock($($this));
                }
            });
            return false;
        },
        remove_account_anniversary_rule: function (event) {
            event.preventDefault( );
            var $this = $(event.currentTarget);
            $($this).closest("tr").remove();
        },
        add_custom_anniversary_rule: function (event) {
            event.preventDefault( );
            var $this = $(event.currentTarget);
            var data = {
                action: 'add_custom_anniversary_rule',
                sumo_security: fp_anniversary_points_module_params.add_custom_anniversary_rule_nonce
            };

            Anniversary_Points_Module.block($($this));
            $.post(fp_anniversary_points_module_params.ajaxurl, data, function (response) {
                if (true == response.success && response.data.html) {
                    $($this).closest('.rs-custom-anniversary-rule-based-type').find('tbody').append(response.data.html);
                    Anniversary_Points_Module.unblock($($this));
                } else {
                    alert(response.data.error);
                    Anniversary_Points_Module.unblock($($this));
                }
            });
            return false;
        },
        remove_custom_anniversary_rule: function (event) {
            event.preventDefault( );
            var $this = $(event.currentTarget);
            $($this).closest("tr").remove();
        },
        toggle_account_anniversary_point_type: function (event) {
            event.preventDefault( );
            var $this = $(event.currentTarget);
            $('.rs_account_anniversary_point_value').closest('tr').hide();
            $('.rs-account-anniversary-rule-based-type-row').hide();
            if ('one_time' == $this.val()) {
                $('.rs_account_anniversary_point_value').closest('tr').show();
            } else if ('every_year' == $this.val()) {
                $('.rs_account_anniversary_point_value').closest('tr').show();
            } else if ('rule_based' == $this.val()) {
                $('.rs-account-anniversary-rule-based-type-row').show();
            }
        },
        toggle_account_anniversary_checkbox: function (event) {
            event.preventDefault( );
            var $this = $(event.currentTarget);
            $('#rs_account_anniversary_point_type').closest('tr').hide();
            $('#rs_account_anniversary_point_value').closest('tr').hide();
            $('.rs-account-anniversary-rule-based-type-row').hide();
            $('#rs_enable_account_anniversary_mail').closest('tr').hide();
            $('#rs_email_subject_account_anniversary').closest('tr').hide();
            $('#rs_email_message_account_anniversary').closest('tr').hide();
            $('#rs_account_anniversary_field_reward_log').closest('tr').hide();

            if ($this.is(':checked')) {
                $('#rs_account_anniversary_point_type').closest('tr').show();
                $('#rs_account_anniversary_point_value').closest('tr').show();
                $('#rs_enable_account_anniversary_mail').closest('tr').show();
                $('#rs_enable_account_anniversary_mail').change();
                $('#rs_account_anniversary_point_type').change();
                $('#rs_account_anniversary_field_reward_log').closest('tr').show();
            }
        },
        toggle_custom_anniversary_point_type: function (event) {
            event.preventDefault( );
            var $this = $(event.currentTarget);
            $('#rs_custom_anniversary_point_value').closest('tr').hide();
            $('#rs_enable_repeat_custom_anniversary_point').closest('tr').hide();
            $('#rs_enable_mandatory_custom_anniversary_point').closest('tr').hide();
            $('#rs_custom_anniversary_field_name').closest('tr').hide();
            $('#rs_custom_anniversary_field_desc').closest('tr').hide();
            $('.rs-custom-anniversary-rule-based-type-row').hide();
            if ('single_anniversary' == $this.val()) {
                $('#rs_custom_anniversary_point_value').closest('tr').show();
                $('#rs_enable_repeat_custom_anniversary_point').closest('tr').show();
                $('#rs_enable_mandatory_custom_anniversary_point').closest('tr').show();
                $('#rs_custom_anniversary_field_name').closest('tr').show();
                $('#rs_custom_anniversary_field_desc').closest('tr').show();
            } else if ('multiple_anniversary' == $this.val()) {
                $('.rs-custom-anniversary-rule-based-type-row').show();
            }
        },
        toggle_custom_anniversary_checkbox: function (event) {
            event.preventDefault( );
            var $this = $(event.currentTarget);

            if ($this.is(':checked')) {
                $('#rs_enable_custom_anniversary_mail').closest('tr').show();
                $('#rs_enable_custom_anniversary_mail').change();
                $('#rs_custom_anniversary_point_type').closest('tr').show();
                $('#rs_custom_anniversary_point_type').change();
                $('#rs_custom_anniversary_field_reward_log').closest('tr').show();
            } else {
                $('#rs_custom_anniversary_point_value').closest('tr').hide();
                $('#rs_custom_anniversary_point_type').closest('tr').hide();
                $('#rs_enable_repeat_custom_anniversary_point').closest('tr').hide();
                $('#rs_enable_mandatory_custom_anniversary_point').closest('tr').hide();
                $('#rs_custom_anniversary_field_name').closest('tr').hide();
                $('#rs_custom_anniversary_field_desc').closest('tr').hide();
                $('.rs-custom-anniversary-rule-based-type-row').hide();
                $('#rs_enable_custom_anniversary_mail').closest('tr').hide();
                $('#rs_email_subject_custom_anniversary').closest('tr').hide();
                $('#rs_email_message_custom_anniversary').closest('tr').hide();
                $('#rs_custom_anniversary_field_reward_log').closest('tr').hide();
            }
        },
        view_account_anniversary_points_popup: function (event) {
            event.preventDefault();
            var $this = $(event.currentTarget);

            var data = ({
                action: 'view_account_anniversary_points_popup',
                user_id: $this.data('user_id'),
                sumo_security: fp_anniversary_points_module_params.view_account_anniversary_points_popup_nonce
            });

            Anniversary_Points_Module.block($($this));
            $.post(ajaxurl, data, function (res) {

                if (true === res.success) {
                    // Backbone Modal for display popup.
                    $(this).WCBackboneModal({
                        template: 'rs-account-anniversary-points-backbone-modal',
                        variable: res.data
                    });

                    Anniversary_Points_Module.unblock($($this));
                } else {
                    alert(res.data.error);
                    Anniversary_Points_Module.unblock($($this));
                }
            });
            return false;
        },
        view_single_anniversary_points_popup: function (event) {

            event.preventDefault();
            var $this = $(event.currentTarget);

            var data = ({
                action: 'view_single_anniversary_points_popup',
                user_id: $this.data('user_id'),
                sumo_security: fp_anniversary_points_module_params.view_single_anniversary_points_popup_nonce
            });

            Anniversary_Points_Module.block($($this));
            $.post(ajaxurl, data, function (res) {

                if (true === res.success) {
                    // Backbone Modal for display popup.
                    $(this).WCBackboneModal({
                        template: 'rs-single-anniversary-points-backbone-modal',
                        variable: res.data
                    });

                    Anniversary_Points_Module.unblock($($this));
                } else {
                    alert(res.data.error);
                    Anniversary_Points_Module.unblock($($this));
                }
            });
            return false;
        },
        view_multiple_anniversary_points_popup: function (event) {
            event.preventDefault();
            var $this = $(event.currentTarget);

            var data = ({
                action: 'view_multiple_anniversary_points_popup',
                user_id: $this.data('user_id'),
                sumo_security: fp_anniversary_points_module_params.view_multiple_anniversary_points_popup_nonce
            });

            Anniversary_Points_Module.block($($this));
            $.post(ajaxurl, data, function (res) {

                if (true === res.success) {
                    // Backbone Modal for display popup.
                    $(this).WCBackboneModal({
                        template: 'rs-multiple-anniversary-points-backbone-modal',
                        variable: res.data
                    });

                    Anniversary_Points_Module.unblock($($this));
                } else {
                    alert(res.data.error);
                    Anniversary_Points_Module.unblock($($this));
                }
            });
            return false;
        },
        block: function (id) {
            $(id).block({
                message: null,
                overlayCSS: {
                    background: '#fff',
                    opacity: 0.6
                }
            });
        },
        unblock: function (id) {
            $(id).unblock();
        },
    };
    Anniversary_Points_Module.init();
});