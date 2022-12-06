<?php

namespace ACA\WC\Export\ShopOrder;

use ACA\WC\Column;
use ACP;

/**
 * @property Column\ShopOrder\OrderDate $column
 */
class OrderDate extends ACP\Export\Model {

	public function __construct( Column\ShopOrder\OrderDate $column ) {
		parent::__construct( $column );
	}

	public function get_value( $id ) {
		if ( ! $this->column->get_field() ) {
			return false;
		}

		$date = $this->column->get_field()->get_date( wc_get_order( $id ) );

		if ( ! $date ) {
			return false;
		}

		return $date->format( 'Y-m-d H:i' );
	}

}