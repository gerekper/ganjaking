<?php

namespace ACA\WC\Column\ShopOrder;

use AC;
use ACA\WC\Export;
use ACP;

/**
 * @since 2.0
 */
class OrderNotes extends AC\Column
	implements ACP\Export\Exportable {

	public function __construct() {
		$this->set_type( 'order_notes' )
		     ->set_original( true );
	}

	public function export() {
		return new Export\ShopOrder\OrderNotes( $this );
	}
}