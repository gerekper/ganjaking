<?php

namespace ACA\WC\Export\Product;

use ACP;

class SKU implements ACP\Export\Service {

	public function get_value( $id ) {
		$product = wc_get_product( $id );

		return $product
			? $product->get_sku()
			: '';
	}

}