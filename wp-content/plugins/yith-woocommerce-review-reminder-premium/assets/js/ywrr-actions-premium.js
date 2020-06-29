jQuery(function ($) {

    $('body')
        .on('click', 'a.ywrr-schedule-actions', function () {

            var $button = $(this);

            $(this).WCBackboneModal({
                template: 'ywrr-actions',
                variable: {
                    button_type     : $button.data('button-type'),
                    order_id        : $button.data('order-id'),
                    order_item_id   : $button.data('order-item-id'),
                    booking_id      : $button.data('booking-id'),
                    order_date      : $button.data('order-date'),
                    additional_label: $button.data('additional-label'),
                    scheduled_date  : $button.data('scheduled-date'),
                    row_id          : $button.parent().prop('id')
                }
            });

            return false;

        })
        .on('click', 'a.ywrr-schedule-delete', function () {
            var $button = $(this);

            $(this).WCBackboneModal({
                template: 'ywrr-delete',
                variable: {
                    order_id  : $button.data('order-id'),
                    booking_id: $button.data('booking-id'),
                    row_id    : $button.parent().prop('id')
                }
            });

            return false;
        })
        .on('click', '.ywrr-send-box', function () {
            $(this).find('a.ywrr-schedule-actions').click();
            return false;
        });

    $(document.body).on('wc_backbone_modal_loaded', function () {

        $(document).trigger('yith_fields_init');

        $('.ywrr-email-action').click(function () {

            var data = {},
                items_to_review = {},
                container = $('.ywrr-actions-modal'),
                order_id = container.find('.ywrr-order-id').val(),
                order_date = container.find('.ywrr-order-date').val(),
                order_item_id = container.find('.ywrr-order-item-id').val(),
                booking_id = container.find('.ywrr-booking-id').val(),
                action_type = container.find('input[type=radio]:checked').val(),
                schedule_date = container.find('#schedule_date').val();

            container
                .find('#schedule_date')
                .parent()
                .removeClass('is_required')
                .find('span')
                .remove()
                .find('.error-message')
                .html('');

            if (action_type === 'schedule' && schedule_date === '') {
                container
                    .find('#schedule_date')
                    .parent()
                    .addClass('is_required')
                    .append('<span>' + ywrr_actions.missing_date_error + '</span>');
                return;
            }

            container.block({
                message   : null,
                overlayCSS: {
                    background: '#fff',
                    opacity   : 0.6
                }
            });

            if (order_item_id !== '' && order_item_id !== '0') {
                items_to_review [order_item_id] = order_item_id;
            }

            if (action_type === 'now') {
                data = {
                    action         : 'ywrr_send_request_mail',
                    order_id       : order_id,
                    order_date     : order_date,
                    booking_id     : booking_id,
                    items_to_review: JSON.stringify(items_to_review, null, '')
                };
            } else {
                data = {
                    action         : 'ywrr_reschedule_mail',
                    order_id       : order_id,
                    booking_id     : booking_id,
                    schedule_date  : schedule_date,
                    items_to_review: JSON.stringify(items_to_review, null, '')
                };
            }

            $.post(ywrr_actions.ajax_url, data, function (response) {

                if (response.success === true) {

                    $.post(document.location.href, function (data) {
                        if (data !== '') {
                            var temp_content = $("<div></div>").html(data),
                                row_id = container.find('.ywrr-row-id').val(),
                                content = temp_content.find('#' + row_id);
                            $('#' + row_id).html(content.html());
                            $('.modal-close').click();
                        }
                    });

                } else {
                    container
                        .find('.error-message')
                        .html(response.error)
                }

                container.unblock();

            });

            return false;

        });

        $('.ywrr-delete-action').click(function () {

            var container = $('.ywrr-delete-modal'),
                order_id = container.find('.ywrr-order-id').val(),
                booking_id = container.find('.ywrr-booking-id').val();

            container
                .find('.error-message')
                .html('');

            container.block({
                message   : null,
                overlayCSS: {
                    background: '#fff',
                    opacity   : 0.6
                }
            });

            var data = {
                action    : 'ywrr_cancel_mail',
                order_id  : order_id,
                booking_id: booking_id
            };

            $.post(ywrr_actions.ajax_url, data, function (response) {

                if (response === true) {

                    $.post(document.location.href, function (data) {
                        if (data !== '') {
                            var temp_content = $("<div></div>").html(data),
                                row_id = container.find('.ywrr-row-id').val(),
                                content = temp_content.find('#' + row_id);
                            $('#' + row_id).html(content.html());
                            $('.modal-close').click();
                        }
                    });

                } else {
                    container
                        .find('.error-message')
                        .html(response.error)
                }

                container.unblock();

            });

            return false;

        });

        $('input[name=action_type]').change(function () {

            var value = $('input[name=action_type]:checked').val(),
                button = $('.ywrr-email-action');
            if (value === 'now') {
                button.val(ywrr_actions.send_button_label);
            } else {
                button.val(ywrr_actions.schedule_button_label);
            }

        });

    });

    $(document).ready(function () {

        var bulk_selectors = $('#bulk-action-selector-top, #bulk-action-selector-bottom');
        bulk_selectors.append('<option value="ywrr_send">' + ywrr_actions.send_label + '</option>');
        bulk_selectors.append('<option value="ywrr_reschedule">' + ywrr_actions.reschedule_label + '</option>');
        bulk_selectors.append('<option value="ywrr_cancel">' + ywrr_actions.cancel_label + '</option>');

    });

});



