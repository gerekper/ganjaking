<?php

namespace ACA\BP\Export;

use ACA\BP\Column;
use ACP;

/**
 * @property Column\Profile $column
 */
class MultipleValues implements ACP\Export\Service {

	private $column;

	public function __construct( Column\Profile $column ) {
		$this->column = $column;
	}

	public function get_value( $id ) {
		$values = (array) $this->column->get_raw_value( $id );

		return implode( ', ', $values );
	}

}