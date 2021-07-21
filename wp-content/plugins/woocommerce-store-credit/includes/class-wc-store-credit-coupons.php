<?php
/**
 * Class to handle the store credit coupons.
 *
 * @package WC_Store_Credit/Classes
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Store_Credit_Coupons class.
 */
class WC_Store_Credit_Coupons {

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 */
	public function __construct() {
		add_filter( 'woocommerce_coupon_discount_types', array( $this, 'add_discount_type' ) );
		add_filter( 'woocommerce_product_coupon_types', array( $this, 'product_coupon_types' ) );
		add_filter( 'woocommerce_coupon_is_valid_for_cart', array( $this, 'is_valid_for_cart' ), 10, 2 );
		add_filter( 'woocommerce_coupon_is_valid', array( $this, 'is_valid' ), 10, 3 );
		add_filter( 'woocommerce_coupon_error', array( $this, 'error_message' ), 10, 3 );

		add_action( 'woocommerce_new_coupon', array( $this, 'flush_coupon_cache' ) );
		add_action( 'woocommerce_update_coupon', array( $this, 'flush_coupon_cache' ) );
		add_action( 'wc_store_credit_before_trash_coupon', array( $this, 'flush_coupon_cache' ) );
		add_action( 'wc_store_credit_before_delete_coupon', array( $this, 'flush_coupon_cache' ) );
	}

	/**
	 * Registers the 'store_credit' discount type.
	 *
	 * @since 3.0.0
	 *
	 * @param array $discount_types The coupon types.
	 * @return array
	 */
	public function add_discount_type( $discount_types ) {
		$discount_types['store_credit'] = _x( 'Store Credit', 'discount type label', 'woocommerce-store-credit' );

		return $discount_types;
	}

	/**
	 * Registers the 'store_credit' coupon as a product discount type.
	 *
	 * @since 3.0.0
	 *
	 * @param array $types The cart coupon types.
	 * @return array
	 */
	public function product_coupon_types( $types ) {
		$types[] = 'store_credit';

		return $types;
	}

	/**
	 * Checks if the coupon is valid for cart.
	 *
	 * @since 3.0.0
	 *
	 * @param bool      $valid  True if the coupon is valid. False otherwise.
	 * @param WC_Coupon $coupon The coupon object.
	 * @return bool
	 */
	public function is_valid_for_cart( $valid, $coupon ) {
		if ( wc_is_store_credit_coupon( $coupon ) ) {
			// Not valid for the cart if the coupon has product restrictions.
			$valid = ! (
				count( $coupon->get_product_ids() ) || count( $coupon->get_product_categories() ) ||
				count( $coupon->get_excluded_product_ids() ) || count( $coupon->get_excluded_product_categories() ) ||
				$coupon->get_exclude_sale_items()
			);
		}

		return $valid;
	}

	/**
	 * Validates a store credit coupon.
	 *
	 * @since 3.0.0
	 *
	 * @param bool         $valid     True if the coupon is valid. False otherwise.
	 * @param WC_Coupon    $coupon    The coupon object.
	 * @param WC_Discounts $discounts WC_Discounts instance.
	 * @return bool
	 */
	public function is_valid( $valid, $coupon, $discounts ) {
		if ( ! $valid || ! wc_is_store_credit_coupon( $coupon ) ) {
			return $valid;
		}

		$is_cart = ( $discounts->get_object() instanceof WC_Cart );

		// The cart contains Store Credit products.
		if ( $is_cart ) {
			foreach ( $discounts->get_items() as $item ) {
				if ( $item->product->is_type( 'store_credit' ) ) {
					return false;
				}
			}
		}

		$credit = $coupon->get_amount();

		if ( $is_cart ) {
			// Include the credit used in the pending payment order.
			$order_id = WC()->session->get( 'order_awaiting_payment' );

			if ( $order_id ) {
				$code        = $coupon->get_code();
				$credit_used = wc_get_store_credit_used_for_order( $order_id, 'per_coupon' );

				if ( ! empty( $credit_used[ $code ] ) ) {
					$credit += $credit_used[ $code ];
				}
			}
		}

		// Credit exhausted.
		if ( $credit <= 0 ) {
			return false;
		}

		return $valid;
	}

	/**
	 * Filters the coupon error message.
	 *
	 * @since 3.0.0
	 *
	 * @param string    $message Error message.
	 * @param int       $code    Error code.
	 * @param WC_Coupon $coupon  Coupon object.
	 * @return mixed
	 */
	public function error_message( $message, $code, $coupon ) {
		if ( 100 !== $code || ! wc_is_store_credit_coupon( $coupon ) ) {
			return $message;
		}

		if ( $coupon->get_amount() <= 0 ) {
			return _x( 'There is no credit remaining on this coupon.', 'error message', 'woocommerce-store-credit' );
		} else {
			return _x( 'This coupon cannot be used on this cart.', 'error message', 'woocommerce-store-credit' );
		}
	}

	/**
	 * Flushes the cache related to the specified coupon.
	 *
	 * @since 3.1.2
	 *
	 * @param mixed $the_coupon Coupon object, ID or code.
	 */
	public function flush_coupon_cache( $the_coupon ) {
		$coupon = wc_store_credit_get_coupon( $the_coupon );

		if ( ! wc_is_store_credit_coupon( $coupon ) ) {
			return;
		}

		$allowed_emails = $coupon->get_email_restrictions();

		foreach ( $allowed_emails as $email ) {
			$key = sanitize_key( $email );

			wp_cache_delete( "wc_store_credit_customer_all_coupons_{$key}", 'store_credit' );
			wp_cache_delete( "wc_store_credit_customer_active_coupons_{$key}", 'store_credit' );
			wp_cache_delete( "wc_store_credit_customer_exhausted_coupons_{$key}", 'store_credit' );
		}
	}
}

return new WC_Store_Credit_Coupons();
