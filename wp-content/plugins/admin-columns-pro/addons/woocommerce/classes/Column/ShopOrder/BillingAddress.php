<?php

namespace ACA\WC\Column\ShopOrder;

use AC;
use ACA\WC\Export;
use ACP;

class BillingAddress extends AC\Column
	implements ACP\Export\Exportable {

	public function __construct() {
		$this->set_type( 'billing_address' )
		     ->set_original( true );
	}

	public function export() {
		return new Export\ShopOrder\BillingAddress();
	}

}