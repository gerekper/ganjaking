<?php

namespace ACA\WC\Export\Product;

use AC\Column;
use ACP;

class Variation implements ACP\Export\Service {

	private $column;

	public function __construct( Column $column ) {
		$this->column = $column;
	}

	public function get_value( $id ) {
		return (string) count( $this->column->get_raw_value( $id ) );
	}

}