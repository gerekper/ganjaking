<?php

namespace ACA\WC\Export\Product;

use ACP;

/**
 * WooCommerce product SKU (default column) exportability model
 * @since 2.2.1
 */
class SKU extends ACP\Export\Model {

	public function get_value( $id ) {
		return wc_get_product( $id )->get_sku();
	}

}