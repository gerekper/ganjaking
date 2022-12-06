<?php

namespace ACA\WC\Column\ProductVariation;

use AC;
use ACA\WC\Editing;
use ACP;

/**
 * @since 3.5.7
 */
class ShippingClass extends AC\Column
	implements ACP\Export\Exportable, ACP\Search\Searchable, ACP\Editing\Editable, ACP\ConditionalFormat\Formattable {

	use ACP\ConditionalFormat\FilteredHtmlFormatTrait;

	public function __construct() {
		$this->set_type( 'column-wc-shipping_class' )
		     ->set_label( __( 'Shipping Class', 'woocommerce' ) )
		     ->set_group( 'woocommerce' );
	}

	public function get_taxonomy() {
		return 'product_shipping_class';
	}

	public function get_value( $post_id ) {
		$term = get_term_by( 'id', $this->get_raw_value( $post_id ), $this->get_taxonomy() );

		if ( ! $term ) {
			return $this->get_empty_char();
		}

		$icon = '';

		if ( empty( wp_get_post_terms( $post_id, 'product_shipping_class' ) ) ) {
			$icon = ac_helper()->html->tooltip( '<span class="woocommerce-help-tip"></span>', __( 'Shipping Class managed by product', 'codepress-admin-columns' ) );
		}

		return sprintf( '%s %s', ac_helper()->taxonomy->get_term_display_name( $term ), $icon );
	}

	public function get_raw_value( $post_id ) {
		return wc_get_product( $post_id )->get_shipping_class_id();
	}

	public function search() {
		return new ACP\Search\Comparison\Post\Taxonomy( $this->get_taxonomy() );
	}

	public function export() {
		return new ACP\Export\Model\StrippedValue( $this );
	}

	public function editing() {
		return new Editing\ProductVariation\ShippingClass();
	}

}