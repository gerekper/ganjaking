<?php
/**
 * Store Credit: PayPal payments manager
 *
 * @package WC_Store_Credit
 * @since   2.4.4
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Store_Credit_Paypal.
 */
class WC_Store_Credit_Paypal {

	/**
	 * Constructor.
	 *
	 * @since 2.4.4
	 */
	public function __construct() {
		add_filter( 'woocommerce_paypal_force_one_line_item', array( $this, 'force_one_line_item' ), 10, 2 );
		add_filter( 'woocommerce_paypal_args', array( $this, 'paypal_args' ), 10, 2 );
	}

	/**
	 * Gets if the order items need to be expressed in a single line item in the PayPal arguments.
	 *
	 * Necessary when the store credit coupons are applied after taxes and the discount is higher than the order subtotal.
	 *
	 * @since 2.4.4
	 *
	 * @param WC_Order $order Order object.
	 * @return bool
	 */
	public function needs_one_line_item( $order ) {
		return ( ! wc_store_credit_apply_before_tax( $order ) && 0 < wc_get_store_credit_used_for_order( $order ) );
	}

	/**
	 * Maybe force one line item in the PayPal arguments.
	 *
	 * @since 2.4.4
	 *
	 * @param bool     $force_one_line_item Force one line item or not.
	 * @param WC_Order $order               Order object.
	 * @return bool
	 */
	public function force_one_line_item( $force_one_line_item, $order ) {
		return ( $this->needs_one_line_item( $order ) ? true : $force_one_line_item );
	}

	/**
	 * Filters the arguments used in a PayPal payment request.
	 *
	 * @since 2.4.4
	 *
	 * @param array    $args  The PayPal arguments.
	 * @param WC_Order $order Order object.
	 * @return mixed
	 */
	public function paypal_args( $args, $order ) {
		if ( ! $this->needs_one_line_item( $order ) ) {
			return $args;
		}

		$credit         = wc_get_store_credit_used_for_order( $order );
		$total_discount = $order->get_total_discount();
		$precision      = ( wc_store_credit_currency_has_decimals( $order->get_currency() ) ? 2 : 0 );

		/*
		 * Restore the order total.
		 * The store credit discount was already restored in the method 'WC_Store_Credit_Order->get_total'.
		 */
		$args['amount_1'] = number_format( round( $args['amount_1'] + ( $total_discount - $credit ), $precision ), $precision, '.', '' );

		// Set the shipping as line item to be able to apply it discounts.
		if ( isset( $args['shipping_1'] ) ) {
			/* translators: %s: Order shipping method */
			$args['item_name_2'] = sprintf( __( 'Shipping via %s', 'woocommerce-store-credit' ), $order->get_shipping_method() );
			$args['quantity_2']  = 1;
			$args['amount_2']    = $args['shipping_1'];

			unset( $args['shipping_1'] );
		}

		// Not included on 'single line item' cases.
		$args['discount_amount_cart'] = number_format( round( $total_discount, $precision ), $precision, '.', '' );

		return $args;
	}
}

return new WC_Store_Credit_Paypal();
