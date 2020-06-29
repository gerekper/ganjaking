<?php
	/**
	 * Outbound class for integrating with the Amazon Outbound Fulfillment API
	 *
	 * @package NeverSettle\WooCommerce-Amazon-Fulfillment
	 * @since 2.0.0
	 */

if ( ! class_exists( 'NS_FBA_Outbound' ) ) {

	class NS_FBA_Outbound {

		private $ns_fba;

		function __construct( $ns_fba ) {
			// local reference to the main ns_fba object
			$this->ns_fba = $ns_fba;

		}

		/**
			 * Builds an MWS outbound fulfillment service request
			 *
			 * @return FBAOutboundServiceMWS_Client
			 */
		function create_service_outbound() {
			$config = array(
				'ServiceURL' => $this->ns_fba->options['ns_fba_service_url'] . '/FulfillmentOutboundShipment/2010-10-01',
				'ProxyHost' => null,
				'ProxyPort' => -1,
				'MaxErrorRetry' => 3,
			);
			$service = new FBAOutboundServiceMWS_Client(
				NS_AWS_ACCESS_KEY_ID,
				NS_AWS_SECRET_ACCESS_KEY,
				$config,
				NS_APPLICATION_NAME,
				NS_APPLICATION_VERSION
			);
			// must temporarily set $_config in \lib\FBAOutboundServiceMWS\Client.php to public for this debug log to work
			//error_log( '<strong>Full UserAgent: </strong>' . $service->_config['UserAgent'] . '<br /><br />', 3, $this->ns_fba->debug_log_path );

			return $service;
		}

		/**
			 * Tests the API credentials and connection
			 *
			 * @return string status of test: 'success' or error message
			 */
		function test_api() {
			// Set up the MWS service connection
			// We use the ListAllFulfillmentOrdersRequest to test connectivity because the generic GetServiceStatus
			// does not validate the credentials - it's open to anyone to ping the service status.
			// This test will validate the credentials even if there are no active fulfillment order requests.
			$service = $this->create_service_outbound();
			$request = new FBAOutboundServiceMWS_Model_ListAllFulfillmentOrdersRequest();
			if ( defined( 'NS_MWS_AUTH_TOKEN' ) ) {
				$request->setMWSAuthToken( NS_MWS_AUTH_TOKEN );
			}
			$request->setSellerId( NS_MERCHANT_ID );
			// ARG CRAZY AMAZON LIB - ListAllFulfillmentOrdersRequest() ONLY has setMarketplace() which is
			// inconsistent with other request models that have and require setMarketplaceId
			$request->setMarketplace( NS_MARKETPLACE_ID );
			$request->setQueryStartDateTime( date( 'c', time() - 1 * HOUR_IN_SECONDS ) );

			try {
				$response = $service->ListAllFulfillmentOrders( $request );
				return 'success';
			} catch ( FBAOutboundServiceMWS_Exception $ex ) {
				// Note: sometimes an exception fires but the status code is 200. in our testing this condition seems to
				//		 indicate that the exact response could not be generated, but the request credientials are valid.
				//		 serious errors return non-200 status codes.
				if ( '200' == $ex->getStatusCode() ) {
					return 'success';
				} else {
					return $ex->getMessage();
				}
			}
		}

		/**
			 * Check the encoding of a string and log it
			 */
		function check_and_log_charset( $string_before, $string_after ) {
			// target output is TABBED separated CSV
			if ( ! file_exists( $this->ns_fba->trans_log_path ) ) {
				$head = '';
				$head .= "DATE\t";
				$head .= "INTERNAL\t";
				$head .= "DETECTED\t";
				$head .= "STR BEFORE\t";
				$head .= "ISO-8859-1\t";
				$head .= "UTF-8\t";
				$head .= "UTF-8 STRICT\t";
				$head .= "STR AFTER\n";
				error_log( $head, 3, $this->ns_fba->trans_log_path );
			}
			$row = '';
			$row .= date( 'm-d-Y H:i:s', time() ) . "\t";
			$row .= mb_internal_encoding() . "\t";
			$row .= mb_detect_encoding( $string_before ) . "\t";
			$row .= $string_before . "\t";
			$row .= ( mb_detect_encoding( $string_before, 'ISO-8859-1' ) ) ? "YES\t" : "NO\t";
			$row .= ( mb_detect_encoding( $string_before, 'UTF-8' ) ) ? "YES\t" : "NO\t";
			$row .= ( mb_detect_encoding( $string_before, 'UTF-8', true ) ) ? "YES\t" : "NO\t";
			$row .= $string_after;
			$row .= "\n";
			error_log( $row, 3, $this->ns_fba->trans_log_path );
		}

		/**
			 * Create an address for the fulfillment order
			 */
		function create_fulfillment_address( $order_id ) {
			// set initial address parameters and a copy to compare converted versus original (raw)
			$shipping_name = get_post_meta( $order_id, '_shipping_first_name', true ) .
							 ' ' . get_post_meta( $order_id, '_shipping_last_name', true );
			$company_name = get_post_meta( $order_id, '_shipping_company', true );
			$address_line1 = get_post_meta( $order_id, '_shipping_address_1', true );
			$address_line2 = get_post_meta( $order_id, '_shipping_address_2', true );
			$address_city = get_post_meta( $order_id, '_shipping_city', true );
			$address_state = get_post_meta( $order_id, '_shipping_state', true );

			$shipping_name_raw = $shipping_name;
			$company_name_raw = $company_name;
			$address_line1_raw = $address_line1;
			$address_line2_raw = $address_line2;
			$address_city_raw = $address_city;
			$address_state_raw = $address_state;

			// if the conversion override option is OFF, then handle non-Latin-1 characters to prevent Amazon from rejecting the order
			if ( ! $this->ns_fba->utils->isset_on( $this->ns_fba->options['ns_fba_encode_convert_bypass'] ) ) {
				$shipping_name = iconv( 'UTF-8', 'ASCII//TRANSLIT//IGNORE', $shipping_name_raw );
				$company_name = iconv( 'UTF-8', 'ASCII//TRANSLIT//IGNORE', $company_name_raw );
				$address_line1 = iconv( 'UTF-8', 'ASCII//TRANSLIT//IGNORE', $address_line1_raw );
				$address_line2 = iconv( 'UTF-8', 'ASCII//TRANSLIT//IGNORE', $address_line2_raw );
				$address_city = iconv( 'UTF-8', 'ASCII//TRANSLIT//IGNORE', $address_city_raw );
				$address_state = iconv( 'UTF-8', 'ASCII//TRANSLIT//IGNORE', $address_state_raw );
				// try an alternate method -- does NOT work as well, but keeping for reference:
				/**
					$shipping_name = utf8_encode( $shipping_name_raw );
					$company_name = utf8_encode( $company_name_raw );
					$address_line1 = utf8_encode( $address_line1_raw );
					$address_line2 = utf8_encode( $address_line2_raw );
					$address_city = utf8_encode( $address_city_raw );
					$address_state = utf8_encode( $address_state_raw );
					 **/
			}

			// log the translation before and after on conversion failures so that we can compile a good model of the issues
			/* CHANGING THIS to log everything for solving tough support cases
			if ( strpos( $shipping_name, '?' ) !== false ) $this->check_and_log_charset( $shipping_name_raw, $shipping_name );
			if ( strpos( $company_name, '?' ) !== false ) $this->check_and_log_charset( $company_name_raw, $company_name );
			if ( strpos( $address_line1, '?' ) !== false ) $this->check_and_log_charset( $address_line1_raw, $address_line1 );
			if ( strpos( $address_line2, '?' ) !== false ) $this->check_and_log_charset( $address_line2_raw, $address_line2 );
			if ( strpos( $address_city, '?' ) !== false ) $this->check_and_log_charset( $address_city_raw, $address_city );
			if ( strpos( $address_state, '?' ) !== false ) $this->check_and_log_charset( $address_state_raw, $address_state );
			//---------------------------------
			// disabling character encoding logging
			//---------------------------------
			$this->check_and_log_charset( $shipping_name_raw, $shipping_name );
			$this->check_and_log_charset( $company_name_raw, $company_name );
			$this->check_and_log_charset( $address_line1_raw, $address_line1 );
			$this->check_and_log_charset( $address_line2_raw, $address_line2 );
			$this->check_and_log_charset( $address_city_raw, $address_city );
			$this->check_and_log_charset( $address_state_raw, $address_state );
			*/

			// create FBA outbound address object
			$address = new FBAOutboundServiceMWS_Model_Address();
			$address->setName( $shipping_name );

			if ( '' !== $company_name ) {
				$address->setLine1( $company_name );
				$address->setLine2( $address_line1 );
				$address->setLine3( $address_line2 );
			} else {
				$address->setLine1( $address_line1 );
				$address->setLine2( $address_line2 );
			}
			$address->setCity( $address_city );
			$address->setStateOrProvinceCode( $address_state );
			$address->setPostalCode( get_post_meta( $order_id, '_shipping_postcode', true ) );
			$address->setCountryCode( get_post_meta( $order_id, '_shipping_country', true ) );

			// only include the phone number if the option to exclude it is NOT set
			if ( ! $this->ns_fba->utils->isset_on( $this->ns_fba->options['ns_fba_exclude_phone'] ) ) {
				$address->setPhoneNumber( get_post_meta( $order_id, '_billing_phone', true ) );
			} else {
				$address->setPhoneNumber( '' );
			}

			return $address;
		}

		/**
			 * Create an html formatted address from a fulfillment address for display especially in the logs
			 */
		function create_fulfillment_address_html( FBAOutboundServiceMWS_Model_Address $address ) {

			$shipping_address_html =
				'Shipping Name  : ' . $address->getName() . '<br />' .
				'Address Line 1 : ' . $address->getLine1() . '<br />' .
				'Address Line 2 : ' . $address->getLine2() . '<br />' .
				'Address City   : ' . $address->getCity() . '<br />' .
				'Address State  : ' . $address->getStateOrProvinceCode() . '<br />' .
				'Address Postal : ' . $address->getPostalCode() . '<br />' .
				'Address Country: ' . $address->getCountryCode() . '<br />' .
				'Address Phone  : ' . $address->getPhoneNumber() . '<br />';

			return $shipping_address_html;
		}

		/**
			 * Returns the marketplace ID to be used for an order, based on the order's
			 * shipping country.
			 *
			 * @param WC_Order order
			 * @param string default
			 * @return string
			 * @since 3.1.6
			 */
		protected function get_marketplace_id_for_order( $order, $default ) {
			// Return the default marketplace ID if the configured region is not Europe
			// @link https://app.codeable.io/tasks/74910
			if ( $this->ns_fba->options['ns_fba_service_url'] != 'https://mws-eu.amazonservices.com' ) {
				return $default;
			}

			// Map country codes against the respective Amazon Marketplace IDs
			// @link https://docs.developer.amazonservices.com/en_US/dev_guide/DG_Endpoints.html
			$marketplace_ids = array(
				'ES' => 'A1RKKUPIHCS9HS',
				'GB' => 'A1F83G8C2ARO7P',
				'FR' => 'A13V1IB3VIYZZH',
				'DE' => 'A1PA6795UKMFR9',
				'IT' => 'APJ6JRA9NG5V4',
			);

			// Return the appropriate marketplace ID, or the default if the shipping country
			// doesn't match any of the marketplaces
			return isset( $marketplace_ids[ $order->get_shipping_country() ] ) ? $marketplace_ids[ $order->get_shipping_country() ] : $default;
		}

		/**
			 * Send a fulfillment order to FBA
			 */
		function send_fulfillment_order( $order_id, $is_manual_send = false ) {
			try {
				if ( $this->ns_fba->is_debug ) {
					error_log( '<b>Step 2 of 8: </b>INSIDE send_fulfillment_order AND INSIDE try<br /><br />', 3, $this->ns_fba->debug_log_path );
				}
				// NOTE: Order ID = Post ID and all shipping info is stored as post meta whether
				// 		 the customer has an account or not, so this is guest checkout safe.

				$order = new WC_Order( $order_id );
				$order_items = $order->get_items();

				// we need to define the order number early so that it's available to error reporting
				$order_number = $this->ns_fba->options['ns_fba_order_prefix'] . $order->get_order_number();

				$address = new FBAOutboundServiceMWS_Model_Address();
				$address = $this->create_fulfillment_address( $order_id );

				// define these early so that they are available for the error logs if an exception is caught before they are used
				$shipping_address_html = $this->create_fulfillment_address_html( $address );
				$order_shipping_method = $order->get_shipping_method();
				$shipping_speed_to_fba = '';

				// we need to keep track of whether or not there are ANY items in the order
				// that are products set to fulfill through FBA. Assume not.
				$is_any_order_item_fba = false;
				$fulfill_item_count = 0;
				$total_item_count = 0;
				// set up the order item list to build as we go
				$order_list = new FBAOutboundServiceMWS_Model_CreateFulfillmentOrderItemList();
				// set up a SKU tracking array for items in FBA to update inventory levels
				$fba_skus = array();
				foreach ( $order_items as $item ) {
					$total_item_count++;
					// use WooCommerce to get either a normal or variable product from the item as a WC_Product
					$item_product = $order->get_product_from_item( $item );
					// set a local int product id
					$product_id = intval( $item['product_id'] );

					// run through the Order Level smart fulfillment rules
					$is_order_item_fba = $this->ns_fba->utils->is_order_item_amazon_fulfill( $order, $item, $item_product, $product_id, $is_manual_send );

					if ( $is_order_item_fba ) {
						$is_any_order_item_fba = true;
						$fulfill_item_count++;
						// add item properties to the request
						// create request order item
						$order_item = new FBAOutboundServiceMWS_Model_CreateFulfillmentOrderItem();
						// set item parameters
						$order_item_sku = '';
						// check if this is a variable product or not so that we can conditionally set the right sku
						//----------------------------------------------------------
						if ( $this->ns_fba->wc->is_woo_version( '3.0' ) ) {
							$parent_id = $item_product->get_parent_id();
							if ( $parent_id ) {
								$parent_product = wc_get_product( $parent_id );
								if ( get_post_meta( $parent_product->get_id(), 'ns_fba_send_parent_sku', true ) == 'yes' ) {
									$order_item_sku = $parent_product->get_sku();
								} else {
									$order_item_sku = $item_product->get_sku();
								}
							} else {
								$order_item_sku = $item_product->get_sku();
							}
						} else {
							if ( is_object( $item_product->parent ) && $item_product->parent->is_type( 'variable' ) ) {
								if ( get_post_meta( $item_product->parent->get_id(), 'ns_fba_send_parent_sku', true ) == 'yes' ) {
									$order_item_sku = $item_product->parent->get_sku();
								} else {
									$order_item_sku = $item_product->get_sku();
								}
							} else {
								$order_item_sku = $item_product->get_sku();
							}
						}
						$order_item->setSellerSKU( $order_item_sku );
						$order_item->setSellerFulfillmentOrderItemId( 'item-' . $fulfill_item_count . '-' . $order_item_sku );
						$order_item->setQuantity( $item['qty'] );
						// required for international orders
						$item_price	= new FBAOutboundServiceMWS_Model_Currency();
						$marketplace_id = $this->get_marketplace_id_for_order( $order, NS_MARKETPLACE_ID );
						// handle custom currency override
						if ( isset( $this->ns_fba->options['ns_fba_currency_code'] ) && $this->ns_fba->options['ns_fba_currency_code'] !== '' ) {
							$item_price->setCurrencyCode( $this->ns_fba->options['ns_fba_currency_code'] );
							$order_item->setPerUnitDeclaredValue( $item_price );
							$item_price->setValue( $item_product->get_price() * intval( $this->ns_fba->options['ns_fba_currency_conversion'] ) );
						} elseif ( 'A1F83G8C2ARO7P' == $marketplace_id ) {
							// handle auto currency override for UK in EU region
							$item_price->setCurrencyCode( 'GBP' );
							$order_item->setPerUnitDeclaredValue( $item_price );
							$item_price->setValue( $item_product->get_price() );
						} elseif ( 'A1RKKUPIHCS9HS' == $marketplace_id ||
						           'A13V1IB3VIYZZH' == $marketplace_id ||
						           'A1PA6795UKMFR9' == $marketplace_id ||
						           'APJ6JRA9NG5V4' == $marketplace_id
								 ) {
							// handle auto currency override for rest of EU region
							$item_price->setCurrencyCode( 'EUR' );
							$order_item->setPerUnitDeclaredValue( $item_price );
							$item_price->setValue( $item_product->get_price() );
						} else {
							$item_price->setCurrencyCode( get_woocommerce_currency() );
							$order_item->setPerUnitDeclaredValue( $item_price );
							$item_price->setValue( $item_product->get_price() );
						}
						// for COD orders in Japan, add unit price because it's required
						if ( 'A1VC38T7YXB528' === $marketplace_id && 'cod' === $order->get_payment_method() ) {
							// per Amazon error: do NOT setPerUnitPrice which is only for Cash on Delivery orders
							$order_item->setPerUnitPrice( $item_price );
						}
						// add this item to the order item list
						$order_list->withmember( $order_item );
						// add this item to the fba skus array for inventory update
						array_push( $fba_skus, $item_product->get_sku() );
					} else {
						// this order item was not sent to FBA
						// report on why at the individual reason level inside is_order_item_amazon_fulfill
						//$order->add_order_note( __( 'The Order Item with SKU: ' . $item_product->get_sku() . ' was not sent to FBA due to its product settings or the NS FBA Smart Fulfillment Settings.', $this->ns_fba->text_domain ) );
					}// End if().
				}// End foreach().
				
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
				
				// if there are zero FBA order items then we're done
				if ( ! $is_any_order_item_fba ) {
					if ( $this->ns_fba->is_debug ) {
						error_log( '<b>SKIPPING STEPS: </b>zero products in this order are set to fulfill with FBA<br /><br />', 3, $this->ns_fba->debug_log_path );
					}
					$order->add_order_note( __( 'There were NO items in this Order that were eligible to be sent to FBA based on their [Fulfill with Amazon FBA] product settings. Please double-check the product(s) and try again.', $this->ns_fba->text_domain ) );
					return $order_id;
				} elseif ( $this->ns_fba->utils->isset_on( $this->ns_fba->options['ns_fba_perfection_mode'] ) && $total_item_count > $fulfill_item_count ) {
					// if we're in Perfection Mode but not ALL of the order items passed fullfillmet rules, then we're done
					if ( $this->ns_fba->is_debug ) {
						error_log( '<b>SKIPPING STEPS: </b>Perfection Mode is active and Not ALL products in this order are set to fulfill with FBA<br /><br />', 3, $this->ns_fba->debug_log_path );
					}
					$order->add_order_note( __( 'Perfection Mode is active and Not ALL products in this order are set to Fulfill with Amazon, so we did not try to send this order to FBA.', $this->ns_fba->text_domain ) );
					return $order_id;
				} else {
					// otherwise, let's send some stuff to FBA!!!

					// but not so fast - first, we must make sure there are no order-level rules that will prevent this order from being sent
					// run through the Order Level smart fulfillment rules
					if ( ! $this->ns_fba->utils->is_order_amazon_fulfill( $order, $is_manual_send ) ) {
						// this generic condition should never happen because we should be throwing all exceptions back from is_order_amazon_fulfill()
						// but just in case...
						throw new Exception( __( 'The Order cannot be sent to FBA due to the configured settings or products.', $this->ns_fba->text_domain ) );
					}

					// ok, now we can send some stuff to FBA for real!!!

					// unless we need to bail because of problems with the address
					// if there are uncovertable encoded characters and the encode check override option is OFF then prevent this order from going through
					if ( strpos( $shipping_address_html, '?' ) !== false && ! $this->ns_fba->utils->isset_on( $this->ns_fba->options['ns_fba_encode_check_override'] ) ) {
						throw new Exception( __( 'The Destination address contains encoded characters that cannot be properly converted. Please manually update shipping address and manually submit to Amazon FBA.', $this->ns_fba->text_domain ) );
					}

					// now, we're officially ready to go!!!!!!

					if ( $this->ns_fba->is_debug ) {
						error_log( '<b>Step 4 of 8: </b>BEFORE new FBAOutboundServiceMWS_Model_CreateFulfillmentOrderRequest<br /><br />', 3, $this->ns_fba->debug_log_path );
					}
					// -----------------------------------------------------------------------------------------------------------------
					// build the fulfillment order request
					// -----------------------------------------------------------------------------------------------------------------
					$service = $this->create_service_outbound();
					$request = new FBAOutboundServiceMWS_Model_CreateFulfillmentOrderRequest();
					if ( defined( 'NS_MWS_AUTH_TOKEN' ) ) {
						$request->setMWSAuthToken( NS_MWS_AUTH_TOKEN );
					}
					$request->setSellerId( NS_MERCHANT_ID );

					// Set the marketplace ID based on the country from the order
					// @since 3.1.6
					$marketplace_id = $this->get_marketplace_id_for_order( $order, NS_MARKETPLACE_ID );
					// ONLY USE setMarketplaceId !!! - using setMarketPlace and/or multiple will break signature API
					$request->setMarketplaceId( $marketplace_id );

					// order prefix is configured on settings page
					$request->setSellerFulfillmentOrderId( $order_number );
					$request->setDisplayableOrderId( $order_number );
					// timestamp in ISO 8601
					$request->setDisplayableOrderDateTime( date( 'c', time() ) );
					// enable filtering order comment sent to Amazon for custom integrations
					$comment = isset( $this->ns_fba->options['ns_fba_order_comment'] ) ? $this->ns_fba->options['ns_fba_order_comment'] : 'Thank you for your order!';
					$comment = apply_filters( 'ns_fba_order_comment', $comment, $order_id );
					$request->setDisplayableOrderComment( $comment ?: 'Thank you for your order!' );

					// enable COD for Japan marketplace
					if ( 'A1VC38T7YXB528' === $marketplace_id && 'cod' === $order->get_payment_method() ) {
						$currency_code =  isset( $this->ns_fba->options['ns_fba_currency_code'] ) && $this->ns_fba->options['ns_fba_currency_code'] !== ''
							? $this->ns_fba->options['ns_fba_currency_code']
							: get_woocommerce_currency();
						$request->setCODSettings(
							new FBAOutboundServiceMWS_Model_CODSettings( [
								'IsCODRequired'  => true,
								'CODCharge'      => new FBAOutboundServiceMWS_Model_Currency( [
									'CurrencyCode' => $currency_code,
									'Value'        => apply_filters( 'ns_fba_cod_charge', 0 ),
								] ),
								'ShippingCharge' => new FBAOutboundServiceMWS_Model_Currency( [
									'CurrencyCode' => $currency_code,
									'Value'        => apply_filters( 'ns_fba_cod_shipping', $order->get_shipping_total() ),
								] ),
							] )
						);
					}

					// -----------------------------------------------------------------------------------------------------------------
					// add the order items from the order_list above
					// -----------------------------------------------------------------------------------------------------------------
					$request->setItems( $order_list );

					// -----------------------------------------------------------------------------------------------------------------
					// set the shipping address from above
					// -----------------------------------------------------------------------------------------------------------------
					$request->setDestinationAddress( $address );

					// -----------------------------------------------------------------------------------------------------------------
					// determine shipping speed from mapped settings or fall through to default
					// and use wc_clean on option values here because the $order_shipping_method
					// value was passed through it - otherwise, something like a double space in
					// the shipping method title will prevent a match from taking place
					// -----------------------------------------------------------------------------------------------------------------
					if ( $this->ns_fba->options['ns_fba_shipping_speed_priority'] != '' &&
						wc_clean( $this->ns_fba->options['ns_fba_shipping_speed_priority'] ) == $order_shipping_method ) {
						$request->setShippingSpeedCategory( 'Priority' );
						$shipping_speed_to_fba = 'Priority';
					} elseif ( $this->ns_fba->options['ns_fba_shipping_speed_expedited'] != '' &&
						wc_clean( $this->ns_fba->options['ns_fba_shipping_speed_expedited'] ) == $order_shipping_method ) {
						$request->setShippingSpeedCategory( 'Expedited' );
						$shipping_speed_to_fba = 'Expedited';
					} elseif ( $this->ns_fba->options['ns_fba_shipping_speed_standard'] != '' &&
						wc_clean( $this->ns_fba->options['ns_fba_shipping_speed_standard'] ) == $order_shipping_method ) {
						$request->setShippingSpeedCategory( 'Standard' );
						$shipping_speed_to_fba = 'Standard';
					} else {
						$request->setShippingSpeedCategory( $this->ns_fba->options['ns_fba_shipping_speed'] );
						$shipping_speed_to_fba = $this->ns_fba->options['ns_fba_shipping_speed'];
					}

					$request->setFulfillmentPolicy( $this->ns_fba->options['ns_fba_fulfillment_policy'] );

					// -----------------------------------------------------------------------------------------------------------------
					// amazon shipping notification list
					// -----------------------------------------------------------------------------------------------------------------
					$notify_list = new FBAOutboundServiceMWS_Model_NotificationEmailList();
					// add the customer email if the disable shipping email option is OFF
					if ( get_post_meta( $order_id, '_billing_email', true ) && ! $this->ns_fba->utils->isset_on( $this->ns_fba->options['ns_fba_disable_shipping_email'] ) ) {
						$notify_list->withmember( get_post_meta( $order_id, '_billing_email', true ) );
					}
					// add the admin email
					if ( $this->ns_fba->utils->isset_on( $this->ns_fba->options['ns_fba_notify_email'] ) ) {
						$notify_list->withmember( $this->ns_fba->options['ns_fba_notify_email'] );
					} elseif ( get_option( 'admin_email' ) ) {
						// based on user feedback if fba_notify_email is BLANK then do NOT send the admin address to Amazon instead
						//$notify_list->withmember( get_option( 'admin_email' ) );
					}
					$request->setNotificationEmailList( $notify_list );

					if ( $this->ns_fba->is_debug ) {
						error_log( '<b>Step 5 of 8: </b>AFTER new FBA $request configured<br /><br />', 3, $this->ns_fba->debug_log_path );
						error_log( '<b>Step 6 of 8: </b>INSIDE try sending $request to FBA<br /><br />', 3, $this->ns_fba->debug_log_path );
					}

					// -----------------------------------------------------------------------------------------------------------------
					// send the request and capture the response
					// -----------------------------------------------------------------------------------------------------------------
					$response = $service->CreateFulfillmentOrder( $request );
					$dom = new DOMDocument();
					$dom->loadXML( $response->toXML() );
					$dom->preserveWhiteSpace = false;
					$dom->formatOutput = true;
					// tracking array for inventory updates
					$inventory_updates = array();
					// update the inventory numbers if sync is enabled
					if ( $this->ns_fba->utils->isset_on( ( $this->ns_fba->options['ns_fba_update_inventory'] ) ) ) {
						$service_inventory = $this->ns_fba->inventory->create_service_inventory();
						foreach ( $fba_skus as $fba_sku ) {
							$inventory = $this->ns_fba->inventory->get_sku_inventory( $service_inventory, $fba_sku );
							// TODO: check for errors
							if ( '' == $inventory['message'] ) {
								$woo_product = $this->ns_fba->wc->get_product_by_sku( $fba_sku );
								$stock = $inventory['number'];
								$message_prefix = 'WC Order ' . $order_id . ': >> ';
								$this->ns_fba->inventory->set_product_stock( $woo_product, $fba_sku, $message_prefix, $stock );
								array_push( $inventory_updates, 'Updated ' . $fba_sku . ' stock to ' . $inventory['number'] );
							} else {
								array_push( $inventory_updates, 'Error updating ' . $fba_sku . ' stock to ' . $inventory['number'] .
								' with FBA message: ' . $inventory['message'] );
							}
						}
					} else {
						array_push( $inventory_updates, __( 'Inventory sync is not enabled in the settings.', $this->ns_fba->text_domain ) );
					}
					// -----------------------------------------------------------------------------------------------------------------
					// log the order, address, item data, and inventory numbers if applicable
					// -----------------------------------------------------------------------------------------------------------------
					$summary = $this->create_log_summary_html( $order_shipping_method, $shipping_speed_to_fba, $shipping_address_html, $order );
					error_log( "<h2>Order # $order_number Successfully Sent to FBA</h2>", 3, $this->ns_fba->log_path );
					error_log( $summary, 3, $this->ns_fba->log_path );
					error_log( '<h3>Inventory Updates:</h3>', 3, $this->ns_fba->log_path );
					error_log( @Kint::dump( $inventory_updates ), 3, $this->ns_fba->log_path );
					error_log( '<h3>Raw Order Data:</h3>', 3, $this->ns_fba->log_path );
					error_log( @Kint::dump( $order ), 3, $this->ns_fba->log_path );
					error_log( @Kint::dump( $order_items ), 3, $this->ns_fba->log_path );
					error_log( @Kint::dump( $service ), 3, $this->ns_fba->log_path );
					error_log( @Kint::dump( $request ), 3, $this->ns_fba->log_path );
					error_log( @Kint::dump( $response ), 3, $this->ns_fba->log_path );
					error_log( @Kint::dump( $dom->saveXML() ), 3, $this->ns_fba->log_path );
					// fulfillment order request was successfully sent, update the status, add order note
					if ( $this->ns_fba->utils->isset_on( $this->ns_fba->options['ns_fba_automatic_completion'] ) ) {
						$ns_wc_term = 'completed';
						error_log( 'Setting for Mark Orders Complete active. Order Status set to Completed.', 3, $this->ns_fba->log_path );
					} elseif ( $fulfill_item_count == $total_item_count ) {
						$ns_wc_term = 'sent-to-fba';
						error_log( 'All items fulfilled with FBA. Order Status set to Sent to FBA', 3, $this->ns_fba->log_path );
					} else {
						$ns_wc_term = 'part-to-fba';
						error_log( 'Some items fulfilled with FBA. Order Status set to Partial to FBA', 3, $this->ns_fba->log_path );
					}

					$this->set_order_status_safe( $order_id, $ns_wc_term );

					$order->add_order_note( '<a href="' . $this->ns_fba->log_url . '" target="_blank">' . __( 'Successfully submitted order to FBA', $this->ns_fba->text_domain ) . '</a> (' . __( 'click for full log', $this->ns_fba->text_domain ) . ').' );
					// add the order meta _sent_to_fba so that we can display tracking info later
					update_post_meta( $order_id, '_sent_to_fba', date( 'm-d-Y H:i:s', time() ) );
					return $order_id;
				}// End if().
			} catch ( Exception $ex ) {
				// Increasing max_depth is necessary to capture data down to the order item detail level.
				Kint::$max_depth = 12;
				$message = "<h2>ERROR sending Order # $order_number to FBA</h2>" .
					   '<b>Error Message: </b><br /><span style="color:red;">' . $ex->getMessage() . '</span><br /><br />';
				$summary = $this->create_log_summary_html( $order_shipping_method, $shipping_speed_to_fba, $shipping_address_html, $order );
				error_log( $message, 3, $this->ns_fba->err_log_path );
				error_log( $summary, 3, $this->ns_fba->err_log_path );
				error_log( '<h3>Raw Error Data:</h3>', 3, $this->ns_fba->err_log_path );
				error_log( @Kint::dump( $ex ), 3, $this->ns_fba->err_log_path );
				error_log( '<h3>Raw Order Data: </h3>', 3, $this->ns_fba->err_log_path );
				error_log( @Kint::dump( $order ), 3, $this->ns_fba->err_log_path );
				error_log( @Kint::dump( $order_items ), 3, $this->ns_fba->err_log_path );
				error_log( @Kint::dump( $service ), 3, $this->ns_fba->err_log_path );
				error_log( @Kint::dump( $request ), 3, $this->ns_fba->err_log_path );
				error_log( @Kint::dump( $this->ns_fba->options ), 3, $this->ns_fba->err_log_path );

				// TODO: Build a scalable mechanism to allow individual settings to trigger a FAIL or be bypassed.
				if ( strpos( $ex->getMessage(), 'International fulfillment' ) !== false ) {
					// For now, we just bypass the international one rather than FAIL, since that might sometimes not be considered a FAIL condition
					$order->add_order_note( __( '<b>Warning:', $this->ns_fba->text_domain ) . '</b><br /><span>' . $ex->getMessage() . '</span>' );
				} elseif ( strpos( $ex->getMessage(), 'disabled for FBA' ) !== false ) {
					// Also, bypass for disabled shipping methods
					$order->add_order_note( __( '<b>Warning:', $this->ns_fba->text_domain ) . '</b><br /><span>' . $ex->getMessage() . '</span>' );
				} else {
					// for every other exception condition treat it as a FAIL
					// $ns_wc_term = 'completed';
					$ns_wc_term = 'fail-to-fba';
					$this->set_order_status_safe( $order_id, $ns_wc_term );
					$order->add_order_note( '<a href="' . $this->ns_fba->err_log_url . '" target="_blank">' . __( 'Failed to submit order to FBA', $this->ns_fba->text_domain ) . '</a> (' . __( 'click for full log', $this->ns_fba->text_domain ) . '). <b>' . __( 'Error Message:', $this->ns_fba->text_domain ) . '</b><br /><span style="color:red;">' . $ex->getMessage() . '</span>' );
					// -----------------------------------------------------------------------------------------------------------------
					// send an email notification of failure to the site admin
					// -----------------------------------------------------------------------------------------------------------------
					$this->ns_fba->utils->mail_message( $message . __( 'Full details: ', $this->ns_fba->text_domain ) . $this->ns_fba->err_log_url, __( 'FBA Order Fulfillment Request FAIL', $this->ns_fba->text_domain ) );
				}

				return $order_id;
			} // End try().
		} //send_fulfillment_order

		function create_log_summary_html( $shipping, $speed, $address, $order ) {
			$summary = '';
			$summary .= '<b>Date: </b>' . date( 'c', time() ) . '<br /><br />';
			$summary .= '<b>Order Shipping Method: </b>' . $shipping . '<br />';
			$summary .= '<b>Shipping Speed to FBA: </b>' . $speed . '<br />';
			$summary .= '<b>Shipping to Address:</b><br />' . $address . '<br /><br />';
			$summary .= '<b>All Order Items (some of these might be excluded in request to fulfillment based on product settings):</b><br />';

			$style = ' style="' . "text-align:left; vertical-align:middle; border: 1px solid #eee; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; word-wrap:break-word;" . '" ';
			$summary .= '<div id="order-items-div" class="order-items"><table id="order-items-table" class="order-items-details"><tbody><tr><th' . $style . '>Product</th><th' . $style . '>QTY</th><th' . $style . '>Price</th></tr>';

			if ( $this->ns_fba->wc->is_woo_version( '3.0' ) ) {
				$summary .= wc_get_email_order_items( $order );
			} else {
				$summary .= $order->email_order_items_table( array(
					'show_sku' => true,
				) );
			}

			$summary .= '</tbody></table></div><br />';
			return $summary;
		}

		// crawl through the shipping info recursively and build up the shipping info string
		function recurse_order_shipping_info( $info, $results = '' ) {
			foreach ( $info as $key => $element ) {
				if ( $key == 'FulfillmentShipmentStatus' ) {
					$results .= 'Shipping Status:<b> ' . $element . '</b><br />';
				}
				if ( $key == 'EstimatedArrivalDateTime' && strpos( $results, 'Estimated Arrival' ) === false ) {
					$results .= 'Estimated Arrival:<b> ' . date( 'm-d-Y', strtotime( $element ) ) . '</b><br />';
				}
				if ( $key == 'CarrierCode' ) {
					$results .= 'Shipping Carrier:<b> ' . $element . '</b><br />';
				}
				if ( $key == 'TrackingNumber' ) {
					$results .= 'Tracking Number:<b> ' . $element . '</b><br />';
				}
				// recurse
				$results = $this->recurse_order_shipping_info( $element, $results );
			}
			return $results;
		}

		function get_fulfillment_order_shipping_info( $order_id ) {
			// this is hooked into the front end view order so the customer can see tracking info
			// however we need to check for the order meta _sent_to_fba so that we don't look up orders that were never sent to Amazon
			// the order meta _sent_to_fba is added to the order upon a successful call of send_fulfillment_order
			if ( empty( get_post_meta( $order_id, '_sent_to_fba', true ) ) ) {
				// since this is a hook that gets displayed we need to return nothing
				return '';
			}

			$order = new WC_Order( $order_id );
			$order_number = $this->ns_fba->options['ns_fba_order_prefix'] . $order->get_order_number();

			// TESTING
			//$order_number = 'OSANA-3443';

			try {
				// build the fulfillment order request
				$service = $this->create_service_outbound();
				$request = new FBAOutboundServiceMWS_Model_GetFulfillmentOrderRequest();
				if ( defined( 'NS_MWS_AUTH_TOKEN' ) ) {
					$request->setMWSAuthToken( NS_MWS_AUTH_TOKEN );
				}
				$request->setSellerId( NS_MERCHANT_ID );

				// Set the marketplace ID based on the country from the order
				// @since 3.1.6
				$marketplace_id = $this->get_marketplace_id_for_order( $order, NS_MARKETPLACE_ID );
				// USE setMarketplace here !!! - GetFulfillmentOrderRequest does not have setMarketplaceId (ugh MWS!)
				$request->setMarketplace( $marketplace_id );

				$request->setSellerFulfillmentOrderId( $order_number );
				$response = $service->getFulfillmentOrder( $request );

				$xml = simplexml_load_string( $response->toXML() );

				$results = $this->recurse_order_shipping_info( $xml );
				$order->add_order_note( 'Order Tracking info viewed on Order page:<br/>' . $results );
				$results = '<section class="woocommerce-order-details"><h2>Order Tracking Info</h2>' . $results . '</section>';
				return print_r( $results, false );

			} catch ( Exception $ex ) {
				error_log( "<h2>ERROR Checking status of Order # $order_number</h2>", 3, $this->ns_fba->err_log_path );
				error_log( '<b>Error Message: </b><br /><span style="color:red;">' . $ex->getMessage() . '</span><br /><br />', 3, $this->ns_fba->err_log_path );
				error_log( '<h3>Raw Error Data:</h3>', 3, $this->ns_fba->err_log_path );
				error_log( @Kint::dump( $ex ), 3, $this->ns_fba->err_log_path );
				error_log( '<h3>Raw Order Data: </h3>', 3, $this->ns_fba->err_log_path );
				error_log( @Kint::dump( $service ), 3, $this->ns_fba->err_log_path );
				$order_note = '<a href="' . $this->ns_fba->err_log_url . '" target="_blank">' . __( 'Failed to check order status', $this->ns_fba->text_domain ) . '</a> (' . __( 'click for full log', $this->ns_fba->text_domain ) . '). <b>' . __( 'Error Message:', $this->ns_fba->text_domain ) . '</b><br /><span style="color:red;">' . $ex->getMessage() . '</span>';
				$order->add_order_note( $order_note );

				// we still need to complete the hook
				return print_r( '<h2>Order Tracking Info</h2>' . $ex->getMessage(), false );
			}
		}

		function sync_fulfillment_order_status() {
			// get oldest 20 (to stay under throttling limit) orders in wc-success-to-fba status
			$orders = get_posts( array(
				'numberposts' 	=> 20,
				'order'			=> 'ASC',
				'post_type'  	=> wc_get_order_types(),
				'post_status' 	=> array( 'wc-sent-to-fba', 'wc-part-to-fba' ),
			) );

			foreach ( $orders as $f_order ) {
				$order = new WC_Order( $f_order->ID );
				$order_id = $f_order->ID;
				$order_number = $this->ns_fba->options['ns_fba_order_prefix'] . $order->get_order_number();

				try {
					// build the fulfillment order request
					$service = $this->create_service_outbound();
					$request = new FBAOutboundServiceMWS_Model_GetFulfillmentOrderRequest();
					if ( defined( 'NS_MWS_AUTH_TOKEN' ) ) {
						$request->setMWSAuthToken( NS_MWS_AUTH_TOKEN );
					}
					$request->setSellerId( NS_MERCHANT_ID );

					// Set the marketplace ID based on the country from the order
					// @since 3.1.6
					$marketplace_id = $this->get_marketplace_id_for_order( $order, NS_MARKETPLACE_ID );
					// ARG CRAZY AMAZON LIB - ListAllFulfillmentOrdersRequest() ONLY has setMarketplace() which is
					// inconsistent with other request models that have and require setMarketplaceId
					$request->setMarketplace( NS_MARKETPLACE_ID );

					$request->setSellerFulfillmentOrderId( $order_number );
					$response = $service->getFulfillmentOrder( $request );

					//$xml = simplexml_load_string($response->toXML());
					$dom = new DOMDocument;
					$dom->loadXML( $response->toXML() );
					$statuses = $dom->getElementsByTagName( 'FulfillmentOrderStatus' );
					$fullfillment_status = '';
					foreach ( $statuses as $status ) {
						echo $order_number . '    ' . $f_order->post_type . '    ' . $f_order->post_status . ' FBA status: ' . $status->nodeValue . '<br />';
						$fullfillment_status = $status->nodeValue;
					}
					// update the order status based on the FBA status returned
					switch ( $fullfillment_status ) {
						case 'COMPLETE':
							$this->set_order_status_safe( $order_id, 'completed' );
							$order->add_order_note( 'Synced fulfillment status with FBA and found COMPLETE. Updated order status from Sent to FBA to Completed.' );
							break;
						case 'COMPLETE_PARTIALLED':
							$this->set_order_status_safe( $order_id, 'completed' );
							$order->add_order_note( 'Synced fulfillment status with FBA and found COMPLETE_PARTIALLED. Updated order status from Sent to FBA to Completed.' );
							break;
						case 'CANCELLED':
							$this->set_order_status_safe( $order_id, 'cancelled' );
							$order->add_order_note( 'Synced fulfillment status with FBA and found CANCELLED. Updated order status from Sent to FBA to Cancelled.' );
							break;
						case 'RECEIVED':
							break;
						case 'INVALID':
							break;
						case 'PROCESSING':
							break;
						case 'UNFULFILLABLE':
							break;
						default:
							break;
					}
				} catch ( Exception $ex ) {
					error_log( "<h2>ERROR Syncing status of Order # $order_number</h2>", 3, $this->ns_fba->err_log_path );
					error_log( '<b>Error Message: </b><br /><span style="color:red;">' . $ex->getMessage() . '</span><br /><br />', 3, $this->ns_fba->err_log_path );
					error_log( '<h3>Raw Error Data:</h3>', 3, $this->ns_fba->err_log_path );
					error_log( @Kint::dump( $ex ), 3, $this->ns_fba->err_log_path );
					error_log( '<h3>Raw Order Data: </h3>', 3, $this->ns_fba->err_log_path );
					error_log( @Kint::dump( $service ), 3, $this->ns_fba->err_log_path );

					echo $order_number . '    ' . $f_order->post_type . '    ' . $f_order->post_status . ' FBA status err: ' . $ex->getMessage() . '<br />';

					$order_note = '<a href="' . $this->ns_fba->err_log_url . '" target="_blank">' . __( 'Failed to sync order status from FBA', $this->ns_fba->text_domain ) . '</a> (' . __( 'click for full log', $this->ns_fba->text_domain ) . '). <b>' . __( 'Error Message:', $this->ns_fba->text_domain ) . '</b><br /><span style="color:red;">' . $ex->getMessage() . '</span>';
					$order->add_order_note( $order_note );
				}// End try().
			}// End foreach().
		}

		function set_order_status_safe( $order_id, $status ) {
			$order = wc_get_order( $order_id );
			$status = apply_filters( 'ns_fba_order_status', $status, $order_id );
			$order->update_status( $status, 'WooCommerce Amazon Fulfillment: ', true );
		}

	} //class
}// End if().
