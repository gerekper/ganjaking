<?php

namespace ACA\WC\Column\Product;

use AC;
use ACA\WC\Editing;
use ACA\WC\Filtering;
use ACA\WC\Search;
use ACA\WC\Sorting;
use ACP;

/**
 * @since 3.0
 */
class GroupedProducts extends AC\Column\Meta
	implements ACP\Editing\Editable, ACP\Filtering\Filterable, ACP\Export\Exportable, ACP\Search\Searchable, ACP\ConditionalFormat\Formattable {

	use ACP\ConditionalFormat\FilteredHtmlFormatTrait;

	public function __construct() {
		$this->set_group( 'woocommerce' )
		     ->set_type( 'column-wc-product-grouped_products' )
		     ->set_label( __( 'Grouped Products', 'woocommerce' ) );
	}

	public function get_value( $id ) {
		$children = $this->get_raw_value( $id );

		if ( empty( $children ) ) {
			return $this->get_empty_char();
		}

		/**
		 * @var AC\Collection $values
		 */
		$values = $this->get_formatted_value( new AC\Collection( $children ) );

		return $values->implode( $this->get_separator() );
	}

	public function get_meta_key() {
		return '_children';
	}

	public function register_settings() {
		$this->add_setting( new AC\Settings\Column\Post( $this ) );
	}

	public function editing() {
		return new Editing\Product\GroupedProducts();
	}

	public function filtering() {
		return new Filtering\Product\GroupedProducts( $this );
	}

	public function search() {
		return new Search\Product\GroupedProducts();
	}

	public function export() {
		return new ACP\Export\Model\StrippedValue( $this );
	}

}