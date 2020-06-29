/**
 * General admin panel handling
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Active Campaign
 * @version 1.0.0
 */

jQuery(document).ready(function ($) {
	var list_select = $('#yith_wcac_active_campaign_list, #yith_wcac_shortcode_active-campaign_list, #yith_wcac_widget_active-campaign_list, #yith_wcac_register_active-campaign_list, #yith_wcac_export_list'),
		tag_select = $('#yith_wcac_active_campaign_tags, #yith_wcac_shortcode_active-campaign_tags, #yith_wcac_shortcode_active-campaign_show_tags, #yith_wcac_widget_active-campaign_tags, #yith_wcac_widget_active-campaign_show_tags, #yith_wcac_register_active-campaign_tags, #yith_wcac_register_active-campaign_show_tags, [id^="yith_wcac_tags_order"], #yith_wcaf_product_tags'),
		field_select = $('#yith_wcac_export_field_waiting_products');

	// add updater list button
	list_select.after($('<a>').addClass('button button-secondary ajax-active-campaign-updater ajax-active-campaign-updater-list').attr('id', 'yith_wcac_active_campaign_list_updater').attr('href', '#').text(yith_wcac.labels.update_list_button));

	// add updater tags button
	tag_select.after($('<a>').addClass('button button-secondary ajax-active-campaign-updater ajax-active-campaign-updater-tags').attr('id', 'yith_wcac_active_campaign_tags_updater').attr('href', '#').text(yith_wcac.labels.update_group_button));

	// add updater fields button
	field_select.after($('<a>').addClass('button button-secondary ajax-active-ampaign-updater ajax-active-campaign-updater-field update-fields').attr('id', 'yith_wcac_active_campaign_field_updater').attr('href', '#').text(yith_wcac.labels.update_field_button));

	var handle_lists = function (ev) {
			var t = $(this),
				list = $(this).closest('td').find('select'),
				selected_option = list.find('option:selected').val();

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
					request                     : 'lists',
					force_update                : true,
					action                      : yith_wcac.actions.wcac_do_request_via_ajax_action,
					yith_wcac_ajax_request_nonce: yith_wcac.ajax_request_nonce
				},
				dataType  : 'json',
				method    : 'POST',
				success   : function (results) {
					var lists = typeof results.lists != 'undefined' ? results.lists : [],
						new_options = '',
						i = 0;

					if (lists.length) {
						while (typeof lists[i] != 'undefined') {
							new_options += '<option value="' + lists[i].id + '" ' + ((selected_option === lists[i].id) ? 'selected="selected"' : '') + ' >' + lists[i].name + '</option>';
							i++;
						}
					}

					list.html(new_options);

					if (new_options.length) {
						list.removeProp('disabled');
					} else {
						list.prop('disabled', true);
					}

				},
				url       : ajaxurl
			});
		},
		handle_tags = function (ev) {
			var t = $(this),
				tag = $(this).closest('tr').find('select'),
				selected_options = [];

			tag.find('option:selected').each(function (e) {
				selected_options.push($(this).val());
			});

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
					request                     : 'tags',
					force_update                : true,
					action                      : yith_wcac.actions.wcac_do_request_via_ajax_action,
					yith_wcac_ajax_request_nonce: yith_wcac.ajax_request_nonce
				},
				dataType  : 'json',
				method    : 'POST',
				success   : function (results) {
					var tags = typeof results.tags != 'undefined' ? results.tags : [],
						new_options = '',
						i = 0;

					if (tags) {
						while (typeof tags[i] != 'undefined') {
							new_options += '<option value="' + tags[i].id + '" ' + ($.inArray(tags[i].id, selected_options) >= 0 ? 'selected="selected"' : '') + ' >' + tags[i].tag + '</option>';
							i++;
						}
					}

					tag.html(new_options);

					if (new_options.length) {
						tag.removeProp('disabled');
					} else {
						tag.prop('disabled', true);
					}

				},
				url       : ajaxurl
			});
		},
		handle_fields = function (ev) {
			var t = $(this).hasClass('ajax-active-campaign-updater-field') ? $(this).parent().find('select') : $(this).parents('tr').next().find('select'),
				button = t.closest('td').find('.update-fields'),
				list_id = t.closest('tr').siblings().find('.list-select').find('option:selected').val(),
				select = t.parent().find('select'),
				selected_option = select.val();

			ev.preventDefault();

			if (list_id.length === 0) {
				t.prop('disabled');
			} else {
				t.removeProp('disabled');
			}
			$.ajax({
				beforeSend: function () {
					button.block({
						message   : null,
						overlayCSS: {
							background: '#fff',
							opacity   : 0.6
						}
					});


				},
				complete  : function () {
					button.unblock();
				},
				data      : {
					force_update                : true,
					action                      : yith_wcac.actions.wcac_get_fields_via_ajax_action,
					yith_wcac_ajax_request_nonce: yith_wcac.ajax_request_nonce
				},
				dataType  : 'json',
				method    : 'POST',
				success   : function (fields) {
					var new_options = '',
						i = 0;

					jQuery.each(fields, function (i, item) {
						new_options += '<option value="' + i + '" ' + ((selected_option == i) ? 'selected="selected"' : '') + ' >' + item.title + '</option>';
					});

					select.html(new_options);

					if (new_options.length === 0) {
						select.prop('disabled');
					} else {
						select.removeProp('disabled');
					}

				},
				url       : ajaxurl
			});
		},
		add_updater_functions = function () {
			$(document).off('click', '.ajax-active-campaign-updater-list');
			$(document).off('click', '.ajax-active-campaign-updater-tags');
			$(document).off('click', '.ajax-active-campaign-updater-field');
			$(document).off('change', '.list-select');

			// add updater button handler
			$(document).on('click', '.ajax-active-campaign-updater-list', handle_lists);
			$(document).on('click', '.ajax-active-campaign-updater-tags', handle_tags);
			$(document).on('click', '.ajax-active-campaign-updater-field', handle_fields);
			$(document).on('change', '.list-select', function () {
				var t = $(this).parents().find('.ajax-active-campaign-updater-tags').click();
				var t = $(this).parents().find('.ajax-active-campaign-updater-field').click();
			});
		},
		handle_send_request_buttons = function(){
			$('#yith_wcac_panel_deep-data').on( 'click', '.send-request', function(){
				var t = $(this),
					target = t.data('url');

				console.log( target );

				window.location.href = target;
			} )
		};

	$('body')
		.on('add_updater_handler', add_updater_functions)
		.on('woocommerce_variations_loaded', function () {
			$('[id^="yith_wcaf_variable_product_tags"]').parent().append($('<a>').addClass('button button-secondary ajax-active-campaign-updater ajax-active-campaign-updater-tags').attr('id', 'yith_wcac_active_campaign_tags_updater').attr('href', '#').text(yith_wcac.labels.update_group_button))
		});
	add_updater_functions();
	handle_send_request_buttons();

	// add dependencies handler
	$('#yith_wcac_checkout_trigger').on('change', function () {
		var t = $(this),
			subscription_checkbox = $('#yith_wcac_checkout_subscription_checkbox'),
			contact_status = $('#yith_wcac_contact_status');

		if (t.val() != 'never') {
			subscription_checkbox.parents('tr').show();
			contact_status.parents('tr').show();

			$('#yith_wcac_checkout_subscription_checkbox_label').parents('tr').show();
			$('#yith_wcac_checkout_subscription_checkbox_position').parents('tr').show();
			$('#yith_wcac_checkout_subscription_checkbox_default').parents('tr').show();

			subscription_checkbox.change();
			contact_status.change();
		} else {
			subscription_checkbox.parents('tr').hide();
			contact_status.parents('tr').hide();

			$('#yith_wcac_checkout_subscription_checkbox_label').parents('tr').hide();
			$('#yith_wcac_checkout_subscription_checkbox_position').parents('tr').hide();
			$('#yith_wcac_checkout_subscription_checkbox_default').parents('tr').hide();
		}
	}).change();

	$('#yith_wcac_checkout_subscription_checkbox')
		.add('#yith_wcac_register_subscription_checkbox')
		.on('change', function () {
			var t = $(this);

			if (!t.is(':visible')) {
				return;
			}

			if (t.is(':checked')) {
				$('#yith_wcac_checkout_subscription_checkbox_label, #yith_wcac_register_subscription_checkbox_label').parents('tr').show();
				$('#yith_wcac_checkout_subscription_checkbox_position').parents('tr').show();
				$('#yith_wcac_checkout_subscription_checkbox_default, #yith_wcac_register_subscription_checkbox_default').parents('tr').show();
			} else {
				$('#yith_wcac_checkout_subscription_checkbox_label, #yith_wcac_register_subscription_checkbox_label').parents('tr').hide();
				$('#yith_wcac_checkout_subscription_checkbox_position').parents('tr').hide();
				$('#yith_wcac_checkout_subscription_checkbox_default, #yith_wcac_register_subscription_checkbox_default').parents('tr').hide();
			}
		})
		.change();

	$('#yith_wcac_store_integration_abandoned_cart_enable')
		.on( 'change', function () {
			var enable_guest = $('#yith_wcac_store_integration_abandoned_cart_enable_guest'),
				delay = $('#yith_wcac_store_integration_abandoned_cart_delay');

			if( $(this).is(':checked') ){
				enable_guest.closest('tr').show().end().change();
				delay.closest('tr').show();
			}
			else{
				enable_guest.closest('tr').hide().end().change();
				delay.closest('tr').hide();
			}
		} )
		.change();

	$('#yith_wcac_store_integration_abandoned_cart_enable_guest')
		.on( 'change', function(){
			var enable_guest_after_tc = $('#yith_wcac_store_integration_abandoned_cart_enable_guest_after_tc');

			if( $(this).is(':checked') && $(this).is(':visible') ){
				enable_guest_after_tc.closest('tr').show();
			}
			else{
				enable_guest_after_tc.closest('tr').hide();
			}
		} )
		.change();

	// disconnect store button
	$( '#yith_wcac_deep_data_delete_connection' ).on( 'click', function(ev){
		var t = $(this);

		ev.preventDefault();

		if( window.confirm( yith_wcac.labels.confirm_connection_delete ) ){
			$.ajax( {
				beforeSend: function(){
					t.block({
						message: null,
						overlayCSS: {
							background: '#fff',
							opacity: 0.6
						}
					});
				},
				data: {
					action: yith_wcac.actions.wcac_delete_connection_via_ajax_action,
					yith_wcac_ajax_request_nonce: yith_wcac.ajax_request_nonce
				},
				success: function(){
					window.location.reload();
				},
				url: ajaxurl
			} );
		}
	} );

	// add tagged select2 to tag input
	tag_select.select2({
		allowClear             : true,
		minimumResultsForSearch: Infinity,
		tokenSeparators        : [',', ' ']
	});

	// change Deep Data tab Save Button label
	if( $('#yith_wcac_store_connection_name').length ) {
		$('#plugin-fw-wc-reset').hide();
		$('#plugin-fw-wc').find('input[type="submit"]').val(yith_wcac.labels.connect_store_button);
	}
});