<?php

namespace ACP\Filtering\Model\CustomField;

use ACP\Filtering\Model;

class Number extends Model\CustomField {

	public function __construct( $column ) {
		parent::__construct( $column );

		$this->set_data_type( 'numeric' );
		$this->set_ranged( true );
	}

}