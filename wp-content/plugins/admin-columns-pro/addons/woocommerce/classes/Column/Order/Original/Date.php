<?php

namespace ACA\WC\Column\Order\Original;

use AC;
use ACA\WC;
use ACP;

class Date extends AC\Column implements ACP\Search\Searchable {

	public function __construct() {
		$this->set_type( 'order_date' )
		     ->set_original( true );
	}

	public function search() {
		return new WC\Search\Order\Date\CreatedDate();
	}

}