<?php

namespace ACA\WC\Column\Order;

use AC;
use ACA\WC\ConditionalFormat\Formatter\PriceFormatter;
use ACP;
use ACP\ConditionalFormat\FormattableConfig;

class Refunds extends AC\Column implements ACP\Export\Exportable, ACP\ConditionalFormat\Formattable {

	public function __construct() {
		$this->set_type( 'column-order_refunds' )
		     ->set_label( __( 'Refunds', 'codepress-admin-columns' ) )
		     ->set_group( 'woocommerce' );
	}

	public function get_value( $id ) {
		$order = wc_get_order( $id );
		$refunds = $order ? $order->get_total_refunded() : false;

		return $refunds ? wc_price( $refunds, [ 'currency' => $order->get_currency() ] ) : $this->get_empty_char();
	}

	public function get_raw_value( $id ) {
		$order = wc_get_order( $id );

		return $order ? $order->get_total_refunded() : false;
	}

	public function export() {
		return new ACP\Export\Model\StrippedValue( $this );
	}

	public function conditional_format(): ?FormattableConfig {
		return new FormattableConfig( new PriceFormatter() );
	}

}