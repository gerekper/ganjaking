<?php

namespace ACA\WC\Export\User;

use ACP;
use ACA\WC;

/**
 * @property WC\Column\User\Orders $column
 */
class Orders extends ACP\Export\Model {

	public function __construct( WC\Column\User\Orders $column ) {
		parent::__construct( $column );
	}

	public function get_value( $id ) {
		$result = [];

		foreach ( $this->column->get_raw_value( $id ) as $order ) {
			$result[] = $order->get_id();
		}

		return implode( ', ', $result );
	}

}