<?php

namespace ACA\Types\Filtering;

use ACA\Types\Column;
use ACA\Types\Filtering;

/**
 * @property Column $column
 */
class Number extends Filtering {

	public function __construct( $column ) {
		parent::__construct( $column );

		$this->set_data_type( 'numeric' );
	}

	public function is_ranged() {
		return true;
	}

}