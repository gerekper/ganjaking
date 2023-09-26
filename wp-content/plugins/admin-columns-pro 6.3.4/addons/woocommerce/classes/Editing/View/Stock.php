<?php

namespace ACA\WC\Editing\View;

use ACP;

class Stock extends ACP\Editing\View {

	public function __construct() {
		parent::__construct( 'wc_stock' );
	}

	public function set_manage_stock( $enable ) {
		return $this->set( 'manage_stock', (bool) $enable );
	}

}
