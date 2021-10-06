<?php

/**
 * WC_Booking_Checkout_Manager class.
 */
class WC_Booking_Checkout_Manager {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_filter( 'woocommerce_available_payment_gateways', array( $this, 'remove_payment_methods' ) );
		add_filter( 'woocommerce_cart_needs_payment', array( $this, 'booking_requires_confirmation' ), 10, 2 );
		add_filter( 'woocommerce_order_needs_payment', array( $this, 'booking_order_requires_confirmation' ), 10, 3 );
	}

	/**
	 * Removes all payment methods when cart has a booking that requires confirmation.
	 *
	 * @param  array $available_gateways
	 * @return array
	 */
	public function remove_payment_methods( $available_gateways ) {

		if ( wc_booking_cart_requires_confirmation() ) {
			unset( $available_gateways );

			$available_gateways = array();
			$available_gateways['wc-booking-gateway'] = new WC_Bookings_Gateway();
		}

		return $available_gateways;
	}

	/**
	 * Always require payment if the order have a booking that requires confirmation.
	 *
	 * @param  bool $needs_payment
	 * @param  WC_Cart $cart
	 *
	 * @return bool
	 */
	public function booking_requires_confirmation( $needs_payment, $cart ) {
		if ( ! $needs_payment ) {
			foreach ( $cart->cart_contents as $cart_item ) {
				if ( wc_booking_requires_confirmation( $cart_item['product_id'] ) ) {
					$needs_payment = true;
					break;
				}
			}
		}

		return $needs_payment;
	}

	/**
	 * If a bookable product is in the order and that bookable product requires confirmation, the order
	 * "requires payment" regardless of the order total.
	 * @param  bool $needs_payment the WooCommerce value passed to the 'woocommerce_order_needs_payment' filter
	 * @param  object $order the WooCommerce order
	 * @return bool  the filtered value of $needs_payment
	 */
	public function booking_order_requires_confirmation( $needs_payment, $order, $valid_order_statuses ) {
		if ( ! $needs_payment && $order->has_status( $valid_order_statuses ) ) {
			$order_items = $order->get_items();
			foreach ( $order_items as $order_item ) {
				if ( wc_booking_requires_confirmation( $order_item['product_id'] ) ) {
					$needs_payment = true;
					break;
				}
			}
		}
		return $needs_payment;
	}
}

