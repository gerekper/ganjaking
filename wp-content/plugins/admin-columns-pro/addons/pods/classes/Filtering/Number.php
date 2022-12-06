<?php

namespace ACA\Pods\Filtering;

use ACA\Pods\Filtering;

class Number extends Filtering {

	public function __construct( $column ) {
		parent::__construct( $column );

		$this->set_data_type( 'numeric' );
	}

	public function is_ranged() {
		return true;
	}

}