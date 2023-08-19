<?php

namespace ACA\WC\Column\ShopOrder\Address;

use ACA\WC\Column\ShopOrder\Address;
use ACA\WC\Settings;

/**
 * @since 3.0
 */
class Billing extends Address {

	public function __construct() {
		$this->set_type( 'column-wc-order_billing_address' )
		     ->set_label( __( 'Billing Address', 'codepress-admin-columns' ) )
		     ->set_group( 'woocommerce' );
	}

	protected function get_meta_key_prefix() {
		return '_billing_';
	}

	protected function get_formatted_address( \WC_Order $order ) {
		return $order->get_formatted_billing_address();
	}

	public function get_setting_address_object() {
		return new Settings\Address\Billing( $this );
	}

}