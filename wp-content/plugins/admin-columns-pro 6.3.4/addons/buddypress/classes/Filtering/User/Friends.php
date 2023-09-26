<?php

namespace ACA\BP\Filtering\User;

use ACP;

class Friends extends ACP\Filtering\Model\Meta {

	public function __construct( $column ) {
		parent::__construct( $column );

		$this->set_data_type( 'numeric' );
	}

	public function is_ranged() {
		return true;
	}

}