<?php

namespace ACA\WC\Field\ShopOrder;

use ACA\WC\Column;
use ACA\WC\Export;
use ACA\WC\Field;
use ACP;
use WC_DateTime;
use WC_Order;

/**
 * @since 3.0
 * @property Column\ShopOrder\OrderDate $column
 */
abstract class OrderDate extends Field implements ACP\Export\Exportable {

	/**
	 * @param WC_Order $order
	 *
	 * @return WC_DateTime|false
	 */
	abstract public function get_date( WC_Order $order );

	public function get_value( $id ) {
		$order = wc_get_order( $id );

		$date = $this->get_date( $order );

		if ( ! $date ) {
			return false;
		}

		return $date->date( 'U' );
	}

	public function get_meta_key() {
		return false;
	}

	public function export() {
		return new Export\ShopOrder\OrderDate( $this->column );
	}

}