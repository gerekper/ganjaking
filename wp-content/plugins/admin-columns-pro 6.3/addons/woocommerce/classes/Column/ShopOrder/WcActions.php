<?php

namespace ACA\WC\Column\ShopOrder;

use AC;
use ACP;

class WcActions extends AC\Column
	implements ACP\Export\Exportable {

	public function __construct() {
		$this->set_type( 'wc_actions' )
		     ->set_original( true );
	}

	public function export() {
		return false;
	}

}