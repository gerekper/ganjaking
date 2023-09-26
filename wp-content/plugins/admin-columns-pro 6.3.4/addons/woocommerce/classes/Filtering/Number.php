<?php

namespace ACA\WC\Filtering;

use ACP;

class Number extends ACP\Filtering\Model\Meta {

	public function __construct( $column ) {
		parent::__construct( $column );

		$this->set_data_type( 'numeric' );
		$this->set_ranged( true );
	}

}