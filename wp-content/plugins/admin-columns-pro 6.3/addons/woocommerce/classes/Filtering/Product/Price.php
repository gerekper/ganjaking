<?php

namespace ACA\WC\Filtering\Product;

use ACP;

class Price extends ACP\Filtering\Model\Meta {

	public function __construct( $column ) {
		parent::__construct( $column );

		$this->set_data_type( 'numeric' );
	}

	public function is_ranged() {
		return true;
	}

}