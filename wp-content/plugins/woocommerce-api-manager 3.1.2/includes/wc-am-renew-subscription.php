<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * WooCommerce API Manager Renewal Class
 *
 * @since       3.0
 *
 * @author      Todd Lahman LLC
 * @copyright   Copyright (c) Todd Lahman LLC
 * @package     WooCommerce API Manager/Renewal
 */
class WC_AM_Renew_Subscription {

	private $discount_percentage = 0;

	/**
	 * @var null
	 */
	private static $_instance = null;

	/**
	 * @static
	 * @return \WC_AM_Renew_Subscription
	 */
	public static function instance() {

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	private function __construct() {
		$this->discount_percentage = get_option( 'woocommerce_api_manager_manual_renewal_discount' );
		add_action( 'wp', array( $this, 'renew_api_resource' ) );
		add_filter( 'woocommerce_add_cart_item', array( $this, 'add_cart_item' ) );
		add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'get_cart_item_from_session' ), 10, 2 );
		add_action( 'woocommerce_checkout_create_order_line_item', array( $this, 'create_order_line_item' ), 10, 4 );
		add_filter( 'woocommerce_hidden_order_itemmeta', array( $this, 'hidden_order_item_meta' ), 50 );
		add_filter( 'woocommerce_order_item_display_meta_key', array( $this, 'order_item_display_meta_key' ), 10, 3 );
		add_filter( 'woocommerce_order_item_display_meta_value', array( $this, 'order_item_display_meta_value' ), 10, 3 );
	}

	/**
	 * Processes the API Key renewal request.
	 *
	 * @since 3.0
	 *
	 * @throws \Exception
	 */
	public function renew_api_resource() {
		$request = wc_clean( $_REQUEST );

		if ( ! empty( $request[ '_wpnonce' ] ) && ! wp_verify_nonce( wc_clean( $_REQUEST[ '_wpnonce' ] ), 'renew_api_resource' ) && ! empty( $request[ 'renew_api_resource' ] ) ) { // WPCS: input var ok, CSRF ok.
			wc_add_notice( __( 'Nonce security check failed.', 'woocommerce-api-manager' ), 'error' );

			return;
		}

		if ( ! empty( $request[ 'wc_am_is_renewed_api_resource' ] ) && $request[ 'wc_am_is_renewed_api_resource' ] == 'yes' && ! empty( $request[ 'api_resource_id' ] ) && ! empty( $request[ 'product_id' ] ) && is_user_logged_in() ) {
			$resource = WC_AM_API_RESOURCE_DATA_STORE()->get_resources_by_api_resource_id( $request[ 'api_resource_id' ] );

			if ( ! WC_AM_FORMAT()->empty( $resource ) && ! empty( WC_AM_API_RESOURCE_DATA_STORE()->get_active_api_resources( $resource->product_order_api_key, $resource->product_id ) ) ) {
				if ( $request[ 'product_id' ] != $resource->product_id ) {
					wc_add_notice( __( 'Invalid Product ID.', 'woocommerce-api-manager' ), 'error' );

					return;
				}

				$product = WC_AM_PRODUCT_DATA_STORE()->get_product_object( $resource->product_id );

				if ( is_object( $product ) && ! $product->is_purchasable() ) {
					wc_add_notice( __( 'This product can no longer be purchased.', 'woocommerce-api-manager' ), 'error' );

					return;
				}

				WC()->cart->empty_cart();
				WC()->cart->add_to_cart( $resource->product_id, ! empty( $request[ 'item_quantity' ] ) ? $request[ 'item_quantity' ] : 1, 0, array(), array(
					'wc_am_is_renewed_api_resource' => $request[ 'wc_am_is_renewed_api_resource' ],
					'wc_am_api_resource_object'     => $resource
				) );

				wp_safe_redirect( wc_get_page_permalink( 'checkout' ) );

				exit();
			}
		}
	}

	/**
	 * Add the product to the cart, and change the price to discount the upgrade.
	 *
	 * @since 3.0
	 *
	 * @param array $cart_item
	 *
	 * @return mixed
	 */
	public function add_cart_item( $cart_item ) {
		if ( ! empty( $cart_item[ 'wc_am_is_renewed_api_resource' ] ) ) {
			$price = $cart_item[ 'data' ]->get_price();
			// No division by zero.
			$discount         = ! empty( $this->discount_percentage ) ? ( $price / 100 ) * $this->discount_percentage : 0;
			$discounted_price = $price - $discount;

			$cart_item[ 'data' ]->set_price( $discounted_price );
			$cart_item[ 'data' ]->set_name( $cart_item[ 'data' ]->get_name() . ' (' . __( 'Renewal', 'woocommerce-api-manager' ) . ')' );
		}

		return $cart_item;
	}

	/**
	 * Get the item from the sessions.
	 *
	 * @since 3.0
	 *
	 * @param array $cart_item
	 * @param array $values
	 *
	 * @return mixed
	 */
	public function get_cart_item_from_session( $cart_item, $values ) {
		if ( ! empty( $values[ 'wc_am_is_renewed_api_resource' ] ) ) {
			$price = $cart_item[ 'data' ]->get_price();
			// No division by zero.
			$discount         = ! empty( $this->discount_percentage ) ? ( $price / 100 ) * $this->discount_percentage : 0;
			$discounted_price = $price - $discount;

			$cart_item[ 'data' ]->set_price( $discounted_price );
			$cart_item[ 'data' ]->set_name( $cart_item[ 'data' ]->get_name() . ' (' . __( 'Renewal', 'woocommerce-api-manager' ) . ')' );

			$cart_item[ 'wc_am_is_renewed_api_resource' ] = $values[ 'wc_am_is_renewed_api_resource' ];
		}

		return $cart_item;
	}

	/**
	 * Store the data in the order item meta.
	 *
	 * @since 3.0
	 *
	 * @param object   $item          @see WC_Order_Item_Product object.
	 * @param string   $cart_item_key // @deprecated 4.4.0 For legacy actions.
	 * @param array    $values        // @deprecated 4.4.0 For legacy actions.
	 * @param WC_Order $order
	 *
	 * @return void
	 * @throws \Exception
	 */
	public function create_order_line_item( $item, $cart_item_key, $values, $order ) {
		if ( ! empty( $values[ 'wc_am_is_renewed_api_resource' ] ) ) {
			$previous_api_resource_object = $values[ 'wc_am_api_resource_object' ];
			$product_id                   = $item->get_variation_id() ? $item->get_variation_id() : $item->get_product_id();
			$product                      = WC_AM_PRODUCT_DATA_STORE()->get_product_object( $product_id );
			$price                        = $product->get_price();
			$order_id                     = $order->save(); // save() returns the item_id, but get_id() returns 0.
			$item_id                      = $item->save(); // save() returns the item_id, but get_id() returns 0.

			/**
			 * Add $access_time_remaining to meta to be retrieved when order completed.
			 * Time to add from the old acess_expires value to extend the new order acess_expires value.
			 */
			//
			$current_time               = WC_AM_ORDER_DATA_STORE()->get_current_time_stamp();
			$old_access_expires         = $previous_api_resource_object->access_expires;
			$access_time_remaining      = ( $old_access_expires > $current_time ) ? ( $old_access_expires - $current_time ) : 0;
			$access_expires_time_to_add = $item->get_meta_data( '_wc_am_access_expires_time_to_add' );

			// Update new item meta.
			$item->add_meta_data( '_wc_am_is_renewed_api_resource', $values[ 'wc_am_is_renewed_api_resource' ] );
			$item->add_meta_data( '_wc_am_discount_applied', ! empty( $this->discount_percentage ) ? ( $price / 100 ) * $this->discount_percentage : 0 );
			$item->add_meta_data( '_wc_am_previous_api_resource_object', $previous_api_resource_object );
			$item->add_meta_data( '_wc_am_previous_product_id', $previous_api_resource_object->product_id );
			$item->add_meta_data( '_wc_am_previous_order_id', $previous_api_resource_object->order_id );
			$item->add_meta_data( '_wc_am_previous_order_item_id', $previous_api_resource_object->order_item_id );
			$item->update_meta_data( '_wc_am_access_expires_time_to_add', $access_time_remaining, ! empty( $access_expires_time_to_add ) ? $access_expires_time_to_add : '' );

			$next_product_id    = wc_get_order_item_meta( $previous_api_resource_object->order_item_id, '_wc_am_next_product_id' );
			$next_order_id      = wc_get_order_item_meta( $previous_api_resource_object->order_item_id, '_wc_am_next_order_id' );
			$next_order_item_id = wc_get_order_item_meta( $previous_api_resource_object->order_item_id, '_wc_am_next_order_item_id' );

			// Update previous item meta.
			wc_update_order_item_meta( $previous_api_resource_object->order_item_id, '_wc_am_next_product_id', $product_id, ! empty( $next_product_id ) ? $next_product_id : '' );
			wc_update_order_item_meta( $previous_api_resource_object->order_item_id, '_wc_am_next_order_id', $order_id, ! empty( $next_order_id ) ? $next_order_id : '' );
			wc_update_order_item_meta( $previous_api_resource_object->order_item_id, '_wc_am_next_order_item_id', $item_id, ! empty( $next_order_item_id ) ? $next_order_item_id : '' );
		}
	}

	/**
	 * Add keys to hide in the item meta.
	 *
	 * @since 3.0
	 *
	 * @param array $item_keys
	 *
	 * @return array
	 */
	public function hidden_order_item_meta( $item_keys ) {
		return array_merge( $item_keys, array(
			'_wc_am_previous_api_resource_id',
			'_wc_am_previous_product_id',
			'_wc_am_previous_order_id',
			'_wc_am_previous_order_item_id',
			'_wc_am_next_order_item_id',
			'_wc_am_access_expires_time_to_add',
			'_wc_am_api_resource_renewal_updated'
		) );
	}

	/**
	 * Replaces the display key with human-readable text.
	 *
	 * @since 3.0
	 *
	 * @param string        $display_key
	 * @param object        $meta
	 * @param WC_Order_Item $item
	 *
	 * @return string
	 */
	public function order_item_display_meta_key( $display_key, $meta, $item ) {
		if ( $meta->key === '_wc_am_is_expired_api_resource' ) {
			$display_key = __( 'Expired API Resource', 'woocommerce-api-manager' );
		}

		if ( $meta->key === '_wc_am_next_product_id' ) {
			$display_key = __( 'New Product ID', 'woocommerce-api-manager' );
		}

		if ( $meta->key === '_wc_am_next_order_id' ) {
			$display_key = __( 'New Order ID', 'woocommerce-api-manager' );
		}

		if ( $meta->key === '_wc_am_is_renewed_api_resource' ) {
			$display_key = __( 'API Resource Renewal', 'woocommerce-api-manager' );
		}

		if ( $meta->key === '_wc_am_discount_applied' ) {
			$display_key = __( 'Discount Applied', 'woocommerce-api-manager' );
		}

		return $display_key;
	}

	/**
	 * Replaces the display value with a custom value to add context.
	 *
	 * @since 3.0
	 *
	 * @param string        $display_value
	 * @param object        $meta
	 * @param WC_Order_Item $item
	 *
	 * @return string
	 */
	public function order_item_display_meta_value( $display_value, $meta, $item ) {
		if ( $meta->key === '_wc_am_next_product_id' ) {
			$display_value = $meta->value . '<br>' . esc_url( admin_url() . 'post.php?post=' . esc_attr( WC_AM_PRODUCT_DATA_STORE()->get_parent_product_id( $meta->value ) ) . '&action=edit' );
		}

		if ( $meta->key === '_wc_am_next_order_id' ) {
			$display_value = $meta->value . '<br>' . esc_url( self_admin_url() . 'admin.php?page=wc-orders&action=edit&id=' . $meta->value );
		}

		if ( $meta->key === '_wc_am_is_expired_api_resource' ) {
			$display_value = ucfirst( $meta->value );
		}

		if ( $meta->key === '_wc_am_is_renewed_api_resource' ) {
			$display_value = ucfirst( $meta->value );
		}

		if ( $meta->key === '_wc_am_discount_applied' ) {
			$display_value = get_woocommerce_currency_symbol() . $meta->value;
		}

		return $display_value;
	}

	/**
	 * Returns true if the time period is within the manual renewal time period.
	 *
	 * @since 3.0
	 *
	 * @param int $access_expires
	 * @param int $api_resource_id
	 *
	 * @return bool
	 */
	public function is_manual_renweal_period( $access_expires, $api_resource_id ) {
		$current_time    = WC_AM_ORDER_DATA_STORE()->get_current_time_stamp();
		$expiration_time = $access_expires + WC_AM_GRACE_PERIOD()->get_expiration( $api_resource_id );
		$time_diff       = $expiration_time - $current_time;

		// Indefinite/Lifetime subscriptions have a zero expiration/access_expires value, so $time_diff must be > 0.
		return $time_diff >= 1 && $time_diff <= $this->calculate_manual_renewal_period();
	}

	/**
	 * Calculates the Period in seconds when the manual Renewal period begins.
	 * This determines when the Renewal button in My Account > API Keys is displayed,
	 * and when the renwal email is first sent.
	 *
	 * @since 3.0
	 *
	 * @return int
	 */
	public function calculate_manual_renewal_period() {
		$array    = get_option( 'woocommerce_api_manager_manual_renewal_period' );
		$interval = 0;

		if ( ! empty( $array ) && is_array( $array ) ) {
			$number = $array[ 'number' ];
			$unit   = $array[ 'unit' ];

			if ( ! empty( $number ) ) {
				if ( $unit == 'days' ) {
					$interval = $number * DAY_IN_SECONDS;
				} elseif ( $unit == 'weeks' ) {
					$interval = $number * WEEK_IN_SECONDS;
				} elseif ( $unit == 'months' ) {
					$interval = $number * MONTH_IN_SECONDS;
				} elseif ( $unit == 'years' ) {
					$interval = $number * YEAR_IN_SECONDS;
				}
			}
		}

		return absint( $interval );
	}
}