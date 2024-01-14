/*
 * Advance Tab
 */
jQuery(function ($) {

    'use strict';
    var AdvanceTabScript = {
        init: function () {
            this.trigger_on_page_load();
            this.show_or_hide_for_apply_previous_order_range();
            this.show_or_hide_for_menu_restriction_based_on_userrole();
            this.show_or_hide_for_pagination_for_total_earned_points();
            this.show_or_hide_for_pagination_for_total_available_points();
            this.show_or_hide_for_enable_msg_to_participate_in_reward_prgm();
            this.show_or_hide_for_coupon_restriction();
            this.toggle_points_earned_in_specific_duration();
            this.show_or_hide_for_my_account_menu_page();
            jQuery(".sortable_menu").sortable({ items: 'tr', handle: '.myrewards_sortable_menu_data' });
            jQuery(".sortable_menu").disableSelection();
            $(document).on('change', '#rs_reward_content_menu_page', this.my_account_menu_page);
            $(document).on('change', '#rs_sumo_select_order_range', this.apply_previous_order_range);
            $(document).on('change', '#rs_menu_restriction_based_on_user_role', this.menu_restriction_based_on_userrole);
            $(document).on('change', '#rs_select_pagination_for_total_earned_points', this.pagination_for_total_earned_points);
            $(document).on('change', '#rs_select_pagination_for_available_points', this.pagination_for_total_available_points);
            $(document).on('change', '#rs_enable_reward_program', this.enable_msg_to_participate_in_reward_prgm);
            $(document).on('change', '#rs_enable_email_for_reward_program', this.enable_email_in_reward_prgm);
            $(document).on('click', '.rs_sumo_rewards_for_previous_order', this.apply_points_for_previous_order);
            $(document).on('click', '#rs_add_old_points', this.add_old_points_for_user);
            $(document).on('click', '#_rs_enable_coupon_restriction', this.show_or_hide_for_coupon_restriction);
            $(document).on('click', '#rs_points_earned_in_specific_duration_is_enabled', this.toggle_points_earned_in_specific_duration);
            $(document).on('click', '.rs_unsubscribe_user', this.unsubscribe_selected_user);
        },
        trigger_on_page_load: function () {
            $("#rs_points_earned_in_specific_duration_from_date").datepicker({
                dateFormat: 'yy-mm-dd',
            });
            $("#rs_points_earned_in_specific_duration_to_date").datepicker({
                dateFormat: 'yy-mm-dd',
            });
            $("#rs_from_date").datepicker({
                defaultDate: "+1w",
                changeMonth: true,
                dateFormat: 'yy-mm-dd',
                numberOfMonths: 1,
                onClose: function (selectedDate) {
                    $("#to").datepicker("option", "minDate", selectedDate);
                    var maxDate = new Date(Date.parse(selectedDate));
                    maxDate.setDate(maxDate.getDate() + 1);
                    $('#rs_to_date').datepicker('option', 'minDate', maxDate);
                }
            });
            $('#rs_from_date').datepicker('setDate', '-1');
            $("#rs_to_date").datepicker({
                defaultDate: "+1w",
                changeMonth: true,
                dateFormat: 'yy-mm-dd',
                numberOfMonths: 1,
                onClose: function (selectedDate) {
                    $("#from").datepicker("option", "maxDate", selectedDate);
                }

            });
            $("#rs_to_date").datepicker('setDate', new Date());
            if (fp_advance_params.fp_wc_version <= parseFloat('2.2.0')) {
                $('.rewardpoints_userrole_menu_restriction').chosen();
            } else {
                $('.rewardpoints_userrole_menu_restriction').select2();
            }
        },
        my_account_menu_page: function () {
            AdvanceTabScript.show_or_hide_for_my_account_menu_page();
        },
        show_or_hide_for_my_account_menu_page: function () {
            if (jQuery('#rs_reward_content_menu_page').is(':checked') == true) {
                jQuery('#rs_my_reward_table_menu_page').parent().parent().show();
                jQuery('#rs_show_hide_generate_referral_menu_page').parent().parent().show();
                jQuery('#rs_show_hide_referal_table_menu_page').parent().parent().show();
                jQuery('#rs_my_cashback_table_menu_page').parent().parent().show();
                jQuery('#rs_show_hide_nominee_field_menu_page').parent().parent().show();
                jQuery('#rs_show_hide_redeem_voucher_menu_page').parent().parent().show();
                jQuery('#rs_show_hide_your_subscribe_link_menu_page').parent().parent().show();
                jQuery('#rs_my_reward_content_title').parent().parent().show();
                jQuery('#rs_my_reward_url_title').parent().parent().show();
                jQuery('.rs-my-reward-menu-sorting-content').show();
                jQuery('#rs_show_hide_refer_a_friend_menu_page').parent().parent().show();
                jQuery('#rs_my_cashback_form_menu_page').parent().parent().show();
            } else {
                jQuery('#rs_my_reward_table_menu_page').parent().parent().hide();
                jQuery('#rs_show_hide_generate_referral_menu_page').parent().parent().hide();
                jQuery('#rs_show_hide_referal_table_menu_page').parent().parent().hide();
                jQuery('#rs_my_cashback_table_menu_page').parent().parent().hide();
                jQuery('#rs_show_hide_nominee_field_menu_page').parent().parent().hide();
                jQuery('#rs_show_hide_redeem_voucher_menu_page').parent().parent().hide();
                jQuery('#rs_show_hide_your_subscribe_link_menu_page').parent().parent().hide();
                jQuery('#rs_my_reward_content_title').parent().parent().hide();
                jQuery('#rs_my_reward_url_title').parent().parent().hide();
                jQuery('.rs-my-reward-menu-sorting-content').hide();
                jQuery('#rs_show_hide_refer_a_friend_menu_page').parent().parent().hide();
                jQuery('#rs_my_cashback_form_menu_page').parent().parent().hide();
            }
        },
        apply_previous_order_range: function () {
            AdvanceTabScript.show_or_hide_for_apply_previous_order_range();
        },
        show_or_hide_for_apply_previous_order_range: function () {
            if (jQuery('#rs_sumo_select_order_range').val() === '1') {
                jQuery('#rs_from_date').parent().parent().hide();
            } else {
                jQuery('#rs_from_date').parent().parent().show();
            }
        },
        menu_restriction_based_on_userrole: function () {
            AdvanceTabScript.show_or_hide_for_menu_restriction_based_on_userrole();
        },
        show_or_hide_for_menu_restriction_based_on_userrole: function () {
            if (jQuery('#rs_menu_restriction_based_on_user_role').is(':checked') == true) {
                jQuery('.rewardpoints_userrole_menu_restriction').parent().parent().show();
            } else {
                jQuery('.rewardpoints_userrole_menu_restriction').parent().parent().hide();
            }
        },
        pagination_for_total_earned_points: function () {
            AdvanceTabScript.show_or_hide_for_pagination_for_total_earned_points();
        },
        show_or_hide_for_pagination_for_total_earned_points: function () {
            if (jQuery('#rs_select_pagination_for_total_earned_points').val() == '1') {
                jQuery('#rs_value_without_pagination_for_total_earned_points').closest('tr').hide();
            } else {
                jQuery('#rs_value_without_pagination_for_total_earned_points').closest('tr').show();
            }
        },
        pagination_for_total_available_points: function () {
            AdvanceTabScript.show_or_hide_for_pagination_for_total_available_points();
        },
        show_or_hide_for_pagination_for_total_available_points: function () {
            if (jQuery('#rs_select_pagination_for_available_points').val() == '1') {
                jQuery('#rs_value_without_pagination_for_available_points').closest('tr').hide();
            } else {
                jQuery('#rs_value_without_pagination_for_available_points').closest('tr').show();
            }
        },
        enable_msg_to_participate_in_reward_prgm: function () {
            AdvanceTabScript.show_or_hide_for_enable_msg_to_participate_in_reward_prgm();
        },
        show_or_hide_for_enable_msg_to_participate_in_reward_prgm: function () {
            if (jQuery('#rs_enable_reward_program').is(':checked') == true) {
                jQuery('#rs_msg_in_reg_page').closest('tr').show();
                jQuery('#rs_msg_in_acc_page_when_checked').closest('tr').show();
                jQuery('#rs_msg_in_acc_page_when_unchecked').closest('tr').show();
                jQuery('#rs_alert_msg_in_acc_page_when_checked').closest('tr').show();
                jQuery('#rs_alert_msg_in_acc_page_when_unchecked').closest('tr').show();
                jQuery('#rs_enable_email_for_reward_program').closest('tr').show();
                AdvanceTabScript.toggle_email_notification_for_reward_program('#rs_enable_email_for_reward_program');
            } else {
                jQuery('#rs_msg_in_reg_page').closest('tr').hide();
                jQuery('#rs_msg_in_acc_page_when_checked').closest('tr').hide();
                jQuery('#rs_msg_in_acc_page_when_unchecked').closest('tr').hide();
                jQuery('#rs_alert_msg_in_acc_page_when_checked').closest('tr').hide();
                jQuery('#rs_alert_msg_in_acc_page_when_unchecked').closest('tr').hide();
                jQuery('#rs_enable_email_for_reward_program').closest('tr').hide();
                $('#rs_subject_for_reward_program_email').closest('tr').hide();
                $('#rs_message_for_reward_program_email').closest('tr').hide();
            }
        },
        enable_email_in_reward_prgm: function () {
            AdvanceTabScript.toggle_email_notification_for_reward_program('#rs_enable_email_for_reward_program');
        },
        show_or_hide_for_coupon_restriction: function () {
            if (jQuery('#_rs_enable_coupon_restriction').is(':checked') == true) {
                jQuery('#_rs_restrict_coupon').closest('tr').show();
                if (jQuery('#_rs_restrict_coupon').val() == '2') {
                    jQuery('#rs_delete_coupon_by_cron_time').closest('tr').show();
                    jQuery('#rs_delete_coupon_specific_time').closest('tr').show();
                } else {
                    jQuery('#rs_delete_coupon_by_cron_time').closest('tr').hide();
                    jQuery('#rs_delete_coupon_specific_time').closest('tr').hide();
                }
                jQuery('#_rs_restrict_coupon').change(function () {
                    if (jQuery('#_rs_restrict_coupon').val() == '2') {
                        jQuery('#rs_delete_coupon_by_cron_time').closest('tr').show();
                        jQuery('#rs_delete_coupon_specific_time').closest('tr').show();
                    } else {
                        jQuery('#rs_delete_coupon_by_cron_time').closest('tr').hide();
                        jQuery('#rs_delete_coupon_specific_time').closest('tr').hide();
                    }
                });
            } else {
                jQuery('#_rs_restrict_coupon').closest('tr').hide();
                jQuery('#rs_delete_coupon_by_cron_time').closest('tr').hide();
                jQuery('#rs_delete_coupon_specific_time').closest('tr').hide();
            }
        },
        toggle_email_notification_for_reward_program: function ($this) {
            if ($($this).is(':checked')) {
                $('#rs_subject_for_reward_program_email').closest('tr').show();
                $('#rs_message_for_reward_program_email').closest('tr').show();
            } else {
                $('#rs_subject_for_reward_program_email').closest('tr').hide();
                $('#rs_message_for_reward_program_email').closest('tr').hide();
            }
        },
        toggle_points_earned_in_specific_duration: function () {
            if ($('#rs_points_earned_in_specific_duration_is_enabled').is(':checked')) {
                $('#rs_points_earned_in_specific_duration_from_date').closest('tr').show();
                $('#rs_points_earned_in_specific_duration_to_date').closest('tr').show();
                $('#rs_points_earned_in_specific_duration_pagination').closest('tr').show();
            } else {
                $('#rs_points_earned_in_specific_duration_from_date').closest('tr').hide();
                $('#rs_points_earned_in_specific_duration_to_date').closest('tr').hide();
                $('#rs_points_earned_in_specific_duration_pagination').closest('tr').hide();
            }
        },
        apply_points_for_previous_order: function () {

            var fromdate = jQuery('#rs_from_date').val();
            var todate = jQuery('#rs_to_date').val();
            var previous_order_points_for = jQuery('#rs_award_previous_order_points').val();
            var award_points_on = jQuery('#rs_sumo_select_order_range').val();

            if ('2' == award_points_on && !fromdate || !todate) {
                alert(fp_advance_params.from_to_date_range_error);
                return false;
            }

            var dataparam = ({
                action: 'apply_points_previous_orders',
                fromdate: fromdate,
                todate: todate,
                awardpointson: award_points_on,
                previousorderpointsfor: previous_order_points_for,
                sumo_security: fp_advance_params.fp_apply_points
            });

            AdvanceTabScript.block($('.rs_sumo_rewards_for_previous_order').closest('table'));
            $.post(fp_advance_params.ajaxurl, dataparam, function (response) {
                if (true === response.success) {
                    AdvanceTabScript.unblock($('.rs_sumo_rewards_for_previous_order').closest('table'));
                    console.log('Ajax Done Successfully');
                    window.location.href = response.data.redirect_url;
                } else {
                    AdvanceTabScript.unblock($('.rs_sumo_rewards_for_previous_order').closest('table'));
                    window.alert(response.data.error);
                }
            });

            return false;
        },
        add_old_points_for_user: function () {
            var dataparam = ({
                action: 'add_old_points',
                sumo_security: fp_advance_params.fp_old_points
            });
            $.post(fp_advance_params.ajaxurl, dataparam, function (response) {
                if (true === response.success) {
                    console.log('Ajax Done Successfully');
                    window.location.href = response.data.redirect_url;
                } else {
                    window.alert(response.data.error);
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

        unsubscribe_selected_user: function () {
            jQuery('.gif_rs_sumo_reward_button_for_unsubscribe').css('display', 'inline-block');
            var unsubscribe = jQuery('#rs_select_user_to_unsubscribe').val();
            var emailsubject = jQuery('#rs_subject_for_user_unsubscribe').val();
            var emailmessage = jQuery('#rs_message_for_user_unsubscribe').val();
            var data = ({
                action: 'unsubscribeuser',
                unsubscribe: unsubscribe,
                emailsubject: emailsubject,
                emailmessage: emailmessage,
                sumo_security: fp_advance_params.fp_unsubscribe_email
            });

            $.post(fp_advance_params.ajaxurl, data, function (response) {
                if (true === response.success) {
                    console.log('Ajax Done Successfully');
                    jQuery('.button-primary').trigger('click');
                    jQuery('.gif_rs_sumo_reward_button_for_unsubscribe').css('display', 'none');
                } else {
                    window.alert(response.data.error);
                }
            });
        },
    };
    AdvanceTabScript.init();
});