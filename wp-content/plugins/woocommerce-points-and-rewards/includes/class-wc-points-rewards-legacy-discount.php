<?php
/**
 * WooCommerce Points and Rewards
 *
 * @package     WC-Points-Rewards/Classes
 * @author      WooThemes
 * @copyright   Copyright (c) 2013, WooThemes
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Discount class
 *
 * Handles generating the coupon code and data that allows the user to redeem their points for a discount
 *
 * @since 1.0
 */
class WC_Points_Rewards_Discount {

	/**
	 * Add coupon-related filters to help generate the custom coupon
	 *
	 * @since 1.0
	 */
	public function __construct() {
		// set our custom coupon data
		add_filter( 'woocommerce_get_shop_coupon_data', array( $this, 'get_discount_data' ), 10, 2 );

		// filter the "coupon applied successfully" message
		add_filter( 'woocommerce_coupon_message', array( $this, 'get_discount_applied_message' ), 10, 3 );

		// Set the discount amount when needed
		add_filter( 'woocommerce_coupon_get_discount_amount', array( $this, 'get_discount_amount' ), 10, 5 );
	}

	/**
	 * Generate the coupon data required for the discount
	 *
	 * @since 1.0
	 * @param array $data the coupon data
	 * @param string $code the coupon code
	 * @return array the custom coupon data
	 */
	public function get_discount_data( $data, $code ) {
		if ( strtolower( $code ) != $this->get_discount_code() ) {
			return $data;
		}

		// note: we make our points discount "greedy" so as many points as possible are
		//   applied to the order.  However we also want to play nice with other discounts
		//   so if another coupon is applied we want to use less points than otherwise.
		//   The solution is to make this discount apply post-tax so that both pre-tax
		//   and post-tax discounts can be considered.  At the same time we use the cart
		//   subtotal excluding tax to calculate the maximum points discount, so it
		//   functions like a pre-tax discount in that sense.
		$data = array(
			'id'                         => true,
			'type'                       => 'fixed_cart',
			'amount'                     => 0,
			'coupon_amount'              => 0, // 2.2
			'individual_use'             => 'no',
			'product_ids'                => '',
			'exclude_product_ids'        => '',
			'usage_limit'                => '',
			'usage_count'                => '',
			'expiry_date'                => '',
			'apply_before_tax'           => 'yes',
			'free_shipping'              => 'no',
			'product_categories'         => array(),
			'exclude_product_categories' => array(),
			'exclude_sale_items'         => 'no',
			'minimum_amount'             => '',
			'maximum_amount'             => '',
			'customer_email'             => '',
		);

		return $data;
	}

	/**
	 * Get coupon discount amount
	 * @param  float $discount
	 * @param  float $discounting_amount
	 * @param  object $cart_item
	 * @param  bool $single
	 * @param  WC_Coupon $coupon
	 * @return float
	 */
	public function get_discount_amount( $discount, $discounting_amount, $cart_item, $single, $coupon ) {
		if ( strtolower( $coupon->code ) != $this->get_discount_code() ) {
			return $discount;
		}

		/**
		 * This is the most complex discount - we need to divide the discount between rows based on their price in
		 * proportion to the subtotal. This is so rows with different tax rates get a fair discount, and so rows
		 * with no price (free) don't get discounted.
		 *
		 * Get item discount by dividing item cost by subtotal to get a %
		 */
		$discount_percent = 0;

		if ( WC()->cart->subtotal_ex_tax ) {
			$discount_percent = ( $cart_item['data']->get_price_excluding_tax() * $cart_item['quantity'] ) / WC()->cart->subtotal_ex_tax;
		}

		$total_discount = WC_Points_Rewards_Cart_Checkout::get_discount_for_redeeming_points( true );
		$total_discount = min( ( $total_discount * $discount_percent ) / $cart_item['quantity'], $discounting_amount );

		return $total_discount;
	}

	/**
	 * Change the "Coupon applied successfully" message to "Discount Applied Successfully"
	 *
	 * @since 1.0
	 * @param string $message the message text
	 * @param string $message_code the message code
	 * @param object $coupon the WC_Coupon instance
	 * @return string the modified messages
	 */
	public function get_discount_applied_message( $message, $message_code, $coupon ) {
		if ( WC_Coupon::WC_COUPON_SUCCESS === $message_code && $coupon->code === $this->get_discount_code() ) {
			return __( 'Discount Applied Successfully', 'woocommerce-points-and-rewards' );
		} else {
			return $message;
		}
	}

	/**
	 * Generates a unique discount code tied to the current user ID and timestamp
	 *
	 * @since 1.0
	 */
	public static function generate_discount_code() {
		// set the discount code to the current user ID + the current time in YYYY_MM_DD_H_M format
		$discount_code = sprintf( 'wc_points_redemption_%s_%s', get_current_user_id(), date( 'Y_m_d_h_i', current_time( 'timestamp' ) ) );

		WC()->session->set( 'wc_points_rewards_discount_code', $discount_code );

		return $discount_code;
	}

	/**
	 * Returns the unique discount code generated for the applied discount if set
	 *
	 * @since 1.0
	 */
	public static function get_discount_code() {
		if ( WC()->session !== null ) {
			return WC()->session->get( 'wc_points_rewards_discount_code' );
		}
	}
}
