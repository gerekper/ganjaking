<?php
/**
 * Class to handle the store credit coupons in the cart.
 *
 * @package WC_Store_Credit/Classes
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Store_Credit_Cart class.
 */
class WC_Store_Credit_Cart {

	/**
	 * The cart discounts instance.
	 *
	 * @var WC_Store_Credit_Discounts_Cart
	 */
	protected $cart_discounts;

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 */
	public function __construct() {
		add_filter( 'woocommerce_cart_totals_coupon_label', array( $this, 'cart_totals_coupon_label' ), 10, 2 );
		add_filter( 'woocommerce_coupon_discount_amount_html', array( $this, 'coupon_discount_amount_html' ), 10, 2 );
		add_filter( 'woocommerce_coupon_sort', array( $this, 'set_coupon_priority' ), 10, 2 );
		add_filter( 'woocommerce_coupon_get_discount_amount', array( $this, 'coupon_get_discount_amount' ), 10, 5 );
		add_filter( 'woocommerce_coupon_custom_discounts_array', array( $this, 'coupon_get_discounts_array' ), 10, 2 );

		/*
		 * AvaTax priority: 999.
		 * Update totals after AvaTax load the taxes.
		 */
		add_action( 'woocommerce_after_calculate_totals', array( $this, 'after_calculate_totals' ), 1000 );
		add_action( 'woocommerce_checkout_create_order', array( $this, 'create_order' ) );
	}

	/**
	 * Gets the cart discounts instance.
	 *
	 * @since 3.0.0
	 *
	 * @return WC_Store_Credit_Discounts_Cart
	 */
	public function get_cart_discounts() {
		if ( ! $this->cart_discounts ) {
			$this->cart_discounts = new WC_Store_Credit_Discounts_Cart( WC()->cart );
		}

		return $this->cart_discounts;
	}

	/**
	 * Change label in cart
	 *
	 * @since 3.0.0
	 *
	 * @param  string    $label  The coupon label.
	 * @param  WC_Coupon $coupon The coupon instance.
	 * @return string
	 */
	public function cart_totals_coupon_label( $label, $coupon ) {
		if ( wc_is_store_credit_coupon( $coupon ) ) {
			/* translators: %s: coupon code */
			$label = sprintf( esc_html__( 'Store credit: %s', 'woocommerce-store-credit' ), $coupon->get_code() );
		}

		return $label;
	}

	/**
	 * Filters the HTML content for the coupon discount amount.
	 *
	 * @since 3.0.0
	 *
	 * @param string    $html   HTML content.
	 * @param WC_Coupon $coupon Coupon object.
	 * @return mixed
	 */
	public function coupon_discount_amount_html( $html, $coupon ) {
		if ( wc_is_store_credit_coupon( $coupon ) ) {
			$cart_discounts = $this->get_cart_discounts();

			$type      = ( WC()->cart->display_prices_including_tax() ? 'base_tax' : 'base' );
			$discounts = $cart_discounts->coupon_discounts()->get( $coupon->get_code() )->get_discounts_by_type( $type );
			$html      = '-' . wc_price( array_sum( $discounts ) );
		}

		return $html;
	}

	/**
	 * Sets the coupon priority.
	 *
	 * @since 3.0.0
	 *
	 * @param int       $priority The coupon priority.
	 * @param WC_Coupon $coupon   The coupon instance.
	 * @return int
	 */
	public function set_coupon_priority( $priority, $coupon ) {
		// Process the store credit coupons after all the other kinds of coupons.
		if ( wc_is_store_credit_coupon( $coupon ) ) {
			// Coupons without shipping discounts go first.
			$priority = ( wc_store_credit_coupon_apply_to_shipping( $coupon ) ? 5 : 4 );
		}

		return $priority;
	}

	/**
	 * Gets the coupon discount.
	 *
	 * @since 3.0.0
	 *
	 * @param float      $discount           The coupon discount.
	 * @param float      $discounting_amount Amount the coupon is being applied to.
	 * @param array|null $cart_item          Cart item being discounted if applicable.
	 * @param boolean    $single             True if discounting a single qty item, false if its the line.
	 * @param WC_Coupon  $coupon             The coupon instance.
	 * @return float Amount this coupon has discounted.
	 */
	public function coupon_get_discount_amount( $discount, $discounting_amount, $cart_item, $single, $coupon ) {
		if ( $cart_item instanceof WC_Order_Item_Product || ! wc_is_store_credit_coupon( $coupon ) ) {
			return $discount;
		}

		/*
		 * Return the maximum amount to obtain the total discount for this coupon and fix the discounts per item
		 * in the 'woocommerce_coupon_custom_discounts_array' filter hook.
		 */
		return $discounting_amount;
	}

	/**
	 * Post-process the coupon discounts.
	 *
	 * @since 3.0.0
	 *
	 * @param array     $discounts An array with the applied discounts per item.
	 * @param WC_Coupon $coupon    The coupon instance.
	 * @return array
	 */
	public function coupon_get_discounts_array( $discounts, $coupon ) {
		// phpcs:disable WordPress.Security.NonceVerification
		if (
			! wc_is_store_credit_coupon( $coupon ) ||
			! in_array( $coupon->get_code(), WC()->cart->get_applied_coupons(), true ) || // Discard coupons not applied to the cart.
			( ! empty( $_POST ) && ! empty( $_POST['order_id'] ) ) // Discard order actions.
		) {
			return $discounts;
		}
		// phpcs:enable WordPress.Security.NonceVerification

		$cart_discounts = $this->get_cart_discounts();
		$discounts      = $cart_discounts->calculate_item_discounts( $coupon, wc_remove_number_precision_deep( $discounts ) );

		return wc_add_number_precision_deep( $discounts );
	}

	/**
	 * Updates the cart totals.
	 *
	 * @since 3.0.0
	 */
	public function after_calculate_totals() {
		$cart_discounts = $this->get_cart_discounts();
		$cart_discounts->calculate_shipping_discounts();
		$cart_discounts->calculate_totals();
	}

	/**
	 * Processes the order before saving it.
	 *
	 * @since 3.0.0
	 *
	 * @param WC_Order $order Order object.
	 */
	public function create_order( $order ) {
		$cart_discounts  = $this->get_cart_discounts();
		$total_discounts = $cart_discounts->coupon_discounts()->get_total_discounts();

		if ( empty( $total_discounts ) ) {
			return;
		}

		$cart_discounts->update_shipping_discount_items( $order );
		$cart_discounts->update_credit_discounts( $order );
		$cart_discounts->update_credit_used( $order );
	}
}

return new WC_Store_Credit_Cart();
