<?php

namespace ACA\WC\Column\Order;

use AC;
use ACA\WC\Settings\Product\UseIcon;
use ACP;

class CustomerNote extends AC\Column implements ACP\ConditionalFormat\Formattable {

	use ACP\ConditionalFormat\ConditionalFormatTrait;

	public function __construct() {
		$this->set_type( 'column-order_customer_note' )
		     ->set_label( __( 'Customer Note', 'codepress-admin-columns' ) )
		     ->set_group( 'woocommerce' );
	}

	public function get_raw_value( $id ) {
		$order = wc_get_order( $id );

		$note = $order ? $order->get_customer_note() : false;

		return $note ?: false;
	}

	protected function register_settings() {
		parent::register_settings();

		$this->add_setting( new UseIcon( $this ) );
	}
}