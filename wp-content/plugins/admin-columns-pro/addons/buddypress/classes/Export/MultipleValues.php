<?php

namespace ACA\BP\Export;

use ACA\BP\Column;
use ACP;

/**
 * @property Column\Profile $column
 */
class MultipleValues extends ACP\Export\Model {

	public function __construct( Column\Profile $column ) {
		parent::__construct( $column );
	}

	public function get_value( $id ) {
		$values = (array) $this->column->get_raw_value( $id );

		return implode( ', ', $values );
	}

}