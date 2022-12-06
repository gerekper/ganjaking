<?php

namespace ACA\WC\Helper\Select\Formatter;

use AC;
use WP_Post;

class ProductTitleAndSKU extends AC\Helper\Select\Formatter {

	/**
	 * @param WP_Post $post
	 *
	 * @return string
	 */
	public function get_label( $post ) {
		$label = $post->post_title;
		$sku = get_post_meta( $post->ID, '_sku', true );

		if ( ! $label ) {
			$label = $post->ID;
		}

		if ( $sku ) {
			$label .= sprintf( ' (%s)', $sku );
		}

		return $label;
	}

	protected function get_label_unique( $label, $entity ) {
		if ( 'product_variation' === $entity->post_type ) {
			$product = wc_get_product( $entity->ID );
			$attributes = array_values( $product->get_attributes() );

			return $label . sprintf( ' (%s)', implode( ', ', $attributes ) );
		}

		return parent::get_label_unique( $label, $entity );
	}

}