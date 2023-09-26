<?php

namespace ACA\WC\Column\ShopOrder;

use AC;
use ACA\WC;
use ACP\Search;
use WC_Order_Item_Fee;

class Fees extends AC\Column implements Search\Searchable {

	public function __construct() {
		$this->set_type( 'column-wc-order_fees' )
		     ->set_label( __( 'Fees', 'codepress-admin-columns' ) )
		     ->set_group( 'woocommerce' );
	}

	public function get_value( $id ) {
		$order = wc_get_order( $id );

		if ( ! $order ) {
			return $this->get_empty_char();
		}

		$values = [];
		$items = $order->get_items( [ 'fee' ] );

		foreach ( $items as $item ) {
			if ( $item instanceof WC_Order_Item_Fee ) {
				$values[] = sprintf( '%s - %s', wc_price( $item->get_amount() ), $item->get_name() );
			}
		}

		return ! empty( $values )
			? implode( $this->get_separator(), $values )
			: $this->get_empty_char();
	}

	public function search() {
		return new WC\Search\ShopOrder\HasFees();
	}

}