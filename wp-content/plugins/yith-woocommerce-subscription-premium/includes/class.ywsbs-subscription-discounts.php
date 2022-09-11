<?php //phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * YWSBS_Subscription_Discounts Class.
 *
 * @class   YWSBS_Subscription_Discounts
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'YWSBS_Subscription_Discounts' ) ) {

	/**
	 * Class YWSBS_Subscription_Coupons
	 */
	class YWSBS_Subscription_Discounts extends WC_Discounts {

		/**
		 * WC_Discounts Constructor.
		 *
		 * @param YWSBS_Subscription $subscription Subscription object.
		 */
		public function __construct( $subscription ) {
			$this->set_items_from_subscription( $subscription );
		}

		/**
		 * Set the items to discount from the subscription
		 *
		 * @param YWSBS_Subscription $subscription Subscription.
		 */
		public function set_items_from_subscription( $subscription ) {
			$this->items     = array();
			$this->discounts = array();

			if ( ! is_a( $subscription, 'YWSBS_Subscription' ) ) {
				return;
			}

			$order          = $subscription->get_order();
			$item           = new stdClass();
			$item->object   = $subscription;
			$item->key      = $subscription->get_id();
			$item->product  = $subscription->get_product();
			$item->quantity = $subscription->get_quantity();
			if ( 'yes' === get_option( 'woocommerce_calc_discounts_sequentially' ) ) {
				$item->price = wc_add_number_precision_deep( $subscription->get_line_total() );
				if ( $order->get_prices_include_tax() ) {
					$item->price += wc_add_number_precision_deep( $subscription->get_line_tax() );
				}
			} else {
				$item->price = wc_add_number_precision_deep( $subscription->get_line_subtotal() );
				if ( $order->get_prices_include_tax() ) {
					$item->price += wc_add_number_precision_deep( $subscription->get_line_subtotal_tax() );
				}
			}

			$this->items[ $subscription->get_id() ] = $item;

			uasort( $this->items, array( $this, 'sort_by_price' ) );
		}


		/**
		 * Apply a discount to all items using a coupon.
		 *
		 * @param WC_Coupon $coupon Coupon object being applied to the items.
		 * @param bool      $validate Set to false to skip coupon validation.
		 * @return bool|WP_Error True if applied or WP_Error instance in failure.
		 * @throws Exception Error message when coupon isn't valid.
		 * @since  3.2.0
		 */
		public function apply_coupon( $coupon, $validate = true ) {
			if ( ! is_a( $coupon, 'WC_Coupon' ) ) {
				return new WP_Error( 'invalid_coupon', __( 'Invalid coupon', 'woocommerce' ) );
			}

			$is_coupon_valid = $validate ? $this->is_coupon_valid( $coupon ) : true;

			if ( is_wp_error( $is_coupon_valid ) ) {
				return $is_coupon_valid;
			}

			if ( ! isset( $this->discounts[ $coupon->get_code() ] ) ) {
				$this->discounts[ $coupon->get_code() ] = array_fill_keys( array_keys( $this->items ), 0 );
			}

			$items_to_apply = $this->get_items_to_apply_coupon( $coupon );

			// Core discounts are handled here as of 3.2.
			switch ( $coupon->get_discount_type() ) {
				case 'recurring_percent':
					$total_discount = $this->apply_coupon_percent( $coupon, $items_to_apply );
					break;
				case 'recurring_fixed':
					$total_discount = $this->apply_coupon_fixed_product( $coupon, $items_to_apply );
					break;
			}

			return true;
		}
	}
}
