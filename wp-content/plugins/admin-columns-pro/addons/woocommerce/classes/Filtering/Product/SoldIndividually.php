<?php

namespace ACA\WC\Filtering\Product;

use ACP;

/**
 * @since 3.0
 */
class SoldIndividually extends ACP\Filtering\Model\Meta {

	public function get_filtering_vars( $vars ) {
		$operator = 'yes' === $this->get_filter_value() ? '=' : '!=';

		$vars['meta_query'] = [
			[
				'key'     => $this->column->get_meta_key(),
				'value'   => 'yes',
				'compare' => $operator,
			],
		];

		return $vars;
	}

	public function get_filtering_data() {
		return [
			'order'   => false,
			'options' => [
				'yes' => __( 'Sold Individually', 'codepress-admin-columns' ),
				'no'  => __( 'Not Sold Individually', 'codepress-admin-columns' ),
			],
		];
	}

}