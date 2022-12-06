<?php

namespace ACA\WC\Column\Product;

use AC;
use ACA\WC\Export;
use ACA\WC\Search;
use ACA\WC\Type\ProductAttribute;
use ACP;

/**
 * @since 3.5.7
 */
class DefaultFormValues extends AC\Column
	implements ACP\Export\Exportable, ACP\Search\Searchable, ACP\ConditionalFormat\Formattable {

	use ACP\ConditionalFormat\FilteredHtmlFormatTrait;

	public function __construct() {
		$this->set_type( 'column-wc-product_default_form_values' )
		     ->set_label( __( 'Default Form Values', 'codepress-admin-columns' ) )
		     ->set_group( 'woocommerce' );
	}

	public function get_value( $id ) {
		$default_attributes = $this->get_raw_value( $id );

		if ( empty( $default_attributes ) ) {
			return $this->get_empty_char();
		}

		$result = [];

		foreach ( $default_attributes as $key => $default_value ) {
			$a = new ProductAttribute( $key );

			$result[] = sprintf( '<strong>%s:</strong> %s', $a->get_label(), $default_value );
		}

		return implode( ', ', $result );
	}

	public function get_raw_value( $id ) {
		$product = wc_get_product( $id );

		if ( $product->get_type() !== 'variable' ) {
			return null;
		}

		return $product->get_default_attributes();
	}

	public function search() {
		return new Search\Product\Coupons();
	}

	public function export() {
		return new ACP\Export\Model\StrippedValue( $this );
	}

}