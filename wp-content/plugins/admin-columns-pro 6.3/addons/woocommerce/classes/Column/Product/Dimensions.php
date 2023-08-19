<?php

namespace ACA\WC\Column\Product;

use AC;
use ACA\WC\Editing;
use ACA\WC\Sorting;
use ACP;

/**
 * @since 1.0
 */
class Dimensions extends AC\Column
	implements ACP\Sorting\Sortable, ACP\Editing\Editable, ACP\Export\Exportable, ACP\ConditionalFormat\Formattable {

	use ACP\ConditionalFormat\ConditionalFormatTrait;

	public function __construct() {
		$this->set_type( 'column-wc-dimensions' )
		     ->set_label( __( 'Dimensions', 'woocommerce' ) )
		     ->set_group( 'woocommerce' );
	}

	public function is_valid() {
		return function_exists( 'wc_product_dimensions_enabled' ) && wc_product_dimensions_enabled();
	}

	public function get_value( $post_id ) {
		$value = $this->human_readable_surface( $this->get_dimensions( $post_id ) );

		if ( empty( $value ) ) {
			return $this->get_empty_char();
		}

		return $value;
	}

	public function get_raw_value( $post_id ) {
		return $this->get_dimensions( $post_id );
	}

	public function editing() {
		return new Editing\Product\Dimensions();
	}

	public function sorting() {
		return new Sorting\Product\Dimensions();
	}

	public function export() {
		return new ACP\Export\Model\StrippedValue( $this );
	}

	/**
	 * @param int $post_id
	 *
	 * @return array|false
	 */
	public function get_dimensions( $post_id ) {
		$product = wc_get_product( $post_id );

		if ( ! $product || $product->is_virtual() ) {
			return false;
		}

		return [
			'length' => $product->get_length(),
			'width'  => $product->get_width(),
			'height' => $product->get_height(),
		];
	}

	/**
	 * @param string $dimension
	 *
	 * @return string
	 */
	private function get_dimension_label( $dimension ) {
		$labels = [
			'length' => __( 'Length', 'codepress-admin-columns' ),
			'width'  => __( 'Width', 'codepress-admin-columns' ),
			'height' => __( 'Height', 'codepress-admin-columns' ),
		];

		if ( ! isset( $labels[ $dimension ] ) ) {
			return false;
		}

		return $labels[ $dimension ];
	}

	/**
	 * @param array $dimensions
	 *
	 * @return array
	 */
	public function dimensions_used( $dimensions ) {
		$values = [];

		foreach ( [ 'length', 'width', 'height' ] as $d ) {
			if ( ! empty( $dimensions[ $d ] ) ) {
				$label = $this->get_dimension_label( $d );
				$values[ $label ] = $dimensions[ $d ];
			}
		}

		return $values;
	}

	/**
	 * @param array $dimensions
	 *
	 * @return string
	 */
	private function human_readable_surface( $dimensions ) {
		if ( empty( $dimensions ) ) {
			return false;
		}

		$values = $this->dimensions_used( $dimensions );

		if ( ! $values ) {
			return false;
		}

		return implode( ' x ', $values ) . ' ' . $this->get_dimension_unit();
	}

	/**
	 * @return string
	 */
	private function get_dimension_unit() {
		return get_option( 'woocommerce_dimension_unit' );
	}

}