<?php

namespace ACA\WC\Filtering\Product;

use ACP;

class Visibility extends ACP\Filtering\Model\Post\Taxonomy {

	public function get_filtering_vars( $vars ) {

		switch ( $this->get_filter_value() ) {
			case 'search':
				$vars['tax_query'][] = [
					'taxonomy' => 'product_visibility',
					'field'    => 'slug',
					'terms'    => [ 'exclude-from-search' ],
					'operator' => 'NOT IN',
				];
				$vars['tax_query'][] = [
					'taxonomy' => 'product_visibility',
					'field'    => 'slug',
					'terms'    => [ 'exclude-from-catalog' ],
					'operator' => 'IN',
				];

				break;
			case 'catalog':
				$vars['tax_query'][] = [
					'taxonomy' => 'product_visibility',
					'field'    => 'slug',
					'terms'    => [ 'exclude-from-catalog' ],
					'operator' => 'NOT IN',
				];
				$vars['tax_query'][] = [
					'taxonomy' => 'product_visibility',
					'field'    => 'slug',
					'terms'    => [ 'exclude-from-search' ],
					'operator' => 'IN',
				];
				break;
			case 'visible':
				$vars['tax_query'][] = [
					'taxonomy' => 'product_visibility',
					'field'    => 'slug',
					'terms'    => [ 'exclude-from-catalog', 'exclude-from-search' ],
					'operator' => 'NOT IN',
				];
				break;
			case 'hidden':
				$vars['tax_query'][] = [
					'taxonomy' => 'product_visibility',
					'field'    => 'slug',
					'terms'    => [ 'exclude-from-catalog', 'exclude-from-search' ],
					'operator' => 'AND',
				];
				break;
		}

		return $vars;
	}

	public function get_filtering_data() {
		return [
			'options' => wc_get_product_visibility_options(),
		];
	}

}