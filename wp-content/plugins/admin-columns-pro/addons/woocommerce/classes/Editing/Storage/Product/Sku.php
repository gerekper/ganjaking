<?php

namespace ACA\WC\Editing\Storage\Product;

use ACP\Editing\Storage;
use RuntimeException;
use WC_Data_Exception;

class Sku implements Storage {

	public function get( int $id ) {
		$product = wc_get_product( $id );

		return $product ? $product->get_sku() : false;
	}

	public function update( int $id, $data ): bool {
		$product = wc_get_product( $id );

		try {
			$product->set_sku( $data );
		} catch ( WC_Data_Exception $e ) {
			throw new RuntimeException( $e->getMessage() );
		}

		return $product->save() > 0;
	}

}