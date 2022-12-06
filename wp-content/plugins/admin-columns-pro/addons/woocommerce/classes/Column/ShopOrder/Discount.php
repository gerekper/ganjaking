<?php

namespace ACA\WC\Column\ShopOrder;

use ACA\WC\ConditionalFormat\Formatter\PriceFormatter;
use ACA\WC\Filtering;
use ACP;
use ACP\ConditionalFormat\FormattableConfig;
use ACP\ConditionalFormat\Formatter;

class Discount extends ACP\Column\Meta implements ACP\ConditionalFormat\Formattable {

	public function __construct() {
		$this->set_type( 'column-wc-order_discount' )
		     ->set_label( __( 'Order Discount', 'codepress-admin-columns' ) )
		     ->set_group( 'woocommerce' );
	}

	public function conditional_format(): ?FormattableConfig {
		return new FormattableConfig( new PriceFormatter() );
	}

	public function get_meta_key() {
		return '_cart_discount';
	}

	public function get_value( $id ) {
		$order = wc_get_order( $id );

		if ( ! $order->get_total_discount() ) {
			return $this->get_empty_char();
		}

		return $order->get_discount_to_display();
	}

	public function filtering() {
		return new Filtering\Number( $this );
	}

	public function sorting() {
		return new ACP\Sorting\Model\Post\Meta( $this->get_meta_key() );
	}

	public function editing() {
		return false;
	}

}