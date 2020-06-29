jQuery(document).ready(function ($) {
	"use strict";

	var $body = $('body'),
		$add_to_cart_el = $('.add-request-quote-button'),
		$widget = $(document).find('.widget_ywraq_list_quote, .widget_ywraq_mini_list_quote'),
		ajax_loader = (typeof ywraq_frontend !== 'undefined') ? ywraq_frontend.block_loader : false,
		allow_out_of_stock = (typeof ywraq_frontend !== 'undefined') ? ywraq_frontend.allow_out_of_stock : false,
		allow_only_out_of_stock = (typeof ywraq_frontend !== 'undefined') ? ywraq_frontend.allow_only_on_out_of_stock : false,
		$remove_item = $('.yith-ywraq-item-remove'),
		$shipping_form = $('.shipping_address'),
		$billing_form = $('.woocommerce-billing-fields'),
		url = document.location.href,
		table = $(document).find('#yith-ywrq-table-list'),
		blockParams = {
			message: null,
			overlayCSS: {background: '#fff', opacity: 0.7},
			ignoreIfBlocked: true
		};

	allow_out_of_stock = (allow_out_of_stock == 'yes') ? true : false;
	allow_only_out_of_stock = (allow_only_out_of_stock == 'yes') ? true : false;

	if (table.length > 0 && ywraq_frontend.raq_table_refresh_check) {
		$.post(url, function (data) {
			if (data != '') {
				var c = $("<div></div>").html(data),
					table = c.find('#yith-ywrq-table-list');
				$('#yith-ywrq-table-list').html(table.html());
				$(document).trigger('ywraq_table_reloaded');
			}
		});
	}

	$(document).on('ywraq_table_reloaded, yith_wwraq_removed_successfully, yith_check_table', function () {
		var table = $(document).find('#yith-ywrq-table-list');
		if (table.length == 0) {
			$(document).find('.yith-ywraq-before-table .wc-backward').hide();
		}
	});
	$(document).trigger( 'yith_check_table' );


	/* Variation change */
	$.fn.yith_ywraq_variations = function () {


		var form = $(document).find('form.variations_form');

		if (form.length && typeof form.data('product_id') !== 'undefined') {
			var product_id = form.data('product_id').toString().replace(/[^0-9]/g, ''),
				buttonWrap = $('.add-to-quote-' + product_id).find('.yith-ywraq-add-button'),
				button = buttonWrap.find('a.add-request-quote-button'),
				add_response = $('.yith_ywraq_add_item_product-response-' + product_id),
				raq_message = $('.yith_ywraq_add_item_response-' + product_id),
				browse_list = $('.yith_ywraq_add_item_browse-list-' + product_id),
				initMe = function () {
					// init
					button.show().addClass('disabled');
					buttonWrap.show().removeClass('hide').removeClass('addedd');
					add_response.hide().removeClass('show');
					raq_message.hide().removeClass('show');
					browse_list.hide().removeClass('show');

					if (allow_only_out_of_stock && allow_out_of_stock || allow_only_out_of_stock) {
						button.hide();
					}
				};

			form.on('found_variation', function (ev, variation) {
				var variationData = '' + $('.add-to-quote-' + product_id).attr('data-variation'),
					show_button = true;

				add_response.hide().removeClass('show');

				if (allow_out_of_stock) {
					if (allow_only_out_of_stock && variation.is_in_stock) {
						show_button = false;
					}
				} else {
					if (!variation.is_in_stock) {
						show_button = false;
					}
				}


				if (show_button) {
					button.show().removeClass('disabled');
					buttonWrap.show().removeClass('hide').removeClass('addedd');
					raq_message.hide().removeClass('show');
					browse_list.hide().removeClass('show');
				} else {
					button.hide().addClass('disabled');
					buttonWrap.hide().removeClass('show').removeClass('addedd');
					raq_message.hide().removeClass('show');
					browse_list.hide().removeClass('show');
				}

				if (variationData.indexOf('' + variation.variation_id) !== -1 && show_button) {
					button.hide();
					raq_message.show().removeClass('hide');
					browse_list.show().removeClass('hide');
				} else if (show_button) {
					button.show().removeClass('disabled');
					buttonWrap.show().removeClass('hide').removeClass('addedd');
					raq_message.hide().removeClass('show');
					browse_list.hide().removeClass('show');
				}
			});

			form.on('reset_data', function (ev) {
				initMe();
			});

			initMe();
		}
	};

	// INIT RAQ VARIATIONS
	$('.variations_form').each(function () {
		$(this).yith_ywraq_variations();
	});

	$(document).on('qv_loader_stop', function (ev) {
		$('.variations_form').each(function () {
			$(this).yith_ywraq_variations();
		});
	});

	$.fn.yith_ywraq_refresh_button = function () {
		var $product_id = $('[name|="product_id"]'),
			product_id = $product_id.val(),
			button = $('.add-to-quote-' + product_id).find('a.add-request-quote-button'),
			$button_wrap = button.parents('.yith-ywraq-add-to-quote'),
			$variation_id = $('[name|="variation_id"]');

		if (!$variation_id.length) {
			return false;
		}

	};
	$.fn.yith_ywraq_refresh_button();

	var xhr = false;

	/* Add to cart element */
	$(document).on('click', '.add-request-quote-button', function (e) {

		e.preventDefault();

		var $t = $(this),
			$t_wrap = $t.closest('.yith-ywraq-add-to-quote'),
			add_to_cart_info = 'ac',
			$cart_form = '';

		if ($t.hasClass('outofstock')) {
			window.alert(ywraq_frontend.i18n_out_of_stock);
		} else if ($t.hasClass('disabled')) {
			window.alert(ywraq_frontend.i18n_choose_a_variation);
		}
		if ($t.hasClass('disabled') || $t.hasClass('outofstock') || xhr) {
			return;
		}

		if ($('.grouped_form').length) {
			var qtys = 0;
			$('.grouped_form input.qty').each(function () {
				qtys = Math.floor($(this).val()) + qtys;
			});
			if (qtys == 0) {
				alert(ywraq_frontend.select_quantity);
				return;
			}
		}
		// find the form
		if ($t.closest('.cart').length) {
			$cart_form = $t.closest('.cart');
		} else if ($t_wrap.siblings('.cart').first().length) {
			$cart_form = $t_wrap.siblings('.cart').first();
		} else if ($('.composite_form').length) {
			$cart_form = $('.composite_form');
		} else if ($t.closest('ul.products').length > 0) {
			$cart_form = $t.closest('ul.products');
		} else {
			$cart_form = $('.cart:not(.in_loop)'); // not(in_loop) for color and label
		}

		if (typeof $cart_form[0] !== 'undefined' && typeof $cart_form[0].checkValidity === 'function' && !$cart_form[0].checkValidity()) {
			// If the form is invalid, submit it. The form won't actually submit;
			// this will just cause the browser to display the native HTML5 error messages.
			$('<input type="submit">').hide().appendTo($cart_form).click().remove();
			return;
		}

		if ($t.closest('ul.products').length > 0) {
			var $add_to_cart_el = '',
				$product_id_el = $t.closest('li.product').find('a.add_to_cart_button'),
				$product_id_el_val = $product_id_el.data('product_id');
		} else {
			var $add_to_cart_el = $t.closest('.product').find('input[name="add-to-cart"]'),
				$product_id_el = $t.closest('.product').find('input[name="product_id"]'),
				$product_id_el_val = $t.data('product_id') || ($product_id_el.length ? $product_id_el.val() : $add_to_cart_el.val());
		}

		var prod_id = (typeof $product_id_el_val == 'undefined') ? $t.data('product_id') : $product_id_el_val;

		add_to_cart_info = $cart_form.serializefiles();

		add_to_cart_info.append('context', 'frontend');
		add_to_cart_info.append('action', 'yith_ywraq_action');
		add_to_cart_info.append('ywraq_action', 'add_item');
		add_to_cart_info.append('product_id', $t.data('product_id'));
		add_to_cart_info.append('wp_nonce', $t.data('wp_nonce'));
		add_to_cart_info.append('yith-add-to-cart', $t.data('product_id'));
		var quantity = $t_wrap.find('input.qty').val();


		if (quantity > 0) {
			add_to_cart_info.append('quantity', quantity);
		}

		//compatibility with Woocommerce Product Table by Barn2media
		if ($('.wc-product-table-wrapper').length > 0) {
			var quantity = $t.parents('.product-row').find('.cart input.qty').val()
			if (quantity > 0) {
				add_to_cart_info.append('quantity', quantity);
			}

		}

		//compatibility with YITH Quick Order Form
		if ($t.closest('.yith_wc_qof_button_and_price').length > 0) {
			var qof_wrap = $t.closest('.yith_wc_qof_button_and_price'),
				qof_quantity = qof_wrap.find('.YITH_WC_QOF_Quantity_Cart').val();
			add_to_cart_info.append('quantity', qof_quantity);
		}

		// compatibility with color and label
		var wcclForm = $t.closest('li.product').find('.variations_form.in_loop'),
			varID_wccl = wcclForm.length ? wcclForm.data('active_variation') : false;
		if (varID_wccl) {
			add_to_cart_info.append('variation_id', varID_wccl);
			// get select value
			wcclForm.find('select').each(function () {
				add_to_cart_info.append(this.name, this.value);
			});
		}

		$(document).trigger('yith_ywraq_action_before');

		if (typeof yith_wapo_general !== 'undefined') {
			if (!yith_wapo_general.do_submit) {
				return false;
			}
		}

		if (typeof ywcnp_raq !== 'undefined') {
			if (!ywcnp_raq.do_submit) {
				return false;
			}
		}

		xhr = $.ajax({
			type: 'POST',
			url: ywraq_frontend.ajaxurl.toString().replace('%%endpoint%%', 'yith_ywraq_action'),
			dataType: 'json',
			data: add_to_cart_info,
			contentType: false,
			processData: false,
			beforeSend: function () {
				$t.after(' <img src="' + ajax_loader + '" class="ywraq-loader" >');
			},
			complete: function () {
				$t.next().remove();
			},

			success: function (response) {
				if (response.result == 'true' || response.result == 'exists') {

					if (ywraq_frontend.go_to_the_list == 'yes') {
						window.location.href = response.rqa_url;
					} else {
						$('.yith_ywraq_add_item_response-' + prod_id).hide().addClass('hide').html('');
						$('.yith_ywraq_add_item_product-response-' + prod_id).show().removeClass('hide').html(response.message);
						$('.yith_ywraq_add_item_browse-list-' + prod_id).show().removeClass('hide');
						$t.parent().hide().removeClass('show').addClass('addedd');
						$('.add-to-quote-' + prod_id).attr('data-variation', response.variations);

						if ($widget.length) {
							$widget.ywraq_refresh_widget();
							$widget = $(document).find('.widget_ywraq_list_quote, .widget_ywraq_mini_list_quote');
						}

						ywraq_refresh_number_items();
					}

					$(document).trigger('yith_wwraq_added_successfully', [response]);

				} else if (response.result == 'false') {
					$('.yith_ywraq_add_item_response-' + prod_id).show().removeClass('hide').html(response.message);

					$(document).trigger('yith_wwraq_error_while_adding');
				}
				xhr = false;
			}
		});

	});

	$.fn.serializefiles = function () {
		var obj = $(this);
		/* ADD FILE TO PARAM AJAX */
		var formData = new FormData();
		$.each($(obj).find("input[type='file']"), function (i, tag) {
			$.each($(tag)[0].files, function (i, file) {
				formData.append(tag.name, file);
			});
		});

		var params = $(obj).serializeArray();

		var quantity_in = false;
		$.each(params, function (i, val) {
			if (val.name == 'quantity' || val.name.indexOf("quantity")) {
				quantity_in = true;
			}

			if (val.name != 'add-to-cart') {

				formData.append(val.name, encodeURIComponent(val.value));
			}
		});

		if (quantity_in === false) {
			formData.append('quantity', 1);
		}
		return formData;
	};

	/* Refresh the widget */
	$.fn.ywraq_refresh_widget = function () {
		$widget.each(function () {
			var $t = $(this),
				$wrapper_list = $t.find('.yith-ywraq-list-wrapper'),
				$list = $t.find('.yith-ywraq-list'),
				data_widget = $t.find('.yith-ywraq-list-widget-wrapper').data('instance');

			$.ajax({
				type: 'POST',
				url: ywraq_frontend.ajaxurl.toString().replace('%%endpoint%%', 'yith_ywraq_action'),
				data: data_widget + '&ywraq_action=refresh_quote_list&action=yith_ywraq_action&context=frontend',
				beforeSend: function () {
					$list.css('opacity', 0.5);
					if ($t.hasClass('widget_ywraq_list_quote')) {
						$wrapper_list.prepend(' <img src="' + ajax_loader + '" class="ywraq-loader">');
					}
				},
				complete: function () {
					if ($t.hasClass('widget_ywraq_list_quote')) {
						$wrapper_list.next().remove();
					}
					$list.css('opacity', 1);
				},
				success: function (response) {
					if ($t.hasClass('widget_ywraq_mini_list_quote')) {
						$t.find('.yith-ywraq-list-widget-wrapper').html(response.mini);
					} else {
						$t.find('.yith-ywraq-list-widget-wrapper').html(response.large);
					}
					$(document).trigger('yith_ywraq_widget_refreshed');
				}
			});
		});
	};

	/*Remove an item from rqa list*/
	$(document).on('click', '.yith-ywraq-item-remove', function (e) {

		e.preventDefault();

		var $t = $(this),
			key = $t.data('remove-item'),
			wrapper = $t.parents('.ywraq-wrapper'),
			form = $('#yith-ywraq-form'),
			cf7 = wrapper.find('.wpcf7-form'),
			gravity_forms = wrapper.find('.gform_wrapper'),
			remove_info = '',
			product_id = $t.data('product_id');

		remove_info = 'context=frontend&action=yith_ywraq_action&ywraq_action=remove_item&key=' + $t.data('remove-item') + '&wp_nonce=' + $t.data('wp_nonce') + '&product_id=' + product_id;

		$.ajax({
			type: 'POST',
			url: ywraq_frontend.ajaxurl.toString().replace('%%endpoint%%', 'yith_ywraq_action'),
			dataType: 'json',
			data: remove_info,
			beforeSend: function () {
				$t.find('.ajax-loading').css('visibility', 'visible');
			},
			complete: function () {
				$t.siblings('.ajax-loading').css('visibility', 'hidden');
			},
			success: function (response) {
				if (response === 1) {
					var $row_to_remove = $("[data-remove-item='" + key + "']").parents('.cart_item');

					//compatibility with WC Composite Products
					if ($row_to_remove.hasClass('composite-parent')) {
						var composite_id = $row_to_remove.data('composite-id');
						$("[data-composite-id='" + composite_id + "']").remove();
					}

					//compatibility with YITH WooCommerce Product Add-ons
					if ($row_to_remove.hasClass('yith-wapo-parent')) {
						var wapo_id = $row_to_remove.find('.product-remove a').data('remove-item');
						$("[data-wapo_parent_key='" + wapo_id + "']").remove();
					}

					if ($row_to_remove.hasClass('ywcp_component_item')) {
						$('tr.ywcp_component_child_item').filter("[data-wcpkey='" + key + "']").remove();
					}

					//compatibility with YITH WooCommerce Product Bundles Premium
					if ($row_to_remove.hasClass('bundle-parent')) {
						var bundle_key = $row_to_remove.data('bundle-key');
						$("[data-bundle-key='" + bundle_key + "']").remove();
					}

					$row_to_remove.remove();

					if ($('.cart_item').length === 0) {

						if (cf7.length) {
							cf7.remove();
						}

						if (gravity_forms.length) {
							gravity_forms.remove();
						}

						$('#yith-ywraq-form, .yith-ywraq-mail-form-wrapper').remove();
						$('#yith-ywraq-message').html(ywraq_frontend.no_product_in_list);
					}
					if ($widget.length) {
						$widget.ywraq_refresh_widget();
						$widget = $(document).find('.widget_ywraq_list_quote, .widget_ywraq_mini_list_quote');
					}

					ywraq_refresh_number_items();

					//restore the request a quote button on the product removed
					$(document).find('.hide-when-removed[data-product_id="' + product_id + '"]').hide();
					$(document).find('.yith-ywraq-add-button[data-product_id="' + product_id + '"]').show();

					$(document).trigger('yith_wwraq_removed_successfully');
				} else {
					$(document).trigger('yith_wwraq_error_while_removing');
				}
			}
		});
	});

	/* clean the request list table - remove all the items */
	$('.ywraq_clean_list').on('click', function (e) {
		e.preventDefault();
		$('#yith-ywrq-table-list tbody .product-remove a.remove').each(function () {
			$(this).trigger('click');
		});
	});

	var content_data = '';
	/* Contact Form 7 */
	var $cform7 = $('.wpcf7-submit').closest('.wpcf7');
	if ($cform7.length > 0) {

		$(document).find('.ywraq-wrapper .wpcf7').each(function () {
			var $cform7 = $(this);
			var idform = $cform7.find('input[name="_wpcf7"]').val();

			if (idform == ywraq_frontend.cform7_id) {

				$cform7.on('wpcf7:mailsent', function () {
					$.ajax({
						type: 'POST',
						url: ywraq_frontend.ajaxurl.toString().replace('%%endpoint%%', 'yith_ywraq_order_action'),
						dataType: 'json',
						data: {
							lang: ywraq_frontend.current_lang,
							action: 'yith_ywraq_order_action',
							current_user_id: ywraq_frontend.current_user_id,
							context: 'frontend',
							ywraq_order_action: 'mail_sent_order_created'
						},
						success: function (response) {
							if (response.rqa_url != '') {
								window.location.href = response.rqa_url;
							}
						}
					});
				});


				document.addEventListener('wpcf7mailsent', function (event) {

					window.location.href = ywraq_frontend.rqa_url;

				}, false);
			}


		});
	}

	$('#yith-ywrq-table-list').on('change', '.qty', function () {
		var qty = $(this).val();
		if (qty <= 0) {
			$(this).val(1);
		}
	});

	/* Gravity Form */
	$(document).bind('gform_confirmation_loaded', function (event, formId) {
		// code to be trigger when confirmation page is loaded
		if (ywraq_frontend.gf_id == formId) {
			window.location.href = ywraq_frontend.rqa_url;
		}
	});


	/**
	 * To fix the problem of update the quantity automatically when the theme use a different quantity field
	 * https://gist.github.com/kreamweb/cede2722b72b1b558ea592b8fbf23413
	 */

	if (ywraq_frontend.auto_update_cart_on_quantity_change) {

		$(document).on('click, change', '.product-quantity input', function (e) {
			var $t = $(this),
				name = $t.attr('name'),
				container = $t.closest('.quantity');

			container.block(blockParams);

			if (typeof name == 'undefined') {
				var $input_quantity = $t.closest('.product-quantity').find('.input-text.qty'),
					name = $input_quantity.attr('name'),
					value = $input_quantity.val(),
					item_keys = name.match(/[^[\]]+(?=])/g);

				//this is not necessary for some theme like flatsome
				if ($t.hasClass('plus')) {
					value++;
				}

				if ($t.hasClass('minus')) {
					value--;
				}
				//end

				var request_info = 'context=frontend&action=yith_ywraq_action&ywraq_action=update_item_quantity&quantity=' + value + '&key=' + item_keys[0];

			} else {
				var value = $t.val(),
					item_keys = name.match(/[^[\]]+(?=])/g),
					request_info = 'context=frontend&action=yith_ywraq_action&ywraq_action=update_item_quantity&quantity=' + value + '&key=' + item_keys[0];

			}

			var request = $.ajax({
				type: 'POST',
				url: ywraq_frontend.ajaxurl.toString().replace('%%endpoint%%', 'yith_ywraq_action'),
				dataType: 'json',
				data: request_info,
				success: function (response) {
					$.post(url, function (data) {
						if (data != '') {
							var c = $("<div></div>").html(data),
								table = c.find('#yith-ywrq-table-list');
							$('#yith-ywrq-table-list').html(table.html());
							$(document).trigger('ywraq_table_reloaded');
							if ($widget.length) {
								$widget.ywraq_refresh_widget();
								$widget = $(document).find('.widget_ywraq_list_quote, .widget_ywraq_mini_list_quote');
							}
						}
						container.unblock();
					});
				}
			});
		});

	}


	/* disable shipping fields */
	if ($shipping_form.length > 0 && ywraq_frontend.lock_shipping == true) {
		$shipping_form.find('input').attr("readonly", "readonly");
		$shipping_form.find('select').attr("readonly", "readonly");
		$('.woocommerce-checkout #shipping_country_field').css('pointer-events', 'none');
		$('.woocommerce-checkout #shipping_state_field').css('pointer-events', 'none');
	}

	if ($billing_form.length > 0 && ywraq_frontend.lock_billing == true) {
		$billing_form.find('input').attr("readonly", "readonly");
		$billing_form.find('select').attr("readonly", "readonly");
		$('.woocommerce-checkout #billing_country_field').css('pointer-events', 'none');
		$('.woocommerce-checkout #billing_state_field').css('pointer-events', 'none');

	}


	function ywraq_refresh_number_items() {
		var $number_items = $(document).find('.ywraq_number_items');
		$number_items.each(function () {
			var $t = $(this),
				show_url = $t.data('show_url'),
				item_name = $t.data('item_name'),
				item_plural_name = $t.data('item_plural_name');

			$.ajax({
				type: 'POST',
				url: ywraq_frontend.ajaxurl.toString().replace('%%endpoint%%', 'yith_ywraq_action'),
				data: 'ywraq_action=refresh_number_items&action=yith_ywraq_action&context=frontend&item_name=' + item_name + '&item_plural_name=' + item_plural_name + '&show_url=' + show_url,
				success: function (response) {
					$t.replaceWith(response);
					$(document).trigger('ywraq_number_items_refreshed');
				}
			});

		});
	};

	//to fix the cache of page
	$widget.ywraq_refresh_widget();
	ywraq_refresh_number_items();


	//checkout button
	function trigger_checkout_quote_click() {
		$(document).on('click', '#ywraq_checkout_quote', function (e) {
			$(document).find('input[name="payment_method"]').val('yith-request-a-quote');
			$('#ywraq_checkout_quote').val(true);
		});
	}

	trigger_checkout_quote_click();

	$(document).find('.theme-yith-proteo .ywraq-wrapper .woocommerce-message').removeAttr('role');


	/**
	 * Ajax Loading
	 */
	if( 1 == ywraq_frontend.enable_ajax_loading ){
		initAjaxLoad();
	}

	function initAjaxLoad() {
		var fragments = getYwraqFragments();
		fragments && updateYwraqFragments(fragments);
	}

	function getYwraqFragments() {
		var objects = $(document).find('.yith-ywraq-add-to-quote'),
			fragments = [];
		if (objects.length === 0) {
			return false;
		}

		$.each(objects, function () {
			var $t = $(this),
				is_variable = $t.closest('.variations_form '),
			    id = $(this).find('.yith-ywraq-add-button').data('product_id');

			if( typeof is_variable === 'undefined'  ){
				fragments.push(id);
			}
		});

		return fragments;
	}

	function updateYwraqFragments(fragments) {
		var data = {
			fragments: fragments,
			ywraq_action: 'update_ywraq_fragments',
			action: 'yith_ywraq_action',
			context: 'frontend'
		};
		$.ajax({
			type: 'POST',
			url: ywraq_frontend.ajaxurl.toString().replace('%%endpoint%%', 'yith_ywraq_action'),
			data: data,
			success: function (response) {
				if ( typeof response.error !== 'undefined' ) {
					console.log( response.error );
				}else if ( response.success === true ) {
					refreshYwraqFragments(  response.fragments );
				}
			}
		});
	}

	function refreshYwraqFragments(fragments) {

		var objects = $(document).find('.yith-ywraq-add-to-quote');

		if( objects.length > 0 ){
			$.each(objects, function () {
				var $t = $(this),
					variation = $t.data('variation'),
					id = $t.find('.yith-ywraq-add-button').data('product_id');

				if(  typeof variation === 'undefined' && typeof fragments[id] !== 'undefined'){
					$t.replaceWith( fragments[id] );
				}
			});
		}
	}

	/* Ajax Loading End */

});
