<?php

namespace ACA\WC\Filtering\Product;

use ACP;

class GroupedProducts extends ACP\Filtering\Model\Meta {

	public function get_filtering_vars( $vars ) {
		$vars['meta_query'][] = [
			'key'     => $this->column->get_meta_key(),
			'value'   => serialize( (int) $this->get_filter_value() ),
			'compare' => 'LIKE',
		];

		$vars['tax_query'][] = [
			'taxonomy' => 'product_type',
			'field'    => 'slug',
			'terms'    => 'grouped',
		];

		return $vars;
	}

	public function get_grouped_products() {
		return get_posts( [
			'fields'         => 'ids',
			'post_type'      => 'product',
			'posts_per_page' => -1,
			'tax_query'      => [
				[
					'taxonomy' => 'product_type',
					'field'    => 'slug',
					'terms'    => 'grouped',
				],
			],
		] );

	}

	public function get_filtering_data() {
		$products = [];

		foreach ( $this->get_grouped_products() as $product_id ) {
			$product = wc_get_product( $product_id );

			foreach ( $product->get_children() as $_id ) {
				if ( isset( $products[ $_id ] ) ) {
					continue;
				}

				$_product = wc_get_product( $_id );
				if ( $_product ) {
					$products[ $_id ] = $_product->get_title();
				}
			}
		}

		return [
			'options' => $products,
		];
	}

}
