jQuery(function ($) {
/*
	$("#a2w_bulk_order_place").click(function(){
		$('.hover_a2w_fulfillment').show();
		return false;
	});*/

	$(document).on("click", ".a2w_aliexpress_order_fulfillment", function () {
	    if ( typeof $(this).attr('id') == "undefined" && $(this).attr('href').substr(0,1) == "#" ) var id = $(this).attr('href').substr(1);
        else var id =  $(this).attr('id').split('-')[1];

		var ids = [];

		ids.push(id);


		a2w_start_order_process(ids, 1);
   
	//	$.a2w_ali_fulfill_order(id);
		
		return false;
	});
	
	$.a2w_ali_fulfill_order = function (id) {
		var data = {'action': 'a2w_get_aliexpress_order_data', 'id': id};

		$.post(ajaxurl, data, function (response) {
		
			var json = jQuery.parseJSON(response);
		

			if (json.state === 'error') {
				
				console.log(json);
				jQuery('.wrap > h1').after('<div class="error notice is-dismissible"><p>'+json.error_message+'</p><button id="a2w-fulfill-dismiss-admin-message" class="notice-dismiss" type="button"><span class="screen-reader-text">Dismiss this notice.</span></button></div>');
				
				jQuery("#a2w-fulfill-dismiss-admin-message").click(function(event) {
					event.preventDefault();
					jQuery('.' + 'error').fadeTo(100, 0, function() {
						jQuery('.' + 'error').slideUp(100, function() {
							jQuery('.' + 'error').remove();
						});
					});
				});
		
			} else {
                                //console.log(json);
				if (json.action == 'upd_ord_status'){
					
				}
				a2w_get_order_fulfillment(json.data.content, function(data){
					console.log(data);
				} );
			
			}

		});	
	}

	function app_rsp_timer_run(t){
		return setTimeout(function(){

			a2w_reset_blocks();

			a2w_show_tip(a2w_sprintf(a2w_ali_orderfulfill_js.lang.get_no_responces_from_chrome_ext_d, 'https://ali2woo.com/codex/chrome-no-responce-issue/'), false, false, false, true);
				$('.hover_a2w_fulfillment .close').off('click').click(function () {
					a2w_hide_block();
					a2w_close_chrome_tab();
				});


		}, 30000);
	}


	function a2w_js_place_order(ids, state, on_load_calback){

		if (ids.length > 0) {

			var tmp_ids = ids.slice(0), id = ids.shift();

			var data = {'action': 'a2w_get_aliexpress_order_data', 'id': id};

			$.post(ajaxurl, data, function (response) {

				var json = $.parseJSON(response);
				if (json.state !== 'ok') {
					console.log(json);
				}

				if (json.state === 'error') {
					state.error_cnt += 1;
					if (on_load_calback) {

						var data = {'stage': -5}; // unknown error

						if (typeof json.error_code !== "undefined") {
							data['stage'] = json.error_code
						}

						on_load_calback(json.state, state, data, tmp_ids);
					}
				} else {
						a2w_get_order_fulfillment(json.data.content, function(data){
							on_load_calback('ok', state, data, tmp_ids);
						} );
				}


			}).fail(function (xhr, status, error) {
					console.log(error);
					state.error_cnt += 1;

					if (on_load_calback) {
						var data = {'stage': -6}; // server error
						on_load_calback('error', state, data, tmp_ids);
					}
			});
		} else {
			var data = {'stage': 6};
			on_load_calback('ok', state, data, tmp_ids);
		}
	}

	var a2w_start_order_process = function(ids, total_ids) {
		if (total_ids > 0) {
			a2w_reset_blocks();
			a2w_show_block(a2w_sprintf(a2w_ali_orderfulfill_js.lang.placing_orders_d_of_d, 0, total_ids));

			if (typeof a2w_get_order_fulfillment == "undefined") {

				a2w_reset_blocks();
				a2w_show_tip(a2w_ali_orderfulfill_js.lang.install_chrome_ext, false, false, false, true);
				$('.hover_a2w_fulfillment .close').off('click').click(function () {
					a2w_hide_block();
					a2w_close_chrome_tab();
				});

				return;
			}

			var skip_order = function(ids, state){
				ids.shift();
				state.error_cnt += 1;
				state.success_cnt += 1;
				a2w_reset_blocks();
				a2w_show_block(a2w_sprintf(a2w_ali_orderfulfill_js.lang.placing_orders_d_of_d, state.success_cnt, state.num_to_update));
				rsp_timer = app_rsp_timer_run();
				a2w_js_place_order(ids, state, on_load);
			}

			var on_load = function (response_state, state, data, ids) {

				clearTimeout(rsp_timer);

				if (response_state == "error"){

					if (data.stage === -6) {
						a2w_reset_blocks();
						a2w_show_tip(a2w_ali_orderfulfill_js.lang.server_error, true);
						$('.hover_a2w_fulfillment .continue').off('click').click(function () {
							a2w_reset_blocks();
							rsp_timer = app_rsp_timer_run();
							a2w_js_place_order(ids, state, on_load);
						});
					}

					if (data.stage === -5) {
						a2w_reset_blocks();
						a2w_show_tip(a2w_ali_orderfulfill_js.lang.unknown_error, false, true);
						$('.hover_a2w_fulfillment .skip').off('click').click(function () {
							skip_order(ids, state);
						});
					}

					if (data.stage === -4) {
						a2w_reset_blocks();
						a2w_show_tip(a2w_sprintf(a2w_ali_orderfulfill_js.lang.no_ali_products,'https://ali2woo.com/codex/no-aliexpress-prodoct-error/'),false,true);
						$('.hover_a2w_fulfillment .skip').off('click').click(function () {
							skip_order(ids, state);
						});
					}

					if (data.stage === -3) {
						a2w_reset_blocks();
						a2w_show_tip(a2w_sprintf(a2w_ali_orderfulfill_js.lang.no_product_url,'https://ali2woo.com/codex/no-product-url-error/'),false,true);
						$('.hover_a2w_fulfillment .skip').off('click').click(function () {
							skip_order(ids, state);
						});
					}

					if (data.stage === -2) {
						a2w_reset_blocks();
						a2w_show_tip(a2w_sprintf(a2w_ali_orderfulfill_js.lang.no_variable_data,'https://ali2woo.com/codex/no-variable-data-error/'),false,true);
						$('.hover_a2w_fulfillment .skip').off('click').click(function () {
							skip_order(ids, state);
						});
					}

					if (data.stage === -1) {
						a2w_reset_blocks();
						a2w_show_tip(a2w_ali_orderfulfill_js.lang.bad_product_id,false,true);
						$('.hover_a2w_fulfillment .skip').off('click').click(function () {
							skip_order(ids, state);
						});
					}

				}

				else if (typeof data !== "undefined") {

					if (data.stage === 51) {
						rsp_timer = app_rsp_timer_run();
						a2w_reset_blocks();
						a2w_show_tip(a2w_ali_orderfulfill_js.lang.cart_is_cleared);
					}

					if (data.stage === 52) {
						rsp_timer = app_rsp_timer_run();
						a2w_reset_blocks();
						a2w_show_tip(a2w_ali_orderfulfill_js.lang.all_products_are_added);
					}

					if (data.stage === 53) {
						rsp_timer = app_rsp_timer_run();
						a2w_reset_blocks();
						var msg = typeof data.param !== "undefined" ? a2w_sprintf(a2w_ali_orderfulfill_js.lang.product_is_added_to_cart, data.param) : a2w_ali_orderfulfill_js.lang.product_is_added_to_cart;
						a2w_show_tip(msg);
					}

					if (data.stage === 54) {
						rsp_timer = app_rsp_timer_run();
						a2w_reset_blocks();
						a2w_show_tip(a2w_ali_orderfulfill_js.lang.your_customer_address_entered);
					}

					if (data.stage === 55) {

						a2w_reset_blocks();
						a2w_show_tip(a2w_ali_orderfulfill_js.lang.detected_old_aliexpress_interface, false, false, false, true);
						$('.hover_a2w_fulfillment .close').off('click').click(function () {
							a2w_hide_block();
							a2w_close_chrome_tab();
						});
					}

					if (data.stage === 56) {
						rsp_timer = app_rsp_timer_run();
						a2w_reset_blocks();
						a2w_show_tip(a2w_ali_orderfulfill_js.lang.fill_order_note);
					}

					if (data.stage === 57) {

						a2w_reset_blocks();
						a2w_show_tip(a2w_ali_orderfulfill_js.lang.done_pay_manually,false,false,false,false,true);
						$('.hover_a2w_fulfillment .solve').off('click').click(function () {
							rsp_timer = app_rsp_timer_run();
							a2w_switch_to_chrome_tab();
						});
					}

					if (data.stage === 0) {
						a2w_show_tip(a2w_ali_orderfulfill_js.lang.login_into_aliexpress_account,false,false,false,false,true);

						$('.hover_a2w_fulfillment .solve').off('click').click(function () {
							rsp_timer = app_rsp_timer_run();
							a2w_switch_to_chrome_tab();
						});

					}

					if (data.stage === 1) {
						a2w_show_tip(a2w_sprintf(a2w_ali_orderfulfill_js.lang.please_connect_chrome_extension_check_d, 'https://ali2woo.com/codex/ali2woo-google-chrome-extension/'), true);

						$('.hover_a2w_fulfillment .continue').off('click').click(function () {
							rsp_timer = app_rsp_timer_run();
							a2w_start_order_process(ids, ids.length);
						});
					}

					if (data.stage === 2) {
						a2w_show_tip(a2w_ali_orderfulfill_js.lang.please_activate_right_store_apikey_in_chrome, true);

						$('.hover_a2w_fulfillment .continue').off('click').click(function () {
							rsp_timer = app_rsp_timer_run();
							a2w_start_order_process(ids, ids.length);
						});
					}

					if (data.stage === 11) {
						a2w_show_tip(a2w_ali_orderfulfill_js.lang.we_found_old_order, true);

						$('.hover_a2w_fulfillment .continue').off('click').click(function () {
							a2w_reset_blocks();
							a2w_show_block(a2w_sprintf(a2w_ali_orderfulfill_js.lang.placing_orders_d_of_d, state.success_cnt, state.num_to_update));
							rsp_timer = app_rsp_timer_run();
							a2w_js_place_order(ids, state, on_load);
						});
					}

					if (data.stage === 21) {

						a2w_show_tip(a2w_sprintf(a2w_ali_orderfulfill_js.lang.cant_add_product_to_cart_d,'https://ali2woo.com/codex/chrome-add-to-cart-issue/'), true,false,false,false,true);

						$('.hover_a2w_fulfillment .solve').off('click').click(function () {
							a2w_switch_to_chrome_tab();
						});

						$('.hover_a2w_fulfillment .continue').off('click').click(function () {
							rsp_timer = app_rsp_timer_run();
							a2w_get_order_fulfillment({});
							a2w_switch_to_chrome_tab();
						});
					}

					if (data.stage === 3) {
						a2w_show_tip(a2w_ali_orderfulfill_js.lang.please_type_customer_address,false,true,false,false,true);

						$('.hover_a2w_fulfillment .solve').off('click').click(function () {
							rsp_timer = app_rsp_timer_run();
							a2w_switch_to_chrome_tab();
						});

						//+
						$('.hover_a2w_fulfillment .skip').off('click').click(function () {
							skip_order(ids, state);
						});
					}

					if (data.stage === 33) {
						a2w_show_tip(a2w_ali_orderfulfill_js.lang.please_input_captcha,false,false,false,false,true);

						$('.hover_a2w_fulfillment .solve').off('click').click(function () {
							rsp_timer = app_rsp_timer_run();
							a2w_switch_to_chrome_tab();
						});
					}

					if (data.stage === 5) {

						a2w_show_tip(a2w_ali_orderfulfill_js.lang.order_is_placed);

						setTimeout(function(){
							ids.shift();
							state.success_cnt += 1;
							a2w_reset_blocks();
							a2w_show_block(a2w_sprintf(a2w_ali_orderfulfill_js.lang.placing_orders_d_of_d, state.success_cnt, state.num_to_update));
							rsp_timer = app_rsp_timer_run();
							a2w_js_place_order(ids, state, on_load);

						},1500);
					}

					if (data.stage === 41) {
						a2w_show_tip(a2w_ali_orderfulfill_js.lang.internal_aliexpress_error, true,true);

						$('.hover_a2w_fulfillment .continue').off('click').click(function () {
							rsp_timer = app_rsp_timer_run();
							a2w_start_order_process(ids, ids.length);
						});

						$('.hover_a2w_fulfillment .skip').off('click').click(function () {
							skip_order(ids, state);
						})
					}

					if (data.stage === 42) {
						a2w_show_tip(a2w_ali_orderfulfill_js.lang.payment_is_failed, true);

						$('.hover_a2w_fulfillment .continue').off('click').click(function () {
							ids.shift();
							state.error_cnt += 1;
							state.success_cnt += 1;
							a2w_reset_blocks();
							a2w_show_block(a2w_sprintf(a2w_ali_orderfulfill_js.lang.placing_orders_d_of_d, state.success_cnt, state.num_to_update));
							rsp_timer = app_rsp_timer_run();
							a2w_js_place_order(ids, state, on_load);
						})
					}

					if (data.stage === 43) {
						a2w_show_tip(a2w_ali_orderfulfill_js.lang.cant_get_order_id, true);

						$('.hover_a2w_fulfillment .continue').off('click').click(function () {
							ids.shift();
							state.error_cnt += 1;
							state.success_cnt += 1;
							a2w_reset_blocks();
							a2w_show_block(a2w_sprintf(a2w_ali_orderfulfill_js.lang.placing_orders_d_of_d, state.success_cnt, state.num_to_update));
							rsp_timer = app_rsp_timer_run();
							a2w_js_place_order(ids, state, on_load);
						})
					}

					if (data.stage === 44) {
						a2w_show_tip(a2w_ali_orderfulfill_js.lang.choose_payment_method,false,false,false,false,true);

						$('.hover_a2w_fulfillment .solve').off('click').click(function () {
							rsp_timer = app_rsp_timer_run();
							a2w_switch_to_chrome_tab();
						})
					}

					if (data.stage === 6) {
						a2w_reset_blocks();
						if (state.error_cnt < state.num_to_update) {
							a2w_show_tip(a2w_ali_orderfulfill_js.lang.all_orders_are_placed, false, false, true);
							$('.hover_a2w_fulfillment .payall').off('click').click(function () {
								a2w_go_to_payall();
								a2w_hide_block();
							});
						} else {
							a2w_show_tip(a2w_ali_orderfulfill_js.lang.cant_process_your_orders, false, false, false, true);
							$('.hover_a2w_fulfillment .close').off('click').click(function () {
								a2w_hide_block();
								a2w_close_chrome_tab();
							});
						}
					}
				}


			};

			var state = {num_to_update: total_ids, success_cnt: 0, error_cnt: 0};

			var rsp_timer = app_rsp_timer_run();
			a2w_js_place_order(ids, state, on_load);

		}
	}

	$("#doaction, #doaction2").click(function (event) {
		var check_action = ($(this).attr('id') == 'doaction') ? $('#bulk-action-selector-top').val() : $('#bulk-action-selector-bottom').val();
		if ('a2w_order_place_bulk' === check_action) {
			event.preventDefault();

			var ids = [], cnt = 0, total_ids = 0;

			$('input:checkbox[name="post[]"]:checked').each(function () {
				total_ids++;
				cnt++;
				ids.push($(this).val());
			});

			a2w_start_order_process(ids, total_ids);

		}
	});


	var a2w_show_block = function(message){
		$('.hover_a2w_fulfillment .pr').html(message)
		$('.hover_a2w_fulfillment').show();
	}

	var a2w_hide_block = function(){
		$('.hover_a2w_fulfillment').hide();
	}

	var a2w_show_tip = function(message, _continue, skip, payall, close, solve){

		a2w_reset_blocks();

		$('.hover_a2w_fulfillment .tip').html(message)
		$('.hover_a2w_fulfillment .tip').show();

		if (_continue){
			$('.hover_a2w_fulfillment .continue').show();
		}
		if (skip){
			$('.hover_a2w_fulfillment .skip').show();
		}
		if (payall){
			$('.hover_a2w_fulfillment .payall').show();
		}

		if (typeof close !== "undefined" && close){
			$('.hover_a2w_fulfillment .close').show();
		}

		if (typeof solve !== "undefined" && solve){
			$('.hover_a2w_fulfillment .solve').show();
		}
	}

	var a2w_reset_blocks = function(){
		$('.hover_a2w_fulfillment .tip').html('');
		$('.hover_a2w_fulfillment .tip').hide();
		$('.hover_a2w_fulfillment .continue').hide();
		$('.hover_a2w_fulfillment .skip').hide();
		$('.hover_a2w_fulfillment .payall').hide();
		$('.hover_a2w_fulfillment .close').hide();
		$('.hover_a2w_fulfillment .solve').hide();
	}

});