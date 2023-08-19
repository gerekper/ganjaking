<?php

namespace ACA\WC\Editing\ProductVariation;

use ACA\WC\Editing;
use ACP;
use ACP\Editing\View;
use stdClass;
use WC_Product;
use WC_Product_Attribute;
use WC_Product_Variation;
use WP_Term;

class Variation implements ACP\Editing\Service {

	public function get_view( string $context ): ?View {
		return $context === self::CONTEXT_BULK
			? null
			: new Editing\View\Variation();
	}

	public function get_value( $id ) {
		$variation = new WC_Product_Variation( $id );
		$product = wc_get_product( $variation->get_parent_id() );

		return (object) [
			'value'   => $variation->get_attributes(),
			'options' => $this->get_product_variation_options( $product ),
		];
	}

	public function update( int $id, $data ): void {
		$variation = new WC_Product_Variation( $id );
		$variation->set_attributes( $data );
		$variation->save();
	}

	private function get_product_variation_options( WC_Product $product ): array {
		$results = [];

		foreach ( $product->get_attributes() as $key => $attribute ) {
			if ( ! $attribute instanceof WC_Product_Attribute ) {
				continue;
			}

			// Is used for variations
			if ( ! $attribute->get_variation() ) {
				continue;
			}

			$options = [];

			if ( $attribute->is_taxonomy() ) {
				foreach ( $attribute->get_terms() as $term ) {
					if ( $term instanceof WP_Term ) {
						$options[ $term->slug ] = $term->name;
					}
				}
			} else {
				$options = array_combine( $attribute->get_options(), $attribute->get_options() );
			}

			$results[ $key ] = [
				'label'   => $this->get_attribute_label( $attribute ),
				'options' => $options,
			];
		}

		return $results;
	}

	private function get_attribute_label( WC_Product_Attribute $attribute ): string {
		$label = $attribute->get_name();

		if ( $attribute->is_taxonomy() ) {
			/** @var stdClass $taxonomy */
			$taxonomy = $attribute->get_taxonomy_object();
			$label = $taxonomy->attribute_label;
		}

		return $label;
	}

}