<?php

namespace ACA\WC\Column\ShopOrder;

use AC;
use ACP;

/**
 * @since 2.0
 */
class Actions extends AC\Column
	implements ACP\Export\Exportable {

	public function __construct() {
		$this->set_type( 'order_actions' )
		     ->set_original( true );
	}

	public function export() {
		return false;
	}

}