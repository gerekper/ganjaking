<?php

namespace ACA\WC\Column\Product;

use AC;
use ACA\WC\Editing;
use ACA\WC\Filtering;
use ACP;

/**
 * @since 1.0
 */
class Price extends AC\Column\Meta
	implements ACP\Editing\Editable, ACP\Filtering\Filterable, ACP\Search\Searchable {

	public function __construct() {
		$this->set_type( 'price' )
		     ->set_original( true );
	}

	public function get_value( $id ) {
		return null;
	}

	public function get_meta_key() {
		return '_price';
	}

	public function filtering() {
		$insert_tax = get_option( 'woocommerce_prices_include_tax' );
		$tax_included = get_option( 'woocommerce_tax_display_shop' );

		if ( ( 'yes' === $insert_tax && 'incl' === $tax_included ) || ( 'no' === $insert_tax && 'excl' === $tax_included ) ) {
			return new Filtering\Product\Price( $this );
		}

		return new ACP\Filtering\Model\Disabled( $this );
	}

	public function editing() {
		return new Editing\Product\Price();
	}

	public function search() {
		return new ACP\Search\Comparison\Meta\Decimal( $this->get_meta_key(), AC\MetaType::POST );
	}

}