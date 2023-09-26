<?php

namespace ACA\WC\Export\ShopOrder;

use ACA\WC\Column;
use ACP;

class OrderDate implements ACP\Export\Service {

	protected $column;

	public function __construct( Column\ShopOrder\OrderDate $column ) {
		$this->column = $column;
	}

	public function get_value( $id ) {
		if ( ! $this->column->get_field() ) {
			return '';
		}

		$date = $this->column->get_field()->get_date( wc_get_order( $id ) );

		if ( ! $date ) {
			return '';
		}

		return $date->format( 'Y-m-d H:i' );
	}

}