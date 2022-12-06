<?php

namespace ACA\WC\Column\ShopOrder;

use AC;
use ACP\Export;

class Subtotal extends AC\Column
	implements Export\Exportable {

	public function __construct() {
		$this->set_type( 'column-wc-order-subtotal' )
		     ->set_label( __( 'Subtotal', 'woocommerce' ) )
		     ->set_group( 'woocommerce' );
	}

	public function get_value( $post_id ) {
		return wc_get_order( $post_id )->get_subtotal_to_display();
	}

	public function export() {
		return new Export\Model\StrippedValue( $this );
	}

}