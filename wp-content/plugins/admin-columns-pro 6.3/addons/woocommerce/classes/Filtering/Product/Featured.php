<?php

namespace ACA\WC\Filtering\Product;

use ACP;

class Featured extends ACP\Filtering\Model {

	public function get_filtering_vars( $vars ) {
		$product_visibility_term_ids = wc_get_product_visibility_term_ids();

		$operator = 'yes' === $this->get_filter_value() ? 'IN' : 'NOT IN';

		$vars['tax_query'] = [
			[
				'taxonomy' => 'product_visibility',
				'field'    => 'term_taxonomy_id',
				'terms'    => [ $product_visibility_term_ids['featured'] ],
				'operator' => $operator,
			],
		];

		return $vars;
	}

	public function get_filtering_data() {
		return [
			'options' => [
				'no'  => __( 'No' ),
				'yes' => __( 'Yes' ),
			],
		];
	}

}
