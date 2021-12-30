<?php
/**
 * WooCommerce Drip Events / Notifications
 *
 * @package   WooCommerce Drip
 * @author    Bryce <bryce@bryce.se>
 * @license   GPL-2.0+
 * @link      http://bryce.se
 * @copyright 2014 Bryce Adams
 * @since     1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * wcdrip Events Class
 *
 * @package  WooCommerce Drip
 * @author   Bryce <bryce@bryce.se>
 * @since    1.1.4
 */

if ( ! class_exists( 'WC_Drip_Events' ) ) {

	class WC_Drip_Events {

		protected static $instance = null;

		public function __construct() {
			add_action( 'woocommerce_payment_complete', array( $this, 'new_order' ) );
			add_action( 'woocommerce_order_edit_status', array( $this, 'update_order_status' ), null, 2 );
		}

		/**
		 * Start the Class when called
		 *
		 * @package WooCommerce Drip
		 * @author  Bryce <bryce@bryce.se>
		 * @since   1.0.0
		 */
	    public static function get_instance() {
			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self;
			}

			return self::$instance;
		}

		/**
		 * Send the new order data when an order status changes to "completed".
		 *
		 * @package WooCommerce Drip
		 * @since   1.2.4
		 */
		public function update_order_status( $order_id, $status ) {
			if ( 'completed' == $status && 0 < (int)$order_id ) {
				$this->new_order( $order_id );
			}
		}

		/**
		 * New Order Made.
		 *
		 * @package WooCommerce Drip
		 * @author  Bryce <bryce@bryce.se>
		 * @since   1.1.0
		 */
		public function new_order( $order_id ) {
			if ( 1 == get_post_meta( $order_id, '_wcdrip_tracked', true ) ) {
				return false;
			}

			$wrapper = wcdrip_get_settings();

			if ( empty( $wrapper['api_key'] ) || empty( $wrapper['account'] ) ) {
				return;
			}

			$api_key    = $wrapper['api_key'];
			$wcdrip_api = new Drip_Api( $api_key );
			$compat     = new WC_Drip_WC_Plugin_Compatibility();

			if ( $wrapper['event_sale_name'] ) {
				$event_sale_name = $wrapper['event_sale_name'];
			} else {
				$event_sale_name = apply_filters( 'wcdrip_action_order', __( 'Purchase', 'woocommerce-drip' ) );
			}

			// Order Variable
			$order = $compat->wc_get_order( $order_id );

			$billing_email = version_compare( WC_VERSION, '3.0', '<' ) ? $order->billing_email : $order->get_billing_email();

			if ( empty( $billing_email ) ) {
				return;
			}

			// Order Items
			$products = implode(', ', array_map( function ( $product ) {
				return version_compare( WC_VERSION, '3.0', '<' ) ? $product['name'] : $product->get_name();
			}, $order->get_items() ) );

			$product_ids = implode( ', ', array_filter( array_map( function ( $product ) {
				if ( is_a( $product, 'WC_Order_Item_Product' ) ) {
					return $product->get_product_id();
				}
			}, $order->get_items() ) ) );

			// Customer ID
			$customer_id = $order->get_user_id();

			// Fetch Parameters
			$fetch_params = array(
				'account_id'    => $wrapper['account'],
				'subscriber_id' => $billing_email,
			);

			// $is_subscriber Variable
			wcdrip_log( sprintf( '%s: Fetch subscriber from API with params: %s', __METHOD__, print_r( $fetch_params, true ) ) );
			$is_sub_action = $wcdrip_api->fetch_subscriber( $fetch_params );

			wcdrip_log( sprintf( '%s: Got subscriber from API: %s', __METHOD__, print_r( $is_sub_action, true ) ) );

			if ( $is_sub_action ) {
				$is_subscriber = $is_sub_action['id'];
				wcdrip_log( sprintf( '%s: Subscriber ID %s. Attempting to record event %s and update the subscriber data.', __METHOD__, $is_subscriber, $event_sale_name ) );
			} else {
				$is_subscriber = false;
				wcdrip_log( sprintf( '%s: Subscriber does not exists. Skip recording event.', __METHOD__ ) );
			}

			// Event Tag Parameters
			$event_params = array(
				'account_id' => $wrapper['account'],
				'email'      => $billing_email,
				'action'     => $event_sale_name,
				'properties' => $this->event_properties( $order->get_total(), $products, $order_id, $product_ids ),
			);

			// Tags
			$tags = apply_filters( 'wcdrip_tag_customer', array(
				__( 'Customer', 'woocommerce-drip' ),
			) );

			// Subscriber parameters.
			$subscriber_params = array(
				'account_id'    => $wrapper['account'],
				'email'         => $billing_email,
				'first_name'    => $order->get_billing_first_name(),
				'last_name'     => $order->get_billing_last_name(),
				'address1'      => $order->get_billing_address_1(),
				'address2'      => $order->get_billing_address_2(),
				'city'          => $order->get_billing_city(),
				'state'         => $order->get_billing_state(),
				'zip'           => $order->get_billing_postcode(),
				'phone'         => $order->get_billing_phone(),
				'country'       => $order->get_billing_country(),
				'custom_fields' => $this->custom_fields( $order, $customer_id ),
				'tags'          => $tags,
			);

			wcdrip_log( sprintf( '%s: Record event to API with params: %s', __METHOD__, print_r( $event_params, true ) ) );
			$wcdrip_api->record_event( $event_params );

			// Check if subscriber exists and if so, send data to Drip
			if ( $is_subscriber ) {

				wcdrip_log( sprintf( '%s: Create or update subscriber to API with params: %s', __METHOD__, print_r( $subscriber_params, true ) ) );
				$wcdrip_api->create_or_update_subscriber( $subscriber_params );

			}

			wcdrip_log( sprintf( '%s: Mark order ID %s as tracked', __METHOD__, $order_id ) );
			update_post_meta( $order_id, '_wcdrip_tracked', 1 );
		}


		// Helper method for properties for sending an event
		public function event_properties( $value, $products, $order_id, $product_ids ) {
	    	$content = array(
				'value'    => $value*100,
				'price'    => '$' . $value,
				'products' => $product_ids,
				'product_names' => $products,
				'order_id' => $order_id,
	    	);

			$obj = json_decode( wp_json_encode( $content ), false );

			return $obj;
		}

		/**
		 * Helper method for adding custom fields to the subscriber.
		 * Includes: name, lifetime value, purchased products (, separated) and customer ID (if user).
		 *
		 * @since 1.1.4
		 *
		 * @param $order
		 * @param $customer_id
		 *
		 * @return array
		 * @throws Exception
		 */
		public function custom_fields( $order, $customer_id ) {
			$wrapper    = wcdrip_get_settings();
			$api_key    = $wrapper['api_key'];
			$wcdrip_api = new Drip_Api( $api_key );
			$email      = version_compare( WC_VERSION, '3.0', '<' ) ? $order->billing_email : $order->get_billing_email();
			$value      = $order->get_total();
			$products   = $order->get_items();

	    	// Fetch Parameters
			$fetch_params = array(
				'account_id'    => $wrapper['account'],
				'subscriber_id' => $email,
			);

			wcdrip_log( sprintf( '%s: Fetch subscriber from API with params: %s', __METHOD__, print_r( $fetch_params, true ) ) );

			// Store lifetime_value field in variable
			$is_fetch_action = $wcdrip_api->fetch_subscriber( $fetch_params );

			wcdrip_log( sprintf( '%s: Got subscriber from API: %s', __METHOD__, print_r( $is_fetch_action, true ) ) );

			if ( is_array( $is_fetch_action ) ) {
				$is_fetch_action = array_filter( $is_fetch_action );
			}

			$return_lifetime_value    = false;
			$return_previous_products = false;

			if ( ! empty( $is_fetch_action['custom_fields']['lifetime_value'] ) ) {
				$return_lifetime_value = $is_fetch_action['custom_fields']['lifetime_value'];
			}

			if ( ! empty( $is_fetch_action['custom_fields']['purchased_products'] ) ) {
				$return_previous_products = $is_fetch_action['custom_fields']['purchased_products'];
			}

			// Check for lifetime_value field
			if ( $return_lifetime_value ) {
				$lifetime_value = $return_lifetime_value;
			} else {
				$lifetime_value = 0;
			}

			// Add value to lifetime_value field
			$lifetime_value = $lifetime_value + $value;

			// Product IDs
			$product_ids = implode( ', ', array_reduce( $products, function( $carry, $item ) {
				if ( is_a( $item, 'WC_Order_Item_Product' ) ) {
					$carry[] = $item->get_product_id();
				}

				return $carry;
			}, array() ) );

			// Determine and build list of total products, purchased before and now
			if ( $return_previous_products ) {
				$previous_products = $return_previous_products . ', ';
				$total_products = $previous_products . $product_ids;
			} else {
				$total_products = $product_ids;
			}

			// Build custom fields to attach to customer
			$content = apply_filters( 'wcdrip_custom_fields', array(
				'name'               => version_compare( WC_VERSION, '3.0', '<' ) ? $order->billing_first_name . ' ' . $order->billing_last_name : $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
				'lifetime_value'     => $lifetime_value,
				'purchased_products' => $total_products,
			), $email, $lifetime_value, $products, $order );

			if ( $customer_id ) {
				$content['customer_id'] = $customer_id;
			}

			return $content;
		}

		/**
		 * Settings Wrapper
		 * @return  array
		 * @since   1.0.0
		 *
		 * @deprecated
		 */
		public function wrapper() {
			_deprecated_function( 'WC_Drip_Events::wrapper', 'wcdrip_get_settings', '1.3.0' );
			return wcdrip_get_settings();
	    }

	}

}
