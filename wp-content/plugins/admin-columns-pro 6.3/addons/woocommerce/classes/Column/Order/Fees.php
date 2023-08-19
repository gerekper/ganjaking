<?php

namespace ACA\WC\Column\Order;

use AC;
use ACP;
use WC_Order_Item_Fee;

class Fees extends AC\Column implements ACP\Export\Exportable, ACP\ConditionalFormat\Formattable {

	use ACP\ConditionalFormat\ConditionalFormatTrait;

	public function __construct() {
		$this->set_type( 'column-order_fees' )
		     ->set_label( __( 'Fees', 'codepress-admin-columns' ) )
		     ->set_group( 'woocommerce' );
	}

	public function get_value( $id ) {
		$order = wc_get_order( $id );

		if ( ! $order ) {
			return $this->get_empty_char();
		}

		$values = [];

		foreach ( $order->get_items( [ 'fee' ] ) as $item ) {
			if ( $item instanceof WC_Order_Item_Fee ) {
				$values[] = sprintf( '%s - %s', wc_price( $item->get_amount() ), $item->get_name() );
			}
		}

		return ! empty( $values )
			? implode( $this->get_separator(), $values )
			: $this->get_empty_char();
	}

	public function export() {
		return new ACP\Export\Model\StrippedValue( $this );
	}

}