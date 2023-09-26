<?php

namespace ACA\Types\Filtering;

use ACP;

class Date extends ACP\Filtering\Model\MetaDate {

	public function __construct( $column ) {
		parent::__construct( $column );

		$this->set_date_format( 'U' );
	}

}