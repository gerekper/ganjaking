<?php
/**
 * Fulfillment class for integrating with the Amazon Fulfillment Outbound API.
 *
 * TODO: 4.1.0 This class should implement all fulfillment calls to SP API.
 * TODO: It should also contain all helper functions that are needed in dealing with fulfillment orders.
 * TODO: Code will need to be moved / merged in here FROM class-ns-fba-outbound.php.
 * TODO: When all the relevant functionality is here and tested well, then class-ns-fba-outbound.php. can be deleted.
 * TODO: Code will need to be moved / merged in here FROM class-sp-fulfillment.php.
 * TODO: When all the relevant functionality is here and tested well, then class-sp-fulfillment.php.. can be deleted.
 *
 * @package NeverSettle\WooCommerce-Amazon-Fulfillment
 * @since 4.1.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'NS_MCF_Fulfillment' ) ) {

	/**
	 * Fulfillment class. For sending fulfillment orders and getting price / time estimates for orders.
	 */
	class NS_MCF_Fulfillment extends NS_MCF_Integration {

		/**
		 * Initialize class.
		 */
		public function init() {
			add_action( 'init', array( $this, 'maybe_init_sp_api' ) );
		}

		/**
		 * Initialize our local SP_API object.
		 */
		public function maybe_init_sp_api() {
			if ( ! $this->ns_fba->sp_api->is_initialized() && is_object( $this->ns_fba->wc_integration ) ) {
				$this->ns_fba->sp_api->init_api( $this->wc_integration->get_SP_API_options() );
			}
		}

		/**
		 * API Test
		 *
		 * @param string $path The api path.
		 *
		 * @return array
		 */
		public function test_api_connection( $path = '/fba/outbound/2020-07-01/fulfillmentOrders' ): array {
			/** TODO: Only used for manually testing specific paths.
			$path  = '/fba/inventory/v1/summaries?details=true&granularityType=Marketplace&granularityId=ATVPDKIKX0DER';
			$path .= '&marketplaceIds=ATVPDKIKX0DER';
			*/
			$response = $this->make_request( $path, 'TestApiConnection' );

			if ( SP_API::is_error_in( $response ) ) {
				$this->ns_fba->logger->add_entry( $response, 'wc' );
				return array(
					'success' => false,
					'message' => 'An error has occurred, please try again!',
				);
			}

			return array(
				'success'  => true,
				'message'  => 'Success',
				'response' => json_decode( $response['body'] ),
			);
		}

		/**
		 * Toggle product fulfill.
		 *
		 * @param array $products List of WooCommerce products.
		 */
		public function toggle_fulfill( $products ) {
			// Receives an array with the products where ns_fba_is_fulfill meta needs to be updated.
			foreach ( $products as $wc_product_info ) {
				$wc_product_id      = (int) $wc_product_info['wc_product_id'];
				$wc_product_fulfill = $wc_product_info['ns_fba_fulfill'];
				if ( $wc_product_id > 0 && in_array( $wc_product_fulfill, array( 'yes', 'no' ), true ) ) {
					update_post_meta( $wc_product_id, 'ns_fba_is_fulfill', $wc_product_fulfill );
				}
			}
		}

		/**
		 * Get the fulfillment orders preview from Seller Partner.
		 *
		 * @param array $package The package details.
		 * @param array $shippingSpeedCategories The shipping categories.
		 */
		public function get_fulfillment_orders_preview( $package, $shippingSpeedCategories ) { // phpcs:ignore WordPress.NamingConventions

			// This is just for testing with sandbox.
			// $path = '/fba/outbound/2020-07-01/fulfillmentOrders/preview';
			// $request_body = '{ "address" : { "addressLine1" : "1234 Amazon Way", "city" : "Troy", "countryCode" : "US", "name" : "Amazon", "postalCode" : "48084", "stateOrRegion" : "MI" }, "featureConstraints" : [ { "featureFulfillmentPolicy" : "Required", "featureName" : "BLANK_BOX" }, { "featureFulfillmentPolicy" : "Required", "featureName" : "BLOCK_AMZL" } ], "items" : [ { "quantity" : 1, "sellerFulfillmentOrderItemId" : "OrderItemID2", "sellerSku" : "PSMM-TEST-SKU-Jan-21_19_39_23-0788" } ], "marketplaceId" : "ATVPDKIKX0DER", "shippingSpeedCategories" : [ "Standard" ] }';
			// return $this->ns_fba->sp_api->make_request( $path, $type, $qty, $method, $json_encoded_body );->make_sandbox_requests( $path, 'POST', $request_body ); .

			$blank_box  = $this->ns_fba->wc_integration->get_option( 'ns_fba_fulfillment_ship_blank_box' );
			$block_amzl = $this->ns_fba->wc_integration->get_option( 'ns_fba_fulfillment_ship_amzl' );

			// We need to hard set some of these values to get shipping previews.
			// The reason is that they won't likely be available from the customer yet at the cart step especially.
			// And the WooCommerce shipping estimate form only collects Country, City, Postal code.
			// But the SP API also requires name and address line 1 which are not optional.
			$body                             = array();
			$body['address']                  = array();
			$body['address']['name']          = 'WooCommerce';
			$body['address']['addressLine1']  = ( '' === $package['destination']['address'] ) ? 'Customer Address' : $package['destination']['address'];
			$body['address']['city']          = ( '' === $package['destination']['city'] ) ? 'Customer City' : $package['destination']['city'];
			$body['address']['stateOrRegion'] = ( '' === $package['destination']['state'] ) ? 'Customer State' : $package['destination']['state'];
			$body['address']['postalCode']    = ( '' === $package['destination']['postcode'] ) ? 'Customer Postal Code' : $package['destination']['postcode'];
			$body['address']['countryCode']   = ( '' === $package['destination']['country'] ) ? 'Customer Country' : $package['destination']['country'];

			if ( 'yes' === $blank_box ) {
				$body['featureConstraints'][] = array(
					'featureFulfillmentPolicy' => 'Required',
					'featureName'              => 'BLANK_BOX',
				);
			}

			if ( 'yes' === $block_amzl ) {
				$body['featureConstraints'][] = array(
					'featureFulfillmentPolicy' => 'Required',
					'featureName'              => 'BLOCK_AMZL',
				);
			}

			$body['items'] = array();

			foreach ( $package['contents'] as $item ) {

				if ( isset( $item['variation_id'] ) && $item['variation_id'] > 0 ) {
					$product_id = $item['variation_id'];
				} else {
					$product_id = $item['product_id'];
				}

				$product = wc_get_product( $product_id );

				if ( ! $product ) {
					continue;
				}

				$current_sku = $this->ns_fba->utils->get_sku_to_send( $product );

				$body['items'][] = array(
					'quantity'                     => $item['quantity'],
					'sellerSku'                    => $current_sku,
					'sellerFulfillmentOrderItemId' => uniqid( 'id_' . $product_id . '_' ),
				);
			}

			if ( empty( $body['items'] ) ) {
				// Dont send for shipping calculations if no items.
				return new WP_Error( 'failed', __( 'No items found to send to calculate shipping.', $this->ns_fba->text_domain ) );
			}

			$body['marketplaceId'] = $this->ns_fba->wc_integration->get_option( 'ns_fba_marketplace_id' );

			$body['shippingSpeedCategories'] = $shippingSpeedCategories; // phpcs:ignore WordPress.NamingConventions

			$request_body = wp_json_encode( $body );

			$path = '/fba/outbound/2020-07-01/fulfillmentOrders/preview';
			return $this->make_request( $path, 'GetFulfillmentOrdersPreview', 0, 'POST', $request_body );
		}

		/**
		 * Post an order in Seller Partner
		 *
		 * @param WC_Order $order The order to be sent to Seller Partner.
		 * @param bool     $is_manual_send If its manual send. Defaults to false.
		 *
		 * @throws Exception If an error occurs.
		 *
		 * @return false|void
		 */
		public function post_fulfillment_order( WC_Order $order, $is_manual_send = false ) {
			$order_id     = $order->get_id();
			$order_number = $this->ns_fba->get_option( 'ns_fba_order_prefix' ) . $order->get_order_number();

			if ( ! NS_MCF_Utils::is_valid_length( $order_number, 40 ) ) {
				$this->write_debug_log( $log_tag, 'Order number longer than the required 40 characters. Removing prefix defined in settings' );
				$order_number = $order->get_order_number();
			}

			$log_tag = 'post_fulfillment_order: ';

			$this->write_debug_log( $log_tag, 'Starting post_fulfillment_order' );

			try {

				$this->ns_fba->logger->add_entry( '<b>Step 2 of 8: </b>INSIDE post_fulfillment_order AND INSIDE try<br /><br />', 'debug', $this->ns_fba->debug_log_path );

				if ( ! $is_manual_send && 'yes' === $this->ns_fba->wc_integration->get_option( 'ns_fba_manual_only_mode' ) ) {
					$this->write_debug_log( $log_tag, 'Skipping fulfillment' );
					$order->add_order_note( __( 'Order was not sent to Amazon because Manual Only mode is on.', $this->ns_fba->text_domain ) );
					return false;
				}

				// Validate if exist at least 1 product with Amazon Fulfillment Enable.
				$fulfill_item_count = 0;
				$total_item_count   = 0;
				$digital_item_count = 0;

				$order_data = $order->get_data();

				$body  = array();
				$items = array();

				// Comma-separated list of fulfilled skus to update inventory levels on later.
				$fba_skus = '';

				// Get from option table. Placing in the loop is a heavy database call.
				$currency_code = get_woocommerce_currency();

				foreach ( $order->get_items() as $item ) {

					$total_item_count++;

					// phpcs:ignore
					// $product = wc_get_product( $item->get_data()['product_id'] );
					$product = $item->get_product();

					if ( ! is_a( $product, 'WC_Product' ) ) {
						continue;
					}

					// set a local int product id.
					// phpcs:ignore
					// $product_id = intval( $item['product_id'] );
					$product_id = intval( $product->get_id() );

					// Check for virtual items and skip.
					$is_order_item_virtual = $this->ns_fba->utils->is_order_item_virtual( $item, $product_id );
					if ( $is_order_item_virtual ) {
						$digital_item_count++;
						continue;
					}

					// run through the Order Level smart fulfillment rules.
					$is_order_item_fba = $this->ns_fba->utils->is_order_item_amazon_fulfill( $order, $item, $product, $product_id, $is_manual_send );

					if ( ! $is_order_item_fba ) {
						/**
						 * Add filter to manually add extra items to the fulfilment order items.
						 * This can be used for bundled or grouped products that have inner products that should be fulfiled and are skipped.
						 *
						 * @param array         $items The current items to be sent.
						 * @param WC_Order_Item $item  The Order item.
						 * @param WC_Product    $product The WooCommerce Product.
						 * @param int           $product_id The product id.
						 * @param WC_Order      $order The order.
						 * @param string        $order_number The order number.
						 *
						 * @return array $items
						 */
						$items = apply_filters( 'ns_fba_fulfilment_order_fulfilment_items', $items, $item, $product, $product_id, $order, $order_number );
						continue;
					}

					$current_sku = $this->ns_fba->utils->get_sku_to_send( $product );

					$fulfill_item_count++;

					$fulfilment_sku = NS_MCF_Utils::get_fulfilment_sku( $order_number, $fulfill_item_count, $current_sku );

					// phpcs:ignore
					// error_log( "post_fulfillment_order SENDING to Amazon the SKU: " . $current_sku );

					// sellerFulfillmentOrderItemId must be a Seller recognizable ID like SKU.
					// Otherwise, error messages with other values won't make sense.
					// We use $fulfill_item_count + 1 because it is 0 on the first pass.
					$items[] = array(
						'quantity'                     => $item->get_quantity(),
						'sellerFulfillmentOrderItemId' => $fulfilment_sku,
						'sellerSku'                    => $current_sku,
						'perUnitDeclaredValue '        => array(
							'currencyCode ' => $currency_code,
							'value '        => floatval( $product->get_price() ),
						),
					);
					
					if ( '' === $fba_skus ) {
						$fba_skus = $current_sku;
					} else {
						$fba_skus .= ',' . $current_sku;
					}
				}

				if ( $this->ns_fba->is_debug ) {
					$this->ns_fba->logger->add_entry( '<b>Step 3 of 8: </b>AFTER checking all items in order<br />', 'debug', $this->ns_fba->debug_log_path );
					if ( $this->ns_fba->utils->isset_on( $this->ns_fba->get_option( 'ns_fba_perfection_mode', 'no' ) ) ) {
						$this->ns_fba->logger->add_entry( '----: Perfection Mode is ACTIVE<br />', 'debug', $this->ns_fba->debug_log_path );
					} else {
						$this->ns_fba->logger->add_entry( '----: Perfection Mode is OFF<br />', 'debug', $this->ns_fba->debug_log_path );
					}
					$this->ns_fba->logger->add_entry( '----: Total Item Count = ' . $total_item_count . '<br />', 'debug', $this->ns_fba->debug_log_path );
					$this->ns_fba->logger->add_entry( '----: Fulfill Item Count = ' . $fulfill_item_count . '<br /><br />', 'debug', $this->ns_fba->debug_log_path );
				}

				if ( 0 === $fulfill_item_count ) {
					// if there are zero FBA order items then we're done.
					$this->write_debug_log( $log_tag, 'Zero products detected for fulfillment' );
					return false;
				}

				if ( ! $is_manual_send && $this->ns_fba->utils->isset_on( $this->ns_fba->get_option( 'ns_fba_perfection_mode', 'no' ) ) && $total_item_count !== $fulfill_item_count ) {
					// if we're in Perfection Mode but not ALL of the order items passed fulfillment rules, then we're done.
					$this->write_debug_log( $log_tag, 'Perfection Mode is active and Not ALL products in this order are set to fulfill with FBA' );
					$order->add_order_note( __( 'Perfection Mode is active and Not ALL products in this order are set to Fulfill with Amazon, so we did not try to send this order to FBA.', $this->ns_fba->text_domain ) );
					return false;
				}

				if ( ! $this->ns_fba->utils->is_order_amazon_fulfill( $order, $is_manual_send ) ) {
					// we must make sure there are no order-level rules that will prevent this order from being sent
					// run through the Order Level smart fulfillment rules.

					// this generic condition should never happen because we should be throwing all exceptions back from is_order_amazon_fulfill()
					// but just in case...
					return new WP_Error( 'failed', __( 'The Order cannot be sent to FBA due to the configured settings or products.', $this->ns_fba->text_domain ) );
				}

				// let's send some stuff to FBA!!!

				$this->ns_fba->logger->add_entry( '<b>Step 4 of 8: </b>BEFORE new Fulfillment Order configured<br /><br />', 'debug', $this->ns_fba->debug_log_path );

				$this->write_debug_log( $log_tag, 'Preparing request' );

				$order_comment = $this->ns_fba->wc_integration->get_option( 'ns_fba_order_comment' );
				if ( ! NS_MCF_Utils::is_valid_length( $order_comment, 1000 ) ) {
					$this->write_debug_log( $log_tag, 'Order comment longer than 100 characters. Defaulting to the default comment' );
					$order_comment = 'Thank you for your order!';
				}

				$body['marketplaceId']            = $this->ns_fba->wc_integration->get_option( 'ns_fba_marketplace_id' );
				$body['sellerFulfillmentOrderId'] = $order_number;
				$body['displayableOrderId']       = $order_number;
				$body['displayableOrderDate']     = $order_data['date_created']->format( 'Y-m-d\TH:i:s.000\Z' );
				$body['displayableOrderComment']  = $order_comment;

				// Make SURE we have a non-blank displayableOrderComment otherwise the submission will fail.
				if ( '' === $body['displayableOrderComment'] ) {
					$body['displayableOrderComment'] = 'Thank you for your order!';
				}

				// Billing is the address data source by default.
				// Shipping has data only if "Ship to a different address?" is checked.
				$address_source = empty( $order_data['shipping']['first_name'] ) ? $order_data['billing'] : $order_data['shipping'];

				$body['destinationAddress']                  = array();
				$body['destinationAddress']['name']          = $this->get_formatted_string( $address_source['first_name'] ) . ' ' .
																$this->get_formatted_string( $address_source['last_name'] );
				$body['destinationAddress']['addressLine1']  = $this->get_formatted_string( $address_source['address_1'] );
				$body['destinationAddress']['addressLine2']  = $this->get_formatted_string( $address_source['address_2'] );
				$body['destinationAddress']['city']          = $this->get_formatted_string( $address_source['city'] );
				$body['destinationAddress']['countryCode']   = $this->get_formatted_string( $address_source['country'] );
				$body['destinationAddress']['postalCode']    = $this->get_formatted_string( $address_source['postcode'] );
				$body['destinationAddress']['stateOrRegion'] = $this->get_formatted_string( $address_source['state'] );

				// phone and email need to come from billing.
				if ( ! $this->ns_fba->utils->isset_on( $this->ns_fba->get_option( 'ns_fba_exclude_phone', 'no' ) ) ) {
					$body['destinationAddress']['phone'] = $this->get_formatted_string( $order_data['billing']['phone'] );
				} else {
					$body['destinationAddress']['phone'] = '';
				}

				// Amazon shipping notification list.
				$notify = array();
				if ( $order->get_billing_email() && ! $this->ns_fba->utils->isset_on( $this->ns_fba->get_option( 'ns_fba_disable_shipping_email', 'no' ) ) ) {
					$notify = array( $order_data['billing']['email'] );
				}
				// Add the admin email.
				if ( $this->ns_fba->utils->isset_on( $this->ns_fba->get_option( 'ns_fba_email_on_error', 'no' ) ) ) {
					if ( !empty( $this->ns_fba->get_option( 'ns_fba_notify_email' ) ) ) {
						$notify = $notify + array( $this->ns_fba->get_option( 'ns_fba_notify_email' ) );
					}
				}

				$body['notificationEmails'] = $notify;

				$body['items'] = $items;

				$feature_blank_box = $this->ns_fba->wc_integration->get_option( 'ns_fba_fulfillment_ship_blank_box' );
				$feature_amzl      = $this->ns_fba->wc_integration->get_option( 'ns_fba_fulfillment_ship_amzl' );

				if ( 'yes' === $feature_blank_box ) {
					$body['featureConstraints'][] = array(
						'featureFulfillmentPolicy' => 'Required',
						'featureName'              => 'BLANK_BOX',
					);
				}

				if ( 'yes' === $feature_amzl ) {
					$body['featureConstraints'][] = array(
						'featureFulfillmentPolicy' => 'Required',
						'featureName'              => 'BLOCK_AMZL',
					);
				}

				$body['shippingSpeedCategory'] = $this->get_shipping_speed_category( $order );

				$body['fulfillmentPolicy'] = $this->ns_fba->wc_integration->get_option( 'ns_fba_fulfillment_policy' );

				$request_body = wp_json_encode( $body );

				$this->write_debug_log( $log_tag, $request_body );

				$this->ns_fba->logger->add_entry( '<b>Step 5 of 8: </b>AFTER new Fulfillment Order configured<br /><br />', 'debug', $this->ns_fba->debug_log_path );
				$this->ns_fba->logger->add_entry( '<b>Step 6 of 8: </b>INSIDE try sending request to FBA<br /><br />', 'debug', $this->ns_fba->debug_log_path );

				$path     = '/fba/outbound/2020-07-01/fulfillmentOrders';
				$response = $this->make_request(
					$path,
					'CreateFulfillmentOrderRequest',
					$fulfill_item_count,
					'POST',
					$request_body
				);

				$this->write_debug_log( $log_tag, 'Response received' );

				// Before we handle the response, use the opportunity to sync inventory even if there was an error.
				// Tracking array for inventory updates.
				$inventory_updates = array();
				$inventory_data    = $this->get_inventory_summaries( '', $fba_skus );
				// Update the inventory numbers if sync is enabled.
				if ( $this->ns_fba->utils->isset_on( ( $this->ns_fba->get_option( 'ns_fba_update_inventory', 'no' ) ) ) ) {
					// phpcs:disable WordPress.NamingConventions.ValidVariableName
					foreach ( $inventory_data->payload->inventorySummaries as $inventorySummary ) {
						// TODO: check for errors.
						$fba_sku        = $inventorySummary->sellerSku;
						$stock          = $inventorySummary->inventoryDetails->fulfillableQuantity;
						$woo_product    = $this->ns_fba->utils->get_product_by_sku( $fba_sku );
						$message_prefix = 'WC Order ' . $order_number . ' >>';
						$this->ns_fba->inventory->set_product_stock( $woo_product, $fba_sku, $message_prefix, $stock );
						// TODO: something with this array of logged inventory updates.
						array_push( $inventory_updates, 'Updated ' . $fba_sku . ' stock to ' . $stock );
					}
				} else {
					array_push( $inventory_updates, __( 'Inventory sync is not enabled in the settings.', $this->ns_fba->text_domain ) );
				}

				if ( ! SP_API::is_error_in( $response ) ) {
					$this->write_debug_log( $log_tag, $response['body'] );

					if ( $this->ns_fba->utils->isset_on( $this->ns_fba->get_option( 'ns_fba_automatic_completion', 'no' ) ) ) {
						$ns_wc_term = 'completed';
						$this->write_debug_log( $log_tag, 'Setting for Mark Orders Complete active. Order Status set to Completed.' );
					} elseif ( $fulfill_item_count === $total_item_count || ( ( $digital_item_count + $fulfill_item_count ) === $total_item_count ) ) {
						$ns_wc_term = 'sent-to-fba';
						$this->write_debug_log( $log_tag, 'All items fulfilled with FBA. Order Status set to Sent to FBA' );
					} else {
						$ns_wc_term = 'part-to-fba';
						$this->write_debug_log( $log_tag, 'Some items fulfilled with FBA. Order Status set to Partial to FBA' );
					}

					$this->set_order_status_safe( $order_id, $ns_wc_term );

					// Create the log of order info.
					$this->ns_fba->logger->add_entry( 'LOG for Order: ' . $order_id . ' @ ' . wp_date( 'Y-m-d H:i:s' ) . '<br><br>', 'info', $this->ns_fba->log_path );
					$this->ns_fba->logger->add_entry( 'REQUEST: <br><pre>' . print_r( json_decode( $request_body, true ), true ) . '</pre><br><br>', 'info', $this->ns_fba->log_path );
					$this->ns_fba->logger->add_entry( 'RESPONSE: <br><pre>' . print_r( $response, true ) . '</pre><br><br>', 'info', $this->ns_fba->log_path );

					$order->add_order_note( '<a href="' . $this->ns_fba->log_url . '" target="_blank">' . __( 'Successfully submitted order to FBA', $this->ns_fba->text_domain ) . '</a> (' . __( 'click for full log', $this->ns_fba->text_domain ) . ').' );

					$order->update_meta_data( '_sent_to_fba', gmdate( 'm-d-Y H:i:s', time() ) );

					$this->write_debug_log( $log_tag, '_sent_to_fba has been updated' );

					$order->save();

					return $order_id;

				} else {
					if ( is_wp_error( $response ) ) {
						$error_message = $response->get_error_messages();
					} else {
						// TODO: finish handling error responses from Amazon.
						$error_message = json_decode( $response['body'], true );
					}

					$this->write_debug_log( $log_tag, "In ERROR: error_message:\n" . print_r( $error_message, true ) ); // phpcs:ignore
					$this->ns_fba->logger->add_entry( "ERROR LOG for Order: " . $order_id . " @ " . wp_date("Y-m-d H:i:s") . "<br><br>",'info', $this->ns_fba->err_log_path ); // phpcs:ignore
					$this->ns_fba->logger->add_entry( "Original REQUEST: <br><pre>" . print_r( json_decode( $request_body, true ), true ) . "</pre><br><br>",'info', $this->ns_fba->err_log_path ); // phpcs:ignore
					$this->ns_fba->logger->add_entry( "Final RESPONSE: <br><pre>" . print_r( $response, true ) . "</pre><br><br>",'info', $this->ns_fba->err_log_path ); // phpcs:ignore
					$this->set_order_status_safe( $order_id, 'fail-to-fba' );
					$order->add_order_note(
						'<a href="' . $this->ns_fba->err_log_url . '" target="_blank">' .
						__( 'Failed to submit order to FBA', $this->ns_fba->text_domain ) . '</a> (' .
						__( 'click for full log', $this->ns_fba->text_domain ) . '). <b>' .
						__( 'Error Message:', $this->ns_fba->text_domain ) . '</b><br /><span style="color:#a00000;">' .
						$error_message['errors'][0]['message'] . '</span>'
					);

					return new WP_Error( 'failed', $error_message['errors'][0]['message'] );
				}
			} catch ( Exception $ex ) {
				$this->write_debug_log( $log_tag, $ex->getMessage() );
				return new WP_Error( 'failed', $ex->getMessage() );
			}
		}

		/**
		 * Check if the order has already been sent to amazon and update its status.
		 *
		 * @param int $order_id The order id.
		 */
		public function check_post_fulfillment_order( $order_id ) {
			$order        = wc_get_order( $order_id );
			$already_sent = get_post_meta( $order_id, '_sent_to_fba', true );
			if ( ! $already_sent ) {
				$this->post_fulfillment_order( $order );
			} else {
				// Update status back from processing back to completed if order was already sent.
				// This is because PayPal orders can get set back to processing by IPN *after* order has already been sent and marked complete.
				// TODO add checking for part-to-fba for full integrity - this could go from part-to-fba to sent-to-fba.
				if ( 'processing' === $order->get_status() ) {
					$complete_status = $this->ns_fba->utils->isset_on( $this->ns_fba->get_option( 'ns_fba_automatic_completion', 'no' ) ) ? 'completed' : 'sent-to-fba';
					$this->set_order_status_safe( $order_id, $complete_status );
					$order->add_order_note( __( 'Skipping order completion via Amazon: this order already sent.', $this->ns_fba->text_domain ) );
				}
			}
		}

		/**
		 * Get the Selling Partner fulfillment order response
		 *
		 * @param   int $order_id  The order id.
		 *
		 * @return array|false|WP_Error
		 */
		public function get_fulfillment_order( int $order_id ) {
			$log_tag      = 'get_fulfillment_order: ' . $order_id;
			$order        = wc_get_order( $order_id );
			$order_number = $this->ns_fba->get_option( 'ns_fba_order_prefix' ) . $order->get_order_number();

			// For manual testing only.
			// $order_number = 'ns_test_wc_order_PXvpRxjZ0bMr7'; //phpcs:ignore.
			$path = '/fba/outbound/2020-07-01/fulfillmentOrders/' . $order_number;

			// The OLD new way of doing it before going back to straight WC Order number
			// $order_pre = $this->ns_fba->wc_integration->get_option( 'ns_fba_order_prefix' ); //phpcs:ignore.
			// $path      = '/fba/outbound/2020-07-01/fulfillmentOrders/' . $order_pre . $order->get_order_key(); //phpcs:ignore.
			$fulfillment_response = $this->make_request( $path, 'GetFulfillmentOrder' );

			if ( SP_API::is_error_in( $fulfillment_response ) ) {
				$this->ns_fba->logger->add_entry( $fulfillment_response, 'wc', '_fulfillment' );
				$this->write_debug_log( $log_tag, 'Fulfillment order response returns an error' );
				return false;
			}

			return $fulfillment_response;
		}

		/**
		 * Get the tracking information for an order viewed in customer My Account and add an order note with the info.
		 *
		 * @param int $order_id The order id.
		 *
		 * @return void
		 */
		public function get_fulfillment_order_shipping_info( $order_id ) {

			// Check for order post meta _sent_to_fba so that we don't look up orders that were never sent to Amazon.
			if ( empty( get_post_meta( $order_id, '_sent_to_fba', true ) ) ) {
				// since this is a hook that gets displayed we need to return nothing.
				return;
			}

			$order = wc_get_order( $order_id );

			try {
				$fulfillment_response = $this->get_fulfillment_order( $order_id );
				$fulfillment_json     = json_decode( $fulfillment_response['body'], true );

				if ( ! $fulfillment_response || empty( $fulfillment_json['payload']['fulfillmentShipments'] ) ) {
					// Add an admin note that the tracking info could not be retrieved.
					$order->add_order_note( 'Order tracking info currently unavailable or unable to be retrieved.' );
					return;
				}

				$order_note_open  = '<section class="woocommerce-order-fulfillment"><div>';
				$order_note_title = '<h2>Order Tracking Info</h2>';
				$order_note_close = '</div></section>';
				$order_note_body  = '';
				$order_note       = '';

				// phpcs:disable WordPress.NamingConventions.ValidVariableName
				foreach ( $fulfillment_json['payload']['fulfillmentShipments'] as $fulfillmentShipment ) {
					// If a $fulfillmentShipment['fulfillmentShipmentStatus'] = 'PENDING' then it won't have a Package # yet.
					// But we can do a temporary status return even immediately when an order is created.

					if ( 'PENDING' === $fulfillmentShipment['fulfillmentShipmentStatus'] ) {
						$package_status  = $fulfillmentShipment['fulfillmentShipmentStatus'];
						$package_ship_id = $fulfillmentShipment['amazonShipmentId'];
						$package_arrival = wp_date( 'Y-m-d H:i:s', ( new DateTime( $fulfillmentShipment['estimatedArrivalDate'] ) )->getTimestamp() );

						$order_note_body .= '<p>';
						$order_note_body .= '<b>' . __( 'Shipment Status:   ', $this->ns_fba->text_domain ) . '</b>' . $package_status . '<br />';
						$order_note_body .= '<b>' . __( 'Amazon ShipmentID: ', $this->ns_fba->text_domain ) . '</b>' . $package_ship_id . '<br />';
						$order_note_body .= '<b>' . __( 'Estimated Arrival: ', $this->ns_fba->text_domain ) . '</b>' . $package_arrival . '<br />';
						$order_note_body .= '</p>';
						$order_note       = $order_note_open . $order_note_title . $order_note_body . $order_note_close;

					} else {
						// The fulfillmentShipmentPackage is an array within the fulfillmentShipment array.
						// We need to foreach the packages in case there are more than one. Not sure if there every will be.

						foreach ( $fulfillmentShipment['fulfillmentShipmentPackage'] as $package_details ) {
							$package_status   = $fulfillmentShipment['fulfillmentShipmentStatus'];
							$package_number   = $package_details['packageNumber'];
							$package_carrier  = $package_details['carrierCode'];
							$package_tracking = '<a href="https://www.swiship.com/track?id=' . $package_details['trackingNumber'] . '" target="_blank">' . $package_details['trackingNumber'] . '</a>';
							$package_arrival  = wp_date( 'Y-m-d H:i:s', ( new DateTime( $package_details['estimatedArrivalDate'] ) )->getTimestamp() );

							/** If we want to add full tracking timeline later this is how we'd do it.
							 * // phpcs:ignore
							 * // $path = '/fba/outbound/2020-07-01/tracking?packageNumber=' . $package_number;
							 * $this->write_debug_log( $log_tag, 'Getting tracking information for package number: ' . $package_number );
							 * $tracking_response = $this->make_request( $path, 'GetFulfillmentOrderShippingInfo' );
							 *
							 * if ( SP_API::is_error_in( $tracking_response ) ) {
							 * $this->write_debug_log( $log_tag, "Tracking info response includes an error.\n" .   // phpcs:ignore
							 * print_r( $tracking_response, true ) );                                              // phpcs:ignore
							 * continue;
							 * }
							 */

							$order_note_body .= '<p>';
							$order_note_body .= '<b>' . __( 'Shipment Status:   ', $this->ns_fba->text_domain ) . '</b>' . $package_status . '<br />';
							$order_note_body .= '<b>' . __( 'Package Number:    ', $this->ns_fba->text_domain ) . '</b>' . $package_number . '<br />';
							$order_note_body .= '<b>' . __( 'Shipping Carrier:  ', $this->ns_fba->text_domain ) . '</b>' . $package_carrier . '<br />';
							$order_note_body .= '<b>' . __( 'Tracking Number:   ', $this->ns_fba->text_domain ) . '</b>' . $package_tracking . '<br />';
							$order_note_body .= '<b>' . __( 'Estimated Arrival: ', $this->ns_fba->text_domain ) . '</b>' . $package_arrival . '<br />';
							$order_note_body .= '</p>';
							$order_note       = $order_note_open . $order_note_title . $order_note_body . $order_note_close;
						}
					}
				}

				if ( ! empty( $order_note ) ) {
					$order->add_order_note( $order_note );
					// Write the output to the page as the Hook is expecting to do.
					print_r( $order_note ); // phpcs:ignore
				}

				// phpcs:enable WordPress.NamingConventions.ValidVariableName
			} catch ( Exception $ex ) {
				// we still need to complete the hook.
				print_r( '<h2>Order Tracking Info</h2>Currently Unavailable.', false ); //phpcs:ignore
			}
		}

		/**
		 * Sync fulfilment order status.
		 */
		public function sync_fulfillment_order_status() {

			$this->write_debug_log( 'DEBUG', 'Starting to sync fulfillment order status' );

			$orders = wc_get_orders(
				array(
					'numberposts' => 20,
					'order'       => 'ASC',
					'return'      => 'ids',
					'post_type'   => wc_get_order_types(),
					'status'      => array( 'wc-sent-to-fba', 'wc-part-to-fba' ),
				)
			);

			if ( empty( $orders ) ) {
				return;
			}

			$this->ns_fba->logger->add_entry( 'sync_fulfillment_order_status', 'wc', '_order_sync' );
			$this->ns_fba->logger->add_entry( $orders, 'wc', '_order_sync' );

			foreach ( $orders as $order_id ) {
				$order = wc_get_order( $order_id );
				if ( ! $order ) {
					$this->ns_fba->logger->add_entry( 'Invalid order ' . $order_id, 'wc', '_order_sync' );
					continue;
				}

				$order_number = $this->ns_fba->get_option( 'ns_fba_order_prefix' ) . $order->get_order_number();

				$log_tag = 'Order ' . $order_id;

				// phpcs:ignore
				// error_log( "order_id = " . $order_id );

				try {

					$this->write_debug_log( $log_tag, 'Getting fulfillment order' );

					$fulfillment_response = $this->get_fulfillment_order( $order_id );
					if ( ! $fulfillment_response || SP_API::is_error_in( $fulfillment_response ) ) {
						$this->write_debug_log( $log_tag, 'Response is not readable' );
						continue;
					}
					$fulfillment_json     = json_decode( $fulfillment_response['body'], true );
					$fulfillment_status   = $fulfillment_json['payload']['fulfillmentOrder']['fulfillmentOrderStatus'];

					if ( empty( $fulfillment_status ) ) {
						$this->write_debug_log( $log_tag, 'Response is not readable' );
						continue;
					}

					$this->write_debug_log( $log_tag, 'Updating fulfillment order status to ' . $fulfillment_status );

					// update the order status based on the FBA status returned.
					switch ( $fulfillment_status ) {
						case 'Cancelled':
							$this->set_order_status_safe( $order, 'cancelled' );
							$order->add_order_note( 'Synced fulfillment status with FBA and found CANCELLED. Updated order status from Sent to FBA to Cancelled.' );
							break;
						case 'Complete':
							$this->set_order_status_safe( $order, 'completed' );
							$order->add_order_note( 'Synced fulfillment status with FBA and found COMPLETE. Updated order status from Sent to FBA to Completed.' );
							break;
						case 'CompletePartialled':
							$this->set_order_status_safe( $order, 'completed' );
							$order->add_order_note( 'Synced fulfillment status with FBA and found COMPLETE_PARTIALLED. Updated order status from Sent to FBA to Completed.' );
							break;
					}

					if ( $this->ns_fba->utils->isset_on( $this->get_option( 'ns_fba_display_order_tracking', 'no' ) ) ) {
						$this->get_fulfillment_order_shipping_info( $order_id );
					}

				} catch ( Exception $ex ) {
					$this->write_debug_log( $log_tag, $ex->getMessage() );

					echo esc_html( $order_number . '	' . ' FBA status err: ' . $ex->getMessage() . '<br />' );

					$order_note = '<a href="' . $this->ns_fba->err_log_url . '" target="_blank">' . __( 'Failed to sync order status from FBA', $this->ns_fba->text_domain ) . '</a> (' . __( 'click for full log', $this->ns_fba->text_domain ) . '). <b>' . __( 'Error Message:', $this->ns_fba->text_domain ) . '</b><br /><span style="color:red;">' . $ex->getMessage() . '</span>';
					$order->add_order_note( $order_note );
				}

				$order->save();
			}

			if ( $this->ns_fba->utils->isset_on( $this->ns_fba->get_option( 'ns_fba_sync_ship_retry', 'no' ) ) ) {

				$this->write_debug_log( 'DEBUG', 'Trying to resend orders' );

				// We should consider a direct query to avoid loading all data.
				$failed_orders = wc_get_orders(
					array(
						'numberposts'  => 20,
						'order'        => 'ASC',
						'return'       => 'ids',
						'post_type'    => wc_get_order_types(),
						'status'       => array( 'wc-fail-to-fba' ),
						'meta_key'     => 'ns_fba_retried',
						'meta_compare' => 'NOT EXISTS',
					)
				);

				foreach ( $failed_orders as $order_id ) {

					$order = wc_get_order( $order_id );
					if ( ! $order ) {
						continue;
					}

					if ( ! $order->get_meta( 'ns_fba_retried' ) ) {

						$this->write_debug_log( 'Order ' . $order_id, 'Trying to resend order' );
						$order->add_meta_data( 'ns_fba_retried', time() );
						$order->add_order_note( 'Retrying to submit order to Amazon' );
						$order->save();
						$this->post_fulfillment_order( $order );
					}
				}
			}
		}

		/**
		 * Set the order status.
		 *
		 * @param int|object $order_id The order id or the order obect.
		 * @param string     $status The order status.
		 */
		private function set_order_status_safe( $order_id, $status ) {
			if ( is_int( $order_id ) ) {
				$order  = wc_get_order( $order_id );
			} else {
				$order  = $order_id;
			}

			if ( ! $order ) {
				return;
			}
			
			$status = apply_filters( 'ns_fba_order_status', $status, $order_id );
			$order->update_status( $status, 'WooCommerce Amazon Fulfillment: ', true );
		}

		/**
		 * Makes a request to the selling partner for inventory summaries.
		 *
		 * @param string $next_token Optional next token.
		 * @param string $skus Optional comma-separated list of skus.
		 *
		 * @return object|false
		 */
		public function get_inventory_summaries( $next_token = '', $skus = '' ) {
			// TODO: woocommerce-setting-integration::inventory_test_results() should be using get_inventory_summaries().

			$marketplace_id = $this->ns_fba->wc_integration->get_option( 'ns_fba_marketplace_id' );
			$path           = '/fba/inventory/v1/summaries?details=true&granularityType=Marketplace&granularityId=' .
							$marketplace_id . '&marketplaceIds=' . $marketplace_id;

			if ( '' !== $skus ) {
				$path .= '&sellerSkus=' . rawurlencode( $skus );
			}

			if ( '' !== $next_token ) {
				$path .= '&nextToken=' . urlencode_deep( $next_token );
			}
			$response = $this->make_request( $path, 'GetInventorySummaries' );

			if ( SP_API::is_error_in( $response ) ) {
				$this->ns_fba->logger->add_entry( $response, 'wc', '_fulfillment' );
				return false;
			}

			$body = json_decode( $response['body'] );

			if ( isset( $body->error ) ) {
				return false;
			}

			return $body;
		}

		/**
		 * Get a list of all SKUs of products that are set to sync.
		 *
		 * @param int $offset The page offset.
		 *
		 * @return array
		 */
		public function get_sync_product_skus( $offset = 0 ) {
			global $wpdb;
			$per_page = apply_filters( 'ns_sync_products_per_batch_number', 25 );
			$offset   = max( $per_page * $offset, 0 );
			$sql      = "SELECT m.meta_value FROM $wpdb->posts p LEFT JOIN $wpdb->postmeta meta ON meta.post_id = p.ID LEFT JOIN $wpdb->postmeta m ON m.post_id = p.ID WHERE meta.meta_key like '%ns_fba_is_fulfill%' AND m.meta_key like '%_sku%' AND meta.meta_value = 'yes' AND m.meta_value != 'no' AND p.post_type = 'product' LIMIT %d, %d";
			$skus     = $wpdb->get_results( $wpdb->prepare( $sql, $offset, $per_page ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery
			$sku_data = array();
			if ( $skus ) {
				foreach ( $skus as $sku ) {
					if ( empty( $sku->meta_value ) || 'no' === $sku->meta_value ) {
						continue;
					}
					$sku_data[] = $sku->meta_value;
				}
			}
			return $sku_data;
		}

		/**
		 * Only sync the local inventory.
		 * This checks local SKUs that are set to sync with FBA and processes them.
		 *
		 * @param boolean $force Optional. If true, will try to sync inventory, even if `ns_fba_sp_api_sync_inventory_interval_enabled` setting is turned off.
		 *
		 * @return boolean
		 */
		public function sync_sku_inventory( $force = false ) : bool {
			$success      = true;
			$sync_enabled = $this->ns_fba->wc_integration->get_option( 'ns_fba_sp_api_sync_inventory_interval_enabled' );
			if ( 'yes' === $sync_enabled || true === $force ) {

				$skus         = array();
				$current_page = 0;

				do {
					$skus = $this->get_sync_product_skus( $current_page );

					if ( empty( $skus ) ) {
						$this->ns_fba->logger->add_entry( 'No more products found to sync', 'wc', '_inventory' );
						$this->ns_fba->logger->add_entry( '-------------------------------------', 'wc', '_inventory' );
						break;
					}

					$sku_string     = implode( ',', $skus );
					$inventory_data = $this->get_inventory_summaries( '', $sku_string );

					if ( false === $inventory_data || ! isset( $inventory_data->payload ) || ! isset( $inventory_data->payload->inventorySummaries ) ) {
						// Log WooCommerce error if debug is enabled.
						$this->ns_fba->logger->add_entry( 'Inventory Sync error', 'wc', '_inventory' );
						$this->ns_fba->logger->add_entry( $inventory_data, 'wc', '_inventory' );
						$success = false;
						break;
					}

					$this->process_inventory_data( $inventory_data );

					$current_page++;
				} while ( ! empty( $skus ) );

				$this->ns_fba->wc_integration->update_option( LAST_INVENTORY_SYNC_OPT_NAME, ( new DateTime() )->format( 'Y-m-d H:i:s' ) );
			} else {
				$success = false;
			}
			return $success;
		}

		/**
		 * Sync inventory levels.
		 * Since this function can be called from ajax button or scheduled action, always check for
		 * the setting value before trying to start its logic to handle the latter scenario.
		 *
		 * @param bool $force Optional. If true, will try to sync inventory, even if `ns_fba_sp_api_sync_inventory_interval_enabled` setting is turned off.
		 *
		 * @return bool
		 */
		public function sync_inventory( $force = false ): bool {
			// Clear inventory logs.
			$this->ns_fba->file_utils->delete( $this->ns_fba->inv_log_path );

			$success = true;

			$sync_enabled = $this->ns_fba->wc_integration->get_option( 'ns_fba_sp_api_sync_inventory_interval_enabled' );

			// phpcs:ignore
			//error_log( "sync_inventory" );

			if ( 'yes' === $sync_enabled || true === $force ) {
				$next_token = '';

				do {
					$inventory_data = $this->get_inventory_summaries( $next_token );
					if ( false === $inventory_data || ! isset( $inventory_data->payload ) || ! isset( $inventory_data->payload->inventorySummaries ) ) {
						$this->ns_fba->logger->add_entry( 'Inventory Sync error', 'wc', '_inventory' );
						$this->ns_fba->logger->add_entry( $inventory_data, 'wc', '_inventory' );
						$success = false;
						break;
					}

					$next_token = $this->process_inventory_data( $inventory_data );
				} while ( '' !== $next_token );

				$this->ns_fba->wc_integration->update_option( LAST_INVENTORY_SYNC_OPT_NAME, ( new DateTime() )->format( 'Y-m-d H:i:s' ) );
			} else {
				$success = false;
			}

			return $success;
		}

		/**
		 * Process the inventory data.
		 *
		 * @param object $inventory_data The inventory data.
		 *
		 * @return string
		 */
		private function process_inventory_data( $inventory_data ) {
			// phpcs:disable WordPress.NamingConventions.ValidVariableName
			foreach ( $inventory_data->payload->inventorySummaries as $inventorySummary ) {
				if ( ! isset( $inventorySummary->sellerSku ) || ! isset( $inventorySummary->inventoryDetails->fulfillableQuantity ) ) {
					continue;
				}
				$this->ns_fba->inventory->set_product_stock( false, $inventorySummary->sellerSku, 'sync-inventory', $inventorySummary->inventoryDetails->fulfillableQuantity );
			}
			// phpcs:enable WordPress.NamingConventions.ValidVariableName
			/**
			 * The nextToken is reference for pagination @see https://developer-docs.amazon.com/sp-api/docs/fbainventory-api-v1-reference#pagination
			 * The payload only returns the payload schema @see https://developer-docs.amazon.com/sp-api/docs/fbainventory-api-v1-reference#getinventorysummariesresult .
			 */
			// $next_token = $inventory_data->payload->nextToken ?? '';
			return $inventory_data->pagination->nextToken ?? '';
		}

		/**
		 * Get the product details defined in Seller Partner.
		 *
		 * @param string $product_SKU The product sku.
		 *
		 * @return object|null
		 */
		public function get_sp_product_details( $product_SKU ): ?object { // phpcs:ignore WordPress.NamingConventions.ValidVariableName
			// phpcs:disable WordPress.NamingConventions.ValidVariableName
			$marketplace_id = $this->ns_fba->wc_integration->get_option( 'ns_fba_marketplace_id' );
			$path           = '/catalog/v0/items?MarketplaceId=' . $marketplace_id . '&SellerSKU=' . $product_SKU;
			$response       = $this->make_request( $path, 'GetProductDetails' );

			if ( SP_API::is_error_in( $response ) ) {
				$this->ns_fba->logger->add_entry( $response, 'wc', '_fulfillment' );
				return null;
			}

			$body = json_decode( $response['body'] );

			if ( isset( $body->error ) ||
			empty( $body->payload->Items ) ||
			empty( $body->payload->Items[0]->AttributeSets )
			) {
				return null;
			}

			return $body->payload->Items[0]->AttributeSets[0];
			// phpcs:enable WordPress.NamingConventions.ValidVariableName
		}

		/**
		 * Check the encoding of a string and log it.
		 *
		 * @param string $string_before The before string value.
		 * @param string $string_after  The string value after.
		 */
		public function check_and_log_charset( $string_before, $string_after ) {
			// target output is TABBED separated CSV.
			if ( ! $this->ns_fba->file_utils->exists( $this->ns_fba->trans_log_path ) ) {
				$head  = '';
				$head .= "DATE\t";
				$head .= "INTERNAL\t";
				$head .= "DETECTED\t";
				$head .= "STR BEFORE\t";
				$head .= "ISO-8859-1\t";
				$head .= "UTF-8\t";
				$head .= "UTF-8 STRICT\t";
				$head .= "STR AFTER\n";
				$this->ns_fba->logger->add_entry( $head, 'info', $this->ns_fba->trans_log_path );
			}
			$row  = '';
			$row .= gmdate( 'm-d-Y H:i:s', time() ) . "\t";
			$row .= mb_internal_encoding() . "\t";
			$row .= mb_detect_encoding( $string_before ) . "\t";
			$row .= $string_before . "\t";
			$row .= ( mb_detect_encoding( $string_before, 'ISO-8859-1' ) ) ? "YES\t" : "NO\t";
			$row .= ( mb_detect_encoding( $string_before, 'UTF-8' ) ) ? "YES\t" : "NO\t";
			$row .= ( mb_detect_encoding( $string_before, 'UTF-8', true ) ) ? "YES\t" : "NO\t";
			$row .= $string_after;
			$row .= "\n";
			$this->ns_fba->logger->add_entry( $row, 'info', $this->ns_fba->trans_log_path );
		}

		/**
		 * Returns the marketplace ID to be used for an order, based on the order's
		 * shipping country.
		 *
		 * @param WC_Order $order The order.
		 * @param string   $default The default string.
		 *
		 * @since 3.1.6
		 *
		 * @return string
		 */
		protected function get_marketplace_id_for_order( $order, $default ) {
			// Return the default marketplace ID if the configured region is not Europe
			// @link https://app.codeable.io/tasks/74910 .
			if ( 'https://mws-eu.amazonservices.com' !== $this->ns_fba->get_option( 'ns_fba_service_url' ) ) {
				return $default;
			}

			// Map country codes against the respective Amazon Marketplace IDs
			// @link https://docs.developer.amazonservices.com/en_US/dev_guide/DG_Endpoints.html
			// these 5 appear to be the only ones included in the European Fulfillment Network
			// (NL, for instance fails with "Incorrect SellerId" for UK sellers).
			$marketplace_ids = array(
				'ES' => 'A1RKKUPIHCS9HS',
				'GB' => 'A1F83G8C2ARO7P',
				'FR' => 'A13V1IB3VIYZZH',
				'DE' => 'A1PA6795UKMFR9',
				'IT' => 'APJ6JRA9NG5V4',
			);

			// Return the appropriate marketplace ID, or the default if the shipping country
			// doesn't match any of the marketplaces.
			return isset( $marketplace_ids[ $order->get_shipping_country() ] ) ? $marketplace_ids[ $order->get_shipping_country() ] : $default;
		}

		/**
		 * Log summary html.
		 *
		 * @param string   $shipping The shipping method.
		 * @param string   $speed The speed to FBA.
		 * @param string   $address The address.
		 * @param WC_Order $order The order.
		 *
		 * @return string
		 */
		public function create_log_summary_html( $shipping, $speed, $address, $order ) {
			$summary  = '';
			$summary .= '<b>Date: </b>' . gmdate( 'c', time() ) . '<br /><br />';
			$summary .= '<b>Order Shipping Method: </b>' . $shipping . '<br />';
			$summary .= '<b>Shipping Speed to FBA: </b>' . $speed . '<br />';
			$summary .= '<b>Shipping to Address:</b><br />' . $address . '<br /><br />';
			$summary .= '<b>All Order Items (some of these might be excluded in request to fulfillment based on product settings):</b><br />';

			$style    = ' style="' . "text-align:left; vertical-align:middle; border: 1px solid #eee; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; word-wrap:break-word;" . '" ';
			$summary .= '<div id="order-items-div" class="order-items"><table id="order-items-table" class="order-items-details"><tbody><tr><th' . $style . '>Product</th><th' . $style . '>QTY</th><th' . $style . '>Price</th></tr>';

			if ( $this->ns_fba->wc->is_woo_version( '3.0' ) ) {
				$summary .= wc_get_email_order_items( $order );
			} else {
				$summary .= $order->email_order_items_table(
					array(
						'show_sku' => true,
					)
				);
			}

			$summary .= '</tbody></table></div><br />';
			return $summary;
		}

		/**
		 * Crawl through the shipping info recursively and build up the shipping info string.
		 *
		 * @param array  $info The shipping info.
		 * @param string $results The output results.
		 *
		 * @return string
		 */
		public function recurse_order_shipping_info( $info, $results = '' ) {
			foreach ( $info as $key => $element ) {
				if ( 'FulfillmentShipmentStatus' === $key ) {
					$results .= 'Shipping Status:<b> ' . $element . '</b><br />';
				}
				if ( 'EstimatedArrivalDateTime' === $key && strpos( $results, 'Estimated Arrival' ) === false ) {
					$results .= 'Estimated Arrival:<b> ' . gmdate( 'm-d-Y', strtotime( $element ) ) . '</b><br />';
				}
				if ( 'CarrierCode' === $key ) {
					$results .= 'Shipping Carrier:<b> ' . $element . '</b><br />';
				}
				if ( 'TrackingNumber' === $key ) {
					$results .= 'Tracking Number:<b> ' . $element . '</b><br />';
				}
				// recurse.
				$results = $this->recurse_order_shipping_info( $element, $results );
			}
			return $results;
		}

		/**
		 * Look at ns_fba_encode_convert_bypass setting for getting the right formatted string
		 *
		 * @param string $string_raw The raw string to format.
		 *
		 * @return false|mixed|string
		 */
		private function get_formatted_string( $string_raw ) {
			$org_string = $string_raw;
			if ( ! $this->ns_fba->utils->isset_on( $this->ns_fba->get_option( 'ns_fba_encode_convert_bypass', 'no' ) ) ) {
				$string_raw = iconv( 'UTF-8', 'ASCII//TRANSLIT//IGNORE', $string_raw );
			}

			// Handle other specific cases that cause SP SPI and signature calculations to choke.
			// We need to do these no matter what the settings are or requests could fail.
			// TODO: Provide a better single validation point for all input through the API to check for and prevent issues.
			// TODO: Some of these handlers might not be necessary when submitting requests directly from postman.
			// TODO: However, some of them still cause the Sig Server to choke so the Sig Server (ns-fbasig > mcf branch)...
			// TODO: ...might need some work and extensive testing to figure out why these are breaking the requests...
			// TODO: ...probably something related to the signature calculation...
			// Handle # symbols.
			$string_raw = str_ireplace( '#', 'no.', $string_raw );
			// Handle & symbols which cause "Could not process payload" errors.
			$string_raw = str_ireplace( '&', 'and', $string_raw );

			// Sometimes this can be empty. HS #1018.
			if ( empty( $string_raw ) ) {
				return $org_string;
			}

			return $string_raw;
		}

		/**
		 * Get shipping speed category for $order
		 *
		 * @param WC_Order $order The order.
		 *
		 * @return string
		 */
		private function get_shipping_speed_category( WC_Order $order ): string {
			// Get the default shipping speed.
			$result = $this->ns_fba->wc_integration->get_option( 'ns_fba_shipping_speed' );

			// All attempt to determine the speed must result in specific value in set ['Standard', 'Expedited', 'Priority']
			// otherwise default value will be used.

			$shipping_method = $order->get_shipping_method();

			if ( empty( $shipping_method ) ) {
				return $result;
			}

			if ( str_starts_with( $shipping_method, 'Amazon' ) ) {
				// If shipping method is Amazon try to determine which is the speed.
				$method_words = explode( ' ', $shipping_method );

				if ( count( $method_words ) === 3 ) {
					$shipping_speed_category = $method_words[2];

					// Due to the speed has been extracted from string the value is verified.
					if ( in_array( $shipping_method, array( 'Standard', 'Expedited', 'Priority' ), true ) ) {
						$result = $shipping_speed_category;
					}
				}
			} else {
				$shipping_speed_category_standard  = $this->ns_fba->wc_integration->get_option( 'ns_fba_shipping_speed_standard' );
				$shipping_speed_category_expedited = $this->ns_fba->wc_integration->get_option( 'ns_fba_shipping_speed_expedited' );
				$shipping_speed_category_priority  = $this->ns_fba->wc_integration->get_option( 'ns_fba_shipping_speed_priority' );

				if ( $shipping_speed_category_standard === $shipping_method ) {
					$result = 'Standard';
				} elseif ( $shipping_speed_category_expedited === $shipping_method ) {
					$result = 'Expedited';
				} elseif ( $shipping_speed_category_priority === $shipping_method ) {
					$result = 'Priority';
				}
			}

			return $result;
		}

		/**
		 * Write a log entry if setting allow it
		 *
		 * @param   string $tag  The log tag.
		 * @param   string $log  The log message.
		 */
		private function write_debug_log( string $tag, string $log ) {
			$this->ns_fba->logger->add_entry( '<p><b>' . $tag . '</b>: ' . $log . '</p><br />', 'debug', $this->ns_fba->debug_log_path );
		}

		/**
		 * Make the remote request.
		 *
		 * @param   string $path               URL to be called.
		 * @param   string $type               Amazon request type / end-point / function.
		 * @param   int    $qty                Total fulfillment Qty (if applicable).
		 * @param   string $method             Request method.
		 * @param   string $json_encoded_body  The json body.
		 *
		 * @return WP_Error|array can be WP_Error or array
		 */
		private function make_request( string $path = '',
			string $type = 'Unspecified',
			int $qty = 0,
			string $method = 'GET',
			string $json_encoded_body = '' ) {

			// Check if the ap_api is null and initiate it.
			// Tends to happen in the frontend.
			$this->maybe_init_sp_api();
			return $this->ns_fba->sp_api->make_request( $path, $type, $qty, $method, $json_encoded_body );
		}
	} // class.
}
