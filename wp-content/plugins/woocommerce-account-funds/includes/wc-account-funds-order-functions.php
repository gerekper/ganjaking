<?php
/**
 * Order functions
 *
 * @package WC_Account_Funds/Functions
 * @since   2.6.3
 */

defined( 'ABSPATH' ) || exit;

/**
 * Formats the price with the order's currency symbol.
 *
 * @since 2.6.3
 *
 * @param WC_Order $order Order object.
 * @param float    $price Raw price.
 * @param array    $args  Optional. Arguments to format the price. Default empty.
 * @return string
 */
function wc_account_funds_format_order_price( $order, $price, $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'currency' => $order->get_currency(),
		)
	);

	return wc_price( $price, $args );
}

/**
 * Gets if the order contains deposit products.
 *
 * @since 2.9.0
 *
 * @param WC_Order $order Order object.
 * @return bool
 */
function wc_account_funds_order_contains_deposit( $order ) {
	$items = $order->get_items();

	foreach ( $items as $item ) {
		$product = $item->get_product();

		if ( $product && $product->is_type( array( 'deposit', 'topup' ) ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Pays the order with the customer's funds.
 *
 * @since 3.0.0
 *
 * @param WC_Order $order       Order object.
 * @param float    $order_total Optional. Order total. Default null.
 * @return true|WP_Error True on success, WP_Error on failure.
 */
function wc_account_funds_pay_order_with_funds( $order, $order_total = null ) {
	$customer_id = $order->get_customer_id();

	if ( ! $customer_id ) {
		return new WP_Error( 'customer_not_found', __( 'Customer not found.', 'woocommerce-account-funds' ) );
	}

	$funds = WC_Account_Funds::get_account_funds( $customer_id, false, $order->get_id() );

	if ( is_null( $order_total ) ) {
		$order_total = $order->get_total();
	}

	if ( $funds < $order_total ) {
		return new WP_Error(
			'insufficient_funds',
			sprintf(
				/* translators: %s funds name */
				_x( 'Insufficient %s amount.', 'payment error', 'woocommerce-account-funds' ),
				wc_get_account_funds_name()
			)
		);
	}

	WC_Account_Funds_Manager::decrease_user_funds( $customer_id, $order_total );

	$order->update_meta_data( '_funds_used', $order_total );
	$order->update_meta_data( '_funds_removed', 1 );
	$order->update_meta_data( '_funds_version', WC_ACCOUNT_FUNDS_VERSION );
	$order->save_meta_data();

	$order->add_order_note(
		sprintf(
			/* translators: 1: Payment gateway title, 2: Funds used */
			_x( '%1$s payment applied: %2$s', 'order note', 'woocommerce-account-funds' ),
			$order->get_payment_method_title(),
			wc_account_funds_format_order_price( $order, $order_total )
		)
	);

	return true;
}
