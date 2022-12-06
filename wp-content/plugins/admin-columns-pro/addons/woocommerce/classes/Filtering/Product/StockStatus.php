<?php

namespace ACA\WC\Filtering\Product;

use ACP;

class StockStatus extends ACP\Filtering\Model\Meta {

	public function get_filtering_data() {
		return [
			'order'   => false,
			'options' => [
				'instock'    => __( 'In stock', 'woocommerce' ),
				'outofstock' => __( 'Out of stock', 'woocommerce' ),
			],
		];
	}

}
