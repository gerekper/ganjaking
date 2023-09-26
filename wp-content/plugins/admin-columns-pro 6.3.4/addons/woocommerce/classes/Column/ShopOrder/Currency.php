<?php

namespace ACA\WC\Column\ShopOrder;

use AC;
use ACA\WC\Filtering;
use ACA\WC\Search;
use ACP;

/**
 * @since 3.0
 */
class Currency extends AC\Column\Meta
	implements ACP\Filtering\Filterable, ACP\Sorting\Sortable, ACP\Export\Exportable, ACP\Search\Searchable, ACP\ConditionalFormat\Formattable {

	use ACP\ConditionalFormat\ConditionalFormatTrait;

	public function __construct() {
		$this->set_label( 'Currency' )
		     ->set_type( 'column-wc-order_currency' )
		     ->set_group( 'woocommerce' );
	}

	public function get_meta_key() {
		return '_order_currency';
	}

	public function get_value( $id ) {
		$value = $this->get_raw_value( $id );

		if ( ! $value ) {
			return $this->get_empty_char();
		}

		return $value;
	}

	public function filtering() {
		return new Filtering\MetaWithoutEmptyOption( $this );
	}

	public function export() {
		return new ACP\Export\Model\RawValue( $this );
	}

	public function sorting() {
		return new ACP\Sorting\Model\Post\Meta( $this->get_meta_key() );
	}

	public function search() {
		return new Search\ShopOrder\Currency();
	}

}