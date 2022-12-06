<?php

namespace ACA\WC\Column\ShopOrder;

use AC;
use ACA\WC\Search;

/**
 * @since 3.6
 */
class PaidAmount extends AC\Column {

	public function __construct() {
		$this->set_type( 'column-wc-order_paid' )
		     ->set_label( __( 'Paid Amount', 'codepress-admin-columns' ) )
		     ->set_group( 'woocommerce' );
	}

	public function get_value( $id ) {
		$price = $this->get_raw_value( $id );

		if ( ! $price ) {
			return $this->get_empty_char();
		}

		return wc_price( $this->get_raw_value( $id ), [ 'currency' => wc_get_order( $id )->get_currency() ] );
	}

	public function get_raw_value( $id ) {
		$order = wc_get_order( $id );

		return $order->is_paid()
			? $order->get_total() - $order->get_total_refunded()
			: 0;
	}

}