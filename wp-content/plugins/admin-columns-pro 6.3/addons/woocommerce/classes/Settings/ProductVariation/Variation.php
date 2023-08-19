<?php

namespace ACA\WC\Settings\ProductVariation;

use AC;
use AC\View;
use stdClass;
use WC_Product;
use WC_Product_Attribute;
use WC_Product_Variation;

/**
 * @since 3.0
 */
class Variation extends AC\Settings\Column
	implements AC\Settings\FormatValue {

	/**
	 * @var string
	 */
	private $variation_display;

	protected function set_name() {
		$this->name = 'variation_display';
	}

	protected function define_options() {
		return [
			'variation_display' => 'short',
		];
	}

	public function create_view() {
		$select = $this->create_element( 'select' )
		               ->set_options( $this->get_display_options() );

		return new View( [
			'label'   => __( 'Display', 'codepress-admin-columns' ),
			'setting' => $select,
		] );
	}

	protected function get_display_options() {
		return [
			''      => __( 'With label' ),
			'short' => __( 'Without label', 'codepress-admin-columns' ),
		];
	}

	/**
	 * @return string
	 */
	public function get_variation_display() {
		return $this->variation_display;
	}

	/**
	 * @param string $variation_display
	 */
	public function set_variation_display( $variation_display ) {
		$this->variation_display = $variation_display;
	}

	/**
	 * @param string $value
	 * @param int    $variation_id
	 *
	 * @return string
	 */
	public function format( $value, $variation_id ) {
		$variation = new WC_Product_Variation( $variation_id );

		switch ( $this->get_variation_display() ) {
			case 'short' :
				$items = [];
				foreach ( $variation->get_attributes() as $attribute_name => $attribute_value ) {
					$items[] = ac_helper()->html->tooltip( urldecode( $attribute_value ), $this->get_attribute_label_by_variation( $variation, $attribute_name ) );
				}

				$value = implode( ' | ', array_filter( $items ) );

				break;
			default:
				$product = wc_get_product( $variation->get_parent_id() );

				$labels = [];

				foreach ( $variation->get_attributes() as $attribute_name => $attribute_value ) {
					$attribute = $this->get_product_attribute( $product, $attribute_name );

					$label = $this->get_attribute_label( $attribute );

					if ( $attribute_value && $attribute->is_taxonomy() ) {

						$term = get_term_by( 'slug', $attribute_value, $attribute->get_taxonomy() );

						if ( $term ) {
							$attribute_value = $term->name;
						}
					}

					if ( ! $attribute_value ) {
						$attribute_value = __( 'Any', 'codepress-admin-columns' );
					}

					$labels[] = sprintf( '<strong>%s</strong>: %s', $label, $attribute_value );
				}

				$value = implode( '<br>', array_filter( $labels ) );
		}

		return $value;
	}

	/**
	 * @param WC_Product_Variation $variation
	 * @param string               $attribute_name
	 *
	 * @return string
	 */
	private function get_attribute_label_by_variation( WC_Product_Variation $variation, $attribute_name ) {
		$attribute = $this->get_product_attribute( wc_get_product( $variation->get_parent_id() ), $attribute_name );

		return $attribute ? $this->get_attribute_label( $attribute ) : $attribute_name;
	}

	/**
	 * @param WC_Product_Attribute $attribute
	 *
	 * @return string
	 */
	public function get_attribute_label( WC_Product_Attribute $attribute ) {
		$label = $attribute->get_name();

		if ( $attribute->is_taxonomy() ) {
			/** @var stdClass $taxonomy */
			$taxonomy = $attribute->get_taxonomy_object();
			$label = $taxonomy->attribute_label;
		}

		return $label;
	}

	/**
	 * @param WC_Product $product
	 * @param string     $attribute_name
	 *
	 * @return false|WC_Product_Attribute
	 */
	private function get_product_attribute( WC_Product $product, $attribute_name ) {
		$product_attributes = $product->get_attributes();

		if ( ! isset( $product_attributes[ $attribute_name ] ) ) {
			return false;
		}

		return $product_attributes[ $attribute_name ];
	}

}