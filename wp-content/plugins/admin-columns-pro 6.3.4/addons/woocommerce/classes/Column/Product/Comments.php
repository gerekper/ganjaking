<?php

namespace ACA\WC\Column\Product;

use AC;

/**
 * Deprecated: Column does not exist anymore in WooCommerce
 */
class Comments extends AC\Column {

	public function __construct() {
		$this->set_type( 'comments' )
		     ->set_original( true );
	}

}