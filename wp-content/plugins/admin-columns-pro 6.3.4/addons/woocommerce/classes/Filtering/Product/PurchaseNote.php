<?php

namespace ACA\WC\Filtering\Product;

use ACP;

/**
 * @since 3.0
 */
class PurchaseNote extends ACP\Filtering\Model\Meta {

	public function get_filtering_data() {
		return [
			'empty_option' => $this->get_empty_labels( __( 'Purchase Note', 'woocommerce' ) ),
		];
	}

}