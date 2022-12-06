<?php

namespace ACA\WC\Column\ShopOrder;

use AC;
use ACA\WC\ConditionalFormat\Formatter\PriceFormatter;
use ACA\WC\Filtering;
use ACA\WC\Search;
use ACP;
use ACP\ConditionalFormat\FormattableConfig;
use ACP\ConditionalFormat\Formatter;

/**
 * @since 3.6
 */
class Refunds extends AC\Column implements ACP\Search\Searchable, ACP\Filtering\Filterable, ACP\ConditionalFormat\Formattable {

	public function __construct() {
		$this->set_type( 'column-wc-order_refunds' )
		     ->set_label( __( 'Refunds', 'codepress-admin-columns' ) )
		     ->set_group( 'woocommerce' );
	}

	public function conditional_format(): ?FormattableConfig {
		return new FormattableConfig( new PriceFormatter() );
	}

	public function get_value( $id ) {
		$price = $this->get_raw_value( $id );

		if ( ! $price ) {
			return $this->get_empty_char();
		}

		return wc_price( $this->get_raw_value( $id ), [ 'currency' => wc_get_order( $id )->get_currency() ] );
	}

	public function get_raw_value( $id ) {
		return wc_get_order( $id )->get_total_refunded();
	}

	public function search() {
		return new Search\ShopOrder\Refunds();
	}

	public function filtering() {
		return new Filtering\ShopOrder\Refunds( $this );
	}

}