<?php

namespace ACA\WC\Filtering\ShopOrder;

use ACP;

class MetaDate extends ACP\Filtering\Model\MetaDate {

	public function __construct( $column ) {
		parent::__construct( $column );

		$this->set_date_format( 'U' );
	}

}