<?php

namespace ACA\Types\Export;

use ACA\Types\Column;
use ACP;

/**
 * @property Column $column
 */
class Field extends ACP\Export\Model {

	/**
	 * @param Column $column
	 */
	public function __construct( Column $column ) {
		parent::__construct( $column );
	}

	/**
	 * @param int $id
	 *
	 * @return string
	 */
	public function get_value( $id ) {
		$raw_value = (array) $this->column->get_raw_value( $id );

		return implode( ', ', array_filter( $raw_value ) );
	}

}