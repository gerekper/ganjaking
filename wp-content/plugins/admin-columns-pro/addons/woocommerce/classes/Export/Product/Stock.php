<?php

namespace ACA\WC\Export\Product;

use ACP;

class Stock implements ACP\Export\Service {

	public function get_value( $id ) {
		$product = wc_get_product( $id );

		$stock = $product->get_stock_status();

		if ( $product->managing_stock() ) {
			$stock .= ', ' . $product->get_stock_quantity();
		}

		return $stock;
	}

}