<?php

namespace ACA\WC\Export\ShopCoupon;

use AC\Column;
use ACP;

class EmailRestrictions implements ACP\Export\Service {

	private $column;

	public function __construct( Column $column ) {
		$this->column = $column;
	}

	public function get_value( $id ) {
		return implode( ', ', $this->column->get_raw_value( $id ) );
	}

}