<?php

namespace ACA\WC\Export\User;

use ACA\WC;
use ACP;

class Orders implements ACP\Export\Service {

	protected $column;

	public function __construct( WC\Column\User\Orders $column ) {
		$this->column = $column;
	}

	public function get_value( $id ) {
		$result = [];

		$orders = (array) $this->column->get_raw_value( $id );

		foreach ( $orders as $order ) {
			$result[] = $order->get_id();
		}

		return implode( ', ', $result );
	}

}