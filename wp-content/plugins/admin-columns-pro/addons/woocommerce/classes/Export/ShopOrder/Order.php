<?php

namespace ACA\WC\Export\ShopOrder;

use ACP;

/**
 * WooCommerce order title (default column) exportability model
 * @since 2.2.1
 */
class Order extends ACP\Export\Model {

	public function get_value( $id ) {
		$order = wc_get_order( $id );

		$first_name = $order->get_billing_first_name();
		$last_name = $order->get_billing_last_name();
		$company = $order->get_billing_company();

		if ( $order->get_customer_id() ) {
			$user = get_user_by( 'id', $order->get_customer_id() );
			$name = $user->display_name;
		} elseif ( $first_name || $last_name ) {
			$name = $first_name . ' ' . $last_name;
		} elseif ( $company ) {
			$name = $company;
		} else {
			$name = __( 'guest', 'woocommerce' );
		}

		return sprintf( '%d (%s)', $order->get_order_number(), $name );
	}

}