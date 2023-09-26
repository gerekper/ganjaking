<?php

namespace ACA\WC\Editing\View;

use ACP;

class Notes extends ACP\Editing\View {

	public function __construct() {
		parent::__construct( 'wc_order_notes' );
	}

	public function set_mode( $mode ) {
		return $this->set( 'mode', (string) $mode );
	}

}
