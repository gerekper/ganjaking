<?php
/**
 * Fulfilment management.
 *
 * @package NeverSettle\WooCommerce-Amazon-Fulfillment
 */

defined( 'ABSPATH' ) || exit;

require_once dirname( __FILE__ ) . '/class-sp-api.php';

if ( ! class_exists( 'SP_Fulfillment' ) ) {

	/**
	 * Fulfilment management helper.
	 */
	class SP_Fulfillment {

		/**
		 * Singleton instance of NS_FBA
		 *
		 * @var NS_FBA $ns_fba
		 */
		private $ns_fba;

		/**
		 * Will store an SP_API class instance (initialized in __construct)
		 *
		 * @var SP_API $sp_api
		 */
		private $sp_api;

		/**
		 * Singleton Pattern
		 *
		 * @var ?SP_Fulfillment $instance
		 */
		private static $instance = null;

		/**
		 * SINGLETON INSTANCE
		 */
		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self( NS_FBA::get_instance() );
			}
			return self::$instance;
		}

		/**
		 * Constructor.
		 *
		 * @param NS_FBA $ns_fba Core plugin object.
		 */
		public function __construct( NS_FBA $ns_fba ) {
			self::$instance = $this;
			$this->ns_fba   = $ns_fba;
			add_action( 'init', array( $this, 'init_sp_api' ) );
		}

		/**
		 * Initialize our local SP_API object.
		 */
		public function init_sp_api() {
			$this->sp_api = new SP_API( $this->ns_fba->wc_integration->get_SP_API_options() );
		}

		/**
		 * API Test
		 *
		 * @param string $path The api path.
		 *
		 * @return array
		 */
		public function test_api_connection( $path = '/fba/outbound/2020-07-01/fulfillmentOrders' ): array {
			// TODO: Assess the possibility of using a simpler endpoint.
			// TODO: Assess getting rid of this clunky result array logic.
			$result = array(
				'success'  => false,
				'message'  => 'An error has occurred, please try again!',
				'response' => array(),
			);

			/** TODO: Only used for manually testing specific paths.
			$path  = '/fba/inventory/v1/summaries?details=true&granularityType=Marketplace&granularityId=ATVPDKIKX0DER';
			$path .= '&marketplaceIds=ATVPDKIKX0DER';
			*/

			$response = $this->sp_api->make_request( $path, 'TestApiConnection' );

			if ( $this->sp_api->is_error_in( $response ) ) {
				return $result;
			}

			$result['success']  = true;
			$result['message']  = 'Success';
			$result['response'] = json_decode( $response['body'] );

			return $result;
		}

		/**
		 * Get SKUs.
		 *
		 * @param string $marketplace_id The marketplace id.
		 * @param string $next_token The next token.
		 *
		 * @return array
		 */
		public function get_SKUs( $marketplace_id, $next_token = '' ): array { // phpcs:ignore WordPress.NamingConventions
			// TODO: Assess getting rid of this clunky result array logic.
			$result = array(
				'success' => false,
				'message' => 'An error has occurred getting SKUs from API',
				'data'    => array(),
			);

			$path = '/fba/inventory/v1/summaries?details=true&granularityType=Marketplace&granularityId=' . $marketplace_id . '&marketplaceIds=' . $marketplace_id;

			if ( ! empty( $next_token ) ) {
				$path .= '&nextToken=' . $next_token;
			}

			$response = $this->sp_api->make_request( $path, 'GetInventorySkus' );
			$body     = json_decode( $response['body'] );

			if ( $this->sp_api->is_error_in( $response ) ) {
				return $result;
			} else {
				$added_inventory   = array();
				$pending_inventory = array();
				// phpcs:disable WordPress.NamingConventions.ValidVariableName
				if ( isset( $body->payload ) && isset( $body->payload->inventorySummaries ) ) {

					/**
					 * Iterates the inventory summary to differentiate the product according to
					 * whether it already exists in woocommerce or not,
					 * and stores it in two different arrays for further processing.
					 */
					foreach ( $body->payload->inventorySummaries as $inventorySummary ) {

						$wc_product_id = wc_get_product_id_by_sku( $inventorySummary->sellerSku );

						$inventorySummary->totalQuantity = $inventorySummary->inventoryDetails->fulfillableQuantity;

						$sp_product_details = $this->get_sp_product_details( $inventorySummary->sellerSku );

						if ( ! empty( $sp_product_details ) ) {
							if ( ! empty( $sp_product_details->SmallImage ) ) {
								$inventorySummary->image_url = $sp_product_details->SmallImage->URL;
							}

							if ( ! empty( $sp_product_details->ListPrice ) ) {
								$inventorySummary->regular_price = $sp_product_details->ListPrice->Amount;
							}
						}

						if ( 0 === $wc_product_id ) {
							$inventorySummary->md5_sku      = md5( $inventorySummary->sellerSku );
							$inventorySummary->string_value = base64_encode( wp_json_encode( $inventorySummary ) );
							$pending_inventory[]            = $inventorySummary;
						} else {
							$wc_product = (object) wc_get_product( $wc_product_id )->get_data();

							$wc_product = $this->get_formatted_wc_product( $wc_product );

							$added_inventory[] = $wc_product;
						}
					}
				}
				// phpcs:enable WordPress.NamingConventions.ValidVariableName
				$result['success']                   = true;
				$result['data']['added_inventory']   = $added_inventory;
				$result['data']['pending_inventory'] = $pending_inventory;
				$result['data']['next_token']        = $body->payload->nextToken ?? '';
			}

			return $result;
		}

		/**
		 * Import Seller Partner SKUs
		 *
		 * @param array $data An encode object collection.
		 *
		 * @return array
		 */
		public function import_SKUs( $data ): array { // phpcs:ignore WordPress.NamingConventions
			$result = array(
				'added'   => array(),
				'failure' => array(),
				'ignored' => array(),
			);

			if ( empty( $data ) ) {
				return $result;
			}

			$added   = array();
			$failure = array();
			$ignored = array();

			foreach ( $data as $encoded_object ) {
				$sp_product = json_decode( base64_decode( $encoded_object ) );

				$wc_product_id = wc_get_product_id_by_sku( $sp_product->sellerSku ); // phpcs:ignore WordPress.NamingConventions

				if ( ! $wc_product_id ) {
					$this->create_simple_product( $sp_product, $added, $failure );
				} else {
					$ignored = md5( $sp_product->sellerSku ); // phpcs:ignore WordPress.NamingConventions
				}
			}

			$result['added']   = $added;
			$result['failure'] = $failure;
			$result['ignored'] = $ignored;

			return $result;
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
			// return $this->sp_api->make_sandbox_requests( $path, 'POST', $request_body ); .

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

				$product = wc_get_product( $item['product_id'] );

				$body['items'][] = array(
					'quantity'                     => $item['quantity'],
					'sellerSku'                    => $product->get_sku(),
					'sellerFulfillmentOrderItemId' => uniqid( 'id_' . $item['product_id'] . '_' ),
				);
			}

			$body['marketplaceId'] = $this->ns_fba->wc_integration->get_option( 'ns_fba_marketplace_id' );

			$body['shippingSpeedCategories'] = array();

			$body['shippingSpeedCategories'] = $shippingSpeedCategories; // phpcs:ignore WordPress.NamingConventions

			$request_body = wp_json_encode( $body );

			$path = '/fba/outbound/2020-07-01/fulfillmentOrders/preview';

			return $this->sp_api->make_request( $path, 'GetFulfillmentOrdersPreview', 0, 'POST', $request_body );
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
			$order_id  = $order->get_id();
			$order_number = $this->ns_fba->options['ns_fba_order_prefix'] . $order->get_order_number();

			$log_tag = 'post_fulfillment_order: ';

			$this->write_debug_log( $log_tag, 'Starting post_fulfillment_order' );

			try {

				if ( $this->ns_fba->is_debug ) {
					error_log( '<b>Step 2 of 8: </b>INSIDE post_fulfillment_order AND INSIDE try<br /><br />', 3, $this->ns_fba->debug_log_path );
				}

				if ( ! $is_manual_send && 'yes' === $this->ns_fba->wc_integration->get_option( 'ns_fba_manual_only_mode' ) ) {
					$this->write_debug_log( $log_tag, 'Skipping fulfillment' );
					$order->add_order_note( __( 'Order was not sent to Amazon because Manual Only mode is on.', $this->ns_fba->wc_integration->text_domain ) );
					return false;
				}

				// Validate if exist at least 1 product with Amazon Fulfillment Enable.
				$fulfill_item_count = 0;
				$total_item_count   = 0;

				$order_data = $order->get_data();

				$body  = array();
				$items = array();

				// Comma-separated list of fulfilled skus to update inventory levels on later.
				$fba_skus = '';

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

					// run through the Order Level smart fulfillment rules.
					$is_order_item_fba = $this->ns_fba->utils->is_order_item_amazon_fulfill( $order, $item, $product, $product_id, $is_manual_send );

					if ( $is_order_item_fba ) {
						$current_sku = $this->get_sku_to_send( $product );

						// phpcs:ignore
						// error_log( "post_fulfillment_order SENDING to Amazon the SKU: " . $current_sku );

						$items[] = array(
							'quantity'                     => $item->get_data()['quantity'],
							'sellerFulfillmentOrderItemId' => $order_number . '_item_' . $product->get_id(),
							'sellerSku'                    => $current_sku,
							'perUnitDeclaredValue '        => array(
								'currencyCode ' => get_woocommerce_currency(),
								'value '        => floatval( $product->get_price() ),
							),
						);
						if ( '' === $fba_skus ) {
							$fba_skus = $current_sku;
						} else {
							$fba_skus .= ',' . $current_sku;
						}
						$fulfill_item_count++;
					}
				}

				if ( $this->ns_fba->is_debug ) {
					error_log( '<b>Step 3 of 8: </b>AFTER checking all items in order<br />', 3, $this->ns_fba->debug_log_path );
					if ( $this->ns_fba->utils->isset_on( $this->ns_fba->options['ns_fba_perfection_mode'] ) ) {
						error_log( '----: Perfection Mode is ACTIVE<br />', 3, $this->ns_fba->debug_log_path );
					} else {
						error_log( '----: Perfection Mode is OFF<br />', 3, $this->ns_fba->debug_log_path );
					}
					error_log( '----: Total Item Count = ' . $total_item_count . '<br />', 3, $this->ns_fba->debug_log_path );
					error_log( '----: Fulfill Item Count = ' . $fulfill_item_count . '<br /><br />', 3, $this->ns_fba->debug_log_path );
				}

				if ( 0 === $fulfill_item_count ) {
					// if there are zero FBA order items then we're done.
					$this->write_debug_log( $log_tag, 'Zero products detected for fulfillment' );
					return false;
				}

				if ( ! $is_manual_send && $this->ns_fba->utils->isset_on( $this->ns_fba->options['ns_fba_perfection_mode'] ) && $total_item_count !== $fulfill_item_count ) {
					// if we're in Perfection Mode but not ALL of the order items passed fulfillment rules, then we're done.
					$this->write_debug_log( $log_tag, 'Perfection Mode is active and Not ALL products in this order are set to fulfill with FBA' );
					$order->add_order_note( __( 'Perfection Mode is active and Not ALL products in this order are set to Fulfill with Amazon, so we did not try to send this order to FBA.', $this->ns_fba->text_domain ) );
					return;
				}

				if ( ! $this->ns_fba->utils->is_order_amazon_fulfill( $order, $is_manual_send ) ) {
					// we must make sure there are no order-level rules that will prevent this order from being sent
					// run through the Order Level smart fulfillment rules.

					// this generic condition should never happen because we should be throwing all exceptions back from is_order_amazon_fulfill()
					// but just in case...
					throw new Exception( __( 'The Order cannot be sent to FBA due to the configured settings or products.', $this->ns_fba->text_domain ) );
				}

				// let's send some stuff to FBA!!!

				if ( $this->ns_fba->is_debug ) {
					// phpcs:ignore
					error_log( '<b>Step 4 of 8: </b>BEFORE new Fulfillment Order configured<br /><br />', 3, $this->ns_fba->debug_log_path );
				}

				$this->write_debug_log( $log_tag, 'Preparing request' );

				$body['marketplaceId']            = $this->ns_fba->wc_integration->get_option( 'ns_fba_marketplace_id' );
				$body['sellerFulfillmentOrderId'] = $order_number;
				$body['displayableOrderId']       = $order_number;
				$body['displayableOrderDate']     = $order_data['date_created']->format( 'Y-m-d\TH:i:s.000\Z' );
				$body['displayableOrderComment']  = $this->ns_fba->wc_integration->get_option( 'ns_fba_order_comment' );

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
				if ( ! $this->ns_fba->utils->isset_on( $this->ns_fba->options['ns_fba_exclude_phone'] ) ) {
					$body['destinationAddress']['PhoneNumber'] = $this->get_formatted_string( $order_data['billing']['phone'] );
				} else {
					$body['destinationAddress']['PhoneNumber'] = '';
				}

				// Amazon shipping notification list.
				$notify = array();
				if ( get_post_meta( $order_id, '_billing_email', true ) && ! $this->ns_fba->utils->isset_on( $this->ns_fba->options['ns_fba_disable_shipping_email'] ) ) {
					$notify = array( $order_data['billing']['email'] );
				}
				// Add the admin email.
				if ( $this->ns_fba->utils->isset_on( $this->ns_fba->options['ns_fba_email_on_error'] ) ) {
					$notify = $notify + array( $this->ns_fba->options['ns_fba_notify_email'] );
				}

				$body['notificationEmails'] = $notify;

				$body['items'] = $items;

				$feature_blank_box = $this->ns_fba->wc_integration->get_option( 'ns_fba_fulfillment_ship_blank_box' );
				$feature_amzl      = $this->ns_fba->wc_integration->get_option( 'ns_fba_fulfillment_ship_amzl' );

				if ( 'yes' === $feature_blank_box ) {
					$body['featureConstraints'][] = array(
						'featureFulfillmentPolicy' => 'Required',
						'featureName'              => 'BLANK_BOXES',
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

				if ( $this->ns_fba->is_debug ) {
					error_log( '<b>Step 5 of 8: </b>AFTER new Fulfillment Order configured<br /><br />', 3, $this->ns_fba->debug_log_path );
					error_log( '<b>Step 6 of 8: </b>INSIDE try sending request to FBA<br /><br />', 3, $this->ns_fba->debug_log_path );
				}

				$path = '/fba/outbound/2020-07-01/fulfillmentOrders';

				$response = $this->sp_api->make_request(
					$path,
					'CreateFulfillmentOrderRequest',
					$fulfill_item_count,
					'POST',
					$request_body
				);

				$this->write_debug_log( $log_tag, 'Response received' );

				if ( ! $this->sp_api->is_error_in( $response ) ) {
					$this->write_debug_log( $log_tag, $response['body'] );

					// Tracking array for inventory updates.
					$inventory_updates = array();
					$inventory_data    = $this->get_inventory_summaries( '', $fba_skus );
					// Update the inventory numbers if sync is enabled.
					if ( $this->ns_fba->utils->isset_on( ( $this->ns_fba->options['ns_fba_update_inventory'] ) ) ) {
						// phpcs:disable WordPress.NamingConventions.ValidVariableName
						foreach ( $inventory_data->payload->inventorySummaries as $inventorySummary ) {
							// TODO: check for errors.
							$fba_sku        = $inventorySummary->sellerSku;
							$stock          = $inventorySummary->inventoryDetails->fulfillableQuantity;
							$woo_product    = $this->ns_fba->wc->get_product_by_sku( $fba_sku );
							$message_prefix = 'WC Order ' . $order_data['order_key'] . ': >> ';
							$this->ns_fba->inventory->set_product_stock( $woo_product, $fba_sku, $message_prefix, $stock );
							// TODO: something with this array of logged inventory updates.
							array_push( $inventory_updates, 'Updated ' . $fba_sku . ' stock to ' . $stock );
						}
					} else {
						array_push( $inventory_updates, __( 'Inventory sync is not enabled in the settings.', $this->ns_fba->text_domain ) );
					}

					if ( $this->ns_fba->utils->isset_on( $this->ns_fba->options['ns_fba_automatic_completion'] ) ) {
						$ns_wc_term = 'completed';
						$this->write_debug_log( $log_tag, 'Setting for Mark Orders Complete active. Order Status set to Completed.' );
					} elseif ( $fulfill_item_count === $total_item_count ) {
						$ns_wc_term = 'sent-to-fba';
						$this->write_debug_log( $log_tag, 'All items fulfilled with FBA. Order Status set to Sent to FBA' );
					} else {
						$ns_wc_term = 'part-to-fba';
						$this->write_debug_log( $log_tag, 'Some items fulfilled with FBA. Order Status set to Partial to FBA' );
					}

					$this->set_order_status_safe( $order_id, $ns_wc_term );

					// Create the log of order info.
					error_log( "LOG for Order: " . $order_id . " @ " . wp_date("Y-m-d H:i:s") . "<br><br>",3, $this->ns_fba->log_path ); // phpcs:ignore
					error_log( "REQUEST: <br><pre>" . print_r( json_decode( $request_body, true ), true ) . "</pre><br><br>",3, $this->ns_fba->log_path ); // phpcs:ignore
					error_log( "RESPONSE: <br><pre>" . print_r( $response, true ) . "</pre><br><br>",3, $this->ns_fba->log_path ); // phpcs:ignore

					$order->add_order_note( '<a href="' . $this->ns_fba->log_url . '" target="_blank">' . __( 'Successfully submitted order to FBA', $this->ns_fba->text_domain ) . '</a> (' . __( 'click for full log', $this->ns_fba->text_domain ) . ').' );

					update_post_meta( $order_id, '_sent_to_fba', gmdate( 'm-d-Y H:i:s', time() ) );

					$this->write_debug_log( $log_tag, '_sent_to_fba has been updated' );

					return $order_id;

				} else {
					// TODO: finish handling error responses from Amazon.
					$error_message = json_decode( $response['body'], true );
					$this->write_debug_log( $log_tag, "In ERROR: error_message:\n" . print_r( $error_message, true ) ); // phpcs:ignore
					error_log( "ERROR LOG for Order: " . $order_id . " @ " . wp_date("Y-m-d H:i:s") . "<br><br>",3, $this->ns_fba->err_log_path ); // phpcs:ignore
					error_log( "Original REQUEST: <br><pre>" . print_r( json_decode( $request_body, true ), true ) . "</pre><br><br>",3, $this->ns_fba->err_log_path ); // phpcs:ignore
					error_log( "Final RESPONSE: <br><pre>" . print_r( $response, true ) . "</pre><br><br>",3, $this->ns_fba->err_log_path ); // phpcs:ignore
					$this->set_order_status_safe( $order_id, 'fail-to-fba' );
					$order->add_order_note(
						'<a href="' . $this->ns_fba->err_log_url . '" target="_blank">' .
						__( 'Failed to submit order to FBA', $this->ns_fba->text_domain ) . '</a> (' .
						__( 'click for full log', $this->ns_fba->text_domain ) . '). <b>' .
						__( 'Error Message:', $this->ns_fba->text_domain ) . '</b><br /><span style="color:#a00000;">' .
						$error_message['errors'][0]['message'] . '</span>'
					);
				}
			} catch ( Exception $ex ) {
				$this->write_debug_log( $log_tag, $ex->getMessage() );
			}
		}

		/**
		 * Get the parent SKU or variation SKU depending on the product and settings.
		 *
		 * @param WC_Product $item_product The product to check.
		 */
		public function get_sku_to_send( WC_Product $item_product ) {
			// check if this is a variable product or not so that we can conditionally set the right sku.
			if ( $this->ns_fba->wc->is_woo_version( '3.0' ) ) {
				$parent_id = $item_product->get_parent_id();
				if ( $parent_id ) {
					$parent_product = wc_get_product( $parent_id );
					if ( get_post_meta( $parent_product->get_id(), 'ns_fba_send_parent_sku', true ) === 'yes' ) {
						return $parent_product->get_sku();
					} else {
						return $item_product->get_sku();
					}
				} else {
					return $item_product->get_sku();
				}
			} else {
				if ( is_object( $item_product->parent ) && $item_product->parent->is_type( 'variable' ) ) {
					if ( get_post_meta( $item_product->parent->get_id(), 'ns_fba_send_parent_sku', true ) === 'yes' ) {
						return $item_product->parent->get_sku();
					} else {
						return $item_product->get_sku();
					}
				} else {
					return $item_product->get_sku();
				}
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
					$complete_status = $this->ns_fba->utils->isset_on( $this->ns_fba->options['ns_fba_automatic_completion'] ) ? 'completed' : 'sent-to-fba';
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
			$order_number = $this->ns_fba->options['ns_fba_order_prefix'] . $order->get_order_number();
			$path         = '/fba/outbound/2020-07-01/fulfillmentOrders/' . $order_number;

			// The OLD new way of doing it before going back to straight WC Order number
			// $order_pre = $this->ns_fba->wc_integration->get_option( 'ns_fba_order_prefix' );
			// $path      = '/fba/outbound/2020-07-01/fulfillmentOrders/' . $order_pre . $order->get_order_key();

			$fulfillment_response = $this->sp_api->make_request( $path, 'GetFulfillmentOrder' );

			if ( $this->sp_api->is_error_in( $fulfillment_response ) ) {
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
					// TODO: Add an admin note that the tracking info could not be retrieved ???
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
							$package_tracking = $package_details['trackingNumber'];
							$package_arrival  = wp_date( 'Y-m-d H:i:s', ( new DateTime( $package_details['estimatedArrivalDate'] ) )->getTimestamp() );

							/** If we want to add full tracking timeline later this is how we'd do it.
							 * // phpcs:ignore
							 * // $path = '/fba/outbound/2020-07-01/tracking?packageNumber=' . $package_number;
							 * $this->write_debug_log( $log_tag, 'Getting tracking information for package number: ' . $package_number );
							 * $tracking_response = $this->sp_api->make_request( $path, 'GetFulfillmentOrderShippingInfo' );
							 *
							 * if ( $this->sp_api->is_error_in( $tracking_response ) ) {
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

			// phpcs:ignore
			// error_log( "sync_fulfillment_order_status" );

			// get oldest 20 (to stay under throttling limit) orders in wc-success-to-fba status.
			$orders = get_posts(
				array(
					'numberposts' => 20,
					'order'       => 'ASC',
					'post_type'   => wc_get_order_types(),
					'post_status' => array( 'wc-sent-to-fba', 'wc-part-to-fba' ),
				)
			);

			foreach ( $orders as $f_order ) {
				$order        = wc_get_order( $f_order->ID );
				$order_id     = $f_order->ID;
				$order_number = $this->ns_fba->options['ns_fba_order_prefix'] . $order->get_order_number();

				$log_tag = 'Order ' . $order_id;

				// phpcs:ignore
				// error_log( "order_id = " . $order_id );

				try {

					$this->write_debug_log( $log_tag, 'Getting fulfillment order' );

					$fulfillment_response = $this->get_fulfillment_order( $order_id );
					$fulfillment_json     = json_decode( $fulfillment_response['body'], true );
					$fulfillment_status   = $fulfillment_json['payload']['fulfillmentOrder']['fulfillmentOrderStatus'];

					if ( ! $fulfillment_response || empty( $fulfillment_status ) ) {
						$this->write_debug_log( $log_tag, 'Response is not readable' );
						continue;
					}

					// error_log( "order_id = " . $order_id . " response = \n\n" . print_r( $response, true ) );
					// error_log( "order_id = " . $order_id . " status = " . $fulfillment_status );

					$this->write_debug_log( $log_tag, 'Updating fulfillment order status to ' . $fulfillment_status );

					// update the order status based on the FBA status returned.
					switch ( $fulfillment_status ) {
						case 'Cancelled':
							$this->set_order_status_safe( $order_id, 'cancelled' );
							$order->add_order_note( 'Synced fulfillment status with FBA and found CANCELLED. Updated order status from Sent to FBA to Cancelled.' );
							break;
						case 'Complete':
							$this->set_order_status_safe( $order_id, 'completed' );
							$order->add_order_note( 'Synced fulfillment status with FBA and found COMPLETE. Updated order status from Sent to FBA to Completed.' );
							break;
						case 'CompletePartialled':
							$this->set_order_status_safe( $order_id, 'completed' );
							$order->add_order_note( 'Synced fulfillment status with FBA and found COMPLETE_PARTIALLED. Updated order status from Sent to FBA to Completed.' );
							break;
					}
				} catch ( Exception $ex ) {
					$this->write_debug_log( $log_tag, $ex->getMessage() );

					echo esc_html( $order_number . '	' . $f_order->post_type . '	' . $f_order->post_status . ' FBA status err: ' . $ex->getMessage() . '<br />' );

					$order_note = '<a href="' . $this->ns_fba->err_log_url . '" target="_blank">' . __( 'Failed to sync order status from FBA', $this->ns_fba->text_domain ) . '</a> (' . __( 'click for full log', $this->ns_fba->text_domain ) . '). <b>' . __( 'Error Message:', $this->ns_fba->text_domain ) . '</b><br /><span style="color:red;">' . $ex->getMessage() . '</span>';
					$order->add_order_note( $order_note );
				}
			}

			if ( $this->ns_fba->utils->isset_on( $this->ns_fba->options['ns_fba_sync_ship_retry'] ) ) {

				$this->write_debug_log( 'DEBUG', 'Trying to resend orders' );

				$failed_orders = get_posts(
					array(
						'numberposts' => 20,
						'order'       => 'ASC',
						'post_type'   => wc_get_order_types(),
						'post_status' => array( 'wc-fail-to-fba' ),
						'meta_query'  => array(
							array(
								'key'     => 'ns_fba_retried',
								'compare' => 'NOT EXISTS',
							),
						),
					)
				);
				foreach ( $failed_orders as $f_order ) {
					$order_id = $f_order->ID;

					if ( ! get_post_meta( $order_id, 'ns_fba_retried', true ) ) {

						$this->write_debug_log( 'Order ' . $order_id, 'Trying to resend order' );

						$order = wc_get_order( $f_order->ID );
						add_post_meta( $order_id, 'ns_fba_retried', time() );
						$order->add_order_note( 'Retrying to submit order to Amazon' );
						$this->post_fulfillment_order( $order );
					}
				}
			}
		}

		/**
		 * Set the order status.
		 *
		 * @param int    $order_id The order id.
		 * @param string $status The order status.
		 */
		private function set_order_status_safe( $order_id, $status ) {
			$order  = wc_get_order( $order_id );
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
				$path .= '&nextToken=' . $next_token;
			}

			$response = $this->sp_api->make_request( $path, 'GetInventorySummaries' );

			if ( is_wp_error( $response ) ) {
				return false;
			}

			$body = json_decode( $response['body'] );

			if ( isset( $body->error ) ) {
				return false;
			}

			return $body;
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
			$success = true;

			$sync_enabled = $this->ns_fba->wc_integration->get_option( 'ns_fba_sp_api_sync_inventory_interval_enabled' );

			// phpcs:ignore
			//error_log( "sync_inventory" );

			if ( 'yes' === $sync_enabled || true === $force ) {
				$next_token = '';

				do {
					$inventory_data = $this->get_inventory_summaries( $next_token );

					if ( false === $inventory_data || ! isset( $inventory_data->payload ) || ! isset( $inventory_data->payload->inventorySummaries ) ) {
						$success = false;
						break;
					}
					// phpcs:disable WordPress.NamingConventions.ValidVariableName
					foreach ( $inventory_data->payload->inventorySummaries as $inventorySummary ) {
						// TODO: REPLACE THIS WITH SINGLE STOCK LEVEL FUNCTION NEW VER OF inventory->set_product_stock.
						$wc_product = $this->ns_fba->wc->get_product_by_sku( $inventorySummary->sellerSku );

						// TODO: Add checking for variation level on/off for amazon fulfill.
						// But for now don't check because it could interfere with variation SKU stock level updates.
						//$is_ns_fba_is_fulfilled = get_post_meta( $wc_product->get_id(), 'ns_fba_is_fulfill', true );
						//if ( ! empty( $is_ns_fba_is_fulfilled ) && 'yes' === $is_ns_fba_is_fulfilled ) {
						if ( ! empty( $wc_product ) ) {
							$wc_product->set_stock_quantity( $inventorySummary->inventoryDetails->fulfillableQuantity );
							$wc_product->save();
						}
					}

					$next_token = $inventory_data->payload->nextToken ?? '';
					// phpcs:enable WordPress.NamingConventions.ValidVariableName
				} while ( '' !== $next_token );

				$this->ns_fba->wc_integration->update_option( WC_Integration_FBA::LAST_INVENTORY_SYNC_DATE_OPTION_NAME, ( new DateTime() )->format( 'Y-m-d H:i:s' ) );
			} else {
				$success = false;
			}

			return $success;
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

			$response = $this->sp_api->make_request( $path, 'GetProductDetails' );

			if ( is_wp_error( $response ) ) {
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
		 * Prepares the WC product object to adapt it for showing in More SKUs modal table
		 *
		 * @param object $wc_product The product.
		 *
		 * @return object
		 */
		private function get_formatted_wc_product( $wc_product ) {
			// Formatting some fields format.
			$wc_product->stock_quantity    = $wc_product->stock_quantity ?? '';
			$wc_product->date_modified     = null !== $wc_product->date_modified ? $wc_product->date_modified->format( 'Y-m-d' ) : $wc_product->date_created->format( 'Y-m-d' );
			$product_meta                  = get_post_meta( $wc_product->id, 'ns_fba_is_fulfill', true );
			$wc_product->ns_fba_is_fulfill = ! empty( $product_meta ) && 'yes' === $product_meta;
			$wc_product->md5_sku           = md5( $wc_product->id );

			return $wc_product;
		}

		/**
		 * Receives an object with product data and create a simple product in woocommerce
		 *
		 * @param object $sp_product The product from Amazon.
		 * @param array  $added Added array of products.
		 * @param array  $failure Failed product skus.
		 */
		private function create_simple_product( $sp_product, &$added, &$failure ) {
			// phpcs:disable WordPress.NamingConventions.ValidVariableName
			try {
				$product = new WC_Product_Simple();
				$product->set_name( $sp_product->productName );
				$product->set_status( 'draft' );
				$product->set_manage_stock( true );
				$product->set_stock_quantity( $sp_product->totalQuantity );
				$product->set_sku( $sp_product->sellerSku );
				$product->set_regular_price( $sp_product->regular_price );
				$product->update_meta_data( 'ns_fba_is_fulfill', 'yes' );
				$product->save();

				if ( ! empty( $sp_product->image_url ) ) {
					$upload = wc_rest_upload_image_from_url( esc_url_raw( $sp_product->image_url ) );

					if ( ! is_wp_error( $upload ) ) {
						$attachment_id = wc_rest_set_uploaded_image_as_attachment( $upload, $product->get_id() );

						if ( wp_attachment_is_image( $attachment_id ) ) {
							$product->set_image_id( $attachment_id );
							$product->save();
						}
					}
				}

				$added[] = $this->get_formatted_wc_product( (object) $product->get_data() );
			} catch ( Exception $exception ) {
				$failure[] = md5( $sp_product->sellerSku );
			}
			// phpcs:enable WordPress.NamingConventions.ValidVariableName
		}

		/**
		 * Look at ns_fba_encode_convert_bypass setting for getting the right formatted string
		 *
		 * @param string $string_raw The raw string to format.
		 *
		 * @return false|mixed|string
		 */
		private function get_formatted_string( $string_raw ) {
			if ( ! $this->ns_fba->utils->isset_on( $this->ns_fba->options['ns_fba_encode_convert_bypass'] ) ) {
				return iconv( 'UTF-8', 'ASCII//TRANSLIT//IGNORE', $string_raw );
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

			if ( ! empty( $shipping_method ) ) {

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
			if ( $this->ns_fba->is_debug ) {
				// phpcs:ignore
				error_log( '<p><b>' . $tag . '</b>: ' . $log . '</p><br />', 3, $this->ns_fba->debug_log_path );
			}
		}
	} // End Class.
}
