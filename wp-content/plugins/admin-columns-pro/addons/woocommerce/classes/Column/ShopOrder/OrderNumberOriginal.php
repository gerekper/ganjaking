<?php

namespace ACA\WC\Column\ShopOrder;

use AC;
use ACP;

/**
 * @since 3.0.4
 */
class OrderNumberOriginal extends AC\Column
	implements ACP\Search\Searchable {

	public function __construct() {
		$this->set_type( 'order_number' )
		     ->set_original( true );
	}

	protected function register_settings() {
		$width = $this->get_setting( 'width' );

		$width->set_default( 300 );
		$width->set_default( 'px', 'width_unit' );
	}

	public function get_value( $id ) {
		return null;
	}

	public function get_raw_value( $id ) {
		$order = wc_get_order( $id );

		return $order->get_order_number();
	}

	public function search() {
		return new ACP\Search\Comparison\Post\ID();
	}

}