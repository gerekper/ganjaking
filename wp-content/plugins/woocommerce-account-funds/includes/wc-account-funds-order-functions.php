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
