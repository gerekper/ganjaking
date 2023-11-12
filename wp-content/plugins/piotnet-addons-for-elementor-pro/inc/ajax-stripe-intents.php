<?php
	require_once('stripe-vendor/autoload.php');

	add_action( 'wp_ajax_pafe_ajax_stripe_intents', 'pafe_ajax_stripe_intents' );
	add_action( 'wp_ajax_nopriv_pafe_ajax_stripe_intents', 'pafe_ajax_stripe_intents' );

	function find_element_recursive_stripe( $elements, $form_id ) {
		foreach ( $elements as $element ) {
			if ( $form_id === $element['id'] ) {
				return $element;
			}

			if ( ! empty( $element['elements'] ) ) {
				$element = find_element_recursive( $element['elements'], $form_id );

				if ( $element ) {
					return $element;
				}
			}
		}

		return false;
	}

	function set_val_stripe(&$array,$path,$val) {
		for($i=&$array; $key=array_shift($path); $i=&$i[$key]) {
			if(!isset($i[$key])) $i[$key] = array();
		}
		$i = $val;
	}

	function pafe_merge_string_stripe(&$string,$string_add) {
		$string = $string . $string_add;
	}

	function pafe_unset_string_stripe(&$string) {
		$string = '';
	}

	function pafe_set_string_stripe(&$string,$string_set) {
		$string = $string_set;
	}

	function replace_email_stripe($content, $fields, $payment_status = 'succeeded', $payment_id = '', $succeeded = 'succeeded', $pending = 'pending', $failed = 'failed', $submit_id = 0 ) {
		$message = $content;

		$message_all_fields = '';

		if (!empty($fields)) {

			// all fields
			foreach ($fields as $field) {
				$field_value = $field['value'];
				$field_label = isset($field['label']) ? $field['label'] : '';
				if (isset($field['value_label'])) {
					$field_value = $field['value_label'];
				}

				$repeater_id = $field['repeater_id'];
				$repeater_id_string = '';
				$repeater_id_array = array_reverse( explode(',', rtrim($repeater_id, ',')) );
				foreach ($repeater_id_array as $repeater) {
					$repeater_array = explode('|', $repeater);
					array_pop($repeater_array);
					$repeater_id_string .= join(",",$repeater_array);
				}
				$repeater_index = $field['repeater_index']; 
				$repeater_index_1 = $repeater_index + 1;
				$repeater_label = '<span data-id="' . $repeater_id_string . '"><strong>' . $field['repeater_label'] . ' ' . $repeater_index_1 . ': </strong></span><br>';

				$repeater_remove_this_field = false;
				if (isset($field['repeater_remove_this_field'])) {
					$repeater_remove_this_field = true;
				}
				
				if (!empty($repeater_id) && !empty($repeater_label) && $repeater_remove_this_field == false) {
					if (strpos($message_all_fields, $repeater_label) !== false) {
						$message_all_fields .= $field_label . ': ' . $field_value . '<br />';
					} else {
						$message_all_fields .= $repeater_label;
						$message_all_fields .= $field_label . ': ' . $field_value . '<br />';
					}
					// if ($field['repeater_index'] != ($field['repeater_length'] - 1)) {
					// 	$message .=  '<br />';
					// }
				} else {
					if (strpos($field['name'], 'pafe-end-repeater') === false) {
						$message_all_fields .= $field_label . ': ' . $field_value . '<br />';
					}
				}

			}

			$message = str_replace( '[all-fields]', $message_all_fields, $message );

			// each field

			$repeater_content = '';
			$repeater_id_one = '';
			foreach ($fields as $field) {
				$field_value = $field['value'];
				$field_label = isset($field['label']) ? $field['label'] : '';
				if (isset($field['value_label'])) {
					$field_value = $field['value_label'];
				}

				$search_remove_line_if_field_empty = '[field id="' . $field['name'] . '"]' . '[remove_line_if_field_empty]';

				if (empty($field_value)) {
					$lines = explode("\n", $message);
					$lines_found = array();

					foreach($lines as $num => $line){
					    $pos = strpos($line, $search_remove_line_if_field_empty);
					    if($pos !== false) {
					    	$lines_found[] = $line;
					    }
					}

					if (!empty($lines_found)) {
						foreach ($lines_found as $line) {
							$message = str_replace( [ $line . "\n", "\n" . $line ], '', $message );
						}
					}
				}

				$search = '[field id="' . $field['name'] . '"]';
				$message = str_replace($search, $field_value, $message);

				$repeater_id = $field['repeater_id'];
				$repeater_id_string = '';
				$repeater_id_array = array_reverse( explode(',', rtrim($repeater_id, ',')) );
				foreach ($repeater_id_array as $repeater) {
					$repeater_array = explode('|', $repeater);
					array_pop($repeater_array);
					$repeater_id_string .= join(",",$repeater_array);
				}
				$repeater_index = $field['repeater_index']; 
				$repeater_index_1 = $repeater_index + 1;
				$repeater_label = '<span data-id="' . $repeater_id_string . '"><strong>' . $field['repeater_label'] . ' ' . $repeater_index_1 . ': </strong></span><br>';

				$repeater_remove_this_field = false;
				if (isset($field['repeater_remove_this_field'])) {
					$repeater_remove_this_field = true;
				}
				
				if (!empty($repeater_id) && !empty($repeater_label) && $repeater_remove_this_field == false) {
					if (strpos($repeater_content, $repeater_label) !== false) {
						$string_add = $field_label . ': ' . $field_value . '<br />';
						pafe_merge_string_stripe($repeater_content,$string_add);
					} else {
						$string_add = $repeater_label . $field_label . ': ' . $field_value . '<br />';
						pafe_merge_string_stripe($repeater_content,$string_add);
					}
					if (substr_count($field['repeater_id'],'|') == 2) {
						pafe_set_string_stripe($repeater_id_one,$field['repeater_id_one']);
					}
				}

				if (empty($repeater_id)) {
					if (!empty($repeater_id_one) && !empty($repeater_content)) {
						$search_repeater = '[repeater id="' . $repeater_id_one . '"]';
						$message = str_replace($search_repeater, $repeater_content, $message);
						pafe_unset_string_stripe($repeater_content);
						pafe_unset_string_stripe($repeater_id_one);
					}
				}
				
			}
		}

		$search_remove_line_if_field_empty = '"]' . '[remove_line_if_field_empty]'; // fix alert [

		$lines = explode("\n", $message);
		$lines_found = array();

		foreach($lines as $num => $line){
		    $pos = strpos($line, $search_remove_line_if_field_empty);
		    if($pos !== false) {
		    	$lines_found[] = $line;
		    }
		}

		if (!empty($lines_found)) {
			foreach ($lines_found as $line) {
				$message = str_replace( [ $line . "\n", "\n" . $line ], '', $message );
			}
		}

		$message = str_replace( [ "[remove_line_if_field_empty]" ], '', $message );

		$message = str_replace( [ "\r\n", "\n", "\r", "[remove_line_if_field_empty]" ], '<br />', $message );

		if ($payment_status == 'succeeded') {
			$message = str_replace( '[payment_status]', $succeeded, $message );
		}

		if ($payment_status == 'pending') {
			$message = str_replace( '[payment_status]', $pending, $message );
		}

		if ($payment_status == 'failed') {
			$message = str_replace( '[payment_status]', $failed, $message );
		}

		if (!empty($payment_id)) {
			$message = str_replace( '[payment_id]', $payment_id, $message );
		}

		if (!empty($submit_id)) {
			$message = str_replace( '[submit_id]', $submit_id, $message );
		}

		return $message;
	}

	function generatePaymentResponse($intent,$subscriptions=false) {
	    # Note that if your API version is before 2019-02-11, 'requires_action'
	    # appears as 'requires_source_action'.
	    if ($intent->status == 'requires_action' &&
	        $intent->next_action->type == 'use_stripe_sdk' || $intent->status == 'requires_source_action' &&
	        $intent->next_action->type == 'use_stripe_sdk') {
	      # Tell the client to handle the action
	      echo json_encode([
	        'requires_action' => true,
	        'payment_intent_client_secret' => $intent->client_secret,
	        "subscriptions" => $subscriptions,
	        "payment_intent_id" => $intent->id,
	      ]);
	    } else if ($intent->status == 'succeeded') {
	      # The payment didn’t need any additional actions and completed!
	      # Handle post-payment fulfillment
	      echo json_encode([
	        "success" => true,
	        "payment_intent_id" => $intent->id,
	      ]);
	    } else {
	      # Invalid status
	      http_response_code(500);
	      echo json_encode(['error' => 'Invalid PaymentIntent status']);
	    }
	}

	function get_field_name_shortcode_stripe($content) {
		$field_name = str_replace('[field id="', '', $content);
		$field_name = str_replace('[repeater id="', '', $field_name); // fix alert ]
		$field_name = str_replace('"]', '', $field_name);
		return trim($field_name);
	}

	function pafe_get_field_value_stripe($field_name,$fields, $payment_status = 'succeeded', $payment_id = '', $succeeded = 'succeeded', $pending = 'pending', $failed = 'failed' ) {

		$field_name_first = $field_name;

		if (strpos($field_name, '[repeater id') !== false) { // ] [ [ fix alert
			$field_name = str_replace('id="', "id='", $field_name);
			$field_name = str_replace('"]', "']", $field_name);
			$message = $field_name;
			$repeater_content = '';
			$repeater_id_one = '';
			foreach ($fields as $field) {
				$field_label = isset($field['label']) ? $field['label'] : '';
				$search = '[field id="' . $field['name'] . '"]';
				$message = str_replace($search, $field['value'], $message);

				$repeater_id = $field['repeater_id'];
				$repeater_id_string = '';
				$repeater_id_array = array_reverse( explode(',', rtrim($repeater_id, ',')) );
				foreach ($repeater_id_array as $repeater) {
					$repeater_array = explode('|', $repeater);
					array_pop($repeater_array);
					$repeater_id_string .= join(",",$repeater_array);
				}
				$repeater_index = $field['repeater_index']; 
				$repeater_index_1 = $repeater_index + 1;
				$repeater_label = $field['repeater_label'] . ' ' . $repeater_index_1 . '\n';
				
				if (!empty($repeater_id) && !empty($repeater_label)) {
					if (strpos($repeater_content, $repeater_label) !== false) {
						$string_add = $field_label . ': ' . $field['value'] . '\n';
						pafe_merge_string_stripe($repeater_content,$string_add);
					} else {
						$string_add = $repeater_label . $field_label . ': ' . $field['value'] . '\n';
						pafe_merge_string_stripe($repeater_content,$string_add);
					}
					if (substr_count($field['repeater_id'],'|') == 2) {
						pafe_set_string_stripe($repeater_id_one,$field['repeater_id_one']);
					}
				}

				if (empty($repeater_id)) {
					if (!empty($repeater_id_one) && !empty($repeater_content)) {
						$search_repeater = "[repeater id='" . $repeater_id_one . "']";
						$message = str_replace($search_repeater, $repeater_content, $message);

						pafe_unset_string_stripe($repeater_content);
						pafe_unset_string_stripe($repeater_id_one);
					}
				}
			}

			$field_value = $message;
		} else {
			$field_name = get_field_name_shortcode($field_name);
			$field_value = '';
			foreach ($fields as $key_field=>$field) {
				if ($fields[$key_field]['name'] == $field_name) {
					// if (!empty($fields[$key_field]['value'])) {
					// 	$field_value = $fields[$key_field]['value'];
					// }

					if (isset($fields[$key_field]['calculation_results'])) {
						$field_value = $fields[$key_field]['calculation_results'];
					} else {
						$field_value = $fields[$key_field]['value'];
					}
				}
			}
		}

		if (strpos($field_name_first, '[payment_status]') !== false || strpos($field_name_first, '[payment_id]') !== false) {
			if ($payment_status == 'succeeded') {
				$field_value = str_replace( '[payment_status]', $succeeded, $field_name_first );
			}

			if ($payment_status == 'pending') {
				$field_value = str_replace( '[payment_status]', $pending, $field_name_first );
			}

			if ($payment_status == 'failed') {
				$field_value = str_replace( '[payment_status]', $failed, $field_name_first );
			}

			if (!empty($payment_id) && strpos($field_name_first, '[payment_id]') !== false) {
				$field_value = str_replace( '[payment_id]', $payment_id, $field_name_first );
			}
		}
		
		return trim($field_value);
	}

	function pafe_ajax_stripe_intents() {
		global $wpdb;

		\Stripe\Stripe::setApiKey(get_option('piotnet-addons-for-elementor-pro-stripe-secret-key'));


		if ( !empty($_POST['post_id']) && !empty($_POST['form_id']) && !empty($_POST['fields']) ) {
			$post_id = $_POST['post_id'];
			$form_id = $_POST['form_id'];
			$fields = stripslashes($_POST['fields']);
			$fields = json_decode($fields, true);
			$fields = array_unique($fields, SORT_REGULAR);

			$elementor = \Elementor\Plugin::$instance;

			if ( version_compare( ELEMENTOR_VERSION, '2.6.0', '>=' ) ) {
				$meta = $elementor->documents->get( $post_id )->get_elements_data();
			} else {
				$meta = $elementor->db->get_plain_editor( $post_id );
			}

			$form = find_element_recursive_stripe( $meta, $form_id );
			$widget = $elementor->elements_manager->create_element_instance( $form );
			$form['settings'] = $widget->get_active_settings();
			$currency = strtolower($form['settings']['pafe_stripe_currency']);
			$customer_array = array();

			if (!empty($_POST['description'])) {
				$customer_array['description'] = esc_sql( $_POST['description'] );
			}

			if (!empty($form['settings']['pafe_stripe_customer_field_name'])) {
				$customer_name = replace_email_stripe($form['settings']['pafe_stripe_customer_field_name'], $fields);
				if (!empty($customer_name)) {
					$customer_array['name'] = $customer_name;
				}
			}

			if (!empty($form['settings']['pafe_stripe_customer_field_email'])) {
				$customer_email = replace_email_stripe($form['settings']['pafe_stripe_customer_field_email'], $fields);
				if (!empty($customer_email)) {
					$customer_array['email'] = $customer_email;
				}
			}

			if (!empty($form['settings']['pafe_stripe_customer_field_phone'])) {
				$customer_phone = replace_email_stripe($form['settings']['pafe_stripe_customer_field_phone'], $fields);
				if (!empty($customer_phone)) {
					$customer_array['phone'] = $customer_phone;
				}
			}

			if (!empty($form['settings']['pafe_stripe_customer_field_address_line2'])) {
				$customer_address_line2 = replace_email_stripe($form['settings']['pafe_stripe_customer_field_address_line2'], $fields);
				if (!empty($customer_address_line2)) {
					$customer_array['address']['line2'] = $customer_address_line2;
				}
			}

			if (!empty($form['settings']['pafe_stripe_customer_field_address_city'])) {
				$customer_city = replace_email_stripe($form['settings']['pafe_stripe_customer_field_address_city'], $fields);
				if (!empty($customer_city)) {
					$customer_array['address']['city'] = $customer_city;
				}
			}

			if (!empty($form['settings']['pafe_stripe_customer_field_address_country'])) {
				$customer_country = replace_email_stripe($form['settings']['pafe_stripe_customer_field_address_country'], $fields);
				if (!empty($customer_country)) {
					$customer_array['address']['country'] = $customer_country;
				}
			}

			if (!empty($form['settings']['pafe_stripe_customer_field_address_postal_code'])) {
				$customer_postal_code = replace_email_stripe($form['settings']['pafe_stripe_customer_field_address_postal_code'], $fields);
				if (!empty($customer_postal_code)) {
					$customer_array['address']['postal_code'] = $customer_postal_code;
				}
			}

			if (!empty($form['settings']['pafe_stripe_customer_field_address_state'])) {
				$customer_state = replace_email_stripe($form['settings']['pafe_stripe_customer_field_address_state'], $fields);
				if (!empty($customer_state)) {
					$customer_array['address']['state'] = $customer_state;
				}
			}

			if (!empty($form['settings']['pafe_stripe_customer_field_address_line1'])) {
				$customer_address_line1 = replace_email_stripe($form['settings']['pafe_stripe_customer_field_address_line1'], $fields);
				if (!empty($customer_address_line1)) {
					$customer_array['address']['line1'] = $customer_address_line1;
				} else {
					unset($customer_array['address']);
				}
			}

			if (empty($form['settings']['pafe_stripe_subscriptions'])) {

				$amount = floatval($_POST['amount']);

				if ($currency != 'jpy') {
					$amount = $amount * 100;
				}
				
				try {
					if (!empty($_POST['payment_method_id'])) {

						$token = $_POST['stripeToken'];

						// $customer_array["source"] = $token;
						
						$currency = strtolower($form['settings']['pafe_stripe_currency']);

						// Create Customer In Stripe
						$customer_array["payment_method"] = $_POST['payment_method_id'];
						$customer_array["invoice_settings"] = [
							'default_payment_method' => $_POST['payment_method_id'],
						];
						$customer_array["description"] = replace_email_stripe($form['settings']['pafe_stripe_customer_info_field'], $fields);
						$customer = \Stripe\Customer::create($customer_array);

						$fields_metadata = array();

						$fields_metadata_index = 0;
						foreach ($fields as $field) {
							$fields_metadata_index++;
							if ($fields_metadata_index < 50) {
								if (strlen($field['name']) < 40) {
									if (!empty($field['type'])) {
										if ($field['type'] != 'signature') {
											$fields_metadata[$field['name']] = $field['value'];
										}
									} else {
										$fields_metadata[$field['name']] = $field['value'];
									}
								}
							}
						}
						//Filters metadata
						$fields_metadata = apply_filters( 'pafe_customs_metadata_stripe', $fields_metadata );
						# Create the PaymentIntent
						$intent_array = array(
							'payment_method' => $_POST['payment_method_id'],
							'amount' => $amount,
				    		'currency' => $currency,
							'confirmation_method' => 'manual',
							'confirm' => true,
							'customer' => $customer->id,
							'metadata' => $fields_metadata,
                            'return_url' => $_REQUEST['referrer']
						);

						if (!empty($form['settings']['pafe_stripe_customer_receipt_email'])) {
							$intent_array['receipt_email'] = replace_email_stripe($form['settings']['pafe_stripe_customer_receipt_email'], $fields);
						}

						if (!empty($form['settings']['pafe_stripe_customer_description'])) {
							$intent_array['description'] = replace_email_stripe($form['settings']['pafe_stripe_customer_description'], $fields);
						} else {
							$intent_array['description'] = $form_id;
						}

						$intent = \Stripe\PaymentIntent::create( $intent_array );
						if(!empty($form['settings']['pafe_stripe_create_invoice'])){
							$invoiceitems = [
								'customer' => $customer->id,
								'amount' => $amount,
								'currency' => $currency,
								'description' => replace_email_stripe($form['settings']['pafe_stripe_customer_description'], $fields),
							];
							$invoice_create = [
								'customer' => $customer->id,
							];
							if(!empty($form['settings']['pafe_stripe_tax_invoice'])){
								$invoice_create['default_tax_rates'] = [replace_email_stripe($form['settings']['pafe_stripe_tax_invoice'], $fields)];
							}
							pafe_update_invoiceitems(http_build_query($invoiceitems));
							pafe_create_invoice(http_build_query($invoice_create));
						}
					}
					if (!empty($_POST['payment_intent_id'])) {
						$intent = \Stripe\PaymentIntent::retrieve(
							$_POST['payment_intent_id']
						);
						$intent->confirm();
						
					}
					generatePaymentResponse($intent);
				} catch (\Stripe\Exception\ApiErrorException $e) {
					# Display error on client
					echo json_encode([
						'error' => $e->getMessage()
					]);
				}

			} else {
				if (!empty($_POST['payment_method_id'])) {

					$token = $_POST['stripeToken'];

					// $customer_array["source"] = $token;
					$customer_array["payment_method"] = $_POST['payment_method_id'];
					$customer_array["invoice_settings"] = [
					    'default_payment_method' => $_POST['payment_method_id'],
					];
					
					$currency = strtolower($form['settings']['pafe_stripe_currency']);

					if (!empty($_POST['description'])) {
						$customer_array['description'] = esc_sql( $_POST['description'] );
					}
					$customer_array["description"] = replace_email_stripe($form['settings']['pafe_stripe_customer_info_field'], $fields);
					// Create Customer In Stripe
					try{
						$customer = \Stripe\Customer::create($customer_array);

						$fields_metadata = array();

						$fields_metadata_index = 0;
						foreach ($fields as $field) {
							$fields_metadata_index++;

							if ($fields_metadata_index < 50) {
								if (!empty($field['type'])) {
									if ($field['type'] != 'signature') {
										$fields_metadata[$field['name']] = $field['value'];
									}
								}
							}
						}

						$subscriptions = $form['settings']['pafe_stripe_subscriptions_list'];
						$product_name = $form['settings']['pafe_stripe_subscriptions_product_name'];
						$product_id = $form['settings']['pafe_stripe_subscriptions_product_id'];
						$one_time_fee = 0;
						$cancel_after = '';
					}catch(\Stripe\Exception\ApiErrorException $e){
						echo json_encode([
							'error' => $e->getMessage()
						]);
						wp_die();
					}
					if(!empty($form['settings']['pafe_stripe_subscriptions_only_price_enable'])){
						try{
							$subscription_array = array(
								'customer' => $customer->id,
								'expand' => array('latest_invoice.payment_intent'),
								'enable_incomplete_payments' => true,
							);
							if(!empty($form['settings']['pafe_stripe_tax_rate_enable']) && !empty($form['settings']['pafe_stripe_tax_rate'])){
								$subscription_array['default_tax_rates'] = [
									replace_email_stripe($form['settings']['pafe_stripe_tax_rate'], $fields)
								];
							}
							$subscription_array['items'][0]['price'] = replace_email_stripe($form['settings']['pafe_stripe_subscriptions_price_id'], $fields);

							$subscription = \Stripe\Subscription::create($subscription_array);
							$payment_status = $subscription->status;
							$payment_id = $subscription->id;
							$intent = $subscription->latest_invoice->payment_intent;
							\Stripe\PaymentIntent::update(
								$intent->id,
								[
									'description' => replace_email_stripe($form['settings']['pafe_stripe_customer_description'], $fields),
								]
							);
							generatePaymentResponse($intent,true);
						}catch (\Stripe\Exception\ApiErrorException $e) {
							# Display error on client
							echo json_encode([
								'error' => $e->getMessage()
							]);
						}
					}else{
						if (!empty($subscriptions)) {
							if (!empty($product_name)) {
								if (count($subscriptions) == 1 && empty($form['settings']['pafe_stripe_subscriptions_field_enable'])) {
									$interval = $subscriptions[0]['pafe_stripe_subscriptions_interval'];
									$interval_count = $subscriptions[0]['pafe_stripe_subscriptions_interval_count'];

									if (!empty($interval) && !empty($interval_count)) {
										if (!empty($subscriptions[0]['pafe_stripe_subscriptions_amount_field_enable'])) {
											if (!empty($subscriptions[0]['pafe_stripe_subscriptions_amount_field'])) {
												$amount = floatval( pafe_get_field_value_stripe($subscriptions[0]['pafe_stripe_subscriptions_amount_field'], $fields) );
											}
										} else {
											if (!empty($subscriptions[0]['pafe_stripe_subscriptions_amount'])) {
												$amount = floatval( $subscriptions[0]['pafe_stripe_subscriptions_amount'] );
											}
										}

										if (!empty($subscriptions[0]['pafe_stripe_subscriptions_one_time_fee'])) {
											$one_time_fee = floatval( $subscriptions[0]['pafe_stripe_subscriptions_one_time_fee'] );
										}

										if (!empty($subscriptions[0]['pafe_stripe_subscriptions_cancel'])) {
											if (!empty($subscriptions[0]['pafe_stripe_subscriptions_cancel_add'])) {
												$cancel_after = '+ ' . $subscriptions[0]['pafe_stripe_subscriptions_cancel_add'] . $subscriptions[0]['pafe_stripe_subscriptions_cancel_add_unit'];
											}
										}
									}
								} else {
									if (!empty($form['settings']['pafe_stripe_subscriptions_field_enable'])) {
										$plan_value = pafe_get_field_value_stripe($form['settings']['pafe_stripe_subscriptions_field'], $fields);
										if (!empty($plan_value)) {
											foreach ($subscriptions as $subscription_item) {
												if (!empty($subscription_item['pafe_stripe_subscriptions_field_enable_repeater']) && !empty($subscription_item['pafe_stripe_subscriptions_field_value'])) {
													if ($plan_value == $subscription_item['pafe_stripe_subscriptions_field_value']) {
														$interval = $subscription_item['pafe_stripe_subscriptions_interval'];
														$interval_count = $subscription_item['pafe_stripe_subscriptions_interval_count'];
														if (!empty($interval) && !empty($interval_count)) {
															if (!empty($subscription_item['pafe_stripe_subscriptions_amount_field_enable'])) {
																if (!empty($subscription_item['pafe_stripe_subscriptions_amount_field'])) {
																	$amount = floatval( pafe_get_field_value_stripe($subscription_item['pafe_stripe_subscriptions_amount_field'], $fields) );
																}
															} else {
																if (!empty($subscription_item['pafe_stripe_subscriptions_amount'])) {
																	$amount = floatval( $subscription_item['pafe_stripe_subscriptions_amount'] );
																}
															}

															if (!empty($subscription_item['pafe_stripe_subscriptions_one_time_fee'])) {
																$one_time_fee = floatval( $subscription_item['pafe_stripe_subscriptions_one_time_fee'] );
															}

															if (!empty($subscription_item['pafe_stripe_subscriptions_cancel'])) {
																if (!empty($subscription_item['pafe_stripe_subscriptions_cancel_add'])) {
																	$cancel_after = '+ ' . $subscription_item['pafe_stripe_subscriptions_cancel_add'] . $subscription_item['pafe_stripe_subscriptions_cancel_add_unit'];
																}
															}
														}
													}
												}
											}
										}
									}
								}

								if ($currency != 'jpy') {
									$amount = $amount * 100;
								}
								
								if (!empty($amount) && !empty($interval) && !empty($interval_count)) {

									$plan_array = array(
										"amount" => $amount,
										"currency" => $currency,
										"interval" => $interval,
										"interval_count" => $interval_count,
										"metadata" => $fields_metadata,
										"product" => [
											"name" => $product_name,
											"metadata" => $fields_metadata,
										],
									);
									if (!empty($form['settings']['pafe_stripe_subscriptions_product_id'])) {
										$plan_array['product'] = $form['settings']['pafe_stripe_subscriptions_product_id'];
									}
									
									$plan = \Stripe\Plan::create( $plan_array );
									$customer_id = $customer->id;
									$plan_id = $plan->id;

									try {
										if (!empty($one_time_fee)) {
											if ($currency != 'jpy') {
												$one_time_fee = $one_time_fee * 100;
											}
											$one_time_fee_invoice = \Stripe\InvoiceItem::create([
												'amount' => $one_time_fee,
												'currency' => $currency,
												'customer' => $customer->id,
												'description' => 'One-time fee',
											]);
										}

										$subscription_array = array(
										'customer' => $customer_id,
										  'items' => array(
										  	array(
										  		'plan' => $plan_id,
										  	),
										),
										'expand' => array('latest_invoice.payment_intent'),
										'enable_incomplete_payments' => true,
										);

										if (!empty($cancel_after)) {
											$today = time();
											$subscription_array['cancel_at'] = strtotime($cancel_after, $today);
										}
										if(!empty($form['settings']['pafe_stripe_tax_rate_enable']) && !empty($form['settings']['pafe_stripe_tax_rate'])){
											$subscription_array['default_tax_rates'] = [
												replace_email_stripe($form['settings']['pafe_stripe_tax_rate'], $fields)
											];
										}
										$subscription = \Stripe\Subscription::create($subscription_array);
										$payment_status = $subscription->status;
										$payment_id = $subscription->id;
										$intent = $subscription->latest_invoice->payment_intent;
										\Stripe\PaymentIntent::update(
											$intent->id,
											['description' => replace_email_stripe($form['settings']['pafe_stripe_customer_description'], $fields)]
										);
										generatePaymentResponse($intent,true);
									} catch (\Stripe\Exception\ApiErrorException $e) {
										# Display error on client
										echo json_encode([
											'error' => $e->getMessage()
										]);
									}
								}
							}
						}
					}
				}

				if (!empty($_POST['payment_intent_id'])) {
					try {
						$intent = \Stripe\PaymentIntent::retrieve(
							$_POST['payment_intent_id']
						);
						if (!empty($form['settings']['pafe_stripe_customer_receipt_email'])) {
							\Stripe\PaymentIntent::update(
								$_POST['payment_intent_id'],
								['receipt_email' => replace_email_stripe($form['settings']['pafe_stripe_customer_receipt_email'], $fields)]
							);
						}
						generatePaymentResponse($intent);
					} catch (\Stripe\Exception\ApiErrorException $e) {
						# Display error on client
						echo json_encode([
							'error' => $e->getMessage()
						]);
					}	
				}
			}
		}

		wp_die();
	}
	function pafe_update_invoiceitems($data){
		$api_key = get_option('piotnet-addons-for-elementor-pro-stripe-publishable-key');
		$api_secret = get_option('piotnet-addons-for-elementor-pro-stripe-secret-key');
		$curl = curl_init();
		curl_setopt_array($curl, array(
		CURLOPT_URL => 'https://api.stripe.com/v1/invoiceitems',
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'POST',
		CURLOPT_POSTFIELDS => $data,
		CURLOPT_HTTPHEADER => array(
			'Authorization: Basic '.base64_encode($api_secret.':'.$api_key),
			'Content-Type: application/x-www-form-urlencoded',
		),
		));
		$response = curl_exec($curl);
		curl_close($curl);
		return $response;
	}
	function pafe_create_invoice($data){
		$curl = curl_init();
		curl_setopt_array($curl, array(
		CURLOPT_URL => 'https://api.stripe.com/v1/invoices',
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'POST',
		CURLOPT_POSTFIELDS => $data,
		CURLOPT_HTTPHEADER => array(
			'Authorization: Basic '.base64_encode(get_option('piotnet-addons-for-elementor-pro-stripe-secret-key').':'.get_option('piotnet-addons-for-elementor-pro-stripe-publishable-key')),
			'Content-Type: application/x-www-form-urlencoded',
		),
		));
		$response = curl_exec($curl);
		curl_close($curl);
		return $response;
	}

?>