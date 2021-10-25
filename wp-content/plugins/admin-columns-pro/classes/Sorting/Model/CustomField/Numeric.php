<?php

namespace ACP\Sorting\Model\CustomField;

use AC;
use ACP\Sorting\Model\CustomField;

class Numeric extends CustomField {

	public function __construct( AC\Column\CustomField $column ) {
		parent::__construct( $column );

		$this->set_data_type( 'numeric' );
	}

}