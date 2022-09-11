/**
 * ywsbs-admin.js
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Subscription
 * @version 1.0.0
 */
/* global ywsbs_subscription_admin */
jQuery(function ($) {

	$('#ywsbs_safe_submit_field').val('');

	$('#ywsbs_schedule_subscription_button').on('click', function (e) {
		e.preventDefault();
		$('#ywsbs_safe_submit_field').val('schedule_subscription');
		$(this).closest('form').submit();
	});

	/**
	 * SUBSCRIPTION EDITOR TITLE
	 */
	if ($(document).find('.wp-heading-inline').length > 0) {
		$('<div class="view-all-subs"><a href="' + ywsbs_subscription_admin.url_back_to_all_subscription + '"> < ' + ywsbs_subscription_admin.back_to_all_subscription + '</a></div>').insertBefore('.wp-heading-inline');
	}


	/**
	 * BILLING AND SHIPPING INFO
	 */
	$(document).on('click', 'a.edit_address', function (e) {
		e.preventDefault();
		var $t = $(this),
			$edit_div = $t.closest('.subscription_data_column').find('div.edit_address'),
			$links = $t.closest('.subscription_data_column').find('a'),
			$show_div = $t.closest('.subscription_data_column').find('div.address');
		$show_div.toggle();
		$links.toggle();
		$edit_div.toggle();
	});

	var load_info = function (t, from, to, force) {
		var message = (from === to) ? 'load_' + from : 'copy_billing';

		if (true === force || window.confirm(ywsbs_subscription_admin[message])) {
			// Get user ID to load data for
			var user_id = $('#user_id').val();

			if (user_id === 0) {
				window.alert(ywsbs_subscription_admin.no_customer_selected);
				return false;
			}

			var data = {
				user_id: user_id,
				action: 'woocommerce_get_customer_details',
				security: ywsbs_subscription_admin.get_customer_details_nonce
			};

			$.ajax({
				url: ywsbs_subscription_admin.ajaxurl,
				data: data,
				type: 'POST',
				success: function (response) {
					console.log(response);
					if (response && response[from]) {
						$.each(response[from], function (key, data) {
							$('#_' + to + '_' + key).val(data).change();
						});
					}

				}
			});
		}
		return false;
	};

	$(document).on('click', '.load_customer_info', function (e) {
		e.preventDefault();
		var $t = $(this),
			from = $t.data('from'),
			to = $t.data('to');
		load_info($t, from, to);
	});


	/**
	 * METABOX SCHEDULE
	 */
	if ($.fn.datetimepicker !== undefined) {
		$(document).find('.ywsbs-timepicker').each(function () {
			$(this).prop('placeholder', 'YYYY-MM-DD HH:mm')
		}).datetimepicker({
			timeFormat: 'HH:mm:ss',
			defaultDate: '',
			dateFormat: 'yy-mm-dd',
			numberOfMonths: 1,
			showButtonPanel: true,
			showSeconds: false
		});

	}
	$(document).find('.ui-datepicker').addClass('yith-plugin-fw-datepicker-div');

	$('#ywsbs_schedule_subscription_button').on('click', function (e) {
		e.preventDefault();
		$("#ywsbs_safe_submit_field").val('schedule_subscription');
		$(this).closest('form').submit();
	});

	/**
	 * PRODUCT EDITOR
	 */
	var ywsbs_product_meta_boxes = {
		init: function () {
			var content = $(document).find('#woocommerce-order-items');
			content.on('click', 'a.edit-order-item', this.edit_item);
			content.on('click', '.save-action', this.save_items);
			content.on('click', '.recalculate-action', this.recalculate);

		},
		edit_item: function () {
			$(this).closest('tr').find('.view').hide();
			$(this).closest('tr').find('.edit').show();
			$(this).hide();
			$('.wc-order-add-item').show();
			$('.wc-order-recalculate').hide();
			$('button.cancel-action').attr('data-reload', true);
			return false;
		},
		save_items: function () {
			var data = {
				subscription_id: $('#post_ID').val(),
				items: $('table.woocommerce_order_items :input[name], .wc-order-totals-items :input[name]').serialize(),
				action: 'ywsbs_save_items',
				security: ywsbs_subscription_admin.save_item_nonce
			};

			$.ajax({
				url: ywsbs_subscription_admin.ajaxurl,
				data: data,
				type: 'POST',
				beforeSend: function () {
					$('#woocommerce-order-items').block({
						message: null,
						overlayCSS: {
							background: '#fff',
							opacity: 0.6
						}
					});
				},
				success: function (response) {
					$('#ywsbs-product-subscription').find('.inside').empty().append(response);
					$('#woocommerce-order-items').unblock();
					ywsbs_product_meta_boxes.init();
				}
			});

			return false;
		},
		recalculate: function () {
			var data = {
				subscription_id: $('#post_ID').val(),
				action: 'ywsbs_recalculate',
				security: ywsbs_subscription_admin.recalculate_nonce
			};

			$.ajax({
				url: ywsbs_subscription_admin.ajaxurl,
				data: data,
				type: 'POST',
				beforeSend: function () {
					$('#woocommerce-order-items').block({
						message: null,
						overlayCSS: {
							background: '#fff',
							opacity: 0.6
						}
					});
				},
				success: function (response) {
					$('#ywsbs-product-subscription').find('.inside').empty().append(response);
					$('#woocommerce-order-items').unblock();
					ywsbs_product_meta_boxes.init();
				}
			});
		}
	};

	ywsbs_product_meta_boxes.init();

	if ($('.ywsbs-export').length > 0) {
		$('.wp-header-end').before($('.ywsbs-export'));
		$('.ywsbs-export').css({display: 'inline-block'});
	}


	function initTable() {
		return $(document).find('.ywsbs-delivery-schedules-table').DataTable({
			"searching": true,
			"ordering": false,
			language: {
				"lengthMenu": ywsbs_subscription_admin.datatable_lengthMenu + " _MENU_",
			},
		});
	}

	var dataTable = initTable();

	$(document).on('change', '#ywsbs-delivery-schedules-status', function () {
		dataTable.columns(1).search(this.value).draw();
	});


	var updateStatus = function (data, $wrapper) {
		$.ajax({
			url: ywsbs_subscription_admin.ajaxurl,
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
					var order = $('#ywsbs-delivery-schedules-status').val();
					$wrapper.unblock();
					$wrapper.closest('.inside').html(response);
					dataTable.destroy;
					dataTable = initTable();
					if (order) {
						$('#ywsbs-delivery-schedules-status').val(order).change();
					}
				}
			}
		});
	}

	$(document).on('change', '.status-hover', function () {
		var $t = $(this),
			value = $t.val(),
			$wrapper = $t.closest('.status-td'),
			id = $wrapper.data('id');

		var confirm = $('#yith-shipped-confirm');

		var data = {
			action: 'ywsbs_update_delivery_status',
			security: ywsbs_subscription_admin.delivery_nonce,
			deliveryID: id,
			status: value,
			subscriptionID: $('#post_ID').val()
		};

		var buttons = {};
		buttons[ywsbs_subscription_admin.continue] = function () {
			updateStatus(data, $wrapper);
			confirm.dialog("close");
		};
		buttons[ywsbs_subscription_admin.cancel] = function () {
			confirm.dialog("close");
		};
		if ('shipped' === value) {
			confirm.dialog({
				width: 450,
				modal: true,
				dialogClass: 'ywsbs-shipped-confirm',
				buttons: buttons
			});
		} else {
			updateStatus(data, $wrapper);
		}

	});

	//COUPONS
	$(document).on('click', '.remove-coupon', function () {
		var $t = $(this),
			$coupon_code = $t.data('code');

		$('#ywsbs-product-subscription').block({
			message: null,
			overlayCSS: {
				background: '#fff',
				opacity: 0.6
			}
		});

		var data = {
			action: 'ywsbs_remove_subscription_coupon',
			dataType: 'json',
			subscription_id: $('#post_ID').val(),
			security: $t.data('nonce'),
			coupon: $coupon_code
		};


		$.post( ywsbs_subscription_admin.ajaxurl, data, function (response) {
			if (response.success) {
				$('#ywsbs-product-subscription').find('.inside').empty().append(response.data.html);
				$('#ywsbs-product-subscription').unblock();
			} else {
				window.alert(response.data.error);
				$('#ywsbs-product-subscription').unblock();
			}

		});

	});

	$(document).on('click', '.add-coupon', function () {
		var $t = $(this);
		var value = window.prompt( ywsbs_subscription_admin.add_coupon_text );

		if ( null !== value ) {
			$('#ywsbs-product-subscription').block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});

			var data = {
				action: 'ywsbs_add_subscription_coupon',
				dataType: 'json',
				subscription_id: $('#post_ID').val(),
				security: $t.data('nonce'),
				coupon: value.trim(),
				user_id :$( '#user_id' ).val(),
				user_email : $( '#_billing_email' ).val()
			};

			$.post( ywsbs_subscription_admin.ajaxurl, data, function (response) {

				if (response.success) {
					$('#ywsbs-product-subscription').find('.inside').empty().append(response.data.html);
					$('#ywsbs-product-subscription').unblock();
				} else {
					window.alert(response.data.error);
					$('#ywsbs-product-subscription').unblock();
				}

			});
		}

		return false;
	});
});