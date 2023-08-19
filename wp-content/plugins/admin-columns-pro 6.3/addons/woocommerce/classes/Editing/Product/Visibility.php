<?php

namespace ACA\WC\Editing\Product;

use ACA\WC\Editing\Storage;
use ACP;
use ACP\Editing\View;
use RuntimeException;
use WC_Data_Exception;

class Visibility implements ACP\Editing\Service {

	public function get_view( string $context ): ?View {
		return new ACP\Editing\View\Select( wc_get_product_visibility_options() );
	}

	public function get_value( $id ) {
		$product = wc_get_product( $id );

		return $product ? $product->get_catalog_visibility() : false;
	}

	public function update( int $id, $data ): void {
		$product = wc_get_product( $id );

		try {
			$product->set_catalog_visibility( $data );
		} catch ( WC_Data_Exception $e ) {
			throw new RuntimeException( $e->getMessage() );
		}

		$product->save();
	}

}