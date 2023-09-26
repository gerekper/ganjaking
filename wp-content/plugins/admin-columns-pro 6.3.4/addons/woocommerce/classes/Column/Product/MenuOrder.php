<?php

namespace ACA\WC\Column\Product;

use ACP;

/**
 * @since 3.0
 */
class MenuOrder extends ACP\Column\Post\Order {

	public function __construct() {
		parent::__construct();

		$this->set_label( __( 'Menu Order' ) )
		     ->set_group( 'woocommerce' );
	}

	public function is_valid() {
		return true;
	}

}