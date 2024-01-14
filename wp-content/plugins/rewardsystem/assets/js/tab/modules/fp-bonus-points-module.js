/*
 * Bonus Points Module.
 */
jQuery(function ($) {
    var Bonus_Points_Module = {
        init: function () {
            this.trigger_on_page_load();

            $(document).on('click', '.rs-add-bonus-points-rule-for-orders', this.add_rule_for_bonus_points_without_repeat_for_orders);
            $(document).on('click', '.rs-remove-bonus-points-rule-for-orders', this.remove_rule_for_bonus_points_without_repeat);

            $(document).on('change', '.rs_enable_bonus_point_for_orders', this.toggle_enable_bonus_point_for_orders);
            $(document).on('change', '.rs_bonus_points_rules_for_orders_type', this.toggle_bonus_points_rules_type);
            $(document).on('change', '#rs_number_of_orders_bonus_email_enabled', this.toggle_number_of_orders_bonus_email_checkbox);

            $(document).on('change', '#rs_enable_bonus_point_for_orders', this.checkbox_alert_message);

            // View bonus point placed orderids action.
            $(document).on('click', '.rs-bonus-point-placed-order-ids-view', this.view_bonus_point_placed_order_ids_popup)

            // Paginations - Page selector.
            $(document).on('change', '.rs-bonus-placed-orders-content-pagenav .rs-page-selector', this.page_selector);
            // Paginations - First Page.
            $(document).on('click', '.rs-bonus-placed-orders-content-pagenav .rs-first-page', this.first_page);
            // Paginations - Previous Page.
            $(document).on('click', '.rs-bonus-placed-orders-content-pagenav .rs-prev-page', this.prev_page);
            // Paginations - Next Page.
            $(document).on('click', '.rs-bonus-placed-orders-content-pagenav .rs-next-page', this.next_page);
            // Paginations - Last Page.
            $(document).on('click', '.rs-bonus-placed-orders-content-pagenav .rs-last-page', this.last_page);
        },
        trigger_on_page_load: function () {
            // Trigger date duration.
            Bonus_Points_Module.trigger_date_duration();

            Bonus_Points_Module.bonus_points_rules_type($('.rs_bonus_points_rules_for_orders_type'));
            Bonus_Points_Module.number_of_orders_bonus_email_checkbox($('#rs_number_of_orders_bonus_email_enabled'));
            Bonus_Points_Module.enable_bonus_point_for_orders($('.rs_enable_bonus_point_for_orders'));
        },
        toggle_enable_bonus_point_for_orders: function (event) {
            event.preventDefault();
            var $this = $(event.currentTarget);
            Bonus_Points_Module.enable_bonus_point_for_orders($this);
        },
        toggle_bonus_points_rules_type: function (event) {
            event.preventDefault();
            var $this = $(event.currentTarget);
            Bonus_Points_Module.bonus_points_rules_type($this);
        },
        enable_bonus_point_for_orders: function ($this) {
            if (true == $this.is(':checked')) {
                $('.rs_bonus_points_rules_for_orders_type').closest('tr').show();
                Bonus_Points_Module.bonus_points_rules_type($('#rs_bonus_points_rules_for_orders_type'));
                $('#rs_number_of_orders_bonus_email_enabled').closest('tr').show();
                Bonus_Points_Module.number_of_orders_bonus_email_checkbox($('#rs_number_of_orders_bonus_email_enabled'));
            } else {
                $('.rs_bonus_points_rules_for_orders_type').closest('tr').hide();
                $('.rs_bonus_points_number_of_orders_with_repeat').closest('tr').hide();
                $('.rs_bonus_points_value_number_of_orders_with_repeat').closest('tr').hide();
                $('.rs_bonus_points_from_date_number_of_orders_with_repeat').closest('tr').hide();
                $('.rs_bonus_points_to_date_number_of_orders_with_repeat').closest('tr').hide();
                $('.rs-bonus-points-rules-data-for-orders').closest('tr').hide();
                $('#rs_number_of_orders_bonus_email_enabled').closest('tr').hide();
                $('#rs_email_subject_number_of_orders_bonus_point').closest('tr').hide();
                $('#rs_email_message_number_of_orders_bonus_point').closest('tr').hide();
            }
        },
        bonus_points_rules_type: function ($this) {

            if ('1' == $this.val()) {
                $('.rs_bonus_points_number_of_orders_with_repeat').closest('tr').show();
                $('.rs_bonus_points_value_number_of_orders_with_repeat').closest('tr').show();
                $('.rs_bonus_points_from_date_number_of_orders_with_repeat').closest('tr').show();
                $('.rs_bonus_points_to_date_number_of_orders_with_repeat').closest('tr').show();
                $('.rs-bonus-points-rules-data-for-orders').closest('tr').hide();
            } else {
                $('.rs_bonus_points_number_of_orders_with_repeat').closest('tr').hide();
                $('.rs_bonus_points_value_number_of_orders_with_repeat').closest('tr').hide();
                $('.rs_bonus_points_from_date_number_of_orders_with_repeat').closest('tr').hide();
                $('.rs_bonus_points_to_date_number_of_orders_with_repeat').closest('tr').hide();
                $('.rs-bonus-points-rules-data-for-orders').closest('tr').show();
            }
        },
        toggle_number_of_orders_bonus_email_checkbox: function (event) {
            event.preventDefault();
            var $this = $(event.currentTarget);
            Bonus_Points_Module.number_of_orders_bonus_email_checkbox($this);
        },
        number_of_orders_bonus_email_checkbox: function ($this) {
            if ($this.is(':checked')) {
                $('#rs_email_subject_number_of_orders_bonus_point').closest('tr').show();
                $('#rs_email_message_number_of_orders_bonus_point').closest('tr').show();
            } else {
                $('#rs_email_subject_number_of_orders_bonus_point').closest('tr').hide();
                $('#rs_email_message_number_of_orders_bonus_point').closest('tr').hide();
            }
        },
        checkbox_alert_message: function (event) {
            event.preventDefault( );
            var $this = $(event.currentTarget);
            if ($this.is(':checked')) {
                alert(fp_bonus_points_module_params.checkbox_alert);
                return false;
            }
        },
        add_rule_for_bonus_points_without_repeat_for_orders: function (event) {
            event.preventDefault( );
            var $this = $(event.currentTarget);
            var data = {
                action: 'add_rule_for_bonus_points_without_repeat_for_orders',
                sumo_security: fp_bonus_points_module_params.bonus_points_rule_for_orders_nonce
            };
            $.post(fp_bonus_points_module_params.ajaxurl, data, function (response) {
                if (true == response.success && response.data.html) {
                    $($this).closest('.rs-bonus-points-without-repeat-rules-for-orders').find('tbody').append(response.data.html);
                    $('.rs-bonus-points-number-of-orders-from-date-without-repeat').datepicker({dateFormat: 'dd-mm-yy'});
                    $('.rs-bonus-points-number-of-orders-to-date-without-repeat').datepicker({dateFormat: 'dd-mm-yy'});
                } else {
                    alert(response.data.error);
                }
            });
        },
        remove_rule_for_bonus_points_without_repeat: function (event) {
            event.preventDefault( );
            var $this = $(event.currentTarget);
            $($this).closest("tr").remove();
        },
        trigger_date_duration: function () {
            $('.rs-bonus-points-number-of-orders-from-date-without-repeat').datepicker({
                changeMonth: true,
                dateFormat: 'yy-mm-dd',
                numberOfMonths: 1,
                showButtonPanel: true,
                defaultDate: '',
                showOn: 'focus',
                buttonImageOnly: true,
                onClose: function (selectedDate) {
                    var maxDate = new Date(Date.parse(selectedDate));
                    maxDate.setDate(maxDate.getDate() + 1);
                    $('.rs-bonus-points-number-of-orders-to-date-without-repeat').datepicker('option', 'minDate', maxDate);
                }}
            );

            $('.rs-bonus-points-number-of-orders-to-date-without-repeat').datepicker({dateFormat: 'dd-mm-yy'});
            $('.rs_bonus_points_from_date_number_of_orders_with_repeat').datepicker({
                changeMonth: true,
                dateFormat: 'yy-mm-dd',
                numberOfMonths: 1,
                showButtonPanel: true,
                defaultDate: '',
                showOn: 'focus',
                buttonImageOnly: true,
                onClose: function (selectedDate) {
                    var maxDate = new Date(Date.parse(selectedDate));
                    maxDate.setDate(maxDate.getDate() + 1);
                    $('.rs_bonus_points_to_date_number_of_orders_with_repeat').datepicker('option', 'minDate', maxDate);
                }}
            );

            $('.rs_bonus_points_to_date_number_of_orders_with_repeat').datepicker({dateFormat: 'dd-mm-yy'});
        },
        view_bonus_point_placed_order_ids_popup: function (event, selected_page) {

            if (event) {
                event.preventDefault();
                var $this = $(event.currentTarget),
                        $selected_wrapper = $this;
            } else {
                var $this = $('.rs-bonus-point-placed-order-ids-view'),
                        $selected_wrapper = $('.rs-bonus-point-placed-orders-table-popup');
            }

            Bonus_Points_Module.block($($selected_wrapper));
            var data = ({
                action: 'view_bonus_point_placed_order_ids_popup',
                user_id: $this.data('user_id'),
                stored_order_id: $this.data('order_id'),
                selected_page: selected_page,
                sumo_security: fp_bonus_points_module_params.view_bonus_point_placed_order_ids_popup_nonce
            });

            $.post(ajaxurl, data, function (res) {

                if (true === res.success) {
                    $('#wc-backbone-modal-dialog').remove();
                    // Backbone Modal for display popup.
                    $(this).WCBackboneModal({
                        template: 'rs-bonus-placed-order-ids-backbone-modal',
                        variable: res.data
                    });

                    selected_page = selected_page ? selected_page : 1;
                    $('.rs-bonus-placed-orders-content-pagenav .rs-page-selector').val(selected_page);
                    Bonus_Points_Module.change_classes(selected_page, parseInt($('.rs-bonus-placed-orders-content-pagenav').find('.rs-total-orders').val(), 10));

                    Bonus_Points_Module.unblock($($selected_wrapper));
                } else {
                    alert(res.data.error);
                    Bonus_Points_Module.unblock($($selected_wrapper));
                }
            });
            return false;
        },
        page_selector: function (event) {
            event.preventDefault();
            var $this = $(event.currentTarget);
            Bonus_Points_Module.view_bonus_point_placed_order_ids_popup(false, $this.val());
        },
        first_page: function (event) {
            event.preventDefault();
            var $this = $(event.currentTarget);
            if (Bonus_Points_Module.check_is_enabled($this)) {
                Bonus_Points_Module.view_bonus_point_placed_order_ids_popup(false, 1);
            }

            return false;
        },
        prev_page: function (event) {
            event.preventDefault();
            var $this = $(event.currentTarget);
            if (Bonus_Points_Module.check_is_enabled($this)) {
                var $current_page = $('.rs-bonus-placed-orders-content-pagenav .rs-page-selector').val(),
                        prev_page = parseInt($current_page) - 1,
                        new_page = (0 < prev_page) ? prev_page : 1;

                Bonus_Points_Module.view_bonus_point_placed_order_ids_popup(false, new_page);
            }

            return false;
        },
        next_page: function (event) {
            event.preventDefault();
            var $this = $(event.currentTarget);
            if (Bonus_Points_Module.check_is_enabled($this)) {

                var $current_page = $('.rs-bonus-placed-orders-content-pagenav .rs-page-selector').val(),
                        total_pages = parseInt($('.rs-bonus-placed-orders-content-pagenav').find('.rs-total-orders').val(), 10),
                        next_page = parseInt($current_page) + 1,
                        new_page = (total_pages >= next_page) ? next_page : total_pages;

                Bonus_Points_Module.view_bonus_point_placed_order_ids_popup(false, new_page);
            }

            return false;
        },
        last_page: function (event) {
            event.preventDefault();
            var $this = $(event.currentTarget);
            if (Bonus_Points_Module.check_is_enabled($this)) {
                Bonus_Points_Module.view_bonus_point_placed_order_ids_popup(false, $('.rs-bonus-placed-orders-content-pagenav').find('.rs-total-orders').val());
            }

            return false;
        },
        check_is_enabled: function (current) {
            return !$(current).hasClass('disabled');
        },
        change_classes: function (selected, total) {
            var first_page = $('.rs-bonus-placed-orders-content-pagenav .rs-first-page'),
                    prev_page = $('.rs-bonus-placed-orders-content-pagenav .rs-prev-page'),
                    next_page = $('.rs-bonus-placed-orders-content-pagenav .rs-next-page'),
                    last_page = $('.rs-bonus-placed-orders-content-pagenav .rs-last-page');

            if (1 === selected) {
                first_page.addClass('disabled');
                prev_page.addClass('disabled');
            } else {
                first_page.removeClass('disabled');
                prev_page.removeClass('disabled');
            }

            if (total === selected) {
                next_page.addClass('disabled');
                last_page.addClass('disabled');
            } else {
                next_page.removeClass('disabled');
                last_page.removeClass('disabled');
            }
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
    Bonus_Points_Module.init();
});