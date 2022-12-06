<?php

namespace ACA\WC\Filtering\Product;

use ACP;

class ProductTag extends ACP\Filtering\Model\Post\Taxonomy {

	public function get_filtering_vars( $vars ) {
		return $this->strategy->get_filterable_request_vars_taxonomy( $vars, $this->get_filter_value(), 'product_tag' );
	}

	public function get_filtering_data() {
		return [
			'order'        => false,
			'empty_option' => true,
			'options'      => $this->get_terms_list( 'product_tag' ),
		];
	}

}
