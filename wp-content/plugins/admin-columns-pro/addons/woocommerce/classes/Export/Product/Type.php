<?php

namespace ACA\WC\Export\Product;

use ACP;

/**
 * WooCommerce product type (default column) exportability model
 * @since 2.2.1
 */
class Type extends ACP\Export\Model {

	public function get_value( $id ) {
		$product = wc_get_product( $id );

		$type = $product->get_type();

		if ( $product->is_downloadable() ) {
			$type = 'downloadable';
		}

		if ( $product->is_virtual() ) {
			$type = 'virtual';
		}

		return $type;
	}

}