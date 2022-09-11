/**
 * ywsbs-admin.js
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Subscription
 * @version 1.0.0
 */

jQuery(function ($) {

	$('#yith_woocommerce_subscription_general .form-table').find('[data-deps]').each(function () {

		var t = $(this),
			wrap = t.closest('tr'),
			deps = t.attr('data-deps').split(','),
			values = t.attr('data-deps_value').split(','),
			conditions = [];

		$.each(deps, function (i, dep) {
			$('[name="' + dep + '"]').on('change', function () {

				var value = this.value,
					check_values = '';

				// exclude radio if not checked
				if (this.type == 'radio' && !$(this).is(':checked')) {
					return;
				}

				if (this.type == 'checkbox') {
					value = $(this).is(':checked') ? 'yes' : 'no';
				}

				check_values = values[i] + ''; // force to string
				check_values = check_values.split('|');
				conditions[i] = $.inArray(value, check_values) !== -1;

				if ($.inArray(false, conditions) === -1) {
					wrap.fadeIn();
				} else {
					wrap.fadeOut();
				}

			}).change();
		});
	});

	var manageFailedPaymentOptionDep = function () {
		$(document).on('change', '#ywsbs_change_status_after_renew_order_creation_status', function () {
			var $t = $(this),
				current_status = $t.val();

			$(document).find('.show-if-overdue').hide();
			$(document).find('.show-if-suspended').hide();
			$(document).find('.show-if-cancelled').hide();
			$(document).find('.show-if-' + current_status).show();

		});

		$(document).on('change', '#ywsbs_change_status_after_renew_order_creation_step_2_status', function () {
			var $t = $(this),
				current_status = $t.val(),
				div_to_change = $(document).find('.show-if-no-cancelled-step-2');

			if ('cancelled' === current_status) {
				div_to_change.hide();
			} else {
				div_to_change.show();
			}
		});

		$(document).on('change', '#ywsbs_delivery_default_schedule_delivery_period', function () {
			var $t = $(this),
				current_period = $t.val();

			$(document).find('.show-if-weeks').hide();
			$(document).find('.show-if-months').hide();
			$(document).find('.show-if-years').hide();
			$(document).find('.show-if-days').hide();
			if (current_period === 'days') {
				$(document).find('.hide-if-days').hide();
			} else {
				$(document).find('.hide-if-days').show();
			}

			$(document).find('.show-if-' + current_period).show();

		});


		$('#ywsbs_change_status_after_renew_order_creation_status').change();
		$('#ywsbs_change_status_after_renew_order_creation_step_2_status').change();
		$('#ywsbs_delivery_default_schedule_delivery_period').change();
	}

	var managePanelStyle = function () {
		// remove table row padding on subscription status when a payment failed.
		$(document).find('.without-padding').closest('tr').find('td').css({padding: '0 20px 30px 20px'});

		// add a general wrapper inside the custom list table.
		var activitiesWrapper = $(document).find('.wrap.ywsbs_subscription_activities');
		if (activitiesWrapper.length > 0) {
			activitiesWrapper.closest('.wrap.yith-plugin-ui').addClass('yith-plugin-fw-wp-page-wrapper').addClass('yith-current-subtab-opened').removeClass('wrap');
		}
	}

	manageFailedPaymentOptionDep();
	managePanelStyle();


	$('#ywsbs_change_status_after_renew_order_creation_status').on('change', function () {
		var $t = $(this);
		if ($t.val() == 'overdue') {
			$('.hide-overdue').hide();
			$('.renew_order_step1').closest('tr').addClass('no-padding-bottom');
		} else {
			$('.hide-overdue').show();
			$('.renew_order_step1').closest('tr').removeClass('no-padding-bottom');
		}
	}).change();

	$('#ywsbs_subscription_action_style').on('change', function () {
		var $t = $(this),
			can_be_cancelled = $('#ywsbs_allow_customer_cancel_subscription').is(':checked');

		if ('dropdown' === $t.val() && can_be_cancelled) {
			$('[data-dep-target="ywsbs_text_cancel_subscription_dropdown"]').show();
		} else {
			$('[data-dep-target="ywsbs_text_cancel_subscription_dropdown"]').hide();
		}
	});

	$('#ywsbs_allow_customer_cancel_subscription').on('change', function () {
		var $t = $(this),
			is_dropdown = 'dropdown' === $('#ywsbs_subscription_action_style').val();
		if ($t.is(':checked') && is_dropdown) {
			$('[data-dep-target="ywsbs_text_cancel_subscription_dropdown"]').show();
		} else {
			$('[data-dep-target="ywsbs_text_cancel_subscription_dropdown"]').hide();
		}
	});

	var updateStatus = function (data, $wrapper, $tr, newLabel) {
		$.ajax({
			url: ywsbs_admin.ajaxurl,
			data: data,
			type: 'POST',
			beforeSend: function () {
				$wrapper.block({
					message: null,
					overlayCSS: {
						background: '#fff',
						opacity: 0.6,
					}
				});
			},
			success: function (response) {
				if (typeof response.error === "undefined") {
					if (response.result === 1) {
						$wrapper.find('.status-normal').text(newLabel);
						$tr.find('.sent_on').text(response.sentOn);
					}

					$wrapper.unblock();
				}
			}
		});
	}

	$(document).on('change', '.status-hover', function () {
		var $t = $(this),
			value = $t.val(),
			$wrapper = $t.closest('.status-td'),
			$tr = $t.closest('tr'),
			current = $wrapper.find('status-normal').text(),
			newLabel = $t.find(':selected').attr('data-label'),
			id = $wrapper.data('id');
		var confirm = $('#yith-shipped-confirm');
		var data = {
			action: 'ywsbs_update_delivery_status',
			security: ywsbs_admin.delivery_nonce,
			deliveryID: id,
			status: value,
			subscriptionID: $('#post_ID').val(),
			deliveryListTable: true
		};

		var buttons = {};
		buttons[ywsbs_admin.continue] = function() {
			updateStatus(data, $wrapper, $tr, newLabel);
			confirm.dialog("close");
		};
		buttons[ywsbs_admin.cancel] = function() { confirm.dialog("close"); };

		if ('shipped' === value) {
			confirm.dialog({
				width: 450,
				modal: true,
				dialogClass: 'ywsbs-shipped-confirm',
				buttons: buttons
			});
		} else {
			updateStatus(data, $wrapper, $tr, newLabel);
		}
	});


	$('#post-query-submit').on('click', function (e) {
		e.preventDefault();
		window.onbeforeunload = null;
		$('form').submit();
	});
});