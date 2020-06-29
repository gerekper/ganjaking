/*
 * @package YITH WooCommerce Dynamic Pricing and Discounts Premium
 * @since   1.0.0
 * @author  YITH
 */

jQuery(document).ready(function ($) {
	"use strict";
	var wrapper = $(document).find('.ywdpd-sections-group'),
		container = wrapper.find('.ywdpd-section'),
		eventType = container.find('.yith-ywdpd-eventype-select'),
		del_msg = (typeof yith_ywdpd_admin !== 'undefined') ? yith_ywdpd_admin.del_msg : false,
		ajax_url = yith_ywdpd_admin.ajaxurl + '?action=ywdpd_admin_action',

		/****
		 * Deps function option
		 */
		deps_func = function (eventType) {
			eventType.each(function () {
				var t = $(this),
					field = t.data('field'),
					selected = t.find('option:selected');

				hide_show_func(t, selected.val(), field);

				t.on('change', function () {
					var field = t.data('field'),
						selected = t.find('option:selected');
					hide_show_func(t, selected.val(), field);
				})
			});
		},

		hide_show_func = function (t, val, field) {
			var opt = t.closest('.ywdpd-select-wrapper').find('tr.deps-' + field);

			opt.each(function () {
				var types = $(this).data('type').split(';');
				if ($.inArray(val, types) !== -1) {
					$(this).show();
				} else {
					$(this).hide();
					if (typeof $(this).data('rel') !== 'undefined') {
						var item_class = 'deps-' + $(this).data('rel');
						$(this).parents('.ywdpd-section').find('.' + item_class).hide();
					}


				}
			});
		};


	/****
	 * Add a row pricing rules
	 ****/
	$(document).on('click', '.add-row', function () {
		var $t = $(this),
			table = $t.closest('table'),
			current_row = $t.closest('tr'),
			current_index = parseInt(current_row.data('index')),
			clone = current_row.clone(),
			rows = table.find('tr'),
			max_index = 1;

		clone.find('select').removeClass('enhanced');
		clone.find('span.select2').remove();

		rows.each(function () {
			var index = $(this).data('index');
			if (index > max_index) {
				max_index = index;
			}
		});

		var new_index = max_index + 1;
		clone.attr('data-index', new_index);
		var fields = clone.find("[name*='rules']");

		fields.each(function () {
			var $t = $(this),
				name = $t.attr('name'),
				id = $t.attr('id'),

				new_name = name.replace('[rules][' + current_index + ']', '[rules][' + new_index + ']'),
				new_id = id.replace('[rules][' + current_index + ']', '[rules][' + new_index + ']');

			$t.attr('name', new_name);
			$t.attr('id', new_id);
			$t.val('');

		});

		clone.find('.remove-row').removeClass('hide-remove');
		table.append(clone);
		$(document.body).trigger('wc-enhanced-select-init');
		var eventType = clone.find('.yith-ywdpd-eventype-select');
		deps_func(eventType);
	});

	/****
	 * remove a row pricing rules
	 ****/
	$(document).on('click', '.remove-row', function () {
		var $t = $(this),
			current_row = $t.closest('tr');
		current_row.remove();
	});

	/**
	 * Add handler for row sortable
	 */
	$('.post-type-ywdpd_discount table.wp-list-table  tr.type-ywdpd_discount').append(
		'<td class="column-handle"></td>'
	);
	$('.post-type-ywdpd_discount table.wp-list-table thead tr').append(
		'<th class="manage-column" id="handle" scope="col"></th>'
	);
	$('.post-type-ywdpd_discount table.wp-list-table tfoot tr').append(
		'<th class="manage-column" id="handle" scope="col"></th>'
	);

	jQuery('.ywdpd-sections-group').sortable({
		items: '.ywdpd-section-handle',
		cursor: 'move',
		axis: 'y',
		handle: 'form',
		scrollSensitivity: 40,
		forcePlaceholderSize: true,
		helper: 'clone',
		start: function (event, ui) {
			ui.item.css('background-color', '#f6f6f6');
		},
		stop: function (event, ui) {
			ui.item.removeAttr('style');
			var keys = $('.ywdpd-section-handle'), i = 0, array_keys = new Array();
			for (i = 0; i < keys.length; i++) {
				array_keys[i] = $(keys[i]).data('key');
			}

			if (array_keys.length > 0) {
				$.post(ajax_url, {
					ywdpd_action: 'order_section',
					tab: $('.form-table').closest('.yit-admin-panel-content-wrap-full').data('type'),
					order_keys: array_keys
				}, function (resp) {
				});
			}

		}
	});

	// init

	deps_func(eventType);

	container.find('.datepicker').each(function () {
		$(this).prop('placeholder', 'YYYY-MM-DD HH:mm')
	}).datetimepicker({
		timeFormat: 'HH:mm',
		defaultDate: '',
		dateFormat: 'yy-mm-dd',
		numberOfMonths: 1
	});



	if ($('#_discount_type').length) {
		var std = $('#_discount_type').data('std'),
			href = $('.page-title-action').attr('href');
		$('#_discount_type').attr('value', std);
		$('.page-title-action').attr('href', href + '&ywdpd_discount_type=' + std);
	}

	$('#_schedule_from, #_schedule_to').each(function () {
		$(this).prop('placeholder', 'YYYY-MM-DD HH:mm')
	}).datetimepicker({
		timeFormat: 'HH:mm',
		defaultDate: '',
		dateFormat: 'yy-mm-dd',
		numberOfMonths: 1,
	});

	var neweventType = $('#ywdpd_cart_discount').find('.yith-ywdpd-eventype-select');
	deps_func(neweventType);

	$('.post-type-ywdpd_discount table.wp-list-table').sortable({
		items: 'tbody tr:not(.inline-edit-row)',
		cursor: 'move',
		handle: '.column-handle',
		axis: 'y',
		forcePlaceholderSize: true,
		helper: 'clone',
		opacity: 0.65,
		start: function (event, ui) {
			ui.item.css('background-color', '#f6f6f6');
		},
		stop: function (event, ui) {
			ui.item.removeAttr('style');
			var roleid = ui.item.find('.check-column input').val(); // this post id
			var previd = ui.item.prev().find('.check-column input').val();
			var nextid = ui.item.next().find('.check-column input').val();

			$.post(ajax_url, {
				ywdpd_action: 'table_order_section',
				type: $(document).find('body').hasClass('ywdpd-discount-type-cart') ? 'cart' : 'pricing',
				roleid: roleid,
				previd: previd,
				nextid: nextid
			}, function (resp) {
			});
		}
	});

	$('#ywdpd-discount-list-table').on('submit', function () {
		var $t = $(this),

			bulk = $t.find('#bulk-action-selector-top').val();

		if (bulk == 'delete') {
			var confirm = window.confirm(del_msg);
			if (confirm == true) {
				return true;
			} else {
				return false;
			}
		}

	});

	$('.cart_discount,.cart_discount_type').show();


	/**
	 * Register toggle enabled
	 */
	$(document).on('change', '.ywdpd-toggle-enabled input', function () {
		var enabled = $(this).val() === 'yes' ? 'yes' : 'no',
			container = $(this).closest('.ywdpd-toggle-enabled'),
			discountID = container.data('discount-id'),
			security = container.data('security');

		$.ajax({
			type: 'POST',
			data: {
				ywdpd_action: 'discount_toggle_enabled',
				id: discountID,
				enabled: enabled,
				security: security
			},
			url: ajax_url,
			success: function (response) {
				if (typeof response.error !== 'undefined') {
					alert(response.error);
				}
			}

		});
	});


	/**
	 * Added discount type to links
	 */
	if ($(document).find('body').hasClass('post-type-ywdpd_discount')) {
		var linkList = $('.subsubsub').find('li a'),
			rowAction = $('.row-actions'),
			type = '';

		if( $(document).find('body').hasClass('ywdpd-discount-type-cart') ){
			type = 'cart';
		}

		if( $(document).find('body').hasClass('ywdpd-discount-type-pricing') ){
			type = 'pricing';
		}

		if( $(document).find('#ywdpd_discount_type').length > 0 ){
			type = $(document).find('#ywdpd_discount_type').val();
		}

		$.each( linkList, function () {
			var $t = $(this),
				link = $t.attr('href');

			link += '&ywdpd_discount_type=' + type;

			$t.attr('href', link);
		});

		$.each( rowAction, function () {
			var $t = $(this),
				postLinks = $t.find('a');

			$.each( postLinks, function () {
				var $tt = $(this),
					link = $tt.attr('href');

				link += '&ywdpd_discount_type=' + type;

				$tt.attr('href', link);
			});

		});

		var $pageAction = $(document).find('a.page-title-action');

		if ($pageAction.length > 0) {
			$.each($pageAction, function () {
				var $t = $(this),
					link = $t.attr('href');

				link += '&ywdpd_discount_type=' + type;

				$t.attr('href', link);
			});
		}

		var filter_form = $(document).find('#posts-filter');
		$('<input>').attr({
			type: 'hidden',
			value: type,
			name: 'ywdpd_discount_type'
		}).appendTo(filter_form);
	}


	$(document).find('.ui-datepicker').addClass('yith-plugin-fw-datepicker-div');
});
