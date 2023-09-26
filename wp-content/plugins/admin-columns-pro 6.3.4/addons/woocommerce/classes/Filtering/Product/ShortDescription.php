<?php

namespace ACA\WC\Filtering\Product;

use ACP;

/**
 * @since 3.0
 */
class ShortDescription extends ACP\Filtering\Model\Post\Excerpt {

	public function get_filtering_data() {
		return [
			'options' => [
				'without_exerpt' => __( 'Without Short Description', 'codepress-admin-columns' ),
				'has_excerpt'    => __( 'Has Short Description', 'codepress-admin-columns' ),
			],
		];
	}

}