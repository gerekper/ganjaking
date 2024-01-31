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
	 * The cart coupons instance.
	 *
	 * @var WC_Store_Credit_Cart_Coupons
	 */
	protected $cart_coupons;

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 */
	public function __construct() {
		$this->cart_coupons = new WC_Store_Credit_Cart_Coupons();

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
		add_action( 'woocommerce_store_api_checkout_order_processed', array( $this, 'create_order' ) );
		add_action( 'woocommerce_cart_emptied', array( $this, 'cart_emptied' ) );
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
			$coupon_discount = $this->get_cart_discounts()->coupon_discounts()->get( $coupon->get_code() );

			if ( $coupon_discount ) {
				$type      = ( WC()->cart->display_prices_including_tax() ? 'base_tax' : 'base' );
				$discounts = $coupon_discount->get_discounts_by_type( $type );
				$html      = '-' . wc_price( array_sum( $discounts ) );
			}
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

		// It's a deposit product.
		if ( isset( $cart_item['deposit_amount'] ) ) {
			return $cart_item['deposit_amount'];
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
			( ! empty( $_POST ) && ! empty( $_POST['order_id'] ) ) || // Discard order actions.
			( ! WC()->cart || ! in_array( $coupon->get_code(), WC()->cart->get_applied_coupons(), true ) ) // Discard coupons not applied to the cart.
		) {
			return $discounts;
		}
		// phpcs:enable WordPress.Security.NonceVerification

		$cart_discounts = $this->get_cart_discounts();
		$discounts      = $cart_discounts->calculate_item_discounts( $coupon, wc_remove_number_precision_deep( $discounts ) );

		$this->fix_discounts_for_deposits( $coupon );

		return wc_add_number_precision_deep( $discounts );
	}

	/**
	 * Updates the cart totals.
	 *
	 * @since 3.0.0
	 *
	 * @param WC_Cart $cart Cart object.
	 */
	public function after_calculate_totals( $cart ) {
		/**
		 * Filters whether to calculate the shipping discounts for the specified cart.
		 *
		 * @since 4.1.0
		 *
		 * @param bool    $calculate_discounts Whether to calculate the shipping discounts.
		 * @param WC_Cart $cart                Cart object.
		 */
		if ( ! apply_filters( 'wc_store_credit_calculate_shipping_discounts_for_cart', true, $cart ) ) {
			return;
		}

		$cart_discounts = $this->get_cart_discounts();
		$cart_discounts->calculate_shipping_discounts();
		$cart_discounts->calculate_totals();

		/*
		 * The cart session is updated with priority 100, and priority 10 before WC 6.3.
		 * In both cases, it happens before applying these changes, so we update the cart session manually.
		 */
		WC()->session->set( 'cart_totals', WC()->cart->get_totals() );
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

	/**
	 * Cart emptied.
	 *
	 * @since 3.5.1
	 */
	public function cart_emptied() {
		// Clear the cart discounts.
		$this->cart_discounts = null;
	}

	/**
	 * Fixes the store credit discounts applied to the deposit products.
	 *
	 * @since 3.8.0
	 *
	 * @param WC_Coupon $coupon Coupon object.
	 */
	protected function fix_discounts_for_deposits( $coupon ) {
		$deposit_discounts     = WC()->session->get( 'deposits_present_discounts', array() );
		$deposit_tax_discounts = WC()->session->get( 'deposits_discount_tax', array() );

		// There are deposit products.
		if ( empty( $deposit_discounts ) ) {
			return;
		}

		$coupon_discounts = $this->get_cart_discounts()->coupon_discounts()->get( $coupon->get_code() );

		if ( ! $coupon_discounts ) {
			return;
		}

		$coupon_id      = $coupon->get_id();
		$item_discounts = $coupon_discounts->item_discounts()->get_by_group( 'cart' );

		foreach ( $item_discounts as $item_discount ) {
			$cart_item_id = WC_Deposits_Cart_Manager::generate_cart_id( $item_discount->get_item()->object );

			$deposit_discounts[ $cart_item_id ][ $coupon_id ] = $item_discount->get_discount();

			if ( $item_discount->get_item()->product->is_taxable() ) {
				$deposit_tax_discounts[ $cart_item_id ][ $coupon_id ] = $item_discount->get_discount_tax();
			}
		}

		WC()->session->set( 'deposits_present_discounts', $deposit_discounts );
		WC()->session->set( 'deposits_discount_tax', $deposit_tax_discounts );
	}
}

return new WC_Store_Credit_Cart();
