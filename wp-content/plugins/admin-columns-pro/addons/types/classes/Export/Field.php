<?php

namespace ACA\Types\Export;

use ACA\Types\Column;
use ACP;

class Field implements ACP\Export\Service {

	protected $column;

	public function __construct( Column $column ) {
		$this->column = $column;
	}

	public function get_value( $id ) {
		$raw_value = (array) $this->column->get_raw_value( $id );

		return implode( ', ', array_filter( $raw_value ) );
	}

}