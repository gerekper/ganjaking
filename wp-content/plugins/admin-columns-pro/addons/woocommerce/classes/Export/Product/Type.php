<?php

namespace ACA\WC\Export\Product;

use ACP;

class Type implements ACP\Export\Service {

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